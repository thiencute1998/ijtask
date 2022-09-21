@extends('layouts.master')

@section('content')

    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><span class="ft ft-list icon-header"></span> <span class="text-header">Nhóm người dùng</span></h4>
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
                            <form class="form" method="get" action="{{route('group.index')}}">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="form-group col-md-2 mb-2">
                                            <input type="text" id="UserGroupName" class="form-control" placeholder="Tên nhóm" name="UserGroupName" value="@isset($request->UserGroupName) {{$request->UserGroupName}} @endisset">
                                        </div>
                                        <div class="right">
                                            <button type="submit" class="btn btn-warning mr-1 btn-sbc">
                                                <i class="ft-search"></i> Tìm
                                            </button>
                                            <a href="{{route('group.new')}}" class="btn btn-primary btn-sbc">
                                                <i class="fa fa-plus"></i> Tạo mới
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </form>
                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="dataTables_length" id="DataTables_Table_0_length">
                                            <table id="simple-table" class="table table-striped  table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="w-20px">STT</th>
                                                        <th>Tên nhóm</th>
                                                        <th>Trạng thái</th>
                                                        <th>Ghi chú</th>
                                                        <th class="th-action" style="width: 80px !important;"></th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @if(!empty($data))
                                                        @php($i = 1)
                                                        @foreach($data as $item)
                                                            <tr>
                                                                <td>{{$i}}</td>
                                                                <td>{{$item->UserGroupName}}</td>
                                                                <td>@if($item->Inactive == 1) Hoạt động @else Ngừng hoạt động @endif</td>
                                                                <td>{{$item->Note}}</td>
                                                                <td>
                                                                    <a href="{{route('group.edit', ['id' => $item->UserGroupID])}}">
                                                                        <i class="ft-edit icon"></i>
                                                                    </a>

                                                                    <a href="{{route('group.delete', ['id' => $item->UserGroupID])}}" onclick="return confirm('Bạn muốn xóa đơn vị: {{$item->UserGroupName}}?')">
                                                                        <i class="ft-trash-2 icon"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>

                                                            @php($i++)
                                                        @endforeach

                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{ $data->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection