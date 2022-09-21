<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.DirectionName" type="text" id="DirectionName" class="form-control" placeholder="Tên chỉ thị" name="DirectionName"/>
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.DirectionNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Là con của</label>
      <div class="col-md-15">
        <IjcoreModalParent v-model="value" :title="'Chỉ thị'" :api="'/listing/api/common/list'" :table="'direction'" :fieldID="'DirectionID'" :fieldNo="'DirectionNo'" :fieldName="'DirectionName'" :currentID="value.DirectionID" :placeholderInput="'Chọn chỉ thị cha'" :placeholderSearch="'Nhập tên chỉ thị'">
        </IjcoreModalParent>
      </div>
      <div v-if="value.ParentID" class="col-lg-6 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.ParentNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Loại chỉ thị</label>
      <div class="col-md-21">
        <direction-modal-search-input-catelist
          v-model="value.DirectionCate"
          title-modal="Loại chỉ thị"
          placeholder="Loại chỉ thị"
        ></direction-modal-search-input-catelist>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" >Cơ quan ban hành</label>
      <div class="col-md-5">
        <IjcoreModalSearchCompany v-model="value.CompanyIssuedName" :fieldCateList="'39'" :fieldCateValue="[]" :title="'Cơ quan ban hành'" :table="'company'" :api="'/listing/api/company/get-cate-value'"  :placeholderInput="'Cơ quan ban hành'" @changeCompanyIssued="updateCompanyIssued"></IjcoreModalSearchCompany>
      </div>
      <label class="col-md-3 m-0">Người ký</label>
      <div class="col-md-5 mb-3 mb-sm-0">
        <input v-model="value.SignerIssuedName" type="text" class="form-control" placeholder="Người ký">
      </div>
      <label class="col-md-3 m-0">Quyền truy cập</label>
      <div class="col-md-5">
        <b-form-select v-model="value.AccessType" :options="AccessTypeOptions" id="item-uom">
        </b-form-select>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Ngày ban hành</label>
      <div class="col-md-5 mb-3 mb-sm-0">
        <IjcoreDatePicker v-model="value.DirectionDate"></IjcoreDatePicker>
      </div>
      <label class="col-md-3 m-0">Đã đóng</label>
      <div class="col-md-5">
        <b-form-checkbox v-model="value.Closed"></b-form-checkbox>
      </div>
      <label class="col-md-3 m-0">Ngày đóng</label>
      <div class="col-md-5">
        <IjcoreDatePicker v-model="value.ClosedDate"></IjcoreDatePicker>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3" for="Description">Mô tả</label>
      <div class="col-md-21">
        <textarea v-model="value.Description" class="form-control" id="Description" rows="3" placeholder="Mô tả" name="Description"></textarea>
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
import DirectionModalSearchInputCatelist from "./DirectionModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import IjcoreModalSearchCompany from "../../../../components/IjcoreModalSearchCompany";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";

export default {
  name: 'DirectionGeneralForm',
  props: ['value','directionOptions','employeeOptions'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    DirectionModalSearchInputCatelist,
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
    }
  },
  methods: {
    updateCompanyIssued(data){
      this.value.CompanyIssuedID = data.CompanyID;
      this.value.CompanyIssuedName = data.CompanyName;
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
          table: 'direction',
          ParentID: this.value.ParentID,
        }
      }
      self.$store.commit('isLoading',true)
      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response=>{
          let responseData = response.data;
          if(responseData.status === 1){
            this.value.DirectionNo = responseData.data;
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
