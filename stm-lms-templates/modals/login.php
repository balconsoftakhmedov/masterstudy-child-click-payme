<div class="modal fade stm-lms-modal-login" tabindex="-1" role="dialog" aria-labelledby="stm-lms-modal-register">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body"   data-buy-plan="<?php echo $plan_id ?>" >
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#stm-lms-login-modal" data-toggle="tab"><?php esc_html_e('Kirish', 'masterstudy-lms-learning-management-system'); ?></a>
                    </li>
                    <li role="presentation">
                        <a href="#stm-lms-register" data-toggle="tab"><?php esc_html_e('Ro\'yxatdan o\'tish', 'masterstudy-lms-learning-management-system'); ?></a>
                    </li>
                </ul>
				<?php STM_LMS_Templates::show_lms_template('account/v1/login', array('form_position' => '-modal')); ?>
				<?php STM_LMS_Templates::show_lms_template('account/v1/register', array('plan_id' => $plan_id )); ?>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    stm_lms_login(false);
    stm_lms_register(false);
</script>