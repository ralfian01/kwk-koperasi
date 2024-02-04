<?php $this->extend('Layout/Layout.php'); ?>

<!-- Sections -->

<?php $this->section('content'); ?>

<div class="content_box">

    <div class="initial mb1c5">
        <div class="title flex y_start x_between flex_gap1">
            <div class="flex_child">
                Pemasukan
            </div>

            <?php if (in_array('FINANCE_REPORT_MANAGE_EXPORT', $auth['privilege'])) : ?>

                <div class="flex_child fits">
                    <button onclick="window.print()" class="button1 semi_color nwrap">
                        <i class="ri-file-line mr1"></i>
                        Cetak laporan
                    </button>
                </div>

            <?php endif; ?>


            <div class="flex_child fits">
                <a href="<?= member_url('manage/finance_report/outcome/new'); ?>" class="button1 nwrap">
                    <i class="ri-add-line mr1"></i>
                    Laporan
                </a>
            </div>
        </div>
    </div>

    <form method="get" class="search_bar">

        <input type="hidden" name="pagination" value="<?= $filter['pagination'] ?? '20,0'; ?>">

        <div class="tx_w_bolder">
            Dari:
        </div>
        <input class="search_item" style="max-width: 150px;" max="<?= appendDate(date('Y-m-d'), -1); ?>" type="date" name="date_range" value="<?= $filter['date_range'][0] ?? ''; ?>" placeholder="Tanggal mulai">

        <div class="tx_w_bolder ml1">
            Sampai:
        </div>
        <input class="search_item" style="max-width: 150px;" type="date" max="<?= date('Y-m-d'); ?>" name="date_range" value="<?= $filter['date_range'][1] ?? ''; ?>" placeholder="Tanggal mulai">

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

    <?php if (isset($financeReportOutcomeList) && count($financeReportOutcomeList) >= 1) : ?>

        <table class="table1 printable">
            <thead>
                <tr>
                    <th class="tx_al_left" style="width: 150px;">Dilaporkan oleh</th>
                    <th class="tx_al_ct" style="width: 200px;">Jumlah pemasukan</th>
                    <th class="tx_al_left">Deskripsi</th>
                    <th class="tx_al_left" style="width: 200px;">Tanggal laporan</th>

                    <?php if (in_array('FINANCE_REPORT_MANAGE_DELETE', $auth['privilege'])) : ?>

                        <th class="not_printable" style="width: 50px;"></th>

                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php $index = 1; ?>
                <?php foreach ($financeReportOutcomeList as $list) : ?>

                    <tr class="<?= $index % 2 == 0 ? 'even' : 'odd'; ?>">
                        <td>
                            <?php if ($list['reporter_id'] != null) : ?>

                                <a href="<?= member_url('manage/member/' . base64_encode($list['reporter']['member_id'])); ?>" class="orig_udline">
                                    <?= $list['reporter']['member_fullname']; ?>
                                </a>

                            <?php else : ?>

                                Sistem

                            <?php endif; ?>
                        </td>
                        <td>
                            <?= 'Rp. ' . rupiah($list['finance_report_amount']); ?>
                        </td>
                        <td class="tx_overflow_mt2">
                            <?= $list['finance_report_description']; ?>
                        </td>
                        <td>
                            <?= convertYmdhi($list['finance_report_date']); ?>
                        </td>

                        <?php if (in_array('FINANCE_REPORT_MANAGE_DELETE', $auth['privilege'])) : ?>

                            <td class="not_printable flex y_start flex_gap1">
                                <button data-id="<?= base64_encode($list['finance_report_id']); ?>" class="delete_data button1 semi_color bt_small wd_fit nwrap" style="--bt_bg: var(--colorRed); --bt_border_color: var(--colorRed); --bt_color: var(--colorRed);">
                                    Hapus data
                                </button>
                            </td>

                        <?php endif; ?>

                    </tr>

                    <?php $index++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <script type="text/javascript">
            // Events
            $('body')
                .on('click', 'button.delete_data', function(e) {
                    let button = this;
                    let id = $(button).data('id');

                    // Override modal
                    $.modalBox
                        .overrideModal({
                            overrideModal: {
                                modalButton: {
                                    confirm: '<button class="button1 semi_color"></button>',
                                    cancel: '<button class="button1" style="--bt_bg: var(--colorRed); --bt_border_color: var(--colorRed);"></button>',
                                }
                            },
                        })
                        .setup({
                            title: 'Hapus data pemasukan?',
                            description: 'Setelah dihapus, data tidak dapat dikembalikan ',
                            button: {
                                confirmText: 'Ya, hapus',
                                cancelText: 'Kembali',
                            }
                        })
                        .open({
                            onConfirm: (trigger, element) => {

                                $(element.target).buttonOnLoading(true);

                                // Default confirm deposit url
                                let url = $.makeURL.api().addPath('manage/finance_report/outcome/' + id).href;

                                // Start send 
                                $.ajax({
                                    type: 'DELETE',
                                    url: url,
                                    headers: {
                                        'Authorization': 'Bearer ' + jsCookie.get('_PTS-Auth:Token'),
                                        'Content-Type': 'application/json'
                                    },
                                    success: function(res) {
                                        if (res.code == 200) {
                                            $.notif().success("Data laporan dihapus");

                                            setTimeout(() => {
                                                location.reload();
                                            }, 500);
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
                                            case 'MFID1':
                                                $.notif().error('Anda tidak dapat menghapus data ini');

                                                setTimeout(() => {
                                                    trigger.close();
                                                }, 250);
                                                break;
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
                        });
                });
        </script>

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

            <?php $nextButton = ($pagination[1] + 1 >= 0 && isset($memberDepositList) && count($memberDepositList) >= $pagination[0]); ?>
            <a href="<?= clean_current_url("?pagination={$pagination[0]}," . $pagination[1] + 1); ?>" class="button1 semi_color bt_small wd_fit" style="<?= !$nextButton ? 'visibility: hidden' : ''; ?>">
                <i class="ri-arrow-right-s-line"></i>
            </a>

        </div>
    </div>

</div>

<?php $this->endSection('content'); ?>