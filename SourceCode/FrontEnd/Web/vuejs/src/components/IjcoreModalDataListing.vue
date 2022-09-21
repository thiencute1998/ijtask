<template>
    <div>
        <b-input-group>
            <b-form-input :placeholder="'Chọn ' + value.LinkTableName" :readonly="true" v-model="LinkNo" class="readonly form-control"  @click="onToggleModal()"></b-form-input>
            <input type="hidden" :readonly="true" v-model="LinkID" class="readonly form-control"></input>
            <div class="input-group-prepend"  @click="onToggleModal()">
                <span class="input-group-text">
                  <i class='fa fa-search'></i>
                </span>
            </div>
        </b-input-group>
        <b-modal ref="modal" id="modal">
            <template slot="modal-title">
                {{this.TitleModal}}
            </template>
            <b-input-group class="pt-10">
                <b-form-input v-model="search" :placeholder="'Nhập tên '+TitleModal" class="readonly form-control" @change="fetchData"></b-form-input>
                <div class="input-group-prepend"  @click="fetchData">
                                <span class="input-group-text">
                                  <i class='fa fa-search'></i>
                                </span>
                </div>
            </b-input-group>
            <div class="table-responsive pb-10">
                <table class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
                    <tbody>
<!--                        <tr v-for="item in listtable" @click="updateForParent(item[tableName+'ID'], item[tableName+'Name'], item[tableName+'No'])">-->
                        <tr v-for="item in listtable" @click="updateForParent(item)">
<!--                            <td class="pr-3">{{item[tableName+'Name']}}</td>-->
                            <td class="pr-3"><span :title="getName(item)">{{getName(item)}}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <template v-slot:modal-footer>
                <div class="w-100 left">
                    <b-button
                            variant="primary"
                            size="md"
                            class="float-left"
                            @click="onHideModal()"
                    >
                        Đóng
                    </b-button>
                    <div class="d-flex flex-wrap justify-content-between align-items-center m-0 float-right">
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
                    </div>
                </div>
            </template>

        </b-modal>
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import mixinLists from '@/mixins/lists';
    export default {
        name: 'IjcoreModalDataListing',
        mixins: [mixinLists],
        components: {
        },
        computed: {
            rows() {
                return this.totalRows
            },
        },
        data () {
            return {
                listtable: [
                ],
                tableName: '',
                search:'',
                TitleModal: '',
                LinkNo: '',
                LinkID: '',
            }
        },
        created() {
            if(this.table){
                this.tableName = this.table.charAt(0).toUpperCase() + this.table.slice(1);
                this.TitleModal = this.value.LinkTableName.charAt(0).toUpperCase() + this.value.LinkTableName.slice(1);
            }else{
                this.TitleModal= '';
            }
            this.currentPage = 1;
            this.LinkID = this.value['LinkID'];
            this.LinkNo = this.value['LinkNo'];
        },
        mounted() {
        },
        methods: {
          fetchData(){
                let self = this;
                let urlApi = this.api;

              let TableFieldSearch = {
                'customer' : 'CustomerName',
                'project' : 'ProjectName',
                'vendor' : 'VendorName',
                'item' : 'ItemName',
                'expense' : 'ExpenseName',
                'task' : 'TaskName',
                'company': 'CompanyName',
                'employee': 'EmployeeName'
              };
                let requestData = {
                    method: 'post',
                    url: urlApi,
                    data: {
                        per_page: 10,
                        page: this.currentPage,
                        table: this.tableName,
                        search: this.search,
                        FieldName: TableFieldSearch[this.tableName.toLowerCase()],
                        FieldID: this.tableName.toUpperCase() + 'ID',
                        FieldNo: this.tableName.toUpperCase() + 'No',
                    },

                };
                // if (this.modelSearch.CompanyNo.trim() !== '') {
                //     requestData.data.CompanyNo = this.modelSearch.CompanyNo;
                // }

                this.$store.commit('isLoading', true);
                ApiService.customRequest(requestData).then((response) => {
                    let dataResponse = response.data;

                    if (dataResponse.status == '1') {
                        self.totalRows = dataResponse.data.total;
                        self.perPage = String(dataResponse.data.per_page);
                        self.currentPage = dataResponse.data.current_page;
                        // converse object to array
                        self.listtable = _.toArray(dataResponse.data.data);

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
          getName(item){
            if (this.tableName === 'task' || this.tableName === 'Task') {
              let name = '';
              if (item.Type === 2 && item.ParentName) {
                name = item.TaskName + ' (' + item.ParentName + ')';
              } else {
                name = item.TaskName;
              }
              name = __.stripHtml(name);
              return name;
            } else {
              return item[this.tableName + 'Name'];
            }
          },
          updateForParent: function (item) {
            if (this.value.DatalistTable === 'task' || this.value.DatalistTable === 'Task') {
              this.value.LinkID = item.TaskID;
              this.value.LinkName = item.TaskName;
              this.value.LinkNo = item.TaskNo;
              this.value.Status = item.Status;
              this.value.StatusID = item.StatusID;
              this.value.StatusName = item.StatusName;
              this.value.StatusValue = item.StatusValue;
              this.value.StatusDescription = item.StatusDescription;
              this.value.Type = item.Type;
              this.value.ParentID = item.ParentID;
              this.value.ParentNo = item.ParentNo;
              this.value.ParentName = item.ParentName;
              this.LinkID = item.TaskID;
              this.LinkNo = item.TaskNo;
            } else {
              this.value.LinkID = item[this.tableName + 'ID'];
              this.value.LinkName = item[this.tableName + 'Name'];
              this.value.LinkNo = item[this.tableName + 'No'];
              this.LinkID = item[this.tableName + 'ID'];
              this.LinkNo = item[this.tableName + 'No'];
            }
            this.$refs['modal'].hide();
          },
          onHideModal(){
              this.$refs['modal'].hide();
          },
          onToggleModal(){
              this.listtable = null;
              if(this.table){
                  this.tableName = this.table.charAt(0).toUpperCase() + this.table.slice(1);
                  this.TitleModal = this.value.LinkTableName.charAt(0).toUpperCase() + this.value.LinkTableName.slice(1);
                  this.currentPage = 1;
                  this.fetchData();
              }
              let self = this;
              this.$refs['modal'].show();
          },
          onResetModal(){
          },
        },
        watch: {
            currentPage() {
                this.fetchData();
            },
            table(){
                this.LinkID = '';
                this.LinkNo = '';
            },
            'value.LinkNo'(){
              this.LinkNo = this.value.LinkNo;
            },
            'value.LinkID'(){
              this.LinkID = this.value.LinkID;
            },
            title(){
                this.TitleModal = this.title;
            }
        },
        props: {
            value: {
                type: Object,
                default() {
                    return {
                        ParentID: '',
                    };
                }
            },
            title:{},
            name:{},
            api: {},
            table: {}
        },
    }
</script>
<style>
    .readonly{
        background-color: #fff !important;
    }
    .table th, .table td{
        border-bottom: 1px solid #c8ced3;
    }
    .modal-footer ol,.modal-footer ul,.modal-footer dl{
        margin-bottom: 0px;
    }
</style>
