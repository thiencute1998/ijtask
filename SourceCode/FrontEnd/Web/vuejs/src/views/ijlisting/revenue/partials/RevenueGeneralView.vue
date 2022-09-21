<template>
  <div>
    <div class="form-group row">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên: </div>
      <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.RevenueName}}
      </div>
      <div class="col-md-4 col-sm-8 col-24 d-flex app-object-code">
        <span >Mã số: </span>
        {{value.RevenueNo}}
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
      <label class="col-md-3 m-0">Loại khoản thu: </label>
      <div class="col-md-21 m-0">
        <span v-for="(revenueCate, key) in value.RevenueCate">
          {{revenueCate.CateName}}: {{revenueCate.Description}} <span v-if="key < (value.RevenueCate.length - 1)"> , </span>
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
      <label class="col-md-3">Lĩnh vực thu: </label>
      <div class="col-md-9 m-0">
        <span>{{value.SbrSectorName}}</span>
      </div>
    </div>
    <div class="form-group row" v-if="isDetail">
      <label class="col-md-3 m-0">Ghi chú: </label>
      <label class="col-md-21 m-0" id="view-Note">
        {{value.Note}}
      </label>
    </div>
    <div class="form-group row" v-if="isDetail">
      <div class="col-md-24">Tỷ lệ điều tiết thu ngân sách :</div>
      <div class="col-md-24">
        <table class="table b-table table-sm table-bordered">
          <thead>
          <tr>
            <th scope="col" style="width: 15%" class="text-center">Ngày hiệu lực</th>
            <th scope="col" style="width: 15%" class="text-center">Ngày hết hiệu lực</th>
            <th scope="col" style="width: 60%" class="text-center">Chỉ tiêu phân bổ</th>
            <th scope="col" style="width: 5%" class="text-center">%</th>
            <th scope="col" style="width: 5%" class="text-center" title="Ngừng hoạt động"><i class="fa fa-ban m-0"></i></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(field, key) in value.RevenueReguItem">
            <td class="text-center">{{value.RevenueReguItem[key].EffectiveDate | convertServerDateToClientDate}}</td>
            <td class="text-center">{{value.RevenueReguItem[key].ExpirationDate | convertServerDateToClientDate}}</td>
            <td class="text-left">{{ReguRateOptions[value.RevenueReguItem[key].BudgetLevel].text}}</td>
            <td class="text-right">{{value.RevenueReguItem[key].ReguRate}}</td>
            <td class="text-center"><i class="fa fa-check" v-if="value.RevenueReguItem[key].RevenueReguActive === 0"></i></td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>


  </div>
</template>
<script>
export default {
  name: 'RevenueGeneralView',
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
      ReguRateOptions : [
        {value: 0, text : 'Để lại đơn vị'},
        {value: 1, text : 'Ngân sách trung ương'},
        {value: 2, text : 'Ngân sách tỉnh'},
        {value: 3, text : 'Ngân sách huyện'},
        {value: 4, text : 'Ngân sách xã'},
      ],
    }
  }
}
</script>
