<?php
/**
 * @var $course_id
 * @var $item_id
 */
stm_lms_register_script( 'buy-button' );
stm_lms_register_style( 'buy-button-mixed' );
$item_id      = ( ! empty( $item_id ) ) ? $item_id : '';
$has_course   = STM_LMS_User::has_course_access( $course_id, $item_id, false );
$course_price = STM_LMS_Course::get_course_price( $course_id );
if ( isset( $has_access ) ) {
	$has_course = $has_access;
}
$is_prerequisite_passed = true;
if ( class_exists( 'STM_LMS_Prerequisites' ) ) {
	$is_prerequisite_passed = STM_LMS_Prerequisites::is_prerequisite( true, $course_id );
}
do_action( 'stm_lms_before_button_mixed', $course_id );
if ( apply_filters( 'stm_lms_before_button_stop', false, $course_id ) ) {
	return false;
}
$is_affiliate = STM_LMS_Course_Pro::is_external_course( $course_id );
$not_salebale = get_post_meta( $course_id, 'not_single_sale', true );
if ( ! $is_affiliate ):
	?>

    <div class="stm-lms-buy-buttons stm-lms-buy-buttons-mixed stm-lms-buy-buttons-mixed-pro sssss dssssssssss">
		<?php if ( ( $has_course or ( empty( $course_price ) and ! $not_salebale ) ) and $is_prerequisite_passed ): ?>

			<?php $user         = STM_LMS_User::get_current_user();
			if ( empty( $user['id'] ) ) : ?>
				<?php
				stm_lms_register_style( 'login' );
				stm_lms_register_style( 'register' );
				enqueue_login_script();
				enqueue_register_script();
				?>

                <a href="#"
                   class="btn btn-default"
                   data-target=".stm-lms-modal-login"
                   data-lms-modal="login">
                    <span><?php esc_html_e( 'A\'zo bo\'lish', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
                </a>
			<?php else:
				$user_id = $user['id'];
				$course         = STM_LMS_Helpers::simplify_db_array( stm_lms_get_user_course( $user_id, $course_id, array(
					'current_lesson_id',
					'progress_percent'
				) ) );
				$current_lesson = ( ! empty( $course['current_lesson_id'] ) ) ? $course['current_lesson_id'] : '0';
				$progress       = ( ! empty( $course['progress_percent'] ) ) ? intval( $course['progress_percent'] ) : 0;
				$lesson_url     = STM_LMS_Course::item_url( $course_id, $current_lesson );
				$btn_label      = esc_html__( 'Boshlash', 'masterstudy-lms-learning-management-system-pro' );
				if ( $progress > 0 ) {
					$btn_label = esc_html__( 'Davom etish', 'masterstudy-lms-learning-management-system-pro' );
				}
				?>
                <a href="<?php echo esc_url( $lesson_url ); ?>" class="btn btn-default start-course">
                    <span><?php echo sanitize_text_field( $btn_label ); ?></span>
                </a>

			<?php endif; ?>

		<?php else:
			$price = get_post_meta( $course_id, 'price', true );

			$sale_price        = STM_LMS_Course::get_sale_price( $course_id );
			$not_in_membership = get_post_meta( $course_id, 'not_membership', true );
			$btn_class         = array( 'btn btn-default' );
			if ( empty( $price ) and ! empty( $sale_price ) ) {
				$price      = $sale_price;
				$sale_price = '';
			}
			if ( ! empty( $price ) and ! empty( $sale_price ) ) {
				$tmp_price  = $sale_price;
				$sale_price = $price;
				$price      = $tmp_price;
			}
			if ( $not_salebale ) {
				$price = $sale_price = '';
			}
			$btn_class[] = 'btn_big heading_font';
			if ( is_user_logged_in() ) {
				$attributes = array();
				if ( ! $not_salebale ) {
					$attributes[] = 'data-buy-course="' . intval( $course_id ) . '"';
				}
			} else {
				stm_lms_register_style( 'login' );
				stm_lms_register_style( 'register' );
				enqueue_login_script();
				enqueue_register_script();
				$attributes = array(
					'data-target=".stm-lms-modal-login"',
					'data-lms-modal="login"'
				);
			}
			$subscription_enabled = ( empty( $not_in_membership ) and STM_LMS_Subscriptions::subscription_enabled() );
			$dropdown_enabled     = $subscription_enabled;
			if ( ! $subscription_enabled ) {
				$dropdown_enabled = is_user_logged_in() && class_exists( 'STM_LMS_Point_System' );
			}
			$mixed_classes   = array( 'stm_lms_mixed_button' );
			$mixed_classes[] = ( $dropdown_enabled ) ? 'subscription_enabled' : 'subscription_disabled';
			?>

			<?php if ( $show_buttons = apply_filters( 'stm_lms_pro_show_button', true, $course_id ) ): ?>

            <div class="<?php echo implode( ' ', $mixed_classes ) ?>">

				<?php /* ?>
                <div class="kkkkkkkk buy-button <?php echo esc_attr(implode(' ', $btn_class)); ?>" <?php if (!$dropdown_enabled) echo implode(' ', apply_filters('stm_lms_buy_button_auth', $attributes, $course_id)); ?>>

                    <span>
                        <?php esc_html_e('Sotib olish', 'masterstudy-lms-learning-management-system-pro'); ?>
                    </span>

                </div>
                <?php */
				?>


				<?php if ( is_user_logged_in() ) : ?>


					<?php
					$payment_methods = STM_LMS_Options::get_option( 'payment_methods' );
					$user_id         = get_current_user_id();
					$oferta          = ( get_user_meta( $user_id, 'accept', true ) ) ? get_user_meta( $user_id, 'accept', true ) : false;
					if ( ! empty( $payment_methods && $oferta == 'yes' ) ) :
						$payment_method_names = STM_LMS_Cart::payment_methods();
						?>
                        <div class="stm_lms_form_html"></div>

                        <div class="stm-lms-buy-buttons stm-lms-buy-buttons-mixed stm-lms-buy-buttons-mixed-pro" <?php if ( ! $dropdown_enabled ) {
							echo implode( ' ', apply_filters( 'stm_lms_buy_button_auth', $attributes, $course_id ) );
						} ?>>

                            <div class="stm-lms-buy-buttons stm-lms-buy-buttons-mixed stm-lms-buy-buttons-mixed-pro">
                                <div class="container">
                                    <div class="form-group">
                                        <div class="btn btn-default"
                                             id="redirect_price_list"
                                             data-href="<?php echo site_url() . '/upwork-price-list/' ?>"
                                        <span>Sotib olish</span>
                                    </div>
                                </div>

                            </div>
                        </div>

						<?php
						//  $payment_methods = [];

						foreach ( $payment_methods as $payment_method_code => $payment_method ):
							if ( ! empty( $payment_method['enabled'] ) ):
								?>

                                <div data-payment-method="<?php echo $payment_method_code ?>"
                                     class="stm-lms-buy-buttons stm-lms-buy-buttons-mixed stm-lms-buy-buttons-mixed-pro" <?php if ( ! $dropdown_enabled ) {
									echo implode( ' ', apply_filters( 'stm_lms_buy_button_auth', $attributes, $course_id ) );
								} ?>>
                                    <div class="container">
                                        <div class="form-group">
                                            <div class="btn btn-default <?php echo $payment_method_code ?>-payment">
                                                <span><?php echo $payment_method_names[ $payment_method_code ] ?></span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
							<?php
							else:
							endif;
						endforeach; ?>


					<?php
					else:
						?>
                        <div class="stm-lms-buy-buttons stm-lms-buy-buttons-mixed stm-lms-buy-buttons-mixed-pro">
                            <a href="https://itstars.uz/contact-us/">
                                <div class="form-group">
                                    <div class="btn btn-default stm-advise">
                                        <span>Maslaxat olish</span>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!--
        <div class="stm-lms-buy-buttons stm-lms-buy-buttons-mixed stm-lms-buy-buttons-mixed-pro">
                <a href="<?php echo site_url() . '/upwork-price-list/' ?>">
                    <div class="form-group">
                        <div class="btn btn-default stm-advise">
                            <span>Sotib olish</span>
                        </div>
                    </div>
                </a>
            </div>
!-->
						<?php buy_button( $course_id, 1 ); ?>
					<?php endif; ?>
					<?php
					if ( ( ! empty( $price ) or ! empty( $sale_price ) ) /* && $oferta == 'yes' */ ): ?>
                        <div class="tech__course-price" style="text-align: center;">
                            <strong id="discount_price">
								<?php if ( ! empty( $price ) ): ?>
									<?php echo STM_LMS_Helpers::display_price( $price ); ?>
								<?php endif; ?>
                            </strong >
                            <span id="discount_price">
                            <?php if ( ! empty( $sale_price ) ): ?>
	                            <?php echo STM_LMS_Helpers::display_price( $sale_price ); ?>
                            <?php endif; ?>
                        </span>
                            <div class="row">
                    <span class="row stm-padding-top">
                            <input name="discount_code" id="stm_discount_code" class="discount_class col-lg-12" type="text" placeholder="Promo kodni Kirgizing"/>
                    </span>
                                <span class="row stm-padding-top stm-message">
                                </span>

                                <span class="row stm-padding-top">
                <button type="button" class="btn btn-theme btn-outline-primary" id="data-discount-course"
                        data-discount-course="<?php echo esc_attr( $course_id ) ?>">
                        Promo kodni Kirgizing
                    </button>
                                     </span>
                    </div>
                        </div>
					<?php endif; ?>

				<?php else: ?>

					<?php /* ?>
<div class="stm-lms-buy-buttons stm-lms-buy-buttons-mixed stm-lms-buy-buttons-mixed-pro">
    <div class="container">
        <div class="form-group">
            <div class="btn btn-outline">
                <span>Maslahat Olish</span>
            </div>
        </div>
    </div>
</div>

<?php */ ?>

                    <div class="stm-lms-buy-buttons stm-lms-buy-buttons-mixed stm-lms-buy-buttons-mixed-pro">
                        <div class="container">
                            <div class="form-group">
                                <!--

								!-->
								<?php if ( is_user_logged_in() ) : ?>
                                    <div class="btn btn-default"
                                         id="redirect_price_list"
                                         data-href="<?php echo site_url() . '/upwork-price-list/' ?>">
                                        <span>Kursga yozilish</span>
                                    </div>
								<?php else: ?>
                                    <!--
                <div class="btn btn-default"
                         id="redirect_price_list"
                         data-href="<?php echo site_url() . '/upwork-price-list/' ?>" >
                    <span>Kursga yozilish</span>
                     </div>
             !-->
                                    <div class="btn btn-default"
                                         data-text="Log in"
                                         data-target=".stm-lms-modal-login"
                                         data-lms-modal="login">
                                        <span>Kursga yozilish</span>
                                    </div>

									<?php
									if ( ( ! empty( $price ) or ! empty( $sale_price ) ) /* && $oferta == 'yes' */ ): ?>
                                        <div class="tech__course-price" style="text-align: center;">
                                            <strong>
												<?php if ( ! empty( $price ) ): ?>
													<?php echo STM_LMS_Helpers::display_price( $price ); ?>
												<?php endif; ?>
                                            </strong>
                                            <span>
                            <?php if ( ! empty( $sale_price ) ): ?>
	                            <?php echo STM_LMS_Helpers::display_price( $sale_price ); ?>
                            <?php endif; ?>
                        </span>
                                        </div>
									<?php endif; ?>
								<?php endif; ?>
                            </div>
                        </div>
                    </div>

					<?php /* ?>
<div class="modal fade stm-lms-modal-advice" tabindex="-1" data-keyboard="false" data-backdrop="static" role="dialog"
 aria-labelledby="stm-lms-modal-oferta">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body bg-secondary">
                Maslahat Olish
            </div>
        </div>
    </div>
</div>
<?php */ ?>

				<?php endif; ?>

                <div class="stm_lms_mixed_button__list">


					<?php if ( $dropdown_enabled ):
						stm_lms_register_style( 'membership' );
						$subs                 = STM_LMS_Subscriptions::user_subscription_levels(); ?>

						<?php if ( ! $not_salebale ): ?>

                        <a class="stm_lms_mixed_button__single"
                           href="#" <?php echo implode( ' ', apply_filters( 'stm_lms_buy_button_auth', $attributes, $course_id ) ); ?>>
                            <span><?php esc_html_e( 'Bir martalik to\'lov', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
                        </a>

					<?php endif; ?>

						<?php
						if ( $subscription_enabled ):
							$plans_courses = STM_LMS_Course::course_in_plan( $course_id );
							$plans_course_ids = wp_list_pluck( $plans_courses, 'id' );
							$plans_have_quota = false;
							foreach ( $subs as $sub ) {

								if ( ! in_array( $sub->ID, $plans_course_ids ) ) {
									continue;
								}
								if ( $sub->course_number > 0 ) {
									$plans_have_quota = true;
								}

							}
							if ( $plans_have_quota ) :
								$subs_info = array();
								foreach ( $subs as $sub ) {

									if ( ! in_array( $sub->ID, $plans_course_ids ) ) {
										continue;
									}
									$subs_info[] = array(
										'id'            => $sub->subscription_id,
										'course_id'     => get_the_ID(),
										'name'          => $sub->name,
										'course_number' => $sub->course_number,
										'used_quotas'   => $sub->used_quotas,
										'quotas_left'   => $sub->quotas_left
									);
								}
								?>
                                <button type="button"
                                        data-lms-params='<?php echo json_encode( $subs_info ); ?>'
                                        class=""
                                        data-target=".stm-lms-use-subscription"
                                        data-lms-modal="use_subscription">
                                    <span><?php esc_html_e( 'A\'zo qilish', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
                                </button>

							<?php else: ?>

                                <!--Check course available only in one plan as a category-->
								<?php
								$buy_url   = STM_LMS_Subscriptions::level_url();
								$buy_label = esc_html__( 'A\'zo qilish', 'masterstudy-lms-learning-management-system-pro' );
								$plans     = array(
									$buy_url => $buy_label
								);
								if ( ! empty( $plans_courses ) ) {
									$plans = array();
									foreach ( $plans_courses as $plan_course ) {
										$plan_course_limit = get_option( "stm_lms_course_number_{$plan_course->id}", 0 );
										if ( empty( $plan_course_limit ) ) {
											continue;
										}
										stm_lms_register_script( 'buy/plan_cookie', array( 'jquery.cookie' ), true );
										$buy_url           = add_query_arg( 'level', $plan_course->id, STM_LMS_Subscriptions::checkout_url() );
										$buy_label         = sprintf(
											esc_html__( 'Available in "%s" plan', 'masterstudy-lms-learning-management-system-pro' ),
											$plan_course->name
										);
										$plans[ $buy_url ] = $buy_label;
									}
								}
								foreach ( $plans as $plan_url => $plan_label ):
									?>

                                    <a href="<?php echo esc_url( $plan_url ); ?>"
                                       class="btn btn-default btn-subscription btn-outline btn-save-checkpoint"
                                       data-course-id="<?php echo esc_attr( $course_id ) ?>">
                                        <span><?php echo esc_html( $plan_label ); ?></span>
                                    </a>

								<?php endforeach; ?>
							<?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>


					<?php do_action( 'stm_lms_after_mixed_button_list', $course_id ); ?>

                </div>

            </div>
		<?php else: ?>
			<?php do_action( 'stm_lms_pro_instead_buttons', $course_id ); ?>
		<?php endif; ?>

		<?php endif; ?>

		<?php do_action( 'stm_lms_buy_button_end', $course_id ); ?>

    </div>

<?php endif; ?>