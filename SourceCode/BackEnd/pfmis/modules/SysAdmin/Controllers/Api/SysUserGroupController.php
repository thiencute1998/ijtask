<?php
namespace Module\SysAdmin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Module\SysAdmin\Models\SysFeature;
use Module\SysAdmin\Models\SysGroupFeaturePer;
use Module\SysAdmin\Models\SysGroupMember;
use Module\SysAdmin\Models\SysUser;
use Module\SysAdmin\Models\SysUserFeaturePer;
use Module\SysAdmin\Models\SysUserGroup;

class SysUserGroupController extends Controller{

    public function list(Request $request){
        $data = SysUserGroup::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

//    public function search(Request $request){
//        $user = new User();
//        $data = $user->search($request);
//        return view('SysAdmin::user.index', ['data' => $data, 'request' => $request]);
//    }
    public function create(){
        $data = SysFeature::raw("SELECT FeatureID, if(ParentID = 0, FeatureID, ParentID) as parent_id, FeatureName, FeatureKey, DefaultAccess,
          DefaultAddNew, DefaultEdit, DefaultDelete
          FROM sys_feature ORDER BY parent_id, ParentID")->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['feature'] = $data;

        return json_encode($json);
    }
    public function store(Request $request){
        $GroupUser = $request->information;
        $Feature = $request->features;
        $validate = Validator::make($GroupUser, [
            'UserGroupName' => 'required|unique:sys_user_group|max:50',
        ]);
        if($validate->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validate->errors();
            return $json;
        }
        $GroupUser = (object)$GroupUser;
        $userGroup = new SysUserGroup([
            'UserGroupName' => $GroupUser->UserGroupName,
            'UserGroupType' => $GroupUser->UserGroupType,
            'Note' => $GroupUser->Note
        ]);

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

        if($userGroup->save()){
            $feature = SysFeature::raw("SELECT FeatureID, if(ParentID = 0, FeatureID, ParentID) as parent_id, FeatureName, FeatureKey, UserGroupType FROM sys_feature ORDER BY parent_id, ParentID")->get();
            $arr = [];
            $arrInsert = [];
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

                $arrInsert[] = ['UserGroup' => $userGroup->UserGroupID, 'FeatureID' => $item->FeatureID, 'Access' => $access, 'Addnew' => $addnew, 'Edit' => $edit, 'Delete' => $delete];
            }
            DB::table('sys_group_feature_per')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = null;

        return json_encode($json);
    }


    public function edit($id){
        $data = SysUserGroup::find($id);
        $dataFeature = DB::select( DB::raw("SELECT P.*, if(ParentID = 0, P.FeatureID, P.ParentID) as parent_id,
                        C.Access, C.Addnew, C.Edit, C.Delete
                        FROM sys_feature P
                        LEFT JOIN sys_group_feature_per C
                        ON P.FeatureID = C.FeatureID AND C.UserGroup = '".$id."'
                        ORDER BY parent_id, ParentID"));


        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['feature'] = $dataFeature;

        return json_encode($json);
    }


    public function update(Request $request, $id){
        $GroupUser = $request->information;
        $Feature = $request->features;
        $userGroup = SysUserGroup::find($id);
        $GroupUserValidate = $GroupUser;
        $GroupUser = (object)$GroupUser;
        if($userGroup->UserGroupName != $GroupUser->UserGroupName){
            $validate = Validator::make($GroupUserValidate, [
                'UserGroupName' => 'required|max:50',
            ]);
            if($validate->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validate->errors();
                return $json;
            }
        }
        $userGroup->UserGroupName = $GroupUser->UserGroupName;
        $userGroup->UserGroupType = $GroupUser->UserGroupType;
        $userGroup->Note = $GroupUser->Note;


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
        if($userGroup->save()){
            SysGroupFeaturePer::where('UserGroup', $id)->delete();
            $feature = SysFeature::raw("SELECT FeatureID, if(parentId = 0, id, parentId) as parent_id, FeatureName, featureKey FROM sys_feature ORDER BY parent_id, ParentID")->get();
            $arr = [];
            $arrInsert = [];
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

                $arrInsert[] = ['UserGroup' => $userGroup->UserGroupID, 'FeatureID' => $item->FeatureID, 'Access' => $access, 'Addnew' => $addnew, 'Edit' => $edit, 'Delete' => $delete];
            }
            DB::table('sys_group_feature_per')->insert($arrInsert);

            $userGroups = SysGroupMember::where('UserGroupID', $id)->get();
            if($userGroups) {
                $groupbyUser = DB::select(DB::raw("SELECT UserID, GROUP_CONCAT(UserGroupID SEPARATOR ',') groups FROM sys_group_member GROUP BY UserID"));
                $arrGroupUser = [];
                if ($groupbyUser) {
                    foreach ($groupbyUser as $item) {
                        $arrGroupUser[$item->UserID] = $item->groups;
                    }
                }
                foreach ($userGroups as $item) {
                    $sql = "SELECT P.FeatureID, P.FeatureKey,
                        max(C.Access) accessC, max(C.Addnew) addnewC, max(C.Edit) editC, max(C.Delete) deleteC,
                        C1.Access, C1.Addnew, C1.Edit, C1.Delete, C1.AccessType, C1.AddnewType, C1.EditType, C1.DeleteType
                        FROM sys_feature P
                        LEFT JOIN sys_group_feature_per C
                        ON P.FeatureID = C.FeatureID AND C.UserGroup IN (".$arrGroupUser[$item->UserID].")
                        LEFT JOIN sys_user_feature_per C1
                        ON P.FeatureID = C1.FeatureID AND C1.UserID = '".$item->UserID."'
                        GROUP BY P.FeatureID";
                    $feature = DB::select( DB::raw($sql));
                    $arrInsert = [];
                    foreach ($feature as $value){
                        if($value->accessC == 1){
                            $access = 1;
                            $accessType = 0;
                        }else{
                            $access = $item->Access;
                            $accessType = 1;
                        }

                        if($value->addnewC == 1){
                            $addnew = 1;
                            $addnewType = 0;
                        }else{
                            $addnew = $item->Addnew;
                            $addnewType = 1;
                        }
                        if($value->editC == 1){
                            $edit = 1;
                            $editType = 0;
                        }else{
                            $edit = $item->Edit;
                            $editType = 1;
                        }
                        if($value->deleteC == 1){
                            $delete = 1;
                            $deleteType = 0;
                        }else{
                            $delete = $item->Delete;
                            $deleteType = 1;
                        }
                        $arrInsert[] = ['UserID' => $item->UserID, 'FeatureID' => $value->FeatureID, 'Access' => $access, 'Addnew' => $addnew, 'Edit' => $edit, 'Delete' => $delete,
                            'AccessType' => $accessType, 'AddnewType' => $addnewType, 'EditType' => $editType, 'DeleteType' => $deleteType, 'FeatureKey' => $value->FeatureKey
                        ];
                    }

                    SysUserFeaturePer::where('UserID', $item->UserID)->delete();
                    DB::table('sys_user_feature_per')->insert($arrInsert);
                }
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = null;

        return json_encode($json);
    }

    public function delete($id){
        $data = SysUserGroup::find($id);
        if($data){
            SysGroupFeaturePer::where('UserGroup', $id)->delete();
            $data->delete();


            $userGroups = SysGroupMember::where('UserGroupID', $id)->get();
            if($userGroups) {
                SysGroupMember::where('UserGroupID', $id)->delete();
                $groupbyUser = DB::select(DB::raw("SELECT UserID, GROUP_CONCAT(UserGroupID SEPARATOR ',') groups FROM sys_group_member GROUP BY UserID"));
                $arrGroupUser = [];
                if ($groupbyUser) {
                    foreach ($groupbyUser as $item) {
                        $arrGroupUser[$item->UserID] = $item->groups;
                    }
                }
                foreach ($userGroups as $item) {
                    if(isset($arrGroupUser[$item->UserID]) && !empty($arrGroupUser[$item->UserID])) {
                        $sql = "SELECT P.FeatureID, P.FeatureKey
                            max(C.Access) accessC, max(C.Addnew) addnewC, max(C.Edit) editC, max(C.Delete) deleteC,
                            C1.Access, C1.Addnew, C1.Edit, C1.Delete, C1.AccessType, C1.AddnewType, C1.EditType, C1.DeleteType
                            FROM sys_feature P
                            LEFT JOIN sys_group_feature_per C
                            ON P.id = C.featureId AND C.userGroup IN (" . $arrGroupUser[$item->userId] . ")
                            LEFT JOIN sys_user_feature_per C1
                            ON P.FeatureID = C1.FeatureID AND C1.UserID = '" . $item->UserID . "'
                            GROUP BY P.FeatureID";
                        $feature = DB::select(DB::raw($sql));
                        $arrInsert = [];
                        foreach ($feature as $value) {
                            if ($value->accessC == 1) {
                                $access = 1;
                                $accessType = 0;
                            } else {
                                $access = $item->Access;
                                $accessType = 1;
                            }

                            if ($value->addnewC == 1) {
                                $addnew = 1;
                                $addnewType = 0;
                            } else {
                                $addnew = $item->Addnew;
                                $addnewType = 1;
                            }
                            if ($value->editC == 1) {
                                $edit = 1;
                                $editType = 0;
                            } else {
                                $edit = $item->Edit;
                                $editType = 1;
                            }
                            if ($value->deleteC == 1) {
                                $delete = 1;
                                $deleteType = 0;
                            } else {
                                $delete = $item->Delete;
                                $deleteType = 1;
                            }
                            $arrInsert[] = ['UserID' => $item->UserID, 'FeatureID' => $value->FeatureID, 'Access' => $access, 'Addnew' => $addnew, 'Edit' => $edit, 'Delete' => $delete,
                                'AccessType' => $accessType, 'AddnewType' => $addnewType, 'EditType' => $editType, 'DeleteType' => $deleteType, 'FeatureKey' => $item->FeatureKey
                            ];
                        }

                        SysUserFeaturePer::where('UserID', $item->UserID)->delete();
                        DB::table('sys_user_feature_per')->insert($arrInsert);
                    }else{
                        DB::table('sys_user_feature_per')->where('UserID', $item->UserID)->update(['AccessType' => 1, 'AddnewType' => 1, 'EditType' => 1, 'DeleteType' => 1]);
                    }
                }
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = null;

        return json_encode($json);
    }


    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        DB::beginTransaction();
        try {
            // run your code here
            for ($i = 0; $i < sizeof($IDs); $i++){
                $id = $IDs[$i];
                $data = SysUserGroup::find($id);
                if($data){
                    SysGroupFeaturePer::where('UserGroup', $id)->delete();
                    $data->delete();


                    $userGroups = SysGroupMember::where('UserGroupID', $id)->get();
                    if($userGroups) {
                        SysGroupMember::where('UserGroupID', $id)->delete();
                        $groupbyUser = DB::select(DB::raw("SELECT UserID, GROUP_CONCAT(UserGroupID SEPARATOR ',') groups FROM sys_group_member GROUP BY UserID"));
                        $arrGroupUser = [];
                        if ($groupbyUser) {
                            foreach ($groupbyUser as $item) {
                                $arrGroupUser[$item->UserID] = $item->groups;
                            }
                        }
                        foreach ($userGroups as $item) {
                            if(isset($arrGroupUser[$item->UserID]) && !empty($arrGroupUser[$item->UserID])) {
                                $sql = "SELECT P.FeatureID, P.FeatureKey
                            max(C.Access) accessC, max(C.Addnew) addnewC, max(C.Edit) editC, max(C.Delete) deleteC,
                            C1.Access, C1.Addnew, C1.Edit, C1.Delete, C1.AccessType, C1.AddnewType, C1.EditType, C1.DeleteType
                            FROM sys_feature P
                            LEFT JOIN sys_group_feature_per C
                            ON P.id = C.featureId AND C.userGroup IN (" . $arrGroupUser[$item->userId] . ")
                            LEFT JOIN sys_user_feature_per C1
                            ON P.FeatureID = C1.FeatureID AND C1.UserID = '" . $item->UserID . "'
                            GROUP BY P.FeatureID";
                                $feature = DB::select(DB::raw($sql));
                                $arrInsert = [];
                                foreach ($feature as $value) {
                                    if ($value->accessC == 1) {
                                        $access = 1;
                                        $accessType = 0;
                                    } else {
                                        $access = $item->Access;
                                        $accessType = 1;
                                    }

                                    if ($value->addnewC == 1) {
                                        $addnew = 1;
                                        $addnewType = 0;
                                    } else {
                                        $addnew = $item->Addnew;
                                        $addnewType = 1;
                                    }
                                    if ($value->editC == 1) {
                                        $edit = 1;
                                        $editType = 0;
                                    } else {
                                        $edit = $item->Edit;
                                        $editType = 1;
                                    }
                                    if ($value->deleteC == 1) {
                                        $delete = 1;
                                        $deleteType = 0;
                                    } else {
                                        $delete = $item->Delete;
                                        $deleteType = 1;
                                    }
                                    $arrInsert[] = ['UserID' => $item->UserID, 'FeatureID' => $value->FeatureID, 'Access' => $access, 'Addnew' => $addnew, 'Edit' => $edit, 'Delete' => $delete,
                                        'AccessType' => $accessType, 'AddnewType' => $addnewType, 'EditType' => $editType, 'DeleteType' => $deleteType, 'FeatureKey' => $item->FeatureKey
                                    ];
                                }

                                SysUserFeaturePer::where('UserID', $item->UserID)->delete();
                                DB::table('sys_user_feature_per')->insert($arrInsert);
                            }else{
                                DB::table('sys_user_feature_per')->where('UserID', $item->UserID)->update(['AccessType' => 1, 'AddnewType' => 1, 'EditType' => 1, 'DeleteType' => 1]);
                            }
                        }
                    }
                }
            }


            DB::commit();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = null;
            return json_encode($json);
        }
        catch (exception $e) {
            //code to handle the exception

            DB::rollBack();

            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = null;
            return json_encode($json);
        }
    }
}
