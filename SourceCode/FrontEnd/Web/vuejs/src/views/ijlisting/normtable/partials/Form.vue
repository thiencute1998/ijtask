<template>
  <div class="main-entry component-norm-table-form">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Định mức dự toán<span v-if="model.NormTableName">:</span> {{model.NormTableName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Định mức dự toán<span v-if="model.NormTableName">:</span> {{model.NormTableName}}</span>
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
              <b-dropdown id="dropdown-per-page" title="Số bản ghi/trang" menu-class="p-0" :text="perPage" class="app-dropdown-center main-header-icon">
                <b-dropdown-item @click="changePerPage(10)">10</b-dropdown-item>
                <b-dropdown-item @click="changePerPage(15)">15</b-dropdown-item>
                <b-dropdown-item @click="changePerPage(20)">20</b-dropdown-item>
                <b-dropdown-item @click="changePerPage(30)">30</b-dropdown-item>
                <b-dropdown-item @click="changePerPage(40)">40</b-dropdown-item>
                <b-dropdown-item @click="changePerPage(50)">50</b-dropdown-item>
              </b-dropdown>
              <div class="main-header-collapse" id="main-header-collapse">
                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen="true"/>
              </div>
            </div>
          </b-col>
        </b-row>
      </div>

    </div>
    <div class="main-body main-body-view-action">
      <div class="container-fluid h-100">
          <b-card class="h-100" body-class="h-100">
            <div class="d-flex flex-column h-100">
              <div class="form-group row align-items-center mb-3">
                <label class="col-md-3 m-0" for="NormTableName" title="Tên định mức dự toán">Tên bảng ĐMDTCT</label>
                <div class="col-md-18">
                  <input v-model="model.NormTableName" type="text" id="NormTableName" class="form-control" placeholder="Tên định mức dự toán" name="NormTableName"/>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                  <span>Mã số</span>
                  <input v-model="model.NormTableNo" type="text" class="form-control" placeholder="Mã số"/>
                </div>
              </div>
              <div class="form-group row align-items-center">
                <label class="col-md-3 m-0" title="Loại định mức dự toán">Loại ĐMDT</label>
                <div class="col-md-5">
                  <b-form-select v-model="model.NormType" :options="[
                    {value: 1, text: 'Định mức cơ sở'},
                    {value: 2, text: 'Định mức của ĐVSDNS'}
                    ]"></b-form-select>
                </div>
                <label class="col-md-3 m-0">Ngày ban hành</label>
                <div class="col-md-3">
                  <ijcore-date-picker style="width: 100%;" v-model="model.NormTableDate"></ijcore-date-picker>
                </div>
                <label class="col-md-3 m-0">Ngày hiệu lực</label>
                <div class="col-md-3">
                  <ijcore-date-picker style="width: 100%;" v-model="model.EffectiveDate"></ijcore-date-picker>
                </div>
              </div>
              <div class="form-group row align-items-center">
                <label class="col-md-3 m-0">Kỳ</label>
                <div class="col-md-3">
                  <b-form-select
                    v-model="model.PeriodID" @change="changePeriodType"
                    :options="PeriodOption">
                  </b-form-select>
                </div>
                <div class="col-4" v-if="model.PeriodID !== 99">
                  <b-form-select
                    v-if="model.PeriodID !== 5 && model.PeriodID !== 99"
                    v-model="model.PeriodValue" @change="changePeriodValue"
                    :options="PeriodValueOption">
                  </b-form-select>
                  <ijcore-date-picker v-model="model.FromDate" style="width: 100%;" v-if="model.PeriodID === 5" @input-date-picker="changeFromDate"></ijcore-date-picker>
                </div>

                <label class="col-md-2 m-0" v-if="model.PeriodID === 99">Từ ngày</label>
                <div class="col-md-3" v-if="model.PeriodID === 99">
                  <ijcore-date-picker v-model="model.FromDate" style="width: 100%;"></ijcore-date-picker>
                </div>
                <label class="col-md-2 m-0" v-if="model.PeriodID === 99">Đến ngày</label>
                <div class="col-md-3" v-if="model.PeriodID === 99">
                  <ijcore-date-picker v-model="model.ToDate" style="width: 100%;"></ijcore-date-picker>
                </div>

                <label class="col-md-3 m-0">Ngày hết hiệu lực</label>
                <div class="col-md-3">
                  <ijcore-date-picker v-model="model.ExpirationDate" style="width: 100%;"></ijcore-date-picker>
                </div>
              </div>
              <div class="table-responsive b-table-sticky-header b-table-sticky-custom m-0" style="max-height: none">
                <table class="table b-table table-sm table-bordered table-editable table-tree table-column-resizable">
                  <thead>
                  <tr>
                    <th style="width: 5%; min-width: 50px; background: #fff"></th>
                    <th style="width: 5%; min-width: 50px; background: #fff">STT</th>
                    <th scope="col" style="width: 5%; min-width: 65px; background: #fff" class="text-center" title="Thu/Chi">Loại</th>
                    <th scope="col" style="width: 6%; min-width: 100px; background: #fff; z-index: 12 !important;" class="text-center" title="Chỉ tiêu định mức phân bổ dự toán">ĐMPBDT</th>
                    <th scope="col" style="width: 6%; min-width: 100px; background: #fff; z-index: 12 !important;" class="text-center" title="Chỉ tiêu dự toán">CTDT</th>
                    <th scope="col" style="width: 6%; min-width: 100px; background: #fff" class="text-center b-r-0">Mã số</th>
                    <th scope="col" style="min-width: 400px; background: #fff; z-index: 12 !important;" class="text-center b-table-sticky-column"><div>Tên chỉ tiêu</div></th>
                    <th scope="col" style="width: 8%; min-width: 100px; background: #fff; z-index: 11" class="text-center">Đơn vị tính</th>
                    <th scope="col" style="width: 4%; min-width: 100px; background: #fff; z-index: 11" class="text-center">Tiền tệ</th>
                    <th scope="col" style="width: 10%; background: #fff" class="text-center">Tỷ giá</th>
                    <th scope="col" style="width: 4%; min-width: 30px; background: #fff" class="text-center" v-if="stage.reCalculator"></th>
                    <th scope="col" style="width: 10%; min-width: 300px; background: #fff" class="text-center">Công thức</th>
                    <th scope="col" style="width: 10%; min-width: 120px; background: #fff" class="text-center">Số lượng</th>
                    <th scope="col" style="width: 10%; min-width: 120px; background: #fff" class="text-center" title="Đơn giá tối thiểu nguyên tệ">ĐG tối thiểu NT</th>
                    <th scope="col" style="width: 10%; min-width: 120px; background: #fff" class="text-center" title="Đơn giá tối thiểu quy đổi">ĐG tối thiểu QĐ</th>
                    <th scope="col" style="width: 10%; min-width: 120px; background: #fff" class="text-center" title="Đơn giá tối đa nguyên tệ">ĐG tối đa NT</th>
                    <th scope="col" style="width: 10%; min-width: 120px; background: #fff" class="text-center" title="Đơn giá tối đa quy đổi">ĐG tối đa QĐ</th>
                    <th scope="col" style="width: 10%; min-width: 110px; background: #fff" class="text-center" title="Loại đơn giá">Loại ĐG</th>
                    <th scope="col" style="width: 10%; min-width: 110px; background: #fff" class="text-center" title="Định mức dự toán cơ sở nguyên tệ">ĐMDTCS NT</th>
                    <th scope="col" style="width: 10%; min-width: 110px; background: #fff" class="text-center" title="Định mức dự toán cơ sở quy đổi">ĐMDTCS QĐ</th>
                    <th scope="col" style="width: 10%; min-width: 120px; background: #fff" class="text-center" title="Định mức dự toán chi tiết nguyên tệ">ĐMDTCT NT</th>
                    <th scope="col" style="width: 10%; min-width: 120px; background: #fff" class="text-center" title="Định mức dự toán chi tiết quy đổi">ĐMDTCT QĐ</th>
                    <th scope="col" style="width: 10%; min-width: 120px; background: #fff" class="text-center" title="Định mức nguyên tệ">Định mức NT</th>
                    <th scope="col" style="width: 10%; min-width: 120px; background: #fff" class="text-center" title="Định mức quy đổi">Định mức QĐ</th>
                    <th scope="col" style="width: 10%; min-width: 120px; background: #fff" class="text-center" title="Tỷ lệ tiết kiệm chi">TK chi (%)</th>
                    <th scope="col" style="width: 10%; min-width: 120px; background: #fff" class="text-center" title="Định mức tiết kiệm nguyên tệ">Định mức TK NT</th>
                    <th scope="col" style="width: 10%; min-width: 120px; background: #fff" class="text-center b-r-0" title="Định mức tiết kiệm quy đổi">Định mức TK QĐ</th>
                    <th style="width: 3%; min-width: 30px; background: #fff; z-index: 12 !important;" class="b-table-sticky-column-right"></th>
                  </tr>
                  </thead>
                  <draggable v-model="model.NormTableItem" tag="tbody" draggable=".draggable" handle=".my-handle" @change="changeNormTableItem">
                    <tr v-if="tableItem.ShowPagination" class="draggable" :id="'table-item-' + tableItem.NormTableItemID" v-show="tableItem.Show" :key="key" v-for="(tableItem, key) in model.NormTableItem">
                      <td class="text-center my-handle" style="overflow: unset; cursor: move">
                        <div class="d-flex align-items-center justify-content-around">
                          <i title="Thêm con" @click="onAddFieldChildrenOnTable(key)" class="fa fa-plus-circle" style="cursor: pointer; font-size: 18px; margin-top: 2px; color: #a79f9f;"></i>
                          <i title="Nhân bản" @click="onCloneFieldChildrenOnTable(key)" class="fa fa-clone" style="cursor: pointer; font-size: 16px; margin-top: 2px; color: #a79f9f;"></i>
                        </div>
                      </td>
                      <td>
                        <b-form-input v-model="model.NormTableItem[key].NOrder"></b-form-input>
                      </td>
                      <td>
                        <b-form-select :class="model.NormTableItem[key].NormType" v-model="model.NormTableItem[key].NormType" :options="[{value: 1, text: 'Thu'}, {value: 2, text: 'Chi'}, {value: 3, text: 'Thu & Chi'}]"></b-form-select>
                      </td>
                      <td>
                        <modal-listing-norm-allot-level
                          v-model="model.NormTableItem[key]"
                          :refModal="'modal-listing-norm-level' + model.NormTableItem[key].NormTableItemID"
                          :id-modal="'modal-listing-norm-level' + model.NormTableItem[key].NormTableItemID"
                          placeholder="Định mức phân bổ dự toán"
                          title-modal="Định mức phân bổ dự toán" size-modal="lg"></modal-listing-norm-allot-level>
                      </td>
                      <td>
                        <ijcore-modal-listing
                          v-model="model.NormTableItem[key]" title="Chỉ tiêu dự toán"  api="/listing/api/common/list"
                          field-name="NormName" field-no="NormNo" field-i-d="NormID" :field-type="2" table="norm"
                          :field-where="{NormType: model.NormTableItem[key].NormType}"
                          @changed="changeNorm($event, key)">
                        </ijcore-modal-listing>
                      </td>
                      <td class="b-r-0">
                        <b-form-input v-model="model.NormTableItem[key].NormTableItemNo"></b-form-input>
                      </td>
                      <td class="bg-tree-tr b-table-sticky-column" style="background: #fff;">
                        <span class="bg-tree-dot" :style="{'left': (level * 12) + 'px', top: 0}" v-for="level in model.NormTableItem[key].Level"></span>
                        <div class="bg-tree-content bg-tree-td"
                             :style="{'margin-left': (model.NormTableItem[key].Level * 12 - 12) + 'px', width: 'calc(100% - ' + (model.NormTableItem[key].Level * 12 - 12) + 'px)'}" style="position: absolute; top: 0">
                          <b-form-input v-model="model.NormTableItem[key].NormTableItemName"></b-form-input>
                          <i class="fa fa-minus-square-o bg-tree-icon-action" v-if="model.NormTableItem[key].HaveChildren" @click="onToggleChildNodes($event, tableItem)"></i>
                        </div>
                      </td>
                      <td>
                        <ijcore-modal-listing
                          v-model="model.NormTableItem[key]" title="Đơn vị tính"  api="/listing/api/common/list"
                          field-name="UomName" field-no="UomNo" field-i-d="UomID" table="uom">
                        </ijcore-modal-listing>
                      </td>
                      <td>
                        <ijcore-modal-listing
                          v-model="model.NormTableItem[key]" title="Tiền tệ"  api="/listing/api/common/list"
                          field-name="CcyName" field-no="CcyNo" field-i-d="CcyID" table="ccy">
                        </ijcore-modal-listing>
                      </td>
                      <td>
                        <ijcore-number @change="changeExchangeRate" v-model="model.NormTableItem[key].ExchangeRate"></ijcore-number>
                      </td>
                      <td v-if="stage.reCalculator">
                        <b-form-checkbox class="text-center" v-model="model.NormTableItem[key].checkCalculator"></b-form-checkbox>
                      </td>
                      <td>
                        <b-input-group style="z-index: 1">
                          <b-input-group-prepend>
                            <b-button style="border-radius: 0" title="Thiết lập" @click="setNormTableItem(key)">
                              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"></path><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"></path></svg>
                            </b-button>
                          </b-input-group-prepend>

                          <b-form-input type="text" v-model="model.NormTableItem[key].Formula"></b-form-input>

                          <b-input-group-append>
                            <b-button title="Cập nhật" style="border-radius: 0" @click="calculatorNormTableItem(key, 'server')"><i class="fa fa-calculator"></i></b-button>
                          </b-input-group-append>
                        </b-input-group>
                      </td>
                      <td>
                        <ijcore-number @input="changeAmount(key)" v-model="model.NormTableItem[key].Quantity"></ijcore-number>
                      </td>
                      <td><ijcore-number v-model="model.NormTableItem[key].FCMinUnitPrice" @input="changeFC(key, 'FCMinUnitPrice', 'LCMinUnitPrice')"></ijcore-number></td>
                      <td><ijcore-number v-model="model.NormTableItem[key].LCMinUnitPrice"></ijcore-number></td>
                      <td><ijcore-number v-model="model.NormTableItem[key].FCMaxUnitPrice" @input="changeFC(key, 'FCMaxUnitPrice', 'LCMaxUnitPrice')"></ijcore-number></td>
                      <td><ijcore-number v-model="model.NormTableItem[key].LCMaxUnitPrice"></ijcore-number></td>
                      <td>
                        <b-form-select :class="model.NormTableItem[key].UnitPriceType" @change="changeUnitPriceType(key)" v-model="model.NormTableItem[key].UnitPriceType" :options="[{value: 1, text: 'Tối đa'}, {value: 2, text: 'Tối thiểu'}, {value: 3, text: 'Trung bình'}, {value: 4, text: 'Khác'}]"></b-form-select>
                      </td>
                      <td><ijcore-number v-model="model.NormTableItem[key].FCBaseUnitPrice" @input="changeBaseUnitPrice(key, 'FC')"></ijcore-number></td>
                      <td><ijcore-number v-model="model.NormTableItem[key].LCBaseUnitPrice" @input="changeBaseUnitPrice(key, 'LC')"></ijcore-number></td>
                      <td>
                        <ijcore-number v-model="model.NormTableItem[key].FCUnitPrice" @input="changeFCUnitPrice(key)"></ijcore-number>
                      </td>
                      <td>
                        <ijcore-number v-model="model.NormTableItem[key].LCUnitPrice"></ijcore-number>
                      </td>
                      <td><ijcore-number v-model="model.NormTableItem[key].FCAmount" @input="changeFCAmount(key)"></ijcore-number></td>
                      <td><ijcore-number v-model="model.NormTableItem[key].LCAmount"></ijcore-number></td>
                      <td><ijcore-number v-model="model.NormTableItem[key].SaveRate" @input="changeSaveRate(key)"></ijcore-number></td>
                      <td><ijcore-number v-model="model.NormTableItem[key].FCSaveAmount"></ijcore-number></td>
                      <td class="b-r-0"><ijcore-number v-model="model.NormTableItem[key].LCSaveAmount"></ijcore-number></td>
                      <td class="b-table-sticky-column-right" style="background: #fff">
                        <div class="d-flex align-items-center justify-content-around">
                          <i class="fa fa-trash-o" @click="$_tableTree_onDeleteFieldOnTable(key, 'NormTableItem', 'NormTableItemID')" title="Xóa" style="font-size: 18px; cursor: pointer"></i>
<!--                          <i class="fa fa-trash-o" @click="onDeleteFieldOnNormTableItem(key)" title="Xóa" style="font-size: 18px; cursor: pointer"></i>-->
                        </div>
                      </td>
                    </tr>
                  </draggable>
                </table>
              </div>
              <div class="d-flex justify-content-between mt-2">
                <div class="d-flex align-items-center">
                  <a @click="onAddFieldOnTable()" class="new-row mr-3">
                    <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm dòng
                  </a>
                </div>
                <div class="d-flex align-items-center">
                  <div class="table-pagination mr-3" v-if="model.NormTableItem.length > Number(perPage)">
                    <b-pagination
                      v-model="currentPage"
                      :total-rows="totalRows"
                      :per-page="perPage"
                      aria-controls="my-table"
                      size="md"
                    ></b-pagination>
                  </div>
                  <b-form-checkbox class="mr-2" id="re-calculator" v-model="stage.reCalculator" name="re-calculator">Tính lại</b-form-checkbox>
                  <b-button class="mr-2" variant="primary" title="Tính lại" @click="reCalculatorNormTableItem" v-if="stage.reCalculator"><i class="fa fa-calculator"></i></b-button>
<!--                  <b-button variant="primary" title="Tổng cộng" @click="sumUpNormTableItem">-->
                  <b-button variant="primary" title="Tổng cộng" @click="$_tableTree_sumpUp('NormTableItem', 'NormTableItemID', [])">
                    <svg xmlns="http://www.w3.org/2000/svg" style="fill: #fff" height="18" viewBox="0 0 24 24" width="18"><path d="M0 0h24v24H0z" fill="none"/><path d="M18 4H6v2l6.5 6L6 18v2h12v-3h-7l5-5-5-5h7z"/></svg>
                  </b-button>
                </div>
              </div>
            </div>
          </b-card>
        </div>
    </div>
    <b-modal ref="norm-table-item" id="modal-norm-table-item" size="lg" title="Định mức dự toán chi tiết">

      <table class="table b-table table-sm table-bordered table-editable">
        <thead>
        <tr>
          <th scope="col" class="text-center" style="width: 35%">Định mức dự toán</th>
          <th scope="col" class="text-center" style="width: 61%" title="Mô tả liên kết">Chi tiết</th>
          <th scope="col" class="text-center" style="width: 4%"></th>
        </tr>
        </thead>
        <tbody v-if="currentKey !== null && model.NormTableItem[currentKey] && model.NormTableItem[currentKey].FormulaArr">
        <tr v-for="(item, keyItem) in model.NormTableItem[currentKey].FormulaArr">
          <td>
            <ijcore-modal-listing
              v-model="model.NormTableItem[currentKey].FormulaArr[keyItem]" title="Định mức dự toán" :api="'/listing/api/common/list'"
              :table="'norm_table'" :FieldID="'NormTableID'" :FieldName="'NormTableName'"
              :FieldNo="'NormTableNo'">
            </ijcore-modal-listing>
          </td>
          <td>
            <ijcore-modal-listing
              v-model="model.NormTableItem[currentKey].FormulaArr[keyItem]" title="Chi tiết" :api="'/listing/api/common/list'"
              :table="'norm_table_item'" :FieldID="'NormTableItemID'" :FieldName="'NormTableItemName'"
              :FieldUpdate="['FCMinUnitPrice', 'FCMaxUnitPrice', 'LCMinUnitPrice', 'LCMaxUnitPrice']"
              :FieldNo="'NormTableItemNo'" :FieldWhere="{NormTableID : model.NormTableItem[currentKey].FormulaArr[keyItem].NormTableID}">
            </ijcore-modal-listing>
          </td>
          <td class="text-center"><span @click="onDeleteFieldOnTableFormula(keyItem)"><i class="fa fa-trash-o" style="font-size: 18px; cursor: pointer"></i></span></td>
        </tr>
        </tbody>
      </table>
      <a class="new-row" @click="onAddFieldOnTableFormula"><i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm dòng</a>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onSetFormula">Thiết lập</b-button>
        </div>
      </template>
    </b-modal>
  </div>
</template>

<script>
  import ApiService from '@/services/api.service';
  import {TokenService} from '@/services/storage.service';
  import MaskedInput from 'vue-text-mask';
  import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
  import Select2 from 'v-select2-component';
  import moment from 'moment';
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import ModalListingNormAllotLevel from "./ModalListingNormAllotLevel";
  import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
  import IjcoreNumber from "../../../../components/IjcoreNumber";
  import ColumnResizer from 'column-resizer';
  import draggable from 'vuedraggable';
  import mixinTablePagination from "@/mixins/tablePagination";
  import mixinTableTree from '@/mixins/tableTree';

  moment.locale('vi');

  const ListRouter = 'listing-normtable';
  const EditRouter = 'listing-normtable-edit';
  const CreateRouter = 'listing-normtable-create';
  const ViewRouter = 'listing-normtable-view';
  const DetailApi = 'listing/api/norm-table/view';
  const EditApi = 'listing/api/norm-table/edit';
  const CreateApi = 'listing/api/norm-table/create';
  const StoreApi = 'listing/api/norm-table/store';
  const UpdateApi = 'listing/api/norm-table/update';
  const ListApi = 'listing/api/norm-table';

  export default {
    name: 'listing-normtable-form',
    mixins: [mixinTablePagination, mixinTableTree],
    data() {
      return {
        idParams: this.idParamsProps,
        reqParams: this.reqParamsProps,
        RowItem: 1,
        model: {
          NormTableID: null,
          NormTableNo: '',
          NormTableName: '',
          NormType: 1,
          NormTableDate: null,
          EffectiveDate: null,
          ExpirationDate: null,
          PeriodID: null,
          PeriodName: '',
          PeriodValue: null,
          PeriodValueName: '',
          FromDate: null,
          ToDate: null,
          Inactive: '',
          NormTableItem: []
        },
        currentKey: null,
        PeriodOption: [
          {value: null, text: 'Chọn chu kỳ'},
          {value: 1, text: 'Năm'},
          {value: 2, text: 'Quý'},
          {value: 3, text: 'Tháng'},
          {value: 4, text: 'Tuần'},
          {value: 5, text: 'Ngày'},
          {value: 6, text: '6 tháng'},
          {value: 7, text: '9 tháng'},
          {value: 8, text: '3 năm'},
          {value: 9, text: '5 năm'},
          {value: 10, text: '10 năm'},
          {value: 99, text: 'Tùy chọn'},
        ],
        PeriodValueOption: [],
        // saveRate: 10,
        stage: {
          reCalculator: false
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
      IjcoreNumber,
      IjcoreDatePicker,
      IjcoreModalListing,
      ModalListingNormAllotLevel,
      Select2,
      MaskedInput,
      draggable
    },
    beforeCreate() {
    },
    mounted() {
      // let self = this;
      this.fetchData();
      if (document.querySelector('.table-column-resizable')) {
        new ColumnResizer(
            document.querySelector('.table-column-resizable'),{resizeMode: 'overflow'}
        );
      }
      // SaveRate - Tỷ lệ tiết kiệm
      // let optionSetting = JSON.parse(localStorage.getItem('OptionSetting'));
      // if (optionSetting) {
      //   let saveRate = _.find(optionSetting, ['SettingKey', 'Global_SaveRate']);
      //   if (saveRate) {
      //     self.saveRate = Number(saveRate.SettingValue);
      //   }
      // }
    },
    updated() {
      this.stage.updatedData = true;
    },
    computed: {},
    methods: {
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
                self.model.NormTableID = responsesData.data.data.NormTableID;
                self.model.NormTableNo = responsesData.data.data.NormTableNo;
                self.model.NormTableName = responsesData.data.data.NormTableName;
                self.model.NormTableDate = __.convertServerDateToClientDate(responsesData.data.data.NormTableDate);
                self.model.EffectiveDate = __.convertServerDateToClientDate(responsesData.data.data.EffectiveDate);
                self.model.ExpirationDate = __.convertServerDateToClientDate(responsesData.data.data.ExpirationDate);
                self.model.PeriodID = responsesData.data.data.PeriodID;
                self.model.PeriodName = responsesData.data.data.PeriodName;
                self.changePeriodType();
                self.model.PeriodValue = responsesData.data.data.PeriodValue;
                self.model.PeriodValueName = responsesData.data.data.PeriodValueName;
                self.model.FromDate = __.convertServerDateToClientDate(responsesData.data.data.FromDate);
                self.model.ToDate = __.convertServerDateToClientDate(responsesData.data.data.ToDate);

                _.forEach(responsesData.data.NormTableItem, function (tableItem, key) {
                  tableItem.Show = true;
                  tableItem.HaveChildren = (tableItem.Detail) ? false : true;
                  tableItem.checkCalculator = false;
                  tableItem.FormulaArr = [{
                    NormTableID: null,
                    NormTableNo: '',
                    NormTableName: '',
                    NormTableItemID: null,
                    NormTableItemNo: '',
                    NormTableItemName: '',
                    FCMinUnitPrice: null,
                    FCMaxUnitPrice: null,
                    LCMinUnitPrice: null,
                    LCMaxUnitPrice: null,
                  }];
                  tableItem.ShowPagination = false;
                  self.model.NormTableItem.push(tableItem);
                  if (tableItem.LineIDTmp > self.RowItem) {
                    self.RowItem = tableItem.LineIDTmp;
                  }
                });
                self.RowItem += 1;
              }
              if (!_.isEmpty(self.itemCopy)) {
                self.model.NormTableNo = responses.data.data.auto;
              }
            } else {
              self.model.NormTableNo = responses.data.data.auto;
            }
            self.totalRows = self.model.NormTableItem.length;
            self.changePage();
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      handleSubmitForm(){
        if (!this.model.NormTableName) {
          this.$bvToast.toast('Tên định mức dự toán không được để trống', {
            variant: 'warning',
            title: 'Thông báo',
            solid: true
          });
          return false;
        }
        let self = this;
        let periodObj = _.find(this.PeriodOption, ['value', this.model.PeriodID]);
        let periodValueObj = _.find(this.PeriodValueOption, ['value', Number(this.model.PeriodValue)]);
        const requestData = {
          method: 'post',
          url: StoreApi,
          data: {
            master: {
              NormTableID: this.model.NormTableID,
              NormTableNo: this.model.NormTableNo,
              NormTableName: this.model.NormTableName,
              NormType: this.model.NormType,
              NormTableDate: (this.model.NormTableDate) ? this.model.NormTableDate : '',
              EffectiveDate: (this.model.EffectiveDate) ? this.model.EffectiveDate : '',
              ExpirationDate: (this.model.ExpirationDate) ? this.model.ExpirationDate : '',
              PeriodID: this.model.PeriodID,
              PeriodName: (periodObj.value) ? periodObj.text : '',
              PeriodValue: this.model.PeriodValue,
              PeriodValueName: (periodValueObj) ? periodValueObj.text : '',
              FromDate: (this.model.FromDate) ? this.model.FromDate : '',
              ToDate: (this.model.ToDate) ? this.model.ToDate : '',
              Inactive: this.model.Inactive,
            },
            detail: this.model.NormTableItem,
          }
        };
        // edit user
        if (this.idParams) {
          requestData.data.master.TemplateID = this.idParams;
          requestData.url = UpdateApi + '/' + this.idParams+'?XDEBUG_SESSION_START=PHPSTORM';
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
      onBackToList(){
        this.$router.push({name: 'listing-normtable'});
      },
      changePeriodType(){
        let self = this;
        let workDate = TokenService.getWorkdate();
        if (!workDate) {
          workDate = moment().format('L');
        }
        let momentWorkDate = moment(workDate, 'L');
        let yearWorkDate = momentWorkDate.get("year");
        let monthWorkDate = momentWorkDate.get('month');
        this.PeriodValueOption = [];
        switch (this.model.PeriodID) {
          case 1:
            for (let i = 8; i >= 1; i--) {
              let year = Number(yearWorkDate) - i;
              let tmpObj = {};
              tmpObj.value = year;
              tmpObj.text = year;
              tmpObj.fromDate = moment([year]).startOf("year").format('L');
              tmpObj.toDate = moment([year]).endOf("year").format('L');
              self.PeriodValueOption.push(tmpObj);
            }
            this.PeriodValueOption.push({
              value: yearWorkDate,
              text: yearWorkDate,
              fromDate: moment([yearWorkDate]).startOf("year").format('L'),
              toDate: moment([yearWorkDate]).endOf("year").format('L')
            });
            for (let i = 1; i <= 8; i++) {
              let year = Number(yearWorkDate) + i;
              let tmpObj = {};
              tmpObj.value = Number(yearWorkDate);
              tmpObj.text = year;
              tmpObj.fromDate = moment([year]).startOf("year").format('L');
              tmpObj.toDate = moment([year]).endOf("year").format('L');
              self.PeriodValueOption.push(tmpObj);
            }
            this.model.PeriodValue = Number(yearWorkDate);
            break;
          case 2:
            this.PeriodValueOption.push({
              value: 1,
              text: 'Quý 1/' + yearWorkDate,
              fromDate: moment([yearWorkDate, 0]).startOf("months").format('L'),
              toDate: moment([yearWorkDate, 2]).endOf("months").format('L')
            });
            this.PeriodValueOption.push({
              value: 2,
              text: 'Quý 2/' + yearWorkDate,
              fromDate: moment([yearWorkDate, 3]).startOf("months").format('L'),
              toDate: moment([yearWorkDate, 5]).endOf("months").format('L')
            });
            this.PeriodValueOption.push({
              value: 3,
              text: 'Quý 3/' + yearWorkDate,
              fromDate: moment([yearWorkDate, 6]).startOf("months").format('L'),
              toDate: moment([yearWorkDate, 8]).endOf("months").format('L')
            });
            this.PeriodValueOption.push({
              value: 4,
              text: 'Quý 4/' + yearWorkDate,
              fromDate: moment([yearWorkDate, 9]).startOf("months").format('L'),
              toDate: moment([yearWorkDate, 11]).endOf("months").format('L')
            });
            this.model.PeriodValue = 1;
            break;
          case 3:
            for (let i = 1; i <= 12; i++) {
              self.PeriodValueOption.push({
                value: i,
                text: 'Tháng ' + i + '/' + yearWorkDate,
                fromDate: moment([yearWorkDate, i - 1]).startOf("months").format('L'),
                toDate: moment([yearWorkDate, i - 1]).endOf("months").format('L')
              });
            }
            this.model.PeriodValue = 1;
            break;
          case 4:
            for (let i = 1; i <= 52; i++) {
              self.PeriodValueOption.push({
                value: i,
                text: 'Tuần ' + i + '/' + yearWorkDate,
                fromDate: moment(workDate).week(i - 1).startOf('week').format('L'),
                toDate: moment(workDate).week(i-1).endOf('week').format('L')
              });
            }
            this.model.PeriodValue = 1;
            break;
          case 5:
            this.model.FromDate = workDate;
            this.model.ToDate = this.model.FromDate;
            break;
          case 6:
            self.PeriodValueOption.push({
              value: 1,
              text: yearWorkDate + '/6th đầu',
              fromDate: moment([yearWorkDate, 0]).startOf("months").format('L'),
              toDate: moment([yearWorkDate, 5]).endOf("months").format('L')
            });
            self.PeriodValueOption.push({
              value: 2,
              text: yearWorkDate + '/6th cuối',
              fromDate: moment([yearWorkDate, 6]).startOf("months").format('L'),
              toDate: moment([yearWorkDate, 11]).endOf("months").format('L')
            });
            this.model.PeriodValue = 1;
            // this.model.FromDate = workDate;
            // this.model.ToDate = workDate;
            break;
          case 7:
            this.PeriodValueOption.push({
              value: 1,
              text: (Number(yearWorkDate) - 1) + '/9 tháng',
              fromDate: moment([(Number(yearWorkDate) - 1), 0]).startOf("months").format('L'),
              toDate: moment([(Number(yearWorkDate) - 1), 8]).endOf("months").format('L')
            });
            this.PeriodValueOption.push({
              value: 2,
              text: (Number(yearWorkDate)) + '/9 tháng',
              fromDate: moment([yearWorkDate, 0]).startOf("months").format('L'),
              toDate: moment([yearWorkDate, 8]).endOf("months").format('L')
            });
            this.PeriodValueOption.push({
              value: 3,
              text: (Number(yearWorkDate) + 1) + '/9 tháng',
              fromDate: moment([(Number(yearWorkDate) + 1), 0]).startOf("months").format('L'),
              toDate: moment([(Number(yearWorkDate) + 1), 8]).endOf("months").format('L')
            });
            this.model.PeriodValue = 2;
            break;
          case 8:
            this.PeriodValueOption.push({
              value: 1,
              text: (Number(yearWorkDate) - 3) + ' - ' + (Number(yearWorkDate) - 1),
              fromDate: moment([Number(yearWorkDate) - 3]).startOf("year").format('L'),
              toDate: moment([Number(yearWorkDate) - 1]).endOf("year").format('L')
            });
            this.PeriodValueOption.push({
              value: 2,
              text: (Number(yearWorkDate)) + ' - ' + (Number(yearWorkDate) + 2),
              fromDate: moment([Number(yearWorkDate)]).startOf("year").format('L'),
              toDate: moment([Number(yearWorkDate) + 1]).endOf("year").format('L')
            });
            this.PeriodValueOption.push({
              value: 3,
              text: (Number(yearWorkDate) + 3) + ' - ' + (Number(yearWorkDate) + 5),
              fromDate: moment([Number(yearWorkDate) + 3]).startOf("year").format('L'),
              toDate: moment([Number(yearWorkDate) + 5]).endOf("year").format('L')
            });
            this.model.PeriodValue = 2;
            break;
          case 9:
            this.PeriodValueOption.push({
              value: 1,
              text: (Number(yearWorkDate) - 5) + ' - ' + (Number(yearWorkDate) - 1),
              fromDate: moment([Number(yearWorkDate) - 5]).startOf("year").format('L'),
              toDate: moment([Number(yearWorkDate) - 1]).endOf("year").format('L')
            });
            this.PeriodValueOption.push({
              value: 2,
              text: (Number(yearWorkDate)) + ' - ' + (Number(yearWorkDate) + 4),
              fromDate: moment([Number(yearWorkDate)]).startOf("year").format('L'),
              toDate: moment([Number(yearWorkDate) + 4]).endOf("year").format('L')
            });
            this.PeriodValueOption.push({
              value: 3,
              text: (Number(yearWorkDate) + 5) + ' - ' + (Number(yearWorkDate) + 9),
              fromDate: moment([Number(yearWorkDate) + 5]).startOf("year").format('L'),
              toDate: moment([Number(yearWorkDate) + 9]).endOf("year").format('L')
            });
            this.model.PeriodValue = 2;
            break;
          case 10:
            this.PeriodValueOption.push({
              value: 1,
              text: (Number(yearWorkDate) - 10) + ' - ' + (Number(yearWorkDate) - 1),
              fromDate: moment([Number(yearWorkDate) - 10]).startOf("year").format('L'),
              toDate: moment([Number(yearWorkDate) - 1]).endOf("year").format('L')
            });
            this.PeriodValueOption.push({
              value: 2,
              text: (Number(yearWorkDate)) + ' - ' + (Number(yearWorkDate) + 9),
              fromDate: moment([Number(yearWorkDate)]).startOf("year").format('L'),
              toDate: moment([Number(yearWorkDate) + 9]).endOf("year").format('L')
            });
            this.PeriodValueOption.push({
              value: 3,
              text: (Number(yearWorkDate) + 10) + ' - ' + (Number(yearWorkDate) + 19),
              fromDate: moment([Number(yearWorkDate) + 10]).startOf("year").format('L'),
              toDate: moment([Number(yearWorkDate) + 19]).endOf("year").format('L')
            });
            this.model.PeriodValue = 2;
            break;
          case 99:
            this.model.FromDate = '';
            this.model.ToDate = '';
            break;
          default:
            break;
        }
        this.changePeriodValue();
      },
      changePeriodValue(){
        let dateRange = _.find(this.PeriodValueOption, ['value', Number(this.model.PeriodValue)]);
        if (dateRange) {
          this.model.FromDate = dateRange.fromDate;
          this.model.ToDate = dateRange.toDate;
        }
      },
      changeFromDate() {
        this.model.FromDate = this.model.ToDate;
      },
      onAddFieldOnTable(argc = null) {

        let tmpObj = {};
        tmpObj.NormTableItemNo = '';
        tmpObj.NormTableItemName = (argc) ? argc.NormAllotLevelName : '';
        tmpObj.ParentID = null;
        tmpObj.Level = (argc && argc.Level) ? argc.Level : 1;
        tmpObj.Detail = (argc) ? argc.Detail : 1;
        tmpObj.NOrder = '';
        tmpObj.UomID = ((argc && argc.UomID)) ? argc.UomID : ((this.model.NormTableItem[0]) ? this.model.NormTableItem[0].UomID : null);
        tmpObj.UomNo = ((argc && argc.UomNo)) ? argc.UomNo : ((this.model.NormTableItem[0]) ? this.model.NormTableItem[0].UomNo : '');
        tmpObj.UomName = ((argc && argc.UomName)) ? argc.UomName : ((this.model.NormTableItem[0]) ? this.model.NormTableItem[0].UomName : '');
        tmpObj.Percentage = null;
        tmpObj.CcyID = (this.model.NormTableItem[0]) ? this.model.NormTableItem[0].CcyID : null;
        tmpObj.CcyNo = (this.model.NormTableItem[0]) ? this.model.NormTableItem[0].CcyNo : '';
        tmpObj.CcyName = (this.model.NormTableItem[0]) ? this.model.NormTableItem[0].CcyName : '';
        tmpObj.NormTableID = null;
        tmpObj.NormID = (argc && argc.NormID) ? argc.NormID : null;
        tmpObj.NormNo = (argc && argc.NormNo) ? argc.NormNo : '';
        tmpObj.NormName = (argc && argc.NormName) ? argc.NormName : '';
        tmpObj.NormAllotLevelNo = (argc && argc.NormAllotLevelNo) ? argc.NormAllotLevelNo : '';
        tmpObj.NormAllotLevelName = (argc && argc.NormAllotLevelName) ? argc.NormAllotLevelName : '';
        tmpObj.NormAllotLevelID = (argc && argc.NormAllotLevelID) ? argc.NormAllotLevelID : null;
        tmpObj.NormType = (argc && argc.NormLevelType) ? argc.NormLevelType : 1;
        tmpObj.Quantity = (argc && argc.Quantity) ? argc.Quantity : 1;
        tmpObj.ExchangeRate = (argc && argc.ExchangeRate) ? argc.ExchangeRate : 1;
        tmpObj.FCUnitPrice = (argc && argc.FCUnitPrice) ? argc.FCUnitPrice : null;
        tmpObj.LCUnitPrice = (argc && argc.LCUnitPrice) ? argc.LCUnitPrice : null;
        tmpObj.FCMinUnitPrice = (argc && argc.FCMinUnitPrice) ? argc.FCMinUnitPrice : null;
        tmpObj.FCMaxUnitPrice = (argc && argc.FCMaxUnitPrice) ? argc.FCMaxUnitPrice : null;
        tmpObj.LCMinUnitPrice = (argc && argc.LCMinUnitPrice) ? argc.LCMinUnitPrice : null;
        tmpObj.LCMaxUnitPrice = (argc && argc.LCMaxUnitPrice) ? argc.LCMaxUnitPrice : null;
        tmpObj.FCBaseUnitPrice = null;
        tmpObj.LCBaseUnitPrice = null;
        tmpObj.FCAmount = null;
        tmpObj.LCAmount = null;
        tmpObj.UnitPriceType = 1;
        tmpObj.FCSaveAmount = 0;
        tmpObj.LCSaveAmount = 0;
        tmpObj.SaveRate = 0;
        tmpObj.Formula = '';
        tmpObj.FormulaArr = [{
          NormTableID: null,
          NormTableNo: '',
          NormTableName: '',
          NormTableItemID: null,
          NormTableItemNo: '',
          NormTableItemName: '',
          FCMinUnitPrice: null,
          FCMaxUnitPrice: null,
          LCMinUnitPrice: null,
          LCMaxUnitPrice: null,
        }];
        tmpObj.Inactive = 0;
        tmpObj.Show = true;
        tmpObj.ShowPagination = true;
        tmpObj.checkCalculator = false;
        tmpObj.HaveChildren = false;
        tmpObj.LineIDTmp = this.RowItem;
        tmpObj.NormTableItemID = this.RowItem;
        this.RowItem++;
        let currentItems = _.filter(this.model.NormTableItem, ['ShowPagination', true]);
        if (currentItems.length) {
          let lastItem = currentItems.pop();
          if (lastItem) {
            let lastIndexItem = _.findIndex(this.model.NormTableItem, ['NormTableItemID', lastItem.NormTableItemID]);
            if (lastIndexItem > -1) {
              this.model.NormTableItem = __.insertBeforeKey(this.model.NormTableItem, lastIndexItem + 1, tmpObj);
            }else {
              this.model.NormTableItem.push(tmpObj);
            }
          }else {
            this.model.NormTableItem.push(tmpObj);
          }
        }else {
          this.model.NormTableItem.push(tmpObj);
        }
      },
      onAddMultiFieldOnTable(normAllotLevel, key) {
        // parent first
        this.model.NormTableItem[key].NormID = normAllotLevel.NormID;
        this.model.NormTableItem[key].NormNo = normAllotLevel.NormNo;
        this.model.NormTableItem[key].NormName = normAllotLevel.NormName;
        this.model.NormTableItem[key].NormAllotLevelID = normAllotLevel.NormAllotLevelID;
        this.model.NormTableItem[key].NormAllotLevelNo = normAllotLevel.NormAllotLevelNo;
        this.model.NormTableItem[key].NormAllotLevelName = normAllotLevel.NormAllotLevelName;
        this.model.NormTableItem[key].UomID = normAllotLevel.UomID;
        this.model.NormTableItem[key].UomNo = normAllotLevel.UomNo;
        this.model.NormTableItem[key].UomName = normAllotLevel.UomName;
        this.model.NormTableItem[key].CcyID = normAllotLevel.CcyID;
        this.model.NormTableItem[key].CcyNo = normAllotLevel.CcyNo;
        this.model.NormTableItem[key].CcyName = normAllotLevel.CcyName;
        this.model.NormTableItem[key].ExchangeRate = normAllotLevel.ExchangeRate;
        this.model.NormTableItem[key].UnitPriceType = normAllotLevel.UnitPriceType;
        this.model.NormTableItem[key].FCMinUnitPrice = normAllotLevel.FCMinUnitPrice;
        this.model.NormTableItem[key].FCMaxUnitPrice = normAllotLevel.FCMaxUnitPrice;
        this.model.NormTableItem[key].LCMinUnitPrice = normAllotLevel.LCMinUnitPrice;
        this.model.NormTableItem[key].LCMaxUnitPrice = normAllotLevel.LCMaxUnitPrice;
        this.model.NormTableItem[key].FCUnitPrice = normAllotLevel.FCUnitPrice;
        this.model.NormTableItem[key].LCUnitPrice = normAllotLevel.LCUnitPrice;
        this.model.NormTableItem[key].NormTableItemName = normAllotLevel.NormAllotLevelName;
      },
      onAddFieldChildrenOnTable(keyParent){
        let tmpObj = {};
        this.model.NormTableItem[keyParent].Detail = 0;
        this.model.NormTableItem[keyParent].HaveChildren = true;
        tmpObj.NormTableItemNo = '';
        tmpObj.NormTableItemName = '';
        tmpObj.Level = this.model.NormTableItem[keyParent].Level + 1;
        tmpObj.Detail = 1;
        tmpObj.NOrder = '';
        tmpObj.UomID = this.model.NormTableItem[keyParent].UomID;
        tmpObj.UomNo = this.model.NormTableItem[keyParent].UomNo;
        tmpObj.UomName = this.model.NormTableItem[keyParent].UomName;
        tmpObj.Percentage = null;
        tmpObj.CcyID = this.model.NormTableItem[keyParent].CcyID;
        tmpObj.CcyNo = this.model.NormTableItem[keyParent].CcyNo;
        tmpObj.CcyName = this.model.NormTableItem[keyParent].CcyName;
        tmpObj.NormTableID = null;
        tmpObj.NormID = null;
        tmpObj.NormNo = '';
        tmpObj.NormName = '';
        tmpObj.NormAllotLevelNo = '';
        tmpObj.NormAllotLevelName = '';
        tmpObj.NormAllotLevelID = null;
        tmpObj.NormType = this.model.NormTableItem[keyParent].NormType;
        tmpObj.Quantity = 1;
        tmpObj.ExchangeRate = 1;
        tmpObj.FCUnitPrice = null;
        tmpObj.LCUnitPrice = null;
        tmpObj.FCMinUnitPrice = null;
        tmpObj.FCMaxUnitPrice = null;
        tmpObj.LCMinUnitPrice = null;
        tmpObj.LCMaxUnitPrice = null;
        tmpObj.FCBaseUnitPrice = null;
        tmpObj.LCBaseUnitPrice = null;
        tmpObj.FCAmount = null;
        tmpObj.LCAmount = null;
        tmpObj.UnitPriceType = 1;
        tmpObj.FCSaveAmount = 0;
        tmpObj.LCSaveAmount = 0;
        tmpObj.SaveRate = 0;
        tmpObj.Formula = '';
        tmpObj.FormulaArr = [{
            NormTableID: null,
            NormTableNo: '',
            NormTableName: '',
            NormTableItemID: null,
            NormTableItemNo: '',
            NormTableItemName: '',
            FCMinUnitPrice: null,
            FCMaxUnitPrice: null,
            LCMinUnitPrice: null,
            LCMaxUnitPrice: null,
        }];
        tmpObj.Inactive = 0;
        tmpObj.Show = true;
        tmpObj.ShowPagination = true;
        tmpObj.checkCalculator = false;
        tmpObj.HaveChildren = false;
        tmpObj.LineIDTmp = this.RowItem;
        tmpObj.NormTableItemID = this.RowItem;
        tmpObj.ParentID = this.model.NormTableItem[keyParent].NormTableItemID;
        this.RowItem++;

        let indexInsert = keyParent + 1;
        let allChild = _.filter(this.model.NormTableItem, ['ParentID', this.model.NormTableItem[keyParent].NormTableItemID]);
        if (allChild.length) {
          let lastItemChild = allChild[allChild.length - 1];
          let indexLastChild = _.findIndex(this.model.NormTableItem, ['NormTableItemID', lastItemChild.NormTableItemID]);
          indexInsert = indexLastChild + 1;
        }
        this.model.NormTableItem = __.insertBeforeKey(this.model.NormTableItem, indexInsert, tmpObj);
        // this.model.NormTableItem.push(tmpObj);

      },
      onCloneFieldChildrenOnTable(keyClone){
        let cloneObj = {};
        cloneObj.NormTableItemNo = '';
        cloneObj.NormTableItemName = this.model.NormTableItem[keyClone].NormTableItemName;
        cloneObj.ParentID = this.model.NormTableItem[keyClone].ParentID;
        cloneObj.Level = this.model.NormTableItem[keyClone].Level;
        cloneObj.Detail = this.model.NormTableItem[keyClone].Detail;
        cloneObj.NOrder = '';
        cloneObj.UomID = this.model.NormTableItem[keyClone].UomID;
        cloneObj.UomNo = this.model.NormTableItem[keyClone].UomNo;
        cloneObj.UomName = this.model.NormTableItem[keyClone].UomName;
        cloneObj.Percentage = this.model.NormTableItem[keyClone].Percentage;
        cloneObj.CcyID = this.model.NormTableItem[keyClone].CcyID;
        cloneObj.CcyNo = this.model.NormTableItem[keyClone].CcyNo;
        cloneObj.CcyName = this.model.NormTableItem[keyClone].CcyName;
        cloneObj.NormTableID = this.model.NormTableItem[keyClone].NormTableID;
        cloneObj.NormID = this.model.NormTableItem[keyClone].NormID;
        cloneObj.NormNo = this.model.NormTableItem[keyClone].NormNo;
        cloneObj.NormName = this.model.NormTableItem[keyClone].NormName;
        cloneObj.NormAllotLevelNo = this.model.NormTableItem[keyClone].NormAllotLevelNo;
        cloneObj.NormAllotLevelName = this.model.NormTableItem[keyClone].NormAllotLevelName;
        cloneObj.NormAllotLevelID = this.model.NormTableItem[keyClone].NormAllotLevelID;
        cloneObj.NormType = this.model.NormTableItem[keyClone].NormType;
        cloneObj.Quantity = this.model.NormTableItem[keyClone].Quantity;
        cloneObj.ExchangeRate = this.model.NormTableItem[keyClone].ExchangeRate;
        cloneObj.FCUnitPrice = this.model.NormTableItem[keyClone].FCUnitPrice;
        cloneObj.LCUnitPrice = this.model.NormTableItem[keyClone].LCUnitPrice;
        cloneObj.FCMinUnitPrice = this.model.NormTableItem[keyClone].FCMinUnitPrice;
        cloneObj.FCMaxUnitPrice = this.model.NormTableItem[keyClone].FCMaxUnitPrice;
        cloneObj.LCMinUnitPrice = this.model.NormTableItem[keyClone].LCMinUnitPrice;
        cloneObj.LCMaxUnitPrice = this.model.NormTableItem[keyClone].LCMaxUnitPrice;
        cloneObj.FCBaseUnitPrice = this.model.NormTableItem[keyClone].FCBaseUnitPrice;
        cloneObj.LCBaseUnitPrice = this.model.NormTableItem[keyClone].LCBaseUnitPrice;
        cloneObj.FCAmount = this.model.NormTableItem[keyClone].FCAmount;
        cloneObj.LCAmount = this.model.NormTableItem[keyClone].LCAmount;
        cloneObj.UnitPriceType = this.model.NormTableItem[keyClone].UnitPriceType;
        cloneObj.FCSaveAmount = this.model.NormTableItem[keyClone].FCSaveAmount;
        cloneObj.LCSaveAmount = this.model.NormTableItem[keyClone].LCSaveAmount;
        cloneObj.SaveRate = this.model.NormTableItem[keyClone].SaveRate;
        cloneObj.Formula = this.model.NormTableItem[keyClone].Formula;
        cloneObj.FormulaArr = [{
          NormTableID: this.model.NormTableItem[keyClone].FormulaArr.NormTableID,
          NormTableNo: this.model.NormTableItem[keyClone].FormulaArr.NormTableNo,
          NormTableName: this.model.NormTableItem[keyClone].FormulaArr.NormTableName,
          NormTableItemID: this.model.NormTableItem[keyClone].FormulaArr.NormTableItemID,
          NormTableItemNo: this.model.NormTableItem[keyClone].FormulaArr.NormTableItemNo,
          NormTableItemName: this.model.NormTableItem[keyClone].FormulaArr.NormTableItemName,
          FCMinUnitPrice: this.model.NormTableItem[keyClone].FormulaArr.FCMinUnitPrice,
          FCMaxUnitPrice: this.model.NormTableItem[keyClone].FormulaArr.FCMaxUnitPrice,
          LCMinUnitPrice: this.model.NormTableItem[keyClone].FormulaArr.LCMinUnitPrice,
          LCMaxUnitPrice: this.model.NormTableItem[keyClone].FormulaArr.LCMaxUnitPrice,
        }];
        cloneObj.Inactive = this.model.NormTableItem[keyClone].Inactive;
        cloneObj.Show = true;
        cloneObj.ShowPagination = true;
        cloneObj.checkCalculator = false;
        cloneObj.HaveChildren = false;
        cloneObj.LineIDTmp = this.RowItem;
        cloneObj.NormTableItemID = this.RowItem;

        cloneObj.NormTableItemNo = this.model.NormTableItem[keyClone].NormTableItemNo;
        cloneObj.LineIDTmp = this.RowItem;
        cloneObj.NormTableItemID = this.RowItem;
        this.RowItem++;
        if (this.model.NormTableItem[keyClone].Detail === 1) {
          this.model.NormTableItem = __.insertBeforeKey(this.model.NormTableItem, keyClone + 1, cloneObj);
        } else {
          let allChild = _.filter(this.model.NormTableItem, ['ParentID', this.model.NormTableItem[keyClone].NormTableItemID]);
          if (allChild.length) {
            let lastItemChild = allChild[allChild.length - 1];
            let indexLastChild = _.findIndex(this.model.NormTableItem, ['NormTableItemID', lastItemChild.NormTableItemID]);
            this.model.NormTableItem = __.insertBeforeKey(this.model.NormTableItem, indexLastChild + 1, cloneObj);
          }
        }
      },
      onAddFieldOnTableFormula(){
        let tmpObj = {
          NormTableID: null,
          NormTableNo: '',
          NormTableName: '',
          NormTableItemID: null,
          NormTableItemNo: '',
          NormTableItemName: '',
          FCMinUnitPrice: null,
          FCMaxUnitPrice: null,
          LCMinUnitPrice: null,
          LCMaxUnitPrice: null,
        };
        this.model.NormTableItem[this.currentKey].FormulaArr.push(tmpObj);
      },
      onToggleChildNodes(e, itemParent) {
        let self = this;
        if (e && e.target.classList.contains('fa-minus-square-o')) {
          // close children
          e.target.classList.remove('fa-minus-square-o');
          e.target.classList.add('fa-plus-square-o');
          let allChildTableItem = this.getAllChildTableItem(itemParent, this.model.NormTableItem);
          if (allChildTableItem && allChildTableItem.length) {
            _.forEach(allChildTableItem, function (childTableItem, key) {
              let indexItem = _.findIndex(self.model.NormTableItem, ['NormTableItemID', childTableItem.NormTableItemID]);
              if (indexItem > -1) {
                self.model.NormTableItem[indexItem].Show = false;
              }
            });
          }
        } else {
          // open children
          e.target.classList.remove('fa-plus-square-o');
          e.target.classList.add('fa-minus-square-o');
          let allChildren = _.filter(this.model.NormTableItem, ['ParentID', itemParent.NormTableItemID]);
          if (allChildren.length) {
            _.forEach(allChildren, function (childTableItem, key) {
              let indexItem = _.findIndex(self.model.NormTableItem, ['NormTableItemID', childTableItem.NormTableItemID]);
              if (indexItem > -1) {
                self.model.NormTableItem[indexItem].Show = true;
                $('#table-item-' + self.model.NormTableItem[indexItem].NormTableItemID + ' .bg-tree-icon-action').removeClass('fa-minus-square-o');
                $('#table-item-' + self.model.NormTableItem[indexItem].NormTableItemID + ' .bg-tree-icon-action').addClass('fa-plus-square-o');
              }
            });
          }
        }
      },
      changeExchangeRate(){},
      changeFC(key, FC, LC){
        let ExchangeRate = (this.model.NormTableItem[key].ExchangeRate) ? this.model.NormTableItem[key].ExchangeRate : 0;
        this.model.NormTableItem[key][LC] = this.model.NormTableItem[key][FC] * ExchangeRate;
        this.changeUnitPriceType(key);
      },
      changeBaseUnitPrice(key, type) {
        if (type === 'FC') {
          this.model.NormTableItem[key].FCUnitPrice = this.model.NormTableItem[key].FCBaseUnitPrice * this.model.NormTableItem[key].Quantity;
          this.model.NormTableItem[key].LCBaseUnitPrice = this.model.NormTableItem[key].FCBaseUnitPrice * this.model.NormTableItem[key].ExchangeRate;
          this.model.NormTableItem[key].LCUnitPrice = this.model.NormTableItem[key].LCBaseUnitPrice * this.model.NormTableItem[key].Quantity;
        }
        if (type === 'LC') {
          if (this.model.NormTableItem[key].ExchangeRate) {
            this.model.NormTableItem[key].FCBaseUnitPrice = this.model.NormTableItem[key].LCBaseUnitPrice / this.model.NormTableItem[key].ExchangeRate;
          }
          this.model.NormTableItem[key].FCUnitPrice = this.model.NormTableItem[key].FCBaseUnitPrice * this.model.NormTableItem[key].Quantity;
          this.model.NormTableItem[key].LCUnitPrice = this.model.NormTableItem[key].LCBaseUnitPrice * this.model.NormTableItem[key].Quantity;
        }
      },
      setNormTableItem(key){
        this.currentKey = key;
        this.$refs['norm-table-item'].show();
      },
      onSetFormula(){
        let self = this;
        if (this.currentKey !== null && this.model.NormTableItem[this.currentKey] && this.model.NormTableItem[this.currentKey].FormulaArr) {
          let Formula = '';
          _.forEach(this.model.NormTableItem[this.currentKey].FormulaArr, function (formula, key) {
            Formula += '@DMDT&' + formula.NormTableNo + '&' + formula.NormTableItemNo;
            if (key !== (self.model.NormTableItem[self.currentKey].FormulaArr.length - 1)) {
              Formula += ' + ';
            }
          });
          this.model.NormTableItem[this.currentKey].Formula = Formula;
        }
        this.calculatorNormTableItem(this.currentKey, 'client');
        this.$refs['norm-table-item'].hide();
      },
      calculatorNormTableItem(key, type = 'server'){
        let self = this;

        // type : client | server
        if (this.model.NormTableItem[key].Formula && this.model.NormTableItem[key].Formula !== '@DMDT&sumup') {
          if (type === 'client') {
            let FCMinUnitPrice = 0;
            let FCMaxUnitPrice = 0;
            let LCMinUnitPrice = 0;
            let LCMaxUnitPrice = 0;
            _.forEach(this.model.NormTableItem[key].FormulaArr, function (formula, key) {
              FCMinUnitPrice += (formula.FCMinUnitPrice) ? formula.FCMinUnitPrice : 0;
              FCMaxUnitPrice += (formula.FCMaxUnitPrice) ? formula.FCMaxUnitPrice : 0;
              LCMinUnitPrice += (formula.LCMinUnitPrice) ? formula.LCMinUnitPrice : 0;
              LCMaxUnitPrice += (formula.LCMaxUnitPrice) ? formula.LCMaxUnitPrice : 0;
            });

            this.model.NormTableItem[key].FCMinUnitPrice = FCMinUnitPrice;
            this.model.NormTableItem[key].FCMaxUnitPrice = FCMaxUnitPrice;
            this.model.NormTableItem[key].LCMinUnitPrice = LCMinUnitPrice;
            this.model.NormTableItem[key].LCMaxUnitPrice = LCMaxUnitPrice;

            this.changeUnitPriceType(key);
          } else {
            let requestData = {
              method: 'post',
              url: 'listing/api/norm-table/calculator-table-item',
              data: {
                Formula: this.model.NormTableItem[key].Formula
              }
            };

            if (!this.idParams) {
              let currentFormula = '@DMDT&' + this.model.NormTableNo;
              if (this.model.NormTableItem[key].Formula && this.model.NormTableItem[key].Formula.includes(currentFormula)) {
                let currentPieces = this.model.NormTableItem[key].Formula.split(/(:|-|\*|\+|\(|\)| |\/)+/);
                let arrFormula = {};
                _.forEach(currentPieces, function (currentPiece, key) {
                  currentPiece = currentPiece.trim();
                  if (currentPiece.includes(currentFormula)) {
                    arrFormula[currentPiece] = {
                      FCMinUnitPrice: 0,
                      FCMaxUnitPrice: 0,
                      LCMinUnitPrice: 0,
                      LCMaxUnitPrice: 0
                    };
                    let pieces = currentPiece.split(currentFormula + '&');
                    if (pieces) {
                      let NormTableItemNo = pieces[pieces.length - 1];
                      let normTableItem = _.find(self.model.NormTableItem, ['NormTableItemNo', NormTableItemNo]);
                      if (normTableItem) {
                        arrFormula[currentPiece].FCMinUnitPrice = (normTableItem.FCMinUnitPrice) ? normTableItem.FCMinUnitPrice : 0;
                        arrFormula[currentPiece].FCMaxUnitPrice = (normTableItem.FCMaxUnitPrice) ? normTableItem.FCMaxUnitPrice : 0;
                        arrFormula[currentPiece].LCMinUnitPrice = (normTableItem.LCMinUnitPrice) ? normTableItem.LCMinUnitPrice : 0;
                        arrFormula[currentPiece].LCMaxUnitPrice = (normTableItem.LCMaxUnitPrice) ? normTableItem.LCMaxUnitPrice : 0;
                      }
                    }
                  }
                });
                requestData.data.CurrentFormula = arrFormula;
              }
            }

            // Api edit user
            this.$store.commit('isLoading', true);
            ApiService.setHeader();
            ApiService.customRequest(requestData).then((responses) => {
              let responsesData = responses.data;
              if (responsesData.status === 1) {
                if (responsesData.data) {
                  self.model.NormTableItem[key].FCMinUnitPrice = responsesData.data.FCMinUnitPrice;
                  self.model.NormTableItem[key].FCMaxUnitPrice = responsesData.data.FCMaxUnitPrice;
                  self.model.NormTableItem[key].LCMinUnitPrice = responsesData.data.LCMinUnitPrice;
                  self.model.NormTableItem[key].LCMaxUnitPrice = responsesData.data.LCMaxUnitPrice;
                  this.$bvToast.toast('Cập nhật thành công', {
                    variant: 'success',
                    title: 'Thông báo',
                    solid: true
                  });
                } else {
                  this.$bvToast.toast('Đảm bảo số bị chia khác 0', {
                    variant: 'warning',
                    title: 'Thông báo',
                    solid: true
                  });
                }

                self.changeUnitPriceType(key);
              } else {
                this.$bvToast.toast('Định dạng công thức chưa đúng', {
                  variant: 'warning',
                  title: 'Thông báo',
                  solid: true
                });
              }
              self.$store.commit('isLoading', false);
            }, (error) => {
              this.$bvToast.toast('Đảm bảo số bị chia khác 0', {
                variant: 'warning',
                title: 'Thông báo',
                solid: true
              });
              self.$store.commit('isLoading', false);
            });
          }
        } else if(this.model.NormTableItem[key].Formula === '@DMDT&sumup'){
          // sumup level 1
          let items = _.filter(this.model.NormTableItem, ['Level', 1]);
          this.model.NormTableItem[key].FCAmount = 0;
          this.model.NormTableItem[key].LCAmount = 0;
          _.forEach(items, function (item, keySum) {
            if (item.NormTableItemID !== self.model.NormTableItem[key].NormTableItemID) {
              let FCAmount = (item.FCAmount) ? item.FCAmount : 0;
              self.model.NormTableItem[key].FCAmount += FCAmount;
            }
          });
          this.model.NormTableItem[key].LCAmount = this.model.NormTableItem[key].FCAmount * this.model.NormTableItem[key].ExchangeRate;
        } else {
          let sumUp = this.sumUpChildren(key);
          if (sumUp) {
            this.$bvToast.toast('Cập nhật thành công', {
              variant: 'success',
              title: 'Thông báo',
              solid: true
            });
          } else {
            this.$bvToast.toast('Chưa nhập công thức', {
              variant: 'warning',
              title: 'Thông báo',
              solid: true
            });
          }
        }
      },
      reCalculatorNormTableItem() {
        let self = this;
        let calculators = _.filter(this.model.NormTableItem, ['checkCalculator', true]);
        let formulas = [];
        let currentFormula = {};
        _.forEach(calculators, function (calculator, key) {
          if (calculator.Formula) {
            formulas.push({
              NormTableItemID: calculator.NormTableItemID,
              Formula: calculator.Formula
            });
            let formulaTmp = self.getCurrentFormula(calculator.Formula);
            if (!_.isEmpty(formulaTmp)) {
              _.forEach(formulaTmp, function (value, key) {
                if (!_.has(currentFormula, key)) {
                  currentFormula[key] = value;
                }
              });
            }
          }
        });

        let requestData = {
          method: 'post',
          url: 'listing/api/norm-table/re-calculator-table-item',
          data: {
            Formulas: formulas,
            CurrentFormula: currentFormula
          }
        };

        // Api edit user
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            if (responsesData.data) {
              _.forEach(responsesData.data, function (value, key) {
                let itemIndex = _.findIndex(self.model.NormTableItem, ['NormTableItemID', Number(key)]);
                if (itemIndex > -1) {
                  self.model.NormTableItem[itemIndex].FCMinUnitPrice = value.FCMinUnitPrice;
                  self.model.NormTableItem[itemIndex].FCMaxUnitPrice = value.FCMaxUnitPrice;
                  self.model.NormTableItem[itemIndex].LCMinUnitPrice = value.LCMinUnitPrice;
                  self.model.NormTableItem[itemIndex].LCMaxUnitPrice = value.LCMaxUnitPrice;
                  self.changeUnitPriceType(itemIndex);
                }
              });
              this.$bvToast.toast('Cập nhật thành công', {
                variant: 'success',
                title: 'Thông báo',
                solid: true
              });
            }
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          self.$store.commit('isLoading', false);
        });
      },
      getCurrentFormula(Formula) {
        let self = this;
        let currentFormula = '@DMDT&' + this.model.NormTableNo;
        let arrFormula = {};
        if (Formula && Formula.includes(currentFormula)) {
          let currentPieces = Formula.split(/(:|-|\*|\+|\(|\)| |\/)+/);
          _.forEach(currentPieces, function (currentPiece, key) {
            currentPiece = currentPiece.trim();
            if (currentPiece.includes(currentFormula)) {
              arrFormula[currentPiece] = {
                FCMinUnitPrice: 0,
                FCMaxUnitPrice: 0,
                LCMinUnitPrice: 0,
                LCMaxUnitPrice: 0
              };
              let pieces = currentPiece.split(currentFormula + '&');
              if (pieces) {
                let NormTableItemNo = pieces[pieces.length - 1];
                let normTableItem = _.find(self.model.NormTableItem, ['NormTableItemNo', NormTableItemNo]);
                if (normTableItem) {
                  arrFormula[currentPiece].FCMinUnitPrice = (normTableItem.FCMinUnitPrice) ? normTableItem.FCMinUnitPrice : 0;
                  arrFormula[currentPiece].FCMaxUnitPrice = (normTableItem.FCMaxUnitPrice) ? normTableItem.FCMaxUnitPrice : 0;
                  arrFormula[currentPiece].LCMinUnitPrice = (normTableItem.LCMinUnitPrice) ? normTableItem.LCMinUnitPrice : 0;
                  arrFormula[currentPiece].LCMaxUnitPrice = (normTableItem.LCMaxUnitPrice) ? normTableItem.LCMaxUnitPrice : 0;
                }
              }
            }
          });
        }
        return arrFormula;
      },
      changeUnitPriceType(key){
        switch (this.model.NormTableItem[key].UnitPriceType) {
          case 1:
            this.model.NormTableItem[key].FCUnitPrice = this.model.NormTableItem[key].FCMaxUnitPrice;
            this.model.NormTableItem[key].LCUnitPrice = this.model.NormTableItem[key].LCMaxUnitPrice;
            break;
          case 2:
            this.model.NormTableItem[key].FCUnitPrice = this.model.NormTableItem[key].FCMinUnitPrice;
            this.model.NormTableItem[key].LCUnitPrice = this.model.NormTableItem[key].LCMinUnitPrice;
            break;
          case 3:
            this.model.NormTableItem[key].FCUnitPrice = (this.model.NormTableItem[key].FCMinUnitPrice + this.model.NormTableItem[key].FCMaxUnitPrice) / 2;
            this.model.NormTableItem[key].LCUnitPrice = (this.model.NormTableItem[key].LCMinUnitPrice + this.model.NormTableItem[key].LCMaxUnitPrice) / 2;
            break;
          case 4:
            break;
          default:
            this.model.NormTableItem[key].FCUnitPrice = this.model.NormTableItem[key].FCMaxUnitPrice;
            this.model.NormTableItem[key].LCUnitPrice = this.model.NormTableItem[key].LCMaxUnitPrice;
            break;
        }
        this.changeAmount(key);
      },
      changeFCUnitPrice(key){
        this.model.NormTableItem[key].FCAmount = this.model.NormTableItem[key].FCUnitPrice * this.model.NormTableItem[key].Quantity;
        this.model.NormTableItem[key].LCUnitPrice = this.model.NormTableItem[key].FCUnitPrice * this.model.NormTableItem[key].ExchangeRate;
        this.model.NormTableItem[key].LCAmount = this.model.NormTableItem[key].LCUnitPrice * this.model.NormTableItem[key].Quantity;

      },
      changeAmount(key){
        this.model.NormTableItem[key].FCAmount = this.model.NormTableItem[key].FCUnitPrice * this.model.NormTableItem[key].Quantity;
        this.model.NormTableItem[key].LCAmount = this.model.NormTableItem[key].LCUnitPrice * this.model.NormTableItem[key].Quantity;
      },
      getAllChildTableItem(item, tableItemArr){
        let self = this, listChild = [];
        let allChild = _.filter(tableItemArr, ['ParentID', item.NormTableItemID]);
        if (allChild.length) {
          allChild = _.orderBy(allChild, ['NormTableItemID'], ['asc']);
          _.forEach(allChild, function (value, key) {
            listChild.push(value);
            if (_.filter(tableItemArr, ['ParentID', value.NormTableItemID]).length) {
              let recursiveArr = self.getAllChildTableItem(value, tableItemArr);
              recursiveArr = _.orderBy(recursiveArr, ['NormTableItemID', 'asc']);
              _.forEach(recursiveArr, function (recursive, key) {
                listChild.push(recursive);
              });
            }

          });
        }
        return listChild;
      },
      onChangeNorm(data, key){
        this.model.NormTableItem[key].NormTableItemName = this.model.NormTableItem[key].NormName;
      },
      changeFCAmount(key){
        this.model.NormTableItem[key].LCAmount = this.model.NormTableItem[key].FCAmount * this.model.NormTableItem[key].ExchangeRate;
      },
      onDeleteFieldOnNormTableItem(key) {
        let self = this;
        if (this.model.NormTableItem[key].Detail === 1) {
          if (this.model.NormTableItem[key].ParentID) {
            let siblings = _.filter(this.model.NormTableItem, ['ParentID', this.model.NormTableItem[key].ParentID]);
            if (siblings && siblings.length === 1) {
              let indexParent = _.findIndex(this.model.NormTableItem, ['NormTableItemID', this.model.NormTableItem[key].ParentID]);
              if (indexParent > -1) {
                this.model.NormTableItem[indexParent].Detail = 1;
                this.model.NormTableItem[indexParent].HaveChildren = false;
              }
            }
          }
        }else {

          // lui cap
          let allChildTableItem = this.getAllChildTableItem(this.model.NormTableItem[key], this.model.NormTableItem);
          _.forEach(allChildTableItem, function (item, keyItem) {
            let indexItem = _.findIndex(self.model.NormTableItem, ['NormTableItemID', item.NormTableItemID]);
            if (indexItem > -1) {
              self.model.NormTableItem[indexItem].Level -= 1;
            }
          });

          // gan lai cha cho con
          let children = _.filter(this.model.NormTableItem, ['ParentID', this.model.NormTableItem[key].NormTableItemID]);
          _.forEach(children, function (sonny, keySonny) {
            let indexSonny = _.findIndex(self.model.NormTableItem, ['NormTableItemID', sonny.NormTableItemID]);
            if (indexSonny > -1) {
              self.model.NormTableItem[indexSonny].ParentID = self.model.NormTableItem[key].ParentID;
            }
          });

        }
        this.model.NormTableItem.splice(key, 1);
      },
      sumUpChildren(key){
        let self = this;
        if (this.model.NormTableItem[key].Detail !== 1) {
          let allChildren = _.filter(this.model.NormTableItem, ['ParentID', this.model.NormTableItem[key].NormTableItemID]);
          if (allChildren.length) {
            self.model.NormTableItem[key].FCAmount = 0;
            _.forEach(allChildren, function (item, keyItem) {
              let FCAmount = (item.FCAmount) ? item.FCAmount : 0;
              self.model.NormTableItem[key].FCAmount += FCAmount;
            });
            self.model.NormTableItem[key].LCAmount = self.model.NormTableItem[key].FCAmount * self.model.NormTableItem[key].ExchangeRate;
            return true;
          }
        }

        return false;
      },
      onDeleteFieldOnTableFormula(key){
        this.model.NormTableItem[this.currentKey].FormulaArr.splice(key, 1);
      },
      autoCorrectedDatePipe: () => {
        return createAutoCorrectedDatePipe('dd/mm/yyyy')
      },
      autoCorrectedDateTimePipe: () => {
        return createAutoCorrectedDatePipe('dd/mm/yyyy hh:mm')
      },
      changeNormTableItem(moved) {},
      changeNorm(data, key){
        this.model.NormTableItem[key].NormTableItemName = data.NormName;
        this.model.NormTableItem[key].UomID = data.UomID;
        this.model.NormTableItem[key].UomName = data.UomName;
        this.model.NormTableItem[key].UomNo = data.UomNo;
      },
      sumUpNormTableItem() {
        let self = this;
        let maxLevel = _.maxBy(this.model.NormTableItem, 'Level');
        if (maxLevel) {
          for (let i = maxLevel.Level; i > 0; i--) {
            let tmpSumUPItems = _.filter(self.model.NormTableItem, ['Level', i]);
            if (tmpSumUPItems && tmpSumUPItems.length) {
              const sumParents = _(tmpSumUPItems)
                .groupBy('ParentID')
                .map((item, ParentID) => ({
                  NormTableItemID: ParentID,
                  FCAmount: _.sumBy(item, 'FCAmount'),
                  LCAmount: _.sumBy(item, 'LCAmount')
                }))
                .value();

              if (sumParents && sumParents.length) {
                _.forEach(sumParents, function (sumParent, key) {
                  if (sumParent.NormTableItemID) {
                    let sumItemIndex = _.findIndex(self.model.NormTableItem, ['NormTableItemID', Number(sumParent.NormTableItemID)]);
                    if (sumItemIndex > -1) {
                      self.model.NormTableItem[sumItemIndex].FCAmount = sumParent.FCAmount;
                      self.model.NormTableItem[sumItemIndex].LCAmount = self.model.NormTableItem[sumItemIndex].FCAmount * self.model.NormTableItem[sumItemIndex].ExchangeRate;
                    }
                  }
                });
              }
            }
          }
        }
        this.$bvToast.toast('Cập nhật thành công', {
          variant: 'success',
          title: 'Thông báo',
          solid: true
        });
      },
      changePage(){
        let self = this;
        let indexStart = (this.currentPage - 1) * Number(this.perPage);
        let indexEnd = this.currentPage * Number(this.perPage) - 1;
        _.forEach(self.model.NormTableItem, function (NormTableItem, key) {
          self.model.NormTableItem[key].ShowPagination = false;
        });
        for (let key = indexStart; key <= indexEnd; key++) {
          if (self.model.NormTableItem[key]) {
            self.model.NormTableItem[key].ShowPagination = true;
          }
        }
      },
      changePerPage(perPage){
        this.perPage = String(perPage);
        this.currentPage = 1;
        this.changePage();
      },
      changeSaveRate(key) {
        if (this.model.NormTableItem[key].SaveRate && this.model.NormTableItem[key].FCAmount) {
          this.model.NormTableItem[key].FCSaveAmount = this.model.NormTableItem[key].FCAmount * this.model.NormTableItem[key].SaveRate / 100;
          this.model.NormTableItem[key].LCSaveAmount = this.model.NormTableItem[key].FCSaveAmount * this.model.NormTableItem[key].ExchangeRate;
        }else {
          this.model.NormTableItem[key].FCSaveAmount = 0;
          this.model.NormTableItem[key].LCSaveAmount = 0;
        }
      }
    },
    watch: {
      idParams() {
        this.fetchData();
      },
      currentPage(){
        this.changePage();
      },
      'model.NormTableItem': {
        handler(val){
          // do stuff
          this.totalRows = this.model.NormTableItem.length;
          let currentItem = _.filter(this.model.NormTableItem, ['ShowPagination', true]);
          if (!currentItem.length) {
            if (this.currentPage > 1) {
              this.currentPage = this.currentPage - 1;
            }
            this.changePage();
          }
        },
        deep: true
      }
    }
  }
</script>
<style lang="css">
  .table.b-table thead th {
    vertical-align: middle;
  }
  .component-norm-table-form .main-header-icon .dropdown-toggle {
    border-left: 1px solid #a5aeb7;
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
  }
  .bg-tree-tr{
    position: relative;
  }
  .bg-tree-content {
    display: flex;
    align-items: center;
    position: relative;
  }
  .bg-tree-content input {
    padding-left: 20px;
  }
  .bg-tree-td:before{
    display: inline-block;
    content: "";
    position: relative;
    top: 0;
    left: 13px;
    width: 16px;
    height: 0;
    border-top: 1px dotted #858585;
    z-index: 1;
  }
  .bg-tree-td:after{
    display: inline-block;
    content: "";
    position: absolute;
    top: 0;
    left: 12px;
    width: 1px;
    height: 100%;
    border-left: 1px dotted #858585;
    z-index: 1;
  }
  .bg-tree-dot {
    position: absolute;
    width: 1px;
    height: 100%;
    left: 12px;
  }
  .bg-tree-dot:before {
    display: inline-block;
    content: "";
    left: 0;
    width: 1px;
    height: 100%;
    border-left: 1px dotted #858585;
    z-index: 1;
  }
  .bg-tree-icon-action {
    position: absolute;
    left: 7px;
    background: #fff;
    z-index: 2;
    cursor: pointer;
  }
  #dropdown-action button {
    padding: 0;
    background: #fff;
    border: 0;
  }
  .table-pagination .pagination{
    margin: 0;
  }
</style>
