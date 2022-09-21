<template>
    <div>
        <div class="main-entry">
            <div class="main-header">
                <div class="main-header-padding">
                    <b-row class="mb-2">
                        <b-col class="col-md-12">
                            <div class="main-header-item main-header-name">
                                <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Công việc</span>
                            </div>
                        </b-col>
                        <b-col class="col-md-12">
                            <div class="main-header-item main-header-search"></div>
                        </b-col>
                    </b-row>
                    <b-row class="mb-2">
                        <b-col>
                            <div class="main-header-item main-header-actions">
                                <b-button class="main-header-action mr-2" variant="primary" @click="handleAddNewItem" size="md">
                                    <i class="fa fa-plus"></i> Thêm
                                </b-button>

                                <b-button v-b-toggle.collapse-main-header-filter class="main-header-action mr-2" variant="primary">
                                    <i class="fa fa-filter"></i> Lọc
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
                        <b-col>
                            <div class="main-header-item main-header-icons">
                                <b-button-group id="main-header-views" class="main-header-views">
                                    <b-dropdown id="dropdown-view-type" menu-class="p-0" @toggle="stage.viewType = 'select'" :class="[(stage.viewType === 'list' || stage.viewType === 'tree' || stage.viewType === 'select') ? 'view-active' : '']" :no-caret="true" class="app-dropdown-center" toggle-class="main-header-view">
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
                                    <b-tooltip container="main-header-views" target="tooltip-view-kanban">Kanban</b-tooltip>
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

                    <b-collapse id="collapse-main-header-filter">
                        <div class="main-header-filter pt-2 border-top">
                            <b-row>
                                <b-col md="5">
                                  <ijcore-date-range></ijcore-date-range>
                                </b-col>
                                <b-col md="2">
                                    <b-form-group label-for="searchFieldUsername" class="m-0">
                                        <b-form-input
                                                id="searchFieldUsername" type="text"
                                                v-model="modelSearch.username"
                                                placeholder="Username"
                                                @keyup.enter="handleSubmitSearch"
                                                name="username"
                                                size="md"
                                                autocomplete="name">
                                        </b-form-input>
                                    </b-form-group>
                                </b-col>
                                <b-col md="2">
                                    <b-form-group class="m-0">
                                        <b-form-select
                                                id="searchFieldUsername"
                                                :plain="true"
                                                name="InActive"
                                                v-model="modelSearch.inActive"
                                                @change="handleSubmitSearch"
                                                size="md"
                                                :options="inActiveOption">
                                        </b-form-select>
                                    </b-form-group>
                                </b-col>
                                <b-col>
                                    <div class="main-action d-lg-flex justify-content-end">
                                        <b-button variant="primary" @click="handleSubmitSearch" size="md">
                                            <i class="fa fa-search"></i> Tìm Kiếm
                                        </b-button>
                                    </div>
                                </b-col>
                            </b-row>
                        </div>
                    </b-collapse>
                </div>
            </div>
            <div class="main-body main-body-view-list">
                <b-card class="m-0 border-0" body-class="py-0 px-0">
                    <div class="content-body">
                        <div class="content-table">
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

<!--                                :striped="propsTable.striped"-->

                                <!-- A custom formatted header cell for field 'name' -->
                                <template v-slot:head(selected)="data">
                                    <b-form-checkbox @input="onToggleSelectAllRows($event)" :checked="false"></b-form-checkbox>
                                </template>


                                <template slot="top-row" slot-scope="data">

                                    <td v-if="!stage.showFullTextSearch" :class="[(field.key == 'selected') ? 'pl-3' : '', (field.key == 'UserType') ? 'pr-3' : '']" role="cell" v-for="(field, key) in data.fields">
                                        <span class="d-lg-flex align-items-center" title="Tìm kiếm chung" @click="onToggleFullTextSearch" v-if="field.key == 'selected'"><i class="fa fa-hand-pointer-o" style="font-size: 18px"></i></span>
<!--                                        <b-form-checkbox ></b-form-checkbox>-->
                                        <!-- type input -->
                                        <b-form-input
                                                type="text" :placeholder="field.searchOnTopRow.placeholder"
                                                v-if="field.searchOnTopRow && field.searchOnTopRow.type == 'text' && key !== 0 && key !== (data.fields.length - 1)"
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
                                        <span class="d-lg-flex align-items-center" title="Tìm kiếm chung" @click="onToggleFullTextSearch"><i class="fa fa-hand-pointer-o" style="font-size: 18px"></i></span>
                                    </td>
                                    <td colspan="3" v-if="stage.showFullTextSearch">
                                        <b-form-input v-if="stage.showFullTextSearch" placeholder="Tìm kiếm..."></b-form-input>
                                    </td>

                                </template>
                                <!-- Example scoped slot for select state illustrative purposes -->
                                <template v-slot:cell(selected)="data">
                                    <b-form-checkbox class="checkbox-selected" @change="onToggleRowSelected(data)" :checked="data.rowSelected"></b-form-checkbox>
                                </template>

                                <template v-slot:cell(index)="data">
                                    {{ (data.index + 1) + ((currentPage - 1) * perPage) }}
                                </template>

                                <template v-slot:cell(UserType)="data">
                                    <span v-if="data.item.Inactive !== 1">Hoạt động</span>
                                    <span v-else>Ngừng hoạt động</span>
                                </template>

<!--                                <template v-slot:cell(table-action)="data">-->
<!--                                    <div class="icon-items" id="table-icon-actions">-->
<!--                                        <span title="Sửa" @click="handleEditItem(data)" class="icon-item px-2"><i class="fa fa-edit"></i></span>-->
<!--                                        <span title="Xóa" @click="handleDeleteItem(data)" class="icon-item px-2"><i class="fa fa-trash-o"></i></span>-->
<!--                                    </div>-->
<!--                                </template>-->

                            </b-table>
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
    import IjcoreDateRange from '@/components/IjcoreDateRange';

    const inActiveOption = [
        {text: 'Trạng thái', value: -1},
        {text: 'Hoạt động', value: 1},
        {text: 'Ngừng hoạt động', value: 0}
    ];

    const ListApi = 'sysadmin/api/users';
    const DeleteApi = 'sysadmin/api/users/delete';
    const CreateRouter = '/sysadmin/user/create';
    const EditRouter = '/sysadmin/user/edit';
    const ViewRouter = 'sysadmin-user-view';
    // const NameRouter = 'sysadmin-user-view';

    export default {
        name: 'TableLists',
        data() {
            return {
                perPage: (this.$store.state.optionBehavior.perPage) ? this.$store.state.optionBehavior.perPage : null,
                currentPage: 1,
                itemsArray: [],
                totalRows: null,
                typeShow: 1,
                selected: [],
                modelSearch: {
                    fullName: '',
                    username: '',
                    inActive: -1
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
          IjcoreDateRange
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
                    {key: 'selected', label: '', thStyle: 'width: 5%',tdClass: 'pl-3', thClass: 'pl-3'},
                    {key: 'username', label: 'Tên đăng nhập', field: 'username', thStyle: 'width: 20%',searchOnTopRow: {type: 'text', placeholder: 'Tên đăng nhập'}},
                    {key: 'FullName', label: 'Họ và tên', field: 'fullName', thStyle: 'width: 40%', searchOnTopRow: {type: 'text', placeholder: 'Họ và tên'}},
                    {key: 'UserType', label: 'Trạng thái', field: 'inActive', searchOnTopRow: {type: 'select', option: [{text: 'Trạng thái',value: -1}, {text: 'Hoạt động',value: 1}, {text: 'Ngừng hoạt động', value: 0}], valueDefault: -1}, thStyle: 'width: 15%', tdClass: 'pr-3', thClass: 'pr-3'},
                    // {key: 'table-action', label: '', thStyle: 'width: 8%'}
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
            if (this.stage.message !== '') {
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

            handleDebugger(event){
                event.stopPropagation();
                event.preventDefault();
                alert('aaa');
                return false;
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


                if (this.modelSearch.fullName.trim() !== '') {
                    requestData.data.FullName = this.modelSearch.fullName;
                }
                if (this.modelSearch.username !== '') {
                    requestData.data.username = this.modelSearch.username;
                }

                if (this.modelSearch.inActive !== -1) {
                    requestData.data.Inactive = this.modelSearch.inActive;
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
                    self.paramsReqRouter.idsArray.push(value.UserID);
                });

                // set search
                this.paramsReqRouter.search.FullName = this.modelSearch.fullName;
                this.paramsReqRouter.search.username = this.modelSearch.username;
                this.paramsReqRouter.search.Inactive = this.modelSearch.inActive;

                // set pagination
                this.paramsReqRouter.total = this.totalRows;
                this.paramsReqRouter.perPage = this.perPage;
                this.paramsReqRouter.currentPage = this.currentPage;

            },

            handleSubmitSearch(){
                this.currentPage = 1;
                this.fetchData();
            },
            handleEditItem(data) {
                let router = EditRouter + '/' + data.item.UserID;
                this.$router.push({path: router, params: {req: this.paramsReqRouter}});
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
                                Swal.fire(
                                    'Đã xóa!',
                                    'Tài khoản đã bị xóa.',
                                    'success'
                                );

                                _.forEach(self.idsSelected, function (id, key) {
                                    const index = self.itemsArray.findIndex(post => post.UserID === id); // find the post index
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
                this.$router.push({path: CreateRouter});
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
                    self.idsSelected.push(item.UserID);
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
                    // let router = ViewRouter + '/' + item.UserID;
                    // this.$router.push({path: router});
                    this.$router.push({
                        name: ViewRouter,
                        params: {id: item.UserID, req: this.paramsReqRouter}
                    });
                }
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
            }
        }
    }
</script>

<style lang="css">
    .main-footer-pagination ul {
        margin-bottom: 0;
    }
</style>
