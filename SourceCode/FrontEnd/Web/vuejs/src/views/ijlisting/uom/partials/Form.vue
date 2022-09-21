<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Đơn vị tính<span v-if="model.uomName">:</span> {{model.uomName}}</span>
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Đơn vị tính<span v-if="model.uomName">:</span> {{model.uomName}}</span>
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
                          <div class="col-lg-3 col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên</div>
                          <div class="col-lg-8 col-md-16 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2">
                            <input type="text" v-model="model.uomName" id="UomName" class="form-control" placeholder="Tên đơn vị tính" name="UomName"/>
                          </div>
                          <div class="col-lg-3 col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2 ml-lg-auto" style="white-space: nowrap">Viết tắt</div>
                          <div class="col-lg-3 col-md-16 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2">
                            <input type="text" v-model="model.UomShortName" id="UomShortName" class="form-control" placeholder="Viết tắt" name="UomShortName"/>
                          </div>
                          <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span>Mã số</span>
                            <input type="text" v-model="model.uomNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-3 m-0" >Loại ĐVT</label>
                          <div class="col-md-8">
                            <b-form-select
                              v-model="model.UomType"
                              :options="[
                                    {value: null, text: 'Chọn loại đơn vị tính'},
                                    {value: 1, text: 'Đơn vị đo kích thước'},
                                    {value: 2, text: 'Đơn vị đo trọng lượng'},
                                    {value: 3, text: 'Đơn vị đo thời gian'},
                                    {value: 4, text: 'Đơn vị đo tiền tệ'},
                                    {value: 5, text: 'Đơn vị số đếm'},
                                    {value: 6, text: 'Đơn vị điểm số'},
                                    {value: 7, text: 'Đơn vị phần trăm (%)'},
                                    {value: 8, text: 'Đơn vị nhị phân (1-0)'},
                                    {value: 9, text: 'Đơn vị đo sự việc'},
                                    {value: 99, text: 'Đơn vị đo khác'},]">
                            </b-form-select>
                          </div>
                          <span class="col-3"></span>
                          <label class="col-md-3 m-0" >Loại ĐVT thời gian</label>
                          <div class="col-md-3">
                            <b-form-select
                              v-model="model.UomTime"
                              :options="[
                                    {value: null, text: 'Chọn loại ĐVT thời gian'},
                                    {value: 1, text: 'năm'},
                                    {value: 2, text: 'quý'},
                                    {value: 3, text: 'tháng'},
                                    {value: 4, text: 'tuần'},
                                    {value: 5, text: 'ngày'},
                                    {value: 6, text: 'giờ'},
                                    {value: 7, text: 'phút'},
                                    {value: 8, text: 'giây'},]">
                            </b-form-select>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-3 m-0" >Loại ĐVT tiền tệ</label>
                          <div class="col-md-8">
                            <b-form-select
                              v-model="model.UomCurrency"
                              :options="[
                                      {value: null, text: 'Chọn loại ĐVT tiền tệ'},
                                      {value: 1, text: 'Tỷ'},
                                      {value: 2, text: 'Triệu'},
                                      {value: 3, text: 'Nghìn'},
                                      {value: 4, text: 'Đồng'},]">
                            </b-form-select>
                          </div>
                          <span class="col-3"></span>
                          <label class="col-md-3 m-0" >Loại ĐVT điểm số</label>
                          <div class="col-md-3">
                            <b-form-select
                              v-model="model.UomPoint"
                              :options="[
                                      {value: null, text: 'Chọn loại ĐVT điểm số'},
                                      {value: 1, text: 'Thang điểm 100'},
                                      {value: 2, text: 'Thang điểm 10'},
                                      {value: 3, text: 'Thang điểm 5'},
                                      {value: 4, text: 'Thang điểm 4'},
                                      {value: 5, text: 'Thang điểm 3'},]">
                            </b-form-select>
                          </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 m-0" for="Note">Ghi chú</label>
                            <div class="col-md-21">
                                <textarea v-model="model.note" class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note"></textarea>
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

    const ListRouter = 'listing-uom';
    const EditRouter = 'listing-uom-edit';
    const CreateRouter = 'listing-uom-create';
    const ViewRouter = 'listing-uom-view';
    const ViewApi = 'listing/api/uom/view';
    const EditApi = 'listing/api/uom/edit';
    const CreateApi = 'listing/api/uom/create';
    const StoreApi = 'listing/api/uom/store';
    const UpdateApi = 'listing/api/uom/update';
    const ListApi = 'listing/api/uom';

    String.prototype.capitalize = function () {
        return this.charAt(0).toUpperCase() + this.slice(1);
    };

    export default {
        name: 'listing-view-item',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    uomID: null,
                    uomNo: '',
                    uomName: '',
                    UomShortName: '',
                    UomType: '1',
                    UomTime: '6',
                    UomCurrency: '4',
                    UomPoint: '5',
                    note: null,
                    NOrder: null,
                    inactive: null,
                    numberValue: null,
                    Prefix: '',
                    Suffix: '',
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
            itemCopy: {
                type: Object,
                default: function () {
                    return {}
                }
            }
        },
        components: {
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
                        let auto = responsesData.data.auto;
                        responsesData = self.itemCopy;
                        responsesData.data.auto = auto;
                    }
                    if (responsesData.status === 1) {

                        if (this.idParams || !_.isEmpty(self.itemCopy)) {
                            if (_.isObject(responsesData.data)) {
                                self.model.uomID = responsesData.data.UomID;
                                self.model.uomNo = responsesData.data.UomNo;
                                self.model.uomName = responsesData.data.UomName;
                                self.model.UomShortName = responsesData.data.UomShortName;
                                self.model.UomType = responsesData.data.UomType;
                                self.model.UomTime = responsesData.data.UomTime;
                                self.model.UomCurrency = responsesData.data.UomCurrency;
                                self.model.UomPoint = responsesData.data.UomPoint;
                                self.model.note = responsesData.data.Note;
                                self.model.NOrder = responsesData.data.NOrder;
                                self.model.inactive = (responsesData.data.Inactive) ? true : false;
                                self.model.numberValue = responsesData.data.NumberValue;
                                self.model.suffix = responsesData.data.Suffix;
                                self.model.prefix = responsesData.data.Prefix;
                            }
                            if (!_.isEmpty(self.itemCopy)) {
                                self.model.uomNo = responsesData.data.auto;
                            }
                        } else {
                            self.model.uomNo = responsesData.data.auto;
                        }


                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                  Swal.fire({
                    title: 'Thông báo',
                    text: 'Không kết nối được với máy chủ',
                    confirmButtonText: 'Đóng'
                  });
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

                if (this.reqParams.search.uomNo !== '') {
                    requestData.data.UomNo = this.reqParams.search.uomNo;
                }
                if (this.reqParams.search.uomName !== '') {
                    requestData.data.UomName = this.reqParams.search.uomName;
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
                            self.reqParams.idsArray.push(value.UomID);
                        });

                        (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                  Swal.fire({
                    title: 'Thông báo',
                    text: 'Không kết nối được với máy chủ',
                    confirmButtonText: 'Đóng'
                  });
                });

            },

            handleSubmitForm(){
              let self = this;
              const requestData = {
                  method: 'post',
                  url: StoreApi,
                  data: {
                    UomNo: this.model.uomNo,
                    UomName: this.model.uomName,
                    UomShortName: this.model.UomShortName,
                    UomType: this.model.UomType,
                    UomTime: this.model.UomTime,
                    UomCurrency: this.model.UomCurrency,
                    UomPoint: this.model.UomPoint,
                    Note: this.model.note,
                    Inactive: (this.model.inactive) ? 1 : 0
                  }
              };

              // edit user
              if (this.idParams) {
                  requestData.data.UomID = this.idParams;
                  requestData.url = UpdateApi + '/' + this.idParams;
              }

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

              }, (error) => {
                  console.log(error);
                  Swal.fire(
                      'Thông báo',
                      'Không kết nối được với máy chủ',
                      'error'
                  )
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

            }

        },
        watch: {
            idParams() {
                this.fetchData();
            },

            'model.taxRate'(){

            }
        }
    }
</script>

<style lang="css"></style>
