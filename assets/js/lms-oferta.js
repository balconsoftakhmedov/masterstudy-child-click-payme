(function ($) {
    $(document).ready(function () {
        let selected_savol_global = [];
        let right_answers = {
            "savol_1": "value_2",
            "savol_2": "value_1",
            "savol_3": "value_3",
            "savol_4": "value_2",
            "savol_5": "value_3",
            "savol_6": "value_1",
            "savol_7": "value_1",
            "savol_8": "value_1",
        };

        $(document).ajaxComplete(function () {
            validate_fields();
        });

        $('body').on('change', 'input', function (e) {
            validate_fields();
        });

        $('body').on('change', '.sel_savol', function (e) {

            let checked_answer = [];
            i = 0;
            $('.sel_savol').each(function (key, value) {
                let user_meta_key = $(value).attr('name');
                if (right_answers[user_meta_key] == $(value).val()) {
                    selected_savol_global[i] = 'true';
                    $(this).css('border', '2px solid green');
                } else if ($(value).val() == '') {
                    selected_savol_global[i] = 'false';
                    $(this).css('border', 'none');
                } else {
                    selected_savol_global[i] = 'false';
                    $(this).css('border', '2px solid red');
                }
                i++;
            });
            if ($("#shart_check_box").is(':checked'))
                selected_savol_global[i] = 'true';
            else
                selected_savol_global[i] = 'false';
            if ($('#passport').val()) {
                selected_savol_global[i + 1] = 'true';
            } else {
                selected_savol_global[i + 1] = 'false';
            }
            hide_buttons(selected_savol_global);

        });

        if (selected_savol_global.includes('false')) {

            $('#tasdiqlash').css('display', 'none');
        } else {
            $('#tasdiqlash').css('display', 'inline-block');

            $('body').on('click', '.offerta-accept', function (e) {
                e.preventDefault();

                let userid = $(this).data('userid');
                let accept = $(this).data('accept');
                let select = $(this).parents('.stm-lms-modal-oferta').find('select');
                let selected_savol = {};

                $.each(select, function (key, value) {
                    let user_meta_key = $(value).attr('name');
                    selected_savol[user_meta_key] = {
                        'user_meta_key_value': $(value).val(),
                        'user_meta_key_label': $(value).find('option:selected').text()
                    }
                });

                fd = new FormData();
                fd.append('file', $("#passport").get(0).files[0]);
                fd.append('action', 'stm_lms_offerta');
                fd.append('nonce', stm_lms_nonces['load_modal']);
                fd.append('userid', userid);
                fd.append('accept', accept);
                fd.append('selected_javoblar', JSON.stringify(selected_savol));

                $.ajax({
                    url: stm_lms_ajaxurl,
                    type: "POST",
                    data: fd,
                    cache: false,
                    processData: false,
                    contentType: false,
                    beforeSend: function beforeSend() {
                        $(this).addClass('loading');
                    },
                    complete: function complete(data) {
                        data = data['responseJSON'];
                        $(this).removeClass('loading');
                        location.href = data['redirect_url'];
                        if (data['accept'] == 'no') {
                            location.href = data['redirect_url'];
                        } else {
                            document.location.reload();
                        }
                    }
                });

            });
        }

        $('.stm-lms-modal-oferta-button').trigger('click');

        function error() {
            let error = `<div class="error-message danger">Noto'gri</div>`;
            return error;
        }

        function hide_buttons(selected_savol_global) {
            console.log(selected_savol_global);
            if (selected_savol_global.includes('false')) {
                $('#tasdiqlash').css('display', 'none');
                $('.stm-message-error').text("Oferta Shartlarini qabul qilsangiz, va Passport nusxasini yuklasangiz Oferta \"Qabul Qilish\" tugmachasi paydo bo'ladi.")
                $('.stm-message-error').css('display', 'inline-block');
                $('.stm-message-error').css('display', 'inline-block');
            } else {
                $('#tasdiqlash').css('display', 'inline-block');
                $('.stm-message-error').text();
                $('.stm-message-error').css('display', 'none');
            }
        }

        function validate_fields() {
            let i = 0;
            $('.sel_savol').each(function (key, value) {
                let user_meta_key = $(value).attr('name');
                if (right_answers[user_meta_key] == $(value).val()) {
                    selected_savol_global[i] = 'true';
                    $(this).css('border', '2px solid green');
                } else if ($(value).val() == '') {
                    selected_savol_global[i] = 'false';
                    $(this).css('border', 'none');
                } else {
                    selected_savol_global[i] = 'false';
                    $(this).css('border', '2px solid red');
                }
                i++;
            });

            if ($("#shart_check_box").is(':checked'))
                selected_savol_global[i] = 'true';
            else
                selected_savol_global[i] = 'false';

            if ($('#passport').val()) {
                selected_savol_global[i + 1] = 'true';
            } else {
                selected_savol_global[i + 1] = 'false';
            }
            hide_buttons(selected_savol_global)
        }
    });
})(jQuery);
