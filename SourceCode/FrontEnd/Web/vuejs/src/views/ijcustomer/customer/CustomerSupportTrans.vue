<template>
    <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
        <i class="fa fa-plus-square-o ij-icon ij-icon-plus" aria-hidden="true" v-if="isNew" title="Thêm"></i>
        <i class="fa fa-external-link ij-icon" aria-hidden="true" v-if="!isNew" title="Chi tiết"></i>
        <b-modal scrollable ref="modal" id="modal-form-input-task-general" size="xl" @hide="onHideModalDataflow($event)">
            <template slot="modal-title">
                <i class="icon-eye icon" v-if="!isForm"></i><i class="fa fa-edit" v-if="isForm"></i> {{this.title}}
            </template>
            <CustomerSupportTransContent v-model="value" v-if="!isForm" :isDetail="true" :Customer="Customer" :StatusOption="StatusOption" :CustomerSupportTransCate="CustomerSupportTransCate">
            </CustomerSupportTransContent>
            <CustomerSupportTransContent v-model="value" v-if="isForm" :isForm="true" :Customer="Customer" :StatusOption="StatusOption" :CustomerSupportTransCate="CustomerSupportTransCate">
            </CustomerSupportTransContent>
            <template v-slot:modal-footer>
                <div class="w-100 left">
<!--                    <b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" v-if="!isForm">-->
<!--                        Sửa-->
<!--                    </b-button>-->
                    <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdate()" v-if="!isForm">
                        Lưu
                    </b-button>
                    <b-button variant="primary" size="md" class="float-left mr-2" @click="" v-if="!isForm">
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
    import CustomerSupportTransContent from "./partials/CustomerSupportTransContent";
    import Swal from 'sweetalert2';

    export default {
        name: 'CustomerSupportTrans',
        components: {
            CustomerSupportTransContent
        },
        computed: {
            rows() {
                return this.totalRows
            },
        },
        data () {
            return {
                idParams: this.$route.params.id,
                isForm: false,
                listtable: [
                ],
                tableName: '',
                search:'',
                lenghNo: 0,
                model: {
                  TransDate: '',
                  TransComment: '',
                  EmployeeID: '',
                  EmployeeName: '',
                  CustomerID: '',
                  CustomerName: '',
                  ContactName: '',
                  CustomerInfo: '',
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
                  CreatedUserID: '',
                  UpdatedDate: '',
                  UpdatedUserID: '',
                  Locked: '',
                  LockedDate: '',
                  LockedUserID: '',
                },
            }
        },
        created() {
          if (this.isNew) {
            this.isForm = true;
          } else {
            this.isForm = false;
          }
        },
        mounted() {
            if (this.isDataflow) {
                this.onToggleModal();
            }
        },
        methods: {
          fetchData() {
            let self = this;
            let requestData = {
              method: 'get',
              data: {}
            };
            requestData.url = '/customer/api/customer/get-table';
            this.$store.commit('isLoading', true);

            ApiService.setHeader();
            ApiService.customRequest(requestData).then((responses) => {
              let responsesData = responses.data;
              self.SysTable = [];
              _.forEach(responsesData.data, function (value, key) {
                let tmpObj = {};
                tmpObj.value = value.TableName;
                tmpObj.text = value.TableDescription;
                self.SysTable.push(tmpObj);
              });

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
            onEdit(){
                this.isForm = true;
            },
            onUpdate() {
              this.$store.commit('isLoading', true);
              let self = this;
              const requestData = {
                method: 'post',
                url: 'customer/api/customer/customer-support/' + this.idParams,
                data: {
                  // CustomerSupportTrans: {
                  //   TransDate: self.value.TransDate,
                  //   TransComment: self.value.TransComment,
                  //   EmployeeID: self.value.EmployeeID,
                  //   EmployeeName: self.value.EmployeeName,
                  //   CustomerID: self.value.CustomerID,
                  //   CustomerName: self.value.CustomerName,
                  //   ContactName: self.value.ContactName,
                  //   CustomerInfo: self.value.CustomerInfo,
                  //   Time: self.value.Time,
                  //   FileID: self.value.FileID,
                  //   FileName: self.value.FileName,
                  //   ItemID: self.value.ItemID,
                  //   ItemName: self.value.ItemName,
                  //   FCAmount: self.value.FCAmount,
                  //   LCAmount: self.value.LCAmount,
                  //   ExpectedEndDate: self.value.ExpectedEndDate,
                  //   PercentSuccess: self.value.PercentSuccess,
                  //   StatusDescription: self.value.StatusDescription,
                  // },
                  CustomerSupportTrans: self.value,
                  CustomerSupportTransCate: this.CustomerSupportTransCate
                }
              };
              // edit user
              requestData.data.ItemID = this.idParams;

              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data; //console.log(responses.data);
                if (responsesData.status === 1) {
                  if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
                  this.$bvToast.toast('Cập nhật thành công!', {
                    title: 'Thông báo',
                    variant: 'success',
                    solid: true
                  });
                  this.isForm = false;
                  this.$refs['modal'].hide();
                  self.$store.commit('isLoading', false);
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
                this.isForm = false;
                this.$refs['modal'].hide();
            },
            onHideModalDataflow(){
                if (this.isDataflow) {
                    this.$emit('onHideModalCustomer');
                }
            },
            onToggleModal(){
                let self = this;
                this.currentPage = 1;
                this.isForm = false;
                this.$refs['modal'].show();
            },
            onResetModal(){
            },
        },
        watch: {
            currentPage() {
                this.fetchData();
            }
        },
        props: {
          value: {},
          title: {},
          name: {},
          api: {},
          //Customer: {},
          table: {},
          isNew: {},
          Customer: {},
          StatusOption: {},
          CustomerSupportTransCate: {},
          //keyArray: {},
          isDataflow: false,
        },
    }
</script>
<style>
    .readonly{
        background-color: #fff !important;
    }
    .table th, .table td{
        border-bottom: 1px solid #c8ced3;
    }
    .modal-footer ol,.modal-footer ul,.modal-footer dl{
        margin-bottom: 0px;
    }
    #modal-form-input-task-general .modal-lg .modal-content{
        width: 1024px;
        margin: auto;
    }
    @media (max-width: 1024px){
        #modal-form-input-task-general .modal-lg {
            max-width: 100%;
        }
        #modal-form-input-task-general .modal-lg .modal-content{
             width: 90%;
             margin: auto;
         }
    }
    @media (min-width: 992px){
        #modal-form-input-task-general .modal-lg {
            max-width: 100%;
        }
    }
</style>
