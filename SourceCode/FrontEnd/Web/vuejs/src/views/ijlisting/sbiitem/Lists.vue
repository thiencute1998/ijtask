<template>
  <div class="component-sbi-item-list">
    <div class="main-entry">
      <div class="main-header">
        <div class="main-header-padding">
          <b-row class="mb-2">
            <b-col class="col-md-12">
              <div class="main-header-item main-header-name">
                <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Mục - Tiểu mục</span>
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
                  <SbiItemModalSearchLink>
                    v-model="filter.SbiItemLink"
                    ref="myModalSearchInputLink"
                    placeholder="Danh mục liên kết"
                    @onSubmitSearch="handleSubmitSearch"
                    @onClear="fetchData"
                    title-modal="Danh mục liên kết" size-modal="lg"
                    id-modal="myModalSearchInputLink">
                  </SbiItemModalSearchLink>
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
                       @row-clicked="$_lists_onRowClicked"
                       selected-variant="active"
                       primary-key="index"
                       :items="itemsArray"
                       :tbody-transition-props="transProps"
                       :filter="'tree'"
                       :filter-function="filterTable">

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
                <template v-slot:cell(SbiItemName)="data">
                  <div v-if="stage.viewType === 'tree'" class="field-sbi-item-name" :class="(!data.item.ParentID) ? 'sbi-item-parent' : 'sbi-item-children'">
                    <span class="dash" v-html="renderDashHtml(data.item.Level)" v-if="data.item.Level !== 1"></span>
                    <span class="sbi-item-icon" :class="'sbi-item-icon-' + data.item.SbiItemID" v-if="haveChildren(data.item)" @click="onToggleChildren($event,data.item)">
                                      <i class="fa fa-folder-open-o"></i>
                                    </span>
                    <span :title="data.item.SbiItemName | stripHtml">
                                        {{data.item.SbiItemName | stripHtml}}
                                    </span>
                  </div>
                  <span v-if="stage.viewType === 'list'" :title="data.item.SbiItemName">{{data.item.SbiItemName}}</span>
                </template>
                <template v-slot:cell(SbiItemNo)="data">
                  <span :title="data.item.SbiItemNo">{{data.item.SbiItemNo}}</span>
                </template>
                <template #cell(SbiItemType)="data">
                  <span>{{ItemTypeOption[data.item.SbiItemType]}}</span>
                </template>
                <template #cell(SbiItemGroup)="data">
                  <span>{{ItemGroupOption[data.item.SbiItemGroup]}}</span>
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
                      <span class="kanban-title">{{item.SbiItemName}}</span>
                    </div>
                    <div class="kanban-record">
                      <span>Mã số </span>
                      <span class="kanban-no">{{item.SbiItemNo}}</span>
                    </div>
                    <div class="kanban-record">
                      <span>Loại </span>
                      <span class="kanban-no">{{ItemTypeOption[item.SbiItemType]}}</span>
                    </div>
                    <div class="kanban-record">
                      <span>Nhóm </span>
                      <span class="kanban-no">{{ItemGroupOption[item.SbiItemGroup]}}</span>
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
import SbiItemModalSearchLink from "./partials/SbiItemModalSearchLink";

export default {
  name: 'listing-items',
  mixins: [mixinLists],
  data() {
    return {
      filter: {
        DateRange: (this.$route.query && this.$route.query.DateRange) ? this.$route.query.DateRange : null,
        SbiItemLink: (this.$route.query && this.$route.query.SbiItemLink) ? this.$route.query.SbiItemLink : [],
      },
      ItemTypeOption: [],
      ItemGroupOption: [],
      SbiItemTypeOption: [],
      SbiItemGroupOption: [],
      transProps: {
        // Transition name
        name: 'list'
      },
    }
  },
  components:{
    SbiItemModalSearchLink,
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
        {key: 'SbiItemNo', label: 'Mã mục - tiểu mục', field: 'SbiItemNo', thStyle: 'width: 12%; min-width: 100px', searchOnTopRow: {type: 'text'}},
        {key: 'SbiItemName', label: 'Tên mục - tiểu mục', thStyle: 'min-width: 300px', field: 'SbiItemName',searchOnTopRow: {type: 'text'}},
        {key: 'SbiItemType', label: 'Loại', thStyle: 'min-width: 120px', field: 'SbiItemType',searchOnTopRow: {type: 'select', option: this.SbiItemTypeOption}},
        {key: 'SbiItemGroup', label: 'Nhóm', thStyle: 'min-width: 120px', field: 'SbiItemGroup',searchOnTopRow: {type: 'select', option: this.SbiItemGroupOption}},
      ];

      return fieldsTable;
    }
  },
  created() {

    this.settings.FieldID = 'SbiItemID';
    this.settings.Table = 'sbi_item';
    this.settings.FieldInactive = 'Inactive';

    this.settings.ListApi = 'listing/api/sbi-item?XDEBUG_SESSION_START=PHPSTORM';
    this.settings.DeleteApi = 'listing/api/sbi-item/delete';
    this.settings.CreateRouter = 'listing-sbi-item-create';
    this.settings.EditRouter = 'listing-sbi-item-edit';
    this.settings.ViewRouter = 'listing-sbi-item-view';

    this.modelSearch = {
      SbiItemNo: (this.$route.query && this.$route.query.SbiItemNo) ? this.$route.query.SbiItemNo : '',
      SbiItemName: (this.$route.query && this.$route.query.SbiItemName) ? this.$route.query.SbiItemName : '',
      SbiItemType: (this.$route.query && this.$route.query.SbiItemType) ? this.$route.query.SbiItemType : null,
      SbiItemGroup: (this.$route.query && this.$route.query.SbiItemGroup) ? this.$route.query.SbiItemGroup : null,
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
          viewType: this.stage.viewType,
        },

      };

      if (this.modelSearch.SbiItemNo.trim() !== '') {
        requestData.data.SbiItemNo = this.modelSearch.SbiItemNo;
      }
      if (this.modelSearch.SbiItemName !== '') {
        requestData.data.SbiItemName = this.modelSearch.SbiItemName;
      }
      if (this.modelSearch.SbiItemType !== null) {
        requestData.data.SbiItemType = this.modelSearch.SbiItemType;
      }
      if (this.modelSearch.SbiItemGroup !== null) {
        requestData.data.SbiItemGroup = this.modelSearch.SbiItemGroup;
      }
      if (this.stage.filterInactive !== 2) {
        requestData.data.Inactive = this.stage.filterInactive;
      }

      if (this.fullTextSearch !== '') {
        requestData.data.fullTextSearch = this.fullTextSearch;
      }

      if (this.filter.SbiItemLink.length) {
        let haveLink = false;
        _.forEach(this.filter.SbiItemLink, function (sbi_itemLink, key) {
          if (sbi_itemLink.LinkID) {
            haveLink = true;
            return false;
          }
        });
        if (haveLink) {
          requestData.data.SbiItemLink = this.filter.SbiItemLink;
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
          // converse object to array
          self.itemsArray = _.toArray(dataResponse.data.data);
          self.itemsArray = _.uniqBy(self.itemsArray, 'SbiItemID');

          if (self.stage.viewType === 'tree') {
            let tmpItemsArray = [];
            _.forEach(self.itemsArray, function (item, key) {

                tmpItemsArray.push(item);
                if (!item.ParentID) {
                  let allChild = self.getAllChildSbiItem(item, self.itemsArray);
                  if (allChild.length) {
                    tmpItemsArray = _.concat(tmpItemsArray, allChild);
                  }
                }

            });

            // self.itemsArray = tmpItemsArray;
            self.itemsArray = _.uniqBy(tmpItemsArray, 'SbiItemID');
          }

          self.ItemTypeOption = dataResponse.ItemTypeOption;
          self.SbiItemTypeOption = [{value: null, text: '-- Tất cả --'}];
          _.forEach(dataResponse.ItemTypeOption, function(value, key){
            let tmpObj = {};
            tmpObj.value = key;
            tmpObj.text = value;
            self.SbiItemTypeOption.push(tmpObj);
          });

          self.ItemGroupOption = dataResponse.ItemGroupOption
          self.SbiItemGroupOption = [{value: null, text: '-- Tất cả --'}];
          _.forEach(dataResponse.ItemGroupOption, function(value, key){
            let tmpObj = {};
            tmpObj.value = key;
            tmpObj.text = value;
            self.SbiItemGroupOption.push(tmpObj);
          });

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

    renderDashHtml(level){
      let dash = '';
      for (let i = 0; i < level - 1; i++) {
        dash += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
      }
      return dash;
    },
    haveChildren(item){
      let children = _.filter(this.itemsArray, ['ParentID', item.SbiItemNo]);
      if (children.length > 0) {
        return true;
      }
      return false;
    },
    onToggleChildren($event,item){
      $event.stopPropagation();
      let allChildSbiItem = this.getAllChildSbiItem(item, this.itemsArray);
      let self = this;
      let $icon = $('.sbi-item-icon-' + item.SbiItemID + ' i');
      if ($icon.hasClass('fa-folder-o')) {
        // $('[data-parent-id=' + item.SbiItemID + ']').show();
        _.forEach(allChildSbiItem, function (sbiitem, key) {
          let _index = _.findIndex(self.itemsArray, ['SbiItemID', sbiitem.SbiItemID]);
          if (_index >= 0) {
            self.$set(self.itemsArray[_index], 'hidden', false);
          }
        });
        $('.sbi-item-icon-' + item.SbiItemID + ' i').removeClass('fa-folder-o').addClass('fa-folder-open-o');
      } else {
        // $('[data-parent-id=' + item.SbiItemID + ']').hide();
        _.forEach(allChildSbiItem, function (sbiitem, key) {
          let _index = _.findIndex(self.itemsArray, ['SbiItemID', sbiitem.SbiItemID]);
          if (_index >= 0) {
            self.$set(self.itemsArray[_index], 'hidden', true);
          }
        });
        $('.sbi-item-icon-' + item.SbiItemID + ' i').removeClass('fa-folder-open-o').addClass('fa-folder-o');
      }

    },
    getAllChildSbiItem(item, sbiitemArr){
      let self = this, listChild = [];
      let allChild = _.filter(sbiitemArr, ['ParentID', item.SbiItemNo]);
      if (allChild.length) {
        allChild = _.orderBy(allChild, ['SbiItemID'], ['asc']);
        _.forEach(allChild, function (value, key) {
          listChild.push(value);
          if (_.filter(sbiitemArr, ['ParentID', value.SbiItemNo]).length) {
            let recursiveArr = self.getAllChildSbiItem(value, sbiitemArr);
            recursiveArr = _.orderBy(recursiveArr, ['SbiItemID', 'asc']);
            _.forEach(recursiveArr, function (recusive, key) {
              listChild.push(recusive);
            });
          }

        });
      }
      return listChild;
    },
    filterTable(row,filter){
      if(this.stage.viewType == filter){
        if(row.hidden){
          return false;
        }
      }
      return true;
    },
    handleExportPrint() {
      // Todo: handle export print
      let request = {};
      if (this.modelSearch.SbiItemNo !== '') {
        request.SbiItemNo = this.modelSearch.SbiItemNo;
      }
      if (this.modelSearch.SbiItemName !== '') {
        request.SbiItemName = this.modelSearch.SbiItemName;
      }
      if (this.modelSearch.SbiItemType !== '') {
        request.SbiItemType = this.modelSearch.SbiItemType;
      }
      if (this.modelSearch.SbiItemGroup !== '') {
        request.SbiItemGroup = this.modelSearch.SbiItemGroup;
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
      if (this.filter.SbiItemLink.length) {
        let haveLink = false;
        _.forEach(this.filter.SbiItemLink, function (fixedAssetLink, key) {
          if (fixedAssetLink.LinkID) {
            haveLink = true;
            return false;
          }
        });
        if (haveLink) {
          request.SbiItemLink = this.filter.SbiItemLink;
        }
      }
      request.currentPage = 1;
      request.perPage = this.perPage;
      request.totalRows = this.totalRows;
      request.exportData = true;
      this.$router.push({
        name: 'listing-sbi-item-report',
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
      let reportJson = await this.$_lists_handleReportTemplate('sbi_item');
      let request = {};
      if (this.modelSearch.SbiItemNo !== '') {
        request.SbiItemNo = this.modelSearch.SbiItemNo;
      }
      if (this.modelSearch.SbiItemName !== '') {
        request.SbiItemName = this.modelSearch.SbiItemName;
      }
      if (this.stage.filterInactive !== 2) {
        request.Inactive = this.stage.filterInactive;
      }
      if (this.fullTextSearch !== '') {
        request.fullTextSearch = this.fullTextSearch;
      }
      if (this.filter.SbiItemLink.length) {
        let haveLink = false;
        _.forEach(this.filter.SbiItemLink, function (sbi_itemLink, key) {
          if (sbi_itemLink.LinkID) {
            haveLink = true;
            return false;
          }
        });
        if (haveLink) {
          request.SbiItemLink = this.filter.SbiItemLink;
        }
      }
      request.exportData = true;

      let reportData = await this.$_lists_handleReportResponse('listing/api/sbi-item/get-report-data', request);
      this.$_lists_handleDowloadExcel(reportJson, reportData, 'SbiItem');
    },
  },
  watch: {
    currentPage() {
      this.fetchData();
    },
    'stage.viewType'(){
      this.fetchData();
      this.$store.commit('optionBehavior', {'viewType': this.stage.viewType});
    }
  }
}
</script>

<style lang="css">
.main-footer-pagination ul {
  margin-bottom: 0;
}
.component-sbi-item-list .main-body-view-tree .sbi-item-children {
  font-style: italic;
}
table {
  transition: transform 1s;
}
.list-enter-active, .list-leave-active {
  transition: all 1s;
}
.list-enter, .list-leave-to /* .list-leave-active below version 2.1.8 */ {
  opacity: 0;
  transform: translateY(30px);
}
</style>
