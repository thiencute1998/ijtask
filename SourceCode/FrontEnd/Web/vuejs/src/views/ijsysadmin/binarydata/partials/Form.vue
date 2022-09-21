<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Kiểu dữ liệu nhị phân<span v-if="model.BinaryDataName">:</span> {{model.BinaryDataName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Kiểu dữ liệu nhị phân<span v-if="model.BinaryDataName">:</span> {{model.BinaryDataName}}</span>
                        </div>
                    </b-col>
                    <b-col class="col-md-6"></b-col>
                </b-row>
                <b-row>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-actions">
                            <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitForm"><i class="fa fa-check-square-o"></i> Lưu</b-button>
                            <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onBackToList"><i class="fa fa-ban"></i> Hủy</b-button>
                        </div>
                    </b-col>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-icons">
                          <div class="main-header-collapse">
                            <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen=true />
                          </div>
                        </div>
                    </b-col>
                </b-row>
            </div>

        </div>
        <div class="main-body main-body-view-action">
            <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">
                <div class="container-fluid">
                    <b-card>

                        <div class="form-group row align-items-center">
                          <div class="col-lg-2 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên</div>
                          <div class="col-lg-18 col-md-14 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
                            <input v-model="model.BinaryDataName" type="text" id="BinaryDataName" class="form-control" placeholder="Tên" name="BinaryDataName" />
                          </div>
                          <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span>Mã số</span>
                            <input type="text" v-model="model.BinaryDataNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-2 m-0" >Khóa</label>
                          <div class="col-md-6" style="padding-right: 0px;">
                            <input type="text" v-model="model.BinaryDataKey" class="form-control" placeholder="Khóa"/>
                          </div>
                          <label class="col-md-2 m-0">kết quả 1</label>
                          <div class="col-md-6">
                            <input type="text" v-model="model.BinaryData1" class="form-control" placeholder="kết quả 1"/>
                          </div>
                          <label class="col-md-2 m-0">kết quả 0</label>
                          <div class="col-md-6">
                            <input type="text" v-model="model.BinaryData0" class="form-control" placeholder="kết quả 0"/>
                          </div>
                        </div>

                    </b-card>
                </div>
            </vue-perfect-scrollbar>
        </div>
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import Swal from 'sweetalert2';
    import 'sweetalert2/src/sweetalert2.scss';
    import vSelect from 'vue-select';

    const ListRouter = 'sysadmin-binarydata';
    const EditRouter = 'sysadmin-binarydata-edit';
    const CreateRouter = 'sysadmin-binarydata-create';
    const ViewRouter = 'sysadmin-binarydata-view';
    const ViewApi = 'sysadmin/api/binarydata/view';
    const EditApi = 'sysadmin/api/binarydata/edit';
    const CreateApi = 'sysadmin/api/binarydata/create';
    const StoreApi = 'sysadmin/api/binarydata/store';
    const UpdateApi = 'sysadmin/api/binarydata/update';
    const ListApi = 'sysadmin/api/binarydata';


    export default {
        name: 'binarydata-view',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    BinaryDataNo: '',
                    BinaryDataName: '',
                    BinaryDataKey: '',
                    BinaryData1: '',
                    BinaryData0: '',
                    inactive: ''
                },
                stage: {
                    updatedData: false
                }
            }
        },

        props: {
            idParamsProps: {
                type: Number,
                default: 0
            },
            reqParamsProps: {
                type: Object,
                default: function () {
                    return {}
                }
            },
            itemCopy: {
                type: Object,
                default: function () {
                    return {}
                }
            }
        },

        components: {
            vSelect,
        },
        beforeCreate() {},
        mounted() {
            this.fetchData();
        },
        updated() {
            this.stage.updatedData = true;
        },
        computed: {
            itemNo(){
                let index = 0;
                index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
                return index;
            }
        },
        methods: {
            fetchData() {
                let self = this;
                let urlApi = CreateApi;
                let requestData = {
                    method: 'get',
                    data: {}
                };
                // Api edit user
                if(this.idParams){
                    urlApi = EditApi + '/' + this.idParams;
                    requestData.data.id = this.idParams;
                }
                requestData.url = urlApi;
                this.$store.commit('isLoading', true);

                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => {

                    let responsesData = responses.data;
                    // copy item
                    if (!self.idParams && !_.isEmpty(self.itemCopy)) {
                        responsesData.data.data = self.itemCopy.data.data;
                    }

                    if (responsesData.status === 1 || !_.isEmpty(self.itemCopy)) {

                        if (self.idParams || !_.isEmpty(self.itemCopy)) {
                            if (_.isObject(responsesData.data.data)) {
                                self.model.BinaryDataNo = responsesData.data.data.BinaryDataNo;
                                self.model.BinaryDataName = responsesData.data.data.BinaryDataName;
                                self.model.BinaryDataKey = responsesData.data.data.BinaryDataKey;
                                self.model.BinaryData1 = responsesData.data.data.BinaryData1;
                                self.model.BinaryData0 = responsesData.data.data.BinaryData0;
                                self.model.inactive = (responsesData.data.data.Inactive) ? true : false;
                            }
                            if (!_.isEmpty(self.itemCopy)) {
                                self.model.BinaryDataNo = responsesData.data.auto;
                            }
                        } else {
                            self.model.BinaryDataNo = responsesData.data.auto;
                        }

                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                });
            },
            onNavigationItem(type) {
                let currentIndex = this.reqParams.idsArray.indexOf(this.idParams);
                let newIndex = (type === 'prev') ? currentIndex - 1 : currentIndex + 1;

                if (newIndex === (this.reqParams.idsArray.length) && (this.reqParams.currentPage != this.reqParams.lastPage)) {
                    this.reqParams.currentPage = this.reqParams.currentPage + 1;
                    this.getItemIds(type);
                } else if (newIndex < 0 && this.reqParams.currentPage > 1){
                    this.reqParams.currentPage = this.reqParams.currentPage - 1;
                    this.getItemIds(type);
                }
                else {
                    this.idParams = (this.reqParams.idsArray[newIndex]) ? this.reqParams.idsArray[newIndex] : this.idParams;
                }
            },
            getItemIds(type){
                let self = this;
                let requestData = {
                    method: 'post',
                    url: ListApi,
                    data: {
                        per_page: this.reqParams.perPage,
                        page: this.reqParams.currentPage,
                        type: 'only-id'
                    }
                };

                if (this.reqParams.search.BinaryDataNo !== '') {
                    requestData.data.BinaryDataNo = this.reqParams.search.BinaryDataNo;
                }
                if (this.reqParams.search.BinaryDataName !== '') {
                    requestData.data.BinaryDataName = this.reqParams.search.BinaryDataName;
                }

                this.$store.commit('isLoading', true);
                ApiService.customRequest(requestData).then((response) => {
                    let dataResponse = response.data;
                    if (dataResponse.status === 1) {
                        self.reqParams.total = dataResponse.data.total;
                        self.reqParams.perPage = String(dataResponse.data.per_page);
                        self.reqParams.currentPage = dataResponse.data.current_page;

                        this.reqParams.idsArray = [];
                        _.forEach(dataResponse.data.data, function (value, key) {
                            self.reqParams.idsArray.push(value.BinaryDataID);
                        });

                        (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                });

            },
            handleSubmitForm(){
                let self = this;
                const requestData = {
                    method: 'post',
                    url: StoreApi,
                    data: {
                        BinaryDataNo: this.model.BinaryDataNo,
                        BinaryDataName: this.model.BinaryDataName,
                        BinaryDataKey: this.model.BinaryDataKey,
                        BinaryData1: this.model.BinaryData1,
                        BinaryData0: this.model.BinaryData0,
                        Inactive: (this.model.inactive) ? 1 : 0,
                    }
                };

                // edit user
                if (this.idParams) {
                    requestData.data.BinaryDataID = this.idParams;
                    requestData.url = UpdateApi + '/' + this.idParams;
                }

                this.$store.commit('isLoading', true);
                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => {
                    let responsesData = responses.data;
                    if (responsesData.status === 1) {
                      if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
                      self.$router.push({
                        name: ViewRouter,
                        params: {id: self.idParams, message: 'Bản ghi đã được cập nhật!'}
                      });
                    } else {
                        console.log(responsesData.data);
                        let htmlErrors = __.renderErrorApiHtml(responsesData.data);
                        Swal.fire(
                            'Thông báo',
                            htmlErrors,
                            'error'
                        )
                    }

                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    Swal.fire(
                        'Thông báo',
                        'Không kết nối được với máy chủ',
                        'error'
                    );
                    self.$store.commit('isLoading', false);
                });
            },

            onEditClicked(){
                this.$router.push({
                    name: EditRouter,
                    params: {id: this.idParams, req: this.reqParams}
                });
            },
            onCreateClicked(){
                this.$router.push({name: CreateRouter});
            },
            onBackToList() {
                this.$router.push({name: ListRouter});
            },

            updateModel() {
                if (this.stage.updatedData) {
                    this.$forceUpdate();
                }
            },

        },
        watch: {
            idParams() {
                this.fetchData();
            },
        }
    }
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="css">
    .v-select .dropdown-menu {
        max-height: 170px !important;
    }
    .custom-align {
        flex: 0 0 12.3%;
    }
</style>
