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

  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
	
	if(empty($_GET['lr_resource'])){
		echo $after_widget;
		return;
	}
	
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $after_title;;
	
	$options = get_option('lr_options_object');
	$host  = empty($options['host']) ? "http://12.109.40.31" : $options['host'];
?>
	
	
	<div class="modal-timeline">
		<table id="timeline-table" class="table table-striped">
			<tbody data-bind="foreach: getReversedTimeline()">
				<tr>
					<td data-bind="style: { 'background-color': $index()%2 == 1 ? '#F9F9F9' : '#FFF', 'border-top': $index()==0 ? 'none' : '1px #DDD solid'}">
						<div data-bind="html: $root.generateParadataText($data, $index()), attr:{id: 'paradata' + $index() }"></div>
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
		<?php include_once('templates/scripts/applicationPreview.php'); ?>
	</script>
	<?php
    echo $after_widget;
  }
 
}