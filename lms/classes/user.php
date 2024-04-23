<?php

STM_LMS_User_Child::init();

class STM_LMS_User_Child
{

    public static function init()
    {

        add_filter('stm_lms_float_menu_items', 'STM_LMS_User_Child::stm_lms_float_menu_items', 20, 4);
		add_filter('stm_lms_custom_routes_config', 'STM_LMS_User_Child::stm_lms_custom_routes_config');

    }

    public static function stm_lms_float_menu_items($menus, $current_user, $lms_template_current, $object_id)
    {

        $menus[] = array(
            'order' => 250,
            'current_user' => $current_user,
            'lms_template_current' => $lms_template_current,
            'lms_template' => 'stm-lms-user-payments',
            'menu_title' => esc_html__('To\'lovlar', 'masterstudy-child'),
            'menu_icon' => 'fa-shopping-basket',
            'menu_url' => STM_LMS_User_Child::url(),
        );

           $menus[] = array(
            'order' => 350,
            'current_user' => $current_user,
            'lms_template_current' => $lms_template_current,
            'lms_template' => 'stm-lms-user-reports',
            'menu_title' => esc_html__('Xisobotlar', 'masterstudy-child'),
            'menu_icon' => 'fa-shopping-basket',
            'menu_url' => STM_LMS_User_Child::report_url(),
        );

        return $menus;
    }

	 public static function stm_lms_custom_routes_config($routes)
	 {
		 $routes['user_url']['sub_pages']['my_payments_url'] = array(
			 'template' => 'stm-lms-user-payments',
			 'protected' => true,
			 'url' => 'my-payments',
		 );

		 $routes['user_url']['sub_pages']['my_reports_url'] = array(
			 'template' => 'stm-lms-user-reports',
			 'protected' => true,
			 'url' => 'my-reports',
		 );
		 return $routes;
	 }
	 public static function url()
    {
        $pages_config = STM_LMS_Page_Router::pages_config();

        return STM_LMS_User::login_page_url() . $pages_config['user_url']['sub_pages']['my_payments_url']['url'];
    }

    public static function report_url()
    {
        $pages_config = STM_LMS_Page_Router::pages_config();

        return STM_LMS_User::login_page_url() . $pages_config['user_url']['sub_pages']['my_reports_url']['url'];
    }
}