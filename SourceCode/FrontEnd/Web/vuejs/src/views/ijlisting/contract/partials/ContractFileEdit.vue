<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-edit ij-icon" aria-hidden="true" style=""></i>
    <b-modal ref="modal" id="modal-form-input-task-file-edit" size="lg" no-close-on-backdrop>
      <template slot="modal-title">
        <i class="fa fa-edit"></i> Tệp: {{Contract.ContractName}}
      </template>
      <div class="form-group row  align-items-center mt-2">
        <label class="col-md-2" style="white-space: nowrap">Tên</label>
        <div class="col-md-12">
          <IjcoreUploadFileDescription v-model="value"></IjcoreUploadFileDescription>
        </div>
        <label class="col-md-4" style="white-space: nowrap">Người cập nhật</label>
        <div class="col-md-6">
          <input v-model="value.UserModified" class="form-control" readonly />
        </div>
      </div>
      <div class="form-group row  align-items-center mb-2">
        <label class="col-md-2" style="white-space: nowrap">Kiểu</label>
        <div class="col-md-4">
          <input v-model="value.FileType" class="form-control" readonly/>
        </div>
        <label class="col-md-4" style="white-space: nowrap">Kích thước</label>
        <div class="col-md-4">
          <input v-model="value.FileSize" class="form-control" readonly/>
        </div>
        <label class="col-md-4" style="white-space: nowrap">Ngày cập nhật</label>
        <div class="col-md-6">
          <input v-model="value.DateModified" class="form-control" readonly/>
        </div>
      </div>
      <template v-slot:modal-footer>
        <div class="left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdate()">
            Lưu
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModal()">
            Hủy
          </b-button>
          <b-button
            variant="primary"
            size="md"
            class="float-left mr-2"
            @click="onHideModal()"
          >
            Đóng
          </b-button>
        </div>
      </template>

    </b-modal>
  </a>

</template>

<script>
import ApiService from '@/services/api.service';
import IjcoreUploadFileDescription from "../../../../components/IjcoreUploadFileDescription";
import IjcoreModalListing from "../../../../components/IjcoreModalListing";

export default {
  name: 'contract-file-edit',
  components: {
    IjcoreModalListing,
    IjcoreUploadFileDescription
  },
  computed: {
    rows() {
      return this.totalRows
    },
  },
  data() {
    return {
      isForm: false,
      listtable: [],
      tableName: '',
      search: '',
      lenghNo: 0,
    }
  },
  created() {
  },
  mounted() {
  },
  methods: {
    fetchData() {

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
      this.$refs['modal'].hide();
      this.isForm = false;
    },
    onToggleModal() {
      this.$refs['modal'].show();
      this.fetchData();
    },
    onResetModal() {
    },
    onEdit() {
      this.isForm = true;
    },
    onUpdate() {
      let self = this;
      self.$store.commit('isLoading', true);
      let formData = new FormData();
      formData.append('LineID', self.value.LineID);
      formData.append('FileUpload', self.value.FileUpload);
      formData.append('ContractID', self.value.ContractID);
      formData.append('FileName', self.value.FileName);
      formData.append('Description', self.value.Description);
      formData.append('FileType', self.value.FileType);
      formData.append('FileSize', self.value.FileSize);
      formData.append('DateModified', self.value.DateModified);
      formData.append('UserModified', self.value.UserModified);
      formData.append('changeFile', self.value.changeFile);
      formData.append('changeData', self.value.changeData);

      let currentObj = this;
      const config = {
        headers: {
          'content-type': 'multipart/form-data',
        }
      }

      // send upload request
      axios.post('listing/api/contract/contract-upload-file/' + self.Contract.ContractID, formData, config)
        .then(function (response) {
          currentObj.success = response.data.success;
          currentObj.filename = "";
          let dataR = response.data.data;
          self.value.FileID = dataR.FileID;
          self.value.FileUpload = null;
          self.value.changeFile = 0;
          self.$store.commit('isLoading', false);
        })
        .catch(function (error) {
          // currentObj.output = error;
          console.log(error);
        });
    }
  },
  watch: {},
  props: {
    value: {},
    Contract: {},
    per: {}
  },
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
.align-items-center label {
  margin-bottom: 0;
}

</style>
