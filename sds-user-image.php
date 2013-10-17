<?php
/*
Plugin Name: SDS User image
Plugin URI: http://sydsvenskan.se
Description: Add a user image to the user profile. For upload we use the standard wp file uploader.
Version: 1.0
Author: Johannes Fosseus
*/

/**
 * Usage:
 * This will simply save an url as metadata.
 * To get the image use, get_user_meta()
 * echo esc_attr( get_user_meta( $user->ID, 'sds_profile_picture', true) );
 */

/**
 * Define constants
 */
define( 'VER', 1 );

/**
 * Add actions
 */
add_action( 'admin_enqueue_scripts', 'sds_assets' );
add_action( 'show_user_profile', 'sds_add_custom_profile_fields' );
add_action( 'edit_user_profile', 'sds_add_custom_profile_fields' );
add_action( 'personal_options_update', 'sds_save_custom_profile_fields' );
add_action( 'edit_user_profile_update', 'sds_save_custom_profile_fields' );

/**
 * Filter upload text button
 */
add_filter( 'gettext', 'sds_filter_upload_button_text', 1, 3 );


/**
 * Add sds_assets, scripts and styles if we are viewing profile edit pages
 */
function sds_assets() {

	wp_register_script( 'sds-user-image', plugins_url('sds-user-image.js', __FILE__), array('jquery','media-upload','thickbox') );

	if ( 'profile' == get_current_screen()->id OR 'user-edit' == get_current_screen()->id) {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script('media-upload', null, array( 'jquery' ));
		wp_enqueue_script('sds-user-image', null, array( 'jquery' ));
		wp_enqueue_script( 'thickbox', null, array( 'jquery' ) );
		wp_enqueue_style( 'thickbox.css', '/' . WPINC . '/js/thickbox/thickbox.css', null, VER );
	}

}


/**
 * Filter text in file uploader
 * "Insert into Post" -> "Make this my profile image"
 * But only if I call this form "profile" as referer
 */
function sds_filter_upload_button_text( $translated_text, $text, $domain ) {

	if ('Insert into Post' === $text) {

		$referer = strpos( wp_get_referer(), 'profile' );

		if ( $referer != '' ) {
			return __('Make this my profile image', 'q' );
		}
	}
	return $translated_text;
}

/**
 * Display the user image on Profile page
 */
function sds_add_custom_profile_fields( $user ) {

	$buttontext = '';

	$avatar = get_the_author_meta( 'sds_profile_picture', $user->ID );

	// add cta text
	if( $avatar ) {
		$buttontext = "Change profile image";
	} else {
		$buttontext = "Click to upload profile image";
	} ?>

	<table class="form-table">
		<tr>
			<th>
				<label for="sds_user_image_button"><?php _e('Profile image' ); ?></label>
			</th>
			<td>
				<input id="sds_user_image_button" type="button" class="button" value="<?php echo $buttontext; ?>" />
				<input type="hidden" id="author_profile_picture_url" name="author_profile_picture_url" value="<?php echo esc_url( $avatar ); ?>" />
				<div id="author_profile_picture_preview" style="min-height: 100px; margin-top: 20px;">

					<? if ( $avatar ) { ?>

					<img src="<?php echo esc_url( $avatar ); ?>">

					<fieldset>
						<label for="profile-user-remove">
							<input type="checkbox" value="remove" id="profile-user-remove" name="profile-user-remove">Remove image
						</label>
					</fieldset>

					<?php } ?>

				</div>
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Callback to save custom fields
 * If "profile-user-remove" val is "remove", we simply remove the metadata
 */
function sds_save_custom_profile_fields( $user_id ) {

	$picture_url = $_POST['author_profile_picture_url'];

	if ( !current_user_can( 'edit_user', $user_id ) )
		return FALSE;

	if( $_POST['profile-user-remove'] === 'remove' ){
		$picture_url = '';
	}

	update_user_meta( $user_id, 'sds_profile_picture', $picture_url );

}