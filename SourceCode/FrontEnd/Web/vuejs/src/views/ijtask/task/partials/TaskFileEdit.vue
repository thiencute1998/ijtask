<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-edit ij-icon" aria-hidden="true" style=""></i>
    <b-modal ref="modal" size="lg" id="modal-form-input-task-file-edit" no-close-on-backdrop>
      <template slot="modal-title">
        <i class="fa fa-edit"></i> Tệp
      </template>
      <div class="form-group row align-items-center">
        <label class="col-md-2" style="white-space: nowrap">Công việc</label>
        <label class="col-md-20 ij-data-label" style="white-space: nowrap">{{Task.TaskName}}</label>
        <div class="ij-line">
        </div>
      </div>
      <div class="form-group row  align-items-center">
        <label class="col-md-1" style="white-space: nowrap">Tên</label>
        <div class="col-md-12" v-if="perEditView(value.FileName, per, 'FileName')">
          <IjcoreUploadFileDescription v-model="value" v-if="perEditField(value.FileName, per, 'FileName')"></IjcoreUploadFileDescription>
          <input type="text" v-else disabled="true" class="form-control" :value="value.FileName"
                 placeholder=""/>
        </div>
        <div class="col-md-12" v-else>
          <input type="text" disabled class="form-control"
                 placeholder=""/>
        </div>
        <label class="col-md-2" style="white-space: nowrap">Người cập nhật</label>
        <div class="col-md-6" v-if="perEditView(value.UserModified, per, 'UserModified')">
          <input v-model="value.UserModified" class="form-control" readonly v-if="perEditField(value.UserModified, per, 'UserModified')"/>
          <input type="text" v-else disabled="true" class="form-control" :value="value.UserModified"
                 placeholder=""/>
        </div>
        <div class="col-md-6" v-else>
          <input type="text" disabled class="form-control"
                 placeholder=""/>
        </div>
      </div>
      <div class="form-group row  align-items-center">
        <label class="col-md-1" style="white-space: nowrap">Kiểu</label>
        <div class="col-md-2" v-if="perEditView(value.FileType, per, 'FileType')">
          <input v-model="value.FileType" class="form-control" readonly v-if="perEditField(value.FileType, per, 'FileType')"/>
          <input type="text" v-else disabled="true" class="form-control" :value="value.FileType"
                 placeholder=""/>
        </div>
        <div class="col-md-2" v-else>
          <input type="text" disabled class="form-control"
                 placeholder=""/>
        </div>
        <label class="col-md-2" style="white-space: nowrap">Kích thước</label>
        <div class="col-md-2" v-if="perEditView(value.FileSize, per, 'FileSize')">
          <input v-model="value.FileSize" class="form-control" readonly v-if="perEditField(value.FileSize, per, 'FileSize')"/>
          <input type="text" v-else disabled="true" class="form-control" :value="value.FileSize"
                 placeholder=""/>
        </div>
        <div class="col-md-2" v-else>
          <input type="text" disabled class="form-control"
                 placeholder=""/>
        </div>
        <label class="col-md-2" style="white-space: nowrap">Ngày cập nhật</label>
        <div class="col-md-6" v-if="perEditView(value.DateModified, per, 'DateModified')">
          <input v-model="value.DateModified" class="form-control" readonly v-if="perEditField(value.DateModified, per, 'DateModified')"/>
          <input type="text" v-else disabled="true" class="form-control" :value="value.DateModified"
                 placeholder=""/>
        </div>
        <div class="col-md-6" v-else>
          <input type="text" disabled class="form-control"
                 placeholder=""/>
        </div>
      </div>
      <div class="form-group row  align-items-center">
        <label class="col-md-1" style="white-space: nowrap">Tài liệu</label>
        <div class="col-md-12" v-if="perEditView(value.DocID, per, 'DocID')">
          <IjcoreModalListing v-model="value" :title="'tài liệu'" :api="'/listing/api/common/list'" :table="'doc'" v-if="perEditField(value.DocID, per, 'DocID')">
          </IjcoreModalListing>
          <input type="text" v-else disabled="true" class="form-control" :value="value.DocName"
                 placeholder=""/>
        </div>
        <div class="col-md-12" v-else>
          <input type="text" disabled class="form-control"
                 placeholder=""/>
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
    name: 'TaskFileEdit',
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
      perEditView(value, per, field) {
        var AccessField = ','+per['AccessField']+',';
        if(per['AccessField'] == 'all' || AccessField.includes(','+field+',')){
          return true;
        }else{
          return false;
        }
      },
      perEditField(value, per, field){
        var EditField = ','+per['EditField']+',';
        if(per['EditField'] == 'all' || EditField.includes(','+field+',')){
          return true;
        }else{
          return false;
        }
      },
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
      updateStatusDescription(value, key) {
        var result = this.TaskStatus.filter(obj => {
          return obj.value === value
        });
        this.TaskExecution.StatusDescription = result[0].text;
      },
      onUpdate() {
        let self = this;
        self.$store.commit('isLoading', true);
        let formData = new FormData();
        formData.append('LineID', self.value.LineID);
        formData.append('FileUpload', self.value.FileUpload);
        formData.append('TaskID', self.value.TaskID);
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
        }

        // send upload request
        axios.post('task/api/task/task-upload-file/' + self.Task.TaskID, formData, config)
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
      Task: {},
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
