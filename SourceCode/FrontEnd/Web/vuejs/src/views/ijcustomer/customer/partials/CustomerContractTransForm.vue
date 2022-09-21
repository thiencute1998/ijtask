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
      <label class="col-md-2" style="white-space: nowrap">Ngày</label>
      <div class="col-md-6 DateTimeText" >
        <IjcoreDateTimePicker v-model="CustomerContractTrans.TransDate" :allowEmptyClear="true" style="width: 250px">
        </IjcoreDateTimePicker>
      </div>
      <label class="col-md-1" style="white-space: nowrap">Tiền tệ </label>
      <div class="col-md-2">
        <IjcoreModalListing v-model="CustomerContractTrans" :title="'tiền tệ'" :api="'/listing/api/common/list'"
                            :table="'ccy'" :FieldID="'CcyID'" :FieldName="'CcyName'">
        </IjcoreModalListing>
      </div>
      <label class="col-md-1" style="white-space: nowrap">Tỷ giá</label>
      <div class="col-md-6">
        <IjcoreNumber v-model="CustomerContractTrans.ExchangeRate" ></IjcoreNumber>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-2" style="white-space: nowrap">Khách hàng</label>
      <div class="col-md-20">
        <IjcoreModalListing v-model="CustomerContractTrans" :title="'khách hàng'" :api="'/listing/api/common/list'"
                            :table="'customer'" :FieldID="'CustomerID'" :FieldName="'CustomerName'"
                            :FieldUpdate="['CustomerName', 'ContactName', 'DepartmentName', 'OfficePhone', 'HandPhone', 'Email']">
        </IjcoreModalListing>
      </div>
<!--      <label class="col-md-1" style="white-space: nowrap">Người liên hệ</label>-->
<!--      <div class="col-md-6">-->
<!--        <IjcoreModalListing v-model="CustomerContractTrans" :title="'người liên hệ'" :api="'/listing/api/common/list'"-->
<!--                            :table="'Customer_Contact'" :FieldID="'LineID'" :FieldName="'ContactName'"-->
<!--                            :FieldUpdate="['PositionName']"-->
<!--                            :FieldWhere="{'CustomerID' : CustomerContractTrans.CustomerID}">-->
<!--        </IjcoreModalListing>-->
<!--      </div>-->

    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-2" style="white-space: nowrap">Hợp đồng</label>
      <div class="col-md-20" >
        <IjcoreModalListing v-model="CustomerContractTrans" :title="'hợp đồng'" :api="'/listing/api/common/list'"
                            :table="'contract'" :FieldID="'ContractID'" :FieldName="'ContractName'"
                            :FieldUpdate="['ContractName', 'ContractNo', 'ContractDate', 'ContractAmount']">
        </IjcoreModalListing>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-2" style="white-space: nowrap">Nội dung</label>
      <div class="col-md-20" >
        <textarea class="form-control" rows="3" placeholder="" v-model="CustomerContractTrans.TransComment" ></textarea>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-2" style="white-space: nowrap">Giá trị nguyên tệ</label>
      <div class="col-md-2">
        <IjcoreNumber v-model="CustomerContractTrans.FCAmount"></IjcoreNumber>
      </div>
      <label class="col-md-2" style="white-space: nowrap">Giá trị qui đổi</label>
      <div class="col-md-2">
        <IjcoreNumber v-model="CustomerContractTrans.LCAmount" ></IjcoreNumber>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-2" style="white-space: nowrap">Số hóa đơn</label>
      <div class="col-md-2">
        <input v-model="CustomerContractTrans.InvoiceNo" class="form-control"/>
      </div>
      <label class="col-md-2" style="white-space: nowrap">Ngày hóa đơn</label>
      <div class="col-md-4 DateText" >
        <IjcoreDatePicker v-model="CustomerContractTrans.InvoiceDate" style="width: 160px">
        </IjcoreDatePicker>
      </div>

    </div>
    <div class="form-group row">
      <label class="col-md-2"  style="white-space: nowrap">Loại giao dịch hợp đồng</label>
      <div class="col-md-2 col-sm-20">
        <i @click="AddCustomerContractTransCate()"
           class="fa fa-external-link" title="Loại giao dịch Hợp đồng bán hàng
"
           style="font-size: 18px; cursor: pointer; padding-right: 5px; padding-left: 12px;"></i>
      </div>
    </div>
    <b-collapse class="mt-2" v-model="showCustomerTransCate">
      <div class="form-group row">
        <label class="col-md-2" style="white-space: nowrap"></label>
        <div class="col-md-20">
          <p v-for="(field, key) in value.CustomerContractTransCate" style="margin-bottom: -2px;"><span v-if="field.CateName">{{field.CateName}}: {{field.CateValue}}<br></span></p>
        </div>
      </div>
    </b-collapse>
    <div class="form-group row">
      <label class="col-md-2"  style="white-space: nowrap">Tệp đính kèm</label>
      <div class="col-md-2 col-sm-20">
        <IjcoreUploadMultipleFile
        v-on:changed="changeFile" :isIcon="true"></IjcoreUploadMultipleFile>
      </div>
    </div>
    <b-collapse class="mt-2" v-model="showCustomerFile">
      <div class="form-group row">
        <label class="col-md-2" style="white-space: nowrap"></label>
        <div class="col-md-20 m-0">
          <CustomerFileContent v-model="CustomerFile" :Customer="Customer" :per="CustomerPerFile">
          </CustomerFileContent>
        </div>
      </div>
    </b-collapse>

    <div class="form-group row align-items-center">
      <div class="col-lg-2">Loại trạng thái</div>
      <div class="col-lg-2">
        <b-form-select v-model="CustomerContractTrans.StatusID" :options="StatusOption" @change="onChangeStatus"></b-form-select>
      </div>
      <div class="col-lg-2">Trạng thái</div>
      <div class="col-lg-2">
        <b-form-select v-model="CustomerContractTrans.StatusValue" :options="StatusValueOption | filterStatusValueOption(Number(CustomerContractTrans.StatusID))"></b-form-select>
      </div>
    </div>

    <!-- Loại giao dịch kinh doanh -->
    <b-modal ref="CustomerContractTransCate" id="modal-form-input-task-general1" size="lg"
             title="Loại giao dịch Hợp đồng bán hàng
">
<!--    <div style="margin-bottom: -12px;"><b>Loại giao dịch bán hàng</b></div>-->
    <div class="main-body main-body-view-action">
      <table class="table b-table table-sm table-bordered table-editable">
        <thead>
        <tr>
          <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Loại giao dịch Hợp đồng bán hàng</th>
          <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Giá trị</th>
          <th scope="col" style="width: 2%; border-bottom: none;" class="text-center">
            <!--<i @click="onAddFieldOnTable" class="fa fa-plus" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i>--></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(field, key) in value.CustomerContractTransCate">
          <td>
            <IjcoreModalListing v-model="value.CustomerContractTransCate[key]" :title="'loại giao dịch Hợp đồng bán hàng'" :api="'/listing/api/common/list'"
                                :table="'customer_contract_trans_cate_list'" :FieldID="'CateID'" :FieldName="'CateName'" @changed="changeCustomerCateValue()">
            </IjcoreModalListing>
          </td>
          <td>
            <IjcoreModalListing v-model="value.CustomerContractTransCate[key]" :title="'giá trị'" :api="'/listing/api/common/list'"
                                :table="'customer_contract_trans_cate_value'" :FieldName="'CateValue'"
                                :FieldWhere="{'CateID' : value.CustomerContractTransCate[key]}" @changed="changeCustomerCateValue()">
            </IjcoreModalListing>
          </td>
          <td class="text-center">
            <i @click="onDeleteFieldCustomerContractTransCate(key)" class="fa fa-trash-o" title="Xóa"
               style="font-size: 18px; cursor: pointer"></i>
          </td>
        </tr>

        </tbody>
      </table>
      <a @click="onAddFieldCustomerContractTransCate()" class="new-row">
        <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
      </a>
    </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="SaveCustomerContractTransCate()">
            Lưu
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="HideCustomerContractTransCate()">
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
  import IjcoreUploadMultipleFile from "../../../../components/IjcoreUploadMultipleFile";
  import CustomerFileContent from "./CustomerFileContent";

  export default {
    name: 'CustomerContractTransForm',
    components: {
      IjcoreNumber,
      IjcoreDatePicker,
      IjcoreDateTimePicker,
      IjcoreModalListing,
      IjcoreModalMultiListing,
      IjcoreUploadMultipleFile,
      CustomerFileContent,
    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data () {
      return {
        isForm: false,
        showCustomerFile: false,
        showCustomerTransCate: false,
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
        CustomerContractTrans: {
          CustomerID: '',
          TransDate: '',
          TransComment: '',
          CustomerName: '',
          ContractID: '',
          ContractName: '',
          CustomerInfo: '',
          ContractInfo: '',
          Time: '',
          FileID: '',
          FileName: '',
          ItemID: '',
          ItemName: '',
          CcyID: '',
          CcyNo: '',
          CcyName: '',
          ExchangeRate: '',
          FCAmount: '',
          LCAmount: '',
          InvoiceDate: '',
          InvoiceNo: '',
          ExpectedEndDate: '',
          PercentSuccess: '',
          //StatusID: '',
          StatusID: null,
          StatusDescription: '',
          CreatedDate: '',
          CreatedUserID: '',
          UpdatedDate: '',
          UpdatedUserID: '',
          Locked: '',
          LockedDate: '',
          LockedUserID: '',
        },
        //CustomerContractTransCate: {},
        attachments: [],
        attachment_labels: [],
        categories: [],
        data: new FormData(),
        percentCompleted: 0,
        CustomerFile: [],
        CustomerPerFile: [],
      }
    },
    created() {
    },
    mounted() {
      this.fetchData();
    },
    methods: {
      changeCustomerCateValue(){
        this.$forceUpdate();
      },
      fetchData(){

      },
      onAddFieldCustomerContractTransCate() {
        let fieldObj = {};
        fieldObj.CateID = '';
        fieldObj.CateValue = null;
        if(this.value.CustomerContractTransCate == undefined){
          this.value.CustomerContractTransCate = [];
        }
        this.value.CustomerContractTransCate.push(fieldObj);
        this.$forceUpdate();
      },
      onDeleteFieldCustomerContractTransCate(key) {
        this.value.CustomerContractTransCate.splice(key, 1);
        //this.setStyleAction();
        this.$forceUpdate();
      },
      AddCustomerContractTransCate(TransID,key) {
        this.TransItemIDCurrent = TransID;
        this.TransItemKeyCurrent = key;
        this.$forceUpdate();
        this.$refs['CustomerContractTransCate'].show();
      },
      HideCustomerContractTransCate() {
        //this.isForm = false;
        this.$refs['CustomerContractTransCate'].hide();
      },
      SaveCustomerContractTransCate() {
        this.showCustomerTransCate = true;
        this.$bvToast.toast('Đã lưu loại khách hàng\n', {
          title: 'Thông báo',
          variant: 'success',
          solid: true
        });
        this.$refs['CustomerContractTransCate'].hide();
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
      onHideModal(){
        this.$refs['modal'].hide();
        this.isForm = false;
      },
      onToggleModal(){
        let self = this;
        this.currentPage = 1;
        if(!this.addnew){
          //this.CustomerContractTrans.TransDate = __.convertDateTimeToString(this.value[this.keyArray].TransDate);
          //this.CustomerContractTrans.ExpectedEndDate = __.convertDateTimeToString(this.value[this.keyArray].ExpectedEndDate);
          //this.CustomerContractTrans.InvoiceDate = __.convertDateToString(this.value[this.keyArray].InvoiceDate);
          this.CustomerContractTrans.TransDate = this.value[this.keyArray].TransDate;
          this.CustomerContractTrans.InvoiceDate = this.value[this.keyArray].InvoiceDate;
          this.CustomerContractTrans.StatusID = this.value[this.keyArray].StatusID;
          this.CustomerContractTrans.StatusName = this.value[this.keyArray].StatusName;
          this.CustomerContractTrans.StatusValue = this.value[this.keyArray].StatusValue;
          this.CustomerContractTrans.StatusDescription = this.value[this.keyArray].StatusDescription;
          this.CustomerContractTrans.CustomerID = this.value[this.keyArray].CustomerID;
          this.CustomerContractTrans.CustomerName = this.value[this.keyArray].CustomerName;
          this.CustomerContractTrans.ContractID = this.value[this.keyArray].ContractID;
          this.CustomerContractTrans.ContractName = this.value[this.keyArray].ContractName;
          this.CustomerContractTrans.LineID = this.value[this.keyArray].ContactID;
          this.CustomerContractTrans.ItemID = this.value[this.keyArray].ItemID;
          this.CustomerContractTrans.ItemName = this.value[this.keyArray].ItemName;
          this.CustomerContractTrans.Time = this.value[this.keyArray].Time;
          this.CustomerContractTrans.FCAmount = this.value[this.keyArray].FCAmount;
          this.CustomerContractTrans.InvoiceNo = this.value[this.keyArray].InvoiceNo;
          this.CustomerContractTrans.PercentSuccess = this.value[this.keyArray].PercentSuccess;
          this.CustomerContractTrans.FileID = this.value[this.keyArray].FileID;
          this.CustomerContractTrans.FileName = this.value[this.keyArray].FileName;
          this.CustomerContractTrans.TransComment = this.value[this.keyArray].TransComment;
          this.CustomerContractTrans.ExchangeRate = this.value[this.keyArray].ExchangeRate;
          this.CustomerContractTrans.CcyID = this.value[this.keyArray].CcyID;
          this.CustomerContractTrans.CcyName = this.value[this.keyArray].CcyName;

          self.TransID = this.value[this.keyArray].TransID;
          // self.value.CustomerContractTransCate = [];
          if (!self.value.CustomerContractTransCate) {
            self.$set(self.value, 'CustomerContractTransCate', []);
            _.forEach(self.CustomerContractTransCate, function (field, key) {
              let tmpObj = {};
              if (self.CustomerContractTransCate[key].TransID == self.TransID) {
                tmpObj.CateID= field.CateID;
                tmpObj.CateName= field.CateName;
                tmpObj.CateValue= field.CateValue;
                self.$set(self.value.CustomerContractTransCate, self.value.CustomerContractTransCate.length, tmpObj);
                // self.value.CustomerContractTransCate.push(tmpObj)
              }
            });
          }

          //View Upload file
          let urlApi = '';
          let requestData = {
            method: 'get',
          };
          this.showCustomerFile = true;
          this.TransID = this.value[this.keyArray].TransID;
          if(this.TransID){
            urlApi = '/customer/api/customer/select-all-file-transid/' + this.TransID;
            let data = {
              id: this.TransID
            };
            requestData.data = data;
          }
          requestData.url = urlApi;
          this.$store.commit('isLoading', true);
          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;
            if (responsesData.status === 1) {
                self.CustomerFile = responsesData.data;
            }
          });
          this.showCustomerTransCate = true;
          self.$store.commit('isLoading', false);
        }else{
          this.CustomerContractTrans.TransDate = __.convertDateTimeToString(new Date());
          this.CustomerContractTrans.ExpectedEndDate = __.convertDateTimeToString(new Date());

          this.CustomerContractTrans.StatusDescription = '';
          this.CustomerContractTrans.InvoiceDate = '';
          this.CustomerContractTrans.InvoiceNo = '';
          this.CustomerContractTrans.CustomerID = '';
          this.CustomerContractTrans.CustomerName = '';
          this.CustomerContractTrans.ContractID = '';
          this.CustomerContractTrans.ContractName = '';
          this.CustomerContractTrans.CcyID = '';
          this.CustomerContractTrans.CcyName = '';
          this.CustomerContractTrans.ItemID = '';
          this.CustomerContractTrans.ItemName = '';
          this.CustomerContractTrans.Time = '';
          this.CustomerContractTrans.ExchangeRate = '';
          this.CustomerContractTrans.FCAmount = '';
          this.CustomerContractTrans.LCAmount = '';
          this.CustomerContractTrans.InvoiceDate = '';
          this.CustomerContractTrans.InvoiceNo = '';
          this.CustomerContractTrans.PercentSuccess = '';
          this.CustomerContractTrans.FileID = '';
          this.CustomerContractTrans.FileName = '';
          this.CustomerContractTrans.TransComment = '';
          this.CustomerContractTrans.StatusID = 1 ;
          this.CustomerContractTrans.StatusValue = 1 ;
          this.value.CustomerContractTransCate = [];
          this.CustomerFile = [];

        }

        this.$refs['modal'].show();
        this.fetchData();
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
        let CustomerContractTransData = self.CustomerContractTrans;
        CustomerContractTransData.ContactID = self.CustomerContractTrans.LineID;
        let statusValue = _.find(this.StatusValueOption, {
          StatusID: this.CustomerContractTrans.StatusID,
          value: this.CustomerContractTrans.StatusValue
        });
        this.CustomerContractTrans.StatusDescription = (statusValue) ? statusValue.text : '';
        let requestData = {
          method: 'post',
          url: 'customer/api/customer/customer-contract/' + this.idParams,
          data: {
            CustomerContractTrans: CustomerContractTransData,
            CustomerContractTransCate: self.value.CustomerContractTransCate
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
            let status = _.find(this.StatusOption, ['value', Number(this.CustomerContractTrans.StatusID)]);
            let statusValue = _.find(this.StatusValueOption, {
              StatusID: this.CustomerContractTrans.StatusID,
              value: this.CustomerContractTrans.StatusValue
            });
            if(this.addnew){
              responsesData.data.CustomerContractTrans.StatusName = (status) ? status.text : '';
              responsesData.data.CustomerContractTrans.StatusDescription = (statusValue) ? statusValue.text : '';
              //responsesData.data.CustomerContractTrans.TransDate = __.convertDateTimeToString(responsesData.data.CustomerContractTrans.TransDate);
              //responsesData.data.CustomerContractTrans.ExpectedEndDate = __.convertDateTimeToString(responsesData.data.CustomerContractTrans.ExpectedEndDate);
              this.value.push(responsesData.data.CustomerContractTrans);

              if (!status) {
                this.$bvToast.toast('Loại trạng thái không được để trống', {
                  title: 'Thông báo',
                  variant: 'danger',
                  solid: true
                });
                return;
              }
              this.$forceUpdate();
            }else{
              if(!this.CustomerContractTrans.PositionName){ this.CustomerContractTrans.PositionName='';}
              if(!this.CustomerContractTrans.OfficePhone){ this.CustomerContractTrans.OfficePhone='';}
              this.CustomerInfo = 'Tên khách hàng: '+this.CustomerContractTrans.CustomerName+', '+'Người liên hệ: '+this.CustomerContractTrans.ContactName+', '+'Chức vụ: '+this.CustomerContractTrans.PositionName+', '+'Điện thoại: '+this.CustomerContractTrans.OfficePhone;
              this.value[this.keyArray].TransID = this.CustomerContractTrans.TransID;
              this.value[this.keyArray].TransDate = this.CustomerContractTrans.TransDate;
              this.value[this.keyArray].TransComment = this.CustomerContractTrans.TransComment;
              this.value[this.keyArray].InvoiceDate = this.CustomerContractTrans.InvoiceDate;
              this.value[this.keyArray].InvoiceNo = this.CustomerContractTrans.InvoiceNo;
              this.value[this.keyArray].CustomerID = this.CustomerContractTrans.CustomerID;
              this.value[this.keyArray].CustomerName = this.CustomerContractTrans.CustomerName;
              this.value[this.keyArray].LineID = this.CustomerContractTrans.ContactID;
              this.value[this.keyArray].ContactName = this.CustomerContractTrans.ContactName;
              this.value[this.keyArray].PositionName = this.CustomerContractTrans.PositionName;
              this.value[this.keyArray].OfficePhone = this.CustomerContractTrans.OfficePhone;
              this.value[this.keyArray].CustomerInfo = this.CustomerInfo;
              this.value[this.keyArray].ContractName = this.CustomerContractTrans.ContractName;
              this.value[this.keyArray].ContractNo = this.CustomerContractTrans.ContractNo;
              this.value[this.keyArray].ContactID = this.CustomerContractTrans.ContactID;
              this.value[this.keyArray].ContactName = this.CustomerContractTrans.ContactName;
              this.value[this.keyArray].ContractDate = this.CustomerContractTrans.ContractDate;
              this.value[this.keyArray].CcyID = this.CustomerContractTrans.CcyID;
              this.value[this.keyArray].CcyName = this.CustomerContractTrans.CcyName;
              this.value[this.keyArray].ContractAmount = this.CustomerContractTrans.ContractAmount;
              this.value[this.keyArray].Time = this.CustomerContractTrans.Time;
              this.value[this.keyArray].FileID = this.CustomerContractTrans.FileID;
              this.value[this.keyArray].FileName = this.CustomerContractTrans.FileName;
              this.value[this.keyArray].ItemID = this.CustomerContractTrans.ItemID;
              this.value[this.keyArray].ItemName = this.CustomerContractTrans.ItemName;
              this.value[this.keyArray].FCAmount = this.CustomerContractTrans.FCAmount;
              this.value[this.keyArray].ExpectedEndDate = this.CustomerContractTrans.ExpectedEndDate;
              this.value[this.keyArray].PercentSuccess = this.CustomerContractTrans.PercentSuccess;
              this.value[this.keyArray].CreatedDate = this.CustomerContractTrans.CreatedDate;
              this.value[this.keyArray].StatusID = this.CustomerContractTrans.StatusID;
              this.value[this.keyArray].StatusName = this.CustomerContractTrans.StatusName;
              this.value[this.keyArray].StatusValue = this.CustomerContractTrans.StatusValue;
              this.value[this.keyArray].StatusDescription = this.CustomerContractTrans.StatusDescription;
              this.value[this.keyArray].addnew = false;
            }
            // self.value.CustomerContractTransCate = responsesData.data.CustomerContractTransCate;
            self.value.CustomerContractTrans = responsesData.data.CustomerContractTrans;
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
      changeFile(files) {
        let self = this;
        let dateC = __.convertDateTimeToString(new Date());
        let Employee = JSON.parse(localStorage.getItem('Employee'));
        for (var i = 0; i < files.length; i++) {
          self.$store.commit('isLoading', true);
          var file = files[i];
          let formData = new FormData();
          formData.append('LineID', '');
          formData.append('FileUpload', file);
          formData.append('CustomerID', self.Customer.CustomerID);
          formData.append('TransID', Employee.UserID);
          formData.append('TransTable', 'customer_contract_trans');
          formData.append('FileName', file.name);
          formData.append('Description', file.name);
          formData.append('DocID', '');
          formData.append('DocNo', '');
          formData.append('DocName', '');
          formData.append('FileType', file.name.split('.').pop());
          formData.append('FileSize', file.size);
          formData.append('DateModified', dateC);
          formData.append('UserModified', '');
          formData.append('changeFile', 1);
          formData.append('changeData', 1);

          let currentObj = this;
          const config = {
            headers: {
              'content-type': 'multipart/form-data',
            }
          };

          // send upload request
          axios.post('customer/api/customer/customer-upload-file/' + self.Customer.CustomerID, formData, config)
            .then(function (response) {
              let responseData = response.data; //console.log(response.data);
              if (responseData.status === 1) {
                currentObj.success = response.data.success;
                currentObj.filename = "";
                let dataR = response.data.data;
                self.CustomerFile.push({
                  LineID: dataR.LineID,
                  FileUpload: file,
                  TransID: 1,
                  CustomerID: dataR.CustomerID,
                  FileID: dataR.FileID,
                  FileName: dataR.FileName,
                  Description: dataR.FileName,
                  FileType: dataR.FileType,
                  FileSize: dataR.FileSize,
                  DateModified: dateC,
                  UserModified: dataR.UserModified,
                  Link: dataR.Link,
                  DateModifiedRoot: '',
                  FileNameRoot: '',
                  DocID: '',
                  DocNo: '',
                  DocName: '',
                  changeFile: 0,//Đã thay đổi file
                  changeData: 0
                });
                //self.$forceUpdate();
                self.$bvToast.toast('Tải lên thành công', {
                  title: 'Thông báo',
                  variant: 'success',
                  solid: true
                });

                if ($('.component-dataflow').length) {
                  self.$_storeTaskDataflowNotice(self.Customer.CustomerID, 'updateFile');
                } else {
                  self.$_storeTaskNotice(self.Customer.CustomerID, 'updateFile');
                }

              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Thông báo',
                  text: responseData.msg,
                  confirmButtonText: 'Đóng'
                });
              }

              self.showCustomerFile = true;
              self.$store.commit('isLoading', false);
            })
            .catch(function (error) {
              console.log(error);
              self.$store.commit('isLoading', false);
              self.$bvToast.toast('Tải lên thất bại', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
            });
        }
      },
      onChangeStatus() {
        let statusValueOption = _.filter(this.CustomerContractTrans.StatusValueOption, ['StatusID', this.CustomerContractTrans.StatusID]);
        if (statusValueOption && statusValueOption.length) {
          this.CustomerContractTrans.StatusValue = statusValueOption[0].value;
        }
      },
      autoFCAmount(){
        let FCAmount = Number(this.CustomerContractTrans.FCAmount);
        let ExchangeRate = Number(this.CustomerContractTrans.ExchangeRate);

        let LCAmount = 0;
        if (ExchangeRate) {
          LCAmount = FCAmount * ExchangeRate;
        }else{
          LCAmount = FCAmount;
        }
        if(LCAmount){
          this.CustomerContractTrans.LCAmount = LCAmount.toFixed(3);
        }
        this.$forceUpdate()
      },

    },
    watch: {
      idParams() {
        this.fetchData();
      },
      'CustomerContractTrans.FCAmount'(){
        this.autoFCAmount();
      },
      'CustomerContractTrans.ExchangeRate'(){
        this.autoFCAmount();
      }
    },
    filters: {
      filterStatusValueOption(value, StatusID){
        if (StatusID) return _.filter(value, ['StatusID', StatusID]);
        return value;
      }
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
      StatusValueOption: {},
      CustomerContractTransCate: {},
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
</style>
