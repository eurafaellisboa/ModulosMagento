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

      data.category_ids = this.decodeCategoryIds(attributes.category_ids || "");
      data.title = attributes.title;
      data.short_description = this.decodeWysiwygCharacters(attributes.short_description || "");
      data.template_id = attributes.template_id;
      data.image_hover_effects = attributes.image_hover_effects;
      data.space_between_item = attributes.space_between_item;
      return data;
    };

    _proto.toDom = function toDom(data, config) {
      var attributes = {
        type: "Blueskytechco\\WidgetCategory\\Block\\Widget\\CategoryThumbnail",
        template: "Blueskytechco_WidgetCategory::widget/category_thumbnail/grid/default.phtml",
        category_ids: data.category_ids,
        title: data.title,
        short_description: this.encodeWysiwygCharacters(data.short_description || ""),
        template_id: data.template_id,
        image_hover_effects: data.image_hover_effects,
        space_between_item: data.space_between_item,
        type_name: data.appearance
      };

      if ((typeof attributes.category_ids !== "string" && typeof attributes.category_ids !== "object" ) || attributes.category_ids === "") {
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