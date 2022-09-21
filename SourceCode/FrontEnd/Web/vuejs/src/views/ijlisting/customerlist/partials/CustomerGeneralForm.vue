<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-lg-3 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tên</div>
      <div class="col-lg-17 col-lg-21 col-md-20 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 app-object-name">
        <input v-model="value.CustomerName" type="text" class="form-control" placeholder="Tên nhà cung cấp"/>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-8 app-object-code d-md-none d-sm-none d-none d-lg-flex align-items-center">
        <span>Mã số</span>
        <input type="text" v-model="value.CustomerNo" class="form-control" placeholder="Mã số"/>
      </div>

      <div class="d-lg-none col-lg-3 col-md-4 col-sm-4">Mã số</div>
      <div class="d-lg-none col-lg-9 col-md-8 col-sm-8">
        <input type="text" v-model="value.CustomerNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" for="Address">Địa chỉ GD</label>
      <div class="col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        <input class="form-control" v-model="value.Address" id="Address" placeholder="Địa chỉ giao dịch" name="Address"/>
      </div>

      <label class="col-lg-3 col-md-4 col-sm-4 m-0" for="BillTo" title="Địa chỉ nhận hóa đơn">Địa chỉ NHĐ</label>
      <div class="col-lg-9 col-md-20 col-sm-20">
        <input class="form-control" v-model="value.BillTo" id="BillTo" placeholder="Địa chỉ nhận hóa đơn" name="BillTo"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" for="Address">Địa chỉ NH</label>
      <div class="col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        <input class="form-control" v-model="value.ShipTo" id="ShipTo" placeholder="Địa chỉ nhận hàng" name="ShipTo"/>
      </div>
      <label class="col-lg-3 col-md-4 col-sm-4 m-0" for="TaxCode" title="Mã số thuế">Mã số thuế</label>
      <div class="col-lg-9 col-md-20 col-sm-20">
        <input class="form-control" v-model="value.TaxCode" id="TaxCode" placeholder="Mã số thuế" name="TaxCode"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" for="BankAccount" title="Tài khoản ngân hàng">Tài khoản NH</label>
      <div class="col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        <input class="form-control" v-model="value.BankAccount" id="BankAccount" placeholder="Tài khoản ngân hàng" name="BankAccount"/>
      </div>
      <label class="col-lg-3 col-md-4 col-sm-4 m-0" for="BankName" title="Địa chỉ ngân hàng">Địa chỉ NH</label>
      <div class="col-lg-9 col-md-20 col-sm-20">
        <input class="form-control" v-model="value.BankAccount" id="BankName" placeholder="Địa chỉ ngân hàng" name="BankName"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4 mb-lg-0 mb-md-3 mb-sm-3" title="Loại nhà cung cấp">Loại NCC</div>
      <div class="col-lg-9 col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        <customer-modal-search-input-vcatelist
          v-model="value.CustomerCate"
          tableApi="customer_cate_list"
          refModal="myModalSearchVcatelist"
          id-modal="myModalSearchVcatelist"
          placeholder="Loại nhà cung cấp"
          title-modal="Loại nhà cung cấp" size-modal="lg"></customer-modal-search-input-vcatelist>
      </div>
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4" title="Loại nhà cung cấp">Quyền truy cập</div>
      <div class="col-lg-9 col-lg-9 col-md-20 col-sm-20">
        <b-form-select v-model="value.AccessType" :options="AccessTypeOptions" id="item-uom"></b-form-select>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3">Tỉnh</label>
      <div class="col-md-5 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        <ijcore-modal-search-input
          v-model="value.Province"
          :select-fields-api="[
                              {field: 'ProvinceID',fieldForSelected: 'id', showInTable: false, key: 'ProvinceID'},
                              {field: 'ProvinceName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'ProvinceName', sortable: true, thClass: 'd-none'}
                            ]"
          :search-fields-api="[{field: 'ProvinceName', placeholder: 'Nhập tên', name: 'ProvinceName', class: '', style: ''}]"
          table="province"
          ref="myModalSearchInputProvince"
          id-modal="myModalSearchInputProvince"
          :item-per-page="8"
          placeholder="Tỉnh"
          :url-api="$store.state.appRootApi + '/listing/api/common/get-province'"
          name-input="input-province"
          title-modal="Tỉnh" size-modal="lg">
        </ijcore-modal-search-input>
      </div>

      <label class="col-lg-2 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3">Huyện</label>
      <div class="col-lg-5 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        <ijcore-modal-search-input
          v-model="value.District"
          :select-fields-api="[
                              {field: 'DistrictID',fieldForSelected: 'id', showInTable: false, key: 'DistrictID'},
                              {field: 'DistrictName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'DistrictName', sortable: true, thClass: 'd-none'}
                            ]"
          :search-fields-api="[{field: 'DistrictName', placeholder: 'Nhập tên', name: 'DistrictName', class: '', style: ''}]"
          table="district"
          ref="myModalSearchInputDistrict"
          id-modal="myModalSearchInputDistrict"
          :item-per-page="8"
          placeholder="Huyện"
          :request-data="{ProvinceID: (value.Province) ? value.Province.ProvinceID : null}"
          :url-api="$store.state.appRootApi + '/listing/api/common/get-district'"
          name-input="input-district"
          title-modal="Huyện" size-modal="lg">
        </ijcore-modal-search-input>
      </div>

      <label class="col-lg-2 col-md-4 col-sm-4 m-0">Xã</label>
      <div class="col-lg-5 col-md-20 col-sm-20">
        <ijcore-modal-search-input
          v-model="value.Commune"
          :select-fields-api="[
                              {field: 'CommuneID',fieldForSelected: 'id', showInTable: false, key: 'CommuneID'},
                              {field: 'CommuneName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'CommuneName', sortable: true, thClass: 'd-none'}
                            ]"
          :search-fields-api="[{field: 'CommuneName', placeholder: 'Nhập tên', name: 'CommuneName', class: '', style: ''}]"
          table="commune"
          ref="myModalSearchInputCommune"
          id-modal="myModalSearchInputCommune"
          :item-per-page="8"
          placeholder="Xã"
          :request-data="{
                              ProvinceID: (value.Province) ? value.Province.ProvinceID : null,
                              DistrictID: (value.District) ? value.District.DistrictID : null
                            }"
          :url-api="$store.state.appRootApi + '/listing/api/common/get-commune'"
          name-input="input-commune"
          title-modal="Xã" size-modal="lg">
        </ijcore-modal-search-input>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" for="OfficePhone">ĐTĐV</label>
      <div class="col-lg-5 col-md-20 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0 mb-lg-0 mb-md-3 mb-sm-3">
        <input type="number" v-model="value.OfficePhone" id="OfficePhone" class="form-control" placeholder="Số điện thoại" name="OfficePhone" />
      </div>

      <label class="col-lg-2 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" for="Fax">Số Fax</label>
      <div class="col-lg-5 col-md-20 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0 mb-lg-0 mb-md-3 mb-sm-3">
        <input v-model="value.Fax" type="number" id="Fax" class="form-control" placeholder="Số Fax" name="Fax" />
      </div>

      <label class="col-lg-2 col-md-4 col-sm-4 m-0">Email</label>
      <div class="col-lg-7 col-md-20 col-sm-20">
        <input v-model="value.Email" type="Email" id="Email" class="form-control" placeholder="Email" name="Email" />
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-0 mb-sm-3" for="Website">Website</label>
      <div class="col-md-12 col-sm-20 mb-lg-0 mb-md-0 mb-sm-3 mb-3">
        <input v-model="value.Website" type="text" id="Website" class="form-control" placeholder="Website" name="Website" />
      </div>
      <label class="col-lg-2 col-md-4 col-sm-4 m-0" title="Là khách hàng">Là KH</label>
      <div class="col-lg-5 col-md-3 col-sm-20">
        <b-form-checkbox v-model="value.isCustomer"></b-form-checkbox>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-md-4 col-md-4 col-sm-4 m-0" for="Note">Ghi chú</label>
      <div class="col-lg-21 col-md-20 col-sm-20">
        <textarea v-model="value.Note" class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note"></textarea>
      </div>
    </div>
  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
  import Select2 from 'v-select2-component';
  import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
  import CustomerModalSearchInputVcatelist from "./CustomerModalSearchInputVcatelist";

  export default {
    name: 'customer-general-form',
    components: {IjcoreDatePicker, Select2, IjcoreModalSearchInput, CustomerModalSearchInputVcatelist},
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
    props: ['title', 'value', 'name', 'api', 'table', 'Task', 'isDetail', 'per', 'EmployeeOption', 'CompanyOption', 'CustomerPerEmployee'],
  }
</script>
<style>
  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }

</style>
