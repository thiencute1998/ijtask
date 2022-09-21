<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tên</div>
      <div class="col-lg-17 col-md-20 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 app-object-name">
        <input v-model="value.InvestAssetName" type="text" class="form-control" placeholder="Tên tài sản đầu tư"/>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-8 app-object-code d-md-none d-sm-none d-none d-lg-flex align-items-center">
        <span>Mã số</span>
        <input type="text" v-model="value.InvestAssetNo" class="form-control" placeholder="Mã số"/>
      </div>

    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3">Đơn vị tính</label>
      <div class="col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        <ijcore-modal-search-input
          v-model="value.Uom"
          :select-fields-api="[
                                {field: 'UomID',fieldForSelected: 'id', showInTable: false, key: 'UomID'},
                                {field: 'UomName', fieldForSelected: 'name', showInTable: true, label: 'Đơn vị tính', key: 'UomName', sortable: true, thClass: 'd-none'}
                              ]"
          :search-fields-api="[{field: 'UomName', placeholder: 'Nhập tên đơn vị tính', name: 'UomName', class: '', style: ''}]"
          table="uom"
          ref="myModalSearchInputUom"
          id-modal="myModalSearchInputUom"
          :item-per-page="8"
          placeholder="Đơn vị tính"
          :url-api="$store.state.appRootApi + '/listing/api/invest-asset/get-uom'"
          name-input="input-uom"
          title-modal="Đơn vị tính" size-modal="lg">
        </ijcore-modal-search-input>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4 mb-lg-0 mb-md-3 mb-sm-3" title="Loại tài sản đầu tư">Loại TSĐT</div>
      <div class="col-lg-21 col-lg-21 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        <invest-asset-modal-search-input-vcatelist
          v-model="value.InvestAssetCate"
          tableApi="invest_asset_cate_list"
          refModal="myModalSearchVcatelist"
          id-modal="myModalSearchVcatelist"
          placeholder="Loại tài sản đầu tư"
          title-modal="Loại tài sản đầu tư" size-modal="lg"></invest-asset-modal-search-input-vcatelist>
      </div>
    </div>

    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4" title="Quyền truy cập">Quyền truy cập</div>
      <div class="col-lg-9 col-lg-9 col-md-20 col-sm-20">
        <b-form-select v-model="value.AccessType" :options="AccessTypeOptions" id="item-uom"></b-form-select>
      </div>
    </div>

  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
  import Select2 from 'v-select2-component';
  import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
  import InvestAssetModalSearchInputVcatelist from "./InvestAssetModalSearchInputVcatelist";

  export default {
    name: 'invest-asset-general-form',
    components: {IjcoreDatePicker, Select2, IjcoreModalSearchInput, InvestAssetModalSearchInputVcatelist},
    computed: {},
    data() {
      return {
        AccessTypeOptions:{
          1: 'Chia sẻ',
          2: 'Công khai',
          3: 'Riêng tư'
        }
      }
    },
    created() {},
    mounted() {},
    methods: {
      fetchData() {

      },
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onHideModal() {
        this.$refs['modal'].hide();
      },
      onToggleModal() {
        let self = this;
        this.currentPage = 1;
        this.$refs['modal'].show();
        this.fetchData();
      },
      onResetModal() {
      },
    },
    watch: {
      currentPage() {
        this.fetchData();
      }
    },
    props: ['title', 'value', 'name', 'api', 'table', 'Task', 'isDetail', 'per', 'EmployeeOption', 'CompanyOption', 'InvestAssetPerEmployee'],
  }
</script>
<style>
  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }

</style>
