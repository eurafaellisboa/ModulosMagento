 /********************************************
 * REVOLUTION  EXTENSION - CAROUSEL
 * @date:  24.01.2020
 * @requires rs6.main.js
 * @author ThemePunch
*********************************************/

(function($) {
	"use strict";
var version="6.2.0";
jQuery.fn.revolution = jQuery.fn.revolution || {};
var _R = jQuery.fn.revolution;

		///////////////////////////////////////////
		// 	EXTENDED FUNCTIONS AVAILABLE GLOBAL  //
		///////////////////////////////////////////
	jQuery.extend(true,_R, {

		// CALCULATE CAROUSEL POSITIONS
	prepareCarousel : function(id,direction,speed) {
			if (id===undefined) return;
			var _ = _R[id].carousel;
		_.slidepositions = _.slidepositions===undefined ? [] : _.slidepositions;
		_.slideFakePositions = _.slideFakePositions===undefined ? [] : _.slideFakePositions;
		direction = _.lastdirection = dircheck(direction,_.lastdirection);
			_R.setCarouselDefaults(id);
		if (_.slidepositions[0]===undefined) {
			_.slideAnims = [];
				_R.organiseCarousel(id,"right",true,false,false);
				_.focused = 0;
				_.keepFocusedFirst = true;
			}
		_.slide_offset = _.slide_offset===undefined || !_R.isNumeric(_.slide_offset) ? 0 : _.slide_offset;
			_.swipeTo = (_.slide_offset + getActiveCarouselOffset(id));
			_.swipeToDistance = Math.abs(_.slide_offset) + Math.abs(getActiveCarouselOffset(id));

		//if (_.focusedAfterAnimation === _.focused) return;
		if (_.swipeTo===undefined || !_R.isNumeric(_.swipeTo)) {
				_R.swipeAnimate({id:id,to:0,direction:direction,speed:0});
		} else
		if (speed!==undefined)
			_R.swipeAnimate({id:id,to:_.swipeTo, distance: _.swipeToDistance, direction:direction,fix:true,speed:speed});
		else
				_R.swipeAnimate({id:id,to:_.swipeTo, distance: _.swipeToDistance, direction:direction,fix:true});
		},


		// MOVE FORWARDS/BACKWARDS DEPENDING ON THE OFFSET TO GET CAROUSEL IN EVAL POSITION AGAIN
	carouselToEvalPosition : function(id,direction,precalculate) {
		var _ = _R[id].carousel;
		if (_.justify) {
				_.focused = _.focused===undefined ? 0 : _.focused;
			_.slidepositions[_.focused] = _.slidepositions[_.focused]===undefined ? 0 : _.slidepositions[_.focused];
			_.slide_offset_target =  getOffset(id,_.focused);
			} else {
			direction = _.lastdirection = dircheck(direction,_.lastdirection);
				var bb = _.horizontal_align==="center" ? ((_.wrapwidth/2-_.slide_width/2) - _.slide_offset) / _.slide_width : (0 - _.slide_offset) / _.slide_width,
				fi = bb%_R[id].slideamount,
				cm = fi - Math.floor(fi),
					mc = -1 * (Math.ceil(fi) - fi),
					mf = -1 * (Math.floor(fi) - fi),
				px = cm*_.slide_width,
				rule = px>=20 && direction==="left" ? 1 :
						   px>=(_.slide_width-20) && direction==="right" ? 2 :
						   px<20 && direction==="left" ? 3 :
					   px<(_.slide_width-20) && direction==="right" ? 4 : 5,
				calc =rule===1 || rule===2 ?  mc : rule===3 || rule===4 ? mf : 0;
			_.slide_offset_target = (!_.infinity ?  fi<0 ? fi : bb>_R[id].slideamount-1 ? bb-(_R[id].slideamount-1) : calc : calc) * _.slide_width;
			}_
			if (_.slide_offset_target!==_.slide_offset_targetCACHE && precalculate!==true) {
				 if (Math.abs(_.slide_offset_target) !==0) _R.animateCarousel(id,direction,true); else _R.organiseCarousel(id,direction);

			_.slide_offset_targetCACHE= _.slide_offset_target;
		}
			return _.slide_offset_target;
		},

	loadVisibleCarouselItems : function(id,forceload) {
		var ar =[];
		_R[id].carousel.focused = parseInt(_R[id].carousel.focused,0);
		_R[id].carousel.focused = _R.isNumeric(_R[id].carousel.focused) ? _R[id].carousel.focused : 0;
			for (var i=0;i<Math.ceil(_R[id].carousel.maxVisibleItems/2);i++) {
				var n = _R[id].carousel.horizontal_align==="right" ? _R[id].carousel.focused-i : _R[id].carousel.focused + i,
				b = _R[id].carousel.horizontal_align==="center" ? _R[id].carousel.focused-i : _R[id].carousel.horizontal_align==="left" ? _R[id].carousel.maxVisibleItems + n - 1: n - _R[id].carousel.maxVisibleItems+1;

				n = n>=_R[id].slideamount ? 0 + (n-_R[id].slideamount) : n;
			b = b>=_R[id].slideamount ? 0 + (b-_R[id].slideamount) : b;
			n = n<0 ? _R[id].slideamount +n : n;
				b = b<0 ? _R[id].slideamount +b : b;

				ar.push(_R[id].slides[n]);
			if (n!==b) ar.push(_R[id].slides[b])
			}


		if (forceload) {
				_R.loadImages(ar,id,1);
				_R.waitForCurrentImages(ar,id);
			}
			return ar;
		},

		// ORGANISE THE CAROUSEL ELEMENTS IN POSITION AND TRANSFORMS
		organiseCarousel : function(id,direction,setmaind,unli,noanim) {

			var session = Math.round(Math.random()*100000),
			_ = _R[id].carousel,
				ha = _.horizontal_align==="center" ? 2 : 1,
				mc = Math.ceil(_.maxVisibleItems/ha),
			limR = _.horizontal_align==="center" ? _.wrapwidth/2 + _.maxwidth/2 : _.maxwidth-_.slide_width,
			limL = _.horizontal_align==="center" ? _.wrapwidth/2 - _.maxwidth/2 : 0-_.slide_width,
				cpos=0,pos=0,oldpos=0;
		_.ocfirsttun=true;

			// REMOVE CAROUSEl, DETACH CAROUSEL  - Remove it due high costs and a link issues 6.2.91
			//if (_R[id].noDetach!==true && _R[id].slideHasIframe!==true && _R[id].fullScreenMode!==true) _R[id].carousel.wrap.detach();

		//direction = direction===undefined ? _.slide_offset_target<0 ? "right" : "left" : direction;
		// Make Sure things not moved to early into wrong position
			// Since Version 6.1.7
			direction = _.slide_offset<_.cached_slide_offset ? "left" : "right";
			_.cached_slide_offset = _.slide_offset;

			if (_.justify!==true && _.horizontal_align==="center") {
			var om = (_.windhalf-_.wrapoffset)*2 +_.slide_width;

			if (om>=_.maxwidth) {
					if (direction==="left") {
						limR = _.windhalf*2;
						limL = 0-(_.slide_width - (om - _.maxwidth));
					}
					if (direction==="right") {
						limR = (_.windhalf*2) - (om - _.maxwidth);
						limL = 0-_.slide_width;
				}
				}
			}

			var smallest = _.windhalf*2,
				biggest = 0,
				smindex = -1,
				biindex = -1;


			for (var i=0;i<_.len;i++) {
			if (_.justify===true) {
					cpos+= i>0 ? _.slide_widths[i-1] +_.space : _.slide_offset;
				if (_.wrapwidth>=_.maxwidth && _.horizontal_align!=="center") _.slideFakePositions[i] = cpos - _.slide_offset;
					limL = 0 - _.slide_widths[i];
					limR = _.maxwidth-_.slide_widths[i];
					_.inneroffset = 0;
				} else {
					cpos = (i * _.slide_width) + _.slide_offset;
					if (_.wrapwidth>=_.maxwidth && _.horizontal_align==="left") _.slideFakePositions[i] = (i * _.slide_width);
					if (_.wrapwidth>=_.maxwidth && _.horizontal_align==="right") _.slideFakePositions[i] = _.wrapwidth - ((i+1) * _.slide_width);
				}
				pos = cpos;
			oldpos = pos;

			if (_.infinity) pos = pos>=limR-_.inneroffset  ? pos - _.maxwidth : pos<=limL-_.inneroffset  ? pos + _.maxwidth : pos;
				if (smallest>pos) {
					smallest = pos;
					smindex = i;
				}
				if (biggest<pos) {
					biggest = pos;
					biindex = i;
			}
				_.slidepositions[i] = oldpos>_.maxwidth+limR ? pos-_.maxwidth : oldpos<limL - _.maxwidth ? pos+_.maxwidth : pos;

		}

			// DOUBLE CHECK
			if (_.infinity && smallest>0 && biggest>_.wrapwidth)_.slidepositions[biindex] -= _.maxwidth ;



			var maxd = 999,
			scaleoffset = 0,
			minl = _R[id].module.width,
				newfound = false,
				lastfoundposition = _.horizontal_align==="right" ? 0 : _.wrapwidth;

			// SECOND RUN FOR NEGATIVE ADJUSTMENETS
			if (_R[id].slides)
		for (var i=0;i<_R[id].slides.length;i++) {
			var	pos = _.slidepositions[i],
				tr = { 	left : pos + _.inneroffset,
							width : _.justify===true ? _.slide_widths[i] : _.slide_width,
						x : 0
						},
					d=0;
				if (_.slideAnims[i]===undefined) {
					//tr.transformPerspective = 1200;
					tr.transformOrigin = "50% "+_.vertical_align;
					tr.scale = 1;
					// TRANSFORM STYLE
				tr.force3D = true;
				tr.transformStyle = _R[id].parallax.type!="3D" && _R[id].parallax.type!="3d" ? "flat" : "preserve-3d";
				}

				if (_.justify) {
						tr.autoAlpha = 1;
						if (_.wrapwidth>=_.maxwidth && _.horizontal_align!=="center") {
							//
						} else {
							if (_.horizontal_align==="center" &&  _.slidepositions[i]<_.windhalf && _.slidepositions[i]+_.slide_widths[i]>_.windhalf) _.focused = i;
							else
							if (_.horizontal_align==="left" && _.slidepositions[i]>=-25 && _.slidepositions[i]<_.windhalf && (!newfound || _.slidepositions[i]<lastfoundposition)) { _.focused = i; newfound = true;lastfoundposition=_.slidepositions[i]}
							else
						if (_.horizontal_align==="right" && _.slidepositions[i]+_.slide_widths[i]<=_.wrapwidth+25 && ((_.slide_widths[i]<_.windhalf && _.slidepositions[i]>_.windhalf) || (_.slidepositions[i]>_.wrapwidth-_.slide_widths[i])) &&  (!newfound || _.slidepositions[i]>lastfoundposition)) { _.focused = i; newfound = true;lastfoundposition=_.slidepositions[i]}

						_.focused = _.focused>=_.len ? _.infinity ? 0 : _.len-1 : _.focused<0 ? _.infinity ? _.len-1 : 0 : _.focused;
						}
			} else{

					// CHCECK DISTANCES FROM THE CURRENT FAKE FOCUS POSITION
				d =  _.horizontal_align==="center" ? (Math.abs(_.wrapwidth/2) - (tr.left+_.slide_width/2))/_.slide_width : (_.inneroffset - tr.left)/_.slide_width;

				if ((Math.abs(d)<maxd) || d===0) {
					maxd = Math.abs(d);
					_.focused = i;
					}

				// SET SCALE DOWNS
					if (_.minScale!==undefined && _.minScale >0) {
					if (_.vary_scale)
						tr.scale = 1-Math.abs((((1-_.minScale)/mc)*d));
						else
							tr.scale = d>=1 || d<=-1 ? _.minScale : _.minScale + ((1-_.minScale)*(1-Math.abs(d)));
					 scaleoffset = d * (tr.width - tr.width*tr.scale)/2;
					}


				// SET VISIBILITY OF ELEMENT
				if (_.fadeout) 	{
					if (_.vary_fade) tr.autoAlpha = 1-Math.abs(((_.maxOpacity/mc)*d));
					else  tr.autoAlpha = d>=1 || d<=-1 ?  _.maxOpacity : _.maxOpacity + ((1-_.maxOpacity)*(1-Math.abs(d)));
				}

					var opi = Math.ceil((_.maxVisibleItems/ha)) - Math.abs(d);
					tr.autoAlpha = tr.autoAlpha===undefined ? 1 : tr.autoAlpha;
					tr.autoAlpha= Math.max(0,Math.min(opi,tr.autoAlpha));



				// ROTATION FUNCTIONS
				if (_.maxRotation!==undefined && Math.abs(_.maxRotation)!=0)	{
						if (_.vary_rotation) {
						tr.rotationY = Math.abs(_.maxRotation) - Math.abs((1-Math.abs(((1/mc)*d))) * _.maxRotation);
							tr.autoAlpha = Math.abs(tr.rotationY)>90 ? 0 : tr.autoAlpha;
						} else {
						tr.rotationY = d>=1 || d<=-1 ?  _.maxRotation : Math.abs(d)*_.maxRotation;
					}
						tr.rotationY = d<0 ? tr.rotationY*-1 : tr.rotationY;
					if (_R.isSafari11()) tr.z = d!==0 ? 0-Math.abs(tr.rotationY) : 0;
					}
					// SET SPACES BETWEEN ELEMENTS
				tr.x = Math.floor(((-1*_.space) * d * (_.offsetScale ? tr.scale : 1)));
					if (tr.scale!==undefined) tr.x = tr.x + scaleoffset;
				}

				// AVOID REFLOWS, SET ONLY X IF POSSIBLE

				tr.x += (_.wrapwidth>=_.maxwidth && (_.horizontal_align==="left" || _.horizontal_align==="right"))  ? _.slideFakePositions[i] : Math.floor(tr.left);
				delete tr.left;



			// ZINDEX ADJUSTEMENT
				tr.zIndex = _.justify ? 95 : Math.round(100-Math.abs(d*5));

				if (noanim!==true) {
					if (_.slideAnims[i]!==undefined) {
						if (tr.width===_.slideAnims[i].width) delete tr.width;
						if (tr.x===_.slideAnims[i].x) delete tr.x;
						if (tr.autoAlpha===_.slideAnims[i].autoAlpha) delete tr.autoAlpha;
						if (tr.scale===_.slideAnims[i].scale) delete tr.scale;
						if (tr.zIndex===_.slideAnims[i].zIndex) delete tr.zIndex;
						if (tr.rotationY===_.slideAnims[i].rotationY) delete tr.rotationY;
				}
					tpGS.gsap.set(_R[id].slides[i],tr);

					// ADJUST TRANSFORMATION OF SLIDE
					_.slideAnims[i] = jQuery.extend(true,_.slideAnims[i],tr);
				}
		};

			// Remove it due high costs and a link issues 6.2.91
			// if (_R[id].noDetach!==true && _R[id].slideHasIframe!==true && _R[id].fullScreenMode!==true) _R[id].c[0].appendChild(_R[id].carousel.wrap[0]);

		_R.loadVisibleCarouselItems(id,true);

		if (unli && noanim!==true) {

				_.focused = _.focused===undefined ? 0 : _.focused;
			_.oldfocused = _.oldfocused===undefined ? 0 : _.oldfocused;
			_R[id].pr_next_key = _.focused;

			if (_.focused!==_.oldfocused) {

				if (_.oldfocused !==undefined) _R.removeTheLayers(jQuery(_R[id].slides[_.oldfocused]),id);
				_R.animateTheLayers({slide:_.focused, id:id, mode: "start"});
				_R.animateTheLayers({slide:'individual', id:id, mode:(!_R[id].carousel.allLayersStarted ? "start" : "rebuild")});
				for (var nbgi in _R[id].sbgs) {

					if (!_R[id].sbgs.hasOwnProperty(nbgi) || _R[id].sbgs[nbgi].bgvid===undefined || _R[id].sbgs[nbgi].bgvid.length===0) continue;
					if (""+_R[id].sbgs[nbgi].skeyindex===""+_.focused)
						_R.playBGVideo(id,_R.gA(_R[id].pr_next_slide[0],"key"));
					else
						_R.stopBGVideo(id,_R[id].sbgs[nbgi].key);
				}
				}
			_.oldfocused = _.focused;

				_R[id].c.trigger("revolution.nextslide.waiting");
		}
		},

	swipeAnimate : function(obj) {
			var _ = _R[obj.id].carousel,animobj = {from:_.slide_offset, to: obj.to},speed=obj.speed===undefined ? 0.5 : obj.speed;
			_.distance = obj.distance !== undefined ? obj.distance : obj.to;
		if (_.positionanim!==undefined) _.positionanim.pause();
		if (obj.fix) {
			if (_.snap!==false) {
					var temp = _.slide_offset,
						prefocused = obj.phase==="end" ? _.focusedBeforeSwipe : _.focused;
				_.slide_offset = obj.to;
				_R.organiseCarousel(obj.id,obj.direction,true,false,false);
				if (Math.abs(_.swipeDistance)>40 && prefocused==_.focused) {
						_.focused = obj.direction ==="right" ? _.focused-1 : _.focused+1;
						_.focused = _.focused>=_.len ? _.infinity ? 0 : _.len-1 : _.focused<0 ? _.infinity ? _.len-1 : 0 : _.focused;
					}
					animobj.to += _R.carouselToEvalPosition(obj.id,obj.direction,true);
				_.slide_offset = temp;
				_R.organiseCarousel(obj.id,obj.direction,true,false,false);
					if (_.keepFocusedFirst) {
						_.keepFocusedFirst = false;
						_.focused = 0;
				}
			} else
			if ( _.infinity!==true) {
					if (animobj.to>0) animobj.to = 0;
					if (animobj.to<_.wrapwidth-_.maxwidth) animobj.to = _.wrapwidth-_.maxwidth;
			} else {
					if (obj.phase==="end") _.dragModeJustEnded = true;
				else if (_.dragModeJustEnded!==true) animobj.to += _R.carouselToEvalPosition(obj.id,obj.direction,true);
					else _.dragModeJustEnded = false;
				}
				speed = (_.speed/1000) * circ(Math.abs(Math.abs(animobj.from) - Math.abs(_.distance)) / _.wrapwidth);
			if (speed!==0 && speed<0.1 && Math.abs(animobj.to)>25) speed = 0.3;
		}
		_.swipeDistance = 0;
			speed = _.firstSwipedDone!==true ? 0 : speed;
			_.firstSwipedDone=true;

			_.positionanim = tpGS.gsap.to(animobj,speed,{from:animobj.to,
			onUpdate:function() {
				_.slide_offset = animobj.from%_.maxwidth;
					_R.organiseCarousel(obj.id,obj.direction,obj.fix!==true,obj.fix!==true);
					_.slide_offset = animobj.from;
			},
				onComplete:function() {
				_.slide_offset = animobj.from%_.maxwidth;
					if (_R[obj.id].sliderType==="carousel" && !_.fadein) {
						tpGS.gsap.to(_R[obj.id].canvas,1,{scale:1,opacity:1});
					_.fadein=true;
				}
					_.lastNotSimplifedSlideOffset = _.slide_offset;
				_.justDragged = false;

				if (obj.fix) {
						_.focusedAfterAnimation = _.focused;
					if (obj.newSlide && _.focusedBeforeSwipe !== _.focused) _R.callingNewSlide(obj.id,jQuery(_R[obj.id].slides[_.focused]).data('key'),true);
						_R.organiseCarousel(obj.id,obj.direction,true,true);
						/*
							For the Particles AddOn, we need to get the "jQuery(slide).offset()" for particle mousemove events
							as the Particles AddOn listens for this "carouselchange" event so we can guarantee that the offset() values are accurate
						*/
						_R[obj.id].c.trigger('revolution.slide.carouselchange', {
							slider:obj.id,
							slideIndex : parseInt(_R[obj.id].pr_active_key,0)+1,
						slideLIIndex : _R[obj.id].pr_active_key,
							slide : _R[obj.id].pr_next_slide,
							currentslide : _R[obj.id].pr_next_slide,
							prevSlideIndex : _R[obj.id].pr_lastshown_key!==undefined ? parseInt(_R[obj.id].pr_lastshown_key,0)+1 : false,
							prevSlideLIIndex : _R[obj.id].pr_lastshown_key!==undefined ? parseInt(_R[obj.id].pr_lastshown_key,0) : false,
							prevSlide : _R[obj.id].pr_lastshown_key!==undefined ? _R[obj.id].slides[_R[obj.id].pr_lastshown_key] : false
						});
					}
			//
			},
				ease:obj.easing ? obj.easing : _.easing });

	},
		defineCarouselElements : function(id) {
			var _ = _R[id].carousel;
			_.infbackup = _.infinity;
			_.maxVisiblebackup = _.maxVisibleItems;

			// SET DEFAULT OFFSETS TO 0
			_.slide_offset = "none";
		_.slide_offset = 0;
			_.cached_slide_offset = 0;

			// SET UL REFERENCE
			_.wrap = jQuery(_R[id].canvas[0].parentNode);


			// CHANGE PERSPECTIVE IF PARALLAX 3D SET
		if (_.maxRotation!==0) if (_R[id].parallax.type==="3D" || _R[id].parallax.type==="3d") tpGS.gsap.set(_.wrap,{perspective:"1600px",transformStyle:"preserve-3d"});
		},
	setCarouselDefaults : function(id,quickmode) {

		var _=_R[id].carousel;

			// DEFAULT LI WIDTH SHOULD HAVE THE SAME WIDTH OF TH id WIDTH
		_.slide_width = _.stretch!==true ? _R[id].gridwidth[_R[id].level]*(_R[id].CM.w===0 ? 1 : _R[id].CM.w) : _R[id].canv.width;
		_.slide_height = _.stretch!==true ? _R[id].infullscreenmode ? _R.getWinH(id) - _R.getFullscreenOffsets(id) : _R[id].gridheight[_R[id].level]*(_R[id].CM.w===0 ? 1 : _R[id].CM.w) : _R[id].canv.height;
			_.ratio = _.slide_width / _.slide_height;

			// CALCULATE CAROUSEL WIDTH
			_.len = _R[id].slides.length;
			_.maxwidth = _R[id].slideamount*_.slide_width;
			if (_.justify!=true && _.maxVisiblebackup>_.len) _.maxVisibleItems = (_.len%2) ? _.len : _.len+1;


		// SET MAXIMUM CAROUSEL WARPPER WIDTH (SHOULD BE AN ODD NUMBER)
		_.wrapwidth = (_.maxVisibleItems * _.slide_width) + ((_.maxVisibleItems - 1) * _.space);
		_.wrapwidth = _R[id].sliderLayout!="auto" ? _.wrapwidth>_R[id].canv.width ? _R[id].canv.width : _.wrapwidth : _.wrapwidth>_R[id].module.width ?_R[id].module.width : _.wrapwidth;

		if (_.justify===true) {
			_.slide_height = _R[id].sliderLayout==="fullscreen" ? _R[id].module.height : _R[id].gridheight[_R[id].level];
			_.slide_widths = [];
				_.slide_widthsCache = 	_.slide_widthsCache===undefined ? [] :_.slide_widthsCache;
				_.maxwidth = 0;
				for (var i=0;i<_.len;i++) {
					if (!_R[id].slides.hasOwnProperty(i)) continue;
					var ir  = _R.gA(_R[id].slides[i],'iratio');
				ir = ir===undefined || ir===0 || ir===null ? _.ratio : ir;
				 _.slide_widths[i] = Math.round(_.slide_height * ir);
					if (_.justifyMaxWidth!==false) _.slide_widths[i] = Math.min(_.wrapwidth,_.slide_widths[i]);
				if (_.slide_widths[i]!==_.slide_widthsCache[i]) {
						_.slide_widthsCache[i] = _.slide_widths[i];
				 	if (quickmode!==true) tpGS.gsap.set(_R[id].slides[i],{width:_.slide_widths[i]}); // KRIKI TO DO!!
					}
				_.maxwidth += _.slide_widths[i] +_.space;
			}
			}


		// INFINITY MODIFICATIONS
			_.infinity = _.wrapwidth >=_.maxwidth ? false : _.infbackup;

			if (_.quickmode!==true) {
			// SET POSITION OF WRAP CONTAINER
			_.wrapoffset = _.horizontal_align==="center" ? (_R[id].canv.width-_R[id].outNavDims.right - _R[id].outNavDims.left - _.wrapwidth)/2 : 0;
			_.wrapoffset = _R[id].sliderLayout!="auto" && _R[id].outernav ? 0 : _.wrapoffset < _R[id].outNavDims.left ? _R[id].outNavDims.left : _.wrapoffset;

				var ovf = ((_R[id].parallax.type=="3D" || _R[id].parallax.type=="3d")) ? "visible" : "hidden",
					obj = _.horizontal_align==="right" ? {left:"auto",right:_.wrapoffset+"px", width:_.wrapwidth, overflow:ovf} : _.horizontal_align==="left"  || _.wrapwidth<_R.winW? {right:"auto",left:_.wrapoffset+"px", width:_.wrapwidth, overflow:ovf} : {right:"auto",left:"auto", width:"100%", overflow:ovf}
			//Fixed an issue where fullwidth sliders did no go fullwdith if it is not set to 100% width.
			if (_.cacheWrapObj===undefined || obj.left!==_.cacheWrapObj.left || obj.right!==_.cacheWrapObj.right || obj.width!==_.cacheWrapObj.width) {
				window.requestAnimationFrame(function() {
					tpGS.gsap.set(_.wrap,obj);
					// reset left property on canvas because when carousel is created,
					// _R[id].navOutterOffsets.left value is still incorrect
					if(_R[id].carousel.wrapoffset > 0) tpGS.gsap.set(_R[id].canvas, {left: 0});
					});
					_.cacheWrapObj = jQuery.extend(true,{},obj);
				}

				// INNER OFFSET FOR RTL
				_.inneroffset = _.horizontal_align==="right" ? _.wrapwidth - _.slide_width : 0;

				// THE SCREEN WIDTH/2
			_.windhalf = _R[id].sliderLayout==="auto" ? _R[id].module.width/2 : _R.winW/2;
			}

		}
	});

	/**************************************************
		-	CAROUSEL FUNCTIONS   -
	***************************************************/

	var getOffset = function(id,ix) {
	var _ = _R[id].carousel;
	return _.horizontal_align==="center" ?  (_.windhalf - _.slide_widths[ix]/2) - _.slidepositions[ix] :  _.horizontal_align==="left" ? 0-_.slidepositions[ix] : (_.wrapwidth-_.slide_widths[ix]) - _.slidepositions[ix];
	},

	circ = function(p) {return p<1 ? Math.sqrt(1 - (p = p - 1) * p) : Math.sqrt(p);},


	// DIRECTION CHECK
	dircheck = function(d,b) {return d===null || jQuery.isEmptyObject(d) ? b : d === undefined ?  "right" : d;},
	breduc = function(a,m) {return Math.abs(a)>Math.abs(m) ? a>0 ? a - Math.abs(Math.floor(a/(m))*(m)) : a + Math.abs(Math.floor(a/(m))*(m)) : a;},

	// CAROUSEL INFINITY MODE, DOWN OR UP ANIMATION
	getBestDirection = function(a,b,max) {
		var dira = b-a,
            dirb = (b-max) - a;
		dira = breduc(dira,max);
        dirb = breduc(dirb,max);
		return Math.abs(dira)>Math.abs(dirb) ? dirb : dira;
	},

	// GET OFFSETS BEFORE ANIMATION
	getActiveCarouselOffset = function(id) {

		var ret = 0,
			_ = _R[id].carousel;

		if (_.positionanim!==undefined) _.positionanim.pause();

		if (_.justify) {

			if (_.horizontal_align==="center") ret =  (_.windhalf - _.slide_widths[_.focused]/2) - _.slidepositions[_.focused];
			else if (_.horizontal_align==="left") ret =  0 - _.slidepositions[_.focused];
			else if (_.horizontal_align==="right") ret =  (_.wrapwidth - _.slide_widths[_.focused]) - _.slidepositions[_.focused];
			ret = ret>_.maxwidth/2 ? _.maxwidth-ret : ret < 0-_.maxwidth/2 ? ret + _.maxwidth : ret;
        } else {
            var ci = _R[id].pr_processing_key>=0 ? _R[id].pr_processing_key : _R[id].pr_active_key>=0 ? _R[id].pr_active_key : 0,
                fi = (_.horizontal_align==="center" ? ((_.wrapwidth/2-_.slide_width/2) - _.slide_offset) / _.slide_width : (0 - _.slide_offset) / _.slide_width)%_R[id].slideamount;
            ret = (!_.infinity ? fi-ci : -getBestDirection(fi,ci,_R[id].slideamount)) * _.slide_width;
        }

		if (_.snap===false && _.justDragged) ret = 0;
        _.justDragged = false;

        return ret;
    };

    //Support Defer and Async and Footer Loads
    window.RS_MODULES = window.RS_MODULES || {};
    window.RS_MODULES.carousel = {loaded:true, version:version};
    if (window.RS_MODULES.checkMinimal) window.RS_MODULES.checkMinimal();

})(jQuery);