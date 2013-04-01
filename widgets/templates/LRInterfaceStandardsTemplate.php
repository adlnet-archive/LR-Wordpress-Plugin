<script type="text/html" id="standards-template">
		<div>
			<!-- ko if: $data && $data.children !== undefined -->
				<a href="#" data-bind="text: '[ + ] '" class="standard-plus"></a>
				<a href="#" data-bind="html: title[0].toUpperCase() + title.slice(1, title.length), attr:{name:$data.id!=undefined?$data.id:title}" class="standard-link-collapse" style="line-height:21px;"></a>
				<span class="childrenResourceNumber" data-bind="text: $data.count >= 0? '( ' + $data.count + ' )': ''"></span>
				<br/><br/>
				<div class="saveOpen"></div>
				<div style="padding-left: 40px;" data-bind="'template':{'name': 'standards-template', 'foreach': children}, 'attr':{'class':'standard-div standard-' + $data.children.length}"></div>
			<!-- /ko -->
			
			<!-- ko if: $data && $data.children == undefined -->
				<div style="border: 1px #d8d8d8 solid; padding: 7px; background:#f7f7f7;" >
					<a style="text-decoration:none;" href="#" data-bind="html: title, click:$root.handleStandardsClick" class="standard-link"></a>
					<span class="childrenResourceNumber" data-bind="text: $data.count >= 0? '( ' + $data.count + ' )': ''"></span>
					
					<div class="noChildren"></div>
				</div>
					<br/><br/>
			<!-- /ko -->
		</div>
</script>

<div style="border-bottom:1px #ddd solid; width:100%;overflow:hidden;">
	<div class="standardHeader" data-bind="click:handleStandardHeaderClick">Common Core</div>
	<div class="standardHeader standardHeaderInactive" data-bind="click:handleStandardHeaderClick">State</div>
</div>
<div class="allStates">
	<div id="standardsMapContainer" class="loading" style="clear:both; overflow:hidden; margin: 0 auto; width: 100%;" data-bind="foreach:standards().children">
		<div id="standards-map-left" style="width: 90%; float: left; padding-left:10%;" data-bind="'template':{'name': 'standards-template', 'data': $data}"></div>
	</div>
	<div class="stateList" data-bind="foreach:listOfStates.slice(0, 18)" style="float:left;">
		<div class="individualState">
			<span data-bind="visible:$data"><a data-bind="text:$data, attr:{href:'/'}, click:$root.handleSubCategoryClick"></a></span>
		</div>
	</div>
	<div class="stateList" data-bind="foreach:listOfStates.slice(18, 35)" style="float:left;height:200px;">
		<div class="individualState">
			<span data-bind="visible:$data"><a data-bind="text:$data, attr:{href:'/'}, click:$root.handleSubCategoryClick"></a></span>
		</div>
	</div>
	<div class="stateList" data-bind="foreach:listOfStates.slice(35, 52)" style="float:left;height:200px;">
		<div class="individualState">
			<span data-bind="visible:$data"><a data-bind="text:$data, attr:{href:'/'}, click:$root.handleSubCategoryClick"></a></span>
		</div>
	</div>
	
</div>


	<script type="text/javascript">
		

		var serviceHost = "<?php echo $host; ?>";
		var permalink = '<?php echo add_query_arg(array("lr_resource"=>"LRreplaceMe", 'query'=>false)); ?>';
		var qmarkUrl = '<?php echo plugins_url( 'templates/images/qmark.png' , __FILE__ ) ?>';
		var saveThisStateNow = false;
		<?php if(empty($_GET['query']) && empty($_GET['lr_interface'])){
			@include_once('scripts/applicationPreview.php'); 
		} ?>
		
		$(document).ready(function(){
			$("#standardsMapContainer").hide();
			$.getJSON(serviceHost + "/new/standards", function(data){
				
				var remove = ['Common', 'english', 'math'];
				for(var i = 0; i < data.length; i++){
				
					if($.inArray(data[i], remove) > -1){
						
						delete data[i];
					}
				}
				
				self.listOfStates(data);
				console.log("STANDARDS ", data);
				
			});
			
			

		});
	</script>