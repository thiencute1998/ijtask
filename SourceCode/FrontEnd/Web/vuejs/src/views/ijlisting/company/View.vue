<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span><i class="icon-eye icon mr-2"></i> Cơ quan chủ quản: {{Company.CompanyName}}</span>
                        </div>
                    </b-col>
                    <b-col class="col-md-6"></b-col>
                </b-row>
                <b-row>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-actions">
                            <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i class="fa fa-plus"></i> Thêm</b-button>
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
<!--                                <span>{{itemNo}} - {{itemTotalPerPage}}</span>-->
<!--                                /-->
<!--                                <span>{{itemTotal}}</span>-->
                              <span>{{itemNo}} - {{itemTotal}}</span>
                            </div>
                            <b-button-group id="main-header-views" class="main-header-views">
                                <b-button id="tooltip-view-prev" @click="onNavigationItem('prev')" v-if="reqParams" class="main-header-view"><i class="fa fa-angle-left"></i></b-button>
                                <b-button id="tooltip-view-next" @click="onNavigationItem('next')" v-if="reqParams" class="main-header-view"><i class="fa fa-angle-right"></i></b-button>
                                <b-button id="tooltip-view-back-to-list" @click="onBackToList" class="main-header-view"><i class="fa fa-list"></i></b-button>
                                <b-tooltip container="main-header-views" target="tooltip-view-back-to-list">Danh sách</b-tooltip>
                                <b-tooltip container="main-header-views" target="tooltip-view-prev">Trước</b-tooltip>
                                <b-tooltip container="main-header-views" target="tooltip-view-next">Sau</b-tooltip>
                            </b-button-group>
                            <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
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
                      <div class="form-group row ij-line-head">
                        <label class="col-md-4">Thông tin chung</label>
                        <div class="col-md-20">
                          <div class="ij-icon-popup float-right">
                            <company-general-modal v-model="Company" :title="'Cơ quan chủ quản : ' + Company.CompanyName" @changeDefaultModel="changeItemCopy" :Fin="Fin" :Tre="Tre"></company-general-modal>
                            <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                            </a>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showGeneralInfo">
                        <company-general-view v-model="Company" :Fin="Fin" :Tre="Tre"></company-general-view>
                      </b-collapse>
                      <div class="form-group row ij-line-head" v-if="Company.AccessType != 3">
                        <label class="col-md-4">Phân quyền</label>
                        <div class="col-md-20">
                          <div class="ij-icon-popup float-right">
                            <div class="float-right">
                              <company-per-modal v-model="Company" :title="'Phân quyền: ' + Company.CompanyName" @changed="fetchData" :per="CompanyPer" :EmployeeOption="EmployeeOption" :CompanyOption="CompanyOption" :GroupOption="GroupOption"></company-per-modal>
                              <a @click="showCompanyPer = !showCompanyPer" class="ij-a-icon">
                                <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCompanyPer"
                                   title="Thu gọn"></i>
                                <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                                   title="Mở rộng"></i>
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showCompanyPer" v-if="Company.AccessType != 3">
                        <div class="form-group row">
                          <div class="col-md-24 m-0">
                            <company-per-view v-model="Company" :per="CompanyPer" :EmployeeOption="EmployeeOptionArr"></company-per-view>
                          </div>
                        </div>
                      </b-collapse>
                      <div class="form-group row ij-line-head">
                        <label class="col-md-4" @click="onToggleCompanyLink">Danh mục liên kết</label>
                        <div class="col-md-20">
                          <div class="ij-icon-popup float-right">
                            <CompanyLinkModal @on:get-data="onToggleCompanyLink(false)" v-model="CompanyLink" :title="'Danh mục liên kết'" :Company="Company"></CompanyLinkModal>
                            <a class="ij-a-icon" @click="onToggleCompanyLink">
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showCompanyLink"></i>
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                            </a>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showCompanyLink">
                        <CompanyLinkContent v-model="CompanyLink"></CompanyLinkContent>
                      </b-collapse>
                      <div class="form-group row ij-line-head">
                        <label class="col-md-4" @click="onToggleCompanyFile">Tệp</label>
                        <div class="col-md-20">
                          <div class="float-right">
                            <span @click="downloadAllFile" style="font-size: 18px; cursor: pointer" title="Tải tất cả tệp"><i class="fa fa-download"></i></span>
                            <ijcore-upload-multiple-file v-on:changed="changeFile" :isIcon="true"></ijcore-upload-multiple-file>
                              <a @click="onToggleCompanyFile" class="ij-a-icon">
                                <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCompanyFile"
                                   title="Thu gọn"></i>
                                <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                                   title="Mở rộng"></i>
                              </a>
                          </div>

                        </div>
                      </div>
                      <b-collapse v-model="showCompanyFile">
                        <div class="form-group row">
                          <div class="col-md-24 m-0">
                            <company-file-view v-model="CompanyFile" :Company="Company"></company-file-view>
                          </div>
                        </div>
                      </b-collapse>
                      <div class="form-group row ij-line-head">
                        <label class="col-md-4" @click="onToggleCompanyVideo">Phim</label>
                        <div class="col-md-20">
                          <div class="ij-icon-popup float-right">
                            <div class="float-right">
                              <IjcoreUploadMultipleVideo v-on:changed="changeVideo" :isIcon="true"></IjcoreUploadMultipleVideo>
                              <a @click="onToggleCompanyVideo" class="ij-a-icon">
                                <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCompanyVideo"
                                   title="Thu gọn"></i>
                                <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                                   title="Mở rộng"></i>
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showCompanyVideo">
                        <div class="form-group row">
                          <div class="col-md-24 m-0">
                            <CompanyVideoView v-model="CompanyVideo" :Company="Company"></CompanyVideoView>
                          </div>
                        </div>
                      </b-collapse>
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
    import CompanyGeneralView from "./partials/CompanyGeneralView";
    import CompanyGeneralModal from "./partials/CompanyGeneralModal";
    import CompanyLinkModal from "./partials/CompanyLinkModal";
    import CompanyLinkContent from "./partials/CompanyLinkContent";
    import IjcoreUploadMultipleFile from "../../../components/IjcoreUploadMultipleFile";
    import CompanyFileView from "./partials/CompanyFileView";
    import IjcoreUploadMultipleVideo from "@/components/IjcoreUploadMultipleVideo";
    import CompanyVideoView from "./partials/CompanyVideoView";
    import CompanyPerView from "./partials/CompanyPerView";
    import CompanyPerModal from "./partials/CompanyPerModal";

    const ListRouter = 'listing-company';
    const EditRouter = 'listing-company-edit';
    const CreateRouter = 'listing-company-create';
    const ViewApi = 'listing/api/company/view';
    const ListApi = 'listing/api/company';
    const DeleteApi = 'listing/api/company/delete';
    const UpdateApi = 'listing/api/company/update';

    export default {
        name: 'listing-view-item',
        data () {
            return {
              showGeneralInfo: true,
              showCompanyLink: false,
              showCompanyFile: false,
              showCompanyVideo: false,
              showCompanyPer: false,
              Company: {
                CompanyID: null,
                CompanyNo: '',
                CompanyName: '',
                Address: '',
                Tel: '',
                Fax: '',
                Email: '',
                EmployeeID: null,
                ContactName: '',
                ContactTel: '',
                SbiChapterID: null,
                SbiChapterNo: '',
                Note: '',
                Detail:'',
                Prefix: '',
                Suffix: '',
                ParentID: null,
                ParentNo: '',
                ParentName: '',
                Province: {},
                District: {},
                Commune: {},
                Inactive: false,
                AccessType: null,
                UserIDCreated: null,
                AuthorizedPerson: null,
                IsFinancialCompany: false,
                CompanyCate: [],
                CompanyCateList: [],
                CompanyCateValue: [],
                ManagementLevel: null,
                SumCompanyType: 1,
                isAutOrg : false,
                AutOrgID : null,
                AutOrgNo : '',
                AutOrgName : '',
                AutOrgChapterID: null,
                AutOrgChapterNo: '',
                AutOrgAddress: '',
                AutOrgContactName: '',
                AutOrgContactPosition: '',
                AutOrgContactOfficePhone:'',
                AutOrgContactHandPhone: '',
                AutOrgContactMail: '',
                isFinOrg: false,
                FinMofID: null,
                FinMofNo: '',
                FinMofName: '',
                FinDofID: null,
                FinDofNo: '',
                FinDofName: '',
                FinDfpID: null,
                FinDfpNo: '',
                FinDfpName: '',
                isTreOrg: false,
                TreMofID: null,
                TreMofNo: '',
                TreMofName: '',
                TreDofID: null,
                TreDofNo: '',
                TreDofName: '',
                TreDfpID: null,
                TreDfpNo: '',
                TreDfpName: '',
              },
              Tre: {
                TreID: null,
                TreName: '',
                TreNo: ''
              },
              Fin: {
                FinID: null,
                FinName: '',
                FinNo: '',
              },
              CompanyLink: [],
              CompanyFile: [],
              CompanyVideo: [],
              CompanyPer: {},
              EmployeeOption: [],
              EmployeeOptionArr: [],
              CompanyOption: [],
              GroupOption: [],
              idParams: this.$route.params.id,
              reqParams: this.$route.params.req,
              defaultModel: {},
              stage: {
                updatedData: false,
                message: (this.$route.params.message) ? this.$route.params.message : ''
              }
            }

        },

        components: {
          CompanyGeneralView,
          CompanyGeneralModal,
          CompanyLinkModal,
          CompanyLinkContent,
          IjcoreUploadMultipleFile,
          CompanyFileView,
          IjcoreUploadMultipleVideo,
          CompanyVideoView,
          CompanyPerView,
          CompanyPerModal,
        },
        beforeCreate() {

            this.$router.onReady(() => {
                if (!this.$route.params.id) {
                    this.$router.push({name: ListRouter});
                }
            });
        },
        created(){
        },
        beforeMount(){},
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
            itemNo(){
                if (!this.idParams) return;
                let index = 0;
                index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
                return index;
            },
            itemTotalPerPage(){
                if (!this.idParams) return;
                return this.reqParams.idsArray.length + this.reqParams.perPage * (this.reqParams.currentPage - 1);
            },
            itemTotal(){
                if (!this.idParams) return;
                return this.reqParams.total;
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
                if(this.idParams){
                    urlApi = ViewApi + '/' + this.idParams;
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
                        self.Company.CompanyID = responsesData.data.data.CompanyID;
                        self.Company.CompanyName = responsesData.data.data.CompanyName;
                        self.Company.CompanyNo = responsesData.data.data.CompanyNo;
                        self.Company.Address = responsesData.data.data.Address;
                        self.Company.Tel = responsesData.data.data.Tel;
                        self.Company.Fax = responsesData.data.data.Fax;
                        self.Company.Email = responsesData.data.data.Email;
                        self.Company.EmployeeID = responsesData.data.data.EmployeeID;
                        self.Company.ContactName = responsesData.data.data.ContactName;
                        self.Company.ContactTel = responsesData.data.data.ContactTel;
                        self.Company.SbiChapterID = responsesData.data.data.SbiChapterID;
                        self.Company.SbiChapterNo = responsesData.data.data.SbiChapterNo;
                        self.Company.Note = responsesData.data.data.Note;
                        self.Company.Detail = responsesData.data.data.Detail;
                        self.Company.Prefix = responsesData.data.data.Prefix;
                        self.Company.Suffix = responsesData.data.data.Suffix;
                        self.Company.Inactive = (responsesData.data.data.Inactive) ? true : false;
                        self.Company.Province.ProvinceID = responsesData.data.data.ProvinceID;
                        self.Company.Province.ProvinceName = responsesData.data.data.ProvinceName;
                        self.Company.District.DistrictID = responsesData.data.data.DistrictID;
                        self.Company.District.DistrictName = responsesData.data.data.DistrictName;
                        self.Company.Commune.CommuneID = responsesData.data.data.CommuneID;
                        self.Company.Commune.CommuneName = responsesData.data.data.CommuneName;
                        self.Company.ParentID = responsesData.data.data.ParentID;
                        self.Company.ParentNo = responsesData.data.Parent.ParentNo;
                        self.Company.ParentName = responsesData.data.Parent.ParentName;
                        self.Company.AccessType = responsesData.data.data.AccessType;
                        self.Company.UserIDCreated = responsesData.data.data.UserIDCreated;
                        self.Company.AuthorizedPerson = responsesData.data.data.AuthorizedPerson;
                        self.Company.IsFinancialCompany = responsesData.data.data.IsFinancialCompany;
                        self.Company.ManagementLevel = responsesData.data.data.ManagementLevel;
                        self.Company.CenterID = responsesData.data.data.CenterID;
                        self.Company.CenterNo = responsesData.data.data.CenterNo;
                        self.Company.CenterName = responsesData.data.data.CenterName;
                        self.Company.SumCompanyType = responsesData.data.data.SumCompanyType;
                        self.Company.isAutOrg = (responsesData.data.data.isAutOrg == 1)? true : false;
                        self.Company.AutOrgID = responsesData.data.data.AutOrgID;
                        self.Company.AutOrgNo = responsesData.data.data.AutOrgNo;
                        self.Company.AutOrgName = responsesData.data.data.AutOrgName;
                        self.Company.AutOrgContactName = responsesData.data.data.AutOrgContactName;
                        self.Company.AutOrgAddress = responsesData.data.data.AutOrgAddress;
                        self.Company.AutOrgChapterID = responsesData.data.data.AutOrgChapterID;
                        self.Company.AutOrgChapterNo = responsesData.data.data.AutOrgChapterNo;
                        self.Company.AutOrgContactPosition = responsesData.data.data.AutOrgContactPosition;
                        self.Company.AutOrgContactMail = responsesData.data.data.AutOrgContactMail;
                        self.Company.AutOrgContactOfficePhone = responsesData.data.data.AutOrgContactOfficePhone;
                        self.Company.AutOrgContactHandPhone = responsesData.data.data.AutOrgContactHandPhone;
                        self.Company.isFinOrg = (responsesData.data.data.isFinOrg == 1) ? true : false ;
                        self.Company.FinMofID = responsesData.data.data.FinMofID;
                        self.Company.FinMofNo = responsesData.data.data.FinMofNo;
                        self.Company.FinMofName = responsesData.data.data.FinMofName;
                        self.Company.FinDofID = responsesData.data.data.FinDofID;
                        self.Company.FinDofNo = responsesData.data.data.FinDofNo;
                        self.Company.FinDofName = responsesData.data.data.FinDofName;
                        self.Company.FinDfpID = responsesData.data.data.FinDfpID;
                        self.Company.FinDfpNo = responsesData.data.data.FinDfpNo;
                        self.Company.FinDfpName = responsesData.data.data.FinDfpName;
                        self.Company.isTreOrg = (responsesData.data.data.isTreOrg == 1) ? true : false;
                        self.Company.TreMofID = responsesData.data.data.TreMofID;
                        self.Company.TreMofNo = responsesData.data.data.TreMofNo;
                        self.Company.TreMofName = responsesData.data.data.TreMofName;
                        self.Company.TreDofID = responsesData.data.data.TreDofID;
                        self.Company.TreDofNo = responsesData.data.data.TreDofNo;
                        self.Company.TreDofName = responsesData.data.data.TreDofName;
                        self.Company.TreDfpID = responsesData.data.data.TreDfpID;
                        self.Company.TreDfpNo = responsesData.data.data.TreDfpNo;
                        self.Company.TreDfpName = responsesData.data.data.TreDfpName;
                        if(self.Company.ManagementLevel == 2 ){
                          self.Tre.TreID = self.Company.TreMofID;
                          self.Tre.TreNo = self.Company.TreMofNo;
                          self.Tre.TreName = self.Company.TreMofName;
                          self.Fin.FinID = self.Company.FinMofID;
                          self.Fin.FinNo = self.Company.FinMofNo;
                          self.Fin.FinName = self.Company.FinMofName;
                        }
                        if(self.Company.ManagementLevel == 3 ){
                          self.Tre.TreID = self.Company.TreDofID;
                          self.Tre.TreNo = self.Company.TreDofNo;
                          self.Tre.TreName = self.Company.TreDofName;
                          self.Fin.FinID = self.Company.FinDofID;
                          self.Fin.FinNo = self.Company.FinDofNo;
                          self.Fin.FinName = self.Company.FindofName;
                        }
                        if(self.Company.ManagementLevel == 4 ){
                          self.Tre.TreID = self.Company.TreDfpID;
                          self.Tre.TreNo = self.Company.TreDfpNo;
                          self.Tre.TreName = self.Company.TreDfpName;
                          self.Fin.FinID = self.Company.FinDfpID;
                          self.Fin.FinNo = self.Company.FinDfpNo;
                          self.Fin.FinName = self.Company.FinDfpName;
                        }
                        self.Company.CompanyCate = [];
                        self.$set(self.Company,'CompanyCate',[]);
                        if(responsesData.data.CompanyCate){
                          _.forEach(responsesData.data.CompanyCate, (companyCate, key)=>{
                            let tmpObj = {};
                            if(companyCate.CateID){
                              let cateList = _.find(responsesData.data.CompanyCateList, ['CateID', companyCate.CateID]);
                              if(cateList){
                                tmpObj.CateID = cateList.CateID;
                                tmpObj.CateName = cateList.CateName;
                              }
                            }
                            if(companyCate.CateValue){
                              // let cateValue = _.find(responsesData.data.CompanyCateValue, (cate)=> {
                              //   return cate.CateID === companyCate.CateID && cate.CateValue === companyCate.CateValue;
                              // });
                              let cateValue = _.find(responsesData.data.CompanyCateValue,{
                                CateID: companyCate.CateID,
                                CateValue: companyCate.CateValue
                              });
                              if(cateValue){
                                tmpObj.CateValue = cateValue.CateValue;
                                tmpObj.Description = cateValue.Description;
                              }
                            }
                            // self.Company.CompanyCate.push(tmpObj);
                            self.$set(self.Company.CompanyCate, self.Company.CompanyCate.length, tmpObj);
                          })
                        }

                        // Employee
                        self.EmployeeOption = [];
                        self.EmployeeOptionArr = [];
                        _.forEach(responsesData.Employee, function (val, key) {
                          if(val.EmployeeID === self.Company.AuthorizedPerson){
                            self.Company.EmployeeName = val.EmployeeName;
                          }
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
                        _.forEach(responsesData.CompanyPer, function (val, key) {
                          responsesData.CompanyPer[key].Access = (responsesData.CompanyPer[key].Access) ? true : false;
                          responsesData.CompanyPer[key].Edit = (responsesData.CompanyPer[key].Edit) ? true : false;
                          responsesData.CompanyPer[key].Delete = (responsesData.CompanyPer[key].Delete) ? true : false;
                          responsesData.CompanyPer[key].Create = (responsesData.CompanyPer[key].Create) ? true : false;
                        });
                        self.CompanyPer = responsesData.CompanyPer;
                        self.CompanyPerEmployee = responsesData.CompanyPerEmployee;

                    }
                    else if(responsesData.status === 3){
                      self.$router.push({name: ListRouter, params: {message: responsesData.msg}});
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

                if (this.reqParams.search.CompanyNo !== '') {
                    requestData.data.CompanyNo = this.reqParams.search.CompanyNo;
                }
                if (this.reqParams.search.CompanyName !== '') {
                    requestData.data.CompanyName = this.reqParams.search.CompanyName;
                }
                if (this.reqParams.search.Tel !== '') {
                    requestData.data.Tel = this.reqParams.search.Tel;
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
                            self.reqParams.idsArray.push(value.CompanyID);
                        });

                        (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                });

            },
          onCreateClicked(){
            let self = this;
            let params = (this.$route.params.req) ? this.$route.params.req:{};
            let query = this.$route.query;
            query.isBackToList = true;
            this.$router.push({
              name: CreateRouter,
              query: query,
              params: {id: self.idParams, req: params}
            });
          },

          onBackToList(message = '') {
            let params = (this.$route.params.req) ? this.$route.params.req:{};
            let query = this.$route.query;
            query.isBackToList = true;
            if (_.isString(message)) {
              params.message = message;
              this.$router.push({name: ListRouter, query: query, params: params, message: message});
            } else {
              this.$router.push({name: ListRouter, query: query, params: params});
            }
          },

          handleCopyItem(){
                this.$router.push({name: CreateRouter, params: {itemCopy: this.defaultModel}});
            },
          handleDeleteItem() {
            debugger
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
                    let index = _.findIndex(self.$route.params.req.itemsArray, {'CompanyID' : self.idParams});
                    self.$route.params.req.itemsArray.splice(index, 1);
                    let indexParent = _.findIndex(self.$route.params.req.itemsArray, {'CompanyID':self.Company.ParentID});
                    if(indexParent >= 0){
                      let child = _.filter(self.$route.params.req.itemsArray, {'ParentID': self.Company.ParentID});
                      if(child.length == 0){
                        self.$set(self.$route.params.req.itemsArray[indexParent], 'Class', '');
                        self.$set(self.$route.params.req.itemsArray[indexParent], 'Detail', 1);
                      }
                    }
                    self.onBackToList('Bản ghi đã được xóa');
                  } else  if (responseData.status === 4) {
                    Swal.fire(
                      'Lỗi',
                      'Không được xóa bản ghi Tổng hợp',
                      'error'
                    );
                  } else {
                    Swal.fire(
                      'Có lỗi',
                      '',
                      'error'
                    );

                  }
                }, (error) => {
                  console.log(error);
                  Swal.fire({
                    title: 'Thông báo',
                    text: 'Không kết nối được với máy chủ',
                    confirmButtonText: 'Đóng'
                  });

                });

              }
            });
          },

          updateModel() {
                if (this.stage.updatedData) {
                    this.$forceUpdate();
                }
            },
          onToggleCompanyLink(toggle = true){
              let self = this;
              if(!this.CompanyLink.length){
                let requestData = {
                  method: 'get',
                  url: '/listing/api/company/get-company-link/' + this.$route.params.id,
                  data: {}
                }

                self.$store.commit('isLoading',false);
                ApiService.setHeader();
                ApiService.customRequest(requestData)
                .then(response =>{
                  let responseData = response.data;
                  self.CompanyLink = responseData.data;
                })
                .catch(error=>{
                  console.log(error);

                })
              }
              if(toggle){
                this.showCompanyLink = !this.showCompanyLink;
              }
            },
          onToggleCompanyFile(toggle = true){
              let self = this;
              if(!this.CompanyFile.length){
                let requestData = {
                  method: 'get',
                  url: 'listing/api/company/get-company-file/' + this.Company.CompanyID,
                  data:{}
                }

                ApiService.setHeader();
                ApiService.customRequest(requestData)
                .then(response=> {
                  let reponseData = response.data;
                  console.log(reponseData)
                  if(reponseData.status === 1){
                    self.CompanyFile = reponseData.data;
                  }
                })
                .catch(error=>{
                  console.log(error)
                })
              }
              if (toggle) {
                this.showCompanyFile = !this.showCompanyFile;
              }
            },
          downloadAllFile(){
              let self = this;
              let requestData = {
                url: 'listing/api/company/download-all-file/' + this.Company.CompanyID,
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
              formData.append('CompanyID', self.Company.CompanyID);
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
              axios.post('listing/api/company/company-upload-file/' + self.Company.CompanyID, formData, config)
                .then(function (response) {
                  let responseData = response.data;
                  if (responseData.status === 1) {
                    currentObj.success = response.data.success;
                    currentObj.filename = "";
                    let dataR = response.data.data;
                    self.CompanyFile.push({
                      LineID: dataR.LineID,
                      FileUpload: file,
                      CompanyID: dataR.CompanyID,
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
                    self.showCompanyFile = true;
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

          },
          onToggleCompanyVideo(toggle = true) {
            let self = this;
            if (!this.CompanyVideo.length) {
              let requestData = {
                method: 'get',
                url: 'listing/api/company/get-company-video/' + this.Company.CompanyID,
                data: {}
              };
              // Api edit user
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                if (responsesData.status === 1) {
                  self.CompanyVideo = responsesData.data;
                }
                self.$store.commit('isLoading', false);
              }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
              });
            }
            if (toggle) {
              this.showCompanyVideo = !this.showCompanyVideo;
            }
          },
          changeVideo(videos) {
            let self = this;
            let dateC = __.convertDateTimeToString(new Date());
            for (var i = 0; i < videos.length; i++) {
              self.$store.commit('isLoading', true);
              var video = videos[i];
              let formData = new FormData();
              formData.append('LineID', '');
              formData.append('VideoUpload', video);
              formData.append('CompanyID', self.Company.CompanyID);
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
              axios.post('listing/api/company/company-upload-video/' + self.Company.CompanyID, formData, config)
                .then(function (response) {
                  let responseData = response.data;
                  if (responseData.status === 1) {
                    currentObj.success = response.data.success;
                    currentObj.videoname = "";
                    let dataR = response.data.data;
                    self.CompanyVideo.push({
                      LineID: dataR.LineID,
                      VideoUpload: video,
                      CompanyID: dataR.CompanyID,
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
                    self.showCompanyVideo = true;
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

          },
          changeItemCopy(result){
            this.defaultModel.data.data = result.data;
          }
        },
        watch: {
            // idParams() {
            //     this.fetchData();
            // },
            '$route.params.id': function (id) {
              this.idParams = id;
              this.fetchData();
            },
        },
        // beforeDestroy(){
        //     window.removeEventListener('unload', this.onReloadPage)
        // }
    }
</script>

<style lang="css"></style>
