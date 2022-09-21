import Vue from 'vue';
import Vuex from 'vuex';
import auth from './modules/auth';

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production';
let noticeInterval = null;
let titleDocument = '';

export default new Vuex.Store({
    state: {
      isLoading: false,
      moduleNavTop: 'DASHBOARD',
      menuTop: {
        dashboard: {
          tabNo: 1
        }
      },
      psSettings: {
        maxScrollbarLength: 200,
        minScrollbarLength: 40,
        suppressScrollX: getComputedStyle(document.querySelector('html')).direction !== 'rtl',
        wheelPropagation: false,
        interceptRailY: styles => ({ ...styles, height: 0 })
      },
      appRootApi: process.env.VUE_APP_ROOT_API,
      optionBehavior: {
          perPage: null,
          viewType: 'list'
      },
      chat: {
        newMessage: 0,
        haveNewMessage: false,
        online: {},
        asideShow: false,
        fontSize: 'lg' // 'sm' | 'md' | lg
      },

      notification: {
        notice: [],
        amount: 0,
        total: 0,
        CategoryID: null,
        TypeCategory: null,
        TypeAction: null,
        reload: false
      },
      socket: {
        disconnect: false
      }
    },
    getters:{},
    actions: {
    },
    mutations: {
      isLoading (state, value) {
          state.isLoading = value;
      },
      moduleNavTop (state, value){
          state.moduleNavTop = value;
      },
      menuTop(state, value){
        if (value && value.hasOwnProperty('dashboard')) {
          state.menuTop.dashboard.tabNo = value.dashboard.tabNo;
        }
      },
      optionBehavior(state, optionObj){
        _.forEach(optionObj, function (value, key) {
          if (!_.isUndefined(state.optionBehavior[key])) state.optionBehavior[key] = value;
        });
      },

      // chat
      newMessage(state, value) {
        state.chat.newMessage = value;

        titleDocument = document.querySelector('title').innerText;
        let pieces = titleDocument.split(' ');
        if (value) {
          titleDocument = '(' + value + ') ' + pieces[pieces.length - 1];
          document.querySelector('title').innerText = titleDocument;
        } else {
          document.querySelector('title').innerText = pieces[pieces.length - 1];
        }

      },
      asideShowChat(state, value){
        state.chat.asideShow = value;
      },
      userOnline(state, value) {
        if (value && !_.isEmpty(value)) {
          state.chat.online[value.UserID] = value.value;
        } else {
          state.chat.online = {};
        }
      },
      setChat(state, value){
        if (value && value.hasOwnProperty('fontSize')) {
          state.chat.fontSize = value.fontSize;
        }

        if (value && value.hasOwnProperty('haveNewMessage')) {
          let checkNewMessage = (!state.chat.haveNewMessage && value.haveNewMessage) ? true : false;
          state.chat.haveNewMessage = value.haveNewMessage;
          setTimeout(() => {
            let jump = false;
            if (state.chat.haveNewMessage && checkNewMessage) {
              noticeInterval = setInterval(() => {
                if (state.chat.haveNewMessage) {
                  if (jump) {
                    document.querySelector('title').innerText = 'Bạn có tin nhắn mới';
                  } else {
                    document.querySelector('title').innerText = titleDocument;
                  }
                  jump = !jump;
                } else {
                  if (noticeInterval) {
                    clearInterval(noticeInterval);
                  }

                  let title = 'Task';
                  if (state.chat.newMessage) {
                    title = '(' + state.chat.newMessage + ') ' + title;
                  }
                  document.querySelector('title').innerText = title;
                }
              }, 1000);
            }
          });
        }
      },

      // notification
      notification(state, value) {
        if (value && value.hasOwnProperty('notice')) {
          state.notification.notice = value.notice;
        }
        if (value && value.hasOwnProperty('total')) {
          state.notification.total = value.total;
        }
        if (value && value.hasOwnProperty('CategoryID')) {
          state.notification.CategoryID = value.CategoryID;
        }
        if (value && value.hasOwnProperty('TypeCategory')) {
          state.notification.TypeCategory = value.TypeCategory;
        }
        if (value && value.hasOwnProperty('TypeAction')) {
          state.notification.TypeAction = value.TypeAction;
        }

        if (value && value.hasOwnProperty('reload')) {
          state.notification.reload = value.reload;
        }
      },
      toggleActionNotification(state, value){
        if (state.notification.notice[value]) {
          state.notification.notice[value].showAction = !state.notification.notice[value].showAction;
        }
      },
      hideActionNotification(state, value){
        if (state.notification.notice[value]) {
          state.notification.notice[value].showAction = false;
        }
      },
      removeNotification(state, value) {
        if (state.notification.notice[value]) {
          state.notification.notice.splice(value, 1);
          state.notification.total -= 1;
        }
      },
      addNotification(state, value) {
        let notice = _.find(state.notification.notice, ['NotificationID', value.NotificationID]);
        if (!notice) {
          state.notification.notice.unshift(value);
          state.notification.total += 1;
        }
      },

      // socket
      socket(state, value){
        if (value && value.hasOwnProperty('disconnect')) {
          state.socket.disconnect = value.disconnect;
        }
      }
    },
    modules: {
        auth
    },
    strict: debug
})
