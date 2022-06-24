var config = {
    map: {
        '*': {
            'lazysizes': 'Blueskytechco_PageBuilderCustom/js/resource/lazysizes/lazysizes.min',
            'countdown': 'Blueskytechco_PageBuilderCustom/js/resource/countdown/countdown'
        }
    },
    deps: [
        'lazysizes',
        'Blueskytechco_PageBuilderCustom/js/resource/lazysizes/ls.bgset.min'
    ],
    shim: {
        'Blueskytechco_PageBuilderCustom/js/resource/countdown/countdown': {
            deps: ['jquery']
        },
        'Blueskytechco_PageBuilderCustom/js/resource/lazysizes/lazysizes.min': {
            exports: 'lazySizes',
        }
    }
};