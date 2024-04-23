<?php if (!defined('ABSPATH')) exit; //Exit if accessed directly

/**
 * @var $lesson_id
 * @var $lms_page_path
 */

$course = get_page_by_path( $lms_page_path, OBJECT, 'stm-courses' );

$post_id = intval($course->ID);
$item_id = intval($lesson_id);


do_action('stm_lms_before_item_template_start', $post_id, $item_id);

$is_previewed = (!empty($is_previewed)) ? $is_previewed : false;

$content_type = (get_post_type($item_id) == 'stm-lessons') ? 'lesson' : get_post_type($item_id);
$content_type = (get_post_type($item_id) == 'stm-quizzes') ? 'quiz' : $content_type;

$lesson_type = '';
if ($content_type === 'lesson') {
    $lesson_type = get_post_meta($item_id, 'type', true);
    stm_lms_register_style('lesson_' . $lesson_type);
}

STM_LMS_Templates::show_lms_template(
    'lesson/header',
    compact('post_id', 'item_id', 'is_previewed', 'content_type', 'lesson_type')
);

$custom_css = get_post_meta($item_id, '_wpb_shortcodes_custom_css', true);

stm_lms_register_style('lesson', array(), $custom_css);
do_action('stm_lms_template_main');

$has_access = STM_LMS_User::has_course_access($post_id, $item_id);
$has_preview = STM_LMS_Lesson::lesson_has_preview($item_id);
$is_previewed = STM_LMS_Lesson::is_previewed($post_id, $item_id);
$lesson_style = STM_LMS_Options::get_option('lesson_style', 'default');
if ($has_access or $has_preview):

    if (apply_filters('stm_lms_stop_item_output', false, $post_id)) {
        do_action('stm_lms_before_item_lesson_start', $post_id, $item_id);
    } else {
        if($lesson_style === 'classic' && $lesson_type !== 'stream' && $lesson_type !== 'zoom_conference'){
            stm_lms_register_style('lesson/style_classic', array());
        }
        if (!$is_previewed) do_action('stm_lms_lesson_started', $post_id, $item_id, '');
        stm_lms_update_user_current_lesson($post_id, $item_id);

        ?>
        <div class="stm-lms-course__overlay"></div>

        <div class="stm-lms-wrapper container-fluid--tech__lesson-wrapper <?php echo esc_attr(get_post_type($item_id) . ' ' . 'lesson_style_' . $lesson_style); ?>">

            <div class="container-fluid container-fluid--tech__lesson">
                <div class="row">
                    <div class="col-md-8">
                        <?php $item_content = apply_filters('stm_lms_show_item_content', true, $post_id, $item_id); ?>

                        <?php if ($item_content) : ?>
                            <div id="stm-lms-lessons" class="tech__stm-lms-lessons">
                                <div class="stm-lms-course__content">

                                    <div class="stm-lms-course__content_wrapper">

                                        <?php echo apply_filters('stm_lms_lesson_content', STM_LMS_Templates::load_lms_template(
                                            'course/parts/' . $content_type,
                                            compact('post_id', 'item_id', 'is_previewed')
                                        ), $post_id, $item_id); ?>

                                    </div>
                                </div>
                            </div>

                        <?php endif; ?>

                        <?php echo apply_filters('stm_lms_course_item_content', $content = '', $post_id, $item_id); ?>
                    </div>
                    <div class="col-md-4">
                        <div class="tech__curriculum">
                            <?php STM_LMS_Templates::show_lms_template('lesson/curriculum', array('post_id' => $post_id, 'item_id' => $item_id, 'lesson_type' => $lesson_type)); ?>
                            <div class="telegram-invite-link">
                                <a href="https://t.me/+HTOzeXYh0KgxZGYy" target="_blank" style="width: 60px;height: 60px;position: fixed;right: 20px;bottom: 20px;z-index: 999999;">
                                    <svg style="width: 100%;height: 100%;" width="240px" height="240px" viewBox="0 0 240 240" id="svg2" xmlns="http://www.w3.org/2000/svg"><style>.st0{fill:url(#path2995-1-0_1_)}.st1{fill:#c8daea}.st2{fill:#a9c9dd}.st3{fill:url(#path2991_1_)}</style><linearGradient id="path2995-1-0_1_" gradientUnits="userSpaceOnUse" x1="-683.305" y1="534.845" x2="-693.305" y2="511.512" gradientTransform="matrix(6 0 0 -6 4255 3247)"><stop offset="0" stop-color="#37aee2"/><stop offset="1" stop-color="#1e96c8"/></linearGradient><path id="path2995-1-0" class="st0" d="M240 120c0 66.3-53.7 120-120 120S0 186.3 0 120 53.7 0 120 0s120 53.7 120 120z"/><path id="path2993" class="st1" d="M98 175c-3.9 0-3.2-1.5-4.6-5.2L82 132.2 152.8 88l8.3 2.2-6.9 18.8L98 175z"/><path id="path2989" class="st2" d="M98 175c3 0 4.3-1.4 6-3 2.6-2.5 36-35 36-35l-20.5-5-19 12-2.5 30v1z"/><linearGradient id="path2991_1_" gradientUnits="userSpaceOnUse" x1="128.991" y1="118.245" x2="153.991" y2="78.245" gradientTransform="matrix(1 0 0 -1 0 242)"><stop offset="0" stop-color="#eff7fc"/><stop offset="1" stop-color="#fff"/></linearGradient><path id="path2991" class="st3" d="M100 144.4l48.4 35.7c5.5 3 9.5 1.5 10.9-5.1L179 82.2c2-8.1-3.1-11.7-8.4-9.3L55 117.5c-7.9 3.2-7.8 7.6-1.4 9.5l29.7 9.3L152 93c3.2-2 6.2-.9 3.8 1.3L100 144.4z"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php /* ?>
            <div class="container">
                <?php STM_LMS_Templates::show_lms_template( 'course/parts/tabs' ); ?>
            </div>
            <?php */ ?>

        </div>

    <?php } ?>
<?php else:

	wp_redirect(home_url());
	exit();

    stm_lms_register_style('lesson_locked');
    stm_lms_register_script('lesson_locked', array(), false, "stm_lms_course_id = {$post_id};");

    ?>


    <div class="stm_lms_locked_lesson__overlay"></div>
    <div class="stm_lms_locked_lesson__popup">
        <div class="stm_lms_locked_lesson__popup_inner">
            <h3><?php esc_html_e('Salom, ajoyib kurs, to\'g\'rimi? Sizga bu kurs yoqdimi?', 'masterstudy-lms-learning-management-system'); ?></h3>
            <p>
                <?php esc_html_e('Qiziqarli va foydali darslar hali oldinda. Davom etish uchun kursni sotib oling!', 'masterstudy-lms-learning-management-system'); ?>
            </p>
            <?php STM_LMS_Templates::show_lms_template('global/buy-button', array('course_id' => $post_id, 'item_id' => $item_id, 'has_access' => false)); ?>
            <a class="stm_lms_locked_lesson__popup_close" href="<?php echo esc_url(get_permalink($post_id)); ?>">
                <i class="lnricons-cross"></i>
            </a>
        </div>
    </div>

    <div class="stm-lms-course__overlay"></div>

    <div class="stm-lms-wrapper container-fluid--tech__lesson-wrapper <?php echo esc_attr(get_post_type($item_id)); ?>">

        <div class="container-fluid container-fluid--tech__lesson">
            <div class="row">
                <div class="col-md-8">
                    <div id="stm-lms-lessons" class="tech__stm-lms-lessons">
                        <div class="stm-lms-course__content">

                            <?php STM_LMS_Templates::show_lms_template('lesson/content_top_wrapper_start', compact('lesson_type')); ?>
                            <?php STM_LMS_Templates::show_lms_template('lesson/content_top', compact('post_id', 'item_id')); ?>
                            <?php STM_LMS_Templates::show_lms_template('lesson/content_top_wrapper_end', compact('lesson_type')); ?>

                            <div class="stm-lms-course__content_wrapper">

                                <h4 class="text-center">
                                    <?php esc_html_e('Lesson is locked. Please Buy course to proceed.', 'masterstudy-lms-learning-management-system'); ?>
                                </h4>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <?php STM_LMS_Templates::show_lms_template('lesson/curriculum', array('post_id' => $post_id, 'item_id' => $item_id)); ?>
                </div>
            </div>
        </div>

        <?php /* ?>
        <div class="container">
            <?php STM_LMS_Templates::show_lms_template( 'course/parts/tabs' ); ?>
        </div>
        <?php */ ?>

    </div>
<?php endif; ?>

<?php if (!$is_previewed) STM_LMS_Templates::show_lms_template('lesson/navigation', compact('post_id', 'item_id', 'lesson_type')); ?>


<?php
do_action('wp_footer');
do_action('template_redirect');
?>


<?php
STM_LMS_Templates::show_lms_template(
    'lesson/footer',
    compact('post_id', 'item_id', 'is_previewed')
);

do_action('stm_lms_template_main_after');

?>