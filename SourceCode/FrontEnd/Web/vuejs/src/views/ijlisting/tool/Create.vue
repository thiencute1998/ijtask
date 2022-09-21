
<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Công cụ dụng cụ<span v-if="model.ToolName">:</span> {{model.ToolName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Công cụ dụng cụ<span v-if="model.ToolName">:</span> {{model.ToolName}}</span>
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
              <div class="col-lg-3 col-md-3 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tên</div>
              <div class="col-lg-17 col-md-21 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 app-object-name">
                <input v-model="model.ToolName" type="text" class="form-control" placeholder="Tên công cụ dụng cụ"/>
              </div>
              <div class="col-lg-4 app-object-code d-md-none d-sm-none d-none d-lg-flex align-items-center">
                <span>Mã số</span>
                <input type="text" v-model="model.ToolNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-3 col-sm-4 m-0 mb-lg-0 mb-md-3 mb-sm-3">Đơn vị tính</label>
              <div class="col-md-9 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
                <ijcore-modal-search-input
                  v-model="model.Uom"
                  :select-fields-api="[
                                {field: 'UomID',fieldForSelected: 'id', showInTable: false, key: 'UomID'},
                                {field: 'UomName', fieldForSelected: 'name', showInTable: true, label: 'Đơn vị tính', key: 'UomName', sortable: true, thClass: 'd-none'}
                              ]"
                  :search-fields-api="[{field: 'UomName', placeholder: 'Nhập tên đơn vị tính', name: 'UomName', class: '', style: ''}]"
                  table="uom"
                  ref="myModalSearchInputUom"
                  id-modal="myModalSearchInputUom"
                  :item-per-page="8"
                  placeholder="Đơn vị tính"
                  :url-api="$store.state.appRootApi + '/listing/api/tool/get-uom'"
                  name-input="input-uom"
                  title-modal="Đơn vị tính" size-modal="lg">
                </ijcore-modal-search-input>
              </div>

            </div>
            <div class="form-group row align-items-center">
              <div class="col-lg-3 col-md-3 col-sm-4 mb-lg-0 mb-md-3 mb-sm-3" title="Loại công cụ dụng cụ">Loại CCDC</div>
              <div class="col-lg-21 col-md-21 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
                <tool-modal-search-input-vcatelist
                  v-model="model.ToolCate"
                  tableApi="invest_asset_cate_list"
                  refModal="myModalSearchVcatelist"
                  id-modal="myModalSearchVcatelist"
                  placeholder="Loại công cụ dụng cụ"
                  title-modal="Loại công cụ dụng cụ" size-modal="lg"></tool-modal-search-input-vcatelist>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <div class="col-lg-3 col-md-3 col-sm-4" title="Quyền truy cập">Quyền truy cập</div>
              <div class="col-lg-9 col-md-9 col-sm-20">
                <b-form-select v-model="model.AccessType" :options="AccessTypeOptions"
                               id="item-uom"></b-form-select>
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
  import vSelect from 'vue-select';
  import IjcoreModalSearchInput from "../../../components/IjcoreModalSearchInput";
  import ToolModalSearchInputVcatelist from "./partials/ToolModalSearchInputVcatelist";

  const ListRouter = 'listing-tool';
  const EditRouter = 'listing-tool-edit';
  const CreateRouter = 'listing-tool-create';
  const ViewRouter = 'listing-tool-view';
  const ViewApi = 'listing/api/tool/view';
  const EditApi = 'listing/api/tool/edit';
  const CreateApi = 'listing/api/tool/create';
  const StoreApi = 'listing/api/tool/store';
  const UpdateApi = 'listing/api/tool/update';
  const ListApi = 'listing/api/tool';

  export default {
    name: 'listing-tool-view',
    data () {
      return {
        idParams: this.idParamsProps,
        reqParams: this.reqParamsProps,
        itemCopy: {},
        model: {
          ToolNo: '',
          ToolName: '',
          Uom: {},
          Locked: null,
          Inactive: null,
          ToolCate: [],
          AccessType: 1
        },
        AccessTypeOptions:{
          1: 'Chia sẻ',
          2: 'Công khai',
          3: 'Riêng tư'
        },
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
    },

    components: {
      vSelect,
      IjcoreModalSearchInput,
      ToolModalSearchInputVcatelist
    },
    beforeCreate() {},
    mounted() {
      this.itemCopy = (this.$route.params.itemCopy) ? this.$route.params.itemCopy : {};
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
            let auto = responsesData.data.auto;
            responsesData = self.itemCopy;
            responsesData.data.auto = auto;
          }
          if (responsesData.status === 1) {

            if (self.idParams || !_.isEmpty(self.itemCopy)) {
              if (_.isObject(responsesData.data)) {
                self.model.ToolNo = responsesData.data.data.ToolNo;
                self.model.ToolName = responsesData.data.data.ToolName;
                self.model.Address = responsesData.data.data.Address;
                self.model.BillTo = responsesData.data.data.BillTo;
                self.model.ShipTo = responsesData.data.data.ShipTo;
                self.model.TaxCode = responsesData.data.data.TaxCode;
                self.model.BankAccount = responsesData.data.data.BankAccount;
                self.model.BankName = responsesData.data.data.BankName;
                self.model.Address = responsesData.data.data.Address;
                self.model.OfficePhone = responsesData.data.data.OfficePhone;
                self.model.Fax = responsesData.data.data.Fax;
                self.model.Email = responsesData.data.data.Email;
                self.model.Website = responsesData.data.data.Website;
                self.model.Note = responsesData.data.data.Note;
                self.model.isCustomer = (responsesData.data.data.isCustomer) ? true : false;
                self.model.Province.ProvinceID = responsesData.data.data.ProvinceID;
                self.model.Province.ProvinceName = responsesData.data.data.ProvinceName;
                self.model.District.DistrictID = responsesData.data.data.DistrictID;
                self.model.District.DistrictName = responsesData.data.data.DistrictName;
                self.model.Commune.CommuneID = responsesData.data.data.CommuneID;
                self.model.Commune.CommuneName = responsesData.data.data.CommuneName;
                self.model.ToolCate = responsesData.data.data.ToolCate;
              }
              if (!_.isEmpty(self.itemCopy)) {
                self.model.ToolNo = responsesData.data.auto;
              }
            } else {
              self.model.ToolNo = responsesData.data.auto;
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

        if (this.reqParams.search.ToolNo !== '') {
          requestData.data.ToolNo = this.reqParams.search.ToolNo;
        }
        if (this.reqParams.search.ToolName !== '') {
          requestData.data.ToolName = this.reqParams.search.ToolName;
        }
        if (this.reqParams.search.OfficePhone !== '') {
          requestData.data.OfficePhone = this.reqParams.search.OfficePhone;
        }
        if (this.reqParams.search.Fax !== '') {
          requestData.data.Fax = this.reqParams.search.Fax;
        }
        if (this.reqParams.search.Email !== '') {
          requestData.data.Email = this.reqParams.search.Email;
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
              self.reqParams.idsArray.push(value.ToolID);
            });

            (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });

      },
      onChangedEmployeeCompany(){
        this.model.companyID = this.model.companyObj.value;
      },
      onChangedEmployeeManager(){
        this.model.managerID = this.model.managerObj.value;
      },
      onChangedEmployeeConcurrentManager(){
        this.model.concurrentManagerID = this.model.concurrentManagerObj.value;
      },
      onChangedEmployeeChecker(){
        this.model.checkerID = this.model.checkerObj.value;
      },
      onChangedEmployeeUser(){
        this.model.userID = this.model.userObj.value;
      },

      handleSubmitForm(){
        let self = this;
        const requestData = {
          method: 'post',
          url: StoreApi + '',
          data: {
            ToolNo: this.model.ToolNo,
            ToolName: this.model.ToolName,
            UomID: (this.model.Uom) ? this.model.Uom.UomID : null,
            UomName: (this.model.Uom) ? this.model.Uom.UomName : '',
            AccessType: this.model.AccessType
          }
        };

        if (this.model.ToolCate && this.model.ToolCate.length) {
          requestData.data.ToolCate = this.model.ToolCate;
        }

        // edit user
        if (this.idParams) {
          requestData.data.ToolID = this.idParams;
          requestData.url = UpdateApi + '/' + this.idParams;
        }

        if (!_.isEmpty(this.itemCopy)) {
          requestData.data.CopyID = this.itemCopy.data.data.ToolID;
        }

        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            let itemsArray = [];
            if (self.$route.params && self.$route.params.req && self.$route.params.req.itemsArray) {
              itemsArray = self.$route.params.req.itemsArray;
              itemsArray.unshift(responsesData.data);
              self.$route.params.req.itemsArray = itemsArray;
            }

            self.$router.push({
              name: ViewRouter,
              query: self.$route.query,
              params: {id: responsesData.data.ToolID, req: self.$route.params.req, message: 'Bản ghi đã được cập nhật!'}
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

      onBackToList() {
        let query = this.$route.query;
        query.isBackToList = true;
        let params = this.$route.params.req;
        this.$router.push({
          name: ListRouter,
          query: query,
          params: params
        });
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
