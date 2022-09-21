<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Phân quyền">
    <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
    <b-modal scrollable ref="modal" id="modal-form-input-doc-general" size="lg" @hide="onHideModalDataflow($event)">
      <template slot="modal-title">
        <i class="icon-eye icon" v-if="!isForm"></i><i class="fa fa-edit" v-if="isForm"></i> {{this.title}}
      </template>
      <coa-snr-per-view v-model="value" v-if="!isForm" :isDetail="true" :per="per" :EmployeeOption="EmployeeOptionArr"></coa-snr-per-view>
      <coa-snr-per-form v-model="value" v-if="isForm" :per="per" :EmployeeOption="EmployeeOptionArr" :CoaSnrOption="CoaSnrOption" :GroupOption="GroupOption" :EmployeeLogin=" EmployeeLogin"></coa-snr-per-form>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" v-if="!isForm && (value.UserIDCreated == EmployeeLogin.UserID || value.AuthorizedPerson == EmployeeLogin.UserID)">
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
import CoaSnrPerView from "./CoaSnrPerView";
import CoaSnrPerForm from "./CoaSnrPerForm";

export default {
  name: 'coa-snr-per-modal',
  components: {CoaSnrPerForm, CoaSnrPerView},
  computed: {},
  data() {
    return {
      isForm: false,
      EmployeeOptionArr: [],
      EmployeeLogin: JSON.parse(localStorage.getItem('Employee')),
    }
  },
  created() {},
  mounted() {},
  methods: {
    fetchData() {},
    onEdit() {
      this.isForm = true;
    },
    onUpdate() {
      let self = this;
      this.$store.commit('isLoading', true);
      let UpdateApi = 'listing/api/coa-snr/update-per';

      _.forEach(self.per, function (per, key) {
        if (per.Edit && !per.EditField) {
          self.per[key].EditField = 'all';
        }
      });

      const requestData = {
        method: 'post',
        url: UpdateApi,
        data: {
          AccountID: self.value.AccountID,
          CoaSnrPer: self.per
        }
      };
      // edit user
      requestData.url = UpdateApi + '/' + self.value.AccountID;
      ApiService.setHeader();
      ApiService.customRequest(requestData).then((responses) => {
        let responsesData = responses.data;
        if (responsesData.status === 1) {
          if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
          self.$emit('changed');
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
      self.EmployeeOptionArr = [];
      _.forEach(self.EmployeeOption, function (val, key) {
        self.EmployeeOptionArr[val.id] = val.text;
      });
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
  props: ['title', 'value', 'name', 'api', 'table', 'CoaSnr', 'isDetail', 'per', 'EmployeeOption', 'CoaSnrOption', 'GroupOption'],
}
</script>
<style>
.readonly {
  background-color: #fff !important;
}
.table th, .table td {
  border-bottom: 1px solid #c8ced3;
}
.modal-footer ol, .modal-footer ul, .modal-footer dl {
  margin-bottom: 0;
}

</style>
