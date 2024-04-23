<?php

add_filter('stm_wpcfto_fields', function ($fields) {

	$fields['stm_lesson_settings']['section_lesson_settings']['fields']['lesson_video'] = array(
		'type' => 'video',
		'label' => esc_html__('Lesson video protected', 'masterstudy-child'),
		'dependency' => array(
			'key' => 'type',
			'value' => 'video'
		)
	);

	return $fields;
});

add_filter('wpcfto_options_page_setup', function ($setups) {
	$setups[0]['fields']['stm_lms_video_path_folder'] = stm_lms_video_path_folder();
	return $setups;
}, 5, 1);

function stm_lms_video_path_folder()
{
	return array(
		'name' => esc_html__('Protected Video Folder', 'masterstudy-child'),
		'label' => esc_html__('Protected Video Folder Settings', 'masterstudy-child'),
		'icon' => 'fas fa-file-code',
		 'fields' => array(
            'protected_video_absolute_folder_path' => array(
                'type' => 'text',
                'label' => esc_html__('Protected Video Absolute Folder Path', 'masterstudy-child'),
                'value' => ''
            ),
        )
	);
}





