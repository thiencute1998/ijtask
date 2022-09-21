<template>
  <div class="table-responsive table-responsive-assign" ref="DivContainerTable">
      <table class="not-border" v-if="!isForm" :style="[isDetail ? {width: '1660px'} : '']">
      <thead>
      <tr class="text-left">
        <th class="pr-3">Khách hàng</th>
        <th class="pr-3" v-if="isDetail">Người liên hệ</th>
        <th class="pr-3">Chức vụ</th>
        <th class="pr-3">Bộ phận</th>
        <th class="pr-3" v-if="isDetail">ĐTCQ </th>
        <th class="pr-3">ĐTDĐ </th>
        <th class="pr-3">Email </th>
        <th class="pr-3" v-if="isDetail">Facebook </th>
        <th class="pr-3" v-if="isDetail">Tiwtter </th>
        <th class="pr-3" v-if="isDetail">Skype </th>
        <th class="pr-3" v-if="isDetail">Zalo </th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in value">
        <td class="pr-3">{{item.CustomerName}}</td>
        <td class="pr-3" v-if="isDetail">{{item.ContactName}}</td>
        <td class="pr-3">{{item.PositionName}}</td>
        <td class="pr-3">{{item.DepartmentName}}</td>
        <td class="pr-3" v-if="isDetail">{{item.OfficePhone}}</td>
        <td class="pr-3">{{item.HandPhone}}</td>
        <td class="pr-3">{{item.Email}}</td>
        <td class="pr-3" v-if="isDetail">{{item.FacebookID}}</td>
        <td class="pr-3" v-if="isDetail">{{item.TiwtterID}}</td>
        <td class="pr-3" v-if="isDetail">{{item.SkypeID}}</td>
        <td class="pr-3" v-if="isDetail">{{item.ZaloID}}</td>
      </tr>
      </tbody>
    </table>
    <table v-if="isForm" class="table b-table table-sm table-bordered table-editable el-first-modal" style="width: 1660px;" ref="TableEditAssign">
      <thead>
      <tr class="text-center">
        <th>Khách hàng</th>
        <th>Người liên hệ</th>
        <th>Chức vụ</th>
        <th>Bộ phận</th>
        <th style="width: 100px">ĐTCQ</th>
        <th style="width: 100px">ĐTDĐ</th>
        <th style="width: 200px">Email</th>
        <th style="width: 150px">Facebook</th>
        <th style="width: 150px">Tiwtter</th>
        <th style="width: 150px">Skype</th>
        <th style="width: 150px">Zalo</th>
        <th class="td-action"></th>
      </tr>
      </thead>
      <tbody style="width: auto">
      <tr v-for="(item, key) in this.value"  style="width: auto;">
        <td class="">
          <IjcoreModalListing v-model="value[key]" :title="'Khách hàng'" :api="'/listing/api/common/list'"
                              :table="'customer'">
          </IjcoreModalListing>
        </td>
        <td class="DateText">
          <input v-model="value[key].ContactName" class="form-control"/>
        </td>
        <td class="DateText">
          <input v-model="value[key].PositionName" class="form-control"/>
        </td>
        <td class="DateText">
          <input v-model="value[key].DepartmentName" class="form-control"/>
        </td>
        <td class="DateText">
          <input v-model="value[key].OfficePhone" class="form-control"/>
        </td>
        <td class="DateText">
          <input v-model="value[key].HandPhone" class="form-control"/>
        </td>
        <td class="DateText">
          <input v-model="value[key].Email" class="form-control"/>
        </td>
        <td class="DateText">
          <input v-model="value[key].FacebookID" class="form-control"/>
        </td>
        <td class="DateText">
          <input v-model="value[key].TiwtterID" class="form-control"/>
        </td>
        <td class="DateText">
          <input v-model="value[key].SkypeID" class="form-control"/>
        </td>
        <td class="DateText">
          <input v-model="value[key].ZaloID" class="form-control"/>
        </td>

        <td style="text-align: center;width: 50px;" class="td-action"><i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o"
                                                                         style="font-size: 18px; cursor: pointer;"></i></td>
      </tr>
      </tbody>
    </table>
    <IjcoreModalMultiListing v-model="value" @changed="addLine" v-if="isForm" :title="'khách hàng'"
                             :api="'/listing/api/common/list'" :table="'customer'"></IjcoreModalMultiListing>
  </div>

</template>

<script>
    import ApiService from '@/services/api.service';
    import IjcoreModalListing from "../../../../components/IjcoreModalListing";
    import IjcoreModalMultiListing from "../../../../components/IjcoreModalMultiListing";

    export default {
        name: 'CustomerContactContent',
        components: {
          IjcoreModalListing,
          IjcoreModalMultiListing,
        },
        computed: {
          rows() {
            return this.totalRows
          },
        },
        data () {
          return {
            idParams: this.$route.params.id,
            listtable: [
            ],
            tableName: '',
            search:'',
            lenghNo: 0,
            object: {
              master: {},
              detail: [],
            },
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
            addLine(link) {
              let self = this;
              link.map(function (item, key) {
                self.value.push({
                  CustomerID: item.CustomerID,
                  CustomerName: item.CustomerName,
                  ContactName: '',
                  PositionName: '',
                  DepartmentName: '',
                  OfficePhone: '',
                  HandPhone: '',
                  Email: '',
                  FacebookID: '',
                  TiwtterID: '',
                  SkypeID: '',
                  ZaloID: '',
                  addnew: true,
                });
              });

            },
            deleteLine(key) {
              this.value.splice(key, 1);
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
          value: [Array, Object],
          title: {},
          name: {},
          api: {},
          table: {},
          Customer: {},
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
