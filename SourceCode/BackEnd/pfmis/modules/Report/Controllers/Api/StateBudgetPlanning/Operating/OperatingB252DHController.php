<?php
namespace Module\Report\Controllers\StateBudgetPlanning\Operating;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;
use function Psy\debug;

class OperatingB252DHController extends Controller
{
    use Common;

    public function BCDHB02DHCH()
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }

    public function STDS02DHCH()
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }

    public function STDTHDTTNSNN02()
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }

    public function STDDTGTGT03()
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }


    public function SBPB252DH(Request $request)
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $Year = date("Y");
        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : $Year;
        $companyID = (isset($filter['CompanyID'])) ? $filter['CompanyID'] : null;
        $directionID = (isset($filter['DirectionID'])) ? $filter['DirectionID'] : null;
        $note = 'Ghi chú: ';
        if (isset($filter['SectorName'])) {
            $note .= 'Ngành ' . $filter['SectorName'] . ', ';
        }
        if (isset($filter['ProvinceName'])) {
            $note .= 'tỉnh ' . $filter['ProvinceName'] . ', ';
        }
        if (isset($filter['DistrictName'])) {
            $note .= 'huyện ' . $filter['DistrictName'] . ', ';
        }
        if (isset($filter['CommuneName'])) {
            $note .= 'xã ' . $filter['CommuneName'] . ', ';
        }
        if (isset($filter['CompanyName'])) {
            $note .= 'đơn vị ' . $filter['CompanyName'];
        }

        $whereCompany = '';
        if ($companyID) {
            $whereCompany .= " AND A.CompanyID = " . $companyID . ' ';
        }
        $whereDirection = '';
        if ($directionID) {
            $whereDirection = 'AND A.DirectionID = ' . $directionID . ' ';
        }

        // TODO check isFinalTrans: AND A.isFinalTrans = 1

        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN ($year - 2) AND $year AND A.InTransTypeID NOT IN (16,17,18) ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
            `STT` varchar(50) DEFAULT '',
            `ItemID` varchar(100) NOT NULL PRIMARY KEY,
            `ParentID` varchar(100) DEFAULT '',
            `Level` int(2) DEFAULT 1,
            `ItemName` varchar(255) DEFAULT NULL,
            `FontWeight` tinyint(1) DEFAULT 0,
            `Italic` tinyint(1) DEFAULT 0,
            `Center` tinyint(1) DEFAULT 0,
            `Revenue` varchar(20) DEFAULT '',
            `SbiCategoryValue` varchar(20) DEFAULT '',
            `NormCateNo` varchar(20) DEFAULT '',
            `NormCateValue` varchar(20) DEFAULT '',
            `I1` double(20,4) DEFAULT 0 NOT NULL,
            `I2` double(20,4) DEFAULT 0 NOT NULL,
            `I3` double(20,4) DEFAULT 0 NOT NULL,
            `I4` double(20,4) DEFAULT 0 NOT NULL,
            `I5` double(20,4) DEFAULT 0 NOT NULL,
            `Detail` tinyint(1) NOT NULL
        )   ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);


        $countRecords = DB::table('table_tmp')->count();
        $this->insertExpenseRI($whereCondition, $year);
        if (DB::table('table_tmp')->count() > $countRecords) {
            $this->getParentDataTreeCommon(['Revenue', 'SbiCategoryValue', 'NormCateNo', 'NormCateValue']);
        }
        $this->InsertTemplateII($whereCondition);

        $this->insertExpenseRII1($whereCondition, $year);
        $this->insertExpenseRII2($whereCondition, $year);


        // Thiết lập STT

        //Cấp 1
        $insertArr1 = [
            ['STT' => 'A', 'ItemID' => 'B', 'ParentID' => '', 'Level' => 0, 'ItemName' => 'TỔNG CHI', 'FontWeight' => '1', 'Detail' => '0'],
            ['STT' => 'I', 'ItemID' => 'B#01', 'ParentID' => 'B', 'Level' => 1, 'ItemName' => 'Chi từ phần thu phí được để lại', 'FontWeight' => '1', 'Detail' => '0'],
            ['STT' => 'II', 'ItemID' => 'B#02', 'ParentID' => 'B', 'Level' => 1, 'ItemName' => 'Chi nộp ngân sách nhà nước theo tỷ lệ', 'FontWeight' => '1', 'Detail' => '0'],
            ['STT' => '1', 'ItemID' => 'B#02#01', 'ParentID' => 'B#02', 'Level' => 2, 'ItemName' => 'Lệ phí', 'FontWeight' => '0', 'Detail' => '0'],
            ['STT' => '2', 'ItemID' => 'B#02#02', 'ParentID' => 'B#02', 'Level' => 2, 'ItemName' => 'Phí', 'FontWeight' => '0', 'Detail' => '0'],

        ];

        DB::table('table_tmp')->insert($insertArr1); // Query Builder approach

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumUpSBPB252DH(0, $maxLevel);

        // insert detail

        $data = DB::table("table_tmp")->orderBy('ItemID', 'asc')->get();
        $yearPre = (int)$year - 2;
        $yearNext = (int)$year - 1;

        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $yearPre;
            $data[0]->HeaderName2 = 'Năm ' . $yearNext;
            $data[0]->HeaderName3 = 'Dự toán năm ' . $year;
        }
        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            if($data[$key]->I2 !=0){
                $data[$key]->I5 = round((((int)($data[$key]->I4) - (int)($data[$key]->I2))*100)/(int)($data[$key]->I2), 2);
            }
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';

        }

        $company = Company::find($companyID);
        $companyName = '';
        if ($company) {
            $companyName = $company->CompanyName;
        }
        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $dateTime = "Vĩnh Phú, ngày $date tháng $month năm $yearC";

        if ($company) {
            $dateTime = $company->Address . ", ngày $date tháng $month năm $yearC";
        }

        $response = [
            'master' => [
                "Year" => "",
                "CreateName" => "Trần Thế Thái",
                "TransNo" => "00001",
                "ReportName" => "CÂN ĐỐI DỰ TOÁN TỪ NGUỒN THU SỰ NGHIỆP, DỊCH VỤ NĂM " . $year,
                "DateTime" => $dateTime,
                "ParentCompanyName" => 'UBND tỉnh Vĩnh Phú',
                "CompanyName" => $companyName,
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function InsertTemplateII($whereCondition)
    {
        $sqlUpdate = "SELECT A.TransTypeID, A.FiscalYear, A.AccountNo AccountNo,A.ItemName ItemName,
                   NC.CateNo NormCateNo, N.NormID NormID
                    FROM act_gl_books A
                        INNER JOIN norm_cate NC ON NC.NormID = A.NormID
                        INNER JOIN norm N ON N.NormID = A.NormID
                        WHERE NC.CateNo = '2011111' $whereCondition  GROUP BY N.NormID";

        $result = DB::select($sqlUpdate);
        $result = (array)$result;
        $arrInsert1 = [];
        $i = 1;
        if ($result) {
            foreach ($result as $template) {
                $STT = '1.'. $i++;
                $ItemID = 'B#02#01' . $template->NormID ;
                $ParentID = 'B#02#01';
                $arrInsert1[] = [
                    'STT' => $STT,
                    'Show'=>'',
                    'ItemID' => $ItemID,
                    'ParentID' => $ParentID,
                    'ItemName' => $template->ItemName,
                    'Level'=>'3',
                    'FontWeight'=> '',
                    'NormID' => $template->NormID,
                    'AccountNo' => $template->AccountNo,
                    'I1'=>'',
                    'I2'=>'',
                    'I3'=>'',
                    'I4'=>'',
                ];
            }
            DB::table('table_tmp')->insert($arrInsert1);
        }
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }

    public function insertExpenseRI($whereCondition, $year)
    {
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Revenue, SbiCategoryValue, NormCateNo, NormCateValue, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID,'#', NormCateValue) as ItemID, ParentID, ItemName, Revenue, SbiCategoryValue, NormCateNo, NormCateValue, 1,
        MAX(CASE WHEN AccountNo = '0462' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0442' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0712' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('B#','01','#',A.RevenueNo,'#',SCC.CateValue,'#',NC.CateNo) AS ParentID,A.ItemName as ItemName,
        A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, A.RevenueNo as Revenue, SCC.CateValue as SbiCategoryValue, SCC.CateNo as CateNo,
		NC.CateNo as NormCateNo,NC.CateValue as NormCateValue,
        SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
		INNER JOIN sbi_category_cate as SCC on A.SbiCategoryID = SCC.SbiCategoryID
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
		WHERE A.AccountNo IN ('0462','0442','0712','0411')
        AND SCC.CateNo = '004'
		AND CC.CateNo = '200212'
		AND NC.CateNo = '2015112'
        $whereCondition
        GROUP BY A.RevenueNo, SCC.CateValue, NC.CateNo,NC.CateValue, YEAR(A.PeriodFromDate)) as CH GROUP BY NormCateValue";
        DB::statement($sql);
    }
    public function insertExpenseRII1($whereCondition,$year){
        $sql = "UPDATE table_tmp AS TT, (SELECT
            MAX(CASE WHEN AccountNo = '0361' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		    MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		    MAX(CASE WHEN AccountNo = '0711' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		    MAX(CASE WHEN AccountNo = '0331' AND YEARD = $year THEN I ELSE 0 END) AS I4
            FROM (SELECT SUM(A.FCDebitAmount) AS I, A.AccountNo, A.RevenueNo AS RevenueNo, YEAR(A.PeriodFromDate)AS YearD
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
            INNER JOIN revenue as R on A.RevenueID = R.RevenueID
            INNER JOIN norm_cate as NC on A.NormID = NC.NormID
            WHERE A.AccountNo IN ('0361','0341','0711','0331')
            AND NC.CateNo = '2011111'
            $whereCondition
            GROUP BY A.AccountNo, A.RevenueNo) AS B) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.ParentID = 'B#02#01'";
        DB::statement($sql);
    }

    public function insertExpenseRII2($whereCondition,$year){
        $sql = "UPDATE table_tmp AS TT, (SELECT
            MAX(CASE WHEN AccountNo = '0361' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		    MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		    MAX(CASE WHEN AccountNo = '0711' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		    MAX(CASE WHEN AccountNo = '0331' AND YEARD = $year THEN I ELSE 0 END) AS I4
            FROM (SELECT SUM(A.FCDebitAmount) AS I, A.AccountNo, A.RevenueNo AS RevenueNo, YEAR(A.PeriodFromDate)AS YearD
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
            INNER JOIN revenue as R on A.RevenueID = R.RevenueID
            INNER JOIN norm_cate as NC on A.NormID = NC.NormID
            WHERE A.AccountNo IN ('0361','0341','0711','0331')
            AND NC.CateNo = '2011112' AND NC.CateValue = '2'
            $whereCondition
            GROUP BY A.AccountNo, A.RevenueNo) AS B) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.ParentID = 'B#02#02'";
        DB::statement($sql);
    }


    // SUM
    public function sumUpSBPB252DH($start, $maxLevel)
    {
        for ($i = $maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4 FROM table_tmp M
            WHERE M.Level = " . $i . " GROUP BY ParentID";
            $result = DB::select($sql);
            $result = (array)$result;
            if ($result) {
                foreach ($result as $value) {
                    $value = (array)$value;
                    $I1 = ($value['I1']) ? $value['I1'] : 0;
                    $I2 = ($value['I2']) ? $value['I2'] : 0;
                    $I3 = ($value['I3']) ? $value['I3'] : 0;
                    $I4 = ($value['I4']) ? $value['I4'] : 0;
                    $update = "UPDATE table_tmp
                SET I1 = I1 + " . $I1 . ", I2 = I2 + " . $I2 . ", I3 = I3 + " . $I3 . ", I4 = I4 + " . $I4 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = $j"; //Log::debug($update);
                    DB::statement($update);
                }
            }
        }
    }

    public function setIndexReportB252($id = null)
    {
        $insertArr = [];
        $maxLevel = DB::table('table_tmp')->select(DB::raw('Max(Level) as maxLevel'))->first();
        for ($i = 2; $i <= $maxLevel->maxLevel; $i++) {
            $insertArr1 = [];
            $where = '';
            if ($id !== null) {
                $where = "AND LEFT(ItemID, LENGTH('" . $id . "')) = '" . $id . "'";
            }
            $sql = "SELECT * FROM table_tmp WHERE Level = $i $where ORDER BY ItemID ASC ";
            $result = DB::select($sql);
            if ($i == 2) {
                for ($j = 1; $j <= 3; $j++) {
                    $index = 1;
                    foreach ($result as $key => $item) {
                        $detail = explode('#', $item->ParentID);
                        if ((int)$detail[1] == $j) {
                            $insertArr1[] = [
                                'ItemID' => $item->ItemID,
                                'ParentID' => $item->ParentID,
                                'Level' => $item->Level,
                                'STT' => $index
                            ];
                            DB::table('table_tmp')->where('ItemID', '=', $item->ItemID)->update(['STT' => $index]);
                            $index++;
                        }

                    }
                }

            } else {
                foreach ($insertArr[$i - 3] as $value) {
                    $x = 1;
                    foreach ($result as $key => $item) {
                        if ($value['ItemID'] == $item->ParentID) {
                            $insertArr1[] = [
                                'ItemID' => $item->ItemID,
                                'ParentID' => $item->ParentID,
                                'Level' => $item->Level,
                                'STT' => $value['STT'] . '.' . $x,
                            ];
                            DB::table('table_tmp')->where('ItemID', '=', $item->ItemID)->update(['STT' => $value['STT'] . '.' . $x]);
                            $x++;
                        }
                    }
                }
            }
            $insertArr[] = $insertArr1;
        }
    }

    public function updateDetailExpense($id = null)
    {
        $sql = "SELECT ItemID, ParentID, ItemExpense, SUM(I1) as I1, SUM(I2) as I2, SUM(I3) as I3, SUM(I4) as I4 FROM table_tmp
                    WHERE Level = 3 AND ItemExpense != '' AND LEFT(ItemID,4) = '" . $id . "' GROUP BY ItemExpense";
        $result = DB::select($sql);
        $result = (array)$result;
        foreach ($result as $item) {
            $I1 = $item->I1;
            $I2 = $item->I2;
            $I3 = $item->I3;
            $I4 = $item->I4;

            DB::table('table_tmp')->where('ItemExpense', '=', $item->ItemExpense)->where('Level', 2)->update([
                'I1' => $I1, 'I2' => $I2,
                'I3' => $I3, 'I4' => $I4
            ]);
        }
    }

}

