jQuery(document).ready(function($){

	/**
	 * Open the box
	 */
	$('#sds_user_image_button').click(function() {

		tb_show('Author profile picture', 'media-upload.php?referer=profile&amp;type=image&amp;TB_iframe=true', false);

		return false;
	});

	/**
	 * Send the url to hidden filed 'author_profile_picture_url'
	 * Send the image url so we can disply the preview
	 * Close box
	 */
	window.send_to_editor = function(html) {

		var image_url = $('img',html).attr('src');

		$('#author_profile_picture_url').val(image_url);

		$('#author_profile_picture_preview').html( '<img src="' + image_url + '"">');

		tb_remove();

	}

});