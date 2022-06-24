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
      data.category_id = attributes.category_id;
      data.margin_item = attributes.margin_item;
      data.col_xxl = attributes.col_xxl;
      data.col_xl = attributes.col_xl;
      data.col_lg = attributes.col_lg;
      data.col_md = attributes.col_md;
      data.col_sm = attributes.col_sm;
      data.col_xs = attributes.col_xs;
      data.desgin = attributes.desgin;
      data.countdown_text = attributes.countdown_text;
      data.countdown_data = attributes.countdown_data;
      data.number_products = attributes.number_products;
      data.hide_products = attributes.hide_products;

      return data;
    };

    _proto.toDom = function toDom(data, config) {
      var attributes = {
        type: "Blueskytechco\\DailyDeal\\Block\\Widget\\DailyDeal",
        template: "Blueskytechco_DailyDeal::widget/carousel.phtml",
        title: data.title,
        short_description: this.encodeWysiwygCharacters(data.short_description || ""),
        category_id: data.category_id,
        margin_item: data.margin_item,
        countdown_text: data.countdown_text,
        countdown_data: data.countdown_data,
        number_products: data.number_products,
        hide_products: data.hide_products,
        desgin: data.desgin,
        col_xxl: data.col_xxl,
        col_xl: data.col_xl,
        col_lg: data.col_lg,
        col_md: data.col_md,
        col_sm: data.col_sm,
        col_xs: data.col_xs
      };

      if (data.category_id == "") {
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

    _proto.decodeCategoryIds = function decodeCategoryIds(cat_ids) {
      if ((typeof cat_ids !== "string" && cat_ids !== "object" ) || cat_ids === "") {
        return '';
      }
      var string_cat = String(cat_ids);
      return string_cat.split(",");
    };

    return WidgetDirective;
  }(_widgetDirectiveAbstract);

  return WidgetDirective;
});
//# sourceMappingURL=widget-directive.js.map