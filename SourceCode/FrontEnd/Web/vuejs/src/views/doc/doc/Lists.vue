<template>
    <div>
        <div class="main-entry">
            <div class="main-header">
                <div class="main-header-padding">
                  <b-row class="mb-2">
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-name">
                            <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Tài liệu</span>
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
<!--                            <i class="fa fa-random m-0" v-if="stage.filterInactive === 2"></i>-->
                            <svg v-if="stage.filterInactive === 2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M18.6 6.62c-1.44 0-2.8.56-3.77 1.53L12 10.66 10.48 12h.01L7.8 14.39c-.64.64-1.49.99-2.4.99-1.87 0-3.39-1.51-3.39-3.38S3.53 8.62 5.4 8.62c.91 0 1.76.35 2.44 1.03l1.13 1 1.51-1.34L9.22 8.2C8.2 7.18 6.84 6.62 5.4 6.62 2.42 6.62 0 9.04 0 12s2.42 5.38 5.4 5.38c1.44 0 2.8-.56 3.77-1.53l2.83-2.5.01.01L13.52 12h-.01l2.69-2.39c.64-.64 1.49-.99 2.4-.99 1.87 0 3.39 1.51 3.39 3.38s-1.52 3.38-3.39 3.38c-.9 0-1.76-.35-2.44-1.03l-1.14-1.01-1.51 1.34 1.27 1.12c1.02 1.01 2.37 1.57 3.82 1.57 2.98 0 5.4-2.41 5.4-5.38s-2.42-5.37-5.4-5.37z"/><path fill="none" d="M0 0h24v24H0V0z"/></svg>
                            <i class="fa fa-circle-o m-0" v-if="stage.filterInactive === 0"></i>
                            <i class="fa fa-ban m-0" v-if="stage.filterInactive === 1"></i>
                          </template>
                          <b-dropdown-item @click="$_lists_handleFilterInactive(2)" title="Trạng thái">
<!--                            <i class="fa fa-random m-0"></i>-->
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

                  <b-collapse id="collapse-main-header-filter">
                    <div class="main-header-filter pt-2">
                      <div class="row mb-2">
                        <div class="col-6 mb-2">
                          <doc-modal-search-link
                            v-model="filter.DocLink"
                            ref="myModalSearchInputLink"
                            placeholder="Danh mục liên kết"
                            @onSubmitSearch="handleSubmitSearch"
                            @onClear="fetchData"
                            title-modal="Danh mục liên kết" size-modal="lg"
                            id-modal="myModalSearchInputLink">
                          </doc-modal-search-link>
                        </div>

                        <div class="col-10 mb-2">
                          <ijcore-date-range v-model="filter.DateRange"></ijcore-date-range>
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
            <div class="main-body" :class="(stage.viewType !== 'kanban') ? 'main-body-view-list' : 'main-body-view-kanban'">
                <b-card class="m-0 border-0" body-class="py-0 px-0">
                    <div class="content-body">
                      <div class="content-table content-body-list" v-if="stage.viewType === 'list' || stage.viewType === 'tree'">
                        <div class="b-table-sticky-header table-responsive">
                          <table role="table" aria-busy="false" aria-colcount="2" aria-multiselectable="true"
                                 class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
                            <thead role="rowgroup" class="thead-light">
                            <tr role="row" class="">
                              <th role="columnheader" scope="col" aria-label="Selected" class="pl-3" style="width: 3%;">
                                <b-form-checkbox @input="onToggleSelectAllRows($event)" class="text-left"></b-form-checkbox>
                              </th>
                              <th role="columnheader" scope="col" style="min-width: 250px">Tên tài liệu</th>
                            </tr>
                            </thead>
<!--                            <transition-group tag="tbody" name="slide-fade">-->
<!--                            </transition-group>-->
                            <tbody role="rowgroup">
                              <tr role="row" :key="'table-top-row'" class="b-table-top-row">
                              <td role="cell" class="pl-3"></td>
                              <td role="cell" class="">
                                <b-form-input
                                  type="text"
                                  v-model="modelSearch.DocName"
                                  @keyup.enter="handleSubmitSearch">
                                </b-form-input>
                              </td>
                            </tr>
                              <tr class="bg-tree-tr" role="row" tabindex="0"
                                aria-selected="false"
                                :class="[(itemsArray[key].rowSelected) ? 'b-table-row-selected table-active' : '']"
                                :key="key"
                                v-if="item.IsShow"
                                v-for="(item, key) in itemsArray">
                              <td aria-colindex="1" role="cell" class="pl-3">
                                <b-form-checkbox v-model="itemsArray[key].rowSelected" @input="onToggleRowSelected($event, item, key)" class="text-left"></b-form-checkbox>
                              </td>
                              <td aria-colindex="2" role="cell"
                                  class="field-task-name"
                                  @click="onRowClicked(item)"
                                  v-if="stage.viewType === 'tree'"
                                  :style="'padding-left:' + 15 * (item.Level - 1) + 'px;'">
                                <div>
                                  <i @click="toggleListChild($event, item.DocID)" class="pl-2 pr-2 fa" :class="[(item.IsOpen) ? 'fa-folder-open-o' : 'fa-folder-o']" style="cursor: pointer"></i>
                                  <span>{{item.DocName}}</span>
                                </div>
                              </td>
                              <td @click="onRowClicked(item)" v-else>
                                <span>{{item.DocName}}</span>
                              </td>
                            </tr>
                            </tbody>
                          </table>
                        </div>

                      </div>
                      <div class="content-body-kanban" v-if="stage.viewType === 'kanban'">
                        <div class="kanban-items row">
                          <div class="kanban-item col-lg-6 col-md-8 col-sm-12 col-24" v-for="(item, key) in itemsArray">
                            <div class="kanban-item-inner" @click="$_lists_onFieldClicked(item)">
                              <div class="kanban-record d-flex justify-content-between">
                                <span class="kanban-title">{{item.DocName}}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                </b-card>

            </div>
            <div class="main-footer">
                <div class="d-lg-flex justify-content-between align-items-center m-0">
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
    import IjcoreDateRange from "../../../components/IjcoreDateRange";
    import mixinLists from '@/mixins/lists';
    import DocModalSearchLink from "./partials/DocModalSearchLink";

    export default {
        name: 'doc-items',
      mixins: [mixinLists],
        data() {
          return {
            selectedRows: [],
            model: {
              ParentID: null
            },
            filter: {
              DateRange: null,
              DocLink: []
            }
          }
        },
        components:{
          IjcoreDateRange,
          DocModalSearchLink
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
                    {key: 'selected', label: '', thStyle: 'width: 5%', tdClass: 'pl-3', thClass: 'pl-3'},
                    {key: 'DocNo', label: 'Mã nhà cung cấp', thStyle: 'width: 12%; min-width: 100px', field: 'DocNo',searchOnTopRow: {type: 'text'}},
                    {key: 'DocName', label: 'Tên nhà cung cấp', thStyle: 'width: 38%; min-width: 300px', field: 'DocName',searchOnTopRow: {type: 'text'}},
                    {key: 'OfficePhone', label: 'Số điện thoại', thStyle: 'width: 15%; min-width: 150px', field: 'OfficePhone',searchOnTopRow: {type: 'text'}},
                    {key: 'Fax', label: 'Fax', field: 'Fax', thStyle: 'width: 15%; min-width: 150px', searchOnTopRow: {type: 'text'}},
                    {key: 'Email', label: 'Email', field: 'Email', thStyle: 'width: 15%; min-width: 150px',searchOnTopRow: {type: 'text'}, tdClass: 'pr-3', thClass: 'pr-3'},
                ];

                return fieldsTable;
            }
        },
        created() {
          this.settings.FieldID = 'DocID';
          this.settings.Table = 'doc';
          this.settings.FieldInactive = 'Inactive';

          this.settings.ListApi = 'doc/api/doc';
          this.settings.DeleteApi = 'doc/api/doc/delete';
          this.settings.CreateRouter = 'doc-doc-create';
          this.settings.EditRouter = 'doc-doc-edit';
          this.settings.ViewRouter = 'doc-doc-view';
          this.modelSearch = {
            DocName: ''
          };
          this.stage.viewType = 'tree';
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
                  ViewType: this.stage.viewType
                }
            };

            if (this.modelSearch.DocName !== '') {
              requestData.data.DocName = this.modelSearch.DocName;
            }

            if (this.stage.filterInactive !== 2) {
              requestData.data.Inactive = this.stage.filterInactive;
            }

            if (this.filter.DateRange && this.filter.DateRange.fromDate && this.filter.DateRange.fromDate !== '' && this.filter.DateRange.fromDate !== '__/__/____') {
              requestData.data.fromDate = this.filter.DateRange.fromDate;
            }
            if (this.filter.DateRange && this.filter.DateRange.toDate && this.filter.DateRange.toDate !== '' && this.filter.DateRange.toDate !== '__/__/____') {
              requestData.data.toDate = this.filter.DateRange.toDate;
            }

            if (this.filter.DocLink.length) {
              let haveLink = false;
              _.forEach(this.filter.DocLink, function (docLink, key) {
                if (docLink.LinkID) {
                  haveLink = true;
                  return false;
                }
              });
              if (haveLink) {
                requestData.data.DocLink = this.filter.DocLink;
              }
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
                  // converse object to array
                  self.itemsArray = _.toArray(responseData.data.data);

                  _.forEach(self.itemsArray, function (item, key) {
                    self.itemsArray[key].IsOpen = false;
                    self.itemsArray[key].IsLoad = false;
                    self.itemsArray[key].IsShow = true;
                  });

                    // set params request
                  self.paramsReqRouter.lastPage = responseData.data.last_page;
                  self.paramsReqRouter.from = responseData.data.from;
                  self.paramsReqRouter.to = responseData.data.to;
                  self.$_lists_setParamsReqRouter();
                }
                self.$store.commit('isLoading', false);
            }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
            });
          },

          handleSubmitSearch(){
            this.currentPage = 1;
            this.stage.viewType = 'list';
            this.fetchData();
          },
          onRowClicked(item){
            this.$router.push({
              name: this.settings.ViewRouter,
              params: {id: item[this.settings.FieldID], req: this.paramsReqRouter}
            });
          },

          toggleListChild(e, DocID){
            e.preventDefault();
            e.stopPropagation();
            let document = _.find(this.itemsArray, ['DocID', DocID]);
            let indexDocument = _.findIndex(this.itemsArray, ['DocID', DocID]);
            let self = this;
            if (!document) return;
            if (!document.IsOpen) {
              if (!document.IsLoad) {
                this.getListChild(DocID);
              } else {
                _.forEach(this.itemsArray, function (doc, key) {
                  let str = self.itemsArray[indexDocument].Path ? self.itemsArray[indexDocument].Path + self.itemsArray[indexDocument].DocID + '-' : '-' + self.itemsArray[indexDocument].DocID + '-';
                  if(doc.Path && doc.Path.indexOf(str) >= 0){
                    self.itemsArray[key].IsShow = true;
                  }
                });
              }
            } else {
              _.forEach(this.itemsArray, function (doc, key) {
                let str = self.itemsArray[indexDocument].Path ? self.itemsArray[indexDocument].Path + self.itemsArray[indexDocument].DocID + '-' : '-' + self.itemsArray[indexDocument].DocID + '-';
                if(doc.Path && doc.Path.indexOf(str) >= 0){
                  self.itemsArray[key].IsShow = false;
                }
              });
            }

            this.itemsArray[indexDocument].IsOpen = !this.itemsArray[indexDocument].IsOpen;

          },

          getListChild(DocID){
            this.model.ParentID = DocID;
            let self = this;
            let requestData = {
              method: 'post',
              url: 'doc/api/doc/get-list-child',
              data: {
                per_page: this.perPage,
                page: this.currentPage,
                ParentID: DocID
              },

            };

            ApiService.customRequest(requestData).then((response) => {
              let responseData = response.data;
              if (responseData.status === 1) {
                let indexDoc = _.findIndex(self.itemsArray, ['DocID', DocID]);

                _.forEach(responseData.data, function (doc, key) {
                  let indexSplice = indexDoc;
                  responseData.data[key].IsOpen = false;
                  responseData.data[key].IsLoad = false;
                  responseData.data[key].IsShow = true;
                  self.itemsArray.splice(indexSplice + 1, 0, doc);
                });

                if (indexDoc > -1) {
                  self.itemsArray[indexDoc].IsOpen = true;
                  self.itemsArray[indexDoc].IsLoad = true;
                  self.itemsArray[indexDoc].IsShow = true;
                }

              }
              self.$store.commit('isLoading', false);
            }, (error) => {
              console.log(error);
              self.$store.commit('isLoading', false);
            });
          },

          onToggleSelectAllRows(value){
            let self = this;
            if (value) {
              _.forEach(this.itemsArray, function (item, key) {
                self.$set(self.itemsArray[key], 'rowSelected', true)
              });
            } else {
              _.forEach(this.itemsArray, function (item, key) {
                self.$set(self.itemsArray[key], 'rowSelected', false)
              });
            }
          },

          onToggleRowSelected(event, item, key){
            let indexRow = _.findIndex(this.selectedRows, ['DocID', item.DocID]);
            if (event && (indexRow < 0)) {
              this.selectedRows.push(item);
            } else {
              this.selectedRows.splice(indexRow, 1);
            }
            this.$_lists_onRowSelected(this.selectedRows);
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
          scrollHandle(evt){},
        },
        watch: {
          currentPage() {
            this.fetchData();
          },
          'stage.viewType'() {
            this.fetchData();
          },
          // 'filter.DateRange': {
          //   handler(val){
          //     // do stuff
          //     this.handleSubmitSearch();
          //   },
          //   deep: true
          // }
        }
    }
</script>

<style lang="css">
  .main-footer-pagination ul {
      margin-bottom: 0;
  }

  .bg-tree-tr{
    background-image: url("http://pabmis.vn/demo2/style/treeview/images/treeview-default-line.gif");
    background-position: 5px;
    background-repeat: no-repeat;
  }
  .bg-tree-td:before{
    display: inline-block;
    content: "";
    position: relative;
    top: -4px;
    left: -7px;
    width: 16px;
    height: 0;
    border-top: 1px dotted #858585;
    z-index: 1;
  }
  .bg-tree-td-parent:before{
    display: inline-block;
    content: "";
    position: relative;
    top: -4px;
    left: 8px;
    width: 6px;
    height: 0;
    border-top: 1px dotted #858585;
    z-index: 1;
  }
  .bg-tree-td{
    background-image: url("http://pabmis.vn/demo2/style/treeview/images/treeview-default-line.gif");
    background-position: 20px;
    background-repeat: no-repeat;
  }

</style>
