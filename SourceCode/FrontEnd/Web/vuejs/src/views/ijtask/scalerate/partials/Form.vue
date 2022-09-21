<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Bảng điểm<span v-if="model.ScaleRateName ">:</span> {{model.ScaleRateName }}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Bảng điểm<span v-if="model.ScaleRateName ">:</span> {{model.ScaleRateName}}</span>
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
                            <label class="col-md-4 m-0" for="ScaleRateName">Tên</label>
                            <div class="col-md-20">
                                <input v-model="model.ScaleRateName" type="text" id="ScaleRateName" class="form-control" placeholder="Tên" name="ScaleRateName"/>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 m-0" >Mức tối đa</label>
                          <div class="col-md-6" style="padding-right: 0px;">
                            <IjcoreNumber v-model="model.MaxLevel"></IjcoreNumber>
<!--                            <input type="number" v-model="model.MaxLevel" class="form-control" placeholder="Mức tối đa" />-->
                          </div>
                          <label class="col-md-4 m-0">Là bảng điểm mặc định</label>
                          <div class="col-md-1">
                            <b-form-checkbox v-model="model.isDefault" id="item-is-isDefault" style="cursor: pointer"></b-form-checkbox>
                          </div>
                          <label class="col-md-6 m-0">Dùng để xếp loại công việc</label>
                          <div class="col-md-1">
                            <b-form-checkbox v-model="model.UsingEvaluationTask" id="item-is-manager" style="cursor: pointer"></b-form-checkbox>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-md-2" for="Comment">Ghi chú</label>
                          <div class="col-md-20">
                            <textarea v-model="model.Comment" class="form-control" id="Comment" rows="3" placeholder="Ghi chú" name="Comment"></textarea>
                          </div>
                        </div>

                        <label>Giá trị bảng điểm:</label>
                        <table class="table b-table table-sm table-bordered table-editable">
                            <thead>
                            <tr>
                                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Mức(1,2,3,…)</th>
                                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Mức(A,B,C,…)</th>
                                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Từ điểm</th>
                                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Đến điểm</th>
                                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Từ điểm 100</th>
                                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Đến điểm 100</th>
                                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Điểm 100 </th>
                                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Xếp loại </th>
                                <th scope="col" style="width: 3%; border-bottom: none;" class="text-center"><!--<i @click="onAddFieldOnTable" class="fa fa-plus" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i>--></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(field, key) in model.ScaleRateItem">
                                <td style="text-align: left">
                                  <b-form-input
                                    type="number"
                                    v-model="model.ScaleRateItem[key].LevelInt" style="text-align: left">
                                  </b-form-input>
                                </td>
                                <td style="text-align: left">
                                  <b-form-input
                                    type="text"
                                    v-model="model.ScaleRateItem[key].LevelChar" style="text-align: left">
                                  </b-form-input>
                                </td>
                                <td style="text-align: left">
                                  <b-form-input
                                    type="number"
                                    v-model="model.ScaleRateItem[key].FromPoint" style="text-align: left">
                                  </b-form-input>
                                </td>
                                <td style="text-align: left">
                                  <b-form-input
                                    type="number"
                                    v-model="model.ScaleRateItem[key].ToPoint" style="text-align: left">
                                  </b-form-input>
                                </td>
                                <td style="text-align: left">
                                  <b-form-input
                                    type="number" @keyup="addtoLevelInt100(model.ScaleRateItem[key].FromPoint100, key)"
                                    v-model="model.ScaleRateItem[key].FromPoint100" style="text-align: left">
                                  </b-form-input>
                                </td>
                                <td style="text-align: left">
                                  <b-form-input
                                    type="number"
                                    @keyup="addtoLevelInt100(model.ScaleRateItem[key].ToPoint100, key)"
                                    v-model="model.ScaleRateItem[key].ToPoint100" style="text-align: left">
                                  </b-form-input>
                                </td>
                                <td style="text-align: left">
                                  <b-form-input
                                    type="number"
                                    v-model="model.ScaleRateItem[key].LevelInt100" style="text-align: left">
                                  </b-form-input>
                                </td>
                                <td style="text-align: left">
                                  <b-form-input
                                    type="text"
                                    v-model="model.ScaleRateItem[key].LevelText" style="text-align: left">
                                  </b-form-input>
                                </td>

                                <td class="text-center">
                                    <i @click="onDeleteFieldOnTable(field)" class="fa fa-trash-o" title="Xóa" style="font-size: 18px; cursor: pointer"></i>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                      <a @click="onAddFieldOnTable()" class="new-row">
                        <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
                      </a>
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
    import IjcoreNumber from "../../../../components/IjcoreNumber";

    moment.locale('vi');

    const ListRouter = 'task-scalerate';
    const EditRouter = 'task-scalerate-edit';
    const CreateRouter = 'task-scalerate-create';
    const ViewRouter = 'task-scalerate-view';
    const DetailApi = 'task/api/scalerate/view';
    const EditApi = 'task/api/scalerate/edit';
    const CreateApi = 'task/api/scalerate/create';
    const StoreApi = 'task/api/scalerate/store';
    const UpdateApi = 'task/api/scalerate/update';
    const ListApi = 'task/api/scalerate';

    export default {
        name: 'task-scalerate-detail',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    ScaleRateName : '',
                    MaxLevel : '',
                    UsingEvaluationTask : true,
                    isDefault : true,
                    Comment : '',
                    LevelInt : '',
                    LevelChar : '',
                    FromPoint : '',
                    ToPoint : '',
                    FromPoint100 : '',
                    ToPoint100 : '',
                    LevelInt100 : '',
                    LevelText : '',
                    ScaleRateItem : [],
                    maxLineID: 0,
                    Inactive: false,
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
            IjcoreNumber,
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
            addtoLevelInt100(evt, key){
              // if (!evt) {
              //   alert('Bạn nhập sai kiểu dữ liệu!');
              // }
              let FromPoint100 = this.model.ScaleRateItem[key].FromPoint100;
              let ToPoint100 = this.model.ScaleRateItem[key].ToPoint100;
              let LevelInt100 = (Number(FromPoint100) + Number(ToPoint100))/2;
              if(LevelInt100=='Infinity'){LevelInt100='';}
              if(LevelInt100){
                this.model.ScaleRateItem[key].LevelInt100 = LevelInt100.toFixed(2);
              }else{
                this.model.ScaleRateItem[key].LevelInt100 ='';
              }
              this.$forceUpdate()
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

                    let responsesData = responses.data;
                    // copy item
                    if (!self.idParams && !_.isEmpty(self.itemCopy)) {
                        responsesData = self.itemCopy;
                    }
                    if (responsesData.status === 1) {
                      //console.log(responsesData);
                        if (self.idParams || !_.isEmpty(self.itemCopy)) {
                            if (_.isObject(responsesData.data.data)) {
                                self.model.ScaleRateName  = responsesData.data.data.ScaleRateName;
                                self.model.MaxLevel  = responsesData.data.data.MaxLevel;
                                self.model.Comment  = responsesData.data.data.Comment;
                                self.model.UsingEvaluationTask = (responsesData.data.data.UsingEvaluationTask) ? true : false;
                                self.model.isDefault = (responsesData.data.data.isDefault) ? true : false;
                                self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;

                                self.model.ScaleRate = responsesData.data.ScaleRate;
                                self.model.ScaleRateItem = responsesData.data.ScaleRateItem;

                                // set max lineID
                                _.forEach(self.model.ScaleRateItem, function (field, key) {
                                    if (Number(field.LineID) > self.model.maxLineID) self.model.maxLineID = Number(field.LineID);
                                });
                            }
                        }
                    }
                    // console.log('xxxx');
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

                if (this.reqParams.search.ScaleRateName  !== '') {
                    requestData.data.CateName  = this.reqParams.search.ScaleRateName ;
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
                            self.reqParams.idsArray.push(value.CateID);
                        });

                        (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                });

            },
            onAddFieldOnTable(){
                let fieldObj = {};
                this.model.maxLineID += 1;
                fieldObj.LineID = this.model.maxLineID;
                fieldObj.LevelInt = null;
                fieldObj.LevelChar = null;
                fieldObj.FromPoint = '';
                fieldObj.ToPoint = '';
                fieldObj.FromPoint100 = '';
                fieldObj.ToPoint100 = '';
                fieldObj.LevelInt100 = '';
                fieldObj.LevelText = null;
                this.model.ScaleRateItem.push(fieldObj);
                this.$forceUpdate();
            },
            onDeleteFieldOnTable(field){
                // remove field in fieldOnTableReq
                let fieldExist = _.find(this.model.ScaleRateItem, ['LineID', field.LineID]);
                if (_.isObject(fieldExist)) {
                    _.remove(this.model.ScaleRateItem, ['LineID', field.LineID]);
                }
                this.$forceUpdate();
            },
            handleSubmitForm(){
                let self = this;
                const requestData = {
                    method: 'post',
                    url: StoreApi,
                    data: {
                        master: {
                            ScaleRateName : this.model.ScaleRateName ,
                            MaxLevel : this.model.MaxLevel ,
                            UsingEvaluationTask : this.model.UsingEvaluationTask ,
                            isDefault : this.model.isDefault ,
                            Comment : this.model.Comment ,
                            Inactive: (this.model.Inactive) ? 1 : 0,
                        },
                        detail: this.model.ScaleRateItem
                    }
                };

                // edit user
                if (this.idParams) {
                    requestData.data.master.ScaleRateID = this.idParams;
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
            isCheckValidate(evt){
              if (!evt) {
                alert('Bạn nhập sai kiểu dữ liệu!');
              }
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
    .col-md-6 input{ text-align: left !important;}
</style>
