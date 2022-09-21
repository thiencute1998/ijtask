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
                                    <section id="configuration">
                                        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                            <div class="row">
                                            <div class="col-sm-12">
                                                <div class="dataTables_length" id="DataTables_Table_0_length">
                                                    <table id="simple-table" class="table table-striped  table-bordered table-hover">
                                                        <thead>
                                                        <tr class="text-center">
                                                            <th style="width: 150px">Tên</th>
                                                            <th style="width: 100px">Giá trị tăng</th>
                                                            <th style="width: 80px;">Giá trị đầu</th>
                                                            <th style="width: 80px;">Giá trị sau</th>
                                                            <th>Định dạng mã số</th>
                                                            <th style="width: 50px;"></th>
                                                        </tr>
                                                        </thead>

                                                        <tbody id="content-task-assign-table">
                                                        @php($i=1)
                                                        @php($k=1)
                                                        @foreach($autoNumber as $item)
                                                            @php($i=$item->NumberID)
                                                            @php($k++)
                                                            <tr data="{{$item->NumberID}}" class="row_autonumber_{{$item->NumberID}}">
                                                                <td class="td-text-input td-input td-NumberKey td-NumberKey-input-{{$item->NumberID}} display-none" data="{{$item->NumberID}}" data-name="NumberKey">
                                                                    <input type="text" name="NumberKey[]" class="form-control input-NumberKey-{{$item->NumberID}}" value="{{$item->NumberKey}}"/>
                                                                </td>
                                                                <td class="td-text td-NumberName td-NumberName-text-{{$item->NumberID}}" data="{{$item->NumberID}}" data-name="NumberName">{{$item->NumberName}}</td>
                                                                <td class="td-text-input td-input td-NumberName td-NumberName-input-{{$item->NumberID}} display-none" data="{{$item->NumberID}}" data-name="NumberName">
                                                                    <input type="text" name="NumberName[]" class="form-control input-NumberName-{{$item->NumberID}}" value="{{$item->NumberName}}"/>
                                                                </td>

                                                                <td class="td-text td-NumberValue td-NumberValue-text-{{$item->NumberID}}" data="{{$item->NumberID}}" data-name="NumberValue">{{$item->NumberValue}}</td>
                                                                <td class="td-text-input td-input td-NumberValue td-NumberValue-input-{{$item->NumberID}} display-none" data="{{$item->NumberID}}" data-name="NumberValue">
                                                                    <input type="text" name="NumberValue[]" class="form-control input-NumberValue-{{$item->NumberID}}" value="{{$item->NumberValue}}"/>
                                                                </td>

                                                                <td class="td-text td-Prefix td-Prefix-text-{{$item->NumberID}}" data="{{$item->NumberID}}" data-name="Prefix">{{$item->Prefix}}</td>
                                                                <td class="td-text-input td-input td-Prefix td-Prefix-input-{{$item->NumberID}} display-none" data="{{$item->NumberID}}" data-name="Prefix">
                                                                    <input type="text" name="Prefix[]" class="form-control input-Prefix-{{$item->NumberID}}" value="{{$item->Prefix}}"/>
                                                                </td>

                                                                <td class="td-text td-Suffix td-Suffix-text-{{$item->NumberID}}" data="{{$item->NumberID}}" data-name="Suffix">{{$item->Suffix}}</td>
                                                                <td class="td-text-input td-input td-Suffix td-Suffix-input-{{$item->NumberID}} display-none" data="{{$item->NumberID}}" data-name="Suffix">
                                                                    <input type="text" name="Suffix[]" class="form-control input-Suffix-{{$item->NumberID}}" value="{{$item->Suffix}}"/>
                                                                </td>

                                                                <td class="td-text td-NumberMask td-NumberMask-text-{{$item->NumberID}}" data="{{$item->NumberID}}" data-name="NumberMask" style="word-break: break-all;">{{$item->NumberMask}}</td>
                                                                <td class="td-text-input td-input td-NumberMask td-NumberMask-input-{{$item->NumberID}} display-none" data="{{$item->NumberID}}" data-name="NumberMask">
                                                                    <input type="text" name="NumberMask[]" class="form-control input-NumberMask-{{$item->NumberID}}" value="{{$item->NumberMask}}"/>
                                                                </td>
                                                                <td class="td-input td-action"><i title="Lưu" class="ft-save icon" data="{{$item->NumberID}}" onclick="saveAutoNumber(this)" style="cursor: pointer;"></i></td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </section>
                                </div>


                                <div class="form-actions form-footer">
                                    <div class="float-right mg-bottom-10">
                                        <a class="btn btn-warning mr-1 btn-sbc" href="{{route('group.index')}}">
                                            <i class="ft-x"></i> Đóng
                                        </a>
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
        $(document).ready(function () {
            // $('.EstimatedWork').prop('disabled', true);
            $('.addnew_row').attr('data', {{$i+1}});

            $('.td-text').click(function () {
                $data = $(this).attr('data');
                $dataname = $(this).attr('data-name');
                $(this).hide();
                $('.td-'+$dataname+'-input-'+$data).show();
                if($dataname == 'EmployeeName'){
                    $('.td-'+$dataname+'-input-'+$data+' input').focus();
                    $('.td-'+$dataname+'-input-'+$data+' input').click();
                }else{
                    $('.td-'+$dataname+'-input-'+$data+' input').focus();
                }
            });
            $('.td-text-input').focusout(function () {
                $data = $(this).attr('data');
                $dataname = $(this).attr('data-name');
                $(this).hide();
                $('.td-' + $dataname + '-text-' + $data).text($('.input-'+$dataname+'-'+$data).val());
                $('.td-' + $dataname + '-text-' + $data).show();

            });
            $('.StartDateDetail,.DueDateDetail').on("dp.change", function (e) {
                $data = $(this).attr('data');
                getHour($data);
            });
        });

        function addAssignTask($element){
            $data = parseInt($($element).attr('data'));
            $("#content-task-assign-table").append('');
            $($element).attr('data', $data + 1);

        }

        function saveAutoNumber($element) {
            $data = $($element).attr('data');
            $NumberKey = $('.input-NumberKey-'+$data).val();
            $NumberName = $('.input-NumberName-'+$data).val();
            $NumberValue = $('.input-NumberValue-'+$data).val();
            $Prefix = $('.input-Prefix-'+$data).val();
            $Suffix = $('.input-Suffix-'+$data).val();
            $NumberMask = $('.input-NumberMask-'+$data).val();
            $.ajax({
                'url': '{{route('autonumber.saveAutoNumber')}}',
                'data': {
                    "_token": "{{ csrf_token() }}",
                    'NumberName': $NumberName,
                    'NumberValue': $NumberValue,
                    'Prefix': $Prefix,
                    'Suffix': $Suffix,
                    'NumberMask': $NumberMask,
                    'NumberKey' : $NumberKey,
                    'NumberID' : $data
                },
                'type': 'POST',
                success: function (data) {
                    var result = JSON.parse(data);
                    if(result.status == 1){
                        alert('Cập nhật thành công số tự động tăng "'+result.name+'"!');
                    }else{
                        var errors = result.msg;
                        var textAlert = '';
                        $.each(errors, function( index, value ) {
                            if(!textAlert){
                                textAlert += 'Cập nhật thành công số tự động tăng "'+result.name+'" bởi:\n'+value;
                            }else{
                                textAlert += '\n'+value;
                            }
                        });
                        alert(textAlert);
                    }
                }
            });
        }
    </script>
@stop