<?php $this->extend('Layout/Layout.php'); ?>

<!-- Sections -->
<?php include_once(__DIR__ . "/sections.php"); ?>

<?php $memberDepositData['payment']['status'] == null ? $memberDepositData['payment']['status'] = 'NOT_PAID' : ''; ?>


<?php $this->section('content'); ?>

<div class="flex flex_gap2 mb_flex_col">
    <div class="flex_child">

        <?= $this->renderSection("payment_status:{$memberDepositData['payment']['status']}"); ?>

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
                            <?= $memberDepositData['deposit_type'] == 'BASE' ? 'Simpanan pokok' : ''; ?>
                            <?= $memberDepositData['deposit_type'] == 'MANDATORY' ? 'Simpanan wajib' : ''; ?>
                            <?= $memberDepositData['deposit_type'] == 'VOLUNTARY' ? 'Simpanan sukarela' : ''; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="py0c5 pr1 nwrap tx_sm1" style="vertical-align: top;">
                            Jumlah
                        </td>
                        <td class="pl1 tx_w_bolder">
                            <?= 'Rp. ' . rupiah($memberDepositData['deposit_amount']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="py0c5 pr1 nwrap tx_sm1" style="vertical-align: top;">
                            Tanggal rilis
                        </td>
                        <td class="pl1 tx_w_bolder">
                            <?= convertYmdhi($memberDepositData['created_at']); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <?php if ($memberDepositData['payment']['status'] != 'NOT_PAID') : ?>

            <?= $this->renderSection('form:AFTER_CONFIRM'); ?>

        <?php else : ?>

            <?= $this->renderSection('form:BEFORE_CONFIRM'); ?>

        <?php endif; ?>
    </div>

    <style>
        @media only screen and (max-width: 600px) {
            .flex_child {
                max-width: none !important;
            }
        }
    </style>

    <div class="flex_child wd100pc" style="max-width: 450px;">
        <div class="content_box">
            <div class="initial mb1c5">
                <div class="title">
                    Cara pembayaran
                </div>
            </div>

            <div class="collapse1 clp_anim">
                <div class="clp_init">
                    Bayar Tunai
                </div>
                <div class="clp_container">
                    <ol>
                        <li>
                            <b>Bayar secara langsung</b>
                            <br>
                            Pembayaran dilakukan secara langsung ke pengurus koperasi.
                        </li>
                        <li>
                            <b>Tunggu konfirmasi</b>
                            <br>
                            Tunggu sampai pengurus mengkonfirmasi pembayaran anda.
                        </li>
                        <li>
                            <b>Selesai</b>
                            <br>
                            Setelah pembayaran diverifikasi, status pembayaran selesai dan status simpanan berubah.
                        </li>
                    </ol>
                </div>
            </div>

            <div class="collapse1 clp_anim mt2">
                <div class="clp_init">
                    Bayar Transfer
                </div>
                <div class="clp_container">
                    <ol>
                        <li>
                            <b>Transfer dana</b>
                            <br>
                            Transfer dana via Mobile banking, SMS Banking, atau ATM.
                            <br>
                            Anda juga bisa transfer dana melalui e-wallet Dana, OVO, atau GoPay.
                            <br>
                            Pastikan jumlah transfer sesuai.
                        </li>
                        <li>
                            <b>Upload bukti bayar</b>
                            <br>
                            Upload bukti pembayaran.
                            <br>
                            Pastikan metode pembayaran yang anda pilih sesuai dengan metode pembayaran yang anda gunakan.
                        </li>
                        <li>
                            <b>Tunggu konfirmasi</b>
                            <br>
                            Tunggu sampai pengurus mengkonfirmasi pembayaran anda.
                        </li>
                        <li>
                            <b>Selesai</b>
                            <br>
                            Setelah pembayaran diverifikasi, status pembayaran selesai dan status simpanan berubah.
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <?php if (
            $memberDepositData['payment']['status'] == 'NOT_PAID'
            && in_array('MEMBER_DEPOSIT_CANCEL', $auth['privilege'])
        ) : ?>

            <?= $this->renderSection('cancel_deposit'); ?>

        <?php endif; ?>
    </div>
</div>


<?php $this->endSection('content'); ?>