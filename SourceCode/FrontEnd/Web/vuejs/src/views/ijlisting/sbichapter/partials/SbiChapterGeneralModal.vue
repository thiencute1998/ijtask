<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
    <b-modal scrollable ref="modal" id="modal-form-input-sbi-chapter-general" size="xl" @hide="onHideModalDataflow($event)">
      <template slot="modal-title">
        <i class="icon-eye icon" v-if="!isForm"></i><i class="fa fa-edit" v-if="isForm"></i> {{this.title}}
      </template>
      <sbi-chapter-general-view v-model="value" v-if="!isForm" :isDetail="true"></sbi-chapter-general-view>
      <sbi-chapter-general-form v-model="value" v-if="isForm"></sbi-chapter-general-form>
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
  import SbiChapterGeneralView from "./SbiChapterGeneralView";
  import SbiChapterGeneralForm from "./SbiChapterGeneralForm";

  export default {
    name: 'sbi-chapter-general-modal',
    components: {SbiChapterGeneralForm, SbiChapterGeneralView},
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
        if (self.value.Uom) {
          self.value.UomID = self.value.Uom.UomID;
          self.value.UomName = self.value.Uom.UomName;
        }

        this.$store.commit('isLoading', true);
        let UpdateApi = 'listing/api/sbi-chapter/update';
        const requestData = {
          method: 'post',
          url: UpdateApi,
          data: {
            SbiChapterNo: this.value.SbiChapterNo,
            SbiChapterName: this.value.SbiChapterName,
            Note: this.value.Note,
            AccessType: this.value.AccessType,
            Serialnumber: this.value.Serialnumber
          }
        };
        // edit user
        requestData.url = UpdateApi + '/' + self.value.SbiChapterID;

        if (this.value.SbiChapterCate && this.value.SbiChapterCate.length) {
          requestData.data.SbiChapterCate = this.value.SbiChapterCate;
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
