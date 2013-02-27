<div class="result"></div>
<script type="text/html" id="subject-template">
	<span>
		
		<!-- ko if: children.length > 0 -->
			<a href="#" data-bind="text: '[ + ] '" class="standard-plus" />
			<a href="#" data-bind="text: name" class="standard-link" /><br/><br/>
			<div class="saveOpen"></div>
			<div style="padding-left: 50px;" data-bind="template:{name: 'subject-template', foreach: children}, attr:{class:'standard-div standard-' + children.length }"/>
		<!-- /ko -->
		
		<!-- ko if: children.length == 0 -->
			<a href="#" data-bind="text: name" class="standard-link" />	<br/><br/>
			<div class="noChildren"></div>
		<!-- /ko -->
		
		
	</span>
</script>

<span data-bind="template:{name: 'subject-template', foreach: children}"/>
<script type="text/javascript">
	
	
	var serviceHost = "<?php echo $host; ?>";
	var permalink = '<?php echo add_query_arg(array("lr_resource"=>"LRreplaceMe", 'query'=>false)); ?>';
	$(function(){
		$.getJSON(serviceHost + "/data/sitemap", function(data){
			console.log(data);
			ko.applyBindings(data);

			

			

			$(".standard-div").hide();
			$(".standard-link, .standard-plus").click(function(){
				
				//This element has no children.. start search
				if($(this).siblings(".noChildren").length == 1){
				
					window.location.href = '<?php echo add_query_arg("query", "LRreplaceMe", get_page_link( $instance['results']));?>'.replace("LRreplaceMe", $(this).text());
					return;
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
			});
		});
	});	
</script>