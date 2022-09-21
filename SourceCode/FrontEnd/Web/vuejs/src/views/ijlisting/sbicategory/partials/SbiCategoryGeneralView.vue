<template>
  <div>
    <div class="form-group row">
      <div class="col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên: </div>
      <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.SbiCategoryName}}
      </div>
      <div class="col-md-3 col-sm-8 col-24 d-flex app-object-code">
        <span >Mã số: </span>
        {{value.SbiCategoryNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-4 m-0"> Là mục con của: </label>
      <label class="col-md-17 m-0">{{value.ParentName}}</label>
      <div v-if="value.ParentID" class="col-md-3 col-sm-8 col-24 d-flex app-object-code">
        <span >Mã số: </span>
        {{value.ParentNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-4 m-0">Loại - Loại khoản: </label>
      <div class="col-md-20 m-0">
        <span v-for="(sbiCategoryCate, key) in value.SbiCategoryCate">
          {{sbiCategoryCate.CateName}}: {{sbiCategoryCate.Description}} <span v-if="key < (value.SbiCategoryCate.length - 1)"> , </span>
        </span>
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
  name: 'SbiCategoryGeneralView',
  props: ['value','isDetail'],
  data(){
    return{

    }
  }
}
</script>
