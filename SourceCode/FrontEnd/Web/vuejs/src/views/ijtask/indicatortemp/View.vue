<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Mẫu bảng chỉ tiêu ĐGCV: {{model.TemplateName}}</span>
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
                <span>{{itemNo}} - {{reqParams.idsArray.length + this.reqParams.perPage * (this.reqParams.currentPage - 1)}}</span>
                /
                <span>{{reqParams.total}}</span>
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
            <div class="form-body">
              <div class="form-group row">
                <div class="col-md-4 m-0">Tên</div>
                <div class="col-md-16 m-0">
                  {{model.TemplateName}}
                </div>
                <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex app-object-code">
                  <span>Mã số</span>
                  {{model.TemplateNo}}
                </div>
              </div>

              <div class="form-group row">
                <label class="col-md-4 m-0">Loại chỉ tiêu </label>
                <label class="col-md-8 m-0">{{model.IndicatorType}}</label>
<!--                <label class="col-md-4 m-0">Phương pháp đánh giá </label>-->
<!--                <label class="col-md-8 m-0">{{model.EvaluationMethod}}</label>-->
              </div>

              <div class="form-group row">
                <label class="col-md-4 m-0">Giá trị loại chỉ tiêu:</label>
                <label class="col-md-20 m-0"></label>
              </div>

              <table class="table b-table table-sm table-bordered">
                <thead>
                <tr>
                  <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Phương pháp đánh giá</th>
                  <th scope="col" style="border-bottom: none;" class="text-center">Chỉ tiêu</th>
                  <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Đơn vị tính</th>
                  <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Loại chấm điểm</th>
                  <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Trọng số</th>
                  <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Tần suất</th>
                  <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Ghi chú</th>
                  <th scope="col" style="width: 50px; border-bottom: none;" class="text-center"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(field, fkey) in model.IndicatorTempItem">
                  <td class="has-padding">{{EvaluationMethodArr[field.EvaluationMethod]}}</td>
                  <td class="has-padding">{{field.IndicatorName}}</td>
                  <td class="has-padding">{{model.Uom[field.UomID]}}</td>
                  <td class="has-padding">{{model.GradingTypeName[field.GradingType]}}</td>
                  <td class="has-padding">{{field.Rate}}</td>
                  <td class="has-padding">{{model.FrequencyTypeName[field.FrequencyType]}}</td>
                  <td class="has-padding">{{field.Description}}</td>
                  <td class="has-padding text-center">
                    <i @click="onViewOnTableKeyResult(field.TransItemID)"
                       class="fa fa-external-link ij-icon ij-icon-plus"
                       title="Xem kết quả then chốt" v-if="model.IndicatorTempItem[fkey].EvaluationMethod==2"
                       style="font-size: 18px; cursor: pointer"></i>
                    <i @click="onViewOnTableFeedback(field.TransItemID)"
                       class="fa fa-external-link ij-icon ij-icon-plus"
                       title="Xem kết quả phản hồi" v-if="model.IndicatorTempItem[fkey].EvaluationMethod==6"
                       style="font-size: 18px; cursor: pointer"></i>
                  </td>

                </tr>
                </tbody>
              </table>
            </div>
          </b-card>
        </div>
      </vue-perfect-scrollbar>
    </div>

    <!-- Popup Add KeyResult -->
    <b-modal ref="ModalViewKeyResult" id="ij-modal-lg" size="xl"
             title="Bảng mẫu chỉ tiêu ĐGCV – Kết quả then chốt " >
      <div class="main-body main-body-view-action" style="margin-top: 10px;margin-bottom: 0px;">
        <div class="table-responsive pb-10">
          <table class="table b-table table-hover table-sm b-table-selectable table-bordered">
            <thead>
            <tr>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Loại chấm điểm</th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Đơn vị tính</th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Trọng số</th>
              <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Ghi chú</th>
<!--              <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Ghi chú</th>-->
            </tr>
            </thead>
            <tbody>
            <tr v-for="(field, key) in model.IndicatorTempItemKeyresult[TransItemIDCurrent]">
              <td>{{KeyResult[model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].KeyresultType]}}</td>
              <td>{{model.Uom[model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].UomID]}}</td>
              <td>{{model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].Rate}}</td>
              <td>{{model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].KeyresultName}}</td>
<!--              <td>{{model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].Description}}</td>-->
            </tr>
            </tbody>
          </table>
        </div>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModalViewKeyResult()">
            Đóng
          </b-button>
        </div>
      </template>
    </b-modal>


    <!-- Popup Add Feedback -->
    <b-modal ref="ModalViewFeedback" id="ij-modal-lg" size="xl"
             title="Bảng mẫu chỉ tiêu ĐGCV – Kết quả phản hồi" >
      <div class="main-body main-body-view-action" style="margin-top: 10px;margin-bottom: 0px;">
        <div class="table-responsive pb-10">
          <table class="table b-table table-hover table-sm b-table-selectable table-bordered">
            <thead>
            <tr>
              <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Tên chỉ tiêu</th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Là kết quả</th>
              <th scope="col" style="width: 15%; border-bottom: none;" class="text-center">Kiểu kết quả</th>
              <th scope="col" style="width: 36%; border-bottom: none;" class="text-center">Ghi chú</th>
<!--              <th scope="col" style="border-bottom: none;" class="text-center">Ghi chú</th>-->
            </tr>
            </thead>
            <tbody>
            <tr v-for="(field, key) in model.IndicatorTempItemFeedback[TransItemIDCurrent]">
              <td>{{model.IndicatorTempItemFeedback[TransItemIDCurrent][key].IndicatorName}}</td>
              <td class="text-center">
                <b-form-checkbox :disabled="true" v-model="model.IndicatorTempItemFeedback[TransItemIDCurrent][key].isBinaryData==1">
                </b-form-checkbox>
              </td>
              <td>{{model.BinaryData[model.IndicatorTempItemFeedback[TransItemIDCurrent][key].BinaryDataID]}}</td>
              <td>{{model.IndicatorTempItemFeedback[TransItemIDCurrent][key].FeedbackName}}</td>
<!--              <td>{{model.IndicatorTempItemFeedback[TransItemIDCurrent][key].Description}}</td>-->
            </tr>
            </tbody>
          </table>
        </div>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModalViewFeedback()">
            Đóng
          </b-button>
        </div>
      </template>
    </b-modal>
  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import Swal from 'sweetalert2';
  import 'sweetalert2/src/sweetalert2.scss';

  const ListRouter = 'task-indicator-temp';
  const EditRouter = 'task-indicator-temp-edit';
  const CreateRouter = 'task-indicator-temp-create';
  const DetailApi = 'task/api/indicator-temp/view';
  const ListApi = 'task/api/indicator-temp';
  const DeleteApi = 'task/api/indicator-temp/delete';

  const IndicatorType = {
    1: 'Chỉ tiêu đơn vị',
    2: 'Chỉ tiêu cá nhân',
  };
  const EvaluationMethod = {
    1: 'Chỉ số đo lường hiệu suất',
    2: 'Mục tiêu và kết quả then chốt',
    3: 'Thẻ điểm cân bằng',
    4: 'Bảng điểm',
    5: 'Danh sách kiểm tra',
    6: 'Phản hồi 360 độ',
  };
  export default {
    name: 'task-indicator-temp',
    data() {
      return {
        idParams: this.$route.params.id,
        reqParams: this.$route.params.req,
        model: {
          IndicatorTempItemKeyresult: [],
          IndicatorTempItemFeedback: [],
          TemplateID: null,
          TemplateNo: '',
          TemplateName: '',
          UomID: '',
          UomName: '',
          IndicatorType: null,
          EvaluationMethod: null,
          Uom: [],
          BinaryData: [],
          Inactive: false,
          GradingTypeName: {},
          FrequencyTypeName: {},
        },
        KeyResult : {
          1: 'Điểm thường',
          2: 'Điểm thưởng',
          3: 'Điểm phạt',
        },
        EvaluationMethodArr : {
          1: 'Chỉ số đo lường hiệu suất',
          2: 'Mục tiêu và kết quả then chốt',
          3: 'Thẻ điểm cân bằng',
          4: 'Bảng điểm',
          5: 'Danh sách kiểm tra',
          6: 'Phản hồi 360 độ',
          7: 'Trắc nghiệm',
        },
        TransItemIDCurrent: '',
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
      itemNo() {
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
        ApiService.customRequest(requestData).then((responses) => { //console.log('hhhhhhhh'); console.log(responses);
          let responsesData = responses.data;
          self.defaultModel = responsesData;
          if (responsesData.status === 1) {
            self.model.TemplateID = responsesData.data.data.TemplateID;
            self.model.TemplateNo = responsesData.data.data.TemplateNo;
            self.model.TemplateName = responsesData.data.data.TemplateName;
            self.model.inactive = (responsesData.data.data.Inactive) ? true : false;

            self.model.EvaluationMethod_val = responsesData.data.data.EvaluationMethod;
            self.model.IndicatorType = (responsesData.data.data.IndicatorType && IndicatorType[responsesData.data.data.IndicatorType]) ? IndicatorType[responsesData.data.data.IndicatorType] : 'Chỉ tiêu đơn vị';
            self.model.EvaluationMethod = (responsesData.data.data.EvaluationMethod && EvaluationMethod[responsesData.data.data.EvaluationMethod]) ? EvaluationMethod[responsesData.data.data.EvaluationMethod] : 'Chỉ số đo lường hiệu suất';

            self.model.IndicatorTemp = responsesData.data.IndicatorTemp;
            self.model.IndicatorTempItem = responsesData.data.IndicatorTempItem;
            self.model.GradingTypeName = responsesData.data.GradingTypeName;
            self.model.FrequencyTypeName = responsesData.data.FrequencyTypeName;
            _.forEach(responsesData.data.Uom, function (field, key) {
              self.model.Uom[field.UomID] = field.UomName
            });
            _.forEach(responsesData.data.BinaryData, function (field, key) {
                self.model.BinaryData[field.BinaryDataID] = field.BinaryDataName
              });
            _.forEach(responsesData.data.IndicatorTempItemKeyResult, function (field, key) {
              if(self.model.IndicatorTempItemKeyresult[field.TransItemID] == undefined){
                field.LineIDTemp = field.TransItemID;
                self.model.IndicatorTempItemKeyresult[field.TransItemID] = [];
              }
              self.model.IndicatorTempItemKeyresult[field.TransItemID].push(field)
            });
            _.forEach(responsesData.data.IndicatorTempItemFeedback, function (field, key) {
              if(self.model.IndicatorTempItemFeedback[field.TransItemID] == undefined){
                field.LineIDTemp = field.TransItemID;
                self.model.IndicatorTempItemFeedback[field.TransItemID] = [];
              }
              self.model.IndicatorTempItemFeedback[field.TransItemID].push(field)
            });

          }

          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      getUomNameByID(UomID) {
        let UomObj = _.find(this.model.Uom, ['UomID', UomID]);
        if (_.isObject(UomObj)) return UomObj.UomName;
        return '';
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

        if (this.reqParams.search.TemplateName !== '') {
          requestData.data.TemplateName = this.reqParams.search.TemplateName;
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
              self.reqParams.idsArray.push(value.CustomerCodeID);
            });

            (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          self.$store.commit('isLoading', false);
          console.log(error);
        });

      },
      handleCopyItem() {
        this.$router.push({name: CreateRouter, params: {itemCopy: this.defaultModel}});
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

            });

          }
        });
      },
      onViewOnTableKeyResult(TransItemID) {
        this.TransItemIDCurrent = TransItemID;
        this.$refs['ModalViewKeyResult'].show();
      },
      onViewOnTableFeedback(TransItemID) {
        this.TransItemIDCurrent = TransItemID;
        this.$refs['ModalViewFeedback'].show();
      },
      onHideModalViewKeyResult(){
        this.$refs['ModalViewKeyResult'].hide();
      },
      onHideModalViewFeedback(){
        this.$refs['ModalViewFeedback'].hide();
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

<style>
  #ij-modal-lg .modal-lg .modal-content{
    width: 1024px;
    margin: auto;
  }
  @media (max-width: 1024px){
    #ij-modal-lg .modal-lg {
      max-width: 100%;
    }
    #ij-modal-lg .modal-lg .modal-content{
      width: 90%;
      margin: auto;
    }
  }
  @media (min-width: 992px){
    #ij-modal-lg .modal-lg {
      max-width: 100%;
    }
  }
</style>

