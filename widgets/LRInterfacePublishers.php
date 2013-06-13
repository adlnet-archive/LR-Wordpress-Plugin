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
	?>
	<div style="width:100%;clear:both;float:left;">
		<ul style="width:48%;float:left;" data-bind="foreach: publishers.slice(0, publishers().length*.5)">
			<li style="margin-bottom: 10px;">
				<a data-bind="text: $data.length>27?$data.substr(0,27)+'...':$data, attr: {href: $root.fixPublisherURL($data)}"></a><br/>
			</li>
		</ul>
		<ul style="width:48%;float:right;" data-bind="foreach: publishers.slice(publishers().length*.5, publishers().length)">
			<li style="margin-bottom: 10px;">
				<a data-bind="text: $data.length>27?$data.substr(0,27)+'...':$data, attr: {href: $root.fixPublisherURL($data)}"></a><br/>
			</li>
		</ul>
	</div>
	<div class="pubSpinner" style="width:100%;">
	
	</div>
	<div>
		<!-- ko if: loadMore -->
			<button data-bind="click: previous, visible:publishers().length>0 && page() > 0">Previous</button>
			<span data-bind="text:'Page: ' + (page() + 1)">&nbsp;</span>
			<button data-bind="click: load, visible:publishers().length>0">Next</button>
		<!-- /ko -->
	</div>
		<script type="text/javascript">
			var serviceHost = "<?php echo $host; ?>";
			var publishersSpin = new Spinner(opts).spin($('.pubSpinner')[0]);
			<?php include_once('templates/scripts/applicationPreview.php'); ?>
			
			$(function(){
				self.load();
			});

		</script>
	
	<?php
		echo $after_widget;
		return;
  }
} 

