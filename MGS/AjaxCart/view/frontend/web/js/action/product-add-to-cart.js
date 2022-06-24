require([
  "jquery",
  "MGS_AjaxCart/js/config",
  "Magento_Ui/js/modal/modal"
], function($, mgsConfig, modal) {
  $(document).on("click", "#product-addtocart-button", function() {
    var $_this = jQuery(this);
    var form = jQuery("#product_addtocart_form");
    var isValid = form.valid();
    if (isValid) {
      $_this.addClass("disabled tocart-loading");
      var data = form.serializeArray();
      var formData = new FormData();
      for (var i = 0; i < data.length; i++) {
        formData.append(data[i].name, data[i].value);
      }
      var files = $('input[type="file"]');
      files.each(function(index) {
        formData.append(files[index].name, files[index].files[0]);
      });
      formData.append("ajax", 1);
      jQuery.ajax({
        url: form.attr("action"),
        data: formData,
        type: "post",
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function(response, status) {
          $_this.removeClass("disabled tocart-loading");
          if (response.backUrl) {
            location.reload();
          }
          if (status == "success") {
            if (response.ui) {
              if (response.animationType == "popup") {
                if ($(document).find(".ajaxCartForm").length) {
                  $(document)
                    .find(".ajaxCartForm .modal-header .action-close")
                    .click();
                }

                $("body").append(
                  '<div id="popup_ajaxcart_success" class="popup__main popup--result"></div>'
                );

                var options = {
                  type: "popup",
                  modalClass: "success-ajax--popup viewBox",
                  responsive: true,
                  innerScroll: true,
                  title: false,
                  buttons: false
                };

                var popup = modal(options, $("#popup_ajaxcart_success"));
                $("#popup_ajaxcart_success").html(
                  response.ui + response.related
                );
                $("#popup_ajaxcart_success").trigger("contentUpdated");
                $("#popup_ajaxcart_success")
                  .modal("openModal")
                  .on("modalclosed", function() {
                    $("#popup_ajaxcart_success")
                      .parents(".success-ajax--popup")
                      .remove();
                  });
              } else if (response.animationType == "flycart") {
                var $animatedObject = jQuery(
                  '<div class="flycart-animated-add" style="position: absolute;z-index: 99999;">' +
                    response.image +
                    "</div>"
                );

                var left = $_this.offset().left;
                var top = $_this.offset().top;

                $animatedObject.css({
                  top: top - 1,
                  left: left - 1
                });
                jQuery("html").append($animatedObject);

                if (jQuery(window).width() > 1199) {
                  jQuery("#footer-cart-trigger").addClass("active");
                  jQuery("#footer-mini-cart").slideDown(300);
                }

                var gotoX = jQuery("#fixed-cart-footer").offset().left + 20;
                var gotoY = jQuery("#fixed-cart-footer").offset().top;

                if ($(document).find(".ajaxCartForm").length) {
                  $(document)
                    .find(".ajaxCartForm .modal-header .action-close")
                    .click();
                }

                $animatedObject.animate(
                  {
                    opacity: 0.6,
                    left: gotoX,
                    top: gotoY
                  },
                  2000,
                  function() {
                    $animatedObject.fadeOut("fast", function() {
                      $animatedObject.remove();
                      jQuery("html").removeClass("add-item-success");
                    });
                  }
                );
              } else {
                $(".action.showcart").trigger("click");
              }
            }
          }
        }
      });
      return false;
    } else {
      jQuery("#product-addtocart-button").removeClass(
        "disabled tocart-loading"
      );
    }
  });
});
