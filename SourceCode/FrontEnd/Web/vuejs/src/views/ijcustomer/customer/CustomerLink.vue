<template>
    <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
        <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
        <b-modal scrollable ref="modal" id="modal-form-input-task-general" size="xl" @hide="onHideModalDataflow($event)">
            <template slot="modal-title">
                <i class="icon-eye icon" v-if="!isForm"></i><i class="fa fa-edit" v-if="isForm"></i> {{this.title}}
            </template>
            <CustomerLinkContent v-model="value" v-if="!isForm" :per="per" :Customer="Customer">
            </CustomerLinkContent>
            <CustomerLinkContent v-model="value" v-if="isForm" :isForm="true" :per="per" :Customer="Customer" :SysTable="SysTable">
            </CustomerLinkContent>
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
    import mixinLists from '@/mixins/lists';
    import CustomerLinkContent from "./partials/CustomerLinkContent";
    import Swal from 'sweetalert2';

    export default {
        name: 'CustomerLink',
        mixins: [mixinLists],
        components: {
            CustomerLinkContent
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
                SysTable: [],
            }
        },
        created() {
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
              this.value.push({
                LinkID: '',
                LinkNo: '',
                LinkName: '',
                LinkTable: '',
                LinkTableName: '',
                addnew: true,
              });
              this.isForm = true;
              this.fetchData();
            },
            onUpdate() {
              this.$store.commit('isLoading', true);
              let self = this;
              const requestData = {
                method: 'post',
                url: 'customer/api/customer/customer-link/' + this.idParams,
                data: {
                  CustomerLink: self.value
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
          Customer: {},
          table: {},
          isDataflow: false,
          per: {}
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
