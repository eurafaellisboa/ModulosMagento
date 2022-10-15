function qtyChange(event){


    if(jQuery(event.target).val() == 'inc') { /* qty increase */
        var oldVal = jQuery(event.target).prev().val();

        if ( parseFloat(oldVal) >= 0 ) {

            var newVal = parseFloat(oldVal) + 1;

            jQuery(event.target).prev().val(newVal);

            jQuery(event.target).next().show();

        }
    }else{ /* qty decrease */

        var oldVal = jQuery(event.target).next().val();

        if (parseFloat(oldVal) >= 2) {

            var newVal = parseFloat(oldVal) - 1;

            jQuery(event.target).next().val(newVal);

            jQuery(event.target).next().next().next().show();

        }
    }



}