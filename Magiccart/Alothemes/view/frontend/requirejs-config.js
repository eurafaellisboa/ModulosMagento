var config = {

	map: {
		'*': {
			'alothemes': 'Magiccart_Alothemes/js/alothemes',
		},
	},

	paths: {
		'magiccart/easing'			: 'Magiccart_Alothemes/js/plugins/jquery.easing.min',
		'magiccart/parallax'		: 'Magiccart_Alothemes/js/plugins/jquery.parallax',
		'magiccart/socialstream'	: 'Magiccart_Alothemes/js/plugins/jquery.socialstream',
		'magiccart/zoom'			: 'Magiccart_Alothemes/js/plugins/jquery.zoom.min',
		'magiccart/bootstrap'		: 'Magiccart_Alothemes/js/plugins/bootstrap.min',
		'magiccart/slick'			: 'Magiccart_Alothemes/js/plugins/slick.min',
		'magiccart/lazyload'		: 'Magiccart_Alothemes/js/plugins/lazyload.min',
		'magiccart/sticky'		: 'Magiccart_Alothemes/js/plugins/sticky-kit.min',
		'magiccart/woo'				: 'Magiccart_Alothemes/js/plugins/woo.min',
		// 'alothemes'		: 'Magiccart_Alothemes/js/alothemes',
	},

	shim: {
		'magiccart/easing': {
			deps: ['jquery']
		},
		'magiccart/bootstrap': {
			deps: ['jquery']
		},
		'magiccart/parallax': {
			deps: ['jquery']
		},
		'magiccart/socialstream': {
			deps: ['jquery']
		},
		'magiccart/slick': {
			deps: ['jquery']
		},
		'magiccart/zoom': {
			deps: ['jquery']
		},
		'magiccart/lazyload': {
			deps: ['jquery']
		},
		'magiccart/sticky': {
			deps: ['jquery']
		},
		'magiccart/woo': {
			deps: ['jquery']
		},
        'alothemes': {
            deps: ['jquery', 'magiccart/easing', 'magiccart/slick' , 'magiccart/zoom']
        },

	}

};
