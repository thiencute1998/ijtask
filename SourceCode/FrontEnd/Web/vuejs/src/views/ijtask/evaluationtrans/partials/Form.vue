<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Phiếu ĐGCV<span
                v-if="model.TransName ">:</span> {{model.TransName }}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Phiếu ĐGCV<span
                v-if="model.TransName ">:</span> {{model.TransName }}</span>
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
              <label class="col-md-4 m-0">Loại chỉ tiêu</label>
              <div class="col-md-6">
                <b-form-select
                  @change="onChangeTransType($event)"
                  v-model="model.TransType"
                  :options="[
                    {value: 1, text: 'Chỉ tiêu đơn vị'},
                    {value: 2, text: 'Chỉ tiêu cá nhân'},]">
                </b-form-select>
              </div>
              <label class="col-md-2 m-0" v-show="model.TransType == 1">Đơn vị</label>
              <div class="col-md-12" v-show="model.TransType == 1">
                <IjcoreModalListing
                  v-model="model" :title="'đơn vị'" :api="'/listing/api/common/list'"
                  :table="'company'" :FieldID="'CompanyID'" :FieldName="'CompanyName'"
                  @changed="handleGetTemplate"
                  :FieldNo="'CompanyNo'">
                </IjcoreModalListing>
              </div>
              <label class="col-md-2 m-0" v-show="model.TransType == 2">Nhân viên</label>
              <div class="col-md-8" v-show="model.TransType == 2">
                <IjcoreModalListing
                  v-model="model" :title="'nhân viên'" :api="'/listing/api/common/list'"
                  :table="'employee'" :FieldID="'EmployeeID'" :FieldName="'EmployeeName'"
                  @changed="handleGetTemplate"
                  :FieldNo="'EmployeeNo'">
                </IjcoreModalListing>
              </div>
              <div class="col-md-4 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.TransNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0">Chu kỳ</label>
              <div class="col-md-2">
                <b-form-select
                  v-model="model.PeriodType"
                  @change="setPeriodType"
                  :options="[
                    {value: '', text: '-- Chọn chu kỳ --', disabled: true},
                    {value: 1, text: 'Năm'},
                    {value: 2, text: '6 tháng'},
                    {value: 3, text: 'Quý'},
                    {value: 4, text: 'Tháng'},
                    {value: 5, text: 'Tuần'},
                    {value: 6, text: 'Ngày'}]">
                </b-form-select>
              </div>
              <label class="col-md-2 m-0">Từ ngày</label>
              <div class="col-md-4 period-from-date">
                <ijcore-date-picker v-model="model.FromDate" style="width: 100%;">
                </ijcore-date-picker>
              </div>
              <div class="col-md-2 m-0">Đến ngày</div>
              <div class="col-md-4 period-to-date">
                <ijcore-date-picker v-model="model.ToDate" style="width: 100%;">
                </ijcore-date-picker>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0" for="TransName">Tên</label>
              <div class="col-md-20">
                <input v-model="model.TransName" type="text" id="TransName" class="form-control"
                       placeholder="Tên phiếu" name="TransName"/>
              </div>
            </div>

            <label>Giá trị loại chỉ tiêu:</label>
            <div class="div-scroll-table">
            <table class="table b-table table-sm table-bordered table-editable" style="width: 2000px;">
              <thead>
              <tr>
                <th scope="col" style="width: 50px; border-bottom: none;" class="text-center"><i title="Công việc" class="fa fa-building-o"></i></th>
                <th scope="col" style="border-bottom: none;" class="text-center">Phương pháp đánh giá</th>
                <th scope="col" style="border-bottom: none; width:250px;" class="text-center">Chỉ tiêu</th>
                <th scope="col" style="border-bottom: none;" class="text-center" >ĐVT</th>
                <th scope="col" style="border-bottom: none; width:120px;" class="text-center" >Loại chấm điểm</th>
                <th scope="col" style="border-bottom: none;" class="text-center" >Trọng số</th>
                <th scope="col" style="border-bottom: none;" class="text-center" >Chỉ số mục tiêu</th>
                <th scope="col" style="border-bottom: none;" class="text-center" >Chỉ số thực tế</th>
                <th scope="col" style="border-bottom: none;" class="text-center" >% HT</th>
                <th scope="col" style="border-bottom: none;" class="text-center" >{{EmployeeLogin.EmployeeName}}</th>
                <th scope="col" style="border-bottom: none;" class="text-center" v-for="(item, key) in TaskEvaluatorEmployee" v-if="item.EvaluatorID != EmployeeLogin.EmployeeID">{{item.EvaluatorName}}</th>
                <th scope="col" style="width: 80px;border-bottom: none;"></th>
                <th scope="col" class="text-center td-action-fix-right-form" style="height: 29px;"></th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(field, key) in model.EvaluationTransItem" v-bind:RowItem="field.TransItemID">
                <td class="text-center">
                    <i class="fa fa-building-o" @click="onViewTask(key)" style="cursor: pointer" title="Xem công việc"></i>
                </td>
                <td>
                  <b-form-select
                    v-model="model.EvaluationTransItem[key].EvaluationMethod"
                    @change="changeEvaluationMethod(key)"
                    :options="[
                      {value: 'null', text: '-- Chọn phương pháp đánh giá --'},
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
                    v-model="model.EvaluationTransItem[key]" :title="'chỉ tiêu'"  :api="'/listing/api/common/list'"
                    :FieldName="'IndicatorName'" :FieldNo="'IndicatorNo'" :FieldID="'IndicatorID'" :table="'task_indicator'"
                    :FieldUpdate="['EvaluationMethod', 'GradingMethod', 'ScaleRateID', 'UomName', 'UomID']"
                    :FieldWhere="(model.EvaluationTransItem[key].EvaluationMethod && model.EvaluationTransItem[key].EvaluationMethod !== 'null') ? {EvaluationMethod : model.EvaluationTransItem[key].EvaluationMethod} : {}"
                    @changed="selectedIndicator(key)"
                  ></IjcoreModalListing>
                </td>
                <td class="has-padding">
                  {{model.EvaluationTransItem[key].UomName}}
                </td>
                <td>
                  <b-form-select
                    v-model="model.EvaluationTransItem[key].GradingType"
                    :options="[
                      {value: null, text: '-- Chọn loại chấm điểm --', disabled: true},
                      {value: 1, text: 'Điểm thường'},
                      {value: 2, text: 'Điểm thưởng'},
                      {value: 3, text: 'Điểm phạt'},]">
                  </b-form-select>
                </td>
                <td style="text-align: right">
                  <ijcore-number
                    @changed="validateValuation('EvaluationTrans', key, 'ObjectiveRate')"
                    v-model="model.EvaluationTransItem[key].ObjectiveRate"></ijcore-number>
                </td>
                <td v-if="model.EvaluationTransItem[key].EvaluationMethod != 2">
                  <ijcore-number
                    @changed="autoActualRate(key)"
                    v-model="model.EvaluationTransItem[key].ObjectiveIndex">
                  </ijcore-number>
                </td>
                <td v-else class="text-right" style="padding-right: 0.5rem">
                  {{model.EvaluationTransItem[key].ObjectiveIndex|convertNumberToText}}
                </td>
                <td v-if="model.EvaluationTransItem[key].EvaluationMethod != 2">
                  <ijcore-number
                    @changed="autoActualRate(key)"
                    v-model="model.EvaluationTransItem[key].ActualIndex">
                  </ijcore-number>
                </td>
                <td v-else class="text-right" style="padding-right: 0.5rem">
                  {{model.EvaluationTransItem[key].ActualIndex|convertNumberToText}}
                </td>
                <td @click="onAddOnKeyResult(model.EvaluationTransItem[key].TransItemID, key)">
                  <ijcore-number
                    v-model="model.EvaluationTransItem[key].ActualRate">
                  </ijcore-number>
<!--                  <i @click="onAddOnKeyResult(model.EvaluationTransItem[key].TransItemID, key)"-->
<!--                     class="fa fa-external-link ij-icon " title="Kết quả then chốt"-->
<!--                     v-if="model.EvaluationTransItem[key].EvaluationMethod == 2"-->
<!--                     style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); cursor: pointer"></i>-->
                </td>

                <td scope="col" style="border-bottom: none;" class="text-center" >
                  <ijcore-number @changed="updateActualIndexParent(key, field.IndicatorID)" v-if="TaskEvaluationEmployeeArr[field.IndicatorID +'_'+ EmployeeLogin.EmployeeID] != undefined"
                    v-model="TaskEvaluationEmployeeArr[field.IndicatorID +'_'+ EmployeeLogin.EmployeeID].ActualIndex">
                  </ijcore-number>
                </td>
                <td scope="col" style="border-bottom: none;padding-right: 0.3rem" class="text-right" v-for="(item, key1) in TaskEvaluatorEmployee" v-if="item.EvaluatorID != EmployeeLogin.EmployeeID">
                  {{TaskEvaluationEmployeeArr[field.IndicatorID +'_'+ item.EvaluatorID]?TaskEvaluationEmployeeArr[field.IndicatorID +'_'+ item.EvaluatorID].ActualIndex:""|convertNumberToText}}
                </td>
                <th scope="col" style="width: 5em;"></th>
                <th scope="col" class="text-center td-action-fix-right-form">
                  <i @click="onAddOnKeyResult(model.EvaluationTransItem[key].TransItemID, key)"
                     class="fa fa-external-link ij-icon " title="Kết quả then chốt"
                     v-if="model.EvaluationTransItem[key].EvaluationMethod == 2"
                     style="font-size: 18px; cursor: pointer; padding-right: 5px;position: relative; top: 1px;"></i>
                  <i @click="onAddOnFeedback(model.EvaluationTransItem[key].TransItemID, key)"
                     class="fa fa-external-link ij-icon " title="Kết quả phản hồi"
                     v-if="model.EvaluationTransItem[key].EvaluationMethod == 6"
                     style="font-size: 18px; cursor: pointer; padding-right: 5px;position: relative; top: 1px;"></i>
                  <i @click="onDeleteFieldOnTrans(key)" class="fa fa-trash-o" title="Xóa"
                     style="font-size: 18px; cursor: pointer"></i>
                </th>
              </tr>
              </tbody>
            </table>
            </div>
            <a @click="onAddFieldOnTrans(RowItem)" class="new-row">
              <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
            </a>

            <div class="right" style="position: absolute; right: 8px;bottom: 8px;">
              <b-button variant="primary" size="md" class="float-left mr-2" @click="updateDataEvaluation()">
                <i class="fa fa-calculator"></i>
              </b-button>
            </div>
          </b-card>
        </div>
      </vue-perfect-scrollbar>
    </div>
    <!-- Popup Add KeyResult -->
    <b-modal ref="KeyResult" id="modal-key-result" size="xl"
             title="Phiếu chỉ tiêu ĐGCV – Kết quả then chốt ">
      <div class="main-body main-body-view-action">
        <table class="table b-table table-sm table-bordered table-editable">
          <thead>
          <tr>
            <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Chỉ tiêu</th>
            <th scope="col" style="width: 15%; border-bottom: none;" class="text-center">Loại chấm điểm</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">ĐVT</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Trọng số</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Chỉ số mục tiêu</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Kết quả thực tế</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">% hoàn thành</th>
            <th scope="col" style="width: 3%; border-bottom: none;" class="text-center"></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(field, key) in model.EvaluationTransItemKeyresult[TransItemIDCurrent]">
            <td>
              <IjcoreModalListing
                v-model="model.EvaluationTransItemKeyresult[TransItemIDCurrent][key]" :title="'chỉ tiêu'"
                :api="'/listing/api/common/list'"
                :FieldName="'IndicatorName'" :FieldNo="'IndicatorNo'" :FieldID="'IndicatorID'" :table="'task_indicator'"
                :FieldUpdate="['EvaluationMethod', 'GradingMethod', 'ScaleRateID', 'UomID', 'UomName']"
                :FieldWhere="{EvaluationMethod : model.EvaluationTransItem[TransItemKeyCurrent]['EvaluationMethod']}"
                @changed="selectedIndicatorItem('EvaluationTransItemKeyresult', key)"
              ></IjcoreModalListing>
            </td>
            <td style="position: relative">
              <b-form-select
                v-model="model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].KeyresultType"
                @change="onChangeKeyresultType($event, key, TransItemIDCurrent)"
                :options="[
                  {value: null, text: '-- Chọn loại chấm điểm --', disabled: true},
                  {value: 1, text: 'Khối lượng'},
                  {value: 2, text: 'Bảng điểm'},
                  {value: 3, text: 'Nhị phân'},]">
              </b-form-select>
              <IjcoreKeyResultType
                v-model="model.EvaluationTransItemKeyresult[TransItemIDCurrent][key]"
                title="Bảng điểm" api="/listing/api/common/list"
                table="task_scale_rate" FieldID="ScaleRateID" FieldName="ScaleRateName"
                table-detail="task_scale_rate_item"
                v-show="model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].KeyresultType == 2"
                style="position: absolute; top: 9px; right: 30px;"
                @changed="onChangeKeyResultScaleRate($event, key)"
                :FirstShow="model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].UomName">
              </IjcoreKeyResultType>
              <IjcoreKeyResultType
                v-model="model.EvaluationTransItemKeyresult[TransItemIDCurrent][key]"
                title="Nhị phân" api="/listing/api/common/list"
                table="sys_binary_data" FieldID="BinaryDataID" FieldName="BinaryDataName"
                :FieldUpdate="['BinaryData1', 'BinaryData0']"
                v-show="model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].KeyresultType == 3"
                style="position: absolute; top: 9px; right: 30px;"
                :FirstShow="model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].BinaryData">
              </IjcoreKeyResultType>
            </td>
            <td class="has-padding">
              {{model.Uom[model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].UomID]}}
            </td>
            <td>
              <ijcore-number
                v-model="model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].Rate"
                @changed="validateValuation('EvaluationTransItemKeyresult', key, 'Rate')"></ijcore-number>
            </td>
            <td>
              <ijcore-number
                v-model="model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].ObjectiveIndex"
                @changed="validateValuation('EvaluationTransItemKeyresult', key, 'ObjectiveIndex')"></ijcore-number>
            </td>
            <td style="position: relative">
              <ijcore-number
                v-model="model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].ActualIndex"
                @changed="validateValuation('EvaluationTransItemKeyresult', key, 'ActualIndex')"></ijcore-number>
              <i v-if="model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].KeyresultType && model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].KeyresultType !== 1"
                 @click="onClickKeyresultActualIndex(key, TransItemIDCurrent)"
                 class="fa fa-external-link" style="cursor: pointer; position: absolute; left: 10px; top: 50%; transform: translateY(-50%);"></i>
            </td>
            <td>
              <ijcore-number v-model="model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].ActualRate"></ijcore-number>
            </td>
            <td class="text-center">
              <i @click="onDeleteFieldOnKeyResult(key)" class="fa fa-trash-o" title="Xóa"
                 style="font-size: 18px; cursor: pointer"></i>
            </td>
          </tr>
          </tbody>
        </table>
        <a @click="onAddFieldOnKeyResult()" class="new-row">
          <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
        </a>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onSaveKeyResult()">
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

    <!-- Popup Key Result Scale Rate Item -->
    <b-modal
      ref="key-result-scale-rate-item"
      id="key-result-scale-rate-item"
      size="sm"
      ok-only
      body-class="py-3"
      ok-title="Đóng"
      title="Bảng điểm">
      <div class="main-body">
        <table class="table b-table table-sm mb-0">
          <tbody>
          <tr v-for="(field, key) in KeyResultScaleRateItem" style="cursor: pointer" @click="onClickKeyResultScaleRateItem(field)">
            <td>{{field.LevelInt}}</td>
          </tr>
          </tbody>
        </table>
      </div>
    </b-modal>

    <!-- Popup Key Result Binary Item -->
    <b-modal
      ref="key-result-binary-item"
      id="key-result-binary-item"
      size="sm"
      ok-only
      ok-title="Đóng"
      body-class="py-3"
      title="Nhị phân">
      <div class="main-body">
        <table class="table b-table table-sm mb-0">
          <tbody>
          <tr v-for="(binary, key) in KeyResultBinaryItem" style="cursor: pointer" @click="onClickKeyResultBinaryItem(binary)">
            <td>{{binary.text}}</td>
          </tr>
          </tbody>
        </table>
      </div>
    </b-modal>

    <!-- Popup Add Feedback -->
    <b-modal ref="Feedback" id="modal-feedback" size="xl"
             body-class=""
             title="Phiếu chỉ tiêu ĐGCV - kết quả phản hồi">
      <div class="main-body main-body-view-action">
        <table class="table b-table table-sm table-bordered table-editable">
          <thead>
          <tr>
            <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Chỉ tiêu</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Ngày</th>
            <th scope="col" style="width: 15%; border-bottom: none;" class="text-center">Người phản hồi</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Là kết quả</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Kiểu kết quả</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Giá trị</th>
            <th scope="col" style="width: 22%; border-bottom: none;" class="text-center">Nội dung phản hồi</th>
            <th scope="col" style="width: 3%; border-bottom: none;" class="text-center"></th>
          </tr>
          </thead>
          <tbody>
          <tr
            :class="[(model.EvaluationTransItemFeedback[TransItemIDCurrent][key].UserID !== currentEmployee.UserID) ? 'app-disable' : '']"
            v-for="(field, key) in model.EvaluationTransItemFeedback[TransItemIDCurrent]">
            <td>
              <IjcoreModalListing
                v-model="model.EvaluationTransItemFeedback[TransItemIDCurrent][key]" :title="'chỉ tiêu'"  :api="'/listing/api/common/list'"
                :FieldName="'IndicatorName'" :FieldNo="'IndicatorNo'" :FieldID="'IndicatorID'" :table="'task_indicator'"
                :FieldUpdate="['EvaluationMethod', 'GradingMethod', 'ScaleRateID']"
                :FieldWhere="(model.EvaluationTransItem[TransItemKeyCurrent]['EvaluationMethod']) ? {EvaluationMethod : model.EvaluationTransItem[TransItemKeyCurrent]['EvaluationMethod']} : {}"
              ></IjcoreModalListing>
            </td>
            <td style="position: relative">
              <ijcore-date-picker v-model="model.EvaluationTransItemFeedback[TransItemIDCurrent][key].FeedbackDate"></ijcore-date-picker>
            </td>
            <td class="has-padding">
              {{model.EvaluationTransItemFeedback[TransItemIDCurrent][key].UserName}}
            </td>
            <td class="text-center">
              <b-form-checkbox
                type="check"
                v-model="model.EvaluationTransItemFeedback[TransItemIDCurrent][key].IsBinaryData"
                @change="changeIsBinaryData(key)">
              </b-form-checkbox>
            </td>
            <td>
              <b-form-select
                v-if="model.EvaluationTransItemFeedback[TransItemIDCurrent][key].IsBinaryData"
                @change="changeBinaryData($event, key, TransItemIDCurrent)"
                v-model="model.EvaluationTransItemFeedback[TransItemIDCurrent][key].BinaryDataID"
                :options="model.BinaryDataOption"></b-form-select>
            </td>
            <td>
              <b-form-select
                v-if="model.EvaluationTransItemFeedback[TransItemIDCurrent][key].IsBinaryData"
                v-model="model.EvaluationTransItemFeedback[TransItemIDCurrent][key].FeedbackValueInt"
                @change="changeBinaryDataValue(key)"
                :options="model.EvaluationTransItemFeedback[TransItemIDCurrent][key].BinaryDataValueOption"></b-form-select>
            </td>
            <td>
              <b-form-input
                type="text"
                v-model="model.EvaluationTransItemFeedback[TransItemIDCurrent][key].FeedbackContent">
              </b-form-input>
            </td>
            <td class="text-center">
              <i @click="onDeleteFieldOnFeedback(key)" class="fa fa-trash-o" title="Xóa"
                 v-if="model.EvaluationTransItemFeedback[TransItemIDCurrent][key].UserID === currentEmployee.UserID"
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
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onSaveFeedback()">
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

    <!-- Popup View task.IndicatorID -->
    <b-modal ref="Task" id="modal-task-indicator" size="xl"
             title="Danh sách công việc">
      <div class="main-body main-body-view-action">
        <table class="table b-table table-sm table-bordered">
          <thead>
          <tr>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Mã số</th>
            <th scope="col" style="width: 60%; border-bottom: none;" class="text-center">Tên</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">ĐVT</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Khối lượng ƯTH</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Khối lượng ĐTH </th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(task, key) in model.TaskIndicator">
            <td class="text-center">
              {{task.TaskNo}}
            </td>
            <td>
              {{task.TaskName}}
            </td>
            <td>
              {{model.Uom[task.UomID]}}
            </td>
            <td class="text-right">
              {{task.EstimatedQuantity}}
            </td>
            <td class="text-right">
            </td>
          </tr>
          </tbody>
        </table>

      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideTask()">
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
  import MaskedInput from 'vue-text-mask';
  import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
  import Select2 from 'v-select2-component';
  import moment from 'moment';
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import IjcoreDatePicker from "@/components/IjcoreDatePicker";
  import IjcoreModalIndicatorTable from "../../../../components/IjcoreModalIndicatorTable";
  import IjcoreKeyResultType from "../../../../components/IjcoreModalKeyResultType";
  import {TokenService} from '@/services/storage.service';
  import IjcoreNumber from "../../../../components/IjcoreNumber";
  import IjcoreSelect2Server from "../../../../components/IjcoreSelect2Server";

  moment.locale('vi');


  const ListRouter = 'task-evaluation-trans';
  const EditRouter = 'task-evaluation-trans-edit';
  const CreateRouter = 'task-evaluation-trans-create';
  const ViewRouter = 'task-evaluation-trans-view';
  const DetailApi = 'task/api/evaluation-trans/view';
  const EditApi = 'task/api/evaluation-trans/edit';
  const CreateApi = 'task/api/evaluation-trans/create';
  const StoreApi = 'task/api/evaluation-trans/store';
  const UpdateApi = 'task/api/evaluation-trans/update';
  const ListApi = 'task/api/evaluation-trans';

  export default {
    name: 'task-evaluation-trans-form',
    data() {
      return {
        isForm: false,
        idParams: this.idParamsProps,
        reqParams: this.reqParamsProps,
        TransItemIDCurrent: -1,
        TransItemKeyCurrent: -1,
        RowItem: 0,
        TaskEvaluationEmployeeArr: {},
        TaskEvaluatorEmployee: {},
        EmployeeLogin: JSON.parse(localStorage.getItem('Employee')),
        model: {
          TransID: '',
          TransNo: '',
          TransName: '',
          TransType: 2,
          CompanyID: '',
          CompanyNo: '',
          CompanyName: '',
          EmployeeID: '',
          EmployeeNo: '',
          EmployeeName: '',
          EvaluationMethod: null,
          PeriodType: (this.$route.params.PeriodType) ? this.$route.params.PeriodType : 2,
          FromDate: '',
          ToDate: '',
          AccessType: '',
          Locked: '',
          Inactive: '',
          EvaluationTransItem: [],
          EvaluationTransItemKeyresult: [],
          EvaluationTransItemFeedback: [],
          Evaluation1Job: [],
          uomOption: [],
          indicatorOption: [],
          BinaryData: [],
          BinaryDataOption: [],
          IndexRowSelected: null,
          TaskIndicator: [],
          TaskIndicatorList: [],
          Uom: []
        },

        KeyResultScaleRateItem: [],
        KeyResultBinaryItem: [],
        KeyResultKeyCurrent: -1,

        IndicatorTable: {
          TemplateID: '',
          TemplateName: ''
        },
        GradingMethodOption: [],
        Uom: [],
        currentEmployee: (localStorage.getItem('Employee')) ? JSON.parse(localStorage.getItem('Employee')) : null,
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
      IjcoreKeyResultType,
      IjcoreModalIndicatorTable,
      IjcoreModalListing,
      Select2,
      MaskedInput,
      IjcoreNumber,
      IjcoreDatePicker,
      IjcoreSelect2Server
    },
    beforeCreate() {
    },
    mounted() {
      this.setPeriodType();
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
      updateDataEvaluation(){
        let self = this;
        _.forEach(self.model.EvaluationTransItem, function (value, key) {
            let requestData = {
              method: 'post',
              data: {
                EmployeeID: self.model.EmployeeID,
                PeriodType: self.model.PeriodType,
                FromDate: self.model.FromDate,
                ToDate: self.model.ToDate,
                IndicatorID: value.IndicatorID
              }
            };
            requestData.url = 'task/api/evaluation-trans/cal-evaluation';
            self.$store.commit('isLoading', true);
            ApiService.setHeader();
            ApiService.customRequest(requestData).then((responses) => {
              let responsesData = responses.data;
              if (responsesData.status === 1) {

                if(responsesData.TotalEstimatedQuantity){

                }
                if(responsesData.TotalPlanEstimatedQuantity){

                }
                self.model.EvaluationTransItem[key].ObjectiveIndex = TotalPlanEstimatedQuantity;
                if(responsesData.data){
                  self.TaskEvaluationEmployeeArr[value.IndicatorID+'_'+self.EmployeeLogin.EmployeeID] = {
                      IndicatorID: value.IndicatorID,
                      ActualIndexC: responsesData.data[0].ActualIndex,
                      EvaluatorID: self.EmployeeLogin.EmployeeID,
                      EvaluatorName: self.EmployeeLogin.EmployeeName,
                      ActualIndex: responsesData.data[0].ActualIndex
                    };
                  self.updateActualIndexParent(key, value.IndicatorID)
                }
              }
              self.$store.commit('isLoading', false);
            }, (error) => {
              console.log(error);
              self.$store.commit('isLoading', false);
            });
        });
      },
      updateActualIndexParent(key, IndicatorID){
        let self = this;
        let Total = 0;
        let Count = 0;
        _.forEach(this.TaskEvaluationEmployeeArr, function (value, k) {
          if(k.includes(IndicatorID+'_')){
            if(value.ActualIndex){
              Total += value.ActualIndex;
              Count++;
            }else{
              self.TaskEvaluationEmployeeArr[k].ActualIndex = 0;
            }
          }
        });
        this.model.EvaluationTransItem[key].ActualIndex = Total/(Count?Count:1);
        this.autoActualRate(key)
      },
      test(key){
        console.log(this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key]);
      },
      changeEvaluationMethod(key){
        this.model.EvaluationTransItem[key].IndicatorID = null;
        this.model.EvaluationTransItem[key].IndicatorNo = '';
        this.model.EvaluationTransItem[key].IndicatorName = '';
        this.model.EvaluationTransItem[key].UomName = '';
        this.model.EvaluationTransItem[key].UomID = null;
        this.model.EvaluationTransItem[key].ObjectiveIndex = '';
        this.model.EvaluationTransItem[key].ActualIndex = '';
        this.model.EvaluationTransItem[key].ActualRate = '';
      },
      selectedIndicator(key){
        let indicators = _.filter(this.model.EvaluationTransItem, ['IndicatorID', this.model.EvaluationTransItem[key].IndicatorID]);
        if (indicators.length > 1) {
          this.$bvToast.toast('Đã tồn tại chỉ tiêu', {
            title: 'Thông báo',
            variant: 'warning',
            solid: true
          });
          this.model.EvaluationTransItem[key].IndicatorID = null;
          this.model.EvaluationTransItem[key].IndicatorNo = '';
          this.model.EvaluationTransItem[key].IndicatorName = '';
          this.model.EvaluationTransItem[key].EvaluationMethod = '';
          this.model.EvaluationTransItem[key].UomName = '';
          this.model.EvaluationTransItem[key].UomID = null;
        } else {
          // TODO: check update ObjectIndex ActualIndex
          let self = this;
          let indicator = indicators[0];
          self.TaskEvaluationEmployeeArr[indicator.IndicatorID+'_'+self.EmployeeLogin.EmployeeID] = {
            IndicatorID: indicator.IndicatorID,
            ActualIndexC: '',
            EvaluatorID: self.EmployeeLogin.EmployeeID,
            EvaluatorName: self.EmployeeLogin.EmployeeName,
            ActualIndex: ''
          };
          let requestData = {
            method: 'post',
            data: {
              TableID: this.IndicatorTable.TableID,
              IndicatorID: this.model.EvaluationTransItem[key].IndicatorID,
              PeriodType: this.model.PeriodType,
              FromDate: this.model.FromDate,
              ToDate: this.model.ToDate,
              TransType: this.model.TransType,
              EmployeeID: this.model.EmployeeID,
              CompanyID: this.model.CompanyID

            }
          };
          requestData.url = 'task/api/evaluation-trans/load-indicator';
          self.$store.commit('isLoading', true);
          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;
            if (responsesData.status === 1 && responsesData.data.data) {
              // ActualIndex
              let totalActualIndex = 0, evaluation1Job = [], unqTaskID = [];
              self.model.EvaluationTransItem[key].GradingMethod = responsesData.data.data.GradingMethod;
              self.model.EvaluationTransItem[key].EvaluationMethod = responsesData.data.data.EvaluationMethod;

              if (responsesData.data.IndicatorTableItem) {
                self.model.EvaluationTransItem[key].ObjectiveRate = responsesData.data.IndicatorTableItem.ObjectiveRate;
                self.model.EvaluationTransItem[key].GradingType = responsesData.data.IndicatorTableItem.GradingType;
                self.model.EvaluationTransItem[key].ObjectiveIndex = responsesData.data.IndicatorTableItem.ObjectiveIndex;
              }
              if (responsesData.data.Evaluation1Job) self.setTaskIndicatorList(responsesData.data.Evaluation1Job);
              switch (responsesData.data.data.GradingMethod) {
                case 1:
                  // Đơn vị đo
                  evaluation1Job = responsesData.data.Evaluation1Job;
                  totalActualIndex = 0;
                  _.forEach(evaluation1Job, function (item, key1Job) {
                    if (!_.includes(unqTaskID, item.TaskID)) {
                      unqTaskID.push(item.TaskID);
                    } else {
                      return;
                    }
                    let _1job = _.filter(evaluation1Job, ['TaskID', item.TaskID]);
                    let _1jobActualIndex = 0, total1JobActualIndex = 0;
                    if (_1job.length > 1) {
                      let i = 0;
                      _.forEach(_1job, function (job, key1Job) {
                        if (job.TotalActualConvertQuantity) {
                          total1JobActualIndex += Number(job.TotalActualConvertQuantity);
                          i++;
                        }
                      });
                      _1jobActualIndex = (i) ? total1JobActualIndex / i : 0;
                    } else {
                      _1jobActualIndex = Number(item.TotalActualConvertQuantity);
                    }
                    totalActualIndex += _1jobActualIndex;
                  });
                  self.model.EvaluationTransItem[key].ActualIndex = (totalActualIndex) ? totalActualIndex : '';
                  break;
                case 2:
                  // Bảng điểm
                  evaluation1Job = responsesData.data.Evaluation1Job;
                  totalActualIndex = 0;
                  let count = 0;
                  _.forEach(evaluation1Job, function (item, key1Job) {
                    if (!_.includes(unqTaskID, item.TaskID)) {
                      unqTaskID.push(item.TaskID);
                    } else {
                      return;
                    }
                    let _1job = _.filter(evaluation1Job, ['TaskID', item.TaskID]);
                    let _1jobActualIndex = 0, total1JobActualIndex = 0;
                    if (_1job.length > 1) {
                      let i = 0;
                      _.forEach(_1job, function (job, key1Job) {
                        if (job.LevelInt100) {
                          total1JobActualIndex += Number(job.LevelInt100);
                          i++;
                        }
                      });
                      _1jobActualIndex = (i) ? total1JobActualIndex / _1job.length : 0;
                    } else {
                      _1jobActualIndex = Number(item.LevelInt100);
                    }
                    if (_1jobActualIndex) {
                      totalActualIndex += _1jobActualIndex;
                      count++;
                    }
                  });
                  self.model.EvaluationTransItem[key].ActualIndex = (count) ? totalActualIndex / count : '';
                  break;
                case 3:
                  // Nhị phân
                  break;
                case 4:
                  // Tỷ lệ %
                  break;
              }
              if (self.model.EvaluationTransItem[key].ActualIndex) self.model.EvaluationTransItem[key].ActualIndex = self.model.EvaluationTransItem[key].ActualIndex.toFixed(2);
              self.$forceUpdate();
            }

            if (responsesData.data.IndicatorTableItemKeyResult) {
              self.model.EvaluationTransItemKeyresult[self.model.EvaluationTransItem[key].TransItemID] = [];
              _.forEach(responsesData.data.IndicatorTableItemKeyResult, function (field, key) {
                let fieldObj = {};
                fieldObj.TransItemID = field.TableItemID;
                fieldObj.ScaleRateID = field.ScaleRateID;
                fieldObj.BinaryDataID = field.BinaryDataID;
                // fieldObj.Description = field.Description;
                fieldObj.KeyresultType = field.KeyresultType;
                fieldObj.KeyresultName = field.KeyresultName;
                fieldObj.UomID = field.UomID;
                fieldObj.UomName = field.UomName;
                fieldObj.Rate = field.Rate;
                fieldObj.IndicatorID = field.IndicatorID;
                fieldObj.IndicatorNo = field.IndicatorNo;
                fieldObj.IndicatorName = field.IndicatorName;
                fieldObj.LineIDTemp = self.TransItemIDCurrent;
                self.model.EvaluationTransItemKeyresult[fieldObj.TransItemID].push(fieldObj);
              });
            }

            if (responsesData.data.IndicatorTableItemFeedback) {
              self.model.EvaluationTransItemFeedback[self.model.EvaluationTransItem[key].TransItemID] = [];
              _.forEach(responsesData.data.IndicatorTableItemFeedback, function (field, key) {
                let fieldObj = {};
                fieldObj.TransItemID = field.TableItemID;
                fieldObj.ScaleRateID = field.ScaleRateID;
                fieldObj.BinaryDataID = field.BinaryDataID;
                fieldObj.Description = field.Description;
                fieldObj.FeedbackName = field.FeedbackName;
                fieldObj.IsBinaryData = field.IsBinaryData;
                fieldObj.FeedbackDate = field.FeedbackDate;
                fieldObj.UserName = field.UserName;
                fieldObj.LineIDTemp = self.TransItemIDCurrent;
                self.model.EvaluationTransItemFeedback[fieldObj.TransItemID].push(fieldObj)
              });
            }

            self.$store.commit('isLoading', false);
          }, (error) => {
            console.log(error);
            self.$store.commit('isLoading', false);
          });
        }
      },
      selectedIndicatorItem(keyMethod, key){
        let indicators = _.filter(this.model[keyMethod][this.TransItemIDCurrent], ['IndicatorID', this.model[keyMethod][this.TransItemIDCurrent][key].IndicatorID]);
        if (indicators.length > 1) {
          this.$bvToast.toast('Đã tồn tại chỉ tiêu', {
            title: 'Thông báo',
            variant: 'warning',
            solid: true
          });
          this.model[keyMethod][this.TransItemIDCurrent][key].IndicatorID = null;
          this.model[keyMethod][this.TransItemIDCurrent][key].IndicatorNo = '';
          this.model[keyMethod][this.TransItemIDCurrent][key].IndicatorName = '';
          this.model.EvaluationTransItem[key].EvaluationMethod = '';
          this.model[keyMethod][this.TransItemIDCurrent][key].UomName = '';
          this.model[keyMethod][this.TransItemIDCurrent][key].UomID = null;
        }
        this.$forceUpdate();
      },
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
              this.$forceUpdate();
              return true;
            }
          }
        }
      },
      checkIndicatorItem(KeyModel, Key){
        let k = 0;
        for (let i = 0; i < this.model[KeyModel][this.TransItemIDCurrent].length; i++) {
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
              this.$forceUpdate();
              return true;
            }
          }
        }
      },
      changePeriodType(){
        var CurrentDate = new Date();
        switch (this.model.PeriodType) {
          case 1:
            this.model.FromDate = '01/01/'+CurrentDate.getFullYear()
            this.model.ToDate = '31/12/'+CurrentDate.getFullYear()
            break;
          case 2:
            var CurrentMonth = CurrentDate.getMonth();
            if(CurrentMonth < 6){
              var d = new Date(CurrentDate.getFullYear()+'-07-01');
              d.setDate(d.getDate()-1);
              var TempToDate = moment(d).format('DD/MM/YYYY');
              this.model.FromDate = '01/01/'+CurrentDate.getFullYear()
              this.model.ToDate = TempToDate
            }else{
              this.model.FromDate = '01/07/'+CurrentDate.getFullYear()
              this.model.ToDate = '31/12/'+CurrentDate.getFullYear()
            }
            break;
          case 3:
            var CurrentMonth = CurrentDate.getMonth();
            var Quarter = Math.floor(CurrentMonth/3);
            var FromMonthQuarter = Quarter*3 + 1;
            var ToMonthQuarter = Quarter*3 + 3;
            if(ToMonthQuarter + 1 < 10){
              ToMonthQuarter = ToMonthQuarter + 1;
              ToMonthQuarter = '0'+ToMonthQuarter
            }else{
              ToMonthQuarter = ''+ToMonthQuarter;
            }
            if(FromMonthQuarter < 10){
              FromMonthQuarter = '0'+FromMonthQuarter
            }else{
              FromMonthQuarter = ''+FromMonthQuarter;
            }
            var d = new Date(CurrentDate.getFullYear()+'-'+ToMonthQuarter+'-01');
            d.setDate(d.getDate()-1);
            var TempToDate = moment(d).format('DD/MM/YYYY');
            this.model.FromDate = '01/'+FromMonthQuarter+'/'+CurrentDate.getFullYear()
            this.model.ToDate = TempToDate
            break;
          case 4:
            var CurrentMonth = CurrentDate.getMonth() + 1;
            var NextMonth = CurrentMonth+1;

            if(NextMonth < 10){
              NextMonth = '0'+NextMonth
            }else{
              NextMonth = ''+NextMonth;
            }
            if(CurrentMonth < 10){
              CurrentMonth = '0'+CurrentMonth
            }else{
              CurrentMonth = ''+CurrentMonth;
            }
            var d = new Date(CurrentDate.getFullYear()+'-'+NextMonth+'-01');
            d.setDate(d.getDate()-1);
            var TempToDate = moment(d).format('DD/MM/YYYY');
            this.model.FromDate = '01/'+CurrentMonth+'/'+CurrentDate.getFullYear()
            this.model.ToDate = TempToDate
            break;
          case 5:
            var d = new Date(CurrentDate.getFullYear()+'-01-01');
            var d1 = new Date(CurrentDate.getFullYear()+'-01-01');
            var dayOfWeekFirst = d.getDay();
            var NumberDayWeekFirst = 8 - dayOfWeekFirst;
            var NumberDays = Math.round((CurrentDate-d)/(1000*60*60*24)) + 1;
            var NumberWeek = Math.floor((NumberDays - NumberDayWeekFirst)/7);
            var NumberFromDate = d.getDate() + NumberWeek*7 + NumberDayWeekFirst;
            var NumberToDate = d.getDate() + NumberWeek*7 + NumberDayWeekFirst + 6;
            this.model.FromDate = moment(d.setDate(NumberFromDate)).format('DD/MM/YYYY')
            this.model.ToDate = moment(d1.setDate(NumberToDate)).format('DD/MM/YYYY')
            break;
          case 6:
            this.model.FromDate = moment(CurrentDate).format('DD/MM/YYYY')
            this.model.ToDate = moment(CurrentDate).format('DD/MM/YYYY')
            break;
          default:
            break;
        }
      },
      setPeriodType(){
        let workDate = TokenService.getWorkdate();
        if (!workDate) {
          workDate = moment().format('L');
        }
        let momentWorkDate = moment(workDate, 'L');
        let yearWorkDate = momentWorkDate.get("year");
        let monthWorkDate = momentWorkDate.get('month');
        switch (this.model.PeriodType) {
          case 1:
            this.model.FromDate = moment([yearWorkDate, 0]).startOf("months").format('L');
            this.model.ToDate = moment([yearWorkDate, 11]).endOf("months").format('L');
            break;
          case 2:
            if (monthWorkDate < 6) {
              this.model.FromDate = moment([yearWorkDate, 0]).startOf("months").format('L');
              this.model.ToDate = moment([yearWorkDate, 5]).endOf("months").format('L');
            } else {
              this.model.FromDate = moment([yearWorkDate, 6]).startOf("months").format('L');
              this.model.ToDate = moment([yearWorkDate, 11]).endOf("months").format('L');
            }
            break;
          case 3:
            if (0 <= monthWorkDate && monthWorkDate < 3) {
              this.model.FromDate = moment([yearWorkDate, 0]).startOf("months").format('L');
              this.model.ToDate = moment([yearWorkDate, 2]).endOf("months").format('L');
            }else if (3 <= monthWorkDate && monthWorkDate < 6) {
              this.model.FromDate = moment([yearWorkDate, 3]).startOf("months").format('L');
              this.model.ToDate = moment([yearWorkDate, 5]).endOf("months").format('L');
            }else if (6 <= monthWorkDate && monthWorkDate < 9) {
              this.model.FromDate = moment([yearWorkDate, 6]).startOf("months").format('L');
              this.model.ToDate = moment([yearWorkDate, 8]).endOf("months").format('L');
            }else {
              this.model.FromDate = moment([yearWorkDate, 9]).startOf("months").format('L');
              this.model.ToDate = moment([yearWorkDate, 11]).endOf("months").format('L');
            }
            break;
          case 4:
            this.model.FromDate = momentWorkDate.startOf("months").format('L');
            this.model.ToDate = momentWorkDate.endOf("months").format('L');
            break;
          case 5:
            this.model.FromDate = momentWorkDate.startOf("isoWeek").format('L');
            this.model.ToDate = momentWorkDate.endOf("isoWeek").format('L');
            break;
          case 6:
            this.model.FromDate = workDate;
            this.model.ToDate = workDate;
            break;
          default:
            break;
        }
        this.handleGetTemplate();
      },
      handleGetTemplate(){
        let self = this;
        if (!this.model.TransType || !this.model.PeriodType || !this.model.FromDate || !this.model.ToDate || (!this.model.CompanyID && !this.model.EmployeeID)) {
          return;
        }
        let requestData = {
          method: 'post',
          url: 'task/api/evaluation-trans/get-temp',
          data: {
            IndicatorType: this.model.TransType,
            CompanyID: this.model.CompanyID,
            EmployeeID: this.model.EmployeeID,
            PeriodType: this.model.PeriodType,
            FromDate: this.model.FromDate,
            ToDate: this.model.ToDate
          }
        };
        // Api edit user
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          self.model.EvaluationTransItem = [];
          if (responsesData.status === 1) {

            if (responsesData.data) {
              self.IndicatorTable.TableID = responsesData.data.TableID;
              self.IndicatorTable.TableName = responsesData.data.TableName;
              self.changeTemplate();
            } else {
              self.$store.commit('isLoading', false);
            }
          } else {
            self.$store.commit('isLoading', false);
          }
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      changeTemplate() {
        let self = this;
        let requestData = {
          method: 'post',
          data: {
            TableID: self.IndicatorTable.TableID,
            TransType: self.model.TransType,
            CompanyID: self.model.CompanyID,
            EmployeeID: self.model.EmployeeID,
            PeriodType: self.model.PeriodType,
            FromDate: self.model.FromDate,
            ToDate: self.model.ToDate
          }
        };
        requestData.url = 'task/api/evaluation-trans/load-temp';
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          self.model.EvaluationTransItem = [];
          if (responsesData.status === 1) {
            self.model.TransName = responsesData.data.data.TableName;
            self.model.TransType = responsesData.data.data.IndicatorType;
            //self.model.EvaluationMethod = responsesData.data.data.EvaluationMethod;
            self.model.PeriodType = responsesData.data.data.PeriodType;
            self.model.FromDate = __.convertDateToString(responsesData.data.data.FromDate);
            self.model.ToDate = __.convertDateToString(responsesData.data.data.ToDate);
            self.model.CompanyID = responsesData.data.data.CompanyID;
            self.model.CompanyNo = responsesData.data.data.CompanyNo;
            self.model.CompanyName = responsesData.data.data.CompanyName;
            self.model.EmployeeID = responsesData.data.data.EmployeeID;
            self.model.EmployeeNo = responsesData.data.data.EmployeeNo;
            self.model.EmployeeName = responsesData.data.data.EmployeeName;

            //////////////////////////TaskEvaluationEmployee//////////////////////////
            _.forEach(responsesData.data.TaskEvaluationEmployee, function (value, key) {
              self.TaskEvaluationEmployeeArr[value.IndicatorID+'_'+value.EvaluatorID] = value;
              self.TaskEvaluatorEmployee[value.EvaluatorID] = {
                EvaluatorID: value.EvaluatorID,
                EvaluatorName: value.EvaluatorName
              };
            });
            // self.model.Evaluation1Job = responsesData.data.Evaluation1Job;
            // self.setTaskIndicatorList(responsesData.data.Evaluation1Job);
            _.forEach(responsesData.data.IndicatorTableItem, function (field, key) {
              let fieldObj = {};
              self.RowItem = field.TableItemID;
              fieldObj.TransItemID = self.RowItem;
              fieldObj.IndicatorID = field.IndicatorID;
              fieldObj.IndicatorName = field.IndicatorName;
              fieldObj.IndicatorNo = field.IndicatorNo;
              fieldObj.UomID = field.UomID;
              fieldObj.UomName = field.UomName;
              fieldObj.GradingType = field.GradingType;
              fieldObj.EvaluationMethod = field.EvaluationMethod;
              fieldObj.ObjectiveRate = field.ObjectiveRate;
              fieldObj.GradingMethod = field.GradingMethod;
              fieldObj.ObjectiveIndex = field.ObjectiveIndex;
              fieldObj.ScaleRateID = field.ScaleRateID;
              fieldObj.Description = field.Description;
              fieldObj.ActualIndex = '';

              // ActualIndex
              let totalActualIndex = 0, evaluation1Job = [], unqTaskID = [];
              switch (field.GradingMethod) {
                case 1:
                  // Đơn vị đo
                  evaluation1Job = _.filter(self.model.Evaluation1Job, ['IndicatorID', field.IndicatorID]);
                  totalActualIndex = 0;
                  _.forEach(evaluation1Job, function (item, key) {
                    if (!_.includes(unqTaskID, item.TaskID)) {
                      unqTaskID.push(item.TaskID);
                    } else {
                      return;
                    }
                    let _1job = _.filter(evaluation1Job, ['TaskID', item.TaskID]);
                    let _1jobActualIndex = 0, total1JobActualIndex = 0;
                    if (_1job.length > 1) {
                      let i = 0;
                      _.forEach(_1job, function (job, key) {
                        if (job.TotalActualConvertQuantity) {
                          total1JobActualIndex += Number(job.TotalActualConvertQuantity);
                          i++;
                        }
                      });
                      _1jobActualIndex = (i) ? total1JobActualIndex / i : 0;
                    } else {
                      _1jobActualIndex = Number(item.TotalActualConvertQuantity);
                    }
                    totalActualIndex += _1jobActualIndex;
                  });
                  fieldObj.ActualIndex = (totalActualIndex) ? totalActualIndex : '';
                  break;
                case 2:
                  // Bảng điểm
                  evaluation1Job = _.filter(self.model.Evaluation1Job, ['IndicatorID', field.IndicatorID]);
                  totalActualIndex = 0;
                  let count = 0;
                  _.forEach(evaluation1Job, function (item, key) {
                    if (!_.includes(unqTaskID, item.TaskID)) {
                      unqTaskID.push(item.TaskID);
                    } else {
                      return;
                    }
                    let _1job = _.filter(evaluation1Job, ['TaskID', item.TaskID]);
                    let _1jobActualIndex = 0, total1JobActualIndex = 0;
                    if (_1job.length > 1) {
                      let i = 0;
                      _.forEach(_1job, function (job, key) {
                        if (job.LevelInt100) {
                          total1JobActualIndex += Number(job.LevelInt100);
                          i++;
                        }
                      });
                      _1jobActualIndex = (i) ? total1JobActualIndex / _1job.length : 0;
                    } else {
                      _1jobActualIndex = Number(item.LevelInt100);
                    }
                    if (_1jobActualIndex) {
                      totalActualIndex += _1jobActualIndex;
                      count++;
                    }
                  });
                  fieldObj.ActualIndex = (count) ? totalActualIndex / count : '';
                  break;
                case 3:
                  // Nhị phân
                  break;
                case 4:
                  // Tỷ lệ %
                  break;
              }
              if (fieldObj.ActualIndex) fieldObj.ActualIndex = fieldObj.ActualIndex.toFixed(2);
              fieldObj.ActualRate = '';
              fieldObj.YesNo = '';
              fieldObj.FeedbackContent = '';
              fieldObj.TestResult = '';
              fieldObj.LineIDTemp = self.RowItem;
              self.RowItem = self.RowItem + 1;
              self.model.EvaluationTransItem.push(fieldObj);

              if (self.TaskEvaluationEmployeeArr[field.IndicatorID+'_'+self.EmployeeLogin.EmployeeID] === undefined) {
                self.TaskEvaluationEmployeeArr[field.IndicatorID+'_'+self.EmployeeLogin.EmployeeID] = {
                  TransItemID: '',
                  TransID: '',
                  ActualIndex: 0,
                  ActualRate: 0,
                  EvaluatorID: self.EmployeeLogin.EmployeeID,
                  EvaluatorName: self.EmployeeLogin.EmployeeName,
                };
              }
            });
            _.forEach(responsesData.data.IndicatorTableItemKeyResult, function (field, key) {
              let fieldObj = {};
              fieldObj.TransItemID = field.TableItemID;
              fieldObj.ScaleRateID = field.ScaleRateID;
              fieldObj.BinaryDataID = field.BinaryDataID;
              // fieldObj.Description = field.Description;
              fieldObj.KeyresultType = field.KeyresultType;
              fieldObj.KeyresultName = field.KeyresultName;
              fieldObj.UomID = field.UomID;
              fieldObj.UomName = field.UomName;
              fieldObj.Rate = field.Rate;
              fieldObj.IndicatorID = field.IndicatorID;
              fieldObj.IndicatorNo = field.IndicatorNo;
              fieldObj.IndicatorName = field.IndicatorName;
              fieldObj.LineIDTemp = self.TransItemIDCurrent;

              if (self.model.EvaluationTransItemKeyresult[fieldObj.TransItemID] == undefined) {
                self.model.EvaluationTransItemKeyresult[fieldObj.TransItemID] = [];
              }
              self.model.EvaluationTransItemKeyresult[fieldObj.TransItemID].push(fieldObj);
            });
            _.forEach(responsesData.data.IndicatorTableItemFeedback, function (field, key) {
              let fieldObj = {};
              fieldObj.TransItemID = field.TableItemID;
              fieldObj.ScaleRateID = field.ScaleRateID;
              fieldObj.BinaryDataID = field.BinaryDataID;
              fieldObj.Description = field.Description;
              fieldObj.FeedbackName = field.FeedbackName;
              fieldObj.IsBinaryData = field.IsBinaryData;
              fieldObj.FeedbackDate = field.FeedbackDate;
              fieldObj.UserName = field.UserName;
              fieldObj.LineIDTemp = self.TransItemIDCurrent;
              if (self.model.EvaluationTransItemFeedback[fieldObj.TransItemID] == undefined) {
                self.model.EvaluationTransItemFeedback[fieldObj.TransItemID] = [];
              }
              self.model.EvaluationTransItemFeedback[fieldObj.TransItemID].push(fieldObj)
            });
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      setTaskIndicatorList(evaluation1Job){
        let self = this;
        _.forEach(evaluation1Job, function (item, key) {
          let taskExist = _.find(self.model.TaskIndicatorList, {
            TaskID: item.TaskID,
            IndicatorID: item.IndicatorID
          });
          if (!taskExist) {
            self.model.TaskIndicatorList.push(item);
          }
        });
      },
      fetchData() {
        let self = this;
        let urlApi = CreateApi;
        let requestData = {
          method: 'post',
          data: {}
        };
        // Api edit user
        if (this.idParams) {
          urlApi = EditApi + '/' + this.idParams;
          requestData.data.id = this.idParams;
          requestData.method = 'get';
        }else{
          requestData.data.PeriodType = this.model.PeriodType;
          requestData.data.FromDate = this.model.FromDate;
          requestData.data.ToDate = this.model.ToDate;
          requestData.data.IndicatorType = this.model.TransType;
        }
        requestData.url = urlApi;
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (!self.idParams && !_.isEmpty(self.itemCopy)) {
            responsesData.data.data = self.itemCopy.data.data;
          }
          self.model.EvaluationTransItem = [];
          if (responsesData.status === 1) {
            if (self.idParams || !_.isEmpty(self.itemCopy)) {
              if (_.isObject(responsesData.data.data)) {
                self.model.TransName = responsesData.data.data.TransName;
                self.model.TransNo = responsesData.data.data.TransNo;
                self.model.TransType = responsesData.data.data.TransType;
                //self.model.EvaluationMethod = responsesData.data.data.EvaluationMethod;
                self.model.PeriodType = responsesData.data.data.PeriodType;
                self.model.FromDate = __.convertDateToString(responsesData.data.data.FromDate);
                self.model.ToDate = __.convertDateToString(responsesData.data.data.ToDate);
                self.model.CompanyID = responsesData.data.data.CompanyID;
                self.model.CompanyNo = responsesData.data.data.CompanyNo;
                self.model.CompanyName = responsesData.data.data.CompanyName;
                self.model.EmployeeID = responsesData.data.data.EmployeeID;
                self.model.EmployeeNo = responsesData.data.data.EmployeeNo;
                self.model.EmployeeName = responsesData.data.data.EmployeeName;
              }

              if (!_.isEmpty(self.itemCopy)) {
                self.model.TransNo = responsesData.data.auto;
              }
            } else {
              self.model.TransNo = responsesData.data.auto;
            }


            _.forEach(responsesData.data.Uom, function (value, key) {
              self.model.Uom[value.UomID] = value.UomName;
            });

            //////////////////////////TaskEvaluationEmployee//////////////////////////
            _.forEach(responsesData.data.TaskEvaluationEmployee, function (value, key) {
              self.TaskEvaluationEmployeeArr[value.IndicatorID+'_'+value.EvaluatorID] = value;
              self.TaskEvaluatorEmployee[value.EvaluatorID] = {
                EvaluatorID: value.EvaluatorID,
                EvaluatorName: value.EvaluatorName
              };
            });
            // self.model.Evaluation1Job = responsesData.data.Evaluation1Job;
            // self.setTaskIndicatorList(responsesData.data.Evaluation1Job);
            _.forEach(responsesData.data.EvaluationTransItem, function (field, key) {
              let fieldObj = {};
              self.RowItem = field.TransItemID;
              fieldObj = field;
              fieldObj.TransItemID = self.RowItem;
              fieldObj.LineIDTemp = self.RowItem;

              // ActualIndex
              let totalActualIndex = 0, evaluation1Job = [], unqTaskID = [];
              fieldObj.LineIDTemp = self.RowItem;
              self.RowItem = self.RowItem + 1;
              self.model.EvaluationTransItem.push(fieldObj);

              if (self.TaskEvaluationEmployeeArr[field.IndicatorID+'_'+self.EmployeeLogin.EmployeeID] === undefined) {
                self.TaskEvaluationEmployeeArr[field.IndicatorID+'_'+self.EmployeeLogin.EmployeeID] = {
                  TransItemID: '',
                  TransID: '',
                  ActualIndex: 0,
                  ActualRate: 0,
                  EvaluatorID: self.EmployeeLogin.EmployeeID,
                  EvaluatorName: self.EmployeeLogin.EmployeeName,
                };
              }
            });
            _.forEach(responsesData.data.EvaluationTransItemKeyresult, function (field, key) {
              let fieldObj = field;
              fieldObj = field;
              fieldObj.LineIDTemp = self.TransItemIDCurrent;
              if (self.model.EvaluationTransItemKeyresult[fieldObj.TransItemID] == undefined) {
                self.model.EvaluationTransItemKeyresult[fieldObj.TransItemID] = [];
              }
              self.model.EvaluationTransItemKeyresult[fieldObj.TransItemID].push(fieldObj);
            });
            _.forEach(responsesData.data.EvaluationTransItemFeedback, function (field, key) {
              let fieldObj = field;
              fieldObj.LineIDTemp = self.TransItemIDCurrent;
              if (self.model.EvaluationTransItemFeedback[fieldObj.TransItemID] == undefined) {
                self.model.EvaluationTransItemFeedback[fieldObj.TransItemID] = [];
              }
              self.model.EvaluationTransItemFeedback[fieldObj.TransItemID].push(fieldObj)
            });
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
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
        });

      },
      autoActualRate(key){
        if (this.model.EvaluationTransItem[key].EvaluationMethod !== 2) {
          let ActualIndex = Number(this.model.EvaluationTransItem[key].ActualIndex);
          let ObjectiveIndex = Number(this.model.EvaluationTransItem[key].ObjectiveIndex);
          let ObjectiveRate = Number(this.model.EvaluationTransItem[key].ObjectiveRate);
          let RsActualRate = (ActualIndex / ObjectiveIndex) * ObjectiveRate;
          if (!isNaN(RsActualRate) && RsActualRate) {
            this.model.EvaluationTransItem[key].ActualRate = RsActualRate.toFixed(2);
          } else {
            this.model.EvaluationTransItem[key].ActualRate = '';
          }
        } else {
          this.model.IndexRowSelected = key;
          this.TransItemIDCurrent = this.model.EvaluationTransItem[key].TransItemID;
          this.TransItemKeyCurrent = key;
          this.onSaveKeyResult();
        }
        this.$forceUpdate();
      },
      onChangeTransType(value){
        if(value == 1){
          this.model.EmployeeID = '';
          this.model.EmployeeName = '';
        }
        if(value == 2){
          this.model.CompanyID = '';
          this.model.CompanyName = '';
        }
        this.handleGetTemplate();
      },
      onSelectUom(value, key) {
        let uom = _.find(this.model.uomOption, ['value', value]);
        this.model.EvaluationTransItem[key].UomName = uom.text;
      },
      changeIsBinaryData(key){
        this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].IsBinaryData = !this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].IsBinaryData;
        this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].BinaryDataID = null;
        this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].FeedbackName = 'Phản hồi ngày: ' + this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].FeedbackDate;
        this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].FeedbackValueInt = null;
        this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].FeedbackValue = '';
        this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].FeedbackContent = '';
        if (!this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].IsBinaryData) {
          this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].FeedbackValue = 'Kiểu văn bản';
          this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].BinaryDataValueOption = [];
        }
        this.$forceUpdate();
      },
      changeBinaryData(value, key, TransItemIDCurrent) {
        let BinaryData = _.find(this.model.BinaryData, ['BinaryDataID', value]);
        let FeedbackDate = this.model.EvaluationTransItemFeedback[TransItemIDCurrent][key].FeedbackDate;
        this.model.EvaluationTransItemFeedback[TransItemIDCurrent][key].FeedbackName = 'Phản hồi ngày: ' + FeedbackDate + '; ' + 'theo giá trị: ' + BinaryData.BinaryDataName;
        this.model.EvaluationTransItemFeedback[TransItemIDCurrent][key].FeedbackContent = '';
        this.model.EvaluationTransItemFeedback[TransItemIDCurrent][key].BinaryDataValueOption = [
          {value: 0, text: BinaryData.BinaryData0},
          {value: 1, text: BinaryData.BinaryData1},
        ];
        this.$forceUpdate();
      },
      changeBinaryDataValue(key){
        let BinaryDataValue = _.find(this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].BinaryDataValueOption, ['value', Number(this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].FeedbackValueInt)]);
        this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].FeedbackValue = BinaryDataValue.text;
        this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].FeedbackName = 'Phản hồi ngày + '
          + this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].FeedbackDate + ' theo giá trị: ' + BinaryDataValue.text;
        this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent][key].FeedbackContent = BinaryDataValue.text;
        this.$forceUpdate();
      },
      onChangeKeyresultType(value, key, TransItemIDCurrent) {

        this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].ObjectiveIndex = '';
        this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].ActualIndex = '';
        this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].ActualRate = '';

        this.$set(this.model.EvaluationTransItemKeyresult, TransItemIDCurrent, this.model.EvaluationTransItemKeyresult[TransItemIDCurrent]);
        this.model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].ScaleRate = 0;
        this.model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].BinaryData = 0;
        if(this.model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].KeyresultType == 2){
          this.model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].ScaleRate = 1;
          this.model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].BinaryData = 0;
        }
        if(this.model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].KeyresultType == 3){
          this.model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].BinaryData = 1;
          this.model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].ScaleRate = 0;
          this.model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].ObjectiveIndex = 1;
        }
        this.$forceUpdate()
      },
      onChangeKeyResultScaleRate(params, key){
        if (this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].DetailItems) {
          let maxValue = this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].DetailItems[0].LevelInt,
            minValue = this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].DetailItems[0].LevelInt;
          _.forEach(this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].DetailItems, function (value, key) {
            if (value.LevelInt > maxValue) {
              maxValue = value.LevelInt;
            }
            if (value.LevelInt < minValue) {
              minValue = value.LevelInt;
            }
          });
          this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].ObjectiveIndex = maxValue;
          this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].MaxObjectiveIndex = maxValue;
          this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].MinObjectiveIndex = minValue;
        }
      },
      onClickKeyresultActualIndex(key, TransItemIDCurrent) {
        this.KeyResultKeyCurrent = key;
        switch (this.model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].KeyresultType) {
          case 1:
            break;
          case 2:
            this.KeyResultScaleRateItem = this.model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].DetailItems;
            this.$refs['key-result-scale-rate-item'].show();
            break;
          case 3:
            this.KeyResultBinaryItem = [
              {value: 0, text: this.model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].BinaryData0},
              {value: 1, text: this.model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].BinaryData1}
            ];
            this.$refs['key-result-binary-item'].show();
            break;
        }
      },
      onClickKeyResultScaleRateItem(scaleRateItem){
        this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][this.KeyResultKeyCurrent].ActualIndex = scaleRateItem.LevelInt;
        this.autoActualRateKeyResult(this.KeyResultKeyCurrent);
        this.$refs['key-result-scale-rate-item'].hide();
      },
      onClickKeyResultBinaryItem(binary){
        if (binary.value) {
          this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][this.KeyResultKeyCurrent].ActualIndex = 1;
          this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][this.KeyResultKeyCurrent].ActualRate = 100;
        } else {
          this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][this.KeyResultKeyCurrent].ActualIndex = 0;
          this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][this.KeyResultKeyCurrent].ActualRate = 0;
        }
        this.$refs['key-result-binary-item'].hide();
      },
      autoActualRateKeyResult(key){
        let ActualIndex = Number(this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].ActualIndex);
        let ObjectiveIndex = Number(this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].ObjectiveIndex);

        let RsActualRate = 0;
        if (ObjectiveIndex) {
          RsActualRate = (ActualIndex / ObjectiveIndex) * 100;
        }else {
          RsActualRate = '';
        }
        if(RsActualRate){
          this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].ActualRate = RsActualRate.toFixed(2);
        }else{
          this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].ActualRate = '';
        }
        this.$forceUpdate()
      },
      onAddFieldOnTrans(RowItem) {
        let fieldObj = {};
        fieldObj.TransItemID = RowItem;
        fieldObj.Description = '';
        fieldObj.IndicatorID = '';
        fieldObj.IndicatorNo = '';
        fieldObj.IndicatorName = '';
        fieldObj.UomID = '';
        fieldObj.UomName = '';
        fieldObj.GradingType = 1;
        fieldObj.ObjectiveRate = '';
        fieldObj.FrequencyType = 1;
        fieldObj.ObjectiveIndex = '';
        fieldObj.ActualIndex = '';
        fieldObj.ActualRate = '';
        fieldObj.LineIDTemp = RowItem;
        this.RowItem = RowItem + 1;
        let totalObjectiveRate = 0;
        _.forEach(this.model.EvaluationTransItem, function (evaluation, key) {
          totalObjectiveRate += Number(evaluation.ObjectiveRate);
        });
        fieldObj.ObjectiveRate = 100 - totalObjectiveRate;

        this.model.EvaluationTransItem.push(fieldObj);

        this.$forceUpdate();
      },
      onAddFieldOnKeyResult() {
        let fieldObj = {};
        fieldObj.ScaleRateID = '';
        fieldObj.BinaryDataID = '';
        fieldObj.BinaryDataValue = null;
        fieldObj.BinaryDataName = '';
        fieldObj.BinaryDataKey = null;
        fieldObj.Description = '';
        fieldObj.KeyresultType = 1;
        fieldObj.KeyresultName = '';
        fieldObj.UomID = null;
        fieldObj.UomName = null;
        fieldObj.Rate = '';
        fieldObj.ObjectiveIndex = '';
        fieldObj.ActualIndex = '';
        fieldObj.ActualRate = '';
        fieldObj.IndicatorID = '';
        fieldObj.IndicatorNo = '';
        fieldObj.IndicatorName = '';
        fieldObj.EvaluationMethod = this.model.EvaluationTransItem[this.TransItemKeyCurrent].EvaluationMethod;
        fieldObj.LineIDTemp = this.TransItemIDCurrent;
        let totalRate = 0;
        _.forEach(this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent], function (item, key) {
          totalRate += Number(item.Rate);
        });
        fieldObj.Rate = 100 - totalRate;

        this.$set(this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent], this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent].length, fieldObj);
        this.$forceUpdate();
      },
      onAddFieldOnFeedback() {
        let fieldObj = {};
        fieldObj.ScaleRateID = '';
        fieldObj.BinaryDataID = null;
        fieldObj.Description = '';
        fieldObj.IsBinaryData = false;
        fieldObj.UserName = (this.currentEmployee) ? this.currentEmployee.EmployeeName : '';
        fieldObj.UserID = (this.currentEmployee) ? this.currentEmployee.UserID : '';
        fieldObj.FeedbackDate = moment().format('L');
        fieldObj.IndicatorID = '';
        fieldObj.IndicatorNo = '';
        fieldObj.IndicatorName = '';
        fieldObj.EvaluationMethod = this.model.EvaluationTransItem[this.TransItemKeyCurrent].EvaluationMethod;
        fieldObj.LineIDTemp = this.TransItemIDCurrent;
        fieldObj.FeedbackName = 'Phản hồi ngày: ' + fieldObj.FeedbackDate;
        fieldObj.FeedbackContent = '';
        fieldObj.FeedbackValueInt = null;
        fieldObj.FeedbackValue = 'Kiểu văn bản';
        this.$set(this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent], this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent].length, fieldObj);
        this.$forceUpdate();
      },
      onDeleteFieldOnTrans(key) {
        this.model.EvaluationTransItemKeyresult.splice(this.model.EvaluationTransItem[key].TransItemID, 1);
        this.model.EvaluationTransItemFeedback.splice(this.model.EvaluationTransItem[key].TransItemID, 1);
        this.model.EvaluationTransItem.splice(key, 1);
        _.remove(this.model.TaskIndicatorList, ['Indicator', this.model.EvaluationTransItem[key].IndicatorID]);
        this.$forceUpdate();
      },
      onDeleteFieldOnKeyResult(key) {
        // remove field in fieldOnTransReq
        this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent].splice(key, 1)
        this.$forceUpdate();
      },
      onDeleteFieldOnFeedback(key) {
        // remove field in fieldOnTransReq
        this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent].splice(key, 1)
        this.$forceUpdate();
      },
      handleSubmitForm() {
        if(!__.checkPeriod(this.model.PeriodType, this.model.FromDate, this.model.ToDate)){
          Swal.fire(
            'Thông báo',
            'Kiểm tra lại chu kỳ, ngày bắt đầu, ngày kết thúc!',
            'error'
          );
        }else {
          let self = this;
          const requestData = {
            method: 'post',
            url: StoreApi,
            data: {
              master: {
                TransNo: this.model.TransNo,
                TransName: this.model.TransName,
                TransType: this.model.TransType,
                Inactive: (this.model.Inactive) ? 1 : 0,
                PeriodType: this.model.PeriodType,
                FromDate: this.model.FromDate,
                ToDate: this.model.ToDate,
                CompanyID: this.model.CompanyID,
                CompanyNo: this.model.CompanyNo,
                CompanyName: this.model.CompanyName,
                EmployeeID: this.model.EmployeeID,
                EmployeeNo: this.model.EmployeeNo,
                EmployeeName: this.model.EmployeeName
              },
              detail: this.model.EvaluationTransItem,
              SubDetail: this.TaskEvaluationEmployeeArr,
              EvaluationTransItemKeyresult: this.model.EvaluationTransItemKeyresult,
              EvaluationTransItemFeedback: this.model.EvaluationTransItemFeedback
            }
          };
          // edit user
          if (this.idParams) {
            requestData.data.master.TransID = this.idParams;
            // requestData.url = UpdateApi + '/' + this.idParams;
          }

          this.$store.commit('isLoading', true);
          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;
            if (responsesData.status === 1) {
              if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
              self.$router.push({
                name: ViewRouter,
                params: {id: responsesData.data, message: 'Bản ghi đã được cập nhật!'}
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
        }
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
      onAddOnKeyResult(TransItemID, key) {
        this.model.IndexRowSelected = key;
        this.TransItemIDCurrent = TransItemID;
        this.TransItemKeyCurrent = key;
        if (this.model.EvaluationTransItem[key].EvaluationMethod == 2) {
          if (!this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent]) {
            this.$set(this.model.EvaluationTransItemKeyresult, this.TransItemIDCurrent, []);
          }
          this.$refs['KeyResult'].show();
        };
        this.$forceUpdate();
      },

      onHideKeyResult() {
        this.isForm = false;
        this.$refs['KeyResult'].hide();
      },
      onAddOnFeedback(TransItemID, key) {
        this.TransItemIDCurrent = TransItemID;
        this.TransItemKeyCurrent = key;
        if (!this.model.EvaluationTransItemFeedback[this.TransItemIDCurrent]) {
          this.$set(this.model.EvaluationTransItemFeedback, this.TransItemIDCurrent, []);
        }
        this.$refs['Feedback'].show();
      },
      onHideFeedback() {
        this.isForm = false;
        this.$refs['Feedback'].hide();
      },
      onViewTask(key) {
        let self = this;
        let requestData = {
          method: 'post',
          data: {
            FromDate: self.model.FromDate,
            ToDate: self.model.ToDate,
            FrequencyType: self.model.PeriodType,
            EmployeeID: self.model.EmployeeID,
            IndicatorID: self.model.EvaluationTransItem[key].IndicatorID,
          },
          url: 'task/api/evaluation-trans/get-task'
        };

        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.model.TaskIndicator = responsesData.data;
            _.forEach(responsesData.Uom, function (value, key) {
              self.model.Uom[value.UomID] = value.UomName;
            });
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
        this.$forceUpdate();
        this.$refs['Task'].show();
      },
      onHideTask() {
        this.isForm = false;
        this.$refs['Task'].hide();
      },
      onSaveKeyResult() {
        let total = 0, emptyIndicator = false, TotalActualIndex = 0, TotalObjectiveIndex = 0;
        let ObjectiveRate = Number(this.model.EvaluationTransItem[this.TransItemKeyCurrent].ObjectiveRate);
        _.forEach(this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent], function (item, key) {
          if (item.IndicatorID) {
            let ActualRate = (item.ActualRate) ? Number(item.ActualRate) : 0;
            let ActualIndex = (item.ActualIndex) ? Number(item.ActualIndex) : 0;
            let ObjectiveIndex = (item.ObjectiveIndex) ? Number(item.ObjectiveIndex) : 0;
            let Rate = (item.Rate) ? Number(item.Rate) : 0;
            total += ActualRate * Rate / 100;
            TotalActualIndex += ActualIndex * Rate / 100;
            TotalObjectiveIndex += ObjectiveIndex * Rate / 100;
          } else {
            emptyIndicator = true;
            return;
          }
        });
        this.model.EvaluationTransItem[this.model.IndexRowSelected].ActualIndex = TotalActualIndex.toFixed(2);
        this.model.EvaluationTransItem[this.model.IndexRowSelected].ObjectiveIndex = TotalObjectiveIndex.toFixed(2);
        this.model.EvaluationTransItem[this.model.IndexRowSelected].ActualRate = total * ObjectiveRate / 100;
        this.model.EvaluationTransItem[this.model.IndexRowSelected].ActualRate = this.model.EvaluationTransItem[this.model.IndexRowSelected].ActualRate.toFixed(2);

        if (emptyIndicator) {
          this.$bvToast.toast('Một hoặc nhiều chỉ tiêu chưa được chọn', {
            title: 'Thông báo',
            variant: 'warning',
            solid: true
          });
        } else {
          this.$bvToast.toast('Đã lưu kết quả then chốt', {
            title: 'Thông báo',
            variant: 'success',
            solid: true
          });
        }
        this.$refs['KeyResult'].hide();
      },
      onSaveFeedback() {

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

      validateValuation(type, key, valueName){
        if (type === 'EvaluationTransItemKeyresult') {
          if (this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].KeyresultType == 2){
            if (this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key][valueName] > this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].MaxObjectiveIndex) {
              this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key][valueName] = this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].MaxObjectiveIndex;
              this.$bvToast.toast('Giá trị không được lớn hơn ' + this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].MaxObjectiveIndex, {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
            }
            if (this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key][valueName] < this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].MinObjectiveIndex) {
              this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key][valueName] = this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].MinObjectiveIndex;
              this.$bvToast.toast('Giá trị không được nhỏ hơn ' + this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].MinObjectiveIndex, {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
            }
          }
          if (this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].KeyresultType == 3) {
            if (this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].ActualIndex != 0 && this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].ActualIndex != 1) {
              this.$bvToast.toast('Giá trị chỉ nhận "0" và "1"', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
              this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].ActualIndex = 0;
              this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].ActualRate = 0;
            }
          }
          if (valueName == 'Rate') {
            if (this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].Rate < 0) {
              this.$bvToast.toast('Trọng số phải lớn hơn 0 và nhỏ hơn 100', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
              this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].Rate = 0;
            }
            if (this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].Rate > 100) {
              this.$bvToast.toast('Trọng số phải lớn hơn 0 và nhỏ hơn 100', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
              this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].Rate = 100;
            }

            let totalRate = 0;
            _.forEach(this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent], function (item, key) {
              totalRate += Number(item.Rate);
            });
            if (totalRate > 100) {
              this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].Rate = 0;
              this.$bvToast.toast('Tổng trọng số phải nhỏ hơn 100', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
            }
          }
          if (valueName !== 'Rate' && this.model.EvaluationTransItemKeyresult[this.TransItemIDCurrent][key].KeyresultType !== 3) this.autoActualRateKeyResult(key);
        }

        if (type === 'EvaluationTrans') {
          if (valueName === 'ObjectiveRate') {
            if (this.model.EvaluationTransItem[key].ObjectiveRate < 0) {
              this.$bvToast.toast('Trọng số phải lớn hơn 0 và nhỏ hơn 100', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
              this.model.EvaluationTransItem[key].ObjectiveRate = 0;
            }
            if (this.model.EvaluationTransItem[key].ObjectiveRate > 100) {
              this.$bvToast.toast('Trọng số phải lớn hơn 0 và nhỏ hơn 100', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
              this.model.EvaluationTransItem[key].ObjectiveRate = 100;
            }

            let totalRate = 0;
            _.forEach(this.model.EvaluationTransItem, function (item, key) {
              totalRate += Number(item.ObjectiveRate);
            });
            if (totalRate > 100) {
              this.model.EvaluationTransItem[key].ObjectiveRate = 0;
              this.$bvToast.toast('Tổng trọng số phải nhỏ hơn 100', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
            }
            this.autoActualRate(key);
          }
        }
      }
    },
    watch: {
      idParams() {
        this.fetchData();
      }
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

  .color-w{
    background-color: #63c2de;
    cursor: pointer;
  }
  #modal-feedback .mx-datepicker {
    position: absolute;
    left: 0;
    top: 0;
  }
  #modal-feedback .select2-selection {
    border: none;
  }
  #modal-feedback .select2-selection:focus {
    outline: none;
  }
  #modal-feedback .select2-container {
    width: 100% !important;
  }
  .td-action-fix-right-form {
    position: absolute;
    width: 83px;
    right: 20px;
    top: auto;
    /*only relevant for first row*/
    background: #fff;
    border-bottom: none !important;
    /*compensate for top border*/
    height: 34px;
  }
  .div-scroll-table {
    width: 100%;
    overflow-x: scroll;
    margin-right: 5em;
    overflow-y: visible;
    padding: 0;
  }
  .td-action-fix-right-form:last-child{
    border-bottom: 1px solid #c8ced3 !important;
    height: 34px;
  }
</style>
