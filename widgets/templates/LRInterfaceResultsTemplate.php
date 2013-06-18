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
										<img src="<?php echo plugins_url( "images/qmark.png" , __FILE__); ?>" data-bind="attr:{alt:title,src:$root.getImageSrc(url, '<?php echo $host; ?>/screenshot/' + _id)}" class="img-polaroid" alt="Resource screenshot" />
										<!-- /ko -->
										<!-- ko if: !hasScreenshot -->
										<img src="<?php echo plugins_url( "images/qmark.png" , __FILE__); ?>" data-bind="attr:{src:$root.getImageSrc(url, '<?php echo plugins_url( 'images/qmark.png' , __FILE__ ) ?>')}" alt="Magnifying glass image" class="img-polaroid" />
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
		var NODE_URL = '<?php echo empty($options['node'])?"http://node01.public.learningregistry.net/":$options['node']; ?>';
	</script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jquery.xml2json.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jquery.eComboBox.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jit-yc.js' , __FILE__ ) ?>"></script>
	
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/lrbrowser.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/paradata.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/utils.js' , __FILE__ ) ?>"></script>
	<link type="text/css" href="<?php echo plugins_url( '/styles/Hypertree.css' , __FILE__ ) ?>" rel="stylesheet" />
	
	<?php endif; ?>
	<?php if($type == "index" || $type == "publisher"): ?>

		<?php if(empty($_GET['standard']) && ! empty($text)): ?>
			<div style="width: 100%; overflow: hidden; clear:both;margin-bottom:30px;">
				<span style="font-size:22px;"><?php echo $text; ?></span>
				<span data-bind="text:resultsCount" style="font-size:13px;"></span>
			</div>
		<?php endif; ?>
		<?php if(!empty($_GET['standard'])): ?>
			<div class="row" style="width: 100%; overflow: hidden; clear:both; border: 1px #d8d8d8 solid; padding: 7px; background:#f7f7f7; margin-bottom:15px;">
				<span style="line-height:19px;" data-bind="text:standardDescription"></span><br/><br/>
				<a class="childrenResourceNumber" href="http://asn.jesandco.org/resources/<?php echo sanitize_lr($_GET['query']); ?>" style="float:right;"><?php echo sanitize_lr($_GET['query']); ?></a>
			</div>
		<?php endif; ?>
		<div class="row">
			<div class="span12 activity">
				<!-- ko if: results().length > 0 -->
				
					<table class="table table-striped resultsTable">
						<tbody data-bind="foreach:results">
							<tr style="border-top:none;">
								<td style="border-top:none;padding-top:15px;padding-bottom:15px;" data-bind="style: { 'border-bottom': $index() < $root.results().length - 1 ? '#ddd dotted 1px' : ''}" class="imageCell">
									<div style="float:left;width: 140px;min-width:140px;text-align:center;">
										<a data-bind="attr:{href:$root.wordpressLinkTransform($root.permalink,url)}">
										<!-- ko if: hasScreenshot -->
										<img src="<?php echo plugins_url( "images/qmark.png" , __FILE__ ); ?>" data-bind="attr:{alt:title, src:$root.getImageSrc(url, '<?php echo $host; ?>/screenshot/' + _id)}" class="img-polaroid" alt="Resource Screenshot" />
										<!-- /ko -->
										<!-- ko if: !hasScreenshot -->
										<img src="<?php echo plugins_url( 'images/qmark.png' , __FILE__ ) ?>" class="img-polaroid" alt="Magnifying glass image" />
										<!-- /ko -->
										</a>
									</div>
									<div style="float:left;text-align:left;width:75%;">
										<div style="color:#888; padding-bottom: 10px; width: 100%; max-width: 100%;">
											<a data-bind="html:$root.getShorterStr($data, 50), attr:{href:$root.wordpressLinkTransform($root.permalink,url), html:title}" class="title"></a><br/>
											<p style="line-height:16px;margin-bottom:0;" data-bind="html: 'Source: ' + publisher, visible: $data.publisher != undefined "></p>
										</div>
										<p data-bind="html:(description.length==0)? '':description.substr(0, 280)+'...'" class="fine"></p>
										<a data-bind="text:$root.getShorterStr(url, 50), attr:{href:url}" class="fine" style="float:right;"></a><br/>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
					<button data-bind="click:loadNewPage, visible:results().length>0" id="loadMore" style="float:right; border: 1px solid #d9d9d9 !important;" class="btn lrSubmit">Show More</button>
				<!-- /ko -->
	
	
				<div id="spinnerDiv"></div>
				<div id="resultsNotFound" class="resultsPrompt" style="margin: 10px auto; width: 30%;" data-bind="visible:resultsNotFound">
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
		
		<?php @include_once('scripts/applicationPreview.php'); ?>
		
		totalSlice = 15;
		newLoad = 15;
		var countReplace = <?php echo json_encode($count); ?>;
		var saveSearchType = '<?php echo $type; ?>';
		
		<?php if(!empty($_GET['standard'])): ?>
		
			self.standardDescription = Base64.decode('<?php echo $_GET['standard']; ?>');
 		<?php endif; ?>

		jQuery(document).ready(function($){
			
			//if not in debug mode
			spinner = new Spinner(opts).spin($('#spinnerDiv')[0]);
			
			$("#endOfResults").hide();
			$('input, textarea').placeholder();
			var cacheJObj = $(".resultModal");
			
			<?php if(!empty($_GET['filter'])): ?>
	
				temp.filterSearchTerms()[1] = '<?php echo $_GET['filter']; ?>';
			<?php endif; ?>
			
			//if regular search
			self.loadNewPage(saveSearchType);
		});
	</script>