<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Công việc<span v-if="model.taskName">:</span> {{model.taskName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Công việc<span v-if="model.taskName">:</span> {{model.taskName}}</span>
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
                          <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên công việc</div>
                          <div class="col-lg-16">
                            <input type="text" v-model="model.TaskName" id="task-name" class="form-control" name="ItemName" placeholder="Nhập tên công việc"/>
                          </div>
                          <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span>Mã số</span>
                            <input type="text" v-model="model.TaskNo" class="form-control" placeholder="Nhập mã số"/>
                          </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <div class="col-md-2" style="white-space: nowrap">Là con của</div>
                            <div class="col-md-16">
                                <input type="text" v-model="model.ParentID" id="parentid" class="form-control" name="ParentID" placeholder="Là con của"/>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                                <span>Mã số</span>
                                <input type="text" v-model="model.TaskNo" class="form-control" placeholder="Nhập mã số"/>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-md-2" style="white-space: nowrap">Mức độ ưu tiên</label>
                            <div class="col-md-2">
                                <b-form-select v-model="model.Priority" :options="model.PriorityOptions" id="item-uom"></b-form-select>
                            </div>

                            <div class="col-md-2" style="white-space: nowrap">Quyền truy cập</div>
                            <div class="col-md-2">
                                <b-form-select v-model="model.AccessType" :options="model.AccessTypeOptions" id="item-uom"></b-form-select>
                            </div>

                            <div class="col-md-8">
                                <b-form-select v-model="model.PublicCompanyID" :options="model.CompanyOptions" id="PublicCompanyID"></b-form-select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2" style="white-space: nowrap">Mô tả</label>
                            <div class="col-md-20">
                                <textarea v-model="model.TaskDescription" class="form-control" id="TaskDescription" rows="3" placeholder="Nhập mô tả" name="TaskDescription"></textarea>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-md-4 m-0">Đơn vị tính</label>
                            <div class="col-md-2">
                                <b-form-select v-model="model.Uom" :options="model.UomOptions" id="Uom"></b-form-select>
                            </div>

                            <div class="col-md-2"></div>
                            <label class="col-md-4 m-0">Kiểu lịch</label>
                            <div class="col-md-8">
                                <b-form-select v-model="model.CalendarTypeID" :options="model.CalendarOptions" id="CalendarTypeID"></b-form-select>
                            </div>

                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-md-4 m-0">Ngày bắt đầu</label>
                            <div class="col-md-8">


                                <date-picker
                                        v-model="model.StartDate" valueType="format"
                                        :lang="lang"
                                        :format="momentFormat"
                                        :input-class="classDatePicker"
                                >
                                    <template v-slot:input="{ emit }">
                                        <masked-input
                                                type="text"
                                                name="StartDate"
                                                class="form-control"
                                                v-model="model.StartDate"
                                                :mask="[/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/]"
                                                :guide="true"
                                                placeholderChar="_"
                                                :showMask="true"
                                                :keepCharPositions="true"
                                                :pipe="autoCorrectedDatePipe()"
                                        >
                                        </masked-input>

                                    </template>
                                </date-picker>
                            </div>

                            <label class="col-md-4 m-0">Ngày kết thúc</label>
                            <div class="col-md-8">

                                <date-picker
                                        v-model="model.DueDate" valueType="format"
                                        :lang="lang"
                                        :format="momentFormat"
                                        :input-class="classDatePicker"
                                >
                                    <template v-slot:input="{ emit }">
                                        <masked-input
                                                type="text"
                                                name="DueDate"
                                                class="form-control"
                                                v-model="model.DueDate"
                                                :mask="[/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/]"
                                                :guide="true"
                                                placeholderChar="_"
                                                :showMask="true"
                                                :keepCharPositions="true"
                                                :pipe="autoCorrectedDatePipe()"
                                        >
                                        </masked-input>

                                    </template>
                                </date-picker>
                            </div>

                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-md-4 m-0">Số giờ ước thực hiện</label>
                            <div class="col-md-8">
                                <input type="number" v-model="model.Duration" id="Duration" class="form-control" name="Duration" placeholder="Nhập số giờ ước thực hiện"/>
                            </div>

                            <label class="col-md-4 m-0">KL ước thực hiện</label>
                            <div class="col-md-8">
                                <input type="number" v-model="model.EstimatedQuantity" id="EstimatedQuantity" class="form-control" name="EstimatedQuantity" placeholder="Nhập khối lượng ước thực hiện"/>
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
    import MaskedInput from 'vue-text-mask';
    import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
    //datepicker
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';
    import 'vue2-datepicker/locale/vi';

    const ListRouter = 'task-tasks';
    const EditRouter = 'task-task-edit';
    const CreateRouter = 'task-task-create';
    const ViewRouter = 'task-task-view';
    const DetailApi = 'task/api/task/view';
    const EditApi = 'task/api/task/edit';
    const CreateApi = 'task/api/task/create';
    const StoreApi = 'task/api/task/store';
    const UpdateApi = 'task/api/task/update';
    const ListApi = 'task/api/task';
    let today = new Date();
    let dd = String(today.getDate()).padStart(2, '0');
    let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    let yyyy = today.getFullYear();

    today = dd + '/' + mm + '/' + yyyy;
    String.prototype.capitalize = function () {
        return this.charAt(0).toUpperCase() + this.slice(1);
    };
    export default {
        name: 'task-detail-task',
        components: {
            MaskedInput,
            DatePicker,
        },
        data () {
            return {
                attrDate: {id:'inputDate', ref:'inputDate1'},
                idDatePicker: 'demoStartDate',
                classDatePicker: 'mx-input demo',
                momentFormat: 'DD/MM/YYYY',
                lang: {
                    formatLocale: {
                        firstDayOfWeek: 1,
                    },
                    monthBeforeYear: false,
                },
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    TaskID: null,
                    TaskNo: '',
                    TaskName: '',
                    ParentID: '',
                    ParentName: '',
                    Priority: 3,
                    AccessType: 2,
                    PublicCompanyID: '',
                    TaskDescription: '',
                    Uom: '',
                    CalendarTypeID: '',
                    StartDate: today,
                    DueDate: '',
                    Duration: '',
                    EstimatedQuantity:'',
                    PriorityOptions: [],
                    AccessTypeOptions: [],
                    CompanyOptions: [],
                    CalendarOptions: [],
                    UomOptions: [],
                    TaskOptions: [],
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
            taskCopy: {
                type: Object,
                default: function () {
                    return {}
                }
            }
        },

        beforeCreate() {
        },
        mounted() {
            this.fetchData();

        },
        updated() {
            this.stage.updatedData = true;
        },
        computed: {
            taskNo(){
                let index = 0;
                index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
                return index;
            },
        },
        methods: {
            autoCorrectedDatePipe: () => {
                return createAutoCorrectedDatePipe('dd/mm/yyyy')
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

                    // copy task
                    if (!self.idParams && !_.isEmpty(self.taskCopy)) {
                        responsesData.data.data = self.taskCopy.data;
                    }

                    if (responsesData.status === 1 || !_.isEmpty(self.taskCopy)) {

                        if (_.isObject(responsesData.data.data)) {
                            let dataModel = responsesData.data.data;
                            self.model.TaskName = dataModel.TaskName
                            self.model.TaskID = dataModel.TaskID;
                            self.model.TaskNo = dataModel.TaskNo;
                            self.model.TaskName = dataModel.TaskName;
                            self.model.ParentID = dataModel.ParentID;
                            self.model.ParentName = dataModel.ParentName;
                            self.model.Priority = dataModel.Priority;
                            self.model.AccessType = dataModel.AccessType;
                            self.model.PublicCompanyID = dataModel.PublicCompanyID;
                            self.model.TaskDescription = dataModel.TaskDescription;
                            self.model.Unit = dataModel.Unit;
                            self.model.CalendarTypeID = dataModel.CalendarTypeID;
                            self.model.StartDate = dataModel.StartDate;
                            self.model.DueDate = dataModel.DueDate;
                            self.model.Duration = dataModel.Duration;
                            self.model.EstimatedQuantity = dataModel.EstimatedQuantity;
                        }
                        self.model.TaskNo = responsesData.data.auto;
                        _.forEach(responsesData.data.Priority, function (value, key) {
                            let tmpObj = {};
                            tmpObj.value = key;
                            tmpObj.text = value;
                            self.model.PriorityOptions.push(tmpObj);
                        });

                        _.forEach(responsesData.data.AccessType, function (value, key) {
                            let tmpObj = {};
                            tmpObj.value = key;
                            tmpObj.text = value;
                            self.model.AccessTypeOptions.push(tmpObj);
                        });
                        if (_.isArray(responsesData.data.Calendar)) {

                            self.model.CalendarOptions = [
                                {value: "", text: '[Chọn kiểu lịch]'}
                            ];
                            _.forEach(responsesData.data.Calendar, function (value, key) {
                                let tmpObj = {};
                                tmpObj.value = value.CalendarTypeID;
                                tmpObj.text = value.CalendarName;
                                self.model.CalendarOptions.push(tmpObj);
                            });
                        }
                        if (_.isArray(responsesData.data.Uom)) {

                            self.model.UomOptions = [
                                {value: "", text: '[Chọn đơn vị tính]'}
                            ];
                            _.forEach(responsesData.data.Uom, function (value, key) {
                                let tmpObj = {};
                                tmpObj.value = value.UomID;
                                tmpObj.text = value.UomName;
                                self.model.UomOptions.push(tmpObj);
                            });
                        }

                        if (_.isArray(responsesData.data.Company)) {

                            self.model.CompanyOptions = [
                                {value: "", text: '[Chọn đơn vị truy cập]'}
                            ];
                            _.forEach(responsesData.data.Company, function (value, key) {
                                let tmpObj = {};
                                tmpObj.value = value.CompanyID;
                                tmpObj.text = value.CompanyName;
                                self.model.CompanyOptions.push(tmpObj);
                            });
                        }

                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                });
            },
            handleSubmitForm(){
                const requestData = {
                    method: 'post',
                    url: StoreApi,
                    data: {
                        TaskNo: this.model.TaskNo,
                        TaskName: this.model.TaskName,
                        ParentID: this.model.ParentID,
                        ParentName: this.model.ParentName,
                        Priority: this.model.Priority,
                        AccessType: this.model.AccessType,
                        PublicCompanyID: this.model.PublicCompanyID,
                        TaskDescription: this.model.TaskDescription,
                        Unit: this.model.Unit,
                        CalendarTypeID: this.model.CalendarTypeID,
                        StartDate: this.model.StartDate,
                        DueDate: this.model.DueDate,
                        Duration: this.model.Duration,
                        EstimatedQuantity: this.model.EstimatedQuantity,
                    }
                };

                // edit user
                if (this.idParams) {
                    requestData.data.ItemID = this.idParams;
                    requestData.url = UpdateApi + '/' + this.idParams;
                }

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

                }, (error) => {
                    console.log(error);
                    Swal.fire(
                        'Thông báo',
                        'Không kết nối được với máy chủ',
                        'error'
                    )
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
            'model.taxRate'(){

            }
        }
    }
</script>

<style lang="css"></style>
