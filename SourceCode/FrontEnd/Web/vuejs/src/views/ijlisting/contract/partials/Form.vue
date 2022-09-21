<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Hợp Đồng<span v-if="model.ContractName">:</span> {{model.ContractName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Hợp Đồng<span v-if="model.ContractName">:</span> {{model.ContractName}}</span>
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
                            <input v-model="model.ContractName" type="text" id="ContractName" class="form-control" placeholder="Tên hợp đồng" name="ContractName"/>
                          </div>
                          <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span>Số hợp đồng</span>
                            <input type="text" v-model="model.ContractNo" class="form-control" placeholder="Số hợp đồng"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-md-3 m-0">Loại hợp đồng</label>
                            <div class="col-md-9">
                                <contract-modal-search-input-catelist
                                  v-model="model.ContractCate"
                                  title-modal="Loại hợp đồng"
                                  placeholder="Loại hợp đồng"
                                ></contract-modal-search-input-catelist>
                            </div>
                            <label class="col-md-3 m-0">Quyền truy cập</label>
                            <div class="col-md-9">
                              <b-form-select v-model="model.AccessType" :options="AccessTypeOptions" id="item-uom">
                              </b-form-select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-3 m-0">Ngày ký</label>
                          <div class="col-md-5 mb-3 mb-sm-0">
                            <IjcoreDatePicker v-model="model.ContractDate"></IjcoreDatePicker>
                          </div>
                          <label class="col-md-3 m-0">Ngày hiệu lực</label>
                          <div class="col-md-5">
                            <IjcoreDatePicker v-model="model.EffectiveDate"></IjcoreDatePicker>
                          </div>
                          <label class="col-md-3 m-0">Ngày kết thúc</label>
                          <div class="col-md-5">
                            <IjcoreDatePicker v-model="model.FinishDate"></IjcoreDatePicker>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-3 m-0">Giá trị HĐ</label>
                          <div class="col-md-5">
                            <ijcore-number v-model="model.ContractAmount"></ijcore-number>
                          </div>
                          <label class="col-md-3 m-0">Dự án</label>
                          <div class="col-md-5">
                            <ijcore-modal-search-listing
                              v-model="model" :title="'Dự án'" :table="'project'" :api="'/listing/api/common/list'"
                              :fieldID="'ProjectID'" :fieldNo="'ProjectNo'" :fieldName="'ProjectName'"
                              :fieldAssignID="'ProjectID'" :fieldAssignNo="'ProjectNo'" :fieldAssignName="'ProjectName'"
                            >
                            </ijcore-modal-search-listing>
                          </div>
                          <label class="col-md-3 m-0">Người ký</label>
                          <div class="col-md-5 mb-3 mb-sm-0">
                            <ijcore-modal-search-listing
                              v-model="model" :title="'Người ký'" :table="'employee'" :api="'/listing/api/common/list'"
                              :fieldID="'EmployeeID'" :fieldNo="'EmployeeNo'" :fieldName="'EmployeeName'"
                              :fieldAssignID="'EmployeeID'" :fieldAssignNo="'EmployeeNo'" :fieldAssignName="'EmployeeName'"
                            >
                            </ijcore-modal-search-listing>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-3 m-0">Nhà cung cấp</label>
                          <div class="col-md-5">
                            <ijcore-modal-search-listing
                              v-model="model" :title="'Nhà cung cấp'" :table="'vendor'" :api="'/listing/api/common/list'"
                              :fieldID="'VendorID'" :fieldNo="'VendorNo'" :fieldName="'VendorName'"
                              :fieldAssignID="'VendorID'" :fieldAssignNo="'VendorNo'" :fieldAssignName="'VendorName'"
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
    import IjcoreDatePicker from '@/components/IjcoreDatePicker';
    import IjcoreModalListing from "../../../../components/IjcoreModalListing";
    import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
    import ContractModalSearchInputCatelist from "@/views/ijlisting/contract/partials/ContractModalSearchInputCatelist";
    import IjcoreModalParent from "../../../../components/IjcoreModalParent";
    import IjcoreNumber from "@/components/IjcoreNumber";
    import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";

    const ListRouter = 'listing-contract';
    const EditRouter = 'listing-contract-edit';
    const ViewRouter = 'listing-contract-view';
    const CreateRouter = 'listing-contract-create';
    const ViewApi = 'listing/api/contract/view';
    const EditApi = 'listing/api/contract/edit';
    const CreateApi = 'listing/api/contract/create';
    const StoreApi = 'listing/api/contract/store';
    const UpdateApi = 'listing/api/contract/update';
    const ListApi = 'listing/api/contract';

    export default {
        name: 'listing-view-item',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    ContractID: null,
                    ContractNo: '',
                    ContractName: '',
                    ContractDate: null,
                    EffectiveDate: null,
                    FinishDate: null,
                    ContractAmount: 0,
                    Note: '',
                    Prefix: '',
                    Suffix: '',
                    Inactive: false,
                    EmployeeName: '',
                    EmployeeID: null,
                    ProjectID: null,
                    ProjectNo: '',
                    ProjectName: '',
                    VendorID: null,
                    VendorName: '',
                    ContractOption: [],
                    AccessType: 1,

                    ContractCate: [],

                },
                AccessTypeOptions:{
                  1: 'Chia sẻ',
                  2: 'Công khai',
                  3: 'Riêng tư'
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
          ContractModalSearchInputCatelist,
          IjcoreModalParent,
          IjcoreModalSearchInput,
          IjcoreDatePicker,
          IjcoreNumber,
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
                                self.model.ContractID = responsesData.data.data.ContractID;
                                self.model.ContractName = responsesData.data.data.ContractName;
                                self.model.ContractNo = responsesData.data.data.ContractNo;
                                self.model.ContractDate = (responsesData.data.data.ContractDate ? self.onFormatDate(responsesData.data.data.ContractDate) : null);
                                self.model.EffectiveDate = (responsesData.data.data.EffectiveDate ? self.onFormatDate(responsesData.data.data.EffectiveDate) : null);
                                self.model.FinishDate = (responsesData.data.data.FinishDate ? self.onFormatDate(responsesData.data.data.FinishDate) : null);
                                self.model.Note = responsesData.data.data.Note;
                                self.model.Prefix = responsesData.data.data.Prefix;
                                self.model.Suffix = responsesData.data.data.Suffix;
                                self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
                            }

                            if (!_.isEmpty(self.itemCopy)) {
                                self.model.ContractNo = responsesData.data.auto;
                            }
                        }else {
                            self.model.ContractNo = responsesData.data.auto;
                        }


                        if (_.isArray(responsesData.data.contract)) {

                            self.model.ContractOption = [];
                            _.forEach(responsesData.data.contract, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.ContractID;
                                tmpObj.text = value.ContractName;
                                self.model.ContractOption.push(tmpObj);
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
              let requestData = {
                method: 'post',
                url: '/listing/api/common/auto-child',
                data: {
                  per_page: 10,
                  page: this.currentPage,
                  table: 'contract',
                  ParentID: this.model.ParentID,
                },

              };

              ApiService.customRequest(requestData).then((response) => {
                let responseData = response.data;

                this.model.ContractNo = responseData.data;
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

                if (this.reqParams.search.contractNo !== '') {
                    requestData.data.ContractNo = this.reqParams.search.contractNo;
                }
                if (this.reqParams.search.contractName !== '') {
                    requestData.data.ContractName = this.reqParams.search.contractName;
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
                            self.reqParams.idsArray.push(value.ContractID);
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
                        ContractNo: this.model.ContractNo,
                        ContractName: this.model.ContractName,
                        ContractDate : this.model.ContractDate,
                        EffectiveDate : this.model.EffectiveDate,
                        FinishDate : this.model.FinishDate,
                        ContractAmount: this.model.ContractAmount,
                        EmployeeID: this.model.EmployeeID,
                        EmployeeName: this.model.EmployeeName,
                        ProjectID: this.model.ProjectID,
                        ProjectNo: this.model.ProjectNo,
                        ProjectName: this.model.ProjectName,
                        VendorID: this.model.VendorID,
                        VendorName: this.model.VendorName,
                        Inactive: (this.model.Inactive) ? 1 : 0,
                        Note: this.model.Note,
                        AccessType: this.model.AccessType,
                        ContractCate: this.model.ContractCate
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
            onFormatDate(formatDate){
              return formatDate.split('-').reverse().join().replaceAll('/');
            }

        },
        watch: {
          idParams() {
              this.fetchData();
          },
          'model.ParentID'(){
              let self = this;
              let urlApi = '/listing/api/common/auto-child';
              let requestData = {
                method: 'post',
                url: urlApi,
                data: {
                  per_page: 10,
                  page: this.currentPage,
                  table: 'contract',
                  ParentID: this.model.ParentID,
                }
              }
              self.$store.commit('isLoading',true)
              ApiService.setHeader();
              ApiService.customRequest(requestData)
                .then(response=>{
                  let responseData = response.data;
                  if(responseData.status === 1){
                    self.model.ContractNo = responseData.data;
                  }
                  self.$store.commit('isLoading',false)
                }).catch(error=> {
                self.$store.commit('isLoading',false)
              })
          }
        }
    }
</script>
<!--<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>-->
<style lang="css" scoped>
  .select2-container{
    width: 100% !important;
  }
  .mx-datepicker, .mx-input-wrapper{
    width: 100% !important;
  }
</style>
