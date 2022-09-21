<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-md-3 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tên</div>
      <div class="col-lg-17 col-md-21 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 app-object-name">
        {{value.VendorName}}
      </div>
      <div class="col-lg-4 app-object-code d-md-none d-sm-none d-none d-lg-flex align-items-center">
        <span>Mã số</span>
        {{value.VendorNo}}
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" title="Địa chỉ giao hàng">Địa chỉ GD</label>
      <div class="col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        {{value.Address}}
      </div>

      <label class="col-lg-3 col-md-4 col-sm-4 m-0" title="Địa chỉ nhận hóa đơn">Địa chỉ NHĐ</label>
      <div class="col-lg-9 col-md-20 col-sm-20">
        {{value.BillTo}}
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" title="Địa chỉ nhận hàng">Địa chỉ NH</label>
      <div class="col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        {{value.ShipTo}}
      </div>
      <label class="col-lg-3 col-md-4 col-sm-4 m-0" title="Mã số thuế">Mã số thuế</label>
      <div class="col-lg-9 col-md-20 col-sm-20">
        {{value.TaxCode}}
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" title="Tài khoản ngân hàng">Tài khoản NH</label>
      <div class="col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        {{value.BankAccount}}
      </div>
      <label class="col-lg-3 col-md-4 col-sm-4 m-0" title="Địa chỉ ngân hàng">Địa chỉ NH</label>
      <div class="col-lg-9 col-md-20 col-sm-20">
        {{value.BankName}}
      </div>
    </div>
    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4 mb-lg-0 mb-md-3 mb-sm-3" title="Loại nhà cung cấp">Loại NCC</div>
      <div class="col-lg-9 col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        <span v-for="(VendorCate, key) in value.VendorCate" v-if="value.VendorCate.length">
            {{VendorCate.CateName}}: {{VendorCate.Description}} <span v-if="key < (value.VendorCate.length - 1)">,</span>
          </span>
      </div>
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4" title="Loại nhà cung cấp">Quyền truy cập</div>
      <div class="col-lg-9 col-lg-9 col-md-20 col-sm-20">
        <span>{{AccessTypeOptions[value.AccessType]}}</span>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3">Tỉnh</label>
      <div class="col-lg-5 col-md-5 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        {{value.ProvinceName}}
      </div>

      <label class="col-lg-2 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3">Huyện</label>
      <div class="col-lg-5 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        {{value.DistrictName}}
      </div>

      <label class="col-lg-2 col-md-4 col-sm-4 m-0">Xã</label>
      <div class="col-lg-5 col-md-20 col-sm-20">
        {{value.CommuneName}}
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" title="Điện thoại đơn vị">ĐTĐV</label>
      <div class="col-lg-5 col-md-20 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0 mb-lg-0 mb-md-3 mb-sm-3">
        {{value.OfficePhone}}
      </div>

      <label class="col-lg-2 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3">Số Fax</label>
      <div class="col-lg-5 col-md-20 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0 mb-lg-0 mb-md-3 mb-sm-3">
        {{value.Fax}}
      </div>

      <label class="col-lg-2 col-md-4 col-sm-4 m-0">Email</label>
      <div class="col-lg-7 col-md-20 col-sm-20">
        {{value.Email}}
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-0 mb-sm-3">Website</label>
      <div class="col-md-12 col-sm-20 mb-lg-0 mb-md-0 mb-sm-3 mb-3">
        {{value.Website}}
      </div>
      <label class="col-lg-2 col-md-4 col-sm-4 m-0" title="Là khách hàng">Là KH</label>
      <div class="col-lg-5 col-md-3 col-sm-20">
        <b-form-checkbox v-model="value.isCustomer" disabled></b-form-checkbox>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-md-4 col-md-4 col-sm-4 m-0">Ghi chú</label>
      <div class="col-lg-21 col-md-20 col-sm-20">{{value.Note}}</div>
    </div>
  </div>

</template>

<script>
  import ApiService from '@/services/api.service';

  export default {
    name: 'vendor-general-view',
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
    props: ['title', 'value', 'name', 'api', 'table', 'Task', 'isDetail', 'per', 'EmployeeOption', 'CompanyOption', 'VendorPerEmployee'],
  }
</script>
<style>

  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }
</style>
