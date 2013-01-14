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
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'host' => '' ) );
    $title = $instance['title'];
	$host = $instance['host'];
?>

<p>
	<label for="<?php echo $this->get_field_id('title'); ?>">
		Title: 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /><br/><br/>
		
		WebService Endpoint:
		<input class="widefat" id="<?php echo $this->get_field_id('host'); ?>" name="<?php echo $this->get_field_name('host'); ?>" type="text" value="<?php echo (attribute_escape($host))?attribute_escape($host):'http://12.109.40.31'; ?>" />
	</label>
</p>
  
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['host'] = $new_instance['host'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $after_title;;
 
?>
	
	 <div class="container">
		<div class="row">
			<div class="span12 activity">
				<div>
					<span class="searchHeader">Search Results</span>
					<form method="get" action="/find" id="searchinput" class="input-append" style="float:right;">
						 <input class="input-xxlarge" id="term" name="query" type="text" placeholder="Start New Search (current: {{query}})"/>
						 <button class="btn btn-primary"id="Search" style="margin-top:0;" type="submit">Search</button>
					</form>
				</div>
				<!-- ko if: results().length > 0 -->
					<table class="table table-striped resultsTable">
						<tbody data-bind="foreach:results">
							<tr style="border-top:none;" data-bind="style: { 'background-color': $index()%2 == 1 ? '#F9F9F9' : '#FFF'}">
								<td style="border-top:none;padding-top:15px;padding-bottom:15px;" class="imageCell">
									<a data-bind="attr:{href:'/timeline?query='+url}">
										<!-- ko if: hasScreenshot -->
										<img data-bind="attr:{src:'<?php echo $instance['host']; ?>/screenshot/' + _id}" class="img-polaroid" />
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
					<a data-bind="click:loadNewPage" id="loadMore" class="btn">Load More</a>
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

 </div>
	
	
	
	<script type="text/javascript">
		var allOrganizations = [], followed = [], allTerms = [], query = "<?php echo $_GET['query']; ?>";
				var temp = new mainViewModel([]), activeModalName = null, lastSearchCache = "";
		for (var f in followed){
			temp.followers.push({name:followed[f], content:[]});
		}
		
		var handlePerfectSize = function(){};
		var serviceHost = "<?php echo $instance['host']; ?>";
		
		totalSlice = 15;
		newLoad = 15;
		ko.applyBindings(temp);

		jQuery(document).ready(function($){
			
			$("#endOfResults").hide();
			spinner = new Spinner(opts).spin($('#spinnerDiv')[0]);
			$('input, textarea').placeholder();

			self.loadNewPage();
		});
	</script>
	
	
<?php
    echo $after_widget;
  }
 
}