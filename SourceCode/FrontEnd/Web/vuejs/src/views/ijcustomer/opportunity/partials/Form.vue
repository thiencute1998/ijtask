<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Cơ hội<span v-if="model.uomName">:</span> {{model.uomName}}</span>
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Cơ hội<span v-if="model.uomName">:</span> {{model.uomName}}</span>
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
                          <div class="col-lg-2 col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên</div>
                          <div class="col-lg-10 col-md-16 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2">
                            <input type="text" v-model="model.OpportunityName" id="OpportunityName" class="form-control" placeholder="Tên cơ hội"/>
                          </div>
                          <div class="col-lg-2 col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Giá trị cơ hội</div>
                          <div class="col-lg-4 col-md-16 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2">
                            <IjcoreNumber v-model="model.OTAmount" ></IjcoreNumber>
                          </div>
                          <div class="col-lg-6 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span style="width: 122px">Ngày giao</span>
                            <IjcoreDatePicker v-model="model.OpportunityDate" style="width: 130px;">
                            </IjcoreDatePicker>
                          </div>
                        </div>

                        <div class="form-group row">
                          <div class="col-lg-2 col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Người liên hệ</div>
                          <div class="col-lg-10 col-md-16 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2">
                            <IjcoreModalListing
                              v-model="model" :title="'khách hàng'" :api="'/listing/api/common/list'"
                              :table="'customer'" :FieldID="'CustomerID'" :FieldName="'CustomerName'">
                            </IjcoreModalListing>
                          </div>

                          <div class="col-lg-2 col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Giao cho</div>
                          <div class="col-lg-4 col-md-16 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2">
                            <IjcoreModalListing
                              v-model="model" :title="'giao cho'" :api="'/listing/api/common/list'"
                              :table="'employee'" :FieldID="'EmployeeID'" :FieldName="'EmployeeName'">
                            </IjcoreModalListing>
                          </div>
                          <div class="col-lg-6 col-md-6 col-sm-8 col-24 d-flex align-items-center">
                            <span style="width: 122px">Ngày chốt</span>
                            <IjcoreDatePicker v-model="model.OpportunityDate" style="width: 130px;">
                            </IjcoreDatePicker>
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
    import IjcoreModalListing from "../../../../components/IjcoreModalListing";
    import IjcoreNumber from "../../../../components/IjcoreNumber";
    import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
    import IjcoreDateTimePicker from '@/components/IjcoreDateTimePicker';

    const ListRouter = 'customer-opportunity';
    const EditRouter = 'customer-opportunity-edit';
    const CreateRouter = 'customer-opportunity-create';
    const ViewRouter = 'customer-opportunity-view';
    const ViewApi = 'customer/api/opportunity/view';
    const EditApi = 'customer/api/opportunity/edit';
    const CreateApi = 'customer/api/opportunity/create';
    const StoreApi = 'customer/api/opportunity/store';
    const UpdateApi = 'customer/api/opportunity/update';
    const ListApi = 'customer/api/opportunity';

    String.prototype.capitalize = function () {
        return this.charAt(0).toUpperCase() + this.slice(1);
    };

    export default {
        name: 'listing-view-item',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    OpportunityID: null,
                    OpportunityName: '',
                    CustomerID: '',
                    CustomerName: null,
                    EmployeeID: null,
                    EmployeeName: null,
                    OpportunityDate: null,
                    ExpectedDate: null,
                    OTAmount: null,
                    inactive: null,
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
          IjcoreNumber,
          IjcoreDatePicker,
          IjcoreDateTimePicker,
          IjcoreModalListing,
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
                        let auto = responsesData.data.auto;
                        responsesData = self.itemCopy;
                        responsesData.data.auto = auto;
                    }
                    if (responsesData.status === 1) {

                        if (this.idParams || !_.isEmpty(self.itemCopy)) {
                            if (_.isObject(responsesData.data)) {
                                self.model.OpportunityID = responsesData.data.OpportunityID;
                                self.model.OpportunityName = responsesData.data.OpportunityName;
                                self.model.CustomerID = responsesData.data.CustomerID;
                                self.model.CustomerName = responsesData.data.CustomerName;
                                self.model.EmployeeID = responsesData.data.EmployeeID;
                                self.model.EmployeeName = responsesData.data.EmployeeName;
                                self.model.OpportunityDate = responsesData.data.OpportunityDate;
                                self.model.ExpectedDate = responsesData.data.ExpectedDate;
                                self.model.OTAmount = responsesData.data.OTAmount;
                                self.model.inactive = (responsesData.data.Inactive) ? true : false;
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

                if (this.reqParams.search.OpportunityName !== '') {
                    requestData.data.OpportunityName = this.reqParams.search.OpportunityName;
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
                            self.reqParams.idsArray.push(value.UomID);
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
                    OpportunityName: this.model.OpportunityName,
                    CustomerID: this.model.CustomerID,
                    CustomerName: this.model.CustomerName,
                    EmployeeID: this.model.EmployeeID,
                    EmployeeName: this.model.EmployeeName,
                    OpportunityDate: this.model.OpportunityDate,
                    ExpectedDate: this.model.ExpectedDate,
                    OTAmount: this.model.OTAmount,
                    Inactive: (this.model.inactive) ? 1 : 0
                  }
              };

              // edit user
              if (this.idParams) {
                  requestData.data.UomID = this.idParams;
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

            }
        }
    }
</script>

<style lang="css"></style>
