<script type="text/html" id="standards-template">
		<div>
			<!-- ko if: $data && $data.children !== undefined -->
				<a href="#" data-bind="text: '[ + ] '" class="standard-plus"></a>
				<a href="#" data-bind="html: title[0].toUpperCase() + title.slice(1, title.length), attr:{name:$data.id!=undefined?$data.id:title}" class="standard-link"></a>
				<span class="childrenResourceNumber" data-bind="text: $data.count >= 0? '( ' + $data.count + ' )': ''"></span>
				<br/><br/>
				<div class="saveOpen"></div>
				<div style="padding-left: 40px;" data-bind="'template':{'name': 'standards-template', 'foreach': children}, 'attr':{'class':'standard-div standard-' + $data.children.length}"></div>
			<!-- /ko -->
			
			<!-- ko if: $data && $data.children == undefined -->
				<a style="text-decoration:none;" href="#" data-bind="html: title, attr:{name: id}" class="standard-link"></a>
				<br/><br/>
				<div class="noChildren"></div>
			<!-- /ko -->
		</div>
</script>
<div id="standardsMapContainer" class="loading" style="clear:both; overflow:hidden; margin: 0 auto; width: 100%;">
	<div id="standards-map-left" style="width: 90%; float: left; padding-left:10%;" data-bind="'template':{'name': 'standards-template', 'data': standards[0]}"></div>
	<div id="standards-map-right" style="width: 90%; float: left; padding-left:10%;" data-bind="'template':{'name': 'standards-template', 'data': standards[1]}"></div>
</div>

	<script type="text/javascript">
		
		
		var serviceHost = "<?php echo $host; ?>";
		var permalink = '<?php echo add_query_arg(array("lr_resource"=>"LRreplaceMe", 'query'=>false)); ?>';
		var qmarkUrl = '<?php echo plugins_url( 'templates/images/qmark.png' , __FILE__ ) ?>';

		<?php if(empty($_GET['query']) && empty($_GET['lr_interface'])){
			@include_once('scripts/applicationPreview.php'); 
		} ?>
		
		$(document).ready(function(){
			
			spinner.spin($("#standardsMapContainer")[0]);
			
			$.getJSON(serviceHost + "/standards", function(data){
				
				$("#standardsMapContainer").removeClass("loading");
				spinner.stop();
				
				self.standards = data;
				
				console.log(data, self);
				ko.applyBindings(self, $("#standards-map-left")[0]);
				ko.applyBindings(self, $("#standards-map-right")[0]);
				$("#standardsMapContainer .standard-div").hide();
				
				
				$("#standardsMapContainer .standard-link").click(function(e){
			
					e.preventDefault();
					e.stopPropagation();
					
					window.location.href = 
					'<?php echo add_query_arg("query", "LRreplaceMe", get_page_link( $options['results']));?>'.replace("LRreplaceMe", encodeURIComponent($(this).attr("name")));
						return false;
				});
				
			});
		});
	</script>