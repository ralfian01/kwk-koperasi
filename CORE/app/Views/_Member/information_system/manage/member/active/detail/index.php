<?php $this->extend('Layout/Layout.php'); ?>

<!-- Sections -->
<?php include_once(__DIR__ . "/sections.php"); ?>

<?php $this->section('content'); ?>

<div class="block" style="max-width: 700px;">

    <?php if (in_array('MEMBER_MANAGE_UPDATE', $auth['privilege'])) : ?>

        <?= $this->renderSection("privilege:MEMBER_MANAGE_UPDATE"); ?>

    <?php endif; ?>

    <div class="content_box">
        <div class="initial mb1c5">
            <div class="title">
                Data anggota
            </div>
        </div>

        <div class="block">

            <h4 class="m0">
                Diverifikasi oleh:
            </h4>

            <table class="wd100pc mb2" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="py0c5 pr1 nwrap" style="width: 250px; vertical-align: top;">
                        Nama verifikator
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <a href="<?= base64_encode($memberData['verifier']['member_id']); ?>" class="orig_udline">
                            <?= $memberData['verifier']['fullname']; ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap" style="width: 250px; vertical-align: top;">
                        Nomor anggota
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['verifier']['register_number']; ?>
                    </td>
                </tr>
            </table>

            <h4 class="m0">
                Data calon anggota
            </h4>

            <table class="wd100pc">
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nomor anggota
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['register_number'] ?? 'Belum ada'; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nama panggilan
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['nickname']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Alamat domisili
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['address_domicile']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nomor telepon
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= printPhoneNumber('62', '0', $memberData['phone_number']); ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nomor whatsapp
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= printPhoneNumber('62', '0', $memberData['wa_number']); ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Tanggal registrasi
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= convertYmd(substr($memberData['register_date'], 0, 10)); ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="block mt2">
            <h4 class="m0">
                Data KTP calon anggota
            </h4>

            <table class="wd100pc">
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nama lengkap
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['identity']['fullname']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nomor Induk Kependudukan
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['identity']['nik']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Tempat lahir
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['identity']['birth_place']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Tanggal lahir
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= convertYmd($memberData['identity']['birth_date']); ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Alamat
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['identity']['address']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Jenis kelamin
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['identity']['gender'] == 'M' ? 'Laki - laki' : 'Perempuan'; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nomor Pokok Wajib Pajak
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['identity']['npwp']; ?>
                    </td>
                </tr>
                <!-- <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Foto KTP
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <button id="showImage" class="button1 semi_color wd_fit">
                            <i class="ri-eye-2-line mr0c5"></i> Lihat foto KTP
                        </button>
                    </td>
                </tr> -->
            </table>
        </div>

        <div class="block mt2">

            <h4 class="m0">
                Data usaha calon anggota
            </h4>

            <table class="wd100pc">
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nomor Induk Berusaha
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['business']['registration_number']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        NPWP usaha
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['business']['business_npwp']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nama usaha
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['business']['business_name']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Alamat usaha
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['business']['business_address']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nomor telepon usaha
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= printPhoneNumber('62', '0', $memberData['business']['business_phone_number']); ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Email usaha
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['business']['business_email']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Tanggal registrasi usaha
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $memberData['business']['registration_date']; ?>
                    </td>
                </tr>
                <!-- <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Scan/foto NIB
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <button id="showImage" class="button1 semi_color wd_fit">
                            <i class="ri-eye-2-line mr0c5"></i> Lihat NIB
                        </button>
                    </td>
                </tr> -->
            </table>
        </div>
    </div>
</div>


<?php $this->endSection('content'); ?>