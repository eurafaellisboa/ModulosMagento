/*eslint-disable */
/* jscs:disable */

function _inheritsLoose(subClass, superClass) { subClass.prototype = Object.create(superClass.prototype); subClass.prototype.constructor = subClass; _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

define(["jquery", "knockout", "Magento_PageBuilder/js/events", "Magento_PageBuilder/js/content-type-menu/hide-show-option", "Magento_PageBuilder/js/uploader", "Magento_PageBuilder/js/content-type/preview", "Magento_PageBuilder/js/content-type-menu/option", "mage/translate", "underscore"], function (_jquery, _knockout, _events, _hideShowOption, _uploader, _preview, _option, _translate, _underscore) {

  var Preview = /*#__PURE__*/function (_preview2) {
    "use strict";

    _inheritsLoose(Preview, _preview2);

    function Preview() {
      var _this_preview;
      _this_preview = _preview2.apply(this, arguments) || this;
      _this_preview.buildLazySizes = _underscore.debounce(function () {
        var $imgElem = (0, _jquery)(_this_preview.wrapperElement).find(".data-bgset-image-wrapper");
        var data_src = $imgElem.data("bgset");
        var getDesktopImageData = this.data.desktop_image;
        var imageAttributes = getDesktopImageData.attributes();
        if(imageAttributes["data-bgset"] && imageAttributes["data-bgset"] != ''){
          data_src = imageAttributes["data-bgset"];
        }
        $imgElem.css('background-image', 'url(' + data_src + ')');
      }, 50);
      return _this_preview;
    }

    var _proto = Preview.prototype;

    _proto.retrieveOptions = function retrieveOptions() {
      var options = _preview2.prototype.retrieveOptions.call(this);
      delete options.move;
      delete options.title;
      delete options.duplicate;
      return options;
    };

    _proto.getUploader = function getUploader() {
      var initialImageValue = this.contentType.dataStore.get(this.config.additional_data.uploaderConfig.dataScope, "");
      return new _uploader("imageuploader_" + this.contentType.id, this.config.additional_data.uploaderConfig, this.contentType.id, this.contentType.dataStore, initialImageValue);
    };

    _proto.getViewportImageData = function getViewportImageData() {
      var desktopImageData = this.data.desktop_image;
      return desktopImageData;
    };

    _proto.getAspectRatio = function getAspectRatio() {
      var getInfoImg = this.data.desktop_image;
      var imageAttrInfoImg = getInfoImg.attributes();
      if(imageAttrInfoImg["data-width"] && imageAttrInfoImg["data-width"] != ''){
        var w_ = parseFloat(imageAttrInfoImg["data-width"]);
        var h_ = parseFloat(imageAttrInfoImg["data-height"]);
        var ca_w_h_ = w_ / h_;
        return ca_w_h_.toString();
      }
      return '1';
    };

    _proto.checkHaveUploadImageData = function checkHaveUploadImageData() {
      var desktopImageDataSelect = this.data.desktop_image;
      var imageAttr = desktopImageDataSelect.attributes();
      if(imageAttr["data-bgset"] && imageAttr["data-bgset"] != ''){
        return true;
      }
      return false;
    };

    _proto.onClick = function onClick(index, event) {
      event.stopPropagation();
    };

    _proto.onFocusOut = function onFocusOut(index, event) {
      if (this.contentType && this.contentType.parentContentType) {
        var parentPreview = this.contentType.parentContentType.preview;

        var unfocus = function unfocus() {
          window.getSelection().removeAllRanges();
          parentPreview.focusedItemImg(null);
        };

        if (event.relatedTarget && _jquery.contains(parentPreview.wrapperElement, event.relatedTarget)) {
          // Verify the focus was not onto the options menu
          if ((0, _jquery)(event.relatedTarget).closest(".pagebuilder-options").length > 0) {
            unfocus();
          } else {
            // Have we moved the focus onto another button in the current group?
            var buttonItem = _knockout.dataFor(event.relatedTarget);

            if (buttonItem && buttonItem.contentType && buttonItem.contentType.parentContentType && buttonItem.contentType.parentContentType.id === this.contentType.parentContentType.id) {
              var newIndex = buttonItem.contentType.parentContentType.children().indexOf(buttonItem.contentType);
              parentPreview.focusedItemImg(newIndex);
            } else {
              unfocus();
            }
          }
        } else if (parentPreview.focusedItemImg() === index) {
          unfocus();
        }
      }
    };

    _proto.onFocusIn = function onFocusIn(index, event) {
      var parentPreview = this.contentType.parentContentType.preview;

      if (parentPreview.focusedItemImg() !== index) {
        parentPreview.focusedItemImg(index);
      }
    };

    _proto.onButtonMouseOver = function onButtonMouseOver(context, event) {
      if (this.display() === false) {
        this.onMouseOver(context, event);
      }
    };

    _proto.onButtonMouseOut = function onButtonMouseOut(context, event) {
      if (this.display() === false) {
        this.onMouseOut(context, event);
      }
    };

    _proto.bindEvents = function bindEvents() {
      var _this = this;

      _preview2.prototype.bindEvents.call(this);

      _events.on("itemimg:mountAfter", function (args) {
        if (args.id === _this.contentType.id) {
          _this.buildLazySizes();
          _this.isSnapshot.subscribe(function (value) {
            _this.changeUploaderControlsVisibility();
          });

          _this.changeUploaderControlsVisibility();
        }
      });

      _events.on(this.config.name + ":" + this.contentType.id + ":updateAfter", function () {
        var files = _this.contentType.dataStore.get(_this.config.additional_data.uploaderConfig.dataScope);

        var imageObject = files ? files[0] : {};
        _events.trigger("image:" + _this.contentType.id + ":assignAfter", imageObject);
        _this.buildLazySizes();
      });
    }
    /**
     * Change uploader controls visibility
     */
    ;

    _proto.changeUploaderControlsVisibility = function changeUploaderControlsVisibility() {
      var _this2 = this;

      this.getUploader().getUiComponent()(function (uploader) {
        uploader.visibleControls = !_this2.isSnapshot();
      });
    };

    return Preview;
  }(_preview);

  return Preview;
});
//# sourceMappingURL=preview.js.map