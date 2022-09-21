<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.AccountName" type="text" id="AccountName" class="form-control" placeholder="Tên HTTK BQLDA" name="AccountName"/>
      </div>
      <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.AccountNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3  m-0">Là mục con của</label>
      <div class="col-md-17">
        <IjcoreModalSystemParent
          v-model="value"
          :title="'Hệ thống tài khoản  ban quản lý dự án'"
          :api="'/listing/api/common/get-parent'"
          :table="'coa_pmu'"
          :fieldName="'AccountName'"
          :fieldNo="'AccountNo'"
          :fieldID="'AccountID'"
          :placeholderInput="'Chọn hệ thống tài khoản ban quản lý dự án cha'"
          :placeholderSearch="'Nhập tên HTTK BQLDA'"
          :columnID="'AccountID'"
          :columnNo="'AccountNo'"
          :columnName="'AccountName'"
          :currentID="value.AccountID"
        ></IjcoreModalSystemParent>
      </div>
      <div v-if="value.ParentID" class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.ParentNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3">Loại</label>
      <div class="col-md-9">
        <coa-pmu-modal-search-input-catelist v-model="value.CoaPmuCate" title-modal="Loại HTTK BQLDA" placeholder="Loại HTTK BQLDA">
        </coa-pmu-modal-search-input-catelist>
      </div>
      <label class="col-md-3 m-0">Quyền truy cập</label>
      <div class="col-md-9">
        <b-form-select v-model="value.AccessType" :options="value.AccessTypeOptions" id="item-uom">
        </b-form-select>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Loại số dư TK</label>
      <div class="col-md-9">
        <b-form-select v-model="value.BalanceType" :options="value.BalanceTypeOptions" id="item-uom">
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
import IjcoreModalSystemParent from "../../../../components/IjcoreModalSystemParent";
import CoaPmuModalSearchEmployee from "./CoaPmuModalSearchEmployee";
import ApiService from "@/services/api.service";
import CoaPmuModalSearchInputCatelist from "./CoaPmuModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";

export default {
  name: 'CoaPmuGeneralForm',
  props: ['value','coaPmuOptions','employeeOptions'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalSystemParent,
    IjcoreModalSearchInput,
    CoaPmuModalSearchEmployee,
    CoaPmuModalSearchInputCatelist
  },
  data(){
    return{

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
