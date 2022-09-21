<?php

return [
    // ============================== Commom ========================================
    'Common' => [
        'AccessTypeOptions' =>[
            '1' => 'Chia sẻ',
            '2' => 'Công khai',
            '3' => 'Riêng tư'
        ],
        'PeriodType' => [
            '1' => 'Năm',
            '2' => '6 tháng',
            '3' => 'Quý',
            '4' => 'Tháng',
            '5' => 'Tuần',
            '6' => 'Ngày'
        ]
    ],
    // ======================== Coa  - Hệ thống tài khoản ========================
    'Coa' => [
        'BalanceTypeOptions'=>[
            '1' => 'Dư nợ',
            '2' => 'Dư có',
            '3' => 'Lưỡng tính',
            '4' => 'Không có số dư'
        ],
    ],

//    'CoaType'=> [
//        '1'=> 'Hợp nhất',
//        '2'=> 'KBNN',
//        '3'=> 'Ngân hàng nhà nước',
//        '4'=> 'Đơn vị HCSN',
//        '5'=> 'Ban QLDA',
//        '6'=> 'Xã phường',
//        '7'=> 'Doanh nghiệp',
//        '8'=> 'Dự trữ Quốc gia',
//        '9'=> 'Bảo hiểm xã hội',
//        '10'=> 'Quỹ tín dụng nhân dân',
//    ],

    'ManagementLevelOption' => [
        '1'=> 'Trung ương',
        '2'=> 'Tỉnh',
        '3'=> 'Huyện',
        '4'=> 'Xã'
    ],

    // Program
    'Program'=> [
        // Programe Type
        'ProgramTypeOption'=> [
            '1'=> 'CTMT Quốc gia',
            '2'=> 'CTMT Bổ sung'
        ],
    ],

    // Project
    'Project'=> [
        'StartYearOfMPeriod'=> 2016,
        'NumbersOfMPeriod'=> 10,
        'MPeriodDistance'=> 5,
        'ManagementLevelOption'=> [
            '1'=> 'Trung ương',
            '2'=> 'Tỉnh',
            '3'=> 'Huyện',
            '4'=> 'Xã'
        ],
        'Group'=> [
            'QTQG'=> 'QTQG',
            'A'=> 'A',
            'B'=> 'B',
            'C'=> 'C'
        ],

    ],

    // AccessTypeOptions

    // SbiItem
    'SbiItem'=> [
        'ItemType'=> [
            '1'=> 'Thu',
            '2'=> 'Chi',
            '3'=> 'Vay và trả nợ gốc',
            '4'=> 'Chuyển nguồn giữa các năm',
            '5'=> 'Thu, chi chưa đưa vào cân đối ngân sách nhà nước',
        ],
        'ItemGroup'=> [
            '1'=> 'Nhóm',
            '2'=> 'Tiểu nhóm',
            '3'=> 'Mục',
            '4'=> 'Tiểu mục'
        ],
    ],

    // Item type
    'ItemType'=>[
        '1'=>'Hàng hóa',
        '2'=>'Dịch vụ'
    ],

    //PeriodType
    'PeriodType' => [
        '1'=> 'Năm',
        '2'=> 'Quý',
        '3'=> 'Tháng',
        '4'=> 'Tuần',
        '5'=> 'Ngày',
        '6'=> '6 tháng đầu',
        '7'=> '6 tháng cuối',
        '8'=> '3 năm',
        '9'=> '5 năm',
        '10'=> '10 năm',
        '99'=> 'Tùy chọn',
    ],
    //Dự toán NNNN
    'SbpPlan'=> [
        // Năm
        'Year'=> [
            '1'=> '2017',
            '2'=> '2018',
            '3'=> '2019',
            '4'=> '2020',
            '5'=> '2021',
            '6'=> '2022',
            '7'=> '2023',
            '8'=> '2024',
            '9'=> '2025',
            '10'=> '2026'
        ],
    ],

    // Accounting CFAccount
    'AccountingCFAccount' => [
        'CFAccountTypeOption' => [
            '1'=> 'Số dư TK (hai bên)',
            '2'=> 'Số dư bên Nợ',
            '3'=> 'Số dư bên Có',
            '4'=> 'Phát sinh bên Nợ',
            '5'=> 'Phát sinh bên Có'
        ]
    ],

    // Employee
    'Employee' => [
        'EmployeeNameDisplayType' => 3
        ]

];
