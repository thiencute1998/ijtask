<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Tài liệu<span v-if="model.DocName">:</span> {{model.DocName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Tài liệu<span v-if="model.DocName">:</span> {{model.DocName}}</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitForm"><i
                class="fa fa-check-square-o"></i> Lưu
              </b-button>
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onBackToList"><i
                class="fa fa-ban"></i> Hủy
              </b-button>
            </div>
          </b-col>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-icons">
              <div class="main-header-collapse">
                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen="true"/>
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
              <div class="col-lg-4 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tên</div>
              <div class="col-lg-20 col-md-20 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3">
                <input v-model="model.DocName" type="text" class="form-control" placeholder="Tên tài liệu"/>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <div class="col-lg-4 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tài liệu cha</div>
              <div class="col-lg-20 col-md-20 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3">
                <IjcoreModalDocSearch  v-model="model" :title="'tài liệu'" :api="'/doc/api/doc/get-list'"
                                       :table="'doc'" :FieldID="'DocID'" :FieldName="'DocName'"
                ></IjcoreModalDocSearch>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <div class="col-lg-4 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap" title="Đơn vị ban hành">Đơn vị ban hành</div>
              <div class="col-lg-20 col-md-20 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3">
                <input v-model="model.CompanyIssued" type="text" class="form-control" placeholder="Đơn vị ban hành"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <div class="col-lg-4 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Ngày ký</div>
              <div class="col-lg-4 col-md-22 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 width-datepicker-auto">
                <IjcoreDatePicker v-model="model.DocDate">
                </IjcoreDatePicker>
              </div>
              <div class="col-lg-4 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Ngày hiệu lực</div>
              <div class="col-lg-4 col-md-22 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 width-datepicker-auto">
                <IjcoreDatePicker v-model="model.EffectiveDate">
                </IjcoreDatePicker>
              </div>
              <div class="col-lg-2 col-md-2 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Người ký</div>
              <div class="col-lg-6 col-md-6 col-sm-6 mb-sm-4 mb-md-3 mb-lg-0 mb-3">
                <input v-model="model.SignerName" type="text" class="form-control" placeholder="Họ và tên người ký"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-4 col-sm-4 m-0" for="Note" title="Người phân quyền">Người phân quyền</label>
              <div class="col-md-4 col-sm-4 width-select2-auto">
                <Select2 v-model="model.AuthorizedPerson" :settings="{allowClear: true, placeholder: {id: null, text: 'Chọn nhân viên'}}" :options="EmployeeOption"></Select2>
              </div>
              <label class="col-md-4 col-sm-4 m-0" for="Note">Quyền truy cập</label>
              <div class="col-md-4 col-sm-2">
                <b-form-select v-model="model.AccessType" :options="AccessTypeOptions"
                               id="item-uom"></b-form-select>
              </div>
              <div class="col-md-8 col-sm-8 width-select2-auto" v-if="model.AccessType==2">
                <Select2 v-model="model.PublicCompanyID" @change="changeCompany" multiple="true" :settings="{multiple: true, allowClear: true, placeholder: {id: null, text: 'Chọn đơn vị'}}" :options="CompanyOption"></Select2>
              </div>
            </div>
<!--            <div class="form-group row" v-if="model.AccessType==2">-->
<!--              <label class="col-md-2 col-sm-2 m-0" for="Note">Đơn vị</label>-->
<!--              <div class="col-md-10 col-sm-10 width-select2-auto">-->
<!--                <Select2 v-model="model.PublicCompanyID" @change="changeCompany" multiple="true" :settings="{multiple: true, allowClear: true, placeholder: {id: null, text: 'Chọn đơn vị'}}" :options="CompanyOption"></Select2>-->
<!--              </div>-->
<!--              <label class="col-md-2 col-sm-2 m-0" for="Note">Nhóm</label>-->
<!--              <div class="col-md-10 col-sm-10 width-select2-auto">-->
<!--                <Select2 v-model="model.PublicGroupID" @change="changeGroup" multiple="true" :settings="{multiple: true, allowClear: true, placeholder: {id: null, text: 'Chọn nhóm'}}" :options="GroupOption"></Select2>-->
<!--              </div>-->
<!--            </div>-->
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
  import vSelect from 'vue-select';
  import Select2 from 'v-select2-component';
  import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
  import IjcoreModalDocSearch from "../../../../components/IjcoreModalDocSearch";
  import moment from "moment";

  const ListRouter = 'doc-doc';
  const EditRouter = 'doc-doc-edit';
  const CreateRouter = 'doc-doc-create';
  const ViewRouter = 'doc-doc-view';
  const DetailApi = 'doc/api/doc/view';
  const EditApi = 'doc/api/doc/edit';
  const CreateApi = 'doc/api/doc/create';
  const StoreApi = 'doc/api/doc/store';
  const UpdateApi = 'doc/api/doc/update';
  const ListApi = 'doc/api/doc';

  export default {
    name: 'doc-doc-form',
    data() {
      return {
        idParams: this.idParamsProps,
        reqParams: this.reqParamsProps,
        EmployeeLogin: JSON.parse(localStorage.getItem('Employee')),
        model: {
          DocName: '',
          AcessType: 1,
          ParentID: '',
          ParentName: '',
          Level: '',
          Detail: '',
          DocDate: moment().format('DD/MM/YYYY'),
          EffectiveDate: moment().format('DD/MM/YYYY'),
          SignerName: '',
          CompanyIssued: '',
          AccessType: 1,
          PublicCompanyID: '',
          PublicGroupID: '',
          Path: '',
          UserIDCreated: '',
          Title: '',
          AuthorizedPerson: '',
        },
        EmployeeOption: [],
        CompanyOption: [],
        GroupOption: [],
        AccessTypeOptions:{
          1: 'Chia sẻ',
          2: 'Công khai',
          3: 'Riêng tư'
        },
        stage: {
          updatedData: false
        },
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
      IjcoreModalDocSearch,
      IjcoreDatePicker,
      vSelect,
      Select2,
    },
    beforeCreate() {
    },
    mounted() {
      this.fetchData();
    },
    updated() {
      this.stage.updatedData = true;
    },
    computed: {
      itemNo() {
        let index = 0;
        index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
        return index;
      }
    },
    methods: {
      changeCompany(){
        console.log(this.model.PublicCompanyID.length);
      },
      changeGroup(){
        console.log(this.model.PublicCompanyID);
      },
      fetchData() {
        this.model.UserIDCreated = this.EmployeeLogin.EmployeeID;
        this.model.AuthorizedPerson = this.EmployeeLogin.EmployeeID;
        let self = this;
        let urlApi = CreateApi;
        let requestData = {
          method: 'get',
          data: {}
        };
        // Api edit user
        if (this.idParams) {
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
            responsesData.data = self.itemCopy;
          }
          if (responsesData.status === 1) {
            self.EmployeeOption = [];
            _.forEach(responsesData.Employee, function (val, key) {
              let tmpObj = {};
              tmpObj.id = val.EmployeeID;
              tmpObj.text = val.EmployeeName;
              self.EmployeeOption.push(tmpObj);
            });
            self.CompanyOption = [];
            _.forEach(responsesData.Company, function (val, key) {
              let tmpObj = {};
              tmpObj.id = val.CompanyID;
              tmpObj.text = val.CompanyName;
              self.CompanyOption.push(tmpObj);
            });
            self.GroupOption = [];
            _.forEach(responsesData.Group, function (val, key) {
              let tmpObj = {};
              tmpObj.id = val.UserGroupID;
              tmpObj.text = val.UserGroupName;
              self.GroupOption.push(tmpObj);
            });
            if (self.idParams || !_.isEmpty(self.itemCopy)) {
                self.model = responsesData.data;
            } else {
            }

          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      onNavigationItem(type) {
        let currentIndex = this.reqParams.idsArray.indexOf(this.idParams);
        let newIndex = (type === 'prev') ? currentIndex - 1 : currentIndex + 1;

        if (newIndex === (this.reqParams.idsArray.length) && (this.reqParams.currentPage != this.reqParams.lastPage)) {
          this.reqParams.currentPage = this.reqParams.currentPage + 1;
          this.getItemIds(type);
        } else if (newIndex < 0 && this.reqParams.currentPage > 1) {
          this.reqParams.currentPage = this.reqParams.currentPage - 1;
          this.getItemIds(type);
        } else {
          this.idParams = (this.reqParams.idsArray[newIndex]) ? this.reqParams.idsArray[newIndex] : this.idParams;
        }
      },
      getItemIds(type) {
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

        if (this.reqParams.search.docNo !== '') {
          requestData.data.VendorNo = this.reqParams.search.docNo;
        }
        if (this.reqParams.search.docName !== '') {
          requestData.data.VendorName = this.reqParams.search.docName;
        }
        if (this.reqParams.search.officePhone !== '') {
          requestData.data.OfficePhone = this.reqParams.search.officePhone;
        }
        if (this.reqParams.search.fax !== '') {
          requestData.data.Fax = this.reqParams.search.fax;
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
              self.reqParams.idsArray.push(value.VendorID);
            });

            (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });

      },
      onChangedEmployeeCompany() {
        this.model.companyID = this.model.companyObj.value;
      },
      onChangedEmployeeManager() {
        this.model.managerID = this.model.managerObj.value;
      },
      onChangedEmployeeConcurrentManager() {
        this.model.concurrentManagerID = this.model.concurrentManagerObj.value;
      },
      onChangedEmployeeChecker() {
        this.model.checkerID = this.model.checkerObj.value;
      },
      onChangedEmployeeUser() {
        this.model.userID = this.model.userObj.value;
      },

      handleSubmitForm() {
        let self = this;
        if(self.model.PublicCompanyID){
          self.model.PublicCompanyIDValue = self.model.PublicCompanyID.join();
        }
        const requestData = {
          method: 'post',
          url: StoreApi,
          data: {
            model: self.model
          }
        };

        // edit user
        if (this.idParams) {
          requestData.data.DocID = this.idParams;
          requestData.url = UpdateApi + '/' + this.idParams;
        }

        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;

          self.idParams = responsesData.data;
          if (responsesData.status === 1) {
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

      onEditClicked() {
        this.$router.push({
          name: EditRouter,
          params: {id: this.idParams, req: this.reqParams}
        });
      },
      onCreateClicked() {
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

      }

    },
    watch: {
      idParams() {
        this.fetchData();
      },
    }
  }
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="css">
  .v-select .dropdown-menu {
    max-height: 170px !important;
  }

  .custom-align {
    flex: 0 0 12.3%;
  }
  .width-datepicker-auto .mx-datepicker{
    width: auto;
  }
  .width-select2-auto .select2-container{
    width: 100% !important;
  }
</style>
