<template>
    <div class="animated fadeIn">
        <b-card>
            <div slot="header">
                <strong>Danh sách người dùng</strong>
            </div>
            <b-form>
                <div class="ij-main-header">
                    <b-row>

                        <b-col :md="(searchField.type === 'operator-time') ? '3' : '2'" v-for="searchField in searchFieldsApi" :key="searchField.id" v-if="searchField.type !== 'date-range'">
                            <b-form-group :label-for="'basicName' + searchField.id" v-if="searchField.type === 'text' || !searchField.type">
                                <b-form-input
                                    :id="'basicName' + searchField.id" type="text" placeholder="Họ và tên"
                                    :name="searchField.field"
                                    :placeholder="searchField.placeholder"
                                    :style="searchField.style"
                                    :class="searchField.class"
                                    v-model="model[searchField.field]"
                                    autocomplete="name">
                                </b-form-input>
                            </b-form-group>

                            <b-form-group v-if="searchField.type === 'select'">
                                <b-form-select
                                        :id="searchField.id"
                                        :plain="true"
                                        :name="searchField.field"
                                        v-model="model[searchField.field]"
                                        :options="searchField.option"
                                        :value="searchField.valueDefault">
                                </b-form-select>
                            </b-form-group>

                            <b-form-group v-if="searchField.type === 'operator-time'">
                                <b-input-group>
                                    <template slot="prepend">
                                        <b-dropdown :text="model[searchField.field].operator" variant="secondary" :no-caret="true" class="app-dropdown-center ij-operator-time">
                                            <b-dropdown-item
                                                class="text-center"
                                                @click="handleChangeOperator(searchField.field, operator)"
                                                v-for="operator in searchField.operator" :key="operator.id"> {{operator}} </b-dropdown-item>
                                        </b-dropdown>
                                    </template>

                                    <v-date-picker
                                            mode='single'
                                            :formats="formats"
                                            v-model="model[searchField.field].value">
                                        <div slot-scope="props">
                                            <masked-input
                                                    type="text"
                                                    name="date"
                                                    :value="props.inputValue"
                                                    class="form-control"
                                                    :mask="[/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/]"
                                                    :guide="true"
                                                    placeholderChar="_"
                                                    :showMask="true"
                                                    :keepCharPositions="true"
                                                    @change.native="props.updateValue($event.target.value)"
                                                    :pipe="autoCorrectedDatePipe()">
                                            </masked-input>
                                        </div>
                                    </v-date-picker>
                                </b-input-group>
                            </b-form-group>
                        </b-col>

                        <b-col md="5" v-for="searchField in searchFieldsApi" :key="searchField.id" v-if="searchField.type === 'date-range'">
                            <ijtask-date-range v-model="model[searchField.field]"></ijtask-date-range>
                        </b-col>

                        <b-col>
                            <div class="main-action d-lg-flex mb-3 justify-content-end">
                                <b-input-group-append
                                    class="ml-2"
                                    v-if="actionHeader.search">
                                    <b-button variant="primary" @click="handleSubmitSearch">
                                        <i class="fa fa-search"></i> Tìm Kiếm
                                    </b-button>
                                </b-input-group-append>

                                <b-input-group-append
                                    v-if="actionHeader.addNew || actionHeader.addNew == ''"
                                    class="ml-2">
                                    <b-button variant="success" @click="handleAddNewItem">
                                        <i class="fa fa-plus"></i> Thêm
                                    </b-button>
                                </b-input-group-append>
                                <b-input-group-append
                                        v-if="actionHeader.excel"
                                        class="ml-2">
                                    <b-button variant="success" @click="handleExportExcel">
                                        <i class="fa fa-file-excel-o"></i> Excel
                                    </b-button>
                                </b-input-group-append>
                                <b-input-group-append
                                        v-if="actionHeader.print"
                                        class="ml-2">
                                    <b-button variant="success" @click="handleExportPrint">
                                        <i class="fa fa-print"></i> In
                                    </b-button>
                                </b-input-group-append>
                            </div>
                        </b-col>
                    </b-row>
                </div>

                <div class="ij-main-body">
                    <div class="ij-table">
                        <b-table :hover="propsTable.hover" :striped="propsTable.striped"
                                 :bordered="propsTable.bordered"
                                 :small="propsTable.small"
                                 :fields="captions"
                                 fixed="fixed" responsive="sm"
                                 primary-key="index"
                                 :items="itemsArray">

                            <template slot="index" slot-scope="data">
                                {{ (data.index + 1) + ((currentPage - 1) * perPage) }}
                            </template>

                            <template slot="table-action" slot-scope="data">
                                <div class="d-lg-flex justify-content-around align-items-center">
                                    <b-button variant="primary" @click="handleEditItem(data)" size="sm">Sửa</b-button>
                                    <b-button variant="danger" @click="handleDeleteItem(data)" size="sm">Xóa</b-button>
                                </div>
                            </template>

                            <template slot="top-row" slot-scope="data">
                                <td role="cell" class="text-center" v-for="(field, key) in data.fields">
<!--                                    type show-->
                                    <b-dropdown
                                            :id="'tableDropdown' + key"
                                            :no-caret="true"
                                            :lazy="true"
                                            variant="secondary"
                                            class="ij-table-type-show app-dropdown-center"
                                            v-if="key == 0 && changeTypeShow">
                                        <template slot="button-content">
                                            <i class="fa fa-bars"></i>
                                        </template>
                                        <b-dropdown-item @click="handleChangeTypeShow(1)"><i v-b-tooltip.hover title="Type 1" class="fa fa-bars m-0"></i></b-dropdown-item>
                                        <b-dropdown-item @click="handleChangeTypeShow(2)"><i v-b-tooltip.hover title="Type 2" class="fa fa-list m-0"></i></b-dropdown-item>
                                        <b-dropdown-item @click="handleChangeTypeShow(3)"><i v-b-tooltip.hover title="Type 3" class="fa fa-th m-0"></i></b-dropdown-item>
                                    </b-dropdown>


<!--                                    type input-->
                                    <b-form-input
                                        type="text" :placeholder="field.searchOnTopRow.placeholder"
                                        v-if=" field.searchOnTopRow && field.searchOnTopRow.type == 'text' && key !== 0 && key !== (data.fields.length - 1)"
                                        :name="field.field"
                                        v-model="model[field.field]"
                                        @keyup.enter="handleSubmitSearch"
                                        autocomplete="name">
                                    </b-form-input>

<!--                                    type slect-->
                                    <b-form-select
                                            v-if="field.searchOnTopRow && field.searchOnTopRow.type == 'select' && key !== 0 && key !== (data.fields.length - 1)"
                                            :plain="true"
                                            :name="field.field"
                                            v-model="model[field.field]"
                                            :options="field.searchOnTopRow.option"
                                            @change="handleSubmitSearch"
                                            :value="field.searchOnTopRow.valueDefault">
                                    </b-form-select>

<!--                                    type checkbox dropdown-->
                                    <b-dropdown
                                        id="dropdownCheckbox"
                                        class="app-dropdown-center"
                                        v-if="field.searchOnTopRow && field.searchOnTopRow.type == 'checkbox-dropdown' && key !== 0 && key !== (data.fields.length - 1)"
                                        :text="field.searchOnTopRow.dropdownText">
                                        <b-form-checkbox-group stacked class="my-2">
                                            <b-dropdown-text v-for="(item, index) in field.searchOnTopRow.checkbox">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox"
                                                        class="custom-control-input"
                                                        :id="'customChk' + index"
                                                        :name="field.field + '[]'"
                                                        v-model="model[field.field]"
                                                        :value="item.value"
                                                        @change="handleSubmitSearch">
                                                    <label class="custom-control-label" :for="'customChk' + index">{{item.text}}</label>
                                                </div>
                                            </b-dropdown-text>
                                        </b-form-checkbox-group>
                                    </b-dropdown>


                                    <div class="d-lg-flex justify-content-center align-items-center">
                                        <b-button variant="secondary"
                                            @click="handleSubmitSearch"
                                            v-if="key == (data.fields.length - 1)">
                                                <i class="fa fa-search"></i>
                                        </b-button>
                                    </div>
                                </td>
<!--                                <div v-for="field in data.fields">-->
<!--                                    -->
<!--                                </div>-->
<!--                                <b-button variant="success" @click="handleDebugger(data)">asffafs</b-button>-->
                            </template>

                        </b-table>
                    </div>
                </div>
                <div class="ij-main-footer d-lg-flex justify-content-between align-items-center">
                    <div class="ij-modal-pagination">
                        <div class="overflow-auto">
                            <b-pagination
                                    v-model="currentPage"
                                    :total-rows="rows"
                                    :per-page="perPage"
                                    aria-controls="my-table"
                            ></b-pagination>
                        </div>
                    </div>
                    <div class="ij-total-item">
                        Tổng số: {{totalRows}}
                    </div>
                </div>
            </b-form>

        </b-card>

    </div>
</template>


<script>
    import ApiService from '@/services/api.service';
    import MaskedInput from 'vue-text-mask';
    import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
    import {setupCalendar, DatePicker} from 'v-calendar';
    import 'v-calendar/lib/v-calendar.min.css';
    import IjtaskDateRange from './IjtaskDateRange';

    setupCalendar({
        firstDayOfWeek: 2 // Monday
    });

    export default {
        name: 'TableLists',
        data() {
            // declaration model for all search
            let model = {};
            _.forEach(this.selectFieldsApi, function (value, index) {
                if (value.searchOnTopRow.type === 'checkbox-dropdown') {
                    if (!_.isEmpty(value.searchOnTopRow.valueDefault)) {
                        model[value.field] = value.searchOnTopRow.valueDefault;
                    }else {
                        model[value.field] = [];
                    }

                }else if (value.searchOnTopRow.type === 'operator-time'){
                    model[value.field] = {};
                    if (value.searchOnTopRow.defaultOperator) {
                        model[value.field].operator = value.searchOnTopRow.defaultOperator;
                    } else {
                        model[value.field].operator = '=';
                    }
                    model[value.field].value = new Date();
                } else if (value.searchOnTopRow.type === 'date-range'){
                    model[value.field] = {};

                } else {
                    if (value.searchOnTopRow.valueDefault) {
                        model[value.field] = value.searchOnTopRow.valueDefault;
                    } else {
                        model[value.field] = '';
                    }
                }
            });
            _.forEach(this.searchFieldsApi, function (value, index) {
                if (value.type == 'checkbox-dropdown') {
                    if (!_.isEmpty(value.valueDefault)) {
                        model[value.field] = value.valueDefault;
                    }else {
                        model[value.field] = [];
                    }
                }else if (value.type == 'operator-time') {
                    model[value.field] = {};
                    if (value.defaultOperator) {
                        model[value.field].operator = value.defaultOperator;
                    } else {
                        model[value.field].operator = '=';
                    }
                    model[value.field].value = new Date();
                }else if (value.type === 'date-range'){
                    model[value.field] = {};

                } else {
                    if (value.valueDefault) {
                        model[value.field] = value.valueDefault;
                    } else {
                        model[value.field] = '';
                    }
                }
            });

            return {
                perPage: this.itemPerPage,
                currentPage: 1,
                itemsArray: [],
                totalRows: 10,
                select: [],
                selectedItem: this.value,
                typeShow: 1,
                model: model,

                // for field operator time
                formats: {
                    title: 'MMMM YYYY',
                    weekdays: 'W',
                    navMonths: 'MMM',
                    input: ['L', 'YYYY-MM-DD', 'YYYY/MM/DD'], // Only for `v-date-picker`
                    dayPopover: 'L', // Only for `v-date-picker`
                }
            }
        },
        props:{
            value: {
                type: Object,
                default () {
                    return {
                        id: '',
                        name: ''
                    }
                }
            },
            tableApi: {
                type: String,
                default: ''
            },
            urlApi: {
                type: String,
                default: ''
            },

            /**
             * @example: [
                 {field: 'capitalid', label: 'Mã', searchOnTopRow: {type: 'text', placeholder: 'Nhập mã'}, key: 'capitalid', thStyle: 'width: 10%', sortable: true},
                 {field: 'capitalname', label: 'Tên nguồn vốn', searchOnTopRow: {type: 'select', option: [{text: 'Option 3',value: 'c'}, {text: 'Option 4',value: 'd'}], valueDefault: 'c'}, key: 'capitalname', sortable: true},
                 {field: 'capitalcheckbox', label: 'Tình trạng', searchOnTopRow: {type: 'checkbox-dropdown', dropdownText: 'Lọc',checkbox: [{text: 'Chưa thực hiện',value: '1'}, {text: 'Đang thực hiện',value: '2'}, {text: 'Đã hoàn thành',value: '3'}, {text: 'Đang đợi ý kiến',value: '4'}, {text: 'Từ chối',value: '5'}], valueDefault: ['1','2','3']}, key: 'capitalcheckbox', thStyle: 'width: 10%'}
             ]
             */
            selectFieldsApi: {
                type: Array,
                default: function () { return [] }

            },
            /**
             * @example: [
                {field: 'capitalid1', placeholder: 'Nhập mã', class: ''},
                {field: 'capitalname2', type: 'text', placeholder: 'Nhập tên', class: ''},
                {field: 'capitalselect', id: 'simpleSelect', type: 'select', option: [{text: 'Option 1',value: 'b'}, {text: 'Option 2',value: 'a'}], valueDefault: 'b' , class: ''},
             ]
             */
            searchFieldsApi: {
                type: Array,
                default() {
                    return [];
                }
            },
            propsTable: {
                type: Object,
                default() {
                    return {
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
                        stickyHeader: false,
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
                    };
                }
            },
            /**
             * @example: {editRouterPath: '', editRouterName: '', deleteRouterPath: '', deleteRouterName: '', addNewRouterPath: '', addNewRouterName: '', passFields: []}
             */
            actionFields: {
              type: Object,
                default() {
                    return {
                        // editRouterPath: '',
                        // editRouterName: '',
                        // deleteRouterPath: '',
                        // deleteRouterName: '',
                        // addNewRouterPath: '',
                        // addNewRouterName: '',
                        // passFields: []
                    };
                }
            },
            actionHeader: {
                type: Object,
                default() {
                    return {};
                }
            },
            itemPerPage: {
                type: Number,
                default: 6
            },
            changeTypeShow: {
                type: Boolean,
                default: false
            }
        },
        components:{
            MaskedInput,
            'v-date-picker': DatePicker,
            IjtaskDateRange
        },

        computed: {

            rows() {
                return this.totalRows
            },
            captions: function() {
                let fieldsTable = [{key: 'index', label: 'Stt', thStyle: 'width: 5%'}];
                _.forEach(this.selectFieldsApi, function (value, index) {
                    let objTmp = {};
                    objTmp.field = value.field;
                    objTmp.label = value.label;
                    objTmp.key = value.key;
                    objTmp.headerTitle = value.headerTitle;
                    objTmp.headerAbbr = value.headerAbbr;
                    objTmp.class = value.class;
                    objTmp.formatter = value.formatter;
                    objTmp.sortable = value.sortable;
                    objTmp.sortDirection = value.sortDirection;
                    objTmp.sortByFormatted = value.sortByFormatted;
                    objTmp.filterByFormatted = value.filterByFormatted;
                    objTmp.tdClass = value.tdClass;
                    objTmp.thClass = value.thClass;
                    objTmp.thStyle = value.thStyle;
                    objTmp.variant = value.variant;
                    objTmp.tdAttr = value.tdAttr;
                    objTmp.isRowHeader = value.isRowHeader;
                    objTmp.stickyColumn = value.stickyColumn;
                    objTmp.searchOnTopRow = value.searchOnTopRow;
                    // fieldsTable[value.field] = objTmp;
                    fieldsTable.push(objTmp);
                });
                if (!_.isEmpty(this.actionFields, true)) {
                    fieldsTable.push({key: 'table-action', label: '', thStyle: 'width: 10%'});
                }
                // console.log(fieldsTable);
                return fieldsTable;
            }
        },
        created() {
            this.init();
        },

        methods: {
            init(){
                this.fetchData();
                this.setValueSelect();
            },

            handleDebugger(data){
                console.log(data);
            },

            fetchData(){
                let self = this;
                let offset = (this.currentPage - 1) * this.perPage;
                let limit = this.perPage;

                // let urlApi = 'http://localhost/pabmis/api/index.php';
                let urlApi = this.urlApi;
                let requestData = {
                    method: 'post',
                    url: urlApi,
                    data: {
                        table: this.tableApi,
                        select: this.select,
                        limit: limit,
                        offset: offset,
                        search: this.model,
                        typeShow: this.typeShow
                    },
                };

                ApiService.customRequest(requestData).then((response) => {
                    let dataResponse = response.data;
                    if (dataResponse.flag == '1') {
                        self.totalRows = dataResponse.data.total;
                        // converse object to array
                        self.itemsArray = _.toArray(dataResponse.data.items);
                    }
                }, (error) => {
                    console.log(error);

                });
            },
            handleSubmitSearch(){

                console.log(this.model);

                // let objSearch = {};
                // _.forEach(this.searchFieldsApi, function (value, key) {
                //
                //     if (value.type == 'text' || !value.type) {
                //         let searchValue = document.querySelector('input[name="' + value.field + '"]').value;
                //         searchValue = searchValue.trim();
                //         if (searchValue !== '') {
                //             objSearch[value.field] = searchValue;
                //         }
                //     }
                //
                //     if (value.type == 'select') {
                //         let searchValue = document.querySelector('select[name="' + value.field + '"]').value;
                //         searchValue = searchValue.trim();
                //         if (searchValue !== '') {
                //             objSearch[value.field] = searchValue;
                //         }
                //     }
                // });
                //
                // _.forEach(this.captions, function (value, key) {
                //     if (value.searchOnTopRow) {
                //         if (value.searchOnTopRow.type == 'text') {
                //             let searchValue = document.querySelector('input[name="' + value.field + '"]').value;
                //             searchValue = searchValue.trim();
                //             if (searchValue !== '') {
                //                 objSearch[value.field] = searchValue;
                //             }
                //         }
                //
                //         if (value.searchOnTopRow.type == 'select') {
                //             let searchValue = document.querySelector('select[name="' + value.field + '"]').value;
                //             searchValue = searchValue.trim();
                //             if (searchValue !== '') {
                //                 objSearch[value.field] = searchValue;
                //             }
                //         }
                //
                //         if (value.searchOnTopRow.type == 'checkbox-dropdown') {
                //             let searchValue = document.querySelectorAll("input[name^='" + value.field + "[']:checked");
                //             let valueArr = [];
                //             _.forEach(searchValue, function (selectedE, index) {
                //                 valueArr.push(selectedE.value);
                //             });
                //             if (!_.isEmpty(valueArr)) {
                //                 objSearch[value.field] = valueArr;
                //             }
                //             // console.log(searchValue);
                //         }
                //     }
                // });

                // console.log(objSearch);

                // Todo: uncomment
                // if (!_.isEmpty(objSearch)) {
                //     this.search = objSearch;
                //     this.fetchData();
                // }

            },
            setValueSelect(){
                var self = this;
                _.forEach(this.selectFieldsApi, function (value, index) {
                    self.select.push(value.field);
                });
            },

            handleEditItem(data) {
                let passData = {};
                let editRouter = this.actionFields.editRouterPath;
                if (!_.isEmpty(this.actionFields.passFields)) {
                    _.each(this.actionFields.passFields, function (field, key) {
                        if (data.item[field]) {
                            passData[field] = data.item[field];
                        }
                    });
                }
                this.$router.push({path: editRouter, params: passData});
            },

            handleDeleteItem(data) {
                let passData = {};
                let deleteRouter = this.actionFields.deleteRouterPath;
                if (!_.isEmpty(this.actionFields.passFields)) {
                    _.each(this.actionFields.passFields, function (field, key) {
                        if (data.item[field]) {
                            passData[field] = data.item[field];
                        }
                    });
                }
                this.$router.push({path: deleteRouter, params: passData});
            },
            handleAddNewItem() {
                let addNewRouterPath = this.actionFields.addNewRouterPath;
                this.$router.push({path: addNewRouterPath});
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

            autoCorrectedDatePipe: () => { return createAutoCorrectedDatePipe('dd/mm/yyyy') },
        },
        watch: {
            currentPage() {
                this.fetchData();
            }
        }
    }
</script>

<style lang="css">
    .ij-main-footer {
        margin-bottom: 1rem;
    }
    .ij-modal-pagination ul {
        margin-bottom: 0;
    }
    .b-dropdown-text {
        padding: 0.25rem 0.75rem;
    }

    /*.app-dropdown-center ul{*/
    /*    min-width: auto;*/
    /*}*/

    .ij-table-type-show ul, .ij-operator-time ul{
        min-width: auto;
    }

    .app-dropdown-center ul {
        right: auto;
        left: 50% !important;
        top: 100% !important;
        -webkit-transform: translate(-50%, 0);
        -o-transform: translate(-50%, 0);
        transform: translate(-50%, 0) !important;
    }
    .app-dropdown-center .dropdown-item {
        padding: 0.25rem 0.75rem;
        border-radius: 0;
    }
    .app-dropdown-center .dropdown-item i {
        width: 100%;
        height: 100%;
    }

    .app-dropdown-center .dropdown-item:last-child {
        border-bottom: 1px solid #c8ced3;
    }
    .app-dropdown-center li:last-child .dropdown-item {
        border-bottom: none;
    }

</style>
