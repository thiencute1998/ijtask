<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Module\Report\Traits\SBP3422016TTBTCMB12;

class SBP3422016TTBTCMB12Controller extends Controller
{
    use SBP3422016TTBTCMB12;

    public function SBP3422016TTBTC_MB12_2(Request $request)
    {
        $this->SBP3422016TTBTC_MB12($request, 1);

    }
    public function SBP3422016TTBTC_MB12_3(Request $request)
    {
        $this->SBP3422016TTBTC_MB12($request, 2);

    }
    public function SBP3422016TTBTC_MB12_4(Request $request)
    {
        $this->SBP3422016TTBTC_MB12($request, 3);

    }
    public function SBP3422016TTBTC_MB12_5(Request $request)
    {
        $this->SBP3422016TTBTC_MB12($request, 4);

    }
}
