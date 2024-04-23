<?php
stm_lms_register_style('cart');
stm_lms_register_style('user-orders');
wp_enqueue_style('boostrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', NULL, STM_THEME_VERSION, 'all');
wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), STM_THEME_VERSION, TRUE);
$user_id = get_current_user_id();
$balance = (get_user_meta($user_id, 'lms_balance', true)) ? get_user_meta($user_id, 'lms_balance', true) : 0;
$payments = (get_user_meta($user_id, 'lms_payments', true)) ? get_user_meta($user_id, 'lms_payments', true) : ['0' => ''];
$debt = (get_user_meta($user_id, 'lms_debt', true)) ? get_user_meta($user_id, 'lms_debt', true) : 0;
?>

<?php STM_LMS_Templates::show_lms_template('account/private/parts/top_info', array(
	'title' => esc_html__('Mening To\'lovlarim', 'masterstudy-lms-learning-management-system')
)); ?>

<div id="my-payments">

    <div class="container">
        <div class="row">
            <div class="col-lg-2"><h3>Balance:</h3></div>
            <div class="col-lg-3"><h3><?php echo $balance ?> So'm</h3></div>
        </div>
        <div class="row">
            <div class="col-lg-2"><h3>Qarzdorlik:</h3></div>
            <div class="col-lg-3"><h3><?php echo $debt ?> So'm</h3></div>
        </div>

		<?php $i = 1;
		if (!empty($payments['tolov'])):?>
            <h3>To'lovlar</h3>
			<?php
			foreach ($payments['tolov'] as $k => $payment): ?>
                <div class="row">
                    <div class="col-lg-2"><h3><?php echo $i ?>. <?php echo $payment ?> So'm</h3></div>
                    <div class="col-lg-3">
                        <h3>Sanasi: <?php
							if (!empty($payments['tolov_date'][$k])) {
								$date = new DateTime($payments['tolov_date'][$k]);
								echo $date->format('d-m-Y');
							}
							?></h3></div>
                </div>
				<?php
				$i++;
			endforeach;
		else:
			?>
            <h3><?php esc_html_e('Tolov yo\'q.', 'masterstudy-lms-learning-management-system'); ?></h3>
		<?php endif; ?>
    </div>
</div>