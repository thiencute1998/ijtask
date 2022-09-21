<template>
  <AppHeaderDropdown right no-caret>
    <template slot="header">
      <img
        :src="$store.state.appRootApi + currentUser.Avata"
        class="img-avatar"
        :title="currentUser.EmployeeName"
        alt="admin@bootstrapmaster.com" />
    </template>\
    <template slot="dropdown">
      <b-dropdown-header tag="div" class="text-center"><strong>Người dùng</strong></b-dropdown-header>
      <b-dropdown-item @click="routerToMessage"><i class="fa fa-comments-o"/> Tin nhắn mới
        <b-badge :variant="(!$store.state.chat.newMessage) ? 'warning' : 'info'">{{ $store.state.chat.newMessage }}</b-badge>
      </b-dropdown-item>
      <b-dropdown-item @click="routerToNotice"><i class="fa fa-bell-o" /> Thông báo mới
        <b-badge :variant="(!$store.state.notification.total) ? 'warning' : 'info'">{{ $store.state.notification.total }}</b-badge>
      </b-dropdown-item>
<!--      <b-dropdown-item><i class="fa fa-envelope-o" /> Messages-->
<!--        <b-badge variant="success">{{ itemsCount }}</b-badge>-->
<!--      </b-dropdown-item>-->
<!--      <b-dropdown-item><i class="fa fa-tasks" /> Tasks-->
<!--        <b-badge variant="danger">{{ itemsCount }}</b-badge>-->
<!--      </b-dropdown-item>-->
<!--      <b-dropdown-header-->
<!--        tag="div"-->
<!--        class="text-center">-->
<!--        <strong>Settings</strong>-->
<!--      </b-dropdown-header>-->
<!--      <b-dropdown-item><i class="fa fa-wrench" /> Settings</b-dropdown-item>-->
<!--      <b-dropdown-item><i class="fa fa-usd" /> Payments-->
<!--        <b-badge variant="secondary">{{ itemsCount }}</b-badge>-->
<!--      </b-dropdown-item>-->
<!--      <b-dropdown-item><i class="fa fa-file" /> Projects-->
<!--        <b-badge variant="primary">{{ itemsCount }}</b-badge>-->
<!--      </b-dropdown-item>-->
      <b-dropdown-divider />
<!--      <b-dropdown-item><i class="fa fa-shield" /> Lock Account</b-dropdown-item>-->
      <b-dropdown-item @click="changePasswordUser"><i class="fa fa-user" /> Đổi thông tin</b-dropdown-item>
      <b-dropdown-item @click="handleLogout"><i class="fa fa-lock" /> Đăng xuất</b-dropdown-item>
    </template>
  </AppHeaderDropdown>
</template>
<style type="text/css">
  .app-header .nav-item .nav-link > .img-avatar {
    width: 35px !important;
    object-fit: cover;
  }
</style>
<script>
import {HeaderDropdown as AppHeaderDropdown} from '@coreui/vue';
import {mapGetters, mapActions} from 'vuex';
export default {
  name: 'DefaultHeaderDropdownAccnt',
  components: {
    AppHeaderDropdown
  },
  data: () => {
    return {
      itemsCount: 42,
      currentUser: null
    }
  },
  computed: {
    ...mapGetters('auth', [
      'authenticating',
      'authenticationError',
      'authenticationErrorCode'
    ])
  },
  created() {
    this.currentUser = JSON.parse(localStorage.getItem('Employee'));
  },
  methods:{
    ...mapActions('auth', [
      'logout'
    ]),
    handleLogout(){
      socket.emit('user left');
      socket.removeAllListeners();
      this.logout();
    },
    changePasswordUser() {
      this.$router.push({name: 'sysadmin-user-password'});
    },
    routerToMessage() {
      this.$router.push({
        name: 'apps-chat-message'
      });
    },
    routerToNotice() {
      this.$router.push({
        name: 'apps-notification',
        params: {Status: 0}
      });
    }
  }
}
</script>
