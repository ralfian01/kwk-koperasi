<?php $this->extend('Layout/Layout.php'); ?>

<!-- Sections -->

<?php $this->section('content'); ?>

<div class="content_box">
    <form method="get" class="search_bar">

        <input type="hidden" name="pagination" value="<?= $filter['pagination'] ?? '20,0'; ?>">

        <div class="search_gr" style="max-width: 150px;">
            <div class="initial">Status</div>

            <div class="gr_box">
                <?php $actived = $filter['actived'] ?? []; ?>

                <input type="hidden" name="actived" value="">

                <label for="actived:1" class="checkbox1 wd100pc" <?= in_array(1, $actived) ? 'checked' : ''; ?>>
                    <input id="actived:1" type="checkbox" name="actived" value="1">
                    <div class="name">Aktif</div>
                    <div class="box"></div>
                </label>

                <label for="actived:0" class="checkbox1 wd100pc mt1" <?= in_array(0, $actived) ? 'checked' : ''; ?>>
                    <input id="actived:0" type="checkbox" name="actived" value="0">
                    <div class="name">Non-aktif</div>
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

    <?php if (isset($memberList) && count($memberList) >= 1) : ?>

        <table class="table1">
            <thead>
                <tr>
                    <th class="tx_al_ct" style="width: 70px;">Status</th>
                    <th class="tx_al_left" style="width: 200px;">Nomor anggota</th>
                    <th class="tx_al_left" style="width: 300px;">Nama anggota</th>
                    <th class="tx_al_left">Nomor telepon</th>
                    <th style="width: 50px;">/</th>
                </tr>
            </thead>
            <tbody>
                <?php $index = 1; ?>
                <?php foreach ($memberList as $list) : ?>

                    <tr class="<?= $index % 2 == 0 ? 'even' : 'odd'; ?>">
                        <td class="px0 tx_al_ct tx_sm2 tx_w_black <?= !$list['actived'] ? 'clr_context error' : ''; ?>" style="border: none;">
                            <?= $list['actived'] ? 'Aktif' : 'Non-aktif'; ?>
                        </td>
                        <td>
                            <?= $list['register_number']; ?>
                        </td>
                        <td>
                            <?= $list['identity']['fullname']; ?>
                        </td>
                        <td>
                            <?= censorText(printPhoneNumber('62', '0', $list['phone_number']), 6); ?>
                        </td>
                        <td class="flex y_center x_center">
                            <a href="<?= member_url('manage/member/' . base64_encode($list['member_id'])); ?>" class="button1 bt_small">
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

            <?php $nextButton = ($pagination[1] + 1 >= 0 && isset($memberList) && count($memberList) >= $pagination[0]); ?>
            <a href="<?= clean_current_url("?pagination={$pagination[0]}," . $pagination[1] + 1); ?>" class="button1 semi_color bt_small wd_fit" style="<?= !$nextButton ? 'visibility: hidden' : ''; ?>">
                <i class="ri-arrow-right-s-line"></i>
            </a>

        </div>
    </div>

</div>

<?php $this->endSection('content'); ?>