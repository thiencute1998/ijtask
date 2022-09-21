<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-edit ij-icon" aria-hidden="true" style=""></i>
    <b-modal ref="modal" size="lg" id="modal-form-input-task-file-edit" no-close-on-backdrop>
      <template slot="modal-title">
        <i class="fa fa-edit"></i> Tệp
      </template>
      <div class="form-group row align-items-center">
        <label class="col-md-2" style="white-space: nowrap">Khách hàng</label>
        <label class="col-md-20 ij-data-label" style="white-space: nowrap">{{Customer.CustomerName}}</label>
        <div class="ij-line"></div>
      </div>
      <div class="form-group row  align-items-center">
        <label class="col-md-1" style="white-space: nowrap">Tên</label>
        <div class="col-md-12">
          <input type="text" v-model="value.FileName" class="form-control"
                 placeholder=""/>
        </div>
        <label class="col-md-2" style="white-space: nowrap">Người cập nhật</label>
        <div class="col-md-6">
          <input type="text" v-model="value.UserModified" class="form-control"/>
        </div>
      </div>
      <div class="form-group row  align-items-center">
        <label class="col-md-1" style="white-space: nowrap">Kiểu</label>
        <div class="col-md-2">
          <input type="text" :disabled="true" class="form-control" :value="value.FileType"/>
        </div>
        <label class="col-md-2" style="white-space: nowrap">Kích thước</label>
        <div class="col-md-2">
          <input type="text" :disabled="true" class="form-control" :value="value.FileSize"/>
        </div>
        <label class="col-md-2" style="white-space: nowrap">Ngày cập nhật</label>
        <div class="col-md-6">
          <input type="text" class="form-control" v-model="value.DateModified"/>
        </div>
      </div>
      <div class="form-group row  align-items-center">
        <label class="col-md-1" style="white-space: nowrap">Tài liệu</label>
        <div class="col-md-12">
          <IjcoreModalListing v-model="value" :title="'tài liệu'" :api="'/listing/api/common/list'" :table="'doc'"></IjcoreModalListing>
        </div>
      </div>
      <template v-slot:modal-footer>
        <div class="left">
          <!--<b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" v-if="!isForm"-->
          <!--&gt;-->
          <!--Sửa-->
          <!--</b-button>-->
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
  import mixinLists from '@/mixins/lists';
  import IjcoreUploadFileDescription from "../../../../components/IjcoreUploadFileDescription";
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";

  export default {
    name: 'CustomerFileEdit',
    mixins: [mixinLists],
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
      fetchData() {},
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
      onResetModal() {},
      onEdit() {
        this.isForm = true;
      },
      updateStatusDescription(value, key) {
        let result = this.CustomerStatus.filter(obj => {
          return obj.value === value
        });
        this.CustomerExecution.StatusDescription = result[0].text;
      },
      onUpdate() {
        let self = this;
        let formData = new FormData();
        formData.append('LineID', self.value.LineID);
        formData.append('FileUpload', self.value.FileUpload);
        formData.append('CustomerID', self.value.CustomerID);
        formData.append('FileName', self.value.FileName);
        formData.append('Description', self.value.Description);
        formData.append('DocID', self.value.DocID);
        formData.append('DocNo', self.value.DocNo);
        formData.append('DocName', self.value.DocName);
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
        };

        // send upload request
        self.$store.commit('isLoading', true);
        axios.post('customer/api/customer/customer-upload-file/' + self.Customer.CustomerID, formData, config)
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
      Customer: {},
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
    margin-bottom: 0px;
  }

  .ij-line {
    height: 1px;
    width: 100%;
    margin-left: 15px;
    margin-right: 15px;
    border-bottom: 1px solid #e6e2e2;
    margin-top: 10px;
    margin-bottom: 10px;
  }

  .ij-data-label {
    font-weight: bold;
  }

</style>
