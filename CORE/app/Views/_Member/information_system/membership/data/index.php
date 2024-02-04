<?php $this->extend('Layout/Layout.php'); ?>


<!-- Sections -->
<?php include_once(__DIR__ . "/sections.php"); ?>

<?php $this->section('content'); ?>

<?php if (!isset($member)) : ?>

    <!-- No member data -->
    <div class="content_box p0">
        <div class="context_box info">
            <div class="tx_bg0c5 tx_w_bold">
                Belum daftar
            </div>

            <div class="mt2">
                Anda belum terdaftar sebagai anggota koperasi. Silakan daftar terlebih dahulu untuk dapat mengakses fitur - fitur keanggotaan koperasi
            </div>

            <a href="<?= member_url('membership/register'); ?>" class="button1 mt1 wd_fit">
                Daftar sekarang
            </a>
        </div>
    </div>

<?php else : ?>

    <div class="flex y_start x_between flex_gap2 mb_flex_col">

        <style>
            @media only screen and (max-width: 600px) {
                .flex_child {
                    max-width: none !important;
                }
            }
        </style>

        <div class="flex_child" style="max-width: 300px; width: 100%;">

            <h3 class="mt0" style="color: var(--colorGreyDark);">
                Pengaturan
            </h3>

            <div class="content_box">
                <div class="initial mb1c5">
                    <div class="title">
                        Atur data anggota
                    </div>
                </div>

                <a href="<?= member_url('membership/setting/common'); ?>" class="button1 invert_color flex y_center x_between wd100pc borad0">
                    <div class="flex_child flex y_center x_start">
                        <i class="ri-contacts-line mr1"></i> Atur data anggota
                    </div>
                    <div class="flex_child fits">
                        <i class="ri-arrow-right-line"></i>
                    </div>
                </a>

                <a href="<?= member_url('membership/setting/identity'); ?>" class="button1 invert_color flex y_center x_between wd100pc borad0 mt1">
                    <div class="flex_child flex y_center x_start">
                        <i class="ri-user-line mr1"></i> Atur data identitas
                    </div>
                    <div class="flex_child fits">
                        <i class="ri-arrow-right-line"></i>
                    </div>
                </a>

                <a href="<?= member_url('membership/setting/business'); ?>" class="button1 invert_color flex y_center x_between wd100pc borad0 mt1">
                    <div class="flex_child flex y_center x_start">
                        <i class="ri-store-2-line mr1"></i> Atur data usaha
                    </div>
                    <div class="flex_child fits">
                        <i class="ri-arrow-right-line"></i>
                    </div>
                </a>
            </div>

            <!-- <div class="content_box context_box error mt2">
                <div class="initial mb1c5" style="color: var(--colorRed);">
                    <div class="title" style="color: inherit;">
                        Hapus data anggota
                    </div>
                </div>

                <a href="<?= member_url('membership/setting/business'); ?>" class="button1 wd100pc borad0" style="--bt_bg: var(--colorRed); --bt_border_color: var(--colorRed);">
                    Mundur dari keanggotaan
                </a>
            </div> -->

        </div>

        <div class="flex_child">

            <?php if (isset($member['pm_metaState'])) : ?>
                <?= $this->renderSection("state_code:{$member['pm_metaState']['code']}"); ?>
            <?php endif; ?>

            <h3 class="mt0" style="color: var(--colorGreyDark);">
                Data keanggotaan
            </h3>

            <!-- Identity -->
            <div class="content_box">
                <div class="initial mb1c5">
                    <div class="title">
                        Identitas
                    </div>
                </div>

                <div class="flex y_start x_between mb_flex_col">
                    <div class="flex_child mb_mb2">
                        <div class="tx_w_light tx_sm1">Nama panggilan</div>
                        <div class="tx_w_bolder">
                            <?= $member['nickname']; ?>
                        </div>

                        <div class="tx_w_light tx_sm1 mt0c5">Alamat domisili</div>
                        <div class="tx_w_bolder">
                            <?= $member['address_domicile']; ?>
                        </div>

                        <div class="tx_w_light tx_sm1 mt0c5">Nomor telepon</div>
                        <div class="tx_w_bolder">
                            <?= censorText(printPhoneNumber('62', '0', $member['phone_number']), 6); ?>
                        </div>

                        <div class="tx_w_light tx_sm1 mt0c5">Nomor whatsapp</div>
                        <div class="tx_w_bolder">
                            <?= censorText(printPhoneNumber('62', '0', $member['wa_number']), 6); ?>
                        </div>
                    </div>

                    <div class="flex_child">
                        <div class="tx_w_light tx_sm1">Nama lengkap</div>
                        <div class="tx_w_bolder">
                            <?= $member['identity']['fullname']; ?>
                        </div>

                        <div class="tx_w_light tx_sm1 mt0c5">NIK</div>
                        <div class="tx_w_bolder">
                            <?= censorText($member['identity']['nik'], 7); ?>
                        </div>

                        <div class="tx_w_light tx_sm1 mt0c5">Tempat, tanggal lahir</div>
                        <div class="tx_w_bolder">
                            <?= "{$member['identity']['birth_place']}, " . convertYmd($member['identity']['birth_date']); ?>
                        </div>

                        <div class="tx_w_light tx_sm1 mt0c5">Jenis kelamin</div>
                        <div class="tx_w_bolder">
                            <?= $member['identity']['gender'] == 'M' ? 'Laki - laki' : 'Perempuan'; ?>
                        </div>

                        <div class="tx_w_light tx_sm1 mt0c5">Alamat</div>
                        <div class="tx_w_bolder">
                            <?= $member['identity']['address']; ?>
                        </div>

                        <div class="tx_w_light tx_sm1 mt0c5">NPWP</div>
                        <div class="tx_w_bolder">
                            <?= censorText($member['identity']['npwp'], 10); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business -->
            <div class="content_box mt2">
                <div class="initial mb1c5">
                    <div class="title">
                        Data usaha
                    </div>
                </div>

                <div class="flex y_start x_between mb_flex_col">
                    <div class="flex_child">
                        <div class="tx_w_light tx_sm1">Nama usaha</div>
                        <div class="tx_w_bolder">
                            <?= $member['business']['business_name']; ?>
                        </div>

                        <div class="tx_w_light tx_sm1 mt0c5">Nomor induk berusaha</div>
                        <div class="">
                            <?= censorText($member['business']['registration_number'], 5); ?>
                        </div>

                        <div class="tx_w_light tx_sm1 mt0c5">NPWP Usaha</div>
                        <div class="tx_w_bolder">
                            <?= censorText($member['business']['business_npwp'], 10); ?>
                        </div>

                        <div class="tx_w_light tx_sm1 mt0c5">Tanggal pengesahan</div>
                        <div class="tx_w_bolder">
                            <?= convertYmd($member['business']['registration_date']); ?>
                        </div>
                    </div>

                    <div class="flex_child">
                        <div class="tx_w_light tx_sm1 mt0c5">Nomor telepon usaha</div>
                        <div class="tx_w_bolder">
                            <?= censorText(printPhoneNumber('62', '0', $member['business']['business_phone_number']), 6); ?>
                        </div>

                        <div class="tx_w_light tx_sm1 mt0c5">Email usaha</div>
                        <div class="tx_w_bolder">
                            <?= censorText($member['business']['business_email'], 10); ?>
                        </div>

                        <div class="tx_w_light tx_sm1 mt0c5">Alamat usaha</div>
                        <div class="tx_w_bolder">
                            <?= $member['business']['business_address']; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

<?php endif; ?>

<?php $this->endSection('content'); ?>