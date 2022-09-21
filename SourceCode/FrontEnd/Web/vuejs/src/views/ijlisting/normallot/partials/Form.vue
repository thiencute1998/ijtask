<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i>Tiêu chí phân bổ dự toán<span v-if="model.NormAllotName">:</span> {{model.NormAllotName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i>Tiêu chí phân bổ dự toán<span v-if="model.NormAllotName">:</span> {{model.NormAllotName}}</span>
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
              <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
              <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                <input v-model="model.NormAllotName" type="text" id="NormAllotName" class="form-control" placeholder="TênTiêu chí phân bổ dự toán" name="NormAllotName"/>
              </div>
              <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.NormAllotNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Là mục con của</label>
              <div class="col-md-17">
                <IjcoreModalSysTemParent
                  v-model="model"
                  :title="'Tiêu chí phân bổ dự toán'"
                  :api="'/listing/api/common/list'"
                  :table="'norm_allot'"
                  :fieldName="'NormAllotName'"
                  :fieldNo="'NormAllotNo'"
                  :fieldID="'NormAllotID'"
                  :placeholderInput="'ChọnTiêu chí phân bổ dự toán cha'"
                  :placeholderSearch="'NhậpTiêu chí phân bổ dự toán'"
                  :columnID="'NormAllotID'"
                  :columnNo="'NormAllotNo'"
                  :columnName="'NormAllotName'">
                </IjcoreModalSysTemParent>
              </div>
              <div v-if="model.ParentID" class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.ParentNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Đơn vị tính</label>
              <div class="col-md-5 mb-3 mb-sm-0">
                <ijcore-modal-search-listing
                  v-model="model" :title="'Đơn vị tính'" :table="'uom'" :api="'/listing/api/common/list'"
                  :fieldID="'UomID'" :fieldNo="'UomNo'" :fieldName="'UomName'"
                  :fieldAssignID="'UomID'" :fieldAssignNo="'UomNo'" :fieldAssignName="'UomName'"
                >
                </ijcore-modal-search-listing>
              </div>
              <label class="col-md-3">Ngày hiệu lực</label>
              <div class="col-md-5 mb-sm-0">
                <IjcoreDatePicker v-model="model.EffectiveDate" ></IjcoreDatePicker>
              </div>
              <label class="col-md-4">Ngày  hết hiệu lực</label>
              <div class="col-md-4 mb-sm-0">
                <IjcoreDatePicker v-model="model.ExpirationDate" ></IjcoreDatePicker>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Loại TCPBDT</label>
              <div class="col-md-21">
                <norm-allot-modal-search-input-catelist
                  v-model="model.NormAllotCate"
                  title-modal="Loại tiêu chí phân bổ dự toán"
                  placeholder="Loại tiêu chí phân bổ dự toán"
                ></norm-allot-modal-search-input-catelist>
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
import IjcoreModalListing from "@/components/IjcoreModalListing";
import IjcoreModalSearchInput from "@/components/IjcoreModalSearchInput";
import NormAllotModalSearchInputCatelist from "@/views/ijlisting/normallot/partials/NormAllotModalSearchInputCatelist";
import IjcoreModalSysTemParent from "@/components/IjcoreModalSystemParent";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";
import IjcoreNumber from "@/components/IjcoreNumber";
import IjcoreDatePicker from "@/components/IjcoreDatePicker";


const ListRouter = 'listing-normallot';
const EditRouter = 'listing-normallot-edit';
const ViewRouter = 'listing-normallot-view';
const CreateRouter = 'listing-normallot-create';
const ViewApi = 'listing/api/norm-allot/view';
const EditApi = 'listing/api/norm-allot/edit';
const CreateApi = 'listing/api/norm-allot/create';
const StoreApi = 'listing/api/norm-allot/store';
const UpdateApi = 'listing/api/norm-allot/update';
const ListApi = 'listing/api/norm-allot';

export default {
  name: 'listing-view-item',
  data () {
    return {
      idParams: this.idParamsProps,
      reqParams: this.reqParamsProps,
      model: {
        SbiItemID: null,
        SbiItemNo: '',
        SbiItemName: '',
        NormAllotID: null,
        NormAllotNo: '',
        NormAllotName: '',
        ParentID: null,
        ParentNo: '',
        ParentName: null,
        NOrder: '',
        Inactive: false,
        UomID: null,
        UomNo: '',
        UomName: '',
        EffectiveDate:null,
        ExpirationDate:null,
        UomOption: [],
        NormAllotCate: [],
      },
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
    IjcoreDatePicker,
    IjcoreModalListing,
    Select2,
    NormAllotModalSearchInputCatelist,
    IjcoreModalSysTemParent,
    IjcoreModalSearchInput,
    IjcoreModalSearchListing,
    IjcoreNumber
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

        if (responsesData.status === 1) {

          if (self.idParams || !_.isEmpty(self.itemCopy)) {
            if (_.isObject(responsesData.data.data)) {
              self.model.NormAllotID = responsesData.data.data.NormAllotID;
              self.model.ParentID = responsesData.data.data.ParentID;
              self.model.NormAllotName = responsesData.data.data.NormAllotName;
              self.model.NormAllotNo = responsesData.data.data.NormAllotNo;
              self.model.UomID = responsesData.data.data.UomID;
              self.model.UomNo = responsesData.data.data.UomNo;
              self.model.UomName = responsesData.data.data.UomName;
              self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
              self.model.EffectiveDate = (responsesData.data.data.EffectiveDate)? self.onFormatDate(responsesData.data.data.EffectiveDate) : null;
              self.model.ExpirationDate = (responsesData.data.data.EffectiveDate)? self.onFormatDate(responsesData.data.data.ExpirationDate) : null;
            }

            if (!_.isEmpty(self.itemCopy)) {
              self.model.NormAllotNo = '';
            }
          }else {
            self.model.NormAllotNo = '';
          }


          if (_.isArray(responsesData.data.normAllot)) {
            self.model.NormAllotOption = [];
            _.forEach(responsesData.data.normAllot, function (value, key) {
              let tmpObj = {};
              tmpObj.id = value.NormAllotID;
              tmpObj.text = value.NormAllotName;
              self.model.NormAllotOption.push(tmpObj);
            });
          }

          if (_.isArray(responsesData.data.uom)) {

            self.model.UomOption = [];
            _.forEach(responsesData.data.uom, function (value, key) {
              let tmpObj = {};
              tmpObj.id = value.UomID;
              tmpObj.text = value.UomName;
              self.model.UomOption.push(tmpObj);
            });
          }
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
      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;

        this.model.NormAllotNo = responseData.data;
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

      if (this.reqParams.search.normAllotNo !== '') {
        requestData.data.NormAllotNo = this.reqParams.search.normAllotNo;
      }
      if (this.reqParams.search.normAllotName !== '') {
        requestData.data.NormAllotName = this.reqParams.search.normAllotName;
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
            self.reqParams.idsArray.push(value.NormAllotID);
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
        url: StoreApi+'?XDEBUG_SESSION_START=PHPSTORM',
        data: {
          NormAllotNo: this.model.NormAllotNo,
          NormAllotName: this.model.NormAllotName,
          Inactive: (this.model.Inactive) ? 1 : 0,
          ParentID: this.model.ParentID,
          ParentNo: this.model.ParentNo,
          UomID : this.model.UomID,
          UomNo : this.model.UomNo,
          UomName : this.model.UomName,
          EffectiveDate : this.model.EffectiveDate,
          ExpirationDate : this.model.ExpirationDate,
          NormAllotCate: this.model.NormAllotCate,
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
          let item = {
            NormAllotID: responsesData.data,
            NormAllotNo: self.model.NormAllotNo,
            NormAllotName: self.model.NormAllotName,
            Inactive: (self.model.Inactive) ? 1 : 0,

            ParentID: self.model.ParentID,
            ParentNo: self.model.ParentNo,
            UomID: self.model.UomID,
            UomNo: self.model.UomNo,
            UomName: self.model.UomName,
            EffectiveDate: self.model.EffectiveDate,
            ExpirationDate: self.model.ExpirationDate,
            NormAllotCate: self.model.NormAllotCate,
          }
          let indexold =  _.findIndex(self.$route.params.req.itemsArray, {'NormAllotID': item.NormAllotID});
          let indexParent = null;
          if(indexold  < 0){
            if(self.model.ParentID){
              indexParent = _.findIndex(self.$route.params.req.itemsArray, {'NormAllotID': self.model.ParentID});
              if(indexParent >= 0){
                let level = self.$route.params.req.itemsArray[indexParent].Level;
                if(self.$route.params.req.itemsArray[indexParent].Detail) {
                  self.$set(self.$route.params.req.itemsArray[indexParent], 'Detail', 0);
                  self.$set(self.$route.params.req.itemsArray[indexParent], 'Class', 'fa fa-minus-square-o');
                }
                item.Level = level + 1;
                item.Detail = 1;
                self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, indexParent + 1, item);
              }
            } else {
              item.Level = 1;
              item.Detail = 1;
              self.$route.params.req.itemsArray.push(item);
              _.orderBy(self.$route.params.req.itemsArray, ['NormAllotID'], 'asc');
            }
          }
          self.onBackToList('Bản ghi đã được cập nhật');
        }else if (responsesData.status === 4){
          Swal.fire(
            'Lỗi',
            'Không được sửa bản ghi Tổng hợp',
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


    RegulationChange(){
      let self = this
      if(self.model.isNormAllotRegulationRate === 0){
        self.model.isNormAllotRegulationRate = 1;
      }  else {
        self.model.isNormAllotRegulationRate = 0;
      }
    },
    onEditClicked(){
      this.$router.push({
        name: EditRouter,
        params: {id: this.idParams, req: this.reqParams}
      });
    },
    onFormatDate(formatDate){
      return formatDate.split('-').reverse().join().replaceAll('/');
    },
    onCreateClicked(){
      this.$router.push({name: CreateRouter});
    },
    onBackToList(message = '') {

      let self = this;
      let params = (this.$route.params.req) ? this.$route.params.req:{};
      let query = this.$route.query;
      query.isBackToList = true;
      if (_.isString(message)) {
        params.message = message;
        this.$router.push({
          name: ViewRouter,
          query: query,
          params: {id: self.idParams, req: params, message: 'Bản ghi đã được cập nhật!'}
        });
      } else {
        this.$router.push({
          name: ListRouter,
          query: query,
          params: params
        });
      }
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
.component-norm-allot .component-compare-time .form-group {
  margin-bottom: 0;
}
.mx-datepicker{
  width: 140px;
}
</style>
