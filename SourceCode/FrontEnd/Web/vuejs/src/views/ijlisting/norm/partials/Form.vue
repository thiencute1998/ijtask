<template>
    <div class="main-entry norm-main-form">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Chỉ tiêu dự toán<span v-if="model.NormName">:</span> {{model.NormName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Chỉ tiêu dự toán<span v-if="model.NormName">:</span> {{model.NormName}}</span>
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
                          <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                            <input v-model="model.NormName" type="text" id="NormName" class="form-control" placeholder="Tên chỉ tiêu dự toán" name="NormName"/>
                          </div>
                          <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span>Mã số</span>
                            <input type="text" v-model="model.NormNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>
                      <div class="form-group row align-items-center">
                        <label class="col-md-3 m-0">Là mục con của</label>
                        <div class="col-md-15">
                          <IjcoreModalParent v-model="model"
                                             :title="'Chỉ tiêu dự toán'"
                                             :api="'/listing/api/common/get-parent'"
                                             :table="'norm'" :fieldID="'NormID'"
                                             :fieldNo="'NormNo'"
                                             :fieldName="'NormName'"
                                             :placeholderInput="'Chọn định mức cơ sở cha'"
                                             :placeholderSearch="'Nhập tên định mức cơ sở'">
                          </IjcoreModalParent>
                        </div>
                        <div v-if="model.ParentID" class="col-lg-6 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                          <span>Mã số</span>
                          <input type="text" v-model="model.ParentNo" class="form-control" placeholder="Mã số"/>
                        </div>
                      </div>
                        <div class="form-group row align-items-center">
                            <label class="col-md-3 m-0" title="Loại chỉ tiêu dự toán">Loại ĐMDTCS</label>
                            <div class="col-md-13">
                                <norm-modal-search-input-catelist
                                  v-model="model.NormCate"
                                  title-modal="Loại chỉ tiêu dự toán"
                                  placeholder="Loại chỉ tiêu dự toán"
                                ></norm-modal-search-input-catelist>
                            </div>
                            <label class="col-md-3 m-0">Đơn vị tính</label>
                            <div class="col-md-5 mb-3 mb-sm-0">
                              <ijcore-modal-search-listing
                                v-model="model" :title="'Đơn vị tính'" :table="'uom'" :api="'/listing/api/common/list'"
                                :fieldID="'UomID'" :fieldNo="'UomNo'" :fieldName="'UomName'"
                                :fieldAssignID="'UomID'" :fieldAssignNo="'UomNo'" :fieldAssignName="'UomName'"
                              >
                              </ijcore-modal-search-listing>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                        <label class="col-md-3 m-0">Thu/Chi</label>
                        <div class="col-md-5">
                          <b-form-select v-model="model.NormType" :options="NormTypeOption"></b-form-select>
                        </div>
                        <label class="col-md-3 m-0" v-if="model.NormType == 1 || model.NormType == 3">Khoản thu</label>
                        <div class="col-md-5 mb-3 mb-sm-0" v-if="model.NormType == 1 || model.NormType == 3">
                          <ijcore-modal-search-listing
                            v-model="model" :title="'Khoản thu'" :table="'revenue'" :api="'/listing/api/common/list'"
                            :fieldID="'RevenueID'" :fieldNo="'RevenueNo'" :fieldName="'RevenueName'"
                            :fieldAssignID="'RevenueID'" :fieldAssignNo="'RevenueNo'" :fieldAssignName="'RevenueName'"
                          >
                          </ijcore-modal-search-listing>
                        </div>
                        <label class="col-md-3 m-0" v-if="model.NormType == 2 || model.NormType == 3">Khoản chi</label>
                        <div class="col-md-5 mb-3 mb-sm-0" v-if="model.NormType == 2 || model.NormType == 3">
                          <ijcore-modal-search-listing
                            v-model="model" :title="'Khoản chi'" :table="'expense'" :api="'/listing/api/common/list'"
                            :fieldID="'ExpenseID'" :fieldNo="'ExpenseNo'" :fieldName="'ExpenseName'"
                            :fieldAssignID="'ExpenseID'" :fieldAssignNo="'ExpenseNo'" :fieldAssignName="'ExpenseName'"
                          >
                          </ijcore-modal-search-listing>
                        </div>
                      </div>
                        <div class="form-group row">
                          <label class="col-md-3" for="Comment">Ghi chú</label>
                          <div class="col-md-21">
                            <textarea v-model="model.Comment" class="form-control" id="Comment" rows="3" placeholder="Ghi chú" name="Comment"></textarea>
                          </div>
                        </div>
<!--                        <norm-map-form v-model="model" @changed="onAddLine"></norm-map-form>-->
<!--                        <table class="table b-table table-sm table-bordered table-editable">-->
<!--                          <thead>-->
<!--                          <tr>-->
<!--                            <th scope="col" style="width: 10%" class="text-center">Bảng chỉ tiêu</th>-->
<!--                            <th scope="col" style="width: 10%" class="text-center">Tên chỉ tiêu</th>-->
<!--                            <th scope="col" style="width: 1%" class="text-center"></th>-->
<!--                          </tr>-->
<!--                          </thead>-->
<!--                          <tbody>-->
<!--                          <tr v-for="(field, key) in getNormMap">-->
<!--                            <td>-->
<!--                              <span :title="field.NormTableName">{{field.NormTableName}}</span>-->
<!--                            </td>-->
<!--                            <td>-->
<!--                              <span :title="field.NormTableItemName">{{field.NormTableItemName}}</span>-->
<!--                            </td>-->
<!--                            <td class="text-center">-->
<!--                              <i @click="onDeleteLine(key)" class="fa fa-trash-o" title="Xóa"-->
<!--                                 style="font-size: 18px; cursor: pointer"></i>-->
<!--                            </td>-->
<!--                          </tr>-->
<!--                          </tbody>-->
<!--                        </table>-->
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
    import NormModalSearchInputCatelist from "@/views/ijlisting/norm/partials/NormModalSearchInputCatelist";
    import NormMapForm from './NormMapForm';
    import IjcoreModalParent from "../../../../components/IjcoreModalParent";
    import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";

    const ListRouter = 'listing-norm';
    const EditRouter = 'listing-norm-edit';
    const ViewRouter = 'listing-norm-view';
    const CreateRouter = 'listing-norm-create';
    const ViewApi = 'listing/api/norm/view';
    const EditApi = 'listing/api/norm/edit';
    const CreateApi = 'listing/api/norm/create';
    const StoreApi = 'listing/api/norm/store';
    const UpdateApi = 'listing/api/norm/update';
    const ListApi = 'listing/api/norm';

    export default {
        name: 'listing-view-item',
        data () {
            return {
                NormTypeOption: {
                  '1': 'Thu',
                  '2': 'Chi',
                  '3': 'Thu & Chi'
                },
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    NormID: null,
                    NormNo: '',
                    NormName: '',
                    ParentID: null,
                    ParentNo: '',
                    ParentName: '',
                    NormType: 1,
                    RevenueID: null,
                    RevenueNo: '',
                    RevenueName: '',
                    ExpenseID: null,
                    ExpenseNo: '',
                    ExpenseName: '',
                    Comment: '',
                    Inactive: false,
                    UomID: null,
                    UomName: '',
                    UomNo: '',
                    NormCate: [],
                    NormCateList: [],
                    NormCateValue: [],

                    NormMap: [],
                    NormTableOption: [],

                },
                 stage: {
                    isNotification: false,
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
          IjcoreModalListing,
          Select2,
          NormModalSearchInputCatelist,
          IjcoreModalSearchInput,
          IjcoreModalParent,
          NormMapForm,
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
            getNormMap(){
              return _.sortBy(this.model.NormMap, ['NormTableID', 'NormTableName']);
            }
        },
        filters: {
          filterItem(value, revenueItem){
            if(revenueItem){
              // let listCateValues = listItems.map(cateValue=> cateValue.CateValue);
              // console.log(listCateValues);
              let RevenueItem = _.filter(value, item => item['RevenueTableItemID'] === Number(revenueItem));
              return RevenueItem;
            }
            return [];
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
                                self.model.NormID = responsesData.data.data.NormID;
                                self.model.NormName = responsesData.data.data.NormName;
                                self.model.NormNo = responsesData.data.data.NormNo;
                                self.model.NormType = responsesData.data.data.NormType;
                                self.model.Comment = responsesData.data.data.Comment;
                                self.model.UomID = responsesData.data.data.UomID;
                                self.model.UomNo = responsesData.data.data.UomNo;
                                self.model.UomName = responsesData.data.data.UomName;
                                self.model.RevenueID = responsesData.data.data.RevenueID;
                                self.model.RevenueNo = responsesData.data.data.RevenueNo;
                                self.model.RevenueName = responsesData.data.data.RevenueName;
                                self.model.ExpenseID = responsesData.data.data.ExpenseID;
                                self.model.ExpenseNo = responsesData.data.data.ExpenseNo;
                                self.model.ExpenseName = responsesData.data.data.ExpenseName;
                                self.model.ParentID = responsesData.data.data.ParentID;
                                self.model.ParentNo = responsesData.data.data.ParentNo;
                                self.model.ParentName = responsesData.data.data.ParentName;

                                // self.model.NormMap = responsesData.data.NormMap;
                              self.model.NormCate = [];
                              self.$set(self.model,'NormCate',[]);
                              if(responsesData.data.NormCate){
                                _.forEach(responsesData.data.NormCate, (normCate, key)=>{
                                  let tmpObj = {};
                                  if(normCate.CateID){
                                    let cateList = _.find(responsesData.data.NormCateList, ['CateID', normCate.CateID]);
                                    if(cateList){
                                      tmpObj.CateID = cateList.CateID;
                                      tmpObj.CateName = cateList.CateName;
                                    }
                                  }
                                  if(normCate.CateValue){
                                    // let cateValue = _.find(responsesData.data.NormCateValue, (cate)=> {
                                    //   return cate.CateID === normCate.CateID && cate.CateValue === normCate.CateValue;
                                    // });
                                    let cateValue = _.find(responsesData.data.NormCateValue,{
                                      CateID: normCate.CateID,
                                      CateValue: normCate.CateValue
                                    });
                                    if(cateValue){
                                      tmpObj.CateValue = cateValue.CateValue;
                                      tmpObj.Description = cateValue.Description;
                                    }
                                  }
                                  else{
                                    tmpObj.CateValue = null;
                                    tmpObj.Description = '';
                                  }
                                  // self.model.NormCate.push(tmpObj);
                                  self.$set(self.model.NormCate, self.model.NormCate.length, tmpObj);
                                })
                              }

                                self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
                            }
                        }

                      if (_.isArray(responsesData.data.NormTable)) {
                        self.model.NormTableOption = [{value: null, text: '--Chọn bảng chỉ tiêu--'}];
                        _.forEach(responsesData.data.NormTable, function (value, key) {
                          let tmpObj = {};
                          tmpObj.value = value.NormTableID;
                          tmpObj.text = value.NormTableName;
                          self.model.NormTableOption.push(tmpObj);
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

                if (this.reqParams.search.normNo !== '') {
                    requestData.data.NormNo = this.reqParams.search.normNo;
                }
                if (this.reqParams.search.normName !== '') {
                    requestData.data.NormName = this.reqParams.search.normName;
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
                            self.reqParams.idsArray.push(value.NormID);
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
                    url: StoreApi+ '?XDEBUG_SESSION_START=PHPSTORM',
                    data: {
                        NormNo: this.model.NormNo,
                        NormName: this.model.NormName,
                        NormType: this.model.NormType,
                        Inactive: (this.model.Inactive) ? 1 : 0,
                        UomID: this.model.UomID,
                        UomNo: this.model.UomNo,
                        ParentID: this.model.ParentID,
                        ParentNo: this.model.ParentNo,
                        UomName: this.model.UomName,
                        Comment: this.model.Comment,
                        NormCate: this.model.NormCate,
                        // NormMap: this.model.NormMap
                    }
                };

                if(this.model.NormType == 1 || this.model.NormType == 3){
                  requestData.data.RevenueID = this.model.RevenueID;
                  requestData.data.RevenueNo = this.model.RevenueNo;
                  requestData.data.RevenueName = this.model.RevenueName;
                }
                else if(this.model.NormType == 2 || this.model.NormType == 3){
                  requestData.data.ExpenseID = this.model.ExpenseID;
                  requestData.data.ExpenseNo = this.model.ExpenseNo;
                  requestData.data.ExpenseName = this.model.ExpenseName;
                }

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
                        NormID: responsesData.data,
                        NormNo: self.model.NormNo,
                        NormName: self.model.NormName,
                        ParentID: self.model.ParentID,
                        ParentNo: self.model.ParentNo,
                        NormType: self.model.NormType,
                        Inactive: (self.model.Inactive) ? 1 : 0,
                        UomID: self.model.UomID,
                        UomNo: self.model.UomNo,
                        UomName: self.model.UomName,
                        Comment: self.model.Comment,
                        NormCate: self.model.NormCate,
                        Level: null,
                        Detail: null,
                        Class: '',
                        hidden: false,
                      }
                      let indexold =  _.findIndex(self.$route.params.req.itemsArray, {'NormID': item.NormID});
                      let indexParent = null;
                      if(indexold >= 0){
                        // update

                        item.Detail = 1;
                        // set for new Parent
                        let ParentOldID =  self.$route.params.req.itemsArray[indexold].ParentID;
                        self.$route.params.req.itemsArray.splice(indexold, 1);
                        if(item.ParentID) {
                          indexParent = _.findIndex(self.$route.params.req.itemsArray, {'NormID': self.model.ParentID});
                          if(indexParent >= 0 ){
                            self.$set(self.$route.params.req.itemsArray[indexParent], 'Detail', 0);
                            self.$set(self.$route.params.req.itemsArray[indexParent], 'Class', 'fa fa-minus-square-o');
                            item.Level = self.$route.params.req.itemsArray[indexParent].Level + 1;
                            self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, indexParent + 1, item);
                          }
                        }else {
                          item.Level = 1;
                          self.$route.params.req.itemsArray.push(item);
                        }

                        // set for ParentOld
                        let indexParentOld = _.findIndex(self.$route.params.req.itemsArray, {'NormID':  ParentOldID});
                        if(indexParentOld >= 0){
                          let ParentOld = self.$route.params.req.itemsArray[indexParentOld];
                          let child = _.filter(self.$route.params.req.itemsArray, ['ParentID', ParentOld.NormID]);
                          if(child.length > 0){
                            self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Detail', 0);
                            self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Class', 'fa fa-minus-square-o');
                          } else {
                            self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Detail', 1);
                            self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Class', '');
                          }
                        }

                      } else {
                        // store
                        if(self.model.ParentID){
                          indexParent = _.findIndex(self.$route.params.req.itemsArray, {'NormID': self.model.ParentID});
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
                          _.orderBy(self.$route.params.req.itemsArray, ['NormID'], 'asc');
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

            updateModel() {
                if (this.stage.updatedData) {
                    this.$forceUpdate();
                }
            },

            autoCorrectedTaxRatePipe() {

            },

            onDeleteLine(key){
              this.model.NormMap.splice(key,1);
            },
            onAddLine(data){
              let self = this;
              data.map(function (val, key) {
                self.model.NormMap.push({
                  NormTableID: val.NormTableID,
                  NormTableName: val.NormTableName,
                  NormTableNo: val.NormTableNo,
                  NormTableItemID : val.NormTableItemID,
                  NormTableItemNo: val.NormTableItemNo,
                  NormTableItemName: val.NormTableItemName,
                  IsCheck: false
                });

              });
            }
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
  .norm-main-form .table tbody td{
    height: calc(1.5em + 0.55rem + 2px);
    font-size: 0.875rem;
    padding: 0.275rem 0.75rem;
  }
  .form-group{
    margin-bottom: 1rem;
  }
</style>
