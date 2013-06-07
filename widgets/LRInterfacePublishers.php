<?php

class LRInterfacePublishers extends WP_Widget
{
  function LRInterfacePublishers()
  {
    $widget_ops = array('classname' => 'LRInterfacePublishers', 'description' => 'Displays a searchable list of LR publishers.' );
    $this->WP_Widget('LRInterfacePublishers', 'LR Interface Publishers List', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '') );
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
	$options = get_option('lr_options_object');	
	
	$host  = empty($options['host']) ? "http://12.109.40.31" : $options['host'];	
	extract($args, EXTR_SKIP);
	
	echo $before_widget;
	echo $before_title . $title .  $after_title;;
	?>

		<div data-bind="">
			Testing Publishers
		</div>
		<script type="text/javascript">
			var serviceHost = "<?php echo $host; ?>";
			<?php include_once('templates/scripts/applicationPreview.php'); ?>
			
			lrConsole("Testing utility.. ", self);
			$(document).ready(function(){});
		</script>
	
	<?php
		echo $after_widget;
		return;
  }
} 