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
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'resources' => '', 'total' => '', 'hide' => '') );
    $title = $instance['title'];
    $resources = $instance['resources'];
    $total = $instance['total'];
    $hide = $instance['hide'];
?>

<p>

	<label for="<?php echo $this->get_field_id('title'); ?>">
		Title: 
	</label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
	<br/><br/>		
	
	<label for="<?php echo $this->get_field_id('total'); ?>">
		Enter desired number of featured resource boxes: 
	</label>
	
	
	<select class="widefat" id="<?php echo $this->get_field_id('total'); ?>" name="<?php echo $this->get_field_name('total'); ?>" type="text">
	
	 <?php for($g = 1; $g < 6; $g++): ?>
			<option value="<?php echo $g; ?>" <?php echo $total == $g ? 'selected="selected"' : ''; ?>><?php echo $g; ?></option>
	 <?php endfor; ?>
	
	</select>
	
	
	<br/><br/>	
	
	<label for="<?php echo $this->get_field_id('resources'); ?>">
		Resource ID pool separated by semicolons: 
	</label>
	<textarea class="widefat" rows="10" id="<?php echo $this->get_field_id('resources'); ?>" name="<?php echo $this->get_field_name('resources'); ?>" type="text"><?php echo attribute_escape($resources); ?></textarea>
	<br/><br/>
	
	<label for="<?php echo $this->get_field_id('hide'); ?>">
		Check to hide this widget on results and preview pages: 
	</label>
	<input class="widefat" <?php echo $hide == 'on' ? 'checked' : ''; ?> id="<?php echo $this->get_field_id('hide'); ?>" name="<?php echo $this->get_field_name('hide'); ?>" type="checkbox" />
	<br/><br/>
</p>
  
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = trim($new_instance['title']);
    $instance['resources'] = trim($new_instance['resources']);
    $instance['total'] = $new_instance['total'];
    $instance['hide'] = $new_instance['hide'];
    return $instance;
  }
  
   function display_rand_resource($arr, $host, $title, $total, $results, $args)
  {
	
	if($total > sizeof($arr) || (sizeof($arr) == 1 && trim($arr[0]) == trim($_GET['lr_resource'])))
		return false;
	
	extract($args, EXTR_SKIP);
	echo $before_widget;
	echo $before_title . $title . $after_title;
	
	$g = 0;
	$totalTimes = 0;
	$save_arr = array();

	while($g < $total){
		
		$totalTimes++;
		if($totalTimes > 20)
			break;
			
		$temp = rand(0, sizeof($arr) - 1);
		if(trim($arr[$temp]) == trim($_GET['lr_resource']) || in_array(trim($arr[$temp]), $save_arr)){
		
			continue;
		}
		
		else{
			
			$save_arr[$g] = trim($arr[$temp]);
			$g++;
		}
	}
	
	?>
	<script type="text/javascript">
		var serviceHost = "<?php echo $host; ?>";
		var NODE_URL = '<?php echo empty($options['node'])?"http://node01.public.learningregistry.net/":$options['node']; ?>';
		var qmarkUrl = '<?php echo plugins_url( "/images/qmark.png" , dirname(__FILE__) ) ?>';
		
		<?php if(empty($_GET['query'])){
			include_once('templates/scripts/applicationPreview.php'); 
		} ?>
		
		$(document).ready(function(){
			console.log(serviceHost + '/data/?keys=' + encodeURIComponent('<?php echo json_encode($save_arr); ?>'));
			$.getJSON(serviceHost + '/data/?keys=' + encodeURIComponent('<?php echo json_encode($save_arr); ?>'),function(data){		
				$.each(data, function(i, data){
					
					console.log(data);
					var src = data.url;
					var md5 = data._id;
					var currentObject = new resourceObject("Item", src);
					var imageUrl = qmarkUrl? qmarkUrl:"/images/qmark.png";
					
					//This is done because observable.valueHasMutated wasn't working..
					currentObject.title = (data.title == undefined) ? doTransform(src) : data.title;
					currentObject.description = (data.description == undefined) ? "" : data.description;
					currentObject.url = (data.url == undefined) ? "" : data.url;
					currentObject.source = data.publisher == undefined ? "" : data.publisher;
					currentObject.image = (data.hasScreenshot !== true) ? imageUrl : serviceHost + "/screenshot/" + md5;
					
					currentObject.image = self.getImageSrc(null, currentObject.image);
					currentObject.hasScreenshot = currentObject.image != imageUrl;				
					
					self.featuredResource.push(currentObject);
					
				});
			});
		});
	</script>
	
	<div data-bind="foreach: featuredResource">
		<div data-bind="attr:{style:$index()>0?'margin: 40px auto 10px auto;' : 'margin: auto auto 10px auto'}">
			<a style="font-size: 16px;" data-bind="text:$root.getShorterStr(title, 40), attr:{href:$root.wordpressLinkTransform('<?php echo add_query_arg(array("lr_resource"=>"LRreplaceMe"), get_page_link( $results));?>',url), title:title}" class="title"></a><br/>
			<span class="childrenResourceNumber" data-bind="text: 'Source: ' + source, visible: $data.source != undefined && $data.source != ''"></span>
		</div>
		<a data-bind="attr:{href:$root.wordpressLinkTransform('<?php echo add_query_arg(array("lr_resource"=>"LRreplaceMe"), get_page_link( $results));?>',url)}" class="title">
			<img alt="Featured Resource" src="" style="border: 1px solid #ddd;padding: 1px 1px;" data-bind="attr:{src:image}" class="img-polaroid" />
		</a>
	</div>
	<?php
	
	return true;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
	
	if($instance['hide'] == 'on' && (! empty($_GET['query']) || ! empty($_GET['lr_resource'])))
		return;
 
	$options = get_option('lr_options_object');
	
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    $resources = empty($instance['resources']) ? array('') : explode(';', $instance['resources']);
    $host  = empty($options['host']) ? "http://12.109.40.31" : $options['host'];
    $total  = empty($instance['total']) ? 1 : $instance['total'];
    $results  = empty($options['results']) ? '' : $options['results'];

    if (!empty($title) && $this->display_rand_resource($resources, $host, $title, $total, $results, $args) != false){
	
	  echo $after_widget;
	}
  }
}