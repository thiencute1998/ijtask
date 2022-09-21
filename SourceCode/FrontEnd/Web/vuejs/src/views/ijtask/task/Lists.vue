<template>
    <div class="component-task-list">
        <div class="main-entry">
            <div class="main-header">
                <div class="main-header-padding">
                  <b-row class="mb-2">
                      <b-col md="6" class="col-24">
                          <div class="main-header-item main-header-name">
                              <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Công việc</span>
                          </div>
                      </b-col>
                      <b-col md="6" class="col-24"></b-col>
                  </b-row>
                  <b-row class="mb-2">
                    <b-col class="col-lg-16 col-md-24 col-sm-24 col-24 mb-2 mb-lg-0 d-flex flex-wrap justify-content-start align-items-center">
                      <div class="main-header-item main-header-actions">
                        <b-button class="main-header-action mr-2" variant="primary" @click="$_lists_handleAddNewItem" size="md">
                          <i class="fa fa-plus"></i> Thêm
                        </b-button>

                        <b-dropdown variant="primary" id="dropdown-actions" @toggle="$_lists_onToggleActionMajor" class="main-header-action mr-2" text="Thao tác">
                          <b-dropdown-item @click="$_lists_handleDeleteItem" :disabled="stage.disableActions">Xóa</b-dropdown-item>
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
                          <b-dropdown-item :disabled="stage.disableActions || !stage.actionInactive.showInactive" v-if="stage.actionInactive.inactive == 0" @click="$_lists_handleChangeInActive">Đang hoạt động</b-dropdown-item>
                          <b-dropdown-item :disabled="stage.disableActions || !stage.actionInactive.showInactive" v-if="stage.actionInactive.inactive == 1" @click="$_lists_handleChangeInActive">Ngừng hoạt động</b-dropdown-item>
                        </b-dropdown>
                      </div>
                      <div style="position: inherit;" class="mr-2">
                        <b-form-select
                          v-model="filter.Type" style="max-width: 200px"
                          class="task-filter-type"
                          @change="onChangeTaskType($event)"
                          :options="TypeOptions">
                        </b-form-select>
                        <i class="fa fa-external-link" v-if="filter.Type == 9" @click="showModalTcatelist" style="position: absolute; top: 10px; right: 33px; cursor: pointer"></i>
                      </div>
                        <div class="main-header-item main-header-search mb-sm-2 mt-2 mt-md-2 mt-lg-2 mt-xl-0" style="flex: 1 1 auto">
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
<!--                          <b-dropdown v-if="stage.viewType === 'tree'" id="dropdown-per-page" title="Cấp của cây" menu-class="p-0" :text="filter.Level" class="app-dropdown-center main-header-icon">-->
<!--                            <b-dropdown-item @click="changePerPage(1)">1</b-dropdown-item>-->
<!--                            <b-dropdown-item @click="changePerPage(2)">2</b-dropdown-item>-->
<!--                            <b-dropdown-item @click="changePerPage(3)">3</b-dropdown-item>-->
<!--                            <b-dropdown-item @click="changePerPage(4)">4</b-dropdown-item>-->
<!--                            <b-dropdown-item @click="changePerPage(5)">5</b-dropdown-item>-->
<!--                            <b-dropdown-item @click="changePerPage(6)">6</b-dropdown-item>-->
<!--                            <b-dropdown-item @click="changePerPage(7)">7</b-dropdown-item>-->
<!--                            <b-dropdown-item @click="changePerPage(8)">8</b-dropdown-item>-->
<!--                            <b-dropdown-item @click="changePerPage(9)">9</b-dropdown-item>-->
<!--                            <b-dropdown-item @click="changePerPage(10)">10</b-dropdown-item>-->
<!--                          </b-dropdown>-->
                          <b-button id="tooltip-view-kanban" title="Thẻ tin" @click="stage.viewType = 'kanban'" :class="[(stage.viewType === 'kanban') ? 'view-active' : '']" class="main-header-view"><i class="fa fa-th"></i></b-button>
                          <b-button id="tooltip-view-workflow" title="Quy trình" @click="onClickWorkflow" :class="[(stage.viewType === 'workflow') ? 'view-active' : '']" class="main-header-view"><i class="fa fa-list-ol"></i></b-button>
                          <b-button id="tooltip-view-line-chart" title="Biểu đồ tiến độ" @click="stage.viewType = 'line-chart'" :class="[(stage.viewType === 'line-chart') ? 'view-active' : '']" class="main-header-view"><i class="fa fa-line-chart"></i></b-button>
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
                        <div class="col-4 mb-2" v-if="stage.viewType === 'kanban'">
                          <b-form-input
                            title="Nhập tên công việc"
                            v-model="filter.TaskName"
                            @keyup.enter="$_lists_handleSubmitSearch"
                            placeholder="Nhập tên công việc..."></b-form-input>
                        </div>
                        <div class="col-8 mb-2">
                          <ijcore-date-range v-model="filter.DateRange"></ijcore-date-range>
                        </div>
                        <div class="col-4 mb-2" v-if="stage.viewType === 'kanban'">
                          <b-form-select
                            title="Mức độ ưu tiên"
                            v-model="filter.Priority"
                            :options="PriorityOptions">
                          </b-form-select>
                        </div>
                        <div class="col-4 mb-2">
                          <b-form-select v-model="filter.AccessType" :options="AccessTypeOptions" placeholder="Quyền truy cập"></b-form-select>
                        </div>

                        <div class="col-4 mb-2">
                          <ijcore-modal-search-input
                            v-model="filter.Company"
                            :select-fields-api="[
                              {field: 'CompanyID',fieldForSelected: 'id', showInTable: false, key: 'CompanyID'},
                              {field: 'CompanyName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'CompanyName', sortable: true, thClass: 'd-none'}
                            ]"
                            :search-fields-api="[{field: 'CompanyName', placeholder: 'Nhập tên', name: 'CompanyName', class: '', style: ''},]"
                            tableApi="company"
                            ref="myModalSearchInputCompany"
                            id-modal="myModalSearchInputCompany"
                            :item-per-page="8"
                            placeholder="Đơn vị"
                            :url-api="$store.state.appRootApi + '/task/api/common/get-company'"
                            name-input="input-company"
                            title-modal="Đơn vị" size-modal="lg">
                          </ijcore-modal-search-input>
                        </div>
                        <div class="col-4 mb-2 d-flex align-items-center" v-if="stage.viewType === 'kanban'">
                          <ijcore-compare-time
                            title="Ngày tạo"
                            style="width: 100%"
                            @clear-date-picker="fetchData" v-model="filter.CreateDate">
                          </ijcore-compare-time>
                        </div>
                        <div class="col-4 mb-2 d-flex align-items-center" v-if="stage.viewType === 'kanban'">
                          <ijcore-compare-time
                            title="Ngày bắt đầu"
                            style="width: 100%"
                            @clear-date-picker="fetchData" v-model="filter.StartDate">
                          </ijcore-compare-time>
                        </div>
                        <div class="col-4 mb-2 d-flex align-items-center" v-if="stage.viewType === 'kanban'">
                          <ijcore-compare-time
                            title="Hạn hoàn thành"
                            style="width: 100%"
                            @clear-date-picker="fetchData" v-model="filter.DueDate">
                          </ijcore-compare-time>
                        </div>
                        <div class="col-4 mb-2 d-flex align-items-center" v-if="stage.viewType === 'kanban'">
                          <ijcore-compare-number
                            style="width: 100%"
                            title="Phần trăm hoàn thành"
                            @keyup-compare-input="fetchData"
                            v-model="filter.PercentCompleted"></ijcore-compare-number>
                        </div>
                        <div class="col-4 mb-2" v-if="stage.viewType === 'kanban'">
                          <ijcore-select2-server
                            v-model="filter.ImplementEmployeeID"
                            :url="$store.state.appRootApi + '/task/api/common/get-employee'"
                            title="Người thực hiện"
                            id-name="EmployeeID"
                            text-name="EmployeeName"
                            placeholder="Người thực hiện"
                            :allowClear="true"
                            :delay="1000">
                          </ijcore-select2-server>
                        </div>
                        <div class="col-4 mb-2">
                          <ijcore-select2-server
                            v-model="filter.CreateEmployeeID"
                            :url="$store.state.appRootApi + '/task/api/common/get-employee'"
                            id-name="EmployeeID"
                            text-name="EmployeeName"
                            placeholder="Người tạo"
                            title="Người tạo"
                            :allowClear="true"
                            :delay="1000">
                          </ijcore-select2-server>
                        </div>
                        <div class="col-4 mb-2">
                          <ijcore-select2-server
                            v-model="filter.AssignEmployeeID"
                            :url="$store.state.appRootApi + '/task/api/common/get-employee'"
                            id-name="EmployeeID"
                            text-name="EmployeeName"
                            placeholder="Người giao việc"
                            title="Người giao việc"
                            :allowClear="true"
                            :delay="1000">
                          </ijcore-select2-server>
                        </div>
<!--                        <div class="col-4 mb-2">-->
<!--                          <ijcore-select2-server-->
<!--                            v-model="filter.ReceiveEmployeeID"-->
<!--                            :url="$store.state.appRootApi + '/task/api/common/get-employee'"-->
<!--                            id-name="EmployeeID"-->
<!--                            text-name="EmployeeName"-->
<!--                            placeholder="Người nhận việc"-->
<!--                            title="Người nhận việc"-->
<!--                            :allowClear="true"-->
<!--                            :delay="1000">-->
<!--                          </ijcore-select2-server>-->
<!--                        </div>-->
                        <div class="col-4 mb-2">
                          <ijcore-select2-server
                            v-model="filter.ResponEmployeeID"
                            :url="$store.state.appRootApi + '/task/api/common/get-employee'"
                            id-name="EmployeeID"
                            text-name="EmployeeName"
                            placeholder="Người chịu trách nhiệm"
                            title="Người chịu trách nhiệm"
                            :allowClear="true"
                            :delay="1000">
                          </ijcore-select2-server>
                        </div>
                        <div class="col-4 mb-2" v-if="stage.viewType === 'kanban'">
                          <task-modal-search-sys-status
                            v-model="filter.Status"
                            tableApi="sys_status"
                            refModal="myModalSearchSysStatus"
                            id-modal="myModalSearchSysStatus"
                            placeholder="Trạng thái"
                            @onSubmitSearch="fetchData"
                            @onClear="fetchData"
                            title-modal="Loại trạng thái" size-modal="lg"></task-modal-search-sys-status>
                        </div>
                        <div class="col-4 mb-2 mb-2">
                          <ijcore-modal-search-input
                            v-model="filter.Doc"
                            :select-fields-api="[
                              {field: 'DocID',fieldForSelected: 'id', showInTable: false, key: 'DocID'},
                              {field: 'DocNo', showInTable: true, label: 'Mã số', key: 'DocNo', thStyle: 'width: 15%'},
                              {field: 'DocName', fieldForSelected: 'name', showInTable: true, label: 'Tên tài liệu', key: 'DocName', sortable: true}
                            ]"
                            :search-fields-api="[
                              {field: 'DocNo', placeholder: '', name: 'DocNo', class: '', style: ''},
                              {field: 'DocName', placeholder: '', name: 'DocName', class: '', style: ''},
                              ]"
                            tableApi="doc"
                            ref="myModalSearchInputDoc"
                            id-modal="myModalSearchInputDoc"
                            :item-per-page="8"
                            placeholder="Tài liệu"
                            :url-api="$store.state.appRootApi + '/task/api/common/get-doc'"
                            name-input="input-doc"
                            title-modal="Tài liệu" size-modal="lg">
                          </ijcore-modal-search-input>
                        </div>
                        <div class="col-4 mb-2">
                          <task-modal-search-link
                            v-model="filter.TaskLink"
                            ref="myModalSearchInputLink"
                            placeholder="Danh mục liên kết"
                            @onSubmitSearch="fetchData"
                            @onClear="fetchData"
                            title-modal="Danh mục liên kết" size-modal="lg"
                            id-modal="myModalSearchInputLink">
                          </task-modal-search-link>
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

              <task-modal-search-tcatelist
                v-model="filter.TaskCateList"
                tableApi="task_cate_list"
                refModal="myModalSearchTcatelist"
                id-modal="myModalSearchTcatelist"
                @onSubmitSearch="fetchData"
                title-modal="Loại công việc" size-modal="lg"></task-modal-search-tcatelist>
            </div>
            <div class="main-body" :class="[(stage.viewType !== 'kanban') ? 'main-body-view-list' : 'main-body-view-kanban', (stage.viewType === 'tree') ? 'main-body-view-tree' : '']">
                <b-card class="m-0 border-0" body-class="py-0 px-0">
                    <div class="content-body">
                      <div class="content-table content-body-list" v-if="stage.viewType === 'list' || stage.viewType === 'tree'">
                        <div class="b-table-sticky-header table-responsive">
                          <table role="table" aria-busy="false" aria-colcount="2" aria-multiselectable="true"
                                 class="table b-table table-hover table-sm table-tree b-table-selectable b-table-select-multi">
                            <thead role="rowgroup" class="thead-light">
                            <tr role="row" class="">
                              <th role="columnheader" scope="col" aria-label="Selected" class="pl-3" style="width: 3%;">
                                  <b-form-checkbox class="text-left" @input="onToggleSelectAllRows($event)"></b-form-checkbox>
                              </th>
                              <th role="columnheader" scope="col" style="width: 15%; min-width: 250px" aria-sort="none" @click="sortTableByField($event, 'TaskName')" v-if="stage.viewType === 'list'">Tên công việc</th>
                              <th role="columnheader" scope="col" style="width: 15%; min-width: 250px" v-else>Tên công việc</th>
                              <th role="columnheader" class="d-md-none d-lg-none d-xl-table-cell" scope="col" style="width: 6%; min-width: 120px" aria-sort="none" @click="sortTableByField($event, 'Priority')" v-if="stage.viewType === 'list'">Ưu tiên</th>
                              <th role="columnheader" class="d-md-none d-lg-none d-xl-table-cell" scope="col" style="width: 6%; min-width: 120px" v-else>Ưu tiên</th>
                              <th role="columnheader" class="d-md-none d-lg-none d-xl-table-cell" scope="col" style="width: 8%; min-width: 120px" aria-sort="none" @click="sortTableByField($event, 'CreateDate')" v-if="stage.viewType === 'list'">Ngày tạo</th>
                              <th role="columnheader" class="d-md-none d-lg-none d-xl-table-cell" scope="col" style="width: 8%; min-width: 120px" v-else>Ngày tạo</th>
                              <th role="columnheader" class="d-md-none d-lg-none d-xl-table-cell" scope="col" title="Ngày bắt đầu" style="width: 8%; min-width: 120px" aria-sort="none" @click="sortTableByField($event, 'StartDate')" v-if="stage.viewType === 'list'">Ngày BĐ</th>
                              <th role="columnheader" class="d-md-none d-lg-none d-xl-table-cell" scope="col" title="Ngày bắt đầu" style="width: 8%; min-width: 120px" v-else>Ngày BĐ</th>
                              <th role="columnheader" scope="col" title="Hạn hoàn thành" style="width: 8%; min-width: 120px" aria-sort="none" @click="sortTableByField($event, 'DueDate')" v-if="stage.viewType === 'list'">Hạn HT</th>
                              <th role="columnheader" scope="col" title="Hạn hoàn thành" style="width: 8%; min-width: 120px" v-else>Hạn HT</th>
                              <th role="columnheader" class="d-md-none d-lg-none d-xl-table-cell" scope="col" title="Người thực hiện" style="width: 8%; min-width: 120px">Người TH</th>
                              <th role="columnheader" class="d-md-none d-lg-none d-xl-table-cell" scope="col" style="width: 6%; min-width: 120px">Trạng thái</th>
                              <th role="columnheader" scope="col" title="Phần trăm hoàn thành" style="width: 5%; min-width: 75px" aria-sort="none" @click="sortTableByField($event, 'PercentCompleted')" v-if="stage.viewType === 'list'">% HT</th>
                              <th role="columnheader" scope="col" title="Phần trăm hoàn thành" style="width: 5%; min-width: 75px" v-else>% HT</th>
                            </tr>
                            </thead>
                            <tbody role="rowgroup">
                              <tr role="row" :key="'table-top-row'" class="b-table-top-row">
                                <td role="cell" class="pl-3"></td>
                                <td role="cell" class="">
                                  <b-form-input
                                    v-model="filter.TaskName"
                                    @keyup.enter="$_lists_handleSubmitSearch"></b-form-input>
                                </td>
                                <td class="d-md-none d-lg-none d-xl-table-cell" role="cell">
                                  <b-form-select
                                    v-model="filter.Priority"
                                    :options="PriorityOptions">
                                  </b-form-select>
                                </td>
                                <td class="d-md-none d-lg-none d-xl-table-cell no-overflow" role="cell">
                                  <ijcore-compare-time @clear-date-picker="fetchData" v-model="filter.CreateDate">
                                  </ijcore-compare-time>
                                </td>
                                <td class="d-md-none d-lg-none d-xl-table-cell no-overflow" role="cell">
                                  <ijcore-compare-time @clear-date-picker="fetchData" v-model="filter.StartDate"></ijcore-compare-time>
                                </td>
                                <td role="cell" class="no-overflow">
                                  <ijcore-compare-time @clear-date-picker="fetchData" v-model="filter.DueDate"></ijcore-compare-time>
                                </td>
                                <td class="d-md-none d-lg-none d-xl-table-cell" role="cell">
                                  <ijcore-select2-server
                                    v-model="filter.ImplementEmployeeID"
                                    :url="$store.state.appRootApi + '/task/api/common/get-employee'"
                                    id-name="EmployeeID"
                                    text-name="EmployeeName"
                                    placeholder="Người thực hiện"
                                    :allowClear="true"
                                    :delay="500">
                                  </ijcore-select2-server>
                                </td>
                                <td class="d-md-none d-lg-none d-xl-table-cell" role="cell">
                                  <task-modal-search-sys-status
                                    v-model="filter.Status"
                                    tableApi="sys_status"
                                    refModal="myModalSearchSysStatus"
                                    id-modal="myModalSearchSysStatus"
                                    placeholder=""
                                    @onSubmitSearch="fetchData"
                                    @onClear="fetchData"
                                    title-modal="Loại trạng thái" size-modal="lg"></task-modal-search-sys-status>

                                </td>
                                <td role="cell" class="no-overflow">
                                  <ijcore-compare-number @keyup-compare-input="fetchData" v-model="filter.PercentCompleted"></ijcore-compare-number>
                                </td>
                              </tr>
                              <tr role="row" tabindex="0"
								                  :id="'table-item-' + item.TaskID"
                                  v-show="item.Show"
                                  aria-selected="false"
                                  :class="[(!item.ParentID) ? 'task-parent' : 'task-children', (itemsArray[key].rowSelected) ? 'b-table-row-selected table-active' : '']"
                                  :data-parent-id="item.ParentID"
                                  v-if="!itemsArray[key].hidden"
                                  :key="key"
                                  v-for="(item, key) in itemsArray">
                                <td aria-colindex="1" role="cell" class="pl-3">
                                  <b-form-checkbox v-model="itemsArray[key].rowSelected" class="text-left" @input="onToggleRowSelected($event, item, key)"></b-form-checkbox>
                                </td>
                                <td v-if="stage.viewType === 'tree'" aria-colindex="2" role="cell" class="field-task-name bg-tree-tr p-0">
                                  <span class="bg-tree-dot" :style="{'left': (level * 12) + 'px'}" v-for="level in (itemsArray[key].Level - 1)"></span>
                                  <div class="bg-tree-content bg-tree-td"
                                       :style="{'margin-left': (itemsArray[key].Level * 12 - 12) + 'px', width: 'calc(100% - ' + (itemsArray[key].Level * 12 - 12) + 'px)'}" style="position: absolute;">
                                    <a :href="'#/task/task/view/' + itemsArray[key].TaskID" class="table-list-link" style="margin-left: 16px" :style="{width: 'calc(100% - ' + (itemsArray[key].Level * 12 - 12 + 16) + 'px)'}" @click="onRowClicked(item)" :title="item.TaskName | stripHtml">{{item.TaskName | stripHtml}}</a>
<!--                                    <span style="margin-left: 16px" :style="{width: 'calc(100% - ' + (itemsArray[key].Level * 12 - 12 + 16) + 'px)'}" @click="onRowClicked(item)" :title="item.TaskName | stripHtml">{{item.TaskName | stripHtml}}</span>-->
                                    <i class="fa fa-plus-square-o bg-tree-icon-action" v-if="!itemsArray[key].Detail" @click="onToggleChildNodes($event, item)"></i>
                                  </div>
                                </td>
                                <td v-if="stage.viewType === 'list'">
                                  <div @click="onRowClicked(item)">
                                    <span :title="getTaskName(item)">{{getTaskName(item)}}</span>
                                  </div>
                                </td>
                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="d-md-none d-lg-none d-xl-table-cell">
                                  <span>{{Priority[item.Priority]}}</span>
                                </td>
                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="d-md-none d-lg-none d-xl-table-cell text-center">
                                  <span>{{item.CreateDate | convertServerDateToClientDate}}</span>
                                </td>
                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="d-md-none d-lg-none d-xl-table-cell text-center">
                                  <span>{{item.StartDate | convertServerDateToClientDate}}</span>
                                </td>
                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="text-center">
                                  <span>{{item.DueDate | convertServerDateToClientDate}}</span>
                                </td>
                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="d-md-none d-lg-none d-xl-table-cell">
                                  <ijcore-users-icon :all-users="model.taskAssign" filter-name="TaskID" :filter-value="item.TaskID" :number="4"></ijcore-users-icon>
                                </td>
                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="d-md-none d-lg-none d-xl-table-cell">
                                  <span>{{item.StatusDescription}}</span>
                                </td>
                                <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="text-right pr-3">
                                  <div class="d-inline-flex align-items-center justify-content-end">
                                    <b-badge :variant="(item.PercentCompleted <= 0) ? 'warning' : (item.PercentCompleted > 0 && item.PercentCompleted < 100) ? 'primary' : 'success'">{{item.PercentCompleted}}</b-badge>
                                  </div>
                                </td>
                              </tr>
                            </tbody>
<!--                            <tbody role="rowgroup">-->
<!--                              -->
<!--                              </tbody>-->
                            </table>
                        </div>

                        </div>
                      <div class="content-body-kanban" v-if="stage.viewType === 'kanban'">
                        <task-kanban v-model="itemsArray" :priority="Priority" :task-assign="model.taskAssign"></task-kanban>
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
    import TaskModalSearchLink from "./partials/TaskModalSearchLink";
    import TaskModalSearchTcatelist from "./partials/TaskModalSearchTcatelist";
    import TaskModalSearchSysStatus from "./partials/TaskModalSearchSysStatus";
    import IjcoreDatePicker from "@/components/IjcoreDatePicker";
    import IjcoreCompareNumber from "@/components/IjcoreCompareNumber";
    import ClickOutside from 'vue-click-outside';
    import TaskKanban from "./partials/TaskKanban";

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
              TaskName: (this.$route.query && this.$route.query.TaskName) ? this.$route.query.TaskName : '',
              Type: (this.$route.query && this.$route.query.Type) ? this.$route.query.Type : null,
              CreateDate: (this.$route.query && this.$route.query.CreateDate) ? this.$route.query.CreateDate : null,
              StartDate: (this.$route.query && this.$route.query.StartDate) ? this.$route.query.StartDate : null,
              DueDate: (this.$route.query && this.$route.query.DueDate) ? this.$route.query.DueDate : null,
              DateRange: (this.$route.query && this.$route.query.DateRange) ? this.$route.query.DateRange : null,
              PercentCompleted: (this.$route.query && this.$route.query.PercentCompleted) ? this.$route.query.PercentCompleted : null,
              Priority: (this.$route.query && this.$route.query.Priority) ? this.$route.query.Priority : null,
              AccessType: (this.$route.query && this.$route.query.AccessType) ? this.$route.query.AccessType : null,
              Status: (this.$route.query && this.$route.query.Status) ? this.$route.query.Status : [],
              Company: (this.$route.query && this.$route.query.Company) ? this.$route.query.Company : null,
              Project: (this.$route.query && this.$route.query.Project) ? this.$route.query.Project : null,
              CreateEmployeeID: (this.$route.query && this.$route.query.CreateEmployeeID) ? this.$route.query.CreateEmployeeID : null,
              AssignEmployeeID: (this.$route.query && this.$route.query.AssignEmployeeID) ? this.$route.query.AssignEmployeeID : null,
              ReceiveEmployeeID: (this.$route.query && this.$route.query.ReceiveEmployeeID) ? this.$route.query.ReceiveEmployeeID : null,
              ResponEmployeeID: (this.$route.query && this.$route.query.ResponEmployeeID) ? this.$route.query.ResponEmployeeID : null,
              ImplementEmployeeID: (this.$route.query && this.$route.query.ImplementEmployeeID) ? this.$route.query.ImplementEmployeeID : null,
              Contract: (this.$route.query && this.$route.query.Contract) ? this.$route.query.Contract : null,
              Customer: (this.$route.query && this.$route.query.Customer) ? this.$route.query.Customer : null,
              Vendor: (this.$route.query && this.$route.query.Vendor) ? this.$route.query.Vendor : null,
              Object: (this.$route.query && this.$route.query.Object) ? this.$route.query.Object : null,
              Item: (this.$route.query && this.$route.query.Item) ? this.$route.query.Item : null,
              Doc: (this.$route.query && this.$route.query.Doc) ? this.$route.query.Doc : null,
              TaskLink: (this.$route.query && this.$route.query.TaskLink) ? this.$route.query.TaskLink : [],
              TaskCateList: (this.$route.query && this.$route.query.TaskCateList) ? this.$route.query.TaskCateList : [],
              Level: '1'

            },
            stage: {
              // viewType: ['list', 'tree', 'workflow', 'line-chart]
              viewType: (this.$store.state.optionBehavior.viewType) ? this.$store.state.optionBehavior.viewType : 'list',
              showModalSearchTcatelist: false
            },

            Priority: [],
            PriorityOptions: [],
            AccessType: [],
            AccessTypeOptions: [],
            TypeOptions: []
          }
      },
      components:{
        IjcoreUsersIcon,
        IjcoreCompareTime,
        IjcoreDateRange,
        IjcoreModalSearchInput,
        IjcoreSelect2Server,
        TaskModalSearchLink,
        TaskModalSearchTcatelist,
        IjcoreDatePicker,
        IjcoreCompareNumber,
        TaskModalSearchSysStatus,
        TaskKanban,
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
            {key: 'TaskName', label: 'Tên công việc', field: 'TaskName',searchOnTopRow: {type: 'text'}, thStyle: 'width: 15%'},
            {key: 'Priority', label: 'Ưu tiên', field: 'Priority',searchOnTopRow: {type: 'text'}, thStyle: 'width: 8%'},
            {key: 'CreateDate', label: 'Ngày tạo', field: 'CreateDate',searchOnTopRow: {type: 'text'}, thStyle: 'width: 8%'},
            {key: 'StartDate', label: 'Ngày BĐ', headerTitle: 'Ngày bắt đầu', field: 'StartDate',searchOnTopRow: {type: 'text'}, thStyle: 'width: 8%'},
            {key: 'DueDate', label: 'Ngày HT', headerTitle: 'Ngày hoàn thành', field: 'DueDate',searchOnTopRow: {type: 'text'}, thStyle: 'width: 8%'},
            {key: 'TaskAssign', label: 'Người thực hiện', field: 'TaskAssign',searchOnTopRow: {type: 'text'}, thStyle: 'width: 8%'},
            {key: 'StatusID', label: 'Tình trạng', field: 'StatusID',searchOnTopRow: {type: 'text'}, thStyle: 'width: 8%'},
            {key: 'PercentCompleted', label: '%HT', headerTitle: 'Phần trăm hoàn thành', field: 'PercentCompleted',searchOnTopRow: {type: 'text'}, thStyle: 'width: 5%'},
          ];

          return fieldsTable;
        }
      },
      created() {
        // init setting
        this.settings.FieldID = 'TaskID';
        this.settings.Table = 'task';
        this.settings.FieldInactive = 'Inactive';

        this.settings.ListApi = 'task/api/task';
        this.settings.DeleteApi = 'task/api/task/delete';
        this.settings.CreateRouter = 'task-task-create';
        this.settings.EditRouter = 'task-task-edit';
        this.settings.ViewRouter = 'task-task-view';

        this.modelSearch = {
          TaskName: ''
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
              filter: {}
            },

          };

          if (this.filter.TaskName !== '') {
            requestData.data.filter.TaskName = this.filter.TaskName;
          }
          if (this.filter.Type) {
            requestData.data.filter.Type = this.filter.Type;
          }
          if (this.filter.DateRange) {
            requestData.data.filter.DateRange = this.filter.DateRange;
          }
          if (this.filter.Priority) {
            requestData.data.filter.Priority = this.filter.Priority;
          }
          if (this.filter.CreateDate && !_.isEmpty(this.filter.CreateDate.dateTime) && this.filter.CreateDate.dateTime !== '__/__/____') {
            requestData.data.filter.CreateDate = this.filter.CreateDate;
          }
          if (this.filter.StartDate && !_.isEmpty(this.filter.StartDate.dateTime) && this.filter.StartDate.dateTime !== '__/__/____') {
            requestData.data.filter.StartDate = this.filter.StartDate;
          }
          if (this.filter.DueDate && !_.isEmpty(this.filter.DueDate.dateTime) && this.filter.DueDate.dateTime !== '__/__/____') {
            requestData.data.filter.DueDate = this.filter.DueDate;
          }
          if (this.filter.Status && this.filter.Status.length) {
            requestData.data.filter.Status = this.filter.Status;
          }
          if (this.filter.PercentCompleted) {
            requestData.data.filter.PercentCompleted = this.filter.PercentCompleted;
          }
          if (this.filter.Company) {
            requestData.data.filter.CompanyID = this.filter.Company.CompanyID;
          }
          if (this.filter.Doc) {
            requestData.data.filter.DocID = this.filter.Doc.DocID;
          }
          if (this.filter.DateRange && this.filter.DateRange.fromDate && this.filter.DateRange.fromDate !== '' && this.filter.DateRange.fromDate !== '__/__/____') {
            requestData.data.filter.fromDate = this.filter.DateRange.fromDate;
          }
          if (this.filter.DateRange && this.filter.DateRange.toDate && this.filter.DateRange.toDate !== '' && this.filter.DateRange.toDate !== '__/__/____') {
            requestData.data.filter.toDate = this.filter.DateRange.toDate;
          }
          if (this.filter.AccessType) {
            requestData.data.filter.AccessType = this.filter.AccessType;
          }
          if (this.filter.CreateEmployeeID) {
            requestData.data.filter.CreateEmployeeID = this.filter.CreateEmployeeID;
          }
          if (this.filter.AssignEmployeeID) {
            requestData.data.filter.AssignEmployeeID = this.filter.AssignEmployeeID;
          }
          if (this.filter.ReceiveEmployeeID) {
            requestData.data.filter.ReceiveEmployeeID = this.filter.ReceiveEmployeeID;
          }
          if (this.filter.ResponEmployeeID) {
            requestData.data.filter.ResponEmployeeID = this.filter.ResponEmployeeID;
          }
          if (this.filter.ImplementEmployeeID) {
            requestData.data.filter.ImplementEmployeeID = this.filter.ImplementEmployeeID;
          }
          if (this.filter.TaskLink.length) {
            requestData.data.filter.TaskLink = this.filter.TaskLink;
          }
          if (this.filter.TaskCateList.length) {
            requestData.data.filter.TaskCateList = this.filter.TaskCateList;
          }
          if (this.stage.filterInactive !== 2) {
            requestData.data.filter.Inactive = this.stage.filterInactive;
          }

          if (this.fullTextSearch !== '') {
            requestData.data.fullTextSearch = this.fullTextSearch;
          }

          if (this.filter.Level !== 1) {
            requestData.data.filter.Level = this.filter.Level;
          }
          if (!_.isEmpty(requestData.data.filter)){
            if (!_.isEqual(this.$route.query, requestData.data.filter)) {
              this.$router.replace({query: requestData.data.filter});
            }
          }
          this.$store.commit('isLoading', true);
          ApiService.customRequest(requestData).then((response) => {
            let responseData = response.data;
            if (responseData.status === 1) {
              self.totalRows = responseData.data.data.total;
              self.perPage = String(responseData.data.data.per_page);
              self.currentPage = responseData.data.data.current_page;
              // converse object to array
              self.itemsArray = _.toArray(responseData.data.data.data);
              self.itemsArray = _.uniqBy(self.itemsArray, 'TaskID');
			  _.forEach(self.itemsArray, function (item, key) {
                self.$set(self.itemsArray[key], 'Show', true);
                self.$set(self.itemsArray[key], 'HaveChildren', (item.Detail) ? false : true);
              });
              // if (self.stage.viewType === 'tree') {
              //   self.itemsArray = _.orderBy(self.itemsArray, ['TaskNo', 'asc']);
              // }
              self.model.taskAssign = responseData.data.taskAssign;
              self.Priority = responseData.data.Priority;
              self.PriorityOptions = [{value: null, text: '-- Ưu tiên --'}];
              _.forEach(self.Priority, function (value, key) {
                let tmpObj = {};
                tmpObj.value = key;
                tmpObj.text = value;
                self.PriorityOptions.push(tmpObj);
              });
              self.AccessType = responseData.data.AccessType;
              self.AccessTypeOptions = [{value: null, text: '-- Quyền truy cập --'}];
              _.forEach(self.AccessType, function (value, key) {
                let tmpObj = {};
                tmpObj.value = key;
                tmpObj.text = value;
                self.AccessTypeOptions.push(tmpObj);
              });

              self.TypeOptions = [
                {value: null, text: '-- Loại công việc --'},
              ];
              _.forEach(responseData.data.Type, function (type, key) {
                let tmpObj = {};
                tmpObj.value = key;
                tmpObj.text = type;
                self.TypeOptions.push(tmpObj);
              });

              // set params request
              self.paramsReqRouter.lastPage = responseData.data.data.last_page;
              self.paramsReqRouter.from = responseData.data.data.from;
              self.paramsReqRouter.to = responseData.data.data.to;
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
        getTaskName(task){
          let name = '';
          if (task.Type === 2 && task.ParentName) {
            name = task.TaskName + ' (' + task.ParentName + ')';
          } else {
            name = task.TaskName;
          }
          name = __.stripHtml(name);
          return name;
        },
        getAllChildTask(item, taskArr){
            let self = this, listChild = [];
            let allChild = _.filter(taskArr, ['ParentID', item.TaskID]);
            if (allChild.length) {
                allChild = _.orderBy(allChild, ['TaskID'], ['asc']);
                _.forEach(allChild, function (value, key) {
                    listChild.push(value);
                    if (_.filter(taskArr, ['ParentID', value.TaskID]).length) {
                        let recursiveArr = self.getAllChildTask(value, taskArr);
                        recursiveArr = _.orderBy(recursiveArr, ['TaskID', 'asc']);
                        _.forEach(recursiveArr, function (recusive, key) {
                            listChild.push(recusive);
                        });
                    }

                });
            }
            return listChild;
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
            alert('print');
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
        onClickWorkflow(){
          this.$router.push({
            name: 'task-dataflow'
          });
        },
        onToggleChildNodes(e, itemParent) {
          let self = this;
          let taskChild = _.filter(self.itemsArray, ['ParentID', itemParent.TaskID]);

          if (taskChild && taskChild.length) {
            if (e && e.target.classList.contains('fa-minus-square-o')) {
              // close children
              e.target.classList.remove('fa-minus-square-o');
              e.target.classList.add('fa-plus-square-o');
              let allChildTableItem = this.getAllChildTableItem(itemParent, self.itemsArray);
              if (allChildTableItem && allChildTableItem.length) {
                _.forEach(allChildTableItem, function (childTableItem, key) {
                  let indexItem = _.findIndex(self.itemsArray, ['TaskID', childTableItem.TaskID]);
                  if (indexItem > -1) {
                    self.itemsArray[indexItem].Show = false;
                  }
                });
              }
            }else {
              // open children
              e.target.classList.remove('fa-plus-square-o');
              e.target.classList.add('fa-minus-square-o');
              let allChildren = _.filter(this.itemsArray, ['ParentID', itemParent.TaskID]);
              if (allChildren.length) {
                _.forEach(allChildren, function (childTableItem, key) {
                  let indexItem = _.findIndex(self.itemsArray, ['TaskID', childTableItem.TaskID]);
                  if (indexItem > -1) {
                    self.itemsArray[indexItem].Show = true;
                    $('#table-item-' + self.itemsArray[indexItem].TaskID + ' .bg-tree-icon-action').removeClass('fa-minus-square-o');
                    $('#table-item-' + self.itemsArray[indexItem].TaskID + ' .bg-tree-icon-action').addClass('fa-plus-square-o');
                  }
                });
              }
            }
          }else {
            let urlApi = 'task/api/task/get-list-child';
            let requestData = {
              method: 'post',
              url: urlApi,
              data: {
                ParentID: itemParent.TaskID
              }
            };
            this.$store.commit('isLoading', true);
            ApiService.customRequest(requestData).then((response) => {
              let responseData = response.data;
              if (responseData.status === 1) {
                e.target.classList.remove('fa-plus-square-o');
                e.target.classList.add('fa-minus-square-o');
                let indexParent = _.findIndex(self.itemsArray, ['TaskID', itemParent.TaskID]);
                if (indexParent > -1) {
                  _.forEach(responseData.data, function (item, key) {
                    let keyTmp = indexParent + key + 1;
                    item.Show = true;
                    item.HaveChildren = (item.Detail) ? false : true;
                    self.itemsArray = __.insertBeforeKey(self.itemsArray, keyTmp, item);
                  });

                }
                if (responseData.TaskAssign) {
                  self.model.taskAssign = _.concat(self.model.taskAssign, responseData.TaskAssign);
                }
              }
              self.$store.commit('isLoading', false);
            }, (error) => {
              console.log(error);
              self.$store.commit('isLoading', false);
            });
          }
        },
		getAllChildTableItem(item, tableItemArr){
          let self = this, listChild = [];
          let allChild = _.filter(tableItemArr, ['ParentID', item.TaskID]);
          if (allChild.length) {
            allChild = _.orderBy(allChild, ['TaskID'], ['asc']);
            _.forEach(allChild, function (value, key) {
              listChild.push(value);
              if (_.filter(tableItemArr, ['ParentID', value.TaskID]).length) {
                let recursiveArr = self.getAllChildTableItem(value, tableItemArr);
                recursiveArr = _.orderBy(recursiveArr, ['TaskID', 'asc']);
                _.forEach(recursiveArr, function (recursive, key) {
                  listChild.push(recursive);
                });
              }

            });
          }
          return listChild;
        },
        // changePerPage(Level){
        //   this.filter.Level = String(Level);
        // }
      },
      directives: {
          ClickOutside
      },
      watch: {
        currentPage() {
            this.fetchData();
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
        // 'filter.Level'(){
        //   this.currentPage = 1;
        //   this.fetchData();
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
  .component-task-list .task-parent .field-task-name{
    /*font-weight: 500;*/
  }
  .component-task-list .table.b-table > thead > tr > th,
  .component-task-list .table.b-table .b-table-top-row td{
    z-index: 3;
  }
  .component-task-list .main-body-view-tree .task-children .field-task-name {
      font-style: italic;
  }
  /*.component-task-list .task-children {*/
  /*  display: none;*/
  /*}*/
  .component-task-list .main-header-filter .select2-container {
    width: 100% !important;
    min-width: unset;
  }
  .component-task-list .custom-select {
    background-color: #fff;
  }
  .component-task-list .component-compare-time .form-group {
    margin-bottom: 0;
  }
  .component-task-list table .component-compare-time .dropdown-toggle,
  .component-task-list table .component-compare-number .dropdown-toggle{
    padding-left: 5px;
    padding-right: 5px;
  }
  .component-task-list table .component-compare-time .mx-input-wrapper,
  .component-task-list table .component-compare-time .mx-datepicker{
    width: 105px !important
  }
  .component-task-list table .component-compare-time .mx-datepicker input,
  .component-task-list .component-compare-number input{
    padding-left: 5px;
  }
  .component-task-list .component-compare-number .form-group {
    margin-bottom: 0;
  }
  .component-task-list table td .select2-container {
    width: 100% !important;
    min-width: unset;
  }
  .component-task-list .component-compare-time .mx-input-wrapper {
    width: auto !important;
  }
  .task-filter-type.custom-select {
    padding: 0.275rem 1.75rem 0.275rem 0.75rem;
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
</style>
