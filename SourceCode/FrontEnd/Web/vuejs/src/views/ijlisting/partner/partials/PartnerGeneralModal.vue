<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
    <b-modal scrollable ref="modal" id="modal-form-input-partner-general" size="xl" @hide="onHideModalDataflow($event)">
      <template slot="modal-title">
        <i class="icon-eye icon" v-if="!isForm"></i><i class="fa fa-edit" v-if="isForm"></i> {{this.title}}
      </template>
      <partner-general-view v-model="value" v-if="!isForm" :isDetail="true"></partner-general-view>
      <partner-general-form v-model="value" v-if="isForm"></partner-general-form>
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
  import PartnerGeneralView from "./PartnerGeneralView";
  import PartnerGeneralForm from "./PartnerGeneralForm";

  export default {
    name: 'partner-general-modal',
    components: {PartnerGeneralForm, PartnerGeneralView},
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
          self.value.CommuneID = self.value.Commune.CommuneID;
          self.value.CommuneName = self.value.Commune.CommuneName;
        }
        if(!self.value.FirstName){
          self.value.FirstName = '';
        }
        if(!self.value.MiddleName){
          self.value.MiddleName = '';
        }
        if(!self.value.LastName){
          self.value.LastName = '';
        }
        self.value.FullName =self.value.FirstName +' '+ self.value.MiddleName + ' ' + self.value.LastName;

        this.$store.commit('isLoading', true);
        let UpdateApi = 'listing/api/partner/update';
        const requestData = {
          method: 'post',
          url: UpdateApi,
          data: {
            PartnerNo: this.value.PartnerNo,
            PartnerName: this.value.PartnerName,
            PartnerAddress: this.value.PartnerAddress,
            FirstName: this.value.FirstName,
            MiddleName: this.value.MiddleName,
            LastName: this.value.LastName,
            FullName: this.value.FullName,
            BirthDay: this.value.BirthDay,
            PartnerIdNo: this.value.PartnerIdNo,
            PartnerIdIssuedDate: this.value.PartnerIdIssuedDate,
            ProvinceID: this.value.ProvinceID,
            ProvinceName: this.value.ProvinceName,
            DistrictID: this.value.DistrictID,
            DistrictName: this.value.DistrictName,
            CommuneID: this.value.CommuneID,
            CommuneName: this.value.CommuneName,
            Nationality: this.value.Nationality,
            NativeCountry: this.value.NativeCountry,
            PermanceAddress: this.value.PermanceAddress,
            ResidenceAddress: this.value.ResidenceAddress,
            Tel: this.value.Tel,
            Email: this.value.Email,
            Note: this.value.Note,
            AccessType: this.value.AccessType,
          }
        };
        // edit user
        requestData.url = UpdateApi + '/' + self.value.PartnerID;

        if (this.value.PartnerCate && this.value.PartnerCate.length) {
          requestData.data.PartnerCate = this.value.PartnerCate;
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
