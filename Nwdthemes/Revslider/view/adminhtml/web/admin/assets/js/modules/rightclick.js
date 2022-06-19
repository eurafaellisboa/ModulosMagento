/*!
 * REVOLUTION 6.0.0 EDITOR RIGHTCLICK JS
 * @version: 1.0 (01.07.2019)
 * @author ThemePunch
*/

/*
	BEGIN RIGHT CLICK MENU
*/


jQuery(function() {	
	var BufferX = 50,
		BufferY = 50,
		Margin = 10,
		SelectedItem,
		SingleLayers,
		SingleParents,
		SupressClick,
		CopiedLayer,
		CopiedTitle,
		PasteLayer,
		PasteTitle,
		RcMenuAdded,
		RcMenuOpen,
		LayerMenu,
		PrevItem,
		CopiedBG,
		CurEvent,
		SlideId,
		PasteBg,
		PanZoom,
		BgMenu,
		Win;

	RVS.DOC = RVS.DOC===undefined ? jQuery(document) : RVS.DOC;

	

	function addMenus() {

		LayerMenu =

			jQuery('<div class="tool_dd_wrap rc-menu-wrap" id="rc_layer_menu">' +
				'<div class="toolbar_dd_subdrop_wrap">' +
					'<div class="rc-menu-item rc-menu-single"><i class="material-icons">edit</i>Edit</div>' +
					'<div class="toolbar_dd_subdrop">' +
						'<div class="rc-menu-item rc-menu-layer-edit" data-type="content" data-rcevent="editlayer"><i class="material-icons">edit</i>Content</div>' +
						'<div class="rc-menu-item rc-menu-layer-edit" data-type="image" data-rcevent="editlayer"><i class="material-icons">edit</i>Image</div>' +
						'<div class="rc-menu-item" data-type="style" data-rcevent="editlayer"><i class="material-icons">color_lens</i>Base Style</div>' +
						'<div class="rc-menu-item" data-type="advstyle" data-rcevent="editlayer"><i class="material-icons">invert_colors</i>Adv. Style</div>' +
						'<div class="rc-menu-item" data-type="hover" data-rcevent="editlayer"><i class="material-icons">mouse</i>Hover Style</div>' +
						'<div class="rc-menu-item" data-type="sizepos" data-rcevent="editlayer"><i class="material-icons">open_with</i>Size & Position</div>' +
						'<div class="rc-menu-item" data-type="responsive" data-rcevent="editlayer"><i class="material-icons">photo_size_select_large</i>Responsivity</div>' +
						'<div class="rc-menu-item" data-type="animation" data-rcevent="editlayer"><i class="material-icons">play_arrow</i>Animation</div>' +
						'<div class="rc-menu-item" data-type="scroll" data-rcevent="editlayer"><i class="material-icons">system_update_alt</i>On Scroll</div>' +
						'<div class="rc-menu-item" data-type="actions" data-rcevent="editlayer"><i class="material-icons">touch_app</i>Actions</div>' +
					'</div>' +
				'</div>' +
				'<div class="toolbar_dd_subdrop_wrap">' +
					'<div class="rc-menu-item" data-type="layer" data-rcevent="copylayer"><i class="material-icons" data-title="Layer(s)">content_paste</i>Copy</div>' +
					'<div class="toolbar_dd_subdrop">' +
						'<div class="rc-menu-item" data-type="layer" data-rcevent="copylayer"><i class="material-icons" data-title="Layers(s)">layers</i>Selected Layers</div>' +
						'<div class="rc-menu-item rc-menu-single" data-type="idle" data-rcevent="copylayersettings" data-title="Base Style"><i class="material-icons">color_lens</i>Base Style</div>' +
						'<div class="rc-menu-item rc-menu-single" data-type="idleadv" data-rcevent="copylayersettings" data-title="Adv. Style"><i class="material-icons">invert_colors</i>Adv. Style</div>' +
						'<div class="rc-menu-item rc-menu-single" data-type="hover" data-rcevent="copylayersettings" data-title="Hover Style"><i class="material-icons">mouse</i>Hover Style</div>' +
						'<div class="rc-menu-item rc-menu-single" data-type="size" data-rcevent="copylayersettings" data-title="Size"><i class="material-icons">aspect_ratio</i>Size</div>' +
						'<div class="rc-menu-item rc-menu-single" data-type="position" data-rcevent="copylayersettings" data-title="Position"><i class="material-icons">open_with</i>Position</div>' +
						'<div class="rc-menu-item rc-menu-single" data-type="behavior" data-rcevent="copylayersettings" data-title="Responsivity"><i class="material-icons">photo_size_select_large</i>Responsivity</div>' +
						'<div class="rc-menu-item rc-menu-single" data-type="timeline" data-rcevent="copylayersettings" data-title="Animation"><i class="material-icons">play_arrow</i>Animation</div>' +
						'<div class="rc-menu-item rc-menu-single" data-type="effects" data-rcevent="copylayersettings" data-title="On Scroll"><i class="material-icons">system_update_alt</i>On Scroll</div>' +
						'<div class="rc-menu-item rc-menu-single" data-type="actions" data-rcevent="copylayersettings" data-title="Actions"><i class="material-icons">touch_app</i>Actions</div>' +
					'</div>' +
				'</div>' +
				'<div id="rc_paste_layer" class="rc-menu-item" data-rcevent="paste"><i class="material-icons">file_download</i><span class="rc-menu-paste">Paste</span></div>' +
				'<div class="rc-menu-item" data-rcevent="duplicatelayer"><i class="material-icons">content_copy</i>Duplicate</div>' +
				'<div class="rc-menu-item" data-rcevent="deletelayer"><i class="material-icons">delete</i>Delete</div>' +
				'<div class="rc-menu-item" data-rcevent="hidelayer"><i class="material-icons">visibility</i>Show/Hide</div>' +
				'<div class="rc-menu-item" data-rcevent="locklayer"><i class="material-icons">lock_outline</i>Lock/Unlock</div>' +
				'<div class="rc-menu-item" data-rcevent="disable"><i class="material-icons">toggle_off</i>Disable Menu</div>' +
			'</div>').on('mouseleave', closeMenu);

		BgMenu =

			jQuery('<div class="tool_dd_wrap rc-menu-wrap" id="rc_bg_menu">' +
				'<div class="rc-menu-item rc-menu-bg-edit" data-type="content" data-rcevent="editbg"><i class="material-icons">edit</i>Edit</div>' +
				'<div class="toolbar_dd_subdrop_wrap rc-menu-bg-edit" data-type="image">' +
					'<div class="rc-menu-item"><i class="material-icons">edit</i>Edit</div>' +
					'<div class="toolbar_dd_subdrop">' +
						'<div class="rc-menu-item" data-type="image" data-rcevent="editbg" data-title="Background"><i class="material-icons">style</i>Media Library</div>' +
						'<div class="rc-menu-item" data-type="objectlibrary" data-rcevent="editbg" data-title="Animation"><i class="material-icons">camera_enhance</i>Object Library</div>' +
					'</div>' +
				'</div>' +
				'<div class="toolbar_dd_subdrop_wrap">' +
					'<div class="rc-menu-item"><i class="material-icons">content_paste</i>Copy</div>' +
					'<div class="toolbar_dd_subdrop">' +
						'<div class="rc-menu-item" data-type="background" data-rcevent="copybg" data-title="Background"><i class="material-icons">color_lens</i>Background</div>' +
						'<div class="rc-menu-item" data-type="animation" data-rcevent="copybg" data-title="Animation"><i class="material-icons">invert_colors</i>Animation</div>' +
						'<div class="rc-menu-item" data-type="filter" data-rcevent="copybg" data-title="Filter"><i class="material-icons">blur_on</i>Filter</div>' +
						'<div class="rc-menu-item" data-type="onscroll" data-rcevent="copybg" data-title="On Scroll"><i class="material-icons">system_update_alt</i>On Scroll</div>' +
						'<div id="rc_menu_panzoom" class="rc-menu-item" data-type="panzoom" data-rcevent="copybg" data-title="Ken Burns"><i class="material-icons">leak_add</i>Ken Burns</div>' +
					'</div>' +
				'</div>' +
				'<div id="rc_paste_bg" class="rc-menu-item" data-rcevent="paste"><i class="material-icons">file_download</i><span class="rc-menu-paste">Paste</span></div>' +
				'<div class="rc-menu-item" data-rcevent="disable"><i class="material-icons">toggle_off</i>Disable Menu</div>' +
			'</div>').on('mouseleave', closeMenu);

		PasteLayer = LayerMenu.find('#rc_paste_layer');
		PasteBg = BgMenu.find('#rc_paste_bg');

		var bodies = jQuery(document.body).on('click.rcmenu', '.rc-menu-item:not(.rc-menu-title)', menuSelection).on('click.rcmenu', bodyClick);
		jQuery('#main_hor_toolbar, #the_right_toolbar, #timeline_settings').on('mouseover.rcmenu', closeMenu);

		bodies.append(LayerMenu).append(BgMenu);
		SingleLayers = jQuery('.rc-menu-single');
		SingleParents = SingleLayers.parents('.toolbar_dd_subdrop_wrap');

		PasteTitle = jQuery('.rc-menu-paste');
		PanZoom = jQuery('#rc_menu_panzoom');

		Win = RVS.WIN.on('resize.rcmenu', closeMenu);
		RcMenuAdded = true;

	}

	var events = {

		editlayer: function() {

			var triggers;
			switch(this.dataset.type) {

				case 'content':
					triggers = ['#gst_layer_1'];
				break;

				case 'image':
					triggers = ['#gst_layer_1', '#image_layer_media_library_button'];
				break;

				case 'style':
					triggers = ['#gst_layer_3'];
				break;

				case 'advstyle':
					triggers = ['#gst_layer_6'];
				break;

				case 'size':
					triggers = ['#gst_layer_2'];
				break;

				case 'hover':
					triggers = ['#gst_layer_9'];
				break;

				case 'scroll':
					triggers = ['#gst_layer_8'];
				break;

				case 'responsive':
					triggers = ['#gst_layer_13'];
				break;

				case 'animation':
					triggers = ['#gst_layer_4'];
				break;

				case 'actions':
					triggers = ['#gst_layer_5'];
				break;

			}

			SupressClick = true;
			triggers.unshift('#module_layers_trigger');

			var len = triggers.length;
			for(var i = 0; i < len; i++) {

				try {
					jQuery(triggers[i]).trigger('click');
				}
				catch(e) {}

			}

			SupressClick = true;

		},

		editbg: function() {

			var triggers;
			switch(this.dataset.type) {

				case 'content':
					triggers = ['#gst_slide_1'];
				break;

				case 'image':
					triggers = ['#gst_slide_1', '#slide_bg_image_btn'];
				break;

				case 'objectlibrary':
					triggers = ['#gst_slide_1', '#slide_object_library_btn'];
				break;

			}

			SupressClick = true;
			triggers.unshift('#module_slide_trigger');

			var len = triggers.length;
			for(var i = 0; i < len; i++) {

				try {
					jQuery(triggers[i]).trigger('click');
				}
				catch(e) {}

			}

			SupressClick = false;

		},

		duplicatelayer: function() {

			RVS.DOC.trigger('do_duplicate_layer');

		},

		deletelayer: function() {

			RVS.DOC.trigger('do_delete_layer');

		},

		hidelayer: function() {

			RVS.F.showHideLayers();

		},

		locklayer: function() {

			RVS.F.lockUnlockLayers();

		},

		copylayer: function() {

			CopiedBG = false;
			PrevItem = false;

			CopiedLayer = true;
			CopiedTitle = 'Paste Layer(s)';

			if(RVS.selLayers && RVS.selLayers.length === 1) {

				var tpe = RVS.L[SelectedItem.dataset.uid].type;
				if(tpe && tpe.search(/column|row|zone/) === -1) {

					var itm = RVS.H[SelectedItem.dataset.uid],
						hh = itm.w_height,
						ww = itm.w_width;

					if(ww === 'auto') ww = itm.w.outerWidth(true);
					else if(ww.search('%') !== -1) ww = itm.w.closest('.layer_grid').width() * (parseInt(ww, 10) * 0.01);
					else ww = parseInt(ww, 10);

					if(hh === 'auto') hh = itm.w.outerHeight(true);
					else if(hh.search('%') !== -1) 	hh = itm.w.closest('.layer_grid').height() * (parseInt(hh, 10) * 0.01);					
					else hh = parseInt(hh, 10);

					var perc = RVS.S.shrink[RVS.screen];
					ww *= perc;
					hh *= perc;

					PrevItem = {w: ww * 0.5, h: hh * 0.5, keys: Object.keys(RVS.H)};

				}

			}

			SlideId = RVS.S.slideId;
			RVS.DOC.trigger('do_copy_layer');

		},

		copylayersettings: function() {

			CopiedBG = false;

			var tpe = this.dataset.type,
				titl = this.dataset.title,
				layrs = RVS.L[SelectedItem.dataset.uid];

			if(!layrs) {

				console.log('Wrong object path when copying layer style/settings');
				return;

			}

			if(tpe.search('idle') === -1) {
				CopiedLayer = [tpe, RVS.F.safeExtend(true, {}, layrs[tpe]), titl, RVS.selLayers[0]];
			}
			else {
				CopiedLayer = [tpe, buildStyle(layrs.idle, tpe), titl];
			}

			CopiedTitle = 'Paste ' + titl;

		},

		copybg: function() {

			CopiedLayer = false;

			var settings = RVS.SLIDER[RVS.S.slideId].slide,
				titl = this.dataset.title,
				tpe = this.dataset.type,
				obj;

			switch(tpe) {

				case 'background':

					obj = RVS.F.safeExtend(true, {}, settings.bg);
					delete obj.mediaFilter;
					CopiedBG = [tpe, obj, titl];

				break;

				case 'animation':

					CopiedBG = [tpe, getSlideAnimation(settings.timeline), titl];

				break;

				case 'filter':

					CopiedBG = [tpe, settings.bg.mediaFilter, titl];

				break;

				case 'onscroll':

					CopiedBG = [tpe, RVS.F.safeExtend(true, {}, settings.effects), titl];

				break;

				case 'panzoom':

					obj = RVS.F.safeExtend(true, {}, settings.panzoom);
					settings = settings.bg;

					obj.bg = {position: settings.position, positionX: settings.positionX, positionY: settings.positionY};
					CopiedBG = [tpe, obj, titl];


				break;

			}

			CopiedTitle = 'Paste ' + titl;

		},

		paste: function(e) {

			if(CopiedLayer) pasteLayer(e);
			else pasteBG();

		},

		disable: function() {

			BgMenu.remove();
			LayerMenu.remove();

			Win.off('.rcmenu');
			jQuery(document.body).off('.rcmenu');

			jQuery('#main_hor_toolbar, #the_right_toolbar, #timeline_settings').off('.rcmenu');
			PasteLayer = PasteBg = SingleLayers = SingleParents = PasteTitle = PanZoom = SelectedItem = CopiedLayer = CopiedBG = CurEvent = BgMenu = LayerMenu = Win = undefined;

		}

	};

	function pastePosition(e, uid) {

		var position = RVS.L[uid].position,
			alignX = position.horizontal[RVS.screen].v,
			alignY = position.vertical[RVS.screen].v;

		var grid = RVS.H[uid].w.closest('.layer_grid'),
			pos = grid.offset();

		var mouseX = Math.round((e.pageX - pos.left) - PrevItem.w),
			mouseY = Math.round((e.pageY - pos.top) - PrevItem.h),
			calcX = mouseX,
			calcY = mouseY;

		switch(alignX) {

			case 'right':
				calcX = grid.width() - mouseX;
			break;

			case 'center':
				calcX = Math.round((grid.width() * 0.5) - PrevItem.w);
			break;

		}

		switch(alignY) {

			case 'bottom':
				calcY = grid.height() - mouseY;
			break;

			case 'center':
				calcY = Math.round((grid.height() * 0.5) - PrevItem.h);
			break;

		}

		var posHor = RVS.F.safeExtend(true, {}, position.horizontal),
			posVer = RVS.F.safeExtend(true, {}, position.vertical),
			curScren,
			len = 4,
			iLen,
			last,
			size,
			i;

		var sizes = RVS.V.sizes.slice().reverse();
		while(RVS.screen !== sizes[0]) sizes.shift();
		for(i = 0; i < 4; i++) posHor[RVS.V.sizes[i]].e = false;

		len = sizes.length;
		iLen = len - 1;
		calcX += 'px';
		calcY += 'px';

		for(i = 0; i < len; i++) {

			size = sizes[i];
			last = i === iLen;
			curScren = RVS.screen === size || last;

			if(curScren || position.x[size].e) position.x[size] = {v: calcX, e: true, u: 'px'};
			if(curScren || position.y[size].e) position.y[size] = {v: calcY, e: true, u: 'px'};

			pH = RVS.F.safeExtend(true, {}, posHor);
			pV = RVS.F.safeExtend(true, {}, posVer);

			if(curScren) {

				pH[size].e = true;
				pV[size].e = true;

			}

			position.horizontal[size] = RVS.F.safeExtend(true, {}, pH[size]);
			position.vertical[size] = RVS.F.safeExtend(true, {}, pV[size]);

		}

		sizes = RVS.V.sizes.slice();
		while(RVS.screen !== sizes[0]) sizes.shift();

		if(sizes.length) sizes.shift();
		len = sizes.length;

		for(i = 0; i < len; i++) {

			size = sizes[i];

			position.x[size].e = false;
			position.y[size].e = false;

			position.horizontal[size] = RVS.F.safeExtend(true, {}, posHor[size]);
			position.vertical[size] = RVS.F.safeExtend(true, {}, posVer[size]);

		}

		// RVS.F.intelligentUpdateValuesOnLayer(uid);
		// RVS.H[uid].w.trigger('click');

		for (var li in RVS.L) {

			if(!RVS.L.hasOwnProperty(li)) continue;
			if (RVS.L[li].type !== 'zone') RVS.F.drawHTMLLayer({uid: li});

		}

		RVS.S.clickedLayer = uid;

	}

	function pasteLayer(e) {



		if(!CopiedLayer || CopiedLayer === false) {

			console.log('pasting from layer copy failed');
			return;

		}

		var uid;

		// copied the entire layer(s)
		if(CopiedLayer === true) {
			
			RVS.DOC.trigger('do_paste_layer');
			if(!PrevItem || SlideId !== RVS.S.slideId) return;

			var newKeys = Object.keys(RVS.H),
				len = newKeys.length;

			for(var i = 0; i < len; i++) {

				if(PrevItem.keys.indexOf(newKeys[i]) === -1) {

					uid = newKeys[i];
					break;

				}

			}

			if(uid) pastePosition(e, uid);
			return;

		}

		// copied layer settings
		var path = CopiedLayer[0],
			val = CopiedLayer[1],
			ids = CopiedLayer[3];

		// open backup group
		RVS.F.openBackupGroup({id: 'pasteLayerSettings', txt: 'Paste Layer ' + CopiedLayer[2], icon: 'file_download'});

		// pasting settings
		if(path.search('idle') === -1) {

			if(path === 'timeline') {
				RVS.TL[RVS.S.slideId].layers[RVS.selLayers[0]] = RVS.F.safeExtend(true, {}, RVS.TL[RVS.S.slideId].layers[ids]);
			}

			RVS.F.updateLayerObj({path: path, val: val});
			if(path === 'timeline') {

				RVS.F.reDrawListElements();
				jQuery(SelectedItem).trigger('click');

			}

		}
		// pasting styles
		else {

			// idle styles vary depending on whether the basic or advanced was copied
			for(var prop in val) {					
				if(!val.hasOwnProperty(prop)) continue;				
				RVS.F.updateLayerObj({path: 'idle.' + prop, val: val[prop]});				
			}

		}

		RVS.F.updateLayerInputFields();
		RVS.F.closeBackupGroup({id: 'pasteLayerSettings'});

		RVS.S.clickedLayer = null;

	}

	function adjustPanZoom(settings, val) {

		settings.position = val.position;
		settings.positionX = val.positionX;
		settings.positionY = val.positionY;

		RVS.F.updateEasyInputs({container: jQuery('#slide_bg_settings_wrapper'), path:RVS.S.slideId + '.slide.', trigger: 'init'});
		jQuery('#sl_pz_set').change();

	}

	function pasteBG() {

		if(!CopiedBG || CopiedBG === false) {

			console.log('pasting from bg copy failed');
			return;

		}

		var container,
			path = CopiedBG[0],
			val = CopiedBG[1],
			settings = RVS.SLIDER[RVS.S.slideId].slide;

		// open backup group
		RVS.F.openBackupGroup({id: 'pasteBgSettings', txt: 'Paste BG ' + CopiedBG[2], icon: 'file_download'});

		switch(path) {

			case 'background':
				RVS.F.safeExtend(true, settings.bg, val);
				container = '#form_slidebg_source';
			break;

			case 'animation':
				RVS.F.safeExtend(true, settings.timeline, val);
				container = '#form_slide_transition';
			break;

			case 'filter':
				settings.bg.mediaFilter = val;
				container = '#form_slidebg_filters';
			break;

			case 'onscroll':
				RVS.F.safeExtend(true, settings.effects, val);
				container = '#form_slide_onscroll';
			break;

			case 'panzoom':
				RVS.F.safeExtend(true, settings.panzoom, val);
				container = '#form_slidebg_kenburn';
			break;

		}

		// Update Input Fields in Slide Settings
		RVS.F.updateEasyInputs({container: container, path:RVS.S.slideId + '.slide.', trigger: 'init'});

		// needs to be updated manually
		if(path === 'panzoom') adjustPanZoom(settings.bg, val.bg);
		else if(path === 'animation') RVS.F.updateSlideAnimation();

		// close backup group
		RVS.F.closeBackupGroup({id: 'pasteBgSettings'});

		// redraw the stage
		RVS.DOC.trigger('updateslidebasic');

	}

	function getSlideAnimation(settings) {

		var props = ['transition', 'duration', 'easeIn', 'easeOut', 'slots', 'rotation'],
			len = props.length,
			obj = {},
			prop;

		for(var i = 0; i < len; i++) {

			prop = props[i];
			obj[prop] = RVS.F.safeExtend(true, {}, settings[prop]);

		}

		return obj;

	}

	function buildStyle(layrs, tpe) {

		var styles;
		if(tpe === 'idle') {

			// copy all idle field paths here
			styles = [

				'fontSize',
				'lineHeight',
				'fontWeight',
				'letterSpacing',
				'fontStyle',
				'textDecoration',
				'textTransform',
				'selectable',
				'fontFamily',
				'color',
				'backgroundColor',
				'backgroundImage',
				'backgroundImageId',
				'backgroundPosition',
				'backgroundSize',
				'backgroundRepeat',
				'margin',
				'padding',
				'borderColor',
				'borderStyle',
				'borderWidth',
				'borderRadius'

			];

		}
		else {

			// copy all advanced field paths here
			styles = [

				'rotationX',
				'rotationY',
				'rotationZ',
				'opacity',
				'boxShadow',
				'textShadow',
				'filter',
				'spikeUse',
				'spikeLeft',
				'spikeLeftWidth',
				'spikeRight',
				'spikeRightWidth',
				'cornerLeft',
				'cornerRight'

			];

		}

		var len = styles.length,
			obj = {},
			val;

		for(var i = 0; i < len; i++) {

			val = layrs[styles[i]];
			if(!val) continue;

			// deep array copy
			if(Array.isArray(val)) val = JSON.parse(JSON.stringify(val));

			// deep object copy
			else if(typeof val === 'object') val = RVS.F.safeExtend(true, {}, val);

			// primitive value
			obj[styles[i]] = val;

		}

		return obj;

	}

	function menuSelection(e) {

		var evt = this.dataset.rcevent;
		if(evt && events[evt]) events[this.dataset.rcevent].call(this, e);
		return false;

	}

	function closeMenu() {

		if(RcMenuOpen) {

			RcMenuOpen = false;
			LayerMenu.hide();
			BgMenu.hide();

		}

	}

	function removeClasses(i, className) {

		return (className.match (/(^|\s)rc-\align-\S+/g) || []).join(' ');

	}

	// close menu when somewhere else on the stage is clicked
	function bodyClick(e) {

		if(!RcMenuOpen || SupressClick) return;
		var isTarget = e.target.id === 'rc_layer_menu' || jQuery(e.target).closest('#rc_layer_menu').length;
		if(!isTarget) closeMenu();

	}

	function adjustLayerMenu() {

		// edit view
		var tpe = this.className.split('_lc_type_');
		tpe = tpe[1].split(' ')[0];
		tpe = tpe.search(/text|button|video|audio/) !== -1 ? 'content' : tpe === 'image' ? 'image' : false;

		jQuery('.rc-menu-layer-edit').hide();
		if(tpe) jQuery('.rc-menu-layer-edit[data-type="' + tpe + '"').show();

		// paste view
		if(CopiedLayer) {

			PasteLayer.removeClass('disabled');
			PasteTitle.text(CopiedTitle);

		}
		else {

			PasteLayer.addClass('disabled');
			PasteTitle.text('Paste');

		}

	}

	function adjustBgMenu() {

		// edit view
		jQuery('.rc-menu-bg-edit').hide();
		var tpe = RVS.SLIDER[RVS.S.slideId].slide.bg.type === 'image' ? 'image' : 'content';
		if(tpe) jQuery('.rc-menu-bg-edit[data-type="' + tpe + '"]').show();

		// paste view
		if(CopiedLayer === true || CopiedBG) {

			PasteBg.removeClass('disabled');
			PasteTitle.text(CopiedTitle);

		}
		else {

			PasteBg.addClass('disabled');
			PasteTitle.text('Paste');

		}

	}

	function layerSelected(e, $this) {

		rightClick.call($this);

	}

	function rightClick() {

		if(!RcMenuAdded) addMenus();

		var menu,
			method,
			bgType,
			$this = jQuery(this);

		if($this.hasClass('_lc_')) {

			BgMenu.hide();
			adjustLayerMenu.call(this);
			menu = LayerMenu.removeClass(removeClasses);

			method = RVS.selLayers && RVS.selLayers.length === 1 ? 'removeClass' : 'addClass';
			SingleLayers[method]('disabled');
			SingleParents[method]('disabled');

		}
		else {

			LayerMenu.hide();
			adjustBgMenu();
			menu = BgMenu.removeClass(removeClasses);

			bgType = RVS.SLIDER[RVS.S.slideId].slide.bg.type;
			method = bgType === 'image' || bgType === 'external' ? 'show' : 'hide';
			PanZoom[method]();

		}

		var alignX,
			alignY,
			left,
			tops;

		if(CurEvent.pageX < Win.width() - menu.width() - BufferX) {

			left = CurEvent.pageX + Margin;

		}
		else {

			left = CurEvent.pageX - menu.width() - Margin;
			alignX = 'rc-align-right';

		}

		if(CurEvent.pageY < Win.height() - menu.height() - BufferY) {

			tops = CurEvent.pageY + Margin;

		}
		else {

			tops = CurEvent.pageY - menu.height() - Margin;
			alignY = 'rc-align-bottom';

		}

		if(alignX) menu.addClass(alignX);
		if(alignY) menu.addClass(alignY);

		menu.css({'left': left, 'top': tops}).show();
		RcMenuOpen = true;
		CurEvent = false;

	}

	jQuery(document.body).on('contextmenu.rcmenu', '.layer_grid, ._lc_', function(evt) {

		if(CurEvent) return false;
		SelectedItem = this;
		CurEvent = evt;

		var $this = jQuery(this);
		if($this.hasClass('_lc_') && !$this.hasClass('selected')) {

			RVS.DOC.one('layerselectioncomplete.rcmenu', layerSelected);
			$this.trigger('click');
			return false;

		}

		rightClick.call(this);
		return false;

	});

});
/*
	END RIGHT CLICK MENU
*/

