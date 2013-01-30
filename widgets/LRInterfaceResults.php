<?php

class LRInterfaceResults extends WP_Widget
{

   public $nodeArray = array("http://node01.public.learningregistry.net", "http://node02.public.learningregistry.net", "http://lrtest01.public.learningregistry.net", "http://sandbox.learningregistry.org/", "http://lrdev03.learningregistry.org", "http://lrdev05.learningregistry.org");

  function LRInterfaceResults()
  {
    $widget_ops = array('classname' => 'LRInterfaceResults', 'description' => 'Adds LR search results to a page' );
    $this->WP_Widget('LRInterfaceResults', 'LR Interface Results', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'host' => '', 'type' => '', 'numResults' => '', 'node' => '' ) );
    $title = $instance['title'];
	$host = $instance['host'];
	$type = $instance['type'];
	$numResults = $instance['numResults'];
	$node = $instance['node'];
	
	//echo $nodeArray[0];
?>

<p>
		
	<label for="<?php echo $this->get_field_id('type'); ?>">
		Search Method:
	</label>
	<select class="widefat" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
		<option value="index" <?php echo attribute_escape($type) == "index" ? 'selected="selected"':''; ?>>Indexed Search</option>
		<option value="slice" <?php echo attribute_escape($type) == "slice" ? 'selected="selected"':''; ?>>Slice</option>
	</select>
	<br/><br/>	
	
	<label for="<?php echo $this->get_field_id('node'); ?>">
		Learning Registry Node:
	</label>
	<select class="widefat" id="<?php echo $this->get_field_id('node'); ?>" name="<?php echo $this->get_field_name('node'); ?>">
		
		<?php $len = sizeof($this->nodeArray); for($i = 0; $i < $len; $i++): ?>
		
		
			<option value="<?php echo $i; ?>" <?php echo attribute_escape($node) == $this->nodeArray[$i] ? 'selected="selected"':''; ?> ><?php echo $this->nodeArray[$i]; ?></option>
		
		<?php endfor; ?>
	</select>
	<span> [Note: Move to plugin settings page]</span>
	<br/><br/>
	
	<label>
		Number of Results Per Page:
	</label>
	<input class="widefat" id="<?php echo $this->get_field_id('numResults'); ?>" name="<?php echo $this->get_field_name('numResults'); ?>" type="text" 
	       value="<?php echo (attribute_escape($numResults))?attribute_escape($numResults):'50'; ?>" />
	<br/><br/>
	
	<label>
		WebService Endpoint:
	</label>
	<input class="widefat" id="<?php echo $this->get_field_id('host'); ?>" name="<?php echo $this->get_field_name('host'); ?>" type="text" value="<?php echo (attribute_escape($host))?attribute_escape($host):'http://12.109.40.31'; ?>" />
</p>
  
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['host'] = $new_instance['host'];
    $instance['type'] = $new_instance['type'];
    $instance['numResults'] = (is_numeric($new_instance['numResults']) && $new_instance['numResults'] > 0)? $new_instance['numResults'] : 50;
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
	$type  = empty($instance['type']) ? "index" : $instance['type'];
	$host  = empty($instance['host']) ? "http://12.109.40.31" : $instance['host'];
 
    if (!empty($title))
      echo $before_title . $after_title;;
 
	if(!empty($_GET['lr_resource']))
		include_once("templates/LRInterfacePreviewTemplate.php");
	else
		include_once("templates/LRInterfaceResultsTemplate.php");
		
    echo $after_widget;
  }
 
}