<?php $this->extend('Layout/Layout.php'); ?>

<!-- Sections -->


<?php $this->section('content'); ?>

<div class="content_box" style="max-width: 500px;">
    <div class="initial mb1c5">
        <div class="title">
            Ganti password
        </div>
    </div>

    <div class="form_target block">

        <div class="tx_field1">
            <div class="input_label">
                <label for="current_pass">
                    Password lama <span style="color: red">*</span>
                </label>
            </div>

            <div class="input_item">
                <input type="text" name="current_pass" id="current_pass" placeholder="Masukan password lama" value="">
            </div>
        </div>

        <div class="tx_field1 mt2">
            <div class="input_label">
                <label for="new_pass">
                    Password baru <span style="color: red">*</span>
                </label>
            </div>

            <div class="input_item">
                <input type="text" name="new_pass" id="new_pass" placeholder="Masukan password baru" value="">
            </div>
        </div>

        <div class="tx_field1 mt2 hide" style="max-width: 150px;">
            <div class="input_label">
                <label for="token">
                    Token <span style="color: red">*</span>
                </label>
            </div>

            <div class="input_item">
                <input type="text" name="token" id="token" placeholder="123456" value="">
            </div>
        </div>

        <button class="submit button1 mt2 wd100pc nwrap" style="max-width: 150px;">
            Ganti password
        </button>

    </div>
    <script type="text/javascript">
        // Submit form
        $('body')
            .on('click', '.form_target button.submit', function(e) {
                let button = this;
                let formTarget = $(button).parents('.form_target')[0];

                $(formTarget)
                    .find('*[class*="tx_field"], *[class*="qty_field"]')
                    .attr('ptx_validation', '');

                $.formCollect
                    .target(formTarget)
                    .required([{
                        name: 'current_pass'
                    }, {
                        name: 'new_pass'
                    }])
                    .collect(
                        (json) => {

                            let url = $.makeURL.api().addPath('account/security/password').href;

                            $(button).buttonOnLoading(true);

                            // Start send 
                            $.ajax({
                                type: (typeof json['token'] == 'undefined') ? 'PUT' : 'POST',
                                url: url,
                                headers: {
                                    'Authorization': 'Bearer ' + jsCookie.get('_PTS-Auth:Token'),
                                    'Content-type': 'application/json',
                                },
                                data: JSON.stringify(json),
                                success: function(res) {

                                    if (res.code == 200) {

                                        if (typeof json['token'] == 'undefined') {

                                            $(formTarget)
                                                .find('input[name="token"]').val(res.data.token)
                                            // .parents('*[class*="tx_field"], *[class*="qty_field"]').removeClass('hide');

                                            setTimeout(() => {

                                                $(button).click();
                                            }, 500);
                                        } else {

                                            $.notif().success("Ganti password berhasil");

                                            setTimeout(() => {

                                                location.reload();
                                            }, 500);
                                        }
                                    }

                                    $(button).buttonOnLoading(false);
                                },
                                error: function({
                                    responseJSON
                                }) {

                                    if (typeof responseJSON === 'undefined') {
                                        $(button).buttonOnLoading(false);
                                        return;
                                    }

                                    switch (responseJSON.report_id) {
                                        case 'ASPR2':

                                            let currentPass = $(formTarget).find('input[name="current_pass"]').parents('*[class*="tx_field"], *[class*="qty_field"]');
                                            $(currentPass)
                                                .attr('ptx_validation', 'invalid')
                                                .find('.notif_text').remove().end()
                                                .append(`
                                                    <div class="notif_text">
                                                        Password lama salah
                                                    </div>
                                                `);
                                            break;
                                        default:
                                            $.notif().error('Kode error (' + responseJSON.report_id + ')');
                                            break;
                                    }

                                    $(button).buttonOnLoading(false);
                                }
                            });
                        },
                        (err) => {
                            console.log(err);

                            if ($.inArray(err.code, [undefined, null, '']) < 0 &&
                                err.code == 'REQUIRED_FORM_IS_EMPTY') {

                                let errDomParent = $(err.form.dom).parents('*[class*="tx_field"], *[class*="qty_field"]');

                                $(errDomParent).attr('ptx_validation', 'invalid')
                                    .find('input, textarea, select').focus()
                                    .end();
                            }
                        }
                    );
            });
    </script>
</div>

<?php $this->endSection('content'); ?>