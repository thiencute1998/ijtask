<template>
  <div>
    <a class="ij-a-icon" @click="showModal" title="Chi tiết">
      <i class="fa fa-external-link ij-icon"></i>
    </a>
    <b-modal scrollable ref="modal" id="modal-form-input-project-general" size="xl">
      <template v-slot:modal-title>
        <i class="fa fa-edit" v-if="isForm"></i><i class="icon-eye icon" v-else></i> {{title}}
      </template>
      <project-general-form v-model="value" :projectOptions="projectOptions" :employeeOptions="employeeOptions"  v-if="isForm"></project-general-form>
      <project-general-view :isDetail="true" v-model="value" v-else></project-general-view>
      <template v-slot:modal-footer>
        <div class="w-100">
          <b-button variant="primary" size="md" class="mr-2" v-if="!isForm" @click="onEdit">Sửa</b-button>
          <b-button variant="primary" size="md" class="mr-2" v-if="isForm" @click="onUpdate">Lưu</b-button>
          <b-button variant="primary" size="md" class="mr-2" v-if="isForm" @click="onHideModal">Hủy</b-button>
          <b-button variant="primary" size="md" class="mr-2" @click="onHideModal">Đóng</b-button>
        </div>
      </template>
    </b-modal>
  </div>
</template>
<script>
import ApiService from "@/services/api.service";
import Swal from "sweetalert2";
import ProjectGeneralView from "./ProjectGeneralView";
import ProjectGeneralForm from "./ProjectGeneralForm";

export default {
  name: 'ProjectGeneralModal',
  props: ['value','title'],
  components: {
    ProjectGeneralView,
    ProjectGeneralForm
  },
  data() {
    return{
      isForm: false,
      employeeOptions: [],
      projectOptions: [],
    }
  },
  methods: {
    showModal(){
      this.$refs['modal'].show();
    },
    onHideModal(){
      this.isForm = false;
      this.$refs['modal'].hide();
    },
    onEdit(){
      this.isForm = true;
    },
    onUpdate(){
      let updateAPI = 'listing/api/project/update/';
      let requestData = {
        method: 'post',
        url: updateAPI + this.value.ProjectID,
        data: {
          ProjectNo: this.value.ProjectNo,
          TabmisNo: this.value.TabmisNo,
          TabmisDate: this.value.TabmisDate,
          ProjectName: this.value.ProjectName,
          ParentID: this.value.ParentID,
          MPeriodID: this.value.MPeriodID,
          Group: this.value.Group,
          Inactive: (this.value.Inactive) ? 1 : 0,
          Note: this.value.Note,
          ManagementLevel: this.value.ManagementLevel,
          ProvinceID: this.value.Province.ProvinceID,
          ProvinceName: this.value.Province.ProvinceName,
          DistrictID: this.value.District.DistrictID,
          DistrictName: this.value.District.DistrictName,
          CommuneID: this.value.Commune.CommuneID,
          CommuneName: this.value.Commune.CommuneName,
          SectorID: this.value.SectorID,
          SectorName: this.value.SectorName,
          ProgramID: this.value.ProgramID,
          ProgramName: this.value.ProgramName,
          InvestDecisionOrganID: this.value.InvestDecisionOrganID,
          InvestDecisionOrganName: this.value.InvestDecisionOrganName,
          InvestorID: this.value.InvestorID,
          InvestorName: this.value.InvestorName,
          StateOrganID: this.value.StateOrganID,
          StateOrganName: this.value.StateOrganName,
          SbiChapterID: this.value.SbiChapterID,
          SbiChapterNo: this.value.SbiChapterNo,
          SbiChapterName: this.value.SbiChapterName,
          SbiCategoryID: this.value.SbiCategoryID,
          SbiCategoryNo: this.value.SbiCategoryID,
          SbiCategoryName: this.value.SbiCategoryID,
          Status: this.value.Status,
          PercentCompleted: this.value.PercentCompleted,
          BuildAddress: this.value.BuildAddress,
          CapableFulfilling: this.value.CapableFulfilling,
          Tarnget: this.value.Tarnget,
          InvestScale: this.value.InvestScale,
          CapableDesign: this.value.CapableDesign,
          ExpectedStartDate: this.value.ExpectedStartDate,
          ExpectedFinishDate: this.value.ExpectedFinishDate,
          ExpectedHandoverDate: this.value.ExpectedHandoverDate,
          StartedDate: this.value.StartedDate,
          HandoverDate: this.value.HandoverDate,
          FinishedDate: this.value.FinishedDate,
          SettlementDate: this.value.SettlementDate,
          ClosedDate: this.value.ClosedDate,
          InvestdocNo: this.value.InvestdocNo,
          InvestdocDate: this.value.InvestdocDate,
          PacttdocNo: this.value.PacttdocNo,
          PacttdocDate: this.value.PacttdocDate,
          AccessType: this.value.AccessType,
          NumberValue: this.value.NumberValue,
          ProjectCate: this.value.ProjectCate,
          UseCapital: this.value.UseCapital,
        }
      }
      this.$store.commit('isLoading',true);
      let self = this;
      ApiService.setHeader();
      ApiService.customRequest(requestData)
      .then(response=>{
        let responseData = response.data;
        if(responseData.status === 1){
          self.$emit('changeDefaultModel', requestData);
          this.isForm = false;
          this.$refs['modal'].hide();
        }
        else {
          let htmlErrors = __.renderErrorApiHtml(responseData.data);
          Swal.fire(
            'Thông báo',
            htmlErrors,
            'error'
          );
        }
        this.$store.commit('isLoading',false);
      }).catch(error=>{
        Swal.fire('Thông báo','Không kết nối được với máy chủ','error');
        this.$store.commit('isLoading',false);
        console.log(error);
      })
      // ApiService

    }
  }
}
</script>
