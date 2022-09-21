<template>
  <div>
    <div class="form-group row">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên: </div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.SectorName}}
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số: </span>
        {{value.SectorNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Loại lĩnh vực chi: </label>
      <div class="col-md-21 m-0">
        <span v-for="(sectorCate, key) in value.SectorCate">
          {{sectorCate.CateName}}: {{sectorCate.Description}} <span v-if="key < (value.SectorCate.length - 1)"> , </span>
        </span>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-md-3" v-if="value.SectorType == 2">Loại khoản: </label>
      <div class="col-md-5 m-0" v-if="value.SectorType == 2">
        <span>{{value.SbiCategoryName}}</span>
      </div>
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
    name: 'SectorGeneralView',
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
