<template>
  <div class="component-dataflow-list">
    <div class="main-entry">
      <div class="main-header">
        <div class="main-header-padding">
          <b-row class="mb-2">
            <b-col class="col-md-12">
              <div class="main-header-item main-header-name">
                <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Luồng công việc</span>
              </div>
            </b-col>
            <b-col class="col-md-12"></b-col>
          </b-row>
          <b-row class="mb-2">
            <b-col class="col-lg-12 col-md-24 col-sm-24 col-24 mb-2 mb-lg-0 d-lg-flex justify-content-start align-items-center">
              <div class="main-header-item main-header-actions">
                <b-button class="main-header-action mr-2" variant="primary" @click="$_lists_handleAddNewItem" size="md">
                  <i class="fa fa-plus"></i> Thêm
                </b-button>

                <b-dropdown variant="primary" id="dropdown-actions" @toggle="$_lists_onToggleActionMajor" class="main-header-action mr-2" text="Thao tác">
                  <b-dropdown-item @click="$_lists_handleDeleteItem" :disabled="stage.disableActions">Xóa</b-dropdown-item>
                  <b-dropdown-item>In</b-dropdown-item>
                  <li class="dropdown b-dropdown dropright" >
                    <a class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" data-toggle="dropdown" @click.stop="$_lists_onToggleDropdownSubMenu($event)" href="#">Nhập</a>
                    <ul role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-menu m-0">
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Excel</a></li>
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Cvs</a></li>
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Xml</a></li>
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Json</a></li>
                    </ul>
                  </li>
                  <li class="dropdown b-dropdown dropright">
                    <a class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" :class="[stage.disableActions ? 'disabled' : '']" data-toggle="dropdown" @click.stop="onToggleDropdownSubMenu($event)" href="#">Xuất</a>
                    <ul role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-menu m-0">
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Excel</a></li>
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Cvs</a></li>
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Xml</a></li>
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Json</a></li>
                    </ul>
                  </li>
                  <b-dropdown-item :disabled="stage.disableActions">Chia sẻ</b-dropdown-item>
                  <b-dropdown-item :disabled="stage.disableActions">Chat</b-dropdown-item>
                  <b-dropdown-item :disabled="stage.disableActions">Zalo</b-dropdown-item>
                  <b-dropdown-item :disabled="stage.disableActions">Phân quyền</b-dropdown-item>
                  <b-dropdown-item :disabled="stage.disableActions || !stage.actionInactive.showInactive" v-if="stage.actionInactive.inactive == 0" @click="$_lists_handleChangeInActive">Đang hoạt động</b-dropdown-item>
                  <b-dropdown-item :disabled="stage.disableActions || !stage.actionInactive.showInactive" v-if="stage.actionInactive.inactive == 1" @click="$_lists_handleChangeInActive">Ngừng hoạt động</b-dropdown-item>
                </b-dropdown>
              </div>
              <div class="main-header-item main-header-search" style="flex: 1 1 auto">
                <div class="search-input">
                  <input v-model="fullTextSearch" @keyup.enter="$_lists_handleFullTextSearch" type="text" placeholder="Tìm kiếm..."/>
                </div>
                <span class="search-icon" @click="$_lists_handleFullTextSearch"><i class="fa fa-search"></i></span>
              </div>
            </b-col>
            <b-col class="col-lg-12 col-md-24 col-sm-24 col-24">
              <div class="main-header-item main-header-icons">
                <b-button-group id="main-header-views" class="main-header-views">
<!--                  <b-dropdown id="dropdown-view-type" title="Loại hiển thị" menu-class="p-0" :class="[(stage.viewType === 'list' || stage.viewType === 'tree' || stage.viewType === 'select') ? 'view-active' : '']" class="app-dropdown-center" toggle-class="main-header-view">-->
<!--                    <template v-slot:button-content>-->
<!--                      <i class="fa fa-tree" v-if="stage.viewType === 'tree'"></i>-->
<!--                      <i class="fa fa-list" v-else></i>-->
<!--                    </template>-->
<!--                    <b-dropdown-item id="tooltip-view-list" title="Danh sách" @click="stage.viewType = 'list'"><i class="fa fa-list m-0"></i></b-dropdown-item>-->
<!--                    <b-dropdown-item id="tooltip-view-tree" title="Cây" @click="stage.viewType = 'tree'"><i class="fa fa-tree m-0"></i></b-dropdown-item>-->
<!--                  </b-dropdown>-->
<!--                  <b-button id="tooltip-view-kanban" title="Thẻ tin" @click="stage.viewType = 'kanban'" :class="[(stage.viewType === 'kanban') ? 'view-active' : '']" class="main-header-view"><i class="fa fa-th"></i></b-button>-->
                </b-button-group>

                <b-form-select id="dropdown-sort" style="max-width: 120px; border-top-right-radius: 0; border-bottom-right-radius: 0" v-model="modelSearch.sortBy" :options="[
                  {value: 1, text: 'Mới nhất'},
                  {value: 2, text: 'Cũ nhất'},
                  {value: 3, text: 'Khẩn cấp'},
                  {value: 4, text: 'Đang chờ'},
                  ]"></b-form-select>

                <b-dropdown id="dropdown-per-page" title="Số bản ghi/trang" menu-class="p-0" :text="perPage" class="app-dropdown-center main-header-icon">
                  <b-dropdown-item @click="$_lists_handleChangePerPage(10)">10</b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleChangePerPage(15)">15</b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleChangePerPage(20)">20</b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleChangePerPage(30)">30</b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleChangePerPage(40)">40</b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleChangePerPage(50)">50</b-dropdown-item>
                </b-dropdown>
                <b-dropdown id="dropdown-inactive" title="Trạng thái" menu-class="p-0" class="app-dropdown-center main-header-icon">
                  <template v-slot:button-content>
                    <svg v-if="stage.filterInactive === 2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M18.6 6.62c-1.44 0-2.8.56-3.77 1.53L12 10.66 10.48 12h.01L7.8 14.39c-.64.64-1.49.99-2.4.99-1.87 0-3.39-1.51-3.39-3.38S3.53 8.62 5.4 8.62c.91 0 1.76.35 2.44 1.03l1.13 1 1.51-1.34L9.22 8.2C8.2 7.18 6.84 6.62 5.4 6.62 2.42 6.62 0 9.04 0 12s2.42 5.38 5.4 5.38c1.44 0 2.8-.56 3.77-1.53l2.83-2.5.01.01L13.52 12h-.01l2.69-2.39c.64-.64 1.49-.99 2.4-.99 1.87 0 3.39 1.51 3.39 3.38s-1.52 3.38-3.39 3.38c-.9 0-1.76-.35-2.44-1.03l-1.14-1.01-1.51 1.34 1.27 1.12c1.02 1.01 2.37 1.57 3.82 1.57 2.98 0 5.4-2.41 5.4-5.38s-2.42-5.37-5.4-5.37z"/><path fill="none" d="M0 0h24v24H0V0z"/></svg>
                    <i class="fa fa-circle-o m-0" v-if="stage.filterInactive === 0"></i>
                    <i class="fa fa-ban m-0" v-if="stage.filterInactive === 1"></i>
                  </template>
                  <b-dropdown-item @click="$_lists_handleFilterInactive(2)" title="Trạng thái">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" style="fill: #73818f" viewBox="0 0 24 24"><path d="M18.6 6.62c-1.44 0-2.8.56-3.77 1.53L12 10.66 10.48 12h.01L7.8 14.39c-.64.64-1.49.99-2.4.99-1.87 0-3.39-1.51-3.39-3.38S3.53 8.62 5.4 8.62c.91 0 1.76.35 2.44 1.03l1.13 1 1.51-1.34L9.22 8.2C8.2 7.18 6.84 6.62 5.4 6.62 2.42 6.62 0 9.04 0 12s2.42 5.38 5.4 5.38c1.44 0 2.8-.56 3.77-1.53l2.83-2.5.01.01L13.52 12h-.01l2.69-2.39c.64-.64 1.49-.99 2.4-.99 1.87 0 3.39 1.51 3.39 3.38s-1.52 3.38-3.39 3.38c-.9 0-1.76-.35-2.44-1.03l-1.14-1.01-1.51 1.34 1.27 1.12c1.02 1.01 2.37 1.57 3.82 1.57 2.98 0 5.4-2.41 5.4-5.38s-2.42-5.37-5.4-5.37z"/><path fill="none" d="M0 0h24v24H0V0z"/></svg>
                  </b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleFilterInactive(0)" title="Đang hoạt động"><i class="fa fa-circle-o m-0"></i></b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleFilterInactive(1)" title="Ngừng hoạt động"><i class="fa fa-ban m-0"></i></b-dropdown-item>
                </b-dropdown>
                <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
                  <sidebar-toggle class="d-md-down-none btn" display="lg" :defaultOpen=true />
                </div>
              </div>
            </b-col>
          </b-row>
        </div>
      </div>
      <div class="main-body" :class="(stage.viewType === 'list') ? 'main-body-view-list' : 'main-body-view-kanban'">
        <b-card class="m-0 border-0" body-class="py-0 px-0">
          <div class="content-body">
            <div class="content-table content-body-list" v-if="stage.viewType === 'list'" >
              <b-table ref="selectableTable"
                       :hover="settings.propsTable.hover"
                       :small="settings.propsTable.small"
                       :sticky-header="settings.propsTable.stickyHeader"
                       head-variant="light"
                       :fields="captions"
                       responsive
                       selectable
                       select-mode="multi"
                       @row-selected="$_lists_onRowSelected"
                       @row-clicked="onRowClicked"
                       selected-variant="active"
                       primary-key="index"
                       :items="itemsArray">

                <!-- A custom formatted header cell for field 'name' -->
                <template v-slot:head(selected)="data">
                  <b-form-checkbox class="text-left" @input="$_lists_onToggleSelectAllRows($event)" :checked="false"></b-form-checkbox>
                </template>


                <template slot="top-row" slot-scope="data">
                  <td :class="[(field.key == 'selected') ? 'pl-3' : '', (field.key == 'ItemName') ? 'pr-3' : '']" role="cell" style="z-index: 12" v-for="(field, key) in data.fields">
                    <!-- type input-->
                    <b-form-input
                      type="text" :placeholder="field.searchOnTopRow.placeholder"
                      v-if=" field.searchOnTopRow && field.searchOnTopRow.type == 'text' && key !== 0"
                      :name="field.field"
                      v-model="modelSearch[field.field]"
                      @keyup.enter="$_lists_handleSubmitSearch"
                      :autocomplete="field.field">
                    </b-form-input>

                    <!-- type slect -->
                    <b-form-select
                      v-if="field.searchOnTopRow && field.searchOnTopRow.type == 'select' && key !== 0"
                      :plain="true"
                      :name="field.field"
                      v-model="modelSearch[field.field]"
                      :options="field.searchOnTopRow.option"
                      @change="$_lists_handleSubmitSearch">
                    </b-form-select>
                  </td>
                </template>
                <template v-slot:cell(TaskDataflow)="data">
                  <div class="stepwizard overflow-auto">
                    <div v-if="dataflow.ProcessType !== 3 && dataflow.ProcessType !== 4 && dataflow.ProcessType !== 5" class="stepwizard-step no-overflow" v-for="(dataflow, index) in filterDataflowByTask(data.item)">
                      <button :id="'popover-target-' + dataflow.TaskID" :title="dataflow.FeatureStatusTitle" type="button" class="btn btn-primary btn-circle">
                        <i class="fa fa-spinner" v-if="!dataflow.StatusCompleted"></i>
                        <i class="fa fa-check" v-if="dataflow.StatusCompleted"></i>
                      </button>
                      <p class="m-0" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" :title="dataflow.WFItemName | stripHtml">{{dataflow.WFItemName | stripHtml}}</p>
                      <b-popover :target="'popover-target-' + dataflow.TaskID" triggers="hover" placement="top">
                        <template #title>{{dataflow.TaskName | stripHtml}}</template>
                        <div>- Trạng thái: {{dataflow.StatusDescription}}</div>
                        <div>- Người thực hiện: <ijcore-users-icon :all-users="model.taskAssignUsers" filter-name="TaskID" :filter-value="dataflow.TaskID" :number="6"></ijcore-users-icon></div>
                        <div>- Ngày bắt đầu: {{dataflow.StartDate | convertServerDateToClientDate}}</div>
                        <div>- Hạn hoàn thành: {{dataflow.DueDate | convertServerDateToClientDate}}</div>
                        <div>- Phần trăm hoàn thành: <b-badge :variant="(dataflow.PercentCompleted <= 0) ? 'warning' : (dataflow.PercentCompleted > 0 && dataflow.PercentCompleted < 100) ? 'primary' : 'success'">{{dataflow.PercentCompleted}}</b-badge></div>
                      </b-popover>
                    </div>
                  </div>
                </template>
                <template v-slot:cell(TaskName)="data">
                  <div><span :title="data.item.TaskName | stripHtml">{{data.item.TaskName | stripHtml}}</span></div>
                </template>
                <!-- Example scoped slot for select state illustrative purposes -->
                <template v-slot:cell(selected)="data">
                  <b-form-checkbox class="checkbox-selected" @change="$_lists_onToggleRowSelected(data)" :checked="data.rowSelected"></b-form-checkbox>
                </template>
              </b-table>
            </div>
          </div>
        </b-card>

      </div>
      <div class="main-footer">
        <div class="d-flex flex-wrap justify-content-between align-items-center m-0">
          <div class="main-footer-pagination">
            <div class="overflow-auto">
              <b-pagination
                v-model="currentPage"
                :total-rows="rows"
                :per-page="perPage"
                aria-controls="my-table"
                size="md"
              ></b-pagination>
            </div>
          </div>
          <div class="total-item">
            <span style="font-weight: 500">Tổng số: {{totalRows}}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import ApiService from '@/services/api.service';
import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
import 'v-calendar/lib/v-calendar.min.css';
import mixinLists from '@/mixins/lists';
import IjcoreUsersIcon from "@/components/IjcoreUsersIcon";

export default {
  name: 'dataflow-lists',
  mixins: [mixinLists],
  data() {
    return {
      model: {
        taskArray: [],
        dataflowArray: [],
        taskAssignUsers: [],
      }
    }
  },
  components:{
    IjcoreUsersIcon
  },

  computed: {

    rows() {
      return this.totalRows
    },
    inActiveOption() {
      return inActiveOption;
    },
    captions: function() {
      let fieldsTable = [
        {key: 'selected', label: '', thStyle: 'width: 0%',tdClass: 'pl-3 no-overflow table-vertical-align-middle', thClass: 'pl-3 no-overflow'},
        {key: 'TaskName', label: 'Tên', thStyle: 'width: 12%; min-width: 100px;', tdClass: 'table-vertical-align-middle', field: 'TaskName',searchOnTopRow: {type: 'text'}},
        {key: 'TaskDataflow', label: 'Luồng công việc',thStyle: 'width: 30%; min-width: 300px; z-index: 12', field: 'TaskDataflow'},
      ];

      return fieldsTable;
    }
  },
  created() {
    this.settings.FieldID = 'DFID';
    this.settings.Table = 'task_dataflow';
    this.settings.FieldInactive = 'Inactive';

    this.settings.DeleteApi = 'task/api/dataflow/delete';

    this.modelSearch = {
      TaskName: '',
      sortBy: 1
    };
    this.init();
  },
  updated() {
    this.stage.updatedData = true;
  },
  mounted() {
    this.$_lists_showMessage();
    this.stage.viewType = 'list';
  },
  methods: {
    init(){
      this.fetchData();
    },

    handleDebugger(data){
      console.log(data);
    },

    fetchData(){
      let self = this;
      let requestData = {
        method: 'post',
        url: 'task/api/dataflow/get-list-dataflow',
        data: {
          per_page: this.perPage,
          page: this.currentPage,
        },

      };

      if (this.modelSearch.sortBy) {
        requestData.data.sortBy = this.modelSearch.sortBy;
      }

      if (this.modelSearch.TaskName) {
        requestData.data.TaskName = this.modelSearch.TaskName;
      }

      console.log('request');
      console.log(requestData);

      this.$store.commit('isLoading', true);
      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;

        console.log('response');
        console.log(responseData);

        if (responseData.status === 1) {
          self.model.dataflowArray = responseData.data.dataflow;
          self.model.taskAssignUsers = responseData.data.taskAssignUsers;

          self.totalRows = responseData.data.task.total;
          self.perPage = String(responseData.data.task.per_page);
          self.currentPage = responseData.data.task.current_page;
          // converse object to array
          self.itemsArray = _.toArray(responseData.data.task.data);

          // set params request
          self.paramsReqRouter.lastPage = responseData.data.task.last_page;
          self.paramsReqRouter.from = responseData.data.task.from;
          self.paramsReqRouter.to = responseData.data.task.to;
          self.$_lists_setParamsReqRouter();
        }
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

      // scroll to top perfect scroll
      const container = document.querySelector('.b-table-sticky-header');
      if (container) container.scrollTop = 0;

    },
    onRowClicked(data){
      this.$router.push({
        path: '/task/dataflow/' + data.WFID + '/' + data.DFID
      });
    },

    filterDataflowByTask(task){
      if (!task) return [];
      return _.filter(this.model.dataflowArray, ['DFKey', task.DFKey]);
    },

    handleExportExcel() {
      // Todo: handle export excel
      alert('excel');
    },
    handleExportPrint() {
      // Todo: handle export print
      alert('print');
    },
  },
  watch: {
    currentPage() {
      this.fetchData();
    },
    'modelSearch.sortBy'() {
      this.page = 1;
      this.fetchData();
    }
  }
}
</script>

<style lang="css">
.main-footer-pagination ul {
  margin-bottom: 0;
}

.table-vertical-align-middle {
  vertical-align: middle !important;
}

.component-dataflow-list #dropdown-per-page button{
  /*border-top-left-radius: 4px;*/
  /*border-bottom-left-radius: 4px;*/
  /*border-left: 1px solid #c8ced3;*/
}

/*======================= process step ===============================*/
.stepwizard-step p {
  margin-top: 10px;
}
.stepwizard {
  display: flex;
  width: 100%;
  position: relative;
  overflow-x: auto;
  padding: 5px 0;
}
.stepwizard-step:before{
  top: 14px;
  bottom: 0;
  position: absolute;
  content: " ";
  width: 100%;
  height: 1px;
  background-color: #ccc;
  z-index: 1;
}
.stepwizard-step button {
  position: relative;
  z-index: 2;
}
.stepwizard-step button[disabled] {
  opacity: 1 !important;
  filter: alpha(opacity=100) !important;
}
.stepwizard-step {
  text-align: center;
  position: relative;
  padding-right: 60px;
  width: 160px;
  min-width: 160px;
}
.stepwizard .stepwizard-step:last-child:before {
  display: none;
}
.btn-circle {
  width: 30px;
  height: 30px;
  text-align: center;
  padding: 6px 0;
  font-size: 12px;
  line-height: 1.428571429;
  border-radius: 15px;
}

</style>
