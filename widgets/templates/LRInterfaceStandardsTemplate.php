	<div class="result"></div>
	
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/crumbs.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jquery.cookie.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/browser.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript">
		
		
		var serviceHost = "<?php echo $host; ?>";
		var permalink = '<?php echo add_query_arg(array("lr_resource"=>"LRreplaceMe", 'query'=>false)); ?>';
		var qmarkUrl = '<?php echo plugins_url( '/images/qmark.png' , __FILE__ ) ?>';
		
		$(document).ready(function(){
			$.get(serviceHost + '/browser/?ajax', function(data) {
			  $('.result').html(data);
			});
		});
	</script>