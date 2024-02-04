<?php $this->extend('Layout/Layout.php'); ?>

<!-- Sections -->

<?php $this->section('content'); ?>

<div class="block" style="max-width: 650px;">

    <div class="form_target content_box">
        <div class="tx_field1">
            <div class="input_label">
                <label for="amount">
                    Jumlah pengeluaran <span style="color: red">*</span>
                </label>
            </div>

            <div class="input_item">
                <div class="addon">
                    Rp.
                </div>
                <input type="text" name="amount" id="amount" placeholder="10.000" value="" ptx_format="currency" pattern="[0-9]*">
            </div>
        </div>

        <div class="tx_field1 mt2">
            <div class="input_label">
                <label for="description">
                    Deskripsi <span style="color: red">*</span>
                </label>
            </div>

            <div class="input_item">
                <textarea name="description" id="description" placeholder="Cth: Pengeluaran"></textarea>
            </div>
        </div>

        <div class="flex y_center x_start mt1 wd_fit">
            <i class="ri-information-line mr1"></i>
            <div class="flex_child">
                <span style="color: red;">*</span>wajib diisi
            </div>
        </div>

        <button class="submit button1 mt2 wd100pc" style="max-width: 150px;">
            Kirim
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
                        name: 'amount'
                    }, {
                        name: 'description'
                    }])
                    .collect(
                        (json) => {

                            json['amount'] = json['amount'].replaceAll('.', '');

                            // Override modal
                            $.modalBox
                                .overrideModal({
                                    overrideModal: {
                                        modalButton: {
                                            confirm: '<button class="button1 semi_color"></button>',
                                            cancel: '<button class="button1" style="--bt_bg: var(--colorRed); --bt_border_color: var(--colorRed);"></button>',
                                        }
                                    },
                                })
                                .setup({
                                    title: 'Tambah laporan pengeluaran?',
                                    description: 'Anda mungkin tidak dapat menghapus laporan',
                                    button: {
                                        confirmText: 'Ya, tambahkan',
                                        cancelText: 'Kembali',
                                    }
                                })
                                .open({
                                    onConfirm: (trigger, element) => {

                                        $(element.target).buttonOnLoading(true);

                                        // Default confirm deposit url
                                        let url = $.makeURL.api().addPath('manage/finance_report/outcome').href;

                                        // Start send 
                                        $.ajax({
                                            type: 'POST',
                                            url: url,
                                            headers: {
                                                'Authorization': 'Bearer ' + jsCookie.get('_PTS-Auth:Token'),
                                                'Content-Type': 'application/json'
                                            },
                                            data: JSON.stringify(json),
                                            success: function(res) {
                                                if (res.code == 200) {
                                                    $.notif().success("Tambah laporan berhasil");

                                                    setTimeout(() => {
                                                        history.go(-1);
                                                    }, 500);
                                                }

                                                $(element.target).buttonOnLoading(false);
                                            },
                                            error: function({
                                                responseJSON
                                            }) {

                                                if (typeof responseJSON === 'undefined') {
                                                    $(element.target).buttonOnLoading(false);
                                                    return;
                                                }

                                                switch (responseJSON.report_id) {
                                                    default:
                                                        $.notif().error('Kode error (' + responseJSON.report_id + ')');
                                                        break;
                                                }

                                                $(element.target).buttonOnLoading(false);
                                            }
                                        });
                                    },
                                    onCancel: (trigger) => {

                                        trigger.close();
                                    }
                                })
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