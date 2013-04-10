<?php

class LRInterfaceIENotSupported extends WP_Widget
{
  function LRInterfaceIENotSupported()
  {
    $widget_ops = array('classname' => 'LRInterfaceIENotSupported', 'description' => 'Displays a message if the user\'s browser is an unsupported version of IE.' );
    $this->WP_Widget('LRInterfaceIENotSupported', 'LR Interface IE Detector', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = empty($instance['title'])?"We have detected that you may be using an unsupported version of Internet Explorer. You may experience errors while browsing this website. Please consider upgrading." : $instance['title'];
	

?>


<p>

	<label for="<?php echo $this->get_field_id('title'); ?>">
		IE Detected Message: 
	</label>
	<textarea class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo attribute_escape($title); ?>" ><?php echo $title;?></textarea>
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
	$title = empty($instance['title']) ? "We have detected that you may be using an unsupported version of Internet Explorer. You may experience errors while browsing this website. Please consider upgrading." : $instance['title'];
	
	?>
		<div class="IE_error">
			<?php echo $title; ?>
		</div>
		<script type="text/javascript">
			(function(){
				if($.browser){
				
					var version = parseInt($.browser.version);
					
					if($.browser.msie && version <= 7)
						$(".IE_error").show();
				}
			})();
		</script>
	
	<?php

  }
}