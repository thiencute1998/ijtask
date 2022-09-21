<template>
    <div class="component-direction-list">
        <div class="main-entry">
            <div class="main-header">
                <div class="main-header-padding">
                    <b-row class="mb-2">
                        <b-col class="col-md-12">
                            <div class="main-header-item main-header-name">
                                <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Chỉ thị</span>
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
                          <!--                                <b-button v-b-toggle.collapse-main-header-filter class="main-header-action mr-2" variant="primary">-->
                          <!--                                    <i class="fa fa-filter"></i> Lọc-->
                          <!--                                </b-button>-->

                          <b-dropdown variant="primary" id="dropdown-actions" @toggle="$_lists_onToggleActionMajor" class="main-header-action mr-2" text="Thao tác">
                            <b-dropdown-item @click="$_lists_handleDeleteItem" :disabled="stage.disableActions">Xóa</b-dropdown-item>
                            <b-dropdown-item @click="handleExportPrint">In</b-dropdown-item>
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
                              <a class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" data-toggle="dropdown" @click.stop="$_lists_onToggleDropdownSubMenu($event)" href="#">Xuất</a>
                              <ul role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-menu m-0">
                                <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item" @click="handleExportExcel">Excel</a></li>
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
                            <b-dropdown id="dropdown-view-type" title="Loại hiển thị" menu-class="p-0" :class="[(stage.viewType === 'list' || stage.viewType === 'tree' || stage.viewType === 'select') ? 'view-active' : '']" class="app-dropdown-center" toggle-class="main-header-view">
                              <template v-slot:button-content>
                                <i class="fa fa-tree" v-if="stage.viewType === 'tree'"></i>
                                <i class="fa fa-list" v-else></i>
                              </template>
                              <b-dropdown-item id="tooltip-view-list" title="Danh sách" @click="stage.viewType = 'list'"><i class="fa fa-list m-0"></i></b-dropdown-item>
                              <b-dropdown-item id="tooltip-view-tree" title="Cây" @click="stage.viewType = 'tree'"><i class="fa fa-tree m-0"></i></b-dropdown-item>
                            </b-dropdown>
                            <b-button id="tooltip-view-kanban" title="Thẻ tin" @click="stage.viewType = 'kanban'" :class="[(stage.viewType === 'kanban') ? 'view-active' : '']" class="main-header-view"><i class="fa fa-th"></i></b-button>
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
<!--                              <i class="fa fa-random m-0" v-if="stage.filterInactive === 2"></i>-->
                              <svg v-if="stage.filterInactive === 2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M18.6 6.62c-1.44 0-2.8.56-3.77 1.53L12 10.66 10.48 12h.01L7.8 14.39c-.64.64-1.49.99-2.4.99-1.87 0-3.39-1.51-3.39-3.38S3.53 8.62 5.4 8.62c.91 0 1.76.35 2.44 1.03l1.13 1 1.51-1.34L9.22 8.2C8.2 7.18 6.84 6.62 5.4 6.62 2.42 6.62 0 9.04 0 12s2.42 5.38 5.4 5.38c1.44 0 2.8-.56 3.77-1.53l2.83-2.5.01.01L13.52 12h-.01l2.69-2.39c.64-.64 1.49-.99 2.4-.99 1.87 0 3.39 1.51 3.39 3.38s-1.52 3.38-3.39 3.38c-.9 0-1.76-.35-2.44-1.03l-1.14-1.01-1.51 1.34 1.27 1.12c1.02 1.01 2.37 1.57 3.82 1.57 2.98 0 5.4-2.41 5.4-5.38s-2.42-5.37-5.4-5.37z"/><path fill="none" d="M0 0h24v24H0V0z"/></svg>
                              <i class="fa fa-circle-o m-0" v-if="stage.filterInactive === 0"></i>
                              <i class="fa fa-ban m-0" v-if="stage.filterInactive === 1"></i>
                            </template>
                            <b-dropdown-item @click="$_lists_handleFilterInactive(2)" title="Trạng thái">
<!--                              <i class="fa fa-random m-0"></i>-->
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
                        <b-col>
                          <div class="main-action d-lg-flex justify-content-end">
                            <b-button variant="primary" size="md" @click="handleSubmitSearch">
                              <i class="fa fa-search"></i> Tìm
                            </b-button>
                          </div>
                        </b-col>
                      </div>

                    </div>
                  </b-collapse>
                </div>
            </div>
            <div class="main-body" :class="(stage.viewType === 'list') ? 'main-body-view-list' : 'main-body-view-kanban'">
                <b-card class="m-0 border-0" body-class="py-0 px-0">
                    <div class="content-body">
                      <div class="content-table content-body-list" v-if="stage.viewType === 'list'">
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
                           @row-clicked="$_lists_onRowClicked"
                           selected-variant="active"
                           primary-key="index"
                           :items="itemsArray">

                          <!-- A custom formatted header cell for field 'name' -->
                          <template v-slot:head(selected)="data">
                            <b-form-checkbox class="text-left" @input="$_lists_onToggleSelectAllRows($event)" :checked="false"></b-form-checkbox>
                          </template>
                          <template slot="top-row" slot-scope="data">
                            <td :class="[(field.key == 'selected') ? 'pl-3' : '', (field.key == 'ItemName') ? 'pr-3' : '']" role="cell" v-for="(field, key) in data.fields">
                                <!-- type input-->
                              <b-form-input
                                type="text" :placeholder="field.searchOnTopRow.placeholder"
                                v-if=" field.searchOnTopRow && field.searchOnTopRow.type == 'text' && key !== 0"
                                :name="field.field"
                                v-model="modelSearch[field.field]"
                                @keyup.enter="$_lists_handleSubmitSearch"
                                :autocomplete="field.field">
                              </b-form-input>

                              <!-- type select -->
                              <b-form-select
                                v-if="field.searchOnTopRow && field.searchOnTopRow.type == 'select' && key !== 0"
                                :plain="true"
                                :name="field.field"
                                v-model="modelSearch[field.field]"
                                :options="field.searchOnTopRow.option"
                                @change="$_lists_handleSubmitSearch">
                              </b-form-select>
                              <!-- type date -->
                              <ijcore-compare-time
                                v-if=" field.searchOnTopRow && field.searchOnTopRow.type == 'date' && key !== 0"
                                @clear-date-picker="fetchData"
                                v-model="modelSearch[field.field]">
                              </ijcore-compare-time>
                            </td>
                          </template>
                          <template v-slot:cell(DirectionName)="data">
                            <span :title="data.item.DirectionName">{{data.item.DirectionName}}</span>
                          </template>
                          <!-- Example scoped slot for select state illustrative purposes -->
                          <template v-slot:cell(selected)="data">
                              <b-form-checkbox class="checkbox-selected" @change="$_lists_onToggleRowSelected(data)" :checked="data.rowSelected"></b-form-checkbox>
                          </template>
                          <template v-slot:cell(DirectionDate)="data">
                            {{data.item.DirectionDate | convertServerDateToClientDate}}
                          </template>
                          <template v-slot:cell(Closed)="data">
                            <span v-if="(data.item.Closed) == 1"><i class="fa fa-check" aria-hidden="true"></i></span>
                          </template>
                        </b-table>
                      </div>
                      <div class="content-body-kanban" v-if="stage.viewType === 'kanban'">
                        <div class="kanban-items row">
                          <div class="kanban-item col-lg-6 col-md-8 col-sm-12 col-24" v-for="(item, key) in itemsArray">
                            <div class="kanban-item-inner" @click="$_lists_onFieldClicked(item)">
                              <div class="kanban-record d-flex justify-content-between">
                                <span class="kanban-title">{{item.DirectionName}}</span>
                              </div>
                              <div class="kanban-record">
                                <span>Mã số </span>
                                <span class="kanban-no">{{item.DirectionNo}}</span>
                              </div>
                              <div class="kanban-record" v-if="item.DirectionDate && item.DirectionDate !== null">
                                <span>Ngày ban hành </span>
                                <span class="kanban-text">{{item.DirectionDate| convertServerDateToClientDate}}</span>
                              </div>
<!--                              <div class="kanban-record" v-if="item.Closed && item.Closed !== null">-->
<!--                                <span>Đã đóng </span>-->
<!--                                <span class="kanban-text">{{item.Closed}}</span>-->
<!--                              </div>-->
                            </div>
                          </div>
                        </div>
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
    import Swal from 'sweetalert2';
    import 'sweetalert2/src/sweetalert2.scss';
    import mixinLists from '@/mixins/lists';
    import IjcoreCompareTime from "@/components/IjcoreCompareTime";

    export default {
        name: 'listing-items',
        mixins: [mixinLists],
        data() {
            return {
              filter: {
                DateRange: (this.$route.query && this.$route.query.DateRange) ? this.$route.query.DateRange : null,
              }
            }
        },
        components:{
          IjcoreCompareTime
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
                    {key: 'selected', label: '', thStyle: 'width: 5%',tdClass: 'pl-3 no-overflow', thClass: 'pl-3 no-overflow'},
                    {key: 'DirectionNo', label: 'Mã chỉ thị', field: 'DirectionNo', thStyle: 'width: 12%; min-width: 100px', searchOnTopRow: {type: 'text'}},
                    {key: 'DirectionName', label: 'Tên chỉ thị', thStyle: 'min-width: 300px', field: 'DirectionName',searchOnTopRow: {type: 'text'}},
                    {key: 'DirectionDate', label: 'Ngày ban hành', thStyle: 'width: 15%; min-width: 150px', field: 'DirectionDate',searchOnTopRow: {type: 'date'}, tdClass: 'text-center'},
                    {key: 'Closed', label: 'Đã đóng', field: 'Closed', thStyle: 'width: 8%; min-width: 80px', tdClass: 'text-center'},
                ];

                return fieldsTable;
            }
        },
        created() {

          this.settings.FieldID = 'DirectionID';
          this.settings.Table = 'direction';
          this.settings.FieldInactive = 'Inactive';

          this.settings.ListApi = 'listing/api/direction';
          this.settings.DeleteApi = 'listing/api/direction/delete';
          this.settings.CreateRouter = 'listing-direction-create';
          this.settings.EditRouter = 'listing-direction-edit';
          this.settings.ViewRouter = 'listing-direction-view';

          this.modelSearch = {
            DirectionNo: (this.$route.query && this.$route.query.DirectionNo) ? this.$route.query.DirectionNo : '',
            DirectionName: (this.$route.query && this.$route.query.DirectionName) ? this.$route.query.DirectionName : '',
            DirectionDate: (this.$route.query && this.$route.query.DirectionDate) ? this.$route.query.DirectionDate : null,
            // Closed: (this.$route.query && this.$route.query.Closed) ? this.$route.query.Closed : null,
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

            handleDebugger(data){
                console.log(data);
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

              if (this.modelSearch.DirectionNo.trim() !== '') {
                  requestData.data.DirectionNo = this.modelSearch.DirectionNo;
              }
              if (this.modelSearch.DirectionName !== '') {
                  requestData.data.DirectionName = this.modelSearch.DirectionName;
              }
              if (this.modelSearch.DirectionDate && !_.isEmpty(this.modelSearch.DirectionDate.dateTime) && this.modelSearch.DirectionDate.dateTime !== '__/__/____') {
                requestData.data.DirectionDate = this.modelSearch.DirectionDate;
              }
              // if (this.modelSearch.Closed !== null) {
              //     requestData.data.Closed = this.modelSearch.Closed;
              // }
              if (this.stage.filterInactive !== 2) {
                requestData.data.Inactive = this.stage.filterInactive;
              }

              if (this.fullTextSearch !== '') {
                  requestData.data.fullTextSearch = this.fullTextSearch;
              }

              // if (!_.isEmpty(this.filter) || !_.isEmpty(this.modelSearch)){
              //   this.queryReqRouter = _.merge(this.modelSearch, this.filter);
              //   if (!_.isEqual(this.$route.query, this.queryReqRouter)) {
              //     this.$router.replace({query: this.queryReqRouter});
              //   }
              // }

              this.$store.commit('isLoading', true);
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((response) => {
                  let dataResponse = response.data;

                  if (dataResponse.status === 1) {
                      self.totalRows = dataResponse.data.total;
                      self.perPage = String(dataResponse.data.per_page);
                      self.currentPage = dataResponse.data.current_page;
                      // converse object to array
                      self.itemsArray = _.toArray(dataResponse.data.data);

                      // set params request
                      self.paramsReqRouter.lastPage = dataResponse.data.last_page;
                      self.paramsReqRouter.from = dataResponse.data.from;
                      self.paramsReqRouter.to = dataResponse.data.to;
                      self.$_lists_setParamsReqRouter();
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

          handleExportPrint() {
              debugger
            // Todo: handle export print
            let request = {};
            if (this.modelSearch.DirectionNo !== '') {
              request.DirectionNo = this.modelSearch.DirectionNo;
            }
            if (this.modelSearch.DirectionName !== '') {
              request.DirectionName = this.modelSearch.DirectionName;
            }
            if (this.modelSearch.STT !== '') {
              request.STT = this.modelSearch.STT;
            }
            if (this.modelSearch.DirectionDate !== '') {
              request.DirectionDate = this.modelSearch.DirectionDate;
            }
            if (this.stage.filterInactive !== 2) {
              request.Inactive = this.stage.filterInactive;
            }
            if (this.fullTextSearch !== '') {
              request.fullTextSearch = this.fullTextSearch;
            }
            request.currentPage = 1;
            request.perPage = this.perPage;
            request.totalRows = this.totalRows;
            request.exportData = true;
            this.$router.push({
              name: 'listing-direction-report',
              query: request
            });
          },

            autoCorrectedDatePipe: () => { return createAutoCorrectedDatePipe('dd/mm/yyyy') },
            scrollHandle(evt){},
            handleSubmitSearch(){
              this.currentPage = 1;
              this.stage.viewType = 'list';
              this.fetchData();
            },
            async handleExportExcel() {
            // const viewer = this.$refs.reportViewer.Viewer();
            // viewer.open(reportResponse);
            let reportJson = await this.$_lists_handleReportTemplate('direction');
            let request = {};
            if (this.modelSearch.DirectionNo !== '') {
              request.DirectionNo = this.modelSearch.DirectionNo;
            }
            if (this.modelSearch.DirectionName !== '') {
              request.DirectionName = this.modelSearch.DirectionName;
            }
            if (this.modelSearch.DirectionDate && !_.isEmpty(this.modelSearch.DirectionDate.dateTime) && this.modelSearch.DirectionDate.dateTime !== '__/__/____') {
              request.data.DirectionDate = this.modelSearch.DirectionDate;
            }
            if (this.modelSearch.Closed !== null) {
              request.Closed = this.modelSearch.Closed;
            }
            if (this.stage.filterInactive !== 2) {
              request.Inactive = this.stage.filterInactive;
            }
            if (this.fullTextSearch !== '') {
              request.fullTextSearch = this.fullTextSearch;
            }
            request.exportData = true;

            let reportData = await this.$_lists_handleReportResponse('listing/api/direction/get-report-data', request);
            this.$_lists_handleDowloadExcel(reportJson, reportData, 'Direction');
          },
        },
        watch: {
            currentPage() {
                this.fetchData();
            },
            'modelSearch.DirectionDate.dateTime'(){
              if (this.modelSearch.DirectionDate.dateTime) {
                this.currentPage = 1;
                this.fetchData();
              }
            }
        }
    }
</script>

<style lang="css">
.main-footer-pagination ul {
  margin-bottom: 0;
}
.component-direction-list .component-compare-time .form-group {
  margin-bottom: 0;
}
.component-direction-list table .component-compare-time .dropdown-toggle,
.component-direction-list table .component-compare-number .dropdown-toggle{
  padding-left: 5px;
  padding-right: 5px;
}
/*.component-direction-list table .component-compare-time .mx-input-wrapper,*/
/*.component-direction-list table .component-compare-time .mx-datepicker{*/
/*  width: 105px !important*/
/*}*/
.component-direction-list table .component-compare-time .mx-datepicker input,
.component-direction-list .component-compare-number input{
  padding-left: 5px;
}
.component-direction-list .component-compare-number .form-group {
  margin-bottom: 0;
}
.component-direction-list .component-compare-time .mx-input-wrapper {
  width: auto !important;
}
.component-direction-list .custom-select {
  background-color: #fff;
}
</style>
