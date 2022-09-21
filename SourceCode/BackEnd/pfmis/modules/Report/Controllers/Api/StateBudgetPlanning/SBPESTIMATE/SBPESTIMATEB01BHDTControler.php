<?php

namespace Module\Report\Controllers\StateBudgetPlanning\SBPESTIMATE;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;

class SBPESTIMATEB01BHDTController extends Controller
{
    public function SBPESTIMATEB01BHDT(Request $request)
    {
         return $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

    }








}
