<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Giao dịch bán hàng<span v-if="model.CustomerName">:</span> {{model.CustomerName}}</span>
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Giao dịch bán hàng<span v-if="model.CustomerName">:</span> {{model.CustomerName}}</span>
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
                        <label class="col-md-2" style="white-space: nowrap">Ngày</label>
                        <div class="col-md-6 DateTimeText" >
                          <IjcoreDateTimePicker v-model="model.TransDate" :allowEmptyClear="true" style="width: 250px">
                          </IjcoreDateTimePicker>
                        </div>
                        <label class="col-md-1" style="white-space: nowrap">Số phút</label>
                        <div class="col-md-2">
                          <input v-model="model.Time" class="form-control"/>
                        </div>
                        <label class="col-md-1" style="white-space: nowrap">Nhân viên</label>
                        <div class="col-md-6">
                          <IjcoreModalListing v-model="model" :title="'nhân viên'" :api="'/listing/api/common/list'"
                                              :table="'employee'" :FieldID="'EmployeeID'" :FieldName="'EmployeeName'">
                          </IjcoreModalListing>
                        </div>
                      </div>
                      <div class="form-group row align-items-center">
                        <label class="col-md-2" style="white-space: nowrap">Khách hàng</label>
                        <div class="col-md-12">
                          <IjcoreModalListing v-model="model" :title="'khách hàng'" :api="'/listing/api/common/list'"
                                              :table="'customer'" :FieldID="'CustomerID'" :FieldName="'CustomerName'"
                                              :FieldUpdate="['CustomerName', 'ContactName', 'DepartmentName', 'OfficePhone', 'HandPhone', 'Email']">
                          </IjcoreModalListing>
                        </div>
                        <label class="col-md-1" style="white-space: nowrap">Người liên hệ</label>
                        <div class="col-md-6">
                          <IjcoreModalListing v-model="model" :title="'người liên hệ'" :api="'/listing/api/common/list'"
                                              :table="'Customer_Contact'" :FieldID="'LineID'" :FieldName="'ContactName'"
                                              :FieldUpdate="['PositionName']"
                                              :FieldWhere="{'CustomerID' : model.CustomerID}">
                          </IjcoreModalListing>
                        </div>

                      </div>
                      <div class="form-group row align-items-center">
                        <label class="col-md-2" style="white-space: nowrap">Hàng hóa</label>
                        <div class="col-md-20" >
                          <IjcoreModalListing v-model="model" :title="'hàng hóa dịch vụ'" :api="'/listing/api/common/list'"
                                              :table="'item'" :FieldID="'ItemID'" :FieldName="'ItemName'">
                          </IjcoreModalListing>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-md-2" style="white-space: nowrap">Nội dung</label>
                        <div class="col-md-20" >
                          <textarea class="form-control" rows="3" placeholder="" v-model="model.TransComment" ></textarea>
                        </div>
                      </div>
                      <div class="form-group row align-items-center">
                        <label class="col-md-2" style="white-space: nowrap">Tiền tệ </label>
                        <div class="col-md-2">
                          <IjcoreModalListing v-model="model.CcyName" :title="'tiền tệ'" :api="'/listing/api/common/list'"
                                              :table="'ccy'" :FieldID="'CcyID'" :FieldName="'CcyName'">
                          </IjcoreModalListing>
                        </div>
                        <label class="col-md-2" style="white-space: nowrap">Tỷ giá</label>
                        <div class="col-md-2">
                          <IjcoreNumber v-model="model.ExchangeRate" ></IjcoreNumber>
                        </div>
                        <label class="col-md-2" style="white-space: nowrap">Giá trị nguyên tệ</label>
                        <div class="col-md-2">
                          <IjcoreNumber v-model="model.FCAmount"></IjcoreNumber>
                        </div>
                      </div>
                      <div class="form-group row align-items-center">
                        <label class="col-md-2" style="white-space: nowrap">Ngày chốt</label>
                        <div class="col-md-4 DateText" >
                          <IjcoreDatePicker v-model="model.ExpectedEndDate" style="width: 160px">
                          </IjcoreDatePicker>
                        </div>
                        <label class="col-md-2" style="white-space: nowrap">% Thành công</label>
                        <div class="col-md-2">
                          <input v-model="model.PercentSuccess" class="form-control"/>
                        </div>
                        <label class="col-md-2" style="white-space: nowrap">Giá trị qui đổi</label>
                        <div class="col-md-2">
                          <IjcoreNumber v-model="model.LCAmount" ></IjcoreNumber>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-md-2"  style="white-space: nowrap">Loại giao dịch bán hàng</label>
                        <div class="col-md-2 col-sm-20">
                          <i @click="AddCustomerSalesTransCate()"
                             class="fa fa-external-link" title="Loại giao dịch bán hàng"
                             style="font-size: 18px; cursor: pointer; padding-right: 5px; padding-left: 12px;"></i>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-md-2" style="white-space: nowrap"></label>
                        <div class="col-md-20">
                          <p v-for="(field, key) in value.CustomerSalesTransCate" style="margin-bottom: -2px;"><span v-if="field.CateName">{{field.CateName}}: {{field.CateValue}}<br></span></p>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-md-2"  style="white-space: nowrap">Tệp đính kèm</label>
                        <div class="col-md-2 col-sm-20">
                          <IjcoreUploadMultipleFile
                            v-on:changed="changeFile" :isIcon="true"></IjcoreUploadMultipleFile>
                        </div>
                      </div>
                      <b-collapse class="mt-2" v-model="showCustomerFile">
                        <div class="form-group row">
                          <label class="col-md-2" style="white-space: nowrap"></label>
                          <div class="col-md-20 m-0">
<!--                            <CustomerFileContent v-model="model" >-->
<!--                            </CustomerFileContent>-->
                          </div>
                        </div>
                      </b-collapse>

                      <div class="form-group row align-items-center">
                        <div class="col-lg-2">Loại trạng thái</div>
                        <div class="col-lg-2">
                          <b-form-select v-model="model.StatusID" @change="onChangeStatus"></b-form-select>
                        </div>
                        <div class="col-lg-1">Trạng thái</div>
                        <div class="col-lg-2">
                          <b-form-select v-model="model.StatusValue" :options="StatusValueOption | filterStatusValueOption(Number(CustomerSalesTrans.StatusID))"></b-form-select>
                        </div>
                      </div>

                      <!-- Loại giao dịch kinh doanh -->
                      <b-modal ref="CustomerSalesTransCate" id="modal-form-input-task-general1" size="lg"
                               title="Loại giao dịch bán hàng">
                        <div class="main-body main-body-view-action">
                          <table class="table b-table table-sm table-bordered table-editable">
                            <thead>
                            <tr>
                              <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Loại giao dịch bán hàng </th>
                              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Giá trị</th>
                              <th scope="col" style="width: 2%; border-bottom: none;" class="text-center"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(field, key) in CustomerSalesTransCate">
                              <td>
                                <IjcoreModalListing v-model="CustomerSalesTransCate[key]" :title="'loại giao dịch bán hàng'" :api="'/listing/api/common/list'"
                                                    :table="'customer_sales_trans_cate_list'" :FieldID="'CateID'" :FieldName="'CateName'" @changed="changeCustomerCateValue()">
                                </IjcoreModalListing>
                              </td>
                              <td>
                                <IjcoreModalListing v-model="CustomerSalesTransCate[key]" :title="'giá trị'" :api="'/listing/api/common/list'"
                                                    :table="'customer_sales_trans_cate_value'" :FieldName="'CateValue'"
                                                    :FieldWhere="{'CateID' : CustomerSalesTransCate[key]}" @changed="changeCustomerCateValue()">
                                </IjcoreModalListing>
                              </td>
                              <td class="text-center">
                                <i @click="onDeleteFieldCustomerSalesTransCate(key)" class="fa fa-trash-o" title="Xóa"
                                   style="font-size: 18px; cursor: pointer"></i>
                              </td>
                            </tr>

                            </tbody>
                          </table>
                          <a @click="onAddFieldCustomerSalesTransCate()" class="new-row">
                            <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
                          </a>
                        </div>
                        <template v-slot:modal-footer>
                          <div class="w-100 left">
                            <b-button variant="primary" size="md" class="float-left mr-2" @click="SaveCustomerSalesTransCate()">
                              Lưu
                            </b-button>
                            <b-button variant="primary" size="md" class="float-left mr-2" @click="HideCustomerSalesTransCate()">
                              Đóng
                            </b-button>
                          </div>
                        </template>
                      </b-modal>
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
    import IjcoreModalMultiListing from "../../../../components/IjcoreModalMultiListing";
    import IjcoreUploadMultipleFile from "../../../../components/IjcoreUploadMultipleFile";

    const ListRouter = 'customer-salestrans';
    const EditRouter = 'customer-salestrans-edit';
    const CreateRouter = 'customer-salestrans-create';
    const ViewRouter = 'customer-salestrans-view';
    const ViewApi = 'customer/api/salestrans/view';
    const EditApi = 'customer/api/salestrans/edit';
    const CreateApi = 'customer/api/salestrans/create';
    const StoreApi = 'customer/api/salestrans/store';
    const UpdateApi = 'customer/api/salestrans/update';
    const ListApi = 'customer/api/salestrans';

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
                    CustomerID: '',
                    TransDate: '',
                    TransComment: '',
                    EmployeeID: '',
                    EmployeeName: '',
                    CustomerName: '',
                    ContactID: '',
                    ContactName: '',
                    CustomerInfo: '',
                    Time: '',
                    FileID: '',
                    FileName: '',
                    ItemID: '',
                    ItemName: '',
                    CcyID: '',
                    CcyNo: '',
                    ExchangeRate: '',
                    FCAmount: '',
                    LCAmount: '',
                    ExpectedEndDate: '',
                    PercentSuccess: '',
                    StatusID: null,
                    StatusDescription: '',
                    CreatedDate: '',
                    CreatedUserID: '',
                    UpdatedDate: '',
                    UpdatedUserID: '',
                    Locked: '',
                    LockedDate: '',
                    LockedUserID: '',
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
          IjcoreModalMultiListing,
          IjcoreUploadMultipleFile,
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
                                self.model.EmployeeID = responsesData.data.EmployeeID;
                                self.model.EmployeeName = responsesData.data.EmployeeName;
                                self.model.CustomerID = responsesData.data.CustomerID;
                                self.model.CustomerName = responsesData.data.CustomerName;
                                self.model.ContactID = responsesData.data.ContactID;
                                self.model.ContactName = responsesData.data.ContactName;
                                self.model.ItemID = responsesData.data.ItemID;
                                self.model.ItemName = responsesData.data.ItemName;
                                self.model.Time = responsesData.data.Time;
                                self.model.FCAmount = responsesData.data.FCAmount;
                                self.model.PercentSuccess = responsesData.data.PercentSuccess;
                                self.model.FileID = responsesData.data.FileID;
                                self.model.FileName = responsesData.data.FileName;
                                self.model.TransComment = responsesData.data.TransComment;
                                self.model.StatusValue = responsesData.data.StatusValue;
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
            },
            AddCustomerSalesTransCate(TransID,key) {
              this.TransItemIDCurrent = TransID;
              this.TransItemKeyCurrent = key;
              this.$forceUpdate();
              this.$refs['CustomerSalesTransCate'].show();
            },
            HideCustomerSalesTransCate() {
              this.$refs['CustomerSalesTransCate'].hide();
            },
            SaveCustomerSalesTransCate() {
              this.$bvToast.toast('Đã lưu loại khách hàng\n', {
                title: 'Thông báo',
                variant: 'success',
                solid: true
              });
              this.$refs['CustomerSalesTransCate'].hide();
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
