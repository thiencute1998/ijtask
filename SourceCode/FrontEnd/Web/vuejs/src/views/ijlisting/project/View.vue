<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Dự án: {{Project.ProjectName}}</span>
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
                  <project-general-modal v-model="Project" :title="'Dự án : ' + Project.ProjectName" @changeDefaultModel="changeItemCopy"></project-general-modal>
                  <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showGeneralInfo">
              <project-general-view v-model="Project" ></project-general-view>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4">Phân quyền</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <div class="float-right">
                    <project-per-modal v-model="Project" :title="'Phân quyền: ' + Project.ProjectName" @changed="fetchData" :per="ProjectPer" :EmployeeOption="EmployeeOption" :CompanyOption="CompanyOption" :GroupOption="GroupOption"></project-per-modal>
                    <a @click="showProjectPer = !showProjectPer" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showProjectPer"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <b-collapse v-model="showProjectPer">
              <div class="form-group row">
                <div class="col-md-24 m-0">
                  <project-per-view v-model="Project" :per="ProjectPer" :EmployeeOption="EmployeeOptionArr"></project-per-view>
                </div>
              </div>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleProjectLink">Danh mục liên kết</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <ProjectLinkModal @on:get-data="onToggleProjectLink(false)" v-model="ProjectLink" :title="'Danh mục liên kết'" :Project="Project"></ProjectLinkModal>
                  <a class="ij-a-icon" @click="onToggleProjectLink">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showProjectLink"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showProjectLink">
              <ProjectLinkContent v-model="ProjectLink"></ProjectLinkContent>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleProjectFile">Tệp</label>
              <div class="col-md-20">
                <div class="float-right">
                  <span @click="downloadAllFile" style="font-size: 18px; cursor: pointer" title="Tải tất cả tệp"><i class="fa fa-download"></i></span>
                  <ijcore-upload-multiple-file v-on:changed="changeFile" :isIcon="true"></ijcore-upload-multiple-file>
                  <a @click="onToggleProjectFile" class="ij-a-icon">
                    <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showProjectFile"
                       title="Thu gọn"></i>
                    <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                       title="Mở rộng"></i>
                  </a>
                </div>

              </div>
            </div>
            <b-collapse v-model="showProjectFile">
              <div class="form-group row">
                <div class="col-md-24 m-0">
                  <project-file-view v-model="ProjectFile" :Project="Project"></project-file-view>
                </div>
              </div>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleProjectVideo">Phim</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <div class="float-right">
                    <IjcoreUploadMultipleVideo v-on:changed="changeVideo" :isIcon="true"></IjcoreUploadMultipleVideo>
                    <a @click="onToggleProjectVideo" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showProjectVideo"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <b-collapse v-model="showProjectVideo">
              <div class="form-group row">
                <div class="col-md-24 m-0">
                  <ProjectVideoView v-model="ProjectVideo" :Project="Project"></ProjectVideoView>
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
import ProjectGeneralView from "./partials/ProjectGeneralView";
import ProjectGeneralModal from "./partials/ProjectGeneralModal";
import ProjectLinkModal from "./partials/ProjectLinkModal";
import ProjectLinkContent from "./partials/ProjectLinkContent";
import IjcoreUploadMultipleFile from "../../../components/IjcoreUploadMultipleFile";
import ProjectFileView from "./partials/ProjectFileView";
import IjcoreUploadMultipleVideo from "@/components/IjcoreUploadMultipleVideo";
import ProjectVideoView from "./partials/ProjectVideoView";
import ProjectPerView from "./partials/ProjectPerView";
import ProjectPerModal from "./partials/ProjectPerModal";

const ListRouter = 'listing-project';
const EditRouter = 'listing-project-edit';
const CreateRouter = 'listing-project-create';
const ViewApi = 'listing/api/project/view';
const ListApi = 'listing/api/project';
const DeleteApi = 'listing/api/project/delete';
const UpdateApi = 'listing/api/project/update';

export default {
  name: 'listing-view-item',
  data () {
    return {
      showGeneralInfo: true,
      showProjectLink: false,
      showProjectFile: false,
      showProjectVideo: false,
      showProjectPer: false,
      Project: {
        ProjectID: null,
        ProjectNo: '',
        TabmisNo: '',
        TabmisDate: null,
        ProjectName: '',
        ParentID: null,
        ParentNo: '',
        ParentName: null,
        MPeriodID: 1,
        ManagementLevel: 1,
        Status: 1,
        Group: 'QTQG',
        PercentCompleted: 0,
        SectorID: null,
        SectorName: '',
        ProgramID: null,
        ProgramName: '',
        InvestDecisionOrganID: null,
        InvestDecisionOrganName: '',
        InvestorID: null,
        InvestorName: '',
        StateOrganID: null,
        StateOrganName: '',
        SbiChapterID: null,
        SbiChapterNo: '',
        SbiChapterName: '',
        SbiCategoryID: null,
        SbiCategoryNo: '',
        SbiCategoryName: '',
        BuildAddress: '',
        CapableDesign: '',
        CapableFulfilling: '',
        Tarnget: '',
        InvestScale: '',
        ExpectedStartDate: null,
        ExpectedFinishDate: null,
        ExpectedHandoverDate: null,
        StartedDate: null,
        HandoverDate: null,
        FinishedDate: null,
        SettlementDate: null,
        ClosedDate: null,
        Note: '',
        NumberValue: 1,
        Prefix: '',
        Suffix: '',
        Inactive: false,
        Province: {},
        District: {},
        Commune: {},
        AccessType: 1,
        UserIDCreated: null,
        AuthorizedPerson: null,
        InvestdocNo: '',
        InvestdocDate: null,
        PacttdocNo: '',
        PacttdocDate: null,
        ProjectCate: [],
        ProjectCateList: [],
        ProjectCateValue: [],

        ManagementLevelOption: [],
        MPeriodOption: [],
        StatusOption: {},
        GroupOption: [],
        UseCapital: null,
      },
      ProjectLink: [],
      ProjectFile: [],
      ProjectVideo: [],
      ProjectPer: {},
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
    ProjectGeneralView,
    ProjectGeneralModal,
    ProjectLinkModal,
    ProjectLinkContent,
    IjcoreUploadMultipleFile,
    ProjectFileView,
    IjcoreUploadMultipleVideo,
    ProjectVideoView,
    ProjectPerView,
    ProjectPerModal,
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
          self.Project.ProjectID = responsesData.data.data.ProjectID;
          self.Project.ProjectName = responsesData.data.data.ProjectName;
          self.Project.ProjectNo = responsesData.data.data.ProjectNo;
          self.Project.TabmisNo = responsesData.data.data.TabmisNo;
          self.Project.TabmisDate = responsesData.data.data.TabmisDate;
          self.Project.MPeriodID = responsesData.data.data.MPeriodID;
          self.Project.ManagementLevel = responsesData.data.data.ManagementLevel;
          self.Project.Status = responsesData.data.data.Status;
          self.Project.PercentCompleted = responsesData.data.data.PercentCompleted;
          self.Project.SectorID = responsesData.data.data.SectorID;
          self.Project.SectorName = responsesData.data.data.SectorName;
          self.Project.ProgramID = responsesData.data.data.ProgramID;
          self.Project.ProgramName = responsesData.data.data.ProgramName;
          self.Project.InvestDecisionOrganID = responsesData.data.data.InvestDecisionOrganID;
          self.Project.InvestDecisionOrganName = responsesData.data.data.InvestDecisionOrganName;
          self.Project.InvestorID = responsesData.data.data.InvestorID;
          self.Project.InvestorName = responsesData.data.data.InvestorName;
          self.Project.StateOrganID = responsesData.data.data.StateOrganID;
          self.Project.StateOrganName = responsesData.data.data.StateOrganName;
          self.Project.SbiChapterID = responsesData.data.data.SbiChapterID;
          self.Project.SbiChapterNo = responsesData.data.data.SbiChapterNo;
          self.Project.SbiChapterName = responsesData.data.data.SbiChapterName;
          self.Project.SbiCategoryID = responsesData.data.data.SbiCategoryID;
          self.Project.SbiCategoryNo = responsesData.data.data.SbiCategoryNo;
          self.Project.SbiCategoryName = responsesData.data.data.SbiCategoryName;
          self.Project.BuildAddress = responsesData.data.data.BuildAddress;
          self.Project.CapableDesign = responsesData.data.data.CapableDesign;
          self.Project.CapableFulfilling = responsesData.data.data.CapableFulfilling;
          self.Project.Tarnget = responsesData.data.data.Tarnget;
          self.Project.InvestScale = responsesData.data.data.InvestScale;
          self.Project.ExpectedStartDate = responsesData.data.data.ExpectedStartDate;
          self.Project.ExpectedFinishDate = responsesData.data.data.ExpectedFinishDate;
          self.Project.ExpectedHandoverDate = responsesData.data.data.ExpectedHandoverDate;
          self.Project.StartedDate = responsesData.data.data.StartedDate;
          self.Project.HandoverDate = responsesData.data.data.HandoverDate;
          self.Project.FinishedDate = responsesData.data.data.FinishedDate;
          self.Project.SettlementDate = responsesData.data.data.SettlementDate;
          self.Project.ClosedDate = responsesData.data.data.ClosedDate;
          self.Project.EmployeeID = responsesData.data.data.EmployeeID;
          self.Project.Note = responsesData.data.data.Note;
          self.Project.Prefix = responsesData.data.data.Prefix;
          self.Project.Suffix = responsesData.data.data.Suffix;
          self.Project.Inactive = (responsesData.data.data.Inactive) ? true : false;
          self.Project.Province.ProvinceID = responsesData.data.data.ProvinceID;
          self.Project.Province.ProvinceName = responsesData.data.data.ProvinceName;
          self.Project.District.DistrictID = responsesData.data.data.DistrictID;
          self.Project.District.DistrictName = responsesData.data.data.DistrictName;
          self.Project.Commune.CommuneID = responsesData.data.data.CommuneID;
          self.Project.Commune.CommuneName = responsesData.data.data.CommuneName;
          self.Project.ParentID = responsesData.data.data.ParentID;
          self.Project.ParentNo = responsesData.data.Parent.ParentNo;
          self.Project.ParentName = responsesData.data.Parent.ParentName;
          self.Project.AccessType = responsesData.data.data.AccessType;
          self.Project.UserIDCreated = responsesData.data.data.UserIDCreated;
          self.Project.AuthorizedPerson = responsesData.data.data.AuthorizedPerson;
          self.Project.InvestdocNo = responsesData.data.data.InvestdocNo;
          self.Project.InvestdocDate = responsesData.data.data.InvestdocDate;
          self.Project.PacttdocNo = responsesData.data.data.PacttdocNo;
          self.Project.PacttdocDate = responsesData.data.data.PacttdocDate;
          self.Project.UseCapital = responsesData.data.data.UseCapital;

          self.Project.ProjectCate = [];
          self.$set(self.Project,'ProjectCate',[]);
          if(responsesData.data.ProjectCate){
            _.forEach(responsesData.data.ProjectCate, (projectCate, key)=>{
              let tmpObj = {};
              if(projectCate.CateID){
                let cateList = _.find(responsesData.data.ProjectCateList, ['CateID', projectCate.CateID]);
                if(cateList){
                  tmpObj.CateID = cateList.CateID;
                  tmpObj.CateName = cateList.CateName;
                }
              }
              if(projectCate.CateValue){
                // let cateValue = _.find(responsesData.data.ProjectCateValue, (cate)=> {
                //   return cate.CateID === projectCate.CateID && cate.CateValue === projectCate.CateValue;
                // });
                let cateValue = _.find(responsesData.data.ProjectCateValue,{
                  CateID: projectCate.CateID,
                  CateValue: projectCate.CateValue
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
              // self.Project.ProjectCate.push(tmpObj);
              self.$set(self.Project.ProjectCate, self.Project.ProjectCate.length, tmpObj);
            })
          }

          if(_.isObject(responsesData.MPeriodOption)){
            self.Project.MPeriodOption = responsesData.MPeriodOption;
          }
          if(_.isObject(responsesData.ManagementLevelOption)){
            self.Project.ManagementLevelOption = responsesData.ManagementLevelOption;
          }

          if(_.isObject(responsesData.GroupOption)){
            self.Project.GroupOption = responsesData.GroupOption;
          }

          if (_.isArray(responsesData.StatusItem)) {
            _.forEach(responsesData.StatusItem, function (value, key) {
              self.Project.StatusOption[value.StatusValue] = value.StatusDescription;
            });
          }
          // Employee
          self.EmployeeOption = [];
          self.EmployeeOptionArr = [];
          _.forEach(responsesData.Employee, function (val, key) {
            if(val.EmployeeID === self.Project.AuthorizedPerson){
              self.Project.EmployeeName = val.EmployeeName;
            }
            self.EmployeeOptionArr[val.EmployeeID] = val.EmployeeName;
            let tmpObj = {};
            tmpObj.id = val.EmployeeID;
            tmpObj.text = val.EmployeeName;
            self.EmployeeOption.push(tmpObj);
          });
          //Project
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
          _.forEach(responsesData.ProjectPer, function (val, key) {
            responsesData.ProjectPer[key].Access = (responsesData.ProjectPer[key].Access) ? true : false;
            responsesData.ProjectPer[key].Edit = (responsesData.ProjectPer[key].Edit) ? true : false;
            responsesData.ProjectPer[key].Delete = (responsesData.ProjectPer[key].Delete) ? true : false;
            responsesData.ProjectPer[key].Create = (responsesData.ProjectPer[key].Create) ? true : false;
          });
          self.ProjectPer = responsesData.ProjectPer;
          self.ProjectPerEmployee = responsesData.ProjectPerEmployee;

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

      if (this.reqParams.search.ProjectNo !== '') {
        requestData.data.ProjectNo = this.reqParams.search.ProjectNo;
      }
      if (this.reqParams.search.ProjectName !== '') {
        requestData.data.ProjectName = this.reqParams.search.ProjectName;
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
            self.reqParams.idsArray.push(value.ProjectID);
          });

          (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
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
      let params = this.$route.params.req;
      let query = this.$route.query;
      query.isBackToList = true;
      if (_.isString(message)) {
        params.message = message;
        this.$router.push({name: ListRouter, query: query, params: params});
      } else {
        this.$router.push({name: ListRouter, query: query, params: params});
      }
    },
    handleCopyItem(){
      this.$router.push({name: CreateRouter, params: {itemCopy: this.defaultModel}});
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
    onToggleProjectLink(toggle = true){
      let self = this;
      if(!this.ProjectLink.length){
        let requestData = {
          method: 'get',
          url: '/listing/api/project/get-project-link/' + this.$route.params.id,
          data: {}
        }

        self.$store.commit('isLoading',false);
        ApiService.setHeader();
        ApiService.customRequest(requestData)
          .then(response =>{
            let responseData = response.data;
            self.ProjectLink = responseData.data;
          })
          .catch(error=>{
            console.log(error);

          })
      }
      if(toggle){
        this.showProjectLink = !this.showProjectLink;
      }
    },
    onToggleProjectFile(toggle = true){
      let self = this;
      if(!this.ProjectFile.length){
        let requestData = {
          method: 'get',
          url: 'listing/api/project/get-project-file/' + this.Project.ProjectID,
          data:{}
        }

        ApiService.setHeader();
        ApiService.customRequest(requestData)
          .then(response=> {
            let reponseData = response.data;
            console.log(reponseData)
            if(reponseData.status === 1){
              self.ProjectFile = reponseData.data;
            }
          })
          .catch(error=>{
            console.log(error)
          })
      }
      if (toggle) {
        this.showProjectFile = !this.showProjectFile;
      }
    },
    downloadAllFile(){
      let self = this;
      let requestData = {
        url: 'listing/api/project/download-all-file/' + this.Project.ProjectID,
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
        formData.append('ProjectID', self.Project.ProjectID);
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
        axios.post('listing/api/project/project-upload-file/' + self.Project.ProjectID, formData, config)
          .then(function (response) {
            let responseData = response.data;
            if (responseData.status === 1) {
              currentObj.success = response.data.success;
              currentObj.filename = "";
              let dataR = response.data.data;
              self.ProjectFile.push({
                LineID: dataR.LineID,
                FileUpload: file,
                ProjectID: dataR.ProjectID,
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
              self.showProjectFile = true;
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
    onToggleProjectVideo(toggle = true) {
      let self = this;
      if (!this.ProjectVideo.length) {
        let requestData = {
          method: 'get',
          url: 'listing/api/project/get-project-video/' + this.Project.ProjectID,
          data: {}
        };
        // Api edit user
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.ProjectVideo = responsesData.data;
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      }
      if (toggle) {
        this.showProjectVideo = !this.showProjectVideo;
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
        formData.append('ProjectID', self.Project.ProjectID);
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
        axios.post('listing/api/project/project-upload-video/' + self.Project.ProjectID, formData, config)
          .then(function (response) {
            let responseData = response.data;
            if (responseData.status === 1) {
              currentObj.success = response.data.success;
              currentObj.videoname = "";
              let dataR = response.data.data;
              self.ProjectVideo.push({
                LineID: dataR.LineID,
                VideoUpload: video,
                ProjectID: dataR.ProjectID,
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
              self.showProjectVideo = true;
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

<style lang="css">
.mx-datepicker{width: 100% !important;}
.mx-input-wrapper{ width: 100% !important;}
</style>
