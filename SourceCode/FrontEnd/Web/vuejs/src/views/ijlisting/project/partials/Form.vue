<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Dự án<span v-if="model.ProjectName">:</span> {{model.ProjectName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Dự án<span v-if="model.ProjectName">:</span> {{model.ProjectName}}</span>
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
              <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Mã dự án</div>
              <div class="col-md-4 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                <input v-model="model.ProjectNo" type="text" class="form-control" placeholder="Mã dự án" name="ProjectName"/>
              </div>
              <div class="col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Mã Tabmis</div>
              <div class="col-md-4 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                <input v-model="model.TabmisNo" type="text" class="form-control" placeholder="Mã Tabmis" name="ProjectName"/>
              </div>
              <div class="col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Ngày cấp</div>
              <div class="col-md-3 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                <IjcoreDatePicker v-model="model.TabmisDate"></IjcoreDatePicker>
              </div>
              <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap" title="Kỳ kế hoạch vốn trung hạn">Kỳ KHVTH</div>
              <div class="col-md-3 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                <b-form-select v-model="model.MPeriodID" :options="MPeriodOption">
                </b-form-select>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
              <div class="col-md-21 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                <input v-model="model.ProjectName" type="text" id="ProjectName" class="form-control" placeholder="Tên dự án" name="ProjectName"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Là mục con của</label>
              <div class="col-md-21">
                <IjcoreModalParent v-model="model" :title="'Dự án'" :api="'/listing/api/common/get-parent'" :table="'project'" :fieldID="'ProjectID'" :fieldNo="'ProjectNo'" :fieldName="'ProjectName'" :placeholderInput="'Chọn dự án cha'" :placeholderSearch="'Nhập tên dự án'">
                </IjcoreModalParent>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Loại dự án</label>
              <div class="col-md-15">
                <project-modal-search-input-catelist
                  v-model="model.ProjectCate"
                  :listApi="'listing/api/project/get-project-cate-list'"
                  title-modal="Loại dự án"
                  placeholder="Loại dự án"
                ></project-modal-search-input-catelist>
              </div>
              <label class="col-md-3 m-0">Quyền truy cập</label>
              <div class="col-md-3">
                <b-form-select v-model="model.AccessType" :options="AccessTypeOptions" id="item-uom">
                </b-form-select>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Nhóm</label>
              <div class="col-md-3 mb-3 mb-sm-0">
                <b-form-select v-model="model.Group" :options="GroupOption"></b-form-select>
              </div>
              <label class="col-md-3 m-0" title="Tên cơ quan quyết định đầu tư">Tên CQQĐĐT</label>
              <div class="col-md-3 mb-3 mb-sm-0">
                <IjcoreModalSearchCompany v-model="model.InvestDecisionOrganName" :fieldCateList="'29'" :fieldCateValue="[1]" :title="'Tên CQQĐĐT'" :table="'company'" :api="'/listing/api/company/get-cate-value'" :placeholderInput="'Tên CQQĐĐT'" @changeInvestDecisionOrgan="updateInvestDecisionOrgan"></IjcoreModalSearchCompany>
              </div>
              <label class="col-md-3 m-0" >Chủ đầu tư</label>
              <div class="col-md-3">
                <IjcoreModalSearchCompany v-model="model.InvestorName" :fieldCateList="'29'" :fieldCateValue="[2]" :title="'Chủ đầu tư'" :table="'company'" :api="'/listing/api/company/get-cate-value'" :placeholderInput="'Chủ đầu tư'" @changeInvestor="updateInvestor"></IjcoreModalSearchCompany>
              </div>
              <label class="col-md-3 m-0" title="Chương trình mục tiêu">CTMT</label>
              <div class="col-md-3">
                <ijcore-modal-search-listing
                v-model="model" :title="'Chương trình mục tiêu'" :table="'program'" :api="'/listing/api/common/list'"
                :fieldID="'ProgramID'" :fieldNo="'ProgramNo'" :fieldName="'ProgramName'"
                :fieldAssignID="'ProgramID'" :fieldAssignNo="'ProgramNo'" :fieldAssignName="'ProgramName'"
                >
                </ijcore-modal-search-listing>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Cấp quản lý</label>
              <div class="col-md-3 mb-3 mb-sm-0">
                <b-form-select v-model="model.ManagementLevel" :options="ManagementLevelOption">
                </b-form-select>
              </div>
              <label class="col-md-3 m-0">Chương</label>
              <div class="col-md-3 mb-3 mb-sm-0">
                <ijcore-modal-search-listing
                  v-model="model" :title="'Chương'" :table="'sbi_chapter'" :api="'/listing/api/common/list'"
                  :fieldID="'SbiChapterID'" :fieldNo="'SbiChapterNo'" :fieldName="'SbiChapterName'"
                  :fieldAssignID="'SbiChapterID'" :fieldAssignNo="'SbiChapterNo'" :fieldAssignName="'SbiChapterName'"
                >
                </ijcore-modal-search-listing>
              </div>
              <label class="col-md-3 m-0">Loại khoản</label>
              <div class="col-md-3">
                <ijcore-modal-search-listing
                  v-model="model" :title="'Loại - Khoản'" :table="'sbi_category'" :api="'/listing/api/common/list'"
                  :fieldID="'SbiCategoryID'" :fieldNo="'SbiCategoryNo'" :fieldName="'SbiCategoryName'"
                  :fieldAssignID="'SbiCategoryID'" :fieldAssignNo="'SbiCategoryNo'" :fieldAssignName="'SbiCategoryName'"
                >
                </ijcore-modal-search-listing>
              </div>
              <label class="col-md-3 m-0">Lĩnh vực</label>
              <div class="col-md-3 mb-3 mb-sm-0">
                <ijcore-modal-search-listing
                  v-model="model" :title="'Lĩnh vực'" :table="'sector'" :api="'/listing/api/common/list'"
                  :fieldID="'SectorID'" :fieldNo="'SectorNo'" :fieldName="'SectorName'"
                  :fieldAssignID="'SectorID'" :fieldAssignNo="'SectorNo'" :fieldAssignName="'SectorName'"
                >
                </ijcore-modal-search-listing>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Tỉnh</label>
              <div class="col-md-3 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
                <ijcore-modal-search-input
                  v-model="model.Province"
                  :select-fields-api="[
                              {field: 'ProvinceID',fieldForSelected: 'id', showInTable: false, key: 'ProvinceID'},
                              {field: 'ProvinceName', fieldForSelected: 'name', showInTable: true, label: 'Tên dự án', key: 'ProvinceName', sortable: true, thClass: 'd-none'}
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
              <label class="col-md-3 m-0">Huyện</label>
              <div class="col-md-3 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
                <ijcore-modal-search-input
                  v-model="model.District"
                  :select-fields-api="[
                              {field: 'DistrictID',fieldForSelected: 'id', showInTable: false, key: 'DistrictID'},
                              {field: 'DistrictName', fieldForSelected: 'name', showInTable: true, label: 'Tên dự án', key: 'DistrictName', sortable: true, thClass: 'd-none'}
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
              <label class="col-md-3 m-0">Xã</label>
              <div class="col-md-3 ">
                <ijcore-modal-search-input
                  v-model="model.Commune"
                  :select-fields-api="[
                              {field: 'CommuneID',fieldForSelected: 'id', showInTable: false, key: 'CommuneID'},
                              {field: 'CommuneName', fieldForSelected: 'name', showInTable: true, label: 'Tên dự án', key: 'CommuneName', sortable: true, thClass: 'd-none'}
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
              <label class="col-md-3 m-0" title="Ban quản lý dự án">Ban QLDA</label>
              <div class="col-md-3 mb-3 mb-sm-0">
                <IjcoreModalSearchCompany v-model="model.StateOrganName" :fieldCateList="'29'" :fieldCateValue="[3]" :title="'Ban QLDA'" :table="'company'" :api="'/listing/api/company/get-cate-value'"  :placeholderInput="'Ban QLDA'" @changeStateOrgan="updateStateOrgan"></IjcoreModalSearchCompany>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" title="Ngày dự kiến khởi công">Ngày DKKC</label>
              <div class="col-md-3 mb-3 mb-sm-0">
                <IjcoreDatePicker v-model="model.ExpectedStartDate"></IjcoreDatePicker>
              </div>
              <label class="col-md-3 m-0" title="Ngày dự kiến hoàn thành">Ngày DKHT</label>
              <div class="col-md-3 mb-3 mb-sm-0">
                <IjcoreDatePicker v-model="model.ExpectedFinishDate"></IjcoreDatePicker>
              </div>
              <label class="col-md-3 m-0">Ngày khởi công</label>
              <div class="col-md-3 mb-3 mb-sm-0">
                <IjcoreDatePicker v-model="model.StartedDate"></IjcoreDatePicker>
              </div>
              <label class="col-md-3 m-0">Ngày hoàn thành</label>
              <div class="col-md-3">
                <IjcoreDatePicker v-model="model.FinishedDate"></IjcoreDatePicker>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" title="Ngày dự kiến bàn giao">Ngày DKBG</label>
              <div class="col-md-3 mb-3 mb-sm-0">
                <IjcoreDatePicker v-model="model.ExpectedHandoverDate"></IjcoreDatePicker>
              </div>
              <label class="col-md-3 m-0" title="Ngày bàn giao">Ngày BG</label>
              <div class="col-md-3">
                <IjcoreDatePicker v-model="model.HandoverDate"></IjcoreDatePicker>
              </div>
              <label class="col-md-3 m-0" title="Ngày quyết toán">Ngày QT</label>
              <div class="col-md-3 mb-3 mb-sm-0">
                <IjcoreDatePicker v-model="model.SettlementDate"></IjcoreDatePicker>
              </div>
              <label class="col-md-3 m-0" title="Ngày ngừng theo dõi">Ngày NTD</label>
              <div class="col-md-3 mb-3 mb-sm-0">
                <IjcoreDatePicker v-model="model.ClosedDate"></IjcoreDatePicker>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" title="Số quyết định đầu tư">Số QĐĐT</label>
              <div class="col-md-3 mb-3 mb-sm-0">
                <b-form-input v-model="model.InvestdocNo" placeholder="Số QĐĐT"></b-form-input>
              </div>
              <label class="col-md-3 m-0" title="Ngày quyết định đầu tư">Ngày QĐĐT</label>
              <div class="col-md-3">
                <IjcoreDatePicker v-model="model.InvestdocDate"></IjcoreDatePicker>
              </div>
              <label class="col-md-3 m-0">Số hiệp định</label>
              <div class="col-md-3 mb-3 mb-sm-0">
                <b-form-input v-model="model.PacttdocNo" placeholder="Số hiệp định"></b-form-input>
              </div>
              <label class="col-md-3 m-0">Ngày hiệp định</label>
              <div class="col-md-3 mb-3 mb-sm-0">
                <IjcoreDatePicker v-model="model.PacttdocDate"></IjcoreDatePicker>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" title="Công suất thiết kế">Công suất TK</label>
              <div class="col-md-9">
                <input v-model="model.CapableDesign" type="text"  class="form-control" placeholder="Công suất thiết kế">
              </div>
              <label class="col-md-3 m-0" title="Công suất hoàn thành">Công suất HT</label>
              <div class="col-md-9 mb-3 mb-sm-0">
                <input v-model="model.CapableFulfilling" type="text" class="form-control" placeholder="Công suất hoàn thành">
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" >Quy mô</label>
              <div class="col-md-9">
                <input v-model="model.InvestScale" type="text"  class="form-control" placeholder="Quy mô CT thực hiện">
              </div>
              <label class="col-md-3 m-0" title="Địa điểm xây dựng">Địa điểm XD</label>
              <div class="col-md-9 mb-3 mb-sm-0">
                <input v-model="model.BuildAddress" type="text" class="form-control" placeholder="Địa điểm xây dựng">
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" >Mục tiêu</label>
              <div class="col-md-9">
                <input v-model="model.Tarnget" type="text"  class="form-control" placeholder="Mục tiêu">
              </div>
              <label class="col-md-3 m-0" >Tình trạng</label>
              <div class="col-md-3">
                <b-form-select v-model="model.Status" :options="StatusOption">
                </b-form-select>
              </div>
              <label class="col-md-3 m-0" title="Phần trăm hoàn thành">%HT</label>
              <div class="col-md-3">
                <b-form-input v-model="model.PercentCompleted" placeholder="Phần trăm hoàn thành"></b-form-input>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <div class="col-md-3 m-0" title="Nguồn vốn sử dụng">Nguồn vốn SD</div>
              <div class="col-md-9">
                <b-form-select v-model="model.UseCapital" :options="UseCapitalOptions"></b-form-select>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Ghi chú</div>
              <div class="col-md-21 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
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
import IjcoreDatePicker from '@/components/IjcoreDatePicker';
import IjcoreModalListing from "../../../../components/IjcoreModalListing";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import ProjectModalSearchInputCatelist from "./ProjectModalSearchInputCatelist";
import IjcoreModalParent from "../../../../components/IjcoreModalParent";
import IjcoreModalSearchCompany from "../../../../components/IjcoreModalSearchCompany";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";

const ListRouter = 'listing-project';
const EditRouter = 'listing-project-edit';
const ViewRouter = 'listing-project-view';
const CreateRouter = 'listing-project-create';
const ViewApi = 'listing/api/project/view';
const EditApi = 'listing/api/project/edit';
const CreateApi = 'listing/api/project/create';
const StoreApi = 'listing/api/project/store';
const UpdateApi = 'listing/api/project/update';
const ListApi = 'listing/api/project';

export default {
  name: 'listing-view-item',
  data () {
    return {
      idParams: this.idParamsProps,
      reqParams: this.reqParamsProps,
      ManagementLevelOption: [],
      MPeriodOption: [],
      StatusOption: [],
      GroupOption: [],
      model: {
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
        ProjectOption: [],
        Province: {},
        District: {},
        Commune: {},
        AccessType: 1,
        InvestdocNo: '',
        InvestdocDate: null,
        PacttdocNo: '',
        PacttdocDate: null,
        ProjectCate: [],
        StatusCate: {},
        UseCapital: null,
      },
      AccessTypeOptions:{
        1: 'Chia sẻ',
        2: 'Công khai',
        3: 'Riêng tư'
      },
      UseCapitalOptions : [
        {value: null, text: '----- Chọn nguồn vốn sử dụng -----'},
        {value: 1, text: 'Vốn trong nước'},
        {value: 2, text: 'Vốn ODA và vay ưu đãi'},
        {value: 3, text: 'Vốn vay nợ nước ngoài'},
        {value: 4, text: 'Vốn viện trợ không hoàn lại'},
        {value: 5, text: 'Vốn khác'},
      ],
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
    ProjectModalSearchInputCatelist,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    IjcoreDatePicker,
    IjcoreModalSearchCompany,
    IjcoreModalSearchListing,
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
              self.model.ProjectID = responsesData.data.data.ProjectID;
              self.model.ParentID = responsesData.data.data.ParentID;
              self.model.ProjectName = responsesData.data.data.ProjectName;
              self.model.ProjectNo = responsesData.data.data.ProjectNo;
              self.model.TabmisNo = responsesData.data.data.TabmisNo;
              self.model.TabmisDate = (responsesData.data.data.TabmisDate ? self.onFormatDate(responsesData.data.data.TabmisDate) : null);
              self.model.ParentNo = responsesData.data.data.ParentNo;
              self.model.MPeriodID = responsesData.data.data.MPeriodID;
              self.model.ManagementLevel = responsesData.data.data.ManagementLevel;
              self.model.Status = responsesData.data.data.Status;
              self.model.PercentCompleted = responsesData.data.data.PercentCompleted;
              self.model.SectorID = responsesData.data.data.SectorID;
              self.model.SectorName = responsesData.data.data.SectorName;
              self.model.ProgramID = responsesData.data.data.ProgramID;
              self.model.ProgramName = responsesData.data.data.ProgramName;
              self.model.InvestDecisionOrganID = responsesData.data.data.InvestDecisionOrganID;
              self.model.InvestDecisionOrganName = responsesData.data.data.InvestDecisionOrganName;
              self.model.InvestorID = responsesData.data.data.InvestorID;
              self.model.InvestorName = responsesData.data.data.InvestorName;
              self.model.StateOrganID = responsesData.data.data.StateOrganID;
              self.model.StateOrganName = responsesData.data.data.StateOrganName;
              self.model.SbiChapterID = responsesData.data.data.SbiChapterID;
              self.model.SbiChapterNo = responsesData.data.data.SbiChapterNo;
              self.model.SbiChapterNo = responsesData.data.data.SbiChapterNo;
              self.model.SbiChapterName = responsesData.data.data.SbiChapterName;
              self.model.SbiCategoryID = responsesData.data.data.SbiCategoryID;
              self.model.SbiCategoryNo = responsesData.data.data.SbiCategoryNo;
              self.model.SbiCategoryName = responsesData.data.data.SbiCategoryName;
              self.model.BuildAddress = responsesData.data.data.BuildAddress;
              self.model.CapableDesign = responsesData.data.data.CapableDesign;
              self.model.CapableFulfilling = responsesData.data.data.CapableFulfilling;
              self.model.Tarnget = responsesData.data.data.Tarnget;
              self.model.InvestScale = responsesData.data.data.InvestScale;
              self.model.ExpectedStartDate = (responsesData.data.data.ExpectedStartDate ? self.onFormatDate(responsesData.data.data.ExpectedStartDate) : null);
              self.model.ExpectedFinishDate = (responsesData.data.data.ExpectedFinishDate ? self.onFormatDate(responsesData.data.data.ExpectedFinishDate) : null);
              self.model.ExpectedHandoverDate = (responsesData.data.data.ExpectedHandoverDate ? self.onFormatDate(responsesData.data.data.ExpectedHandoverDate) : null);
              self.model.StartedDate = (responsesData.data.data.StartedDate ? self.onFormatDate(responsesData.data.data.StartedDate) : null);
              self.model.HandoverDate = (responsesData.data.data.HandoverDate ? self.onFormatDate(responsesData.data.data.HandoverDate) : null);
              self.model.FinishedDate = (responsesData.data.data.FinishedDate ? self.onFormatDate(responsesData.data.data.FinishedDate) : null);
              self.model.SettlementDate = (responsesData.data.data.SettlementDate ? self.onFormatDate(responsesData.data.data.SettlementDate) : null);
              self.model.ClosedDate = (responsesData.data.data.ClosedDate ? self.onFormatDate(responsesData.data.data.ClosedDate) : null);
              self.model.Province = {
                ProvinceID: responsesData.data.data.ProvinceID,
                ProvinceName: responsesData.data.data.ProvinceName,
              }
              self.model.District = {
                DistrictID: responsesData.data.data.DistrictID,
                DistrictName: responsesData.data.data.DistrictName,
              }
              self.model.Commune = {
                CommuneID: responsesData.data.data.CommuneID,
                CommuneName: responsesData.data.data.CommuneName,
              }
              self.model.Note = responsesData.data.data.Note;
              self.model.AccessType = responsesData.data.data.AccessType;
              self.model.NumberValue = responsesData.data.data.NumberValue;
              self.model.InvestdocNo = responsesData.data.data.InvestdocNo;
              self.model.InvestdocDate = (responsesData.data.data.InvestdocDate ? self.onFormatDate(responsesData.data.data.InvestdocDate) : null);
              self.model.PacttdocNo = responsesData.data.data.PacttdocNo;
              self.model.PacttdocDate = (responsesData.data.data.PacttdocDate ? self.onFormatDate(responsesData.data.data.PacttdocDate) : null);
              self.model.Prefix = responsesData.data.data.Prefix;
              self.model.Suffix = responsesData.data.data.Suffix;
              self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
              self.model.UseCapital = responsesData.data.data.UseCapital;
            }

            if (!_.isEmpty(self.itemCopy)) {
              self.model.ProjectNo = responsesData.data.auto;
              self.model.ProjectCate = [];
              if(self.itemCopy.data.ProjectCate){
                _.forEach(self.itemCopy.data.ProjectCate, (projectCate, key)=>{
                  let tmpObj = {};
                  if(projectCate.CateID){
                    let cateList = _.find(self.itemCopy.data.ProjectCateList, ['CateID', projectCate.CateID]);
                    if(cateList){
                      tmpObj.CateID = cateList.CateID;
                      tmpObj.CateName = cateList.CateName;
                    }
                  }
                  if(projectCate.CateValue){
                    // let cateValue = _.find(self.itemCopy.data.ProjectCateValue, (cate)=> {
                    //   return cate.CateID === projectCate.CateID && cate.CateValue === projectCate.CateValue;
                    // });
                    let cateValue = _.find(self.itemCopy.data.ProjectCateValue,{
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
                  // self.model.ProjectCate.push(tmpObj);
                  self.$set(self.model.ProjectCate, self.model.ProjectCate.length, tmpObj);
                })
              }
              if(self.itemCopy.data.Parent){
                self.model.ParentID = self.itemCopy.data.Parent.ParentID;
                self.model.ParentNo = self.itemCopy.data.Parent.ParentNo;
                self.model.ParentName = self.itemCopy.data.Parent.ParentName;
              }
            }
          }else {
            self.model.ProjectNo = responsesData.data.auto;

            if(_.isObject(responsesData.data.StatusCate)){
              self.model.StatusCate.CateID = responsesData.data.StatusCate.CateID;
              self.model.StatusCate.CateName = responsesData.data.StatusCate.CateName;
              self.model.StatusCate.CateValue = responsesData.data.StatusCate.CateValue;
              self.model.StatusCate.Description = responsesData.data.StatusCate.Description;
              self.$forceUpdate();
            }
            if(_.isObject(responsesData.data.CateDefault)){
              let tmpObj = {};
              tmpObj.CateID = responsesData.data.CateDefault.CateID;
              tmpObj.CateName = responsesData.data.CateDefault.CateName;
              tmpObj.CateValue = responsesData.data.CateDefault.CateValue;
              tmpObj.Description = responsesData.data.CateDefault.Description;
              self.model.ProjectCate.push(tmpObj);
            }
          }

          if (_.isArray(responsesData.data.project)) {

            self.model.ProjectOption = [];
            _.forEach(responsesData.data.project, function (value, key) {
              let tmpObj = {};
              tmpObj.id = value.ProjectID;
              tmpObj.text = value.ProjectName;
              self.model.ProjectOption.push(tmpObj);
            });
          }

          if (_.isArray(responsesData.data.StatusItem)) {

            self.StatusOption = [];
            _.forEach(responsesData.data.StatusItem, function (value, key) {
              let tmpObj = {};
              tmpObj.value = value.StatusValue;
              tmpObj.text = value.StatusDescription;
              self.StatusOption.push(tmpObj);
            });
          }

          if(_.isObject(responsesData.data.MPeriodOption)){
            self.MPeriodOption = responsesData.data.MPeriodOption;
          }

          if(_.isObject(responsesData.data.ManagementLevelOption)){
            self.ManagementLevelOption = responsesData.data.ManagementLevelOption;
          }

          if(_.isObject(responsesData.data.GroupOption)){
            self.GroupOption = responsesData.data.GroupOption;
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
          table: 'project',
          ParentID: this.model.ParentID,
        },

      };

      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;

        this.model.ProjectNo = responseData.data;
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

      if (this.reqParams.search.projectNo !== '') {
        requestData.data.ProjectNo = this.reqParams.search.projectNo;
      }
      if (this.reqParams.search.projectName !== '') {
        requestData.data.ProjectName = this.reqParams.search.projectName;
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

    handleSubmitForm(){
      let self = this;
      const requestData = {
        method: 'post',
        url: StoreApi+'?XDEBUG_SESSION_START=PHPSTORM',
        data: {
          ProjectNo: this.model.ProjectNo,
          TabmisNo: this.model.TabmisNo,
          TabmisDate: this.model.TabmisDate,
          ProjectName: this.model.ProjectName,
          ParentID: this.model.ParentID,
          MPeriodID: this.model.MPeriodID,
          Group: this.model.Group,
          Inactive: (this.model.Inactive) ? 1 : 0,
          Note: this.model.Note,
          ManagementLevel: this.model.ManagementLevel,
          ProvinceID: this.model.Province.ProvinceID,
          ProvinceName: this.model.Province.ProvinceName,
          DistrictID: this.model.District.DistrictID,
          DistrictName: this.model.District.DistrictName,
          CommuneID: this.model.Commune.CommuneID,
          CommuneName: this.model.Commune.CommuneName,
          SectorID: this.model.SectorID,
          SectorName: this.model.SectorName,
          ProgramID: this.model.ProgramID,
          ProgramName: this.model.ProgramName,
          InvestDecisionOrganID: this.model.InvestDecisionOrganID,
          InvestDecisionOrganName: this.model.InvestDecisionOrganName,
          InvestorID: this.model.InvestorID,
          InvestorName: this.model.InvestorName,
          StateOrganID: this.model.StateOrganID,
          StateOrganName: this.model.StateOrganName,
          SbiChapterID: this.model.SbiChapterID,
          SbiChapterNo: this.model.SbiChapterNo,
          SbiChapterName: this.model.SbiChapterName,
          SbiCategoryID: this.model.SbiCategoryID,
          SbiCategoryNo: this.model.SbiCategoryNo,
          SbiCategoryName: this.model.SbiCategoryName,
          Status: this.model.Status,
          PercentCompleted: this.model.PercentCompleted,
          BuildAddress: this.model.BuildAddress,
          CapableFulfilling: this.model.CapableFulfilling,
          Tarnget: this.model.Tarnget,
          InvestScale: this.model.InvestScale,
          CapableDesign: this.model.CapableDesign,
          ExpectedStartDate: this.model.ExpectedStartDate,
          ExpectedFinishDate: this.model.ExpectedFinishDate,
          ExpectedHandoverDate: this.model.ExpectedHandoverDate,
          StartedDate: this.model.StartedDate,
          HandoverDate: this.model.HandoverDate,
          FinishedDate: this.model.FinishedDate,
          SettlementDate: this.model.SettlementDate,
          ClosedDate: this.model.ClosedDate,
          InvestdocNo: this.model.InvestdocNo,
          InvestdocDate: this.model.InvestdocDate,
          PacttdocNo: this.model.PacttdocNo,
          PacttdocDate: this.model.PacttdocDate,
          AccessType: this.model.AccessType,
          NumberValue: this.model.NumberValue,
          ProjectCate: this.model.ProjectCate,
          UseCapital: this.model.UseCapital,
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
    updateInvestor(data){
      this.model.InvestorID = data.CompanyID;
      this.model.InvestorName = data.CompanyName;
    },
    updateStateOrgan(data){
      this.model.StateOrganID = data.CompanyID;
      this.model.StateOrganName = data.CompanyName;
    },
    updateInvestDecisionOrgan(data){
      this.model.InvestDecisionOrganID = data.CompanyID;
      this.model.InvestDecisionOrganName = data.CompanyName;
    },
    onFormatDate(formatDate){
      return formatDate.split('-').reverse().join().replaceAll('/');
    },
    updateStatusCate(data){
      this.model.StatusCate = data;
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
          table: 'project',
          ParentID: this.model.ParentID,
        }
      }
      self.$store.commit('isLoading',true)
      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response=>{
          let responseData = response.data;
          if(responseData.status === 1){
            self.model.ProjectNo = responseData.data;
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
<style lang="css" scoped>
.select2-container{
  width: 100% !important;
}
.mx-datepicker{
  width: 100%;
}
</style>
