<?php
require_once 'rest_route.php';
require_once 'user-tasdiqlash.php';
require_once 'user-video-timer.php';
if ( ! function_exists( 'stm_put_log' ) ) {
	function stm_put_log( $file_name, $data, $append = true ) {
		$file = get_stylesheet_directory() . "/logs/{$file_name}.log";
		$data = date( 'd.m.Y H:i:s', time() ) . " - " . var_export( $data, true ) . "\n";
		if ( $append ) {
			file_put_contents( $file, $data, FILE_APPEND );
		} else {
			file_put_contents( $file, $data );
		}
	}
}
function add_init_lms() {
	wp_dequeue_script( 'stm-lms-lms' );
	wp_enqueue_script( 'stm-lms-lms-custom', get_stylesheet_directory_uri() . '/assets/js/lms.js', [], stm_lms_custom_styles_v(), false );
	wp_enqueue_script( 'stm-lms-discount', get_stylesheet_directory_uri() . '/assets/js/discount-code.js', [ 'jquery' ], stm_lms_custom_styles_v(), false );
	wp_localize_script( 'stm-lms-lms-custom', 'stm_lms_vars', array(
		'symbol'             => STM_LMS_Options::get_option( 'currency_symbol', '$' ),
		'position'           => STM_LMS_Options::get_option( 'currency_position', 'left' ),
		'currency_thousands' => STM_LMS_Options::get_option( 'currency_thousands', ',' ),
		'wp_rest_nonce'      => wp_create_nonce( 'wp_rest' )
	) );
}

add_action( 'wp_enqueue_scripts', 'add_init_lms', 11 );
$data = $payload = json_decode( file_get_contents( 'php://input' ), true );
//stm_put_log('all_request', $data);
add_action( 'init', 'load_modal_oferta' );
function load_modal_oferta( $modal = 'oferta', $params = [] ) {
	$user_id = get_current_user_id();
	//$oferta = (get_user_meta($user_id, 'accept', true)) ? get_user_meta($user_id, 'accept', true) : false;
	$oferta = '';
	if ( $user_id && $oferta != 'yes' ) {
		//  wp_enqueue_script('script-modal-oferta', get_stylesheet_directory_uri() . '/assets/js/lms-oferta.js', array('jquery'), time(), true);
	}
}

add_filter( 'manage_users_columns', 'pippin_add_user_id_column' );
function pippin_add_user_id_column( $columns ) {
	$columns['user_id'] = 'User ID';

	return $columns;
}

add_action( 'manage_users_custom_column', 'pippin_show_user_id_column_content', 10, 3 );
function pippin_show_user_id_column_content( $value, $column_name, $user_id ) {
	$user = get_userdata( $user_id );
	if ( 'user_id' == $column_name ) {
		return $user_id;
	}

	return $value;
}

function stm_lms_offerta() {
	//check_ajax_referer('stm_lms_add_to_cart', 'nonce');
	$user_id           = ( get_current_user_id() ) ? get_current_user_id() : null;
	$accept            = ( $_REQUEST['accept'] ) ? sanitize_text_field( $_REQUEST['accept'] ) : null;
	$selected_javoblar = ( $_REQUEST['selected_javoblar'] ) ? (array) json_decode( str_replace( "\\", "", $_REQUEST['selected_javoblar'] ) ) : null;
	$passport          = upload_stm_file( $user_id );
	update_user_meta( $user_id, 'accept', $accept );
	$user_info = get_user_meta( $user_id );
	foreach ( $selected_javoblar as $key => $javoblar ) {
		$javoblar = (array) $javoblar;
		update_user_meta( $user_id, $key, $javoblar );
	}
	$redirect_url = '';
	if ( $passport ) {
		update_user_meta( $user_id, 'passport', $passport );
		send_email( $user_id );
	};
	if ( $accept == 'no' ) {
		wp_logout();
		$redirect_url = site_url();
	}
	$response = [
		'userid'       => $user_id,
		'userinfo'     => $user_info,
		'redirect_url' => $redirect_url,
		'accept'       => $accept
	];
	wp_send_json( $response );
}

add_action( 'wp_ajax_stm_lms_offerta', 'stm_lms_offerta' );
add_action( 'wp_ajax_nopriv_stm_lms_offerta', 'stm_lms_offerta' );
function load_oferta_ajax() {

	check_ajax_referer( 'stm_lms_add_to_cart', 'nonce' );
	if ( empty( $_GET['modal'] ) ) {
		die;
	}
	$r                 = array();
	$modal             = 'modals/' . sanitize_text_field( $_GET['modal'] );
	$params            = ( ! empty( $_GET['params'] ) ) ? json_decode( stripslashes_deep( $_GET['params'] ), true ) : array();
	$params['plan_id'] = ( ! empty( $_GET['plan_id'] ) ) ? $_GET['plan_id'] : '';
	$r['params']       = $params;
	$r['modal']        = STM_LMS_Templates::load_lms_template( $modal, $params );
	wp_send_json( $r );

}

add_action( 'wp_ajax_load_oferta_ajax', 'load_oferta_ajax' );
add_action( 'wp_ajax_nopriv_load_oferta_ajax', 'load_oferta_ajax' );
function upload_stm_file( $user_id ) {
	global $wp_filesystem;
	WP_Filesystem();
	$upload            = 'uploads/';
	$passport          = 'passports';
	$content_directory = $wp_filesystem->wp_content_dir() . $upload;
	$wp_filesystem->mkdir( $content_directory . $passport );
	$target_dir_location = $content_directory . "{$passport}/";
	$fileInfo            = wp_check_filetype( basename( $_FILES['file']['name'] ) );
	$file_type           = '';
	if ( ! empty( $fileInfo['ext'] ) ) {
		$file_type = $fileInfo['ext'];
	} else {
		return false;
	}
	if ( isset( $_FILES['file'] ) && in_array( $file_type, [ 'jpg', 'png', 'gif', 'pdf', 'doc' ] ) ) {
		$name_file = $_FILES['file']['name'];
		$tmp_name  = $_FILES['file']['tmp_name'];
		$name_file = time() . "_{$user_id}_{$name_file}";
		if ( move_uploaded_file( $tmp_name, $target_dir_location . $name_file ) ) {
			return "{$upload}{$passport}/{$name_file}";
		} else {
			return false;
		}
	}
}

function send_email( $user_id ) {

	$firstname = get_user_meta( $user_id, 'first_name', true );
	$lastname  = get_user_meta( $user_id, 'last_name', true );
	$passport  = get_user_meta( $user_id, 'passport', true );
	$accept    = ( get_user_meta( $user_id, 'accept', true ) == 'yes' ) ? 'Xa' : 'Yoq';
	$token     = send_token_save( $user_id );
	$buttons   = approval_button( $token );
	$body      = " $firstname $lastname royhatdan otdi. Va Tasdiqlagan holati '$accept'. Tadiqlash uchun tugmachalrni bosing {$buttons}";
	global $wp_filesystem;
	WP_Filesystem();
	$file = $wp_filesystem->wp_content_dir() . $passport;
	$ext  = pathinfo( $file, PATHINFO_EXTENSION );
	if ( $file ) {
		$uid  = "passport_{$user_id}"; //will map it to this UID
		$name = 'file.' . $ext; //this will be the file name for the attachment
		global $phpmailer;
		add_action( 'phpmailer_init', function ( &$phpmailer ) use ( $file, $uid, $name ) {
			$phpmailer->SMTPKeepAlive = true;
			$phpmailer->AddEmbeddedImage( $file, $uid, $name );
		} );
	}
	$admin_email = 'islomirzo@gmail.com';
	// $attachments = array(WP_CONTENT_DIR . '/' . $passport);
	$headers [] = 'Content-Type: text/html; charset=UTF-8';
	$headers[]  = 'From: Itstar <admin@itstar.uz>' . "\r\n";
	$headers[]  = 'Cc: tutyou1972@gmail.com';
	$headers[]  = 'Cc: itstarsuz@gmail.com';
	wp_mail( $admin_email, 'Salom Zafar itstar habar', $body, $headers );
}

function send_token_save( $user_id ) {
	//Generate a random string.
	$token = openssl_random_pseudo_bytes( 16 );
//Convert the binary data into hexadecimal representation.
	$token = bin2hex( $token );
//Print it out for example purposes.
	$transient  = $token;
	$value      = $user_id;
	$expiration = 360000;
	set_transient( $transient, $value, $expiration );

	return $transient;
}

function approval_button( $token ) {
	$token_link = site_url() . "/approve_user/?approval_token={$token}";
	$tasdiqlash = "$token_link&accept=yes";
	$inkor      = "$token_link&accept=no";
	$button     = "<div style='display:inline-block'>
                <button style='color:green; margin-right:10px;'><a href='{$tasdiqlash}'>Tasdiqlash</a></button>
                <button style='color:red'><a href='{$inkor}'>Inkor qilish</a></button>
                </div>";

	return $button;
}

function buy_button( $course_id, $plan_id ) {

	?>
    <div class=" stm-lms-buy-buttons-mixed stm-lms-buy-buttons-mixed-pro sssss dssssssssss">
        <div class="stm_lms_mixed_button subscription_disabled">
            <div class="stm_lms_form_html"></div>
        </div>

		<?php if ( is_user_logged_in() ) : ?>
			<?php
			$user_id = get_current_user_id();
			$oferta  = ( get_user_meta( $user_id, 'accept', true ) ) ? get_user_meta( $user_id, 'accept', true ) : false;
			$oferta  = 'yes';
			if ( in_array( $plan_id, [ 1, 2 ] ) || ( $oferta == 'yes' ) ):
				?>
                <div class="payment-btns">
                    <!-- <button type="button" class="payment-btns__switch btn-theme btn btn-outline-primary">Sotib olish
                    </button>

                    !-->
                    <div class="payment-btns__inner" style="display: block !important;">
                        <button type="button" class="btn btn-theme btn-outline-primary"
                                data-payment-method="payme"
                                class="stm-lms-buy-buttons stm-lms-buy-buttons-mixed stm-lms-buy-buttons-mixed-pro"
                                data-buy-course="<?php echo $course_id ?>"
                                data-buy-plan="<?php echo $plan_id ?>"
                        >Payme
                        </button>
                        <button type="button" class="btn btn-theme btn-outline-primary"
                                data-payment-method="click"
                                class="stm-lms-buy-buttons stm-lms-buy-buttons-mixed stm-lms-buy-buttons-mixed-pro  click-payment"
                                data-buy-course="<?php echo $course_id ?>"
                                data-buy-plan="<?php echo $plan_id ?>"
                        >Click
                        </button>
                    </div>
                </div>
			<?php else: ?>
                <div class="payment-btns">
                    <a href="https://itstars.uz/qabul-qildik" class="stm-redirect">
                        <button type="button" class="btn-theme btn btn-outline-primary">Sotib olish
                        </button>
                    </a>

                </div>
			<?php endif; ?>
		<?php else: ?>
            <div class="btn btn-default"
                 data-text="Log in"
                 data-target=".stm-lms-modal-login"
                 data-lms-modal="login"
                 data-buy-plan="<?php echo $plan_id ?>"
            >
                <span>Sotib olish</span>
            </div>
		<?php endif; ?>
    </div>

	<?php
}

add_action( 'template_redirect', 'showid' );
function showid() {
	global $wp_query;
	$theid = intval( $wp_query->queried_object->ID );
	if ( in_array( $theid, [ 9308, 9613 ] ) ) {
		if ( ! empty( get_current_user_id() ) ) {
			$tries = get_user_meta( get_current_user_id(), 'tries', true );
			$tries = (int) $tries + 1;
			update_user_meta( get_current_user_id(), 'tries', $tries );
		}
	}
}


add_action( 'wp_ajax_discount_code_ajax', 'discount_code_ajax' );
add_action( 'wp_ajax_nopriv_discount_code_ajax', 'discount_code_ajax' );

function discount_code_ajax() {
	check_ajax_referer( 'stm_lms_add_to_cart', 'nonce' );
	$r             = array();

	$course_id     = $_GET['course_id'];
	$discount_code  = $_GET['discount_code'];
	$discount_price = stm_check_discount_code( $course_id );
	$bonus_price = ( ! empty( $course_id ) && ! empty( $discount_code ) ) ? 500000 : 0;

    if ( $bonus_price <= 0 ) {
	    $r['stm_message']  = ' Promo Kod mavjud emas ! ';

    }else{
	    $r['stm_message']  = ' Promo Kod qollandi ! ';
    }
	$r['discount_price'] = $discount_price;
	wp_send_json( $r );

}

function stm_check_discount_code( $course_id ) {

	$course_price = ( ! empty( $course_id ) ) ? STM_LMS_Course::get_course_price( $course_id ) : 0;

	return $course_price . ' so\'m';
}
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
add_filter( 'stm_lms_course_price', function ( $price ) {

	$discount_code  = $_GET['discount_code'];
	$course_id      = $_GET['course_id'];
	$discount_price = ( ! empty( $course_id ) && ! empty( $discount_code ) ) ? 500000 : 0;

	return ( $discount_price > 0 ) ? abs( $price - $discount_price ) : $price;
} );


add_filter('stm_lms_get_course_price_in_meta', 'stm_lms_get_course_price_in_meta', 20 ,2);


function stm_lms_get_course_price_in_meta( $price, $course_meta ) {
	$discount_code  = $_GET['discount_code'];
	$course_id      = $_GET['item_id'];
	$discount_price = ( ! empty( $course_id ) && ! empty( $discount_code ) ) ? 500000 : 0;

	return ( $discount_price > 0 ) ? abs( $price - $discount_price ) : $price;
}


