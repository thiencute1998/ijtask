<template>
  <div>
    <a class="ij-a-icon" @click="showModal" title="Chi tiết">
      <i class="fa fa-external-link ij-icon"></i>
    </a>
    <b-modal scrollable ref="modal" id="modal-form-input-customer-general" size="xl">
      <template v-slot:modal-title>
        <i class="fa fa-edit" v-if="isForm"></i><i class="icon-eye icon" v-else></i> {{title}}
      </template>
      <customer-general-form v-model="value" :customerOptions="customerOptions" :employeeOptions="employeeOptions" v-if="isForm"></customer-general-form>
      <customer-general-view :isDetail="true" v-model="value" v-else></customer-general-view>
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
import CustomerGeneralView from "./CustomerGeneralView";
import CustomerGeneralForm from "./CustomerGeneralForm";

export default {
  name: 'CustomerGeneralModal',
  props: ['value','title'],
  components: {
    CustomerGeneralView,
    CustomerGeneralForm
  },
  data() {
    return{
      isForm: false,
      employeeOptions: [],
      customerOptions: [],
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
      let updateAPI = 'listing/api/customer/update/';
      let requestData = {
        method: 'post',
        url: updateAPI + this.value.CustomerID,
        data: {
          CustomerNo: this.value.CustomerNo,
          CustomerName: this.value.CustomerName,
          Address : this.value.Address,
          BillTo : this.value.BillTo,
          ShipTo: this.value.ShipTo,
          TaxCode: this.value.TaxCode,
          BankAccount : this.value.BankAccount,
          BankName : this.value.BankName,
          OfficePhone: this.value.OfficePhone,
          Fax: this.value.Fax,
          Email : this.value.Email,
          Website : this.value.Website,
          ProvinceID: this.value.Province.ProvinceID,
          ProvinceName: this.value.Province.ProvinceName,
          DistrictID: this.value.District.DistrictID,
          DistrictName: this.value.District.DistrictName,
          CommuneID: this.value.Commune.CommuneID,
          CommuneName: this.value.Commune.CommuneName,
          AccessType: this.value.AccessType,
          isVendor: this.value.isVendor,
          Inactive: (this.value.Inactive) ? 1 : 0,
          CustomerCate: this.value.CustomerCate,
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
