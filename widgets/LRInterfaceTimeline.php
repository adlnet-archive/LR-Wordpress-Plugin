<?php

class LRInterfaceTimeline extends WP_Widget
{
  function LRInterfaceTimeline()
  {
    $widget_ops = array('classname' => 'LRInterfaceTimeline', 'description' => 'Automatically adds an LR paradata timeline to a resource preview page' );
    $this->WP_Widget('LRInterfaceTimeline', 'LR Interface Paradata Timeline', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'results' => '', 'placeholder'=>'' ) );
    $title = $instance['title'];
	$results = $instance['results'];
	$placeholder = $instance['placeholder'];
	$host  = empty($instance['host']) ? "http://12.109.40.31" : $instance['host'];
?>

<p>

	<label for="<?php echo $this->get_field_id('placeholder'); ?>">
		Search Placeholder: 
	</label>
	<input class="widefat" id="<?php echo $this->get_field_id('placeholder'); ?>" name="<?php echo $this->get_field_name('placeholder'); ?>" type="text" value="<?php echo attribute_escape($placeholder); ?>" />
	<br/><br/>
	
	<label for="<?php echo $this->get_field_id('results'); ?>">
		Results: 
	</label>
	<select class="widefat" id="<?php echo $this->get_field_id('results'); ?>" name="<?php echo $this->get_field_name('results'); ?>"> 
		<option value=""><?php echo esc_attr( __( 'Select a results page' ) ); ?></option> 
		<?php 
			$pages = get_pages(); 
			foreach ( $pages as $page ) {
				$option = ($page->ID == attribute_escape($results)) ? '<option selected="selected" value="' . $page->ID . '">' : '<option value="' . $page->ID . '">';
				$option .= $page->post_title;
				$option .= '</option>';
				echo $option;
			}
		?>
	</select>
	
</p>
  
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
	$instance['results'] = $new_instance['results'];
	$instance['placeholder'] = $new_instance['placeholder'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $after_title;;
 
	?>
	
	
	<div class="modal-timeline">
		<table id="timeline-table" class="table table-striped">
			<th data-bind="visible: $.inArray(temp.currentObject().url, tempBookmarksArray) == -1" ><a class="btn btn-info" id="bookmark">Bookmark Resource</a></th>
			<th data-bind="visible:  $.inArray(temp.currentObject().url, tempBookmarksArray) > -1" ><a class="btn btn-info disabled">Bookmark Resource</a></th>
			<tbody data-bind="foreach: getReversedTimeline()">
				<tr>
					<td data-bind="style: { 'background-color': $index()%2 == 1 ? '#F9F9F9' : '#FFF', 'border-top': $index()==0 ? 'none' : '1px #DDD solid'}">
								   
						<div data-bind="html: $root.generateParadataText($data, $index()), attr:{id: 'paradata' + $index() }"></div>
					</td>			   
				</tr>
			</tbody>
		</table>
		
		<div style="text-align: center;">
			<span data-bind="if: checkTimelineLength(currentObject().timeline) == 0">{{^hideFrame}}Be the first to interact (below){{/hideFrame}}{{#hideFrame}}Paradata not found{{/hideFrame}}</span>
		</div>
	</div>
	
	
	
	
	<script type="text/javascript">
		var allOrganizations = [], followed = [], allTerms = [], query = "<?php echo $_GET['query']; ?>";
		var temp = new mainViewModel([]), activeModalName = null, lastSearchCache = "";
		var iframeHidden = true;
		var tempBookmarksArray = [];
		handleMainResourceModal(query);
		
		for (var f in followed){
			temp.followers.push({name:followed[f], content:[]});
		}
		
		var handlePerfectSize = function(){};
		var serviceHost = "<?php echo $host; ?>";
		var initialGraphBuild = false;
		
		totalSlice = 15;
		newLoad = 15;
		ko.applyBindings(temp);

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
				
				window.top.location = '/frame'  
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
	</script>
	<?php
    echo $after_widget;
  }
 
}