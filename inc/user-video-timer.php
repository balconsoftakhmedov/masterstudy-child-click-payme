<?php

class user_video_timer
{
    public function __construct()
    {
        add_action('wp_ajax_stm_lms_timer', [$this, 'stm_lms_timer']);
        add_action('wp_ajax_nopriv_stm_lms_timer', [$this, 'stm_lms_timer']);
    }

    public function stm_lms_timer()
    {
        check_ajax_referer('wp_rest', 'nonce');
        $requests = ['userid', 'postid', 'user_timer'];
        $_mrequests = [];
        foreach ($requests as $request) {
            $_mrequests[$request] = ($_REQUEST[$request]) ? sanitize_text_field($_REQUEST[$request]) : null;
        }


        $this->get_user_timer($_mrequests['userid'], $_mrequests['postid'], $_mrequests['user_timer']);
        $user_post_timer = (get_post_meta($_mrequests['postid'], 'user_timer', true))? get_post_meta($_mrequests['postid'], 'user_timer', true):[];
        $set_completed = $this->set_completed($_mrequests['userid'], $_mrequests['postid'], $user_post_timer);
        wp_send_json(['user_timer'=>$user_post_timer, 'set_completed'=> $set_completed]);
    }

    public function get_user_timer($userid, $postid, $user_timer)
    {
        $user_post_timer =  (get_post_meta($postid, 'user_timer', true))?get_post_meta($postid, 'user_timer', true):[];
        $user_post_timer[$userid] = (int)$user_timer;
        update_post_meta($postid, 'user_timer', $user_post_timer);
    }

    public static function set_user_timer($id)
    {
        $user_id = get_current_user_id();

        if ($user_id) {
            $user_timer = (get_post_meta($id, 'user_timer', true))? get_post_meta($id, 'user_timer', true):[];
            $user_timer = (!empty($user_timer[$user_id]))?(int)$user_timer[$user_id]:0;
            wp_enqueue_script('stm-lms-lms-timer', get_stylesheet_directory_uri() . '/assets/js/lms-video-timer.js', ['jquery'], time(), false);
            wp_localize_script('stm-lms-lms-timer', 'stm_lms_timer', array(
                'user_timer' => $user_timer,
                'wp_rest_nonce' => wp_create_nonce('wp_rest'),
                'userid' => $user_id,
                'post_id' => $id,
            ));
        }
    }

    public function set_completed($userid, $postid, $user_timer)
    {
        $lesson_time= get_post_meta($postid, 'duration', true);
        $lesson_time = explode(':', $lesson_time);
        $lesson_time = ((((int)$lesson_time[0])*60) + (int)$lesson_time[1]) * 1000;
        $diff = (int) $user_timer - (int)($lesson_time/2);
        if ($diff > 0){
            return 'true';
        }else{
            return 'false';
        }
    }
}

new user_video_timer;
?>