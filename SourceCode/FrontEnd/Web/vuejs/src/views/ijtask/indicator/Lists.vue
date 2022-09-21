<template>
  <div>
    <div class="main-entry">
      <div class="main-header">
        <div class="main-header-padding">
          <b-row class="mb-2">
            <b-col md="6">
              <div class="main-header-item main-header-name">
                <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Chỉ tiêu đánh giá công việc</span>
              </div>
            </b-col>
            <b-col md="6"></b-col>
          </b-row>
          <b-row class="mb-2">
            <b-col
              class="col-lg-12 col-md-24 col-sm-24 col-24 mb-4 mb-lg-0 d-lg-flex justify-content-start align-items-center">
              <div class="main-header-item main-header-actions">
                <b-button class="main-header-action mr-2" variant="primary" @click="$_lists_handleAddNewItem" size="md">
                  <i class="fa fa-plus"></i> Thêm
                </b-button>
                <!--                                <b-button v-b-toggle.collapse-main-header-modelSearch class="main-header-action mr-2" variant="primary">-->
                <!--                                    <i class="fa fa-modelSearch"></i> Lọc-->
                <!--                                </b-button>-->

                <b-dropdown variant="primary" id="dropdown-actions" @toggle="$_lists_onToggleActionMajor"
                            class="main-header-action mr-2" text="Thao tác">
                  <b-dropdown-item @click="$_lists_handleDeleteItem" :disabled="stage.disableActions">Xóa
                  </b-dropdown-item>
                  <b-dropdown-item>In</b-dropdown-item>
                  <li class="dropdown b-dropdown dropright">
                    <a
                      class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0"
                      data-toggle="dropdown" @click.stop="$_lists_onToggleDropdownSubMenu($event)" href="#">Nhập</a>
                    <ul role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-menu m-0">
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Excel</a>
                      </li>
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Cvs</a>
                      </li>
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Xml</a>
                      </li>
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Json</a>
                      </li>
                    </ul>
                  </li>
                  <li class="dropdown b-dropdown dropright">
                    <a
                      class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0"
                      :class="[stage.disableActions ? 'disabled' : '']" data-toggle="dropdown"
                      @click.stop="onToggleDropdownSubMenu($event)" href="#">Xuất</a>
                    <ul role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-menu m-0">
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Excel</a>
                      </li>
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Cvs</a>
                      </li>
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Xml</a>
                      </li>
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Json</a>
                      </li>
                    </ul>
                  </li>
                  <b-dropdown-item :disabled="stage.disableActions">Chia sẻ</b-dropdown-item>
                  <b-dropdown-item :disabled="stage.disableActions">Chat</b-dropdown-item>
                  <b-dropdown-item :disabled="stage.disableActions">Zalo</b-dropdown-item>
                  <b-dropdown-item :disabled="stage.disableActions">Phân quyền</b-dropdown-item>
                  <b-dropdown-item :disabled="stage.disableActions || !stage.actionInactive.showInactive"
                                   v-if="stage.actionInactive.inactive == 0" @click="$_lists_handleChangeInActive">Đang
                    hoạt động
                  </b-dropdown-item>
                  <b-dropdown-item :disabled="stage.disableActions || !stage.actionInactive.showInactive"
                                   v-if="stage.actionInactive.inactive == 1" @click="$_lists_handleChangeInActive">Ngừng
                    hoạt động
                  </b-dropdown-item>
                </b-dropdown>
              </div>
              <div class="main-header-item main-header-search" style="flex: 1 1 auto">
                <div class="search-input">
                  <input v-model="fullTextSearch" @keyup.enter="$_lists_handleFullTextSearch" type="text"
                         placeholder="Tìm kiếm..."/>
                </div>
                <span class="search-icon" @click="$_lists_handleFullTextSearch"><i class="fa fa-search"></i></span>
              </div>
            </b-col>
            <b-col class="col-lg-12 col-md-24 col-sm-24 col-24">
              <div class="main-header-item main-header-icons">
                <b-button-group id="main-header-views" class="main-header-views">
                  <b-dropdown id="dropdown-view-type" title="Loại hiển thị" menu-class="p-0"
                              :class="[(stage.viewType === 'list' || stage.viewType === 'tree' || stage.viewType === 'select') ? 'view-active' : '']"
                              class="app-dropdown-center" toggle-class="main-header-view">
                    <template v-slot:button-content>
                      <i class="fa fa-tree" v-if="stage.viewType === 'tree'"></i>
                      <i class="fa fa-list" v-else></i>
                    </template>
                    <b-dropdown-item id="tooltip-view-list" title="Danh sách" @click="stage.viewType = 'list'"><i
                      class="fa fa-list m-0"></i></b-dropdown-item>
                    <b-dropdown-item id="tooltip-view-tree" title="Cây" @click="stage.viewType = 'tree'"><i
                      class="fa fa-tree m-0"></i></b-dropdown-item>
                  </b-dropdown>
                  <b-button id="tooltip-view-kanban" title="Thẻ tin" @click="stage.viewType = 'kanban'"
                            :class="[(stage.viewType === 'kanban') ? 'view-active' : '']" class="main-header-view"><i
                    class="fa fa-th"></i></b-button>
                </b-button-group>
                <b-button id="tooltip-view-filter" v-b-toggle.collapse-main-header-filter title="Lọc" class="main-header-view"><i class="fa fa-filter"></i></b-button>
                <b-dropdown id="dropdown-per-page" title="Số bản ghi/trang" menu-class="p-0" :text="perPage"
                            class="app-dropdown-center main-header-icon">
                  <b-dropdown-item @click="$_lists_handleChangePerPage(10)">10</b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleChangePerPage(15)">15</b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleChangePerPage(20)">20</b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleChangePerPage(30)">30</b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleChangePerPage(40)">40</b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleChangePerPage(50)">50</b-dropdown-item>
                </b-dropdown>
                <b-dropdown id="dropdown-inactive" title="Trạng thái" menu-class="p-0"
                            class="app-dropdown-center main-header-icon">
                  <template v-slot:button-content>
                    <!--                              <i class="fa fa-random m-0" v-if="stage.modelSearchInactive === 2"></i>-->
                    <svg v-if="stage.modelSearchInactive === 2" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                         viewBox="0 0 24 24">
                      <path
                        d="M18.6 6.62c-1.44 0-2.8.56-3.77 1.53L12 10.66 10.48 12h.01L7.8 14.39c-.64.64-1.49.99-2.4.99-1.87 0-3.39-1.51-3.39-3.38S3.53 8.62 5.4 8.62c.91 0 1.76.35 2.44 1.03l1.13 1 1.51-1.34L9.22 8.2C8.2 7.18 6.84 6.62 5.4 6.62 2.42 6.62 0 9.04 0 12s2.42 5.38 5.4 5.38c1.44 0 2.8-.56 3.77-1.53l2.83-2.5.01.01L13.52 12h-.01l2.69-2.39c.64-.64 1.49-.99 2.4-.99 1.87 0 3.39 1.51 3.39 3.38s-1.52 3.38-3.39 3.38c-.9 0-1.76-.35-2.44-1.03l-1.14-1.01-1.51 1.34 1.27 1.12c1.02 1.01 2.37 1.57 3.82 1.57 2.98 0 5.4-2.41 5.4-5.38s-2.42-5.37-5.4-5.37z"/>
                      <path fill="none" d="M0 0h24v24H0V0z"/>
                    </svg>
                    <i class="fa fa-circle-o m-0" v-if="stage.modelSearchInactive === 0"></i>
                    <i class="fa fa-ban m-0" v-if="stage.modelSearchInactive === 1"></i>
                  </template>
                  <b-dropdown-item @click="$_lists_handleFilterInactive(2)" title="Trạng thái">
                    <!--                              <i class="fa fa-random m-0"></i>-->
                    <svg xmlns="http://www.w3.org/2000/svg" style="fill: #73818f" width="16" height="16"
                         viewBox="0 0 24 24">
                      <path
                        d="M18.6 6.62c-1.44 0-2.8.56-3.77 1.53L12 10.66 10.48 12h.01L7.8 14.39c-.64.64-1.49.99-2.4.99-1.87 0-3.39-1.51-3.39-3.38S3.53 8.62 5.4 8.62c.91 0 1.76.35 2.44 1.03l1.13 1 1.51-1.34L9.22 8.2C8.2 7.18 6.84 6.62 5.4 6.62 2.42 6.62 0 9.04 0 12s2.42 5.38 5.4 5.38c1.44 0 2.8-.56 3.77-1.53l2.83-2.5.01.01L13.52 12h-.01l2.69-2.39c.64-.64 1.49-.99 2.4-.99 1.87 0 3.39 1.51 3.39 3.38s-1.52 3.38-3.39 3.38c-.9 0-1.76-.35-2.44-1.03l-1.14-1.01-1.51 1.34 1.27 1.12c1.02 1.01 2.37 1.57 3.82 1.57 2.98 0 5.4-2.41 5.4-5.38s-2.42-5.37-5.4-5.37z"/>
                      <path fill="none" d="M0 0h24v24H0V0z"/>
                    </svg>
                  </b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleFilterInactive(0)" title="Đang hoạt động"><i
                    class="fa fa-circle-o m-0"></i></b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleFilterInactive(1)" title="Ngừng hoạt động"><i
                    class="fa fa-ban m-0"></i></b-dropdown-item>
                </b-dropdown>
                <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
                  <sidebar-toggle class="d-md-down-none btn" display="lg" :defaultOpen="true"/>
                </div>
              </div>
            </b-col>
          </b-row>

          <b-collapse id="collapse-main-header-filter">
            <div class="main-header-filter pt-2">
              <div class="row mb-2">
                <div class="col-12 mb-2">
                  <ijcore-modal-cate-list
                    v-model="filter.IndicatorCate"
                    table-cate-list="task_indicator_cate_list"
                    table-cate-value="task_indicator_cate_value"
                    :field-update-cate-value="['ConvertedValue', 'ValueID']"
                    object-i-d="IndicatorID"
                    title="Loại chỉ tiêu"
                    :is-filter="true"
                    @saved="fetchData"
                    @onClear="fetchData"
                    placeholder="Chọn loại chỉ tiêu"></ijcore-modal-cate-list>
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
      <div class="main-body"
           :class="[(stage.viewType !== 'kanban') ? 'main-body-view-list' : 'main-body-view-kanban', (stage.viewType === 'tree') ? 'main-body-view-tree' : '']">
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

                <!--                                :striped="propsTable.striped"-->

                <!-- A custom formatted header cell for field 'name' -->
                <template v-slot:head(selected)="data">
                  <b-form-checkbox class="text-left" @input="$_lists_onToggleSelectAllRows($event)"
                                   :checked="false"></b-form-checkbox>
                </template>
                <template slot="top-row" slot-scope="data">
                  <td :class="[(field.key == 'selected') ? 'pl-3' : '', (field.key == 'ItemName') ? 'pr-3' : '']"
                      role="cell" v-for="(field, key) in data.fields">
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
                <!-- Example scoped slot for select state illustrative purposes -->
                <template v-slot:cell(selected)="data">
                  <b-form-checkbox class="checkbox-selected" @change="$_lists_onToggleRowSelected(data)"
                                   :checked="data.rowSelected"></b-form-checkbox>
                </template>
              </b-table>
            </div>
            <div class="content-body-kanban" v-if="stage.viewType === 'kanban'">
              <div class="kanban-items row">
                <div class="kanban-item col-lg-6 col-md-8 col-sm-12 col-24" v-for="(item, key) in itemsArray">
                  <div class="kanban-item-inner" @click="$_lists_onFieldClicked(item)">
                    <div class="kanban-record d-flex justify-content-between">
                      <span class="kanban-title">{{item.IndicatorName}}</span>
                      <span class="kanban-no">{{item.IndicatorNo}}</span>
                    </div>
                    <div class="kanban-record">
                      <span class="kanban-text" v-if="item.Tel && item.Tel !== ''">{{item.Tel}}</span>
                    </div>
                    <div class="kanban-record">
                      <span class="kanban-text" v-if="item.Fax && item.Fax !== ''">{{item.Fax}}</span>
                    </div>
                    <div class="kanban-record">
                      <span class="kanban-text" v-if="item.Email && item.Email !== ''">{{item.Email}}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="content-table content-body-list" v-if="stage.viewType === 'tree'">
              <div class="b-table-sticky-header table-responsive">
                <table role="table" aria-busy="false" aria-colcount="2" aria-multiselectable="true"
                       class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
                  <thead role="rowgroup" class="thead-light">
                  <tr role="row" class="">
                    <th role="columnheader" scope="col" aria-label="Selected" class="pl-3" style="width: 5%;">
                      <b-form-checkbox class="text-left" @input="onToggleSelectAllRows($event)"></b-form-checkbox>
                    </th>
                    <th role="columnheader" scope="col" style="width: 12%;">Mã chỉ tiêu</th>
                    <th role="columnheader" scope="col" style="min-width: 250px">Tên chỉ tiêu</th>
                  </tr>
                  </thead>
                  <transition-group tag="tbody" name="slide-fade">
                    <tr role="row" :key="'table-top-row'" class="b-table-top-row">
                      <td role="cell" class="pl-3"></td>
                      <td role="cell" class="">
                        <b-form-input
                          v-model="modelSearch.IndicatorNo"
                          @keyup.enter="$_lists_handleSubmitSearch"></b-form-input>
                      </td>
                      <td role="cell" class="">
                        <b-form-input
                          v-model="modelSearch.IndicatorName"
                          @keyup.enter="$_lists_handleSubmitSearch"></b-form-input>
                      </td>
                    </tr>
                    <tr role="row" tabindex="0"
                        aria-selected="false"
                        :class="[(!item.ParentID) ? 'task-parent' : 'task-children', (itemsArray[key].rowSelected) ? 'b-table-row-selected table-active' : '']"
                        :data-parent-id="item.ParentID"
                        v-if="!itemsArray[key].hidden"
                        :key="key"
                        v-for="(item, key) in itemsArray">
                      <td aria-colindex="1" role="cell" class="pl-3">
                        <b-form-checkbox v-model="itemsArray[key].rowSelected" class="text-left"
                                         @input="onToggleRowSelected($event, item, key)"></b-form-checkbox>
                      </td>
                      <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="text-left">
                        <span>{{item.IndicatorNo}}</span>
                      </td>
                      <td @click="onRowClicked(item)" aria-colindex="2" role="cell">
                                  <span v-if="!item.ParentID" v-show="false">
                                    {{style='padding-left: '+(item.Level-1)*15+'px; font-weight: 500'}}
                                  </span>
                        <span v-else v-show="false">
                                    {{style='padding-left: '+(item.Level-1)*15+'px;'}}
                                  </span>
                        <span :style="style">{{item.IndicatorName}}</span>
                      </td>
                    </tr>
                  </transition-group>
                  <!--                            <tbody role="rowgroup">-->
                  <!--                              -->
                  <!--                              </tbody>-->
                </table>
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
  import IjcoreModalCateList from "../../../components/IjcoreModalCateList";
  import mixinLists from '@/mixins/lists';

  export default {
    name: 'task-items',
    mixins: [mixinLists],
    data() {
      return {
        selectedRows: [],
        modelSearch: {
          IndicatorNo: '',
          IndicatorName: '',
        },
        filter: {
          IndicatorCate: []
        }
      }
    },
    components: {IjcoreModalCateList},
    computed: {
      rows() {
        return this.totalRows
      },
      inActiveOption() {
        return inActiveOption;
      },
      captions: function () {
        let fieldsTable = [
          {key: 'selected', label: '', thStyle: 'width: 5%', tdClass: 'pl-3', thClass: 'pl-3'},
          {
            key: 'IndicatorNo',
            label: 'Mã chỉ tiêu',
            field: 'IndicatorNo',
            thStyle: 'width: 12%; min-width: 100px',
            searchOnTopRow: {type: 'text'}
          },
          {
            key: 'IndicatorName',
            label: 'Tên chỉ tiêu',
            thStyle: 'min-width: 300px',
            field: 'IndicatorName',
            searchOnTopRow: {type: 'text'}
          },
        ];

        return fieldsTable;
      }
    },
    created() {

      this.stage.viewType = 'list';
      this.settings.FieldID = 'IndicatorID';
      this.settings.Table = 'indicator';
      this.settings.FieldInactive = 'Inactive';
      this.settings.ListApi = 'task/api/indicator';
      this.settings.DeleteApi = 'task/api/indicator/delete';
      this.settings.CreateRouter = 'task-indicator-create';
      this.settings.EditRouter = 'task-indicator-edit';
      this.settings.ViewRouter = 'task-indicator-view';

      this.modelSearch = {
        IndicatorNo: '',
        IndicatorName: '',
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
      init() {
        this.fetchData();
      },

      handleDebugger(data) {
        console.log(data);
      },

      fetchData() {
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
        let FlagSearch = 0;
        if (this.modelSearch.IndicatorNo.trim() !== '') {
          requestData.data.IndicatorNo = this.modelSearch.IndicatorNo;
          FlagSearch = 1;
        }
        if (this.modelSearch.IndicatorName !== '') {
          requestData.data.IndicatorName = this.modelSearch.IndicatorName;
          FlagSearch = 1;
        }

        if (this.fullTextSearch !== '') {
          requestData.data.fullTextSearch = this.fullTextSearch;
          FlagSearch = 1;
        }

        if (this.filter.IndicatorCate.length) {
          let haveIndicatorCate = false;
          _.forEach(this.filter.IndicatorCate, function (item, key) {
            if (item.CateID || item.CateValue) {
              haveIndicatorCate = true;
              return false;
            }
          });
          if (haveIndicatorCate) {
            requestData.data.IndicatorCate = this.filter.IndicatorCate;
          }
        }

        if(FlagSearch === 1 && this.stage.viewType === 'tree'){
          this.stage.viewType = 'list';
        }
        //console.log('');
        this.$store.commit('isLoading', true);
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
          Swal.fire({
            title: 'Thông báo',
            icon: 'warning',
            text: 'Phiên làm việc của bạn đã hết hạn. Vui lòng thử lại hoặc đăng nhập lại!',
            confirmButtonText: 'Đóng'
          });
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

      onRowClicked(item) {
        this.$router.push({
          name: this.settings.ViewRouter,
          params: {id: item[this.settings.FieldID], req: this.paramsReqRouter}
        });
      },
      onToggleRowSelected(event, item, key){
        let indexRow = _.findIndex(this.selectedRows, ['IndicatorID', item.IndicatorID]);
        if (event && (indexRow < 0)) {
          this.selectedRows.push(item);
        } else {
          this.selectedRows.splice(indexRow, 1);
        }
        this.$_lists_onRowSelected(this.selectedRows);
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
      autoCorrectedDatePipe: () => {
        return createAutoCorrectedDatePipe('dd/mm/yyyy')
      },
      scrollHandle(evt) {
      },
    },
    watch: {
      currentPage() {
        this.fetchData();
      },
    }
  }
</script>

<style lang="css">
  .main-footer-pagination ul {
    margin-bottom: 0;
  }
</style>
