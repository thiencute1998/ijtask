<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Mẫu bảng chỉ tiêu ĐGCV<span v-if="model.TemplateName ">:</span> {{model.TemplateName }}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Mẫu bảng chỉ tiêu ĐGCV<span v-if="model.TemplateName ">:</span> {{model.TemplateName }}</span>
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
                            <label class="col-md-4 m-0" for="TemplateName ">Tên mẫu</label>
                            <div class="col-md-16">
                                <input v-model="model.TemplateName " type="text" id="TemplateName " class="form-control" placeholder="Tên mẫu bảng chỉ tiêu " name="TemplateName "/>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                              <span>Mã số</span>
                              <input type="text" v-model="model.TemplateNo" class="form-control" placeholder="Mã số"/>
                            </div>
                        </div>
                      <div class="form-group row align-items-center">
                        <label class="col-md-4 m-0" >Loại chỉ tiêu</label>
                        <div class="col-md-8">
                          <b-form-select
                            v-model="model.IndicatorType"
                            :options="[
                                        {value: null, text: 'Chọn loại chỉ tiêu'},
                                        {value: 1, text: 'Chỉ tiêu đơn vị'},
                                        {value: 2, text: 'Chỉ tiêu cá nhân'},]">
                          </b-form-select>
                        </div>
                        <label class="col-md-4 m-0">Phương pháp đánh giá</label>
                        <div class="col-md-8">
                          <b-form-select
                            v-model="model.EvaluationMethod"
                            :options="[
                                        {value: null, text: 'Chọn phương pháp'},
                                        {value: 1, text: 'Chỉ số đo lường hiệu suất'},
                                        {value: 2, text: 'Mục tiêu và kết quả then chốt'},
                                        {value: 3, text: 'Thẻ điểm cân bằng'},
                                        {value: 4, text: 'Bảng điểm'},
                                        {value: 5, text: 'Danh sách kiểm tra'},
                                        {value: 6, text: 'Phản hồi 360 độ'},]">
                          </b-form-select>
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
    import MaskedInput from 'vue-text-mask';
    import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
    import Select2 from 'v-select2-component';
    import moment from 'moment';

    moment.locale('vi');

    const ListRouter = 'task-indicator-temp';
    const EditRouter = 'task-indicator-temp-edit';
    const CreateRouter = 'task-indicator-temp-create';
    const ViewRouter = 'task-indicator-temp-view';
    const DetailApi = 'task/api/indicator-temp/view';
    const EditApi = 'task/api/indicator-temp/edit';
    const CreateApi = 'task/api/indicator-temp/create';
    const StoreApi = 'task/api/indicator-temp/store';
    const UpdateApi = 'task/api/indicator-temp/update';
    const ListApi = 'task/api/indicator-temp';

    const GradingTypeOption = [
        {value: 1, text: 'Điểm thường'},
        {value: 2, text: 'Điểm thưởng'},
        {value: 3, text: 'Điểm phạt'},
    ];

    export default {
        name: 'task-indicatortemp-detail',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    TemplateName : '',
                    TemplateNo : '',
                    IndicatorType : '',
                    EvaluationMethod : null,
                },
                stage: {
                    updatedData: false
                },
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
            Select2,
            MaskedInput,
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
            },
            dataTypeOption(){
                return DataTypeOption;
            }
        },
        methods: {
            handleDebugger(value){
                alert('aaa');
            },
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
                  //console.log(responses);
                    let responsesData = responses.data;
                    // copy item
                    if (!self.idParams && !_.isEmpty(self.itemCopy)) {
                        responsesData = self.itemCopy;
                    }
                    if (responsesData.status === 1) {
                      //console.log(responsesData);
                        if (self.idParams || !_.isEmpty(self.itemCopy)) {
                            if (_.isObject(responsesData.data.data)) {
                                self.model.TemplateName  = responsesData.data.data.TemplateName ;
                                self.model.TemplateNo  = responsesData.data.data.TemplateNo ;
                                self.model.IndicatorType = responsesData.data.data.IndicatorType;
                                self.model.EvaluationMethod = responsesData.data.data.EvaluationMethod;
                                self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;

                            }
                            if (!_.isEmpty(self.itemCopy)) {
                              self.model.TemplateNo = responsesData.data.auto;
                            }
                        } else {
                            if (_.isArray(responsesData.data)) {

                                self.model.IndicatorTempOption = [];
                                _.forEach(responsesData.data, function (value, key) {
                                    let tmpObj = {};
                                    tmpObj.id = value.TemplateID;
                                    tmpObj.text = value.TemplateName ;
                                    self.model.IndicatorTempOption.push(tmpObj);
                                });
                            }
                            self.model.TemplateNo = responsesData.data.auto;
                        }

                        if (_.isArray(responsesData.data.IndicatorTemp)) {

                            self.model.IndicatorTempOption = [];
                            _.forEach(responsesData.data.IndicatorTemp, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.TemplateID;
                                tmpObj.text = value.TemplateName ;
                                self.model.IndicatorTempOption.push(tmpObj);
                            });
                        }


                    }
                     //console.log('sssssss');
                    // console.log(self.model.IndicatorCateValue);
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

                if (this.reqParams.search.TemplateNo !== '') {
                  requestData.data.TemplateNo = this.reqParams.search.TemplateNo;
                }
                if (this.reqParams.search.TemplateName  !== '') {
                    requestData.data.TemplateName  = this.reqParams.search.TemplateName ;
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
                            self.reqParams.idsArray.push(value.TemplateID);
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
                        TemplateNo: this.model.TemplateNo,
                        TemplateName : this.model.TemplateName ,
                        IndicatorType: this.model.IndicatorType,
                        EvaluationMethod: this.model.EvaluationMethod,
                        Inactive: (this.model.Inactive) ? 1 : 0,
                    }
                };

                // edit user
                if (this.idParams) {
                    requestData.data.TemplateID = this.idParams;
                    requestData.url = UpdateApi + '/' + this.idParams;
                }

                this.$store.commit('isLoading', true);
                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => {
                    let responsesData = responses.data;
                    //console.log(responsesData);
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
            autoCorrectedDatePipe: () => { return createAutoCorrectedDatePipe('dd/mm/yyyy') },
            autoCorrectedDateTimePipe: () => {return createAutoCorrectedDatePipe('dd/mm/yyyy hh:mm')},
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
