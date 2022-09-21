<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Trạng thái chức năng<span v-if="model.featureStatusName">:</span> {{model.featureStatusName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Trạng thái chức năng<span v-if="model.featureStatusName">:</span> {{model.featureStatusName}}</span>
                        </div>
                    </b-col>
                    <b-col class="col-md-6"></b-col>
                </b-row>
                <b-row>
                  <b-col class="col-md-12 col-sm-12 col-24 mb-2 mb-sm-0 mb-md-0">
                    <div class="main-header-item main-header-actions">
                        <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitForm"><i class="fa fa-check-square-o"></i> Lưu</b-button>
                        <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onBackToList"><i class="fa fa-ban"></i> Hủy</b-button>
                    </div>
                    </b-col>
                  <b-col class="col-md-12 col-sm-12 col-24">
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
                            <div class="col-md-4 col-sm-4 col-24 mb-2 mb-sm-0 mb-md-0" for="TaskCodeName">Tên trạng thái chức năng</div>
                            <div class="col-md-20 col-sm-20 col-24">
                                <input v-model="model.StatusName" type="text" id="TaskCodeName" class="form-control" placeholder="Tên trạng thái chức năng" name="StatusName "/>
                            </div>
                        </div>

                        <label>Giá trị loại trạng thái:</label>
                        <table class="table b-table table-sm table-bordered table-editable">
                            <thead>
                            <tr>
                                <th scope="col" style="width: 30%" class="text-center">Tên</th>
                                <th scope="col" style="width: 10%" class="text-center">Kiểu giá trị</th>
                                <th scope="col" style="width: 10%" class="text-center">Giá trị</th>
                                <th scope="col" style="width: 10%" class="text-center">Trạng thái thực hiện</th>
                                <th scope="col" style="width: 3%" class="text-center"><i @click="onAddFieldOnTable" class="fa fa-plus" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(field, key) in model.sysStatusItems">
                                <td>
                                    <b-form-input
                                            type="text"
                                            v-model="model.sysStatusItems[key].StatusDescription"
                                            autocomplete="task code list description">
                                    </b-form-input>
                                </td>
                                <td>
                                    <b-form-select :options="dataTypeOption" @change="onChangeDataType($event, field)" v-model="model.sysStatusItems[key].DataType"></b-form-select>
                                </td>
                                <td>
                                    <b-form-input
                                            v-if="field.DataType === 1"
                                            type="number"
                                            v-model="model.sysStatusItems[key].StatusValue"
                                            autocomplete="task code list description">
                                    </b-form-input>
                                    <b-form-input
                                            v-if="field.DataType === 2"
                                            type="text"
                                            v-model="model.sysStatusItems[key].StatusValue"
                                            autocomplete="task code list description">
                                    </b-form-input>
                                    <masked-input
                                            v-if="field.DataType === 3"
                                            type="text"
                                            name="date"
                                            v-model="model.sysStatusItems[key].StatusValue + ''"
                                            class="form-control"
                                            :mask="[/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/]"
                                            :guide="true"
                                            placeholderChar="_"
                                            :showMask="true"
                                            :keepCharPositions="true"
                                            :pipe="autoCorrectedDatePipe()">
                                    </masked-input>
                                    <masked-input
                                            v-if="field.DataType === 4"
                                            type="text"
                                            name="date-time"
                                            v-model="model.sysStatusItems[key].StatusValue + ''"
                                            class="form-control"
                                            :mask="[/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/, ' ', /\d/, /\d/, ':', /\d/, /\d/]"
                                            :guide="true"
                                            placeholderChar="_"
                                            :showMask="true"
                                            :keepCharPositions="true"
                                            :pipe="autoCorrectedDateTimePipe()">
                                    </masked-input>


                                    <b-form-select v-if="field.DataType === 5" v-model="model.sysStatusItems[key].StatusValue" :options="[{value: 1, text: 'Có'},{value: 2, text: 'Không'}]"></b-form-select>
                                    <b-form-select v-if="field.DataType === 6" v-model="model.sysStatusItems[key].StatusValue" :options="[{value: 1, text: 'Đúng'},{value: 2, text: 'Sai'}]"></b-form-select>

                                </td>

                                <td>
                                    <b-form-select :options="executionStatusOption" v-model="model.sysStatusItems[key].ExecutionStatus"></b-form-select>
                                </td>

                                <td class="text-center">
                                    <i @click="onDeleteFieldOnTable(field)" class="fa fa-trash-o" title="Xóa" style="font-size: 18px; cursor: pointer"></i>
                                </td>
                            </tr>
                            </tbody>
                        </table>
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
    import moment from 'moment';
    import Select2 from 'v-select2-component';

    moment.locale('vi');


    const ListRouter = 'sysadmin-sys-status';
    const EditRouter = 'sysadmin-sys-status-edit';
    const CreateRouter = 'sysadmin-sys-status-create';
    const ViewRouter = 'sysadmin-sys-status-view';
    const ViewApi = 'sysadmin/api/sys-status/view';
    const EditApi = 'sysadmin/api/sys-status/edit';
    const CreateApi = 'sysadmin/api/sys-status/create';
    const StoreApi = 'sysadmin/api/sys-status/store';
    const UpdateApi = 'sysadmin/api/sys-status/update';
    const ListApi = 'sysadmin/api/sys-status';

    const dataTypeOption = {
        1: 'Số',
        2: 'Kí tự',
        3: 'Ngày',
        4: 'Ngày giờ',
        5: 'Có/Không',
        6: 'Đúng/Sai'
    };

    export default {
        name: 'sysadmin-tcatelist-view',
        data () {
            return {

              myValue: '',
              myOptions: ['op1', 'op2', 'op3'], // or [{id: key, text: value}, {id: key, text: value}],

                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                  StatusName: '',
                  sysStatusItems: [],
                  maxLineID: 0,
                },
                stage: {
                    updatedData: false
                },

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
            MaskedInput,
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
            },
            dataTypeOption(){
                return [
                    {value: 1, text: 'Số'},
                    {value: 2, text: 'Kí tự'},
                    {value: 3, text: 'Ngày'},
                    {value: 4, text: 'Ngày giờ'},
                    {value: 5, text: 'Có/Không'},
                    {value: 6, text: 'Đúng/Sai'}
                ];
            },
            executionStatusOption(){
                return [
                    {value: null, text: 'Chọn trạng thái thực hiện'},
                    {value: 1, text: 'Chưa thực hiện'},
                    {value: 2, text: 'Đang chờ'},
                    {value: 3, text: 'Đang thực hiện'},
                    {value: 4, text: 'Tạm dừng'},
                    {value: 5, text: 'Hủy bỏ'},
                    {value: 6, text: 'Đã hoàn thành'},
                ]
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

                    let responsesData = responses.data;
                    // copy item
                    if (!self.idParams && !_.isEmpty(self.itemCopy)) {
                        responsesData = self.itemCopy;
                    }
                    if (responsesData.status === 1) {
                        if (self.idParams || !_.isEmpty(self.itemCopy)) {
                            if (_.isObject(responsesData.data.data)) {
                                self.model.StatusName = responsesData.data.data.StatusName ;
                                self.model.inactive = (responsesData.data.data.Inactive) ? true : false;

                                self.model.sysStatusItems = responsesData.data.SysStatusItem;
                                // set max lineID
                                _.forEach(self.model.sysStatusItems, function (field, key) {
                                    if (Number(field.LineID) > self.model.maxLineID) self.model.maxLineID = Number(field.LineID);
                                    // set type of value
                                    if (field.DataType == 1) self.model.sysStatusItems[key].StatusValue = Number(self.model.sysStatusItems[key].StatusValue);
                                    if (field.DataType == 2) self.model.sysStatusItems[key].StatusValue = String(self.model.sysStatusItems[key].StatusValue);
                                    if (field.DataType == 3) self.model.sysStatusItems[key].StatusValue = moment(self.model.sysStatusItems[key].StatusValue).format('DD/MM/YYYY');
                                    if (field.DataType == 4) self.model.sysStatusItems[key].StatusValue = moment(self.model.sysStatusItems[key].StatusValue).format('DD/MM/YYYY HH:mm');
                                });

                            }
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

                if (this.reqParams.search.StatusName !== '') {
                    requestData.data.StatusName  = this.reqParams.search.StatusName;
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
                            self.reqParams.idsArray.push(value.StatusID);
                        });

                        (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                });

            },
            onChangeDataType(value, field){
                if (value === 1) field.StatusValue = 0;
                if (value === 2) field.StatusValue = '';
                if (value === 3) field.StatusValue = moment().format('DD/MM/YYYY');
                if (value === 4) field.StatusValue = moment().format('DD/MM/YYYY hh:mm');
                if (value === 5 || value === 6) field.StatusValue = 1;
                this.$forceUpdate();
            },
            onAddFieldOnTable(){
                let fieldObj = {};
                this.model.maxLineID += 1;
                fieldObj.LineID = this.model.maxLineID;
                fieldObj.DataType = 1;
                fieldObj.StatusValue = null;
                fieldObj.StatusDescription = '';
                fieldObj.NOrder = null;
                this.model.sysStatusItems.push(fieldObj);
                this.$forceUpdate();
            },
            onDeleteFieldOnTable(field){

                // remove field in fieldOnTableReq
                let fieldExist = _.find(this.model.sysStatusItems, ['LineID', field.LineID]);
                if (_.isObject(fieldExist)) {
                    _.remove(this.model.sysStatusItems, ['LineID', field.LineID]);
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
                            StatusName: this.model.StatusName,
                            Inactive: (this.model.inactive) ? 1 : 0,
                        },
                        detail: this.model.sysStatusItems
                    }
                };

                // edit user
                if (this.idParams) {
                    requestData.data.master.StatusID = this.idParams;
                    requestData.url = UpdateApi + '/' + this.idParams;
                }

                console.log('gửi đi');
                console.log(requestData);
                this.$store.commit('isLoading', true);
                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => {
                    let responsesData = responses.data;
                    console.log('trả về');
                    console.log(responsesData);
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
