<template>
  <div class="animated fadeIn">
    <a @click="init" class="new-row" v-if="isCreate">
      <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
    </a>
    <span v-else @click="init"><i class="fa fa-pencil-square-o" style="font-size: 18px; cursor: pointer" aria-hidden="true"></i></span>
    <b-modal scrollable ref="modal-master-detail-form" id="modal-master-detail-form" size="lg" @shown="onShownModal">
      <template v-slot:modal-title>
        <i class="fa fa-plus-square-o" v-if="isCreate"></i><i class="fa fa-edit icon" v-else></i> {{title}}
      </template>
      <div class="form-group row align-items-center">
        <div class="col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Ngày hiệu lực </div>
        <div class="col-md-5">
          <ijcore-date-picker v-model="value.EffectiveDate"></ijcore-date-picker>
        </div>
        <div class="col-md-5">
          <span>Ngày hết hiệu lực</span>
        </div>
        <div class="col-md-5"><ijcore-date-picker v-model="value.ExpirationDate"></ijcore-date-picker></div>
      </div>
      <div class="form-group row align-items-center">
        <div class="col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Đơn vị tính </div>
        <div class="col-md-5">
          <ijcore-modal-listing
            title="Đơn vị tính"
            api="/listing/api/common/list"
            v-model="value"
            field-i-d="UomID"
            field-no="UomNo"
            field-name="UomName"
            table="uom"></ijcore-modal-listing>
        </div>
        <div class="col-md-3">
          <span>Tiền tệ</span>
        </div>
        <div class="col-md-5">
          <ijcore-select2-server2
            v-model="value"
            :url="$store.state.appRootApi + '/listing/api/common/list2'"
            table="ccy"
            field-name="CcyName"
            field-i-d="CcyID"
            field-no="CcyNo"
            :field-type="1"
            :get-all="true"
            placeholder="Chọn tiền tệ"
            :allowClear="false"
            :multiple="false">
          </ijcore-select2-server2>
        </div>
        <div class="col-md-2">
          <span>Tỉ giá</span>
        </div>
        <div class="col-md-5">
          <ijcore-number v-model="value.ExchangeRate" @changed="changeExchangeRate"></ijcore-number>
        </div>
      </div>
      <div class="form-group row align-items-center">
        <div class="col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Đơn giá tối thiểu</div>
        <div class="col-md-5">
          <ijcore-number v-model="value.FCMinUnitPrice" @changed="changeFCMinUnitPrice"></ijcore-number>
        </div>
        <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Quy đổi</div>
        <div class="col-md-5">
          <ijcore-number v-model="value.LCMinUnitPrice"></ijcore-number>
        </div>
      </div>
      <div class="form-group row align-items-center">
        <div class="col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Đơn giá tối đa</div>
        <div class="col-md-5">
          <ijcore-number v-model="value.FCMaxUnitPrice" @changed="changeFCMaxUnitPrice"></ijcore-number>
        </div>
        <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Quy đổi</div>
        <div class="col-md-5">
          <ijcore-number v-model="value.LCMaxUnitPrice"></ijcore-number>
        </div>
      </div>
      <div class="form-group row align-items-center">
        <div class="col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Đơn giá áp dụng</div>
        <div class="col-md-5">
          <b-form-select v-model="value.UnitPriceType" :options="[
            {value: null, text: '-- Chọn đơn giá áp dụng --', disabled: true},
            {value: 1, text: 'Tối đa'},
            {value: 2, text: 'Tối thiểu'},
            {value: 3, text: 'Trung bình'},
            {value: 4, text: 'Khác'}
          ]" @change="changeUnitPriceType"></b-form-select>
        </div>
        <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Nguyên tệ</div>
        <div class="col-md-5">
          <ijcore-number v-model="value.FCUnitPrice"></ijcore-number>
        </div>
        <div class="col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Quy đổi</div>
        <div class="col-md-5">
          <ijcore-number v-model="value.LCUnitPrice"></ijcore-number>
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
export default {
  name: 'master-detail-form',
  components: {
    IjcoreDatePicker,
    IjcoreModalListing,
    IjcoreSelect2Server2,
    IjcoreNumber
  },
  props: {
    value: {
      type: Object,
      default: function () {
        return {
          EffectiveDate: '',
          ExpirationDate: '',
          UomID: null,
          UomNo: '',
          UomName: '',
          CcyID: null,
          CcyNo: '',
          CcyName: '',
          ExchangeRate: 1,
          FCMinUnitPrice: '',
          LCMinUnitPrice: '',
          FCMaxUnitPrice: '',
          LCMaxUnitPrice: '',
          UnitPriceType: 1,
          FCUnitPrice: '',
          LCUnitPrice: ''
        }
      }
    },
    isCreate: true,
    title: [String]
  },
  data() {
    return {}
  },
  created() {},
  mounted() {},
  methods: {
    init() {
      if (this.isCreate) {
        this.value.EffectiveDate = '';
        this.value.ExpirationDate = '';
        this.value.UomID = null;
        this.value.UomNo = '';
        this.value.UomName = '';
        this.value.CcyID = null;
        this.value.CcyNo = '';
        this.value.CcyName = '';
        this.value.ExchangeRate = 1;
        this.value.FCMinUnitPrice = '';
        this.value.LCMinUnitPrice = '';
        this.value.FCMaxUnitPrice = '';
        this.value.LCMaxUnitPrice = '';
        this.value.UnitPriceType = 1;
        this.value.FCUnitPrice = '';
        this.value.LCUnitPrice = '';
      }
      this.onShownModal();
    },
    changeExchangeRate(){
      this.value.LCMinUnitPrice = this.value.FCMinUnitPrice * this.value.ExchangeRate;
      this.value.LCMaxUnitPrice = this.value.FCMaxUnitPrice * this.value.ExchangeRate;
    },
    changeFCMinUnitPrice(){
      this.value.LCMinUnitPrice = this.value.FCMinUnitPrice * this.value.ExchangeRate;
      this.changeUnitPriceType();
    },
    changeFCMaxUnitPrice(){
      this.value.LCMaxUnitPrice = this.value.FCMaxUnitPrice * this.value.ExchangeRate;
      this.changeUnitPriceType();
    },
    onEdit(){},
    onShownModal() {
      this.$refs['modal-master-detail-form'].show()
    },
    onHideModal() {
      this.$refs['modal-master-detail-form'].hide()
    },
    changeUnitPriceType(){
      if (this.value.UnitPriceType === 1) {
        this.value.FCUnitPrice = this.value.FCMaxUnitPrice;
        this.value.LCUnitPrice = this.value.FCUnitPrice * this.value.ExchangeRate;
      } else if (this.value.UnitPriceType === 2) {
        this.value.FCUnitPrice = this.value.FCMinUnitPrice;
        this.value.LCUnitPrice = this.value.FCUnitPrice * this.value.ExchangeRate;
      }else if (this.value.UnitPriceType === 2) {
        this.value.FCUnitPrice = (this.value.FCMinUnitPrice + this.value.FCMaxUnitPrice) / 2;
        this.value.LCUnitPrice = this.value.FCUnitPrice * this.value.ExchangeRate;
      }
    },
    onUpdate(){
      this.changeUnitPriceType();
      this.$emit('saved', this.value);
      this.onHideModal();
    }
  },
  beforeCreate(){
  },
  watch: {}
}
</script>
