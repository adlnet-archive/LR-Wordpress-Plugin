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

	//$options = json_decode


?>
<div class="wrap">
<?php screen_icon(); ?>
<h2>Learning Registry Interface Plugin</h2>


<form method="post" action="options.php">
    <?php //settings_fields( 'lr-settings-group' ); ?>
    <?php // do_settings( 'lr-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">New Option Name</th>
        <td><input type="text" name="host" value="<?php echo get_option('host'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Some Other Option</th>
        <td><input type="text" name="some_other_option" value="<?php echo get_option('some_other_option'); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Options, Etc.</th>
        <td><input type="text" name="option_etc" value="<?php echo get_option('option_etc'); ?>" /></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>