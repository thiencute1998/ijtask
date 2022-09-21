<template>
    <div>
        <div class="main-entry">
            <div class="main-header">
                <div class="main-header-padding">
                  <b-row class="mb-2">
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-name">
                            <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Khách hàng</span>
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
                            <a class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" data-toggle="dropdown" @click.stop="$_lists_onToggleDropdownSubMenu($event)" href="#">Xuất</a>
                            <ul role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-menu m-0">
                              <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">
                                <json-excel
                                  class=""
                                  :fetch="handleExportExcel"
                                  :fields="jsonExcel.jsonFields"
                                  worksheet="My Worksheet"
                                  name="customer.xls">
                                  Excel
                                </json-excel>
                              </a></li>
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
                          <customer-modal-search-ccatelist
                            v-model="filter.CustomerCateList"
                            tableApi="customer_cate_list"
                            refModal="myModalSearchCcatelist"
                            id-modal="myModalSearchCcatelist"
                            placeholder="Loại khách hàng"
                            @onSubmitSearch="fetchData"
                            @onClear="fetchData"
                            title-modal="Loại khách hàng" size-modal="lg"></customer-modal-search-ccatelist>
                        </div>
<!--                        <div class="col-10 mb-2">-->
<!--                          <ijcore-date-range v-model="filter.DateRange"></ijcore-date-range>-->
<!--                        </div>-->
                        <div class="col-6 mb-2">
                          <customer-modal-search-link
                            v-model="filter.CustomerLink"
                            ref="myModalSearchInputLink"
                            placeholder="Danh mục liên kết"
                            @onSubmitSearch="handleSubmitSearch"
                            @onClear="fetchData"
                            title-modal="Danh mục liên kết" size-modal="lg"
                            id-modal="myModalSearchInputLink">
                          </customer-modal-search-link>
                        </div>
                        <div class="col-6 mb-2">
                          <ijcore-modal-search-input
                            v-model="filter.Doc"
                            :select-fields-api="[
                              {field: 'DocID',fieldForSelected: 'id', showInTable: false, key: 'DocID'},
                              {field: 'DocName', fieldForSelected: 'name', showInTable: true, key: 'DocName', sortable: true, thClass: 'd-none'}
                            ]"
                            :search-fields-api="[
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
                        <div class="col-6 mb-2"></div>
                        <div class="col-6 mb-2">
                          <ijcore-modal-search-input
                            v-model="filter.Province"
                            :select-fields-api="[
                              {field: 'ProvinceID',fieldForSelected: 'id', showInTable: false, key: 'ProvinceID'},
                              {field: 'ProvinceName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'ProvinceName', sortable: true, thClass: 'd-none'}
                            ]"
                            :search-fields-api="[{field: 'ProvinceName', placeholder: 'Nhập tên', name: 'ProvinceName', class: '', style: ''}]"
                            table="province"
                            ref="myModalSearchInputProvince"
                            id-modal="myModalSearchInputProvince"
                            :item-per-page="8"
                            placeholder="Tỉnh"
                            :url-api="$store.state.appRootApi + '/customer/api/common/get-province'"
                            name-input="input-province"
                            title-modal="Tỉnh" size-modal="lg">
                          </ijcore-modal-search-input>
                        </div>

                        <div class="col-6 mb-2">
                          <ijcore-modal-search-input
                            v-model="filter.District"
                            :select-fields-api="[
                              {field: 'DistrictID',fieldForSelected: 'id', showInTable: false, key: 'DistrictID'},
                              {field: 'DistrictName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'DistrictName', sortable: true, thClass: 'd-none'}
                            ]"
                            :search-fields-api="[{field: 'DistrictName', placeholder: 'Nhập tên', name: 'DistrictName', class: '', style: ''}]"
                            table="district"
                            ref="myModalSearchInputDistrict"
                            id-modal="myModalSearchInputDistrict"
                            :item-per-page="8"
                            placeholder="Huyện"
                            :request-data="{ProvinceID: (filter.Province) ? filter.Province.ProvinceID : null}"
                            :url-api="$store.state.appRootApi + '/customer/api/common/get-district'"
                            name-input="input-district"
                            title-modal="Huyện" size-modal="lg">
                          </ijcore-modal-search-input>
                        </div>

                        <div class="col-6 mb-2">
                          <ijcore-modal-search-input
                            v-model="filter.Commune"
                            :select-fields-api="[
                              {field: 'CommuneID',fieldForSelected: 'id', showInTable: false, key: 'CommuneID'},
                              {field: 'CommuneName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'CommuneName', sortable: true, thClass: 'd-none'}
                            ]"
                            :search-fields-api="[{field: 'CommuneName', placeholder: 'Nhập tên', name: 'CommuneName', class: '', style: ''}]"
                            table="commune"
                            ref="myModalSearchInputCommune"
                            id-modal="myModalSearchInputCommune"
                            :item-per-page="8"
                            placeholder="Xã"
                            :request-data="{
                              ProvinceID: (filter.Province) ? filter.Province.ProvinceID : null,
                              DistrictID: (filter.District) ? filter.District.DistrictID : null
                            }"
                            :url-api="$store.state.appRootApi + '/customer/api/common/get-commune'"
                            name-input="input-commune"
                            title-modal="Xã" size-modal="lg">
                          </ijcore-modal-search-input>
                        </div>

                        <b-col>
                          <div class="main-action d-lg-flex justify-content-end">
                            <b-button variant="primary" @click="handleSubmitSearch" size="md">
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
                              <template v-slot:cell(CustomerName)="data">
                                <span :title="data.item.CustomerName">{{data.item.CustomerName}}</span>
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
                                <span class="kanban-title">{{item.CustomerName}}</span>
                              </div>
                              <div class="kanban-record">
                                <span class="kanban-no">{{item.CustomerNo}}</span>
                              </div>
                              <div class="kanban-record">
                                <span class="kanban-text" v-if="item.OfficePhone && item.OfficePhone !== ''">{{item.OfficePhone}}</span>
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
    import 'v-calendar/lib/v-calendar.min.css';
    import mixinLists from '@/mixins/lists';
    import CustomerModalSearchLink from "./partials/CustomerModalSearchLink";
    import IjcoreDateRange from "../../../components/IjcoreDateRange";
    import CustomerModalSearchCcatelist from "./partials/CustomerModalSearchCcatelist";
    import IjcoreModalSearchInput from "../../../components/IjcoreModalSearchInput";
    import JsonExcel from 'vue-json-excel';

    export default {
      name: 'customer-items',
      mixins: [mixinLists],
        data() {
          return {
            filter: {
              DateRange: null,
              Province: null,
              District: null,
              Commune: null,
              Doc: null,
              CustomerLink: [],
              CustomerCateList: []
            },

            jsonExcel: {
              jsonFields: {
                'STT': 'No',
                'CustomerName': "CustomerName",
                'Address': 'Address',
                'ContactName1': 'ContactName1',
                'PositionName1': 'PositionName1',
                'OfficePhone1': 'OfficePhone1',
                'HandPhone1': 'HandPhone1',
                'Mail1': 'Email1',
                'ContactName2': 'ContactName2',
                'PositionName2': 'PositionName2',
                'OfficePhone2': 'OfficePhone2',
                'HandPhone2': 'HandPhone2',
                'Mail2': 'Email2',
                'ContactName3': 'ContactName3',
                'PositionName3': 'PositionName3',
                'OfficePhone3': 'OfficePhone3',
                'HandPhone3': 'HandPhone3',
                'Mail3': 'Email3',
              },
              jsonData: [
                // {
                //   name: "Tony Peña",
                //   city: "New York",
                //   country: "United States",
                //   birthdate: "1978-03-15",
                //   phone: {
                //     mobile: "1-541-754-3010",
                //     landline: "(541) 754-3010",
                //   },
                // },
              ],
            },
          }
        },
        components:{
          CustomerModalSearchLink,
          IjcoreDateRange,
          CustomerModalSearchCcatelist,
          IjcoreModalSearchInput,
          JsonExcel
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
                  {key: 'CustomerNo', label: 'Mã khách hàng', thStyle: 'width: 12%; min-width: 100px', field: 'CustomerNo',searchOnTopRow: {type: 'text'}},
                  {key: 'CustomerName', label: 'Tên khách hàng', thStyle: 'width: 38%; min-width: 300px', field: 'CustomerName',searchOnTopRow: {type: 'text'}},
                  {key: 'OfficePhone', label: 'Số điện thoại', thStyle: 'width: 15%; min-width: 150px', field: 'OfficePhone',searchOnTopRow: {type: 'text'}},
                  {key: 'Fax', label: 'Fax', field: 'Fax', thStyle: 'width: 15%; min-width: 150px', searchOnTopRow: {type: 'text'}},
                  {key: 'Email', label: 'Email', field: 'Email', thStyle: 'width: 15%; min-width: 150px',searchOnTopRow: {type: 'text'}, tdClass: 'pr-3', thClass: 'pr-2'},
              ];

              return fieldsTable;
          }
        },
        created() {
          this.settings.FieldID = 'CustomerID';
          this.settings.Table = 'customer';
          this.settings.FieldInactive = 'Inactive';

          this.settings.ListApi = 'customer/api/customer';
          this.settings.DeleteApi = 'customer/api/customer/delete';
          this.settings.CreateRouter = 'customer-customer-create';
          this.settings.EditRouter = 'customer-customer-edit';
          this.settings.ViewRouter = 'customer-customer-view';
          this.modelSearch = {
            CustomerNo: '',
            CustomerName: '',
            OfficePhone: '',
            Fax: '',
            Email: ''
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

            if (this.modelSearch.CustomerNo !== '') {
                requestData.data.CustomerNo = this.modelSearch.CustomerNo;
            }
            if (this.modelSearch.CustomerName !== '') {
                requestData.data.CustomerName = this.modelSearch.CustomerName;
            }
            if (this.modelSearch.OfficePhone !== '') {
                requestData.data.OfficePhone = this.modelSearch.OfficePhone;
            }
            if (this.modelSearch.Fax !== '') {
                requestData.data.Fax = this.modelSearch.Fax;
            }
            if (this.modelSearch.Email !== '') {
                requestData.data.Email = this.modelSearch.Email;
            }
            if (this.stage.filterInactive !== 2) {
              requestData.data.Inactive = this.stage.filterInactive;
            }
            if (this.filter.CustomerLink.length) {
              requestData.data.filter.CustomerLink = this.filter.CustomerLink;
            }
            if (this.filter.CustomerCateList.length) {
              requestData.data.filter.CustomerCateList = this.filter.CustomerCateList;
            }

            if (this.filter.Doc) {
              requestData.data.filter.DocID = this.filter.Doc.DocID;
            }

            if (this.filter.Province) {
              requestData.data.filter.ProvinceID = this.filter.Province.ProvinceID;
            }
            if (this.filter.District) {
              requestData.data.filter.DistrictID = this.filter.District.DistrictID;
            }
            if (this.filter.Commune) {
              requestData.data.filter.CommuneID = this.filter.Commune.CommuneID;
            }

            // if (this.filter.DateRange && this.filter.DateRange.fromDate && this.filter.DateRange.fromDate !== '' && this.filter.DateRange.fromDate !== '__/__/____') {
            //   requestData.data.filter.fromDate = this.filter.DateRange.fromDate;
            // }
            // if (this.filter.DateRange && this.filter.DateRange.toDate && this.filter.DateRange.toDate !== '' && this.filter.DateRange.toDate !== '__/__/____') {
            //   requestData.data.filter.toDate = this.filter.DateRange.toDate;
            // }

            if (this.fullTextSearch !== '') {
                requestData.data.fullTextSearch = this.fullTextSearch;
            }

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
            });

            // scroll to top perfect scroll
            const container = document.querySelector('.b-table-sticky-header');
            if (container) container.scrollTop = 0;

          },

          handleSubmitSearch(){
            this.currentPage = 1;
            this.stage.viewType = 'list';
            this.fetchData();
          },

          async handleExportExcel() {
            // Todo: handle export excel
            let self = this;
            let requestData = {
              method: 'post',
              url: 'customer/api/customer/export-excel',
              data: {
                exportExcel: true,
                filter: {}
              }
            };

            if (this.modelSearch.CustomerNo !== '') {
              requestData.data.CustomerNo = this.modelSearch.CustomerNo;
            }
            if (this.modelSearch.CustomerName !== '') {
              requestData.data.CustomerName = this.modelSearch.CustomerName;
            }
            if (this.modelSearch.OfficePhone !== '') {
              requestData.data.OfficePhone = this.modelSearch.OfficePhone;
            }
            if (this.modelSearch.Fax !== '') {
              requestData.data.Fax = this.modelSearch.Fax;
            }
            if (this.modelSearch.Email !== '') {
              requestData.data.Email = this.modelSearch.Email;
            }
            if (this.stage.filterInactive !== 2) {
              requestData.data.Inactive = this.stage.filterInactive;
            }
            if (this.filter.CustomerLink.length) {
              requestData.data.filter.CustomerLink = this.filter.CustomerLink;
            }
            if (this.filter.CustomerCateList.length) {
              requestData.data.filter.CustomerCateList = this.filter.CustomerCateList;
            }
            if (this.filter.Doc) {
              requestData.data.filter.DocID = this.filter.Doc.DocID;
            }
            if (this.filter.Province) {
              requestData.data.filter.ProvinceID = this.filter.Province.ProvinceID;
            }
            if (this.filter.District) {
              requestData.data.filter.DistrictID = this.filter.District.DistrictID;
            }
            if (this.filter.Commune) {
              requestData.data.filter.CommuneID = this.filter.Commune.CommuneID;
            }

            // if (this.filter.DateRange && this.filter.DateRange.fromDate && this.filter.DateRange.fromDate !== '' && this.filter.DateRange.fromDate !== '__/__/____') {
            //   requestData.data.filter.fromDate = this.filter.DateRange.fromDate;
            // }
            // if (this.filter.DateRange && this.filter.DateRange.toDate && this.filter.DateRange.toDate !== '' && this.filter.DateRange.toDate !== '__/__/____') {
            //   requestData.data.filter.toDate = this.filter.DateRange.toDate;
            // }

            if (this.fullTextSearch !== '') {
              requestData.data.fullTextSearch = this.fullTextSearch;
            }

            this.$store.commit('isLoading', true);
            const response = await ApiService.customRequest(requestData);
            let responseData = response.data;
            this.$store.commit('isLoading', false);
            if (responseData.status === 1) {
              let jsonData = [];

              let no = 1;
              _.forEach(responseData.data.data, function (customer, key) {
                let tmpData = {};
                tmpData.No = no;
                tmpData.CustomerName = customer.CustomerName;
                tmpData.Address = customer.Address;

                let contacts = _.filter(responseData.data.Contact, ['CustomerID', customer.CustomerID]);
                let contactNo = 1;
                _.forEach(contacts, function (contact, key) {
                  if (contactNo > 3) return false;
                  tmpData['ContactName' + contactNo] = contact.ContactName;
                  tmpData['PositionName' + contactNo] = contact.PositionName;
                  tmpData['OfficePhone' + contactNo] = contact.OfficePhone;
                  tmpData['HandPhone' + contactNo] = contact.HandPhone;
                  tmpData['Email' + contactNo] = contact.Email;
                  contactNo += 1;
                });
                no += 1;

                jsonData.push(tmpData);
              });

              return jsonData;
            }


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
        }
    }
</script>

<style lang="css">
    .main-footer-pagination ul {
        margin-bottom: 0;
    }
</style>
