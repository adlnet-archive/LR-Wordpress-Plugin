<script type="text/html" id="subject-template">
		<div>
			<!-- ko if: children.length > 0 -->

				<a href="#" data-bind="text: '[ + ] '" class="standard-plus"></a>
				<span class="levelTracker" style="visibility:invisible" data-bind="attr:{name:$root.levelTracker}"></span>
				<a href="#" data-bind="text: name, attr:{name:$data.name}" class="standard-link"></a><br/><br/>
				<div class="saveOpen"></div>
				<!-- ko if: $root.levelTracker.push(0) --><!-- /ko -->
				<div style="padding-left: 40px;" data-bind="'template':{'name': 'subject-template', 'foreach': children}, 'attr':{'class':'standard-div standard-' + children.length}"></div>
				<!-- ko if: $root.levelTracker.pop() --><!-- /ko -->
			<!-- /ko -->
			
			<!-- ko if: children.length == 0 -->
				<span class="levelTracker" style="visibility:invisible" data-bind="attr:{name:$root.levelTracker}"></span>
				<a href="#" data-bind="text: name, attr:{name:$data.name}" class="standard-link"></a><br/><br/>
				<div class="noChildren"></div>
			<!-- /ko -->
			
			<!-- ko if: $root.levelTracker[$root.levelTracker.length-1]++ --><!-- /ko -->
		</div>
</script>
<div id="subjectMapContainer" style="clear:both; overflow:hidden; margin: 0 auto; width: 100%;">
	<div id="subject-map-left" style="width: 40%; float: left; padding-left:10%;" data-bind="'template':{'name': 'subject-template', 'foreach': children.slice(0, children.length * .5)}"></div>
	<div id="subject-map-right" style="width: 40%; float: right;" data-bind="'template':{'name': 'subject-template', 'foreach': children.slice(children.length * .5, children.length)}"></div>
</div>
<div style="clear:both; overflow:hidden; margin-bottom: 20px; width: 100%;"></div>
<script type="text/javascript">
	
	
	var serviceHost = "<?php echo $host; ?>";
	var permalink = '<?php echo add_query_arg(array("lr_resource"=>"LRreplaceMe", 'query'=>false)); ?>';
	<?php if(empty($_GET['query']) && empty($_GET['lr_interface'])){
			@include_once('scripts/applicationPreview.php'); 
	} ?>
	
	$(function(){
		$.getJSON(serviceHost + "/data/sitemap", function(data){
			lrConsole("Data: ", data);
			self.children = data.children;
			ko.applyBindings(self, $("#subject-map-left")[0]);
			ko.applyBindings(self, $("#subject-map-right")[0]);

			$("#subjectMapContainer .standard-div").hide();
			$("#subjectMapContainer .standard-link").each(function(){
				
				$(this).attr('href', '<?php echo add_query_arg(array('query'=>'LRreplaceMe', 'subject'=>'LRsubjectReplace'), get_page_link( $options['results']));?>'.replace("LRreplaceMe", encodeURIComponent($(this).text())).replace("LRsubjectReplace", encodeURIComponent($(this).siblings(".levelTracker").attr('name'))));
				
			});
		});
	});	
</script>