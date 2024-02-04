<?php $this->extend('Layout/Layout.php'); ?>

<!-- Sections -->
<?php include_once(__DIR__ . "/sections.php"); ?>

<?php $this->section('content'); ?>

<div class="block" style="max-width: 700px">
    <?= $this->renderSection("payment_status:{$memberDepositPaymentData['payment_status']}"); ?>

    <div class="content_box mb2 p0">
        <div class="context_box netral1 netral1">
            <h4 class="m0">
                Data pembayaran simpanan
            </h4>

            <table class="wd100pc">
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="vertical-align: top;">
                        Jenis simpanan
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberDepositPaymentData['deposit_type'] == 'BASE' ? 'Simpanan pokok' : ''; ?>
                        <?= $memberDepositPaymentData['deposit_type'] == 'MANDATORY' ? 'Simpanan wajib' : ''; ?>
                        <?= $memberDepositPaymentData['deposit_type'] == 'VOLUNTARY' ? 'Simpanan sukarela' : ''; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="vertical-align: top;">
                        Jumlah
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= 'Rp. ' . rupiah($memberDepositPaymentData['deposit_amount']); ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="vertical-align: top;">
                        Tanggal
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= convertYmdhi($memberDepositPaymentData['created_at']); ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="content_box">
        <h3 class="m0">
            Data anggota
        </h3>

        <table class="wd100pc">
            <tr>
                <td class="py0c5 pr1 nwrap tx_sm1" style="vertical-align: top; width: 250px;">
                    Metode pembayaran
                </td>
                <td class="pl1 tx_w_bolder">
                    <?= $memberDepositPaymentData['payment_method'] == 'CASH' ? 'Tunai' : ''; ?>
                    <?= $memberDepositPaymentData['payment_method'] == 'TRANSFER' ? 'Transfer' : ''; ?>
                </td>
            </tr>
            <tr>
                <td class="py0c5 pr1 nwrap tx_sm1" style="vertical-align: top; width: 250px;">
                    Bukti pembayaran
                </td>
                <td class="pl1 tx_w_bolder">
                    <button id="showImage" class="button1 semi_color wd_fit">
                        <i class="ri-eye-2-line mr0c5"></i> Lihat bukti pembayaran
                    </button>
                </td>
            </tr>
        </table>

        <h3 class="m0 mt1">
            Data anggota
        </h3>

        <table class="wd100pc">
            <tr>
                <td class="py0c5 pr1 nwrap tx_sm1" style="vertical-align: top; width: 250px;">
                    Nama anggota
                </td>
                <td class="pl1 tx_w_bolder">
                    <?= $memberDepositPaymentData['member_fullname']; ?>
                </td>
            </tr>
            <tr>
                <td class="py0c5 pr1 nwrap tx_sm1" style="vertical-align: top; width: 250px;">
                    Nomor anggota
                </td>
                <td class="pl1 tx_w_bolder">
                    <?= $memberDepositPaymentData['member_register_number'] == null ? '-' : $memberDepositPaymentData['member_register_number']; ?>
                </td>
            </tr>
        </table>

        <?php if ($memberDepositPaymentData['payment_status'] == 'PENDING') : ?>

            <form method="post" id="confirmation" class="form_target flex mt2 wd_fit flex_gap1">
                <input type="hidden" name="id" value="<?= base64_encode($memberDepositPaymentData['deposit_id']); ?>">

                <input type="hidden" name="member_register_number" value="<?= base64_encode($memberDepositPaymentData['member_register_number']); ?>">
                <input type="hidden" name="member_id" value="<?= base64_encode($memberDepositPaymentData['member_id']); ?>">

                <button type="submit" name="accept" value="Y" class="submit flex_child fits button1 nwrap">
                    Terima pembayaran
                </button>

                <button type="submit" name="accept" value="N" class="submit flex_child button1 wd_fit" style="--bt_bg: var(--colorRed); --bt_border_color: var(--colorRed);">
                    Tolak
                </button>
            </form>
            <script type="text/javascript">
                // Event
                $('body')
                    .on('submit', 'form#confirmation', function(e) {
                        e.preventDefault();
                    })
                    .on('click', 'form#confirmation button[type="submit"]', function(e) {

                        let button = this;
                        let formTarget = $(button).parents('form')[0];
                        $.formCollect
                            .target(formTarget)
                            .collect((json) => {
                                json['accept'] = $(button).val();

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
                                        title: json['accept'] == 'Y' ? 'Terima pembayaran?' : 'Tolak pembayaran?',
                                        description: 'Status pembayaran dan simpanan akan berubah',
                                        button: {
                                            confirmText: json['accept'] == 'Y' ? 'Terima' : 'Tolak',
                                            cancelText: 'Batalkan',
                                        }
                                    })
                                    .open({
                                        onConfirm: (trigger, element) => {

                                            $(element.target).buttonOnLoading(true);

                                            // Default confirm deposit url
                                            let url = $.makeURL.api().addPath('manage/member/deposit_payment/confirm/' + json['id']).href;

                                            if (typeof json['member_register_number'] === 'undefined') {
                                                // Use confirm deposit for new member
                                                url = $.makeURL.api().addPath('manage/member/register/confirm_payment/' + json['member_id']).href;

                                                delete json['id'];
                                            }

                                            // Start send 
                                            $.ajax({
                                                type: 'PUT',
                                                url: url,
                                                headers: {
                                                    'Authorization': 'Bearer ' + jsCookie.get('_PTS-Auth:Token'),
                                                    'Content-Type': 'application/json'
                                                },
                                                data: JSON.stringify(json),
                                                success: function(res) {
                                                    if (res.code == 200) {
                                                        $.notif().success("Berhasil memperbarui status pembayaran");

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
                                                        default:
                                                            $.notif().error('Kode error (' + responseJSON.report_id + ')');
                                                            break;
                                                    }

                                                    $(button).buttonOnLoading(false);
                                                }
                                            });
                                        },
                                        onCancel: (trigger) => {

                                            trigger.close();
                                        }
                                    })
                            });
                    });
            </script>

        <?php elseif (in_array($memberDepositPaymentData['payment_status'], ['VALID', 'INVALID'])) : ?>

            <h3 class="m0 mt1">
                Dikonfirmasi oleh
            </h3>

            <table class="wd100pc">
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="vertical-align: top; width: 250px;">
                        Nama pengurus
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <a href="<?= member_url("manage/member/" . base64_encode($memberDepositPaymentData['verifier']['member_id'])); ?>" class="orig_udline">
                            <?= $memberDepositPaymentData['verifier']['fullname']; ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="vertical-align: top; width: 250px;">
                        Nomor anggota
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberDepositPaymentData['verifier']['register_number']; ?>
                    </td>
                </tr>
            </table>

        <?php endif; ?>
    </div>
</div>


<?php $this->endSection('content'); ?>