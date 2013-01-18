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
	
	if(empty($_GET['lr_resource'])){
		echo $after_widget;
		return;
	}
	
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $after_title;;
	
	$host  = "http://12.109.40.31";//empty($instance['host']) ? "http://12.109.40.31" : $instance['host'];
	?>
	
	
	<div class="modal-timeline">
		<table id="timeline-table" class="table table-striped">
			<th data-bind="visible: $.inArray(temp.currentObject().url, tempBookmarksArray) == -1" ><a class="btn btn-info" id="bookmark">Bookmark Resource</a></th>
			<th data-bind="visible: $.inArray(temp.currentObject().url, tempBookmarksArray) > -1" ><a class="btn btn-info disabled">Bookmark Resource</a></th>
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
	
	
	
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/moment.min.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript">
		<?php include_once('templates/scripts/applicationPreview.php'); ?>
	</script>
	<?php
    echo $after_widget;
  }
 
}