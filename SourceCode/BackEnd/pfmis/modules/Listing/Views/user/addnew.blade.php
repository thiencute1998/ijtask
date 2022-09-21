@extends('layouts.master')
@section('content')
    <section id="horizontal-form-layouts">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="horz-layout-basic"><span class="ft ft-plus-square icon-header"></span> <span class="text-header">Người dùng</span></h4>
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
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div><br />
                            @endif
                            <form class="form form-horizontal" method="post" action="{{route('user.store')}}">
                                @csrf
                                <div class="form-body">
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary mr-1 btn-sbc">
                                                <i class="fa fa-check-square-o"></i> Lưu
                                            </button>
                                            <a class="btn btn-warning mr-1 btn-sbc" href="{{route('user.index')}}">
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
                                                <label class="col-md-2" for="username">Tài khoản đăng nhập</label>
                                                <div class="col-md-4">
                                                    <input type="text" id="username" class="form-control" placeholder="Tài khoản đăng nhập" name="username" value="{{old('username')}}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-2" for="password">Mật khẩu</label>
                                                <div class="col-md-4">
                                                    <input type="password" id="password" class="form-control" placeholder="Mật khẩu" name="password" value="{{old('password')}}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-2" for="password_confirmed">Nhập lại mật khẩu</label>
                                                <div class="col-md-4">
                                                    <input type="password" id="password_confirmation" class="form-control" placeholder="Mật khẩu" name="password_confirmation" value="{{old('password_confirmation')}}">
                                                </div>
                                            </div>


                                            <div class="form-group row">
                                                <label class="col-md-2" for="FullName">Họ và tên</label>
                                                <div class="col-md-4">
                                                    <input type="text" id="FullName" class="form-control" placeholder="Họ và tên" name="FullName" value="{{old('FullName')}}">
                                                </div>
                                            </div>


                                            <div class="form-group row">
                                                <label class="col-md-2" for="GroupUser">Nhóm người dùng</label>
                                                <div class="col-md-4">
                                                    <select data-placeholder="Nhóm người dùng" name = "GroupUser[]" class="select2-icons form-control" id="GroupUser" multiple="multiple" style="width: 100%;">
                                                        @foreach($group as $item)
                                                            <option value="{{$item->UserGroupID}}">{{$item->UserGroupName}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-2" for="Note">Ghi chú</label>
                                                <div class="col-md-4">
                                                    <textarea class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note">{{old('Note')}}</textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-2" for="NOrder">Số thứ tự</label>
                                                <div class="col-md-2">
                                                    <input type="number" style="width: 100px !important;" id="NOrder" class="form-control" placeholder="STT" name="NOrder" value="{{old('NOrder')}}">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-2"></label>
                                                <fieldset class="checkbox" style="padding-top: 7px; margin-left: 15px;">
                                                    <label>
                                                        <input type="checkbox" value="0" name="Inactive" id="Inactive" @if(old('Inactive') == 1) checked @endif>
                                                        Ngừng hoạt động
                                                    </label>
                                                </fieldset>
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
                                                                @foreach($data as $item)
                                                                    <?php
                                                                    if($item->ParentID == 0){
                                                                        $parent = $item->FeatureID;
                                                                        $tr = 'parent';
                                                                        $parentAccessDefault = $item->DefaultAccess;
                                                                        $parentAddNewDefault = $item->DefaultAddNew;
                                                                        $parentEditDefault = $item->DefaultEdit;
                                                                        $parentDeleteDefault = $item->DefaultDelete;
                                                                    }else{
                                                                        $tr = 'child';
                                                                        $parent = $item->ParentID;
                                                                    }

                                                                    if($parentAccessDefault == 0 || $item->DefaultAccess == 0){
                                                                        $checkAddNew = 'disabled';
                                                                        $checkEdit = 'disabled';
                                                                        $checkDelete = 'disabled';
                                                                        $item->DefaultAddNew = 0;
                                                                        $item->DefaultEdit = 0;
                                                                        $item->DefaultDelete = 0;
                                                                    }else{
                                                                        $checkAddNew = '';
                                                                        $checkEdit = '';
                                                                        $checkDelete = '';
                                                                    }
                                                                    ?>
                                                                    <tr role="row" class="bg-tree-tr @if($item->ParentID == 0) feature-parent bold @else feature-child group-feature-{{$item->ParentID}} hide @endif">
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
                                                                            <input name="Access[{{$item->FeatureID}}]" type="checkbox" parent_id="{{$item->ParentID}}" data="{{$item->FeatureID}}" @if($tr=='parent') onclick="clickAllCollumnParent(this, 1, {{$parent}})" @else onclick="clickAccessFeature(this, {{$item->FeatureID}})" @endif class="collumn-1 collumn-1-{{$item->FeatureID}} feature-1-{{$item->FeatureID}} feature-child-group-{{$item->FeatureID}} feature-{{$tr}}-group-1-{{$parent}} feature-{{$tr}}-group-{{$parent}}" @if($item->DefaultAccess == 1) checked @endif/>
                                                                        </td>
                                                                        <td class="center">
                                                                            <input name="Add[{{$item->FeatureID}}]" type="checkbox" {{$checkAddNew}} parent_id="{{$item->ParentID}}" data="{{$item->FeatureID}}"  @if($tr=='parent') onclick="clickAllCollumnParent(this, 2, {{$parent}})" @endif class="collumn-2 collumn-2-{{$item->FeatureID}} feature-2-{{$item->FeatureID}} feature-child-group-{{$item->FeatureID}} feature-{{$tr}}-group-2-{{$parent}} feature-{{$tr}}-group-{{$parent}}" @if($item->DefaultAddNew == 1) checked @endif/>
                                                                        </td>
                                                                        <td class="center">
                                                                            <input name="Edit[{{$item->FeatureID}}]" type="checkbox"{{$checkEdit}}  parent_id="{{$item->ParentID}}" data="{{$item->FeatureID}}"  @if($tr=='parent') onclick="clickAllCollumnParent(this, 3, {{$parent}})" @endif class="collumn-3 collumn-3-{{$item->FeatureID}} feature-3-{{$item->FeatureID}} feature-child-group-{{$item->FeatureID}} feature-{{$tr}}-group-3-{{$parent}} feature-{{$tr}}-group-{{$parent}}" @if($item->DefaultEdit == 1) checked @endif/>
                                                                        </td>

                                                                        <td class="center">
                                                                            <input name="Delete[{{$item->FeatureID}}]" type="checkbox" {{$checkDelete}} parent_id="{{$item->ParentID}}" data="{{$item->FeatureID}}" @if($tr=='parent') onclick="clickAllCollumnParent(this, 4, {{$parent}})" @endif class="collumn-4 collumn-4-{{$item->FeatureID}} feature-4-{{$item->FeatureID}} feature-child-group-{{$item->FeatureID}} feature-{{$tr}}-group-4-{{$parent}} feature-{{$tr}}-group-{{$parent}}" @if($item->DefaultDelete == 1) checked @endif/>
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
    <script src="<?=env('BASE_URL')?>/js/user-permission.js"></script>
    <script>
        $(document).ready(function () {
            $('#GroupUser').change(function () {
                if($(this).val().length > 0) {
                    $.ajax({
                        'url': '{{route('user.setGroup')}}',
                        'data': {
                            "_token": "{{ csrf_token() }}",
                            'groups': JSON.stringify($(this).val())
                        },
                        'type': 'POST',
                        success: function (data) {
                            var results = $.parseJSON(data);
                            console.log(results);
                            $.each( results, function( key, value ) {
                                console.log(value['id']);
                                if(value['accessC'] == 1){
                                    $('.feature-1-'+value['FeatureID']).prop('disabled', true);
                                    $('.feature-1-'+value['FeatureID']).prop('checked', true);
                                    $('.feature-1-'+value['FeatureID']).addClass('group');
                                    $('.feature-1-'+value['FeatureID']).removeClass('change');
                                }else{
                                    $('.feature-1-'+value['FeatureID']).prop('disabled', false);
                                    $('.feature-1-'+value['FeatureID']).removeClass('group');
                                }
                                if(value['addnewC'] == 1){
                                    $('.feature-2-'+value['FeatureID']).prop('disabled', true);
                                    $('.feature-2-'+value['FeatureID']).prop('checked', true);
                                    $('.feature-2-'+value['FeatureID']).addClass('group');
                                    $('.feature-2-'+value['FeatureID']).removeClass('change');
                                }else{
                                    $('.feature-2-'+value['FeatureID']).prop('disabled', false);
                                    $('.feature-2-'+value['FeatureID']).removeClass('group');
                                    if(value['accessC'] == 1){
                                        $('.feature-2-'+value['FeatureID']).addClass('change');
                                    }
                                }
                                if(value['editC'] == 1){
                                    $('.feature-3-'+value['FeatureID']).prop('disabled', true);
                                    $('.feature-3-'+value['FeatureID']).prop('checked', true);
                                    $('.feature-3-'+value['FeatureID']).addClass('group');
                                    $('.feature-3-'+value['FeatureID']).removeClass('change');
                                }else{
                                    $('.feature-3-'+value['FeatureID']).prop('disabled', false);
                                    $('.feature-3-'+value['FeatureID']).removeClass('group');
                                    if(value['accessC'] == 1){
                                        $('.feature-3-'+value['FeatureID']).addClass('change');
                                    }
                                }
                                if(value['deleteC'] == 1){
                                    $('.feature-4-'+value['FeatureID']).prop('disabled', true);
                                    $('.feature-4-'+value['FeatureID']).prop('checked', true);
                                    $('.feature-4-'+value['FeatureID']).addClass('group');
                                    $('.feature-4-'+value['FeatureID']).removeClass('change');
                                }else{
                                    $('.feature-4-'+value['FeatureID']).prop('disabled', false);
                                    $('.feature-4-'+value['FeatureID']).removeClass('group');
                                    if(value['accessC'] == 1){
                                        $('.feature-4-'+value['FeatureID']).addClass('change');
                                    }
                                }
                            });
                        }
                    });
                }else{
                    $('.group').prop('disabled', false);
                    $('.group').removeClass('group');
                    $('.change').removeClass('change');
                }
                setDisabledByAccess();
            });
        });
    </script>
@stop