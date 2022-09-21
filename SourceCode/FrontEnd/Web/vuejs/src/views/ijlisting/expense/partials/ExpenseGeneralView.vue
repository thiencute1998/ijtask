<template>
  <div>
    <div class="form-group row">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên: </div>
      <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.ExpenseName}}
      </div>
      <div class="col-md-4 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số: </span>
        {{value.ExpenseNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0"> Là mục con của: </label>
      <label class="col-md-17 m-0">{{value.ParentName}}</label>
      <div v-if="value.ParentID" class="col-md-4 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số: </span>
        {{value.ParentNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Loại khoản chi: </label>
      <div class="col-md-21 m-0">
        <span v-for="(expenseCate, key) in value.ExpenseCate">
          {{expenseCate.CateName}}: {{expenseCate.Description}} <span v-if="key < (value.ExpenseCate.length - 1)"> , </span>
        </span>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3">Đơn vị Tính: </label>
      <div class="col-md-9 m-0">
        <span>{{value.UomName}}</span>
      </div>
      <label class="col-md-3">Mục - Tiểu mục: </label>
      <div class="col-md-9 m-0">
        <span>{{value.SbiItemName}}</span>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-md-3">Cân đối ngân sách: </label>
      <div class="col-md-9 m-0">
        <span>{{BudgetBalanceTypeOption[value.BudgetBalanceType]}}</span>
      </div>
      <label class="col-md-3">Loại NSNN: </label>
      <div class="col-md-9 m-0">
        <span>{{BudgetStateTypeOption[value.BudgetStateType]}}</span>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3">CTDT: </label>
      <div class="col-md-9 m-0">
        <span>{{value.NormName}}</span>
      </div>
      <label class="col-md-3">Lĩnh vực chi: </label>
      <div class="col-md-9">
        <span>{{value.SectorName}}</span>
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
  name: 'ExpenseGeneralView',
  props: ['value','isDetail'],
  data(){
    return{
      BudgetBalanceTypeOption: {
        '1': 'Trong CĐNS',
        '2': 'Ngoài CĐNS',
      },
      BudgetStateTypeOption: {
        '1': 'Trong ngân sách',
        '2': 'Ngoài ngân sách',
      },
    }
  }
}
</script>
