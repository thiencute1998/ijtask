<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Khách hàng: {{model.CustomerName}}</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i
                class="fa fa-plus"></i> Thêm
              </b-button>
              <!--                          <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="onEditClicked"><i class="fa fa-edit"></i> Sửa</b-button>-->
              <b-dropdown variant="primary" id="dropdown-actions" class="main-header-action mr-2" text="Thao tác">
                <b-dropdown-item @click="handleDeleteItem">Xóa</b-dropdown-item>
                <b-dropdown-item @click="handleCopyItem">Nhân bản</b-dropdown-item>
                <b-dropdown-item>Chia sẻ</b-dropdown-item>
                <b-dropdown-item>Chat</b-dropdown-item>
                <b-dropdown-item>Zalo</b-dropdown-item>
                <b-dropdown-item>Phân quyền</b-dropdown-item>
              </b-dropdown>
            </div>
          </b-col>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-icons">
              <div class="main-header-item-counter ml-auto mr-3" v-if="reqParams">
                <span>{{itemNo}} - {{reqParams.idsArray.length + this.reqParams.perPage * (this.reqParams.currentPage - 1)}}</span>
                /
                <span>{{reqParams.total}}</span>
              </div>
              <b-button-group id="main-header-views" class="main-header-views">
                <b-button id="tooltip-view-prev" @click="onNavigationItem('prev')" v-if="reqParams"
                          class="main-header-view"><i class="fa fa-angle-left"></i></b-button>
                <b-button id="tooltip-view-next" @click="onNavigationItem('next')" v-if="reqParams"
                          class="main-header-view"><i class="fa fa-angle-right"></i></b-button>
                <b-button id="tooltip-view-back-to-list" @click="onBackToList" class="main-header-view"><i
                  class="fa fa-list"></i></b-button>
                <b-tooltip container="main-header-views" target="tooltip-view-back-to-list">Danh sách</b-tooltip>
                <b-tooltip container="main-header-views" target="tooltip-view-prev">Trước</b-tooltip>
                <b-tooltip container="main-header-views" target="tooltip-view-next">Sau</b-tooltip>
              </b-button-group>
              <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen="true"/>
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
            <div class="form-body">
              <div class="form-group row ij-line-head" style="margin-top: 0px !important;">
                <label class="col-md-4 m-0">Thông tin chung</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <CustomerGeneral v-model="model" :title="'Khách hàng'" :per="CustomerPer"></CustomerGeneral>
                    <a @click="showGeneralInfo = !showGeneralInfo" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showGeneralInfo"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showGeneralInfo"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2 ij-content-view" v-model="showGeneralInfo">
                <CustomerGeneralContent v-model="model">
                </CustomerGeneralContent>
              </b-collapse>

              <!-- per -->
              <div class="form-group row ij-line-head">
                <label class="col-md-4 m-0">Phân quyền</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <CustomerPerModal v-model="Customer" :title="'Phân quyền: ' + Customer.CustomerName" @changed="changedCustomerPer" :per="CustomerPer" :EmployeeOption="EmployeeOption" :CompanyOption="CompanyOption" :GroupOption="GroupOption"></CustomerPerModal>
                    <a @click="showCustomerPer = !showCustomerPer" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCustomerPer"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showCustomerPer"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showCustomerPer">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <CustomerPerView v-model="Customer" :per="CustomerPer" :EmployeeOption="EmployeeOptionArr">
                    </CustomerPerView>
                  </div>
                </div>
              </b-collapse>
              <!-- per -->

              <div class="form-group row ij-line-head">
                <label class="col-md-4 m-0">Liên hệ khách hàng</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <CustomerContact v-model="CustomerContact" :title="'Liên hệ khách hàng'" :Customer="Customer"
                                     :isNew="true" :isForm="true">
                    </CustomerContact>
                    <a @click="showCustomerContact = !showCustomerContact" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCustomerContact"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showCustomerContact"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showCustomerContact">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <CustomerContactContent v-model="CustomerContact" :Customer="Customer">
                    </CustomerContactContent>
                  </div>
                </div>
              </b-collapse>

              <div class="form-group row ij-line-head">
                <label class="col-md-6 m-0">Liên kết khách hàng</label>
                <div class="col-md-18 float-right">
                  <div class="float-right">
                    <CustomerLink v-model="CustomerLink" :title="'Liên kết khách hàng'" :Customer="Customer"
                                  :per="CustomerPerLink">
                    </CustomerLink>
                    <a @click="showCustomerLink = !showCustomerLink" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCustomerLink"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showCustomerLink"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showCustomerLink">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <CustomerLinkContent v-model="CustomerLink" :per="CustomerPerLink">
                    </CustomerLinkContent>
                  </div>
                </div>
              </b-collapse>

              <div class="form-group row ij-line-head">
              <label class="col-md-4 m-0">Giao dịch bán hàng </label>
              <div class="col-md-20 float-right">
                <div class="float-right">
                  <CustomerSalesTransForm v-model="CustomerSalesTrans" :title="'Giao dịch bán hàng'" :addnew="true" :Customer="Customer"
                                          :per="CustomerPerSalesTrans" :CustomerSalesTransCate="CustomerSalesTransCate" :StatusOption="StatusOption" :StatusValueOption="StatusValueOption">
                  </CustomerSalesTransForm>
                  <a @click="showCustomerSalesTrans = !showCustomerSalesTrans" class="ij-a-icon">
                    <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCustomerSalesTrans"
                       title="Thu gọn"></i>
                    <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showCustomerSalesTrans"
                       title="Mở rộng"></i>
                  </a>
                </div>
              </div>
            </div>
              <b-collapse class="mt-2" v-model="showCustomerSalesTrans">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <CustomerSalesTransContent v-model="CustomerSalesTrans" :per="CustomerPerSalesTrans" :StatusOption="StatusOption" :StatusValueOption="StatusValueOption" :Customer="Customer" :CustomerSalesTransCate="CustomerSalesTransCate">
                    </CustomerSalesTransContent>
                  </div>
                </div>
              </b-collapse>

              <div class="form-group row ij-line-head">
                <label class="col-md-8 m-0">Giao dịch Hợp đồng bán hàng </label>
                <div class="col-md-16 float-right">
                  <div class="float-right">
                    <CustomerContractTransForm v-model="CustomerContractTrans" :title="'Giao dịch Hợp đồng bán hàng'" :addnew="true" :Customer="Customer"
                                            :per="CustomerPerContractTrans" :CustomerContractTransCate="CustomerContractTransCate" :StatusOption="StatusOption" :StatusValueOption="StatusValueOption">
                    </CustomerContractTransForm>
                    <a @click="showCustomerContractTrans = !showCustomerContractTrans" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCustomerContractTrans"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showCustomerContractTrans"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showCustomerContractTrans">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <CustomerContractTransContent v-model="CustomerContractTrans" :per="CustomerPerContractTrans" :StatusOption="StatusOption" :StatusValueOption="StatusValueOption" :Customer="Customer" :CustomerContractTransCate="CustomerContractTransCate">
                    </CustomerContractTransContent>
                  </div>
                </div>
              </b-collapse>

<!--              <div class="form-group row ij-line-head">-->
<!--                <label class="col-md-4 m-0">Báo giá bán hàng </label>-->
<!--                <div class="col-md-20 float-right">-->
<!--                  <div class="float-right">-->
<!--                    <CustomerQuotationTransForm v-model="CustomerQuotationTrans" :title="'Báo giá bán hàng'" :addnew="true" :Customer="Customer"-->
<!--                                            :per="CustomerPerQuotationTrans" :CustomerQuotationTransCate="CustomerQuotationTransCate" :StatusOption="StatusOption">-->
<!--                    </CustomerQuotationTransForm>-->
<!--                    <a @click="showCustomerQuotationTrans = !showCustomerQuotationTrans" class="ij-a-icon">-->
<!--                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCustomerQuotationTrans"-->
<!--                         title="Thu gọn"></i>-->
<!--                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showCustomerQuotationTrans"-->
<!--                         title="Mở rộng"></i>-->
<!--                    </a>-->
<!--                  </div>-->
<!--                </div>-->
<!--              </div>-->
<!--              <b-collapse class="mt-2" v-model="showCustomerQuotationTrans">-->
<!--                <div class="form-group row">-->
<!--                  <div class="col-md-24 m-0">-->
<!--                    <CustomerQuotationTransContent v-model="CustomerQuotationTrans" :per="CustomerPerQuotationTrans" :StatusOption="StatusOption" :Customer="Customer" :CustomerQuotationTransCate="CustomerQuotationTransCate">-->
<!--                    </CustomerQuotationTransContent>-->
<!--                  </div>-->
<!--                </div>-->
<!--              </b-collapse>-->
              <div class="form-group row ij-line-head">
                <label class="col-md-4 m-0">Giao dịch hỗ trợ </label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <CustomerSupportTransForm v-model="CustomerSupportTrans" :title="'Giao dịch hỗ trợ'" :addnew="true" :Customer="Customer"
                                            :per="CustomerPerSupportTrans" :CustomerSupportTransCate="CustomerSupportTransCate" :StatusOption="StatusOption" :StatusValueOption="StatusValueOption">
                    </CustomerSupportTransForm>
                    <a @click="showCustomerSupportTrans = !showCustomerSupportTrans" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCustomerSupportTrans"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showCustomerSupportTrans"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showCustomerSupportTrans">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <CustomerSupportTransContent v-model="CustomerSupportTrans" :per="CustomerPerSupportTrans" :StatusOption="StatusOption" :StatusValueOption="StatusValueOption" :Customer="Customer" :CustomerSupportTransCate="CustomerSupportTransCate">
                    </CustomerSupportTransContent>
                  </div>
                </div>
              </b-collapse>

              <!--File-->
              <div class="form-group row ij-line-head">
                <label class="col-md-4 m-0">Tệp</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <span @click="downloadAllFile" style="font-size: 18px; cursor: pointer" title="Tải tất cả tệp"><i class="fa fa-download"></i></span>
                    <IjcoreUploadMultipleFile v-on:changed="changeFile" :isIcon="true"></IjcoreUploadMultipleFile>
                    <a @click="showCustomerFile = !showCustomerFile" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCustomerFile"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showCustomerFile"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showCustomerFile">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <CustomerFileView v-model="CustomerFile" :Customer="Customer">
                    </CustomerFileView>
                  </div>
                </div>
              </b-collapse>
              <!--File-->

              <div class="form-group row ij-line-head">
                <label class="col-md-4 m-0">Phim</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">

                    <IjcoreUploadMultipleVideo v-on:changed="changeVideo" :isIcon="true"></IjcoreUploadMultipleVideo>
                    <a @click="showCustomerVideo = !showCustomerVideo" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCustomerVideo"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showCustomerVideo"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showCustomerVideo">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <CustomerVideoView v-model="CustomerVideo" :Customer="Customer">
                    </CustomerVideoView>
                  </div>
                </div>
              </b-collapse>

            </div>

          </b-card>
        </div>
      </vue-perfect-scrollbar>
    </div>
  </div>
</template>

<script>
  import ApiService from '@/services/api.service';
  import CustomerGeneral from "./CustomerGeneral";
  import CustomerGeneralContent from "./partials/CustomerGeneralContent";
  import CustomerContact from "./CustomerContact";
  import CustomerContactContent from "./partials/CustomerContactContent";
  import CustomerLink from "./CustomerLink";
  import CustomerFileView from "./partials/CustomerFileView";
  import CustomerVideoView from "./partials/CustomerVideoView";
  import CustomerLinkContent from "./partials/CustomerLinkContent";
  import CustomerSalesTransForm from "./partials/CustomerSalesTransForm";
  import CustomerSalesTransContent from "./partials/CustomerSalesTransContent";
  import CustomerQuotationTransForm from "./partials/CustomerQuotationTransForm";
  import CustomerQuotationTransContent from "./partials/CustomerQuotationTransContent";
  import CustomerContractTransForm from "./partials/CustomerContractTransForm";
  import CustomerContractTransContent from "./partials/CustomerContractTransContent";
  import CustomerSupportTransForm from "./partials/CustomerSupportTransForm";
  import CustomerSupportTransContent from "./partials/CustomerSupportTransContent";
  import IjcoreUploadMultipleFile from "../../../components/IjcoreUploadMultipleFile";
  import IjcoreUploadMultipleVideo from "../../../components/IjcoreUploadMultipleVideo";
  import CustomerPerModal from "./partials/CustomerPerModal";
  import CustomerPerView from "./partials/CustomerPerView";

  const ListRouter = 'customer-customer';
  const EditRouter = 'customer-customer-edit';
  const CreateRouter = 'customer-customer-create';
  const DetailApi = 'customer/api/customer/view';
  const ListApi = 'customer/api/customer';
  const DeleteApi = 'customer/api/customer/delete';

  export default {
    name: 'customer-view-Customer',
    data() {
      return {
        showGeneralInfo: true,
        showCustomerContact: false,
        showCustomerLink: false,
        showCustomerFile: false,
        showCustomerVideo: false,
        showCustomerPer: false,
        showCustomerSalesTrans: false,
        showCustomerSupportTrans: false,
        showCustomerQuotationTrans: false,
        showCustomerContractTrans: false,
        idParams: this.$route.params.id,
        reqParams: this.$route.params.req,
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
          AuthorizedPerson: null
        },
        CustomerContact: [],
        CustomerLink: [],
        CustomerSalesTrans: [],
        CustomerQuotationTrans: [],
        CustomerSupportTrans: [],
        CustomerContractTrans: [],
        StatusOption: [],
        StatusValueOption: [],
        CustomerSalesTransCate: {},
        CustomerQuotationTransCate: {},
        CustomerSupportTransCate: {},
        CustomerContractTransCate: {},
        Customer: {},
        CustomerPer: [],
        defaultModel: [],
        CustomerPerLink: [],
        CustomerPerSalesTrans: [],
        CustomerPerQuotationTrans: [],
        CustomerPerSupportTrans: [],
        CustomerPerContractTrans: [],

        CustomerFile: [],
        CustomerVideo: [],

        EmployeeOption: [],
        CompanyOption: [],
        GroupOption: [],
        EmployeeOptionArr: [],

        stage: {
          updatedData: false,
          message: (this.$route.params.message) ? this.$route.params.message : ''
        }
      }
    },

    components: {
      CustomerGeneralContent,
      CustomerGeneral,
      CustomerContact,
      CustomerContactContent,
      CustomerLink,
      CustomerLinkContent,
      CustomerSalesTransForm,
      CustomerSalesTransContent,
      CustomerQuotationTransForm,
      CustomerQuotationTransContent,
      CustomerContractTransForm,
      CustomerContractTransContent,
      CustomerSupportTransForm,
      CustomerSupportTransContent,
      IjcoreUploadMultipleFile,
      CustomerFileView,
      CustomerVideoView,
      IjcoreUploadMultipleVideo,
      CustomerPerModal,
      CustomerPerView
    },
    beforeCreate() {
      if (!this.$route.params.id) {
        this.$router.push({name: ListRouter});
      }
    },
    mounted() {
      this.fetchData();
      // hiển thị thông báo
      if (this.stage.message && this.stage.message !== '') {
        this.$bvToast.toast(this.stage.message, {
          title: 'Thông báo',
          variant: 'success',
          solid: true
        });
      }
    },
    updated() {
      this.stage.updatedData = true;
    },
    computed: {
      itemNo() {
        let index = 0;
        index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
        return index;
      }
    },
    methods: {
      fetchData() {
        if (this.idParams == 0 || _.isUndefined(this.idParams)) {
          return false;
        }
        let self = this;
        let urlApi = '';
        let requestData = {
          method: 'get',
        };
        // Api edit user
        if (this.idParams) {
          urlApi = DetailApi + '/' + this.idParams;
          let data = {
            id: this.idParams
          };
          requestData.data = data;
        }
        requestData.url = urlApi;
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          self.defaultModel = responsesData;
          if (responsesData.status === 1) {
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
            self.model.IsVendor = (responsesData.data.IsVendor) ? true : false;
            self.model.inactive = (responsesData.data.Inactive) ? true : false;
            self.model.AuthorizedPerson = responsesData.data.AuthorizedPerson;

            self.Customer = responsesData.data;
            self.CustomerContact = responsesData.data.CustomerContact;
            self.CustomerLink = responsesData.data.CustomerLink;
            self.CustomerSalesTrans = responsesData.data.CustomerSalesTrans;
            self.CustomerSupportTrans = responsesData.data.CustomerSupportTrans;
            self.CustomerContractTrans = responsesData.data.CustomerContractTrans;
            self.CustomerFile = responsesData.CustomerFile;
            _.forEach(self.CustomerFile, function (item, key) {
              self.CustomerFile[key].DateModified = __.convertDateTimeToString(item.DateModified);
              self.CustomerFile[key].changeFile = 0;
              self.CustomerFile[key].changeData = 0;
            });
            self.CustomerVideo = responsesData.CustomerVideo;
            _.forEach(self.CustomerVideo, function (item, key) {
              self.CustomerVideo[key].DateModified = __.convertDateTimeToString(item.DateModified);
            });

            //Employee
            self.EmployeeOption = [];
            self.EmployeeOptionArr = [];
            _.forEach(responsesData.Employee, function (val, key) {
              self.EmployeeOptionArr[val.EmployeeID] = val.EmployeeName;
              let tmpObj = {};
              tmpObj.id = val.EmployeeID;
              tmpObj.text = val.EmployeeName;
              self.EmployeeOption.push(tmpObj);
            });
            //Company
            self.CompanyOption = [];
            _.forEach(responsesData.Company, function (val, key) {
              let tmpObj = {};
              tmpObj.id = val.CompanyID;
              tmpObj.text = val.CompanyName;
              self.CompanyOption.push(tmpObj);
            });
            //Group
            self.GroupOption = [];
            _.forEach(responsesData.Group, function (val, key) {
              let tmpObj = {};
              tmpObj.id = val.UserGroupID;
              tmpObj.text = val.UserGroupName;
              self.GroupOption.push(tmpObj);
            });
            _.forEach(responsesData.CustomerPer, function (val, key) {
              if(responsesData.CustomerPer[key].Access === 1){
                responsesData.CustomerPer[key].Access = true;
              }else{
                responsesData.CustomerPer[key].Access = false;
              }
              if(responsesData.CustomerPer[key].Edit === 1){
                responsesData.CustomerPer[key].Edit = true;
              }else{
                responsesData.CustomerPer[key].Edit = false;
              }
              if(responsesData.CustomerPer[key].Delete === 1){
                responsesData.CustomerPer[key].Delete = true;
              }else{
                responsesData.CustomerPer[key].Delete = false;
              }
              if(responsesData.CustomerPer[key].Create === 1){
                responsesData.CustomerPer[key].Create = true;
              }else{
                responsesData.CustomerPer[key].Create = false;
              }
            });
            self.CustomerPer = responsesData.CustomerPer;

            // self.StatusOption = responsesData.data.StatusOption;
            self.CustomerSalesTransCate = responsesData.data.CustomerSalesTransCate;
            self.CustomerSupportTransCate = responsesData.data.CustomerSupportTransCate;
            self.CustomerContractTransCate = responsesData.data.CustomerContractTransCate;

            // self.StatusOption = [];
            // _.forEach(responsesData.data.StatusOption, function (value, key) {
            //   let tmpObj = {};
            //   tmpObj.value = value.StatusName;
            //   tmpObj.text = value.StatusName;
            //   self.StatusOption.push(tmpObj);
            // });

            if (_.isArray(responsesData.data.Status)) {
              self.StatusOption = [];
              _.forEach(responsesData.data.Status, function (status, key) {
                let tmpObj = {};
                tmpObj.value = status.StatusID;
                tmpObj.text = status.StatusName;
                self.StatusOption.push(tmpObj);
              });

              // TODO: set default option for status
              if (!self.StatusID && self.StatusOption[0]) {
                self.StatusID = self.StatusOption[0].value;
              }
            }

            if (responsesData.data.StatusItem) {
              self.StatusValueOption = [];
              _.forEach(responsesData.data.StatusItem, function (statusItem, key) {
                let tmpObj = {};
                tmpObj.value = statusItem.StatusValue;
                tmpObj.text = statusItem.StatusDescription;
                tmpObj.StatusID = statusItem.StatusID;
                self.StatusValueOption.push(tmpObj);
              });
              //self.onChangeStatus();
            }
            ///

          }else if (responsesData.status === 3) {
            self.$router.push({name: ListRouter, params: {message: 'Bạn không có quyền truy cập!'}});
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
        } else if (newIndex < 0 && this.reqParams.currentPage > 1) {
          this.reqParams.currentPage = this.reqParams.currentPage - 1;
          this.getItemIds(type);
        } else {
          this.idParams = (this.reqParams.idsArray[newIndex]) ? this.reqParams.idsArray[newIndex] : this.idParams;
        }
      },

      getItemIds(type) {
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

      onEditClicked() {
        this.$router.push({
          name: EditRouter,
          params: {id: this.idParams, req: this.reqParams}
        });
      },
      handleCopyItem() {
        this.$router.push({name: CreateRouter, params: {itemCopy: this.defaultModel}});
      },
      onCreateClicked() {
        this.$router.push({name: CreateRouter});
      },
      onBackToList(message = '') {
        if (_.isString(message)) {
          this.$router.push({name: ListRouter, params: {message: message}});
        } else {
          this.$router.push({name: ListRouter});
        }

      },
      handleDeleteItem() {
        let self = this;
        let title = 'Bạn có muốn xóa bản ghi?';
        Swal.fire({
          title: title,
          text: 'Bạn sẽ không thể khôi phục thông tin bản ghi!',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Đồng ý',
          cancelButtonText: 'Hủy bỏ'
        }).then((result) => {
          if (result.value) {
            let requestData = {
              method: 'post',
              url: DeleteApi + '/' + self.idParams,
              data: {
                array_id: [self.idParams],
              },
            };

            ApiService.setHeader();
            ApiService.customRequest(requestData).then((response) => {
              let responseData = response.data;
              if (responseData.status === 1) {
                self.onBackToList('Bản ghi đã được xóa');
              } else {
                Swal.fire(
                  'Có lỗi',
                  '',
                  'error'
                );
                console.log(response);
              }
            }, (error) => {
              console.log(error);
            });
          }
        });
      },
      updateModel() {
        if (this.stage.updatedData) {
          this.$forceUpdate();
        }
      },
      downloadAllFile(){
        let self = this;
        let requestData = {
          url: 'customer/api/customer/download-all-file/' + this.Customer.CustomerID,
          method: 'get',
          data: {}
        };

        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          self.$store.commit('isLoading', false);
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            let link = document.createElement('a');
            link.href = self.$store.state.appRootApi + responsesData.data;
            link.download = 'Archive.zip';
            link.click();
          } else {
            self.$bvToast.toast(responsesData.msg, {
              title: 'Thông báo',
              variant: 'warning',
              solid: true
            });
          }
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      changeFile(files) {
        let self = this;
        let dateC = __.convertDateTimeToString(new Date());
        for (let i = 0; i < files.length; i++) {
          self.$store.commit('isLoading', true);
          let file = files[i];
          let formData = new FormData();
          formData.append('LineID', '');
          formData.append('FileUpload', file);
          formData.append('DocID', self.DocID);
          formData.append('FileName', file.name);
          formData.append('Description', file.name);
          formData.append('FileType', file.name.split('.').pop());
          formData.append('FileSize', file.size);
          formData.append('DateModified', dateC);
          formData.append('UserModified', '');
          formData.append('changeFile', 1);
          formData.append('changeData', 1);

          let currentObj = this;
          const config = {
            headers: {
              'content-type': 'multipart/form-data',
            }
          };

          // send upload request
          axios.post('customer/api/customer/customer-upload-file/' + self.Customer.CustomerID, formData, config)
            .then(function (response) {

              let responseData = response.data;
              if (responseData.status === 1) {
                currentObj.success = response.data.success;
                currentObj.filename = "";
                let dataR = response.data.data;
                self.CustomerFile.push({
                  LineID: dataR.LineID,
                  FileUpload: file,
                  DocID: dataR.DocID,
                  FileID: dataR.FileID,
                  FileName: dataR.FileName,
                  Description: dataR.FileName,
                  FileType: dataR.FileType,
                  FileSize: dataR.FileSize,
                  DateModified: dateC,
                  UserModified: dataR.UserModified,
                  Link: dataR.Link,
                  DateModifiedRoot: '',
                  FileNameRoot: '',
                  changeFile: 0,//Đã thay đổi file
                  changeData: 0
                });

                self.$bvToast.toast('Tải lên thành công', {
                  title: 'Thông báo',
                  variant: 'success',
                  solid: true
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Thông báo',
                  text: responseData.msg,
                  confirmButtonText: 'Đóng'
                });
              }
              self.$store.commit('isLoading', false);
            })
            .catch(function (error) {
              // currentObj.output = error;
            });
        }
        this.showCustomerFile = true;
      },
      changeVideo(videos) {
        let self = this;
        let dateC = __.convertDateTimeToString(new Date());
        for (let i = 0; i < videos.length; i++) {
          self.$store.commit('isLoading', true);
          let video = videos[i];
          let formData = new FormData();
          formData.append('LineID', '');
          formData.append('VideoUpload', video);
          formData.append('DocID', self.DocID);
          formData.append('VideoName', video.name);
          formData.append('Description', video.name);
          formData.append('VideoType', video.name.split('.').pop());
          formData.append('VideoSize', video.size);
          formData.append('DateModified', dateC);
          formData.append('UserModified', '');
          formData.append('changeVideo', 1);
          formData.append('changeData', 1);

          let currentObj = this;
          const config = {
            headers: {
              'content-type': 'multipart/form-data',
            }
          };

          // send upload request
          axios.post('customer/api/customer/customer-upload-video/' + self.Customer.CustomerID, formData, config)
            .then(function (response) {
              let responseData = response.data;
              if (responseData.status === 1) {
                currentObj.success = response.data.success;
                currentObj.videoname = '';
                let dataR = response.data.data;
                self.CustomerVideo.push({
                  LineID: dataR.LineID,
                  VideoUpload: video,
                  DocID: dataR.DocID,
                  FileID: dataR.FileID,
                  VideoName: dataR.VideoName,
                  Description: dataR.VideoName,
                  VideoType: dataR.VideoType,
                  VideoSize: dataR.VideoSize,
                  DateModified: dateC,
                  UserModified: dataR.UserModified,
                  Link: dataR.Link,
                  DateModifiedRoot: '',
                  FileNameRoot: '',
                  changeVideo: 0,//Đã thay đổi file
                  changeData: 0
                });
                self.$bvToast.toast('Tải lên thành công', {
                  title: 'Thông báo',
                  variant: 'success',
                  solid: true
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Thông báo',
                  text: responseData.msg,
                  confirmButtonText: 'Đóng'
                });
              }
              self.$store.commit('isLoading', false);
            })
            .catch(function (error) {
              console.log(error);
              self.$store.commit('isLoading', false);
            });
        }
        this.showCustomerVideo = true;
      },
      changedCustomerPer() {
        this.showCustomerPer = true;
      },

    },
    watch: {
      idParams() {
        this.fetchData();
      }
    }
  }
</script>

<style lang="css">
  .ij-icon {
    font-size: 18px;
    padding-left: 10px;
  }

  .ij-a-icon:hover {
    cursor: pointer;
  }

  .ij-content-view {
    margin-bottom: 10px;
  }

  .ij-content-view .form-group {
    margin-bottom: 3px;
  }

  .ij-line-head {
    border-bottom: 1px solid #e6e2e2;
    margin-left: 0px;
    margin-right: 0px;
  }

  .ij-line-head label {
    padding-left: 0px !important;
    padding-right: 0px !important;
    font-size: 16px;
  }

  .not-border th, .not-border td {
    border: none;
  }

  .sss {
    max-width: none !important;
    white-space: nowrap;
    display: table-caption;
  }

  .right-absolute {
    position: absolute;
    right: 35px;
  }

  .float-right {
    padding-right: 0;
  }

  .evaluation-content .datepicker-fill-content {
    width: 120px !important;
  }

  .datepicker-fill-content .mx-input-wrapper {
    width: 120px !important;
  }
</style>
