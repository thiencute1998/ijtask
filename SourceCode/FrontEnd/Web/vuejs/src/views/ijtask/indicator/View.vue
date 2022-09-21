<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Chỉ tiêu đánh giá công việc: {{model.indicatorName}}</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i
                class="fa fa-plus"></i> Thêm
              </b-button>
              <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="onEditClicked"><i
                class="fa fa-edit"></i> Sửa
              </b-button>
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
                <span>{{itemNo}} - {{itemTotalPerPage}}</span>
                /
                <span>{{itemTotal}}</span>
              </div>
              <b-button-group id="main-header-views" class="main-header-views">
                <b-button id="tooltip-view-prev" @click="onNavigationItem('prev')" v-if="reqParams"
                          class="main-header-view"><i class="fa fa-angle-left"></i></b-button>
                <b-button id="tooltip-view-next" @click="onNavigationItem('next')" v-if="reqParams"
                          class="main-header-view"><i class="fa fa-angle-right"></i></b-button>
                <b-button id="tooltip-view-back-to-list" @click="onBackToList" class="main-header-view"><i
                  class="fa fa-list"></i></b-button>
                <b-tooltip container="main-header-views" target="tooltip-view-back-to-list">Danh sách</b-tooltip>
                <b-tooltip container="main-header-views" target="tooltip-view-prev">Trước</b-tooltip>
                <b-tooltip container="main-header-views" target="tooltip-view-next">Sau</b-tooltip>
              </b-button-group>
              <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen="true"/>
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
              <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên</div>
              <div class="col-lg-16 col-md-14 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-4 app-object-name">
                {{model.indicatorName}}
              </div>
              <div class="col-lg-4 col-md-12 col-sm-8 col-24 d-flex app-object-code">
                <span>Mã số</span>
                {{model.indicatorNo}}
              </div>
            </div>
            <div class="form-group row">
              <label class="col-md-4 m-0">Là con của</label>
              <label class="col-md-20 m-0">{{model.parentName}}</label>
            </div>
            <div class="form-group row">
              <label class="col-md-4 m-0">Loại chỉ tiêu</label>
              <label class="col-md-20 m-0">
                <ijcore-modal-cate-list v-model="model.IndicatorCate" :is-view="true"></ijcore-modal-cate-list>
              </label>
            </div>
            <div class="form-group row">
              <label class="col-md-4 m-0">Đơn vị tính</label>
              <label class="col-md-8 m-0">{{model.uomName}}</label>
              <label class="col-md-4 m-0">Tần suất</label>
              <label class="col-md-8 m-0">{{model.FrequencyValue}}</label>
            </div>
            <div class="form-group row">
              <label class="col-md-4 m-0">Phương pháp đánh giá</label>
              <label class="col-md-8 m-0">{{model.EvaluationMethod}}</label>
              <label class="col-md-4 m-0">Loại điểm</label>
              <label class="col-md-8 m-0">{{model.GradingMethod}}</label>
            </div>
            <div class="form-group row">
              <label class="col-md-4 m-0">Bảng điểm</label>
              <label class="col-md-8 m-0">{{model.ScaleRateName}}</label>
              <label class="col-md-4 m-0">PPĐG công việc HT</label>
              <label class="col-md-8 m-0">{{model.IndicatorCalMethod}}</label>
            </div>

            <div class="form-group row">
              <label class="col-md-4 m-0">Mô tả</label>
              <label class="col-md-20 m-0" id="view-Note">
                {{model.description}}
              </label>
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
  import IjcoreModalCateList from "../../../components/IjcoreModalCateList";

  const ListRouter = 'task-indicator';
  const EditRouter = 'task-indicator-edit';
  const CreateRouter = 'task-indicator-create';
  const DetailApi = 'task/api/indicator/view';
  const ListApi = 'task/api/indicator';
  const DeleteApi = 'task/api/indicator/delete';
  const FrequencyValue = {
    1: 'Năm',
    2: '6 tháng',
    3: 'Quý',
    4: 'Tháng',
    5: 'Tuần',
    6: 'Ngày',
    7: 'Vụ việc',
  };
  const GradingMethod = {
    1: 'Đơn vị đo',
    2: 'Bảng điểm',
    3: 'Nhi phân',
    4: 'Tỷ lệ %',
  };
  const EvaluationMethod = {
    1: 'Chỉ số đo lường hiệu suất',
    2: 'Mục tiêu và kết quả then chốt',
    3: 'Thẻ điểm cân bằng',
    4: 'Bảng điểm',
    5: 'Danh sách kiểm tra',
    6: 'Phản hồi 360 độ',
  };
  const IndicatorCalMethod = {
    1: 'Trung bình cộng',
    2: 'Tổng cộng',
    3: 'Kết quả cuối cùng',
  };

  export default {
    name: 'task-detail-item',
    data() {
      return {
        idParams: this.$route.params.id,
        reqParams: this.$route.params.req,
        model: {
          IndicatorID: null,
          indicatorNo: '',
          indicatorName: '',
          FrequencyValue: null,
          ScaleRateID: null,
          ScaleRateName: '',
          GradingMethod: null,
          EvaluationMethod: null,
          IndicatorCalMethod: '',
          IndicatorCate: [],
          description: '',
          uomID: '',
          uomName: '',
          inactive: false
        },
        defaultModel: {},
        stage: {
          updatedData: false,
          message: (this.$route.params.message) ? this.$route.params.message : ''
        }
      }

    },

    components: {
      IjcoreModalCateList
    },
    beforeCreate() {

      this.$router.onReady(() => {
        if (!this.$route.params.id) {
          this.$router.push({name: ListRouter});
        }
      });
    },
    created() {
    },
    beforeMount() {
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
      itemNo() {
        if (!this.idParams) return;
        let index = 0;
        index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
        return index;
      },
      itemTotalPerPage() {
        if (!this.idParams) return;
        return this.reqParams.idsArray.length + this.reqParams.perPage * (this.reqParams.currentPage - 1);
      },
      itemTotal() {
        if (!this.idParams) return;
        return this.reqParams.total;

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
        if (this.idParams) {
          urlApi = DetailApi + '/' + this.idParams;
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
            self.model.indicatorID = responsesData.data.data.IndicatorID;
            self.model.indicatorName = responsesData.data.data.IndicatorName;
            self.model.indicatorNo = responsesData.data.data.IndicatorNo;
            //self.model.FrequencyValue = (responsesData.data.data.FrequencyValue && FrequencyValue[responsesData.data.data.FrequencyValue]) ? FrequencyValue[responsesData.data.data.FrequencyValue] : 'Năm';
            self.model.GradingMethod = (responsesData.data.data.GradingMethod && GradingMethod[responsesData.data.data.GradingMethod]) ? GradingMethod[responsesData.data.data.GradingMethod] : 'Đơn vị đo';
            self.model.IndicatorCalMethod = (responsesData.data.data.IndicatorCalMethod && IndicatorCalMethod[responsesData.data.data.IndicatorCalMethod]) ? IndicatorCalMethod[responsesData.data.data.IndicatorCalMethod] : 'Tổng cộng';

            let ArrFrequencyValue = [];
            _.forEach(responsesData.data.data.FrequencyValue, function (val, key) {
              if(val!=','){
              name = FrequencyValue[val];
              ArrFrequencyValue.push(name);
              }
            })
            self.model.FrequencyValue = ArrFrequencyValue.join(', ');
            // if (responsesData.data.data.IndicatorCalMethod) {
            //   self.model.IndicatorCalMethod = responsesData.data.data.IndicatorCalMethod;
            //   _.forEach(IndicatorCalMethod, function (val, key) {
            //     self.model.IndicatorCalMethod = val;
            //   })
            // }
            self.model.EvaluationMethod = (responsesData.data.data.EvaluationMethod && EvaluationMethod[responsesData.data.data.EvaluationMethod]) ? EvaluationMethod[responsesData.data.data.EvaluationMethod] : 'Chỉ số đo lường hiệu suất';
            self.model.uomID = responsesData.data.data.UomID;
            self.model.description = responsesData.data.data.Description;
            self.model.inactive = (responsesData.data.data.Inactive) ? true : false;
            self.model.parentName = responsesData.data.ParentName;
            self.model.uomName = responsesData.data.UomName;
            self.model.ScaleRateID = responsesData.data.ScaleRateID;
            self.model.ScaleRateName = responsesData.data.ScaleRateName;
            self.model.IndicatorCate = responsesData.data.IndicatorCate;
          }

          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
          Swal.fire({
            title: 'Thông báo',
            icon: 'warning',
            text: 'Phiên làm việc của bạn đã hết hạn. Vui lòng thử lại hoặc đăng nhập lại!',
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
        } else if (newIndex < 0 && this.reqParams.currentPage > 1) {
          this.reqParams.currentPage = this.reqParams.currentPage - 1;
          this.getItemIds(type);
        } else {
          this.idParams = (this.reqParams.idsArray[newIndex]) ? this.reqParams.idsArray[newIndex] : this.idParams;
        }
      },
      getItemIds(type) {
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

        if (this.reqParams.search.IndicatorNo !== '') {
          requestData.data.IndicatorNo = this.reqParams.search.IndicatorNo;
        }
        if (this.reqParams.search.IndicatorName !== '') {
          requestData.data.CIndicatorName = this.reqParams.search.IndicatorName;
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
              self.reqParams.idsArray.push(value.IndicatorID);
            });

            (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
          Swal.fire({
            title: 'Thông báo',
            icon: 'warning',
            text: 'Phiên làm việc của bạn đã hết hạn. Vui lòng thử lại hoặc đăng nhập lại!',
            confirmButtonText: 'Đóng'
          });
        });

      },
      onEditClicked() {
        this.$router.push({
          name: EditRouter,
          params: {id: this.idParams, req: this.reqParams}
        });
      },
      onCreateClicked() {
        this.$router.push({name: CreateRouter});
      },
      onBackToList(message = '') {
        if (_.isString(message)) {
          this.$router.push({name: ListRouter, params: {message: message}});
        } else {
          this.$router.push({name: ListRouter});
        }

      },
      handleCopyItem() {
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
                icon: 'warning',
                text: 'Phiên làm việc của bạn đã hết hạn. Vui lòng thử lại hoặc đăng nhập lại!',
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
    },
    // beforeDestroy(){
    //     window.removeEventListener('unload', this.onReloadPage)
    // }
  }
</script>

<style lang="css"></style>
