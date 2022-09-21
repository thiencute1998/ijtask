<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-md-3 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tên</div>
      <div class="col-lg-17 col-md-21 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 app-object-name">
        {{value.FixedAssetName}}
      </div>
      <div class="col-lg-4 app-object-code d-md-none d-sm-none d-none d-lg-flex align-items-center">
        <span>Mã số</span>
        {{value.FixedAssetNo}}
      </div>
    </div>

    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4 mb-lg-0 mb-md-3 mb-sm-3" title="Số hiệu sản phẩm">Số hiệu SP</div>
      <div class="col-lg-9 col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        {{ value.Serialnumber }}
      </div>
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4" title="Đơn vị tính">Đơn vị tính</div>
      <div class="col-lg-9 col-lg-9 col-md-20 col-sm-20">
        <span>{{ value.UomName }}</span>
      </div>
    </div>

    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4 mb-lg-0 mb-md-3 mb-sm-3" title="Loại nhà cung cấp">Loại TSCĐ</div>
      <div class="col-lg-9 col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        <span v-for="(FixedAssetCate, key) in value.FixedAssetCate" v-if="value.FixedAssetCate.length">
            {{FixedAssetCate.CateName}}: {{FixedAssetCate.Description}} <span v-if="key < (value.FixedAssetCate.length - 1)">,</span>
          </span>
      </div>
    </div>

    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4" title="Loại nhà cung cấp">Quyền truy cập</div>
      <div class="col-lg-9 col-lg-9 col-md-20 col-sm-20">
        <span>{{AccessTypeOptions[value.AccessType]}}</span>
      </div>
    </div>

  </div>

</template>

<script>
  import ApiService from '@/services/api.service';

  export default {
    name: 'fixed-asset-general-view',
    components: {},
    computed: {},
    data() {
      return {
        AccessTypeOptions: {
          1 : 'Chia sẻ',
          2 : 'Công khai',
          3 : 'Riêng tư'
        },
      }
    },
    created() {
    },
    mounted() {
    },
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
    filters: {},
    props: ['title', 'value', 'name', 'api', 'table', 'Task', 'isDetail', 'per', 'EmployeeOption', 'CompanyOption', 'FixedAssetPerEmployee'],
  }
</script>
<style>

  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }
</style>
