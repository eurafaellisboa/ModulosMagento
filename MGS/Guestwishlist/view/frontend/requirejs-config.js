var config = {
	map: {
        '*': {
            'mgs/ajaxwishlist'  : 'MGS_Guestwishlist/js/ajax-wishlist',
			'mage/dataPost': 'MGS_Guestwishlist/js/mage/dataPost'
        }
    },
    config: {
        mixins: {
            'Magento_Wishlist/js/add-to-wishlist': {
                'MGS_Guestwishlist/js/add-to-wishlist': true
            },
            'Magento_Wishlist/js/view/wishlist': {
                'MGS_Guestwishlist/js/view/guest-wishlist': true
            }
        }
    }
};