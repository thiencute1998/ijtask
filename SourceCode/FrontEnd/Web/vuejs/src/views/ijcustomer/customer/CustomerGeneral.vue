<template>
    <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
        <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
        <b-modal scrollable ref="modal" id="modal-form-input-task-general" size="xl" @hide="onHideModalDataflow($event)">
            <template slot="modal-title">
                <i class="icon-eye icon" v-if="!isForm"></i><i class="fa fa-edit" v-if="isForm"></i> {{this.title}}
            </template>
            <CustomerGeneralContent v-model="value" v-if="!isForm" :isDetail="true" :per="per">
            </CustomerGeneralContent>
            <CustomerGeneralForm v-model="value" v-if="isForm" :per="per">
            </CustomerGeneralForm>
            <template v-slot:modal-footer>
                <div class="w-100 left">
                    <b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" v-if="!isForm">
                        Sửa
                    </b-button>
                    <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdate()" v-if="isForm">
                        Lưu
                    </b-button>
                    <b-button variant="primary" size="md" class="float-left mr-2" @click="" v-if="isForm">
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
    import CustomerGeneralContent from "./partials/CustomerGeneralContent";
    import CustomerGeneralForm from "./partials/CustomerGeneralForm";
    import Swal from 'sweetalert2';

    const UpdateApi = 'customer/api/customer/update';

    export default {
        name: 'CustomerGeneral',
        components: {
            CustomerGeneralForm,
            CustomerGeneralContent
        },
        computed: {
            rows() {
                return this.totalRows
            }
        },
        data () {
            return {
              idParams: this.$route.params.id,
              isForm: false,
              listtable: [],
              tableName: '',
              search:'',
              lenghNo: 0,
              model: {
                  CustomerID: '',
                  CustomerNo: '',
                  CustomerName: '',
                  Address: '',
                  BillTo: '',
                  ShipTo: '',
                  TaxCode: '',
                  BankAccount: '',
                  BankName: '',
                  OfficePhone: '',
                  Fax: '',
                  Email: '',
                  Website: '',
                  ProvinceID: '',
                  ProvinceName: '',
                  DistrictID: '',
                  DistrictName: '',
                  CommuneID: '',
                  CommuneName: '',
                  Note: '',
                  IsVendor: '',
                  inactive: '',
                },
              perEdit: false,
              currentUser: JSON.parse(localStorage.getItem('Employee'))
            }
        },
        created() {
        },
        mounted() {},
        methods: {
            onEdit(){
                this.isForm = true;
            },
            onUpdate(){
                let self = this;
                const requestData = {
                    method: 'post',
                    url: UpdateApi,
                    data: {
                      CustomerNo: this.value.CustomerNo,
                      CustomerName: this.value.CustomerName,
                      Address: this.value.Address,
                      BillTo: this.value.BillTo,
                      ShipTo: this.value.ShipTo,
                      TaxCode: this.value.TaxCode,
                      BankAccount: this.value.BankAccount,
                      BankName: this.value.BankName,
                      OfficePhone: this.value.OfficePhone,
                      Fax: this.value.Fax,
                      Email: this.value.Email,
                      Website: this.value.Website,
                      ProvinceID: this.value.ProvinceID,
                      ProvinceName: this.value.ProvinceName,
                      DistrictID: this.value.DistrictID,
                      DistrictName: this.value.DistrictName,
                      CommuneID: this.value.CommuneID,
                      CommuneName: this.value.CommuneName,
                      Note: this.value.Note,
                      IsVendor: (this.value.IsVendor) ? 1 : 0,
                      Inactive: (this.value.inactive) ? 1 : 0,
                      detail: this.value.CustomerCate,
                    }
                };
                // edit user
                //requestData.data.ItemID = self.value.CustomerID;
                requestData.data.CustomerID = this.idParams;
                requestData.url = UpdateApi + '/' + this.idParams;
                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => { //console.log(responses.data);
                    let responsesData = responses.data;
                    if (responsesData.status === 1) {
                        if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
                        this.isForm = false;
                    } else {
                        let htmlErrors = __.renderErrorApiHtmlObject(responsesData.data);
                        Swal.fire(
                            'Thông báo',
                            htmlErrors,
                            'error'
                        )
                    }

                    self.$store.commit('isLoading', false);
                }, (error) => {
                    self.$store.commit('isLoading', false);
                    Swal.fire(
                        'Thông báo',
                        'Không kết nối được với máy chủ',
                        'error'
                    )
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
                // e.preventDefault();
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
              let perExist = _.find(this.per, ['EmployeeID', this.currentUser.EmployeeID]);
              if ((perExist && perExist.Edit) || this.value.AuthorizedPerson === this.currentUser.UserID) {
                this.perEdit = true;
              }

            },
            onResetModal(){},
        },
        watch: {
            currentPage() {
                this.fetchData();
            }
        },
        props: ['title', 'value', 'name', 'api', 'table', 'isDataflow', 'per'],
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
