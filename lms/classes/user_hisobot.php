<?php

class STM_LMS_Report_Child
{

	public function __construct()
	{
		add_action('init', [$this, 'report_upload']);
	}

	public function report_upload()
	{
		if (!empty($_POST['action_s']) && $_POST['action_s'] == 'update_xisobot') {
			$user_id = ($_POST['user_id']) ? $_POST['user_id'] : '';
			$d_files = (!empty($_POST['lms_reports']['xisobot_saved'])) ? $_POST['lms_reports']['xisobot_saved'] : [];
			$d_dates= (!empty($_POST['lms_reports']['xisobot_date'])) ? $_POST['lms_reports']['xisobot_date'] : [];
			$dir = (get_user_meta($user_id, 'reports', true)) ? get_user_meta($user_id, 'reports', true) : [];

			foreach ($dir as $k => $file) {
				if (!in_array($file['path'], $d_files)) {
					unset($dir[$k]);
				}
			}


			if (!empty($_FILES)) {
				foreach ($_FILES['lms_reports']['name'] as $files) {
					foreach ($files as $k => $name) {
						$ufile = [
							'name' => $name,
							'type' => $_FILES['lms_reports']['type']['xisobot'][$k],
							'tmp_name' => $_FILES['lms_reports']['tmp_name']['xisobot'][$k],
							'error' => $_FILES['lms_reports']['error']['xisobot'][$k],
							'size' => $_FILES['lms_reports']['size']['xisobot'][$k]
						];
						$fl = $this->upload_stm_file($ufile, $user_id);
						if ($fl) {
							$dir[] = $fl;
						}
					}
				}

			}

				update_user_meta($user_id, 'reports', $dir);
		}
	}

	function upload_stm_file($ufile, $user_id)
	{
		global $wp_filesystem;
		WP_Filesystem();
		$upload = 'uploads/';
		$passport = 'reports';
		$content_directory = $wp_filesystem->wp_content_dir() . $upload;
		$wp_filesystem->mkdir($content_directory . $passport);

		$target_dir_location = $content_directory . "{$passport}/";
		$fileInfo = wp_check_filetype(basename($ufile['name']));

		$file_type = '';
		if (!empty($fileInfo['ext'])) {
			$file_type = $fileInfo['ext'];
		} else {
			return false;
		}

		if (isset($ufile) && in_array($file_type, ['pdf'])) {
			$name_file = $ufile['name'];
			$tmp_name = $ufile['tmp_name'];
			$name_file = time() . "_{$user_id}_{$name_file}";

			if (move_uploaded_file($tmp_name, $target_dir_location . $name_file)) {
					$fl['path'] = "{$upload}{$passport}/{$name_file}";
					$fl['date'] = date("Y-m-d");
					return $fl;
			} else {
				return false;
			}
		}
	}

}

new STM_LMS_Report_Child();