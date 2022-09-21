<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Nguồn vốn<span v-if="model.CapitalName">:</span> {{model.CapitalName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Nguồn vốn<span v-if="model.CapitalName">:</span> {{model.CapitalName}}</span>
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
                            <input v-model="model.CapitalName" type="text" id="CapitalName" class="form-control" placeholder="Tên nguồn vốn" name="CapitalName"/>
                          </div>
                          <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span>Mã số</span>
                            <input type="text" v-model="model.CapitalNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-md-3 m-0">Là mục con của</label>
                            <div class="col-md-17">
                                <IjcoreModalParent v-model="model" :title="'Nguồn vốn'" :api="'/listing/api/common/list'" :table="'capital'" :fieldName="'CapitalName'" :fieldNo="'CapitalNo'" :placeholderInput="'Chọn nguồn vốn cha'" :placeholderSearch="'Nhập tên nguồn vốn'">
                                </IjcoreModalParent>
                            </div>
                            <div v-if="model.ParentID" class="col-lg-4 col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
                              <span>Mã số</span>
                              <input type="text" v-model="model.ParentNo" class="form-control" placeholder="Mã số"/>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-md-3 m-0">Loại nguồn vốn</label>
                            <div class="col-md-21">
                                <capital-modal-search-input-catelist
                                  v-model="model.CapitalCate"
                                  title-modal="Loại nguồn vốn"
                                  placeholder="Loại nguồn vốn"
                                ></capital-modal-search-input-catelist>
                            </div>
                        </div>

                      <div class="form-group row align-items-center">
                        <label class="col-md-3 m-0">Thuộc</label>
                        <div class="col-md-9">
                          <b-form-select v-model="model.CapitalInOut" :options="CapitalInOutOption"></b-form-select>
                        </div>
                        <label class="col-md-3 m-0">Loại vốn NSNN</label>
                        <div class="col-md-9">
                          <b-form-select v-model="model.BudgetStateType " :options="BudgetStateTypeOption"></b-form-select>
                        </div>
                      </div>

                        <div class="form-group row">
                          <label class="col-md-3" for="Note">Ghi chú</label>
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
    import Select2 from 'v-select2-component'
    import IjcoreModalListing from "../../../../components/IjcoreModalListing";
    import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
    import CapitalModalSearchInputCatelist from "@/views/ijlisting/capital/partials/CapitalModalSearchInputCatelist";
    import IjcoreModalParent from "../../../../components/IjcoreModalParent";

    const ListRouter = 'listing-capital';
    const EditRouter = 'listing-capital-edit';
    const ViewRouter = 'listing-capital-view';
    const CreateRouter = 'listing-capital-create';
    const ViewApi = 'listing/api/capital/view';
    const EditApi = 'listing/api/capital/edit';
    const CreateApi = 'listing/api/capital/create';
    const StoreApi = 'listing/api/capital/store';
    const UpdateApi = 'listing/api/capital/update';
    const ListApi = 'listing/api/capital';

    export default {
        name: 'listing-view-item',
        data () {
            return {
              CapitalInOutOption: {
                '1': 'Vốn trong nước',
                '2': 'Vốn ngoài nước',
              },
              BudgetStateTypeOption: {
                '1': 'Trong ngân sách',
                '2': 'Ngoài ngân sách',
              },
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    CapitalID: null,
                    CapitalNo: '',
                    CapitalName: '',
                    ParentID: null,
                    ParentNo: '',
                    ParentName: null,
                    Note: '',
                    Detail: '',
                    NOrder: '',
                    Inactive: false,
                    CapitalCate: [],
                    CapitalInOut: 1,
                    BudgetStateType: 1

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
          CapitalModalSearchInputCatelist,
          IjcoreModalParent,
          IjcoreModalSearchInput
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
                                self.model.CapitalID = responsesData.data.data.CapitalID;
                                self.model.ParentID = responsesData.data.data.ParentID;
                                self.model.CapitalName = responsesData.data.data.CapitalName;
                                self.model.CapitalNo = responsesData.data.data.CapitalNo;
                                self.model.Note = responsesData.data.data.Note;
                                self.model.Detail = responsesData.data.data.Detail;
                                self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
                            }

                            if (!_.isEmpty(self.itemCopy)) {
                                self.model.CapitalNo = '';
                            }
                        }else {
                            self.model.CapitalNo = '';
                        }


                        if (_.isArray(responsesData.data.capital)) {

                            self.model.CapitalOption = [];
                            _.forEach(responsesData.data.capital, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.CapitalID;
                                tmpObj.text = value.CapitalName;
                                self.model.CapitalOption.push(tmpObj);
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

                this.model.CapitalNo = responseData.data;
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

                if (this.reqParams.search.capitalNo !== '') {
                    requestData.data.CapitalNo = this.reqParams.search.capitalNo;
                }
                if (this.reqParams.search.capitalName !== '') {
                    requestData.data.CapitalName = this.reqParams.search.capitalName;
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
                            self.reqParams.idsArray.push(value.CapitalID);
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
                CapitalNo: this.model.CapitalNo,
                CapitalName: this.model.CapitalName,
                Inactive: (this.model.Inactive) ? 1 : 0,
                Note: this.model.Note,
                Detail: this.model.Detail,
                ParentID: this.model.ParentID,
                ParentNo: this.model.ParentNo,
                ParentName: this.model.ParentName,
                CapitalCate: this.model.CapitalCate,
                CapitalInOut: this.model.CapitalInOut,
                BudgetStateType: this.model.BudgetStateType
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
                  CapitalID: responsesData.data,
                  CapitalNo: self.model.CapitalNo,
                  CapitalName: self.model.CapitalName,
                  Inactive: (self.model.Inactive) ? 1 : 0,
                  Note: self.model.Note,
                  Detail: self.model.Detail,
                  ParentID: self.model.ParentID,
                  ParentNo: self.model.ParentNo,
                  ParentName: self.model.ParentName,
                  CapitalCate: self.model.CapitalCate
                }
                let indexold =  _.findIndex(self.$route.params.req.itemsArray, {'CapitalID': item.CapitalID});
                let indexParent = null;
                if(indexold  < 0){
                  if(self.model.ParentID){
                    indexParent = _.findIndex(self.$route.params.req.itemsArray, {'CapitalID': self.model.ParentID});
                    if(indexParent >= 0){
                      let ClassParentBeforUpdate = self.$route.params.req.itemsArray[indexParent].Class;
                      if(ClassParentBeforUpdate == 'fa fa-plus-square-o'){
                        self.$route.params.req.itemsArray[indexParent].Class = 'fa fa-minus-square-o';
                        self.getListChild(self.$route.params.req.itemsArray[indexParent].CapitalID);
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
                    _.orderBy(self.$route.params.req.itemsArray, ['CapitalID'], 'asc');
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

          getListChild(CapitalID){
            let self = this;
            let requestData = {
              method: 'post',
              url: 'listing/api/capital/get-list-child',
              data: {
                per_page: this.perPage,
                page: this.currentPage,
                ParentID: CapitalID,
              },
            };

            ApiService.customRequest(requestData).then((response) => {
              let responseData = response.data;
              if (responseData.status === 1) {
                debugger
                let listChild = _.toArray(responseData.data);
                _.map(listChild, function(v, k){
                  if(v.Detail == 0){
                    v.Class = "fa fa-plus-square-o";
                  } else {
                    v.Class = "";
                  }
                  return v;
                });
                let keyParent = _.findIndex(self.$route.params.req.itemsArray, ['CapitalID', CapitalID]);
                _.forEach(listChild, function (val, key){
                  self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, keyParent + 1, val );
                });
                _.orderBy(self.$route.params.req.itemsArray,'CapitalNo','asc');
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
