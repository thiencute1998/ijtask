<template>
  <div class="table-responsive" style="position: relative">
    <table class="not-border" v-if="!isForm">
      <thead>
      <tr class="text-left">
        <th class="pr-3" v-if="ViewPerTransDate">Ngày</th>
        <th class="pr-3" v-if="ViewPerEmployeeName">Người thực hiện</th>
        <th class="pr-3" v-if="ViewPerActualHour">Số giờ</th>
        <th class="pr-3" v-if="ViewPerActualQuantity">Khối lượng</th>
        <th class="pr-3" v-if="ViewPerStatusDescription">Trạng thái</th>
        <th class="pr-3" v-if="ViewPerDescription">Nội dung</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in value">
        <td class="pr-3" v-if="ViewPerTransDate">{{item.TransDate}}</td>
        <td class="pr-3" v-if="ViewPerEmployeeName">{{item.EmployeeName}}</td>
        <td class="pr-3" v-if="ViewPerActualHour">{{item.ActualHour|convertNumberToText}}</td>
        <td class="pr-3" v-if="ViewPerActualQuantity">{{item.ActualQuantity|convertNumberToText}}</td>
        <td class="pr-3" v-if="ViewPerStatusDescription">{{item.StatusDescription}}</td>
        <td class="pr-3" style="max-width: 500px; white-space: pre-wrap" v-if="ViewPerDescription">{{item.Description}}</td>
        <td class="text-right" style="position: absolute; right: 0; vertical-align: top">
          <div class="d-inline-flex">
            <TaskExecutionForm v-model="value" :keyArray="key" :title="'Thực hiện'" :Task="Task" :TaskStatus="TaskStatus" :per="per" :per-gen="perGen"  v-if="per['Edit']"
                               :addnew="false">
            </TaskExecutionForm>
            <i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o" v-if="per['Delete']"
               style="font-size: 18px; cursor: pointer; position: relative; top: -1px; right: 0"></i>
          </div>
        </td>
      </tr>
      </tbody>
    </table>
  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';
  import moment from 'moment';
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import IjcoreDateTimePicker from "../../../../components/IjcoreDateTimePicker";
  import IjcoreNumber from "../../../../components/IjcoreNumber";
  import TaskExecutionForm from "./TaskExecutionForm";

  export default {
    name: 'TaskExecutionContent',
    mixins: [mixinLists],
    components: {
      TaskExecutionForm,
      IjcoreNumber,
      IjcoreDateTimePicker,
      IjcoreModalListing
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
        ViewPerTransDate: true,
        ViewPerEmployeeName: true,
        ViewPerActualHour: true,
        ViewPerActualQuantity: true,
        ViewPerStatusDescription: true,
        ViewPerDescription: true,
      }
    },
    created() {
    },
    mounted() {
      this.ViewPerTransDate = __.perViewColumn(this.per, 'TransDate')
      this.ViewPerEmployeeName = __.perViewColumn(this.per, 'EmployeeName')
      this.ViewPerActualHour = __.perViewColumn(this.per, 'ActualHour')
      this.ViewPerActualQuantity = __.perViewColumn(this.per, 'ActualQuantity')
      this.ViewPerStatusDescription = __.perViewColumn(this.per, 'StatusDescription')
      this.ViewPerDescription = __.perViewColumn(this.per, 'Description')
    },
    methods: {
      fetchData() {
        if (this.idParams == 0 || _.isUndefined(this.idParams)) {
          return false;
        }
        let self = this;
        let urlApi = '';
        let requestData = {
          method: 'get',
        };
        // Api edit user
        if (this.idParams) {
          urlApi = 'task/api/task/get-status-task/' + this.idParams;
          let data = {
            id: this.idParams
          };
          requestData.data = data;
        }
        requestData.url = urlApi;
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          self.defaultModel = responsesData;
          if (responsesData.status === 1) {

          }

          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
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
        this.fetchData();
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
      addLine() {
        this.value.push({
          TaskID: this.Task.TaskID,
          ActualHour: '',
          ActualQuantity: '',
          CalMethod: '',
          PercentCompleted: '',
          Description: '',
          TransDate: '',
          EmployeeID: '',
          EmployeeName: '',
          StatusID: this.Task.StatusID,
          StatusName: this.Task.StatusName,
          StatusValue: '',
          StatusDescription: '',
          addnew: true,
        });
      },
      deleteLine(key) {
        this.$store.commit('isLoading', true);
        let self = this;
        const requestData = {
          method: 'post',
          url: 'task/api/task/task-execution-delete',
          data: {
            TransID: self.value[key].TransID
          }
        };
        // edit user

        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            if (!self.idParams && responsesData.data) self.idParams = responsesData.data.param;
            this.$bvToast.toast('Đã xóa thành công!', {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });
            this.value.splice(key, 1);
            this.Task.TotalActualQuantity = __.convertNumberToText(responsesData.data.Task.TotalActualQuantity);
            this.Task.OldTotalActualQuantity = this.Task.TotalActualQuantity;
            this.Task.ActualQuantity = __.convertNumberToText(responsesData.data.Task.ActualQuantity);
            this.Task.OldActualQuantity = this.Task.ActualQuantity;
            this.Task.PercentCompleted = __.convertNumberToText(responsesData.data.Task.PercentCompleted);
            this.Task.EstimatedQuantity = __.convertNumberToText(responsesData.data.Task.EstimatedQuantity);
            self.$store.commit('isLoading', false);
          } else {
            let htmlErrors = __.renderErrorApiHtml(responsesData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            )
            self.$store.commit('isLoading', false);
          }

        }, (error) => {
          console.log(error);
          Swal.fire(
            'Thông báo',
            'Không kết nối được với máy chủ',
            'error'
          )
          self.$store.commit('isLoading', false);
        });
      },

      onReloadDataflow() {
        this.$emit('onReloadDataflow');
      },
      onHideModalTask() {
        this.$emit('onHideModalTask');
      }
    },
    watch: {
      currentPage() {
        this.fetchData();
      }
    },
    props: {
      value: {},
      title: {},
      name: {},
      api: {},
      table: {},
      Task: {},
      isForm: false,
      isDataflow: false,
      TaskStatus: {},
      per: {},
      perGen: {}
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

  .mx-input-wrapper {
    width: 173px !important;
  }

  .mx-datepicker {
    width: 173px !important;
  }
</style>
