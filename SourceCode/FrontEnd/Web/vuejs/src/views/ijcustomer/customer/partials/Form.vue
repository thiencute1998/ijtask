<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Khách hàng<span v-if="model.CustomerName">:</span> {{model.CustomerName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Khách hàng<span v-if="model.CustomerName">:</span> {{model.CustomerName}}</span>
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
                          <div class="col-lg-4 col-md-2 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tên</div>
                          <div class="col-lg-16 col-md-22 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 app-object-name">
                            <input v-model="model.CustomerName" type="text" class="form-control" placeholder="Tên khách hàng"/>
                          </div>
                          <div class="col-lg-4 col-md-6 col-sm-8 app-object-code d-md-none d-sm-none d-none d-lg-flex align-items-center">
                            <span>Mã số</span>
                            <input type="text" v-model="model.CustomerNo" class="form-control" placeholder="Mã số"/>
                          </div>

                          <div class="d-lg-none col-md-2 col-sm-2">Mã số</div>
                          <div class="d-lg-none col-md-8 col-sm-8">
                            <input type="text" v-model="model.CustomerNo" class="form-control" placeholder="Mã số"/>
                          </div>

                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-md-4 col-sm-4 m-0" for="Address">Địa chỉ giao dịch</label>
                            <div class="col-md-20 col-sm-20">
                                <input class="form-control" v-model="model.Address" id="Address" placeholder="Địa chỉ giao dịch" name="Address"/>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-sm-4 m-0" for="BillTo">Địa chỉ nhận hóa đơn</label>
                          <div class="col-md-20 col-sm-20">
                            <input class="form-control" v-model="model.BillTo" id="BillTo" placeholder="Địa chỉ nhận hóa đơn" name="BillTo"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-sm-4 m-0" for="ShipTo">Địa chỉ nhận hàng </label>
                          <div class="col-md-20 col-sm-20">
                            <input class="form-control" v-model="model.ShipTo" id="ShipTo" placeholder="Địa chỉ nhận hàng" name="ShipTo"/>
                          </div>
                        </div>

                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-sm-4 m-0" for="TaxCode">Mã số thuế</label>
                          <div class="col-md-4 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
                            <input type="number" v-model="model.TaxCode" id="TaxCode" class="form-control" placeholder="Mã số thuế" name="TaxCode" />
                          </div>

                          <label class="col-md-2 col-sm-4 m-0" for="Fax">TKNH</label>
                          <div class="col-md-4 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
                            <input v-model="model.BankAccount" type="number" id="BankAccount" class="form-control" placeholder="Tài khoản ngân hàng" name="BankAccount" />
                          </div>

                          <label class="col-md-2 col-sm-4 m-0">ĐCNH</label>
                          <div class="col-md-8 col-sm-20">
                            <input v-model="model.BankName" id="BankName" class="form-control" placeholder="Địa chỉ ngân hàng" name="BankName" />
                          </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-md-4 col-sm-4 m-0" for="OfficePhone">Số điện thoại</label>
                            <div class="col-md-4 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
                                <input type="number" v-model="model.OfficePhone" id="OfficePhone" class="form-control" placeholder="Số điện thoại" name="OfficePhone" />
                            </div>

                            <label class="col-md-2 col-sm-4 m-0" for="Fax">Số Fax</label>
                            <div class="col-md-4 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
                                <input v-model="model.Fax" type="number" id="Fax" class="form-control" placeholder="Số Fax" name="Fax" />
                            </div>

                            <label class="col-md-2 col-sm-4 m-0">Email</label>
                            <div class="col-md-8 col-sm-20">
                                <input v-model="model.Email" type="email" id="Email" class="form-control" placeholder="Email" name="Email" />
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-md-4 col-sm-4 m-0" for="Website">Website</label>
                            <div class="col-md-4 col-sm-20">
                                <input v-model="model.Website" type="text" id="Website" class="form-control" placeholder="Website" name="Website" />
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-sm-4 m-0" for="OfficePhone">Tỉnh</label>
                          <div class="col-md-4 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
                            <IjcoreModalListing
                              v-model="model" :title="'Tỉnh'" :api="'/listing/api/common/list'"
                              :table="'province'" :FieldID="'ProvinceID'" :FieldName="'ProvinceName'">
                            </IjcoreModalListing>
                          </div>

                          <label class="col-md-2 col-sm-4 m-0" for="Fax">Huyện</label>
                          <div class="col-md-6 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
                            <IjcoreModalListing
                              v-model="model" :title="'Huyện'" :api="'/listing/api/common/list'"
                              :table="'district'" :FieldID="'DistrictID'" :FieldName="'DistrictName'"
                              :FieldWhere="{ProvinceID : model.ProvinceID}">
                            </IjcoreModalListing>
                          </div>

                          <label class="col-md-2 col-sm-4 m-0">Xã</label>
                          <div class="col-md-6 col-sm-20">
                            <IjcoreModalListing
                              v-model="model" :title="'Xã'" :api="'/listing/api/common/list'"
                              :table="'commune'" :FieldID="'CommuneID'" :FieldName="'CommuneName'"
                              :FieldWhere="{DistrictID : model.DistrictID}">
                            </IjcoreModalListing>
                          </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-sm-4 m-0" for="Note">Ghi chú</label>
                            <div class="col-md-20 col-sm-20">
                                <textarea v-model="model.Note" class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note"></textarea>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-sm-4 m-0" for="Website">Là nhà cung cấp</label>
                          <div class="col-md-4 col-sm-20">
                            <b-form-checkbox
                              type="check" v-model="model.IsVendor">
                            </b-form-checkbox>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-md-4 col-sm-4 m-0" for="Note">Loại khách hàng</label>
                          <div class="col-md-2 col-sm-20">
                            <i @click="AddCustomerCate()"
                               class="fa fa-external-link" title="Loại khách hàng"
                               style="font-size: 18px; cursor: pointer; padding-right: 5px;"></i>
                          </div>
                          <div class="col-md-18">
<!--                            <span v-if="model.CustomerCate[0]">{{model.CustomerCate[0]}}</span>-->
                            <span v-for="(field, key) in model.CustomerCate">{{field.CateName}}: {{field.Description}}, </span>
                          </div>
                        </div>
                    </b-card>
                </div>
            </vue-perfect-scrollbar>
        </div>

      <!-- Popup Add CustomerCate -->
      <b-modal ref="CustomerCate" id="modal-form-input-task-general1" size="lg"
               title="Loại khách hàng">
        <div class="main-body main-body-view-action">
          <table class="table b-table table-sm table-bordered table-editable">
            <thead>
            <tr>
              <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Loại khách hàng </th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Giá trị</th>
              <th scope="col" style="width: 3%; border-bottom: none;" class="text-center">
                <!--<i @click="onAddFieldOnTable" class="fa fa-plus" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i>--></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(field, key) in model.CustomerCate" v-bind:RowItem="field.CustomerID">
              <td>
                <IjcoreModalListing v-model="model.CustomerCate[key]" :title="'loại khách hàng'" :api="'/listing/api/common/list'"
                                    :table="'customer_cate_list'" :FieldID="'CateID'" :FieldName="'CateName'"
                                    :FieldNo="'CateNo'">
                </IjcoreModalListing>
              </td>
              <td>
                <IjcoreModalListing @changed="changeCustomerCateValue()" v-model="model.CustomerCate[key]" :title="'giá trị'" :api="'/listing/api/common/list'"
                                    :table="'customer_cate_value'" :FieldName="'Description'" :FieldNo="'CateValue'"
                                    :FieldWhere="{CateID : model.CustomerCate[key]['CateID']}">
                </IjcoreModalListing>
              </td>

              <td class="text-center">
                <i @click="onDeleteFieldCustomerCate(key)" class="fa fa-trash-o" title="Xóa"
                   style="font-size: 18px; cursor: pointer"></i>
              </td>
            </tr>
            </tbody>
          </table>
          <a @click="onAddFieldCustomerCate(RowItem)" class="new-row">
            <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
          </a>
        </div>
        <template v-slot:modal-footer>
          <div class="w-100 left">
            <b-button variant="primary" size="md" class="float-left mr-2" @click="SaveCustomerCate()">
              Lưu
            </b-button>
            <b-button variant="primary" size="md" class="float-left mr-2" @click="HideCustomerCate()">
              Đóng
            </b-button>
          </div>
        </template>
      </b-modal>

    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import Swal from 'sweetalert2';
    import 'sweetalert2/src/sweetalert2.scss';
    import vSelect from 'vue-select';
    import IjcoreModalListing from "../../../../components/IjcoreModalListing";

    const ListRouter = 'customer-customer';
    const EditRouter = 'customer-customer-edit';
    const CreateRouter = 'customer-customer-create';
    const ViewRouter = 'customer-customer-view';
    const DetailApi = 'customer/api/customer/view';
    const EditApi = 'customer/api/customer/edit';
    const CreateApi = 'customer/api/customer/create';
    const StoreApi = 'customer/api/customer/store';
    const UpdateApi = 'customer/api/customer/update';
    const ListApi = 'customer/api/customer';

    export default {
        name: 'customer-customer-view',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    CustomerNo: '',
                    CustomerName: '',
                    Address: '',
                    BillTo: '',
                    ShipTo: '',
                    TaxCode: '',
                    BankAccount: '',
                    BankName: '',
                    OfficePhone: '',
                    Fax: '',
                    Email: '',
                    Website: '',
                    ProvinceID: '',
                    ProvinceName: '',
                    DistrictID: '',
                    DistrictName: '',
                    CommuneID: '',
                    CommuneName: '',
                    Note: '',
                    IsVendor: '',
                    inactive: '',
                    CateID: '',
                    CustomerCate: [],
                },
                RowItem: [],
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
            changeCustomerCateValue(){
              this.$forceUpdate();
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
                        let auto = responsesData.data.auto;
                        responsesData = self.itemCopy;
                        responsesData.data.auto = auto;
                    }
                    if (responsesData.status === 1) {

                        if (self.idParams || !_.isEmpty(self.itemCopy)) {
                            if (_.isObject(responsesData.data)) {
                                self.model.CustomerNo = responsesData.data.CustomerNo;
                                self.model.CustomerName = responsesData.data.CustomerName;
                                self.model.Address = responsesData.data.Address;
                                self.model.BillTo = responsesData.data.BillTo;
                                self.model.ShipTo = responsesData.data.ShipTo;
                                self.model.TaxCode = responsesData.data.TaxCode;
                                self.model.BankAccount = responsesData.data.BankAccount;
                                self.model.BankName = responsesData.data.BankName;
                                self.model.OfficePhone = responsesData.data.OfficePhone;
                                self.model.Fax = responsesData.data.Fax;
                                self.model.Email = responsesData.data.Email;
                                self.model.Website = responsesData.data.Website;
                                self.model.ProvinceID = responsesData.data.ProvinceID;
                                self.model.ProvinceName = responsesData.data.ProvinceName;
                                self.model.DistrictID = responsesData.data.DistrictID;
                                self.model.DistrictName = responsesData.data.DistrictName;
                                self.model.CommuneID = responsesData.data.CommuneID;
                                self.model.CommuneName = responsesData.data.CommuneName;
                                self.model.Note = responsesData.data.Note;
                                self.model.IsVendor = responsesData.data.IsVendor;
                                self.model.inactive = (responsesData.data.Inactive) ? true : false;
                            }
                            if (!_.isEmpty(self.itemCopy)) {
                                self.model.CustomerNo = responsesData.data.auto;
                            }
                        } else {
                            self.model.CustomerNo = responsesData.data.auto;
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

                if (this.reqParams.search.CustomerNo !== '') {
                    requestData.data.CustomerNo = this.reqParams.search.CustomerNo;
                }
                if (this.reqParams.search.CustomerName !== '') {
                    requestData.data.CustomerName = this.reqParams.search.CustomerName;
                }
                if (this.reqParams.search.OfficePhone !== '') {
                    requestData.data.OfficePhone = this.reqParams.search.OfficePhone;
                }
                if (this.reqParams.search.Fax !== '') {
                    requestData.data.Fax = this.reqParams.search.Fax;
                }
                if (this.reqParams.search.Email !== '') {
                    requestData.data.Email = this.reqParams.search.Email;
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
                            self.reqParams.idsArray.push(value.CustomerID);
                        });

                        (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                });

            },
            onChangedEmployeeCompany(){
                this.model.companyID = this.model.companyObj.value;
            },
            onChangedEmployeeManager(){
                this.model.managerID = this.model.managerObj.value;
            },
            onChangedEmployeeConcurrentManager(){
                this.model.concurrentManagerID = this.model.concurrentManagerObj.value;
            },
            onChangedEmployeeChecker(){
                this.model.checkerID = this.model.checkerObj.value;
            },
            onChangedEmployeeUser(){
                this.model.userID = this.model.userObj.value;
            },

            handleSubmitForm(){
                let self = this;
                const requestData = {
                    method: 'post',
                    url: StoreApi,
                    data: {
                        CustomerNo: this.model.CustomerNo,
                        CustomerName: this.model.CustomerName,
                        Address: this.model.Address,
                        BillTo: this.model.BillTo,
                        ShipTo: this.model.ShipTo,
                        TaxCode: this.model.TaxCode,
                        BankAccount: this.model.BankAccount,
                        BankName: this.model.BankName,
                        OfficePhone: this.model.OfficePhone,
                        Fax: this.model.Fax,
                        Email: this.model.Email,
                        Website: this.model.Website,
                        ProvinceID: this.model.ProvinceID,
                        ProvinceName: this.model.ProvinceName,
                        DistrictID: this.model.DistrictID,
                        DistrictName: this.model.DistrictName,
                        CommuneID: this.model.CommuneID,
                        CommuneName: this.model.CommuneName,
                        Note: this.model.Note,
                        IsVendor: (this.model.IsVendor) ? 1 : 0,
                        Inactive: (this.model.inactive) ? 1 : 0,
                        CateID: this.model.CateID,
                        detail: this.model.CustomerCate,
                        CopyID: (this.itemCopy && this.itemCopy.data) ? this.itemCopy.data.CustomerID : null
                    }
                };

                // edit user
                if (this.idParams) {
                    requestData.data.CustomerID = this.idParams;
                    requestData.url = UpdateApi + '/' + this.idParams;
                }

                this.$store.commit('isLoading', true);
                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => {
                    let responsesData = responses.data;
                    if (responsesData.status === 1) {
                      self.$router.push({
                        name: ViewRouter,
                        params: {id: self.idParams, req: self.reqParams, message: 'Bản ghi đã được cập nhật!'}
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
            onAddFieldCustomerCate(RowItem) {
              let fieldObj = {};
              fieldObj.CustomerID = RowItem;
              fieldObj.CateID = '';
              fieldObj.CateValue = null;
              this.RowItem = RowItem + 1;
              this.model.CustomerCate.push(fieldObj);
              this.$forceUpdate();
            },
            onDeleteFieldCustomerCate(key) {
              this.model.CustomerCate.splice(key, 1);
              //
              this.setStyleAction();
              this.$forceUpdate();
            },
            AddCustomerCate() {
              this.$forceUpdate();
              this.$refs['CustomerCate'].show();
            },
            HideCustomerCate() {
              this.isForm = false;
              this.$refs['CustomerCate'].hide();
            },
            SaveCustomerCate() {
              this.$bvToast.toast('Đã lưu loại khách hàng\n', {
                title: 'Thông báo',
                variant: 'success',
                solid: true
              });
              this.$refs['CustomerCate'].hide();
            },
            autoCorrectedTaxRatePipe() {

            }

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
