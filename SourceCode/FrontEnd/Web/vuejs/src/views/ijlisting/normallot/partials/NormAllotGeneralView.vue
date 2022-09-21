<template>
  <div>
    <div class="form-group row">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên: </div>
      <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.NormAllotName}}
      </div>
      <div class="col-md-4 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số: </span>
        {{value.NormAllotNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Là mục con của:</label>
      <label class="col-md-17 m-0">{{value.ParentName}}</label>
      <div v-if="value.ParentID" class="col-md-4 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số: </span>
        {{value.ParentNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Đơn vị tính: </label>
      <label class="col-md-5 m-0">{{value.UomName}}</label>
      <label class="col-md-3 m-0">Ngày hiệu lực:</label>
      <label class="col-md-5 m-0">
        {{value.EffectiveDate | convertServerDateToClientDate}}
      </label>
      <label class="col-md-4  m-0">Ngày hết hiệu lực:</label>
      <label class="col-md-4  m-0">
        {{value.ExpirationDate | convertServerDateToClientDate}}
      </label>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Loại TCPBDT: </label>
      <div class="col-md-21 m-0">
        <span v-for="(normAllotCate, key) in value.NormAllotCate">
          {{normAllotCate.CateName}} - {{normAllotCate.CateNo}} :<span v-if="normAllotCate.Description"> {{normAllotCate.Description}} - {{normAllotCate.CateValue}}</span> <span v-if="key < (value.NormAllotCate.length - 1)"> <br> </span>
        </span>
      </div>
    </div>


  </div>
</template>
<script>
export default {
  name: 'NormAllotGeneralView',
  props: ['value','isDetail'],
  data(){
    return{

    }
  }
}
</script>
