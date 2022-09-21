// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import './polyfill';
// import cssVars from 'css-vars-ponyfill'
import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue';
import App from './App';
import router from './router/index';
import Money from 'v-money';
import ApiService from './services/api.service';
import {TokenService} from './services/storage.service';
import store from './store';
import __ from './helpers';
import VuePerfectScrollbar from 'vue-perfect-scrollbar';
import SidebarToggle from './containers/sidebar/SidebarToggler';
import filters from './mixins/filters';
import methods from "./mixins/methods";
import io from 'socket.io-client';
import Dexie from "dexie";

// global config
window.Swal = require('sweetalert2');
window._ = require('lodash');
window.axios = require('axios');
window.$ = window.jQuery = require('jquery');
window.__ = __;
window.socket = io(process.env.VUE_APP_SOCKET_PORT);

// create schema client database
__.createSchemaClientDB();
// const db = new Dexie('Listing');
// db.version(1).stores({
//   employee: "++ID, EmployeeID, EmployeeNo, EmployeeName",
//   coa_con: "++ID, AccountID, AccountNo, AccountName", //Hợp nhất
//   coa_anu: "++ID, AccountID, AccountName, AccountNo", //Đơn vị HCSN
//   coa_pmu: "++ID, AccountID, AccountName, AccountNo", //Ban QLDA
//   coa_tab: "++ID, AccountID, AccountName, AccountNo", //Kho bạc Nhà nước
//   coa_sna: "++ID, AccountID, AccountName, AccountNo", //Tài Khoản Quốc gia
//   coa_scb: "++ID, AccountID, AccountName, AccountNo", //Xã phường
//   ccy: "++ID, CcyID, CcyName, CcyNo",
//   item: "++ID, ItemID, ItemName, ItemNo",
//   fixed_asset: "++ID, FixedAssetID, FixedAssetName, FixedAssetNo",
//   tool: "++ID, ToolID, ToolName, ToolNo",
//   invest_asset: "++ID, InvestAssetID, InvestAssetName, InvestAssetNo",
//   uom: "++ID, UomID, UomName, UomNo"
// });
//
// const dbReport = new Dexie('Report');
// dbReport.version(1).stores({
//   rpt_report_para: "++ID, LineID, ReportID, ReportName, ParaID, ParaName, ParaValue, ParaValueID, ParaValueName, ParaValueNo, ParaKey, NOrder",
//   rpt_para: "++ID, NOrder, DataType, ParaKey, ParaName, ParaType, Inactive"
// });
//
// db.open();
// dbReport.open();

// todo
// cssVars()

// custom
// const requireComponent = require.context(
//     // The relative path of the components folder
//     './containers/navbar-module',
//     // './modules/ijcore/ijcore_1.0/components/common',
//     // Whether or not to look in subfolders
//     false,
//     // The regular expression used to match base component filenames
//     /NavModule[A-Z]\w+\.(vue|js)$/
// );
//
// // auto register component navbar-module
// requireComponent.keys().forEach(fileName => {
//   // Get component config
//   const componentConfig = requireComponent(fileName);
//
//   // Get PascalCase name of component
//   const componentName = _.upperFirst(
//       _.camelCase(
//           // Gets the file name regardless of folder depth
//           fileName
//               .split('/')
//               .pop()
//               .replace(/\.\w+$/, '')
//       )
//   );
//
//
//   // Register component globally
//   Vue.component(
//       componentName,
//       // Look for the component options on `.default`, which will
//       // exist if the component was exported with `export default`,
//       // otherwise fall back to module's root.
//       componentConfig.default || componentConfig
//   )
// });

// desktop notification : JavaScript Notifications API
if (!window.Notification) {
  console.log('Browser does not support notifications.');
} else {
  // check if permission is already granted
  if (Notification.permission === 'granted') {
    // show notification here
  } else {
    // request permission from user
    Notification.requestPermission().then(function(p) {
      if(p === 'granted') {
        // show notification here
      } else {
        console.log('User blocked notifications.');
      }
    }).catch(function(err) {
      console.error(err);
    });
  }
}


// Set the base URL of the API
ApiService.init(process.env.VUE_APP_ROOT_API);
let Employee = JSON.parse(localStorage.getItem('Employee'));
if (Employee) {
  socket.emit('add user', Employee);
}

// If token exists set header
if (TokenService.getToken()) {
  ApiService.setHeader();
}

// end custom
//global component
Vue.component('vue-perfect-scrollbar', VuePerfectScrollbar);
Vue.component('sidebar-toggle', SidebarToggle);

Vue.use(BootstrapVue);
Vue.use(Money);

Vue.mixin({
  filters: filters,
  methods: methods
});

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: {
    App
  }
});
