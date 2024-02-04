<?php $this->section('state_code:WT_VALIDATION'); ?>

<div class="content_box mb3 p0">
    <div class="context_box warning">
        <div class="tx_bg0c5 tx_w_bold">
            Belum aktif
        </div>

        <div class="mt2">
            Status keanggotaan belum aktif. Data anda masih menunggu validasi dari pengurus koperasi.
        </div>
    </div>
</div>

<?php $this->endSection('state_code:WT_VALIDATION'); ?>

<?php $this->section('state_code:REGISTER_REJECT'); ?>

<div class="content_box mb3 p0">
    <div class="context_box error">
        <div class="tx_bg0c5 tx_w_bold">
            Data ditolak
        </div>

        <div class="mt2">
            Permohonan pendaftaran anda ditolak. Silakan perbaiki data yang sesuai lalu kirim ulang permohonan pendaftaran.
        </div>

        <a href="" class="button1 wd_fit mt2">
            Kirim ulang permohonan
        </a>
    </div>
</div>

<?php $this->endSection('state_code:REGISTER_REJECT'); ?>

<?php $this->section('state_code:WT_PAYMENT'); ?>

<div class="content_box mb3 p0">
    <div class="context_box success">
        <div class="tx_bg0c5 tx_w_bold">
            Data diterima
        </div>

        <div class="mt2">
            Selamat! Permohonan anda diterima.
            <br>
            Silakan selesaikan biaya pendaftaran untuk agar dapat menjadi anggota aktif.
        </div>

        <a href="" class="button1 wd_fit mt2">
            Bayar sekarang
        </a>
    </div>
</div>

<?php $this->endSection('state_code:WT_PAYMENT'); ?>