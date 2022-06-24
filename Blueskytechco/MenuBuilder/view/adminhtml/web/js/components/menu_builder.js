define([
    "jquery",
	'mage/translate',
	"mage/adminhtml/events",
	"mage/adminhtml/wysiwyg/tiny_mce/setup",
	"mage/adminhtml/wysiwyg/widget",
	"Magento_Theme/js/sortable"
], function ($, $t) { 

    $.widget('mage.menuBuilder', {
        options: {
			addMenuBuilderText: $t('Please enter full information.'),
			inputprefix: 'add-image-input',
        },
        _create: function () {  
			this.url_add_menu_item = this.options.ajaxAddMenuItem;
			this.config_wysiwyg = this.options.getJsonConfigWysiwyg;
			var self = this;
			var currentDepth = 0,originalDepth, minDepth, maxDepth,
				prev, next, prevBottom, nextThreshold, helperHeight, transport,
				menuEdge = $('#menu-to-edit').offset().left,config_wysiwyg = this.config_wysiwyg;
			self.extensionsSortable();
			
			$(document).on('click', '#submit-menu-cmspage', function(){ 
				self.addMenuCmsPage();
			});
			
			$(document).on('click', '#submit-menu-category', function(){ 
				self.addMenuCategoty();
			});
			
			$(document).on('click', '#submit-menu-custom-links', function(){ 
				self.addMenuCustomLinks();
				return false;
			});
			
			$(document).on('click', '.item-edit', function(){ 
				if($(this).closest( ".menu-item" ).hasClass('active')){
					$(this).closest( ".menu-item" ).find('.menu-item-settings').hide(300);
					$(this).closest( ".menu-item" ).removeClass('active');
				}else{
					$(this).closest( ".menu-item" ).find('.menu-item-settings').show(300);
					$(this).closest( ".menu-item" ).addClass('active');
					var wysiwyg = $(this).closest( ".menu-item" ).find('.select_type .wysiwyg-input');
					var data = wysiwyg.data('block');
					self.initializationEditor(data);
				}
				return false;
			});
			
			$(document).on('click', '.item-cancel', function(){ 
				$(this).closest( ".menu-item" ).find('.menu-item-settings').hide(300);
				$(this).closest( ".menu-item" ).find('.item-edit').removeClass('active');
				return false;
			});
			
			$(document).on('click', '.accordion-section-title', function(){ 
				if($(this).closest( ".control-section" ).hasClass('active')){
					$(this).closest( ".control-section" ).removeClass('active');
					$(this).closest( ".control-section" ).find('.accordion-section-content').hide(300);
				}else{
					$('.outer-border .accordion-section-content').hide();
					$('.outer-border .control-section').removeClass('active');
					$(this).closest( ".control-section" ).addClass('active');
					$(this).closest( ".control-section" ).find('.accordion-section-content').show(300);
				}
				return false;
			});
			
            $(document).on("keyup",'.edit-menu-item-title', function(){ 
				var value = $(this).val();
				var item = $(this).closest('.menu-item');
				item.find( '.menu-item-title' ).html(value);
			});
			
			$(document).on("change",'.menu-item-submenu_type', function(){ 
				var value = $(this).val();
				var item = $(this).closest('.menu-item');
				if(value == 'multicolumn_dropdown'){
					item.find( '.multicolumn_dropdown_field' ).show();
					item.find( '.block_type_content_field' ).hide(); 
				}else if(value == 'block_content'){
					item.find( '.multicolumn_dropdown_field' ).hide();
					item.find( '.block_type_content_field' ).show();
				}else{
					item.find( '.multicolumn_dropdown_field' ).hide();
					item.find( '.block_type_content_field' ).hide();
				}
			});
			
			$(document).on("change",'.edit-menu-item-menu-type', function(){ 
				var value = $(this).val();
				var item = $(this).closest('.menu-item');
				if(value == 'fullwidth'){
					item.find( '.submenu_static_width' ).hide(); 
				}else{
					item.find( '.submenu_static_width' ).show();
				}
			});
			
			$(document).on('click', '.block-content-html .fieldset-title', function(){ 
				if($(this).closest( ".admin_field" ).hasClass('active')){
					$(this).closest( ".admin_field" ).removeClass('active');
					$(this).closest( ".admin_field" ).find('.descr').hide(300);
				}else{
					$(this).closest( ".admin_field" ).addClass('active');  
					$(this).closest( ".admin_field" ).find('.descr').show(300);
					if(!$(this).closest( ".admin_field" ).find('.wysiwyg-input').hasClass('wysiwyg-editor')){
						var wysiwyg = $(this).closest( ".admin_field" ).find('.wysiwyg-input');
						var data = wysiwyg.data('block');
						self.initializationEditor(data);
					}
				}
				return false;
			});
			
			$(document).on("change",'.submenu_bg_image', function(){ 
				event.preventDefault();
				var url = $(this).attr('data-url');
				var files = $(this).get(0).files[0];
				var formData = new FormData();
				var item = $(this).closest('.image-upload-container');
				formKey = $('input[name="form_key"]').val();
				formData.append('file', files);
				formData.append('form_key', formKey);
				if(formData && url){
					$.ajax({
						url: url, 
						type: 'POST',
						data: formData,
						processData: false,
						contentType: false,
						showLoader: true,
						success:function(data){
							$(".submenu_bg_image").val(null);
							if (data.url != undefined)  {
                                var imgSrc = data.url ;
								item.find('.saved_image_img').attr("src",imgSrc);
								item.find('.submenu_bg_image_save').val(data.file);
								item.find('.saved_image').show();
                            }else{
								item.find('.saved_image').hide();
							}
						} 
					});
				}
			});
			
			
			$(document).on("change",'.menu_item_icon_image', function(){ 
				event.preventDefault();
				var url = $(this).attr('data-url');
				var files = $(this).get(0).files[0];
				var formData = new FormData();
				var item = $(this).closest('.image-icon-upload-container');
				formKey = $('input[name="form_key"]').val();
				formData.append('file', files);
				formData.append('form_key', formKey);
				if(formData && url){
					$.ajax({
						url: url, 
						type: 'POST',
						data: formData,
						processData: false,
						contentType: false,
						showLoader: true,
						success:function(data){
							$(".menu_item_icon_image").val(null);
							if (data.url != undefined)  {
                                var imgSrc = data.url ;
								item.find('.saved_icon_image').attr("src",imgSrc);
								item.find('.icon_image_save').val(data.file);
								item.find('.saved_image_icon').show();
                            }else{
								item.find('.saved_image_icon').hide();
							}
						} 
					});
				}
			});
			
			$(document).on("click",'.deleteImageIcon', function(){ 
				var item = $(this).closest('.image-icon-upload-container');
				item.find('.saved_icon_image').attr("src",null);
				item.find('.icon_image_save').val(null);
				item.find('.saved_image_icon').hide();
				return false;
			});
			
			$(document).on("click",'.deleteImage', function(){ 
				var item = $(this).closest('.image-upload-container');
				item.find('.saved_image_img').attr("src",null);
				item.find('.submenu_bg_image_save').val(null);
				item.find('.saved_image').hide();
			});
			
			$(document).on("click",'.item-delete', function(){ 
				var idItem = $(this).data('id');
				var el = $('#menu-item-' + idItem);
				var children = el.childMenuItems();

				$( document ).trigger( 'menu-removing-item', [ el ] );
				el.addClass('deleting').animate({
					opacity : 0,
					height: 0
				}, 350, function() {
					el.remove();
					children.shiftDepthClass( -1 ).updateParentMenuItemDBId(config_wysiwyg);
				}); 
				return false;
			});
			
			$(document).on("click",'.categorychecklist .menu-item .open-children-toggle', function(){  
				if(!$(this).parent().parent().hasClass("ui-state-active")) {
					$(this).parent().parent().children(".menu-item-sub").show(300);
					$(this).parent().parent().addClass("ui-state-active");
				}
				else {
					$(this).parent().parent().children(".menu-item-sub").hide(300);
					$(this).parent().parent().removeClass("ui-state-active");
                    $(this).parent().parent().find('ul > li.ui-menu-item > .menu-item input[type="checkbox"]').attr('checked', false);  
				}
                return false;
			});

            $(document).on("click",'.ui-menu-root .menu-root', function(){  
				if(!$(this).parent().hasClass("root-active")) {
					$(this).parent().children(".root-list-menu").slideDown(300);
					$(this).parent().addClass("root-active");
				}
				else {
					$(this).parent().children(".root-list-menu").slideUp(300);
					$(this).parent().removeClass("root-active");
                    $(this).parent().find('ul > li.ui-menu-item > .menu-item input[type="checkbox"]').attr('checked', false);  
				}
                return false;
			});
            
			$(document).on("click",'.select-all-checkbox', function(){ 
				var section = $(this).closest(".control-section");
                if (section.find('.accordion-section-content .tabs-panel-active .ui-menu-root').length){
                    section.find('.accordion-section-content .tabs-panel-active .root-active > ul > li.ui-menu-item > .menu-item input[type="checkbox"]').attr('checked', true);  
                    section.find('.accordion-section-content .root-active li.ui-menu-item.ui-state-active > .menu-item-sub > ul > li.ui-menu-item > .menu-item input[type="checkbox"]').attr('checked', true);
                }else{
                    section.find('.accordion-section-content .tabs-panel-active > ul > li.ui-menu-item > .menu-item input[type="checkbox"]').attr('checked', true);  
                    section.find('.accordion-section-content li.ui-menu-item.ui-state-active > .menu-item-sub > ul > li.ui-menu-item > .menu-item input[type="checkbox"]').attr('checked', true);  
                } 
			});
				
			$('#menu-to-edit').sortable({
				handle: '.menu-item-handle',
				placeholder: 'sortable-placeholder',
				items: 'li.menu-item',
				start: function(e, ui) {
					var height, width;
					transport = ui.item.children('.menu-item-transport');
					
					originalDepth = ui.item.menuItemDepth();
					updateCurrentDepth(ui, originalDepth);
					
					parent = ( ui.item.next()[0] == ui.placeholder[0] ) ? ui.item.next() : ui.item;
					children = parent.childMenuItems();
					transport.append( children );
					
					height = transport.outerHeight();
					height += ui.helper.find('.menu-item-handle').outerHeight();
					ui.placeholder.height(height);
					
					width = ui.helper.find('.menu-item-handle').outerWidth();
					ui.placeholder.width(width);
					updateSortable(ui);
				},
				stop: function(e, ui) {
					var children,depthChange = currentDepth - originalDepth;
						
					children = transport.children().insertAfter(ui.item);
					
					if ( 0 !== depthChange ) {
						ui.item.updateDepthClass( currentDepth );
						children.shiftDepthClass( depthChange );
					}
					
					ui.item.updateParentMenuItemDBId(config_wysiwyg);
				},
				change: function(e, ui) {
					updateSortable(ui);
				},
				sort: function(e, ui) {
					var offset = ui.helper.offset(),edge = offset.left,depth = $(this).pxToDepth( edge - menuEdge );
						
					if ( depth > maxDepth || offset.top < ( prevBottom - 0 ) ) {
						depth = maxDepth;
					} else if ( depth < minDepth ) {
						depth = minDepth;
					}
					
					if( depth != currentDepth ){
						updateCurrentDepth(ui, depth);
					}
					
					if( nextThreshold && offset.top + helperHeight > nextThreshold ) {
						next.after( ui.placeholder );
						updateSharedVars( ui );
						$( this ).sortable( 'refreshPositions' );
					}
				}
			});
			
			function updateSortable(ui) {
				var depth;

				prev = ui.placeholder.prev( '.menu-item' );
				next = ui.placeholder.next( '.menu-item' );

				if( prev[0] == ui.item[0] ) prev = prev.prev( '.menu-item' );
				if( next[0] == ui.item[0] ) next = next.next( '.menu-item' );

				prevBottom = (prev.length) ? prev.offset().top + prev.height() : 0;
				nextThreshold = (next.length) ? next.offset().top + next.height() / 3 : 0; 
				minDepth = (next.length) ? next.menuItemDepth() : 0;

				if( prev.length )
					maxDepth = ( (depth = prev.menuItemDepth() + 1) > 4 ) ? 4 : depth;
				else
					maxDepth = 0;
			}
			
			function updateCurrentDepth(ui, depth) {
				
				ui.placeholder.updateDepthClass( depth, currentDepth );
				currentDepth = depth;
			}
        },
		extensionsSortable : function() {
			// jQuery Sortable extensions.
			$.fn.extend({
				menuItemDepth : function() {
					var margin = $(this).eq(0).css('margin-left');
					return $(this).pxToDepth( margin && -1 != margin.indexOf('px') ? margin.slice(0, -2) : 0 );
				},
				updateDepthClass : function(current, prev) {
					return this.each(function(){
						var t = $(this);
						prev = prev || t.menuItemDepth();
						current = ( current > 4 ) ? 4 : current;
						t.updateParentMaxDepth();
						$(this).removeClass('menu-item-depth-'+ prev )
							.addClass('menu-item-depth-'+ current );
					});
				},
				shiftDepthClass : function(change) {
					return this.each(function(){
						var t = $(this),
							depth = t.menuItemDepth(),
							newDepth = ( (depth + change) > 4 ) ? 4 : depth + change;
							t.updateParentMaxDepth();
						t.removeClass( 'menu-item-depth-'+ depth )
							.addClass( 'menu-item-depth-'+ ( newDepth ) );
					});
				},
				childMenuItems : function() {
					var result = $();
					this.each(function(){
						var t = $(this), depth = t.menuItemDepth(), next = t.next( '.menu-item' );
						while( next.length && next.menuItemDepth() > depth ) {
							result = result.add( next );
							next = next.next( '.menu-item' );
						}
					});
					return result;
				},
				updateParentMaxDepth : function() {
					return this.each(function(){
						var item = $(this),
							input = item.find( '.menu-item-data-parent-id' ),
							depth = parseInt( item.menuItemDepth(), 10 ),
							parentDepth = depth - 1,
							parent = item.prevAll( '.menu-item-depth-' + parentDepth ).first();
						if ( 0 === depth ) {
							input.val(0);
						} else {
							input.val( parent.find( '.menu-item-data-db-id' ).val() );
						}
					});
				},
				updateParentMenuItemDBId : function(config_wysiwyg) {
					return this.each(function(){
						var item = $(this),
							input = item.find( '.menu-item-data-parent-id' ),
							depth = parseInt( item.menuItemDepth(), 10 ),
							parentDepth = depth - 1,
							parent = item.prevAll( '.menu-item-depth-' + parentDepth ).first(),
							wysiwyg_top = item.find('.block-top'),data_top = wysiwyg_top.data('block'),
							wysiwyg_bottom = item.find('.block-bottom'),data_bottom = wysiwyg_bottom.data('block'),
							wysiwyg_left = item.find('.block-left'),data_left = wysiwyg_left.data('block'),
							wysiwyg_right = item.find('.block-right'),data_right = wysiwyg_right.data('block')
							wysiwyg_block_content = item.find('.select_type .wysiwyg-input'),data_block_content = wysiwyg_block_content.data('block');
						if($(this).hasClass('active')){
							$(this).initializationEditor(data_block_content,config_wysiwyg);	
							$(this).initializationEditor(data_top,config_wysiwyg);
							$(this).initializationEditor(data_bottom,config_wysiwyg);
							$(this).initializationEditor(data_left,config_wysiwyg);
							$(this).initializationEditor(data_right,config_wysiwyg);
						}
						if ( 0 === depth ) {
							input.val(0);
							item.find( '.add-block-content' ).show();
							item.find(".menu-item-submenu_type option[value='multicolumn_dropdown']").show();
							var val_submenu_type = item.find( '.menu-item-submenu_type' ).val();
							if(val_submenu_type == 'multicolumn_dropdown'){
								item.find( '.multicolumn_dropdown_field' ).show();
								item.find( '.block_dropdown_field' ).hide();
							}else if(val_submenu_type == 'block_content'){
								item.find( '.multicolumn_dropdown_field' ).hide();
								item.find( '.block_dropdown_field' ).show();
							}else{
								item.find( '.multicolumn_dropdown_field' ).hide();
								item.find(".menu-item-submenu_type option[value='multicolumn_dropdown']").show();
								item.find( '.block_dropdown_field' ).hide();
							}
						} else {
							input.val( parent.find( '.menu-item-data-db-id' ).val() );
							item.find(".menu-item-submenu_type option[value='multicolumn_dropdown']").hide();
							item.find( '.multicolumn_dropdown_field' ).hide();
							item.find( '.block_dropdown_field' ).hide();
							item.find( '.add-block-content' ).hide();
							var val_submenu_type = item.find( '.menu-item-submenu_type' ).val();
							if(val_submenu_type == 'multicolumn_dropdown'){
								item.find( '.menu-item-submenu_type' ).val("default_dropdown").change();
							}
						}
						item.find( ".block-content-html" ).find('.descr').hide();
						
					});
				},
				initializationEditor: function (data,config_wysiwyg) {
					editor = new wysiwygSetup(''+data+'', config_wysiwyg );
					editor.setup('exact'); 
					if(!$('#'+data+'').hasClass('wysiwyg-editor')){
						$('#'+data+'').addClass('wysiwyg-editor');
					}
				},
				depthToPx : function(depth) {
					return depth * 30;
				},
				pxToDepth : function(px) {
					return Math.floor(px / 30); 
				}
			});
		},
		
		addMenuCmsPage: function () {
			event.preventDefault();
			var id_cmspage = $("#cmspagechecklist-pop .menu-item-checkbox:checkbox:checked").map(function(){
				return $(this).val();
			}).get();
			if(id_cmspage.length > 0){
				var id_menu = $("input[name='entity_id']").val();
				if(id_menu){
					$.ajax({
						url: this.url_add_menu_item,
						type: 'POST',
						data: {id:id_cmspage,id_menu:id_menu,type:'cmspage'},
						showLoader: true,
						success:function(data){
							if(data.html){
                                $( "#menu-to-edit" ).append(data.html);
                            }
							$("#cmspagechecklist-pop .menu-item-checkbox").prop('checked', false); 
						} 
					});
				}else{
					alert(this.options.addMenuBuilderText);
				}
			}
		},
		
		addMenuCategoty: function () {
			event.preventDefault();
			var self = this;
			var id_category = $("#categorychecklist-pop .menu-item-checkbox:checkbox:checked").map(function(){
				var parent_id =  $(this).data('parent_id');
				parent_id = self.parentCategoty(parent_id);
				return [{ "category" : $(this).val(), "parent_id" : parent_id }];
			}).get();
			
			if(id_category.length > 0){
				var id_menu = $("input[name='entity_id']").val();
				if(id_menu){
					$.ajax({
						url: this.url_add_menu_item,
						type: 'POST',
						data: {id:id_category,id_menu:id_menu,type:'category'},
						showLoader: true,
						success:function(data){
							if(data.html){
                                $( "#menu-to-edit" ).append(data.html);
                            }
							$("#categorychecklist-pop .menu-item-checkbox").prop('checked', false); 
						} 
					});
				}
			}
		},
		
		parentCategoty: function (parent_id) {
			var category = $(".category"+parent_id+"").length;
			if(category > 0){
				var checked_checkbox = $(".category"+parent_id+":checkbox:checked").length;
				if(checked_checkbox > 0){
					parent_id = parent_id;
				}else{
					parent_id =  $(".category"+parent_id+"").data('parent_id');
					parent_id = this.parentCategoty(parent_id);
				}
			}
			return parent_id;
		},
		
		addMenuCustomLinks: function () {
			event.preventDefault();
			var url = $('#custom-menu-item-url').val();
			var name = $('#custom-menu-item-name').val();
			if(url && name){
				var id_menu = $("input[name='entity_id']").val();
				if(id_menu){
					$.ajax({
						url: this.url_add_menu_item,
						type: 'POST',
						data: {url:url,name:name,id_menu:id_menu,type:'customlink'},
						showLoader: true,
						success:function(data){
                            if(data.html){
                                $( "#menu-to-edit" ).append(data.html);
                            }
						} 
					});
				}else{
					alert(this.options.addMenuBuilderText);
				}
			}else{
				alert(this.options.addMenuBuilderText);
			}
		},
		
		initializationEditor: function (data) {
			editor = new wysiwygSetup(''+data+'', this.config_wysiwyg );
			editor.setup('exact'); 
			Event.observe('toggle'+data+'', "click",editor.toggle.bind(editor));
			if(!$('#'+data+'').hasClass('wysiwyg-editor')){
				$('#'+data+'').addClass('wysiwyg-editor');
			}
		},
    });

    return $.mage.menuBuilder;
});