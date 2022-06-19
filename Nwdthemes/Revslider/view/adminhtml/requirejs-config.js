var config = {
    paths: {
        revsliderAdmin:         'Nwdthemes_Revslider/admin/assets/js/admin.min',
        revsliderEditor:        'Nwdthemes_Revslider/admin/assets/js/editor.min',
        rs6editor:              'Nwdthemes_Revslider/admin/assets/js/rs6editor.min',
        help:                   'Nwdthemes_Revslider/admin/assets/js/modules/help',
        overview:               'Nwdthemes_Revslider/admin/assets/js/modules/overview',
        tooltip:                'Nwdthemes_Revslider/admin/assets/js/modules/tooltip',
        clipboard:              'Nwdthemes_Revslider/admin/assets/js/plugins/clipboard.min',
        codemirror:             'Nwdthemes_Revslider/admin/assets/js/plugins/codemirror',
        pennerEasing:           'Nwdthemes_Revslider/admin/assets/js/plugins/penner-easing',
        wavesurfer:             'Nwdthemes_Revslider/admin/assets/js/plugins/wavesurfer',
        revsliderColorPicker:   'Nwdthemes_Revslider/framework/js/color-picker.min',
        galleryBrowser:         'Nwdthemes_Revslider/framework/js/browser',
        iris:                   'Nwdthemes_Revslider/framework/js/iris.min',
        jQueryUI:               'Nwdthemes_Revslider/framework/js/jquery-ui.min',
        loading:                'Nwdthemes_Revslider/framework/js/loading',
        wpUtil:                 'Nwdthemes_Revslider/framework/js/wp-util.min',
        prototype:              'legacy-build.min'
    },
    shim: {
        galleryBrowser: {
            deps: ['Magento_Variable/variables']
        },
        revsliderColorPicker: {
            deps: ['jQueryUI', 'iris']
        },
        iris: {
            deps: ['jQueryUI']
        },
        jQueryUI: {
            deps: ['jquery']
        },
        loading: {
            deps: ['jquery', 'revolutionTools'],
            exports: 'showWaitAMinute'
        },
        wpUtil: {
            deps: ['jquery', 'underscore']
        },
        rs6editor: {
            deps: ['jquery', 'revolutionTools', 'revsliderAdmin']
        }
    }
};