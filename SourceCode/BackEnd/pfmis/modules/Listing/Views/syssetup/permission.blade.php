@extends('layouts.master')

@section('content')
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Phân quyền</h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                <li><a data-action="close"><i class="ft-x"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard card-body-custom-list">
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
                                                    <td class="center"><input type="checkbox" onclick="checkAllCollumn(this, 1)"/></td>
                                                    <td class="center"><input type="checkbox" onclick="checkAllCollumn(this, 2)"/></td>
                                                    <td class="center"><input type="checkbox" onclick="checkAllCollumn(this, 3)"/></td>
                                                    <td class="center"><input type="checkbox" onclick="checkAllCollumn(this, 4)"/></td>
                                                </tr>
                                                @php($i = 1)
                                                @foreach($data as $item)
                                                    <?php
                                                        if($item->ParentID == 0){
                                                            $parent = $item->id;
                                                            $tr = 'parent';
                                                        }else{
                                                            $tr = 'child';
                                                            $parent = $item->ParentID;
                                                        }
                                                    ?>
                                                    <tr role="row" class="bg-tree-tr @if($item->ParentID == 0) feature-parent bold @else feature-child group-feature-{{$item->ParentID}} hide @endif">
                                                        <td class="sorting_1 @if($item->ParentID != 0) bg-tree-td @else bg-tree-td-parent @endif">
                                                            @if($item->ParentID == 0)
                                                                <i class="ft-plus-square toggle-parent" data="{{$item->id}}"></i>
                                                                <input type="checkbox" parent_id="{{$item->id}}" onclick="clickAllParent(this, {{$item->id}})" class="feature-parent-group"/>
                                                            @else
                                                                <input type="checkbox" parent_id="{{$item->ParentID}}" onclick="clickAllRowParent(this, {{$item->id}})" class="feature-child-group-{{$item->ParentID}}"/>
                                                            @endif
                                                            {{$item->featureName}}
                                                        </td>
                                                        <td class="center">
                                                            <input type="checkbox" parent_id="{{$item->ParentID}}" data="{{$item->id}}" @if($tr=='parent') onclick="clickAllCollumnParent(this, 1, {{$parent}})" @endif class="collumn-1 feature-child-group-{{$item->id}} feature-{{$tr}}-group-1-{{$parent}} feature-{{$tr}}-group-{{$parent}}"/>
                                                        </td>
                                                        <td class="center">
                                                            <input type="checkbox" parent_id="{{$item->ParentID}}" data="{{$item->id}}"  @if($tr=='parent') onclick="clickAllCollumnParent(this, 2, {{$parent}})" @endif class="collumn-2 feature-child-group-{{$item->id}} feature-{{$tr}}-group-2-{{$parent}} feature-{{$tr}}-group-{{$parent}}"/>
                                                        </td>
                                                        <td class="center">
                                                            <input type="checkbox" parent_id="{{$item->ParentID}}" data="{{$item->id}}"  @if($tr=='parent') onclick="clickAllCollumnParent(this, 3, {{$parent}})" @endif class="collumn-3 feature-child-group-{{$item->id}} feature-{{$tr}}-group-3-{{$parent}} feature-{{$tr}}-group-{{$parent}}"/>
                                                        </td>

                                                        <td class="center">
                                                            <input type="checkbox" parent_id="{{$item->ParentID}}" data="{{$item->id}}" @if($tr=='parent') onclick="clickAllCollumnParent(this, 4, {{$parent}})" @endif class="collumn-4 feature-child-group-{{$item->id}} feature-{{$tr}}-group-4-{{$parent}} feature-{{$tr}}-group-{{$parent}}"/>
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
                    </div>
                </div>
            </div>
    </section>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $('.toggle-parent').click(function () {
                $('.group-feature-'+$(this).attr('data')).toggle();
                if($(this).hasClass('ft-plus-square')){
                    $(this).removeClass('ft-plus-square');
                    $(this).addClass('ft-minus-square');
                }else{
                    $(this).removeClass('ft-minus-square');
                    $(this).addClass('ft-plus-square');
                }
            });


        });
        function clickAllParent($element, $ParentID) {
            if($($element).is(":checked")){
                $('.feature-child-group-'+$ParentID).prop('checked', true);
            }else{
                $('.feature-child-group-'+$ParentID).prop('checked', false);
            }
        }

        function clickAllRowParent($element, $data) {
            if($($element).is(":checked")){
                $('.feature-child-group-'+$data).prop('checked', true);
            }else{
                $('.feature-child-group-'+$data).prop('checked', false);
            }
        }

        function clickAllCollumnParent($element, $collumn, $data) {
            if($($element).is(":checked")){
                $('.feature-child-group-'+$collumn+'-'+$data).prop('checked', true);
            }else{
                $('.feature-child-group-'+$collumn+'-'+$data).prop('checked', false);
            }
        }

        function checkAll($element) {
            if($($element).is(":checked")){
                $('#data-permission').find('input[type="checkbox"]').prop('checked', true);
            }else{
                $('#data-permission').find('input[type="checkbox"]').prop('checked', false);
            }
        }

        function checkAllCollumn($element, $collumn) {
            if($($element).is(":checked")){
                $('.collumn-'+$collumn).prop('checked', true);
            }else{
                $('.collumn-'+$collumn).prop('checked', false);
            }
        }
    </script>
@stop