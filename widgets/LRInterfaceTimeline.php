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

	if(empty($_GET['lr_resource']) && $type != "slice"){
		return;
	}
	
	$options = get_option('lr_options_object');
	$host  = empty($options['host']) ? "http://12.109.40.31" : $options['host'];	
	extract($args, EXTR_SKIP);
	echo $before_widget;
	
	if(!empty($_GET["query"]) && $type == "slice"){
	
	    $title = apply_filters('widget_title', 'Related Tags');
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