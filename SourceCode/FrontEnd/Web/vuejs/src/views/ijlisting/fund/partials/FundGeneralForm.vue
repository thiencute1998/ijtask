<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.FundName" type="text" id="FundName" class="form-control" placeholder="Tên HTTKHN" name="FundName"/>
      </div>
      <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.FundNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3  m-0">Là con của</label>
      <div class="col-md-17">
        <IjcoreModalSystemParent v-model="value" :title="'Danh mục quỹ'" :api="'/listing/api/common/list'" :table="'fund'" :fieldName="'FundName'" :fieldNo="'FundNo'" :fieldID="'FundID'" :currentID="value.FundID"  :placeholderInput="'Chọn danh mục quỹ cha'" :placeholderSearch="'Nhập tên danh mục quỹ'" :columnID="'FundID'" :columnNo="'FundNo'" :columnName="'FundName'">
        </IjcoreModalSystemParent>
      </div>
      <div v-if="value.ParentID" class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.ParentNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3">Loại</label>
      <div class="col-md-21">
        <fund-modal-search-input-catelist v-model="value.FundCate" title-modal="Loại danh mục quỹ" placeholder="Loại danh mục quỹ">
        </fund-modal-search-input-catelist>
      </div>
    </div>

    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Quyền truy cập</label>
      <div class="col-md-9">
        <b-form-select v-model="value.AccessType" :options="value.AccessTypeOptions" id="item-uom">
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
import FundModalSearchEmployee from "./FundModalSearchEmployee";
import ApiService from "@/services/api.service";
import FundModalSearchInputCatelist from "./FundModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";

export default {
  name: 'FundGeneralForm',
  props: ['value','fundOptions','employeeOptions'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalSystemParent,
    IjcoreModalSearchInput,
    FundModalSearchEmployee,
    FundModalSearchInputCatelist
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
