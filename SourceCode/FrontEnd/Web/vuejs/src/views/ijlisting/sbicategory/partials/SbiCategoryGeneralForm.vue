<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên: </div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.SbiCategoryName" type="text" id="SbiCategoryName" class="form-control" placeholder="Tên loại khoản" name="SbiCategoryName"/>
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.SbiCategoryNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Là mục con của: </label>
      <div class="col-md-15">
        <IjcoreModalSystemParent
          v-model="value"
          :title="'Loại khoản'"
          :api="'/listing/api/common/list'"
          :table="'sbi_category'"
          :fieldName="'SbiCategoryName'"
          :fieldNo="'SbiCategoryNo'"
          :fieldID="'SbiCategoryID'"
          :placeholderInput="'Chọn loại khoản cha'"
          :placeholderSearch="'Nhập tên loại khoản'"
          :columnID="'SbiCategoryID'"
          :columnNo="'SbiCategoryNo'"
          :columnName="'SbiCategoryName'"
        ></IjcoreModalSystemParent>
      </div>
      <div v-if="value.ParentID" class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.ParentNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3">Loại - Loại khoản: </label>
      <div class="col-md-21">
        <sbi-category-modal-search-input-catelist v-model="value.SbiCategoryCate" title-modal="Loại - Loại khoản" placeholder="Loại - loại khoản">
        </sbi-category-modal-search-input-catelist>
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
import IjcoreModalParent from "@/components/IjcoreModalParent";
import SbiCategoryModalSearchUom from "./SbiCategoryModalSearchUom";
import ApiService from "@/services/api.service";
import SbiCategoryModalSearchInputCatelist from "./SbiCategoryModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import IjcoreModalSystemParent from "@/components/IjcoreModalSystemParent";

export default {
  name: 'SbiCategoryGeneralForm',
  props: ['value','sbiCategoryOptions','uomOptions'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    SbiCategoryModalSearchUom,
    SbiCategoryModalSearchInputCatelist,
    IjcoreModalSystemParent
  },
  data(){
    return{
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
