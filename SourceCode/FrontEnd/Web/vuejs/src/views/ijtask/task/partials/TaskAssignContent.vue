<template>
  <div class="table-responsive table-responsive-assign" ref="DivContainerTable">
    <table class="not-border" v-if="!isForm">
      <thead>
      <tr class="text-left">
        <th class="pr-3">Nhân viên</th>
        <th class="pr-3">Ngày bắt đầu</th>
        <th class="pr-3">Hạn HT</th>
        <th class="pr-3">Số giờ</th>
        <th class="pr-3">Mô tả</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in value">
        <td class="pr-3">{{item.EmployeeName}}</td>
        <td class="pr-3">{{item.StartDate}}</td>
        <td class="pr-3">{{item.DueDate}}</td>
        <td class="pr-3">{{item.EstimatedHour|convertNumberToText}}</td>
        <td class="pr-3">{{item.Description}}</td>
        <td class="pr-3" style="position: absolute; right: 0;"><TaskPerContent :EmployeeID="item.EmployeeID" :Task="Task"></TaskPerContent></td>
      </tr>
      </tbody>
    </table>
    <table v-if="isForm" class="table b-table table-sm table-bordered table-editable el-first-modal table-fit-content" ref="TableEditAssign">
      <thead>
      <tr class="text-center">
        <th style="min-width: 200px">Nhân viên</th>
        <th style="min-width: 200px">Vai trò</th>
        <th>Ngày bắt đầu</th>
        <th>Hạn HT</th>
        <th>Số giờ</th>
        <th style="min-width: 200px">Mô tả</th>
        <th style="min-width: 50px" class="b-table-sticky-column-right"></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in this.value">
        <td>
          <ijcore-modal-listing v-model="value[key]" :title="'nhân viên'" :api="'/listing/api/common/list'" :table="'employee'"></ijcore-modal-listing>
        </td>
        <td class="td-select2">
          <Select2 v-model="value[key].PersonAssign" :settings="{multiple: true, placeholder: '', closeOnSelect: false, allowClear: true}" :options="options" @change="changePersonAssign($event, key)"></Select2>
        </td>
        <td>
          <ijcore-date-picker-assign v-model="value[key].StartDate" :keyValue="key"></ijcore-date-picker-assign>
        </td>
        <td>
          <ijcore-date-picker-assign v-model="value[key].DueDate" :keyValue="key"></ijcore-date-picker-assign>
        </td>
        <td>
          <ijcore-number v-model="value[key].EstimatedHour"></ijcore-number>
        </td>
        <td style="width: 280px;"><input v-model="value[key].Description" class="form-control"/></td>
        <td style="text-align: center;" class="b-table-sticky-column-right">
          <i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o" style="font-size: 18px; cursor: pointer;"></i>
        </td>
      </tr>
      </tbody>
    </table>
    <ijcore-modal-multi-listing v-model="value" @changed="addLine" v-if="isForm" :title="'nhân viên'" :api="'/listing/api/common/list'" :table="'employee'"></ijcore-modal-multi-listing>
  </div>
</template>
<script>
  import ApiService from '@/services/api.service';
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import IjcoreDatePickerAssign from "../../../../components/IjcoreDatePickerAssign";
  import IjcoreModalMultiListing from "../../../../components/IjcoreModalMultiListing";
  import IjcoreNumber from "../../../../components/IjcoreNumber";
  import TaskPer from "./TaskPer";
  import Multiselect from "vue-multiselect";
  import Select2 from "v-select2-component";
  import TaskPerContent from "./TaskPerContent";

  export default {
    name: 'TaskAssignContent',
    components: {
      TaskPerContent,
      TaskPer,
      IjcoreNumber,
      IjcoreModalMultiListing,
      IjcoreDatePickerAssign,
      IjcoreModalListing,
      Multiselect,
      Select2,
    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        listtable: [],
        tableName: '',
        search: '',
        lenghNo: 0,
        options: [
          {id: '1', text: 'Giao việc'},
          {id: '2', text: 'Trách nhiệm chính'},
          {id: '3', text: 'Trách nhiệm liên quan'},
          {id: '6', text: 'Người thực hiện'},
          {id: '4', text: 'Kiểm tra'},
          {id: '5', text: 'Theo dõi'},
        ],
        IsMainResponsiblePerson: -1,
        stage: {
          updatedData: false,
          message: (this.$route.params.message) ? this.$route.params.message : ''
        }
      }
    },
    created() {
      this.IsMainResponsiblePerson = this.Task.IsMainResponsiblePerson;
    },
    mounted() {
    },
    methods: {
      validateEstimatedQuantityRate(key){
        let self = this;
        let total = 0;
        _.forEach(self.value, function (val, k) {
          total += parseFloat(val.EstimatedQuantityRate);
        });
        if(total > 100){
          self.value[key].EstimatedQuantityRate = 0;
          self.value[key].EstimatedQuantity = 0;
          self.$bvToast.toast('Tổng trọng số không được quá 100!', {
            title: 'Thông báo',
            variant: 'success',
            solid: true
          });
        }else{
          self.value[key].EstimatedQuantity = self.value[key].EstimatedQuantityRate*parseFloat(self.Task.EstimatedQuantity)/100;
        }
      },
      changePersonAssign(valueChange, key){
        let self = this;
        if(this.value[key].PersonAssign.includes("1")){
          this.value[key].IsAssignee = 1;
        }else{
          this.value[key].IsAssignee = 0;
        }

        if(this.value[key].PersonAssign.includes("2")){
          if(this.IsMainResponsiblePerson != key && this.IsMainResponsiblePerson != -1 && this.IsMainResponsiblePerson != undefined){
            this.value[key].IsMainResponsiblePerson = 0;
            //Xóa, không cho phép quá 2 người chịu trách nhiệm chính
            var index = this.value[key].PersonAssign.indexOf("2");
            if (index > -1) {
              this.value[key].PersonAssign.splice(index, 1);
              Swal.fire(
                'Đã tồn tại người chịu trách nhiệm chính!',
                '',
                'error'
              );
              this.$forceUpdate();
            }
          }else{
            this.value[key].IsMainResponsiblePerson = 1;
            this.IsMainResponsiblePerson = key;
          }
        }else{
          if(this.IsMainResponsiblePerson == key){
            this.value[key].IsMainResponsiblePerson = 0;
            this.IsMainResponsiblePerson = -1;
          }
        }
        if(this.value[key].PersonAssign.includes("3")){
          this.value[key].IsResponsiblePerson = 1;
        }else{
          this.value[key].IsResponsiblePerson = 0;
        }
        if(this.value[key].PersonAssign.includes("4")){
          this.value[key].IsChecker = 1;
        }else{
          this.value[key].IsChecker = 0;
        }
        if(this.value[key].PersonAssign.includes("5")){
          this.value[key].IsFollower = 1;
        }else{
          this.value[key].IsFollower = 0;
        }
        if(this.value[key].PersonAssign.includes("6")){
          this.value[key].IsExecutor = 1;
        }else{
          this.value[key].IsExecutor = 0;
        }

        let description = '';
        _.forEach(valueChange, function (value, keyChange) {
          let personAssign = _.find(self.options, ['id', value]);
          if (personAssign) {
            // if (!self.value[key].Description.includes(personAssign.text)) {
            //   if (self.value[key].Description) {
            //     self.value[key].Description += ', ';
            //   }
            //   self.value[key].Description += personAssign.text;
            // }

            if (description) {
              description += ', ';
            }
            description += personAssign.text;
          }
        });
        this.value[key].Description = description;
      },
      formatDate(data) {
        data = data.split(' ');
        data = data[0];
        data = data.split('-');
        let dd = data[2];
        let mm = data[1];
        let yyyy = data[0];
        data = dd + '/' + mm + '/' + yyyy;
        return data;
      },
      fetchData() {

      },
      onSaveModal() {

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
      },
      onResetModal() {
      },
      clickText: function (event, key) {
        if (this.isForm) {
          event.target.hidden = true;
          event.target.nextSibling.hidden = false;
          this.value[key].addnew = true;
        }
      },
      hideInput: function (event, loop, key) {
        let element = event.target;
        if (event.target.value) {
          for (let i = 1; i <= loop; i++) {
            element = element.parentElement;
          }
          element.hidden = true;
          element.previousSibling.hidden = false;
          this.value[key].addnew = false;
        }
      },
      addLine(link) {
        let linkReset = link.filter(function (val) {
          return val
        });
        let self = this;
        _.forEach(linkReset, function (item, key) {
          let indexExist = _.findIndex(self.value, ['EmployeeID', item.EmployeeID]);
          if (indexExist < 0) {
            self.value.push({
              EmployeeID: item.EmployeeID,
              EmployeeName: item.EmployeeName,
              StartDate: self.Task.StartDate,
              FinishDate: self.Task.DueDate,
              DueDate: self.Task.DueDate,
              EstimatedHour: self.Task.EstimatedQuantity,
              EstimatedQuantity: 0,
              EstimatedQuantityRate: 0,
              Description: '',
              IsChecker: 0,
              IsMainResponsiblePerson: 0,
              IsResponsiblePerson: 0,
              IsAssignee: 0,
              IsCreator: 0,
              IsFollower: 0,
              IsExecutor: 1,
              addnew: true,
            });
            let indexNew = _.findIndex(self.value, ['EmployeeID', item.EmployeeID]);
            if (indexNew > -1) {
              self.value[indexNew].PersonAssign = ['6'];
              self.value[indexNew].Description = 'Người thực hiện';
            }
          }
        });

      },
      updateHour(key) {
        if (this.value[key].StartDate && this.value[key].DueDate) {
          let self = this;
          let urlApi = '/task/api/task/get-hour';
          let requestData = {
            method: 'post',
            url: urlApi,
            data: {
              StartDate: self.value[key].StartDate,
              DueDate: self.value[key].DueDate,
              CalendarTypeID: self.Task.CalendarTypeID,
            },

          };
          this.$store.commit('isLoading', true);
          ApiService.customRequest(requestData).then((response) => {
            let dataResponse = response.data;

            if (dataResponse.status === 1) {
              self.value[key].EstimatedHour = dataResponse.data;
            }
            self.$store.commit('isLoading', false);
          }, (error) => {
            console.log(error);
            self.$store.commit('isLoading', false);
          });

          // scroll to top perfect scroll
          const container = document.querySelector('.b-table-sticky-header');
          if (container) container.scrollTop = 0;
        }
      },
      deleteLine(key) {
        this.value.splice(key, 1);
      }
    },
    watch: {
      currentPage() {
        this.fetchData();
      },
    },
    props: {
      value: {},
      title: {},
      name: {},
      api: {},
      table: {},
      Task: {},
      isForm: false,
    },
  }
</script>
<style>
  .readonly {
    background-color: #fff !important;
  }

  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }

  .modal-footer ol, .modal-footer ul, .modal-footer dl {
    margin-bottom: 0;
  }


  .mx-datepicker {
    display: block !important;
  }

  .td-select2 {
    width: 650px !important;
    max-width: 650px;
    height: 30px;
    overflow-y: hidden;
  }

  .td-select2 .select2-selection{
    border: none !important;
  }
  .td-select2 .selection {
    width: 100%;
  }
  .td-select2 .select2-container{
    width: auto !important;
    min-width: 100%;
    display: inline-flex;
  }
  .td-select2 .select2-container .select2-selection--multiple{
    width: 100%;
  }
  .td-select2 .select2-selection__choice {
    max-width: none;
  }
  .td-select2 .select2-container--default .select2-selection--multiple .select2-selection__choice{
    margin-top: 1px;
    margin-bottom: 1px;
    height: 30px;
    display: flex;
    padding-top: 2px;
  }
  .td-select2 .select2-container--default.select2-container--focus .select2-selection--multiple{
    display: flex;
    height: 32px;
  }
  .td-select2 .select2-container .select2-selection--multiple{
    min-height: auto;
  }
  .td-select2 .select2-container--default .select2-search--inline .select2-search__field{
    height: 32px !important;
    margin-top: -1px !important;
  }
  .td-select2 .select2-container--default .select2-selection--multiple .select2-selection__rendered li{
    height: 30px;
  }
  .td-select2 .select2-container .select2-selection--multiple .select2-selection__rendered{
    display: flex !important;
  }
  .td-select2 > div{
    height: 100%;
  }

  .table-editable .td-select2 .select2-container--default .select2-selection--multiple .select2-selection__rendered li{
    height: 26px;
  }
  .table-responsive-assign .table-editable .select2-container--default .select2-selection--multiple .select2-selection__choice{
    margin-top: 3px !important;
  }
  .table-fit-content.table.b-table td {
    max-width: none;
  }
</style>
