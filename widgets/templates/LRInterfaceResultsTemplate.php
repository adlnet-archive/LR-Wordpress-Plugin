	<div class="container">
	
	<?php if($type == "slice"): ?>
		<div class="searchHeader visualModal">
			<div>
				<span style="display:none;" id="doc_list_header"></span> 
			</div>
		</div>
		<div data-bind="visible: <?php echo "false"; //This would be enabled on "debug mode" ?>" class="resultParent" style="margin-top:15px;">
			<div class="resultModal span4">
				<table class="table table-striped" style="width:100%;">
					<tbody data-bind="foreach: results">
						<tr>
							<td style="padding-top: 15px; padding-bottom: 15px;">
								<a class="resultClick" style="color: #666;" data-bind="text:$root.getShorterArr(keys, 4), 
								attr:{name: $root.getShorterArr(url), href:'<?php echo the_permalink(); ?>?query='+url}"></a><br/>
								<a class="resultClick" data-bind="text:$root.getShorterArr(url, 50), 
								attr:{name: $root.getShorterArr(url), href:'<?php echo the_permalink(); ?>?query='+url}"></a>
							</td>
						</tr>
					</tbody>
				</table>


			</div>
			<div class="infovizModal span8">

				<div id="infovis"></div>

				<div class="text" >
					<div style="text-align: center;">
						<div id="progressbar" align="center"></div>
					</div>
					<div id="status">
						...
					</div>
				</div>
				<br>
				<div class="text">
					<div id="results_summary"></div>
				</div>
				<br>
				<div id = "debugDiv" class="text">
					<div id="debug">
						...
					</div>
				</div>

			</div>
		</div>
		<div class="row" data-bind="visible:results().length > 0">
			<div class="span12 activity">
				<table class="table table-striped resultsTable">
					<tbody data-bind="foreach: getResults()">
						<tr style="border-top:none;">
							<td style="border-top:none;padding-top:15px;padding-bottom:15px;" data-bind="style: { 'border-bottom': $index() < $root.results().length - 1 ? '#ddd dotted 1px' : ''}" class="imageCell">
								<div>
									<a data-bind="attr:{href:$root.wordpressLinkTransform($root.permalink,url)}">
										<!-- ko if: hasScreenshot -->
										<img data-bind="attr:{src:$root.getImageSrc(url, '<?php echo $host; ?>/screenshot/' + _id)}" class="img-polaroid" />
										<!-- /ko -->
										<!-- ko if: !hasScreenshot -->
										<img data-bind="attr:{src:$root.getImageSrc(url, '<?php echo plugins_url( 'images/qmark.png' , __FILE__ ) ?>')}" class="img-polaroid" />
										<!-- /ko -->
									</a>
								</div>
								<div style="float:right;text-align:left;width:75%;">
									<a data-bind="text:title?$root.getShorterStr(title, 50):$root.getShorterArr(keys, 5, true), 
									attr:{href:$root.wordpressLinkTransform($root.permalink,url), title:title}" class="title"></a><br/>
									<a data-bind="text:$root.getShorterStr(url, 50), attr:{href:$root.wordpressLinkTransform($root.permalink,url)}" class="fine"></a><br/>
									<span data-bind="text:(description.length<280)? description:description.substr(0, 280)+'...'" class="fine"></span>
								</div>
							</td>
						</tr>
					</tbody>
				</table>	
			</div>
		</div>				
		<div id="spinnerDiv" style="height: 25px;"></div>
		<div id="resultsNotFound" class="resultsPrompt" data-bind="visible:resultsNotFound">
			<span>Results Not Found</span>
		</div>
		<div id="endOfResults" class="resultsPrompt">
			<span>End of Results</span>
		</div>
		
	<script type="text/javascript">
		var globalSliceMax = 500;
		var NODE_URL = "http://node01.public.learningregistry.net";
	</script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jquery.xml2json.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jquery.eComboBox.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jit-yc.js' , __FILE__ ) ?>"></script>
	
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/lrbrowser.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/paradata.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/utils.js' , __FILE__ ) ?>"></script>
	<link type="text/css" href="<?php echo plugins_url( '/styles/Hypertree.css' , __FILE__ ) ?>" rel="stylesheet" />
	
	<?php endif; ?>
	<?php if($type == "index"): ?>
		<div class="row" style="width: 100%; overflow: hidden; clear:both;height:80px;"><h2><?php echo $text; ?></h2></div>
		<div class="row">
			<div class="span12 activity">
				<!-- ko if: results().length > 0 -->
				
					<table class="table table-striped resultsTable">
						<tbody data-bind="foreach:results">
							<tr style="border-top:none;">
								<td style="border-top:none;padding-top:15px;padding-bottom:15px;" data-bind="style: { 'border-bottom': $index() < $root.results().length - 1 ? '#ddd dotted 1px' : ''}" class="imageCell">
									<div style="float:left">
										<a data-bind="attr:{href:$root.wordpressLinkTransform($root.permalink,url)}">
										<!-- ko if: hasScreenshot -->
										<img data-bind="attr:{src:$root.getImageSrc(url, '<?php echo $host; ?>/screenshot/' + _id)}" class="img-polaroid" />
										<!-- /ko -->
										<!-- ko if: !hasScreenshot -->
										<img src="<?php echo plugins_url( 'images/qmark.png' , __FILE__ ) ?>" class="img-polaroid" />
										<!-- /ko -->
										</a>
									</div>
									<div style="float:right;text-align:left;width:75%;">
										<a data-bind="text:$root.getShorterStr($data, 50), attr:{href:$root.wordpressLinkTransform($root.permalink,url), title:title}" class="title"></a><br/>
										<a data-bind="text:$root.getShorterStr(url, 50), attr:{href:$root.wordpressLinkTransform($root.permalink,url)}" class="fine"></a><br/>
										<span data-bind="text:(description.length==0)? '':description.substr(0, 280)+'...'" class="fine"></span>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
					<button data-bind="click:loadNewPage" id="loadMore" class="btn">Load More</button>
				<!-- /ko -->
	
	
				<div id="spinnerDiv"></div>
				<div id="resultsNotFound" class="resultsPrompt" data-bind="visible:resultsNotFound">
					<span>Results Not Found</span>
				</div>
				<div id="endOfResults" class="resultsPrompt">
					<span>End of Results</span>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
	
	
	
	<script type="text/javascript">
		var allOrganizations = [], followed = [], allTerms = [], query = "<?php echo sanitize_lr($_GET['query'], ' '); ?>";
				var temp = new mainViewModel([]), activeModalName = null, lastSearchCache = "";
		for (var f in followed){
			temp.followers.push({name:followed[f], content:[]});
		}
		
		var handlePerfectSize = function(){};
		var serviceHost = "<?php echo $host; ?>";
		var initialGraphBuild = false;
		temp.permalink = '<?php echo add_query_arg(array("lr_resource"=>"LRreplaceMe", 'query'=>false)); ?>';
		
		totalSlice = 15;
		newLoad = 15;

		jQuery(document).ready(function($){
			
			//if not in debug mode
			ko.applyBindings(temp);
			spinner = new Spinner(opts).spin($('#spinnerDiv')[0]);
			
			$("#endOfResults").hide();
			$('input, textarea').placeholder();
			var cacheJObj = $(".resultModal");

			//if regular search
			self.loadNewPage(<?php echo $type == 'slice' ? 'true': ''; ?>);
		});
	</script>