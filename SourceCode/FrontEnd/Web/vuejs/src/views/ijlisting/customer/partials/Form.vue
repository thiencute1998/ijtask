<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Khách Hàng<span v-if="model.CustomerName">:</span> {{model.CustomerName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Khách Hàng<span v-if="model.CustomerName">:</span> {{model.CustomerName}}</span>
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
                          <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                            <input v-model="model.CustomerName" type="text" id="CustomerName" class="form-control" placeholder="Tên khách hàng" name="CustomerName"/>
                          </div>
                          <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span>Mã số</span>
                            <input type="text" v-model="model.CustomerNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-md-3 m-0">Loại khách hàng</label>
                            <div class="col-md-21">
                                <customer-modal-search-input-catelist
                                  v-model="model.CustomerCate"
                                  title-modal="Loại khách hàng"
                                  placeholder="Loại khách hàng"
                                ></customer-modal-search-input-catelist>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-3 m-0">Địa chỉ GD</label>
                          <div class="col-md-9 mb-3 mb-sm-0">
                            <input v-model="model.Address" type="text" class="form-control" placeholder="Địa chỉ giao dịch">
                          </div>
                          <label class="col-md-3 m-0">Địa chỉ nhận HĐ</label>
                          <div class="col-md-9">
                            <input v-model="model.BillTo" type="text" class="form-control" placeholder="Địa chỉ hợp đồng">
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-3 m-0">ĐC nhận hàng</label>
                          <div class="col-md-9 mb-3 mb-sm-0">
                            <input v-model="model.ShipTo" type="text" class="form-control" placeholder="Địa chỉ nhận hàng">
                          </div>
                          <label class="col-md-3 m-0">Mã số thuế</label>
                          <div class="col-md-9">
                            <input v-model="model.TaxCode" type="text" class="form-control" placeholder="Mã số thuế">
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-3 m-0">Tài khoản NH</label>
                          <div class="col-md-9 mb-3 mb-sm-0">
                            <input v-model="model.BankAccount" type="text" class="form-control" placeholder="Tài khoản ngân hàng">
                          </div>
                          <label class="col-md-3 m-0">Địa chỉ NH</label>
                          <div class="col-md-9">
                            <input v-model="model.BankName" type="text" class="form-control" placeholder="Địa chỉ ngân hàng">
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-3 m-0">ĐTCQ</label>
                          <div class="col-md-5 mb-3 mb-sm-0">
                            <input v-model="model.OfficePhone" type="text" class="form-control" placeholder="Điện thoại cơ quan">
                          </div>
                          <label class="col-md-2 m-0">Fax</label>
                          <div class="col-md-5">
                            <input v-model="model.Fax" type="text" class="form-control" placeholder="Số Fax">
                          </div>
                          <label class="col-md-2 m-0">Email</label>
                          <div class="col-md-7">
                            <input v-model="model.Email" type="text" class="form-control" placeholder="Địa chỉ Email">
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                        <label class="col-md-3 m-0">Tỉnh</label>
                        <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
                          <ijcore-modal-search-input
                            v-model="model.Province"
                            :select-fields-api="[
                              {field: 'ProvinceID',fieldForSelected: 'id', showInTable: false, key: 'ProvinceID'},
                              {field: 'ProvinceName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'ProvinceName', sortable: true, thClass: 'd-none'}
                            ]"
                            :search-fields-api="[{field: 'ProvinceName', placeholder: 'Nhập tên', name: 'ProvinceName', class: '', style: ''}]"
                            table="province"
                            ref="myModalSearchInputProvince"
                            id-modal="myModalSearchInputProvince"
                            :item-per-page="8"
                            placeholder="Tỉnh"
                            :url-api="$store.state.appRootApi + '/listing/api/common/get-province'"
                            name-input="input-province"
                            title-modal="Tỉnh" size-modal="lg">
                          </ijcore-modal-search-input>
                        </div>
                        <label class="col-md-2 m-0">Huyện</label>
                        <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
                          <ijcore-modal-search-input
                            v-model="model.District"
                            :select-fields-api="[
                              {field: 'DistrictID',fieldForSelected: 'id', showInTable: false, key: 'DistrictID'},
                              {field: 'DistrictName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'DistrictName', sortable: true, thClass: 'd-none'}
                            ]"
                            :search-fields-api="[{field: 'DistrictName', placeholder: 'Nhập tên', name: 'DistrictName', class: '', style: ''}]"
                            table="district"
                            ref="myModalSearchInputDistrict"
                            id-modal="myModalSearchInputDistrict"
                            :item-per-page="8"
                            placeholder="Huyện"
                            :request-data="{ProvinceID: (model.Province) ? model.Province.ProvinceID : null}"
                            :url-api="$store.state.appRootApi + '/listing/api/common/get-district'"
                            name-input="input-district"
                            title-modal="Huyện" size-modal="lg">
                          </ijcore-modal-search-input>
                        </div>
                        <label class="col-md-2 m-0">Xã</label>
                        <div class="col-md-7 ">
                          <ijcore-modal-search-input
                            v-model="model.Commune"
                            :select-fields-api="[
                              {field: 'CommuneID',fieldForSelected: 'id', showInTable: false, key: 'CommuneID'},
                              {field: 'CommuneName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'CommuneName', sortable: true, thClass: 'd-none'}
                            ]"
                            :search-fields-api="[{field: 'CommuneName', placeholder: 'Nhập tên', name: 'CommuneName', class: '', style: ''}]"
                            table="commune"
                            ref="myModalSearchInputCommune"
                            id-modal="myModalSearchInputCommune"
                            :item-per-page="8"
                            placeholder="Xã"
                            :request-data="{
                              ProvinceID: (model.Province) ? model.Province.ProvinceID : null,
                              DistrictID: (model.District) ? model.District.DistrictID : null
                            }"
                            :url-api="$store.state.appRootApi + '/listing/api/common/get-commune'"
                            name-input="input-commune"
                            title-modal="Xã" size-modal="lg">
                          </ijcore-modal-search-input>
                        </div>
                      </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-3 m-0">Quyền truy cập</label>
                          <div class="col-md-5">
                            <b-form-select v-model="model.AccessType" :options="AccessTypeOptions" id="item-uom">
                            </b-form-select>
                          </div>
                          <label class="col-md-2 col-sm-4 m-0" title="Là nhà cung cấp">Là NCC</label>
                          <div class="col-md-5 col-sm-20">
                            <b-form-checkbox v-model="model.isVendor"></b-form-checkbox>
                          </div>
                          <label class="col-md-2 m-0" for="Website">Website</label>
                          <div class="col-md-7 m-0">
                            <input v-model="model.Website" type="text" id="Website" class="form-control" placeholder="Website" name="Website" />
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
    import CustomerModalSearchInputCatelist from "@/views/ijlisting/customer/partials/CustomerModalSearchInputCatelist";
    import IjcoreModalParent from "../../../../components/IjcoreModalParent";

    const ListRouter = 'listing-customer';
    const EditRouter = 'listing-customer-edit';
    const ViewRouter = 'listing-customer-view';
    const CreateRouter = 'listing-customer-create';
    const ViewApi = 'listing/api/customer/view';
    const EditApi = 'listing/api/customer/edit';
    const CreateApi = 'listing/api/customer/create';
    const StoreApi = 'listing/api/customer/store';
    const UpdateApi = 'listing/api/customer/update';
    const ListApi = 'listing/api/customer';

    export default {
        name: 'listing-view-item',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    CustomerID: null,
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
                    Province: {},
                    District: {},
                    Commune: {},
                    Note: '',
                    isVendor: false,
                    Prefix: '',
                    Suffix: '',
                    Inactive: false,
                    EmployeeName: '',
                    EmployeeID: null,
                    CustomerOption: [],
                    AccessType: 1,

                    CustomerCate: [],

                },
                AccessTypeOptions:{
                  1: 'Chia sẻ',
                  2: 'Công khai',
                  3: 'Riêng tư'
                },
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
          CustomerModalSearchInputCatelist,
          IjcoreModalParent,
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
            },
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
                        responsesData.data.data = self.itemCopy.data.data;
                    }

                    if (responsesData.status === 1) {

                        if (self.idParams || !_.isEmpty(self.itemCopy)) {
                            if (_.isObject(responsesData.data.data)) {
                                self.model.CustomerID = responsesData.data.data.CustomerID;
                                self.model.CustomerName = responsesData.data.data.CustomerName;
                                self.model.CustomerNo = responsesData.data.data.CustomerNo;
                                self.model.ManagementLevel = responsesData.data.data.ManagementLevel;
                                self.model.Note = responsesData.data.data.Note;
                                self.model.Prefix = responsesData.data.data.Prefix;
                                self.model.Suffix = responsesData.data.data.Suffix;
                                self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
                            }

                            if (!_.isEmpty(self.itemCopy)) {
                                self.model.CustomerNo = responsesData.data.auto;
                                self.model.CustomerCate = [];
                                if(self.itemCopy.data.CustomerCate){
                                  _.forEach(self.itemCopy.data.CustomerCate, (customerCate, key)=>{
                                    let tmpObj = {};
                                    if(customerCate.CateID){
                                      let cateList = _.find(self.itemCopy.data.CustomerCateList, ['CateID', customerCate.CateID]);
                                      if(cateList){
                                        tmpObj.CateID = cateList.CateID;
                                        tmpObj.CateName = cateList.CateName;
                                      }
                                    }
                                    if(customerCate.CateValue){
                                      // let cateValue = _.find(self.itemCopy.data.CustomerCateValue, (cate)=> {
                                      //   return cate.CateID === customerCate.CateID && cate.CateValue === customerCate.CateValue;
                                      // });
                                      let cateValue = _.find(self.itemCopy.data.CustomerCateValue,{
                                        CateID: customerCate.CateID,
                                        CateValue: customerCate.CateValue
                                      });
                                      if(cateValue){
                                        tmpObj.CateValue = cateValue.CateValue;
                                        tmpObj.Description = cateValue.Description;
                                      }
                                    }
                                    else{
                                      tmpObj.CateValue = null;
                                      tmpObj.Description = '';
                                    }
                                    // self.model.CustomerCate.push(tmpObj);
                                    self.$set(self.model.CustomerCate, self.model.CustomerCate.length, tmpObj);
                                  })
                                }
                            }
                        }else {
                            self.model.CustomerNo = responsesData.data.auto;
                        }


                        if (_.isArray(responsesData.data.customer)) {

                            self.model.CustomerOption = [];
                            _.forEach(responsesData.data.customer, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.CustomerID;
                                tmpObj.text = value.CustomerName;
                                self.model.CustomerOption.push(tmpObj);
                            });
                        }

                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                });
            },
            changeParent(){
              let self = this;
              let urlApi = this.api;
              let requestData = {
                method: 'post',
                url: '/listing/api/common/auto-child',
                data: {
                  per_page: 10,
                  page: this.currentPage,
                  table: 'customer',
                  ParentID: this.model.ParentID,
                },

              };

              ApiService.customRequest(requestData).then((response) => {
                let responseData = response.data;

                this.model.CustomerNo = responseData.data;
                self.$store.commit('isLoading', false);
              }, (error) => {
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

                if (this.reqParams.search.customerNo !== '') {
                    requestData.data.CustomerNo = this.reqParams.search.customerNo;
                }
                if (this.reqParams.search.customerName !== '') {
                    requestData.data.CustomerName = this.reqParams.search.customerName;
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

            handleSubmitForm(){
                let self = this;
                const requestData = {
                    method: 'post',
                    url: StoreApi,
                    data: {
                        CustomerNo: this.model.CustomerNo,
                        CustomerName: this.model.CustomerName,
                        Address : this.model.Address,
                        BillTo : this.model.BillTo,
                        ShipTo: this.model.ShipTo,
                        TaxCode: this.model.TaxCode,
                        BankAccount : this.model.BankAccount,
                        BankName : this.model.BankName,
                        OfficePhone: this.model.OfficePhone,
                        Fax: this.model.Fax,
                        Email : this.model.Email,
                        Website : this.model.Website,
                        ProvinceID: this.model.Province.ProvinceID,
                        ProvinceName: this.model.Province.ProvinceName,
                        DistrictID: this.model.District.DistrictID,
                        DistrictName: this.model.District.DistrictName,
                        CommuneID: this.model.Commune.CommuneID,
                        CommuneName: this.model.Commune.CommuneName,
                        isVendor: this.model.isVendor,
                        Inactive: (this.model.Inactive) ? 1 : 0,
                        Note: this.model.Note,
                        AccessType: this.model.AccessType,
                        CustomerCate: this.model.CustomerCate
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
          'model.ParentID'(){
              let self = this;
              let urlApi = '/listing/api/common/auto-child';
              let requestData = {
                method: 'post',
                url: urlApi,
                data: {
                  per_page: 10,
                  page: this.currentPage,
                  table: 'customer',
                  ParentID: this.model.ParentID,
                }
              }
              self.$store.commit('isLoading',true)
              ApiService.setHeader();
              ApiService.customRequest(requestData)
                .then(response=>{
                  let responseData = response.data;
                  if(responseData.status === 1){
                    self.model.CustomerNo = responseData.data;
                  }
                  self.$store.commit('isLoading',false)
                }).catch(error=> {
                self.$store.commit('isLoading',false)
              })
          }
        }
    }
</script>
<!--<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>-->
<style lang="css">
  .select2-container{
    width: 100% !important;
  }
</style>
