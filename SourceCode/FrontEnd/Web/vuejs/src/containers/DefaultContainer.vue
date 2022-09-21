<template>
  <div class="app">
    <AppHeader fixed>
      <SidebarToggler class="d-lg-none" display="md" mobile />
      <b-link class="navbar-brand" to="">
        <span class="navbar-brand-full" style="font-size: 21px; font-weight: bold;" @click="redirectToHome">PFMIS</span>
<!--        <img class="navbar-brand-full" src="img/logo.png" height="75%" alt="CoreUI Logo">-->
        <img class="navbar-brand-minimized" src="img/favicon.png" width="75" height="30" alt="CoreUI Logo">
      </b-link>
      <SidebarToggler class="d-md-down-none d-lg-none" display="lg" :defaultOpen=true />

<!--      <b-navbar-nav class="d-md-down-none">-->
<!--        <b-nav-item class="px-3" to="/dashboard">Dashboard</b-nav-item>-->
<!--        <b-nav-item class="px-3" to="/users" exact>Users</b-nav-item>-->
<!--        <b-nav-item class="px-3">Settings</b-nav-item>-->
<!--      </b-navbar-nav>-->
<!--      <component :is="$store.state.moduleNavHeader"/>-->
      <navbar-top></navbar-top>
      <b-navbar-nav class="ml-auto">
        <b-nav-item class="d-md-down-none">
          <i class="fa fa-clock-o" style="font-size: 18px;" aria-hidden="true" :title="'Ngày làm việc: '+workdate" @click="onShowWorkDate"></i>
          <b-modal ref="workdate"  scrollable id="modal-workdate" size="sm">
            <template slot="modal-title">
              <i class="fa fa-edit"></i> Ngày làm việc
            </template>
            <div class="row">
              <div class="col-24 mb-2">
                <IjcoreDatePicker v-model="workdatetemp" style="width: 120px;"></IjcoreDatePicker>
              </div>
              <div class="col-24 d-flex align-items-center">
                <b-form-group class="mb-0" label-for="basic-inline-checkboxes">
                  <b-form-checkbox v-model="isWorkdate" :plain="true" value="1" @change="onChangeIsWorkdate">Là ngày hệ thống</b-form-checkbox>
                </b-form-group>
              </div>
            </div>
            <template v-slot:modal-footer>
              <div class="w-100 left">
                <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdateWorkdate()">
                  Thay đổi
                </b-button>
              </div>
            </template>
          </b-modal>
        </b-nav-item>
        <b-nav-item>
          <span title="Báo cáo" @click="onClickReport"><i class="fa fa-newspaper-o"></i></span>
        </b-nav-item>
        <b-nav-item class="d-md-down-none">
          <DefaultHeaderDropdownTasks/>
        </b-nav-item>
        <b-nav-item class="d-md-down-none">
          <DefaultHeaderDropdownMssgs/>
        </b-nav-item>
        <b-nav-item class="d-md-down-none">
          <DefaultHeaderDropdown/>
        </b-nav-item>
        <li class="nav-item d-md-down-none">
          <div @click="onToggleAside" title="Trò chuyện">
            <i v-b-toggle.chat-sidebar class="fa fa-comments-o chat-icon" aria-hidden="true"></i>
            <span class="badge badge-danger badge-pill" style="z-index: -1;" v-if="$store.state.chat.newMessage">{{$store.state.chat.newMessage}}</span>
          </div>

<!--          <chat-sidebar></chat-sidebar>-->
        </li>
        <b-nav-item class="d-md-down-none">
          <DefaultHeaderDropdownNotif/>
        </b-nav-item>
        <b-nav-item class="d-md-down-none">
          <span class="social-icon" @click="openSocial" title="Mạng xã hội"><i class="fa fa-recycle"></i></span>
        </b-nav-item>
        <b-nav-item class="d-lg-none" @click="routerToMessage">
          <div>
            <i class="fa fa-comments-o" style="font-size: 21px"></i>
            <span class="badge badge-danger badge-pill" v-if="$store.state.chat.newMessage > 0">{{$store.state.chat.newMessage}}</span>
          </div>
        </b-nav-item>
        <b-nav-item class="d-lg-none" @click="routerToNotice">
          <div>
            <i class="fa fa-bell-o" style="font-size: 21px"></i>
            <span class="badge badge-danger badge-pill" v-if="$store.state.notification.total > 0">{{$store.state.notification.total}}</span>
          </div>
        </b-nav-item>
        <DefaultHeaderDropdownAccnt/>
      </b-navbar-nav>
      <AsideToggler class="d-none d-lg-block" style="margin-right: 20px" />
<!--      <AsideToggler class="d-lg-none" mobile />-->
    </AppHeader>
    <div class="app-body">
      <AppSidebar fixed>
        <SidebarHeader/>
        <SidebarForm/>
        <SidebarNav :navItems="nav"></SidebarNav>
<!--        <SidebarFooter/>-->
        <SidebarMinimizer @on-search-nav="onSearchNav"/>
      </AppSidebar>
      <main class="main">
<!--        <Breadcrumb :list="list"/>-->
<!--        <div class="container-fluid">-->
          <router-view></router-view>
<!--        </div>-->
      </main>
      <AppAside fixed>
        <!--aside-->
        <div class="close-aside" @click="onCloseAside">
          <button type="button" aria-label="Close" class="close">×</button>
        </div>
        <DefaultAside/>
      </AppAside>
    </div>
<!--    <TheFooter>-->
<!--      &lt;!&ndash;footer&ndash;&gt;-->
<!--      <div>-->
<!--        <a href="https://coreui.io">CoreUI</a>-->
<!--        <span class="ml-1">&copy; 2018 creativeLabs.</span>-->
<!--      </div>-->
<!--      <div class="ml-auto">-->
<!--        <span class="mr-1">Powered by</span>-->
<!--        <a href="https://coreui.io">CoreUI for Vue</a>-->
<!--      </div>-->
<!--    </TheFooter>-->
    <the-loading-page></the-loading-page>
    <div class="notice-audio-notification hide" style="display: none">
      <audio controls id="notice-audio-notification">
        <source :src="$store.state.appRootApi + '/audio/notification.mp3'" type="audio/ogg">
        <source :src="$store.state.appRootApi + '/audio/notification.mp3'" type="audio/mpeg">
        Your browser does not support the audio element.
      </audio>
    </div>

    <div class="message-audio-notification hide" style="display: none">
      <audio controls id="message-audio-notification">
        <source :src="$store.state.appRootApi + '/audio/message.mp3'" type="audio/ogg">
        <source :src="$store.state.appRootApi + '/audio/message.mp3'" type="audio/mpeg">
        Your browser does not support the audio element.
      </audio>
    </div>

  </div>
</template>

<script>
import nav from '@/_nav';
import { Header as AppHeader, SidebarToggler, Sidebar as AppSidebar, SidebarFooter, SidebarForm, SidebarHeader, Aside as AppAside, AsideToggler, Footer as TheFooter, Breadcrumb } from '@coreui/vue';
import DefaultAside from './DefaultAside';
import DefaultHeaderDropdown from './DefaultHeaderDropdown';
import DefaultHeaderDropdownNotif from './DefaultHeaderDropdownNotif';
import DefaultHeaderDropdownAccnt from './DefaultHeaderDropdownAccnt';
import DefaultHeaderDropdownMssgs from './DefaultHeaderDropdownMssgs';
import DefaultHeaderDropdownTasks from './DefaultHeaderDropdownTasks';
import SidebarMinimizer from './sidebar/SidebarMinimizer';
import SidebarNav from './sidebar/SidebarNav';
import ChatSidebar from "../views/apps/chat/ChatSidebar";
import ApiService from '@/services/api.service';

// ======================
// import NavModuleIjtask from './navbar-module/NavModuleIjtask';
// import NavModuleDefault from './navbar-module/NavModuleDefault';
// import NavModuleSysadmin from './navbar-module/NavModuleSysadmin';

import TheLoadingPage from './partials/TheLoading';
import NavbarTop from './DefaultNavbarTop';
import moment from "moment";
import {TokenService} from "../services/storage.service";
import IjcoreDatePicker from "../components/IjcoreDatePicker";
import {PermissionService} from "../services/permission.service";


export default {
  name: 'DefaultContainer',
  components: {
    IjcoreDatePicker,
    AsideToggler,
    AppHeader,
    AppSidebar,
    AppAside,
    TheFooter,
    Breadcrumb,
    DefaultAside,
    DefaultHeaderDropdown,
    DefaultHeaderDropdownMssgs,
    DefaultHeaderDropdownNotif,
    DefaultHeaderDropdownTasks,
    DefaultHeaderDropdownAccnt,
    SidebarForm,
    SidebarFooter,
    SidebarToggler,
    SidebarHeader,
    SidebarNav,
    SidebarMinimizer,

    // custom
    TheLoadingPage,
    NavbarTop,
    ChatSidebar
    // NavModuleDefault,
    // NavModuleSysadmin
  },
  data () {
    return {
      nav: nav.items,
      workdate: '',
      workdatetemp: '',
      isWorkdate: 0
    }
  },
  mounted() {
    this.$store.watch(
        function (state) {
          return state.moduleNavTop;
        },
        function () {
          //do something on data change
        },
        {
          deep: true //add this if u need to watch object properties change etc.
        }
    );
    this.isWorkdate = (localStorage.getItem('isWorkdate')) ? Number(localStorage.getItem('isWorkdate')) : 0;
    if (this.isWorkdate == 1) {
      this.workdate = TokenService.getWorkdate();
      this.workdatetemp = this.workdate;
    } else {
      this.setWorkdate();
    }
    // this.onToggleAside();
  },
  methods:{
    onShowWorkDate(){
      this.$refs['workdate'].show();
    },
    setWorkdate(){
      let self = this;
      let requestData = {
        method: 'get',
        url: 'listing/api/common/get-workdate',
        data: {}
      };
      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;
        if (responseData.status === 1) {
          self.workdatetemp = __.convertServerDateToClientDate(responseData.data);
          self.workdate = self.workdatetemp;
          TokenService.setWorkdate(self.workdatetemp);
          this.$forceUpdate();
        }
      }, (error) => {});
    },
    onHideWorkDate(){
      this.$refs['workdate'].hide();
    },
    onUpdateWorkdate(){
      if (this.isWorkdate) {
        this.workdate = this.workdatetemp;
        TokenService.setWorkdate(this.workdatetemp);
      }
      this.$refs['workdate'].hide();
    },
    onChangeIsWorkdate() {
      let isWorkdate = (this.isWorkdate) ? this.isWorkdate : 0;
      localStorage.setItem('isWorkdate', isWorkdate);
    },
    openSocial() {
      if (!$('.component-chat-social').length) {
        this.$router.push({
          name: 'apps-chat-social'
        });
      }
    },
    onToggleAside(){
      if ($('body').hasClass('aside-menu-lg-show')) {
        $('body').removeClass('aside-menu-lg-show');
      } else {
        $('body').addClass('aside-menu-lg-show');
        socket.emit('user online');
        this.$store.commit('asideShowChat', true);
      }
    },
    onCloseAside(){
      $('body').removeClass('aside-menu-lg-show');
    },
    redirectToHome() {
      this.$router.push({
        name: 'Home'
      });
    },
    routerToMessage() {
      if (window.innerWidth < 768) {
        $('.chat-messages-mobile').addClass('show-sidebar');
      }
      this.$router.push({
        name: 'apps-chat-message'
      });
    },
    routerToNotice() {
      this.$router.push({
        name: 'apps-notification',
        params: {Status: 0}
      });
    },
    onSearchNav(search) {
      this.nav = [];
      let self = this;
      let permission = PermissionService.getPermission();
      let menuLeft = permission.menuLeftArr;
      _.forEach(menuLeft, function (menu, key) {
        if (!menu.MenuName) {
          menuLeft[key].MenuName = '';
        }
      });

      let navItems = [];
      if (search) {
        let navSearch = _.filter(menuLeft, function (o) {
          let noAccent = __.cleanAccents(o.MenuName);
          let groupNameLower = _.toLower(o.MenuName);
          let noAccentLower = _.toLower(noAccent);

          return o.MenuName.includes(search) || noAccent.includes(search)
            || groupNameLower.includes(search) || noAccentLower.includes(search);
        });

        _.forEach(navSearch, function (item, key) {
          navItems.push({
            icon: item.Icon,
            menuID: item.MenuID,
            module: item.Module,
            name: item.MenuName,
            parentID: item.ParentID,
            url: '/' + item.RouterFrontEnd
          });
        });
        _.forEach(navItems, function (navItem, key) {
          self.nav.push(navItem);
        });
      } else {
        this.nav = nav.items;
      }
      $('.sidebar-nav .scroll-area').scrollTop(0);
    },
    onClickReport(){
      this.$router.push({
        path: '/sysadmin/report'
      })
    }
  },
  computed: {
    name () {
      return this.$route.name
    },
    list () {
      return this.$route.matched.filter((route) => route.name || route.meta.label )
    }
  },
  watch: {

  }
}
</script>

<style>
  .social-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 31px;
    height: 31px;
    cursor: pointer;
  }
  .social-icon i {
    font-size: 16px;
    margin-top: 3px;
  }

  .navbar-nav .chat-icon {
    color: #73818f;
    font-size: 18px;
    cursor: pointer;
  }
  .chat-icon:focus {
    outline: none;
  }
  .app-header .nav-item .badge-pill {
    position: absolute;
    top: 50%;
    left: 50%;
    margin-top: -16px;
    margin-left: 0;
  }
  .close-aside {
    position: absolute;
    right: 10px;
    top: 10px;
    padding: 0 5px;
  }
  .close-aside .close:focus,
  .close-aside .close:active {
    outline: none;
  }

</style>
