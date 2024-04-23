<?php
/**
 *
 * @var $id
 */
user_video_timer::set_user_timer($id);

$lesson_video_poster = get_post_meta($id, 'lesson_video_poster', true);
$lesson_video_url = get_post_meta($id, 'lesson_video_url', true);
$lesson_video = get_post_meta($id, 'lesson_video', true);
$lesson_video_width = get_post_meta($id, 'lesson_video_width', true);
$wp_custom_attachment = get_post_meta($id, 'wp_custom_attachment', true);

if (!empty($lesson_video)) {
	$uploaded_video = wp_get_attachment_url($lesson_video);
	$type = explode('.', $uploaded_video);
	$type = strtolower(end($type));
}
if (!empty($lesson_video_url)) {
	$uploaded_video = wp_get_attachment_url($lesson_video);
	$type = explode('.', $uploaded_video);
	$type = strtolower(end($type));
}

if (!empty($lesson_video_url)):
	echo do_shortcode("[vdo id='{$lesson_video_url}']");
endif;

if (empty($lesson_video_url) && !empty($uploaded_video) && empty($wp_custom_attachment)):
	?>
    <video
            controls
            controlsList="nodownload" width="576" height="720"
            src="<?php echo esc_attr($uploaded_video); ?>" type='video/<?php echo esc_attr($type); ?>'
    >
    </video>
<?php endif;

if (!empty($wp_custom_attachment)):
	$uploaded_video = $wp_custom_attachment['file'];
	$type = $wp_custom_attachment['type'];
	?>

<?php endif; ?>
