<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết" :style="StyleIcon">
    <i class="fa fa-plus-square-o ij-icon ij-icon-plus" aria-hidden="true" v-if="addnew"></i>
    <i class="fa fa-edit ij-icon" aria-hidden="true" v-if="!addnew" style="margin-right: 6px;"></i>
    <b-modal ref="modal" id="modal-form-input-task-general" size="xl" no-close-on-backdrop @hide="onHideModalDataflow($event)">
      <template slot="modal-title">
        <i class="fa fa-plus" v-if="addnew"></i><i class="fa fa-edit" v-if="!addnew"></i> {{this.title}}
      </template>

  <div class="main-body main-body-view-action" ref="DivContainerTable">
    <div class="form-group row align-items-center">
      <label class="col-md-1" style="white-space: nowrap">Tên công ty </label>
      <div class="col-md-16" >
        <IjcoreModalListing v-model="CustomerQuotationTrans" :title="'công ty'" :api="'/listing/api/common/list'"
                            :table="'company'" :FieldID="'CompanyID'" :FieldName="'CompanyName'"
                            :FieldUpdate="['Address', 'TaxCode', 'Tel', 'Fax']">
        </IjcoreModalListing>
      </div>
      <label class="col-md-1" style="white-space: nowrap">Ngày báo giá</label>
      <div class="col-md-4 DateTimeText">
        <IjcoreDatePicker v-model="CustomerQuotationTrans.TransDate" style="width: 160px">
        </IjcoreDatePicker>
      </div>

    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-1" style="white-space: nowrap">Địa chỉ </label>
      <div class="col-md-16" >
        <input v-model="CustomerQuotationTrans.Address" type="text" class="form-control" placeholder="Địa chỉ công ty"/>
      </div>
      <label class="col-md-1" style="white-space: nowrap">Số báo giá</label>
      <div class="col-md-2">
        <input v-model="CustomerQuotationTrans.TransNo" class="form-control"/>
      </div>

    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-1" style="white-space: nowrap">Người nhận</label>
      <div class="col-md-6">
        <IjcoreModalListing v-model="CustomerQuotationTrans" :title="'người nhận'" :api="'/listing/api/common/list'"
                            :table="'customer_contact'" :FieldID="'CustomerID'" :FieldName="'ContactName'"
                            :FieldUpdate="['CustomerName', 'CustomerNo', 'CustomerTaxCode', 'CustomerAddress', 'CustomerEmail', 'CustomerHandPhone']">
        </IjcoreModalListing>
      </div>
      <label class="col-md-1" style="white-space: nowrap">Khách hàng</label>
      <div class="col-md-8">
        <IjcoreModalListing v-model="CustomerQuotationTrans" :title="'khách hàng'" :api="'/listing/api/common/list'"
                            :table="'customer'" :FieldID="'CustomerID'" :FieldName="'CustomerName'"
                            :FieldUpdate="['CustomerName', 'CustomerNo', 'TaxCode', 'Address', 'Email', 'OfficePhone']"
                            :FieldWhere="{CustomerID : CustomerQuotationTrans['CustomerID']}">
        </IjcoreModalListing>
      </div>

    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-1" style="white-space: nowrap">Tiền tệ</label>
      <div class="col-md-6">
        <b-form-select :options="dataCcyOption" @change="updateprice"
                       v-model="CustomerQuotationTrans.CcyID"></b-form-select>
      </div>
      <label class="col-md-1" style="white-space: nowrap">Tỷ giá</label>
      <div class="col-md-8">
        <input v-model="CustomerQuotationTrans.ExchangeRate" class="form-control" @change="updateprice"/>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-1" style="white-space: nowrap">Tệp đính kèm</label>
      <div class="col-md-2" >
        <a id="attachments" @change="uploadFieldChange">
          <input type="file" id="file" ref="file" v-on:change="onImageChange()" multiple/>
        </a>
      </div>
      <div class="col-md-18">
        <ul style="margin-left: -113px; margin-bottom: 0px; max-height: 63px; overflow-x: hidden;">
          <li v-cloak v-for="(attachment, index) in attachments" style="float: left;">
            <span class="label label-primary btn btn-primary">{{attachment.name + ' (' + Number((attachment.size / 1024 / 1024).toFixed(1)) + 'MB)'}}</span>
            <span style="cursor: pointer;" @click="removeAttachment(attachment)"><i title="Xóa" class="fa fa-trash-o" style="vertical-align: middle; margin-left: 2px; margin-right: 5px; font-size: 18px;"></i></span>
          </li>
        </ul>

      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-1" style="white-space: nowrap">Nội dung</label>
      <div class="col-md-22" >
        <textarea class="form-control" rows="3" placeholder="" v-model="CustomerQuotationTrans.TransComment" ></textarea>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-4 col-sm-4 m-0">Loại khách hàng</label>
      <div class="col-md-2 col-sm-20">
        <i @click="AddCustomerCate()"
           class="fa fa-external-link" title="Loại khách hàng"
           style="font-size: 18px; cursor: pointer; padding-right: 5px;"></i>
      </div>
      <div class="col-md-18">
<!--        <span v-for="(field, key) in model.CustomerCate">{{field.CustomerCateName}}: {{field.Description}}, </span>-->
      </div>
    </div>
    <!-- Báo giá bán hàng (detail) -->
    <label>Giá trị báo giá bán hàng:</label>
    <div class="div-scroll-table">
    <table class="table b-table table-sm table-bordered table-editable" :style="[CustomerQuotationTrans.CcyID==2 ? {width: '2160px'} : {width: '1560px'}]">
      <thead>
      <tr>
        <th scope="col" style="width: 210px; border-bottom: none;" class="text-center">Hàng hóa, Dịch vụ</th>
        <th scope="col" style="width: 280px; border-bottom: none;" class="text-center">Diễn giải</th>
        <th scope="col" style="width: 115px; border-bottom: none;" class="text-center">Đơn vị tính</th>
        <th scope="col" style="width: 80px; border-bottom: none;" class="text-center">Số lượng</th>
        <th scope="col" style="width: 120px; border-bottom: none;" class="text-center">Đơn giá NT</th>
        <th scope="col" style="width: 120px; border-bottom: none;" class="text-center" v-if="CustomerQuotationTrans.CcyID==2">Đơn giá QĐ</th>
        <th scope="col" style="width: 120px; border-bottom: none;" class="text-center">Thành tiền NT</th>
        <th scope="col" style="width: 120px; border-bottom: none;" class="text-center" v-if="CustomerQuotationTrans.CcyID==2">Thành tiền QĐ</th>
        <th scope="col" style="width: 80px; border-bottom: none;" class="text-center">% Thuế</th>
        <th scope="col" style="width: 120px; border-bottom: none;" class="text-center">Tiền thuế NT</th>
        <th scope="col" style="width: 120px; border-bottom: none;" class="text-center" v-if="CustomerQuotationTrans.CcyID==2">Tiền thuế QĐ</th>
        <th scope="col" style="width: 80px; border-bottom: none;" class="text-center">% KM</th>
        <th scope="col" style="width: 120px; border-bottom: none;" class="text-center">Khuyến mại NT</th>
        <th scope="col" style="width: 120px; border-bottom: none;" class="text-center" v-if="CustomerQuotationTrans.CcyID==2">Khuyến mại QĐ</th>
        <th scope="col" style="width: 80px; border-bottom: none;" class="text-center">% GG</th>
        <th scope="col" style="width: 120px; border-bottom: none;" class="text-center">Giảm giá NT</th>
        <th scope="col" style="width: 120px; border-bottom: none;" class="text-center" v-if="CustomerQuotationTrans.CcyID==2">Giảm giá QĐ</th>

        <th scope="col" style="width: 30px; border-bottom: none; display: inline-table" >
          <!--<i @click="onAddFieldOnTable" class="fa fa-plus" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i>--></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(field, key) in CustomerQuotationTransItem" v-bind:RowItem="field.LineID">
        <td>
          <IjcoreModalListing v-model="CustomerQuotationTransItem" :title="'hàng hóa dịch vụ'" :api="'/listing/api/common/list'"
                              :table="'item'" :FieldID="'ItemID'" :FieldName="'ItemName'">
          </IjcoreModalListing>
        </td>
        <td>
          <b-form-input
            type="text"
            v-model="CustomerQuotationTransItem[key].Description"
            autocomplete="code list description">
          </b-form-input>
        </td>
        <td>
          <b-form-select v-model="CustomerQuotationTransItem[key].UomID" @change="onSelectUom($event, key)"
                         :options="uomOption" id="UomID"></b-form-select>
        </td>
        <td>
          <b-form-input
            type="text"
            v-model="CustomerQuotationTransItem[key].Quantity">
          </b-form-input>
        </td>
        <td>
<!--          <b-form-input-->
<!--            type="text"-->
<!--            v-model="CustomerQuotationTransItem[key].FCUnitPrice">-->
<!--          </b-form-input>-->
          <IjcoreNumber v-model="CustomerQuotationTransItem[key].FCUnitPrice" ></IjcoreNumber>
        </td>
        <td v-if="CustomerQuotationTrans.CcyID==2">
          <IjcoreNumber v-model="CustomerQuotationTransItem[key].LCUnitPrice"  v-if="CustomerQuotationTrans.CcyID==1"></IjcoreNumber>
          <input type="text" disabled="true" v-else class="form-control" placeholder="" v-model="CustomerQuotationTransItem[key].LCUnitPrice"/>
        </td>
        <td>
          <IjcoreNumber v-model="CustomerQuotationTransItem[key].FCAmount" ></IjcoreNumber>
        </td>
        <td v-if="CustomerQuotationTrans.CcyID==2">
<!--          <IjcoreNumber v-model="CustomerQuotationTransItem[key].LCAmount" ></IjcoreNumber>-->
          <input type="text" disabled="true" class="form-control" placeholder="" v-model="CustomerQuotationTransItem[key].LCAmount"/>
        </td>
        <td>
          <b-form-input
            type="text"
            v-model="CustomerQuotationTransItem[key].TaxRate">
          </b-form-input>
        </td>
        <td>
          <IjcoreNumber v-model="CustomerQuotationTransItem[key].FCTaxAmount" ></IjcoreNumber>
        </td>
        <td v-if="CustomerQuotationTrans.CcyID==2">
<!--          <IjcoreNumber v-model="CustomerQuotationTransItem[key].LCTaxAmount" ></IjcoreNumber>-->
          <input type="text" disabled="true" class="form-control" placeholder="" v-model="CustomerQuotationTransItem[key].LCTaxAmount"/>
        </td>
        <td>
          <b-form-input
            type="text"
            v-model="CustomerQuotationTransItem[key].PromotionPercent">
          </b-form-input>
        </td>
        <td>
          <IjcoreNumber v-model="CustomerQuotationTransItem[key].FCPromotionAmount" ></IjcoreNumber>
        </td>
        <td v-if="CustomerQuotationTrans.CcyID==2">
<!--          <IjcoreNumber v-model="CustomerQuotationTransItem[key].LCPromotionAmount" ></IjcoreNumber>-->
          <input type="text" disabled="true" class="form-control" placeholder="" v-model="CustomerQuotationTransItem[key].LCPromotionAmount"/>
        </td>
        <td>
          <b-form-input
            type="text"
            v-model="CustomerQuotationTransItem[key].DiscountPercent"
            autocomplete="Indicator">
          </b-form-input>
        </td>
        <td>
          <IjcoreNumber v-model="CustomerQuotationTransItem[key].FCDiscountAmount" ></IjcoreNumber>
        </td>
        <td v-if="CustomerQuotationTrans.CcyID==2">
<!--          <IjcoreNumber v-model="CustomerQuotationTransItem[key].LCDiscountAmount" ></IjcoreNumber>-->
          <input type="text" disabled="true" class="form-control" placeholder="" v-model="CustomerQuotationTransItem[key].LCDiscountAmount"/>
        </td>

        <td class="text-center" style="vertical-align: middle;text-align: right !important;padding-right: 8px;">
          <i @click="onDeleteFieldOnTable(key)" class="fa fa-trash-o" title="Xóa"
             style="font-size: 18px; cursor: pointer"></i>
        </td>
      </tr>
      </tbody>
    </table>
    </div>
    <a @click="onAddFieldOnTable(RowItem)" class="new-row">
      <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
    </a>
    <!-- Loại giao dịch kinh doanh -->
    <b-modal ref="CustomerCate" id="modal-form-input-task-general1" size="lg"
             title="Loại khách hàng">
<!--    <div style="margin-bottom: -12px;"><b>Loại giao dịch kinh doanh</b></div>-->
<!--    <div class="main-body main-body-view-action">-->
<!--      <table class="table b-table table-sm table-bordered table-editable">-->
<!--        <thead>-->
<!--        <tr>-->
<!--          <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Loại GDKD </th>-->
<!--          <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Giá trị</th>-->
<!--          <th scope="col" style="width: 2%; border-bottom: none;" class="text-center">-->
<!--            &lt;!&ndash;<i @click="onAddFieldOnTable" class="fa fa-plus" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i>&ndash;&gt;</th>-->
<!--        </tr>-->
<!--        </thead>-->
<!--        <tbody>-->
<!--        <tr v-for="(field, key) in value.CustomerQuotationTransCate">-->
<!--          <td>-->
<!--            <IjcoreModalListing v-model="value.CustomerQuotationTransCate[key]" :title="'loại GDKD'" :api="'/listing/api/common/list'"-->
<!--                                :table="'customer_quotation_trans_cate_list'" :FieldID="'CateID'" :FieldName="'CateName'">-->
<!--            </IjcoreModalListing>-->
<!--          </td>-->
<!--          <td>-->
<!--            <IjcoreModalListing v-model="value.CustomerQuotationTransCate[key]" :title="'giá trị'" :api="'/listing/api/common/list'"-->
<!--                                :table="'customer_quotation_trans_cate_value'" :FieldName="'CateValue'"-->
<!--                                :FieldWhere="{'CateID' : value.CustomerQuotationTransCate[key]}">-->
<!--            </IjcoreModalListing>-->
<!--          </td>-->
<!--          <td class="text-center">-->
<!--            <i @click="onDeleteFieldCustomerQuotationTransCate(key)" class="fa fa-trash-o" title="Xóa"-->
<!--               style="font-size: 18px; cursor: pointer"></i>-->
<!--          </td>-->
<!--        </tr>-->

<!--        </tbody>-->
<!--      </table>-->
<!--      <a @click="onAddFieldCustomerQuotationTransCate()" class="new-row">-->
<!--        <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới-->
<!--      </a>-->
<!--    </div>-->
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="SaveCustomerCate()">
            Lưu
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="HideCustomerCate()">
            Đóng
          </b-button>
        </div>
      </template>
    </b-modal>
  </div>

      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdate()">
            Lưu
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="" >
            Hủy
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModal()">
            Đóng
          </b-button>
        </div>
      </template>
    </b-modal>
  </a>
</template>

<script>
  import ApiService from '@/services/api.service';
  import IjcoreNumber from "../../../../components/IjcoreNumber";
  import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
  import IjcoreDateTimePicker from '@/components/IjcoreDateTimePicker';
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import IjcoreModalMultiListing from "../../../../components/IjcoreModalMultiListing";

  const dataCcyOption = {
    1: 'VNĐ',
    2: 'USD'
  };

  export default {
    name: 'CustomerQuotationTransForm',
    components: {
      IjcoreNumber,
      IjcoreDatePicker,
      IjcoreDateTimePicker,
      IjcoreModalListing,
      IjcoreModalMultiListing,
    },
    computed: {
      rows() {
        return this.totalRows
      },
      dataCcyOption() {
        return dataCcyOption;
      }
    },
    data () {
      return {
        isForm: false,
        idParams: this.$route.params.id,
        listtable: [],
        RowItem: 0,
        tableName: '',
        search:'',
        lenghNo: 0,
        object: {
          master: {},
          detail: [],
        },
        CustomerQuotationTrans: {
          CustomerID: '',
          TransDate: '',
          TransNo: '',
          TransComment: '',
          CcyID: '1',
          CcyNo: '',
          ExchangeRate: '',
          CompanyID: '',
          CompanyName: '',
          Address: '',
          TaxCode: '',
          Tel: '',
          Fax: '',
          CustomerName: '',
          CustomerAddress: '',
          CustomerTaxCode: '',
          CustomerBankAccount: '',
          CustomerBankName: '',
          CustomerTel: '',
          CustomerFax: '',
          CustomerEmail: '',
          CustomerBuyer: '',
          FileID: '',
          FileName: '',
          StatusID: '',
          StatusDescription: '',
          Locked: '',
          LockedDate: '',
          LockedUserID: '',
        },
        //CustomerQuotationTransCate: {},
        CustomerQuotationTransItem: [],
        uomOption: [],
        attachments: [],
        attachment_labels: [],
        categories: [],
        data: new FormData(),
        percentCompleted: 0,
      }
    },
    created() {
    },
    mounted() {
      this.fetchData();
    },
    methods: {
      fetchData(){
        let self = this;
        let requestData = {
          method: 'get',
          data: {}
        };
        requestData.url = '/customer/api/customer/get-allquotation';
        this.$store.commit('isLoading', true);

        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          self.uomOption = [];
          _.forEach(responsesData.data.Uom, function (value, key) {
            let tmpObj = {};
            tmpObj.value = value.UomID;
            tmpObj.text = value.UomName;
            self.uomOption.push(tmpObj);
          });

          self.CustomerQuotationTrans.TransNo = responsesData.data.auto;

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
      onAddFieldCustomerQuotationTransCate() {
        let fieldObj = {};
        fieldObj.CateID = '';
        fieldObj.CateValue = null;
        if(this.value.CustomerQuotationTransCate == undefined){
          this.value.CustomerQuotationTransCate = [];
        }
        this.value.CustomerQuotationTransCate.push(fieldObj);
        this.$forceUpdate();
      },
      onDeleteFieldCustomerQuotationTransCate(key) {
        this.value.CustomerQuotationTransCate.splice(key, 1);
        //this.setStyleAction();
        this.$forceUpdate();
      },
      AddCustomerQuotationTransCate(TransID,key) {
        this.TransItemIDCurrent = TransID;
        this.TransItemKeyCurrent = key;
        this.$forceUpdate();
        this.$refs['CustomerQuotationTransCate'].show();
      },
      HideCustomerQuotationTransCate() {
        //this.isForm = false;
        this.$refs['CustomerQuotationTransCate'].hide();
      },
      SaveCustomerQuotationTransCate() {
        this.$bvToast.toast('Đã lưu loại khách hàng\n', {
          title: 'Thông báo',
          variant: 'success',
          solid: true
        });
        this.$refs['CustomerQuotationTransCate'].hide();
      },
      onSaveModal(){
        this.$bvToast.toast('Đã lưu ràng buộc', {
          variant: 'success',
          solid: true
        });
      },
      onCancelModal(e){
        this.onResetModal();
        e.preventDefault();
      },
      onCancelModal(e){
        this.onResetModal();
        e.preventDefault();
      },
      onHideModal(){
        this.$refs['modal'].hide();
        this.isForm = false;
      },
      onToggleModal(){
        let self = this;
        this.currentPage = 1;
        if(!this.addnew){
          this.CustomerQuotationTrans.TransDate = this.value[this.keyArray].TransDate;
          this.CustomerQuotationTrans.ExpectedEndDate = this.value[this.keyArray].ExpectedEndDate;
          this.CustomerQuotationTrans.StatusDescription = this.value[this.keyArray].StatusDescription;
          this.CustomerQuotationTrans.EmployeeID = this.value[this.keyArray].EmployeeID;
          this.CustomerQuotationTrans.EmployeeName = this.value[this.keyArray].EmployeeName;
          this.CustomerQuotationTrans.CustomerID = this.value[this.keyArray].CustomerID;
          this.CustomerQuotationTrans.CustomerName = this.value[this.keyArray].CustomerName;
          this.CustomerQuotationTrans.ItemID = this.value[this.keyArray].ItemID;
          this.CustomerQuotationTrans.ItemName = this.value[this.keyArray].ItemName;
          this.CustomerQuotationTrans.Time = this.value[this.keyArray].Time;
          this.CustomerQuotationTrans.FCAmount = this.value[this.keyArray].FCAmount;
          this.CustomerQuotationTrans.PercentSuccess = this.value[this.keyArray].PercentSuccess;
          this.CustomerQuotationTrans.FileID = this.value[this.keyArray].FileID;
          this.CustomerQuotationTrans.FileName = this.value[this.keyArray].FileName;
          this.CustomerQuotationTrans.TransComment = this.value[this.keyArray].TransComment;

          self.TransID = this.value[this.keyArray].TransID;
          self.value.CustomerQuotationTransCate = [];
          _.forEach(self.CustomerQuotationTransCate, function (field, key) {
            let tmpObj = {};
            if (self.CustomerQuotationTransCate[key].TransID == self.TransID) {
              tmpObj.CateID= field.CateID;
              tmpObj.CateName= field.CateName;
              tmpObj.CateValue= field.CateValue;
              self.value.CustomerQuotationTransCate.push(tmpObj)
            }
          });

        }else{
          this.CustomerQuotationTrans.TransDate = __.convertDateTimeToString(new Date());
          this.CustomerQuotationTrans.ExpectedEndDate = __.convertDateTimeToString(new Date());
          if (this.CustomerQuotationTransItem == undefined) {
            this.CustomerQuotationTransItem = [];
          }
        }


        this.$refs['modal'].show();
        this.fetchData();
      },
      onSelectUom(value, key) {
        let uom = _.find(this.model.uomOption, ['value', value]);
      },
      onResetModal(){
      },
      onHideModalDataflow(){
        if (this.isDataflow) {
          this.$emit('onHideModalTask');
        }
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
      deleteLine(key) {
        this.value.splice(key, 1);
      },
      onUpdate(){
        let self = this;
        let CustomerQuotationTransData = self.CustomerQuotationTrans;
        let requestData = {
          method: 'post',
          url: 'customer/api/customer/customer-quotation/' + this.idParams,
          data: {
            CustomerQuotationTrans: CustomerQuotationTransData,
            CustomerQuotationTransCate: self.value.CustomerQuotationTransCate
          }
        };
        // edit user
        requestData.data.ItemID = self.Customer.CustomerID;
        //alert(requestData.data.ItemID);
        ApiService.setHeader();
        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data; //console.log(responses.data);
          if (responsesData.status === 1) {
            this.$bvToast.toast('Cập nhật thành công!', {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });
            if(this.addnew){
              //responsesData.data.CustomerQuotationTrans.TransDate = __.convertDateTimeToString(responsesData.data.CustomerQuotationTrans.TransDate);
              //responsesData.data.CustomerQuotationTrans.ExpectedEndDate = __.convertDateTimeToString(responsesData.data.CustomerQuotationTrans.ExpectedEndDate);
              //this.value.push(responsesData.data.CustomerQuotationTrans);

            }else{ //this.currentUser = JSON.parse(localStorage.getItem('Employee')); alert (this.currentUser.CompanyID);
              this.value[this.keyArray].TransID = this.CustomerQuotationTrans.TransID;
              this.value[this.keyArray].TransDate = this.CustomerQuotationTrans.TransDate;
              this.value[this.keyArray].TransComment = this.CustomerQuotationTrans.TransComment;
              this.value[this.keyArray].EmployeeID = this.CustomerQuotationTrans.EmployeeID;
              this.value[this.keyArray].EmployeeName = this.CustomerQuotationTrans.EmployeeName;
              this.value[this.keyArray].CustomerID = this.CustomerQuotationTrans.CustomerID;
              this.value[this.keyArray].CustomerName = this.CustomerQuotationTrans.CustomerName;
              this.value[this.keyArray].ContactName = this.CustomerQuotationTrans.ContactName;
              this.value[this.keyArray].CustomerInfo = this.CustomerQuotationTrans.CustomerInfo;
              this.value[this.keyArray].Time = this.CustomerQuotationTrans.Time;
              this.value[this.keyArray].FileID = this.CustomerQuotationTrans.FileID;
              this.value[this.keyArray].FileName = this.CustomerQuotationTrans.FileName;
              this.value[this.keyArray].ItemID = this.CustomerQuotationTrans.ItemID;
              this.value[this.keyArray].ItemName = this.CustomerQuotationTrans.ItemName;
              this.value[this.keyArray].FCAmount = this.CustomerQuotationTrans.FCAmount;
              this.value[this.keyArray].ExpectedEndDate = this.CustomerQuotationTrans.ExpectedEndDate;
              this.value[this.keyArray].PercentSuccess = this.CustomerQuotationTrans.PercentSuccess;
              this.value[this.keyArray].StatusDescription = this.CustomerQuotationTrans.StatusDescription;
              this.value[this.keyArray].addnew = false;
            }
            self.CustomerQuotationTransCate = responsesData.data.CustomerQuotationTransCate;
            self.CustomerQuotationTransItem = responsesData.data.CustomerQuotationTransItem;
            this.isForm = false;
            this.$refs['modal'].hide();
            self.$store.commit('isLoading', false);
            //self.$_storeTaskNotice(self.Customer.CustomerID, 'execute');
          } else {
            let htmlErrors = __.renderErrorApiHtml(responsesData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            );
            self.$store.commit('isLoading', false);
          }

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

      updateprice(key) {
        let CcyID = this.CustomerQuotationTrans.CcyID;
        let ExchangeRate = this.CustomerQuotationTrans.ExchangeRate;
        let FCUnitPrice = this.CustomerQuotationTransItem[key].FCUnitPrice;
        let FCAmount = this.CustomerQuotationTransItem[key].FCAmount;
        let FCTaxAmount = this.CustomerQuotationTransItem[key].FCTaxAmount;
        let FCPromotionAmount = this.CustomerQuotationTransItem[key].FCPromotionAmount;
        let FCDiscountAmount = this.CustomerQuotationTransItem[key].FCDiscountAmount;
        if(CcyID==2){
          this.CustomerQuotationTransItem[key].LCUnitPrice = ExchangeRate * FCUnitPrice;
          this.CustomerQuotationTransItem[key].LCAmount = ExchangeRate * FCAmount;
          this.CustomerQuotationTransItem[key].LCTaxAmount = ExchangeRate * FCTaxAmount;
          this.CustomerQuotationTransItem[key].LCPromotionAmount = ExchangeRate * FCPromotionAmount;
          this.CustomerQuotationTransItem[key].LCDiscountAmount = ExchangeRate * FCDiscountAmount;
        }
      },
      getAttachmentSize() {
        this.upload_size = 0; // Reset to beginningƒ
        this.attachments.map((item) => { this.upload_size += parseInt(item.size); });
        this.upload_size = Number((this.upload_size).toFixed(1));
        this.$forceUpdate();
      },
      removeAttachment(attachment) {
        this.attachments.splice(this.attachments.indexOf(attachment), 1);
        this.getAttachmentSize();
      },
      uploadFieldChange(e) {
        var files = e.target.files || e.dataTransfer.files;
        if (!files.length)
          return;
        for (var i = files.length - 1; i >= 0; i--) {
          this.attachments.push(files[i]);
        }
        // Reset the form to avoid copying these files multiple times into this.attachments
        document.getElementById("attachments").value = [];
      },
      onImageChange(e) {
        let files = e.target.files || e.dataTransfer.files;
        if (!files.length)
          return;
        this.createImage(files[0]);
        this.$emit('changed', files);
      },
      createImage(file) {
        let reader = new FileReader();
        let vm = this;
        reader.onload = (e) => {
          vm.image = e.target.result;
        };
        reader.readAsDataURL(file);
      },
      onAddFieldOnTable() {
        let fieldObj = {};
        fieldObj.TransID = '';
        fieldObj.ItemID = '';
        fieldObj.ItemName = null;
        fieldObj.Description = null;
        fieldObj.UomID = null;
        fieldObj.UomName = '';
        fieldObj.Quantity = null;
        fieldObj.FCUnitPrice = null;
        fieldObj.LCUnitPrice = null;
        fieldObj.FCAmount = null;
        fieldObj.LCAmount = null;
        fieldObj.TaxRate = null;
        fieldObj.FCTaxAmount = null;
        fieldObj.LCTaxAmount = null;
        fieldObj.PromotionPercent = null;
        fieldObj.FCPromotionAmount = null;
        fieldObj.LCPromotionAmount = null;
        fieldObj.DiscountPercent = null;
        fieldObj.FCDiscountAmount = null;
        fieldObj.LCDiscountAmount = null;
        this.CustomerQuotationTransItem.push(fieldObj);
        this.$forceUpdate();
      },
      onDeleteFieldOnTable(key) {
        this.CustomerQuotationTransItem.splice(key, 1)
        this.setStyleAction();
        this.$forceUpdate();
      },
      onAddFieldCustomerQuotationTransCate(RowItem) {
        let fieldObj = {};
        fieldObj.CustomerID = RowItem;
        fieldObj.CustomerCateID = '';
        fieldObj.CustomerCateValue = null;
        this.RowItem = RowItem + 1;
        this.model.CustomerCate.push(fieldObj);
        this.$forceUpdate();
      },
      onDeleteFieldCustomerQuotationTransCate(key) {
        this.model.CustomerCate.splice(key, 1);
        //
        this.setStyleAction();
        this.$forceUpdate();
      },
      AddCustomerCate() {
        this.$forceUpdate();
        this.$refs['CustomerCate'].show();
      },
      HideCustomerCate() {
        this.isForm = false;
        this.$refs['CustomerCate'].hide();
      },
      SaveCustomerCate() {
        this.$bvToast.toast('Đã lưu loại khách hàng\n', {
          title: 'Thông báo',
          variant: 'success',
          solid: true
        });
        this.$refs['CustomerCate'].hide();
      },

    },
    watch: {
      idParams() {
        this.fetchData();
      }
    },
    filters: {

    },
    props: {
      isDetail: true,
      value: {},
      title: {},
      name: {},
      api: {},
      table: {},
      Customer: {},
      StatusOption: {},
      CustomerQuotationTransCate: {},
      //CustomerQuotationTransItem: {},
      StyleIcon: {},
      keyArray: {},
      addnew: false,
    },

  }
</script>
<style>
  .mr-bottom-3{
    margin-bottom: 3px !important;
  }
  .readonly{
    background-color: #fff !important;
  }
  .table th, .table td{
    border-bottom: 1px solid #c8ced3;
  }
  .modal-footer ol,.modal-footer ul,.modal-footer dl{
    margin-bottom: 0px;
  }
  #modal-form-input-task-general-content .modal-lg .modal-content{
    width: 1024px;
    margin: auto;
  }
  @media (max-width: 1024px){
    #modal-form-input-task-general-content .modal-lg {
      max-width: 100%;
    }
    #modal-form-input-task-general-content .modal-lg .modal-content{
      width: 90%;
      margin: auto;
    }
  }
  @media (min-width: 992px){
    #modal-form-input-task-general-content .modal-lg {
      max-width: 100%;
    }
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
  table.table tr th {white-space: normal !important; vertical-align: middle !important;}
</style>
