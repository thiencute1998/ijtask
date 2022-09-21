<template>
  <div>
    <div class="form-group row mr-bottom-5">
      <label class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên</label>
      <div class="col-lg-20 col-md-20 col-sm-20 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.DocName|perView(DocPerEmployee, 'DocName')}}
      </div>
    </div>
    <div class="form-group row mr-bottom-5">
      <label class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tài liệu cha</label>
      <div class="col-lg-20 col-md-20 col-sm-20 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.ParentName|perView(DocPerEmployee, 'ParentName')}}
      </div>
    </div>
    <div class="form-group row mr-bottom-5" v-if="isDetail">
      <label class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Đơn vị ban hành</label>
      <div class="col-lg-20 col-md-20 col-sm-20 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.CompanyIssued|perView(DocPerEmployee, 'CompanyIssued')}}
      </div>
    </div>
    <div class="form-group row mr-bottom-5" v-if="isDetail">
      <label class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Ngày ký</label>
      <div class="col-lg-4 col-md-4 col-sm-4 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.DocDate|perView(DocPerEmployee, 'DocDate')}}
      </div>
      <label class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Ngày hiệu lực</label>
      <div class="col-lg-4 col-md-4 col-sm-4 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.EffectiveDate|perView(DocPerEmployee, 'EffectiveDate')}}
      </div>
      <label class="col-lg-2 col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-2 mb-2" style="white-space: nowrap">Người ký</label>
      <div class="col-lg-6 col-md-6 col-sm-6 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.SignerName|perView(DocPerEmployee, 'SignerName')}}
      </div>
    </div>
    <div class="form-group row mr-bottom-5">
      <label class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap" title="Người phân quyền">Người phân quyền</label>
      <div class="col-lg-2">
        {{value.EmployeeName|perView(DocPerEmployee, 'EmployeeName')}}
      </div>
      <label class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Quyền truy cập</label>
      <div class="col-lg-2">
        {{OptionAccessType[value.AccessType]|perView(DocPerEmployee, 'AccessType')}}
      </div>
      <div class="col-lg-8" v-if="value.AccessType == 2">
        {{value.PublicCompanyName|perView(DocPerEmployee, 'PublicCompanyName')}}
      </div>
    </div>

  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';

  export default {
    name: 'DocGeneralView',
    mixins: [mixinLists],
    components: {},
    computed: {},
    data() {
      return {
        OptionAccessType: {
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
    props: ['title', 'value', 'name', 'api', 'table', 'Task', 'isDetail', 'per', 'EmployeeOption', 'CompanyOption', 'DocPerEmployee'],
  }
</script>
<style>
  .mr-bottom-3 {
    margin-bottom: 3px !important;
  }

  .readonly {
    background-color: #fff !important;
  }

  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }

  .modal-footer ol, .modal-footer ul, .modal-footer dl {
    margin-bottom: 0px;
  }

  #modal-form-input-task-general-content .modal-lg .modal-content {
    width: 1024px;
    margin: auto;
  }

  @media (max-width: 1024px) {
    #modal-form-input-task-general-content .modal-lg {
      max-width: 100%;
    }

    #modal-form-input-task-general-content .modal-lg .modal-content {
      width: 90%;
      margin: auto;
    }
  }

  @media (min-width: 992px) {
    #modal-form-input-task-general-content .modal-lg {
      max-width: 100%;
    }
  }
  .width-datepicker-auto .mx-datepicker{
    width: auto;
  }
  .width-select2-auto .select2-container{
    width: 100% !important;
  }
</style>
