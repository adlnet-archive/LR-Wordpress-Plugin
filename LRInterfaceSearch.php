<?php
/*
Plugin Name: LR Interface Search
Plugin URI: http://adlnet.gov/
Description: Search bar for LR Interface
Author: ADL Tech Team
Version: 1
Author URI: http://adlnet.gov/
*/
 
 
class LRInterfaceSearch extends WP_Widget
{
  function LRInterfaceSearch()
  {
    $widget_ops = array('classname' => 'LRInterfaceSearch', 'description' => 'Add an LR search bar to a page' );
    $this->WP_Widget('LRInterfaceSearch', 'LR Interface Search Bar', $widget_ops);
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
	<label for="<?php echo $this->get_field_id('type'); ?>">
		Search Method:
		<select class="widefat" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
			<option>Indexed Search</option>
			<option>Slice</option>
		</select>
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
 
	// WIDGET CODE GOES HERE
	echo 'LR Search Bar: <input type="text" placeholder="Search for resources" name="lrSearchBar" /><input type="submit" value="Search" />';
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("LRInterfaceSearch");') );?>