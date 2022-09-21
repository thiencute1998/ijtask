<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
    <b-modal scrollable ref="modal" id="modal-form-input-customer-general" size="xl" @hide="onHideModalDataflow($event)">
      <template slot="modal-title">
        <i class="icon-eye icon" v-if="!isForm"></i><i class="fa fa-edit" v-if="isForm"></i> {{this.title}}
      </template>
      <customer-general-view v-model="value" v-if="!isForm" :isDetail="true"></customer-general-view>
      <customer-general-form v-model="value" v-if="isForm"></customer-general-form>
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
  import CustomerGeneralView from "./CustomerGeneralView";
  import CustomerGeneralForm from "./CustomerGeneralForm";

  export default {
    name: 'customer-general-modal',
    components: {CustomerGeneralForm, CustomerGeneralView},
    computed: {},
    data() {
      return {
        isForm: false,
      }
    },
    created() {
    },
    mounted() {},
    methods: {
      fetchData() {},
      onEdit() {
        this.isForm = true;
      },
      onUpdate() {
        let self = this;
        if (self.value.Province) {
          self.value.ProvinceID = self.value.Province.ProvinceID;
          self.value.ProvinceName = self.value.Province.ProvinceName;
        }

        if (self.value.District) {
          self.value.DistrictID = self.value.District.DistrictID;
          self.value.DistrictName = self.value.District.DistrictName;
        }

        if (self.value.Commune) {
          self.value.CommuneID = self.value.District.CommuneID;
          self.value.CommuneName = self.value.District.CommuneName;
        }

        this.$store.commit('isLoading', true);
        let UpdateApi = 'listing/api/customer/update';
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
            ProvinceID: (this.value.Province) ? this.value.Province.ProvinceID : null,
            ProvinceName: (this.value.Province) ? this.value.Province.ProvinceName : '',
            DistrictID: (this.value.District) ? this.value.District.DistrictID : null,
            DistrictName: (this.value.District) ? this.value.District.DistrictName : '',
            CommuneID: (this.value.Commune) ? this.value.Commune.CommuneID : null,
            CommuneName: (this.value.Commune) ? this.value.Commune.CommuneName : null,
            AccessType: this.value.AccessType,
            isCustomer: (this.value.isCustomer) ? 1 : 0,
            Note: this.value.Note
          }
        };
        // edit user
        requestData.url = UpdateApi + '/' + self.value.CustomerID;

        if (this.value.CustomerCate && this.value.CustomerCate.length) {
          requestData.data.CustomerCate = this.value.CustomerCate;
        }

        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
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
      onSaveModal() {
        this.$bvToast.toast('Đã lưu ràng buộc', {
          variant: 'success',
          solid: true
        });
      },
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onHideModal() {
        this.isForm = false;
        this.$refs['modal'].hide();
      },
      onHideModalDataflow() {
        if (this.isDataflow) {
          this.$emit('onHideModalTask');
        }
      },
      onToggleModal() {
        let self = this;
        this.currentPage = 1;
        this.isForm = false;
        this.$refs['modal'].show();
        this.fetchData();
      },
      onResetModal() {
      },
    },
    watch: {
      currentPage() {
        this.fetchData();
      }
    },
    filters: {},
    props: ['title', 'value', 'name', 'api', 'table', 'isDetail', 'per'],
  }
</script>
<style>
  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }
  .modal-footer ol, .modal-footer ul, .modal-footer dl {
    margin-bottom: 0;
  }
</style>
