<?php

add_shortcode('user_approval', 'shortcode_function');
function shortcode_function()
{
    $token = (!empty($_GET['approval_token'])) ? $_GET['approval_token'] : '';
    $accept = (!empty($_GET['accept'])) ? sanitize_text_field($_GET['accept']) : '';
    $user_id = get_transient($token);
    $firstname = get_user_meta($user_id, 'first_name', true);
    $lastname = get_user_meta($user_id, 'last_name', true);
    if ($accept) {
        update_user_meta($user_id, 'accept', $accept);
        delete_transient($token);
        if ($accept == 'yes') {
            update_user_meta($user_id, 'admin_accepted', $accept);
            send_student_email($user_id);
        }
    }
    if ($user_id && $accept == 'yes') { ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1><?php echo "$firstname  $lastname" ?> Tasdiqlandi</h1>
                </div>
            </div>
        </div>
        <?php
    } else if ($user_id && $accept == 'no') { ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1><?php echo "$firstname  $lastname" ?> Tasdiqlanmadi</h1>
                </div>
            </div>
        </div>
        <?php
    } else { ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1>Token eskirdi eki yoq !</h1>
                </div>
            </div>
        </div>
        <?php
    }
}

function send_student_email($user_id)
{
    $firstname = get_user_meta($user_id, 'first_name', true);
    $lastname = get_user_meta($user_id, 'last_name', true);
    $user_info = get_userdata($user_id);
    $user_email = $user_info->user_email;

    $body = "<h2>Xurmatli $firstname $lastname! <br> sizning Profilingiz tasdiqlandi.
     itstars.uz kirib kurslarni sotib olishingiz mumkin</h2>";

    $headers = 'From: Itstar <admin@itstars.uz>' . "\r\n";
    wp_mail($user_email, 'itstars.uz dan sizga Habar bor', $body, $headers);
}

?>