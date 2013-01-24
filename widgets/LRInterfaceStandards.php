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
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;;
	
	$host  = "http://12.109.40.31"; //empty($instance['host']) ? "http://12.109.40.31" : $instance['host'];
	
	if(!empty($_GET['lr_resource']))
		include_once("templates/LRInterfacePreviewTemplate.php");
	else
		include_once("templates/LRInterfaceStandardsTemplate.php");

    echo $after_widget;
  }
 
}