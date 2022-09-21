@extends('layouts.master')

@section('content')

    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><span class="ft ft-list icon-header"></span> <span class="text-header">Người dùng</span></h4>
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
                            <form class="form" method="get" action="{{route('user.index')}}">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="form-group col-md-2 mb-2">
                                            <input type="text" id="FullName" class="form-control" placeholder="Họ và tên" name="FullName" value="@isset($request->FullName) {{$request->FullName}} @endisset">
                                        </div>
                                        <div class="form-group col-md-2 mb-2">
                                            <input type="text" id="username" class="form-control" placeholder="Username" name="username" value="@isset($request->username) {{$request->username}} @endisset">
                                        </div>
                                        <div class="form-group col-md-2 mb-2">
                                            <select id="Inactive" name="Inactive" class="form-control">
                                                <option value="">Trạng thái</option>
                                                <option value="1" @isset($request->Inactive) @if($request->Inactive == 1) selected @endif @endisset>Hoạt động</option>
                                                <option value="0" @isset($request->Inactive) @if($request->Inactive == 0) selected @endif @endisset>Ngừng hoạt động</option>
                                            </select>
                                        </div>
                                        <div class="right">
                                            <button type="submit" class="btn btn-warning mr-1 btn-sbc">
                                                <i class="ft-search"></i> Tìm
                                            </button>
                                            <a href="{{route('user.new')}}" class="btn btn-primary btn-add btn-sbc">
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
                                                        <th>Tên đăng nhập</th>
                                                        <th>Họ và tên</th>
                                                        <th>Loại người dùng</th>
                                                        <th>Trạng thái</th>
                                                        <th class="th-action"></th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @if(!empty($data))
                                                        @php($i = 1)
                                                        @foreach($data as $item)
                                                            <tr>
                                                                <td>{{$i}}</td>
                                                                <td>{{$item->username}}</td>
                                                                <td>{{$item->FullName}}</td>
                                                                <td>
                                                                    @switch($item->UserType)
                                                                        @case(1)
                                                                            Người quản trị
                                                                        @break

                                                                        @case(2)
                                                                            Người tác nghiệp
                                                                        @break

                                                                        @case(3)
                                                                            Người khai thác
                                                                        @break

                                                                        @case(4)
                                                                            Khách
                                                                        @break

                                                                        @default
                                                                    @endswitch
                                                                </td>
                                                                <td>@if($item->Inactive == 1) Hoạt động @else Ngừng hoạt động @endif</td>
                                                                <td>
                                                                    <a href="{{route('user.edit', ['id' => $item->UserID])}}">
                                                                        <i class="ft-edit icon"></i>
                                                                    </a>

                                                                    <a href="{{route('user.delete', ['id' => $item->UserID])}}" onclick="return confirm('Bạn muốn xóa người dùng: {{$item->username}}?')">
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