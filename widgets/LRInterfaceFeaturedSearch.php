<?php

class LRInterfaceFeaturedSearch extends WP_Widget
{
  function LRInterfaceFeaturedSearch()
  {
    $widget_ops = array('classname' => 'LRInterfaceFeaturedSearch', 'description' => 'Display a preview of featured search terms' );
    $this->WP_Widget('LRInterfaceFeaturedSearch', 'LR Interface Featured Search Terms', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'hide' => '', 'ids' => '') );
    $title = $instance['title'];
    $hide = $instance['hide'];
    $ids = $instance['ids'];
	 wp_enqueue_media();
?>
<p>

	<span>Current featured searches:</span>
	<textarea style="width: 100%; height: 80px; background: #f3f3f3;" class="lr_list_featured_searches" disabled="disabled"></textarea><br/><br/>
	
	<button class="mca_tray_button">Select Images for Featured Search</button>
	<input id="<?php echo $this->get_field_id('ids'); ?>" name="<?php echo $this->get_field_name('ids'); ?>" type="hidden" value="<?php echo attribute_escape($ids); ?>" /><br/><br/>


	<label for="<?php echo $this->get_field_id('hide'); ?>">
		Check to hide this widget on results and preview pages: 
	</label>
	<input class="widefat" <?php echo $hide == 'on' ? 'checked' : ''; ?> id="<?php echo $this->get_field_id('hide'); ?>" name="<?php echo $this->get_field_name('hide'); ?>" type="checkbox" />
	<br/><br/>
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
			
			jQuery('.lr_list_featured_searches').text(text);
		};
		
		setText();
		
		jQuery('.mca_tray_button').click(function( event ){
			 
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
    $instance['title'] = trim($new_instance['title']);
    $instance['hide'] = $new_instance['hide'];
    $instance['ids'] = $new_instance['ids'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
	
    extract($args, EXTR_SKIP);
	
	if($instance['hide'] == 'on' && (! empty($_GET['query']) || ! empty($_GET['lr_resource'])))
		return;
 
	$options = get_option('lr_options_object');
	
    $ids = empty($instance['ids']) ? ' ' : $instance['ids'];
    $resources = empty($instance['resources']) ? array('') : explode(';', $instance['resources']);
    $host  = empty($options['host']) ? "http://12.109.40.31" : $options['host'];
	
	 echo $before_widget;
	?>
	
	<div class="lr_free_images" id="lr_featured_search" data-bind="foreach:images">
		<a data-bind="attr:{href:href}">
			<img data-bind="attr:{src:url, alt:title}" />
		</a>
	</div>
		
	<script type="text/javascript">
		<?php if(empty($_GET['query']) && empty($_GET['lr_interface'])){
			@include_once('templates/scripts/applicationPreview.php'); 
		} ?>
		
		var ids = '<?php echo $ids; ?>' ? JSON.parse('<?php echo $ids; ?>') : '';
		for(var i = 0; i < ids.length; i++){
			
			ids[i].href = '<?php echo add_query_arg(array("query"=>"LRreplaceMe","filter"=>"free.ed.gov"), get_page_link( $options['results']));?>'.replace("LRreplaceMe", encodeURIComponent(ids[i].title));
			self.images.push(ids[i]);
		}
	</script>

	<?php

	  echo $after_widget;
  }
}