<!--============== How to use =======================================
==================================================================-->

<template>
    <div class="component-para-value">
      <b-form-select v-if="ComponentType === 1" v-model="value.ParaValueID" :options="PeriodValueOption" @change="changeParaValue"></b-form-select>
      <ijcore-date-picker v-model="value.ParaValue" style="width: 100%;" v-if="ComponentType === 0" @input-date-picker="changeParaValue"></ijcore-date-picker>
      <ijcore-select2-server2
        v-if="ComponentType === 2"
        v-model="value"
        :url="$store.state.appRootApi + '/listing/api/common/list2'"
        :table="Table"
        :field-name="TableName"
        :field-i-d="TableID"
        :field-no="TableNo"
        :field-type="1"
        :field-update-special="{ParaValue: TableID, ParaValueID: TableID, ParaValueNo: TableNo, ParaValueName: TableName}"
        :delay="200"
        :get-all="true"
        :client-d-b="true"
        placeholder="Chọn loại tiền"
        :allowClear="false"
        :settings="{}">
      </ijcore-select2-server2>
      <ijcore-modal-listing
        v-if="ComponentType === 3 && value.ParaKey === 'DISTRICT'"
        v-model="value" title="Huyện"  api="/listing/api/common/list"
        :field-where="{ProvinceID: value.ProvinceID}"
        :field-update-special="{ParaValue: TableID, ParaValueID: TableID, ParaValueNo: TableNo, ParaValueName: TableName}"
        field-name="DistrictName" field-no="DistrictNo" field-i-d="DistrictID" table="district">
      </ijcore-modal-listing>
      <ijcore-modal-listing
        v-else-if="ComponentType === 3 && value.ParaKey === 'COMMUNE'"
        v-model="value" title="Xã"  api="/listing/api/common/list"
        :field-where="{ProvinceID: value.ProvinceID, DistrictID: value.DistrictID}"
        :field-update-special="{ParaValue: TableID, ParaValueID: TableID, ParaValueNo: TableNo, ParaValueName: TableName}"
        field-name="CommuneName" field-no="CommuneNo" field-i-d="CommuneID" table="commune">
      </ijcore-modal-listing>
      <ijcore-modal-listing
        v-else-if="ComponentType === 3"
        v-model="value"
        :title="Title"
        :api="'/listing/api/common/list'"
        :table="Table"
        :field-update-special="{ParaValue: TableID, ParaValueID: TableID, ParaValueNo: TableNo, ParaValueName: TableName}"
        :field-i-d="TableID" :field-name="TableName" :field-no="TableNo">
      </ijcore-modal-listing>
    </div>
</template>

<style lang="css">

</style>
<script>
import IjcoreSelect2Server2 from "@/components/IjcoreSelect2Server2";
import Select2 from "v-select2-component";
import IjcoreModalListing from "@/components/IjcoreModalListing";
import IjcoreDatePicker from "@/components/IjcoreDatePicker";

import mixinPeriod from '@/mixins/period';

const ParaSetting = {
  // Kỳ
  PERIODTYPE: {
    Table: '',
    TableName: '',
    TableNo: '',
    TableID: '',
    ComponentType: 1 // kỳ
  },
  FROMDATE: {
    Table: '',
    TableName: '',
    TableNo: '',
    TableID: '',
    PeriodID: 11,
    ComponentType: 0 // kỳ
  },
  TODATE: {
    Table: '',
    TableName: '',
    TableNo: '',
    TableID: '',
    PeriodID: 12,
    ComponentType: 0 // kỳ
  },
  AMOUNTTYPE: {
    Table: 'ccy',
    TableName: 'CcyName',
    TableNo: 'CcyNo',
    TableID: 'CcyID',
    ComponentType: 2 // combobox
  },
  REVENUECATELIST: {
    Title: 'Loại khoản thu',
    Table: 'revenue_cate_list',
    ListingName: 'Revenue',
    TableName: 'CateName',
    TableNo: 'CateNo',
    TableID: 'CateID',
    ComponentType: 3 // popup combobox
  },
  EXPENSECATELIST: {
    Title: 'Loại khoản chi',
    Table: 'expense_cate_list',
    ListingName: 'Expense',
    TableName: 'CateName',
    TableNo: 'CateNo',
    TableID: 'CateID',
    ComponentType: 3 // popup combobox
  },
  SECTOR: {
    Title: 'Ngành',
    Table: 'sector',
    TableName: 'SectorName',
    TableNo: 'SectorNo',
    TableID: 'SectorID',
    ComponentType: 3 // popup combobox
  },
  PROVINCE: {
    Title: 'Tỉnh',
    Table: 'province',
    TableName: 'ProvinceName',
    TableNo: 'ProvinceNo',
    TableID: 'ProvinceID',
    ComponentType: 3 // popup combobox
  },
  DISTRICT: {
    Title: 'Huyện',
    Table: 'district',
    TableName: 'DistrictName',
    TableNo: 'DistrictNo',
    TableID: 'DistrictID',
    ComponentType: 3 // popup combobox
  },
  COMMUNE: {
    Title: 'Xã',
    Table: 'commune',
    TableName: 'CommuneName',
    TableNo: 'CommuneNo',
    TableID: 'CommuneID',
    ComponentType: 3 // popup combobox
  },
  COMPANY: {
    Title: 'Đơn vị',
    Table: 'company',
    TableName: 'CompanyName',
    TableNo: 'CompanyNo',
    TableID: 'CompanyID',
    ComponentType: 3 // popup combobox
  },
  DIRECTION: {
    Title: 'Chỉ thị',
    Table: 'direction',
    TableName: 'DirectionName',
    TableNo: 'DirectionNo',
    TableID: 'DirectionID',
    ComponentType: 3 // popup combobox
  }
}
  export default {
    name: 'component-para-value',
    mixins: [mixinPeriod],
    props:{
      value: [Array, Object],
      paras: [Array, Object]
    },
    components: {
      Select2,
      IjcoreSelect2Server2,
      IjcoreModalListing,
      IjcoreDatePicker
    },
    data () {
      return {
        ComponentType: 1,
        Table: '',
        TableName: '',
        TableNo: '',
        TableID: '',
        Title: ''
      }
    },
    created() {},
    mounted() {
      if (ParaSetting[this.value.ParaKey]) {
        this.ComponentType = ParaSetting[this.value.ParaKey].ComponentType;
        if (this.ComponentType === 1) {
          this.PeriodID = this.value.ParaValue;
          this.$_period_changePeriodType();
        }
        this.Table = ParaSetting[this.value.ParaKey].Table;
        this.TableName = ParaSetting[this.value.ParaKey].TableName;
        this.TableNo = ParaSetting[this.value.ParaKey].TableNo;
        this.TableID = ParaSetting[this.value.ParaKey].TableID;

        this.$set(this.value, 'TableID', this.TableID);
        this.$set(this.value, 'TableNo', this.TableNo);
        this.$set(this.value, 'TableName', this.TableName);

        if (ParaSetting[this.value.ParaKey].Title) {
          this.Title = ParaSetting[this.value.ParaKey].Title;
        }

        if (this.TableID && !this.value[this.TableID]) {
          this.$set(this.value, this.TableID, this.value.ParaValueID);
        }
        if (this.TableNo && !this.value[this.TableNo]) {
          this.$set(this.value, this.TableNo, this.value.ParaValueNo);
        }
        if (this.TableName && !this.value[this.TableName]) {
          this.$set(this.value, this.TableName, this.value.ParaValueName);
        }
        if (ParaSetting[this.value.ParaKey].ListingName) {
          this.$set(this.value, 'ListingName', ParaSetting[this.value.ParaKey].ListingName);
        }
        this.changeParaValue();
      }
    },
    methods: {
      changeParaValue() {
        if (this.value.ParaKey === 'PERIODTYPE') {
          let dateRange = _.find(this.PeriodValueOption, ['value', Number(this.value.ParaValueID)]);
          if (dateRange) {
            this.FromDate = dateRange.fromDate;
            this.ToDate = dateRange.toDate;
            this.value.FromDate = this.FromDate;
            this.value.ToDate = this.ToDate;
          }
        } else if (this.value.ParaKey === 'FROMDATE'){
          this.value.FromDate = this.value.ParaValue;
        }else if (this.value.ParaKey === 'TODATE') {
          this.value.ToDate = this.value.ParaValue;
        }
      }
    },
    watch:{
      value: {
        handler(val){
          // do stuff
        },
        deep: true
      },
      'value.ProvinceID'() {
        let indexDistrict = _.findIndex(this.paras, ['ParaKey', 'DISTRICT']);
        if (indexDistrict > -1) {
          if (this.paras[indexDistrict].ProvinceID) {
            this.paras[indexDistrict].ProvinceID = this.value.ProvinceID;
          }else {
            this.$set(this.paras[indexDistrict], 'ProvinceID', this.value.ProvinceID);
          }
          if (this.paras[indexDistrict].ProvinceNo) {
            this.paras[indexDistrict].ProvinceNo = this.value.ProvinceNo;
          }else {
            this.$set(this.paras[indexDistrict], 'ProvinceNo', this.value.ProvinceNo);
          }
          if (this.paras[indexDistrict].ProvinceName) {
            this.paras[indexDistrict].ProvinceName = this.value.ProvinceName;
          }else {
            this.$set(this.paras[indexDistrict], 'ProvinceName', this.value.ProvinceName);
          }
        }
        let indexCommune = _.findIndex(this.paras, ['ParaKey', 'COMMUNE']);
        if (indexCommune > -1) {
          if (this.paras[indexCommune].ProvinceID) {
            this.paras[indexCommune].ProvinceID = this.value.ProvinceID;
          }else {
            this.$set(this.paras[indexCommune], 'ProvinceID', this.value.ProvinceID);
          }
          if (this.paras[indexCommune].ProvinceNo) {
            this.paras[indexCommune].ProvinceNo = this.value.ProvinceNo;
          }else {
            this.$set(this.paras[indexCommune], 'ProvinceNo', this.value.ProvinceNo);
          }
          if (this.paras[indexCommune].ProvinceName) {
            this.paras[indexCommune].ProvinceName = this.value.ProvinceName;
          }else {
            this.$set(this.paras[indexCommune], 'ProvinceName', this.value.ProvinceName);
          }
        }
      },
      'value.DistrictID'() {
        let indexCommune = _.findIndex(this.paras, ['ParaKey', 'COMMUNE']);
        if (indexCommune > -1) {
          if (this.paras[indexCommune].DistrictID) {
            this.paras[indexCommune].DistrictID = this.value.DistrictID;
          }else {
            this.$set(this.paras[indexCommune], 'DistrictID', this.value.DistrictID);
          }
          if (this.paras[indexCommune].DistrictNo) {
            this.paras[indexCommune].DistrictNo = this.value.DistrictNo;
          }else {
            this.$set(this.paras[indexCommune], 'DistrictNo', this.value.DistrictNo);
          }
          if (this.paras[indexCommune].DistrictName) {
            this.paras[indexCommune].DistrictName = this.value.DistrictName;
          }else {
            this.$set(this.paras[indexCommune], 'DistrictName', this.value.DistrictName);
          }
        }
      },
      'value.ParaValue'(){
        if (this.value.ParaKey === 'PERIODTYPE') {
          this.PeriodID = this.value.ParaValue;
          this.$_period_changePeriodType();
          this.value.ParaValueID = this.PeriodValue;
        }
      }
    }
  }
</script>
