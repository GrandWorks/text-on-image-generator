<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.grandworks.co
 * @since             1.0.0
 * @package           Text_Over_Image
 *
 * @wordpress-plugin
 * Plugin Name:       Text over image
 * Plugin URI:        www.grandworks.co
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.2.0
 * Author:            GrandWorks
 * Author URI:        www.grandworks.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       text-over-image
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TEXT_OVER_IMAGE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-text-over-image-activator.php
 */
function activate_text_over_image() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-text-over-image-activator.php';
	Text_Over_Image_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-text-over-image-deactivator.php
 */
function deactivate_text_over_image() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-text-over-image-deactivator.php';
	Text_Over_Image_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_text_over_image' );
register_deactivation_hook( __FILE__, 'deactivate_text_over_image' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-text-over-image.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

// Add Metaboxes
add_action( 'add_meta_boxes', 'wpt_add_event_metaboxes' );
function wpt_add_event_metaboxes() {
    $post_types = get_post_types(["_builtin"=>false]);

    foreach ($post_types as $key => $value)
    {
        add_meta_box(
            'wpt_events_location',
            'Image Generator',
            'wpt_events_location',
            $value,
            'advanced',
            'default'
        );
    }

    add_meta_box(
        'wpt_events_location',
        'Image Generator',
        'wpt_events_location',
        'post',
        'advanced',
        'default'
    );
}

// Render fields in metabox
function wpt_events_location(){
    global $post;
    wp_nonce_field( "textoverimage", "toi_form" );
	$line_one = get_post_meta( $post->ID, 'line-one', true );
	$line_one_color = get_post_meta( $post->ID, 'line-one-color', true ) ? get_post_meta( $post->ID, 'line-one-color', true ) :"#7b00ff"; ;
	$line_two = get_post_meta( $post->ID, 'line-two', true );
	$line_two_color = get_post_meta( $post->ID, 'line-two-color', true ) ? get_post_meta( $post->ID, 'line-two-color', true ) :"#7b00ff";
	$line_three = get_post_meta( $post->ID, 'line-three', true );
	$line_three_color = get_post_meta( $post->ID, 'line-three-color', true ) ? get_post_meta( $post->ID, 'line-three-color', true ) :"#7b00ff";
    $file_path = get_post_meta( $post->ID, 'file-path', true );
	echo '
    <h1>Text over image</h1>
    <input type="hidden" name="updated" value="true" />

    <table class="form-table">
        <tr>
            <th scope="row"><label for="line-one">Line One</label></th>
			<td>
				<input type="text" name="line-one" class="regular-text" value="'.$line_one.'">
				<input type="color" name="line-one-color" value="'.$line_one_color.'">
			</td>
        </tr>
        <tr>
            <th scope="row"><label for="line-two">Line Two</label></th>
			<td>
				<input type="text" name="line-two" class="regular-text" value= "'.$line_two.'">
				<input type="color" name="line-two-color" value="'.$line_two_color.'">
			</td>
        </tr>
        <tr>
            <th scope="row"><label for="line-three">Line Three</label></th>
			<td>
				<input type="text" name="line-three" class="regular-text" value="'.$line_three.'">
				<input type="color" name="line-three-color" value="'.$line_three_color.'">
			</td>
        </tr>
        <tr>
            <th scope="row"><label for="file-path">File Path</label></th>
            <td><input type="text" name="file-path" class="regular-text" value="'.$file_path.'" readonly></td>
        </tr>
    </table>
    <img src="'.$file_path.'" heigh=640px width="400">
    
';
}

// Save metabox values 
function wpt_save_events_meta( $post_id, $post ) {
    // echo wp_verify_nonce( "toi_form","textoverimage" );
    // die('after update');
	// Return if the user doesn't have edit permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}
	// Verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times.
	if ( ! isset( $_POST['line-one'] ) ) {
		return $post_id;
	}
	// Now that we're authenticated, time to save the data.
	// This sanitizes the data from the field and saves it into an array $events_meta.
	$events_meta['line-one'] = esc_textarea( $_POST['line-one'] );
	$events_meta['line-one-color'] = esc_textarea( $_POST['line-one-color'] );
	$events_meta['line-two'] = esc_textarea( $_POST['line-two'] );
	$events_meta['line-two-color'] = esc_textarea( $_POST['line-two-color'] );
	$events_meta['line-three'] = esc_textarea( $_POST['line-three'] );
	$events_meta['line-three-color'] = esc_textarea( $_POST['line-three-color'] );

    $file_path = handle_form($post_id);

    $events_meta['file-path'] = $file_path;
	// Cycle through the $events_meta array.
	// Note, in this example we just have one item, but this is helpful if you have multiple.
	foreach ( $events_meta as $key => $value ) :
		// Don't store custom data twice
		if ( 'revision' === $post->post_type ) {
			return;
		}
		if ( get_post_meta( $post_id, $key, false ) ) {
			// If the custom field already has a value, update it.
			update_post_meta( $post_id, $key, $value );
		} else {
			// If the custom field doesn't have a value, add it.
			add_post_meta( $post_id, $key, $value);
		}
		if ( ! $value ) {
			// Delete the meta key if there's no value
			delete_post_meta( $post_id, $key );
		}
    endforeach;
}
add_action( 'save_post', 'wpt_save_events_meta', 1, 2 );

function handle_form($id)
{
	require 'vendor/autoload.php';
	// Create image
	$image = new \NMC\ImageWithText\Image(ABSPATH .'wp-content/plugins/text-over-image/admin/img/share.jpg');
	// $font_path = ABSPATH .'wp-content/plugins/text_over_image/admin/fonts/SourceSansPro-Bold.ttf';
	// print_r(plugins_url('admin/fonts/SourceSansPro-Bold.ttf',dirname(__FILE__)));
	// print_r(ABSPATH .'wp-content/plugins/text_over_image/admin/fonts/SourceSansPro-Bold.ttf');
	// die();
	$font_path = dirname(__FILE__).'/admin/fonts/SourceSansPro-Bold.ttf';
	$font_size = 65;
	$line_height = 87.5;
	$satrt_x = 40;

	$line_one = $_POST['line-one'];
	$line_one_color = $_POST['line-one-color'];
	$line_two = $_POST['line-two'];
	$line_two_color = $_POST['line-two-color'];
	$line_three = $_POST['line-three'];
	$line_three_color = $_POST['line-three-color'];

	if($line_one!="" &&  $line_two == "" && $line_three=="")
	{
		// Add styled text to image
		$text1 = new \NMC\ImageWithText\Text($line_one, 1, 1280);
		$text1->align = 'left';
		$text1->color = $line_one_color;
		$text1->font = $font_path;
		$text1->lineHeight = 87.5;
		$text1->size = 65;
		$text1->startX = 40;
		$text1->startY = (630/2) - (65);
		$image->addText($text1);
	}
	elseif($line_one!="" &&  $line_two != "" && $line_three =="")
    {
		// Add styled text to image
		$text1 = new \NMC\ImageWithText\Text($line_one, 1, 1280);
		$text1->align = 'left';
		$text1->color = $line_one_color;
		$text1->font = $font_path;
		$text1->lineHeight = 87.5;
		$text1->size = 65;
		$text1->startX = 40;
		$text1->startY = (630/2) - 65 - (87.5/2);
		$image->addText($text1);

		// Add another styled text to image
		$text2 = new \NMC\ImageWithText\Text($line_two, 1, 1280);
		$text2->align = 'left';
		$text2->color = $line_two_color;
		$text2->font = $font_path;
		$text2->lineHeight = 87.5;
		$text2->size = 65;
		$text2->startX = 40;
		$text2->startY = (630/2) - 65 + (87.5/2);
		$image->addText($text2);
	}
	elseif($line_one!="" &&  $line_two != "" && $line_three!="")
    {
		$text1 = new \NMC\ImageWithText\Text($line_one, 1, 1280);
		$text1->align = 'left';
		$text1->color = $line_one_color;
		$text1->font = $font_path;
		$text1->lineHeight = 87.5;
		$text1->size = 65;
		$text1->startX = 40;
		$text1->startY = (630/2) - 65 - (87.5);
		$image->addText($text1);

		// Add another styled text to image
		$text2 = new \NMC\ImageWithText\Text($line_two, 1, 1280);
		$text2->align = 'left';
		$text2->color = $line_two_color;
		$text2->font = $font_path;
		$text2->lineHeight = 87.5;
		$text2->size = 65;
		$text2->startX = 40;
		$text2->startY = (630/2) - 65;
		$image->addText($text2);

		$text2 = new \NMC\ImageWithText\Text($line_three, 1, 1280);
		$text2->align = 'left';
		$text2->color = $line_three_color;
		$text2->font = $font_path;
		$text2->lineHeight = 87.5;
		$text2->size = 65;
		$text2->startX = 40;
		$text2->startY = (630/2) - 65 + 87.5;
		$image->addText($text2);	
	}
	// // Add styled text to image
	// $text1 = new \NMC\ImageWithText\Text($line_one, 1, 1280);
	// $text1->align = 'left';
	// $text1->color = 'FFFFFF';
	// $text1->font = $font_path;
	// $text1->lineHeight = 87.5;
	// $text1->size = 65;
	// $text1->startX = 40;
	// $text1->startY = (630/2) - 65 - (87.5);
	// $image->addText($text1);

	// // Add another styled text to image
	// $text2 = new \NMC\ImageWithText\Text($line_two, 1, 1280);
	// $text2->align = 'left';
	// $text2->color = '7b00ff';
	// $text2->font = $font_path;
	// $text2->lineHeight = 87.5;
	// $text2->size = 65;
	// $text2->startX = 40;
	// $text2->startY = (630/2) - 65;
	// $image->addText($text2);

	// $text2 = new \NMC\ImageWithText\Text($line_three, 1, 1280);
	// $text2->align = 'left';
	// $text2->color = 'FFFFFF';
	// $text2->font = $font_path;
	// $text2->lineHeight = 87.5;
	// $text2->size = 65;
	// $text2->startX = 40;
	// $text2->startY = (630/2) - 65 + 87.5;
	// $image->addText($text2);
	// // Render image
	$image->render(dirname(__FILE__).'/public/img/share_edited_'.$id.'.jpg');
	// Upload to medial library
    $file = dirname(__FILE__).'/public/img/share_edited_'.$id.'.jpg';
    $filename = basename($file);
    $processed_file_name = preg_replace('/\.[^.]+$/', '', $filename);
    $upload_file = wp_upload_bits($filename, null, file_get_contents($file));
    if (!$upload_file['error']) {
        $wp_filetype = wp_check_filetype($filename, null );

        if( post_exists( $processed_file_name ) ){
            $attachment_array = get_page_by_title( $processed_file_name, OBJECT, 'attachment');
            if( !empty( $attachment_array ) ){
            //   $attachment_data['ID'] = 
                $attachment = array(
                    'ID' => $attachment_array->ID,
                    'post_mime_type' => $wp_filetype['type'],
                    // 'post_parent' => $parent_post_id,
                    'post_title' => $processed_file_name,
                    'post_content' => '',
                    'post_status' => 'inherit'
                );
            }
          }
          else {
              
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                // 'post_parent' => $parent_post_id,
                'post_title' => $processed_file_name,
                'post_content' => '',
                'post_status' => 'inherit'
            );
          }    

        $attachment_id = wp_insert_attachment( $attachment, $upload_file['file'] );
        if (!is_wp_error($attachment_id)) {
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
            wp_update_attachment_metadata( $attachment_id,  $attachment_data );
            return wp_get_attachment_url($attachment_id);
        }
    }
}


function run_text_over_image() {

	$plugin = new Text_Over_Image();
	$plugin->run();

}
run_text_over_image();
