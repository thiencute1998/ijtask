<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBPND312017NDCP;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Module\Report\Traits\Common;
use PHPUnit\Framework\Constraint\Count;

class SBPND312017NDCPMB05Controller extends Controller {
    use Common;
    public function SBPND312017NDCP_MB05(Request $request){

        $userCompany = DB::table('company')->where('CompanyID','=', Auth::user()->CompanyID)->first();
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : 2021;
        $companyID = (isset($filter['CompanyID'])) ? $filter['CompanyID'] : null;
        $directionID = (isset($filter['DirectionID'])) ? $filter['DirectionID'] : null;
        $note = 'Ghi chú: ';
        if (isset($filter['SectorName'])) {
            $note .= 'Ngành ' . $filter['SectorName'] . ', ';
        }
        if (isset($filter['ProvinceName'])) {
            $note .= 'tỉnh ' . $filter['ProvinceName'] . ', ';
            $provinceNo = $filter['ProvinceNo'];
        }
        else{
            $provinceNo = $userCompany->ProvinceNo;
        }
        if (isset($filter['DistrictName'])) {
            $note .= 'huyện ' . $filter['DistrictName'] . ', ';
            $districtNo = $filter['DistrictNo'];
        }
        else{
            $districtNo = $userCompany->DistrictNo;
        }
        if (isset($filter['CommuneName'])) {
            $note .= 'xã ' . $filter['CommuneName'] . ', ';
        }
        if (isset($filter['CompanyName'])) {
            $note .= 'đơn vị ' . $filter['CompanyName'];
        }
        if(isset($filter['PeriodYear'])){
            $periodYear = $filter['PeriodYear'];
            $periodDate = explode('-', $filter['PeriodYear']);
            $fromDate = $periodDate[0];
            $toDate = $periodDate[1];
        }
        else{
            $periodYear = '2021-2025';
            $fromDate = 2021;
            $toDate = 2025;
        }
        $whereCompany = '';
        if ($companyID) {
            $whereCompany .= " AND A.CompanyID = " . $companyID . ' ';
        }
        $whereDirection = '';
        if ($directionID) {
            $whereDirection = 'AND A.DirectionID = ' . $directionID . ' ';
        }

        $typeUserCompany = DB::table('company_cate')->where('CompanyID', Auth::user()->CompanyID)
            ->where('CateNo', '1000')->whereNotNull('CateValue')
            ->orderBy('CateValue','ASC')->first();
        $whereCommonCondition = ' AND A.InTransTypeID NOT IN (16, 17 ,18) ';
        $currentDate = date('Y-m-d');
        $whereDebt = " AND A.PeriodFromDate <= '". $currentDate ."' $whereCommonCondition ";
        $whereCondition = " AND A.PeriodType = 9 AND A.PeriodName = '" . $periodYear . "' $whereCommonCondition";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(20) DEFAULT '',
          `ItemID` varchar(100) NOT NULL PRIMARY KEY,
          `ParentID` varchar(100) DEFAULT '',
          `ParentID1` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `I1` double(20,4) DEFAULT 0,
          `I2` double(20,4) DEFAULT 0,
          `I3` double(20,4) DEFAULT 0,
          `I4` double(20,4) DEFAULT 0,
          `I5` double(20,4) DEFAULT 0,
          `I6` double(20,4) DEFAULT 0,
            `SectorCateValue` varchar(20) DEFAULT '',
            `T1Temporary` varchar(20) DEFAULT '',
            `T2Temporary` varchar(20) DEFAULT '',
            `District` varchar(30) DEFAULT '',
            `Commune` varchar(30) DEFAULT '',
            `Company` varchar(30) DEFAULT '',
            `Project` varchar(30) DEFAULT '',
            `BuildAddress` varchar(255) DEFAULT '',
            `CapableDesign` varchar(255) DEFAULT '',
            `PerformDate` varchar(255) DEFAULT '',
            `Investdoc` varchar(255) DEFAULT '',
            `Path` varchar(255) DEFAULT '',
            `ParentCompanyNo` varchar(20) DEFAULT '',
            `Norder` int(10) DEFAULT 0,
            `Detail` tinyint(1) NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $this->createTemporaryT1();
        $this->createTemporaryT2();

        // caculator here
        $insertArr = [
            'ItemID'=> "A", 'ItemName'=> 'Tổng số', 'Level'=> 0, 'FontWeight'=> 1
        ];
        DB::table('table_tmp')->insert($insertArr);
        $this->insertDataMB05($userCompany, $typeUserCompany, $whereDebt, $whereCondition, $provinceNo, $districtNo, $fromDate, $toDate);
        $this->setLevel();
        $this->sortSector();


        // sum up
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumRow1($maxLevel);
        $this->sumRow($maxLevel);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('Norder', 'ASC')->orderBy('ItemID', 'ASC')->get();

        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
        }

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
                "ReportName" => "DANH MỤC CHƯƠNG TRÌNH, DỰ ÁN DỰ KIẾN BỐ TRÍ KẾ HOẠCH ĐẦU TƯ CÔNG TRUNG HẠN VỐN TRONG NƯỚC GIAI ĐOẠN 05 NĂM " . $periodYear,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function createTemporaryT1(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `T1Temporary` (
          `ItemID` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $insertA = [
            ['ItemID'=> '1','ItemName'=> 'Chuẩn bị đầu tư'],
            ['ItemID'=> '2','ItemName'=> 'Thực hiện dự án'],
        ];
        DB::table('T1Temporary')->insert($insertA);
    }

    public function createTemporaryT2(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `T2Temporary` (
          `ItemID` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $insertA = [
            ['ItemID'=> '01','ItemName'=> 'Dự án chuyển tiếp từ giai đoạn 5 năm … sang giai đoạn 5 năm …'],
            ['ItemID'=> '02','ItemName'=> 'Dự án khởi công mới trong giai đoạn 5 năm …'],
        ];
        DB::table('T2Temporary')->insert($insertA);
    }

    public function getDetailParentCompany($levelCompany){
        $insertArr = [];
        $listItemDetail = DB::table('table_tmp')->where('Detail', 1)->where('Company','!=', '')->get();
        if($listItemDetail){
            foreach($listItemDetail as $item){
                $listParent = explode('#', $item->Path);
                array_pop($listParent);
                if(!empty($listParent)){
                    for($i = count($listParent) - 1; $i >= ($levelCompany - 1) ; $i--){
                        $parentID1 = '';
                        $itemID = str_replace($item->Company, $listParent[$i], $item->ItemID);
                        $parentID = str_replace($item->Company, $listParent[$i], $item->ParentID);
                        if($i > ($levelCompany - 1) && $item->ParentID1 != ''){
                            // Có ParentID1
                            $parentID1 = str_replace($item->Company, $listParent[$i - 1], $item->ItemID);
                            $parentCompanyNo = $listParent[$i-1];
                        }
                        else{
                            $parentCompanyNo = null;
                        }
                        $insertArr [] = [
                            'ItemID'=>  $itemID,
                            'ParentID'=> $parentID,
                            'ParentID1'=> $parentID1,
                            'ItemName'=> $item->ItemName,
                            'SectorCateValue'=> $item->SectorCateValue,
                            'District'=> $item->District,
                            'Company'=> $listParent[$i],
                            'T1Temporary'=> $item->T1Temporary,
                            'T2Temporary'=> $item->T2Temporary,
                            'Project'=> $item->Project,
                            'BuildAddress'=> $item->BuildAddress,
                            'CapableDesign'=> $item->CapableDesign,
                            'PerformDate'=> $item->PerformDate,
                            'Investdoc'=> $item->Investdoc,
                            'ParentCompanyNo'=> $parentCompanyNo,
                            'Norder'=> $item->Norder,
                            'Detail'=> 1,
                        ];
                    }
                }
            }
            DB::table('table_tmp')->insertOrIgnore($insertArr);
        }
    }

    public function insertDataMB05($userCompany, $typeUserCompany, $whereDebt, $whereCondition, $provinceNo, $districtNo, $fromDate, $toDate){
        if($typeUserCompany){
            if($typeUserCompany->CateValue == '1'){
                $whereCondition .= " AND C.Level >= " . $userCompany->Level;
                // Đơn vị dự toán cấp 1
                if($userCompany->SumCompanyType == '2'){
                    // Tổng hợp dữ liệu theo cấp chính quyền
                    if($userCompany->ManagementLevel == '2'){
                        // Tổng hợp theo cấp tỉnh (Riêng báo cáo này tổng hợp cả đơn vị của Huyện)
                        // Lấy ds đơn vị cấp tỉnh
                        $this->insertDistrictR1C5_6($whereDebt, $provinceNo);
                        $this->insertDistrictR1C7_8_9_10($whereCondition, $provinceNo);
                        $this->insertDistrictR2C5_6($whereDebt, $provinceNo, $fromDate, $toDate);
                        $this->insertDistrictR2C7_8_9_10($whereCondition, $provinceNo, $fromDate, $toDate);

                        $this->getParentDataTreeCommonNorder([['sector_cate_value', '200030301'], 'district', 'T1Temporary', 'project']);
                        $this->getParentDataTreeCommonNorder([['sector_cate_value', '200030301'], 'district', 'T1Temporary', 'T2Temporary', 'project']);

                        $whereDebt .= " AND C.ManagementLevel = 2 AND C.ProvinceNo = '$provinceNo'";
                        $whereCondition .= " AND C.ManagementLevel = 2 AND C.ProvinceNo = '$provinceNo'";
                    }
                    else if($userCompany->ManagementLevel == '3'){
                        $this->insertCommuneR1C5_6($whereDebt, $districtNo);
                        $this->insertCommuneR1C7_8_9_10($whereCondition, $districtNo);
                        $this->insertCommuneR2C5_6($whereDebt, $districtNo, $fromDate, $toDate);
                        $this->insertCommuneR2C7_8_9_10($whereCondition, $districtNo, $fromDate, $toDate);

                        $this->getParentDataTreeCommonNorder([['sector_cate_value', '200030301'], 'commune', 'T1Temporary', 'project']);
                        $this->getParentDataTreeCommonNorder([['sector_cate_value', '200030301'], 'commune', 'T1Temporary', 'T2Temporary', 'project']);

                        $whereDebt .= " AND C.ManagementLevel = 3 AND C.DistrictNo = '$districtNo'";
                        $whereCondition .= " AND C.ManagementLevel = 3 AND C.DistrictNo = '$districtNo'";
                    }
                    else{
                        // Tổng hợp xã
                        $whereDebt .= " AND C.ManagementLevel = 4";
                        $whereCondition .= " AND C.ManagementLevel = 4";
                    }
                }
                else if($userCompany->SumCompanyType == '3'){
                    // Tổng hợp dữ liệu theo ngành, Lấy danh sách đơn vị thuộc ngành đó + cùng cấp chính quyền
                    $whereDebt .= " AND C.ManagementLevel = " . $userCompany->ManagementLevel . " AND C.SectorID = " . $userCompany->SectorID;
                    $whereCondition .= " AND C.ManagementLevel = " . $userCompany->ManagementLevel . " AND C.SectorID = " . $userCompany->SectorID;
                    if($userCompany->ManagementLevel == '2'){
                        $whereDebt .= " AND C.ProvinceNo = '$provinceNo'";
                        $whereCondition .= " AND C.ProvinceNo = '$provinceNo'";
                    }
                    else if($userCompany->ManagementLevel == '3'){
                        $whereDebt .= " AND C.DistrictNo = '$districtNo'";
                        $whereCondition .= " AND C.DistrictNo = '$districtNo'";
                    }
                }
            }
            else{
                if($typeUserCompany->CateValue == '2'){
                    $whereDebt .= " AND C.ManagementLevel = " . $userCompany->ManagementLevel . " AND C.AutOrgNo = '" . $userCompany->AutOrgNo . "'";
                    $whereCondition .= " AND C.ManagementLevel = " . $userCompany->ManagementLevel . " AND C.AutOrgNo = '" . $userCompany->AutOrgNo . "'";
                    // Đơn vị dự toán cấp 2 (Đơn vị trung gian) -> Tổng hợp cùng đơn vị chủ quản
                }
                else{
                    // Lấy các đơn vị con của nó
                    $whereDebt .= " AND LEFT(C.CompanyNo, " . 3* ($userCompany->Level) . ") = LEFT('" .  $userCompany->CompanyNo. "', " . 3* ($userCompany->Level) . ")";
                    $whereCondition .= " AND LEFT(C.CompanyNo, " . 3* ($userCompany->Level) . ") = LEFT('" .  $userCompany->CompanyNo. "', " . 3* ($userCompany->Level) . ")";
                }
                if($userCompany->ManagementLevel == '2'){
                    $whereDebt .= " AND C.ProvinceNo = '$provinceNo'";
                    $whereCondition .= " AND C.ProvinceNo = '$provinceNo'";
                }
                else if($userCompany->ManagementLevel == '3'){
                    $whereDebt .= " AND C.DistrictNo = '$districtNo'";
                    $whereCondition .= " AND C.DistrictNo = '$districtNo'";
                }

            }
            $this->insertR1C5_6($whereDebt);
            $this->insertR1C7_8_9_10($whereCondition);
            $this->insertR2C5_6($whereDebt, $fromDate, $toDate);
            $this->insertR2C7_8_9_10($whereCondition, $fromDate, $toDate);
            $this->getDetailParentCompany($userCompany->Level);
            $this->getParentDataTreeCommonMB05([['sector_cate_value', '200030301'], 'company', 'T1Temporary', 'project']);
            $this->getParentDataTreeCommonMB05([['sector_cate_value', '200030301'], 'company', 'T1Temporary', 'T2Temporary', 'project']);
        }
    }


    public function selectCommon(){
        return " P.BuildAddress, P.CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as PerformDate, P.InvestdocNo as Investdoc";
    }


    // Province

    public function insertR1C5_6($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ParentID1, ItemName, SectorCateValue, Company, T1Temporary, Project,
        BuildAddress, CapableDesign, PerformDate, Investdoc, Path, ParentCompanyNo, Norder, Detail, I1, I2)
        SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ParentID1, ItemName, SectorCateValue, CompanyNo, ProjectCateValue as T1Temporary, ProjectNo,
        BuildAddress, CapableDesign, PerformDate, Investdoc, Path, ParentNo, 1 as Norder, 1 as Detail,
        SUM(I) as I1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) as I2 FROM(
        SELECT CONCAT('A#', SC.CateValue, '#', C.CompanyNo, '#', PC.CateValue) as ParentID,
        IF(C.ParentNo IS NOT NULL, CONCAT('A#', SC.CateValue, '#', C.ParentNo, '#', PC.CateValue, '#', A.ProjectNo), '') as ParentID1,
        A.ProjectName as ItemName, SC.CateValue as SectorCateValue, C.CompanyNo, PC.CateValue as ProjectCateValue,
        A.ProjectNo, " . $this->selectCommon() . ",
        A.BudgetLevel, SUM(LCDebitAmount - LCCreditAmount) as I, C.Path, C.ParentNo
        FROM act_gl_books as A
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN capital as CA on A.CapitalID = CA.CapitalID
        WHERE A.AccountNo = '091'
        AND SC.CateNo = '200030301'
        AND PC.CateNo = '009' AND PC.CateValue = '1'
        AND CA.CapitalInOut = 1
        $whereDebt
        GROUP BY A.BudgetLevel, SC.CateValue, C.CompanyNo, A.ProjectNo
        ) as CH GROUP BY SectorCateValue, CompanyNo, ProjectNo
        ";

        DB::statement($sql);
    }

    public function insertR1C7_8_9_10($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ParentID1, ItemName, SectorCateValue, Company, T1Temporary, Project,
        BuildAddress, CapableDesign, PerformDate, Investdoc, Path, ParentCompanyNo, Norder, Detail, I3, I4, I5, I6)
        SELECT * FROM
        (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ParentID1, ItemName, SectorCateValue, CompanyNo, ProjectCateValue as T1Temporary,
        ProjectNo, BuildAddress, CapableDesign, PerformDate, Investdoc, Path, ParentNo, 1 as Norder, 1 as Detail,
        SUM(CASE WHEN AccountNo IN ('0441', '0442') THEN I ELSE 0 END) as A1,
        SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel = 1 THEN I ELSE 0 END) as A2,
        SUM(CASE WHEN AccountNo IN ('0411', '0412') THEN I ELSE 0 END) as A3,
        SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel = 1 THEN I ELSE 0 END) as A4
        FROM(
        SELECT CONCAT('A#', SC.CateValue, '#', C.CompanyNo, '#', PC.CateValue) as ParentID,
        CONCAT('A#', SC.CateValue, '#', C.ParentNo, '#', PC.CateValue, '#', A.ProjectNo) as ParentID1,
        A.ProjectName as ItemName, SC.CateValue as SectorCateValue, C.CompanyNo, PC.CateValue as ProjectCateValue,
        A.ProjectNo, " . $this->selectCommon() . ",
        A.AccountNo, A.BudgetLevel, SUM(LCDebitAmount) as I, C.Path, C.ParentNo
        FROM act_gl_books as A
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN capital as CA on A.CapitalID = CA.CapitalID
        WHERE A.AccountNo IN ('0441', '0442', '0411', '0412')
        AND SC.CateNo = '200030301'
        AND PC.CateNo = '009' AND PC.CateValue = '1'
        AND CA.CapitalInOut = 1
        $whereCondition
        GROUP BY A.AccountNo, A.BudgetLevel, SC.CateValue, C.CompanyNo, A.ProjectNo
        ) as CH GROUP BY SectorCateValue, CompanyNo, ProjectNo) as DT ON DUPLICATE KEY UPDATE I3 = A1, I4 = A2, I5 = A3, I6 = A4
        ";

        DB::statement($sql);
    }


    public function insertDistrictR1C5_6($whereDebt, $provinceNo){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, SectorCateValue, District, T1Temporary, Project,
        BuildAddress, CapableDesign, PerformDate, Investdoc, Norder, Detail, I1, I2)
        SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, SectorCateValue, DistrictNo, ProjectCateValue as T1Temporary, ProjectNo,
        BuildAddress, CapableDesign, PerformDate, Investdoc, 2 as Norder, 1 as Detail,
        SUM(I) as I1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) as I2 FROM(
        SELECT CONCAT('A#', SC.CateValue, '#', C.DistrictNo, '#', PC.CateValue) as ParentID,
        A.ProjectName as ItemName, SC.CateValue as SectorCateValue, C.DistrictNo, PC.CateValue as ProjectCateValue,
        A.ProjectNo, " . $this->selectCommon() . ",
        A.BudgetLevel, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN capital as CA on A.CapitalID = CA.CapitalID
        WHERE A.AccountNo = '091'
        AND SC.CateNo = '200030301'
        AND PC.CateNo = '009' AND PC.CateValue = '1'
        AND C.ProvinceNo = '$provinceNo'
        AND CA.CapitalInOut = 1
        AND C.ManagementLevel >= 3
        $whereDebt
        GROUP BY A.BudgetLevel, SC.CateValue, C.DistrictNo, A.ProjectNo
        ) as CH GROUP BY SectorCateValue, DistrictNo, ProjectNo
        ";

        DB::statement($sql);
    }

    public function insertR2C5_6($whereDebt, $fromDate, $toDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ParentID1, ItemName, SectorCateValue, Company, T1Temporary, T2Temporary, Project,
        BuildAddress, CapableDesign, PerformDate, Investdoc, Path, ParentCompanyNo, Norder, Detail, I1, I2)
        SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ParentID1, ItemName, SectorCateValue, CompanyNo, ProjectCateValue as T1Temporary, T2Temporary,
        ProjectNo, BuildAddress, CapableDesign, PerformDate, Investdoc, Path, ParentNo, 1 as Norder, 1 as Detail,
        SUM(I) as I1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) as I2
        FROM(
        SELECT CONCAT('A#', SC.CateValue, '#', C.CompanyNo, '#', PC.CateValue, '#'," . $this->setPerformDateNo($fromDate, $toDate) . ") as ParentID,
        CONCAT('A#', SC.CateValue, '#', C.ParentNo, '#', PC.CateValue, '#'," . $this->setPerformDateNo($fromDate, $toDate) . ", '#', A.ProjectNo) as ParentID1,
        A.ProjectName as ItemName, SC.CateValue as SectorCateValue, C.CompanyNo, PC.CateValue as ProjectCateValue, " . $this->setPerformDateNo($fromDate, $toDate) . " as T2Temporary,
        A.ProjectNo, " . $this->selectCommon() . ",
        A.BudgetLevel, SUM(LCDebitAmount - LCCreditAmount) as I, C.Path, C.ParentNo
        FROM act_gl_books as A
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN capital as CA on A.CapitalID = CA.CapitalID
        WHERE A.AccountNo = '091'
        AND SC.CateNo = '200030301'
        AND PC.CateNo = '009' AND PC.CateValue = '2'
        AND CA.CapitalInOut = 1
        $whereDebt
        GROUP BY A.BudgetLevel, SC.CateValue, C.CompanyNo, A.ProjectNo
        ) as CH GROUP BY SectorCateValue, CompanyNo, ProjectNo
        ";

        DB::statement($sql);
    }

    public function insertR2C7_8_9_10($whereCondition, $fromDate, $toDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ParentID1, ItemName, SectorCateValue, Company, T1Temporary, T2Temporary, Project,
        BuildAddress, CapableDesign, PerformDate, Investdoc, Path, ParentCompanyNo, Norder, Detail, I3, I4, I5, I6)
        SELECT * FROM
        (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ParentID1, ItemName, SectorCateValue, CompanyNo, ProjectCateValue as T1Temporary, T2Temporary,
        ProjectNo, BuildAddress, CapableDesign, PerformDate, Investdoc, Path, ParentNo, 1 as Norder, 1 as Detail,
        SUM(CASE WHEN AccountNo IN ('0441', '0442') THEN I ELSE 0 END) as A1,
        SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel = 1 THEN I ELSE 0 END) as A2,
        SUM(CASE WHEN AccountNo IN ('0411', '0412') THEN I ELSE 0 END) as A3,
        SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel = 1 THEN I ELSE 0 END) as A4
        FROM(
        SELECT CONCAT('A#', SC.CateValue, '#', C.CompanyNo, '#', PC.CateValue, '#'," . $this->setPerformDateNo($fromDate, $toDate) . ") as ParentID,
        CONCAT('A#', SC.CateValue, '#', C.ParentNo, '#', PC.CateValue, '#'," . $this->setPerformDateNo($fromDate, $toDate) . ", '#', A.ProjectNo) as ParentID1,
        A.ProjectName as ItemName, SC.CateValue as SectorCateValue, C.CompanyNo, PC.CateValue as ProjectCateValue, " . $this->setPerformDateNo($fromDate, $toDate) . " as T2Temporary,
        A.ProjectNo, " . $this->selectCommon() . ",
        A.AccountNo, A.BudgetLevel, SUM(LCDebitAmount) as I, C.Path, C.ParentNo
        FROM act_gl_books as A
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN capital as CA on A.CapitalID = CA.CapitalID
        WHERE A.AccountNo IN ('0441', '0442', '0411', '0412')
        AND SC.CateNo = '200030301'
        AND PC.CateNo = '009' AND PC.CateValue = '2'
        AND CA.CapitalInOut = 1
        $whereCondition
        GROUP BY A.AccountNo, A.BudgetLevel, SC.CateValue, C.CompanyNo, A.ProjectNo
        ) as CH GROUP BY SectorCateValue, CompanyNo, ProjectNo) as DT ON DUPLICATE KEY UPDATE I3 = A1, I4 = A2, I5 = A3, I6 = A4
        ";

        DB::statement($sql);
    }

    public function setPerformDateNo($fromDate, $toDate){
        return "CASE WHEN (YEAR(P.StartedDate) BETWEEN $fromDate - 4 AND $fromDate) AND (YEAR(P.ExpectedFinishDate) BETWEEN $fromDate AND $toDate ) THEN '01'
                     WHEN YEAR(ExpectedStartDate) BETWEEN $fromDate AND $toDate THEN '02' END ";
    }

    public function insertDistrictR1C7_8_9_10($whereCondition, $provinceNo){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, SectorCateValue, District, T1Temporary, Project,
        BuildAddress, CapableDesign, PerformDate, Investdoc, Norder, Detail, I3, I4, I5, I6)
        SELECT * FROM
        (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, SectorCateValue, DistrictNo, ProjectCateValue as T1Temporary,
        ProjectNo, BuildAddress, CapableDesign, PerformDate, Investdoc, 2 as Norder, 1 as Detail,
        SUM(CASE WHEN AccountNo IN ('0441', '0442') THEN I ELSE 0 END) as A1,
        SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel = 1 THEN I ELSE 0 END) as A2,
        SUM(CASE WHEN AccountNo IN ('0411', '0412') THEN I ELSE 0 END) as A3,
        SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel = 1 THEN I ELSE 0 END) as A4
        FROM(
        SELECT CONCAT('A#', SC.CateValue, '#', C.DistrictNo, '#', PC.CateValue) as ParentID,
        A.ProjectName as ItemName, SC.CateValue as SectorCateValue, C.DistrictNo, C.CompanyNo, PC.CateValue as ProjectCateValue,
        A.ProjectNo, " . $this->selectCommon() . ",
        A.AccountNo, A.BudgetLevel, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN capital as CA on A.CapitalID = CA.CapitalID
        WHERE A.AccountNo IN ('0441', '0442', '0411', '0412')
        AND SC.CateNo = '200030301'
        AND PC.CateNo = '009' AND PC.CateValue = '1'
        AND C.ProvinceNo = '$provinceNo'
        AND CA.CapitalInOut = 1
        AND C.ManagementLevel >= 3
        $whereCondition
        GROUP BY A.AccountNo, A.BudgetLevel, SC.CateValue, C.DistrictNo, A.ProjectNo
        ) as CH GROUP BY SectorCateValue, DistrictNo, ProjectNo) as DT ON DUPLICATE KEY UPDATE I3 = A1, I4 = A2, I5 = A3, I6 = A4
        ";

        DB::statement($sql);
    }

    public function insertDistrictR2C5_6($whereDebt, $provinceNo, $fromDate, $toDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, SectorCateValue, District, T1Temporary, T2Temporary, Project,
        BuildAddress, CapableDesign, PerformDate, Investdoc, Norder, Detail, I1, I2)
        SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, SectorCateValue, DistrictNo, ProjectCateValue as T1Temporary, T2Temporary,
        ProjectNo, BuildAddress, CapableDesign, PerformDate, Investdoc, 2 as Norder, 1 as Detail,
        SUM(I) as I1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) as I2
        FROM(
        SELECT CONCAT('A#', SC.CateValue, '#', C.DistrictNo, '#', PC.CateValue, '#'," . $this->setPerformDateNo($fromDate, $toDate) . ") as ParentID,
        A.ProjectName as ItemName, SC.CateValue as SectorCateValue, C.DistrictNo, PC.CateValue as ProjectCateValue, " . $this->setPerformDateNo($fromDate, $toDate) . " as T2Temporary,
        A.ProjectNo, " . $this->selectCommon() . ",
        A.BudgetLevel, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN capital as CA on A.CapitalID = CA.CapitalID
        WHERE A.AccountNo = '091'
        AND SC.CateNo = '200030301'
        AND PC.CateNo = '009' AND PC.CateValue = '2'
        AND C.ProvinceNo = '$provinceNo'
        AND CA.CapitalInOut = 1
        AND C.ManagementLevel >= 3
        $whereDebt
        GROUP BY A.BudgetLevel, SC.CateValue, C.DistrictNo, A.ProjectNo
        ) as CH GROUP BY SectorCateValue, DistrictNo, ProjectNo
        ";

        DB::statement($sql);
    }

    public function insertDistrictR2C7_8_9_10($whereCondition, $provinceNo, $fromDate, $toDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, SectorCateValue, District, T1Temporary, T2Temporary, Project,
        BuildAddress, CapableDesign, PerformDate, Investdoc, Norder, Detail, I3, I4, I5, I6)
        SELECT * FROM
        (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, SectorCateValue, DistrictNo, ProjectCateValue as T1Temporary, T2Temporary,
        ProjectNo, BuildAddress, CapableDesign, PerformDate, Investdoc, 2 as Norder, 1 as Detail,
        SUM(CASE WHEN AccountNo IN ('0441', '0442') THEN I ELSE 0 END) as A1,
        SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel = 1 THEN I ELSE 0 END) as A2,
        SUM(CASE WHEN AccountNo IN ('0411', '0412') THEN I ELSE 0 END) as A3,
        SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel = 1 THEN I ELSE 0 END) as A4
        FROM(
        SELECT CONCAT('A#', SC.CateValue, '#', C.DistrictNo, '#', PC.CateValue, '#'," . $this->setPerformDateNo($fromDate, $toDate) . ") as ParentID,
        A.ProjectName as ItemName, SC.CateValue as SectorCateValue, C.DistrictNo, PC.CateValue as ProjectCateValue, " . $this->setPerformDateNo($fromDate, $toDate) . " as T2Temporary,
        A.ProjectNo, " . $this->selectCommon() . ",
        A.AccountNo, A.BudgetLevel, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN capital as CA on A.CapitalID = CA.CapitalID
        WHERE A.AccountNo IN ('0441', '0442', '0411', '0412')
        AND SC.CateNo = '200030301'
        AND PC.CateNo = '009' AND PC.CateValue = '2'
        AND C.ProvinceNo = '$provinceNo'
        AND CA.CapitalInOut = 1
        AND C.ManagementLevel >= 3
        $whereCondition
        GROUP BY A.AccountNo, A.BudgetLevel, SC.CateValue, C.DistrictNo, A.ProjectNo
        ) as CH GROUP BY SectorCateValue, DistrictNo, ProjectNo) as DT ON DUPLICATE KEY UPDATE I3 = A1, I4 = A2, I5 = A3, I6 = A4
        ";

        DB::statement($sql);
    }

    public function insertCommuneR1C5_6($whereDebt, $districtNo){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, SectorCateValue, Commune, T1Temporary, Project,
        BuildAddress, CapableDesign, PerformDate, Investdoc, Norder, Detail, I1, I2)
        SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, SectorCateValue, CommuneNo, ProjectCateValue as T1Temporary, ProjectNo,
        BuildAddress, CapableDesign, PerformDate, Investdoc, 2 as Norder, 1 as Detail,
        SUM(I) as I1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) as I2 FROM(
        SELECT CONCAT('A#', SC.CateValue, '#', C.CommuneNo, '#', PC.CateValue) as ParentID,
        A.ProjectName as ItemName, SC.CateValue as SectorCateValue, C.CommuneNo, PC.CateValue as ProjectCateValue,
        A.ProjectNo, " . $this->selectCommon() . ",
        A.BudgetLevel, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN capital as CA on A.CapitalID = CA.CapitalID
        WHERE A.AccountNo = '091'
        AND SC.CateNo = '200030301'
        AND PC.CateNo = '009' AND PC.CateValue = '1'
        AND C.DistrictNo = '$districtNo'
        AND CA.CapitalInOut = 1
        AND C.ManagementLevel = 4
        $whereDebt
        GROUP BY A.BudgetLevel, SC.CateValue, C.CommuneNo, A.ProjectNo
        ) as CH GROUP BY SectorCateValue, CommuneNo, ProjectNo
        ";

        DB::statement($sql);


    }

    public function insertCommuneR1C7_8_9_10($whereCondition, $districtNo){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, SectorCateValue, Commune, T1Temporary, Project,
        BuildAddress, CapableDesign, PerformDate, Investdoc, Norder, Detail, I3, I4, I5, I6)
        SELECT * FROM
        (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, SectorCateValue, CommuneNo, ProjectCateValue as T1Temporary,
        ProjectNo, BuildAddress, CapableDesign, PerformDate, Investdoc, 2 as Norder, 1 as Detail,
        SUM(CASE WHEN AccountNo IN ('0441', '0442') THEN I ELSE 0 END) as A1,
        SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel = 1 THEN I ELSE 0 END) as A2,
        SUM(CASE WHEN AccountNo IN ('0411', '0412') THEN I ELSE 0 END) as A3,
        SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel = 1 THEN I ELSE 0 END) as A4
        FROM(
        SELECT CONCAT('A#', SC.CateValue, '#', C.CommuneNo, '#', PC.CateValue) as ParentID,
        A.ProjectName as ItemName, SC.CateValue as SectorCateValue, C.CommuneNo, C.CompanyNo, PC.CateValue as ProjectCateValue,
        A.ProjectNo, " . $this->selectCommon() . ",
        A.AccountNo, A.BudgetLevel, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN capital as CA on A.CapitalID = CA.CapitalID
        WHERE A.AccountNo IN ('0441', '0442', '0411', '0412')
        AND SC.CateNo = '200030301'
        AND PC.CateNo = '009' AND PC.CateValue = '1'
        AND C.DistrictNo = '$districtNo'
        AND CA.CapitalInOut = 1
        AND C.ManagementLevel = 4
        $whereCondition
        GROUP BY A.AccountNo, A.BudgetLevel, SC.CateValue, C.CommuneNo, A.ProjectNo
        ) as CH GROUP BY SectorCateValue, CommuneNo, ProjectNo) as DT ON DUPLICATE KEY UPDATE I3 = A1, I4 = A2, I5 = A3, I6 = A4
        ";

        DB::statement($sql);
    }

    public function insertCommuneR2C5_6($whereDebt, $districtNo, $fromDate, $toDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, SectorCateValue, Commune, T1Temporary, T2Temporary, Project,
        BuildAddress, CapableDesign, PerformDate, Investdoc, Norder, Detail, I1, I2)
        SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, SectorCateValue, CommuneNo, ProjectCateValue as T1Temporary, T2Temporary,
        ProjectNo, BuildAddress, CapableDesign, PerformDate, Investdoc, 2 as Norder, 1 as Detail,
        SUM(I) as I1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) as I2
        FROM(
        SELECT CONCAT('A#', SC.CateValue, '#', C.CommuneNo, '#', PC.CateValue, '#'," . $this->setPerformDateNo($fromDate, $toDate) . ") as ParentID,
        A.ProjectName as ItemName, SC.CateValue as SectorCateValue, C.CommuneNo, PC.CateValue as ProjectCateValue, " . $this->setPerformDateNo($fromDate, $toDate) . " as T2Temporary,
        A.ProjectNo, " . $this->selectCommon() . ",
        A.BudgetLevel, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN capital as CA on A.CapitalID = CA.CapitalID
        WHERE A.AccountNo = '091'
        AND SC.CateNo = '200030301'
        AND PC.CateNo = '009' AND PC.CateValue = '2'
        AND C.DistrictNo = '$districtNo'
        AND CA.CapitalInOut = 1
        AND C.ManagementLevel = 4
        $whereDebt
        GROUP BY A.BudgetLevel, SC.CateValue, C.CommuneNo, A.ProjectNo
        ) as CH GROUP BY SectorCateValue, CommuneNo, ProjectNo
        ";

        DB::statement($sql);
    }

    public function insertCommuneR2C7_8_9_10($whereCondition, $districtNo, $fromDate, $toDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, SectorCateValue, Commune, T1Temporary, T2Temporary, Project,
        BuildAddress, CapableDesign, PerformDate, Investdoc, Norder, Detail, I3, I4, I5, I6)
        SELECT * FROM
        (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, SectorCateValue, CommuneNo, ProjectCateValue as T1Temporary, T2Temporary,
        ProjectNo, BuildAddress, CapableDesign, PerformDate, Investdoc, 2 as Norder, 1 as Detail,
        SUM(CASE WHEN AccountNo IN ('0441', '0442') THEN I ELSE 0 END) as A1,
        SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel = 1 THEN I ELSE 0 END) as A2,
        SUM(CASE WHEN AccountNo IN ('0411', '0412') THEN I ELSE 0 END) as A3,
        SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel = 1 THEN I ELSE 0 END) as A4
        FROM(
        SELECT CONCAT('A#', SC.CateValue, '#', C.CommuneNo, '#', PC.CateValue, '#'," . $this->setPerformDateNo($fromDate, $toDate) . ") as ParentID,
        A.ProjectName as ItemName, SC.CateValue as SectorCateValue, C.CommuneNo, PC.CateValue as ProjectCateValue, " . $this->setPerformDateNo($fromDate, $toDate) . " as T2Temporary,
        A.ProjectNo, " . $this->selectCommon() . ",
        A.AccountNo, A.BudgetLevel, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN capital as CA on A.CapitalID = CA.CapitalID
        WHERE A.AccountNo IN ('0441', '0442', '0411', '0412')
        AND SC.CateNo = '200030301'
        AND PC.CateNo = '009' AND PC.CateValue = '2'
        AND C.DistrictNo = '$districtNo'
        AND CA.CapitalInOut = 1
        AND C.ManagementLevel = 4
        $whereCondition
        GROUP BY A.AccountNo, A.BudgetLevel, SC.CateValue, C.CommuneNo, A.ProjectNo
        ) as CH GROUP BY SectorCateValue, CommuneNo, ProjectNo) as DT ON DUPLICATE KEY UPDATE I3 = A1, I4 = A2, I5 = A3, I6 = A4
        ";

        DB::statement($sql);
    }


    // sum Row ParentID

    public function sumRow1($maxLevel, $start = 0){
        for ($i = $maxLevel; $i > $start; $i--) {
            $sql = "SELECT ItemID, ParentID1, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4,
            SUM(M.I5) AS I5, SUM(M.I6) AS I6
            FROM table_tmp M
            WHERE ParentID1 != '' AND M.Level = " . $i . " GROUP BY ParentID1";
            $result = DB::select($sql);
            $result = (array)$result;
            if ($result) {
                foreach ($result as $value) {
                    $value = (array)$value;
                    $I1 = ($value['I1']) ? $value['I1'] : 0;
                    $I2 = ($value['I2']) ? $value['I2'] : 0;
                    $I3 = ($value['I3']) ? $value['I3'] : 0;
                    $I4 = ($value['I4']) ? $value['I4'] : 0;
                    $I5 = ($value['I5']) ? $value['I5'] : 0;
                    $I6 = ($value['I6']) ? $value['I6'] : 0;
                    $update = "UPDATE table_tmp
                SET I1 = I1 + " . $I1 . ", I2 = I2 + " . $I2 . ", I3 = I3 + " . $I3 . ", I4 = I4 + " . $I4 . ",
                I5 = I5 + " . $I5 . ", I6 = I6 + " . $I6 . "
                WHERE ItemID = '" . $value['ParentID1'] . "'
                AND table_tmp.Level = " . $i;
                    DB::statement($update);
                }
            }
        }

    }

    // sum Row ParentID
    public function sumRow($maxLevel, $start = 0){
        for ($i = $maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4,
            SUM(M.I5) AS I5, SUM(M.I6) AS I6
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
                    $I5 = ($value['I5']) ? $value['I5'] : 0;
                    $I6 = ($value['I6']) ? $value['I6'] : 0;
                    $update = "UPDATE table_tmp
                SET I1 = I1 + " . $I1 . ", I2 = I2 + " . $I2 . ", I3 = I3 + " . $I3 . ", I4 = I4 + " . $I4 . ",
                I5 = I5 + " . $I5 . ", I6 = I6 + " . $I6 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                AND table_tmp.Level = " . $j;
                    DB::statement($update);
                }
            }
        }

    }

    public function sortSector(){
        DB::table('table_tmp')->where('Level', 1)->update(['Norder'=> 1]);
    }

    public function getParentDataTreeCommonMB05($table, $id = null){
        $uniqueItems = [];
        $arrID = [];
        for($i = count($table) - 2; $i >=0 ; $i--){
            $insertArr = [];
            $insertNoParent = [];
            if($i == count($table) - 2){
                $insert = [];
                $groupBy = '';
                $where = ' 1 = 1';
                for($j = 0; $j <= $i; $j++) {
                    if(is_array($table[$j])){
                        $tmp = $table[$j][0];
                        $argValue = $table[$j][1];
                    }
                    else{
                        $tmp = $table[$j];
                    }
                    $join = ucfirst($tmp);
                    if(strpos($tmp,'_') !== false){
                        $join = join(array_map('ucfirst',explode('_',$tmp)));
                    }
                    if(is_array($table[$j])){
                        if(strpos($tmp,'_cate_value') === false){
                            $where .= " AND $join = '" . $argValue . "'";
                        }
                    }
                    $where .= " AND $join != '' ";
                    if($j < $i){
                        $join .= ',';
                    }
                    $groupBy .= $join;
                }
                $like = '';
                if($id !== null){
                    $like = "ItemID LIKE '" . $id . "%' AND";
                }
                if($groupBy != ''){
                    $sql = "SELECT * FROM table_tmp where $like $where GROUP BY $groupBy";
                }
                else{
                    $sql = "SELECT * FROM table_tmp where $like $where";
                }
                $listDetail = DB::select($sql);
//                $listParent = explode(',',$groupBy);
                if(is_array($table[$i])){
                    $tmp = $table[$i][0];
                    $argCateNo = $table[$i][1];
                }
                else{
                    $tmp = $table[$i];
                    $argCateNo = '';
                }
                $argValue = join(array_map('ucfirst',explode('_',$tmp)));
                if($listDetail){
                    foreach ($listDetail as $item){
                        $item = (array)$item;
//                        if($listParent != ''){
//                            foreach($listParent as $value){
//                                $insert[] = [
//                                    $value=> $item[$value],
//                                ];
//                            }
//                        }
                        $itemID = $item['ParentID'];
                        $lastIndex = strrpos($itemID, '#');
                        $parentID = substr($itemID,0,$lastIndex);
                        if (strpos($tmp, 'Temporary') !== false) {
                            $listTemporary = DB::table($tmp)->get();
                            foreach($listTemporary as $data){
                                $insertArr[] = [
                                    'ItemID'=> $parentID . '#' . $data->ItemID,
                                    'ParentID'=> $parentID,
                                    'ItemName'=> $data->ItemName,
                                    'Detail'=> 0,
                                    'Norder'=> $item['Norder'],
                                    'Company'=> $item['Company'],
                                    'ParentCompanyNo'=> $item['ParentCompanyNo'],

                                ];
//                                $insertArr[] = call_user_func_array('array_merge', $insert);
                            }
                        }
                        else{
                            $itemName = $this->getItemNameReport($tmp, $argCateNo, $item[$argValue]);
                            $insertArr[] = [
                                'ItemID'=> $itemID,
                                'ParentID'=> $parentID,
                                'ItemName'=> $itemName,
                                'Detail'=> 0,
                                'Norder'=> $item['Norder'],
                                'Company'=> $item['Company'],
                                'ParentCompanyNo'=> $item['ParentCompanyNo'],
                            ];
//                            $insertArr[] = call_user_func_array('array_merge', $insert);
                        }
                    }
                    foreach($insertArr as $insert){
                        if(!in_array($insert['ItemID'], $arrID)){
                            $arrID[] = $insert['ItemID'];
                            $uniqueItems[] = $insert;
                        }
                    }
                    DB::table('table_tmp')->insertOrIgnore($uniqueItems);
                }
            }
            else{
                if($uniqueItems){
                    $arrItemID = [];
                    foreach($uniqueItems as $item){
                        if(!in_array($item['ParentID'], $arrItemID)){
                            $arrItemID[] = $item['ParentID'];
                            $lastIndex = strrpos($item['ParentID'], '#');
                            if($table[$i] == 'company' && $item['ParentCompanyNo'] != null){
                                $parentID = str_replace($item['Company'], $item['ParentCompanyNo'], $item['ParentID']);
                            }
                            else{
                                $parentID = substr($item['ParentID'],0,$lastIndex);
                            }
                            $argValue = substr($item['ParentID'], $lastIndex + 1, strlen($item['ParentID']));
                            if(is_array($table[$i])){
                                $tmp = $table[$i][0];
                                $argCateNo = $table[$i][1];
                            }
                            else{
                                $tmp = $table[$i];
                                $argCateNo = '';
                            }
                            if (strpos($tmp, 'Temporary') !== false) {
                                $listTemporary = DB::table($tmp)->get();
                                foreach($listTemporary as $data){
                                    $insertArr[] = [
                                        'ItemID'=> $parentID . '#' . $data->ItemID,
                                        'ParentID'=> $parentID,
                                        'ItemName'=> $data->ItemName,
                                        'Detail'=> 0,
                                        'Norder'=> $item['Norder'],
                                        'Company'=> $item['Company'],
                                        'ParentCompanyNo'=> $item['ParentCompanyNo'],
                                    ];
                                }
                            }
                            else{
                                $itemName = $this->getItemNameReport($tmp, $argCateNo, $argValue);
                                if($table[$i] == 'company' && $item['ParentCompanyNo'] != null){
                                    $insertNoParent[] =
                                        [
                                            'ItemID' => $item['ParentID'],
                                            'ParentID'=> $parentID,
                                            'ItemName'=> $itemName,
                                            'Detail'=> 0,
                                            'Norder'=> $item['Norder'],
                                            'Company'=> $item['Company'],
                                            'ParentCompanyNo'=> $item['ParentCompanyNo'],
                                        ];
                                }
                                else{
                                    $insertArr[] = [
                                        'ItemID' => $item['ParentID'],
                                        'ParentID'=> $parentID,
                                        'ItemName'=> $itemName,
                                        'Detail'=> 0,
                                        'Norder'=> $item['Norder'],
                                        'Company'=> $item['Company'],
                                        'ParentCompanyNo'=> $item['ParentCompanyNo'],
                                    ];
                                }
                            }
                        }
                    }
                    DB::table('table_tmp')->insertOrIgnore($insertArr);
                    DB::table('table_tmp')->insertOrIgnore($insertNoParent);
                    $uniqueItems = $insertArr;
                }

            }

        }
    }

}
