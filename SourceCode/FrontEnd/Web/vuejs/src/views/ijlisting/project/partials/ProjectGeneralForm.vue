<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Mã dự án</div>
      <div class="col-md-4 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.ProjectNo" type="text" class="form-control" placeholder="Mã dự án" name="ProjectName"/>
      </div>
      <div class="col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Mã Tabmis</div>
      <div class="col-md-4 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.TabmisNo" type="text" class="form-control" placeholder="Mã Tabmis" name="ProjectName"/>
      </div>
      <div class="col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Ngày cấp</div>
      <div class="col-md-3 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <IjcoreDatePicker v-model="value.TabmisDate"></IjcoreDatePicker>
      </div>
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap" title="Kỳ kế hoạch vốn trung hạn">Kỳ KHVTH</div>
      <div class="col-md-3 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <b-form-select v-model="value.MPeriodID" :options="value.MPeriodOption">
        </b-form-select>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-21 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.ProjectName" type="text" id="ProjectName" class="form-control" placeholder="Tên dự án" name="ProjectName"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Là mục con của</label>
      <div class="col-md-21">
        <IjcoreModalParent v-model="value" :title="'Dự án'" :api="'/listing/api/common/get-parent'" :table="'project'" :fieldID="'ProjectID'" :fieldNo="'ProjectNo'" :fieldName="'ProjectName'" :currentID="value.ProjectID" :placeholderInput="'Chọn dự án cha'" :placeholderSearch="'Nhập tên dự án'">
        </IjcoreModalParent>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Loại dự án</label>
      <div class="col-md-15">
        <project-modal-search-input-catelist
          v-model="value.ProjectCate"
          :listApi="'listing/api/project/get-project-cate-list'"
          title-modal="Loại dự án"
          placeholder="Loại dự án"
        ></project-modal-search-input-catelist>
      </div>
      <label class="col-md-3 m-0">Quyền truy cập</label>
      <div class="col-md-3">
        <b-form-select v-model="value.AccessType" :options="AccessTypeOptions" id="item-uom">
        </b-form-select>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Nhóm</label>
      <div class="col-md-3 mb-3 mb-sm-0">
        <b-form-select v-model="value.Group" :options="value.GroupOption"></b-form-select>
      </div>
      <label class="col-md-3 m-0" title="Tên cơ quan quyết định đầu tư">Tên CQQĐĐT</label>
      <div class="col-md-3 mb-3 mb-sm-0">
        <IjcoreModalSearchCompany v-model="value.InvestDecisionOrganName" :fieldCateList="'29'" :fieldCateValue="[1]" :title="'Tên CQQĐĐT'" :table="'company'" :api="'/listing/api/company/get-cate-value'" :placeholderInput="'Tên CQQĐĐT'" @changeInvestDecisionOrgan="updateInvestDecisionOrgan"></IjcoreModalSearchCompany>
      </div>
      <label class="col-md-3 m-0" >Chủ đầu tư</label>
      <div class="col-md-3">
        <IjcoreModalSearchCompany v-model="value.InvestorName" :fieldCateList="'29'" :fieldCateValue="[2]" :title="'Chủ đầu tư'" :table="'company'" :api="'/listing/api/company/get-cate-value'" :placeholderInput="'Chủ đầu tư'" @changeInvestor="updateInvestor"></IjcoreModalSearchCompany>
      </div>
      <label class="col-md-3 m-0" title="Chương trình mục tiêu">CTMT</label>
      <div class="col-md-3">
        <ijcore-modal-search-listing
          v-model="value" :title="'Chương trình mục tiêu'" :table="'program'" :api="'/listing/api/common/list'"
          :fieldID="'ProgramID'" :fieldNo="'ProgramNo'" :fieldName="'ProgramName'"
          :fieldAssignID="'ProgramID'" :fieldAssignNo="'ProgramNo'" :fieldAssignName="'ProgramName'"
        >
        </ijcore-modal-search-listing>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Cấp quản lý</label>
      <div class="col-md-3 mb-3 mb-sm-0">
        <b-form-select v-model="value.ManagementLevel" :options="value.ManagementLevelOption">
        </b-form-select>
      </div>
      <label class="col-md-3 m-0">Chương</label>
      <div class="col-md-3 mb-3 mb-sm-0">
        <ijcore-modal-search-listing
          v-model="value" :title="'Chương'" :table="'sbi_chapter'" :api="'/listing/api/common/list'"
          :fieldID="'SbiChapterID'" :fieldNo="'SbiChapterNo'" :fieldName="'SbiChapterName'"
          :fieldAssignID="'SbiChapterID'" :fieldAssignNo="'SbiChapterNo'" :fieldAssignName="'SbiChapterName'"
        >
        </ijcore-modal-search-listing>
      </div>
      <label class="col-md-3 m-0">Loại khoản</label>
      <div class="col-md-3">
        <ijcore-modal-search-listing
          v-model="value" :title="'Loại - Khoản'" :table="'sbi_category'" :api="'/listing/api/common/list'"
          :fieldID="'SbiCategoryID'" :fieldNo="'SbiCategoryNo'" :fieldName="'SbiCategoryName'"
          :fieldAssignID="'SbiCategoryID'" :fieldAssignNo="'SbiCategoryNo'" :fieldAssignName="'SbiCategoryName'"
        >
        </ijcore-modal-search-listing>
      </div>
      <label class="col-md-3 m-0">Lĩnh vực</label>
      <div class="col-md-3 mb-3 mb-sm-0">
        <ijcore-modal-search-listing
          v-model="value" :title="'Lĩnh vực'" :table="'sector'" :api="'/listing/api/common/list'"
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
          v-model="value.Province"
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
          v-model="value.District"
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
          :request-data="{ProvinceID: (value.Province) ? value.Province.ProvinceID : null}"
          :url-api="$store.state.appRootApi + '/listing/api/common/get-district'"
          name-input="input-district"
          title-modal="Huyện" size-modal="lg">
        </ijcore-modal-search-input>
      </div>
      <label class="col-md-3 m-0">Xã</label>
      <div class="col-md-3 ">
        <ijcore-modal-search-input
          v-model="value.Commune"
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
                              ProvinceID: (value.Province) ? value.Province.ProvinceID : null,
                              DistrictID: (value.District) ? value.District.DistrictID : null
                            }"
          :url-api="$store.state.appRootApi + '/listing/api/common/get-commune'"
          name-input="input-commune"
          title-modal="Xã" size-modal="lg">
        </ijcore-modal-search-input>
      </div>
      <label class="col-md-3 m-0" title="Ban quản lý dự án">Ban QLDA</label>
      <div class="col-md-3 mb-3 mb-sm-0">
        <IjcoreModalSearchCompany v-model="value.StateOrganName" :fieldCateList="'29'" :fieldCateValue="[3]" :title="'Ban QLDA'" :table="'company'" :api="'/listing/api/company/get-cate-value'"  :placeholderInput="'Ban QLDA'" @changeStateOrgan="updateStateOrgan"></IjcoreModalSearchCompany>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Ngày khởi công</label>
      <div class="col-md-3 mb-3 mb-sm-0">
        <IjcoreDatePicker v-model="value.StartedDate"></IjcoreDatePicker>
      </div>
      <label class="col-md-3 m-0">Ngày hoàn thành</label>
      <div class="col-md-3">
        <IjcoreDatePicker v-model="value.FinishedDate"></IjcoreDatePicker>
      </div>
      <label class="col-md-3 m-0" title="Ngày dự kiến khởi công">Ngày DKKC</label>
      <div class="col-md-3 mb-3 mb-sm-0">
        <IjcoreDatePicker v-model="value.ExpectedStartDate"></IjcoreDatePicker>
      </div>
      <label class="col-md-3 m-0" title="Ngày dự kiến hoàn thành">Ngày DKHT</label>
      <div class="col-md-3 mb-3 mb-sm-0">
        <IjcoreDatePicker v-model="value.ExpectedFinishDate"></IjcoreDatePicker>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" title="Ngày đăng ký bàn giao">Ngày DKBG</label>
      <div class="col-md-3 mb-3 mb-sm-0">
        <IjcoreDatePicker v-model="value.ExpectedHandoverDate"></IjcoreDatePicker>
      </div>
      <label class="col-md-3 m-0" title="Ngày bàn giao">Ngày BG</label>
      <div class="col-md-3">
        <IjcoreDatePicker v-model="value.HandoverDate"></IjcoreDatePicker>
      </div>
      <label class="col-md-3 m-0" title="Ngày quyết toán">Ngày QT</label>
      <div class="col-md-3 mb-3 mb-sm-0">
        <IjcoreDatePicker v-model="value.SettlementDate"></IjcoreDatePicker>
      </div>
      <label class="col-md-3 m-0" title="Ngày ngừng theo dõi">Ngày NTD</label>
      <div class="col-md-3 mb-3 mb-sm-0">
        <IjcoreDatePicker v-model="value.ClosedDate"></IjcoreDatePicker>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" title="Số quyết định đầu tư">Số QĐĐT</label>
      <div class="col-md-3 mb-3 mb-sm-0">
        <b-form-input v-model="value.InvestdocNo" placeholder="Số QĐĐT"></b-form-input>
      </div>
      <label class="col-md-3 m-0" title="Ngày quyết định đầu tư">Ngày QĐĐT</label>
      <div class="col-md-3">
        <IjcoreDatePicker v-model="value.InvestdocDate"></IjcoreDatePicker>
      </div>
      <label class="col-md-3 m-0" title="Số hiệp định">Số hiệp định</label>
      <div class="col-md-3 mb-3 mb-sm-0">
        <b-form-input v-model="value.PacttdocNo" placeholder="Số hiệp định"></b-form-input>
      </div>
      <label class="col-md-3 m-0" title="Ngày hiệp định">Ngày hiệp định</label>
      <div class="col-md-3 mb-3 mb-sm-0">
        <IjcoreDatePicker v-model="value.PacttdocDate"></IjcoreDatePicker>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" title="Công suất thiết kế">Công suất TK</label>
      <div class="col-md-9">
        <input v-model="value.CapableDesign" type="text"  class="form-control" placeholder="Công suất thiết kế">
      </div>
      <label class="col-md-3 m-0" title="Công suất hoàn thành">Công suất HT</label>
      <div class="col-md-9 mb-3 mb-sm-0">
        <input v-model="value.CapableFulfilling" type="text" class="form-control" placeholder="Công suất hoàn thành">
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" >Quy mô</label>
      <div class="col-md-9">
        <input v-model="value.InvestScale" type="text"  class="form-control" placeholder="Quy mô CT thực hiện">
      </div>
      <label class="col-md-3 m-0" title="Địa điểm xây dựng">Địa điểm XD</label>
      <div class="col-md-9 mb-3 mb-sm-0">
        <input v-model="value.BuildAddress" type="text" class="form-control" placeholder="Địa điểm xây dựng">
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" >Mục tiêu</label>
      <div class="col-md-9">
        <input v-model="value.Tarnget" type="text"  class="form-control" placeholder="Mục tiêu">
      </div>
      <label class="col-md-3 m-0" >Tình trạng</label>
      <div class="col-md-3">
        <b-form-select v-model="value.Status" :options="value.StatusOption">
        </b-form-select>
      </div>
      <label class="col-md-3 m-0" title="Phần trăm hoàn thành">%HT</label>
      <div class="col-md-3">
        <b-form-input v-model="value.PercentCompleted" placeholder="Phần trăm hoàn thành"></b-form-input>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <div class="col-md-3" title="Nguồn vốn sử dụng">Nguồn vố SD</div>
      <div class="col-md-9">
        <b-form-select v-model="value.UseCapital" :options="UseCapitalOptions"></b-form-select>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Ghi chú</div>
      <div class="col-md-21 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <textarea v-model="value.Note" class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note"></textarea>
      </div>
    </div>
  </div>
</template>
<script>
import Select2 from "v-select2-component";
import Swal from "sweetalert2";
import IjcoreDatePicker from '@/components/IjcoreDatePicker';
import IjcoreModalListing from "@/components/IjcoreModalListing";
import IjcoreModalParent from "@/components/IjcoreModalParent";
import ApiService from "@/services/api.service";
import ProjectModalSearchInputCatelist from "./ProjectModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import IjcoreModalSearchCompany from "../../../../components/IjcoreModalSearchCompany";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";

export default {
  name: 'ProjectGeneralForm',
  props: ['value','projectOptions','employeeOptions'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    ProjectModalSearchInputCatelist,
    IjcoreDatePicker,
    IjcoreModalSearchCompany,
    IjcoreModalSearchListing,
  },
  data(){
    return{
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
    }
  },
  methods: {
    updateInvestor(data){
      this.value.InvestorID = data.CompanyID;
      this.value.InvestorName = data.CompanyName;
    },
    updateStateOrgan(data){
      this.value.StateOrganID = data.CompanyID;
      this.value.StateOrganName = data.CompanyName;
    },
    updateInvestDecisionOrgan(data){
      this.value.InvestDecisionOrganID = data.CompanyID;
      this.value.InvestDecisionOrganName = data.CompanyName;
    },
  },
  watch: {
    'value.ParentID'(){
      let self = this;
      let urlApi = '/listing/api/common/auto-child';
      let requestData = {
        method: 'post',
        url: urlApi,
        data: {
          per_page: 10,
          page: this.currentPage,
          table: 'project',
          ParentID: this.value.ParentID,
        }
      }
      self.$store.commit('isLoading',true)
      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response=>{
          let responseData = response.data;
          if(responseData.status === 1){
            this.value.ProjectNo = responseData.data;
          }
          self.$store.commit('isLoading',false)
        }).catch(error=> {
        self.$store.commit('isLoading',false)
      })
    }
  }
}
</script>
<style lang="css" scoped>

.mx-datepicker{
  width: 100%;
}
.mx-input-wrapper{
  width: 100% !important;
}

</style>
