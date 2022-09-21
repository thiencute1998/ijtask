<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên: </div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        {{value.DirectionName}}
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số: </span>
        {{value.DirectionNo}}
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Là con của: </label>
      <div class="col-md-15">
        {{value.ParentName}}
      </div>
      <div v-if="value.ParentID" class="col-lg-6 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        {{value.ParentID}}
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Loại chỉ thị: </label>
      <span v-for="(directionCate, key) in value.DirectionCate">
          {{directionCate.CateName}}: {{directionCate.Description}} <span v-if="key < (value.DirectionCate.length - 1)"> , </span>
      </span>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" >Cơ quan ban hành: </label>
      <div class="col-md-5">
        {{value.CompanyIssuedName}}
      </div>
      <label class="col-md-3 m-0">Người ký: </label>
      <div class="col-md-5 mb-3 mb-sm-0">
        {{value.SignerIssuedName}}
      </div>
      <label class="col-md-3 m-0">Quyền truy cập: </label>
      <div class="col-md-5">
        {{AccessTypeOptions[value.AccessType]}}
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Ngày ban hành: </label>
      <div class="col-md-5 mb-3 mb-sm-0">
        {{value.DirectionDate | convertServerDateToClientDate}}
      </div>
      <label class="col-md-3 m-0">Đã đóng: </label>
      <div class="col-md-5">
        <b-form-checkbox disabled v-model="value.Closed"></b-form-checkbox>
      </div>
      <label class="col-md-3 m-0">Ngày đóng: </label>
      <div class="col-md-5">
        {{value.ClosedDate | convertServerDateToClientDate}}
      </div>
    </div>
    <div class="form-group row" v-if="isDetail">
      <label class="col-md-3">Mô tả: </label>
      <div class="col-md-21">
        {{value.Description}}
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    name: 'DirectionGeneralView',
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
