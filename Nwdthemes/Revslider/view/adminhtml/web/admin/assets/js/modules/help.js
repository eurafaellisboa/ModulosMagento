/*!
 * REVOLUTION 6.0.0 HELP JS
 * @version: 1.0 (01.07.2019)
 * @author ThemePunch
*/
;jQuery(function() {

	if(typeof jQuery === 'undefined') {

		console.log('jQuery not available');
		return;

	}


	var html,
		icon,
		title,
		index,
		level,
		bricks,
		bodies,
		helper,
		timerIn,
		trigger,
		cssModal,
		addLayer,
		timerOut,
		helpOver,
		helpPath,
		searchBtn,
		showTimer,
		stillOver,
		inputOver,
		titleWrap,
		helpVideo,
		mainTitle,
		videoWrap,
		clickTimer,
		indexPaths,
		helpActive,
		linkButton,
		actionMenu,
		faqResults,
		videoTimer,
		helpButtons,
		closeModals,
		actionModal,
		resizeTimer,
		hoverActive,
		description,
		iconHovered,
		scrollTimer,
		optionButton,
		rightToolbar,
		modalVisible,
		videoPromise,
		videoPlaying,
		searchResults,
		optionResults,
		helpActivated,
		helpInputClear,
		elementHovered,
		helpDescription,
		helpOptionsWrap,
		helpSearchInput,
		wildcard = ':checked';

	var layerClasses =  new RegExp('layerinput|actioninput'),
		sliderClasses = new RegExp('sliderinput|navstyleinput'),
		slideClasses =  new RegExp('slideinput|added_slide_transition'),
		hoverClasses = '*[data-r], *[data-select], *[data-helpkey], .ddTP, .revbuilder-colorpicker, .tponoffwrap, .fake_on_button, .added_slide_transition, .lal_group_member',
		translateClasses = '.frame_list_title, .intelligent_buttons';


	// help icon activate/deactive
	trigger = jQuery('.help_wrap').on('click', function(e) {

		e.stopImmediatePropagation();
		// verify globals
		if(typeof RVS.ENV.plugin_url === 'undefined' || typeof RVS === 'undefined' || typeof tpGS === 'undefined') return;


		if(!helpActivated) loadData();
		else onOffHelp();

	}).on('mouseenter', function() {

		iconHovered = true;
		if(helpActive) {

			modalVisible = true;
			toolBarOut();

		}

	}).on('mouseleave', function() {

		iconHovered = false;

	}).trigger('click');

	// load main definitions
	function loadData() {

		jQuery('head').append('<link rel="stylesheet" type="text/css" href="' + RVS.ENV.plugin_url + 'admin/assets/css/help.css" />');
		RVS.F.ajaxRequest('get_help_directory', {}, function(response) {

			var data;
			if(response.success) {

				try {
					data = JSON.stringify(response.data);
					data = JSON.parse(data);
				}
				catch(e) {
					data = false;
				}

				if(data) {

					bricks = data.translations;
					index = data.helpindex;
					helpActivated = true;
					init();

				}
				else {
					console.log('help directory error');
				}

			}
			else {
				console.log('help directory error');
			}

		});

	}

	// trigger resize after directory is opened and closed just in case
	function onResizeTimer() {

		RVS.WIN.trigger('resize');

	}

	// enable/disable help mode
	function onOffHelp() {

		clearTimeout(videoTimer);

		if(!helpActive) {

			helpActive = true;
			helpInputClear.trigger('click');
			tpGS.gsap.set(helper, {top: 50, left: 'auto', right: '100%', bottom: 'auto', height: 'auto'});

			/*
				MODAL
			*/
			linkButton.hide();
			optionButton.hide();
			helpDescription.hide();
			title.removeClass(removeIconClasses).addClass('help-icon-default').html(title.data('origtext'));
			bodies.addClass('help-mode-activated help-mode-active').on('mouseenter.helpguide', hoverClasses, onInputOver)
																   .on('mouseenter.helpguide', translateClasses, onSpecialOver)
																   .on('mouseleave.helpguide', hoverClasses + ',' + translateClasses, onInputOut)
																   .on('mouseover.helpguide', '.toolbar_selector_icons', toolBarOver).on('mouseout.helpguide', '.toolbar_selector_icons', toolBarOut);


			/*
				DIRECTORY
			*/
			bodies.on('click.helpguide', '.help-button', openItem).on('mouseenter.helpguide', '.help-hover', removeHoverClass);
			showTimer = setTimeout(hideBubble, 3000);
			RVS.WIN.on('resize.helpguide', updateContainer);
			updateContainer();
			showHideVideo(true);

		}
		else {

			clearTimeout(scrollTimer);
			clearTimeout(showTimer);
			clearTimeout(timerOut);
			clearTimeout(timerIn);

			helpActive = false;
			modalVisible = false;
			elementHovered = false;

			RVS.WIN.off('.helpguide');
			bodies.off('.helpguide').removeClass('help-mode-activated help-mode-active');

			jQuery('.help-input-focus').removeClass('help-input-focus');
			videoTimer = setTimeout(showHideVideo, 500);

		}

		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(onResizeTimer, 100);

	}

	function showHideVideo(show) {

		if(!helpVideo || !helpVideo.length) return;

		if(show) {

			mainTitle.text(bricks.instructions);
			videoWrap.show();

			helpVideo[0].currentTime = 0;
			videoPromise = helpVideo[0].play();
			videoPromise.then(function() {videoPlaying = true;}).catch(function(error) {videoPlaying = false;});
			helpVideo.show();

		}
		else {

			mainTitle.text(bricks.selectresult);
			videoWrap.hide();

			if(videoPlaying) helpVideo[0].pause();
			helpVideo.hide();

		}

	}

	// hide help bubble when adjacent toolbar icons are hovered
	function toolBarOver() {

		modalVisible = bodies.hasClass('help-mode-active');
		if(modalVisible) {

			clearTimeout(timerOut);
			bodies.removeClass('help-mode-active');

		}

	}

	// reshow help bubble after adjacent toolbar icons are moused out
	function toolBarOut() {

		if(modalVisible) {

			bodies.addClass('help-mode-active');
			clearTimeout(timerOut);
			hideHelpModal();

		}

	}

	// add/build directory
	function traverseIndex(obj, clas) {

		var keys = Object.keys(obj),
			len = keys.length,
			total = len - 1;

		html += '<ul' + clas + '>';
		for(var i = 0; i < len; i++) {

			var key = keys[i],
				data = obj[key],
				isObject = HelpGuide.verifyObject(data) || key === 'addons';

			if(isObject) {

				var path = isObject && data.helpPath;
				if(!path) {

					switch(key) {

						case 'general_how_to':
							icon = 'help_outline';
						break;

						case 'slider_settings':
							icon = 'settings';
						break;

						case 'navigation_settings':
							icon = 'gamepad';
						break;

						case 'slide_settings':
							icon = 'burst_mode';
						break;

						case 'layer_settings':
							icon = 'layers';
						break;

					}

					var titl = toUpper(key);
					html += '<li class="help-directory-menu" data-path="' + key + '">' +
								'<div class="help-directory-item">' +
									'<i class="material-icons help-arrow-down">folder</i><i class="material-icons help-arrow-up">folder_open</i><span>' + titl + '</span>' +
								'</div>';
					level++;
					traverseIndex(data, '');

				}
				else {

					var ends = '',
						article = '',
						section = '',
						highlight = '',
						description = '',
						arrow = '<i class="material-icons">keyboard_arrow_right</i>',
						linkText = path !== true ? bricks.docs : bricks.tutorial;

					if(data.description) description = '<span class="help-text">' + data.description + '</span>';
					if(data.article) article = '<span class="basic_action_button longbutton help-article" data-article="' + data.article + '"><i class="material-icons">assignment</i>' + linkText + '</span>';
					if(data.section) {

						if(!Array.isArray(data.section)) {

							section = '<span class="help-section">' + data.section.replace(/\-\>/g, arrow) + '</span>';

						}
						else {

							section = '';
							var leg = data.section.length;
							for(var s = 0; s < leg; s++) {

								section += '<span class="help-section">' + data.section[s].replace(/\-\>/g, arrow) + '</span>';

							}
						}

					}

					if(data.highlight && HelpGuide.verifyObject(data.highlight)) {

						var menus = '',
							modal = '',
							focussed = '',
							scrollable = '',
							ids = !data.dependency_id ? '' : ' id="revhelp_' + data.dependency_id + '"';

						highlight = data.highlight;

						if(highlight.menu) menus = ' data-menu="' + highlight.menu + '"';
						if(highlight.modal) modal = ' data-modal="' + highlight.modal + '"';
						if(highlight.focus) focussed = ' data-focus="' + highlight.focus + '"';
						if(highlight.scrollTo) scrollable = ' data-scrollto="' + highlight.scrollTo + '"';
						if(highlight.dependencies && Array.isArray(highlight.dependencies)) scrollable += " data-dependencies='" + JSON.stringify(highlight.dependencies) + "'";

						highlight = '<span' + ids + ' class="basic_action_button longbutton help-option"' + menus + scrollable + focussed + modal + '>' +
										'<i class="material-icons">settings</i>' + bricks.option + '</span>';

					}

					if(i === total) {

						for(var j = 0; j < level; j++) ends += '</li>';
						level = 0;

					}

					html += '<li class="help-directory-menu help-directory-target" data-path="' + key + '">' +
								'<div class="help-directory-item">' +
									'<i class="material-icons">' + icon + '</i>' + data.title +
								'</div>' +
								'<ul>' +
									'<li>' +
										'<div class="help-description">' + section + description + article + highlight + '</div>' +
									'</li>' +
								'</ul>' +
							'</li>' + ends;

				}
			}
		}

		html += '</ul>';

	}

	// addon activated/deactivated
	HelpGuide.toggleHelpAddOn = function(slug, activate) {

		var method = activate ? 'removeClass' : 'addClass';
		HelpGuide.allHelpPaths.find('.help-directory-menu[data-path="' + slug + '"]')[method]('help-hide-addon');

		updateAddOnFolders();
		helpInputClear.trigger('click');

	};

	// hide addons folder if no addons exist
	function filterAddons() {

		var $this = jQuery(this);
		if(!$this.html()) {

			$this.remove();
			return false;

		}

		return $this.children('li').not('.help-hide-addon').length;

	}

	// only show addon directory folder if it contains an AddOn
	function updateAddOnFolders() {

		jQuery('.help-directory-menu[data-path="addons"]').each(function() {

			var $this = jQuery(this),
				uls = $this.children('ul').filter(filterAddons);

			if(uls.length) $this.show();
			else $this.hide();

		});

	}

	// disable help for addons that aren't active
	function checkAddOns() {

		var obj,
			prop,
			addon,
			branch;

		for(var i = 0; i < 3; i++) {

			branch = i === 0 ? 'slider' : i === 1 ? 'slide' : 'layer';
			obj = index.editor_settings[branch + '_settings'].addons;

			for(prop in obj) {

				if(!obj.hasOwnProperty(prop)) continue;
				prop = prop.replace('_addon', '');

				addon = 'revslider-' + prop + '-addon';
				if(!RVS.SLIDER.settings.addOns.hasOwnProperty(addon) || !RVS.SLIDER.settings.addOns[addon].enable) {

					HelpGuide.deactivate(prop + '_addon');

				}

			}

		}

	}

	// add AddOn help data to directory
	HelpGuide.extendHelpAddOns = function(addons, writeMarkup) {

		var len = addons.length;
		for(var i = 0; i < len; i++) {

			var obj = addons[i];
			if(!HelpGuide.verifyObject(obj)) continue;

			var slug = obj.slug,
				settings,
				options,
				tpe;

			for(var j = 0; j < 3; j++) {

				tpe = j === 0 ? 'slider' : j === 1 ? 'slide' : 'layer';
				settings = obj[tpe];
				if(!HelpGuide.verifyObject(settings)) continue;

				options = {};
				options[slug] = settings;

				if(writeMarkup) {

					html = '';
					icon = 'extension';

					traverseIndex(options, '');
					jQuery('.help-directory-menu[data-path="' + tpe + '_settings"]').find('.help-directory-menu[data-path="addons"]').append(html);

				}
				else {

					RVS.F.safeExtend(true, index.editor_settings[tpe + '_settings'].addons, options);

				}

			}

		}

	};

	// create directory
	function buildHelpTree() {

		// extend any AddOns
		if(HelpGuide.addOnsHelp.length) HelpGuide.extendHelpAddOns(HelpGuide.addOnsHelp);

		html = '';
		level = 0;
		traverseIndex(index, ' class="help-directory-top"');
		HelpGuide.allHelpPaths = jQuery(html);
		updateAddOnFolders();

	}

	// store all paths in an Array for search buttons usage
	function writePaths() {

		indexPaths = [];
		HelpGuide.allHelpPaths.find('.help-directory-target').each(function(i) {

			var st = '',
				ar = jQuery(this).parents('.help-directory-menu').not('.help-hide-addon').toArray().reverse(),
				len = ar.length;

			for(var j = 0; j < len; j++) st += ar[j].dataset.path + '.';
			indexPaths[indexPaths.length] = st + this.dataset.path;

		});

	}

	// get definition from main definition object
	function getData(obj, path) {

		if(typeof path === 'string')
			path = path.split('.');

		if(path.length > 1) {

			var prop = path.shift();
			return obj.hasOwnProperty(prop) ? getData(obj[prop], path) : false;

		}

		return obj.hasOwnProperty(path[0]) ? obj[path[0]] : false;

	}

	// pretty directory titles
	function toUpper(st) {

		return st.replace(/\_/g, ' ').replace(/\b\w/g, function(l) {return l.toUpperCase();});

	}

	// handles special circumstances for input hovers
	function sanitizePath($this, path) {

		// parallax levels
		if(path.search('parallax.levels') !== -1) {

			path = path.split('.');
			if(path.length === 3) return path[0] + '.' + path[1];

		}

		// slide params
		if(path.search('info.params') !== -1) {

			path = path.split('.');
			if(path.length === 4) return path[0] + '.' + path[1] + '.' + path[3];

		}

		// slide transition
		if($this.hasClass('added_slide_transition')) return 'added_slide_transition';

		// common nav styles
		if(path.search(/nav\.|bullets\./) === -1) return path;
		if($this.closest('#sr_bullets_styles_fieldset, #sr_tabs_styles_fieldset').length) {

			if(path.search('def') === -1) return 'navigation.styles';
			else return 'navigation.styles.default';

		}
		if($this.closest('#sl_bullets_styles_fieldset, #sl_tabs_styles_fieldset').length) {

			if(path.search('def') === -1) return 'navigation.styles';
			else return 'navigation.styles.default';

		}

		return path;

	}

	// find object path based on a certain value
	function getPath(obj, path, val) {

		if(helpPath) return;
		var paths = obj.helpPath,
			len;

		if(paths) {

			paths = paths.split(',');
			len = paths.length;
			for(var i = 0; i < len; i++) {

				if(RVS.F.trim(paths[i]) === val) {

					helpPath = path;
					break;

				}

			}

			if(helpPath) return;

		}

		var keys = Object.keys(obj);
		len = keys.length;

		for(var j = 0; j < len; j++) {

			if(HelpGuide.verifyObject(obj[keys[j]])) {

				getPath(obj[keys[j]], path + '.' + keys[j], val);

			}

		}

	}

	// get main root from input element
	function realRoot($this, path) {

		return $this.closest('.slider_general_collector').length ? 'slider' : 'nav';

	}

	// gets the revbuilder root path
	function getRoot($this, path) {

		var clas = $this.attr('class');
		if(clas) {

			// first check
			if(clas.search(sliderClasses) !== -1) return realRoot($this, path);
			if(clas.search(slideClasses)  !== -1 || path.search('#slide#') !== -1) return 'slide';
			if(clas.search(layerClasses)  !== -1 || path.search('#layer#') !== -1) return 'layer';

		}

		// fallback check
		if($this.closest('#rbm_layer_action').length) return 'layer';
		if($this.closest('.mode__sliderlayout').length) return 'slider';
		if($this.closest('.mode__navlayout').length) return 'nav';
		if($this.closest('.mode__slidecontent').length) return 'layer';
		if($this.closest('.mode__slidelayout').length) return 'slide';

		return false;

	}

	// for cases when real submenu names don't match the help index object titles
	function translateSub(root, sub) {

		switch(sub) {

			case 'progress':
				if(root === 'navigation_settings') return 'progress_bar';
			break;

			case 'prev_image':
			return 'preview_image';

			case 'holiday_snow':
			return 'snow';

		}

		return sub;

	}

	// get correct path for addon action inputs
	function checkAddonActions(path) {

		if(path.search(/panorama|whiteboard|beforeafter/) !== -1) return 'addons';
		return 'actions';

	}

	// get correct path for addon animation frame inputs
	function checkAddonFrames(path) {

		if(path.search('explode') !== -1) return 'addons';
		return false;

	}

	// get input element help data
	function readData($this, path, root) {

		if(!path) return false;
		if(!root) root = getRoot($this, path);
		if(!root) return false;

		var subroot = path.indexOf('actions.') === -1 ? false : 'actions';
		if(subroot === 'actions') subroot = checkAddonActions(path);
		if(path.indexOf('#frame#.') === 0) subroot = checkAddonFrames(path);

		if(!subroot) {

			subroot = root !== 'slider' ? root : 'general';
			subroot += '_submodule_trigger';
			subroot = jQuery('.' + subroot + '.selected').attr('id');

		}

		if(subroot) {

			if(root === 'nav') root = 'navigation';
			root += '_settings';

			subroot = RVS.F.trim(subroot).toLowerCase().replace('.', '').replace('&', 'and').replace(/\-/g, '_').replace(/\s/g, '_');
			subroot = translateSub(root, subroot);

			var isIndexed = index.editor_settings[root] && index.editor_settings[root][subroot];
			if(!isIndexed) {

				subroot = 'addons';
				isIndexed = index.editor_settings[root] && index.editor_settings[root][subroot];

			}

			if(isIndexed) {

				helpPath = '';
				getPath(index.editor_settings[root][subroot], '', path);
				if(helpPath) return ['editor_settings.' + root + '.' + subroot + helpPath, index.editor_settings[root][subroot], helpPath];

			}

		}

		return false;

	}

	// search button
	function createButton(titl, path, tpe) {

		var st = '<span class="help-button',
			icon = '';

		if(tpe) st += ' help-button-' + tpe;
		st += '" data-path="' + path + '">';

		switch(tpe) {

			case 'slider':
				icon = '<i class=" material-icons">settings</i>';
			break;

			case 'nav':
				icon = '<i class=" material-icons">gamepad</i>';
			break;

			case 'slide':
				icon = '<i class=" material-icons">burst_mode</i>';
			break;

			case 'layer':
				icon = '<i class=" material-icons">layers</i>';
			break;

			case 'doc':
				icon = '<i class=" material-icons">library_books</i>';
			break;

			default:
				icon = '<i class=" material-icons">help_outline</i>';
			// end default

		}

		st += icon + '<span>' + titl + '</span></span>';
		return st;

	}

	// search button click
	function openItem() {

		var path = this.dataset.path;
		displayHelpModal(getData(index, path), path);

	}

	// build search buttons
	function buildButtons(val, popup) {

		val = RVS.F.trim(val);

		var tutorials = '',
			slider = '',
			slide = '',
			layr = '',
			nav = '';

		var len = indexPaths.length,
			items = [],
			keywords,
			paths,
			data,
			titl,
			leg,
			s;

		for(var i = 0, j; i < len; i++) {

			data = getData(index, indexPaths[i]);
			keywords = data.keywords;

			if(!keywords) continue;
			leg = keywords.length;

			for(j = 0; j < leg; j++) {

				// account for invalid expressions typed into the search box
				try {
					s = keywords[j].search(val);
				}
				catch(e) {
					continue;
				}

				if(s === -1 || items.indexOf(indexPaths[i]) !== -1) continue;
				items[items.length] = indexPaths[i];

				titl = data.buttonTitle || data.title;
				paths = indexPaths[i].split('.');

				if(paths[0] === 'general_how_to') {

					tutorials += createButton(titl, indexPaths[i], data.helpPath);

				}
				else {

					switch(indexPaths[i].split('.')[1]) {

						case 'slider_settings':
							slider += createButton(titl, indexPaths[i], 'slider');
						break;

						case 'slide_settings':
							slide += createButton(titl, indexPaths[i], 'slide');
						break;

						case 'layer_settings':
							layr += createButton(titl, indexPaths[i], 'layer');
						break;

						case 'navigation_settings':
							nav += createButton(titl, indexPaths[i], 'nav');
						break;

					}

				}

			}

		}

		return [tutorials, slider, nav, slide, layr];

	}

	// update perfect scrollbar container height
	function updateContainer(e, skipButtons) {

		var max = Math.max(optionResults.height(), faqResults.height()),
			h = Math.min(RVS.WIN.height() / 3, max);

		helpOptionsWrap.height(h);
		helpOptionsWrap[0].scrollTop = 0;
		helpOptionsWrap[1].scrollTop = 0;
		helpOptionsWrap.RSScroll('update');

	}

	// remove all help icon classes
	function removeIconClasses(i, className) {

		return (className.match (/(^|\s)help-\icon-\S+/g) || []).join(' ');

	}

	// show help modal
	function displayHelpModal(data, path) {

		var highlight = data.highlight,
			helpTitle = data.buttonTitle || data.title;

		path = path.split('.');
		path = path[0] !== 'general_how_to' ? path[1].replace('_settings', '') : 'faq';

		mainTitle.text(helpTitle);
		title.html(path + ' ' + bricks.options);
		titleWrap.removeClass(removeIconClasses).addClass('help-icon-' + path);

		description.html(data.description);
		helpDescription.show();
		linkButton.attr('data-link', data.article).css('display', 'inline-block');
		optionButton.removeAttr('data-menu data-modal data-scrollto data-focus data-dependencies').removeClass(removeIconClasses).addClass('help-icon-' + path).css('display', 'inline-block');

		if(highlight) {

			if(highlight.menu) optionButton.attr('data-menu', highlight.menu);
			if(highlight.modal) optionButton.attr('data-modal', highlight.modal);
			if(highlight.scrollTo) optionButton.attr('data-scrollto', highlight.scrollTo);
			if(highlight.focus) optionButton.attr('data-focus', highlight.focus);
			if(highlight.dependencies && Array.isArray(highlight.dependencies)) optionButton.attr('data-dependencies', JSON.stringify(highlight.dependencies));

		}
		else {

			optionButton.hide();

		}

		bodies.addClass('help-mode-active');

	}

	// hide help modal
	function hideHelpModal() {

		timerOut = setTimeout(function() {

			if(!helpOver && !stillOver && !iconHovered) bodies.removeClass('help-mode-active');

		}, 3000);

	}

	// handle non-input focusables
	function onSpecialOver() {

		var data;

		// keyframe buttons
		if(this.className && this.className.search('frame_list_title') !== -1) {

			var fram = jQuery(this).closest('.keyframe_liste').attr('data-frame');
			if(!fram) return;

			fram = fram.replace('frame_', '');
			switch(fram) {

				case '0':
					data = 'animation.in.from';
				break;

				case '1':
					data = 'animation.in.to';
				break;

				case '999':
					data = 'animation.out.to';
				break;

				default:
					data = 'animation.keyframe.to';
				// end default

			}

		}
		// intelligent inherit buttons
		else {

			data = this.dataset.evt;

		}

		this.dataset.helpkey = data;
		onInputOver.call(this);

	}

	// settings panel input field mouseover
	function onInputOver() {

		var orig = jQuery(this);
		if(orig.hasClass('opensettingstrigger') || orig.hasClass('formcontainer')) return;

		clearTimeout(showTimer);
		clearTimeout(timerIn);

		jQuery('.help-input-focus').removeClass('help-input-focus');

		var $this,
			path = this.dataset.helpkey || this.dataset.r;

		if(!path) {

			$this = orig;
			if(!$this.attr('data-select')) {

				$this = $this.hasClass('ddTP') ? $this.prev('select') :
						$this.hasClass('revbuilder-colorpicker') ? $this.find('.revbuilder-cpicker-component') : $this.find('input[data-r]');

			}
			else {

				$this = jQuery($this.attr('data-select'));

			}

			path = $this.attr('data-helpkey') || $this.attr('data-r') || '';

		}

		if(!$this || !$this.length) $this = orig;
		path = sanitizePath($this, path);

		if(!path) return;
		var curPath = path;

		// radio paths can describe the group collectively or individually so we check for both
		if(this.type === 'radio') curPath += '.' + this.value;

		var data = readData($this, curPath, orig.attr('data-helproot'));
		if(!data) {

			if(this.type === 'radio') data = readData($this, path, orig.attr('data-helproot'));
			if(!data) return;

		}

		inputOver = true;
		elementHovered = true;

		// wait 500ms on hover to avoid collisions
		timerIn = setTimeout(function() {

			if(inputOver) {

				clearTimeout(timerOut);
				helpInputClear.trigger('click');
				stillOver = true;

				showHideVideo();
				/* showDirectoryItem(data[0]); */
				displayHelpModal(getData(data[1], data[2].substr(1)), data[0]);

				// handle blur box shadow on the multiple inputs
				if(!orig.hasClass('revbuilder-colorpicker') && !orig.hasClass('tponoffwrap')) {

					if(orig.attr('class') && orig.attr('class').search(/bg_alignselector|layer_hor_selector|layer_ver_selector|layer_content_hor_selector|layer_content_ver_selector/) === -1) {
						$this.addClass('help-input-focus');
					}
					else {
						orig.addClass('help-input-focus');
					}

				}
				else {

					var onOff = orig.closest('.tponoffwrap');
					if(!onOff.length) orig.addClass('help-input-focus');
					else onOff.addClass('help-input-focus');

				}

			}

		}, 500);

	}

	// settings field input element mouseout
	function onInputOut() {

		inputOver = false;
		stillOver = false;
		hideHelpModal();

	}

	// help modal mouseover
	function onHelpModalOver() {

		clearTimeout(timerOut);
		helpOver = true;

	}

	 // help modal mouseout
	function onHelpModalOut() {

		helpOver = false;
		if(elementHovered && !iconHovered) hideHelpModal();

	}

	// hide help modal
	function hideBubble() {

		if(!helpOver && !iconHovered) {

			elementHovered = true;
			onHelpModalOut();

		}

	}

	// remove hover classes for dropdown hints
	function removeClasses() {

		jQuery(this).removeClass(function(i, className) {

			return (className.match (/(^|\s)help-\hover-\S+/g) || []).join(' ');

		});

	}

	// remove hover classes for dropdown hints
	function removeHoverClass() {

		if(hoverActive) {

			hoverActive = false;
			bodies.off('.helpguidehover');
			jQuery('.help-hover').removeClass('help-hover').each(removeClasses);

		}

	}

	// remove dropdown hint classes as soon as something is clicked
	function addBodyClick() {

		bodies.off('.helpguidehover').one('click.helpguidehover', removeHoverClass);

	}

	// show add layer, add slide menu, etc.
	function showMenu(nme, tpe) {

		clearTimeout(clickTimer);
		removeHoverClass();

		var slideMenu;
		if(nme === 'layers') {

			if(!tpe) tpe = 'text';
			addLayer.addClass('help-hover');
			addLayer.addClass('help-hover-' + tpe);

		}
		else {

			slideMenu = addLayer.prev().addClass('help-hover');
			if(nme === 'slideorder') slideMenu.addClass('help-hover-slideorder');
			else if(nme === 'staticlayers') slideMenu.addClass('help-hover-staticlayers');

		}

		hoverActive = true;
		clickTimer = setTimeout(addBodyClick, 100);

	}

	// highlight the targeted option
	function addFocus() {

		var $this = jQuery(this);

		if($this.hasClass('tponoff')) $this = $this.closest('.tponoffwrap');
		else if($this.hasClass('revbuilder-cpicker-component')) $this = $this.closest('.revbuilder-colorpicker');

		$this.addClass('help-input-focus');

	}

	// store the selected layer for further dependancy checks
	function getLayerType($this) {

		var clas = $this[0].className.split(' '),
			len = clas.length;

		while(len--) {

			if(clas[len].search('_lc_type_') !== -1) {

				return clas[len].replace('_lc_type_', '');

			}

		}

		return false;

	}

	// show dropdown menu hints or select a currently existing layer
	function activateHints(dependency) {

		var i,
			cl,
			len,
			clas,
			layr,
			layrs,
			addon,
			classes,
			theType,
			special,
			layerType;

		dependency = dependency.split('::');

		// do special stuff
		switch(dependency[0]) {

			case 'layerselected':

				if(dependency.length === 2) layerType = dependency[1];
				if(layerType) {

					classes = layerType.split('||');
					layerType = classes[0];
					special = '';

					// check for addon layer
					if(layerType.search('{{') !== -1) {

						layerType = layerType.split('{{');
						addon = layerType[1].split('}}')[0];
						special = ' .tp-' + addon;
						layerType = addon;

					}

					len = classes.length;
					clas = '';

					for(i = 0; i < len; i++) {

						if(i > 0) clas += ', ';
						cl = classes[i];

						if(special) cl = cl.split('{{')[0];
						clas += '._lc_type_' + cl + special;

					}

				}
				else {
					clas = '._lc_';
				}

				try {
					layrs = jQuery(clas);
				}
				catch(e) {
					layrs = false;
				}

				if(layrs && layrs.length) {

					if(layrs.hasClass('_lc_content_')) layrs = layrs.closest('._lc_');
					layr = layrs.filter('.selected');

					if(!layr.length) layr = layrs.eq(0).trigger('click');
					theType = getLayerType(layr);

				}
				else {

					showMenu('layers', layerType);

				}

			break;

			case 'addlayer':
				showMenu('layers', 'text');
			break;

			case 'addslide':
				showMenu('slides');
			break;

			case 'slideorder':
				showMenu('slideorder');
			break;

			case 'staticlayers':
				showMenu('staticlayers');
			break;

			default:

				try {
					jQuery(dependency[0]).trigger('click');
				}
				catch(e){}

			// end default

		}

		return theType;

	}

	// on/off input button values
	function checkBoolean(value) {

		if(value === 'true' || value === 'on') return true;
		else if(value === 'false' || value === 'off') return false;
		return value;

	}

	// translate placeholder paths for accessing revbuilder
	function sanitizeKey(key) {

		if(key === '#layer#') {

			if(typeof RVS.selLayers !== 'undefined' && Array.isArray(RVS.selLayers) && RVS.selLayers.length) {

				// need to research more if this is reliable
				return typeof RVS.S.clickedLayer !== 'undefined' ? lastClickedLayer : RVS.selLayers[0].toString();

			}

		}

		return key === '#slide#' && typeof RVS.S.slideId !== 'undefined' ? RVS.S.slideId :
			   key === '#frame#' && typeof RVS.S.keyFrame !== 'undefined' ? RVS.S.keyFrame :
			   key === '#action#' && typeof RVS.S.actionIdx !== 'undefined' ? RVS.S.actionIdx : key;

	}

	// figure out if option can be shown, and if not, highlight the dependency instead
	function checkDependencies(dependencies) {

		dependencies = JSON.parse(dependencies);
		if(!dependencies || !Array.isArray(dependencies)) return false;

		var len = dependencies.length,
			prevVal,
			passed,
			check,
			value,
			path,
			leg,
			key,
			j;

		for(var i = 0; i < len; i++) {

			check = dependencies[i];
			if(!HelpGuide.verifyObject(check)) {

				// do special stuff like show add layer menu, etc.
				prevVal = activateHints(dependencies[i]);

			}
			// check dependent options
			else {

				// a dependency can have its own dependency
				// for cases when one of the dependencies is a wildcard
				if(check.dependency && check.dependency !== prevVal) continue;

				path = check.path.split('.');
				value = RVS.SLIDER;
				leg = path.length;
				passed = false;


				// get the revbuilder value
				for(j = 0; j < leg; j++) {

					key = sanitizeKey(path[j]);


					if(!value.hasOwnProperty(key)) return true;
					value = value[key];

				}

				value = checkBoolean(value);
				prevVal = value;

				if(typeof check.value === 'string' && check.value.search('::') !== -1) {

					var vals = check.value.split('::');
					leg = vals.length;

					for(j = 0; j < leg; j++) {

						if(vals[j] === value) {

							passed = true;
							break;

						}

					}

				}
				else {

					if(check.value === value) passed = true;


				}

				if(!passed) {

					// dependency failed, highlight the dependent option
					if(check.target) wildcard = "[value='" + check.target + "']";

					// jQuery('#revhelp_' + check.option).trigger('click');
					onOptionClick.call(HelpGuide.allHelpPaths.find('#revhelp_' + check.option));

					return true;

				}

			}

		}

		return false;

	}

	// trigger click on an options submenu item
	function menuClick($this) {

		if(!$this.hasClass('selected')) {

			// actions window, only open if it's not already open
			if($this.attr('id') === 'gst_layer_5') {

				if(activateHints('layerselected') && !actionModal.is(':visible')) $this.trigger('click');

			}
			else if($this.attr('id') === 'gst_sl_11') {

				jQuery('.emc_toggle_wrap').removeClass('open');
				if(!cssModal.is(':visible')) $this.trigger('click');

			}
			else {

				$this.trigger('click');

			}

		}

	}

	// trigger clicks on option menus
	function openMenus(menus) {

		menus = menus.split(',');
		var len = menus.length;

		for(var i = 0; i < len; i++) {

			menuClick(jQuery(RVS.F.trim(menus[i])));

		}

	}

	// add a box-shadow to the targeted option(s) to be shown
	function focusElement(focuss) {

		jQuery('.help-input-focus').removeClass('help-input-focus');
		jQuery('.lal_group_member.selected').removeClass('selected');

		focuss = focuss.replace('*wildcard*', wildcard);
		wildcard = ':checked';

		var first = focuss.search('{first}') !== -1,
			isKeyFrame,
			isFrame;

		if(focuss.search('{frame}') !== -1) {

			isFrame = true;
			focuss = focuss.replace('{frame}', '');

		}
		else if(focuss.search('{keyframe}') !== -1) {

			isKeyFrame = true;
			focuss = focuss.replace('{keyframe}', '');

		}

		if(first) focuss = focuss.replace('{first}', '');
		var $this;

		try {
			$this = jQuery(focuss);
		}
		catch(e) {
			return;
		}

		if(isKeyFrame && !$this.length) $this = jQuery('.add_frame_after').first();
		if(isFrame || isKeyFrame) $this.closest('.keyframe_liste').css('z-index', '29');

		if(!first) $this.each(addFocus);
		else $this.eq(0).addClass('help-input-focus');

	}

	// attempt to scroll to the option when it's highlighted
	function scrollElement(scrollTo) {

		var method,
			section;

		if(scrollTo.search('{actions}') === -1) {

			method = 'offset';
			section = rightToolbar;

		}
		else {

			method = 'position';
			section = actionMenu;
			scrollTo = scrollTo.replace('{actions}', '');

		}

		scrollTo = jQuery(scrollTo).filter(':visible');
		if(scrollTo.length) {

			section.scrollTop(0);
			section.scrollTop(scrollTo[method]().top);

		}

	}

	// close any open modals for regular settings clicks
	function modalClose() {

		try {
			this.trigger('click');
		}
		catch(e) {}

	}

	// highlight real option based on directory button click
	function onOptionClick(e) {

		if(e) e.stopImmediatePropagation();
		jQuery('.help-input-focus').removeClass('help-input-focus');

		var $this = jQuery(this),
			modal = $this.attr('data-modal'),
			actions = modal && modal === 'actions',
			dependencies = $this.attr('data-dependencies');

		/*
			for non-actions, dependencies need to be checked first
			for actions, dependencies need to be checked last
		*/

		// check dependencies
		if(!actions && dependencies && checkDependencies(dependencies)) return;

		var dataMenu = $this.attr('data-menu'),
			dataFocus = $this.attr('data-focus'),
			dataScroll = $this.attr('data-scrollto');

		// close any open modals
		if(!modal) jQuery.each(closeModals, modalClose);

		// open menu items
		if(dataMenu) openMenus(dataMenu);

		// highlight option
		if(dataFocus) focusElement(dataFocus);

		// scroll to option
		if(dataScroll) scrollElement(dataScroll);

		// check dependencies
		if(actions && dependencies) checkDependencies(dependencies);

	}

	// joining woocommerce/post common options
	function mapHelpPaths() {

		// TEMP
		var maps = [

			['.fake_on_button', 'slider', 'size.custom.d'],
			['*[data-r="source.woo.types"]', 'slider', 'source.post.types'],
			['*[data-r="source.woo.category"]', 'slider', 'source.post.category'],
			['*[data-r="source.woo.sortBy"]', 'slider', 'source.post.sortBy'],
			['*[data-r="source.woo.sortDirection"]', 'slider', 'source.post.sortDirection'],
			['*[data-r="source.woo.maxProducts"]', 'slider', 'source.post.maxPosts'],
			['*[data-r="source.woo.excerptLimit"]', 'slider', 'source.post.excerptLimit'],
			['#row_column_structure', 'layer', 'row_column_structure'],
			['.colselector label_bigicon', 'layer', 'row_column_structure'],
			['.layer_rowbreak_icons', 'layer', 'group.columnbreakat'],
			['.modal_hor_selector', 'slider', 'modal.horizontal'],
			['.modal_ver_selector', 'slider', 'modal.vertical']

		];
		for(var i = 0; i < maps.length; i++) {

			jQuery(maps[i][0]).attr({'data-helproot': maps[i][1], 'data-helpkey': maps[i][2]});

		}

	}

	// search input event for blue bubble
	function searchInput() {

		var st = '',
			btnHtml,
			hasFaq,
			hasOption;

		if(this.value && this.value.length > 2) {

			btnHtml = buildButtons(this.value.toLowerCase(), true);
			for(var i = 1; i < 5; i++) if(btnHtml[i]) st += btnHtml[i];

			if(st) {

				hasOption = true;
				optionResults.html(st).show();
				bodies.removeClass('help-options-empty');

			}
			else {

				optionResults.hide();
				bodies.addClass('help-options-empty');

			}

			if(btnHtml[0]) {

				hasFaq = true;
				st = btnHtml[0];
				faqResults.html(st).show();
				bodies.removeClass('help-faqs-empty');

			}
			else {

				faqResults.hide();
				bodies.addClass('help-faqs-empty');

			}

		}

		if(hasOption || hasFaq) {

			title.html(bricks.helpMode);
			titleWrap.removeClass(removeIconClasses).addClass('help-icon-default');

			showHideVideo();
			linkButton.hide();
			optionButton.hide();
			searchResults.show();

			helpInputClear.css('visibility', 'visible');

			var max = Math.max(optionResults.height(), faqResults.height()),
				h = Math.min(RVS.WIN.height() / 3, max);

			helpOptionsWrap.height(h);
			helpOptionsWrap[0].scrollTop = 0;
			helpOptionsWrap[1].scrollTop = 0;
			helpOptionsWrap.RSScroll('update');

			clearTimeout(scrollTimer);
			scrollTimer = setTimeout(function() {helpOptionsWrap.RSScroll('update');}, 250);

		}
		else {

			searchResults.hide();
			helpInputClear.css('visibility', 'hidden');
			showHideVideo(true);

		}

		helpDescription.hide();

	}


	// directory activated
	function init() {

		win = jQuery(window);
		bodies = jQuery('body');

		/*
			MODAL
		*/
		var modal =

			'<div id="help_mode_modal">' +
				'<div class="help-mode-title">' +
					'<span id="help_mode_title_wrap" class="help-icon-default"><i class="material-icons">touch_app</i><i class="material-icons">settings</i><i class="material-icons">gamepad</i><i class="material-icons">burst_mode</i><i class="material-icons">layers</i><span id="help_mode_title">' + bricks.helpMode + '</span></span>' +
					'<span id="help_mode_main_title">' + bricks.instructions + '</span>' +
					'<div id="help_mode_video_wrap"><video id="help_mode_video" width="520" height="292" muted loop playsinline><source src="' + RVS.ENV.plugin_url + '/admin/assets/videos/hover_tutorial.mp4" type="video/mp4" /></video></div>' +
				'</div>' +
				'<div class="help-mode-description">' +
					'<div class="help-mode-section"><div id="help_mode_description"></div></div>' +
					'<div id="help-mode-buttons" class="help-mode-section">' +
						'<div id="help_mode_documentation" class="help-mode-button"><i class="material-icons">library_books</i> ' + bricks.viewDocs + '</div>' +
						'<div id="help_mode_option" class="help-mode-button"><i class="material-icons">near_me</i><i class="material-icons">settings</i><i class="material-icons">gamepad</i><i class="material-icons">burst_mode</i><i class="material-icons">layers</i> ' + bricks.showOption + '</div>' +
						'<div class="tp-clearfix"></div>' +
					'</div>' +
				'</div>' +
				'<div id="help_mode_search_wrap">' +
					'<div id="help_mode_search" class="help-mode-section"><input id="help_search_input" type="text" placeholder="' + bricks.search + "'><span id='help_input_clear'><i class='material-icons'>close</i></span></div>" +
					'<div id="help_search_results">' +
						'<div class="help-results-container"><div id="help-options-wrap" class="help-results-wrap"><div id="help_options_results" class="help-results"></div></div></div>' +
						'<div class="help-results-container"><div id="help-faqs-wrap" class="help-results-wrap"><div id="help_faq_results" class="help-results"></div></div></div>' +
						'<div class="tp-clearfix"></div>' +
					'</div>' +
				'</div>' +
				'<span id="help_modal_close"><i class="material-icons help-no-drag">close</i></span>' +
			'</div>';

		jQuery(modal).prependTo(jQuery('#the_right_toolbar'));

		title = jQuery('#help_mode_title');
		titleWrap = jQuery('#help_mode_title_wrap');
		description = jQuery('#help_mode_description');
		helpButtons = jQuery('#help-mode-buttons');

		faqResults = jQuery('#help_faq_results');
		searchResults = jQuery('#help_search_results');
		optionResults = jQuery('#help_options_results');

		searchBtn = jQuery('#help_mode_search');
		helpSearchInput = jQuery('#help_search_input').on('focus', writePaths).on('keyup', searchInput);
		helpInputClear = jQuery('#help_input_clear').on('click', function() {helpSearchInput.val('').trigger('keyup');});
		title.data('origtext', title.html());

		helper = jQuery('#help_mode_modal').draggable({cancel: '.help-no-drag, .help-mode-description, #help_mode_search_wrap'})
										   .on('mouseenter', onHelpModalOver)
										   .on('mouseleave', onHelpModalOut);

		jQuery('#help_modal_close').on('click', function() {bodies.removeClass('help-mode-active');});
		linkButton = jQuery('#help_mode_documentation').on('click', function() {

			jQuery('.help-input-focus').removeClass('help-input-focus');
			window.open(this.dataset.link);

		});

		optionButton = jQuery('#help_mode_option').on('click', onOptionClick);

		helpOptionsWrap = jQuery('.help-results-wrap').RSScroll({

			wheelPropagation:true,
			suppressScrollX:true,
			minScrollbarLength:100

		});

		helpVideo = jQuery('#help_mode_video');
		videoWrap = jQuery('#help_mode_video_wrap');
		helpDescription = jQuery('.help-mode-description');

		/*
			DIRECTORY
		*/
		mapHelpPaths();
		buildHelpTree();

		indexPaths = [];
		closeModals = [];

		jQuery('.rbm_close').each(function(i) {
			closeModals[i] = jQuery(this);
		});

		cssModal = jQuery('#rbm_slider_api');
		actionMenu = jQuery('#layeraction_list');
		actionModal = jQuery('#rbm_layer_action');
		addLayer = jQuery('#add_layer_toolbar_wrap');
		rightToolbar = jQuery('#the_right_toolbar_inner');
		mainTitle = jQuery('#help_mode_main_title');

		checkAddOns();
		onOffHelp();

	}


});
