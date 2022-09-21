<template>
  <div class="table-responsive table-responsive-assign" ref="DivContainerTable">
    <table class="not-border" v-if="!isForm" :style="[isDetail ? {width: '1960px'} : '']" style="width: 98.333%">
      <thead>
      <tr class="text-left">
        <th class="pr-3" style="width: 145px;">Ngày</th>
        <th class="pr-3" style="width: 200px;">Nội dung</th>
        <th class="pr-3" style="width: 239px;">Hợp đồng</th>
        <th class="pr-3" style="width: 239px;">Khách hàng</th>
        <th class="pr-3" style="width: 120px;">Giá trị (vnđ)</th>
        <th class="pr-3">Trạng thái</th>
        <th class="pr-3" v-if="isDetail"><i class="fa fa-database"></i> </th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in value">
        <td class="pr-3">{{item.TransDate|convertTimeToHMTime}}</td>
        <td class="pr-3" style="white-space: pre;" :title="item.TransComment | stripHtml"><div style="width: 100%; max-width: 150px; overflow: hidden; text-overflow: ellipsis;
    white-space: nowrap;">{{item.TransComment}}</div></td>
        <td class="pr-3">{{item.ContractInfo}}</td>
        <td class="pr-3">{{item.CustomerInfo}}</td>

        <td class="pr-3" style="text-align: right">{{item.FCAmount|convertNumberToText}}</td>
        <td class="pr-3"><span class="badge badge-success">{{item.StatusDescription}}</span></td>
        <td class="pr-3" v-if="isDetail">
          <i @click=""
             class="fa fa-external-link" title="Tình trạng cập nhật data"
             style="font-size: 18px; cursor: pointer; padding-right: 5px;"></i>
        </td>
        <td style="text-align: center;width: 50px;right: 8px;" class="right-absolute">
          <CustomerContractTransForm v-model="value" :keyArray="key" :title="'Giao dịch Hợp đồng bán hàng'" :Customer="Customer" :StatusOption="StatusOption" :StatusValueOption="StatusValueOption" :CustomerContractTransCate="CustomerContractTransCate" :per="per" :addnew="false" :StyleIcon="'position: absolute; left: 2px;'">
          </CustomerContractTransForm>
          <i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o"
             style="font-size: 18px; cursor: pointer;position: relative; top: -1px;right: -16px"></i>
        </td>
      </tr>
      </tbody>
    </table>

  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import IjcoreNumber from "../../../../components/IjcoreNumber";
  import CustomerContractTransForm from "./CustomerContractTransForm";

  export default {
    name: 'CustomerContractTransContent',
    components: {
      IjcoreNumber,
      CustomerContractTransForm
    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data () {
      return {
        //isForm: false,
        idParams: this.$route.params.id,
        listtable: [],
        RowItem: 0,
        tableName: '',
        search:'',
        lenghNo: 0,
      }
    },
    created() {
    },
    mounted() {
      this.fetchData();
    },
    methods: {
      fetchData(){

      },
      onAddFieldCustomerContractTransCate() {
        let fieldObj = {};
        fieldObj.CateID = '';
        fieldObj.CateValue = null;
        if(this.value.CustomerContractTransCate[this.TransItemIDCurrent] == undefined){
          this.value.CustomerContractTransCate[this.TransItemIDCurrent] = [];
        }
        this.value.CustomerContractTransCate[this.TransItemIDCurrent].push(fieldObj);
        this.$forceUpdate();
      },
      onDeleteFieldCustomerContractTransCate(key) {
        this.value.CustomerContractTransCate[this.TransItemIDCurrent].splice(key, 1);
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
      },
      onToggleModal(){
        let self = this;
        this.currentPage = 1;
        this.$refs['modal'].show();
        this.fetchData();
      },
      onResetModal(){
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
        this.value.push({
          TransID: this.RowItem,
          CustomerID: '',
          CustomerName: '',
          ContactName: '',
          CustomerInfo: '',
          ContractInfo: '',
          TransDate: '',
          TransComment: '',
          Time: '',
          FileID: '',
          FileName: '',
          ItemID: '',
          ItemName: '',
          CcyID: '',
          CcyNo: '',
          ExchangeRate: '',
          FCAmount: '',
          LCAmount: '',
          ExpectedEndDate: '',
          PercentSuccess: '',
          StatusID: '',
          StatusDescription: '',
          CreatedDate: '',
          addnew: true,
          LineIDTemp: this.RowItem
        });
        this.RowItem = this.RowItem + 1;
      },
      deleteLine(key) {
        this.$store.commit('isLoading', true);
        let self = this;
        const requestData = {
          method: 'post',
          url: 'customer/api/customer/customer-contracttrans-delete',
          data: {
            TransID: self.value[key].TransID
          }
        };
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            if (!self.idParams && responsesData.data) self.idParams = responsesData.data.param;
            this.$bvToast.toast('Đã xóa thành công!', {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });
            this.value.splice(key, 1);
            self.$store.commit('isLoading', false);
          } else {
            let htmlErrors = __.renderErrorApiHtml(responsesData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            )
            self.$store.commit('isLoading', false);
          }

        }, (error) => {
          console.log(error);
          Swal.fire(
            'Thông báo',
            'Không kết nối được với máy chủ',
            'error'
          )
          self.$store.commit('isLoading', false);
        });
      },
      // deleteLine(key) {
      //   this.value.splice(key, 1);
      // },
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
      StatusValueOption: {},
      CustomerContractTransCate: {},
      per: {},
      isForm: false,
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
