<?php
/*
Plugin Name: Payme
Plugin URI:  http://paycom.uz
Description: Payme Checkout Plugin for WooCommerce
Version: 1.4.4
Author: richman@mail.ru, support@paycom.uz
Text Domain: payme
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

if (!function_exists('getallheaders')) {
	function getallheaders()
	{
		$headers = [];
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
}

class STM_LMS_PAYME
{
	protected $merchant_id;
	protected $merchant_key;
	protected $checkout_url;

	public function __construct()
	{
		$this->id = 'payme';
		$this->title = 'Payme';
		$this->description = __('Payment system Payme', 'payme');
		$this->has_fields = false;
		$this->init_form_fields();
		$payment_methods = STM_LMS_Options::get_option('payment_methods');
		// Populate options from the saved settings
		$this->merchant_id = $payment_methods['payme']['fields']['merchant_id'];
		$this->merchant_key = $payment_methods['payme']['fields']['merchant_key'];
		$this->checkout_url = $payment_methods['payme']['fields']['checkout_url'];
		$this->return_url = $payment_methods['payme']['fields']['return_url'];
		add_filter('get_payme_form', [$this, 'get_payme_form']);
		//	add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
		//add_action('woocommerce_api_wc_' . $this->id, [$this, 'callback']);
		//$this->callback();
		//add_action('rest_api_init', [$this, 'stm_mra_register_routes']);
		if (!empty($_REQUEST['payment_processing'])) {
			if ($_REQUEST['payment_processing'] == 'payme') $this->callback();
		}
	}


	public function stm_mra_register_routes()
	{
		$routesArr = array(
			array(// App payme
				'route' => '/payme-api/',
				'args' => array(
					'methods' => 'GET',
					'callback' => [$this, 'callback'],
					'permission_callback' => '__return_true'
				)
			)
		);

		foreach ($routesArr as $k => $routeSettings) {
			register_rest_route('payments/v1', $routeSettings['route'], $routeSettings['args']);
		}
	}

	function showMessage($content)
	{
		return '
        <h1>' . $this->msg['title'] . '</h1>
        <div class="box ' . $this->msg['class'] . '-box">' . $this->msg['message'] . '</div>
        ';
	}

	function showTitle($title)
	{
		return false;
	}

	public function init_form_fields()
	{
		$this->form_fields = [
			'enabled' => [
				'title' => __('Enable/Disable', 'payme'),
				'type' => 'checkbox',
				'label' => __('Enabled', 'payme'),
				'default' => 'yes'
			],
			'merchant_id' => [
				'title' => __('Merchant ID', 'payme'),
				'type' => 'text',
				'description' => __('Obtain and set Merchant ID from the Paycom Merchant Cabinet', 'payme'),
				'default' => ''
			],
			'merchant_key' => [
				'title' => __('KEY', 'payme'),
				'type' => 'text',
				'description' => __('Obtain and set KEY from the Paycom Merchant Cabinet', 'payme'),
				'default' => ''
			],
			'checkout_url' => [
				'title' => __('Checkout URL', 'payme'),
				'type' => 'text',
				'description' => __('Set Paycom Checkout URL to submit a payment', 'payme'),
				'default' => 'https://checkout.paycom.uz'
			],
			'return_url' => [
				'title' => __('Return URL', 'payme'),
				'type' => 'text',
				'description' => __('Set Paycom return URL', 'payme'),
				'default' => site_url('/cart/?payme_success=1')
			]
		];
	}

	public function get_payme_form($data)
	{

		// convert an amount to the coins (Payme accepts only coins)
		$sum = $data['cart_total'] * 100;
		$order_id = $data['invoice'];
		// format the amount
		$sum = number_format($sum, 0, '.', '');

		$description = sprintf(__('Payment for Order #%1$s', 'payme'), $order_id);

		$lang_codes = ['ru_RU' => 'ru', 'en_US' => 'en', 'uz_UZ' => 'uz'];
		$lang = isset($lang_codes[get_locale()]) ? $lang_codes[get_locale()] : 'en';

		$label_pay = __('Pay', 'payme');
		$label_cancel = __('Cancel payment and return back', 'payme');
		$order = $this->stm_get_order($order_id);
		$courses = $order['items'];
		foreach ($courses as $course) {
			if (get_post_type($course['item_id']) === 'stm-courses') {
				$callbackUrl = get_permalink($course['item_id']);;
			}
		}

		$order_key = $order_id;

		$form = <<<FORM
<form action="{$this->checkout_url}" method="POST" id="stm_lms_form_html_processing">
<input type="hidden" name="account[order_id]" value="$order_id">
<input type="hidden" name="amount" value="$sum">
<input type="hidden" name="merchant" value="{$this->merchant_id}">
<input type="hidden" name="callback" value="{$callbackUrl}">
<input type="hidden" name="lang" value="uz">
<input type="hidden" name="description" value="Tolov">
<input type="hidden" name="currency" value="860">
</form>
FORM;
//<a class="button cancel" href="{$order->get_cancel_order_url()}">$label_cancel</a>
		return $form;
	}


	public function order_details($order_id)
	{
		// Get an instance of the WC_Order object
		$order = wc_get_order($order_id);

// convert an amount to the coins (Payme accepts only coins)
		$sum = $order->get_total();
// format the amount
		$sum = number_format($sum, 0, '.', '');
		$item_list = '';

		$order_data = $order->get_data(); // The Order data
		$order_status = $order_data['status'];
		$order_currency = $order_data['currency'];
		$order_payment_method_title = $order_data['payment_method_title'];
		$order_date = $order_data['date_created']->date('Y/m/d');
		$shipping = $order->get_shipping_to_display();

## BILLING INFORMATION:

		$order_billing_first_name = $order_data['billing']['first_name'];
		$order_billing_last_name = $order_data['billing']['last_name'];
		$order_billing_address_1 = $order_data['billing']['address_1'];
		$order_billing_state = $order_data['billing']['state'];
		$order_billing_email = $order_data['billing']['email'];
		$order_billing_phone = $order_data['billing']['phone'];
		$order_delivery_time = $order_data['additional']['delivery_time'];

// Iterating through each WC_Order_Item_Product objects
		foreach ($order->get_items() as $item_key => $item):
			## Using WC_Order_Item methods ##
			// Item ID is directly accessible from the $item_key in the foreach loop or
			$item_id = $item->get_id();

			## Using WC_Order_Item_Product methods ##
			$product = $item->get_product(); // Get the WC_Product object
			$product_id = $item->get_product_id(); // the Product id
			$variation_id = $item->get_variation_id(); // the Variation id
			$item_name = $item->get_name(); // Name of the product
			$quantity = $item->get_quantity();

			$tax_class = $item->get_tax_class();
			$line_subtotal = $item->get_subtotal(); // Line subtotal (non discounted)
			$line_subtotal_tax = $item->get_subtotal_tax(); // Line subtotal tax (non discounted)
			$line_total = $item->get_total(); // Line total (discounted)
			$line_total_tax = $item->get_total_tax(); // Line total tax (discounted)


			// Get data from The WC_product object using methods (examples)
			$product = $item->get_product(); // Get the WC_Product object
			$product_url = $product->get_permalink();
			$product_price = $product->get_price();
			$item_list .= '<tr><td><a href = "' . $product_url . '">' . $item_name . '</a> x ' . $quantity . '</td><td>' . $quantity * $product_price . $order_currency . '</td></tr>';
		endforeach;


		$table = <<< TABLE
<div class="order-steps">
		<ul>
			<li class="done">
				<span>1</span>
				<strong>Моя корзина</strong>
			</li>
			<li class="done">
				<span>2</span>
				<strong>Оформление заказа</strong>
			</li>
			<li class="done">
				<span>3</span>
				<strong>Оплата</strong>
			</li>
		</ul>
	</div>
<div class = "row">
    <div class = "col-md-7">
        <h2 class="woocommerce-order-details__title">Информация о заказе</h2><br>
        <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
          <tr>
            <td>НОМЕР ЗАКАЗА:</td>
            <td>$order_id</td>
          </tr>
         <tr>
            <td>ДАТА:</td>
            <td>$order_date</td>
          </tr>
          <tr>
            <td>Товар:</td>
            <td><table style="width:100%">$item_list</table></td>
          </tr>
         <tr>
            <td>Доставка:</td>
            <td>$shipping</td>
          </tr>
           <tr>
            <td>Метод оплаты:</td>
            <td><strong>$order_payment_method_title</strong></td>
          </tr>
           <tr>
            <td>ВСЕГО:</td>
            <td><strong>$sum</strong>$order_currency</td>
          </tr>
        </table>
    </div>
    <div class = "col-md-5">
        <div class = "woocommerce-customer-details">
            <h2 class="woocommerce-column__title">Платёжный адрес</h2><br>
            <address>
                $order_billing_first_name $order_billing_last_name <br>
                 $order_billing_state <br>
                 $order_billing_address_1 <br>
                $order_billing_phone <br>
                $order_billing_email
            </address>    
        </div>
    
    </div>
</div>

TABLE;

		return $table;
	}

	public function process_payment($order_id)
	{
		$order = new WC_Order($order_id);

		return [
			'result' => 'success',
			'redirect' => add_query_arg(
				'order_pay',
				$order->get_id(),
				add_query_arg('key', $order->get_order_key(), $order->get_checkout_payment_url(true))
			)
		];

	}

	/**
	 * Endpoint method. This method handles requests from Paycom.
	 */
	public function callback()
	{
		// Parse payload
		$payload = json_decode(file_get_contents('php://input'), true);
		stm_put_log('paymepayload', $payload);
		if (json_last_error() !== JSON_ERROR_NONE) { // handle Parse error
			$this->respond($this->error_invalid_json());
		}

		// Authorize client
		$headers = getallheaders();

		$v = html_entity_decode($this->merchant_key);
		$encoded_credentials = base64_encode("Paycom:" . $v);
		//$encoded_credentials = base64_encode("Paycom:{$this->merchant_key}");
		if (!$headers || // there is no headers
			!isset($headers['Authorization']) || // there is no Authorization
			!preg_match('/^\s*Basic\s+(\S+)\s*$/i', $headers['Authorization'], $matches) || // invalid Authorization value
			$matches[1] != $encoded_credentials // invalid credentials
		) {
			$this->respond($this->error_authorization($payload));
		}

		// Execute appropriate method
		$response = method_exists($this, $payload['method'])
			? $this->{$payload['method']}($payload)
			: $this->error_unknown_method($payload);

		// Respond with result
		$this->respond($response);
	}

	/**
	 * Responds and terminates request processing.
	 * @param array $response specified response
	 */
	private function respond($response)
	{
		if (!headers_sent()) {
			header('Content-Type: application/json; charset=UTF-8');
		}

		echo json_encode($response);
		die();
	}

	/**
	 * Gets order instance by id.
	 * @param array $payload request payload
	 * @return WC_Order found order by id
	 */
	private function get_order(array $payload)
	{
		$order_id = $payload['params']['account']['order_id'];

		if ($this->stm_get_order($order_id)) {
			$order = (array)$this->stm_get_order($order_id);
		} else {
			$this->respond($this->error_order_id($payload));
		}

		return $order;
	}

	function stm_get_order($order_id)
	{
		$post = get_post($order_id);
		$order = null;
		$order_info = array(
			'user_id',
			'items',
			'date',
			'status',
			'payment_code',
			'order_key',
			'_order_total',
			'_order_currency',
		);
		if ($post->post_type == 'stm-orders') {
			$order = (array)$post;
			foreach ($order_info as $meta_key) {
				$order[$meta_key] = get_post_meta($order_id, $meta_key, true);
			}
		}

		return $order;
	}

	/**
	 * Gets order instance by transaction id.
	 * @param array $payload request payload
	 * @return WC_Order found order by id
	 */
	private function get_order_by_transaction($payload)
	{
		global $wpdb;

		try {
			$prepared_sql = $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_value = '%s' AND meta_key = '_payme_transaction_id'", $payload['params']['id']);
			$order_id = $wpdb->get_var($prepared_sql);
			return $this->stm_get_order($order_id);
		} catch (Exception $ex) {
			$this->respond($this->error_transaction($payload));
		}
	}

	/**
	 * Converts amount to coins.
	 * @param float $amount amount value.
	 * @return int Amount representation in coins.
	 */
	private function amount_to_coin($amount)
	{
		return 100 * number_format($amount, 0, '.', '');
	}

	/**
	 * Gets current timestamp in milliseconds.
	 * @return float current timestamp in ms.
	 */
	private function current_timestamp()
	{
		return round(microtime(true) * 1000);
	}

	/**
	 * Get order's create time.
	 * @param WC_Order $order order
	 * @return float create time as timestamp
	 */
	private function get_create_time($order)
	{
		return (double)get_post_meta($order['ID'], '_payme_create_time', true);
	}

	/**
	 * Get order's perform time.
	 * @param WC_Order $order order
	 * @return float perform time as timestamp
	 */
	private function get_perform_time($order)
	{
		return (double)get_post_meta($order['ID'], '_payme_perform_time', true);
	}

	/**
	 * Get order's cancel time.
	 * @param WC_Order $order order
	 * @return float cancel time as timestamp
	 */
	private function get_cancel_time($order)
	{
		return (double)get_post_meta($order['ID'], '_payme_cancel_time', true);
	}

	/**
	 * Get order's transaction id
	 * @param WC_Order $order order
	 * @return string saved transaction id
	 */
	private function get_transaction_id($order)
	{
		return (string)get_post_meta($order['ID'], '_payme_transaction_id', true);
	}

	private function get_cencel_reason($order)
	{
		$b_v = (int)get_post_meta($order['ID'], '_cancel_reason', true);

		if ($b_v) return $b_v;
		else return null;
	}

	private function CheckPerformTransaction($payload)
	{
		$order = $this->get_order($payload);
		$amount = $this->amount_to_coin($order['_order_total']);

		if ($amount != $payload['params']['amount']) {
			$response = $this->error_amount($payload);
		} else {
			$response = [
				'id' => $payload['id'],
				'result' => [
					'allow' => true
				],
				'error' => null
			];
		}

		return $response;
	}

	private function CreateTransaction($payload)
	{
		$order = $this->get_order($payload);
		$amount = $this->amount_to_coin($order['_order_total']);

		if ($amount != $payload['params']['amount']) {
			$response = $this->error_amount($payload);
		} else {
			$create_time = $this->current_timestamp();
			$transaction_id = $payload['params']['id'];
			$saved_transaction_id = $this->get_transaction_id($order);

			if ($order['status'] == "pending") { // handle new transaction
				// Save time and transaction id
				add_post_meta($order['ID'], '_payme_create_time', $create_time, true);
				add_post_meta($order['ID'], '_payme_transaction_id', $transaction_id, true);

				// Change order's status to Processing
				update_post_meta($order['ID'], 'status', 'processing');
				$response = [
					"id" => $payload['id'],
					"result" => [
						"create_time" => /*$create_time*/
							$this->get_create_time($order),
						"transaction" => "000" . $order['ID'],
						"state" => 1
					]
				];
			} elseif ($order['status'] == "processing" && $transaction_id == $saved_transaction_id) { // handle existing transaction
				$response = [
					"id" => $payload['id'],
					"result" => [
						"create_time" => /*$create_time*/
							$this->get_create_time($order),
						"transaction" => "000" . $order['ID'],
						"state" => 1
					]
				];
			} elseif ($order['status'] == "processing" && $transaction_id !== $saved_transaction_id) { // handle new transaction with the same order
				$response = $this->error_has_another_transaction($payload);
			} else {
				$response = $this->error_unknown($payload);
			}
		}

		return $response;
	}

	private function PerformTransaction($payload)
	{
		$perform_time = $this->current_timestamp();
		$order = $this->get_order_by_transaction($payload);

		STM_Custom_LMS_Cart::stm_lms_order_created($order);

		if ($order['status'] == "processing") { // handle new Perform request
			// Save perform time
			add_post_meta($order['ID'], '_payme_perform_time', $perform_time, true);

			$response = [
				"id" => $payload['id'],
				"result" => [
					"transaction" => "000" . $order['ID'],
					"perform_time" => $this->get_perform_time($order),
					"state" => 2
				]
			];

			// Mark order as completed
			update_post_meta($order['ID'], 'status', 'completed');
			STM_Custom_LMS_Cart::stm_lms_order_created($order);
		} elseif ($order['status'] == "processing") { // handle existing Perform request
			$response = [
				"id" => $payload['id'],
				"result" => [
					"transaction" => "000" . $order['ID'],
					"perform_time" => $this->get_perform_time($order),
					"state" => 2
				]
			];
		} elseif ($order['status'] == "completed") { // handle existing Perform request
			$response = [
				"id" => $payload['id'],
				"result" => [
					"transaction" => "000" . $order['ID'],
					"perform_time" => $this->get_perform_time($order),
					"state" => 2
				]
			];
		} elseif ($order['status'] == "cancelled" || $order['status'] == "refunded") { // handle cancelled order
			$response = $this->error_cancelled_transaction($payload);
		} else {
			$response = $this->error_unknown($payload);
		}

		return $response;
	}

	private function CheckTransaction($payload)
	{
		$transaction_id = $payload['params']['id'];
		$order = $this->get_order_by_transaction($payload);

		// Get transaction id from the order
		$saved_transaction_id = $this->get_transaction_id($order);

		$response = [
			"id" => $payload['id'],
			"result" => [
				"create_time" => $this->get_create_time($order),
				"perform_time" => (is_null($this->get_perform_time($order)) ? 0 : $this->get_perform_time($order)),
				"cancel_time" => (is_null($this->get_cancel_time($order)) ? 0 : $this->get_cancel_time($order)),
				"transaction" => "000" . $order['ID'],
				"state" => null,
				"reason" => (is_null($this->get_cencel_reason($order)) ? null : $this->get_cencel_reason($order))
			],
			"error" => null
		];

		if ($transaction_id == $saved_transaction_id) {

			switch ($order['status']) {

				case 'processing':
					$response['result']['state'] = 1;
					break;
				case 'completed':
					$response['result']['state'] = 2;
					break;
				case 'cancelled':
					$response['result']['state'] = -1;
					break;
				case 'refunded':
					$response['result']['state'] = -2;
					break;

				default:
					$response = $this->error_transaction($payload);
					break;
			}
		} else {
			$response = $this->error_transaction($payload);
		}

		return $response;
	}

	private function CancelTransaction($payload)
	{
		$order = $this->get_order_by_transaction($payload);

		$transaction_id = $payload['params']['id'];
		$saved_transaction_id = $this->get_transaction_id($order);

		if ($transaction_id == $saved_transaction_id) {

			$cancel_time = $this->current_timestamp();

			$response = [
				"id" => $payload['id'],
				"result" => [
					"transaction" => "000" . $order['ID'],
					"cancel_time" => $cancel_time,
					"state" => null
				]
			];

			switch ($order['status']) {
				case 'pending':
					add_post_meta($order['ID'], '_payme_cancel_time', $cancel_time, true); // Save cancel time
					update_post_meta($order['ID'], 'status', 'cancelled'); // Change status to Cancelled
					$response['result']['state'] = -1;

					if (update_post_meta($order['ID'], '_cancel_reason', $payload['params']['reason'])) {
						add_post_meta($order['ID'], '_cancel_reason', $payload['params']['reason'], true);
					}
					break;
				case 'processing':
					add_post_meta($order['ID'], '_payme_cancel_time', $cancel_time, true); // Save cancel time
					update_post_meta($order['ID'], 'status', 'cancelled'); // Change status to Cancelled
					$response['result']['state'] = -1;
					if (update_post_meta($order['ID'], '_cancel_reason', $payload['params']['reason'])) {
						add_post_meta($order['ID'], '_cancel_reason', $payload['params']['reason'], true);
					}
					break;

				case 'completed':
					add_post_meta($order['ID'], '_payme_cancel_time', $cancel_time, true); // Save cancel time
					update_post_meta($order['ID'], 'status', 'refunded'); // Change status to Refunded
					$response['result']['state'] = -2;
					if (update_post_meta($order['ID'], '_cancel_reason', $payload['params']['reason'])) {
						add_post_meta($order['ID'], '_cancel_reason', $payload['params']['reason'], true);
					}
					break;

				case 'cancelled':
					$response['result']['cancel_time'] = $this->get_cancel_time($order);
					$response['result']['state'] = -1;
					break;

				case 'refunded':
					$response['result']['cancel_time'] = $this->get_cancel_time($order);
					$response['result']['state'] = -2;
					break;

				default:
					$response = $this->error_cancel($payload);
					break;
			}
		} else {
			$response = $this->error_transaction($payload);
		}

		return $response;
	}

	private function ChangePassword($payload)
	{
		if ($payload['params']['password'] != $this->merchant_key) {
			$woo_options = get_option('woocommerce_payme_settings');

			if (!$woo_options) { // No options found
				return $this->error_password($payload);
			}

			// Save new password
			$woo_options['merchant_key'] = $payload['params']['password'];
			$is_success = update_option('woocommerce_payme_settings', $woo_options);

			if (!$is_success) { // Couldn't save new password
				return $this->error_password($payload);
			}

			return [
				"id" => $payload['id'],
				"result" => ["success" => true],
				"error" => null
			];
		}

		// Same password or something wrong
		return $this->error_password($payload);
	}

	private function error_password($payload)
	{
		$response = [
			"error" => [
				"code" => -32400,
				"message" => [
					"ru" => __('Cannot change the password', 'payme'),
					"uz" => __('Cannot change the password', 'payme'),
					"en" => __('Cannot change the password', 'payme')
				],
				"data" => "password"
			],
			"result" => null,
			"id" => $payload['id']
		];

		return $response;
	}

	private function error_invalid_json()
	{
		$response = [
			"error" => [
				"code" => -32700,
				"message" => [
					"ru" => __('Could not parse JSON', 'payme'),
					"uz" => __('Could not parse JSON', 'payme'),
					"en" => __('Could not parse JSON', 'payme')
				],
				"data" => null
			],
			"result" => null,
			"id" => 0
		];

		return $response;
	}

	private function error_order_id($payload)
	{
		$response = [
			"error" => [
				"code" => -31099,
				"message" => [
					"ru" => __('Order number cannot be found', 'payme'),
					"uz" => __('Order number cannot be found', 'payme'),
					"en" => __('Order number cannot be found', 'payme')
				],
				"data" => "order"
			],
			"result" => null,
			"id" => $payload['id']
		];

		return $response;
	}

	private function error_has_another_transaction($payload)
	{
		$response = [
			"error" => [
				"code" => -31099,
				"message" => [
					"ru" => __('Other transaction for this order is in progress', 'payme'),
					"uz" => __('Other transaction for this order is in progress', 'payme'),
					"en" => __('Other transaction for this order is in progress', 'payme')
				],
				"data" => "order"
			],
			"result" => null,
			"id" => $payload['id']
		];

		return $response;
	}

	private function error_amount($payload)
	{
		$response = [
			"error" => [
				"code" => -31001,
				"message" => [
					"ru" => __('Order amount is incorrect', 'payme'),
					"uz" => __('Order amount is incorrect', 'payme'),
					"en" => __('Order amount is incorrect', 'payme')
				],
				"data" => "amount"
			],
			"result" => null,
			"id" => $payload['id']
		];

		return $response;
	}

	private function error_unknown($payload)
	{
		$response = [
			"error" => [
				"code" => -31008,
				"message" => [
					"ru" => __('Unknown error', 'payme'),
					"uz" => __('Unknown error', 'payme'),
					"en" => __('Unknown error', 'payme')
				],
				"data" => null
			],
			"result" => null,
			"id" => $payload['id']
		];

		return $response;
	}

	private function error_unknown_method($payload)
	{
		$response = [
			"error" => [
				"code" => -32601,
				"message" => [
					"ru" => __('Unknown method', 'payme'),
					"uz" => __('Unknown method', 'payme'),
					"en" => __('Unknown method', 'payme')
				],
				"data" => $payload['method']
			],
			"result" => null,
			"id" => $payload['id']
		];

		return $response;
	}

	private function error_transaction($payload)
	{
		$response = [
			"error" => [
				"code" => -31003,
				"message" => [
					"ru" => __('Transaction number is wrong', 'payme'),
					"uz" => __('Transaction number is wrong', 'payme'),
					"en" => __('Transaction number is wrong', 'payme')
				],
				"data" => "id"
			],
			"result" => null,
			"id" => $payload['id']
		];

		return $response;
	}

	private function error_cancelled_transaction($payload)
	{
		$response = [
			"error" => [
				"code" => -31008,
				"message" => [
					"ru" => __('Transaction was cancelled or refunded', 'payme'),
					"uz" => __('Transaction was cancelled or refunded', 'payme'),
					"en" => __('Transaction was cancelled or refunded', 'payme')
				],
				"data" => "order"
			],
			"result" => null,
			"id" => $payload['id']
		];

		return $response;
	}

	private function error_cancel($payload)
	{
		$response = [
			"error" => [
				"code" => -31007,
				"message" => [
					"ru" => __('It is impossible to cancel. The order is completed', 'payme'),
					"uz" => __('It is impossible to cancel. The order is completed', 'payme'),
					"en" => __('It is impossible to cancel. The order is completed', 'payme')
				],
				"data" => "order"
			],
			"result" => null,
			"id" => $payload['id']
		];

		return $response;
	}

	private function error_authorization($payload)
	{
		$response = [
			"error" =>
				[
					"code" => -32504,
					"message" => [
						"ru" => __('Error during authorization', 'payme'),
						"uz" => __('Error during authorization', 'payme'),
						"en" => __('Error during authorization', 'payme')
					],
					"data" => null
				],
			"result" => null,
			"id" => $payload['id']
		];

		return $response;
	}
}

new STM_LMS_PAYME;


/////////////// success page

add_filter('query_vars', 'stm_lms_payme_success_query_vars');
function stm_lms_payme_success_query_vars($query_vars)
{
	$query_vars[] = 'payme_success';
	$query_vars[] = 'order_id';
	return $query_vars;
}


add_action('parse_request', 'stm_lms_payme_success_parse_request');
function stm_lms_payme_success_parse_request(&$wp)
{
	if (array_key_exists('payme_success', $wp->query_vars)) {

		$order = new WC_Order($wp->query_vars['order_id']);

		$a = new WC_PAYME();
		add_action('the_title', array($a, 'showTitle'));
		add_action('the_content', array($a, 'showMessage'));

		if ($wp->query_vars['payme_success'] == 1) {

			if ($order['status'] == "pending") {
				/*
				$a->msg['title']   =  __('Payment not paid', 'payme');
				$a->msg['message'] =  __('An error occurred during payment. Try again or contact your administrator.', 'payme');
				$a->msg['class']   = 'woocommerce_message woocommerce_message_info';
				*/
				wp_redirect($order->get_cancel_order_url());
			} else {
				$a->msg['title'] = __('Payment successfully paid', 'payme');
				$a->msg['message'] = __('Thank you for your purchase!', 'payme');
				$a->msg['class'] = 'woocommerce_message woocommerce_message_info';
				//	WC()->cart->empty_cart();
			}

		} else {
			$a->msg['title'] = __('Payment not paid', 'payme');
			$a->msg['message'] = __('An error occurred during payment. Try again or contact your administrator.', 'payme');
			$a->msg['class'] = 'woocommerce_message woocommerce_message_info';
		}
	}
	return;
}

/////////////// success page end

?>