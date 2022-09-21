<template>
  <div>
    <div class="form-group row">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên: </div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.CustomerName}}
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số: </span>
        {{value.CustomerNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Loại khách hàng: </label>
      <div class="col-md-21 m-0">
        <span v-for="(customerCate, key) in value.CustomerCate">
          {{customerCate.CateName}}: {{customerCate.Description}} <span v-if="key < (value.CustomerCate.length - 1)"> , </span>
        </span>
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0">Địa chỉ GD: </label>
      <div class="col-md-9 mb-3 mb-sm-0">
        {{value.Address}}
      </div>
      <label class="col-md-3 m-0">Địa chỉ nhận HĐ: </label>
      <div class="col-md-9">
        {{value.BillTo}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0">ĐC nhận hàng: </label>
      <div class="col-md-9 mb-3 mb-sm-0">
        {{value.ShipTo}}
      </div>
      <label class="col-md-3 m-0">Mã số thuế: </label>
      <div class="col-md-9">
        {{value.TaxCode}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0">Tài khoản NH: </label>
      <div class="col-md-9 mb-3 mb-sm-0">
        {{value.BankAccount}}
      </div>
      <label class="col-md-3 m-0">Địa chỉ NH: </label>
      <div class="col-md-9">
        {{value.BankName}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0">ĐTCQ: </label>
      <div class="col-md-5 mb-3 mb-sm-0">
        {{value.OfficePhone}}
      </div>
      <label class="col-md-2 m-0">Fax: </label>
      <div class="col-md-5">
        {{value.Fax}}
      </div>
      <label class="col-md-2 m-0">Email: </label>
      <div class="col-md-7">
        {{value.Email}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0">Tỉnh: </label>
      <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
        {{value.Province.ProvinceName}}
      </div>
      <label class="col-md-2 m-0">Huyện: </label>
      <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
        {{value.District.DistrictName}}
      </div>
      <label class="col-md-2 m-0">Xã: </label>
      <div class="col-md-7 ">
        {{value.Commune.CommuneName}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3">Quyền truy cập: </label>
      <div class="col-md-5 m-0">
        {{AccessTypeOptions[value.AccessType]}}
      </div>
      <label class="col-md-2 col-sm-4 m-0" title="Là nhà cung cấp">Là NCC: </label>
      <div class="col-md-5 col-sm-20">
        <b-form-checkbox id="isVendor" v-model="value.isVendor" disabled></b-form-checkbox>
      </div>
      <label class="col-md-2 m-0">Website: </label>
      <div class="col-md-7 m-0">
        {{value.Website}}
      </div>
    </div>
    <div class="form-group row" v-if="isDetail">
      <label class="col-md-4 m-0">Ghi chú: </label>
      <label class="col-md-20 m-0" id="view-Note">
        {{value.Note}}
      </label>
    </div>

  </div>
</template>
<script>
  export default {
    name: 'CustomerGeneralView',
    props: ['value','isDetail'],
    data(){
      return{
        AccessTypeOptions: {
          1 : 'Chia sẻ',
          2 : 'Công khai',
          3 : 'Riêng tư'
        },
      }
    }
  }
</script>
