<?php
STM_Custom_LMS_Cart::init();

class STM_Custom_LMS_Cart extends STM_LMS_Cart
{
	public static function init()
	{
		add_filter('stm-lms-payment-methods', 'STM_Custom_LMS_Cart::stm_lms_payment_methods');
		add_filter('wpcfto_field_payments', 'STM_Custom_LMS_Cart::stm_lms_settings_payment_methods');
		remove_action('wp_ajax_stm_lms_add_to_cart', 'STM_LMS_Cart::add_to_cart');
		remove_action('wp_ajax_nopriv_stm_lms_add_to_cart', 'STM_LMS_Cart::add_to_cart');
		add_action('wp_ajax_stm_lms_add_to_cart', 'STM_Custom_LMS_Cart::stm_add_to_cart');
		add_action('wp_ajax_nopriv_stm_lms_add_to_cart', 'STM_Custom_LMS_Cart::stm_add_to_cart');
		add_action('order_created', 'STM_Custom_LMS_Cart::add_plan_id_to_order',20, 4);
	}

	public static function add_plan_id_to_order($user_id, $cart_items, $payment_code, $invoice)
	{
		$plan_id = intval($_GET['plan_id']);
		 update_post_meta($invoice, 'plan_id', $plan_id);
	}
	public static function stm_lms_payment_methods($payment_methods)
	{
		$payment_methods['payme'] = esc_html__('Payme', 'masterstudy-child');
		$payment_methods['click'] = esc_html__('Click', 'masterstudy-child');
		return $payment_methods;
	}

	public static function stm_lms_settings_payment_methods($template)
	{
		return get_stylesheet_directory() . '/settings/payments/fields/payments.php';
	}

	public static function stm_add_to_cart()
	{

		check_ajax_referer('stm_lms_add_to_cart', 'nonce');
		if (!is_user_logged_in() or empty($_GET['item_id'])) die;
		$item_id = intval($_GET['item_id']);
		$plan_id = intval($_GET['plan_id']);
		$user = STM_LMS_User::get_current_user();
		$user_id = $user['id'];
		stm_lms_get_delete_cart_items($user_id);
		$r = STM_Custom_LMS_Cart::_add_to_cart($item_id, $user_id, $plan_id);
		$payment_method = (!empty($_REQUEST['payment_code'])) ? sanitize_text_field($_REQUEST['payment_code']) : false;
		if ($payment_method) {
			$r = self::payment_processing($payment_method);
		}
		$r['redirect'] = true;
		//$r['text'] = __('To\'lovga o\'tish', 'masterstudy-child');
		//	print_r($r);
		wp_send_json($r);
	}

	public static function payment_processing()
	{

		$user = STM_LMS_User::get_current_user();
		if (empty($user['id'])) die;
		$user_id = $user['id'];
		$payment_code = (!empty($_GET['payment_code'])) ? sanitize_text_field($_GET['payment_code']) : '';
		$plan_id = intval($_GET['plan_id']);
		$r = array(
			'status' => 'success'
		);
		if (empty($payment_code)) {
			$r = array(
				'status' => 'error',
				'message' => esc_html__('Tolov turini tanlang', 'masterstudy-lms-learning-management-system')
			);
			return $r;
		}
		$cart_items = stm_lms_get_cart_items($user_id, apply_filters('stm_lms_cart_items_fields', array('item_id', 'price')));
		$cart_total = STM_LMS_Cart::get_cart_totals($cart_items);
		$symbol = STM_LMS_Options::get_option('currency_symbol', 'none');
		/*Create ORDER*/
		$invoice = STM_LMS_Order::create_order([
			"user_id" => $user_id,
			"cart_items" => $cart_items,
			"payment_code" => $payment_code,
			"_order_total" => $cart_total['total'],
			"_order_currency" => $symbol,
			"plan_id"=>$plan_id
		], true);

		do_action('order_created', $user_id, $cart_items, $payment_code, $invoice);
		/*If Paypal*/
		if ($payment_code == 'paypal') {
			$r = self::paypal_payment($cart_total, $invoice, $user_id, $user);
		} else if ($payment_code == 'stripe') {
			$r = self::stripe_payment($cart_total, $invoice, $user_id);
		} else
			if ($payment_code == 'payme') {
				$r = self::payme_payment($cart_total, $invoice, $user_id, $user);
			} else
				if ($payment_code == 'click') {
					$r = self::click_payment($cart_total, $invoice, $user_id, $user);
				} else {
					$r['message'] = esc_html__('Buyurtma Yaratildi', 'masterstudy-lms-learning-management-system');
					$r['url'] = STM_LMS_User::user_page_url($user_id);
				}
		$r['cart_url'] = $r['url'];
		$r['text'] = $r['message'];
		return $r;
	}

	static function _add_to_cart($item_id, $user_id, $plan_id=null)
	{

		$r = array();
		$not_salebale = get_post_meta($item_id, 'not_single_sale', true);
		if ($not_salebale) die;
		$item_meta = STM_LMS_Helpers::parse_meta_field($item_id);
		$quantity = 1;
		$prices_by_plan[1] = 1500000;
		$prices_by_plan[2] = 1900000;
		$item_meta['price']=(!empty($prices_by_plan[$plan_id]))?$prices_by_plan[$plan_id]:$item_meta['price'];
		$price = STM_Custom_LMS_Cart::get_course_price($item_meta, $plan_id);

		$is_woocommerce = STM_LMS_Cart::woocommerce_checkout_enabled();
		$item_added = count(stm_lms_get_item_in_cart($user_id, $item_id, array('user_cart_id')));
		if (!$item_added) {
			stm_lms_add_user_cart(compact('user_id', 'item_id', 'quantity', 'price'));
		}
		if (!$is_woocommerce) {
			$r['text'] = esc_html__('Go to Cart', 'masterstudy-lms-learning-management-system');
			$r['cart_url'] = esc_url(STM_LMS_Cart::checkout_url());
		} else {
			$r['added'] = STM_LMS_Woocommerce::add_to_cart($item_id);
			$r['text'] = esc_html__('Go to Cart', 'masterstudy-lms-learning-management-system');
			$r['cart_url'] = esc_url(wc_get_cart_url());
		}
		$r['redirect'] = STM_LMS_Options::get_option('redirect_after_purchase', false);
		return apply_filters('stm_lms_add_to_cart_r', $r, $item_id);
	}

	public static function get_course_price($course_meta, $plan_id = null)
    {
        $price = 0;
        if (!empty($course_meta['price'])) $price = $course_meta['price'];
        if (!empty($course_meta['sale_price'])) {
            $price = apply_filters('stm_lms_sale_price_meta', $course_meta['sale_price'], $course_meta, $price);
        }
        return apply_filters('stm_lms_get_course_price_in_meta', $price, $course_meta);
    }

	public static function paypal_payment($cart_total, $invoice, $user_id, $user)
	{
		$paypal = new STM_LMS_PayPal(
			$cart_total['total'],
			$invoice,
			$cart_total['item_name'],
			$invoice,
			$user['email']
		);
		$r['url'] = $paypal->generate_payment_url();
		$r['message'] = esc_html__("Tolovga otish", 'masterstudy-child');
		return $r;
	}

	public static function stripe_payment($cart_total, $invoice, $user_id)
	{
		if (!empty($_GET['token_id'])) {
			$url = 'https://api.stripe.com/v1/charges';
			$payment = STM_LMS_Options::get_option('payment_methods');
			if (empty($payment['stripe'])
				or empty($payment['stripe']['enabled'])
				or empty($payment['stripe']['fields'])
				or empty($payment['stripe']['fields']['secret_key'])
			) die;
			$sk_key = $payment['stripe']['fields']['secret_key'];
			$headers = array(
				'Authorization' => 'Bearer ' . $sk_key,
			);
			$currency = (!empty($payment['stripe']['fields']['currency'])) ? $payment['stripe']['fields']['currency'] : 'usd';
			$increment = apply_filters('masterstudy_payment_increment', 100);
			$args = array(
				'source' => $_GET['token_id'],
				'amount' => floatval($cart_total['total']) * $increment,
				'description' => sprintf(esc_html__('%s. Order key: %s', 'masterstudy-lms-learning-management-system'), $cart_total['item_name'], get_the_title($invoice)),
				'currency' => $currency,
			);
			$req = wp_remote_post($url, array('headers' => $headers, 'body' => $args));
			$req = wp_remote_retrieve_body($req);
			$req = json_decode($req, true);
			/*Check if paid*/
			$r['message'] = esc_html__('Tolovga otish', 'masterstudy-lms-learning-management-system');
			if (!empty($req['paid']) and !empty($req['amount']) and $req['amount'] == $cart_total['total'] * $increment) {
				update_post_meta($invoice, 'status', 'completed');
				STM_LMS_Order::accept_order($user_id, $invoice);
				$r['message'] = esc_html__('Order created. Payment completed.', 'masterstudy-lms-learning-management-system');
			} else {
				wp_delete_post($invoice, true);
				$r['status'] = 'error';
				$r['message'] = esc_html__('Error occurred. Please try again.', 'masterstudy-lms-learning-management-system');
				$r['url'] = false;
			}
			$r['url'] = STM_LMS_User::user_page_url($user_id);
			$r['order'] = $req;
		} else {
			$r = array(
				'status' => 'error',
				'message' => esc_html__('Please, select payment method', 'masterstudy-lms-learning-management-system')
			);
		}
		return $r;
	}

	public static function payme_payment($cart_total, $invoice, $user_id, $user)
	{
		$data = [
			'cart_total' => $cart_total['total'],
			'invoice' => $invoice,
			'cart_total_name' => $cart_total['item_name'],
			'user_email' => $user['email']
		];
		$form = apply_filters('get_payme_form', $data);
		$r['form_html'] = $form;
		$r['message'] = esc_html__("Tolovga otish", 'masterstudy-child');
		return $r;
	}

	public static function click_payment($cart_total, $invoice, $user_id, $user)
	{
		$data = [
			'cart_total' => $cart_total['total'],
			'invoiceww' => $invoice,
			'cart_total_name' => $cart_total['item_name'],
			'user_email' => $user['email'],
			'phone' => $user['phone']
		];
		$form = apply_filters('get_click_form', $cart_total['total'], $invoice, $cart_total['item_name'], $user['email'], $user['phone']);
		$r['form_html'] = $form;
		$r['message'] = esc_html__("Tolovga otish", 'masterstudy-child');
		return $r;
	}

	public static function stm_lms_order_created($order)
	{

		$user_id = $order['post_author'];
		$courses = $order['items'];
		foreach ($courses as $course) {
			if (get_post_type($course['item_id']) === 'stm-courses') {
				if (empty($course['enterprise_id'])) {
					STM_LMS_Course::add_user_course($course['item_id'], $user_id, 0, 0);
					STM_LMS_Course::add_student($course['item_id']);
				}
			}
		}

	}

	public static function stm_get_order($order_id)
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
			'_is_paid'
		);
		if ($post->post_type == 'stm-orders') {
			$order = (array)$post;
			foreach ($order_info as $meta_key) {
				$order[$meta_key] = get_post_meta($order_id, $meta_key, true);
			}
		}
		return $order;
	}


}