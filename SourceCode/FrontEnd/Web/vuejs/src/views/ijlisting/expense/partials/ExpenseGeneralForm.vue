<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên: </div>
      <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.ExpenseName" type="text" id="ExpenseName" class="form-control" placeholder="Tên khoản chi" name="ExpenseName"/>
      </div>
      <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.ExpenseNo" class="form-control" placeholder="Mã số" :disabled="!value.Detail"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" >Là mục con của</label>
      <div class="col-md-17" v-if="value.Detail">
        <IjcoreModalSysTemParent
          v-model="value"
          :title="'Khoản chi'"
          :api="'/listing/api/common/list'"
          :table="'expense'"
          :fieldName="'ExpenseName'"
          :fieldNo="'ExpenseNo'"
          :fieldID="'ExpenseID'"
          :placeholderInput="'Chọn khoản chi cha'"
          :placeholderSearch="'Nhập khoản chi'"
          :columnID="'ExpenseID'"
          :columnNo="'ExpenseNo'"
          :columnName="'ExpenseName'">
        </IjcoreModalSysTemParent>
      </div>
      <div class="col-md-17" v-if="!value.Detail">
        <input  type="text" v-model="value.ParentName" class="form-control" :disabled="!value.Detail" />
      </div>
      <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span >Mã số</span>
        <input  type="text" v-model="value.ParentNo" class="form-control" placeholder="Mã số" :disabled="!value.Detail" />
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3">Loại khoản chi: </label>
      <div class="col-md-21">
        <expense-modal-search-input-catelist v-model="value.ExpenseCate" title-modal="Loại khoản chi" placeholder="Loại khoản chi">
        </expense-modal-search-input-catelist>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Đơn vị tính: </label>
      <div class="col-md-9 mb-3 mb-sm-0">
        <ExpenseModalSearchUom v-model="value" :title="'Đơn vị'" :table="'uom'" :api="'/listing/api/common/list'" :fieldName="'UomName'"></ExpenseModalSearchUom>
      </div>
      <label class="col-md-3 m-0">Mục - Tiểu mục: </label>
      <div class="col-md-9 mb-3 mb-sm-0">
        <ijcore-modal-search-listing
          v-model="value" :title="'Mục - Tiểu mục'" :table="'sbi_item'" :api="'/listing/api/common/list'"
          :fieldID="'SbiItemID'" :fieldNo="'SbiItemNo'" :fieldName="'SbiItemName'"
          :fieldAssignID="'SbiItemID'" :fieldAssignNo="'SbiItemNo'" :fieldAssignName="'SbiItemName'"
        >
        </ijcore-modal-search-listing>
      </div>
    </div>

    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Cân đối ngân sách</label>
      <div class="col-md-9">
        <b-form-select v-model="value.BudgetBalanceType" :options="BudgetBalanceTypeOption"></b-form-select>
      </div>
      <label class="col-md-3 m-0">Loại NSNN</label>
      <div class="col-md-9">
        <b-form-select v-model="value.BudgetStateType " :options="BudgetStateTypeOption"></b-form-select>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">CTDT</label>
      <div class="col-md-9 mb-3 mb-sm-0">
        <ijcore-modal-search-listing
          v-model="value" :title="'Chỉ tiêu dự toán'" :table="'norm'" :api="'/listing/api/common/list'"
          :fieldID="'NormID'" :fieldNo="'NormNo'" :fieldName="'NormName'"
          :fieldAssignID="'NormID'" :fieldAssignNo="'NormNo'" :fieldAssignName="'NormName'"
        >
        </ijcore-modal-search-listing>
      </div>
      <label class="col-md-3">Lĩnh vực chi</label>
      <div class="col-md-9">
        <ijcore-modal-search-listing
          v-model="value" :title="'Chỉ tiêu định mức dự toán'" :table="'sector'" :api="'/listing/api/common/list'"
          :fieldID="'SectorID'" :fieldNo="'SectorNo'" :fieldName="'SectorName'"
          :fieldAssignID="'SectorID'" :fieldAssignNo="'SectorNo'" :fieldAssignName="'SectorName'"
        >
        </ijcore-modal-search-listing>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3" for="Note">Ghi chú: </label>
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
import ExpenseModalSearchUom from "./ExpenseModalSearchUom";
import ApiService from "@/services/api.service";
import ExpenseModalSearchInputCatelist from "./ExpenseModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";
import IjcoreModalSysTemParent from "../../../../components/IjcoreModalSystemParent";


export default {
  name: 'ExpenseGeneralForm',
  props: ['value','expenseOptions','uomOptions'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalSearchInput,
    ExpenseModalSearchUom,
    ExpenseModalSearchInputCatelist,
    IjcoreModalSearchListing,
    IjcoreModalSysTemParent
  },
  data(){
    return{
      BudgetBalanceTypeOption: {
        '1': 'Trong CĐNS',
        '2': 'Ngoài CĐNS',
      },
      BudgetStateTypeOption: {
        '1': 'Trong ngân sách',
        '2': 'Ngoài ngân sách',
      },
    }
  },
  methods: {
    changeUserContact(){
      let uom = _.find(this.uomOptions, ['id', Number(this.value.uomID)]);
      if(uom){
        this.value.contactName = uom.text;
      }
    }
  },
  watch: {

  }
}
</script>
