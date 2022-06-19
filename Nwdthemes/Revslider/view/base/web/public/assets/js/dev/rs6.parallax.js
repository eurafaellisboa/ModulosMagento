/********************************************
 * REVOLUTION EXTENSION - PARALLAX
 * @date: 03.10.2020
 * @requires rs6.main.js
 * @author ThemePunch
 *********************************************/
(function ($) {
	"use strict";
	var version = "6.2.24";
	jQuery.fn.revolution = jQuery.fn.revolution || {};
	var _R = jQuery.fn.revolution;

	jQuery.extend(true, _R, {

		checkForParallax: function (id) {

			var _ = _R[id].parallax;


			if (_.done) return;
			_.done = true;

			if (_R.ISM && _.disable_onmobile) return false;

			if (_.type == "3D" || _.type == "3d") {
				_R.addSafariFix(id);
				tpGS.gsap.set(_R[id].c, {
					overflow: _.ddd_overflow
				});
				tpGS.gsap.set(_R[id].canvas, {
					overflow: _.ddd_overflow
				});
				if (_R[id].sliderType != "carousel" && _.ddd_shadow) {
					var dddshadow = jQuery('<div class="dddwrappershadow"></div>');
					tpGS.gsap.set(dddshadow, {
						force3D: "auto",
						transformPerspective: 1600,
						transformOrigin: "50% 50%",
						width: "100%",
						height: "100%",
						position: "absolute",
						top: 0,
						left: 0,
						zIndex: 0
					});
					_R[id].c.prepend(dddshadow);
				}



				for (var i in _R[id].slides)
					if (_R[id].slides.hasOwnProperty(i)) setDDDInContainer(jQuery(_R[id].slides[i]),id);
				if (_R[id].c.find('rs-static-layers').length > 0) {
					tpGS.gsap.set(_R[id].c.find('rs-static-layers'), {
						top: 0,
						left: 0,
						width: "100%",
						height: "100%"
					});
					setDDDInContainer(_R[id].c.find('rs-static-layers'),id);
				}
			}

			_.pcontainers = {};
			_.bgcontainers = [];
			_.bgcontainer_depths = [];
			_.speed = _.speed === undefined ? 0 : parseInt(_.speed, 0);
			_.speedbg = _.speedbg === undefined ? 0 : parseInt(_.speedbg, 0);
			_.speedls = _.speedls === undefined ? 0 : parseInt(_.speedls, 0);

			_R[id].c.find('rs-slide rs-sbg-wrap, rs-slide rs-bgvideo').each(function () {
				var t = jQuery(this),
					l = t.data('parallax');

				//This Solves Flickering on BG Elements in Mac Chrome
				if (!window.isSafari11) _R[id].parZ = 1;

				l = l == "on" || l === true ? 1 : l;
				if (l !== undefined && (l !== "off" && l !== false)) {
					_.bgcontainers.push(t.closest('rs-sbg-px'));
					_.bgcontainer_depths.push(_R[id].parallax.levels[parseInt(l, 0) - 1] / 100);
				}
			});

			for (var i = 1; i <= _.levels.length; i++) {
				for(var key in _R[id].slides){
					if(!_R[id].slides.hasOwnProperty(key)) continue;
					var slide = _R[id].slides[key];
					var skey = slide.dataset.key;
					if(_.pcontainers[skey] === undefined) _.pcontainers[skey] = {};
					collectParallaxTargets(i, _, slide, _.pcontainers[skey])
				}

				var skey = 'static';
				if(_.pcontainers[skey] === undefined) _.pcontainers[skey] = {};
				collectParallaxTargets(i, _, _R[id].slayers[0], _.pcontainers[skey]);
				if (JSON.stringify(_.pcontainers[skey]) == JSON.stringify({})) delete _.pcontainers[skey];
			}

			if (_.type == "mouse" || _.type == "mousescroll" || _.type == "3D" || _.type == "3d") {
				var sctor = 'rs-slide .dddwrapper, .dddwrappershadow, rs-slide .dddwrapper-layer, rs-static-layers .dddwrapper-layer';
				if (_R[id].sliderType === "carousel") sctor = "rs-slide .dddwrapper, rs-slide .dddwrapper-layer, rs-static-layers .dddwrapper-layer";

				_.sctors = {};

				for(var key in _R[id].slides){
					if(!_R[id].slides.hasOwnProperty(key)) continue;
					var slide = _R[id].slides[key];
					var skey = slide.dataset.key;

					_.sctors[skey] = slide.querySelectorAll(sctor);
				}

				if(_R[id].slayers[0]) _.sctors['static'] = _R[id].slayers[0].querySelectorAll(sctor);
				_.mouseEntered = false;

				_R[id].c.on('mouseenter', function (e) {
					var t = _R[id].c.offset().top,
						l = _R[id].c.offset().left;

					_.mouseEnterX = e.pageX - l;
					_.mouseEnterY = e.pageY - t;
					_.mouseEntered = true;

				});

				var parallaxHandler = this.updateParallax.bind(this, id, _);

				_R[id].c.on('mousemove.hoverdir, mouseleave.hoverdir, trigger3dpath', function (e) {
					_.eventData = e;
					if (_.frame === undefined || e.type === "mouseleave") _.frame = window.requestAnimationFrame(parallaxHandler);
				});

				if (_R.ISM) {
					window.addEventListener("deviceorientation", function (e) {
						_.eventData = e;
						if (_.frame === undefined) _.frame = window.requestAnimationFrame(parallaxHandler);
					});
				}

			}

			// COLLECT ALL ELEMENTS WHICH NEED FADE IN/OUT ON PARALLAX SCROLL
			var _s = _R[id].scrolleffect;

			if (_s.set) {
				_s.multiplicator_layers = parseFloat(_s.multiplicator_layers);
				_s.multiplicator = parseFloat(_s.multiplicator);
			}

			if (_s._L !== undefined && _s._L.length === 0) _s._L = false;
			if (_s.bgs !== undefined && _s.bgs.length === 0) _s.bgs = false;
		},
		getLayerParallaxOffset : function(id,lid,dir) {
			return _R[id].parallax!==undefined && _R[id].parallax.pcontainers!==undefined && _R[id].parallax.pcontainers[_R[id]._L[lid].slidekey]!==undefined && _R[id].parallax.pcontainers[_R[id]._L[lid].slidekey][lid]!==undefined ? Math.abs(_R[id].parallax.pcontainers[_R[id]._L[lid].slidekey][lid]['offs'+dir]) : 0;
		},
		/**
		 *
		 * @param {string} id id to be passed to _R revolution object to retrive revolution instance
		 * @param {Object} _ shorthand for _R[id].parallax object
		 */
		updateParallax: function (id, _) {
			if (_.frame) _.frame = window.cancelAnimationFrame(_.frame);

			var e = _.eventData;
			var l = _R[id].c.offset().left,
				t = _R[id].c.offset().top,
				cw = _R[id].canv.width,
				ch = _R[id].canv.height,
				s = _.speed / 1000 || 3,
				diffh, diffv;

			// CALCULATE DISTANCES
			if (_.origo == "enterpoint" && e.type !== "deviceorientation") {

				if (_.mouseEntered === false) {
					_.mouseEnterX = e.pageX - l;
					_.mouseEnterY = e.pageY - t;
					_.mouseEntered = true;
				}

				diffh = _.mouseEnterX - (e.pageX - l);
				diffv = _.mouseEnterY - (e.pageY - t);
				s = _.speed / 1000 || 0.4;
			} else if (e.type !== "deviceorientation") {
				diffh = cw / 2 - (e.pageX - l);
				diffv = ch / 2 - (e.pageY - t);
			}

			if (e.type == "deviceorientation") {
				var beta, gamma, x, y;
				beta = e.beta - 60;
				gamma = e.gamma;

				x = gamma;
				y = beta;

				var orientationChanged = (Math.abs(_.orientationX - x) > 1 || Math.abs(_.orientationY - y) > 1);
				_.orientationX = x;
				_.orientationY = y;
				if (!orientationChanged) return;

				if (_R.winW > _R.getWinH(id)) {
					var xx = x;
					x = y;
					y = xx;
				}
				x *= 1.5;
				y *= 1.5;

				diffh = (360 / cw * x);
				diffv = (180 / ch * y);
			}

			if (e.type === "mouseleave", e.type === "mouseout") {
				diffh = 0;
				diffv = 0;
				_.mouseEntered = false;
			}

			for(var slidekey in _.pcontainers){
				if(!_.pcontainers.hasOwnProperty(slidekey)) continue;

				if(_R[id].activeRSSlide === undefined || slidekey === 'static' || _R[id].slides[_R[id].activeRSSlide].dataset.key === slidekey){
					for (var i in _.pcontainers[slidekey]) {
						if (!_.pcontainers[slidekey].hasOwnProperty(i)) continue;
						var pc = _.pcontainers[slidekey][i];
						pc.pl = _.type == "3D" || _.type == "3d" ? pc.depth / 200 : pc.depth / 100;
						pc.offsh = diffh * pc.pl,
						pc.offsv = diffv * pc.pl;

						if (_.type == "mousescroll")
							tpGS.gsap.to(pc.tpw, s, {
								force3D: "auto",
								x: pc.offsh,
								ease: "power3.out",
								overwrite: "all"
							});
						else
							tpGS.gsap.to(pc.tpw, s, {
								force3D: "auto",
								x: pc.offsh,
								y: pc.offsv,
								ease: "power3.out",
								overwrite: "all"
							});
					}
				}

			}

			if (_.type == "3D" || _.type == "3d") {

				for(var slidekey in _.sctors){
					if(!_.sctors.hasOwnProperty(slidekey)) continue;
					if(!(_R[id].activeRSSlide === undefined || slidekey === 'static' || _R[id].slides[_R[id].activeRSSlide].dataset.key === slidekey) && !_R.isFF) continue;
					for(var i in _.sctors[slidekey]){
						if(!_.sctors[slidekey].hasOwnProperty(i)) continue;

						var t = jQuery(_.sctors[slidekey][i]),
						pl = _R.isFirefox() ? Math.min(25, _.levels[_.levels.length - 1]) / 200 : _.levels[_.levels.length - 1] / 200,
						offsh = diffh * pl,
						offsv = diffv * pl,
						offrv = _R[id].canv.width == 0 ? 0 : Math.round((diffh / _R[id].canv.width * pl) * 100) || 0,
						offrh = _R[id].canv.height == 0 ? 0 : Math.round((diffv / _R[id].canv.height * pl) * 100) || 0,
						li = t.closest('rs-slide'),
						zz = 0,
						itslayer = false;

					if (e.type === "deviceorientation") {
						pl = _.levels[_.levels.length - 1] / 200,
							offsh = diffh * pl;
						offsv = diffv * pl * 3;
						offrv = _R[id].canv.width == 0 ? 0 : Math.round((diffh / _R[id].canv.width * pl) * 500) || 0;
						offrh = _R[id].canv.height == 0 ? 0 : Math.round((diffv / _R[id].canv.height * pl) * 700) || 0;
					}

					if (t.hasClass("dddwrapper-layer")) {
						zz = _.ddd_z_correction || 65;
						itslayer = true;
					}

					if (t.hasClass("dddwrapper-layer")) {
						offsh = 0;
						offsv = 0;
					}

					if (li.index() === _R[id].pr_active_key || _R[id].sliderType != "carousel")
						if (!_.ddd_bgfreeze || (itslayer))
							tpGS.gsap.to(t, s, {
								rotationX: offrh,
								rotationY: -offrv,
								x: offsh,
								z: zz,
								y: offsv,
								ease: "power3.out",
								overwrite: "all"
							});
						else
							tpGS.gsap.to(t, 0.5, {
								force3D: "auto",
								rotationY: 0,
								rotationX: 0,
								z: 0,
								ease: "power3.out",
								overwrite: "all"
							});
					else
						tpGS.gsap.to(t, 0.5, {
							force3D: "auto",
							rotationY: 0,
							x: 0,
							y: 0,
							rotationX: 0,
							z: 0,
							ease: "power3.out",
							overwrite: "all"
						});

					if (e.type == "mouseleave" || e.type === "mouseout")
						tpGS.gsap.to(this, 3.8, {
							z: 0,
							ease: "power3.out"
						});
					}

				}

			}
		},
		parallaxProcesses: function (id, b, ignorelayers, speedoverwrite) {

			var mproc = _R[id].fixedOnTop ? Math.min(1, Math.max(0, (window.scrollY / _R.lastwindowheight))) : Math.min(1, Math.max(0, ( 0 - (b.top - _R.lastwindowheight)) / (b.hheight + _R.lastwindowheight))),
				visible = (b.top >= 0 && b.top <= _R.lastwindowheight) || (b.top <= 0 && b.bottom >= 0) || (b.top <= 0 && b.bottom >= 0),
				slide = _R[id].slides[_R[id].pr_active_key === undefined ? 0 : _R[id].pr_active_key];

			_R[id].scrollProg = mproc;
			_R[id].scrollProgBasics = { top:b.top, height:b.hheight, bottom:b.bottom};

			// FIXED TOP POSITION ON SCROLL (STICKY SLIDER)
			if (_R[id].sbtimeline.fixed) {
				if (_R[id].fixedScrollOnState !== false && _R.stickySupported && (_R[id].fullScreenOffsetResult==0 || _R[id].fullScreenOffsetResult==undefined)) {
					_R[id].topc.addClass("rs-stickyscrollon");
					_R[id].fixedScrollOnState = true;
				} else _R.stickySupported=false;
				if (_R[id].sbtimeline.rest === undefined) _R.updateFixedScrollTimes(id);
				if (b.top >= _R[id].fullScreenOffsetResult && b.top <= _R.lastwindowheight) {

					mproc = (_R[id].sbtimeline.fixStart * (1 - (b.top / _R.lastwindowheight))) / 1000;
					if (_R.stickySupported !== true && _R[id].fixedScrollOnState !== false) {
						_R[id].topc.removeClass("rs-fixedscrollon");
						tpGS.gsap.set(_R[id].cpar, {
							top: 0,
							y:0
						});
						_R[id].fixedScrollOnState = false;
					}
				} else
				if (b.top <= _R[id].fullScreenOffsetResult && b.bottom >= _R[id].module.height) {

					if (_R.stickySupported !== true && _R[id].fixedScrollOnState !== true) {
						_R[id].fixedScrollOnState = true;
						_R[id].topc.addClass("rs-fixedscrollon");
						tpGS.gsap.set(_R[id].cpar, {
							top: 0,
							y:_R[id].fullScreenOffsetResult
						});
					}
					mproc = (_R[id].sbtimeline.fixStart + (_R[id].sbtimeline.time * (Math.abs(b.top) / (b.hheight - _R[id].module.height)))) / 1000;
				} else {
					if (_R.stickySupported !== true) {
						tpGS.gsap.set(_R[id].cpar, {
							top: _R[id].scrollproc >= 0 ? 0 : (b.height - _R[id].module.height)
						});
						if (_R[id].fixedScrollOnState !== false) {
							_R[id].topc.removeClass("rs-fixedscrollon");
							_R[id].fixedScrollOnState = false;
						}
					}
					mproc = b.top > _R.lastwindowheight ? 0 : (_R[id].sbtimeline.fixEnd + (_R[id].sbtimeline.rest * (1 - (b.bottom / _R[id].module.height)))) / 1000;
				}

			} else mproc = (_R[id].duration * mproc) / 1000;


			// Erste Animation mit Speed, dann vielleicht immer sofort auf position springen !

			//Animate to Timeline Based on Scroll Position
			if (slide !== undefined && _R.gA(slide, "key") !== undefined && ignorelayers !== true) {
				var wasnotprepared = 0; //check if Timelines prepared already
				for (var sba in _R[id].sbas[_R.gA(slide, "key")]) {
					if (_R[id]._L[sba] !== undefined && _R[id]._L[sba].timeline == undefined) wasnotprepared++;
					if (_R[id]._L[sba] !== undefined && _R[id]._L[sba].timeline !== undefined && (_R[id]._L[sba].animationonscroll == true || _R[id]._L[sba].animationonscroll == "true")) {
						wasnotprepared = -9999; // This makes sure, that no unneeded recalls happens
						var time = (_R[id]._L[sba].scrollBasedOffset !== undefined ? mproc + _R[id]._L[sba].scrollBasedOffset : mproc);
						time = time <= 0 ? 0 : time < 0.1 ? 0.1 : time;

						if (_R[id]._L[sba].animteToTime !== time) {
							_R[id]._L[sba].animteToTime = time;
							tpGS.gsap.to(_R[id]._L[sba].timeline, _R[id].sbtimeline.speed, {
								time: time,
								ease: _R[id].sbtimeline.ease
							});
						}
					}
				}
				if (wasnotprepared>0) requestAnimationFrame(function() {
					_R.parallaxProcesses(id, b, ignorelayers, speedoverwrite);
				});
				_R[id].c.trigger('timeline_scroll_processed',{id:id,mproc:mproc,speed:_R[id].sbtimeline.speed});
			}


			// SCROLL BASED PARALLAX EFFECT
			if (_R.ISM && _R[id].parallax.disable_onmobile) return false;

			var _ = _R[id].parallax, procslidekey;
			if (_R[id].slides[_R[id].pr_processing_key]!==undefined && _R[id].slides[_R[id].pr_processing_key].dataset!==undefined) procslidekey = _R[id].slides[_R[id].pr_processing_key].dataset.key;
			if (_.type != "3d" && _.type != "3D") {
				if (_.type == "scroll" || _.type == "mousescroll")
					for(var slidekey in _.pcontainers)
						if(_.pcontainers.hasOwnProperty(slidekey) && (_R[id].activeRSSlide === undefined || slidekey === 'static' || _R[id].slides[_R[id].activeRSSlide].dataset.key === slidekey || procslidekey === slidekey))
							for (var i in _.pcontainers[slidekey])
								if (_.pcontainers[slidekey].hasOwnProperty(i)) {
									var pc = _.pcontainers[slidekey][i],
										s = speedoverwrite !== undefined ? speedoverwrite : _.speedls / 1000 || 0;
									pc.pl = pc.depth / 100;
									pc.offsv = Math.round((_R[id].scrollproc * -(pc.pl * _R[id].canv.height) * 10)) / 10 || 0;
									tpGS.gsap.to(pc.tpw, s, {
										overwrite: "auto",
										force3D: "auto",
										y: pc.offsv
									});
								}

				if (_.bgcontainers) {
					for (var i = 0; i < _.bgcontainers.length; i++) {
						var t = _.bgcontainers[i],
							l = _.bgcontainer_depths[i],
							offsv = _R[id].scrollproc * -(l * _R[id].canv.height) || 0,
							s = speedoverwrite !== undefined ? speedoverwrite : _.speedbg / 1000 || 0.015;
						s = _R[id].parallax.lastBGY !== undefined && s === 0 && Math.abs(offsv - _R[id].parallax.lastBGY) > 50 ? 0.15 : s;

						tpGS.gsap.to(t, s, {
							position: "absolute",
							top: "0px",
							left: "0px",
							backfaceVisibility: "hidden",
							force3D: "true",
							y: offsv + "px"
						});
						_R[id].parallax.lastBGY = offsv;
					}
				}
			}

			// SCROLL BASED BLUR,FADE,GRAYSCALE EFFECT
			var _s = _R[id].scrolleffect;
			if (_s.set && (!_R.ISM || _s.disable_onmobile === false)) {

				var _fproc = Math.abs(_R[id].scrollproc) - (_s.tilt / 100);
				_fproc = _fproc < 0 ? 0 : _fproc;
				if (_s._L !== false) {
					var elev = 1 - (_fproc * _s.multiplicator_layers),
						seo = {
							force3D: "true"
						};
					if (_s.direction == "top" && _R[id].scrollproc >= 0) elev = 1;
					if (_s.direction == "bottom" && _R[id].scrollproc <= 0) elev = 1;
					elev = elev > 1 ? 1 : elev < 0 ? 0 : elev;

					if (_s.fade) seo.opacity = elev;

					if (_s.scale) {
						var scalelevel = (elev);
						seo.scale = 1 + (1 - scalelevel);
					}

					if (_s.blur) {
						var blurlevel = (1 - elev) * _s.maxblur;

						// new line for fix, see comment above
						blurlevel = blurlevel <= 0.03 ? 0 : blurlevel;

						seo['-webkit-filter'] = 'blur(' + blurlevel + 'px)';
						seo.filter = 'blur(' + blurlevel + 'px)';
					}


					if (_s.grayscale) {
						var graylevel = (1 - elev) * 100,
							gf = 'grayscale(' + graylevel + '%)';
						seo['-webkit-filter'] = seo['-webkit-filter'] === undefined ? gf : seo['-webkit-filter'] + ' ' + gf;
						seo.filter = seo.filter === undefined ? gf : seo.filter + ' ' + gf;
					}

					tpGS.gsap.set(_s._L, seo);
				}

				if (_s.bgs !== false) {

					var elev = 1 - (_fproc * _s.multiplicator),
						seo = {
							backfaceVisibility: "hidden",
							force3D: "true"
						};
					if (_s.direction == "top" && _R[id].scrollproc >= 0) elev = 1;
					if (_s.direction == "bottom" && _R[id].scrollproc <= 0) elev = 1;
					elev = elev > 1 ? 1 : elev < 0 ? 0 : elev;
					for (var si in _s.bgs) {
						if (!_s.bgs.hasOwnProperty(si)) continue;
						if (_s.bgs[si].fade) seo.opacity = elev;

						if (_s.bgs[si].blur) {
							var blurlevel = (1 - elev) * _s.maxblur;
							seo['-webkit-filter'] = 'blur(' + blurlevel + 'px)';
							seo.filter = 'blur(' + blurlevel + 'px)';
						}

						if (_s.bgs[si].grayscale) {
							var graylevel = (1 - elev) * 100,
								gf = 'grayscale(' + graylevel + '%)';
							seo['-webkit-filter'] = seo['-webkit-filter'] === undefined ? gf : seo['-webkit-filter'] + ' ' + gf;
							seo.filter = seo.filter === undefined ? gf : seo.filter + ' ' + gf;
						}

						tpGS.gsap.set(_s.bgs[si].c, seo);
					}
				}
			}

		}
	});

	var setDDDInContainer = function(li,id) {

		var _ = _R[id].parallax;

		li.find('rs-sbg-wrap').wrapAll('<div class="dddwrapper" style="width:100%;height:100%;position:absolute;top:0px;left:0px;overflow:hidden"></div>');
		var players = li[0].querySelectorAll('.rs-parallax-wrap'),
			dddwrapper = document.createElement('div');

		dddwrapper.className = "dddwrapper-layer";
		dddwrapper.style.width = "100%";
		dddwrapper.style.height = "100%";
		dddwrapper.style.position = "absolute";
		dddwrapper.style.top = "0px";
		dddwrapper.style.left = "0px";
		dddwrapper.style.zIndex = 5;
		dddwrapper.style.overflow = _.ddd_layer_overflow;
		for (var i=0; i<players.length;i++) {
			if (!players.hasOwnProperty(i)) continue;
			if (_R.closestNode(players[i],'RS-GROUP')===null && _R.closestNode(players[i],'RS-ROW')===null) dddwrapper.appendChild(players[i]);
		}
		li[0].appendChild(dddwrapper);
		//li.find('.rs-parallax-wrap').wrapAll('<div class="dddwrapper-layer" style="width:100%;height:100%;position:absolute;top:0px;left:0px;z-index:5;overflow:' + _.ddd_layer_overflow + ';"></div>');

		// MOVE THE REMOVED 3D LAYERS OUT OF THE PARALLAX GROUP
		li.find('.rs-pxl-tobggroup').closest('.rs-parallax-wrap').wrapAll('<div class="dddwrapper-layertobggroup" style="position:absolute;top:0px;left:0px;z-index:50;width:100%;height:100%"></div>');

		var dddw = li.find('.dddwrapper'),
			dddwl = li.find('.dddwrapper-layer'),
			dddwlbg = li.find('.dddwrapper-layertobggroup');

		dddwlbg.appendTo(dddw);

		if (_R[id].sliderType == "carousel") {
			if (_.ddd_shadow) dddw.addClass("dddwrappershadow");
			tpGS.gsap.set(dddw, {
				borderRadius: _R[id].carousel.border_radius
			});
		}
		tpGS.gsap.set(li, {
			overflow: "visible",
			transformStyle: "preserve-3d",
			perspective: 1600
		});
		tpGS.gsap.set(dddw, {
			force3D: "auto",
			transformOrigin: "50% 50%",
			transformStyle: "preserve-3d",
			transformPerspective: 1600
		});
		tpGS.gsap.set(dddwl, {
			force3D: "auto",
			transformOrigin: "50% 50%",
			zIndex: 5,
			transformStyle: "flat",
			transformPerspective: 1600
		});
		tpGS.gsap.set(_R[id].canvas, {
			transformStyle: "preserve-3d",
			transformPerspective: 1600
		});
	}

    function collectParallaxTargets(i, _, slide, pcontainer){
        $(slide).find('.rs-pxl-' + i).each(function () {
            var rspxmask = this.className.indexOf('rs-pxmask') >= 0,
                tpw = rspxmask ? _R.closestNode(this,'RS-PX-MASK') : _R.closestClass(this,'rs-parallax-wrap');
            if(!tpw) return;

            if (rspxmask && !window.isSafari11) {
                tpGS.gsap.set(tpw,{z:1});
                tpGS.gsap.set(_R.closestNode(tpw,'RS-BG-ELEM'),{z:1});
            }

            tpw.dataset['parallaxlevel'] = _.levels[i - 1];
            tpw.classList.add("tp-parallax-container");

            pcontainer[this.id] = {tpw:tpw, depth:_.levels[i-1], offsv:0, offsh:0};
        });
    }

//Support Defer and Async and Footer Loads
window.RS_MODULES = window.RS_MODULES || {};
window.RS_MODULES.parallax = {loaded:true, version:version};
if (window.RS_MODULES.checkMinimal) window.RS_MODULES.checkMinimal();

})(jQuery);