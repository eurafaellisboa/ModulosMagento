/*!

  - Slider Revolution JavaScript Plugin -

..........................xXXXXX.................
................. xXXXXX..xXXXXX..xXXXXX.........
..................xXXXXX..xXXXXX..xXXXXX.........
..........xXXXXX..xXXXXX..xXXXXX..xXXXXX.........
..........xXXXXX..xXXXXX..xXXXXX..xXXXXX.........
..........xXXXXX..xXXXXX..xXXXXX..xXXXXX.........
..........xXXXXX..xXXXXX..xXXXXX..xXXXXX.........
..........xXXXXX..xXXXXX..xXXXXX..xXXXXX.........
.........,xXXXXX..xXXXXX..xXXXXX..xXXXXX.........
.........,xXXXXX..xXXXXX..xXXXXX..xXXXXX.........
.........,xXXXXX..xXXXXX..xXXXXX..xXXXXX.........
..........xXXXXX..xXXXXX..xXXXXX..xXXXXX.........
.....................xxxxxxxxxxxxxxxxxxx.........
.....................xxxxxxxxxxxxxxxxxxx.........
.....................xxxxxxxxxxxxxxxxxxx.........

			   DATE: 2021-06-16
	@author: Krisztian Horvath, ThemePunch OHG.

INTRODUCING GIT
UPDATES AND DOCS AT:
https://www.themepunch.com/support-center

GET LICENSE AT:
https://www.themepunch.com/links/slider_revolution_wordpress_regular_license

LICENSE:
Copyright (c) 2009-2019, ThemePunch. All rights reserved.
This work is subject to the terms at https://www.themepunch.com/links/slider_revolution_wordpress_regular_license (Regular / Extended)

*/
//test
(function(jQuery, undefined) {
	"use strict";

	var version = "Slider Revolution 6.5.2";

	window.RSANYID = window.RSANYID === undefined ? [] : window.RSANYID;
	window.RSANYID_sliderID = window.RSANYID_sliderID === undefined ? [] : window.RSANYID_sliderID;

	jQuery.fn.revolution = jQuery.fn.revolution || {};
	var _R = jQuery.fn.revolution;

	jQuery.fn.revolutionInit = function(options) {

		return this.each(function() {


			_R.ISM = _R.ISM || _R.is_mobile();
			// REMOVE P TAG FIXES FOR WORDPRESS
			var pwpfix = document.getElementsByClassName('rs-p-wp-fix');
			while (pwpfix[0]) pwpfix[0].parentNode.removeChild(pwpfix[0]);
			if (this.id !== undefined) {
				_R[id] = { anyid: [] };
				this.id = _R.revCheckIDS(id, this, true);
			} else this.id = "rs_module_" + Math.round(Math.random() * 10000000);

			var id = this.id,
				exp = _R.clone(options);
			_R[id] = getModuleDefaults(options);
			_R[id].ignoreHeightChange = _R.ISM && _R[id].sliderLayout === "fullscreen" && _R[id].ignoreHeightChange;
			_R[id].option_export = exp;
			_R[id].anyid = [];
			_R[id]._Lshortcuts = {};
			_R[id].computedStyle = {};
			_R[id].c = jQuery(this);
			_R[id].cpar = _R[id].c.parent();
			_R[id].canvas = _R[id].c.find('rs-slides');
			_R[id].caches = { calcResponsiveLayersList: [], contWidthManager: {} };
			_R[id].sbgs = {};
			window.RSBrowser = window.RSBrowser === undefined ? _R.get_browser() : window.RSBrowser;
			_R.setIsIOS();
			_R.setIsChrome8889();
			_R[id].noDetach = _R[id].BUG_ie_clipPath = window.RSBrowser === "Edge" || window.RSBrowser === "IE";

			_R.getByTag = getTagSelector();

			// BASIC SETTINGS
			_R[id].indexhelper = 0;
			_R[id].fullScreenOffsetResult = 0;
			_R[id].level = 0;
			_R[id].rtl = jQuery('body').hasClass("rtl");
			_R[id]._L = _R[id]._L === undefined ? {} : _R[id]._L;
			_R[id].emptyObject = "{}";
			_R[id].dimensionReCheck = {};

			if (_R.globalListener === undefined) _R.pageHandler(id);

			if (_R[id].stopAfterLoops != undefined && _R[id].stopAfterLoops > -1)
				_R[id].looptogo = _R[id].stopAfterLoops;
			else
				_R[id].looptogo = "disabled";

			//DEBUG
			window.T = _R[id];

			// FALLBACKS
			_R[id].BUG_safari_clipPath = _R.get_browser() === "Safari" && _R.get_browser_version() > "12";

			// Prepare maxHeight
			_R[id].minHeight = _R[id].sliderLayout === "fullwidth" ? 0 : _R[id].minHeight != undefined && _R[id].minHeight !== "" ? parseInt(_R[id].minHeight, 0) : 0;
			_R[id].minHeight = _R[id].minHeight === undefined ? 0 : _R[id].minHeight;

			_R[id].isEdge = _R.get_browser() === "Edge";

			//Prepare Min Height
			updateStartHeights(id);
			_R.updateVisibleArea(id);
			checkBlockSpaces(id);

			if (!_R.mesuredScrollBarDone) _R.mesureScrollBar();

			window.requestAnimationFrame(function() {
				// If Fullscreen Slidr has Offset Size containers, need to reduce height at very early timepoint
				if (_R[id].sliderLayout === "fullscreen") {
					var foffset = _R.getFullscreenOffsets(id);
					if (foffset !== 0) _R[id].cpar.height(_R.getWinH(id) - foffset);
				}
				_R[id].cpar[0].style.visibility = "visible";

			});

			//REMOVE SLIDES IF SLIDER IS HERO
			if (_R[id].sliderType == "hero") _R[id].c.find('rs-slide').each(function(i) { if (i > 0) jQuery(this).remove(); });

			// NAVIGATION EXTENSTION
			_R[id].navigation.use = _R[id].sliderType !== "hero" && (_R[id].sliderType == "carousel" || _R[id].navigation.keyboardNavigation || _R[id].navigation.mouseScrollNavigation == "on" || _R[id].navigation.mouseScrollNavigation == "carousel" || _R[id].navigation.touch.touchenabled || _R[id].navigation.arrows.enable || _R[id].navigation.bullets.enable || _R[id].navigation.thumbnails.enable || _R[id].navigation.tabs.enable);

			// LAYERANIM, VIDEOS, ACTIONS EXTENSIONS
			_R[id].c.find('rs-bgvideo').each(function() { if (this.tagName === "RS-BGVIDEO" && (this.id === undefined || this.id === "")) this.id = "rs-bg-video-" + Math.round(Math.random() * 1000000); });

			tpGS.force3D = "auto";

			// CHECK IF MODAL HAS BEEN INITIALISED ALREADY
			if (_R[id].modal.useAsModal === true && _R.RS_prioList.indexOf(id) === -1) {
				_R.RS_toInit[id] = false;
				_R.RS_prioList.push(id);
			}

			// CHECK IF SLIDER WAS ALREADY KILLED BEFORE
			if (_R.RS_killedlist !== undefined && _R.RS_killedlist.indexOf(id) !== -1) {
				_R.RS_toInit[id] = false;
				_R.RS_prioList.push(id);
			}

			// CHECK IF SLIDER HAS BEED CALLED AFTER GLOBAL LISTENER AND IS NOT YET ON THE LIST
			if (_R.RS_prioListFirstInit === true && _R[id].modal.useAsModal !== true && _R.RS_prioList.indexOf(id) === -1) {
				_R.RS_toInit[id] = false;
				_R.RS_prioList.push(id);
			}
			_R.initNextRevslider(id);
		});
	}



	var _R = window.RS_F;



	jQuery.fn.extend({


		getRSJASONOptions: function(id) { console.log(JSON.stringify(_R[id].option_export)); },

		//Get All Loaded Version
		getRSVersion: function(silent) {
			var v = window.SliderRevolutionVersion,
				t, m;
			if (!silent) {
				t = m = "---------------------------------------------------------\n";
				t += "    Currently Loaded Slider Revolution & SR Modules :\n" + m;
				for (var key in v)
					if (v.hasOwnProperty(key)) t += (v[key].alias + ": " + v[key].ver) + "\n";
				t += m;
			}
			return silent ? v : t;
		},


		// Remove a Slide from the Slider
		revremoveslide: function(sindex) {
			return this.each(function() {
				var id = this.id;
				// REDUCE THE CURRENT ID
				if (sindex < 0 || sindex > _R[id].slideamount) return;

				if (_R[id] && _R[id].slides.length > 0) {
					if (sindex > 0 || sindex <= _R[id].slides.length) {
						var ref = _R.gA(_R[id].slides[sindex], 'key');
						_R[id].slideamount = _R[id].slideamount - 1;
						_R[id].realslideamount = _R[id].realslideamount - 1;
						removeNavWithLiref('rs-bullet', ref, id);
						removeNavWithLiref('rs-tab', ref, id);
						removeNavWithLiref('rs-thumb', ref, id);
						jQuery(_R[id].slides[sindex]).remove();
						_R[id].thumbs = removeArray(_R[id].thumbs, sindex);
						if (_R.updateNavIndexes) _R.updateNavIndexes(id);
						if (sindex <= _R[id].pr_active_key) _R[id].pr_active_key = _R[id].pr_active_key - 1;
					}
				}
			});

		},

		// Add a New Call Back to some Module
		revaddcallback: function(callback) {
			return this.each(function() {
				if (_R[this.id]) {
					if (_R[this.id].callBackArray === undefined)
						_R[this.id].callBackArray = [];
					_R[this.id].callBackArray.push(callback);
				}
			});
		},

		// Get Current Parallax Proc
		revgetparallaxproc: function() {
			if (_R[this[0].id]) return _R[this[0].id].scrollproc;
		},

		// ENABLE DEBUG MODE
		revdebugmode: function() {
			return;
		},

		// METHODE SCROLL
		revscroll: function(oy) {
			return this.each(function() {
				var c = jQuery(this);
				jQuery('body,html').animate({ scrollTop: (c.offset().top + (c.height()) - oy) + "px" }, { duration: 400 });
			});
		},

		// METHODE PAUSE
		revredraw: function() {
			return this.each(function() {
				containerResized(this.id, undefined, true);
			});
		},
		// METHODE PAUSE
		revkill: function() {
			return this.each(function() {
				var id = this.id;

				_R[id].c.data('conthover', 1);
				_R[id].c.data('conthoverchanged', 1);
				_R[id].c.trigger('revolution.slide.onpause');
				_R[id].tonpause = true;
				_R[id].c.trigger('stoptimer');
				_R[id].sliderisrunning = false;


				var resizid = "updateContainerSizes." + _R[id].c.attr('id');
				_R.window.unbind(resizid);
				tpGS.gsap.killTweensOf(_R[id].c.find('*'), false);
				tpGS.gsap.killTweensOf(_R[id].c, false);
				_R[id].c.unbind('hover, mouseover, mouseenter,mouseleave, resize');
				_R[id].c.find('*').each(function() {
					var el = jQuery(this);

					el.unbind('on, hover, mouseenter,mouseleave,mouseover, resize,restarttimer, stoptimer');
					el.off('on, hover, mouseenter,mouseleave,mouseover, resize');
					el.data('mySplitText', null);
					el.data('ctl', null);
					if (el.data('tween') != undefined) el.data('tween').kill();
					if (el.data('pztl') != undefined) el.data('pztl').kill();
					if (el.data('timeline_out') != undefined) el.data('timeline_out').kill();
					if (el.data('timeline') != undefined) el.data('timeline').kill();
					el.remove();
					el.empty();
					el = null;
				});

				tpGS.gsap.killTweensOf(_R[id].c.find('*'), false);
				tpGS.gsap.killTweensOf(_R[id].c, false);
				_R[id].progressC.remove();
				try { _R[id].c.closest('.rev_slider_wrapper').detach(); } catch (e) {}
				try { _R[id].c.closest('rs-fullwidth-wrap').remove(); } catch (e) {}
				try { _R[id].c.closest('rs-module-wrap').remove(); } catch (e) {}
				try { _R[id].c.remove(); } catch (e) {}
				_R[id].cpar.detach();
				_R[id].c.html('');
				_R[id].c = null;
				delete _R[id];

				//Remove Slider from the PrioList and reset Preparations
				_R.RS_prioList.splice(_R.RS_prioList.indexOf(id), 1);
				_R.RS_toInit[id] = false;
				//Put Slider on the Killed List, so it can be reinitialised later again if needed
				_R.RS_killedlist = _R.RS_killedlist === undefined ? [] : _R.RS_killedlist;
				if (_R.RS_killedlist.indexOf(id) === -1) _R.RS_killedlist.push(id);
			});

		},

		// METHODE PAUSE
		revpause: function() {
			return this.each(function() {
				var c = jQuery(this);
				if (c != undefined && c.length > 0 && jQuery('body').find('#' + c.attr('id')).length > 0) {
					c.data('conthover', 1);
					c.data('conthoverchanged', 1);
					c.trigger('revolution.slide.onpause');
					_R[this.id].tonpause = true;
					c.trigger('stoptimer');
				}
			});
		},

		// METHODE RESUME
		revresume: function() {
			return this.each(function() {
				if (_R[this.id] !== undefined) {
					var c = jQuery(this);
					c.data('conthover', 0);
					c.data('conthoverchanged', 1);
					c.trigger('revolution.slide.onresume');
					_R[this.id].tonpause = false;
					c.trigger('starttimer');
				}
			});
		},

		revmodal: function(o) {
			var $this = this instanceof jQuery ? this[0] : this,
				id = $this.id;
			if (_R[$this.id] !== undefined) _R.revModal(id, o);
		},



		revstart: function() {
			// "this" is a jQuery Object here
			var $this = this instanceof jQuery ? this[0] : this;

			if (_R[$this.id] === undefined) {
				console.log("Slider is Not Existing");
				return false;
			} else
			if (!_R[$this.id].sliderisrunning && _R[$this.id].initEnded !== true) { //Double Check if we really can start the slider !
				_R[$this.id].c = jQuery($this);
				_R[$this.id].canvas = _R[$this.id].c.find('rs-slides');
				runSlider($this.id);
				return true;
			} else {
				console.log("Slider Is Running Already");
				return false;
			}
		},

		// METHODE NEXT
		revnext: function() {
			return this.each(function() {
				// CATCH THE CONTAINER
				if (_R[this.id] !== undefined) _R.callingNewSlide(this.id, 1, _R[this.id].sliderType === "carousel");

			});
		},

		// METHODE RESUME
		revprev: function() {
			return this.each(function() {
				// CATCH THE CONTAINER
				if (_R[this.id] !== undefined) _R.callingNewSlide(this.id, -1, _R[this.id].sliderType === "carousel");
			});
		},

		// METHODE LENGTH
		revmaxslide: function() {
			// CATCH THE CONTAINER
			return jQuery(this).find('rs-slide').length;
		},


		// METHODE CURRENT
		revcurrentslide: function() {
			// CATCH THE CONTAINER
			if (_R[jQuery(this)[0].id] !== undefined) return parseInt(_R[jQuery(this)[0].id].pr_active_key, 0) + 1;
		},

		// METHODE CURRENT
		revlastslide: function() {
			// CATCH THE CONTAINER
			return jQuery(this).find('rs-slide').length;
		},


		// METHODE JUMP TO SLIDE
		revshowslide: function(slide) {
			return this.each(function() {
				if (_R[this.id] !== undefined && slide !== undefined) _R.callingNewSlide(this.id, "to" + (slide - 1));
			});
		},
		revcallslidewithid: function(slide) {
			return this.each(function() {
				if (_R[this.id] !== undefined) _R.callingNewSlide(this.id, slide, _R[this.id].sliderType === "carousel");
			});
		},

	});


	//////////////////////////////////////////////////////////////
	// -	REVOLUTION FUNCTION EXTENSIONS FOR GLOBAL USAGE  -  //
	//////////////////////////////////////////////////////////////
	_R = jQuery.fn.revolution;

	jQuery.extend(true, _R, {
		isNumeric: function(n) {
			return !isNaN(parseFloat(n)) && isFinite(n);
		},
		trim: function(t) {
			return t !== undefined && t !== null && typeof t == "string" ? t.trim() : t;
		},

		setCookie: function(cname, cvalue, exdays) {
			var d = new Date();
			d.setTime(d.getTime() + (exdays * 60 * 60 * 1000));
			var expires = "expires=" + d.toUTCString();
			document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		},

		getCookie: function(cname) {
			var name = cname + "=";
			var ca = document.cookie.split(';');
			for (var i = 0; i < ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0) == ' ') {
					c = c.substring(1);
				}
				if (c.indexOf(name) == 0) {
					return decodeURIComponent(c.substring(name.length, c.length));
				}
			}
			return "";
		},

		mesureScrollBar: function() {
			_R.mesuredScrollBarDone = true;
			requestAnimationFrame(function() {
				// Create the measurement node
				var scrollDiv = document.createElement("div");
				scrollDiv.className = "RSscrollbar-measure";
				document.body.appendChild(scrollDiv);

				// Get the scrollbar width
				_R.mesuredScrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;


				// Delete the DIV
				document.body.removeChild(scrollDiv);
			});
		},

		pageHandler: function(id) {
			_R.globalListener = true;
			_R.window = jQuery(window);
			_R.document = jQuery(document);
			_R.RS_toInit = {};
			_R.RS_prioList = [];
			_R.RS_swapping = [];
			_R.RS_swapList = {};
			if (window.isSafari11 === undefined) window.isSafari11 = _R.isSafari11();

			if (_R.ISM) {
				window.addEventListener("orientationchange", function() {
					_R.getWindowDimension(false, true);
					setTimeout(function() {
						_R.getWindowDimension(true, true);
					}, 400)
				});
				window.addEventListener("resize", _R.getWindowDimension);
				tpGS.gsap.delayedCall(3, function(){
					window.removeEventListener("resize", _R.getWindowDimension);
				});
			} else {
				// removed old touchscreen check as sliders were not resizing on touchscreen desktops
				window.addEventListener("resize", _R.getWindowDimension);
			}

			_R.getWindowDimension(false);
			_R.stickySupported = false;

			if (window.RSBrowser !== "IE") _R.stickySupported = true;

			_R.checkParrentOverflows(id);

			var rsmodules = _R.getByTag(document, 'RS-MODULE');
			for (var i in rsmodules)
				if (rsmodules.hasOwnProperty(i)) {
					_R.RS_toInit[rsmodules[i].id] = false;
					_R.RS_prioList.push(rsmodules[i].id);
				}
			_R.nextSlider = id;
			_R.RS_prioListFirstInit = true;

			if (_R.hasNavClickListener === undefined) {
				_R.document.on((_R.is_mobile() ? 'touchstart' : 'mouseenter'), '.tparrows, .tp-bullets, .tp-bullet, .tp-tab, .tp-thumb, .tp-thumbs, .tp-tabs, .tp-rightarrow, .tp-leftarrow', function(e) {
					this.classList.add('rs-touchhover');
				});

				_R.document.on((_R.is_mobile() ? 'touchend' : 'mouseleave'), '.tparrows, .tp-bullets, .tp-bullet, .tp-tab, .tp-thumb, .tp-tabs,  .tp-rightarrow, .tp-leftarrow', function(e) {
					var el = this;
					requestAnimationFrame(function() {
						el.classList.remove('rs-touchhover');
					});
				});
				_R.hasNavClickListener = true;
			}

			window.addEventListener('unload', function(event) {
				for (var i in _R.RS_toInit) {
					if (!_R.hasOwnProperty(i)) continue;

					for (var j in _R[i].sbgs) {
						if (!_R[id].sbgs.hasOwnProperty(j)) continue;
						var BG = _R[id].sbgs[j];

						_R.destroyCanvas(BG.canvas);
						_R.destroyCanvas(BG.shadowCanvas);
						if (BG.three) _R.destroyCanvas(BG.three.canvas);
						_R.destroyCanvas(BG.patternImageCanvas);
						_R.destroyCanvas(BG.fmShadow);
						_R.destroyCanvas(BG.help_canvas);
					}

					_R.destroyCanvas(_R[i].createPattern);
				}

				var canvases = document.querySelectorAll('canvas');
				for (var i in canvases) {
					if (!canvases.hasOwnProperty(i)) continue;
					_R.destroyCanvas(canvases[i]);
				}
			});

		},

		destroyCanvas: function(canvas) {
			if (!canvas) return;
			canvas.width = canvas.height = 0;
			canvas.remove();
			canvas = null;
		},

		checkParrentOverflows: function(id) {
			window.requestAnimationFrame(function() {
				var element = _R[id].cpar[0];
				while (element.parentNode && _R.stickySupported !== false) {
					if (element.tagName !== "RS-MODULE-WRAP" && element.tagName !== "RS-FULLWIDTH-WRAP" && element.tagName !== "RS-MODULE-WRAP" && element.className.indexOf("wp-block-themepunch-revslider") === -1) {
						var s = window.getComputedStyle(element);
						_R.stickySupported = s.overflow !== "hidden" && s.overflowX !== "hidden" && s.overflowY !== "hidden";
					}
					element = element.parentNode;
				}
			});
		},

		initNextRevslider: function(id) {
			if (_R.RS_prioList[0] === id && _R.RS_toInit[id] === false) {
				_R.RS_toInit[id] = "waiting";
				initSlider(id);
				setTimeout(function() { _R.initNextRevslider(id); }, 19);
			} else
			if (_R.RS_prioList[0] === id && _R.RS_toInit[id] === "waiting") setTimeout(function() { _R.initNextRevslider(id); }, 19);
			else
			if (_R.RS_prioList[0] === id && _R.RS_toInit[id] === true) {
				_R.RS_prioList.shift();
				if (_R.RS_prioList.length !== 0) setTimeout(function() { _R.initNextRevslider(id); }, 19);
			} else
			if (_R.RS_prioList[0] !== id && _R.RS_toInit[id] === false) setTimeout(function() { _R.initNextRevslider(id); }, 19);
			else
			if (_R.RS_prioList.length === 0 && _R.RS_toInit[id] === true) initSlider(id);

		},

		scrollTicker: function(id) {
			if (_R.scrollTickerAdded != true) {
				_R.slidersToScroll = [];
				_R.scrollTickerAdded = true;
				if (_R.ISM) {
					tpGS.gsap.ticker.fps(150);
					tpGS.gsap.ticker.add(function() {
						_R.generalObserver();
					});
				} else
					document.addEventListener('scroll', function(e) {
						if (_R.scrollRaF === undefined) _R.scrollRaF = requestAnimationFrame(_R.generalObserver.bind(this, true));
					}, { passive: true });
			}
			_R.slidersToScroll.push(id);
			_R.generalObserver(_R.ISM);
		},

		generalObserver: function(fromMouse, ignoreLayers) {
			if (_R.scrollRaF) _R.scrollRaF = cancelAnimationFrame(_R.scrollRaF);
			_R.lastwindowheight = _R.lastwindowheight || _R.winH;
			_R.scrollY = window.scrollY;
			for (var i in _R.slidersToScroll) {
				if (!_R.slidersToScroll.hasOwnProperty(i)) continue;
				_R.scrollHandling(_R.slidersToScroll[i], fromMouse, undefined, ignoreLayers);
			}
		},

		wrapObserver: {
			targets: [],
			init: function(callback) {
				var limit = 1,
					lastCall = 0,
					count = 0,
					observe = observeTargets.bind(_R.wrapObserver);
				observeTargets();

				function observeTargets() {
					count++;
					requestAnimationFrame(observe);
					if (count - lastCall < 30 / limit) return;
					lastCall = count;
					for (var i = 0; i < _R.wrapObserver.targets.length; i++) {
						if (!_R.wrapObserver.targets.hasOwnProperty(i)) continue;
						var target = _R.wrapObserver.targets[i],
						box = target.elem.getBoundingClientRect();

						if ((target.lw !== box.width || target.lh !== box.height) && box.width !== 0) {
							if (target.callback) {
								target.callback.pause();
								target.callback.kill();
								target.callback = null;
							}
							target.callback = tpGS.gsap.to({}, { duration: 0.2, onComplete: callback.bind(window, target.elem, target.id) });
						}
						target.lw = box.width;
						target.lh = box.height;
					}
				}
			},
			observe: function(elem, id) {
				var elem = elem.getBoundingClientRect ? elem : elem[0].getBoundingClientRect ? elem[0] : "";
				if (elem === "") return;
				var box = elem.getBoundingClientRect();
				_R.wrapObserver.targets.push({
					elem: elem,
					id: id,
					lw: box.width,
					lh: box.height
				});
			}
		},


		enterViewPort: function(id, force) {

			// START FIRST SLIDE IF NOT YET STARTED AND VP ENTERED
			if (_R[id].started !== true) {
				_R[id].started = true;
				_R.lazyLoadAllSlides(id);
				_R[id].c.trigger('revolution.slide.firstrun');
				// START THE SLIDER
				setTimeout(function() {
					swapSlide(id);
					if (_R[id].sliderType !== "hero" && _R.manageNavigation && _R[id].navigation.use && _R[id].navigation.createNavigationDone === true) _R.manageNavigation(id);
					// START COUNTDOWN
					if (_R[id].slideamount > 1) countDown(id);
					setTimeout(function() {
						if (_R[id] === undefined) return;
						_R[id].revolutionSlideOnLoaded = true;
						_R[id].c.trigger('revolution.slide.onloaded');
					}, 50);
				}, _R[id].startDelay);
				_R[id].startDelay = 0;
				window.requestAnimationFrame(function() { hideSliderUnder(id); });

			} else {
				// START COUNTER IF VP ENTERED, AND COUNTDOWN WAS NOT ON YET
				if (_R[id].waitForCountDown) {
					countDown(id);
					_R[id].waitForCountDown = false;
				}
				if (_R[id].sliderlaststatus == "playing" || _R[id].sliderlaststatus == undefined) _R[id].c.trigger("starttimer");
				if (_R[id].lastplayedvideos != undefined && _R[id].lastplayedvideos.length > 0)
					jQuery.each(_R[id].lastplayedvideos, function(i, _nc) {
						_R.playVideo(_nc, id);
					});
			}
		},

		leaveViewPort: function(id) {
			_R[id].sliderlaststatus = _R[id].sliderstatus;
			_R[id].c.trigger("stoptimer");
			if (_R[id].playingvideos != undefined && _R[id].playingvideos.length > 0) {
				_R[id].lastplayedvideos = jQuery.extend(true, [], _R[id].playingvideos);
				if (_R[id].playingvideos)
					jQuery.each(_R[id].playingvideos, function(i, _nc) {
						_R[id].leaveViewPortBasedStop = true;
						if (_R.stopVideo) _R.stopVideo(_nc, id);
					});
			}
		},


		//	-	SET POST OF SCROLL PARALLAX	-
		scrollHandling: function(id, fromMouse, speedoverwrite, ignorelayers) {
			if (_R[id] === undefined) return;

			//if (_R.lastscrolltop==_R.scrollY && !_R[id].duringslidechange && !fromMouse) return false;

			var b = _R[id].topc !== undefined ? _R[id].topc[0].getBoundingClientRect() : _R[id].canv.height === 0 ? _R[id].cpar[0].getBoundingClientRect() : _R[id].c[0].getBoundingClientRect();
			b.hheight = b.height === 0 ? _R[id].canv.height === 0 ? _R[id].module.height : _R[id].canv.height : b.height;

			_R[id].scrollproc = b.top < 0 || b.hheight > _R.lastwindowheight && b.top < _R.lastwindowheight ? b.top / b.hheight : b.bottom > _R.lastwindowheight ? (b.bottom - _R.lastwindowheight) / b.hheight : 0;
			var area = Math.max(0, 1 - Math.abs(_R[id].scrollproc));

			if (_R[id].viewPort.enable) {
				// enter / leave Viewport
				if ((_R[id].viewPort.vaType[_R[id].level] === "%" && (_R[id].viewPort.visible_area[_R[id].level] <= area || (area > 0 && area <= 1 && _R[id].sbtimeline.fixed))) ||
					(_R[id].viewPort.vaType[_R[id].level] === "px" && (
						(b.top <= 0 && b.bottom >= _R.lastwindowheight) ||
						(b.top >= 0 && b.bottom <= _R.lastwindowheight) ||
						(b.top >= 0 && b.top < _R.lastwindowheight - _R[id].viewPort.visible_area[_R[id].level]) ||
						(b.bottom >= _R[id].viewPort.visible_area[_R[id].level] && b.bottom < _R.lastwindowheight)))) {
					if (!_R[id].inviewport) {
						_R[id].inviewport = true;
						_R.enterViewPort(id, true);
						_R[id].c.trigger('enterviewport');
					}
				} else {
					if (_R[id].inviewport) {
						_R[id].inviewport = false;
						_R.leaveViewPort(id);
						_R[id].c.trigger('leftviewport');
					}
				}
			}

			// NOT VISIBLE, NO MORE CHECK NEEDED
			if (!_R[id].inviewport) return;
			if (_R.callBackHandling) _R.callBackHandling(id, "parallax", "start");
			requestAnimationFrame(function() { if (_R[id].sliderLayout === "fullscreen") _R.getFullscreenOffsets(id); });
			_R.parallaxProcesses(id, b, ignorelayers, speedoverwrite);
			if (_R.callBackHandling) _R.callBackHandling(id, "parallax", "end");
		},
		clone: function(inObject, deep) {
			if (deep === undefined && inObject === undefined) return {};

			function cloneIt(obj, deep) {
				var outObject = Array.isArray(obj) ? [] : {};
				for (var key in obj) {
					if (!obj.hasOwnProperty(key)) continue;
					if (obj[key] !== undefined && typeof obj[key] === "object" && deep) {
						outObject[key] = cloneIt(obj[key], true);
					} else if (obj[key] !== undefined) {
						outObject[key] = obj[key];
					}
				}
				return outObject;
			}
			return cloneIt(inObject, deep);
		},

		closest: function(el, fn) {
			return el && (fn(el) ? el : _R.closest(el.parentNode, fn));
		},
		closestNode: function(el, node) {
			return _R.closest(el, function(el) {
				return el.nodeName === node;
			});
		},
		closestClass: function(el, cs) {
			return _R.closest(el, function(el) {
				return (' ' + el.className + ' ').indexOf(' ' + cs + ' ') >= 0;
			});
		},
		getWinH: function(id) {
			return _R[id].ignoreHeightChange ? _R.mobileWinH : _R.winH;
		},

		getWindowDimension: function(e, oriChanged) {

			if (e === false) {
				_R.rAfScrollbar = "skip";
				_R.winWAll = window.innerWidth;
				_R.winWSbar = document.documentElement.clientWidth;
				if (_R.ISM) {
					_R.zoom = oriChanged ? 1 : _R.winWSbar / _R.winWAll;

					_R.winW = _R.zoom !== 1 ? _R.winWSbar * _R.zoom : Math.min(_R.winWAll, _R.winWSbar);
					_R.winH = _R.zoom !== 1 ? window.innerHeight * _R.zoom : window.innerHeight;
					if (oriChanged && window.visualViewport) {
						_R.winH *= window.visualViewport.scale;
						_R.winWAll *= window.visualViewport.scale;
					}
					_R.scrollBarWidth = 0;
				} else {
					// MODAL SCROLLBAR CHECK !!
					if (_R.isModalOpen && _R.openModalId !== undefined && _R[_R.openModalId] !== undefined && _R[_R.openModalId].canv.height > _R.winH) {
						_R.scrollBarWidth = _R.mesuredScrollbarWidth;
					} else _R.scrollBarWidth = _R.winWAll - _R.winWSbar;
					_R.winW = Math.min(_R.winWAll, _R.winWSbar);
					_R.winH = window.innerHeight;
				}

				if (_R.ISM && _R.winH > 125)
					if (_R.lastwindowheight !== undefined && Math.abs(_R.lastwindowheight - _R.winH) < 125) _R.mobileWinH = _R.lastwindowheight;
					else _R.mobileWinH = _R.winH;
			} else
				clearTimeout(_R.windowDimenstionDelay);
			_R.windowDimenstionDelay = setTimeout(function() {
				_R.rAfScrollbar = undefined;
				_R.winWAll = window.innerWidth;
				_R.winWSbar = document.documentElement.clientWidth;
				if (_R.ISM) {
					_R.zoom = oriChanged ? 1 : _R.winWSbar / _R.winWAll;
					_R.RS_px_ratio = window.devicePixelRatio || window.screen.availWidth / document.documentElement.clientWidth;
					_R.winW = _R.zoom !== 1 ? _R.winWSbar * _R.zoom : Math.min(_R.winWAll, _R.winWSbar);
					_R.winH = _R.zoom !== 1 ? window.innerHeight * _R.zoom : window.innerHeight;
					if (oriChanged && window.visualViewport) {
						_R.winH *= window.visualViewport.scale;
						_R.winWAll *= window.visualViewport.scale;
					}
					_R.scrollBarWidth = 0;
					if (oriChanged) tpGS.gsap.delayedCall(0.1, function() { _R.getWindowDimension() });
				} else {
					// MODAL SCROLLBAR CHECK !!
					if (_R.isModalOpen && _R.openModalId !== undefined && _R[_R.openModalId] !== undefined && _R[_R.openModalId].canv.height > _R.winH) {
						_R.scrollBarWidth = _R.mesuredScrollbarWidth;
					} else _R.scrollBarWidth = _R.winWAll - _R.winWSbar;
					_R.winW = Math.min(_R.winWAll, _R.winWSbar);
					_R.winH = window.innerHeight;
				}

				if (_R.ISM && _R.winH > 125)
					if (_R.lastwindowheight !== undefined && Math.abs(_R.lastwindowheight - _R.winH) < 125) _R.mobileWinH = _R.lastwindowheight;
					else _R.mobileWinH = _R.winH;
				if (e !== false) _R.document.trigger('updateContainerSizes');
			}, 100);
		},

		aC: function(e, c) {
			if (!e) return;
			if (e.classList && e.classList.add) {
				e.classList.add("" + c);
			} else {
				// only needed for pre-Edge IE
				jQuery(e).addClass(c);
			}
		},

		rC: function(e, c) {
			if (!e) return;
			if (e.classList && e.classList.remove) {
				e.classList.remove("" + c);
			} else {
				// only needed for pre-Edge IE
				jQuery(e).removeClass(c);
			}
		},

		sA: function(e, a, v) {
			if (e && e.setAttribute) {
				e.setAttribute('data-' + a, v);
			}
		},
		gA: function(e, a, d) {
			return e === undefined ? undefined : (e.hasAttribute && e.hasAttribute('data-' + a) && e.getAttribute('data-' + a) !== undefined && e.getAttribute('data-' + a) !== null) ? e.getAttribute('data-' + a) : d !== undefined ? d : undefined;
		},

		rA: function(e, a) {
			if (e && e.removeAttribute) {
				e.removeAttribute('data-' + a);
			}
		},

		iWA: function(id, slide) {
			return _R[id].justifyCarousel ? slide === "static" ? _R[id].carousel.wrapwidth : _R[id].carousel.slide_widths[slide !== undefined ? slide : _R[id].carousel.focused] : _R[id].gridwidth[_R[id].level];
		},
		iHE: function(id, slide) {
			return _R[id].useFullScreenHeight ? _R[id].canv.height : Math.max(_R[id].currentRowsHeight, _R[id].gridheight[_R[id].level]);
		},
		updateFixedScrollTimes: function(id) {
			if (_R[id].sbtimeline.set === true && _R[id].sbtimeline.fixed === true && _R[id].sliderLayout !== "auto") {
				_R[id].sbtimeline.rest = _R[id].duration - _R[id].sbtimeline.fixEnd;
				_R[id].sbtimeline.time = _R[id].duration - (_R[id].sbtimeline.fixStart + _R[id].sbtimeline.rest);
				_R[id].sbtimeline.extended = _R[id].sbtimeline.time / 10;
			}
		},

		addSafariFix: function(id) {
			if (window.isSafari11 !== true) return;
			if (_R[id].safari3dFix !== true) {
				_R[id].safari3dFix = true;
				_R[id].c[0].className += " safarifix";
			}
		},

		openModalAPI: function(modal, modalslide, url, cover, id, event) {
			// RS_60_MODALS - > Stores the loaded and once opened MODALS
			// RS_60_MODAL_API_CALLS -> Stores the MODALS to Load


			if ((window.RS_60_MODALS === undefined || jQuery.inArray(modal, window.RS_60_MODALS) == -1) && (window.RS_60_MODAL_API_CALLS === undefined || jQuery.inArray(modal, window.RS_60_MODAL_API_CALLS) == -1)) {

				// DONT LOAD TWICE THE SAME MODAL !
				window.RS_60_MODAL_API_CALLS = window.RS_60_MODAL_API_CALLS || [];
				window.RS_60_MODAL_API_CALLS.push(modal);
				//SHOW MODAL COVER
				if (cover) _R.showModalCover(id, event, "show");

				// GET SLIDER PER AJAX
				var data = { action: 'revslider_ajax_call_front', client_action: 'get_slider_html', alias: modal, usage: "modal" };
				jQuery.ajax({
					type: 'post',
					url: url,
					dataType: 'json',
					data: data,
					success: function(ret, textStatus, XMLHttpRequest) {

						if (ret !== null && ret.success == true) {
							var i;

							// AFTER LOAD UPDATE THE GENERAL WAITING LIST, MAYBE ADDON NEED TO BE LOADED AS WELL
							if (ret.waiting !== undefined)
								for (i in ret.waiting)
									if (jQuery.inArray(ret.waiting[i], RS_MODULES.waiting) == -1) {
										RS_MODULES.waiting.push(ret.waiting[i]);
										window.RS_MODULES.minimal = false;
									}

									// ADD SCRIPTS TO LOAD
							if (ret.toload !== undefined) {
								var scripts = "";
								RS_MODULES = RS_MODULES || {};
								RS_MODULES.requestedScripts = [];
								for (i in ret.toload) {
									if (!ret.toload.hasOwnProperty(i)) continue;
									if (RS_MODULES == undefined || RS_MODULES[i] == undefined || RS_MODULES[i].loaded !== true) {
										// ADD SCRIPT IN HEADER
										if (jQuery.inArray(i, RS_MODULES.requestedScripts) === -1) {
											RS_MODULES.requestedScripts.push(i);
											scripts += ret.toload[i];
										}
									}
								}
								if (scripts !== "") jQuery('body').append(scripts);
							}
							// ADD MARKUP, CSS and INIT SCRIPT TO THE DOM
							jQuery('body').append(ret.data);

							if (cover) _R.showModalCover(id, event, "hide");

							// CHECK IF MODAL API LISTENER ADDED ALREADY, IF SO, OPEN MODAL, OTHER WAY UNTIL MODAL OPEN API FUNCTION EXTST
							if (_R[modal] !== undefined && _R[modal].openModalApiListener)
								jQuery.fn.revolution.document.trigger('RS_OPENMODAL_' + modal, modalslide);
							else
								jQuery(document).on('RS_MODALOPENLISTENER_' + modal, function() { //WAIT for a kick back and start first after that
									jQuery.fn.revolution.document.trigger('RS_OPENMODAL_' + modal, modalslide);
								});
						} else {
							if (cover) _R.showModalCover(id, event, "hide");
						}
					},
					error: function(e) {
						if (cover) _R.showModalCover(id, event, "hide");
						console.log("Modal Can not be Loaded");
						console.log(e);
					}
				});
			} else
			if (jQuery.inArray(modal, window.RS_60_MODALS) >= 0) jQuery.fn.revolution.document.trigger('RS_OPENMODAL_' + modal, modalslide);
		},

		showModalCover: function(id, _, mode) {
			switch (mode) {
				case "show":
					var spinner;
					if (_.spin !== undefined && _.spin !== "off") spinner = _R.buildSpinner(id, "spinner" + _.spin, _.spinc, "modalspinner");
					if (_.bg !== undefined && _.bg !== false && _.bg !== "false" && _.bg !== "transparent") {
						var m = jQuery('<rs-modal-cover data-alias="' + _.alias + '" data-rid="' + id + '" id="' + id + '_modal_bg" style="display:none;opacity:0;background:' + _.bg + '"></rs-modal-cover>');
						jQuery('body').append(m);
						_.speed = parseFloat(_.speed);
						_.speed = _.speed > 200 ? _.speed / 1000 : _.speed;
						_.speed = Math.max(Math.min(3, _.speed), 0.3);
						tpGS.gsap.to(m, _.speed, { display: "block", opacity: 1, ease: "power3.inOut" });
						_R.isModalOpen = true;
						if (spinner !== undefined) m.append(spinner);
					} else
					if (spinner !== undefined) _R[id].c.append(spinner);
					break;
				case "hide":
					var m = jQuery('rs-modal-cover[data-alias="' + _.alias + '"] .modalspinner');
					if (m !== undefined && m.length > 0)
						m.remove();
					else
					if (id !== undefined) _R[id].c.find('.modalspinner').remove();
					break;
			}
		},

		revModal: function(id, _) {

			if (id === undefined || _R[id] === undefined) return;
			if (_R[id].modal.closeProtection === "clicked") return;
			if (_R[id].modal.closeProtection === true) {
				_R[id].modal.closeProtection === "clicked";
				setTimeout(function() {
					_R[id].modal.closeProtection = false;
					_R.revModal(id, _);
				}, 750);
				return;
			}

			switch (_.mode) {
				case "show":
					if (_R[id].modal.isLive === true) return;
					if (_R.anyModalclosing === true) return;


					_R[id].modal.isLive = true;
					_.slide = _.slide === undefined ? "to0" : _.slide;
					if (_R[id].modal.bodyclass !== undefined && _R[id].modal.bodyclass.length >= 0) document.body.classList.add(_R[id].modal.bodyclass);
					_R[id].modal.bg.attr('data-rid', id);

					tpGS.gsap.to(_R[id].modal.bg, _R[id].modal.coverSpeed, { display: "block", opacity: 1, ease: "power3.inOut" });
					tpGS.gsap.set(_R[id].modal.c, { display: (_R[id].sliderLayout === "auto" ? "inline-block" : "block"), opacity: 0 });
					_R[id].cpar.removeClass("hideallscrollbars");
					tpGS.gsap.set(_R[id].cpar, { display: "block", opacity: 1 });
					var u = { a: 0 }
					_R.isModalOpen = true;
					_R[id].clearModalBG = true;
					tpGS.gsap.fromTo(u, _R[id].modal.coverSpeed / 5, { a: 0 }, {
						a: 10,
						ease: "power3.inOut",
						onComplete: function() {
							_R.openModalId = id;
							if (!_R[id].sliderisrunning) {
								if (_.slide !== "to0") _R[id].startWithSlideKey = _.slide;
								runSlider(id);
							} else _R.callingNewSlide(id, _.slide);
						}
					});

					setTimeout(function() {
						tpGS.gsap.fromTo([_R[id].modal.c], 0.01, { opacity: 0 }, { opacity: 1, delay: _R[id].modal.coverSpeed / 4, ease: "power3.inOut", onComplete: function() {} });
						window.overscrollhistory = document.body.style.overflow;
						document.body.style.overflow = "hidden";
						_R.getWindowDimension();
					}, 250);
					break;
				case "close":
					if (_R.anyModalclosing === true) return;
					_R.anyModalclosing = true;
					_R.openModalId = undefined;
					hideSlide(id);
					document.body.style.overflow = window.overscrollhistory;
					_R[id].cpar.addClass("hideallscrollbars");
					if (_R[id].modal.bodyclass !== undefined && _R[id].modal.bodyclass.length >= 0) document.body.classList.remove(_R[id].modal.bodyclass);
					tpGS.gsap.to(_R[id].modal.bg, _R[id].modal.coverSpeed, { display: "none", opacity: 0, ease: "power3.inOut" });
					tpGS.gsap.to(_R[id].modal.c, _R[id].modal.coverSpeed / 6.5, {
						display: "none",
						delay: _R[id].modal.coverSpeed / 4,
						opacity: 0,
						onComplete: function() {
							tpGS.gsap.set(_R[id].cpar, { display: "none", opacity: 0 });
							_R.document.trigger("revolution.all.resize");
							_R.document.trigger("revolution.modal.close", [_R[id].modal]); // JM - 30.04.21 - for Newsletter Block
							_R.getWindowDimension();
							_R.isModalOpen = false;
						}
					});
					_R[id].modal.closeProtection = true;
					clearTimeout(_R[id].modal.closeTimer);
					_R[id].modal.closeTimer = setTimeout(function() {
						_R.anyModalclosing = false;
						_R[id].modal.isLive = false;
						_R[id].modal.closeProtection = false;
					}, Math.max(750, _R[id].modal.coverSpeed * 1020));
					break;
				case "init":

					window.RS_60_MODALS = window.RS_60_MODALS === undefined ? [] : window.RS_60_MODALS;
					if (jQuery.inArray(_R[id].modal.alias, window.RS_60_MODALS) === -1) window.RS_60_MODALS.push(_R[id].modal.alias);
					if (_R[id].modal.listener === undefined) {
						_R[id].modal.c = jQuery('#' + id + '_modal');
						if (_R[id].modal.cover === false || _R[id].modal.cover === "false") _R[id].modal.coverColor = "transparent";
						_R[id].modal.bg = jQuery('rs-modal-cover[data-alias="' + _.alias + '"]');
						if (_R[id].modal.bg === undefined || _R[id].modal.bg.length === 0) {
							_R[id].modal.bg = jQuery('<rs-modal-cover style="display:none;opacity:0;background:' + _R[id].modal.coverColor + '" data-rid="' + id + '" id="' + id + '_modal_bg"></rs-modal-cover>');

							if (_R[id].sliderLayout === "auto" && _R[id].modal.cover)
								jQuery('body').append(_R[id].modal.bg);
							else
								_R[id].modal.c.append(_R[id].modal.bg);
						} else _R[id].modal.bg.attr('data-rid', id);

						_R[id].modal.c[0].className += "rs-modal-" + _R[id].sliderLayout;
						_R[id].modal.calibration = {
							left: (_R[id].sliderLayout === "auto" ? _R[id].modal.horizontal === "center" ? "50%" : _R[id].modal.horizontal === "left" ? "0px" : "auto" : "0px"),
							right: (_R[id].sliderLayout === "auto" ? _R[id].modal.horizontal === "center" ? "auto" : _R[id].modal.horizontal === "left" ? "auto" : "0px" : "0px"),
							top: (_R[id].sliderLayout === "auto" || _R[id].sliderLayout === "fullwidth" ? _R[id].modal.vertical === "middle" ? "50%" : _R[id].modal.vertical === "top" ? "0px" : "auto" : "0px"),
							bottom: (_R[id].sliderLayout === "auto" || _R[id].sliderLayout === "fullwidth" ? _R[id].modal.vertical === "middle" ? "auto" : _R[id].modal.vertical === "top" ? "auto" : "0px" : "0px"),
							y: (_R[id].sliderLayout === "auto" || _R[id].sliderLayout === "fullwidth" ? _R[id].modal.vertical === "middle" ? "-50%" : 0 : 0),
							x: (_R[id].sliderLayout === "auto" ? _R[id].modal.horizontal === "center" ? "-50%" : 0 : 0)
						};
						if (_R[id].modal.calibration.y === "-50%") _R[id].modal.calibration.filter = "blur(0px)";
						tpGS.gsap.set(_R[id].modal.c, _R[id].sliderLayout === "auto" || _R[id].sliderLayout === "fullscreen" ? jQuery.extend(true, _R[id].modal.calibration, { opacity: 0, display: "none" }) : { opacity: 0, display: "none" });
						if (_R[id].sliderLayout === "fullwidth") tpGS.gsap.set(_R[id].modal.c.find('rs-module-wrap'), _R[id].modal.calibration);


						// Listen to Open Modal (i.e due API)
						_R.document.on('RS_OPENMODAL_' + _R[id].modal.alias, function(e, s) {
							_R[id].initEnded = true;
							_R.revModal(id, { mode: "show", slide: s });
						});

						// Kick back and set up API Based Initialisation
						_R[_R[id].modal.alias] = _R[_R[id].modal.alias] || {};
						_R[_R[id].modal.alias].openModalApiListener = true;
						_R.document.trigger('RS_MODALOPENLISTENER_' + _R[id].modal.alias);


						_R.document.on('click', 'rs-modal-cover', function() { _R.revModal(_R.gA(this, 'rid'), { mode: "close" }); });
						_R[id].modal.listener = true;

						// INIT TRIGGER LISTENERS
						if (_R[id].modal.trigger !== undefined) {
							var trs = _R[id].modal.trigger.split(";"),
								u, s;
							_R[id].modal.trigger = {};
							for (u in trs) {
								if (!trs.hasOwnProperty(u)) continue;
								s = trs[u].split(":");
								switch (s[0]) {
									case "t":
										_R[id].modal.trigger.time = parseInt(s[1], 0);
										break;
									case "s":
										_R[id].modal.trigger.scroll = s[1];
										break;
									case "so":
										_R[id].modal.trigger.scrollo = parseInt(s[1], 0);
										break;
									case "e":
										_R[id].modal.trigger.event = s[1];
										break;
									case "ha":
										_R[id].modal.trigger.hash = s[1];
										break;
									case "co":
										_R[id].modal.trigger.cookie = s[1];
										break;
								}
							}

							// CHECK 1 TIME PER SESSION OPTION
							var canrun = true;
							if (_R[id].modal.trigger.cookie !== undefined)
								canrun = _R.getCookie(_R[id].modal.alias + '_modal_one_time') !== 'true';
							else
							if (_R.getCookie(_R[id].modal.alias + '_modal_one_time') == 'true') _R.setCookie(_R[id].modal.alias + '_modal_one_time', false, 10);


							// IF NO ISSUE, WE CAN CALL PARTS
							if (canrun) {
								// START POPUP AFTER TIME "x"
								if (_R[id].modal.trigger.time !== undefined && _R[id].modal.trigger.time !== 0) {
									if (_R[id].modal.trigger.cookie !== undefined) _R.setCookie(_R[id].modal.alias + '_modal_one_time', true, _R[id].modal.trigger.cookie);
									setTimeout(function() { _R.document.trigger('RS_OPENMODAL_' + _R[id].modal.alias); }, _R[id].modal.trigger.time);
								}

								// START POP UP AFTER SCROLL OFFSET / ELEMENT
								if (_R[id].modal.trigger.scrollo !== undefined || _R[id].modal.trigger.scroll !== undefined) {
									if (_R[id].modal.trigger.scroll !== undefined && jQuery(_R[id].modal.trigger.scroll)[0] !== undefined) _R[id].modal.trigger.scroll = jQuery(_R[id].modal.trigger.scroll)[0];
									var startModalOnScrollListener = function() {
										if (_R[id].modal.trigger.scroll !== undefined) var rect = _R[id].modal.trigger.scroll.getBoundingClientRect();
										if ((_R[id].modal.trigger.scroll !== undefined && Math.abs((rect.top + (rect.bottom - rect.top) / 2) - _R.getWinH(id) / 2) < 50) || (_R[id].modal.trigger.scrollo !== undefined && Math.abs(_R[id].modal.trigger.scrollo - (_R.scrollY !== undefined ? _R.scrollY : window.scrollY)) < 100)) {
											_R.document.trigger('RS_OPENMODAL_' + _R[id].modal.alias);
											if (_R[id].modal.trigger.cookie !== undefined) _R.setCookie(_R[id].modal.alias + '_modal_one_time', true, _R[id].modal.trigger.cookie);
											document.removeEventListener('scroll', startModalOnScrollListener);
										}
									}
									document.addEventListener('scroll', startModalOnScrollListener, { id: id, passive: true });
								}
							}

							// START POP UP ON EVENT
							if (_R[id].modal.trigger.event !== undefined) _R.document.on(_R[id].modal.trigger.event, function() { _R.document.trigger('RS_OPENMODAL_' + _R[id].modal.alias); });

							// IF HASH DEPENDENT
							if (_R[id].modal.trigger.hash == 't' && window.location.hash.substring(1) == _R[id].modal.alias) _R.document.trigger('RS_OPENMODAL_' + _R[id].modal.alias)
						}
					}
					break;
			}
		},
		smartConvertDivs: function(a) {
			var ret = "";
			if (typeof a === "string" && a.indexOf("#") >= 0) {
				var b = a.split(","),
					l = b.length - 1;
				for (var j in b) {
					if (typeof b[j] === "string" && b[j][0] === "#")
						ret = ret + ((b[j][1] / b[j][3])) * 100 + "%" + (j < l ? "," : "");
					else
						ret = ret + b[j] + (j < l ? "," : "");
				}
			} else ret = a;
			return ret;
		},
		revToResp: function(_, dim, def, div) {
			_ = _ === undefined ? def : _;
			if (_ === undefined) return;
			div = div === undefined ? "," : div;
			if (!(typeof _ === 'boolean' || (typeof _ == "object" && !Array.isArray(_)))) {
				try { _ = _.replace(/[[\]]/g, '').replace(/\'/g, '').split(div); } catch (e) {}
				_ = Array.isArray(_) ? _ : [_];
				while (_.length < dim) _[_.length] = _[_.length - 1];
			}
			return _;
		},


		// LOAD THE IMAGES OF THE PREDEFINED CONTAINER
		loadImages: function(container, id, prio, staticlayer) {

			if (container === undefined || container.length === 0) return;
			var containers = [];

			if (Array.isArray(container)) {
				for (var i in container) {
					if (!container.hasOwnProperty(i) || container[i] === undefined) continue;
					containers.push(container[i]);
				}
			} else
				containers.push(container);

			for (var j in containers) {
				if (!containers.hasOwnProperty(j)) continue;
				var imgs = containers[j].querySelectorAll('img, rs-sbg, .rs-svg');

				for (var i in imgs) {
					if (!imgs.hasOwnProperty(i)) continue;
					// SMUSH PROTECTION
					if (imgs[i] !== undefined && imgs[i].dataset !== undefined && imgs[i].dataset.src !== undefined && imgs[i].dataset.src.indexOf('dummy.png') >= 0 && imgs[i].src.indexOf("data") >= 0) delete imgs[i].dataset.src;
					var lazy = deliverLazy(imgs[i], undefined, id),
						src = lazy !== undefined ? lazy : _R.gA(imgs[i], "svg_src") != undefined ? _R.gA(imgs[i], "svg_src") : imgs[i].src === undefined ? jQuery(imgs[i]).data('src') : imgs[i].src,
						type = _R.gA(imgs[i], "svg_src") != undefined ? "svg" : "img";
					//_R[id].loadqueue.filter(x=>x.src === src)
					if (src !== undefined && _R[id].loadqueue !== undefined && _R[id].loadqueue.filter(function(x) { return x.src === src; }).length == 0) _R[id].loadqueue.push({ src: src, img: imgs[i], index: i, starttoload: jQuery.now(), type: type || "img", prio: prio, progress: (imgs[i].complete && src === imgs[i].src) ? "loaded" : "prepared", static: staticlayer, width: (imgs[i].complete && src === imgs[i].src ? imgs[i].width : undefined), height: (imgs[i].complete && src === imgs[i].src) ? imgs[i].height : undefined });

				}
			}
			progressImageLoad(id);
		},

		// WAIT PROGRESS TILL THE PREDEFINED CONTAINER HAS ALL IMAGES LOADED INSIDE
		waitForCurrentImages: function(container, id, callback) {

			if (container === undefined || container.length === 0 || _R[id] === undefined) return;
			var waitforload = false,
				containers = [];

			if (Array.isArray(container)) {
				for (var i in container)
					if (container.hasOwnProperty(i) && container[i] !== undefined) containers.push(container[i]);
			} else
				containers.push(container);



			for (var j in containers) {

				if (!containers.hasOwnProperty(j)) continue;
				var imgs = containers[j].querySelectorAll('img, rs-sbg, .rs-svg');

				for (i in imgs) {

					if (!imgs.hasOwnProperty(i) || i === "length") continue;
					if (imgs[i].className.indexOf("rs-pzimg") >= 0) continue;


					var data = jQuery(imgs[i]).data(),
						lazy = deliverLazy(imgs[i], undefined, id);


					var src = lazy !== undefined ? lazy : _R.gA(imgs[i], "svg_src") != undefined ? _R.gA(imgs[i], "svg_src") : imgs[i].src === undefined ? data.src : imgs[i].src,
						loadobj = _R.getLoadObj(id, src);


					_R.sA(imgs[i], 'src-rs-ref', src);


					// IF ELEMENTS IS NOT LOADED YET, AND IT IS NOW LOADED
					if (data.loaded === undefined && loadobj !== undefined && loadobj.progress && loadobj.progress == "loaded") {
						if (loadobj.type == "img") {
							if (imgs[i].src.slice(imgs[i].src.length - 10) !== loadobj.src.slice(loadobj.src.length - 10)) imgs[i].src = loadobj.src;
							if (data.slidebgimage) {
								if ((loadobj.src.indexOf('images/transparent.png') == -1 && loadobj.src.indexOf('assets/transparent.png') == -1) || data.bgcolor === undefined) {
									//imgs[i].style.backgroundImage = 'url("'+loadobj.src+'")';
								} else
								if (data.bgcolor !== undefined && data.bgcolor !== "transparent") {
									loadobj.bgColor = true;
									loadobj.useBGColor = true;
									//imgs[i].style.background = data.bgcolor;
								}

								_R.sA(containers[j], "owidth", loadobj.width);
								_R.sA(containers[j], "oheight", loadobj.height);

								var bgwrap = _R.getByTag(containers[j], "RS-SBG-WRAP"),
									key = _R.gA(containers[j], 'key');

								_R[id].sbgs[key].loadobj = loadobj;

								if (bgwrap.length > 0) {
									_R.sA(bgwrap[0], "owidth", loadobj.width);
									_R.sA(bgwrap[0], "oheight", loadobj.height);
								}


								//PREPARE KEN BURNS ON LOADED SLIDES
								if (_R[id].sliderType === "carousel") {
									var bg = jQuery(bgwrap),
										_skeyindex = _R.getSlideIndex(id, key);
									if ((_R[id].carousel.justify && _R[id].carousel.slide_widths === undefined) || _R[id].carousel.slide_width === undefined) _R.setCarouselDefaults(id, true);
									if (bg.data('panzoom') !== undefined && (_R[id].panzoomTLs === undefined || _R[id].panzoomTLs[_skeyindex] === undefined)) _R.startPanZoom(bg, id, 0, _skeyindex, 'prepare', key);

									//Use Poster for HTML5 Videos also at Start
									if (_R[id].sbgs[key].isHTML5 && !_R[id].sbgs[key].videoisplaying) _R[id].sbgs[key].video = _R[id].sbgs[key].loadobj.img;

									if (containers[j].getAttribute('data-iratio') !== undefined && !containers[j].getAttribute('data-iratio') && loadobj.img && loadobj.img.naturalWidth) {
										containers[j].setAttribute('data-iratio', loadobj.img.naturalWidth / loadobj.img.naturalHeight);
										_R.setCarouselDefaults(id, "redraw");
										if (_R[id].carousel.ocfirsttun === true) _R.organiseCarousel(id, "right", true, false, false);
									}
									_R.updateSlideBGs(id, key, _R[id].sbgs[key]);
								}

							}
						} else
						if (loadobj.type == "svg" && loadobj.progress == "loaded") imgs[i].innerHTML = loadobj.innerHTML;
						data.loaded = true;
					}

					if (loadobj && loadobj.progress && loadobj.progress.match(/inprogress|inload|prepared/g))
						if (!loadobj.error && jQuery.now() - loadobj.starttoload < 15000) waitforload = true;
						else {
							loadobj.progress = "failed";
							if (!loadobj.reported_img) {
								loadobj.reported_img = true;
								console.log(src + "  Could not be loaded !");
							}
						}


						// WAIT FOR VIDEO API'S
					if (_R[id].youtubeapineeded == true && (!window.YT || YT.Player == undefined)) waitforload = vidWarning("youtube", id);
					if (_R[id].vimeoapineeded == true && !window.Vimeo) waitforload = vidWarning("vimeo", id);
				}

			}


			if (!_R.ISM && _R[id].audioqueue && _R[id].audioqueue.length > 0) {
				jQuery.each(_R[id].audioqueue, function(i, obj) {
					if (obj.status && obj.status === "prepared")
						if (jQuery.now() - obj.start < obj.waittime)
							waitforload = true;
				});
			}

			jQuery.each(_R[id].loadqueue, function(i, o) {
				if (o.static === true && ((o.progress != "loaded" && o.progress !== "done") || o.progress === "failed")) {
					if (o.progress == "failed" && !o.reported) o.reported = simWarn(o.src, o.error);
					else if (!o.error && jQuery.now() - o.starttoload < 5000) waitforload = true;
					else if (!o.reported) o.reported = simWarn(o.src, o.error);
				}
			});



			if (waitforload) tpGS.gsap.delayedCall(0.02, _R.waitForCurrentImages, [container, id, callback]);
			else if (callback !== undefined) tpGS.gsap.delayedCall(0.0001, callback);
		},

		updateVisibleArea: function(id) {
			_R[id].viewPort.visible_area = _R.revToResp(_R[id].viewPort.visible_area, _R[id].rle, "0px");
			_R[id].viewPort.vaType = new Array(4);
			for (var i in _R[id].viewPort.visible_area) {
				if (!_R[id].viewPort.visible_area.hasOwnProperty(i)) continue;
				if(_R[id].viewPort.local === false && _R[id].viewPort.global === true){

					_R[id].viewPort.vaType[i] = _R[id].viewPort.globalDist.indexOf("%") >= 0 ? "%" : "px";
					_R[id].viewPort.visible_area[i] = parseInt(_R[id].viewPort.globalDist);
				} else {
					if (_R.isNumeric(_R[id].viewPort.visible_area[i])) _R[id].viewPort.visible_area[i] += "%";

					if (_R[id].viewPort.visible_area[i] !== undefined) _R[id].viewPort.vaType[i] = _R[id].viewPort.visible_area[i].indexOf("%") >= 0 ? "%" : "px";
					_R[id].viewPort.visible_area[i] = parseInt(_R[id].viewPort.visible_area[i], 0);
				}

				_R[id].viewPort.visible_area[i] = _R[id].viewPort.vaType[i] == "%" ? _R[id].viewPort.visible_area[i] / 100 : _R[id].viewPort.visible_area[i];
			}

		},

		observeFonts: function(font, callback, _try) {
			_try = _try === undefined ? 0 : _try;
			if (_R.fonts === undefined) {
				_R.fonts = {};
				_R.monoWidth = getFontWidth('monospace');
				_R.sansWidth = getFontWidth('sans-serif');
				_R.serifWidth = getFontWidth('serif');
			}
			_try++;

			var cache = _R.fonts[font];
			if (_R.fonts[font] !== true) _R.fonts[font] = _R.monoWidth !== getFontWidth(font + ',monospace') || _R.sansWidth !== getFontWidth(font + ',sans-serif') || _R.serifWidth !== getFontWidth(font + ',serif');
			if (_try === 100 || ((cache === false || cache === undefined) && _R.fonts[font] === true)) {
				getFontWidth(font + ',monospace', true);
				getFontWidth(font + ',sans-serif', true);
				getFontWidth(font + ',serif', true);
				callback();
			} else setTimeout(function() { _R.observeFonts(font, callback, _try) }, 19);
		},


		getversion: function() { return version; },

		currentSlideIndex: function(id) { return _R[id].pr_active_key; },

		//	-	IS IOS VERSION OLDER THAN 5 ??
		iOSVersion: function() {
			if ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))) return navigator.userAgent.match(/OS 4_\d like Mac OS X/i);
			else return false;
		},

		setIsIOS: function() {
			_R.isIOS = (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream) || (navigator.platform === "MacIntel" && typeof navigator.standalone !== "undefined");
		},

		setIsChrome8889: function() {
			_R.isChrome8889 = _R.isChrome8889 === undefined ? navigator.userAgent.indexOf("Chrome/88") >= 0 || navigator.userAgent.indexOf("Chrome/89") >= 0 : _R.isChrome8889;
		},

		//	-	CHECK IF BROWSER IS IE		-
		isIE: function() {
			if (_R.isIERes === undefined) {
				var $div = jQuery('<div style="display:none;"/>').appendTo(jQuery('body'));
				$div.html('<!--[if IE 8]><a>&nbsp;</a><![endif]-->');
				_R.isIERes = $div.find('a').length;
				$div.remove();
			}
			return _R.isIERes;
		},

		// 	-	IS MOBILE ??
		is_mobile: function() {
			var agents = ['android', 'webos', 'iphone', 'ipad', 'blackberry', 'Android', 'webos', 'iPod', 'iPhone', 'iPad', 'Blackberry', 'BlackBerry'],
				ismobile = false;
			if (window.orientation !== undefined) ismobile = true;
			else
				for (var i in agents)
					if (agents.hasOwnProperty(i)) ismobile = ismobile || (navigator.userAgent.split(agents[i]).length > 1) ? true : ismobile;

			if (ismobile && document.body && document.body.className.indexOf('rs-ISM') === -1) document.body.className += ' rs-ISM';

			return ismobile;
		},

		is_android: function() {
			var agents = ['android', 'Android'],
				isandroid = false;
			for (var i in agents)
				if (agents.hasOwnProperty(i)) isandroid = isandroid || (navigator.userAgent.split(agents[i]).length > 1) ? true : isandroid;
			return isandroid;
		},

		// -  CALL BACK HANDLINGS - //
		callBackHandling: function(id, type, position) {
			if (_R[id].callBackArray)
				jQuery.each(_R[id].callBackArray, function(i, c) {
					if (c)
						if (c.inmodule && c.inmodule === type)
							if (c.atposition && c.atposition === position)
								if (c.callback) c.callback.call();
				});
		},

		get_browser: function() {
			var ua = navigator.userAgent,
				tem, M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
			if (/trident/i.test(M[1])) {
				tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
				return 'IE';
			}
			if (M[1] === 'Chrome') {
				tem = ua.match(/\b(OPR|Edge)\/(\d+)/);
				if (tem != null) return tem[1].replace('OPR', 'Opera');
			}
			M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
			if ((tem = ua.match(/version\/(\d+)/i)) != null)
				M.splice(1, 1, tem[1]);
			return M[0];
		},

		get_browser_version: function() {
			var N = navigator.appName,
				ua = navigator.userAgent,
				tem,
				M = ua.match(/(edge|opera|chrome|safari|firefox|msie)\/?\s*(\.?\d+(\.\d+)*)/i);
			if (M && (tem = ua.match(/version\/([\.\d]+)/i)) != null) M[2] = tem[1];
			M = M ? [M[1], M[2]] : [N, navigator.appVersion, '-?'];
			return M[1];
		},

		isFaceBook: function() {
			if (_R.isFaceBookApp == undefined) {
				_R.isFaceBookApp = navigator.userAgent || navigator.vendor || window.opera;
				_R.isFaceBookApp = (_R.isFaceBookApp.indexOf("FBAN") > -1) || (_R.isFaceBookApp.indexOf("FBAV") > -1);
			}
			return _R.isFaceBookApp;
		},

		isFirefox: function(id) {
            if ( ! id)
                return _R.get_browser() === "Firefox";
			_R[id].isFirefox = _R[id].isFirefox === undefined ? _R.get_browser() === "Firefox" : _R[id].isFirefox;
			_R.isFF = _R[id].isFirefox;
			return _R[id].isFirefox;
		},

		isSafari11: function() {
			return _R.trim(_R.get_browser().toLowerCase()) === 'safari' && parseFloat(_R.get_browser_version()) >= 11;
		},

		isWebkit: function() {
			var browser = /(webkit)[ \/]([\w.]+)/.exec((navigator.userAgent).toLowerCase());
			return browser && browser[1] && browser[1] === "webkit";
		},

		isIE11: function() {
			_R.IE11 = _R.IE11 === undefined ? !!navigator.userAgent.match(/Trident.*rv\:11\./) : _R.IE11;
			return _R.IE11;
		},

		deepLink: function(id, deeplink) {
			if (deeplink === undefined) return;

			var st = 'slide',
				slideIndex = parseInt((deeplink.toString().replace(/^slide/, '').replace('-', '')), 10);

			// check custom slide deep link
			if (isNaN(slideIndex))
				for (var i in _R[id].slides) {
					if (!_R[id].slides.hasOwnProperty(i)) continue;
					if (_R.gA(_R[id].slides[i], "deeplink") === deeplink) {
						slideIndex = parseInt(_R.gA(_R[id].slides[i], "originalindex"), 10);
						break;
					}
				}

			// check out of bounds
			if (isNaN(slideIndex) || (slideIndex < 1 || slideIndex > _R[id].realslideamount))
				return;
			else return slideIndex;
		},

		// GET THE HORIZONTAL OFFSET OF SLIDER BASED ON THE THU`MBNAIL AND TABS LEFT AND RIGHT SIDE
		getHorizontalOffset: function(container, side) {
			var thumbloff = gWiderOut(container, '.outer-left'),
				thumbroff = gWiderOut(container, '.outer-right');
			return side == "left" ? thumbloff : side == "right" ? thumbroff : side == "all" ? { left: thumbloff, right: thumbroff, both: thumbloff + thumbroff, inuse: (thumbloff + thumbroff) != 0 } : thumbloff + thumbroff;
		},

		getComingSlide: function(id, direction) {
			var aindex = _R[id].pr_next_key !== undefined ? _R[id].pr_next_key : _R[id].pr_processing_key !== undefined ? _R[id].pr_processing_key : _R[id].pr_active_key,
				ret = 0;
			ret = 0;

			// IF WE ARE ON AN INVISIBLE SLIDE CURRENTLY
			if (_R[id].pr_active_slide !== undefined && _R.gA(_R[id].pr_active_slide[0], 'not_in_nav') == 'true') aindex = _R[id].pr_lastshown_key;
			// SET NEXT DIRECTION
			if (direction !== undefined && _R.isNumeric(direction) || direction !== undefined && direction.match(/to/g)) {
				if (direction === 1 || direction === -1)
					ret = parseInt(aindex, 0) + direction < 0 ? _R[id].slideamount - 1 : parseInt(aindex, 0) + direction >= _R[id].slideamount ? 0 : parseInt(aindex, 0) + direction;
				else {
					direction = _R.isNumeric(direction) ? direction : parseInt(direction.split("to")[1], 0);
					ret = direction < 0 ? 0 : direction > _R[id].slideamount - 1 ? _R[id].slideamount - 1 : direction;
				}
			} else if (direction)
				for (var i in _R[id].slides)
					if (_R[id].slides.hasOwnProperty(i)) ret = _R[id].slides && _R[id].slides[i] && (_R.gA(_R[id].slides[i], 'key') === direction || _R[id].slides[i].id === direction) ? i : ret;
			return { nindex: ret, aindex: aindex };
		},

		// 	-	CALLING THE NEW SLIDE 	-	//
		callingNewSlide: function(id, direction, ignore) {
			var r = _R.getComingSlide(id, direction);

			_R[id].pr_next_key = r.nindex;
			_R[id].sdir = _R[id].pr_next_key < _R[id].pr_active_key ? 1 : 0;
			if (ignore && _R[id].carousel !== undefined) _R[id].carousel.focused = _R[id].pr_next_key;


			if (!_R[id].ctNavElement) _R[id].c.trigger("revolution.nextslide.waiting");
			else _R[id].ctNavElement = false;
			if (_R[id].started && (r.aindex === _R[id].pr_next_key && r.aindex === _R[id].pr_lastshown_key) || (_R[id].pr_next_key !== r.aindex && _R[id].pr_next_key != -1 && _R[id].pr_lastshown_key !== undefined)) swapSlide(id, ignore);
		},

		// FIND SEARCHED IMAGE/SRC IN THE LOAD QUEUE
		getLoadObj: function(id, src) {
			var ret = _R[id].loadqueue !== undefined && _R[id].loadqueue.filter(function(x) { return x.src === src; })[0];
			return ret === undefined ? { src: src } : ret;
		},


		// FIND CURRENT RESOPNSIVE LEVEL
		getResponsiveLevel: function(id) {
			var curwidth = 9999,
				lastmaxlevel = 0,
				lastmaxid = 0,
				r = 0;

			if (_R[id].responsiveLevels && _R[id].responsiveLevels.length)
				for (var i in _R[id].responsiveLevels) {
					if (!_R[id].responsiveLevels.hasOwnProperty(i)) continue;
					if (_R.winWAll < _R[id].responsiveLevels[i] && (lastmaxlevel == 0 || lastmaxlevel > parseInt(_R[id].responsiveLevels[i]))) {
						curwidth = parseInt(_R[id].responsiveLevels[i]);
						r = parseInt(i);
						lastmaxlevel = parseInt(_R[id].responsiveLevels[i]);

					}
					if (_R.winWAll > _R[id].responsiveLevels[i] && lastmaxlevel < _R[id].responsiveLevels[i]) {
						lastmaxlevel = parseInt(_R[id].responsiveLevels[i]);
						lastmaxid = parseInt(i);

					}
				}

			return (lastmaxlevel < curwidth) ? lastmaxid : r;
		},

		// CALCULATE LINEAR RESIZING MULTIPLICATOR
		getSizeMultpilicator: function(id, upscaling, compareto) {
			var r = { h: 0, w: 0 };

			if (_R[id].justifyCarousel) r.h = r.w = 1;
			else {

				r.w = (compareto.width / _R[id].gridwidth[_R[id].level]);
				r.h = (compareto.height / _R[id].gridheight[_R[id].level]);

				r.w = isNaN(r.w) ? 1 : r.w;
				r.h = isNaN(r.h) ? 1 : r.h;

				//ALlow to Scale over "1" which will upscale elements, positions module wide
				if (_R[id].enableUpscaling == true) {
					r.h = r.w;
				} else {
					if (r.h > r.w) r.h = r.w;
					else r.w = r.h;
					if (r.h > 1 || r.w > 1) {
						r.w = 1;
						r.h = 1;
					}
				}
			}
			return r;
		},


		// UPDATE DIMENSIONS ON ALL IMPORTANT CONTAINERS
		updateDims: function(id, type) {
			// 	BLOCK
			// 		FW WRAP
			// 			MODULE
			//    			CANVAS
			//       			CONTENT
			//

			var _actli = _R[id].pr_processing_key || _R[id].pr_active_key || 0,
				_oldli = _R[id].pr_active_key || 0,
				ismodal = _R[id].modal !== undefined && _R[id].modal.useAsModal,
				_W_ = ismodal ? _R.winWAll : _R.winW,
				somethingchanged = false;

			_R[id].lastScrollBarWidth = _R.scrollBarWidth;
			_R[id].redraw = _R[id].redraw === undefined ? {} : _R[id].redraw;
			_R[id].module = _R[id].module === undefined ? {} : _R[id].module;
			_R[id].canv = _R[id].canv === undefined ? {} : _R[id].canv;
			_R[id].content = _R[id].content === undefined ? {} : _R[id].content;

			_R[id].drawUpdates = { c: {}, cpar: {}, canv: {} };


			// GET CAROUSEL MARGINS
			if (_R[id].sliderType == "carousel") _R[id].module.margins = { top: parseInt((_R[id].carousel.padding_top || 0), 0), bottom: parseInt((_R[id].carousel.padding_bottom || 0), 0) };
			else _R[id].module.margins = { top: 0, bottom: 0 };

			// GET MODULE PADDINGS
			if (_R[id].module.paddings === undefined) _R[id].module.paddings = { top: (parseInt(_R[id].cpar.css("paddingTop"), 0) || 0), bottom: (parseInt(_R[id].cpar.css("paddingBottom"), 0) || 0) };

			// GET BLOCK WRAPPER PADDINGS
			if (_R[id].blockSpacing !== undefined) {
				_R[id].block = {
					bottom: _R[id].blockSpacing.bottom !== undefined ? parseInt(_R[id].blockSpacing.bottom[_R[id].level], 0) : 0,
					top: _R[id].blockSpacing.top !== undefined ? parseInt(_R[id].blockSpacing.top[_R[id].level], 0) : 0,
					left: _R[id].blockSpacing.left !== undefined ? parseInt(_R[id].blockSpacing.left[_R[id].level], 0) : 0,
					right: _R[id].blockSpacing.right !== undefined ? parseInt(_R[id].blockSpacing.right[_R[id].level], 0) : 0
				}
				_R[id].block.hor = _R[id].block.left + _R[id].block.right;
				_R[id].block.ver = _R[id].block.top + _R[id].block.bottom;
			} else
			if (_R[id].block === undefined) _R[id].block = { top: 0, left: 0, right: 0, bottom: 0, hor: 0, ver: 0 }


			/*UPDATE PADDINGS ON BLOCK*/
			if (_R[id].blockSpacing !== undefined) {
				var blockobj = {
						paddingLeft: _R[id].block.left,
						paddingRight: _R[id].block.right,
						marginTop: _R[id].block.top,
						marginBottom: _R[id].block.bottom
					},
					stbl = JSON.stringify(blockobj);

				if (blockobj !== _R[id].emptyObject && stbl !== _R[id].caches.setsizeBLOCKOBJ) {
					tpGS.gsap.set(_R[id].blockSpacing.block, blockobj);
					_R[id].caches.setsizeBLOCKOBJ = stbl;
					somethingchanged = true;
				}
			}



			/* CALCULATE RESPONSIVE LEVEL */
			_R[id].levelForced = _R[id].level = _R.getResponsiveLevel(id);

			/* GET ROW HEIGHTS */
			_R[id].rowHeights = _R.getRowHeights(id);

			/* CALCULATE MODULE WIDTH */
			_R[id].aratio = _R[id].gridheight[_R[id].level] / _R[id].gridwidth[_R[id].level];
			_R[id].module.width = _R[id].sliderLayout === "auto" || _R[id].disableForceFullWidth == true ?
				_R[id].cpar.width() : _W_ - _R[id].block.hor;


			/* CALCULATE OUTER NAVIGATION SIZES */
			_R[id].outNavDims = _R.getOuterNavDimension(id);


			/* CALCULATE CANVAS WIDTH */
			_R[id].canv.width = _R[id].module.width - _R[id].outNavDims.horizontal - (ismodal ? _R.scrollBarWidth : 0);
			if (ismodal && _R[id].sliderLayout === "auto") _R[id].canv.width = Math.min(_R[id].gridwidth[_R[id].level], _W_);

			/* CALCULAT CANVAS HEIGHT */
			if (_R[id].sliderLayout === "fullscreen" || _R[id].infullscreenmode) {
				var tempHeight = _R.getWinH(id) - (_R[id].modal.useAsModal ===  true ? 0 : _R.getFullscreenOffsets(id));
				_R[id].canv.height = Math.max(_R[id].rowHeights.cur, Math.max(tempHeight - _R[id].outNavDims.vertical, _R[id].minHeight));

				/* FIX IF DIFFERENT HEIGHTS OF CURRENT AND LAST SLIDE DIMENSION EXIST BASED ON ROW HEIGHT !! */
				if (_oldli !== _actli) {
					_R[id].currentSlideHeight = Math.max(_R[id].rowHeights.last, Math.max(tempHeight - _R[id].outNavDims.vertical, _R[id].minHeight));
					_R[id].redraw.maxHeightOld = true;
				}
				_R[id].drawUpdates.c.height = "100%";
			} else {
				_R[id].canv.height = _R[id].keepBPHeight ? _R[id].gridheight[_R[id].level] : Math.round(_R[id].canv.width * _R[id].aratio);
				_R[id].canv.height = !_R[id].autoHeight ? Math.min(_R[id].canv.height, _R[id].gridheight[_R[id].level]) : _R[id].canv.height;
				_R[id].canv.height = Math.max(Math.max(_R[id].rowHeights.cur, _R[id].canv.height), _R[id].minHeight);
				_R[id].drawUpdates.c.height = _R[id].canv.height;
			}

			_R[id].module.height = _R[id].canv.height;


			//SET REAL BG SIZE HERE
			//_R[id].BG = _R[id].sliderType==="carousel" ? {width:_R[id].module.width, height:_R[id].module.height} : {width:_R[id].module.width, height:_R[id].module.height};

			//CALCULATE MAX HEIGHT OF CONTENT
			if (_R[id].sliderLayout == "fullwidth" && !_R[id].fixedOnTop) {

				_R[id].drawUpdates.c.maxHeight = _R[id].maxHeight != 0 ? Math.min(_R[id].canv.height, _R[id].maxHeight) : _R[id].canv.height;
				//_R[id].module.height = _R[id].canv.height = _R[id].drawUpdates.c.maxHeight;
			}



			/* CALCULATE CONTENT MULTIPLICATORS */
			_R[id].CM = _R.getSizeMultpilicator(id, _R[id].enableUpscaling, { width: _R[id].canv.width, height: _R[id].canv.height });


			/* CALCULATE CONTENT WIDTH AND HEIGHT */
			_R[id].content.width = _R[id].gridwidth[_R[id].level] * _R[id].CM.w;
			_R[id].content.height = Math.round(Math.max(_R[id].rowHeights.cur, _R[id].gridheight[_R[id].level] * _R[id].CM.h));

			/* UPDATE REAL CONTAINERS */
			var _h = _R[id].module.margins.top +
				_R[id].module.margins.bottom +
				(_R[id].sliderLayout === "fullscreen" ? 0 : _R[id].outNavDims.vertical) +
				_R[id].canv.height +
				_R[id].module.paddings.top +
				_R[id].module.paddings.bottom;

			_R[id].drawUpdates.cpar.height = _h;
			_R[id].drawUpdates.cpar.width = _R[id].sliderLayout === "auto" ? "auto" : _R[id].module.width;

			if (_R[id].sliderLayout !== "auto" && (_R[id].sliderLayout !== "fullscreen" || _R[id].disableForceFullWidth !== true) && _R[id].rsFullWidthWrap !== undefined)
				_R[id].drawUpdates.cpar.left = 0 - Math.ceil(_R[id].rsFullWidthWrap.offset().left - (_R[id].outNavDims.left + _R[id].block.left));
			else
			if (_R[id].sliderLayout == "fullscreen" && _R[id].disableForceFullWidth == true) _R[id].drawUpdates.cpar.left = 0;


			// OUTER CONTAINER HEIGHT MUST BE DIFFERENT DUE FIXED SCROLL EFFECT
			if (_R[id].sbtimeline.set && _R[id].sbtimeline.fixed) {
				if (_R[id].sbtimeline.extended === undefined) _R.updateFixedScrollTimes(id);

				_R[id].forcerHeight = ((2 * _h) + _R[id].sbtimeline.extended);
			} else {
				_R[id].forcerHeight = _h;

			}


			if (_R[id].forcerHeight !== _R[id].caches.setsizeForcerHeight && _R[id].forcer !== undefined) {
				_R[id].caches.setsizeForcerHeight = _R[id].forcerHeight;
				somethingchanged = true;
				_R[id].redraw.forcer = true;
			}



			/* SET CONTENT POSITION AND WIDTH */
			_R[id].drawUpdates.c.width = _R[id].canv.width;
			if (_R[id].sliderLayout === "auto") _R[id].drawUpdates.c.left = _R[id].outNavDims.left;

			/* SET DIMENSION ON RS-MODULE CONTAINER */
			if (_R[id].drawUpdates.c !== _R[id].emptyObject && JSON.stringify(_R[id].drawUpdates.c) !== _R[id].caches.setsizeCOBJ) {
				_R[id].caches.setsizeCOBJ = JSON.stringify(_R[id].drawUpdates.c);
				somethingchanged = true;
				_R[id].redraw.c = true;
			}

			/* SET DIMS ON RS-MODULE-WRAP CONTAINER */
			if (_R[id].drawUpdates.cpar !== _R[id].emptyObject && JSON.stringify(_R[id].drawUpdates.cpar) !== _R[id].caches.setsizeCPAROBJ) {
				_R[id].caches.setsizeCPAROBJ = JSON.stringify(_R[id].drawUpdates.cpar);
				somethingchanged = true;
				_R[id].redraw.cpar = true;
			}

			/* UPDATE MODAL CONTAINER AND CANVAS WIDTH */
			if (ismodal && _R[id].sliderLayout === "auto" && _R[id].caches.canWidth !== _R[id].canv.width) {
				_R[id].caches.canWidth = _R[id].canv.width;
				somethingchanged = true;
				_R[id].redraw.modalcanvas = true;
			}

			/* UPDATE STATIC LAYER POSITIONS */
			if (_R[id].slayers && _R[id].slayers.length > 0 && _R[id].outNavDims.left !== _R[id].caches.outNavDimsLeft && (_R[id].sliderLayout != "fullwidth" && _R[id].sliderLayout != "fullscreen")) {
				_R[id].caches.outNavDimsLeft = _R[id].outNavDims.left;
				_R[id].redraw.slayers = true;
			}

			/* FINETUNING OF MODAL WINDOWS */
			if (ismodal && _R[id].modal.calibration !== undefined && _R[id].modal.vertical === "middle") {
				_R[id].modal.calibration.top = _R.getWinH(id) < _h ? "0%" : "50%";
				_R[id].modal.calibration.y = _R.getWinH(id) < _h ? "0px" : "-50%";
				if (_R[id].sliderLayout === "fullwidth") {
					somethingchanged = true;
					_R[id].redraw.modulewrap = true;
				}
			}

			/* CALCULATE GRID OFFSETS FOR PROGRESS BAR */
			_R[id].gridOffsetWidth = (_R[id].module.width - _R[id].gridwidth[_R[id].level]) / 2;
			//_R[id].gridOffsetHeight = (_R[id].module.height - _R[id].gridheight[_R[id].level])/2;
			_R[id].gridOffsetHeight = (_R[id].module.height - _R[id].content.height) / 2;


			// FALLBACKS AND CACHES (ADDONS OR SIMILAR)
			_R[id].caches.curRowsHeight = _R[id].currentRowsHeight = _R[id].rowHeights.cur;
			_R[id].caches.moduleWidth = _R[id].width = _R[id].module.width;
			_R[id].caches.moduleHeight = _R[id].height = _R[id].module.height;
			_R[id].caches.canWidth = _R[id].conw = _R[id].canv.width;
			_R[id].caches.canHeight = _R[id].conh = _R[id].canv.height;
			_R[id].bw = _R[id].CM.w;
			_R[id].bh = _R[id].CM.h;
			_R[id].caches.outNavDimsLeft = _R[id].outNavDims.left;


			window.requestAnimationFrame(function() {
				if (_R[id].redraw.forcer) tpGS.gsap.set(_R[id].forcer, { height: _R[id].forcerHeight });
				if (_R[id].redraw.c) tpGS.gsap.set(_R[id].c, _R[id].drawUpdates.c);
				if (_R[id].redraw.cpar) tpGS.gsap.set(_R[id].cpar, _R[id].drawUpdates.cpar);
				if (_R[id].redraw.modalcanvas) tpGS.gsap.set([_R[id].modal.c, _R[id].canvas], { width: _R[id].canv.width });
				if (_R[id].redraw.maxHeightOld) _R[id].slides[_oldli].style.maxHeight = _R[id].currentSlideHeight !== _R[id].canv.height ? _R[id].currentSlideHeight + "px" : "none";
				if (_R[id].redraw.slayers) tpGS.gsap.set(_R[id].slayers, { left: _R[id].outNavDims.left });
				if (_R[id].redraw.modulewrap) tpGS.gsap.set(_R[id].modal.c.find('rs-module-wrap'), _R[id].modal.calibration);
				//1ST TIME START NAVIGATION BUILDER
				if (_R[id].navigation.initialised !== true && type === "prepared") {
					if (_R[id].sliderType !== "hero" && _R.createNavigation && _R[id].navigation.use && _R[id].navigation.createNavigationDone !== true) _R.createNavigation(id);
					if (_R.resizeThumbsTabs && _R.resizeThumbsTabs && _R[id].navigation.use) _R.resizeThumbsTabs(id);
				}

				//IF PROGRESSBAR DRAWN, BUT NOT YET POSITIONED WELL
				if (_R[id].rebuildProgressBar) buildProgressBar(id);

				_R[id].redraw = {};
			});

			var layersNeedUpdate = _R[id].inviewport && ((_R[id].heightInLayers !== undefined && _R[id].module.height !== _R[id].heightInLayers) ||
				(_R[id].widthInLayers !== undefined && _R[id].module.width !== _R[id].widthInLayers));


			if (type !== "ignore" && layersNeedUpdate) {
				_R[id].heightInLayers = undefined;
				_R[id].widthInLayers = undefined;
				if (_R[id].sliderType !== 'carousel') {
					if (_R[id].pr_next_key !== undefined) _R.animateTheLayers({ slide: _R[id].pr_next_key, id: id, mode: "rebuild", caller: "swapSlideProgress_1" });
					else
					if (_R[id].pr_processing_key !== undefined) _R.animateTheLayers({ slide: _R[id].pr_processing_key, id: id, mode: "rebuild", caller: "swapSlideProgress_2" });
					else
					if (_R[id].pr_active_key !== undefined) _R.animateTheLayers({ slide: _R[id].pr_active_key, id: id, mode: "rebuild", caller: "swapSlideProgress_3" });
				}
				somethingchanged = true;
			}

			// CALL PROCESSES (LATER REWRITE ON EVENT BASED !?)
			if (somethingchanged && type !== "ignore") _R.requestLayerUpdates(id, "enterstage");
			if (_R[id].module.height !== _R[id].module.lastHeight) {
				_R[id].module.lastHeight = _R[id].module.height;

				//window.requestAnimationFrame(function() {containerResized(id,undefined,false);});
				window.requestAnimationFrame(function() { if (!(window.innerHeight === screen.height || Math.round(window.innerHeight * window.devicePixelRatio) === screen.height)) containerResized(id, undefined, false); });
			}

			// Force recalculate if scrollbar width has changed during calculation
			tpGS.gsap.delayedCall(0.1, function() {

				if (_R[id].lastScrollBarWidth !== _R.scrollBarWidth) {
					_R.updateDims(id, 'ignore');
					containerResized(id);
				} else if (!_R.isModalOpen && _R.scrollBarWidth !== window.innerWidth - document.documentElement.clientWidth) {
					if (_R.rAfScrollbar === undefined) _R.rAfScrollbar = requestAnimationFrame(function() {
						_R.rAfScrollbar = undefined;
						_R.getWindowDimension(id, false);
					});
				}
			});

			return somethingchanged;
		},

		getSlideIndex: function(id, key) {
			var fnd = false;
			for (var i in _R[id].slides)
				if (!_R[id].slides.hasOwnProperty(i) || fnd !== false) continue;
				else fnd = _R.gA(_R[id].slides[i], 'key') === key ? i : fnd;
			return fnd === false ? 0 : fnd;
		},

		loadUpcomingContent: function(id) {

			if (_R[id].lazyType == "smart") {
				var list = [],
					ix = parseInt(_R.getSlideIndex(id, _R.gA(_R[id].pr_next_slide[0], 'key')), 0),
					px = (ix - 1 < 0 ? _R[id].realslideamount - 1 : ix - 1),
					nx = (ix + 1 == _R[id].realslideamount ? 0 : ix + 1);

				if (px !== ix) list.push(_R[id].slides[px]);
				if (nx !== ix) list.push(_R[id].slides[nx]);
				if (list.length > 0) {
					_R.loadImages(list, id, 2);
					_R.waitForCurrentImages(list, id, function() {});
				}
			}
		},

		lazyLoadAllSlides : function(id) {
			if (_R[id].lazyType == "all" && _R[id].lazyLoad_AllDone!==true && ((_R[id].viewPort.enable && _R[id].inviewport) || !_R[id].viewPort.enable)) {
				for (var i in _R[id].slides) {
					if (!_R[id].slides.hasOwnProperty(i)) continue;
					_R.loadImages(_R[id].slides[i], id, i);
					_R.waitForCurrentImages(_R[id].slides[i], id, function() {});
				}
				_R[id].lazyLoad_AllDone = true;
			}
		},

		getFullscreenOffsets: function(id) {
			var r = 0;
			if (_R[id].fullScreenOffsetContainer != undefined) {
				var ocs = ("" + _R[id].fullScreenOffsetContainer).split(",");
				for (var i in ocs)
					if (ocs.hasOwnProperty(i)) r += (jQuery(ocs[i]).outerHeight(true) || 0);
			}

			if (_R[id].fullScreenOffset != undefined) {
				if (!_R.isNumeric(_R[id].fullScreenOffset) && _R[id].fullScreenOffset.split("%").length > 1) r += ((_R.getWinH(id)) * parseInt(_R[id].fullScreenOffset, 0) / 100);
				else
				if (_R.isNumeric(parseInt(_R[id].fullScreenOffset, 0))) r += (parseInt(_R[id].fullScreenOffset, 0) || 0);
			}
			_R[id].fullScreenOffsetResult = r;
			return r;
		},

		unToggleState: function(a) {
			if (a !== undefined)
				for (var i = 0; i < a.length; i++) try { document.getElementById(a[i]).classList.remove("rs-tc-active"); } catch (e) {}
		},

		toggleState: function(a) {
			if (a !== undefined)
				for (var i = 0; i < a.length; i++) try { document.getElementById(a[i]).classList.add("rs-tc-active"); } catch (e) {}
		},
		swaptoggleState: function(a) {

			if (a != undefined && a.length > 0)
				for (var i = 0; i < a.length; i++) {
					var el = document.getElementById(a[i]);
					if (_R.gA(el, "toggletimestamp") !== undefined && ((new Date().getTime() - _R.gA(el, "toggletimestamp")) < 250)) return;
					_R.sA(el, "toggletimestamp", new Date().getTime());
					if (el !== null) {
						if (el.className.indexOf("rs-tc-active") >= 0)
							el.classList.remove("rs-tc-active");
						else
							el.classList.add("rs-tc-active");
					}
				}
		},
		lastToggleState: function(a) {
			var re;
			if (a !== undefined)
				for (var i = 0; i < a.length; i++) {
					var el = document.getElementById(a[i]);
					re = re === true || (el !== null && el.className.indexOf("rs-tc-active") >= 0) ? true : re;
				}
			return re;
		},
		revCheckIDS: function(id, item) {
			if (_R.gA(item, "idcheck") === undefined) {
				var cache = item.id,
					inGlobal = jQuery.inArray(item.id, window.RSANYID),
					inLocal = -1;
				if (inGlobal !== -1) {
					inLocal = jQuery.inArray(item.id, _R[id].anyid);
					if (window.RSANYID_sliderID[inGlobal] !== id || inLocal !== -1) {
						item.id = item.id + "_" + Math.round(Math.random() * 9999);
						console.log("Warning - ID:" + cache + " exists already. New Runtime ID:" + item.id);
						inGlobal = inLocal = -1;
					}
				}
				if (inLocal === -1) _R[id].anyid.push(item.id);
				if (inGlobal === -1) {
					window.RSANYID.push(item.id);
					window.RSANYID_sliderID.push(id);
				}
			}
			_R.sA(item, "idcheck", true);
			return item.id;
		},



		buildSpinner: function(id, spinner, color, eclass) {

			var container;
			if (spinner === "off") return;
			eclass = eclass === undefined ? "" : eclass;
			color = color === undefined ? '#ffffff' : color;
			var spinnerType = parseInt(spinner.replace('spinner', ''), 10);

			if (isNaN(spinnerType) || spinnerType < 6) {
				var a = 'style="background-color:' + color + '"',
					ems = (eclass !== undefined && (spinnerType === 1 || spinnerType == 2)) ? a : '',
					eds = (eclass !== undefined && (spinnerType === 3 || spinnerType == 4)) ? a : '';
				container = jQuery('<rs-loader ' + ems + ' class="' + spinner + ' ' + eclass + '"><div ' + eds + ' class="dot1"></div><div ' + eds + ' class="dot2"></div><div ' + eds + ' class="bounce1"></div><div ' + eds + ' class="bounce2"></div><div ' + eds + ' class="bounce3"></div></rs-loader>');
			} else {

				// new spinners
				var spinHtml = '<div class="rs-spinner-inner"';

				if (spinnerType === 7) {
					var clr;
					if (color.search('#') !== -1) {
						clr = color.replace('#', '');
						clr = 'rgba(' + parseInt(clr.substring(0, 2), 16) + ', ' + parseInt(clr.substring(2, 4), 16) + ', ' + parseInt(clr.substring(4, 6), 16) + ', ';
					} else if (color.search('rgb') !== -1) {
						clr = color.substring(color.indexOf('(') + 1, color.lastIndexOf(')')).split(',');
						if (clr.length > 2) clr = 'rgba(' + clr[0].trim() + ', ' + clr[1].trim() + ', ' + clr[2].trim() + ', ';
					}
					if (clr && typeof clr === 'string') spinHtml += ' style="border-top-color: ' + clr + '0.65); border-bottom-color: ' + clr + '0.15); border-left-color: ' + clr + '0.65); border-right-color: ' + clr + '0.15)"';
				} else if (spinnerType === 12) {
					spinHtml += ' style="background:' + color + '"';
				}

				spinHtml += '>';
				var numSpans = [10, 0, 4, 2, 5, 9, 0, 4, 4, 2],
					totalSpans = numSpans[spinnerType - 6];

				for (var its = 0; its < totalSpans; its++) {
					if (its > 0) spinHtml += ' ';
					spinHtml += '<span style="background:' + color + '"></span>';
				}

				spinHtml += '</div>';
				container = jQuery('<rs-loader class="' + spinner + ' ' + eclass + '">' + spinHtml + '</div></rs-loader>');
			}
			return container;
		},

		addStaticLayerTo: function(id, where, layer) {
			if (_R[id].slayers.length < 2) {
				var container = document.createElement('rs-static-layers');
				container.className = "rs-stl-" + where;
				container.appendChild(layer[0]);
				_R[id].c[0].appendChild(container);
				_R[id].slayers.push(container);
			} else
				_R[id].slayers[1].appendChild(layer[0]);

		}

	});




	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	var getTagSelector = function() {
		if (_R.isIE11()) {
			return function(target, query) {
				return target.querySelectorAll(query);
			}
		} else {
			return function(target, query) {
				return target.getElementsByTagName(query);
			}
		}
	}

	var updateStartHeights = function(id) {

		_R[id].responsiveLevels = _R.revToResp(_R[id].responsiveLevels, _R[id].rle);
		_R[id].visibilityLevels = _R.revToResp(_R[id].visibilityLevels, _R[id].rle);
		_R[id].responsiveLevels[0] = 9999;
		_R[id].rle = _R[id].responsiveLevels.length || 1;
		_R[id].gridwidth = _R.revToResp(_R[id].gridwidth, _R[id].rle);
		_R[id].gridheight = _R.revToResp(_R[id].gridheight, _R[id].rle);


		if (_R[id].editorheight !== undefined) _R[id].editorheight = _R.revToResp(_R[id].editorheight, _R[id].rle);
		_R.updateDims(id);

		//var newh = Math.max(_R[id].minHeight,_R[id].gridheight[_R[id].level] * _R[id].CM.w);
		//if (_R[id].editorheight!==undefined) newh = Math.max(newh,_R[id].editorheight[_R[id].level] * _R[id].CM.w);

		/*if (_R[id].cpar!==undefined && _R[id].cpar[0].offsetHeight<newh)
			tpGS.gsap.set([_R[id].cpar,_R[id].c],{height:newh});
		else
			tpGS.gsap.set(_R[id].c,{height:newh});*/


	};


	var removeArray = function(arr, i) {
		var newarr = [];
		jQuery.each(arr, function(a, b) {
			if (a != i) newarr.push(b);
		});
		return newarr;
	};

	var removeNavWithLiref = function(a, ref, id) {
		_R[id].c.find(a).each(function() {
			var a = jQuery(this);
			if (a.data('key') === ref) a.remove();
		});
	};


	///////////////////////////////////
	//   -  WAIT FOR SCRIPT LOADS  - //
	///////////////////////////////////
	var getFontWidth = function(fontFamily, remove) {
		if (_R["rsfont_" + fontFamily] == undefined) {
			_R["rsfont_" + fontFamily] = document.createElement('span');
			_R["rsfont_" + fontFamily].innerHTML = Array(100).join('wi');
			_R["rsfont_" + fontFamily].style.cssText = [
				'position:absolute',
				'width:auto',
				'font-size:128px',
				'left:-99999px'
			].join(' !important;');
			_R["rsfont_" + fontFamily].style.fontFamily = fontFamily;
			document.body.appendChild(_R["rsfont_" + fontFamily]);
		}
		if (remove === undefined) return _R["rsfont_" + fontFamily].clientWidth;
		else document.body.removeChild(_R["rsfont_" + fontFamily]);
	};




	//////////////////////////////////////////
	//	-	INITIALISATION OF OPTIONS 	-	//
	//////////////////////////////////////////

	var gWiderOut = function(c, cl) {
		var r = 0;
		c.find(cl).each(function() {
			var a = jQuery(this);
			if (!a.hasClass("tp-forcenotvisible") && r < a.outerWidth())
				r = a.outerWidth();
		});

		return r;
	};

	//////////////////////////////////////////
	//	-	INITIALISATION OF SLIDER	-	//
	//////////////////////////////////////////
	var initSlider = function(id) {

		if (id === undefined || _R[id] === undefined || _R[id].c === undefined) return false;

		// CHECK FOR ALTERNATIVE IMAGE, AND IFRAM EXIST, AND WE ARE IN IE8, MOBILE, DRAW IT SIMPLE
		if (_R[id].cpar !== undefined && _R[id].cpar.data('aimg') != undefined && ((_R[id].cpar.data('aie8') == "enabled" && _R.isIE(8)) || (_R[id].cpar.data('amobile') == "enabled" && _R.ISM))) {
			_R[id].c.html('<img class="tp-slider-alternative-image" src="' + _R[id].cpar.data('aimg') + '">');
			return;
		}

		// CHECK IF FIREFOX 13 IS ON WAY.. IT HAS A STRANGE BUG, CSS ANIMATE SHOULD NOT BE USED
		window._rs_firefox13 = false;
		window._rs_firefox = _R.isFirefox();
		window._rs_ie = window._rs_ie === undefined ? !jQuery.support.opacity : window._rs_ie;
		window._rs_ie9 = window._rs_ie9 === undefined ? (document.documentMode == 9) : window._rs_ie9;

		// CHECK THE jQUERY VERSION
		var version = jQuery.fn.jquery.split('.'),
			versionTop = parseFloat(version[0]),
			versionMinor = parseFloat(version[1]);
		if (versionTop == 1 && versionMinor < 7) _R[id].c.html('<div style="text-align:center; padding:40px 0px; font-size:20px; color:#992222;"> The Current Version of jQuery:' + version + ' <br>Please update your jQuery Version to min. 1.7 in Case you wish to use the Revolution Slider Plugin</div>');
		if (versionTop > 1) window._rs_ie = false;

		_R[id].realslideamount = _R[id].slideamount = 0;

		var rss = _R.getByTag(_R[id].canvas[0], "RS-SLIDE"),
			slidestoremove = [];
		_R[id].notInNav = [];


		_R[id].slides = [];

		// Remove Not Needed Slides for Mobile Devices AND Index Current Slides
		for (var i in rss) {
			if (!rss.hasOwnProperty(i)) continue;
			if (_R.gA(rss[i], "hsom") == "on" && _R.ISM) slidestoremove.push(rss[i]);
			else {
				if (_R.gA(rss[i], "invisible") || _R.gA(rss[i], "invisible") == true) _R[id].notInNav.push(rss[i]);
				else {
					_R[id].slides.push(rss[i]);
					_R[id].slideamount++;
				}
				_R[id].realslideamount++;
				_R.sA(rss[i], "originalindex", _R[id].realslideamount);
				_R.sA(rss[i], "origindex", _R[id].realslideamount - 1);
			}
		}
		for (i in slidestoremove)
			if (slidestoremove.hasOwnProperty(i)) slidestoremove[i].remove();

		for (i in _R[id].notInNav)
			if (_R[id].notInNav.hasOwnProperty(i)) {
				_R.sA(_R[id].notInNav[i], 'not_in_nav', true);
				_R[id].canvas[0].appendChild(_R[id].notInNav[i]);
			}

		_R[id].canvas.css({ visibility: "visible" });
		_R[id].slayers = _R[id].c.find('rs-static-layers');
		if (_R[id].slayers.length > 0) _R.sA(_R[id].slayers[0], 'key', 'staticlayers');
		if (_R[id].modal.useAsModal === true) {
			_R[id].cpar.wrap('<rs-modal id="' + (_R[id].c[0].id + "_modal") + '"></rs-modal>');
			_R[id].modal.c = jQuery(_R.closestNode(_R[id].cpar[0], 'RS-MODAL')) /*_R[id].cpar.closest('rs-modal')*/ ;
			_R[id].modal.c.appendTo(jQuery('body'));
			if (_R[id].modal !== undefined && _R[id].modal.alias !== undefined) _R.revModal(id, { mode: "init" });
		}
		if (_R[id].waitForInit == true || _R[id].modal.useAsModal == true) {
			if (_R.RS_toInit !== undefined) _R.RS_toInit[id] = true;
			_R[id].c.trigger('revolution.slide.waitingforinit');
			_R[id].waitingForInit = true;
			return;
		} else window.requestAnimationFrame(function() {
			runSlider(id);
		});
		// Some Addon Decides at which point the Slider Process is based on the following Variable
		_R[id].initEnded = true;
	};

	var ofsc = function() {
		jQuery("body").data('rs-fullScreenMode', !jQuery("body").data('rs-fullScreenMode'));
		if (jQuery("body").data('rs-fullScreenMode')) {
			setTimeout(function() {
				_R.window.trigger("resize");
			}, 200);
		}
	};

	var deliverLazy = function(e, def, id) {
		return _R.gA(e, "lazyload") !== undefined ? _R.gA(e, "lazyload") : // INTERNAL LAZY LOADING
			_R[id].lazyloaddata !== undefined && _R[id].lazyloaddata.length > 0 && _R.gA(e, _R[id].lazyloaddata) !== undefined ? _R.gA(e, _R[id].lazyloaddata) : // CUSTOM DATA
			_R.gA(e, "lazy-src") !== undefined ? _R.gA(e, "lazy-src") : //WP ROCKET
			_R.gA(e, "lazy-wpfc-original-src") !== undefined ? _R.gA(e, "lazy-wpfc-original-src") : //WP Fastes Cache Premium
			_R.gA(e, "lazy") !== undefined ? _R.gA(e, "lazy") : // LAZY
			def; // DEFAULT
	}




	var runSlider = function(id) {
		if (_R[id] === undefined) return;

		_R[id].sliderisrunning = true;

		// REMOVE SLIDER MODULE FOR A WHILE
		if (_R[id].noDetach !== true) _R[id].c.detach();

		// RANDOMIZE THE SLIDER SHUFFLE MODE
		if (_R[id].shuffle) {
			var fli = _R[id].canvas.find('rs-slide:first-child'),
				fsa = _R.gA(fli[0], "firstanim");
			for (var u = 0; u < _R[id].slideamount; u++) _R[id].canvas.find('rs-slide:eq(' + Math.round(Math.random() * _R[id].slideamount) + ')').prependTo(_R[id].canvas);
			_R.sA(_R[id].canvas.find('rs-slide:first-child')[0], "firstanim", fsa);
		}

		// COLLECT ALL LI INTO AN ARRAY
		_R[id].slides = _R.getByTag(_R[id].canvas[0], "RS-SLIDE");
		_R[id].thumbs = new Array(_R[id].slides.length);
		_R[id].slots = 1;
		_R[id].firststart = 1;
		_R[id].loadqueue = [];
		_R[id].syncload = 0;

		var aindex = 0,
			carborder = _R[id].sliderType === "carousel" && _R[id].carousel.border_radius !== undefined ? parseInt(_R[id].carousel.border_radius, 0) : 0;



		for (var index in _R[id].slides) {
			if (!_R[id].slides.hasOwnProperty(index) || index === "length") continue;

			var slide = _R[id].slides[index],
				img = _R.getByTag(slide, 'IMG')[0];

			if (_R.gA(slide, "key") === undefined) _R.sA(slide, "key", 'rs-' + Math.round(Math.random() * 999999));

			var obj = { params: Array(12), id: _R.gA(slide, "key"), src: _R.gA(slide, "thumb") !== undefined ? _R.gA(slide, "thumb") : deliverLazy(img, img !== undefined ? img.src : undefined, id) };
			if (_R.gA(slide, "title") === undefined) _R.sA(slide, "title", "");
			if (_R.gA(slide, "description") === undefined) _R.sA(slide, "description", "");
			obj.params[0] = { from: RegExp("\\{\\{title\\}\\}", "g"), to: _R.gA(slide, "title") };
			obj.params[1] = { from: RegExp("\\{\\{description\\}\\}", "g"), to: _R.gA(slide, "description") };
			for (var i = 1; i <= 10; i++)
				if (_R.gA(slide, "p" + i) !== undefined)
					obj.params[i + 1] = { from: RegExp("\\{\\{param" + i + "\\}\\}", "g"), to: _R.gA(slide, "p" + i) };
				else
					obj.params[i + 1] = { from: RegExp("\\{\\{param" + i + "\\}\\}", "g"), to: "" };


			_R[id].thumbs[aindex] = jQuery.extend({}, true, obj);

			if (carborder > 0) tpGS.gsap.set(slide, { borderRadius: carborder + "px" });

			// IF LINK ON SLIDE EXISTS, NEED TO CREATE A PROPER LAYER FOR IT.
			if (_R.gA(slide, "link") != undefined || _R.gA(slide, "linktoslide") !== undefined) {

				var link = _R.gA(slide, "link") !== undefined ? _R.gA(slide, "link") : "slide",
					linktoslide = link != "slide" ? "no" : _R.gA(slide, "linktoslide"),
					seoz = _R.gA(slide, "seoz");

				if (linktoslide != undefined && linktoslide != "no" && linktoslide != "next" && linktoslide != "prev") {
					for (var ris in _R[id].slides) {
						if (!_R[id].slides.hasOwnProperty(ris)) continue;
						if (parseInt(_R.gA(_R[id].slides[ris], "origindex"), 0) + 1 == _R.gA(slide, "linktoslide")) linktoslide = _R.gA(_R[id].slides[ris], "key");
					}
				}

				jQuery(slide).prepend('<rs-layer class="rs-layer slidelink" id="rs_slidelink_' + Math.round(Math.random() * 100000) + '" data-zindex="' + (seoz === "back" ? 0 : seoz === "front" ? 95 : seoz !== undefined ? parseInt(seoz, 0) : 100) + '" dataxy="x:c;y:c" data-dim="w:100%;h:100%" data-basealign="slide"' +
					(linktoslide == "no" ? link != "slide" && !_R.ISM ? "  data-actions=\'" + 'o:click;a:simplelink;target:' + (_R.gA(slide, "target") || "_self") + ';url:' + link + ';' + "\'" : "" : "  data-actions=\'" + (linktoslide === "scroll_under" ? 'o:click;a:scrollbelow;offset:100px;' : linktoslide === "prev" ? 'o:click;a:jumptoslide;slide:prev;d:0.2;' : linktoslide === "next" ? 'o:click;a:jumptoslide;slide:next;d:0.2;' : 'o:click;a:jumptoslide;slide:' + linktoslide + ';d:0.2;') + "\'") +
					" data-frame_1='e:power3.inOut;st:100;sp:100' data-frame_999='e:power3.inOut;o:0;st:w;sp:100'>" +
					(_R.ISM ? "<a " + (link != "slide" ? (_R.gA(slide, "target") === "_blank" ? 'rel="noopener" ' : '') + 'target="' + (_R.gA(slide, "target") || "_self") + '" href="' + link + '"' : '') + "><span></span></a>" : "") +
					"</rs-layer>");
			}
			aindex++;
		}



		// SIMPLIFY ANIMATIONS ON OLD IOS AND IE8 IF NEEDED
		if (_R[id].simplifyAll && (_R.isIE(8) || _R.iOSVersion())) {
			_R[id].c.find('.rs-layer').each(function() {
				var tc = jQuery(this);
				tc.removeClass("customin customout").addClass("fadein fadeout");
				tc.data('splitin', "");
				tc.data('speed', 400);
			});
			_R[id].c.find('rs-slide').each(function() {
				var li = jQuery(this);
				li.data('transition', "fade");
				li.data('masterspeed', 500);
				li.data('slotamount', 1);
				var img = li.find('.rev-slidebg') || li.find('>img').first();

				img.data('panzoom', null);
			});
		}

		window._rs_desktop = window._rs_desktop === undefined ? !navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry|BB10|mobi|tablet|opera mini|nexus 7)/i) : window._rs_desktop;

		// SOME _R[id]IONS WHICH SHOULD CLOSE OUT SOME OTHER SETTINGS
		_R[id].autoHeight = _R[id].sliderLayout == "fullscreen" ? true : _R[id].autoHeight;

		if (_R[id].sliderLayout == "fullwidth" && !_R[id].autoHeight) _R[id].c.css({ maxHeight: _R[id].gridheight[_R[id].level] + "px" });



		// BUILD A FORCE FULLWIDTH CONTAINER, TO SPAN THE FULL SLIDER TO THE FULL WIDTH OF BROWSER
		if (_R[id].sliderLayout != "auto" && _R.closestNode(_R[id].c[0], 'RS-FULLWIDTH-WRAP') === null /*_R[id].c.closest('rs-fullwidth-wrap').length==0*/ && (_R[id].sliderLayout !== "fullscreen" || _R[id].disableForceFullWidth != true)) {
			var mt = _R[id].cpar[0].style.marginTop,
				mb = _R[id].cpar[0].style.marginBottom;
			mt = mt === undefined || mt === "" ? "" : "margin-top:" + mt + ";";
			mb = mb === undefined || mb === "" ? "" : "margin-bottom:" + mb + ";";
			_R[id].rsFullWidthWrap = _R[id].topc = jQuery('<rs-fullwidth-wrap id="' + (_R[id].c[0].id + "_forcefullwidth") + '" style="' + mt + mb + '"></rs-fullwidth-wrap>');

			_R[id].forcer = jQuery('<rs-fw-forcer style="height:' + (_R[id].forcerHeight === undefined ? _R[id].cpar.height() : _R[id].forcerHeight) + 'px"></rs-fw-forcer>');
			_R[id].topc.append(_R[id].forcer);
			_R[id].topc.insertBefore(_R[id].cpar);
			_R[id].cpar.detach();
			_R[id].cpar.css({ marginTop: "0px", marginBottom: "0px", position: 'absolute' });
			_R[id].cpar.prependTo(_R[id].topc);
		} else
			_R[id].topc = _R[id].cpar;



		// MENU MODE AND SIMILIAR FUN
		if (_R[id].forceOverflow) _R[id].topc[0].classList.add("rs-forceoverflow");
		if (_R[id].sliderType === "carousel" && _R[id].overflowHidden !== true) _R[id].c.css({ overflow: "visible" });
		if (_R[id].maxHeight !== 0) tpGS.gsap.set([_R[id].cpar, _R[id].c, _R[id].topc], { maxHeight: _R[id].maxHeight + "px" });
		if (_R[id].fixedOnTop) tpGS.gsap.set(_R[id].blockSpacing !== undefined && _R[id].blockSpacing.block !== undefined ? _R[id].blockSpacing.block : _R[id].topc, { position: "fixed", top: "0px", left: "0px", pointerEvents: "none", zIndex: 5000 });



		// SHADOW ADD ONS
		if (_R[id].shadow !== undefined && _R[id].shadow > 0) _R[id].cpar.addClass('tp-shadow' + _R[id].shadow).append('<div class="tp-shadowcover" style="background-color:' + _R[id].cpar.css('backgroundColor') + ';background-image:' + _R[id].cpar.css('backgroundImage') + '"></div>');

		// ESTIMATE THE CURRENT WINDOWS RANGE INDEX
		_R.updateDims(id, "prepared");

		// Observe Wrapper Dimension changes
		if (_R.observeWraps === undefined) _R.observeWraps = new _R.wrapObserver.init(function(elem, id) {
			containerResized(id, undefined, true);
		});

		// IF THE CONTAINER IS NOT YET INITIALISED, LETS GO FOR IT
		if (!_R[id].c.hasClass("revslider-initialised")) {

			// MARK THAT THE CONTAINER IS INITIALISED WITH SLIDER REVOLUTION ALREADY
			_R[id].c[0].classList.add("revslider-initialised");

			// WE DONT HAVE ANY ID YET ? WE NEED ONE ! LETS GIVE ONE RANDOMLY FOR RUNTIME
			_R[id].c[0].id = _R[id].c[0].id === undefined ? "revslider-" + Math.round(Math.random() * 1000 + 5) : _R[id].c[0].id;
			_R.revCheckIDS(id, _R[id].c[0]);

			_R[id].origcd = parseInt(_R[id].duration, 0);


			//PREPARING FADE IN/OUT PARALLAX
			_R[id].scrolleffect._L = [];
			_R[id].sbas = _R[id].sbas === undefined ? {} : _R[id].sbas;
			_R[id].layers = _R[id].layers || {};
			_R[id].sortedLayers = _R[id].sortedLayers || {};


			var elements = _R[id].c[0].querySelectorAll('rs-layer, rs-row, rs-column, rs-group,  rs-bgvideo, .rs-layer');

			/*_R[id].c.find('rs-layer, rs-row, rs-column, rs-group,  rs-bgvideo, .rs-layer').each(function() {*/
			for (var _eli_ in elements) {
				if (!elements.hasOwnProperty(_eli_)) continue;
				var _nc = jQuery(elements[_eli_]),
					_ = _nc.data(),
					s, v;
				_.startclasses = elements[_eli_].className;
				_.startclasses = _.startclasses === undefined || _.startclasses === null ? "" : _.startclasses;
				_.animationonscroll = _R[id].sbtimeline.set ? _R[id].sbtimeline.layers : false;
				_.animationonscroll = _.animationonscroll === true || _.animationonscroll == 'true' ? true : false;
				_.filteronscroll = _R[id].scrolleffect.set ? _R[id].scrolleffect.layers : false;
				_.pxundermask = _.startclasses.indexOf('rs-pxmask') >= 0 && _R[id].parallax.type !== 'off' && _.startclasses.indexOf('rs-pxl-') >= 0;
				_.noPevents = _.startclasses.indexOf('rs-noevents') >= 0;

				//CATCH SCROLLEFFECT AND TIMELINE SCROLL BASE
				if (_.sba) {
					s = _.sba.split(";");
					for (var i in s) {
						if (!s.hasOwnProperty(i)) continue;
						v = s[i].split(":");
						if (v[0] == "t") { _.animationonscroll = v[1]; if (v[1] == "false") _.animOnScrollForceDisable = true; }
						if (v[0] == "e") _.filteronscroll = v[1];
						if (v[0] == "so") _.scrollBasedOffset = parseInt(v[1]) / 1000;
					}
				}
				if (_.animationonscroll == "true" || _.animationonscroll == true) {
					_.startclasses += " rs-sba";
					_nc[0].className += " rs-sba";
				}
				if (_.startclasses.indexOf("rs-layer-static") >= 0 && _R.handleStaticLayers) _R.handleStaticLayers(_nc, id);
				if (_nc[0].tagName !== "RS-BGVIDEO") {
					_nc[0].classList.add("rs-layer");
					if (_.type === "column") {
						_.columnwidth = "33.33%";
						_.verticalalign = "top";
						if (_.column !== undefined) {
							s = _.column.split(";");
							for (var ci in s) {
								if (!s.hasOwnProperty(ci)) continue;
								v = s[ci].split(":");
								if (v[0] === "w") _.columnwidth = v[1];
								if (v[0] === "a") _.verticalalign = v[1];
							}
						}
					}
					// PREPARE LAYERS AND WRAP THEM WITH PARALLAX, LOOP, MASK HELP CONTAINERS
					var ec = _.startclasses.indexOf("slidelink") >= 0 ? "z-index:" + _.zindex + ";width:100% !important;height:100% !important;" : "",
						specec = _.type !== "column" ? "" : _.verticalalign === undefined ? " vertical-align:top;" : " vertical-align:" + _.verticalalign + ";",
						_pos = _.type === "row" || _.type === "column" ? "position:relative;" : "position:absolute;",
						preclas = "",
						pretag = _.type === "row" ? "rs-row-wrap" : _.type === "column" ? "rs-column-wrap" : _.type === "group" ? "rs-group-wrap" : "rs-layer-wrap",
						dmode = "",
						preid = "",
						pevents = _.noPevents ? ';pointer-events:none' : ';pointer-events:none';

					if (_.type === "row" || _.type === "column" || _.type === "group") {
						_nc[0].classList.remove("tp-resizeme");
						if (_.type === "column") {
							_.width = "auto";
							_nc[0].group = "row";
							tpGS.gsap.set(_nc, { width: 'auto' });
							_.filteronscroll = false;
						}
					} else {
						dmode = "display:" + (_nc[0].style.display === "inline-block" ? "inline-block" : "block") + ";";
						if (_R.closestNode(_nc[0], 'RS-COLUMN') !== null /*nc.closest('rs-column').length>0*/ ) {
							_nc[0].group = "column";

							_.filteronscroll = false;
						} else
						if (_R.closestNode(_nc[0], 'RS-GROUP-WRAP') !== null /*_nc.closest('rs-group-wrap').length>0*/ ) {
							_nc[0].group = "group";
							_.filteronscroll = false;
						}
					}

					if (_.wrpcls !== undefined) preclas = preclas + " " + _.wrpcls;
					if (_.wrpid !== undefined) preid = 'id="' + _.wrpid + '"';

					//WRAP LAYER
					_nc.wrap('<' + pretag + ' ' + preid + ' class="rs-parallax-wrap ' + preclas + '" style="' + specec + ' ' + ec + _pos + dmode + '' + pevents + '">' +
						'<rs-loop-wrap style="' + ec + _pos + dmode + '">' +
						'<rs-mask-wrap style="' + ec + _pos + dmode + '">' +
						(_.pxundermask ? '<rs-px-mask></rs-px-mask>' : '') +
						'</rs-mask-wrap>' +
						'</rs-loop-wrap>' +
						'</' + pretag + '>');



					// ONLY ADD LAYERS TO FADEOUT DYNAMIC LIST WHC

					if (_.filteronscroll === true || _.filteronscroll == 'true') _R[id].scrolleffect._L.push(_nc.parent());

					_nc[0].id = _nc[0].id === undefined ? 'layer-' + Math.round(Math.random() * 999999999) : _nc[0].id;
					_R.revCheckIDS(id, _nc[0]);

					// CACHE PARENT ELEMENTS
					if (_.pxundermask) {
						_R[id]._Lshortcuts[_nc[0].id] = {
							p: jQuery(_nc[0].parentNode.parentNode.parentNode.parentNode),
							lp: jQuery(_nc[0].parentNode.parentNode.parentNode),
							m: jQuery(_nc[0].parentNode.parentNode)
						}
					} else {
						_R[id]._Lshortcuts[_nc[0].id] = {
							p: jQuery(_nc[0].parentNode.parentNode.parentNode),
							lp: jQuery(_nc[0].parentNode.parentNode),
							m: jQuery(_nc[0].parentNode)
						}
					}


					// Add BG for Columns
					if (_.type === "column") _R[id]._Lshortcuts[_nc[0].id].p.append('<rs-cbg-mask-wrap><rs-column-bg id="' + _nc[0].id + '_rs_cbg"></rs-column-bg></rs-cbg-mask-wrap>');
					if (_.type === "text" && _R.getByTag(_nc[0], 'IFRAME').length > 0) {
						_R[id].slideHasIframe = true;
						_nc[0].classList.add('rs-ii-o'); //inner iframe ok
					}

					//tpGS.gsap.set(_nc,{visibility:"hidden"});
					if (_R[id].BUG_safari_clipPath && _.animationonscroll != "true" && _.animationonscroll != true) _nc[0].classList.add("rs-pelock");

					if (_nc[0].dataset.staticz !== undefined && _.type !== "row" && _nc[0].group !== "row" && _nc[0].group !== "column") _R.addStaticLayerTo(id, _nc[0].dataset.staticz, _R[id]._Lshortcuts[_nc[0].id].p);

				}

				// INITISLIASE THE EVENTS ON LAYERS

				if (_R.gA(_nc[0], "actions") && _R.checkActions) _R.checkActions(_nc, id, _R[id]);
				if (_R.checkVideoApis && (!window.rs_addedvim || !window.rs_addedyt) && (!_R[id].youtubeapineeded || !_R[id].vimeoapineeded)) _R.checkVideoApis(_nc, id);

			}

			if (_R.checkActions) _R.checkActions(undefined, id);

			// Wait for 1ST Click to play Videos in case they could not be played yet
			_R[id].c[0].addEventListener('mousedown', function() {
				if (_R[id].onceClicked !== true) {
					_R[id].onceClicked = true;
					if (_R[id].onceVideoPlayed !== true && _R[id].activeRSSlide !== undefined && _R[id].slides !== undefined && _R[id].slides[_R[id].activeRSSlide] !== undefined) {
						var nc = jQuery(_R[id].slides[_R[id].activeRSSlide]).find('rs-bgvideo');
						if (nc !== undefined && nc !== null && nc.length > 0) _R.playVideo(nc, id);
					}
				}
			});


			_R[id].c[0].addEventListener('mouseenter', function() {
				_R[id].c.trigger('tp-mouseenter');
				_R[id].overcontainer = true;
			}, { passive: true });

			_R[id].c[0].addEventListener('mouseover', function() {
				_R[id].c.trigger('tp-mouseover');
				_R[id].overcontainer = true;
			}, { passive: true });

			_R[id].c[0].addEventListener('mouseleave', function() {
				_R[id].c.trigger('tp-mouseleft');
				_R[id].overcontainer = false;
			}, { passive: true });

			// REMOVE ANY VIDEO JS SETTINGS OF THE VIDEO  IF NEEDED  (OLD FALL BACK, AND HELP FOR 3THD PARTY PLUGIN CONFLICTS)
			_R[id].c.find('.rs-layer video').each(function(i) {
				var v = jQuery(this);
				v.removeClass("video-js vjs-default-skin");
				v.attr("preload", "");
				v.css({ display: "none" });
			});

			//PREPARE LOADINGS ALL IN SEQUENCE
			//if (_R[id].sliderType!=="standard") _R[id].lazyType = "all";

			// PRELOAD STATIC LAYERS
			_R[id].rs_static_layer = _R.getByTag(_R[id].c[0], 'RS-STATIC-LAYERS');
			if (_R.preLoadAudio && _R[id].rs_static_layer.length > 0) _R.preLoadAudio(jQuery(_R[id].rs_static_layer), id, 1);
			if (_R[id].rs_static_layer.length > 0) {
				_R.loadImages(_R[id].rs_static_layer[0], id, 0, true);
				_R.waitForCurrentImages(_R[id].rs_static_layer[0], id, function() {
					if (_R[id] === undefined) return;
					_R[id].c.find('rs-static-layers img').each(function() {
						this.src = _R.getLoadObj(id, (_R.gA(this, "src") != undefined ? _R.gA(this, "src") : this.src)).src;
					});
				});
			}

			_R[id].rowzones = [];
			_R[id].rowzonesHeights = [];
			_R[id].topZones = [];
			_R[id].middleZones = [];
			_R[id].bottomZones = [];



			// IF DEEPLINK HAS BEEN SET
			var deeplink = _R.deepLink(id, getUrlVars("#")[0]);
			if (deeplink !== undefined) {
				_R[id].startWithSlide = deeplink;
				_R[id].deepLinkListener = true;
				window.addEventListener('hashchange', function() {
					if (_R[id].ignoreDeeplinkChange !== true) {
						var deeplink = _R.deepLink(id, getUrlVars("#")[0]);
						if (deeplink !== undefined) _R.callingNewSlide(id, deeplink, true);
					}
					_R[id].ignoreDeeplinkChange = false;
				});
			}


			// PREPARE THE SPINNER
			_R[id].loader = _R.buildSpinner(id, _R[id].spinner, _R[id].spinnerclr);
			_R[id].loaderVisible = true;
			_R[id].c.append(_R[id].loader);

			// PREPARE THE SLIDES
			prepareSlides(id);

			if ((_R[id].parallax.type !== "off" || _R[id].scrolleffect.set || _R[id].sbtimeline.set) && _R.checkForParallax) _R.checkForParallax(id);

			// INIT BLURFOCUS
			if (!_R[id].fallbacks.disableFocusListener && _R[id].fallbacks.disableFocusListener != "true" && _R[id].fallbacks.disableFocusListener !== true) {
				_R[id].c.addClass("rev_redraw_on_blurfocus");
				tabBlurringCheck();
			}

			// MANAGE VIEWPORT SETTINGS
			var _v = _R[id].viewPort;
			if (_R[id].navigation.mouseScrollNavigation === "on") _v.enable = true;


			// SET ALL LI AN INDEX AND INIT LAZY LOADING
			for (var i in _R[id].slides) {
				if (!_R[id].slides.hasOwnProperty(i)) continue;
				var li = jQuery(_R[id].slides[i]);
				_R[id].rowzones[i] = [];
				_R[id].rowzonesHeights[i] = [];
				_R[id].topZones[i] = [];
				_R[id].middleZones[i] = [];
				_R[id].bottomZones[i] = [];
				li.find('rs-zone').each(function() {
					_R[id].rowzones[i].push(jQuery(this));
					if (this.className.indexOf("rev_row_zone_top") >= 0) _R[id].topZones[i].push(this);
					if (this.className.indexOf("rev_row_zone_middle") >= 0) _R[id].middleZones[i].push(this);
					if (this.className.indexOf("rev_row_zone_bottom") >= 0) _R[id].bottomZones[i].push(this);
				});
			}

			//LAZY LOAD ALL IMAGES
			_R.lazyLoadAllSlides(id);


			// COLLECT STATIC ROWS, ZONES
			_R[id].srowzones = [];
			_R[id].smiddleZones = [];
			if (_R[id].slayers)
				_R[id].slayers.find('rs-zone').each(function() {
					_R[id].srowzones.push(jQuery(this));
					if (this.className.indexOf("rev_row_zone_middle") >= 0) _R[id].smiddleZones.push(this);
				});

			if (_R[id].sliderType === "carousel") tpGS.gsap.set(_R[id].canvas, { scale: 1, perspective: 1200, transformStyle: "flat", opacity: 0 });



			// APPEND MODULE CONTAINER BACK
			_R[id].c.prependTo(_R[id].cpar);

			/******************************
				-	FULLSCREEN CHANGE	-
			********************************/
			// FULLSCREEN MODE TESTING
			jQuery("body").data('rs-fullScreenMode', false);
			window.addEventListener('fullscreenchange', ofsc, { passive: true });
			window.addEventListener('mozfullscreenchange', ofsc, { passive: true });
			window.addEventListener('webkitfullscreenchange', ofsc, { passive: true });

			_R.document.on("updateContainerSizes." + _R[id].c.attr('id'), function() {
				if (_R[id] === undefined) return;
				else if (_R[id].c == undefined) return false;
				if (_R.updateDims(id, 'ignore'))
					window.requestAnimationFrame(function() {
						_R.updateDims(id, 'ignore');
						_R[id].fullScreenMode = _R.checkfullscreenEnabled(id);
						_R.lastwindowheight = _R.getWinH(id);
						containerResized(id);
					});
			});


			if (_v.presize) {
				_R[id].pr_next_slide = jQuery(_R[id].slides[0]);
				// PRELOAD STATIC LAYERS
				_R.loadImages(_R[id].pr_next_slide[0], id, 0, true);
				_R.waitForCurrentImages(_R[id].pr_next_slide.find('.tp-layers'), id, function() { if (_R.animateTheLayers) _R.animateTheLayers({ slide: _R[id].pr_next_key, id: id, mode: "preset", caller: "runSlider" }); });
			}

			//START SLIDER AND/OR SCROLL HANDLER
			if (_R[id].parallax.type != "off" || _R[id].sbtimeline.set || _v.enable === true) _R.scrollTicker(id);
			if (_v.enable !== true) {
				_R[id].inviewport = true;
				_R.enterViewPort(id);
			}

			if (_R.RS_toInit !== undefined) _R.RS_toInit[id] = true;

			// ADD WRAPPER OBSERVER
			if (_R[id].observeWrap && _R.observeWraps) _R.wrapObserver.observe(_R[id].rsFullWidthWrap !== undefined ? _R[id].rsFullWidthWrap[0] : _R[id].cpar[0], id);

		}
	};






	var hideSliderUnder = function(id, resized) {
		if (_R.winW < _R[id].hideSliderAtLimit) {
			_R[id].c.trigger('stoptimer');
			if (_R[id].sliderIsHidden !== true) {
				_R.sA(_R[id].cpar[0], "displaycache", _R[id].cpar.css('display') != "none" ? _R[id].cpar.css('display') : _R.gA(_R[id].cpar[0], "displaycache"));
				_R[id].cpar.css({ display: "none" });
				_R[id].sliderIsHidden = true;
			}
		} else {
			if ((_R[id].sliderIsHidden === true || (_R[id].sliderIsHidden === undefined && _R[id].c.is(":hidden"))) && resized) {
				_R[id].cpar[0].style.display = _R.gA(_R[id].cpar[0], "displaycache") != undefined && _R.gA(_R[id].cpar[0], "displaycache") != "none" ? _R.gA(_R[id].cpar[0], "displaycache") : "block";
				_R[id].sliderIsHidden = false;
				_R[id].c.trigger('restarttimer');

				window.requestAnimationFrame(function() {
					containerResized(id, true);
				});
			}
		}
		if (_R.hideUnHideNav && _R[id].navigation.use) _R.hideUnHideNav(id);
	};


	//////////////////////////
	//	CONTAINER RESIZED	//
	/////////////////////////
	var containerResized = function(id, ignoreHideSlideUnder, updateDim) {
		if (_R[id].c === undefined) return false;
		_R[id].dimensionReCheck = {};

		_R[id].c.trigger('revolution.slide.beforeredraw');
		if (_R[id].infullscreenmode == true) _R[id].minHeight = _R.getWinH(id);
		if (_R.ISM) _R[id].lastMobileHeight = _R.getWinH(id);

		if (updateDim) _R.updateDims(id);
		if (!_R.resizeThumbsTabs || _R.resizeThumbsTabs(id) === true) {
			window.requestAnimationFrame(function() {
				hideSliderUnder(id, ignoreHideSlideUnder !== true);
				buildProgressBar(id);
			});

			if (_R[id].started) {
				if (_R[id].sliderType == "carousel") {
					_R.prepareCarousel(id);
					for (var nbgi in _R[id].sbgs)
						if (_R[id].sbgs.hasOwnProperty(nbgi) && _R[id].sbgs[nbgi].mDIM !== undefined) {
							_R.updateSlideBGs(id, _R[id].sbgs[nbgi].key, _R[id].sbgs[nbgi]);
							//_R.prepareCoveredVideo(id,_R[id].sbgs[nbgi].skeyindex,_R[id].sbgs[nbgi].key)
						}
				} else {
					_R.updateSlideBGs(id);
				}

				if (_R[id].sliderType === "carousel" && _R[id].carCheckconW != _R[id].canv.width) {
					clearTimeout(_R[id].pcartimer);
					for (var i in _R[id].sbgs)
						if (_R[id].sbgs[i].loadobj !== undefined) _R.updateSlideBGs(id, _R[id].sbgs[i].key, _R[id].sbgs[i]);

					_R[id].pcartimer = setTimeout(function() {
						_R.prepareCarousel(id);
						_R.animateTheLayers({ slide: "individual", id: id, mode: "rebuild", caller: "containerResized_1" });
						_R[id].carCheckconW = _R[id].canv.width;
					}, 100);
					_R[id].lastconw = _R[id].canv.width;
				}


				// DOUBLE CALL FOR SOME FUNCTION TO AVOID PORTRAIT/LANDSCAPE ISSUES, AND TO AVOID FULLSCREEN/NORMAL SWAP ISSUES
				if (_R[id].pr_processing_key !== undefined) _R.animateTheLayers({ slide: _R[id].pr_processing_key, id: id, mode: "rebuild", caller: "containerResized_2" });
				else if (_R[id].pr_active_key !== undefined) _R.animateTheLayers({ slide: _R[id].pr_active_key, id: id, mode: "rebuild", caller: "containerResized_3" });

				//Pan Zoom Calls
				if (_R[id].sliderType === "carousel") {
					for (var i in _R[id].panzoomTLs)
						if (_R[id].panzoomTLs.hasOwnProperty(i)) {
							var key = _R.gA(_R[id].panzoomBGs[i][0], "key");
							_R.startPanZoom(_R[id].panzoomBGs[i], id, _R[id].panzoomTLs[i].progress(), i, _R[id].panzoomTLs[i].isActive() ? 'play' : 'reset', key);
						}
				} else {
					if (_R[id].pr_active_bg !== undefined && _R[id].pr_active_bg[0] !== undefined) pzrpt(id, _R[id].pr_active_bg, _R[id].pr_active_bg[0].dataset.key);
					if (_R[id].pr_next_bg !== undefined && _R[id].pr_next_bg[0] !== undefined) pzrpt(id, _R[id].pr_next_bg, _R[id].pr_next_bg[0].dataset.key);
				}

				clearTimeout(_R[id].mNavigTimeout);
				if (_R.manageNavigation) _R[id].mNavigTimeout = setTimeout(function() { _R.manageNavigation(id); }, 20);
			}

			_R.prepareCoveredVideo(id);
		}

		_R[id].c.trigger('revolution.slide.afterdraw', [{ id: id }]);

	};

	var pzrpt = function(id, a, key) {
		if (_R[id].panzoomTLs === undefined) return;
		var cid = _R.getSlideIndex(id, key);
		_R.startPanZoom(a, id, (_R[id].panzoomTLs[cid] !== undefined ? _R[id].panzoomTLs[cid].progress() : 0), cid, 'play', key);
	};

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////       PREPARING / REMOVING		////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/////////////////////////////////////////
	//	-	PREPARE THE SLIDES / SLOTS -  //
	///////////////////////////////////////
	var prepareSlides = function(id) {
		//REMOVE CANVAS FROM DOM UNTIL MODIFICATION

		if (_R[id].noDetach !== true) _R[id].canvas.detach();
		var overflow = _R.isFaceBook() ? 'visible' : 'hidden';


		// PREPARE THE UL CONTAINER TO HAVEING MAX HEIGHT AND HEIGHT FOR ANY SITUATION
		if (_R[id].autoHeight) tpGS.gsap.set([_R[id].c, _R[id].cpar], { maxHeight: 'none' });

		tpGS.gsap.set(_R[id].canvas, (_R[id].modal !== undefined && _R[id].modal.useAsModal ? { overflow: overflow, width: '100%', height: '100%' } : { overflow: overflow, width: '100%', height: '100%', maxHeight: _R[id].autoHeight ? 'none' : _R[id].cpar.css('maxHeight') }));
		//SET CAROUSEL
		if (_R[id].sliderType === "carousel") {
			var carsty = "margin-top:" + parseInt((_R[id].carousel.padding_top || 0), 0) + "px;";
			_R[id].canvas.css({ overflow: "visible" }).wrap('<rs-carousel-wrap style="' + carsty + '"></rs-carousel-wrap>');
			_R[id].cpar.prepend('<rs-carousel-space></rs-carousel-space>').append('<rs-carousel-space></rs-carousel-space>');
			_R.defineCarouselElements(id);
		}
		//Number od Start with should never be smaller then 0 !
		_R[id].startWithSlide = _R[id].startWithSlide === undefined ? undefined : Math.max(1, _R[id].sliderType === "carousel" ? parseInt(_R[id].startWithSlide) : parseInt(_R[id].startWithSlide));
		// RESOLVE OVERFLOW HIDDEN OF MAIN CONTAINER
		_R[id].cpar.css({ 'overflow': 'visible' });

		//SCROLL BASED BG COLLECTION
		_R[id].scrolleffect.bgs = [];


		for (var i = 0; i < _R[id].slides.length; i++) {
			var cli = jQuery(_R[id].slides[i]),
				key = _R.gA(cli[0], 'key'),
				img = cli.find('.rev-slidebg') || cli.find('>img'),
				B = _R[id].sbgs[key] = getBGValues(img.data(), id),
				mf = cli.data('mediafilter');

			B.skeyindex = _R.getSlideIndex(id, key);
			B.bgvid = cli.find('rs-bgvideo');
			img.detach();
			B.bgvid.detach();

			//START WITH CORRECT SLIDE
			if ((_R[id].startWithSlide != undefined && _R.gA(_R[id].slides[i], "originalindex") == _R[id].startWithSlide) || _R[id].startWithSlide === undefined && i == 0) _R[id].pr_next_key = cli.index();

			tpGS.gsap.set(cli, { width: '100%', height: '100%', overflow: overflow });
			img.wrap('<rs-sbg-px><rs-sbg-wrap data-key="' + key + '"></rs-sbg-wrap></rs-sbg-px>');

			B.wrap = jQuery(_R.closestNode(img[0], 'RS-SBG-WRAP'));
			B.src = img[0].src;
			B.lazyload = B.lazyload = deliverLazy(img[0], undefined, id);
			B.slidebgimage = true;
			B.loadobj = B.loadobj === undefined ? {} : B.loadobj;
			B.mediafilter = mf = mf === "none" || mf === undefined ? "" : mf;
			B.sbg = document.createElement('rs-sbg');




			if (_R[id].overlay !== undefined && _R[id].overlay.type != "none" && _R[id].overlay.type != undefined) {
				var url = _R.createOverlay(id, _R[id].overlay.type, _R[id].overlay.size, { 0: _R[id].overlay.colora, 1: _R[id].overlay.colorb });
				B.wrap.append('<rs-dotted style="background-image:' + url + '"></rs-dotted>');
			}

			img.data('mediafilter', mf);
			//mf = img.data('panzoom')!==undefined ? "" : mf;


			B.canvas = document.createElement('canvas');
			B.sbg.appendChild(B.canvas);
			B.canvas.style.width = "100%";
			B.canvas.style.height = "100%";
			B.ctx = B.canvas.getContext('2d');

			if (B.lazyload !== undefined) B.sbg.dataset.lazyload = B.lazyload;
			B.sbg.className = mf;
			B.sbg.src = B.src;
			B.sbg.dataset.bgcolor = B.bgcolor;


			/*if (!(B.bgcolor!==undefined && B.bgcolor.indexOf('gradient')>=0)) {
				B.sbg.style.backgroundRepeat = B.bgrepeat;
				B.sbg.style.backgroundImage = 'url('+B.src+')';
				B.sbg.style.backgroundSize = B.bgfit;
				B.sbg.style.backgroundPosition = B.bgposition;
			}*/
			//if (_R[id].sliderType === "standard" || _R[id].sliderType==="undefined" ) B.sbg.style.opacity = 0;
			B.sbg.style.width = "100%";
			B.sbg.style.height = "100%";
			B.key = key;
			B.wrap[0].dataset.key = key;
			jQuery(B.sbg).data(B);
			B.wrap.data(B);

			B.wrap[0].appendChild(B.sbg);

			var comment = document.createComment("Runtime Modification - Img tag is Still Available for SEO Goals in Source - " + img.get(0).outerHTML);
			img.replaceWith(comment);
			//img[0].style.display = "none";

			if (_R.gA(cli[0], "sba") === undefined) _R.sA(cli[0], "sba", "");
			var _ = {},
				s = _R.gA(cli[0], "sba").split(";");
			for (var si in s) {
				if (!s.hasOwnProperty(si)) continue;
				var v = s[si].split(":");
				switch (v[0]) {
					case "f":
						_.f = v[1];
						break;
					case "b":
						_.b = v[1];
						break;
					case "g":
						_.g = v[1];
						break;
					case "t":
						_.s = v[1];
						break;
				}
			}

			_R.sA(cli[0], "scroll-based", (_R[id].sbtimeline.set ? _.s !== undefined ? _.s : false : false))

			//HANDLE BG VIDEOS
			if (B.bgvid.length > 0) {
				B.bgvidid = B.bgvid[0].id;
				B.animateDirection = "idle";
				B.bgvid.addClass("defaultvid").css({ zIndex: 30 });
				if (mf !== undefined && mf !== "" && mf !== "none") B.bgvid.addClass(mf);
				B.bgvid.appendTo(B.wrap);
				if (B.parallax != undefined) {
					B.bgvid.data('parallax', B.parallax);
					B.bgvid.data('showcoveronpause', "on");
					B.bgvid.data('mediafilter', mf);
				}
				B.poster = false;
				if ((B.src !== undefined && B.src.indexOf('assets/dummy.png') == -1 && B.src.indexOf('assets/transparent.png') == -1) ||
					(B.lazyload !== undefined && B.lazyload.indexOf('assets/transparent.png') == -1 && B.lazyload.indexOf('assets/dummy.png') == -1)) B.poster = true;

				B.bgvid.data("bgvideo", 1);
				B.bgvid[0].dataset.key = key;
				//_R.manageVideoLayer(B.bgvid,id,key);

				if (B.bgvid.find('.rs-fullvideo-cover').length == 0) B.bgvid.append('<div class="rs-fullvideo-cover"></div>');
			}

			if (_R[id].scrolleffect.set) {
				_R[id].scrolleffect.bgs.push({
					fade: _.f !== undefined ? _.f : _R[id].scrolleffect.slide ? _R[id].scrolleffect.fade : false,
					blur: _.b !== undefined ? _.b : _R[id].scrolleffect.slide ? _R[id].scrolleffect.blur : false,
					grayscale: _.g !== undefined ? _.g : _R[id].scrolleffect.slide ? _R[id].scrolleffect.grayscale : false,
					c: B.wrap.wrap('<rs-sbg-effectwrap></rs-sbg-effectwrap>').parent()
				});
				cli.prepend(B.wrap.parent().parent());
			} else cli.prepend(B.wrap.parent());
		}

		// Append the Container Again
		if (_R[id].sliderType === "carousel") {
			tpGS.gsap.set(_R[id].carousel.wrap, { opacity: 0 });
			_R[id].c[0].appendChild(_R[id].carousel.wrap[0]);
		} else _R[id].c[0].appendChild(_R[id].canvas[0]);

	};

	// CONVERT NEW VALUES TO DATA's
	var getBGValues = function(_, id) {

		_.bg = _.bg === undefined ? "" : _.bg;
		var attrs = _.bg.split(";"),
			bg = { bgposition: "50% 50%", bgfit: 'cover', bgrepeat: "no-repeat", bgcolor: "transparent" };
		for (var k in attrs) {
			if (!attrs.hasOwnProperty(k)) continue;
			var basic = attrs[k].split(":"),
				key = basic[0],
				val = basic[1],
				nk = "";
			switch (key) {
				case "p":
					nk = "bgposition";
					break;
				case "f":
					nk = "bgfit";
					break;
				case "r":
					nk = "bgrepeat";
					break;
				case "c":
					nk = "bgcolor";
					break;
			}
			if (nk !== undefined) bg[nk] = val;
		}

		// TURN OF KEN BURNS IF WE ARE ON MOBILE AND IT IS WISHED SO
		if (_R[id].fallbacks.panZoomDisableOnMobile && _R.ISM) {
			bg.panzoom = undefined;
			bg.bgfit = "cover";
			_.panzoom = undefined;
		}

		return jQuery.extend(true, _, bg);
	};

	//	REMOVE SLOTS	//
	var removeSlots = function(id, where) {

		where.find('.slot, .slot-circle-wrapper').each(function() {
			jQuery(this).remove();
		});
		_R[id].transition = 0;
	};


	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////       SLIDE SWAPS			////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	// THE IMAGE IS LOADED, WIDTH, HEIGHT CAN BE SAVED
	var cutParams = function(a) {
		var b = a;
		if (a != undefined && a.length > 0)
			b = a.split("?")[0];
		return b;
	};

	var cutProtocol = function(a) {
		var b = a;
		if (a != undefined && a.length > 0)
			b = b.replace(document.location.protocol, "");
		return b;
	}


	var abstorel = function(base, relative) {
		var stack = base.split("/"),
			parts = relative.split("/");
		stack.pop();
		for (var i = 0; i < parts.length; i++) {
			if (parts[i] == ".")
				continue;
			if (parts[i] == "..")
				stack.pop();
			else
				stack.push(parts[i]);
		}
		return stack.join("/");
	};

	var imgLoaded = function(img, id, progress) {
		if (_R[id] === undefined) return;
		_R[id].syncload--;
		var REF = _R.gA(img, 'reference');
		for (var i in _R[id].loadqueue) {
			if (!_R[id].loadqueue.hasOwnProperty(i) || _R[id].loadqueue[i].progress === "loaded") continue;
			else if (REF == _R[id].loadqueue[i].src) {
				_R[id].loadqueue[i].img = img;
				_R[id].loadqueue[i].progress = progress;
				_R[id].loadqueue[i].width = img.naturalWidth;
				_R[id].loadqueue[i].height = img.naturalHeight;
			}
		}

		progressImageLoad(id);
	};

	// PRELOAD IMAGES 3 PIECES ON ONE GO, CHECK LOAD PRIORITY
	var progressImageLoad = function(id) {
		if (_R[id].syncload == 4) return;
		if (_R[id].loadqueue)
			jQuery.each(_R[id].loadqueue, function(index, queue) {
				if (queue.progress == 'prepared') {
					if (_R[id].syncload <= 4) {
						_R[id].syncload++;
						if (queue.type == "img") {
							var img = queue.img.tagName == "IMG" ? queue.img : new Image();
							_R.sA(img, "reference", queue.src);
							if (/^([\w]+\:)?\/\//.test(queue.src) && queue.src.indexOf(location.host) === -1 && _R[id].imgCrossOrigin !== "" && _R[id].imgCrossOrigin !== undefined) img.crossOrigin = _R[id].imgCrossOrigin;
							img.onload = function() {
								imgLoaded(this, id, "loaded");
								queue.error = false;
							};
							img.onerror = function() {
								imgLoaded(this, id, "failed");
								queue.error = true;
							};
							img.src = queue.src;
							queue.starttoload = jQuery.now();
						} else {
							jQuery.get(queue.src, function(data) {
								queue.innerHTML = new XMLSerializer().serializeToString(data.documentElement);
								queue.progress = "loaded";
								_R[id].syncload--;
								progressImageLoad(id);
							}).fail(function() {
								queue.progress = "failed";
								_R[id].syncload--;
								progressImageLoad(id);
							});
						}
						queue.progress = "inload";
					}
				}
			});
	};



	var simWarn = function(s, e) {
		console.log("Static Image " + s + "  Could not be loaded in time. Error Exists:" + e);
		return true;
	};

	var vidWarning = function(w, id) {

		if (jQuery.now() - _R[id][w + "starttime"] > 5000 && _R[id][w + "warning"] != true) {
			_R[id][w + "warning"] = true;
			var txt = w + " Api Could not be loaded !";
			if (location.protocol === 'https:') txt = txt + " Please Check and Renew SSL Certificate !";
			console.error(txt);
			_R[id].c.append('<div style="position:absolute;top:50%;width:100%;color:#e74c3c;  font-size:16px; text-align:center; padding:15px;background:#000; display:block;"><strong>' + txt + '</strong></div>');
		}
		return true;
	};




	//////////////////////////////////////
	//	-	CALL TO SWAP THE SLIDES -  //
	/////////////////////////////////////
	var hideSlide = function(id) {
		if (_R[id] === undefined) return;
		_R[id].pr_active_slide = jQuery(_R[id].slides[_R[id].pr_active_key]);
		_R[id].pr_next_slide = jQuery(_R[id].slides[_R[id].pr_processing_key]);
		_R[id].pr_active_bg = _R[id].pr_active_slide.find('rs-sbg-wrap');
		_R[id].pr_next_bg = _R[id].pr_next_slide.find('rs-sbg-wrap');
		if (_R[id].pr_active_bg !== undefined && _R[id].pr_active_bg.length > 0) tpGS.gsap.to(_R[id].pr_active_bg, 0.5, { opacity: 0 });
		if (_R[id].pr_next_bg !== undefined && _R[id].pr_next_bg.length > 0) tpGS.gsap.to(_R[id].pr_next_bg, 0.5, { opacity: 0 });
		tpGS.gsap.set(_R[id].pr_active_slide, { zIndex: 18 });
		if (_R[id].pr_next_slide !== undefined && _R[id].pr_next_slide.length > 0) tpGS.gsap.set(_R[id].pr_next_slide, { autoAlpha: 0, zIndex: 20 });
		_R[id].tonpause = false;

		if (_R[id].pr_active_key !== undefined) _R.removeTheLayers(_R[id].pr_active_slide, id, true);
		_R[id].firststart = 1;
		setTimeout(function() {
			delete _R[id].pr_active_key;
			delete _R[id].pr_processing_key;
		}, 200);
	};

	var swapSlide = function(id, ignore) {
		if (_R[id] === undefined) return;


		clearTimeout(_R[id].waitWithSwapSlide);
		if (_R[id].pr_processing_key !== undefined && _R[id].firstSlideShown === true) {
			_R[id].waitWithSwapSlide = setTimeout(function() { swapSlide(id, ignore); }, 18);
			return;
		}
		clearTimeout(_R[id].waitWithSwapSlide);

		// IF SOME OTHER SLIDE SHOULD BE CALLED INSTEAD AT START
		if (_R[id].startWithSlideKey !== undefined) {
			_R[id].pr_next_key = _R.getComingSlide(id, _R[id].startWithSlideKey).nindex;
			delete _R[id].startWithSlideKey;
		}



		_R[id].pr_active_slide = jQuery(_R[id].slides[_R[id].pr_active_key]);
		_R[id].pr_next_slide = jQuery(_R[id].slides[_R[id].pr_next_key]);
		if (_R[id].pr_next_key == _R[id].pr_active_key) return delete _R[id].pr_next_key;

		//Preload Video
		var key = _R.gA(_R[id].pr_next_slide[0], "key");
		if (_R[id].sbgs[key].bgvid && _R[id].sbgs[key].bgvid.length > 0 && (_R[id].videos == undefined || _R[id].videos[_R[id].sbgs[key].bgvid[0].id] === undefined)) {
			_R.manageVideoLayer(_R[id].sbgs[key].bgvid, id, key);
		}


		_R[id].pr_processing_key = _R[id].pr_next_key;
		_R[id].pr_cache_pr_next_key = _R[id].pr_next_key;
		delete _R[id].pr_next_key;
		if (_R[id].pr_next_slide !== undefined && _R[id].pr_next_slide[0] !== undefined && _R.gA(_R[id].pr_next_slide[0], "hal") !== undefined)
			_R.sA(_R[id].pr_next_slide[0], "sofacounter", _R.gA(_R[id].pr_next_slide[0], "sofacounter") === undefined ? 1 : parseInt(_R.gA(_R[id].pr_next_slide[0], "sofacounter"), 0) + 1);

		// CHECK IF WE ARE ALREADY AT LAST ITEM TO PLAY IN REAL LOOP SESSION
		if (_R[id].stopLoop && _R[id].pr_processing_key == _R[id].lastslidetoshow - 1) {
			_R[id].progressC.css({ 'visibility': 'hidden' });
			_R[id].c.trigger('revolution.slide.onstop');
			_R[id].noloopanymore = 1;
		}

		// INCREASE LOOP AMOUNTS
		if (_R[id].pr_next_slide.index() === _R[id].slideamount - 1 && _R[id].looptogo > 0 && _R[id].looptogo !== "disabled") {
			_R[id].looptogo--;
			if (_R[id].looptogo <= 0) _R[id].stopLoop = true;
		}


		_R[id].tonpause = true;
		_R[id].slideInSwapTimer = true;
		_R[id].c.trigger('stoptimer');

		if (_R[id].spinner === "off") {
			if (_R[id].loader !== undefined && _R[id].loaderVisible === true) {
				_R[id].loader.css({ display: "none" });
				_R[id].loaderVisible = false;
			}
		} else
			_R[id].loadertimer = setTimeout(function() {
				if (_R[id].loader !== undefined && _R[id].loaderVisible !== true) {
					_R[id].loader.css({ display: "block" });
					_R[id].loaderVisible = true;
				}
			}, 100);

		var waitList = _R[id].sliderType === "carousel" && _R[id].lazyType !== "all" ? _R.loadVisibleCarouselItems(id) : _R[id].pr_next_slide[0];
		_R.loadImages(waitList, id, 1);

		if (_R.preLoadAudio) _R.preLoadAudio(_R[id].pr_next_slide, id, 1);


		// WAIT FOR SWAP SLIDE PROGRESS
		_R.waitForCurrentImages(waitList, id, function() {
			_R[id].firstSlideShown = true;
			// MANAGE BG VIDEOS
			_R[id].pr_next_slide.find('rs-bgvideo').each(function() { _R.prepareCoveredVideo(id); });

			_R.loadUpcomingContent(id);
			window.requestAnimationFrame(function() {

				swapSlideProgress(_R[id].pr_next_slide.find('rs-sbg'), id, ignore);
			});
		});

	};



	//////////////////////////////////////
	//	-	PROGRESS SWAP THE SLIDES -  //
	/////////////////////////////////////
	var swapSlideProgress = function(defimg, id, ignoreLayerAnimation) {

		if (_R[id] === undefined) return;

		// RESET THE TIMER
		buildProgressBar(id);

		_R[id].pr_active_slide = jQuery(_R[id].slides[_R[id].pr_active_key]);
		_R[id].pr_next_slide = jQuery(_R[id].slides[_R[id].pr_processing_key]);
		_R[id].pr_active_bg = _R[id].pr_active_slide.find('rs-sbg-wrap');
		_R[id].pr_next_bg = _R[id].pr_next_slide.find('rs-sbg-wrap');
		_R[id].tonpause = false;

		clearTimeout(_R[id].loadertimer);
		if (_R[id].loader !== undefined && _R[id].loaderVisible === true) {
			window.requestAnimationFrame(function() {
				_R[id].loader.css({ display: "none" });
			});
			_R[id].loaderVisible = false;
		}


		// TRIGGER THE ON CHANGE EVENT
		_R[id].onBeforeSwap = {
			slider: id,
			slideIndex: parseInt(_R[id].pr_active_key, 0) + 1,
			slideLIIndex: _R[id].pr_active_key,
			nextSlideIndex: parseInt(_R[id].pr_processing_key, 0) + 1,
			nextSlideLIIndex: _R[id].pr_processing_key,
			nextslide: _R[id].pr_next_slide,
			slide: _R[id].pr_active_slide,
			currentslide: _R[id].pr_active_slide,
			prevslide: _R[id].pr_lastshown_key !== undefined ? _R[id].slides[_R[id].pr_lastshown_key] : ""
		}


		_R[id].c.trigger('revolution.slide.onbeforeswap', _R[id].onBeforeSwap);

		_R[id].transition = 1;
		_R[id].stopByVideo = false;

		// IF DELAY HAS BEEN SET VIA THE SLIDE, WE TAKE THE NEW VALUE, OTHER WAY THE OLD ONE...
		if (_R[id].pr_next_slide[0] !== undefined && _R.gA(_R[id].pr_next_slide[0], "duration") != undefined && _R.gA(_R[id].pr_next_slide[0], "duration") != "")
			_R[id].duration = parseInt(_R.gA(_R[id].pr_next_slide[0], "duration"), 0);
		else
			_R[id].duration = _R[id].origcd;

		if (_R[id].pr_next_slide[0] !== undefined && (_R.gA(_R[id].pr_next_slide[0], "ssop") == "true" || _R.gA(_R[id].pr_next_slide[0], "ssop") === true))
			_R[id].ssop = true;
		else
			_R[id].ssop = false;


		// OUTER CONTAINER HEIGHT MUST BE DIFFERENT DUE FIXED SCROLL EFFECT
		if (_R[id].sbtimeline.set && _R[id].sbtimeline.fixed) _R.updateFixedScrollTimes(id);


		_R[id].c.trigger('nulltimer');
		_R[id].sdir = _R[id].pr_processing_key < _R[id].pr_active_key ? 1 : 0;

		if (_R[id].sc_indicator == "arrow") {
			if (_R[id].pr_active_key == 0 && _R[id].pr_processing_key == _R[id].slideamount - 1) _R[id].sdir = 1;
			if ((_R[id].pr_active_key == _R[id].slideamount - 1) && _R[id].pr_processing_key == 0) _R[id].sdir = 0;
		}

		//_R[id].lsdir = _R[id].lsdir === undefined ? _R[id].sdir : _R[id].lsdir;
		_R[id].lsdir = _R[id].sdir;

		///////////////////////////
		//	REMOVE THE CAPTIONS //
		///////////////////////////
		if (_R[id].pr_active_key != _R[id].pr_processing_key && _R[id].firststart != 1 && _R[id].sliderType !== "carousel")
			if (_R.removeTheLayers) _R.removeTheLayers(_R[id].pr_active_slide, id);
		if (_R.gA(_R[id].pr_next_slide[0], 'rspausetimeronce') !== 1 && _R.gA(_R[id].pr_next_slide[0], "rspausetimeralways") !== 1)
			_R[id].c.trigger('restarttimer');
		else {
			_R[id].stopByVideo = true;
			_R.unToggleState(_R[id].slidertoggledby);
		}

		_R.sA(_R[id].pr_next_slide[0], "rspausetimeronce", 0);
		if (_R[id].pr_next_slide[0] !== undefined) _R.sA(_R[id].c[0], "slideactive", _R.gA(_R[id].pr_next_slide[0], "key"));



		// SELECT SLIDER TYPE
		if (_R[id].sliderType == "carousel") {
			_R[id].mtl = tpGS.gsap.timeline();
			_R.prepareCarousel(id);
			letItFree(id);
			_R.updateSlideBGs(id);
			if (_R[id].carousel.checkFVideo !== true) {
				var key = _R.gA(_R[id].pr_next_slide[0], "key");
				if (_R[id].sbgs[key] !== undefined && _R[id].sbgs[key].bgvid !== undefined && _R[id].sbgs[key].bgvid.length !== 0) _R.playBGVideo(id, key);
				_R[id].carousel.checkFVideo = true;
			}

			_R[id].transition = 0;
		} else {
			_R[id].pr_lastshown_key = _R[id].pr_lastshown_key === undefined ? _R[id].pr_next_key !== undefined ? _R[id].pr_next_key : _R[id].pr_processing_key !== undefined ? _R[id].pr_processing_key : _R[id].pr_active_key !== undefined ? _R[id].pr_active_key : _R[id].pr_lastshown_key : _R[id].pr_lastshown_key;
			_R[id].mtl = tpGS.gsap.timeline({ paused: true, onComplete: function() { letItFree(id); } });


			//_R[id].mtl.add(tpGS.gsap.set(_R[id].pr_next_bg.find('rs-sbg'),{opacity:0}));
			//_R[id].mtl.pause();  Removed Paused here, and added above in timeline. Hopefully things goes well....
			if (_R[id].pr_next_key !== undefined) _R.animateTheLayers({ slide: _R[id].pr_next_key, id: id, mode: "preset", caller: "swapSlideProgress_1" });
			else
			if (_R[id].pr_processing_key !== undefined) _R.animateTheLayers({ slide: _R[id].pr_processing_key, id: id, mode: "preset", caller: "swapSlideProgress_2" });
			else
			if (_R[id].pr_active_key !== undefined) _R.animateTheLayers({ slide: _R[id].pr_active_key, id: id, mode: "preset", caller: "swapSlideProgress_3" });

			if (_R[id].firststart == 1) {
				if (_R[id].pr_active_slide[0] !== undefined) tpGS.gsap.set(_R[id].pr_active_slide, { autoAlpha: 0 });
				_R[id].firststart = 0;
			}

			if (_R[id].pr_active_slide[0] !== undefined) tpGS.gsap.set(_R[id].pr_active_slide, { zIndex: 18 });
			if (_R[id].pr_next_slide[0] !== undefined) tpGS.gsap.set(_R[id].pr_next_slide, { autoAlpha: 0, zIndex: 20 });


			var key = _R.gA(_R[id].pr_next_slide[0], "key");

			// Alternated Animations
			if (_R[id].sbgs[key].alt === undefined) {
				_R[id].sbgs[key].alt = _R.gA(_R[id].pr_next_slide[0], "alttrans") || false;
				_R[id].sbgs[key].alt = _R[id].sbgs[key].alt !== false ? _R[id].sbgs[key].alt.split(",") : false;
				_R[id].sbgs[key].altIndex = 0;
				_R[id].sbgs[key].altLen = _R[id].sbgs[key].alt !== false ? _R[id].sbgs[key].alt.length : 0;

			}

			var a = _R[id].firstSlideAnimDone === undefined && _R[id].fanim !== undefined && _R[id].fanim !== false ? 1 :
				_R[id].sbgs[key].slideanimation === undefined || _R[id].sbgs[key].slideanimationRebuild ? 2 :
				_R[id].sbgs[key].random !== undefined && _R.SLTR !== undefined ? 3 :
				_R[id].sbgs[key].altLen > 0 && _R.SLTR !== undefined ? 4 :
				5;

			_R[id].sbgs[key].slideanimation = _R[id].firstSlideAnimDone === undefined && _R[id].fanim !== undefined && _R[id].fanim !== false ? _R.convertSlideAnimVals(jQuery.extend(true, {}, _R.getSlideAnim_EmptyObject(), _R[id].fanim)) :
				_R[id].sbgs[key].slideanimation === undefined || _R[id].sbgs[key].slideanimationRebuild ? _R.getSlideAnimationObj(id, { anim: _R.gA(_R[id].pr_next_slide[0], "anim"), filter: _R.gA(_R[id].pr_next_slide[0], "filter"), in: _R.gA(_R[id].pr_next_slide[0], "in"), out: _R.gA(_R[id].pr_next_slide[0], "out"), d3: _R.gA(_R[id].pr_next_slide[0], "d3") }, key) :
				_R[id].sbgs[key].random !== undefined && _R.SLTR !== undefined ? _R.convertSlideAnimVals(jQuery.extend(true, {}, _R.getSlideAnim_EmptyObject(), _R.getAnimObjectByKey(_R.getRandomSlideTrans(_R[id].sbgs[key].random.rndmain, _R[id].sbgs[key].random.rndgrp, _R.SLTR), _R.SLTR))) :
				_R[id].sbgs[key].altLen > 0 && _R.SLTR !== undefined ? _R.convertSlideAnimVals(jQuery.extend(true, { altAnim: _R[id].sbgs[key].alt[_R[id].sbgs[key].altIndex] }, _R.getSlideAnim_EmptyObject(), _R.getAnimObjectByKey(_R[id].sbgs[key].alt[_R[id].sbgs[key].altIndex], _R.SLTR))) :
				_R[id].sbgs[key].slideanimation;


			// Alternate the Animations
			if (_R[id].sbgs[key].altLen > 0) {
				if (_R[id].sbgs[key].firstSlideAnimDone !== undefined) {
					_R[id].sbgs[key].altIndex++;
					_R[id].sbgs[key].altIndex = _R[id].sbgs[key].altIndex >= _R[id].sbgs[key].altLen ? 0 : _R[id].sbgs[key].altIndex;
				} else {
					_R[id].sbgs[key].firstSlideAnimDone = true;
					if (_R.SLTR === undefined && _R.SLTR_loading === undefined) _R.loadSlideAnimLibrary(id);
				}
			}


			_R[id].sbgs[key].currentState = "animating";
			_R.animateSlide(id, _R[id].sbgs[key].slideanimation);
			if (_R[id].firstSlideAnimDone === undefined && _R[id].fanim !== undefined && _R[id].fanim !== false) _R[id].sbgs[key].slideanimationRebuild = true;
			_R[id].firstSlideAnimDone = true;

			if (_R[id].pr_next_bg.data('panzoom') !== undefined) {
				requestAnimationFrame(function() {
					var key = _R.gA(_R[id].pr_next_slide[0], 'key');
					_R.startPanZoom(_R[id].pr_next_bg, id, 0, _R.getSlideIndex(id, key), 'first', key);
				});
			}

			// SHOW FIRST LI && ANIMATE THE CAPTIONS
			_R[id].mtl.pause();
		}

		if (_R.animateTheLayers) {
			if (_R[id].sliderType === "carousel") {
				if (_R[id].carousel.showLayersAllTime !== false) {
					if (!_R[id].carousel.allLayersStarted)
						_R.animateTheLayers({ slide: "individual", id: id, mode: "start", caller: "swapSlideProgress_4" });
					else
						_R.animateTheLayers({ slide: "individual", id: id, mode: "rebuild", caller: "swapSlideProgress_5" });

					_R[id].carousel.allLayersStarted = true;
				}
				if (_R[id].firststart !== 0) _R.animateTheLayers({ slide: 0, id: id, mode: "start", caller: "swapSlideProgress_6" });
				else
				if (ignoreLayerAnimation !== true) _R.animateTheLayers({ slide: (_R[id].pr_next_key !== undefined ? _R[id].pr_next_key : _R[id].pr_processing_key !== undefined ? _R[id].pr_processing_key : _R[id].pr_active_key), id: id, mode: "start", caller: "swapSlideProgress_7" });
				_R[id].firststart = 0;

			} else
				_R.animateTheLayers({ slide: (_R[id].pr_next_key !== undefined ? _R[id].pr_next_key : _R[id].pr_processing_key !== undefined ? _R[id].pr_processing_key : _R[id].pr_active_key), id: id, mode: "start", caller: "swapSlideProgress_8" });
		} else
		if (_R[id].mtl != undefined) setTimeout(function() { _R[id].mtl.resume(); }, 18);

		if (_R[id].sliderType !== "carousel") tpGS.gsap.to(_R[id].pr_next_slide, 0.001, { autoAlpha: 1 });



	};

	//////////////////////////
	//	MANAGE SLIDE LOOPS	//
	//////////////////////////
	var manageSlideLoop = function(id) {

		if (_R[id] === undefined) return;
		_R[id].sloops = _R[id].sloops === undefined ? {} : _R[id].sloops;

		var key = _R.gA(_R[id].slides[_R[id].pr_active_key], "key"),
			_ = _R[id].sloops[key];
		if (_ === undefined) {
			_ = { s: 2500, e: 4500, r: 'unlimited' };
			var s = _R.gA(_R[id].slides[_R[id].pr_active_key], "sloop").split(";");
			for (var i in s) {
				if (!s.hasOwnProperty(i)) continue;
				var tmp = s[i].split(":");
				switch (tmp[0]) {
					case "s":
						_.s = parseInt(tmp[1], 0) / 1000;
						break;
					case "e":
						_.e = parseInt(tmp[1], 0) / 1000;
						break;
					case "r":
						_.r = tmp[1];
						break;
				}
			}
			_.r = _.r === "unlimited" ? -1 : parseInt(_.r, 0);
			_R[id].sloops[key] = _;
			_.key = key;
		}
		_.ct = { time: _.s };
		_.tl = tpGS.gsap.timeline({});

		_.timer = tpGS.gsap.fromTo(_.ct, (_.e - _.s), { time: _.s }, {
			time: _.e,
			ease: "none",
			onRepeat: function() {
				for (var li in _R[id].layers[_.key])
					if (_R[id].layers[_.key].hasOwnProperty(li)) _R[id]._L[li].timeline.play(_.s);
				var bt = _R[id].progressC;
				if (bt !== undefined && bt[0] !== undefined && bt[0].tween !== undefined) bt[0].tween.time(_.s);
			},
			onUpdate: function() {},
			onComplete: function() {

			}
		}).repeat(_.r);
		_.tl.add(_.timer, _.s);
		//Overjump the Offset due the Slide Animation Time for First Start !
		_.tl.time(_R[id].mtldiff);
	};

	//////////////////////////////////////////
	//	GIVE FREE THE TRANSITIOSN			//
	//////////////////////////////////////////
	var letItFree = function(id) {
		if (_R[id] === undefined) return;
		if (_R.RS_swapList[id] !== "done") {
			_R.RS_swapList[id] = "done";
			var ind = jQuery.inArray(id, _R.RS_swapping);
			_R.RS_swapping.splice(ind, 1);
		}

		if (_R[id].firstSlideAvailable === undefined) {
			_R[id].firstSlideAvailable = true;
			window.requestAnimationFrame(function() {
				if (_R[id].sliderType !== "hero" && _R.createNavigation && _R[id].navigation.use && _R[id].navigation.createNavigationDone !== true) _R.createNavigation(id);
			});
		}

		if (_R[id].sliderType === "carousel") {
			tpGS.gsap.to(_R[id].carousel.wrap, 1, { opacity: 1 });
			// CAROUSEL SLIDER
		}

		_R[id].pr_active_key = _R[id].pr_processing_key !== undefined ? _R[id].pr_processing_key : _R[id].pr_active_key;
		delete _R[id].pr_processing_key;

		if (_R[id].parallax.type == "scroll" || _R[id].parallax.type == "scroll+mouse" || _R[id].parallax.type == "mouse+scroll") {
			_R[id].lastscrolltop = -999;
			_R.generalObserver(_R.ISM);
		}
		_R[id].mtldiff = _R[id].mtl.time();
		delete _R[id].mtl;

		if (_R[id].pr_active_key === undefined) return;
		if (_R.gA(_R[id].slides[_R[id].pr_active_key], "sloop") !== undefined) manageSlideLoop(id);

		// RECORD CURRENT ACTIVE SLIDE GLOBAL, LOCAL
		_R.sA(_R[id].slides[_R[id].activeRSSlide], 'isactiveslide', false);
		_R[id].activeRSSlide = _R[id].pr_active_key;
		_R.sA(_R[id].slides[_R[id].activeRSSlide], 'isactiveslide', true);

		var activeKey = _R.gA(_R[id].slides[_R[id].pr_active_key], "key"),
			prevKey = _R.gA(_R[id].slides[_R[id].pr_lastshown_key], "key");

		_R.sA(_R[id].c[0], "slideactive", activeKey);

		if (prevKey !== undefined && _R[id].panzoomTLs !== undefined && _R[id].panzoomTLs[_R.getSlideIndex(id, prevKey)] !== undefined) {
			if (_R[id].sliderType === "carousel") {
				_R[id].panzoomTLs[_R.getSlideIndex(id, prevKey)].timeScale(3);
				_R[id].panzoomTLs[_R.getSlideIndex(id, prevKey)].reverse();
			} else {
				_R[id].panzoomTLs[_R.getSlideIndex(id, prevKey)].pause();
			}
		}

		// CHECK IF THIS REALLY NEEDED !?
		if (_R[id].pr_next_bg.data('panzoom') !== undefined) {
			if (_R[id].panzoomTLs !== undefined && _R[id].panzoomTLs[_R.getSlideIndex(id, activeKey)] !== undefined) {
				_R[id].panzoomTLs[_R.getSlideIndex(id, activeKey)].timeScale(1);
				_R[id].panzoomTLs[_R.getSlideIndex(id, activeKey)].play();
			} else {

				_R.startPanZoom(_R[id].pr_next_bg, id, 0, _R.getSlideIndex(id, activeKey), 'play', activeKey);
			}
		}


		// TIRGGER THE ON CHANGE EVENTS
		var data = {
			slider: id,
			slideIndex: parseInt(_R[id].pr_active_key, 0) + 1,
			slideLIIndex: _R[id].pr_active_key,
			slide: _R[id].pr_next_slide,
			currentslide: _R[id].pr_next_slide,
			prevSlideIndex: _R[id].pr_lastshown_key !== undefined ? parseInt(_R[id].pr_lastshown_key, 0) + 1 : false,
			prevSlideLIIndex: _R[id].pr_lastshown_key !== undefined ? parseInt(_R[id].pr_lastshown_key, 0) : false,
			prevSlide: _R[id].pr_lastshown_key !== undefined ? _R[id].slides[_R[id].pr_lastshown_key] : false
		};
		_R[id].c.trigger('revolution.slide.onchange', data);
		_R[id].c.trigger('revolution.slide.onafterswap', data);

		//Need to update the current HASH Tag ?
		if (_R[id].deepLinkListener || _R[id].enableDeeplinkHash) {
			var dlink = _R.gA(_R[id].slides[_R[id].pr_active_key], "deeplink");
			if (dlink !== undefined && dlink.length > 0) {
				_R[id].ignoreDeeplinkChange = true;
				window.location.hash = _R.gA(_R[id].slides[_R[id].pr_active_key], "deeplink");
			}
		}



		_R[id].pr_lastshown_key = _R[id].pr_active_key;

		if (_R[id].startWithSlide !== undefined && _R[id].startWithSlide !== "done" && _R[id].sliderType === "carousel") _R[id].firststart = 0;

		_R[id].duringslidechange = false;
		if (_R[id].pr_active_slide.length > 0 && _R.gA(_R[id].pr_active_slide[0], "hal") != 0 && _R.gA(_R[id].pr_active_slide[0], "hal") <= _R.gA(_R[id].pr_active_slide[0], "sofacounter")) _R[id].c.revremoveslide(_R[id].pr_active_slide.index());


		var _actli = _R[id].pr_processing_key || _R[id].pr_active_key || 0;
		if (_R[id].rowzones != undefined) _actli = _actli > _R[id].rowzones.length ? _R[id].rowzones.length : _actli;

		if ((_R[id].rowzones != undefined && _R[id].rowzones.length > 0 && _R[id].rowzones[_actli] != undefined && _actli >= 0 && _actli <= _R[id].rowzones.length && _R[id].rowzones[_actli].length > 0) || _R.winH < _R[id].module.height) _R.updateDims(id);

		delete _R[id].sc_indicator;
		delete _R[id].sc_indicator_dir;
		if (_R[id].firstLetItFree === undefined) {
			_R.generalObserver(_R.ISM);
			_R[id].firstLetItFree = true;
		}

	};





	///////////////////////////
	//	REMOVE THE LISTENERS //
	///////////////////////////
	var removeAllListeners = function(id) {
		_R[id].c.children().each(function() {
			try { jQuery(this).die('click'); } catch (e) {}
			try { jQuery(this).die('mouseenter'); } catch (e) {}
			try { jQuery(this).die('mouseleave'); } catch (e) {}
			try { jQuery(this).unbind('hover'); } catch (e) {}
		});
		try { _R[id].c.die('click', 'mouseenter', 'mouseleave'); } catch (e) {}
		clearInterval(_R[id].cdint);
		_R[id].c = null;
	};


	///////////////////////////
	// BUILD THE PROGRESSBAR //
	///////////////////////////
	var buildProgressBar = function(id) {
		var p = _R[id].progressBar;
		// BUILD 1 TIME THE CONTAINERS
		if (_R[id].progressC === undefined || _R[id].progressC.length == 0) {
			_R[id].progressC = jQuery('<rs-progress style="visibility:hidden;"></rs-progress>');
			if (p.style === "horizontal" || p.style === "vertical") {
				// ALL SLIDES
				if (p.basedon === "module") {
					var cntx = "";
					for (var i = 0; i < _R[id].slideamount; i++) cntx += '<rs-progress-bar></rs-progress-bar>';
					cntx += '<rs-progress-bgs>';
					for (var i = 0; i < _R[id].slideamount; i++) cntx += '<rs-progress-bg></rs-progress-bg>';
					cntx += '</rs-progress-bgs>';
					if (p.gaptype !== "nogap")
						for (var i = 0; i < _R[id].slideamount; i++) cntx += '<rs-progress-gap></rs-progress-gap>';
					_R[id].progressC[0].innerHTML = cntx;
					if (_R[id].noDetach === true) _R[id].c.append(_R[id].progressC);
					_R[id].progressCBarBGS = _R.getByTag(_R[id].progressC[0], 'RS-PROGRESS-BG');
					_R[id].progressCBarGAPS = _R.getByTag(_R[id].progressC[0], 'RS-PROGRESS-GAP');
					if (p.gaptype !== "nogap") tpGS.gsap.set(_R[id].progressCBarGAPS, { backgroundColor: p.gapcolor, zIndex: p.gaptype === "gapbg" ? 17 : 27 });
					tpGS.gsap.set(_R[id].progressCBarBGS, { backgroundColor: p.bgcolor });
				} else {
					_R[id].progressC[0].innerHTML = '<rs-progress-bar></rs-progress-bar>';
					if (_R[id].noDetach === true) _R[id].c.append(_R[id].progressC);
				}

				_R[id].progressCBarInner = _R.getByTag(_R[id].progressC[0], 'RS-PROGRESS-BAR');
				tpGS.gsap.set(_R[id].progressCBarInner, { background: p.color });
			} else {
				// SINGLE SLIDE
				_R[id].progressC[0].innerHTML = '<canvas width="' + p.radius * 2 + '" height="' + p.radius * 2 + '" style="position:absolute" class="rs-progress-bar"></canvas>';
				if (_R[id].noDetach === true) _R[id].c.append(_R[id].progressC);
				_R[id].progressCBarInner = _R[id].progressC[0].getElementsByClassName('rs-progress-bar')[0];
				_R[id].progressBCanvas = _R[id].progressCBarInner.getContext('2d');
				_R[id].progressBar.degree = (_R[id].progressBar.style === "cw" ? 360 : 0);
				drawCWCCW(id);
			}
		}


		// REMOVE AND STLYE, RESIZE RPGORESS CONTAINERS
		if (_R[id].noDetach !== true) _R[id].progressC.detach();
		if (_R[id].progressBar.visibility[_R[id].level] && _R[id].progressBar.disableProgressBar != true) {
			// SIZING OF GAPS, BGS, PROGRESS
			if (p.style === "horizontal" || p.style === "vertical") {
				var GAP = _R[id].slideamount - 1,
					WNG, WWG;
				if (p.style === "horizontal") {
					var WNGcalc = (p.alignby === "grid" ? _R[id].gridwidth[_R[id].level] : _R[id].module.width);
					WNG = Math.ceil(WNGcalc / _R[id].slideamount),
						WWG = Math.ceil(((WNGcalc - (GAP * p.gapsize)) / _R[id].slideamount));
					tpGS.gsap.set(_R[id].progressC, {
						visibility: "visible",
						top: p.vertical === "top" ? p.y + (p.alignby === "grid" && _R[id].gridOffsetHeight !== undefined ? Math.max(0, _R[id].gridOffsetHeight) : 0) : p.vertical === "center" ? "50%" : "auto",
						bottom: p.vertical === "top" || p.vertical === "center" ? "auto" : p.y + (p.alignby === "grid" && _R[id].gridOffsetHeight !== undefined ? Math.max(0, _R[id].gridOffsetHeight) : 0),
						left: p.horizontal === "left" ? p.alignby === "grid" && _R[id].gridOffsetWidth !== undefined ? Math.max(0, _R[id].gridOffsetWidth) : "auto" : "auto",
						right: p.horizontal === "right" ? p.alignby === "grid" && _R[id].gridOffsetWidth !== undefined ? Math.max(0, _R[id].gridOffsetWidth) : "auto" : "auto",
						y: p.vertical === "center" ? p.y : 0,
						height: p.size,
						backgroundColor: p.basedon === "module" ? "transparent" : p.bgcolor,
						marginTop: p.vertical === "bottom" ? 0 : p.vertical === "top" ? 0 : parseInt(p.size, 0) / 2,
						width: p.alignby === "grid" ? _R[id].gridwidth[_R[id].level] : "100%"
					});
					tpGS.gsap.set(_R[id].progressCBarInner, {
						x: p.basedon === "module" ? p.gap ? function(index) { return (p.horizontal === "right" ? GAP - index : index) * (WWG + p.gapsize) } : function(index) { return (p.horizontal === "right" ? GAP - index : index) * WNG } : 0,
						width: p.basedon === "module" ? p.gap ? WWG + "px" : 100 / _R[id].slideamount + "%" : "100%"
					});

					if (p.basedon === "module") {
						tpGS.gsap.set(_R[id].progressCBarBGS, {
							x: p.basedon === "module" ? p.gap ? function(index) { return index * (WWG + p.gapsize) } : function(index) { return index * WNG } : 0,
							width: p.basedon === "module" ? p.gap ? WWG + "px" : 100 / _R[id].slideamount + "%" : "100%"
						});
						tpGS.gsap.set(_R[id].progressCBarGAPS, {
							width: p.gap ? p.gapsize + "px" : 0,
							x: p.gap ? function(index) { return (index + 1) * (WWG) + (parseInt(p.gapsize, 0) * index) } : 0
						});
					}
				} else
				if (p.style === "vertical") {
					var WNGcalc = (p.alignby === "grid" ? _R[id].gridheight[_R[id].level] : _R[id].module.height);
					WNG = Math.ceil(WNGcalc / _R[id].slideamount),
						WWG = Math.ceil(((WNGcalc - (GAP * p.gapsize)) / _R[id].slideamount));
					tpGS.gsap.set(_R[id].progressC, {
						visibility: "visible",
						left: p.horizontal === "left" ? p.x + (p.alignby === "grid" && _R[id].gridOffsetWidth !== undefined ? Math.max(0, _R[id].gridOffsetWidth) : 0) : p.horizontal === "center" ? "50%" : "auto",
						right: p.horizontal === "left" || p.horizontal === "center" ? "auto" : p.x + (p.alignby === "grid" && _R[id].gridOffsetWidth !== undefined ? Math.max(0, _R[id].gridOffsetWidth) : 0),
						x: p.horizontal === "center" ? p.x : 0,
						top: p.vertical === "top" ? p.alignby === "grid" && _R[id].gridOffsetHeight !== undefined ? Math.max(_R[id].gridOffsetHeight, 0) : "auto" : "auto",
						bottom: p.vertical === "bottom" ? p.alignby === "grid" && _R[id].gridOffsetHeight !== undefined ? Math.max(_R[id].gridOffsetHeight, 0) : "auto" : "auto",
						width: p.size,
						marginLeft: p.horizontal === "left" ? 0 : p.horizontal === "right" ? 0 : parseInt(p.size, 0) / 2,
						backgroundColor: p.basedon === "module" ? "transparent" : p.bgcolor,
						height: p.alignby === "grid" ? _R[id].gridheight[_R[id].level] : "100%"
					});
					tpGS.gsap.set(_R[id].progressCBarInner, {
						y: p.basedon === "module" ? p.gap ? function(index) { return (p.vertical === "bottom" ? GAP - index : index) * (WWG + p.gapsize) } : function(index) { return (p.vertical === "bottom" ? GAP - index : index) * WNG } : 0,
						height: p.basedon === "module" ? p.gap ? WWG + "px" : 100 / _R[id].slideamount + "%" : "100%"
					});
					if (p.basedon === "module") {
						tpGS.gsap.set(_R[id].progressCBarBGS, {
							y: p.basedon === "module" ? p.gap ? function(index) { return index * (WWG + p.gapsize) } : function(index) { return index * WNG } : 0,
							height: p.basedon === "module" ? p.gap ? WWG + "px" : 100 / _R[id].slideamount + "%" : "100%"
						});
						tpGS.gsap.set(_R[id].progressCBarGAPS, {
							height: p.gap ? p.gapsize + "px" : 0,
							y: p.gap ? function(index) { return (index + 1) * (WWG) + (parseInt(p.gapsize, 0) * index) } : 0
						});
					}
				}
			} else {
				tpGS.gsap.set(_R[id].progressC, {
					top: p.vertical === "top" ? p.y + (p.alignby === "grid" && _R[id].gridOffsetHeight !== undefined ? Math.max(0, _R[id].gridOffsetHeight) : 0) : p.vertical === "center" ? "50%" : "auto",
					bottom: p.vertical === "top" || p.vertical === "center" ? "auto" : p.y + (p.alignby === "grid" && _R[id].gridOffsetHeight !== undefined ? Math.max(0, _R[id].gridOffsetHeight) : 0),
					left: p.horizontal === "left" ? p.x + (p.alignby === "grid" && _R[id].gridOffsetWidth !== undefined ? Math.max(0, _R[id].gridOffsetWidth) : 0) : p.horizontal === "center" ? "50%" : "auto",
					right: p.horizontal === "left" || p.horizontal === "center" ? "auto" : p.x + (p.alignby === "grid" && _R[id].gridOffsetWidth !== undefined ? Math.max(0, _R[id].gridOffsetWidth) : 0),
					y: p.vertical === "center" ? p.y : 0,
					x: p.horizontal === "center" ? p.x : 0,
					width: p.radius * 2,
					height: p.radius * 2,
					marginTop: p.vertical === "center" ? 0 - p.radius : 0,
					marginLeft: p.horizontal === "center" ? 0 - p.radius : 0,
					backgroundColor: "transparent",
					visibility: "visible"
				});
			}
		} else _R[id].progressC[0].style.visibility = 'hidden';

		// APPEND PROGRES BACK TO THE SLIDER
		if (_R[id].noDetach !== true) _R[id].c.append(_R[id].progressC);

		if (_R[id].gridOffsetWidth === undefined && p.alignby === "grid") _R[id].rebuildProgressBar = true;
		else _R[id].rebuildProgressBar = false;


	}

	var drawCWCCW = function(id) {

		var p = _R[id].progressBar;
		if (p.radius - parseInt(p.size, 0) <= 0) p.size = p.radius / 4;
		var posX = parseInt(p.radius),
			posY = parseInt(p.radius);

		_R[id].progressBCanvas.lineCap = 'round';
		_R[id].progressBCanvas.clearRect(0, 0, p.radius * 2, p.radius * 2);

		_R[id].progressBCanvas.beginPath();
		_R[id].progressBCanvas.arc(posX, posY, p.radius - parseInt(p.size, 0), (Math.PI / 180) * 270, (Math.PI / 180) * (270 + 360));
		_R[id].progressBCanvas.strokeStyle = p.bgcolor;
		_R[id].progressBCanvas.lineWidth = parseInt(p.size, 0) - 1;
		_R[id].progressBCanvas.stroke();

		_R[id].progressBCanvas.beginPath();
		_R[id].progressBCanvas.strokeStyle = p.color;
		_R[id].progressBCanvas.lineWidth = parseInt(p.size, 0);
		_R[id].progressBCanvas.arc(posX, posY, p.radius - parseInt(p.size, 0), (Math.PI / 180) * 270, (Math.PI / 180) * (270 + _R[id].progressBar.degree), p.style !== "cw");
		_R[id].progressBCanvas.stroke();
	}

	var animateProgressBar = function(id) {
		var countDownNext = function() {
			if (id === undefined || _R === undefined || _R[id] === undefined) return;
			if (jQuery('body').find(_R[id].c).length == 0 || _R[id] === null || _R[id].c === null || _R[id].c === undefined || _R[id].length === 0) {
				removeAllListeners(id);
				clearInterval(_R[id].cdint);
			} else {
				_R[id].c.trigger("revolution.slide.slideatend");
				//STATE OF API CHANGED -> MOVE TO AIP BETTER
				if (_R[id].c.data('conthoverchanged') == 1) {
					_R[id].conthover = _R[id].c.data('conthover');
					_R[id].c.data('conthoverchanged', 0);
				}
				_R.callingNewSlide(id, 1, true);
			}
		}
		var tl = tpGS.gsap.timeline({ paused: true }),
			resett = _R[id].progressBar.reset === "reset" || _R[id].progressBar.notnew === undefined ? 0 : 0.2,
			nsl = _R[id].progressBar.basedon === "slide" ? 0 : _R[id].pr_processing_key !== undefined ? _R[id].pr_processing_key : _R[id].pr_active_key;
		nsl = nsl === undefined ? 0 : nsl;

		if (_R[id].progressBar.style === "horizontal") {
			tl.add(tpGS.gsap.to(_R[id].progressCBarInner[nsl], resett, { scaleX: 0, transformOrigin: (_R[id].progressBar.horizontal === "right" ? "100% 50%" : "0% 50%") }));
			tl.add(tpGS.gsap.to(_R[id].progressCBarInner[nsl], _R[id].duration / 1000, { transformOrigin: (_R[id].progressBar.horizontal === "right" ? "100% 50%" : "0% 50%"), force3D: "auto", scaleX: 1, onComplete: countDownNext, delay: 0.5, ease: _R[id].progressBar.ease }));
			if (_R[id].progressBar.basedon === "module")
				for (var i = 0; i < _R[id].slideamount; i++)
					if (i !== nsl) tl.add(tpGS.gsap.set(_R[id].progressCBarInner[i], { scaleX: (i < nsl ? 1 : 0), transformOrigin: (_R[id].progressBar.horizontal === "right" ? "100% 50%" : "0% 50%") }), 0)
		} else
		if (_R[id].progressBar.style === "vertical") {
			if (_R[id].progressCBarInner[nsl] !== undefined) tl.add(tpGS.gsap.to(_R[id].progressCBarInner[nsl], resett, { scaleY: 0, transformOrigin: (_R[id].progressBar.vertical === "bottom" ? "50% 100%" : "50% 0%") }));
			if (_R[id].progressCBarInner[nsl] !== undefined) tl.add(tpGS.gsap.to(_R[id].progressCBarInner[nsl], _R[id].duration / 1000, { transformOrigin: (_R[id].progressBar.vertical === "bottom" ? "50% 100%" : "50% 0%"), force3D: "auto", scaleY: 1, onComplete: countDownNext, delay: 0.5, ease: _R[id].progressBar.ease }));
			if (_R[id].progressBar.basedon === "module")
				for (var i = 0; i < _R[id].slideamount; i++)
					if (i !== nsl && _R[id].progressCBarInner[i] !== undefined) tl.add(tpGS.gsap.set(_R[id].progressCBarInner[i], { scaleY: (i < nsl ? 1 : 0), transformOrigin: (_R[id].progressBar.vertical === "botton" ? "50% 100%" : "50% 0%") }), 0)
		} else {
			var midgr = _R[id].progressBar.basedon === "slide" ? 0 : Math.max(0, (360 / _R[id].slideamount) * (nsl)),
				madgr = _R[id].progressBar.basedon === "slide" ? 360 : (360 / _R[id].slideamount) * (nsl + 1);
			if (_R[id].progressBar.style === "ccw" && _R[id].progressBar.basedon !== 'slide') {
				midgr = 360 - madgr;
				madgr = 360 - ((360 / _R[id].slideamount) * (nsl));
			}
			tl.add(tpGS.gsap.to(_R[id].progressBar, resett, { degree: (_R[id].progressBar.style === "cw" ? midgr : madgr), onUpdate: function() { drawCWCCW(id); } }));
			tl.add(tpGS.gsap.to(_R[id].progressBar, _R[id].duration / 1000, { degree: (_R[id].progressBar.style === "cw" ? madgr : midgr), onUpdate: function() { drawCWCCW(id); }, onComplete: countDownNext, delay: 0.5, ease: _R[id].progressBar.ease }));
			//drawCWCCW(id,)
		}
		_R[id].progressBar.notnew = true;
		return tl;
	}


	///////////////////////////
	//	-	countDown	-	//
	/////////////////////////
	var countDown = function(id) {
		if (_R[id].progressC == undefined) buildProgressBar(id);
		_R[id].loop = 0;
		if (_R[id].stopAtSlide != undefined && _R[id].stopAtSlide > -1)
			_R[id].lastslidetoshow = _R[id].stopAtSlide;
		else
			_R[id].lastslidetoshow = 999;

		_R[id].stopLoop = false;

		if (_R[id].looptogo == 0) _R[id].stopLoop = true;

		// LISTENERS  //container.trigger('stoptimer');
		_R[id].c.on('stoptimer', function() {
			if (_R[id].progressC == undefined) return;
			_R[id].progressC[0].tween.pause();
			if (_R[id].progressBar.disableProgressBar) _R[id].progressC[0].style.visibility = "hidden";
			_R[id].sliderstatus = "paused";
			if (!_R[id].slideInSwapTimer) _R.unToggleState(_R[id].slidertoggledby);
			_R[id].slideInSwapTimer = false;
		});


		_R[id].c.on('starttimer', function() {
			if (_R[id].progressC == undefined) return;
			if (_R[id].forcepaused) return;
			if (_R[id].conthover != 1 && _R[id].stopByVideo != true && _R[id].module.width > _R[id].hideSliderAtLimit && _R[id].tonpause != true && _R[id].overnav != true && _R[id].ssop != true) {
				if (_R[id].noloopanymore !== 1 && (!_R[id].viewPort.enable || _R[id].inviewport)) {
					if (!_R[id].progressBar.visibility[_R[id].level]) _R[id].progressC[0].style.visibility = "visible";
					_R[id].progressC[0].tween.resume();
					_R[id].sliderstatus = "playing";
				}
			}
			if (_R[id].progressBar.disableProgressBar || !_R[id].progressBar.visibility[_R[id].level]) _R[id].progressC[0].style.visibility = "hidden";
			_R.toggleState(_R[id].slidertoggledby);
		});


		_R[id].c.on('restarttimer', function() {
			if (_R[id].progressC == undefined) return;
			if (_R[id].forcepaused) return;
			if (_R[id].mouseoncontainer && _R[id].navigation.onHoverStop == "on" && (!_R.ISM)) return false;
			if (_R[id].noloopanymore !== 1 && (!_R[id].viewPort.enable || _R[id].inviewport) && _R[id].ssop != true) {
				if (!_R[id].progressBar.visibility[_R[id].level]) _R[id].progressC[0].style.visibility = "visible";
				if (_R[id].progressC[0].tween !== undefined) _R[id].progressC[0].tween.kill();
				_R[id].progressC[0].tween = animateProgressBar(id);
				_R[id].progressC[0].tween.play();
				_R[id].sliderstatus = "playing";
				_R.toggleState(_R[id].slidertoggledby);
			} else {
				_R.unToggleState(_R[id].slidertoggledby);
			}
			if (_R[id].progressBar.disableProgressBar || !_R[id].progressBar.visibility[_R[id].level]) _R[id].progressC[0].style.visibility = "hidden";
			if (_R[id].mouseoncontainer && _R[id].navigation.onHoverStop == true && (!_R.ISM)) {
				_R[id].c.trigger('stoptimer');
				_R[id].c.trigger('revolution.slide.onpause');
			}
		});

		_R[id].c.on('nulltimer', function() {
			if (_R[id].progressC == undefined || _R[id].progressC[0] === undefined) return;
			if (_R[id].progressC[0].tween !== undefined) _R[id].progressC[0].tween.kill();
			_R[id].progressC[0].tween = animateProgressBar(id);
			_R[id].progressC[0].tween.pause(0);
			if (_R[id].progressBar.disableProgressBar || !_R[id].progressBar.visibility[_R[id].level]) _R[id].progressC[0].style.visibility = "hidden";
			_R[id].sliderstatus = "paused";
		});

		if (_R[id].progressC !== undefined) _R[id].progressC[0].tween = animateProgressBar(id);

		if (_R[id].slideamount > 1 && !(_R[id].stopAfterLoops == 0 && _R[id].stopAtSlide == 1))
			_R[id].c.trigger("starttimer");
		else {
			_R[id].noloopanymore = 1;
			_R[id].c.trigger("nulltimer");
		}

		_R[id].c.on('tp-mouseenter', function() {
			_R[id].mouseoncontainer = true;
			if (_R[id].navigation.onHoverStop == true && (!_R.ISM)) {
				_R[id].c.trigger('stoptimer');
				_R[id].c.trigger('revolution.slide.onpause');
			}
		});
		_R[id].c.on('tp-mouseleft', function() {
			_R[id].mouseoncontainer = false;
			if (_R[id].c.data('conthover') != 1 && _R[id].navigation.onHoverStop == true && ((_R[id].viewPort.enable == true && _R[id].inviewport) || _R[id].viewPort.enable == false)) {
				_R[id].c.trigger('revolution.slide.onresume');
				_R[id].c.trigger('starttimer');
			}
		});



	};




	//////////////////////////////////////////////////////
	// * Revolution Slider - NEEDFULL FUNCTIONS
	// * @version: 1.0 (30.10.2014)
	// * @author ThemePunch
	//////////////////////////////////////////////////////

	var restartOnFocus = function() {
		jQuery('.rev_redraw_on_blurfocus').each(function() {
			var id = this.id;
			if (_R[id] == undefined || _R[id].c == undefined || _R[id].c.length === 0) return false;
			if (_R[id].windowfocused != true) {
				_R[id].windowfocused = true;
				tpGS.gsap.delayedCall(0.1, function() {
					// TAB IS ACTIVE, WE CAN START ANY PART OF THE SLIDER
					if (_R[id].fallbacks.nextSlideOnWindowFocus) _R[id].c.revnext();
					_R[id].c.revredraw();
					if (_R[id].lastsliderstatus == "playing") _R[id].c.revresume();
					_R[id].c.trigger('revolution.slide.tabfocused');
				});
			}

		});

	};

	var lastStatBlur = function() {
		if (document.hasFocus()) return;
		jQuery('.rev_redraw_on_blurfocus').each(function(i) {
			var id = this.id;
			_R[id].windowfocused = false;
			_R[id].lastsliderstatus = _R[id].sliderstatus;
			_R[id].c.revpause();
			_R[id].c.trigger('revolution.slide.tabblured');
		});


	};

	var tabBlurringCheck = function() {
		var notIE = (document.documentMode === undefined),
			isChromium = window.chrome;

		if (_R.revslider_focus_blur_listener === 1) return;
		_R.revslider_focus_blur_listener = 1;
		if (notIE && !isChromium) {
			// checks for Firefox and other  NON IE Chrome versions
			_R.window.on("focusin", function() {
				if (_R.windowIsFocused !== true) restartOnFocus();
				_R.windowIsFocused = true;
			}).on("focusout", function() {
				if (_R.windowIsFocused === true || _R.windowIsFocused === undefined) lastStatBlur();
				_R.windowIsFocused = false;
			});
		} else {
			// checks for IE and Chromium versions
			if (window.addEventListener) {
				// bind focus event
				window.addEventListener("focus", function(event) {
					if (_R.windowIsFocused !== true) restartOnFocus();
					_R.windowIsFocused = true;
				}, { capture: false, passive: true });
				// bind blur event
				window.addEventListener("blur", function(event) {
					if (_R.windowIsFocused === true || _R.windowIsFocused === undefined) lastStatBlur();
					_R.windowIsFocused = false;
				}, { capture: false, passive: true });

			} else {

				// bind focus event
				window.attachEvent("focus", function(event) {
					if (_R.windowIsFocused !== true) restartOnFocus();
					_R.windowIsFocused = true;
				});
				// bind focus event
				window.attachEvent("blur", function(event) {
					if (_R.windowIsFocused === true || _R.windowIsFocused === undefined) lastStatBlur();
					_R.windowIsFocused = false;
				});
			}
		}
	};


	// 	-	GET THE URL PARAMETER //

	var getUrlVars = function(hashdivider) {
		var vars = [],
			hash;
		var hashes = window.location.href.slice(window.location.href.indexOf(hashdivider) + 1).split('_');
		for (var i = 0; i < hashes.length; i++) {
			hashes[i] = hashes[i].replace('%3D', "=");
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	};

	var checkBlockSpaces = function(id) {
		if (_R[id].blockSpacing !== undefined) {
			var btemp = _R[id].blockSpacing.split(";");
			_R[id].blockSpacing = {};
			for (var u in btemp) {
				if (!btemp.hasOwnProperty(u)) continue;
				var s = btemp[u].split(":");
				switch (s[0]) {
					case "t":
						_R[id].blockSpacing.top = _R.revToResp(s[1], 4, 0);
						break;
					case "b":
						_R[id].blockSpacing.bottom = _R.revToResp(s[1], 4, 0);
						break;
					case "l":
						_R[id].blockSpacing.left = _R.revToResp(s[1], 4, 0);
						break;
					case "r":
						_R[id].blockSpacing.right = _R.revToResp(s[1], 4, 0);
						break;
				}
			}

			_R[id].blockSpacing.block = jQuery(_R.closestClass(_R[id].c[0], 'wp-block-themepunch-revslider'));


			if (_R[id].level !== undefined && _R[id].blockSpacing !== undefined) tpGS.gsap.set(_R[id].blockSpacing.block, {
				paddingLeft: _R[id].blockSpacing.left[_R[id].level],
				paddingRight: _R[id].blockSpacing.right[_R[id].level],
				marginTop: _R[id].blockSpacing.top[_R[id].level],
				marginBottom: _R[id].blockSpacing.bottom[_R[id].level]
			});
		}
	}

	var generalConverts = function(_) {


		_.minHeight = _.minHeight !== undefined ? _.minHeight === "none" || _.minHeight === "0" || _.minHeight === "0px" || _.minHeight == "" || _.minHeight == " " ? 0 : parseInt(_.minHeight, 0) : 0;
		_.maxHeight = _.maxHeight === "none" || _.maxHeight === "0" ? 0 : parseInt(_.maxHeight, 0);
		_.carousel.maxVisibleItems = _.carousel.maxVisibleItems < 1 ? 999 : _.carousel.maxVisibleItems;
		_.carousel.vertical_align = _.carousel.vertical_align === "top" ? "0%" : _.carousel.vertical_align === "bottom" ? "100%" : "50%";
		_.carousel.space = parseInt(_.carousel.space, 0);
		_.carousel.maxOpacity = parseInt(_.carousel.maxOpacity, 0);
		_.carousel.maxOpacity = _.carousel.maxOpacity > 1 ? _.carousel.maxOpacity / 100 : _.carousel.maxOpacity;
		_.carousel.showLayersAllTime = _.carousel.showLayersAllTime === "true" || _.carousel.showLayersAllTime === true ? "all" : _.carousel.showLayersAllTime;
		_.carousel.maxRotation = parseInt(_.carousel.maxRotation, 0);
		_.carousel.minScale = parseInt(_.carousel.minScale, 0);
		_.carousel.minScale = _.carousel.minScale > 0.9 ? _.carousel.minScale / 100 : _.carousel.minScale;
		_.carousel.speed = parseInt(_.carousel.speed, 0);
		_.navigation.maintypes = ["arrows", "tabs", "thumbnails", "bullets"];
		_.perspective = parseInt(_.perspective, 0);
		for (var i in _.navigation.maintypes) {
			if (!_.navigation.maintypes.hasOwnProperty((i))) continue;
			if (_.navigation[_.navigation.maintypes[i]] === undefined) continue;
			_.navigation[_.navigation.maintypes[i]].animDelay = _.navigation[_.navigation.maintypes[i]].animDelay === undefined ? 1000 : _.navigation[_.navigation.maintypes[i]].animDelay;
			_.navigation[_.navigation.maintypes[i]].animSpeed = _.navigation[_.navigation.maintypes[i]].animSpeed === undefined ? 1000 : _.navigation[_.navigation.maintypes[i]].animSpeed;
			_.navigation[_.navigation.maintypes[i]].animDelay = parseInt(_.navigation[_.navigation.maintypes[i]].animDelay, 0) / 1000;
			_.navigation[_.navigation.maintypes[i]].animSpeed = parseInt(_.navigation[_.navigation.maintypes[i]].animSpeed, 0) / 1000;
		}
		// To Make sure it is not any more in %

		if (!_R.isNumeric(_.scrolleffect.tilt))
			if (_.scrolleffect.tilt.indexOf('%') !== -1) _.scrolleffect.tilt = parseInt(_.scrolleffect.tilt);
		_.scrolleffect.tilt = _.scrolleffect.tilt / 100;

		//Thumbs und Tabs Settings ->
		_.navigation.thumbnails.position = _.navigation.thumbnails.position == "outer-horizontal" ? _.navigation.thumbnails.v_align == "bottom" ? "outer-bottom" : "outer-top" :
			_.navigation.thumbnails.position == "outer-vertical" ? _.navigation.thumbnails.h_align == "left" ? "outer-left" : "outer-right" : _.navigation.thumbnails.position;

		_.navigation.tabs.position = _.navigation.tabs.position == "outer-horizontal" ? _.navigation.tabs.v_align == "bottom" ? "outer-bottom" : "outer-top" :
			_.navigation.tabs.position == "outer-vertical" ? _.navigation.tabs.h_align == "left" ? "outer-left" : "outer-right" : _.navigation.tabs.position;
		_.sbtimeline.speed = parseInt(_.sbtimeline.speed, 0) / 1000 || 0.5;

		if (_.sbtimeline.set === true && _.sbtimeline.fixed === true && _.sliderLayout !== "auto") {
			_.sbtimeline.fixStart = parseInt(_.sbtimeline.fixStart);
			_.sbtimeline.fixEnd = parseInt(_.sbtimeline.fixEnd);
		} else {
			_.sbtimeline.fixed = false;
		}

		if (_.progressBar !== undefined && (_.progressBar.disableProgressBar == "true" || _.progressBar.disableProgressBar == true)) _.progressBar.disableProgressBar = true;

		_.startDelay = parseInt(_.startDelay, 0) || 0;

		if (_.navigation !== undefined && _.navigation.arrows != undefined && _.navigation.arrows.hide_under != undefined) _.navigation.arrows.hide_under = parseInt(_.navigation.arrows.hide_under);
		if (_.navigation !== undefined && _.navigation.bullets != undefined && _.navigation.bullets.hide_under != undefined) _.navigation.bullets.hide_under = parseInt(_.navigation.bullets.hide_under);
		if (_.navigation !== undefined && _.navigation.thumbnails != undefined && _.navigation.thumbnails.hide_under != undefined) _.navigation.thumbnails.hide_under = parseInt(_.navigation.thumbnails.hide_under);
		if (_.navigation !== undefined && _.navigation.tabs != undefined && _.navigation.tabs.hide_under != undefined) _.navigation.tabs.hide_under = parseInt(_.navigation.tabs.hide_under);

		if (_.navigation !== undefined && _.navigation.arrows != undefined && _.navigation.arrows.hide_over != undefined) _.navigation.arrows.hide_over = parseInt(_.navigation.arrows.hide_over);
		if (_.navigation !== undefined && _.navigation.bullets != undefined && _.navigation.bullets.hide_over != undefined) _.navigation.bullets.hide_over = parseInt(_.navigation.bullets.hide_over);
		if (_.navigation !== undefined && _.navigation.thumbnails != undefined && _.navigation.thumbnails.hide_over != undefined) _.navigation.thumbnails.hide_over = parseInt(_.navigation.thumbnails.hide_over);
		if (_.navigation !== undefined && _.navigation.tabs != undefined && _.navigation.tabs.hide_over != undefined) _.navigation.tabs.hide_over = parseInt(_.navigation.tabs.hide_over);

		if (_.lazyloaddata !== undefined && _.lazyloaddata.length > 0 && _.lazyloaddata.indexOf("-") > 0) {
			var temp = _.lazyloaddata.split("-");
			_.lazyloaddata = temp[0];
			for (var i = 1; i < temp.length; i++) _.lazyloaddata += jsUcfirst(temp[i]);
		}
		_.duration = parseInt(_.duration);

		if (_.lazyType === "single" && _.sliderType === "carousel") _.lazyType = "smart";
		if (_.sliderType === "carousel" && _.carousel.justify) {
			_.justifyCarousel = true;
			_.keepBPHeight = true;
		}
		_.enableUpscaling = _.enableUpscaling == true && _.sliderType !== "carousel" && _.sliderLayout === "fullwidth" ? true : false;
		_.useFullScreenHeight = _.sliderType === "carousel" && _.sliderLayout === "fullscreen" && _.useFullScreenHeight === true;
		_.progressBar.y = parseInt(_.progressBar.y, 0);
		_.progressBar.x = parseInt(_.progressBar.x, 0);

		/*! Custom Eases */
		if (window.RSBrowser !== "IE" && _.customEases !== undefined) {
			if ((_.customEases.SFXBounceLite || _.customEases.SFXBounceLite == "true") && tpGS.SFXBounceLite === undefined) tpGS.SFXBounceLite = tpGS.CustomBounce.create("SFXBounceLite", { strength: 0.3, squash: 1, squashID: "SFXBounceLite-squash" });
			if ((_.customEases.SFXBounceSolid || _.customEases.SFXBounceSolid == "true") && tpGS.SFXBounceSolid === undefined) tpGS.SFXBounceSolid = tpGS.CustomBounce.create("SFXBounceSolid", { strength: 0.5, squash: 2, squashID: "SFXBounceSolid-squash" });
			if ((_.customEases.SFXBounceStrong || _.customEases.SFXBounceStrong == "true") && tpGS.SFXBounceStrong === undefined) tpGS.SFXBounceStrong = tpGS.CustomBounce.create("SFXBounceStrong", { strength: 0.7, squash: 3, squashID: "SFXBounceStrong-squash" });
			if ((_.customEases.SFXBounceExtrem || _.customEases.SFXBounceExtrem == "true") && tpGS.SFXBounceExtrem === undefined) tpGS.SFXBounceExtrem = tpGS.CustomBounce.create("SFXBounceExtrem", { strength: 0.9, squash: 4, squashID: "SFXBounceExtrem-squash" });
			if ((_.customEases.BounceLite || _.customEases.BounceLite == "true") && tpGS.BounceLite === undefined) tpGS.BounceLite = tpGS.CustomBounce.create("BounceLite", { strength: 0.3 });
			if ((_.customEases.BounceSolid || _.customEases.BounceSolid == "true") && tpGS.BounceSolid === undefined) tpGS.BounceSolid = tpGS.CustomBounce.create("BounceSolid", { strength: 0.5 });
			if ((_.customEases.BounceStrong || _.customEases.BounceStrong == "true") && tpGS.BounceStrong === undefined) tpGS.BounceStrong = tpGS.CustomBounce.create("BounceStrong", { strength: 0.7 });
			if ((_.customEases.BounceExtrem || _.customEases.BounceExtrem == "true") && tpGS.BounceExtrem === undefined) tpGS.BounceExtrem = tpGS.CustomBounce.create("BounceExtrem", { strength: 0.9 });
		}

		_.modal.coverSpeed = parseFloat(_.modal.coverSpeed);
		_.modal.coverSpeed = _.modal.coverSpeed > 200 ? _.modal.coverSpeed / 1000 : _.modal.coverSpeed;
		_.modal.coverSpeed = Math.max(Math.min(3, _.modal.coverSpeed), 0.3);

		_.navigation.wheelViewPort = _.navigation.wheelViewPort === undefined ? 0.5 : _.navigation.wheelViewPort / 100;
		_.navigation.wheelCallDelay = _.navigation.wheelCallDelay === undefined ? 1000 : parseInt(_.navigation.wheelCallDelay);
		_.autoDPR = typeof _.DPR === 'string' && _.DPR.indexOf('ax') !== -1;
		_.DPR = _.DPR.replace("ax", "");
		_.DPR = parseInt(_.DPR.replace("x", ""));
		_.DPR = isNaN(_.DPR) ? window.devicePixelRatio : _.autoDPR ? Math.min(window.devicePixelRatio, _.DPR) : _.DPR;
		_.DPR = (_.onedpronmobile == true || _.onedpronmobile == "true") && _R.ISM ? 1 : _.DPR;

		if(_.viewPort.global === false) _.viewPort.enable = false;
		else if(_.viewPort.global === true) {
			_.viewPort.local = _.viewPort.enable;
			_.viewPort.enable = true;
		}
		return _;
	};



	var jsUcfirst = function(string) { return string.charAt(0).toUpperCase() + string.slice(1); }


	var getModuleDefaults = function(options) {
		return generalConverts(jQuery.extend(true, {
			DPR: "dpr", // THE SCREEN MAX RESOLUTION DPR
			sliderType: "standard", // standard, carousel, hero
			sliderLayout: "auto", // auto, fullwidth, fullscreen
			//dottedOverlay:"none",					//twoxtwo, threexthree, twoxtwowhite, threexthreewhite
			overlay: {
				type: "none",
				size: 1,
				colora: 'transparent',
				colorb: '#000000'
			},
			duration: 9000,
			imgCrossOrigin: "",
			modal: {
				useAsModal: false,
				cover: true,
				coverColor: "rgba(0,0,0,0.5)",
				horizontal: "center",
				vertical: "middle",
				coverSpeed: 1
			},
			navigation: {
				keyboardNavigation: false,
				keyboard_direction: "horizontal", //	horizontal - left/right arrows,  vertical - top/bottom arrows
				mouseScrollNavigation: 'off', // on, off, carousel
				wheelViewPort: 50,
				wheelCallDelay: '1000ms',
				/*msWayUp:"top",							// if mouseScrollNavigation === "on" top/center/bottom/ignore
				msWayDown:"top",						// if mouseScrollNavigation === "on" top/center/bottom/ignore
				msWayUpOffset:0,						// if mouseScrollNavigation === "on" px
				msWayDownOffset:0,						// if mouseScrollNavigation === "on" px						*/
				onHoverStop: true, // Stop Banner Timet at Hover on Slide on/off
				mouseScrollReverse: "default",
				touch: {
					touchenabled: false, // Enable Swipe Function : on/off
					touchOnDesktop: false, // Enable Tuoch on Desktop Systems also
					swipe_treshold: 75, // The number of pixels that the user must move their finger by before it is considered a swipe.
					swipe_min_touches: 1, // Min Finger (touch) used for swipe
					swipe_direction: "horizontal",
					drag_block_vertical: false, // Prevent Vertical Scroll during Swipe
					mobileCarousel: true,
					desktopCarousel: true
				},
				arrows: {
					style: "",
					enable: false,
					hide_onmobile: false,
					hide_under: 0,
					hide_onleave: false,
					hide_delay: 200,
					hide_delay_mobile: 1200,
					hide_over: 9999,
					tmp: '',
					rtl: false,
					left: {
						h_align: "left",
						v_align: "center",
						h_offset: 20,
						v_offset: 0,
						container: "slider"
					},
					right: {
						h_align: "right",
						v_align: "center",
						h_offset: 20,
						v_offset: 0,
						container: "slider"
					}
				},
				bullets: {
					enable: false,
					hide_onmobile: false,
					hide_onleave: false,
					hide_delay: 200,
					hide_delay_mobile: 1200,
					hide_under: 0,
					hide_over: 9999,
					direction: "horizontal",
					h_align: "center",
					v_align: "bottom",
					space: 5,
					h_offset: 0,
					v_offset: 20,
					tmp: '<span class="tp-bullet-image"></span><span class="tp-bullet-title"></span>',
					container: "slider",
					rtl: false,
					style: ""
				},
				thumbnails: {
					container: "slider",
					rtl: false,
					style: "",
					enable: false,
					width: 100,
					height: 50,
					min_width: 100,
					wrapper_padding: 2,
					wrapper_color: "transparent",
					tmp: '<span class="tp-thumb-image"></span><span class="tp-thumb-title"></span>',
					visibleAmount: 5,
					hide_onmobile: false,
					hide_onleave: false,
					hide_delay: 200,
					hide_delay_mobile: 1200,
					hide_under: 0,
					hide_over: 9999,
					direction: "horizontal",
					span: false,
					position: "inner",
					space: 2,
					h_align: "center",
					v_align: "bottom",
					h_offset: 0,
					v_offset: 20,
					mhoff: 0,
					mvoff: 0
				},
				tabs: {
					container: "slider",
					rtl: false,
					style: "",
					enable: false,
					width: 100,
					min_width: 100,
					height: 50,
					wrapper_padding: 10,
					wrapper_color: "transparent",
					tmp: '<span class="tp-tab-image"></span>',
					visibleAmount: 5,
					hide_onmobile: false,
					hide_onleave: false,
					hide_delay: 200,
					hide_delay_mobile: 1200,
					hide_under: 0,
					hide_over: 9999,
					direction: "horizontal",
					span: false,
					space: 0,
					position: "inner",
					h_align: "center",
					v_align: "bottom",
					h_offset: 0,
					v_offset: 20,
					mhoff: 0,
					mvoff: 0
				}
			},

			responsiveLevels: 4064, // Single or Array for Responsive Levels i.e.: 4064 or i.e. [2048, 1024, 778, 480]
			visibilityLevels: [2048, 1024, 778, 480], // Single or Array for Responsive Visibility Levels i.e.: 4064 or i.e. [2048, 1024, 778, 480]
			gridwidth: 960, // Single or Array i.e. 960 or [960, 840,760,460]
			gridheight: 500, // Single or Array i.e. 500 or [500, 450,400,350]
			minHeight: 0,
			maxHeight: 0,
			keepBPHeight: false,
			useFullScreenHeight: true, // Use FullScreen Height for Content if Carousel Mode is on and FullScreen Layout selected
			overflowHidden: false, // Use Overflow Hidden in carouselMode (force)
			forceOverflow: false, // Use OverFlow Visible (Force) in None Carousel Mode
			fixedOnTop: false,
			autoHeight: false,
			gridEQModule: false,
			disableForceFullWidth: false, // Turns the FullScreen Slider to be a FullHeight but auto Width Slider

			fullScreenOffsetContainer: "", // Size for FullScreen Slider minimising Calculated on the Container sizes
			fullScreenOffset: "0", // Size for FullScreen Slider minimising

			hideLayerAtLimit: 0, // It Defines if a caption should be shown under a Screen Resolution ( Basod on The Width of Browser)
			hideAllLayerAtLimit: 0, // Hide all The Captions if Width of Browser is less then this value
			hideSliderAtLimit: 0, // Hide the whole slider, and stop also functions if Width of Browser is less than this value
			progressBar: {
				disableProgressBar: false, // Hides Progress Bar if is set to "on"
				style: "horizontal",
				size: "5px",
				radius: 10,
				vertical: "bottom",
				horizontal: "left",
				x: 0,
				y: 0,
				color: 'rgba(255,255,255,0.5)',
				bgcolor: 'transparent',
				basedon: "slide",
				gapsize: 0,
				reset: 'reset',
				gaptype: "gapboth",
				gapcolor: 'rgba(255,255,255,0.5)',
				ease: 'none',
				visibility: {
					0: true, //desktop
					1: true, //notebok
					2: true, //tablet
					3: true //mobile
				}
			},
			stopAtSlide: -1, // Stop Timer if Slide "x" has been Reached. If stopAfterLoops set to 0, then it stops already in the first Loop at slide X which defined. -1 means do not stop at any slide. stopAfterLoops has no sinn in this case.
			stopAfterLoops: 0, // Stop Timer if All slides has been played "x" times. IT will stop at THe slide which is defined via stopAtSlide:x, if set to -1 slide never stop automatic
			shadow: 0, //0 = no Shadow, 1,2,3 = 3 Different Art of Shadows  (No Shadow in Fullwidth Version !)

			startDelay: 0, // Delay before the first Animation starts.
			lazyType: "none", //all, smart, single
			spinner: "off",
			shuffle: false, // Random Order of Slides,
			perspective: "600px",
			perspectiveType: "local", // Perspective Will be set Locally depenent of Layer Values

			viewPort: {
				enable: false, // if enabled, slider wait with start or wait at first slide.
				global: false,
				globalDist: '-400px',
				outof: "wait", // wait,pause
				visible_area: "200px", // Start Animation when 60% of Slider is visible
				presize: false // Presize the Height of the Slider Container for Internal Link Positions
			},

			fallbacks: {
				isJoomla: false,
				panZoomDisableOnMobile: false,
				simplifyAll: true,
				nextSlideOnWindowFocus: false,
				disableFocusListener: false,
				allowHTML5AutoPlayOnAndroid: true
			},

			fanim: false, // No Global First Different Animation set

			parallax: {
				type: "off", // off, mouse, scroll, mouse+scroll
				levels: [10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85],
				origo: "enterpoint", // slidercenter or enterpoint
				disable_onmobile: false,
				ddd_shadow: false,
				ddd_bgfreeze: false,
				ddd_overflow: "visible",
				ddd_layer_overflow: "visible",
				ddd_z_correction: 65,
				speed: 400,
				speedbg: 0,
				speedls: 0
			},

			scrolleffect: {
				set: false,
				fade: false,
				blur: false,
				scale: false,
				grayscale: false,
				maxblur: 10,
				layers: false,
				slide: false,
				direction: "both",
				multiplicator: 1.35,
				multiplicator_layers: 0.5,
				tilt: 30,
				disable_onmobile: false
			},

			sbtimeline: { // SCROLL BASED TIMELINE
				set: false,
				fixed: false,
				fixStart: 0,
				fixEnd: 0,
				layers: false,
				slide: false,
				ease: "none",
				speed: 500
			},

			carousel: {
				easing: "power3.inOut",
				speed: 800,
				showLayersAllTime: false,
				horizontal_align: "center",
				vertical_align: "center",
				infinity: false,
				space: 0,
				maxVisibleItems: 3,
				stretch: false,
				fadeout: true,
				maxRotation: 0,
				maxOpacity: 100,
				minScale: 0,
				offsetScale: false,
				vary_fade: false,
				vary_rotation: false,
				vary_scale: false,
				border_radius: "0px",
				padding_top: 0,
				padding_bottom: 0
			},
			observeWrap: false,
			extensions: "extensions/", //example extensions/ or extensions/source/
			extensions_suffix: ".min.js",
			//addons:[{fileprefix:"revolution.addon.whiteboard",init:"initWhiteBoard",params:"opt",handel:"whiteboard"}],
			stopLoop: false,
			waitForInit: false,
			ignoreHeightChange: true,
			onedpronmobile: false
		}, options));
	};
	//Support Defer and Async and Footer Loads
	window.RS_MODULES = window.RS_MODULES || {};
	window.RS_MODULES.waiting = window.RS_MODULES.waiting || [];
	window.RS_MODULES.waiting = window.RS_MODULES.waiting.concat(['DOM','main', 'parallax', 'video', 'slideanims', 'actions', 'layeranimation', 'navigation', 'carousel', 'panzoom']);
	window.RS_MODULES.main = { loaded: true, version: version };
	window.RS_MODULES.minimal = false;

	// INIT THE SLIDERS HERE
	window.RS_MODULES.callSliders = function() {
		for (var i in RS_MODULES.modules) {
			if (RS_MODULES.modules[i].once !== true && window.RS_MODULES !== undefined && window.RS_MODULES.minimal) {
				RS_MODULES.modules[i].once = true;
				RS_MODULES.modules[i].init();
			}
		}
	}

	// ELEMENTOR SPECIALS
	function elementorGlobalHook($scope){
		elementorFrontend.hooks.removeAction( 'frontend/element_ready/global', elementorGlobalHook);
		window.RS_MODULES.elementor = {loaded:true, version:'6.5.0'};
		if (window.RS_MODULES.checkMinimal) window.RS_MODULES.checkMinimal();	// Do something that is based on the elementorFrontend object.
	}

	// ELEMENTOR INIT WAITS
	function addElementorHook() {
		if (window.elementorFrontend===undefined || window.elementorFrontend.hooks===undefined || window.elementorFrontend.hooks.addAction===undefined) {
			requestAnimationFrame(addElementorHook);
			return;
		}
		if (window.elementorFrontend.config.environmentMode.edit)
			elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', elementorGlobalHook);
		else elementorGlobalHook();
	}

	function checkElementor() {
		// CHECK ELEMENTOR EXISTS
		if (document.body && document.body.className.indexOf('elementor-page')>=0) {
			window.RS_MODULES.waiting = window.RS_MODULES.waiting===undefined ? [] : window.RS_MODULES.waiting;
			window.RS_MODULES.waiting.push('elementor');
			addElementorHook();
		}
	}

	// IF DOCUMENT NOT LADED YET !??? ( NOT POSSIBLE...)
	if (document.readyState === "loading")
		document.addEventListener('readystatechange', function() {
			if (document.readyState === "interactive" || document.readyState === "complete") {
				checkElementor();
				 window.RS_MODULES.DOM = {loaded:true};
				 window.RS_MODULES.checkMinimal();
			}
		});
	else if (document.readyState === "complete" || document.readyState === "interactive") {
		checkElementor();
		window.RS_MODULES.DOM = {loaded:true};
	}

	//CHECK FOR MINIMAL REQUIREMENTS
	window.RS_MODULES.checkMinimal = function() {
		if (window.RS_MODULES.minimal == false) {
			var allthere = window.RS_MODULES.minimal == true ? true : window.RS_MODULES.waiting !== undefined && jQuery.fn.revolution !== undefined && window.tpGS !== undefined && window.tpGS.gsap !== undefined;
			if (allthere){
				for (var i in window.RS_MODULES.waiting) {
					if(!window.RS_MODULES.waiting.hasOwnProperty(i)) continue;
					if (!allthere) continue;
					if (window.RS_MODULES[window.RS_MODULES.waiting[i]] === undefined) {
						allthere = false;
					}
				}
			}

			if (allthere) {
				if (window.RS_MODULES.minimal !== true) jQuery(document).trigger('REVSLIDER_READY_TO_USE');
				window.RS_MODULES.minimal = true;
			}
		} else {
			window.RS_MODULES.minimal = true;
		}
		if (window.RS_MODULES.minimal === true) window.RS_MODULES.callSliders()

	}
	window.RS_MODULES.checkMinimal();
})(jQuery);