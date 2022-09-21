const path = require('path');
module.exports = {
  publicPath: './',
  lintOnSave: false,
  runtimeCompiler: true,
  // baseUrl: process.env.NODE_ENV === 'production' ? '/prodserver1/' : 'aaaa',
  devServer: {
    /*
    open: process.platform === 'darwin',
    host: '0.0.0.0',
    port: 8080,
    https: false,
    hotOnly: false,
    proxy: null, // string | Object
    before: app => {}
  */
  },

  chainWebpack: config => {
    config.resolve.alias
        .set("@", path.join(__dirname, "./src"))
        .set("@__", path.join(__dirname, '../../../core/test.js'))
        .set("@_ijcore", path.join(__dirname, "./src/modules/ijcore"))
        .set("@_ijcore_1.0", path.join(__dirname, "./src/modules/ijcore/ijcore_1.0"))
        .set("@_coreui", path.join(__dirname, "./src/modules/coreui"))
        .set("@_coreui_1.0", path.join(__dirname, "./src/modules/coreui/ijtask_1.0"))
        .set("@_ijtask", path.join(__dirname, "./src/modules/ijtask"))
        .set("@_ijtask_1.0", path.join(__dirname, "./src/modules/ijtask/ijtask_1.0"));

  },
  // plugins: [
  //   'flowchart'
  // ]
};
