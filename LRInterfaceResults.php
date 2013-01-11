<?php
class LRInterfaceResults extends WP_Widget
{
  function LRInterfaceResults()
  {
    $widget_ops = array('classname' => 'LRInterfaceResults', 'description' => 'Adds LR search results to a page' );
    $this->WP_Widget('LRInterfaceResults', 'LR Interface Results', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'type' => '' ) );
    $title = $instance['title'];
	$type = $instance['type'];
?>

<p>
	<label for="<?php echo $this->get_field_id('title'); ?>">
		Title: 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
	</label>
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
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;;
 
	echo 'This is the query var: ' . get_query_var('query');
	print_r($_GET);
	echo '<br/><br/>RESULTS PAGE';
 
    echo $after_widget;
  }
 
}