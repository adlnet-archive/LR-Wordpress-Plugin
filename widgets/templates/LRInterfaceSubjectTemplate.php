<div class="result"></div>
<script type="text/html" id="subject-template">
	<span>
		<a href="#" data-bind="text: name" class="standard-link"/>			
		<div style="padding-left: 10px;" data-bind="template:{name: 'subject-template', foreach: children}"/>
	</span>
</script>
<script type="text/javascript" src="<?php echo plugins_url( '/scripts/crumbs.js' , __FILE__ ) ?>"></script>
<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jquery.cookie.js' , __FILE__ ) ?>"></script>
<script type="text/javascript" src="<?php echo plugins_url( '/scripts/subjects.js' , __FILE__ ) ?>"></script>
<script type="text/javascript">
	
	
	var serviceHost = "<?php echo $host; ?>";
	var permalink = '<?php echo add_query_arg(array("lr_resource"=>"LRreplaceMe", 'query'=>false)); ?>';
	$(function(){
		$.getJSON(serviceHost + "/data/sitemap", function(data){
			ko.applyBindings(data);					
		});
	});	
</script>