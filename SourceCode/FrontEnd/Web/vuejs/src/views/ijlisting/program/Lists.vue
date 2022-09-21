<template>
    <div class="component-program-list">
        <div class="main-entry">
            <div class="main-header">
                <div class="main-header-padding">
                    <b-row class="mb-2">
                        <b-col class="col-md-12">
                            <div class="main-header-item main-header-name">
                                <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Chương trình mục tiêu</span>
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
                              <a class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" :class="[stage.disableActions ? 'disabled' : '']" data-toggle="dropdown" @click.stop="$_lists_onToggleDropdownSubMenu($event)" href="#">Xuất</a>
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
                        <div class="col-6 mb-2">
                          <program-modal-search-link
                            v-model="filter.ProgramLink"
                            ref="myModalSearchInputLink"
                            placeholder="Danh mục liên kết"
                            @onSubmitSearch="handleSubmitSearch"
                            @onClear="fetchData"
                            title-modal="Danh mục liên kết" size-modal="lg"
                            id-modal="myModalSearchInputLink">
                          </program-modal-search-link>
                        </div>

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
                        <div class="b-table-sticky-header table-responsive">
                          <table role="table" aria-busy="false" aria-colcount="2" aria-multiselectable="true"
                                 class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
                            <thead role="rowgroup" class="thead-light">
                            <tr role="row" class="">
                              <th role="columnheader" scope="col" aria-label="Selected" class="pl-3" style="width: 3%;">
                                <b-form-checkbox class="text-left" @input="onToggleSelectAllRows($event)"></b-form-checkbox>
                              </th>
                              <th role="columnheader" scope="col" style="width: 12%; min-width: 100px" aria-sort="none" @click="sortTableByField($event, 'ProgramNo')" v-if="stage.viewType === 'list'">Mã CTMT</th>
                              <th role="columnheader" scope="col" style="width: 12%; min-width: 100px" v-else>Mã CTMT</th>
                              <th role="columnheader" scope="col" style="width: 50%; min-width: 250px" aria-sort="none" @click="sortTableByField($event, 'ProgramName')" v-if="stage.viewType === 'list'">Tên chương trình mục tiêu</th>
                              <th role="columnheader" scope="col" style="width: 50%; min-width: 250px" v-else>Tên chương trình mục tiêu</th>
                              <th role="columnheader" scope="col" style="width: 12%; min-width: 120px" aria-sort="none" @click="sortTableByField($event, 'ProgramType')" v-if="stage.viewType === 'list'">Loại</th>
                              <th role="columnheader" scope="col" style="width: 12%; min-width: 120px" v-else>Loại</th>
                              <th role="columnheader" scope="col" style="width: 12%; min-width: 120px" aria-sort="none" @click="sortTableByField($event, 'ManagementLevel')" v-if="stage.viewType === 'list'">Cấp quản lý</th>
                              <th role="columnheader" scope="col" style="width: 12%; min-width: 120px" v-else>Cấp quản lý</th>
                            </tr>
                            </thead>
                            <transition-group tag="tbody" name="slide-fade">
                              <tr role="row" :key="'table-top-row'" class="b-table-top-row">
                                <td role="cell" class="pl-3"></td>
                                <td role="cell" class="">
                                  <b-form-input
                                    v-model="modelSearch.ProgramNo"
                                    @keyup.enter="$_lists_handleSubmitSearch"></b-form-input>
                                </td>
                                <td role="cell" class="">
                                  <b-form-input v-model="modelSearch.ProgramName" @keyup.enter="$_lists_handleSubmitSearch"></b-form-input>
                                </td>
                                <td role="cell" class="no-overflow">
                                  <b-form-select
                                    v-model="modelSearch.ProgramType"
                                    :options="ProgramTypeOption"
                                    @change="changeOption">
                                  </b-form-select>
                                </td>
                                <td role="cell" class="no-overflow">
                                  <b-form-select
                                    v-model="modelSearch.ManagementLevel"
                                    :options="ManagementLevelOption"
                                    @change="changeOption">
                                  </b-form-select>
                                </td>
                              </tr>
                              <tr role="row" tabindex="0"
                                  aria-selected="false"
                                  :class="[(!item.ParentID) ? 'program-parent' : 'program-children', (itemsArray[key].rowSelected) ? 'b-table-row-selected table-active' : '']"
                                  :data-parent-id="item.ParentID"
                                  v-if="!itemsArray[key].hidden"
                                  :key="key"
                                  v-for="(item, key) in itemsArray">
                                <td aria-colindex="1" role="cell" class="pl-3">
                                  <b-form-checkbox v-model="itemsArray[key].rowSelected" class="text-left" @input="onToggleRowSelected($event, item, key)"></b-form-checkbox>
                                </td>
                                <td aria-colindex="2" role="cell" class="field-program-name">
                                  <div v-if="stage.viewType === 'tree'">
                                    <span class="dash" v-html="renderDashHtml(item.Level)" v-if="item.Level !== 1"></span>
                                    <span class="program-icon" :class="'program-icon-' + item.ProgramID" v-if="haveChildren(item)" @click="onToggleChildren(item, key)">
                                      <i class="fa fa-folder-open-o"></i>
                                    </span>
                                    <span @click="onRowClicked(item)" :title="item.ProgramName | stripHtml">
                                        {{item.ProgramName | stripHtml}}
                                    </span>
                                  </div>
                                  <div @click="onRowClicked(item)">
                                    <span>{{item.ProgramNo}}</span>
                                  </div>
                                </td>
                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell" v-if="stage.viewType === 'list'">
                                  <span :title="item.ProgramName">{{item.ProgramName}}</span>
                                </td>
                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell">
                                  <span>{{ProgramType[item.ProgramType]}}</span>
                                </td>
                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell">
                                  <span>{{ManagementLevel[item.ManagementLevel]}}</span>
                                </td>
                              </tr>
                            </transition-group>
                            <!--                            <tbody role="rowgroup">-->
                            <!--                              -->
                            <!--                              </tbody>-->
                          </table>
                        </div>

                      </div>
                      <div class="content-body-kanban" v-if="stage.viewType === 'kanban'">
                        <div class="kanban-items row">
                          <div class="kanban-item col-lg-6 col-md-8 col-sm-12 col-24" v-for="(item, key) in itemsArray">
                            <div class="kanban-item-inner" @click="$_lists_onFieldClicked(item)">
                              <div class="kanban-record d-flex justify-content-between">
                                <span>Mã CTMT</span>
                                <span class="kanban-no">{{item.ProgramNo}}</span>
                              </div>
                              <div class="kanban-record">
                                <span class="kanban-title">{{item.ProgramName}}</span>
                              </div>
                              <div class="kanban-record">
                                <span>Loại</span>
                                <span class="kanban-text">{{ProgramType[item.ProgramType]}}</span>
                              </div>
                              <div class="kanban-record">
                                <span>Cấp quản lý</span>
                                <span class="kanban-text">{{ManagementLevel[item.ManagementLevel]}}</span>
                              </div>
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
    import ProgramModalSearchLink from "./partials/ProgramModalSearchLink";

    export default {
        name: 'listing-items',
        mixins: [mixinLists],
        data() {
            return {
              filter: {
                DateRange: (this.$route.query && this.$route.query.DateRange) ? this.$route.query.DateRange : null,
                ProgramLink: (this.$route.query && this.$route.query.ProgramLink) ? this.$route.query.ProgramLink : [],
              },
              ProgramType: [],
              ProgramTypeOption: [],
              Management: [],
              ManagementLevelOption: [],
            }
        },
        components:{
          ProgramModalSearchLink,
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
                    {key: 'ProgramNo', label: 'Mã chương trình mục tiêu', field: 'ProgramNo', thStyle: 'width: 12%; min-width: 100px', searchOnTopRow: {type: 'text'}, tdClass: 'text-center'},
                    {key: 'ProgramName', label: 'Tên chương trình mục tiêu', thStyle: 'min-width: 300px', field: 'ProgramName',searchOnTopRow: {type: 'text'}},
                    {key: 'ProgramType', label: 'Loại', thStyle: 'width: 15%; min-width: 150px', field: 'ProgramType',searchOnTopRow: {type: 'select'}},
                    {key: 'ManagementLevel', label: 'Cấp quản lý', field: 'ManagementLevel', thStyle: 'width: 15%; min-width: 150px', searchOnTopRow: {type: 'text'}},
                ];

                return fieldsTable;
            }
        },
        created() {

          this.settings.FieldID = 'ProgramID';
          this.settings.Table = 'program';
          this.settings.FieldInactive = 'Inactive';

          this.settings.ListApi = 'listing/api/program';
          this.settings.DeleteApi = 'listing/api/program/delete';
          this.settings.CreateRouter = 'listing-program-create';
          this.settings.EditRouter = 'listing-program-edit';
          this.settings.ViewRouter = 'listing-program-view';

          this.modelSearch = {
            ProgramNo: (this.$route.query && this.$route.query.ProgramNo) ? this.$route.query.ProgramNo : '',
            ProgramName: (this.$route.query && this.$route.query.ProgramName) ? this.$route.query.ProgramName : '',
            ProgramType: (this.$route.query && this.$route.query.ProgramType) ? this.$route.query.ProgramType : null,
            ManagementLevel: (this.$route.query && this.$route.query.ManagementLevel) ? this.$route.query.ManagementLevel : null,
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
          changeOption(event){
            this.fetchData();
          },
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

              if (this.modelSearch.ProgramNo.trim() !== '') {
                  requestData.data.ProgramNo = this.modelSearch.ProgramNo;
              }
              if (this.modelSearch.ProgramName !== '') {
                  requestData.data.ProgramName = this.modelSearch.ProgramName;
              }
              if (this.modelSearch.ProgramType !== '') {
                  requestData.data.ProgramType = this.modelSearch.ProgramType;
              }
              if (this.modelSearch.ManagementLevel !== '') {
                  requestData.data.ManagementLevel = this.modelSearch.ManagementLevel;
              }
              if (this.stage.filterInactive !== 2) {
                requestData.data.Inactive = this.stage.filterInactive;
              }

              if (this.fullTextSearch !== '') {
                  requestData.data.fullTextSearch = this.fullTextSearch;
              }

              if (this.filter.ProgramLink.length) {
                let haveLink = false;
                _.forEach(this.filter.ProgramLink, function (programLink, key) {
                  if (programLink.LinkID) {
                    haveLink = true;
                    return false;
                  }
                });
                if (haveLink) {
                  requestData.data.ProgramLink = this.filter.ProgramLink;
                }
              }

              if (!_.isEmpty(this.filter) || !_.isEmpty(this.modelSearch)){
                this.queryReqRouter = _.merge(this.modelSearch, this.filter);
                if (!_.isEqual(this.$route.query, this.queryReqRouter)) {
                  this.$router.replace({query: this.queryReqRouter});
                }
              }

              this.$store.commit('isLoading', true);
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((response) => {
                  let dataResponse = response.data;

                  if (dataResponse.status === 1) {
                      self.totalRows = dataResponse.data.total;
                      self.perPage = String(dataResponse.data.per_page);
                      self.currentPage = dataResponse.data.current_page;
                      self.itemsArray = [];

                      // ProgramTypeOption
                      self.ProgramType = dataResponse.ProgramTypeOption;
                      self.ProgramTypeOption = [{ value: null, text: '-- Tất cả --'}];
                      _.forEach(dataResponse.ProgramTypeOption, function(value, key){
                        let tmpObj = {};
                        tmpObj.value = key;
                        tmpObj.text = value;
                        self.ProgramTypeOption.push(tmpObj);
                      })

                      // ManagementLevelOption
                      self.ManagementLevel = dataResponse.ManagementLevelOption;
                      self.ManagementLevelOption = [{ value: null, text: '-- Tất cả --'}];
                      _.forEach(dataResponse.ManagementLevelOption, function(value, key){
                        let tmpObj = {};
                        tmpObj.value = key;
                        tmpObj.text = value;
                        self.ManagementLevelOption.push(tmpObj);
                      })
                      self.itemsArray = _.toArray(dataResponse.data.data);
                      // converse object to array

                      // self.itemsArray = _.toArray(dataResponse.data.data);
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
            // Todo: handle export print
            let request = {};
            if (this.modelSearch.ProgramNo !== '') {
              request.ProgramNo = this.modelSearch.ProgramNo;
            }
            if (this.modelSearch.ProgramName !== '') {
              request.ProgramName = this.modelSearch.ProgramName;
            }
            if (this.modelSearch.ProgramType !== '') {
              request.ProgramType = this.modelSearch.ProgramType;
            }
            if (this.modelSearch.STT !== '') {
              request.STT = this.modelSearch.STT;
            }
            if (this.modelSearch.ManagementLevel !== '') {
              request.ManagementLevel = this.modelSearch.ManagementLevel;
            }
            if (this.stage.filterInactive !== 2) {
              request.Inactive = this.stage.filterInactive;
            }
            if (this.fullTextSearch !== '') {
              request.fullTextSearch = this.fullTextSearch;
            }
            if (this.filter.ProgramLink.length) {
              let haveLink = false;
              _.forEach(this.filter.ProgramLink, function (programLink, key) {
                if (programLink.LinkID) {
                  haveLink = true;
                  return false;
                }
              });
              if (haveLink) {
                request.ProgramLink = this.filter.ProgramLink;
              }
            }
            request.currentPage = 1;
            request.perPage = this.perPage;
            request.totalRows = this.totalRows;
            request.exportData = true;
            this.$router.push({
              name: 'listing-program-report',
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
            let reportJson = await this.$_lists_handleReportTemplate('program');
            let request = {};
            if (this.modelSearch.ProgramNo !== '') {
              request.ProgramNo = this.modelSearch.ProgramNo;
            }
            if (this.modelSearch.ProgramName !== '') {
              request.ProgramName = this.modelSearch.ProgramName;
            }
            if (this.modelSearch.ProgramType !== '') {
              request.ProgramType = this.modelSearch.ProgramType;
            }
            if (this.modelSearch.ManagementLevel !== '') {
              request.ManagementLevel = this.modelSearch.ManagementLevel;
            }
            if (this.stage.filterInactive !== 2) {
              request.Inactive = this.stage.filterInactive;
            }
            if (this.fullTextSearch !== '') {
              request.fullTextSearch = this.fullTextSearch;
            }
            if (this.filter.ProgramLink.length) {
              let haveLink = false;
              _.forEach(this.filter.ProgramLink, function (programLink, key) {
                if (programLink.LinkID) {
                  haveLink = true;
                  return false;
                }
              });
              if (haveLink) {
                request.ProgramLink = this.filter.ProgramLink;
              }
            }
            request.exportData = true;

            let reportData = await this.$_lists_handleReportResponse('listing/api/program/get-report-data', request);
            this.$_lists_handleDowloadExcel(reportJson, reportData, 'Program');
          },
          sortTableByField(e, field){
            let ariaSort = $(e.target).attr('aria-sort');
            switch (ariaSort) {
              case 'none':
                $(e.target).attr('aria-sort', 'ascending');
                this.itemsArray = _.orderBy(this.itemsArray, [field], ['asc']);
                break;
              case 'ascending':
                $(e.target).attr('aria-sort', 'descending');
                this.itemsArray = _.orderBy(this.itemsArray, [field], ['desc']);
                break;
              case 'descending':
                $(e.target).attr('aria-sort', 'ascending');
                this.itemsArray = _.orderBy(this.itemsArray, [field], ['asc']);
                break;
              default:
                break;
            }
          },
          onRowClicked(item){
            this.$router.push({
              name: this.settings.ViewRouter,
              params: {id: item[this.settings.FieldID], req: this.paramsReqRouter},
              query: this.queryReqRouter
            });
          },
        },
        watch: {
            currentPage() {
                this.fetchData();
            }
        }
    }
</script>

<style lang="css">
    .main-footer-pagination ul {
        margin-bottom: 0;
    }
    .component-program-list .custom-select {
      background-color: #fff;
    }
</style>
