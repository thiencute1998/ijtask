<template>
  <div class="table-responsive table-responsive-assign" ref="DivContainerTable">
    <table class="not-border" v-if="!isForm" :style="[isDetail ? {width: '1960px'} : '']">
      <thead>
      <tr class="text-left">
        <th class="pr-3" style="width: 145px;">Ngày </th>
        <th class="pr-3">Số </th>
        <th class="pr-3">Công ty</th>
        <th class="pr-3">Khách hàng</th>
        <th class="pr-3" style="width: 255px;">Nội dung</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in value">
        <td class="pr-3">{{item.TransDate}}</td>
        <td class="pr-3">{{item.TransNo}}</td>
        <td class="pr-3">{{item.CompanyName}}</td>
        <td class="pr-3">{{item.CustomerName}}</td>
        <td class="pr-3">{{item.TransComment}}</td>

        <td style="text-align: center;width: 53px;right: 14px;" class="right-absolute">
          <CustomerQuotationTransForm v-model="value" :keyArray="key" :title="'Báo giá bán hàng'" :Customer="Customer" :StatusOption="StatusOption" :StatusValueOption="StatusValueOption" :CustomerQuotationTransCate="CustomerQuotationTransCate" :per="per" :addnew="false" :StyleIcon="'position: absolute; left: 2px;'">
          </CustomerQuotationTransForm>
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
  import CustomerQuotationTransForm from "./CustomerQuotationTransForm";

  export default {
    name: 'CustomerQuotationTransContent',
    components: {
      IjcoreNumber,
      CustomerQuotationTransForm
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
      onAddFieldCustomerQuotationTransCate() {
        let fieldObj = {};
        fieldObj.CateID = '';
        fieldObj.CateValue = null;
        if(this.value.CustomerQuotationTransCate[this.TransItemIDCurrent] == undefined){
          this.value.CustomerQuotationTransCate[this.TransItemIDCurrent] = [];
        }
        this.value.CustomerQuotationTransCate[this.TransItemIDCurrent].push(fieldObj);
        this.$forceUpdate();
      },
      onDeleteFieldCustomerQuotationTransCate(key) {
        this.value.CustomerQuotationTransCate[this.TransItemIDCurrent].splice(key, 1);
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
      deleteLine(key) {
        this.$store.commit('isLoading', true);
        let self = this;
        const requestData = {
          method: 'post',
          url: 'customer/api/customer/customer-quotationtrans-delete',
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
      CustomerQuotationTransCate: {},
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
