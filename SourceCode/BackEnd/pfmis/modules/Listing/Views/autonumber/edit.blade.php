@extends('layouts.master')
@section('content')
    <section id="horizontal-form-layouts">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="horz-layout-basic"><span class="ft ft-plus-square icon-header"></span> <span class="text-header">Thiết lập số tự động tăng</span></h4>
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
                            <form class="form form-horizontal" id="form-group" method="post" action="{{route('autonumber.store')}}">
                                @csrf
                                <div class="form-body">
                                    <div class="form-group row">
                                        <label class="col-md-2" for="NumberKey">Khóa</label>
                                        <div class="col-md-4">
                                            <input type="text" id="NumberKey" class="form-control" placeholder="Nhập khóa" name="NumberKey" value="{{old('NumberKey')}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2" for="NumberName">Tên</label>
                                        <div class="col-md-4">
                                            <input type="text" id="NumberName" class="form-control" placeholder="Nhập tên" name="NumberName" value="{{old('NumberName')}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2" for="NumberValue">Giá trị tăng</label>
                                        <div class="col-md-4">
                                            <input type="text" id="NumberValue" class="form-control" placeholder="Nhập giá trị tăng" name="NumberValue" value="{{old('NumberValue')}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2" for="Prefix">Giá trị đầu</label>
                                        <div class="col-md-4">
                                            <input type="text" id="Prefix" class="form-control" placeholder="Nhập giá trị đầu" name="Prefix" value="{{old('Prefix')}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2" for="Suffix">Giá trị sau</label>
                                        <div class="col-md-4">
                                            <input type="text" id="Suffix" class="form-control" placeholder="Nhập giá trị sau" name="Suffix" value="{{old('Suffix')}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-2" for="NumberMask">Định dạng mã số</label>
                                        <div class="col-md-4">
                                            <input type="text" id="NumberMask" class="form-control" placeholder="Nhập định dạng mã số" name="NumberMask" value="{{old('NumberMask')}}">
                                        </div>
                                    </div>
                                </div>


                                <div class="form-actions form-footer">
                                    <a class="btn btn-warning mr-1 btn-sbc" href="{{route('group.index')}}">
                                        <i class="ft-x"></i> Hủy
                                    </a>
                                    <button type="submit" id="submit-button" class="btn btn-primary btn-sbc">
                                        <i class="fa fa-check-square-o"></i> Lưu
                                    </button>
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

@stop