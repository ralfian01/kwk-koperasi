<?php $this->section('payment_status:PENDING'); ?>

<div class="content_box mb2 p0">
    <div class="context_box warning">
        <div class="tx_bg0c5 tx_w_bold">
            Menunggu validasi
        </div>

        <div class="mt2">
            Pembayaran simpanan anggota menunggu konfirmasi pengurus.
            <br>
            Segera konfirmasi status pembayaran ini.
        </div>
    </div>
</div>

<?php $this->endSection('payment_status:PENDING'); ?>

<?php $this->section('payment_status:VALID'); ?>

<div class="content_box mb2 p0">
    <div class="context_box success">
        <div class="tx_bg0c5 tx_w_bold">
            Diterima
        </div>

        <div class="mt2">
            Konfirmasi pembayaran diterima
        </div>
    </div>
</div>

<?php $this->endSection('payment_status:VALID'); ?>

<?php $this->section('payment_status:INVALID'); ?>

<div class="content_box mb2 p0">
    <div class="context_box error">
        <div class="tx_bg0c5 tx_w_bold">
            Ditolak
        </div>

        <div class="mt2">
            Konfirmasi pembayaran ditolak
        </div>
    </div>
</div>

<?php $this->endSection('payment_status:INVALID'); ?>


<?php $this->section('form:BEFORE_CONFIRM'); ?>

<div class="content_box">

    <div class="form_target block">

        <input type="hidden" name="id" value="<?= base64_encode($memberDepositPaymentData['deposit_id']); ?>">

        <div class="context_box info mb1c5">
            <span class="context">Info:</span> Pastikan metode pembayaran yang anda pilih sesuai dengan metode pembayaran yang anda gunakan.
        </div>

        <div class="tx_field1 mt2 wd50pc mb_wd100pc tb_wd100pc">
            <div class="input_label">
                <label for="payment_method">
                    Metode pembayaran <span style="color: red">*</span>
                </label>
            </div>

            <div class="input_item">
                <select name="payment_method">
                    <option value="" selected disabled>-- Pilih metode pembayaran --</option>
                    <option value="CASH">Tunai</option>
                    <option value="TRANSFER">Transfer</option>
                </select>
            </div>
        </div>

        <div class="tx_field1 mt1c5">
            <div class="input_label">
                <label>
                    Bukti pembayaran <span style="color: red">*</span>
                </label>
            </div>

            <div class="input_file_item" id="testung">

                <div class="input_instruction">
                    <div class="icon_instruction">
                        <i class="main ri-image-2-fill"></i>
                        <i class="drag ri-upload-2-fill"></i>
                    </div>

                    <div class="main_instruction">

                        <label for="evidence" class="input_trigger">
                            Pilih file
                        </label>
                        atau drag dan drop disini
                    </div>

                    <div class="sub_instruction">
                        PNG, JPG, JPEG, PDF up to 5MB
                    </div>
                </div>

                <div class="file_preview">
                    <!--  -->
                </div>

                <input id="evidence" name="evidence" type="file" accept="image/png, image/jpeg, image/jpg, application/pdf" required />
            </div>

            <div class="notif_text">
                Bukti pembayaran wajib diisi
            </div>
        </div>

        <div class="flex y_center x_start mt1 wd_fit">
            <i class="ri-information-line mr1"></i>
            <div class="flex_child">
                <span style="color: red;">*</span>wajib diisi
            </div>
        </div>

        <button class="submit button1 mt2 wd_fit">
            Kirim
        </button>

    </div>
    <script type="text/javascript">
        // Event
        $('body')
            .on('click', '.form_target button.submit', function(e) {
                let button = this;
                let formTarget = $(button).parents('.form_target')[0];

                $(formTarget)
                    .find('*[class*="tx_field"], *[class*="qty_field"]')
                    .attr('ptx_validation', '')

                $.formCollect
                    .target(formTarget)
                    .required([{
                        name: 'payment_method'
                    }, {
                        name: 'evidence'
                    }])
                    .collect(
                        (json) => {

                            let url = $.makeURL.api().addPath('registration/member/pay').href;
                            let formData = jsonToFormData(json);

                            $(button).buttonOnLoading(true);

                            // Start send 
                            $.ajax({
                                type: 'POST',
                                url: url,
                                headers: {
                                    'Authorization': 'Bearer ' + jsCookie.get('_PTS-Auth:Token'),
                                },
                                data: formData,
                                success: function(res) {
                                    if (res.code == 200) {
                                        $.notif().success("Berhasil mengirim bukti pembayaran");

                                        setTimeout(() => {
                                            location.reload();
                                        }, 150);
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
                                        case 'MDC2':
                                            $.notif().warning("Id simpanan tidak tersedia");
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

                                console.log(errDomParent);

                                $(errDomParent).attr('ptx_validation', 'invalid')
                                    .find('input, textarea, select').focus()
                                    .end();
                            }
                        }
                    );
            });
    </script>
</div>

<?php $this->endSection('form:BEFORE_CONFIRM'); ?>
