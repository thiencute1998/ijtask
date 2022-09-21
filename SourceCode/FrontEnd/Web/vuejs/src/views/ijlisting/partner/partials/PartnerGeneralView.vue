<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-md-3 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tên</div>
      <div class="col-lg-17 col-md-21 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 app-object-name">
        {{value.PartnerName}}
      </div>
      <div class="col-lg-4 app-object-code d-md-none d-sm-none d-none d-lg-flex align-items-center">
        <span>Mã số</span>
        {{value.PartnerNo}}
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3">Địa chỉ TC</label>
      <div class="col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        {{value.PartnerAddress}}
      </div>

    </div>
    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4 mb-lg-0 mb-md-3 mb-sm-3" title="Loại nhà cung cấp">Loại đối tác</div>
      <div class="col-lg-9 col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        <span v-for="(PartnerCate, key) in value.PartnerCate" v-if="value.PartnerCate.length">
            {{PartnerCate.CateName}}: {{PartnerCate.Description}} <span v-if="key < (value.PartnerCate.length - 1)">,</span>
          </span>
      </div>
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4" title="Loại nhà cung cấp">Quyền TC</div>
      <div class="col-lg-9 col-lg-9 col-md-20 col-sm-20">
        <span>{{AccessTypeOptions[value.AccessType]}}</span>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" title="Số Điện Thoại">Số điện thoại</label>
      <div class="col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        {{value.Tel}}
      </div>
      <label class="col-lg-3 col-md-4 col-sm-4 m-0" title="Email">Email</label>
      <div class="col-lg-9 col-md-20 col-sm-20">
        {{value.Email}}
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
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" title="Nhân viên">Nhân viên</label>
      <div class="col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        {{value.FullName}}
      </div>
      <label class="col-lg-3 col-md-4 col-sm-4 m-0" title="Chứng minh nhân dân/Căn cước công dân">CMND/CCCD</label>
      <div class="col-lg-9 col-md-20 col-sm-20">
        {{value.PartnerIdNo}}
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" title="Quốc Tịch">Quốc tịch</label>
      <div class="col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        {{value.Nationality}}
      </div>
      <label class="col-lg-3 col-md-4 col-sm-4 m-0" title="Quê quán">Quê quán</label>
      <div class="col-lg-9 col-md-20 col-sm-20">
        {{value.NativeCountry}}
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-lg-3 col-md-4 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" title="Địa chỉ thường chú">Thường Trú</label>
      <div class="col-lg-9 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        {{value.PermanceAddress}}
      </div>
      <label class="col-lg-3 col-md-4 col-sm-4 m-0" title="Quê quán">ĐC Cư trú</label>
      <div class="col-lg-9 col-md-20 col-sm-20">
        {{value.ResidenceAddress}}
      </div>
    </div>

    <div class="form-group row ">
      <label class="col-lg-3 col-md-4 col-md-4 col-sm-4 m-0">Ghi chú</label>
      <div class="col-lg-21 col-md-20 col-sm-20">{{value.Note}}</div>
    </div>
  </div>

</template>

<script>
  import ApiService from '@/services/api.service';

  export default {
    name: 'partner-general-view',
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
    props: ['title', 'value', 'name', 'api', 'table', 'Task', 'isDetail', 'per', 'EmployeeOption', 'CompanyOption', 'PartnerPerEmployee'],
  }
</script>
<style>

  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }
</style>
