var allOrganizations = [], followed = [], allTerms = [], query = "<?php echo empty($_GET['lr_resource'])?$_GET['query']:$_GET['lr_resource']; ?>";
var temp = new mainViewModel([]), activeModalName = null, lastSearchCache = "";
var iframeHidden = true;
var tempBookmarksArray = [];
handleMainResourceModal(query);
var qmarkUrl = '<?php echo plugins_url( "/images/qmark.png" , dirname(__FILE__) ) ?>';

for (var f in followed){
	temp.followers.push({name:followed[f], content:[]});
}

var handlePerfectSize = function(){};
var serviceHost = "<?php echo $host; ?>";
var initialGraphBuild = false;

totalSlice = 15;
newLoad = 15;

jQuery(document).ready(function($){

	///////////////////////////////////////////
	//An attempt to stop frame redirect      //
	///////////////////////////////////////////
	var prevent_bust = 0;
	var bustHappened = 0;
	
	window.onbeforeunload = function() { 

		prevent_bust++;
		bustHappened++;
		if(tInterval != "done" && bustHappened > 0)
			return "";
	};  
	
	var tInterval = setInterval(function() {  
		
	  console.log(prevent_bust);
	  if (prevent_bust > 0) {  
		prevent_bust -= 2  
		
		window.top.location = serviceHost + '/frame'  
	  }  
	}, 1);
	
	setTimeout(function(){
		
		clearInterval(tInterval);
		tInterval = "done";
	}, 3000);
	/////////////////////////////////////////////
	
	$('input, textarea').placeholder();

	enableModal();
	
	$("#bookmark").click(function(){
		
		if("{{user}}".length < 1){
			alert("You must be logged in to bookmark resources.");
			return;
		}
		
		//add element to observable array, send a request via socketio, and remove current textarea value
		var paradata = genParadataDoc("{{user.jobTitle}}", "{{user._id}}", "bookmarked");

		//temp.currentObject().timeline.push(paradata);
		scrollbarFix();
		
		
		$.ajax({
			type: "POST",
			url: "/main",
			dataType: "json",
			jsonp: false,
			contentType: 'application/json',
			data: createJSON(paradata, "bookmark"),
			success: function(data){

				console.log("added");
				console.log("Response data: ", data);
				$("#bookmark").addClass("disabled");
				$("#bookmark").off();
			},
			error: function(error){
				console.error(error);
			}
		});
		
		
	});
	
	$(".icon-flag").click(function(){
						
		if("{{user}}".length < 1){
			alert("You must be logged in to flag resources.");
			return;
		}
		
		//add element to observable array, send a request via socketio, and remove current textarea value
		var paradata = genParadataDoc("{{user.jobTitle}}", "{{user._id}}","flagged");
		temp.currentObject().timeline.push(paradata);
		paradataStoreRequest(paradata);
		
		scrollbarFix();
		
	});
	
	$(".chatBox textarea").keyup(function(e){
		
		//Enter was pressed
		if(e.which == 13){
			
			if("{{user}}".length < 1){
				alert("You must be logged in to comment.");
				return;
			}

			//add element to observable array, send a request via socketio, and remove current textarea value
			var paradata = genParadataDoc("{{user.jobTitle}}", "{{user._id}}","commented", $(this).val().trim());

			temp.currentObject().timeline.push(paradata);
			scrollbarFix();
			
			paradataStoreRequest(paradata);

			$(this).val("");
		}
	});

	$(".resultModal").on("click", ".resultClick", function(e){

		e.preventDefault();
		e.stopPropagation();

		$("#visualBrowser").modal("hide");
		handleMainResourceModal($(this).attr("name"));
		console.log("show click");
	});

	$("table").on("click", ".author-timeline", function(evt){

		$(".author-timeline").not(this).popover('hide');
		$(this).popover('toggle');

		//Enable tooltips
		$(".bottomBar i").tooltip();

		//evt.stopPropagation();
	});
		
	var hidePopover = function(){

		$(".author-timeline").popover('hide');
	};
});

var handleOnclickUserBar = function(obj){
	
	var cacheObj = $(obj);
	var name = cacheObj.attr("name");
	var className = cacheObj.attr("class");

	if(className == "icon-star")
		self.followUser(name);
		
	else if(className == "icon-file"){
		
		
	}
	
	//Substr gets the number portion of "paradataX"
	var test = cacheObj.attr("name").substr(8, cacheObj.attr("name").length-8);
	console.log(test, self.currentObject().timeline()[test]);
	displayObjectData(self.getReversedTimeline()[test]);

}

ko.applyBindings(temp);