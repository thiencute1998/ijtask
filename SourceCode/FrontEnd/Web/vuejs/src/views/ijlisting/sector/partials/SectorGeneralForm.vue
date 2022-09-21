<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.SectorName" type="text" id="SectorName" class="form-control" placeholder="Tên lĩnh vực chi" name="SectorName"/>
      </div>
      <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.SectorNo" class="form-control" placeholder="Mã số" :disabled="!value.Detail" />
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Là mục con: </label>
      <div class="col-md-17" v-if="value.Detail">
        <IjcoreModalSysTemParent v-model="value" :title="'Lĩnh vực thu cha'" :api="'/listing/api/common/list'" :table="'sector'" :fieldName="'SectorName'" :fieldNo="'SectorNo'" :fieldID="'SectorID'" :placeholderInput="'Chọn lĩnh vực thu cha'" :placeholderSearch="'Nhập tên lĩnh vực thu'" :columnID="'SectorID'" :columnNo="'SectorNo'" :columnName="'SectorName'">
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
      <label class="col-md-3">Loại lĩnh vực chi</label>
      <div class="col-md-21">
        <sector-modal-search-input-catelist v-model="value.SectorCate" title-modal="Loại lĩnh vực chi" placeholder="Loại lĩnh vực chi">
        </sector-modal-search-input-catelist>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Thu/Chi</label>
      <div class="col-md-5">
        <b-form-select v-model="value.SectorType" :options="SectorTypeOption"></b-form-select>
      </div>
      <label class="col-md-3 m-0" v-if="value.SectorType == 1">Chương</label>
      <div class="col-md-5 mb-3 mb-sm-0" v-if="value.SectorType == 1">
        <ijcore-modal-search-listing
          v-model="value" :title="'chương'" :table="'sbi_chapter'" :api="'/listing/api/common/list'"
          :fieldID="'SbiChapterID'" :fieldNo="'SbiChapterNo'" :fieldName="'SbiChapterName'"
          :fieldAssignID="'SbiChapterID'" :fieldAssignNo="'SbiChapterNo'" :fieldAssignName="'SbiChapterName'"
        >
        </ijcore-modal-search-listing>
      </div>
      <label class="col-md-3 m-0" v-if="value.SectorType == 2">Loại khoản</label>
      <div class="col-md-5 mb-3 mb-sm-0" v-if="value.SectorType == 2">
        <ijcore-modal-search-listing
          v-model="value" :title="'loại khoản'" :table="'sbi_category'" :api="'/listing/api/common/list'"
          :fieldID="'SbiCategoryID'" :fieldNo="'SbiCategoryNo'" :fieldName="'SbiCategoryName'"
          :fieldAssignID="'SbiCategoryID'" :fieldAssignNo="'SbiCategoryNo'" :fieldAssignName="'SbiCategoryName'"
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
import SectorModalSearchInputCatelist from "./SectorModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";
import IjcoreModalSysTemParent from "../../../../components/IjcoreModalSystemParent";

export default {
  name: 'SectorGeneralForm',
  props: ['value'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    SectorModalSearchInputCatelist,
    IjcoreModalSearchListing,
    IjcoreModalSysTemParent
  },
  data(){
    return{
      SectorTypeOption: {
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
