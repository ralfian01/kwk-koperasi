<?php $this->section('state_code:WT_VALIDATION'); ?>

<div class="content_box mb3 p0">
    <form method="post" id="confirmation" class="context_box info">
        <input type="hidden" name="id" value="<?= base64_encode($newMemberData['member_id']); ?>">

        <div class="tx_bg0c5 tx_w_bold">
            Menunggu validasi data
        </div>

        <div class="mt2">
            Calon anggota baru menunggu validasi data dari pengurus
        </div>

        <div class="flex flex_gap1">
            <button type="submit" name="accept" value="Y" class="button1 mt1 wd_fit">
                Terima data
            </button>

            <button type="submit" name="accept" value="N" class="button1 mt1 wd_fit" style="--bt_bg: var(--colorRed); --bt_border_color: var(--colorRed);">
                Tolak
            </button>
        </div>
    </form>
    <script type="text/javascript">
        // Events
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

                        $.modalBox
                            .setup({
                                title: json['accept'] == 'Y' ? 'Terima data?' : 'Tolak data?',
                                description: 'Status calon anggota akan berubah',
                                button: {
                                    confirmText: json['accept'] == 'Y' ? 'Terima' : 'Tolak',
                                    cancelText: 'Batalkan',
                                }
                            })
                            .open({
                                onConfirm: (trigger, element) => {

                                    $(element.target).buttonOnLoading(true);

                                    let url = $.makeURL.api().addPath('manage/member/register/confirm/' + json['id']).href;

                                    $.ajax({
                                        type: 'PUT',
                                        url: url,
                                        headers: {
                                            'Authorization': 'Bearer ' + jsCookie.get('_PTS-Auth:Token'),
                                            'Content-type': 'application/json'
                                        },
                                        data: JSON.stringify(json),
                                        success: function(res) {

                                            // console.log(res);

                                            if (res.code == 200) {
                                                location.reload();
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
                    });
            });
    </script>
</div>

<?php $this->endSection('state_code:WT_VALIDATION'); ?>

<?php $this->section('state_code:REGISTER_REJECT'); ?>

<div class="content_box mb3 p0">
    <div class="context_box error">
        <div class="tx_bg0c5 tx_w_bold">
            Data ditolak
        </div>

        <div class="mt2">
            Data calon anggota ditolak
        </div>
    </div>
</div>

<?php $this->endSection('state_code:REGISTER_REJECT'); ?>

<?php $this->section('state_code:WT_PAYMENT'); ?>

<div class="content_box mb3 p0">
    <div class="context_box info">
        <div class="tx_bg0c5 tx_w_bold">
            Data diterima
        </div>

        <div class="mt2">
            Data calon anggota diterima. Sekarang menunggu calon anggota membayar biaya pendaftaran.
        </div>
    </div>
</div>

<?php $this->endSection('state_code:WT_PAYMENT'); ?>

<?php $this->section('state_code:WT_PAYMENT_VALIDATION'); ?>

<div class="content_box mb3 p0">
    <div class="context_box info">
        <div class="tx_bg0c5 tx_w_bold">
            Menunggu verifikasi pembayaran
        </div>

        <div class="mt2">
            Calon anggota telah melakukan pembayaran biaya pendaftaran. Sekarang menunggu verifikasi dari pengurus.
        </div>

        <a href="<?= member_url('manage/deposit/'); ?>" class="button1 mt1 wd_fit">
            Cek pembayaran
        </a>
    </div>
</div>

<?php $this->endSection('state_code:WT_PAYMENT_VALIDATION'); ?>