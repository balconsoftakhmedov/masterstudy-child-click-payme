<?php
/**
 * Click.uz Payment Gateway.
 *
 * Provides a Click.uz Payment Gateway.
 *
 * @class        WC_Gateway_Clickuz
 * @extends        WC_Payment_Gateway
 * @version        1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * WC_Gateway_Clickuz Class.
 */
class STM_LMS_Gateway_Clickuz
{

	/** @var bool Whether or not logging is enabled */
	public static $log_enabled = false;

	/** @var WC_Logger Logger instance */
	public static $log = false;

	/**
	 * Constructor for the gateway.
	 */
	public function __construct()
	{
		$this->id = 'clickuz';
		$this->has_fields = false;
		$this->order_button_text = __('Pay', 'clickuz');
		$this->method_title = 'CLICK';
		$this->method_description = __('Proceed payment with CLICK', 'clickuz');
		$this->supports = array('products',);

		// Load the settings.
		//$this->init_form_fields();
		//$this->init_settings();

		// Define user set variables.
		$this->title = is_admin() ? 'CLICK' : 'Click <img src="' . 'CLICK_LOGO' . '" alt="Click" style="width: auto; height: 30px;" />';
		$this->description = __('Pay with CLICK', 'clickuz');

		$payment_methods = STM_LMS_Options::get_option('payment_methods');
		$testmode = (!empty($payment_methods['click']['fields']['testmode'])) ? $payment_methods['click']['fields']['testmode'] : 'no';
		$this->testmode = ($testmode == 'yes') ? $testmode : 'no';

		$stm_debug = (!empty($payment_methods['click']['fields']['debug'])) ? $payment_methods['click']['fields']['debug'] : 'no';
		$this->debug = ($stm_debug == 'yes') ? $stm_debug : 'no';

		self::$log_enabled = true;
		add_filter('get_click_form', [$this, 'get_click_form'], 10, 5);
	}

	/**
	 * Logging method.
	 *
	 * @param string $message Log message.
	 * @param string $level Optional. Default 'info'.
	 *     emergency|alert|critical|error|warning|notice|info|debug
	 */
	public static function log($message, $level = 'info')
	{
		if (self::$log_enabled) {
			if (empty(self::$log)) {
				self::$log = wc_get_logger();
			}
			self::$log->log($level, $message, array('source' => 'paypal'));
		}
	}


	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields()
	{
		$this->form_fields = include('settings.php');
	}

	/**
	 * Process the payment and return the result.
	 * @param int $order_id
	 * @return array
	 */


	public function process_payment($order_id)
	{
		$order = new WC_Order($order_id);
		//wc_empty_cart();
		return array(
			'result' => 'success',
			'redirect' => add_query_arg(
				'order',
				$order['ID'](),
				add_query_arg('key', $order['order_key'], wc_get_page_permalink('pay'))
			)
		);
		// return [
		//         'result' => 'success',
		//         'redirect' => add_query_arg(
		//             'order_pay',
		//             $order['ID'](),
		//             add_query_arg('key', $order->get_order_key(), $order->get_checkout_payment_url(true))
		//         )
		//     ];
	}

	/**
	 * @param WC_Order $order
	 * @return bool
	 */
	public function can_refund_order($order)
	{
		return $order && get_post_meta($order['ID'], 'transaction_id', true);;
	}

	public function get_click_form($cart_total, $invoice, $cart_total_name, $userEmail, $userPhone)
	{
		$payment_methods = STM_LMS_Options::get_option('payment_methods');

		$this->testmode = ($payment_methods['click']['fields']['testmode'] == 'yes') ? $payment_methods['click']['fields']['testmode'] : 'no';
		$this->debug = ($payment_methods['click']['fields']['debug'] == 'yes') ? $payment_methods['click']['fields']['debug'] : 'no';
		$order = STM_Custom_LMS_Cart::stm_get_order($invoice);
		$cart_total = $order['_order_total'];

		$secret = $payment_methods['click']['fields']['secret_key'];
		$merchantID = $payment_methods['click']['fields']['merchant_id'];
		$merchantUserID = $payment_methods['click']['fields']['merchant_user_id'];
		$merchantServiceID = $payment_methods['click']['fields']['merchant_service_id'];

		$transID = $invoice;
		$transAmount = number_format((int)$cart_total, 0, '.', '');
		$transNote = '';
		$userPhone = preg_replace("/[^0-9,.]/", "", $userPhone);
		$signTime = date("Y-m-d h:i:s");
		$signString = md5($signTime . $secret . $merchantServiceID . $transID . $transAmount);

		$courses = $order['items'];
		foreach ($courses as $course) {
			if (get_post_type($course['item_id']) === 'stm-courses') {
				$returnURL = get_permalink($course['item_id']);;
			}
		}

		$form = <<<FORM
<form id="stm_lms_form_html_processing" action="https://my.click.uz/services/pay" id="click_form" method="get">
<input type="hidden" name="amount" value="{$transAmount}" class="click_input"
id="click_amount_field"/>
<input type="hidden" name="merchant_id" value="{$merchantID}"/>
<input type="hidden" name="merchant_user_id" value="{$merchantUserID}"/>
<input type="hidden" name="service_id" value="{$merchantServiceID}"/>
<input type="hidden" name="transaction_param" value="{$transID}"/>
<input type="hidden" name="MERCHANT_TRANS_NOTE" value="{$transNote}"/>
<input type="hidden" name="MERCHANT_USER_PHONE" value="{$userPhone}"/>
<input type="hidden" name="MERCHANT_USER_EMAIL" value="{$userEmail}"/>
<input type="hidden" name="card_type" value="uzcard"/>
<input type="hidden" name="SIGN_TIME" value="{$signTime}"/>
<input type="hidden" name="SIGN_STRING" value="{$signString}"/>
<input type="hidden" name="return_url" value="{$returnURL}"/>
</form>
FORM;

//<a class="button cancel" href="{$order->get_cancel_order_url()}">$label_cancel</a>

		return $form;
	}
}

new STM_LMS_Gateway_Clickuz;
