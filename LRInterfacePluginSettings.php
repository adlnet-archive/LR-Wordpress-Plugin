<?php
// create custom plugin settings menu
add_action('admin_menu', 'lr_options_manager');

function lr_options_manager() {

	//create new top-level menu
	add_menu_page('LR Interface', 'LR Interface', 'administrator', __FILE__, 'lr_options_page', plugins_url('/images/icon.png', __FILE__));

	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
	//register our settings
	register_setting( 'lr-settings-group', 'lr_options_object' );
}

function lr_options_page() {

$options = get_option('lr_options_object');
$nodeArray = array("http://node01.public.learningregistry.net/", "http://node02.public.learningregistry.net/", "http://lrtest01.public.learningregistry.net/", "http://sandbox.learningregistry.org/", "http://lrdev03.learningregistry.org/", "http://lrdev05.learningregistry.org/");

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
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>