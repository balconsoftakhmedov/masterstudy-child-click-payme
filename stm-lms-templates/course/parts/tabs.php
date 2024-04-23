<?php if (!defined('ABSPATH')) exit; //Exit if accessed directly ?>


<?php
$tabs = array();

$tabs['curriculum'] = esc_html__('O\'quv dasturi', 'masterstudy-lms-learning-management-system');
$tabs['description'] = esc_html__('Kurs haqida', 'masterstudy-lms-learning-management-system');
$tabs['faq'] = esc_html__('FAQ', 'masterstudy-lms-learning-management-system');
$tabs['announcement'] = esc_html__('E\'lon', 'masterstudy-lms-learning-management-system');
$tabs['reviews'] = esc_html__('Izohlar', 'masterstudy-lms-learning-management-system');
//$tabs['taqdim'] = esc_html__('Sizga taqdim etiladi', 'masterstudy-lms-learning-management-system');
$tabs = apply_filters('stm_lms_course_tabs', $tabs, get_the_ID());

$active = array_search(reset($tabs), $tabs);
$tabs_length = count($tabs);
?>

<?php if ($tabs_length > 1) : ?>
    <ul class="nav nav-tabs" role="tablist">

        <?php foreach ($tabs as $slug => $name): ?>
            <li role="presentation" class="<?php echo ($slug == $active) ? 'active' : '' ?>">
                <a href="#<?php echo esc_attr($slug); ?>"
                   data-toggle="tab">
                    <?php echo wp_kses_post($name); ?>
                </a>
            </li>
        <?php endforeach; ?>

    </ul>

<?php endif; ?>


<div class="tab-content">
    <?php foreach ($tabs as $slug => $name): ?>
        <div role="tabpanel"
             class="tab-pane <?php echo ($slug == $active) ? 'active' : '' ?>"
             id="<?php echo esc_attr($slug); ?>">
            <?php STM_LMS_Templates::show_lms_template('course/parts/tabs/' . $slug); ?>
        </div>
    <?php endforeach; ?>
</div>