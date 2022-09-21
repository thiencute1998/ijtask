<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col md="9">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Chỉ tiêu đánh giá công việc <span
                v-if="model.IndicatorName">:</span> {{model.IndicatorName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Chỉ tiêu đánh giá công việc <span
                v-if="model.IndicatorName">:</span> {{model.IndicatorName}}</span>
            </div>
          </b-col>
          <b-col md="3"></b-col>
        </b-row>
        <b-row>
          <b-col md="6">
            <div class="main-header-item main-header-actions">
              <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitForm"><i
                class="fa fa-check-square-o"></i> Lưu
              </b-button>
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onBackToList"><i
                class="fa fa-ban"></i> Hủy
              </b-button>
            </div>
          </b-col>
          <b-col md="6">
            <div class="main-header-item main-header-icons">
              <div class="main-header-collapse">
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
            <div class="form-group row align-items-center">
              <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-4" style="white-space: nowrap">Tên</div>
              <div class="col-lg-16 col-md-14 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-4 app-object-name">
                <input v-model="model.IndicatorName" type="text" id="IndicatorName" class="form-control"
                       placeholder="Tên chỉ tiêu" name="IndicatorName"/>
              </div>
              <div class="col-lg-4 col-md-12 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.IndicatorNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0">Là con của</label>
              <div class="col-md-20">
                <Select2 v-model="model.ParentID" @change="changePR" :options="model.indicatorOption"
                         :settings="{allowClear: true, placeholder: {id: 0, text: 'Chọn chỉ tiêu cha'}}"></Select2>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0">Loại chỉ tiêu</label>
              <div class="col-md-20">
                <ijcore-modal-cate-list
                  v-model="model.IndicatorCate"
                  table-cate-list="task_indicator_cate_list"
                  table-cate-value="task_indicator_cate_value"
                  :field-update-cate-value="['ConvertedValue', 'ValueID']"
                  object-i-d="IndicatorID"
                  title="Loại chỉ tiêu"
                  placeholder="Chọn loại chỉ tiêu"></ijcore-modal-cate-list>
              </div>
            </div>


            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0">Đơn vị tính</label>
              <div class="col-md-8">
                <b-form-select v-model="model.UomID" :options="model.uomOption" id="UomID"></b-form-select>
              </div>
              <label class="col-md-4 m-0">Tần suất</label>
              <div class="col-md-8">
<!--                <b-form-select-->
<!--                  v-model="model.FrequencyValue"-->
<!--                  :options="[-->
<!--                                        {value: null, text: 'Chọn tần suất'},-->
<!--                                        {value: 1, text: 'Năm'},-->
<!--                                        {value: 2, text: '6 tháng'},-->
<!--                                        {value: 3, text: 'Quý'},-->
<!--                                        {value: 4, text: 'Tháng'},-->
<!--                                        {value: 5, text: 'Tuần'},-->
<!--                                        {value: 6, text: 'Ngày'},-->
<!--                                        {value: 7, text: 'Vụ việc'},]">-->
<!--                </b-form-select>-->
                <Select2 v-model="model.FrequencyValue" multiple="true" :options="[
                                        {id: 1, text: 'Năm'},
                                        {id: 2, text: '6 tháng'},
                                        {id: 3, text: 'Quý'},
                                        {id: 4, text: 'Tháng'},
                                        {id: 5, text: 'Tuần'},
                                        {id: 6, text: 'Ngày'},
                                        {id: 7, text: 'Vụ việc'},]"
                         :settings="{multiple: true, allowClear: true, placeholder: {id: 0, text: 'Chọn tần suất'}}">

                </Select2>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0">Phương pháp đánh giá</label>
              <div class="col-md-8">
                <b-form-select
                  v-model="model.EvaluationMethod" v-on:change="changeEvaluationMethod($event)"
                  :options="[
                                            {value: 'null', text: 'Chọn phương pháp'},
                                            {value: 1, text: 'Chỉ số đo lường hiệu suất'},
                                            {value: 2, text: 'Mục tiêu và kết quả then chốt'},
                                            {value: 3, text: 'Thẻ điểm cân bằng'},
                                            {value: 4, text: 'Bảng điểm'},
                                            {value: 5, text: 'Danh sách kiểm tra'},
                                            {value: 6, text: 'Phản hồi 360 độ'},
                                            {value: 7, text: 'Trắc nghiệm'},]">
                </b-form-select>
              </div>
              <label class="col-md-4 m-0">Loại điểm</label>
              <div class="col-md-8">
                <b-form-select
                  v-model="model.GradingMethod"
                  :options="[
                                          {value: null, text: 'Chọn loại điểm'},
                                          {value: 1, text: 'Đơn vị đo'},
                                          {value: 2, text: 'Bảng điểm'},
                                          {value: 3, text: 'Nhi phân'},
                                          {value: 4, text: 'Tỷ lệ %'},]">
                </b-form-select>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0">Bảng điểm</label>
              <div class="col-md-8">
                <!--                            <IjcoreModalListing v-model="model" :title="'bảng điểm'" :api="'/listing/api/common/list'"-->
                <!--                                                :table="'task_scale_rate'" :FieldID="'ScaleRateID'" :FieldNo="'ScaleRateNo'" :FieldName="'ScaleRateName'">-->
                <!--                            </IjcoreModalListing>-->
                <Select2 v-model="model.ScaleRateID" @change="changeScaleRate" :options="model.ScaleRateOption"
                         :settings="{allowClear: true, placeholder: {id: 0, text: 'Chọn bảng điểm'}}"></Select2>
              </div>
              <label class="col-md-4 m-0" title="Phương pháp đánh giá kết quả công việc hoàn thành">PPĐG công việc HT</label>
              <div class="col-md-8">
                <b-form-select
                  v-model="model.IndicatorCalMethod"
                  :options="[
                                            {value: 1, text: 'Trung bình cộng'},
                                            {value: 2, text: 'Tổng cộng'},
                                            {value: 3, text: 'Kết quả cuối cùng'}]">
                </b-form-select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-md-2" for="Description">Mô tả</label>
              <div class="col-md-20">
                <textarea v-model="model.Description" class="form-control" id="Description" rows="3"
                          placeholder="Ghi chú" name="Description"></textarea>
              </div>
            </div>
<!--            <div v-if="model.EvaluationMethod==2">-->
<!--              &lt;!&ndash;List KeyResult&ndash;&gt;-->
<!--              <label>Kết quả then chốt:</label>-->
<!--              <table class="table b-table table-sm table-bordered table-editable">-->
<!--                <thead>-->
<!--                <tr>-->
<!--                  <th scope="col" class="text-center">Mô tả</th>-->
<!--                  <th scope="col" style="width: 10%" class="text-center">Trọng số</th>-->
<!--                  <th scope="col" style="width: 3%" class="text-center"></th>-->
<!--                </tr>-->
<!--                </thead>-->
<!--                <tbody>-->
<!--                <tr v-for="(field, key) in IndicatorKeyResult">-->
<!--                  <td>-->
<!--                    <b-form-input-->
<!--                      type="text"-->
<!--                      v-model="IndicatorKeyResult[key].Description">-->
<!--                    </b-form-input>-->
<!--                  </td>-->
<!--                  <td>-->
<!--                    <ijcore-number-->
<!--                      v-model="IndicatorKeyResult[key].Rate">-->
<!--                    </ijcore-number>-->
<!--                  </td>-->
<!--                  <td class="text-center">-->
<!--                    <i @click="onDeleteFieldOnKeyResult(key)" class="fa fa-trash-o" title="Xóa"-->
<!--                       style="font-size: 18px; cursor: pointer"></i>-->
<!--                  </td>-->
<!--                </tr>-->
<!--                </tbody>-->
<!--              </table>-->
<!--              <a @click="onAddFieldOnKeyResult" class="new-row">-->
<!--                <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới-->
<!--              </a>-->
<!--            </div>-->
<!--            <div v-if="model.EvaluationMethod==6">-->
<!--            &lt;!&ndash;List Feedback&ndash;&gt;-->
<!--              <label>Kết quả phản hồi:</label>-->
<!--              <table class="table b-table table-sm table-bordered table-editable">-->
<!--                <thead>-->
<!--                <tr>-->
<!--                  <th scope="col" class="text-center">Tên</th>-->
<!--                  <th scope="col" style="width: 10%" class="text-center">Là kết quả</th>-->
<!--                  <th scope="col" style="width: 22%" class="text-center">Kiểu kết quả</th>-->
<!--                  <th scope="col" style="width: 3%" class="text-center"></th>-->
<!--                </tr>-->
<!--                </thead>-->
<!--                <tbody>-->
<!--                <tr v-for="(field, key) in IndicatorFeedback">-->

<!--                  <td>-->
<!--                    <b-form-input-->
<!--                      type="text"-->
<!--                      v-model="IndicatorFeedback[key].FeedbackName">-->
<!--                    </b-form-input>-->
<!--                  </td>-->
<!--                  <td class="text-center">-->
<!--                    <b-form-checkbox-->
<!--                      type="check"-->
<!--                      v-model="IndicatorFeedback[key].IsBinaryData==1"-->
<!--                      @change="changeBinaryDataID(key)"-->
<!--                      autocomplete="Indicator">-->
<!--                    </b-form-checkbox>-->
<!--                  </td>-->
<!--                  <td>-->
<!--                    <b-form-select-->
<!--                      v-if="IndicatorFeedback[key].IsBinaryData==1"-->
<!--                      @change="changeBinaryData($event, key)"-->
<!--                      v-model="IndicatorFeedback[key].BinaryDataID"-->
<!--                      :options="BinaryDataOption" id="BinaryDataID"></b-form-select>-->
<!--                  </td>-->
<!--                  <td class="text-center">-->
<!--                    <i @click="onDeleteFieldOnKeyResult(key)" class="fa fa-trash-o" title="Xóa"-->
<!--                       style="font-size: 18px; cursor: pointer"></i>-->
<!--                  </td>-->
<!--                </tr>-->
<!--                </tbody>-->
<!--              </table>-->
<!--              <a @click="onAddFieldOnFeedback" class="new-row">-->
<!--                <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới-->
<!--              </a>-->
<!--            </div>-->
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
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import IjcoreNumber from "../../../../components/IjcoreNumber";
  import IjcoreModalCateList from "../../../../components/IjcoreModalCateList";

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
    data() {
      return {
        idParams: this.idParamsProps,
        reqParams: this.reqParamsProps,
        model: {
          IndicatorID: null,
          IndicatorNo: '',
          IndicatorName: '',
          ParentID: null,
          UomID: null,
          UomName: '',
          FrequencyValue: null,
          ScaleRateID: '',
          ScaleRateName: '',
          GradingMethod: null,
          EvaluationMethod: null,
          Inactive: false,
          Description: '',
          Uom: null,
          uomOption: [],
          Locked: 0,
          IndicatorCalMethod: 1,
          IndicatorCate: []
        },
        IndicatorKeyResult: [],
        IndicatorFeedback: [],
        BinaryDataOption: [],
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
      IjcoreNumber,
      IjcoreModalListing,
      Select2,
      IjcoreModalCateList
    },
    beforeCreate() {
    },
    mounted() {
      this.fetchData();
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
      changeBinaryData(value, key) {
        let BinaryData = _.find(this.BinaryDataOption, ['value', value]);
        this.IndicatorFeedback[key].FeedbackName = BinaryData.text;
        this.$forceUpdate()
      },
      changeBinaryDataID(key) {
        //this.$set(this.model.IndicatorTempItemFeedback, TransItemIDCurrent, this.model.IndicatorTempItemFeedback[TransItemIDCurrent]);
        if(this.IndicatorFeedback[key].IsBinaryData == 1){
          this.IndicatorFeedback[key].IsBinaryData = 0;
        }else{
          this.IndicatorFeedback[key].IsBinaryData = 1;
        }
        this.IndicatorFeedback[key].BinaryDataID = '';
        this.$forceUpdate()
      },
      onAddFieldOnKeyResult(){
        this.IndicatorKeyResult.push({
          Description: '',
          Rate: ''
        });
      },
      onDeleteFieldOnKeyResult(key){
        this.IndicatorKeyResult.splice(key, 1)
      },
      onAddFieldOnFeedback(){
        this.IndicatorFeedback.push({
          IsBinaryData: 0,
          BinaryDataID: '',
          FeedbackName: '',

        });
      },
      onDeleteFieldOnFeedback(key){
        this.IndicatorFeedback.splice(key, 1)
      },
      changeFrequencyValue(){
      },
      changeScaleRate(){
        let self = this;
        _.forEach(self.model.ScaleRateOption, function (val, key) {
          if(val.id == self.model.ScaleRateID){
            self.model.ScaleRateName = val.text
          }
        });
      },
      changeEvaluationMethod(event) {
        let self = this;
        let cktype = self.model.EvaluationMethod;
        if (cktype == 1) {
          self.model.GradingMethod = 1;
        } else if (cktype == 2 || cktype == 3 || cktype == 4) {
          self.model.GradingMethod = 2;
        } else {
          self.model.GradingMethod = null;
        }
        this.$emit('changed', true);
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
        if (this.idParams) {
          urlApi = EditApi + '/' + this.idParams;
          requestData.data.id = this.idParams;
        }
        requestData.url = urlApi;
        this.$store.commit('isLoading', true);

        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => { //console.log(responses);
          let responsesData = responses.data;
          //console.log(responsesData);
          // copy item
          if (!self.idParams && !_.isEmpty(self.itemCopy)) {
            responsesData.data.data = self.itemCopy.data.data;
          }

          if (responsesData.status === 1) {

            if (self.idParams || !_.isEmpty(self.itemCopy)) {
              if (_.isObject(responsesData.data.data)) {
                self.model.IndicatorID = responsesData.data.data.IndicatorID;
                self.model.ParentID = responsesData.data.data.ParentID;
                self.model.IndicatorName = responsesData.data.data.IndicatorName;
                self.model.IndicatorNo = responsesData.data.data.IndicatorNo;
                self.model.FrequencyValue = responsesData.data.data.FrequencyValue.split(',');
                self.model.ScaleRateID = responsesData.data.data.ScaleRateID;
                self.model.ScaleRateName = responsesData.data.ScaleRateName;
                self.model.GradingMethod = responsesData.data.data.GradingMethod;
                self.model.EvaluationMethod = responsesData.data.data.EvaluationMethod;
                self.model.IndicatorCalMethod = responsesData.data.data.IndicatorCalMethod;
                self.model.UomID = responsesData.data.data.UomID;
                self.model.UomName = responsesData.data.data.UomName;
                self.model.Description = responsesData.data.data.Description;
                self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
                self.model.IndicatorCate = responsesData.data.IndicatorCate;
                }

              if (!_.isEmpty(self.itemCopy)) {
                self.model.IndicatorNo = responsesData.data.auto;
              }
            } else {
              self.model.IndicatorNo = responsesData.data.auto;
            }
            self.BinaryDataOption = [
              {value: null, text: ''}
            ];
            _.forEach(responsesData.data.BinaryData, function (value, key) {
              let tmpObj = {};
              tmpObj.value = value.BinaryDataID;
              tmpObj.text = value.BinaryDataName;
              self.BinaryDataOption.push(tmpObj);
            });
            self.model.ScaleRateOption = [];
            _.forEach(responsesData.data.ScaleRate, function (value, key) {
              let tmpObj = {};
              tmpObj.id = value.ScaleRateID;
              tmpObj.text = value.ScaleRateName;
              self.model.ScaleRateOption.push(tmpObj);
            });
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

              // get uom option setting
              let optionSetting = JSON.parse(localStorage.getItem('OptionSetting'));
              if (optionSetting) {
                let defaultUom = _.find(optionSetting, ['SettingKey', 'GLOBAL_UOM']);
                if (defaultUom) {
                  self.model.UomID = Number(defaultUom.SettingValue);
                  let settingValueMeta = JSON.parse(defaultUom.SettingValueMeta);
                  if (settingValueMeta && settingValueMeta.UomName) {
                    self.model.UomName = settingValueMeta.UomName;
                  }
                }
              }
            }

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
          requestData.data.IndicatorName = this.reqParams.search.IndicatorName;
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
        });

      },

      handleSubmitForm() {
        let self = this;

        if (this.model.UomID) {
          let uom = _.find(this.model.uomOption, ['value', Number(this.model.UomID)]);
          if (uom) {
            this.model.UomName = uom.text;
          }
        }

        const requestData = {
          method: 'post',
          url: StoreApi,
          data: {
            IndicatorNo: this.model.IndicatorNo,
            IndicatorName: this.model.IndicatorName,
            ParentID: this.model.ParentID,
            FrequencyValue: (this.model.FrequencyValue) ? this.model.FrequencyValue.join() : '',
            ScaleRateID: this.model.ScaleRateID,
            ScaleRateName: this.model.ScaleRateName,
            GradingMethod: this.model.GradingMethod,
            EvaluationMethod: this.model.EvaluationMethod,
            Inactive: (this.model.Inactive) ? 1 : 0,
            UomID: this.model.UomID,
            UomName: this.model.UomName,
            Description: this.model.Description,
            IndicatorCalMethod: this.model.IndicatorCalMethod,
            IndicatorCate: this.model.IndicatorCate
          },
        };
        //console.log(this.model.requestData);
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
          Swal.fire({
            title: 'Thông báo',
            icon: 'warning',
            text: 'Phiên làm việc của bạn đã hết hạn. Vui lòng thử lại hoặc đăng nhập lại!',
            confirmButtonText: 'Đóng'
          });
          self.$store.commit('isLoading', false);
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
            ParentID: this.model.ParentID,
            TableNo: 'IndicatorNo',
            TableID: 'IndicatorID',
          },

        };

        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {//console.log(response);
          let dataResponse = response.data;
          this.model.IndicatorNo = dataResponse.data;
          self.$store.commit('isLoading', false);
        }, (error) => {
          self.$store.commit('isLoading', false);
        });
      }

    },
    watch: {
      idParams() {
        this.fetchData();
      },

      'model.taxRate'() {

      },
      'model.ParentID'() {
        // let self = this;
        // let urlApi = this.api;
        // let requestData = {
        //   method: 'post',
        //   url: '/listing/api/common/auto-childtable',
        //   data: {
        //     table: 'task_indicator',
        //     ParentID: this.model.ParentID,
        //     TableNo: 'IndicatorNo',
        //     TableID: 'IndicatorID',
        //   },
        //
        // };
        //
        // this.$store.commit('isLoading', true);
        // ApiService.customRequest(requestData).then((response) => {//console.log(response);
        //   let dataResponse = response.data;
        //   this.model.IndicatorNo = dataResponse.data;
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
  .select2-selection__rendered {
    line-height: 24px !important;
  }
</style>
