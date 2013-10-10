<?php

class LRInterfaceStandards extends WP_Widget
{
  function LRInterfaceStandards()
  {
    $widget_ops = array('classname' => 'LRInterfaceStandards', 'description' => 'Enables discovery of LR resources aligned to standards' );
    $this->WP_Widget('LRInterfaceStandards', 'LR Interface Standards Browser', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '') );
    $title = $instance['title'];
	
	
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
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;
	
	$options = get_option('lr_options_object');
	$host  = empty($options['host']) ? "http://12.109.40.31" : $options['host'];
	
	if(!empty($_GET['lr_resource']))
		include_once("templates/LRInterfacePreviewTemplate.php");
	else
		include_once("templates/LRInterfaceStandardsTemplate.php");

    echo $after_widget;
  }
 
}