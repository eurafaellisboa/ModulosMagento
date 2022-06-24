/*eslint-disable */
/* jscs:disable */

function _inheritsLoose(subClass, superClass) { subClass.prototype = Object.create(superClass.prototype); subClass.prototype.constructor = subClass; _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

define(["Magento_PageBuilder/js/mass-converter/widget-directive-abstract", "Magento_PageBuilder/js/utils/object"], function (_widgetDirectiveAbstract, _object) {

  var WidgetDirective = /*#__PURE__*/function (_widgetDirectiveAbstr) {
    "use strict";

    _inheritsLoose(WidgetDirective, _widgetDirectiveAbstr);

    function WidgetDirective() {
      return _widgetDirectiveAbstr.apply(this, arguments) || this;
    }

    var _proto = WidgetDirective.prototype;

    _proto.fromDom = function fromDom(data, config) {
      var attributes = _widgetDirectiveAbstr.prototype.fromDom.call(this, data, config);
      data.title = attributes.title;
      data.short_description = this.decodeWysiwygCharacters(attributes.short_description || "");
      data.show_captions = attributes.show_captions;
      data.show_instagram_icon = attributes.show_instagram_icon;
      data.image_format = attributes.image_format;
      data.margin_item = attributes.margin_item;
      data.image_hover_effects = attributes.image_hover_effects;
      data.col_xxl = attributes.col_xxl;
      data.col_xl = attributes.col_xl;
      data.col_lg = attributes.col_lg;
      data.col_md = attributes.col_md;
      data.col_sm = attributes.col_sm;
      data.col_xs = attributes.col_xs;
      return data;
    };

    _proto.toDom = function toDom(data, config) {
      var attributes = {
        type: "Blueskytechco\\Instagram\\Block\\Widget\\Instagram",
        template: "Blueskytechco_Instagram::widget/carousel.phtml",
        title: data.title,
        short_description: this.encodeWysiwygCharacters(data.short_description || ""),
        show_captions: data.show_captions,
        show_instagram_icon: data.show_instagram_icon,
        image_format: data.image_format,
        margin_item: data.margin_item,
        type_name: data.appearance,
        image_hover_effects: data.image_hover_effects,
        col_xxl: data.col_xxl,
        col_xl: data.col_xl,
        col_lg: data.col_lg,
        col_md: data.col_md,
        col_sm: data.col_sm,
        col_xs: data.col_xs
      };

      if (attributes.image_format === "") {
        return data;
      }

      (0, _object.set)(data, config.html_variable, this.buildDirective(attributes));
      return data;
    };
    
    _proto.encodeWysiwygCharacters = function encodeWysiwygCharacters(content) {
      return content.replace(/\{/g, "^[").replace(/\}/g, "^]").replace(/"/g, "`").replace(/\\/g, "|").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    };
   
    _proto.decodeWysiwygCharacters = function decodeWysiwygCharacters(content) {
      return content.replace(/\^\[/g, "{").replace(/\^\]/g, "}").replace(/`/g, "\"").replace(/\|/g, "\\").replace(/&lt;/g, "<").replace(/&gt;/g, ">");
    };

    return WidgetDirective;
  }(_widgetDirectiveAbstract);

  return WidgetDirective;
});
//# sourceMappingURL=widget-directive.js.map