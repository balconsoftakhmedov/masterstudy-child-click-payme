<?php
wp_enqueue_style('boostrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', NULL, STM_THEME_VERSION, 'all');
wp_enqueue_style('user-report-list', get_stylesheet_directory_uri() . '/assets/css/user-report-list.css', NULL, STM_THEME_VERSION, 'all');


wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), STM_THEME_VERSION, TRUE);
wp_enqueue_script('user-report-list', get_stylesheet_directory_uri() . '/assets/js/user-report-list.js', ['jquery'], time());
$user_id = get_current_user_id();
$dirs = (get_user_meta($user_id, 'reports', true)) ? get_user_meta($user_id, 'reports', true) : [];
?>

<?php STM_LMS_Templates::show_lms_template('account/private/parts/top_info', array(
	'title' => esc_html__('Mening Xisobotim', 'masterstudy-lms-learning-management-system')
));


return ; ?>

<div id="my-reports">

    <div class="container">
        <div class="row">

        </div>
        <div class="row">

        </div>

    </div>

    <div class="container user-list-form">
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default ">
                    <div class="panel-heading"><h2> Xisobotlar</h2></div>
                    <div class="panel-body">

                        <form method="POST" action="" enctype="multipart/form-data">

                            <div class="input-group control-group ">
                                <h3>Xisobotni Yuklang PDF formatda</h3>

                            </div>
							<?php foreach ($dirs as $dir) : ?>
                                <div class="report_saved input-group control-group">

                                    <a class="form-control"
                                       href="<?php echo site_url().'/wp-content/'.$dir['path'] ?>"><?php echo basename($dir['path']) ?></a>
                                    <input type="hidden" name="lms_reports[xisobot_saved][]"
                                           value="<?php echo $dir['path']  ?>">
                                    <input type="hidden" disabled name="lms_reports[xisobot_date][]"
                                           value="<?php echo $dir['date'] ?>"/>
                                    <span class="form-control">Sana: <?php
                                        $date=date_create($dir['date']);
                                        echo date_format($date, "d-m-Y"); ?></span>
                                    <div class="input-group-btn">
                                        <button data-id="" class="btn btn-danger remove" type="button">Remove
                                        </button>
                                    </div>

                                </div>
							<?php endforeach; ?>
                            <input type="hidden" name="action_s" value="update_xisobot"/>
                            <input type="hidden" name="user_id" value="<?php echo $user_id ?>"/>

                            <div class="input-group control-group after-add-more">
                                <input type="file" accept="application/pdf" name="lms_reports[xisobot][]"
                                       class="form-control lms-upload"
                                       placeholder="Xisobot">
                                <input class="form-control lms-date" type="hidden" name="lms_reports[xisobot_date][]"
                                       placeholder="Xisobot sanasi"
                                       value=""/>
                                <div class="input-group-btn">
                                    <button data-id="" class="btn btn-success add-more"
                                            type="button"> Add
                                    </button>
                                </div>
                            </div>
                            <div class="input-group control-group ">
                                <input type="submit" class="btn btn-success" value="submit"/>
                            </div>
                        </form>

                        <!-- Copy Fields -->
                        <div class="copy hide">
                            <div class="control-group input-group" style="margin-top:10px">
                                <input type="file" accept="application/pdf" name="lms_reports[xisobot][]"
                                       class="form-control lms-upload"
                                       placeholder="Xisobot">
                                <input class="form-control lms-date" type="hidden" name="lms_reports[xisobot_date][]"
                                       placeholder="Xisobot sanasi"
                                       value=""/>
                                <div class="input-group-btn">
                                    <button data-id="" class="btn btn-danger remove" type="button">Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>