var Encore = require('@symfony/webpack-encore');

Encore
  // directory where compiled assets will be stored
  .setOutputPath('public/build/')
  // public path used by the web server to access the output path
  .setPublicPath('/build')

  .addEntry('admin/product/index', './assets/js/admin/product/index.js')

  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
;

module.exports = Encore.getWebpackConfig();
