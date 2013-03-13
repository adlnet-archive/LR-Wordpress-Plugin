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
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'ids' => '') );
    $title = $instance['title'];
    $ids = $instance['ids'];
	 wp_enqueue_media();
?>
<p>

	<span>Current featured searches:</span>
	<textarea style="width: 100%; height: 80px; background: #f3f3f3;" class="lr_list_subject_resources" disabled="disabled"></textarea><br/><br/>
	
	<button class="subject_upload_button">Select Images for Subject Sub-menu</button>
	<input id="<?php echo $this->get_field_id('ids'); ?>" name="<?php echo $this->get_field_name('ids'); ?>" type="hidden" value="<?php echo attribute_escape($ids); ?>" /><br/><br/>

</p>
  
  <script type="text/javascript">
	(function(){
		var file_frame;
		var ids = '<?php echo $ids; ?>' ? JSON.parse('<?php echo $ids; ?>') : '';
		var setText = function(){
			var text = '';
			for(var i = 0; i < ids.length; i++){
			
				text += ids[i].title + '\n';
			}
			
			jQuery('.lr_list_subject_resources').text(text);
		};
		
		setText();
		
		jQuery('.subject_upload_button').click(function( event ){
			 
			  event.preventDefault();
			 
				if ( file_frame ) {
					file_frame.open();
					return;
				}
			 
				file_frame = wp.media.frames.file_frame = wp.media({
					title: jQuery( this ).data( 'uploader_title' ),
					button: {
						text: jQuery( this ).data( 'uploader_button_text' ),
					},
					multiple: true  
				});
			 
				file_frame.on( 'select', function() {
					
					attachment = file_frame.state().get('selection').toJSON();
					ids = [];
					for(var i = 0; i < attachment.length; i++){
					
						ids.push({title:attachment[i].title, url: attachment[i].url, caption: attachment[i].caption});
					}
			 
					// "mca_features_tray" is the ID of my text field that will receive the image
					// I'm getting the ID rather than the URL:

					jQuery("#<?php echo $this->get_field_id('ids'); ?>").val(JSON.stringify(ids));
					setText();			 
				});
			 
				file_frame.open();
		});
			// "mca_tray_button" is the ID of my button that opens the Media window 
	})();
</script>
  
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
    /*$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;;*/
	
	$options = get_option('lr_options_object');
	$host  = empty($options['host']) ? "http://12.109.40.31" : $options['host'];
	
	include_once("templates/LRInterfaceSubjectTemplate.php");

    echo $after_widget;
  }
 
}