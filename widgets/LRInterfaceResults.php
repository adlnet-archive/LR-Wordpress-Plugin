<?php

class LRInterfaceResults extends WP_Widget
{

  function LRInterfaceResults()
  {
    $widget_ops = array('classname' => 'LRInterfaceResults', 'description' => 'Adds LR search results to a page' );
    $this->WP_Widget('LRInterfaceResults', 'LR Interface Results', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'numResults' => '', 'text' => '') );
    $title = $instance['title'];
	$numResults = $instance['numResults'];
	$text = $instance['text'];

?>

<p>
		
	
	<label>
		Number of Results Per Page:
	</label>
	<input class="widefat" id="<?php echo $this->get_field_id('numResults'); ?>" name="<?php echo $this->get_field_name('numResults'); ?>" type="text" 
	       value="<?php echo (attribute_escape($numResults))?attribute_escape($numResults):'50'; ?>" />
	<br/><br/>	
	
	<label>
		Results page text - '$query' is replaced with the user's search terms:
	</label>
	<input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo $text; ?>" />
	<br/><br/>
</p>
  
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['text'] = $new_instance['text'];
    $instance['numResults'] = (is_numeric($new_instance['numResults']) && $new_instance['numResults'] > 0)? $new_instance['numResults'] : 50;
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
	
	$options = get_option('lr_options_object');
	
	$text = str_ireplace( '$query', $_GET['query'], $instance['text']);
	
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
	
	$type  = $_GET['type'] == 'index' || $_GET['type'] == 'slice'  ? $_GET['type'] : 'index';
	$host  = empty($options['host']) ? "http://12.109.40.31" : $options['host'];
 
    if (!empty($title))
      echo $before_title . $after_title;;
 
	if(!empty($_GET['lr_resource']))
		include_once("templates/LRInterfacePreviewTemplate.php");
	else
		include_once("templates/LRInterfaceResultsTemplate.php");
		
    echo $after_widget;
  }
 
}