// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import './polyfill';
// import cssVars from 'css-vars-ponyfill'
import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue';
import App from './App';
import router from './router/index';
import Money from 'v-money';

// global config
window._ = require('lodash');
window.axios = require('axios');

// todo
// cssVars()

// custom
const requireComponent = require.context(
    // The relative path of the components folder
    '../../../ijcore/ijcore_1.0/components/common',
    // Whether or not to look in subfolders
    false,
    // The regular expression used to match base component filenames
    /Ijcore[A-Z]\w+\.(vue|js)$/
);

requireComponent.keys().forEach(fileName => {
  // Get component config
  const componentConfig = requireComponent(fileName)

  // Get PascalCase name of component
  const componentName = _.upperFirst(
      _.camelCase(
          // Gets the file name regardless of folder depth
          fileName
              .split('/')
              .pop()
              .replace(/\.\w+$/, '')
      )
  );


  // Register component globally
  Vue.component(
      componentName,
      // Look for the component options on `.default`, which will
      // exist if the component was exported with `export default`,
      // otherwise fall back to module's root.
      componentConfig.default || componentConfig
  )
});
// end custom

//global component
Vue.use(BootstrapVue);
Vue.use(Money);

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: {
    App
  }
});
