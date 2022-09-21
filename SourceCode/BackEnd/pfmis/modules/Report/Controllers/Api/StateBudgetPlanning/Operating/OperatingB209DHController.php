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

class OperatingB209DHController extends Controller
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


    public function SBPB209DH(Request $request)
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
            `Item` varchar(20) DEFAULT '',
            `ItemRevenue` varchar(20) DEFAULT '',
            `ItemExpense` varchar(20) DEFAULT '',
            `I1` double(20,4) DEFAULT 0 NOT NULL,
            `I2` double(20,4) DEFAULT 0 NOT NULL,
            `I3` double(20,4) DEFAULT 0 NOT NULL,
            `I4` double(20,4) DEFAULT 0 NOT NULL,
            `I5` double(20,4) DEFAULT 0 NOT NULL,
            `Detail` tinyint(1) NOT NULL
        )   ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        // Thu
        $countRecords = DB::table('table_tmp')->count();

        $this->insertR1Revenue($whereCondition, $year);
        if (DB::table('table_tmp')->count() > $countRecords) {
            $this->getParentDataTreeCommon(['item', 'ItemRevenue'], 'A#01');
        }
        $this->insertR2Revenue($whereCondition, $year);
        $this->insertR3Revenue($whereCondition, $year);

        $countRecords = DB::table('table_tmp')->count();
        // Chi
        $this->insertR1Expense($whereCondition, $year);
        if (DB::table('table_tmp')->count() > $countRecords) {
            $this->getParentDataTreeCommon(['item', 'ItemExpense'], 'B#01');
            $countRecords = DB::table('table_tmp')->count();
        }
        $this->insertR2Expense($whereCondition, $year);
        if (DB::table('table_tmp')->count() > $countRecords) {
            $this->getParentDataTreeCommon(['item', 'ItemExpense'], 'B#02');
            $countRecords = DB::table('table_tmp')->count();
        }
        $this->insertR3Expense($whereCondition, $year);
        if (DB::table('table_tmp')->count() > $countRecords) {
            $this->getParentDataTreeCommon(['item', 'ItemExpense'], 'B#03');
        }

        $this->setLevel();

        // Thiết lập STT
        $this->setIndexReportB209('A');
        $this->setIndexReportB209('B');

        //Cấp 1
        $insertArr1 = [
            ['STT' => 'A', 'ItemID' => 'A', 'ParentID' => '', 'Level' => 0, 'ItemName' => 'TỔNG THU', 'FontWeight' => '1', 'Detail' => '0'],
            ['STT' => 'B', 'ItemID' => 'B', 'ParentID' => '', 'Level' => 0, 'ItemName' => 'TỔNG CHI', 'FontWeight' => '1', 'Detail' => '0'],
            ['STT' => 'I', 'ItemID' => 'A#01', 'ParentID' => 'A', 'Level' => 1, 'ItemName' => 'Thu từ các hoạt động cung cấp các dịch vụ công do nhà nước định giá', 'FontWeight' => '1', 'Detail' => '0'],
            ['STT' => 'II', 'ItemID' => 'A#02', 'ParentID' => 'A', 'Level' => 1, 'ItemName' => 'Kinh phí nhà nước đặt hàng', 'FontWeight' => '1', 'Detail' => '0'],
            ['STT' => 'III', 'ItemID' => 'A#03', 'ParentID' => 'A', 'Level' => 1, 'ItemName' => 'Thu từ các hoạt động dịch vụ khác theo quy định của pháp luật', 'FontWeight' => '1', 'Detail' => '0'],
            ['STT' => 'I', 'ItemID' => 'B#01', 'ParentID' => 'B', 'Level' => 1, 'ItemName' => 'Chi từ nguồn thu các hoạt động cung cấp các dịch vụ công do nhà nước định giá', 'FontWeight' => '1', 'Detail' => '0'],
            ['STT' => 'II', 'ItemID' => 'B#02', 'ParentID' => 'B', 'Level' => 1, 'ItemName' => 'Kinh phí nhà nước đặt hàng', 'FontWeight' => '1', 'Detail' => '0'],
            ['STT' => 'III', 'ItemID' => 'B#03', 'ParentID' => 'B', 'Level' => 1, 'ItemName' => 'Chi từ nguồn thu các hoạt động dịch vụ khác theo quy định của pháp luật', 'FontWeight' => '1', 'Detail' => '0'],
        ];

        DB::table('table_tmp')->insert($insertArr1); // Query Builder approach

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumUpSBPB209DH(0, $maxLevel);

        $insertA = [
            ['STT' => '', 'ItemID' => 'A#01#001', 'ParentID' => 'A#01', 'Level' => 2, 'ItemName' => 'Phần thu theo giá dịch vụ công', 'Italic' => '1', 'Detail' => '0', 'ItemRevenue' => 1],
            ['STT' => '', 'ItemID' => 'A#01#002', 'ParentID' => 'A#01', 'Level' => 2, 'ItemName' => 'Phần thu tăng thêm do thực hiện lộ trình điều chỉnh giá dịch vụ theo quy định', 'Italic' => '1', 'Detail' => '0', 'ItemRevenue' => 2],
        ];

        $insertB = [
            ['STT' => '', 'ItemID' => 'B#01#001', 'ParentID' => 'B#01', 'Level' => 2, 'ItemName' => 'Chi tiền lương', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 1],
            ['STT' => '', 'ItemID' => 'B#01#002', 'ParentID' => 'B#01', 'Level' => 2, 'ItemName' => 'Chi hoạt động chuyên môn, chi quản lý', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 2],
            ['STT' => '', 'ItemID' => 'B#01#003', 'ParentID' => 'B#01', 'Level' => 2, 'ItemName' => 'Trích khấu hao tài sản cố định theo quy định', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 3],
            ['STT' => '', 'ItemID' => 'B#01#004', 'ParentID' => 'B#01', 'Level' => 2, 'ItemName' => 'Chi đầu tư phát triển theo quy định', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 4],
            ['STT' => '', 'ItemID' => 'B#01#005', 'ParentID' => 'B#01', 'Level' => 2, 'ItemName' => 'Nộp thuế và các khoản nộp NSNN khác theo quy định', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 99],
            ['STT' => '', 'ItemID' => 'B#01#006', 'ParentID' => 'B#01', 'Level' => 2, 'ItemName' => 'Chi khác theo quy định', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 98],
            ['STT' => '', 'ItemID' => 'B#02#0011', 'ParentID' => 'B#02', 'Level' => 2, 'ItemName' => 'Chi tiền lương', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 1],
            ['STT' => '', 'ItemID' => 'B#02#002', 'ParentID' => 'B#02', 'Level' => 2, 'ItemName' => 'Chi hoạt động chuyên môn, chi quản lý', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 2],
            ['STT' => '', 'ItemID' => 'B#02#003', 'ParentID' => 'B#02', 'Level' => 2, 'ItemName' => 'Trích khấu hao tài sản cố định theo quy định', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 3],
            ['STT' => '', 'ItemID' => 'B#02#004', 'ParentID' => 'B#02', 'Level' => 2, 'ItemName' => 'Chi đầu tư phát triển theo quy định', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 4],
            ['STT' => '', 'ItemID' => 'B#02#005', 'ParentID' => 'B#02', 'Level' => 2, 'ItemName' => 'Nộp thuế và các khoản nộp NSNN khác theo quy định', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 99],
            ['STT' => '', 'ItemID' => 'B#02#006', 'ParentID' => 'B#02', 'Level' => 2, 'ItemName' => 'Chi khác theo quy định', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 98],
            ['STT' => '', 'ItemID' => 'B#03#001', 'ParentID' => 'B#03', 'Level' => 2, 'ItemName' => 'Chi tiền lương', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 1],
            ['STT' => '', 'ItemID' => 'B#03#002', 'ParentID' => 'B#03', 'Level' => 2, 'ItemName' => 'Chi hoạt động chuyên môn, chi quản lý', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 2],
            ['STT' => '', 'ItemID' => 'B#03#003', 'ParentID' => 'B#03', 'Level' => 2, 'ItemName' => 'Trích khấu hao tài sản cố định theo quy định', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 3],
            ['STT' => '', 'ItemID' => 'B#03#004', 'ParentID' => 'B#03', 'Level' => 2, 'ItemName' => 'Chi đầu tư phát triển theo quy định', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 4],
            ['STT' => '', 'ItemID' => 'B#03#005', 'ParentID' => 'B#03', 'Level' => 2, 'ItemName' => 'Nộp thuế và các khoản nộp NSNN khác theo quy định', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 99],
            ['STT' => '', 'ItemID' => 'B#03#006', 'ParentID' => 'B#03', 'Level' => 2, 'ItemName' => 'Chi khác theo quy định', 'Italic' => '1', 'Detail' => '0', 'ItemExpense' => 98],
        ];

        DB::table('table_tmp')->insert($insertA);
        DB::table('table_tmp')->insert($insertB);

        // insert detail

        $this->updateDetailRevenue();
        $this->updateDetailExpense('B#01');
//        $this->updateDetailExpense('B#02');
//        $this->updateDetailExpense('B#03');
        $data = DB::table("table_tmp")->orderBy('ItemID', 'asc')->get();
        $yearPre = (int)$year - 2;
        $yearNext = (int)$year - 1;

        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $yearPre;
            $data[0]->HeaderName2 = 'Năm ' . $yearNext;
            $data[0]->HeaderName3 = 'Dự toán năm ' . $year;

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


    public function insertR1Revenue($whereCondition, $year)
    {
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Item, ItemRevenue, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', ItemRevenue) as ItemID, ParentID, ItemName, Item, ItemRevenue, 1,
        MAX(CASE WHEN AccountNo = '0362' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0342' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0712' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0312' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('A#','01','#',A.ItemNo) AS ParentID,
        CASE RC.CateValue
            WHEN '1' THEN 'Phần thu theo giá dịch vụ công'
            WHEN '2' THEN 'Phần thu tăng thêm do thực hiện lộ trình điều chỉnh giá dịch vụ theo quy định' END as ItemName,
        A.AccountNo, A.ItemNo as Item,RC.CateValue as ItemRevenue, YEAR(A.PeriodFromDate) AS YEARD,
        SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
		INNER JOIN item_cate as IC on A.ItemID = IC.ItemID
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
		WHERE A.AccountNo IN ('0362','0342','0712','0312')
        AND IC.CateNo = '002' AND IC.CateValue = '1'
		AND NC.CateNo = '2011114' AND NC.CateValue = '1'
		AND RC.CateNo = '2003101' AND RC.CateValue IN ('1','2')
        $whereCondition
        GROUP BY A.AccountNo, A.ItemNo, RC.CateValue, YEAR(A.PeriodFromDate)) as CH GROUP BY Item, ItemRevenue";

        DB::statement($sql);
    }

    public function insertR2Revenue($whereCondition, $year)
    {
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Item, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', Item) as ItemID, ParentID, ItemName, Item, 1,
        MAX(CASE WHEN AccountNo = '0362' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0342' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0712' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0312' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('A#','02') AS ParentID, A.ItemName as ItemName,
        A.AccountNo, A.ItemNo as Item, YEAR(A.PeriodFromDate) AS YEARD,
        SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
        INNER JOIN item as I on A.ItemID = I.ItemID
		INNER JOIN item_cate as IC on A.ItemID = IC.ItemID
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
		WHERE A.AccountNo IN ('0362','0342','0712','0312')
        AND IC.CateNo = '002' AND IC.CateValue = '2'
		AND NC.CateNo = '2011114' AND NC.CateValue = '2'
		AND RC.CateNo = '2003102'
        $whereCondition
        GROUP BY A.AccountNo, A.ItemNo, RC.CateNo, YEAR(A.PeriodFromDate)) as CH GROUP BY Item";

        DB::statement($sql);
    }

    public function insertR3Revenue($whereCondition, $year)
    {
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Item, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', Item) as ItemID, ParentID, ItemName, Item, 1,
        MAX(CASE WHEN AccountNo = '0362' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0342' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0712' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0312' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('A#','03') AS ParentID, A.ItemName as ItemName,
        A.AccountNo, A.ItemNo as Item, YEAR(A.PeriodFromDate) AS YEARD,
        SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
        INNER JOIN item as I on A.ItemID = I.ItemID
		INNER JOIN item_cate as IC on A.ItemID = IC.ItemID
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
		WHERE A.AccountNo IN ('0362','0342','0712','0312')
        AND IC.CateNo = '002' AND IC.CateValue = '3'
		AND NC.CateNo = '2011114' AND NC.CateValue = '3'
		AND RC.CateNo = '2003199' AND RC.CateValue = '99'
        $whereCondition
        GROUP BY A.AccountNo, A.ItemNo, YEAR(A.PeriodFromDate)) as CH GROUP BY Item";

        DB::statement($sql);
    }

    public function insertR1Expense($whereCondition, $year)
    {
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Item, ItemExpense, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', ItemExpense) as ItemID, ParentID, ItemName, Item, ItemExpense, 1,
        MAX(CASE WHEN AccountNo = '0462' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0442' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0722' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0412' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('B#','01','#',A.ItemNo) AS ParentID,
        CASE NC.CateValue
            WHEN '1' THEN 'Chi tiền lương'
            WHEN '2' THEN 'Chi hoạt động chuyên môn, chi quản lý'
            WHEN '3' THEN 'Trích khấu hao tài sản cố định theo quy định'
            WHEN '4' THEN 'Chi đầu tư phát triển theo quy định'
            WHEN '99' THEN 'Nộp thuế và các khoản nộp NSNN khác theo quy định'
            WHEN '98' THEN 'Chi khác theo quy định'
            END as ItemName,
        A.AccountNo, A.ItemNo as Item, NC.CateValue as ItemExpense, YEAR(A.PeriodFromDate) AS YEARD,
        SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
		INNER JOIN item_cate as IC on A.ItemID = IC.ItemID
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
		WHERE A.AccountNo IN ('0462','0442','0722','0412')
		AND CC.CateNo = '200212'
        AND IC.CateNo = '002' AND IC.CateValue = '1'
		AND NC.CateNo = '2015115' AND NC.CateValue IN ('1','2','3','4','99','98')
        $whereCondition
        GROUP BY A.AccountNo, A.ItemNo, CC.CateNo, NC.CateValue, YEAR(A.PeriodFromDate)) as CH GROUP BY Item, ItemExpense";

        DB::statement($sql);
    }

    public function insertR2Expense($whereCondition, $year)
    {
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Item, ItemExpense, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', ItemExpense) as ItemID, ParentID, ItemName, Item, ItemExpense, 1,
        MAX(CASE WHEN AccountNo = '0462' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0442' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0722' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0412' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('B#','02','#',A.ItemNo) AS ParentID,
        CASE NC.CateValue
            WHEN '1' THEN 'Chi tiền lương'
            WHEN '2' THEN 'Chi hoạt động chuyên môn, chi quản lý'
            WHEN '3' THEN 'Trích khấu hao tài sản cố định theo quy định'
            WHEN '4' THEN 'Chi đầu tư phát triển theo quy định'
            WHEN '99' THEN 'Nộp thuế và các khoản nộp NSNN khác theo quy định'
            WHEN '98' THEN 'Chi khác theo quy định'
            END as ItemName,
        A.AccountNo, A.ItemNo as Item, NC.CateValue as ItemExpense, YEAR(A.PeriodFromDate) AS YEARD,
        SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
		INNER JOIN item_cate as IC on A.ItemID = IC.ItemID
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
		WHERE A.AccountNo IN ('0462','0442','0722','0412')
		AND CC.CateNo = '200212'
        AND IC.CateNo = '002' AND IC.CateValue = '2'
		AND NC.CateNo = '2015115' AND NC.CateValue IN ('1','2','3','4','99','98')
        $whereCondition
        GROUP BY A.AccountNo, A.ItemNo, CC.CateNo, NC.CateValue, YEAR(A.PeriodFromDate)) as CH GROUP BY Item, ItemExpense";

        DB::statement($sql);
    }

    public function insertR3Expense($whereCondition, $year)
    {
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Item, ItemExpense, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', ItemExpense) as ItemID, ParentID, ItemName, Item, ItemExpense, 1,
        MAX(CASE WHEN AccountNo = '0462' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0442' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0722' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0412' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('B#','03','#',A.ItemNo) AS ParentID,
        CASE NC.CateValue
            WHEN '1' THEN 'Chi tiền lương'
            WHEN '2' THEN 'Chi hoạt động chuyên môn, chi quản lý'
            WHEN '3' THEN 'Trích khấu hao tài sản cố định theo quy định'
            WHEN '4' THEN 'Chi đầu tư phát triển theo quy định'
            WHEN '99' THEN 'Nộp thuế và các khoản nộp NSNN khác theo quy định'
            WHEN '98' THEN 'Chi khác theo quy định'
            END as ItemName,
        A.AccountNo, A.ItemNo as Item, NC.CateValue as ItemExpense, YEAR(A.PeriodFromDate) AS YEARD,
        SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
		INNER JOIN item_cate as IC on A.ItemID = IC.ItemID
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
		WHERE A.AccountNo IN ('0462','0442','0722','0412')
		AND CC.CateNo = '200212'
        AND IC.CateNo = '002' AND IC.CateValue = '3'
		AND NC.CateNo = '2015115' AND NC.CateValue IN ('1','2','3','4','99','98')
        $whereCondition
        GROUP BY A.AccountNo, A.ItemNo, CC.CateNo, NC.CateValue, YEAR(A.PeriodFromDate)) as CH GROUP BY Item, ItemExpense";

        DB::statement($sql);
    }

    // SUM
    public function sumUpSBPB209DH($start, $maxLevel)
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

    public function setIndexReportB209($id = null)
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

    public function updateDetailRevenue()
    {
        $sql = "SELECT ItemID, ParentID, ItemRevenue, SUM(I1) as I1, SUM(I2) as I2, SUM(I3) as I3, SUM(I4) as I4 FROM table_tmp
                    WHERE Level = 3 AND ItemRevenue != '' GROUP BY ItemRevenue";
        $result = DB::select($sql);
        $result = (array)$result;
        foreach ($result as $item) {
            $I1 = $item->I1;
            $I2 = $item->I2;
            $I3 = $item->I3;
            $I4 = $item->I4;

            DB::table('table_tmp')->where('ItemRevenue', '=', $item->ItemRevenue)->where('Level', 2)->update([
                'I1' => $I1, 'I2' => $I2,
                'I3' => $I3, 'I4' => $I4
            ]);
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

