<template>
  <div class="norm-general-view">
    <div class="form-group row">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên: </div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.NormName}}
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số: </span>
        {{value.NormNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Là mục con của: </label>
      <label class="col-md-15 m-0">{{value.ParentName}}</label>
      <div v-if="value.ParentID" class="col-md-6 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số</span>
        {{value.ParentNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0" title="Loại chỉ tiêu dự toán">Loại CTDT: </label>
      <div class="col-md-13">
        <span v-for="(normCate, key) in value.NormCate">
          {{normCate.CateName}}: {{normCate.Description}} <span v-if="key < (value.NormCate.length - 1)"> , </span>
        </span>
      </div>
      <label class="col-md-3 m-0">Đơn vị tính: </label>
      <div class="col-md-5 m-0">
        {{value.UomName}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Thu/Chi: </label>
      <div class="col-md-5 m-0">
        {{NormTypeOption[value.NormType]}}
      </div>
      <label class="col-md-3" v-if="value.NormType == 1 || value.NormType == 3">Khoản thu: </label>
      <div class="col-md-5 m-0" v-if="value.NormType == 1 || value.NormType == 3">
        <span>{{value.RevenueName}}</span>
      </div>
      <label class="col-md-3" v-if="value.NormType == 2 || value.NormType == 3">Khoản chi: </label>
      <div class="col-md-5 m-0" v-if="value.NormType == 2 || value.NormType == 3">
        <span>{{value.ExpenseName}}</span>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Ghi chú: </label>
      <label class="col-md-21 m-0" id="view-Comment" style="white-space: pre-wrap;">
        {{value.Comment}}
      </label>
    </div>
<!--    <table class="table b-table table-sm table-bordered table-editable">-->
<!--      <thead>-->
<!--      <tr>-->
<!--        <th scope="col" style="width: 10%" class="text-center">Bảng chỉ tiêu</th>-->
<!--        <th scope="col" style="width: 10%" class="text-center">Tên chỉ tiêu</th>-->
<!--      </tr>-->
<!--      </thead>-->
<!--      <tbody>-->
<!--      <tr v-for="(field, key) in getNormMap">-->
<!--        <td>-->
<!--          <span :title="field.NormTableName">{{field.NormTableName}}</span>-->
<!--        </td>-->
<!--        <td>-->
<!--          <span :title="field.NormTableItemName">{{field.NormTableItemName}}</span>-->
<!--        </td>-->
<!--      </tr>-->
<!--      </tbody>-->

<!--    </table>-->
  </div>
</template>
<script>
export default {
  name: 'NormGeneralView',
  props: ['value','isDetail'],
  data(){
    return{
      NormTypeOption: {
        '1': 'Thu',
        '2': 'Chi',
        '3': 'Thu & Chi'
      },
    }
  },
  computed: {
    getNormMap(){
      return _.sortBy(this.value.NormMap, ['NormTableID', 'NormTableName']);
    }
  }
}
</script>
<style lang="css">
.norm-general-view .table tbody td{
  height: calc(1.5em + 0.55rem + 2px);
  font-size: 0.875rem;
  padding: 0.275rem 0.75rem;
}
</style>
