import ApiService from '@/services/api.service';
import Swal from "sweetalert2";
export default {
  data(){
    return {
      model: {
        searchContact: ''
      },
      tabsChat: [],

      CurrentUser: null,
      AllUsers: [],
      UsersInterested: [],
      UsersOther: [],
      Groups: [],
      GroupsMessenger: [],
      AllGroups: [],
      MembersInGroup: [],
      LastMessageGroup: [],
      UserRead: [],

      contact: [],
      contactSearch: [],

      usersConnected: [],
      newMessage: (this.$store.state.chat.newMessage) ? this.$store.state.chat.newMessage : 0,

      // type messenger
      allMessenger: [],

      stage: {
        showSearch: false,
        loading: false,
        showSidebar: false
      }
    }
  },
  mounted(){
    let self = this;
    // Socket events
    socket.on('user online', (data) => {
      self.usersConnected = data;
      self.$store.commit('userOnline', {});
      _.forEach(self.usersConnected, function (userOnline, key) {
        self.$store.commit('userOnline', {UserID: userOnline.UserID, value: true});
      });
      self.sortGroups();
      self.$forceUpdate();
    });
    socket.on('user joined', (data) => {
      self.usersConnected = data.usersConnected;
      self.$store.commit('userOnline', {});
      _.forEach(self.usersConnected, function (userOnline, key) {
        self.$store.commit('userOnline', {UserID: userOnline.UserID, value: true});
      });
      self.sortGroups();
      self.$forceUpdate();
    });
    socket.on('user left', (data) => {
      _.remove(self.usersConnected, ['UserID', data.UserID]);
      self.$store.commit('userOnline', {UserID: data.UserID, value: false});
      self.sortGroups();
      self.$forceUpdate();
    });

    socket.on('seen message', (data) => {
      let tabChatIndex = _.findIndex(self.tabsChat, ['GroupID', data.GroupID]);
      if (data.GroupType !== 1) {
        // group
        let indexGroup = _.findIndex(self.Groups, ['GroupID', data.GroupID]);
        if (indexGroup > -1) {
          let userExist = _.find(self.Groups[indexGroup].userSeen, ['UserID', data.UserIDSeen]);
          if (!userExist) {
            if (!self.Groups[indexGroup].userSeen) {
              self.Groups[indexGroup].userSeen = [];
            }
            self.Groups[indexGroup].userSeen.push({
              UserID: data.UserIDSeen
            });
          }
        }

        // messenger
        if (self.GroupsMessenger && self.GroupsMessenger.length) {
          let indexGroupMessenger = _.findIndex(self.GroupsMessenger, ['GroupID', data.GroupID]);
          if (indexGroupMessenger > -1) {
            let userExistMessenger = _.find(self.GroupsMessenger[indexGroupMessenger].userSeen, ['UserID', data.UserIDSeen]);
            if (!userExistMessenger) {
              if (!self.GroupsMessenger[indexGroupMessenger].userSeen) {
                self.GroupsMessenger[indexGroupMessenger].userSeen = [];
              }
              self.GroupsMessenger[indexGroupMessenger].userSeen.push({
                UserID: data.UserIDSeen
              });
            }
          }
        }

        if (tabChatIndex > -1) {
          // tab chat
          self.tabsChat[tabChatIndex].seen = true;
          let userExist = _.find(self.tabsChat[tabChatIndex].userSeen, ['UserID', data.UserIDSeen]);
          if (!userExist) {
            if (!self.tabsChat[tabChatIndex].userSeen) {
              self.tabsChat[tabChatIndex].userSeen = [];
            }
            self.tabsChat[tabChatIndex].userSeen.push({
              UserID: data.UserIDSeen
            });
          }
        }
      } else {
        let indexUser = _.findIndex(self.AllUsers, ['UserID', data.UserIDSeen]);
        if (indexUser > -1) {
          self.AllUsers[indexUser].seen = true;
        }

        let indexInterested = _.findIndex(self.UsersInterested, ['UserID', data.UserIDSeen]);
        if (indexInterested > -1) {
          self.UsersInterested[indexInterested].seen = true;
        }

        let indexOther = _.findIndex(self.UsersOther, ['UserID', data.UserIDSeen]);
        if (indexOther > -1) {
          self.UsersOther[indexOther].seen = true;
        }

        if (tabChatIndex > -1) {
          self.tabsChat[tabChatIndex].seen = true;
        }

        let indexGroup = _.findIndex(self.Groups, ['GroupID', data.GroupID]);
        if (indexGroup > -1) {
          self.Groups[indexGroup].seen = true;
        }

        if (self.GroupsMessenger && self.GroupsMessenger.length) {
          let indexGroupMessenger = _.findIndex(self.GroupsMessenger, ['GroupID', data.GroupID]);
          if (indexGroupMessenger > -1) {
            self.GroupsMessenger[indexGroupMessenger].seen = true;
          }
        }

      }
      this.$forceUpdate();
    });

    socket.on('remove member', (data) => {
      _.remove(self.MembersInGroup, {
        GroupID: data.GroupID,
        UserID: data.member.UserID
      });
    });

    socket.on('left group', (data) => {
      _.remove(self.Groups, ['GroupID', data.GroupID]);
      if (self.GroupsMessenger && self.GroupsMessenger.length) {
        _.remove(self.GroupsMessenger, ['GroupID', data.GroupID]);
      }
      let index = _.findIndex(self.tabsChat, ['GroupID', data.GroupID]);
      if (index > -1) {
        self.onRemoveTabChat(index);
      }
      self.$forceUpdate();
    });

    socket.on('join group', (data) => {
      let groupExist = _.find(self.Groups, ['GroupID', data.GroupID]);
      if (!_.isObject(groupExist)) {
        self.Groups.push(data.group);
        self.$forceUpdate();
      }

      // messenger
      if (self.GroupsMessenger && self.GroupsMessenger.length) {
        let groupExistMessenger = _.find(self.GroupsMessenger, ['GroupID', data.GroupID]);
        if (!_.isObject(groupExistMessenger)) {
          self.GroupsMessenger.push(data.group);
          self.$forceUpdate();
        }
      }
    });
    socket.on('delete group', (data) => {
      let group = data.group;
      let indexTabChat = _.findIndex(self.tabsChat, ['GroupID', group.GroupID]);
      if (indexTabChat > -1) {
        self.onRemoveTabChat(indexTabChat);
      }
      _.remove(self.Groups, ['GroupID', group.GroupID]);
      if (self.GroupsMessenger && self.GroupsMessenger.length) {
        _.remove(self.GroupsMessenger, ['GroupID', group.GroupID]);
      }
      self.$forceUpdate();
    });

    socket.on('delete message', (data) => {

      let index = _.findIndex(self.LastMessageGroup, ['GroupID', Number(data.GroupID)]);
      if (index > -1) {
        self.LastMessageGroup[index].Content = 'Tin nhắn đã bị gỡ bỏ';
      }

      let lastMessage = document.querySelector('#last-content-' + data.GroupID);
      if (lastMessage && lastMessage.classList.contains('unread')) {
        $(lastMessage).removeClass('unread');
        $(lastMessage).closest('.user-unread').removeClass('user-unread');
        self.changeNewMessage('-');

        let index = _.findIndex(self.AllUsers, ['GroupID', data.GroupID]);
        if (index > -1) {
          self.AllUsers[index].read = true;
        }

        let indexInterested = _.findIndex(self.UsersInterested, ['GroupID', data.GroupID]);
        if (indexInterested > -1) {
          self.UsersInterested[indexInterested].read = true;
        }

        let indexOther = _.findIndex(self.UsersOther, ['GroupID', data.GroupID]);
        if (indexOther > -1) {
          self.UsersOther[indexOther].read = true;
        }

        let indexGroup = _.findIndex(self.Groups, ['GroupID', data.GroupID]);
        if (indexGroup > -1) {
          self.Groups[indexGroup].read = true;
        }

        //messenger
        if (self.GroupsMessenger && self.GroupsMessenger.length) {
          let indexGroupMessenger = _.findIndex(self.GroupsMessenger, ['GroupID', data.GroupID]);
          if (indexGroupMessenger > -1) {
            self.GroupsMessenger[indexGroupMessenger].read = true;
          }
        }

      }

    });
  },
  methods:{
    onInitChat(view = 'classic'){
      let self = this;
      if (this.AllUsers.length) return;
      let requestData = {
        method: 'post',
        url: 'extensions/api/chat',
        data: {
          view: view
        }
      };
      self.newMessage = 0;
      ApiService.setHeader();
      ApiService.customRequest(requestData).then((responses) => {
        let responsesData = responses.data;
        if (responsesData.status === 1) {
          self.CurrentUser = responsesData.data.CurrentUser;
          self.contact = [];
          if (responsesData.data.UserRead) self.UserRead = responsesData.data.UserRead;
          if (responsesData.data.AllGroups) self.AllGroups = responsesData.data.AllGroups;
          if (responsesData.data.MembersInGroup) self.MembersInGroup = responsesData.data.MembersInGroup;
          if (responsesData.data.LastMessageGroup) self.LastMessageGroup = responsesData.data.LastMessageGroup;
          if (responsesData.data.data) {
            self.AllUsers = responsesData.data.data;

            // chat private
            if (view === 'classic') {
              self.UsersInterested = [];
              self.UsersOther = [];
              _.forEach(self.AllUsers, function (user, key) {
                let tmpObj = user;
                tmpObj.ChatContentID = 0;
                tmpObj.GroupName = user.FullName;
                let member = _.find(self.MembersInGroup, {
                  UserID: user.UserID,
                  GroupType: 1
                });
                if (!member) {
                  tmpObj.GroupID = null;
                  tmpObj.GroupType = 1;
                  tmpObj.read = true;
                  tmpObj.seen = false;
                  self.AllUsers[key].GroupID = null;
                  self.AllUsers[key].GroupType = 1;
                  self.AllUsers[key].read = true;
                  self.AllUsers[key].seen = false;

                  let userOnline = _.find(self.usersConnected, ['UserID', user.UserID]);
                  self.AllUsers[key].Online = (userOnline) ? true : false;
                  tmpObj.Online = (userOnline) ? true : false;
                  tmpObj.Avatar = user.Avata;
                  tmpObj.GroupName = user.FullName;
                  tmpObj.UserID = user.UserID;
                  tmpObj.UserName = user.FullName;

                  if (!member) {
                    self.$set(self.UsersOther, self.UsersOther.length, tmpObj);
                  }

                  self.contact.push({
                    GroupID: tmpObj.GroupID,
                    GroupName: (tmpObj.FullName) ? tmpObj.FullName : '',
                    GroupType: tmpObj.GroupType,
                    Avatar: tmpObj.Avatar,
                    UserID: tmpObj.UserID,
                    FullName: tmpObj.FullName
                  });

                }
              });
            }
          }

          // chat group
          if (view === 'classic') {
            self.Groups = [];
            _.forEach(self.AllGroups, function (group, key) {
              let tmpObj = group;
              tmpObj.ChatContentID = 0;
              let lastMessage = _.find(self.LastMessageGroup, ['GroupID', group.GroupID]);
              if (lastMessage) {
                let userRead = _.find(self.UserRead, {
                  ChatContentID: lastMessage.LineID,
                  UserID: self.CurrentUser.UserID
                });
                if (userRead || lastMessage.UserID === self.CurrentUser.UserID) {
                  tmpObj.read = true;
                  tmpObj.seen = true;
                } else {
                  tmpObj.read = false;
                  tmpObj.seen = false;
                  self.newMessage++;
                }

                // all user seen
                let usersSeen = _.filter(self.UserRead, ['ChatContentID', lastMessage.LineID]);
                tmpObj.userSeen = [];
                _.forEach(usersSeen, function (user, key) {
                  tmpObj.userSeen.push(user);
                });
                if (tmpObj.userSeen.length) {
                  tmpObj.seen = true;
                } else {
                  tmpObj.seen = false;
                }

                tmpObj.ChatContentID = lastMessage.LineID;
              }else {
                tmpObj.read = true;
                tmpObj.seen = false;
              }

              let members = _.filter(self.MembersInGroup, ['GroupID', group.GroupID]);
              tmpObj.Avatar0 = (members[0]) ? members[0].Avata : '';
              tmpObj.Avatar1 = (members[1]) ? members[1].Avata : '';

              let userMember = _.find(members, ['UserID', self.CurrentUser.UserID]);
              if (userMember) {
                if (userMember.Type === 1) {
                  tmpObj.isAdmin = true;
                } else {
                  tmpObj.isAdmin = false;
                }
              }

              if (group.GroupType === 1) {
                let member = _.find(self.MembersInGroup, function (o) {
                  return o.GroupID === group.GroupID && o.UserID !== self.CurrentUser.UserID;
                });

                if (member) {
                  let userMember = _.find(self.AllUsers, ['UserID', member.UserID]);
                  if (userMember) {
                    tmpObj.GroupName = userMember.FullName;
                    tmpObj.Avatar = userMember.Avata;
                    tmpObj.UserID = userMember.UserID;
                    tmpObj.UserName = userMember.FullName;
                  } else {
                    return;
                  }
                }
              } else {
                let members = _.filter(self.MembersInGroup, ['GroupID', group.GroupID]);
                tmpObj.Avatar0 = (members[0]) ? members[0].Avata : '';
                tmpObj.Avatar1 = (members[1]) ? members[1].Avata : '';
              }

              tmpObj.CategoryKey = group.CategoryKey;

              self.$set(self.Groups, self.Groups.length, tmpObj);

              self.contact.push({
                GroupID: tmpObj.GroupID,
                GroupName: tmpObj.GroupName,
                GroupType: tmpObj.GroupType,
                UserID: (tmpObj.UserID) ? tmpObj.UserID : null,
                Avatar: (tmpObj.Avatar) ? tmpObj.Avatar : '',
                Avatar0: (tmpObj.Avatar0) ? tmpObj.Avatar0 : '',
                Avatar1: (tmpObj.Avatar1) ? tmpObj.Avatar1 : ''
              });
            });

            // join to room
            if (self.Groups.length) {
              socket.emit('join room', self.Groups);
            }
          }

          // messenger
          if (view === 'messenger') {
            self.GroupsMessenger = [];
            _.forEach(self.AllGroups, function (group, key) {
              let tmpObj = group;
              tmpObj.ChatContentID = 0;
              let lastMessage = _.find(self.LastMessageGroup, ['GroupID', group.GroupID]);
              if (lastMessage) {
                let userRead = _.find(self.UserRead, {
                  ChatContentID: lastMessage.LineID,
                  UserID: self.CurrentUser.UserID
                });
                if (userRead || lastMessage.UserID === self.CurrentUser.UserID) {
                  tmpObj.read = true;
                  tmpObj.seen = true;
                } else {
                  tmpObj.read = false;
                  tmpObj.seen = false;
                  self.newMessage++;
                }

                // all user seen
                let usersSeen = _.filter(self.UserRead, ['ChatContentID', lastMessage.LineID]);
                tmpObj.userSeen = [];
                _.forEach(usersSeen, function (user, key) {
                  tmpObj.userSeen.push(user);
                });
                if (tmpObj.userSeen.length) {
                  tmpObj.seen = true;
                } else {
                  tmpObj.seen = false;
                }

                tmpObj.ChatContentID = lastMessage.LineID;
              }else {
                tmpObj.read = true;
                tmpObj.seen = false;
              }

              if (group.GroupType === 1) {
                let member = _.find(self.MembersInGroup, function (o) {
                  return o.GroupID === group.GroupID && o.UserID !== self.CurrentUser.UserID;
                });

                if (member) {
                  let userMember = _.find(self.AllUsers, ['UserID', member.UserID]);
                  if (userMember) {
                    tmpObj.GroupName = userMember.FullName;
                    tmpObj.Avatar = userMember.Avata;
                    tmpObj.UserID = userMember.UserID;
                    tmpObj.UserName = userMember.FullName;
                  }
                }
              } else {
                let members = _.filter(self.MembersInGroup, ['GroupID', group.GroupID]);
                tmpObj.Avatar0 = (members[0]) ? members[0].Avata : '';
                tmpObj.Avatar1 = (members[1]) ? members[1].Avata : '';
              }

              tmpObj.CategoryKey = group.CategoryKey;

              self.$set(self.GroupsMessenger, self.GroupsMessenger.length, tmpObj);

              self.contact.push({
                GroupID: tmpObj.GroupID,
                GroupName: tmpObj.GroupName,
                GroupType: tmpObj.GroupType,
                UserID: (tmpObj.UserID) ? tmpObj.UserID : null,
                Avatar: (tmpObj.Avatar) ? tmpObj.Avatar : '',
                Avatar0: (tmpObj.Avatar0) ? tmpObj.Avatar0 : '',
                Avatar1: (tmpObj.Avatar1) ? tmpObj.Avatar1 : ''
              });
            });
            this.contact = self.GroupsMessenger;

            if (this.params.GroupID) {
              let group = _.find(self.GroupsMessenger, ['GroupID', Number(this.params.GroupID)]);
              if (group) {
                self.onAddTabChat(group, 'messenger');
              } else {
                self.onAddTabChat(self.GroupsMessenger[0], 'messenger');
              }
            } else {
              if (window.innerWidth >= 768) {
                self.onAddTabChat(self.GroupsMessenger[0], 'messenger');
              }
            }

            // fix box-shadow messeenger
            if ($('.component-chat-messages').length) {
              if (!$('.component-chat-messages #chat-sidebar').hasClass('message-loaded')) {
                $('.component-chat-messages #chat-sidebar').addClass('message-loaded');
              }
            }

            // join to room
            if (self.GroupsMessenger.length) {
              socket.emit('join room', self.GroupsMessenger);
            }
          }

          // set new message
          this.changeNewMessage('');

          // sort message
          self.sortGroups();
        }
      }, (error) => {
        console.log(error);
        Swal.fire({
          title: 'Thông báo',
          text: 'Không kết nối được với máy chủ',
          confirmButtonText: 'Đóng'
        });
      });
    },
    getLastMessage(GroupID){
      if (GroupID) {
        let lastMessage = _.find(this.LastMessageGroup, ['GroupID', GroupID]);
        if (lastMessage) {
          // remove <br>
          lastMessage.Content = lastMessage.Content.replace(/[<]br[^>]*[>]/gi,' ');
          if (!lastMessage.Content.includes(':sb-action-member')) {
            return lastMessage.Content;
          } else {
            return this.getMessageActionMember(lastMessage);
            // return 'Có tin nhắn mới';
          }

        }
      }
      return '';
    },
    changeNewMessage(operator = ''){
      if (operator) {
        this.newMessage = this.$store.state.chat.newMessage;
        if (operator === '+') {
          this.newMessage++;
        }
        if (operator === '-') {
          this.newMessage--;
        }
      }

      if (this.newMessage > 0) {
        this.$store.commit('newMessage', this.newMessage);
      } else {
        this.$store.commit('newMessage', 0);
      }

    },
    getMessageActionMember(message){
      let self = this, pieces = message.Content.split('_'), text = '', listUsersName = '',
        userIDs = pieces[2].split(',');

      _.forEach(userIDs, function (UserID, key) {
        let user = _.find(self.AllUsers, ['UserID', Number(UserID)]);
        if (user) {
          listUsersName += user.FullName;
          if (key !== (userIDs.length - 1)) {
            if (userIDs.length === 2) {
              listUsersName += ' và ';
            } else {
              listUsersName += ', ';
            }
          }
        } else if (Number(UserID) === self.CurrentUser.UserID) {
          listUsersName += 'bạn';
        }
      });
      if (pieces[1] === 'add') {
        text = ((message.UserID === this.CurrentUser.UserID) ? 'Bạn' : this.getLastName(message.UserID)) + ' đã thêm ' + listUsersName + ' vào nhóm';
      }
      if (pieces[1] === 'remove') {
        text = ((message.UserID === this.CurrentUser.UserID) ? 'Bạn' : this.getLastName(message.UserID)) + ' đã loại ' + listUsersName + ' khỏi nhóm';
      }
      if (pieces[1] === 'leave') {
        text = ((message.UserID === this.CurrentUser.UserID) ? 'Bạn' : this.getLastName(message.UserID)) + ' đã rời khỏi nhóm';
      }
      return text;
    },
    getLastName(UserID){
      let user = null;
      if (UserID === this.CurrentUser.UserID) {
        user = this.CurrentUser;
      }else {
        user = _.find(this.AllUsers, ['UserID', UserID]);
      }
      if (user) {
        let res = user.FullName.split(' ');
        return res[res.length - 1];
      }
      return '';
    },
    onAddTabChat(item, type){
      let tmpObj = {};
      let tabIndex = null;
      let self = this;
      let originType = type;

      if (type === 'contact') {
        type = (item.GroupType === 1) ? 'private' : 'group';
        this.model.searchContact = '';
      }

      if (type === 'trigger') {
        type = (item.GroupType === 1) ? 'private' : 'group';
      }

      if (type !== 'messenger') {
        if (type === 'private') {
          // add tab
          tabIndex = _.findIndex(this.tabsChat, ['UserID', item.UserID]);
          if (tabIndex > -1) {
            this.tabsChat[tabIndex].show = true;
            this.tabsChat[tabIndex].delete = false;
            if (this.$refs['chat-tab-' + this.tabsChat[tabIndex].GroupID] && this.$refs['chat-tab-' + this.tabsChat[tabIndex].GroupID][0]) {
              this.$refs['chat-tab-' + this.tabsChat[tabIndex].GroupID][0].stage.minimize = false;
            }
          } else {
            tmpObj.GroupName = (item.FullName) ? item.FullName : ((item.GroupName) ? item.GroupName : '');
            tmpObj.Avatar = (item.Avata) ? item.Avata : item.Avatar;
            tmpObj.GroupID = item.GroupID;
            tmpObj.UserID = item.UserID;
            tmpObj.GroupType = 1;
            tmpObj.Online = item.Online;
            tmpObj.LineID = this.tabsChat.length;
            tmpObj.show = true;
            tmpObj.delete = false;
            tmpObj.seen = item.seen;
            tabIndex = this.tabsChat.length;
            this.$set(this.tabsChat, this.tabsChat.length, tmpObj);
          }
        }
        // TODO: logic for group
        if (type === 'group') {
          // add tab
          tabIndex = _.findIndex(this.tabsChat, ['GroupID', item.GroupID]);
          if (tabIndex > -1) {
            this.tabsChat[tabIndex].show = true;
            this.tabsChat[tabIndex].delete = false;
            if (this.$refs['chat-tab-' + this.tabsChat[tabIndex].GroupID] && this.$refs['chat-tab-' + this.tabsChat[tabIndex].GroupID][0]) {
              this.$refs['chat-tab-' + this.tabsChat[tabIndex].GroupID][0].stage.minimize = false;
            }
          } else {
            tmpObj.GroupName = item.GroupName;
            tmpObj.Avatar0 = (item.Avatar0) ? item.Avatar0 : '';
            tmpObj.Avatar1 = (item.Avatar1) ? item.Avatar1 : '';
            tmpObj.GroupID = item.GroupID;
            tmpObj.GroupType = item.GroupType;
            tmpObj.LineID = this.tabsChat.length;
            tmpObj.Online = item.Online;
            tmpObj.CategoryKey = (item.CategoryKey) ? item.CategoryKey : null;
            tmpObj.show = true;
            tmpObj.delete = false;
            tmpObj.seen = item.seen;
            tmpObj.userSeen = item.userSeen;

            if (item.GroupType === 1) {
              tmpObj.UserID = item.UserID;
              tmpObj.UserName = item.UserName;
              tmpObj.Avatar = item.Avatar
            }

            tabIndex = this.tabsChat.length;
            this.$set(this.tabsChat, this.tabsChat.length, tmpObj);
          }
        }

        let tabsShow = _.filter(this.tabsChat, {
          show: true,
          delete: false
        });

        if (this.tabsChat.length > 3 && tabsShow.length > 3) {
          _.forEach(this.tabsChat, function (tabChat, key) {
            if (tabChat.show === true && tabChat.delete === false && key !== tabIndex) {
              self.tabsChat[key].show = false;
              return false;
            }
          });
        }
      } else {

        this.tabsChat = [];
        tmpObj.GroupName = item.GroupName;
        tmpObj.Avatar0 = (item.Avatar0) ? item.Avatar0 : '';
        tmpObj.Avatar1 = (item.Avatar1) ? item.Avatar1 : '';
        tmpObj.GroupID = item.GroupID;
        tmpObj.GroupType = item.GroupType;
        tmpObj.LineID = this.tabsChat.length;
        tmpObj.Online = item.Online;
        tmpObj.seen = item.seen;
        tmpObj.userSeen = item.userSeen;
        tmpObj.show = true;
        tmpObj.delete = false;
        tmpObj.CategoryKey = (item.CategoryKey) ? item.CategoryKey : null;

        if (item.GroupType === 1) {
          tmpObj.UserID = item.UserID;
          tmpObj.UserName = item.UserName;
          tmpObj.Avatar = item.Avatar
        }

        this.$set(this.tabsChat, this.tabsChat.length, tmpObj);

        this.$nextTick(() => {
          $('.chat-group').removeClass('active');
          $('.chat-group-' + item.GroupID).addClass('active');
          if (window.innerWidth < 768) {
            $('.chat-messages-mobile').removeClass('show-sidebar');
          }
        });
      }

      this.$nextTick(() => {
        if (originType !== 'trigger' && (window.innerWidth >= 768)) {
          $('.chat-tab-' + item.GroupID + ' .message-input').focus();
        }
      });

      this.$forceUpdate();
    },

    onRemoveTabChat(key){
      this.tabsChat[key].show = false;
      this.tabsChat[key].delete = true;

      // show next chat
      let indexShow = _.findIndex(this.tabsChat, {
        show: false,
        delete: false
      });
      if (indexShow > -1) {
        this.tabsChat[indexShow].show = true;
      }

      let tabsDelete = _.filter(this.tabsChat, ['delete', true]);
      if (tabsDelete.length === this.tabsChat.length) {
        this.tabsChat = [];
      }
      this.$forceUpdate();
    },

    onAddNewGroup(data) {
      if (data.group) {
        data.group.seen = false;
        data.group.read = false;
        data.group.userSeen = [];

        data.group.Avatar0 = (data.members[0]) ? data.members[0].Avata : '';
        data.group.Avatar1 = (data.members[1]) ? data.members[1].Avata : '';
      }
      socket.emit('new group', data);
    },

    onSearchContact() {
      let self = this;
      this.contactSearch = _.filter(this.contact, function (o) {
        let noAccent = __.cleanAccents(o.GroupName);
        let groupNameLower = _.toLower(o.GroupName);
        let noAccentLower = _.toLower(noAccent);

        return o.GroupName.includes(self.model.searchContact) || noAccent.includes(self.model.searchContact)
          || groupNameLower.includes(self.model.searchContact) || noAccentLower.includes(self.model.searchContact);
      });
    },

    onKeydownSearchContact(e){
      let code = e.which;

      if (code === 40) {
        e.preventDefault();
        if (!$('.chat-contact .contact.active').length) {
          $('.chat-contact .contact').first().addClass('active');
        } else {
          let $currentActive = $('.chat-contact .contact').filter('.active');
          $currentActive.removeClass('active');
          $currentActive.next().addClass('active');
        }

      } else if (code === 38) {
        e.preventDefault();
        // up here
        if (!$('.chat-contact .contact.active').length) {
          $('.chat-contact .contact').last().addClass('active');
        } else {
          let $currentActive = $('.chat-contact .contact').filter('.active');
          $currentActive.removeClass('active');
          $currentActive.prev().addClass('active');
        }
      }else if (code === 13) {
        $('.chat-contact .contact.active').trigger('click');
      }

    },

    onAddNewMessage(content, key){
      this.tabsChat[key].seen = false;
      this.tabsChat[key].userSeen = [];
      let lasMessage = _.find(this.LastMessageGroup, ['GroupID', content.GroupID]);
      if (!lasMessage) {
        this.LastMessageGroup.push(content);
      }
      this.$nextTick(() => {
        $('.last-content-' + content.GroupID).html(content.Content);
      });

      let indexUsersInterested = _.findIndex(this.UsersInterested, ['GroupID', content.GroupID]);
      if (indexUsersInterested > -1) {
        this.UsersInterested[indexUsersInterested].ChatContentID = content.LineID;
        this.UsersInterested[indexUsersInterested].seen = false;
      }

      let indexUsersOther = _.findIndex(this.UsersOther, ['GroupID', content.GroupID]);
      if (indexUsersOther > -1) {
        this.UsersOther[indexUsersOther].ChatContentID = content.LineID;
        // this.UsersInterested.push(this.UsersOther[indexUsersOther]);
        this.Groups.push(this.UsersOther[indexUsersOther]);
        if (this.GroupsMessenger && this.GroupsMessenger.length) {
          this.GroupsMessenger.push(this.UsersOther[indexUsersOther]);
        }
        this.UsersOther.splice(indexUsersOther, 1);
        this.UsersOther[indexUsersOther].seen = false;
      }

      let indexGroup = _.findIndex(this.Groups, ['GroupID', content.GroupID]);
      if (indexGroup > -1) {
        this.Groups[indexGroup].ChatContentID = content.LineID;
        this.Groups[indexGroup].seen = false;
        this.Groups[indexGroup].userSeen = [];
      }

      // messenger
      let indexGroupMessenger = _.findIndex(this.GroupsMessenger, ['GroupID', content.GroupID]);
      if (indexGroupMessenger > -1) {
        this.GroupsMessenger[indexGroupMessenger].ChatContentID = content.LineID;
        this.GroupsMessenger[indexGroupMessenger].userSeen = [];
      }

      this.sortGroups();
      this.$forceUpdate();
    },

    onUserReadMessage(key){
      if (this.tabsChat[key].GroupID) {
        let lastMessage = document.querySelector('.last-content-' + this.tabsChat[key].GroupID);
        let checkNewGroup = false;
        if (!$('.component-chat-messages').length) {
          let _indexGroup = _.findIndex(this.Groups, ['GroupID', this.tabsChat[key].GroupID]);
          if (_indexGroup > -1 && this.Groups[_indexGroup].isNewGroup) {
            checkNewGroup = true;
            this.Groups[_indexGroup].isNewGroup = false;
          }
        }else {
          let _indexGroup = _.findIndex(this.GroupsMessenger, ['GroupID', this.tabsChat[key].GroupID]);
          if (_indexGroup > -1 && this.GroupsMessenger[_indexGroup].isNewGroup) {
            checkNewGroup = true;
            this.GroupsMessenger[_indexGroup].isNewGroup = false;
          }
        }

        if ((lastMessage && lastMessage.classList.contains('unread')) || checkNewGroup) {
          let self = this;
          let requestData = {
            method: 'post',
            url: 'extensions/api/chat/read-message',
            data: {
              GroupID: this.tabsChat[key].GroupID,
            }
          };

          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;
            if (responsesData.status === 1 || checkNewGroup) {
              $(lastMessage).removeClass('unread');
              $(lastMessage).closest('.user-unread').removeClass('user-unread');
              self.changeNewMessage('-');

              let indexOther = _.findIndex(self.UsersOther, ['GroupID', self.tabsChat[key].GroupID]);
              if (indexOther > -1) {
                self.UsersOther[indexOther].read = true;
              }

              let indexGroup = _.findIndex(self.Groups, ['GroupID', self.tabsChat[key].GroupID]);
              if (indexGroup > -1) {
                self.Groups[indexGroup].read = true;
              }

              if (self.GroupsMessenger && self.GroupsMessenger.length) {
                let indexGroupMessenger = _.findIndex(self.GroupsMessenger, ['GroupID', self.tabsChat[key].GroupID]);
                if (indexGroupMessenger > -1) {
                  self.GroupsMessenger[indexGroupMessenger].read = true;
                }
              }

              socket.emit('read message', {
                GroupID: self.tabsChat[key].GroupID,
                GroupType: self.tabsChat[key].GroupType,
                UserID: self.CurrentUser.UserID
              });

              if (responsesData.data) {
                let dataSeen = responsesData.data;
                dataSeen.GroupID = self.tabsChat[key].GroupID;
                dataSeen.GroupType = self.tabsChat[key].GroupType;
                dataSeen.UserIDSeen = self.CurrentUser.UserID;
                dataSeen.UserIDSocket = self.tabsChat[key].UserID;
                socket.emit('seen message', dataSeen);
              }

              self.$forceUpdate();
            }
          }, (error) => {
            console.log(error);
            Swal.fire({
              title: 'Thông báo',
              text: 'Không kết nối được với máy chủ',
              confirmButtonText: 'Đóng'
            });
          });
        }
      }
    },

    onUpdateGroupName(group){
      let indexTabChat = _.findIndex(this.tabsChat, ['GroupID', group.GroupID]);
      if (indexTabChat > -1) {
        let tmpTabChat = this.tabsChat[indexTabChat];
        tmpTabChat.GroupName = group.GroupName;
        this.tabsChat[indexTabChat] = tmpTabChat;
      }
      let indexGroup = _.findIndex(this.Groups, ['GroupID', group.GroupID]);
      if (indexGroup > -1) {
        let tmpGroup = this.Groups[indexGroup];
        tmpGroup.GroupName = group.GroupName;
        this.Groups[indexGroup] = tmpGroup;
      }

      // messenger
      if (this.GroupsMessenger && this.GroupsMessenger.length) {
        let indexGroupMessenger = _.findIndex(this.GroupsMessenger, ['GroupID', group.GroupID]);
        if (indexGroupMessenger > -1) {
          let tmpGroupMessenger = this.GroupsMessenger[indexGroupMessenger];
          tmpGroupMessenger.GroupName = group.GroupName;
          this.GroupsMessenger[indexGroupMessenger] = tmpGroupMessenger;
        }
      }
      this.$forceUpdate();
    },

    openMessenger() {
      this.tabsChat = [];
      this.stage.showSidebar = false;
    },

    openSocial() {
      this.$router.push({
        name: 'apps-chat-social'
      });
    },
    onReloadUserOnline(){
      socket.emit('user online');
    },
    sortGroups() {
      let self = this;
      let onlineObj = this.$store.state.chat.online;
      _.forEach(this.UsersInterested, function (group, key) {
        if (onlineObj[group.UserID]) {
          self.UsersInterested[key].online = onlineObj[group.UserID];
        } else {
          self.UsersInterested[key].online = false;
        }
      });
      self.UsersInterested = _.orderBy(self.UsersInterested, ['ChatContentID'], ['desc']);

      _.forEach(this.UsersOther, function (group, key) {
        if (onlineObj[group.UserID]) {
          self.UsersOther[key].online = onlineObj[group.UserID];
        } else {
          self.UsersOther[key].online = false;
        }
      });
      self.UsersOther = _.orderBy(self.UsersOther, ['online', 'GroupName'], ['desc', 'asc']);

      self.Groups = _.orderBy(self.Groups, ['ChatContentID'], ['desc']);
      // messenger
      if (self.GroupsMessenger && self.GroupsMessenger.length) {
        self.GroupsMessenger = _.orderBy(self.GroupsMessenger, ['ChatContentID'], ['desc']);
      }

      this.$forceUpdate();
    },
    onDeleteGroup(group){
      let indexTabChat = _.findIndex(this.tabsChat, ['GroupID', group.GroupID]);
      if (indexTabChat > -1) {
        this.onRemoveTabChat(indexTabChat);
      }
      _.remove(this.Groups, ['GroupID', group.GroupID]);
      _.remove(this.GroupsMessenger, ['GroupID', group.GroupID]);

      socket.emit('delete group', {
        GroupID: group.GroupID,
        group: group
      });

      this.$forceUpdate();
    },

    notifyMessage(message) {
      if (!window.Notification) {
        console.log('Browser does not support notifications.');
      } else {
        // check if permission is already granted
        if (Notification.permission === 'granted') {
          // show notification here
          let content = __.stripHtml(message.Content);
          let notify = new Notification(message.UserName, {
            body: content,
            icon: '/img/favicon.png'
          });
          setTimeout(function(){
            notify.close();
          },5000);
        }
      }
    },
  },
  watch:{}
}
