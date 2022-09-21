<template>
  <div class="component-chat-sidebar" style="height: 100%;">
    <div class="chat-sidebar" id="chat-sidebar" style="height: 100%;">
      <div class="chat-sidebar-body">
        <div class="scroll-area" ref="chat-sidebar-scroll-area">
          <div class="pr-2" v-if="!model.searchContact">
              <div class="chat-group" v-if="Groups.length">
                <ul class="chat-group-list p-0">
                  <li class="media align-items-center pl-3 pr-2 py-1" @click="onAddTabChat(group, 'group')" v-for="(group, key) in Groups" style="cursor: pointer">
                    <div class="img-block d-flex mr-3 align-self-center" v-if="group.GroupType === 1">
                      <img :id="'sidebar-avatar-' + group.UserID" :src="$store.state.appRootApi + group.Avatar" class="img-avatar">
                      <b-popover
                        :target="'sidebar-avatar-' + group.UserID"
                        placement="top"
                        :delay="{show: 600, hide: 100}"
                        :title="group.GroupName"
                        triggers="hover focus">
                        <a :href="$store.state.appRootApi + group.Avatar" target="_blank">
                          <img :src="$store.state.appRootApi + group.Avatar" style="width: 100px; height: 100px" class="img-avatar"/>
                        </a>
                      </b-popover>
                    </div>
                    <div class="img-block img-block-group d-flex mr-3 align-self-center" v-if="group.GroupType !== 1">
                      <div class="img-block-item" v-if="group.Avatar0">
                        <img :src="$store.state.appRootApi + group.Avatar0" class="img-avatar"/>
                      </div>
                      <div class="img-block-item" v-if="group.Avatar1">
                        <img :src="$store.state.appRootApi + group.Avatar1" class="img-avatar"/>
                      </div>
                    </div>
                    <div class="media-body text-left" :class="[(!Groups[key].read) ? 'user-unread' : '']">
                      <h6 class="chat-user-name mt-0 mb-0" :title="group.GroupName">{{group.GroupName}}</h6>
                      <p class="chat-user-last-content mb-0" :id="'last-content-' + group.GroupID" :class="[(!Groups[key].read) ? 'unread' : '' , 'last-content-' + group.GroupID]" v-html="getLastMessage(group.GroupID)"></p>
                    </div>
                    <b-dropdown class="chat-more-action" no-caret v-if="group.GroupType !== 1">
                      <template v-slot:button-content>
                        <span><i class="fa fa-ellipsis-v"></i></span>
                      </template>
                      <b-dropdown-item>Chi tiết</b-dropdown-item>
                      <b-dropdown-item @click.self="onShowMember($event, group)">Thành viên</b-dropdown-item>
                      <b-dropdown-item @click.self="onShowAddMember($event, group)">Thêm thành viên</b-dropdown-item>
                      <b-dropdown-divider v-if="Groups[key].isAdmin"></b-dropdown-divider>
                      <b-dropdown-item v-if="Groups[key].isAdmin" @click.self="handleDeleteGroup($event, group)">Xóa cuộc trò chuyện</b-dropdown-item>
                    </b-dropdown>
                    <span aria-label="Đang hoạt động" v-if="$store.state.chat.online[group.UserID]" class="chat-icon-active"></span>
                  </li>
                </ul>
              </div>
              <div class="chat-type-label px-3 my-1" v-if="UsersOther.length">
                <span>Người liên hệ khác</span>
              </div>
              <div class="chat-private chat-private-other">
                <ul class="chat-private-list p-0">
                  <li class="media align-items-center pl-3 pr-2 py-1" @click="onAddTabChat(user, 'private')" v-for="(user, key) in UsersOther" style="cursor: pointer">
                    <div class="img-block d-flex mr-3 align-self-center">
                      <img :id="'sidebar-avatar-' + user.UserID" :src="$store.state.appRootApi + user.Avatar" class="img-avatar"/>
                      <b-popover
                        :target="'sidebar-avatar-' + user.UserID"
                        placement="top"
                        :title="user.FullName"
                        :delay="{show: 600, hide: 50}"
                        triggers="hover focus">
                        <a :href="$store.state.appRootApi + user.Avatar" target="_blank">
                          <img :src="$store.state.appRootApi + user.Avatar" style="width: 100px; height: 100px" class="img-avatar"/>
                        </a>
                      </b-popover>
                    </div>
                    <div class="media-body text-left" :class="[(!UsersOther[key].read) ? 'user-unread' : '']">
                      <h6 class="chat-user-name mt-0 mb-0" :title="user.FullName">{{user.FullName}}</h6>
                      <p class="chat-user-last-content mb-0"
                         :class="[(!UsersOther[key].read) ? 'unread' : '' , 'last-content-' + user.GroupID]" :id="'last-content-' + user.GroupID" v-html="getLastMessage(user.GroupID)"></p>
                    </div>
                    <span class="chat-icon-active" v-if="$store.state.chat.online[UsersOther[key].UserID]" aria-label="Đang hoạt động"></span>
                  </li>
                </ul>
              </div>
          </div>
          <div class="pr-2 py-2" v-if="model.searchContact">
              <div class="chat-type-label px-3 my-1">
                <span>Liên hệ</span>
              </div>
              <div class="chat-group chat-contact" v-if="contactSearch.length">
                <ul class="chat-group-list p-0">
                  <li class="media align-items-center contact pl-3 pr-1 py-1" @click="onAddTabChat(contact, 'contact')" v-for="(contact, key) in contactSearch" style="cursor: pointer">
                    <div class="img-block img-block-group d-flex mr-3 align-self-center" v-if="contact.GroupType !== 1">
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
        </div>
      </div>
      <div class="chat-sidebar-footer">
        <div class="d-flex align-content-center justify-content-center px-2 py-1">
          <b-form-input v-model="model.searchContact" @input="onSearchContact" @keydown="onKeydownSearchContact" size="sm" placeholder="Tìm kiếm..." style="flex: 1 1 auto"></b-form-input>
          <chat-create-group v-model="Groups" @on:new-group="onAddNewGroup" style="flex: 1 1 auto; display: flex; align-items: center"></chat-create-group>
        </div>
      </div>

    </div>
    <div class="chat-tabs">
      <chat-tab
        v-model="tabsChat"
        :ref="'chat-tab-' + tabChat.GroupID"
        :key="tabChat.GroupID"
        :tab-chat="tabChat"
        :current-user="CurrentUser"
        :all-users="AllUsers"
        :members-groups="MembersInGroup"
        :groups="Groups"
        v-show="tabsChat[key].show && !tabsChat[key].delete"
        @on:remove-tab-chat="onRemoveTabChat(key)"
        @on:update-group="onUpdateGroupTabChat($event, key)"
        @on:update-group-name="onUpdateGroupName"
        @on:delete-group="onDeleteGroup"
        @on:user-read="onUserReadMessage(key)"
        @on:new-message="onAddNewMessage($event, key)"
        @on:show-chat-message="onShowChatMessage"
        @on:open-messenger="openMessenger"
        v-for="(tabChat, key) in tabsChat"></chat-tab>
    </div>
  </div>
</template>
<style type="text/css">
  #chat-sidebar {}

  #chat-sidebar .spinners {
    position: absolute;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9;
    background: #fff;
  }

  #chat-sidebar {
    overflow-y: hidden;
    display: flex;
    flex-direction: column;
    position: relative;
  }


  .chat-sidebar-footer {
    border-top: 1px solid #ccc;
    margin-top: auto;
    background: #fff;
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
    position: relative;
    flex: 1;
  }
  .chat-sidebar .media-body, .chat-sidebar .chat-user-last-content {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .chat-sidebar .chat-user-last-content {
    color: #737373;
    max-height: 21px;
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
  .chat-sidebar .media:hover {
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
    right: 285px;
    display: flex;
    flex-direction: row-reverse;
    z-index: 9;
  }
  .chat-sidebar .user-unread h6,
  .chat-sidebar .user-unread .unread {
    color: #404040;
    font-weight: bold;
  }

  .chat-icon-agree:hover svg path {
    fill: #0084ff;
  }

  .chat-contact .contact.active {
    background: #dddfe2;
  }

  .img-block-group img{
    height: 100%;
  }


</style>
<script>
  import ChatTab from "./ChatTab";
  import ApiService from '@/services/api.service';
  import ChatCreateGroup from "./partials/ChatCreateGroup";
  import mixinsSidebar from './mixins/sidebar';
  import moment from "moment";
  import PerfectScrollbar from "perfect-scrollbar";
  import wakeEvent from 'wake-event';

  let ps = null;
  export default {
    name: 'chat-sidebar',
    mixins: [mixinsSidebar],
    data () {
      return {
        // model: {
        //   searchContact: ''
        // },
        // tabsChat: [],

        // CurrentUser: null,
        // AllUsers: [],
        // UsersInterested: [],
        // UsersOther: [],
        // Groups: [],
        // AllGroups: [],
        // MembersInGroup: [],
        // LastMessageGroup: [],
        // UserRead: [],
        //
        // contact: [],
        // contactSearch: [],
        //
        // usersConnected: [],
        // newMessage: 0,
        // stage: {
        //   showSearch: false,
        //   loading: false,
        // }
        searchContact: ''
      }
    },
    components: {
      ChatTab,
      ChatCreateGroup
    },
    updated() {
      if (!ps) {
        ps = new PerfectScrollbar('.component-chat-sidebar .scroll-area', {
          wheelSpeed: 2,
          wheelPropagation: true,
          minScrollbarLength: 20
        });
      } else {
        ps.update();
      }
    },
    beforeCreate() {},
    mounted() {
      let self = this;
      this.onInitChat();
      this.onReloadUserOnline();
      setTimeout(() => {
        if ($('.component-chat-messages.messages-messenger').length) {
          $('body').removeClass('aside-menu-lg-show');
          this.tabsChat = [];
        }
      });

      socket.on('left group', (data) => {
        _.remove(self.contact, ['GroupID', data.GroupID]);
      });

      // socket disconnect
      wakeEvent(function () {
        if (self.$store.state.socket.disconnect) {
          // location.reload();
        }
      });

      window.addEventListener('click', function () {
        if (self.$store.state.chat.haveNewMessage) {
          self.$store.commit('setChat', {haveNewMessage: false});
          socket.emit('no new message', {
            UserID: self.CurrentUser.UserID,
            haveNewMessage: false
          });
        }
        if (self.$store.state.socket.disconnect) {
          // location.reload();
        }
      });

      socket.on('no new message', (data) => {
        self.$store.commit('setChat', {haveNewMessage: false});
      });

      socket.on('disconnect', (reason) => {
        console.log(reason);
        if (reason === "io server disconnect") {
          socket.connect();
        }
      });

      // Event này là kết nối lại thành công.
      socket.on('reconnect', (attemptNumber) => {
        // ...
        console.log('reconnect: ' + attemptNumber);
        let Employee = JSON.parse(localStorage.getItem('Employee'));
        if (Employee) {
          socket.emit('add user', Employee);
        }
        if (self.Groups.length) {
          socket.emit('join room', self.Groups);
        }
      });

      // Event này là bị lỗi khi kết nối lại.
      socket.on('reconnect_error', (error) => {
        // ...
      });

      // Event này không thể reconect được nữa.
      socket.on('reconnect_failed', () => {
        // ...
      });
      // Event này là kết nối đã bị lỗi.
      socket.on('connect_error', (error) => {
        // ...
      });

      socket.on('new message', (data) => {
        let lastMessage = document.querySelector('#last-content-' + data.GroupID);
        let isNewGroup = false;
        let content = (data.Content) ? data.Content : ((data.ContentFile.length) ? 'Bạn có tin nhắn mới' : '');
        let index = _.findIndex(self.LastMessageGroup, ['GroupID', Number(data.GroupID)]);
        if (index > -1) {
          self.LastMessageGroup[index].Content = data.Content;
        } else {
          self.LastMessageGroup.push(data.Content);
        }
        // chat private
        if (data.GroupType === 1) {
          let indexOther = _.findIndex(self.UsersOther, ['UserID', Number(data.UserID)]);
          if (indexOther > -1) {
            if (!self.UsersOther[indexOther].GroupID) {
              isNewGroup = true;
              self.UsersOther[indexOther].GroupID = data.GroupID;
            }
            self.UsersOther[indexOther].ChatContentID = data.LineID;

            self.Groups.push(self.UsersOther[indexOther]);
            self.UsersOther.splice(indexOther, 1);
          }

          let indexGroup = _.findIndex(self.Groups, ['GroupID', Number(data.GroupID)]);
          if (indexGroup > -1) {
            if (self.CurrentUser.UserID !== data.UserID) {
              if ((self.Groups[indexGroup].read || isNewGroup) && !$('.component-chat-messages').length) {
                self.changeNewMessage('+');
              }
              self.Groups[indexGroup].read = false;
            }
            self.Groups[indexGroup].ChatContentID = data.LineID;
            self.Groups[indexGroup].userSeen = [];
          }

        } else {
          // chat group
          let indexGroup = _.findIndex(self.Groups, ['GroupID', Number(data.GroupID)]);
          if (indexGroup > -1) {
            if (self.CurrentUser.UserID !== data.UserID) {
              if (self.Groups[indexGroup].read && lastMessage && !lastMessage.classList.contains('unread')) {
                self.changeNewMessage('+');
              }
              self.Groups[indexGroup].read = false;
            }

            self.Groups[indexGroup].ChatContentID = data.LineID;
            self.Groups[indexGroup].userSeen = [];
          }
        }

        self.$nextTick(() => {
          if (content.includes(':sb-action-member')) {
            if (content.includes(':sb-action-member_add')) {
              $('.last-content-' + data.GroupID).text('Bạn đã được thêm vào nhóm');
            } else {
              let text = self.getMessageActionMember(data);
              $('.last-content-' + data.GroupID).html(text);
            }
          } else {
            $('.last-content-' + data.GroupID).html(content);
          }
        });
        // set userSeen
        let indexTabChat = _.findIndex(self.tabsChat, ['GroupID', data.GroupID]);
        if (indexTabChat > -1 && data.GroupType !== 1) {
          self.tabsChat[indexTabChat].seen = false;
          self.tabsChat[indexTabChat].userSeen = [];
        }

        if (!data.isDataflow && !data.HideGroupChat) {
          self.$nextTick(() => {
            if (content.includes(':sb-action-member')) {
              if (content.includes(':sb-action-member_add')) {
                $('.last-content-' + data.GroupID).text('Bạn đã được thêm vào nhóm');
              } else {
                let text = self.getMessageActionMember(data);
                $('.last-content-' + data.GroupID).html(text);
              }
            } else {
              $('.last-content-' + data.GroupID).html(content);
            }
          });
          
          if (data.UserID !== self.CurrentUser.UserID) {
            self.$store.commit('setChat', {haveNewMessage: true});
          }
          if (data.UserID !== self.CurrentUser.UserID) {
            let audio = document.getElementById('message-audio-notification');
            audio.play();
            this.notifyMessage(data);
          }
        }

        self.sortGroups();
        this.$forceUpdate();
      });

      socket.on('read message', (data) => {
        let indexGroup = _.findIndex(self.Groups, ['GroupID', data.GroupID]);

        let lastMessage = document.querySelector('.last-content-' + data.GroupID);
        if (lastMessage && lastMessage.classList.contains('unread') || (indexGroup > -1 && self.Groups[indexGroup].isNewGroup)) {
          $(lastMessage).removeClass('unread');
          $(lastMessage).closest('.user-unread').removeClass('user-unread');
          self.changeNewMessage('-');
        }

        let indexOther = _.findIndex(self.UsersOther, ['GroupID', data.GroupID]);
        if (indexOther > -1) {
          self.UsersOther[indexOther].read = true;
        }

        // let indexGroup = _.findIndex(self.Groups, ['GroupID', data.GroupID]);
        if (indexGroup > -1) {
          self.Groups[indexGroup].read = true;
        }
      });

      socket.on('new group', (data) => {
        if (data.group) {
          data.group.read = (data.group.Created && (data.group.Created === self.CurrentUser.UserID)) ? true : false;
          data.group.seen = false;
          data.group.isNewGroup = true;
          self.Groups.unshift(data.group);
          self.Groups = _.uniqBy(self.Groups, 'GroupID');

          self.contact.push({
            GroupID: data.group.GroupID,
            GroupName: data.group.GroupName,
            GroupType: data.group.GroupType,
            Avatar0: (data.group.Avatar0) ? data.group.Avatar0 : '',
            Avatar1: (data.group.Avatar1) ? data.group.Avatar1 : ''
          });
          self.contact = _.uniqBy(self.contact, 'GroupID');
          self.MembersInGroup = _.concat(self.MembersInGroup, data.members);

          if (!$('.component-chat-messages').length && (data.group.Created !== self.CurrentUser.UserID)) {
            self.changeNewMessage('+');
          }

          let dataSent = [];
          dataSent.push(data.group);
          socket.emit('join room', dataSent);
        }
      });
    },
    methods: {
      onShowChatMessage(user){
        let item = null;
        let indexInterested = _.findIndex(this.UsersInterested, ['UserID', user.UserID]);
        if (indexInterested > -1) {
          item = this.UsersInterested[indexInterested];
        }

        let indexOther = _.findIndex(this.UsersOther, ['UserID', user.UserID]);
        if (indexOther > -1) {
          item = this.UsersOther[indexOther];
        }

        if (item) {
          this.onAddTabChat(item, 'private');
        } else {
          this.$bvToast.toast('Không tìm thấy người dùng', {
            title: 'Thông báo',
            variant: 'warning'
          });
        }
      },

      onUpdateGroupTabChat(group, key){
        this.tabsChat[key].GroupType = group.GroupType;
        this.tabsChat[key].GroupID = group.GroupID;
        if (this.tabsChat[key].GroupType === 1) {
          let userIndex = _.findIndex(this.AllUsers, ['UserID', this.tabsChat[key].UserID]);
          if (userIndex > -1) {
            this.AllUsers[userIndex].GroupID = group.GroupID;
          }

          let userInterestedIndex = _.findIndex(this.UsersInterested, ['UserID', this.tabsChat[key].UserID]);
          if (userInterestedIndex > -1) {
            this.UsersInterested[userInterestedIndex].GroupID = group.GroupID;
          }

          let userOtherIndex = _.findIndex(this.UsersOther, ['UserID', this.tabsChat[key].UserID]);
          if (userOtherIndex > -1) {
            this.UsersOther[userOtherIndex].GroupID = group.GroupID;
          }
        }
        this.$forceUpdate();
      },

      onShowMember(e, group){
        let self = this;
        e.preventDefault();
        e.stopPropagation();
        this.onAddTabChat(group, 'group');
        this.$nextTick(() => {
          setTimeout(() => {
            self.$refs['chat-tab-' + group.GroupID][0].onShowMember();
            self.$forceUpdate();
          });
        });
      },
      onShowAddMember(e, group){
        let self = this;
        e.preventDefault();
        e.stopPropagation();
        this.onAddTabChat(group, 'group');
        this.$nextTick(() => {
          setTimeout(() => {
            self.$refs['chat-tab-' + group.GroupID][0].onShowAddMember();
            self.$forceUpdate();
          });
        });
      },
      handleDeleteGroup(e, group){
        e.preventDefault();
        e.stopPropagation();
        if (!group.isAdmin) return false;

        Swal.fire({
          title: 'Xác nhận',
          text: 'Bạn có chắc xóa nhóm',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Đồng ý',
          cancelButtonText: 'Hủy bỏ'
        }).then((result) => {
          if (result.value) {
            let self = this;
            let requestData = {
              method: 'post',
              url: 'extensions/api/chat/delete-group',
              data: {
                GroupID: group.GroupID,
              }
            };

            ApiService.setHeader();
            ApiService.customRequest(requestData).then((responses) => {
              let responsesData = responses.data;
              if (responsesData.status === 1) {
                self.onDeleteGroup(responsesData.data);
                self.$bvToast.toast('Cập nhật thành công', {
                  title: 'Thông báo',
                  variant: 'success'
                });
              }else if (responsesData.status === 2) {
                self.$bvToast.toast(responsesData.msg, {
                  title: 'Thông báo',
                  variant: 'warning'
                });
              } else {
                self.$bvToast.toast('Cập nhật thất bại', {
                  title: 'Thông báo',
                  variant: 'warning'
                });
              }
            }, (error) => {
              console.log(error);
            });
          }
        });
      },
    },
    watch: {}
  }
</script>
