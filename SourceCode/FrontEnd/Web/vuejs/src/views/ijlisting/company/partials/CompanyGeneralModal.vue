<template>
  <div>
    <a class="ij-a-icon" @click="showModal" title="Chi tiết">
      <i class="fa fa-external-link ij-icon"></i>
    </a>
    <b-modal scrollable ref="modal" id="modal-form-input-company-general" size="xl">
      <template v-slot:modal-title>
        <i class="fa fa-edit" v-if="isForm"></i><i class="icon-eye icon" v-else></i> {{title}}
      </template>
      <company-general-form v-model="value" :companyOptions="companyOptions" :employeeOptions="employeeOptions" :fin="Fin" :tre="Tre" v-if="isForm"></company-general-form>
      <company-general-view :isDetail="true" v-model="value" v-else :fin="Fin" :tre="Tre"></company-general-view>
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
import CompanyGeneralView from "./CompanyGeneralView";
import CompanyGeneralForm from "./CompanyGeneralForm";
const ViewRouter = 'listing-company-view';
export default {
  name: 'CompanyGeneralModal',
  props: ['value','title', 'Fin', 'Tre'],
  components: {
    CompanyGeneralView,
    CompanyGeneralForm,
  },
  data() {
    return{
      isForm: false,
      employeeOptions: [],
      companyOptions: [],
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
      let updateAPI = 'listing/api/company/update/';
      if(this.value.ManagementLevel == 1){
        this.value.Province.ProvinceID = null;
        this.value.Province.ProvinceNo = '';
        this.value.Province.ProvinceName = '';
        this.value.District.DistrictID = null;
        this.value.District.DistrictNo = '';
        this.value.District.DistrictName = '';
        this.value.Commune.CommuneID = null;
        this.value.Commune.CommuneNo = '';
        this.value.Commune.CommuneName = '';
      }
      if(this.value.ManagementLevel == 2){
        this.value.CenterID = null;
        this.value.CenterName = '';
        this.value.CenterNo = '';
        this.value.District.DistrictID = null;
        this.value.District.DistrictNo = '';
        this.value.District.DistrictName = '';
        this.value.Commune.CommuneID = null;
        this.value.Commune.CommuneNo = '';
        this.value.Commune.CommuneName = '';
        if(self.value.isFinOrg){
          self.value.FinMofID = self.Fin.FinID;
          self.value.FinMofNo = self.Fin.FinNo;
          self.value.FinMofName = self.Fin.FinName;
        }
        if(self.value.isTreOrg){
          self.value.TreMofID = self.Tre.TreID;
          self.value.TreMofNo = self.Tre.TreNo;
          self.value.TreMofName = self.Tre.TreName;
        }
      }
      if(this.value.ManagementLevel == 3){
        this.value.CenterID = null;
        this.value.CenterName = '';
        this.value.CenterNo = '';
        this.value.Commune.CommuneID = null;
        this.value.Commune.CommuneNo = '';
        this.value.Commune.CommuneName = '';
        if(self.value.isFinOrg){
          self.value.FinDofID = self.Fin.FinID;
          self.value.FinDofNo = self.Fin.FinNo;
          self.value.FinDofName = self.Fin.FinName;
        }
        if(self.value.isTreOrg){
          self.value.TreDofID = self.Tre.TreID;
          self.value.TreDofNo = self.Tre.TreNo;
          self.value.TreDofName = self.Tre.TreName;
        }
      }
      if(this.value.ManagementLevel == 4){
        this.value.CenterID = null;
        this.value.CenterName = '';
        this.value.CenterNo = '';
        if(self.value.isFinOrg){
          self.value.FinDfpID = self.Fin.FinID;
          self.value.FinDfpNo = self.Fin.FinNo;
          self.value.FinDfpName = self.Fin.FinName;
        }
        if(self.value.isTreOrg){
          self.value.TreDfpID = self.Tre.TreID;
          self.value.TreDfpNo = self.Tre.TreNo;
          self.value.TreDfpName = self.Tre.TreName;
        }
      }
      let requestData = {
        method: 'post',
        url: updateAPI + this.value.CompanyID+'?XDEBUG_SESSION_START=PHPSTORM',
        data: {
          CompanyID: this.value.CompanyID,
          CompanyNo: this.value.CompanyNo,
          CompanyName: this.value.CompanyName,
          ParentID: this.value.ParentID,
          ParentNo: this.value.ParentNo,
          Note: this.value.Note,
          Address: this.value.Address,
          Tel: this.value.Tel,
          Fax: this.value.Fax,
          Email: this.value.Email,
          EmployeeID: this.value.EmployeeID,
          ContactName: this.value.ContactName,
          ContactTel: this.value.ContactTel,
          SbiChapterID: this.value.SbiChapterID,
          SbiChapterName: this.value.SbiChapterName,
          Inactive: (this.value.Inactive) ? 1 : 0,
          ProvinceID: (this.value.Province.ProvinceID) ? this.value.Province.ProvinceID : null,
          ProvinceName: (this.value.Province.ProvinceID) ? this.value.Province.ProvinceName : '',
          DistrictID: (this.value.District.DistrictID) ? this.value.District.DistrictID : null,
          DistrictName: (this.value.District.DistrictID) ? this.value.District.DistrictName : '',
          CommuneID: (this.value.Commune.CommuneID) ? this.value.Commune.CommuneID : null,
          CommuneName: (this.value.Commune.CommuneID) ? this.value.Commune.CommuneName : '',
          CompanyCate: this.value.CompanyCate,
          AccessType: this.value.AccessType,
          IsFinancialCompany: (this.value.IsFinancialCompany) ? 1 : 0,
          ManagementLevel: this.value.ManagementLevel,
          CenterID : this.value.CenterID,
          CenterNo : this.value.CenterNo,
          CenterName : this.value.CenterName,
          SumCompanyType : this.value.SumCompanyType,
          isAutOrg: this.value.isAutOrg ? 1 : 0,
          AutOrgID: this.value.AutOrgName,
          AutOrgNo: this.value.AutOrgNo,
          AutOrgName: this.value.AutOrgName,
          AutOrgChapterID: this.value.AutOrgChapterID,
          AutOrgChapterNo: this.value.AutOrgChapterNo,
          AutOrgContactName : this.value.AutOrgContactName,
          AutOrgContactPosition: this.value.AutOrgContactPosition,
          AutOrgContactHandPhone : this.value.AutOrgContactHandPhone,
          AutOrgContactOfficePhone : this.value.AutOrgContactOfficePhone,
          AutOrgContactMail : this.value.AutOrgContactMail,
          AutOrgAddress : this.value.AutOrgAddress,
          isFinOrg: this.value.isFinOrg ? 1 : 0,
          FinMofID: this.value.FinMofID,
          FinMofNo: this.value.FinMofNo,
          FinMofName: this.value.FinMofName,
          FinDofID: this.value.FinDofID,
          FinDofNo: this.value.FinDofNo,
          FinDofName: this.value.FinDofName,
          FinDfpID: this.value.FinDfpID,
          FinDfpNo: this.value.FinDfpNo,
          FinDfpName: this.value.FinDfpName,
          isTreOrg : this.value.isTreOrg,
          TreMofID : this.value.TreMofID,
          TreMofNo : this.value.TreMofNo,
          TreMofName : this.value.TreMofName,
          TreDofID : this.value.TreDofID,
          TreDofNo : this.value.TreDofNo,
          TreDofName : this.value.TreDofName,
          TreDfpID : this.value.TreDfpID,
          TreDfpNo : this.value.TreDfpNo,
          TreDfpName : this.value.TreDfpName,
        }
      }
      this.$store.commit('isLoading',true);
      let self = this;
      ApiService.setHeader();
      ApiService.customRequest(requestData)
      .then(response=>{
        let responsesData = response.data;
        if(responsesData.status === 1){
          if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
          let item = {
            CompanyID: responsesData.data,
            CompanyNo: self.value.CompanyNo,
            CompanyName: self.value.CompanyName,
            Address: self.value.Address,
            Inactive: (self.value.Inactive) ? 1 : 0,
            Tel: self.value.Tel,
            Fax: self.value.Fax,
            Email: self.value.Email,
            Note: self.value.Note,
            ContactName: self.value.ContactName,
            ContactTel: self.value.ContactTel,
            SbiChapterID: self.value.SbiChapterID,
            SbiChapterName: self.value.SbiChapterName,
            ParentID: self.value.ParentID,
            ParentNo: self.value.ParentNo,
            EmployeeID: self.value.EmployeeID,
            ProvinceID: self.value.Province.ProvinceID,
            ProvinceName: self.value.Province.ProvinceName,
            DistrictID: self.value.District.DistrictID,
            DistrictName: self.value.District.DistrictName,
            CommuneID: self.value.Commune.CommuneID,
            CommuneName: self.value.Commune.CommuneName,
            AccessType: self.value.AccessType,
            NumberValue: self.value.NumberValue,
            CompanyCate: self.value.CompanyCate,
            SumCompanyType: self.value.SumCompanyType
          }
          if(self.$route.params.req){
            let indexold =  _.findIndex(self.$route.params.req.itemsArray, {'CompanyID': item.CompanyID});
            let indexParent = null;
            if(indexold >= 0 && self.$route.params.req.itemsArray){
              item.Detail = 1;
              // set for new Parent
              let ParentOldID =  self.$route.params.req.itemsArray[indexold].ParentID;
              self.$route.params.req.itemsArray.splice(indexold, 1);
              if(item.ParentID) {
                indexParent = _.findIndex(self.$route.params.req.itemsArray, {'CompanyID': self.value.ParentID});
                if(indexParent >= 0 ){
                  self.$set(self.$route.params.req.itemsArray[indexParent], 'Detail', 0);
                  self.$set(self.$route.params.req.itemsArray[indexParent], 'Class', 'fa fa-minus-square-o');
                  item.Level = self.$route.params.req.itemsArray[indexParent].Level + 1;
                  self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, indexParent + 1, item);
                }
              }else {
                item.Level = 1;
                self.$route.params.req.itemsArray.push(item);
              }

              // set for ParentOld
              let indexParentOld = _.findIndex(self.$route.params.req.itemsArray, {'CompanyID':  ParentOldID});
              if(indexParentOld >= 0){
                let ParentOld = self.$route.params.req.itemsArray[indexParentOld];
                let child = _.filter(self.$route.params.req.itemsArray, ['ParentID', ParentOld.CompanyID]);
                if(child.length > 0){
                  self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Detail', 0);
                  self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Class', 'fa fa-minus-square-o');
                } else {
                  self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Detail', 1);
                  self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Class', '');
                }
              }
            }
          }
          self.isForm = false;
          self.$bvToast.toast('Bản ghi đã được cập nhật', {
            variant: 'success',
            solid: true
          })
        } else if(responsesData.status === 4){
          Swal.fire(
            'Lỗi',
            'Không được sửa bản ghi Tổng hợp',
            'error'
          )
        } else if(responsesData.status === 5){
          Swal.fire(
            'Lỗi',
            'Cấp quản lý Huyện hoặc Xã : nhập cấp đơn vị cha!',
            'error'
          )
        } else {
          let htmlErrors = __.renderErrorApiHtml(responsesData.data);
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

    },
    onBackToList(message = '') {

      let self = this;
      let params = (this.$route.params.req) ? this.$route.params.req:{};
      let query = this.$route.query;
      query.isBackToList = true;
      if (_.isString(message)) {
        params.message = message;
        this.$router.push({
          name: ViewRouter,
          query: query,
          params: {id: self.idParams, req: params, message: 'Bản ghi đã được cập nhật!'}
        });
      } else {
        this.$router.push({
          name: ListRouter,
          query: query,
          params: params
        });
      }
    },
  }
}
</script>
