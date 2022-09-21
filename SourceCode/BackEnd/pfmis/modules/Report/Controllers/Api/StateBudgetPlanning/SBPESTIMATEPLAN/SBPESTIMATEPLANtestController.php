<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBPESTIMATEPLAN;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Symfony\Component\ErrorHandler\Debug;

class SBPESTIMATEPLANtestController extends Controller
{
    public function SBPESTIMATEPLAN02(Request $request)
    {
        $arrInsert = [];
        $arrInsert = [];
        $arrInsert1 = [];
        $arrInsert2 = [];
        $arrInsert3 = [];
        $books1 = $this->insert1201_1215(32,11,'1214,1215');
        foreach ($books1 as $key => $item){
            $arrInsert[] = [
                'ItemID'=> 'A#02#01#' . sprintf("%02d",($key + 1)),
                'Level'=> 4,
                'ParentID'=> 'A#02#01',
                'ConAccountNoI1'=> '01712',
                'ConAccountNoI2'=> '01512',
                'ConAccountNoI3'=> '01112',
                'RevenueNo'=> $item->RevenueNo,
                'NormCateList'=> 32,
                'NormCateValue'=> '11',
                'ItemName'=> $item->RevenueName,
            ];
        }
        $books2 = $this->insert1201_1215(32,11,'1201,1213');
        foreach ($books2 as $key => $item){
            $arrInsert1[] =  [
                'ItemID'=>  'A#02#02#' . sprintf("%02d",($key + 1)),
                'Level'=> 4,
                'ParentID'=> 'A#02#02',
                'ItemName'=> $item->RevenueName,
                'CheckCountRevenue'=> $key
            ];

            $arrInsert2[] =  [
                'ItemID'=>  'A#02#03#' . sprintf("%02d",($key + 1)),
                'Level'=> 4,
                'ParentID'=> 'A#02#03',
                'ConAccountNoI1'=> '01723',
                'ConAccountNoI2'=> '01523',
                'ConAccountNoI3'=> '01123',
                'RevenueNo'=> $item->RevenueNo,
                'NormCateList'=> 32,
                'NormCateValue'=> '2',
                'ItemName'=> $item->RevenueName,
                'CheckCountRevenue'=> $key
            ];

            $arrInsert3[] =  [
                'ItemID'=>  'A#02#04#' . sprintf("%02d",($key + 1)),
                'Level'=> 4,
                'ParentID'=> 'A#02#04',
                'ConAccountNoI1'=> '01712',
                'ConAccountNoI2'=> '01512',
                'ConAccountNoI3'=> '01112',
                'RevenueNo'=> $item->RevenueNo,
                'NormCateList'=> 32,
                'NormCateValue'=> '3',
                'ItemName'=> $item->RevenueName,
                'CheckCountRevenue'=> $key
            ];
        }

        DB::table('table_tmp_linh')->insert($arrInsert);
        DB::table('table_tmp_linh')->insert($arrInsert1);
        DB::table('table_tmp_linh')->insert($arrInsert2);
        DB::table('table_tmp_linh')->insert($arrInsert3);
        $this->insertByRevenueType1();
        $this->test();

        return json_encode([
            'status' => 1,
            'data' => [],
            'msg' => ''
        ]);
    }

    public function insert1201_1215($catelist,$catevalue,$revenue){
        return DB::table('act_gl_books')
            ->where(function($query) use($catelist,$catevalue,$revenue){
                $RevenueArr = explode(',',$revenue);
                $distance = (int)$RevenueArr[1] - (int)$RevenueArr[0];
                for($i = 0; $i <= $distance; $i++){
                    $query->orWhere(function($q) use ($RevenueArr,$i){
                        $q->where(DB::raw('LEFT(RevenueNo,4)'),'=',(int)$RevenueArr[0] + $i)
                            ->where(DB::raw('LENGTH(RevenueNo)'),'=',6);
                    });
                }
            })->groupBy('RevenueNo')->get();
    }

    public function insertByRevenueType1(){
        $arrInsert = [];
        $books = DB::table('table_tmp_linh')->where('RevenueType','1')->where('RevenueNo','like','%,%')->get();
        foreach($books as $item){
            $arrRevenueNo = explode(',',$item->RevenueNo);
            foreach ($arrRevenueNo as $data){
                $arrInsert[] = [
                    'ParentID'=> $item->ItemID,
                    'Level'=> -1,
                    'RevenueNo'=> $data,
                    'ConAccountNoI1'=> $item->ConAccountNoI1,
                    'ConAccountNoI2'=> $item->ConAccountNoI2,
                    'ConAccountNoI3'=> $item->ConAccountNoI3,
                    'NormCateList'=> $item->NormCateList,
                    'NormCateValue'=> $item->NormCateValue,
                ];
            }
        }
        DB::table('table_tmp_linh')->insert($arrInsert);


    }

    public function test(){
        $sql = "UPDATE table_tmp_linh as TT, (SELECT A.ConAccountNo, A.RevenueNo, N.CateID, N.CateValue, SUM(LCDebitAmount) AS I1
        FROM act_gl_books AS A
				INNER JOIN norm_cate as N on A.NormID = N.NormID
				WHERE A.ConAccountNo != '' AND A.RevenueNo != ''
				AND N.CateID is not null AND N.CateValue is not null
				GROUP BY A.ConAccountNo, A.RevenueNo, N.CateID, N.CateValue
			)  as HH set TT.I1 = HH.I1 WHERE TT.ConAccountNoI1 = HH.ConAccountNo
			AND TT.RevenueNo = HH.RevenueNo
			AND TT.NormCateList = HH.CateID
			AND TT.NormCateValue = HH.CateValue
            AND (TT.RevenueType != 3 or TT.RevenueType is null)
			";

        DB::statement($sql);

        $sqlToTalDetail = "UPDATE table_tmp_linh as TT, (SELECT ParentID, SUM(I1) AS I1
        FROM table_tmp_linh
				WHERE ParentID != ''
				AND Level = -1
				GROUP BY ParentID
			)  as HH set TT.I1 = HH.I1 WHERE TT.ItemID = HH.ParentID AND TT.I1 is null";

        DB::statement($sqlToTalDetail);

        $sqlCheckCount = "UPDATE table_tmp_linh as TT, (SELECT CheckCountRevenue, SUM(I1) AS I1
        FROM table_tmp_linh
				WHERE ParentID != 'A#02#02'
				AND CheckCountRevenue is not null
				GROUP BY CheckCountRevenue
			)  as HH set TT.I1 = HH.I1 WHERE TT.ParentID = 'A#02#02' AND TT.CheckCountRevenue = HH.CheckCountRevenue";

        DB::statement($sqlCheckCount);


        // children revenue

        $sqlChildrenRevenue = "UPDATE table_tmp_linh as TT, (SELECT A.ConAccountNo, N.CateID, N.CateValue, LEFT(A.RevenueNo,4) as RevenueNo, SUM(LCDebitAmount) AS I1
        FROM act_gl_books AS A
				INNER JOIN norm_cate as N on A.NormID = N.NormID
				WHERE A.ConAccountNo != '' AND A.RevenueNo != ''
				AND N.CateID is not null AND N.CateValue is not null
				GROUP BY A.ConAccountNo, N.CateID, N.CateValue, LEFT(A.RevenueNo,4)
			)  as HH set TT.I1 = HH.I1 WHERE TT.ConAccountNoI1 = HH.ConAccountNo
			AND TT.RevenueNo = HH.RevenueNo
			AND TT.NormCateList = HH.CateID
			AND TT.NormCateValue = HH.CateValue
			AND TT.RevenueType = 3";

        DB::statement($sqlChildrenRevenue);

        $getMaxLevel = DB::table('table_tmp_linh')
            ->select(DB::raw('MAX(Level) as MaxLevel'))
            ->get();

        $maxLevel = $getMaxLevel[0]->MaxLevel;

        // parent
        for ($i = $maxLevel ; $i > 0 ; $i--){
            $sqlParent = "UPDATE table_tmp_linh as TT, (SELECT ParentID, SUM(I1) AS I1
                FROM table_tmp_linh
				WHERE ParentID != ''
				AND Level = $i
				GROUP BY ParentID
			)  as HH set TT.I1 = HH.I1 WHERE TT.ItemID = HH.ParentID AND TT.I1 is null";
            DB::statement($sqlParent);

        }
    }
}
