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
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'hide'=> '' ) );
    $title = empty($instance['title']) ? "Paradata" : $instance['title'];
    $hide = $instance['hide'];

?>
<p>
	<label for="<?php echo $this->get_field_id('title'); ?>">
		Title: 
	</label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
	<br/><br/>
	
	<label for="<?php echo $this->get_field_id('hide'); ?>">
		Check to hide this widget on results and preview pages: 
	</label>
	<input class="widefat" <?php echo $hide == 'on' ? 'checked' : ''; ?> id="<?php echo $this->get_field_id('hide'); ?>" name="<?php echo $this->get_field_name('hide'); ?>" type="checkbox" />
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
    $type = empty($_GET['type'])?'':$_GET['type'];
	$options = get_option('lr_options_object');

	if((empty($_GET['lr_resource']) || !empty($options['paradata'])) && $type != "slice" && empty($_GET["query"])){
		return;
	}
	
	
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

		<!--ko if: filterSearchTerms().length > 0 -->
		<div style="margin-bottom:3px;">Active Filters:</div>
		<!-- /ko -->
		<div style="margin-top:10px; padding-left:10px;" data-bind="foreach: filterSearchTerms">
			<label data-bind="text: $root.getShorterStr($data.name, 40), attr: {for: $data.name}"></label>
			<button class="LRxButton" title="Remove Filter" data-bind="click:$root.removeFilter, attr: {name: $data.name}">X</button>
		</div>		
		<div data-bind="with: getFilterSections">
			<div style="margin-bottom:3px;">By content type:</div>
			
			<div data-bind="foreach:$data.contentTypes" style="clear:both;overflow:hidden;width:100%;padding-left:10px;">				     					  
				<button data-bind="'html':$data.category, 'attr':{'name':$data.category, 'class':$data.style}, 'click':$root.handleContentTypeClick" style=""></button>
			</div>			
		</div>
		<!--ko if: accessibilityFeatures().length > 0 -->
		<div style="margin-bottom:3px;">By accessibility features:</div>
		<!-- /ko -->
		<div data-bind="foreach: accessibilityFeatures"	style="clear:both;overflow:hidden;width:100%;padding-left:10px;">
			<button class="filterPublisher btn" data-bind="'html':$data.name, 'attr':{'name':$data.name, 'class':$data.style}, 'click':$root.applyFilter" style=""></button>					
		</div>				
		<script type="text/javascript">
			<?php include_once('templates/scripts/applicationPreview.php'); ?>
		
			lrConsole("Testing utility.. ", self);
			$(document).ready(function(){
				
				$(".filterPublisherSelect").val("All Publishers");
				
				/*$(document).on("click", ".LRxButton", function(e){
					
					self.results.removeAll();
					self.filterSearchTerms()[0] = '';
					self.filterSearchTerms.valueHasMutated();
					self.loadNewPage(false, true);
				});*/
				
				$(document).on("change", ".filterPublisherSelect", function(e){

					self.results.removeAll();
					
					var cacheJobj = $(this).find("option:selected");
					self.filterSearchTerms()[0] = cacheJobj.val();
					
					if(self.filterSearchTerms()[0] == 'All publishers'){
						self.filterSearchTerms()[0] = '';
					}
					
					lrConsole(self.filterSearchTerms());
					self.filterSearchTerms.valueHasMutated();
					self.loadNewPage(false, true);
				});		
			});
		</script>
	
	<?php
		echo $after_widget;
		return;
	}
	
	if(empty($options['paradata'])){
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
	
	<?php } ?>
	
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