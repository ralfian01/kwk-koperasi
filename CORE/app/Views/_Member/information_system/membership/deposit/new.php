<?php $this->extend('Layout/Layout.php'); ?>

<!-- Sections -->

<?php
$depositInfo = [
    'BASE' => <<<HTML
        Simpanan pokok adalah simpanan yang dibayar sebanyak 1 kali pada saat pendaftaran sebagai anggota koperasi.
        <br>
        Simpanan tidak dapat diambil selama menjadi anggota.
    HTML,
    'MANDATORY' => <<<HTML
        Simpanan wajib adalah simpanan yang dibayar setiap bulan.
    HTML,
    'VOLUNTARY' => <<<HTML
        Simpanan sukarela adalah simpanan yang dibayar secara sukarela dan jumlahnya ditentukan sendiri oleh anggota.
    HTML,
];
?>


<?php $this->section('content'); ?>

<div class="content_box" style="max-width: 500px;">

    <div class="form_target block">

        <input type="hidden" name="member_id" value="<?= base64_encode($member['member_id']); ?>">

        <div class="tx_field1">
            <div class="input_label">
                <label for="deposit_type">
                    Jenis simpanan <span style="color: red">*</span>
                </label>
            </div>

            <div class="input_item">
                <select name="deposit_type">
                    <option value="" selected disabled>-- Pilih jenis simpanan --</option>
                    <option value="BASE" disabled>Simpanan pokok</option>
                    <option value="MANDATORY">Simpanan wajib</option>
                    <option value="VOLUNTARY">Simpanan sukarela</option>
                </select>
            </div>
        </div>

        <?php foreach ($deposit as $value) : ?>

            <input type="hidden" name="deposit_amount_template" id="<?= $value['deposit_code']; ?>" value="<?= $value['deposit_amount']; ?>">

        <?php endforeach; ?>

        <!-- Deposit info - start -->

        <?php foreach ($depositInfo as $dpKey => $dpVal) : ?>

            <div id="<?= $dpKey; ?>" class="deposit_info context_box info p0c5 tx_sm1 mt1 hide">
                <span class="context">Info: </span>
                <?= $dpVal; ?>
            </div>

        <?php endforeach; ?>

        <!-- Deposit info - end -->

        <div class="tx_field1 mt2">
            <div class="input_label">
                <label for="amount">
                    Jumlah <span style="color: red">*</span>
                </label>
            </div>

            <div class="input_item">
                <div class="addon">
                    Rp.
                </div>
                <input type="text" name="amount" id="amount" placeholder="10000" value="" ptx_format="currency" pattern="[0-9]">
            </div>
        </div>

        <button class="submit button1 mt2 wd100pc nwrap" style="max-width: 150px;">
            Bayar simpanan
        </button>

    </div>
    <script type="text/javascript">
        // Fill in amount with deposit fixed amount
        $('body')
            .on('input', '.form_target select[name="deposit_type"]', function(e) {
                let value = this.value;
                let form = $(this).parents('.form_target')[0];
                let amount = $(form).find('input[name="deposit_amount_template"]#' + value).val();

                // Show deposit info
                $(form).find('.deposit_info').addClass('hide');

                $(form).find('.deposit_info#' + value).removeClass('hide');

                if (amount > 0) {
                    $(form).find('input[name="amount"]')
                        .val(amount)
                        .attr('disabled', '')
                        .change();

                    return;
                }

                $(form).find('input[name="amount"]')
                    .val('')
                    .removeAttr('disabled');

                return;
            });

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
                        name: 'deposit_type'
                    }, {
                        name: 'amount'
                    }])
                    .collect(
                        (json) => {

                            json['amount'] = json['amount'].replaceAll('.', '');

                            let url = $.makeURL.api().addPath('member/deposit').href;

                            $(button).buttonOnLoading(true);

                            // Start send 
                            $.ajax({
                                type: 'PUT',
                                url: url,
                                headers: {
                                    'Authorization': 'Bearer ' + jsCookie.get('_PTS-Auth:Token'),
                                    'Content-type': 'application/json',
                                },
                                data: JSON.stringify(json),
                                success: function(res) {
                                    if (res.code == 200) {
                                        $.notif().success("Permintaan berhasil");

                                        setTimeout(() => {
                                            history.go(-1);
                                        }, 500);
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