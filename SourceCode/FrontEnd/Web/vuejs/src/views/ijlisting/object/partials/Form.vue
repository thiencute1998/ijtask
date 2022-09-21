<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Đối tượng khác<span v-if="model.objectName">:</span> {{model.objectName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Đối tượng khác<span v-if="model.objectName">:</span> {{model.objectName}}</span>
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
                            <input type="text" v-model="model.objectName" id="ObjectName" class="form-control" placeholder="Tên đối tượng khác" name="ObjectName"/>
                          </div>
                          <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span>Mã số</span>
                            <input type="text" v-model="model.objectNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-md-4 m-0" for="Address">Địa chỉ</label>
                            <div class="col-md-20">
                                <input class="form-control" v-model="model.address" id="Address" rows="3" placeholder="Địa chỉ" name="Address"/>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-md-4 m-0" for="OfficePhone">ĐTĐV</label>
                            <div class="col-md-2">
                                <input type="number" v-model="model.officePhone" id="OfficePhone" class="form-control" placeholder="Số điện thoại" name="OfficePhone" />
                            </div>

                            <label class="col-md-2 m-0" for="Fax">Số Fax</label>
                            <div class="col-md-2">
                                <input v-model="model.fax" type="number" id="Fax" class="form-control" placeholder="Số Fax" name="Fax" />
                            </div>

                            <label class="col-md-2 m-0">Email</label>
                            <div class="col-md-8">
                                <input v-model="model.email" type="email" id="Email" class="form-control" placeholder="Email" name="Email" />
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-md-4 m-0" for="Website">Website</label>
                            <div class="col-md-8">
                                <input v-model="model.website" type="text" id="Website" class="form-control" placeholder="Website" name="Website" />
                            </div>
                        </div>

                        <div class="form-group row">
                          <label class="col-md-4 m-0" for="Note">Ghi chú</label>
                          <div class="col-md-20">
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
    import vSelect from 'vue-select';

    const ListRouter = 'listing-object';
    const EditRouter = 'listing-object-edit';
    const CreateRouter = 'listing-object-create';
    const ViewRouter = 'listing-object-view';
    const ViewApi = 'listing/api/object/view';
    const EditApi = 'listing/api/object/edit';
    const CreateApi = 'listing/api/object/create';
    const StoreApi = 'listing/api/object/store';
    const UpdateApi = 'listing/api/object/update';
    const ListApi = 'listing/api/object';

    export default {
        name: 'listing-vendor-view',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    objectNo: '',
                    objectName: '',
                    address: '',
                    officePhone: '',
                    fax: '',
                    email: '',
                    website: '',
                    note: '',
                    inactive: '',
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
            vSelect,
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
                        responsesData.data = self.itemCopy.data;
                        responsesData.data.auto = auto;
                    }


                    if (responsesData.status === 1) {

                        if (self.idParams || !_.isEmpty(self.itemCopy)) {
                            if (_.isObject(responsesData.data)) {
                                self.model.objectNo = responsesData.data.ObjectNo;
                                self.model.objectName = responsesData.data.ObjectName;
                                self.model.address = responsesData.data.Address;
                                self.model.officePhone = responsesData.data.OfficePhone;
                                self.model.fax = responsesData.data.Fax;
                                self.model.email = responsesData.data.Email;
                                self.model.website = responsesData.data.Website;
                                self.model.note = responsesData.data.Note;
                                self.model.inactive = (responsesData.data.Inactive) ? true : false;
                            }
                            if (!_.isEmpty(self.itemCopy)) {
                                self.model.objectNo = responsesData.data.auto;
                            }
                        } else {
                            self.model.objectNo = responsesData.data.auto;
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

                if (this.reqParams.search.objectNo !== '') {
                    requestData.data.ObjectNo = this.reqParams.search.objectNo;
                }
                if (this.reqParams.search.objectName !== '') {
                    requestData.data.ObjectName = this.reqParams.search.objectName;
                }
                if (this.reqParams.search.officePhone !== '') {
                    requestData.data.OfficePhone = this.reqParams.search.officePhone;
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
                            self.reqParams.idsArray.push(value.ObjectID);
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
                        ObjectNo: this.model.objectNo,
                        ObjectName: this.model.objectName,
                        Address: this.model.address,
                        OfficePhone: this.model.officePhone,
                        Fax: this.model.fax,
                        Email: this.model.email,
                        Website: this.model.website,
                        Note: this.model.note,
                        Inactive: (this.model.inactive) ? 1 : 0,

                    }
                };

                // edit user
                if (this.idParams) {
                    requestData.data.ObjectID = this.idParams;
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

            }

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
