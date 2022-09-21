<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
    <b-modal scrollable ref="modal" id="modal-form-input-task-general" size="xl" @hide="onHideModalDataflow($event)">
      <template slot="modal-title">
        <i class="icon-eye icon" v-if="!isForm"></i><i class="fa fa-edit" v-if="isForm"></i> {{this.title}}
      </template>
      <TaskGeneralContent v-model="value" v-if="!isForm" :isDetail="true" :per="per">
      </TaskGeneralContent>
      <TaskGeneralForm v-model="value" v-if="isForm" :per="per">
      </TaskGeneralForm>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" v-if="!isForm">
            Sửa
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdate()" v-if="isForm">
            Lưu
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModal()" v-if="isForm">
            Hủy
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModal()">
            Đóng
          </b-button>
        </div>
      </template>

    </b-modal>
  </a>
</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';
  import TaskGeneralContent from "./partials/TaskGeneralContent";
  import TaskGeneralForm from "./partials/TaskGeneralForm";
  import Swal from 'sweetalert2';

  export default {
    name: 'TaskGeneral',
    mixins: [mixinLists],
    components: {
      TaskGeneralForm,
      TaskGeneralContent
    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        isForm: false,
        listtable: [],
        tableName: '',
        search: '',
        lenghNo: 0,
      }
    },
    created() {
    },
    mounted() {
      if (this.isDataflow) {
        this.onToggleModal();
      }
    },
    methods: {
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
      onEdit() {
        this.isForm = true;
      },
      onUpdate() {
        let self = this;
        let StartDate = this.value.StartDate;
        let DueDate = this.value.DueDate;
        if (this.value.StartDate == '__/__/____') {
          StartDate = '';
        }
        if (this.value.DueDate == '__/__/____') {
          DueDate = '';
        }
        let status = _.find(this.value.StatusValueOption, ['value', Number(this.value.StatusValue)]);
        this.value.StatusDescription = (status) ? status.text : '';
        this.$store.commit('isLoading', true);
        let UpdateApi = 'task/api/task/update';
        const requestData = {
          method: 'post',
          url: UpdateApi,
          data: {
            TaskNo: this.value.TaskNo,
            TaskName: this.value.TaskName,
            ParentID: this.value.ParentID,
            ParentName: this.value.ParentName,
            Priority: this.value.Priority,
            AccessType: this.value.AccessType,
            PublicCompanyID: this.value.PublicCompanyID,
            TaskDescription: this.value.TaskDescription,
            UomID: this.value.UomID,
            CalendarTypeID: this.value.CalendarTypeID,
            StartDate: StartDate,
            DueDate: DueDate,
            Duration: this.value.Duration,
            EstimatedQuantity: this.value.EstimatedQuantity,
            DoneNowType: this.value.DoneNowType,
            StatusID: this.value.StatusID,
            StatusValue: this.value.StatusValue,
            StatusDescription: this.value.StatusDescription
          }
        };

        if (this.value.TaskCate && this.value.TaskCate.length) {
          requestData.data.TaskCate = this.value.TaskCate;
        }

        // dataflow
        if (this.isDataflow && this.value.DFKey) {
          requestData.data.TaskIDParent = this.value.TaskParent.TaskID;
          requestData.data.DFKey = this.value.DFKey;
          requestData.data.DFID = this.value.DFID;
          requestData.data.WFID = this.value.WFID;
          requestData.data.WFItemID = this.value.WFItemID;
          requestData.data.isDataflow = true;
        }

        // edit user
        requestData.data.ItemID = self.value.TaskID;
        requestData.url = UpdateApi + '/' + self.value.TaskID;
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
            this.isForm = false;
            if (self.isDataflow) {
              if (responsesData.data && responsesData.data.AutoNewTask) {
                self.$emit('onReloadDataflow');
                self.$_storeTaskDataflowNotice(self.value.TaskID, 'autoNewTask');
              }else {
                self.$_storeTaskDataflowNotice(self.value.TaskID, 'edit');
              }
            } else {
              self.$_storeTaskNotice(self.value.TaskID, 'edit');
            }
          } else {
            let htmlErrors = __.renderErrorApiHtmlObject(responsesData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            )
          }

          self.onHideModal();
          self.$store.commit('isLoading', false);
        }, (error) => {
          self.$store.commit('isLoading', false);
          Swal.fire(
            'Thông báo',
            'Không kết nối được với máy chủ',
            'error'
          )
        });
      },
      onSaveModal() {
        this.$bvToast.toast('Đã lưu ràng buộc', {
          variant: 'success',
          solid: true
        });
      },
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onHideModal() {
        this.isForm = false;
        this.$refs['modal'].hide();
      },
      onHideModalDataflow() {
        if (this.isDataflow) {
          this.$emit('onHideModalTask');
        }
      },
      onToggleModal() {
        let self = this;
        this.currentPage = 1;
        this.isForm = false;
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
    props: ['title', 'value', 'name', 'api', 'table', 'Task', 'isDataflow', 'per'],
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
    margin-bottom: 0px;
  }

  #modal-form-input-task-general .modal-lg .modal-content {
    width: 1024px;
    margin: auto;
  }

  @media (max-width: 1024px) {
    #modal-form-input-task-general .modal-lg {
      max-width: 100%;
    }

    #modal-form-input-task-general .modal-lg .modal-content {
      width: 90%;
      margin: auto;
    }
  }

  @media (min-width: 992px) {
    #modal-form-input-task-general .modal-lg {
      max-width: 100%;
    }

    #modal-form-input-task-general .modal-lg, #modal-form-input-task-general .modal-xl {
      width: 962px !important;
    }
  }
</style>
