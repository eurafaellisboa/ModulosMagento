/*eslint-disable */
/* jscs:disable */

function _inheritsLoose(subClass, superClass) { subClass.prototype = Object.create(superClass.prototype); subClass.prototype.constructor = subClass; _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

define(["require", "jquery", "knockout", "mage/translate", "Magento_PageBuilder/js/events", "slick", "underscore", "Magento_PageBuilder/js/config", "Magento_PageBuilder/js/content-type-menu/hide-show-option", "Magento_PageBuilder/js/content-type/preview", "Blueskytechco_PageBuilderCustom/js/resource/packery/packery.pkgd", "countdown"], function (require, _jquery, _knockout, _translate, _events, _slick, _underscore, _config, _hideShowOption, _preview, Packery, countdown) {
  var Preview = /*#__PURE__*/function (_preview2) {
    "use strict";

    _inheritsLoose(Preview, _preview2);

    function Preview(contentType, config, observableUpdater) {
      var _this;

      _this = _preview2.call(this, contentType, config, observableUpdater) || this;
      _this.displayPreview = _knockout.observable(false);
      _this.previewElement = _jquery.Deferred();
      _this.widgetUnsanitizedHtml = _knockout.observable();
      _this.slidesToShow = 5;
      _this.lookbookItemSelector = ".product-item";
      _this.centerModeClass = "center-mode";
      _this.messages = {
        EMPTY: (0, _translate)("Empty Products."),
        NO_RESULTS: (0, _translate)("No products were found matching your condition."),
        LOADING: (0, _translate)("Loading..."),
        UNKNOWN_ERROR: (0, _translate)("An unknown error occurred. Please try again.")
      };
      _this.ignoredKeysForBuild = ["margins_and_padding", "border", "border_color", "border_radius", "border_width", "css_classes", "text_align"];
      _this.placeholderText = _knockout.observable(_this.messages.EMPTY);

      _events.on("contentType:redrawAfter", function (args) {
        if (_this.element && _this.element.children) {
          var $element = (0, _jquery)(_this.element.children).find('.product-items');

          if (args.element && $element.closest(args.element).length) {
            $element.slick("setPosition");
          }
        }
      });

      _events.on("stage:" + _this.contentType.stageId + ":viewportChangeAfter", function (args) {
        var viewports = _config.getConfig("viewports");
        if (_this.element && _this.appearance() === "carousel") {
          _this.slidesToShow = parseFloat(_this.getColBySetting(args.viewport));
          _this.destroySlider();
          _this.initSlider();
        }
        else if(_this.element && _this.appearance() === "grid"){
          _this.initGrid();
        }
      });

      return _this;
    }
    
    var _proto = Preview.prototype;

    _proto.retrieveOptions = function retrieveOptions() {
      var options = _preview2.prototype.retrieveOptions.call(this);

      options.hideShow = new _hideShowOption({
        preview: this,
        icon: _hideShowOption.showIcon,
        title: _hideShowOption.showText,
        action: this.onOptionVisibilityToggle,
        classes: ["hide-show-content-type"],
        sort: 40
      });
      return options;
    };

    _proto.getColBySetting = function getColBySetting(current_viewport) {
      var get_lable = current_viewport;
      var data = this.contentType.dataStore.getState();
      var item = 4;
      if(get_lable == 'desktop'){
        item = data.col_xl;
      }
      else if(get_lable == 'tablet'){
        item = data.col_md;
      }
      else if(get_lable == 'mobile'){
        item = data.col_sm;
      }
      return item;
    };

    _proto.onAfterRender = function onAfterRender(element) {
      this.element = element;
      this.previewElement.resolve(element);
      this.initSlider();
      this.initGrid();
    };

    _proto.afterObservablesUpdated = function afterObservablesUpdated() {
      var _this2 = this;

      _preview2.prototype.afterObservablesUpdated.call(this);

      var data = this.contentType.dataStore.getState();

      if (this.hasDataChanged(this.previousData, data)) {
        this.displayPreview(false);
        if (typeof data.product_ids !== "object") {
          this.placeholderText(this.messages.EMPTY);
          return;
        }

        var url = _config.getConfig("preview_url");

        var requestConfig = {
          method: "POST",
          data: {
            role: this.config.name,
            directive: this.data.main.html()
          }
        };
        this.placeholderText(this.messages.LOADING);

        _jquery.ajax(url, requestConfig).done(function (response) {
          if (typeof response.data !== "object" || !Boolean(response.data.content)) {
            _this2.placeholderText(_this2.messages.NO_RESULTS);

            return;
          }

          if (response.data.error) {
            _this2.widgetUnsanitizedHtml(response.data.error);
          } else {
            _this2.widgetUnsanitizedHtml(response.data.content);

            _this2.displayPreview(true);
          }

          _this2.previewElement.done(function () {
            (0, _jquery)(_this2.element).trigger("contentUpdated");
          });
        }).fail(function () {
          _this2.placeholderText(_this2.messages.UNKNOWN_ERROR);
        });
      }

      this.previousData = Object.assign({}, data);
    };

    _proto.initSlider = function initSlider() {
      if (this.element && this.appearance() === "carousel") {
        var _thisCarousel = this;
        (0, _jquery)(this.element.children).find('.product-items').slick(this.buildSlickConfig());
        _thisCarousel.initSectionCountdown((0, _jquery)(this.element.children));
      }
    };

    _proto.initSectionCountdown = function initSectionCountdown($ele) {
      var $countdownItems = $ele.find('[data-countdown]');
      var data_state = this.contentType.dataStore.getState();
      $countdownItems.each(function() {
        var $thisCountdown = (0, _jquery)(this), finalDate = (0, _jquery)(this).data('countdown');
        $thisCountdown.countdown(finalDate, function(event) {
          $thisCountdown.closest('.product-item').find('.final-date-product-countdown-timers').html(event.strftime(''
             + '<span class="countdown-days"><span class="countdown_ti">%-D</span> <span class="countdown_tx">'+data_state.countdown_day+'</span></span> '
             + '<span class="countdown-hours"><span class="countdown_ti">%H</span> <span class="countdown_tx">'+data_state.countdown_hour+'</span></span> '
             + '<span class="countdown-min"><span class="countdown_ti">%M</span> <span class="countdown_tx">'+data_state.countdown_minute+'</span></span> '
             + '<span class="countdown-sec"><span class="countdown_ti">%S</span> <span class="countdown_tx">'+data_state.countdown_second+'</span></span>'));
        });
      });
    };

    _proto.initGrid = function initGrid() {
      if (this.element && this.appearance() === "grid") {
        var _thisGrid = this;
        var $gridItems = (0, _jquery)(this.element.children).find('.product-item');
        var currentViewport = this.viewport();
        $gridItems.attr('class', '');
        $gridItems.attr('class', 'product-item col-'+this.getColBySetting(currentViewport));
        _thisGrid.initSectionCountdown((0, _jquery)(this.element.children));
      }
    };

    _proto.destroySlider = function destroySlider() {
      (0, _jquery)(this.element.children).find('.product-items').slick("unslick");
    };

    _proto.buildSlickConfig = function buildSlickConfig() {
      var attributes = this.data.main.attributes();
      var postCount = (0, _jquery)(this.widgetUnsanitizedHtml()).find(this.lookbookItemSelector).length;

      var viewports = _config.getConfig("viewports");

      var currentViewport = this.viewport();
      var carouselMode = attributes["data-carousel-mode"];
      var config = {
        slidesToShow: this.slidesToShow,
        slidesToScroll: this.slidesToShow,
        dots: attributes["data-show-dots"] === "true",
        arrows: attributes["data-show-arrows"] === "true",
        autoplay: attributes["data-autoplay"] === "true",
        autoplaySpeed: parseFloat(attributes["data-autoplay-speed"])
      };
    
      var slidesToShow = parseFloat(this.getColBySetting(currentViewport));
      config.slidesToShow = parseFloat(slidesToShow);

      if (attributes["data-carousel-mode"] === "continuous" && postCount > config.slidesToShow) {
        config.centerPadding = attributes["data-center-padding"];
        config.centerMode = true;
        (0, _jquery)(this.element).addClass(this.centerModeClass);
      } else {
        config.infinite = attributes["data-infinite-loop"] === "true";
        (0, _jquery)(this.element).removeClass(this.centerModeClass);
      }
      config.rows = parseFloat(attributes["data-slick-rows"]);
      return config;
    };

    _proto.hasDataChanged = function hasDataChanged(previousData, newData) {
      previousData = _underscore.omit(previousData, this.ignoredKeysForBuild);
      newData = _underscore.omit(newData, this.ignoredKeysForBuild);
      return !_underscore.isEqual(previousData, newData);
    };

    return Preview;
  }(_preview);

  return Preview;
});
//# sourceMappingURL=preview.js.map