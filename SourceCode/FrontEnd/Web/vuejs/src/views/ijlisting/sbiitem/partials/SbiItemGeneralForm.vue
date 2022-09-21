<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.SbiItemName" type="text" id="SbiItemName" class="form-control" placeholder="Tên chương" name="SbiItemName"/>
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.SbiItemNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Là mục con của</label>
      <div class="col-md-9">
        <IjcoreModalParent v-model="value" :title="'Mục - Tiểu mục'" :api="'/listing/api/common/get-parent'" :table="'sbi_item'" :fieldID="'SbiItemID'" :fieldNo="'SbiItemNo'" :fieldName="'SbiItemName'" :currentID="value.SbiItemID" :placeholderInput="'Chọn mục - tiểu mục cha'" :placeholderSearch="'Nhập tên mục - tiểu mục cha'" :specialTable="'SbiItem'">
        </IjcoreModalParent>
      </div>
      <label class="col-md-3 m-0">Quyền truy cập</label>
      <div class="col-md-9">
        <b-form-select v-model="value.AccessType" :options="AccessTypeOptions" id="item-uom">
        </b-form-select>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Loại</label>
      <div class="col-md-9">
        <b-form-select v-model="value.SbiItemType" :options="value.ItemTypeOption"></b-form-select>
      </div>
      <label class="col-md-3 m-0">Nhóm</label>
      <div class="col-md-9">
        <b-form-select v-model="value.SbiItemGroup" :options="value.ItemGroupOption" id="item-uom">
        </b-form-select>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Khoản thu</label>
      <div class="col-md-9">
        <ijcore-modal-search-listing
          v-model="value" :title="'Khoản thu'" :table="'revenue'" :api="'/listing/api/common/list'"
          :fieldID="'RevenueID'" :fieldNo="'RevenueID'" :fieldName="'RevenueName'"
          :fieldAssignID="'RevenueID'" :fieldAssignNo="'RevenueID'" :fieldAssignName="'RevenueName'"
        >
        </ijcore-modal-search-listing>
      </div>
      <label class="col-md-3 m-0">Khoản chi</label>
      <div class="col-md-9">
        <ijcore-modal-search-listing
          v-model="value" :title="'Khoản chi'" :table="'expense'" :api="'/listing/api/common/list'"
          :fieldID="'ExpenseID'" :fieldNo="'ExpenseNo'" :fieldName="'ExpenseName'"
          :fieldAssignID="'ExpenseID'" :fieldAssignNo="'ExpenseNo'" :fieldAssignName="'ExpenseName'"
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
import IjcoreModalListing from "@/components/IjcoreModalListing";
import IjcoreModalParent from "@/components/IjcoreModalParent";
import ApiService from "@/services/api.service";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";

export default {
  name: 'SbiItemGeneralForm',
  props: ['value'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalParent,
    IjcoreModalSearchInput,
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
