<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Chỉ tiêu đánh giá công việc <span v-if="model.indicatorName">:</span> {{model.indicatorName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Chỉ tiêu đánh giá công việc <span v-if="model.indicatorName">:</span> {{model.indicatorName}}</span>
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
                          <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên</div>
                          <div class="col-lg-16 col-md-14 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
                            <input v-model="model.indicatorName" type="text" id="IndicatorName" class="form-control" placeholder="Tên chỉ tiêu" name="IndicatorName"/>
                          </div>
                          <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span>Mã số</span>
                            <input type="text" v-model="model.indicatorNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-md-4 m-0">Là chỉ tiêu con của</label>
                            <div class="col-md-20">
                              <Select2 v-model="model.parentID" @change="changePR" :options="model.indicatorOption" :settings="{allowClear: true, placeholder: {id: 0, text: 'Chọn chỉ tiêu cha'}}"></Select2>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-md-4 m-0" >Đơn vị tính</label>
                            <div class="col-md-8">
                              <b-form-select v-model="model.UomID" :options="model.uomOption" id="UomID"></b-form-select>
                            </div>
                            <label class="col-md-4 m-0">Loại tần suất</label>
                            <div class="col-md-8">
                              <b-form-select
                                v-model="model.frequencyType"
                                :options="[
                                        {value: null, text: 'Chọn tần suất'},
                                        {value: 1, text: 'Năm'},
                                        {value: 2, text: '6 tháng'},
                                        {value: 3, text: 'Quý'},
                                        {value: 4, text: 'Tháng'},
                                        {value: 5, text: 'Tuần'},
                                        {value: 6, text: 'Ngày'},
                                        {value: 7, text: 'Vụ việc'},]">
                              </b-form-select>
                            </div>
                        </div>

                        <div class="form-group row">
                          <label class="col-md-2" for="Description">Mô tả</label>
                          <div class="col-md-20">
                            <textarea v-model="model.description" class="form-control" id="Description" rows="3" placeholder="Ghi chú" name="Description"></textarea>
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
    import Select2 from 'v-select2-component';

    const ListRouter = 'task-indicator';
    const EditRouter = 'task-indicator-edit';
    const ViewRouter = 'task-indicator-view';
    const CreateRouter = 'task-indicator-create';
    const DetailApi = 'task/api/indicator/view';
    const EditApi = 'task/api/indicator/edit';
    const CreateApi = 'task/api/indicator/create';
    const StoreApi = 'task/api/indicator/store';
    const UpdateApi = 'task/api/indicator/update';
    const ListApi = 'task/api/indicator';

    export default {
        name: 'listing-detail-item',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                  indicatorID: null,
                  indicatorNo: '',
                  indicatorName: '',
                  parentID: null,
                  UomID: null,
                  uomName: '',
                  frequencyType: null,
                  inactive: false,
                  description: '',
                  Uom: null,
                  uomOption: []
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
          Select2
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
                ApiService.customRequest(requestData).then((responses) => { console.log(responses);
                    let responsesData = responses.data;
                    console.log(responsesData);
                    // copy item
                    if (!self.idParams && !_.isEmpty(self.itemCopy)) {
                        responsesData.data.data = self.itemCopy.data.data;
                    }

                    if (responsesData.status === 1) {

                        if (self.idParams || !_.isEmpty(self.itemCopy)) {
                            if (_.isObject(responsesData.data.data)) {
                                self.model.indicatorID = responsesData.data.data.IndicatorID;
                                self.model.parentID = responsesData.data.data.ParentID;
                                self.model.indicatorName = responsesData.data.data.IndicatorName;
                                self.model.indicatorNo = responsesData.data.data.IndicatorNo;
                                self.model.frequencyType = responsesData.data.data.FrequencyType;
                                self.model.UomID = responsesData.data.data.UomID;
                                self.model.uomName = responsesData.data.data.uomName;
                                self.model.description = responsesData.data.data.Description;
                                self.model.inactive = (responsesData.data.data.Inactive) ? true : false;
                            }

                            if (!_.isEmpty(self.itemCopy)) {
                                self.model.indicatorNo = responsesData.data.auto;
                            }
                        }else {
                            self.model.indicatorNo = responsesData.data.auto;
                        }

                        if (_.isArray(responsesData.data.indicator)) {
                            self.model.indicatorOption = [];
                            _.forEach(responsesData.data.indicator, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.IndicatorID;
                                tmpObj.text = value.IndicatorName;
                                self.model.indicatorOption.push(tmpObj);
                            });
                        }
                        if (_.isArray(responsesData.data.Uom)) {

                            self.model.uomOption = [
                              {value: null, text: 'Chọn đơn vị tính'}
                            ];
                            _.forEach(responsesData.data.Uom, function (value, key) {
                              let tmpObj = {};
                              tmpObj.value = value.UomID;
                              tmpObj.text = value.UomName;
                              self.model.uomOption.push(tmpObj);
                            });
                            console.log(self.model.uomOption);
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

                if (this.reqParams.search.indicatorNo !== '') {
                    requestData.data.IndicatorNo = this.reqParams.search.indicatorNo;
                }
                if (this.reqParams.search.indicatorName !== '') {
                    requestData.data.indicatorName = this.reqParams.search.indicatorName;
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
                            self.reqParams.idsArray.push(value.indicatorID);
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
                        IndicatorNo: this.model.indicatorNo,
                        IndicatorName: this.model.indicatorName,
                        ParentID: this.model.parentID,
                        FrequencyType: this.model.frequencyType,
                        Inactive: (this.model.inactive) ? 1 : 0,
                        UomID: this.model.UomID,
                        UomName: this.model.uomName,
                        Description: this.model.description
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
          changePR() {
            let self = this;
            let urlApi = this.api;
            let requestData = {
              method: 'post',
              url: '/listing/api/common/auto-childtable',
              data: {
                table: 'task_indicator',
                ParentID: this.model.parentID,
                TableNo: 'IndicatorNo',
                TableID: 'IndicatorID',
              },

            };

            this.$store.commit('isLoading', true);
            ApiService.customRequest(requestData).then((response) => {//console.log(response);
              let dataResponse = response.data;
              this.model.indicatorNo = dataResponse.data;
              self.$store.commit('isLoading', false);
            }, (error) => {
              self.$store.commit('isLoading', false);
              Swal.fire({
                title: 'Thông báo',
                text: 'Không kết nối được với máy chủ',
                confirmButtonText: 'Đóng'
              });
            });
          }

        },
        watch: {
            idParams() {
                this.fetchData();
            },

            'model.taxRate'(){

            },
            'model.parentID'(){
              // let self = this;
              // let urlApi = this.api;
              // let requestData = {
              //   method: 'post',
              //   url: '/listing/api/common/auto-childtable',
              //   data: {
              //     table: 'task_indicator',
              //     ParentID: this.model.parentID,
              //     TableNo: 'IndicatorNo',
              //     TableID: 'IndicatorID',
              //   },
              //
              // };
              //
              // this.$store.commit('isLoading', true);
              // ApiService.customRequest(requestData).then((response) => {//console.log(response);
              //   let dataResponse = response.data;
              //   this.model.indicatorNo = dataResponse.data;
              //   self.$store.commit('isLoading', false);
              // }, (error) => {
              //   self.$store.commit('isLoading', false);
              // });

            },
        }
    }
</script>
<!--<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>-->
<style lang="css">
  .select2-selection__rendered{line-height: 24px !important;}
</style>
