let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js');
mix.sass('resources/assets/sass/app.scss', 'public/css');

mix.styles([
    'public/css/custom.css',
    // 'public/css/app.css',
    'resources/assets/sass/responsive.bootstrap.min.css',
    'node_modules/font-awesome/css/font-awesome.css',
    'node_modules/sweetalert2/dist/sweetalert2.css',
    'node_modules/selectize/dist/css/selectize.bootstrap3.css',

    // BEGIN GLOBAL MANDATORY STYLES
    'resources/assets/global/plugins/font-awesome/css/font-awesome.min.css',
    'resources/assets/global/plugins/simple-line-icons/simple-line-icons.min.css',
    'resources/assets/global/plugins/bootstrap/css/bootstrap.min.css',
    'resources/assets/global/plugins/uniform/css/uniform.default.css',
    'resources/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css',
    // END GLOBAL MANDATORY STYLES

    // BEGIN PAGE LEVEL PLUGIN STYLES
    'resources/assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css',
    'resources/assets/global/plugins/fullcalendar/fullcalendar.min.css',
    'resources/assets/global/plugins/jqvmap/jqvmap/jqvmap.css',
    'resources/assets/global/plugins/morris/morris.css',
    // END PAGE LEVEL PLUGIN STYLES

    // BEGIN PAGE STYLES
    'resources/assets/admin/pages/css/tasks.css',
    // END PAGE STYLES

    // BEGIN THEME STYLES
    // DOC: To use 'rounded corners' style just load 'components-rounded.css' stylesheet instead of 'components.css' in the below style tag
    'resources/assets/global/css/components-md.css',
    'resources/assets/global/css/plugins-md.css',
    'resources/assets/admin/layout4/css/layout.css',
    'resources/assets/admin/layout4/css/themes/light.css',
    'resources/assets/admin/layout4/css/custom.css',
    // END THEME STYLES
    
    // BEGIN PAGE LEVEL STYLES
    // 'resources/assets/global/plugins/select2/select2.css',
    'resources/assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css',
    'resources/assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css',
    'resources/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css',
    // END PAGE LEVEL STYLES
], 'public/css/all.css');

mix.scripts([
    // BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time)
    // BEGIN CORE PLUGINS
    'resources/assets/global/plugins/respond.min.js',
    'resources/assets/global/plugins/excanvas.min.js',
    'resources/assets/global/plugins/jquery.min.js',
    'resources/assets/global/plugins/jquery-migrate.min.js',
    // IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip
    'resources/assets/global/plugins/jquery-ui/jquery-ui.min.js',
    'resources/assets/global/plugins/bootstrap/js/bootstrap.min.js',
    'resources/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js',
    'resources/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js',
    'resources/assets/global/plugins/jquery.blockui.min.js',
    'resources/assets/global/plugins/jquery.cokie.min.js',
    'resources/assets/global/plugins/uniform/jquery.uniform.min.js',
    'resources/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js',
    // END CORE PLUGINS

    // BEGIN PAGE LEVEL PLUGINS
    'resources/assets/global/plugins/jqvmap/jqvmap/jquery.vmap.js',
    'resources/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.russia.js',
    'resources/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.world.js',
    'resources/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.europe.js',
    'resources/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.germany.js',
    'resources/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.usa.js',
    'resources/assets/global/plugins/jqvmap/jqvmap/data/jquery.vmap.sampledata.js',
    // IMPORTANT! fullcalendar depends on jquery-ui.min.js for drag & drop support
    'resources/assets/global/plugins/morris/morris.min.js',
    'resources/assets/global/plugins/morris/raphael-min.js',
    'resources/assets/global/plugins/jquery.sparkline.min.js',
    // END PAGE LEVEL PLUGINS

    // BEGIN PAGE LEVEL SCRIPTS
    'resources/assets/global/scripts/metronic.js',
    'resources/assets/admin/layout4/scripts/layout.js',
    'resources/assets/admin/layout2/scripts/quick-sidebar.js',
    'resources/assets/admin/layout4/scripts/demo.js',
    'resources/assets/admin/pages/scripts/index3.js',
    'resources/assets/admin/pages/scripts/tasks.js',
    // END PAGE LEVEL SCRIPTS

    // BEGIN PAGE LEVEL PLUGINS
    // 'resources/assets/global/plugins/select2/select2.min.js',
    'resources/assets/global/plugins/datatables/media/js/jquery.dataTables.min.js',
    'resources/assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js',
    'resources/assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js',
    'resources/assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js',
    'resources/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js',
    // END PAGE LEVEL PLUGINS
    
    // 'resources/assets/admin/pages/scripts/table-advanced.js',
    
    // 'node_modules/jquery/dist/jquery.js',
    'node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js',
    'node_modules/sweetalert2/dist/sweetalert2.min.js',
    'node_modules/selectize/dist/js/standalone/selectize.min.js',
    'resources/assets/js/sweet.js',
    'resources/assets/js/selectize.js',
    'resources/assets/js/application.js',
    'resources/assets/js/jquery.dataTables.min.js',
    'resources/assets/js/dataTables.responsive.min.js',
], 'public/js/all.js');

mix.copy('node_modules/font-awesome/fonts', 'public/fonts');
mix.copy('node_modules/font-awesome/fonts', 'public/build/fonts');

mix.copy('resources/assets/global/plugins/simple-line-icons/fonts', 'public/css/fonts');