import Swal from "sweetalert2";
import Swal from "sweetalert2";
import Swal from "sweetalert2";
import Swal from "sweetalert2";
<template>
  <div class="table-responsive">
    <table class="not-border" v-if="!isForm">
      <thead>
      <tr class="text-left">
        <th class="pr-3" v-if="ViewPerDescription">Tên</th>
        <th class="pr-3" v-if="ViewPerDocName">Tài liệu</th>
        <th class="pr-3" v-if="ViewPerVideoType">Kiểu tệp</th>
        <th class="pr-3" v-if="ViewPerVideoSize">Kích thước</th>
        <th class="pr-3" v-if="ViewPerDateModified">Ngày cập nhật</th>
        <th class="pr-3" v-if="ViewPerUserModified">Người cập nhật</th>
        <th class="pr-3"></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in value">
        <td class="pr-3" v-if="ViewPerDescription">
          <span class="text-nowrap" style="max-width: 200px; display: inline-block" :title="item.Description">{{item.Description}}</span>
        </td>
        <td class="pr-3" v-if="ViewPerDocName">
          <span class="text-nowrap" style="max-width: 200px; display: inline-block" :title="item.DocName">{{item.DocName}}</span>
        </td>
        <td class="pr-3" v-if="ViewPerVideoType">{{item.VideoType}}</td>
        <td class="pr-3" v-if="ViewPerVideoSize">{{item.VideoSize}}</td>
        <td class="pr-3" v-if="ViewPerDateModified">{{item.DateModified}}</td>
        <td class="pr-3" v-if="ViewPerUserModified">{{item.UserModified}}</td>
        <td style="text-align: center;width: auto; position: absolute; right: 15px; background: #fff">
          <a @click="viewVideo(key)" title="Xem" class="ij-a-icon">
            <i class="icon-eye icon" style="font-size: 15px; cursor: pointer;position: relative;"></i>
          </a>
          <a title="Tải về" class="ij-a-icon" @click.prevent="downloadVideo(item.FileID, item.VideoName)"
             style="color: #151b1e;">
            <i class="cui-cloud-download icons ij-icon" style="font-size: 18px; cursor: pointer;"></i>
          </a>
          <TaskVideoEdit :Task="Task" v-model="value[key]" v-if="per['Edit']" :per="per"></TaskVideoEdit>
          <a title="Chi tiết" class="ij-a-icon" v-if="per['Delete']">
            <i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o ij-icon"
               style="font-size: 18px; cursor: pointer;padding-left: 7px;position: relative; top: -1px;"></i>
          </a>
        </td>
      </tr>
      </tbody>
    </table>

    <b-modal ref="modal-video" id="modal-form-input-view-video" scrollable no-close-on-backdrop :size="sizeModal">
      <template v-slot:modal-header="{ close }">
        <h5 style="margin-bottom: 0px;"><i class="icon-eye icon"></i> Phim</h5>
        <div class="right">
          <i class="icon-size-actual icons" @click="toggleSizeModal" v-if="sizeModal === 'lg'" title="Phóng to"
             style="margin-right: 10px;cursor: pointer;"></i>
          <i class="icon-size-fullscreen icons" @click="toggleSizeModal" v-if="sizeModal === 'xl'" title="Thu nhỏ"
             style="margin-right: 10px;cursor: pointer;"></i>
          <i class="icon-close icons" @click="close()" style="cursor: pointer;" title="Đóng"></i>
        </div>
      </template>
      <div class="video-container">
        <video style="max-width: 100%;" controls>
          <source class="src-video-view" :src="srcVideo" type="video/mp4">
          <source class="src-video-view" :src="srcVideo" type="video/ogg">
          Your browser does not support the video tag.
        </video>
      </div>
      <template v-slot:modal-footer>
        <div class="left">
          <b-button
            variant="primary"
            class="float-left mr-2"
            @click="onHideModalVideo()">
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
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import IjcoreDatePickerAssign from "../../../../components/IjcoreDatePickerAssign";
  import IjcoreDateTimePicker from "../../../../components/IjcoreDateTimePicker";
  import IjcoreUploadVideo from "../../../../components/IjcoreUploadVideo";
  import IjcoreUploadMultipleVideo from "../../../../components/IjcoreUploadMultipleVideo";
  import TaskVideoEdit from "./TaskVideoEdit";

  export default {
    name: 'TaskVideoContent',
    mixins: [mixinLists],
    components: {
      TaskVideoEdit,
      IjcoreUploadVideo,
      IjcoreUploadMultipleVideo,
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
        srcVideo: '',
        VideoType: 1,
        sizeModal: 'lg',
        ViewPerDescription: true,
        ViewPerDocName: true,
        ViewPerVideoType: true,
        ViewPerVideoSize: true,
        ViewPerDateModified: true,
        ViewPerUserModified: true,
      }
    },
    created() {
    },
    mounted() {
      this.ViewPerDescription = __.perViewColumn(this.per, 'Description')
      this.ViewPerDocName = __.perViewColumn(this.per, 'DocName')
      this.ViewPerVideoType = __.perViewColumn(this.per, 'VideoType')
      this.ViewPerVideoSize = __.perViewColumn(this.per, 'VideoSize')
      this.ViewPerDateModified = __.perViewColumn(this.per, 'DateModified')
      this.ViewPerUserModified = __.perViewColumn(this.per, 'UserModified')
    },
    methods: {
      toggleSizeModal() {
        if (this.sizeModal === 'lg') {
          this.sizeModal = 'xl';
        } else {
          this.sizeModal = 'lg';
        }
      },
      downloadVideo(FileID, name) {
        let self = this;
        let urlApi = '/task/api/task/download-video/' + FileID;
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
          link.download = name;
          link.click();
          self.$store.commit('isLoading', false);
        }, (error) => {
          self.$store.commit('isLoading', false);
          Swal.fire({
            title: 'Thông báo',
            text: 'Không kết nối được với máy chủ',
            confirmButtonText: 'Đóng'
          });
        });
      },
      viewVideo(key) {
        let href = this.value[key].Link;
        let self = this;
        let urlApi = '/task/api/task/download-video/' + this.value[key].FileID;
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

          self.VideoType = 2;
          self.srcVideo = href;
          self.$refs['modal-video'].show();
          self.$store.commit('isLoading', false);
        }, (error) => {
          self.$store.commit('isLoading', false);
          Swal.fire({
            title: 'Thông báo',
            text: 'Không kết nối được với máy chủ',
            confirmButtonText: 'Đóng'
          });
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
      onHideModal() {
        this.$refs['modal'].hide();
      },
      onHideModalVideo() {
        this.$refs['modal-video'].hide();
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
            Swal.fire({
              title: 'Thông báo',
              text: 'Không kết nối được với máy chủ',
              confirmButtonText: 'Đóng'
            });
          });

          // scroll to top perfect scroll
          const container = document.querySelector('.b-table-sticky-header');
          if (container) container.scrollTop = 0;
        }
      },
      deleteLine(key) {
        let self = this;
        let urlApi = '/task/api/task/delete-video/' + self.value[key].LineID;
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
          Swal.fire({
            title: 'Thông báo',
            text: 'Không kết nối được với máy chủ',
            confirmButtonText: 'Đóng'
          });
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
  .video-container {
    position: relative;
    min-height: 500px;
  }
  .video-container video {
    position: absolute;
    right: 0;
    bottom: 0;
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    background-size: cover;
    overflow: hidden;
  }
  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }
  .modal-footer ol, .modal-footer ul, .modal-footer dl {
    margin-bottom: 0;
  }
</style>
