<template>
  <div>
    <div class="form-group row">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên: </div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.ProgramName}}
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số: </span>
        {{value.ProgramNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Loại CTMT: </label>
      <div class="col-md-9 m-0">
        <span v-for="(programCate, key) in value.ProgramCate">
          {{programCate.CateName}}: {{programCate.Description}} <span v-if="key < (value.ProgramCate.length - 1)"> , </span>
        </span>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Loại: </label>
      <label class="col-md-5 m-0">{{ProgramTypeOption[value.ProgramType]}}</label>
      <label class="col-md-3">Cấp quản lý: </label>
      <label class="col-md-5">{{ManagementLevelOption[value.ManagementLevel]}}</label>
      <label class="col-md-3">Quyền truy cập: </label>
      <div class="col-md-5 m-0">
        {{AccessTypeOptions[value.AccessType]}}
      </div>
    </div>
    <div class="form-group row" v-if="isDetail">
      <label class="col-md-4 m-0">Ghi chú: </label>
      <label class="col-md-20 m-0" id="view-Note">
        {{value.Note}}
      </label>
    </div>


  </div>
</template>
<script>
  export default {
    name: 'ProgramGeneralView',
    props: ['value','isDetail'],
    data(){
      return{
        AccessTypeOptions: {
          1 : 'Chia sẻ',
          2 : 'Công khai',
          3 : 'Riêng tư'
        },
        ProgramTypeOption: {
          1: 'CTMT Quốc gia',
          2: 'CTMT Bổ sung'
        },
        ManagementLevelOption: {
          1:  'Trung ương',
          2:  'Tỉnh',
          3:  'Huyện',
          4:  'Xã',
        },
      }
    }
  }
</script>
