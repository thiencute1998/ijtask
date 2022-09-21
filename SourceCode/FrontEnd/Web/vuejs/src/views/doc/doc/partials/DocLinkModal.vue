import Swal from "sweetalert2";
<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
    <b-modal scrollable ref="modal" id="modal-form-input-doc-general" size="xl" @hide="onHideModalDataflow($event)">
      <template slot="modal-title">
        <i class="icon-eye icon" v-if="!isForm"></i><i class="fa fa-edit" v-if="isForm"></i> {{this.title}}
      </template>
      <DocLinkView v-model="value" v-if="!isForm">
      </DocLinkView>

      <DocLinkForm v-model="value" v-if="isForm" :sys-table="SysTable">
      </DocLinkForm>
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
  import DocGeneralView from "./DocGeneralView";
  import DocGeneralForm from "./DocGeneralForm";
  import DocLinkView from "./DocLinkView";
  import DocLinkForm from "./DocLinkForm";

  export default {
    name: 'DocLinkModal',
    mixins: [mixinLists],
    components: {DocLinkForm, DocLinkView, DocGeneralForm, DocGeneralView},
    computed: {},
    data() {
      return {
        isForm: false,
        SysTable: [],
      }
    },
    created() {
    },
    mounted() {
    },
    methods: {
      fetchData() {
        let self = this;
        let requestData = {
          method: 'get',
          data: {}
        };
        requestData.url = '/task/api/task/get-table';
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
        });

      },
      onEdit() {
        this.isForm = true;
        this.fetchData();
      },
      onUpdate() {
        let self = this;
        this.$store.commit('isLoading', true);
        let UpdateApi = 'doc/api/doc/update-doc-link';
        const requestData = {
          method: 'post',
          url: UpdateApi,
          data: {
            DocLink: self.value
          }
        };
        // edit user
        requestData.url = UpdateApi + '/' + self.Doc.DocID;
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
    props: ['title', 'value', 'name', 'api', 'table', 'Doc'],
  }
</script>
<style>
  .mr-bottom-3 {
    margin-bottom: 3px !important;
  }

  .readonly {
    background-color: #fff !important;
  }

  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }

  .modal-footer ol, .modal-footer ul, .modal-footer dl {
    margin-bottom: 0px;
  }

  #modal-form-input-doc-general-content .modal-lg .modal-content {
    width: 1024px;
    margin: auto;
  }

  @media (max-width: 1024px) {
    #modal-form-input-doc-general-content .modal-lg {
      max-width: 100%;
    }

    #modal-form-input-doc-general-content .modal-lg .modal-content, #modal-form-input-doc-general-content .modal-xl .modal-content {
      width: 90%;
      margin: auto;
    }
  }

  #modal-form-input-doc-general .modal-lg .modal-content, #modal-form-input-doc-general .modal-xl .modal-content {
    width: 1024px;
    margin: auto;
  }

  @media (max-width: 1024px) {
    #modal-form-input-doc-general .modal-lg, #modal-form-input-doc-general .modal-xl {
      max-width: 100%;
    }

    #modal-form-input-doc-general .modal-lg .modal-content, #modal-form-input-doc-general .modal-xl .modal-content {
      width: 90%;
      margin: auto;
    }
  }

  @media (min-width: 992px) {
    #modal-form-input-doc-general-content .modal-lg, #modal-form-input-doc-general .modal-xl {
      max-width: 100%;
    }

    #modal-form-input-doc-general .modal-lg, #modal-form-input-doc-general .modal-xl{
      width: 962px !important;
    }
  }
</style>
