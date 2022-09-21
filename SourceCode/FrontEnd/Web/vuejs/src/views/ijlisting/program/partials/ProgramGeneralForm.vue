<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.ProgramName" type="text" id="ProgramName" class="form-control" placeholder="Tên chương trình mục tiêu" name="ProgramName"/>
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.ProgramNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3">Loại CTMT</label>
      <div class="col-md-21">
        <program-modal-search-input-catelist v-model="value.ProgramCate" title-modal="Loại chương trình mục tiêu" placeholder="Loại chương trình mục tiêu">
        </program-modal-search-input-catelist>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Loại</label>
      <div class="col-md-5 mb-3 mb-sm-0">
        <b-form-select v-model="value.ProgramType" :options="ProgramTypeOption" id="item-uom">
        </b-form-select>
      </div>
      <label class="col-md-3 m-0">Cấp quản lý</label>
      <div class="col-md-5">
        <b-form-select v-model="value.ManagementLevel" :options="ManagementLevelOption" id="item-uom">
        </b-form-select>
      </div>
      <label class="col-md-3 m-0">Quyền truy cập</label>
      <div class="col-md-5">
        <b-form-select v-model="value.AccessType" :options="AccessTypeOptions" id="item-uom">
        </b-form-select>
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
import IjcoreModalListing from "@/components/IjcoreModalListing";
import IjcoreModalParent from "@/components/IjcoreModalParent";
import ApiService from "@/services/api.service";
import ProgramModalSearchInputCatelist from "./ProgramModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";

export default {
  name: 'ProgramGeneralForm',
  props: ['value','programOptions','employeeOptions'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    ProgramModalSearchInputCatelist
  },
  data(){
    return{
      AccessTypeOptions:{
        1: 'Chia sẻ',
        2: 'Công khai',
        3: 'Riêng tư'
      },
      ProgramTypeOption: {
        1: 'CTMT Quốc gia',
        2: 'CTMT Bổ sung'
      },
      ManagementLevelOption: {
        1:  'Trung ương',
        2:  'Tỉnh',
        3:  'Huyện',
        4:  'Xã',
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
