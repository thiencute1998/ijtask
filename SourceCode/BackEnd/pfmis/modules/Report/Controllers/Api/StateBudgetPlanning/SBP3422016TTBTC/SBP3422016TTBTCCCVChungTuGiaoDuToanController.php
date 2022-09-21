<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Symfony\Component\ErrorHandler\Debug;

class SBP3422016TTBTCCCVChungTuGiaoDuToanController extends Controller {
    public function sbptt3422016ttbtcchungtugiaodutoan(Request $request){

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
        if(isset($filter['queryFilter'])){
            $book = DB::table('act_gvouc_trans')->where('TransID',$filter['queryFilter'])->first();
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
        $company = Company::find($companyID);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $dateTime = "Vĩnh Phú, ngày $date tháng $month năm $yearC";
        if ($company) {
            $dateTime = $company->Address . ", ngày $date tháng $month năm $yearC";
        }
        $parentReviewCompany = "";
        if(isset($book)){
            if($book->ParentCompanyID){
                $parentReviewCompany = DB::table('company')->where('CompanyID','=',$book->ParentCompanyID)->first();
            }
            if($parentReviewCompany){
                $parentReviewCompany = DB::table('company')->where('CompanyID','=',$parentReviewCompany->ParentID)->first();
            }
            if($parentReviewCompany){
                $parentReviewCompany = $parentReviewCompany->CompanyName;
            }
            $response = [
                'master' => [
                    "Year" => $year,
                    "TransNo" => "$book->TransNo",
                    "eTransNo" => $book->eTransNo,
                    "DateTime"=>$dateTime,
                    "MakeCompany"=>$book->CompanyName,
                    "ReviewCompany"=>$book->ParentCompanyName,
                    "ParentReviewCompany"=> $parentReviewCompany,
                    "represent"=>"dinh van tuan"
                ],
            ];
        }
        else{
            $response = [
                'master' => [
                    "Year" => "",
                    "TransNo" => "",
                    "eTransNo" => '',
                ],
            ];
        }
        $json['data'] = $response;
        return json_encode($json);
    }

    // c1




}
