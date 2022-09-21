<template>
  <div>
    <div class="form-group row">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên: </div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.CompanyName}}
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số: </span>
        {{value.CompanyNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Là mục con của: </label>
      <label class="col-md-15 m-0">{{value.ParentName}}</label>
      <div v-if="value.ParentID" class="col-md-6 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số: </span>
        {{value.ParentNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Loại đơn vị: </label>
      <div class="col-md-21 m-0">
        <span v-for="(companyCate, key) in value.CompanyCate">
          {{companyCate.CateName}}: {{companyCate.Description}} <span v-if="key < (value.CompanyCate.length - 1)"> , </span>
        </span>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3">Quyền truy cập: </label>
      <div class="col-md-9">
        {{AccessTypeOptions[value.AccessType]}}
      </div>
      <label class="col-md-3 m-0">Chương: </label>
      <div class="col-md-9 m-0">
        {{value.SbiChapterNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Người liên hệ: </label>
      <label class="col-md-9 m-0">{{value.ContactName}}</label>
      <label class="col-md-3">ĐTCN: </label>
      <label class="col-md-9">{{value.ContactTel}}</label>
    </div>
    <div class="form-group row" v-if="isDetail">
      <label class="col-md-3">Cấp quản lý: </label>
      <div class="col-md-9">
        {{value.ManagementLevel ? ManagementLevelOptions[value.ManagementLevel] : ''}}
      </div>
      <label class="col-md-3 m-0">Trung ương: </label>
      <div class="col-md-9 m-0">
        {{value.CenterName}}
      </div>
    </div>
    <div class="form-group row" v-if="isDetail && value.ManagementLevel >= 2">
      <label class="col-md-3 m-0">Tỉnh: </label>
      <label class="col-md-5 m-0">{{value.Province.ProvinceName}}</label>

      <label class="col-md-2 m-0" v-if="isDetail && value.ManagementLevel >= 3">Huyện: </label>
      <label class="col-md-5 m-0" v-if="isDetail && value.ManagementLevel >= 3">{{value.District.DistrictName}}</label>

      <label class="col-md-2 m-0" v-if="isDetail && value.ManagementLevel >= 4">Xã: </label>
      <label class="col-md-7 m-0" v-if="isDetail && value.ManagementLevel >= 4">{{value.Commune.CommuneName}}</label>
    </div>
    <div class="form-group row" v-if="isDetail">
      <label class="col-md-3 m-0">ĐTĐV: </label>
      <label class="col-md-5 m-0">{{value.Tel}}</label>
      <label class="col-md-2 m-0">Số Fax: </label>
      <div class="col-md-5 m-0">
        {{value.Fax}}
      </div>
      <label class="col-md-2 m-0">Email: </label>
      <label class="col-md-7 m-0">
        {{value.Email}}
      </label>
    </div>

    <div class="form-group row" v-if="isDetail">
      <label class="col-md-3 m-0">Địa chỉ: </label>
      <label class="col-md-12 m-0">{{value.Address}}</label>
      <label class="col-md-2" title="Loại tổng hợp dữ liệu">THDL: </label>
      <div class="col-md-7">
        <span>{{value.SumCompanyType ? SumCompanyTypeOptions[value.SumCompanyType]: ''}}</span>
      </div>
    </div>
    <div class="form-group row" v-if="isDetail">
      <label class="col-md-4 m-0">Ghi chú: </label>
      <label class="col-md-20 m-0" id="view-Note">
        {{value.Note}}
      </label>
    </div>
    <div class="form-group row" v-if="isDetail">
      <div class="col-md-8">
        <b-form-checkbox v-model="value.isAutOrg" disabled>Là cơ quan chủ quản</b-form-checkbox>
      </div>
      <div class="col-md-8">
        <b-form-checkbox v-model="value.isFinOrg" disabled>Là cơ tài chính</b-form-checkbox>
      </div>
      <div class="col-md-8">
        <b-form-checkbox v-model="value.isTreOrg" disabled>Là kho bạc nhà nước </b-form-checkbox>
      </div>
    </div>
    <div class="form-group row ij-line-head" v-if="isDetail && value.isAutOrg && value.ManagementLevel > 1">
      <div class="col-md-24">
        <span>Cơ quan chủ quản</span>
      </div>
    </div>
    <div class="form-group row mt-3" v-if="isDetail && value.isAutOrg && value.ManagementLevel > 1">
      <label class="col-md-3">Tên đơn vị</label>
      <div class="col-md-15">
        <span>{{value.AutOrgName}}</span>
      </div>
      <div class="col-md-6">
        <span>Mã số: </span>
        {{value.AutOrgNo}}
      </div>
    </div>
    <div class="form-group row " v-if="isDetail && value.isAutOrg && value.ManagementLevel > 1">
      <label class="col-md-3">Địa chỉ</label>
      <div class="col-md-15">
        {{value.AutOrgAddress}}
      </div>
      <div class="col-md-6">
        <span>Mã chương: </span>
        {{value.AutOrgChapterNo}}
      </div>
    </div>
    <div class="form-group row " v-if="isDetail && value.isAutOrg && value.ManagementLevel > 1">
      <label class="col-md-3">Người liên hệ</label>
      <div class="col-md-9">
        <span>{{value.AutOrgContactName}}</span>
      </div>
      <label class="col-md-3">Chức vụ</label>
      <div class="col-md-9">
        <span>{{value.AutOrgContactPosition}}</span>
      </div>
    </div>
    <div class="form-group row " v-if="isDetail && value.isAutOrg && value.ManagementLevel > 1">
      <label class="col-md-3" title="Điện thoại cơ quan">ĐTCQ</label>
      <div class="col-md-9">
        <span>{{value.AutOrgContactOfficePhone}}</span>
      </div>
      <label class="col-md-3" title="Điện thoại di dộng">ĐTDĐ</label>
      <div class="col-md-9">
        <span>{{value.AutOrgContactHandPhone}}</span>
      </div>
    </div>
    <div class="form-group row " v-if="isDetail && value.isAutOrg && value.ManagementLevel > 1">
      <label class="col-md-3">Email</label>
      <div class="col-md-21">
        <span>{{value.AutOrgContactMail}}</span>
      </div>
    </div>
    <div class="form-group row ij-line-head" v-if="isDetail && value.isFinOrg && value.ManagementLevel > 1">
      <div class="col-md-3">Tên đơn vị</div>
      <div class="col-md-15">
        <span>{{Fin.FinName}}}</span>
      </div>
      <div class="col-md-6">
        <span>Mã số: </span>
        {{Fin.FinNo}}
      </div>
    </div>
    <div class="form-group row ij-line-head" v-if="isDetail && value.isTreOrg && value.ManagementLevel > 1">
        <div class="col-md-3">Tên đơn vị: </div>
        <div class="col-md-15">
          <span>{{Tre.TreName}}}</span>
        </div>
        <div class="col-md-6">
          <span>Mã số: </span>
          {{Tre.TreNo}}
        </div>
    </div>
  </div>
</template>
<script>
  export default {
    name: 'CompanyGeneralView',
    props: ['value','isDetail', 'Fin', 'Tre'],
    data(){
      return{
        AccessTypeOptions: {
          1 : 'Chia sẻ',
          2 : 'Công khai',
          3 : 'Riêng tư'
        },
        ManagementLevelOptions: {
          1: 'Trung ương',
          2: 'Tỉnh',
          3: 'Huyện',
          4: 'Xã'
        },
        SumCompanyTypeOptions: {
          1: 'Không tổng hợp dữ liệu',
          2: 'Tổng hợp dữ liệu theo cấp chính quyền',
          3: 'Tổng hợp dữ liệu theo ngành'
        },
      }
    },
    methods:{
    },
    mounted() {
    }
  }
</script>
