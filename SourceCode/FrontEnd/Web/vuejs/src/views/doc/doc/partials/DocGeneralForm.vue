<template>
  <div>
    <div class="form-group row align-items-center mt-2">
      <div class="col-lg-4 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tên</div>
      <div class="col-lg-20 col-md-20 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3" v-if="perEditView(value.TaskName, DocPerEmployee, 'TaskName')">
        <input v-model="value.DocName" type="text" class="form-control" placeholder="Tên tài liệu" v-if="perEditField(value.DocName, DocPerEmployee, 'DocName')"/>
        <input type="text" v-else disabled="true" class="form-control" :value="value.DocName"
               placeholder=""/>
      </div>
      <div class="col-lg-20 col-md-20 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3" v-else>
        <input type="text" disabled class="form-control"
               placeholder=""/>
      </div>
    </div>

    <div class="form-group row align-items-center">
      <div class="col-lg-4 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tài liệu cha</div>
      <div class="col-lg-20 col-md-20 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3" v-if="perEditView(value.ParentName, DocPerEmployee, 'ParentName')">
        <IjcoreModalDocSearch  v-model="value" :title="'tài liệu'" :api="'/doc/api/doc/get-list'" v-if="perEditField(value.ParentName, DocPerEmployee, 'ParentName')" :table="'doc'" :FieldID="'DocID'" :FieldName="'DocName'"
        ></IjcoreModalDocSearch>
        <input type="text" v-else disabled="true" class="form-control" :value="value.ParentName" placeholder=""/>
      </div>
      <div class="col-lg-20 col-md-20 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3" v-else>
        <input type="text" disabled class="form-control" placeholder=""/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <div class="col-lg-4 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap" title="Đơn vị ban hành">Đơn vị ban hành</div>
      <div class="col-lg-20 col-md-20 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3" v-if="perEditView(value.CompanyIssued, DocPerEmployee, 'CompanyIssued')">
        <input v-model="value.CompanyIssued" type="text" class="form-control" placeholder="Đơn vị ban hành" v-if="perEditField(value.CompanyIssued, DocPerEmployee, 'CompanyIssued')"/>
        <input type="text" v-else disabled="true" class="form-control" :value="value.CompanyIssued" placeholder=""/>
      </div>
      <div class="col-lg-20 col-md-20 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3" v-else>
        <input type="text" disabled class="form-control" placeholder=""/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <div class="col-lg-4 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Ngày ký</div>
      <div class="col-lg-4 col-md-22 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 width-datepicker-auto" v-if="perEditView(value.DocDate, DocPerEmployee, 'DocDate')">
        <IjcoreDatePicker v-model="value.DocDate" v-if="perEditField(value.DocDate, DocPerEmployee, 'DocDate')">
        </IjcoreDatePicker>
        <input type="text" v-else disabled="true" class="form-control" :value="value.DocDate" placeholder=""/>
      </div>
      <div class="col-lg-4 col-md-22 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3" v-else>
        <input type="text" disabled class="form-control" placeholder=""/>
      </div>
      <div class="col-lg-4 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Ngày hiệu lực</div>
      <div class="col-lg-4 col-md-22 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 width-datepicker-auto" v-if="perEditView(value.EffectiveDate, DocPerEmployee, 'EffectiveDate')">
        <IjcoreDatePicker v-model="value.EffectiveDate" v-if="perEditField(value.EffectiveDate, DocPerEmployee, 'EffectiveDate')">
        </IjcoreDatePicker>
        <input type="text" v-else disabled="true" class="form-control" :value="value.EffectiveDate" placeholder=""/>
      </div>
      <div class="col-lg-4 col-md-22 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3" v-else>
        <input type="text" disabled class="form-control"
               placeholder=""/>
      </div>
      <div class="col-lg-2 col-md-2 mb-md-0 col-sm-4 mb-sm-0 mb-3" style="white-space: nowrap">Người ký</div>
      <div class="col-lg-6 col-md-6 col-sm-6 mb-sm-4 mb-md-3 mb-lg-0 mb-3" v-if="perEditView(value.SignerName, DocPerEmployee, 'SignerName')">
        <input v-model="value.SignerName" type="text" class="form-control" placeholder="Họ và tên người ký" v-if="perEditField(value.SignerName, DocPerEmployee, 'SignerName')"/>
        <input type="text" v-else disabled="true" class="form-control" :value="value.SignerName" placeholder=""/>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6 mb-sm-4 mb-md-3 mb-lg-0 mb-3" v-else>
        <input type="text" disabled class="form-control" placeholder=""/>
      </div>
    </div>
    <div class="form-group row align-items-center mb-2">
      <label class="col-md-4 col-sm-4 m-0 mb-3" for="per-permission" title="Người phân quyền">Người phân quyền</label>
      <div class="col-md-4 col-sm-4 width-select2-auto mb-3" v-if="perEditView(value.AuthorizedPerson, DocPerEmployee, 'AuthorizedPerson')">
        <Select2 v-model="value.AuthorizedPerson" :settings="{allowClear: true, placeholder: {id: null, text: 'Chọn nhân viên'}}" :options="EmployeeOption"  v-if="perEditField(value.AuthorizedPerson, DocPerEmployee, 'AuthorizedPerson')"></Select2>
        <input type="text" v-else disabled="true" id="per-permission" class="form-control" :value="value.AuthorizedPerson" placeholder=""/>
      </div>
      <div class="col-md-4 col-sm-4 mb-3" v-else>
        <input type="text" disabled class="form-control" placeholder=""/>
      </div>
      <label class="col-md-4 col-sm-4 m-0 mb-3">Quyền truy cập</label>
      <div class="col-md-4 col-sm-4 mb-3" v-if="perEditView(value.AccessType, DocPerEmployee, 'AccessType')">
        <b-form-select v-model="value.AccessType" :options="AccessTypeOptions" v-if="perEditField(value.AccessType, DocPerEmployee, 'AccessType')"></b-form-select>
        <input type="text" v-else disabled="true" class="form-control" :value="value.AccessType" placeholder=""/>
      </div>
      <div class="col-md-4 col-sm-4 mb-3" v-else>
        <input type="text" disabled class="form-control" placeholder=""/>
      </div>
      <div class="col-md-8 col-sm-8 width-select2-auto mb-3" v-if="value.AccessType==2&&perEditView(value.PublicCompanyID, DocPerEmployee, 'PublicCompanyID')">
        <Select2 v-model="value.PublicCompanyID" multiple="true"  v-if="perEditField(value.PublicCompanyID, DocPerEmployee, 'PublicCompanyID')" :settings="{multiple: true, allowClear: true, placeholder: {id: null, text: 'Chọn đơn vị'}}" :options="CompanyOption"></Select2>
        <input type="text" v-else disabled="true" class="form-control" :value="value.PublicCompanyID" placeholder=""/>
      </div>
      <div class="col-md-8 col-sm-8 mb-3" v-else>
        <input type="text" disabled class="form-control" placeholder=""/>
      </div>
    </div>

  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';
  import IjcoreModalDocSearch from "../../../../components/IjcoreModalDocSearch";
  import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
  import Select2 from 'v-select2-component';

  export default {
    name: 'DocGeneralForm',
    mixins: [mixinLists],
    components: {IjcoreDatePicker, IjcoreModalDocSearch, Select2},
    computed: {},
    data() {
      return {
        AccessTypeOptions:{
          1: 'Chia sẻ',
          2: 'Công khai',
          3: 'Riêng tư'
        },
      }
    },
    created() {},
    mounted() {},
    methods: {
      perEditView(value, DocPerEmployee, field) {
        let AccessField = ',' + DocPerEmployee['AccessField'] + ',';
        if(DocPerEmployee['AccessField'] == 'all' || AccessField.includes(',' + field + ',')){
          return true;
        }else{
          return false;
        }
      },
      perEditField(value, DocPerEmployee, field){
        let EditField = ',' + DocPerEmployee['EditField'] + ',';
        if(DocPerEmployee['EditField'] == 'all' || EditField.includes(',' + field + ',')){
          return true;
        }else{
          return false;
        }
      },
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
</style>
