<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Vùng<span v-if="model.AreaName">:</span> {{model.AreaName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Vùng<span v-if="model.AreaName">:</span> {{model.AreaName}}</span>
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
              <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Vùng</div>
              <div class="col-lg-16 col-md-14 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
                <input v-model="model.AreaName" type="text" id="AreaName" class="form-control" placeholder="Tên vùng" name="AreaName" />
              </div>
              <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.AreaNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Quốc gia</div>
              <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0 mr-5">
                <ijcore-modal-search-input
                  v-model="model.Country"
                  :select-fields-api="[
                                {field: 'CountryID',fieldForSelected: 'id', showInTable: false, key: 'CountryID'},
                                {field: 'CountryName', fieldForSelected: 'name', showInTable: true, label: 'Tên quốc gia', key: 'CountryName', sortable: true, thClass: 'd-none'}
                              ]"
                  :search-fields-api="[{field: 'CountryName', placeholder: 'Nhập tên', name: 'CountryName', class: '', style: ''}]"
                  table="country"
                  ref="myModalSearchInputCountry"
                  id-modal="myModalSearchInputCountry"
                  :item-per-page="8"
                  placeholder="Quốc gia"
                  :url-api="$store.state.appRootApi + '/listing/api/area/get-country'"
                  name-input="input-country"
                  title-modal="Quốc gia" size-modal="lg">
                </ijcore-modal-search-input>
              </div>

              </div>
          </b-card>
        </div>
      </vue-perfect-scrollbar>
    </div>
  </div>
</template>

<script>
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import ApiService from '@/services/api.service';
import Swal from 'sweetalert2';
import 'sweetalert2/src/sweetalert2.scss';
import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
import moment from 'moment';

moment.locale('vi');


const ListRouter = 'listing-area';
const EditRouter = 'listing-area-edit';
const CreateRouter = 'listing-area-create';
const ViewRouter = 'listing-area-view';
const ViewApi = 'listing/api/area/view';
const EditApi = 'listing/api/area/edit';
const CreateApi = 'listing/api/area/create';
const StoreApi = 'listing/api/area/store';
const UpdateApi = 'listing/api/area/update';
const ListApi = 'listing/api/area';

const dataTypeOption = {
  1: 'Số',
  2: 'Kí tự',
  3: 'Ngày',
  4: 'Ngày giờ',
  5: 'Có/Không',
  6: 'Đúng/Sai'
};

const DataTypeOption = [
  {value: 1, text: 'Số'},
  {value: 2, text: 'Kí tự'},
  {value: 3, text: 'Ngày'},
  {value: 4, text: 'Ngày giờ'},
  {value: 5, text: 'Có/Không'},
  {value: 6, text: 'Đúng/Sai'}
];

export default {
  name: 'listing-area-view',
  data() {
    return {
      idParams: this.idParamsProps,
      reqParams: this.reqParamsProps,
      model: {
        AreaNo: '',
        AreaName: '',
        Area: {},
        Country: {},
        Inactive: ''
      },
      stage: {
        updatedData: false
      },
    }

  },
  components: {
    IjcoreModalSearchInput
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
    },
    dataTypeOption() {
      return DataTypeOption;
    }
  },
  methods: {
    handleDebugger(value) {
      alert('aaa');
    },
    fetchData() {
      let self = this;
      let urlApi = CreateApi;
      let requestData = {
        method: 'get',
        data: {}
      };
      // Api edit user
      if (this.idParams) {
        urlApi = EditApi + '/' + this.idParams ;
        requestData.data.id = this.idParams;
      }
      requestData.url = urlApi;
      this.$store.commit('isLoading', true);

      ApiService.setHeader();
      ApiService.customRequest(requestData).then((responses) => {

        let responsesData = responses.data; //console.log(responses.data);
        // copy item
        if (!self.idParams && !_.isEmpty(self.itemCopy)) {
          responsesData = self.itemCopy;
        }

        if (responsesData.status === 1) {

          if (self.idParams || !_.isEmpty(self.itemCopy)) {
            if (_.isObject(responsesData.data.data)) {

              self.model.AreaNo = responsesData.data.data.AreaNo;
              self.model.AreaName = responsesData.data.data.AreaName;
              self.model.Country.CountryName = responsesData.data.data.CountryName;
              self.model.Country.CountryID = responsesData.data.data.CountryID;
              self.model.inactive = (responsesData.data.data.Inactive) ? true : false;

              // set max lineID
              _.forEach(self.model.vendorCateValue, function (field, key) {
                if (Number(field.LineID) > self.model.maxLineID) self.model.maxLineID = Number(field.LineID);
                // set type of value
                if (field.DataType == 1) self.model.vendorCateValue[key].CateValue = Number(self.model.vendorCateValue[key].CateValue);
                if (field.DataType == 2) self.model.vendorCateValue[key].CateValue = String(self.model.vendorCateValue[key].CateValue);
                if (field.DataType == 3) self.model.vendorCateValue[key].CateValue = moment(self.model.vendorCateValue[key].CateValue).format('DD/MM/YYYY');
                if (field.DataType == 4) self.model.vendorCateValue[key].CateValue = moment(self.model.vendorCateValue[key].CateValue).format('DD/MM/YYYY hh:mm');
              });

            }
            if (!_.isEmpty(self.itemCopy)) {
              self.model.BankNo = responsesData.data.auto;
            }
          }else {
            self.model.BankNo = responsesData.data.auto;
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

      if (this.reqParams.search.CateName !== '') {
        requestData.data.CateName = this.reqParams.search.CateName;
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
            self.reqParams.idsArray.push(value.CateID);
          });

          (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });

    },
    onChangeDataType(value, field) {
      if (value === 1) field.CateValue = 0;
      if (value === 2) field.CateValue = '';
      if (value === 3) field.CateValue = moment().format('DD/MM/YYYY');
      if (value === 4) field.CateValue = moment().format('DD/MM/YYYY hh:mm');
      if (value === 5 || value === 6) field.CateValue = 1;
      this.$forceUpdate();
    },
    onAddFieldOnTable() {
      let fieldObj = {};
      this.model.maxLineID += 1;
      fieldObj.LineID = this.model.maxLineID;
      fieldObj.LinkNo = '';
      fieldObj.LinkName = '';
      fieldObj.LinkTable = '';
      this.model.BankLink.push(fieldObj);
      this.$forceUpdate();
    },
    onDeleteFieldOnTable(field) {

      // remove field in fieldOnTableReq
      let fieldExist = _.find(this.model.BankLink, ['LineID', field.LineID]);
      if (_.isObject(fieldExist)) {
        _.remove(this.model.BankLink, ['LineID', field.LineID]);
      }
      this.$forceUpdate();
    },
    handleSubmitForm() {
      let self = this;
      const requestData = {
        method: 'post',
        url: StoreApi ,
        data: {
            AreaNo: this.model.AreaNo,
            AreaName: this.model.AreaName,
            CountryID: this.model.Country.CountryID,
            CountryName: this.model.Country.CountryName,
            Inactive: (this.model.inactive) ? 1 : 0
        }
      };

      // edit user
      if (this.idParams) {
        requestData.url = UpdateApi + '/' + this.idParams ;
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
    autoCorrectedDatePipe: () => {
      return createAutoCorrectedDatePipe('dd/mm/yyyy')
    },
    autoCorrectedDateTimePipe: () => {
      return createAutoCorrectedDatePipe('dd/mm/yyyy hh:mm')
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
</style>
