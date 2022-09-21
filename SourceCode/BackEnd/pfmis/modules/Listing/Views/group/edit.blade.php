@extends('layouts.master')

@section('content')
    <section id="horizontal-form-layouts">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="horz-layout-basic"><span class="ft ft-edit icon-header"></span> <span class="text-header">Nhóm người dùng</span></h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                <li><a data-action="close"><i class="ft-x"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collpase show">
                        <div class="card-body">
                            @if ($errors->any())
                                <?php
                                    $data->username = old('username');
                                    $data->password = old('password');
                                    $data->password_confirmation = old('password_confirmation');
                                    $data->fullname = old('fullname');
                                    $data->userType = old('userType');
                                    $data->NOrder = old('NOrder');
                                    $data->Note = old('Note');
                                ?>
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div><br />
                            @endif
                            <form class="form form-horizontal" id="form-group" method="post" action="{{route('group.update', ['id' =>$data->UserGroupID])}}">
                                @csrf
                                <div class="form-body">

                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary mr-1 btn-sbc" id="submit-button">
                                                <i class="fa fa-check-square-o"></i> Lưu
                                            </button>
                                            <a class="btn btn-warning mr-1 btn-sbc" href="{{route('group.index')}}">
                                                <i class="ft-x"></i> Hủy
                                            </a>
                                        </div>
                                    </div>
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="base-tab1" data-toggle="tab" aria-controls="tab1" href="#tab1" role="tab" aria-selected="false">Thông tin chung</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="base-tab2" data-toggle="tab" aria-controls="tab2" href="#tab2" role="tab" aria-selected="false">Phân quyền tính năng</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="base-tab3" data-toggle="tab" aria-controls="tab3" href="#tab3" role="tab" aria-selected="false">Phân quyền báo cáo</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content px-1 pt-1">
                                        <div class="tab-pane active" id="tab1" role="tabpanel" aria-labelledby="base-tab1">
                                            <div class="form-group row">
                                                <label class="col-md-2" for="UserGroupName">Tên nhóm</label>
                                                <div class="col-md-4">
                                                    <input type="text" id="UserGroupName" class="form-control" placeholder="Tên nhóm" name="UserGroupName" value="{{$data->UserGroupName}}">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-2" for="Note">Ghi chú</label>
                                                <div class="col-md-4">
                                                    <textarea class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note">{{$data->Note}}</textarea>
                                                </div>
                                            </div>


                                            <div class="form-group row">
                                                <label class="col-md-2" for="UserGroupType">Loại người dùng</label>
                                                <div class="col-md-4">
                                                    <select id="UserGroupType" name="UserGroupType" class="form-control">
                                                        <option value="">Chọn</option>
                                                        <option value="1" @if($data->UserGroupType == 1) selected @endif>Người quản trị</option>
                                                        <option value="2 " @if($data->UserGroupType == 2) selected @endif>Người tác nghiệp </option>
                                                        <option value="3" @if($data->UserGroupType == 3) selected @endif>Người khai thác</option>
                                                        <option value="4" @if($data->UserGroupType == 4) selected @endif>Người kiểm tra</option>
                                                        <option value="4" @if($data->UserGroupType == 5) selected @endif>Khách</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-2" for="NOrder">Số thứ tự</label>
                                                <div class="col-md-4">
                                                    <input type="number" id="NOrder" class="form-control" placeholder="Vị trí hiển thị" name="NOrder" value="{{$data->NOrder}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab2" role="tabpanel" aria-labelledby="base-tab2">
                                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="dataTables_length" id="DataTables_Table_0_length">
                                                            <table class="table table-striped table-striped table-bordered zero-configuration dataTable" id="data-permission" role="grid" aria-describedby="DataTables_Table_0_info">
                                                                <thead>
                                                                <tr role="row">
                                                                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="padding-left: 38px;">Tên</th>
                                                                    <th class="sorting  center" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 80px;">Truy cập</th>
                                                                    <th class="sorting center" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 80px;">Thêm</th>
                                                                    <th class="sorting center" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 80px;">Sửa</th>
                                                                    <th class="sorting center" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 80px;">Xóa</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr>
                                                                    <td style="padding-left: 38px;"><input type="checkbox" onclick="checkAll(this)"/> Chọn tất</td>
                                                                    <td class="center"><input type="checkbox" onclick="checkAllCollumn(this, 1)" id="check-all-1"/></td>
                                                                    <td class="center"><input type="checkbox" onclick="checkAllCollumn(this, 2)" id="check-all-2" disabled/></td>
                                                                    <td class="center"><input type="checkbox" onclick="checkAllCollumn(this, 3)" id="check-all-3" disabled/></td>
                                                                    <td class="center"><input type="checkbox" onclick="checkAllCollumn(this, 4)" id="check-all-4" disabled/></td>
                                                                </tr>
                                                                @php($i = 1)
                                                                <?php
                                                                $parentAccessDefault = 0;
                                                                $parentAddNewDefault = 0;
                                                                $parentEditDefault = 0;
                                                                $parentDeleteDefault = 0;

                                                                $checkAddNew = '';
                                                                $checkEdit = '';
                                                                $checkDelete = '';
                                                                ?>
                                                                @foreach($dataFeature as $item)
                                                                    <?php
                                                                    if($item->ParentID == 0){
                                                                        $parent = $item->FeatureID;
                                                                        $tr = 'parent';
                                                                        $parentAccessValue = $item->Access;
                                                                        $parentAddNewValue = $item->Addnew;
                                                                        $parentEditValue = $item->Edit;
                                                                        $parentDeleteValue = $item->Delete;
                                                                    }else{
                                                                        $tr = 'child';
                                                                        $parent = $item->ParentID;
                                                                    }

                                                                    if($item->Access == 0){
                                                                        $checkAddNew = 'disabled';
                                                                        $checkEdit = 'disabled';
                                                                        $checkDelete = 'disabled';
                                                                    }else{
                                                                        $checkAddNew = '';
                                                                        $checkEdit = '';
                                                                        $checkDelete = '';
                                                                    }
                                                                    ?>
                                                                    <tr role="row" UserGroupType="{{$item->UserGroupType}}" class="tr-feature bg-tree-tr @if($item->ParentID == 0) feature-parent bold @else feature-child group-feature-{{$item->ParentID}} hide @endif">
                                                                        <td class="sorting_1 @if($item->ParentID != 0) bg-tree-td @else bg-tree-td-parent @endif">
                                                                            @if($item->ParentID == 0)
                                                                                <i class="ft-plus-square toggle-parent" data="{{$item->FeatureID}}"></i>
                                                                                <input type="checkbox" parent_id="{{$item->FeatureID}}" onclick="clickAllParent(this, {{$item->FeatureID}})" class="feature-parent-group"/>
                                                                            @else
                                                                                <input type="checkbox" parent_id="{{$item->ParentID}}" onclick="clickAllRowParent(this, {{$item->FeatureID}})" class="feature-child-group-{{$item->ParentID}}"/>
                                                                            @endif
                                                                            {{$item->FeatureName}}
                                                                        </td>
                                                                        <td class="center">
                                                                            <input name="AccessInput[{{$item->FeatureID}}]" type="hidden" parent_id="{{$item->ParentID}}" data="{{$item->FeatureID}}" class="collumn-1 collumn-1-{{$item->FeatureID}} feature-child-group-{{$item->FeatureID}} feature-{{$tr}}-group-1-{{$parent}} feature-{{$tr}}-group-{{$parent}}" value="{{$item->Access}}"/>
                                                                            <input name="Access[{{$item->FeatureID}}]" type="checkbox" parent_id="{{$item->ParentID}}" data="{{$item->FeatureID}}" @if($tr=='parent') onclick="clickAllCollumnParent(this, 1, {{$parent}})" @else onclick="clickAccessFeature(this, {{$item->FeatureID}})" @endif class="checkbox collumn-1 collumn-1-{{$item->FeatureID}} feature-child-group-{{$item->FeatureID}} feature-{{$tr}}-group-1-{{$parent}} feature-{{$tr}}-group-{{$parent}}" @if($item->Access == 1) checked @endif/>
                                                                        </td>
                                                                        <td class="center">
                                                                            <input name="AddInput[{{$item->FeatureID}}]" type="hidden" parent_id="{{$item->ParentID}}" data="{{$item->FeatureID}}"  class="collumn-2 collumn-2-{{$item->FeatureID}} feature-child-group-{{$item->FeatureID}} feature-{{$tr}}-group-2-{{$parent}} feature-{{$tr}}-group-{{$parent}}" value="{{$item->Addnew}}"/>
                                                                            <input name="Add[{{$item->FeatureID}}]" type="checkbox" {{$checkAddNew}} parent_id="{{$item->ParentID}}" data="{{$item->FeatureID}}"  @if($tr=='parent') onclick="clickAllCollumnParent(this, 2, {{$parent}})" @endif class="checkbox collumn-2 collumn-2-{{$item->FeatureID}} feature-child-group-{{$item->FeatureID}} feature-{{$tr}}-group-2-{{$parent}} feature-{{$tr}}-group-{{$parent}}" @if($item->Addnew == 1) checked @endif/>
                                                                        </td>
                                                                        <td class="center">
                                                                            <input name="EditInput[{{$item->FeatureID}}]" type="hidden"  parent_id="{{$item->ParentID}}" data="{{$item->FeatureID}}" class="collumn-3 collumn-3-{{$item->FeatureID}} feature-child-group-{{$item->FeatureID}} feature-{{$tr}}-group-3-{{$parent}} feature-{{$tr}}-group-{{$parent}}" value="{{$item->Edit}}"/>
                                                                            <input name="Edit[{{$item->FeatureID}}]" type="checkbox" {{$checkEdit}}  parent_id="{{$item->ParentID}}" data="{{$item->FeatureID}}"  @if($tr=='parent') onclick="clickAllCollumnParent(this, 3, {{$parent}})" @endif class="checkbox collumn-3 collumn-3-{{$item->FeatureID}} feature-child-group-{{$item->FeatureID}} feature-{{$tr}}-group-3-{{$parent}} feature-{{$tr}}-group-{{$parent}}" @if($item->Edit == 1) checked @endif/>
                                                                        </td>

                                                                        <td class="center">
                                                                            <input name="DeleteInput[{{$item->FeatureID}}]" type="hidden" parent_id="{{$item->ParentID}}" data="{{$item->FeatureID}}" class="collumn-4 collumn-4-{{$item->FeatureID}} feature-child-group-{{$item->FeatureID}} feature-{{$tr}}-group-4-{{$parent}} feature-{{$tr}}-group-{{$parent}}" value="{{$item->Delete}}"/>
                                                                            <input name="Delete[{{$item->FeatureID}}]" type="checkbox" {{$checkDelete}} parent_id="{{$item->ParentID}}" data="{{$item->FeatureID}}" @if($tr=='parent') onclick="clickAllCollumnParent(this, 4, {{$parent}})" @endif class="checkbox collumn-4 collumn-4-{{$item->FeatureID}} feature-child-group-{{$item->FeatureID}} feature-{{$tr}}-group-4-{{$parent}} feature-{{$tr}}-group-{{$parent}}" @if($item->Delete == 1) checked @endif/>
                                                                        </td>
                                                                    </tr>
                                                                    @php($i++)
                                                                @endforeach

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab3" role="tabpanel" aria-labelledby="base-tab3">
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
    <script src="<?=env('BASE_URL')?>/js/group-permission.js"></script>
    <script>
        setUserGroupType('{{$data->UserGroupType}}');
    </script>
@stop