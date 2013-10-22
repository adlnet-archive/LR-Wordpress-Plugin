/*

    Small list of relevant objects:

        previewObject: An array used to preview topics to follow. Requires topic name
        and an array of resource objects belonging to that topic. Please note that topics
        should be unique. previewObjects can also support user names (i.e. you are also able to follow a user).
        previewObjects contains an array of resourceObjects.



        resourceObject: The unit resource. These would be unique metadata entries from the LR. resourceObjects also
        have paradata arranged in a timeline array.

        self.followers should contain an array of previewObjects
        self.organizations needs only to contain an array of strings that can be used to search against a node
*/

//IE9 Fix. 
if (!(window.console && console.log)) { (function() { var noop = function() {}; var methods = ['assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error', 'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log', 'markTimeline', 'profile', 'profileEnd', 'markTimeline', 'table', 'time', 'timeEnd', 'timeStamp', 'trace', 'warn']; var length = methods.length; var console = window.console = {}; while (length--) { console[methods[length]] = noop; } }()); }

var $=($)?$:jQuery;
var currentObjectMetadata = [], lastContentFrameSource = "", saveFrameState = "", directAccess = false, currentSubstrLetter = 'a', publishersCache = {},
	totalSlice = 6, loadIndex = 1, newLoad = 10, blackList = ["www.engineeringpathway.com"], previewSearchLoaded = false, debugMode = true, saveStandardsData, resultsSaveBuffer;

String.prototype.formatMediaFeature = function(){
	var test = /[A-Z]/;
	var display = new Array(this.length + 1);
	console.log(this);
	var j = 0;
	for(var i  = 0; i < this.length; i++){
			if(test.test(this[i])){
				display[j] = " ";
				j++;
			}
			display[j] = this[i].toLowerCase();
			j++;
	}	
	return display.join("");
}

var lrConsole  = function(){

	if(debugMode && arguments.length > 0 && console.log.apply)
		console.log.apply(console, arguments)
};
	
var urlTransform = {

    "3dr.adlnet.gov" : function(urlObj, screenUrl){

        //After splitting, this is the index of the most important part of the URL (the id)
        var idIndex = 2;

        var temp = (urlObj.pathname[0] == "/") ? urlObj.pathname.substr(1, urlObj.pathname.length - 1) : urlObj.pathname;
        var id = temp.split("/")[idIndex];
			
		if(screenUrl !== undefined){
		
			return screenUrl;
			//return (id == undefined) ? screenUrl : "http://3dr.adlnet.gov/Public/Serve.ashx?pid="+id+"&mode=GetScreenshot";
		}
		
		else{
			return (id == undefined) ? urlObj.href : "http://3dr.adlnet.gov/Public/Model.aspx?ContentObjectID=" + id;
		}
    }
};

var reverseTransform = {

    "3dr.adlnet.gov" : function(urlObj){

        //After splitting, this is the index of the most important part of the URL (the id)
        var idIndex = 1;
        var id = urlObj.href.split("=")[idIndex];

        lrConsole(urlObj.href.split("="));

        return "http://3dr.adlnet.gov/api/rest/"+id+"/Format/dae?ID=00-00-00";
    }
};

var paradataStoreRequest = function(paradata){
	
	$.ajax({
		type: "POST",
		url: serviceHost + "/main",
		dataType: "json",
		jsonp: false,
		contentType: 'application/json',
		data: createJSON(paradata, "paradata"),
		success: function(data){

			lrConsole("added");
			lrConsole("Response data: ", data);
		},
		error: function(error){
			console.error(error);
		}
	});
};

var genParadataDoc = function(jobTitle, id, action, detail, date){
				
	return {
			"activity": {
				"actor": {
					"objectType": jobTitle,
					"description": [
						"You"
					],
					"id": id
				},
				"verb": {
					"action": action,
					"detail": detail != undefined ? detail : "",
					"date": date != undefined ? date : new Date()
				},
				"object": temp.currentObject().url
			}
		};
	
};

//This may need to be refactored for memory efficiency. Not sure how createElement handles memory.
var getLocation = function(href, hostname) {
    var l = document.createElement("a");
    l.href = href;
    return (hostname === true) ? l.hostname : l;
};

var scrollbarFix = function(obj){

	obj = (obj == undefined)? $(".modal-timeline") : obj;
	obj.getNiceScroll().remove();
	obj.niceScroll({"cursoropacitymax": 0.7, "cursorborderradius": 0} );
};

var opts = {
  lines: 7, // The number of lines to draw
  length: 3, // The length of each line
  width: 3, // The line thickness
  radius: 7, // The radius of the inner circle
  corners: 1, // Corner roundness (0..1)
  rotate: 18, // The rotation offset
  color: '#000', // #rgb or #rrggbb
  speed: 0.7, // Rounds per second
  trail: 60, // Afterglow percentage
  shadow: false, // Whether to render a shadow
  hwaccel: true, // Whether to use hardware acceleration
  className: 'spinner', // The CSS class to assign to the spinner
  zIndex: 2e9, // The z-index (defaults to 2000000000)
  top: 'auto', // Top position relative to parent in px
  left: 'auto' // Left position relative to parent in px
};

var self, spinner = null;

var generateContentFrame = function(src, alreadyAppended){

	if(alreadyAppended !== true){

		//This is definitely not a trivial workaround. However, this does disable adding to the browser's history
		var frameCode = '<iframe id="modalFrame" style="visibility: hidden;" src="about:blank" frameborder="0"></iframe>';
		$("#mBody").append(frameCode);
	}

	var frame = $('#modalFrame')[0];
	if(frame)
		frame.contentWindow.location.replace(src);

	if(alreadyAppended !== true){
		$("#spinnerDiv").show();
		$("#modalFrame").load(function(){

			spinner.stop();
			$("#spinnerDiv").hide("slow", function(){
				$("#modalFrame").css("visibility", "visible");
			});
		});
	}
};

var sortTimeline = function(l, r){
	
	return getDate(l.activity.verb.date) - getDate(r.activity.verb.date);
};

var doTransform = function(src){
	
	var tempUrl = getLocation(src);
	return (urlTransform[tempUrl.hostname] !== undefined ) ? urlTransform[tempUrl.hostname](tempUrl) : src;
};

var fetchLiveParadata = function(src){

	$.ajax(NODE_URL + "obtain?request_id="+src, {dataType: 'jsonp', jsonp: 'callback'}).success(function(data){
		
		//For each document found in data
		var jsonData;
		currentObjectMetadata = [];

		if(data.documents.length == 0)
			return;
			
		for(var i = 0; i < data.documents[0].document.length; i++){
			if(data.documents[0].document[i].resource_data_type == "paradata"){

				jsonData = (typeof data.documents[0].document[i].resource_data == "string") ?
							JSON.parse( data.documents[0].document[i].resource_data ) : data.documents[0].document[i].resource_data;

				self.currentObject().timeline.push(jsonData);
			}
			else if(data.documents[0].document[i].resource_data_type == "metadata"){

				currentObjectMetadata.push(data.documents[0].document[i]);
			}
		}
		
		temp.currentObject().timeline.sort(sortTimeline);
	});
};

var displaySocialMediaButtons = function(){
	if(socialMediaPlugins){
		$("#socialMediaPlugins").show();
		$("#socialMediaPlugins").html(socialMediaPlugins);
	}
};

var handleMainResourceModal = function(src, direct){
	
	if(src){
		//src should either be the URL, or a jQuery object whose name attribute is the URL
		src = (typeof src == "string")? src : hex_md5($(this).attr("name"));

		var target = document.getElementById('spinnerDiv');
		self.currentObject(new resourceObject("Item", src));
		
		//Remove any residual JSON prettyprinted documents
		$(".prettyprint").remove();
		
		if(iframeHidden){

			//Workaround to get 'hasScreenshot' property
			if(src){
                var url = window.location.pathname + "?json=data.get_data_items&keys=" + encodeURIComponent(JSON.stringify([src]));
				$.getJSON(url, function(data){
					data = data.data;
					data = data.pop();
					console.log(data);
					var md5 = src;
					if(data){
						src = data.url;
						document.title = data.title;
						
						//This is done because observable.valueHasMutated wasn't working.. so assign each property to a new object individually and update self
						var currentObject = new resourceObject("Item", src);
						currentObject.timeline = self.currentObject().timeline;
						currentObject.title = (data.title == undefined) ? doTransform(src) : stripHTML(data.title);
						currentObject.description = (data.description == undefined) ? "" : stripHTML(data.description);
						currentObject.url = (data.url == undefined) ? "" : data.url;
						currentObject.publisher = (data.publisher == undefined) ? "" : stripHTML(data.publisher);
						
						lrConsole("qmarkUrl: ", qmarkUrl);
						var imageUrl = qmarkUrl? qmarkUrl:"/images/qmark.png";
						
						currentObject.image = (data.hasScreenshot !== true) ? imageUrl : serviceHost + "/screenshot/" + md5;
						currentObject.image = self.getImageSrc(data.url, currentObject.image);
						currentObject.hasScreenshot = currentObject.image != imageUrl;				
						
						self.currentObject(currentObject);
					}
					
					else{
						
						src = data.url;
						var imageUrl = qmarkUrl? qmarkUrl:"/images/qmark.png";
						var currentObject = new resourceObject("Item", src);
						currentObject.image = self.getImageSrc(data.url, imageUrl);
						currentObject.hasScreenshot = currentObject.image != imageUrl;		
						
						self.currentObject(currentObject);
					}
					
					displaySocialMediaButtons();
					fetchLiveParadata(data.url);
				});
			}
		}
		
		else{
			
			//This will not work unless we gain access to the actual URL and not the hashed version
			generateContentFrame(src);

			/*
				While the modal content is loading, load the timeline. Need jQuery/socket.io here. Need to do ordering.

				self.currentObject().timeline.push(NEW ENTRIES);
			*/
		}
	}
	
	if(spinner !== null){

		//Checks to see if there are enough rows in the timeline to warrant showing the scroll bars
		//Should be checked whenever an element is added to or removed from the timeline
		lrConsole("height: ", $("#timeline-table").height());
		if($("#timeline-table").height() > 460)
			$(".modal-timeline").getNiceScroll().show();

		scrollbarFix();

		spinner.spin(target);
	}
	else {

		$(".modal-timeline").niceScroll({"cursoropacitymax": 0.7, "cursorborderradius": 0} );
		if($("#timeline-table").height() > 460)
			$(".modal-timeline").getNiceScroll().show();

		scrollbarFix();

		spinner = new Spinner(opts).spin(target);
	}
};


var enableModal = function(name){

    $(".draggable span").click(handleMainResourceModal);

    $("#modal").on("hidden", function(){

        //Destroy tooltips
        $(".bottomBar i").tooltip('destroy');

        $(".scrollbar").scrollTop(0);
        $(".modal-timeline").getNiceScroll().hide();
        $("#modalFrame").attr({src:"about:blank"});
        $(".author-timeline").popover('hide');

        //lrConsole("");
        //self.currentObject({});
        //self.timeline.removeAll();
    });
};

var notOnBlackList = function(url){
		
	var link = getLocation(url);
	
	//We don't want to show resources in the blackList
	return $.inArray(link.hostname, blackList) == -1;
};

var previewObject = function(name, content){
    this.name = name;
    this.content = ko.observableArray(content);
    this.isUser = false;
};

var resourceObject = function(name, url, timeline){

    this.url = (url !== undefined) ? url : null;
    this.title = getLocation(url).hostname;
    this.description = "";
    this.image = "";
    this.hasScreenshot = false;

    //The timeline should be an observable array of paradata objects
    this.timeline = (timeline !== undefined) ? ko.observableArray(timeline) : ko.observableArray();
};

var user = function(obj){

    this.isFollower = false;
    this.isFollowing = false;

    this.name = obj.name;
};

var followingList = [];

//Swaps an element with an element that's not currently being displayed, if it exists
var swapResourceElement = function(arr, removeIndex, swapIndex){

    //arr must be a ko observable array
    if(arr()[swapIndex] === undefined)
        arr.splice(removeIndex, 1);

    else{
        arr()[removeIndex] = arr()[swapIndex];
        arr.splice(swapIndex, 1);
    }
};

var getProperArray = function(str){

    switch(str){

        case "data":
            return self.data;
        case "followers":
            return self.followers;
        case "bookmarks":
            return "bookmarks";
        default:
            return self.data;
    }
};



var generateAuthorSpan = function(str, author, content, i){

    //Check for any potential XSS attacks

	content = (content == undefined)? "" : content.replace(/"/g, "&quot;").replace(/'/g, "&apos;");
	author = author.replace(/"/g, "&quot;").replace(/'/g, "&apos;");
	str = str.replace(/"/g, "&quot;").replace(/'/g, "&apos;");

	//lrConsole("Debug span ", content + " " + author + " " + str);

    var title = author + '<button type="button" onclick="hidePopover()" class="close closeTimeline" aria-hidden="true">&times;</button>';

    var bottomBar = '<div class="bottomBar">'+
                        '<i name="'+author+'" rel="tooltip" title="Follow User" onclick="handleOnclickUserBar(this)" class="icon-star"></i>'+
                        '<i name="'+author+'" rel="tooltip" title="View User Profile" onclick="handleOnclickUserBar(this)" class="icon-user"></i>'+
                        '<i rel="tooltip" title="View Raw Paradata" onclick="handleOnclickUserBar(this)" class="icon-file" name="paradata'+i+'"></i>'+
                    '</div>';

    var localContent = '<div>'+content+'</div>' + bottomBar;

    return "<br/><span data-content='"+localContent+"' data-title='"+title+"' data-trigger='manual' class='author-timeline'>" + str + "</span>";
};

var createJSON = function(obj, type){

	return JSON.stringify({action: type, subject: obj});
};

var displayObjectData = function(pmdata){

		$(".prettyprint").remove();

		//Watch out for XSS attacks
		lrConsole("metadata: ", pmdata);
		var metadata = '<pre class="prettyprint">';

		if($.isArray(pmdata)){
			for(var i = 0; i < pmdata.length; i++){
				metadata += JSON.stringify(pmdata[i], null, 4);
			}

			metadata = (pmdata.length == 0)? "<center class='prettyprint' style='margin-top: 20%;'>No metadata found</center>" : metadata;
		}

		else {

			metadata += JSON.stringify(pmdata, null, 4);
		}

		$("#modal-data-view").html(metadata + "</pre>");
		prettyPrint();
		//$("#metadata").modal('show'); 
};

var getDate = function(dateStr){
	
	var dateArr = dateStr.replace("-", "/");
	var date = new Date(dateArr);
	
	//Not a valid date object
	if(isNaN(date.getTime())){

		if(self.currentObject().url.indexOf("3dr.adlnet.gov") > -1){

			//This gets the timestamp within "/Date(x)/"
			date = new Date(parseInt(dateStr.substr(6, dateStr.length - 8)));
		}
	}
	
	return date;
};

var addFullDescriptions = function(){
			
			if(self.results().length == 0)
				return;
			
			lrConsole("The total slice: ", totalSlice);	
				
			var keys = [];			
			//Generate an array of MD5 hashes to use as identifiers in request
			for(var i = 0; i < totalSlice; i++){
				
				if(self.results()[i].md5 === undefined){
					
					keys[i] = hex_md5(self.results()[i].url);
					self.results()[i].md5 = keys[i];
				}
				
				else 
					keys[i] = self.results()[i].md5;
			}
			
			keys = encodeURIComponent(JSON.stringify(keys));
            var url = window.location.pathname + "?json=data.get_data_items&keys=" + keys;
			//Do request and update self.results			
			$.getJSON(url, function(data){
				data = data.data;			
				
				lrConsole("Incoming data: ", data);
				
				for(var i = 0; i < totalSlice; i++){
					
					if(data[i]){
						
						self.results.remove(i);
						
						lrConsole("Results: ", self.results()[i].title);
						self.results()[i].description = (data[i].description == undefined) ? "" : data[i].description;
						
						//If resource doesn't have a title, set title equal to "" (Knockout will display tags if title == "")
						//However, if resource doesn't have a title or tags, then set title equal to url
						self.results()[i].title = (data[i].title == undefined || data[i].title == data[i].url) ? "" : data[i].title;
						self.results()[i].title = (self.results()[i].title == "" && self.results()[i].keys.length < 1) ? self.results()[i].url : self.results()[i].title;
						
						self.results()[i].hasScreenshot = (data[i].hasScreenshot == undefined) ? false : data[i].hasScreenshot;
						self.results()[i]._id = (data[i]._id == undefined) ? "" : data[i]._id;
					}
					
				}
				
				var temp2 = self.results.removeAll();
				self.results(temp2);
				
			});
		
	
};

var sliceSearchDone = function(){
	
	
	$('#spinnerDiv').hide();
	$('#spinnerDiv').css("margin-top", "0px");
	$("#loadMore").show();
	
	if(self.results().length == 0){
		
		$("#loadMore").hide();
		$("#resultsNotFound").show();
	}
	
	handlePerfectSize();
};

var addComma = function(num){
	var newStr = '';
	var temp = 0;
	var saveMod = 0;
	
	do {
		temp = parseInt(num/1000);
		
		if(temp >= 1){
			
			saveMod = num % 1000;
			
			if(saveMod < 100)
				saveMod = (saveMod < 10 ) ? '00' + saveMod : '0' + saveMod;
				
			newStr = ',' + saveMod + newStr;
		}
		
		else{		
			newStr = parseInt(num) + newStr;
		}
		
		num = temp;
		
	} while(temp >= 1);
	
	return newStr;
};

var stripHTML = function(str){
	return str.replace(/<[^>]+>/gim, '').replace(/&[^;]+;/gim, '');
};

var updateResults = function(data){
	
	lrConsole("data: ", data);

	if(countReplace && data.count >= 0){
		self.resultsCount(countReplace.replace('$count', ' - ' + addComma(data.count)));
	}
	
	if(data.responseText)
		data = JSON.parse(data.responseText).data;
	
	data = data.data ? data.data : data;

	//lrConsole(data);
	$('#spinnerDiv').hide();
	$("#loadMore").show();
	$('#spinnerDiv').css("margin-top", "50px");
	//var startIndex = (loadIndex == 2) ? 0 : 1;
	
	if(data.length == 0 && loadIndex <= 2){
		temp.resultsNotFound(true);
		return false;
	}
	
	else if(data.length == 0){
		
		$("#loadMore").hide();
		$("#endOfResults").show();
	}		
	
	
	var tempRegexArr = query.replace(/[^a-zA-Z0-9 ]/gi, '').split(' ');
	for(var i = 0; i < tempRegexArr.length; i++){
		
		if(tempRegexArr[i].search(/[a-zA-Z0-9]/gi) == -1)
			tempRegexArr.splice(i, 1);
	}
	

	var regexObj = new RegExp('(' + tempRegexArr.join('|') + ')', 'gi');

	for(var i = 0; i < data.length; i++){
	
		data[i].title = stripHTML(data[i].title).replace(regexObj, '<b>$&</b>');
		data[i].description = stripHTML(data[i].description).replace(regexObj, '<b>$&</b>');
		data[i].publisher = data[i].publisher ? stripHTML(data[i].publisher).replace(regexObj, '<b>$&</b>') : '';
		data[i].hasScreenshot = data[i].hasScreenshot==undefined?true:data[i].hasScreenshot;
		
		self.results.push(data[i]);
	}

	self.results.remove(function(item){
		
		return !self.notOnBlackList(item.url);
	});
	
	handlePerfectSize();
	return true;
};

var noMoreResults = function(error){
	lrConsole(error);
	$('#spinnerDiv').hide();
	$('#spinnerDiv').css("margin-top", "50px");
	temp.resultsNotFound(true);			
};

/* The main View Model used by Knockout.js */
var mainViewModel = function(resources){

    self = this;
	
	/*
		Should refactor the view model and templates to load only the observables that are required by a widget
	*/
    self.data = ko.observableArray(resources);
    self.bookmarks = ko.observableArray();
    self.followers = ko.observableArray(followingList);
    self.results = ko.observableArray();
    self.resultsNotFound = ko.observable(false);
	self.saveResultsDisplay = ko.observableArray();
	self.relatedResultsNodes = ko.observableArray();
	self.isMetadataHidden = ko.observable(-1);
	self.featuredResource = ko.observableArray();
	self.children = [];
	self.standards = ko.observable({children:[]});
	self.accessibilityFeatures = ko.observableArray();
	self.images = ko.observableArray();
	self.featuredResultsHelper = ko.observableArray();
	self.levelTracker = [0];
	self.handleStandardsClick = function(item, e){};
	self.standardDescription = '';
	self.filterSearchTerms = ko.observableArray();
	self.listOfStates = ko.observableArray();
	self.standardsCounter = 0;
	self.subjectCounter = 0;
	self.page = ko.observable(-1);
	self.publishers = ko.observableArray([]);
	self.resultsCount = ko.observableArray();
	self.allLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
	self.errorPublisher = ko.observable(false);
	
	//Temporarily set to false for federal resources
	self.loadMore = ko.observable(false);
	
	self.handleStandardsNodeClick = function(data, e){
		
		if(data.id)
			self.handleStandardsClick(data, e);
			
		else{
			standardPlusCollapse(e, $(e.currentTarget));
			data.loadChildren();
		}
		
		return true;
	};
	
	self.previous = function(){
		self.load(true);
	};
	
	self.handleLetterClick = function(data, e){
		
		if(currentSubstrLetter == data.toLowerCase())
			return;
			
		currentSubstrLetter = data.toLowerCase();
		self.load();
		
		console.log(data, e);
	};
	
	self.load = function(prev){
		self.page(prev===true && self.page() > 0 ? self.page()-1 : self.page()+1);
		
		//Assuming there is no pagination. Will break if there is.
		if(publishersCache[currentSubstrLetter]){
		
			self.publishers(publishersCache[currentSubstrLetter]);
			return;
		}
		
		$(publishersSpin.el).show();
		self.errorPublisher(false);
		self.publishers([]);
		$.ajax({
			url: "?json=publishers.publishers_list&letter="+currentSubstrLetter, 
			success: function(data){							
			
				$(publishersSpin.el).hide();
				data = data.data;
				if(! data){
					self.errorPublisher(true);
				}
				else{
					self.errorPublisher(false);
					
					
					self.publishers(data);
					self.publishers().sort(function (a, b) {
						return a.toLowerCase().localeCompare(b.toLowerCase());
					});
					self.publishers.valueHasMutated();
					
					publishersCache[currentSubstrLetter] = self.publishers();
				}
			},
			error: function(){
				self.errorPublisher(true);
				$(publishersSpin.el).hide();
			}
		});
	};
	
	self.model = function(node, noChildren, parentRoute){
		
		var me = this;
		me.node = node;
		me.parentRoute = parentRoute ? parentRoute : [0];
		var title = node.title.substr(0,8) == 'National' && me.parentRoute.length < 3 ? node.title.charAt(9).toUpperCase() + node.title.slice(10, node.title.length) : 
					node.title.charAt(0).toUpperCase() + node.title.slice(1, node.title.length);
					
		me.title = ko.observable(title);
		me.count = ko.observable(node.count);
		me.childCount = ko.observable(node.childCount);
		me.children = noChildren === true? undefined : ko.observableArray();
		me.id = node.id? ko.observable(node.id) : undefined;
		
		
		me.loadChildren = function(){
			
			//console.log("Load children");
			if(!me.node.children || (me.children && me.children().length>1))
				return;
			
			var tempArr = $.extend(true, [], me.parentRoute);
			tempArr.push(-1);

			for(var i = 0; i < me.node.children.length; i++){
				
				tempArr = $.extend(true, [], tempArr);
				tempArr[tempArr.length-1]++;
				
				childrenCheck = me.node.children[i].children ? false : true;
				me.children.push(new self.model(me.node.children[i], childrenCheck, tempArr));
			}
		};
	};
	
	
	self.addComma = function(num){
		var newStr = '';
		var temp = 0;
		var saveMod = 0;
		
		do {
			temp = parseInt(num/1000);
			
			if(temp >= 1){
				
				saveMod = num % 1000;
				
				if(saveMod < 100)
					saveMod = (saveMod < 10 ) ? '00' + saveMod : '0' + saveMod;
					
				newStr = ',' + saveMod + newStr;
			}
			
			else{		
				newStr = parseInt(num) + newStr;
			}
			
			num = temp;
			
		} while(temp >= 1);
		
		return newStr;
	};
		

	
	self.handleSubCategoryClick = function(data, obj){
			
		$('.stateList').hide();
		
		$("#standardsMapContainer").hide();
		spinner.spin($(".allStates")[0]);		
		var url = window.location.pathname + "?json=standards.standards&standard=" + data;		
		$.getJSON(url, function(data){
			data = data.data;
			
			self.standards(new self.model(data));
			self.standards().loadChildren();
		
			spinner.stop();
			$("#standardsMapContainer").show();			
			lrConsole(data, self);
			$("#standardsMapContainer .standard-div").hide();	
		});
		
		return;
	};
	self.handleStandardHeaderClick = function(data, e){
	
		var obj = $(e.target);
		//console.log(e,data);
		
		if(obj.hasClass('standardHeaderInactive')){
		
			$('.standardHeader').addClass('standardHeaderInactive');
			obj.removeClass('standardHeaderInactive');
			
			if(self.standards().children){
				
				if(data == 'State'){
					$('.stateList').show();
					$("#standardsMapContainer").hide();
					
					
				}
				else {
					$("#standardsMapContainer").show();
					$('.stateList').hide();
					if(self.standards() != saveStandardsData){
						
						$("#standardsMapContainer").hide();
						spinner.spin($(".allStates")[0]);	
						
						
						window.setTimeout(function(){

							self.standards(saveStandardsData);
							//self.standards().loadChildren();
							$("#standardsMapContainer .standard-div").hide();	
							spinner.stop();
							$("#standardsMapContainer").show();
						}, 500);
						
					}
				}
			}
			
		}	
	};
	
	self.getFilterSections = ko.computed(function(){
	
		var returnObj = {
				contentTypes: [
					{
						"category": 'Video', 
						"accessibility": [						
						{
							"name": "Captions",
							"values": ["captions"]
						}, 
						{
							"name": "Audio Description",
							"values": ["audioDescription"]
						}, 
						{
							"name": "Transcript",
							"values": ["transcript"]
						}, 
						{
							"name": "Flashing",
							"values": ["flashing"]
						}, 
						{
							"name": "Sound",
							"values": ["sound"]
						}, 
						{
							"name": "Motion Simulation",
							"values": ["motionSimulation"]
						}						
						]
					}, 
					{
						"category": 'Primary Doc', 
						"accessibility": [
						{
							"name": "Braile",
							"values": ["braile"]
						}, 
						{
							"name": "Description", 
							"values": ["alternativeText", "longDescription"]
						},
						{
							"name": "BRF",
							"values": ["BRF"]
						},
						{
							"name":  "MP3",
							"values": ["MP3"]
						}, 
						{
							"name":	"DAISY",
							"values": ["DAISY"] 
						},
						{
							"name":	"EPUB 3",
							"values": ["EPUB 3"]
						}
						]
					}, 
					{
						"category": 'Animation', 
						"accessibility": [						
						{
							"name": "Captions",
							"values": ["captions"]
						}, 
						{
							"name": "Audio Description",
							"values": ["audioDescription"]
						}, 
						{
							"name": "Transcript",
							"values": ["transcript"]
						}, 
						{
							"name": "Flashing",
							"values": ["flashing"]
						}, 
						{
							"name": "Sound",
							"values": ["sound"]
						}, 
						{
							"name": "Motion Simulation",
							"values": ["motionSimulation"]
						}						
						]
					}, 
					{
						"category": 'Photo', 
						"accessibility": [						
						{
							"name": "Description",
							"values": ["alternativeText", "longDescription"]
						}, 
						{
							"name": "Tactile",
							"values": ["tactileObject", "tactileGraphic"]
						}, 
						{
							"name": "Color Dependent",
							"values": ["colorDependent"]
						}, 
						{
							"name": "Text On Image",
							"values": ["textOnImage"]
						}
						]
					}
					], 
					publishers: []};
		
		//Get different results
		for(var i = 0; i < self.results().length; i++){
			
			if(self.results()[i].publisher && $.inArray(self.results()[i].publisher, returnObj.publishers) == -1)
				returnObj.publishers.push(self.results()[i].publisher);			
		}

		
		if(returnObj.publishers.length > 1){
			var tempArray = ["All publishers"];
			tempArray.push.apply(tempArray, returnObj.publishers);
			returnObj.publishers = tempArray;
		}
				
		return returnObj;
	});

	self.handleContentTypeClick = function(data, obj){
		
		var targetName = $(obj.target).addClass('inverse').removeClass('btn').attr("name");	
		self.accessibilityFeatures.removeAll();
		if(data.accessibility){
			var func = function(x){self.accessibilityFeatures.push(x);};
			data.accessibility.forEach(func);
		}
		self.filterSearchTerms.removeAll()
		self.filterSearchTerms.push({"name": data.category, "values": [data.category]});
		
		self.results.removeAll();
		self.loadNewPage(false, true);
		
		return;
	};	
	self.removeFilter = function(data, obj){
		self.filterSearchTerms.remove(data);
		self.results.removeAll();
		self.loadNewPage(false, true);			
	}
	self.applyFilter = function(data, obj){		
		self.filterSearchTerms.removeAll()
		self.filterSearchTerms.push(data);
		self.results.removeAll();
		self.loadNewPage(false, true);			
	};
	self.notOnBlackList = function(url){
		
		var link = getLocation(url);
		//lrConsole("blacklist? " + link.hostname + " " , $.inArray(link.hostname, blackList));
		
		//We don't want to show resources in the blackList
		return $.inArray(link.hostname, blackList) == -1;
	};
	
	self.getReversedTimeline = function(){
		
		if(self.currentObject == undefined)
			return [];

		return jQuery.extend(true, [], self.currentObject().timeline()).reverse();
	};
	
	self.getResults = function(){
			
			return self.results.slice(0, totalSlice);
	};

	self.updateSlice = function(){

		totalSlice += newLoad * loadIndex;
		loadIndex++;
		self.results.valueHasMutated();
		lrConsole(totalSlice);
		addFullDescriptions();
	};


	self.loadNewPage = function(isVisual, startOver){
		
		$('#spinnerDiv').show();
		$("#loadMore").hide();
		temp.resultsNotFound(false);
		isVisual = saveSearchType;
		//var query = $("#s").val();
		if(isVisual === true || isVisual === 'slice'){
			
			startNewSearch(query);
		}
		
		else {
			//debugger;
			loadIndex = (startOver === true) ? 0 : loadIndex;
			
			var data = {terms: query, lr_page: loadIndex <= 0 ? 0 : loadIndex-1};
			if (gov !== 0){
				data.gov = 1;
			}
			if(self.filterSearchTerms().length > 0){
				
				var newArr = [];
				for(var i = 0; i < self.filterSearchTerms().length; i++){
				  var filter = self.filterSearchTerms()[i];
				  if(filter){
				  	if (filter.values){
				  		newArr = newArr.concat(filter.values);
				  	}else{
						newArr.push(filter);
					}
				  }
				}					
				//Joining to help simplify server side processing
				data.filter = newArr.join(";");
			}
				
			data.json = isVisual == 'publisher'? "search.publisher" :"search.search";			
			if(resultsSaveBuffer && loadIndex > 1){
					
				updateResults(resultsSaveBuffer);
				$.ajax(window.location.pathname, {
					dataType : 'json',
					jsonp : 'callback',
					data: data
				}).done(function(data){
					resultsSaveBuffer = data;
					//debugger;
					if(resultsSaveBuffer.data.length == 0 && (loadIndex > 1 || startOver && startOver.type == 'click')){
						$("#loadMore").hide();
						$("#endOfResults").show();	
					}
					loadIndex++;
						
				});
			}
			
			else{
				console.log(data);	
				$.ajax(window.location.pathname, {
					dataType : 'json',
					jsonp : 'callback',
					data: data
				}).done(function(dataIn){
				
					var nothingFound = updateResults(dataIn);
					if(nothingFound === false)
						return;
					
					loadIndex++;
					data.lr_page = loadIndex-1;
					loadIndex++;
					
					$.ajax(window.location.pathname, {
						dataType : 'json',
						jsonp : 'callback',
						data: data
					})
					.done(function(data){
						resultsSaveBuffer = data;
						if(resultsSaveBuffer.data.length == 0 && (loadIndex > 1 || startOver && startOver.type == 'click')){
							$("#loadMore").hide();
							$("#endOfResults").show();	
						}
					});
				});
			}
			
			scrollbarFix($(".resultModal"));
		}
	};

	self.handleDataHideClick = function(e){
	
		self.isMetadataHidden(true);
	};
	
	self.handleDataClick = function(e){

		if(self.isMetadataHidden() == -1)
			displayObjectData(currentObjectMetadata);

		self.isMetadataHidden(false);
	};
	
	self.getShorterStr = function(a, length){
		
		if(a == undefined)
			return '';
		
		var obj = {};
		obj.title = a.title ? a.title : a;
		length = length ? length : 55;
		return (obj.title.length>length)? obj.title.substr(0, length - 3) + '...' : obj.title;
	};
	
	self.relatedTagSlice = function(e){
		
		lrConsole(e);
		buildDocList(e);
	};

    self.getShorterArr = function(str, length, url){

        if(typeof str == "string"){

            var temp = getLocation(str);

            //Check to see if we should transform the url
            if(urlTransform[temp.hostname] !== undefined && url === undefined && length === undefined)
                str = urlTransform[temp.hostname](temp);

            else str = (str.length > length)? str.substr(0, length) + "..." : str;

            return str;
        }

        else if(str !== undefined){
	
			if(url === true){
				
				var temp = ko.observableArray(str);
				temp.remove(function(item){
					
					
					return item == "" || ! isNaN(item);
				});

				
				return (temp().length > length)? temp().splice(0, length).join(", ") : temp().join(", ") ;
			}
			
			//lrConsole("KEYS: ", str);
            return (str.length > length)? str.splice(0, length).join(", ") : str.join(", ") ;
		}
    };

    self.currentObject = ko.observable({});
    self.currentResourceName = ko.observable("");


    //allOrganizations is defined outside of this script
    lrConsole(allOrganizations);
    self.allOrganizations = ko.observableArray(allOrganizations);
    self.allTerms = allTerms;

    self.checkTimelineLength = function(obj){

        if(obj === undefined) return 0;
        else return obj().length;
    };

    self.generateName = function(a, b, c){

        return a + "_" + b + "_" + c;
    };

    self.generateParadataText = function(e, i){

		/*
		 * TO-DO: Finish coming up with a generalized solution for most paradata documents
		 */
		
		var verb = e.activity.verb.action.toLowerCase();
		var dateStr = (e.activity.verb.date === undefined) ? "" : e.activity.verb.date;
		var content = (e.activity.content === undefined)? "hi" : e.activity.content;
		var measure = (e.activity.verb.measure === undefined)? "hi" : e.activity.verb.measure;

		//These three don't exist for viewed verb
		var detail = (e.activity.verb.detail === undefined)? "hi" : e.activity.verb.detail;

		var actor = (e.activity.actor === undefined)? "Unknown User" : (e.activity.actor.description == undefined && e.activity.actor.displayName !== undefined) ?
					e.activity.actor.displayName : e.activity.actor.description[0];

		var date = getDate(dateStr);
		
		//lrConsole("Final content char: ",content[content.length-1]);
		dateStr = moment(date.getTime()).format("M/D/YYYY"); //moment(date.getTime()).fromNow();

		//3DR paradata fixes. Remove period, and fix "a user". More fixes (for all orgs) to come.
		content = (content[content.length-1] == ".")? content.substr(0, content.length-1) : content;
		content = (content.indexOf("The a user") > -1)? "The anonymous user" + content.substr(10, content.length - 9): content;
		
		//Temporarily disabling paradata logo
		var imageTest = ''; //'<img height="80" width="80" src="http://www.learningregistry.org/_/rsrc/1332197520195/community/adl-3d-repository/3dr_logo.png" />'

        //Handle each verb differently        
        switch(verb){

            case "rated":
                return actor + " " + verb + " this " + generateAuthorSpan(dateStr, actor, undefined, i);

            case "commented":
                return detail + " " + generateAuthorSpan(actor + ", " + dateStr, actor, actor + " commented on this resource.", i);
                
            case "flagged":
                return detail + " <span style='color: #b94a48;' >This resource has been flagged.</span> " + generateAuthorSpan(actor + ", " + dateStr, actor, actor + " flagged this resource.", i);

            case "downloaded":
				return content + " " + measure.value + " times " + generateAuthorSpan(dateStr, actor, content, i);

			//published = uploaded for 3DR
			case "published":
				return content + " " + generateAuthorSpan(dateStr, actor, content, i);

			case "viewed":
				return imageTest + content + " is " + measure.value + generateAuthorSpan(dateStr, actor, undefined, i);

			case "matched":
				return actor + " has a match " + generateAuthorSpan(dateStr, actor, content, i);
        }


        return "Unable to parse paradata document.";
    };

    self.followOrganization = function(e){

        //return;
		
        /* Add jQuery/socket.io call here */
        $.ajax({
            type: "POST",
            url: "/main",
            dataType: "json",
            jsonp: false,
            contentType: 'application/json',
            data: createJSON(e, "follow"),
            success: function(data){

				lrConsole("added");
                self.allOrganizations.remove(e);
                self.followers.push({name:data.subject, content:[]});
            //lrConsole(data);
            },
            error: function(error){
                console.error(error);
            }
        });
    };

    self.followUser = function(name){

        var obj = new previewObject(name, []);
        obj.isUser = true;

        self.followers.push(obj);
        enableDrag();
        enableModal();
    };

    self.moveResourceToBookmark = function(index){

		lrConsole(self.currentObject());

		//Element was found in bookmarks
        if(self.bookmarks.indexOf(self.currentObject().url) !== -1){

            var currentName;
			currentName = "-2_0_bookmarks";
			self.currentResourceName(currentName);
			self.bookmarks.remove(self.currentObject().url);

			lrConsole(self.bookmarks().length);
        }

        else{

            /* Insert socket.io call here to add element to bookmarks, then check if successful */

            //Assign this resource a publisher, add it to bookmarks, and update currentResourceName to reflect that
            //self.currentObject().publisher = self.currentObject().url;
            self.bookmarks.push(self.currentObject().url);
            self.currentResourceName("-1_" + (self.bookmarks().length-1) + "_" + "bookmarks");

            lrConsole(self.bookmarks().length);
        }

        enableDrag();
        enableModal();
    };

	self.getImageSrc = function(url, screen){
		
		var u = (url)?getLocation(url) : {hostname:'void'};

		return urlTransform[u.hostname] ? urlTransform[u.hostname](u, screen) : screen;
		
	};
	
    self.deleteResource = function(){

        /* Add jQuery/socket.io call here*/

        self.data.remove(this);
    };

    self.addResource = function(){

        self.data.push(new previewObject("Organization", [new resourceObject("New Resource")]));
        enableDrag();
    };

    self.isCurrentBookmarked = function(){


		lrConsole("Is in bookmarks? ", self.bookmarks.indexOf(self.currentObject().url));
        return (self.bookmarks.indexOf(self.currentObject().url) !== -1);
    };

    self.getResourcesByFollowers = function(){

        var tempFollowersArr = [];
        var g = 0;

        for(var i = 0; i < self.followers().length; i++){

            //This allows us to call the function only once per iteration
            tempHoldingArr = self.getResourcesByUserName(self.followers()[i].name);

            if(tempHoldingArr.length > 0){

                tempFollowersArr[g] = tempHoldingArr;
                g++;
            }
        }

        return tempFollowersArr;
    };

    /*
        Returns an array of resourceObjects
    */
    self.getResourcesByUserName = function(user, resources){

        if(resources === undefined)
            resources = self.data();

        tempResourcesArr = [];

        for(var i = 0; i < resources.length; i++){

            if(resources[i].name == user){

                tempResourcesArr = resources[i].content();
                break;
            }
        }
        return tempResourcesArr;
    };

    self.getOrganizationAccordionId = function(index, str){

        return str + "org" + index();
    };

    self.getCollapseId = function(name, poundSign){
        if(poundSign === undefined)
            poundSign = "";
        return poundSign + name.replace(/\W/g, "");
    };
	
	self.wordpressLinkTransform = function(link, query){
		
		return link.replace("LRreplaceMe", hex_md5(query));
	};
};

// jQuery.XDomainRequest.js
// Author: Jason Moon - @JSONMOON
// IE8+
/*$.ajaxTransport("+*", function( options, originalOptions, jqXHR ) {
    
    if(jQuery.browser.msie && window.XDomainRequest) {
        
        var xdr;
        
        return {
            
            send: function( headers, completeCallback ) {

                // Use Microsoft XDR
                xdr = new XDomainRequest();
				
				var data = (options.data && options.type == "POST") ? "?" + options.data : "";	
                xdr.open(options.type, options.url + data);
                
                xdr.onload = function() {
                    
                    if(this.contentType.match(/\/xml/)){
                        
                        var dom = new ActiveXObject("Microsoft.XMLDOM");
                        dom.async = false;
                        dom.loadXML(this.responseText);
                        completeCallback(200, "success", [dom]);
                        
                    }else{
                        
                        completeCallback(200, "success", [this.responseText]);
                        
                    }

                };
				
				xdr.onprogress = function () { };
                
                xdr.ontimeout = function(){
                    completeCallback(408, "error", ["The request timed out."]);
                };
                
                xdr.onerror = function(){
					//alert("IE ERROR");
                    completeCallback(404, "error", ["The requested resource could not be found."]);
                };
				
				xdr.timeout = 5000;
                
				setTimeout(function () {
					xdr.send();
				}, 0);
				
          },
          abort: function() {
              if(xdr)xdr.abort();
			  lrConsole("abort!");
          }
        };
      }
    });*/
	
	
var Base64 = {
	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;

		input = Base64._utf8_encode(input);

		while (i < input.length) {

			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);

			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;

			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}

			output = output +
			Base64._keyStr.charAt(enc1) + Base64._keyStr.charAt(enc2) +
			Base64._keyStr.charAt(enc3) + Base64._keyStr.charAt(enc4);

		}

		return output;
	},

	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;

		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		while (i < input.length) {

			enc1 = Base64._keyStr.indexOf(input.charAt(i++));
			enc2 = Base64._keyStr.indexOf(input.charAt(i++));
			enc3 = Base64._keyStr.indexOf(input.charAt(i++));
			enc4 = Base64._keyStr.indexOf(input.charAt(i++));

			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;

			output = output + String.fromCharCode(chr1);

			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}

		}

		output = Base64._utf8_decode(output);

		return output;

	},

	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	},

	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}

		}
		return string;
	}
}