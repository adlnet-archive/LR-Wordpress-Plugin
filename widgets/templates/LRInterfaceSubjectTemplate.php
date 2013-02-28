<script type="text/html" id="subject-template">
		<div>
			<!-- ko if: children.length > 0 -->
				<a href="#" data-bind="text: '[ + ] '" class="standard-plus"></a>
				<a href="#" data-bind="text: name" class="standard-link"></a><br/><br/>
				<div class="saveOpen"></div>
				<div style="padding-left: 40px;" data-bind="'template':{'name': 'subject-template', 'foreach': children}, 'attr':{'class':'standard-div standard-' + children.length}"></div>
			<!-- /ko -->
			
			<!-- ko if: children.length == 0 -->
				<a href="#" data-bind="text: name" class="standard-link"></a><br/><br/>
				<div class="noChildren"></div>
			<!-- /ko -->
		</div>
</script>
<div style="clear:both; overflow:hidden; margin: 25px auto; width: 100%;">
	<div id="subject-map-left" style="width: 40%; float: left; padding-left:10%;" data-bind="'template':{'name': 'subject-template', 'foreach': children.slice(0, children.length * .5)}"></div>
	<div id="subject-map-right" style="width: 40%; float: right;" data-bind="'template':{'name': 'subject-template', 'foreach': children.slice(children.length * .5, children.length)}"></div>
</div>
<script type="text/javascript">
	
	
	var serviceHost = "<?php echo $host; ?>";
	var permalink = '<?php echo add_query_arg(array("lr_resource"=>"LRreplaceMe", 'query'=>false)); ?>';
	$(function(){
		$.getJSON(serviceHost + "/data/sitemap", function(data){

			self.children = data.children;
			self.totalRootSections = data.children.length;
			self.currentLevel = 0;
			self.currentCount = 1;
			ko.applyBindings(self, $("#subject-map-left")[0]);
			ko.applyBindings(self, $("#subject-map-right")[0]);


			$(".standard-div").hide();
			$(".standard-link, .standard-plus").click(function(e){
				
				e.preventDefault();
				e.stopPropagation();
				
				//This element has no children.. start search
				if($(this).siblings(".noChildren").length == 1){
				
					window.location.href = '<?php echo add_query_arg("query", "LRreplaceMe", get_page_link( $instance['results']));?>'.replace("LRreplaceMe", $(this).text());
					return false;
				}
				
				var isOpen = $(this).siblings(".saveOpen").data("isOpen");
				if(isOpen == undefined){
					isOpen = true;
					$(this).siblings(".saveOpen").data("isOpen", true);
					$(this).siblings(".standard-div").show();
					
					$(this).parent().children(".standard-plus").text("[ - ] ");
					return;
				}
				else
					$(this).siblings(".saveOpen").data("isOpen", ! isOpen);
				
				if(isOpen){
					$(this).parent().children(".standard-plus").text("[ + ] ");
					$(this).siblings(".standard-div").hide();
				}
				else{
					$(this).parent().children(".standard-plus").text("[ - ] ");
					$(this).siblings(".standard-div").show();
				}
				
				return false;
			});
		});
	});	
</script>