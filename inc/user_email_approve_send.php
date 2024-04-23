<?php
add_filter('manage_users_columns', 'pippin_add_user_id_column');
function pippin_add_user_id_column($columns)
{
	$columns['user_id'] = 'User ID';
	return $columns;
}

add_action('manage_users_custom_column', 'pippin_show_user_id_column_content', 10, 3);
function pippin_show_user_id_column_content($value, $column_name, $user_id)
{
	$user = get_userdata($user_id);
	if ('user_id' == $column_name)
		return $user_id;
	return $value;
}

function stm_lms_offerta()
{
	//check_ajax_referer('stm_lms_add_to_cart', 'nonce');
	$user_id = (get_current_user_id()) ? get_current_user_id() : null;
	$accept = ($_REQUEST['accept']) ? sanitize_text_field($_REQUEST['accept']) : null;
	$selected_javoblar = ($_REQUEST['selected_javoblar']) ? (array)json_decode(str_replace("\\", "", $_REQUEST['selected_javoblar'])) : null;

	$passport = upload_stm_file($user_id);

	update_user_meta($user_id, 'accept', $accept);
	$user_info = get_user_meta($user_id);
	foreach ($selected_javoblar as $key => $javoblar) {
		$javoblar = (array)$javoblar;
		update_user_meta($user_id, $key, $javoblar);
	}
	$redirect_url = '';
	if ($passport) {
		update_user_meta($user_id, 'passport', $passport);
		send_email($user_id);
	};
	if ($accept == 'no') {
		wp_logout();
		$redirect_url = site_url();
	}
	$response = [
		'userid' => $user_id,
		'userinfo' => $user_info,
		'redirect_url' => $redirect_url,
		'accept' => $accept
	];

	wp_send_json($response);
}

add_action('wp_ajax_stm_lms_offerta', 'stm_lms_offerta');
add_action('wp_ajax_nopriv_stm_lms_offerta', 'stm_lms_offerta');

function load_oferta_ajax()
{

	check_ajax_referer('stm_lms_add_to_cart', 'nonce');

	if (empty($_GET['modal'])) die;
	$r = array();

	$modal = 'modals/' . sanitize_text_field($_GET['modal']);
	$params = (!empty($_GET['params'])) ? json_decode(stripslashes_deep($_GET['params']), true) : array();
	$r['params'] = $params;
	$r['modal'] = STM_LMS_Templates::load_lms_template($modal, $params);

	wp_send_json($r);

}

add_action('wp_ajax_load_oferta_ajax', 'load_oferta_ajax');
add_action('wp_ajax_nopriv_load_oferta_ajax', 'load_oferta_ajax');

$data = $payload = json_decode(file_get_contents('php://input'), true);
//stm_put_log('all_request', $data);
add_action('init', 'load_modal_oferta');

function load_modal_oferta($modal = 'oferta', $params = [])
{
	$user_id = get_current_user_id();
	//$oferta = (get_user_meta($user_id, 'accept', true)) ? get_user_meta($user_id, 'accept', true) : false;
	$oferta = '';
	if ($user_id && $oferta != 'yes') {
		//  wp_enqueue_script('script-modal-oferta', get_stylesheet_directory_uri() . '/assets/js/lms-oferta.js', array('jquery'), time(), true);
	}
}

function upload_stm_file($user_id)
{
	global $wp_filesystem;
	WP_Filesystem();
	$upload = 'uploads/';
	$passport = 'passports';
	$content_directory = $wp_filesystem->wp_content_dir() . $upload;
	$wp_filesystem->mkdir($content_directory . $passport);

	$target_dir_location = $content_directory . "{$passport}/";
	$fileInfo = wp_check_filetype(basename($_FILES['file']['name']));
	$file_type = '';
	if (!empty($fileInfo['ext'])) {
		$file_type = $fileInfo['ext'];
	} else {
		return false;
	}

	if (isset($_FILES['file']) && in_array($file_type, ['jpg', 'png', 'gif', 'pdf', 'doc'])) {
		$name_file = $_FILES['file']['name'];
		$tmp_name = $_FILES['file']['tmp_name'];
		$name_file = time() . "_{$user_id}_{$name_file}";
		if (move_uploaded_file($tmp_name, $target_dir_location . $name_file)) {
			return "{$upload}{$passport}/{$name_file}";
		} else {
			return false;
		}
	}
}

function send_email($user_id)
{

	$firstname = get_user_meta($user_id, 'first_name', true);
	$lastname = get_user_meta($user_id, 'last_name', true);
	$passport = get_user_meta($user_id, 'passport', true);
	$accept = (get_user_meta($user_id, 'accept', true) == 'yes') ? 'Xa' : 'Yoq';

	$token = send_token_save($user_id);
	$buttons = approval_button($token);
	$body = " $firstname $lastname royhatdan otdi. Va Tasdiqlagan holati '$accept'. Tadiqlash uchun tugmachalrni bosing {$buttons}";
	global $wp_filesystem;
	WP_Filesystem();

	$file = $wp_filesystem->wp_content_dir() . $passport;
	$ext = pathinfo($file, PATHINFO_EXTENSION);

	if ($file) {
		$uid = "passport_{$user_id}"; //will map it to this UID
		$name = 'file.' . $ext; //this will be the file name for the attachment

		global $phpmailer;
		add_action('phpmailer_init', function (&$phpmailer) use ($file, $uid, $name) {
			$phpmailer->SMTPKeepAlive = true;
			$phpmailer->AddEmbeddedImage($file, $uid, $name);
		});
	}
	$admin_email = 'islomirzo@gmail.com';
	// $attachments = array(WP_CONTENT_DIR . '/' . $passport);
	$headers [] = 'Content-Type: text/html; charset=UTF-8';
	$headers[] = 'From: Itstar <admin@itstar.uz>' . "\r\n";

	$headers[] = 'Cc: tutyou1972@gmail.com';
	$headers[] = 'Cc: itstarsuz@gmail.com';
	wp_mail($admin_email, 'Salom Zafar itstar habar', $body, $headers);
}

function send_token_save($user_id)
{
	//Generate a random string.
	$token = openssl_random_pseudo_bytes(16);
//Convert the binary data into hexadecimal representation.
	$token = bin2hex($token);
//Print it out for example purposes.

	$transient = $token;
	$value = $user_id;
	$expiration = 360000;

	set_transient($transient, $value, $expiration);

	return $transient;
}

function approval_button($token)
{
	$token_link = site_url() . "/approve_user/?approval_token={$token}";
	$tasdiqlash = "$token_link&accept=yes";
	$inkor = "$token_link&accept=no";
	$button = "<div style='display:inline-block'>
                <button style='color:green; margin-right:10px;'><a href='{$tasdiqlash}'>Tasdiqlash</a></button>
                <button style='color:red'><a href='{$inkor}'>Inkor qilish</a></button>
                </div>";
	return $button;
}

?>