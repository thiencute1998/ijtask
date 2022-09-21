<template>
  <div>
    <div class="form-group row">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên: </div>
      <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.CapitalName}}
      </div>
      <div class="col-md-4 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số: </span>
        {{value.CapitalNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Là mục con của: </label>
      <label class="col-md-17 m-0">{{value.ParentName}}</label>
      <div v-if="value.ParentID" class="col-md-4 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số: </span>
        {{value.ParentNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Loại nguồn vốn: </label>
      <div class="col-md-21 m-0">
        <span v-for="(capitalCate, key) in value.CapitalCate">
          {{capitalCate.CateName}} - {{capitalCate.CateNo}} :<span v-if="capitalCate.Description"> {{capitalCate.Description}} - {{capitalCate.CateValue}}</span> <span v-if="key < (value.CapitalCate.length - 1)"> <br> </span>
        </span>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-md-3">Thuộc: </label>
      <div class="col-md-9 m-0">
        <span>{{CapitalInOutOption[value.CapitalInOut]}}</span>
      </div>
      <label class="col-md-3">Loại vốn NSNN: </label>
      <div class="col-md-9 m-0">
        <span>{{BudgetStateTypeOption[value.BudgetStateType]}}</span>
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
  name: 'CapitalGeneralView',
  props: ['value','isDetail'],
  data(){
    return{
      CapitalInOutOption: {
        '1': 'Vốn trong nước',
        '2': 'Vốn ngoài nước',
      },
      BudgetStateTypeOption: {
        '1': 'Trong ngân sách',
        '2': 'Ngoài ngân sách',
      },
    }
  }
}
</script>
