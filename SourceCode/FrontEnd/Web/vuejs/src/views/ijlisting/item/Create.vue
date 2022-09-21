
<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Vật tư - hàng hóa - dịch vụ<span v-if="model.ItemName">:</span> {{model.ItemName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Vật tư - hàng hóa - dịch vụ<span v-if="model.ItemName">:</span> {{model.ItemName}}</span>
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
                <input v-model="model.ItemName" type="text" class="form-control" placeholder="Tên vật tư - hàng hóa - dịch vụ"/>
              </div>
              <div class="col-lg-4 app-object-code d-md-none d-sm-none d-none d-lg-flex align-items-center">
                <span>Mã số</span>
                <input type="text" v-model="model.ItemNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Loại</label>
              <div class="col-md-5">
                <b-form-select v-model="model.ItemType" :options="ItemTypeOption"></b-form-select>
              </div>
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
                  :url-api="$store.state.appRootApi + '/listing/api/item/get-uom'"
                  name-input="input-uom"
                  title-modal="Đơn vị tính" size-modal="lg">
                </ijcore-modal-search-input>
              </div>

            </div>
            <div class="form-group row align-items-center">
              <div class="col-lg-3 col-md-3 col-sm-4 mb-lg-0 mb-md-3 mb-sm-3" title="Loại vật tư - hàng hóa - dịch vụ">Loại VT-HH-DV</div>
              <div class="col-lg-21 col-md-21 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
                <item-modal-search-input-vcatelist
                  v-model="model.ItemCate"
                  tableApi="item_cate_list"
                  refModal="myModalSearchVcatelist"
                  id-modal="myModalSearchVcatelist"
                  placeholder="Loại vật tư - hàng hóa - dịch vụ"
                  title-modal="Loại vật tư - hàng hóa - dịch vụ" size-modal="lg"></item-modal-search-input-vcatelist>
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
  import ItemModalSearchInputVcatelist from "./partials/ItemModalSearchInputVcatelist";

  const ListRouter = 'listing-item';
  const EditRouter = 'listing-item-edit';
  const CreateRouter = 'listing-item-create';
  const ViewRouter = 'listing-item-view';
  const ViewApi = 'listing/api/item/view';
  const EditApi = 'listing/api/item/edit';
  const CreateApi = 'listing/api/item/create';
  const StoreApi = 'listing/api/item/store';
  const UpdateApi = 'listing/api/item/update';
  const ListApi = 'listing/api/item';

  export default {
    name: 'listing-item-view',
    data () {
      return {
        ItemTypeOption: {
          '1': 'Hàng hóa',
          '2': 'Dịch vụ',
        },
        idParams: this.idParamsProps,
        reqParams: this.reqParamsProps,
        itemCopy: {},
        model: {
          ItemType: 1,
          ItemNo: '',
          ItemName: '',
          Uom: {},
          Locked: null,
          Inactive: null,
          ItemCate: [],
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
      ItemModalSearchInputVcatelist
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
                self.model.ItemNo = responsesData.data.data.ItemNo;
                self.model.ItemName = responsesData.data.data.ItemName;
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
                self.model.ItemCate = responsesData.data.data.ItemCate;
              }
              if (!_.isEmpty(self.itemCopy)) {
                self.model.ItemNo = responsesData.data.auto;
              }
            } else {
              self.model.ItemNo = responsesData.data.auto;
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

        if (this.reqParams.search.ItemNo !== '') {
          requestData.data.ItemNo = this.reqParams.search.ItemNo;
        }
        if (this.reqParams.search.ItemName !== '') {
          requestData.data.ItemName = this.reqParams.search.ItemName;
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
              self.reqParams.idsArray.push(value.ItemID);
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
        debugger
        const requestData = {
          method: 'post',
          url: StoreApi + '',
          data: {
            ItemType: this.model.ItemType,
            ItemNo: this.model.ItemNo,
            ItemName: this.model.ItemName,
            UomID: (this.model.Uom) ? this.model.Uom.UomID : null,
            UomName: (this.model.Uom) ? this.model.Uom.UomName : '',
            AccessType: this.model.AccessType,
            ItemCate: this.model.ItemCate          }
        };

        if (this.model.ItemCate && this.model.ItemCate.length) {
          requestData.data.ItemCate = this.model.ItemCate;
        }

        // edit user
        if (this.idParams) {
          requestData.data.ItemID = this.idParams;
          requestData.url = UpdateApi + '/' + this.idParams;
        }

        if (!_.isEmpty(this.itemCopy)) {
          requestData.data.CopyID = this.itemCopy.data.data.ItemID;
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
              params: {id: responsesData.data.ItemID, req: self.$route.params.req, message: 'Bản ghi đã được cập nhật!'}
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
