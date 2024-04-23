(function ($) {
    $(document).ready(function () {

        $('body').on('click', '.user-accept', function (e) {
            e.preventDefault();
            let userid = $(this).data('userid');
            let accept = $(this).data('accept');

            fd = new FormData();
            fd.append('action', 'stm_lms_offerta_accept');
            fd.append('nonce', stm_lms_nonces['stm_lms_add_to_cart']);
            fd.append('userid', userid);
            fd.append('accept', accept);

            let button_text = $(this).text();
            $(this).text('Loading...');
            let vm = this;
            $.ajax({
                url: stm_lms_ajaxurl,
                type: "POST",
                data: fd,
                cache: false,
                processData: false,
                contentType: false,
                beforeSend: function beforeSend() {
                  //  $(vm).addClass('user-stm-loader');

                },
                complete: function complete(data) {
                    data = data['responseJSON'];
                  //  $(vm).removeClass('user-stm-loader');
                    $(vm).text(button_text);
                    $('tr#user-' + userid).find('td.status.column-status').html(data['button'])
                }
            });
        });
    });
})(jQuery);
