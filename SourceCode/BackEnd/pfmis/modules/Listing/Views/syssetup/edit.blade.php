@extends('layouts.master')

@section('content')
    <section id="horizontal-form-layouts">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="horz-layout-basic"><span class="ft ft-edit icon-header"></span> <span class="text-header">Tùy chọn hệ thống</span></h4>
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
                            <form class="form form-horizontal" method="post" action="{{route('syssetup.update', ['id' =>$data->OptionID])}}">
                                @csrf
                                <div class="form-body">
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary mr-1 btn-sbc">
                                                <i class="fa fa-check-square-o"></i> Lưu
                                            </button>
                                            <a class="btn btn-warning mr-1 btn-sbc" href="{{route('syssetup.index')}}">
                                                <i class="ft-x"></i> Hủy
                                            </a>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2" for="OptionName">Tên tùy chọn</label>
                                        <div class="col-md-4">
                                            <input type="text" id="OptionName" class="form-control" placeholder="Tên tùy chọn" name="OptionName" value="{{$data->OptionName}}">
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label class="col-md-2" for="DataType">Kiểu tùy chọn</label>
                                        <div class="col-md-4">
                                            <select id="DataType" name="DataType" class="form-control">
                                                @foreach($dataType as $key=>$value)
                                                    <option value="{{$key}}" @if($key == $data->DataType) selected @endif>{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-2" for="OptionValue">Giá trị</label>
                                        <div class="col-md-4">
                                            <input type="text" id="OptionValue" class="form-control" placeholder="Giá trị" name="OptionValue" value="{{$data->OptionValue}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-2" for="AllowChange">Được thay đổi</label>
                                        <div class="col-md-4">
                                            <input type="checkbox" name="AllowChange" @if($data->AllowChange == 1) checked @endif/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2" for="NOrder">Vị trí hiển thị</label>
                                        <div class="col-md-4">
                                            <input type="number" id="NOrder" class="form-control" placeholder="Vị trí hiển thị" name="NOrder" value="{{$data->NOrder}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-2" for="Inactive">Trạng thái</label>
                                        <div class="col-md-4">
                                            <select id="Inactive" name="Inactive" class="form-control">
                                                <option value="0">Active</option>
                                                <option value="1">Inactive</option>
                                            </select>
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