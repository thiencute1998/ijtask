<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBPESTIMATEPLAN;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Symfony\Component\ErrorHandler\Debug;

class SBPESTIMATEPLANController extends Controller
{
    public function SBPESTIMATEPLAN_SBPESTIMATEPLAN_01(Request $request)
    {
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

        $yearPre = (int)$year - 1;
        $yearNext = (int)$year + 1;
        $whereCondition = "AND YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year $whereCompany AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18 ";
        $whereConditionPre = "AND YEAR(A.PeriodFromDate) = $yearPre AND YEAR(A.PeriodToDate) = $yearPre $whereCompany AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18 ";
        $whereConditionNext = "AND YEAR(A.PeriodFromDate) = $yearNext AND YEAR(A.PeriodToDate) = $yearNext $whereCompany $whereDirection AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18 ";

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
          `Year` varchar(5) DEFAULT '',
          `SbiCategoryNo` varchar(20) DEFAULT '',
          `ConAccountNo` varchar(20) DEFAULT '',
          `RevenueNo` varchar(20) DEFAULT '',
          `CateValue` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('table_tmp_sbpmakeplan_01')->get();
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
                'Year' => $template->Year,
                'ConAccountNo' => $template->ConAccountNo,
                'RevenueNo' => $template->RevenueNo,
                'CateValue' => $template->CateValue,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
    }

    public function SBPESTIMATEPLAN_InsertTableTmp(Request $request)
    {
        $sqlInsert = "SELECT A.ConAccountNo ConAccountNo, A.RevenueNo RevenueNo, CT.NormID NormID, NC.CateValue CateValue  FROM act_gl_books A INNER JOIN norm_table_item CT ON CT.NormTableItemID = A.NormTableItemID INNER JOIN norm_cate NC ON NC.NormID = CT.NormID  WHERE 1 GROUP BY ConAccountNo, RevenueNo, CateValue";
        DB::statement($sqlInsert);

    }

}
