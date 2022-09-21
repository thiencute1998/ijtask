<template>
  <div>
    <div class="form-group row">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên: </div>
      <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.SbrSectorName}}
      </div>
      <div class="col-md-4 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số: </span>
        {{value.SbrSectorNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0"> Là mục con của: </label>
      <label class="col-md-17 m-0">{{value.ParentName}}</label>
      <div v-if="value.ParentID" class="col-md-4 col-sm-8 col-24 d-flex app-object-code">
        <span >Mã số: </span>
        {{value.ParentNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Loại lĩnh vực thu: </label>
      <div class="col-md-21 m-0">
        <span v-for="(sbrSectorCate, key) in value.SbrSectorCate">
          {{sbrSectorCate.CateName}}: {{sbrSectorCate.Description}} <span v-if="key < (value.SbrSectorCate.length - 1)"> , </span>
        </span>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-md-3" >Chương: </label>
      <div class="col-md-5 m-0" >
        <span>{{value.SbiChapterName}}</span>
      </div>
    </div>

    <div class="form-group row">
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
    name: 'SbrSectorGeneralView',
    props: ['value','isDetail'],
    data(){
      return{
        SbrSectorTypeOption: {
          '1': 'Thu',
          '2': 'Chi',
        },
        AccessTypeOptions: {
          1 : 'Chia sẻ',
          2 : 'Công khai',
          3 : 'Riêng tư'
        },
      }
    }
  }
</script>
