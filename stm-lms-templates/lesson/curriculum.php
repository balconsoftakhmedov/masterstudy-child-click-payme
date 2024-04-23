<?php

stm_lms_register_style('curriculum');
stm_lms_register_script('curriculum');
$post_id = (!empty($post_id)) ? $post_id : get_the_ID();
$curriculum = get_post_meta($post_id, 'curriculum', true);
$lesson_style = STM_LMS_Options::get_option('lesson_style', 'default');
?>

    <h3 class="stm-curriculum__title">
        <?php esc_html_e('Kurs bo\'limlari', 'masterstudy-lms-learning-management-system'); ?>
        <?php if ($lesson_style === 'classic'): ?>
            <?php /* ?>
    <div class="stm-lms-curriculum-trigger">
        <i class="fa fa-list-ul"></i>
    </div>
    <?php */ ?>
        <?php endif; ?>
    </h3>
<?php if (!empty($curriculum)):
    $curriculum_full = $curriculum = explode(',', $curriculum);
    $has_access = STM_LMS_User::has_course_access($post_id);
    $lesson_number = 1;

    $sections = STM_LMS_Lesson::create_sections($curriculum);

    $item_index = 0;
    ?>


    <?php foreach ($sections as $index => $section_info): ?>
    <?php

    $curriculum = (!empty($section_info['items'])) ? $section_info['items'] : array();
    $opened = (is_array($section_info['items']) and in_array($item_id, $section_info['items'])) ? 'opened' : '';
    $lesson_number = 1;

    ?>
    <div class="stm-curriculum-section <?php echo esc_attr($opened); ?>">
        <div class="stm-curriculum-item stm-curriculum-item__section <?php echo esc_attr($opened); ?>">
            <div class="stm-curriculum-section__info">
                <span><?php echo wp_kses_post($section_info['number']); ?></span>
                <?php if (!empty($section_info['title'])): ?>
                    <h5><?php echo wp_kses_post($section_info['title']); ?></h5>
                <?php endif; ?>
            </div>
        </div>

        <div class="stm-curriculum-section__lessons">
            <?php foreach ($curriculum as $curriculum_item):
                $curriculum_item = stm_lms_get_wpml_binded_id($curriculum_item);
                ?>

                <?php $content_type = get_post_type($curriculum_item);
                $meta = '';
                $icon = 'stmlms-text';
                $type = 'lesson';
                $quiz_info = array();

                $previous_completed = (isset($completed)) ? $completed : 'first';

                $user = STM_LMS_User::get_current_user();
                $user_id = $user['id'];
                $completed = [];
                if ($content_type === 'stm-quizzes') {
                    $type = 'quiz';
                    $quiz_info = STM_LMS_Helpers::simplify_db_array(stm_lms_get_user_quizzes($user_id, $curriculum_item, array('progress')));
                    $completed = (STM_LMS_Quiz::quiz_passed($curriculum_item)) ? 'completed' : '';
                    $icon = 'stmlms-quiz';
                } else {
                    $meta = get_post_meta($curriculum_item, 'duration', true);
                    $completed = (STM_LMS_Lesson::is_lesson_completed('', $post_id, $curriculum_item)) ? 'completed' : '';

                    $type = get_post_meta($curriculum_item, 'type', true);

                    if ($type == 'slide' or $type == 'video') $icon = 'stmlms-slides';

                    if ($type == 'zoom_conference') $icon = 'fas fa-video';

                    if ($type == 'stream') $icon = 'fab fa-youtube';
                }


                $item_classes = array('stm-curriculum-item');
                $item_classes[] = $type;

                $item_classes[] = "is-{$completed}";

                if ((int)$curriculum_item == (int)$item_id) {
                    $item_classes[] = 'lms-active';
                }

                $item_classes[] = apply_filters("stm_lms_prev_status", "{$previous_completed}", $post_id, $curriculum_item, $user_id);
                if ($curriculum_item === $item_id) $item_classes[] = 'active';

                ?>
                <a href="<?php echo esc_url(STM_LMS_Lesson::get_lesson_url($post_id, $curriculum_item)) ?>"
                   class="<?php echo implode(' ', $item_classes); ?>">

                    <div class="stm-curriculum-item__icon">
                        <i class="<?php echo esc_attr($icon) ?>"></i>
                    </div>

                    <div class="stm-curriculum-item__num">
                        <?php echo intval($lesson_number); ?>
                    </div>

                    <div class="stm-curriculum-item__title">
                        <div class="heading_font">
                            <?php echo esc_attr(get_the_title($curriculum_item)); ?>
                        </div>
                    </div>

                    <div class="stm-curriculum-item__meta">
                        <?php if (!empty($quiz_info['progress'])): ?>
                            <?php echo intval($quiz_info['progress']) ?>%
                        <?php else:
                            $questions = get_post_meta($curriculum_item, 'questions', true);
                            if (!empty($questions)) {
                                $questions_array = explode(',', $questions);
                                if (!empty($questions_array)) {
                                    echo sprintf(esc_html__('%s savollar', 'masterstudy-lms-learning-management-system'), count($questions_array));
                                }
                            }
                            ?>
                        <?php endif; ?>

                        <?php if ($lesson_style === 'classic' && $lesson_type !== 'stream' && $lesson_type !== 'zoom_conference' && !empty($meta)): ?>
                            <?php echo esc_html($meta); ?>
                        <?php endif; ?>
                    </div>

                    <?php

                    if (!isset($user_id)) $user_id = 0;
                    if ($completed == 'completed') :
                        echo apply_filters('stm_lms_curriculum_item_status', '
                            <div class="stm-curriculum-item__completed ' . esc_attr($completed) . '">
                                <i class="fa fa-check"></i>
                            </div>
                        ', $previous_completed, $post_id, $curriculum_item, $user_id);
                    endif;
                    ?>

                </a>

                <?php $lesson_number++; ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
    <?php
    $progress = 0;
    if (is_user_logged_in() && $lesson_style === 'classic' && $lesson_type !== 'stream' && $lesson_type !== 'zoom_conference') {
        $my_progress = STM_LMS_Helpers::simplify_db_array(stm_lms_get_user_course(get_current_user_id(), $post_id, array('progress_percent')));
        if (!empty($my_progress['progress_percent'])) $progress = $my_progress['progress_percent'];

        if ($progress > 100) $progress = 100;
        ?>
        <div class="course-progress">
            <h3 class="course-progress__title">
                <?php esc_html_e('Natijangiz', 'masterstudy-lms-learning-management-system'); ?>
            </h3>
            <div class="course-progress__bar">
                <div class="progress_line">
                    <div class="past primary_bg_color" style="width: <?php echo esc_attr($progress); ?>%"></div>
                </div>
                <div class="progress_percent primary_bg_color heading_font">
                    <?php echo esc_html($progress); ?>%
                </div>
            </div>
        </div>
        <?php
    }

    ?>
<?php endif; ?>