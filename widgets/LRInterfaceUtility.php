<?php

class LRInterfaceUtility extends WP_Widget
{
  function LRInterfaceUtility()
  {
    $widget_ops = array('classname' => 'LRInterfaceUtility', 'description' => 'Controls paradata timeline, slice filter, and index filter' );
    $this->WP_Widget('LRInterfaceUtility', 'LR Interface Sidebar Utility', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = empty($instance['title']) ? "Paradata" : $instance['title'];
	

?>


<p>

	<label for="<?php echo $this->get_field_id('title'); ?>">
		Title: 
	</label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
	<br/><br/>
	
</p>

<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    $type = $_GET['type'];

	if(empty($_GET['lr_resource']) && $type != "slice" && empty($_GET["query"])){
		return;
	}
	
	$options = get_option('lr_options_object');
	$host  = empty($options['host']) ? "http://12.109.40.31" : $options['host'];	
	extract($args, EXTR_SKIP);
	echo $before_widget;
	
	
	//If the user is doing a slice
	if(!empty($_GET["query"]) && $type == "slice"){
	
	    $title = apply_filters('widget_title', 'Filter');
		echo $before_title . $title .  $after_title;;
	?>
		<ul class="relatedTerms" style="padding-top:10px;" data-bind="foreach: relatedResultsNodes">
			<li>
				<a style="cursor:pointer;" class="relatedList" data-bind="text:(name[0] == undefined)?'':name[0].toUpperCase() + name.substr(1, name.length-1), click:$root.relatedTagSlice, clickBubble: false"></a>
			</li>
		</ul>
	<?php 
		echo $after_widget;
		return;
	}
	
	if(!empty($_GET["query"])){
		$title = apply_filters('widget_title', 'Filter');
		echo $before_title . $title .  $after_title;;
	?>

		<div data-bind="with: getFilterSections">
			<div>By content type:</div>
			
			<div data-bind="foreach:$data.contentTypes" style="clear:both;overflow:hidden;width:100%;padding-left:10px;">	

				<button class="filterPublisher btn" data-bind="'html':$data, 'attr':{'href': '#','name':$data, 'class':$data==$root.filterSearchTerms()[1]?'filterPublisherOn':'filterPublisher'}, 'click':$root.handlePublisherClick" style=""></button>
			</div>
			
			
			<div style="margin-top:10px;">By publisher:</div>
			<div data-bind="visible: $data.publishers.length > 0">
				
				<select class="filterPublisherSelect" style="width:90%;" data-bind="foreach:$data.publishers">	
					<option data-bind="'html': $root.getShorterStr($data, 40), 'attr':{'value':$data}"></option>
				</select>
			</div>
			<div style="margin-top:10px; padding-left:10px;">
				<span data-bind="visible:$root.filterSearchTerms()[0], text: $root.getShorterStr($root.filterSearchTerms()[0], 40)"></span>
				<button class="LRxButton" title="Remove Filter" data-bind="visible:$root.filterSearchTerms()[0]">X</button>
			</div>
		</div>
		<script type="text/javascript">
			<?php include_once('templates/scripts/applicationPreview.php'); ?>
		
			console.log("Testing utility.. ", self);
			$(document).ready(function(){
				
				$(".filterPublisherSelect").val("All Publishers");
				
				$(document).on("click", ".LRxButton", function(e){
					
					self.results.removeAll();
					self.filterSearchTerms()[0] = '';
					self.filterSearchTerms.valueHasMutated();
					self.loadNewPage(false, true);
				});
				
				$(document).on("change", ".filterPublisherSelect", function(e){

					self.results.removeAll();
					
					var cacheJobj = $(this).find("option:selected");
					self.filterSearchTerms()[0] = cacheJobj.val();
					
					if(self.filterSearchTerms()[0] == 'All publishers'){
						self.filterSearchTerms()[0] = '';
					}
					
					console.log(self.filterSearchTerms());
					self.filterSearchTerms.valueHasMutated();
					self.loadNewPage(false, true);
				});		
			});
		</script>
	
	<?php
		echo $after_widget;
		return;
	}
	
	
    $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title .  $after_title;;
?>	
	
	<div class="modal-timeline">
		<table id="timeline-table" class="table table-striped">
			<tbody data-bind="foreach: getReversedTimeline()">
				<tr>
					<td data-bind="style: { 'background-color': $index()%2 == 1 ? '#F9F9F9' : '#FFF', 'border-top': $index()==0 ? 'none' : '1px #DDD solid'}">
						<p data-bind="html: $root.generateParadataText($data, $index()), attr:{id: 'paradata' + $index() }"></p>
					</td>			   
				</tr>
			</tbody>
		</table>
		
		<div style="text-align: center;">
			<span data-bind="if: checkTimelineLength(currentObject().timeline) == 0">Paradata not found</span>
		</div>
	</div>
	
	<script type="text/javascript" src="<?php echo plugins_url( '/templates/scripts/moment.min.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript">
		var serviceHost = "<?php echo $host; ?>";
		var NODE_URL = '<?php echo empty($options['node'])?"http://node01.public.learningregistry.net/":$options['node']; ?>';
		<?php include_once('templates/scripts/applicationPreview.php'); ?>
	</script>
	<?php
    echo $after_widget;
  }
}