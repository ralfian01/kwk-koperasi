<?php $this->extend('Layout/Layout.php'); ?>

<!-- Sections -->

<?php $this->section('content'); ?>

<div class="content_box">

    <div class="initial mb1c5">
        <div class="title flex y_start x_between flex_gap1">
            <div class="flex_child">
                Daftar simpanan
            </div>

            <?php if (in_array('MEMBER_DEPOSIT_ADD', $auth['privilege'])) : ?>

                <div class="flex_child fits">
                    <a href="<?= member_url('membership/deposit/new'); ?>" class="button1 nwrap">
                        <i class="ri-add-line mr1"></i>
                        Simpanan
                    </a>
                </div>

            <?php endif; ?>
        </div>
    </div>

    <form method="get" class="search_bar">

        <input type="hidden" name="pagination" value="<?= $filter['pagination'] ?? '20,0'; ?>">

        <div class="search_gr" style="max-width: 200px;">
            <div class="initial">Status pembayaran</div>

            <div class="gr_box">
                <?php $payment_status_in = $filter['payment_status_in'] ?? []; ?>

                <input type="hidden" name="payment_status_in" value="">

                <label for="payment_status_in:NOT_PAID" class="checkbox1 wd100pc" <?= in_array('NOT_PAID', $payment_status_in) ? 'checked' : ''; ?>>
                    <input id="payment_status_in:NOT_PAID" type="checkbox" name="payment_status_in" value="NOT_PAID">
                    <div class="name">Belum dibayar</div>
                    <div class="box"></div>
                </label>

                <label for="payment_status_in:PENDING" class="checkbox1 wd100pc mt1" <?= in_array('PENDING', $payment_status_in) ? 'checked' : ''; ?>>
                    <input id="payment_status_in:PENDING" type="checkbox" name="payment_status_in" value="PENDING">
                    <div class="name">Menunggu validasi</div>
                    <div class="box"></div>
                </label>

                <label for="payment_status_in:VALID" class="checkbox1 wd100pc mt1" <?= in_array('VALID', $payment_status_in) ? 'checked' : ''; ?>>
                    <input id="payment_status_in:VALID" type="checkbox" name="payment_status_in" value="VALID">
                    <div class="name">Selesai</div>
                    <div class="box"></div>
                </label>

                <label for="payment_status_in:INVALID" class="checkbox1 wd100pc mt1" <?= in_array('INVALID', $payment_status_in) ? 'checked' : ''; ?>>
                    <input id="payment_status_in:INVALID" type="checkbox" name="payment_status_in" value="INVALID">
                    <div class="name">Ditolak</div>
                    <div class="box"></div>
                </label>
            </div>
        </div>

        <div class="search_gr" style="max-width: 200px;">
            <div class="initial">Jenis simpanan</div>

            <div class="gr_box">
                <?php $deposit_type = $filter['deposit_type'] ?? []; ?>

                <input type="hidden" name="deposit_type" value="">

                <label for="deposit_type:BASE" class="checkbox1 wd100pc" <?= in_array('BASE', $deposit_type) ? 'checked' : ''; ?>>
                    <input id="deposit_type:BASE" type="checkbox" name="deposit_type" value="BASE">
                    <div class="name">Simpanan pokok</div>
                    <div class="box"></div>
                </label>

                <label for="deposit_type:MANDATORY" class="checkbox1 wd100pc mt1" <?= in_array('MANDATORY', $deposit_type) ? 'checked' : ''; ?>>
                    <input id="deposit_type:MANDATORY" type="checkbox" name="deposit_type" value="MANDATORY">
                    <div class="name">Simpanan wajib</div>
                    <div class="box"></div>
                </label>

                <label for="deposit_type:VOLUNTARY" class="checkbox1 wd100pc mt1" <?= in_array('VOLUNTARY', $deposit_type) ? 'checked' : ''; ?>>
                    <input id="deposit_type:VOLUNTARY" type="checkbox" name="deposit_type" value="VOLUNTARY">
                    <div class="name">Simpanan sukarela</div>
                    <div class="box"></div>
                </label>
            </div>
        </div>

        <div class="search_item" style="visibility: hidden;"></div>

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

    <?php if (isset($memberDeposit) && count($memberDeposit) >= 1) : ?>

        <table class="table1">
            <thead>
                <tr>
                    <th class="tx_al_ct" style="width: 70px;">Status pembayaran</th>
                    <th class="tx_al_left" style="width: 200px;">Jenis simpanan</th>
                    <th class="tx_al_left">Jumlah</th>
                    <th class="tx_al_left" style="width: 300px;">Tanggal</th>
                    <th style="width: 50px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php $index = 1; ?>
                <?php foreach ($memberDeposit as $list) : ?>

                    <tr class="<?= $index % 2 == 0 ? 'even' : 'odd'; ?>">
                        <?php
                        $colorContext = 'warning';
                        if (in_array($list['payment']['status'], ['INVALID'])) {
                            $colorContext = 'error';
                        } else if (in_array($list['payment']['status'], ['VALID'])) {
                            $colorContext = 'success';
                        }
                        ?>

                        <td class="px0 tx_al_ct tx_sm2 tx_w_black clr_context <?= $colorContext; ?>" style="border: none;">
                            <?= $list['payment']['status'] == null ? 'Belum dibayar' : ''; ?>
                            <?= $list['payment']['status'] == 'PENDING' ? 'Menunggu Validasi' : ''; ?>
                            <?= $list['payment']['status'] == 'VALID' ? 'Selesai' : ''; ?>
                            <?= $list['payment']['status'] == 'INVALID' ? 'Ditolak' : ''; ?>
                        </td>
                        <td>
                            <?= $list['deposit_type'] == 'BASE' ? 'Simpanan pokok' : ''; ?>
                            <?= $list['deposit_type'] == 'MANDATORY' ? 'Simpanan wajib' : ''; ?>
                            <?= $list['deposit_type'] == 'VOLUNTARY' ? 'Simpanan sukarela' : ''; ?>
                        </td>
                        <td>
                            <?= 'Rp. ' . rupiah($list['deposit_amount']); ?>
                        </td>
                        <td>
                            <?= convertYmdhi($list['created_at']); ?>
                        </td>
                        <td class="flex y_center x_center">
                            <?php $depositPaid = $list['payment']['status'] != null; ?>

                            <a href="<?= member_url('membership/deposit/' . base64_encode($list['deposit_id'])); ?>" class="button1 bt_small <?= $depositPaid ? 'semi_color' : ''; ?>">
                                <?php if ($depositPaid) : ?>

                                    <i class="ri-search-eye-line tx_w_regular mr1"></i> Detail

                                <?php else : ?>

                                    <i class="ri-exchange-dollar-line tx_w_regular mr1"></i> Bayar

                                <?php endif; ?>
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

            <?php $nextButton = ($pagination[1] + 1 >= 0 && isset($memberDeposit) && count($memberDeposit) >= $pagination[0]); ?>
            <a href="<?= clean_current_url("?pagination={$pagination[0]}," . $pagination[1] + 1); ?>" class="button1 semi_color bt_small wd_fit" style="<?= !$nextButton ? 'visibility: hidden' : ''; ?>">
                <i class="ri-arrow-right-s-line"></i>
            </a>

        </div>
    </div>

</div>

<?php $this->endSection('content'); ?>