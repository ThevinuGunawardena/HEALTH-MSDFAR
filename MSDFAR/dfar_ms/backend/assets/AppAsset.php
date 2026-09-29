<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'themeAssets/css/theme.min.css',
        'themeAssets/fonts/fontawesome/css/all.min.css',
        'css/site.css',
        "themeAssets/css/sweet-alert.css",
    ];
    public $js = [
//        "js/jquery.min.js",
        "themeAssets/libs/bootstrap/dist/js/bootstrap.bundle.min.js",
        "themeAssets/libs/jquery-slimscroll/jquery.slimscroll.min.js",
        "themeAssets/libs/jquery-sparkline/jquery.sparkline.min.js",
        "themeAssets/libs/chartist/dist/chartist.min.js",
        "themeAssets/libs/chartist-plugin-threshold/dist/chartist-plugin-threshold.min.js",
        "themeAssets/libs/raphael/raphael.min.js",
        "themeAssets/libs/morris.js/morris.min.js",
        "themeAssets/libs/gaugeJS/dist/gauge.min.js",
        "themeAssets/libs/chart.js/dist/Chart.bundle.min.js",
        "themeAssets/libs/c3/c3.min.js",
        "themeAssets/libs/d3/dist/d3.min.js",
        "themeAssets/libs/multiselect/js/jquery.multi-select.js",
        "themeAssets/libs/sortablejs/Sortable.min.js",
        "themeAssets/libs/jquery-nestable/jquery.nestable.js",
        "themeAssets/libs/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js",
        "themeAssets/libs/daterangepicker/moment.min.js",
        "themeAssets/libs/daterangepicker/daterangepicker.js",
        "themeAssets/libs/datatables.net/js/jquery.dataTables.min.js",
        "themeAssets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js",
        "themeAssets/libs/jszip/dist/jszip.min.js",

        // "themeAssets/libs/pdfmake/build/vfs_fonts.js",
        // "themeAssets/libs/datatables.net-buttons/js/buttons.html5.min.js",
        "themeAssets/libs/datatables.net-buttons/js/buttons.print.min.js",
        "themeAssets/libs/datatables.net-buttons/js/buttons.colVis.min.js",
        "themeAssets/libs/datatables.net-rowgroup/js/dataTables.rowGroup.min.js",
        "themeAssets/libs/datatables.net-select/js/dataTables.select.min.js",
        "themeAssets/libs/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js",
        "themeAssets/libs/jvectormap/jquery-jvectormap.min.js",
        // "themeAssets/libs/jvectormap/tests/themeAssets/jquery-jvectormap-world-mill-en.js",
        "themeAssets/libs/bootstrap-select/dist/js/bootstrap-select.min.js",
        "themeAssets/libs/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js",
        // 'node_modules/fullcalendar/dist/fullcalendar.min.js',
        // 'node_modules/jquery-ui-dist/jquery-ui.min.js',
        "themeAssets/libs/jquery-asColor/dist/jquery-asColor.min.js",
        "themeAssets/libs/jquery-asGradient/dist/jquery-asGradient.min.js",
        "themeAssets/libs/jquery-asColorPicker/dist/jquery-asColorPicker.min.js",
        // 'node_modules/@claviska/jquery-minicolors/jquery.minicolors.min.js',
        // "themeAssets/libs/cropper/dist/cropper.min.js",
        "themeAssets/libs/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.min.js",
        "themeAssets/libs/select2/dist/js/select2.min.js",
        // "themeAssets/libs/summernote/dist/summernote-bs4.min.js",
        "themeAssets/libs/inputmask/dist/jquery.inputmask.min.js",
        "themeAssets/libs/parsleyjs/dist/parsley.min.js",
        "themeAssets/libs/prismjs/prism.js",
        "themeAssets/libs/datatables.net-buttons/js/dataTables.buttons.min.js",
        "themeAssets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js",
        "themeAssets/libs/gmaps/gmaps.min.js",
        "themeAssets/libs/jvectormap/jquery-jvectormap.min.js",
        // "themeAssets/libs/jvectormap/tests/themeAssets/jquery-jvectormap-world-mill-en.js",
        "themeAssets/libs/ika.jvectormap/jquery-jvectormap-us-aea-en.js",
        "themeAssets/js/theme.min.js",
//        "js/js/bootstrap-datepicker.min.js",
//        "js/js/choosen.js",
        "themeAssets/js/sweet-alert.min.js",
        // "js/jquery.min.js",
//        "js/jquery-ui.min.js",
//        "js/jquery.signature.js",
        "themeAssets/js/customv2.js",
        "jquery/scientificv5.js",

    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];
}
