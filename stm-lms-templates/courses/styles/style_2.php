<?php
/**
 * @var $has_sale_price
 * @var $id
 * @var $price
 * @var $sale_price
 * @var $author_id
 * @var $style
 */

$classes = array($has_sale_price, $style);

$level = get_post_meta($id, 'level', true);
$duration = get_post_meta($id, 'duration_info', true);
$lectures = STM_LMS_Course::curriculum_info(get_post_meta($id, 'curriculum', true));

$isAvailable = get_field('availability', $id);
?>


<div class="stm_lms_courses__single stm_lms_courses__single_animation <?php echo implode(' ', $classes); ?>">

    <div class="stm_lms_courses__single__inner">

        <div class="stm_lms_courses__single__inner__image">

			<?php STM_LMS_Templates::show_lms_template('courses/parts/image', array('id' => $id, 'img_size' => '370x200')); ?>

			<?php if ($isAvailable != 1) : ?>
                <div class="tech__coming-soon-badge">
					<?php echo 'Tez kunda'; ?>
                </div>
			<?php else: ?>
                <a href="<?php the_permalink(); ?>" class="stm_price_course_hoverable">
					<?php STM_LMS_Templates::show_lms_template('global/price', compact('price', 'sale_price')); ?>
                </a>
			<?php endif; ?>

        </div>

        <div class="stm_lms_courses__single--inner">

			<?php STM_LMS_Templates::show_lms_template('courses/parts/terms', array('id' => $id, 'symbol' => '')); ?>

			<?php if ($isAvailable != 1) : ?>
                <div class="stm_lms_courses__single--title">
                    <h5><?php the_title(); ?></h5>
                </div>
			<?php else: ?>
				<?php STM_LMS_Templates::show_lms_template('courses/parts/title'); ?>
			<?php endif; ?>

            <div class="tech__course-single-excerpt">
                <p><?php echo get_the_excerpt(); ?></p>
            </div>

            <div class="tech__course-single-rating-price">
				<?php
				$user_id = get_current_user_id();
				$accept = get_the_author_meta('accept', $user_id);
				$user_meta = get_userdata($user_id);

				$user_roles = ($user_meta->roles)?$user_meta->roles:[];

				?>
                <div class="stm_lms_courses__single--meta">

					<?php STM_LMS_Templates::show_lms_template('courses/parts/rating', array('id' => $id)); ?>
					<?php if (($accept == 'yes' && $user_id > 0) || in_array('administrator', $user_roles)): ?>
						<?php do_action('stm_lms_archive_card_price', compact('price', 'sale_price', 'id')); ?>
					<?php endif; ?>
                </div>


            </div>

            <div class="stm_lms_courses__single--info_meta">
				<?php STM_LMS_Templates::show_lms_template('courses/parts/meta', compact('level', 'duration', 'lectures')); ?>
            </div>

        </div>

    </div>

</div>