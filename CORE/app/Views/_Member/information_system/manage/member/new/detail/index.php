<?php $this->extend('Layout/Layout.php'); ?>

<!-- Sections -->
<?php include_once(__DIR__ . "/sections.php"); ?>

<?php $this->section('content'); ?>

<div class="block" style="max-width: 700px;">

    <?= $this->renderSection("state_code:{$newMemberData['pm_metaState']['code']}"); ?>

    <div class="content_box">
        <div class="initial mb1c5">
            <div class="title">
                Data anggota baru
            </div>
        </div>

        <div class="block">

            <?php if (!in_array($newMemberData['pm_metaState']['code'], ['WT_VALIDATION'])) : ?>

                <h4 class="m0">
                    Diverifikasi oleh:
                </h4>

                <table class="wd100pc mb2" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="py0c5 pr1 nwrap" style="width: 250px; vertical-align: top;">
                            Nama verifikator
                        </td>
                        <td class="pl1 tx_w_bolder">
                            <a href="" class="orig_udline">
                                <?= $newMemberData['verifier']['fullname']; ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="py0c5 pr1 nwrap" style="width: 250px; vertical-align: top;">
                            Nomor anggota
                        </td>
                        <td class="pl1 tx_w_bolder">
                            <?= $newMemberData['verifier']['register_number']; ?>
                        </td>
                    </tr>
                </table>

            <?php endif; ?>

            <h4 class="m0">
                Data calon anggota
            </h4>

            <table class="wd100pc">
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nama panggilan
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $newMemberData['nickname']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Alamat domisili
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $newMemberData['address_domicile']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nomor telepon
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= printPhoneNumber('62', '0', $newMemberData['phone_number']); ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nomor whatsapp
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= printPhoneNumber('62', '0', $newMemberData['wa_number']); ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Tanggal registrasi
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= convertYmd(substr($newMemberData['register_date'], 0, 10)); ?>
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
                        <?= $newMemberData['identity']['fullname']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nomor Induk Kependudukan
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $newMemberData['identity']['nik']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Tempat lahir
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $newMemberData['identity']['birth_place']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Tanggal lahir
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= convertYmd($newMemberData['identity']['birth_date']); ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Alamat
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $newMemberData['identity']['address']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Jenis kelamin
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $newMemberData['identity']['gender'] == 'M' ? 'Laki - laki' : 'Perempuan'; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nomor Pokok Wajib Pajak
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $newMemberData['identity']['npwp']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Foto KTP
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <button id="showImage" class="button1 semi_color wd_fit">
                            <i class="ri-eye-2-line mr0c5"></i> Lihat foto KTP
                        </button>
                    </td>
                </tr>
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
                        <?= $newMemberData['business']['registration_number']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        NPWP usaha
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $newMemberData['business']['business_npwp']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nama usaha
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $newMemberData['business']['business_name']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Alamat usaha
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $newMemberData['business']['business_address']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Nomor telepon usaha
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= printPhoneNumber('62', '0', $newMemberData['business']['business_phone_number']); ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Email usaha
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $newMemberData['business']['business_email']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Tanggal registrasi usaha
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <?= $newMemberData['business']['registration_date']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="py0c5 pr1 nwrap tx_sm1" style="width: 250px; vertical-align: top;">
                        Scan/foto NIB
                    </td>
                    <td class="pl1 tx_w_bolder">
                        <button id="showImage" class="button1 semi_color wd_fit">
                            <i class="ri-eye-2-line mr0c5"></i> Lihat NIB
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>


<?php $this->endSection('content'); ?>