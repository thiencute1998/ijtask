<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.SbrSectorName" type="text" id="SbrSectorName" class="form-control" placeholder="Tên lĩnh vực thu" name="SbrSectorName"/>
      </div>
      <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.SbrSectorNo" class="form-control" placeholder="Mã số" :disabled="!value.Detail"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Là mục con: </label>
      <div class="col-md-17" v-if="value.Detail">
        <IjcoreModalSysTemParent v-model="value" :title="'Lĩnh vực thu cha'" :api="'/listing/api/common/list'" :table="'sbr_sector'" :fieldName="'SbrSectorName'" :fieldNo="'SbrSectorNo'" :fieldID="'SbrSectorID'" :placeholderInput="'Chọn lĩnh vực thu cha'" :placeholderSearch="'Nhập tên lĩnh vực thu'" :columnID="'SbrSectorID'" :columnNo="'SbrSectorNo'" :columnName="'SbrSectorName'">
        </IjcoreModalSysTemParent>
      </div>
      <div class="col-md-17" v-if="!value.Detail">
        <input  type="text" v-model="value.ParentName" class="form-control" :disabled="!value.Detail" />
      </div>
      <div v-if="value.ParentID" class="col-lg-4 col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.ParentNo" class="form-control" placeholder="Mã số" :disabled="!value.Detail"/>
      </div>
    </div>

    <div class="form-group row align-items-center">
      <label class="col-md-3">Loại lĩnh vực thu</label>
      <div class="col-md-21">
        <sbr-sector-modal-search-input-catelist
          v-model="value.SbrSectorCate"
          :listApi="'listing/api/sbr-sector/get-sbr-sector-cate-list'"
          title-modal="Loại lĩnh vực thu"
          placeholder="Loại lĩnh vực thu"
        ></sbr-sector-modal-search-input-catelist>
      </div>
    </div>

    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Chương</label>
      <div class="col-md-5 mb-3 mb-sm-0">
        <ijcore-modal-search-listing
          v-model="value" :title="'chương'" :table="'sbi_chapter'" :api="'/listing/api/common/list'"
          :fieldID="'SbiChapterID'" :fieldNo="'SbiChapterNo'" :fieldName="'SbiChapterName'"
          :fieldAssignID="'SbiChapterID'" :fieldAssignNo="'SbiChapterNo'" :fieldAssignName="'SbiChapterName'"
        >
        </ijcore-modal-search-listing>
      </div>

    </div>

    <div class="form-group row align-items-center">
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
import SbrSectorModalSearchInputCatelist from "./SbrSectorModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";
import IjcoreModalSysTemParent from "@/components/IjcoreModalSystemParent";


export default {
  name: 'SbrSectorGeneralForm',
  props: ['value'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    SbrSectorModalSearchInputCatelist,
    IjcoreModalSearchListing,
    IjcoreModalSysTemParent
  },
  data(){
    return{
      SbrSectorTypeOption: {
        '1': 'Thu',
        '2': 'Chi',
      },
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
