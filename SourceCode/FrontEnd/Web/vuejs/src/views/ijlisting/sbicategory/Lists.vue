<template>
  <div>
    <div class="main-entry">
      <div class="main-header">
        <div class="main-header-padding">
          <b-row class="mb-2">
            <b-col class="col-md-12">
              <div class="main-header-item main-header-name">
                <span class="text-capitalize"><i class="fa fa-list mr-2"></i>Loại khoản</span>
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
                <div class="col-6 mb-2">
                  <sbi-category-modal-search-link
                    v-model="filter.SbiCategoryLink"
                    ref="myModalSearchInputLink"
                    placeholder="Danh mục liên kết"
                    @onSubmitSearch="handleSubmitSearch"
                    @onClear="fetchData"
                    title-modal="Danh mục liên kết" size-modal="lg"
                    id-modal="myModalSearchInputLink">
                  </sbi-category-modal-search-link>
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
      <div class="main-body" :class="[(stage.viewType !== 'kanban') ? 'main-body-view-list' : 'main-body-view-kanban', (stage.viewType === 'tree') ? 'main-body-view-tree' : '']">
        <b-card class="m-0 border-0" body-class="py-0 px-0">
          <div class="content-body">
            <div class="content-table content-body-list"  v-if="stage.viewType === 'list' || stage.viewType === 'tree'" >
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
                       :items="itemsArray"
                       :filter="'tree'"
                       :filter-function="filterTable"
              >

                <!-- A custom formatted header cell for field 'name' -->
                <template v-slot:head(selected)="data">
                  <b-form-checkbox class="text-left" @input="$_lists_onToggleSelectAllRows($event)" :checked="false"></b-form-checkbox>
                </template>
                <template slot="top-row" slot-scope="data">
                  <td :class="[(field.key == 'selected') ? 'pl-3' : '', (field.key == 'ItemName') ? 'pr-3' : '']" role="cell" v-for="(field, key) in data.fields" style="z-index: 3" >
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
                <template v-slot:cell(SbiCategoryName)="data" v-if="stage.viewType === 'tree'">
                  <span class="bg-tree-dot" :style="{'left': (level * 17) + 'px'}" v-for="level in data.item.Level" ></span>

                  <div class="bg-tree-content bg-tree-td"
                       :id="'table-item-'+data.item.SbiCategoryNo"
                       :style="{'margin-left': (data.item.Level * 17 - 17) + 'px'}">
                    <span style="padding-left: 20px" :title="data.item.Name">{{data.item.SbiCategoryName}}</span>
                    <span
                      style="position: absolute;display: block;  width: 35px; height: 30px; z-index: 4;  left: 0px; opacity: 0"
                      @click="onToggleChildren($event, data.item)"
                      v-if="!data.item.Detail"
                    ></span>
                    <i
                      class="fa fa-plus-square-o bg-tree-icon-action"
                      v-if="data.item.Detail == 0"
                      @click="onToggleChildren($event, data.item)"
                    ></i>

                  </div>

                </template>
                <template v-else>

                  <span :title="data.item.SbiCategoryName">{{data.item.SbiCategoryName}} </span>

                </template>

                <!-- Example scoped slot for select state illustrative purposes -->
                <template v-slot:cell(selected)="data">
                  <b-form-checkbox class="checkbox-selected" @change="$_lists_onToggleRowSelected(data)" :checked="data.rowSelected"></b-form-checkbox>
                </template>
              </b-table>
            </div>
            <div class="content-body-kanban" v-if="stage.viewType === 'kanban'">
              <div class="kanban-items row">
                <div class="kanban-item col-lg-6 col-md-8 col-sm-12 col-24" v-for="(item, key) in itemsArray">
                  <div class="kanban-item-inner" @click="$_lists_onFieldClicked(item)">
                    <div class="kanban-record d-flex justify-content-between">
                      <span class="kanban-title">{{item.SbiCategoryName}}</span>
                      <span class="kanban-no">{{item.SbiCategoryNo}}</span>
                    </div>
                    <div class="kanban-record">
                      <span class="kanban-text" v-if="item.BalanceType && item.BalanceType !== ''">{{item.BalanceType}}</span>
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
import SbiCategoryModalSearchLink from "./partials/SbiCategoryModalSearchLink";

export default {
  name: 'listing-items',
  mixins: [mixinLists],
  data() {
    return {
      filter: {
        DateRange: (this.$route.query && this.$route.query.DateRange) ? this.$route.query.DateRange : null,
        SbiCategoryLink: (this.$route.query && this.$route.query.SbiCategoryLink) ? this.$route.query.SbiCategoryLink : [],
      }
    }
  },
  components:{
    SbiCategoryModalSearchLink,
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
        {key: 'SbiCategoryNo', label: 'Mã số', field: 'SbiCategoryNo', thStyle: 'width: 5%; min-width: 60px', searchOnTopRow: {type: 'text'}},
        {key: 'SbiCategoryName', label: 'Tên Loại khoản', thStyle: 'min-width: 300px; z-index:7', tdClass: 'bg-tree-tr', field: 'SbiCategoryName',searchOnTopRow: {type: 'text'}},
      ];

      return fieldsTable;
    }
  },
  created() {

    this.settings.FieldID = 'SbiCategoryID';
    this.settings.Table = 'sbi_category';
    this.settings.FieldInactive = 'Inactive';

    this.settings.ListApi = 'listing/api/sbi-category';
    this.settings.DeleteApi = 'listing/api/sbi-category/delete';
    this.settings.GetListChildApi = 'listing/api/sbi-category/get-list-child';
    this.settings.CreateRouter = 'listing-sbi-category-create';
    this.settings.EditRouter = 'listing-sbi-category-edit';
    this.settings.ViewRouter = 'listing-sbi-category-view';

    this.modelSearch = {
      SbiCategoryNo: (this.$route.query && this.$route.query.SbiCategoryNo) ? this.$route.query.SbiCategoryNo : '',
      SbiCategoryName: (this.$route.query && this.$route.query.SbiCategoryName) ? this.$route.query.SbiCategoryName : '',

    };
    this.stage.isBackToList = (this.$route.query.isBackToList) ? this.$route.query.isBackToList : false;

    if (this.stage.isBackToList) {
      this.totalRows = this.$route.params.total;
      this.perPage = this.$route.params.perPage;
      this.currentPage = this.$route.params.currentPage;
      // converse object to array
      this.itemsArray = this.$route.params.itemsArray;
      console.log(this.itemsArray);
      console.log(this.$route.params);
      // set params request
      this.paramsReqRouter.lastPage = this.$route.params.lastPage;
      this.paramsReqRouter.from = this.$route.params.from;
      this.paramsReqRouter.to = this.$route.params.to;
      this.$_lists_setParamsReqRouter();
      this.stage.isBackToList = false;
    } else {
      this.init();
    }

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
      let urlApi = this.settings.ListApi ;
      let requestData = {
        method: 'post',
        url: urlApi ,
        data: {
          per_page: this.perPage,
          page: this.currentPage,
          viewType: this.stage.viewType,
        },

      };

      if (this.modelSearch.SbiCategoryNo.trim() !== '') {
        requestData.data.SbiCategoryNo = this.modelSearch.SbiCategoryNo;
      }
      if (this.modelSearch.SbiCategoryName !== '') {
        requestData.data.SbiCategoryName = this.modelSearch.SbiCategoryName;
      }
      if (this.stage.filterInactive !== 2) {
        requestData.data.Inactive = this.stage.filterInactive;
      }

      if (this.fullTextSearch !== '') {
        requestData.data.fullTextSearch = this.fullTextSearch;
      }

      if (this.filter.SbiCategoryLink.length) {
        let haveLink = false;
        _.forEach(this.filter.SbiCategoryLink, function (sbiCategoryLink, key) {
          if (sbiCategoryLink.LinkID) {
            haveLink = true;
            return false;
          }
        });
        if (haveLink) {
          requestData.data.SbiCategoryLink = this.filter.SbiCategoryLink;
        }
      }

      if (!_.isEmpty(this.filter) || !_.isEmpty(this.modelSearch)){
        this.queryReqRouter = _.merge(this.modelSearch, this.filter);
        if (!_.isEqual(this.$route.query, this.queryReqRouter)) {
          this.$router.replace({query: this.queryReqRouter});
        }
      }


      if (!this.stage.isBackToList) {
        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let responseData = response.data;


          if (responseData.status === 1) {
            self.totalRows = responseData.data.total;
            self.perPage = String(responseData.data.per_page);
            self.currentPage = responseData.data.current_page;
            // converse object to array
            self.itemsArray = _.toArray(responseData.data.data);
            // if(self.stage.viewType === 'tree'){
            //   _.forEach(self.itemsArray, function (val, key){
            //     if(val.Level == 1){
            //        self.$set(self.itemsArray[key], 'hidden', false)
            //     } else {
            //       self.$set(self.itemsArray[key], 'hidden', true);
            //     }
            //   })
            // }

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
        this.stage.isBackToList = false;
      }

      // scroll to top perfect scroll
      const container = document.querySelector('.b-table-sticky-header');
      if (container) container.scrollTop = 0;

    },

    onToggleChildren($event,item){

      $event.preventDefault();
      $event.stopPropagation();
      let self = this;

      let allChildSbiCategory = this.getAllChildSbiCategory(item, this.itemsArray);
      let $icon = $('#table-item-' + item.SbiCategoryNo + ' .bg-tree-icon-action');
      if ($icon.hasClass('fa-plus-square-o')) {
        if(!self.haveChildren(item)){
          self.$store.commit('isLoading', true);
          self.getListChild(item.SbiCategoryID);
        }
        _.forEach(allChildSbiCategory, function (sbiCategory, key) {
          let _index = _.findIndex(self.itemsArray, ['SbiCategoryNo', sbiCategory.SbiCategoryNo]);
          if (_index >= 0) {
            if(sbiCategory.ParentID == item.SbiCategoryID){
              self.$set(self.itemsArray[_index], 'hidden', false);
            }
          }
        });

        $('#table-item-' + item.SbiCategoryNo + ' .bg-tree-icon-action').removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
      }
      else {
        _.forEach(allChildSbiCategory, function (sbiCategory, key) {
          let _index = _.findIndex(self.itemsArray, ['SbiCategoryNo', sbiCategory.SbiCategoryNo]);
          if (_index >= 0) {
            self.$set(self.itemsArray[_index], 'hidden', true);
          }
        });
        $('#table-item-' + item.SbiCategoryNo + ' .bg-tree-icon-action').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
      }

    },
    filterTable(row,filter){
      if(this.stage.viewType == filter){
        if(row.hidden){
          return false;
        }
      }
      return true;
    },
    getAllChildSbiCategory(item, sbiCategoryArr){
      let self = this, listChild = [];
      let allChild = _.filter(sbiCategoryArr, ['ParentID', item.SbiCategoryID]);
      if (allChild.length) {
        allChild = _.orderBy(allChild, ['SbiCategoryNo'], ['asc']);
        _.forEach(allChild, function (value, key) {
          listChild.push(value);
          if (_.filter(sbiCategoryArr, ['ParentID', value.SbiCategoryID]).length) {
            let recursiveArr = self.getAllChildSbiCategory(value, sbiCategoryArr);
            recursiveArr = _.orderBy(recursiveArr, ['SbiCategoryNo', 'asc']);
            _.forEach(recursiveArr, function (recusive, key) {
              listChild.push(recusive);
            });
          }

        });
      }
      return listChild;
    },
    getListChild(SbiCategoryID){

      let self = this;
      let requestData = {
        method: 'post',
        url: 'listing/api/sbi-category/get-list-child'+ '' ,
        data: {
          per_page: this.perPage,
          page: this.currentPage,
          ParentID: SbiCategoryID,
        },

      };

      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;
        if (responseData.status === 1) {
          let listChild = _.toArray(responseData.data);
          let keyParent = _.findIndex(self.itemsArray, ['SbiCategoryID', SbiCategoryID]);
          _.forEach(listChild, function (val, key){
            self.itemsArray = __.insertBeforeKey(self.itemsArray, keyParent + 1, val );
          });
        };
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });
      this.isBackToList = false;
    },


    haveChildren(item){
      let children = _.filter(this.itemsArray, ['ParentID', item.SbiCategoryID]);
      if (children.length > 0) {
        return true;
      }
      return false;
    },
    handleExportPrint() {
      // Todo: handle export print
      let request = {};
      if (this.modelSearch.SbicategoryNo !== '') {
        request.SbicategoryNo = this.modelSearch.SbicategoryNo;
      }
      if (this.modelSearch.SbicategoryName !== '') {
        request.SbicategoryName = this.modelSearch.SbicategoryName;
      }
      if (this.modelSearch.STT !== '') {
        request.STT = this.modelSearch.STT;
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
        name: 'listing-sbi-category-report',
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
      let reportJson = await this.$_lists_handleReportTemplate('sbi_category');
      let request = {};
      if (this.modelSearch.SbiCategoryNo !== '') {
        request.SbiCategoryNo = this.modelSearch.SbiCategoryNo;
      }
      if (this.modelSearch.SbiCategoryName !== '') {
        request.SbiCategoryName = this.modelSearch.SbiCategoryName;
      }

      if (this.stage.filterInactive !== 2) {
        request.Inactive = this.stage.filterInactive;
      }
      if (this.fullTextSearch !== '') {
        request.fullTextSearch = this.fullTextSearch;
      }
      if (this.filter.SbiCategoryLink.length) {
        let haveLink = false;
        _.forEach(this.filter.SbiCategoryLink, function (sbiCategoryLink, key) {
          if (sbiCategoryLink.LinkID) {
            haveLink = true;
            return false;
          }
        });
        if (haveLink) {
          request.SbiCategoryLink = this.filter.SbiCategoryLink;
        }
      }
      request.exportData = true;

      let reportData = await this.$_lists_handleReportResponse('listing/api/sbi-category/get-report-data', request);
      this.$_lists_handleDowloadExcel(reportJson, reportData, 'SbiCategory');
    },
  },
  watch: {
    currentPage() {
      this.fetchData();
    },
    'stage.viewType'(){
      this.fetchData();
      this.$store.commit('optionBehavior', {'viewType': this.stage.viewType});
    },
    'itemsArray'(){
      console.log(this.itemsArray);
    }
  }
}
</script>

<style lang="css">
.main-footer-pagination ul {
  margin-bottom: 0;
}
.table.b-table thead th {
  vertical-align: middle;
}
.bg-tree-tr{
  position: relative;
}
.bg-tree-content {
  display: flex;
  align-items: center;
  position: relative;
}
.bg-tree-content input {
  padding-left: 20px;
}
.bg-tree-td:before{
  display: inline-block;
  content: "";
  position: relative;
  top: 0;
  left: 13px;
  width: 16px;
  height: 0;
  border-top: 1px dotted #858585;
  z-index: 1;
}
.bg-tree-td:after{
  display: inline-block;
  content: "";
  position: absolute;
  top: 0;
  left: 12px;
  width: 1px;
  height: 100%;
  border-left: 1px dotted #858585;
  z-index: 1;
}
.bg-tree-dot {
  position: absolute;
  width: 1px;
  top: -2px;
  height: 100%;
  left: 12px;
}
.bg-tree-dot:before {
  display: inline-block;
  content: "";
  left: 0;
  width: 1px;
  height: 100%;
  border-left: 1px dotted #858585;
  z-index: 1;
}
.bg-tree-icon-action {
  position: absolute;
  left: 7px;
  background: #fff;
  z-index: 2;
  cursor: pointer;
}
.bg-tree-icon-action :hover{
  color: lightblue;
}
</style>
