<template>
  <div>
    <div class="form-group row">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên: </div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.SbiItemName}}
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số: </span>
        {{value.SbiItemNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3">Là mục con của: </label>
      <div class="col-md-9 m-0">
        {{value.ParentName}}
      </div>
      <label class="col-md-3">Quyền truy cập: </label>
      <div class="col-md-9 m-0">
        {{AccessTypeOptions[value.AccessType]}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3">Loại: </label>
      <div class="col-md-9 m-0">
        {{value.ItemTypeOption[value.SbiItemType]}}
      </div>
      <label class="col-md-3">Nhóm: </label>
      <div class="col-md-9 m-0">
        {{value.ItemGroupOption[value.SbiItemGroup]}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3">Khoản thu: </label>
      <div class="col-md-9 m-0">
        {{value.RevenueName}}
      </div>
      <label class="col-md-3">Khoản chi: </label>
      <div class="col-md-9 m-0">
        {{value.ExpenseName}}
      </div>
    </div>
    <div class="form-group row" v-if="isDetail">
      <label class="col-md-3 m-0">Ghi chú: </label>
      <label class="col-md-21 m-0" id="view-Note">
        {{value.Note}}
      </label>
    </div>

  </div>
</template>
<script>
export default {
  name: 'SbiItemGeneralView',
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
