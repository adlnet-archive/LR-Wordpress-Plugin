<?php

class LRInterfaceCount extends WP_Widget
{
  function LRInterfaceCount()
  {
    $widget_ops = array('classname' => 'LRInterfaceCount', 'description' => 'Adds text showing LR indexed resources count' );
    $this->WP_Widget('LRInterfaceCount', 'LR Interface Count', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'count' => '' ) );
    $title = $instance['title'];
	$count = $instance['count'];
?>

<p>

	<label>
		Total Resources text - '$count' is replaced with the current number of indexed resources:
	</label>
	<textarea class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"><?php echo $count; ?></textarea>
	<br/><br/>
	
</p>
  
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
	$instance['count'] = $new_instance['count'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
	
    echo $before_widget;
    $count = empty($instance['count']) ? 0 : $instance['count'];
	$options = get_option('lr_options_object');
	$host  = empty($options['host']) ? "http://12.109.40.31" : $options['host'];
		
	?>

	<div>
		<p style="float:right; text-align: right;" id="LR_resourceCount"></p>
	</div>
	
	<script type="text/javascript">
		jQuery(document).ready(function($){
		
			$.getJSON('<?php echo $host; ?>/data',function(data){	
				
				$("#LR_resourceCount").html('<?php echo $count; ?>'.replace('$count', data['doc_count']));
			});
		});
	</script>
	<?php
    echo $after_widget;
  }
 
}