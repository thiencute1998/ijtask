<template>
    <div>
        <div class="main-entry">
            <div class="main-header">
                <div class="main-header-padding">
                  <b-row class="mb-2">
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-name">
                            <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Nhà cung cấp</span>
                        </div>
                    </b-col>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-search"></div>
                    </b-col>
                  </b-row>
                  <b-row class="mb-2">
                    <b-col class="col-md-8 col-sm-24 col-24 mb-2 mb-sm-0 mb-md-0">
                      <div class="main-header-item main-header-actions">
                        <b-button class="main-header-action mr-2" variant="primary" @click="handleAddNewItem" size="md">
                          <i class="fa fa-plus"></i> Thêm
                        </b-button>
                        <b-dropdown variant="primary" id="dropdown-actions" @toggle="onToggleActionMajor" class="main-header-action mr-2" text="Thao tác">
                          <b-dropdown-item @click="handleDeleteItem" :disabled="stage.disableActions">Xóa</b-dropdown-item>
                          <b-dropdown-item>In</b-dropdown-item>
                          <li class="dropdown b-dropdown dropright" >
                            <a class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" data-toggle="dropdown" @click.stop="onToggleDropdownSubMenu($event)" href="#">Nhập</a>
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
                        </b-dropdown>
                      </div>
                    </b-col>
                    <b-col class="col-md-16 col-sm-24 col-24">
                      <div class="main-header-item main-header-icons">
                        <b-button-group id="main-header-views" class="main-header-views">
                          <b-dropdown id="dropdown-view-type" menu-class="p-0" :class="[(stage.viewType === 'list' || stage.viewType === 'tree' || stage.viewType === 'select') ? 'view-active' : '']" :no-caret="true" class="app-dropdown-center" toggle-class="main-header-view">
                            <template v-slot:button-content>
                              <i class="fa fa-tree" v-if="stage.viewType === 'tree'"></i>
                              <i class="fa fa-list" v-else></i>
                            </template>
                            <b-dropdown-item id="tooltip-view-list" @click="stage.viewType = 'list'"><i class="fa fa-list m-0"></i></b-dropdown-item>
                            <b-dropdown-item id="tooltip-view-tree" @click="stage.viewType = 'tree'"><i class="fa fa-tree m-0"></i></b-dropdown-item>
                          </b-dropdown>
                          <b-button id="tooltip-view-kanban" @click="stage.viewType = 'kanban'" :class="[(stage.viewType === 'kanban') ? 'view-active' : '']" class="main-header-view"><i class="fa fa-th"></i></b-button>
                          <b-tooltip container="main-header-views" placement="left" target="tooltip-view-list">Danh sách</b-tooltip>
                          <b-tooltip container="main-header-views" placement="left" target="tooltip-view-tree">Cây</b-tooltip>
                          <b-tooltip container="main-header-views" target="tooltip-view-kanban">Thẻ tin</b-tooltip>
                        </b-button-group>
                        <b-dropdown id="dropdown-per-page" menu-class="p-0" :text="perPage" class="app-dropdown-center ml-2">
                          <b-dropdown-item @click="handleChangePerPage(10)">10</b-dropdown-item>
                          <b-dropdown-item @click="handleChangePerPage(15)">15</b-dropdown-item>
                          <b-dropdown-item @click="handleChangePerPage(20)">20</b-dropdown-item>
                          <b-dropdown-item @click="handleChangePerPage(30)">30</b-dropdown-item>
                          <b-dropdown-item @click="handleChangePerPage(40)">40</b-dropdown-item>
                          <b-dropdown-item @click="handleChangePerPage(50)">50</b-dropdown-item>
                        </b-dropdown>
                        <div class="main-header-collapse ml-2" id="main-header-collapse">
                          <sidebar-toggle class="d-md-down-none btn" display="lg" :defaultOpen=true />
                        </div>
                      </div>
                    </b-col>
                  </b-row>
                </div>
            </div>
            <div class="main-body" :class="(stage.viewType === 'list') ? 'main-body-view-list' : 'main-body-view-kanban'">
                <b-card class="m-0 border-0" body-class="py-0 px-0">
                    <div class="content-body">
                      <div class="content-table content-body-list" v-if="stage.viewType === 'list'">
                            <b-table ref="selectableTable"
                                     :hover="propsTable.hover"
                                     :small="propsTable.small"
                                     :sticky-header="propsTable.stickyHeader"
                                     head-variant="light"
                                     :fields="captions"
                                     responsive
                                     selectable
                                     select-mode="multi"
                                     @row-selected="onRowSelected"
                                     @row-clicked="onRowClicked"
                                     selected-variant="active"
                                     primary-key="index"
                                     :items="itemsArray">

                                <!-- A custom formatted header cell for field 'name' -->
                                <template v-slot:head(selected)="data">
                                    <b-form-checkbox class="text-left" @input="onToggleSelectAllRows($event)" :checked="false"></b-form-checkbox>
                                </template>
                              <template v-slot:cell(VendorName)="data">
                                <span :title="data.item.VendorName">{{data.item.VendorName}}</span>
                              </template>
                                <template slot="top-row" slot-scope="data">
                                    <td v-if="!stage.showFullTextSearch" :class="[(field.key == 'selected') ? 'pl-3' : '', (field.key == 'ItemName') ? 'pr-3' : '']" role="cell" v-for="(field, key) in data.fields">
                                        <span class="d-lg-flex align-items-center" title="Chọn tìm kiếm toàn văn" @click="onToggleFullTextSearch" v-if="field.key == 'selected'"><i class="fa fa-hand-pointer-o" style="font-size: 18px"></i></span>
                                        <!-- type input-->
                                        <b-form-input
                                                type="text" :placeholder="field.searchOnTopRow.placeholder"
                                                v-if=" field.searchOnTopRow && field.searchOnTopRow.type == 'text' && key !== 0"
                                                :name="field.field"
                                                v-model="modelSearch[field.field]"
                                                @keyup.enter="handleSubmitSearch"
                                                :autocomplete="field.field">
                                        </b-form-input>

                                        <!-- type slect -->
                                        <b-form-select
                                                v-if="field.searchOnTopRow && field.searchOnTopRow.type == 'select' && key !== 0"
                                                :plain="true"
                                                :name="field.field"
                                                v-model="modelSearch[field.field]"
                                                :options="field.searchOnTopRow.option"
                                                @change="handleSubmitSearch">
                                        </b-form-select>
                                    </td>

                                    <td v-if="stage.showFullTextSearch" class="pl-3">
                                        <span class="d-lg-flex align-items-center" title="Chọn tìm kiếm theo cột" @click="onToggleFullTextSearch"><i class="fa fa-hand-pointer-o" style="font-size: 18px"></i></span>
                                    </td>
                                    <td colspan="5" v-if="stage.showFullTextSearch">
                                        <b-form-input v-if="stage.showFullTextSearch"></b-form-input>
                                    </td>
                                </template>
                                <!-- Example scoped slot for select state illustrative purposes -->
                                <template v-slot:cell(selected)="data">
                                    <b-form-checkbox class="checkbox-selected" @change="onToggleRowSelected(data)" :checked="data.rowSelected"></b-form-checkbox>
                                </template>
                            </b-table>
                      </div>
                      <div class="content-body-kanban" v-if="stage.viewType === 'kanban'">
                        <div class="kanban-items row">
                          <div class="kanban-item col-lg-6 col-md-8 col-sm-12 col-24" v-for="(item, key) in itemsArray">
                            <div class="kanban-item-inner" @click="onFieldClicked(item)">
                              <div class="kanban-record d-flex justify-content-between">
                                <span class="kanban-title mr-0">{{item.VendorName}}</span>
<!--                                <span class="kanban-no">{{item.VendorNo}}</span>-->
                              </div>
                              <div class="kanban-record">
                                <span class="kanban-text" v-if="item.VendorNo && item.VendorNo !== ''">Mã số: {{item.VendorNo}}</span>
                              </div>
                              <div class="kanban-record">
                                <span class="kanban-text" v-if="item.OfficePhone && item.OfficePhone !== ''">SĐT: {{item.OfficePhone}}</span>
                              </div>
                              <div class="kanban-record">
                                <span class="kanban-text" v-if="item.Fax && item.Fax !== ''">Fax: {{item.Fax}}</span>
                              </div>
                              <div class="kanban-record">
                                <span class="kanban-text" v-if="item.Email && item.Email !== ''">Email: {{item.Email}}</span>
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
    import Swal from 'sweetalert2';
    import 'sweetalert2/src/sweetalert2.scss';

    const inActiveOption = [
        {text: 'Trạng thái', value: -1},
        {text: 'Hoạt động', value: 1},
        {text: 'Ngừng hoạt động', value: 0}
    ];


    const ListApi = 'listing/api/vendor';
    const DeleteApi = 'listing/api/vendor/delete';
    const CreateRouter = 'listing-vendor-create';
    const EditRouter = 'listing-vendor-edit';
    const ViewRouter = 'listing-vendor-detail';

    export default {
        name: 'listing-items',
        data() {
            return {
                perPage: (this.$store.state.optionBehavior.perPage) ? this.$store.state.optionBehavior.perPage : null,
                currentPage: 1,
                itemsArray: [],
                totalRows: null,
                typeShow: 1,
                selected: [],
                modelSearch: {
                    vendorNo: '',
                    vendorName: '',
                    officePhone: '',
                    fax: '',
                    email: ''
                },
                idsSelected: [],
                propsTable: {
                    id: '',
                    primaryKey: '',
                    striped: true,
                    bordered: true,
                    borderless: false,
                    outlined: false,
                    dark: false,
                    hover: true,
                    small: true,
                    fixed: true,
                    responsive: true,
                    stickyHeader: true,
                    captionTop: false,
                    tableVariant: '',
                    tableClass: '',
                    stacked: '',
                    headVariant: '',
                    threadClass: '',
                    threadTrClass: {},
                    footClone: false,
                    footVariant: '',
                    tfootClass: {},
                    tfootTrClass: {},
                    tbodyTrClass: {},
                    tbodyClass: {},
                    tbodyTransitionProps: {},
                    tbodyTransitionHandlers: {}
                    // Todo:: add more props for table
                },

                paramsReqRouter: {
                    idsArray: [],
                    search: {},
                    total: this.totalRows,
                    perPage: this.perPage,
                    currentPage: this.currentPage,
                    lastPage: null,
                    from: null,
                    to: null
                },

                stage: {
                    updatedData: false,
                    disableActions: true,
                    viewType: 'list',
                    showFullTextSearch: false,
                    message: (this.$route.params.message) ? this.$route.params.message : ''
                }
            }
        },
        components:{
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
                    {key: 'VendorNo', label: 'Mã nhà cung cấp', thStyle: 'width: 12%; min-width: 100px', field: 'vendorNo',searchOnTopRow: {type: 'text'}},
                    {key: 'VendorName', label: 'Tên nhà cung cấp', thStyle: 'width: 38%; min-width: 300px', field: 'vendorName',searchOnTopRow: {type: 'text'}},
                    {key: 'OfficePhone', label: 'Số điện thoại', thStyle: 'width: 15%; min-width: 150px', field: 'officePhone',searchOnTopRow: {type: 'text'}},
                    {key: 'Fax', label: 'Fax', field: 'fax', thStyle: 'width: 15%; min-width: 150px', searchOnTopRow: {type: 'text'}},
                    {key: 'Email', label: 'Email', field: 'email', thStyle: 'width: 15%; min-width: 150px',searchOnTopRow: {type: 'text'}, tdClass: 'pr-3', thClass: 'pr-3'},
                ];

                return fieldsTable;
            }
        },
        created() {
            this.init();
        },
        updated() {
            this.stage.updatedData = true;
        },
        mounted() {
          // hiển thị thông báo
          if (this.stage.message && this.stage.message !== '') {
              this.$bvToast.toast(this.stage.message, {
                  title: 'Thông báo',
                  variant: 'success',
                  solid: true
              });
          }
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
            let urlApi = ListApi;
            let requestData = {
                method: 'post',
                url: urlApi,
                data: {
                    per_page: this.perPage,
                    page: this.currentPage,
                },

            };

            if (this.modelSearch.vendorNo !== '') {
                requestData.data.VendorNo = this.modelSearch.vendorNo;
            }
            if (this.modelSearch.vendorName !== '') {
                requestData.data.VendorName = this.modelSearch.vendorName;
            }
            if (this.modelSearch.officePhone !== '') {
                requestData.data.OfficePhone = this.modelSearch.officePhone;
            }
            if (this.modelSearch.fax !== '') {
                requestData.data.Fax = this.modelSearch.fax;
            }
            if (this.modelSearch.email !== '') {
                requestData.data.Email = this.modelSearch.email;
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
                    self.setParamsReqRouter();
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

          setParamsReqRouter() {
            let self = this;
            // set id array
            this.paramsReqRouter.idsArray = [];
            _.forEach(this.itemsArray, function (value, key) {
                self.paramsReqRouter.idsArray.push(value.VendorID);
            });

            // set search
            this.paramsReqRouter.search.VendorNo = this.modelSearch.vendorNo;
            this.paramsReqRouter.search.VendorName = this.modelSearch.vendorName;
            this.paramsReqRouter.search.OfficePhone = this.modelSearch.officePhone;
            this.paramsReqRouter.search.Fax = this.modelSearch.fax;
            this.paramsReqRouter.search.Email = this.modelSearch.email;

            // set pagination
            this.paramsReqRouter.total = this.totalRows;
            this.paramsReqRouter.perPage = this.perPage;
            this.paramsReqRouter.currentPage = this.currentPage;
          },

          handleSubmitSearch(){
            this.currentPage = 1;
            this.fetchData();
          },


          handleDeleteItem() {
            let self = this;
            let title = 'Bạn có muốn xóa bản ghi?';
            if (this.idsSelected.length > 1) {
                title = 'Bạn có muốn xóa các bản ghi này?';
            }
            Swal.fire({
                title: title,
                text: 'Bạn sẽ không thể khôi phục thông tin bản ghi!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy bỏ'
            }).then((result) => {
              if (result.value) {

                let requestData = {
                    method: 'post',
                    url: DeleteApi,
                    data: {
                        array_id: this.idsSelected,
                    },
                };

                ApiService.setHeader();
                ApiService.customRequest(requestData).then((response) => {

                  let dataResponse = response.data;
                  if (dataResponse.status === 1) {

                    self.$bvToast.toast('Bản ghi đã bị xóa.', {
                      title: 'Thông báo',
                      variant: 'success',
                      solid: true
                    });

                    _.forEach(self.idsSelected, function (id, key) {
                      const index = self.itemsArray.findIndex(post => post.VendorID === id); // find the post index
                      if (~index) // if the post exists in array
                          self.itemsArray.splice(index, 1); //delete the post
                    });

                    if (!self.itemsArray.length) {
                      this.currentPage = 1;
                    }

                    self.totalRows = self.totalRows - self.idsSelected.length;

                    // For more information about handling dismissals please visit
                    // https://sweetalert2.github.io/#handling-dismissals
                  } else {

                    Swal.fire(
                      'Có lỗi',
                      '',
                      'error'
                    );
                    console.log(response);

                  }
                }, (error) => {
                  console.log(error);

                });

              }
            });

          },
          handleAddNewItem() {
            this.$router.push({name: CreateRouter});
          },
          handleChangeTypeShow(type) {
            this.typeShow = type;
            this.handleSubmitSearch();
          },
          handleExportExcel() {
            // Todo: handle export excel
            alert('excel');
          },
          handleExportPrint() {
            // Todo: handle export print
            alert('print');
          },
          handleChangeOperator(field, operator) {
            this.model[field].operator = operator;
          },
          handleChangePerPage(perPage){
            this.perPage = String(perPage);
            this.$store.commit('optionBehavior', {'perPage': this.perPage});
            this.currentPage = 1;
            this.fetchData();
          },
          autoCorrectedDatePipe: () => { return createAutoCorrectedDatePipe('dd/mm/yyyy') },
          scrollHandle(evt){},
          onRowSelected(items) {
            let self = this;
            (items.length) ? this.stage.disableActions = false : this.stage.disableActions = true;
            this.idsSelected = [];
            _.forEach(items, function (item, key) {
              self.idsSelected.push(item.VendorID);
            });
          },
          onToggleRowSelected(data){
            if (data.rowSelected) {
              this.$refs.selectableTable.unselectRow(data.index);
            } else {
              this.$refs.selectableTable.selectRow(data.index);
            }
          },
          onToggleSelectAllRows(value){
            (value) ? this.$refs.selectableTable.selectAllRows() : this.$refs.selectableTable.clearSelected();
          },

          onRowClicked(item, index, event ) {
            if (!event.target.classList.contains('checkbox-selected') && !(event.target.firstChild.classList && event.target.firstChild.classList.contains('checkbox-selected'))) {
              this.$router.push({
                name: ViewRouter,
                params: {id: item.VendorID, req: this.paramsReqRouter}
              });
            }
          },
          onFieldClicked(item){
            this.$router.push({
              name: ViewRouter,
              params: {id: item.VendorID, req: this.paramsReqRouter}
            });
          },
          onToggleDropdownSubMenu(event){
            __.onToggleDropdownSubMenu(event);
          },
          onToggleActionMajor() {
            let dropdownSubMenus = document.querySelectorAll('.dropdown-sub-menu');
            for (let j = 0; j < dropdownSubMenus.length; j++) {
              dropdownSubMenus[j].classList.remove('show');
            }
          },
          onToggleFullTextSearch(){
            this.stage.showFullTextSearch = !this.stage.showFullTextSearch;
          }
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
