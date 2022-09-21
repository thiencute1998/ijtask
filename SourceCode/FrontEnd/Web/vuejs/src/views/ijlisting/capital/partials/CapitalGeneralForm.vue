<template>
  <div>
    <!--Tên nguồn vốn-->
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.CapitalName" type="text" id="CapitalName" class="form-control" placeholder="Tên nguồn vốn" name="CapitalName"/>
      </div>
      <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.CapitalNo" class="form-control" placeholder="Mã số" :disabled="!value.Detail"/>
      </div>
    </div>
    <!--Nguồn vốn cha-->
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" >Là mục con của</label>
      <div class="col-md-17" v-if="value.Detail">
        <IjcoreModalSysTemParent
          v-model="value"
          :title="'Khoản chi'"
          :api="'/listing/api/common/list'"
          :table="'capital'"
          :fieldName="'CapitalName'"
          :fieldNo="'CapitalNo'"
          :fieldID="'CapitalID'"
          :placeholderInput="'Chọn nguồn vốn cha'"
          :placeholderSearch="'Nhập nguồn vốn'"
          :columnID="'CapitalID'"
          :columnNo="'CapitalNo'"
          :columnName="'CapitalName'">
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
    <!--Loại nguồn vốn-->
    <div class="form-group row align-items-center">
      <label class="col-md-3">Loại nguồn vốn</label>
      <div class="col-md-21">
        <capital-modal-search-input-catelist v-model="value.CapitalCate" title-modal="Loại nguồn vốn" placeholder="Loại nguồn vốn">
        </capital-modal-search-input-catelist>
      </div>
    </div>
    <!--Trong nước hoặc nước ngoài & loại ngân sách nhàn nước-->
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Thuộc</label>
      <div class="col-md-9">
        <b-form-select v-model="value.CapitalInOut" :options="CapitalInOutOption"></b-form-select>
      </div>
      <label class="col-md-3 m-0">Loại vốn NSNN</label>
      <div class="col-md-9">
        <b-form-select v-model="value.BudgetStateType " :options="BudgetStateTypeOption"></b-form-select>
      </div>
    </div>
    <!--ghi chú-->
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
import CapitalModalSearchInputCatelist from "./CapitalModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import IjcoreModalSysTemParent from "@/components/IjcoreModalSystemParent";
export default {
  name: 'CapitalGeneralForm',
  props: ['value','capitalOptions'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    CapitalModalSearchInputCatelist,
    IjcoreModalSysTemParent
  },
  data(){
    return{
      CapitalInOutOption: {
        '1': 'Vốn trong nước',
        '2': 'Vốn ngoài nước',
      },
      BudgetStateTypeOption: {
        '1': 'Trong ngân sách',
        '2': 'Ngoài ngân sách',
      },
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
