<?php
if(class_exists('STM_LMS_WPCFTO_AJAX')) {
	class STM_LMS_WPCFTO_AJAX_CHILD extends STM_LMS_WPCFTO_AJAX
	{
		public function __construct()
		{
			remove_all_actions('wp_ajax_stm_curriculum_get_item');
			add_action('wp_ajax_stm_curriculum_get_item', array($this, 'stm_lms_curriculum_get_item'));
		}

		function stm_lms_curriculum_get_item()
		{

			check_ajax_referer('stm_curriculum_get_item', 'nonce');

			$post_id = intval($_GET['id']);
			$r = array();

			$r['meta'] = STM_LMS_Helpers::simplify_meta_array(get_post_meta($post_id));

			if (!empty($r['meta']['lesson_video_poster'])) {
				$image = wp_get_attachment_image_src($r['meta']['lesson_video_poster'], 'img-870-440');
				if (!empty($image[0])) $r['meta']['lesson_video_poster_url'] = $image[0];
			}
			if (!empty($r['meta']['lesson_video'])) {
				$video = wp_get_attachment_url($r['meta']['lesson_video']);
				if (!empty($video)) $r['meta']['uploaded_lesson_video'] = $video;
			}

			if (!empty($r['meta']['wp_custom_attachment'])) {
				$video = $r['meta']['wp_custom_attachment']['file'];
				if (!empty($video)) $r['meta']['uploaded_lesson_video'] = $video;
			}

			if (!empty($r['meta']['lesson_files_pack'])) {
				$r['meta']['lesson_files_pack'] = json_decode($r['meta']['lesson_files_pack']);
			}
			$r['content'] = get_post_field('post_content', $post_id);

			wp_send_json($r);
		}
	}
	new STM_LMS_WPCFTO_AJAX_CHILD();
}