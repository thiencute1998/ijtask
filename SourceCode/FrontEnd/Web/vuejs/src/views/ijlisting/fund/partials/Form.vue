<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Danh mục quỹ<span v-if="model.FundName">:</span> {{model.FundName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Danh mục quỹ<span v-if="model.FundName">:</span> {{model.FundName}}</span>
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
                            <input v-model="model.FundName" type="text" id="FundName" class="form-control" placeholder="Tên danh mục quỹ" name="FundName"/>
                          </div>
                          <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span>Mã số</span>
                            <input type="text" v-model="model.FundNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-md-3 m-0">Là con của</label>
                            <div class="col-md-17">
                              <IjcoreModalSystemParent v-model="model" :title="'Danh mục quỹ'" :api="'/listing/api/common/list'" :table="'fund'" :fieldName="'FundName'" :fieldNo="'FundNo'" :fieldID="'FundID'" :placeholderInput="'Chọn danh mục quỹ cha'" :placeholderSearch="'Nhập tên DMQ'" :columnID="'FundID'" :columnNo="'FundNo'" :columnName="'FundName'">
                              </IjcoreModalSystemParent>
                            </div>
                            <div v-if="model.ParentID" class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                              <span>Mã số</span>
                              <input type="text" v-model="model.ParentNo" class="form-control" placeholder="Mã số"/>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-md-3 m-0">Loại</label>
                            <div class="col-md-21">
                                <FundModalSearchInputCatelist
                                  v-model="model.FundCate"
                                  title-modal="Loại DMQ"
                                  placeholder="Loại danh mục quỹ"
                                ></FundModalSearchInputCatelist>
                            </div>
                        </div>

                      <div class="form-group row align-items-center">
                        <label class="col-md-3 m-0">Quyền truy cập</label>
                        <div class="col-md-9">
                          <b-form-select v-model="model.AccessType" :options="AccessTypeOptions" id="item-uom">
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
    import FundModalSearchInputCatelist from "@/views/ijlisting/fund/partials/FundModalSearchInputCatelist";
    import IjcoreModalSystemParent from "../../../../components/IjcoreModalSystemParent";
    import FundModalSearchEmployee from './FundModalSearchEmployee';

    const ListRouter = 'listing-fund';
    const EditRouter = 'listing-fund-edit';
    const ViewRouter = 'listing-fund-view';
    const CreateRouter = 'listing-fund-create';
    const ViewApi = 'listing/api/fund/view';
    const EditApi = 'listing/api/fund/edit';
    const CreateApi = 'listing/api/fund/create';
    const StoreApi = 'listing/api/fund/store';
    const UpdateApi = 'listing/api/fund/update';
    const ListApi = 'listing/api/fund';

    export default {
        name: 'listing-view-item',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    FundID: null,
                    FundNo: '',
                    FundName: '',
                    ParentID: null,
                    ParentNo: '',
                    ParentName: null,
                    Note: '',
                    Inactive: false,
                    EmployeeName: '',
                    EmployeeID: null,
                    FundOption: [],
                    EmployeeOption: [],
                    AccessType: 1,
                    BalanceType:1,
                    FundCate: [],

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
          FundModalSearchInputCatelist,
          IjcoreModalSystemParent,
          FundModalSearchEmployee,
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
                        if (_.isArray(responsesData.data.fund)) {

                            self.model.FundOption = [];
                            _.forEach(responsesData.data.fund, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.FundID;
                                tmpObj.text = value.FundName;
                                self.model.FundOption.push(tmpObj);
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

                if (this.reqParams.search.FundNo !== '') {
                    requestData.data.FundNo = this.reqParams.search.FundNo;
                }
                if (this.reqParams.search.FundName !== '') {
                    requestData.data.FundName = this.reqParams.search.FundName;
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
                            self.reqParams.idsArray.push(value.FundID);
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
                        FundNo: this.model.FundNo,
                        FundName: this.model.FundName,
                        Inactive: (this.model.Inactive) ? 1 : 0,
                        ParentID: this.model.ParentID,
                        EmployeeID: this.model.EmployeeID,
                        BalanceType: this.model.BalanceType,
                        Note : this.model.Note,
                        AccessType: this.model.AccessType,
                        FundCate: this.model.FundCate,

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
