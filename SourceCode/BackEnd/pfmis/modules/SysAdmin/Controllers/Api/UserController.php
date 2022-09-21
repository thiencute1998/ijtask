<?php
namespace Module\SysAdmin\Controllers;
use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Module\SysAdmin\Models\SessionLogin;
use Illuminate\Support\Facades\Validator;
use Module\SysAdmin\Models\SysFeature;
use Module\SysAdmin\Models\SysGroupMember;
use Module\SysAdmin\Models\SysUser;
use Module\SysAdmin\Models\SysUserFeaturePer;
use Module\SysAdmin\Models\SysUserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use function GuzzleHttp\Psr7\_parse_request_uri;

class UserController extends Controller
{
    public function list(Request $request){
        $data = SysUser::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function create(){
        $data = DB::select(DB::raw('SELECT sys_feature.*, if(ParentID = 0, FeatureID, ParentID) as parent_id FROM sys_feature ORDER BY parent_id, ParentID'));
        $group = SysUserGroup::all();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['feature'] = $data;
        $json['data']['group'] = $group;
        return json_encode($json);
    }

    public function store(Request $request){

        $User = (object) $request->information;
        $Feature = $request->features;
        $user = new SysUser([
            'username' => $User->username,
            'password' => Hash::make($User->password),
            'FullName' => $User->FullName,
            'Note' => $User->Note,
            'Avata' => '/avata/avata.png'
        ]);
        if($user->save()){
            if(!empty($User->GroupUsers)){
                $sizeGroup = sizeof($User->GroupUsers);
                $arrInsertGroupMember = [];
                for($i = 0; $i < $sizeGroup; $i++){
                    $arrInsertGroupMember[] = ['UserID' => $user->UserID, 'UserGroupID' => $User->GroupUsers[$i]];
                }
                DB::table('sys_group_member')->insert($arrInsertGroupMember);
            }

            $arrInsert = [];
            $Access = null;
            $Add = null;
            $Edit = null;
            $Delete = null;
            foreach ($Feature as $item){
                $item = (object)$item;
                $Access[$item->FeatureID] = $item->Access;
                $Add[$item->FeatureID] = $item->Addnew;
                $Edit[$item->FeatureID] = $item->Edit;
                $Delete[$item->FeatureID] = $item->Delete;
            }

            $feature = SysFeature::all();
            foreach ($feature as $item){
                if(!empty($Access[$item->FeatureID])){
                    $access = 1;
                    if(!empty($Add[$item->FeatureID])){
                        $addnew = 1;
                    }else{
                        $addnew = 0;
                    }
                    if(!empty($Edit[$item->FeatureID])){
                        $edit = 1;
                    }else{
                        $edit = 0;
                    }
                    if(!empty($Delete[$item->FeatureID])){
                        $delete = 1;
                    }else{
                        $delete = 0;
                    }
                }else{
                    $access = 0;
                    $addnew = 0;
                    $edit = 0;
                    $delete = 0;
                }

                $arrInsert[] = ['UserID' => $user->UserID, 'FeatureID' => $item->FeatureID, 'Access' => $access, 'Addnew' => $addnew, 'Edit' => $edit, 'Delete' => $delete,
                    'AccessType' => 1, 'AddnewType' => 1, 'EditType' => 1, 'DeleteType' => 1, 'FeatureKey' => $item->FeatureKey];
            }
            DB::table('sys_user_feature_per')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $user->UserID;

        return json_encode($json);
    }
    public function view($id){
        $data = SysUser::find($id);
        $dataFeature = DB::select( DB::raw("SELECT P.*, if(ParentID = 0, P.FeatureID, P.ParentID) as parent_id,
                        C.Access, C.Addnew, C.Edit, C.Delete,
                        C.AccessType, C.AddnewType, C.EditType, C.DeleteType
                        FROM sys_feature P
                        LEFT JOIN sys_user_feature_per C
                        ON P.FeatureID = C.FeatureID AND C.UserID = '".$id."'
                        ORDER BY parent_id, ParentID"));
        $group = DB::select( DB::raw("SELECT P.UserGroupName, C.MemberID groupSelect
                    FROM  sys_group_member C
                    LEFT JOIN sys_user_group P
                    ON C.UserGroupID = P.UserGroupID
                    WHERE C.UserID = '".$id."'"));

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['feature'] = $dataFeature;
        $json['data']['group'] = $group;

        return json_encode($json);
    }

    public function edit($id){
        $data = SysUser::find($id);
        $dataFeature = DB::select( DB::raw("SELECT P.*, if(ParentID = 0, P.FeatureID, P.ParentID) as parent_id,
                        C.Access, C.Addnew, C.Edit, C.Delete,
                        C.AccessType, C.AddnewType, C.EditType, C.DeleteType
                        FROM sys_feature P
                        LEFT JOIN sys_user_feature_per C
                        ON P.FeatureID = C.FeatureID AND C.UserID = '".$id."'
                        ORDER BY parent_id, ParentID"));
        $group = DB::select( DB::raw("SELECT P.*, C.MemberID groupSelect
                        FROM sys_user_group P
                        LEFT JOIN sys_group_member C
                        ON P.UserGroupID = C.UserGroupID AND C.UserID = '".$id."'"));

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['feature'] = $dataFeature;
        $json['data']['group'] = $group;

        return json_encode($json);
    }
    public function update(Request $request, $id){
        $User = (object) $request->information;
        $Feature = $request->features;
        $user = SysUser::find($id);
        $user->username = $User->username;
        if($user->password != $User->password){
            $user->password = Hash::make($User->password);
        }
        $user->FullName = $User->FullName;
        $user->Note = $User->Note;
        $user->Inactive = $User->Inactive;
        if($user->save()){

            SysUserFeaturePer::where('UserID', $id)->delete();
            SysGroupMember::where('UserID', $id)->delete();
            if(!empty($User->GroupUsers)){
                $sizeGroup = sizeof($User->GroupUsers);
                $arrInsertGroupMember = [];
                for($i = 0; $i < $sizeGroup; $i++){
                    $arrInsertGroupMember[] = ['UserID' => $user->UserID, 'UserGroupID' => $User->GroupUsers[$i]];
                }
                DB::table('sys_group_member')->insert($arrInsertGroupMember);
            }

            $arrInsert = [];
            $Access = null;
            $Add = null;
            $Edit = null;
            $Delete = null;

            foreach ($Feature as $item){
                $item = (object)$item;
                $Access[$item->FeatureID] = $item->Access;
                $Add[$item->FeatureID] = $item->Addnew;
                $Edit[$item->FeatureID] = $item->Edit;
                $Delete[$item->FeatureID] = $item->Delete;
            }

            $feature = SysFeature::all();
            foreach ($feature as $item){
                if(!empty($Access[$item->FeatureID])){
                    $access = 1;
                    if(!empty($Add[$item->FeatureID])){
                        $addnew = 1;
                    }else{
                        $addnew = 0;
                    }
                    if(!empty($Edit[$item->FeatureID])){
                        $edit = 1;
                    }else{
                        $edit = 0;
                    }
                    if(!empty($Delete[$item->FeatureID])){
                        $delete = 1;
                    }else{
                        $delete = 0;
                    }
                }else{
                    $access = 0;
                    $addnew = 0;
                    $edit = 0;
                    $delete = 0;
                }
                $arrInsert[] = ['UserID' => $user->UserID, 'FeatureID' => $item->FeatureID, 'Access' => $access, 'Addnew' => $addnew, 'Edit' => $edit, 'Delete' => $delete,
                    'AccessType' => 1, 'AddnewType' => 1, 'EditType' => 1, 'DeleteType' => 1, 'FeatureKey' => $item->FeatureKey];
            }
            DB::table('sys_user_feature_per')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $user->UserID;

        return json_encode($json);
    }
    public function getInfoUser(Request $request){
        $UserID = $request->UserID;
        if(!$UserID){
            $json['status'] = 3;
            $json['msg'] = 'Bạn cần phải truyền mã người dùng!';
            $json['data'] = [];
            return json_encode($json);
        }
        $user = SysUser::find($UserID);
        if($user){
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = $user;
            return json_encode($json);
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
        }
    }

    public function delete(Request $request, $id){
        $UserID = $id;
        if(!$UserID){
            $json['status'] = 3;
            $json['msg'] = 'Bạn cần phải truyền mã người dùng!';
            $json['data'] = [];
            return json_encode($json);
        }
        $user = SysUser::find($UserID);
        if($user){
            $user->delete();
            SysUserFeaturePer::where('UserID', $UserID)->delete();
            SysGroupMember::where('UserID', $UserID)->delete();
            $json['status'] = 1;
            $json['msg'] = 'Xóa thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }


    public function deleteArray(Request $request){
        $UserIDs = $request->post('array_id');
        SysUser::destroy($UserIDs);
        SysUserFeaturePer::whereIn('UserID', $UserIDs)->delete();
        SysGroupMember::whereIn('UserID', $UserIDs)->delete();
        $json['status'] = 1;
        $json['msg'] = 'Xóa thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public function setGroup(Request $request){
        $groups = $request->groups;
        $feature = DB::select( DB::raw("SELECT P.FeatureID,
                        max(C.Access) accessC, max(C.Addnew) addnewC, max(C.Edit) editC, max(C.Delete) deleteC
                        FROM sys_feature P
                        LEFT JOIN sys_group_feature_per C
                        ON P.FeatureID = C.FeatureID AND C.UserGroup IN (".implode(",", $groups).")
                        GROUP BY P.FeatureID"));

        $json['status'] = 1;
        $json['msg'] = 'Không tồn tại!';
        $json['data'] = $feature;
        return json_encode($json);
    }

    public function changePassword(){
        $data = SysUser::find(Auth::user()->UserID);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function updatePassword(Request $request){
        $data = SysUser::find(Auth::user()->UserID);
        $file = $request->file('File');
        $username = $request->post('username');
        $password = $request->post('password');
        $linkFileAttach = '';

        $change = 0;
        if ($file) {
            $ext = strtolower($file->getClientOriginalExtension());
            $size = $file->getSize();
            $name = $file->getClientOriginalName();
            if ($size <= 2000000) {
                $arr_ext_img = ["jpeg", "jpg", "png"];
                $is_img = in_array($ext, $arr_ext_img);
                if ($is_img) {
                    if (isset($data->Avata) && file_exists(public_path() . $data->Avata)) {
                        if ($data->Avata != '/avata/avata.png') {
                            unlink(public_path() . $data->Avata);
                        }
                    }
                    $nameFile = Auth::user()->username . '_' . (date('YmdHis').gettimeofday()['usec']) . "." . $ext;
                    $linkFileAttach = '/avata/' . $nameFile;
                    $file->move('avata', $nameFile);
                    $data->Avata = $linkFileAttach;
                    $change = 1;
                }
            }
        }

        if($data->password != $password){

            $validator = Validator::make($request->all(),
                [
                    'username' => 'required',
                    'password' => 'required|confirmed',
                    'password_confirmation' => 'required'
                ],
                [
                    'password.confirmed' => 'Mật khẩu không khớp',
                    'username.required' => 'Bạn phải nhập tên đăng nhập',
                    'password.required' => 'Bạn phải nhập mật khẩu',
                    'password_confirmation.required' => 'Bạn phải nhắc lại mật khẩu',
                ]);

            if ($validator->fails()) {
                $json['status'] = 0;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }

            $data->password = Hash::make($password);
            $change = 1;
        }
        if($change == 1 && $username){
            $data->username = $username;
            $data->save();
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
}
