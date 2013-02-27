<?php

class LRInterfaceSubject extends WP_Widget
{
  function LRInterfaceSubject()
  {
    $widget_ops = array('classname' => 'LRInterfaceSubject', 'description' => 'Enables discovery of LR resources aligned to subjects' );
    $this->WP_Widget('LRInterfaceSubject', 'LR Interface Subject Map', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'results' => '') );
    $title = $instance['title'];
    $results = $instance['results'];
	
	?>
	
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
	
	<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['results'] = $new_instance['results'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    /*$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;;*/
	
	$options = get_option('lr_options_object');
	$host  = empty($options['host']) ? "http://12.109.40.31" : $options['host'];
	
	include_once("templates/LRInterfaceSubjectTemplate.php");

    echo $after_widget;
  }
 
}