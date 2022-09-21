<template>
  <div class="component-chat-messages messages-messenger">
    <div id="chat-sidebar" tabindex="-1" role="dialog" aria-modal="false" aria-labelledby="sidebar-no-header-title"
         class="b-sidebar chat-sidebar shadow bg-light text-dark" style=""><!---->
      <div class="b-sidebar-body">
        <div class="chat-sidebar-header">
          <div class="d-flex align-content-center justify-content-between px-3 py-2 d-lg-down-none" style="height: 44px;">
            <span class="mr-2 d-flex align-items-center" style="font-weight: 500">Trò chuyện</span>
          </div>
        </div>
        <div class="chat-sidebar-body">
          <div class="chat-sidebar-footer" style="height: 40px">
            <div class="d-flex align-content-center justify-content-center px-2 py-1">
              <b-form-input v-model="model.searchContact" @input="onSearchContact" @keydown="onKeydownSearchContact" size="sm" placeholder="Tìm kiếm..." style="flex: 1 1 auto"></b-form-input>
            </div>
          </div>
          <vue-perfect-scrollbar style="height: calc(100% - 40px)" class="scroll-area" :settings="$store.state.psSettings">
            <div class="p-2" v-show="!model.searchContact">
              <div class="chat-group-container">
                <ul class="chat-group-list p-0">
                  <li class="media align-items-center pl-3 pr-1 py-1 chat-group" :class="'chat-group-' + group.GroupID" @click="onAddTabChat(group, 'messenger')" style="cursor: pointer;" v-for="(group, key) in GroupsMessenger">
                    <div class="img-block d-flex mr-3 align-self-center" v-if="group.GroupType === 1">
                      <img :src="$store.state.appRootApi + group.Avatar" class="img-avatar">
                      <span aria-label="Đang hoạt động" v-if="$store.state.chat.online[group.UserID]" class="chat-icon-active"></span>
                    </div>
                    <div class="img-block img-block-group d-flex mr-3 align-self-center" v-if="group.GroupType !== 1">
                      <div class="img-block-item" v-if="group.Avatar0">
                        <img :src="$store.state.appRootApi + group.Avatar0" class="img-avatar"/>
                      </div>
                      <div class="img-block-item" v-if="group.Avatar1">
                        <img :src="$store.state.appRootApi + group.Avatar1" class="img-avatar"/>
                      </div>
                    </div>
                    <div class="media-body text-left" :class="[(!GroupsMessenger[key].read) ? 'user-unread' : '']">
                      <h6 class="chat-user-name mt-0 mb-0">{{group.GroupName}}</h6>
                      <p class="chat-user-last-content mb-0" :class="[(!GroupsMessenger[key].read) ? 'unread' : '' , 'last-content-' + group.GroupID]" v-html="getLastMessage(group.GroupID)"></p>
                    </div>
                  </li>
                </ul>
              </div>
            </div>

            <div class="pr-2 py-2" v-show="model.searchContact">
<!--              <div class="chat-type-label px-3 my-1">-->
<!--                <span>Liên hệ</span>-->
<!--              </div>-->
              <div class="chat-group-container chat-contact" v-if="contactSearch.length">
                <ul class="chat-group-list p-0">
                  <li class="media align-items-center chat-group contact pl-3 pr-1 py-1" @click="onAddTabChat(contact, 'messenger')" v-for="(contact, key) in contactSearch" style="cursor: pointer">
                    <div class="img-block d-flex mr-3 align-self-center" v-if="contact.GroupType !== 1">
                      <div class="img-block-item">
                        <img :src="$store.state.appRootApi + contact.Avatar0" class="img-avatar"/>
                      </div>
                      <div class="img-block-item">
                        <img :src="$store.state.appRootApi + contact.Avatar1" class="img-avatar"/>
                      </div>
                    </div>

                    <div class="img-block d-flex mr-3 align-self-center" v-if="contact.GroupType === 1">
                      <img :src="$store.state.appRootApi + contact.Avatar" class="img-avatar"/>
                    </div>

                    <div class="media-body text-left">
                      <h6 class="chat-user-name mt-0 mb-0">{{contact.GroupName}}</h6>
                    </div>
                  </li>
                </ul>
              </div>
            </div>

          </vue-perfect-scrollbar>
        </div>
      </div>
    </div>
    <div id="chat-messenger">
      <div class="chat-tabs">
        <chat-tab
          v-model="tabsChat"
          :ref="'chat-tab-' + tabChat.GroupID"
          :tab-chat="tabChat"
          :current-user="CurrentUser"
          :all-users="AllUsers"
          :members-groups="MembersInGroup"
          :groups="GroupsMessenger"
          :key="tabChat.GroupID"
          :is-messenger="true"
          @on:new-message="onAddNewMessage($event, key)"
          @on:user-read="onUserReadMessage(key)"
          @on:update-group-name="onUpdateGroupName"
          @on:remove-tab-chat="onCloseMessenger"
          v-for="(tabChat, key) in tabsChat"></chat-tab>
      </div>
    </div>
  </div>
</template>
<style type="text/css">
  .chat-icon:focus {
    outline: none;
  }
  .messages-messenger {
    display: flex;
  }
  .messages-messenger #chat-sidebar {
    top: 0;
    height: calc(100vh - 50px) !important;
    left: auto;
    position: relative !important;
    box-shadow: none !important;
    flex: 0 0 auto;
  }
  .messages-messenger #chat-messenger {
    flex: 1 1 auto;
  }
  .messages-messenger .chat-tab {
    border-left: 1px solid rgba(0, 0, 0, .10);
    margin-left: 0 !important;
    margin-right: 0 !important;
  }
  .messages-messenger .chat-tabs {
    position: relative !important;
    height: 100%;
    right: auto;
  }
  .messages-messenger .chat-tab {
    height: 100%;
    width: 100%;
    box-shadow: none;
  }
  .messages-messenger #chat-sidebar .spinners {
    position: absolute;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9;
    background: #fff;
  }
  .messages-messenger #chat-sidebar.message-loaded .chat-sidebar-header {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, .10);
  }
  .messages-messenger #chat-sidebar .b-sidebar-body {
    overflow-y: hidden;
    display: flex;
    flex-direction: column;
    position: relative;
  }
  .messages-messenger .b-sidebar {
    width: 360px;
  }
  .messages-messenger .tab-body .emojioneemoji {
    min-height: 35px;
    min-width: 35px;
  }
  .messages-messenger .message-content span {
    max-width: 500px;
  }
  .messages-messenger .message-files .message-image {
    /*width: 30%;*/
    /*height: auto;*/
    overflow: hidden;
    /*max-width: 500px;*/
  }
  .messages-messenger .message-files .message-video {
    height: auto;
  }
  .messages-messenger .message-files .message-video .video-content {
    width: 100%;
  }
  .messages-messenger .chat-tab i {
    font-size: 21px;
  }
  .messages-messenger .message-typing .message-input {
    padding: .5rem 1rem !important;
  }
  .messages-messenger .chat-extension {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }
  .messages-messenger .chat-bar, .messages-messenger .chat-tabs .tab-body {
    padding: .5rem 1rem !important;
  }
  .messages-messenger .message-content .message-feedback-info {
    font-size: 12px;
  }
  .messages-messenger .tab-header-title {
    max-width: none;
  }
  .chat-sidebar-header {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, .10);
    visibility: hidden;
    opacity: 0;
  }
  .messages-messenger .message-loaded .chat-sidebar-header{
    visibility: visible;
    opacity: 1;
  }
  .messages-messenger .chat-sidebar-footer {
    border-top: 1px solid rgba(0, 0, 0, .10);
    margin-top: auto;
    background: #fff;
    visibility: hidden;
    opacity: 0;
  }
  .messages-messenger .message-loaded .chat-sidebar-footer{
    visibility: visible;
    opacity: 1;
  }
  .chat-sidebar-footer input{
    flex: 1 1 auto;
    padding: 0;
    font-size: 14px;
    border: none;
  }
  .chat-sidebar-footer input:focus {
    outline: none;
    box-shadow: none;
  }
  .chat-sidebar-body {
    overflow-y: auto;
  }
  .chat-sidebar .img-block {
    width: 32px;
    height: 32px;
    position: relative;
  }
  .chat-sidebar .img-block-item {
    position: absolute;
    width: 21px;
    height: 21px;
  }
  .chat-sidebar .img-block .img-block-item:first-child {
    right: 0;
    top: 0;
  }
  .chat-sidebar .img-block .img-block-item:last-child {
    bottom: 0;
    left: 0;
  }
  .chat-sidebar .media-body, .chat-sidebar .chat-user-last-content {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .chat-sidebar .chat-user-last-content {
    color: #737373;
  }
  .chat-sidebar .chat-user-name {
    font-weight: normal;
    color: #404040;
  }
  .chat-sidebar .chat-icon-active {
    background: rgb(66, 183, 42);
    border-radius: 50%;
    display: inline-block;
    height: 6px;
    margin-left: 4px;
    width: 6px;
  }
  .chat-sidebar .chat-type-label {
    text-align: left;
  }
  .chat-sidebar .chat-type-label span{
    color: #4a4a4a;
    font-size: 11px;
    font-weight: bold;
    overflow: hidden;
    text-overflow: ellipsis;
    text-transform: uppercase;
  }
  .chat-sidebar .media:hover, .chat-sidebar .media.active {
    background-color: #dddfe2;
    box-shadow: 1px 0 0 #eaebed inset;
  }
  .chat-more-action .btn {
    background: transparent;
    border: none;
  }
  .chat-group-action .btn:focus {
    outline: none;
    box-shadow: none;
  }
  .icon-chat-comment-plus {
    position: relative;
  }
  .icon-chat-plus {
    position: absolute;
    font-size: 10px;
  }
  .chat-tabs {
    position: fixed;
    bottom: 0;
    right: 285px; display: flex;
    flex-direction: row-reverse
  }
  .b-sidebar-body .user-unread h6,
  .b-sidebar-body .user-unread .unread {
    color: #404040;
    font-weight: bold;
  }
  .chat-icon-agree:hover svg path {
    fill: #0084ff;
  }
  .chat-contact .contact.active {
    background: #dddfe2;
  }

  /*============================= responsive ==================================*/
  @media screen and (max-width: 768px){
    .chat-messages-mobile.show-sidebar #chat-sidebar {
      width: 100%;
      display: block;
    }
    .chat-messages-mobile #chat-sidebar {
      display: none;
    }
    .chat-messages-mobile.show-sidebar #chat-messenger {
      display: none;
    }
    .chat-messages-mobile #chat-messenger {
      display: block;
      width: 100%;
    }
    .messages-messenger .tab-header-title {
      max-width: 140px;
    }
  }

</style>
<script>
  import ApiService from '@/services/api.service';
  import ChatCreateGroup from "./partials/ChatCreateGroup";
  import ChatTab from "./ChatTab";
  import mixinsSidebar from './mixins/sidebar';
  import moment from "moment";
  export default {
    name: 'chat-messages',
    mixins: [mixinsSidebar],
    data () {
      return {
        tabsChat: [],
        params: {
          GroupID: (this.$route.params.GroupID) ? this.$route.params.GroupID : null
        },
        prevRoute: null
      }
    },
    components: {
      ChatCreateGroup,
      ChatTab
    },
    beforeRouteEnter(to, from, next) {
      next(vm => {
        vm.prevRoute = from
      });
    },
    mounted() {
      let self = this;
      this.$store.commit('asideShowChat', false);
      $('body').removeClass('aside-menu-lg-show');
      this.onInitChat('messenger');
      $('.app-header .chat-icon').on('click', function () {
        if (self.prevRoute && self.prevRoute.path) {
          self.$router.back();
        } else {
          self.$router.push({
            name: 'Home'
          });
        }
      });

      $('.aside-menu .aside-tab-chat').on('click', function () {
        if (self.prevRoute && self.prevRoute.path) {
          self.$router.back();
        } else {
          self.$router.push({
            name: 'Home'
          });
        }
      });

      if (window.innerWidth < 768) {
        this.messengerMobile();
      }


      socket.on('new message', (data) => {
        let index = _.findIndex(self.LastMessageGroup, ['GroupID', Number(data.GroupID)]);
        if (index > -1) {
          self.LastMessageGroup[index].Content = data.Content;
        }else {
          self.LastMessageGroup.push(data.Content);
        }
        // messenger
        if (self.GroupsMessenger && self.GroupsMessenger.length) {
          let indexGroupMessenger = _.findIndex(self.GroupsMessenger, ['GroupID', Number(data.GroupID)]);
          if (indexGroupMessenger > -1) {
            if (self.CurrentUser.UserID !== data.UserID) {
              if (self.GroupsMessenger[indexGroupMessenger].read && $('.component-chat-messages').length) {
                self.changeNewMessage('+');
              }
              self.GroupsMessenger[indexGroupMessenger].read = false;
            }

            self.GroupsMessenger[indexGroupMessenger].ChatContentID = data.LineID;
            self.GroupsMessenger[indexGroupMessenger].userSeen = [];
          }
        }

        // set userSeen
        let indexTabChat = _.findIndex(self.tabsChat, ['GroupID', data.GroupID]);
        if (indexTabChat > -1 && data.GroupType !== 1) {
          self.tabsChat[indexTabChat].seen = false;
          self.tabsChat[indexTabChat].userSeen = [];
        }

        self.sortGroups();
        this.$forceUpdate();
      });

      socket.on('read message', (data) => {
        let indexGroupMessenger = _.findIndex(self.GroupsMessenger, ['GroupID', data.GroupID]);
        if (indexGroupMessenger > -1) {
          self.GroupsMessenger[indexGroupMessenger].read = true;
        }
      });

      socket.on('new group', (data) => {
        if (data.group) {
          data.group.read = (data.group.Created && (data.group.Created === self.CurrentUser.UserID)) ? true : false;
          data.group.seen = false;
          data.group.isNewGroup = true;
          // messenger
          if (self.GroupsMessenger && self.GroupsMessenger) {
            self.GroupsMessenger.unshift(data.group);
            self.GroupsMessenger = _.uniqBy(self.GroupsMessenger, 'GroupID');
          }

          self.contact.push({
            GroupID: data.group.GroupID,
            GroupName: data.group.GroupName,
            GroupType: data.group.GroupType,
            Avatar0: (data.group.Avatar0) ? data.group.Avatar0 : '',
            Avatar1: (data.group.Avatar1) ? data.group.Avatar1 : ''
          });
          self.contact = _.uniqBy(self.contact, 'GroupID');
          self.MembersInGroup = _.concat(self.MembersInGroup, data.members);

          if ($('.component-chat-messages').length && (data.group.Created !== self.CurrentUser.UserID)) {
            self.changeNewMessage('+');
          }

          let dataSent = [];
          dataSent.push(data.group);
          socket.emit('join room', dataSent);
        }
      });

    },
    methods: {
      onCloseMessenger() {
        this.$router.push({
          name: 'Home'
        });
      },
      messengerMobile() {
        $('main.main').css({
          height: (window.innerHeight - 50) + 'px'
        });
        $('.component-chat-messages').addClass('chat-messages-mobile show-sidebar');
      }
    },
    watch: {},
    destroyed() {
      $('.app-header .chat-icon').unbind('click');
      $('.aside-menu .aside-tab-chat').unbind('click');
      this.GroupsMessenger = [];
    }
  }
</script>
