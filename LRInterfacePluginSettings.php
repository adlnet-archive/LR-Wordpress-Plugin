<?php
// create custom plugin settings menu
add_action('admin_menu', 'lr_options_manager');

function lr_options_manager() {

	//create new top-level menu
	add_menu_page('LR Interface', 'LR Interface', 'manage_options',"lr-options-config", 'lr_options_page', plugins_url('/images/icon.png', __FILE__));
	add_submenu_page("lr-options-config", "Flagged Items", "Flagged Items", "manage_options", "lr-flagged-items", 'flagged_items');
	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
	//register our settings
	register_setting( 'lr-settings-group', 'lr_options_object' );
}

function flagged_items(){
?>
<div class="wrap">
  <h1>Flagged Items</h1>	   
  <dl id="flaggedDocuments"/>
  <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
  <script type="text/javascript">
  $(document).ready(function(){
  var container = $("#flaggedDocuments");
  $.getJSON("/wordpress", {json: "flagged.get_flagged_ids"}, function(data){
      if(data.ids){
      for(var i in data.ids){
      var id = data.ids[i];
      var link = $("<a>").attr("href", "/wordpress/?page_id=4&type=index&lr_resource=" + id._id).html(id.title);
      container.append($("<dt>").append(link));
      container.append($("<dd>").append(id.description));
      }}
      });
});
  </script>
</div>
<?php
}
function lr_options_page() {

$options = get_option('lr_options_object');

$nodeArray = array("http://node01.public.learningregistry.net/", 
                   "http://node02.public.learningregistry.net/", 
		   "http://lrtest01.public.learningregistry.net/", 
		   "http://sandbox.learningregistry.org/", 
		   "http://lrdev03.learningregistry.org/", 
		   "http://lrdev05.learningregistry.org/");

?>
<div class="wrap">
<?php screen_icon(); ?>
<h2>Learning Registry Interface Plugin</h2>


<form method="post" action="options.php">
    <?php settings_fields( 'lr-settings-group' ); ?>
    <?php // do_settings( 'lr-settings-group' ); ?>
    <table class="form-table">         		
	    <tr valign="top">
			<th scope="row">WebService Endpoint:</th>
			<td><input type="text" name="lr_options_object[host]" value="<?php echo $options['host']?$options['host']:'http://12.109.40.31'; ?>" /></td>
        </tr>
		
		<tr valign="top">
			<th scope="row">Learning Registry Node:</th>
			<td>
				<select name="lr_options_object[node]">
					<?php $len = sizeof($nodeArray); for($i = 0; $i < $len; $i++): ?>
					
						<option value="<?php echo $nodeArray[$i]; ?>" <?php echo $options['node'] == $nodeArray[$i] ? 'selected="selected"':''; ?> >
							<?php echo $nodeArray[$i]; ?>
						</option>
						
					<?php endfor; ?>
				</select>
			</td>		
        </tr>
		
		<tr valign="top">
			<th scope="row">Maximum Slice:</th>
			<td><input type="text" name="lr_options_object[slice]" value="<?php echo $options['slice']?$options['slice']:500; ?>" /></td>
        </tr>		
		<tr valign="top">
			<th scope="row">Check to hide metadata:</th>
			<td><input type="checkbox" name="lr_options_object[metadata]" <?php echo !empty($options['metadata'])?' checked ':''; ?> /></td>
        </tr>			
		<tr valign="top">
			<th scope="row">Check to hide paradata:</th>
			<td><input type="checkbox" name="lr_options_object[paradata]" <?php echo !empty($options['paradata'])?' checked ':''; ?> /></td>
        </tr>	
		<tr valign="top">
			<th scope="row">Results: </th>
			<td>
				<select name="lr_options_object[results]"> 
					<option><?php echo esc_attr( __( 'Select a results page' ) ); ?></option> 
					<?php 
						$pages = get_pages(); 
						foreach ( $pages as $page ) {
							$option = ($page->ID == $options['results']) ? '<option selected="selected" value="' . $page->ID . '">' : '<option value="' . $page->ID . '">';
							$option .= $page->post_title;
							$option .= '</option>';
							echo $option;
						}
					?>
				</select>
			</td>
        </tr>
		<tr valign="top">
			<th scope="row">Social media plugin code:</th>
			<td><textarea style="width:400px;height:200px;" name="lr_options_object[social]"><?php echo $options['social']; ?></textarea></td>
        </tr>	
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>