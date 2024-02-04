<?php $this->extend(isset($auth) ? 'Layout/Layout' : '../BaseLayout/Layout'); ?>

<?php $this->section(isset($auth) ? 'content' : 'main'); ?>
<div class="flex y_center x_center p4" style="min-height: 100%; min-width: 100%;">
    <div class="flex_child fits tx_al_ct tx_lh1c5" style="max-width: 400px;">

        <img class="mxa" src="<?= asset_url('svg/original/403.svg'); ?>" style="height: 100px;" />

        <div class="mt5 tx_bg0c5 tx_w_bolder">
            Dilarang akses
        </div>

        <p class="mt3">
            Anda tidak memiliki izin untuk mengakses halaman ini
            <br>

            <?php if (!isset($auth)) : ?>

                <a href="<?= member_url(); ?>" style="border-bottom: 1px dashed rgb(var(--Col_theme-main)); color:rgb(var(--Col_theme-main));">
                    Kembali ke halaman utama
                </a>

            <?php endif; ?>
        </p>
    </div>
</div>
<?php $this->endSection(isset($auth) ?  'content' : 'main'); ?>