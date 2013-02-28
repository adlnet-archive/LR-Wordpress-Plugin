<?php

class LRInterfaceSearch extends WP_Widget
{
  function LRInterfaceSearch()
  {
    $widget_ops = array('classname' => 'LRInterfaceSearch', 'description' => 'Adds an LR search bar to a page' );
    $this->WP_Widget('LRInterfaceSearch', 'LR Interface Search Bar', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'results' => '', 'placeholder'=>'', 'type' => '', 'hide' => '' ) );
    $title = $instance['title'];
	$results = $instance['results'];
	$placeholder = $instance['placeholder'];
	$type = $instance['type'];
	$hide = $instance['hide'];
?>

<p>

	<label for="<?php echo $this->get_field_id('placeholder'); ?>">
		Search Placeholder: 
	</label>
	<input class="widefat" id="<?php echo $this->get_field_id('placeholder'); ?>" name="<?php echo $this->get_field_name('placeholder'); ?>" type="text" value="<?php echo attribute_escape($placeholder); ?>" />
	<br/><br/>
	<label for="<?php echo $this->get_field_id('type'); ?>">
		Search Method:
	</label>
	<select class="widefat" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
		<option value="index" <?php echo attribute_escape($type) == "index" ? 'selected="selected"':''; ?>>Indexed Search</option>
		<option value="slice" <?php echo attribute_escape($type) == "slice" ? 'selected="selected"':''; ?>>Slice</option>
	</select>
	<br/><br/>	
	
	<label for="<?php echo $this->get_field_id('results'); ?>">
		Results: 
	</label>
	<select class="widefat" id="<?php echo $this->get_field_id('results'); ?>" name="<?php echo $this->get_field_name('results'); ?>"> 
		<option value=""><?php echo esc_attr( __( 'Select a results page' ) ); ?></option> 
		<?php 
			$pages = get_pages(); 
			foreach ( $pages as $page ) {
				$option = ($page->ID == attribute_escape($results)) ? '<option selected="selected" value="' . $page->ID . '">' : '<option value="' . $page->ID . '">';
				$option .= $page->post_title;
				$option .= '</option>';
				echo $option;
			}
		?>
	</select><br/><br/>
	
	<label for="<?php echo $this->get_field_id('hide'); ?>">
		Check to show this widget only on results and preview pages: 
	</label>
	<input class="widefat" <?php echo $hide == 'on' ? 'checked' : ''; ?> id="<?php echo $this->get_field_id('hide'); ?>" name="<?php echo $this->get_field_name('hide'); ?>" type="checkbox" />
	<br/><br/>
	
</p>
  
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
	$instance['results'] = $new_instance['results'];
	$instance['placeholder'] = $new_instance['placeholder'];
	$instance['type'] = $new_instance['type'];
	$instance['hide'] = $new_instance['hide'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
	if($instance['hide'] == 'on' && (empty($_GET['query']) && empty($_GET['lr_resource'])))
		return;
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    $type = empty($instance['type']) ? 'index' : $instance['type'];

    //if (!empty($title))
    //  echo $before_title . $title . $after_title;;
		
	?>
	
		<form method="get" id="LRsearchForm" action="<?php echo get_page_link( $instance['results'] ); ?>">
		<div class="lrSearchCombo">
			<input class="lrSearch" type="text" placeholder="<?php echo $instance['placeholder']; ?>" name="query" />
			<input class="lrSubmit" type="submit" value="Search" />
			</div>
		</form>
	
	
	<script type="text/javascript">
		jQuery(document).ready(function($){
		
			$("#LRsearchForm").submit(function(e){
				e.preventDefault();
				
				window.location.href = '<?php echo add_query_arg("query", "LRreplaceMe", get_page_link( $instance['results']));?>'.replace("LRreplaceMe", $("#LRsearchForm input").val() + '&type=<?php echo $type; ?>');
			});
		});
	</script>
	<?php
    echo $after_widget;
  }
 
}