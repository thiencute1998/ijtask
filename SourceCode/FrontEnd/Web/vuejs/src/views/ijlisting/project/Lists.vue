<template>
  <div class="component-project-list">
    <div class="main-entry">
      <div class="main-header">
        <div class="main-header-padding">
          <b-row class="mb-2">
            <b-col class="col-md-12">
              <div class="main-header-item main-header-name">
                <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Dự án</span>
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
                    <a class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" data-toggle="dropdown" @click.stop="$_lists_onToggleDropdownSubMenu($event)">Nhập</a>
                    <ul role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-menu m-0">
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Excel</a></li>
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Cvs</a></li>
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Xml</a></li>
                      <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item" @click="test_report">Json</a></li>
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
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                  <IjcoreModalSearchCompany v-model="modelSearch.InvestorName"  :fieldCateList="'29'" :fieldCateValue="[2]" :title="'Chủ đầu tư'" :table="'company'" :api="'/listing/api/company/get-cate-value'"  :placeholderInput="'Chủ đầu tư'" @changeInvestor="updateInvestor"></IjcoreModalSearchCompany>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                  <IjcoreModalSearchCompany v-model="modelSearch.StateOrganName" :fieldCateList="'29'" :fieldCateValue="[3]" :title="'Ban QLDA'" :table="'company'" :api="'/listing/api/company/get-cate-value'" :placeholderInput="'Ban QLDA'" @changeStateOrgan="updateStateOrgan"></IjcoreModalSearchCompany>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                  <IjcoreModalSearchCompany v-model="modelSearch.InvestDecisionOrganName" :fieldCateList="'29'" :fieldCateValue="[1]" :title="'Tên CQQĐĐT'" :table="'company'" :api="'/listing/api/company/get-cate-value'" :placeholderInput="'Tên CQQĐĐT'" @changeInvestDecisionOrgan="updateInvestDecisionOrgan"></IjcoreModalSearchCompany>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                  <b-form-select v-model="modelSearch.ManagementLevel" :options="ManagementLevelOption">
                  </b-form-select>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                  <project-modal-search-input-catelist
                    v-model="modelSearch.ProjectCate"
                    title-modal="Loại dự án"
                    placeholder="Loại dự án"
                  ></project-modal-search-input-catelist>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                  <ijcore-modal-search-listing
                    v-model="modelSearch" :title="'Ngành'" :table="'sector'" :api="'/listing/api/common/list'"
                    :fieldID="'SectorID'" :fieldNo="'SectorNo'" :fieldName="'SectorName'"
                    :fieldAssignID="'SectorID'" :fieldAssignNo="'SectorNo'" :fieldAssignName="'SectorName'"
                  >
                  </ijcore-modal-search-listing>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                  <ijcore-modal-search-listing
                    v-model="modelSearch" :title="'Chương trình mục tiêu'" :table="'program'" :api="'/listing/api/common/list'"
                    :fieldID="'ProgramID'" :fieldNo="'ProgramNo'" :fieldName="'ProgramName'"
                    :fieldAssignID="'ProgramID'" :fieldAssignNo="'ProgramNo'" :fieldAssignName="'ProgramName'"
                  >
                  </ijcore-modal-search-listing>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                  <b-form-input v-model="modelSearch.BuildAddress" placeholder="Địa điểm xây dựng"></b-form-input>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                  <b-form-input v-model="modelSearch.CapableDesign" placeholder="Công suất thiết kế"></b-form-input>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                  <b-form-input v-model="modelSearch.CapableFulfilling" placeholder="Công suất hoàn thành"></b-form-input>
                </div>
              </div>
              <div class="row">
                <div class="col-md-8 col-sm-12 col-12 mb-2">
                  <label for="DateRangeStart">Ngày khởi công </label>
                  <ijcore-date-range v-model="filter.DateRangeStart" id="DateRangeStart"></ijcore-date-range>
                </div>
                <div class="col-md-8 col-sm-12 col-12 mb-2">
                  <label for="DateRangeFinish">Ngày hoàn thành </label>
                  <ijcore-date-range v-model="filter.DateRangeFinish" id="DateRangeFinish"></ijcore-date-range>
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
                    <th role="columnheader" scope="col" style="width: 10%; min-width: 100px" aria-sort="none" @click="sortTableByField($event, 'ProjectNo')" v-if="stage.viewType === 'list'">Mã dự án</th>
                    <th role="columnheader" scope="col" style="width: 10%; min-width: 100px" v-else>Mã dự án</th>
                    <th role="columnheader" scope="col" style="width: 10%; min-width: 120px" aria-sort="none" @click="sortTableByField($event, 'TabmisNo')" v-if="stage.viewType === 'list'">Mã Tabmis</th>
                    <th role="columnheader" scope="col" style="width: 10%; min-width: 120px" v-else>Mã Tabmis</th>
                    <th role="columnheader" scope="col" style="width: 30%; min-width: 250px" aria-sort="none" @click="sortTableByField($event, 'ProjectName')" v-if="stage.viewType === 'list'">Tên dự án</th>
                    <th role="columnheader" scope="col" style="width: 30%; min-width: 250px" v-else>Tên dự án</th>
                    <th role="columnheader" scope="col" style="width: 8%; min-width: 135px" aria-sort="none" @click="sortTableByField($event, 'StartedDate')" v-if="stage.viewType === 'list'">Ngày khởi công</th>
                    <th role="columnheader" scope="col" style="width: 8%; min-width: 135px" v-else>Ngày khởi công</th>
                    <th role="columnheader" scope="col" style="width: 8%; min-width: 135px" aria-sort="none" @click="sortTableByField($event, 'FinishedDate')" v-if="stage.viewType === 'list'">Ngày hoàn thành</th>
                    <th role="columnheader" scope="col" style="width: 8%; min-width: 135px" v-else>Ngày hoàn thành</th>
                    <th role="columnheader" scope="col" style="width: 10%; min-width: 120px" aria-sort="none" @click="sortTableByField($event, 'Inactive')" v-if="stage.viewType === 'list'">Tình trạng</th>
                    <th role="columnheader" scope="col" style="width: 10%; min-width: 120px" v-else>Tình trạng</th>
                    <th role="columnheader" scope="col" style="width: 6%; min-width: 80px" aria-sort="none" @click="sortTableByField($event, 'PercentCompleted')" v-if="stage.viewType === 'list'">%HT</th>
                    <th role="columnheader" scope="col" style="width: 6%; min-width: 80px" v-else>%HT</th>
                    <th role="columnheader" scope="col" style="width: 10%; min-width: 120px" aria-sort="none" @click="sortTableByField($event, 'Note')" v-if="stage.viewType === 'list'">Tài liệu</th>
                    <th role="columnheader" scope="col" style="width: 10%; min-width: 120px" v-else>Tài liệu</th>
                  </tr>
                  </thead>
                  <transition-group tag="tbody" name="slide-fade">
                    <tr role="row" :key="'table-top-row'" class="b-table-top-row">
                      <td role="cell" class="pl-3"></td>
                      <td role="cell">
                        <b-form-input
                          v-model="modelSearch.ProjectNo"
                          @keyup.enter="$_lists_handleSubmitSearch"></b-form-input>
                      </td>
                      <td role="cell">
                        <b-form-input
                          v-model="modelSearch.TabmisNo"
                          @keyup.enter="$_lists_handleSubmitSearch"></b-form-input>
                      </td>
                      <td role="cell" class="">
                        <b-form-input v-model="modelSearch.ProjectName" @keyup.enter="$_lists_handleSubmitSearch"></b-form-input>
                      </td>
                      <td role="cell">
                        <ijcore-compare-time
                          @clear-date-picker="fetchData"
                          v-model="modelSearch.StartedDate">
                        </ijcore-compare-time>
                      </td>
                      <td role="cell">
                        <ijcore-compare-time
                          @clear-date-picker="fetchData"
                          v-model="modelSearch.FinishedDate">
                        </ijcore-compare-time>
                      </td>
                      <td role="cell">
                        <b-form-select
                          v-model="modelSearch.Status"
                          :options="StatusOption"
                          @change="changeOption">
                        </b-form-select>
                      </td>
                      <td role="cell">
                        <ijcore-compare-number @keyup-compare-input="fetchData" v-model="modelSearch.PercentCompleted"></ijcore-compare-number>
                      </td>
                      <td role="cell">
                        <b-form-input
                          v-model="modelSearch.Note"
                          @keyup.enter="$_lists_handleSubmitSearch"></b-form-input>
                      </td>
                    </tr>
                    <tr role="row" tabindex="0"
                        aria-selected="false"
                        :class="[(!item.ParentID) ? 'project-parent' : 'project-children', (itemsArray[key].rowSelected) ? 'b-table-row-selected table-active' : '']"
                        :data-parent-id="item.ParentID"
                        v-if="!itemsArray[key].hidden"
                        :key="key"
                        v-for="(item, key) in itemsArray">
                      <td aria-colindex="1" role="cell" class="pl-3">
                        <b-form-checkbox v-model="itemsArray[key].rowSelected" class="text-left no-overflow pl-4" @input="onToggleRowSelected($event, item, key)"></b-form-checkbox>
                      </td>
                      <td aria-colindex="2" role="cell" class="field-project-name">
                        <div v-if="stage.viewType === 'tree'">
                          <span class="dash" v-html="renderDashHtml(item.Level)" v-if="item.Level !== 1"></span>
                          <span class="project-icon" :class="'project-icon-' + item.ProjectID" v-if="haveChildren(item)" @click="onToggleChildren(item, key)">
                                      <i class="fa fa-folder-open-o"></i>
                                    </span>
                          <span @click="onRowClicked(item)" :title="item.ProjectName | stripHtml">
                                        {{item.ProjectName | stripHtml}}
                                    </span>
                        </div>
                        <div @click="onRowClicked(item)">
                          <span>{{item.ProjectNo}}</span>
                        </div>
                      </td>
                      <td @click="onRowClicked(item)" aria-colindex="2" role="cell">
                        <span>{{item.TabmisNo}}</span>
                      </td>
                      <td @click="onRowClicked(item)" aria-colindex="2" role="cell" v-if="stage.viewType === 'list'">
                        <span :title="item.ProjectName">{{item.ProjectName}}</span>
                      </td>
                      <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="text-center">
                        <span>{{item.StartedDate | convertServerDateToClientDate}}</span>
                      </td>
                      <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="text-center">
                        <span>{{item.FinishedDate | convertServerDateToClientDate}}</span>
                      </td>
                      <td @click="onRowClicked(item)" aria-colindex="2" role="cell">
                        <span>{{ (StatusOption[item.Status]).text}}</span>
                      </td>
                      <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="text-right">
                        <div class="d-inline-flex align-items-center justify-content-end">
                          <b-badge :variant="(item.PercentCompleted <= 0) ? 'warning' : (item.PercentCompleted > 0 && item.PercentCompleted < 100) ? 'primary' : 'success'" >{{item.PercentCompleted}}</b-badge>
                        </div>
                      </td>
                      <td @click="onRowClicked(item)" aria-colindex="2" role="cell">
                        <span v-for="(doc, i) in item.doc" :title="getDocTitle(item)">
                          {{doc.DocName}} <span v-if="i < item.doc.length - 1"> , </span>
                        </span>
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
                    <div class="kanban-record">
                      <span>Mã dự án: </span>
                      <span class="kanban-text">{{item.ProjectNo}}</span>
                    </div>
                    <div class="kanban-record"  v-if="item.TabmisNo && item.TabmisNo !== ''">
                      <span>Mã Tabmis: </span>
                      <span class="kanban-text">{{item.TabmisNo}}</span>
                    </div>
                    <div class="kanban-record d-flex justify-content-between">
                      <span class="kanban-title">{{item.ProjectName}}</span>
                    </div>
                    <div class="kanban-record" v-if="item.StartedDate && item.StartedDate !== ''">
                      <span>Ngày khởi công: </span>
                      <span class="kanban-text" >{{item.StartedDate | convertServerDateToClientDate}}</span>
                    </div>
                    <div class="kanban-record"v-if="item.FinishedDate && item.FinishedDate !== ''">
                      <span>Ngày hoàn thành: </span>
                      <span class="kanban-text" >{{item.FinishedDate | convertServerDateToClientDate}}</span>
                    </div>
                    <div class="kanban-record"v-if="item.Status && item.Status !== ''">
                      <span>Tình trạng: </span>
                      <span class="kanban-text" >{{StatusOption[item.Status].text}}</span>
                    </div>
                    <div class="kanban-record" v-if="item.PercentCompleted && item.PercentCompleted !== ''">
                      <span>%HT: </span>
                      <span class="kanban-text">{{item.PercentCompleted}}</span>
                    </div>
                    <div class="kanban-record" v-if="item.doc.length > 0">
                      <span class="kanban-text">{{getDocTitle(item)}}</span>
                    </div>
                    <div class="kanban-record">
                      <span class="kanban-text" v-if="item.Inactive && item.Inactive !== ''">{{item.Inactive}}</span>
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
import IjcoreCompareTime from "@/components/IjcoreCompareTime";
import IjcoreDateRange from "@/components/IjcoreDateRange";
import ProjectModalSearchLink from "./partials/ProjectModalSearchLink";
import moment from "moment";
import ProjectModalSearchInputCatelist from "./partials/ProjectModalSearchInputCatelist";
import IjcoreCompareNumber from "@/components/IjcoreCompareNumber";
import IjcoreModalSearchCompany from "../../../components/IjcoreModalSearchCompany";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";

export default {
  name: 'listing-items',
  mixins: [mixinLists],
  data() {
    return {
      selectedRows: [],
      filter: {
        DateRangeStart: (this.$route.query && this.$route.query.DateRangeStart) ? this.$route.query.DateRangeStart : null,
        DateRangeFinish: (this.$route.query && this.$route.query.DateRangeFinish) ? this.$route.query.DateRangeFinish : null,
        ProjectLink: (this.$route.query && this.$route.query.ProjectLink) ? this.$route.query.ProjectLink : [],
      },
      StatusOption: [],
      ManagementLevelOption: [],
    }
  },
  components:{
    IjcoreDateRange,
    IjcoreCompareTime,
    ProjectModalSearchLink,
    IjcoreCompareNumber,
    ProjectModalSearchInputCatelist,
    IjcoreModalSearchCompany,
    IjcoreModalSearchListing,
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
        {key: 'ProjectNo', label: 'Mã dự án', field: 'ProjectNo', thStyle: 'width: 10%; min-width: 100px', sortable: true, searchOnTopRow: {type: 'text'}},
        {key: 'TabmisNo', label: 'Mã Tabmis', thStyle: 'width: 10%; min-width: 100px', field: 'TabmisNo', sortable: true ,searchOnTopRow: {type: 'text'}},
        {key: 'ProjectName', label: 'Tên dự án', thStyle: 'width: 45%;min-width: 200px', field: 'ProjectName', sortable: true,searchOnTopRow: {type: 'text'}},
        {key: 'StartedDate', label: 'Ngày khởi công', field: 'StartedDate', thStyle: 'width: 10%; min-width: 100px', sortable: true, tdClass: 'text-center', searchOnTopRow: {type: 'date'}},
        {key: 'FinishedDate', label: 'Ngày hoàn thành', field: 'FinishedDate', thStyle: 'width: 10%; min-width: 100x', sortable: true, tdClass: 'text-center',searchOnTopRow: {type: 'date'}},
        {key: 'InactiveName', label: 'Tình trạng', field: 'Inactive', thStyle: 'width: 5%; min-width: 100px', searchOnTopRow: {type: 'text'}},
        {key: 'PercentCompleted', label: '% HT', field: 'PercentCompleted', thStyle: 'width: 5%; min-width: 100px', sortable: true, tdClass: 'text-center', searchOnTopRow: {type: 'text', tdClass: 'pr-3'}},
      ];

      return fieldsTable;
    },

  },
  created() {

    this.settings.FieldID = 'ProjectID';
    this.settings.Table = 'project';
    this.settings.FieldInactive = 'Inactive';

    this.settings.ListApi = 'listing/api/project';
    this.settings.DeleteApi = 'listing/api/project/delete';
    this.settings.CreateRouter = 'listing-project-create';
    this.settings.EditRouter = 'listing-project-edit';
    this.settings.ViewRouter = 'listing-project-view';

    this.modelSearch = {
      ProjectNo: (this.$route.query && this.$route.query.ProjectNo) ? this.$route.query.ProjectNo : '',
      ProjectName: (this.$route.query && this.$route.query.ProjectName) ? this.$route.query.ProjectName : '',
      TabmisNo: (this.$route.query && this.$route.query.TabmisNo) ? this.$route.query.TabmisNo : '',
      StartedDate: (this.$route.query && this.$route.query.StartedDate) ? this.$route.query.StartedDate : null,
      FinishedDate: (this.$route.query && this.$route.query.FinishedDate) ? this.$route.query.FinishedDate : null,
      Status : (this.$route.query && this.$route.query.Status) ? this.$route.query.Status : null,
      PercentCompleted: (this.$route.query && this.$route.query.PercentCompleted) ? this.$route.query.PercentCompleted : null,
      Note: (this.$route.query && this.$route.query.Note) ? this.$route.query.Note : '',

      // lọc nâng cao
      InvestorID: (this.$route.query && this.$route.query.InvestorID) ? this.$route.query.InvestorID : null,
      InvestorName: (this.$route.query && this.$route.query.InvestorName) ? this.$route.query.InvestorName : '',
      StateOrganID: (this.$route.query && this.$route.query.StateOrganID) ? this.$route.query.StateOrganID : null,
      StateOrganName: (this.$route.query && this.$route.query.StateOrganName) ? this.$route.query.StateOrganName : '',
      InvestDecisionOrganID: (this.$route.query && this.$route.query.InvestDecisionOrganID) ? this.$route.query.InvestDecisionOrganID : null,
      InvestDecisionOrganName: (this.$route.query && this.$route.query.InvestDecisionOrganName) ? this.$route.query.InvestDecisionOrganName : '',
      ManagementLevel: (this.$route.query && this.$route.query.ManagementLevel) ? this.$route.query.ManagementLevel : null,
      ProjectCate: (this.$route.query && this.$route.query.ProjectCate) ? this.$route.query.ProjectCate : [],
      SectorID: (this.$route.query && this.$route.query.SectorID) ? this.$route.query.SectorID : null,
      ProgramID: (this.$route.query && this.$route.query.ProgramID) ? this.$route.query.ProgramID : null,
      BuildAddress: (this.$route.query && this.$route.query.BuildAddress) ? this.$route.query.BuildAddress : '',
      CapableDesign: (this.$route.query && this.$route.query.CapableDesign) ? this.$route.query.CapableDesign : '',
      CapableFulfilling: (this.$route.query && this.$route.query.CapableFulfilling) ? this.$route.query.CapableFulfilling : '',
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
    changeOption(event){
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

      if (this.modelSearch.ProjectNo.trim() !== '') {
        requestData.data.ProjectNo = this.modelSearch.ProjectNo;
      }
      if (this.modelSearch.ProjectName !== '') {
        requestData.data.ProjectName = this.modelSearch.ProjectName;
      }
      if (this.modelSearch.TabmisNo !== '') {
        requestData.data.TabmisNo = this.modelSearch.TabmisNo;
      }
      if (this.modelSearch.StartedDate && !_.isEmpty(this.modelSearch.StartedDate.dateTime) && this.modelSearch.StartedDate.dateTime !== '__/__/____') {
        requestData.data.StartedDate = this.modelSearch.StartedDate;
      }
      if (this.modelSearch.FinishedDate && !_.isEmpty(this.modelSearch.FinishedDate.dateTime) && this.modelSearch.FinishedDate.dateTime !== '__/__/____') {
        requestData.data.FinishedDate = this.modelSearch.FinishedDate;
      }
      if (this.modelSearch.Status !== null) {
        requestData.data.Status = this.modelSearch.Status;
      }
      if (this.modelSearch.PercentCompleted !== '') {
        requestData.data.PercentCompleted = this.modelSearch.PercentCompleted;
      }
      // lọc nâng cao
      if (this.modelSearch.InvestorID !== null) {
        requestData.data.InvestorID = this.modelSearch.InvestorID;
      }
      if (this.modelSearch.StateOrganID !== null) {
        requestData.data.StateOrganID = this.modelSearch.StateOrganID;
      }
      if (this.modelSearch.InvestDecisionOrganID !== null) {
        requestData.data.InvestDecisionOrganID = this.modelSearch.InvestDecisionOrganID;
      }
      if (this.modelSearch.ManagementLevel !== null) {
        requestData.data.ManagementLevel = this.modelSearch.ManagementLevel;
      }
      if (this.modelSearch.ProjectCate.length) {
        requestData.data.ProjectCate = this.modelSearch.ProjectCate;
      }
      if (this.modelSearch.SectorID !== null) {
        requestData.data.SectorID = this.modelSearch.SectorID;
      }
      if (this.modelSearch.ProgramID !== null) {
        requestData.data.ProgramID = this.modelSearch.ProgramID;
      }
      if (this.modelSearch.BuildAddress !== '') {
        requestData.data.BuildAddress = this.modelSearch.BuildAddress;
      }
      if (this.modelSearch.CapableDesign !== '') {
        requestData.data.CapableDesign = this.modelSearch.CapableDesign;
      }
      if (this.modelSearch.CapableFulfilling !== '') {
        requestData.data.CapableFulfilling = this.modelSearch.CapableFulfilling;
      }
      if(this.filter.DateRangeStart && this.filter.DateRangeStart.fromDate && this.filter.DateRangeStart.toDate){
        requestData.data.DateRangeStart = this.filter.DateRangeStart;
      }
      if(this.filter.DateRangeFinish && this.filter.DateRangeFinish.fromDate && this.filter.DateRangeFinish.toDate){
        requestData.data.DateRangeFinish = this.filter.DateRangeFinish;
      }
      if (this.stage.filterInactive !== 2) {
        requestData.data.Inactive = this.stage.filterInactive;
      }
      if (this.fullTextSearch !== '') {
        requestData.data.fullTextSearch = this.fullTextSearch;
      }

      if (this.filter.ProjectLink.length) {
        let haveLink = false;
        _.forEach(this.filter.ProjectLink, function (projectLink, key) {
          if (projectLink.LinkID) {
            haveLink = true;
            return false;
          }
        });
        if (haveLink) {
          requestData.data.ProjectLink = this.filter.ProjectLink;
        }
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
          self.itemsArray = [];

          // converse object to array
          self.itemsArray = _.toArray(dataResponse.data.data);

          self.ManagementLevelOption = [];
          self.ManagementLevelOption = [{value: null, text: '-- Tất cả --'}];
          _.forEach(dataResponse.ManagementLevelOption, function(value, key){
            let tmpObj = {};
            tmpObj.value = key;
            tmpObj.text = value;
            self.ManagementLevelOption.push(tmpObj);
          })

          // set params request
          self.paramsReqRouter.lastPage = dataResponse.data.last_page;
          self.paramsReqRouter.from = dataResponse.data.from;
          self.paramsReqRouter.to = dataResponse.data.to;
          self.$_lists_setParamsReqRouter();

          if(_.isArray(dataResponse.StatusItem)){
            self.StatusOption = [{value: null, text: '-- Tất cả --'}];
            _.forEach(dataResponse.StatusItem, function(value, key){
              let tmpObj = {};
              tmpObj.value = value.StatusValue;
              tmpObj.text = value.StatusDescription;
              self.StatusOption.push(tmpObj);
            })
          }

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
    updateInvestor(data){
      this.modelSearch.InvestorID = data.CompanyID;
      this.modelSearch.InvestorName = data.CompanyName;
    },
    updateStateOrgan(data){
      this.modelSearch.StateOrganID = data.CompanyID;
      this.modelSearch.StateOrganName = data.CompanyName;
    },
    updateInvestDecisionOrgan(data){
      this.modelSearch.InvestDecisionOrganID = data.CompanyID;
      this.modelSearch.InvestDecisionOrganName = data.CompanyName;
    },
    getDocTitle(item){
      let docTitle = '';
      if(item.doc.length > 0){
        _.forEach(item.doc, function(value, key){
          docTitle += value.DocName;
          if(key < item.doc.length - 1){
            docTitle += ' , ';
          }
        })
      }
      return docTitle;
    },
    onToggleRowSelected(event, item, key){
      let indexRow = _.findIndex(this.selectedRows, ['ProjectID', item.ProjectID]);
      if (event && (indexRow < 0)) {
        this.selectedRows.push(item);
      } else {
        this.selectedRows.splice(indexRow, 1);
      }
      this.$_lists_onRowSelected(this.selectedRows);
    },
    test_report(){
      let self = this;
      let urlApi = 'listing/api/project/test-report'+ '';
      let requestData = {
        method: 'post',
        url: urlApi,
      };
      this.$store.commit('isLoading', true);
      ApiService.setHeader();
      ApiService.customRequest(requestData).then(response=>{
        let responseData = response.data;
        this.$store.commit('isLoading', false);
        console.log(responseData)
      }).catch(error=>{
        console.log(error);
        this.$store.commit('isLoading', false);
      })
    },
    handleExportPrint() {
      // Todo: handle export print
      let request = {};
      if (this.modelSearch.ProjectNo !== '') {
        request.ProjectNo = this.modelSearch.ProjectNo;
      }
      if (this.modelSearch.TabmisNo !== '') {
        request.TabmisNo = this.modelSearch.TabmisNo;
      }
      if (this.modelSearch.ProjectName !== '') {
        request.ProjectName = this.modelSearch.ProjectName;
      }
      if (this.modelSearch.STT !== '') {
        request.STT = this.modelSearch.STT;
      }
      if (this.modelSearch.StartedDate !== '') {
        request.StartedDate = this.modelSearch.StartedDate;
      }
      if (this.modelSearch.FinishedDate !== '') {
        request.FinishedDate = this.modelSearch.FinishedDate;
      }
      if (this.modelSearch.CapableFulfilling !== '') {
        request.CapableFulfilling = this.modelSearch.CapableFulfilling;
      }
      if (this.modelSearch.Note !== '') {
        request.Note = this.modelSearch.Note;
      }
      if (this.stage.filterInactive !== 2) {
        request.Inactive = this.stage.filterInactive;
      }
      if (this.fullTextSearch !== '') {
        request.fullTextSearch = this.fullTextSearch;
      }
      if (this.filter.ProjectLink.length) {
        let haveLink = false;
        _.forEach(this.filter.ProjectLink, function (projectLink, key) {
          if (projectLink.LinkID) {
            haveLink = true;
            return false;
          }
        });
        if (haveLink) {
          request.ProjectLink = this.filter.ProjectLink;
        }
      }
      request.currentPage = 1;
      request.perPage = this.perPage;
      request.totalRows = this.totalRows;
      request.exportData = true;
      this.$router.push({
        name: 'listing-project-report',
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
      let reportJson = await this.$_lists_handleReportTemplate('project');
      let request = {};
      if (this.modelSearch.ProjectNo !== '') {
        request.ProjectNo = this.modelSearch.ProjectNo;
      }
      if (this.modelSearch.ProjectName !== '') {
        request.ProjectName = this.modelSearch.ProjectName;
      }
      if (this.modelSearch.TabmisNo !== '') {
        request.TabmisNo = this.modelSearch.TabmisNo;
      }
      if (this.modelSearch.StartedDate !== '') {
        request.StartedDate = this.modelSearch.StartedDate;
      }
      if (this.modelSearch.FinishedDate !== '') {
        request.FinishedDate = this.modelSearch.FinishedDate;
      }
      if (this.modelSearch.Status !== null) {
        request.Status = this.modelSearch.Status;
      }
      if (this.modelSearch.CapableFulfilling !== '') {
        request.CapableFulfilling = this.modelSearch.CapableFulfilling;
      }
      if (this.stage.filterInactive !== 2) {
        request.Inactive = this.stage.filterInactive;
      }
      if (this.fullTextSearch !== '') {
        request.fullTextSearch = this.fullTextSearch;
      }
      if (this.filter.ProjectLink.length) {
        let haveLink = false;
        _.forEach(this.filter.ProjectLink, function (projectLink, key) {
          if (projectLink.LinkID) {
            haveLink = true;
            return false;
          }
        });
        if (haveLink) {
          request.ProjectLink = this.filter.ProjectLink;
        }
      }
      request.exportData = true;

      let reportData = await this.$_lists_handleReportResponse('listing/api/project/get-report-data', request);
      this.$_lists_handleDowloadExcel(reportJson, reportData, 'Project');
    },
  },
  watch: {
    currentPage() {
      this.fetchData();
    },
    'modelSearch.StartedDate.dateTime'(){
      if (this.modelSearch.StartedDate.dateTime) {
        this.$_lists_handleSubmitSearch();
      }
    },
    'modelSearch.FinishedDate.dateTime'(){
      if (this.modelSearch.FinishedDate.dateTime) {
        this.$_lists_handleSubmitSearch();
      }
    },
  }
}
</script>

<style lang="css">
.main-footer-pagination ul {
  margin-bottom: 0;
}
.component-project-list .component-compare-time .form-group {
  margin-bottom: 0;
}
.component-project-list table .component-compare-time .dropdown-toggle,
.component-project-list table .component-compare-number .dropdown-toggle{
  padding-left: 5px;
  padding-right: 5px;
}
.component-project-list table .component-compare-time .mx-input-wrapper,
.component-project-list table .component-compare-time .mx-datepicker{
  width: 105px !important
}
.component-project-list table .component-compare-time .mx-datepicker input,
.component-project-list .component-compare-number input{
  padding-left: 5px;
}
.component-project-list .component-compare-number .form-group {
  margin-bottom: 0;
}
.component-project-list .component-compare-time .mx-input-wrapper {
  width: auto !important;
}
.component-project-list .custom-select {
  background-color: #fff;
}
</style>
