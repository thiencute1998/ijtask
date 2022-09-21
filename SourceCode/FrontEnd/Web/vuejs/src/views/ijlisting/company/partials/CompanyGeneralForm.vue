<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="value.CompanyName" type="text" id="CompanyName" class="form-control" placeholder="Tên đơn vị" name="CompanyName"/>
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.CompanyNo" class="form-control" placeholder="Mã số" :disabled="!value.Detail"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Là mục con của</label>
      <div class="col-md-15" v-if="value.Detail">
        <IjcoreModalParent v-model="value"
                           :title="'Đơn vị'"
                           :api="'/listing/api/common/get-parent'"
                           :table="'company'"
                           :fieldName="'CompanyName'"
                           :fieldNo="'CompanyNo'"
                           :fieldID="'CompanyID'"
                           :placeholderInput="'Chọn đơn vị cha'"
                           :placeholderSearch="'Nhập tên đơn vị'"
                           :currentID="value.CompanyID">

        </IjcoreModalParent>
      </div>
      <div class="col-md-15" v-if="!value.Detail">
        <input  type="text" v-model="value.ParentName" class="form-control" disabled />
      </div>
      <div v-if="value.ParentID" class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.ParentNo" class="form-control" placeholder="Mã số" :disabled="!value.Detail"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3">Loại đơn vị</label>
      <div class="col-md-21">
        <company-modal-search-input-catelist v-model="value.CompanyCate" title-modal="Loại đơn vị" placeholder="Loại đơn vị">
        </company-modal-search-input-catelist>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Quyền truy cập</label>
      <div class="col-md-9">
        <b-form-select v-model="value.AccessType" :options="AccessTypeOptions" id="item-uom">
        </b-form-select>
      </div>
      <label class="col-md-3">Ngành</label>
      <div class="col-md-9">
        <ijcore-modal-search-listing
          v-model="value" :title="'Ngành'" :table="'sector'" :api="'/listing/api/common/list'"
          :fieldID="'SectorID'" :fieldNo="'SectorNo'" :fieldName="'SectorName'"
          :fieldAssignID="'SectorID'" :fieldAssignNo="'SectorNo'" :fieldAssignName="'SectorName'"
        >
        </ijcore-modal-search-listing>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Người liên hệ</label>
      <div class="col-md-9 mb-3 mb-sm-0">
        <ijcore-modal-search-listing
          v-model="value" :title="'Liên hệ'" :table="'employee'" :api="'/listing/api/common/list'"
          :fieldID="'EmployeeID'" :fieldNo="'EmployeeNo'" :fieldName="'EmployeeName'"
          :fieldAssignID="'EmployeeID'" :fieldAssignNo="'EmployeeNo'" :fieldAssignName="'ContactName'"
        >
        </ijcore-modal-search-listing>
      </div>
      <label class="col-md-3 m-0" for="ContactTel">ĐTCN</label>
      <div class="col-md-9">
        <input v-model="value.ContactTel" type="text" id="ContactTel" class="form-control" placeholder="Số điện thoại" name="ContactTel">
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Cấp quản lý</label>
      <div class="col-md-9">
        <b-form-select v-model="value.ManagementLevel" :options="ManagementLevelOptions" id="item-uom">
        </b-form-select>
      </div>
      <label class="col-md-3">Trung ương</label>
      <div class="col-md-9">
        <ijcore-modal-search-listing
          v-if="value.ManagementLevel ===1"
          v-model="value" :title="'Trung ương'" :table="'center'" :api="'/listing/api/common/list'"
          :fieldID="'CenterID'" :fieldNo="'CenterNo'" :fieldName="'CenterName'"
          :fieldAssignID="'CenterID'" :fieldAssignNo="'CenterNo'" :fieldAssignName="'CenterName'"
        >
        </ijcore-modal-search-listing>
        <b-form-input type="text" disabled placeholder="Trung ương" v-if="value.ManagementLevel != 1"></b-form-input>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Tỉnh</label>
      <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
        <ijcore-modal-search-input
          v-if="value.ManagementLevel >=2"
          v-model="value.Province"
          :select-fields-api="[
                              {field: 'ProvinceID',fieldForSelected: 'id', showInTable: false, key: 'ProvinceID'},
                              {field: 'ProvinceName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'ProvinceName', sortable: true, thClass: 'd-none'}
                            ]"
          :search-fields-api="[{field: 'ProvinceName', placeholder: 'Nhập tên', name: 'ProvinceName', class: '', style: ''}]"
          table="province"
          ref="myModalSearchInputProvince"
          id-modal="myModalSearchInputProvince"
          :item-per-page="8"
          placeholder="Tỉnh"
          :url-api="$store.state.appRootApi + '/listing/api/common/get-province'"
          name-input="input-province"
          title-modal="Tỉnh" size-modal="lg">
        </ijcore-modal-search-input>
        <b-form-input type="text" disabled placeholder="Tỉnh"  v-if="value.ManagementLevel < 2 || !value.ManagementLevel"></b-form-input>
      </div>
      <label class="col-md-2 m-0" >Huyện</label>
      <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0" >
        <ijcore-modal-search-input
          v-model="value.District"
          v-if="value.ManagementLevel >=3"
          :select-fields-api="[
                              {field: 'DistrictID',fieldForSelected: 'id', showInTable: false, key: 'DistrictID'},
                              {field: 'DistrictName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'DistrictName', sortable: true, thClass: 'd-none'}
                            ]"
          :search-fields-api="[{field: 'DistrictName', placeholder: 'Nhập tên', name: 'DistrictName', class: '', style: ''}]"
          table="district"
          ref="myModalSearchInputDistrict"
          id-modal="myModalSearchInputDistrict"
          :item-per-page="8"
          placeholder="Huyện"
          :request-data="{ProvinceID: (value.Province) ? value.Province.ProvinceID : null}"
          :url-api="$store.state.appRootApi + '/listing/api/common/get-district'"
          name-input="input-district"
          title-modal="Huyện" size-modal="lg">
        </ijcore-modal-search-input>
        <b-form-input type="text" disabled placeholder="Huyện" v-if="value.ManagementLevel < 3 || !value.ManagementLevel"></b-form-input>
      </div>
      <label class="col-md-2 m-0" >Xã</label>
      <div class="col-md-7" >
        <ijcore-modal-search-input
          v-model="value.Commune"
          v-if="value.ManagementLevel >=4"
          :select-fields-api="[
                              {field: 'CommuneID',fieldForSelected: 'id', showInTable: false, key: 'CommuneID'},
                              {field: 'CommuneName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'CommuneName', sortable: true, thClass: 'd-none'}
                            ]"
          :search-fields-api="[{field: 'CommuneName', placeholder: 'Nhập tên', name: 'CommuneName', class: '', style: ''}]"
          table="commune"
          ref="myModalSearchInputCommune"
          id-modal="myModalSearchInputCommune"
          :item-per-page="8"
          placeholder="Xã"
          :request-data="{
                              ProvinceID: (value.Province) ? value.Province.ProvinceID : null,
                              DistrictID: (value.District) ? value.District.DistrictID : null
                            }"
          :url-api="$store.state.appRootApi + '/listing/api/common/get-commune'"
          name-input="input-commune"
          title-modal="Xã" size-modal="lg">
        </ijcore-modal-search-input>
        <b-form-input type="text" disabled placeholder="Xã" v-if="value.ManagementLevel < 4 || !value.ManagementLevel"></b-form-input>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" for="Tel">ĐTĐV</label>
      <div class="col-md-5 mb-3 mb-sm-0">
        <input type="text" v-model="value.Tel" id="Tel" class="form-control" placeholder="Số điện thoại" name="Tel"/>
      </div>

      <label class="col-md-2 m-0" for="Fax">Số Fax</label>
      <div class="col-md-5 mb-3 mb-sm-0">
        <input type="text" v-model="value.Fax" id="Fax" class="form-control" placeholder="Số Fax" name="Fax"/>
      </div>

      <label class="col-md-2 m-0" for="Email">Email</label>
      <div class="col-md-7 ">
        <input type="text" v-model="value.Email" id="Email" class="form-control" placeholder="Email" name="Email"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" for="Note">Địa chỉ</label>
      <div class="col-md-12">
        <input v-model="value.Address" class="form-control" rows="3" placeholder="Địa chỉ" name="Address"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3" title="Loại tổng hợp dữ liệu">Loại THDL</label>
      <div class="col-md-9">
        <b-form-select v-model="value.SumCompanyType":options="SumCompanyTypeOptions"></b-form-select>
      </div>
      <div class="col-md-3" title="Là cơ quan tài chính">
        <b-form-checkbox v-model="value.IsFinancialCompany">Là cơ quan TC</b-form-checkbox>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3" for="Note">Ghi chú</label>
      <div class="col-md-21">
        <textarea v-model="value.Note" class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note"></textarea>
      </div>
    </div>
    <div class="form-group row align-items-center mt-1">
      <div class="col-md-8">
        <b-form-checkbox v-model="value.isAutOrg">Là cơ quan chủ quản</b-form-checkbox>
      </div>
      <div class="col-md-8">
        <b-form-checkbox v-model="value.isFinOrg">Là cơ quan tài chính</b-form-checkbox>
      </div>
      <div class="col-md-8">
        <b-form-checkbox v-model="value.isTreOrg">Là kho bạc nhà nước</b-form-checkbox>
      </div>
    </div>
    <!--Cơ quan chủ quản-->
    <div class="form-group row ij-line-head " v-if="value.isAutOrg && value.ManagementLevel >=2">
      <label class="col-md-24">Cơ quan chủ quản: </label>
    </div>
    <div class="form-group row align-items-center mt-3" v-if="value.isAutOrg && value.ManagementLevel >=2" >
      <label class="col-md-3">Tên</label>
      <div class="col-md-15">
        <b-form-input type="text"  v-model="value.AutOrgName"></b-form-input>
      </div>
      <div class="col-md-6">
        <div class="row align-items-center">
          <label class="col-md-5 pl-0">Mã số</label>
          <div class="col-md-19">
            <ijcore-modal-search-listing
              v-model="model" :title="'CQCQ'" :table="'company'" :api="'/listing/api/common/list'"
              :fieldID="'CompanyID'" :fieldNo="'CompanyNo'" :fieldName="'CompanyName'"
              :fieldAssignID="'AutOrgID'" :fieldAssignNo="'AutOrgNo'" :fieldAssignName="'AutOrgName'"
              :showNo="true"
              :FieldWhere="{isAutOrg: 1}"
              @selectOrg="selectOrg"
              @clearOrg="clearOrg(1)"
            ></ijcore-modal-search-listing>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="value.isAutOrg && value.ManagementLevel >=2">
      <label class="col-md-3">Địa chỉ</label>
      <div class="col-md-15">
        <b-form-input v-model="value.AutOrgAddress"></b-form-input>
      </div>
      <div class="col-md-6">
        <div class="row align-items-center">
          <label class="col-md-5 pr-0 pl-0">Chương</label>
          <div class="col-md-19">
            <ijcore-modal-search-listing
              v-model="model" :title="'Chương'" :table="'sbi_chapter'" :api="'/listing/api/common/list'"
              :fieldID="'SbiChapterID'" :fieldNo="'SbiChapterNo'" :fieldName="'SbiChapterName'"
              :fieldAssignID="'AutOrgChapterID'" :fieldAssignNo="'AutOrgChapterNo'" :fieldAssignName="'AutOrgChapterName'"
              :showNo="true"
            ></ijcore-modal-search-listing>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="value.isAutOrg && value.ManagementLevel >=2">
      <label class="col-md-3" >Người liên hệ</label>
      <div class="col-md-9">
        <b-form-input type="text" placeholder="Người liên hệ" v-model="value.AutOrgContactName"></b-form-input>
      </div>
      <label class="col-md-3">Chức vụ</label>
      <div class="col-md-9">
        <b-form-input type="text" placeholder="Chức vụ" v-model="value.AutOrgContactPosition"></b-form-input>
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="value.isAutOrg && value.ManagementLevel >=2">
      <label class="col-md-3"  title="Điện thoại cơ quan">ĐTCQ</label>
      <div class="col-md-9">
        <b-form-input type="number" placeholder="Điện thoại cơ quan"  v-model="value.AutOrgContactOfficePhone"></b-form-input>
      </div>
      <label class="col-md-3" title="Điện thoại di dộng">ĐTDĐ</label>
      <div class="col-md-9">
        <b-form-input type="number" placeholder="Điện thoại di dộng" v-model="value.AutOrgContactHandPhone"></b-form-input>
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="value.isAutOrg && value.ManagementLevel >=2">
      <label class="col-md-3">Email</label>
      <div class="col-md-21">
        <b-form-input type="email" placeholder="Email" v-model="value.AutOrgContactMail"></b-form-input>
      </div>
    </div>
    <div class="form-group row ij-line-head " v-if="value.isFinOrg && value.ManagementLevel >=2">
      <label class="col-md-24">Cơ quan tài chính: </label>
    </div>
    <div class="form-group row align-items-center mt-3" v-if="value.isFinOrg && value.ManagementLevel >=2">
      <label class="col-md-3" >Tên đơn vị</label>
      <div class="col-md-15">
        <b-form-input type="text" v-model="Fin.FinName">
        </b-form-input>
      </div>
      <div class="col-md-6">
        <div class="row align-items-center">
          <label class="col-md-5 pl-0">Mã số</label>
          <div class="col-md-19">
            <ijcore-modal-search-listing
              v-model="Fin" :title="'cơ quan TC'" :table="'company'" :api="'/listing/api/common/list'"
              :fieldID="'CompanyID'" :fieldNo="'CompanyNo'" :fieldName="'CompanyName'"
              :fieldAssignID="'FinID'" :fieldAssignNo="'FinNo'" :fieldAssignName="'FinName'"
              :showNo="true"
              :FieldWhere="{isFinOrg: 1}"
              @clearOrg="clearOrg(2)"
            ></ijcore-modal-search-listing>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group row align-items-center ij-line-head" v-if="value.ManagementLevel >=2 && value.isTreOrg">
      <label class="col-md-24">Kho bạc nhà nước</label>
    </div>
    <div class="form-group row align-items-center mt-3" v-if="value.ManagementLevel >=2 && value.isTreOrg">
      <label class="col-md-3">Tên đơn vị</label>
      <div class="col-md-15">
        <b-form-input type="text" v-model="Tre.TreName"></b-form-input>
      </div>
      <div class="col-md-6">
        <div class="row align-items-center">
          <label class="col-md-5 pl-0">Mã số</label>
          <div class="col-md-19">
            <ijcore-modal-search-listing
              v-model="Tre" :title="'Kho bạc nhà nước'" :table="'treasury'" :api="'/listing/api/common/list'"
              :fieldID="'TreasuryID'" :fieldNo="'TreasuryNo'" :fieldName="'TreasuryName'"
              :fieldAssignID="'TreID'" :fieldAssignNo="'TreNo'" :fieldAssignName="'TreName'"
              :showNo="true"
            ></ijcore-modal-search-listing>
          </div>
        </div>
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
import CompanyModalSearchInputCatelist from "./CompanyModalSearchInputCatelist";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";
const getOrgApi = 'listing/api/company/get-org';
const getTreOrgApi = 'listing/api/treasury/get-tre-org';

export default {
  name: 'CompanyGeneralForm',
  props: ['value','companyOptions','employeeOptions', 'Fin', 'Tre'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    CompanyModalSearchInputCatelist,
    IjcoreModalSearchListing,
  },
  data(){
    return{
      AccessTypeOptions:{
        1: 'Chia sẻ',
        2: 'Công khai',
        3: 'Riêng tư'
      },
      ManagementLevelOptions: [
        {value: null, text: '--- Chọn cấp quản lý---'},
        {value: 1, text:'Trung ương'},
        {value: 2, text:'Tỉnh'},
        {value: 3, text:'Huyện'},
        {value: 4, text:'Xã'},
      ],
      SumCompanyTypeOptions: [
        {value: 1, text: 'Không tổng hợp dữ liệu'},
        {value: 2, text: 'Tổng hợp dữ liệu theo cấp chính quyền '},
        {value: 3, text: 'Tổng hợp dữ liệu theo ngành'},
      ],
    }
  },
  methods: {
    changeUserContact(){
      let employee = _.find(this.employeeOptions, ['id', Number(this.value.employeeID)]);
      if(employee){
        this.value.contactName = employee.text;
      }
    },
    autoOrg(companyType, urlApi) {
      let self = this;
      this.clearOrg(companyType);
      let provinceID = null;
      let districtID = null;
      let isAutOrg = null;
      let isFinOrg = null;
      let isTreOrg = null;
      if(self.value.District.DistrictID && self.value.ManagementLevel == 3){
        provinceID = self.value.District.ProvinceID;
      }
      if(self.value.Commune.CommuneID && self.value.ManagementLevel == 4){
        provinceID = self.value.Commune.ProvinceID;
        districtID = self.value.Commune.DistrictID;
      }
      if(companyType === 1){
        isAutOrg = 1;
      } else if(companyType === 2){
        isFinOrg = 1;
      } else {
        isTreOrg = 1;
      }
      let requestData = {
        method: 'post',
        url: urlApi +'?XDEBUG_SESSION_START=PHPSTORM',
        data: {
          ManagementLevel: self.value.ManagementLevel - 1,
          ProvinceID: provinceID,
          DistrictID: districtID,
          isAutOrg: isAutOrg,
          isFinOrg: isFinOrg,
          isTreOrg: isTreOrg,
        }
      }
      self.$store.commit('isLoading',true)
      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response=>{
          let responseData = response.data;
          if(responseData.status === 1){
            debugger
            if(responseData.data){
              if(companyType === 1){
                self.value.AutOrgID = responseData.data.CompanyID;
                self.value.AutOrgNo = responseData.data.CompanyNo;
                self.value.AutOrgName = responseData.data.CompanyName;
                self.value.AutOrgChapterID = responseData.data.SbiChapterID;
                self.value.AutOrgChapterNo = responseData.data.SbiChapterNo;
                self.value.AutOrgAddress = responseData.data.Address;
                self.value.AutOrgContactOfficePhone = responseData.data.Tel;
                self.value.AutOrgContactHandPhone = responseData.data.ContactTel;
                self.value.AutOrgContactMail = responseData.data.Email;
                if(responseData.position){
                  self.value.AutOrgContactName = responseData.position.EmployeeName;
                  self.value.AutOrgContactPosition = responseData.position.PositionName;
                }
              }
              if(companyType === 2){
                self.Fin.FinID =  responseData.data.CompanyID;
                self.Fin.FinNo =  responseData.data.CompanyNo;
                self.Fin.FinName =  responseData.data.CompanyName;
              }
              if(companyType === 3){
                self.Tre.TreID =  responseData.data.TreasuryID;
                self.Tre.TreNo =  responseData.data.TreasuryNo;
                self.Tre.TreName =  responseData.data.TreasuryName;
              }
            }

          } else if (responsesData.status === 4){
            Swal.fire(
              'Lỗi',
              'Không có kho bạc nhà nước cấp Xã!',
              'error'
            )
          } else {
            let htmlErrors = __.renderErrorApiHtml(responsesData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            )
          }
          self.$store.commit('isLoading',false)
        }).catch(error=> {
        self.$store.commit('isLoading',false)
      })
    },
    selectOrg(companyID){
      console.log(companyID);
      let self = this;
      let requestData = {
        method: 'get',
        url: 'listing/api/company/view' + '/' + companyID,
        data: {
          id: companyID
        }
      }
      self.$store.commit('isLoading',true)
      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response=>{
          let responseData = response.data;
          if(responseData.status === 1){
            self.value.AutOrgAddress = responseData.data.data.Address;
            self.value.AutOrgChapterID = responseData.data.data.SbrSectorID;
            self.value.AutOrgChapterNo = responseData.data.data.SbrSectorNo;
            self.value.AutOrgChapter = responseData.data.data.SbrSectorNo;
            self.value.AutOrgContactOfficePhone = responseData.data.data.Tel;
            self.value.AutOrgContactHandPhone = responseData.data.data.ContactTel;
            self.value.AutOrgContactMail = responseData.data.data.Email;
            let contact = _.find(responseData.Employee, ['EmployeeID', responseData.data.data.EmployeeID]);
            self.value.AutOrgContactName = (contact) ? contact['EmployeeName'] : null;
            self.value.AutOrgContactPosition = (contact) ? contact['PositionName'] : null;
          }
          self.$store.commit('isLoading',false)
        }).catch(error=> {
        self.$store.commit('isLoading',false)
      });
    },
    clearOrg(companyType){
      let self = this;
      if(companyType = 1){
        self.value.AutOrgID = null;
        self.value.AutOrgNo = '';
        self.value.AutOrgName = '';
        self.value.AutOrgChapterID = null;
        self.value.AutOrgChapterNo = null;
        self.value.AutOrgAddress = '';
        self.value.AutOrgContactOfficePhone = '';
        self.value.AutOrgContactHandPhone = '';
        self.value.AutOrgContactMail = '';
        self.value.AutOrgContactName = '';
        self.value.AutOrgContactPosition = '';
      }
      if (companyType == 2){
        self.Fin.FinID = null;
        self.Fin.FinNo = '';
        self.Fin.FinName = '';
      }
      if(companyType == 3){
        self.Tre.TreID = null;
        self.Tre.TreNo = '';
        self.Tre.TreName = '';
      }
    }
  },
  watch: {
    'value.ParentID'(){
      let self = this;
      let urlApi = '/listing/api/common/auto-child';
      let requestData = {
        method: 'post',
        url: urlApi,
        data: {
          per_page: 10,
          page: this.currentPage,
          table: 'company',
          ParentID: this.value.ParentID,
        }
      }
      self.$store.commit('isLoading',true)
      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response=>{
          let responseData = response.data;
          if(responseData.status === 1){
            this.value.CompanyNo = responseData.data;
          }
          self.$store.commit('isLoading',false)
        }).catch(error=> {
        self.$store.commit('isLoading',false)
      })
    },
    'value.Province'() {
      let self = this;
      // Cơ quan chủ quản
      if(this.value.ManagementLevel == 2 && this.value.isAutOrg ){
        if(this.value.Province.ProvinceID){
          this.autoOrg(1, getOrgApi);
        } else {
          self.clearOrg(1);
        }
      }
      // Cơ quản lý
      if(this.value.ManagementLevel == 2 && this.value.isFinOrg ){
        if(this.value.Province.ProvinceID){
          this.autoOrg(2, getOrgApi);
        } else {
          self.clearOrg(2);
        }
      }
      if(this.value.ManagementLevel == 2 && this.value.isTreOrg){
        if(this.value.Province.ProvinceID){
          this.autoOrg(3, getTreOrgApi);
        } else {
          self.clearOrg(3);
        }
      }
    },
    'value.District'() {
      let self = this;
      if(this.value.ManagementLevel == 3 && this.value.isAutOrg){
        if( this.value.District.DistrictID){
          this.autoOrg(1, getOrgApi);
        } else {
          self.clearOrg(1);
        }
      }
      if(this.value.ManagementLevel == 3 && this.value.isFinOrg){
        if( this.value.District.DistrictID){
          this.autoOrg(2, getOrgApi);
        } else {
          self.clearOrg(2);
        }
      }
      if(this.value.ManagementLevel == 3 && this.value.isTreOrg){
        if( this.value.District.DistrictID){
          this.autoOrg(3, getTreOrgApi);
        } else {
          self.clearOrg(3);
        }
      }
    },
    'value.Commune'(){
      let self = this;
      if(this.value.ManagementLevel == 4 && this.value.isAutOrg) {
        if(this.value.Commune.CommuneID){
          this.autoOrg(1, getOrgApi);
        } else {
          self.clearOrg(1);
        }
      }
      if(this.value.ManagementLevel == 4 && this.value.isFinOrg) {
        if(this.value.Commune.CommuneID){
          this.autoOrg(2, getOrgApi);
        } else {
          self.clearOrg(2);
        }
      }
      if(this.value.ManagementLevel == 4 && this.value.isTreOrg) {
        if(this.value.Commune.CommuneID){
          this.autoOrg(3, getTreOrgApi);
        } else {
          self.clearOrg(3);
        }
      }
    },
    'value.isAutOrg'() {
      if(this.value.ManagementLevel > 1 && this.value.isAutOrg){
        this.autoOrg(1, getOrgApi);
      }
    },
    'value.isFinOrg'() {
      if(this.value.ManagementLevel > 1 && this.value.isFinOrg){
        this.autoOrg(2, getOrgApi);
      }
    },
    'value.isTreOrg'() {
      debugger
      if(this.value.ManagementLevel > 1 && this.value.isTreOrg){
        this.autoOrg(3, getTreOrgApi);
      }
    },
  }
}
</script>
