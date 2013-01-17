<?php

class LRInterfaceResults extends WP_Widget
{
  function LRInterfaceResults()
  {
    $widget_ops = array('classname' => 'LRInterfaceResults', 'description' => 'Adds LR search results to a page' );
    $this->WP_Widget('LRInterfaceResults', 'LR Interface Results', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'host' => '', 'type' => '' ) );
    $title = $instance['title'];
	$host = $instance['host'];
	$type = $instance['type'];
?>

<p>
		
		<label for="<?php echo $this->get_field_id('type'); ?>">
			Search Method:
		</label>
		<select class="widefat" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
			<option value="index" <?php echo attribute_escape($type) == "index" ? 'selected="selected"':'""'; ?>>Indexed Search</option>
			<option value="slice" <?php echo attribute_escape($type) == "slice" ? 'selected="selected"':'""'; ?>>Slice</option>
		</select>
		<br/><br/>
		<label>
			WebService Endpoint:
		</label>
		<input class="widefat" id="<?php echo $this->get_field_id('host'); ?>" name="<?php echo $this->get_field_name('host'); ?>" type="text" value="<?php echo (attribute_escape($host))?attribute_escape($host):'http://12.109.40.31'; ?>" />
</p>
  
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['host'] = $new_instance['host'];
    $instance['type'] = $new_instance['type'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
	$type  = empty($instance['type']) ? "index" : $instance['type'];
	$host  = empty($instance['host']) ? "http://12.109.40.31" : $instance['host'];
 
    if (!empty($title))
      echo $before_title . $after_title;;
 
?>
	<div class="container">
	
	<?php if($type == "slice"): ?>
			<div class="searchHeader visualModal">
			<div>
				<span style="display:none;" id="doc_list_header"></span> 
			</div>
		</div>
		<div data-bind="visible: <?php echo "false"; ?>" class="resultParent" style="margin-top:15px;">
			<div class="resultModal span4">
				<table class="table table-striped" style="width:100%;">
					<tbody data-bind="foreach: results">
						<tr>
							<td style="padding-top: 15px; padding-bottom: 15px;">
								<a class="resultClick" style="color: #666;" data-bind="text:$root.getShorterArr(keys, 4), attr:{name: $root.getShorterArr(url), href:'/timeline?query='+url}"></a><br/>
								<a class="resultClick" data-bind="text:$root.getShorterArr(url, 50), attr:{name: $root.getShorterArr(url), href:'/timeline?query='+url}"></a>
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

			<div class="span9 activity">
				<table class="table table-striped resultsTable">
					<tbody data-bind="foreach: getResults()">
								<tr style="border-top:none;" data-bind="style: { 'background-color': $index()%2 == 1 ? '#F9F9F9' : '#FFF'}">
									<td style="border-top:none;padding-top:15px;padding-bottom:15px;" class="imageCell">
										<a data-bind="attr:{href:'/timeline?query='+url}">
											<!-- ko if: hasScreenshot -->
											<img data-bind="attr:{src:'<?php echo $host; ?>/screenshot/' + _id}" class="img-polaroid" />
											<!-- /ko -->
											<!-- ko if: !hasScreenshot -->
											<img src="<?php echo plugins_url( '/images/qmark.png' , __FILE__ ) ?>" class="img-polaroid" />
											<!-- /ko -->
										</a>
									</td>
									<td style="border-top:none;padding-top:15px;padding-bottom:15px;">
										<a data-bind="text:title?title:$root.getShorterArr(keys, 5, true), attr:{href:'/timeline?query='+url, title:title}" class="title"></a><br/>
										<a data-bind="text:url, attr:{href:'/timeline?query='+url}" class="fine"></a><br/>
										<span data-bind="text:(description.length<280)? description:description.substr(0, 280)+'...'" class="fine"></span>
									</td>
								</tr>
					</tbody>
				</table>	
			</div>
			
			<div class="span3" style="padding-top:10px;" data-bind="foreach: relatedResultsNodes">
			
				<a class="relatedList" data-bind="text:(name[0] == undefined)?'':name[0].toUpperCase() + name.substr(1, name.length-1), click:$root.relatedTagSlice"></a>
			</div>
			

		</div>
	<script type="text/javascript">
		var globalSliceMax = 500;
		var NODE_URL = "http://node01.public.learningregistry.net";
		/*{{#server}}
			var temp_NODE_URL = ["","http://node01.public.learningregistry.net","http://node02.public.learningregistry.net", "http://lrtest01.public.learningregistry.net",
								 "http://sandbox.learningregistry.org/", "http://lrdev03.learningregistry.org", "http://lrdev05.learningregistry.org"];
			NODE_URL = temp_NODE_URL[{{server}}];

		{{/server}}*/
	</script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jquery.xml2json.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jquery.eComboBox.js' , __FILE__ ) ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo plugins_url( '/scripts/jit-yc.js' , __FILE__ ) ?>"></script>
	
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/lrbrowser.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/paradata.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/utils.js' , __FILE__ ) ?>"></script>
	<link type="text/css" href="<?php echo plugins_url( '/styles/Hypertree.css' , __FILE__ ) ?>" rel="stylesheet" />
	
	<?php endif; ?>
	<?php if($type == "index"): ?>
		<div class="row">
			<div class="span12 activity">
				<div>
					<span class="searchHeader">Search Results</span>
				</div>
				<!-- ko if: results().length > 0 -->
					<table class="table table-striped resultsTable">
						<tbody data-bind="foreach:results">
							<tr style="border-top:none;" data-bind="style: { 'background-color': $index()%2 == 1 ? '#F9F9F9' : '#FFF'}">
								<td style="border-top:none;padding-top:15px;padding-bottom:15px;" class="imageCell">
									<a data-bind="attr:{href:'/timeline?query='+url}">
										<!-- ko if: hasScreenshot -->
										<img data-bind="attr:{src:'<?php echo $host; ?>/screenshot/' + _id}" class="img-polaroid" />
										<!-- /ko -->
										<!-- ko if: !hasScreenshot -->
										<img src="<?php echo plugins_url( 'images/qmark.png' , __FILE__ ) ?>" class="img-polaroid" />
										<!-- /ko -->
									</a>
								</td>
								<td style="border-top:none;padding-top:15px;padding-bottom:15px;">
									<a data-bind="text:$root.getShorterStr($data), attr:{href:'/timeline?query='+url, title:title}" class="title"></a><br/>
									<a data-bind="text:url, attr:{href:'/timeline?query='+url}" class="fine"></a><br/>
									<span data-bind="text:(description.length==0)? '':description.substr(0, 280)+'...'" class="fine"></span>
								</td>
							</tr>
						</tbody>
					</table>
					<button data-bind="click:loadNewPage" id="loadMore" class="btn">Load More</button>
				<!-- /ko -->
	<?php endif; ?>
	
				<div id="spinnerDiv"></div>
				<div id="resultsNotFound" class="resultsPrompt" data-bind="visible:resultsNotFound">
					<span>Results Not Found</span>
				</div>
				<div id="endOfResults" class="resultsPrompt">
					<span>End of Results</span>
				</div>
			</div>
		</div>
	</div>
	
	
	
	<script type="text/javascript">
		var allOrganizations = [], followed = [], allTerms = [], query = "<?php echo $_GET['query']; ?>";
				var temp = new mainViewModel([]), activeModalName = null, lastSearchCache = "";
		for (var f in followed){
			temp.followers.push({name:followed[f], content:[]});
		}
		
		var handlePerfectSize = function(){};
		var serviceHost = "<?php echo $host; ?>";
		var initialGraphBuild = false;
		
		totalSlice = 15;
		newLoad = 15;
		ko.applyBindings(temp);

		jQuery(document).ready(function($){
			
			//if not in debug mode
			spinner = new Spinner(opts).spin($('#spinnerDiv')[0]);
			
			$("#endOfResults").hide();
			$('input, textarea').placeholder();
			var cacheJObj = $(".resultModal");

			//if regular search
			self.loadNewPage(<?php echo $type == 'slice' ? 'true': ''; ?>);
			

			/*
				var bodyWidth = $("body").css("width");
				
				cacheJObj.mouseenter(function(){
						
						$("html").add("body").addClass("overflowHidden");
						$("body").css("width", bodyWidth);
				});
				cacheJObj.mouseleave(function(){
						
						$("html").add("body").removeClass("overflowHidden");
				});
				cacheJObj.niceScroll({"cursoropacitymax": 0.7, "cursorborderradius": 0} );
				
				{{#server}}
					$(".warningParagraph p").append(" ("+NODE_URL+")");
				{{/server}}
			*/	
		});
	</script>
	
	
<?php
    echo $after_widget;
  }
 
}