<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Nhân viên<span v-if="model.EmployeeName">:</span> {{model.EmployeeName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Nhân viên<span v-if="model.EmployeeName">:</span> {{model.EmployeeName}}</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitForm"><i class="fa fa-check-square-o"></i> Lưu</b-button>
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onBackToList"><i class="fa fa-ban"></i> Hủy</b-button>
            </div>
          </b-col>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-icons">
              <div class="main-header-collapse">
                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen=true />
              </div>

            </div>
          </b-col>
        </b-row>
      </div>

    </div>
    <div class="main-body main-body-view-action">
      <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">
        <div class="container-fluid">
          <b-card>
            <div class="form-group row align-items-center">
              <label class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 mb-3">Họ</label>
              <div class="col-md-5">
                <input type="text" v-model="model.FirstName" class="form-control" placeholder="Họ">
              </div>
              <label class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 mb-3">Tên đệm</label>
              <div class="col-md-5">
                <input type="text" v-model="model.MiddleName" class="form-control" placeholder="Tên đệm">
              </div>
              <label class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 mb-3" >Tên</label>
              <div class="col-md-5">
                <input type="text" v-model="model.LastName" class="form-control" placeholder="Tên">
              </div>
            </div>
            <div class="form-group row align-items-center">
              <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Họ và tên</div>
              <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                <input v-model="fullName.trim()" type="text" id="EmployeeName" disabled class="form-control" placeholder="Tên nhân viên" name="EmployeeName"/>
              </div>
              <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.EmployeeNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" title="Loại nhân viên">Loại NV</label>
              <div class="col-md-21">
                <employee-modal-search-input-catelist
                  v-model="model.EmployeeCate"
                  title-modal="Loại nhân viên"
                  placeholder="Loại nhân viên"
                ></employee-modal-search-input-catelist>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Đơn vị</label>
              <div class="col-md-5">
                <ijcore-modal-search-listing
                  v-model="model" :title="'Đơn vị'" :table="'company'" :api="'/listing/api/common/list'"
                  :fieldID="'CompanyID'" :fieldNo="'CompanyNo'" :fieldName="'CompanyName'"
                  :fieldAssignID="'CompanyID'" :fieldAssignNo="'CompanyNo'" :fieldAssignName="'CompanyName'"
                >
                </ijcore-modal-search-listing>
              </div>
              <label class="col-md-3 m-0" for="department">Phòng ban</label>
              <div class="col-md-5" id="department">
                <ijcore-modal-search-listing
                  v-model="model" :title="'Phòng ban'" :table="'department'" :api="'/listing/api/common/list'"
                  :fieldID="'DepartmentID'" :fieldNo="'DepartmentNo'" :fieldName="'DepartmentName'"
                  :fieldAssignID="'DepartmentID'" :fieldAssignNo="'DepartmentNo'" :fieldAssignName="'DepartmentName'"
                >
                </ijcore-modal-search-listing>
              </div>
              <label class="col-md-3 m-0" for="position">Chức vụ</label>
              <div class="col-md-5">
                <Select2 id="position" v-model="model.PositionName" :options="PositionOption"></Select2>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Ngày sinh</label>
              <div class="col-md-5">
                <IjcoreDatePicker v-model="model.BirthDay">
                </IjcoreDatePicker>
              </div>
              <label class="col-md-3 m-0" for="officePhone">ĐT cơ quan</label>
              <div class="col-md-5">
                <input v-model="model.OfficePhone" type="text" id="officePhone" class="form-control" placeholder="Điện thoại cơ quan" />
              </div>
              <label class="col-md-3 m-0" for="handPhone">ĐT di động</label>
              <div class="col-md-5">
                <input v-model="model.HandPhone" type="text" id="handPhone" class="form-control" placeholder="Điện thoại di động"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" for="email">Email</label>
              <div class="col-md-5">
                <input v-model="model.Email" type="text" id="email" class="form-control" placeholder="Email"/>
              </div>
              <label class="col-md-3 m-0" for="facebookID">Facebook ID</label>
              <div class="col-md-5">
                <input v-model="model.FacebookID" type="text" id="facebookID" class="form-control" placeholder="Facebook ID"/>
              </div>
              <label class="col-md-3 m-0" for="twitterID">Twitter ID</label>
              <div class="col-md-5">
                <input v-model="model.TwitterID" type="text" id="twitterID" class="form-control" placeholder="Twitter ID"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" for="skypeID">Skype ID</label>
              <div class="col-md-5">
                <input v-model="model.SkypeID" type="text" id="skypeID" class="form-control" placeholder="Skype ID"/>
              </div>
              <label class="col-md-3 m-0" for="zaloID">Zalo ID</label>
              <div class="col-md-5">
                <input v-model="model.ZaloID" type="text" id="zaloID" class="form-control" placeholder="Zalo ID"/>
              </div>
              <label class="col-md-3 m-0" for="user">Người dùng</label>
              <div class="col-md-5" id="user">
                <EmployeeModalSearchUser v-model="model" :title="'Người dùng'" :table="'sys_user'" :api="'/listing/api/employee/get-user'"></EmployeeModalSearchUser>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" for="citizenIdNo" title="Chứng minh nhân dân/ căn cước công dân">CMND/CCCD</label>
              <div class="col-md-5">
                <input v-model="model.CitizenIdNo" type="text" id="citizenIdNo" class="form-control" placeholder="CMND/CCCD"/>
              </div>
              <label class="col-md-3 m-0">Ngày cấp</label>
              <div class="col-md-5">
                <IjcoreDatePicker v-model="model.CitizenIdDate">
                </IjcoreDatePicker>
              </div>
              <label class="col-md-3 m-0" for="citizenIdAt">Nơi cấp</label>
              <div class="col-md-5">
                <input v-model="model.CitizenIdAt" type="text" id="citizenIdAt" class="form-control" placeholder="Nơi cấp"/>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-md-3" for="Note">Ghi chú</label>
              <div class="col-md-21">
                <textarea v-model="model.Note" class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note"></textarea>
              </div>
            </div>
          </b-card>
        </div>
      </vue-perfect-scrollbar>
    </div>
  </div>
</template>

<script>
import ApiService from '@/services/api.service';
import Swal from 'sweetalert2';
import 'sweetalert2/src/sweetalert2.scss';
import Select2 from 'v-select2-component'
import IjcoreModalListing from "../../../../components/IjcoreModalListing";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import IjcoreDatePicker from '@/components/IjcoreDatePicker';
import EmployeeModalSearchInputCatelist from "./EmployeeModalSearchInputCatelist";
import EmployeeModalSearchUser from "./EmployeeModalSearchUser";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";

const ListRouter = 'listing-employee';
const EditRouter = 'listing-employee-edit';
const ViewRouter = 'listing-employee-view';
const CreateRouter = 'listing-employee-create';
const ViewApi = 'listing/api/employee/view';
const EditApi = 'listing/api/employee/edit';
const CreateApi = 'listing/api/employee/create';
const StoreApi = 'listing/api/employee/store';
const UpdateApi = 'listing/api/employee/update';
const ListApi = 'listing/api/employee';

export default {
  name: 'listing-view-item',
  data () {
    return {
      idParams: this.idParamsProps,
      reqParams: this.reqParamsProps,
      model: {
        EmployeeID: null,
        EmployeeNo: '',
        FirstName: '',
        MiddleName: '',
        LastName: '',
        EmployeeName: '',
        ShortName: '',
        CompanyID: null,
        CompanyName: '',
        DepartmentID: '',
        DepartmentNo: '',
        DepartmentName: '',
        PositionName: '',
        BirthDay: null,
        CitizenIdNo: '',
        CitizenIdDate: '',
        CitizenIdAt: '',
        OfficePhone: '',
        HandPhone: '',
        Email: '',
        FacebookID: null,
        TwitterID: null,
        SkypeID: null,
        ZaloID: null,
        UserID: null,
        UserName: '',
        Note: '',
        Inactive: false,
        EmployeeNameType: 3,

        EmployeeCate: [],

      },
      PositionOption: [],
      stage: {
        isNotification: false,
        updatedData: false
      }
    }

  },
  props: {
    idParamsProps: {
      type: Number,
      default: 0
    },
    reqParamsProps: {
      type: Object,
      default: function () {
        return {}
      }
    },
    itemCopy: {
      type: Object,
      default: function () {
        return {}
      }
    }
  },
  components: {
    IjcoreModalListing,
    Select2,
    IjcoreModalSearchInput,
    IjcoreDatePicker,
    EmployeeModalSearchInputCatelist,
    EmployeeModalSearchUser,
    IjcoreModalSearchListing,
  },
  beforeCreate() {},
  mounted() {
    this.fetchData();
  },
  updated() {
    this.stage.updatedData = true;
  },
  computed: {
    itemNo(){
      let index = 0;
      index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
      return index;
    },
    fullName(){
      if(this.model.EmployeeNameType === 1){
        return this.model.LastName + ' ' + this.model.MiddleName + ' ' + this.model.FirstName;
      }
      else if(this.model.EmployeeNameType === 2){
        return this.model.FirstName + ' ' + this.model.LastName + ' ' + this.model.MiddleName;
      }
      else{
        return this.model.FirstName + ' ' + this.model.MiddleName + ' ' + this.model.LastName;
      }
    }
  },
  methods: {
    fetchData() {
      let self = this;
      let urlApi = CreateApi;
      let requestData = {
        method: 'get',
        data: {}
      };
      // Api edit user
      if(this.idParams){
        urlApi = EditApi + '/' + this.idParams;
        requestData.data.id = this.idParams;
      }
      requestData.url = urlApi;
      this.$store.commit('isLoading', true);

      ApiService.setHeader();
      ApiService.customRequest(requestData).then((responses) => {
        let responsesData = responses.data;

        // copy item
        if (!self.idParams && !_.isEmpty(self.itemCopy)) {
          responsesData.data.data = self.itemCopy.data.data;
        }

        if (responsesData.status === 1) {

          if (self.idParams || !_.isEmpty(self.itemCopy)) {
            if (_.isObject(responsesData.data.data)) {
              self.model.EmployeeID = responsesData.data.data.EmployeeID;
              self.model.EmployeeName = responsesData.data.data.EmployeeName;
              self.model.EmployeeNo = responsesData.data.data.EmployeeNo;
              self.model.Email = responsesData.data.data.Email;
              self.model.Note = responsesData.data.data.Note;
              self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
            }

            if (!_.isEmpty(self.itemCopy)) {
              self.model.EmployeeNo = responsesData.data.auto;
            }
          }else {
            self.model.EmployeeNo = responsesData.data.auto;
          }

          if(_.isArray(responsesData.data.position)){
            _.forEach(responsesData.data.position, function(value,key){
              let tmpObj = {};
              tmpObj.id = value.PositionName;
              tmpObj.text = value.PositionName;
              self.PositionOption.push(tmpObj);
            })
          }
          self.model.EmployeeNameType = responsesData.data.employeeNameType;

        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });
    },
    changeParent(){
      let self = this;
      let urlApi = this.api;
      let requestData = {
        method: 'post',
        url: '/listing/api/common/auto-child',
        data: {
          per_page: 10,
          page: this.currentPage,
          table: 'employee',
        },

      };

      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;

        this.model.EmployeeNo = responseData.data;
        self.$store.commit('isLoading', false);
      }, (error) => {
        self.$store.commit('isLoading', false);
      });
    },
    onNavigationItem(type) {
      let currentIndex = this.reqParams.idsArray.indexOf(this.idParams);
      let newIndex = (type === 'prev') ? currentIndex - 1 : currentIndex + 1;

      if (newIndex === (this.reqParams.idsArray.length) && (this.reqParams.currentPage != this.reqParams.lastPage)) {
        this.reqParams.currentPage = this.reqParams.currentPage + 1;
        this.getItemIds(type);
      } else if (newIndex < 0 && this.reqParams.currentPage > 1){
        this.reqParams.currentPage = this.reqParams.currentPage - 1;
        this.getItemIds(type);
      }
      else {
        this.idParams = (this.reqParams.idsArray[newIndex]) ? this.reqParams.idsArray[newIndex] : this.idParams;
      }
    },
    getItemIds(type){
      let self = this;
      let requestData = {
        method: 'post',
        url: ListApi,
        data: {
          per_page: this.reqParams.perPage,
          page: this.reqParams.currentPage,
          type: 'only-id'
        }
      };

      if (this.reqParams.search.employeeNo !== '') {
        requestData.data.EmployeeNo = this.reqParams.search.employeeNo;
      }
      if (this.reqParams.search.employeeName !== '') {
        requestData.data.EmployeeName = this.reqParams.search.employeeName;
      }
      if (this.reqParams.search.email !== '') {
        requestData.data.Email = this.reqParams.search.email;
      }

      this.$store.commit('isLoading', true);
      ApiService.customRequest(requestData).then((response) => {
        let dataResponse = response.data;
        if (dataResponse.status === 1) {
          self.reqParams.total = dataResponse.data.total;
          self.reqParams.perPage = String(dataResponse.data.per_page);
          self.reqParams.currentPage = dataResponse.data.current_page;

          this.reqParams.idsArray = [];
          _.forEach(dataResponse.data.data, function (value, key) {
            self.reqParams.idsArray.push(value.EmployeeID);
          });

          (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });

    },

    handleSubmitForm(){
      let self = this;
      const requestData = {
        method: 'post',
        url: StoreApi,
        data: {
          EmployeeNo: this.model.EmployeeNo,
          FirstName: this.model.FirstName,
          MiddleName: this.model.MiddleName,
          LastName: this.model.LastName,
          EmployeeName: this.fullName,
          CompanyID: this.model.CompanyID,
          CompanyName : this.model.CompanyName,
          BirthDay: this.model.BirthDay,
          CitizenIdNo: this.model.CitizenIdNo,
          CitizenIdDate: this.model.CitizenIdDate,
          CitizenIdAt: this.model.CitizenIdAt,
          OfficePhone: this.model.OfficePhone,
          HandPhone: this.model.HandPhone,
          FacebookID: this.model.FacebookID,
          TwitterID: this.model.TwitterID,
          SkypeID: this.model.SkypeID,
          ZaloID: this.model.ZaloID,
          Inactive: (this.model.Inactive) ? 1 : 0,
          Email: this.model.Email,
          Note: this.model.Note,
          DepartmentID: this.model.DepartmentID,
          DepartmentNo: this.model.DepartmentNo,
          DepartmentName: this.model.DepartmentName,
          PositionName: this.model.PositionName,
          EmployeeCate: this.model.EmployeeCate
        }
      };

      // edit user
      if (this.idParams) {
        requestData.data.ItemID = this.idParams;
        requestData.url = UpdateApi + '/' + this.idParams;
      }

      this.$store.commit('isLoading', true);
      ApiService.setHeader();
      ApiService.customRequest(requestData).then((responses) => {
        let responsesData = responses.data;
        if (responsesData.status === 1) {
          if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
          self.$router.push({
            name: ViewRouter,
            params: {id: self.idParams, message: 'Bản ghi đã được cập nhật!'}
          });
        } else {
          let htmlErrors = __.renderErrorApiHtml(responsesData.data);
          Swal.fire(
            'Thông báo',
            htmlErrors,
            'error'
          )
        }

        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        Swal.fire(
          'Thông báo',
          'Không kết nối được với máy chủ',
          'error'
        );
        self.$store.commit('isLoading', false);
      });
    },

    onEditClicked(){
      this.$router.push({
        name: EditRouter,
        params: {id: this.idParams, req: this.reqParams}
      });
    },
    onCreateClicked(){
      this.$router.push({name: CreateRouter});
    },
    onBackToList() {
      this.$router.push({name: ListRouter});
    },

    updateModel() {
      if (this.stage.updatedData) {
        this.$forceUpdate();
      }
    },

    autoCorrectedTaxRatePipe() {

    },

  },
  watch: {
    idParams() {
      this.fetchData();
    },
  }
}
</script>
<!--<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>-->
<style lang="css">
.select2-container{
  width: 100% !important;
}
.mx-datepicker{
  width: 100% !important;
}
.form-control:disabled, .form-control[readonly]{
  background: transparent;
}
</style>
