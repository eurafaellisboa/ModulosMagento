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

      data.sort_order = attributes.sort_order;
      data.title = attributes.title;
      data.short_description = this.decodeWysiwygCharacters(attributes.short_description || "");
      data.category_id = attributes.category_id;
      data.posts_tag = attributes.posts_tag;
      data.posts_author = attributes.posts_author;
      data.publish_date_from = attributes.publish_date_from;
      data.publish_date_to = attributes.publish_date_to;
      data.number_posts = attributes.number_posts;
      data.template_id = attributes.template_id;
      data.image_hover_effects = attributes.image_hover_effects;
      data.space_between_item = attributes.space_between_item;
      return data;
    };

    _proto.toDom = function toDom(data, config) {
      var attributes = {
        type: "Blueskytechco\\PageBuilderCustom\\Block\\Widget\\BlogPosts",
        template: "Blueskytechco_PageBuilderCustom::widget/blog_posts/grid/default.phtml",
        sort_order: data.sort_order,
        title: data.title,
        short_description: this.encodeWysiwygCharacters(data.short_description || ""),
        category_id: data.category_id,
        posts_tag: data.posts_tag,
        posts_author: data.posts_author,
        publish_date_from: data.publish_date_from,
        publish_date_to: data.publish_date_to,
        template_id: data.template_id,
        type_name: data.appearance,
        image_hover_effects: data.image_hover_effects,
        space_between_item: data.space_between_item,
        number_posts: data.number_posts
      };

      if (attributes.sort_order === "") {
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