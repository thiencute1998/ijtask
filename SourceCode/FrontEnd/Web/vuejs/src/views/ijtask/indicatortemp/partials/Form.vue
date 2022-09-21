<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Mẫu bảng chỉ tiêu ĐGCV<span
                v-if="model.TemplateName ">:</span> {{model.TemplateName }}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Mẫu bảng chỉ tiêu ĐGCV<span
                v-if="model.TemplateName ">:</span> {{model.TemplateName }}</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitForm"><i
                class="fa fa-check-square-o"></i> Lưu
              </b-button>
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onBackToList"><i
                class="fa fa-ban"></i> Hủy
              </b-button>
            </div>
          </b-col>
          <b-col class="col-md-12">
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
              <label class="col-md-4 m-0" for="TemplateName ">Tên</label>
              <div class="col-md-16">
                <input v-model="model.TemplateName " type="text" id="TemplateName " class="form-control"
                       placeholder="Tên mẫu bảng chỉ tiêu " name="TemplateName "/>
              </div>
              <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.TemplateNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0">Loại chỉ tiêu</label>
              <div class="col-md-8">
                <b-form-select
                  v-model="model.IndicatorType"
                  :options="[
                                        {value: null, text: 'Chọn loại chỉ tiêu'},
                                        {value: 1, text: 'Chỉ tiêu đơn vị'},
                                        {value: 2, text: 'Chỉ tiêu cá nhân'},]">
                </b-form-select>
              </div>
<!--              <label class="col-md-4 m-0">Phương pháp đánh giá</label>-->
<!--              <div class="col-md-8">-->
<!--                <b-form-select-->
<!--                  v-model="model.EvaluationMethod"-->
<!--                  :options="[-->
<!--                                        {value: null, text: 'Chọn phương pháp'},-->
<!--                                        {value: 1, text: 'Chỉ số đo lường hiệu suất'},-->
<!--                                        {value: 2, text: 'Mục tiêu và kết quả then chốt'},-->
<!--                                        {value: 3, text: 'Thẻ điểm cân bằng'},-->
<!--                                        {value: 4, text: 'Bảng điểm'},-->
<!--                                        {value: 5, text: 'Danh sách kiểm tra'},-->
<!--                                        {value: 6, text: 'Phản hồi 360 độ'},]">-->
<!--                </b-form-select>-->
<!--              </div>-->
            </div>

            <label>Giá trị loại chỉ tiêu:</label>
            <table class="table b-table table-sm table-bordered table-editable">
              <thead>
              <tr>
                <th scope="col" style="border-bottom: none;" class="text-center">Phương pháp đánh giá</th>
                <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Chỉ tiêu</th>
                <th scope="col" style="width: 8%; border-bottom: none;" class="text-center">Đơn vị tính</th>
                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Loại chấm điểm</th>
                <th scope="col" style="width: 8%; border-bottom: none;" class="text-center">Trọng số</th>
                <th scope="col" style="width: 8%; border-bottom: none;" class="text-center">Tần suất</th>
                <th scope="col" style="width: 15%; border-bottom: none;" class="text-center">Ghi chú</th>

                <th scope="col" :style="StyleAction">
                  <!--<i @click="onAddFieldOnTable" class="fa fa-plus" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i>--></th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(field, key) in model.IndicatorTempItem" v-bind:RowItem="field.TransItemID">
                <td>
                  <b-form-select
                    v-model="model.IndicatorTempItem[key].EvaluationMethod" @change="setStyleAction"
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
                </td>
                <td>
                  <IjcoreModalListing
                    v-model="model.IndicatorTempItem[key]" :title="'chỉ tiêu'"  :api="'/listing/api/common/list'"
                    :FieldName="'IndicatorName'" :FieldNo="'IndicatorNo'" :FieldID="'IndicatorID'" :table="'task_indicator'"
                    :FieldUpdate="['EvaluationMethod', 'GradingMethod', 'ScaleRateID', 'UomID', 'UomName']" :FieldWhere="{'EvaluationMethod' : field.EvaluationMethod}"
                    @changed="checkIndicator('IndicatorTempItem', key)"
                  ></IjcoreModalListing>
                </td>
                <td>
                  <b-form-select v-model="model.IndicatorTempItem[key].UomID" @change="onSelectUom($event, key)"
                                 :options="model.uomOption" id="UomID"></b-form-select>
                </td>
                <td>
                  <b-form-select
                    v-model="model.IndicatorTempItem[key].GradingType"
                    :options="[
                                    {value: null, text: ''},
                                    {value: 1, text: 'Điểm thường'},
                                    {value: 2, text: 'Điểm thưởng'},
                                    {value: 3, text: 'Điểm phạt'},]">
                  </b-form-select>
                </td>
                <td>
                  <b-form-input
                    type="text"
                    v-model="model.IndicatorTempItem[key].Rate"
                    autocomplete="Indicator">
                  </b-form-input>
                </td>
                <td>
                  <b-form-select
                    v-model="model.IndicatorTempItem[key].FrequencyType"
                    :options="[
                                    {value: null, text: ''},
                                    {value: 1, text: 'Năm'},
                                    {value: 2, text: '6 tháng'},
                                    {value: 3, text: 'Quý'},
                                    {value: 4, text: 'Tháng'},
                                    {value: 5, text: 'Tuần'},
                                    {value: 6, text: 'Ngày'},
                                    {value: 7, text: 'Vụ việc'},]">
                  </b-form-select>
                </td>
                <td>
                  <b-form-input
                    type="text"
                    v-model="model.IndicatorTempItem[key].Description"
                    autocomplete="Indicator code list description">
                  </b-form-input>
                </td>
                <!--                                <td class="text-center" v-if="model.EvaluationMethod==2||model.EvaluationMethod==6">-->
                <!--                                  -->
                <!--                                </td>-->

                <td class="text-center" style="vertical-align: middle;text-align: right !important;padding-right: 8px;">
                  <i @click="onAddOnKeyresult(model.IndicatorTempItem[key].TransItemID, key)"
                     class="fa fa-external-link ij-icon " title="Kết quả then chốt"
                     v-if="model.IndicatorTempItem[key].EvaluationMethod==2"
                     style="font-size: 18px; cursor: pointer; padding-right: 5px;"></i>
                  <i @click="onAddOnFeedback(model.IndicatorTempItem[key].TransItemID, key)"
                     class="fa fa-external-link ij-icon " title="Kết quả phản hồi"
                     v-if="model.IndicatorTempItem[key].EvaluationMethod==6"
                     style="font-size: 18px; cursor: pointer; padding-right: 5px;"></i>
                  <i @click="onDeleteFieldOnTable(key)" class="fa fa-trash-o" title="Xóa"
                     style="font-size: 18px; cursor: pointer"></i>
                </td>
              </tr>
              </tbody>
            </table>

            <a @click="onAddFieldOnTable(RowItem)" class="new-row">
              <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
            </a>
          </b-card>
        </div>
      </vue-perfect-scrollbar>
    </div>

    <!-- Popup Add KeyResult -->
    <b-modal ref="KeyResult" id="modal-form-input-task-general" size="xl"
             title="Bảng mẫu chỉ tiêu ĐGCV – Kết quả then chốt ">
      <div class="main-body main-body-view-action">
        <table class="table b-table table-sm table-bordered table-editable">
          <thead>
          <tr>
            <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Chỉ tiêu</th>
            <th scope="col" style="width: 15%; border-bottom: none;" class="text-center">Loại chấm điểm</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Đơn vị tính</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Trọng số</th>
            <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Ghi chú</th>
<!--            <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Ghi chú</th>-->
            <th scope="col" style="width: 3%; border-bottom: none;" class="text-center">
              <!--<i @click="onAddFieldOnTable" class="fa fa-plus" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i>--></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(field, key) in model.IndicatorTempItemKeyresult[TransItemIDCurrent]">
            <td>
              <IjcoreModalListing
                v-model="model.IndicatorTempItemKeyresult[TransItemIDCurrent][key]" :title="'chỉ tiêu'"  :api="'/listing/api/common/list'"
                :FieldName="'IndicatorName'" :FieldNo="'IndicatorNo'" :FieldID="'IndicatorID'" :table="'task_indicator'"
                :FieldUpdate="['EvaluationMethod', 'GradingMethod', 'ScaleRateID', 'UomID', 'UomName']" :FieldWhere="{'EvaluationMethod' : model.IndicatorTempItem[TransItemKeyCurrent]['EvaluationMethod']}"
                @changed="checkIndicatorItem('IndicatorTempItemKeyresult', key)"
              ></IjcoreModalListing>
            </td>
            <td style="position: relative">
              <b-form-select
                v-model="model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].KeyresultType"
                @change="onChangeKeyresultType($event, key, TransItemIDCurrent)"
                :options="[
                                  {value: null, text: ''},
                                  {value: 1, text: 'Khối lượng'},
                                  {value: 2, text: 'Bảng điểm'},
                                  {value: 3, text: 'Nhị phân'},]">
              </b-form-select>
              <IjcoreKeyResultType v-model="model.IndicatorTempItemKeyresult[TransItemIDCurrent][key]"
                                   :title="'Bảng điểm'" :api="'/listing/api/common/list'"
                                   :table="'task_scale_rate'" :FieldID="'ScaleRateID'" :FieldName="'ScaleRateName'"
                                   :FieldNameUpdate="'KeyresultName'"
                                   v-show="model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].KeyresultType == 2"
                                   style="position: absolute; top: 9px; right: 30px;"
                                   :FirstShow="model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].ScaleRate"
              >
              </IjcoreKeyResultType>
              <IjcoreKeyResultType v-model="model.IndicatorTempItemKeyresult[TransItemIDCurrent][key]"
                                   :title="'Nhị phân'" :api="'/listing/api/common/list'"
                                   :table="'sys_binary_data'" :FieldID="'BinaryDataID'" :FieldName="'BinaryDataName'"
                                   :FieldNameUpdate="'KeyresultName'"
                                   v-show="model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].KeyresultType == 3"
                                   style="position: absolute; top: 9px; right: 30px;"
                                   :FirstShow="model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].BinaryData"
              >
              </IjcoreKeyResultType>
            </td>
            <td>
              <b-form-select v-model="model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].UomID"
                             @change="onSelectUom($event, key)"
                             :options="model.uomOption" id="UomID"></b-form-select>
            </td>
            <td>
              <b-form-input
                type="text"
                v-model="model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].Rate"
                autocomplete="Indicator">
              </b-form-input>
            </td>
            <td>
              <b-form-input
                type="text"
                v-model="model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].KeyresultName"
                autocomplete="Indicator">
              </b-form-input>
            </td>
<!--            <td>-->
<!--              <b-form-input-->
<!--                type="text"-->
<!--                v-model="model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].Description"-->
<!--                autocomplete="Indicator code list description">-->
<!--              </b-form-input>-->
<!--            </td>-->
            <td class="text-center">
              <i @click="onDeleteFieldOnKeyresult(key)" class="fa fa-trash-o" title="Xóa"
                 style="font-size: 18px; cursor: pointer"></i>
            </td>
          </tr>
          </tbody>
        </table>
        <a @click="onAddFieldOnKeyresult()" class="new-row">
          <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
        </a>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onSaveForm()">
            Lưu
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="" v-if="isForm">
            Hủy
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideKeyResult()">
            Đóng
          </b-button>
        </div>
      </template>
    </b-modal>

    <!-- Popup Add Feedback -->
    <b-modal ref="Feedback" id="modal-form-input-task-general1" size="xl"
             title="Bảng mẫu chỉ tiêu ĐGCV - kết quả phản hồi">
      <div class="main-body main-body-view-action">
        <table class="table b-table table-sm table-bordered table-editable">
          <thead>
          <tr>
            <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Chỉ tiêu</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Là kết quả</th>
            <th scope="col" style="width: 15%; border-bottom: none;" class="text-center">Kiểu kết quả</th>
            <th scope="col" style="width: 36%; border-bottom: none;" class="text-center">Ghi chú</th>
<!--            <th scope="col" style="border-bottom: none;" class="text-center">Ghi chú</th>-->
            <th scope="col" style="width: 3%; border-bottom: none;" class="text-center">
              <!--<i @click="onAddFieldOnTable" class="fa fa-plus" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i>--></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(field, key) in model.IndicatorTempItemFeedback[TransItemIDCurrent]">
            <td>
              <IjcoreModalListing
                v-model="model.IndicatorTempItemFeedback[TransItemIDCurrent][key]" :title="'chỉ tiêu'"  :api="'/listing/api/common/list'"
                :FieldName="'IndicatorName'" :FieldNo="'IndicatorNo'" :FieldID="'IndicatorID'" :table="'task_indicator'"
                :FieldUpdate="['EvaluationMethod', 'GradingMethod', 'ScaleRateID']" :FieldWhere="{'EvaluationMethod' : model.IndicatorTempItem[TransItemKeyCurrent]['EvaluationMethod']}"
                @changed="checkIndicatorItem('IndicatorTempItemFeedback', key)"
              ></IjcoreModalListing>
            </td>
            <td class="text-center">
              <b-form-checkbox
                type="check"
                v-model="model.IndicatorTempItemFeedback[TransItemIDCurrent][key].IsBinaryData==1"
                @change="checktoBinaryDataID(key,TransItemIDCurrent)"
                autocomplete="Indicator">
              </b-form-checkbox>
            </td>
            <td>
              <b-form-select
                v-if="model.IndicatorTempItemFeedback[TransItemIDCurrent][key].IsBinaryData==1"
                @change="clickcheckBinaryData($event, key, TransItemIDCurrent)"
                v-model="model.IndicatorTempItemFeedback[TransItemIDCurrent][key].BinaryDataID"
                :options="model.BinaryDataOption" id="BinaryDataID"></b-form-select>
            </td>
            <td>
              <b-form-input
                type="text"
                v-model="model.IndicatorTempItemFeedback[TransItemIDCurrent][key].FeedbackName"
                autocomplete="Indicator">
              </b-form-input>
            </td>
<!--            <td>-->
<!--              <b-form-input-->
<!--                type="text"-->
<!--                v-model="model.IndicatorTempItemFeedback[TransItemIDCurrent][key].Description"-->
<!--                autocomplete="Indicator code list description">-->
<!--              </b-form-input>-->
<!--            </td>-->
            <td class="text-center">
              <i @click="onDeleteFieldOnFeedback(key)" class="fa fa-trash-o" title="Xóa"
                 style="font-size: 18px; cursor: pointer"></i>
            </td>
          </tr>
          </tbody>
        </table>
        <a @click="onAddFieldOnFeedback()" class="new-row">
          <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
        </a>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onSaveForm6()">
            Lưu
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="" v-if="isForm">
            Hủy
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideFeedback()">
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
  import vSelect from 'vue-select';
  import MaskedInput from 'vue-text-mask';
  import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
  import Select2 from 'v-select2-component';
  import moment from 'moment';
  import IjcoreKeyResultType from "../../../../components/IjcoreModalKeyResultType";
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";

  moment.locale('vi');

  const ListRouter = 'task-indicator-temp';
  const EditRouter = 'task-indicator-temp-edit';
  const CreateRouter = 'task-indicator-temp-create';
  const ViewRouter = 'task-indicator-temp-view';
  const DetailApi = 'task/api/indicator-temp/view';
  const EditApi = 'task/api/indicator-temp/edit';
  const CreateApi = 'task/api/indicator-temp/create';
  const StoreApi = 'task/api/indicator-temp/store';
  const UpdateApi = 'task/api/indicator-temp/update';
  const ListApi = 'task/api/indicator-temp';

  export default {
    name: 'task-indicator-detail',
    data() {
      return {
        StyleAction: 'width: 32px; border-bottom: none;',
        isForm: false,
        idParams: this.idParamsProps,
        reqParams: this.reqParamsProps,
        TransItemIDCurrent: -1,
        TransItemKeyCurrent: -1,
        RowItem: 0,
        model: {
          TemplateName: '',
          TemplateNo: '',
          IndicatorName: '',
          IndicatorType: null,
          //EvaluationMethod: null,
          IndicatorTempItem: [],
          IndicatorTempItemKeyresult: [],
          IndicatorTempItemFeedback: [],
          maxLineID: 0,
          maxLineID2: 0,
          maxLineID6: 0,
          UomID: null,
          UomName: '',
          FrequencyType: null,
          GradingType: null,
          Rate: '',
          Description: '',
          Uom: null,
          uomOption: [],
          BinaryData: null,
          BinaryDataName: '',
          BinaryDataOption: [],
          indicatorOption: [],
        },
        stage: {
          updatedData: false,
          countConstraint: 0
        },
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
      IjcoreKeyResultType,
      Select2,
      MaskedInput,
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
      },

    },
    methods: {
      checkIndicator(KeyModel, Key){
        let k = 0;
        for (let i = 0; i < this.model[KeyModel].length; i++) {
          if(this.model[KeyModel][i].IndicatorID == this.model[KeyModel][Key].IndicatorID){
            k++;
            if(k==2){
              Swal.fire(
                'Thông báo',
                'Đã tồn tại chỉ tiêu đánh giá này!',
                'error'
              );
              this.model[KeyModel][Key].IndicatorID = '';
              this.model[KeyModel][Key].IndicatorNo = '';
              this.model[KeyModel][Key].IndicatorName = '';
              this.$forceUpdate()
              return true;
            }
          }
        }
      },
      checkIndicatorItem(KeyModel, Key){
        let k = 0;
        for (let i = 0; i < this.model[KeyModel][this.TransItemIDCurrent].length; i++) {
          //console.log()
          if(this.model[KeyModel][this.TransItemIDCurrent][i].IndicatorID == this.model[KeyModel][this.TransItemIDCurrent][Key].IndicatorID){
            k++;
            if(k==2){
              Swal.fire(
                'Thông báo',
                'Đã tồn tại chỉ tiêu đánh giá này!',
                'error'
              );
              this.model[KeyModel][this.TransItemIDCurrent][Key].IndicatorID = '';
              this.model[KeyModel][this.TransItemIDCurrent][Key].IndicatorNo = '';
              this.model[KeyModel][this.TransItemIDCurrent][Key].IndicatorName = '';
              this.$forceUpdate()
              return true;
            }
          }
        }
      },
      setStyleAction(){
        for (let i = 0; i < this.model.IndicatorTempItem.length; i++) {
          if(this.model.IndicatorTempItem[i].EvaluationMethod == 2 || this.model.IndicatorTempItem[i].EvaluationMethod == 6){
            this.StyleAction = "width: 60px; border-bottom: none;";
            return true;
          }
        }
        this.StyleAction = 'width: 32px; border-bottom: none;';
        return true;
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
        ApiService.customRequest(requestData).then((responses) => {

          let responsesData = responses.data;
          // copy item
          if (!self.idParams && !_.isEmpty(self.itemCopy)) {
            responsesData = self.itemCopy;
          }
          if (responsesData.status === 1) {
            if (self.idParams || !_.isEmpty(self.itemCopy)) {
              if (_.isObject(responsesData.data.data)) {
                self.model.TemplateName = responsesData.data.data.TemplateName;
                self.model.TemplateNo = responsesData.data.data.TemplateNo;
                self.model.IndicatorType = responsesData.data.data.IndicatorType;
                //self.model.EvaluationMethod = responsesData.data.data.EvaluationMethod;
                self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;

                _.forEach(responsesData.data.IndicatorTempItemKeyResult, function (field, key) {
                  if (self.model.IndicatorTempItemKeyresult[field.TransItemID] == undefined) {
                    field.LineIDTemp = field.TransItemID;
                    self.model.IndicatorTempItemKeyresult[field.TransItemID] = [];
                  }
                  self.model.IndicatorTempItemKeyresult[field.TransItemID].push(field)
                });

                _.forEach(responsesData.data.IndicatorTempItemFeedback, function (field, key) {
                  if (self.model.IndicatorTempItemFeedback[field.TransItemID] == undefined) {
                    field.LineIDTemp = field.TransItemID;
                    self.model.IndicatorTempItemFeedback[field.TransItemID] = [];
                  }
                  self.model.IndicatorTempItemFeedback[field.TransItemID].push(field)
                });

                self.model.IndicatorTemp = responsesData.data.IndicatorTemp;
                self.model.IndicatorTempItem = responsesData.data.IndicatorTempItem;

                // set max lineID
                // _.forEach(self.model.IndicatorTempItem, function (field, key) {
                //   if (Number(field.LineID) > self.model.maxLineID) self.model.maxLineID = Number(field.LineID);
                // });
                //
                // _.forEach(self.model.IndicatorTempItemKeyresult, function (field, key) {
                //     if (Number(field.LineID2) > self.model.maxLineID2) self.model.maxLineID2 = Number(field.LineID2);
                //   });
                // _.forEach(self.model.IndicatorTempItemFeedback, function (Keyresult, key) {
                //     if (Number(Keyresult.LineID6) > self.model.maxLineID6) self.model.maxLineID6 = Number(Keyresult.LineID6);
                //   });

              }
              if (!_.isEmpty(self.itemCopy)) {
                self.model.TemplateNo = responsesData.data.auto;
              }
            } else {
              if (_.isArray(responsesData.data)) {

                self.model.IndicatorTempOption = [];
                _.forEach(responsesData.data, function (value, key) {
                  let tmpObj = {};
                  tmpObj.id = value.TemplateID;
                  tmpObj.text = value.TemplateName;
                  self.model.IndicatorTempOption.push(tmpObj);
                });
              }
              self.model.TemplateNo = responsesData.data.auto;
            }

            if (_.isArray(responsesData.data.IndicatorTemp)) {

              self.model.IndicatorTempOption = [];
              _.forEach(responsesData.data.IndicatorTemp, function (value, key) {
                let tmpObj = {};
                tmpObj.id = value.TemplateID;
                tmpObj.text = value.TemplateName;
                self.model.IndicatorTempOption.push(tmpObj);
              });
            }
            if (_.isArray(responsesData.data.Uom)) {

              self.model.uomOption = [
                {value: null, text: ''}
              ];
              _.forEach(responsesData.data.Uom, function (value, key) {
                let tmpObj = {};
                tmpObj.value = value.UomID;
                tmpObj.text = value.UomName;
                self.model.uomOption.push(tmpObj);
              });
            }
            if (_.isArray(responsesData.data.BinaryData)) {

              self.model.BinaryDataOption = [
                {value: null, text: ''}
              ];
              _.forEach(responsesData.data.BinaryData, function (value, key) {
                let tmpObj = {};
                tmpObj.value = value.BinaryDataID;
                tmpObj.text = value.BinaryDataName;
                self.model.BinaryDataOption.push(tmpObj);
              });
            }


          }
          this.setStyleAction();
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
              self.reqParams.idsArray.push(value.CateID);
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
      onSelectUom(value, key) {
        let uom = _.find(this.model.uomOption, ['value', value]);
        //this.model.IndicatorTempItem[key].UomName = uom.text;
      },
      checktoBinaryDataID(key, TransItemIDCurrent) {
        //this.$set(this.model.IndicatorTempItemFeedback, TransItemIDCurrent, this.model.IndicatorTempItemFeedback[TransItemIDCurrent]);
        if(this.model.IndicatorTempItemFeedback[TransItemIDCurrent][key].IsBinaryData == 1){
          this.model.IndicatorTempItemFeedback[TransItemIDCurrent][key].IsBinaryData = 0;
        }else{
          this.model.IndicatorTempItemFeedback[TransItemIDCurrent][key].IsBinaryData = 1;
        }
        this.model.IndicatorTempItemFeedback[TransItemIDCurrent][key].BinaryDataID = '';
        this.$forceUpdate()
      },
      clickcheckBinaryData(value, key, TransItemIDCurrent) {
        let BinaryData = _.find(this.model.BinaryDataOption, ['value', value]);
        this.model.IndicatorTempItemFeedback[TransItemIDCurrent][key].FeedbackName = BinaryData.text;
        this.$forceUpdate()
      },
      onChangeKeyresultType(value, key, TransItemIDCurrent) {
        this.$set(this.model.IndicatorTempItemKeyresult, TransItemIDCurrent, this.model.IndicatorTempItemKeyresult[TransItemIDCurrent]);
        this.model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].ScaleRate = 0;
        this.model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].BinaryData = 0;
        if(this.model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].KeyresultType == 2){
          this.model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].ScaleRate = 1;
          this.model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].BinaryData = 0;
        }
        if(this.model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].KeyresultType == 3){
          this.model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].BinaryData = 1;
          this.model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].ScaleRate = 0;
        }
        if(this.model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].KeyresultType == 1 || this.model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].KeyresultType == null) {
          this.model.IndicatorTempItemKeyresult[TransItemIDCurrent][key].KeyresultName = '';
        }
        this.$forceUpdate()
      },
      onAddFieldOnTable(RowItem) {
        let fieldObj = {};
        this.model.maxLineID += 1;
        fieldObj.TransItemID = RowItem;
        fieldObj.TemplateID = '';
        fieldObj.IndicatorNo = '';
        fieldObj.IndicatorName = null;
        fieldObj.Description = null;
        fieldObj.ParentID = 1;
        fieldObj.Level = 1;
        fieldObj.Detail = 1;
        fieldObj.UomID = null;
        fieldObj.UomName = '';
        fieldObj.GradingType = null;
        fieldObj.FrequencyType = null;
        fieldObj.Rate = null;
        fieldObj.Prefix = 1;
        fieldObj.Suffix = 1;
        fieldObj.NumberValue = 1;
        fieldObj.LineIDTemp = RowItem;
        this.RowItem = RowItem + 1;
        this.model.IndicatorTempItem.push(fieldObj);
        this.$forceUpdate();
      },
      onAddFieldOnKeyresult() {
        let fieldObj = {};
        //this.model.maxLineID2 += 1;
        //fieldObj.LineID2 = this.model.maxLineID2;
        fieldObj.ScaleRateID = '';
        fieldObj.BinaryDataID = '';
        fieldObj.Description = '';
        fieldObj.KeyresultType = '';
        fieldObj.KeyresultName = '';
        fieldObj.UomID = null;
        fieldObj.UomName = null;
        fieldObj.Rate = ''
        fieldObj.IndicatorID = '';
        fieldObj.IndicatorNo = '';
        fieldObj.IndicatorName = '';
        fieldObj.EvaluationMethod = this.model.IndicatorTempItem[this.TransItemKeyCurrent].EvaluationMethod;
        fieldObj.LineIDTemp = this.TransItemIDCurrent;

        if (this.model.IndicatorTempItemKeyresult[this.TransItemIDCurrent] == undefined) {
          this.model.IndicatorTempItemKeyresult[this.TransItemIDCurrent] = [];
        }
        this.model.IndicatorTempItemKeyresult[this.TransItemIDCurrent].push(fieldObj);
        this.$forceUpdate();
      },
      onAddFieldOnFeedback() {
        let fieldObj = {};
        //this.model.maxLineID6 += 1;
        //fieldObj.LineID6 = this.model.maxLineID6;
        fieldObj.ScaleRateID = '';
        fieldObj.BinaryDataID = '';
        fieldObj.Description = '';
        fieldObj.FeedbackName = '';
        fieldObj.IsBinaryData = '';
        fieldObj.IndicatorID = '';
        fieldObj.IndicatorNo = '';
        fieldObj.IndicatorName = '';
        fieldObj.EvaluationMethod = this.model.IndicatorTempItem[this.TransItemKeyCurrent].EvaluationMethod;
        fieldObj.LineIDTemp = this.TransItemIDCurrent;

        if (this.model.IndicatorTempItemFeedback[this.TransItemIDCurrent] == undefined) {
          this.model.IndicatorTempItemFeedback[this.TransItemIDCurrent] = [];
        }
        this.model.IndicatorTempItemFeedback[this.TransItemIDCurrent].push(fieldObj);
        //console.log(this.TransItemIDCurrent)
        this.$forceUpdate();
      },
      onDeleteFieldOnTable(key) {

        // remove field in fieldOnTableReq
        // let fieldExist = _.find(this.model.IndicatorTempItem, ['LineID', field.LineID]);
        // if (_.isObject(fieldExist)) {
        //   _.remove(this.model.IndicatorTempItem, ['LineID', field.LineID]);
        // }
        if (this.model.EvaluationMethod[key] == 2) {
          this.model.IndicatorTempItemKeyresult.splice(this.model.IndicatorTempItem[key].LineIDTemp, 1)
        }
        if (this.model.EvaluationMethod[key] == 6) {
          this.model.IndicatorTempItemFeedback.splice(this.model.IndicatorTempItem[key].LineIDTemp, 1)
        }
        this.model.IndicatorTempItem.splice(key, 1)
        //
        this.setStyleAction();
        this.$forceUpdate();
      },
      onDeleteFieldOnKeyresult(key) {
        // remove field in fieldOnTableReq
        this.model.IndicatorTempItemKeyresult[this.TransItemIDCurrent].splice(key, 1)
        this.$forceUpdate();
      },
      onDeleteFieldOnFeedback(key) {
        // remove field in fieldOnTableReq
        this.model.IndicatorTempItemFeedback[this.TransItemIDCurrent].splice(key, 1)
        this.$forceUpdate();
      },
      handleSubmitForm() {
        let self = this;
        const requestData = {
          method: 'post',
          url: StoreApi,
          data: {
            master: {
              TemplateNo: this.model.TemplateNo,
              TemplateName: this.model.TemplateName,
              IndicatorType: this.model.IndicatorType,
              Inactive: (this.model.Inactive) ? 1 : 0,
            },
            detail: this.model.IndicatorTempItem,
            IndicatorTempItemKeyresult: this.model.IndicatorTempItemKeyresult,
            IndicatorTempItemFeedback: this.model.IndicatorTempItemFeedback
          }
        };
        //console.log('kkkkkkkkkk');
        //console.log(this.model.IndicatorTempItemKeyresult);

        // edit user
        if (this.idParams) {
          requestData.data.master.TemplateID = this.idParams;
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
      onAddOnKeyresult(TransItemID, key) {
        let self = this.key;
        this.TransItemIDCurrent = TransItemID;
        this.TransItemKeyCurrent = key;
        this.$forceUpdate();
        this.$refs['KeyResult'].show();
      },
      onHideKeyResult() {
        this.isForm = false;
        this.$refs['KeyResult'].hide();
      },
      onAddOnFeedback(TransItemID, key) {
        let self = this.key;
        this.TransItemIDCurrent = TransItemID;
        this.TransItemKeyCurrent = key;
        this.$forceUpdate();
        this.$refs['Feedback'].show();
      },
      onHideFeedback() {
        this.isForm = false;
        this.$refs['Feedback'].hide();
      },
      onSaveForm() {

        this.$bvToast.toast('Đã lưu kết quả then chốt\n', {
          title: 'Thông báo',
          variant: 'success',
          solid: true
        });
        this.$refs['KeyResult'].hide();
      },
      onSaveForm6() {

        this.$bvToast.toast('Đã lưu kết quả phản hồi\n', {
          title: 'Thông báo',
          variant: 'success',
          solid: true
        });
        this.$refs['Feedback'].hide();
      },
      autoCorrectedDatePipe: () => {
        return createAutoCorrectedDatePipe('dd/mm/yyyy')
      },
      autoCorrectedDateTimePipe: () => {
        return createAutoCorrectedDatePipe('dd/mm/yyyy hh:mm')
      },
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

  #modal-form-input-task-general .modal-lg .modal-content {
    width: 1024px;
    margin: auto;
  }

  @media (max-width: 1024px) {
    #modal-form-input-task-general .modal-lg {
      max-width: 100%;
    }

    #modal-form-input-task-general .modal-lg .modal-content {
      width: 90%;
      margin: auto;
    }
  }

  @media (min-width: 992px) {
    #modal-form-input-task-general .modal-lg {
      max-width: 100%;
    }
  }

  #modal-form-input-task-general1 .modal-lg .modal-content {
    width: 1024px;
    margin: auto;
  }

  @media (max-width: 1024px) {
    #modal-form-input-task-general1 .modal-lg {
      max-width: 100%;
    }

    #modal-form-input-task-general1 .modal-lg .modal-content {
      width: 90%;
      margin: auto;
    }
  }

  @media (min-width: 992px) {
    #modal-form-input-task-general1 .modal-lg {
      max-width: 100%;
    }
  }
</style>
