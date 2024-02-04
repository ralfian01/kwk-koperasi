<?php $this->section('privilege:MEMBER_MANAGE_UPDATE'); ?>

<div class="content_box mb3 p0">
    <div class="context_box warning">
        <div class="tx_bg0c5 tx_w_bold">
            Edit data anggota
        </div>

        <a href="<?= member_url('manage/member/manual_input/' . base64_encode($memberData['member_id'])); ?>" class="button1 mt1 wd_fit">
            Edit data
        </a>
    </div>
</div>

<?php $this->endSection('privilege:MEMBER_MANAGE_UPDATE'); ?>