<?php $this->extend('Layout/Layout.php'); ?>

<!-- Sections -->

<?php $this->section('content'); ?>

<div class="content_box">
    <form method="get" class="search_bar">

        <input type="hidden" name="pagination" value="<?= $filter['pagination'] ?? '20,0'; ?>">

        <div class="search_gr" style="max-width: 250px;">
            <div class="initial">Status</div>

            <div class="gr_box">
                <?php $status = $filter['status'] ?? []; ?>

                <input type="hidden" name="status" value="">

                <label for="status:WT_VALIDATION" class="checkbox1 wd100pc" <?= in_array('WT_VALIDATION', $status) ? 'checked' : ''; ?>>
                    <input id="status:WT_VALIDATION" type="checkbox" name="status" value="WT_VALIDATION">
                    <div class="name">Menunggu validasi data</div>
                    <div class="box"></div>
                </label>

                <label for="status:REGISTER_REJECT" class="checkbox1 wd100pc mt1" <?= in_array('REGISTER_REJECT', $status) ? 'checked' : ''; ?>>
                    <input id="status:REGISTER_REJECT" type="checkbox" name="status" value="REGISTER_REJECT">
                    <div class="name">Registrasi Ditolak</div>
                    <div class="box"></div>
                </label>

                <label for="status:WT_PAYMENT" class="checkbox1 wd100pc mt1" <?= in_array('WT_PAYMENT', $status) ? 'checked' : ''; ?>>
                    <input id="status:WT_PAYMENT" type="checkbox" name="status" value="WT_PAYMENT">
                    <div class="name">Menunggu pembayaran</div>
                    <div class="box"></div>
                </label>

                <label for="status:WT_PAYMENT_VALIDATION" class="checkbox1 wd100pc mt1" <?= in_array('WT_PAYMENT_VALIDATION', $status) ? 'checked' : ''; ?>>
                    <input id="status:WT_PAYMENT_VALIDATION" type="checkbox" name="status" value="WT_PAYMENT_VALIDATION">
                    <div class="name">Menunggu validasi pembayaran</div>
                    <div class="box"></div>
                </label>
            </div>
        </div>

        <input class="search_item" type="search" name="fullname" value="<?= $filter['fullname'] ?? ''; ?>" placeholder="Cari anggota dari nama lengkap">

        <button type="submit" class="search_item fits button1" style="border: none;">
            <i class="ri-search-2-line mr0c5"></i> Cari
        </button>

        <script type="text/javascript">
            // Event
            let contentFoldableContainer = $('body').find('.foldable_container .content');

            // Open or close search group
            $(contentFoldableContainer)
                .on('click', function(e) {
                    let element = e.target;

                    if ($(element).parents('.search_gr').length <= 0) {
                        $(contentFoldableContainer)
                            .find('.search_bar .search_gr').removeClass('op');

                        return;
                    } else if ($.inArray('initial', element.classList) >= 0) {
                        $(element).parents('.search_gr').toggleClass('op');

                        return;
                    }
                });

            // Search group selected checkboxes
            $(window).on('load input', function() {
                let searchBar = $('body').find('.search_bar');
                let searchGroup = $(searchBar).find('.search_gr');

                $(searchGroup).each(function(key, groupElement) {
                    let initial = $(groupElement).find('.initial');
                    let groupTitle = $(initial).html();

                    let checkedCheckbox = $(groupElement).find('.gr_box')
                        .find('input:checked').length;

                    groupTitle = groupTitle.replace(/\(\d+\)/g, '');
                    groupTitle += `(${checkedCheckbox})`;

                    $(initial).html(groupTitle);
                });

            });

            // Submit search bar
            $(contentFoldableContainer)
                .on('submit', '.search_bar', function(e) {
                    e.preventDefault();

                    let form = e.target;
                    let data = $(form).serializeArray();
                    let json = {};

                    $.each(data, function(key, value) {
                        if (typeof json[value['name']] === 'undefined') {
                            json[value['name']] = value['value'] ?? '';
                        } else {
                            json[value['name']] += ',' + value['value'] ?? '';
                        }
                    });

                    // Convert collected json to query parameter
                    let jsonQueryParam = null;
                    $.each(json, function(queryKey, queryValue) {
                        if (jsonQueryParam == null) {
                            jsonQueryParam = `${queryKey}=${queryValue}`;
                        } else {
                            jsonQueryParam += `&${queryKey}=${queryValue}`;
                        }
                    });

                    window.location.href = '?' + jsonQueryParam;
                });
        </script>
    </form>

    <?php if (isset($newMemberList) && count($newMemberList) >= 1) : ?>

        <table class="table1">
            <thead>
                <tr>
                    <th class="tx_al_ct" style="width: 100px;">Status</th>
                    <th class="tx_al_left" style="width: 300px;">Nama anggota</th>
                    <th class="tx_al_left">Tanggal pendaftaran</th>
                    <th style="width: 50px;">/</th>
                </tr>
            </thead>
            <tbody>
                <?php $index = 1; ?>
                <?php foreach ($newMemberList as $list) : ?>

                    <tr class="<?= $index % 2 == 0 ? 'even' : 'odd'; ?>">
                        <?php
                        $colorContext = 'warning';
                        if (in_array($list['pm_metaState']['code'], ['REGISTER_REJECT'])) {
                            $colorContext = 'error';
                        } else if (in_array($list['pm_metaState']['code'], ['WT_PAYMENT'])) {
                            $colorContext = 'netral1';
                        }
                        ?>

                        <td class="px0 tx_al_ct tx_sm2 tx_w_black clr_context <?= $colorContext; ?>" style="border: none;">
                            <?= $list['pm_metaState']['code'] == 'WT_VALIDATION' ? 'Menunggu Validasi Data' : ''; ?>
                            <?= $list['pm_metaState']['code'] == 'WT_PAYMENT_VALIDATION' ? 'Menunggu Validasi Pembayaran' : ''; ?>
                            <?= $list['pm_metaState']['code'] == 'REGISTER_REJECT' ? 'Registrasi Ditolak' : ''; ?>
                            <?= $list['pm_metaState']['code'] == 'WT_PAYMENT' ? 'Menunggu Pembayaran' : ''; ?>
                        </td>
                        <td>
                            <?= $list['identity']['fullname']; ?>
                        </td>
                        <td>
                            <?php $registerDate = substr($list['register_date'], 0, 10); ?>
                            <?= convertYmd($registerDate); ?>
                        </td>
                        <td class="flex y_center x_center">
                            <a href="<?= member_url('manage/member/new/' . base64_encode($list['member_id'])); ?>" class="button1 bt_small">
                                <i class="ri-search-eye-line mr1"></i> Detail
                            </a>
                        </td>
                    </tr>

                    <?php $index++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else : ?>

        <div class="block tx_al_ct p4">
            Tidak ada data tersedia
        </div>

    <?php endif; ?>

    <!-- Pagination -->
    <div class="flex y_center x_between mt2">
        <?php $pagination = isset($filter['pagination']) ? explode(',', $filter['pagination']) : [1, 0]; ?>

        <div class="flex_child flex y_center x_start flex_gap1">
            <a href="<?= clean_current_url('?pagination=20,0'); ?>" class="button1 <?= $pagination[0] == 20 ? '' : 'invert_color'; ?> bt_small wd_fit borad0">
                20
            </a>
            <a href="<?= clean_current_url('?pagination=50,0'); ?>" class="button1 <?= $pagination[0] == 50 ? '' : 'invert_color'; ?> bt_small wd_fit borad0">
                50
            </a>
        </div>

        <div class="flex_child fits flex y_center x_start flex_gap2">
            <?php $prevButton = ($pagination[1] - 1 >= 0); ?>
            <a href="<?= clean_current_url("?pagination={$pagination[0]}," . $pagination[1] - 1); ?>" class="button1 semi_color bt_small wd_fit" style="<?= !$prevButton ? 'visibility: hidden' : ''; ?>">
                <i class="ri-arrow-left-s-line"></i>
            </a>

            <div class="flex_child fits">
                <?= $pagination[1] + 1; ?>
            </div>

            <?php $nextButton = ($pagination[1] + 1 >= 0 && isset($newMemberList) && count($newMemberList) >= $pagination[0]); ?>
            <a href="<?= clean_current_url("?pagination={$pagination[0]}," . $pagination[1] + 1); ?>" class="button1 semi_color bt_small wd_fit" style="<?= !$nextButton ? 'visibility: hidden' : ''; ?>">
                <i class="ri-arrow-right-s-line"></i>
            </a>

        </div>
    </div>

</div>

<?php $this->endSection('content'); ?>