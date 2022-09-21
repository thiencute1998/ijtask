<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
    <b-modal ref="modal" id="modal-form-input-assign" scrollable size="xl" @hide="onHideModalDataflow($event)">
      <template slot="modal-title">
        <i class="icon-eye icon" v-if="!isForm"></i><i class="fa fa-edit" v-if="isForm"></i> {{this.title}}
      </template>
      <TaskAssignContent v-model="value" v-if="!isForm" :Task="Task">
      </TaskAssignContent>
      <TaskAssignContent v-model="value" :isForm="true" v-if="isForm" :Task="Task" ref="DivContainerTableParent">
      </TaskAssignContent>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" v-if="!isForm && isAssignee"
          >
            Sửa
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdate()" v-if="isForm">
            Lưu
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModal()" v-if="isForm">
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
  import TaskAssignContent from "./partials/TaskAssignContent";

  export default {
    name: 'TaskAssign',
    components: {
      TaskAssignContent
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
        isAssignee: false
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
        this.$refs['modal'].hide();
        this.isForm = false;
      },
      onToggleModal() {
        let self = this;
        this.currentPage = 1;
        this.$refs['modal'].show();
        let employee = JSON.parse(localStorage.getItem('Employee'));
        let personAssign = _.find(this.value, ['EmployeeID', employee.EmployeeID]);
        if (personAssign.IsAssignee) {
          this.isAssignee = true;
        } else {
          if (personAssign.IsCreator) {
            this.isAssignee = true;
          }
        }

        this.fetchData();
        if (!this.$parent.showTaskAssign) this.$parent.showTaskAssign = true;
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
        // var scrollbar = document.createElement('div');
        // scrollbar.appendChild(document.createElement('div'));
        // scrollbar.style.overflow = 'auto';
        // scrollbar.style.overflowY = 'hidden';
        // scrollbar.firstChild.style.width = element.scrollWidth + 'px';
        // scrollbar.firstChild.style.paddingTop = '1px';
        // scrollbar.firstChild.appendChild(document.createTextNode('\xA0'));
        // scrollbar.onscroll = function () {
        //   element.scrollLeft = scrollbar.scrollLeft;
        // };
        // element.onscroll = function () {
        //   scrollbar.scrollLeft = element.scrollLeft;
        // };
        // element.parentNode.parentNode.appendChild(scrollbar);
      },
      onUpdate() {
        let self = this;
        const requestData = {
          method: 'post',
          url: 'task/api/task/task-assign/' + this.Task.TaskID,
          data: {
            TaskAssign: self.value
          }
        };
        // edit user
        requestData.data.ItemID = this.Task.TaskID;
        ApiService.setHeader();
        this.$store.commit('isLoading', true);
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

              if (self.isDataflow) {
                self.$_storeTaskDataflowNotice(this.Task.TaskID, 'assign');
              } else {
                self.$_storeTaskNotice(this.Task.TaskID, 'assign');
              }

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
        // }

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
      Task: {},
      table: {},
      isDataflow: false,
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

  #modal-form-input-assign .modal-lg .modal-content {
    width: 1024px;
    margin: auto;
  }

  @media (max-width: 1024px) {
    #modal-form-input-assign .modal-lg {
      max-width: 100%;
    }

    #modal-form-input-assign .modal-lg .modal-content {
      width: 90%;
      margin: auto;
    }
  }

  @media (min-width: 992px) {
    #modal-form-input-assign .modal-lg {
      max-width: 100%;
    }
    #modal-form-input-assign .modal-lg, #modal-form-input-assign .modal-xl{
      width: 962px !important;
    }
  }
</style>
