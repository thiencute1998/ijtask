<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i>  Khách Hàng<span v-if="model.CustomerName">:</span> {{model.CustomerName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i>  Khách Hàng<span v-if="model.CustomerName">:</span> {{model.CustomerName}}</span>
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
              <div class="col-lg-3 col-md-3 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tên</div>
              <div class="col-lg-17 col-md-21 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 app-object-name">
                <input v-model="model.CustomerName" type="text" class="form-control" placeholder="Tên khách hàng"/>
              </div>
              <div class="col-lg-4 app-object-code d-md-none d-sm-none d-none d-lg-flex align-items-center">
                <span>Mã số</span>
                <input type="text" v-model="model.CustomerNo" class="form-control" placeholder="Mã số"/>
              </div>
              <div class="d-lg-none col-md-3 col-sm-4">Mã số</div>
              <div class="d-lg-none col-md-9 col-sm-8">
                <input type="text" v-model="model.CustomerNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" for="Address">Địa chỉ GD</label>
              <div class="col-md-9 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
                <input class="form-control" v-model="model.Address" id="Address" placeholder="Địa chỉ giao dịch" name="Address"/>
              </div>

              <label class="col-md-3 col-sm-4 m-0" for="BillTo" title="Địa chỉ nhận hóa đơn">Địa chỉ NHĐ</label>
              <div class="col-md-9 col-sm-20">
                <input class="form-control" v-model="model.BillTo" id="BillTo" placeholder="Địa chỉ nhận hóa đơn" name="BillTo"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" for="Address">Địa chỉ NH</label>
              <div class="col-md-9 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
                <input class="form-control" v-model="model.ShipTo" id="ShipTo" placeholder="Địa chỉ nhận hàng" name="ShipTo"/>
              </div>
              <label class="col-md-3 col-sm-4 m-0" for="TaxCode" title="Mã số thuế">Mã số thuế</label>
              <div class="col-md-9 col-sm-20">
                <input class="form-control" v-model="model.TaxCode" id="TaxCode" placeholder="Mã số thuế" name="TaxCode"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" for="BankAccount" title="Tài khoản ngân hàng">Tài khoản NH</label>
              <div class="col-md-9 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
                <input class="form-control" v-model="model.BankAccount" id="BankAccount" placeholder="Tài khoản ngân hàng" name="BankAccount"/>
              </div>
              <label class="col-md-3 col-sm-4 m-0" for="BankName" title="Địa chỉ ngân hàng">Địa chỉ NH</label>
              <div class="col-md-9 col-sm-20">
                <input class="form-control" v-model="model.BankName" id="BankName" placeholder="Địa chỉ ngân hàng" name="BankName"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <div class="col-lg-3 col-md-3 col-sm-4 mb-lg-0 mb-md-3 mb-sm-3" title="Loại khách hàng">Loại NCC</div>
              <div class="col-lg-9 col-md-9 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
                <customer-modal-search-input-vcatelist
                  v-model="model.CustomerCate"
                  tableApi="customer_cate_list"
                  refModal="myModalSearchVcatelist"
                  id-modal="myModalSearchVcatelist"
                  placeholder="Loại khách hàng"
                  title-modal="Loại khách hàng" size-modal="lg"></customer-modal-search-input-vcatelist>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-4" title="Loại khách hàng">Quyền truy cập</div>
              <div class="col-lg-9 col-md-9 col-sm-20">
                <b-form-select v-model="model.AccessType" :options="AccessTypeOptions"
                               id="item-uom"></b-form-select>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3">Tỉnh</label>
              <div class="col-md-5 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
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

              <label class="col-md-2 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3">Huyện</label>
              <div class="col-md-5 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
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

              <label class="col-md-2 col-sm-4 m-0">Xã</label>
              <div class="col-md-5 col-sm-20">
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
              <label class="col-md-3 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" for="OfficePhone">ĐTĐV</label>
              <div class="col-md-5 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0 mb-lg-0 mb-md-3 mb-sm-3">
                <input type="number" v-model="model.OfficePhone" id="OfficePhone" class="form-control" placeholder="Số điện thoại" name="OfficePhone" />
              </div>

              <label class="col-md-2 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" for="Fax">Số Fax</label>
              <div class="col-md-5 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0 mb-lg-0 mb-md-3 mb-sm-3">
                <input v-model="model.Fax" type="number" id="Fax" class="form-control" placeholder="Số Fax" name="Fax" />
              </div>

              <label class="col-md-2 col-sm-4 m-0">Email</label>
              <div class="col-md-7 col-sm-20">
                <input v-model="model.Email" type="Email" id="Email" class="form-control" placeholder="Email" name="Email" />
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3" for="Website">Website</label>
              <div class="col-md-12 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
                <input v-model="model.Website" type="text" id="Website" class="form-control" placeholder="Website" name="Website" />
              </div>
              <label class="col-md-2 col-sm-4 m-0" for="Website" title="Là khách hàng">NCC</label>
              <div class="col-md-5 col-sm-20">
                <b-form-checkbox v-model="model.isCustomer"></b-form-checkbox>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-sm-4 m-0" for="Note">Ghi chú</label>
              <div class="col-md-21 col-sm-20">
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
  import vSelect from 'vue-select';
  import IjcoreModalSearchInput from "../../../components/IjcoreModalSearchInput";
  import CustomerModalSearchInputVcatelist from "./partials/CustomerModalSearchInputVcatelist";

  const ListRouter = 'listing-customer';
  const EditRouter = 'listing-customer-edit';
  const CreateRouter = 'listing-customer-create';
  const ViewRouter = 'listing-customer-view';
  const ViewApi = 'listing/api/customer/view';
  const EditApi = 'listing/api/customer/edit';
  const CreateApi = 'listing/api/customer/create';
  const StoreApi = 'listing/api/customer/store';
  const UpdateApi = 'listing/api/customer/update';
  const ListApi = 'listing/api/customer';

  export default {
    name: 'listing-customer-view',
    data () {
      return {
        idParams: this.idParamsProps,
        reqParams: this.reqParamsProps,
        itemCopy: {},
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
          Province: {},
          District: {},
          Commune: {},
          Note: '',
          isCustomer: false,
          Locked: null,
          Inactive: null,
          CustomerCate: [],
          AccessType: 1
        },
        AccessTypeOptions:{
          1: 'Chia sẻ',
          2: 'Công khai',
          3: 'Riêng tư'
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
    },

    components: {
      vSelect,
      IjcoreModalSearchInput,
      CustomerModalSearchInputVcatelist
    },
    beforeCreate() {},
    mounted() {
      this.itemCopy = (this.$route.params.itemCopy) ? this.$route.params.itemCopy : {};
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

            if (self.idParams || !_.isEmpty(self.itemCopy)) {
              if (_.isObject(responsesData.data)) {
                self.model.CustomerNo = responsesData.data.data.CustomerNo;
                self.model.CustomerName = responsesData.data.data.CustomerName;
                self.model.Address = responsesData.data.data.Address;
                self.model.BillTo = responsesData.data.data.BillTo;
                self.model.ShipTo = responsesData.data.data.ShipTo;
                self.model.TaxCode = responsesData.data.data.TaxCode;
                self.model.BankAccount = responsesData.data.data.BankAccount;
                self.model.BankName = responsesData.data.data.BankName;
                self.model.Address = responsesData.data.data.Address;
                self.model.OfficePhone = responsesData.data.data.OfficePhone;
                self.model.Fax = responsesData.data.data.Fax;
                self.model.Email = responsesData.data.data.Email;
                self.model.Website = responsesData.data.data.Website;
                self.model.Note = responsesData.data.data.Note;
                self.model.isCustomer = (responsesData.data.data.isCustomer) ? true : false;
                self.model.Province.ProvinceID = responsesData.data.data.ProvinceID;
                self.model.Province.ProvinceName = responsesData.data.data.ProvinceName;
                self.model.District.DistrictID = responsesData.data.data.DistrictID;
                self.model.District.DistrictName = responsesData.data.data.DistrictName;
                self.model.Commune.CommuneID = responsesData.data.data.CommuneID;
                self.model.Commune.CommuneName = responsesData.data.data.CommuneName;
                self.model.CustomerCate = responsesData.data.data.CustomerCate;
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
            ProvinceID: (this.model.Province) ? this.model.Province.ProvinceID : null,
            ProvinceName: (this.model.Province) ? this.model.Province.ProvinceName : '',
            DistrictID: (this.model.District) ? this.model.District.DistrictID : null,
            DistrictName: (this.model.District) ? this.model.District.DistrictName : '',
            CommuneID: (this.model.Commune) ? this.model.Commune.CommuneID : null,
            CommuneName: (this.model.Commune) ? this.model.Commune.CommuneName : null,
            AccessType: this.model.AccessType,
            isCustomer: (this.model.isCustomer) ? 1 : 0,
            Note: this.model.Note
          }
        };

        if (this.model.CustomerCate && this.model.CustomerCate.length) {
          requestData.data.CustomerCate = this.model.CustomerCate;
        }

        // edit user
        if (this.idParams) {
          requestData.data.CustomerID = this.idParams;
          requestData.url = UpdateApi + '/' + this.idParams;
        }

        if (!_.isEmpty(this.itemCopy)) {
          requestData.data.CopyID = this.itemCopy.data.data.CustomerID;
        }

        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            let itemsArray = [];
            if (self.$route.params && self.$route.params.req && self.$route.params.req.itemsArray) {
              itemsArray = self.$route.params.req.itemsArray;
              itemsArray.unshift(responsesData.data);
              self.$route.params.req.itemsArray = itemsArray;
            }

            self.$router.push({
              name: ViewRouter,
              query: self.$route.query,
              params: {id: responsesData.data.CustomerID, req: self.$route.params.req, message: 'Bản ghi đã được cập nhật!'}
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

      onBackToList() {
        let query = this.$route.query;
        query.isBackToList = true;
        let params = this.$route.params.req;
        this.$router.push({
          name: ListRouter,
          query: query,
          params: params
        });
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
