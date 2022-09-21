<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên: </div>
      <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.RevenueName" type="text" id="RevenueName" class="form-control" placeholder="Tên khoản thu" name="RevenueName"/>
      </div>
      <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.RevenueNo" class="form-control" placeholder="Mã số" :disabled="!value.Detail"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" >Là mục con của</label>
      <div class="col-md-17" v-if="value.Detail">
        <IjcoreModalSysTemParent
          v-model="value"
          :title="'Khoản thu'"
          :api="'/listing/api/common/list'"
          :table="'revenue'"
          :fieldName="'RevenueName'"
          :fieldNo="'RevenueNo'"
          :fieldID="'RevenueID'"
          :placeholderInput="'Chọn khoản thu cha'"
          :placeholderSearch="'Nhập khoản thu'"
          :columnID="'RevenueID'"
          :columnNo="'RevenueNo'"
          :columnName="'RevenueName'">
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
      <label class="col-md-3">Loại khoản thu: </label>
      <div class="col-md-21">
        <revenue-modal-search-input-catelist
          v-model="value.RevenueCate"
          :listApi="'listing/api/revenue/get-revenue-cate-list'"
          title-modal="Loại khoản thu"
          placeholder="Loại khoản thu"
        ></revenue-modal-search-input-catelist>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Đơn vị tính: </label>
      <div class="col-md-9 mb-3 mb-sm-0">
        <ijcore-modal-search-listing
          v-model="value" :title="'Đơn vị tính'" :table="'uom'" :api="'/listing/api/common/list'"
          :fieldID="'UomID'" :fieldNo="'UomNo'" :fieldName="'UomName'"
          :fieldAssignID="'UomID'" :fieldAssignNo="'UomNo'" :fieldAssignName="'UomName'"
        >
        </ijcore-modal-search-listing>
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

      <label class="col-md-3 m-0">Lĩnh vực thu</label>
      <div class="col-md-9 mb-3 mb-sm-0">
        <ijcore-modal-search-listing
          v-model="value" :title="'Lĩnh vực thu'" :table="'sbr_sector'" :api="'/listing/api/common/list'"
          :fieldID="'SbrSectorID'" :fieldNo="'SbrSectorNo'" :fieldName="'SbrSectorName'"
          :fieldAssignID="'SbrSectorID'" :fieldAssignNo="'SbrSectorNo'" :fieldAssignName="'SbrSectorName'"
        >
        </ijcore-modal-search-listing>
      </div>
    </div>
    <div class="form-group row align-center pl-2 pr-2">
      <label>Tỷ lệ điều tiết thu ngân sách :</label>
      <table class="table b-table table-sm table-bordered">
        <thead>
        <tr>
          <th scope="col" style="width: 10%" class="text-center">Ngày hiệu lực</th>
          <th scope="col" style="width: 10%" class="text-center">Ngày hết hiệu lực</th>
          <th scope="col" style="min-width: 55%" class="text-center">Chỉ tiêu phân bổ</th>
          <th scope="col" style="width: 5%" class="text-center">%</th>
          <th scope="col" style="width: 5%" class="text-center" title="Ngừng hoạt động"><i class="fa fa-ban m-0"></i></th>
          <th scope="col" style="max-width: 8px" class="text-center"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(field, key) in value.RevenueReguItem">
          <td class="text-center">{{value.RevenueReguItem[key].EffectiveDate | convertServerDateToClientDate}}</td>
          <td class="text-center" >{{value.RevenueReguItem[key].ExpirationDate | convertServerDateToClientDate}}</td>
          <td class="text-left pl-3">{{ReguRateOptions[value.RevenueReguItem[key].BudgetLevel].text}}</td>
          <td class="text-right">{{value.RevenueReguItem[key].ReguRate }}</td>
          <td class="text-center"><i class="fa fa-check" v-if="value.RevenueReguItem[key].RevenueReguActive === 0"></i></td>
          <td class="text-center">
            <div class="d-flex align-center justify-content-center">
              <master-detail-form
                title="Tỷ lệ điều tiết thu ngân sách"
                :is-create="false"
                :revenue-regu-item="value.RevenueReguItem"
                :key-item="key"
                @edited="onEditFileOnTalbe"
              ></master-detail-form>
              <i @click="onDeleteFieldOnTable(key)" class="fa fa-trash-o ml-2" title="Xóa"
                 style="font-size: 18px; cursor: pointer"></i>
            </div>
          </td>
        </tr>
        </tbody>
      </table>
      <master-detail-form title="Tỷ lệ điều tiết thu ngân sách"  :revenue-regu-item="value.RevenueReguItem" :is-create="true" @saved="onAddFieldOnTable"></master-detail-form>
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
import RevenueModalSearchUom from "./RevenueModalSearchUom";
import ApiService from "@/services/api.service";
import RevenueModalSearchInputCatelist from "./RevenueModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";
import IjcoreModalSysTemParent from "../../../../components/IjcoreModalSystemParent";
import IjcoreNumber from "@/components/IjcoreNumber";
import MasterDetailForm from "../../revenue/partials/MasterDetailForm";


export default {
  name: 'RevenueGeneralForm',
  props: ['value','revenueOptions','uomOptions'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalSearchInput,
    RevenueModalSearchUom,
    IjcoreModalSysTemParent,
    RevenueModalSearchInputCatelist,
    IjcoreModalSearchListing,
    IjcoreNumber,
    MasterDetailForm
  },
  data(){
    return{
      checked: false,
      BudgetBalanceTypeOption: {
        '1': 'Trong cân đối ngân sách',
        '2': 'Ngoài cân đối ngân sách',
      },
      BudgetStateTypeOption: {
        '1': 'Trong ngân sách',
        '2': 'Ngoài ngân sách',
      },
      ReguRateOptions : [
        {value: 0, text : 'Để lại đơn vị'},
        {value: 1, text : 'Ngân sách trung ương'},
        {value: 2, text : 'Ngân sách tỉnh'},
        {value: 3, text : 'Ngân sách huyện'},
        {value: 4, text : 'Ngân sách xã'},
      ],
    }
  },
  created() {
    console.log(this.value.isRevenueRegulationRate)
    this.checked = (this.value.isRevenueRegulationRate) ? true : false;
  },
  methods: {
    changeUserContact(){
      let uom = _.find(this.uomOptions, ['id', Number(this.value.uomID)]);
      if(uom){
        this.value.contactName = uom.text;
      }
    },
    onAddFieldOnTable(data) {
      let self = this;
      _.forEach(data.BudgetLevelTable, function (value, key){
        if(value.ReguRate){
          let tmpObj = {};
          tmpObj.EffectiveDate = data.EffectiveDate;
          tmpObj.ExpirationDate = data.ExpirationDate;
          tmpObj.BudgetLevel = value.BudgetLevel;
          tmpObj.ReguRate = value.ReguRate;
          tmpObj.RevenueReguActive = data.RevenueReguActive
          self.value.RevenueReguItem.push(tmpObj);
        }
      });
      this.sortFieldOnTable();
    },
    onEditFileOnTalbe(data){
      let self =  this;
      let arrDelete = _.filter(self.value.RevenueReguItem, ['EffectiveDate', data.EffectiveDateOld]);
      _.forEach(arrDelete, function (val, key){
        let index = _.findIndex(self.value.RevenueReguItem, ['EffectiveDate', val.EffectiveDate]);
        if(index > -1) {
          self.value.RevenueReguItem.splice(index ,1 );
        }
      });
      _.forEach(data.BudgetLevelTable, function (val, key){
        let tmpObj = {};
        if(val.ReguRate){
          tmpObj.EffectiveDate = data.EffectiveDate;
          tmpObj.ExpirationDate = data.ExpirationDate;
          tmpObj.RevenueReguActive = data.RevenueReguActive;
          tmpObj.BudgetLevel = val.BudgetLevel;
          tmpObj.ReguRate = val.ReguRate;
          self.value.RevenueReguItem.push(tmpObj);
        }
      });
      this.sortFieldOnTable();
      this.$bvToast.toast('Đã sửa tỷ lệ điều tiết thu ngân sách ' , {
        title: 'Thông báo',
        variant: 'success',
        solid: true
      });
    },
    sortFieldOnTable(){
      this.value.RevenueReguItem = _.orderBy(this.value.RevenueReguItem, ['EffectiveDate', 'BudgetLevel'], ['desc', 'asc']);
      this.$forceUpdate();
    },
    onDeleteFieldOnTable(key) {
      let self = this;
      Swal.fire({
        title: '',
        text: 'Bạn sẽ toàn bộ tỷ lệ điều tiết ngân sách trong kỳ!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy bỏ'
      }).then((result) =>{
        if (result.value){
          let effectiveDate = self.value.RevenueReguItem[key].EffectiveDate;
          let arrDelete = _.filter(self.value.RevenueReguItem, ['EffectiveDate', self.value.RevenueReguItem[key].EffectiveDate]);
          _.forEach(arrDelete, function (v, k){
            let index = _.findIndex(self.value.RevenueReguItem, ['EffectiveDate', v.EffectiveDate]);
            if (index > -1) {
              self.value.RevenueReguItem.splice(index, 1);
            }
          })
          self.$bvToast.toast('Đã xóa xóa tỷ lệ điều tiết', {
            title: 'Thông báo',
            variant: 'success',
            solid: true
          });
        }
      });

    },

  },
  watch: {

  }
}
</script>
