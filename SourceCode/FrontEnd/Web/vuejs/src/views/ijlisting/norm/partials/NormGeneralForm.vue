<template>
  <div class="norm-general-form">
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.NormName" type="text" id="NormName" class="form-control" placeholder="Tên khoản thu" name="NormName"/>
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.NormNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" title="Loại chỉ tiêu dự toán">Loại CTDT</label>
      <div class="col-md-13">
        <norm-modal-search-input-catelist
          v-model="value.NormCate"
          title-modal="Loại chỉ tiêu dự toán"
          placeholder="Loại chỉ tiêu dự toán"
        ></norm-modal-search-input-catelist>
      </div>
      <label class="col-md-3 m-0">Đơn vị tính</label>
      <div class="col-md-5 mb-3 mb-sm-0">
        <ijcore-modal-search-listing
          v-model="value" :title="'Đơn vị tính'" :table="'uom'" :api="'/listing/api/common/list'"
          :fieldID="'UomID'" :fieldNo="'UomNo'" :fieldName="'UomName'"
          :fieldAssignID="'UomID'" :fieldAssignNo="'UomNo'" :fieldAssignName="'UomName'"
        >
        </ijcore-modal-search-listing>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Thu/Chi</label>
      <div class="col-md-5">
        <b-form-select v-model="value.NormType" :options="NormTypeOption"></b-form-select>
      </div>
      <label class="col-md-3 m-0" v-if="value.NormType == 1 || value.NormType == 3">Khoản thu</label>
      <div class="col-md-5 mb-3 mb-sm-0" v-if="value.NormType == 1 || value.NormType == 3">
        <ijcore-modal-search-listing
          v-model="value" :title="'Khoản thu'" :table="'revenue'" :api="'/listing/api/common/list'"
          :fieldID="'RevenueID'" :fieldNo="'RevenueNo'" :fieldName="'RevenueName'"
          :fieldAssignID="'RevenueID'" :fieldAssignNo="'RevenueNo'" :fieldAssignName="'RevenueName'"
        >
        </ijcore-modal-search-listing>
      </div>
      <label class="col-md-3 m-0" v-if="value.NormType == 2 || value.NormType == 3">Khoản chi</label>
      <div class="col-md-5 mb-3 mb-sm-0" v-if="value.NormType == 2 || value.NormType == 3">
        <ijcore-modal-search-listing
          v-model="value" :title="'Khoản chi'" :table="'expense'" :api="'/listing/api/common/list'"
          :fieldID="'ExpenseID'" :fieldNo="'ExpenseNo'" :fieldName="'ExpenseName'"
          :fieldAssignID="'ExpenseID'" :fieldAssignNo="'ExpenseNo'" :fieldAssignName="'ExpenseName'"
        >
        </ijcore-modal-search-listing>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3" for="Comment">Ghi chú</label>
      <div class="col-md-21">
        <textarea v-model="value.Comment" class="form-control" id="Comment" rows="3" placeholder="Ghi chú" name="Comment"></textarea>
      </div>
    </div>
<!--    <norm-map-form v-model="value" @changed="onAddLine"></norm-map-form>-->
<!--    <table class="table b-table table-sm table-bordered table-editable">-->
<!--      <thead>-->
<!--      <tr>-->
<!--        <th scope="col" style="width: 10%" class="text-center">Bảng chỉ tiêu</th>-->
<!--        <th scope="col" style="width: 10%" class="text-center">Tên chỉ tiêu</th>-->
<!--        <th scope="col" style="width: 1%" class="text-center"></th>-->
<!--      </tr>-->
<!--      </thead>-->
<!--      <tbody>-->
<!--      <tr v-for="(field, key) in getNormMap">-->
<!--        <td>-->
<!--          <span :title="field.NormTableName">{{field.NormTableName}}</span>-->
<!--        </td>-->
<!--        <td>-->
<!--          <span :title="field.NormTableItemName">{{field.NormTableItemName}}</span>-->
<!--        </td>-->
<!--        <td class="text-center">-->
<!--          <i @click="onDeleteLine(key)" class="fa fa-trash-o" title="Xóa"-->
<!--             style="font-size: 18px; cursor: pointer"></i>-->
<!--        </td>-->
<!--      </tr>-->
<!--      </tbody>-->

<!--    </table>-->
  </div>
</template>
<script>
import Select2 from "v-select2-component";
import Swal from "sweetalert2";
import IjcoreModalListing from "@/components/IjcoreModalListing";
import IjcoreModalParent from "@/components/IjcoreModalParent";
import ApiService from "@/services/api.service";
import NormModalSearchInputCatelist from "./NormModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import NormMapForm from './NormMapForm';
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";

export default {
  name: 'NormGeneralForm',
  props: ['value','revenueOptions','uomOptions'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    NormModalSearchInputCatelist,
    NormMapForm,
    IjcoreModalSearchListing,
  },
  data(){
    return{
      NormTypeOption: {
        '1': 'Thu',
        '2': 'Chi',
      },
    }
  },
  computed: {
    getNormMap(){
      return _.sortBy(this.value.NormMap, ['NormTableID', 'NormTableName']);
    }
  },
  methods: {
    onDeleteLine(key){
      this.value.NormMap.splice(key,1);
    },
    onAddLine(data){
      let self = this;
      data.map(function (val, key) {
        let isExist = 0;
        _.forEach(self.value.NormMap, function (v, k) {
          if(v.NormTableItemID == val.NormTableItemID){
            isExist = 1;
          }
        });
        if(isExist == 0){
          self.value.NormMap.push({
            NormTableID: val.NormTableID,
            NormTableName: val.NormTableName,
            NormTableNo: val.NormTableNo,
            NormTableItemID : val.NormTableItemID,
            NormTableItemNo: val.NormTableItemNo,
            NormTableItemName: val.NormTableItemName,
            IsCheck: false
          });
        }
      });
    }
  },
  watch: {

  }
}
</script>
<style lang="css">
.norm-general-form .table tbody td{
  height: calc(1.5em + 0.55rem + 2px);
  font-size: 0.875rem;
  padding: 0.275rem 0.75rem;
}
</style>
