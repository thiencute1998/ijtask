<?php

namespace Module\SysAdmin\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Module\Common\Models\SysUser;

class Permission
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function handle($request, Closure $next)
    {
        $feature = Route::currentRouteName();
        $arrFeature = explode('.', $feature);
        $key = isset($arrFeature[0])?$arrFeature[0]:"";
        $action = isset($arrFeature[1])?$arrFeature[1]:"";
        $check = SysUser::checkPer($key, $action);
        if(!$check){
            Session::flash('message', 'Bạn không có quyền truy cập chức năng này!');
            return redirect()->to(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:env('HOME_URL'));
        }else{
            return $next($request);
        }
    }
}