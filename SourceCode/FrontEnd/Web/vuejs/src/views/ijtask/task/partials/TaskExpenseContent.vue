<template>
  <div>
    <div v-if="!isForm">
      <div class="form-group row align-items-center">
        <div class="col-lg-2 col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Loại</div>
        <div class="col-lg-2" v-if="ViewPerExpenseDetailTransType">
          {{TransTypeText[value.master.TransType]}}
        </div>
        <div class="col-lg-2" v-else></div>
        <div class="col-lg-2 col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap" >Ngày</div>
        <div class="col-lg-2" v-if="ViewPerExpenseDetailTransDate">
          {{value.master.TransDate}}
        </div>
        <div class="col-lg-2" v-else></div>
      </div>
      <div class="form-group row">
        <label class="col-md-2" style="white-space: nowrap">Mô tả</label>
        <div class="col-lg-22" v-if="ViewPerExpenseComment">
          {{value.master.Comment}}
        </div>
        <div class="col-lg-22" v-else>
        </div>
      </div>
    </div>
    <div v-if="isForm" class="el-first-modal">

      <div class="form-group row align-items-center">
        <div class="col-lg-2 col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Loại</div>
        <div class="col-lg-2" v-if="ViewPerExpenseDetailTransType">
          <b-form-select :options="optionType" v-if="EditPerExpenseDetailTransType" v-model="value.master.TransType"></b-form-select>
          <input type="text" disabled class="form-control" placeholder="" :value="value.master.TransType" v-else/>
        </div>
        <div class="col-lg-2" v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </div>
        <div class="col-lg-2 col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Ngày</div>
        <div class="col-lg-2" v-if="ViewPerExpenseDetailTransType">
          <IjcoreDatePicker v-model="value.master.TransDate" v-if="EditPerExpenseDetailTransType">
          </IjcoreDatePicker>
          <input type="text" disabled class="form-control" placeholder="" :value="value.master.TransDate" v-else/>
        </div>
        <div class="col-lg-2" v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </div>
      </div>
      <div class="form-group row">
        <label class="col-md-2" style="white-space: nowrap">Mô tả</label>
        <div class="col-lg-22" v-if="ViewPerExpenseComment">
          <textarea v-model="value.master.Comment" class="form-control" rows="2"  v-if="EditPerExpenseComment"
                    placeholder="Nhập diễn giải"></textarea>
          <textarea disabled class="form-control" rows="2"  :value="value.master.Comment" v-else
                    placeholder="Nhập diễn giải"></textarea>
        </div>
        <div class="col-lg-2" v-else>
          <textarea disabled class="form-control" rows="2"></textarea>
        </div>
      </div>
    </div>
    <div class="table-responsive div-table-form" style="overflow-x: scroll;margin-bottom: 10px !important;">
      <table class="not-border" v-if="!isForm">
        <thead>
        <tr class="text-left">
          <th class="pr-3" v-if="ViewPerExpenseDetailDescription">Nội dung chi</th>
          <th class="pr-3" v-if="ViewPerExpenseDetailUomID">Đơn vị tính</th>
          <th class="pr-3" v-if="ViewPerExpenseDetailQuantity">Số lượng</th>
          <th class="pr-3" v-if="ViewPerExpenseDetailUnitPrice">Đơn giá</th>
          <th class="pr-3" v-if="ViewPerExpenseDetailAmount">Thành tiền</th>
          <th class="pr-3" v-if="ViewPerExpenseDetailTaxRate">Thuế suất</th>
          <th class="pr-3" v-if="ViewPerExpenseDetailTaxAmount">Tiền thuế</th>
          <th class="td-action"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(item, key) in value.detail">
          <td class="pr-3" v-if="ViewPerExpenseDetailDescription">{{item.Description}}</td>
          <td class="pr-3" v-if="ViewPerExpenseDetailUomID">{{item.UomName}}</td>
          <td class="pr-3" v-if="ViewPerExpenseDetailQuantity">{{item.Quantity|convertNumberToText}}</td>
          <td class="pr-3" v-if="ViewPerExpenseDetailUnitPrice">{{item.UnitPrice|convertNumberToText}}</td>
          <td class="pr-3" v-if="ViewPerExpenseDetailAmount">{{item.Amount|convertNumberToText}}</td>
          <td class="pr-3" v-if="ViewPerExpenseDetailTaxRate">{{item.TaxRate|convertNumberToText}}</td>
          <td class="pr-3" v-if="ViewPerExpenseDetailTaxAmount">{{item.TaxAmount|convertNumberToText}}</td>
        </tr>
        </tbody>
      </table>

      <table v-if="isForm" class="table b-table table-sm table-bordered table-editable pb">
        <thead>
        <tr class="text-center">
          <th>Nội dung chi</th>
          <th>Đơn vị tính</th>
          <th>Số lượng</th>
          <th>Đơn giá</th>
          <th>Thành tiền</th>
          <th>Thuế suất</th>
          <th>Tiền thuế</th>
          <th></th>
        </tr>
        </thead>
        <tbody style="width: auto">
        <tr v-for="(item, key) in dataList" style="width: auto;">
          <td class="EmployeeName" v-if="ViewPerExpenseDetailDescription">
            <IjcoreDescriptionModalListing
              v-model="value.detail[key]" :title="'khoản chi'"
              FieldID="ExpenseID"
              FieldNo="ExpenseNo"
              FieldName="ExpenseName"
              :field-update="['UomID']"
             :api="'/listing/api/common/list'" :table="'expense'">
            </IjcoreDescriptionModalListing>
          </td>
          <td class="EmployeeName" style="width: 290px" v-else>
            <input type="text" disabled class="form-control" placeholder=""/>
          </td>
          <td style="width: 60px" v-if="ViewPerExpenseDetailUomID">
            <b-form-select v-model="value.detail[key].UomID" :options="Uom" v-if="ViewPerExpenseDetailUomID"
                           v-on:change="changeUom(key)"></b-form-select>
            <input type="text" disabled v-else class="form-control" placeholder="" :value="value.detail[key].UomName"/>
          </td>
          <td style="width: 60px"  v-else>
            <input type="text" disabled class="form-control" placeholder=""/>
          </td>
          <td style="width: 60px" v-if="ViewPerExpenseDetailQuantity">
            <IjcoreNumber v-model="value.detail[key].Quantity" :keyarray="key" @changed="updateAmount(key)" v-if="ViewPerExpenseDetailQuantity"
                          :name="'Quantity'"></IjcoreNumber>
            <input type="text" disabled v-else class="form-control" placeholder="" :value="value.detail[key].Quantity"/>
          </td>
          <td style="width: 60px"  v-else>
            <input type="text" disabled class="form-control" placeholder=""/>
          </td>
          <td class="td-number" v-if="ViewPerExpenseDetailUnitPrice">
            <IjcoreNumber v-model="value.detail[key].UnitPrice" @changed="updateAmount(key)" v-if="ViewPerExpenseDetailUnitPrice"></IjcoreNumber>
            <input type="text" disabled v-else class="form-control" placeholder="" :value="value.detail[key].UnitPrice"/>
          </td>
          <td class="td-number"   v-else>
            <input type="text" disabled class="form-control" placeholder=""/>
          </td>
          <td class="td-number" v-if="ViewPerExpenseDetailAmount" style="width: 80px;">
            <IjcoreNumber v-model="value.detail[key].Amount" v-if="ViewPerExpenseDetailAmount"></IjcoreNumber>
            <input type="text" disabled v-else class="form-control" placeholder="" :value="value.detail[key].Amount"/>
          </td>
          <td class="td-number"   v-else style="width: 80px;">
            <input type="text" disabled class="form-control" placeholder=""/>
          </td>
          <td style="width: 80px" v-if="ViewPerExpenseDetailTaxRate">
            <IjcoreNumber v-model="value.detail[key].TaxRate" @changed="updateAmount(key)" v-if="ViewPerExpenseDetailTaxRate"></IjcoreNumber>
            <input type="text" disabled v-else class="form-control" placeholder="" :value="value.detail[key].TaxRate"/>
          </td>
          <td style="width: 80px"   v-else>
            <input type="text" disabled class="form-control" placeholder=""/>
          </td>
          <td class="td-number" v-if="EditPerExpenseDetailTaxAmount" style="width: 80px;">
            <IjcoreNumber v-model="value.detail[key].TaxAmount" v-if="EditPerExpenseDetailTaxAmount"></IjcoreNumber>
            <input type="text" disabled v-else class="form-control" placeholder="" :value="value.detail[key].TaxAmount"/>
          </td>
          <td class="td-number"   v-else style="width: 80px;">
            <input type="text" disabled class="form-control" placeholder=""/>
          </td>
          <td style="text-align: center;width: 50px;"><i title="Xóa" @click="deleteLine(key)"
                                                                           class="fa fa-trash-o"
                                                                           style="font-size: 18px; cursor: pointer;"></i>
          </td>
        </tr>
        </tbody>
      </table>

      <a @click="addLine()" v-if="isForm" class="new-row"><i aria-hidden="true"
                                                             class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm
        mới</a>
    </div>
  </div>
</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';
  import moment from 'moment';
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
  import Swal from 'sweetalert2';
  import IjcoreNumber from "../../../../components/IjcoreNumber";
  import IjcoreDescriptionModalListing from "../../../../components/IjcoreDescriptionModalListing";

  export default {
    name: 'TaskExpenseContent',
    mixins: [mixinLists],
    components: {
      IjcoreDescriptionModalListing,
      IjcoreNumber,
      IjcoreDatePicker,
      IjcoreModalListing
    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        listtable: [],
        tableName: '',
        search: '',
        lenghNo: 0,
        optionType: [
          {value: 1, text: "Kế hoạch"},
          {value: 2, text: "Thực tế"},
        ],
        dataList: {},
        TransTypeText: {'1': "Kế hoạch", '2': "Thực tế"},

        ViewPerExpenseDetailTransType: true,
        ViewPerExpenseDetailTransDate: true,
        ViewPerExpenseDetailExpenseID: true,
        ViewPerExpenseDetailDescription: true,
        ViewPerExpenseDetailUomID: true,
        ViewPerExpenseDetailQuantity: true,
        ViewPerExpenseDetailUnitPrice: true,
        ViewPerExpenseDetailAmount: true,
        ViewPerExpenseDetailTaxRate: true,
        ViewPerExpenseDetailTaxAmount: true,
        ViewPerExpenseComment: true,

        EditPerExpenseDetailTransType: true,
        EditPerExpenseDetailTransDate: true,
        EditPerExpenseDetailExpenseID: true,
        EditPerExpenseDetailDescription: true,
        EditPerExpenseDetailUomID: true,
        EditPerExpenseDetailQuantity: true,
        EditPerExpenseDetailUnitPrice: true,
        EditPerExpenseDetailAmount: true,
        EditPerExpenseDetailTaxRate: true,
        EditPerExpenseDetailTaxAmount: true,
        EditPerExpenseComment: true,
      }
    },
    created() {
      if (this.isAddNew) {
        this.value.master = {
          TransType: '',
          TransDate: __.convertDateTime(new Date()),
          Comment: '',
        };
        this.value.detail = [{
          ExpenseNo: '',
          Description: '',
          UomID: '',
          Quantity: '',
          UnitPrice: '',
          Amount: '',
          TaxRate: '',
          TaxAmount: '',
          addnew: true,
        }];
        this.dataList = this.value.detail;
      } else {
        this.dataList = this.value.detail;
      }
    },
    mounted() {
      this.ViewPerExpenseDetailTransType = __.perViewColumn(this.perDetail, 'TransType')
      this.ViewPerExpenseDetailTransDate = __.perViewColumn(this.perDetail, 'TransDate')
      this.ViewPerExpenseDetailExpenseID = __.perViewColumn(this.perDetail, 'ExpenseID')
      this.ViewPerExpenseDetailDescription = __.perViewColumn(this.perDetail, 'Description')
      this.ViewPerExpenseDetailUomID = __.perViewColumn(this.perDetail, 'UomID')
      this.ViewPerExpenseDetailQuantity = __.perViewColumn(this.perDetail, 'Quantity')
      this.ViewPerExpenseDetailUnitPrice = __.perViewColumn(this.perDetail, 'UnitPrice')
      this.ViewPerExpenseDetailAmount = __.perViewColumn(this.perDetail, 'Amount')
      this.ViewPerExpenseDetailTaxRate = __.perViewColumn(this.perDetail, 'TaxRate')
      this.ViewPerExpenseDetailTaxAmount = __.perViewColumn(this.perDetail, 'TaxAmount')
      this.ViewPerExpenseComment = __.perViewColumn(this.per, 'comment')


      this.EditPerExpenseDetailTransType = __.perEditColumn(this.perDetail, 'TransType')
      this.EditPerExpenseDetailTransDate = __.perEditColumn(this.perDetail, 'TransDate')
      this.EditPerExpenseDetailExpenseID = __.perEditColumn(this.perDetail, 'ExpenseID')
      this.EditPerExpenseDetailDescription = __.perEditColumn(this.perDetail, 'Description')
      this.EditPerExpenseDetailUomID = __.perEditColumn(this.perDetail, 'UomID')
      this.EditPerExpenseDetailQuantity = __.perEditColumn(this.perDetail, 'Quantity')
      this.EditPerExpenseDetailUnitPrice = __.perEditColumn(this.perDetail, 'UnitPrice')
      this.EditPerExpenseDetailAmount = __.perEditColumn(this.perDetail, 'Amount')
      this.EditPerExpenseDetailTaxRate = __.perEditColumn(this.perDetail, 'TaxRate')
      this.EditPerExpenseDetailTaxAmount = __.perEditColumn(this.perDetail, 'TaxAmount')
      this.EditPerExpenseComment = __.perEditColumn(this.per, 'comment')
    },
    methods: {
      changeUom(key) {
        var result = this.Uom.filter(obj => {
          if (obj.value === this.value.detail[key].UomID) {
            return obj;
          }
        });

        this.value.detail[key].UomName = result[0].text;
      },
      updateDescription(key) {
        if (!this.value.detail[key].Description) {
          this.value.detail[key].Description = this.value.detail[key].ExpenseName;
        }
      },
      updateAmount(key) {
        let dataTemp = this.value.detail[key];
        let Quantity = dataTemp.Quantity;
        let UnitPrice = dataTemp.UnitPrice;
        let TaxRate = dataTemp.TaxRate;
        let TaxAmount = 0;
        let Amount = 0;
        Amount = Quantity * UnitPrice;
        dataTemp.TaxAmount = Amount * TaxRate / 100;
        dataTemp.Amount = Quantity * UnitPrice;
      },
      formatDate(data) {
        data = data.split(' ');
        data = data[0];
        data = data.split('-');
        let dd = data[2];
        let mm = data[1];
        let yyyy = data[0];
        data = dd + '/' + mm + '/' + yyyy;
        return data;
      },
      fetchData() {
      },
      onSaveModal() {

      },
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onHideModal() {
        this.$refs['modal'].hide();
      },
      onToggleModal() {
        let self = this;
        this.currentPage = 1;
        this.$refs['modal'].show();
      },
      onResetModal() {
      },
      clickText: function (event, key) {
        if (this.isForm) {
          event.target.hidden = true;
          event.target.nextSibling.hidden = false;
          this.value[key].addnew = true;
        }
      },
      hideInput: function (event, loop, key) {
        let element = event.target;
        if (event.target.value) {
          for (let i = 1; i <= loop; i++) {
            element = element.parentElement;
          }
          element.hidden = true;
          element.previousSibling.hidden = false;
          this.value[key].addnew = false;
        }
      },
      addLine() {
        this.dataList.push({
          ExpenseNo: '',
          ExpenseID: '',
          ExpenseName: '',
          Description: '',
          UomID: '',
          UomName: '',
          Quantity: '',
          UnitPrice: '',
          Amount: '',
          TaxRate: '',
          TaxAmount: '',
          addnew: true,
        });
      },
      updateHour(key) {
        if (this.value[key].StartDate && this.value[key].DueDate) {
          let self = this;
          let urlApi = '/task/api/task/get-hour';
          let requestData = {
            method: 'post',
            url: urlApi,
            data: {
              StartDate: self.value[key].StartDate,
              DueDate: self.value[key].DueDate,
              CalendarTypeID: self.Task.CalendarTypeID,
            },

          };
          this.$store.commit('isLoading', true);
          ApiService.customRequest(requestData).then((response) => {
            let dataResponse = response.data;

            if (dataResponse.status === 1) {
              self.value[key].Duration = dataResponse.data;
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

          // scroll to top perfect scroll
          const container = document.querySelector('.b-table-sticky-header');
          if (container) container.scrollTop = 0;
        }
      },
      deleteLine(key) {
        this.value.detail.splice(key, 1);
      }
    },
    watch: {
      dataList() {
        this.value.detail = this.dataList;
      },
    },
    props: {
      value: {},
      title: {},
      name: {},
      api: {},
      table: {},
      Task: {},
      isForm: false,
      isAddNew: false,
      TaskExpenseTransContent: {},
      Uom: {},
      per: {},
      perDetail: {}
    },
  }
</script>
<style>
  .readonly {
    background-color: #fff !important;
  }

  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }

  .modal-footer ol, .modal-footer ul, .modal-footer dl {
    margin-bottom: 0;
  }


  .mx-datepicker {
    display: block !important;
  }

  .EmployeeName {
    width: 200px;
  }


  .td-number {
    width: 130px;
  }

  .div-table-form {
    overflow-x: scroll;
  }

  .div-table-form td {
    max-width: none !important;
  }

  .td-action {
    text-align: center;
    width: 50px;
    position: absolute;
    right: 16px;
    height: 34px;
    padding-top: 6px !important;
    background: #fff;
  }
</style>
