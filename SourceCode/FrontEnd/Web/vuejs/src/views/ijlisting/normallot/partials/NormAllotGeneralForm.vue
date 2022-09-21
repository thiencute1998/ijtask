<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.NormAllotName" type="text" id="NormAllotName" class="form-control" placeholder="Tên TCPBDT" name="NormAllotName"/>
      </div>
      <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.NormAllotNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Là mục con của</label>
      <div class="col-md-17">
        <IjcoreModalSystemParent
          v-model="value"
          :title="'Tiêu chí phân bổ  dự toán'"
          :api="'/listing/api/common/list'"
          :table="'norm_allot'"
          :fieldName="'NormAllotName'"
          :fieldNo="'NormAllotNo'"
          :fieldID="'NormAllotID'"
          :placeholderInput="'Chọn Tiêu chí phân bổ dự toán cha'"
          :placeholderSearch="'Nhập Tiêu chí phân bổ dự toán'"
          :columnID="'NormAllotID'"
          :columnNo="'NormAllotNo'"
          :columnName="'NormAllotName'">
        </IjcoreModalSystemParent>
      </div>
      <div v-if="value.ParentID" class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.ParentNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Đơn vị tính</label>
      <div class="col-md-5 mb-3 mb-sm-0">
        <ijcore-modal-search-listing
          v-model="value" :title="'Đơn vị tính'" :table="'uom'" :api="'/listing/api/common/list'"
          :fieldID="'UomID'" :fieldNo="'UomNo'" :fieldName="'UomName'"
          :fieldAssignID="'UomID'" :fieldAssignNo="'UomNo'" :fieldAssignName="'UomName'"
        ></ijcore-modal-search-listing>
      </div>
      <label class="col-md-3">Ngày hiệu lực</label>
      <div class="col-md-5 mb-sm-0">
        <IjcoreDatePicker v-model="value.EffectiveDate" ></IjcoreDatePicker>
      </div>
      <label class="col-md-3">Ngày  hết hiệu lực</label>
      <div class="col-md-5 mb-sm-0">
        <IjcoreDatePicker v-model="value.ExpirationDate" ></IjcoreDatePicker>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3">Loại TCPBDT</label>
      <div class="col-md-21">
        <norm-allot-modal-search-input-catelist v-model="value.NormAllotCate" title-modal="Loại tiêu chí phân bổ dự toán" placeholder="Loại tiêu chí phân bổ dự toán">
        </norm-allot-modal-search-input-catelist>
      </div>
    </div>
  </div>
</template>
<script>
import Select2 from "v-select2-component";
import Swal from "sweetalert2";
import IjcoreModalListing from "@/components/IjcoreModalListing";
import ApiService from "@/services/api.service";
import NormAllotModalSearchInputCatelist from "./NormAllotModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";
import IjcoreDatePicker from "@/components/IjcoreDatePicker";
import IjcoreModalSystemParent from "../../../../components/IjcoreModalSystemParent";


export default {
  name: 'NormAllotGeneralForm',
  props: ['value','normAllotOptions'],
  components: {
    Select2,
    IjcoreDatePicker,
    IjcoreModalListing,
    IjcoreModalSearchInput,
    NormAllotModalSearchInputCatelist,
    IjcoreModalSearchListing,
    IjcoreModalSystemParent
  },
  data(){
    return{

    }
  },
  methods: {
    changeUserContact(){

    }
  },
  watch: {

  }
}
</script>
