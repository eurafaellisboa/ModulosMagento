/************************************************
 * REVOLUTION EXTENSION LAYER ANIMATION
 * @date: 01.02.2021
 * @requires rs6.main.js
 * @author ThemePunch
 ************************************************/

 (function($) {
	"use strict";

	var
		version = "6.4.0",
		splitTypes = ["chars", "words", "lines"],
		HR = ["Top", "Right", "Bottom", "Left"],
		CO = ["TopLeft", "TopRight", "BottomRight", "BottomLeft"],
		hr = ["top", "right", "bottom", "left"];

	jQuery.fn.revolution = jQuery.fn.revolution || {};
	var _R = jQuery.fn.revolution;

	///////////////////////////////////////////
	// 	EXTENDED FUNCTIONS AVAILABLE GLOBAL  //
	///////////////////////////////////////////
	jQuery.extend(true, _R, {
		checkLayerDimensions: function(o) {
			var reset = false;
			for (var li in _R[o.id].layers[o.skey]) {
				if (!_R[o.id].layers[o.skey].hasOwnProperty(li)) continue;
				//if (reset) continue;
				var layer = _R[o.id].layers[o.skey][li],
					_ = _R[o.id]._L[layer.id];
				if (_.eow !== layer.offsetWidth && _R.gA(layer, "vary-layer-dims") !== "true") reset = true;
				_.lastknownwidth = _.eow;
				_.lastknownheight = _.eoh;

				if (!_._slidelink) _R[o.id].caches.calcResponsiveLayersList.push({ a: _R[o.id]._L[layer.id].c, b: o.id, c: 0, d: _.rsp_bd, e: o.slideIndex });
			}
			return reset;
		},



		requestLayerUpdates: function(id, type, layerid, tPE) {
			// Update Force Hidden Classes
			var i, tch, hid, v;
			if (layerid !== undefined) {
				i = layerid;
				//SHOW / HIDE Layers within Menus etc. where Parrent not yet started
				if (_R[id]._L[i].pVisRequest !== _R[id]._L[i].pVisStatus) {
					if (_R[id]._L[i]._ligid === undefined || _R[id]._L[_R[id]._L[i]._ligid].childrenAtStartNotVisible !== true) {
						_R[id]._L[i].pVisStatus = _R[id]._L[i].pVisRequest;
						v = ((_R[id]._L[i].type === "row" || _R[id]._L[i].type === "column" || _R[id]._L[i].type === "group") && _R[id]._L[i].frames !== undefined && _R[id]._L[i].frames.frame_999 !== undefined && _R[id]._L[i].frames.frame_999.transform !== undefined && "" + _R[id]._L[i].frames.frame_999.transform.opacity !== "0");
						hid = _R[id]._L[i].pVisRequest === 1 ? "remove" : !v ? "add" : hid;
						tch = _R[id]._L[i].pVisRequest === 1 ? "remove" : v ? "add" : tch;
					} else {
						hid = "add";
						tch = "remove";
					}
					if (tch !== undefined) _R[id]._L[i].p[0].classList[tch]("rs-forceuntouchable");
					if (hid !== undefined) _R[id]._L[i].p[0].classList[hid]("rs-forcehidden");
				}

				if (_R[id]._L[i].pPointerStatus !== _R[id]._L[i].pPeventsRequest) {
					_R[id]._L[i].pPointerStatus = _R[id]._L[i].pPeventsRequest;
					tpGS.gsap.set(_R[id]._L[i].p[0], { pointerEvents: _R[id]._L[i].pPointerStatus, visibility: (_R[id]._L[i].pVisStatus === 1 ? 'visible' : _R[id]._L[i].pVisStatus === 0 ? 'hidden' : _R[id]._L[i].pVisStatus) });
				}
				// If transform Perspective has any value and we should not ignore it (means layer should not use Perspective) than during Animation we add Perspective to Parrent element to avoid issues with
				// scaleing andjittering unsmooth animations.
				if (tPE !== undefined && tPE !== "ignore" && tPE !== 0) {
					tPE++; // Use an other Number which is unlike any other perspective, so if somethign change we dont remove it !
					if (type === "enterstage" || type === "leavestage" || type === "framestarted") {
						if (_R.isFirefox(id)) {
							if (_R[id]._L[i].p[0].style.transform.indexOf("perspective") === -1) _R[id]._L[i].p[0].style.transform += (_R[id]._L[i].p[0].style.transform.length === 0 ? " " : "") + 'perspective(' + tPE + "px" + ')';
						} else // For Not Safari
						if (!window.isSafari11 && _R[id]._L[i].maskHasPerspective !== true && _R[id]._L[i].p[0].style.perspective.length === 0 || _R[id]._L[i].p[0].style.perspective == "none") _R[id]._L[i].p[0].style.perspective = tPE + "px";

					} else
					if (type === "frameended") {
						//For FireFox
						if (_R.isFirefox(id)) _R[id]._L[i].p[0].style.transform = _R[id]._L[i].p[0].style.transform.replace('perspective(' + tPE + "px" + ')', '');
						else // For not Safari
						if (!window.isSafari11) _R[id]._L[i].p[0].style.perspective = _R[id]._L[i].p[0].style.perspective.replace((tPE - 1) + "px", '');
					}
				}
			} else
				for (i in _R[id]._L) {

					if (!_R[id]._L.hasOwnProperty(i)) continue;
					if (_R[id]._L[i].pVisRequest !== _R[id]._L[i].pVisStatus) {
						_R[id]._L[i].pVisStatus = _R[id]._L[i].pVisRequest;
						if (_R[id]._L[i].pVisStatus === 0) { _R[id]._L[i].p[0].classList.add("rs-forcehidden"); } else _R[id]._L[i].p[0].classList.remove("rs-forcehidden");
					}
					if (_R[id]._L[i].pPointerStatus !== _R[id]._L[i].pPeventsRequest) {
						_R[id]._L[i].pPointerStatus = _R[id]._L[i].pPeventsRequest;
						tpGS.gsap.set(_R[id]._L[i].p[0], { pointerEvents: _R[id]._L[i].pPointerStatus, visibility: _R[id]._L[i].pVisStatus });
					}
				}
				// Update Height of Rows / Columns
			if (type === "enterstage")
				if (layerid !== undefined && _R[id]._L[layerid].esginside !== undefined && _R[id]._L[layerid].esginside.length > 0 && _R[id]._L[layerid].esginside.esredraw !== undefined) _R[id]._L[layerid].esginside.esredraw();

		},

		updateMiddleZonesAndESG: function(id) {
			var _actli = _R[id].pr_processing_key || _R[id].pr_active_key || 0,
				i;
			if (_R[id].middleZones && _R[id].middleZones.length > 0 && _R[id].middleZones[_actli] !== undefined)
				for (i = 0; i < _R[id].middleZones[_actli].length; i++)
					tpGS.gsap.set(_R[id].middleZones[_actli][i], { y: Math.round(_R[id].module.height / 2 - _R[id].middleZones[_actli][i].offsetHeight / 2) + "px" });
			if (_R[id].smiddleZones && _R[id].smiddleZones.length > 0)
				for (i = 0; i < _R[id].smiddleZones.length; i++)
					tpGS.gsap.set(_R[id].smiddleZones[i], { y: Math.round(_R[id].module.height / 2 - _R[id].smiddleZones[i].offsetHeight / 2) + "px" });
		},

		// CALCULATE SUM OF ALL ROW HEIGHTS
		getRowHeights: function(id) {
			var mh = 0,
				omh = 0,
				smh = 0,
				_actli = _R[id].pr_processing_key || _R[id].pr_active_key || 0,
				_oldli = _R[id].pr_active_key || 0;

			if (_R[id].rowzones && _R[id].rowzones.length > 0) {
				if (_R[id].rowzones[_actli] !== undefined)
					for (var a = 0; a < _R[id].rowzones[_actli].length; a++) {
						_R[id].rowzonesHeights[_actli][a] = _R[id].rowzones[_actli][a][0].offsetHeight;
						mh += _R[id].rowzonesHeights[_actli][a];
					}

				if (_oldli !== _actli)
					for (a = 0; a < _R[id].rowzones[_oldli].length; a++) {
						_R[id].rowzonesHeights[_oldli][a] = _R[id].rowzones[_oldli][a][0].offsetHeight;
						omh += _R[id].rowzonesHeights[_oldli][a];
					}

				//mh = omh/2>mh ? omh : mh;  // To Avoid Minimal Portfolio Website Jumps
			}
			if (_R[id].srowzones && _R[id].srowzones.length > 0)
				for (a = 0; a < _R[id].srowzones.length; a++) smh += _R[id].srowzones[a][0].offsetHeight;

			mh = mh < smh ? smh : mh;

			return { cur: mh, last: omh };

		},

		getGridOffset: function(id, slideIndex, basealign, isstatic) {
			var gw = basealign === "grid" ? _R[id].canv.width : _R[id].sliderType === "carousel" && !isstatic ? _R[id].carousel.slide_width : _R[id].canv.width,
				gh = _R[id].useFullScreenHeight ? _R[id].module.height : basealign === "grid" ? _R[id].content.height : _R[id].sliderType === "carousel" && !isstatic ? _R[id].canv.height : _R[id].module.height, //_R[id].carousel.slide_height :  _R[id].module.height;
				offsety = basealign === "slide" ? 0 : Math.max(0, _R[id].sliderLayout == "fullscreen" ? _R[id].module.height / 2 - (_R.iHE(id) * (_R[id].keepBPHeight ? 1 : _R[id].CM.h)) / 2 : (_R[id].autoHeight || (_R[id].minHeight != undefined && _R[id].minHeight > 0) || _R[id].keepBPHeight) ? _R[id].canv.height / 2 - (_R.iHE(id) * _R[id].CM.h) / 2 : 0),
				offsetx = basealign === "slide" ? 0 : Math.max(0, (_R[id].sliderType === "carousel" ? 0 : _R[id].canv.width / 2 - (_R.iWA(id, slideIndex) * _R[id].CM.w) / 2));
			if (basealign !== "slide" && _R[id].sliderType === "carousel" && isstatic && _R[id].carousel !== undefined && _R[id].carousel.horizontal_align !== undefined)
				offsetx = Math.max(0, (_R[id].carousel.horizontal_align === "center" ? 0 + (_R[id].module.width - (_R.iWA(id, 'static') * _R[id].CM.w)) / 2 : _R[id].carousel.horizontal_align === "right" ? (_R[id].module.width - (_R[id].gridwidth[_R[id].level] * _R[id].CM.w)) : offsetx));

			return [gw, gh, offsetx, offsety];
		},


		initLayer: function(o) {
			var id = o.id,
				skey = o.skey,
				u, s, corner;


			// Collect All Layers
			for (var li in _R[id].layers[o.skey]) {

				if (!_R[id].layers[o.skey].hasOwnProperty(li)) continue;

				var layer = _R[id].layers[o.skey][li],
					L = jQuery(layer),
					linited = _R.gA(layer, "initialised"),
					_ = linited ? _R[id]._L[layer.id] : L.data();

				if (o.skey === "individual") {
					_.slideKey = _.slideKey === undefined ? _R.gA(L.closest('rs-slide')[0], 'key') : _.slideKey;
					_.slideIndex = _.slideIndex === undefined ? _R.getSlideIndex(id, _.slideKey) : _.slideIndex;
					o.slideIndex = _.slideIndex;
					skey = _.slideKey;
				}


				/****************************
					PREPARE DATAS 1 TIME
				*****************************/

				if (linited === undefined) {
					_R.revCheckIDS(id, layer);
					_R[id]._L[layer.id] = _;

					/***********************
						FRAME MANAGEMENT
					***********************/

					//FRAME ORDER
					_.ford = _.ford === undefined ? "frame_0;frame_1;frame_999" : _.ford;
					_.ford = _.ford[_.ford.length - 1] == ';' ? _.ford.substring(0, _.ford.length - 1) : _.ford;
					_.ford = _.ford.split(';');

					// CLIPPATH
					if (_.clip !== undefined) {
						_.clipPath = { use: false, origin: "l", type: "rectangle" };
						_.clip = _.clip.split(";");
						for (u in _.clip) {
							if (!_.clip.hasOwnProperty(u)) continue;
							s = _.clip[u].split(":");
							if (s[0] == 'u') _.clipPath.use = s[1] == "true";
							if (s[0] == 'o') _.clipPath.origin = s[1];
							if (s[0] == 't') _.clipPath.type = s[1];
						}
					}
					// 0.05

					_.frames = buildFrameObj(_, id); //0.2 - 0.6ms




					/************
						BASICS
					*************/
					_.caches = {}; //Cache last Set Values and only update if cached and not cached are different !
					_.OBJUPD = {}; //Some further values which influence LOBJ, LPOBJ, MOBJ, POBJ etc.
					_.c = L;

					_.p = _R[id]._Lshortcuts[layer.id].p;
					_.lp = _R[id]._Lshortcuts[layer.id].lp;
					_.m = _R[id]._Lshortcuts[layer.id].m;

					_.triggercache = _.triggercache === undefined ? "reset" : _.triggercache;
					_.rsp_bd = _.rsp_bd === undefined ? _.type === "column" || _.type === "row" ? "off" : "on" : _.rsp_bd;
					_.rsp_o = _.rsp_o === undefined ? "on" : _.rsp_o;
					_.basealign = _.basealign === undefined ? "grid" : _.basealign;

					_.group = _.type !== "group" && _R.closestNode(L[0], 'RS-GROUP-WRAP') !== null /*L.closest('rs-group-wrap').length>0*/ ? "group" : _.type !== "column" && _R.closestNode(L[0], 'RS-COLUMN') !== null /*L.closest('rs-column').length>0*/ ? "column" : _.type !== "row" && _R.closestNode(L[0], 'RS-ROW') !== null /*L.closest("rs-row").length>0 */ ? "row" : undefined;
					_._lig = _.group === "group" ? jQuery(_R.closestNode(L[0], 'RS-GROUP')) /*L.closest('rs-group')*/ : _.group === "column" ? jQuery(_R.closestNode(L[0], 'RS-COLUMN')) /*L.closest('rs-column')*/ : _.group === "row" ? jQuery(_R.closestNode(L[0], 'RS-ROW')) : undefined;
					_._ligid = _._lig !== undefined ? _._lig[0].id : undefined;
					_._column = L[0].tagName === "RS-COLUMN" ? jQuery(_R.closestNode(L[0], 'RS-COLUMN-WRAP')) /*L.closest("rs-column-wrap")*/ : "none";
					_._row = L[0].tagName === "RS-COLUMN" ? jQuery(_R.closestNode(L[0], 'RS-ROW')) /*L.closest("rs-row") */ : false;
					_._ingroup = _.group === "group";
					_._incolumn = _.group === "column";
					_._inrow = _.group === "row";
					_.fsom = _.fsom=="true" || _.fsom==true;

					// EXTEND SBA IF PARRENT ELLEMENT IS ALREADY SBA
					if ((_._ingroup || _._incolumn) && _._lig[0].className.indexOf('rs-sba') >= 0 && !(_.animationonscroll === false && _.frames.loop !== undefined) && _.animOnScrollForceDisable !== true) { _.animationonscroll = true;
						L[0].className += " rs-sba";
						_R[id].sbas[skey][layer.id] = L[0]; }
					_.animOnScrollRepeats = 0;
					_._isgroup = L[0].tagName === "RS-GROUP";
					//_.dchildren = _._row ?  _R.getByTag(L[0],'RS-COLUMN') : _._column!=="none" || _._isgroup ?  L[0].getElementsByClassName('rs-layer') : "none";
					_.type = _.type || "none";



					if (_.type === "row") {
						if (_.cbreak === undefined) _.cbreak = 2;
						if (_.zone === undefined) {
							_.zone = _R.closestNode(L[0], 'RS-ZONE');
							_.zone = _.zone !== null && _.zone !== undefined ? _.zone.className : "";
						}
					}

					_.esginside = jQuery(L[0].getElementsByClassName('esg-grid')[0]);
					_._isnotext = jQuery.inArray(_.type, ["video", "image", "audio", "shape", "row", "group"]) !== -1;
					_._mediatag = _.audio == "html5" ? "audio" : "video";
					_.img = L.find("img");
					_.deepiframe = _R.getByTag(L[0], 'iframe');
					_.deepmedia = _R.getByTag(L[0], _._mediatag);
					_.layertype = _.type === "image" ? "image" : L[0].className.indexOf("rs-layer-video") >= 0 || L[0].className.indexOf("rs-layer-audio") >= 0 || (_.deepiframe.length > 0 && (_.deepiframe[0].src.toLowerCase().indexOf('youtube') > 0 || _.deepiframe[0].src.toLowerCase().indexOf('vimeo') > 0)) || _.deepmedia.length > 0 ? "video" : "html";
					if (_.deepiframe.length > 0) _R.sA(_.deepiframe[0], "layertype", _.layertype);

					if (_.type === "column") {
						_.cbg = jQuery(_R.getByTag(_.p[0], "RS-COLUMN-BG")[0]);
						_.cbgmask = jQuery(_R.getByTag(_.p[0], "RS-CBG-MASK-WRAP")[0]);
					}
					_._slidelink = L[0].className.indexOf("slidelink") >= 0;
					_._isstatic = L[0].className.indexOf("rs-layer-static") >= 0;
					_.slidekey = _._isstatic ? "staticlayers" : skey;
					//_._li = _._isstatic ? L.closest('rs-static-layers') : L.closest('rs-slide');
					_._togglelisteners = L[0].getElementsByClassName('rs-toggled-content').length > 0;

					// Check for two special Metas ->
					// total_slide_count and current_slide_index
					if (_.type === "text") {
						_.c[0].innerHTML = _.c[0].innerHTML.replace('{{total_slide_count}}', _R[id].realslideamount);

						if (_.c[0].innerHTML.indexOf('{{current_slide_index}}') >= 0) {
							if (_._isstatic) {
								_.metas = _.metas || {};
								_.metas.csi = {};
								_.c[0].innerHTML = _.c[0].innerHTML.replace('{{current_slide_index}}', '<cusli>' + _R[id].realslideamount + '</cusli>');
								_.metas.csi.c = _.c[0].getElementsByTagName('CUSLI')[0];

							} else {
								var digit = parseInt(o.slideIndex) + 1;
								_.c[0].innerHTML = _.c[0].innerHTML.replace('{{current_slide_index}}', ((digit < 10 && _R[id].realslideamount > 9 ? '0' : '') + digit));
							}
						}

					}

					_.bgcol = _.bgcol === undefined ? L[0].style.background.indexOf("gradient") >= 0 ? L[0].style.background : L[0].style.backgroundColor : _.bgcol;
					_.bgcol = _.bgcol === "" ? "rgba(0, 0, 0, 0)" : _.bgcol;
					_.bgcol = _.bgcol.indexOf('rgba(0, 0, 0, 0)') === 0 && _.bgcol.length > 18 ? _.bgcol.replace("rgba(0, 0, 0, 0)", "") : _.bgcol;
					_.zindex = _.zindex === undefined ? L[0].style.zIndex : _.zindex;

					//Set Visibility at Start to hidden (0) to avoid Triggered Group Content availibity.
					if (_._isgroup) {
						if (_.frames.frame_1.timeline.waitoncall) _.childrenAtStartNotVisible = true; // If Group is waiting for Start, the Children should not be visible also !
						_.pVisRequest = 0;
					}

					if (_._togglelisteners) L.on('click', function() { _R.swaptoggleState([this.id]); });
					// GET AND SPLIT THE BORDER SETTINGS
					if (_.border !== undefined) {
						_.border = _.border.split(";");
						_.bordercolor = "transparent";
						for (u in _.border) {
							if (!_.border.hasOwnProperty(u)) continue;
							s = _.border[u].split(":");
							switch (s[0]) {
								case "boc":
									_.bordercolor = s[1];
									break;
								case "bow":
									_.borderwidth = _R.revToResp(s[1], 4, 0);
									break;
								case "bos":
									_.borderstyle = _R.revToResp(s[1], 4, 0);
									break;
								case "bor":
									_.borderradius = _R.revToResp(s[1], 4, 0);
									break;
							}
						}
					}

					// GET SVG SETTINGS
					if (_.type === "svg") {
						_.svg = L.find("svg");
						_.svgI = _svgprep(_.svgi, id); //svgi
						_.svgPath = _.svg.find(!_.svgI.svgAll ? 'path' : 'path, circle, ellipse, line, polygon, polyline, rect');
						_.svgH = (_.svgi !== undefined && _.svgi.indexOf('oc:t') === -1) ? _svgprep(_.svgh, id) : {}; //svgh

					}


					// GET MASK BASIC TRANSFORMS
					if (_.btrans !== undefined) { var btr = _.btrans;
						_.btrans = { rX: 0, rY: 0, rZ: 0, o: 1 };
						btr = btr.split(";"); for (u in btr) { if (!btr.hasOwnProperty(u)) continue;
							s = btr[u].split(":"); switch (s[0]) {
								case "rX":
									_.btrans.rX = s[1]; break;
								case "rY":
									_.btrans.rY = s[1]; break;
								case "rZ":
									_.btrans.rZ = s[1]; break;
								case "o":
									_.btrans.o = s[1]; break; } } }

					// GET BOX SHADOW
					if (_.tsh !== undefined) { _.tshadow = { c: "rgba(0,0,0,0.25)", v: 0, h: 0, b: 0 };
						_.tsh = _.tsh.split(";"); for (u in _.tsh) { if (!_.tsh.hasOwnProperty(u)) continue;
							s = _.tsh[u].split(":"); switch (s[0]) {
								case "c":
									_.tshadow.c = s[1]; break;
								case "h":
									_.tshadow.h = s[1]; break;
								case "v":
									_.tshadow.v = s[1]; break;
								case "b":
									_.tshadow.b = s[1]; break; } } }
					if (_.tst !== undefined) { _.tstroke = { c: "rgba(0,0,0,0.25)", w: 1 };
						_.tst = _.tst.split(";"); for (u in _.tst) { if (!_.tst.hasOwnProperty(u)) continue;
							s = _.tst[u].split(":"); switch (s[0]) {
								case "c":
									_.tstroke.c = s[1]; break;
								case "w":
									_.tstroke.w = s[1]; break; } } }
					if (_.bsh !== undefined) { _.bshadow = { e: "c", c: "rgba(0,0,0,0.25)", v: 0, h: 0, b: 0, s: 0 };
						_.bsh = _.bsh.split(";"); for (u in _.bsh) { if (!_.bsh.hasOwnProperty(u)) continue;
							s = _.bsh[u].split(":"); switch (s[0]) {
								case "c":
									_.bshadow.c = s[1]; break;
								case "h":
									_.bshadow.h = s[1]; break;
								case "v":
									_.bshadow.v = s[1]; break;
								case "b":
									_.bshadow.b = s[1]; break;
								case "s":
									_.bshadow.s = s[1]; break;
								case "e":
									_.bshadow.e = s[1]; break; } } }

					// GET AND SPLIT THE DIMENSION PARAMETERS
					if (_.dim !== undefined) { _.dim = _.dim.split(";"); for (u in _.dim) { if (!_.dim.hasOwnProperty(u)) continue;
							s = _.dim[u].split(":"); switch (s[0]) {
								case "w":
									_.width = s[1]; break;
								case "h":
									_.height = s[1]; break;
								case "maxw":
									_.maxwidth = s[1]; break;
								case "maxh":
									_.maxheight = s[1]; break;
								case "minw":
									_.minwidth = s[1]; break;
								case "minh":
									_.minheight = s[1]; break; } } }


					// GET AND SPLIT POSITION PARAMETERS
					if (_.xy !== undefined && _.type !== "row" && _.type !== "column") { _.xy = _.xy.split(";"); for (u in _.xy) { if (!_.xy.hasOwnProperty(u)) continue;
							s = _.xy[u].split(":"); switch (s[0]) {
								case "x":
									_.x = s[1].replace("px", ""); break;
								case "y":
									_.y = s[1].replace("px", ""); break;
								case "xo":
									_.hoffset = s[1].replace("px", ""); break;
								case "yo":
									_.voffset = s[1].replace("px", ""); break; } } }

					// GET TEXT VALUES
					if ((!_._isnotext) && _.text !== undefined) { _.text = _.text.split(";"); for (u in _.text) { if (!_.text.hasOwnProperty(u)) continue;
							s = _.text[u].split(":"); switch (s[0]) {
								case "w":
									_.whitespace = s[1]; break;
								case "td":
									_.textDecoration = s[1]; break;
								case "c":
									_.clear = s[1]; break;
								case "f":
									_.float = s[1]; break;
								case "s":
									_.fontsize = s[1]; break;
								case "l":
									_.lineheight = s[1]; break;
								case "ls":
									_.letterspacing = s[1]; break;
								case "fw":
									_.fontweight = s[1]; break;
								case "a":
									_.textalign = s[1]; break; } } }
					if (_.type === "column" && _.textDecoration !== undefined) delete _.textDecoration;
					// GET FLOAT VALUES
					if (_.flcr !== undefined) { _.flcr = _.flcr.split(";"); for (u in _.flcr) { if (!_.flcr.hasOwnProperty(u)) continue;
							s = _.flcr[u].split(":"); switch (s[0]) {
								case "c":
									_.clear = s[1]; break;
								case "f":
									_.float = s[1]; break; } } }


					// GET PADDING VALUES
					if (_.padding !== undefined) { _.padding = _.padding.split(";"); for (u in _.padding) { if (!_.padding.hasOwnProperty(u)) continue;
							s = _.padding[u].split(":"); switch (s[0]) {
								case "t":
									_.paddingtop = s[1]; break;
								case "b":
									_.paddingbottom = s[1]; break;
								case "l":
									_.paddingleft = s[1]; break;
								case "r":
									_.paddingright = s[1]; break; } } }

					// GET MARGIN VALUES
					if (_.margin !== undefined) { _.margin = _.margin.split(";"); for (u in _.margin) { if (!_.margin.hasOwnProperty(u)) continue;
							s = _.margin[u].split(":"); switch (s[0]) {
								case "t":
									_.margintop = s[1]; break;
								case "b":
									_.marginbottom = s[1]; break;
								case "l":
									_.marginleft = s[1]; break;
								case "r":
									_.marginright = s[1]; break; } } }

					// SPIKE MASK ON ELEMENTS
					if (_.spike !== undefined) _.spike = getSpikePath(_.spike);

					// SHARP CORNERS FALLBACK
					if (_.corners !== undefined) {
						corner = _.corners.split(";");
						_.corners = {};
						for (u in corner) {
							if (!corner.hasOwnProperty(u)) continue;
							if (corner[u].length > 0) {
								_.corners[corner[u]] = jQuery('<' + corner[u] + '></' + corner[u] + '>');
								_.c.append(_.corners[corner[u]]);
							}
						}
					}
					// 0.114 - 0.25


					//CONVERT TEXT VALUES
					_.textalign = convToCLR(_.textalign);
					_.vbility = _R.revToResp(_.vbility, _R[id].rle, true);

					_.hoffset = _R.revToResp(_.hoffset, _R[id].rle, 0);
					_.voffset = _R.revToResp(_.voffset, _R[id].rle, 0);
					_.x = _R.revToResp(_.x, _R[id].rle, "l");
					_.y = _R.revToResp(_.y, _R[id].rle, "t");

					getStyleAtStart(L, 0, id);
					_R.sA(layer, "initialised", true);
					// 1-2 MS
					_R[id].c.trigger('layerinitialised', { layer: L[0].id, slider: id });
				}


				/***************************
					RUNTIME ON EACH CALL
				****************************/

				var elx = _.x[_R[id].level],
					ely = _.y[_R[id].level],
					tempGridOffset = _R.getGridOffset(id, o.slideIndex, _.basealign, _._isstatic),
					gw = tempGridOffset[0],
					gh = tempGridOffset[1],
					offsetx = tempGridOffset[2],
					offsety = tempGridOffset[3];

				//Cache Slide Index into the Layer Attributes
				_.slideIndex = o.slideIndex;

				if (o.mode !== "updateposition") {
					// HIDE CAPTION IF RESOLUTION IS TOO LOW
					if (_.vbility[_R[id].levelForced] == false || _.vbility[_R[id].levelForced] == "f" || (gw < _R[id].hideLayerAtLimit && _.layeronlimit == "on") || (gw < _R[id].hideAllLayerAtLimit)) {
						if (_.layerIsHidden !== true) _.p[0].classList.add("rs-layer-hidden");
						_.layerIsHidden = true;
					} else {
						if (_.layerIsHidden) _.p[0].classList.remove("rs-layer-hidden");
						_.layerIsHidden = false;
					}


					// FALL BACK TO NORMAL IMAGES
					_.poster = _.poster == undefined && _.thumbimage !== undefined ? _.thumbimage : _.poster;

					// LAYER IS AN IMAGE OR HAS IMAGE INSIDE
					if (_.layertype === "image") {
						_.imgOBJ = {};

						if (_.img.data('c') === "cover-proportional") {
							_R.sA(_.img[0], "owidth", _R.gA(_.img[0], "owidth", _.img[0].width));
							_R.sA(_.img[0], "oheight", _R.gA(_.img[0], "oheight", _.img[0].height));
							var ip = _R.gA(_.img[0], "owidth") / _R.gA(_.img[0], "oheight"),
								cp = gw / gh;

							if ((ip > cp && ip <= 1) || (ip < cp && ip > 1))
								_.imgOBJ = { width: "100%", height: "auto", left: elx === "c" || elx === "center" ? "50%" : elx === "left" || elx === "l" ? "0" : "auto", right: elx === "r" || elx === "right" ? "0" : "auto", top: ely === "c" || ely === "center" ? "50%" : ely === "top" || ely === "t" ? "0" : "auto", bottom: ely === "b" || ely === "bottom" ? "0" : "auto", x: elx === "c" || elx === "center" ? "-50%" : "0", y: ely === "c" || elx === "center" ? "-50%" : "0" };
							else
								_.imgOBJ = { height: "100%", width: "auto", left: elx === "c" || elx === "center" ? "50%" : elx === "left" || elx === "l" ? "0" : "auto", right: elx === "r" || elx === "right" ? "0" : "auto", top: ely === "c" || ely === "center" ? "50%" : ely === "top" || ely === "t" ? "0" : "auto", bottom: ely === "b" || ely === "bottom" ? "0" : "auto", x: elx === "c" || elx === "center" ? "-50%" : "0", y: ely === "c" || elx === "center" ? "-50%" : "0" };



						} else {
							// FALL BACK IF WIDTH/HEIGHT IS AUTO AND IMAGE IS ON ROOT
							if (_.group === undefined && _.width[_R[id].level] === "auto" && _.height[_R[id].level] === "auto") {
								_.width[_R[id].level] = _R.gA(_.img[0], "owidth", _.img[0].width);
								_.height[_R[id].level] = _R.gA(_.img[0], "owidth", _.img[0].height);
							}
							_.imgOBJ = { width: _.width[_R[id].level] !== "auto" || isNaN(_.width[_R[id].level]) && _.width[_R[id].level].indexOf("%") >= 0 ? "100%" : "auto", height: _.height[_R[id].level] !== "auto" || isNaN(_.height[_R[id].level]) && _.height[_R[id].level].indexOf("%") >= 0 ? "100%" : "auto" }

						}
					} // END OF LAYERTYPE IMAGE
					else
					if (_.layertype === "video") { // IF IT IS A VIDEO LAYER
						_R.manageVideoLayer(L, id, skey);
						if (o.mode !== "rebuild") _R.resetVideo(L, id, o.mode);
						if (_.aspectratio != undefined && _.aspectratio.split(":").length > 1 && _.bgvideo == 1) _R.prepareCoveredVideo(id, L);
						_.media = _.media === undefined ? _.deepiframe.length > 0 ? jQuery(_.deepiframe[0]) : jQuery(_.deepmedia[0]) : _.media;
						_.html5vid = _.html5vid === undefined ? _.deepiframe.length > 0 ? false : true : _.html5vid;
						_.mediaOBJ = { display: "block" };

						// SET WIDTH / HEIGHT
						var ww = _.width[_R[id].level],
							hh = _.height[_R[id].level];

						ww = ww === "auto" ? ww : (!_R.isNumeric(ww) && ww.indexOf("%") > 0) ? _._incolumn || _._ingroup ? "100%" : _.basealign === "grid" ? _R.iWA(id, o.slideIndex) * _R[id].CM.w : gw : _.rsp_bd !== "off" ? (parseFloat(ww) * _R[id].CM.w) + "px" : parseFloat(ww) + "px";
						hh = hh === "auto" ? hh : (!_R.isNumeric(hh) && hh.indexOf("%") > 0) ? _.basealign === "grid" ? _R.iHE(id) * _R[id].CM.w : gh : _.rsp_bd !== "off" ? (parseFloat(hh) * _R[id].CM.h) + "px" : parseFloat(hh) + "px";
						_.vd = _.vd === undefined ? _R[id].videos[L[0].id].ratio.split(':').length > 1 ? _R[id].videos[L[0].id].ratio.split(':')[0] / _R[id].videos[L[0].id].ratio.split(':')[1] : 1 : _.vd;
						if (_._incolumn && (ww === "100%" || hh === "auto") && _.ytid !== undefined) {
							var nvw = L.width(),
								nvh = hh === "auto" ? nvw / _.vd : hh;
							_.vidOBJ = { width: "auto", height: nvh };
							_.heightSetByVideo = true;
						} else {
							if (L[0].className.indexOf('rs-fsv') == -1) {
								hh = hh === "auto" && _.vd !== undefined && ww !== "auto" ? ww === "100%" ? L.width() / _.vd : ww / _.vd : hh;
								_.vidOBJ = { width: ww, height: hh };
							} else {

								if (_.basealign !== "grid") {
									offsetx = 0;
									offsety = 0;
								}
								_.x = _R.revToResp(0, _R[id].rle, 0);
								_.y = _R.revToResp(0, _R[id].rle, 0);

								_.vidOBJ = { width: ww, height: (_R[id].autoHeight ? _R[id].canv.height : hh) };
							}
							if (_.html5vid == false || !L.hasClass('rs-fsv')) _.mediaOBJ = { width: ww, height: hh, display: "block" };
							if (_._ingroup && _.vidOBJ.width !== null && _.vidOBJ.width !== undefined && !_R.isNumeric(_.vidOBJ.width) && _.vidOBJ.width.indexOf("%") > 0) _.OBJUPD.lppmOBJ = { minWidth: ww };
						}
					} // END OF POSITION AND STYLE READ OUTS OF VIDEO

					// RESPONIVE HANDLING OF CURRENT LAYER  - COLLECT ALL FOR LATER WORK


					if (!_._slidelink) _R[id].caches.calcResponsiveLayersList.push({ a: L, b: id, c: 0, d: _.rsp_bd, e: o.slideIndex });


					// ALL ELEMENTS IF THE MAIN ELEMENT IS REKURSIVE RESPONSIVE SHOULD BE REPONSIVE HANDLED
					if (_.rsp_ch === "on" && _.type !== "row" && _.type !== "column" && _.type !== "group" && _.type !== "image" && _.type !== "video" && _.type !== "shape") {

						L.find('*').each(function() {
							var jthis = jQuery(this);
							if (_R.gA(this, "stylerecorder") !== "true" && _R.gA(this, "stylerecorder") !== true) getStyleAtStart(jthis, "rekursive", id);
							_R[id].caches.calcResponsiveLayersList.push({ a: jthis, b: id, c: "rekursive", d: _.rsp_bd, e: o.slideIndex, RSL: L });
						});
					}


				} // NOT ONLY UPDATE POSITION !


				if (o.mode !== "preset") {

					_.oldeow = _.eow;
					_.oldeoh = _.eoh;
					_.eow = L.outerWidth(true);
					_.eoh = L.outerHeight(true);

					// CHECK FOR CURRENT SLIDE INDEX META
					if (_.metas !== undefined && _.metas.csi !== undefined && _.metas.csi.change !== _R[id].focusedSlideIndex) {
						_.metas.csi.change = _R[id].focusedSlideIndex;
						var digit = parseInt(_.metas.csi.change) + 1;
						_.metas.csi.c.innerHTML = (_R[id].realslideamount > 9 && digit < 10 ? '0' : '') + digit;
					}

					_.imgInFirefox = _.type == "image" && _.width[_R[id].level] == "auto" && _.height[_R[id].level] == "100%" && _R.isFirefox(id);

					//IMAGE WITH FULLHEIGHT NEED TO BE REPSECTED DIFFERENTLY IN FIREFOX
					if (_.imgInFirefox) {
						var imgw = _.img.width();
						_.eow = imgw !== 0 ? imgw : _.eow;
					}

					if (_.eow <= 0 && _.lastknownwidth !== undefined) _.eow = _.lastknownwidth;
					if (_.eoh <= 0 && _.lastknownheight !== undefined) _.eoh = _.lastknownheight;



					// BUILD AND UPDATE SHARP DECO CORNERS
					if (_.corners !== undefined && (_.type === "text" || _.type === "button" || _.type === "shape")) {
						for (corner in _.corners) {
							if (!_.corners.hasOwnProperty(corner)) continue;
							_.corners[corner].css('borderWidth', _.eoh + "px");
							var fcr = corner === "rs-fcrt" || corner === "rs-fcr";
							_.corners[corner].css('border' + (fcr ? "Right" : "Left"), '0px solid transparent');
							_.corners[corner].css('border' + (corner == "rs-fcrt" || corner == "rs-bcr" ? "Bottom" : "Top") + 'Color', _.bgcol);
						}
						_.eow = L.outerWidth(true);
					}

					// NEED CLASS FOR FULLWIDTH AND FULLHEIGHT LAYER SETTING !!
					if (_.eow == 0 && _.eoh == 0) {
						_.eow = _.basealign === "grid" ? _R[id].content.width : _R[id].module.width;
						_.eoh = _.basealign === "grid" ? _R[id].content.height : _R[id].module.height;
					}

					_.basealign = _R[id].justifyCarousel ? "grid" : _.basealign;


					var vofs = _.rsp_o === "on" ? parseInt(_.voffset[_R[id].level], 0) * _R[id].CM.w : parseInt(_.voffset[_R[id].level], 0),
						hofs = _.rsp_o === "on" ? parseInt(_.hoffset[_R[id].level], 0) * _R[id].CM.h : parseInt(_.hoffset[_R[id].level], 0),
						crw = _.basealign === "grid" ? _R.iWA(id, o.slideIndex) * _R[id].CM.w : gw,
						crh = _.basealign === "grid" ? _R.iHE(id) * (_R[id].keepBPHeight || _R[id].currentRowsHeight > _R[id].gridheight[_R[id].level] ? 1 : _R[id].CM.h) : gh;

					if (_R[id].gridEQModule == true || (_._lig !== undefined && _.type !== "row" && _.type !== "column" && _.type !== "group")) {
						crw = _._lig !== undefined ? _._lig.width() : _R[id].module.width;
						crh = _._lig !== undefined ? _._lig.height() : _R[id].module.height;
						offsetx = 0;
						offsety = 0;
					}

					// Soft Protection for Video Height if Video has no Dimenstion yet.
					if (_.type === "video" && _.vidOBJ != undefined) {
						if (_.vidOBJ.height >= 0 && _.eoh === 0) _.eoh = _.vidOBJ.height;
						if (_.vidOBJ.width >= 0 && _.eow === 0) _.eow = _.vidOBJ.width;
					}



					elx = elx === "c" || elx === "m" || elx === "center" || elx === "middle" ? (crw / 2 - _.eow / 2) + hofs : elx === "l" || elx === "left" ? hofs : elx === "r" || elx === "right" ? (crw - _.eow) - hofs : _.rsp_o !== "off" ? elx * _R[id].CM.w : elx;
					ely = ely === "m" || ely === "c" || ely === "center" || ely === "middle" ? (crh / 2 - _.eoh / 2) + vofs : ely === "t" || ely == "top" ? vofs : ely === "b" || ely == "bottom" ? (crh - _.eoh) - vofs : _.rsp_o !== "off" ? ely * _R[id].CM.w : ely;

					elx = _._slidelink ? 0 : _R[id].rtl && ("" + _.width[_R[id].level]).indexOf("%") == -1 ? parseInt(elx) + _.eow : elx;


					_.calcx = (parseInt(elx, 0) + offsetx);
					_.calcy = (parseInt(ely, 0) + offsety);





					// SET TOP/LEFT POSITION OF LAYER
					if (_.type !== "row" && _.type !== "column") _.OBJUPD.POBJ = { zIndex: _.zindex, top: _.calcy, left: _.calcx, overwrite: "auto" };
					else
					if (_.type !== "row") _.OBJUPD.POBJ = { zIndex: _.zindex, width: _.columnwidth, top: 0, left: 0, overwrite: "auto" };
					else
					if (_.type === "row") {
						_.OBJUPD.POBJ = { zIndex: _.zindex, width: (_.basealign === "grid" ? crw + "px" : "100%"), top: 0, left: _R[id].rtl ? offsetx * -1 : offsetx, overwrite: "auto" };

						if (_.cbreak <= _R[id].level) {
							if (L[0].className.indexOf("rev_break_columns") === -1) L[0].classList.add("rev_break_columns");
						} else {
							if (L[0].className.indexOf("rev_break_columns") > 0) L[0].classList.remove("rev_break_columns");
						}
						// Adjust ROW Calculations
						_.rowcalcx = _.OBJUPD.POBJ.left;
						_.pow = _.p.outerWidth(true);

					}



					if (_.blendmode !== undefined) _.OBJUPD.POBJ.mixBlendMode = _.blendmode;

					// LOOP ANIMATION WIDTH/HEIGHT
					if (_.frames.loop !== undefined || _.imgInFirefox) _.OBJUPD.LPOBJ = { width: _.eow, height: _.eoh }; //tpGS.gsap.set(_.lp,{width:_.eow,height:_.eoh});
					// ELEMENT IN GROUPS WITH % WIDTH AND HEIGHT SHOULD EXTEND PARRENT SIZES
					if (_._ingroup) {
						if (_._groupw !== undefined && !_R.isNumeric(_._groupw) && _._groupw.indexOf("%") > 0) _.OBJUPD.lppmOBJ.minWidth = _._groupw;
						if (_._grouph !== undefined && !_R.isNumeric(_._grouph) && _._grouph.indexOf("%") > 0) _.OBJUPD.lppmOBJ.minHeight = _._grouph;
					}

					if (o.mode === "updateposition") {
						if (_.caches.POBJ_LEFT !== _.OBJUPD.POBJ.left || _.caches.POBJ_TOP !== _.OBJUPD.POBJ.top) {
							tpGS.gsap.set(_.p, _.OBJUPD.POBJ);
							_.caches.POBJ_LEFT = _.OBJUPD.POBJ.left;
							_.caches.POBJ_TOP = _.OBJUPD.POBJ.top;
						}
					}
					if (o.animcompleted) _R.animcompleted(L, id);


				} // IT WAS ONLY PRESET ? OVERJUMP PARTS
			} // END OF FOR li RUNTRHOUGH OF LAYERS
		},

		hoverReverseDone: function(_) {
			if (_R[_.id]._L[_.L[0].id].textDecoration) tpGS.gsap.set(_R[_.id]._L[_.L[0].id].c, { textDecoration: _R[_.id]._L[_.L[0].id].textDecoration });
		},

		// MAKE SURE THE ANIMATION ENDS WITH A CLEANING ON MOZ TRANSFORMS
		animcompleted: function(_nc, id, force) {

			if (_R[id].videos === undefined) return;
			var _ = _R[id].videos[_nc[0].id];
			if (_ == undefined) return;
			if (_.type != undefined && _.type != "none")
				if (_.aplay == true || _.aplay == "true" || _.aplay == "on" || _.aplay == "1sttime") {

					//Addedd a double test if carousel and focus not changed, we can compare activeRSSSLider to index when oldfocus and newfocus still same !
					if (_.slideid === "static" || (_R[id].sliderType !== "carousel" || (_nc.closest('rs-slide').index() == _R[id].carousel.focused) || (_nc.closest('rs-slide').index() == _R[id].activeRSSlide && _R[id].carousel.oldfocused == _R[id].carousel.focused) || force)) _R.playVideo(_nc, id);
					_R.toggleState(_nc.data('videotoggledby'));
					if (_.aplay1 || _.aplay == "1sttime") {
						_.aplay1 = false;
						_.aplay = false;
					}
				} else {
					if (_.aplay == "no1sttime") _.aplay = true;
					_R.unToggleState(_nc.data('videotoggledby'));
				}

		},



		/********************************************************
			-	PREPARE AND DEFINE STATIC LAYER DIRECTIONS -
		*********************************************************/
		handleStaticLayers: function(_nc, id) {
			var s = 0,
				e = _R[id].realslideamount + 1;

			if (_R.gA(_nc[0], "onslides") !== undefined) {
				var ons = _R.gA(_nc[0], "onslides").split(";");
				for (var i in ons) {
					if (!ons.hasOwnProperty(i)) continue;
					var v = ons[i].split(":");
					if (v[0] === "s") s = parseInt(v[1], 0);
					if (v[0] === "e") e = parseInt(v[1], 0);
				}
			}
			s = Math.max(0, s);
			e = Math.min(_R[id].realslideamount, (e < 0 ? _R[id].realslideamount : e));


			e = (s === 1 || s === 0) && e === _R[id].realslideamount ? _R[id].realslideamount + 1 : e;
			_nc.data('startslide', s);
			_nc.data('endslide', e);
			_R.sA(_nc[0], "startslide", s);
			_R.sA(_nc[0], "endslide", e);
		},

		updateLayersOnFullStage: function(id) {
			if (_R[id].caches.calcResponsiveLayersList.length > 0) {
				if (_R[id].slideHasIframe !== true && _R[id].fullScreenMode !== true) { if (_R[id].sliderType === "carousel") _R[id].carousel.wrap.detach();
					else _R[id].canvas.detach(); }
				for (var i = 0; i < _R[id].caches.calcResponsiveLayersList.length; i++)
					if (_R[id].caches.calcResponsiveLayersList[i] !== undefined) calcResponsiveLayer(_R[id].caches.calcResponsiveLayersList[i]);
				if (_R[id].slideHasIframe !== true && _R[id].fullScreenMode !== true) { if (_R[id].sliderType === "carousel") _R[id].c[0].appendChild(_R[id].carousel.wrap[0]);
					else _R[id].c[0].appendChild(_R[id].canvas[0]); }
			}
		},

		/************************************
			ANIMATE ALL CAPTIONS
		*************************************/
		animateTheLayers: function(obj) {

			if (obj.slide === undefined) return false;
			var id = obj.id;
			if (_R[id].slides[obj.slide] === undefined && obj.slide !== "individual") return false;

			// Protection against double call if Carousel Auto Slide Swap processing
			if (_R[id].sliderType === "carousel") {
				if (obj.mode === "start" && _R[id].lastATLmode === "start") {
					if (obj.slide === _R[id].lastATLslide && (new Date().getTime() - _R[id].lastATLtime) < 1500) return;
					_R[id].lastATLtime = new Date().getTime();
				}
				_R[id].lastATLmode = obj.mode;
				_R[id].lastATLslide = obj.slide;
			}

			var key = obj.slide !== "individual" ? _R.gA(_R[id].slides[obj.slide], "key") : "individual",
				index = _R[id].pr_processing_key || _R[id].pr_active_key || 0;
			_R[id].focusedSlideIndex = index;
			_R[id].caches.calcResponsiveLayersList = [];

			// COLLECTION OF LAYERS
			_R[id].layers = _R[id].layers || {};

			if (key === "individual") _R[id].layers["individual"] = _R[id].layers["individual"] === undefined ? _R[id].carousel.showLayersAllTime === "all" ? getLayersInSlide(jQuery(_R[id].c), 'rs-layer', 'rs-layer-static') : getLayersInSlide(jQuery(_R[id].c), 'rs-on-car') : _R[id].layers["individual"];
			else {
				_R[id].layers[key] = _R[id].layers[key] === undefined ? _R[id].carousel.showLayersAllTime === "all" ? [] : getLayersInSlide(jQuery(_R[id].slides[obj.slide]), 'rs-layer', (_R[id].sliderType === "carousel" ? 'rs-on-car' : undefined)) : _R[id].layers[key];
				_R[id].layers["static"] = _R[id].layers["static"] === undefined ? getLayersInSlide(jQuery(_R[id].c.find('rs-static-layers')), 'rs-layer', 'rs-on-car') : _R[id].layers["static"];
				_R[id].sbas[key] = _R[id].sbas[key] === undefined ? getLayersInSlide(jQuery(_R[id].slides[obj.slide]), 'rs-sba') : _R[id].sbas[key];
			}


			//_R.updateDims(id);
			var shPermnt = obj.mode === "rebuild" && _R[id].sliderType === "carousel" && key === "individual";

			// PREPARE AND ANIMATE SLIDE LAYERS
			if (key !== undefined && _R[id].layers[key]) _R.initLayer({ id: id, slideIndex: obj.slide, skey: key, mode: obj.mode, animcompleted: shPermnt });

			// PREPARE AND ANIMATE STATIC LAYERS
			if (_R[id].layers["static"]) _R.initLayer({ id: id, skey: "static", slideIndex: 'static', mode: obj.mode, animcompleted: shPermnt });

			// UPDATE LAYER STYLES ON THE FULL STAGE
			_R.updateLayersOnFullStage(id);

			if (obj.mode === "preset" && (_R[id].slidePresets === undefined || _R[id].slidePresets[obj.slide] === undefined)) {
				_R[id].slidePresets = _R[id].slidePresets === undefined ? {} : _R[id].slidePresets;
				_R[id].slidePresets[obj.slide] = true;
				_R[id].c.trigger('revolution.slideprepared', { slide: obj.slide, key: key });
			}

			_R[id].heightInLayers = _R[id].module.height;
			_R[id].widthInLayers = _R[id].module.width;
			_R[id].levelInLayers = _R[id].level;

			var cache = { id: id, skey: key, slide: obj.slide, key: key, mode: obj.mode, index: index };
			// MODIFICATED REQUEST ANIMATION BASED START OF ANIMATION
			window.requestAnimationFrame(function() {
				//_R[obj.id].heightInLayers = undefined;
				//_R[obj.id].widthInLayers = undefined;
				if (_R[id].dimensionReCheck[key] === undefined) {
					_R.updateLayerDimensions(cache);
					if (_R[id].doubleDimensionCheck !== true) setTimeout(function() {
						_R.updateLayerDimensions(cache);
						_R.updateRowZones(cache);
					}, 150);
					else _R.updateRowZones(cache);
					_R[id].doubleDimensionCheck = true;
					_R[id].dimensionReCheck[key] = true;
				} else _R.updateRowZones(cache);

				if (key !== undefined && _R[id].layers[key])
					for (var li in _R[id].layers[key])
						if (_R[id].layers[key].hasOwnProperty(li)) _R.renderLayerAnimation({ layer: jQuery(_R[id].layers[key][li]), id: id, mode: obj.mode, caller: obj.caller });
				if (_R[id].layers.static)
					for (var li in _R[id].layers.static)
						if (_R[id].layers.static.hasOwnProperty(li)) _R.renderLayerAnimation({ layer: jQuery(_R[id].layers.static[li]), id: id, mode: obj.mode, caller: obj.caller });
				if (_R[id].mtl != undefined) _R[id].mtl.resume();
			});
		},

		updateRowZones: function(obj) {
			if ((_R[obj.id].rowzones !== undefined && _R[obj.id].rowzones.length > 0 && obj.index >= 0 && (_R[obj.id].rowzones[Math.min(obj.index, _R[obj.id].rowzones.length)] && _R[obj.id].rowzones[Math.min(obj.index, _R[obj.id].rowzones.length)].length > 0)) || (_R[obj.id].srowzones !== undefined && _R[obj.id].srowzones.length > 0) || (_R[obj.id].smiddleZones !== undefined && _R[obj.id].smiddleZones.length > 0)) {
				_R.updateDims(obj.id);
				//requestAnimationFrame(function() {_R.updateMiddleZonesAndESG(obj.id);}); // CENTERED ROWS, SOLTION NOW VIA CSS
				_R.initLayer({ id: obj.id, skey: obj.key, slideIndex: obj.slide, mode: "updateposition" });
				_R.initLayer({ id: obj.id, skey: "static", slideIndex: 'static', mode: "updateposition" });
				if (obj.mode === "start" || obj.mode === "preset") _R.manageNavigation(obj.id);
			}
		},

		updateLayerDimensions: function(obj) {
			var changes = false;
			_R[obj.id].caches.calcResponsiveLayersList = [];
			if (obj.key !== undefined && (obj.key == "individual" || _R[obj.id].layers[obj.key] !== undefined) && _R.checkLayerDimensions({ id: obj.id, skey: obj.key, slideIndex: obj.slide })) changes = true;
			_R.initLayer({ id: obj.id, skey: obj.key, slideIndex: obj.slide, mode: "updateAndResize" });
			if (_R[obj.id].layers["static"] && _R.checkLayerDimensions({ id: obj.id, skey: "static", slideIndex: 'static' })) { changes = true;
				_R.initLayer({ id: obj.id, skey: "static", slideIndex: 'static', mode: "updateAndResize" }); }
			if (changes) _R.updateLayersOnFullStage(obj.id);
		},

		// Update the Layers which are or will be animated currently .  i.e. Stage size changed based on Navigation outter size. (call from updateDimensions so far);
		updateAnimatingLayerPositions: function(obj) {
			_R.initLayer({ id: obj.id, skey: obj.key, slideIndex: obj.slide, mode: "updateposition" })
		},

		//////////////////////////
		//	REMOVE THE CAPTIONS //
		/////////////////////////
		removeTheLayers: function(slide, id, allforce) {

			var skey = _R.gA(slide[0], "key");
			if (_R[id].sloops && _R[id].sloops[skey] && _R[id].sloops[skey].tl) _R[id].sloops[skey].tl.pause();
			//REMOVE CURRENT LAYERS
			for (var li in _R[id].layers[skey])
				if (_R[id].layers[skey].hasOwnProperty(li)) _R.renderLayerAnimation({ layer: jQuery(_R[id].layers[skey][li]), frame: "frame_999", mode: "continue", remove: true, id: id, allforce: allforce });
				//REMOVE STATIC LAYERS IF NEEDED
			for (var li in _R[id].layers.static)
				if (_R[id].layers.static.hasOwnProperty(li)) _R.renderLayerAnimation({ layer: jQuery(_R[id].layers.static[li]), frame: "frame_999", mode: "continue", remove: true, id: id, allforce: allforce });

		},

		/************************************
				RENDER LAYER ANIMATIONS
		*************************************/
		renderLayerAnimation: function(obj) {

			var L = obj.layer,
				id = obj.id,
				scren = _R[id].level,
				_ = _R[id]._L[L[0].id],
				fifame = "frame_1",
				cachetime = _.timeline !== undefined ? _.timeline.time() : undefined,
				ignoreframes = false,
				calledframereached = false,
				sl = "none",
				tPE;


			if ((obj.caller === "containerResized_2" || obj.caller === "swapSlideProgress_2") && _.animationRendered !== true) return;
			_.animationRendered = true;

			// ONLY PREPARE ELEMENTS WE REALLY NEED
			if (obj.mode === "preset" && _.frames.frame_1.timeline.waitoncall !== true && _.scrollBasedOffset === undefined) return;
			if (obj.mode == "trigger") _.triggeredFrame = obj.frame;
			//STATIC LAYERS CAN BE IGNORED IN SOME CASES
			if (_._isstatic) {

				var cs = _R[id].sliderType === "carousel" && _R[id].carousel.oldfocused !== undefined ? _R[id].carousel.oldfocused : _R[id].pr_lastshown_key === undefined ? 1 : parseInt(_R[id].pr_lastshown_key, 0) + 1,
					ns = _R[id].sliderType === "carousel" ? _R[id].pr_next_key === undefined ? cs === 0 ? 1 : cs : parseInt(_R[id].pr_next_key, 0) + 1 : _R[id].pr_processing_key === undefined ? cs : parseInt(_R[id].pr_processing_key, 0) + 1,
					inrangecs = cs >= _.startslide && cs <= _.endslide,
					inrangens = ns >= _.startslide && ns <= _.endslide;
				sl = cs === _.endslide && obj.mode === "continue" ? true : obj.mode !== "continue" && cs !== _.endslide ? false : "none";

				if (obj.allforce === true || sl === true) {
					//Force to Redo Whatever we need to do
					if (obj.mode === "continue" && obj.frame === "frame_999" && (inrangens || _.lastRequestedMainFrame === undefined)) return;

				} else {


					if (obj.mode === "preset" && (_.elementHovered || !inrangens)) return;
					if (obj.mode === "rebuild" && !inrangecs && !inrangens) return;
					if (obj.mode === "start" && inrangens && _.lastRequestedMainFrame === "frame_1") return;
					if ((obj.mode === "start" || obj.mode === "preset") && _.lastRequestedMainFrame === "frame_999" && _.leftstage !== true) return;
					if (obj.mode === "continue" && obj.frame === "frame_999" && (inrangens || _.lastRequestedMainFrame === undefined)) return;
					if (obj.mode === "start" && !inrangens) return;

				}

			} else if (obj.mode === "start" && _.triggercache !== "keep") _.triggeredFrame = undefined;



			if (obj.mode === "start") {
				if (_.layerLoop !== undefined) _.layerLoop.count = 0;
				obj.frame = _.triggeredFrame === undefined ? 0 : _.triggeredFrame;
			}

			if ((obj.mode !== "continue" && obj.mode !== "trigger") && _.timeline !== undefined && (!_._isstatic || _.leftstage !== true)) _.timeline.pause(0); //(!_._isstatic || _.leftstage!==true) is Fix for Closed Menu due Togglelayer action which reopens if this not exists
			if ((obj.mode === "continue" || obj.mode === "trigger") && _.timeline !== undefined) _.timeline.pause();

			_.timeline = tpGS.gsap.timeline({ paused: true });


			if ((_.type === "text" || _.type === "button") && (_.splitText === undefined || (_.splitTextFix === undefined && (obj.mode === "start" || obj.mode === "preset")))) {
				updateSplitContent({ layer: L, id: id });
				if (obj.mode === "start") _.splitTextFix = true;
			}




			// LETS GO THROUGH THE FRAMES
			for (var fiin in _.ford) {
				if (!_.ford.hasOwnProperty(fiin)) continue;

				var fi = _.ford[fiin],
					renderJustFrom = false;
				if (fi === "frame_0" || fi === "frame_hover" || fi === "loop") continue;
				if (fi === "frame_999" && !_.frames[fi].timeline.waitoncall && _.frames[fi].timeline.start >= _R[id].duration && obj.remove !== true) _.frames[fi].timeline.waitoncall = true;

				// IF SCENE STARTS, AND NO MEMORY SET, RESET CALL STATES
				if (obj.mode === "start" && _.triggercache !== "keep") _.frames[fi].timeline.callstate = _.frames[fi].timeline.waitoncall ? "waiting" : "";

				//SET TRIGGER STATE ON SINGLE FRAMES
				if (obj.mode === "trigger" && _.frames[fi].timeline.waitoncall) {
					if (fi === obj.frame) {
						_.frames[fi].timeline.triggered = true;
						_.frames[fi].timeline.callstate = "called";
					} else
						_.frames[fi].timeline.triggered = false;
				}

				if (obj.mode !== "rebuild" && !_.frames[fi].timeline.triggered) _.frames[fi].timeline.callstate = _.frames[fi].timeline.waitoncall ? "waiting" : "";

				if (obj.fastforward !== false) {
					// WE DONT NEED TO RENDER THE FRAMES COMING BEFORE THE TRIGGERED ONE !
					if ((obj.mode === "continue" || obj.mode === "trigger") && calledframereached === false && fi !== obj.frame) continue;
					// IF REBUILD, CHECK IF WE ALREADY CALLED SOMETHIGN ELSE THAN FRAME_1 and Skip frames before to fix Timeline Issues
					if ((obj.mode === "rebuild" || obj.mode === "preset") && calledframereached === false && (_.triggeredFrame !== undefined && fi !== _.triggeredFrame)) continue;
					if (fi === obj.frame || (obj.mode === "rebuild" && fi === _.triggeredFrame)) calledframereached = true;
				} else {
					if (fi === obj.frame) calledframereached = true;
				}


				//SKIP FRAME IF IT IS ON ACTION, IF TWO NEIGHBOUR ACTION BASED, SKIP ANYTHING ELSE
				if (fi !== obj.frame && _.frames[fi].timeline.waitoncall && _.frames[fi].timeline.callstate !== "called") ignoreframes = true;
				if (fi !== obj.frame && calledframereached) ignoreframes = ignoreframes === true && _.frames[fi].timeline.waitoncall ? 'skiprest' : ignoreframes === true ? false : ignoreframes;

				if (_.hideonfirststart === undefined && fi === "frame_1" && _.frames[fi].timeline.waitoncall) _.hideonfirststart = true;



				// COMING FRAME IGNORED, OR FRAME IS NOT CALLED YET, OR FRAME IS WAITING, BUT NEVER RENDERED YET ?
				if (ignoreframes && _.frames[fi].timeline.callstate === "waiting" && obj.mode === "preset" && _.firstTimeRendered != true) {

					if (_._isstatic && _.currentframe === undefined) continue;
					renderJustFrom = true;
					_.firstTimeRendered = true;
				} else
				if ((ignoreframes === "skiprest") || (_.frames[fi].timeline.callstate !== "called" && ignoreframes && obj.toframe !== fi)) continue;


				if (fi === "frame_999" && (sl === false) && (obj.mode === "continue" || obj.mode === "start" || obj.mode === "rebuild")) continue;

				_.fff = fi === fifame && (obj.mode !== "trigger" || _.currentframe === "frame_999" || _.currentframe === "frame_0" || _.currentframe === undefined);
				if (obj.mode === "trigger" && obj.frame === "frame_1" && _.leftstage === false) _.fff = false; // Fix When 1st Layer triggered after frame_999 started but not yet animated out !!

				if (!renderJustFrom) {
					_.frames[fi].timeline.callstate = "called";
					_.currentframe = fi;
				}





				var _frameObj = _.frames[fi],
					_fromFrameObj = _.fff ? _.frames.frame_0 : undefined,
					ftl = tpGS.gsap.timeline(),
					tt = tpGS.gsap.timeline(),
					aObj = _.c,
					sfx = _frameObj.sfx !== undefined ? checkSFXAnimations(_frameObj.sfx.effect, _.m, _frameObj.timeline.ease) : false,
					speed = _frameObj.timeline.speed / 1000,
					maxSplitDelay = 0,
					anim = convertTransformValues({ id: id, frame: _frameObj, layer: L, ease: _frameObj.timeline.ease, splitAmount: aObj.length, target: fi, forcefilter: (_.frames.frame_hover !== undefined && _.frames.frame_hover.filter !== undefined) }),
					from = _.fff ? convertTransformValues({ id: id, frame: _fromFrameObj, layer: L, ease: _frameObj.timeline.ease, splitAmount: aObj.length, target: "frame_0" }) : undefined,
					mask = _frameObj.mask !== undefined ? convertTransformValues({ id: id, frame: { transform: { x: _frameObj.mask.x, y: _frameObj.mask.y } }, layer: L, ease: anim.ease, target: "mask" }) : undefined,
					frommask = mask !== undefined && _.fff ? convertTransformValues({ id: id, frame: { transform: { x: _fromFrameObj.mask.x, y: _fromFrameObj.mask.y } }, layer: L, ease: anim.ease, target: "frommask" }) : undefined,
					origEase = anim.ease;

				anim.force3D = true;

				if (sfx.type === "block") {
					sfx.ft[0].background = _frameObj.sfx.fxc;
					//_fromFrameObj.mask.overflow = "hidden";
					//_frameObj.mask.overflow = "hidden";
					sfx.ft[0].visibility = 'visible';
					sfx.ft[1].visibility = 'visible';
					ftl.add(tpGS.gsap.fromTo(sfx.bmask_in, speed / 2, sfx.ft[0], sfx.ft[1], 0));
					ftl.add(tpGS.gsap.fromTo(sfx.bmask_in, speed / 2, sfx.ft[1], sfx.t, speed / 2));
					if (fi === "frame_0" || fi === "frame_1") from.opacity = 0;

					// If the following line is added, the Layer Animation starts at wrong time (Already visible at start) if only SFX Block Out set to layer.
					//else if (fi==="frame_999" ) ftl.add(tt.staggerFromTo(aObj,0.05,{ autoAlpha:1},{autoAlpha:0,delay:speed/2},0),0.001);
				}


				// SET COLOR ON LAYER (TO AND FROM)
				if (_frameObj.color !== undefined) anim.color = _frameObj.color;
				else
				if (_.color !== undefined && _.color[scren] !== "npc") anim.color = _.color[scren];

				if (_fromFrameObj !== undefined && _fromFrameObj.color !== undefined) from.color = _fromFrameObj.color;
				else
				if (_fromFrameObj !== undefined && _.color !== undefined && _.color[scren] !== "npc") from.color = _.color[scren];



				// SET BACKGROUNDCOLOR ON LAYER (TO AND FROM)
				if (_frameObj.bgcolor !== undefined)
					if (_frameObj.bgcolor.indexOf("gradient") >= 0) anim.background = _frameObj.bgcolor;
					else anim.backgroundColor = _frameObj.bgcolor;
				else
				if (_.bgcolinuse === true)
					if (_.bgcol.indexOf("gradient") >= 0) anim.background = _.bgcol;
					else anim.backgroundColor = _.bgcol;

				if (_fromFrameObj !== undefined) {
					if (_fromFrameObj.bgcolor !== undefined)
						if (_fromFrameObj.bgcolor.indexOf("gradient") >= 0) from.background = _fromFrameObj.bgcolor;
						else from.backgroundColor = _fromFrameObj.bgcolor;
					else
					if (_.bgcolinuse === true)
						if (_.bgcol.indexOf("gradient") >= 0) from.background = _.bgcol;
						else from.backgroundColor = _.bgcol;
				}

				// ANIMATE CHARS, WORDS, LINES
				if (_.splitText !== undefined && _.splitText !== false) {
					for (var i in splitTypes) {
						if (_frameObj[splitTypes[i]] !== undefined && !_.quickRendering) {
							var sObj = _.splitText[splitTypes[i]],
								sanim = convertTransformValues({ id: id, frame: _frameObj, source: splitTypes[i], ease: origEase, layer: L, splitAmount: sObj.length, target: fi + "_" + splitTypes[i] }),
								sfrom = (_.fff ? convertTransformValues({ id: id, frame: _fromFrameObj, ease: sanim.ease, source: splitTypes[i], layer: L, splitAmount: sObj.length, target: "frame_0_" + splitTypes[i] }) : undefined),
								sDelay = _.frames[fi].dosplit ? _frameObj[splitTypes[i]].delay === undefined ? 0.05 : _frameObj[splitTypes[i]].delay / 100 : 0;

							// SET COLOR ON SPLIT  (TO AND FROM)
							if (_.color[scren] !== anim.color || fi !== "frame_1") sanim.color = anim.color; // ADDED IF TO RESPECT INLINE STYLES
							if (from !== undefined && _.color[scren] !== from.color) sfrom.color = from.color; // ADDED IF TO RESPECT INLINE STYLES
							if (sfrom !== undefined && sfrom.color !== anim.color) sanim.color = anim.color;
							var $anim = _R.clone(sanim),
								$from = _.fff ? _R.clone(sfrom) : undefined,
								dir = _frameObj[splitTypes[i]].dir;

							delete $anim.dir;

							$anim.data = { splitted: true };

							$anim.stagger = dir === "center" || dir === "edge" ? offsetStagger({ each: sDelay, offset: sDelay / 2, from: dir }) : { each: sDelay, from: dir };
							$anim.duration = speed;

							if ($from !== undefined) {
								if ($from.opacity!==undefined && (_R.ISM || window.isSafari11)) $from.opacity = Math.max(0.001,parseFloat($from.opacity));
								delete $from.dir;
							}

							if (_.fff) ftl.add(tt.fromTo(sObj, $from, $anim), 0);
							else
								ftl.add(tt.to(sObj, $anim), 0);


							maxSplitDelay = Math.max(maxSplitDelay, (sObj.length * sDelay));
						}
					}
				}



				//SPEED SYNC WITH THE SPLIT SPEEDS IF NECESSARY
				speed = speed + maxSplitDelay;
				if (tPE === undefined) tPE = _R[id].perspectiveType === "isometric" ? 0 : _R[id].perspectiveType === "local" ? anim.transformPerspective !== undefined ? anim.transformPerspective : _.fff && from.transfromPerspective !== undefined ? from.transfromPerspective : _R[id].perspective : _R[id].perspective;
				_.knowTransformPerspective = tPE;

				// MOVE FILTER FROM LAYER TO MASK
				if (_.fsom && (anim.filter!==undefined || (_.fff && from.filter!==undefined))) {
					mask.filter = anim.filter;
					mask['-webkit-filter'] = anim.filter;
					delete anim.filter;
					delete anim['-webkit-filter'];
					if (_.fff && from.filter!==undefined) {
						frommask = frommask || {};
						frommask.filter = from.filter;
						frommask['-webkit-filter'] = from.filter;
						delete from.filter;
						delete from['-webkit-filter'];
					}
					_.forceFsom = true;
				} else {
					_.forceFsom = false;
				}

				// ANIMATE MASK
				_.useMaskAnimation = (_.pxundermask || (mask !== undefined && ((_fromFrameObj !== undefined && _fromFrameObj.mask.overflow === "hidden") || _frameObj.mask.overflow === "hidden")));

				if (_.useMaskAnimation || _.forceFsom) {
					if (_.useMaskAnimation) ftl.add(tpGS.gsap.to(_.m, 0.001, { overflow: "hidden" }), 0); else ftl.add(tpGS.gsap.to(_.m, 0.001, { overflow: "visible" }), 0);
					if (_.type === "column" && _.useMaskAnimation) ftl.add(tpGS.gsap.to(_.cbgmask, 0.001, { overflow: "hidden" }), 0);
					if (_.btrans) {
						if (frommask) { frommask.rotationX = _.btrans.rX;
							frommask.rotationY = _.btrans.rY;
							frommask.rotationZ = _.btrans.rZ;
							frommask.opacity = _.btrans.o; }
						mask.rotationX = _.btrans.rX;
						mask.rotationY = _.btrans.rY;
						mask.rotationZ = _.btrans.rZ;
						mask.opacity = _.btrans.o;
					}
					if (_.fff)
						ftl.add(tpGS.gsap.fromTo([_.m, _.cbgmask], speed, _R.clone(frommask), _R.clone(mask)), 0.001);
					else
						ftl.add(tpGS.gsap.to([_.m, _.cbgmask], speed, _R.clone(mask)), 0.001);
				} else {
					if (_.btrans !== undefined) {
						var mtrans = { x: 0, y: 0, filter: "none", opacity: _.btrans.o, rotationX: _.btrans.rX, rotationY: _.btrans.rY, rotationZ: _.btrans.rZ, overflow: "visible" }
						if (_.btrans.rX !== 0 || _.btrans.rY != 0) {
							_.maskHasPerspective = true;
							mtrans.transformPerspective = tPE;
						}
						ftl.add(tpGS.gsap.to(_.m, 0.001, mtrans), 0);
					} else
						ftl.add(tpGS.gsap.to(_.m, 0.001, { clearProps: "transform", overflow: "visible" }), 0);
				}

				anim.force3D = "auto";


				// ANIMATE ELEMENT
				if (_.fff) {
					anim.visibility = "visible";
					if (_.cbg !== undefined) ftl.fromTo(_.cbg, speed, from, anim, 0);

					// safari bug fix
					if (_R[id].BUG_safari_clipPath && (from.clipPath || anim.clipPath || _.spike)) {
						if (!from.z || !parseInt(from.z, 10)) from.z = -0.0001;
						if (!anim.z || !parseInt(anim.z, 10)) anim.z = 0;
					}
					if (_.cbg !== undefined && _.type === "column") ftl.fromTo(aObj, speed, reduceColumn(from), reduceColumn(anim), 0);
					else ftl.fromTo(aObj, speed, from, anim, 0);
					ftl.invalidate();


				} else {
					if (_.frame !== "frame_999") anim.visibility = "visible";
					if (_.cbg !== undefined) ftl.to(_.cbg, speed, anim, 0);
					// safari bug fix
					if (_R[id].BUG_safari_clipPath && (anim.clipPath || _.spike) && (!anim.z || !parseInt(anim.z, 10))) anim.z = 0 - Math.random() * 0.01;
					if (_.cbg !== undefined && _.type === "column") ftl.to(aObj, speed, reduceColumn(anim), 0);
					else ftl.to(aObj, speed, anim, 0);
				}


				if (origEase !== undefined && typeof origEase !== "object" && typeof origEase !== "function" && origEase.indexOf("SFXBounce") >= 0) ftl.to(aObj, speed, { scaleY: 0.5, scaleX: 1.3, ease: anim.ease + "-squash", transformOrigin: "bottom" }, 0.0001);

				//WAITING FRAMES ADD DIRECTLY AFTER OTHER FRAMES

				var pos = (obj.mode === "trigger" || ((ignoreframes === true || ignoreframes === "skiprest") && obj.mode === "rebuild")) && obj.frame !== fi && _frameObj.timeline.start !== undefined && _R.isNumeric(_frameObj.timeline.start) ?
					"+=" + parseInt(_frameObj.timeline.startRelative, 0) / 1000 :
					_frameObj.timeline.start === "+=0" || _frameObj.timeline.start === undefined ? "+=0.05" : parseInt(_frameObj.timeline.start, 0) / 1000;


				_.timeline.addLabel(fi, pos);
				_.timeline.add(ftl, pos);
				_.timeline.addLabel(fi + "_end", "+=0.01");
				ftl.eventCallback("onStart", tweenOnStart, [{ id: id, frame: fi, L: L, tPE: tPE }]);
				if (_.animationonscroll == "true" || _.animationonscroll == true) {

					ftl.eventCallback("onUpdate", tweenOnUpdate, [{ id: id, frame: fi, L: L }]);
					ftl.smoothChildTiming = true;
				} else {
					ftl.eventCallback("onUpdate", tweenOnUpdate, [{ id: id, frame: fi, L: L }]);
				}
				ftl.eventCallback("onComplete", tweenOnEnd, [{ id: id, frame: fi, L: L, tPE: tPE }]);
			}

			//RENDER LOOP ANIMATION
			if (_.frames.loop !== undefined) {
				var lspeed = parseInt(_.frames.loop.timeline.speed, 0) / 1000,
					lstart = parseInt(_.frames.loop.timeline.start) / 1000 || 0,
					lsspeed = ((obj.mode !== "trigger" && obj.frame !== "frame_999") || obj.frame !== "frame_999") ? 0.2 : 0, // Check if it is triggered, or last frame called or triggered but not last frame called before adding animation delay!
					lssstart = lstart + lsspeed;

				_.loop = {
					root: tpGS.gsap.timeline({}),
					preset: tpGS.gsap.timeline({}),
					move: tpGS.gsap.timeline({ repeat: -1, yoyo: _.frames.loop.timeline.yoyo_move }),
					rotate: tpGS.gsap.timeline({ repeat: -1, yoyo: _.frames.loop.timeline.yoyo_rotate }),
					scale: tpGS.gsap.timeline({ repeat: -1, yoyo: _.frames.loop.timeline.yoyo_scale }),
					filter: tpGS.gsap.timeline({ repeat: -1, yoyo: _.frames.loop.timeline.yoyo_filter })
				};

				var lif = _.frames.loop.frame_0,
					lof = _.frames.loop.frame_999,
					loopsfilter = 'blur(' + parseInt(lif.blur || 0, 0) + 'px) grayscale(' + parseInt(lif.grayscale || 0, 0) + '%) brightness(' + parseInt(lif.brightness || 100, 0) + '%)',
					loopendfilter = 'blur(' + (lof.blur || 0) + 'px) grayscale(' + (lof.grayscale || 0) + '%) brightness(' + (lof.brightness || 100) + '%)';

				_.loop.root.add(_.loop.preset, 0);
				_.loop.root.add(_.loop.move, lsspeed);
				_.loop.root.add(_.loop.rotate, lsspeed);
				_.loop.root.add(_.loop.scale, lsspeed);
				_.loop.root.add(_.loop.filter, lsspeed);

				if (loopsfilter === 'blur(0px) grayscale(0%) brightness(100%)' && loopendfilter === 'blur(0px) grayscale(0%) brightness(100%)') {
					loopsfilter = "none";
					loopendfilter = "none";
				}

				//_.loop.root.delay = lssstart;
				lof.originX = lif.originX;
				lof.originY = lif.originY;
				lof.originZ = lif.originZ;

				//LOOP MOVE ANIMATION
				if (tPE === undefined) tPE = _R[id].perspectiveType === "isometric" ? 0 : _R[id].perspectiveType === "local" ? anim !== undefined ? anim.transformPerspective !== undefined ? anim.transformPerspective : _.fff && from.transfromPerspective !== undefined ? from.transfromPerspective : _R[id].perspective : _R[id].perspective : _R[id].perspective;

				if (!_.frames.loop.timeline.curved) {
					// Check if it is triggered, or last frame called or triggered but not last frame called before reset values to loop_frame_0 !
					if ((obj.mode !== "trigger" && obj.frame !== "frame_999") || obj.frame !== "frame_999") _.loop.preset.fromTo(_.lp, lsspeed, { '-webkit-filter': loopsfilter, 'filter': loopsfilter, x: 0, y: 0, z: 0, minWidth: (_._incolumn || _._ingroup ? "100%" : _.eow === undefined ? 0 : _.eow), minHeight: (_._incolumn || _._ingroup ? "100%" : _.eoh === undefined ? 0 : _.eoh), scaleX: 1, scaleY: 1, skewX: 0, skewY: 0, rotationX: 0, rotationY: 0, rotationZ: 0, transformPerspective: tPE, transformOrigin: lof.originX + " " + lof.originY + " " + lof.originZ, opacity: 1 }, checkLoopSkew({ x: lif.x * _R[id].CM.w, y: lif.y * _R[id].CM.w, z: lif.z * _R[id].CM.w, scaleX: lif.scaleX, skewX: lif.skewX, skewY: lif.skewY, scaleY: lif.scaleY, rotationX: lif.rotationX, rotationY: lif.rotationY, rotationZ: lif.rotationZ, ease: "sine.out", opacity: lif.opacity, '-webkit-filter': loopsfilter, 'filter': loopsfilter }), 0);
					_.loop.move.to(_.lp, (_.frames.loop.timeline.yoyo_move ? lspeed / 2 : lspeed), { x: lof.x * _R[id].CM.w, y: lof.y * _R[id].CM.w, z: lof.z * _R[id].CM.w, ease: _.frames.loop.timeline.ease });
				} else {
					//CALCULATE EDGES
					var sangle = parseInt(_.frames.loop.timeline.radiusAngle, 0) || 0,
						v = [{ x: (lif.x - lif.xr) * _R[id].CM.w, y: 0, z: (lif.z - lif.zr) * _R[id].CM.w },
							{ x: 0, y: (lif.y + lif.yr) * _R[id].CM.w, z: 0 },
							{ x: (lof.x + lof.xr) * _R[id].CM.w, y: 0, z: (lof.z + lof.zr) * _R[id].CM.w },
							{ x: 0, y: (lof.y - lof.yr) * _R[id].CM.w, z: 0 }
						],
						motionPath = { type: "thru", curviness: _.frames.loop.timeline.curviness, path: [], autoRotate: _.frames.loop.timeline.autoRotate };

					for (var bind in v) {
						if (!v.hasOwnProperty((bind))) continue;
						motionPath.path[bind] = v[sangle];
						sangle++;
						sangle = sangle == v.length ? 0 : sangle;
					}
					// Check if it is triggered, or last frame called or triggered but not last frame called before reset values to loop_frame_0 !
					if ((obj.mode !== "trigger" && obj.frame !== "frame_999") || obj.frame !== "frame_999") _.loop.preset.fromTo(_.lp, lsspeed, { '-webkit-filter': loopsfilter, 'filter': loopsfilter, x: 0, y: 0, z: 0, minWidth: (_._incolumn || _._ingroup ? "100%" : _.eow === undefined ? 0 : _.eow), minHeight: (_._incolumn || _._ingroup ? "100%" : _.eoh === undefined ? 0 : _.eoh), scaleX: 1, scaleY: 1, skewX: 0, skewY: 0, rotationX: 0, rotationY: 0, rotationZ: 0, transformPerspective: tPE, transformOrigin: lof.originX + " " + lof.originY + " " + lof.originZ, opacity: 1 }, checkLoopSkew({ x: motionPath.path[3].x, y: motionPath.path[3].y, z: motionPath.path[3].z, scaleX: lif.scaleX, skewX: lif.skewX, skewY: lif.skewY, scaleY: lif.scaleY, rotationX: lif.rotationX, rotationY: lif.rotationY, rotationZ: lif.rotationZ, '-webkit-filter': loopsfilter, 'filter': loopsfilter, ease: "sine.inOut", opacity: lif.opacity }), 0);
					if (checkMotionPath(motionPath)) _.loop.move.to(_.lp, (_.frames.loop.timeline.yoyo_move ? lspeed / 2 : lspeed), { motionPath: motionPath, ease: _.frames.loop.timeline.ease });
				}

				//LOOP ROTATE ANIMATION
				_.loop.rotate.to(_.lp, (_.frames.loop.timeline.yoyo_rotate ? lspeed / 2 : lspeed), { rotationX: lof.rotationX, rotationY: lof.rotationY, rotationZ: lof.rotationZ, ease: _.frames.loop.timeline.ease });

				//LOOP SCALE ANIMATION
				_.loop.scale.to(_.lp, (_.frames.loop.timeline.yoyo_scale ? lspeed / 2 : lspeed), checkLoopSkew({ scaleX: lof.scaleX, scaleY: lof.scaleY, skewX: lof.skewX, skewY: lof.skewY, ease: _.frames.loop.timeline.ease }));

				//LOOP FILTER ANIMATION
				var filtanim = { opacity: lof.opacity || 1, ease: _.frames.loop.timeline.ease, '-webkit-filter': loopendfilter, 'filter': loopendfilter };
				_.loop.filter.to(_.lp, (_.frames.loop.timeline.yoyo_filter ? lspeed / 2 : lspeed), filtanim);
				_.timeline.add(_.loop.root, lssstart);
			}


			// RENDER HOVER ANIMATION
			if (_.frames.frame_hover !== undefined && (obj.mode === "start" || _.hoverframeadded === undefined)) {

				_.hoverframeadded = true;
				var hoverspeed = _.frames.frame_hover.timeline.speed / 1000;
				hoverspeed = hoverspeed === 0 ? 0.00001 : hoverspeed;

				//SET HOVER ANIMATION
				if (!_.hoverlistener) {
					_.hoverlistener = true;
					_R.document.on("mouseenter mousemove", (_.type === "column" ? "#" + _.cbg[0].id + "," : "") + "#" + _.c[0].id, function(e) {

						if (e.type === "mousemove" && _.ignoremousemove === true) return;


						// possible solution to "hover not working on initial load"
						// with a new "overrride frames" option applied
						/*
						if(!_.readyForHover && _.frame_hover.override) {

							_.timeline.progress(1);
							_.readyForHover = true;

						}
						*/
						if (_.animationonscroll || _.readyForHover) {
							_.elementHovered = true;
							// only create new hover timeline if it doesn't already exist
							if (!_.hovertimeline) _.hovertimeline = tpGS.gsap.timeline({ paused: true });

							if (_.hovertimeline.progress() == 0 && (_.lastHoveredTimeStamp === undefined || ((new Date().getTime() - _.lastHoveredTimeStamp) > 150))) { //changed limit from 1500 to 150 for a shorter delay. "Bug" after animation hover is not directly available
								_.ignoremousemove = true; // Moved ingoremouse here, since first we can ignore it when the animation really runs 1st time.
								_.hovertimeline.to([_.m, _.cbgmask], hoverspeed, { overflow: (_.frames.frame_hover.mask ? "hidden" : "visible") }, 0);
								if (_.type === "column") _.hovertimeline.to(_.cbg, hoverspeed, _R.clone(convertHoverTransform(_.frames.frame_hover, _.cbg, { bgCol: _.bgcol, bg: _.styleProps.background })), 0);
								if ((_.type === "text" || _.type === "button") && _.splitText !== undefined && _.splitText !== false) _.hovertimeline.to([_.splitText.lines, _.splitText.words, _.splitText.chars], hoverspeed, { color: _.frames.frame_hover.color, ease: _.frames.frame_hover.transform.ease }, 0);
								if (_.type === "column")
									_.hovertimeline.to(_.c, hoverspeed, reduceColumn(_R.clone(convertHoverTransform(_.frames.frame_hover, _.c, { bgCol: _.bgcol, bg: _.styleProps.background }))), 0);
								else
									_.hovertimeline.to(_.c, hoverspeed, _R.clone(convertHoverTransform(_.frames.frame_hover, _.c, { bgCol: _.bgcol, bg: _.styleProps.background })), 0);
								if (_.type === "svg") {
									_.svgHTemp = _R.clone(_.svgH);

									// hover colors can exist on the different responsive levels
									var fillColor = Array.isArray(_.svgHTemp.fill) ? _.svgHTemp.fill[_R[id].level] : _.svgHTemp.fill;
									_.svgHTemp.fill = fillColor;

									_.hovertimeline.to(_.svg, hoverspeed, _.svgHTemp, 0);
									_.hovertimeline.to(_.svgPath, hoverspeed, { fill: fillColor }, 0);
								}
							}
							_.hovertimeline.play();
						}
						_.lastHoveredTimeStamp = new Date().getTime();
					});
					_R.document.on("mouseleave", (_.type === "column" ? "#" + _.cbg[0].id + "," : "") + "#" + _.c[0].id, function() {
						_.elementHovered = false;
						if ((_.animationonscroll || _.readyForHover) && _.hovertimeline !== undefined) {
							_.hovertimeline.reverse();
							_.hovertimeline.eventCallback("onReverseComplete", _R.hoverReverseDone, [{ id: id, L: L }]);
						}
					});
				}
			}



			// Added _.currentframe to save last played Frame in Static Layer -> Slide change will nor restart animation of already triggered layers in groups when drawn on Static
			if (!renderJustFrom) _.lastRequestedMainFrame = obj.mode === "start" ? "frame_1" : obj.mode === "continue" ? obj.frame === undefined ? _.currentframe : obj.frame : _.lastRequestedMainFrame;

			if (obj.totime !== undefined) _.tSTART = obj.totime;
			else
			if (cachetime !== undefined && obj.frame === undefined) _.tSTART = cachetime;
			else
			if (obj.frame !== undefined) _.tSTART = obj.frame;
			else
				_.tSTART = 0;

			if (_.tSTART === 0 && _.startedAnimOnce === undefined && _.leftstage === undefined && _.startedAnimOnce === undefined && _.hideonfirststart === true && obj.mode === "preset") {
				//window.requestAnimationFrame(function() {
				//_R[id]._L[_R[id]._L[i]._ligid].childrenAtStartNotVisible
				_R[id]._L[L[0].id].pVisRequest = 0; //Hidden
				//_R[id]._L[L[0].id].p[0].classList.add("rs-forcehidden");
				//});
				_.hideonfirststart = false;
			}


			if ((_.tSTART === "frame_999" || _.triggeredFrame === "frame_999") && (_.leftstage || _.startedAnimOnce === undefined)) {
				// WE DONT NEED TO TOUCH THE LAYER, IT IS ANYWAY NOT VISIBLE
			} else {

				if (_.animationonscroll != "true" && _.animationonscroll != true)
					_.timeline.play(_.tSTART);
				else
					_.timeline.time(_.tSTART);

				// Move Children Timeline to the Right Position
				if (jQuery.inArray(_.type, ["group", "row", "column"]) >= 0 && obj.frame !== undefined) {
					if (_.childrenJS === undefined) {
						_.childrenJS = {};

						for (var i in _R[id]._L)
							if (_R[id]._L[i]._lig !== undefined && _R[id]._L[i]._lig[0] !== undefined && _R[id]._L[i]._lig[0].id === L[0].id && _R[id]._L[i]._lig[0].id !== _R[id]._L[i].c[0].id)
								_.childrenJS[_R[id]._L[i].c[0].id] = _R[id]._L[i].c;
					}
					obj.frame = obj.frame == "0" ? "frame_0" : obj.frame;
					obj.frame = obj.frame == "1" ? "frame_1" : obj.frame;
					obj.frame = obj.frame == "999" ? "frame_999" : obj.frame;
					var totime = obj.totime === undefined ?
						_.frames[obj.frame].timeline.startAbsolute !== undefined ?
						parseInt(_.frames[obj.frame].timeline.startAbsolute, 0) / 1000 :
						_.frames[obj.frame].timeline.start !== undefined ?
						_R.isNumeric(_.frames[obj.frame].timeline.start) ?
						parseInt(_.frames[obj.frame].timeline.start, 0) / 1000 : 0 //obj.totime  Set to "0" if Start is Unknown.
						:
						0.001 :
						obj.totime;
					if (obj.updateChildren === true) {


						for (var i in _.childrenJS)
							if (_.childrenJS.hasOwnProperty(i)) _R.renderLayerAnimation({ layer: _.childrenJS[i], fastforward: false, id: id, mode: "continue", updateChildren: true, totime: totime });
					} else {
						for (var i in _.childrenJS) {
							if (!_.childrenJS.hasOwnProperty(i)) continue;
							if (_R[id]._L[i].pausedTrueParrent) {
								_R.renderLayerAnimation({ layer: _.childrenJS[i], fastforward: false, id: id, mode: "continue", updateChildren: true, totime: totime });
								_R[id]._L[i].pausedTrueParrent = false;
							}
						}
					}
				}
			}
		}

	});


	/**********************************************************************************************
						-	TWEEN STARTS AND ENDS -
	**********************************************************************************************/

	var reduceColumn = function(_) {
			var r = _R.clone(_);
			delete r.backgroundColor;
			delete r.background;
			delete r.backgroundImage;
			delete r.borderSize;
			delete r.borderStyle;
			delete r['backdrop-filter'];
			return r;
		},

		checkMotionPath = function(_) {
			if (_ === undefined || _.path === undefined || !Array.isArray(_.path)) return;
			var x = 0,
				y = 0;
			for (var i in _.path) {
				if (!_.path.hasOwnProperty(i) || x > 0 || y > 0) continue;
				x += _.path[i].x;
				y += _.path[i].y;
			}
			return x == 0 && y == 0 ? false : true;
		},

		checkLoopSkew = function(_) {
			if (_.skewX === undefined) delete _.skewX;
			if (_.skewY === undefined) delete _.skewY;
			return _;
		},
		// reusable function that you can wrap around your stagger vars and define an "offset" value that'll get added to the 2nd half of the values
		offsetStagger = function(_) {
			_.from = _.from === "edge" ? "edges" : _.from;
			var distributor = tpGS.gsap.utils.distribute(_);
			return function(i, target, targets) {
				return distributor(i, target, targets) + (i <= targets.length / 2 ? 0 : _.offset || 0);
			}
		},

		// TWEEN EVENTS - START
		tweenOnStart = function(_) {


			// SAFARI CLIP-PATH BUG FIX
			if (_R[_.id].BUG_safari_clipPath) _.L[0].classList.remove("rs-pelock");

			//Check if Group Element Animation should be paused
			if ((_R[_.id]._L[_.L[0].id]._ingroup || _R[_.id]._L[_.L[0].id]._incolumn || _R[_.id]._L[_.L[0].id]._inrow) && _R[_.id]._L[_R[_.id]._L[_.L[0].id]._ligid] !== undefined && _R[_.id]._L[_R[_.id]._L[_.L[0].id]._ligid].timeline !== undefined) {
				if (!_R[_.id]._L[_R[_.id]._L[_.L[0].id]._ligid].timeline.isActive() && _R[_.id]._L[_.L[0].id] !== undefined && _R[_.id]._L[_.L[0].id].frames[_R[_.id]._L[_.L[0].id].timeline.currentLabel()] !== undefined)
					if (_R[_.id]._L[_R[_.id]._L[_.L[0].id]._ligid].timezone == undefined || _R[_.id]._L[_R[_.id]._L[_.L[0].id]._ligid].timezone.to <= parseInt(_R[_.id]._L[_.L[0].id].frames[_R[_.id]._L[_.L[0].id].timeline.currentLabel()].timeline.start, 0))
						if (_R[_.id]._L[_.L[0].id].animOnScrollForceDisable !== true) {
							_R[_.id]._L[_.L[0].id].pausedTrueParrent = true;
							_R[_.id]._L[_.L[0].id].timeline.pause();
						}
			}

			// handle hover madness
			var curLayer = _R[_.id]._L[_.L[0].id],
				hovertimeline = curLayer.hovertimeline;

			if (hovertimeline && hovertimeline.time() > 0) {
				hovertimeline.pause();
				hovertimeline.time(0);
				hovertimeline.kill();
				delete curLayer.hovertimeline;
			}

			delete _R[_.id]._L[_.L[0].id].childrenAtStartNotVisible;
			_R[_.id]._L[_.L[0].id].pVisRequest = 1;

			var data = { layer: _.L };
			_R[_.id]._L[_.L[0].id].ignoremousemove = false;
			_R[_.id]._L[_.L[0].id].leftstage = false;
			_R[_.id]._L[_.L[0].id].readyForHover = false;

			if (_R[_.id]._L[_.L[0].id].layerLoop !== undefined)
				if (_R[_.id]._L[_.L[0].id].layerLoop.from === _.frame) _R[_.id]._L[_.L[0].id].layerLoop.count++;

				// Safari do not render Image transform well if its opacity === 0 !?
			if (_.frame === "frame_1" && window.RSBrowser === "Safari" && _R[_.id]._L[_.L[0].id].safariRenderIssue === undefined) {
				tpGS.gsap.set([_R[_.id]._L[_.L[0].id].c], { opacity: 1 });
				_R[_.id]._L[_.L[0].id].safariRenderIssue = true;
			}

			if (_.frame !== "frame_999") {
				_R[_.id]._L[_.L[0].id].startedAnimOnce = true;
				_R[_.id]._L[_.L[0].id].pPeventsRequest = _R[_.id]._L[_.L[0].id].noPevents ? "none" : "auto";
			}

			data.eventtype = _.frame === "frame_0" || _.frame === "frame_1" ? "enterstage" : _.frame === "frame_999" ? "leavestage" : "framestarted";
		   // window.requestAnimationFrame(function() {
			   if (_R[_.id]._L[_.L[0].id]._ingroup && _R[_.id]._L[_R[_.id]._L[_.L[0].id]._lig[0].id].frames.frame_1.timeline.waitoncall !== true) _R[_.id]._L[_R[_.id]._L[_.L[0].id]._lig[0].id].pVisRequest = 1;
				_R.requestLayerUpdates(_.id, data.eventtype, _.L[0].id, _R[_.id]._L[_.L[0].id].frames[_.frame] !== undefined && _R[_.id]._L[_.L[0].id].frames[_.frame].timeline !== undefined && _R[_.id]._L[_.L[0].id].frames[_.frame].timeline.usePerspective == false ? _.tPE : "ignore");
		   // });
			data.id = _.id;
			data.layerid = _.L[0].id;
			data.layertype = _R[_.id]._L[_.L[0].id].type;
			data.frame_index = _.frame;
			data.layersettings = _R[_.id]._L[_.L[0].id];
			_R[_.id].c.trigger("revolution.layeraction", [data]);
			if (data.eventtype === "enterstage") _R.toggleState(_R[_.id]._L[_.L[0].id].layertoggledby);
			if (_.frame === "frame_1") _R.animcompleted(_.L, _.id);

		},

		tweenOnUpdate = function(_) {

			if (_.frame === "frame_999") {
				_R[_.id]._L[_.L[0].id].pVisRequest = 1;
				_R[_.id]._L[_.L[0].id].pPeventsRequest = _R[_.id]._L[_.L[0].id].noPevents ? "none" : "auto";
				_R[_.id]._L[_.L[0].id].leftstage = false;
				window.requestAnimationFrame(function() { _R.requestLayerUpdates(_.id, 'update', _.L[0].id); });
			}
		},

		tweenOnEnd = function(_) {

			var vis = true;
			//GET ZONE
			if (_R[_.id]._L[_.L[0].id].type === "column" || _R[_.id]._L[_.L[0].id].type === "row" || _R[_.id]._L[_.L[0].id].type === "group") {
				var cl = _R[_.id]._L[_.L[0].id].timeline.currentLabel(),
					nl = jQuery.inArray(cl, _R[_.id]._L[_.L[0].id].ford);
				nl++;
				nl = _R[_.id]._L[_.L[0].id].ford.length > nl ? _R[_.id]._L[_.L[0].id].ford[nl] : cl;

				if (_R[_.id]._L[_.L[0].id].frames[nl] !== undefined && _R[_.id]._L[_.L[0].id].frames[cl] !== undefined) {
					_R[_.id]._L[_.L[0].id].timezone = { from: parseInt(_R[_.id]._L[_.L[0].id].frames[cl].timeline.startAbsolute, 0), to: parseInt(_R[_.id]._L[_.L[0].id].frames[nl].timeline.startAbsolute, 0) };
				}
			}

			if (_.frame !== "frame_999" && _R[_.id].isEdge && _R[_.id]._L[_.L[0].id].type === "shape") {
				// ISSUS FOR IE EDGE WHICH  BREAKS SHAPE MASK ANIMATIONS
				var cachemw = _R[_.id]._L[_.L[0].id].c[0].style.opacity;
				_R[_.id]._L[_.L[0].id].c[0].style.opacity = cachemw - 0.0001
				tpGS.gsap.set(_R[_.id]._L[_.L[0].id].c[0], { opacity: (cachemw - 0.001), delay: 0.05 });
				tpGS.gsap.set(_R[_.id]._L[_.L[0].id].c[0], { opacity: cachemw, delay: 0.1 });
			}


			var data = {};
			data.layer = _.L;
			data.eventtype = _.frame === "frame_0" || _.frame === "frame_1" ? "enteredstage" : _.frame === "frame_999" ? "leftstage" : "frameended";
			_R[_.id]._L[_.L[0].id].readyForHover = true;
			data.layertype = _R[_.id]._L[_.L[0].id].type;
			data.frame_index = _.frame;
			data.layersettings = _R[_.id]._L[_.L[0].id];
			_R[_.id].c.trigger("revolution.layeraction", [data]);

			if (_.frame === "frame_999" && data.eventtype === "leftstage") {
				_R[_.id]._L[_.L[0].id].leftstage = true;
				_R[_.id]._L[_.L[0].id].pVisRequest = 0; //Hidden
				_R[_.id]._L[_.L[0].id].pPeventsRequest = "none"
				vis = false;
				window.requestAnimationFrame(function() { _R.requestLayerUpdates(_.id, 'leftstage', _.L[0].id); });
			} else // If Perspective should not be used, and Frame Ended, we can remove the "pseudo" perspective which helps to animate things smoother, i.e. scale Animations
			if (_.L[0].id, _R[_.id]._L[_.L[0].id].frames[_.frame] !== undefined && _R[_.id]._L[_.L[0].id].frames[_.frame].timeline !== undefined && _R[_.id]._L[_.L[0].id].frames[_.frame].timeline.usePerspective == false)
				window.requestAnimationFrame(function() { _R.requestLayerUpdates(_.id, 'frameended', _.L[0].id, _.tPE); });


			if (data.eventtype === "leftstage" && _R[_.id].videos !== undefined && _R[_.id].videos[_.L[0].id] !== undefined && _R.stopVideo) _R.stopVideo(_.L, _.id);

			if (_R[_.id]._L[_.L[0].id].type === "column") tpGS.gsap.to(_R[_.id]._L[_.L[0].id].cbg, 0.01, { visibility: "visible" });

			if (data.eventtype === "leftstage") {
				_R.unToggleState(_.layertoggledby);
				//RESET VIDEO AFTER REMOVING LAYER
				if (_R[_.id]._L[_.L[0].id].type === "video" && _R.resetVideo) setTimeout(function() {
					_R.resetVideo(_.L, _.id);
				}, 100);
			}

			if (_R[_.id].BUG_safari_clipPath && !vis) _.L[0].classList.add("rs-pelock");

			// Loop Layer if Needed
			if (_R[_.id]._L[_.L[0].id].layerLoop !== undefined && _R[_.id]._L[_.L[0].id].layerLoop.to === _.frame)
				if ((_R[_.id]._L[_.L[0].id].layerLoop.repeat == -1 || _R[_.id]._L[_.L[0].id].layerLoop.repeat > _R[_.id]._L[_.L[0].id].layerLoop.count))
					_R.renderLayerAnimation({ layer: _R[_.id]._L[_.L[0].id].c, frame: _R[_.id]._L[_.L[0].id].layerLoop.from, updateChildren: _R[_.id]._L[_.L[0].id].layerLoop.children, mode: "continue", fastforward: _R[_.id]._L[_.L[0].id].layerLoop.keep === true ? true : false, id: _.id });

		},

		/**********************************************************************************************
							-	HELPER FUNCTIONS FOR LAYER TRANSFORMS -
		**********************************************************************************************/
		////////////////////////////////
		// EXTRA INTERNAL FUNCTIONS  //
		//////////////////////////////
		buildFilter = function(f) {
			if (f === undefined) return "";
			var r = "";
			if (_R.isChrome8889 && f.blur === 0) f.blur = 0.05;
			r = f.blur !== undefined ? 'blur(' + (f.blur || 0) + 'px)' : '';
			r += f.grayscale !== undefined ? (r.length > 0 ? ' ' : '') + 'grayscale(' + (f.grayscale || 0) + '%)' : '';
			r += f.brightness !== undefined ? (r.length > 0 ? ' ' : '') + 'brightness(' + (f.brightness || 100) + '%)' : '';
			return r === "" ? "none" : r;
		},

		buildBackdropFilter = function(f) {
			if (f === undefined) return "";
			var r = "";
			if (_R.isChrome8889 && f.b_blur === 0) f.b_blur = 0.05;
			r = f.b_blur !== undefined ? 'blur(' + (f.b_blur || 0) + 'px)' : '';
			r += f.b_grayscale !== undefined ? (r.length > 0 ? ' ' : '') + 'grayscale(' + (f.b_grayscale || 0) + '%)' : '';
			r += f.b_sepia !== undefined ? (r.length > 0 ? ' ' : '') + 'sepia(' + (f.b_sepia || 0) + '%)' : '';
			r += f.b_invert !== undefined ? (r.length > 0 ? ' ' : '') + 'invert(' + (f.b_invert || 0) + '%)' : '';
			r += f.b_brightness !== undefined ? (r.length > 0 ? ' ' : '') + 'brightness(' + (f.b_brightness || 100) + '%)' : '';
			return r === "" ? "none" : r;
		},


		convertHoverTransform = function(_, el, idle) {
			var a = _R.clone(_.transform),
				nb, l;
			if (a.originX || a.originY || a.originZ) {
				a.transformOrigin = (a.originX === undefined ? "50%" : a.originX) + " " + (a.originY === undefined ? "50%" : a.originY) + " " + (a.originZ === undefined ? "50%" : a.originZ);
				delete a.originX;
				delete a.originY;
				delete a.originZ;
			}

			if (_ !== undefined && _.filter !== undefined) {
				a.filter = buildFilter(_.filter);
				a['-webkit-filter'] = a.filter;
			}

			a.color = a.color === undefined ? 'rgba(255,255,255,1)' : a.color;
			a.force3D = "auto";

			if (a.borderRadius !== undefined) {
				nb = a.borderRadius.split(" ");
				l = nb.length;
				a.borderTopLeftRadius = nb[0];
				a.borderTopRightRadius = nb[1];
				a.borderBottomRightRadius = nb[2];
				a.borderBottomLeftRadius = nb[3];
				delete a.borderRadius;
			}

			if (a.borderWidth !== undefined) {
				nb = a.borderWidth.split(" "),
					l = nb.length;
				a.borderTopWidth = nb[0];
				a.borderRightWidth = nb[1];
				a.borderBottomWidth = nb[2];
				a.borderLeftWidth = nb[3];
				delete a.borderWidth;
			}

			if (idle.bg === undefined || idle.bg.indexOf('url') === -1) {
				var elGradient = idle.bgCol.search('gradient') !== -1,
					aGradient = a.backgroundImage && typeof a.backgroundImage === 'string' && a.backgroundImage.search('gradient') !== -1;

				if (aGradient && elGradient) {
					if (gradDegree(idle.bgCol) !== 180 && gradDegree(a.backgroundImage) == 180) a.backgroundImage = addGradDegree(a.backgroundImage, 180);
					a.backgroundImage = tpGS.getSSGColors(idle.bgCol, a.backgroundImage, (a.gs === undefined ? "fading" : a.gs)).to;
				} else
				if (aGradient && !elGradient)
					a.backgroundImage = tpGS.getSSGColors(idle.bgCol, a.backgroundImage, (a.gs === undefined ? "fading" : a.gs)).to;
				else
				if (!aGradient && elGradient) {
					a.backgroundImage = tpGS.getSSGColors(idle.bgCol, a.backgroundColor, (a.gs === undefined ? "fading" : a.gs)).to;
				}
			}

			delete a.gs;
			return a;
		},

		addGradDegree = function(grad, deg) {
			grad = grad.split('(');
			var begin = grad[0];
			grad.shift();
			return begin + '(' + deg + 'deg, ' + grad.join('(');
		},
		gradDegree = function(grad) {
			if (grad.search('deg,') !== -1) {
				var deg = grad.split('deg,')[0];
				if (deg.search(/\(/) !== -1) return parseInt(deg.split('(')[1], 10);
			}
			return 180;
		},

		_svgprep = function(a, id) {
			if (a !== undefined && a.indexOf('oc:t') >= 0) return {};

			a = a === undefined ? "" : a.split(";");

			// default fill needs to be responsive
			var r = { fill: _R.revToResp('#ffffff', _R[id].rle), stroke: 'transparent', "stroke-width": "0px", "stroke-dasharray": "0", "stroke-dashoffset": "0" };

			for (var u in a) {
				if (!a.hasOwnProperty(u)) continue;
				var s = a[u].split(":");
				switch (s[0]) {
					//case "oc": if (s[1]==="t") r.original=true;break;
					case "c":
						r.fill = _R.revToResp(s[1], _R[id].rle, undefined, "||");
						break;
					case "sw":
						r["stroke-width"] = s[1];
						break;
					case "sc":
						r.stroke = s[1];
						break;
					case "so":
						r["stroke-dashoffset"] = s[1];
						break;
					case "sa":
						r["stroke-dasharray"] = s[1];
						break;
					case "sall":
						r["svgAll"] = s[1];
						break;
				}
			}
			return r;
		},

		convToCLR = function(a) {
			return a === "c" ? "center" : a === "l" ? "left" : a === "r" ? "right" : a;
		},


		/*
		UPDATE SPLITTED OR NONE SPLITTED CONTENT
		*/
		updateSplitContent = function(obj) {
			var _ = _R[obj.id]._L[obj.layer[0].id],
				split = false;
			if (_.splitText && _.splitText !== false) _.splitText.revert();
			if (_.type === "text" || _.type === "button") {
				for (var frame in _.frames) {
					if (_.frames[frame].chars !== undefined || _.frames[frame].words !== undefined || _.frames[frame].lines !== undefined) {
						split = true;
						break;
					}
				}
				if (split)
					_.splitText = new tpGS.SplitText(_.c, { type: "lines,words,chars", wordsClass: "rs_splitted_words", linesClass: "rs_splitted_lines", charsClass: "rs_splitted_chars" });
				else
					_.splitText = false;
			} else _.splitText = false;
		},


		// SFX ANIMATIONS
		checkSFXAnimations = function(effect, mask, easedata) {
			// BLOCK SFX ANIMATIONS
			if (effect !== undefined && effect.indexOf("block") >= 0) {
				var sfx = {};

				if (mask[0].getElementsByClassName('tp-blockmask_in').length === 0) {
					mask.append('<div class="tp-blockmask_in"></div>');
					mask.append('<div class="tp-blockmask_out"></div>');
				}
				easedata = easedata === undefined ? "power3.inOut" : easedata;

				sfx.ft = [{ scaleY: 1, scaleX: 0, transformOrigin: "0% 50%" }, { scaleY: 1, scaleX: 1, ease: easedata, immediateRender: false }];
				sfx.t = { scaleY: 1, scaleX: 0, transformOrigin: "100% 50%", ease: easedata, immediateRender: false };
				sfx.bmask_in = mask.find('.tp-blockmask_in');
				sfx.bmask_out = mask.find('.tp-blockmask_out');
				sfx.type = "block";

				switch (effect) {
					case "blocktoleft":
					case "blockfromright":
						sfx.ft[0].transformOrigin = "100% 50%";
						sfx.t.transformOrigin = "0% 50%";
						break;

					case "blockfromtop":
					case "blocktobottom":
						sfx.ft = [{ scaleX: 1, scaleY: 0, transformOrigin: "50% 0%" }, { scaleX: 1, scaleY: 1, ease: easedata, immediateRender: false }];
						sfx.t = { scaleX: 1, scaleY: 0, transformOrigin: "50% 100%", ease: easedata, immediateRender: false };
						break;

					case "blocktotop":
					case "blockfrombottom":
						sfx.ft = [{ scaleX: 1, scaleY: 0, transformOrigin: "50% 100%" }, { scaleX: 1, scaleY: 1, ease: easedata, immediateRender: false }];
						sfx.t = { scaleX: 1, scaleY: 0, transformOrigin: "50% 0%", ease: easedata, immediateRender: false };
						break;
				}
				sfx.ft[1].overwrite = "auto";
				sfx.t.overwrite = "auto";
				return sfx;
			} else {
				//mask.find('.tp-blockmask').remove();
				return false;
			}
		},

		checkReverse = function(a, r, t, atr, id) {
			if (_R[id].sdir === 0 || r === undefined) return a;
			if (t === "mask") atr = atr === "x" ? "mX" : atr === "y" ? "mY" : atr;
			else
			if (t === "chars") atr = atr === "x" ? "cX" : atr === "y" ? "cY" : atr === "dir" ? "cD" : atr;
			else
			if (t === "words") atr = atr === "x" ? "wX" : atr === "y" ? "wY" : atr === "dir" ? "wD" : atr;
			else
			if (t === "lines") atr = atr === "x" ? "lX" : atr === "y" ? "lY" : atr === "dir" ? "lD" : atr;
			if (r[atr] === undefined || r[atr] === false) return a;
			else
			if (r !== undefined && r[atr] === true) return a === "t" || a === "top" ? "b" : a === "b" || a === "bottom" ? "t" : a === "l" || a === "left" ? "r" : a === "r" || a === "right" ? "l" : (a * -1);
		},


		convertTransformValues = function(obj) {

			var _ = _R[obj.id]._L[obj.layer[0].id],
				a = obj.source === undefined ? _R.clone(obj.frame.transform) : _R.clone(obj.frame[obj.source]),
				b,
				torig = { originX: "50%", originY: "50%", originZ: "0" },
				parw = _._lig !== undefined ? _R[obj.id]._L[_._lig[0].id].eow : _R[obj.id].conw,
				parh = _._lig !== undefined ? _R[obj.id]._L[_._lig[0].id].eoh : _R[obj.id].conh;



			for (var atr in a) {
				if (!a.hasOwnProperty(atr)) continue;
				a[atr] = (typeof a[atr] === "object") ? a[atr][_R[obj.id].level] : a[atr];

				if (a[atr] === "inherit" || atr === "delay" || atr === "direction" || atr === "use") delete a[atr]; // NOT FOR ANIMATION
				else
				if (atr === "originX" || atr === "originY" || atr === "originZ") { // COLLECT ORIGINS
					torig[atr] = a[atr];
					delete a[atr];
				} else {

					if (_R.isNumeric(a[atr], 0)) a[atr] = checkReverse(a[atr], obj.frame.reverse, obj.target, atr, obj.id, obj.id); // NUMERIC ?
					else
					if (a[atr][0] === "r" && a[atr][1] === "a" && a[atr][3] === "(") a[atr] = a[atr].replace("ran", "random"); // RANDOM
					else
					if (a[atr].indexOf("cyc(") >= 0) { // CYCLE
						var cycar = a[atr].replace("cyc(", "").replace(")", "").replace("[", "").replace("]", "").split("|");
						a[atr] = new function(index) { return tpGS.gsap.utils.wrap(cycar, index) };

					} else // NUMERIC WITH %
					if (a[atr].indexOf("%") >= 0 && _R.isNumeric(b = parseInt(a[atr], 0))) {

						/*a[atr]= atr==="x" ? function() { return checkReverse((_.eow||0)*b/100,obj.frame.reverse,obj.target,atr,obj.id)} :
								atr==="y" ?  function() { return checkReverse((_.eoh||0)*b/100,obj.frame.reverse,obj.target,atr,obj.id)} : a[atr];*/

						a[atr] = atr === "x" ? checkReverse((_.eow || 0) * b / 100, obj.frame.reverse, obj.target, atr, obj.id) : atr === "y" ? checkReverse((_.eoh || 0) * b / 100, obj.frame.reverse, obj.target, atr, obj.id) : a[atr];

					} else {
						a[atr] = a[atr].replace("[", "").replace("]", "");
						a[atr] = checkReverse(a[atr], obj.frame.reverse, obj.target, atr, obj.id, obj.id);
						var zoneOffset = { t: 0, b: 0 };
						if (_.type === "row") {
							if (_.zone === "rev_row_zone_top" && _R[obj.id].topZones[_.slideIndex] !== undefined && _R[obj.id].topZones[_.slideIndex][0] !== undefined)
								zoneOffset = { t: 0, b: 0 };
							else if (_.zone === "rev_row_zone_middle" && _R[obj.id].middleZones[_.slideIndex] !== undefined && _R[obj.id].middleZones[_.slideIndex][0] !== undefined)
								zoneOffset = { t: Math.round(_R[obj.id].module.height / 2 - _R[obj.id].middleZones[_.slideIndex][0].offsetHeight / 2), b: Math.round(_R[obj.id].module.height / 2 + _R[obj.id].middleZones[_.slideIndex][0].offsetHeight / 2) };
							else if (_.zone === "rev_row_zone_bottom" && _R[obj.id].bottomZones[_.slideIndex] !== undefined && _R[obj.id].bottomZones[_.slideIndex][0] !== undefined)
								zoneOffset = { t: Math.round(_R[obj.id].module.height - _R[obj.id].bottomZones[_.slideIndex][0].offsetHeight), b: _R[obj.id].module.height + _R[obj.id].bottomZones[_.slideIndex][0].offsetHeight };

						}


						switch (a[atr]) {
							case "t":
							case "top":
								a[atr] = (0 - (_.eoh || 0) - (_.type === "column" ? 0 : _.calcy || 0) - (_R.getLayerParallaxOffset(obj.id, obj.layer[0].id, "v")) - (_.type === "row" && _.marginTop !== undefined ? _.marginTop[_R[obj.id].level] : 0)) - zoneOffset.b;
								break;
							case "b":
							case "bottom":
								a[atr] = (parh - (_.type === "column" || _.type === "row" ? 0 : _.calcy || 0) + (_R.getLayerParallaxOffset(obj.id, obj.layer[0].id, "v"))) - zoneOffset.t;
								break;
							case "l":
							case "left":
								a[atr] = 0 - (_.type === "row" ? _.pow : (_.eow || 0)) - (_.type === "column" ? 0 : _.type === "row" ? _.rowcalcx : (_.calcx || 0)) - (_R.getLayerParallaxOffset(obj.id, obj.layer[0].id, "h"));
								break;
							case "r":
							case "right":
								a[atr] = parw - (_.type === "column" ? 0 : _.type === "row" ? _.rowcalcx : _.calcx || 0) + _R.getLayerParallaxOffset(obj.id, obj.layer[0].id, "h");
								break;
							case "m":
							case "c":
							case "middle":
							case "center":
								a[atr] = atr === "x" ? checkReverse((parw / 2 - (_.type === "column" ? 0 : _.calcx || 0) - (_.eow || 0) / 2), obj.frame.reverse, obj.target, atr, obj.id) : atr === "y" ? checkReverse((parh / 2 - (_.type === "column" ? 0 : (_.calcy || 0)) - (_.eoh || 0) / 2), obj.frame.reverse, obj.target, atr, obj.id) : a[atr];
								break;
						}

					}
				}
				// MANAGE SKEW CALCULATION
				if (atr === "skewX" && a[atr] !== undefined) {
					a['scaleY'] = a['scaleY'] === undefined ? 1 : parseFloat(a['scaleY']);
					a['scaleY'] *= Math.cos(parseFloat(a[atr]) * tpGS.DEG2RAD);
				}
				if (atr === "skewY" && a[atr] !== undefined) {
					a['scaleX'] = a['scaleX'] === undefined ? 1 : parseFloat(a['scaleX']);
					a['scaleX'] *= Math.cos(parseFloat(a[atr]) * tpGS.DEG2RAD);
				}
			}

			a.transformOrigin = torig.originX + " " + torig.originY + " " + torig.originZ;

			// CLIPPING EFFECTS
			if (!_R[obj.id].BUG_ie_clipPath && a.clip !== undefined && _.clipPath !== undefined && _.clipPath.use) {
				var cty = _.clipPath.type == "rectangle",
					cl = parseInt(a.clip, 0),
					clb = 100 - parseInt(a.clipB, 0),
					ch = Math.round(cl / 2);
				switch (_.clipPath.origin) {
					case "invh":
						a.clipPath = "polygon(0% 0%, 0% 100%, " + cl + "% 100%, " + cl + "% 0%, 100% 0%, 100% 100%, " + clb + "% 100%, " + clb + "% 0%, 0% 0%)";
						break;
					case "invv":
						a.clipPath = "polygon(100% 0%, 0% 0%, 0% " + cl + "%, 100% " + cl + "%, 100% 100%, 0% 100%, 0% " + clb + "%, 100% " + clb + "%, 100% 0%)";
						break;
					case "cv":
						a.clipPath = cty ? "polygon(" + (50 - ch) + "% 0%, " + (50 + ch) + "% 0%, " + (50 + ch) + "% 100%, " + (50 - ch) + "% 100%)" : "circle(" + cl + "% at 50% 50%)";
						break;
					case "ch":
						a.clipPath = cty ? "polygon(0% " + (50 - ch) + "%, 0% " + (50 + ch) + "%, 100% " + (50 + ch) + "%, 100% " + (50 - ch) + "%)" : "circle(" + cl + "% at 50% 50%)";
						break;
					case "l":
						a.clipPath = cty ? "polygon(0% 0%, " + cl + "% 0%, " + cl + "% 100%, 0% 100%)" : "circle(" + cl + "% at 0% 50%)";
						break;
					case "r":
						a.clipPath = cty ? "polygon(" + (100 - cl) + "% 0%, 100% 0%, 100% 100%, " + (100 - cl) + "% 100%)" : "circle(" + cl + "% at 100% 50%)";
						break;
					case "t":
						a.clipPath = cty ? "polygon(0% 0%, 100% 0%, 100% " + cl + "%, 0% " + cl + "%)" : "circle(" + cl + "% at 50% 0%)";
						break;
					case "b":
						a.clipPath = cty ? "polygon(0% 100%, 100% 100%, 100% " + (100 - cl) + "%, 0% " + (100 - cl) + "%)" : "circle(" + cl + "% at 50% 100%)";
						break;
					case "lt":
						a.clipPath = cty ? "polygon(0% 0%," + (2 * cl) + "% 0%, 0% " + (2 * cl) + "%)" : "circle(" + cl + "% at 0% 0%)";
						break;
					case "lb":
						a.clipPath = cty ? "polygon(0% " + (100 - 2 * cl) + "%, 0% 100%," + (2 * cl) + "% 100%)" : "circle(" + cl + "% at 0% 100%)";
						break;
					case "rt":
						a.clipPath = cty ? "polygon(" + (100 - 2 * cl) + "% 0%, 100% 0%, 100% " + (2 * cl) + "%)" : "circle(" + cl + "% at 100% 0%)";
						break;
					case "rb":
						a.clipPath = cty ? "polygon(" + (100 - 2 * cl) + "% 100%, 100% 100%, 100% " + (100 - 2 * cl) + "%)" : "circle(" + cl + "% at 100% 100%)";
						break;
					case "clr":
						a.clipPath = cty ? "polygon(0% 0%, 0% " + cl + "%, " + (100 - cl) + "% 100%, 100% 100%, 100% " + (100 - cl) + "%, " + cl + "% 0%)" : "circle(" + cl + "% at 50% 50%)";
						break;
					case "crl":
						a.clipPath = cty ? "polygon(0% " + (100 - cl) + "%, 0% 100%, " + cl + "% 100%, 100% " + cl + "%, 100% 0%, " + (100 - cl) + "% 0%)" : "circle(" + cl + "% at 50% 50%)";
						break;
				}
				if (_R.isFirefox(obj.id) !== true) a["-webkit-clip-path"] = a.clipPath;
				a["clip-path"] = a.clipPath;
				//a.overflow = "hidden" // MAYBE ADD TO AVOID SKEW ISSUES IN LOOPED AND MASKED LAYERS
				delete a.clip;
				delete a.clipB;
			} else {
				delete a.clip;
			}


			if (obj.target !== "mask") {
				// FILTER EFFECTS
				if (obj.frame !== undefined && (obj.frame.filter !== undefined || obj.forcefilter)) {
					a.filter = buildFilter(obj.frame.filter);
					a['-webkit-filter'] = a.filter;
					a['backdrop-filter'] = buildBackdropFilter(obj.frame.filter);
					if (window.isSafari11) a['-webkit-backdrop-filter'] = a['backdrop-filter'];

					//SAFARI BLUR FILTER FIX - Was rotationX but it negative influence Black Friday Letter Zooms
					// z - had issues with iPad
					// x - has issue on blurred element and mask
					if (window.isSafari11 && a.filter !== undefined && a.x === undefined && obj.frame.filter !== undefined && obj.frame.filter.blur !== undefined) a.x = 0.0001;
				}
				if (jQuery.inArray(obj.source, ["chars", "words", "lines"]) >= 0 && (obj.frame[obj.source].blur !== undefined || obj.forcefilter)) {
					a.filter = buildFilter(obj.frame[obj.source]);
					a['-webkit-filter'] = a.filter;
				}
				delete a.grayscale;
				delete a.blur;
				delete a.brightness;
			}

			// EASE
			a.ease = a.ease !== undefined ? a.ease : (a.ease === undefined && obj.ease !== undefined) || (a.ease !== undefined && obj.ease !== undefined && a.ease === "inherit") ? obj.ease : obj.frame.timeline.ease;
			a.ease = a.ease === undefined || a.ease === "default" ? "power3.inOut" : a.ease;



			return a;
		},


		getCycles = function(anim) {
			var _;
			for (var a in anim) {
				if (typeof anim[a] === "string" && anim[a].indexOf("|") >= 0) {
					_ = anim[a].replace("[", "").replace("]", "").split("|");
					anim[a] = function(index) { return tpGS.gsap.utils.wrap(_, index) };
				}
			}
			return anim;
		},

		shuffleArray = function(array) {
			var currentIndex = array.length,
				temporaryValue, randomIndex;

			// While there remain elements to shuffle...
			while (0 !== currentIndex) {

				// Pick a remaining element...
				randomIndex = Math.floor(Math.random() * currentIndex);
				currentIndex -= 1;

				// And swap it with the current element.
				temporaryValue = array[currentIndex];
				array[currentIndex] = array[randomIndex];
				array[randomIndex] = temporaryValue;
			}
			return array;
		},


		// GET ANIMATION PARAMETERS 1 TIME
		gFrPar = function(_, id, wtl, transform, caller) {

			var color,
				bgcolor,
				n = {},
				f = {},
				t = {};
			transform = transform === undefined ? "transform" : transform;

			if (caller === "loop") {
				t.autoRotate = false;
				t.yoyo_filter = false;
				t.yoyo_rotate = false;
				t.yoyo_move = false;
				t.yoyo_scale = false;
				t.curved = false;
				t.curviness = 2;
				t.ease = "none";
				t.speed = 1000;
				t.st = 0;
				n.x = 0;
				n.y = 0;
				n.z = 0;
				n.xr = 0;
				n.yr = 0;
				n.zr = 0;
				n.scaleX = 1;
				n.scaleY = 1;
				n.originX = "50%";
				n.originY = "50%";
				n.originZ = "0";
				n.rotationX = "0deg";
				n.rotationY = "0deg";
				n.rotationZ = "0deg";
			} else {
				t.speed = 300;
				if (wtl)
					t.ease = "default";
				else
					n.ease = "default";
			}
			if (caller === "sfx") n.fxc = "#ffffff";
			_ = _.split(";");
			for (var i in _) {
				if (!_.hasOwnProperty(i)) continue;
				var v = _[i].split(":");

				switch (v[0]) {
					case "u":
						n.use = v[1] === "true" || v[1] === "t" ? true : fasle;
						break;
						// BASIC VALUES
					case "c":
						color = v[1];
						break;
					case "fxc":
						n.fxc = v[1];
						break;
					case "bgc":
						bgcolor = v[1];
						break;
					case "auto":
						n.auto = v[1] === "t" || v[1] === undefined || v[1] === "true" ? true : false;
						break;

						// FRAME VALUES
					case "o":
						n.opacity = v[1];
						break;
					case "oX":
						n.originX = v[1];
						break;
					case "oY":
						n.originY = v[1];
						break;
					case "oZ":
						n.originZ = v[1];
						break;
					case "sX":
						n.scaleX = v[1];
						break;
					case "sY":
						n.scaleY = v[1];
						break;
					case "skX":
						n.skewX = v[1];
						break;
					case "skY":
						n.skewY = v[1];
						break;
					case "rX":
						n.rotationX = v[1];
						if (v[1] != 0 && v[1] !== "0deg") _R.addSafariFix(id);
						break;
					case "rY":
						n.rotationY = v[1];
						if (v[1] != 0 && v[1] !== "0deg") _R.addSafariFix(id);
						break;
					case "rZ":
						n.rotationZ = v[1];
						break;
					case "sc":
						n.color = v[1];
						break;
					case "se":
						n.effect = v[1];
						break;
					case "bos":
						n.borderStyle = v[1];
						break;
					case "boc":
						n.borderColor = v[1];
						break;
					case "td":
						n.textDecoration = v[1];
						break;
					case "zI":
						n.zIndex = v[1];
						break;
					case "tp":
						n.transformPerspective = _R[id].perspectiveType === "isometric" ? 0 : _R[id].perspectiveType === "global" ? _R[id].perspective : v[1];
						break;
					case "cp":
						n.clip = parseInt(v[1], 0);
						break;
					case "cpb":
						n.clipB = parseInt(v[1], 0);
						break;
						//case "fpr": n.fpr = v[1]==="t" || v[1]==="true" || v[1]===true ? true : false; break;


						// TIMELINE LOOP VALUES
					case "aR":
						t.autoRotate = (v[1] == "t" ? true : false);
						break;
					case "rA":
						t.radiusAngle = v[1];
						break;
					case "yyf":
						t.yoyo_filter = (v[1] == "t" ? true : false);
						break;
					case "yym":
						t.yoyo_move = (v[1] == "t" ? true : false);
						break;
					case "yyr":
						t.yoyo_rotate = (v[1] == "t" ? true : false);
						break;
					case "yys":
						t.yoyo_scale = (v[1] == "t" ? true : false);
						break;
					case "crd":
						t.curved = (v[1] == "t" ? true : false);
						break;

						//RESPONSIVE VALUES
					case "x":
						n.x = caller === "reverse" ? v[1] === "t" || v[1] === true || v[1] == 'true' ? true : false : caller === "loop" ? parseInt(v[1], 0) : _R.revToResp(v[1], _R[id].rle);
						break;
					case "y":
						n.y = caller === "reverse" ? v[1] === "t" || v[1] === true || v[1] == 'true' ? true : false : caller === "loop" ? parseInt(v[1], 0) : _R.revToResp(v[1], _R[id].rle);
						break;
					case "z":
						n.z = caller === "loop" ? parseInt(v[1], 0) : _R.revToResp(v[1], _R[id].rle);
						if (v[1] != 0) _R.addSafariFix(id);
						break;
					case "bow":
						n.borderWidth = _R.revToResp(v[1], 4, 0).toString().replace(/,/g, " ");
						break;
					case "bor":
						n.borderRadius = _R.revToResp(v[1], 4, 0).toString().replace(/,/g, " ");
						break;

						//USE HOVER MASK
					case "m":
						n.mask = v[1] === "t" ? true : v[1] === "f" ? false : v[1];
						break;
					case "iC":
						n.instantClick = v[1] === "t" ? true : v[1] === "f" ? false : v[1];
						break;

						//CONVERTED VALUES
					case "xR":
						n.xr = parseInt(v[1], 0);
						_R.addSafariFix(id);
						break;
					case "yR":
						n.yr = parseInt(v[1], 0);
						_R.addSafariFix(id);
						break;
					case "zR":
						n.zr = parseInt(v[1], 0);
						break;
					case "blu":
						if (caller === "loop") n.blur = parseInt(v[1], 0);
						else f.blur = parseInt(v[1], 0);
						break;
					case "gra":
						if (caller === "loop") n.grayscale = parseInt(v[1], 0);
						else f.grayscale = parseInt(v[1], 0);
						break;
					case "bri":
						if (caller === "loop") n.brightness = parseInt(v[1], 0);
						else f.brightness = parseInt(v[1], 0);
						break;

						// BACKDROP FILTERS
					case "bB":
						f.b_blur = parseInt(v[1], 0);
						break;
					case "bG":
						f.b_grayscale = parseInt(v[1], 0);
						break;
					case "bR":
						f.b_brightness = parseInt(v[1], 0);
						break;
					case "bI":
						f.b_invert = parseInt(v[1], 0);
						break;
					case "bS":
						f.b_sepia = parseInt(v[1], 0);
						break;

					case "sp":
						t.speed = parseInt(v[1], 0);
						break;
					case "d":
						n.delay = parseInt(v[1], 0);
						break;
					case "crns":
						t.curviness = parseInt(v[1], 0);
						break;

						//SPECIAL HANDLINGS
					case "st":
						t.start = (v[1] === "w" || v[1] === "a" ? "+=0" : v[1]);
						t.waitoncall = (v[1] === "w" || v[1] === "a");
						break;
					case "sA":
						t.startAbsolute = v[1];
						break;
					case "sR":
						t.startRelative = v[1];
						break;

					case "e":
						if (wtl) t.ease = v[1];
						else n.ease = v[1];
						break;

						//DEFAULT
					default:
						if (v[0].length > 0) n[v[0]] = v[1] === "t" ? true : v[1] === "f" ? false : v[1];
						break;
				}
			}

			var r = { timeline: t };
			if (!jQuery.isEmptyObject(f))
				if (caller === "split") n = jQuery.extend(true, n, f);
				else r.filter = f;

			if (caller === "split") n.dir = n.dir === undefined ? "start" : n.dir === "backward" ? "end" : n.dir === "middletoedge" ? "center" : n.dir === "edgetomiddle" ? "edge" : n.dir;

			if (!jQuery.isEmptyObject(color)) r.color = color;


			if (!jQuery.isEmptyObject(bgcolor)) r.bgcolor = bgcolor;
			r[transform] = n;

			return r;
		},

		/*
		BUILD THE FRAME OBJECT STRUCTURE
		*/
		buildFrameObj = function(_, id) {
			var n = {},
				i = 0;
			if (_R[id]._rdF0 === undefined) {
				var b = gFrPar("x:0;y:0;z:0;rX:0;rY:0;rZ:0;o:0;skX:0;skY:0;sX:0;sY:0;oX:50%;oY:50%;oZ:0;dir:forward;d:5", id).transform;
				_R[id]._rdF0 = _R[id]._rdF1 = {
					transform: gFrPar("x:0;y:0;z:0;rX:0;rY:0;rZ:0;o:0;skX:0;skY:0;sX:0;sY:0;oX:50%;oY:50%;oZ:0;tp:600px", id, true).transform,
					mask: gFrPar("x:0;y:0", id, true).transform,
					chars: jQuery.extend(true, { blur: 0, grayscale: 0, brightness: 100 }, b),
					words: jQuery.extend(true, { blur: 0, grayscale: 0, brightness: 100 }, b),
					lines: jQuery.extend(true, { blur: 0, grayscale: 0, brightness: 100 }, b)
				};
				_R[id]._rdF1.transform.opacity = _R[id]._rdF1.chars.opacity = _R[id]._rdF1.words.opacity = _R[id]._rdF1.lines.opacity = _R[id]._rdF1.transform.scaleX = _R[id]._rdF1.chars.scaleX = _R[id]._rdF1.words.scaleX = _R[id]._rdF1.lines.scaleX = _R[id]._rdF1.transform.scaleY = _R[id]._rdF1.chars.scaleY = _R[id]._rdF1.words.scaleY = _R[id]._rdF1.lines.scaleY = 1;
			}


			if (_.frame_0 === undefined) _.frame_0 = "x:0";
			if (_.frame_1 === undefined) _.frame_1 = "x:0";
			_.dddNeeded = false;

			// GET ANIMATION FRAME DATAS
			for (var i in _.ford) {
				if (!_.ford.hasOwnProperty(i)) continue;
				var q = _.ford[i];

				if (_[q]) {
					n[q] = gFrPar(_[q], id, true);
					if (n[q].bgcolor !== undefined) _.bgcolinuse = true;
					//IE FIX FOR CLIP PATH
					if (_R[id].BUG_ie_clipPath && _.clipPath !== undefined && _.clipPath.use && n[q].transform.clip !== undefined) {
						var cl = _.clipPath.type === "rectangle" ? 100 - parseInt(n[q].transform.clip) : 100 - Math.min(100, (2 * parseInt(n[q].transform.clip)));
						switch (_.clipPath.origin) {
							case "clr":
							case "rb":
							case "rt":
							case "r":
								_[q + "_mask"] = "u:t;x:" + cl + "%;y:0px;";
								n[q].transform.x = _R.revToResp("-" + cl + "%", _R[id].rle);
								break;
							case "crl":
							case "lb":
							case "lt":
							case "cv":
							case "l":
								_[q + "_mask"] = "u:t;x:-" + cl + "%;y:0px;";
								n[q].transform.x = _R.revToResp("" + cl + "%", _R[id].rle);
								break;
							case "ch":
							case "t":
								_[q + "_mask"] = "u:t;y:-" + cl + "%;y:0px;";
								n[q].transform.y = _R.revToResp("" + cl + "%", _R[id].rle);
								break;
							case "b":
								_[q + "_mask"] = "u:t;y:" + cl + "%;y:0px;";
								n[q].transform.y = _R.revToResp("-" + cl + "%", _R[id].rle);
								break;
						}
						delete n[q].transform.clip;
						delete n[q].transform.clipB;
					}

					if (_[q + "_mask"]) n[q].mask = gFrPar(_[q + "_mask"], id).transform;
					if (n[q].mask != undefined && n[q].mask.use) {
						n[q].mask.x = n[q].mask.x === undefined ? 0 : n[q].mask.x;
						n[q].mask.y = n[q].mask.y === undefined ? 0 : n[q].mask.y;
						delete n[q].mask.use;
						n[q].mask.overflow = "hidden";
					} else {
						n[q].mask = { ease: "default", overflow: "visible" };
					}

					if (_[q + "_chars"]) n[q].chars = gFrPar(_[q + "_chars"], id, undefined, undefined, "split").transform;
					if (_[q + "_words"]) n[q].words = gFrPar(_[q + "_words"], id, undefined, undefined, "split").transform;
					if (_[q + "_lines"]) n[q].lines = gFrPar(_[q + "_lines"], id, undefined, undefined, "split").transform;



					if (_[q + "_chars"] || _[q + "_words"] || _[q + "_lines"]) n[q].dosplit = true;
					n.frame_0 = n.frame_0 === undefined ? { transform: {} } : n.frame_0;
					if (n[q].transform.auto) {

						n[q].transform = _R.clone(n.frame_0.transform);
						n[q].transform.opacity = n[q].transform.opacity === undefined ? 0 : n[q].transform.opacity;
						if (n.frame_0.filter !== undefined) n[q].filter = _R.clone(n.frame_0.filter);
						if (n.frame_0.mask !== undefined) n[q].mask = _R.clone(n.frame_0.mask);
						if (n.frame_0.chars !== undefined) n[q].chars = _R.clone(n.frame_0.chars);
						if (n.frame_0.words !== undefined) n[q].words = _R.clone(n.frame_0.words);
						if (n.frame_0.lines !== undefined) n[q].lines = _R.clone(n.frame_0.lines);
						if (n.frame_0.chars !== undefined || n.frame_0.words !== undefined || n.frame_0.lines !== undefined) n[q].dosplit = true;
					}

					if (_[q + "_sfx"]) n[q].sfx = gFrPar(_[q + "_sfx"], id, false, undefined, "sfx").transform;
					if (_[q + "_reverse"]) n[q].reverse = gFrPar(_[q + "_reverse"], id, false, undefined, "reverse").transform;

				}
			}
			if (n.frame_0.dosplit) n.frame_1.dosplit = true;


			// GET HOVER DATAS
			if (_.frame_hover !== undefined || _.svgh !== undefined) {
				n.frame_hover = gFrPar((_.frame_hover === undefined ? "" : _.frame_hover), id);

				if (_R.ISM && (n.frame_hover.transform.instantClick == 'true' || n.frame_hover.transform.instantClick == true)) delete n.frame_hover;
				else {
					delete n.frame_hover.transform.instantClick;
					n.frame_hover.transform.color = n.frame_hover.color;
					if (n.frame_hover.transform.color === undefined) delete n.frame_hover.transform.color;

					if (n.frame_hover.bgcolor !== undefined && n.frame_hover.bgcolor.indexOf("gradient") >= 0) n.frame_hover.transform.backgroundImage = n.frame_hover.bgcolor;
					else
					if (n.frame_hover.bgcolor !== undefined) n.frame_hover.transform.backgroundColor = n.frame_hover.bgcolor;

					if (n.frame_hover.bgcolor !== undefined) _.bgcolinuse = true;

					n.frame_hover.transform.opacity = n.frame_hover.transform.opacity === undefined ? 1 : n.frame_hover.transform.opacity;
					n.frame_hover.mask = n.frame_hover.transform.mask === undefined ? false : n.frame_hover.transform.mask;
					delete n.frame_hover.transform.mask;
					//CHECK FOR DEFAULT BORDER STYLING:
					if (n.frame_hover.transform !== undefined) {
						if (n.frame_hover.transform.borderWidth || n.frame_hover.transform.borderStyle) n.frame_hover.transform.borderColor = n.frame_hover.transform.borderColor === undefined ? "transparent" : n.frame_hover.transform.borderColor;
						if (n.frame_hover.transform.borderStyle !== "none" && n.frame_hover.transform.borderWidth === undefined) n.frame_hover.transform.borderWidth = _R.revToResp(0, 4, 0).toString().replace(/,/g, " ");
						if (_.bordercolor === undefined && n.frame_hover.transform.borderColor !== undefined) _.bordercolor = "transparent";
						if (_.borderwidth === undefined && n.frame_hover.transform.borderWidth !== undefined) _.borderwidth = _R.revToResp(n.frame_hover.transform.borderWidth, 4, 0);
						if (_.borderstyle === undefined && n.frame_hover.transform.borderStyle !== undefined) _.borderstyle = _R.revToResp(n.frame_hover.transform.borderStyle, 4, 0);
					}
				}
			}



			// Single Loop of Layer
			if (_.tloop !== undefined) {
				_.layerLoop = { from: "frame_1", to: "frame_999", repeat: -1, keep: true, children: true };
				var tlo = _.tloop.split(";");
				for (var i in tlo) {
					if (!tlo.hasOwnProperty(i)) continue;
					var v = tlo[i].split(":");
					switch (v[0]) {
						case "f":
							_.layerLoop.from = v[1];
							break;
						case "t":
							_.layerLoop.to = v[1];
							break;
						case "k":
							_.layerLoop.keep = v[1];
							break;
						case "r":
							_.layerLoop.repeat = parseInt(v[1], 0);
							break;
						case "c":
							_.layerLoop.children = v[1];
							break;
					}
				}
				_.layerLoop.count = 0;
			}

			// GET LOOP DATAS
			if (_.loop_0 || _.loop_999) {
				n.loop = gFrPar(_.loop_999, id, true, "frame_999", "loop");
				n.loop.frame_0 = gFrPar(_.loop_0 || "", id, false, undefined, "loop").transform;

			}

			//OPACITY VALUES FOR START
			n.frame_0.transform.opacity = n.frame_0.transform.opacity === undefined ? 0 : n.frame_0.transform.opacity;
			n.frame_1.transform.opacity = n.frame_1.transform.opacity === undefined ? 1 : n.frame_1.transform.opacity;
			n.frame_999.transform.opacity = n.frame_999.transform.opacity === undefined ? "inherit" : n.frame_999.transform.opacity;

			if (_.clipPath && _.clipPath.use) {
				n.frame_0.transform.clip = n.frame_0.transform.clip === undefined ? 100 : parseInt(n.frame_0.transform.clip);
				n.frame_1.transform.clip = n.frame_1.transform.clip === undefined ? 100 : parseInt(n.frame_1.transform.clip);
			}

			// Reset Filters at Start if Needed !
			_.resetfilter = false;
			_.useFilter = { blur: false, grayscale: false, brightness: false, b_blur: false, b_grayscale: false, b_brightness: false, b_invert: false, b_sepia: false };

			for (var i in n)
				if (n[i].filter !== undefined) {
					_.resetfilter = true;
					_.useFilter = useFilter(_.useFilter, n[i].filter);
				}
			if (_.resetFilter !== true && n.frame_hover !== undefined) _.useFilter = useFilter(_.useFilter, n.frame_hover);

			if (_.resetfilter) {
				n.frame_0.filter = _R.clone(n.frame_0.filter);
				n.frame_0.filter = resetSingleFilter(_.useFilter, _R.clone(n.frame_0.filter));
				for (var i in n)
					if (n[i].filter !== undefined && i !== "frame_1" && i !== "frame_0") {
						n[i].filter = _R.clone(n[i].filter);
						n[i].filter = resetSingleFilter(_.useFilter, _R.clone(n[i].filter));
					}
			}

			if (n.frame_0.filter !== undefined) {
				n.frame_1.filter = _R.clone(n.frame_1.filter);
				if (n.frame_0.filter.blur !== undefined && n.frame_1.filter.blur !== 0) n.frame_1.filter.blur = n.frame_1.filter.blur === undefined ? 0 : n.frame_1.filter.blur;
				if (n.frame_0.filter.brightness !== undefined && n.frame_1.filter.brightness !== 100) n.frame_1.filter.brightness = n.frame_1.filter.brightness === undefined ? 100 : n.frame_1.filter.brightness;
				if (n.frame_0.filter.grayscale !== undefined && n.frame_1.filter.grayscale !== 0) n.frame_1.filter.grayscale = n.frame_1.filter.grayscale === undefined ? 0 : n.frame_1.filter.grayscale;

				if (n.frame_0.filter.b_blur !== undefined && n.frame_1.filter.b_blur !== 0) n.frame_1.filter.b_blur = n.frame_1.filter.b_blur === undefined ? 0 : n.frame_1.filter.b_blur;
				if (n.frame_0.filter.b_brightness !== undefined && n.frame_1.filter.b_brightness !== 100) n.frame_1.filter.b_brightness = n.frame_1.filter.b_brightness === undefined ? 100 : n.frame_1.filter.b_brightness;
				if (n.frame_0.filter.b_grayscale !== undefined && n.frame_1.filter.b_grayscale !== 0) n.frame_1.filter.b_grayscale = n.frame_1.filter.b_grayscale === undefined ? 0 : n.frame_1.filter.b_grayscale;
				if (n.frame_0.filter.b_invert !== undefined && n.frame_1.filter.b_invert !== 0) n.frame_1.filter.b_invert = n.frame_1.filter.b_invert === undefined ? 0 : n.frame_1.filter.b_invert;
				if (n.frame_0.filter.b_sepia !== undefined && n.frame_1.filter.b_sepia !== 0) n.frame_1.filter.b_sepia = n.frame_1.filter.b_sepia === undefined ? 0 : n.frame_1.filter.b_sepia;

			}

			//Sync the Frames
			return syncFrames(n, id, _);
		},

		resetSingleFilter = function(a, f) {
			//NORMAL FILTERS
			if (a.blur) f.blur = f.blur === undefined ? 0 : f.blur;
			else delete f.blur;
			if (a.brightness) f.brightness = f.brightness === undefined ? 100 : f.brightness;
			else delete f.brightness;
			if (a.grayscale) f.grayscale = f.grayscale === undefined ? 0 : f.grayscale;
			else delete f.grayscale;

			//BACKDROP FILTERS
			if (a.b_blur) f.b_blur = f.b_blur === undefined ? 0 : f.b_blur;
			else delete f.b_blur;
			if (a.b_brightness) f.b_brightness = f.b_brightness === undefined ? 100 : f.b_brightness;
			else delete f.b_brightness;
			if (a.b_grayscale) f.b_grayscale = f.b_grayscale === undefined ? 0 : f.b_grayscale;
			else delete f.b_grayscale;
			if (a.b_invert) f.b_invert = f.b_invert === undefined ? 0 : f.b_invert;
			else delete f.b_invert;
			if (a.b_sepia) f.b_sepia = f.b_sepia === undefined ? 0 : f.b_sepia;
			else delete f.b_sepia;

			return f;
		},
		useFilter = function(a, f) {
			//NORMAL FILTERS
			a.blur = a.blur === true || (f.blur !== undefined && f.blur !== 0 && f.blur !== "0px") ? true : false;
			a.grayscale = a.grayscale === true || (f.grayscale !== undefined && f.grayscale !== 0 && f.grayscale !== "0%") ? true : false;
			a.brightness = a.brightness === true || (f.brightness !== undefined && f.brightness !== 100 && f.brightness !== "100%") ? true : false;

			//BACKDROP FILTERS
			a.b_blur = a.b_blur === true || (f.b_blur !== undefined && f.b_blur !== 0 && f.b_blur !== "0px") ? true : false;
			a.b_grayscale = a.b_grayscale === true || (f.b_grayscale !== undefined && f.b_grayscale !== 0 && f.b_grayscale !== "0%") ? true : false;
			a.b_brightness = a.b_brightness === true || (f.b_brightness !== undefined && f.b_brightness !== 100 && f.b_brightness !== "100%") ? true : false;
			a.b_invert = a.b_invert === true || (f.b_invert !== undefined && f.b_invert !== 0 && f.b_invert !== "0%") ? true : false;
			a.b_sepia = a.b_sepia === true || (f.b_sepia !== undefined && f.b_sepia !== 0 && f.b_sepia !== "0%") ? true : false;

			return a;
		},

		needPerspective = function(a) {
			return (a !== undefined && (a.rotationY !== undefined || a.rotationX !== undefined || a.z !== undefined));
		},

		// Sync The Frames, and use the same Attributes on each Frame
		// Sync The Frames, and use the same Attributes on each Frame
		syncFrames = function(_, id, LObj) {
			var e = {},
				c = ["transform", "words", "chars", "lines", "mask"],
				t,
				tPE = _R[id].perspectiveType == "global" ? _R[id].perspective : 0,
				ignorePerspective = true,
				setPreserve3D = false; // set preserve 3D on layer to fix chars/words/lines bug in iOS

			//Collect All Information
			for (var f in _) { if (f !== 'loop' && f !== 'frame_hover') e = jQuery.extend(true, e, _[f]); }

			//All Frame should have the Same Attributes set to a Definitive Value
			for (var f in _) {
				if (!_.hasOwnProperty(f)) continue;
				if (_[f].timeline !== undefined) _[f].timeline.usePerspective = false;
				if (f !== 'loop' && f !== 'frame_hover') {
					for (t in e.transform) {
						if (!e.transform.hasOwnProperty(t)) continue;
						e.transform[t] = _[f].transform[t] === undefined ? f === "frame_0" ? _R[id]._rdF0.transform[t] : f === "frame_1" ? _R[id]._rdF1.transform[t] : e.transform[t] : _[f].transform[t];
						_[f].transform[t] = _[f].transform[t] === undefined ? e.transform[t] : _[f].transform[t];
					}
					for (var ci = 1; ci <= 4; ci++)
						for (t in e[c[ci]]) {
							if (!e[c[ci]].hasOwnProperty(t)) continue;
							_[f][c[ci]] = _[f][c[ci]] === undefined ? {} : _[f][c[ci]];
							e[c[ci]][t] = _[f][c[ci]][t] === undefined ? f === "frame_0" ? _R[id]._rdF0[c[ci]][t] : f === "frame_1" ? _R[id]._rdF1[c[ci]][t] : e[c[ci]][t] : _[f][c[ci]][t];
							_[f][c[ci]][t] = _[f][c[ci]][t] === undefined ? e[c[ci]][t] : _[f][c[ci]][t];
						}
					if (_[f].timeline !== undefined && _[f].timeline.usePerspective === false && _[f].transform !== undefined && (_[f].transform.rotationY !== undefined || _[f].transform.rotationX !== undefined || _[f].transform.z !== undefined || needPerspective(_[f].chars) || needPerspective(_[f].words) || needPerspective(_[f].lines))) {
						tPE = _R[id].perspectiveType == "local" ? _[f].transform.transformPerspective === undefined ? 600 : _[f].transform.transformPerspective : tPE;
						_[f].timeline.usePerspective = true;
						if ((needPerspective(_[f].chars) || needPerspective(_[f].words) || needPerspective(_[f].lines)) && !_R.isFirefox(id)) setPreserve3D = true;
						ignorePerspective = false;
					}
				}
			}

			if (setPreserve3D) requestAnimationFrame(function() { tpGS.gsap.set(LObj.c, { transformStyle: 'preserve-3d' }); });

			// SET START PERSPECTIVE
			if (_.frame_0.timeline !== undefined && _.frame_0.timeline.usePerspective) _.frame_0.transform.transformPerspective = _R[id].perspectiveType === "local" ? _.frame_0.transform.transformPerspective === undefined ? tPE : _.frame_0.transform.transformPerspective : _R[id].perspectiveType === "isometric" ? 0 : _R[id].perspective;


			//REMOVE UNNEEDED PERSPECTITVES IF LAYER NOT NEED IT AT ALL
			if (ignorePerspective)
				for (var f in _)
					if (!_.hasOwnProperty(f) || _[f].transform === undefined) continue;
					else delete _[f].transform.transformPerspective;

			return _;
		},
		getLayersInSlide = function(slide, cname, nocname) {
			if (slide.length === 0) return {};
			var ar = slide[0].getElementsByClassName(cname),
				ret = {}; //(ar[i].dataset.type==="row" ? "0" : ar[i].dataset.type==="column" ? "1" : "2")+"_"+
			for (var i = 0; i < ar.length; i++)
				if (nocname === undefined || ar[i].className.indexOf(nocname) === -1) ret[ar[i].id] = ar[i];
			if (slide[1] !== undefined) {
				ar = slide[1].getElementsByClassName(cname);
				for (i = 0; i < ar.length; i++)
					if (nocname === undefined || ar[i].className.indexOf(nocname) === -1) ret[ar[i].id] = ar[i];
			}
			return ret;
		},

		FWS = function(a) {
			a = _R.isNumeric(a) ? a : a.toLowerCase();
			return (a === "thin" ? '00' : a === "extra light" ? 200 : a === "light" ? 300 : a === "normal" ? 400 : a === "medium" ? 500 : a === "semi bold" ? 600 : a === "bold" ? 700 : a === "extra bold" ? 800 : a === "ultra bold" ? 900 : a === "black" ? 900 : a);
		},

		/*
		COLLECT CSS VALUES FROM ELEMENT
		*/
		getStyleAtStart = function(L, level, id) {

			var i;
			if (L[0].nodeName == "BR" || L[0].tagName == "br" || (typeof L[0].className !== "object" && L[0].className.indexOf("rs_splitted_") >= 0)) return false;
			_R.sA(L[0], "stylerecorder", true);

			if (L[0].id === undefined) L[0].id = "rs-layer-sub-" + Math.round(Math.random() * 1000000);

			_R[id].computedStyle[L[0].id] = window.getComputedStyle(L[0], null);

			var d = L[0].id !== undefined && _R[id]._L[L[0].id] !== undefined ? _R[id]._L[L[0].id] : L.data(),
				pc = level === "rekursive" ? jQuery(_R.closestClass(L[0], 'rs-layer')) : undefined;

			if (pc !== undefined) _R[id].computedStyle[pc[0].id] = _R[id].computedStyle[pc[0].id] === undefined ? window.getComputedStyle(pc[0], null) : _R[id].computedStyle[pc[0].id];

			var gp = pc !== undefined && (_R[id].computedStyle[L[0].id].fontSize == _R[id].computedStyle[pc[0].id].fontSize && FWS(_R[id].computedStyle[L[0].id].fontWeight) == FWS(_R[id].computedStyle[pc[0].id].fontWeight) && _R[id].computedStyle[L[0].id].lineHeight == _R[id].computedStyle[pc[0].id].lineHeight) ? true : false,
				dpc = gp ? pc[0].id !== undefined && _R[id]._L[pc[0].id] !== undefined ? _R[id]._L[pc[0].id] : pc.data() : undefined,
				lhdef = 0;

			d.basealign = d.basealign === undefined ? "grid" : d.basealign;

			if (!d._isnotext) {

				d.fontSize = _R.revToResp(gp ? dpc.fontsize === undefined ? parseInt(_R[id].computedStyle[pc[0].id].fontSize, 0) || 20 : dpc.fontsize : d.fontsize === undefined ? (level !== "rekursive" ? 20 : "inherit") : d.fontsize, _R[id].rle);
				d.fontWeight = _R.revToResp(gp ? dpc.fontweight === undefined ? _R[id].computedStyle[pc[0].id].fontWeight || "inherit" : dpc.fontweight : d.fontweight === undefined ? _R[id].computedStyle[L[0].id].fontWeight || "inherit" : d.fontweight, _R[id].rle);
				d.whiteSpace = _R.revToResp(gp ? dpc.whitespace === undefined ? "nowrap" : dpc.whitespace : d.whitespace === undefined ? "nowrap" : d.whitespace, _R[id].rle);
				d.textAlign = _R.revToResp(gp ? dpc.textalign === undefined ? "left" : dpc.textalign : d.textalign === undefined ? "left" : d.textalign, _R[id].rle);
				d.letterSpacing = _R.revToResp(gp ? dpc.letterspacing === undefined ? parseInt(_R[id].computedStyle[pc[0].id].letterSpacing, 0) || "inherit" : dpc.letterspacing : d.letterspacing === undefined ? parseInt(_R[id].computedStyle[L[0].id].letterSpacing === "normal" ? 0 : _R[id].computedStyle[L[0].id].letterSpacing, 0) || "inherit" : d.letterspacing, _R[id].rle);
				d.textDecoration = gp ? dpc.textDecoration === undefined ? "none" : dpc.textDecoration : d.textDecoration === undefined ? "none" : d.textDecoration;
				lhdef = 25;
				lhdef = pc !== undefined && L[0].tagName === "I" ? "inherit" : lhdef;
				if (d.tshadow !== undefined) {
					d.tshadow.b = _R.revToResp(d.tshadow.b, _R[id].rle);
					d.tshadow.h = _R.revToResp(d.tshadow.h, _R[id].rle);
					d.tshadow.v = _R.revToResp(d.tshadow.v, _R[id].rle);
				}
			}

			if (d.bshadow !== undefined) {
				d.bshadow.b = _R.revToResp(d.bshadow.b, _R[id].rle);
				d.bshadow.h = _R.revToResp(d.bshadow.h, _R[id].rle);
				d.bshadow.v = _R.revToResp(d.bshadow.v, _R[id].rle);
				d.bshadow.s = _R.revToResp(d.bshadow.s, _R[id].rle);
			}

			if (d.tstroke !== undefined) d.tstroke.w = _R.revToResp(d.tstroke.w, _R[id].rle);


			d.display = gp ? dpc.display === undefined ? _R[id].computedStyle[pc[0].id].display : dpc.display : d.display === undefined ? _R[id].computedStyle[L[0].id].display : d.display;
			d.float = _R.revToResp(gp ? dpc.float === undefined ? _R[id].computedStyle[pc[0].id].float || "none" : dpc.float : d.float === undefined ? "none" : d.float, _R[id].rle);
			d.clear = _R.revToResp(gp ? dpc.clear === undefined ? _R[id].computedStyle[pc[0].id].clear || "none" : dpc.clear : d.clear === undefined ? "none" : d.clear, _R[id].rle);


			d.lineHeight = _R.revToResp(!L.is('img') && jQuery.inArray(d.layertype, ["video", "image", "audio"]) == -1 ?
				gp ?
				dpc.lineheight === undefined ? parseInt(_R[id].computedStyle[pc[0].id].lineHeight, 0) || lhdef : dpc.lineheight :
				d.lineheight === undefined ? lhdef :
				d.lineheight :
				lhdef, _R[id].rle);


			d.zIndex = gp ? dpc.zindex === undefined ? parseInt(_R[id].computedStyle[pc[0].id].zIndex, 0) || "inherit" : dpc.zindex : d.zindex === undefined ? parseInt(_R[id].computedStyle[L[0].id].zIndex, 0) || "inherit" : d.zindex;

			for (i = 0; i < 4; i++) {
				d['padding' + HR[i]] = _R.revToResp(d['padding' + hr[i]] === undefined ? parseInt(_R[id].computedStyle[L[0].id]['padding' + HR[i]], 0) || 0 : d['padding' + hr[i]], _R[id].rle);
				d['margin' + HR[i]] = _R.revToResp(d['margin' + hr[i]] === undefined ? parseInt(_R[id].computedStyle[L[0].id]['margin' + HR[i]], 0) || 0 : d['margin' + hr[i]], _R[id].rle);
				d['border' + HR[i] + 'Width'] = d.borderwidth === undefined ? parseInt(_R[id].computedStyle[L[0].id]['border' + HR[i] + 'Width'], 0) || 0 : d.borderwidth[i];
				d['border' + HR[i] + 'Color'] = d.bordercolor === undefined ? _R[id].computedStyle[L[0].id]["border-" + hr[i] + "-color"] : d.bordercolor;
				d['border' + CO[i] + 'Radius'] = _R.revToResp(d.borderradius === undefined ? _R[id].computedStyle[L[0].id]['border' + CO[i] + 'Radius'] || 0 : d.borderradius[i], _R[id].rle);
			}


			d.borderStyle = _R.revToResp(d.borderstyle === undefined ? _R[id].computedStyle[L[0].id].borderStyle || 0 : d.borderstyle, _R[id].rle);

			if (level !== "rekursive") {
				d.color = _R.revToResp(d.color === undefined ? "#ffffff" : d.color, _R[id].rle, undefined, "||");
				d.minWidth = _R.revToResp(d.minwidth === undefined ? parseInt(_R[id].computedStyle[L[0].id].minWidth, 0) || 0 : d.minwidth, _R[id].rle);
				d.minHeight = _R.revToResp(d.minheight === undefined ? parseInt(_R[id].computedStyle[L[0].id].minHeight, 0) || 0 : d.minheight, _R[id].rle);
				d.width = _R.revToResp(d.width === undefined ? "auto" : _R.smartConvertDivs(d.width), _R[id].rle);
				d.height = _R.revToResp(d.height === undefined ? "auto" : _R.smartConvertDivs(d.height), _R[id].rle);
				d.maxWidth = _R.revToResp(d.maxwidth === undefined ? parseInt(_R[id].computedStyle[L[0].id].maxWidth, 0) || "none" : d.maxwidth, _R[id].rle);
				d.maxHeight = _R.revToResp(jQuery.inArray(d.type, ["column", "row"]) !== -1 ? "none" : d.maxheight !== undefined ? parseInt(_R[id].computedStyle[L[0].id].maxHeight, 0) || "none" : d.maxheight, _R[id].rle);
			} else
			if (d.layertype === "html") {
				d.width = _R.revToResp(L[0].width, _R[id].rle);
				d.height = _R.revToResp(L[0].height, _R[id].rle);
			}


			d.styleProps = {
				"background": L[0].style.background,
				"background-color": L[0].style["background-color"],
				"color": L[0].style.color,
				"cursor": L[0].style.cursor,
				"font-style": L[0].style["font-style"]
			};
			if (d.bshadow == undefined) d.styleProps.boxShadow = L[0].style.boxShadow;
			if (d.styleProps.background === "" || d.styleProps.background === undefined || d.styleProps.background === d.styleProps["background-color"]) delete d.styleProps.background;

			if (d.styleProps.color == "") d.styleProps.color = _R[id].computedStyle[L[0].id].color;


			//REMOVE UNNEEDD THINGS
			for (i = 0; i < 4; i++) {
				if (notNeeded(d['padding' + HR[i]], 0)) delete d['padding' + HR[i]];
				if (notNeeded(d['margin' + HR[i]], 0)) delete d['margin' + HR[i]];
				if (notNeeded(d['border' + CO[i] + 'Radius'], '0px')) delete d['border' + CO[i] + 'Radius'];
				else if (notNeeded(d['border' + CO[i] + 'Radius'], "0")) delete d['border' + CO[i] + 'Radius'];
			}

			if (notNeeded(d.borderStyle, 'none')) {
				delete d.borderStyle;
				for (i = 0; i < 4; i++) {
					delete d['border' + HR[i] + 'Width'];
					delete d['border' + HR[i] + 'Color'];
				}
			}

		},

		notNeeded = function(a, d) { return (d === a[0] && d === a[1] && d === a[2] && d === a[3]); },
		getLayerResponsiveValues = function(L, id, RSL) {

			if (L === undefined) return;
			if (L[0].nodeName == "BR" || L[0].tagName == "br") return false;

			var l = _R[id].level,
				i,
				_ = L[0] !== undefined && L[0].id !== undefined && _R[id]._L[L[0].id] !== undefined ? _R[id]._L[L[0].id] : L.data();

			_ = _.basealign === undefined ? RSL.data() : _;
			if (_._isnotext === undefined) _._isnotext = RSL !== undefined && RSL[0] !== undefined && RSL[0].length > 0 ? _R.gA(RSL[0], '_isnotext') : _._isnotext;

			var ret = {
				basealign: _.basealign === undefined ? "grid" : _.basealign,
				lineHeight: _.basealign === undefined ? "inherit" : parseInt(_.lineHeight[l]),
				color: _.color === undefined ? undefined : _.color[l],
				width: _.width === undefined ? undefined : _.width[l] === "a" ? "auto" : _.width[l],
				height: _.height === undefined ? undefined : _.height[l] === "a" ? "auto" : _.height[l],
				minWidth: _.minWidth === undefined ? undefined : _.minWidth[l] === "n" ? "none" : _.minWidth[l],
				minHeight: _.minHeight === undefined ? undefined : _.minHeight[l] == "n" ? "none" : _.minHeight[l],
				maxWidth: _.maxWidth === undefined ? undefined : _.maxWidth[l] == "n" ? "none" : _.maxWidth[l],
				maxHeight: _.maxHeight === undefined ? undefined : _.maxHeight[l] == "n" ? "none" : _.maxHeight[l],
				float: _.float[l],
				clear: _.clear[l]
			};



			if (_.borderStyle) ret.borderStyle = _.borderStyle[l];
			for (i = 0; i < 4; i++) {
				if (_['padding' + HR[i]]) ret['padding' + HR[i]] = _['padding' + HR[i]][l];
				if (_['margin' + HR[i]]) ret['margin' + HR[i]] = parseInt(_['margin' + HR[i]][l]);
				if (_['border' + CO[i] + 'Radius']) ret['border' + CO[i] + 'Radius'] = _['border' + CO[i] + 'Radius'][l];
				if (_['border' + HR[i] + 'Color']) ret['border' + HR[i] + 'Color'] = _['border' + HR[i] + 'Color'];
				if (_['border' + HR[i] + 'Width']) ret['border' + HR[i] + 'Width'] = parseInt(_['border' + HR[i] + 'Width']);
			}

			if (!_._isnotext) {
				ret.textDecoration = _.textDecoration;
				ret.fontSize = parseInt(_.fontSize[l]);
				ret.fontWeight = parseInt(_.fontWeight[l]);
				ret.letterSpacing = parseInt(_.letterSpacing[l]) || 0;
				ret.textAlign = _.textAlign[l];
				ret.whiteSpace = _.whiteSpace[l];
				ret.whiteSpace = ret.whiteSpace === "normal" && ret.width === "auto" && _._incolumn !== true ? "nowrap" : ret.whiteSpace;
				ret.display = _.display;
				if (_.tshadow !== undefined) ret.textShadow = "" + parseInt(_.tshadow.h[l], 0) + "px " + parseInt(_.tshadow.v[l], 0) + "px " + _.tshadow.b[l] + " " + _.tshadow.c;
				if (_.tstroke !== undefined) ret.textStroke = "" + parseInt(_.tstroke.w[l], 0) + "px " + _.tstroke.c;
			}

			if (_.bshadow !== undefined) ret.boxShadow = "" + parseInt(_.bshadow.h[l], 0) + "px " + parseInt(_.bshadow.v[l], 0) + "px " + parseInt(_.bshadow.b[l], 0) + "px " + parseInt(_.bshadow.s[l], 0) + "px " + _.bshadow.c;


			return ret;
		},


		minmaxconvert = function(a, m, r, fr, b) {
			var sfx = !_R.isNumeric(a) && a !== undefined ? a.indexOf("px") >= 0 ? "px" : a.indexOf("%") >= 0 ? "%" : "" : "";
			a = _R.isNumeric(parseInt(a)) ? parseInt(a) : a;
			a = _R.isNumeric(a) ? (a * m) + sfx : a;
			a = a === "full" ? fr : a === "auto" || a === "none" ? r : a;
			a = a == undefined ? b : a;
			return a;
		},

		notzero = function(a) {
			return a !== undefined && a !== null && parseInt(a, 0) !== 0;
		},

		/////////////////////////////////////////////////////////////////
		//	-	CALCULATE THE RESPONSIVE SIZES OF THE CAPTIONS	-	  //
		/////////////////////////////////////////////////////////////////
		calcResponsiveLayer = function(obj) {


			var i,
				S, a, b, c, d,
				frams, prop,
				L = obj.a,
				id = obj.b,
				level = obj.c,
				responsive = obj.d,
				slideIndex = obj.e,
				winw, winh, LOBJ = {},
				MOBJ = {},
				_ = _R[id]._L[L[0].id],
				clasName = L[0].className;


			_ = _ === undefined ? {} : _;

			// svg elements and their children can return an Object as their "className" which then fails when calling "L[0].className.indexOf"
			if (typeof clasName === 'object') clasName = '';

			if (L !== undefined && L[0] !== undefined && (clasName.indexOf("rs_splitted") >= 0 || L[0].nodeName == "BR" || L[0].tagName == "br" || L[0].tagName.indexOf("FCR") > 0 || L[0].tagName.indexOf("BCR") > 0)) return false;

			slideIndex = slideIndex === "individual" ? _.slideIndex : slideIndex;

			var obj = getLayerResponsiveValues(L, id, obj.RSL),
				bw = responsive === "off" ? 1 : _R[id].CM.w,
				objCache;
			if (_._isnotext === undefined) _._isnotext = obj.RSL !== undefined && obj.RSL[0] !== undefined && obj.RSL[0].length > 0 ? _R.gA(obj.RSL[0], '_isnotext') : _._isnotext;

			_.OBJUPD = _.OBJUPD == undefined ? {} : _.OBJUPD;
			_.caches = _.caches == undefined ? {} : _.caches;

			// Column Gets Margin as Padding
			if (_.type === "column") {
				S = {};
				objCache = {};
				for (i = 0; i < 4; i++)
					if (obj['margin' + HR[i]] !== undefined) {
						S['padding' + HR[i]] = Math.round(obj['margin' + HR[i]] * bw) + "px";
						objCache['margin' + HR[i]] = obj['margin' + HR[i]];
						delete obj['margin' + HR[i]];
					}

				if (!jQuery.isEmptyObject(S)) tpGS.gsap.set(_._column, S);
			}

			var POBJ = _R.clone(_.OBJUPD.POBJ),
				LPOBJ = _R.clone(_.OBJUPD.LPOBJ);


			if (clasName.indexOf("rs_splitted_") === -1) {
				S = { overwrite: "auto" }
				for (i = 0; i < 4; i++) {
					if (obj['border' + CO[i] + 'Radius'] !== undefined) S['border' + CO[i] + 'Radius'] = obj['border' + CO[i] + 'Radius'];
					if (obj['padding' + HR[i]] !== undefined) S['padding' + HR[i]] = Math.round(obj['padding' + HR[i]] * bw) + "px";
					if (obj['margin' + HR[i]] !== undefined && !_._incolumn) S['margin' + HR[i]] = _.type === "row" ? 0 : Math.round(obj['margin' + HR[i]] * bw) + "px";
				}
				if (_.spike !== undefined) S["clip-path"] = S["-webkit-clip-path"] = _.spike;
				if (obj.boxShadow) S.boxShadow = obj.boxShadow;


				if (_.type !== "column") {
					if (obj.borderStyle !== undefined && obj.borderStyle !== "none" && (obj.borderTopWidth !== 0 || obj.borderBottomWidth > 0 || obj.borderLeftWidth > 0 || obj.borderRightWidth > 0)) {
						S.borderTopWidth = Math.round(obj.borderTopWidth * bw) + "px";
						S.borderBottomWidth = Math.round(obj.borderBottomWidth * bw) + "px";
						S.borderLeftWidth = Math.round(obj.borderLeftWidth * bw) + "px";
						S.borderRightWidth = Math.round(obj.borderRightWidth * bw) + "px";
						S.borderStyle = obj.borderStyle;
						S.borderTopColor = obj.borderTopColor;
						S.borderBottomColor = obj.borderBottomColor;
						S.borderLeftColor = obj.borderLeftColor;
						S.borderRightColor = obj.borderRightColor;

					} else {
						if (obj.borderStyle === "none") S.borderStyle = "none";
						S.borderTopColor = obj.borderTopColor;
						S.borderBottomColor = obj.borderBottomColor;
						S.borderLeftColor = obj.borderLeftColor;
						S.borderRightColor = obj.borderRightColor;

					}
				}

				if ((_.type === "shape" || _.type === "image") && (notzero(obj.borderTopLeftRadius) || notzero(obj.borderTopRightRadius) || notzero(obj.borderBottomLeftRadius) || notzero(obj.borderBottomRightRadius))) S.overflow = "hidden";

				if (!_._isnotext) {
					if (_.type !== "column") {
						S.fontSize = Math.round((obj.fontSize * bw)) + "px";
						S.fontWeight = obj.fontWeight;
						S.letterSpacing = (obj.letterSpacing * bw) + "px";
						if (obj.textShadow) S.textShadow = obj.textShadow;
						if (obj.textStroke) S['-webkit-text-stroke'] = obj.textStroke;
					}
					S.lineHeight = Math.round(obj.lineHeight * bw) + "px";
					S.textAlign = (obj.textAlign);

				}

				if (_.type === "column") {
					if (_.cbg_set === undefined) {
						_.cbg_set = _.styleProps["background-color"];
						_.cbg_set = _.cbg_set == "" || _.cbg_set === undefined || _.cbg_set.length == 0 ? "transparent" : _.cbg_set;

						_.cbg_img = L.css('backgroundImage');
						if (_.cbg_img !== "" && _.cbg_img !== undefined && _.cbg_img !== "none") {
							_.cbg_img_r = L.css('backgroundRepeat');
							_.cbg_img_p = L.css('backgroundPosition');
							_.cbg_img_s = L.css('backgroundSize');
						}
						_.cbg_o = _.bgopacity ? 1 : _.bgopacity;
						LOBJ.backgroundColor = "transparent";
						LOBJ.backgroundImage = "";
					}
					S.backgroundColor = "transparent";
					S.backgroundImage = "none";
				}

				if (_._isstatic && _.elementHovered) {
					frams = L.data('frames');
					if (frams && frams.frame_hover && frams.frame_hover.transform)
						for (prop in S)
							if (S.hasOwnProperty(prop) && frams.frame_hover.transform.hasOwnProperty(prop)) delete S[prop];
				}


				if (L[0].nodeName == "IFRAME" && _R.gA(L[0], "layertype") === "html") {
					winw = obj.basealign == "slide" ? _R[id].module.width : _R.iWA(id, slideIndex);
					winh = obj.basealign == "slide" ? _R[id].module.height : _R.iHE(id); //*(_R[id].keepBPHeight || _R[id].currentRowsHeight>_R[id].gridheight[_R[id].level] ? 1 :_R[id].CM.h)
					S.width = !_R.isNumeric(obj.width) && obj.width.indexOf("%") >= 0 ? (_._isstatic && !_._incolumn && !_._ingroup) ? winw * parseInt(obj.width, 0) / 100 : obj.width : minmaxconvert(obj.width, bw, "auto", winw, "auto");
					S.height = !_R.isNumeric(obj.height) && obj.height.indexOf("%") >= 0 ? (_._isstatic && !_._incolumn && !_._ingroup) ? winh * parseInt(obj.height, 0) / 100 : obj.height : minmaxconvert(obj.height, bw, "auto", winw, "auto");
				}

				LOBJ = jQuery.extend(true, LOBJ, S);

				if (level != "rekursive") {
					winw = obj.basealign == "slide" ? _R[id].module.width : _R.iWA(id, slideIndex);
					winh = obj.basealign == "slide" ? _R[id].module.height : _R.iHE(id); //*(_R[id].keepBPHeight || _R[id].currentRowsHeight>_R[id].gridheight[_R[id].level] ? 1 :_R[id].CM.h)


					var swid = !_R.isNumeric(obj.width) && obj.width.indexOf("%") >= 0 ? (_._isstatic && !_._incolumn && !_._ingroup) ? winw * parseInt(obj.width, 0) / 100 : obj.width : minmaxconvert(obj.width, bw, "auto", winw, "auto"),
						shei = !_R.isNumeric(obj.height) && obj.height.indexOf("%") >= 0 ? (_._isstatic && !_._incolumn && !_._ingroup) ? winh * parseInt(obj.height, 0) / 100 : obj.height : minmaxconvert(obj.height, bw, "auto", winw, "auto"),
						Sr = {
							maxWidth: minmaxconvert(obj.maxWidth, bw, "none", winw, "none"),
							maxHeight: minmaxconvert(obj.maxHeight, bw, "none", winh, "none"),
							minWidth: minmaxconvert(obj.minWidth, bw, "0px", winw, 0),
							minHeight: minmaxconvert(obj.minHeight, bw, "0px", winh, 0),
							height: shei,
							width: swid,
							overwrite: "auto"
						};


					if (_.heightSetByVideo == true) Sr.height = _.vidOBJ.height;

					var POBJ_HEIGHT_PERCENTAGE = false;

					// FIX FOR OLD VERSION KILLS MAX WIDTH CONTAINERS IN COLUMNS IN NEW VERSION
					//swid = obj.float==="none" && (obj.display==="block" || _.display==="block") && _._incolumn && _.type!=="column" ? "auto" : swid;

					if (_._incolumn) {
						POBJ = jQuery.extend(true, POBJ, { minWidth: swid, maxWidth: swid, float: obj.float, clear: obj.clear });
						for (i = 0; i < 4; i++)
							if (obj['margin' + HR[i]] !== undefined) POBJ['margin' + HR[i]] = (obj['margin' + HR[i]] * bw) + "px";
						LPOBJ.width = "100%";
						if (obj.display === undefined || obj.display === "inline-block") MOBJ = { width: "100%" };
						Sr.width = !_R.isNumeric(obj.width) && obj.width.indexOf("%") >= 0 ? "100%" : swid;
						if (_.type === "image") tpGS.gsap.set(_.img, { width: "100%" /*Sr.width*/ });
						// Make sure Image Inside Layer has 100% Width to fill Inline Block Elements in Columns
						// Put 100% now back to make sure that Borders are visible well on Images in Column.

					} else
					if (!_R.isNumeric(obj.width) && obj.width.indexOf("%") >= 0) {
						POBJ.minWidth = _.basealign === "slide" || _._ingroup === true ? swid : (_R.iWA(id, slideIndex) * _R[id].CM.w) * parseInt(swid) / 100 + "px";
						LPOBJ.width = "100%";
						MOBJ.width = "100%";
					}

					if (!_R.isNumeric(obj.height) && obj.height.indexOf("%") >= 0) {
						POBJ.minHeight = _.basealign === "slide" || _._ingroup === true ? shei : (_R.iHE(id) * ( /*_R[id].keepBPHeight ||*/ _R[id].currentRowsHeight > _R[id].gridheight[_R[id].level] ? 1 : _R[id].CM.w)) * parseInt(shei) / 100 + "px";
						LPOBJ.height = "100%";
						MOBJ.height = "100%";
						POBJ_HEIGHT_PERCENTAGE = true;
					}

					if (!_._isnotext) {
						Sr.whiteSpace = obj.whiteSpace;
						Sr.textAlign = obj.textAlign;
						Sr.textDecoration = obj.textDecoration;
					}
					if (obj.color != "npc" && obj.color !== undefined) Sr.color = obj.color;

					if (_._ingroup) {
						_._groupw = Sr.minWidth;
						_._grouph = Sr.minHeight;
					}

					if (_.type === "row" && (_R.isNumeric(Sr.minHeight) || Sr.minHeight.indexOf("px") >= 0) && Sr.minHeight !== "0px" && Sr.minHeight !== 0 && Sr.minHeight !== "0" && Sr.minHeight !== "none")
						Sr.height = Sr.minHeight;
					else
					if (_.type === "row")
						Sr.height = "auto";

					// see blocked comment block above
					if (_._isstatic && _.elementHovered) {
						frams = L.data('frames');
						if (frams && frams.frame_hover && frams.frame_hover.transform) {
							for (prop in Sr) {
								if (Sr.hasOwnProperty(prop) && frams.frame_hover.transform.hasOwnProperty(prop)) delete Sr[prop];
							}
						}
					}

					if (_.type !== "group" && _.type !== "row" && _.type !== "column") {
						if (!_R.isNumeric(Sr.width) && Sr.width.indexOf("%") >= 0) Sr.width = "100%";
						if (!_R.isNumeric(Sr.height) && Sr.height.indexOf("%") >= 0) Sr.height = "100%";
					}

					if (_._isgroup) {
						if (!_R.isNumeric(Sr.width) && Sr.width.indexOf("%") >= 0) Sr.width = "100%";
						POBJ.height = POBJ_HEIGHT_PERCENTAGE ? "100%" : Sr.height;

					}

					LOBJ = jQuery.extend(true, LOBJ, Sr);

					if (_.svg_src != undefined && _.svgI !== undefined) {
						// patch, as this value can sometimes exist as a string
						if (typeof _.svgI.fill === 'string') _.svgI.fill = [_.svgI.fill];

						_.svgTemp = _R.clone(_.svgI);
						if (_.svgTemp.fill !== undefined) {
							_.svgTemp.fill = _.svgTemp.fill[_R[id].level];
							tpGS.gsap.set(_.svgPath, { fill: _.svgI.fill[_R[id].level] });
						}
						tpGS.gsap.set(_.svg, _.svgTemp);
					}
				}

				// ROW MARGIN SHOULD BE SET AS PADDING
				if (_.type === "row")
					for (i = 0; i < 4; i++)
						if (obj['margin' + HR[i]] !== undefined) POBJ['padding' + HR[i]] = (obj['margin' + HR[i]] * bw) + "px";

				if (_.type === "column" && _.cbg && _.cbg.length > 0) {
					// DYNAMIC HEIGHT AUTO CALCULATED BY BROWSER
					if (_.cbg_img_s !== undefined) _.cbg[0].style.backgroundSize = _.cbg_img_s;
					S = {};
					if (_.styleProps.cursor !== "") S.cursor = _.styleProps.cursor;
					if (_.cbg_set !== "" && _.cbg_set !== "transparent") S.backgroundColor = _.cbg_set;
					if (_.cbg_img !== "" && _.cbg_img !== "none") {
						S.backgroundImage = _.cbg_img;
						if (_.cbg_img_r !== "") S.backgroundRepeat = _.cbg_img_r;
						if (_.cbg_img_p !== "") S.backgroundPosition = _.cbg_img_p;
					}
					if (_.cbg_o !== "" && _.cbg_o !== undefined) S.opacity = _.cbg_o;

					for (i = 0; i < 4; i++) {

						if (obj.borderStyle !== undefined && obj.borderStyle !== "none") {
							if (obj['border' + HR[i] + 'Width'] !== undefined) S['border' + HR[i] + 'Width'] = Math.round(parseInt(obj['border' + HR[i] + 'Width']) * bw) + "px";
							if (obj['border' + HR[i] + 'Color'] !== undefined) S['border' + HR[i] + 'Color'] = obj['border' + HR[i] + 'Color'];
						}
						if (obj['border' + CO[i] + 'Radius']) S['border' + CO[i] + 'Radius'] = obj['border' + CO[i] + 'Radius'];
					}
					if (obj.borderStyle !== undefined && obj.borderStyle !== "none") S.borderStyle = obj.borderStyle;


					a = JSON.stringify(S);
					if (a !== _R[id].emptyObject && a !== _.caches.cbgS) tpGS.gsap.set(_.cbg, S);
					_.caches.cbgS = a;

					S = {};
					for (i = 0; i < 4; i++)
						if (objCache['margin' + HR[i]]) S[hr[i]] = (objCache['margin' + HR[i]] * bw) + "px";
					a = JSON.stringify(S);
					if (a !== _R[id].emptyObject && a !== _.caches.cbgmaskS) { tpGS.gsap.set(_.cbgmask, S);
						_.caches.cbgmaskS = a; }
				}



				if (POBJ.maxWidth === "auto") POBJ.maxWidth = "inherit";
				if (POBJ.maxHeight === "auto") POBJ.maxHeight = "inherit";
				if (MOBJ.maxWidth === "auto") MOBJ.maxWidth = "inherit";
				if (MOBJ.maxHeight === "auto") MOBJ.maxHeight = "inherit";
				if (LPOBJ.maxWidth === "auto") LPOBJ.maxWidth = "inherit";
				if (LPOBJ.maxHeight === "auto") LPOBJ.maxHeight = "inherit";

				// PRESETS
				// VIDEO OBJECT INFLUENCE ON LAYER
				if (_.vidOBJ !== undefined) {
					LOBJ.width = _.vidOBJ.width;
					LOBJ.height = _.vidOBJ.height;
				}

				// VIDEO INFLUENCE ON PARRENT ELEMENTS
				if (_.OBJUPD.lppmOBJ !== undefined) {
					if (_.OBJUPD.lppmOBJ.minWidth !== undefined) {
						LPOBJ.minWidth = _.OBJUPD.lppmOBJ.minWidth;
						MOBJ.minWidth = _.OBJUPD.lppmOBJ.minWidth;
						POBJ.minWidth = _.OBJUPD.lppmOBJ.minWidth;
					}
					if (_.OBJUPD.lppmOBJ.minHeight !== undefined) {
						LPOBJ.minHeight = _.OBJUPD.lppmOBJ.minHeight;
						MOBJ.minHeight = _.OBJUPD.lppmOBJ.minHeight;
						POBJ.minHeight = _.OBJUPD.lppmOBJ.minHeight;
					}
				}
				a = JSON.stringify(LOBJ);
				b = JSON.stringify(LPOBJ);
				c = JSON.stringify(MOBJ);
				d = JSON.stringify(POBJ);


				//IMG Object Update If Necessary
				if (_.imgOBJ !== undefined && (_.caches.imgOBJ === undefined || _.caches.imgOBJ.width !== _.imgOBJ.width || _.caches.imgOBJ.height !== _.imgOBJ.height || _.caches.imgOBJ.left !== _.imgOBJ.left || _.caches.imgOBJ.right !== _.imgOBJ.right || _.caches.imgOBJ.top !== _.imgOBJ.top || _.caches.imgOBJ.bottom !== _.imgOBJ.bottom)) {
					_.caches.imgOBJ = _R.clone(_.imgOBJ);
					_.imgOBJ.position = "relative";
					tpGS.gsap.set(_.img, _.imgOBJ);
				}

				//UPDATE MEDIA OBJ IF NEEDED
				if (_.mediaOBJ !== undefined && (_.caches.mediaOBJ === undefined || _.caches.mediaOBJ.width !== _.mediaOBJ.width || _.caches.mediaOBJ.height !== _.mediaOBJ.height || _.caches.mediaOBJ.display !== _.mediaOBJ.display)) {
					_.caches.mediaOBJ = _R.clone(_.mediaOBJ);
					_.media.css(_.mediaOBJ);
				}


				if (a != _R[id].emptyObject && a != _.caches.LOBJ) { tpGS.gsap.set(L, LOBJ);
					_.caches.LOBJ = a; }
				if (b != _R[id].emptyObject && b != _.caches.LPOBJ) { tpGS.gsap.set(_.lp, LPOBJ);
					_.caches.LPOBJ = b; }
				if (c != _R[id].emptyObject && c != _.caches.MOBJ) { tpGS.gsap.set(_.m, MOBJ);
					_.caches.MOBJ = c; }
				if (d != _R[id].emptyObject && d != _.caches.POBJ) { tpGS.gsap.set(_.p, POBJ);
					_.caches.POBJ = d;
					_.caches.POBJ_LEFT = POBJ.left;
					_.caches.POBJ_TOP = POBJ.top }

			}
		},

		getSpikePath = function(_) {
			var c = { l: "none", lw: 10, r: "none", rw: 10 };
			_ = _.split(";");

			for (var u in _) {
				if (!_.hasOwnProperty(u)) continue;
				var s = _[u].split(":");
				switch (s[0]) {
					case "l":
						c.l = s[1];
						break;
					case "r":
						c.r = s[1];
						break;
					case "lw":
						c.lw = s[1];
						break;
					case "rw":
						c.rw = s[1];
						break;
				}
			}
			return "polygon(" + getClipPaths(c.l, 0, parseFloat(c.lw)) + "," + getClipPaths(c.r, 100, (100 - parseFloat(c.rw)), true) + ")";
		},

		getClipPaths = function(_, o, i, reverse) {
			var r;
			switch (_) {
				case "none":
					r = o + '% 100%,' + o + '% 0%';
					break;
				case "top":
					r = i + '% 100%,' + o + '% 0%';
					break;
				case "middle":
					r = i + '% 100%,' + o + '% 50%,' + i + '% 0%';
					break;
				case "bottom":
					r = o + '% 100%,' + i + '% 0%';
					break;
				case "two":
					r = i + '% 100%,' + o + '% 75%,' + i + '% 50%,' + o + '% 25%,' + i + '% 0%';
					break;
				case "three":
					r = o + '% 100%,' + i + '% 75%,' + o + '% 50%,' + i + '% 25%,' + o + '% 0%';
					break;
				case "four":
					r = o + '% 100%,' + i + '% 87.5%,' + o + '% 75%,' + i + '% 62.5%,' + o + '% 50%,' + i + '% 37.5%,' + o + '% 25%,' + i + '% 12.5%,' + o + '% 0%';
					break;
				case "five":
					r = o + '% 100%,' + i + '% 90%,' + o + '% 80%,' + i + '% 70%,' + o + '% 60%,' + i + '% 50%,' + o + '% 40%,' + i + '% 30%,' + o + '% 20%,' + i + '% 10%,' + o + '% 0%';
					break;
			}
			if (reverse) {
				var s = r.split(",");
				r = "";
				for (var i in s) {
					if (!s.hasOwnProperty(i)) continue;
					r += s[(s.length - 1) - i] + (i < s.length - 1 ? "," : "");
				}
			}
			return r;
		};
	//Support Defer and Async and Footer Loads
	window.RS_MODULES = window.RS_MODULES || {};
	window.RS_MODULES.layeranimation = { loaded: true, version: version };
	if (window.RS_MODULES.checkMinimal) window.RS_MODULES.checkMinimal();

})(jQuery);