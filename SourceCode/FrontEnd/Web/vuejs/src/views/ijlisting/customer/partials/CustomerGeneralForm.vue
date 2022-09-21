<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.CustomerName" type="text" id="CustomerName" class="form-control" placeholder="Tên khách hàng" name="CustomerName"/>
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.CustomerNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Loại khách hàng</label>
      <div class="col-md-21">
        <customer-modal-search-input-catelist
          v-model="value.CustomerCate"
          title-modal="Loại khách hàng"
          placeholder="Loại khách hàng"
        ></customer-modal-search-input-catelist>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Địa chỉ GD</label>
      <div class="col-md-9 mb-3 mb-sm-0">
        <input v-model="value.Address" type="text" class="form-control" placeholder="Địa chỉ giao dịch">
      </div>
      <label class="col-md-3 m-0">Địa chỉ nhận HĐ</label>
      <div class="col-md-9">
        <input v-model="value.BillTo" type="text" class="form-control" placeholder="Địa chỉ hợp đồng">
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">ĐC nhận hàng</label>
      <div class="col-md-9 mb-3 mb-sm-0">
        <input v-model="value.ShipTo" type="text" class="form-control" placeholder="Địa chỉ nhận hàng">
      </div>
      <label class="col-md-3 m-0">Mã số thuế</label>
      <div class="col-md-9">
        <input v-model="value.TaxCode" type="text" class="form-control" placeholder="Mã số thuế">
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Tài khoản NH</label>
      <div class="col-md-9 mb-3 mb-sm-0">
        <input v-model="value.BankAccount" type="text" class="form-control" placeholder="Tài khoản ngân hàng">
      </div>
      <label class="col-md-3 m-0">Địa chỉ NH</label>
      <div class="col-md-9">
        <input v-model="value.BankName" type="text" class="form-control" placeholder="Địa chỉ ngân hàng">
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">ĐTCQ</label>
      <div class="col-md-5 mb-3 mb-sm-0">
        <input v-model="value.OfficePhone" type="text" class="form-control" placeholder="Điện thoại cơ quan">
      </div>
      <label class="col-md-2 m-0">Fax</label>
      <div class="col-md-5">
        <input v-model="value.Fax" type="text" class="form-control" placeholder="Số Fax">
      </div>
      <label class="col-md-2 m-0">Email</label>
      <div class="col-md-7">
        <input v-model="value.Email" type="text" class="form-control" placeholder="Địa chỉ Email">
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Tỉnh</label>
      <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
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
      <label class="col-md-2 m-0">Huyện</label>
      <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
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
      <label class="col-md-2 m-0">Xã</label>
      <div class="col-md-7 ">
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
      <label class="col-md-3 m-0">Quyền truy cập</label>
      <div class="col-md-5">
        <b-form-select v-model="value.AccessType" :options="AccessTypeOptions" id="item-uom">
        </b-form-select>
      </div>
      <label class="col-md-2 col-sm-4 m-0" title="Là nhà cung cấp">Là NCC</label>
      <div class="col-md-5 col-sm-20">
        <b-form-checkbox v-model="value.isVendor"></b-form-checkbox>
      </div>
      <label class="col-md-2 m-0" for="Website">Website</label>
      <div class="col-md-7 m-0">
        <input v-model="value.Website" type="text" id="Website" class="form-control" placeholder="Website" name="Website" />
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3" for="Note">Ghi chú</label>
      <div class="col-md-21">
        <textarea v-model="value.Note" class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note"></textarea>
      </div>
    </div>
  </div>
</template>
<script>
import Select2 from "v-select2-component";
import Swal from "sweetalert2";
import IjcoreModalListing from "@/components/IjcoreModalListing";
import IjcoreModalParent from "@/components/IjcoreModalParent";
import ApiService from "@/services/api.service";
import CustomerModalSearchInputCatelist from "./CustomerModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";

export default {
  name: 'CustomerGeneralForm',
  props: ['value','customerOptions','employeeOptions'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    CustomerModalSearchInputCatelist
  },
  data(){
    return{
      AccessTypeOptions:{
        1: 'Chia sẻ',
        2: 'Công khai',
        3: 'Riêng tư'
      },
    }
  },
  methods: {
  },
  watch: {

  }
}
</script>
