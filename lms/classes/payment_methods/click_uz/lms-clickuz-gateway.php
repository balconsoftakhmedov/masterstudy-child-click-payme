<?php
/*
 * Plugin Name: Woocommerce CLICK Payment Method
 * Plugin URI: https://click.uz
 * Description: CLICK Payment Method Plugin for WooCommerce
 * Version: 1.0.0
 * Author: OOO "Click"
 * Author URI: https://click.uz

 * Text Domain: clickuz
 * Domain Path: /i18n/languages/

 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('CLICK_VERSION1', '1.0.4');

define('CLICK_LOGO1', plugin_dir_url(__FILE__) . 'click-logo.png');

define('CLICK_DELIMITER1', '|');

class LMS_ClickUz
{
	public $plugin;

	public function __construct()
	{

		$this->plugin = plugin_basename(__FILE__);

		add_action('plugins_loaded', array($this, 'init'));

		$this->install();
		$this->init();
	}

	public function init()
	{
		require_once 'include/class-lms-gateway-clickuz.php';
		require_once 'include/class-lms-gateway-clickuz-handlers.php';
		new STM_LMS_Gateway_Clickuz();
		new STM_LMS_ClickAPI();
	}

	public function install()
	{
		global $wpdb;

		$wpdb->hide_errors();

		$collate = '';

		if ($wpdb->has_cap('collation')) {
			if (!empty($wpdb->charset)) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if (!empty($wpdb->collate)) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		dbDelta("
				CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}lms_click_transactions` (
					`ID` BIGINT(20)	UNSIGNED NOT NULL AUTO_INCREMENT,			
                    `click_trans_id` BIGINT(20) UNSIGNED NOT NULL,
                    `service_id` BIGINT(20) UNSIGNED NOT NULL,
                    `click_paydoc_id` BIGINT(20) UNSIGNED NOT NULL,
                    `merchant_trans_id` BIGINT(20) UNSIGNED NOT NULL,                    
                    `amount`  DECIMAL(20, 2) NOT NULL,
                    `error` BIGINT(20) UNSIGNED NOT NULL,
                    `error_note` NVARCHAR(120),
                    `status` NVARCHAR(32),
                    PRIMARY KEY (`ID`)
                ) $collate; ");
	}

}

new LMS_ClickUz();