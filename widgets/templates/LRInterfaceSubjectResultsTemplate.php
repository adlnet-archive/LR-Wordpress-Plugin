<div class="lr_free_images" id="lr_featured_results_helper" data-bind="foreach:featuredResultsHelper">
	<div>
		<a data-bind="attr:{href:caption}">
			<span style="padding-bottom: 5px;display:block;" data-bind="text:title"></span>
			<img data-bind="attr:{src:url}" />
		</a>
	</div>
</div>


<script type="text/javascript">
	
		<?php @include_once('scripts/applicationPreview.php'); ?>
		
		$(document).ready(function(){
			var ids = '<?php echo $instance['ids']; ?>' ? JSON.parse('<?php echo $instance['ids']; ?>') : '';
			var indexLocation = [<?php echo $_GET['subject']; ?>];
			
			$.getJSON(serviceHost + "/data/sitemap", function(data){
			
				if(data.children == undefined)
					return;
				
				var names = [];
				for(var i = 0; i < indexLocation.length; i++){
					data = data.children[indexLocation[i]];
				}

				for(var i = 0; i < data.children.length; i++){
					names.push(data.children[i].name.toLowerCase());
				}

				for(var i = 0; i < ids.length; i++){
				
					if($.inArray(ids[i].title.toLowerCase(), names) >= 0){
						
						ids[i].caption = '<?php echo add_query_arg("query", "LRreplaceMe", get_page_link( $options['results']));?>'.replace("LRreplaceMe", encodeURIComponent(ids[i].title));
						temp.featuredResultsHelper.push(ids[i]);
					}
				}
		
				
				
				console.log(temp.featuredResultsHelper(), names);
			});
			
		});
</script>