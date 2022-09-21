<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tên</div>
      <div class="col-lg-17 col-md-20 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 app-object-name">
        <input v-model="value.FixedAssetName" type="text" class="form-control" placeholder="Tên tài sản cố định"/>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-8 app-object-code d-md-none d-sm-none d-none d-lg-flex align-items-center">
        <span>Mã số</span>
        <input type="text" v-model="value.FixedAssetNo" class="form-control" placeholder="Mã số"/>
      </div>

    </div>

    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4 mb-lg-0 mb-md-3 mb-sm-3" title="Số hiệu sản phẩm">Số hiệu SP</div>
      <div class="col-lg-9 col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        <input v-model="value.Serialnumber" type="text" class="form-control" placeholder="Số hiệu sản phẩm"/>
      </div>
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4" title="Đơn vị tính">Đơn vị tính</div>
      <div class="col-lg-9 col-lg-9 col-md-20 col-sm-20">
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
          :url-api="$store.state.appRootApi + '/listing/api/fixed-asset/get-uom'"
          name-input="input-uom"
          title-modal="Đơn vị tính" size-modal="lg">
        </ijcore-modal-search-input>
      </div>
    </div>

    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4 mb-lg-0 mb-md-3 mb-sm-3" title="Loại tài sản cố định">Loại TSCĐ</div>
      <div class="col-lg-21 col-lg-21 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        <fixed-asset-modal-search-input-vcatelist
          v-model="value.FixedAssetCate"
          tableApi="fixed_asset_cate_list"
          refModal="myModalSearchVcatelist"
          id-modal="myModalSearchVcatelist"
          placeholder="Loại tài sản cố định"
          title-modal="Loại tài sản cố định" size-modal="lg"></fixed-asset-modal-search-input-vcatelist>
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
  import FixedAssetModalSearchInputVcatelist from "./FixedAssetModalSearchInputVcatelist";

  export default {
    name: 'fixed-asset-general-form',
    components: {IjcoreDatePicker, Select2, IjcoreModalSearchInput, FixedAssetModalSearchInputVcatelist},
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
    props: ['title', 'value', 'name', 'api', 'table', 'Task', 'isDetail', 'per', 'EmployeeOption', 'CompanyOption', 'FixedAssetPerEmployee'],
  }
</script>
<style>
  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }

</style>
