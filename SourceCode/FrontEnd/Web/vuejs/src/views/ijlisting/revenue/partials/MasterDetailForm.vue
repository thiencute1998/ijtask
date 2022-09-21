<template>
  <div class="animated fadeIn component-master-detail-form">
    <a @click="init" class="new-row" v-if="isCreate">
      <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
    </a>
    <span v-else @click="init"><i class="fa fa-pencil-square-o" style="font-size: 18px; cursor: pointer" aria-hidden="true"></i></span>
    <b-modal scrollable ref="modal-master-detail-form" id="modal-master-detail-form" size="lg" @shown="onShownModal">
      <template v-slot:modal-title>
        <i class="fa fa-plus-square-o" v-if="isCreate"></i><i class="fa fa-edit icon" v-else></i> {{title}}
      </template>
      <div class="form-group row align-items-center">
        <div class="col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">
          <label >Ngày hiệu lực</label>
        </div>
        <div class="col-md-4">
          <ijcore-date-picker v-model="RevenueReguTable.EffectiveDate"></ijcore-date-picker>
        </div>
        <div class="col-md-4">
          <label >Ngày hết hiệu lực</label>
        </div>
        <div class="col-md-4">
          <ijcore-date-picker v-model="RevenueReguTable.ExpirationDate" ></ijcore-date-picker>
        </div>
      </div>

      <div class="form-group row align-center mb-0">
        <div v-for="val in RevenueReguTable.BudgetLevelTable" class="col-md-8">
          <div class="form-group row align-center ">
            <div class="col-md-12  pr-0" style="white-space: nowrap">
              <label >{{val.Name}}</label>
            </div>
            <div class="col-md-12">
              <ijcore-number v-model="val.ReguRate"></ijcore-number>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group row align-center">
        <div class="col-md-4">
        </div>
        <div class="col-md-20">
          <b-form-checkbox v-model="unActive">Ngừng hoạt động tỷ lệ điều tiết thu ngân sách</b-form-checkbox>
        </div>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100">
          <b-button variant="primary" size="md" class="mr-2" @click="onUpdate">Lưu</b-button>
          <b-button variant="primary" size="md" class="mr-2" @click="onHideModal">Hủy</b-button>
          <b-button variant="primary" size="md" class="mr-2" @click="onHideModal">Đóng</b-button>
        </div>
      </template>
    </b-modal>

  </div>
</template>
<style>
#modal-master-detail-form .mx-datepicker {
  width: 125px;
}
#modal-master-detail-form .select2-container {
  width: 100% !important;
}
</style>

<script>
import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
import IjcoreModalListing from "../../../../components/IjcoreModalListing";
import IjcoreSelect2Server2 from "../../../../components/IjcoreSelect2Server2";
import IjcoreNumber from "../../../../components/IjcoreNumber";
import Swal from "sweetalert2";
import moment from "moment";

export default {
  name: 'master-detail-form',
  components: {
    IjcoreDatePicker,
    IjcoreModalListing,
    IjcoreSelect2Server2,
    IjcoreNumber
  },

  props: {
    RevenueReguItem: [Array, Object],
    KeyItem: [Number],

    isCreate: true,
    title: [String]
  },
  data() {
    return {
      RevenueReguTable: {
          EffectiveDate: '',
          ExpirationDate : '',
          RevenueReguActive : true,
          BudgetLevelTable : [
            {BudgetLevel: 0, ReguRate: null, Name: 'Để lại đơn vị' },
            {BudgetLevel: 1, ReguRate: null, Name: 'Ngân sách TW' },
            {BudgetLevel: 2, ReguRate: null, Name: 'Ngân sách Tỉnh' },
            {BudgetLevel: 3, ReguRate: null, Name: 'Ngân sách Huyện' },
            {BudgetLevel: 4, ReguRate: null, Name: 'Ngân sách Xã' },
          ],
          EffectiveDateOld: '',
        },
      unActive: false,
    }
  },
  created() {},
  mounted() {

  },
  methods: {
    init() {
      let self = this;
      debugger
      if (self.isCreate) {
        self.RevenueReguTable.EffectiveDate = '';
        self.RevenueReguTable.ExpirationDate = '';
        self.RevenueReguTable.RevenueReguActive = true;
        self.RevenueReguTable.BudgetLevelTable = [
          {BudgetLevel: 0, ReguRate: null, Name: 'Để lại đơn vị' },
          {BudgetLevel: 1, ReguRate: null, Name: 'Ngân sách TW' },
          {BudgetLevel: 2, ReguRate: null, Name: 'Ngân sách Tỉnh' },
          {BudgetLevel: 3, ReguRate: null, Name: 'Ngân sách Huyện' },
          {BudgetLevel: 4, ReguRate: null, Name: 'Ngân sách Xã' },
        ];
        self.unActive = false;
        //
      } else {
        debugger
        self.RevenueReguTable.EffectiveDate =  self.RevenueReguItem[self.KeyItem].EffectiveDate;
        self.RevenueReguTable.ExpirationDate =  self.RevenueReguItem[self.KeyItem].ExpirationDate;
        if(self.RevenueReguItem[self.KeyItem].RevenueReguActive === 1){
          self.unActive = false;
        } else self.unActive = true;
        self.RevenueReguTable.BudgetLevelTable = [
          {BudgetLevel: 0, ReguRate: null, Name: 'Để lại đơn vị' },
          {BudgetLevel: 1, ReguRate: null, Name: 'Ngân sách TW' },
          {BudgetLevel: 2, ReguRate: null, Name: 'Ngân sách Tỉnh' },
          {BudgetLevel: 3, ReguRate: null, Name: 'Ngân sách Huyện' },
          {BudgetLevel: 4, ReguRate: null, Name: 'Ngân sách Xã' },
        ];
        let arrEdit =  _.filter(self.RevenueReguItem, ['EffectiveDate', self.RevenueReguItem[self.KeyItem].EffectiveDate]);
        self.$emit('editRevenueReguTable', arrEdit)
        _.forEach(arrEdit, function (val, key){
          if(val.BudgetLevel == 0) self.RevenueReguTable.BudgetLevelTable[0].ReguRate = val.ReguRate;
          if(val.BudgetLevel == 1) self.RevenueReguTable.BudgetLevelTable[1].ReguRate = val.ReguRate;
          if(val.BudgetLevel == 2) self.RevenueReguTable.BudgetLevelTable[2].ReguRate = val.ReguRate;
          if(val.BudgetLevel == 3) self.RevenueReguTable.BudgetLevelTable[3].ReguRate = val.ReguRate;
          if(val.BudgetLevel == 4) self.RevenueReguTable.BudgetLevelTable[4].ReguRate = val.ReguRate;
        });

      }
      self.onShownModal();
    },
    onEdit(){},
    onShownModal() {
      this.$refs['modal-master-detail-form'].show()
    },
    onHideModal() {
      this.$refs['modal-master-detail-form'].hide()
    },
    onUpdate(){
      debugger
      let self = this
      if(self.unActive){
        self.RevenueReguTable.RevenueReguActive = 0;
      } else  self.RevenueReguTable.RevenueReguActive = 1;
      let sumRegu = 0;
      _.forEach(self.RevenueReguTable.BudgetLevelTable, function (value, key){
        sumRegu += value.ReguRate;
      });
      let chekRegu = 0;
      if(self.RevenueReguTable.RevenueReguActive && self.RevenueReguItem.length > 0){
        debugger
        _.forEach(self.RevenueReguItem, function (val, key){
          debugger
          if(val.RevenueReguActive){
            if( moment(self.RevenueReguTable.ExpirationDate, 'L') < moment(val.EffectiveDate, 'L') || moment(self.RevenueReguTable.EffectiveDate, 'L') > moment(val.ExpirationDate, 'L')){
              chekRegu += 0;
            } else {
              chekRegu += 1;
            }
          }
        });
      }
      if(self.RevenueReguTable.EffectiveDate == '' || self.RevenueReguTable.ExpirationDate == '' ){
        Swal.fire(
          'Lỗi',
          'Nhập kỳ tỷ lệ điều tiết thu ngân sách',
          'error'
        );
      } else if(sumRegu != 100){
        Swal.fire(
          'Lỗi',
          'Tỷ lệ điều tiết thu ngân sách không đạt 100%',
          'error'
        );
      } else if(moment(self.RevenueReguTable.ExpirationDate, 'L') < moment(self.RevenueReguTable.EffectiveDate, 'L')){
        Swal.fire(
          'Lỗi',
          'Nhập ngày bắt đầu trước ngày kết thúc !',
          'error'
        );
      }else if(chekRegu > 0){
        Swal.fire(
          'Lỗi',
          'Thời gian tỷ lệ điều tiết bị trùng! ' +
          'Nhập lại hoặc ngừng sử dụng tỷ lệ điều tiết cũ',
          'error'
        );
      }else {
        if(self.isCreate){
          if(self.validateRevenueRegu(self.RevenueReguTable, self.RevenueReguItem)){
            Swal.fire(
              'Lỗi',
              'Kỳ ngân sách đã tồn tại',
              'error'
            );
          } else {
            self.$emit('saved', self.RevenueReguTable);
            self.onHideModal();
          }
        }

        if(!self.isCreate) {
          self.RevenueReguTable.EffectiveDateOld = self.RevenueReguItem[self.KeyItem].EffectiveDate;
          if (self.RevenueReguTable.EffectiveDateOld == self.RevenueReguTable.EffectiveDate) {
            self.$emit('edited', self.RevenueReguTable);
            self.onHideModal();
          } else if (self.validateRevenueRegu(self.RevenueReguTable, self.RevenueReguItem)) {
            Swal.fire(
              'Lỗi',
              'Kỳ ngân sách đã tồn tại ',
              'error'
            );
          } else {
            self.$emit('edited', self.RevenueReguTable);
            self.onHideModal();
          }
        }
      }

    },
    validateRevenueRegu(item, listItem){
      let validate = false;
      if(listItem.length > 0){
        _.forEach(listItem, function (value, key){
          if(item.EffectiveDate == value.EffectiveDate ) validate = true;
        });
      }
      return validate;
    },
  },

  beforeCreate(){
  },
  watch: {

  }
}
</script>
<style lang="css">
#modal-master-detail-form .mx-datepicker .mx-input-wrapper  {
  max-width: 110px !important;
}
</style>
