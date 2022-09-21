<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Loại giao dịch hợp đồng bán hàng<span
                v-if="model.CateName">:</span> {{model.CateName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Loại giao dịch hợp đồng bán hàng<span v-if="model.CateName">:</span> {{model.CateName}}</span>
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
              <label class="col-md-4 m-0" >Tên loại GDHĐBH</label>
              <div class="col-md-20">
                <input v-model="model.CateName" type="text" id="CateName" class="form-control"
                       placeholder="Tên loại giao dịch hợp đồng bán hàng" name="CateName"/>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0">Là mục con của</label>
              <div class="col-md-20">
<!--                <Select2 v-model="model.ParentID" :options="model.customerCateListOption"></Select2>-->
                <IjcoreModalParentListing v-model="model" :title="'loại giá trị hợp đồng bán hàng'" :api="'/listing/api/common/list'"
                                    :table="'customer_contract_trans_cate_list'"
                                    :FieldID="'CateID'" :FieldName="'CateName'"
                                    :FieldIDParent="'ParentID'" :FieldNameParent="'ParentName'"
                                    :FieldUpdate="[{'CateID' : 'ParentID', 'CateName' : 'ParentName'}]"
                                    :FieldSelect="['CateID', 'CateName']">
                </IjcoreModalParentListing>
              </div>
            </div>

            <label>Giá trị loại giao dịch hợp đồng bán hàng:</label>
            <table class="table b-table table-sm table-bordered table-editable">
              <thead>
              <tr>
                <th scope="col" style="width: 30%" class="text-center">Tên</th>
                <th scope="col" style="width: 10%" class="text-center">Kiểu giá trị</th>
                <th scope="col" style="width: 10%" class="text-center">Giá trị</th>
                <th scope="col" style="width: 3%" class="text-center"></th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(field, key) in model.customerContractTransCateValue">
                <td>
                  <b-form-input
                    type="text"
                    v-model="model.customerContractTransCateValue[key].CateValue"
                    autocomplete="Customer cate list description">
                  </b-form-input>
                </td>
                <td>
                  <b-form-select :options="dataTypeOption" @change="onChangeDataType($event, field)"
                                 v-model="model.customerContractTransCateValue[key].DataType"></b-form-select>
                </td>
                <td>
                  <b-form-input
                    v-if="field.DataType == 1"
                    type="number"
                    v-model="model.customerContractTransCateValue[key].ConvertedValue"
                    autocomplete="Customer cate list description">
                  </b-form-input>
                  <b-form-input
                    v-if="field.DataType == 2"
                    type="text"
                    v-model="model.customerContractTransCateValue[key].ConvertedValue"
                    autocomplete="Customer cate list description">
                  </b-form-input>
                  <masked-input
                    v-if="field.DataType == 3"
                    type="text"
                    name="date"
                    v-model="model.customerContractTransCateValue[key].ConvertedValue + ''"
                    class="form-control"
                    :mask="[/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/]"
                    :guide="true"
                    placeholderChar="_"
                    :showMask="true"
                    :keepCharPositions="true"
                    :pipe="autoCorrectedDatePipe()">
                  </masked-input>
                  <masked-input
                    v-if="field.DataType == 4"
                    type="text"
                    name="date-time"
                    v-model="model.customerContractTransCateValue[key].ConvertedValue + ''"
                    class="form-control"
                    :mask="[/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/, ' ', /\d/, /\d/, ':', /\d/, /\d/]"
                    :guide="true"
                    placeholderChar="_"
                    :showMask="true"
                    :keepCharPositions="true"
                    :pipe="autoCorrectedDateTimePipe()">
                  </masked-input>


                  <b-form-select v-if="field.DataType == 5" v-model="model.customerContractTransCateValue[key].ConvertedValue"
                                 :options="[{value: 1, text: 'Có'},{value: 2, text: 'Không'}]"></b-form-select>
                  <b-form-select v-if="field.DataType == 6" v-model="model.customerContractTransCateValue[key].ConvertedValue"
                                 :options="[{value: 1, text: 'Đúng'},{value: 2, text: 'Sai'}]"></b-form-select>

                </td>
                <td class="text-center">
                  <i @click="onDeleteFieldOnTable(field)" class="fa fa-trash-o" title="Xóa"
                     style="font-size: 18px; cursor: pointer"></i>
                </td>
              </tr>
              </tbody>
            </table>
            <a @click="onAddFieldOnTable" class="new-row">
              <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
            </a>
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
  import MaskedInput from 'vue-text-mask';
  import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
  import Select2 from 'v-select2-component';
  import moment from 'moment';
  import IjcoreModalParentListing from "../../../../components/IjcoreModalParentListing";

  moment.locale('vi');


  const ListRouter = 'listing-ccontracttranscatelist';
  const EditRouter = 'listing-ccontracttranscatelist-edit';
  const CreateRouter = 'listing-ccontracttranscatelist-create';
  const ViewRouter = 'listing-ccontracttranscatelist-view';
  const ViewApi = 'listing/api/customer-contract-trans-cate-list/view';
  const EditApi = 'listing/api/customer-contract-trans-cate-list/edit';
  const CreateApi = 'listing/api/customer-contract-trans-cate-list/create';
  const StoreApi = 'listing/api/customer-contract-trans-cate-list/store';
  const UpdateApi = 'listing/api/customer-contract-trans-cate-list/update';
  const ListApi = 'listing/api/customer-contract-trans-cate-list';

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
    name: 'listing-ccontracttranscatelist-view',
    data() {
      return {
        idParams: this.idParamsProps,
        reqParams: this.reqParamsProps,
        model: {
          CateName: '',
          ParentName: '',
          isSystemSetting: '',
          customerContractTransCateValue: [],
          ParentID: null,
          customerCateListOption: [],
          maxValueID: 0,
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
      IjcoreModalParentListing,
      Select2,
      MaskedInput,
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
            responsesData = self.itemCopy;
          }
          if (responsesData.status === 1) {

            if (self.idParams || !_.isEmpty(self.itemCopy)) {
              if (_.isObject(responsesData.data.data)) {
                self.model.CateName = responsesData.data.data.CateName;
                self.model.ParentID = responsesData.data.data.ParentID;
                self.model.ParentName = responsesData.data.data.ParentName;
                self.model.isSystemSetting = responsesData.data.data.isSystemSetting;
                self.model.inactive = (responsesData.data.data.Inactive) ? true : false;

                self.model.customerContractTransCateValue = responsesData.data.customerContractTransCateValue;
                // set max ValueID
                _.forEach(self.model.customerContractTransCateValue, function (field, key) {
                  if (Number(field.ValueID) > self.model.maxValueID) self.model.maxValueID = Number(field.ValueID);
                  // set type of value
                  if (field.DataType == 1) self.model.customerContractTransCateValue[key].ConvertedValue = Number(self.model.customerContractTransCateValue[key].ConvertedValue);
                  if (field.DataType == 2) self.model.customerContractTransCateValue[key].ConvertedValue = String(self.model.customerContractTransCateValue[key].ConvertedValue);
                  if (field.DataType == 3) self.model.customerContractTransCateValue[key].ConvertedValue = moment(self.model.customerContractTransCateValue[key].ConvertedValue).format('DD/MM/YYYY');
                  if (field.DataType == 4) self.model.customerContractTransCateValue[key].ConvertedValue = moment(self.model.customerContractTransCateValue[key].ConvertedValue).format('DD/MM/YYYY hh:mm');
                });

              }
            } else {
              if (_.isArray(responsesData.data)) {

                // self.model.customerCateListOption = [];
                // _.forEach(responsesData.data, function (value, key) {
                //   let tmpObj = {};
                //   tmpObj.id = value.CateID;
                //   tmpObj.text = value.CateName;
                //   if (value.CateID == self.model.ParentID) self.model.ParentID = value.CateID;
                //   self.model.customerCateListOption.push(tmpObj);
                // });
              }
            }

            if (_.isArray(responsesData.data.customerContractTransCateList)) {

              self.model.customerCateListOption = [];
              _.forEach(responsesData.data.customerContractTransCateList, function (value, key) {
                let tmpObj = {};
                tmpObj.id = value.CateID;
                tmpObj.text = value.CateName;
                if (value.CateID == self.model.ParentID) self.model.ParentID = value.CateID;
                self.model.customerCateListOption.push(tmpObj);
              });
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
              self.reqParams.idsArray.push(value.CustomerCateID);
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
        if (value === 1) field.ConvertedValue = 0;
        if (value === 2) field.ConvertedValue = '';
        if (value === 3) field.ConvertedValue = moment().format('DD/MM/YYYY');
        if (value === 4) field.ConvertedValue = moment().format('DD/MM/YYYY hh:mm');
        if (value === 5 || value === 6) field.ConvertedValue = 1;
        this.$forceUpdate();
      },
      onAddFieldOnTable() {
        let fieldObj = {};
        this.model.maxValueID += 1;
        fieldObj.ValueID = this.model.maxValueID;
        fieldObj.DataType = 1;
        fieldObj.CateValue = null;
        fieldObj.ConvertedValue = '';
        fieldObj.NOrder = null;
        this.model.customerContractTransCateValue.push(fieldObj);
        this.$forceUpdate();
      },
      onDeleteFieldOnTable(field) {

        // remove field in fieldOnTableReq
        let fieldExist = _.find(this.model.customerContractTransCateValue, ['ValueID', field.ValueID]);
        if (_.isObject(fieldExist)) {
          _.remove(this.model.customerContractTransCateValue, ['ValueID', field.ValueID]);
        }
        this.$forceUpdate();
      },
      handleSubmitForm() {
        let self = this;
        const requestData = {
          method: 'post',
          url: StoreApi,
          data: {
            master: {
              CateName: this.model.CateName,
              ParentID: this.model.ParentID,
              ParentName: this.model.ParentName,
              isSystemSetting: this.model.isSystemSetting,
              Inactive: (this.model.inactive) ? 1 : 0,
            },
            detail: this.model.customerContractTransCateValue
          }
        };

        // edit user
        if (this.idParams) {
          requestData.data.master.CateID = this.idParams;
          requestData.url = UpdateApi + '/' + this.idParams;
        }

        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data; //console.log(responses.data);
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
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="css">
  .v-select .dropdown-menu {
    max-height: 170px !important;
  }

  .custom-align {
    flex: 0 0 12.3%;
  }
</style>
