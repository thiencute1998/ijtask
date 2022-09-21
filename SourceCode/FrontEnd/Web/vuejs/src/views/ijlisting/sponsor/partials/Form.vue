<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Nhà tài trợ<span v-if="model.SponsorName">:</span> {{model.SponsorName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Nhà tài trợ<span v-if="model.SponsorName">:</span> {{model.SponsorName}}</span>
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
                          <div class="col-lg-3 col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên</div>
                          <div class="col-lg-17 col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
                            <input v-model="model.SponsorName" type="text" id="SponsorName" class="form-control" placeholder="Tên nhà tài trợ" name="SponsorName"/>
                          </div>
                          <div class="col-lg-4 col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span>Mã số</span>
                            <input type="text" v-model="model.SponsorNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <div class="col-lg-3 col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Địa chỉ</div>
                          <div class="col-lg-21 col-md-21 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
                            <input v-model="model.SponsorAddress" type="text" id="SponsorAddress" class="form-control" placeholder="Địa chỉ" name="SponsorAddress"/>
                          </div>

                        </div>

                        <div class="form-group row align-items-center">
                            <div class="col-lg-3 col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2">Loại</div>
                            <div class="col-md-6 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
                              <b-form-select v-model="model.SponsorType" :options="model.optionsSponsorType"></b-form-select>
                            </div>
                          <label class="col-lg-2 col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2">Tỉnh</label>
                          <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0 ">
                            <ijcore-modal-search-listing
                              v-model="model" :title="'Tỉnh'" :table="'province'" :api="'/listing/api/common/list'"
                              :fieldID="'ProvinceID'" :fieldNo="'ProvinceNo'" :fieldName="'ProvinceName'"
                              :fieldAssignID="'ProvinceID'" :fieldAssignNo="'ProvinceNo'" :fieldAssignName="'ProvinceName'"
                            ></ijcore-modal-search-listing>
                          </div>
                          <label class="col-lg-3 col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2">Số điện thoại</label>
                          <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0 ">
                            <input type="number" v-model="model.OfficePhone" class="form-control" placeholder="Số điện thoại">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" for="Note">Ghi chú</label>
                          <div class="col-lg-21 col-md-21 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
                            <textarea v-model="model.Note" class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note"></textarea>
                          </div>
                        </div>

                      <label>Liên kết nhà tài trợ</label>
                      <table class="table b-table table-sm table-bordered table-editable">
                        <thead>
                        <tr>
                          <th scope="col" style="width: 5%" class="text-center">Mã số</th>
                          <th scope="col" style="width: 30%" class="text-center">Tên</th>
                          <th scope="col" style="width: 20%" class="text-center">LinkTable</th>
                          <th scope="col" style="width: 3%" class="text-center"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(field, key) in model.SponsorLink">
                          <td>
                            <b-form-input
                              type="text"
                              v-model="model.SponsorLink[key].LinkNo">
                            </b-form-input>
                          </td>
                          <td>
                            <b-form-input
                              type="text"
                              v-model="model.SponsorLink[key].LinkName">
                            </b-form-input>
                          </td>
                          <td>
                            <b-form-input
                              type="text"
                              v-model="model.SponsorLink[key].LinkTable">
                            </b-form-input>
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
    import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
    import IjcoreModalSearchListing from "../../../../components/IjcoreModalSearchListing";

    const ListRouter = 'listing-sponsor';
    const EditRouter = 'listing-sponsor-edit';
    const ViewRouter = 'listing-sponsor-view';
    const EditApi = 'listing/api/sponsor/edit';
    const CreateApi = 'listing/api/sponsor/create';
    const StoreApi = 'listing/api/sponsor/store';
    const UpdateApi = 'listing/api/sponsor/update';
    const ListApi = 'listing/api/sponsor';

    export default {
        name: 'listing-view-item',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    SponsorNo: '',
                    SponsorType: 1,
                    optionsSponsorType: [
                      { value: 1, text: 'Tổ chức trong nước' },
                      { value: 2, text: 'Tổ chức ngoài nước' },
                    ],
                    SponsorLink: [],
                    SponsorName: '',
                    SponsorAddress: '',
                    ProvinceID: null,
                    ProvinceNo: '',
                    ProvinceName: '',
                    Note: '',
                    OfficePhone: '',
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
          IjcoreModalSearchListing,
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
            onAddFieldOnTable() {
              let fieldObj = {};
              this.model.maxLineID += 1;
              fieldObj.LineID = this.model.maxLineID;
              fieldObj.LinkNo = '';
              fieldObj.LinkName = '';
              fieldObj.LinkTable = '';
              this.model.SponsorLink.push(fieldObj);
              this.$forceUpdate();
            },
          onDeleteFieldOnTable(field) {

            // remove field in fieldOnTableReq
            let fieldExist = _.find(this.model.SponsorLink, ['LineID', field.LineID]);
            if (_.isObject(fieldExist)) {
              _.remove(this.model.SponsorLink, ['LineID', field.LineID]);
            }
            this.$forceUpdate();
          },
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
                              self.model.SponsorNo = responsesData.data.data.SponsorNo;
                              self.model.SponsorType = responsesData.data.data.SponsorType;
                              self.model.SponsorName = responsesData.data.data.SponsorName;
                              self.model.SponsorAddress = responsesData.data.data.SponsorAddress;
                              self.model.ProvinceID = responsesData.data.data.ProvinceID;
                              self.model.ProvinceNo = responsesData.data.data.ProvinceID;
                              self.model.ProvinceName = responsesData.data.data.ProvinceName;
                              self.model.OfficePhone = responsesData.data.data.OfficePhone;
                              self.model.Note = responsesData.data.data.Note;
                              self.model.SponsorLink = responsesData.data.SponsorLink;

                            }

                            if (!_.isEmpty(self.itemCopy)) {
                                self.model.SponsorNo = responsesData.data.auto;
                            }
                        }else {
                            self.model.SponsorNo = responsesData.data.auto;
                        }


                        if (_.isArray(responsesData.data.company)) {

                            self.model.CompanyOption = [];
                            _.forEach(responsesData.data.company, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.CompanyID;
                                tmpObj.text = value.CompanyName;
                                self.model.CompanyOption.push(tmpObj);
                            });
                        }


                        if (_.isArray(responsesData.data.employee)) {

                            self.model.EmployeeOption = [];
                            _.forEach(responsesData.data.employee, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.EmployeeID;
                                tmpObj.text = value.EmployeeName;
                                self.model.EmployeeOption.push(tmpObj);
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
                  table: 'company',
                  ParentID: this.model.ParentID,
                },

              };

              ApiService.customRequest(requestData).then((response) => {
                let responseData = response.data;

                this.model.CompanyNo = responseData.data;
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

                if (this.reqParams.search.companyNo !== '') {
                    requestData.data.CompanyNo = this.reqParams.search.companyNo;
                }
                if (this.reqParams.search.companyName !== '') {
                    requestData.data.CompanyName = this.reqParams.search.companyName;
                }
                if (this.reqParams.search.tel !== '') {
                    requestData.data.Tel = this.reqParams.search.tel;
                }
                if (this.reqParams.search.fax !== '') {
                    requestData.data.Fax = this.reqParams.search.fax;
                }
                if (this.reqParams.search.email !== '') {
                    requestData.data.Email = this.reqParams.search.email;
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
                            self.reqParams.idsArray.push(value.CompanyID);
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
                    url: StoreApi + '',
                    data: {
                      master: {
                        SponsorNo: this.model.SponsorNo,
                        SponsorType: this.model.SponsorType,
                        SponsorName: this.model.SponsorName,
                        SponsorAddress: this.model.SponsorAddress,
                        Note: this.model.Note,
                        ProvinceID : this.model.ProvinceID,
                        ProvinceNo : this.model.ProvinceNo,
                        ProvinceName : this.model.ProvinceName,
                        OfficePhone: this.model.OfficePhone
                      },
                      detail: this.model.SponsorLink
                    }
                };

                // edit user
                if (this.idParams) {
                    requestData.data.ItemID = this.idParams;
                    requestData.url = UpdateApi + '/' + this.idParams + '';
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
                  table: 'company',
                  ParentID: this.model.ParentID,
                }
              }
              self.$store.commit('isLoading',true)
              ApiService.setHeader();
              ApiService.customRequest(requestData)
                .then(response=>{
                  let responseData = response.data;
                  if(responseData.status === 1){
                    self.model.CompanyNo = responseData.data;
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
