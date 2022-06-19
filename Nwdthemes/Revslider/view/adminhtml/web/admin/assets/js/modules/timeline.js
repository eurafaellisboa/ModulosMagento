/*!
 * REVOLUTION 6.0.0 EDITOR TIMELINE JS
 * @version: 1.0 (01.07.2019)
 * @author ThemePunch
*/

// "use strict";
/**********************************
	-	REVBUILDER timeline	-
********************************/
(function() {



	var splitTypes = ["chars","words","lines"],animatedLayers = [],idleMode,tlr,mainTimeLine,frameInfo,slideMaxTime,frameMagnify,cLayer,PN,keyframecache;

	RVS.F.initTimeLineModules = function() {
		initLocalListeners();
		RVS.TL.c.layertime = jQuery('#layer_simulator_time');
		RVS.TL.c.layerprogress = jQuery('#layer_animation_progressarrow');
	};



	RVS.F.animateSlide = function(nextsh,actsh,comingtransition,MS) {

		//return interSlideAnimation(nextsh,actsh,comingtransition,MS);
	};


	/*
	TIME LINE FUNCTIONS
	*/
	RVS.F.buildMainTimeLine = function(obj) {
		RVS.TL[RVS.S.slideId].main = new tpGS.TimelineMax({paused:true});

		// Create TimeLine for BG Animation if needed
		RVS.TL[RVS.S.slideId] = RVS.TL[RVS.S.slideId]===undefined ? {} : RVS.TL[RVS.S.slideId];
		RVS.TL[RVS.S.slideId].slide = RVS.TL[RVS.S.slideId].slide===undefined ? new tpGS.gsap.timeline() : RVS.TL[RVS.S.slideId].slide;

		//RVS.F.buildSlideAnimation();
		RVS.TL[RVS.S.slideId].main.add(RVS.TL[RVS.S.slideId].slide,0);
		RVS.TL[RVS.S.slideId].main.add("end",RVS.F.getSlideLength()/100);
		window.tpfake = 1;
		RVS.TL[RVS.S.slideId].main.add(new tpGS.gsap.set(window,{tpfake:0}),"end");

		if (RVS.SLIDER[RVS.S.slideId].slide.panzoom.set && RVS.TL[RVS.S.slideId].panzoom!==undefined) RVS.TL[RVS.S.slideId].main.add(RVS.TL[RVS.S.slideId].panzoom,0);

		if (obj && (obj.time || obj.progress)) RVS.F.updateTimeLine({timeline:"main",state:"time",time:obj.tim});


		RVS.TL[RVS.S.slideId].main.eventCallback("onUpdate", function() {
			RVS.F.updateCurTime({pos:true, cont:true, left:(this._time*100),refreshMainTimeLine:false, caller:"buildMainTimeLine"});
		});

		RVS.TL[RVS.S.slideId].main.eventCallback('onComplete',function() {
			if (RVS.TL.timelineStartedFromPlayStop) {
				RVS.TL.timelineStartedFromPlayStop = false;
				RVS.TL.cache.main = 0;
				RVS.F.updateCurTime({pos:true, cont:true, force:true, left:0,refreshMainTimeLine:true, caller:"GoToIdle"});
			}
		});
	};

	RVS.F.resetSlideTL = function() {
		if (RVS.TL[RVS.S.slideId].slide!==undefined) RVS.TL[RVS.S.slideId].slide.kill();
		RVS.TL[RVS.S.slideId].slide = new tpGS.gsap.timeline();
	}

	RVS.F.updateFramesZIndexes = function(_) {
		var z = 1000;
		if (RVS.L[_.layerid].timeline.frameOrder===undefined) RVS.F.getFrameOrder({layerid:_.layerid});
		for (var oi in RVS.L[_.layerid].timeline.frameOrder) {
			if(!RVS.L[_.layerid].timeline.frameOrder.hasOwnProperty(oi)) continue;
			if (RVS.L[_.layerid].timeline.frameOrder[oi].id ==="frame_0") continue;
			var el = document.getElementById(RVS.S.slideId+'_'+_.layerid+'_'+RVS.L[_.layerid].timeline.frameOrder[oi].id);
			if (el) tpGS.gsap.set(el,{zIndex:z});
			z--;
		}
	};

	/*
	FRAME HANDLINGS
	*/
	RVS.F.addFrame = function(obj) {

		var __ = {  wrap:obj.container,
					bg:jQuery(obj.container.getElementsByTagName("framebg")[0]),
					frame:RVS.F.cE({t:'framewrap',id:RVS.S.slideId+'_'+obj.layerid+'_'+obj.frame , cN:"frame_"+obj.frame, ds:{layertype:obj.layertype,layerid:obj.layerid,frame:obj.frame}}),
					framedelay:RVS.F.cE({t:'framedelay'}),
					info:RVS.F.cE({t:'frameinfo'}),
					sloop:RVS.F.cE({t:'startloop'}),
					eloop:RVS.F.cE({t:'endloop'}),
					marker:RVS.F.cE({t:'marker'}),
					obj : obj
				};

		if (obj.frame === "frame_1") {
			__.frame.appendChild(RVS.F.cE({t:'ffbefore',ds:{frame:"frame_0"}}));
			__.frame.appendChild(RVS.F.cE({t:'ffafter',ds:{frame:"frame_1"}}));
		}
		__.framedelay.textContent = "2500";
		__.sloop.appendChild(RVS.F.cI({c:"chevron_right"}));
		__.eloop.appendChild(RVS.F.cI({c:"chevron_left"}));

		__.frame.appendChild(__.framedelay);
		__.frame.appendChild(__.sloop);
		__.frame.appendChild(__.eloop);
		__.frame.appendChild(__.info);
		__.frame.appendChild(__.marker);

		obj.container.appendChild(__.frame);

		if (RVS.TL.frameMouseListenerObjects===undefined) {
			RVS.TL.frameMouseListenerObjects={};
			RVS.DOC.on('mouseenter','framewrap',function() {
				if (this.dataset.mlisteners===undefined) {
					this.dataset.mlisteners=true;
					var __ = RVS.TL.frameMouseListenerObjects[this.id];
					__.jframe = jQuery(__.frame);

					if (__.obj.resize!==undefined) {
						__.jframe.resizable({
							handles:__.obj.resize,
							minWidth:5,
							start:__.obj.start,
							stop:__.obj.stopresize,
							resize:__.obj.onresize
						});
					}
					if (__.obj.ondrag!==undefined) {
						__.jframe.draggable({
							axis:"x",
							delay:50,
							start:__.obj.start,
							stop:__.obj.stopdrag,
							drag:__.obj.ondrag
						});

						__.bg.draggable({
							axis:"x",
							delay:50,
							start:__.obj.start,
							stop:__.obj.stopdrag,
							drag:__.obj.ondrag
						});
					}
				}
			});
		}

		RVS.TL.frameMouseListenerObjects[__.frame.id] = __;


		return __;
	};
	/*
	BUILD 1x MAIN SLIDE FRAME (USED FOR ALL SLIDES, NO DIFFERENCES BETWEEN SLIDES, ONLY 1 REFERENCE)
	*/
	RVS.F.buildSlideFrames = function() {
		RVS.TL.fref = RVS.TL.fref===undefined ?
				RVS.F.addFrame({
					container:document.querySelector('#slide_frame_container .frameswrap'),
					frame:"0",
					resize:"e",
					layerid:"",
					start:function(event,ui) {
						mainTimeLine = RVS.TL[RVS.S.slideId] && RVS.TL[RVS.S.slideId].main ? RVS.TL[RVS.S.slideId].main.time() : 0;
						mainTimeLine = mainTimeLine<0.0015 ? "idle" : mainTimeLine;
						frameInfo = ui.element.find('frameinfo');
						slideMaxTime = RVS.F.getSlideLength();
						RVS.TL.inDrag = true;
					},
					onresize:function(event,ui) {
						ui.size.width = ui.size.width<=slideMaxTime ? ui.size.width : slideMaxTime;
						var v = RVS.F.slideAnimWithMoreRowCol() ? (ui.size.width*10)/ (1+1/(RVS.SLIDER[RVS.S.slideId].slide.slideChange.d/10)) : ui.size.width*10;
						RVS.F.updateSliderObj({path:RVS.S.slideId+".slide.slideChange.speed",val:Math.round(v),ignoreBackup:true});
						RVS.F.buildMainTimeLine({time:mainTimeLine});
						RVS.F.redrawSlideBG();
						frameInfo[0].innerHTML = ui.size.width*10;
						document.getElementById('sltrans_duration').value = Math.round(v);
					},
					stopresize:function(event,ui) {
						//CREATE BACKUP
						var v = RVS.F.slideAnimWithMoreRowCol() ? (ui.size.width*10)/ (1+1/(RVS.SLIDER[RVS.S.slideId].slide.slideChange.d/10)) : ui.size.width*10;
						RVS.F.backup({path:RVS.S.slideId+".slide.slideChange.speed", lastkey:"speed", val:Math.round(v), old:ui.originalSize.width*10});
						RVS.F.buildMainTimeLine({time:mainTimeLine});
						RVS.F.timelineDragsStop();
						RVS.F.redrawSlideBG();
						document.getElementById('sltrans_duration').value =  Math.round(v);
						requestAnimationFrame(function() {
							RVS.F.updateSlideFrames();
						});
					}
				}) : RVS.TL.fref;
		requestAnimationFrame(function() {
			RVS.F.updateSlideFrames();
		});
	};

	RVS.F.slideAnimWithMoreRowCol = function() {
		let mcr = RVS.SLIDER[RVS.S.slideId].slide.slideChange.in.row>1 || RVS.SLIDER[RVS.S.slideId].slide.slideChange.in.col>1;
		for (var i in RVS.JHOOKS.slideAnimRowColCheck) {
			if(!RVS.JHOOKS.slideAnimRowColCheck.hasOwnProperty(i)) continue;
			mcr = RVS.JHOOKS.slideAnimRowColCheck[i](mcr);
		}
		return mcr;
	}

	/*
	UPDATE THE SLIDE FRAME SIZES
	*/
	RVS.F.updateSlideFrames = function(obj) {

		obj = obj===undefined ? {} : obj;
		obj.slidedelay = obj.slidedelay===undefined ? RVS.F.getSlideLength() : obj.slidedelay;
		var MS = RVS.SLIDER[RVS.S.slideId].slide.slideChange.speed;
		MS = MS==="default" || MS==="Default" || MS==="Random" ? 1000 : parseFloat(MS,0);
		let mcr = RVS.F.slideAnimWithMoreRowCol();
		var subMS = (mcr ? MS/(RVS.SLIDER[RVS.S.slideId].slide.slideChange.d/10) : 0);

		MS = Math.round(Math.round(MS + subMS)/10);
		RVS.TL.ssubref = RVS.TL.ssubref===undefined ? document.getElementById('slideframespeed_sub') : RVS.TL.ssubref;
		obj.animspeed = obj.animspeed===undefined ? MS : obj.animspeed;
		RVS.TL.ssubref.innerHTML = "(" + obj.animspeed*10 + ")";
		tpGS.gsap.set(RVS.TL.fref.wrap,{width: obj.slidedelay});
		tpGS.gsap.set(RVS.TL.fref.frame,{width: obj.animspeed});
		RVS.TL.fref.info.innerHTML = obj.animspeed*10;
	};


	/*
	ADD ALL FRAMES IN ONE GO FROM ONE LAYER
	*/
	RVS.F.addLayerFrames = function(layer,layerelement) {
		RVS.TL[RVS.S.slideId].layers = RVS.TL[RVS.S.slideId].layers===undefined ? {} : RVS.TL[RVS.S.slideId].layers;
		RVS.TL[RVS.S.slideId].layers[layer.uid] = RVS.TL[RVS.S.slideId].layers[layer.uid] ===undefined ? {} : RVS.TL[RVS.S.slideId].layers[layer.uid];
		var slideLength = RVS.F.getSlideLength()*10;
		if (layer.timeline.frameOrder===undefined) RVS.F.getFrameOrder({layerid:layer.uid});
		for (var oi in layer.timeline.frameOrder) {
			if(!layer.timeline.frameOrder.hasOwnProperty(oi)) continue;
			var findex = layer.timeline.frameOrder[oi].id;
			if (findex ==="frame_0") continue;
			layer.timeline.frames.frame_999.timeline.start = layer.timeline.frames.frame_999.timeline.start===0 || layer.timeline.frames.frame_999.timeline.start> slideLength ? slideLength : layer.timeline.frames.frame_999.timeline.start;
			RVS.F.addLayerFrameOnDemand(layer,layerelement,findex);
		}
		RVS.F.updateFramesZIndexes({layerid:layer.uid});
	};

	RVS.F.addLayerFrameOnDemand = function(layer,layerelement,frame) {

		RVS.F.addLayerFrame({	frame:layer.timeline.frames[frame],
								frameindex:frame,
								layerid:layer.uid,
								layertype:layer.type,
								framecontainer:RVS.TL[RVS.S.slideId].layers[layer.uid],
								wrap:layerelement.querySelector('.frameswrap')
							});
		RVS.F.updateLayerFrame({layerid:layer.uid, frame:frame});
	};

	/*
	ADD LAYER FRAME AND ITS LISTENERS
	*/
	RVS.F.getLayerAliasAndFrame = function(_) {
		var r = _.frame.replace("_"," ").replace("f","F");
		r = RVS.F.sanitize_input(RVS.L[_.layerid].alias)+" "+r;
		return r;
	};

	/*
	UPDATE THE CHILDREN POSITIONS ON THE TIMELINE
	*/
	RVS.F.setChildrenTimelines = function(_) {
		if (_.childLayers===undefined) return;

		for (var li in _.childLayers) {
			if(!_.childLayers.hasOwnProperty(li)) continue;
			var cl = _.childLayers[li];

			for (var oi in RVS.L[li].timeline.frameOrder) if (RVS.L[li].timeline.frameOrder.hasOwnProperty(oi)) {  // Changed Frame Order based Update instead of simple Update
				//for (var fi in cl.frames) {
				fi = RVS.L[li].timeline.frameOrder[oi];
				if(!cl.frames.hasOwnProperty(fi)) continue;
				if (fi==="frame_0") continue;
				var limits = RVS.F.getPrevNextFrame({layerid:li, frame:fi}),
					nstart = cl.frames[fi]/10 -_.difference;

				if (limits.prev.end/10>=nstart)
					nstart = limits.prev.end/10;
				else
				if (limits.next.start/10<=nstart+limits.cur.framelength/10)
					nstart = limits.next.start/10 - limits.cur.framelength/10;

				if (nstart>slideMaxTime)
					nstart = slideMaxTime;

				if (cl.endWithSlide===undefined)
					RVS.SLIDER[RVS.S.slideId].layers[li].timeline.frames[fi].timeline.start = nstart*10;
				else
				if (cl.endWithSlide)
					RVS.SLIDER[RVS.S.slideId].layers[li].timeline.frames[fi].timeline.start = nstart*10;
				else
				if (cl.frames[fi]>slideMaxTime*10 && fi==="frame_999" && !cl.endWithSlide) {
					RVS.SLIDER[RVS.S.slideId].layers[li].timeline.frames[fi].timeline.start = Math.max(limits.prev.end,slideMaxTime*10);

				}

				RVS.F.updateLayerFrame({layerid:li, frame:fi,maxtime:slideMaxTime});
			}
		}
	};

	/*
	UPDATE THE CHILDREN POSITIONS ON THE TIMELINE
	*/
	RVS.F.moveChildrenTimelines = function(_) {
		if (_.childLayers===undefined) return;
		for (var li in _.childLayers) {
			if(!_.childLayers.hasOwnProperty(li)) continue;
			if (frameMagnify == 1 && li!==cLayer.layerid) continue;
			var cl = _.childLayers[li],
				flen = cl.forder.length-1;
			for (var i in cl.forder) {
				if(!cl.forder.hasOwnProperty(i)) continue;
				var fi = _.order===-1 ? cl.forder[flen-i] : cl.forder[i];
				if (fi==="frame_0") continue;
				var limits = RVS.F.getPrevNextFrame({layerid:li, frame:fi}),
					nstart = cl.frames[fi]/10 -_.difference;

				if (limits.prev.frameid===cLayer.frame && _.thend && limits.prev) nstart = nstart < _.thend ? _.thend : nstart;
				if (0>nstart) nstart = 0;
				if (limits.next.start/10<nstart+limits.cur.framelength/10) nstart = limits.next.start/10 - limits.cur.framelength/10;
				if (nstart>slideMaxTime) nstart = slideMaxTime;
				if (cl.endWithSlide===undefined || cl.endWithSlide) RVS.SLIDER[RVS.S.slideId].layers[li].timeline.frames[fi].timeline.start = nstart*10;
				if (cl.frames[fi]>slideMaxTime*10 && fi==="frame_999" && !cl.endWithSlide) RVS.SLIDER[RVS.S.slideId].layers[li].timeline.frames[fi].timeline.start = Math.max(limits.prev.end,slideMaxTime*10);

				RVS.F.updateLayerFrame({layerid:li, frame:fi,maxtime:slideMaxTime});
			}
		}
	};


	/*
	BACKUP THE CHILDREN CHANGES ON TIMELINE. ONLY CALL FROM BACKUPGROUP !!
	*/
	RVS.F.backupChildren = function(_) {
		if (_.childLayers===undefined) return;
		for (var li in _.childLayers) {
			if(!_.childLayers.hasOwnProperty(li)) continue;
			var cl = _.childLayers[li];
			for (var fi in cl.frames) {
				if(!cl.frames.hasOwnProperty(fi)) continue;
				RVS.F.backup({	path:RVS.S.slideId+".layers."+li+".timeline.frames."+fi+".timeline.start",
								lastkey:"start",
								val:RVS.SLIDER[RVS.S.slideId].layers[li].timeline.frames[fi].timeline.start,
								old:cl.frames[fi]});
			}
		}
	};

	RVS.F.getFirstFrame = function(_) {
		if (RVS.L[_.layerid].timeline.frameOrder===undefined) RVS.F.getFrameOrder({layerid:_.layerid});
		if (RVS.L[_.layerid].timeline.frameOrder[0].id==="frame_0")
			return RVS.L[_.layerid].timeline.frameOrder[1].id;
		else
			return RVS.L[_.layerid].timeline.frameOrder[0].id;
	};

	// ADD ONE SINGLE LAYER FRAME TO THE TIMELINE
	RVS.F.addLayerFrame = function(_) {
		_.framecontainer[_.frameindex] = RVS.F.addFrame({
			container:_.wrap,
			frame:_.frameindex,
			layerid:_.layerid,
			layertype:_.layertype,
			resize:"w,e",

			start:function(event,ui) {
				RVS.TL.inDrag = true;
				RVS.TL.tS.classList.add("frame_in_drag");

				RVS.TL.timeBeforeFrameChange = RVS.TL[RVS.S.slideId].main.time();
				cLayer = ui.element===undefined ? { licontainer:document.getElementById('tllayerlist_element_'+RVS.S.slideId+"_"+ui.helper[0].dataset.layerid) , layerid:ui.helper[0].dataset.layerid, frame:ui.helper[0].dataset.frame, type:ui.helper[0].dataset.layertype, bg:ui.helper[0].dataset.bg}
								: { layerid:ui.element[0].dataset.layerid, frame:ui.element[0].dataset.frame, type:ui.element[0].dataset.layertype, bg:ui.element[0].dataset.bg};
				if (cLayer.bg!=="true") {
					cLayer.frameName = RVS.F.getLayerAliasAndFrame(cLayer);
					PN =  RVS.F.getPrevNextFrame(cLayer);
					cLayer.path = RVS.F.getLayerObjPath(cLayer);
				} else {
					cLayer.layerwidth = ui.helper.width();
					cLayer.frame = "All Frames";
					PN =  RVS.F.getPrevNextFrame({layerid:cLayer.layerid, frame:RVS.F.getFirstFrame({layerid:cLayer.layerid})});
					cLayer.frameName = RVS.F.getLayerAliasAndFrame(cLayer);
				}



				slideMaxTime = RVS.F.getSlideLength();

				// IF BG DRAGGED, OR DRAGGED && IT IS A GROUPPING ELEMENT
				if ((cLayer.bg && event.type=="dragstart" && jQuery.inArray(cLayer.type,["column","row","group"])>=0) || (event.type=="dragstart" && cLayer.frame==RVS.F.getFirstFrame({layerid:cLayer.layerid}) && jQuery.inArray(cLayer.type,["column","row","group"])>=0))
					cLayer.childLayers = RVS.F.getLayerChildren({layerid:cLayer.layerid});


				//EXTEND CHILDREN WITH ITS OWN FRAMES IF WE DRAG THE BG
				if (cLayer.bg)
					cLayer.childLayers = RVS.F.getLayerFrames({layerid:cLayer.layerid, extend:cLayer.childLayers});
				else // EXTEND CHILDREN WITH THE FRAMES BEHIND THE CURRENT FRAME ON THE SAME TIMELINE
					cLayer.childLayers = RVS.F.getLayerFrames({layerid:cLayer.layerid, extend:cLayer.childLayers, afterStart:PN.cur.start, include999:true});

				//IF FRAMEMAGNIFY IS ON
				if (frameMagnify == 1 || frameMagnify == 2)
					for (var i in cLayer.childLayers) {
						if(!cLayer.childLayers.hasOwnProperty(i)) continue;
						cLayer.childLayers[i].forder = [];
						for (var fi in cLayer.childLayers[i].frames) {
							if(!cLayer.childLayers[i].frames.hasOwnProperty(fi)) continue;
							cLayer.childLayers[i].forder.push(fi);
						}
					}
				//window.smallestChildLayerStarts = RVS.F.getSmallestFrameInChildren({childLayers:cLayer.childLayers});

				// Here Attach also all Selected Layers Later (Extend the cLayer.childLayers) Object. Everything else will be done automatically

				// UPDATE FRAME TIME
				RVS.F.updateFrameTime({pos:true, cont:true, left:(PN.cur.start-0.310)});

				if (cLayer.frame === "frame_1") {
					jQuery(cLayer.licontainer).addClass("frame_1_indrag");
					for (var i in cLayer.childLayers) {
						if(!cLayer.childLayers.hasOwnProperty(i)) continue;
						cLayer.childLayers[i].hiddenc = document.getElementById('frame_unvisible_start_'+RVS.S.slideId+"_"+i);
					}

				}

			},
			ondrag:function(event,ui) {

				if (ui.position.left>window.lastCachedUiPosition && (frameMagnify == 1 || frameMagnify == 2)) RVS.F.moveChildrenTimelines({thend:((ui.position.left+cLayer.framelength/10)), order:-1,childLayers:cLayer.childLayers, difference: (ui.originalPosition.left - ui.position.left)});
				PN =  RVS.F.getPrevNextFrame(cLayer);

				//CHECK FOR FRAMES
				if (cLayer.bg!=="true") {
					if (cLayer.frame!=="frame_1" && PN.prev.end/10>=ui.position.left) ui.position.left = PN.prev.end/10;
					if (cLayer.frame=="frame_1" && 0>=ui.position.left) ui.position.left = 0;
					if (PN.next.layerid == cLayer.layerid && PN.next.start/10<=ui.position.left+PN.cur.framelength/10) ui.position.left = PN.next.start/10 - PN.cur.framelength/10;
					if (ui.position.left>slideMaxTime) ui.position.left = slideMaxTime;
				} else {//CHECK FOR FULL CONTAINER
					ui.position.left = ui.position.left<PN.prev.end/10 ? PN.prev.end/10 :
									   parseInt(ui.position.left,0) + parseInt(cLayer.layerwidth,0) >= slideMaxTime ? slideMaxTime - cLayer.layerwidth :
									   ui.position.left;
				}

				if (frameMagnify == 1 || frameMagnify == 2)  RVS.F.moveChildrenTimelines({thend:((ui.position.left+cLayer.framelength/10)), childLayers:cLayer.childLayers, difference: (ui.originalPosition.left - ui.position.left)});

				window.lastCachedUiPosition = ui.position.left;

				if (cLayer.bg!=="true") {
					RVS.F.updateSliderObj({path:cLayer.path+"start",val:Math.round(ui.position.left*10),ignoreBackup:true});
					RVS.F.updateSliderObj({path:cLayer.path+"startRelative",val:(Math.round(ui.position.left*10) - PN.prev.end),ignoreBackup:true});
					cLayer.framelength = PN.cur.framelength;
					RVS.F.updateLayerFrame(cLayer);
				} else {
					RVS.F.updateLayerFrame({layerid:cLayer.layerid, frame:"frame_999"});
				}
				if (jQuery.inArray(parseInt(cLayer.layerid,0),RVS.selLayers)>=0) document.getElementById('layerframestart').value = Math.round(ui.position.left*10);

				// UPDATE FRAME TIME
				RVS.F.updateFrameTime({pos:true, cont:true, left:ui.position.left-0.310});
				for (var i in cLayer.childLayers) {
					if(!cLayer.childLayers.hasOwnProperty(i)) continue;
					if (i!==cLayer.layerid) tpGS.gsap.set(cLayer.childLayers[i].hiddenc,{width:(ui.position.left+20)});
				}
				PN =  RVS.F.getPrevNextFrame(cLayer);
			},

			onresize:function(event,ui) {
				if (cLayer.frame!=="frame_1" && PN.prev.end/10>ui.position.left) {
					ui.position.left = PN.prev.end/10;
					ui.size.width = ((PN.cur.end - PN.prev.end) / 10);
				} else
				if (cLayer.frame=="frame_1" && 0>ui.position.left) {
					ui.position.left = 0;
					ui.size.width = (PN.cur.end) / 10;
				} else
				if (PN.next.start/10<=ui.position.left+ui.size.width) {
					ui.size.width = (( PN.next.start - PN.cur.start) / 10);
				}

				if (ui.position.left>slideMaxTime)
					ui.position.left = slideMaxTime;

				RVS.F.updateSliderObj({path:cLayer.path+"start",val:Math.round(ui.position.left*10),ignoreBackup:true});
				RVS.F.updateSliderObj({path:cLayer.path+"startRelative",val:(Math.round(ui.position.left*10) - PN.prev.end),ignoreBackup:true});
				RVS.F.updateSliderObj({path:cLayer.path+"speed",val:Math.round((ui.size.width*10) - PN.cur.splitDelay),ignoreBackup:true});

				RVS.F.updateLayerFrame(cLayer);
				//Also Update Frame999 if Reverse Animation enabled
				if (cLayer.frame==="frame_1" && RVS.L[cLayer.layerid].timeline.frames.frame_999.timeline.auto===true) RVS.F.updateLayerFrame({layerid:cLayer.layerid,frame:"frame_999"});
				if (jQuery.inArray(parseInt(cLayer.layerid,0),RVS.selLayers)>=0) {
					document.getElementById('layerframespeed').value = Math.round((ui.size.width*10) - PN.cur.splitDelay);
					document.getElementById('layerframestart').value = Math.round(ui.position.left*10);
					document.getElementById('layerframespeed_sub').innerHTML = "("+Math.round(ui.size.width*10)+")";
				}
			},

			stopdrag:function(event,ui) {
				jQuery(cLayer.licontainer).removeClass("frame_1_indrag");
				RVS.F.timelineDragsStop();
				//BACKUP VALUES
				RVS.F.openBackupGroup({id:"frame",txt:cLayer.frameName+" Start",icon:"access_time"});

				//UPDATE RELATIVE TIMES AND CHILDREN RELATIVE TIMES->
				RVS.F.updateAllstartRelatives();

				RVS.F.backup({path:cLayer.path+"start", lastkey:"start", val:Math.round(ui.position.left*10), old:Math.round(ui.originalPosition.left*10)});
				RVS.F.backupChildren({childLayers:cLayer.childLayers});
				RVS.F.closeBackupGroup({id:"frame"});
				RVS.F.renderLayerAnimation({layerid:cLayer.layerid, timeline:"full",time:RVS.TL.timeBeforeFrameChange});
				for (var i in cLayer.childLayers) {
					if(!cLayer.childLayers.hasOwnProperty(i)) continue;
					RVS.F.renderLayerAnimation({layerid:i, timeline:"full",time:RVS.TL.timeBeforeFrameChange});
				}
				RVS.TL.tS.classList.remove("frame_in_drag");
			},

			stopresize:function(event,ui) {
				RVS.F.timelineDragsStop();
				//BACKUP VALUES
				RVS.F.openBackupGroup({id:"frame",txt:cLayer.frameName+" Speed",icon:"slow_motion_video"});
				//UPDATE RELATIVE TIMES AND CHILDREN RELATIVE TIMES->
				RVS.F.updateAllstartRelatives();
				RVS.F.backup({path:cLayer.path+"start", lastkey:"start", val:Math.round(ui.position.left*10), old:Math.round(ui.originalPosition.left*10)});
				RVS.F.backup({path:cLayer.path+"speed", lastkey:"speed", val:Math.round((ui.size.width*10) - PN.cur.splitDelay), old:Math.round((ui.originalSize.width*10) - PN.cur.splitDelay)});
				RVS.F.closeBackupGroup({id:"frame"});
				ui.element.css({maxWidth:"none"});
				RVS.F.renderLayerAnimation({layerid:cLayer.layerid, timeline:"full",time:RVS.TL.timeBeforeFrameChange});
				for (var i in cLayer.childLayers) {
					if(!cLayer.childLayers.hasOwnProperty(i)) continue;
					RVS.F.renderLayerAnimation({layerid:i, timeline:"full",time:RVS.TL.timeBeforeFrameChange});
				}
				RVS.TL.tS.classList.remove("frame_in_drag");
			}
		});
	};

	RVS.F.updateAllstartRelatives = function() {
		for (var li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if (li>=0 && li<=9999)
				for (var oi in RVS.L[li].timeline.frameOrder) {
					if(!RVS.L[li].timeline.frameOrder.hasOwnProperty(oi)) continue;
					var fi = RVS.L[li].timeline.frameOrder[oi].id;
					if (fi ==="frame_0") continue;
					var	a = RVS.F.getPrevNextFrame({layerid:RVS.L[li].uid, frame:fi});
					RVS.F.updateSliderObj({path:RVS.F.getLayerObjPath({layerid:RVS.L[li].uid, frame:fi})+"startRelative",val:(a.cur.start - a.prev.end)});
				}
		}
	};

	RVS.F.getLayerObjPath = function(_) {
		return RVS.S.slideId+".layers."+_.layerid+".timeline.frames."+_.frame+".timeline.";
	};

	/*
	GET PREVIOUS AND NEXT FRAME IN TIME
	*/
	RVS.F.getPrevNextFrame = function(_) {

		var frame = RVS.L[_.layerid].timeline.frames[_.frame].timeline,
			framesd = RVS.F.getSplitDelay({layerid:_.layerid, frame:_.frame}),
			frameend = RVS.F.addT([frame.start,frame.speed,framesd]),
			firstframe = RVS.F.getFirstFrame({layerid:_.layerid}),
			pn = {	cur:{start:frame.start, end:frameend, splitDelay:framesd, framelength:(frameend-frame.start)},
					prev:{start:-1,end:0,frame:{}},
					next:{start:9999999, end:9999999, frame:{}}
				};

			//emptyspace calculation for Stucked Frame Movents ?!

		for (var fi in RVS.L[_.layerid].timeline.frames) {
			if(!RVS.L[_.layerid].timeline.frames.hasOwnProperty(fi)) continue;
			if (fi==="frame_0") continue;
			if (fi!==_.frame) {
				var c = RVS.L[_.layerid].timeline.frames[fi].timeline;
				if (c.start<frame.start && c.start>pn.prev.start)
					pn.prev = {start:c.start, end:RVS.F.addT([c.start,c.speed,RVS.F.getSplitDelay({layerid:_.layerid, frame:fi})]), frame:c,layerid:_.layerid, frameid:fi};
				if (c.start>frame.start && c.start<pn.next.start)
					pn.next = {start:c.start, end:RVS.F.addT([c.start,c.speed,RVS.F.getSplitDelay({layerid:_.layerid, frame:fi})]), frame:c, layerid:_.layerid, frameid:fi};
			}
		}

		if (_.frame==firstframe && RVS.L[_.layerid].group.puid!==-1 && jQuery.inArray(RVS.L[_.layerid].group.puid,["top","bottom","middle"])==-1) {
			var gfirstframe = RVS.F.getFirstFrame({layerid:RVS.L[_.layerid].group.puid}),
				gframe = RVS.L[RVS.L[_.layerid].group.puid].timeline.frames[gfirstframe].timeline;
			pn.prev.end = gframe.start;
			/* var _framesd = 	RVS.F.getSplitDelay({layerid:RVS.L[_.layerid].group.puid, frame:gfirstframe}), */
			var _frameend = RVS.F.addT([gframe.start,gframe.speed,framesd]);
			pn.prev.framelength = _frameend - gframe.start;
			pn.prev.realEnd = _frameend;
		}


		return pn;
	};

	/*
	UPDATE RELATIVE TIME ON FRAME
	*/
	RVS.F.setStartRelative = function(_) {
		if (RVS.TL[RVS.S.slideId].layers===undefined || RVS.TL[RVS.S.slideId].layers[_.layerid]===undefined) return;

	};
	/*
	UPDATE THE REAL SPEED IN FRAME SPEED SUB FIELD
	*/
	RVS.F.updateFrameRealSpeed = function() {
		document.getElementById('layerframespeed_sub').innerHTML = "(" + RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame].timeline.frameLength + ")";
	};

	/*
	GET LENGTH OF SPLIT TIME TO EXTEND FRAME LENGTHS
	*/
	RVS.F.getSplitDelay = function(_) {

		if ((RVS.H[_.layerid]!==undefined && RVS.H[_.layerid].splitText!==undefined) || RVS.L[_.layerid]!==undefined && RVS.L[_.layerid].timeline.split) {
			var frame = RVS.L[_.layerid].timeline.frames[_.frame],
				split = frame.chars.use ? "chars" : frame.words.use ? "words" : frame.lines.use ? "lines" : undefined;
			if (RVS.H[_.layerid]!==undefined) {
				if (RVS.H[_.layerid].splitText===undefined) RVS.F.updateSplitContent({layerid:_.layerid})
				return split!==undefined ? RVS.H[_.layerid].splitText[split].length * (frame[split].delay===undefined ? 0 : frame[split].delay*10) : 0;
			}
			else {
				return split!==undefined && frame.timeline.frameLength!==undefined ? frame.timeline.frameLength - parseInt(frame.timeline.speed,0)  : 0;
			}

		} else return 0;
	};

	/*
	UPDAE ALL LAYER FRAMES
	*/
	RVS.F.updateAllLayerFrames = function(_) {

		_ = _===undefined ? {} : _;
		for (var li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if (li!=="top" && li!=="bottom" && li!=="middle") {
				var el = document.getElementById('layerlist_element_alias_'+RVS.S.slideId+'_'+RVS.L[li].uid);
				if (el!==null) el.textContent = RVS.L[li].alias;
				for (var oi in RVS.L[li].timeline.frameOrder) {
					if(!RVS.L[li].timeline.frameOrder.hasOwnProperty(oi)) continue;
					var fi = RVS.L[li].timeline.frameOrder[oi].id;
					if (fi ==="frame_0") continue;
					if (_.frame===undefined || fi===_.frame) RVS.F.updateLayerFrame({layerid:li, frame:fi});
				}
			}
		}
	};

	RVS.F.updateLayerFrames = function(_) {
		for (var fi in RVS.L[_.layerid].timeline.frames) {
			if(!RVS.L[_.layerid].timeline.frames.hasOwnProperty(fi)) continue;
			if ((_.frame===undefined || fi===_.frame) && fi!=="frame_0") {
				RVS.F.updateLayerFrame({layerid:_.layerid, frame:fi});
			}
		}
	};



	/*
	UPDATE LAYER FRAME SIZE, POSITION
	*/
	RVS.F.updateLayerFrame = function(_) {
		if (RVS.TL[RVS.S.slideId].layers===undefined || RVS.TL[RVS.S.slideId].layers[_.layerid]===undefined) return;
		if ( RVS.L[_.layerid].timeline.frames[_.frame]===undefined) return;

		var ref = RVS.TL[RVS.S.slideId].layers[_.layerid][_.frame],
			firstframe = RVS.F.getFirstFrame({layerid:_.layerid}),
			tl = RVS.L[_.layerid].timeline.frames[_.frame].timeline,
			f0 = RVS.L[_.layerid].timeline.frames[firstframe].timeline,
			f999 = RVS.L[_.layerid].timeline.frames.frame_999.timeline,
			reversed = f999.auto && _.frame==="frame_999" && f0!==undefined && f0.frameLength!==undefined,
			framelength = _.framelength==undefined ? reversed ? f0.frameLength : RVS.F.addT([tl.speed,RVS.F.getSplitDelay({layerid:_.layerid, frame:_.frame})]) : _.framelength;


		tpGS.gsap.set(ref.frame,{left:tl.start/10+"px", width:framelength/10});

		ref.info.textContent = framelength + (reversed ? " (R)" : "");


		tl.frameLength = framelength;

		if (_.frame==="frame_999") {
			ref.endframemarker = ref.endframemarker===undefined || ref.endframemarker==null ? document.getElementById('slideendmarker_'+RVS.S.slideId+'_'+_.layerid) : ref.endframemarker;
			_.maxtime = _.maxtime === undefined ? RVS.F.getSlideLength() : _.maxtime;
			if (ref.endframemarker!==null) {
				if (tl.start/10>=_.maxtime) {
					ref.endframemarker.classList.add("endswithslide");
					f999.endWithSlide = true;
				}
				else {
					ref.endframemarker.classList.remove("endswithslide");
					f999.endWithSlide = false;
				}
			}
		}

		tl.actionTriggered = RVS.F.layerFrameTriggeredBy({layerid:_.layerid,frame:_.frame}).uid!=="" && RVS.F.layerFrameTriggered({layerid:_.layerid, frame:_.frame});

		ref.framedelay.textContent =  tl.actionTriggered ? "a" : tl.endWithSlide===true ? RVS_LANG.framewait : tl.start;
		if (RVS.L[_.layerid].timeline!=undefined) {
			ref.sloop.style.display= RVS.L[_.layerid].timeline.tloop.use && RVS.L[_.layerid].timeline.tloop.from===_.frame ? "block" : "none";
			ref.eloop.style.display= RVS.L[_.layerid].timeline.tloop.use && RVS.L[_.layerid].timeline.tloop.to===_.frame ? "block" : "none";
		}
 		ref.framedelay.className = tl.endWithSlide===true ? "coloredbg" : tl.actionTriggered && _.frame ===firstframe ? "coloredbgover" : tl.actionTriggered ? "coloredbg" : "";
	};


	RVS.F.updateAllLayerToIDLE = function() {
		for (var li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if (RVS.H[li]!==undefined && RVS.H[li].timeline!==undefined)	RVS.H[li].timeline.pause("frame_IDLE");
		}

	};



	/*
	UPDATE ANY TIMELINE
	*/
	RVS.F.updateTimeLine = function(obj) {

		if (obj.force && RVS.TL[RVS.S.slideId]!==undefined && RVS.TL[RVS.S.slideId].main===undefined) RVS.F.buildMainTimeLine();
		if (RVS.TL[RVS.S.slideId]!==undefined && RVS.TL[RVS.S.slideId][obj.timeline]!==undefined) {

			if (obj.timeline==="panzoom") {
				RVS.TL[RVS.S.slideId].slide.progress(1);
				if (RVS.TL[RVS.S.slideId].main) RVS.TL[RVS.S.slideId].main.progress(0);
			}
			if (obj.forceFullLayerRender || (idleMode===true && obj.timeline==="main")) RVS.F.buildFullLayerAnimation("atstart");

			switch (obj.state) {
				case "play":
					if (obj.timeline==="main") idleMode = false;
					if (obj.timeline==="main" && RVS.TL[RVS.S.slideId].slide) RVS.TL[RVS.S.slideId].slide.play();
					if (obj.timeline==="main" && RVS.TL[RVS.S.slideId].main) RVS.TL[RVS.S.slideId].main.play();
					if (RVS.TL[RVS.S.slideId].panzoom) RVS.TL[RVS.S.slideId].panzoom.play();
					if (obj.timeline==="main") for (var li in RVS.L) if (RVS.L.hasOwnProperty(li)) if (RVS.H[li]!==undefined && RVS.H[li].timeline!==undefined) 	RVS.H[li].timeline.play();
				break;
				case "stop":
				case "pause":
					if (obj.timeline==="main" && RVS.TL[RVS.S.slideId].slide) RVS.TL[RVS.S.slideId].slide.pause();
					if (obj.timeline==="main" && RVS.TL[RVS.S.slideId].main) RVS.TL[RVS.S.slideId].main.pause();
					for (var li in RVS.L) {
						if(!RVS.L.hasOwnProperty(li)) continue;
						if (RVS.H[li]!==undefined && RVS.H[li].timeline!==undefined) RVS.H[li].timeline.pause();
					}
					if (RVS.TL[RVS.S.slideId].panzoom) RVS.TL[RVS.S.slideId].panzoom.pause();

				break;
				case "rewind":
					if (obj.timeline==="main" && RVS.TL[RVS.S.slideId].slide) RVS.TL[RVS.S.slideId].slide.time(0);
					if (obj.timeline==="main" && RVS.TL[RVS.S.slideId].main) RVS.TL[RVS.S.slideId].main.time(0);
					if (obj.timeline==="main") for (var li in RVS.L) if (RVS.L.hasOwnProperty(li)) if (RVS.H[li]!==undefined && RVS.H[li].timeline!==undefined) RVS.H[li].timeline.time(0);
					if (RVS.TL[RVS.S.slideId].panzoom) RVS.TL[RVS.S.slideId].panzoom.time(0);
				break;

				case "time":
					if (obj.timeline==="main") idleMode = obj.time===0;
					if (obj.timeline==="main" && RVS.TL[RVS.S.slideId].slide) RVS.TL[RVS.S.slideId].slide.time(obj.time);
					if (obj.timeline==="main" && RVS.TL[RVS.S.slideId].main) RVS.TL[RVS.S.slideId].main.time(obj.time);
					if (RVS.TL[RVS.S.slideId].panzoom)
						if (obj.time===undefined)
							RVS.TL[RVS.S.slideId].panzoom.progress(0);
						else
							RVS.TL[RVS.S.slideId].panzoom.time(obj.time);
					obj.time = obj.time===0 ? "frame_IDLE" : obj.time;
					if (obj.timeline==="main") for (var li in RVS.L) if (RVS.L.hasOwnProperty(li)) if (RVS.H[li]!==undefined && RVS.H[li].timeline!==undefined) RVS.H[li].timeline.time(obj.time);
				break;
				case "progress":
					if (obj.timeline==="main") idleMode = obj.prgs===0;
					if (obj.timeline==="main" && RVS.TL[RVS.S.slideId].slide) RVS.TL[RVS.S.slideId].slide.progress(obj.prgs);
					if (obj.timeline==="main" && RVS.TL[RVS.S.slideId].main) RVS.TL[RVS.S.slideId].main.progress(obj.prgs);
					if (obj.timeline==="main") for (var li in RVS.L) if (RVS.L.hasOwnProperty(li)) if (RVS.H[li]!==undefined && RVS.H[li].timeline!==undefined) RVS.H[li].timeline.progress(obj.prgs);
					if (RVS.TL[RVS.S.slideId].panzoom) RVS.TL[RVS.S.slideId].panzoom.progress(obj.prgs);
				break;
				case "getprogress":
					return RVS.TL[RVS.S.slideId][obj.timeline].progress();
				// break;
				case "getstate":
					return RVS.TL[RVS.S.slideId][obj.timeline].isActive();
				// break;
				case "idle":
					RVS.TL.cache = {};
					if (RVS.TL[RVS.S.slideId].main)  RVS.TL.cache.main = RVS.TL[RVS.S.slideId].main.time();
					if (RVS.TL[RVS.S.slideId].panzoom) RVS.TL[RVS.S.slideId].panzoom.progress(0).pause();
					if (RVS.TL[RVS.S.slideId].main) 	RVS.TL[RVS.S.slideId].main.progress(0).pause();
					if (RVS.TL[RVS.S.slideId].slide) RVS.TL[RVS.S.slideId].slide.progress(1).pause();
					RVS.F.changeSwitchState({el:document.getElementById("timline_process"),state:"play"});
					RVS.TL.timelineStartedFromPlayStop=false;
					for (var li in RVS.L) {
						if(!RVS.L.hasOwnProperty(li)) continue;
						if (RVS.H[li]!==undefined && RVS.H[li].timeline!==undefined) RVS.H[li].timeline.pause("frame_IDLE");
					}
					idleMode = true;
				break;
			}

			if (obj.time===0 || obj.time===undefined) tpGS.gsap.set(jQuery("rs-sbg-wrap.slotwrapper_cur"),{autoAlpha:1});


			if (RVS.TL[RVS.S.slideId].main)  RVS.TL.cache.main = RVS.TL[RVS.S.slideId].main.time();
			RVS.TL.requestedTime = obj.time===undefined ? RVS.TL[RVS.S.slideId].main!==undefined ? RVS.TL[RVS.S.slideId].main.time() : 0 : obj.time;

			if (obj.updateCurTime) RVS.F.updateCurTime({pos:true, cont:true, force:true, left:RVS.TL.cache.main*100,refreshMainTimeLine:false});
		} else {
			return false;
		}
	};

	// GET END TIME OF A FRAME
	RVS.F.getTimeAtSelectedFrameEnd = function() {
		var _ = 0;
		try{ _=(RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame].timeline.start/10) + (RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame].timeline.frameLength/10);}
		catch(e) {}
		return _;
	};

	// GET MIDDLE TIME OF A FRAME
	RVS.F.getTimeAtSelectedFrameMiddle = function() {
		var _ = 0;
		try{ _=(RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame].timeline.start/10) + (RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame].timeline.frameLength/10)/2;}
		catch(e) {}
		return _;
	};

	// GET START TIME OF A FRAME
	RVS.F.getTimeAtSelectedFrameStart = function(frame) {
		var _ = 0;
		try{ _=(RVS.L[RVS.selLayers[0]].timeline.frames[frame].timeline.start/10);}
		catch(e) {}
		return _;
	};



	RVS.F.timelineDragsStop = function() {
			RVS.TL.inDrag = false;
			if (!RVS.TL.over) RVS.F.goToIdle();
	};

	RVS.F.updateLoopInputs = function(_) {
		_ = _==undefined ? {s:RVS.SLIDER[RVS.S.slideId].slide.timeline.loop.start, e:RVS.SLIDER[RVS.S.slideId].slide.timeline.loop.end} : _;
		jQuery('#slide_loop_end').val(_.e);
		jQuery('#slide_loop_start').val(_.s);
	};

	RVS.F.updateFixedScrollInputs = function(_) {
		_ = _==undefined ? {s:RVS.SLIDER.settings.scrolltimeline.fixedStart, e:RVS.SLIDER.settings.scrolltimeline.fixedEnd} : _;
		jQuery('#fixed_scroll_end').val(_.e);
		jQuery('#fixed_scroll_start').val(_.s);
	};

	RVS.F.getTimeContents = function(e) {
		var _ = {};
		_.ctm = e.querySelector('.ctm');
		_.cts = e.querySelector('.cts');
		_.ctms = e.querySelector('.ctms');
		return _;
	}

	/*
	INIT TIMELINE
	*/
	RVS.F.initTimeLineConstruct = function() {
		// Draw TimeLinear
		tlr = jQuery('#time_linear'),
		tlrcanvas = tlr.find('#time_linear_canvas');
		buildRuler();

		RVS.TL.TL = jQuery('#timeline');

		RVS.TL.tS = document.getElementById('timeline_settings');
		RVS.TL.ft = document.getElementById('frametime');
		RVS.TL.ft_txt = RVS.F.getTimeContents(RVS.TL.ft);

		RVS.TL.mt = jQuery('#maxtime');
		RVS.TL.mtfbg = jQuery('#slide_frame_container .frameswrap');
		RVS.TL.mt_txt = RVS.F.getTimeContents(RVS.TL.mt[0]);

		RVS.TL.slts = jQuery('#slidelooptimestart');
		RVS.TL.slts_marker = RVS.TL.slts.find('.timebox_marker');
		RVS.TL.slts_txt = RVS.F.getTimeContents(RVS.TL.slts[0]);

		RVS.TL.slte = jQuery('#slidelooptimeend');
		RVS.TL.slte_marker = RVS.TL.slte.find('.timebox_marker');
		RVS.TL.slte_txt = RVS.F.getTimeContents(RVS.TL.slte[0]);

		RVS.TL.fixs = jQuery('#fixedscrolltimestart');
		RVS.TL.fixs_marker = RVS.TL.fixs.find('.timebox_marker');
		RVS.TL.fixs_txt = RVS.F.getTimeContents(RVS.TL.fixs[0]);

		RVS.TL.fixe = jQuery('#fixedscrolltimeend');
		RVS.TL.fixe_marker = RVS.TL.fixe.find('.timebox_marker');
		RVS.TL.fixe_txt = RVS.F.getTimeContents(RVS.TL.fixe[0]);

		RVS.TL.ct = jQuery('#currenttime');
		RVS.TL.ct_marker = RVS.TL.ct.find('.timebox_marker');
		RVS.TL.ct_txt = RVS.F.getTimeContents(RVS.TL.ct[0]);

		RVS.TL.ht = jQuery('#hovertime');
		RVS.TL.ht_txt = RVS.F.getTimeContents(RVS.TL.ht[0]);
		RVS.F.updateMaxTime({pos:true, cont:true});


		//CLICK ON TIMELINE
		tlr.on('click',function(e) {
			var realOffset = (e.pageX - 310) + RVS.TL._scrollLeft;
			RVS.F.updateCurTime({pos:true, cont:true, left:realOffset,refreshMainTimeLine:true,  caller:"initTimeLineConstruct"});
		});

		RVS.TL.fixs.draggable({
			start:function(evet,ui) {
				RVS.TL.inDrag = true;
			},
			drag:function(event,ui) {
				var el = RVS.TL.fixe.position().left;
				ui.position.left = ui.position.left>=el ? el : ui.position.left;
				ui.position.left = ui.position.left<1 ? 1 : ui.position.left;
				tpGS.gsap.set('.fixedscrolltimemarker',{left:ui.position.left, width:(el - ui.position.left)});
				if (RVS.TL.fixs.offset().left - RVS.TL.TL.offset().left < 290)
					RVS.TL.fixs.addClass("covered");
				else
					RVS.TL.fixs.removeClass("covered");

				RVS.F.updateFixedScrollTimes({cont:true, start:Math.max(0,ui.position.left), end:el});
				RVS.F.updateFixedScrollInputs({e:el*10, s:Math.max(0,ui.position.left)*10});
			},
			stop:function(event,ui) {
				var el = RVS.TL.fixe.position().left;
				ui.position.left = ui.position.left>=el ? el : ui.position.left;
				tpGS.gsap.set('.fixedscrolltimemarker',{left:ui.position.left, width:(el - ui.position.left)});
				if (RVS.TL.fixs.offset().left - RVS.TL.TL.offset().left < 290)
					RVS.TL.fixs.addClass("covered");
				else
					RVS.TL.fixs.removeClass("covered");
				RVS.F.updateFixedScrollTimes({cont:true, start:ui.position.left, end:el});

				RVS.F.openBackupGroup({id:"SliderFixedScrollStartTime",txt:"Fixed Scroll Start Time ",icon:"timer_off"});
				RVS.F.updateSliderObj({path:"settings.scrolltimeline.fixedStart",val:Math.round(ui.position.left*10)});
				RVS.F.closeBackupGroup({id:"SliderFixedScrollStartTime"});
				RVS.F.updateFixedScrollInputs();
			},
			axis:"x"
		});


		RVS.TL.fixe.draggable({
			start:function(evet,ui) {
				RVS.TL.inDrag = true;
			},
			drag:function(event,ui) {
				var el = RVS.TL.fixs.position().left;
				ui.position.left = ui.position.left<=el ? el : ui.position.left;
				tpGS.gsap.set('.fixedscrolltimemarker',{width:(ui.position.left-el)});
				if (RVS.TL.fixe.offset().left - RVS.TL.TL.offset().left < 290)
					RVS.TL.fixe.addClass("covered");
				else
					RVS.TL.fixe.removeClass("covered");
				RVS.F.updateFixedScrollTimes({cont:true, end:ui.position.left, start:el});
				RVS.F.updateFixedScrollInputs({s:el*10, e:ui.position.left*10});
			},
			stop:function(event,ui) {
				var el = RVS.TL.fixs.position().left;
				ui.position.left = ui.position.left<=el ? el : ui.position.left;
				tpGS.gsap.set('.fixedscrolltimemarker',{width:(ui.position.left-el)});
				if (RVS.TL.fixe.offset().left - RVS.TL.TL.offset().left < 290)
					RVS.TL.fixe.addClass("covered");
				else
					RVS.TL.fixe.removeClass("covered");
				RVS.F.updateFixedScrollTimes({cont:true, end:ui.position.left, start:el});
				RVS.F.openBackupGroup({id:"SliderFixedScrollEndTime",txt:"Fixed Scroll End Time ",icon:"timer_off"});
				RVS.F.updateSliderObj({path:"settings.scrolltimeline.fixedEnd",val:Math.round(ui.position.left*10)});
				RVS.F.closeBackupGroup({id:"SliderFixedScrollEndTime"});
				RVS.F.updateFixedScrollInputs();
			},
			axis:"x"
		});



		RVS.TL.slts.draggable({
			start:function(evet,ui) {
				RVS.TL.inDrag = true;
			},
			drag:function(event,ui) {
				var el = RVS.TL.slte.position().left;
				ui.position.left = ui.position.left>=el ? el : ui.position.left;
				tpGS.gsap.set('.slidelooptimemarker',{left:ui.position.left, width:(el - ui.position.left)});
				if (RVS.TL.slts.offset().left - RVS.TL.TL.offset().left < 290)
					RVS.TL.slts.addClass("covered");
				else
					RVS.TL.slts.removeClass("covered");

				RVS.F.updateSlideLoopTimes({cont:true, start:ui.position.left, end:el});
				RVS.F.updateLoopInputs({e:el*10, s:ui.position.left*10});
			},
			stop:function(event,ui) {
				var el = RVS.TL.slte.position().left;
				ui.position.left = ui.position.left>=el ? el : ui.position.left;
				tpGS.gsap.set('.slidelooptimemarker',{left:ui.position.left, width:(el - ui.position.left)});
				if (RVS.TL.slts.offset().left - RVS.TL.TL.offset().left < 290)
					RVS.TL.slts.addClass("covered");
				else
					RVS.TL.slts.removeClass("covered");
				RVS.F.updateSlideLoopTimes({cont:true, start:ui.position.left, end:el});

				RVS.F.openBackupGroup({id:"SlideLoopStartTime",txt:"Slide Loop Start Time ",icon:"timer_off"});
				RVS.F.updateSliderObj({path:RVS.S.slideId+".slide.timeline.loop.start",val:Math.round(ui.position.left*10)});
				RVS.F.closeBackupGroup({id:"SlideLoopStartTime"});
				RVS.F.updateLoopInputs();
			},
			axis:"x"
		});

		RVS.TL.slte.draggable({
			start:function(evet,ui) {
				RVS.TL.inDrag = true;
			},
			drag:function(event,ui) {
				var el = RVS.TL.slts.position().left;
				ui.position.left = ui.position.left<=el ? el : ui.position.left;
				tpGS.gsap.set('.slidelooptimemarker',{width:(ui.position.left-el)});
				if (RVS.TL.slte.offset().left - RVS.TL.TL.offset().left < 290)
					RVS.TL.slte.addClass("covered");
				else
					RVS.TL.slte.removeClass("covered");
				RVS.F.updateSlideLoopTimes({cont:true, end:ui.position.left, start:el});
				RVS.F.updateLoopInputs({s:el*10, e:ui.position.left*10});
			},
			stop:function(event,ui) {
				var el = RVS.TL.slts.position().left;
				ui.position.left = ui.position.left<=el ? el : ui.position.left;
				tpGS.gsap.set('.slidelooptimemarker',{width:(ui.position.left-el)});
				if (RVS.TL.slte.offset().left - RVS.TL.TL.offset().left < 290)
					RVS.TL.slte.addClass("covered");
				else
					RVS.TL.slte.removeClass("covered");
				RVS.F.updateSlideLoopTimes({cont:true, end:ui.position.left, start:el});
				RVS.F.openBackupGroup({id:"SlideLoopEndTime",txt:"Slide Loop End Time ",icon:"timer_off"});
				RVS.F.updateSliderObj({path:RVS.S.slideId+".slide.timeline.loop.end",val:Math.round(ui.position.left*10)});
				RVS.F.closeBackupGroup({id:"SlideLoopEndTime"});
				RVS.F.updateLoopInputs();
			},
			axis:"x"
		});




		// CURRENT TIME DRAGGABLE
		RVS.TL.ct.draggable({
			start: function(event,ui) {
				if (RVS.TL[RVS.S.slideId] && RVS.TL[RVS.S.slideId].main && RVS.TL[RVS.S.slideId] && RVS.TL[RVS.S.slideId].main && RVS.TL[RVS.S.slideId].main.isActive()) return;
				RVS.F.buildMainTimeLine();
				RVS.TL.ht.addClass("hideme");
				RVS.TL.inDrag = true;
				if (RVS.TL[RVS.S.slideId] && RVS.TL[RVS.S.slideId].slide!==undefined) RVS._R.transitions.motionFilter.clearFull(RVS.SBGS[RVS.S.slideId].n,RVS.TL[RVS.S.slideId].slide);


			},
			stop:function(event,ui) {
				if (RVS.TL[RVS.S.slideId] && RVS.TL[RVS.S.slideId].main && RVS.TL[RVS.S.slideId].main.isActive()) return;
				if (RVS.TL.ct.offset().left - RVS.TL.TL.offset().left < 265)
					RVS.TL.ct.addClass("covered");
				else
					RVS.TL.ct.removeClass("covered");
				RVS.TL.ht.removeClass("hideme");
				RVS.F.timelineDragsStop();
			},
			drag:function(event,ui) {

				// CLEAR MOTION FILTERS IF NEEDED
				if (RVS.SBGS[RVS.S.slideId].n.fmExists) {
					RVS.SBGS[RVS.S.slideId].n.timeDirection = ui.position.left>RVS.SBGS[RVS.S.slideId].n.lastProcess ? "forwards" : ui.position.left<RVS.SBGS[RVS.S.slideId].n.lastProcess ? "backwards" : RVS.SBGS[RVS.S.slideId].n.timeDirection;
					if (RVS.SBGS[RVS.S.slideId].n.lastDirection!==RVS.SBGS[RVS.S.slideId].n.timeDirection) if (RVS.TL[RVS.S.slideId] && RVS.TL[RVS.S.slideId].slide!==undefined) RVS._R.transitions.motionFilter.clearFull(RVS.SBGS[RVS.S.slideId].n,RVS.TL[RVS.S.slideId].slide);
					RVS.SBGS[RVS.S.slideId].n.lastProcess = ui.position.left;
					RVS.SBGS[RVS.S.slideId].n.lastDirection = RVS.SBGS[RVS.S.slideId].n.timeDirection;
				}

				if (RVS.TL[RVS.S.slideId] && RVS.TL[RVS.S.slideId].main && RVS.TL[RVS.S.slideId].main.isActive()) return;
				requestAnimationFrame(function() {
					RVS.F.updateCurTime({pos:false, cont:true, left:ui.position.left,refreshMainTimeLine:true, caller:"Timeline DraG"});
					if (RVS.TL.ct.offset().left - RVS.TL.TL.offset().left < 265)
						RVS.TL.ct.addClass("covered");
					else
						RVS.TL.ct.removeClass("covered");
				});
			},
			containment:".timeline_right_container",
			axis:"x"
		});

		//HOVER TIME
		RVS.DOC.on('mousemove','.stimeline',function(e,a) {	RVS.F.updateHoverTime({pos:true, cont:true, left:(e.pageX - 310)});});
		RVS.DOC.on('mouseenter','.stimeline',function(e,a) {RVS.TL.ht.show();});
		RVS.DOC.on('mouseenter','.timeline_left_container, .context_left, .timeline_right_container',function(e,a) {RVS.TL.ht.hide();});
		RVS.DOC.on('mouseenter','#timeline_settings',function(e,a) {
			RVS.DOC.trigger('previewStopLayerAnimation');
			if (!RVS.TL.over && RVS.TL[RVS.S.slideId].main && RVS.TL.cache!==undefined && RVS.TL.cache.main!==undefined && RVS.TL.cache.main!==0) RVS.F.goToIdle();
			//RVS.F.updateTimeLine({state:"time",time:RVS.TL.cache.main, timeline:"main"});
			RVS.TL.over = true;
		});
		RVS.DOC.on('mouseleave','#timeline_settings',function(e,a) {
			if (RVS.eMode.mode!=="animation") {
				RVS.TL.over = false;
				RVS.TL.ht.hide();
				if (!RVS.TL.inDrag) RVS.F.goToIdle();
			}
		});
	};

	RVS.F.animationMode = function(on) {
		RVS.F.updateCurTime({pos:true, cont:true, force:true, left:0,refreshMainTimeLine:true, caller:"GoToIdle"});
	};

	RVS.F.setSmallestSlideLength = function(_) {
		var minl = Math.max(_.left, beforeLastEnd()/10);
		if (!_.ignore)
			RVS.F.updateMaxTime({pos:true, cont:true, left:minl});
		return minl;
	};

	RVS.F.goToIdle = function(obj) {
		if (!idleMode) {
			RVS.F.updateCurTime({pos:true, cont:true, force:true, left:0,refreshMainTimeLine:true, caller:"GoToIdle"});
			RVS.F.buildMainTimeLine();
			RVS.F.updateCurTime({pos:true, cont:true, force:false, left:0,refreshMainTimeLine:true, caller:"GoToIdle"});
		}
		idleMode = true;
	};

	/*
	MAX TIME HANDLING
	*/
	RVS.F.updateMaxTime = function(obj) {
		obj = obj===undefined ? {pos:true, cont:true, left:RVS.F.getSlideLength()} : obj;
		obj.left= obj.left===undefined ? RVS.F.getSlideLength() : obj.left;
		if (obj.pos) tpGS.gsap.set(RVS.TL.mt,{left:(obj.left)+"px"});
		if (obj.cont) pxToSec(obj.left,RVS.TL.mt_txt);

		RVS.F.updateCoveredTimelines();
	};

	/*
	CURRENT TIME HANDLING
	*/
	RVS.F.updateCurTime = function(obj) {

		if (obj.pos)
			tpGS.gsap.set(RVS.TL.ct,{left:(obj.left)+"px"});
		if (obj.cont) {
			obj.left = isNaN(obj.left) ? 0 : obj.left;
			if (obj.left>0) {
				RVS.TL.ct[0].classList.add("inmove");
				pxToSec(obj.left,RVS.TL.ct_txt);
			}
			else RVS.TL.ct[0].classList.remove("inmove");

			RVS.F.updateCoveredTimelines();
		}
		if (obj.refreshMainTimeLine)
			if (obj.left/100 <=0)
				RVS.F.updateTimeLine({force:obj.force, state:"idle",timeline:"main",caller:"UpdateCurTime A"});
			else
				RVS.F.updateTimeLine({force:obj.force, state:"time",time:(obj.left/100), timeline:"main", freeze:obj.freeze});
	};

	RVS.F.updateSlideLoopTimes = function(obj) {
		if (obj.pos) {
			tpGS.gsap.set(RVS.TL.slts,{left:obj.start+"px"});
			tpGS.gsap.set(RVS.TL.slte,{left:obj.end+"px"});

			tpGS.gsap.set('.slidelooptimemarker',{left:obj.start, width:(obj.end - obj.start)});
		}
		if (obj.cont) {
			pxToSec(obj.start,RVS.TL.slts_txt);
			pxToSec(obj.end,RVS.TL.slte_txt);
		}
	};

	RVS.F.updateFixedScrollTimes = function(obj) {
		if (obj.pos) {
			tpGS.gsap.set(RVS.TL.fixs,{left:obj.start+"px"});
			tpGS.gsap.set(RVS.TL.fixe,{left:obj.end+"px"});

			tpGS.gsap.set('.fixedscrolltimemarker',{left:obj.start, width:(obj.end - obj.start)});
		}
		if (obj.cont) {
			pxToSec(obj.start,RVS.TL.fixs_txt);
			pxToSec(obj.end,RVS.TL.fixe_txt);
		}
	};

	RVS.F.updateHoverTime = function(obj) {
		RVS.TL.hoverTimeLeft = obj.left === undefined ? RVS.TL.hoverTimeLeft : obj.left;
		RVS.TL.hoverTimeLeft = RVS.TL.hoverTimeLeft===undefined ? 0 : RVS.TL.hoverTimeLeft;
		RVS.TL._scrollLeft = RVS.TL._scrollLeft === undefined ? 0 : RVS.TL._scrollLeft;
		if (obj.pos) tpGS.gsap.set(RVS.TL.ht,{left:(obj.left)+"px"});
		if (obj.cont) pxToSec((RVS.TL.hoverTimeLeft + RVS.TL._scrollLeft),RVS.TL.ht_txt);
	};


	RVS.F.updateFrameTime = function(obj) {
		RVS.TL.frameTimeLeft = obj.left === undefined ? RVS.TL.frameTimeLeft : obj.left;
		RVS.TL.frameTimeLeft = RVS.TL.frameTimeLeft===undefined ? 0 : RVS.TL.frameTimeLeft;
		RVS.TL._scrollLeft = RVS.TL._scrollLeft === undefined ? 0 : RVS.TL._scrollLeft;
		if (obj.pos) tpGS.gsap.set(RVS.TL.ft,{left:(obj.left)+"px"});

		if (obj.cont) pxToSec((RVS.TL.frameTimeLeft),RVS.TL.ft_txt);
	};

	/*
	GET ALL FRAMES OF LAYER
	*/
	RVS.F.getLayerFrames = function(_) {
		var kids = _.extend===undefined ? {} : _.extend;
		kids[_.layerid] = { type:RVS.L[_.layerid].type, frames:{}};
		for (var oi in RVS.L[_.layerid].timeline.frameOrder) {
			if(!RVS.L[_.layerid].timeline.frameOrder.hasOwnProperty(oi)) continue;
			var fi =  RVS.L[_.layerid].timeline.frameOrder[oi].id;
			if (_.afterStart!==undefined) {
				if (RVS.L[_.layerid].timeline.frames[fi].timeline.start>_.afterStart && fi!=="frame_999")
					kids[_.layerid].frames[fi] = RVS.L[_.layerid].timeline.frames[fi].timeline.start;
				if (fi==="frame_999" && _.include999===true)
					kids[_.layerid].frames[fi] = RVS.L[_.layerid].timeline.frames[fi].timeline.start;
			} else {
				kids[_.layerid].frames[fi] = RVS.L[_.layerid].timeline.frames[fi].timeline.start;
			}
		}
		return kids;
	};


	/*
	GET ALL LAYERS LAST FRAME, AND MARK THE ONE WITH END WITH SLIDE ATTRIBUTES
	*/
	RVS.F.getLayersEndWithSlide = function() {
		var kids = {};
		for (var li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			//if (RVS.L[li].timeline.frames.frame_999.timeline.endWithSlide)
				if (li!=="top" && li!=="bottom" && li!=="middle")
					kids[li] = {
							type:RVS.L[li].type,
							endWithSlide:RVS.L[li].timeline.frames.frame_999.timeline.endWithSlide,
							frames:{frame_999:RVS.L[li].timeline.frames.frame_999.timeline.start}
					};
		}
		return kids;
	};

	RVS.F.clearLayerAnimation = function(_) {
		RVS.H[_.layerid].timeline.clear();
	};

	/*
	FORMAT THE TIME TO HUMAN READABLE TIME
	*/
	RVS.F.formatTime = function(d) {
		d = d*1000;
		var ms = parseInt((d%1000)/10),
            s = parseInt((d/1000)%60),
            m = parseInt((d/(1000*60))%60);
        ms = (ms < 10) ? "0" + ms : ms;
        m = (m < 10) ? "0" + m : m;
        s = (s < 10) ? "0" + s : s;
        return  m+":"+s+":"+ms;
	};
	/*
	UPDATE SPLITTED OR NONE SPLITTED CONTENT
	*/
	RVS.F.updateSplitContent = function(_) {
		var split = false;
		if (RVS.H[_.layerid].splitText) RVS.H[_.layerid].splitText.revert();
		if (RVS.L[_.layerid].type==="text" || RVS.L[_.layerid].type==="button") {
			for (var frame  in RVS.L[_.layerid].timeline.frames) {
				if(!RVS.L[_.layerid].timeline.frames.hasOwnProperty(frame)) continue;
				if (RVS.L[_.layerid].timeline.frames[frame].chars.use || RVS.L[_.layerid].timeline.frames[frame].words.use || RVS.L[_.layerid].timeline.frames[frame].lines.use) {
					split = true;
					break;
				}
			}
			if (split)
				RVS.H[_.layerid].splitText = new tpGS.SplitText(RVS.H[_.layerid].c,{type:"lines,words,chars",wordsClass:"rs_splitted_words",linesClass:"rs_splitted_lines",charsClass:"rs_splitted_chars"});
			else
				RVS.H[_.layerid].splitText = undefined;
		}
		else
			RVS.H[_.layerid].splitText = undefined;
		return split;
	};

	/*
	GET THE FRAMEORDER IN THE TIMELINE
	*/
	RVS.F.getFrameOrder = function(_) {
		RVS.L[_.layerid].timeline.frameOrder = [];
		for (var frame in RVS.L[_.layerid].timeline.frames) {
			if(!RVS.L[_.layerid].timeline.frames.hasOwnProperty(frame)) continue;
			RVS.L[_.layerid].timeline.frameOrder.push({id:frame, start:frame==="frame_0" ? -1 : RVS.L[_.layerid].timeline.frames[frame].timeline.start});
		}

		RVS.L[_.layerid].timeline.frameOrder.sort(function(a,b) { return a.start - b.start;});

		RVS.L[_.layerid].timeline.frameToIdle = RVS.L[_.layerid].timeline.frameToIdle===undefined ? "frame_1" : RVS.L[_.layerid].timeline.frameToIdle;



	};

	/*
	 AT COLUMN BG, BORDER WE NEED RO REMOVE VALUES SINCE BG IS SET ALREADY
	 */
	function reduceColumn(_) {
		var r = RVS.F.safeExtend(true,{},_);
		delete r.borderWidth;
		delete r.borderStyle;
		delete r.borderColor;
		delete r.backgroundColor;
		delete r.background;
		delete r.backgroundImage;
		delete r['backdrop-filter'];
		return r;
	}
	function convertDirection(_) {return _===undefined ? "start" : _==="backward" ? "end" : _==="middletoedge" ? "center" : _==="edgetomiddle" ? "edge" : _};

	// reusable function that you can wrap around your stagger vars and define an "offset" value that'll get added to the 2nd half of the values
	function offsetStagger(vars) {
		vars.from = vars.from==="edge" ? "edges" : vars.from;
		let distributor = tpGS.gsap.utils.distribute(vars);
		return function(i, target, targets) {
			return distributor(i, target, targets) + (i <= targets.length / 2 ? 0 : vars.offset || 0);
		}
	}
	/*
	RENDER LAYER ANIMATIONS
	*/
	RVS.F.renderLayerAnimation = function(_) {

		var lh = RVS.H[_.layerid],
			l = RVS.L[_.layerid],
			tPE=600; // transformPerspective

		if (RVS.TL[RVS.S.slideId].layers===undefined || RVS.TL[RVS.S.slideId].layers[_.layerid]===undefined) return;
		if (lh===undefined || l.timeline===undefined || l.timeline.frames===undefined )  return;

		if (lh.timeline) lh.timeline.pause("frame_IDLE");

		lh.timeline = new tpGS.TimelineMax({paused:true});

		var split = l.type==="text" || l.type==="button" ?  RVS.F.updateSplitContent({layerid:_.layerid}) : false;
		l.timeline.split = split;
		RVS.F.getFrameOrder({layerid:_.layerid});
		/* var fOrderLen = l.timeline.frameOrder.length, */
		var firstframe = RVS.F.getFirstFrame({layerid:_.layerid});

		for (var oi in l.timeline.frameOrder) {
			if(!l.timeline.frameOrder.hasOwnProperty(oi)) continue;

			var fi = l.timeline.frameOrder[oi].id;

			if (fi ==="frame_0") continue;



			var	_frameObj = _.frameObj===undefined || _.frame!==fi ?  l.timeline.frames[fi] : _.frameObj;

			l.timeline.sessionFilterUsed = RVS.F.checkGlobalFiltersOnLayer(_.layerid);

			if (fi ==="frame_999" && l.timeline.frames.frame_999.timeline.auto) {

				_frameObj = RVS.F.safeExtend(true,{},l.timeline.frames.frame_999);
				_frameObj.transform = RVS.F.safeExtend(true,{},l.timeline.frames.frame_0.transform);
				_frameObj.mask = RVS.F.safeExtend(true,{},l.timeline.frames.frame_0.mask);
				_frameObj.words = RVS.F.safeExtend(true,{},l.timeline.frames.frame_0.words);
				_frameObj.lines = RVS.F.safeExtend(true,{},l.timeline.frames.frame_0.lines);
				_frameObj.chars = RVS.F.safeExtend(true,{},l.timeline.frames.frame_0.chars);
				_frameObj.sfx = RVS.F.safeExtend(true,{},l.timeline.frames.frame_0.sfx);
				_frameObj.filter = RVS.F.safeExtend(true,{},l.timeline.frames.frame_0.filter);
				_frameObj.bfilter = RVS.F.safeExtend(true,{},l.timeline.frames.frame_0.bfilter);
				_frameObj.color = RVS.F.safeExtend(true,{},l.timeline.frames.frame_0.color);
				_frameObj.bgcolor = RVS.F.safeExtend(true,{},l.timeline.frames.frame_0.bgcolor);

			}

			var	_fromFrameObj = fi===firstframe ? l.timeline.frames.frame_0 : undefined,
				_frame = RVS.TL[RVS.S.slideId].layers[_.layerid][fi],
				aObj = lh.c,
				sfx = checkSFXAnimations(_frameObj.sfx.effect,lh.m,_frameObj.timeline.ease),
				tt = new tpGS.TimelineMax(),
				speed = _frameObj.timeline.speed/1000,
				splitDelay = 0;

			_frame.timeline = new tpGS.TimelineMax();


			if (sfx.type==="block") {

				sfx.ft[0].background = window.RSColor.get(_frameObj.sfx.color);
				_frame.timeline.add(tpGS.gsap.fromTo(sfx.bmask_in,speed/2, sfx.ft[0], sfx.ft[1] ,0));
				_frame.timeline.add(tpGS.gsap.fromTo(sfx.bmask_in,speed/2, sfx.ft[1], sfx.t, speed/2));
				if (fi==="frame_0" || fi==="frame_1")
					_frame.timeline.add(tt.staggerFromTo(aObj,0.05,{ autoAlpha:0},{autoAlpha:1,delay:speed/2},0),0);
				else
				if (fi==="frame_999")
					_frame.timeline.add(tt.staggerFromTo(aObj,0.05,{ autoAlpha:1},{autoAlpha:0,delay:speed/2},0),0);
			}



			var anim = convertTransformValues({sessionFilterUsed:l.timeline.sessionFilterUsed, frame:_frameObj, layerid:_.layerid, ease:_frameObj.timeline.ease, splitAmount:aObj.length,target:fi}),
				from = fi===firstframe ? convertTransformValues({sessionFilterUsed:l.timeline.sessionFilterUsed, frame:_fromFrameObj, layerid:_.layerid,ease:_frameObj.timeline.ease, splitAmount:aObj.length,target:"frame_0"}) : undefined,
				mask = _frameObj.mask.use=="true" || _frameObj.mask.use==true ? convertTransformValues({frame:{transform:{x:_frameObj.mask.x, y:_frameObj.mask.y, clip:_frameObj.mask.clip}}, layerid:_.layerid, ease:anim.ease,target:"mask"}) : undefined,
				frommask = fi===firstframe ? convertTransformValues({frame:{transform:{x:_fromFrameObj.mask.x, y:_fromFrameObj.mask.y, clip:_fromFrameObj.mask.clip}}, layerid:_.layerid, ease:anim.ease,target:"frommask"}) : undefined,
				origEase = anim.ease;


			// SET COLOR ON LAYER (TO AND FROM)
			if (_frameObj.color!==undefined && _frameObj.color.use)
				anim.color = window.RSColor.get(_frameObj.color.color);
			else
				anim.color = window.RSColor.get(l.idle.color[RVS.screen].v);

			if (_fromFrameObj!==undefined) {
				if (_fromFrameObj.color!==undefined && _fromFrameObj.color.use)
					from.color = window.RSColor.get(_fromFrameObj.color.color);
				else
					from.color = window.RSColor.get(l.idle.color[RVS.screen].v);
			}



			// SET BACKGROUNDCOLOR ON LAYER (TO AND FROM)
			if (_frameObj.bgcolor!==undefined && _frameObj.bgcolor.use) {
				var bgval = window.RSColor.get(_frameObj.bgcolor.backgroundColor);
				if (bgval.indexOf("gradient")>=0) anim.background = bgval;
				else anim.backgroundColor = bgval;

			} else {
				var bgval = window.RSColor.get(l.idle.backgroundColor);
				if (bgval.indexOf("gradient")>=0) anim.background = bgval;
				else anim.backgroundColor = bgval;
			}

			if (_fromFrameObj!==undefined) {
				if (_fromFrameObj.bgcolor!==undefined && _fromFrameObj.bgcolor.use) {
					var bgval = window.RSColor.get(_fromFrameObj.bgcolor.backgroundColor);
					if (bgval.indexOf("gradient")>=0) from.background = bgval;
					else from.backgroundColor = bgval;
				} else {
					var bgval = window.RSColor.get(l.idle.backgroundColor);
					if (bgval.indexOf("gradient")>=0) from.background = bgval;
					else from.backgroundColor = bgval;
				}
			}

			var maxSplitDelay = 0;

			// ANIMATE CHARS, WORDS, LINES
			if (split) {
				for (var i in splitTypes) {
					if(!splitTypes.hasOwnProperty(i)) continue;
					if (_frameObj[splitTypes[i]].use && !_.quickRendering) {

						var sObj = lh.splitText[splitTypes[i]], //,_frameObj[splitTypes[i]].direction),
							sanim = convertTransformValues({frame:_frameObj, source:splitTypes[i], ease:origEase, layerid:_.layerid,splitAmount:sObj.length, target:fi+"_"+splitTypes[i]}),
							sfrom = (fi===firstframe ? convertTransformValues({frame:_fromFrameObj,  ease:sanim.ease, source:splitTypes[i], layerid:_.layerid,splitAmount:sObj.length, target:"frame_0_"+splitTypes[i]}) : undefined);

						splitDelay =  parseInt(_frameObj[splitTypes[i]].delay,0) /100;

						// SET COLOR ON SPLIT  (TO AND FROM)

						if (window.RSColor.get(l.idle.color[RVS.screen].v)!==anim.color || fi!=="frame_1") sanim.color = anim.color; 	// ADDED IF TO RESPECT INLINE STYLES
						if (from!==undefined && window.RSColor.get(l.idle.color[RVS.screen].v)!==from.color) sfrom.color = from.color;	// ADDED IF TO RESPECT INLINE STYLES
						if (sfrom!==undefined && sfrom.color!==anim.color) sanim.color=anim.color;

						var	$anim = getCycles(RVS.F.safeExtend(true,{},sanim)),
							$from = fi===firstframe ? getCycles(RVS.F.safeExtend(true,{},sfrom)) : undefined,
							dir = convertDirection(_frameObj[splitTypes[i]].direction);

						delete $anim.grayscale;
						delete $anim.brightness;
						if ($from!==undefined) {
							delete $from.grayscale;
							delete $from.brightness;
						}


						$anim.stagger = dir==="center" || dir==="edge" ? offsetStagger({each:splitDelay, offset:splitDelay/2, from:dir}) : {each:splitDelay, from:convertDirection(_frameObj[splitTypes[i]].direction)};

						$anim.duration = speed;
						if ($from!==undefined) delete $from.dir;
						if (fi===firstframe) _frame.timeline.add(tt.fromTo(sObj,$from,$anim),0); else _frame.timeline.add(tt.to(sObj,$anim),0);
						maxSplitDelay = Math.max(maxSplitDelay, (sObj.length * splitDelay));
					}

				}
			}

			//SPEED SYNC WITH THE SPLIT SPEEDS IF NECESSARY
			speed = speed + maxSplitDelay;
			tPE = RVS.SLIDER.settings.general.perspectiveType==="local" ? from!==undefined && from.transformPerspective!==undefined ? from.transformPerspective : anim.transformPerspective!==undefined ? anim.transformPerspective : RVS.SLIDER.settings.general.perspective : RVS.SLIDER.settings.general.perspective;

			// ANIMATE MASK
			if (mask!==undefined) {
				mask.overflow = "hidden";
				mask.rotationX = l.idle.rotationX;
				mask.rotationY = l.idle.rotationY;
				mask.rotationZ = l.idle.rotationZ;
				mask.opacity = l.idle.opacity;
				mask.transformPerspective = tPE;

				if (fi===firstframe) {
					frommask.rotationX = l.idle.rotationX;
					frommask.rotationY = l.idle.rotationY;
					frommask.rotationZ = l.idle.rotationZ;
					frommask.opacity = l.idle.opacity;
					_frame.timeline.add(tpGS.gsap.fromTo([lh.m,lh.bgmask],speed,frommask,mask),0);
				} else {
					_frame.timeline.add(tpGS.gsap.to([lh.m,lh.bgmask],speed,mask),0);
				}
			} else {
				_frame.timeline.add(tpGS.gsap.to(lh.m,0.001,{transformPerspective:tPE, filter:"none", x:0, y:0, opacity:l.idle.opacity, rotationX:l.idle.rotationX,  rotationY:l.idle.rotationY, rotationZ:l.idle.rotationZ, overflow:"visible"}),0);
			}


			anim.force3D="auto";

			delete anim.clipB;

			// ANIMATE ELEMENT
			if (fi===firstframe) {
				delete from.clipB;

				if (lh.bg!==undefined) _frame.timeline.fromTo(lh.bg,speed,from,anim,0);
				if (lh.bg!==undefined && l.type==="column")
					_frame.timeline.fromTo(aObj,speed,reduceColumn(from),reduceColumn(anim),0);
				else
					_frame.timeline.fromTo(aObj,speed,from,anim,0);
				// BACKDROP FILTER ON THE RIGHT LEVEL
				/*if ((anim["backdrop-filter"]!==undefined || from["backdrop-filter"]!==undefined) && l.type!=="row")
					_frame.timeline.add(tpGS.gsap.fromTo(l.type!=="column" ? lh.w : lh.bg,speed,{'backdrop-filter':from["backdrop-filter"]},{'backdrop-filter':anim["backdrop-filter"],ease:anim.ease}),0);
				*/
			} else {
				if (lh.bg!==undefined) _frame.timeline.to(lh.bg,speed,anim,0);

				if (lh.bg!==undefined && l.type==="column")
					_frame.timeline.to(aObj, speed, reduceColumn(anim),0);
				else
					_frame.timeline.to(aObj, speed, anim,0);

				// BACKDROP FILTER ON THE RIGHT LEVEL
				/*if ((anim["backdrop-filter"]!==undefined) && l.type!=="row")
					_frame.timeline.add(tpGS.gsap.to(l.type!=="column" ? lh.w : lh.bg,speed,{'backdrop-filter':anim["backdrop-filter"],ease:anim.ease}),0);
				*/
			}

			if (origEase!==undefined && Array.isArray(origEase) && origEase.indexOf("SFXBounce")>=0) _frame.timeline.to(aObj,speed,{scaleY:0.5,scaleX:1.3,ease:anim.ease+"-squash",transformOrigin:"bottom"},0.0001);

			if (_.timeline==="full") {
				var pos = parseInt(_frameObj.timeline.start,0)/1000;
				lh.timeline.addLabel(fi,pos);
				lh.timeline.add(_frame.timeline,pos);
				lh.timeline.addLabel(fi+"_end","+=0.01");
				if (l.timeline.frameToIdle === fi) lh.timeline.addLabel("frame_IDLE");
			} else {
				lh.timeline.addLabel(fi);
				lh.timeline.add(_frame.timeline);
				if (fi===_.frame) {
					lh.timeline.addPause(fi+"_end+=0.5", function(frame) {this.play(frame);},[_.frame]);
				} else {
					lh.timeline.addLabel(fi+"_end");
					if (l.timeline.frameToIdle === fi) lh.timeline.addLabel("frame_IDLE");
					if (l.timeline.loop.use) {
						lh.timeline.addPause(fi+"_end+="+l.timeline.loop.speed/500,function() {this.play();});
						if (fi=="frame_999") lh.timeline.addPause(fi+"_end+=0.5",function() {this.play(0);});
					} else
						lh.timeline.addPause(fi+"_end+=0.5",function() {this.play();});

				}
			}

		}

		// RENDER HOVER ANIMATION
		if ((l.hover.usehover=='true' || l.hover.usehover==true || l.hover.usehover=="desktop") && lh.htr) {
			lh.hover = new tpGS.TimelineMax();
			lh.hover.pause();
			lh.htr.ease = l.hover.ease;

			var hoverspeed = parseInt(l.hover.speed,0)/1000;
			hoverspeed = hoverspeed===0 ? 0.00001 : hoverspeed;
			if (l.type==="column" || l.type==="row") lh.hover.to(lh.bg,hoverspeed,RVS.F.safeExtend(true,{},lh.htr),0);
			if ((l.type==="text" || l.type==="button") && l.timeline.split && lh.splitText!==undefined)
				lh.hover.to([lh.splitText.lines, lh.splitText.words, lh.splitText.chars],hoverspeed,{ color:lh.htr.color,ease:lh.htr.ease},0);

			if (l.type==="column")
				lh.hover.to(lh.c,hoverspeed,reduceColumn(RVS.F.safeExtend(true,{},lh.htr)),0);
			else
				lh.hover.to(lh.c,hoverspeed,RVS.F.safeExtend(true,{},lh.htr),0);

			if (l.type==="svg") {
				if (l.idle.svg.originalColor!==true) {
					lh.hover.to(lh.svg,hoverspeed,{	fill:window.RSColor.get(l.hover.svg.color),
												stroke:window.RSColor.get(l.hover.svg.strokeColor),
												"stroke-width":l.hover.svg.strokeWidth,
												"stroke-dasharray":RVS.F.getDashArray(l.hover.svg.strokeDashArray),
												"stroke-dashoffset":(l.hover.svg.strokeDashOffset===undefined ? 0 : l.hover.svg.strokeDashOffset)
											},0);
					lh.hover.to(lh.svgPath,hoverspeed,{ fill:window.RSColor.get(l.hover.svg.color)},0);
				}
			}

			lh.hover.to([lh.m,lh.bgmask],hoverspeed,{overflow:l.hover.usehovermask ? "hidden" : "visible"},0);

			//SET HOVER ANIMATION
			if (!lh.hoverlistener) {
				//lh.uid = _.layerid;
				lh.hoverlistener= true;
				lh.w.on('mouseenter',function() {
					if (this.dataset.uid!==undefined && RVS.L[this.dataset.uid].hover.usehover!='true') return;
					lh.hover.play();
				}).on('mouseleave', function() {
					if (this.dataset.uid!==undefined && RVS.L[this.dataset.uid].hover.usehover!='true') RVS.H[this.dataset.uid].hover.time(0).pause();
					if (RVS.eMode.mode!=="hover" || !lh.w.hasClass("selected")) lh.hover.reverse();
				});

				if (RVS.eMode.mode==="hover" && jQuery.inArray(parseInt(l.uid,0),RVS.selLayers)>=0 && RVS.L[_.layerid].hover.usehove)
					lh.hover.play();
				else
				if (lh.hover.time()>0)  lh.hover.reverse();

			}
		}	else
		if (lh.hoverlistener) {
			lh.hoverlistener= false;
			lh.w.unbind('hover');
		}


		//RENDER LOOP ANIMATION
		if (l.timeline.loop.use && (!idleMode || RVS.eMode.mode==="animation")) {
			var lif = l.timeline.loop.frame_0,
				lof = l.timeline.loop.frame_999,
				/* repeat = -1, */
				looptime = new tpGS.TimelineMax({}),
				loopmove = new tpGS.TimelineMax({repeat:-1,yoyo:l.timeline.loop.yoyo_move}),
				looprotate = new tpGS.TimelineMax({repeat:-1,yoyo:l.timeline.loop.yoyo_rotate}),
				loopscale = new tpGS.TimelineMax({repeat:-1,yoyo:l.timeline.loop.yoyo_scale}),
				loopfilter = new tpGS.TimelineMax({repeat:-1,yoyo:l.timeline.loop.yoyo_filter}),
				lspeed = parseInt(l.timeline.loop.speed,0)/1000,
				lstart = parseInt(l.timeline.loop.start)/1000 || 0,
				lsspeed = 0.2,
				lssstart = lstart+lsspeed,
				loopresetfilter = 'blur(0px) grayscale(0%) brightness(100%)',
				loopsfilter = 'blur('+parseInt(lif.blur || 0,0)+'px) grayscale('+parseInt(lif.grayscale || 0 ,0)+'%) brightness('+parseInt(lif.brightness || 100,0)+'%)',
				loopendfilter = 'blur('+(lof.blur || 0)+'px) grayscale('+(lof.grayscale || 0)+'%) brightness('+(lof.brightness || 100)+'%)';

			if (loopsfilter === 'blur(0px) grayscale(0%) brightness(100%)' && loopendfilter === 'blur(0px) grayscale(0%) brightness(100%)') {
				loopsfilter  ="none";
				loopendfilter = "none";
				loopresetfilter = "none";
			}

			looptime.add(loopmove,0);
			looptime.add(looprotate,0);
			looptime.add(loopscale,0);
			looptime.add(loopfilter,0);

			//LOOP MOVE ANIMATION
			if (!l.timeline.loop.curved) {
				//Move in First Position
				lh.timeline.fromTo(lh.lp,lsspeed,{'-webkit-filter':loopresetfilter, 'filter':loopresetfilter, x:0,y:0,z:0, scale:1,skewX:0, skewY:0, rotationX:0, rotationY:0,rotationZ:0, transformPerspective:tPE, transformOrigin:l.timeline.loop.originX+" "+l.timeline.loop.originY+" "+l.timeline.loop.originZ, opacity:1},
					RVS.F.checkLoopSkew({ x:lif.x, y:lif.y, z:lif.z, scaleX:lif.scaleX, skewX:lif.skewX, skewY:lif.skewY, scaleY:lif.scaleY,rotationX:lif.rotationX,rotationY:lif.rotationY,rotationZ:lif.rotationZ, ease:"sine.out", opacity:lif.opacity,'-webkit-filter':loopsfilter, 'filter':loopsfilter}),lstart);
				loopmove.to(lh.lp,(l.timeline.loop.yoyo_move ? lspeed/2 : lspeed),{x:lof.x, y:lof.y, z:lof.z, ease:l.timeline.loop.ease});
			} else {
				//CALCULATE EDGES
				var sangle = parseInt(l.timeline.loop.radiusAngle,0) || 0,
					v = [{x:parseInt(lif.x,0)-parseInt(lif.xr,0), y:0, z:parseInt(lif.z,0)-parseInt(lif.zr,0)},	{x:0, y:parseInt(lif.y,0)+parseInt(lif.yr,0), z:0}, {x:parseInt(lof.x,0)+parseInt(lof.xr,0), y:0, z:parseInt(lof.z,0)+parseInt(lof.zr,0)},{x:0, y:parseInt(lof.y,0)-parseInt(lof.yr,0), z:0}],
					motionPath = {type:"thru",curviness:l.timeline.loop.curviness,path:[],autoRotate:l.timeline.loop.autoRotate};
				for (var bind in v) {
					if(!v.hasOwnProperty(bind)) continue;
					motionPath.path[bind] = v[sangle];
					sangle++;
					sangle = sangle==v.length ? 0 : sangle;
				}
				//Move in First Position
				lh.timeline.fromTo(lh.lp,lsspeed,{ '-webkit-filter':loopresetfilter, 'filter':loopresetfilter, x:0,y:0,z:0, scale:1,skewX:0, skewY:0, rotationX:0, rotationY:0,rotationZ:0, transformPerspective:tPE, transformOrigin:l.timeline.loop.originX+" "+l.timeline.loop.originY+" "+l.timeline.loop.originZ, opacity:1},{ x:motionPath.path[3].x, y:motionPath.path[3].y, z:motionPath.path[3].z, scaleX:lif.scaleX, skewX:lif.skewX, skewY:lif.skewY, scaleY:lif.scaleY,rotationX:lif.rotationX,rotationY:lif.rotationY,rotationZ:lif.rotationZ, '-webkit-filter':loopsfilter, 'filter':loopsfilter, ease:"sine.out", opacity:lif.opacity},lstart);
				loopmove.to(lh.lp,(l.timeline.loop.yoyo_move ? lspeed/2 : lspeed),{motionPath:motionPath, ease:l.timeline.loop.ease});
			}

			//LOOP ROTATE ANIMATION
			looprotate.to(lh.lp,(l.timeline.loop.yoyo_rotate ? lspeed/2 : lspeed),{rotationX:lof.rotationX,rotationY:lof.rotationY,rotationZ:lof.rotationZ, ease:l.timeline.loop.ease});
			//LOOP SCALE ANIMATION
			loopscale.to(lh.lp,(l.timeline.loop.yoyo_scale ? lspeed/2 : lspeed),RVS.F.checkLoopSkew({scaleX:lof.scaleX, scaleY:lof.scaleY, skewX:lof.skewX, skewY:lof.skewY, ease:l.timeline.loop.ease}));

			//LOOP FILTER ANIMATION
			var filtanim = { opacity:lof.opacity ,ease:l.timeline.loop.ease, '-webkit-filter':loopendfilter, 'filter':loopendfilter};
			loopfilter.to(lh.lp,(l.timeline.loop.yoyo_filter ? lspeed/2 : lspeed),filtanim);

			//WELCHE WERTE MUSS ICH HIN UND HER SCHIEBEN ??
			lh.timeline.add(looptime,lssstart);
		} else {
			loopmove = new tpGS.TimelineMax({});
			loopmove.set(lh.lp,{'-webkit-filter':'blur(0px) grayscale(0%) brightness(100%)', 'filter':'blur(0px) grayscale(0%) brightness(100%)', x:0,y:0,z:0, scale:1,skewX:0, skewY:0, rotationX:0, rotationY:0,rotationZ:0, transformPerspective:tPE, transformOrigin:"50% 50%", opacity:1});
			lh.timeline.add(loopmove,0);
		}


		if (_.mode!=="atstart") {
			if (RVS.S.keyFrame==="0" || RVS.S.keyFrame==="frame_0")
				lh.timeline.pause("frame_1");
			else
			if (RVS.S.keyFrame==="idle")
				lh.timeline.pause("frame_IDLE");
			else {
				lh.timeline.pause(RVS.S.keyFrame+"_end");
			}
		}


		if (_.time!==undefined)
			lh.timeline.time(_.time);

		if (_.timeline==="loopsingleframe")
			lh.timeline.play(_.frame);
		else
		if (_.timeline!=="full") {
			lh.timeline.eventCallback("onComplete",function() {this.restart();});
		}
	};

	RVS.F.checkLoopSkew = function(_) {
		if (_.skewX===undefined) delete _.skewX;
		if (_.skewY===undefined) delete _.skewY;
		return _;
	}

	RVS.F.buildFullLayerAnimation = function(mode) {
		for (var li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if (RVS.L[li].uid!==undefined) RVS.F.renderLayerAnimation({layerid:li, timeline:"full",mode:mode});
		}
	};


	// PLAY SHORT LAYER ANIMATIONS
	RVS.F.playLayerAnimation = function(_) {
		if(_.time!==undefined)
			RVS.H[_.layerid].timeline.play(_.time);
		else
			RVS.H[_.layerid].timeline.play(0);
		animatedLayers.push(_.layerid);
	};

	// STOP SINGLE LAYER ANIMATION
	RVS.F.stopLayerAnimation = function(_) {
		if (RVS.H[_.layerid]===undefined) return;
		if (RVS.H[_.layerid].timeline) RVS.H[_.layerid].timeline.pause("frame_IDLE");
		animatedLayers = RVS.F.rArray(animatedLayers,parseInt(_.layerid,0));
	};

	// STOP ALL LAYER ANIAMTION
	RVS.F.stopAllLayerAnimation = function() {
		var wassomething = animatedLayers.length;
		while (animatedLayers.length>0) {
			RVS.F.stopLayerAnimation({layerid:animatedLayers[0]});
		}
		if (wassomething>0)
			if (RVS.TL.cache.main<=0)
				RVS.F.updateCurTime({pos:true, cont:true, force:true, left:0,refreshMainTimeLine:true, caller:"stopAllLayerAnimation"});
			else
				RVS.F.updateTimeLine({force:true, state:"time",time:RVS.TL.cache.main, timeline:"main", forceFullLayerRender:true, updateCurTime:true});
	};

	RVS.F.stopAndPauseAllLayerAnimation = function() {
		RVS.S.shwLayerAnim = false;
		RVS.F.changeSwitchState({el:document.getElementById("layer_simulator"),state:"play"});
		RVS.F.changeSwitchState({el:document.getElementById("layer_simulator_loop"),state:"play"});
		RVS.F.stopAllLayerAnimation();
	};
	////////////////////////////////////////
	// 		LAYER ANIMATION FUNCTIONS	 //
	////////////////////////////////////////
	function getCycles(anim) {
	 	var _;
		for (var a in anim) if (anim.hasOwnProperty(a)) {
			if (typeof anim[a] === "string" && anim[a].indexOf("|")>=0) {
				_=anim[a].replace("[","").replace("]","").split("|");
				anim[a]=new function(index) { return tpGS.gsap.utils.wrap(_,index)};
			}
		}
		return anim;
	};

	function shuffleArray(array) {
	  var currentIndex = array.length, temporaryValue, randomIndex;

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
	}

	// SFX ANIMATIONS
	function checkSFXAnimations(effect,mask,easedata) {

		// BLOCK SFX ANIMATIONS
		if (effect!==undefined && effect.indexOf("block")>=0) {
			var sfx = {};
			sfx.bmask_in = mask.querySelector('.tp-blockmask_in');
			if (sfx.bmask_in===null) {
				sfx.bmask_in = RVS.F.cE({cN:"tp-blockmask_in"});
				sfx.bmask_out = RVS.F.cE({cN:"tp-blockmask_out"});
				mask.appendChild(sfx.bmask_in);
				mask.appendChild(sfx.bmask_out);
			} else
				sfx.bmask_out = mask.querySelector('.tp-blockmask_out');

			easedata=easedata===undefined ? "power3.inOut" : easedata;

			sfx.ft = [{scaleY:1,scaleX:0,transformOrigin:"0% 50%"},{scaleY:1,scaleX:1,ease:easedata,immediateRender:false}];
			sfx.t =  {scaleY:1,scaleX:0,transformOrigin:"100% 50%",ease:easedata,immediateRender:false};

			sfx.type = "block";

			switch (effect) {
				case "blocktoleft":
				case "blockfromright":
					sfx.ft[0].transformOrigin = "100% 50%";
					sfx.t.transformOrigin = "0% 50%";
				break;

				case "blockfromtop":
				case "blocktobottom":
					sfx.ft = [{scaleX:1,scaleY:0,transformOrigin:"50% 0%"},{scaleX:1,scaleY:1,ease:easedata,immediateRender:false}];
					sfx.t =  {scaleX:1,scaleY:0,transformOrigin:"50% 100%",ease:easedata,immediateRender:false};
				break;

				case "blocktotop":
				case "blockfrombottom":
					sfx.ft = [{scaleX:1,scaleY:0,transformOrigin:"50% 100%"},{scaleX:1,scaleY:1,ease:easedata,immediateRender:false}];
					sfx.t =  {scaleX:1,scaleY:0,transformOrigin:"50% 0%",ease:easedata,immediateRender:false};
				break;
			}
			sfx.ft[1].overwrite = "auto";
			sfx.t.overwrite = "auto";

			return sfx;
		} else {

			return false;
		}
	}


	function convertTransformValues(_) {

		var a = _.source === undefined ? RVS.F.safeExtend(true,{},_.frame.transform) : RVS.F.safeExtend(true,{},_.frame[_.source]),
			dim ,
			torig = {originX:"50%", originY:"50%", originZ:"0"};

		for (var atr in a) {
			if(!a.hasOwnProperty(atr)) continue;

			a[atr] = (typeof a[atr]==="object") ? a[atr][RVS.screen].v : a[atr];

			if (a[atr] === "inherit" || atr==="delay" || atr==="direction" || atr==="use" || atr==="fuse") delete a[atr];	// NOT FOR ANIMATION
			else if (atr==="originX" || atr==="originY" || atr==="originZ") { // ORIGINS CAN BE IGNORED
				torig[atr] = a[atr];
				delete a[atr];
			} else {
				if ((atr==="perspective" || atr==="transformPerspective") && (RVS.SLIDER.settings.general.perspectiveType==="global" || RVS.SLIDER.settings.general.perspectiveType==="isometric")) a[atr] =  RVS.SLIDER.settings.general.perspectiveType==="isometric" ? 0 : RVS.SLIDER.settings.general.perspective;
				if (RVS.F.isNumeric(a[atr],0)) a[atr] = a[atr];  // NUMERIC ?
				else
				if (a[atr].match(/[\{\}]/g)) a[atr] = "random("+a[atr].replace(/[\{&&\}]+/g,'')+")"; // RANDOM RANGE ?
				else
				if (a[atr].match(/%/g) && RVS.F.isNumeric(parseInt(a[atr],0))) {
					dim = dim===undefined ? {height:RVS.H[_.layerid].w.height(), width:RVS.H[_.layerid].w.width()} : dim;
					a[atr] = atr=="x" ? dim.width*parseInt(a[atr],0)/100 : atr=="y" ? dim.height*parseInt(a[atr],0)/100 : a[atr]; // % BASED ?
				} else
				if (a[atr].match(/[\[\]]/g)) {	// CYCLE ?
					var cycar = a[atr].replace("[","").replace("]","").split("|");
					a[atr] = new function(index) { return tpGS.gsap.utils.wrap(cycar,index)};
				} else {
					var pos = RVS.H[_.layerid].w.position(),
						loffset = RVS.L[_.layerid].behavior.baseAlign==="slide" ? RVS.S.layer_grid_offset.left : 0,
						wrapperheight = RVS.L[_.layerid].group.puid===-1 ? RVS.S.lgh : RVS.H[RVS.L[_.layerid].group.puid]===undefined ? RVS.S.lgh  : RVS.H[RVS.L[_.layerid].group.puid].w.height(),
						wrapperwidth = RVS.L[_.layerid].group.puid===-1 ? RVS.S.lgw : RVS.H[RVS.L[_.layerid].group.puid]===undefined ? RVS.S.lgw  : RVS.H[RVS.L[_.layerid].group.puid].w.width(),
						zoneOffset = {t:0, b:0};

					if (RVS.L[_.layerid].type==="row") {
						if (RVS.L[_.layerid].group.puid==="top")
							zoneOffset = {t:0, b:Math.round(RVS.C.rZone.top[0].offsetHeight)};
						else if (RVS.L[_.layerid].group.puid==="middle")
							zoneOffset = {t:Math.round(RVS.S.ulDIM.height/2 - RVS.C.rZone.middle[0].offsetHeight/2), b:Math.round(RVS.S.ulDIM.height/2 + RVS.C.rZone.middle[0].offsetHeight/2)};
						else if (RVS.L[_.layerid].group.puid==="bottom")
							zoneOffset = {t:Math.round(RVS.S.ulDIM.height - RVS.C.rZone.bottom[0].offsetHeight), b:RVS.S.ulDIM.height+RVS.C.rZone.bottom[0].offsetHeight};
					}

					dim = dim===undefined ? {height:RVS.H[_.layerid].w.height(), width:RVS.H[_.layerid].w.width()} : dim;
					switch (a[atr]) {
						case "top":a[atr] = 0-dim.height-pos.top - zoneOffset.b;break;
						case "bottom":a[atr] = wrapperheight-pos.top - zoneOffset.t;break;
						case "left":a[atr] = loffset-dim.width-pos.left;break;
						case "right":a[atr] = wrapperwidth-pos.left;break;
						case "middle": case "center": a[atr] = atr==="x" ? wrapperwidth/2 - pos.left - dim.width/2 : atr==="y" ? wrapperheight/2 - pos.top - dim.height/2 : a[atr];break;
					}
				}
			}

			// MANAGE SKEW CALCULATION
			if (atr==="skewX" && a[atr]!==undefined && parseFloat(a[atr])!==0) {
				a['scaleY'] = a['scaleY']===undefined ? 1 : parseFloat(a['scaleY']);
				a['scaleY'] *= Math.cos(parseFloat(a[atr]) * tpGS.DEG2RAD);
			}
			if (atr==="skewY" && a[atr]!==undefined & parseFloat(a[atr])!==0) {
				a['scaleX'] = a['scaleX']===undefined ? 1 : parseFloat(a['scaleX']);
				a['scaleX'] *= Math.cos(parseFloat(a[atr]) * tpGS.DEG2RAD);
			}

		}

		a.transformOrigin = torig.originX+" "+torig.originY+" "+torig.originZ;

		// CLIPPING EFFECTS
		if (a.clip && RVS.L[_.layerid].timeline.clipPath.use) {
			var	cty = RVS.L[_.layerid].timeline.clipPath.type=="rectangle",
				cl = parseInt(a.clip,0),
				clb = 100-parseInt(a.clipB,0),
				ch = Math.round(cl/2);

			switch (RVS.L[_.layerid].timeline.clipPath.origin) {
				case "invh": a.clipPath = "polygon(0% 0%, 0% 100%, "+cl+"% 100%, "+cl+"% 0%, 100% 0%, 100% 100%, "+clb+"% 100%, "+clb+"% 0%, 0% 0%)";break;
				case "invv": a.clipPath = "polygon(100% 0%, 0% 0%, 0% "+cl+"%, 100% "+cl+"%, 100% 100%, 0% 100%, 0% "+clb+"%, 100% "+clb+"%, 100% 0%)";break;
				case "cv":a.clipPath = cty ? "polygon("+(50-ch)+"% 0%, "+(50+ch)+"% 0%, "+(50+ch)+"% 100%, "+(50-ch)+"% 100%)" : "circle("+cl+"% at 50% 50%)";break;
				case "ch":a.clipPath = cty ? "polygon(0% "+(50-ch)+"%, 0% "+(50+ch)+"%, 100% "+(50+ch)+"%, 100% "+(50-ch)+"%)" : "circle("+cl+"% at 50% 50%)";break;
				case "l":a.clipPath = cty ? "polygon(0% 0%, "+cl+"% 0%, "+cl+"% 100%, 0% 100%)" : "circle("+cl+"% at 0% 50%)";break;
				case "r":a.clipPath = cty ? "polygon("+(100-cl)+"% 0%, 100% 0%, 100% 100%, "+(100-cl)+"% 100%)" : "circle("+cl+"% at 100% 50%)";break;
				case "t":a.clipPath = cty ? "polygon(0% 0%, 100% 0%, 100% "+cl+"%, 0% "+cl+"%)" : "circle("+cl+"% at 50% 0%)";break;
				case "b":a.clipPath = cty ? "polygon(0% 100%, 100% 100%, 100% "+(100-cl)+"%, 0% "+(100-cl)+"%)" : "circle("+cl+"% at 50% 100%)";break;
				case "lt":a.clipPath = cty ? "polygon(0% 0%,"+(2*cl)+"% 0%, 0% "+(2*cl)+"%)" : "circle("+cl+"% at 0% 0%)";break;
				case "lb":a.clipPath = cty ? "polygon(0% "+(100 - 2*cl)+"%, 0% 100%,"+(2*cl)+"% 100%)" : "circle("+cl+"% at 0% 100%)";break;
				case "rt":a.clipPath = cty ? "polygon("+(100-2*cl)+"% 0%, 100% 0%, 100% "+(2*cl)+"%)" : "circle("+cl+"% at 100% 0%)";break;
				case "rb":a.clipPath = cty ? "polygon("+(100-2*cl)+"% 100%, 100% 100%, 100% "+(100 - 2*cl)+"%)" : "circle("+cl+"% at 100% 100%)";break;
				case "clr":a.clipPath = cty ? "polygon(0% 0%, 0% "+cl+"%, "+(100-cl)+"% 100%, 100% 100%, 100% "+(100-cl)+"%, "+cl+"% 0%)" : "circle("+cl+"% at 50% 50%)";break;
				case "crl":a.clipPath = cty ? "polygon(0% "+(100-cl)+"%, 0% 100%, "+cl+"% 100%, 100% "+cl+"%, 100% 0%, "+(100-cl)+"% 0%)" : "circle("+cl+"% at 50% 50%)";break;
			}
			if (RVS.F.isFirefox()!==true) a["-webkit-clip-path"] = a.clipPath;
			a["clip-path"] = a.clipPath;
			//a.overflow = "hidden"
			delete a.clip;
		} else
		if (a.clip) {
			a.clipPath = RVS.L[_.layerid].idle.spikeUse ? "polygon("+RVS.F.getClipPaths(RVS.L[_.layerid].idle.spikeLeft,0,parseFloat(RVS.L[_.layerid].idle.spikeLeftWidth))+","+RVS.F.getClipPaths(RVS.L[_.layerid].idle.spikeRight,100,(100-parseFloat(RVS.L[_.layerid].idle.spikeRightWidth)),true)+")" : "none";
			if (RVS.F.isFirefox()!==true)  a["-webkit-clip-path"] = a.clipPath;
			a["clip-path"] = a.clipPath;
			//if (a.clipPath!=="none") a.overflow = "hidden"
			delete a.clip;
		}


		// BACKDROP FILTER EFFECTS
		if (_.frame!==undefined && _.frame.bfilter!==undefined && _.frame.bfilter.use) {
			a['backdrop-filter'] = RVS.F.buildBackdropFilter(_.frame.bfilter);
			//a['-webkit-backdrop-filter'] = a['backdrop-filter'];
		}


		// FILTER EFFECTS
		if (_.frame!==undefined && _.frame.filter!==undefined && _.frame.filter.use) {
			a.filter = RVS.F.buildFilter(_.frame.filter);
			a['-webkit-filter'] = a.filter;
		} else
		if (jQuery.inArray(_.source,["chars","words","lines"])>=0 && _.frame[_.source].fuse) {
			a.filter = RVS.F.buildFilter(_.frame[_.source]);
			a['-webkit-filter'] = a.filter;

			/*a['-webkit-filter']  = 'blur('+(parseInt(_.frame[_.source].blur,0) || 0)+'px) grayscale('+(parseInt(_.frame[_.source].grayscale,0) || 0)+'%) brightness('+(parseInt(_.frame[_.source].brightness,0) || 100)+'%)';
			a.filter = 'blur('+(parseInt(_.frame[_.source].blur,0) || 0)+'px) grayscale('+(parseInt(_.frame[_.source].grayscale,0) || 0)+'%) brightness('+(parseInt(_.frame[_.source].brightness,0) || 100)+'%)';		*/
		} else {
			if (_.sessionFilterUsed || _.sessionFilterUsed===undefined){
				a['-webkit-filter'] = "blur(0px) grayscale(0%) brightness(100%)";
				a.filter = "blur(0px) grayscale(0%) brightness(100%)";
			} else {
				a['-webkit-filter'] = "none";
				a.filter = "none";
			}
		}

		// EASE
		a.ease = a.ease!==undefined ? a.ease : (a.ease===undefined && _.ease!==undefined) || (a.ease!==undefined && _.ease !==undefined && a.ease==="inherit") ? _.ease : _.frame.timeline.ease;
		a.ease = a.ease===undefined || a.ease==="default" ? "power3.inOut" : a.ease;
		a.force3D = "auto";
		return a;
	}

	RVS.F.buildFilter = function(f) {
		if (f===undefined) return "";
		var r = "";
		if (RVS.S.isChrome8889 && f.blur===0) f.blur= 0.05;
		r = f.blur!==undefined ? 'blur('+parseFloat(f.blur || 0)+'px)' : '';
		r += f.grayscale!==undefined ? (r.length>0 ? ' ': '') + 'grayscale('+parseInt(f.grayscale || 0)+'%)' : '';
		r += f.brightness!==undefined ? (r.length>0 ? ' ': '') + 'brightness('+parseInt(f.brightness || 100)+'%)' : '';

		return r==="" ? "none" : r;
	}

	RVS.F.buildBackdropFilter = function(f) {
		if (f===undefined) return "";
		var r = "";
		if (RVS.S.isChrome8889 && f.b_blur===0) f.b_blur= 0.05;
		r = f.blur!==undefined ? 'blur('+parseInt(f.blur || 0)+'px)' : '';
		r += f.grayscale!==undefined ? (r.length>0 ? ' ': '') + 'grayscale('+parseInt(f.grayscale || 0)+'%)' : '';
		r += f.sepia!==undefined ? (r.length>0 ? ' ': '') + 'sepia('+parseInt(f.sepia || 0)+'%)' : '';
		r += f.invert!==undefined ? (r.length>0 ? ' ': '') + 'invert('+parseInt(f.invert || 0)+'%)' : '';
		r += f.brightness!==undefined ? (r.length>0 ? ' ': '') + 'brightness('+parseInt(f.brightness || 100)+'%)' : '';
		return r==="" ? "none" : r;
	}

	RVS.F.checkGlobalFiltersOnLayer = function(lid) {
		var gf = RVS.L[lid].timeline.hoverFilterUsed===true;
		if (gf!==true) for (var f in RVS.L[lid].timeline.frames) if (gf===true || !RVS.L[lid].timeline.frames.hasOwnProperty(f)) continue;	 else gf = RVS.L[lid].timeline.frames[f].filter.use;
		return gf;
	}

	RVS.F.getClipPaths = function(_,o,i,reverse) {
		var r;
		switch (_) {
			case "none" :   r=o+'% 100%,'+o+'% 0%';break;
			case "top" :    r=i+'% 100%,'+o+'% 0%'; break;
			case "middle" : r=i+'% 100%,'+o+'% 50%,'+i+'% 0%'; break;
			case "bottom" : r=o+'% 100%,'+i+'% 0%'; break;
			case "two": 	r=i+'% 100%,'+o+'% 75%,'+i+'% 50%,'+o+'% 25%,'+i+'% 0%';break;
			case "three": 	r=o+'% 100%,'+i+'% 75%,'+o+'% 50%,'+i+'% 25%,'+o+'% 0%';break;
			case "four": 	r=o+'% 100%,'+i+'% 87.5%,'+o+'% 75%,'+i+'% 62.5%,'+o+'% 50%,'+i+'% 37.5%,'+o+'% 25%,'+i+'% 12.5%,'+o+'% 0%';break;
			case "five": 	r=o+'% 100%,'+i+'% 90%,'+o+'% 80%,'+i+'% 70%,'+o+'% 60%,'+i+'% 50%,'+o+'% 40%,'+i+'% 30%,'+o+'% 20%,'+i+'% 10%,'+o+'% 0%';break;
		}
		if (reverse) {
			var s = r.split(",");
			r="";
			for (var i in s) {
				if(!s.hasOwnProperty(i)) continue;
				r+=s[(s.length-1)-i]+(i<s.length-1 ? "," : "");
			}
		}
		return r;
	};

	/*


.cornerdemo.corner_four { clip-path: polygon(0% 100%, 10% 90%, 0% 70%, 10% 50%, 0% 30%, 10% 10%, 0% 0%, 100% 0%, 90% 10%, 100% 30%, 90% 50%, 100% 70%, 90% 90%, 100% 100%);}
.cornerdemo.corner_five { clip-path: polygon(0% 0%, 100% 0%, 90% 10%, 100% 20%, 90% 30%, 100% 40%, 90% 50%, 100% 60%, 90% 70%, 100% 80%, 90% 90%, 100% 100%, 0% 100%, 10% 90%, 0% 80%, 10% 70%, 0% 60%, 10% 50%, 0% 40%, 10% 30%, 0% 20%, 10% 10%);}
*/



	/*********************************
		-	TIMELINE FUNCTIONS -
	**********************************/

	RVS.F.toggleTimeLine = function() {
		if (RVS.TL.timelineStartedFromPlayStop)
			RVS.DOC.trigger('stopTimeLine');
		else
			RVS.DOC.trigger('playTimeLine');
	};
	/*
	INIT CUSTOM EVENT LISTENERS FOR TRIGGERING FUNCTIONS
	*/
	function initLocalListeners() {
		// DEFAULTS
		RVS.ENV.tlGridWrap = RVS.ENV.tlGridWrap===undefined ? jQuery('#tl_gridmanagement_wrap') : RVS.ENV.tlGridWrap;
		RVS.ENV.tlMultipWrap = RVS.ENV.tlMultipWrap===undefined ? jQuery('.tl_multip_wrap') : RVS.ENV.tlMultipWrap;
		RVS.ENV.tlMagnifWrap = RVS.ENV.tlMagnifWrap===undefined ? jQuery('.tl_magnifying_wrap') : RVS.ENV.tlMagnifWrap;

		// UPDATE SLIDE LOOP RANGE

		RVS.DOC.on('click','#maxtime',function() {
			jQuery('.slide_submodule_trigger.selected').removeClass("selected");
			RVS.F.mainMode({mode:"slidelayout", forms:["*slidelayout**mode__slidestyle*#form_slide_progress"], set:true, uncollapse:true,slide:RVS.S.slideId});
		});

		RVS.DOC.on('updateAllLayerFrames',RVS.F.updateAllLayerFrames);

		RVS.DOC.on('updateSlideLoopRange',function() {
			if (RVS.SLIDER[RVS.S.slideId].slide.timeline.loop.set) RVS.F.updateSlideLoopTimes({cont:true, pos:true, start:RVS.SLIDER[RVS.S.slideId].slide.timeline.loop.start/10, end:RVS.SLIDER[RVS.S.slideId].slide.timeline.loop.end/10});
		});

		RVS.DOC.on('updateFixedScrollRange',function() {
			if (RVS.SLIDER.settings.scrolltimeline.set && RVS.SLIDER.settings.scrolltimeline.fixed) RVS.F.updateFixedScrollTimes({cont:true, pos:true, start:parseInt(RVS.SLIDER.settings.scrolltimeline.fixedStart)/10, end:parseInt(RVS.SLIDER.settings.scrolltimeline.fixedEnd)/10});
		});

		//SHORTLINK TO SLIDE ANIM
		RVS.DOC.on('click','#the_slide_timeline' , function() {
			RVS.F.selectLayers({overwrite:true});
			jQuery('.slide_submodule_trigger.selected').removeClass("selected");
			RVS.F.mainMode({mode:"slidelayout", forms:["*slidelayout**mode__slidestyle*#form_slide_transition"], set:true, uncollapse:true,slide:RVS.S.slideId});
			return false;
		});



		// SPEED MANIPULATION
		RVS.DOC.on('click','#tl_multiplicator',function() {
			RVS.ENV.tlMultipWrap.toggleClass("selected");
			if (RVS.ENV.tlMultipWrap.hasClass("selected")) {
				RVS.ENV.tlMagnifWrap.removeClass("selected");
				RVS.ENV.tlGridWrap.removeClass("selected");
				RVS.F.clearSnapVisual();
			}
		});

		RVS.DOC.on('click','#tl_framemagnet',function() {
			RVS.ENV.tlMagnifWrap.toggleClass("selected");
			if (RVS.ENV.tlMagnifWrap.hasClass("selected")) {
				RVS.ENV.tlMultipWrap.removeClass("selected");
				RVS.ENV.tlGridWrap.removeClass("selected");
				RVS.F.clearSnapVisual();
			}
		});

		RVS.DOC.on('click','#tl_gridmanagement',function() {
			RVS.F.updateEasyInputs({container:RVS.ENV.tlGridWrap,visualUpdate:true});
			RVS.ENV.tlGridWrap.toggleClass("selected");
			if (RVS.ENV.tlGridWrap.hasClass("selected")) {
				RVS.F.snapVisual();
				RVS.ENV.tlMagnifWrap.removeClass("selected");
				RVS.ENV.tlMultipWrap.removeClass("selected");
			} else {
				RVS.F.clearSnapVisual();
			}
		});

		RVS.DOC.on('click','.closeme_tl_miniwrapper',function() {
			RVS.ENV.tlGridWrap.removeClass("selected");
			RVS.ENV.tlMagnifWrap.removeClass("selected");
			RVS.ENV.tlMultipWrap.removeClass("selected");
		});

		RVS.DOC.on('magnetframes',function(e,param) {
			if (param!==undefined && param.val!==undefined) frameMagnify = param.val;
		});

		RVS.DOC.on('click','#gsf_ok',function() {
			var ns = parseInt(document.getElementById('general_speed_factor').value,0);
			if (RVS.F.isNumeric(ns) && ns!==100) {
				ns = ns / 100;
				RVS.F.openBackupGroup({id:"frame",txt:"General Timings",icon:"access_time"});
				for (var li in RVS.L) if (RVS.L.hasOwnProperty(li)) {
					if (RVS.L[li].timeline!==undefined)
						for (var fi in RVS.L[li].timeline.frames) {
							if(!RVS.L[li].timeline.frames.hasOwnProperty(fi)) continue;
							let frame = RVS.L[li].timeline.frames[fi];
							if (RVS.F.isNumeric(parseInt(frame.timeline.start,0))) RVS.F.updateSliderObj({path:RVS.S.slideId+".layers."+li+".timeline.frames."+fi+".timeline.start",val:Math.round(parseInt(frame.timeline.start,0) * ns)});
							if (RVS.F.isNumeric(parseInt(frame.timeline.speed,0))) RVS.F.updateSliderObj({path:RVS.S.slideId+".layers."+li+".timeline.frames."+fi+".timeline.speed",val:Math.round(parseInt(frame.timeline.speed,0) * ns)});
							if (frame.words.use && RVS.F.isNumeric(parseInt(frame.words.delay,0))) RVS.F.updateSliderObj({path:RVS.S.slideId+".layers."+li+".timeline.frames."+fi+".words.delay",val:Math.round(parseInt(frame.words.delay,0) * ns)});
							if (frame.chars.use && RVS.F.isNumeric(parseInt(frame.chars.delay,0))) RVS.F.updateSliderObj({path:RVS.S.slideId+".layers."+li+".timeline.frames."+fi+".chars.delay",val:Math.round(parseInt(frame.chars.delay,0) * ns)});
							if (frame.lines.use && RVS.F.isNumeric(parseInt(frame.lines.delay,0))) RVS.F.updateSliderObj({path:RVS.S.slideId+".layers."+li+".timeline.frames."+fi+".lines.delay",val:Math.round(parseInt(frame.lines.delay,0) * ns)});
						}
				}

				// UPDATE END TIME
				RVS.F.updateSliderObj({path:RVS.S.slideId+".slide.timeline.delay",val:(Math.round(RVS.F.getSlideLength() * ns)) * 10});
				if (RVS.F.isNumeric(RVS.SLIDER[RVS.S.slideId].slide.slideChange.speed)) RVS.F.updateSliderObj({path:RVS.S.slideId+".slide.slideChange.speed",val:(Math.round(RVS.SLIDER[RVS.S.slideId].slide.slideChange.speed * ns))});
				RVS.F.updateAllLayerFrames();
				requestAnimationFrame(function() {
					RVS.F.updateSlideFrames();
				});
				RVS.F.closeBackupGroup({id:"frame"});
				RVS.DOC.trigger('updateMaxTime');
			}
			document.getElementById('general_speed_factor').value = "100%";
		});

		RVS.DOC.on('updateMaxTime',function(e,ep) {
			RVS.F.updateMaxTime({pos:true, cont:true});
			var _ = RVS.F.getLayersEndWithSlide(),
				slideMaxTime = RVS.F.getSlideLength();

			for (var li in _) {
				if(!_.hasOwnProperty(li)) continue;
				if (_[li].endWithSlide) {
					RVS.L[li].timeline.frames.frame_999.timeline.start = slideMaxTime*10;
					RVS.F.updateLayerFrame({layerid:li, frame:"frame_999",maxtime:slideMaxTime});
				}
			}
		});

		RVS.DOC.on('windowresized',function() {

			// bounce if window is resized when editor is first loading
			if(!RVS.TL.hasOwnProperty('cache')) return;

			/* var currentstate = 0; */
			RVS.TL.timelineStartedFromPlayStop = false;
			RVS.TL.cache.main = 0;
			RVS.F.updateCurTime({pos:true, cont:true, force:true, left:0,refreshMainTimeLine:true, caller:"GoToIdle"});
			RVS.F.updateCurTime({pos:true, cont:true, force:true, left:0,refreshMainTimeLine:true, caller:"GoToIdle"});
		});

		RVS.DOC.on('updateSlideTransitionTimeLine',function() {
			requestAnimationFrame(function() {
				RVS.F.updateSlideFrames();
				RVS.F.redrawSlideBG();
			});
		});

		RVS.DOC.on('playTimeLine',function() {

			RVS.F.changeSwitchState({el:document.getElementById("timline_process"),state:"stop"});
			RVS.TL.timelineStartedFromPlayStop=true;
			RVS.F.buildMainTimeLine();
			var currentstate = (RVS.TL[RVS.S.slideId] && RVS.TL[RVS.S.slideId].main) ? RVS.TL[RVS.S.slideId].main.time() : 0;
			RVS.F.updateTimeLine({force:true, state:"time",time:currentstate, timeline:"main", forceFullLayerRender:true, updateCurTime:true});
			RVS.F.updateTimeLine({state:"play",timeline:"main", force:false});
		});
		RVS.DOC.on('stopTimeLine',function() {

			/* var currentstate = 0; */
			RVS.TL.cache.main = 0;
			RVS.TL.timelineStartedFromPlayStop = false;
			RVS.F.updateCurTime({pos:true, cont:true, force:true, left:0,refreshMainTimeLine:true, caller:"GoToIdle"});
			RVS.F.buildMainTimeLine();
			RVS.F.updateCurTime({pos:true, cont:true, force:true, left:0,refreshMainTimeLine:true, caller:"GoToIdle"});
		});

		RVS.DOC.on('previewLayerAnimation',function() {
			RVS.S.shwLayerAnim = true;
			RVS.F.changeSwitchState({el:document.getElementById("layer_simulator"),state:"stop"});
			RVS.F.changeSwitchState({el:document.getElementById("layer_simulator_loop"),state:"stop"});
			for (var lid in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(lid)) continue;
				RVS.F.renderLayerAnimation({layerid:RVS.selLayers[lid]});
				RVS.F.playLayerAnimation({layerid:RVS.selLayers[lid]});
			}
		});

		RVS.DOC.on('previewStopLayerAnimation',function() {
			RVS.S.shwLayerAnim = false;
			RVS.F.changeSwitchState({el:document.getElementById("layer_simulator"),state:"play"});
			RVS.F.changeSwitchState({el:document.getElementById("layer_simulator_loop"),state:"play"});
			RVS.F.stopAllLayerAnimation();
		});


		RVS.DOC.on('click','#copy_keyframe',function() {
			if (RVS.selLayers.length==1 && RVS.S.keyFrame!==undefined) {
				keyframecache = RVS.F.safeExtend(true,{},RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame]);
				jQuery('#paste_keyframe').show();
			}

		});

		RVS.DOC.on('click','#paste_keyframe',function() {
			if (RVS.selLayers.length==1 && RVS.S.keyFrame!==undefined) {
				var fr = RVS.F.getPrevNextFrame({layerid:RVS.selLayers[0], frame:RVS.S.keyFrame});
				if (fr.next.start>=fr.cur.end+keyframecache.timeline.frameLength) {
					var cur = RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame];
					keyframecache.timeline.actionTriggered = cur.timeline.actionTriggered;
					keyframecache.timeline.start = cur.timeline.start;
					keyframecache.timeline.startRelative = cur.timeline.startRelative;
					RVS.F.updateSliderObj({path:RVS.S.slideId+".layers."+RVS.selLayers[0]+".timeline.frames."+RVS.S.keyFrame,val:keyframecache});
					RVS.DOC.trigger('updateKeyFramesList');
					RVS.F.updateAllLayerFrames();
					RVS.F.updateLayerInputFields();
				} else {
					RVS.F.showInfo({content:RVS_LANG.framesizecannotbeextended, type:"warning", showdelay:0, hidedelay:2, hideon:"", event:"" });
				}
			}
		});


	}

	/*
	Get Layer Before FRAME_999 Wiht Biggest End Point
	*/
	function beforeLastEnd() {
		var ret = 0;
		for (var li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if (li!=="top" && li!=="bottom" && li!=="middle") {
				var pn = RVS.F.getPrevNextFrame({layerid:li, frame:"frame_999"});
				ret = ret <  pn.prev.end ? pn.prev.end : ret;
			}
		}
		return ret;
	}



	function buildRuler() {

		var ctx = tlrcanvas[0].getContext('2d'),
			a=0;

		RVS.S.isRetina = RVS.S.isRetina===undefined ? (window.devicePixelRatio > 1) : RVS.S.isRetina;
		RVS.S.isIOS = RVS.S.isIOS===undefined ? ((ctx.webkitBackingStorePixelRatio < 2) || (ctx.webkitBackingStorePixelRatio == undefined)) : RVS.S.isIOS;
		RVS.S.retinaFactor =  RVS.S.retinaFactor===undefined ? (RVS.S.isRetina && RVS.S.isIOS) ? 2 : 1 : RVS.S.retinaFactor;

		ctx.canvas.width=16380*RVS.S.retinaFactor;
		ctx.canvas.height=35*RVS.S.retinaFactor;

		RVS.S.retinaFactor=parseInt(RVS.S.retinaFactor);
		ctx.scale(RVS.S.retinaFactor, RVS.S.retinaFactor);

		ctx.strokeStyle="#414244";
		ctx.font = "12px Arial";
		ctx.fillStyle = "rgba(183,187,192,0.5)";
		ctx.beginPath();

		for(var i=0;i<1640;i++) {
			if (a%20===0){
				ctx.moveTo((i*10)+1,28);
				ctx.lineTo((i*10)+1,14);
			}
			else
			if (a%10===0){
				ctx.moveTo((i*10)+1,28);
				ctx.lineTo((i*10)+1,14);
			}
			else {
				ctx.moveTo((i*10)+1,28);
				ctx.lineTo((i*10)+1,24);
			}

			a++;
			a = a==20 ? 0 : a;
		}
		ctx.stroke();

		for(var i=0;i<164;i++) {
			if (a%2===0) ctx.fillText((i)+"s",(i*100)+5,20);
			else
			ctx.fillText((i)+"s",(i*100)+5,20);
			a++;
			a = a==2 ? 0 : a;
		}

	}



	function pxToSec(d,e) {
		d = d<0 ? 0 : d;
		var min = Math.floor(d/6000),
			sec = Math.floor(Math.ceil(d - (min*6000))/100),
			ms = Math.round(d-(sec*100)-(min*6000));

		if (min==0) min = "00";
		else
		if (min<10) min = "0"+min.toString();

		if (sec==0) sec = "00";
		else
		if (sec<10) sec = "0"+sec.toString();

		if (ms==0) ms = "00";
		else
		if (ms<10) ms = "0"+ms.toString();

		e.ctm.textContent = min.toString();
		e.cts.textContent = sec.toString();
		e.ctms.textContent = ms.toString();

	}

	//////////////////////////////
	//	SWAP SLIDE PROGRESS		//
	//////////////////////////////



})();
