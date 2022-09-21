<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Đánh giá 1 công việc <span v-if="model.indicatorName">:</span> {{model.indicatorName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Đánh giá 1 công việc <span v-if="model.indicatorName">:</span> {{model.indicatorName}}</span>
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
                          <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Người đánh giá</div>
                          <div class="col-lg-8 col-md-14 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
                              <IjcoreModalEvaluation1job v-model="model" :title="'nhân viên'" :api="'/listing/api/common/list'"
                                                  :table="'employee'" :FieldID="'EmployeeID'" :FieldName="'EmployeeName'"
                                                  :FieldTitleUpdate="'EmployeeTitle'">
                              </IjcoreModalEvaluation1job>
                          </div>
                          <span class="col-md-4 m-0">Chức vụ</span>
                          <div class="col-lg-8 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <input type="text" v-model="model.EmployeeTitle" class="form-control" placeholder="Chức vụ"/>
                          </div>

                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-md-4 m-0" >Điểm</label>
                            <div class="col-md-8" style="padding-right: 0px;">
                              <input type="text" v-model="model.LevelInt" class="form-control" placeholder="Điểm"/>
                            </div>
                            <label class="col-md-4 m-0">Xếp loại</label>
                            <div class="col-md-8">
                              <input type="text" v-model="model.LevelResult" class="form-control" placeholder="Xếp loại"/>
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
    import Select2 from 'v-select2-component';
    import IjcoreModalEvaluation1job from "../../../../components/IjcoreModalEvaluation1job";

    const ListRouter = 'task-evaluation-1job';
    const EditRouter = 'task-evaluation-1job-edit';
    const ViewRouter = 'task-evaluation-1job-view';
    const CreateRouter = 'task-evaluation-1job-create';
    const DetailApi = 'task/api/evaluation-1job/view';
    const EditApi = 'task/api/evaluation-1job/edit';
    const CreateApi = 'task/api/evaluation-1job/create';
    const StoreApi = 'task/api/evaluation-1job/store';
    const UpdateApi = 'task/api/evaluation-1job/update';
    const ListApi = 'task/api/evaluation-1job';

    export default {
        name: 'listing-detail-item',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                  EmployeeID: null,
                  EmployeeName: '',
                  EmployeeTitle: '',
                  LevelInt: null,
                  LevelResult: null,
                  inactive: false,
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
          IjcoreModalEvaluation1job,
          Select2
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
                //console.log('kkkkkkkkkkk');
                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => { //console.log(responses);
                    let responsesData = responses.data;
                    //console.log(responsesData);

                    if (responsesData.status === 1) {
                            if (_.isObject(responsesData.data.data)) {
                                self.model.EmployeeID = responsesData.data.data.EmployeeID;
                                self.model.EmployeeName = responsesData.data.data.EmployeeName;
                                self.model.EmployeeTitle = responsesData.data.data.EmployeeTitle;
                                self.model.LevelInt = responsesData.data.data.LevelInt;
                                self.model.LevelResult = responsesData.data.data.LevelResult;
                                self.model.inactive = (responsesData.data.data.Inactive) ? true : false;
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

                if (this.reqParams.search.EmployeeID !== '') {
                    requestData.data.EmployeeID = this.reqParams.search.EmployeeID;
                }
                if (this.reqParams.search.EmployeeName !== '') {
                    requestData.data.EmployeeName = this.reqParams.search.EmployeeName;
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
                            self.reqParams.idsArray.push(value.EmployeeID);
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
                        EmployeeID: this.model.EmployeeID,
                        EmployeeName: this.model.EmployeeName,
                        EmployeeTitle: this.model.EmployeeTitle,
                        LevelInt: this.model.LevelInt,
                        LevelResult: this.model.LevelResult,
                        Inactive: (this.model.inactive) ? 1 : 0,
                    }
                };
              //console.log(this.model.requestData);
                // edit user
                if (this.idParams) {
                    requestData.data.ItemID = this.idParams;
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

            autoCorrectedTaxRatePipe() {

            }

        },
        watch: {
            idParams() {
                this.fetchData();
            },

            'model.taxRate'(){

            },
        }
    }
</script>
<!--<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>-->
<style lang="css">
  .select2-selection__rendered{line-height: 24px !important;}
</style>
