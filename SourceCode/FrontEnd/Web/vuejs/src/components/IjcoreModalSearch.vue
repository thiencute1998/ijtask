<template>
    <div class="ijcore ijcore-modal ijcore-modal-search">
        <b-modal :id="idModal" :title="titleModal"
                 :content-class="classModal"
                 :ref="refModal"
                 :size="sizeModal" ok-title="Đóng" ok-only>
            <div class="ijcore-search ijcore-modal-search mb-3">
                <b-input-group v-if="searchFieldsApi.length > 1">
                    <b-form-input
                        v-for="(searchField, index) in searchFieldsApi" :key="searchField.id"
                        :name="searchField.name"
                        :class="searchField.class"
                        type="text"
                        class="mr-3"
                        :style="searchField.style"
                        :placeholder="searchField.placeholder">
                    </b-form-input>

                    <!-- Attach Right button -->
                    <b-button variant="primary" @click="onSubmitSearch">
                        <i class="fa fa-search"></i>
                    </b-button>
                </b-input-group>

                <b-form-group v-else>
                    <b-input-group>
                        <b-form-input
                            :class="searchFieldsApi[0].class"
                            :style="searchFieldsApi[0].style"
                            :placeholder="searchFieldsApi[0].placeholder"
                            :name="searchFieldsApi[0].name" type="text">

                        </b-form-input>
                        <!-- Attach Right button -->
                        <b-input-group-append>
                            <b-button variant="primary">
                                <i class="fa fa-search"></i>
                            </b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>

            </div>
            <div class="ijcore-modal-data">
                <b-table :hover="propsTable.hover" :striped="propsTable.striped"
                         :bordered="propsTable.bordered"
                         :small="propsTable.small"
                         :fields="captions"
                         @row-clicked="onSelectedItem"
                         fixed="fixed" responsive="sm" :items="itemsArray">
                </b-table>
            </div>
            <fieldset style="margin-bottom: 1rem"></fieldset>
            <div class="ijcore-modal-pagination">
                <div class="overflow-auto">
                    <b-pagination
                            v-model="currentPage"
                            :total-rows="rows"
                            :per-page="perPage"
                            aria-controls="my-table"
                    ></b-pagination>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<style lang="css"></style>


<script>
    import axios from 'axios/index';
    export default {
        name: 'advanced-forms',
        components: {},
        data () {
            return {
                perPage: this.itemPerPage,
                currentPage: 1,
                itemsArray: [],
                totalRows: 10,
                select: [],
                search: {},
                selectedItem: this.value
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
            urlApi: {
                type: String,
                default: ''
            },
            titleModal: {
                type: String,
                default: ''
            },
            classModal: {
                type: String,
                default: ''
            },
            refModal: {
                type: String,
                default: 'appModal'
            },
            idModal: {
                type: String,
                default: 'appModal'
            },
            sizeModal: {
              type: String,
              default: 'md' // sm|md|lg|xl
            },
            tableApi: {
                type: String,
                default: ''
            },
            orderByApi: {
                type: String,
                default: ''
            },
            whereApi: {
                type: String,
                default: ''
            },
            selectFieldsApi: {
                type: Array,
                default: function () { return [] }
            },
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
            itemPerPage: {
                type: Number,
                default: 6
            }
        },
        computed: {
            rows() {
                return this.totalRows
            },
            captions: function() {
                let fieldsTable = [];
                _.forEach(this.selectFieldsApi, function (value, index) {
                    let objTmp = {};
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
                    // fieldsTable[value.field] = objTmp;
                    fieldsTable.push(objTmp);
                });
                return fieldsTable;
            }
        },
        methods: {
            init(){
                this.fetchData();
                this.setValueSelect();
                this.$refs[this.refModal].show();
            },
            fetchData(){

                let offset = (this.currentPage - 1) * this.perPage;
                let limit = this.perPage;

                // let urlApi = 'http://localhost/pabmis/api/index.php';
                let urlApi = this.urlApi;
                let reqObj = {
                    table: this.tableApi,
                    select: this.select,
                    orderBy: this.orderByApi,
                    where: this.whereApi,
                    limit: limit,
                    offset: offset,
                    search: this.search
                };

                axios.post(urlApi, reqObj)
                    .then((response) => {
                        let dataResponse = response.data;
                        if (dataResponse.flag == '1') {
                            this.totalRows = dataResponse.data.total;
                            // converse object to array
                            this.itemsArray = _.toArray(dataResponse.data.items);
                        }
                    }, (error) => {
                        console.log(error);
                    });
            },
            onSubmitSearch(){
                let objSearch = {};

                _.forEach(this.searchFieldsApi, function (value, key) {
                    let searchValue = document.querySelector('input[name="' + value.name + '"]').value;
                    searchValue = searchValue.trim();
                    if (searchValue !== '') {
                        objSearch[value.field] = searchValue;
                    }
                });

                if (!_.isEmpty(objSearch)) {
                    this.search = objSearch;
                    this.fetchData();
                }

            },
            setValueSelect(){
                var self = this;
                _.forEach(this.selectFieldsApi, function (value, index) {
                    self.select.push(value.field);
                });
            },
            onSelectedItem(item, index, event) {
                this.selectedItem = item;
                this.$emit('input', this.selectedItem);
                this.$bvModal.hide(this.idModal);
            },
            onClearValue(){
                this.selectedItem = {};
                this.$emit('input', this.selectedItem);
            },
        },
        watch: {
            currentPage() {
                this.fetchData();
            }
        }
    }
</script>

