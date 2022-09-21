<template>
    <div>
      <div class="form-group row align-items-center">
        <div class="col-lg-2 col-md-2 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tên</div>
        <div class="col-lg-16 col-md-22 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 app-object-name">
          {{model.CustomerName}}
        </div>
        <div class=" col-md-2 col-sm-2">Mã số</div>
        <div class=" col-md-4 col-sm-8">
          {{model.CustomerNo}}
        </div>

      </div>

      <div class="form-group row align-items-center">
        <label class="col-md-2 col-sm-4 m-0" >ĐCGD</label>
        <div class="col-md-22 col-sm-20">
          {{model.Address}}
        </div>
      </div>
      <div class="form-group row align-items-center" v-if="isDetail">
        <label class="col-md-2 col-sm-4 m-0" >ĐCNHĐ</label>
        <div class="col-md-22 col-sm-20">
          {{model.BillTo}}
        </div>
      </div>
      <div class="form-group row align-items-center" v-if="isDetail">
        <label class="col-md-2 col-sm-4 m-0" >ĐCNH </label>
        <div class="col-md-22 col-sm-20">
          {{model.ShipTo}}
        </div>
      </div>

      <div class="form-group row align-items-center" v-if="isDetail">
        <label class="col-md-2 col-sm-4 m-0" >MST</label>
        <div class="col-md-6 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
          {{model.TaxCode}}
        </div>

        <label class="col-md-2 col-sm-4 m-0" >TKNH</label>
        <div class="col-md-4 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
          {{model.BankAccount}}
        </div>

        <label class="col-md-2 col-sm-4 m-0">ĐCNH</label>
        <div class="col-md-8 col-sm-20">
          {{model.BankName}}
        </div>
      </div>

      <div class="form-group row align-items-center">
        <label class="col-md-2 col-sm-4 m-0" >ĐTCQ</label>
        <div class="col-md-6 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
          {{model.OfficePhone}}
        </div>

        <label class="col-md-2 col-sm-4 m-0" >Số Fax</label>
        <div class="col-md-4 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
          {{model.Fax}}
        </div>

        <label class="col-md-2 col-sm-4 m-0">Email</label>
        <div class="col-md-8 col-sm-20">
          {{model.Email}}
        </div>
      </div>

      <div class="form-group row align-items-center" v-if="isDetail">
        <label class="col-md-2 col-sm-4 m-0" >Website</label>
        <div class="col-md-6 col-sm-20">
          {{model.Website}}
        </div>
      </div>
      <div class="form-group row align-items-center" v-if="isDetail">
        <label class="col-md-2 col-sm-4 m-0" >Tỉnh</label>
        <div class="col-md-6 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
          {{model.ProvinceName}}
        </div>

        <label class="col-md-2 col-sm-4 m-0" >Huyện</label>
        <div class="col-md-6 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
          {{model.DistrictName}}
        </div>

        <label class="col-md-2 col-sm-4 m-0">Xã</label>
        <div class="col-md-6 col-sm-20">
          {{model.CommuneName}}
        </div>
      </div>
      <div class="form-group row" v-if="isDetail">
        <label class="col-md-2 col-sm-4 m-0" >Ghi chú</label>
        <div class="col-md-22 col-sm-20">
          {{model.Note}}
        </div>
      </div>
      <div class="form-group row align-items-center" v-if="isDetail">
        <label class="col-md-2 col-sm-4 m-0" >Là NCC</label>
        <div class="col-md-6 col-sm-20">
          <b-form-checkbox
            type="check" v-model="model.IsVendor">
          </b-form-checkbox>
        </div>
      </div>
      <div class="form-group row align-items-center" v-if="isDetail">
        <label class="col-md-2 col-sm-4 m-0" >Loại KH</label>
        <div class="col-md-2 col-sm-20">
          <i @click="AddCustomerCate()"
          class="fa fa-external-link " title="Loại khách hàng"
          style="font-size: 18px; cursor: pointer; padding-right: 5px;"></i>
        </div>
        <div class="col-md-20">
          <span v-for="(field, key) in model.CustomerCate">{{model.arrCustomerCateList[field.CateID]}}: {{field.Description}}, </span>
        </div>
      </div>

      <!-- Popup Add CustomerCate -->
      <b-modal ref="CustomerCate" id="modal-form-input-task-general1" size="lg"
               title="Loại khách hàng">
        <div class="main-body main-body-view-action">
          <table class="table b-table table-sm table-bordered table-editable">
            <thead>
            <tr>
              <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Loại khách hàng  </th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Giá trị</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(field, key) in model.CustomerCate" >
              <td class="has-padding">
                {{model.arrCustomerCateList[field.CateID]}}
              </td>
              <td class="has-padding">
                {{field.Description}}
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <template v-slot:modal-footer>
          <div class="w-100 left">
            <b-button variant="primary" size="md" class="float-left mr-2" @click="HideCustomerCate()">
              Đóng
            </b-button>
          </div>
        </template>
      </b-modal>

    </div>

</template>

<script>
    import ApiService from '@/services/api.service';

    const ListRouter = 'customer-customer';
    const EditRouter = 'customer-customer-edit';
    const CreateRouter = 'customer-customer-create';
    const DetailApi = 'customer/api/customer/view';
    const ListApi = 'customer/api/customer';

    export default {
        name: 'CustomerGeneralContent',
        components: {
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
            model: {
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
              CustomerCate: [],
              arrCustomerCateList: [],
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
              let self = this;
              let urlApi = '';
              let requestData = {
                method: 'get',
              };
              // Api edit user
              if(this.idParams){
                urlApi = DetailApi + '/' + this.idParams;
                let data = {
                  id: this.idParams
                };
                requestData.data = data;
              }
              requestData.url = urlApi;
              this.$store.commit('isLoading', true);
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                self.defaultModel = responsesData;
                if (responsesData.status === 1) {
                  self.model.CustomerNo = responsesData.data.CustomerNo;
                  self.model.CustomerName = responsesData.data.CustomerName;
                  self.model.Address = responsesData.data.Address;
                  self.model.BillTo = responsesData.data.BillTo;
                  self.model.ShipTo = responsesData.data.ShipTo;
                  self.model.TaxCode = responsesData.data.TaxCode;
                  self.model.BankAccount = responsesData.data.BankAccount;
                  self.model.BankName = responsesData.data.BankName;
                  self.model.OfficePhone = responsesData.data.OfficePhone;
                  self.model.Fax = responsesData.data.Fax;
                  self.model.Email = responsesData.data.Email;
                  self.model.Website = responsesData.data.Website;
                  self.model.ProvinceID = responsesData.data.ProvinceID;
                  self.model.ProvinceName = responsesData.data.ProvinceName;
                  self.model.DistrictID = responsesData.data.DistrictID;
                  self.model.DistrictName = responsesData.data.DistrictName;
                  self.model.CommuneID = responsesData.data.CommuneID;
                  self.model.CommuneName = responsesData.data.CommuneName;
                  self.model.Note = responsesData.data.Note;
                  self.model.IsVendor = (responsesData.data.IsVendor) ? true : false;
                  self.model.inactive = (responsesData.data.Inactive) ? true : false;

                  self.model.arrCustomerCateList = responsesData.data.arrCustomerCateList;
                  self.model.CustomerCate = responsesData.data.CustomerCate;
                }

                self.$store.commit('isLoading', false);
              }, (error) => {
                console.log(error);
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
            AddCustomerCate() {
              this.$forceUpdate();
              this.$refs['CustomerCate'].show();
            },
            HideCustomerCate() {
              this.isForm = false;
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
        props: ['isDetail', 'per'],
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
