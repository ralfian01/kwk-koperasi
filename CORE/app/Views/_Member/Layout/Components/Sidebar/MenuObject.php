<?php

$menus = [
    [
        'icon' => 'ri-home-7-line',
        'name' => 'Dashboard',
        'url' => member_url(''),
        'target' => [
            member_url()
        ]
    ],
    [
        'icon' => 'ri-account-circle-line',
        'name' => 'Akun saya',
        'url' => member_url('my_account'),
        'menus' => [
            [
                'icon' => 'ri-settings-4-fill',
                'name' => 'Pengaturan',
                'url' => member_url('my_account/setting'),
                'target' => [
                    member_url('my_account/setting')
                ]
            ],
            // [
            //     'icon' => 'ri-shield-user-fill',
            //     'name' => 'Hak akses',
            //     'url' => member_url('my_account/privilege'),
            //     'target' => [
            //         member_url('my_account/privilege')
            //     ]
            // ],
        ]
    ],
    [
        'icon' => 'ri-user-6-line',
        'name' => 'Keanggotaan',
        'url' => member_url('membership'),
        'target' => [
            member_url('membership'),
            member_url('membership/register')
        ],
        'menus' => !isset($member) ? null : [
            [
                'name' => 'Data saya',
                'url' => member_url('membership'),
                'target' => [
                    member_url('membership')
                ]
            ],
            [
                'name' => 'Pengaturan',
                'icon' => 'ri-settings-4-fill',
                'url' => member_url('membership/setting'),
                'target' => [
                    member_url('membership/setting'),
                    member_url('membership/setting/common'),
                    member_url('membership/setting/identity'),
                    member_url('membership/setting/business')
                ]
            ],
        ]
    ],
    [
        'icon' => 'ri-money-dollar-circle-line',
        'name' => 'Simpanan',
        'url' => member_url('membership/deposit'),
        'notification' => $sideBarNotification['notification'] ?? null,
        'privilege' => [
            "MEMBER_DEPOSIT_VIEW",
        ],
        'target' => [
            member_url('membership/deposit'),
            member_url('membership/deposit/$'),
        ],
    ],
];


$menu_group = [
    [
        'group_name' => 'Pengurus',
        'group_menu' => [
            // [
            //     'name' => 'Kelola Akun',
            //     'icon' => 'ri-account-box-line',
            //     'privilege' => [
            //         "ACCOUNT_MANAGE_VIEW",
            //     ],
            //     'menus' => [
            //         [
            //             'name' => 'Lihat akun',
            //             'url' => member_url('manage/account'),
            //             'target' => [
            //                 member_url('manage/account')
            //             ]
            //         ],
            //     ]
            // ],
            [
                'name' => 'Kelola Anggota',
                'icon' => 'ri-user-2-line',
                'privilege' => [
                    "MEMBER_MANAGE_VIEW",
                ],
                'menus' => [
                    [
                        'name' => 'Lihat anggota',
                        'url' => member_url('manage/member'),
                        'target' => [
                            member_url('manage/member'),
                            member_url('manage/member/$')
                        ]
                    ],
                    [
                        'name' => 'Anggota baru',
                        'icon' => 'ri-user-add-fill',
                        'url' => member_url('manage/member/new'),
                        'target' => [
                            member_url('manage/member/new'),
                            member_url('manage/member/new/$')
                        ]
                    ],
                    [
                        'name' => 'Tambah anggota baru',
                        'icon' => 'ri-user-add-fill',
                        'url' => member_url('manage/member/manual_input'),
                        'privilege' => [
                            "MEMBER_MANAGE_INSERT",
                        ],
                        'target' => [
                            member_url('manage/member/manual_input'),
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Kelola simpanan',
                'icon' => 'ri-coins-line',
                'privilege' => [
                    "DEPOSIT_MANAGE_VIEW",
                ],
                'url' => member_url('manage/deposit'),
                'target' => [
                    member_url('manage/deposit'),
                    member_url('manage/deposit/$')
                ]
            ],
            [
                'name' => 'Laporan keuangan',
                'icon' => 'ri-currency-line',
                'privilege' => [
                    "FINANCE_REPORT_MANAGE_VIEW",
                ],
                'menus' => [
                    [
                        'name' => 'Pemasukan',
                        'url' => member_url('manage/finance_report/income'),
                        'target' => [
                            member_url('manage/finance_report/income'),
                            member_url('manage/finance_report/income/new'),
                        ]
                    ],
                    [
                        'name' => 'Pengeluaran',
                        'url' => member_url('manage/finance_report/outcome'),
                        'target' => [
                            member_url('manage/finance_report/outcome'),
                            member_url('manage/finance_report/outcome/new'),
                        ]
                    ],
                ]
            ],
        ],
        'privilege' => [
            "ACCOUNT_MANAGE_VIEW",
            "DEPOSIT_MANAGE_VIEW",
            "ADMIN_MANAGE_VIEW",
            "CHAIRMAN_MANAGE_VIEW",
            "MEMBER_MANAGE_VIEW",
            "MANAGER_MANAGE_VIEW",
            "FINANCE_REPORT_MANAGE_VIEW",
        ],
    ],
];
