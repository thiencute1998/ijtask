<template>
  <div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 mb-3">Họ</label>
      <div class="col-md-5">
        <input type="text" v-model="value.FirstName" class="form-control" placeholder="Họ">
      </div>
      <label class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 mb-3">Tên đệm</label>
      <div class="col-md-5">
        <input type="text" v-model="value.MiddleName" class="form-control" placeholder="Tên đệm">
      </div>
      <label class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 mb-3" >Tên</label>
      <div class="col-md-5">
        <input type="text" v-model="value.LastName" class="form-control" placeholder="Tên">
      </div>
    </div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        <input v-model="fullName" type="text" id="EmployeeName" readonly="true" class="form-control" placeholder="Tên nhân viên" name="EmployeeName"/>
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
        <span>Mã số</span>
        <input type="text" v-model="value.EmployeeNo" class="form-control" placeholder="Mã số"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Loại nhân viên</label>
      <div class="col-md-21">
        <employee-modal-search-input-catelist
          v-model="value.EmployeeCate"
          title-modal="Loại nhân viên"
          placeholder="Loại nhân viên"
        ></employee-modal-search-input-catelist>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Đơn vị</label>
      <div class="col-md-5">
        <ijcore-modal-search-listing
          v-model="value" :title="'Đơn vị'" :table="'company'" :api="'/listing/api/common/list'"
          :fieldID="'CompanyID'" :fieldNo="'CompanyNo'" :fieldName="'CompanyName'"
          :fieldAssignID="'CompanyID'" :fieldAssignNo="'CompanyNo'" :fieldAssignName="'CompanyName'"
        >
        </ijcore-modal-search-listing>
      </div>
      <label class="col-md-3 m-0" for="department">Phòng ban</label>
      <div class="col-md-5" id="department">
        <ijcore-modal-search-listing
          v-model="value" :title="'Phòng ban'" :table="'department'" :api="'/listing/api/common/list'"
          :fieldID="'DepartmentID'" :fieldNo="'DepartmentNo'" :fieldName="'DepartmentName'"
          :fieldAssignID="'DepartmentID'" :fieldAssignNo="'DepartmentNo'" :fieldAssignName="'DepartmentName'"
        >
        </ijcore-modal-search-listing>
      </div>
      <label class="col-md-3 m-0" for="position">Chức vụ</label>
      <div class="col-md-5">
        <Select2 id="position" v-model="value.PositionName" :options="PositionOption"></Select2>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Ngày sinh</label>
      <div class="col-md-5">
        <IjcoreDatePicker v-model="value.BirthDay">
        </IjcoreDatePicker>
      </div>
      <label class="col-md-3 m-0" for="officePhone">ĐT cơ quan</label>
      <div class="col-md-5">
        <input v-model="value.OfficePhone" type="text" id="officePhone" class="form-control" placeholder="Điện thoại cơ quan" />
      </div>
      <label class="col-md-3 m-0" for="handPhone">ĐT di động</label>
      <div class="col-md-5">
        <input v-model="value.HandPhone" type="text" id="handPhone" class="form-control" placeholder="Điện thoại di động"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" for="email">Email</label>
      <div class="col-md-5">
        <input v-model="value.Email" type="text" id="email" class="form-control" placeholder="Email"/>
      </div>
      <label class="col-md-3 m-0" for="facebookID">Facebook ID</label>
      <div class="col-md-5">
        <input v-model="value.FacebookID" type="text" id="facebookID" class="form-control" placeholder="Facebook ID"/>
      </div>
      <label class="col-md-3 m-0" for="twitterID">Twitter ID</label>
      <div class="col-md-5">
        <input v-model="value.TwitterID" type="text" id="twitterID" class="form-control" placeholder="Twitter ID"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" for="skypeID">Skype ID</label>
      <div class="col-md-5">
        <input v-model="value.SkypeID" type="text" id="skypeID" class="form-control" placeholder="Skype ID"/>
      </div>
      <label class="col-md-3 m-0" for="zaloID">Zalo ID</label>
      <div class="col-md-5">
        <input v-model="value.ZaloID" type="text" id="zaloID" class="form-control" placeholder="Zalo ID"/>
      </div>
      <label class="col-md-3 m-0" for="user">Người dùng</label>
      <div class="col-md-5" id="user">
        <EmployeeModalSearchUser v-model="value" :title="'Người dùng'" :table="'sys_user'" :api="'/listing/api/employee/get-user'" :fieldName="'FullName'"></EmployeeModalSearchUser>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0" for="citizenIdNo" title="Chứng minh nhân dân/ căn cước công dân">CMND/CCCD</label>
      <div class="col-md-5">
        <input v-model="value.CitizenIdNo" type="text" id="citizenIdNo" class="form-control" placeholder="CMND/CCCD"/>
      </div>
      <label class="col-md-3 m-0">Ngày cấp</label>
      <div class="col-md-5">
        <IjcoreDatePicker v-model="value.CitizenIdDate">
        </IjcoreDatePicker>
      </div>
      <label class="col-md-3 m-0" for="citizenIdAt">Nơi cấp</label>
      <div class="col-md-5">
        <input v-model="value.CitizenIdAt" type="text" id="citizenIdAt" class="form-control" placeholder="Nơi cấp"/>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3" for="Note">Ghi chú</label>
      <div class="col-md-20">
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
import IjcoreDatePicker from '@/components/IjcoreDatePicker';
import EmployeeModalSearchInputCatelist from "./EmployeeModalSearchInputCatelist";
import EmployeeModalSearchUser from "./EmployeeModalSearchUser";
import IjcoreModalSearchListing from "../../../../components/IjcoreModalSearchListing";
export default {
  name: 'EmployeeGeneralForm',
  props: ['value','companyOptions','employeeOptions','PositionOption'],
  components: {
    Select2,
    IjcoreModalListing,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    IjcoreDatePicker,
    EmployeeModalSearchInputCatelist,
    EmployeeModalSearchUser,
    IjcoreModalSearchListing
  },
  data(){
    return{
    }
  },
  computed:{
    fullName(){
      if(this.value.EmployeeNameType === 1){
        this.value.EmployeeName = this.value.LastName + ' ' + this.value.MiddleName + ' ' + this.value.FirstName;
      }
      else if(this.value.EmployeeNameType === 2){
        this.value.EmployeeName = this.value.FirstName + ' ' + this.value.LastName + ' ' + this.value.MiddleName;
      }
      else{
        this.value.EmployeeName = this.value.FirstName + ' ' + this.value.MiddleName + ' ' + this.value.LastName;
      }
      return this.value.EmployeeName;
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
}
</script>
