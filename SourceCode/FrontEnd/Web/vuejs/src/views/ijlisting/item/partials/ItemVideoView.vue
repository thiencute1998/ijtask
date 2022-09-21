<template>
  <div class="table-responsive">
    <table class="not-border">
      <thead>
      <tr class="text-left">
        <th class="pr-3">Tên</th>
        <th class="pr-3">Kiểu tệp</th>
        <th class="pr-3">Kích thước</th>
        <th class="pr-3">Ngày cập nhật</th>
        <th class="pr-3">Người cập nhật</th>
        <th style="width: 100px"></th>
        <th class="pr-3"></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in value">
        <td class="pr-3">{{item.Description}}</td>
        <td class="pr-3">{{item.VideoType}}</td>
        <td class="pr-3">{{item.VideoSize}}</td>
        <td class="pr-3">{{item.DateModified}}</td>
        <td class="pr-3">{{item.UserModified}}</td>
        <td style="width: 100px"></td>
        <td style="text-align: center;width: auto; position: absolute; right: 15px;">
          <a @click="viewVideo(key)" title="Xem" class="ij-a-icon">
            <i class="icon-eye icon" style="font-size: 15px; cursor: pointer;position: relative;"></i>
          </a>
          <a title="Tải về" class="ij-a-icon" @click.prevent="downloadVideo(item.FileID, item.VideoName)"
             style="color: #151b1e;">
            <i class="cui-cloud-download icons ij-icon" style="font-size: 18px; cursor: pointer;"></i>
          </a>
          <item-video-edit :Invest="Invest" v-model="value[key]"></item-video-edit>
          <a title="Chi tiết" class="ij-a-icon">
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
          <!--<i class="icon-size-fullscreen icons" @click="toggleSizeModal" v-if="sizeModal=='lg'" title="Phóng to" style="margin-right: 10px;cursor: pointer;"></i>-->
          <!--<i class="icon-size-actual icons" @click="toggleSizeModal" v-if="sizeModal=='xl'" title="Thu nhỏ" style="margin-right: 10px;cursor: pointer;"></i>-->
          <i class="icon-close icons" @click="close()" style="cursor: pointer;" title="Đóng"></i>
        </div>
      </template>
      <video style="width: 100%;" controls>
        <source class="src-video-view" :src="srcVideo" type="video/mp4">
        <source class="src-video-view" :src="srcVideo" type="video/ogg">
        Your browser does not support the video tag.
      </video>
      <template v-slot:modal-footer>
        <div class="left">
          <b-button
            variant="primary"
            size="lg"
            class="float-left mr-2"
            @click="onHideModalVideo()"
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
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import IjcoreDatePickerAssign from "../../../../components/IjcoreDatePickerAssign";
  import IjcoreDateTimePicker from "../../../../components/IjcoreDateTimePicker";
  import IjcoreUploadVideo from "../../../../components/IjcoreUploadVideo";
  import IjcoreUploadMultipleVideo from "../../../../components/IjcoreUploadMultipleVideo";
  import ItemVideoEdit from "./ItemVideoEdit";

  export default {
    name: 'vendor-video-view',
    components: {
      ItemVideoEdit,
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
      }
    },
    created() {
    },
    mounted() {
    },
    methods: {
      toggleSizeModal() {
        if (this.sizeModal === 'xl') {
          this.sizeModal = 'lg';
        } else {
          this.sizeModal = 'xl';
        }
      },
      downloadVideo(FileID, name) {
        let self = this;
        let urlApi = '/listing/api/vendor/download-video/' + FileID;
        let requestData = {
          method: 'get',
          url: urlApi,
          data: {},
          responseType: 'blob',

        };
        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let dataResponse = response.data;
          let link = document.createElement('a');
          link.href = window.URL.createObjectURL(dataResponse);
          link.download = name;
          link.click();
          self.$store.commit('isLoading', false);
        }, (error) => {
          self.$store.commit('isLoading', false);
        });
      },
      viewVideo(key) {
        var type = this.value[key].VideoType;
        var href = this.value[key].Link;
        var ValidInvestTypes = ["pptx", "ppt", "xls", "xlsx", "doc", "docx"];
        let self = this;
        let urlApi = '/listing/api/vendor/download-video/' + this.value[key].FileID;
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
      deleteLine(key) {
        let self = this;
        let urlApi = '/listing/api/vendor/delete-video/' + self.value[key].LineID;
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
      Invest: {},
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
    margin-bottom: 0;
  }
</style>
