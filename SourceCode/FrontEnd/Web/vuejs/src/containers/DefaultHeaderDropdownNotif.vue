<template>
  <AppHeaderDropdown right no-caret class="dropdown-notice">
    <template slot="header">
      <div title="Thông báo">
        <i class="icon-bell"></i>
        <b-badge pill variant="danger" v-if="$store.state.notification.total > 0">{{$store.state.notification.total}}</b-badge>
      </div>
    </template>
    <template slot="dropdown">
      <b-dropdown-header tag="div" class="dropdown-menu-lg">
        <div class="d-flex align-items-center justify-content-between">
          <div><strong>Thông báo</strong></div>
          <div class="dropdown b-dropdown notices-more-action btn-group" v-click-outside="hideMoreActionNotices">
            <button aria-haspopup="true" aria-expanded="false" type="button"
                    @click="stage.showNoticesActionMore = !stage.showNoticesActionMore"
                    class="btn dropdown-toggle btn-secondary dropdown-toggle-no-caret">
              <span><i class="fa fa-ellipsis-h"></i></span>
            </button>
            <ul role="menu" tabindex="-1" class="dropdown-menu dropdown-menu-right" :class="[(stage.showNoticesActionMore) ? 'show' : '']" style="" x-out-of-boundaries="">
<!--              <li role="presentation"><a role="menuitem" href="#" target="_self" class="dropdown-item" v-if="$store.state.notification.notice.length" @click="changeStatusAllNotice($event, 1)">Đanh dấu tất cả là đã đọc</a></li>-->
              <li role="presentation"><a role="menuitem" href="#" target="_self" class="dropdown-item" v-if="$store.state.notification.notice.length" @click="changeStatusAllNotice($event, 2)">Đánh dấu tất cả là không đọc</a></li>
              <li role="presentation"><a role="menuitem" href="#" target="_self" class="dropdown-item" @click="openListNotification">Xem tất cả thông báo</a></li>
<!--              <li role="presentation"><a role="menuitem" href="#" target="_self" class="dropdown-item disabled">Cài đặt thông báo</a></li>-->
            </ul>
          </div>

        </div>
      </b-dropdown-header>
      <div class="notices" @scroll="onScrollNotices">
        <b-dropdown-item @click="onClickNotice(notice)" v-for="(notice, key) in $store.state.notification.notice">
          <div class="media notice">
            <div class="d-flex mr-3 align-self-start img-block" style="position: relative">
              <img
                :src="$store.state.appRootApi + notice.Avata"
                alt="placeholder" width="40" height="40" class="img-avatar">
              <div class="notice-icon-type-action" v-if="notice.TypeCategory === 1">
                <i class="fa fa-tasks notice-icon-parent"></i>
                <i class="fa fa-comment notice-icon-child" v-if="notice.TypeAction === 1"></i>
                <i class="fa fa-plus notice-icon-child" v-if="notice.TypeAction === 2"></i>
                <i class="fa fa-edit notice-icon-child" v-if="notice.TypeAction === 3"></i>
                <i class="fa fa-check-circle notice-icon-child" v-if="notice.TypeAction === 6"></i>
              </div>
            </div>
            <div class="media-body text-left">
              <p class="m-0" v-html="notice.Description"></p>
              <p class="m-0 text-muted" style="font-size: .75rem">{{notice.CreateDate | convertTimeToHMTime}}</p>
            </div>
          </div>
          <div class="dropdown b-dropdown notice-more-action btn-group">
            <button aria-haspopup="true" aria-expanded="false" type="button"
                    @click="toggleMoreActionNotice($event, key)"
                    class="btn dropdown-toggle btn-secondary dropdown-toggle-no-caret">
              <span><i class="fa fa-ellipsis-h m-0"></i></span>
            </button>
            <ul role="menu" tabindex="-1" class="dropdown-menu dropdown-menu-right" :class="[($store.state.notification.notice[key].showAction) ? 'show' : '']" style="" x-out-of-boundaries="">
              <li role="presentation"><a role="menuitem" href="#" target="_self" class="dropdown-item" @click="changeStatusNotice($event, key, 1)">Đanh dấu là đã đọc</a></li>
              <li role="presentation"><a role="menuitem" href="#" target="_self" class="dropdown-item" @click="changeStatusNotice($event, key, 2)">Đánh dấu là không đọc</a></li>
            </ul>
          </div>
        </b-dropdown-item>
        <p class="text-center mt-3" v-if="!$store.state.notification.notice.length">Không có thông báo mới</p>
      </div>
    </template>
  </AppHeaderDropdown>
</template>
<style type="text/css">
  .dropdown-notice .dropdown-item {
    white-space: normal;
  }
  .dropdown-notice .notices {
    height: 320px;
    overflow: hidden;
    overflow-y: auto;
  }
  .dropdown-notice .notice .notice-username,
  .dropdown-notice .notice b{
    font-weight: 500;
    color: #050505;
  }
  .dropdown-notice .notice-all {
    border-top: 1px solid #c8ced3;
  }
  .dropdown-notice .dropdown-menu-lg {
    width: 100%;
  }
  .dropdown-notice .dropdown-menu {
    min-width: 400px;
  }
  .dropdown-item .notice-icon-type-action {
    position: absolute;
    bottom: -5px;
    right: -5px;
    font-size: 18px;
    margin: 0;
  }

  .dropdown-item .notice-icon-type-action i {
    margin: 0;
  }
  .dropdown-item .notice-icon-type-action i.notice-icon-parent {
    color: #00a2e8;
  }
  .dropdown-item .notice-icon-type-action i.notice-icon-child {
    position: absolute;
    right: -10px;
    top: -5px;
    color: #487c92;
  }

  .dropdown-notice .unread .dropdown-item,
  .dropdown-notice .dropdown-item.active, .dropdown-notice .dropdown-item:active{
    background-color: #f0f3f5;
    color: #0b0e0f;
  }
  .dropdown-notice .notice-more-action {
    display: none;
    position: absolute;
    top: 50%;
    right: 5px;
    transform: translateY(-50%);
    z-index: 9;
  }
  .dropdown-notice .notice-more-action .dropdown-menu-right,
  .dropdown-notice .notices-more-action .dropdown-menu-right{
    right: 0;
    left: auto;
  }
  .dropdown-notice .notice-more-action .dropdown-menu-right {
    min-width: 185px;
  }
  .dropdown-notice .notices-more-action .dropdown-menu-right {
    min-width: 220px;
  }
  .dropdown-notice .notice-more-action button,
  .dropdown-notice .notices-more-action button{
    background: #fff;
    border-radius: 50%;
    height: 24px;
    width: 24px;
    padding: 0;
  }
  .dropdown-notice .notices-more-action button {
    background: transparent;
    border-color: transparent;
  }
  .dropdown-notice .notices-more-action button:hover,
  .dropdown-notice .notices-more-action button:focus,
  .dropdown-notice .notices-more-action button:active{
    background: #fff;
    box-shadow: 0 0 0 0.2rem rgba(173, 179, 184, 0.5);
  }

  .dropdown-item:hover .notice-more-action {
    display: block;
  }

</style>
<script>
import ApiService from '@/services/api.service';
import { HeaderDropdown as AppHeaderDropdown } from '@coreui/vue'
import ClickOutside from 'vue-click-outside'
export default {
  name: 'DefaultHeaderDropdownNotif',
  components: {
    AppHeaderDropdown
  },
  data: () => {
    return {
      perPage: 30,
      page: 1,
      lastPage: 1,
      itemsCount: 5,
      stage: {
        showNoticesActionMore: false,
      }
    }
  },
  mounted() {
    let self = this;
    this.fetchData();

    // socket
    socket.on('notify', (data) => {
      let audio = document.getElementById('notice-audio-notification');
      audio.play();
      data.showAction = false;
      self.$store.commit('addNotification', data);
    });
  },
  methods: {
    fetchData() {
      let self = this;
      let requestData = {
        method: 'post',
        url: 'extensions/api/notice',
        data: {
          per_page: this.perPage,
          page: this.page,
          Status: 0
        },
      };

      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;
        if (responseData.status === 1) {

          self.perPage = responseData.data.per_page;
          self.lastPage = responseData.data.last_page;
          self.page = responseData.data.current_page;

          _.forEach(responseData.data.data, function (notice, key) {
            responseData.data.data[key].showAction = false;
          });

          let notice = _.concat(self.$store.state.notification.notice, responseData.data.data);

          self.$store.commit('notification', {
            notice: notice,
            total: responseData.data.total
          });
        }
      }, (error) => {
        console.log(error);
      });
    },
    onClickNotice(notice) {
      let indexNotice = _.findIndex(this.$store.state.notification.notice, ['NotificationID', notice.NotificationID]);
      if (indexNotice > -1) {
        this.changeStatusNotice(null, indexNotice, 1);
      }
      if (notice.TypeCategory === 1) {
        this.$store.commit('notification', {
          TypeCategory: notice.TypeCategory,
          TypeAction: notice.TypeAction,
          reload: !this.$store.state.notification.reload
        });
        if (this.$store.state.notification.CategoryID !== notice.CategoryID) {
          this.$router.push({
            name: 'task-task-view',
            params: {id: notice.CategoryID, TypeAction: notice.TypeAction}
          });
        } else {

        }
        this.$store.commit('notification', {CategoryID: notice.CategoryID});
      }

      if (notice.TypeCategory === 2) {
        this.$router.push({
          path: notice.Link,
          query: {
            notice: notice
          }
        });
      }

    },
    onScrollNotices(e){
      if($(e.target).scrollTop() + $(e.target).innerHeight() >= $(e.target)[0].scrollHeight) {
        if (this.page < this.lastPage) {
          this.page += 1;
          this.fetchData();
        }
      }
    },
    hideMoreActionNotices() {
      this.stage.showNoticesActionMore = false;
    },
    hideMoreActionNotice(key){
      this.$store.commit('hideActionNotification', key);
    },
    toggleMoreActionNotice(e, key) {
      e.preventDefault();
      e.stopPropagation();
      this.$store.commit('toggleActionNotification', key);
    },
    changeStatusNotice(e, key, Status){
      if (e) {
        e.preventDefault();
        e.stopPropagation();
      }

      let self = this;
      let requestData = {
        method: 'post',
        url: 'extensions/api/notice/update-status-notice',
        data: {
          NotificationID: this.$store.state.notification.notice[key].NotificationID,
          Status: Status
        },
      };

      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;
        if (responseData.status === 1) {
          self.$store.commit('removeNotification', key);
        }
      }, (error) => {
        console.log(error);
      });
    },

    changeStatusAllNotice(e, Status){
      if (e) {
        e.preventDefault();
        e.stopPropagation();
      }

      let self = this;
      let requestData = {
        method: 'post',
        url: 'extensions/api/notice/update-status-notice',
        data: {
          TypeUpdate: 'all',
          Status: Status
        },
      };

      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;
        if (responseData.status === 1) {
          self.$store.commit('notification', {notice: [], total: 0});
          self.stage.showNoticesActionMore = false;
          this.$bvToast.toast('Cập nhật thành công', {
            title: 'Thông báo',
            variant: 'success',
            solid: true
          });
        } else {
          this.$bvToast.toast('Cập nhật thất bại', {
            title: 'Thông báo',
            variant: 'warning',
            solid: true
          });
        }
      }, (error) => {
        this.$bvToast.toast('Cập nhật thất bại', {
          title: 'Thông báo',
          variant: 'warning',
          solid: true
        });
        console.log(error);
      });
    },
    openListNotification() {
      if (!$('.component-notification').length) {
        this.$router.push({
          name: 'apps-notification'
        });
        $('body').trigger('click');
      }
      this.stage.showNoticesActionMore = false;
    }

  },
  // do not forget this section
  directives: {
    ClickOutside
  }
}
</script>
