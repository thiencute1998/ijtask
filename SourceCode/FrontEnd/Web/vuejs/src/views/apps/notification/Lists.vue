<template>
  <div class="component-notification">
    <div class="main-entry">
      <div class="main-header">
        <div class="main-header-padding">
          <b-row class="mb-2">
            <b-col md="6" class="col-24">
              <div class="main-header-item main-header-name">
                <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Thông báo</span>
              </div>
            </b-col>
            <b-col md="6" class="col-24"></b-col>
          </b-row>
          <b-row class="mb-2">
            <b-col class="col-lg-16 col-md-24 col-sm-24 col-24 mb-2 mb-lg-0 d-lg-flex justify-content-start align-items-center">
              <div class="main-header-item main-header-actions">
                <b-dropdown variant="primary" id="dropdown-actions" @toggle="$_lists_onToggleActionMajor" class="main-header-action mr-2" text="Thao tác">
                  <b-dropdown-item :disabled="stage.disableActions" @click="changeStatusNotice($event, 1)">Đánh dấu là đã đọc</b-dropdown-item>
                  <b-dropdown-item :disabled="stage.disableActions" @click="changeStatusNotice($event, 2)">Đánh dấu là không đọc</b-dropdown-item>
                  <b-dropdown-item>In</b-dropdown-item>
                  <li class="dropdown b-dropdown dropright">
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
                </b-dropdown>
              </div>
            </b-col>
            <b-col class="col-lg-8 col-md-24 col-sm-24 col-24">
              <div class="main-header-item main-header-icons">
                <b-button-group id="main-header-views" class="main-header-views">
                  <b-button id="tooltip-view-filter" v-b-toggle.collapse-main-header-filter title="Lọc" class="main-header-view"><i class="fa fa-filter"></i></b-button>
                </b-button-group>
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
                    <svg xmlns="http://www.w3.org/2000/svg" style="fill: #73818f" width="16" height="16" viewBox="0 0 24 24"><path d="M18.6 6.62c-1.44 0-2.8.56-3.77 1.53L12 10.66 10.48 12h.01L7.8 14.39c-.64.64-1.49.99-2.4.99-1.87 0-3.39-1.51-3.39-3.38S3.53 8.62 5.4 8.62c.91 0 1.76.35 2.44 1.03l1.13 1 1.51-1.34L9.22 8.2C8.2 7.18 6.84 6.62 5.4 6.62 2.42 6.62 0 9.04 0 12s2.42 5.38 5.4 5.38c1.44 0 2.8-.56 3.77-1.53l2.83-2.5.01.01L13.52 12h-.01l2.69-2.39c.64-.64 1.49-.99 2.4-.99 1.87 0 3.39 1.51 3.39 3.38s-1.52 3.38-3.39 3.38c-.9 0-1.76-.35-2.44-1.03l-1.14-1.01-1.51 1.34 1.27 1.12c1.02 1.01 2.37 1.57 3.82 1.57 2.98 0 5.4-2.41 5.4-5.38s-2.42-5.37-5.4-5.37z"/><path fill="none" d="M0 0h24v24H0V0z"/></svg>
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
          <b-collapse id="collapse-main-header-filter">
            <div class="main-header-filter pt-2">
              <div class="row mb-2">
                <div class="col-8 mb-2">
                  <ijcore-date-range v-model="modelSearch.DateRange"></ijcore-date-range>
                </div>

                <div class="col-4 mb-2">
                  <ijcore-select2-server
                    v-model="modelSearch.UserCreate"
                    :url="$store.state.appRootApi + '/task/api/common/get-employee'"
                    title="Người tạo"
                    id-name="UserID"
                    text-name="EmployeeName"
                    placeholder="Người tạo"
                    :allowClear="true"
                    :delay="1000">
                  </ijcore-select2-server>
                </div>

                <div class="col-4 mb-2">
                  <b-form-select title="Loại thông báo" v-model="modelSearch.Type" :options="[{value: 1, text: 'Gửi đi'}, {value: 2, text: 'Nhận về'}]"></b-form-select>
                </div>
                <b-col>
                  <div class="main-action d-lg-flex justify-content-end">
                    <b-button variant="primary" size="md" @click="fetchData">
                      <i class="fa fa-search"></i> Tìm
                    </b-button>
                  </div>
                </b-col>
              </div>
            </div>
          </b-collapse>

        </div>

      </div>
      <div class="main-body" :class="[(stage.viewType !== 'kanban') ? 'main-body-view-list' : 'main-body-view-kanban', (stage.viewType === 'tree') ? 'main-body-view-tree' : '']">
        <b-card class="m-0 border-0" body-class="py-0 px-0">
          <div class="content-body">
            <div class="content-table content-body-list" v-if="stage.viewType === 'list' || stage.viewType === 'tree'">
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
                       selected-variant="active"
                       primary-key="index"
                       :items="itemsArray">

                <!-- A custom formatted header cell for field 'name' -->
                <template v-slot:head(selected)="data">
                  <b-form-checkbox class="text-left" @input="$_lists_onToggleSelectAllRows($event)" :checked="false"></b-form-checkbox>
                </template>
                <template slot="top-row" slot-scope="data">
                  <td :class="[(field.key == 'selected') ? 'pl-3' : '', (field.key == 'ItemName') ? 'pr-3' : '', (field.key == 'CreateDate') ? 'no-overflow' : '']" role="cell" v-for="(field, key) in data.fields">
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

                    <ijcore-compare-time
                      v-if="field.searchOnTopRow && field.searchOnTopRow.type == 'compare-time' && key !== 0"
                      @clear-date-picker="fetchData"
                      v-model="modelSearch[field.field]">
                    </ijcore-compare-time>

                  </td>
                </template>

                <template v-slot:cell(Description)="data">
                  <span :title="data.item.Description | stripHtml" v-html="data.item.Description" @click="onClickNotice(data.item)"></span>
                </template>

                <template v-slot:cell(CreateDate)="data">
                  <span>{{data.item.CreateDate | convertServerDateToClientDate}}</span>
                </template>

                <template v-slot:cell(UserReceive)="data">
                  <notification-modal-user-receive :notification-i-d="data.item.NotificationID"></notification-modal-user-receive>
                </template>

                <template v-slot:cell(Status)="data">
                  <span v-if="data.item.Status === 0">Chưa đọc</span>
                  <span v-if="data.item.Status === 1">Đã đọc</span>
                  <span v-if="data.item.Status === 2">Không đọc</span>
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
  import mixinLists from '@/mixins/lists';
  import IjcoreCompareTime from "@/components/IjcoreCompareTime";
  import NotificationModalUserReceive from "./NotificationModalUserReceive";
  import IjcoreDateRange from "../../../components/IjcoreDateRange";
  import IjcoreSelect2Server from "../../../components/IjcoreSelect2Server";

  export default {
    name: 'apps-notification',
    mixins: [mixinLists],
    data() {
      return {
        selectedRows: [],
        model: {
        },
        stage: {
          viewType: (this.$store.state.optionBehavior.viewType) ? this.$store.state.optionBehavior.viewType : 'list',
        },

        Priority: [],
        PriorityOptions: [],
        AccessType: [],
        AccessTypeOptions: [],
        TypeOptions: []
      }
    },
    components:{
      IjcoreCompareTime,
      NotificationModalUserReceive,
      IjcoreDateRange,
      IjcoreSelect2Server
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
          {key: 'selected', label: '', thStyle: 'width: 2%',tdClass: 'pl-3', thClass: 'pl-3'},
          {key: 'Description', label: 'Nội dung thông báo', field: 'Description',searchOnTopRow: {type: 'text'}, thStyle: 'width: 50%'},
          {key: 'CreateDate', label: 'Ngày thông báo', tdClass: 'text-center', field: 'CreateDate',searchOnTopRow: {type: 'compare-time'}, thStyle: 'width: 11%'},
          {key: 'Status', label: 'Trạng thái', field: 'Status',searchOnTopRow: {type: 'select',
              option: [
                {value: null, text: '-- Tất cả --'},
                {value: 0, text: 'Chưa đọc'},
                {value: 1, text: 'Đã đọc'},
                {value: 2, text: 'Không đọc'},
                ]},
            thStyle: 'width: 8%'},
          {key: 'UserName', label: 'Người gửi', field: 'UserName',searchOnTopRow: {type: 'text'}, thStyle: 'width: 8%'},
          {key: 'UserReceive', label: 'Người nhận', field: 'UserReceive', tdClass: 'text-center', thStyle: 'width: 8%'}
        ];

        return fieldsTable;
      }
    },
    created() {
      // init setting
      this.settings.FieldID = 'NotificationID';
      this.settings.Table = 'notification';
      this.settings.FieldInactive = 'Inactive';

      this.settings.ListApi = 'extensions/api/notice';

      this.modelSearch = {
        Description: '',
        CreateDate: null,
        UserName: '',
        Status: (!_.isUndefined(this.$route.params.Status)) ? this.$route.params.Status : null,
        DateRange: null,
        UserCreate: null,
        Type: 2
      };
      this.init();
    },
    updated() {
      this.stage.updatedData = true;
    },
    mounted() {
      this.$_lists_showMessage();
    },
    methods: {
      init(){
        this.fetchData();
      },
      fetchData(){
        let self = this;
        let urlApi = this.settings.ListApi;
        let requestData = {
          method: 'post',
          url: urlApi,
          data: {
            per_page: this.perPage,
            page: this.currentPage,
          },

        };

        if (this.modelSearch.Description) {
          requestData.data.Description = this.modelSearch.Description;
        }

        if (this.modelSearch.UserCreate) {
          requestData.data.UserCreate = this.modelSearch.UserCreate;
        }

        if (this.modelSearch.CreateDate && !_.isEmpty(this.modelSearch.CreateDate.dateTime) && this.modelSearch.CreateDate.dateTime !== '__/__/____') {
          requestData.data.CreateDate = this.modelSearch.CreateDate;
        }

        if (this.modelSearch.Type) {
          requestData.data.Type = this.modelSearch.Type;
        }

        if (this.modelSearch.DateRange && this.modelSearch.DateRange.fromDate && this.modelSearch.DateRange.fromDate !== '' && this.modelSearch.DateRange.fromDate !== '__/__/____') {
          requestData.data.fromDate = this.modelSearch.DateRange.fromDate;
        }
        if (this.modelSearch.DateRange && this.modelSearch.DateRange.toDate && this.modelSearch.DateRange.toDate !== '' && this.modelSearch.DateRange.toDate !== '__/__/____') {
          requestData.data.toDate = this.modelSearch.DateRange.toDate;
        }

        if (this.modelSearch.Status || this.modelSearch.Status == 0) {
          requestData.data.Status = this.modelSearch.Status;
        }

        if (this.modelSearch.UserName) {
          requestData.data.UserName = this.modelSearch.UserName;
        }

        if (this.fullTextSearch !== '') {
          requestData.data.fullTextSearch = this.fullTextSearch;
        }

        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let responseData = response.data;

          if (responseData.status === 1) {
            self.totalRows = responseData.data.total;
            self.perPage = String(responseData.data.per_page);
            self.currentPage = responseData.data.current_page;

            self.itemsArray = responseData.data.data;
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });

        // scroll to top perfect scroll
        const container = document.querySelector('.b-table-sticky-header');
        if (container) container.scrollTop = 0;

      },


      handleExportExcel() {
        // Todo: handle export excel
        alert('excel');
      },
      handleExportPrint() {
        // Todo: handle export print
        alert('print');
      },
      autoCorrectedDatePipe: () => { return createAutoCorrectedDatePipe('dd/mm/yyyy') },
      changeStatusNotice(e, Status){
        let self = this;

        let requestData = {
          method: 'post',
          url: 'extensions/api/notice/update-status-notice',
          data: {
            array_id: this.idsSelected,
            Status: Status
          },
        };

        ApiService.customRequest(requestData).then((response) => {
          let responseData = response.data;
          if (responseData.status === 1) {

            // update notification vuex
            _.forEach(this.idsSelected, function (id, key) {
              let notice = _.find(self.itemsArray, ['NotificationID', Number(id)]);
              if (notice && notice.Status === 0) {
                let indexNotice = _.findIndex(self.$store.state.notification.notice, ['NotificationID', Number(id)]);
                if (indexNotice > -1) {
                  self.$store.commit('removeNotification', indexNotice);
                }
              }

              let indexItem = _.findIndex(self.itemsArray, ['NotificationID', Number(id)]);
              if (indexItem > -1) {
                self.itemsArray[indexItem].Status = Status;
              }
            });

            this.$bvToast.toast('Cập nhật thành công', {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });

          } else {
            this.$bvToast.toast('Cập nhật thất bại', {
              title: 'Thông báo',
              variant: 'warning',
              solid: true
            });
          }
        }, (error) => {
          console.log(error);
        });
      },
      onClickNotice(notice) {
        if (notice.TypeCategory === 1) {
          this.$store.commit('notification', {TypeCategory: notice.TypeCategory, TypeAction: notice.TypeAction});
          if (this.$store.state.notification.CategoryID !== notice.CategoryID) {
            this.$router.push({
              name: 'task-task-view',
              params: {id: notice.CategoryID, TypeAction: notice.TypeAction}
            });
          } else {

          }
          this.$store.commit('notification', {CategoryID: notice.CategoryID});
        }

        if (notice.TypeCategory === 2) {
          this.$router.push({
            path: notice.Link
          });
        }
      },
    },
    watch: {
      currentPage() {
        this.fetchData();
      },
      'modelSearch.CreateDate.dateTime'(){
        if (this.modelSearch.CreateDate.dateTime) {
          this.$_lists_handleSubmitSearch();
        }
      },

      'modelSearch.CreateDate.operator'(){
        if (this.modelSearch.CreateDate.dateTime && this.modelSearch.CreateDate.dateTime !== '__/__/____') {
          this.$_lists_handleSubmitSearch();
        }
      }
    },
  }
</script>

<style lang="css">
  .component-notification .notice-username,
  .component-notification b{
    font-weight: 500;
    color: #050505;
  }
  .component-notification .component-compare-time .form-group{
    margin: 0;
  }
  .component-notification #dropdown-per-page .dropdown-toggle{
    /*border-left: 1px solid #c8ced3;*/
    /*border-top-left-radius: 0.25rem;;*/
    /*border-bottom-left-radius: 0.25rem;;*/
  }
  .component-notification .component-select2-server .select2-container{
    width: 100% !important;
  }
</style>
