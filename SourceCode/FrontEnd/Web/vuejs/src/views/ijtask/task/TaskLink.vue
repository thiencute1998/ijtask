<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
    <b-modal ref="modal" scrollable id="modal-form-input-task-link" size="xl" @hide="onHideModalDataflow($event)">
      <template slot="modal-title">
        <i class="icon-eye icon" v-if="!isForm"></i><i class="fa fa-edit" v-if="isForm"></i> {{this.title}}
      </template>
      <TaskLinkContent v-model="value" v-if="!isForm" :Task="Task" :per="per">
      </TaskLinkContent>
      <TaskLinkContent v-model="value" :isForm="true" v-if="isForm" :Task="Task" :SysTable="SysTable" :per="per">
      </TaskLinkContent>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" v-if="!isForm && per['Edit'] == 1"
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
  import TaskLinkContent from "./partials/TaskLinkContent";

  export default {
    name: 'TaskLink',
    mixins: [mixinLists],
    components: {
      TaskLinkContent
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
        SysTable: [],
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
        let self = this;
        let requestData = {
          method: 'get',
          data: {}
        };
        requestData.url = '/task/api/task/get-table';
        this.$store.commit('isLoading', true);


        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          self.SysTable = [];
          _.forEach(responsesData.data, function (value, key) {
            let tmpObj = {};
            tmpObj.value = value.TableName;
            tmpObj.text = value.TableDescription;
            self.SysTable.push(tmpObj);
          });

          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
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
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onHideModal() {
        this.$refs['modal'].hide();
        this.isForm = false;
      },
      onToggleModal() {
        let self = this;
        this.currentPage = 1;
        this.$refs['modal'].show();
        if (!this.$parent.showTaskLink) this.$parent.showTaskLink = true;
      },
      onHideModalDataflow() {
        if (this.isDataflow) {
          this.$emit('onHideModalTask');
        }
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
          url: 'task/api/task/task-data-list/' + this.Task.TaskID,
          data: {
            TaskLink: self.value
          }
        };
        // edit user
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
            this.isForm = false;
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
      isDataflow: false,
      per: {}
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

  #modal-form-input-task-link .modal-lg .modal-content {
    width: 1024px;
    margin: auto;
  }

  @media (max-width: 1024px) {
    #modal-form-input-task-link .modal-lg {
      max-width: 100%;
    }

    #modal-form-input-task-link .modal-lg .modal-content {
      width: 90%;
      margin: auto;
    }
  }

  @media (min-width: 992px) {
    #modal-form-input-task-link .modal-lg {
      max-width: 100%;
    }
    #modal-form-input-task-link .modal-lg, #modal-form-input-task-link .modal-xl{
      width: 962px !important;
    }
  }
</style>
