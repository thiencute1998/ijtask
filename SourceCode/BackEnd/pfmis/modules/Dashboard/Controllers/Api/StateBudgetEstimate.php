<?php
namespace Module\Dashboard\Controllers;

use IjCore\IjDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Dashboard\Traits\Dashboard;

class StateBudgetEstimate extends Controller{
    use Dashboard;
    // du toan nsnn
    public function index(Request $request){
        $filter = $request->post('filter');
        $whereCondition = '';
        $whereConditionPreYear = '';

        if (!empty($filter['FromDate']) && !empty($filter['ToDate'])) {
            $fromDate = IjDate::ConvertClientDateToServerDate($filter['FromDate']);
            $toDate = IjDate::ConvertClientDateToServerDate($filter['ToDate']);
            $whereCondition .= "AND A.PeriodFromDate >= $fromDate ";
            $whereCondition .= "AND A.PeriodToDate <= $toDate";

            $time = strtotime($fromDate);
            $yearFromDate = date("Y", $time);

            $time = strtotime($toDate);
            $yearToDate = date("Y", $time);

            if ($yearFromDate == $yearToDate) {
                $fromDatePreYear = str_replace($yearFromDate, (int)$yearFromDate - 1, $fromDate);
                $toDatePreYear = str_replace($yearToDate, (int)$yearToDate - 1, $toDate);
                $whereConditionPreYear .= "AND A.PeriodFromDate >= $fromDatePreYear AND A.PeriodToDate <= $toDatePreYear";
            }
        }

        // Total revenue
        $dtt = DB::table('act_gl_books AS A')
            ->join('revenue AS R', 'R.RevenueID', '=', 'A.RevenueID')
            ->join('revenue_cate AS RC', 'A.RevenueID', '=', 'RC.RevenueID')
            ->where('A.AccountNo', '=', '0341')
            ->whereRaw(DB::raw("LEFT(RC.CateNo, 3) = '203' $whereCondition"))
            ->where('R.Level', '=', 2)
            ->sum('A.FCDebitAmount');

        // Total revenue pre year
        $dtt_nt = DB::table('act_gl_books AS A')
            ->join('revenue AS R', 'R.RevenueID', '=', 'A.RevenueID')
            ->join('revenue_cate AS RC', 'A.RevenueID', '=', 'RC.RevenueID')
            ->where('A.AccountNo', '=', '0341')
            ->where(DB::raw("LEFT(RC.CateNo, 3) = '203' $whereConditionPreYear"))
            ->where('R.Level', '=', 2)
            ->sum('A.FCDebitAmount');

        // EstimatePlan Revenue
        $uth_t = DB::table('act_gl_books AS A')
            ->join('revenue AS R', 'R.RevenueID', '=', 'A.RevenueID')
            ->join('revenue_cate AS RC', 'A.RevenueID', '=', 'RC.RevenueID')
            ->where('A.AccountNo', '=', '0711')
            ->whereRaw(DB::raw("LEFT(RC.CateNo, 3) = '203' $whereConditionPreYear"))
            ->where('R.Level', '=', 2)
            ->sum('A.FCDebitAmount');

        // Total Expense
        $dtc = DB::table('act_gl_books AS A')
            ->join('revenue AS R', 'R.RevenueID', '=', 'A.RevenueID')
            ->join('revenue_cate AS RC', 'A.RevenueID', '=', 'RC.RevenueID')
            ->where('A.AccountNo', '=', '0441')
            ->whereRaw(DB::raw("LEFT(RC.CateNo, 3) = '203' $whereCondition"))
            ->where('R.Level', '=', 2)
            ->sum('A.FCDebitAmount');

        // Total Expense pre year
        $dtc_nt = DB::table('act_gl_books AS A')
            ->join('revenue AS R', 'R.RevenueID', '=', 'A.RevenueID')
            ->join('revenue_cate AS RC', 'A.RevenueID', '=', 'RC.RevenueID')
            ->where('A.AccountNo', '=', '0441')
            ->whereRaw(DB::raw("LEFT(RC.CateNo, 3) = '203' $whereConditionPreYear"))
            ->where('R.Level', '=', 2)
            ->sum('A.FCDebitAmount');

        // EstimatePlan Expense
        $uth_c = DB::table('act_gl_books AS A')
            ->join('revenue AS R', 'R.RevenueID', '=', 'A.RevenueID')
            ->join('revenue_cate AS RC', 'A.RevenueID', '=', 'RC.RevenueID')
            ->where('A.AccountNo', '=', '0721')
            ->whereRaw(DB::raw("LEFT(RC.CateNo, 3) = '203' $whereConditionPreYear"))
            ->where('R.Level', '=', 2)
            ->sum('A.FCDebitAmount');



        $data = [
            'dtt' => $dtt,
            'dtt_nt' => $dtt_nt,
            'uth_t' => $uth_t,
            'dtc' => $dtc,
            'dtc_nt' => $dtc_nt,
            'uth_c' => $uth_c
        ];
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function revenuePieChart(Request $request){
        $filter = $request->post('filter');
        $whereCondition = self::getWhereCondition($filter);
        Log::debug($whereCondition);
        $CateID = $request->post('CateID');
        $query = DB::table('act_gl_books AS A')
            ->select(DB::raw('R.RevenueName, R.RevenueID, R.RevenueNo, SUM(A.FCDebitAmount) AS FCDebitAmount'))
            ->join('revenue AS R', 'R.RevenueID', '=', 'A.RevenueID')
            ->join('revenue_cate AS RC', 'A.RevenueID', '=', 'RC.RevenueID')
            ->where('A.AccountNo', '=', '0341')
            ->whereRaw("LEFT(RC.CateNo, 3) = '203' $whereCondition");
        if (empty($CateID)) {
            $query->where('R.Level', '=', 3);
        }else{
            $query->where('RC.CateID', '=', $CateID);
            $query->where('R.Level', '=', 4);
        }
        $query->groupBy('R.ParentID');
        $data = $query->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function expensePieChart(Request $request){
        $filter = $request->post('filter');
        $whereCondition = self::getWhereCondition($filter);
        $CateID = $request->post('CateID');
        $query = DB::table('act_gl_books AS A')
            ->select(DB::raw('R.ExpenseName, R.ExpenseID, R.ExpenseNo, SUM(A.FCDebitAmount) AS FCDebitAmount'))
            ->join('expense AS R', 'R.ExpenseID', '=', 'A.ExpenseID')
            ->join('expense_cate AS RC', 'A.ExpenseID', '=', 'RC.ExpenseID')
            ->where('A.AccountNo', '=', '0441')
            ->whereRaw("LEFT(RC.CateNo, 3) = '203' $whereCondition");
        if (empty($CateID)) {
            $query->where('R.Level', '=', 3);
        }else{
            $query->where('RC.CateID', '=', $CateID);
            $query->where('R.Level', '=', 4);
        }
        $query->groupBy('R.ParentID');

        $data = $query->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    // Dự toán NSNN
    public function estimateRevenue(Request $request){
        $filter = $request->post('filter');
        $whereCondition = $this->getWhereCondition1($filter);
        // Tổng thợp thu NSNN

        $Tht = $this->estimateRevenueTht($filter);

        // Thu NSNN theo cơ cấu thu
        $Cct = $this->estimateCc($whereCondition, 1, array("'0341'", "'0711"));

        // Dự toán thu ngân sách theo kỳ ngân sách
        $Knst = $this->estimateKns($filter, 1);

        // Thu NSNN theo lĩnh vực
        $Lvt = $this->estimateRevenuetLvt($whereCondition);

        // Thu NSNN theo địa bàn
        $Dbt = $this->estimateDb($whereCondition, $filter, 1);

        // Thu NSNN theo cấp ngân sách
        $Cnst = $this->estimateCns($whereCondition, 1);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['Cct'] = $Cct;
        $json['data']['Knst'] = $Knst;
        $json['data']['Lvt'] = $Lvt;
        $json['data']['Dbt'] = $Dbt;
        $json['data']['Cnst'] = $Cnst;
        $json['data']['Tht'] = $Tht;
        return json_encode($json);

    }

    public function estimateExpense(Request $request){
        $filter = $request->post('filter');
        $whereCondition = $this->getWhereCondition1($filter);
        // Tổng hợp dư toán chi
        $Thc = $this->estimateExpneseThc($filter);

        // cơ cấu
        $Ccc = $this->estimateCc($whereCondition, 2, array("'0341'", "'0721'"));

        // kỳ ngân sách
        $Knsc = $this->estimateKns($filter, 2);

        // lĩnh vực
        $Lvc = $this->estimateExpenseLvc($whereCondition);

        // cấp ngân sách
        $Cnsc = $this->estimateCns($whereCondition, 2);

        // Địa bàn
        $Dbc = $this->estimateDb($whereCondition, $filter, 2);

        // Đơn vị
        $Dvc = $this->estimateExpenseDvc($filter);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['Thc'] = $Thc;
        $json['data']['Ccc'] = $Ccc;
        $json['data']['Knsc'] = $Knsc;
        $json['data']['Lvc'] = $Lvc;
        $json['data']['Cnsc'] = $Cnsc;
        $json['data']['Dbc'] = $Dbc;
        $json['data']['Dvc'] = $Dvc;
        return json_encode($json);
    }




    // ========================================= Lọc điều kiện ========================================================
    public function getWhereCondition1($filter){
        $whereCondition = '';
        if(!$filter['PeriodValue']){
            if($filter['PeriodID'] === 1){
                $whereCondition = " AND YEAR(A.PostDate) = 2020";
            }
            if($filter['PeriodID'] === 2 || $filter['PeriodID'] === 3 || $filter['PeriodID'] === 4 ){
                $whereCondition = " AND YEAR(A.PostDate) = 2021";
            }
            if($filter['PeriodID'] === 6){
                $whereCondition = " AND MONTH(A.PostDate) <= 6 AND YEAR(A.PostDate) = 2021";
            }
            if($filter['PeriodID'] === 7){
                $whereCondition = " AND MONTH(A.PostDate) <= 9 AND YEAR(A.PostDate) = 2021";
            }
            if($filter['PeriodID'] === 8 || $filter['PeriodID'] === 9 || $filter['PeriodID'] === 10){
                if($filter['PeriodID'] === 8) $toYear = 2023;
                if($filter['PeriodID'] === 9) $toYear = 2025;
                if($filter['PeriodID'] === 10) $toYear = 2030;
                $whereCondition = " AND YEAR(A.PostDate) >= 2021 AND YEAR(A.PostDate) <= $toYear";
            }
        }
        // =====================================================================
        if($filter['PeriodValue']){
            $val = $filter['PeriodValue'];
            if($filter['PeriodID'] === 1){
                $whereCondition = " AND YEAR(A.PostDate) = $val";
            }
            if($filter['PeriodID'] === 2){
                $whereCondition = " AND MONTH(A.PostDate) >= (($val -1 )* 3+ 1) AND MONTH(A.PostDate) <= ($val* 3) AND YEAR(A.PostDate) = 2021 ";
            }
            if($filter['PeriodID'] === 3){
                $whereCondition = " AND MONTH(A.PostDate) = $val AND YEAR(A.PostDate) = 2021";
            }
            if($filter['PeriodID'] === 4){
                $whereCondition = " AND WEEKOFYEAR(A.PostDate) = $val AND YEAR(A.PostDate) = 2021";
            }
            if($filter['PeriodID'] === 6){
                if($filter['PeriodValue'] === 1){
                    $whereCondition = " AND MONTH(A.PostDate) <= 6 AND YEAR(A.PostDate) = 2021";
                } else{
                    $whereCondition = " AND MONTH(A.PostDate) >= 6 AND YEAR(A.PostDate) = 2021";
                }
            }
            if($filter['PeriodID'] === 7){
                if($val === 1) $year7 = 2020;
                if($val === 2) $year7 = 2021;
                if($val === 3) $year7 = 2022;
                $whereCondition = " AND MONTH(A.PostDate) <= 9 AND YEAR(A.PostDate) = $year7";
            }
            if($filter['PeriodID'] === 8){
                $whereCondition = " AND YEAR(A.PostDate) >= ( $val*3 + 2015) AND YEAR(A.PostDate) <= ($val*3 + 2017)";
            }
            if($filter['PeriodID'] === 9){
                $whereCondition = " AND YEAR(A.PostDate) >= ( $val*5 + 2011) AND YEAR(A.PostDate) <= ($val*5 + 2015)";
            }
            if($filter['PeriodID'] === 10){
                $whereCondition = " AND YEAR(A.PostDate) >= ( $val*10 + 2001) AND YEAR(A.PostDate) <= ($val*10 + 2010)";
            }
            $province = $filter['ProvinceID'];
            $district = $filter['DistrictID'];
            $commune = $filter['CommuneID'];
            $whereCondition .= " AND C.ProvinceID = $province";
            if($district){
                $whereCondition .= " AND C.DistrictID = $district";
            }
            if($commune){
                $whereCondition .= " AND C.CommuneID = $commune";
            }
        }

        return $whereCondition;
    }

    // ======================================== Tổng hợp dự toán thu NSNN =============================================

    public function estimateRevenueTht($filter){
        $province = $filter['ProvinceID'];
        $district = $filter['DistrictID'];
        $commune = $filter['CommuneID'];
        if($filter['PeriodID'] === 1 ){
            $year = $filter['PeriodValue'];
            $preYear = $year - 1;
        } else {
            $year = 2021;
            $preYear = $year -1;
        }
        $whereCondition = " AND C.ProvinceID = $province";
        if($district){
            $whereCondition .= " AND C.DistrictID = $district";
        }
        if($commune){
            $whereCondition .= " AND C.CommuneID = $commune";
        }
        $sqlSelect = "SELECT B.STT,
                            MAX(CASE WHEN B.AccountNo = '0341' AND B.Year = $preYear THEN  I END) as DT1,
                            MAX(CASE WHEN B.AccountNo = '0341' AND B.Year = $year THEN  I END) as DT2,
                            MAX(CASE WHEN B.AccountNo = '0711' AND B.Year = $year THEN  I END) as UTT
                        FROM (SELECT  SUM(FCDebitAmount) AS I, AccountNo, YEAR(A.PostDate) AS Year,
                                    (CASE WHEN  RC.CateValue = 1 THEN 1
                                         WHEN  RC.CateValue = 2 THEN  2
                                         WHEN  RC.CateValue IN (3, 4, 5) THEN  3 END
                                    ) AS STT
                                FROM  act_gl_books AS A
                                    INNER  JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                    INNER  JOIN company as C ON C.CompanyID = A.InputCompanyID
                                    INNER  JOIN company_cate CC ON CC.CompanyID = A.InputCompanyID
                                WHERE  RC.CateNo = '203' AND  RC.CateValue IN (1,2,3,4,5)
                                    AND CC.CateNo = '1900'
                                    AND A.AccountNo IN ('0341', '0711')
                                    AND YEAR(A.PostDate) IN ($year, $preYear)
                                    $whereCondition
                                    GROUP BY AccountNo, STT, Year) AS B GROUP BY  STT";
        $Tht = DB::select(DB::raw($sqlSelect));
        return $Tht;

    }

    //==========================================  Dự toán NSNN theo cơ cấu  ============================================
    public function estimateCc($whereCondition, $estimateType, $estimateAccount){
        if($estimateType == 1){
            $estimateTable = 'revenue_cate';
            $estimateTableList = 'revenue_cate_list';
            $estimateColumn = "RevenueID";
        } else {
            $estimateTable = "expense_cate";
            $estimateTableList = 'expense_cate_list';
            $estimateColumn = "ExpenseID";
        } // $estimateType ? 1 thu, 2 chi
        $sqlSelectCtt = "select G.CateGroup, R.CateName, G.DT, G.UTT
                        from $estimateTableList as R
                        inner join (SELECT CateGroup,
                                           MAX(CASE WHEN B.AccountNo = $estimateAccount[0]  THEN B.SumFCDebitAmount END) AS DT,
                                           MAX(CASE WHEN B.AccountNo = $estimateAccount[1]  THEN B.SumFCDebitAmount END) AS UTT
                                    From(SELECT A.AccountNo, RCL.CateName, RCL.CateNo,
                                                SUM(A.FCDebitAmount) as SumFCDebitAmount,
                                                LEFT(RC.CateNo,5) as CateGroup
                                         FROM act_gl_books AS A
                                                  INNER JOIN  $estimateTable AS RC on A.$estimateColumn = RC.$estimateColumn
                                                  INNER JOIN  $estimateTableList AS RCL ON RC.CateNo = RCL.CateNo
                                                  INNER JOIN company AS C ON C.CompanyID = A.InputCompanyID
                                         WHERE  A.AccountNo IN ($estimateAccount[0], $estimateAccount[1]) AND  LEFT(RC.CateNo, 3) = '203'
                                           $whereCondition
                                         GROUP BY A.AccountNo, CateGroup) as B GROUP BY  CateGroup
                            ) as G on G.CateGroup = R.CateNo";
        $Cct = DB::select(DB::raw($sqlSelectCtt));
        return $Cct;
    }

    // ===================================== Dự toán NSNN theo lĩnh vực ================================================
    public function estimateRevenuetLvt( $whereCondition){
        $Lvt = [];
        // Dòng 1 dòng 2
        $sqlSelect = "SELECT STT,
                    MAX(CASE WHEN B.AccountNo = '0341' then I END ) AS DT,
                    MAX(CASE WHEN B.AccountNo = '0711' then I END ) AS UTT
                FROM  (SELECT  SUM(A.FCDebitAmount) as I, SSC.CateValue as STT, AccountNo
                        FROM act_gl_books as A
                            INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                            INNER JOIN sbr_sector_cate AS SSC ON SSC.SbrSectorID = A.SbrSectorID
                            INNER JOIN company AS C ON C.CompanyID = A.InputCompanyID
                        WHERE A.AccountNo IN ('0341', '0711')
                            AND ( RC.CateNo = '2031101' AND RC.CateValue IN (5,2,6,4))
                            AND SSC.CateNo = 20001 AND SSC.CateValue IN (1,2)
                            $whereCondition
                        GROUP BY STT, A.AccountNo
                    ) AS B GROUP BY  STT";
        $arrInsert = DB::select(DB::raw($sqlSelect));
        if($arrInsert){
            foreach ($arrInsert as $arr){
                $Lvt[] = $arr;
            }
        }


        // Dòng 3
        $sqlSelect = "SELECT 3 AS STT,
                           MAX(CASE WHEN B.AccountNo = '0341' then I END ) AS DT,
                           MAX(CASE WHEN B.AccountNo = '0711' then I END ) AS UTT
                    FROM  (SELECT  SUM(A.FCDebitAmount) as I, SSC.CateValue as STT, AccountNo
                           FROM act_gl_books as A
                                    INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                    INNER JOIN sbr_sector_cate AS SSC ON SSC.SbrSectorID = A.SbrSectorID
                                     INNER JOIN company AS C ON C.CompanyID = A.InputCompanyID
                           WHERE A.AccountNo IN ('0341', '0711')
                             AND ( (RC.CateNo = '2031101' AND RC.CateValue IN (1, 2, 4, 5, 6, 7) ) OR ( RC.CateNo = '20311990103' AND  RC.CateValue = 1))
                             AND SSC.CateNo = 20001 AND SSC.CateValue = 3
                             $whereCondition
                           GROUP BY STT, A.AccountNo
                          ) AS B";
        $arrInsert = DB::select(DB::raw($sqlSelect));
        if($arrInsert){
            foreach ($arrInsert as $arr){
                $Lvt[] = $arr;
            }
        }


        // Dòng 4
        $sqlSelect = "SELECT 4 AS STT,
                            MAX(CASE WHEN B.AccountNo = '0341' then I END ) AS DT,
                            MAX(CASE WHEN B.AccountNo = '0711' then I END ) AS UTT
                        FROM  (SELECT  SUM(A.FCDebitAmount) as I, SSC.CateValue as STT, AccountNo
                                FROM act_gl_books as A
                                    INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                    INNER JOIN sbr_sector_cate AS SSC ON SSC.SbrSectorID = A.SbrSectorID
                                     INNER JOIN company AS C ON C.CompanyID = A.InputCompanyID
                                WHERE A.AccountNo IN ('0341', '0711')
                                    AND  (RC.CateNo = '2031101' AND RC.CateValue IN ( 2, 4, 5, 6, 7) )
                                    AND SSC.CateNo = 20001 AND SSC.CateValue = 3
                                    $whereCondition
                                GROUP BY  A.AccountNo
                            ) AS B ";
        $arrInsert = DB::select(DB::raw($sqlSelect));
        if($arrInsert){
            foreach ($arrInsert as $arr){
                $Lvt[] = $arr;
            }
        }

        // Dòng 5
        $sqlSelect = "SELECT 5 AS STT,
                               MAX(CASE WHEN B.AccountNo = '0341' then I END ) AS DT,
                               MAX(CASE WHEN B.AccountNo = '0711' then I END ) AS UTT
                        FROM( SELECT  SUM(FCDebitAmount) AS I, AccountNo
                                FROM act_gl_books AS A
                                  INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                  INNER JOIN company AS C ON C.CompanyID = A.InputCompanyID
                                WHERE RC.CateNo = '2031101' AND  RC.CateValue IN (1 , 8)
                                     AND A.AccountNo IN ('0341', '0711')
                                $whereCondition
                                GROUP BY AccountNo
                            ) AS B";
        $arrInsert = DB::select(DB::raw($sqlSelect));
        if($arrInsert){
            foreach ($arrInsert as $arr){
                $Lvt[] = $arr;
            }
        }

        // Dòng 6
        $sqlSelect = "SELECT 6 AS STT,
                               MAX(CASE WHEN B.AccountNo = '0341' then I END ) AS DT,
                               MAX(CASE WHEN B.AccountNo = '0711' then I END ) AS UTT
                        FROM( SELECT  SUM(FCDebitAmount) AS I, AccountNo
                                FROM act_gl_books AS A
                                  INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                  INNER JOIN company AS C ON C.CompanyID = A.InputCompanyID
                                WHERE RC.CateNo = '203119907'
                                         AND A.AccountNo IN ('0341', '0711')
                                $whereCondition
                                GROUP BY AccountNo
                            ) AS B";
        $arrInsert = DB::select(DB::raw($sqlSelect));
        if($arrInsert){
            foreach ($arrInsert as $arr){
                $Lvt[] = $arr;
            }
        }

        // Dòng 7
        $sqlSelect = "SELECT 7 AS STT,
                           MAX(CASE WHEN B.AccountNo = '0341' then I END ) AS DT,
                           MAX(CASE WHEN B.AccountNo = '0711' then I END ) AS UTT
                    FROM( SELECT  SUM(FCDebitAmount) AS I, AccountNo
                            FROM act_gl_books AS A
                              INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                              INNER JOIN company AS C ON C.CompanyID = A.InputCompanyID
                            WHERE ( (RC.CateNo IN ('20311990101', ' 20311990102', ' 20311990103', '203119905', '203119911') AND  RC.CateValue = 1)
                                OR (RC.CateNo = '203119902' AND  Rc.CateValue IN (1 , 2)) )
                                 AND A.AccountNo IN ('0341', '0711')
                                $whereCondition
                            GROUP BY AccountNo
                        ) AS B";
        $arrInsert = DB::select(DB::raw($sqlSelect));
        if($arrInsert){
            foreach ($arrInsert as $arr){
                $Lvt[] = $arr;
            }
        }

        // Dòng 8
        $sqlSelect = "SELECT 11 AS STT,
                               MAX(CASE WHEN B.AccountNo = '0341' then I END ) AS DT,
                               MAX(CASE WHEN B.AccountNo = '0711' then I END ) AS UTT
                        FROM( SELECT  SUM(FCDebitAmount)  AS I, AccountNo
                                FROM act_gl_books AS A
                                  INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                  INNER JOIN company AS C ON C.CompanyID = A.InputCompanyID
                                WHERE ( (RC.CateNo IN ('203110201', '203110202', '203119908'))
                                        OR (RC.CateNo = '20311990901' AND RC.CateValue IN (1,2))
                                        OR (RC.CateNo = '203110203' AND  RC.CateValue = 1))
                                     AND A.AccountNo IN ('0341', '0711')
                                    $whereCondition
                                GROUP BY AccountNo
                            ) AS B";
        $arrInsert = DB::select(DB::raw($sqlSelect));
        if($arrInsert){
            foreach ($arrInsert as $arr){
                $Lvt[] = $arr;
            }
        }

        // Dòng 9, 10
        $sqlSelect = "SELECT  STT,
                               MAX(CASE WHEN B.AccountNo = '0341' then I END ) AS DT,
                               MAX(CASE WHEN B.AccountNo = '0711' then I END ) AS UTT
                        FROM( SELECT  SUM(FCDebitAmount)  AS I, AccountNo,
                                     (CASE WHEN RC.CateNo = '20312' THEN  9
                                            ELSE 8 END
                                         ) as STT
                                FROM act_gl_books AS A
                                  INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                  INNER JOIN company AS C ON C.CompanyID = A.InputCompanyID
                                WHERE RC.CateNo IN ('20312', '2031103') AND RC.CateValue IN (1, 2)
                                 AND A.AccountNo IN ('0341', '0711')
                                $whereCondition
                                GROUP BY AccountNo, STT
                            ) AS B GROUP BY  STT
                        ";
        $arrInsert = DB::select(DB::raw($sqlSelect));
        if($arrInsert){
            foreach ($arrInsert as $arr){
                $Lvt[] = $arr;
            }
        }
        // Dòng 10
        $sqlSelect = "SELECT  10 as STT,
                           MAX(CASE WHEN B.AccountNo = '0341' then I END ) AS DT,
                           MAX(CASE WHEN B.AccountNo = '0711' then I END ) AS UTT
                    FROM( SELECT  SUM(FCDebitAmount)  AS I, AccountNo
                            FROM act_gl_books AS A
                              INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                              INNER JOIN company AS C ON C.CompanyID = A.InputCompanyID
                            WHERE RC.CateNo = '20313' AND RC.CateValue IN (1, 2, 3, 4, 5)
                                AND A.AccountNo IN ('0341', '0711')
                                $whereCondition
                            GROUP BY AccountNo
                        ) AS B ";
        $arrInsert = DB::select(DB::raw($sqlSelect));
        if($arrInsert){
            foreach ($arrInsert as $arr){
                $Lvt[] = $arr;
            }
        };
        return $Lvt;
    }

    //======================================= Dự toán thu NSNN theo kỳ ngân sách =======================================
    public function estimateKns($filter, $estimateType){
        if(!$filter['PeriodValue']){
            $whereCondition = "AND YEAR(A.PostDate) = 2021";
            if($filter['PeriodID'] === 1){
                $whereCondition = "AND YEAR(A.PostDate) = 2020";
                $selectCondition = " MONTH(A.PostDate) as STT";
            }
            if($filter['PeriodID'] === 2){
                $selectCondition = " (CASE
                 WHEN MONTH(A.PostDate) >= 1 AND MONTH(A.PostDate) <= 3 THEN 1
                 WHEN MONTH(A.PostDate) >= 4 AND MONTH(A.PostDate) <= 6 THEN 2
                 WHEN MONTH(A.PostDate) >= 7 AND MONTH(A.PostDate) <= 9 THEN  3
                 WHEN MONTH(A.PostDate) >= 10 AND MONTH(A.PostDate) <=12 THEN 4
                 END
                 ) AS STT";
            }
            if($filter['PeriodID'] === 3){
                $selectCondition = " MONTH(A.PostDate) as STT";
            }
            if($filter['PeriodID'] === 4){
                $selectCondition = " WEEKOFYEAR(A.PostDate) as STT";
            }
            if($filter['PeriodID'] === 6){
                $selectCondition = "MONTH(A.PostDate) as STT";
                $whereCondition = " AND MONTH(A.PostDate) <= 6 AND YEAR(A.PostDate) = 2021";
            }
            if($filter['PeriodID'] === 7){
                $selectCondition = "MONTH(A.PostDate) as STT";
                $whereCondition = " AND MONTH(A.PostDate) <= 9 AND YEAR(A.PostDate) = 2021";
            }
            if($filter['PeriodID'] === 8 || $filter['PeriodID'] === 9 || $filter['PeriodID'] === 10){
                if($filter['PeriodID'] === 8) $toYear = 2023;
                if($filter['PeriodID'] === 9) $toYear = 2025;
                if($filter['PeriodID'] === 10) $toYear = 2030;
                $selectCondition = "(YEAR(A.PostDate)- 2020) as STT";
                $whereCondition = "AND YEAR(A.PostDate) >= 2021 AND YEAR(A.PostDate) <= $toYear";
            }
        }
        // =====================================================================
        if($filter['PeriodValue']){
            $val = $filter['PeriodValue'];
            if($filter['PeriodID'] === 1){
                $whereCondition = "AND YEAR(A.PostDate) = $val";
                $selectCondition = " MONTH(A.PostDate) as STT";
            }
            if($filter['PeriodID'] === 2){
                $whereCondition = " AND MONTH(A.PostDate) >= (($val -1 )* 3+ 1) AND MONTH(A.PostDate) <= ($val* 3) AND YEAR(A.PostDate) = 2021 ";
                $selectCondition = "  (CASE  WHEN  MONTH(A.PostDate) IN (1, 4, 7, 10) THEN 1
                                        WHEN  MONTH(A.PostDate) IN (2, 5, 8, 11) THEN 2
                                        WHEN  MONTH(A.PostDate) IN (3, 6, 9, 12) THEN 3
                                     END
                                     ) as STT";
            }
            if($filter['PeriodID'] === 3){
                $whereCondition = " AND MONTH(A.PostDate) = $val AND YEAR(A.PostDate) = 2021";
                $selectCondition = "DAY(A.PostDate) as STT";
            }
            if($filter['PeriodID'] === 4){
                $selectCondition = "DAYOFWEEK(A.PostDate) as STT";
                $whereCondition = " AND WEEKOFYEAR(A.PostDate) = $val AND YEAR(A.PostDate) = 2021";
            }
            if($filter['PeriodID'] === 6){
                if($filter['PeriodValue'] === 1){
                    $selectCondition = "MONTH(A.PostDate) as STT";
                    $whereCondition = " AND MONTH(A.PostDate) <= 6 AND YEAR(A.PostDate) = 2021";
                } else{
                    $selectCondition = "(MONTH(A.PostDate)-6) as STT";
                    $whereCondition = "AND MONTH(A.PostDate) >= 6 AND YEAR(A.PostDate) = 2021";
                }
            }
            if($filter['PeriodID'] === 7){
                if($val === 1) $year7 = 2020;
                if($val === 2) $year7 = 2021;
                if($val === 3) $year7 = 2022;
                $selectCondition = "MONTH(A.PostDate) as STT";
                $whereCondition = " AND MONTH(A.PostDate) <= 9 AND YEAR(A.PostDate) = $year7";
            }
            if($filter['PeriodID'] === 8){
                $selectCondition = "(YEAR(A.PostDate)-2014-$val*3 ) as STT";
                $whereCondition = "AND YEAR(A.PostDate) >= ( $val*3 + 2015) AND YEAR(A.PostDate) <= ($val*3 + 2017)";
            }
            if($filter['PeriodID'] === 9){
                $selectCondition = "(YEAR(A.PostDate)-2010-$val*5 ) as STT";
                $whereCondition = "AND YEAR(A.PostDate) >= ( $val*5 + 2011) AND YEAR(A.PostDate) <= ($val*5 + 2015)";
            }
            if($filter['PeriodID'] === 10){
                $selectCondition = "(YEAR(A.PostDate)-2000-$val*10 ) as STT";
                $whereCondition = "AND YEAR(A.PostDate) >= ( $val*10 + 2001) AND YEAR(A.PostDate) <= ($val*10 + 2010)";
            }
        }
        if($estimateType === 1){
            // 1 : dự toán thu
            $sqlSelectKnst = "SELECT  B.STT,
                                   MAX(CASE  WHEN B.AccountNo = '0341' THEN B.Sum END ) as DT,
                                    MAX(CASE  WHEN B.AccountNo = '0711' THEN B.Sum END ) as UTT
                            FROM (SELECT  SUM(A.FCDebitAmount) as Sum, AccountNo, $selectCondition
                                        FROM   act_gl_books as A
                                        INNER  JOIN revenue_cate  as RC ON RC.RevenueID = A.RevenueID
                                        INNER JOIN company AS C ON C.CompanyID = A.InputCompanyID
                                  WHERE LEFT(RC.CateNo, 3) = '203'
                                    AND A.AccountNo IN ('0341', '0711')
                                    $whereCondition
                                  GROUP BY  A.AccountNo, STT) AS B GROUP BY STT ORDER BY B.STT";
            $Kns = DB::select(DB::raw($sqlSelectKnst));
        } else {
            // 2 : dự toán chi

            // Chi
            $sqlSelectKnsc = "SELECT  B.STT,
                                   MAX(CASE  WHEN B.AccountNo = '0341' THEN B.Sum END ) as DT,
                                    MAX(CASE  WHEN B.AccountNo = '0711' THEN B.Sum END ) as UTT
                            FROM (SELECT  SUM(A.FCDebitAmount) as Sum, AccountNo, $selectCondition
                                        FROM   act_gl_books as A
                                        INNER  JOIN expense_cate  as RC ON RC.ExpenseID = A.ExpenseID
                                        INNER JOIN company AS C ON C.CompanyID = A.InputCompanyID
                                  WHERE LEFT(RC.CateNo, 3) = '203'
                                    AND A.AccountNo IN ('0341', '0711')
                                    $whereCondition
                                  GROUP BY  A.AccountNo, STT) AS B GROUP BY STT ORDER BY B.STT";
            $Knsc = DB::select(DB::raw($sqlSelectKnsc));

            // Thu
            $sqlSelectKnst = "SELECT  SUM(A.FCDebitAmount) as DTT, $selectCondition
                                FROM   act_gl_books as A
                                           INNER  JOIN expense_cate  as RC ON RC.ExpenseID = A.ExpenseID
                                           INNER JOIN company AS C ON C.CompanyID = A.InputCompanyID
                                WHERE LEFT(RC.CateNo, 3) = '203'
                                  $whereCondition
                                GROUP BY  A.AccountNo, STT";
            $Knst = DB::select(DB::raw($sqlSelectKnst));
            $Kns = [
                'Knsc' => $Knsc,
                'Knst' => $Knst
            ];
        }
        return $Kns;
    }

    // ======================================= Dự toán thu NSNN theo địa bàn ===========================================
    public function estimateDb($whereCondition, $filter, $estimateType){
        $province = $filter['ProvinceID'];
        $district = $filter['DistrictID'];
        $commune = $filter['CommuneID'];
        if($estimateType == 1){
            $table = 'revenue_cate';
            $columnID = 'RevenueID';
            $Account = ["'0341'", "'0711'"];
        } else {
            $table = 'expense_cate';
            $columnID = 'ExpenseID';
            $Account = ["'0441'", "'0721'"];
        }
        if(!$district){
            $adress = DB::table('district')->where('ProvinceID', $province)->select('DistrictID as ID', 'DistrictNo as No', 'DistrictName as Name')->get()->toArray();
            $sqlSelect = "SELECT DistrictID as ID,
                MAX(CASE  WHEN B.AccountNo = $Account[0] THEN I END ) as DT,
                MAX(CASE  WHEN B.AccountNo = $Account[1] THEN I END ) as UTT
            FROM(SELECT SUM(FCDebitAmount) as I , AccountNo,
                        C.DistrictID
                 FROM act_gl_books AS A
                          INNER JOIN company_cate AS CC ON A.InputCompanyID = CC.CompanyID
                          INNER JOIN company AS C ON A.InputCompanyID = C.CompanyID
                          INNER JOIN $table AS RC ON A.$columnID = RC.$columnID
                 WHERE CC.CateNo = '1200'
                   AND C.ManagementLevel >= 3
                   AND A.AccountNo IN ($Account[0], $Account[1])
                   AND LEFT(Rc.CateNo, 3 ) = '203'
                    $whereCondition
                 GROUP BY A.AccountNo, C.DistrictID) AS B GROUP BY ID ORDER BY  ID";
        } else {
            $adress = DB::table('commune')->where('ProvinceID', '=', $province)
                                                ->where('DistrictID', '=', $district)->select('CommuneID as ID', 'CommuneNo as No', 'CommuneName as Name' )->get()->toArray();
            $sqlSelect = "SELECT CommuneID AS ID ,
                                MAX(CASE  WHEN B.AccountNo = $Account[0] THEN I END ) as DT,
                                MAX(CASE  WHEN B.AccountNo = $Account[1] THEN I END ) as UTT
                            FROM(SELECT SUM(FCDebitAmount) as I , AccountNo,
                                        C.CommuneID
                                 FROM act_gl_books AS A
                                          INNER JOIN company_cate AS CC ON A.InputCompanyID = CC.CompanyID
                                          INNER JOIN company AS C ON A.InputCompanyID = C.CompanyID
                                          INNER JOIN $table AS RC ON A.$columnID = RC.$columnID
                                 WHERE CC.CateNo = '1200'
                                   AND A.AccountNo IN ($Account[0], $Account[1])
                                   AND AND C.ManagementLevel >= 4
                                   AND LEFT(Rc.CateNo, 3 ) = '203'
                                    $whereCondition
                                 GROUP BY A.AccountNo, C.CommuneID) AS B GROUP BY ID ORDER BY ID";
        }
        ///
        if($estimateType === 1){
            $Dbt['data'] = DB::select(DB::raw($sqlSelect));
            $Dbt['Adress'] = $adress;
        }
        if($estimateType === 2){
            if(!$district){
                $sqlSelectDTT = "
                    SELECT  SUM(FCDebitAmount) AS DTT, C.DistrictID AS ID
                    FROM act_gl_books as A
                             INNER JOIN company_cate AS CC ON A.InputCompanyID = CC.CompanyID
                             INNER JOIN company AS C ON A.InputCompanyID = C.CompanyID
                             INNER JOIN revenue_cate AS RC ON A.RevenueID = RC.RevenueID
                    WHERE CC.CateNo = '1200'
                          AND A.AccountNo = '0341'
                          AND C.ManagementLevel >= 3
                          AND LEFT(RC.CateNo, 3) ='203'
                           $whereCondition
                    GROUP BY ID ORDER BY ID
                ";
            } else {
                $sqlSelectDTT = "
                    SELECT  SUM(FCDebitAmount) AS DTT, C.CommuneID AS ID
                    FROM act_gl_books as A
                             INNER JOIN company_cate AS CC ON A.InputCompanyID = CC.CompanyID
                             INNER JOIN company AS C ON A.InputCompanyID = C.CompanyID
                             INNER JOIN revenue_cate AS RC ON A.RevenueID = RC.RevenueID
                     WHERE CC.CateNo = '1200'
                          AND A.AccountNo = '0341'
                          AND C.ManagementLevel >= 4
                          AND LEFT(RC.CateNo, 3) ='203'
                          $whereCondition
                   GROUP BY ID ORDER BY ID
                ";
            }
            $Dbt['data'] = DB::select(DB::raw($sqlSelect));
            $Dbt['Dbt'] = DB::select(DB::raw($sqlSelectDTT));
            $Dbt['Adress'] = $adress;
        }
        return $Dbt;
    }

    // ====================================== Dự toán thu NSNN theo ngân sách ==========================================
    public function estimateCns($whereCondition, $esimateType){
        if($esimateType === 1){
            $table = 'revenue_cate';
            $columnID = 'RevenueID';
            $AccountNo = '0341';
        } else {
            $table = 'expense_cate';
            $columnID = 'ExpenseID';
            $AccountNo = "'0441'";
        }
        $sqlSelect = "SELECT SUM(FCDebitAmount) as data,
                               ( CASE WHEN A.BudgetLevel = 1 THEN 1
                                    WHEN  A.BudgetLevel = 2 THEN 2
                                    WHEN A.BudgetLevel IN (3 ,4) THEN 3  END
                                ) AS Stt
                    FROM act_gl_books AS A
                        INNER JOIN $table AS RC ON RC.$columnID = A.$columnID
                        INNER JOIN company AS C ON C.CompanyID = A.InputCompanyID
                    WHERE LEFT(RC.CateNo, 3) = '203'
                      AND A.AccountNo = $AccountNo
                      AND A.BudgetLevel IN (1, 2, 3, 4)
                      $whereCondition ";
        $Cns = DB::select(DB::raw($sqlSelect));
        return $Cns;
    }

    // =================================== Tổng hợp chi dự toán NSNN ===================================================
    public function estimateExpneseThc($filter){
        $province = $filter['ProvinceID'];
        $district = $filter['DistrictID'];
        $commune = $filter['CommuneID'];
        if($filter['PeriodID'] === 1 ){
            $year = $filter['PeriodValue'];
            $preYear = $year - 1;
        } else {
            $year = 2021;
            $preYear = $year -1;
        }
        $whereCondition = " AND C.ProvinceID = $province";
        if($district){
            $whereCondition .= " AND C.DistrictID = $district";
        }
        if($commune){
            $whereCondition .= " AND C.CommuneID = $commune";
        }
        $sqlSelect = "SELECT B.STT,
                           MAX(CASE WHEN B.AccountNo = '0341' AND B.Year = $preYear THEN  I END) as DT1,
                           MAX(CASE WHEN B.AccountNo = '0341' AND B.Year = $year THEN  I END) as DT2,
                           MAX(CASE WHEN B.AccountNo = '0711' AND B.Year = $year THEN  I END) as UTT
                    FROM (SELECT SUM(FCDebitAmount) AS I,YEAR(A.PostDate) as Year,  A.AccountNo,
                                 (CASE WHEN LEFT(EC.CateNo, 5) = '20351' THEN  1
                                       WHEN LEFT(EC.CateNo, 5) = '20352' THEN  2
                                       ELSE  3 END
                                     ) AS STT
                          FROM act_gl_books as A
                                   INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                   INNER JOIN company AS C ON C.CompanyID = A.InputCompanyName
                          WHERE LEFT(EC.CateNo, 3) = '203'
                            AND A.AccountNo IN ('0441', '0721')
                             AND YEAR(A.PostDate) IN ($preYear, $year)
                          GROUP BY STT, Year, AccountNo) AS B GROUP BY  STT";
        $Thc = DB::select(DB::raw($sqlSelect));
        return $Thc;
    }
    // Dự toán chi NSNN theo lĩnh vực
    public function estimateExpenseLvc($whereCondition){
        $sqlSelect = "SELECT D.STT, C.CateName, D.DT, D.UTT
                FROM expense_cate_list as C
                INNER JOIN  (SELECT  B.STT, B.expenseCateNo,
                           MAX(CASE WHEN B.AccountNo = '0441' THEN I END ) AS DT,
                           MAX(CASE WHEN B.AccountNo = '0721' THEN I END ) AS UTT
                    FROM (SELECT SUM(FCDebitAmount) AS I, AccountNo,
                               SC.CateNo AS STT,
                                LEFT(EC.CateNo, 5) AS expenseCateNo
                            FROM  act_gl_books as A
                                    INNER JOIN expense_cate as EC ON EC.ExpenseID = A.ExpenseID
                                    INNER JOIN sector_cate as SC ON Sc.SectorID = A.SectorID
                                    INNER JOIN company as C ON C.CompanyID = A.InputCompanyID
                            WHERE A.AccountNo IN ('0441', '0721')
                                AND  LEFT(EC.CateNo, 3) = '203' AND LENGTH(Ec.CateNo) >= 5
                                AND  SC.CateNo = '20001'
                                $whereCondition
                            GROUP BY  STT,  expenseCateNo, AccountNo) AS B ORDER BY  STT, expenseCateNo, AccountNo) AS D
                ON C.CateNo = D.expenseCateNo";
        $lvc = DB::select(DB::raw($sqlSelect));
        return $lvc;
    }

    // Dự toán chi NSNN theo đơn vị
    public function estimateExpenseDvc($filter){
        $whereCondition = '';
        if(!$filter['PeriodValue']){
            if($filter['PeriodID'] === 1){
                $whereCondition = " AND YEAR(A.PostDate) = 2020";
            }
            if($filter['PeriodID'] === 2 || $filter['PeriodID'] === 3 || $filter['PeriodID'] === 4 ){
                $whereCondition = " AND YEAR(A.PostDate) = 2021";
            }
            if($filter['PeriodID'] === 6){
                $whereCondition = " AND MONTH(A.PostDate) <= 6 AND YEAR(A.PostDate) = 2021";
            }
            if($filter['PeriodID'] === 7){
                $whereCondition = " AND MONTH(A.PostDate) <= 9 AND YEAR(A.PostDate) = 2021";
            }
            if($filter['PeriodID'] === 8 || $filter['PeriodID'] === 9 || $filter['PeriodID'] === 10){
                if($filter['PeriodID'] === 8) $toYear = 2023;
                if($filter['PeriodID'] === 9) $toYear = 2025;
                if($filter['PeriodID'] === 10) $toYear = 2030;
                $whereCondition = " AND YEAR(A.PostDate) >= 2021 AND YEAR(A.PostDate) <= $toYear";
            }
        }
        // =====================================================================
        if($filter['PeriodValue']) {
            $val = $filter['PeriodValue'];
            if ($filter['PeriodID'] === 1) {
                $whereCondition = " AND YEAR(A.PostDate) = $val";
            }
            if ($filter['PeriodID'] === 2) {
                $whereCondition = " AND MONTH(A.PostDate) >= (($val -1 )* 3+ 1) AND MONTH(A.PostDate) <= ($val* 3) AND YEAR(A.PostDate) = 2021 ";
            }
            if ($filter['PeriodID'] === 3) {
                $whereCondition = " AND MONTH(A.PostDate) = $val AND YEAR(A.PostDate) = 2021";
            }
            if ($filter['PeriodID'] === 4) {
                $whereCondition = " AND WEEKOFYEAR(A.PostDate) = $val AND YEAR(A.PostDate) = 2021";
            }
            if ($filter['PeriodID'] === 6) {
                if ($filter['PeriodValue'] === 1) {
                    $whereCondition = " AND MONTH(A.PostDate) <= 6 AND YEAR(A.PostDate) = 2021";
                } else {
                    $whereCondition = " AND MONTH(A.PostDate) >= 6 AND YEAR(A.PostDate) = 2021";
                }
            }
            if ($filter['PeriodID'] === 7) {
                if ($val === 1) $year7 = 2020;
                if ($val === 2) $year7 = 2021;
                if ($val === 3) $year7 = 2022;
                $whereCondition = " AND MONTH(A.PostDate) <= 9 AND YEAR(A.PostDate) = $year7";
            }
            if ($filter['PeriodID'] === 8) {
                $whereCondition = " AND YEAR(A.PostDate) >= ( $val*3 + 2015) AND YEAR(A.PostDate) <= ($val*3 + 2017)";
            }
            if ($filter['PeriodID'] === 9) {
                $whereCondition = " AND YEAR(A.PostDate) >= ( $val*5 + 2011) AND YEAR(A.PostDate) <= ($val*5 + 2015)";
            }
            if ($filter['PeriodID'] === 10) {
                $whereCondition = " AND YEAR(A.PostDate) >= ( $val*10 + 2001) AND YEAR(A.PostDate) <= ($val*10 + 2010)";
            }
        }
        $province = $filter['ProvinceID'];
        $whereCondition .= " AND C.ProvinceID = $province";
        $company = DB::table('company as C')->join('company_cate as CC', 'C.CompanyID', '=', 'CC.CompanyID')
            ->where('CC.CateNo', '=', '1200')
            ->where('C.ProvinceID', '=', $province)
            ->select('C.CompanyID AS ID', 'C.CompanyName AS Name')->get()->toArray();
        $sqlDvt = "SELECT SUM(FCDebitAmount) as DTT, C.CompanyName as Name, C.CompanyID as ID
                    FROM act_gl_books as A
                        INNER JOIN revenue_cate as RC ON RC.RevenueID = A.RevenueID
                        INNER JOIN company as C ON C.CompanyID = A.InputCompanyID
                        INNER JOIN company_cate as CC ON CC.CompanyID = A.InputCompanyID
                        INNER JOIN sbr_sector_cate as SSC ON SSC.SbrSectorID = A.SbrSectorID
                    WHERE CC.CateNo = '1200'
                        AND C.ManagementLevel = 2
                        AND A.AccountNo = '0341'
                        AND RC.CateValue = '203'
                        $whereCondition
                    GROUP BY ID";
        $sqlDvc ="SELECT ID ,Name,
                           MAX(CASE  WHEN B.AccountNo = '0341' THEN  I END) as DT,
                           MAX(CASE  WHEN B.AccountNo = '0441' THEN  I END) as UTT
                    FROM (SELECT SUM(FCDebitAmount) as I, C.CompanyName as Name, C.CompanyID as ID, AccountNo
                    FROM act_gl_books as A
                        INNER JOIN expense_cate as EC ON EC.ExpenseID = A.ExpenseID
                        INNER JOIN company as C ON C.CompanyID = A.InputCompanyID
                        INNER JOIN company_cate as CC ON CC.CompanyID = A.InputCompanyID
                        INNER JOIN sector_cate as SC ON SC.SectorID = A.SectorID
                    WHERE CC.CateNo = '1200'
                        AND C.ManagementLevel = 2
                        AND A.AccountNo IN ('0441', '0721')
                        AND EC.CateValue = '203'
                        $whereCondition
                    GROUP BY ID) AS B GROUP BY ID ";
        $Dvc['company'] = $company;
        $Dvc['Dvt'] = DB::select(DB::raw($sqlDvt));
        $Dvc['Dvc'] = DB::select(DB::raw($sqlDvc));
        return $Dvc;
    }
}
