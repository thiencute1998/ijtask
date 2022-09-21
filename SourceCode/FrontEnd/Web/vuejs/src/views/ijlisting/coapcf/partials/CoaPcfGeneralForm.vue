<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.AccountName" type="text" id="AccountName" class="form-control" placeholder="Tên HTTK QTDND" name="AccountName"/>
      </div>
      <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.AccountNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3  m-0">Là mục con của</label>
      <div class="col-md-17">
        <IjcoreModalSystemParent v-model="value" :title="'Hệ thống tài khoản  quỹ tín dụng nhân dân'" :api="'/listing/api/common/list'" :table="'coa_pcf'" :fieldID="'AccountID'" :fieldNo="'AccountNo'" :fieldName="'AccountName'" :currentID="value.AccountID" :placeholderInput="'Chọn hệ thống tài khoản  quỹ tín dụng nhân dân cha'" :placeholderSearch="'Nhập tên HTTK QTDND'" :columnID="'AccountID'" :columnNo="'AccountNo'" :columnName="'AccountName'">
        </IjcoreModalSystemParent>
      </div>
      <div v-if="value.ParentID" class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.ParentNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3">Loại</label>
      <div class="col-md-9">
        <coa-pcf-modal-search-input-catelist v-model="value.CoaPcfCate" title-modal="Loại HTTK QTDND" placeholder="Loại HTTK QTDND">
        </coa-pcf-modal-search-input-catelist>
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
import CoaPcfModalSearchEmployee from "./CoaPcfModalSearchEmployee";
import ApiService from "@/services/api.service";
import CoaPcfModalSearchInputCatelist from "./CoaPcfModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";

export default {
  name: 'CoaPcfGeneralForm',
  props: ['value','coaPcfOptions','employeeOptions'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalSystemParent,
    IjcoreModalSearchInput,
    CoaPcfModalSearchEmployee,
    CoaPcfModalSearchInputCatelist
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
