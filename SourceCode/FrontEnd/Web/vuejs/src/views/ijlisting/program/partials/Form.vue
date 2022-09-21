<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Chương Trình Mục Tiêu<span v-if="model.ProgramName">:</span> {{model.ProgramName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Chương Trình Mục Tiêu<span v-if="model.ProgramName">:</span> {{model.ProgramName}}</span>
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
                            <input v-model="model.ProgramName" type="text" id="ProgramName" class="form-control" placeholder="Tên chương trình mục tiêu" name="ProgramName"/>
                          </div>
                          <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span>Mã số</span>
                            <input type="text" v-model="model.ProgramNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-md-3 m-0">Loại CTMT</label>
                            <div class="col-md-21">
                                <program-modal-search-input-catelist
                                  v-model="model.ProgramCate"
                                  title-modal="Loại chương trình mục tiêu"
                                  placeholder="Loại chương trình mục tiêu"
                                ></program-modal-search-input-catelist>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-3 m-0">Loại</label>
                          <div class="col-md-5 mb-3 mb-sm-0">
                            <b-form-select v-model="model.ProgramType" :options="ProgramTypeOption" id="item-uom">
                            </b-form-select>
                          </div>
                          <label class="col-md-3 m-0">Cấp quản lý</label>
                          <div class="col-md-5">
                            <b-form-select v-model="model.ManagementLevel" :options="ManagementLevelOption" id="item-uom">
                            </b-form-select>
                          </div>
                          <label class="col-md-3 m-0">Quyền truy cập</label>
                          <div class="col-md-5">
                            <b-form-select v-model="model.AccessType" :options="AccessTypeOptions" id="item-uom">
                            </b-form-select>
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
    import ProgramModalSearchInputCatelist from "@/views/ijlisting/program/partials/ProgramModalSearchInputCatelist";
    import IjcoreModalParent from "../../../../components/IjcoreModalParent";

    const ListRouter = 'listing-program';
    const EditRouter = 'listing-program-edit';
    const ViewRouter = 'listing-program-view';
    const CreateRouter = 'listing-program-create';
    const ViewApi = 'listing/api/program/view';
    const EditApi = 'listing/api/program/edit';
    const CreateApi = 'listing/api/program/create';
    const StoreApi = 'listing/api/program/store';
    const UpdateApi = 'listing/api/program/update';
    const ListApi = 'listing/api/program';

    export default {
        name: 'listing-view-item',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                ProgramTypeOption: {
                  1: 'CTMT Quốc gia',
                  2: 'CTMT Bổ sung'
                },
                ManagementLevelOption: {
                  1:  'Trung ương',
                  2:  'Tỉnh',
                  3:  'Huyện',
                  4:  'Xã',
                },
                model: {
                    ProgramID: null,
                    ProgramNo: '',
                    ProgramName: '',
                    ProgramType: 1,
                    ManagementLevel: 1,
                    Note: '',
                    Prefix: '',
                    Suffix: '',
                    Inactive: false,
                    EmployeeName: '',
                    EmployeeID: null,
                    ProgramOption: [],
                    AccessType: 1,

                    ProgramCate: [],

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
          ProgramModalSearchInputCatelist,
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
                        responsesData.data.data = self.itemCopy.data.data;
                    }

                    if (responsesData.status === 1) {

                        if (self.idParams || !_.isEmpty(self.itemCopy)) {
                            if (_.isObject(responsesData.data.data)) {
                                self.model.ProgramID = responsesData.data.data.ProgramID;
                                self.model.ProgramName = responsesData.data.data.ProgramName;
                                self.model.ProgramNo = responsesData.data.data.ProgramNo;
                                self.model.ProgramType = responsesData.data.data.ProgramType;
                                self.model.ManagementLevel = responsesData.data.data.ManagementLevel;
                                self.model.Note = responsesData.data.data.Note;
                                self.model.Prefix = responsesData.data.data.Prefix;
                                self.model.Suffix = responsesData.data.data.Suffix;
                                self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
                            }

                            if (!_.isEmpty(self.itemCopy)) {
                                self.model.ProgramNo = responsesData.data.auto;
                            }
                        }else {
                            self.model.ProgramNo = responsesData.data.auto;
                        }


                        if (_.isArray(responsesData.data.program)) {

                            self.model.ProgramOption = [];
                            _.forEach(responsesData.data.program, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.ProgramID;
                                tmpObj.text = value.ProgramName;
                                self.model.ProgramOption.push(tmpObj);
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
                  table: 'program',
                  ParentID: this.model.ParentID,
                },

              };

              ApiService.customRequest(requestData).then((response) => {
                let responseData = response.data;

                this.model.ProgramNo = responseData.data;
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

                if (this.reqParams.search.programNo !== '') {
                    requestData.data.ProgramNo = this.reqParams.search.programNo;
                }
                if (this.reqParams.search.programName !== '') {
                    requestData.data.ProgramName = this.reqParams.search.programName;
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
                            self.reqParams.idsArray.push(value.ProgramID);
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
                        ProgramNo: this.model.ProgramNo,
                        ProgramName: this.model.ProgramName,
                        ProgramType : this.model.ProgramType,
                        ManagementLevel : this.model.ManagementLevel,
                        Inactive: (this.model.Inactive) ? 1 : 0,
                        Note: this.model.Note,
                        AccessType: this.model.AccessType,
                        ProgramCate: this.model.ProgramCate
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
                  table: 'program',
                  ParentID: this.model.ParentID,
                }
              }
              self.$store.commit('isLoading',true)
              ApiService.setHeader();
              ApiService.customRequest(requestData)
                .then(response=>{
                  let responseData = response.data;
                  if(responseData.status === 1){
                    self.model.ProgramNo = responseData.data;
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
<style lang="css">
  .select2-container{
    width: 100% !important;
  }
</style>
