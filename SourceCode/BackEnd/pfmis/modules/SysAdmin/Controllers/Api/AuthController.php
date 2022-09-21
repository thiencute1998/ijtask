<?php
namespace Module\SysAdmin\Controllers;
use App\Company;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Module\Listing\Models\Employee;
use Module\SysAdmin\Models\SysAdminCompanySetting;
use Module\SysAdmin\Models\SysAdminGlobalSetting;
use Module\SysAdmin\Models\SysAdminUserSetting;
use Module\SysAdmin\Models\SysLeftMenu;
use Module\SysAdmin\Models\SysTopMenu;
use Module\SysAdmin\Models\SysUser;
use Module\SysAdmin\Models\SysUserFeaturePer;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $http = new \GuzzleHttp\Client;
        try {
            $response = $http->post(config('services.passport.login_endpoint'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                    'username' => $request->username,
                    'password' => $request->password,
                ]
            ]);
            $user = SysUser::where(['username' => $request->username])->first();
            if ($user->Inactive == 1) {
                $json['status'] = 2;
                $json['msg'] = 'Tài khoản của bạn đã bị khóa';
                $json['data'] = null;
                return json_encode($json);
            }

            $PerFeature = SysUserFeaturePer::where(['UserID' => $user->UserID])->get();
            $MenuLeft = SysLeftMenu::where(['Status' => 1])->orderBy('NOrder')->get();
            $MenuTop = SysTopMenu::where(['Status' => 1])->get();
            $employee = Employee::find($user->EmployeeID);
            $employee->Avata = $user->Avata;
            $PerFeatureArr = null;
            $MenuLeftArr = null;
            $MenuTopArr = null;
            if($PerFeature){
                foreach ($PerFeature as $item){
                    $PerFeatureArr[$item->FeatureID] = $item;
                }
            }

            if($MenuLeft){
                foreach ($MenuLeft as $item){
                    $item->Allow = 1;
                    if(isset($PerFeatureArr[$item->FeatureID])){
                        $item->Allow = $PerFeatureArr[$item->FeatureID]->Access;
                    }
                    $MenuLeftArr[] = $item;
                }
            }
            if($MenuTop){
                foreach ($MenuTop as $item){
                    $item->Allow = 1;
                    if(isset($PerFeatureArr[$item->FeatureID])){
                        $item->Allow = $PerFeatureArr[$item->FeatureID]->Access;
                    }
                    $MenuTopArr[] = $item;
                }
            }

            $SysUserSetting = SysAdminUserSetting::where(['UserID' => $user->UserID])->get()->toArray();
            $SysCompanySetting = SysAdminCompanySetting::where(['CompanyID' => $user->CompanyID])->get()->toArray();
            $SysGlobalSetting = SysAdminGlobalSetting::where(['AllowDisplay' => 1])->get()->toArray();
            $optionSetting = array_merge($SysUserSetting, $SysCompanySetting);
            $optionSetting = array_merge($optionSetting, $SysGlobalSetting);

            $tokenInfo = $response->getBody();
            $data = (array) json_decode($tokenInfo);
            $json['status'] = 1;
            $json['msg'] = 'Đăng nhập thành công!';
            $json['data']['MenuLeftArr'] = $MenuLeftArr;
            $json['data']['MenuTopArr'] = $MenuTopArr;
            $json['data']['Info'] = $user;
            $json['data']['Employee'] = $employee;
            $json['data']['OptionSetting'] = $optionSetting;
            $json['data']['data'] = $data;
            return json_encode($json);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if ($e->getCode() === 400) {
                $json['status'] = 400;
                $json['msg'] = 'Invalid Request. Please enter a username or a password.';
                $json['data'] = null;
                return json_encode($json);
            } else if ($e->getCode() === 401) {
                $json['status'] = 401;
                $json['msg'] = 'Invalid Request. Please enter a username or a password.';
                $json['data'] = null;
                return json_encode($json);
            }

            $json['status'] = $e->getCode();
            $json['msg'] = 'Something went wrong on the server.';
            $json['data'] = null;
            return json_encode($json);
        }
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }
    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return response()->json('Logged out successfully', 200);
    }
    public function config()
    {
        $company = Company::find(auth()->user()->company_id);
        $user = auth()->user();
        return ['user' => $user, 'company' => $company];
    }
}
