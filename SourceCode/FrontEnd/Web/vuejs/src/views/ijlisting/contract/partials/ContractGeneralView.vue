<template>
  <div>
    <div class="form-group row">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên: </div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.ContractName}}
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số: </span>
        {{value.ContractNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Loại hợp đồng: </label>
      <div class="col-md-9 m-0">
        <span v-for="(contractCate, key) in value.ContractCate">
          {{contractCate.CateName}}: {{contractCate.Description}} <span v-if="key < (value.ContractCate.length - 1)"> , </span>
        </span>
      </div>
      <label class="col-md-3">Quyền truy cập: </label>
      <div class="col-md-9 m-0">
        {{AccessTypeOptions[value.AccessType]}}
      </div>
    </div>
    <div class="form-group row" v-if="isDetail">
      <label class="col-md-3 m-0">Ngày ký: </label>
      <div class="col-md-5 mb-3 mb-sm-0">
        {{value.ContractDate | convertServerDateToClientDate}}
      </div>
      <label class="col-md-3 m-0">Ngày hiệu lực: </label>
      <div class="col-md-5">
        {{value.EffectiveDate | convertServerDateToClientDate}}
      </div>
      <label class="col-md-3 m-0">Ngày kết thúc: </label>
      <div class="col-md-5">
        {{value.FinishDate | convertServerDateToClientDate}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0">Giá trị HĐ: </label>
      <div class="col-md-5">
        {{value.ContractAmount | convertNumberToText}}
      </div>
      <label class="col-md-3 m-0">Dự án: </label>
      <div class="col-md-5">
        {{value.ProjectName}}
      </div>
      <label class="col-md-3 m-0">Người ký: </label>
      <div class="col-md-5 mb-3 mb-sm-0">
        {{value.EmployeeName}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0">Nhà cung cấp: </label>
      <div class="col-md-5">
        {{value.VendorName}}
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
    name: 'ContractGeneralView',
    props: ['value','isDetail'],
    data(){
      return{
        AccessTypeOptions: {
          1 : 'Chia sẻ',
          2 : 'Công khai',
          3 : 'Riêng tư'
        },
      }
    }
  }
</script>
