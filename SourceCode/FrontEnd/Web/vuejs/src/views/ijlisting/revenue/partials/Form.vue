<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Khoản thu<span v-if="model.RevenueName">:</span> {{model.RevenueName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Khoản thu<span v-if="model.RevenueName">:</span> {{model.RevenueName}}</span>
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
                            <input v-model="model.RevenueName" type="text" id="RevenueName" class="form-control" placeholder="Tên khoản thu" name="RevenueName"/>
                          </div>
                          <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span>Mã số</span>
                            <input type="text" v-model="model.RevenueNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-md-3 m-0">Là mục con của</label>
                            <div class="col-md-17">
                                <IjcoreModalSysTemParent
                                  v-model="model"
                                  :title="'Khoản thu'"
                                  :api="'/listing/api/common/list'"
                                  :table="'revenue'"
                                  :fieldName="'RevenueName'"
                                  :fieldNo="'RevenueNo'"
                                  :fieldID="'RevenueID'"
                                  :placeholderInput="'Chọn khoản thu cha'"
                                  :placeholderSearch="'Nhập khoản thu'"
                                  :columnID="'RevenueID'"
                                  :columnNo="'RevenueNo'"
                                  :columnName="'RevenueName'">
                                </IjcoreModalSysTemParent>
                            </div>
                            <div v-if="model.ParentID" class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                              <span>Mã số</span>
                              <input type="text" v-model="model.ParentNo" class="form-control" placeholder="Mã số"/>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-md-3 m-0">Loại khoản thu</label>
                            <div class="col-md-21">
                                <revenue-modal-search-input-catelist
                                  v-model="model.RevenueCate"
                                  :listApi="'listing/api/revenue/get-revenue-cate-list'"
                                  title-modal="Loại khoản thu"
                                  placeholder="Loại khoản thu"
                                ></revenue-modal-search-input-catelist>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-3 m-0">Đơn vị tính</label>
                          <div class="col-md-9 mb-3 mb-sm-0">
                            <ijcore-modal-search-listing
                              v-model="model" :title="'Đơn vị tính'" :table="'uom'" :api="'/listing/api/common/list'"
                              :fieldID="'UomID'" :fieldNo="'UomNo'" :fieldName="'UomName'"
                              :fieldAssignID="'UomID'" :fieldAssignNo="'UomNo'" :fieldAssignName="'UomName'"
                            >
                            </ijcore-modal-search-listing>
                          </div>

                          <label class="col-md-3 m-0">Mục - Tiểu mục</label>
                          <div class="col-md-9 mb-3 mb-sm-0">
                            <ijcore-modal-search-listing
                              v-model="model" :title="'Mục - Tiểu mục'" :table="'sbi_item'" :api="'/listing/api/common/list'"
                              :fieldID="'SbiItemID'" :fieldNo="'SbiItemNo'" :fieldName="'SbiItemName'"
                              :fieldAssignID="'SbiItemID'" :fieldAssignNo="'SbiItemNo'" :fieldAssignName="'SbiItemName'"
                            >
                            </ijcore-modal-search-listing>
                          </div>
                        </div>

                      <div class="form-group row align-items-center">
                        <label class="col-md-3 m-0">Cân đối ngân sách</label>
                        <div class="col-md-9">
                          <b-form-select v-model="model.BudgetBalanceType" :options="BudgetBalanceTypeOption"></b-form-select>
                        </div>
                        <label class="col-md-3 m-0">Loại NSNN</label>
                        <div class="col-md-9">
                          <b-form-select v-model="model.BudgetStateType " :options="BudgetStateTypeOption"></b-form-select>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-md-3 m-0">CTDT</label>
                        <div class="col-md-9 mb-3 mb-sm-0">
                          <ijcore-modal-search-listing
                            v-model="model" :title="'Chỉ tiêu dự toán'" :table="'norm'" :api="'/listing/api/common/list'"
                            :fieldID="'NormID'" :fieldNo="'NormNo'" :fieldName="'NormName'"
                            :fieldAssignID="'NormID'" :fieldAssignNo="'NormNo'" :fieldAssignName="'NormName'"
                          >
                          </ijcore-modal-search-listing>
                        </div>
                        <label class="col-md-3 m-0">Lĩnh vực thu</label>
                        <div class="col-md-9 mb-3 mb-sm-0">
                          <ijcore-modal-search-listing
                            v-model="model" :title="'Lĩnh vực thu'" :table="'sbr_sector'" :api="'/listing/api/common/list'"
                            :fieldID="'SbrSectorID'" :fieldNo="'SbrSectorNo'" :fieldName="'SbrSectorName'"
                            :fieldAssignID="'SbrSectorID'" :fieldAssignNo="'SbrSectorNo'" :fieldAssignName="'SbrSectorName'"
                          >
                          </ijcore-modal-search-listing>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-md-3" for="Note">Ghi chú</label>
                        <div class="col-md-21">
                          <textarea v-model="model.Note" class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note"></textarea>
                        </div>
                      </div>
                      <div class="form-group row ">
                        <div class="col-md-24">
                          <label>Tỷ lệ điều tiết thu ngân sách :</label>
                        </div>
                        <div class="col-md-24">
                          <table class="table b-table table-sm table-bordered" style="margin-bottom: 5px">
                            <thead>
                            <tr>
                              <th scope="col" style="width: 15%" class="text-center">Ngày hiệu lực</th>
                              <th scope="col" style="width: 15%" class="text-center">Ngày hết hiệu lực</th>
                              <th scope="col" style="max-width: 53%" class="text-center">Chỉ tiêu phân bổ</th>
                              <th scope="col" style="width: 5%" class="text-center">%</th>
                              <th scope="col" style="width: 5%" class="text-center" title="Ngừng hoạt động"><i class="fa fa-ban m-0"></i></th>
                              <th scope="col" style="max-width: 12px" class="text-center"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(field, key) in RevenueReguItem">
                              <td class="text-center">{{RevenueReguItem[key].EffectiveDate | convertServerDateToClientDate}}</td>
                              <td class="text-center" >{{RevenueReguItem[key].ExpirationDate | convertServerDateToClientDate}}</td>
                              <td class="text-left pl-3">{{ReguRateOptions[RevenueReguItem[key].BudgetLevel].text}}</td>
                              <td class="text-right">{{RevenueReguItem[key].ReguRate }}</td>
                              <td class="text-center"><i class="fa fa-check" v-if="RevenueReguItem[key].RevenueReguActive === 0"></i></td>
                              <td class="text-center">
                                <div class="d-flex align-center justify-content-center">
                                  <master-detail-form
                                    title="Tỷ lệ điều tiết thu ngân sách"
                                    :is-create="false"
                                    :revenue-regu-item="RevenueReguItem"
                                    :key-item="key"
                                    @edited="onEditFileOnTalbe"
                                  ></master-detail-form>
                                  <i @click="onDeleteFieldOnTable(key)" class="fa fa-trash-o ml-2" title="Xóa"
                                     style="font-size: 18px; cursor: pointer"></i>
                                </div>
                              </td>
                            </tr>
                            </tbody>
                          </table>
                          <master-detail-form title="Tỷ lệ điều tiết thu ngân sách"  :revenue-regu-item="RevenueReguItem" :is-create="true" @saved="onAddFieldOnTable"></master-detail-form>
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
    import RevenueModalSearchInputCatelist from "@/views/ijlisting/revenue/partials/RevenueModalSearchInputCatelist";
    import IjcoreModalSysTemParent from "../../../../components/IjcoreModalSystemParent";
    import RevenueModalSearchUom from "@/views/ijlisting/revenue/partials/RevenueModalSearchUom";
    import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";
    import IjcoreNumber from "@/components/IjcoreNumber";
    import MasterDetailForm from "../../revenue/partials/MasterDetailForm";
    import moment from "moment";

    const ListRouter = 'listing-revenue';
    const EditRouter = 'listing-revenue-edit';
    const ViewRouter = 'listing-revenue-view';
    const CreateRouter = 'listing-revenue-create';
    const ViewApi = 'listing/api/revenue/view';
    const EditApi = 'listing/api/revenue/edit';
    const CreateApi = 'listing/api/revenue/create';
    const StoreApi = 'listing/api/revenue/store';
    const UpdateApi = 'listing/api/revenue/update';
    const ListApi = 'listing/api/revenue';

    export default {
        name: 'listing-view-item',
        data () {
            return {
              BudgetBalanceTypeOption: {
                '1': 'Trong CĐNS',
                '2': 'Ngoài CĐNS',
              },
              BudgetStateTypeOption: {
                '1': 'Trong ngân sách',
                '2': 'Ngoài ngân sách',
              },
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    SbiItemID: null,
                    SbiItemNo: '',
                    SbiItemName: '',
                    RevenueID: null,
                    RevenueNo: '',
                    RevenueName: '',
                    ParentID: null,
                    ParentNo: '',
                    ParentName: null,
                    Note: '',
                    NOrder: '',
                    Inactive: false,
                    UomID: null,
                    UomName: '',
                    UomOption: [],
                    RevenueCate: [],
                    BudgetBalanceType: 1,
                    BudgetStateType: 1,
                    NormID: null,
                    NormNo: '',
                    NormName: '',
                    SbrSectorID: null,
                    SbrSectorNo: '',
                    SbrSectorName: '',
                },
                RevenueReguItem : [],
                ReguRateOptions : [
                  {value: 0, text : 'Để lại đơn vị'},
                  {value: 1, text : 'Ngân sách trung ương'},
                  {value: 2, text : 'Ngân sách tỉnh'},
                  {value: 3, text : 'Ngân sách huyện'},
                  {value: 4, text : 'Ngân sách xã'},
                ],
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
          MasterDetailForm,
          IjcoreModalListing,
          RevenueModalSearchUom,
          Select2,
          RevenueModalSearchInputCatelist,
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
                                self.model.RevenueID = responsesData.data.data.RevenueID;
                                self.model.ParentID = responsesData.data.data.ParentID;
                                self.model.RevenueName = responsesData.data.data.RevenueName;
                                self.model.RevenueNo = responsesData.data.data.RevenueNo;
                                self.model.Note = responsesData.data.data.Note;
                                self.model.UomID = responsesData.data.data.UomID;
                                self.model.UomName = responsesData.data.data.UomName;
                                self.model.SbrSectorID = responsesData.data.data.SbrSectorID;
                                self.model.SbrSectorNo = responsesData.data.data.SbrSectorNo;
                                self.model.SbrSectorName = responsesData.data.data.SbrSectorName;
                                self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
                            }

                            if (!_.isEmpty(self.itemCopy)) {
                                self.model.RevenueNo = '';
                            }
                        }else {
                            self.model.RevenueNo = '';
                        }


                        if (_.isArray(responsesData.data.revenue)) {

                            self.model.RevenueOption = [];
                            _.forEach(responsesData.data.revenue, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.RevenueID;
                                tmpObj.text = value.RevenueName;
                                self.model.RevenueOption.push(tmpObj);
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

                this.model.RevenueNo = responseData.data;
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

                if (this.reqParams.search.revenueNo !== '') {
                    requestData.data.RevenueNo = this.reqParams.search.revenueNo;
                }
                if (this.reqParams.search.revenueName !== '') {
                    requestData.data.RevenueName = this.reqParams.search.revenueName;
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
                            self.reqParams.idsArray.push(value.RevenueID);
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
            debugger
            const requestData = {
              method: 'post',
              url: StoreApi+'?XDEBUG_SESSION_START=PHPSTORM',
              data: {
                RevenueNo: this.model.RevenueNo,
                RevenueName: this.model.RevenueName,
                Inactive: (this.model.Inactive) ? 1 : 0,
                ParentID: this.model.ParentID,
                ParentNo: this.model.ParentNo,
                ParentName: this.model.ParentName,
                Note : this.model.Note,
                UomID: this.model.UomID,
                UomName: this.model.UomName,
                SbiItemNo: this.model.SbiItemNo,
                SbiItemName: this.model.SbiItemName,
                BudgetBalanceType: this.model.BudgetBalanceType,
                BudgetStateType: this.model.BudgetStateType,
                NormID: this.model.NormID,
                NormNo: this.model.NormNo,
                NormName: this.model.NormName,
                RevenueCate: this.model.RevenueCate,
                RevenueReguItem : this.RevenueReguItem,
                SbrSectorID: this.model.SbrSectorID,
                SbrSectorNo: this.model.SbrSectorNo,
                SbrSectorName: this.model.SbrSectorName,
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
                  RevenueID: responsesData.data,
                  RevenueNo: self.model.RevenueNo,
                  RevenueName: self.model.RevenueName,
                  ParentID: self.model.ParentID,
                  ParentNo: self.model.ParentNo,
                  UomID: self.model.UomID,
                  UomNo: self.model.UomNo,
                  SbiItemNo: self.model.SbiItemNo,
                  SbiItemName: self.model.SbiItemName,
                  RevenueCate: this.model.RevenueCate,
                  RevenueReguItem : this.RevenueReguItem,
                  Note: self.model.Note,
                  Detail : 1,
                  SbrSectorID: self.model.SbrSectorID,
                  SbrSectorNo: self.model.SbrSectorNo,
                  SbrSectorName: self.model.SbrSectorName,
                }
                let indexold =  _.findIndex(self.$route.params.req.itemsArray, {'RevenueID': item.RevenueID});
                let indexParent = null;
                if(indexold < 0){
                  if(self.model.ParentID){
                    indexParent = _.findIndex(self.$route.params.req.itemsArray, {'RevenueID': self.model.ParentID});
                    if(indexParent >= 0){
                      let ClassParentBeforUpdate = self.$route.params.req.itemsArray[indexParent].Class;
                      if(ClassParentBeforUpdate == 'fa fa-plus-square-o'){
                        self.$route.params.req.itemsArray[indexParent].Class = 'fa fa-minus-square-o';
                        self.getListChild(self.$route.params.req.itemsArray[indexParent].RevenueID);
                      } else {
                        self.$set(self.$route.params.req.itemsArray[indexParent], 'Detail', 0);
                        self.$set(self.$route.params.req.itemsArray[indexParent], 'Class', 'fa fa-minus-square-o');
                        item.Level = self.$route.params.req.itemsArray[indexParent].Level + 1;
                        self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, indexParent + 1, item);
                      };
                    }
                  } else {
                    item.Level = 1;
                    item.Detail = 1;
                    self.$route.params.req.itemsArray.push(item);
                    _.orderBy(self.$route.params.req.itemsArray, ['RevenueID'], 'asc');
                  }

                } ;
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

          onEditClicked(){
                this.$router.push({
                    name: EditRouter,
                    params: {id: this.idParams, req: this.reqParams}
                });
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
          sortFieldOnTable(){
            this.RevenueReguItem = _.orderBy(this.RevenueReguItem, ['EffectiveDate', 'BudgetLevel'], ['desc', 'asc']);
            this.$forceUpdate();
          },
          onDeleteFieldOnTable(key) {
            let self = this;
            Swal.fire({
              title: '',
              text: 'Bạn sẽ toàn bộ tỷ lệ điều tiết ngân sách trong kỳ!',
              type: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Xóa',
              cancelButtonText: 'Hủy bỏ'
            }).then((result) =>{
              if (result.value){
                let effectiveDate = self.RevenueReguItem[key].EffectiveDate;
                let arrDelete = _.filter(self.RevenueReguItem, ['EffectiveDate', self.RevenueReguItem[key].EffectiveDate]);
                _.forEach(arrDelete, function (v, k){
                  let index = _.findIndex(self.RevenueReguItem, ['EffectiveDate', v.EffectiveDate]);
                  if (index > -1) {
                    self.RevenueReguItem.splice(index, 1);
                  }
                })
                self.$bvToast.toast('Đã xóa xóa tỷ lệ điều tiết', {
                  title: 'Thông báo',
                  variant: 'success',
                  solid: true
                });
              }
            });

          },
          onAddFieldOnTable(data) {
            let self = this;
            _.forEach(data.BudgetLevelTable, function (value, key){
              if(value.ReguRate){
                let tmpObj = {};
                tmpObj.EffectiveDate = data.EffectiveDate;
                tmpObj.ExpirationDate = data.ExpirationDate;
                tmpObj.RevenueReguActive = data.RevenueReguActive;
                tmpObj.BudgetLevel = value.BudgetLevel;
                tmpObj.ReguRate = value.ReguRate;
                self.RevenueReguItem.push(tmpObj);
              }
            });
           this.sortFieldOnTable();
          },
          onEditFileOnTalbe(data){
            debugger
            let self =  this;
            let arrDelete = _.filter(self.RevenueReguItem, ['EffectiveDate', data.EffectiveDateOld]);
                      _.forEach(arrDelete, function (val, key){
              let index = _.findIndex(self.RevenueReguItem, ['EffectiveDate', val.EffectiveDate]);
              if(index > -1) {
                self.RevenueReguItem.splice(index ,1 );
              }
            });
            _.forEach(data.BudgetLevelTable, function (val, key){
              let tmpObj = {};
              if(val.ReguRate){
                tmpObj.EffectiveDate = data.EffectiveDate;
                tmpObj.ExpirationDate = data.ExpirationDate;
                tmpObj.BudgetLevel = val.BudgetLevel;
                tmpObj.ReguRate = val.ReguRate;
                if(data.RevenueReguActive){
                  tmpObj.RevenueReguActive = 1;
                } else tmpObj.RevenueReguActive = 0
                self.RevenueReguItem.push(tmpObj);
              }
            });
            this.sortFieldOnTable();
            this.$bvToast.toast('Đã sửa tỷ lệ điều tiết thu ngân sách ' , {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });
          },

          updateModel() {
                if (this.stage.updatedData) {
                    this.$forceUpdate();
                }
            },

          autoCorrectedTaxRatePipe() {

          },
          getListChild(RevenueID){
            let self = this;
            let requestData = {
              method: 'post',
              url: 'listing/api/revenue/get-list-child',
              data: {
                per_page: this.perPage,
                page: this.currentPage,
                ParentID: RevenueID,
              },
            };

            ApiService.customRequest(requestData).then((response) => {
              let responseData = response.data;
              if (responseData.status === 1) {
                let listChild = _.toArray(responseData.data);
                _.map(listChild, function(v, k){
                  if(v.Detail == 0){
                    v.Class = "fa fa-plus-square-o";
                  } else {
                    v.Class = "";
                  }
                  return v;
                });
                let keyParent = _.findIndex(self.$route.params.req.itemsArray, ['RevenueID', RevenueID]);
                _.forEach(listChild, function (val, key){
                  self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, keyParent + 1, val );
                });
                _.orderBy(self.$route.params.req.itemsArray,'RevenueNo','asc');
              };
              self.$store.commit('isLoading', false);
            }, (error) => {
              console.log(error);
              self.$store.commit('isLoading', false);
            });
            this.isBackToList = false;
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
