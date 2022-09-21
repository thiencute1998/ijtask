<template>
  <div>
    <div class="form-group row mt-2">
      <div class="col-lg-4 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Đơn vị</div>
      <div class="col-lg-20 col-md-20 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3">
        <Select2 v-model="AccountID" @change="getListEmployee('CoaLdi')" :settings="{multiple: true, allowClear: true, placeholder: {id: null, text: 'Chọn đơn vị'}}" :options="CoaLdiOption"></Select2>
      </div>
    </div>
    <div class="form-group row">
      <div class="col-lg-4 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Nhóm</div>
      <div class="col-lg-20 col-md-20 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3">
        <Select2 v-model="GroupID" @change="getListEmployee('Group')" :settings="{multiple: true, allowClear: true, placeholder: {id: null, text: 'Chọn nhom'}}" :options="GroupOption"></Select2>
      </div>
    </div>

    <div class="table-responsive">
      <table class="not-border mb-2">
        <thead>
        <tr class="text-left">
          <th class="pr-3">Nhân viên</th>
          <th class="pr-3">Xem</th>
          <th class="pr-3">Trường xem</th>
          <th class="pr-3">Sửa</th>
          <th class="pr-3">Trường sửa</th>
          <th class="pr-3">Xóa</th>
          <th class="pr-3">Thêm</th>
          <th class="pr-3"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(field, key) in per" v-if="EmployeeLogin.EmployeeID != field.EmployeeID">
          <td class="pr-3">{{EmployeeOption[field.EmployeeID]}}</td>
          <td class="pr-3 text-center">
            <b-form-checkbox v-model="per[key].Access">
            </b-form-checkbox>
          </td>
          <td class="pr-3">
            <coa-ldi-per-field-form :CoaLdi="value" :disable="!per[key].Access" :keyPer="key" :title="'Phân quyền'" field-model="AccessField" :table="'doc'" v-model="field.AccessField" @updateFromChild="updateParent"></coa-ldi-per-field-form>
          </td>
          <td class="pr-3 text-center">
            <b-form-checkbox v-model="per[key].Edit">
            </b-form-checkbox>
          </td>
          <td class="pr-3">
            <coa-ldi-per-field-form :CoaLdi="value" :disable="!per[key].Edit" :keyPer="key" :title="'Phân quyền'" field-model="EditField" :table="'doc'" v-model="field.EditField" @updateFromChild="updateParent"></coa-ldi-per-field-form>
          </td>
          <td class="pr-3 text-center">
            <b-form-checkbox v-model="per[key].Delete">
            </b-form-checkbox>
          </td>
          <td class="pr-3 text-center">
            <b-form-checkbox v-model="per[key].Create">
            </b-form-checkbox>
          </td>
          <td @click="deleteLine(key)"><i class="fa fa-trash-o" style="cursor: pointer; font-size: 18px"></i></td>
        </tr>
        </tbody>
      </table>
      <ijcore-modal-multi-listing v-model="value" @changed="addLine" :title="'nhân viên'" :api="'/listing/api/common/list'" :table="'employee'"></ijcore-modal-multi-listing>
    </div>
  </div>

</template>

<script>
import ApiService from '@/services/api.service';
import CoaLdiPerFieldView from "./CoaLdiPerFieldView";
import CoaLdiPerFieldForm from "./CoaLdiPerFieldForm";
import Select2 from 'v-select2-component';
import IjcoreModalMultiListing from "../../../../components/IjcoreModalMultiListing";

export default {
  name: 'CoaLdiPerForm',
  components: {IjcoreModalMultiListing, CoaLdiPerFieldForm, CoaLdiPerFieldView, Select2},
  computed: {

  },
  data() {
    return {
      OptionAccessType: {
        1 : 'Chia sẻ',
        2 : 'Công khai',
        3 : 'Riêng tư'
      },
      GroupID: '',
      AccountID: ''
    }
  },
  created() {
  },
  mounted() {
  },
  methods: {
    getListEmployee(TypeSearch){
      let self = this;
      let urlApi = '/listing/api/common/get-list-employee';
      let requestData = {
        method: 'post',
        url: urlApi,
        data: {
          TypeSearch: TypeSearch
        },
      };
      if(TypeSearch == 'Group'){
        requestData.data.GroupID = self.GroupID.join()
      }
      if(TypeSearch == 'CoaLdi'){
        requestData.data.AccountID = self.AccountID.join()
      }

      this.$store.commit('isLoading', true);
      ApiService.customRequest(requestData).then((response) => {
        let dataResponse = response.data;
        if (dataResponse.status === 1) {
          _.forEach(dataResponse.data, function (val, key) {
            let isExist = 0;
            _.forEach(self.per, function (v, k) {
              if(v.EmployeeID == val.EmployeeID){
                isExist = 1;
              }
            });
            if(isExist == 0){
              self.per.push({
                EmployeeID : val.EmployeeID,
                AccountID: self.value.AccountID,
                Access: true,
                AccessField: 'all',
                Edit: false,
                EditField: '',
                Delete: false,
                Create: false,

              });
            }
          });
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });
    },
    updateParent(data, FieldData, keyPer){
      this.per[keyPer][FieldData] = data;
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
    addLine(link) {
      let self = this;
      link.map(function (val, key) {
        let isExist = 0;
        _.forEach(self.per, function (v, k) {
          if(v.EmployeeID == val.EmployeeID){
            isExist = 1;
          }
        });
        if(isExist == 0){
          self.per.push({
            EmployeeID : val.EmployeeID,
            AccountID: self.value.AccountID,
            Access: true,
            AccessField: 'all',
            Edit: false,
            EditField: '',
            Delete: false,
            Create: false,
          });
        }
      });

    },
    deleteLine(key) {
      this.per.splice(key, 1);
    }
  },
  watch: {
    currentPage() {
      this.fetchData();
    }
  },
  filters: {},
  props: ['title', 'value', 'name', 'api', 'table', 'Task', 'isDetail', 'per', 'EmployeeOption', 'CoaLdiOption', 'GroupOption','EmployeeLogin'],
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
  margin-bottom: 0;
}

.width-datepicker-auto .mx-datepicker{
  width: auto;
}
.width-select2-auto .select2-container{
  width: 100% !important;
}
</style>
