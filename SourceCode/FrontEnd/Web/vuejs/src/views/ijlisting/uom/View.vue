<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span><i class="icon-eye icon mr-2"></i> Đơn vị tính<span v-if="model.uomName">:</span> {{model.uomName}}</span>
                        </div>
                    </b-col>
                    <b-col class="col-md-6"></b-col>
                </b-row>
                <b-row>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-actions">
                          <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i class="fa fa-plus"></i> Thêm</b-button>
                          <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="onEditClicked"><i class="fa fa-edit"></i> Sửa</b-button>
                            <b-dropdown variant="primary" id="dropdown-actions" class="main-header-action mr-2" text="Thao tác">
                                <b-dropdown-item @click="handleDeleteItem">Xóa</b-dropdown-item>
                                <b-dropdown-item @click="handleCopyItem">Nhân bản</b-dropdown-item>
                                <b-dropdown-item>Chia sẻ</b-dropdown-item>
                                <b-dropdown-item>Chat</b-dropdown-item>
                                <b-dropdown-item>Zalo</b-dropdown-item>
                                <b-dropdown-item>Phân quyền</b-dropdown-item>
                            </b-dropdown>
                        </div>
                    </b-col>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-icons">
                            <div class="main-header-item-counter ml-auto mr-3" v-if="reqParams">
                                <span>{{itemNo}} - {{reqParams.total}}</span>
                            </div>
                            <b-button-group id="main-header-views" class="main-header-views">
                                <b-button id="tooltip-view-prev" @click="onNavigationItem('prev')" v-if="reqParams" class="main-header-view"><i class="fa fa-angle-left"></i></b-button>
                                <b-button id="tooltip-view-next" @click="onNavigationItem('next')" v-if="reqParams" class="main-header-view"><i class="fa fa-angle-right"></i></b-button>
                                <b-button id="tooltip-view-back-to-list" @click="onBackToList" class="main-header-view"><i class="fa fa-list"></i></b-button>
                                <b-tooltip container="main-header-views" target="tooltip-view-back-to-list">Danh sách</b-tooltip>
                                <b-tooltip container="main-header-views" target="tooltip-view-prev">Trước</b-tooltip>
                                <b-tooltip container="main-header-views" target="tooltip-view-next">Sau</b-tooltip>
                            </b-button-group>
                            <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
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

                        <div class="form-group row">
                          <div class="col-lg-4 col-md-2 mb-md-0 col-sm-4 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên</div>
                          <div class="col-lg-8 col-md-8 col-sm-4 mb-sm-0 mb-md-0 col-20 mb-2">
                            {{model.uomName}}
                          </div>
                          <div class="col-lg-4 col-md-2 mb-md-0 col-sm-4 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Viết tắt</div>
                          <div class="col-lg-4 col-md-6 col-sm-4 mb-sm-0 mb-md-0 col-20 mb-2">
                            {{model.UomShortName}}
                          </div>
                          <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex app-object-code">
                            <span>Mã số</span>
                            {{model.uomNo}}
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-md-4 m-0">Loại ĐVT</label>
                          <label class="col-md-8 m-0">{{model.UomType}}</label>

                          <label class="col-md-4 m-0">Loại ĐVT thời gian</label>
                          <div class="col-md-4 m-0">
                            {{model.UomTime}}
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-md-4 m-0">Loại ĐVT tiền tệ</label>
                          <label class="col-md-8 m-0">{{model.UomCurrency}}</label>

                          <label class="col-md-4 m-0">Loại ĐVT điểm số</label>
                          <div class="col-md-4 m-0">
                            {{model.UomPoint}}
                          </div>
                        </div>
                        <b-row class="mb-3">
                          <b-col sm="2">Ghi chú</b-col>
                          <b-col sm="10" >{{model.note}}</b-col>
                        </b-row>

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
    const ViewApi = 'listing/api/uom/view';
    const ListApi = 'listing/api/uom';
    const DeleteApi = 'listing/api/uom/delete';
    const UomType = {
      1: 'Đơn vị đo kích thước',
      2: 'Đơn vị đo trọng lượng',
      3: 'Đơn vị đo thời gian',
      4: 'Đơn vị đo tiền tệ',
      5: 'Đơn vị số đếm',
      6: 'Đơn vị điểm số',
      7: 'Đơn vị phần trăm (%)',
      8: 'Đơn vị nhị phân (1-0)',
      9: 'Đơn vị đo sự việc',
      99: 'Đơn vị đo khác',
    };
    const UomTime = {
      1: 'năm',
      2: 'quý',
      3: 'tháng',
      4: 'tuần',
      5: 'ngày',
      6: 'giờ',
      7: 'phút',
      8: 'giây',
    };
    const UomCurrency = {
      1: 'Tỷ',
      2: 'Triệu',
      3: 'Nghìn',
      4: 'Đồng',
    };
    const UomPoint = {
      1: 'Thang điểm 100',
      2: 'Thang điểm 10',
      3: 'Thang điểm 5',
      4: 'Thang điểm 4',
      5: 'Thang điểm 3',
    };
    String.prototype.capitalize = function () {
        return this.charAt(0).toUpperCase() + this.slice(1);
    };

    export default {
        name: 'listing-view-item',
        data () {
            return {
                idParams: this.$route.params.id,
                reqParams: this.$route.params.req,
                model: {
                    uomID: null,
                    uomNo: '',
                    uomName: '',
                    UomShortName: '',
                    UomType: '',
                    UomTime: '',
                    UomCurrency: '',
                    UomPoint: '',
                    note: null,
                    NOrder: null,
                    inactive: null,
                    numberValue: null,
                    Prefix: '',
                    Suffix: '',
                },
                defaultModel: {},
                stage: {
                  updatedData: false,
                  message: (this.$route.params.message) ? this.$route.params.message : ''
                }
            }

        },

        components: {},
        beforeCreate() {
            if (!this.$route.params.id) {
                this.$router.push({name: ListRouter});
            }
        },
        mounted() {
            this.fetchData();

          // hiển thị thông báo
          if (this.stage.message && this.stage.message !== '') {
            this.$bvToast.toast(this.stage.message, {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });
          }
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
                if (this.idParams == 0 || _.isUndefined(this.idParams)) {
                    return false;
                }
                let self = this;
                let urlApi = '';
                let requestData = {
                    method: 'get',
                };
                // Api edit user
                if(this.idParams){
                    urlApi = ViewApi + '/' + this.idParams;
                    let data = {
                        id: this.idParams
                    };
                    requestData.data = data;
                }
                requestData.url = urlApi;
                this.$store.commit('isLoading', true);
                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => {
                    let responsesData = responses.data;
                    self.defaultModel = responsesData;
                    if (responsesData.status === 1) {
                        self.model.uomID = responsesData.data.UomID;
                        self.model.uomNo = responsesData.data.UomNo;
                        self.model.uomName = responsesData.data.UomName;
                        self.model.UomShortName = responsesData.data.UomShortName;
                        self.model.UomType = (responsesData.data.UomType && UomType[responsesData.data.UomType]) ? UomType[responsesData.data.UomType] : '';
                        self.model.UomTime = (responsesData.data.UomTime && UomTime[responsesData.data.UomTime]) ? UomTime[responsesData.data.UomTime] : '';
                        self.model.UomCurrency = (responsesData.data.UomCurrency && UomCurrency[responsesData.data.UomCurrency]) ? UomCurrency[responsesData.data.UomCurrency] : '';
                        self.model.UomPoint = (responsesData.data.UomPoint && UomPoint[responsesData.data.UomPoint]) ? UomPoint[responsesData.data.UomPoint] : '';
                        self.model.note = responsesData.data.Note;
                        self.model.NOrder = responsesData.data.NOrder;
                        self.model.inactive = (responsesData.data.Inactive) ? true : false;
                        self.model.numberValue = responsesData.data.NumberValue;
                        self.model.suffix = responsesData.data.Suffix;
                        self.model.prefix = responsesData.data.Prefix;
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
                if (_.isString(message)) {
                    this.$router.push({name: ListRouter, params: {message: message}});
                } else {
                    this.$router.push({name: ListRouter});
                }
            },
            handleCopyItem(){
                this.$router.push({name: CreateRouter, params: {itemCopy: this.defaultModel}});
            },
            handleDeleteItem() {
                let self = this;
                let title = 'Bạn có muốn xóa bản ghi?';
                Swal.fire({
                    title: title,
                    text: 'Bạn sẽ không thể khôi phục thông tin bản ghi!',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Hủy bỏ'
                }).then((result) => {
                    if (result.value) {
                        let requestData = {
                            method: 'post',
                            url: DeleteApi + '/' + self.idParams,
                            data: {
                                array_id: [self.idParams],
                            },
                        };

                        ApiService.setHeader();
                        ApiService.customRequest(requestData).then((response) => {
                            let responseData = response.data;
                            if (responseData.status === 1) {
                                self.onBackToList('Bản ghi đã được xóa');
                            } else {
                                Swal.fire(
                                    'Có lỗi',
                                    '',
                                    'error'
                                );
                                console.log(response);
                            }
                        }, (error) => {
                          console.log(error);
                          Swal.fire({
                            title: 'Thông báo',
                            text: 'Không kết nối được với máy chủ',
                            confirmButtonText: 'Đóng'
                          });
                        });

                    }
                });
            },
            updateModel() {
                if (this.stage.updatedData) {
                    this.$forceUpdate();
                }
            },
        },
        watch: {
            idParams() {
                this.fetchData();
            }
        }
    }
</script>

<style lang="css"></style>
