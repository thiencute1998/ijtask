<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Hệ thống tài khoản xã<span v-if="model.AccountName">:</span> {{model.AccountName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Hệ thống tài khoản xã<span v-if="model.AccountName">:</span> {{model.AccountName}}</span>
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
                          <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
                          <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                            <input v-model="model.AccountName" type="text" id="AccountName" class="form-control" placeholder="Tên hệ thống tài khoản xã" name="AccountName"/>
                          </div>
                          <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span>Mã số</span>
                            <input type="text" v-model="model.AccountNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-md-3 m-0">Là mục con của</label>
                            <div class="col-md-17">
                              <IjcoreModalSystemParent
                                v-model="model"
                                :title="'Hệ thống tài khoản xã'"
                                :api="'/listing/api/common/get-parent'"
                                :table="'coa_scb'"
                                :fieldName="'AccountName'"
                                :fieldNo="'AccountNo'"
                                :fieldID="'AccountID'"
                                :placeholderInput="'Chọn hệ thống tài khoản  xã cha'"
                                :placeholderSearch="'Nhập tên HTTKX'"
                                :columnID="'AccountID'"
                                :columnNo="'AccountNo'"
                                :columnName="'AccountName'"
                              ></IjcoreModalSystemParent>
                            </div>
                            <div v-if="model.ParentID" class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                              <span>Mã số</span>
                              <input type="text" v-model="model.ParentNo" class="form-control" placeholder="Mã số"/>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-md-3 m-0">Loại</label>
                            <div class="col-md-9">
                                <CoaScbModalSearchInputCatelist
                                  v-model="model.CoaScbCate"
                                  title-modal="Loại HTTKX"
                                  placeholder="Loại hệ thống tài khoản  xã"
                                ></CoaScbModalSearchInputCatelist>
                            </div>
                            <label class="col-md-3 m-0">Quyền truy cập</label>
                            <div class="col-md-9">
                              <b-form-select v-model="model.AccessType" :options="AccessTypeOptions" id="item-uom">
                              </b-form-select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-3 m-0">Loại số dư TK</label>
                          <div class="col-md-9">
                            <b-form-select v-model="model.BalanceType" :options="BalanceTypeOptions" id="item-uom">
                            </b-form-select>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label class="col-md-3" for="Note">Ghi chú</label>
                          <div class="col-md-21">
                            <textarea v-model="model.Note" class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note"></textarea>
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
    import Select2 from 'v-select2-component'
    import IjcoreModalListing from "../../../../components/IjcoreModalListing";
    import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
    import CoaScbModalSearchInputCatelist from "@/views/ijlisting/coascb/partials/CoaScbModalSearchInputCatelist";
    import IjcoreModalSystemParent from "../../../../components/IjcoreModalSystemParent";
    import CoaScbModalSearchEmployee from './CoaScbModalSearchEmployee';

    const ListRouter = 'listing-coa-scb';
    const EditRouter = 'listing-coa-scb-edit';
    const ViewRouter = 'listing-coa-scb-view';
    const CreateRouter = 'listing-coa-scb-create';
    const ViewApi = 'listing/api/coa-scb/view';
    const EditApi = 'listing/api/coa-scb/edit';
    const CreateApi = 'listing/api/coa-scb/create';
    const StoreApi = 'listing/api/coa-scb/store';
    const UpdateApi = 'listing/api/coa-scb/update';
    const ListApi = 'listing/api/coa-scb';

    export default {
        name: 'listing-view-item',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    AccountID: null,
                    AccountNo: '',
                    AccountName: '',
                    ParentID: null,
                    ParentNo: '',
                    ParentName: null,
                    Note: '',
                    Inactive: false,
                    EmployeeName: '',
                    EmployeeID: null,
                    CoaScbOption: [],
                    EmployeeOption: [],
                    AccessType: 1,
                    BalanceType:1,
                    CoaScbCate: [],

                },
                AccessTypeOptions:{},
                BalanceTypeOptions:{},
                stage: {
                    isNotification: false,
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
          IjcoreModalListing,
          Select2,
          CoaScbModalSearchInputCatelist,
          IjcoreModalSystemParent,
          CoaScbModalSearchEmployee,
          IjcoreModalSearchInput
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
                    self.AccessTypeOptions = responsesData.data.AccessTypeOptions;
                    self.BalanceTypeOptions = responsesData.data.BalanceTypeOptions;
                    // copy item
                    if (!self.idParams && !_.isEmpty(self.itemCopy)) {
                        responsesData.data.data = self.itemCopy.data.data;
                    }

                    if (responsesData.status === 1) {
                        if (_.isArray(responsesData.data.coaScb)){

                            self.model.CoaScbOption = [];
                            _.forEach(responsesData.data.coaScb, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.AccountID;
                                tmpObj.text = value.AccountName;
                                self.model.CoaScbOption.push(tmpObj);
                            });
                        }


                        if (_.isArray(responsesData.data.employee)) {

                            self.model.EmployeeOption = [];
                            _.forEach(responsesData.data.employee, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.EmployeeID;
                                tmpObj.text = value.EmployeeName;
                                self.model.EmployeeOption.push(tmpObj);
                            });
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

                if (this.reqParams.search.AccountNo !== '') {
                    requestData.data.AccountNo = this.reqParams.search.AccountNo;
                }
                if (this.reqParams.search.AccountName !== '') {
                    requestData.data.AccountName = this.reqParams.search.AccountName;
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
                            self.reqParams.idsArray.push(value.AccountID);
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
                        AccountNo: this.model.AccountNo,
                        AccountName: this.model.AccountName,
                        Inactive: (this.model.Inactive) ? 1 : 0,
                        ParentID: this.model.ParentID,
                        ParentNo: this.model.ParentNo,
                        EmployeeID: this.model.EmployeeID,
                        BalanceType: this.model.BalanceType,
                        Note : this.model.Note,
                        AccessType: this.model.AccessType,
                        CoaScbCate: this.model.CoaScbCate,

                    }
                };

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
                  let item = {
                    AccountID: responsesData.data,
                    AccountNo: self.model.AccountNo,
                    AccountName: self.model.AccountName,
                    Inactive: (self.model.Inactive) ? 1 : 0,
                    ParentID: self.model.ParentID,
                    EmployeeID: self.model.EmployeeID,
                    BalanceType: self.model.BalanceType,
                    Note : self.model.Note,
                    AccessType: self.model.AccessType,
                    CoaScbCate: self.model.CoaScbCate,
                  }
                  let indexold =  _.findIndex(self.$route.params.req.itemsArray, {'AccountID': item.AccountID});
                  let indexParent = null;
                  if(indexold  < 0){
                    if(self.model.ParentID){
                      indexParent = _.findIndex(self.$route.params.req.itemsArray, {'AccountID': self.model.ParentID});
                      if(indexParent >= 0){
                        let level = self.$route.params.req.itemsArray[indexParent].Level;
                        if(self.$route.params.req.itemsArray[indexParent].Detail) {
                          self.$set(self.$route.params.req.itemsArray[indexParent], 'Detail', 0);
                          self.$set(self.$route.params.req.itemsArray[indexParent], 'Class', 'fa fa-minus-square-o');
                        }
                        item.Level = level + 1;
                        item.Detail = 1;
                        self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, indexParent + 1, item);
                      }
                    } else {
                      item.Level = 1;
                      item.Detail = 1;
                      self.$route.params.req.itemsArray.push(item);
                      _.orderBy(self.$route.params.req.itemsArray, ['AccountID'], 'asc');
                    }
                  }
                  self.onBackToList('Bản ghi đã được cập nhật');
                }else if (responsesData.status === 4){
                  Swal.fire(
                    'Lỗi',
                    'Không được sửa bản ghi Tổng hợp',
                    'error'
                  )
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
          onBackToList(message = '') {

            let self = this;
            let params = (this.$route.params.req) ? this.$route.params.req:{};
            let query = this.$route.query;
            query.isBackToList = true;
            if (_.isString(message)) {
              params.message = message;
              this.$router.push({
                name: ViewRouter,
                query: query,
                params: {id: self.idParams, req: params, message: 'Bản ghi đã được cập nhật!'}
              });
            } else {
              this.$router.push({
                name: ListRouter,
                query: query,
                params: params
              });
            }
          },

            updateModel() {
                if (this.stage.updatedData) {
                    this.$forceUpdate();
                }
            },

            autoCorrectedTaxRatePipe() {

            },
          changeUserContact() {
            let employee = _.find(this.model.EmployeeOption, ['id', Number(this.model.EmployeeID)]);
            if (employee) {
              this.model.ContactName = employee.text;
            }
          }

        },
        watch: {
          idParams() {
              this.fetchData();
          },
        }
    }
</script>
<!--<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>-->
<style lang="css">
  .select2-container{
    width: 100% !important;
  }
</style>
