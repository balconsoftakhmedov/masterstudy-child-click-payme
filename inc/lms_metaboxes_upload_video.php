<?php

class LmsUploadField
{
	function __construct()
	{
		add_action('add_meta_boxes', [$this, 'add_custom_meta_boxes']);
		add_action('save_post', [$this, 'save_custom_meta_data']);
		add_action('post_edit_form_tag', [$this, 'update_edit_form']);
		add_action('admin_footer', [$this, 'wpse_5102_clear_errors']);
		add_action('admin_notices', [$this, 'wpse_5102_admin_notice_handler']);

	}

	function add_custom_meta_boxes()
	{

		// Define the custom attachment for posts
        /*
		add_meta_box(
			'wp_custom_attachment',
			'Hidden Video',
			array($this, 'wp_custom_attachment'),
			'stm-lessons',
			'normal'
		);
        */

	}

	function wp_custom_attachment()
	{
		wp_nonce_field(plugin_basename(__FILE__), 'wp_custom_attachment_nonce');
		$img = get_post_meta(get_the_ID(), 'wp_custom_attachment', true);
		?>
        <div class="file_up">
            <b><?php if (!empty($img['file'])) echo $img['file']; ?></b>
        </div>
        <p class="description">
            Upload Hidden Video
        </p>
        <input type="file" id="wp_custom_attachment" name="wp_custom_attachment" value="<?php if (!empty($img['file'])) echo $img['file']; ?>" size="25"/>
		<?php
	} // end wp_custom_attachment

	static function lms_wp_custom_attachment()
	{
		wp_nonce_field(plugin_basename(__FILE__), 'wp_custom_attachment_nonce');
		$img = get_post_meta(get_the_ID(), 'wp_custom_attachment', true);
		?>

        <div class="wpcfto_generic_field wpcfto_generic_field_flex_input wpcfto_generic_field__video">
            <div class="wpcfto-field-aside">
                <label class="wpcfto-field-aside__label"> Upload Hidden Video</label>
            </div>
            <div class="wpcfto-field-content">
                <input type="file" id="wp_custom_attachment" name="wp_custom_attachment" value="<?php if (!empty($img['file'])) echo $img['file']; ?>" size="25"/>
            </div>
            <div><?php if (!empty($img['file'])) echo $img['file']; ?>&nbsp;</div>
        </div>
        <div>&nbsp;</div>
		<?php
	} // end wp_custom_attachment

	function save_custom_meta_data($id)
	{

stm_put_log('file_u', __LINE__);
		$_POST['wp_custom_attachment_nonce'] = (!empty($_POST['wp_custom_attachment_nonce'])) ? $_POST['wp_custom_attachment_nonce'] : '';
		/* --- security verification --- */
		if (!wp_verify_nonce($_POST['wp_custom_attachment_nonce'], plugin_basename(__FILE__))) {
			return $id;
		} // end if
stm_put_log('file_u', __LINE__);
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $id;
		} // end if

stm_put_log('file_u', __LINE__);

		// Make sure the file array isn't empty
		if (!empty($_FILES['wp_custom_attachment']['name'])) {
stm_put_log('file_u', __LINE__);
			// Setup the array of supported file types. In this case, it's just PDF.
			$supported_types = array(
				'video/x-flv',
				'video/mp4',
				'application/x-mpegURL',
				'video/MP2T',
				'video/3gpp',
				'video/quicktime',
				'video/x-msvideo',
				'video/x-ms-wmv'
			);
			stm_put_log('file_u', __LINE__);
			// Get the file type of the upload
			$arr_file_type = wp_check_filetype(basename($_FILES['wp_custom_attachment']['name']));
stm_put_log('file_u', __LINE__);
			$uploaded_type = $arr_file_type['type'];

			// Check if the type is supported. If not, throw an error.
			if (in_array($uploaded_type, $supported_types)) {
stm_put_log('file_u', __LINE__);
				// Use the WordPress API to upload the file
				$upload = wp_upload_bits($_FILES['wp_custom_attachment']['name'], null, file_get_contents($_FILES['wp_custom_attachment']['tmp_name']));
				$file_path = $upload['file'];
				$moved_file_path = $this->move_file($file_path);
				if ($moved_file_path) {
					$upload = [
						'file' => $moved_file_path,
						'url' => '\\',
						'type' => $upload['type'],
						'error' => false
					];
				} else {
					$upload = [
						'error' => true
					];
				}
stm_put_log('file_u', __LINE__);
				stm_put_log('file_u', __LINE__);
				stm_put_log('file_u', $upload);

				if (isset($upload['error']) && $upload['error'] != 0) {
					//add_action('admin_notices', [$this, 'filbr_invalid_id_error']);
					$this->filbr_invalid_id_error();
				} else {
					add_post_meta($id, 'wp_custom_attachment', $upload);
					update_post_meta($id, 'wp_custom_attachment', $upload);
				} // end if/else

			} else {
				$this->filbr_invalid_id_error();
			} // end if/else

		} // end if

	} // end save_custom_meta_data

	function update_edit_form()
	{
		echo ' enctype="multipart/form-data"';
	} // end update_edit_form

	function filbr_invalid_id_error()
	{
		$errors = esc_attr('The file type that you  uploaded is not a Video', 'masterstudy-child');
		update_option('my_admin_errors', $errors);
	}

	function move_file($path)
	{
		$upload_dir = wp_upload_dir();
		$to = STM_LMS_Options::get_option('protected_video_absolute_folder_path', $upload_dir['path']);

		if ($this->folder_exist($to) === false) {
			mkdir($to);
		}
		$file_name = pathinfo($path);

		$n = rand(1, 10000000);
		if (copy($path, $to . '/' . "{$n}_{$file_name['basename']}")) {
			unlink($path);
			$to = str_replace("\\", "/", $to);
			return $to . '/' . "{$n}_{$file_name['basename']}";
		} else {
			return false;
		}
	}

	function folder_exist($folder)
	{
		// Get canonicalized absolute pathname
		$path = realpath($folder);

		// If it exist, check if it's a directory
		if ($path !== false and is_dir($path)) {
			// Return canonicalized absolute pathname
			return $path;
		}

		// Path/folder does not exist
		return false;
	}


// Display any errors
	function wpse_5102_admin_notice_handler()
	{

		$errors = get_option('my_admin_errors');

		if ($errors) {

			echo '<div class="error"><p>' . $errors . '</p></div>';

		}

	}


// Clear any errors
	function wpse_5102_clear_errors()
	{

		update_option('my_admin_errors', false);

	}


}

new LmsUploadField;




