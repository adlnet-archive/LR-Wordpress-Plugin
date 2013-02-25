<?php

class LRInterfaceFeatured extends WP_Widget
{
  function LRInterfaceFeatured()
  {
    $widget_ops = array('classname' => 'LRInterfaceFeatured', 'description' => 'Display a preview of featured resources' );
    $this->WP_Widget('LRInterfaceFeatured', 'LR Interface Featured Resources', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'resources' => '' ) );
    $title = $instance['title'];
    $resources = $instance['resources'];
?>

<p>

	<label for="<?php echo $this->get_field_id('title'); ?>">
		Title: 
	</label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
	<br/><br/>	
	<label for="<?php echo $this->get_field_id('resources'); ?>">
		Enter Resource ID's separated by semicolons: 
	</label>
	<textarea class="widefat" id="<?php echo $this->get_field_id('resources'); ?>" name="<?php echo $this->get_field_name('resources'); ?>" type="text"><?php echo attribute_escape($resources); ?></textarea>
	<br/><br/>
</p>
  
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = trim($new_instance['title']);
    $instance['resources'] = trim($new_instance['resources']);
    return $instance;
  }
  
   function display_rand_resource($arr, $host)
  {
  
	$i = rand(0, sizeof($arr) - 1);
	?>
	<script type="text/javascript">
		var serviceHost = "<?php echo $host; ?>";
		var NODE_URL = '<?php echo empty($options['node'])?"http://node01.public.learningregistry.net/":$options['node']; ?>';

		var qmarkUrl = '<?php echo plugins_url( "/images/qmark.png" , dirname(__FILE__) ) ?>';
		
		<?php if(empty($_GET['query'])){
			include_once('templates/scripts/applicationPreview.php'); 
		} ?>
		
		$(document).ready(function(){
			$.getJSON(serviceHost + '/data/?keys=' + encodeURIComponent(JSON.stringify(['<?php echo trim($arr[$i]); ?>'])),function(data){		
			

				var md5 = '<?php echo trim($arr[$i]); ?>';
				
				if(data[0]){
					data = data[0];
					var src = data.url;
					
					//This is done because observable.valueHasMutated wasn't working..
					var currentObject = new resourceObject("Item", src);
					currentObject.timeline = self.currentObject().timeline;
					currentObject.title = (data.title == undefined) ? doTransform(src) : data.title;
					currentObject.description = (data.description == undefined) ? "" : data.description;
					currentObject.url = (data.url == undefined) ? "" : data.url;
					
					console.log("qmarkUrl: ", qmarkUrl);
					var imageUrl = qmarkUrl? qmarkUrl:"/images/qmark.png";
					
					currentObject.image = (data.hasScreenshot !== true) ? imageUrl : serviceHost + "/screenshot/" + md5;
					currentObject.image = self.getImageSrc(data.url, currentObject.image);
					currentObject.hasScreenshot = currentObject.image != imageUrl;				
					
					self.featuredResource(currentObject);
				}
				
				else{
					
					src = data.url;
					var imageUrl = qmarkUrl? qmarkUrl:"/images/qmark.png";
					var currentObject = new resourceObject("Item", src);
					currentObject.image = self.getImageSrc(data.url, imageUrl);
					currentObject.hasScreenshot = currentObject.image != imageUrl;		
					
					self.featuredResource(currentObject);
				}
			});
		});
	</script>
	<div style="margin-bottom: 10px;">
		<a data-bind="text:getShorterStr(featuredResource().title, 45), attr:{href:wordpressLinkTransform(permalink,featuredResource().url), title:featuredResource().title}" class="title"></a><br/>
	</div>
	<a data-bind="attr:{href:wordpressLinkTransform(permalink,featuredResource().url)}" class="title">
		<img data-bind="attr:{src:$root.getImageSrc(false, serviceHost + '/screenshot/<?php echo trim($arr[$i]); ?>')}" class="img-polaroid" />
	</a>

	<?php
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
	$options = get_option('lr_options_object');
	
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    $resources = empty($instance['resources']) ? array('') : explode(';', $instance['resources']);
    $host  = empty($options['host']) ? "http://12.109.40.31" : $options['host'];

    if (!empty($title))
      echo $before_title . $title . $after_title;
		
	$this->display_rand_resource($resources, $host);

    echo $after_widget;
  }
  

 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
}