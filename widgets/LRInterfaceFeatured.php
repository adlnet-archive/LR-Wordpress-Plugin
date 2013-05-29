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
	wp_enqueue_script( 'knockout', 'https://ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.0.js', false );
	wp_enqueue_style( 'lrinterface', plugins_url( "styles/application.css" , __DIR__ ));
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'resources' => '', 'total' => '', 'hide' => '') );
    $title = $instance['title'];
    $resources = $instance['resources'];
    $total = $instance['total'];
    $hide = $instance['hide'];
	
	$options = get_option('lr_options_object');
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
		Random selection pool: 
	</label>
	<div style="margin: 10px auto 20px auto;border: 1px #ebebeb solid;padding:5px;">	
		<input data-bind="value:strValue" value="<?php echo attribute_escape($resources); ?>" id="<?php echo $this->get_field_id('resources'); ?>" name="<?php echo $this->get_field_name('resources'); ?>" type="hidden" />
		
		<div data-bind="foreach:resources">
			<div style="margin-bottom:10px;">
				<a data-bind="text: $data.title.length>25?$data.title.substr(0, 25)+'...':$data.title.substr(0, 25), attr:{href:$root.baseUrl.replace('LRreplaceMe', $data.resource())}" 
				target="_blank"></a>
				
				<input type="text" data-bind="value:$data.resource, visible:!existing, valueUpdate:'afterkeydown'" placeholder="Enter resource ID or URL" style="width:80%;" />
				<a class="LRxButton" style="float:right;" data-bind="click: $root.deleteResource" >X</a>
			</div>
		</div>
		
		<div style="text-align:right;">
			<a href="" data-bind="click: $root.addResource" >Add New</a>
		</div>
	</div>	

	<label for="<?php echo $this->get_field_id('hide'); ?>">
		Check to hide this widget on results and preview pages: 
	</label>
	<input class="widefat" <?php echo $hide == 'on' ? 'checked' : ''; ?> id="<?php echo $this->get_field_id('hide'); ?>" name="<?php echo $this->get_field_name('hide'); ?>" type="checkbox" style="background:none;border:none;"/>
	<br/><br/>

</p>
<script type="text/javascript">
	var resourcesModel, allResources = '<?php echo $resources; ?>'.split(';'), temp = [];
	
	//Not sure why Wordpress is loading this widget's script more than once
	//Second time being included seems to work more consistently
	var subsequentLoad = subsequentLoad ? true : false;
	if( subsequentLoad){
		jQuery(document).ready(function(){

			resourcesModel = new function(){
				var self = this;
				
				self.strValue = ko.observable('');
				self.baseUrl = '<?php echo add_query_arg("lr_resource", "LRreplaceMe", get_page_link( $options['results']));?>';
				self.saveRegex = new RegExp(/[^0-9a-zA-Z]/g);
				
				self.testForURL = function(str){
					//This string does not contain characters that are NOT alphanumeric, okay to return
					if(self.saveRegex.test(str) === false)
						return str;
						
					return str.split("lr_resource=")[1] || '';
				};
				
				self.handleValue = function(){
						
					var str = '';				
					if(!self.resources) return '';
					
					var l = self.resources().length;
					var temp = '';
					for(var i = 0; i < l; i++){
						temp = self.testForURL(self.resources()[i].resource());
						if(temp.length == 0)
							continue;
							
						str +=  (str.length > 0 && i > 0)? ';' + temp : temp;
					}

					self.strValue(str);
					return self.strValue();
				};
				
				self.addResource = function(){
					self.resources.push({resource:ko.observable(''), title:'', existing: false});
					self.resources().slice(-1)[0].resource.subscribe(self.handleValue);
				};			
				self.deleteResource = function(e){
					//Possible KO memory leak due to not disposing of subscription?
					self.resources.remove(e);
				};
			};

			jQuery.getJSON('<?php echo home_url(); ?>?json=data.get_data_items&keys='+encodeURIComponent(JSON.stringify(allResources)), function(data){
				
				data = data.data;
				for(var i = 0; i < allResources.length; i++){
					if(allResources[i] && data[i])
						temp[i] = {resource: ko.observable(allResources[i]), title: (allResources[i] == data[i]._id) ? data[i].title : '', existing:true};
				}
				
				resourcesModel.resources = ko.observableArray(temp);
				resourcesModel.resources.subscribe(resourcesModel.handleValue);
				resourcesModel.handleValue();
				
				console.log(temp);
				
				ko.applyBindings(resourcesModel);
			});
		});
	}
	
	subsequentLoad = true;
</script>
  
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
            var url = window.location.pathname + "?json=data.get_data_items&keys=" + encodeURIComponent('<?php echo json_encode($save_arr); ?>');
			$.getJSON(url,function(d){		
				lrConsole(d);
				$.each(d.data, function(i, data){
					
					
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
		<a style="text-decoration:none !important;" data-bind="attr:{href:$root.wordpressLinkTransform('<?php echo add_query_arg(array("lr_resource"=>"LRreplaceMe"), get_page_link( $results));?>',url)}" class="title">
			<img alt="title" src="<?php echo plugins_url( "images/qmark.png" , dirname(__FILE__) ); ?>" style="border: 1px solid #ddd;padding: 1px 1px;" data-bind="attr:{src:image, alt:title}" class="img-polaroid" />
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