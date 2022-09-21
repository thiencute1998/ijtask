<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.ContractName" type="text" id="ContractName" class="form-control" placeholder="Tên hợp đồng" name="ContractName"/>
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.ContractNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3">Loại hợp đồng</label>
      <div class="col-md-9">
        <contract-modal-search-input-catelist v-model="value.ContractCate" title-modal="Loại hợp đồng" placeholder="Loại hợp đồng">
        </contract-modal-search-input-catelist>
      </div>
      <label class="col-md-3 m-0">Quyền truy cập</label>
      <div class="col-md-9">
        <b-form-select v-model="value.AccessType" :options="AccessTypeOptions" id="item-uom">
        </b-form-select>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Ngày ký</label>
      <div class="col-md-5 mb-3 mb-sm-0">
        <IjcoreDatePicker v-model="value.ContractDate"></IjcoreDatePicker>
      </div>
      <label class="col-md-3 m-0">Ngày hiệu lực</label>
      <div class="col-md-5">
        <IjcoreDatePicker v-model="value.EffectiveDate"></IjcoreDatePicker>
      </div>
      <label class="col-md-3 m-0">Ngày kết thúc</label>
      <div class="col-md-5">
        <IjcoreDatePicker v-model="value.FinishDate"></IjcoreDatePicker>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Giá trị HĐ</label>
      <div class="col-md-5">
        <ijcore-number v-model="value.ContractAmount"></ijcore-number>
      </div>
      <label class="col-md-3 m-0">Dự án</label>
      <div class="col-md-5">
        <ijcore-modal-search-listing
          v-model="value" :title="'Dự án'" :table="'project'" :api="'/listing/api/common/list'"
          :fieldID="'ProjectID'" :fieldNo="'ProjectNo'" :fieldName="'ProjectName'"
          :fieldAssignID="'ProjectID'" :fieldAssignNo="'ProjectNo'" :fieldAssignName="'ProjectName'"
        >
        </ijcore-modal-search-listing>
      </div>
      <label class="col-md-3 m-0">Người ký</label>
      <div class="col-md-5 mb-3 mb-sm-0">
        <ijcore-modal-search-listing
          v-model="value" :title="'Người ký'" :table="'employee'" :api="'/listing/api/common/list'"
          :fieldID="'EmployeeID'" :fieldNo="'EmployeeNo'" :fieldName="'EmployeeName'"
          :fieldAssignID="'EmployeeID'" :fieldAssignNo="'EmployeeNo'" :fieldAssignName="'EmployeeName'"
        >
        </ijcore-modal-search-listing>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Nhà cung cấp</label>
      <div class="col-md-5">
        <ijcore-modal-search-listing
          v-model="value" :title="'Nhà cung cấp'" :table="'vendor'" :api="'/listing/api/common/list'"
          :fieldID="'VendorID'" :fieldNo="'VendorNo'" :fieldName="'VendorName'"
          :fieldAssignID="'VendorID'" :fieldAssignNo="'VendorNo'" :fieldAssignName="'VendorName'"
        >
        </ijcore-modal-search-listing>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3" for="Note">Ghi chú</label>
      <div class="col-md-21">
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
import ContractModalSearchInputCatelist from "./ContractModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import IjcoreNumber from "@/components/IjcoreNumber";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";

export default {
  name: 'ContractGeneralForm',
  props: ['value','contractOptions','employeeOptions'],
  components: {
    Select2,
    IjcoreDatePicker,
    IjcoreModalListing,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    ContractModalSearchInputCatelist,
    IjcoreNumber,
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
    changeUserContact(){
      let employee = _.find(this.employeeOptions, ['id', Number(this.value.employeeID)]);
      if(employee){
        this.value.contactName = employee.text;
      }
    }
  },
  watch: {

  }
}
</script>
