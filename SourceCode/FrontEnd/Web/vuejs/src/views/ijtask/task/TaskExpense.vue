import Swal from "sweetalert2";
<template>
  <a @click="onToggleModal()" class="ij-a-icon">
    <i class="fa fa-plus-square-o ij-icon ij-icon-plus" aria-hidden="true" v-if="isNew" title="Thêm"></i>
    <i class="fa fa-external-link ij-icon" aria-hidden="true" v-if="!isNew" title="Chi tiết"></i>
    <b-modal ref="modal"  scrollable id="modal-form-input-task-expense" no-close-on-backdrop size="xl" @hide="onHideModalDataflow($event)">
      <template slot="modal-title">
        <i class="icon-eye icon" v-if="!isForm"></i><i class="fa fa-edit" v-if="isForm && !isNew"></i><i
        class="fa fa-plus" v-if="isForm && isNew"></i> {{this.title}}
      </template>
      <TaskExpenseContent v-model="TaskExpenseTrans" v-if="!isForm" :Task="Task" :per="per" :perDetail="perDetail">
      </TaskExpenseContent>
      <TaskExpenseContent v-model="TaskExpenseTrans" :isForm="true" v-if="isForm && isNew" :Task="Task" :isAddNew="true" :per="per" :perDetail="perDetail"
                          :Uom="Uom">
      </TaskExpenseContent>
      <TaskExpenseContent v-model="TaskExpenseTrans" :isForm="true" v-if="isForm && !isNew" :Task="Task" :per="per" :perDetail="perDetail"
                          :isAddNew="false" :Uom="Uom">
      </TaskExpenseContent>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" v-if="!isForm"
          >
            Sửa
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdate()" v-if="isForm">
            Lưu
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModal" v-if="isForm">
            Hủy
          </b-button>
          <b-button
            variant="primary"
            size="md"
            class="float-left mr-2"
            @click="onHideModal()"
          >
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
  import TaskExpenseContent from "./partials/TaskExpenseContent";

  export default {
    name: 'TaskExpense',
    mixins: [mixinLists],
    components: {
      TaskExpenseContent
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
        Uom: {},
      }
    },
    created() {
      if (this.isNew) {
        this.isForm = true;
      } else {
        this.isForm = false;
      }
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
        let self = this;
        let requestData = {
          method: 'get',
          data: {}
        };
        requestData.url = '/task/api/task/get-uom';
        this.$store.commit('isLoading', true);


        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          self.Uom = [];
          _.forEach(responsesData.data, function (value, key) {
            let tmpObj = {};
            tmpObj.value = value.UomID;
            tmpObj.text = value.UomName;
            self.Uom.push(tmpObj);
          });

          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
          Swal.fire({
            title: 'Thông báo',
            text: 'Không kết nối được với máy chủ',
            confirmButtonText: 'Đóng'
          });
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
        if (this.isNew) {
          this.TaskExpenseTrans.master = {
            TransType: '',
            TransDate: '',
            Comment: '',
          };
          this.TaskExpenseTrans.detail = [{
            ExpenseNo: '',
            Description: '',
            UomID: '',
            Quantity: '',
            UnitPrice: '',
            Amount: '',
            TaxRate: '',
            TaxAmount: '',
            addnew: true,
          }];
          this.isForm = true;
          this.isAddNew = true;
        }
        this.$refs['modal'].hide();
        this.isForm = false;
      },
      onHideModalDataflow() {
        if (this.isDataflow) {
          this.$emit('onHideModalTask');
        }
      },
      onToggleModal() {
        let self = this;
        this.currentPage = 1;
        if (this.isNew) {
          this.isAddNew = true;
          this.fetchData();
          this.isForm = true;
        }
        this.$refs['modal'].show();
      },
      onResetModal() {
      },
      onEdit() {
        this.isForm = true;
        this.fetchData();
      },
      onUpdate() {
        this.$store.commit('isLoading', true);
        let self = this;
        const requestData = {
          method: 'post',
          url: 'task/api/task/task-expense/' + this.Task.TaskID,
          data: {
            TaskExpense: self.TaskExpenseTrans
          }
        };
        requestData.data.ItemID = this.Task.TaskID;
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
            this.$bvToast.toast('Cập nhật thành công!', {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });
            this.value.master.TransID = responsesData.data.TransID;
            this.isForm = false;
            this.$emit("transferExpense", responsesData.data.TaskExpenseTrans, responsesData.data.TaskExpenseTransItem);
            self.$store.commit('isLoading', false);
            if (self.isDataflow) {
              self.$_storeTaskDataflowNotice(self.Task.TaskID, 'expense');
            } else {
              self.$_storeTaskNotice(self.Task.TaskID, 'expense');
            }
          } else {
            let htmlErrors = __.renderErrorApiHtml(responsesData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            );
            self.$store.commit('isLoading', false);
          }
          self.onHideModal();
        }, (error) => {
          console.log(error);
          Swal.fire(
            'Thông báo',
            'Không kết nối được với máy chủ',
            'error'
          )
          self.$store.commit('isLoading', false);
        });
      }
    },
    watch: {},
    props: {
      value: {},
      title: {},
      name: {},
      api: {},
      Task: {},
      table: {},
      isNew: {},
      TaskExpenseTrans: {},
      isDataflow: false,
      per: {},
      perDetail: {}
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
    margin-bottom: 0px;
  }

  #modal-form-input-task-expense .modal-lg .modal-content {
    width: 1024px;
    margin: auto;
  }

  @media (max-width: 1024px) {
    #modal-form-input-task-expense .modal-lg {
      max-width: 100%;
    }

    #modal-form-input-task-expense .modal-lg .modal-content {
      width: 90%;
      margin: auto;
    }
  }

  @media (min-width: 992px) {
    #modal-form-input-task-expense .modal-lg {
      max-width: 100%;
    }
    #modal-form-input-task-expense .modal-lg, #modal-form-input-task-expense .modal-xl{
      width: 962px !important;
    }
  }
</style>
