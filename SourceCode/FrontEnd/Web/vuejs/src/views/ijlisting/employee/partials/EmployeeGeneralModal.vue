<template>
  <div>
    <a class="ij-a-icon" @click="showModal" title="Chi tiết">
      <i class="fa fa-external-link ij-icon"></i>
    </a>
    <b-modal scrollable ref="modal" id="modal-form-input-employee-general" size="xl">
      <template v-slot:modal-title>
        <i class="fa fa-edit" v-if="isForm"></i><i class="icon-eye icon" v-else></i> {{title}}
      </template>
      <employee-general-form v-model="value" :companyOptions="companyOptions" :employeeOptions="employeeOptions" v-if="isForm" :PositionOption="PositionOption"></employee-general-form>
      <employee-general-view :isDetail="true" v-model="value" v-else></employee-general-view>
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
import EmployeeGeneralView from "./EmployeeGeneralView";
import EmployeeGeneralForm from "./EmployeeGeneralForm";

export default {
  name: 'EmployeeGeneralModal',
  props: ['value','title','PositionOption'],
  components: {
    EmployeeGeneralView,
    EmployeeGeneralForm
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
      let updateAPI = 'listing/api/employee/update/';
      let requestData = {
        method: 'post',
        url: updateAPI + this.value.EmployeeID,
        data: {
          EmployeeID: this.value.EmployeeID,
          EmployeeNo: this.value.EmployeeNo,
          FirstName: this.value.FirstName,
          MiddleName: this.value.MiddleName,
          LastName: this.value.LastName,
          EmployeeName: this.value.EmployeeName,
          BirthDay: this.value.BirthDay,
          CitizenIdNo: this.value.CitizenIdNo,
          CitizenIdDate: this.value.CitizenIdDate,
          CitizenIdAt: this.value.CitizenIdAt,
          OfficePhone: this.value.OfficePhone,
          HandPhone: this.value.HandPhone,
          FacebookID: this.value.FacebookID,
          TwitterID: this.value.TwitterID,
          SkypeID: this.value.SkypeID,
          ZaloID: this.value.ZaloID,
          DepartmentID: this.value.DepartmentID,
          DepartmentNo: this.value.DepartmentNo,
          DepartmentName: this.value.DepartmentName,
          PositionName: this.value.PositionName,
          Note: this.value.Note,
          Email: this.value.Email,
          CompanyID: this.value.CompanyID,
          CompanyName : this.value.CompanyName,
          Inactive: (this.value.Inactive) ? 1 : 0,
          EmployeeCate: this.value.EmployeeCate,
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
  },
  watch: {

  }
}
</script>
