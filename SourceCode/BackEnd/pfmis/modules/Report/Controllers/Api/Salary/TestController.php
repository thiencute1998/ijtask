<?php
namespace Module\Report\Controllers\StateBudgetPlanning;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;

class TestController extends Controller {
    public function SBP3422016TTBTC_MB05(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : 2021;
        Log::debug($year);
        $companyID = (isset($filter['CompanyID'])) ? $filter['CompanyID'] : null;
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
        $yearPre = (int)$year - 1;
        $yearNext = (int)$year + 1;
        $whereCondition = "AND YEAR(A.PostDate) = $year $whereCompany";
        $whereConditionPre = "AND YEAR(A.PostDate) = $yearPre $whereCompany";
        $whereConditionNext = "AND YEAR(A.PostDate) = $yearNext $whereCompany";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(11) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `SbiCategoryNo` varchar(20) DEFAULT '',
          `ConAccountNoI1` varchar(20) DEFAULT '',
          `ConAccountNoI2` varchar(20) DEFAULT '',
          `ConAccountNoI3` varchar(20) DEFAULT '',
          `ConAccountNoI4` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb05')->get();
        $insertArr = [];
        foreach ($templateTable as $template) {
            $insertArr[] = [
                'STT' => $template->STT,
                'ItemID' => $template->ItemID,
                'ParentID' => $template->ParentID,
                'Level' => $template->Level,
                'ItemName' => $template->ItemName,
                'FontWeight' => $template->FontWeight,
                'Italic' => $template->Italic,
                'Center' => $template->Center,
                'SbiCategoryNo' => $template->SbiCategoryNo,
                'ConAccountNoI1' => $template->ConAccountNoI1,
                'ConAccountNoI2' => $template->ConAccountNoI2,
                'ConAccountNoI3' => $template->ConAccountNoI3,
                'ConAccountNoI4' => $template->ConAccountNoI4,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4
            ];
        }

        DB::table('table_tmp')->insert($insertArr);

        // cell 1
        $this->insertTableTmpA1C1($whereConditionPre);
        $this->insertTableTmpA2C1($whereConditionPre);
        $this->insertTableTmpBI1C1($whereConditionPre);
        $this->insertTableTmpBI23C1($whereConditionPre);
        $this->insertTableTmpBIIC1($whereConditionPre);
        $this->insertTableTmpBIIIC1($whereConditionPre);
        $this->insertTableTmpBIV11C1($whereConditionPre);
        $this->insertTableTmpBIV12C1($whereConditionPre);
        $this->insertTableTmpBIV21C1($whereConditionPre);
        $this->insertTableTmpBIV22C1($whereConditionPre);

        // cell 2
        $this->insertTableTmpA1C2($whereCondition);
        $this->insertTableTmpA2C2($whereCondition);
        $this->insertTableTmpBI1C2($whereCondition);
        $this->insertTableTmpBI23C2($whereCondition);
        $this->insertTableTmpBIIC2($whereCondition);
        $this->insertTableTmpBIIIC2($whereCondition);
        $this->insertTableTmpBIV11C2($whereCondition);
        $this->insertTableTmpBIV12C2($whereCondition);
        $this->insertTableTmpBIV21C2($whereCondition);
        $this->insertTableTmpBIV22C2($whereCondition);

        // cell 3
        $this->insertTableTmpA1C3($whereCondition);
        $this->insertTableTmpA2C3($whereCondition);
        $this->insertTableTmpBI1C3($whereCondition);
        $this->insertTableTmpBI23C3($whereCondition);
        $this->insertTableTmpBIIC3($whereCondition);
        $this->insertTableTmpBIIIC3($whereCondition);
        $this->insertTableTmpBIV11C3($whereCondition);
        $this->insertTableTmpBIV12C3($whereCondition);
        $this->insertTableTmpBIV21C3($whereCondition);
        $this->insertTableTmpBIV22C3($whereCondition);

        // cell 4
        $this->insertTableTmpA1C4($whereConditionNext);
        $this->insertTableTmpA2C4($whereConditionNext);
        $this->insertTableTmpBI1C4($whereConditionNext);
        $this->insertTableTmpBI23C4($whereConditionNext);
        $this->insertTableTmpBIIC4($whereConditionNext);
        $this->insertTableTmpBIIIC4($whereConditionNext);
        $this->insertTableTmpBIV11C4($whereConditionNext);
        $this->insertTableTmpBIV12C4($whereConditionNext);
        $this->insertTableTmpBIV21C4($whereConditionNext);
        $this->insertTableTmpBIV22C4($whereConditionNext);

        // sum up
        $this->sumUpTableTmp(1, 4);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();

        $company = Company::find($companyID);
        $dateTime = "Vĩnh Phú, ngày $date tháng $month năm $yearC";
        if ($company) {
            $dateTime = $company->Address . ", ngày $date tháng $month năm $yearC";
        }

        $response = [
            'master' => [
                "Year" => "",
                "CreateName" => "Trần Thế Thái",
                "TransNo" => "00001",
                "ReportName" => "DỰ TOÁN THU, CHI NGÂN SÁCH NHÀ NƯỚC NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    // c1
    public function insertTableTmpA1C1($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I1
        FROM act_gl_books AS A
        WHERE A.ConAccountNo = '01712' AND A.InTransTypeID = 18
        AND LEFT(A.RevenueNo, 3) = '120'
        $whereCondition
        ) AS HH SET TT.I1 = HH.I1 WHERE TT.ItemID = 'A#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpA2C1($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I1, S.ParentID AS SbiCategoryNo
        FROM act_gl_books AS A
        INNER JOIN sbi_category AS S on A.SbiCategoryNo = S.SbiCategoryNo
        WHERE S.Level = 2 AND S.ParentID IS NOT NULL
        AND A.ConAccountNo = '02728' AND A.InTransTypeID = 18
        AND LEFT(A.CapitalNo, 3) = '510'
        $whereCondition
        GROUP BY S.ParentID
        ) AS HH SET TT.I1 = HH.I1 WHERE TT.SbiCategoryNo = HH.SbiCategoryNo AND TT.ParentID = 'A#02'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBI1C1($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I1, S.ParentID AS SbiCategoryNo
        FROM act_gl_books AS A
        INNER JOIN sbi_category AS S on A.SbiCategoryNo = S.SbiCategoryNo
        WHERE S.Level = 2 AND S.ParentID IS NOT NULL
        AND A.ConAccountNo = '027121' AND A.InTransTypeID = 18
        $whereCondition
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        GROUP BY S.ParentID
        ) AS HH SET TT.I1 = HH.I1 WHERE TT.SbiCategoryNo = HH.SbiCategoryNo AND TT.ParentID = 'B#01#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBI23C1($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I1, A.ConAccountNo
        FROM act_gl_books AS A
        WHERE (A.ConAccountNo = '027122' OR A.ConAccountNo = '027128') AND A.InTransTypeID = 18
        $whereCondition
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        GROUP BY A.ConAccountNo
        ) AS HH SET TT.I1 = HH.I1 WHERE TT.ConAccountNoI1 = HH.ConAccountNo AND TT.ParentID = 'B#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIIC1($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I1
        FROM act_gl_books AS A
        WHERE A.ConAccountNo = '02715' AND A.InTransTypeID = 18
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        $whereCondition
        ) AS HH SET TT.I1 = HH.I1 WHERE TT.ItemID = 'B#02'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIIIC1($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I1, S.ParentID AS SbiCategoryNo
        FROM act_gl_books AS A
        INNER JOIN sbi_category AS S on A.SbiCategoryNo = S.SbiCategoryNo
        WHERE S.Level = 2 AND S.ParentID IS NOT NULL
        AND A.ConAccountNo = '02711' AND A.InTransTypeID = 18
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        AND (LEFT(A.CapitalNo, 3) = '110' OR LEFT(A.CapitalNo, 3) = '210' OR LEFT(A.CapitalNo, 3) = '310' OR LEFT(A.CapitalNo, 3) = '410')
        $whereCondition
        GROUP BY S.ParentID
        ) AS HH SET TT.I1 = HH.I1 WHERE TT.SbiCategoryNo = HH.SbiCategoryNo AND TT.ParentID = 'B#03'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV11C1($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I1
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND (A.ConAccountNo = '027121' OR A.ConAccountNo = '027122' OR A.ConAccountNo = '027128')
        AND P.ProgramType = 1
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        ) AS HH SET TT.I1 = HH.I1 WHERE TT.ItemID = 'B#04#01#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV12C1($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I1
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND A.ConAccountNo = '027112'
        AND P.ProgramType = 1
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '110' OR LEFT(A.CapitalNo, 3) = '210' OR LEFT(A.CapitalNo, 3) = '310' OR LEFT(A.CapitalNo, 3) = '410')
        ) AS HH SET TT.I1 = HH.I1 WHERE TT.ItemID = 'B#04#01#02'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV21C1($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I1
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND P.ProgramType = 2
        AND (A.ConAccountNo = '027121' OR A.ConAccountNo = '027122' OR A.ConAccountNo = '027128')
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        ) AS HH SET TT.I1 = HH.I1 WHERE TT.ItemID = 'B#04#02#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV22C1($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I1
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND A.ConAccountNo = '027112'
        AND P.ProgramType = 2
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '110' OR LEFT(A.CapitalNo, 3) = '210' OR LEFT(A.CapitalNo, 3) = '310' OR LEFT(A.CapitalNo, 3) = '410')
        ) AS HH SET TT.I1 = HH.I1 WHERE TT.ItemID = 'B#04#02#02'";
        DB::statement($sqlUpdate);
    }

    // c2
    public function insertTableTmpA1C2($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I2
        FROM act_gl_books AS A
        WHERE A.ConAccountNo = '01512'
        AND LEFT(A.RevenueNo, 3) = '120'
        $whereCondition
        ) AS HH SET TT.I2 = HH.I2 WHERE TT.ItemID = 'A#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpA2C2($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I2, S.ParentID AS SbiCategoryNo
        FROM act_gl_books AS A
        INNER JOIN sbi_category AS S on A.SbiCategoryNo = S.SbiCategoryNo
        AND A.InTransTypeID = 18
        WHERE S.Level = 2 AND S.ParentID IS NOT NULL
        AND A.ConAccountNo = '02528'
        AND LEFT(A.CapitalNo, 3) = '510'
        $whereCondition
        GROUP BY S.ParentID
        ) AS HH SET TT.I2 = HH.I2 WHERE TT.SbiCategoryNo = HH.SbiCategoryNo AND TT.ParentID = 'A#02'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBI1C2($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I2, S.ParentID AS SbiCategoryNo
        FROM act_gl_books AS A
        INNER JOIN sbi_category AS S on A.SbiCategoryNo = S.SbiCategoryNo
        AND A.InTransTypeID = 18
        WHERE S.Level = 2 AND S.ParentID IS NOT NULL
        AND A.ConAccountNo = '025121'
        $whereCondition
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        GROUP BY S.ParentID
        ) AS HH SET TT.I2 = HH.I2 WHERE TT.SbiCategoryNo = HH.SbiCategoryNo AND TT.ParentID = 'B#01#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBI23C2($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I2, A.ConAccountNo
        FROM act_gl_books AS A
        WHERE (A.ConAccountNo = '025122' OR A.ConAccountNo = '025128') AND A.InTransTypeID = 18
        AND A.InTransTypeID = 18
        $whereCondition
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        GROUP BY A.ConAccountNo
        ) AS HH SET TT.I2 = HH.I2 WHERE TT.ConAccountNoI2 = HH.ConAccountNo AND TT.ParentID = 'B#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIIC2($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I2
        FROM act_gl_books AS A
        WHERE A.ConAccountNo = '02515' AND A.InTransTypeID = 18
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        $whereCondition
        ) AS HH SET TT.I2 = HH.I2 WHERE TT.ItemID = 'B#02'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIIIC2($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I2, S.ParentID AS SbiCategoryNo
        FROM act_gl_books AS A
        INNER JOIN sbi_category AS S on A.SbiCategoryNo = S.SbiCategoryNo
        WHERE S.Level = 2 AND S.ParentID IS NOT NULL
        AND A.ConAccountNo = '02511' AND A.InTransTypeID = 18
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        AND (LEFT(A.CapitalNo, 3) = '110' OR LEFT(A.CapitalNo, 3) = '210' OR LEFT(A.CapitalNo, 3) = '310' OR LEFT(A.CapitalNo, 3) = '410')
        $whereCondition
        GROUP BY S.ParentID
        ) AS HH SET TT.I2 = HH.I2 WHERE TT.SbiCategoryNo = HH.SbiCategoryNo AND TT.ParentID = 'B#03'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV11C2($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I2
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND (A.ConAccountNo = '025121' OR A.ConAccountNo = '025122' OR A.ConAccountNo = '025128')
        AND P.ProgramType = 1
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        ) AS HH SET TT.I2 = HH.I2 WHERE TT.ItemID = 'B#04#01#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV12C2($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I2
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND A.ConAccountNo = '025112'
        AND P.ProgramType = 1
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '110' OR LEFT(A.CapitalNo, 3) = '210' OR LEFT(A.CapitalNo, 3) = '310' OR LEFT(A.CapitalNo, 3) = '410')
        ) AS HH SET TT.I2 = HH.I2 WHERE TT.ItemID = 'B#04#01#02'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV21C2($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I2
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND P.ProgramType = 2
        AND (A.ConAccountNo = '025121' OR A.ConAccountNo = '025122' OR A.ConAccountNo = '025128')
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        ) AS HH SET TT.I2 = HH.I2 WHERE TT.ItemID = 'B#04#02#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV22C2($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I1
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND A.ConAccountNo = '025112'
        AND P.ProgramType = 2
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '110' OR LEFT(A.CapitalNo, 3) = '210' OR LEFT(A.CapitalNo, 3) = '310' OR LEFT(A.CapitalNo, 3) = '410')
        ) AS HH SET TT.I1 = HH.I1 WHERE TT.ItemID = 'B#04#02#02'";
        DB::statement($sqlUpdate);
    }

    // c3
    public function insertTableTmpA1C3($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I3
        FROM act_gl_books AS A
        WHERE A.ConAccountNo = '01112'
        AND LEFT(A.RevenueNo, 3) = '120'
        $whereCondition
        ) AS HH SET TT.I3 = HH.I3 WHERE TT.ItemID = 'A#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpA2C3($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I3, S.ParentID AS SbiCategoryNo
        FROM act_gl_books AS A
        INNER JOIN sbi_category AS S on A.SbiCategoryNo = S.SbiCategoryNo
        WHERE S.Level = 2 AND S.ParentID IS NOT NULL
        AND A.ConAccountNo = '02128' AND A.InTransTypeID = 18
        AND LEFT(A.CapitalNo, 3) = '510'
        $whereCondition
        GROUP BY S.ParentID
        ) AS HH SET TT.I3 = HH.I3 WHERE TT.SbiCategoryNo = HH.SbiCategoryNo AND TT.ParentID = 'A#02'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBI1C3($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I3, S.ParentID AS SbiCategoryNo
        FROM act_gl_books AS A
        INNER JOIN sbi_category AS S on A.SbiCategoryNo = S.SbiCategoryNo
        WHERE S.Level = 2 AND S.ParentID IS NOT NULL
        AND A.ConAccountNo = '021121' AND A.InTransTypeID = 18
        $whereCondition
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        GROUP BY S.ParentID
        ) AS HH SET TT.I3 = HH.I3 WHERE TT.SbiCategoryNo = HH.SbiCategoryNo AND TT.ParentID = 'B#01#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBI23C3($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I3, A.ConAccountNo
        FROM act_gl_books AS A
        WHERE (A.ConAccountNo = '021122' OR A.ConAccountNo = '021128') AND A.InTransTypeID = 18
        $whereCondition
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        GROUP BY A.ConAccountNo
        ) AS HH SET TT.I3 = HH.I3 WHERE TT.ConAccountNoI3 = HH.ConAccountNo AND TT.ParentID = 'B#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIIC3($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I3
        FROM act_gl_books AS A
        WHERE A.ConAccountNo = '02115' AND A.InTransTypeID = 18
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        $whereCondition
        ) AS HH SET TT.I3 = HH.I3 WHERE TT.ItemID = 'B#02'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIIIC3($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I3, S.ParentID AS SbiCategoryNo
        FROM act_gl_books AS A
        INNER JOIN sbi_category AS S on A.SbiCategoryNo = S.SbiCategoryNo
        WHERE S.Level = 2 AND S.ParentID IS NOT NULL
        AND A.ConAccountNo = '02111' AND A.InTransTypeID = 18
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        AND (LEFT(A.CapitalNo, 3) = '110' OR LEFT(A.CapitalNo, 3) = '210' OR LEFT(A.CapitalNo, 3) = '310' OR LEFT(A.CapitalNo, 3) = '410')
        $whereCondition
        GROUP BY S.ParentID
        ) AS HH SET TT.I3 = HH.I3 WHERE TT.SbiCategoryNo = HH.SbiCategoryNo AND TT.ParentID = 'B#03'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV11C3($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I3
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND (A.ConAccountNo = '021121' OR A.ConAccountNo = '021122' OR A.ConAccountNo = '021128')
        AND P.ProgramType = 1
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        ) AS HH SET TT.I3 = HH.I3 WHERE TT.ItemID = 'B#04#01#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV12C3($whereCondition){

        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I3
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND A.ConAccountNo = '021112'
        AND P.ProgramType = 1
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '110' OR LEFT(A.CapitalNo, 3) = '210' OR LEFT(A.CapitalNo, 3) = '310' OR LEFT(A.CapitalNo, 3) = '410')
        ) AS HH SET TT.I3 = HH.I3 WHERE TT.ItemID = 'B#04#01#02'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV21C3($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I3
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND P.ProgramType = 2
        AND (A.ConAccountNo = '021121' OR A.ConAccountNo = '021122' OR A.ConAccountNo = '021128')
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        ) AS HH SET TT.I3 = HH.I3 WHERE TT.ItemID = 'B#04#02#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV22C3($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCDebitAmount) AS I3
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND A.ConAccountNo = '021112'
        AND P.ProgramType = 2
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '110' OR LEFT(A.CapitalNo, 3) = '210' OR LEFT(A.CapitalNo, 3) = '310' OR LEFT(A.CapitalNo, 3) = '410')
        ) AS HH SET TT.I3 = HH.I3 WHERE TT.ItemID = 'B#04#02#02'";
        DB::statement($sqlUpdate);
    }

    // c4
    public function insertTableTmpA1C4($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I4
        FROM act_gl_books AS A
        WHERE A.ConAccountNo = '01112'
        AND LEFT(A.RevenueNo, 3) = '120'
        $whereCondition
        ) AS HH SET TT.I4 = HH.I4 WHERE TT.ItemID = 'A#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpA2C4($whereCondition){

        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I4, S.ParentID AS SbiCategoryNo
        FROM act_gl_books AS A
        INNER JOIN sbi_category AS S on A.SbiCategoryNo = S.SbiCategoryNo
        WHERE S.Level = 2 AND S.ParentID IS NOT NULL
        AND A.ConAccountNo = '02228' AND A.InTransTypeID = 18
        AND LEFT(A.CapitalNo, 3) = '510'
        $whereCondition
        GROUP BY S.ParentID
        ) AS HH SET TT.I4 = HH.I4 WHERE TT.SbiCategoryNo = HH.SbiCategoryNo AND TT.ParentID = 'A#02'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBI1C4($whereCondition){

        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I4, S.ParentID AS SbiCategoryNo
        FROM act_gl_books AS A
        INNER JOIN sbi_category AS S on A.SbiCategoryNo = S.SbiCategoryNo
        WHERE S.Level = 2 AND S.ParentID IS NOT NULL
        AND A.ConAccountNo = '022121' AND A.InTransTypeID = 18
        $whereCondition
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        GROUP BY S.ParentID
        ) AS HH SET TT.I4 = HH.I4 WHERE TT.SbiCategoryNo = HH.SbiCategoryNo AND TT.ParentID = 'B#01#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBI23C4($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I4, A.ConAccountNo
        FROM act_gl_books AS A
        WHERE (A.ConAccountNo = '022122' OR A.ConAccountNo = '022128') AND A.InTransTypeID = 18
        $whereCondition
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        GROUP BY A.ConAccountNo
        ) AS HH SET TT.I4 = HH.I4 WHERE TT.ConAccountNoI4 = HH.ConAccountNo AND TT.ParentID = 'B#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIIC4($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I4
        FROM act_gl_books AS A
        WHERE A.ConAccountNo = '02215' AND A.InTransTypeID = 18
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        $whereCondition
        ) AS HH SET TT.I4 = HH.I4 WHERE TT.ItemID = 'B#02'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIIIC4($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I4, S.ParentID AS SbiCategoryNo
        FROM act_gl_books AS A
        INNER JOIN sbi_category AS S on A.SbiCategoryNo = S.SbiCategoryNo
        WHERE S.Level = 2 AND S.ParentID IS NOT NULL
        AND A.ConAccountNo = '02211' AND A.InTransTypeID = 18
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        AND (LEFT(A.CapitalNo, 3) = '110' OR LEFT(A.CapitalNo, 3) = '210' OR LEFT(A.CapitalNo, 3) = '310' OR LEFT(A.CapitalNo, 3) = '410')
        $whereCondition
        GROUP BY S.ParentID
        ) AS HH SET TT.I4 = HH.I4 WHERE TT.SbiCategoryNo = HH.SbiCategoryNo AND TT.ParentID = 'B#03'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV11C4($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I4
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND (A.ConAccountNo = '022121' OR A.ConAccountNo = '022122' OR A.ConAccountNo = '022128')
        AND P.ProgramType = 1
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        ) AS HH SET TT.I4 = HH.I4 WHERE TT.ItemID = 'B#04#01#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV12C4($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I4
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND A.ConAccountNo = '022112'
        AND P.ProgramType = 1
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '110' OR LEFT(A.CapitalNo, 3) = '210' OR LEFT(A.CapitalNo, 3) = '310' OR LEFT(A.CapitalNo, 3) = '410')
        ) AS HH SET TT.I4 = HH.I4 WHERE TT.ItemID = 'B#04#01#02'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV21C4($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I4
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND P.ProgramType = 2
        AND (A.ConAccountNo = '022121' OR A.ConAccountNo = '022122' OR A.ConAccountNo = '022128')
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '120' OR LEFT(A.CapitalNo, 3) = '220' OR LEFT(A.CapitalNo, 3) = '320' OR LEFT(A.CapitalNo, 3) = '420')
        ) AS HH SET TT.I4 = HH.I4 WHERE TT.ItemID = 'B#04#02#01'";
        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV22C4($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(LCCreditAmount) AS I4
        FROM act_gl_books AS A
        INNER JOIN program AS P ON A.ProgramID = P.ProgramID
        AND A.InTransTypeID = 18
        AND A.ConAccountNo = '022112'
        AND P.ProgramType = 2
        $whereCondition
        AND (LEFT(A.CapitalNo, 3) = '110' OR LEFT(A.CapitalNo, 3) = '210' OR LEFT(A.CapitalNo, 3) = '310' OR LEFT(A.CapitalNo, 3) = '410')
        ) AS HH SET TT.I4 = HH.I4 WHERE TT.ItemID = 'B#04#02#02'";
        DB::statement($sqlUpdate);
    }

    // sum up
    public function sumUpTableTmp($start, $end = null){
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4
                FROM table_tmp M
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
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }
}
