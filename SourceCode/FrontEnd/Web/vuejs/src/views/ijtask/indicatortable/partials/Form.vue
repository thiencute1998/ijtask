<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Bảng chỉ tiêu ĐGCV<span
                v-if="model.TableName ">:</span> {{model.TableName }}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Bảng chỉ tiêu ĐGCV<span
                v-if="model.TableName ">:</span> {{model.TableName }}</span>
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
              <IjcoreModalIndicatorTemp v-if="!idParams" @changed="changeTemplate"
                                        v-model="IndicatorTemp"></IjcoreModalIndicatorTemp>
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
          <b-card v-if="this.idParams">

            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0" for="TableName">Tên</label>
              <div class="col-md-16" v-if="perEditView(model.TableName, TablePer, 'TableName')">
                <input v-model="model.TableName" type="text" id="TableName" class="form-control" v-if="perEditField(model.TableName, TablePer, 'TableName')"
                       placeholder="Tên bảng chỉ tiêu " name="TableName"/>
                <input type="text" v-else disabled="true" class="form-control" :value="model.TableName"
                       placeholder=""/>
              </div>
              <div class="col-md-16" v-else>
                <input disabled="true" type="text" class="form-control"/>
              </div>
              <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.TableNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0">Loại chỉ tiêu</label>
              <div class="col-md-6" v-if="perEditView(model.IndicatorType, TablePer, 'IndicatorType')">
                <b-form-select
                  v-model="model.IndicatorType"
                  :options="[
                                        {value: 1, text: 'Chỉ tiêu đơn vị'},
                                        {value: 2, text: 'Chỉ tiêu cá nhân'},]"
                  v-if="perEditField(model.IndicatorType, TablePer, 'IndicatorType')"
                >
                </b-form-select>
                <b-form-select v-else disabled
                  v-model="model.IndicatorType"
                  :options="[
                                        {value: 1, text: 'Chỉ tiêu đơn vị'},
                                        {value: 2, text: 'Chỉ tiêu cá nhân'},]"
                >
                </b-form-select>
              </div>
              <div class="col-md-6" v-else>
                <input disabled="true" type="text" class="form-control"/>
              </div>
              <label class="col-md-2 m-0" v-show="model.IndicatorType == 1">Đơn vị</label>
              <div class="col-md-12" v-show="model.IndicatorType == 1" v-if="perEditView(model.CompanyID, TablePer, 'CompanyID')">
                <IjcoreModalListing v-model="model" :title="'đơn vị'" :api="'/listing/api/common/list'"
                                    :table="'company'" :FieldID="'CompanyID'" :FieldName="'CompanyName'"
                                    :FieldNo="'CompanyNo'"
                                    v-if="perEditField(model.CompanyID, TablePer, 'CompanyID')"
                >
                </IjcoreModalListing>
                <input type="text" v-else disabled="true" class="form-control" :value="model.CompanyName"
                       placeholder=""/>
              </div>
              <div class="col-md-12" v-else v-show="model.IndicatorType == 1">
                <input disabled="true" type="text" class="form-control"/>
              </div>
              <label class="col-md-2 m-0" v-show="model.IndicatorType == 2">Nhân viên</label>
              <div class="col-md-6" v-show="model.IndicatorType == 2" v-if="perEditView(model.EmployeeID, TablePer, 'EmployeeID')">
                <IjcoreModalListing v-model="model" :title="'nhân viên'" :api="'/listing/api/common/list'"
                                    :table="'employee'" :FieldID="'EmployeeID'" :FieldName="'EmployeeName'"
                                    :FieldNo="'EmployeeNo'"
                                    v-if="perEditField(model.EmployeeID, TablePer, 'EmployeeID')">
                </IjcoreModalListing>
                <input type="text" v-else disabled="true" class="form-control" :value="model.EmployeeName"
                       placeholder=""/>
              </div>
              <div class="col-md-6" v-else v-show="model.IndicatorType == 2">
                <input disabled="true" type="text" class="form-control"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0">Chu kỳ</label>
              <div class="col-md-4" v-if="perEditView(model.PeriodType, TablePer, 'PeriodType')">
                <b-form-select
                  v-model="model.PeriodType" @change="changePeriodType"
                  :options="[
                                        {value: '', text: 'Chọn chu kỳ'},
                                        {value: 1, text: 'Năm'},
                                        {value: 2, text: '6 tháng'},
                                        {value: 3, text: 'Quý'},
                                        {value: 4, text: 'Tháng'},
                                        {value: 5, text: 'Tuần'},
                                        {value: 6, text: 'Ngày'},]"
                  v-if="perEditField(model.PeriodType, TablePer, 'PeriodType')">
                </b-form-select>
                <b-form-select disabled
                  v-model="model.PeriodType" @change="changePeriodType"
                  :options="[
                                        {value: '', text: 'Chọn chu kỳ'},
                                        {value: 1, text: 'Năm'},
                                        {value: 2, text: '6 tháng'},
                                        {value: 3, text: 'Quý'},
                                        {value: 4, text: 'Tháng'},
                                        {value: 5, text: 'Tuần'},
                                        {value: 6, text: 'Ngày'},]"
                  v-else>
                </b-form-select>
              </div>
              <div class="col-md-3" v-else>
                <input disabled="true" type="text" class="form-control"/>
              </div>
              <label class="col-md-3 m-0">Từ ngày</label>
              <div class="col-md-4">
                <IjcoreDatePicker  style="width: 100%;" v-model="model.FromDate" v-if="perEditField(model.FromDate, TablePer, 'FromDate')&&perEditView(model.FromDate, TablePer, 'FromDate')">
                </IjcoreDatePicker>
                <input disabled="true" type="text" :value="model.FromDate" class="form-control mx-datepicker" v-else-if="!perEditField(model.FromDate, TablePer, 'FromDate')&&perEditView(model.FromDate, TablePer, 'FromDate')"/>
                <input disabled="true" type="text" class="form-control mx-datepicker" v-else/>
              </div>
              <label class="col-md-3 m-0">Đến ngày</label>
              <div class="col-md-4">
                <IjcoreDatePicker style="width: 100%;" v-model="model.ToDate" v-if="perEditField(model.ToDate, TablePer, 'ToDate')&&perEditView(model.ToDate, TablePer, 'ToDate')">
                </IjcoreDatePicker>
                <input disabled="true" type="text" :value="model.ToDate" class="form-control mx-datepicker" v-else-if="!perEditField(model.ToDate, TablePer, 'ToDate')&&perEditView(model.ToDate, TablePer, 'ToDate')"/>
                <input disabled="true" type="text" class="form-control mx-datepicker" v-else/>
              </div>
            </div>
            <table class="table b-table table-sm table-bordered table-editable">
              <thead>
              <tr>
                <th scope="col" style="border-bottom: none;" class="text-center">Phương pháp</th>
                <th scope="col" style="border-bottom: none;" class="text-center">Chỉ tiêu</th>
                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Đơn vị tính</th>
                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Loại chấm điểm</th>
                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Trọng số</th>
                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Tần suất</th>
                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Chỉ số</th>
                <th scope="col" :style="StyleAction">
                  <!--<i @click="onAddFieldOnTable" class="fa fa-plus" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i>--></th>
              </tr>
              </thead>
              <tbody v-if="TablePer.Access">
              <tr v-for="(field, key) in model.IndicatorTableItem" v-bind:RowItem="field.TableItemID">
                <td>
                  <b-form-select
                    v-model="model.IndicatorTableItem[key].EvaluationMethod" @change="setStyleAction"
                    :options="[
                              {value: '', text: 'Chọn phương pháp'},
                              {value: 1, text: 'Chỉ số đo lường hiệu suất'},
                              {value: 2, text: 'Mục tiêu và kết quả then chốt'},
                              {value: 3, text: 'Thẻ điểm cân bằng'},
                              {value: 4, text: 'Bảng điểm'},
                              {value: 5, text: 'Danh sách kiểm tra'},
                              {value: 6, text: 'Phản hồi 360 độ'},
                              {value: 7, text: 'Trắc nghiệm'}]"
                  >
                  </b-form-select>
                </td>
                <td>
                  <IjcoreModalListing
                    v-model="model.IndicatorTableItem[key]" :title="'chỉ tiêu'"  :api="'/listing/api/common/list'"
                    :FieldName="'IndicatorName'" :FieldNo="'IndicatorNo'" :FieldID="'IndicatorID'" :table="'task_indicator'"
                    :FieldUpdate="['EvaluationMethod', 'GradingMethod', 'ScaleRateID']" :FieldWhere="{'EvaluationMethod' : field.EvaluationMethod}"
                    @changed="checkIndicator('IndicatorTableItem', key)"
                  ></IjcoreModalListing>
                </td>
                <td>
                  <b-form-select v-if="TablePer.Edit" v-model="model.IndicatorTableItem[key].UomID" @change="onSelectUom($event, key)"
                                 :options="model.uomOption" id="UomID"></b-form-select>
                  <b-form-select  v-else disabled v-model="model.IndicatorTableItem[key].UomID" @change="onSelectUom($event, key)"
                                 :options="model.uomOption" id="UomID"></b-form-select>
                </td>
                <td>
                  <b-form-select v-if="TablePer.Edit"
                    v-model="model.IndicatorTableItem[key].GradingType"
                    :options="[
                                                  {value: null, text: ''},
                                                  {value: 1, text: 'Điểm thường'},
                                                  {value: 2, text: 'Điểm thưởng'},
                                                  {value: 3, text: 'Điểm phạt'},]">
                  </b-form-select>
                  <b-form-select   v-else disabled
                                 v-model="model.IndicatorTableItem[key].GradingType"
                                 :options="[
                                                  {value: null, text: ''},
                                                  {value: 1, text: 'Điểm thường'},
                                                  {value: 2, text: 'Điểm thưởng'},
                                                  {value: 3, text: 'Điểm phạt'},]">
                  </b-form-select>
                </td>
                <td style="text-align: right">
                  <b-form-input v-if="TablePer.Edit"
                    type="text"
                    v-model="model.IndicatorTableItem[key].ObjectiveRate" style="text-align: right">
                  </b-form-input>
                  <b-form-input   v-else disabled
                                type="text"
                                v-model="model.IndicatorTableItem[key].ObjectiveRate" style="text-align: right">
                  </b-form-input>
                </td>
                <td>
                  <b-form-select v-if="TablePer.Edit"
                    v-model="model.IndicatorTableItem[key].FrequencyType"
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
                  <b-form-select v-else disabled
                                 v-model="model.IndicatorTableItem[key].FrequencyType"
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
                  <b-form-input v-if="TablePer.Edit"
                    type="text"
                    v-model="model.IndicatorTableItem[key].ObjectiveIndex" style="text-align: right"
                  >
                  </b-form-input>
                  <b-form-input v-else disabled
                                type="text"
                                v-model="model.IndicatorTableItem[key].ObjectiveIndex" style="text-align: right"
                  >
                  </b-form-input>
                </td>
                <td class="text-center" style="vertical-align: middle;text-align: right !important;padding-right: 8px;">
                  <i @click="onAddOnKeyResult(model.IndicatorTableItem[key].TableItemID, key)"
                     class="fa fa-external-link ij-icon " title="Kết quả then chốt"
                     v-if="model.IndicatorTableItem[key].EvaluationMethod==2"
                     style="font-size: 18px; cursor: pointer; padding-right: 5px;position: relative; top: 1px;"></i>
                  <i @click="onAddOnFeedback(model.IndicatorTableItem[key].TableItemID, key)"
                     class="fa fa-external-link ij-icon " title="Kết quả phản hồi"
                     v-if="model.IndicatorTableItem[key].EvaluationMethod==6"
                     style="font-size: 18px; cursor: pointer; padding-right: 5px;position: relative; top: 1px;"></i>
                  <i @click="onDeleteFieldOnTable(key)" class="fa fa-trash-o" title="Xóa"  v-if="TablePer.Delete"
                     style="font-size: 18px; cursor: pointer"></i>
                </td>
              </tr>
              </tbody>
            </table>

            <a @click="onAddFieldOnTable(RowItem)" class="new-row" v-if="TablePer.Edit">
              <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
            </a>
          </b-card>
          <b-card v-else>

            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0" for="TableName">Tên</label>
              <div class="col-md-16">
                <input v-model="model.TableName" type="text" id="TableName" class="form-control"
                       placeholder="Tên bảng chỉ tiêu " name="TableName"/>
              </div>
              <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.TableNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0">Loại chỉ tiêu</label>
              <div class="col-md-6">
                <b-form-select
                  v-model="model.IndicatorType"
                  :options="[
                                        {value: 1, text: 'Chỉ tiêu đơn vị'},
                                        {value: 2, text: 'Chỉ tiêu cá nhân'},]">
                </b-form-select>
              </div>
              <label class="col-md-2 m-0" v-show="model.IndicatorType == 1">Đơn vị</label>
              <div class="col-md-12" v-show="model.IndicatorType == 1">
                <IjcoreModalListing v-model="model" :title="'đơn vị'" :api="'/listing/api/common/list'"
                                    :table="'company'" :FieldID="'CompanyID'" :FieldName="'CompanyName'"
                                    :FieldNo="'CompanyNo'">
                </IjcoreModalListing>
              </div>
              <label class="col-md-2 m-0" v-show="model.IndicatorType == 2">Nhân viên</label>
              <div class="col-md-6" v-show="model.IndicatorType == 2">
                <IjcoreModalListing v-model="model" :title="'nhân viên'" :api="'/listing/api/common/list'"
                                    :table="'employee'" :FieldID="'EmployeeID'" :FieldName="'EmployeeName'"
                                    :FieldNo="'EmployeeNo'">
                </IjcoreModalListing>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0">Chu kỳ</label>
              <div class="col-md-4">
                <b-form-select
                  v-model="model.PeriodType" @change="changePeriodType"
                  :options="[
                                        {value: '', text: 'Chọn chu kỳ'},
                                        {value: 1, text: 'Năm'},
                                        {value: 2, text: '6 tháng'},
                                        {value: 3, text: 'Quý'},
                                        {value: 4, text: 'Tháng'},
                                        {value: 5, text: 'Tuần'},
                                        {value: 6, text: 'Ngày'},]">
                </b-form-select>
              </div>
              <label class="col-md-3 m-0">Từ ngày</label>
              <div class="col-md-4">
                <IjcoreDatePicker v-model="model.FromDate" style="width: 100% !important;">
                </IjcoreDatePicker>
              </div>
              <label class="col-md-3 m-0">Đến ngày</label>
              <div class="col-md-4">
                <IjcoreDatePicker v-model="model.ToDate" style="width: 100% !important;">
                </IjcoreDatePicker>
              </div>
            </div>

            <table class="table b-table table-sm table-bordered table-editable">
              <thead>
              <tr>
                <th scope="col" style="border-bottom: none;" class="text-center">Phương pháp</th>
                <th scope="col" style="border-bottom: none;" class="text-center">Chỉ tiêu</th>
                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Đơn vị tính</th>
                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Loại chấm điểm</th>
                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Trọng số</th>
                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Tần suất</th>
                <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Chỉ số</th>
                <th scope="col"  :style="StyleAction">
                  <!--<i @click="onAddFieldOnTable" class="fa fa-plus" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i>--></th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(field, key) in model.IndicatorTableItem" v-bind:RowItem="field.TableItemID">
                <td>
                  <b-form-select @change="setStyleAction"
                    v-model="model.IndicatorTableItem[key].EvaluationMethod"
                    :options="[
                              {value: '', text: 'Chọn phương pháp'},
                              {value: 1, text: 'Chỉ số đo lường hiệu suất'},
                              {value: 2, text: 'Mục tiêu và kết quả then chốt'},
                              {value: 3, text: 'Thẻ điểm cân bằng'},
                              {value: 4, text: 'Bảng điểm'},
                              {value: 5, text: 'Danh sách kiểm tra'},
                              {value: 6, text: 'Phản hồi 360 độ'},
                              {value: 7, text: 'Trắc nghiệm'}]"
                  >
                  </b-form-select>
                </td>
                <td>
                  <IjcoreModalListing
                    v-model="model.IndicatorTableItem[key]" :title="'chỉ tiêu'"  :api="'/listing/api/common/list'"
                    :FieldName="'IndicatorName'" :FieldNo="'IndicatorNo'" :FieldID="'IndicatorID'" :table="'task_indicator'"
                    :FieldUpdate="['EvaluationMethod', 'GradingMethod', 'ScaleRateID']" :FieldWhere="{'EvaluationMethod' : field.EvaluationMethod}"
                    @changed="checkIndicator('IndicatorTableItem', key)"
                  ></IjcoreModalListing>
                </td>
                <td>
                  <b-form-select v-model="model.IndicatorTableItem[key].UomID" @change="onSelectUom($event, key)"
                                 :options="model.uomOption" id="UomID"></b-form-select>
                </td>
                <td>
                  <b-form-select
                    v-model="model.IndicatorTableItem[key].GradingType"
                    :options="[
                                                  {value: null, text: ''},
                                                  {value: 1, text: 'Điểm thường'},
                                                  {value: 2, text: 'Điểm thưởng'},
                                                  {value: 3, text: 'Điểm phạt'},]">
                  </b-form-select>
                </td>
                <td style="text-align: right">
                  <b-form-input
                    type="text"
                    v-model="model.IndicatorTableItem[key].ObjectiveRate" style="text-align: right">
                  </b-form-input>
                </td>
                <td>
                  <b-form-select
                    v-model="model.IndicatorTableItem[key].FrequencyType"
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
                    v-model="model.IndicatorTableItem[key].ObjectiveIndex" style="text-align: right"
                  >
                  </b-form-input>
                </td>

                <td class="text-center" style="vertical-align: middle;text-align: right !important;padding-right: 8px;">
                  <i @click="onAddOnKeyResult(model.IndicatorTableItem[key].TableItemID, key)"
                     class="fa fa-external-link ij-icon " title="Kết quả then chốt"
                     v-if="model.IndicatorTableItem[key].EvaluationMethod==2"
                     style="font-size: 18px; cursor: pointer; padding-right: 5px;position: relative; top: 1px;"></i>
                  <i @click="onAddOnFeedback(model.IndicatorTableItem[key].TableItemID, key)"
                     class="fa fa-external-link ij-icon " title="Kết quả phản hồi"
                     v-if="model.IndicatorTableItem[key].EvaluationMethod==6"
                     style="font-size: 18px; cursor: pointer; padding-right: 5px;position: relative; top: 1px;"></i>
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
             title="Bảng chỉ tiêu ĐGCV – Kết quả then chốt ">
      <div class="main-body pb-5 pt-10">
        <table class="table b-table table-sm table-bordered table-editable">
          <thead>
          <tr>
            <th scope="col" style="border-bottom: none;" class="text-center">Chỉ tiêu</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Đơn vị tính</th>
            <th scope="col" style="width: 13%; border-bottom: none;" class="text-center">Loại chấm điểm</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Trọng số</th>
            <th scope="col" style="border-bottom: none;" class="text-center">Ghi chú</th>
            <th scope="col" style="width: 3%; border-bottom: none;" class="text-center">
              <!--<i @click="onAddFieldOnTable" class="fa fa-plus" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i>--></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(field, key) in model.IndicatorTableItemKeyresult[TableItemIDCurrent]">
            <td>
              <IjcoreModalListing
                v-model="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key]" :title="'chỉ tiêu'"  :api="'/listing/api/common/list'"
                :FieldName="'IndicatorName'" :FieldNo="'IndicatorNo'" :FieldID="'IndicatorID'" :table="'task_indicator'"
                :FieldUpdate="['EvaluationMethod', 'GradingMethod', 'ScaleRateID']" :FieldWhere="{'EvaluationMethod' : model.IndicatorTableItem[TableItemKeyCurrent]['EvaluationMethod']}"
                @changed="checkIndicatorItem('IndicatorTableItemKeyresult', key)"
              ></IjcoreModalListing>
            </td>
            <td>
              <b-form-select v-if="TablePer.Edit||!idParams" v-model="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].UomID"
                             @change="onSelectUom($event, key)"
                             :options="model.uomOption" id="UomID"></b-form-select>
              <b-form-select v-else disabled v-model="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].UomID"
                             @change="onSelectUom($event, key)"
                             :options="model.uomOption" id="UomID"></b-form-select>
            </td>
            <td style="position: relative">
              <b-form-select v-if="TablePer.Edit||!idParams"
                v-model="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].KeyresultType"
                :options="[
                                                  {value: null, text: ''},
                                                  {value: 1, text: 'Khối lượng'},
                                                  {value: 2, text: 'Bảng điểm'},
                                                  {value: 3, text: 'Nhị phân'},]"
                @change="onChangeKeyresultType($event, key, TableItemIDCurrent)">
              </b-form-select>
              <b-form-select v-else disabled
                v-model="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].KeyresultType"
                :options="[
                                                  {value: null, text: ''},
                                                  {value: 1, text: 'Khối lượng'},
                                                  {value: 2, text: 'Bảng điểm'},
                                                  {value: 3, text: 'Nhị phân'},]"
                @change="onChangeKeyresultType($event, key, TableItemIDCurrent)">
              </b-form-select>

              <IjcoreKeyResultType v-model="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key]"
                                   :title="'Bảng điểm'" :api="'/listing/api/common/list'"
                                   :table="'task_scale_rate'" :FieldID="'ScaleRateID'" :FieldName="'ScaleRateName'"
                                   :FieldNameUpdate="'KeyresultName'"
                                   v-show="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].KeyresultType == 2"
                                   style="position: absolute; top: 9px; right: 30px;"
                                   :FirstShow="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].ScaleRate"
              >
              </IjcoreKeyResultType>
              <IjcoreKeyResultType v-model="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key]"
                                   :title="'Nhị phân'" :api="'/listing/api/common/list'"
                                   :table="'sys_binary_data'" :FieldID="'BinaryDataID'" :FieldName="'BinaryDataName'"
                                   :FieldNameUpdate="'KeyresultName'"
                                   v-show="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].KeyresultType == 3"
                                   style="position: absolute; top: 9px; right: 30px;"
                                   :FirstShow="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].BinaryData"
              >
              </IjcoreKeyResultType>
            </td>
            <td>
              <b-form-input v-if="TablePer.Edit||!idParams"
                type="text"
                v-model="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].Rate"
              >
              </b-form-input>
              <b-form-input v-else disabled
                type="text"
                v-model="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].Rate"
              >
              </b-form-input>
            </td>
            <td>
              <b-form-input v-if="TablePer.Edit||!idParams"
                type="text"
                v-model="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].Description"
              >
              </b-form-input>
              <b-form-input v-else disabled
                type="text"
                v-model="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].Description"
              >
              </b-form-input>
            </td>
            <td class="text-center">
              <i v-if="TablePer.Edit||!idParams" @click="onDeleteFieldOnKeyResult(key)" class="fa fa-trash-o" title="Xóa"
                 style="font-size: 18px; cursor: pointer"></i>
              <i v-else disabled class="fa fa-trash-o" title="Xóa"
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

    <!-- Popup Add Feedback -->
    <b-modal ref="Feedback" id="modal-form-input-task-general1" size="xl"
             title="Bảng chỉ tiêu ĐGCV - kết quả phản hồi">
      <div class="main-body pb-5 pt-10">
        <table class="table b-table table-sm table-bordered table-editable">
          <thead>
          <tr>
            <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Chỉ tiêu</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Là kết quả</th>
            <th scope="col" style="width: 15%; border-bottom: none;" class="text-center">Kiểu kết quả</th>
            <th scope="col" style="border-bottom: none;" class="text-center">Ghi chú</th>
            <th scope="col" style="width: 3%; border-bottom: none;" class="text-center">
              <!--<i @click="onAddFieldOnTable" class="fa fa-plus" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i>--></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(field, key) in model.IndicatorTableItemFeedback[TableItemIDCurrent]">
            <td>
              <IjcoreModalListing
                v-model="model.IndicatorTableItemFeedback[TableItemIDCurrent][key]" :title="'chỉ tiêu'"  :api="'/listing/api/common/list'"
                :FieldName="'IndicatorName'" :FieldNo="'IndicatorNo'" :FieldID="'IndicatorID'" :table="'task_indicator'"
                :FieldUpdate="['EvaluationMethod', 'GradingMethod', 'ScaleRateID']" :FieldWhere="{'EvaluationMethod' : model.IndicatorTableItem[TableItemKeyCurrent]['EvaluationMethod']}"
                @changed="checkIndicatorItem('IndicatorTableItemFeedback', key)"
              ></IjcoreModalListing>
            </td>
            <td class="text-center">
              <b-form-checkbox v-if="TablePer.Edit||!idParams"
                type="check"
                v-model="model.IndicatorTableItemFeedback[TableItemIDCurrent][key].IsBinaryData==1"
                @change="checktoBinaryDataID(key,TableItemIDCurrent)">
              </b-form-checkbox>
              <b-form-checkbox v-else disabled
                type="check"
                v-model="model.IndicatorTableItemFeedback[TableItemIDCurrent][key].IsBinaryData==1"
                @change="checktoBinaryDataID(key,TableItemIDCurrent)">
              </b-form-checkbox>
            </td>
            <td>
              <b-form-select
                v-if="model.IndicatorTableItemFeedback[TableItemIDCurrent][key].IsBinaryData==1&&(TablePer.Edit||!idParams)"
                @change="changeBinaryData($event, key, TableItemIDCurrent)"
                v-model="model.IndicatorTableItemFeedback[TableItemIDCurrent][key].BinaryDataID"
                :options="model.BinaryDataOption" id="BinaryDataID"
              ></b-form-select>
              <b-form-select disabled
                v-if="model.IndicatorTableItemFeedback[TableItemIDCurrent][key].IsBinaryData==1&&!(TablePer.Edit||!idParams)"
                @change="changeBinaryData($event, key, TableItemIDCurrent)"
                v-model="model.IndicatorTableItemFeedback[TableItemIDCurrent][key].BinaryDataID"
                :options="model.BinaryDataOption" id="BinaryDataID"
              ></b-form-select>
            </td>

            <td>
              <b-form-input v-if="TablePer.Edit"
                type="text"
                v-model="model.IndicatorTableItemFeedback[TableItemIDCurrent][key].Description">
              </b-form-input>
              <b-form-input v-else disabled
                type="text"
                v-model="model.IndicatorTableItemFeedback[TableItemIDCurrent][key].Description">
              </b-form-input>
            </td>
            <td class="text-center">
              <i  v-if="TablePer.Edit||!idParams" @click="onDeleteFieldOnFeedback(key)" class="fa fa-trash-o" title="Xóa"
                 style="font-size: 18px; cursor: pointer"></i>
              <i class="fa fa-trash-o" title="Xóa" v-else disabled
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
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
  import IjcoreModalIndicatorTemp from "../../../../components/IjcoreModalIndicatorTemp";
  import IjcoreKeyResultType from "../../../../components/IjcoreModalKeyResultType";

  moment.locale('vi');


  const ListRouter = 'task-indicator-table';
  const EditRouter = 'task-indicator-table-edit';
  const CreateRouter = 'task-indicator-table-create';
  const ViewRouter = 'task-indicator-table-view';
  const DetailApi = 'task/api/indicator-table/view';
  const EditApi = 'task/api/indicator-table/edit';
  const CreateApi = 'task/api/indicator-table/create';
  const StoreApi = 'task/api/indicator-table/store';
  const UpdateApi = 'task/api/indicator-table/update';
  const ListApi = 'task/api/indicator-table';

  export default {
    name: 'task-indicator-table-form',
    data() {
      return {
        StyleAction: 'width: 32px; border-bottom: none;',
        isForm: false,
        idParams: this.idParamsProps,
        reqParams: this.reqParamsProps,
        TableItemIDCurrent: -1,
        TableItemKeyCurrent: -1,
        RowItem: 0,
        model: {
          TableID: '',
          TableNo: '',
          TableName: '',
          IndicatorType: 1,
          CompanyID: '',
          CompanyNo: '',
          CompanyName: '',
          EmployeeID: '',
          EmployeeNo: '',
          EmployeeName: '',
          PeriodType: '',
          FromDate: '',
          ToDate: '',
          AccessType: '',
          Locked: '',
          Inactive: '',
          IndicatorTableItem: [],
          IndicatorTableItemKeyresult: [],
          IndicatorTableItemFeedback: [],
          uomOption: [],
          indicatorOption: [],
          BinaryDataOption: [],
        },
        TablePer: {},
        IndicatorTemp: {
          TemplateID: '',
          TemplateName: ''
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
      IjcoreKeyResultType,
      IjcoreModalIndicatorTemp,
      IjcoreDatePicker,
      IjcoreModalListing,
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
        for (let i = 0; i < this.model[KeyModel][this.TableItemIDCurrent].length; i++) {
          console.log()
          if(this.model[KeyModel][this.TableItemIDCurrent][i].IndicatorID == this.model[KeyModel][this.TableItemIDCurrent][Key].IndicatorID){
            k++;
            if(k==2){
              Swal.fire(
                'Thông báo',
                'Đã tồn tại chỉ tiêu đánh giá này!',
                'error'
              );
              this.model[KeyModel][this.TableItemIDCurrent][Key].IndicatorID = '';
              this.model[KeyModel][this.TableItemIDCurrent][Key].IndicatorNo = '';
              this.model[KeyModel][this.TableItemIDCurrent][Key].IndicatorName = '';
              this.$forceUpdate()
              return true;
            }
          }
        }
      },
      setStyleAction(){
        for (let i = 0; i < this.model.IndicatorTableItem.length; i++) {
          if(this.model.IndicatorTableItem[i].EvaluationMethod == 2 || this.model.IndicatorTableItem[i].EvaluationMethod == 6){
            this.StyleAction = "width: 60px; border-bottom: none;";
            return true;
          }
        }
        this.StyleAction = 'width: 32px; border-bottom: none;';
        return true;
      },
      perEditView(value, per, field) {
        var AccessField = ','+per['AccessField']+',';
        if(per['AccessField'] == 'all' || AccessField.includes(','+field+',')){
          return true;
        }else{
          return false;
        }
      },
      perEditField(value, per, field){
        var EditField = ','+per['EditField']+',';
        if(per['EditField'] == 'all' || EditField.includes(','+field+',')){
          return true;
        }else{
          return false;
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
      changeTemplate() {
        let self = this;
        let urlApi = CreateApi;
        let requestData = {
          method: 'get',
          data: {}
        };
        requestData.url = 'task/api/indicator-table/load-temp/'+self.IndicatorTemp.TemplateID;
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.model.TableName = responsesData.data.data.TemplateName;
            self.model.IndicatorType = responsesData.data.data.IndicatorType;
            this.model.IndicatorTableItem = [];
            _.forEach(responsesData.data.IndicatorTempItem, function (field, key) {
              let fieldObj = {};
              self.RowItem = field.TransItemID;
              fieldObj.TableItemID = self.RowItem;
              fieldObj.Description = field.Description;
              fieldObj.IndicatorID = field.IndicatorID;
              fieldObj.IndicatorNo = field.IndicatorNo;
              fieldObj.IndicatorName = field.IndicatorName;
              fieldObj.UomID = field.UomID;
              fieldObj.UomName = field.UomName;
              fieldObj.GradingType = field.GradingType;
              fieldObj.EvaluationMethod = field.EvaluationMethod;
              fieldObj.ObjectiveRate = field.Rate;
              fieldObj.FrequencyType = field.FrequencyType;
              fieldObj.ObjectiveIndex = '';
              fieldObj.LineIDTemp = self.RowItem;
              self.RowItem = self.RowItem + 1;
              self.model.IndicatorTableItem.push(fieldObj);
            });

            _.forEach(responsesData.data.IndicatorTempItemKeyResult, function (field, key) {
              let fieldObj = {};
              fieldObj.TableItemID = field.TransItemID;
              fieldObj.ScaleRateID = field.ScaleRateID;
              fieldObj.BinaryDataID = field.BinaryDataID;
              fieldObj.Description = field.Description;
              fieldObj.IndicatorID = field.IndicatorID;
              fieldObj.IndicatorNo = field.IndicatorNo;
              fieldObj.IndicatorName = field.IndicatorName;
              fieldObj.KeyresultType = field.KeyresultType;
              fieldObj.KeyresultName = field.KeyresultName;
              fieldObj.UomID = field.UomID;
              fieldObj.UomName = field.UomName;
              fieldObj.Rate = field.Rate;
              fieldObj.LineIDTemp = self.TableItemIDCurrent;

              if (self.model.IndicatorTableItemKeyresult[fieldObj.TableItemID] == undefined) {
                self.model.IndicatorTableItemKeyresult[fieldObj.TableItemID] = [];
              }
              self.model.IndicatorTableItemKeyresult[fieldObj.TableItemID].push(fieldObj);
            });
            _.forEach(responsesData.data.IndicatorTempItemFeedback, function (field, key) {
              let fieldObj = {};
              fieldObj.TableItemID = field.TransItemID;
              fieldObj.ScaleRateID = field.ScaleRateID;
              fieldObj.BinaryDataID = field.BinaryDataID;
              fieldObj.Description = field.Description;
              fieldObj.IndicatorID = field.IndicatorID;
              fieldObj.IndicatorNo = field.IndicatorNo;
              fieldObj.IndicatorName = field.IndicatorName;
              fieldObj.FeedbackName = field.FeedbackName;
              fieldObj.IsBinaryData = field.isBinaryData;
              fieldObj.LineIDTemp = self.TransItemID;
              if (self.model.IndicatorTableItemFeedback[fieldObj.TableItemID] == undefined) {
                self.model.IndicatorTableItemFeedback[fieldObj.TableItemID] = [];
              }
              self.model.IndicatorTableItemFeedback[fieldObj.TableItemID].push(fieldObj)
            });
          }
          this.setStyleAction()
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
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
                self.model.TableName = responsesData.data.data.TableName;
                self.model.TableNo = responsesData.data.data.TableNo;
                self.model.IndicatorType = responsesData.data.data.IndicatorType;
                self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
                self.model.FromDate = __.convertDateToString(responsesData.data.data.FromDate);
                self.model.ToDate = __.convertDateToString(responsesData.data.data.ToDate);
                self.model.PeriodType = responsesData.data.data.PeriodType;

                self.model.EmployeeID = responsesData.data.data.EmployeeID;
                self.model.EmployeeNo = responsesData.data.data.EmployeeNo;
                self.model.EmployeeName = responsesData.data.data.EmployeeName;

                self.model.CompanyID = responsesData.data.data.CompanyID;
                self.model.CompanyNo = responsesData.data.data.CompanyNo;
                self.model.CompanyName = responsesData.data.data.CompanyName;
                self.TablePer = responsesData.data.TablePer;
                _.forEach(responsesData.data.IndicatorTableItemKeyResult, function (field, key) {
                  if (self.model.IndicatorTableItemKeyresult[field.TableItemID] == undefined) {
                    field.LineIDTemp = field.TableItemID;
                    self.model.IndicatorTableItemKeyresult[field.TableItemID] = [];
                  }
                  self.model.IndicatorTableItemKeyresult[field.TableItemID].push(field)
                });
                _.forEach(responsesData.data.IndicatorTableItemFeedback, function (field, key) {
                    if (self.model.IndicatorTableItemFeedback[field.TableItemID] == undefined) {
                      field.LineIDTemp = field.TableItemID;
                      self.model.IndicatorTableItemFeedback[field.TableItemID] = [];
                    }
                    self.model.IndicatorTableItemFeedback[field.TableItemID].push(field)
                });

                // _.forEach(responsesData.data.IndicatorTempItem, function (field, key) {
                //   responsesData.data.IndicatorTempItem[key].LineIDTemp = responsesData.data.IndicatorTempItem[key].TableItemID;
                // });
                self.model.IndicatorTable = responsesData.data.IndicatorTable;
                self.model.IndicatorTableItem = responsesData.data.IndicatorTableItem;
              }
              if (!_.isEmpty(self.itemCopy)) {
                self.model.TableNo = responsesData.data.data.TableNo;
              }
            } else {
              self.model.TableNo = responsesData.data.auto;
            }

            if (_.isArray(responsesData.data.Uom)) {

              self.model.uomOption = [
                {value: '', text: 'Chọn'}
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
                {value: '', text: 'Chọn'}
              ];
              _.forEach(responsesData.data.BinaryData, function (value, key) {
                let tmpObj = {};
                tmpObj.value = value.BinaryDataID;
                tmpObj.text = value.BinaryDataName;
                self.model.BinaryDataOption.push(tmpObj);
              });
            }


          }

          this.setStyleAction()
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

      onSelectUom(value, key) {
        let uom = _.find(this.model.uomOption, ['value', value]);
        this.model.IndicatorTableItem[key].UomName = uom.text;
      },
      checktoBinaryDataID(key, TableItemIDCurrent){
        if(this.model.IndicatorTableItemFeedback[TableItemIDCurrent][key].IsBinaryData == 1){
          this.model.IndicatorTableItemFeedback[TableItemIDCurrent][key].IsBinaryData = 0;
        }else{
          this.model.IndicatorTableItemFeedback[TableItemIDCurrent][key].IsBinaryData = 1;
        }
        this.model.IndicatorTableItemFeedback[TableItemIDCurrent][key].BinaryDataID = '';
        this.$forceUpdate()
      },
      changeBinaryData(value, key, TableItemIDCurrent) {
        let BinaryData = _.find(this.model.BinaryDataOption, ['value', value]);
        this.model.IndicatorTableItemFeedback[TableItemIDCurrent][key].FeedbackName = BinaryData.text;
        this.$forceUpdate()
      },
      onChangeKeyresultType(value, key, TableItemIDCurrent) {
        this.$set(this.model.IndicatorTableItemKeyresult, TableItemIDCurrent, this.model.IndicatorTableItemKeyresult[TableItemIDCurrent]);
        this.model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].ScaleRate = 0;
        this.model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].BinaryData = 0;
        if(this.model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].KeyresultType == 2){
          this.model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].ScaleRate = 1;
          this.model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].BinaryData = 0;
        }
        if(this.model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].KeyresultType == 3){
          this.model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].BinaryData = 1;
          this.model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].ScaleRate = 0;
        }
        this.$forceUpdate()
      },
      onAddFieldOnTable(RowItem) {
        let fieldObj = {};
        fieldObj.TableItemID = RowItem;
        fieldObj.Description = '';
        fieldObj.IndicatorID = '';
        fieldObj.IndicatorNo = '';
        fieldObj.IndicatorName = '';
        fieldObj.UomID = '';
        fieldObj.UomName = '';
        fieldObj.EvaluationMethod = '';
        fieldObj.GradingType = 1;
        fieldObj.ObjectiveRate = '';
        fieldObj.FrequencyType = 1;
        fieldObj.ObjectiveIndex = '';
        fieldObj.LineIDTemp = RowItem;
        this.RowItem = RowItem + 1;
        this.model.IndicatorTableItem.push(fieldObj);
        this.$forceUpdate();
      },
      onAddFieldOnKeyResult() {
        let fieldObj = {};
        fieldObj.ScaleRateID = '';
        fieldObj.BinaryDataID = '';
        fieldObj.Description = '';
        fieldObj.KeyresultType = '';
        fieldObj.KeyresultName = '';
        fieldObj.IndicatorID = '';
        fieldObj.IndicatorNo = '';
        fieldObj.IndicatorName = '';
        fieldObj.EvaluationMethod = this.model.IndicatorTableItem[this.TableItemKeyCurrent].EvaluationMethod;
        fieldObj.UomID = null;
        fieldObj.UomName = null;
        fieldObj.Rate = '';
        fieldObj.LineIDTemp = this.TableItemIDCurrent;
        if (this.model.IndicatorTableItemKeyresult[this.TableItemIDCurrent] == undefined) {
          this.model.IndicatorTableItemKeyresult[this.TableItemIDCurrent] = [];
        }
        this.model.IndicatorTableItemKeyresult[this.TableItemIDCurrent].push(fieldObj);
        this.$forceUpdate();
      },
      onAddFieldOnFeedback() {
        let fieldObj = {};
        fieldObj.ScaleRateID = '';
        fieldObj.BinaryDataID = '';
        fieldObj.Description = '';
        fieldObj.FeedbackName = '';
        fieldObj.IsBinaryData = '';
        fieldObj.IndicatorID = '';
        fieldObj.IndicatorNo = '';
        fieldObj.IndicatorName = '';
        fieldObj.EvaluationMethod = this.model.IndicatorTableItem[this.TableItemKeyCurrent].EvaluationMethod;
        fieldObj.LineIDTemp = this.TableItemIDCurrent;

        if (this.model.IndicatorTableItemFeedback[this.TableItemIDCurrent] == undefined) {
          this.model.IndicatorTableItemFeedback[this.TableItemIDCurrent] = [];
        }
        this.model.IndicatorTableItemFeedback[this.TableItemIDCurrent].push(fieldObj);
        this.$forceUpdate();
      },
      onDeleteFieldOnTable(key) {
        if (this.model.EvaluationMethod == 2) {
          this.model.IndicatorTableItemKeyresult.splice(this.model.IndicatorTableItem[key].LineIDTemp, 1)
        }
        if (this.model.EvaluationMethod == 6) {
          this.model.IndicatorTableItemFeedback.splice(this.model.IndicatorTableItem[key].LineIDTemp, 1)
        }
        this.model.IndicatorTableItem.splice(key, 1)
        //
        this.setStyleAction()
        this.$forceUpdate();
      },
      onDeleteFieldOnKeyResult(key) {
        // remove field in fieldOnTableReq
        this.model.IndicatorTableItemKeyresult[this.TableItemIDCurrent].splice(key, 1)
        this.$forceUpdate();
      },
      onDeleteFieldOnFeedback(key) {
        // remove field in fieldOnTableReq
        this.model.IndicatorTableItemFeedback[this.TableItemIDCurrent].splice(key, 1)
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
                TableNo: this.model.TableNo,
                TableName: this.model.TableName,
                IndicatorType: this.model.IndicatorType,
                Inactive: (this.model.Inactive) ? 1 : 0,
                PeriodType: this.model.PeriodType,
                FromDate: this.model.FromDate,
                ToDate: this.model.ToDate,
                CompanyID: this.model.CompanyID,
                CompanyNo: this.model.CompanyNo,
                CompanyName: this.model.CompanyName,
                EmployeeID: this.model.EmployeeID,
                EmployeeNo: this.model.EmployeeNo,
                EmployeeName: this.model.EmployeeName,
              },
              detail: this.model.IndicatorTableItem,
              IndicatorTableItemKeyresult: this.model.IndicatorTableItemKeyresult,
              IndicatorTableItemFeedback: this.model.IndicatorTableItemFeedback
            }
          };
          // edit user
          if (this.idParams) {
            requestData.data.master.TemplateID = this.idParams;
            requestData.url = UpdateApi + '/' + this.idParams;
          }
          this.$store.commit('isLoading', true);
          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;
             console.log(responsesData)
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
      onAddOnKeyResult(TableItemID, Key) {
        this.TableItemIDCurrent = TableItemID;
        this.TableItemKeyCurrent = Key;
        this.$forceUpdate();
        this.$refs['KeyResult'].show();
      },
      onHideKeyResult() {
        this.isForm = false;
        this.$refs['KeyResult'].hide();
      },
      onAddOnFeedback(TableItemID, Key) {
        this.TableItemIDCurrent = TableItemID;
        this.TableItemKeyCurrent = Key;
        this.$forceUpdate();
        this.$refs['Feedback'].show();
      },
      onHideFeedback() {
        this.isForm = false;
        this.$refs['Feedback'].hide();
      },
      onSaveKeyResult() {
        this.$bvToast.toast('Đã lưu kết quả then chốt\n', {
          title: 'Thông báo',
          variant: 'success',
          solid: true
        });
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
    #modal-form-input-task-general .modal-lg{
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

  .fromdate-todate .mx-datepicker {
    width: 50%;
  }
</style>
