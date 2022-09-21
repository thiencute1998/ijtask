<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i>Sửa tài khoản đồng thời </span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i>Thêm tài khoản đồng thời </span>
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
          <div class="card">
            <div class="form-group row" style="margin-top: 10px; margin-bottom: 0px;">
              <div class="col-md-8">
                <div class="form-group row align-items-center ml-2">
                  <label for="CoaTypeName" class="col-md-6 m-0">Loại HTTK</label>
                  <div class="col-md-18">
                    <ijcore-modal-search-listing id="CoaTypeName"
                                                 v-model="model" :title="'Loại hệ thống tài khoản'" :table="'coa_type'" :api="'/listing/api/common/list'"
                                                 :fieldID="'CoaTypeID'" :fieldNo="'CoaTypeNo'" :fieldName="'CoaTypeName'"
                                                 :fieldAssignID="'CoaTypeID'" :fieldAssignNo="'CoaTypeNo'" :fieldAssignName="'CoaTypeName'"
                    >
                    </ijcore-modal-search-listing>
                  </div>
                </div>
              </div>
              <div class="col-md-16" v-if="model.CoaTypeID !== null">
                <b-tabs card>
                  <b-tab title="Bút toán hạch toán" active>
                    <b-card-text>
                      <div class="form-group row align-items-center ">
                        <label for="EntryDebitAccount" class="col-md-3 m-0">TKHT Nợ</label>
                        <div class="col-md-9 ">
                          <IjcoreModalAccounting id="EntryDebitAccount"
                                                 v-model="model" :title="model.CoaTypeName" :api="'/listing/api/common/list'"
                                                 :table="model.SearchTable" :FieldID="'EntryDebitAccountID'" :FieldName="'EntryDebitAccountName'" :FieldNo="'EntryDebitAccountNo'" :FieldType="2" :columnID="model.SearchField + 'ID'" :columnNo="model.SearchField + 'No'" :columnName="model.SearchField + 'Name'">
                          </IjcoreModalAccounting>
                        </div>
                        <label for="EntryCreditAccount" class="col-md-3 m-0">TKHT Có</label>
                        <div class="col-md-9">
                          <IjcoreModalAccounting id="EntryCreditAccount"
                                                 v-model="model" :title="model.CoaTypeName" :api="'/listing/api/common/list'"
                                                 :table="model.SearchTable" :FieldID="'EntryCreditAccountID'" :FieldName="'EntryCreditAccountName'" :FieldNo="'EntryCreditAccountNo'" :FieldType="2" :columnID="model.SearchField + 'ID'" :columnNo="model.SearchField + 'No'" :columnName="model.SearchField + 'Name'">
                          </IjcoreModalAccounting>
                        </div>
                      </div>
                      <div class="form-group row align-items-center ">
                        <label for="EntryInTransType" class="col-md-3 m-0">Nghiệp vụ</label>
                        <div class="col-md-9">
                          <ijcore-modal-search-listing id="EntryInTransType"
                                                       v-model="model" :title="'Nghiệp vụ'" :table="'act_intranstype'" :api="'/listing/api/common/list'"
                                                       :fieldID="'InTransTypeID'" :fieldName="'InTransTypeName'"
                                                       :fieldAssignID="'EntryInTransTypeID'" :fieldAssignName="'EntryInTransTypeName'"
                          >
                          </ijcore-modal-search-listing>
                        </div>
                        <label for="ConditionalExpression" class="col-md-3 m-0">Điều kiện</label>
                        <div class="col-md-9">
                          <input v-model="model.ConditionalExpression" id="ConditionalExpression" type="text" class="form-control" placeholder="Điều kiện">
                        </div>
                      </div>
                    </b-card-text>
                  </b-tab>
                  <b-tab title="Bút toán đồng thời 1">
                    <b-card-text>
                      <div class="form-group row align-items-center ">
                        <label for="CCDebitAccount" class="col-md-3 m-0">TKĐT Nợ</label>
                        <div class="col-md-9">
                          <IjcoreModalAccounting id="CCDebitAccount"
                            v-model="model" :title="model.CoaTypeName" :api="'/listing/api/common/list'"
                            :table="model.SearchTable" :FieldID="'CCDebitAccountID'" :FieldName="'CCDebitAccountName'" :FieldNo="'CCDebitAccountNo'" :FieldType="2" :columnID="model.SearchField + 'ID'" :columnNo="model.SearchField + 'No'" :columnName="model.SearchField + 'Name'">
                          </IjcoreModalAccounting>
                        </div>
                        <label for="CCCreditAccount" class="col-md-3 m-0">TKĐT Có</label>
                        <div class="col-md-9">
                          <IjcoreModalAccounting id="CCCreditAccount"
                                                 v-model="model" :title="model.CoaTypeName" :api="'/listing/api/common/list'"
                                                 :table="model.SearchTable" :FieldID="'CCCreditAccountID'" :FieldName="'CCCreditAccountName'" :FieldNo="'CCCreditAccountNo'" :FieldType="2" :columnID="model.SearchField + 'ID'" :columnNo="model.SearchField + 'No'" :columnName="model.SearchField + 'Name'">
                          </IjcoreModalAccounting>
                        </div>
                      </div>
                      <div class="form-group row align-items-center ">
                        <label for="CCInTransType" class="col-md-3 m-0">Nghiệp vụ</label>
                        <div class="col-md-9">
                          <ijcore-modal-search-listing id="CCInTransType"
                                                       v-model="model" :title="'Nghiệp vụ'" :table="'act_intranstype'" :api="'/listing/api/common/list'"
                                                       :fieldID="'InTransTypeID'" :fieldName="'InTransTypeName'"
                                                       :fieldAssignID="'CCInTransTypeID'" :fieldAssignName="'CCInTransTypeName'"
                          >
                          </ijcore-modal-search-listing>
                        </div>
                        <label for="EnterNegativeValue" class="col-md-3 m-0">Giá tri âm</label>
                        <div class="col-md-9">
                          <input v-model="model.EnterNegativeValue" id="EnterNegativeValue" type="text" class="form-control" placeholder="Giá trị âm">
                        </div>
                      </div>
                      <div class="form-group row align-items-center">
                        <label for="Description" class="col-md-3 m-0">Diễn giải</label>
                        <div class="col-md-21">
                          <input v-model="model.Description" id="Description" type="text" class="form-control" placeholder="Diễn giải">
                        </div>
                      </div>
                    </b-card-text>
                  </b-tab>
                  <b-tab title="Bút toán đồng thời 2">
                    <b-card-text>
                      <div class="form-group row align-items-center ">
                        <label for="CCDebitAccount2" class="col-md-3 m-0">TKĐT Nợ</label>
                        <div class="col-md-9">
                          <IjcoreModalAccounting id="CCDebitAccount2"
                                                 v-model="model" :title="model.CoaTypeName" :api="'/listing/api/common/list'"
                                                 :table="model.SearchTable" :FieldID="'CCDebitAccountID2'" :FieldName="'CCDebitAccountName2'" :FieldNo="'CCDebitAccountNo2'" :FieldType="2" :columnID="model.SearchField + 'ID'" :columnNo="model.SearchField + 'No'" :columnName="model.SearchField + 'Name'">
                          </IjcoreModalAccounting>
                        </div>
                        <label for="CCCreditAccount2" class="col-md-3 m-0">TKĐT Nợ</label>
                        <div class="col-md-9">
                          <IjcoreModalAccounting id="CCCreditAccount2"
                                                 v-model="model" :title="model.CoaTypeName" :api="'/listing/api/common/list'"
                                                 :table="model.SearchTable" :FieldID="'CCCreditAccountID2'" :FieldName="'CCCreditAccountName2'" :FieldNo="'CCCreditAccountNo2'" :FieldType="2" :columnID="model.SearchField + 'ID'" :columnNo="model.SearchField + 'No'" :columnName="model.SearchField + 'Name'">
                          </IjcoreModalAccounting>
                        </div>
                      </div>
                      <div class="form-group row align-items-center ">
                        <label for="CCInTransType2" class="col-md-3 m-0">Nghiệp vụ</label>
                        <div class="col-md-9">
                          <ijcore-modal-search-listing id="CCInTransType2"
                                                       v-model="model" :title="'Nghiệp vụ'" :table="'act_intranstype'" :api="'/listing/api/common/list'"
                                                       :fieldID="'InTransTypeID'" :fieldName="'InTransTypeName'"
                                                       :fieldAssignID="'CCInTransTypeID2'" :fieldAssignName="'CCInTransTypeName2'"
                          >
                          </ijcore-modal-search-listing>
                        </div>
                        <label for="EnterNegativeValue2" class="col-md-3 m-0">Giá tri âm</label>
                        <div class="col-md-9">
                          <input v-model="model.EnterNegativeValue2" id="EnterNegativeValue2" type="text" class="form-control" placeholder="Giá trị âm">
                        </div>
                      </div>
                      <div class="form-group row align-items-center ">
                        <label for="Description2" class="col-md-3 m-0">Diễn giải</label>
                        <div class="col-md-21">
                          <input v-model="model.Description2" id="Description2" type="text" class="form-control" placeholder="Diễn giải">
                        </div>
                      </div>
                    </b-card-text>
                  </b-tab>
                  <b-tab title="Bút toán đồng thời 3">
                    <b-card-text>
                      <div class="form-group row align-items-center ">
                        <label for="CCDebitAccount3" class="col-md-3 m-0">TKĐT Nợ</label>
                        <div class="col-md-9">
                          <IjcoreModalAccounting id="CCDebitAccount3"
                                                 v-model="model" :title="model.CoaTypeName" :api="'/listing/api/common/list'"
                                                 :table="model.SearchTable" :FieldID="'CCDebitAccountID3'" :FieldName="'CCDebitAccountName3'" :FieldNo="'CCDebitAccountNo3'" :FieldType="2" :columnID="model.SearchField + 'ID'" :columnNo="model.SearchField + 'No'" :columnName="model.SearchField + 'Name'">
                          </IjcoreModalAccounting>
                        </div>
                        <label for="CCCreditAccount3" class="col-md-3 m-0">TKĐT Nợ</label>
                        <div class="col-md-9">
                          <IjcoreModalAccounting id="CCCreditAccount3"
                                                 v-model="model" :title="model.CoaTypeName" :api="'/listing/api/common/list'"
                                                 :table="model.SearchTable" :FieldID="'CCCreditAccountID3'" :FieldName="'CCCreditAccountName3'" :FieldNo="'CCCreditAccountNo3'" :FieldType="2" :columnID="model.SearchField + 'ID'" :columnNo="model.SearchField + 'No'" :columnName="model.SearchField + 'Name'">
                          </IjcoreModalAccounting>
                        </div>
                      </div>
                      <div class="form-group row align-items-center ">
                        <label for="CCInTransType3" class="col-md-3 m-0">Nghiệp vụ</label>
                        <div class="col-md-9">
                          <ijcore-modal-search-listing id="CCInTransType3"
                                                       v-model="model" :title="'Nghiệp vụ'" :table="'act_intranstype'" :api="'/listing/api/common/list'"
                                                       :fieldID="'InTransTypeID'" :fieldName="'InTransTypeName'"
                                                       :fieldAssignID="'CCInTransTypeID3'" :fieldAssignName="'CCInTransTypeName3'"
                          >
                          </ijcore-modal-search-listing>
                        </div>
                        <label for="EnterNegativeValue3" class="col-md-3 m-0">Giá tri âm</label>
                        <div class="col-md-9">
                          <input v-model="model.EnterNegativeValue3" id="EnterNegativeValue3" type="text" class="form-control" placeholder="Giá trị âm">
                        </div>
                      </div>
                      <div class="form-group row align-items-center ">
                        <label for="Description3" class="col-md-3">Diễn giải</label>
                        <div class="col-md-21">
                          <input v-model="model.Description3" id="Description3" type="text" class="form-control" placeholder="Diễn giải">
                        </div>
                      </div>
                    </b-card-text>
                  </b-tab>
                </b-tabs>
              </div>
            </div>
          </div>
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
import IjcoreModalParent from "../../../../components/IjcoreModalParent";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";
import IjcoreModalAccounting from "@/components/IjcoreModalAccounting";

const ListRouter = 'accounting-actccaccount';
const EditRouter = 'accounting-actccaccount-edit';
const ViewRouter = 'accounting-actccaccount-view';
const CreateRouter = 'accounting-actccaccount-create';
const ViewApi = 'accounting/api/actccaccount/view';
const EditApi = 'accounting/api/actccaccount/edit';
const CreateApi = 'accounting/api/actccaccount/create';
const StoreApi = 'accounting/api/actccaccount/store';
const UpdateApi = 'accounting/api/actccaccount/update';
const ListApi = 'accounting/api/actccaccount';

export default {
  name: 'accounting-view-item',
  data () {
    return {
      idParams: this.idParamsProps,
      reqParams: this.reqParamsProps,
      model: {
        CCAccountID: null,
        CoaTypeID: null,
        CoaTypeNo: '',
        CoaTypeName: '',
        EntryDebitAccountID: null,
        EntryDebitAccountNo: '',
        EntryCreditAccountID: null,
        EntryCreditAccountNo: '',
        EntryInTransTypeID: null,
        EntryInTransTypeName: '',
        ConditionalExpression: '',
        CCDebitAccountID: null,
        CCDebitAccountNo: '',
        CCCreditAccountID: null,
        CCCreditAccountNo: '',
        CCInTransTypeID: null,
        CCInTransTypeName: '',
        EnterNegativeValue: '',
        Description: '',
        CCDebitAccountID2: null,
        CCDebitAccountNo2: '',
        CCCreditAccountID2: null,
        CCCreditAccountNo2: '',
        CCInTransTypeID2: null,
        CCInTransTypeName2: '',
        EnterNegativeValue2: '',
        Description2: '',
        CCDebitAccountID3: null,
        CCDebitAccountNo3: '',
        CCCreditAccountID3: null,
        CCCreditAccountNo3: '',
        CCInTransTypeID3: null,
        CCInTransTypeName3: '',
        EnterNegativeValue3: '',
        Description3: '',
        Norder: null,
        Inactive: false,
        SearchTable: '',
        SearchField: '',
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
    IjcoreModalListing,
    Select2,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    IjcoreModalSearchListing,
    IjcoreModalAccounting,
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
          responsesData = self.itemCopy;
        }

        if (responsesData.status === 1) {

          if (self.idParams || !_.isEmpty(self.itemCopy)) {
            if (_.isObject(responsesData.data.data)) {
              self.model.CCAccountID = responsesData.data.data.CCAccountID;
              self.model.CoaTypeID = responsesData.data.data.CoaTypeID;
              self.model.CoaTypeNo = responsesData.data.data.CoaTypeNo;
              self.model.CoaTypeName = responsesData.data.data.CoaTypeName;
              self.model.EntryDebitAccountID = responsesData.data.data.EntryDebitAccountID;
              self.model.EntryDebitAccountNo = responsesData.data.data.EntryDebitAccountNo;
              self.model.EntryCreditAccountID = responsesData.data.data.EntryCreditAccountID;
              self.model.EntryCreditAccountNo = responsesData.data.data.EntryCreditAccountNo;
              self.model.EntryInTransTypeID = responsesData.data.data.EntryInTransTypeID;
              self.model.EntryInTransTypeName = responsesData.data.data.EntryInTransTypeName;
              self.model.ConditionalExpression = responsesData.data.data.ConditionalExpression;
              self.model.CCDebitAccountID = responsesData.data.data.CCDebitAccountID;
              self.model.CCDebitAccountNo = responsesData.data.data.CCDebitAccountNo;
              self.model.CCCreditAccountID = responsesData.data.data.CCCreditAccountID;
              self.model.CCCreditAccountNo = responsesData.data.data.CCCreditAccountNo;
              self.model.CCInTransTypeID = responsesData.data.data.CCInTransTypeID;
              self.model.CCInTransTypeName = responsesData.data.data.CCInTransTypeName;
              self.model.EnterNegativeValue = responsesData.data.data.EnterNegativeValue;
              self.model.Description = responsesData.data.data.Description;
              self.model.CCDebitAccountID2 = responsesData.data.data.CCDebitAccountID2;
              self.model.CCDebitAccountNo2 = responsesData.data.data.CCDebitAccountNo2;
              self.model.CCCreditAccountID2 = responsesData.data.data.CCCreditAccountID2;
              self.model.CCCreditAccountNo2 = responsesData.data.data.CCCreditAccountNo2;
              self.model.CCInTransTypeID2 = responsesData.data.data.CCInTransTypeID2;
              self.model.CCInTransTypeName2 = responsesData.data.data.CCInTransTypeName2;
              self.model.EnterNegativeValue2 = responsesData.data.data.EnterNegativeValue2;
              self.model.Description2 = responsesData.data.data.Description2;
              self.model.CCDebitAccountID3 = responsesData.data.data.CCDebitAccountID3;
              self.model.CCDebitAccountNo3 = responsesData.data.data.CCDebitAccountNo3;
              self.model.CCCreditAccountID3 = responsesData.data.data.CCCreditAccountID3;
              self.model.CCCreditAccountNo3 = responsesData.data.data.CCCreditAccountNo3;
              self.model.CCInTransTypeID3 = responsesData.data.data.CCInTransTypeID3;
              self.model.CCInTransTypeName3 = responsesData.data.data.CCInTransTypeName3;
              self.model.EnterNegativeValue3 = responsesData.data.data.EnterNegativeValue3;
              self.model.Description3 = responsesData.data.data.Description3;
              self.model.Norder = responsesData.data.data.Norder;
              self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
            }
            if (!_.isEmpty(self.itemCopy)) {

            }
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

      if (this.reqParams.search.CoaTypeName !== '') {
        requestData.data.CoaTypeName = this.reqParams.search.CoaTypeName;
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
            self.reqParams.idsArray.push(value.CCAccountID);
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
          CoaTypeID: this.model.CoaTypeID,
          CoaTypeNo: this.model.CoaTypeNo,
          CoaTypeName: this.model.CoaTypeName,
          EntryDebitAccountID: this.model.EntryDebitAccountID,
          EntryDebitAccountNo: this.model.EntryDebitAccountNo,
          EntryCreditAccountID: this.model.EntryCreditAccountID,
          EntryCreditAccountNo: this.model.EntryCreditAccountNo,
          EntryInTransTypeID: this.model.EntryInTransTypeID,
          EntryInTransTypeName: this.model.EntryInTransTypeName,
          ConditionalExpression: this.model.ConditionalExpression,
          CCDebitAccountID: this.model.CCDebitAccountID,
          CCDebitAccountNo: this.model.CCDebitAccountNo,
          CCCreditAccountID: this.model.CCCreditAccountID,
          CCCreditAccountNo: this.model.CCCreditAccountNo,
          CCInTransTypeID: this.model.CCInTransTypeID,
          CCInTransTypeName: this.model.CCInTransTypeName,
          EnterNegativeValue: this.model.EnterNegativeValue,
          Description: this.model.Description,
          CCDebitAccountID2: this.model.CCDebitAccountID2,
          CCDebitAccountNo2: this.model.CCDebitAccountNo2,
          CCCreditAccountID2: this.model.CCCreditAccountID2,
          CCCreditAccountNo2: this.model.CCCreditAccountNo2,
          CCInTransTypeID2: this.model.CCInTransTypeID2,
          CCInTransTypeName2: this.model.CCInTransTypeName2,
          EnterNegativeValue2: this.model.EnterNegativeValue2,
          Description2: this.model.Description2,
          CCDebitAccountID3: this.model.CCDebitAccountID3,
          CCDebitAccountNo3: this.model.CCDebitAccountNo3,
          CCCreditAccountID3: this.model.CCCreditAccountID3,
          CCCreditAccountNo3: this.model.CCCreditAccountNo3,
          CCInTransTypeID3: this.model.CCInTransTypeID3,
          CCInTransTypeName3: this.model.CCInTransTypeName3,
          EnterNegativeValue3: this.model.EnterNegativeValue3,
          Description3: this.model.Description3,
          Norder: this.model.Norder,
          Inactive: (this.model.Inactive) ? 1 : 0,
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
    changeUserContact() {
      let employee = _.find(this.model.EmployeeOption, ['id', Number(this.model.EmployeeID)]);
      if (employee) {
        this.model.ContactName = employee.text;
      }
    },
    selectCoaType(val){
      switch (Number(val)){
        case 1:
          this.model.SearchTable = 'coa_con';
          this.model.SearchField = 'Account';
          break;
        case 2:
          this.model.SearchTable = 'coa_tab';
          this.model.SearchField = 'Account'
          break;
        case 3:
          this.model.SearchTable = 'coa_sna';
          this.model.SearchField = 'Account'
          break;
        case 4:
          this.model.SearchTable = 'coa_anu';
          this.model.SearchField = 'Account'
          break;
        case 5:
          this.model.SearchTable = 'coa_pmu';
          this.model.SearchField = 'Account'
          break;
        case 6:
          this.model.SearchTable = 'coa_scb';
          this.model.SearchField = 'Account'
          break;
        case 7:
          this.model.SearchTable = 'coa_eas';
          this.model.SearchField = 'Account'
          break;
        case 8:
          this.model.SearchTable = 'coa_snr';
          this.model.SearchField = 'Account'
          break;
        case 9:
          this.model.SearchTable = 'coa_sia';
          this.model.SearchField = 'Account'
          break;
        case 10:
          this.model.SearchTable = 'coa_pcf';
          this.model.SearchField = 'Account';
          break;
        case 11:
          this.model.SearchTable = 'coa_ldi';
          this.model.SearchField = 'Account'
          break;
        default:
          this.model.SearchTable = '';
          this.model.SearchField = '';
          break;
      }
    }

  },
  watch: {
    idParams() {
      this.fetchData();
    },
    'model.CoaTypeNo'(newVal){
      this.selectCoaType(newVal);
    },
    'model.EntryDebitAccountID'(){
      this.model.CCDebitAccountID = this.model.EntryDebitAccountID;
      this.model.CCDebitAccountNo = this.model.EntryDebitAccountNo;
      this.model.CCDebitAccountID2 = this.model.EntryDebitAccountID;
      this.model.CCDebitAccountNo2 = this.model.EntryDebitAccountNo;
      this.model.CCDebitAccountID3 = this.model.EntryDebitAccountID;
      this.model.CCDebitAccountNo3 = this.model.EntryDebitAccountNo;
    },
    'model.EntryCreditAccountID'(){
      this.model.CCCreditAccountID = this.model.EntryCreditAccountID;
      this.model.CCCreditAccountNo = this.model.EntryCreditAccountNo;
      this.model.CCCreditAccountID2 = this.model.EntryCreditAccountID;
      this.model.CCCreditAccountNo2 = this.model.EntryCreditAccountNo;
      this.model.CCCreditAccountID3 = this.model.EntryCreditAccountID;
      this.model.CCCreditAccountNo3 = this.model.EntryCreditAccountNo;
    },
    'model.EntryInTransTypeID'(){
      this.model.CCInTransTypeID = this.model.EntryInTransTypeID;
      this.model.CCInTransTypeName = this.model.EntryInTransTypeName ;
      this.model.CCInTransTypeID2 = this.model.EntryInTransTypeID;
      this.model.CCInTransTypeName2 = this.model.EntryInTransTypeName ;
      this.model.CCInTransTypeID3 = this.model.EntryInTransTypeID;
      this.model.CCInTransTypeName3 = this.model.EntryInTransTypeName ;
    }
  }
}
</script>
<!--<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>-->
<style lang="css">
.tab-content{ border-bottom: none !important; border-right: none !important; border-left: none !important;}
.nav-tabs .nav-link{ border-top: none !important; }
.td-action-fix-right-form {
  position: absolute;
  width: 39px;
  max-width: 39px;
  right: 10px;
  top: auto;
  /*only relevant for first row*/
  background: #fff;
  border-bottom: none !important;
  /*compensate for top border*/
  height: 34px;
}
.td-action-fix-left-form {
  position: absolute;
  left: 0px;
  top: auto;
  /*only relevant for first row*/
  background: #fff;
  border-bottom: none !important;
}
.div-scroll-table {
  width: 100%;
  overflow-x: scroll;
  margin-right: 5em;
  overflow-y: visible;
  padding: 0;
}
.td-action-fix-right-form:last-child{
  border-bottom: 1px solid #c8ced3 !important;
  height: 34px;
}
.top-detail i { color: #00a2e8}
.top-detail span:hover{ cursor: pointer}
#bar_detail .form-control{ padding-right: 2px !important; padding-left: 9px !important; border:  none !important; }
.card-header{ padding-top: 5px !important; padding-bottom: 5px !important; background: none !important;}
.card-header .nav-tabs .nav-link{ color: #0b0e0f !important; padding: 0.55rem 0.625rem;}
.card-header .nav-tabs .nav-link:hover{ text-decoration: underline;}
.card-header .nav-tabs .nav-link.active{ font-weight: bold; text-decoration:underline; /*background: #f0f3f5;*/}
.tab-content{ border: none !important;}
.card-header .nav-tabs{ border: none !important;}
.nav-tabs .nav-link{ border: none !important;}
.table-bordered thead th, .table-bordered thead td {
  border-bottom-width: 1px !important;
}
.comments{ }
.mx-3{ margin-right: 0px !important;}
#bar_detail .new-row{ margin-top: 6px; font-weight: normal; font-size: 14px;}
.td-select2 {
  width: 99% !important;
  max-width: 650px;
  height: 30px;
  overflow-y: hidden;
}
.td-select2 .select2-container .select2-selection--multiple{
  width: 100% !important;
}
.select2-container{ width: 100% !important;}
#member-radio .form-check-inline{ display: block; margin-bottom: 5px;}
.custom-checkbox{ border: none !important;}

</style>
