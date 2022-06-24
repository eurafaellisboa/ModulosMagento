/*eslint-disable */
/* jscs:disable */

function _inheritsLoose(subClass, superClass) { subClass.prototype = Object.create(superClass.prototype); subClass.prototype.constructor = subClass; _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

define(["jquery", "knockout", "mage/translate", "Magento_PageBuilder/js/events", "underscore", "Magento_PageBuilder/js/config", "Magento_PageBuilder/js/content-type-factory", "Magento_PageBuilder/js/content-type-menu/hide-show-option", "Magento_PageBuilder/js/content-type-menu/option", "Magento_PageBuilder/js/utils/delay-until", "Magento_PageBuilder/js/utils/promise-deferred", "Magento_PageBuilder/js/content-type/preview-collection"], function (_jquery, _knockout, _translate, _events, _underscore, _config, _contentTypeFactory, _hideShowOption, _option, _delayUntil, _promiseDeferred, _previewCollection) {
  var Preview = /*#__PURE__*/function (_previewCollection2) {
    "use strict";

    _inheritsLoose(Preview, _previewCollection2);

    function Preview(contentType, config, observableUpdater) {
      var _this;

      _this = _previewCollection2.call(this, contentType, config, observableUpdater) || this;

      _this.mountAfterDeferred = (0, _promiseDeferred)();
      _this.buildGalleryDebounce = _underscore.debounce(_this.buildGallery.bind(_assertThisInitialized(_this)), 10);
      _this.ignoredKeysForBuild = ["display", "margins_and_padding", "border", "border_color", "border_radius", "border_width", "css_classes", "name", "text_align"];


      _this.focusedItemImg = _knockout.observable();

      _this.focusedItemImg.subscribe(_underscore.debounce(function (index) {
        if (index !== null) {
          _events.trigger("stage:interactionStart");

          var focusedItemImg = _this.contentType.children()[index];

          (0, _delayUntil)(function () {
            return (0, _jquery)(focusedItemImg.preview.wrapperElement).find(".data-bgset-image-wrapper").focus();
          }, function () {
            return typeof focusedItemImg.preview.wrapperElement !== "undefined";
          }, 10);
        } else {
          // We have to force the stop as the event firing is inconsistent for certain operations
          _events.trigger("stage:interactionStop", {
            force: true
          });
        }
      }, 1));

      Promise.all([_this.mountAfterDeferred.promise]).then(function (_ref) {

        (0, _delayUntil)(function () {
          _this.childSubscribeGallery = _this.contentType.children.subscribe(_this.buildGalleryDebounce);
          _this.previousData = _this.contentType.dataStore.getState();

          _this.contentType.dataStore.subscribe(function (data) {
            if (_this.hasDataChanged(_this.previousData, data)) {
              _this.buildGalleryDebounce();
            }

            _this.previousData = data;
          });

          _this.buildGallery();

        }, function () {
          return true;
        });
      });

      return _this;
    }
    /**
     * Bind events
     */


    var _proto = Preview.prototype;

    _proto.bindEvents = function bindEvents() {
      var _this2 = this;

      _previewCollection2.prototype.bindEvents.call(this);

      _events.on("gallery:dropAfter", function (args) {
        if (args.id === _this2.contentType.id && _this2.contentType.children().length === 0) {
          _this2.addItemImg();
        }
      });

      _events.on("gallery:mountAfter", function (args) {
        if (args.id === _this2.contentType.id) {
          if (args.expectChildren !== undefined) {
            _this2.mountAfterDeferred.resolve(args.expectChildren);
          }
        }
      });
    }
    /**
     * Return an array of options
     *
     * @returns {OptionsInterface}
     */
    ;

    _proto.hasDataChanged = function hasDataChanged(previousData, newData) {
      previousData = _underscore.omit(previousData, this.ignoredKeysForBuild);
      newData = _underscore.omit(newData, this.ignoredKeysForBuild);
      return !_underscore.isEqual(previousData, newData);
    }
    /**
     * Build our instance of slick
     */
    ;

    _proto.buildGallery = function buildGallery() {
      var _this5 = this;
      if (this.wrapperElement) {
        var imgItems = (0, _jquery)(this.wrapperElement).find(".elementor-img-item");
        imgItems.each(function() {
          (0, _jquery)(this).attr('class', '');
          (0, _jquery)(this).attr('data-class', '');
          (0, _jquery)(this).attr('class', _this5.getColItem());
          (0, _jquery)(this).attr('data-class', _this5.getColItem());
        });
      }
    }
    /**
     * Take dropped element on focus.
     *
     * @param {JQueryEventObject} event
     * @param {number} index
     */
    ;

    _proto.retrieveOptions = function retrieveOptions() {
      var options = _previewCollection2.prototype.retrieveOptions.call(this);

      options.add = new _option({
        preview: this,
        icon: "<i class='icon-pagebuilder-add'></i>",
        title: (0, _translate)("Add Button"),
        action: this.addItemImg,
        classes: ["add-child"],
        sort: 10
      });
      options.hideShow = new _hideShowOption({
        preview: this,
        icon: _hideShowOption.showIcon,
        title: _hideShowOption.showText,
        action: this.onOptionVisibilityToggle,
        classes: ["hide-show-content-type"],
        sort: 40
      });
      return options;
    }
    /**
     * Add itemimg to buttons children array
     */
    ;

    _proto.addItemImg = function addItemImg() {
      var _this3 = this;

      var createButtonItemPromise = (0, _contentTypeFactory)(_config.getContentTypeConfig("itemimg"), this.contentType, this.contentType.stageId, {});
      createButtonItemPromise.then(function (button) {
        _this3.contentType.addChild(button);

        var buttonIndex = _this3.contentType.children().indexOf(button);

        _this3.focusedItemImg(buttonIndex > -1 ? buttonIndex : null);

        return button;
      }).catch(function (error) {
        console.error(error);
      });
    }
    /**
     * Get the sortable options for the buttons sorting
     *
     * @param {string} orientation
     * @param {string} tolerance
     * @returns {JQueryUI.Sortable}
     */
    ;

    _proto.onSortStart = function onSortStart(event, params) {
      var $elementSortStart = (0, _jquery)(event.target);
      var $getItemSortInfo = $elementSortStart.find('.elementor-img-item:first');
      var $getDataClass = $getItemSortInfo.attr('data-class');
      $elementSortStart.find('.ui-state-highlight-gallery').addClass($getDataClass);
      this.forceContainerHeight();
    }
    /**
     * On sort stop ensure the focused slide and the active slide are in sync, as the focus can be lost in this
     * operation
     */
    ;

    _proto.forceContainerHeight = function forceContainerHeight() {
      (0, _jquery)(this.element).css({
        height: (0, _jquery)(this.element).outerHeight(),
        overflow: "hidden"
      });
    }
    /**
     * Build the slick config object
     *
     * @returns {{autoplay: boolean; autoplaySpeed: (any | number);
     * fade: boolean; infinite: boolean; arrows: boolean; dots: boolean}}
     */
    ;

    _proto.onSortStop = function onSortStop(event, params) {
      var _this2 = this;

      if (params.item.index() !== -1) {
        _underscore.defer(this.focusElement.bind(this, event, params.item.index()));
      }

      _underscore.defer(function () {
        (0, _jquery)(_this2.element).css({
          height: "",
          overflow: ""
        });
      });
    }
    /**
     * Add a slide into the slider
     */
    ;

    _proto.focusElement = function focusElement(event, index) {
      var handleClassName = (0, _jquery)(event.target).data("sortable").options.handle;
      (0, _jquery)((0, _jquery)(event.target).find(handleClassName)[index]).focus();
    }
    /**
     * To ensure smooth animations we need to lock the container height
     */
    ;

    _proto.getColItem = function getColItem() {
      var data = this.contentType.dataStore.getState();
      return 'elementor-img-item col-xxl-'+data.col_xxl+' col-xl-'+data.col_xl+' col-lg-'+data.col_lg+' col-md-'+data.col_md+' col-sm-'+data.col_sm+' col-'+data.col_xs;
    };

    return Preview;
  }(_previewCollection);

  return Preview;
});
//# sourceMappingURL=preview.js.map