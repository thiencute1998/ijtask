<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/rpt-para', function () {
    $arrInsert = [
        [
            'NOrder' => 1,
            'DataType' => 3,
            'ParaType' => 1,
            'ParaKey' => 'PERIODTYPE',
            'ParaName' => 'Kỳ báo cáo'
        ],
        [
            'NOrder' => 2,
            'DataType' => 3,
            'ParaType' => 1,
            'ParaKey' => 'FROMDATE',
            'ParaName' => 'Từ ngày'
        ],
        [
            'NOrder' => 3,
            'DataType' => 3,
            'ParaType' => 1,
            'ParaKey' => 'TODATE',
            'ParaName' => 'Đến ngày'
        ],
        [
            'NOrder' => 4,
            'DataType' => 1,
            'ParaType' => 1,
            'ParaKey' => 'AMOUNTTYPE',
            'ParaName' => 'Tiền báo cáo'
        ],
        [
            'NOrder' => 5,
            'DataType' => 1,
            'ParaType' => 1,
            'ParaKey' => 'REVENUECATELIST',
            'ParaName' => 'Loại khoản thu'
        ],
        [
            'NOrder' => 6,
            'DataType' => 1,
            'ParaType' => 1,
            'ParaKey' => 'EXPENSECATELIST',
            'ParaName' => 'Loại khoản chi'
        ],
        [
            'NOrder' => 7,
            'DataType' => 1,
            'ParaType' => 1,
            'ParaKey' => 'SECTOR',
            'ParaName' => 'Ngành'
        ],
        [
            'NOrder' => 8,
            'DataType' => 1,
            'ParaType' => 1,
            'ParaKey' => 'PROVINCE',
            'ParaName' => 'Tỉnh'
        ],
        [
            'NOrder' => 9,
            'DataType' => 1,
            'ParaType' => 1,
            'ParaKey' => 'DISTRICT',
            'ParaName' => 'Huyện'
        ],
        [
            'NOrder' => 10,
            'DataType' => 1,
            'ParaType' => 1,
            'ParaKey' => 'COMMUNE',
            'ParaName' => 'Xã'
        ],
        [
            'NOrder' => 11,
            'DataType' => 1,
            'ParaType' => 1,
            'ParaKey' => 'COMPANY',
            'ParaName' => 'Đơn vị'
        ],
        [
            'NOrder' => 12,
            'DataType' => 1,
            'ParaType' => 1,
            'ParaKey' => 'DIRECTION',
            'ParaName' => 'Chỉ thị'
        ]
    ];
    \Illuminate\Support\Facades\DB::table('rpt_para')->truncate();
    \Illuminate\Support\Facades\DB::table('rpt_para')->insert($arrInsert);
});
