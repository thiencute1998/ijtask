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
      <label class="col-md-2" style="white-space: nowrap">Ngày </label>
      <div class="col-md-6 DateTimeText" >
        <IjcoreDateTimePicker v-model="CustomerSupportTrans.TransDate" :allowEmptyClear="true" style="width: 250px">
        </IjcoreDateTimePicker>
      </div>
      <label class="col-md-1" style="white-space: nowrap">Số phút</label>
      <div class="col-md-2">
        <input v-model="CustomerSupportTrans.Time" class="form-control"/>
      </div>
      <label class="col-md-1" style="white-space: nowrap">Nhân viên</label>
      <div class="col-md-6">
        <IjcoreModalListing v-model="CustomerSupportTrans" :title="'nhân viên'" :api="'/listing/api/common/list'"
                            :table="'employee'" :FieldID="'EmployeeID'" :FieldName="'EmployeeName'">
        </IjcoreModalListing>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-2" style="white-space: nowrap">Khách hàng</label>
      <div class="col-md-12">
        <IjcoreModalListing v-model="CustomerSupportTrans" :title="'khách hàng'" :api="'/listing/api/common/list'"
                            :table="'customer'" :FieldID="'CustomerID'" :FieldName="'CustomerName'"
                            :FieldUpdate="['CustomerName', 'ContactName', 'DepartmentName', 'OfficePhone', 'HandPhone', 'Email']">
        </IjcoreModalListing>
      </div>
      <label class="col-md-1" style="white-space: nowrap">Người liên hệ</label>
      <div class="col-md-6">
        <IjcoreModalListing v-model="CustomerSupportTrans" :title="'người liên hệ'" :api="'/listing/api/common/list'"
                            :table="'Customer_Contact'" :FieldID="'LineID'" :FieldName="'ContactName'"
                            :FieldUpdate="['PositionName']"
                            :FieldWhere="{'CustomerID' : CustomerSupportTrans.CustomerID}">
        </IjcoreModalListing>
      </div>

    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-2" style="white-space: nowrap">Hàng hóa</label>
      <div class="col-md-20" >
        <IjcoreModalListing v-model="CustomerSupportTrans" :title="'hàng hóa dịch vụ'" :api="'/listing/api/common/list'"
                            :table="'item'" :FieldID="'ItemID'" :FieldName="'ItemName'">
        </IjcoreModalListing>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-2" style="white-space: nowrap">Nội dung</label>
      <div class="col-md-20" >
        <textarea class="form-control" rows="3" placeholder="" v-model="CustomerSupportTrans.TransComment" ></textarea>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-2"  style="white-space: nowrap">Loại giao dịch hỗ trợ</label>
      <div class="col-md-2 col-sm-20">
        <i @click="AddCustomerSupportTransCate()"
           class="fa fa-external-link" title="Loại giao dịch hỗ trợ"
           style="font-size: 18px; cursor: pointer; padding-right: 5px; padding-left: 12px;"></i>
      </div>
    </div>
    <b-collapse class="mt-2" v-model="showCustomerTransCate">
      <div class="form-group row">
        <label class="col-md-2" style="white-space: nowrap"></label>
        <div class="col-md-20">
          <p v-for="(field, key) in value.CustomerSupportTransCate" style="margin-bottom: -2px;"><span v-if="field.CateName">{{field.CateName}}: {{field.CateValue}}<br></span></p>
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

<!--    <div class="form-group row align-items-center">-->
<!--      <label class="col-md-1" style="white-space: nowrap">Trạng thái</label>-->
<!--      <div class="col-md-6">-->
<!--        <b-form-select v-model="CustomerSupportTrans.StatusDescription"-->
<!--                       :options="StatusOption"></b-form-select>-->
<!--      </div>-->
<!--    </div>-->
    <div class="form-group row align-items-center">
      <div class="col-lg-2">Loại trạng thái</div>
      <div class="col-lg-2">
        <b-form-select v-model="CustomerSupportTrans.StatusID" :options="StatusOption" @change="onChangeStatus"></b-form-select>
      </div>
      <div class="col-lg-2">Trạng thái</div>
      <div class="col-lg-2">
        <b-form-select v-model="CustomerSupportTrans.StatusValue" :options="StatusValueOption | filterStatusValueOption(Number(CustomerSupportTrans.StatusID))"></b-form-select>
      </div>
    </div>

    <!-- Loại giao dịch hỗ trợ -->
    <b-modal ref="CustomerSupportTransCate" id="modal-form-input-task-general1" size="lg"
             title="Loại giao dịch hỗ trợ">
<!--    <div style="margin-bottom: -12px;"><b>Loại giao dịch bán hàng</b></div>-->
    <div class="main-body main-body-view-action">
      <table class="table b-table table-sm table-bordered table-editable">
        <thead>
        <tr>
          <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Loại giao dịch hỗ trợ </th>
          <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Giá trị</th>
          <th scope="col" style="width: 2%; border-bottom: none;" class="text-center">
            <!--<i @click="onAddFieldOnTable" class="fa fa-plus" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i>--></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(field, key) in value.CustomerSupportTransCate">
          <td>
            <IjcoreModalListing v-model="value.CustomerSupportTransCate[key]" :title="'loại GDHT'" :api="'/listing/api/common/list'"
                                :table="'customer_support_trans_cate_list'" :FieldID="'CateID'" :FieldName="'CateName'" @changed="changeCustomerCateValue()">
            </IjcoreModalListing>
          </td>
          <td>
            <IjcoreModalListing v-model="value.CustomerSupportTransCate[key]" :title="'giá trị'" :api="'/listing/api/common/list'"
                                :table="'customer_support_trans_cate_value'" :FieldName="'CateValue'"
                                :FieldWhere="{'CateID' : value.CustomerSupportTransCate[key]}" @changed="changeCustomerCateValue()">
            </IjcoreModalListing>
          </td>
          <td class="text-center">
            <i @click="onDeleteFieldCustomerSupportTransCate(key)" class="fa fa-trash-o" title="Xóa"
               style="font-size: 18px; cursor: pointer"></i>
          </td>
        </tr>

        </tbody>
      </table>
      <a @click="onAddFieldCustomerSupportTransCate()" class="new-row">
        <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
      </a>
    </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="SaveCustomerSupportTransCate()">
            Lưu
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="HideCustomerSupportTransCate()">
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
    name: 'CustomerSupportTransForm',
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
        CustomerSupportTrans: {
          CustomerID: '',
          TransDate: '',
          TransComment: '',
          EmployeeID: '',
          EmployeeName: '',
          CustomerName: '',
          ContactID: '',
          ContactName: '',
          CustomerInfo: '',
          Time: '',
          FileID: '',
          FileName: '',
          ItemID: '',
          ItemName: '',
          StatusID: '',
          StatusDescription: '',
          CreatedDate: '',
          CreatedUserID: '',
          UpdatedDate: '',
          UpdatedUserID: '',
          Locked: '',
          LockedDate: '',
          LockedUserID: '',
        },
        //CustomerSupportTransCate: {},
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
      onAddFieldCustomerSupportTransCate() {
        let fieldObj = {};
        fieldObj.CateID = '';
        fieldObj.CateValue = null;
        if(this.value.CustomerSupportTransCate == undefined){
          this.value.CustomerSupportTransCate = [];
        }
        this.value.CustomerSupportTransCate.push(fieldObj);
        this.$forceUpdate();
      },
      onDeleteFieldCustomerSupportTransCate(key) {
        this.value.CustomerSupportTransCate.splice(key, 1);
        //this.setStyleAction();
        this.$forceUpdate();
      },
      AddCustomerSupportTransCate(TransID,key) {
        this.TransItemIDCurrent = TransID;
        this.TransItemKeyCurrent = key;
        this.$forceUpdate();
        this.$refs['CustomerSupportTransCate'].show();
      },
      HideCustomerSupportTransCate() {
        //this.isForm = false;
        this.$refs['CustomerSupportTransCate'].hide();
      },
      SaveCustomerSupportTransCate() {
        this.showCustomerTransCate = true;
        this.$bvToast.toast('Đã lưu loại khách hàng\n', {
          title: 'Thông báo',
          variant: 'success',
          solid: true
        });
        this.$refs['CustomerSupportTransCate'].hide();
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
          this.CustomerSupportTrans.TransDate = this.value[this.keyArray].TransDate;
          //this.CustomerSupportTrans.ExpectedEndDate = this.value[this.keyArray].ExpectedEndDate;
          //this.CustomerSupportTrans.TransDate = __.convertDateTimeToString(this.value[this.keyArray].TransDate);
          this.CustomerSupportTrans.StatusDescription = this.value[this.keyArray].StatusDescription;
          this.CustomerSupportTrans.StatusID = this.value[this.keyArray].StatusID;
          this.CustomerSupportTrans.StatusValue = this.value[this.keyArray].StatusValue;
          this.CustomerSupportTrans.EmployeeID = this.value[this.keyArray].EmployeeID;
          this.CustomerSupportTrans.EmployeeName = this.value[this.keyArray].EmployeeName;
          this.CustomerSupportTrans.CustomerID = this.value[this.keyArray].CustomerID;
          this.CustomerSupportTrans.CustomerName = this.value[this.keyArray].CustomerName;
          this.CustomerSupportTrans.LineID = this.value[this.keyArray].ContactID;
          this.CustomerSupportTrans.ContactName = this.value[this.keyArray].ContactName;
          this.CustomerSupportTrans.ItemID = this.value[this.keyArray].ItemID;
          this.CustomerSupportTrans.ItemName = this.value[this.keyArray].ItemName;
          this.CustomerSupportTrans.Time = this.value[this.keyArray].Time;
          this.CustomerSupportTrans.FileID = this.value[this.keyArray].FileID;
          this.CustomerSupportTrans.FileName = this.value[this.keyArray].FileName;
          this.CustomerSupportTrans.TransComment = this.value[this.keyArray].TransComment;

          self.TransID = this.value[this.keyArray].TransID;
          if (!self.value.CustomerSupportTransCate) {
            self.$set(self.value, 'CustomerSupportTransCate', []);
            _.forEach(self.CustomerSupportTransCate, function (field, key) {
              let tmpObj = {};
              if (self.CustomerSupportTransCate[key].TransID == self.TransID) {
                tmpObj.CateID= field.CateID;
                tmpObj.CateName= field.CateName;
                tmpObj.CateValue= field.CateValue;
                self.$set(self.value.CustomerSupportTransCate, self.value.CustomerSupportTransCate.length, tmpObj);
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
          this.CustomerSupportTrans.TransDate = __.convertDateTimeToString(new Date());
          this.CustomerSupportTrans.StatusDescription = '';
          this.CustomerSupportTrans.EmployeeID = '';
          this.CustomerSupportTrans.EmployeeName = '';
          this.CustomerSupportTrans.CustomerID = '';
          this.CustomerSupportTrans.CustomerName = '';
          this.CustomerSupportTrans.ContactID = '';
          this.CustomerSupportTrans.ContactName = '';
          this.CustomerSupportTrans.ItemID = '';
          this.CustomerSupportTrans.ItemName = '';
          this.CustomerSupportTrans.Time = '';
          this.CustomerSupportTrans.FileID = '';
          this.CustomerSupportTrans.FileName = '';
          this.CustomerSupportTrans.TransComment = '';
          this.CustomerSupportTrans.StatusID = 1 ;
          this.CustomerSupportTrans.StatusValue = 1 ;
          this.value.CustomerSupportTransCate = [];
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
        let CustomerSupportTransData = self.CustomerSupportTrans;
        CustomerSupportTransData.ContactID = self.CustomerSupportTrans.LineID;
        let statusValue = _.find(this.StatusValueOption, {
          StatusID: this.CustomerSupportTrans.StatusID,
          value: this.CustomerSupportTrans.StatusValue
        });
        this.CustomerSupportTrans.StatusDescription = (statusValue) ? statusValue.text : '';
        let requestData = {
          method: 'post',
          url: 'customer/api/customer/customer-support/' + this.idParams,
          data: {
            CustomerSupportTrans: CustomerSupportTransData,
            CustomerSupportTransCate: self.value.CustomerSupportTransCate
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
            let status = _.find(this.StatusOption, ['value', Number(this.CustomerSupportTrans.StatusID)]);
            let statusValue = _.find(this.StatusValueOption, {
              StatusID: this.CustomerSupportTrans.StatusID,
              value: this.CustomerSupportTrans.StatusValue
            });
            if(this.addnew){
              responsesData.data.CustomerSupportTrans.StatusName = (status) ? status.text : '';
              responsesData.data.CustomerSupportTrans.StatusDescription = (statusValue) ? statusValue.text : '';
              //responsesData.data.CustomerSupportTrans.TransDate = __.convertDateTimeToString(responsesData.data.CustomerSupportTrans.TransDate);
              this.value.push(responsesData.data.CustomerSupportTrans);
            }else{
              if(!this.CustomerSupportTrans.PositionName){ this.CustomerSupportTrans.PositionName='';}
              if(!this.CustomerSupportTrans.OfficePhone){ this.CustomerSupportTrans.OfficePhone='';}
              this.CustomerInfo = 'Tên khách hàng: '+this.CustomerSupportTrans.CustomerName+', '+'Người liên hệ: '+this.CustomerSupportTrans.ContactName+', '+'Chức vụ: '+this.CustomerSupportTrans.PositionName+', '+'Điện thoại: '+this.CustomerSupportTrans.OfficePhone;
              this.value[this.keyArray].TransID = this.CustomerSupportTrans.TransID;
              this.value[this.keyArray].TransDate = this.CustomerSupportTrans.TransDate;
              this.value[this.keyArray].TransComment = this.CustomerSupportTrans.TransComment;
              this.value[this.keyArray].EmployeeID = this.CustomerSupportTrans.EmployeeID;
              this.value[this.keyArray].EmployeeName = this.CustomerSupportTrans.EmployeeName;
              this.value[this.keyArray].CustomerID = this.CustomerSupportTrans.CustomerID;
              this.value[this.keyArray].CustomerName = this.CustomerSupportTrans.CustomerName;
              this.value[this.keyArray].LineID = this.CustomerSupportTrans.ContactID;
              this.value[this.keyArray].ContactName = this.CustomerSupportTrans.ContactName;
              this.value[this.keyArray].PositionName = this.CustomerSupportTrans.PositionName;
              this.value[this.keyArray].OfficePhone = this.CustomerSupportTrans.OfficePhone;
              this.value[this.keyArray].CustomerInfo = this.CustomerInfo;
              this.value[this.keyArray].Time = this.CustomerSupportTrans.Time;
              this.value[this.keyArray].FileID = this.CustomerSupportTrans.FileID;
              this.value[this.keyArray].FileName = this.CustomerSupportTrans.FileName;
              this.value[this.keyArray].ItemID = this.CustomerSupportTrans.ItemID;
              this.value[this.keyArray].ItemName = this.CustomerSupportTrans.ItemName;
              this.value[this.keyArray].StatusID = this.CustomerSupportTrans.StatusID;
              this.value[this.keyArray].StatusName = (status) ? status.text : '';
              this.value[this.keyArray].StatusValue = this.CustomerSupportTrans.StatusValue;
              this.value[this.keyArray].StatusDescription = (statusValue) ? statusValue.text : '';
              this.value[this.keyArray].addnew = false;
            }
            self.value.CustomerSupportTransCate = responsesData.data.CustomerSupportTransCate;
            self.value.CustomerSupportTrans = responsesData.data.CustomerSupportTrans;

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
          formData.append('TransTable', 'customer_support_trans');
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
          //console.log('sssssssssssssssssss');
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
                  TransID: dataR.TransID,
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
        let statusValueOption = _.filter(this.CustomerSupportTrans.StatusValueOption, ['StatusID', this.CustomerSupportTrans.StatusID]);
        if (statusValueOption && statusValueOption.length) {
          this.CustomerSupportTrans.StatusValue = statusValueOption[0].value;
        }
      },

    },
    watch: {
      idParams() {
        this.fetchData();
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
      CustomerSupportTransCate: {},
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
