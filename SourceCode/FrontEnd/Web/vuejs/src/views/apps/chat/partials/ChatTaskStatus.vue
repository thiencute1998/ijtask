<template>
  <div class="component-chat-task-status" v-if="taskArray.length">
    <div class="task-one-status mx-2" v-if="taskArray.length === 1">
      <b-dropdown @show="getTaskStatusValue(taskArray[0])" @hide="hideTaskStatus">
        <template #button-content>
          <span v-if="taskArray[0].StatusDescription">{{taskArray[0].StatusDescription}}</span>
          <span v-else>Trạng thái công việc</span>
        </template>
        <div class="spinners" style="height: 10px;" v-if="stage.loadingStatusValue">
          <div class="sk-double-bounce mx-auto my-0">
            <div class="sk-child sk-double-bounce1"></div>
            <div class="sk-child sk-double-bounce2"></div>
          </div>
        </div>

        <li role="presentation" v-for="(StatusValue, key) in taskArray[0].AllStatusValue">
          <a role="menuitem" href="#" @click.stop="changeTaskStatus($event, taskArray[0], StatusValue)" target="_self" class="dropdown-item py-0">
            <div class="custom-control custom-radio py-1">
              <input type="radio" class="custom-control-input" :checked="taskArray[0].StatusValue === StatusValue.StatusValue" :value="StatusValue.StatusValue" :id="'status-value-' + key" name="status-value-radio">
              <label class="custom-control-label" :for="'status-value-' + key">{{StatusValue.StatusDescription}}</label>
            </div>
          </a>
        </li>
        <b-dropdown-divider v-if="!stage.loadingStatusValue"></b-dropdown-divider>
        <b-dropdown-text v-if="!stage.loadingStatusValue">
          <div class="dropdown-footer d-flex justify-content-between">
            <b-button variant="primary mr-1" size="sm" @click="saveTaskStatus($event ,taskArray[0])">Lưu</b-button>
            <b-button variant="primary mr-1" size="sm" @click="resetDefault($event)">Hủy</b-button>
            <b-button variant="primary" size="sm" @click="closeTaskStatus($event)">Đóng</b-button>
          </div>
        </b-dropdown-text>
      </b-dropdown>
    </div>

    <div class="task-more-status" v-if="taskArray.length > 1">
      <b-dropdown>
        <template #button-content>
          <span id="btn-text-dropdown">Trạng thái công việc</span>
        </template>
        <li class="dropdown b-dropdown dropright" v-for="(datalist, key) in taskArray">
          <a class="dropdown-item text-nowrap dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" style="display: inherit !important" :title="datalist.LinkName" data-toggle="dropdown" @click.stop="onToggleDropdownSubMenu($event, taskArray[key])" href="#">
            {{datalist.LinkName}}
          </a>
          <ul role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-top dropdown-menu m-0">
            <div class="spinners" style="height: 10px;" v-if="stage.loadingStatusValue">
              <div class="sk-double-bounce mx-auto my-0">
                <div class="sk-child sk-double-bounce1"></div>
                <div class="sk-child sk-double-bounce2"></div>
              </div>
            </div>
            <li role="presentation" v-for="(StatusValue, keyStatusValue) in taskArray[key].AllStatusValue">
              <a role="menuitem" href="#" @click.stop="changeTaskStatus($event, taskArray[key], StatusValue)" target="_self" class="dropdown-item py-0">
                <div class="custom-control custom-radio py-1">
                  <input type="radio" class="custom-control-input" :checked="taskArray[key].StatusValue === StatusValue.StatusValue" :value="StatusValue.StatusValue" :id="'status-value-' + keyStatusValue" name="status-value-radio">
                  <label class="custom-control-label" :for="'status-value-' + keyStatusValue">{{StatusValue.StatusDescription}}</label>
                </div>
              </a>
            </li>
            <b-dropdown-divider v-if="!stage.loadingStatusValue"></b-dropdown-divider>
            <b-dropdown-text v-if="!stage.loadingStatusValue">
              <div class="dropdown-footer d-flex justify-content-between">
                <b-button variant="primary mr-1" size="sm" @click="saveTaskStatus($event, taskArray[key])">Lưu</b-button>
                <b-button variant="primary mr-1" size="sm" @click="resetDefault($event)">Hủy</b-button>
                <b-button variant="primary" size="sm" @click="closeTaskStatus($event)">Đóng</b-button>
              </div>
            </b-dropdown-text>
          </ul>
        </li>
      </b-dropdown>
    </div>

  </div>
</template>
<style>
  .component-chat-task-status .dropdown-menu {
    min-height: 60px;
    max-width: 200px;
  }
  .component-chat-task-status .btn.dropdown-toggle, .component-chat-task-status .btn.dropdown-toggle:active,
  .component-chat-task-status .btn.dropdown-toggle:focus{
    background: transparent !important;
    padding: 0;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
  }
  .component-chat-task-status .dropdown-center {
    transform: translateY(-50%);
    top: 50%;
  }
  .component-chat-task-status .dropdown-top {
    transform: translateY(-100%);
    top: 100%;
  }
  .component-chat-task-status .dropdown-toggle {
    position: relative;
  }

  .component-chat-task-status .dropdown-toggle.text-nowrap {
    position: relative;
  }
  .component-chat-task-status .dropdown-toggle.text-nowrap::after {
    vertical-align: 0;
    position: absolute;
    right: .25rem;
    top: 50%;
    transform: translateY(-50%);
  }
</style>
<script>
  import ApiService from '@/services/api.service';
  export default {
    name: 'chat-task-status',
    props: {
      value: [Array, Object],
      // Datalist: [Array, Object]
    },
    data () {
      return {
        haveTask: false,
        taskArray: [],
        defaultArray: [],
        stage: {
          loadingStatusValue: false,
          isSaveStatusValue: false
        }
      }
    },
    mounted() {
      let self = this;
      this.taskArray = _.filter(this.value, ['DatalistTable', 'task']);
      this.defaultArray = [];
      _.forEach(this.taskArray, function (item, key) {
        let tmpObj = {};
        tmpObj.DatalistTable = item.DatalistTable;
        tmpObj.LinkID = item.LinkID;
        tmpObj.LinkName = item.LinkName;
        tmpObj.LinkNo = item.LinkNo;
        tmpObj.LinkTableName = item.LinkTableName;
        tmpObj.StatusDescription = item.StatusDescription;
        tmpObj.StatusID = item.StatusID;
        tmpObj.StatusName = item.StatusName;
        tmpObj.StatusValue = item.StatusValue;
        self.defaultArray.push(tmpObj);
      });
    },
    methods: {
      changeTaskStatus(e, datalist, StatusValue){
        e.preventDefault();
        e.stopPropagation();

        let indexTaskArray = _.findIndex(this.taskArray, {
          DatalistTable: datalist.DatalistTable,
          LinkID: datalist.LinkID
        });
        if (indexTaskArray > -1) {
          this.taskArray[indexTaskArray].StatusValue = StatusValue.StatusValue;
          this.taskArray[indexTaskArray].StatusDescription = StatusValue.StatusDescription;
          this.taskArray[indexTaskArray].Status = StatusValue.ExecutionStatus;
        }

        let indexDatalist = _.findIndex(this.value, {
          DatalistTable: datalist.DatalistTable,
          LinkID: datalist.LinkID
        });
        if (indexDatalist > -1) {
          this.value[indexDatalist].StatusValue = StatusValue.StatusValue;
          this.value[indexDatalist].StatusDescription = StatusValue.StatusDescription;
          this.value[indexDatalist].Status = StatusValue.ExecutionStatus;
        }
        this.$forceUpdate();
      },
      getTaskStatusValue(datalist){
        if (!datalist.AllStatusValue) {
          let self = this;
          let requestData = {
            method: 'post',
            url: 'extensions/api/chat/get-task-status-value',
            data: {
              TaskID: datalist.LinkID,
              StatusID: datalist.StatusID
            }
          };
          this.stage.loadingStatusValue = true;
          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            self.stage.loadingStatusValue = false;
            let responsesData = responses.data;
            if (responsesData.status === 1) {
              let datalistIndex = _.findIndex(self.value, {
                DatalistTable: 'task',
                LinkID: datalist.LinkID
              });
              if (datalistIndex > -1) {
                self.value[datalistIndex].AllStatusValue = responsesData.data;
              }

              let taskIndex = _.findIndex(self.taskArray, {
                DatalistTable: 'task',
                LinkID: datalist.LinkID
              });
              if (taskIndex > -1) {
                self.taskArray[taskIndex].AllStatusValue = responsesData.data;
              }
            }
          }, (error) => {
            self.stage.loadingStatusValue = false;
          });
        }
      },
      saveTaskStatus(e, datalist){
        e.preventDefault();
        e.stopPropagation();
        let self = this;
        this.stage.isSaveStatusValue = true;
        let requestData = {
          method: 'post',
          url: '/extensions/api/chat/update-task-status-value',
          data: {
            TaskID: datalist.LinkID,
            StatusValue: datalist.StatusValue,
            StatusDescription: datalist.StatusDescription
          }
        };

        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          self.$store.commit('isLoading', false);
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            let datalistIndex = _.findIndex(self.value, {
              DatalistTable: 'task',
              LinkID: datalist.LinkID
            });
            if (datalistIndex > -1) {
              self.value[datalistIndex].StatusValue = responsesData.data.Task.StatusValue;
              self.value[datalistIndex].StatusDescription = responsesData.data.Task.StatusDescription;
              self.value[datalistIndex].Status = responsesData.data.Task.Status;
            }

            let taskIndex = _.findIndex(self.taskArray, {
              DatalistTable: 'task',
              LinkID: datalist.LinkID
            });
            if (taskIndex > -1) {
              self.taskArray[taskIndex].StatusValue = responsesData.data.Task.StatusValue;
              self.taskArray[taskIndex].StatusDescription = responsesData.data.Task.StatusDescription;
              self.taskArray[taskIndex].Status = responsesData.data.Task.Status;
            }

            // update default
            let defaultIndex = _.findIndex(self.defaultArray, {
              DatalistTable: 'task',
              LinkID: datalist.LinkID
            });
            if (defaultIndex > -1) {
              self.defaultArray[defaultIndex].StatusValue = responsesData.data.Task.StatusValue;
              self.defaultArray[defaultIndex].StatusDescription = responsesData.data.Task.StatusDescription;
              self.defaultArray[defaultIndex].Status = responsesData.data.Task.Status;
            }

            if (responsesData.data.Task.Type === 2) {
              if (responsesData.data.AutoNewTask) {
                self.$_storeTaskDataflowNotice(responsesData.data.Task.TaskID, 'autoNewTask');
              }else {
                self.$_storeTaskDataflowNotice(responsesData.data.Task.TaskID, 'edit');
              }
            } else {
              self.$_storeTaskNotice(responsesData.data.Task.TaskID, 'edit');
            }

            self.$bvToast.toast(responsesData.msg, {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });

          } else {
            this.resetDefault(e);
            self.$bvToast.toast(responsesData.msg, {
              title: 'Thông báo',
              variant: 'warning',
              solid: true
            });
          }
          this.closeTaskStatus(e);

        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      resetDefault(e) {
        if (e) {
          e.preventDefault();
          e.stopPropagation();
        }
        let self = this;
        _.forEach(this.value, function (datalist, key) {
          let defaultItem = _.find(self.defaultArray, {
            DatalistTable: 'task',
            LinkID: datalist.LinkID
          });

          if (defaultItem) {
            self.value[key].StatusValue = defaultItem.StatusValue;
            self.value[key].StatusDescription = defaultItem.StatusDescription;
            self.value[key].Status = defaultItem.Status;
          }
        });
        this.taskArray = _.filter(this.value, ['DatalistTable', 'task']);
      },
      closeTaskStatus(e) {
        e.preventDefault();
        e.stopPropagation();
        if (!this.stage.isSaveStatusValue) {
          this.resetDefault();
        }
        if (this.taskArray.length === 1) {
          $(e.target).closest('.b-dropdown').find('.dropdown-toggle').trigger('click');
        } else {
          $(this.$el.querySelector('#btn-text-dropdown')).click();
          $(this.$el.querySelector('#btn-text-dropdown')).click();
          let $subMenu = $(e.target).closest('.dropdown-sub-menu');
          if ($subMenu.hasClass('show')) {
            $subMenu.removeClass('show');
          }
        }
      },
      hideTaskStatus() {
        if (!this.stage.isSaveStatusValue) {
          this.resetDefault();
        }
      },
      onToggleDropdownSubMenu(event, datalist = null) {
        this.stage.isSaveStatusValue = false;
        __.onToggleDropdownSubMenu(event);
        if (datalist) {
          this.getTaskStatusValue(datalist);
        }
      }
    }
  }
</script>
