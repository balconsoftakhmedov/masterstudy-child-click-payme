<?php
stm_lms_register_style('register');
enqueue_register_script();
$r_enabled = STM_LMS_Helpers::g_recaptcha_enabled();

$disable_instructor = STM_LMS_Options::get_option('register_as_instructor', false);

if ($r_enabled):
    $recaptcha = STM_LMS_Helpers::g_recaptcha_keys();
endif;

$site_key = (!empty($recaptcha['public'])) ? $recaptcha['public'] : '';

if (class_exists('STM_LMS_Form_Builder')):
    $additional_forms = STM_LMS_Form_Builder::register_form_fields();
    $default_fields = STM_LMS_Form_Builder::profile_default_fields_for_register();
    $register_form = $additional_forms['register'];
    $become_instructor = $additional_forms['become_instructor'];
    ?>
    <script>
        window.profileDefaultFieldsForRegister = <?php echo sanitize_text_field(json_encode($default_fields)); ?>;
        window.additionalRegisterFields = <?php echo sanitize_text_field(json_encode($register_form)); ?>;
        window.additionalInstructorsFields = <?php echo sanitize_text_field(json_encode($become_instructor)); ?>;
    </script>
<?php
endif;
?>

<div id="stm-lms-register"
     class="vue_is_disabled"
     v-init="site_key = '<?php echo stm_lms_filtered_output($site_key); ?>'"
     v-bind:class="{'is_vue_loaded' : vue_loaded}">
    <h3><?php esc_html_e('Ro\'yxatdan o\'tish', 'masterstudy-lms-learning-management-system'); ?></h3>

    <form @submit.prevent="register()" class="stm_lms_register_wrapper">
        <div class="row">
            <div class="col-md-6" style="display: none !important;">
                <div class="form-group">
                    <label class="heading_font"><?php esc_html_e('Username', 'masterstudy-lms-learning-management-system'); ?></label>
                    <input class="form-control"
                           type="text"
                           name="login"
                           v-model="login"
                           placeholder="<?php esc_html_e('Enter username', 'masterstudy-lms-learning-management-system'); ?>"/>
                </div>

            </div>
            <div class="col-xs-12">
                <div class="form-group">
                    <label class="heading_font"><?php esc_html_e('Email', 'masterstudy-lms-learning-management-system'); ?></label>
                    <input class="form-control"
                           type="email"
                           name="email"
                           v-model="email"
                           @change="login = email"
                           placeholder="<?php esc_html_e('Email kiriting', 'masterstudy-lms-learning-management-system'); ?>"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="heading_font"><?php esc_html_e('Parol', 'masterstudy-lms-learning-management-system'); ?></label>
                    <input class="form-control"
                           type="password"
                           name="password"
                           v-model="password"
                           placeholder="<?php esc_html_e('Parol kiriting', 'masterstudy-lms-learning-management-system'); ?>"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="heading_font"><?php esc_html_e('Parolni tasdiqlash', 'masterstudy-lms-learning-management-system'); ?></label>
                    <input class="form-control"
                           type="password"
                           name="password_re"
                           v-model="password_re"
                           placeholder="<?php esc_html_e('Parolni qayta kiriting', 'masterstudy-lms-learning-management-system'); ?>"/>
                </div>
            </div>
            <div v-for="(profileField, index) in profileDefaultFieldsForRegister" class="col-md-12">
                <div class="form-group">
                    <label class="heading_font" v-html="profileField.label"></label>
                    <input class="form-control" v-if="index !== 'description'" type="text" v-model="profileField.value" :placeholder="profileField.placeholder" :required="profileField.required" />
                    <textarea class="form-control" v-if="index === 'description'" v-model="profileField.value" :placeholder="profileField.placeholder" :required="profileField.required"></textarea>
                </div>
            </div>
        </div>
        <div class="row additional-fields"  :class="field.label == 'Tajribam' || field.label == 'Ingliz tili darajasi' ? field.label  == 'Ingliz tili darajasi' ? '' : '' : ''"

             v-if="additionalRegisterFields.length"
             v-for="(field, index) in additionalRegisterFields">

            <div  :class="field.label == 'Tajribam' || field.label == 'Ingliz tili darajasi' ? 'col-xs-12' : 'col-xs-12'">
                <div class="form-group">
                    <label class="heading_font" v-if="typeof field.label !== 'undefined'" v-html="field.label"></label>

                    <?php STM_LMS_Templates::show_lms_template('account/v1/form_builder/email'); ?>

                    <?php STM_LMS_Templates::show_lms_template('account/v1/form_builder/select'); ?>

                    <?php STM_LMS_Templates::show_lms_template('account/v1/form_builder/radio'); ?>

                    <?php STM_LMS_Templates::show_lms_template('account/v1/form_builder/textarea'); ?>

                    <?php STM_LMS_Templates::show_lms_template('account/v1/form_builder/checkbox'); ?>

                    <?php STM_LMS_Templates::show_lms_template('account/v1/form_builder/file', array('name' => 'register')); ?>

                    <div class="field-description" v-if="field.description" v-html="field.description"></div>
                </div>
            </div>
        </div>
        <transition name="slide-fade">
            <div class="row" v-if="become_instructor && !additionalInstructorsFields.length">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="heading_font"><?php esc_html_e('Degree', 'masterstudy-lms-learning-management-system'); ?></label>
                        <input class="form-control"
                               type="text"
                               name="degree"
                               v-model="degree"
                               placeholder="<?php esc_html_e('Enter Your Degree', 'masterstudy-lms-learning-management-system'); ?>"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="heading_font"><?php esc_html_e('Expertise', 'masterstudy-lms-learning-management-system'); ?></label>
                        <input class="form-control"
                               type="text"
                               name="expertize"
                               v-model="expertize"
                               placeholder="<?php esc_html_e('Enter your Expertize', 'masterstudy-lms-learning-management-system'); ?>"/>
                    </div>
                </div>
            </div>
        </transition>
        <div class="row additional-fields" v-if="become_instructor && additionalInstructorsFields.length"
             v-for="(field, index) in additionalInstructorsFields">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="heading_font" v-if="typeof field.label !== 'undefined'" v-html="field.label"></label>

                    <?php STM_LMS_Templates::show_lms_template('account/v1/form_builder/email'); ?>

                    <?php STM_LMS_Templates::show_lms_template('account/v1/form_builder/select'); ?>

                    <?php STM_LMS_Templates::show_lms_template('account/v1/form_builder/radio'); ?>

                    <?php STM_LMS_Templates::show_lms_template('account/v1/form_builder/textarea'); ?>

                    <?php STM_LMS_Templates::show_lms_template('account/v1/form_builder/checkbox'); ?>

                    <?php STM_LMS_Templates::show_lms_template('account/v1/form_builder/file', array('name' => 'becomeInstructor')); ?>

                    <div class="field-description" v-if="field.description" v-html="field.description"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

                <?php STM_LMS_Templates::show_lms_template('gdpr/privacy_policy'); ?>

                <?php do_action('stm_lms_register_custom_fields'); ?>

                <div class="stm_lms_register_wrapper__actions">

                    <?php ?>

                    <?php if (!$disable_instructor): ?>
                        <label class="stm_lms_styled_checkbox">
                            <span class="stm_lms_styled_checkbox__inner">
                                <input type="checkbox"
                                       name="become_instructor"
                                       v-model="become_instructor"/>
                                <span><i class="fa fa-check"></i> </span>
                            </span>
                            <span><?php esc_html_e('Register as Instructor', 'masterstudy-lms-learning-management-system'); ?></span>
                        </label>
                    <?php endif; ?>
                    
                    <input type="hidden" id="stm_plan_id" name="plan_id" value="<?php echo $plan_id ?>" />

                    <button type="submit"
                            class="btn btn-default"
                            :disabled="loading"
                            v-bind:class="{'loading': loading}">
                        <span><?php esc_html_e('Ro\'yxatdan o\'tish', 'masterstudy-lms-learning-management-system'); ?></span>
                    </button>

                </div>

            </div>
        </div>

    </form>

    <transition name="slide-fade">
        <div class="stm-lms-message" v-bind:class="status" v-if="message">
            {{ message }}
        </div>
    </transition>
</div>