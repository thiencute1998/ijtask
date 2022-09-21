@extends('layouts.master')

@section('content')
    <section id="horizontal-form-layouts">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="horz-layout-basic"><span class="ft ft-edit icon-header"></span> <span class="text-header">Đổi thông tin người dùng</span></h4>
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
                                ?>
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div><br />
                            @endif
                            <form class="form form-horizontal" method="post" action="{{route('user.updatePassword')}}" enctype="multipart/form-data">
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
                                    <div class="form-group row">
                                        <label class="col-md-2" for="username">Username</label>
                                        <div class="col-md-4">
                                            <input type="text" id="username" disabled class="form-control" placeholder="Username" name="username" value="{{$data->username}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2" for="password">Mật khẩu</label>
                                        <div class="col-md-4">
                                            <input type="password" id="password" class="form-control" placeholder="Mật khẩu" name="password" value="{{$data->password}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2" for="password_confirmed">Nhắc lại mật khẩu</label>
                                        <div class="col-md-4">
                                            <input type="password" id="password_confirmation" class="form-control" placeholder="Mật khẩu" name="password_confirmation" value="{{$data->password}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2" for="Avata">Ảnh đại diện</label>
                                        <div class="col-md-4">
                                            <fieldset class="form-group">
                                                <input type="file" class="form-control-file" id="Avata" name="Avata" onchange="changeFile(this)">
                                            </fieldset>

                                            <div class="card container-file-attach  container-file-attach" style="border: 1px solid #BABFC7;padding-left: 10px; padding-right: 10px;">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <img class="img-attach img-attach" src="<?=env('BASE_URL').$data->Avata?>" width="150" height="150"/>
                                                    </div>
                                                </div>
                                            </div>
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
    <script>
        function changeFile($element){
            var fileType = $element.files[0].type;
            var ext = $element.files[0].name.split('.').pop().toLowerCase();
            var ValidImageTypes = ["jpeg", "jpg", "png"];
            if ($.inArray(ext, ValidImageTypes) < 0 ) {
                swal('File không được phép upload!');
                $(this).empty();
            }else{
                if($.inArray(ext, ValidImageTypes) >= 0){
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('.img-attach')
                            .attr('src', e.target.result)
                            .width(150);
                    };
                    reader.readAsDataURL($element.files[0]);
                    $('.container-file-attach').show();
                    $('.img-attach').show();
                }
            }
        }
    </script>
@endsection