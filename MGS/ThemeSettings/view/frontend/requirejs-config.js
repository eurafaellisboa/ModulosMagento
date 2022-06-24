var config = {
	"map": {
		"*": {
			"mlazyload": "MGS_ThemeSettings/js/jquery.lazyload",
            "mgsvisible": "MGS_ThemeSettings/js/element_visible",
			"mrotateImage": "MGS_ThemeSettings/js/j360"
		}
	},

	"paths": {  
		"mlazyload": "MGS_ThemeSettings/js/jquery.lazyload",
        "mgsvisible": "MGS_ThemeSettings/js/element_visible",
		"mrotateImage": "MGS_ThemeSettings/js/j360"
	},   
    "shim": {
		"MGS_ThemeSettings/js/jquery.lazyload": ["jquery"],
        "MGS_ThemeSettings/js/element_visible": ["jquery"],
		"MGS_ThemeSettings/js/j360": ["jquery"]
	},
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'MGS_ThemeSettings/js/swatch-renderer': true
            }
        }
    }
};