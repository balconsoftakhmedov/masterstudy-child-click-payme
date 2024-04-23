(function ($) {
    $(document).ready(function () {
        function set_video_timer() {
            setInterval(function () {
                stm_lms_timer['user_timer'] = parseInt(stm_lms_timer['user_timer'] ) + 10000;

                fd = new FormData();
                fd.append('action', 'stm_lms_timer');
                fd.append('nonce', stm_lms_timer['wp_rest_nonce']);
                fd.append('userid', stm_lms_timer['userid']);
                fd.append('postid', stm_lms_timer['post_id']);
                fd.append('user_timer', stm_lms_timer['user_timer']);
                $.ajax({
                    url: stm_lms_ajaxurl,
                    type: "POST",
                    data: fd,
                    cache: false,
                    processData: false,
                    contentType: false,
                    complete: function complete(data) {
                    },
                    success: function (data) {
                        if (data['set_completed'] == 'true'){
                            $('.stm_lms_complete_lesson').trigger('click');
                        }
                    }
                });

            }, 10000);
        }

        set_video_timer();
    });
})(jQuery);