<template>
  <div class="table-responsive">
    <table class="not-border" v-if="!isForm">
      <thead>
      <tr class="text-left">
        <th class="pr-3" v-if="ViewPerDescription">Tên</th>
        <th class="pr-3" v-if="ViewPerDocName">Tài liệu</th>
        <th class="pr-3" v-if="ViewPerFileType">Kiểu tệp</th>
        <th class="pr-3" v-if="ViewPerFileSize">Kích thước</th>
        <th class="pr-3" v-if="ViewPerDateModified">Ngày cập nhật</th>
        <th class="pr-3" v-if="ViewPerUserModified">Người cập nhật</th>
        <th class="pr-3"></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in value">
        <td class="pr-3" v-if="ViewPerDescription">{{item.Description}}</td>
        <td class="pr-3" v-if="ViewPerDocName">{{item.DocName}}</td>
        <td class="pr-3" v-if="ViewPerFileType">{{item.FileType}}</td>
        <td class="pr-3" v-if="ViewPerFileSize">{{item.FileSize}}</td>
        <td class="pr-3" v-if="ViewPerDateModified">{{item.DateModified}}</td>
        <td class="pr-3" v-if="ViewPerUserModified">{{item.UserModified}}</td>
        <td style="text-align: center;width: auto; position: absolute; right: 15px;">
          <a @click="viewFile(key)" title="Xem" class="ij-a-icon">
            <i class="icon-eye icon" style="font-size: 15px; cursor: pointer;position: relative;"
               v-if="per['Access']"></i>
          </a>
          <a title="Tải về" class="ij-a-icon" @click.prevent="downloadFile(item.FileID, item.FileName)"
             style="color: #151b1e;">
            <i class="cui-cloud-download icons ij-icon" style="font-size: 18px; cursor: pointer;"></i>
          </a>
          <TaskFileEdit :Task="Task" v-model="value[key]" v-if="per['Edit']" :per="per"></TaskFileEdit>
          <a title="Chi tiết" class="ij-a-icon" v-if="per['Delete']">
            <i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o ij-icon"
               style="font-size: 18px; cursor: pointer;padding-left: 7px;position: relative; top: -1px;"></i>
          </a>
        </td>
      </tr>
      </tbody>
    </table>


    <b-modal ref="modal-file" id="modal-form-input-view-file" :scrollable="scrollable" no-close-on-backdrop
             :size="sizeModal">
      <template v-slot:modal-header="{ close }">
        <h5 style="margin-bottom: 0px;"><i class="icon-eye icon"></i> Tệp</h5>
        <div class="right">
          <i class="fa fa-undo" @click="rotateImg" v-if="FileType==3" title="Thu nhỏ"
             style="margin-right: 10px;cursor: pointer;"></i>
          <i class="icon-size-fullscreen icons" @click="toggleSizeModal" v-if="sizeModal=='md'" title="Phóng to"
             style="margin-right: 10px;cursor: pointer;"></i>
          <i class="icon-size-actual icons" @click="toggleSizeModal" v-if="sizeModal=='lg'" title="Thu nhỏ"
             style="margin-right: 10px;cursor: pointer;"></i>
          <i class="icon-close icons" @click="close()" style="cursor: pointer;" title="Đóng"></i>
        </div>
      </template>

      <iframe id="frame-file" :src="srcFile" v-if="FileType==1" width='100%' height='100%' frameborder="none"
              style="border: none; width: 100%; min-height: 500px;">
      </iframe>

      <embed id="em-pdf" :src="srcFile" width="100%" height="500px" v-if="FileType==2"/>

      <img id="img-shơw" :src="srcFile" style="width: 100%; height: 100%;" v-if="FileType==3" v-bind:style="rotateDeg"/>
      <template v-slot:modal-footer>
        <div class="left">
          <b-button
            variant="primary"
            size="md"
            class="float-left mr-2"
            @click="onHideModalFile()"
          >
            Đóng
          </b-button>
        </div>
      </template>

    </b-modal>

  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';
  import moment from 'moment';
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import IjcoreDatePickerAssign from "../../../../components/IjcoreDatePickerAssign";
  import IjcoreDateTimePicker from "../../../../components/IjcoreDateTimePicker";
  import IjcoreUploadFile from "../../../../components/IjcoreUploadFile";
  import IjcoreUploadMultipleFile from "../../../../components/IjcoreUploadMultipleFile";
  import TaskFileEdit from "./TaskFileEdit";

  export default {
    name: 'TaskFileContent',
    mixins: [mixinLists],
    components: {
      TaskFileEdit,
      IjcoreUploadFile,
      IjcoreUploadMultipleFile,
      IjcoreDateTimePicker,
      IjcoreDatePickerAssign,
      IjcoreModalListing
    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        listtable: [],
        tableName: '',
        search: '',
        lenghNo: 0,
        baseUrl: process.env.VUE_APP_ROOT_API,
        srcFile: '',
        FileType: 1,
        sizeModal: 'md',
        deg: 0,
        rotateDeg: 'transform: rotate(0deg);',
        scrollable: false,
        ViewPerDescription: true,
        ViewPerDocName: true,
        ViewPerFileType: true,
        ViewPerFileSize: true,
        ViewPerDateModified: true,
        ViewPerUserModified: true,
      }
    },
    created() {
    },
    mounted() {
      this.ViewPerDescription = __.perViewColumn(this.per, 'Description')
      this.ViewPerDocName = __.perViewColumn(this.per, 'DocName')
      this.ViewPerFileType = __.perViewColumn(this.per, 'FileType')
      this.ViewPerFileSize = __.perViewColumn(this.per, 'FileSize')
      this.ViewPerDateModified = __.perViewColumn(this.per, 'DateModified')
      this.ViewPerUserModified = __.perViewColumn(this.per, 'UserModified')
    },
    methods: {
      rotateImg() {
        this.deg = this.deg + 90;
        this.rotateDeg = 'transform: rotate(' + this.deg + 'deg);';
      },
      toggleSizeModal() {
        if (this.sizeModal == 'lg') {
          this.sizeModal = 'md';
        } else {
          this.sizeModal = 'lg';
        }
      },
      downloadFile(FileID, name) {
        let self = this;
        let urlApi = '/task/api/task/download-file/' + FileID;
        let requestData = {
          method: 'get',
          url: urlApi,
          data: {},
          responseType: 'blob',

        };
        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let dataResponse = response.data;
          let link = document.createElement('a')
          link.href = window.URL.createObjectURL(dataResponse)
          link.download = name
          link.click()
          self.$store.commit('isLoading', false);
        }, (error) => {
          self.$store.commit('isLoading', false);
        });
      },
      viewFile(key) {
        var type = this.value[key].FileType;
        var href = this.value[key].Link;
        var ValidDocTypes = ["pptx", "ppt", "xls", "xlsx", "doc", "docx"];
        let self = this;
        let urlApi = '/task/api/task/download-file/' + this.value[key].FileID;
        let requestData = {
          method: 'get',
          url: urlApi,
          data: {},
          responseType: 'blob',

        };
        self.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let dataResponse = response.data;
          href = window.URL.createObjectURL(dataResponse)
          this.scrollable = false;
          if (ValidDocTypes.indexOf(type) >= 0) {
            self.FileType = 1;
            self.srcFile = "https://view.officeapps.live.com/op/embed.aspx?src=" + href;
          } else {
            self.FileType = 2;
            var ValidTypesShow = ["pdf", "txt"];
            if (ValidTypesShow.indexOf(type) >= 0) {
              self.srcFile = href;
            } else {
              self.FileType = 3;
              var ValidTypesImg = ["gif", "jpeg", "jpg", "png", "ico", "psd", "ai"];
              if (ValidTypesImg.indexOf(type) >= 0) {
                self.srcFile = href;
                this.scrollable = true;
              } else {
                window.open(href, '_blank');//122
              }
            }
          }
          self.$refs['modal-file'].show();
          self.$store.commit('isLoading', false);
        }, (error) => {
          self.$store.commit('isLoading', false);
        });
      },
      changeDescription(key) {
        this.value[key].changeData = 1;
      },
      formatDate(data) {
        data = data.split(' ');
        data = data[0];
        data = data.split('-');
        let dd = data[2];
        let mm = data[1];
        let yyyy = data[0];
        data = dd + '/' + mm + '/' + yyyy;
        return data;
      },
      fetchData() {

      },
      onSaveModal() {

      },
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onHideModal() {
        this.$refs['modal'].hide();
      },
      onHideModalFile() {
        this.$refs['modal-file'].hide();
      },
      onToggleModal() {
        let self = this;
        this.currentPage = 1;
        this.$refs['modal'].show();
        this.fetchData();
      },
      onResetModal() {
      },
      clickText: function (event, key) {
        if (this.isForm) {
          event.target.hidden = true;
          event.target.nextSibling.hidden = false;
          this.value[key].addnew = true;
        }
      },
      hideInput: function (event, loop, key) {
        let element = event.target;
        if (event.target.value) {
          for (let i = 1; i <= loop; i++) {
            element = element.parentElement;
          }
          element.hidden = true;
          element.previousSibling.hidden = false;
          this.value[key].addnew = false;
        }
      },
      addLine() {
        this.value.push({
          RequestDate: __.convertDateTime(new Date()),
          RequestName: '',
          Description: '',
          addnew: true,
        });
      },
      updateHour(key) {
        if (this.value[key].StartDate && this.value[key].DueDate) {
          let self = this;
          let urlApi = '/task/api/task/get-hour';
          let requestData = {
            method: 'post',
            url: urlApi,
            data: {
              StartDate: self.value[key].StartDate,
              DueDate: self.value[key].DueDate,
              CalendarTypeID: self.Task.CalendarTypeID,
            },

          };
          this.$store.commit('isLoading', true);
          ApiService.customRequest(requestData).then((response) => {
            let dataResponse = response.data;

            if (dataResponse.status === 1) {
              self.value[key].Duration = dataResponse.data;
            }
            self.$store.commit('isLoading', false);
          }, (error) => {
            console.log(error);
            self.$store.commit('isLoading', false);
          });

          // scroll to top perfect scroll
          const container = document.querySelector('.b-table-sticky-header');
          if (container) container.scrollTop = 0;
        }
      },
      deleteLine(key) {
        let self = this;
        let urlApi = '/task/api/task/delete-file/' + self.value[key].LineID;
        let requestData = {
          method: 'post',
          url: urlApi,
          data: {},

        };
        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let dataResponse = response.data;
          if (dataResponse.status === 1) {
            self.value.splice(key, 1);
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          self.$store.commit('isLoading', false);
        });
      }
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
      table: {},
      Task: {},
      isForm: false,
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
    margin-bottom: 0px;
  }

  #modal-form-input .modal-lg .modal-content {
    width: 1024px;
    margin: auto;
  }

  @media (max-width: 1024px) {
    #modal-form-input-view-file .modal-lg, #modal-form-input-view-file .modal-xl, #modal-form-input-view-file .modal-md {
      max-width: 100%;
    }

    #modal-form-input-view-file .modal-lg .modal-content {
      width: 90%;
      margin: auto;
    }

    #modal-form-input-view-file .modal-md .modal-content {
      width: 90%;
      margin: auto;
    }
  }

  @media (min-width: 992px) {
    #modal-form-input-view-file .modal-lg, #modal-form-input-view-file .modal-xl, #modal-form-input-view-file .modal-md {
      max-width: 100%;
    }
  }

  .mx-datepicker {
    display: block !important;
  }

  .NameObject {
    width: 300px;
  }
  .NumberHour {
    width: 80px;
  }


  #modal-form-input-view-file .modal-md .modal-content {
    width: 800px !important;
    margin: auto;
  }

  #modal-form-input-view-file .modal-lg .modal-content {
    width: 100% !important;
  }
</style>
