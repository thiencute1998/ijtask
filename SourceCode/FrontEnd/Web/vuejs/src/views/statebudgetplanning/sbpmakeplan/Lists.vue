<template>
    <div class="component-task-list">
        <div class="main-entry">
            <div class="main-header">
                <div class="main-header-padding">
                  <b-row class="mb-2">
                      <b-col md="6" class="col-24">
                          <div class="main-header-item main-header-name">
                              <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Lập dự toán</span>
                          </div>
                      </b-col>
                      <b-col md="6" class="col-24"></b-col>
                  </b-row>
                  <b-row class="mb-2">
                    <b-col class="col-lg-16 col-md-24 col-sm-24 col-24 mb-2 mb-lg-0 d-lg-flex justify-content-start align-items-center">
                      <div class="main-header-item main-header-actions">
                        <b-button class="main-header-action mr-2" variant="primary" @click="$_lists_handleAddNewItem" size="md">
                          <i class="fa fa-plus"></i> Thêm
                        </b-button>

                        <IjcoreModalTransTemp @changed="changeTemplate" v-model="TransTemp" :options="options" :OptionsTransType="OptionsTransType" :TransTypeID="8" :title="'ước thực hiện dự toán'" :api="'/state-budget-planning/api/sbpmakeplan/get-list'"></IjcoreModalTransTemp>

<!--                        <b-button class="main-header-action mr-2" variant="primary" @click="$_lists_handleAddNewItem" size="md">-->
<!--                          <i class="fa fa-book"></i> Ghi sổ-->
<!--                        </b-button>-->

                        <b-dropdown variant="primary" id="dropdown-actions" @toggle="$_lists_onToggleActionMajor" class="main-header-action mr-2" text="Thao tác">
                          <b-dropdown-item @click="handleExportPrint">In</b-dropdown-item>
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
                            <a class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" data-toggle="dropdown" @click.stop="$_lists_onToggleDropdownSubMenu($event)" href="#">Xuất</a>
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
                          <b-dropdown-item @click="$_lists_handleDeleteItem" :disabled="stage.disableActions">Xóa</b-dropdown-item>
                        </b-dropdown>
                      </div>
                      <div class="main-header-item main-header-search" style="flex: 1 1 auto">
                          <div class="search-input">
                              <input v-model="fullTextSearch" @keyup.enter="$_lists_handleFullTextSearch" type="text" placeholder="Tìm..."/>
                          </div>
                          <span class="search-icon" @click="$_lists_handleFullTextSearch"><i class="fa fa-search"></i></span>
                      </div>
                    </b-col>
                    <b-col class="col-lg-8 col-md-24 col-sm-24 col-24">
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
                        <div class="b-table-sticky-header table-responsive">
                          <table role="table" aria-busy="false" aria-colcount="2" aria-multiselectable="true"
                                 class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
                            <thead role="rowgroup" class="thead-light">
                            <tr role="row" class="">
                              <th role="columnheader" scope="col" aria-label="Selected" class="pl-3" style="width: 3%;">
                                  <b-form-checkbox class="text-left" @input="onToggleSelectAllRows($event)"></b-form-checkbox>
                              </th>
                              <th role="columnheader" scope="col" style="width: 8%; min-width: 120px" aria-sort="none" @click="sortTableByField($event, 'PostDate')" v-if="stage.viewType === 'list'">Ngày HT</th>
                              <th role="columnheader" scope="col" style="width: 8%; min-width: 120px" v-else>Ngày HT</th>
                              <th role="columnheader" scope="col" style="width: 8%; min-width: 120px" aria-sort="none" @click="sortTableByField($event, 'TransNo')" v-if="stage.viewType === 'list'">Số CTG</th>
                              <th role="columnheader" scope="col" style="width: 8%; min-width: 120px" v-else>Số CTG</th>
                              <th role="columnheader" scope="col" style="width: 8%; min-width: 120px" aria-sort="none" @click="sortTableByField($event, 'TransDate')" v-if="stage.viewType === 'list'">Ngày CTG</th>
                              <th role="columnheader" scope="col" style="width: 8%; min-width: 120px" v-else>Ngày CTG</th>

                              <th role="columnheader" scope="col" title="Diễn giải" style="width: 50%; min-width: 430px" aria-sort="none" @click="sortTableByField($event, 'Comment')" v-if="stage.viewType === 'list'">Diễn giải</th>
                              <th role="columnheader" scope="col" title="Diễn giải" style="width: 50%; min-width: 430px" v-else>Diễn giải</th>
                              <th role="columnheader" scope="col" style="width: 6%; min-width: 150px" aria-sort="none" @click="sortTableByField($event, 'LCTotalDebitAmount')" v-if="stage.viewType === 'list'">Tổng số tiền(đ)</th>
                              <th role="columnheader" scope="col" style="width: 6%; min-width: 120px" v-else>Tổng số tiền</th>
                              <th role="columnheader" scope="col" style="width: 6%; min-width: 20px"><i class="fa fa-pencil" aria-hidden="true" title="Đã ghi sổ"></i></th>

                            </tr>
                            </thead>
                            <tbody role="rowgroup">
<!--                            <transition-group tag="tbody" name="slide-fade">-->
                              <tr role="row" :key="'table-top-row'" class="b-table-top-row">
                                <td role="cell" class="pl-3"></td>
                                <td role="cell" class="">
                                  <IjcoreDatePicker v-model="modelSearch.PostDate"  @clear-date-picker="fetchData" style="width: 100%;"></IjcoreDatePicker>
                                </td>
                                <td role="cell" class="">
                                  <b-form-input v-model="modelSearch.TransNo" @change="$_lists_handleSubmitSearch">

                                  </b-form-input>
                                </td>
                                <td role="cell" class="no-overflow">
                                  <IjcoreDatePicker v-model="modelSearch.TransDate" style="width: 100%;"></IjcoreDatePicker>
                                </td>

                                <td role="cell" class="no-overflow">
                                  <b-form-input v-model="modelSearch.Comment" @change="$_lists_handleSubmitSearch">

                                  </b-form-input>
                                </td>
                                <td role="cell" class="">
                                  <ijcore-number v-model="modelSearch.LCTotalAmount" @keyup.enter="fetchData"></ijcore-number>
                                </td>
                                <td role="cell" class="">
                                  <b-form-input disabled></b-form-input>
                                </td>
                              </tr>
                              <tr role="row" tabindex="0"
                                  aria-selected="false"
                                  :class="[(itemsArray[key].rowSelected) ? 'b-table-row-selected table-active' : '']"
                                  :data-parent-id="item.ParentID"
                                  v-if="!itemsArray[key].hidden"
                                  :key="key"
                                  v-for="(item, key) in itemsArray">
                                <td aria-colindex="1" role="cell" class="pl-3 no-overflow">
                                  <b-form-checkbox v-model="itemsArray[key].rowSelected" class="text-left" @input="onToggleRowSelected($event, item, key)"></b-form-checkbox>
                                </td>
                                <td aria-colindex="2" role="cell" class="field-task-name text-center">{{item.PostDate | convertServerDateToClientDate}}</td>
                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="text-center">{{item.TransNo}}</td>
                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="text-center">{{item.TransDate | convertServerDateToClientDate}}</td>
<!--                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="text-center">{{item.TransTypeName}}</td>-->
                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="text-left">{{item.Comment}}</td>
                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="text-right">{{item.LCTotalAmount | convertNumberToText}}</td>
                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="text-center"><span v-if="item.Posted==1"><i class="fa fa-check" aria-hidden="true"></i></span></td>

                              </tr>
<!--                            </transition-group>-->
                            </tbody>
                          </table>
                        </div>

                        </div>
                      <div class="content-body-kanban" v-if="stage.viewType === 'kanban'">
                        <div class="kanban-items row">
                          <div class="kanban-item col-lg-6 col-md-8 col-sm-12 col-24" v-for="(item, key) in itemsArray">
                            <div class="kanban-item-inner" @click="$_lists_onFieldClicked(item)">
                              <div class="kanban-record d-flex justify-content-between">
<!--                                <span class="kanban-title">{{item.TaskName}}</span>-->
                                <span class="kanban-title">{{getTaskName(item)}}</span>
                              </div>
                              <div class="kanban-record">
                                <span>Mã số</span>
                                <span>{{item.TransNo}}</span>
                              </div>
                              <div class="kanban-record">
                                <span class="kanban-priority">Ưu tiên</span>
                                <span>{{Priority[item.Priority]}}</span>
                              </div>
                              <div class="kanban-record">
                                <span class="kanban-create-date">Ngày tạo</span>
                                <span>{{item.CreateDate | convertServerDateToClientDate}}</span>
                              </div>
                              <div class="kanban-record">
                                <span class="kanban-start-date">Ngày bắt đầu</span>
                                <span>{{item.StartDate | convertServerDateToClientDate}}</span>
                              </div>
                              <div class="kanban-record">
                                <span class="kanban-due-date">Hạn hoàn thành</span>
                                <span>{{item.DueDate | convertServerDateToClientDate}}</span>
                              </div>
                              <div class="kanban-record mb-2">
                                <span class="kanban-status" v-if="item.StatusDescription">Trạng thái</span>
                                <span>{{item.StatusDescription}}</span>
                              </div>
                              <div class="kanban-footer d-flex justify-content-between align-items-center">
                                <div class="kanban-assign d-inline-flex"><ijcore-users-icon :all-users="model.taskAssign" filter-name="TaskID" :filter-value="item.TaskID" :number="4"></ijcore-users-icon></div>
                                <span class="kanban-percent-completed"><b-badge :variant="(item.PercentCompleted <= 0) ? 'warning' : (item.PercentCompleted > 0 && item.PercentCompleted < 100) ? 'primary' : 'success'">{{item.PercentCompleted}} %</b-badge></span>
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
    import mixinLists from '@/mixins/lists';
    import IjcoreUsersIcon from "@/components/IjcoreUsersIcon";
    import IjcoreCompareTime from "@/components/IjcoreCompareTime";
    import IjcoreDateRange from "@/components/IjcoreDateRange";
    import IjcoreModalSearchInput from "@/components/IjcoreModalSearchInput";
    import IjcoreSelect2Server from "@/components/IjcoreSelect2Server";
    import IjcoreDatePicker from "@/components/IjcoreDatePicker";
    import IjcoreCompareNumber from "@/components/IjcoreCompareNumber";
    import IjcoreNumber from "@/components/IjcoreNumber";
    import ClickOutside from 'vue-click-outside';
    import IjcoreModalTransTemp from "@/components/IjcoreModalTransTemp";

    export default {
      name: 'sysadmin-fstatuslist',
      mixins: [mixinLists],
      data() {
          return {
            selectedRows: [],
            model: {
              taskAssign: []
            },
            filter: {
            },
            stage: {
              viewType: (this.$store.state.optionBehavior.viewType) ? this.$store.state.optionBehavior.viewType : 'list',
              showModalSearchTcatelist: false
            },
            Priority: [],
            PriorityOptions: [],
            AccessType: [],
            AccessTypeOptions: [],
            TypeOptions: [],
            TransTemp: {
              TransID: '',
              Comment: ''
            },
            options: [
              { item: '0', name: 'Chưa lập dự toán' },
              { item: '1', name: 'Đã lập dự toán' }
            ],
            OptionsTransType: [{value: '8', text: 'Ước thực hiện dự toán'},{value: '2', text: 'Lập dự toán'},],
          }
      },
      components:{
        IjcoreUsersIcon,
        IjcoreCompareTime,
        IjcoreDateRange,
        IjcoreModalSearchInput,
        IjcoreSelect2Server,
        IjcoreDatePicker,
        IjcoreCompareNumber,
        IjcoreNumber,
        IjcoreModalTransTemp,
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
            {key: 'selected', label: '', thStyle: 'width: 2%',tdClass: 'pl-3 no-overflow', thClass: 'pl-3'},
            {key: 'PostDate', label: 'Ngày HT', field: 'PostDate',searchOnTopRow: {type: 'date'}, thStyle: 'width: 15%'},
            {key: 'TransNo', label: 'Số CTG', field: 'TransNo',searchOnTopRow: {type: 'text'}, thStyle: 'width: 8%'},
            {key: 'TransDate', label: 'Ngày CTG', field: 'TransDate',searchOnTopRow: {type: 'date'}, thStyle: 'width: 8%'},
            {key: 'Comment', label: 'Diễn giải', headerTitle: 'Diễn giải', field: 'Comment',searchOnTopRow: {type: 'text'}, thStyle: 'width: 8%'},
            {key: 'LCTotalAmount', label: 'Tổng số tiền', field: 'LCTotalAmount',searchOnTopRow: {type: 'text'}, thStyle: 'width: 8%'},
            {key: 'Posted', label: 'Đã ghi sổ', field: 'Posted',searchOnTopRow: {type: 'text'}, thStyle: 'width: 8%'},
          ];

          return fieldsTable;
        }
      },
      created() {
        // init setting
        this.settings.FieldID = 'TransID';
        this.settings.Table = 'act_gvouc_trans';
        this.settings.FieldInactive = 'Inactive';

        this.settings.ListApi = 'state-budget-planning/api/sbpmakeplan';
        this.settings.DeleteApi = 'state-budget-planning/api/sbpmakeplan/delete';
        this.settings.CreateRouter = 'statebudgetplanning-sbpmakeplan-create';
        this.settings.EditRouter = 'statebudgetplanning-sbpmakeplan-edit';
        this.settings.ViewRouter = 'statebudgetplanning-sbpmakeplan-view';

        this.modelSearch = {
          Comment: ''
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
              filter: {}
            },

          };

          if (this.modelSearch.TransNo !== '') {
            requestData.data.TransNo = this.modelSearch.TransNo;
          }
          if (this.modelSearch.Comment !== '') {
            requestData.data.Comment = this.modelSearch.Comment;
          }
          // if (this.modelSearch.TransDate !== '') {
          //   requestData.data.TransDate = this.modelSearch.TransDate;
          // }
          if (this.modelSearch.LCTotalAmount !== '') {
            requestData.data.LCTotalAmount = this.modelSearch.LCTotalAmount;
          }
          if (this.modelSearch.PostDate && this.modelSearch.PostDate !== '__/__/____') {
            requestData.data.PostDate = this.modelSearch.PostDate;
          }
          if (this.modelSearch.TransDate  && this.modelSearch.TransDate !== '__/__/____') {
            requestData.data.TransDate = this.modelSearch.TransDate;
          }
          if (this.stage.filterInactive !== 2) {
            requestData.data.Inactive = this.stage.filterInactive;
          }
          // if (this.modelSearch.Inactive !== '') {
          //   requestData.data.Inactive = this.modelSearch.Inactive;
          // }
          if (this.fullTextSearch !== '') {
            requestData.data.fullTextSearch = this.fullTextSearch;
          }

          this.$store.commit('isLoading', true);
          ApiService.customRequest(requestData).then((response) => {
            let dataResponse = response.data; //console.log(response.data);
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
        onRowClicked(item){
            this.$router.push({
                name: this.settings.ViewRouter,
                params: {id: item[this.settings.FieldID], req: this.paramsReqRouter}
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
        onChangeTaskType(value){
          // loại công việc khác
          if (Number(value) === 9) {
            this.$bvModal.show('myModalSearchTcatelist');
          }
        },
        onToggleChildren(item, key){
          let allChildTask = this.getAllChildTask(item, this.itemsArray);
          let self = this;
          let $icon = $('.task-icon-' + item.TaskID + ' i');
          if ($icon.hasClass('fa-folder-o')) {
            // $('[data-parent-id=' + item.TaskID + ']').show();
            _.forEach(allChildTask, function (task, key) {
              let _index = _.findIndex(self.itemsArray, ['TaskID', task.TaskID]);
              if (_index >= 0) {
                self.$set(self.itemsArray[_index], 'hidden', false);
              }
            });
            $('.task-icon-' + item.TaskID + ' i').removeClass('fa-folder-o').addClass('fa-folder-open-o');
          } else {
            // $('[data-parent-id=' + item.TaskID + ']').hide();
            _.forEach(allChildTask, function (task, key) {
                let _index = _.findIndex(self.itemsArray, ['TaskID', task.TaskID]);
                if (_index >= 0) {
                    self.$set(self.itemsArray[_index], 'hidden', true);
                }
            });
            $('.task-icon-' + item.TaskID + ' i').removeClass('fa-folder-open-o').addClass('fa-folder-o');
          }

        },
        haveChildren(item){
          let children = _.filter(this.itemsArray, ['ParentID', item.TaskID]);
          if (children.length > 0) {
            return true;
          }
          return false;
        },
        onToggleRowSelected(event, item, key){
            let indexRow = _.findIndex(this.selectedRows, ['TaskID', item.TaskID]);
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
          let request = {};
          if (this.modelSearch.TransNo !== '') {
            request.TransNo = this.modelSearch.TransNo;
          }
          if (this.modelSearch.Comment !== '') {
            request.Comment = this.modelSearch.Comment;
          }
          if (this.modelSearch.TransDate !== '') {
            request.TransDate = this.modelSearch.TransDate;
          }
          if (this.fullTextSearch !== '') {
            request.fullTextSearch = this.fullTextSearch;
          }
          request.exportData = true;
          this.$router.push({
            name: 'statebudgetplanning-sbpmakeplan-report',
            query: request
          });
        },

        autoCorrectedDatePipe: () => { return createAutoCorrectedDatePipe('dd/mm/yyyy') },
        scrollHandle(evt){},
        showModalTcatelist(){
          this.filter.Type = 9;
          this.$bvModal.show('myModalSearchTcatelist');
        },
        renderDashHtml(level){
            let dash = '';
            for (let i = 0; i < level - 1; i++) {
                dash += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
            }
            return dash;
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
        changeTemplate(){
          this.$router.push({
            name: 'statebudgetplanning-sbpmakeplan-create',
            params: {cloneTransID: this.TransTemp.TransID}
          })
        },
        onClickWorkflow(){
          this.$router.push({
            name: 'task-dataflow'
          });
        }
      },
      directives: {
          ClickOutside
      },
      watch: {
        currentPage() {
            this.fetchData();
        },
        'modelSearch.TransDate'(){
          if (this.modelSearch.TransDate) {
            this.$_lists_handleSubmitSearch();
          }
        },
        'modelSearch.PostDate'(){
          if (this.modelSearch.PostDate) {
            this.$_lists_handleSubmitSearch();
          }
        },
        'modelSearch.LCTotalAmount'(){
          if (this.modelSearch.LCTotalAmount) {
            this.$_lists_handleSubmitSearch();
          }
        },
        'filter.Type'(){
          if (this.filter.Type !== 9) this.$_lists_handleSubmitSearch();
        },
        'filter.Priority'(){
          this.$_lists_handleSubmitSearch();
        },
        'filter.CreateDate.dateTime'(){
          if (this.filter.CreateDate.dateTime) {
            this.$_lists_handleSubmitSearch();
          }
        },
        'filter.CreateDate.operator'(){
          if (this.filter.CreateDate.dateTime && this.filter.CreateDate.dateTime !== '__/__/____') {
            this.$_lists_handleSubmitSearch();
          }
        },
        'filter.StartDate.dateTime'(){
          if (this.filter.StartDate.dateTime) {
            this.$_lists_handleSubmitSearch();
          }
        },
        'filter.StartDate.operator'(){
          if (this.filter.StartDate.dateTime && this.filter.StartDate.dateTime !== '__/__/____') {
            this.$_lists_handleSubmitSearch();
          }
        },
        'filter.DueDate.dateTime'(){
          if (this.filter.DueDate.dateTime) {
            this.$_lists_handleSubmitSearch();
          }
        },
        'filter.DueDate.operator'(){
          if (this.filter.DueDate.dateTime && this.filter.DueDate.dateTime !== '__/__/____') {
            this.$_lists_handleSubmitSearch();
          }
        },

        // 'filter.PercentCompleted.operator'(newValue, oldValue){
        //   if (oldValue) {
        //     if (this.filter.PercentCompleted.number != '') {
        //       this.$_lists_handleSubmitSearch();
        //     }
        //   }
        // },
        // 'filter.Company':{
        //   handler(val){
        //     // do stuff
        //     this.$_lists_handleSubmitSearch();
        //   },
        //   deep: true
        // },
        // 'filter.Doc':{
        //   handler(val){
        //     // do stuff
        //     this.$_lists_handleSubmitSearch();
        //   },
        //   deep: true
        // },
        // 'filter.TaskLink': {
        //   handler(val){
        //     // do stuff
        //   },
        //   deep: true
        // },
        // 'filter.DateRange.fromDate'(){
        //   this.$_lists_handleSubmitSearch();
        // },
        // 'filter.DateRange.toDate'(){
        //   this.$_lists_handleSubmitSearch();
        // },

        // 'filter.AccessType'(){
        //   this.$_lists_handleSubmitSearch();
        // },
        'filter.CreateEmployeeID'(){
          if (this.filter.CreateEmployeeID) {
            this.filter.ImplementEmployeeID = null;
            this.filter.AssignEmployeeID = null;
            this.filter.ReceiveEmployeeID = null;
            this.filter.ResponEmployeeID = null;
          }
        },
        'filter.AssignEmployeeID'(){
          if (this.filter.AssignEmployeeID) {
            this.filter.ImplementEmployeeID = null;
            this.filter.CreateEmployeeID = null;
            this.filter.ReceiveEmployeeID = null;
            this.filter.ResponEmployeeID = null;
          }
        },
        'filter.ReceiveEmployeeID'(){
          if (this.filter.ReceiveEmployeeID) {
            this.filter.ImplementEmployeeID = null;
            this.filter.AssignEmployeeID = null;
            this.filter.CreateEmployeeID = null;
            this.filter.ResponEmployeeID = null;
          }
        },
        'filter.ResponEmployeeID'(){
          if (this.filter.ResponEmployeeID) {
            this.filter.ImplementEmployeeID = null;
            this.filter.AssignEmployeeID = null;
            this.filter.ReceiveEmployeeID = null;
            this.filter.CreateEmployeeID = null;
          }
        },
        'filter.ImplementEmployeeID'(){
          if (this.filter.ImplementEmployeeID) {
            this.filter.CreateEmployeeID = null;
            this.filter.AssignEmployeeID = null;
            this.filter.ReceiveEmployeeID = null;
            this.filter.ResponEmployeeID = null;
          }
          this.$_lists_handleSubmitSearch();
        },
        // 'filter.TaskCateList'(){
        // },
        'stage.viewType'(){
          this.fetchData();
          this.$store.commit('optionBehavior', {'viewType': this.stage.viewType});
        }
      },
      beforeDestroy() {
        $(document).unbind("keypress");
      }
    }
</script>

<style lang="css">
  .main-footer-pagination ul {
      margin-bottom: 0;
  }
</style>
