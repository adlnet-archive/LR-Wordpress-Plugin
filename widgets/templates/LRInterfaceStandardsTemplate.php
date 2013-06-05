<script type="text/html" id="standards-template">
		<div class="standardsTree">
			<!-- ko if: $data && $data.children !== undefined -->

				<a href="#" data-bind="click:$data.loadChildren" class="standard-plus">&#9654;</a>
				<a href="#" data-bind="'attr':{'name':$data.title?$data.title():'', href:'#s'+parentRoute}, 'click':$data.loadChildren, 'html': title().charAt(0).toUpperCase() + title().slice(1, title().length)" class="standard-link-collapse" style="line-height:21px;">&nbsp;</a>
				<span class="childrenResourceNumber" data-bind="text: $data.childCount() >= 0? '( ' + $data.childCount() + ' )': ''">&nbsp;</span>
				<a href="#" title="Search for this standard directly" data-bind="click:$root.handleStandardsNodeClick, visible:$data.count(), attr:{href:'#s'+parentRoute}" style="text-decoration:none;">
					<img src="<?php echo plugins_url( 'images/glass.png' , __FILE__ ) ?>" style="box-shadow:none;" />
				</a>
				<br/><br/>
				<div class="saveOpen"></div>
				
				<div style="padding-left: 40px;" class="standard-div" data-bind="'template':{'name': 'standards-template', 'foreach': children}"></div>
			<!-- /ko -->
			<!-- ko if: $data && $data.children == undefined -->				
				<div style="border: 1px #d8d8d8 solid; padding: 7px; background:#f7f7f7;" >
					<a style="text-decoration:none;" href="#" data-bind="'html': title(), 'attr':{href:'#s'+parentRoute}, 'click':$root.handleStandardsClick" class="standard-link">&nbsp;</a>
					<span class="childrenResourceNumber" data-bind="'text': $data.count() >= 0? '( ' + $data.count() + ' )': ''">&nbsp;</span>
					
					<div class="noChildren"></div>
				</div>
					<br/><br/>
			<!-- /ko -->
			
		</div>
</script>

<div style="width:100%; overflow:hidden;clear:both;float:left;margin-bottom:20px;">
	<div class="standardsContainer" data-bind="foreach:['Multistate', 'State']">
		<div class="standardHeader" data-bind="click:$root.handleStandardHeaderClick, text:$data, css:{standardHeaderInactive:$index()>0}"></div>
		<div style="float:right; color: red;padding:16px 10px 0 0;" data-bind="visible:$index()==1">Beta</div>
	</div>
	<div class="allStates">
		<div id="standardsMapContainer" style="clear:both; overflow:hidden; margin: 0 auto; width: 100%;" data-bind="foreach:standards().children">
			<div id="standards-map-left" style="width: 90%; float: left; padding-left:10%;" data-bind="'template':{'name': 'standards-template', 'data': $data}"></div>
		</div>
		<div class="stateList" data-bind="foreach:listOfStates.slice(0, 17)" style="float:left;">
			<div class="individualState">
				<span data-bind="visible:$data"><a data-bind="text:$data, attr:{href:'/'}, click:$root.handleSubCategoryClick"></a></span>
			</div>
		</div>
		<div class="stateList" data-bind="foreach:listOfStates.slice(17, 34)" style="float:left;height:200px;">
			<div class="individualState">
				<span data-bind="visible:$data"><a data-bind="text:$data, attr:{href:'/'}, click:$root.handleSubCategoryClick"></a></span>
			</div>
		</div>
		<div class="stateList" data-bind="foreach:listOfStates.slice(34, 52)" style="float:left;height:200px;">
			<div class="individualState">
				<span data-bind="visible:$data"><a data-bind="text:$data, attr:{href:'/'}, click:$root.handleSubCategoryClick"></a></span>
			</div>
		</div>
		
	</div>
</div>

<script type="text/javascript">
	var serviceHost = "<?php echo $host; ?>";
	var permalink = '<?php echo add_query_arg(array("lr_resource"=>"LRreplaceMe", 'query'=>false)); ?>';
	var qmarkUrl = '<?php echo plugins_url( 'images/qmark.png' , __FILE__ ) ?>';
	var saveThisStateNow = false;
	<?php if(empty($_GET['query']) && empty($_GET['lr_interface'])){
		@include_once('scripts/applicationPreview.php'); 
	} ?>
	
	window.onhashchange = function(e){
		standardCollapseAllAndOpen(e);
	};
	
	$(document).ready(function(){
		
		$.ajaxSetup({ cache:true });

		spinner.spin($(".allStates")[0]);	
		$('.stateList').hide();
		var url = window.location.pathname + "?json=standards.standards&standard=Common";
		$.getJSON(url, function(data){
			data = data.data;
			
			saveStandardsData = new self.model(data);
			self.standards(saveStandardsData);
			self.standards().loadChildren();
			
			console.log("DATA FORMAT: ", saveStandardsData);
			spinner.stop();
			standardCollapseAllAndOpen();
		});			

		self.listOfStates(["AK","AL","AR","AZ","CA","CO","CT","DC","DE","FL","GA","HI","IA","ID","IL","IN","KS","KY","LA","MA","MD","ME","MI","MN","MO","MS","MT","NC","ND","NE","NH","NJ","NM","NV","NY","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT","VA","VT","WA","WI","WV","WY"]);

	});
</script>