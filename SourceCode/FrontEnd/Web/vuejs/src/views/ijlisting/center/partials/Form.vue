<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Cơ quan trung ương<span v-if="model.CenterName">:</span> {{model.CenterName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Cơ quan trung ương<span v-if="model.CenterName">:</span> {{model.CenterName}}</span>
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
              <label class="col-md-3 m-0" for="CenterHandPhone">Tên</label>
              <div class="col-md-15">
                <input v-model="model.CenterName" type="text" id="CenterName" class="form-control" placeholder="Tên cơ quan trung ương" name="CenterName" />
              </div>
              <label class="col-md-2 m-0" for="CenterHandPhone">Mã số</label>
              <div class="col-md-4 mb-3 mb-sm-0">
                <input v-model="model.CenterNo" type="text" class="form-control" placeholder="Mã số" name="CenterName" />
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" for="CenterHandPhone">Địa chỉ</label>
              <div class="col-md-21">
                <input v-model="model.CenterAddress" type="text" id="CenterAddress" class="form-control" placeholder="Địa chỉ" name="CenterAddress" />
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" for="CenterHandPhone">ĐT di động</label>
              <div class="col-md-5 mb-3 mb-sm-0">
                <input type="number"  v-model="model.CenterHandPhone" id="CenterHandPhone" class="form-control" placeholder="Điện thoại di động" name="CenterHandPhone"/>
              </div>

              <label class="col-md-3 m-0" for="CenterTel">ĐT cố định</label>
              <div class="col-md-5 mb-3 mb-sm-0">
                <input type="number" v-model="model.CenterTel" id="CenterTel" class="form-control" placeholder="Điện thoại cố định" name="CenterTel"/>
              </div>

              <label class="col-md-2 m-0" for="CenterFax">Fax</label>
              <div class="col-md-6">
                <input type="text" v-model="model.CenterFax" id="CenterFax" class="form-control" placeholder="Fax" name="CenterFax"/>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">E-Mail</label>
              <div class="col-md-9 mb-3 mb-sm-0">
                <input v-model="model.CenterEmail" type="email" class="form-control" placeholder="E-Mail" name="CenterEmail">
              </div>
              <label class="col-md-3 m-0" for="CenterWebsite">Website</label>
              <div class="col-md-9">
                <input v-model="model.CenterWebsite" type="text" id="CenterWebsite" class="form-control" placeholder="Website" name="CenterWebsite">
              </div>
            </div>
            <div class="form-group row align-items-center center">
              <label class="col-md-3 m-0">Loại</label>
              <div class="col-md-9">
                <b-form-select :options="CenterTypeOptions" v-model="model.CenterType"></b-form-select>
              </div>
            </div>
            <hr>
            <b>Đại diện: </b>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Tên</label>
              <div class="col-md-9 mb-3 mb-sm-0">
                <input v-model="model.ContactName" type="text" class="form-control" placeholder="Tên đại diện" name="ContactName">
              </div>
              <label class="col-md-3 m-0" for="CenterWebsite">Chức vụ</label>
              <div class="col-md-9">
                <input v-model="model.ContactTitle" type="text" class="form-control" placeholder="Chức vụ" name="ContactTitle">
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">ĐT di động</label>
              <div class="col-md-9 mb-3 mb-sm-0">
                <input v-model="model.ContactHandPhone" type="number" class="form-control" placeholder="ĐT di động" name="ContactHandPhone">
              </div>
              <label class="col-md-3 m-0" for="ContactTel">ĐT cố định</label>
              <div class="col-md-9">
                <input v-model="model.ContactTel" type="number" id="ContactTel" class="form-control" placeholder="ĐT cố định" name="ContactTel">
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Email</label>
              <div class="col-md-9 mb-3 mb-sm-0">
                <input v-model="model.ContactEmail" type="email" class="form-control" placeholder="Email" name="ContactEmail">
              </div>
            </div>
            <hr>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0"><b>Ghi chú</b></label>
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

const ListRouter = 'listing-center';
const EditRouter = 'listing-center-edit';
const CreateRouter = 'listing-center-create';
const ViewRouter = 'listing-center-view';
const ViewApi = 'listing/api/center/view';
const EditApi = 'listing/api/center/edit';
const CreateApi = 'listing/api/center/create';
const StoreApi = 'listing/api/center/store';
const UpdateApi = 'listing/api/center/update';
const ListApi = 'listing/api/center';


export default {
  name: 'listing-project-view',
  data () {
    return {
      idParams: this.idParamsProps,
      reqParams: this.reqParamsProps,
      model: {
        CenterNo: '',
        CenterName: '',
        CenterAddress: '',
        CenterTel: '',
        CenterHandPhone: '',
        CenterFax: '',
        CenterEmail: '',
        CenterWebsite: '',
        ContactName: '',
        ContactTitle: '',
        ContactTel: '',
        ContactHandPhone: '',
        ContactEmail: '',
        Note: '',
        inactive: '',
        CenterType: null,
      },
      CenterTypeOptions: [
        {value: null, text: '--- Chọn loại cơ quan trung ương ---'},
        {value: 1, text: 'Bộ'},
        {value: 2, text: 'Cơ quan ngang bộ'},
        {value: 3, text: 'Cơ quan trực thuộc trung chính phủ'},
      ],
      stage: {
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

        if (responsesData.status === 1 || !_.isEmpty(self.itemCopy)) {

          if (self.idParams || !_.isEmpty(self.itemCopy)) {
            if (_.isObject(responsesData.data.data)) {
              self.model.CenterNo = responsesData.data.data.CenterNo;
              self.model.CenterName = responsesData.data.data.CenterName;
              self.model.CenterAddress = responsesData.data.data.CenterAddress;
              self.model.CenterTel = responsesData.data.data.CenterTel;
              self.model.CenterHandPhone = responsesData.data.data.CenterHandPhone;
              self.model.CenterFax = responsesData.data.data.CenterFax;
              self.model.CenterEmail = responsesData.data.data.CenterEmail;
              self.model.CenterWebsite = responsesData.data.data.CenterWebsite;
              self.model.ContactName = responsesData.data.data.ContactName;
              self.model.ContactTitle = responsesData.data.data.ContactTitle;
              self.model.ContactTel = responsesData.data.data.ContactTel;
              self.model.ContactHandPhone = responsesData.data.data.ContactHandPhone;
              self.model.ContactEmail = responsesData.data.data.ContactEmail;
              self.model.Note = responsesData.data.data.Note;
              self.model.CenterType = responsesData.data.data.CenterType;
              self.model.inactive = (responsesData.data.data.Inactive) ? true : false;
            }
            if (!_.isEmpty(self.itemCopy)) {
              self.model.CenterNo = responsesData.data.auto;
            }
          } else {
            self.model.CenterNo = responsesData.data.auto;
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

      if (this.reqParams.search.CenterNo !== '') {
        requestData.data.CenterNo = this.reqParams.search.CenterNo;
      }
      if (this.reqParams.search.CenterName !== '') {
        requestData.data.CenterName = this.reqParams.search.CenterName;
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
            self.reqParams.idsArray.push(value.ExpenseID);
          });

          (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });

    },
    onChangedProjectParent(){
      this.model.parentID = (this.model.parentObj) ? this.model.parentObj.value : null;
    },
    onChangedProjectCompany(){
      this.model.companyID = (this.model.companyObj) ? this.model.companyObj.value : null;
    },
    handleSubmitForm(){
      let self = this;
      const requestData = {
        method: 'post',
        url: StoreApi,
        data: {
          CenterNo: this.model.CenterNo,
          CenterName: this.model.CenterName,
          CenterAddress: this.model.CenterAddress,
          CenterTel: this.model.CenterTel,
          CenterHandPhone: this.model.CenterHandPhone,
          CenterFax: this.model.CenterFax,
          CenterEmail: this.model.CenterEmail,
          CenterWebsite: this.model.CenterWebsite,
          ContactName: this.model.ContactName,
          ContactTitle: this.model.ContactTitle,
          ContactTel: this.model.ContactTel,
          ContactHandPhone: this.model.ContactHandPhone,
          ContactEmail: this.model.ContactEmail,
          Note: this.model.Note,
          Inactive: (this.model.inactive) ? 1 : 0,
          CenterType: this.model.CenterType
        }
      };

      // edit user
      if (this.idParams) {
        requestData.data.CcyID = this.idParams;
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
</style>
