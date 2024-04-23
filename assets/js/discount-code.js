"use strict";

(function ($) {

    $(document).ready(function () {

        $('#data-discount-course').on('click', function (e) {
            e.preventDefault();
            let course_id = $(this).attr('data-discount-course');
            let discount_code = $("#stm_discount_code").val();
            if (!$(course_id).length) {

                $.ajax({
                    url: stm_lms_ajaxurl,
                    dataType: 'json',
                    context: this,
                    data: {
                        action: 'discount_code_ajax',
                        nonce: stm_lms_nonces['stm_lms_add_to_cart'],
                        course_id: course_id,
                        discount_code: discount_code
                    },
                    beforeSend: function beforeSend() {
                        $(this).addClass('loading');
                    },
                    complete: function complete(data) {
                        let res = (data['responseJSON']) ? data['responseJSON'] : {};
                        $(this).removeClass('loading');

                        if ($.isEmptyObject(res['discount_price'])) {
                            $('.stm-message').text(' Promo Kod mavjud emas ! ');

                        } else {
                            $('#discount_price').text(res['discount_price']);
                            $('.stm-message').text(res['stm_message']);

                        }
                    }
                });
            }
        });

    });
})(jQuery);