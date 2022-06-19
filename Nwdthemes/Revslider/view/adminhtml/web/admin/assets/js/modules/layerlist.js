/*!
 * REVOLUTION 6.0.0 EDITOR LAYERLIST JS
 * @version: 1.0 (01.07.2019)
 * @author ThemePunch
*/

(function() {

	var listElementPosArray = [],
		lastTLColHeight,
		preLoadFontSlide = {};

	/*
	INITIALISE THE BASIC LISTENERS, INPUT MANAGEMENTS ETC
	*/
	RVS.F.initLayerListBuilder = function() {
        RVS.C.rb = RVS.C.rb===undefined ? jQuery('#rev_builder') : RVS.C.rb;
        RVS.C.layerListWrap = RVS.C.layerListWrap===undefined ? jQuery('#tlLayerListWrap') : RVS.C.layerListWrap;
        initLocalInputBoxes();
		initLocalListeners();
		setUpBasicScenes();
	};

	RVS.F.updateZoneZIndexes = function() {
		tpGS.gsap.set( RVS.C.rZone.top,{zIndex:RVS.L.top.position.zIndex});
		tpGS.gsap.set( RVS.C.rZone.bottom,{zIndex:RVS.L.bottom.position.zIndex});
		tpGS.gsap.set( RVS.C.rZone.middle,{zIndex:RVS.L.middle.position.zIndex});
	};

	function getHighestRowIndexinZone(zone) {
		var ret = 0;
		for (var i in RVS.L) {
			if (!RVS.L.hasOwnProperty((i))) continue;
			if (RVS.L[i].type==="row") ret = ret<RVS.L[i].position.zIndex ? RVS.L[i].position.zIndex : ret;
		}
		return parseInt(ret);
	}
	/*
	BUILD LAYERS ONE GO IF SLIDE / LAYER LAYOUT SELECTED
	*/
	RVS.F.buildLayerLists = function(obj) {

		//SAVE LAYERS INTO A QUICK REFERENCABLE OBJECT
		RVS.L = RVS.SLIDER[RVS.S.slideId].layers;


		// PRELOAD FONT LAYERS FOR THE CURRENT SLIDE
		if (preLoadFontSlide[RVS.S.slideId]!==true)  {
			preLoadFontSlide[RVS.S.slideId] = true;
			RVS.F.preloadUsedFonts();
		}
		var zII = 0;
		//Update Column Breaks in Rows (FallBack)
		RVS.F.updateColumnBreaksChildren();
		if (RVS.L.top===undefined) {
			zII = getHighestRowIndexinZone();
			RVS.L.top =  {uid:"top", group:{puid:-1, groupOrder:zII}, type:"zone", alias:"TOP ROWS", position:{zIndex:zII}};
			RVS.L.middle = {uid:"middle", group:{puid:-1, groupOrder:zII}, type:"zone", alias:"MID ROWS",  position:{zIndex:zII}};
			RVS.L.bottom =  {uid:"bottom", group:{puid:-1, groupOrder:zII}, type:"zone", alias:"BOTTOM ROWS",  position:{zIndex:zII}};
		}

		RVS.F.updateZoneZIndexes();

		RVS.F.getLlength();
		RVS.selLayers = [];

		RVS.S.llcache[RVS.S.slideId] = RVS.S.llcache[RVS.S.slideId]===undefined ? {} : RVS.S.llcache[RVS.S.slideId];
		obj = obj===undefined ? {} : obj;


		if (obj.force || RVS.S.llcache[RVS.S.slideId].tlLayerList === undefined)
			RVS.F.reDrawListElements();
		else
		if (RVS.S.llcache[RVS.S.slideId].tlLayerList!==undefined) {
			RVS.C.layerListWrap[0].appendChild(RVS.S.llcache[RVS.S.slideId].tlLayerList);
		}

		RVS.F.layerListScrollable("update");

		if (!obj.ignoreRebuildHTML) RVS.F.buildHTMLLayers({ignoreDrawLayers:obj.ignoreDrawLayers});
		if (!obj.ignoreSelectLayers) RVS.F.selectLayers();
	};



	/*
	GET THE LENGTH OF THE CURRENT LAYER LIST
	*/
	RVS.F.getLlength = function() {
		RVS.V.Llength = 0;
		for (var i in RVS.L) {
			if(!RVS.L.hasOwnProperty(i)) continue;
			RVS.V.Llength++;
		}
	};

	/*
	CLEAR THE CACHED, DISPATCHED LAYER LISTS CONTAINERS
	*/
	RVS.F.clearCache = function(obj) {
		if (obj!=undefined && obj.slides!==undefined)
			for (var i in obj.slides) {
				if(!obj.slides.hasOwnProperty(i)) continue;
				if (cache!==undefined && RVS.S.llcache[obj.slides[i]]!==undefined && RVS.S.llcache[obj.slides[i]].tlLayerList!==undefined) {
					//RVS.S.llcache[obj.slides[i]].tlLayerList.detach();
					RVS.S.llcache[obj.slides[i]].tlLayerList.remove();
					RVS.S.llcache[obj.slides[i]] = undefined;
				}
			}
	};


	/*
	GET LAYER KIDS ID'S
	*/
	RVS.F.getLayerChildren = function(_) {
		var kids = _.kids===undefined ? {} : _.kids;
		for (var li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if (RVS.L[li].group.puid==_.layerid) {
				kids[RVS.L[li].uid] = {
						type:RVS.L[li].type,
						frames:{}
				};
				for (var fi in RVS.L[li].timeline.frames) {
					if(!RVS.L[li].timeline.frames.hasOwnProperty(fi)) continue;
					kids[RVS.L[li].uid].frames[fi] = RVS.L[li].timeline.frames[fi].timeline.start;
				}
				if (jQuery.inArray(RVS.L[li].type,["column","row","group"])>=0)
					kids = RVS.F.getLayerChildren({layerid:RVS.L[li].uid, kids:kids});
			}
		}
		return kids;
	};


	/*
	COUNT THE VISIBLE (UNCOLLAPSED) ELEMENTS
	*/
	RVS.F.getVisibleLayersInList = function() {
		var count = 0;
		for (var li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if (RVS.L[li].group.puid===-1) count++;
			else
			if (RVS.L[RVS.L[li].group.puid].group.puid===-1 && !jQuery('#tllayerlist_element_'+RVS.S.slideId+'_'+RVS.L[li].group.puid).hasClass("collapsed")) count++;
			else
			if (RVS.L[RVS.L[li].group.puid].group.puid!==-1 && !jQuery('#tllayerlist_element_'+RVS.S.slideId+'_'+RVS.L[RVS.L[li].group.puid].group.puid).hasClass("collapsed")) count++;
		}
		return count;
	};

	/*
	EXPAND - COLLAPS TIMELINE (TOGGLE)
	*/
	RVS.F.expandCollapseTimeLine = function(updateHeight,direction,noanim,nocallback) {

		if (window.timeline_in_resize || RVS.S.ReadyToShowAll!=="done") return; // WAIT UNTIL EVERYTHING INITIALISED

		var h, hpast;
		h=hpast=RVS.TL.TL.height();

		if (updateHeight) {
			if (h<=76 && direction!=="close" || direction==="open")
				h = RVS.V.timeline_height==="auto" ? Math.max(RVS.V.timeline_minOpenHeight,(window.innerHeight - RVS.S.ulDIM.height - 65)) :  RVS.V.timeline_height;
			else
				h = 76;

			if (h!==hpast)
				if (noanim) {
					RVS.TL.TL[0].style.height = h+"px";
					RVS.ENV.globVerOffset = h;
					if (nocallback!==true) RVS.DOC.trigger('updatesliderlayout',"layerlist.js-142");
				} else {
					tpGS.gsap.to(RVS.TL.TL,0.3,{height:h,ease:"power3.out", onUpdate:function() {
						RVS.ENV.globVerOffset = tpGS.gsap.getProperty(RVS.TL.TL[0],'height');
						RVS.DOC.trigger('updatesliderlayout',"layerlist.js-142");
					}});
				}
			else
				RVS.ENV.globVerOffset = h;

			if (noanim) RVS.C.layerListWrap[0].style.height = (h-36)+"px";
			else
				tpGS.gsap.to(RVS.C.layerListWrap,0.15,{height:(h-36), ease:"power3.out",onComplete:function() {
					RVS.DOC.trigger('updateScrollBars');
				}});
		}
		RVS.C.theEditor[0].style.paddingBottom = h+"px";
		if (h>76)
			jQuery('#timeline_collapser').show();
		else
			jQuery('#timeline_collapser').hide();
		clearTimeout(RVS.S.updateTimeLineHeight);

		if (lastTLColHeight !== h)
			RVS.S.updateTimeLineHeight = setTimeout(function() {
				RVS.ENV.globVerOffset = h;
				if (nocallback!==true) RVS.DOC.trigger('updatesliderlayout',"layerlist.js-157");
				lastTLColHeight = h;
			},170);
	};



	/*
	GET THE LATEST LAYER ORDER BASED ON Z-INDEX
	*/
	RVS.F.getLayerOrder = function() {
		return innerListOrder([],-1);
	};

	RVS.F.getLayerBeforeZIndex = function(uid) {
		var list = innerListOrder([],RVS.L[uid].group.puid),
			luid,
			i;

		switch(RVS.L[uid].type) {
			case "row":
			case "column":
				for (i in list) {
					if(!list.hasOwnProperty(i)) continue;
					//If Sort of List Elements is Smaller (Comes before in the List) then that is our element
					if (list[i].sort < RVS.L[uid].group.groupOrder && (luid===undefined || list[i].sort > RVS.L[luid].group.groupOrder))
						luid = list[i].id;
				}
			break;
			default:
				for (i in list) {
					if(!list.hasOwnProperty(i)) continue;
					//If zIndex of List Elements is Bigger (Comes before in the List) then that is our element
					if (list[i].zIndex > RVS.L[uid].position.zIndex && (luid===undefined || list[i].zIndex < RVS.L[luid].position.zIndex))
						luid = list[i].id;
				}
			break;
		}
		return luid;
	};

	RVS.F.getLayerAfterZIndex = function(uid) {

		var list = innerListOrder([],RVS.L[uid].group.puid),
			luid,
			i;

		switch(RVS.L[uid].type) {
			case "row":
			case "column":
				for (i in list) {
					if(!list.hasOwnProperty(i)) continue;
					//If Sort of List Elements is Bigger (Comes After in the List) then that is our element
					if (list[i].sort > RVS.L[uid].group.groupOrder && (luid===undefined || list[i].sort < RVS.L[luid].group.groupOrder))
						luid = list[i].id;
				}
			break;
			default:
				for (i in list) {
					if(!list.hasOwnProperty(i)) continue;
					//If zIndex of List Elements is Smaller (Comes After in the List) then that is our element
					if (list[i].zIndex < RVS.L[uid].position.zIndex && (luid===undefined || list[i].zIndex > RVS.L[luid].position.zIndex))
						luid = list[i].id;
				}
			break;
		}
		return luid;
	};



/***********************************
	MAIN INTERNAL FUNCTIONS
************************************/

	/*
	INIT LOCAL INPUT BOX FUNCTIONS
	*/
	function initLocalInputBoxes() {


		RVS.DOC.on('click dblclick','.layerselector',function() {
			var _ = jQuery(this);

			if (!_.hasClass("checked")) {
				RVS.F.selectLayers({id:this.dataset.id,overwrite:false, action:"add"});
				_.addClass("checked");
			} else {
				RVS.F.selectLayers({id:this.dataset.id,overwrite:false, action:"remove"});
				_.removeClass("checked");
			}
			return false;
		});

		RVS.DOC.on('click','.layerlist_element_innerwrap',function(evt) {


			if (this.dataset.ignore) return;
			if (this.id==="slide_bg_anim_trigger") return;

			for (var i in RVS.JHOOKS.layerListElementClicked) {
				if(!RVS.JHOOKS.layerListElementClicked.hasOwnProperty(i)) continue;
				RVS.JHOOKS.layerListElementClicked[i](this.dataset.id);
			}


			var frame = evt.target.nodeName==="FRAMEWRAP" || evt.target.nodeName==="FFBEFORE" || evt.target.nodeName==="FFAFTER" ? evt.target.dataset.frame : evt.target.parentNode.nodeName==="FRAMEWRAP" ? evt.target.parentNode.dataset.frame : undefined;


			if (frame!==undefined) {
				if (RVS.eMode.mode!=="animation" || RVS.eMode.top!=="layer")
					RVS.F.mainMode({mode:"slidelayout", forms:["*slidelayout**mode__slidecontent*#form_layer_animation"], set:true, uncollapse:true,slide:RVS.S.slideId});

				jQuery('framewrap.selected').removeClass("selected");
				if (evt.target.nodeName==="FRAMEWRAP") evt.target.className +=" selected";
				if (evt.target.parentNode.nodeName==="FRAMEWRAP") evt.target.parentNode.className +=" selected";
				RVS.F.selectLayers({id:this.dataset.id,overwrite:true, action:"add"}); //selectedKeyFrame:frame
				RVS.F.setKeyframeSelected(frame);
				if (RVS.eMode.mode==="animation") RVS.F.updateKeyframeSelected(frame);
			}
			else
			if (evt.target.className.indexOf('layerselector')==-1 && evt.target.className.indexOf('layerlist_element_level')==-1 && evt.target.className.indexOf('material-icons')==-1)
				if (RVS.eMode.mode==="animation") {
					if (RVS.L[this.dataset.id]!==undefined) {
						RVS.F.selectLayers({id:this.dataset.id,overwrite:true, action:"add",selectedKeyFrame:RVS.L[this.dataset.id].timeline.frameToIdle});
						RVS.F.updateKeyframeSelected(RVS.L[this.dataset.id].timeline.frameToIdle);
					}
				} else {
					if (window.cmdctrldown)
						RVS.F.selectLayers({id:this.dataset.id,overwrite:false, action:"add"});
					else
						RVS.F.selectLayers({id:this.dataset.id,overwrite:true, action:"add"});
				}

			jQuery('.directedit').removeClass("directedit");

			return false;
		});


		RVS.DOC.on('click','.layerlist_element_level',function() {
			jQuery(document.activeElement).blur();

			if (this.className.indexOf("free_positioned_layers_toggle")>=0)
				jQuery('#mainLayerListWrap_'+RVS.S.slideId).toggleClass('collapsedfreelayers');
			else
				jQuery(this).closest('li').toggleClass("collapsed");
			setLayerListMaxHeight(true);
			RVS.F.saveCollapsedGroups();
			return false;
		});

		RVS.DOC.on('click','.layer_allcollaps',function() {
			if (this.className.indexOf("collapsed")>=0) {
				this.className = "layer_allcollaps";
				jQuery('#tl_layerList_'+RVS.S.slideId+" .tllayerlist_element_group.collapsed").removeClass("collapsed");
				jQuery('#tl_layerList_'+RVS.S.slideId+" .tllayerlist_element_row.collapsed").removeClass("collapsed");
				jQuery('#tl_layerList_'+RVS.S.slideId+" .tllayerlist_element_column.collapsed").removeClass("collapsed");
			} else {
				this.className = "layer_allcollaps collapsed";
				jQuery('#tl_layerList_'+RVS.S.slideId+" .tllayerlist_element_group").addClass("collapsed");
				jQuery('#tl_layerList_'+RVS.S.slideId+" .tllayerlist_element_row").addClass("collapsed");
				jQuery('#tl_layerList_'+RVS.S.slideId+" .tllayerlist_element_column").addClass("collapsed");
			}
			setLayerListMaxHeight(true);
			RVS.F.saveCollapsedGroups();
			RVS.F.layerListScrollable("update");
			return false;
		});

		RVS.DOC.on('dblclick','.layerlist_element_innerwrap',function() {
			var lei = jQuery(this);
			lei.addClass("directedit");
			lei.find('.indeplayerinput').trigger('focus');
		});

		RVS.DOC.on('click','.indeplayerinput',function() {
			return false;
		});

		RVS.DOC.on('blur','.indeplayerinput',function() {
			jQuery(this).closest('.directedit').removeClass("directedit");
		});

		RVS.DOC.on('click dblclick','.fclayer_withslideend',function() {

			var p = RVS.F.getLayerObjPath({layerid:this.dataset.layerid, frame:"frame_999"}),
				v = RVS.F.getDeepVal({path:p+"start"}),
				na = RVS.F.getLayerAliasAndFrame({layerid:this.dataset.layerid, frame:"frame_999"});

			if (jQuery(this).hasClass('endswithslide'))
				v = parseInt(v,0)-100;
			else
				v = RVS.F.getSlideLength()*10;

			RVS.F.updateSliderObj({path:p+"start",val:v, txt:na+" Start",icon:"access_time"});
			RVS.F.updateLayerFrame({layerid:this.dataset.layerid, frame:"frame_999"});
			return false;
		});

		RVS.DOC.on('click','#timeline_collapser',function() {
			clearTimeout(window.closeTimeLineTimer);
			RVS.F.expandCollapseTimeLine(true,"close");
		});

		RVS.C.layerListWrap.on('mouseenter',function() {
			window.closeTimeLineTimer = setTimeout(function() {RVS.F.expandCollapseTimeLine(true,"open");},400);
		});

		RVS.C.layerListWrap.on('mouseleave',function() {
			clearTimeout(window.closeTimeLineTimer);
		});
	}




	/*
	INIT CUSTOM EVENT LISTENERS FOR TRIGGERING FUNCTIONS
	*/
	function initLocalListeners() {

		RVS.S.llcache = RVS.S.llcache===undefined ? {} : RVS.S.llcache;
		// LAYER ALIAS UPDATED, VISIBLE NAMES NEED TO BE UPDATED
		RVS.DOC.on('updateLayerAlias',function(e,ep) {
			document.getElementById('layerlist_element_alias_'+RVS.S.slideId+'_'+ep).innerHTML = RVS.L[ep].alias;
			if (RVS.L[ep].type==="group") document.getElementById('_group_head_title_'+RVS.S.slideId+'_'+ep).innerHTML = RVS.L[ep].alias;
			if (RVS.selLayers[0]==ep) document.getElementById('updateLayerSingleAliasInput').value=RVS.L[ep].alias;
		});

		// BEFORE WE LEAVE SLIDE, CLEAN UP A BIT
		RVS.DOC.on('beforeSlideChange',function(e,ep) {
			RVS.F.stopAndPauseAllLayerAnimation();
			if (RVS.S.llcache[RVS.S.slideId]!==undefined && RVS.S.llcache[RVS.S.slideId].tlLayerList!==undefined) {
				RVS.S.llcache[RVS.S.slideId].tlLayerList.parentElement.removeChild(RVS.S.llcache[RVS.S.slideId].tlLayerList);
			}
		});

		RVS.DOC.on('updateScrollBars',function(e,ep){
			RVS.F.layerListScrollable("update");
			RVS.C.layerListWrap.trigger('ps-scroll-x');
			RVS.C.layerListWrap.trigger('ps-scroll-y');
		});


	}



/*************************************
 	- SET UP BASIC SCENES -
**************************************/
	function setUpBasicScenes() {


		RVS.TL.TL.resizable({
			handles:"n",
			minHeight:71,
			maxHeight:600,
			start:function(event,ui) {
				RVS.TL.TL.addClass("inResize");
			},
			resize:function(event,ui) {
				tpGS.gsap.set('#tlLayerListWrap',{height:(ui.size.height-36)+"px"});
				tpGS.gsap.set('#timeline',{top:"0px"});
				RVS.ENV.globVerOffset = ui.size.height;
				RVS.DOC.trigger('updatesliderlayout',"layerlist.js-355");
				window.timeline_in_resize = true;
			},
			stop:function(event,ui) {
				RVS.TL.TL.removeClass("inResize");
				tpGS.gsap.set('#tlLayerListWrap',{height:(ui.size.height-36)+"px"});
				RVS.V.timeline_height = ui.size.height;
				RVS.DOC.trigger('updateScrollBars');
				window.timeline_in_resize = false;
				RVS.F.expandCollapseTimeLine(true,"open");


			}
		});

		RVS.F.layerListScrollable('init');
	}

	function setLayerListMaxHeight(force) {
		/*var cor = 60,
			fh = jQuery('#the_layers_in_slide_'+RVS.S.slideId).height(),
			ch = RVS.S.llcache[RVS.S.slideId].cLayerList.height();
			min = Math.min(fh,ch+cor);

		if (force) min = ch<400 ? ch+cor : min;

		jQuery('#the_layers_in_slide_'+RVS.S.slideId).height(min);

		jQuery('#mainLayerListWrap_'+RVS.S.slideId+'').height(Math.min(min-cor,ch));
		layerListScrollable("update");*/
	}

	RVS.F.updateCoveredTimelines = function() {
		var ol = RVS.TL.TL.offset().left;
		if (RVS.TL.ct.offset().left - ol < 290)
			RVS.TL.ct[0].classList.add("covered");
		else
			RVS.TL.ct[0].classList.remove("covered");

		if (RVS.TL.mt.offset().left - ol < 290)
			RVS.TL.mt[0].classList.add("covered");
		else
			RVS.TL.mt[0].classList.remove("covered");
		if (RVS.SLIDER[RVS.S.slideId].slide.timeline.loop.set) {
			if (RVS.TL.slte.offset().left - ol < 290)
				RVS.TL.slte[0].classList.add("covered");
			else
				RVS.TL.slte[0].classList.remove("covered");
			if (RVS.TL.slts.offset().left - ol < 290)
				RVS.TL.slts[0].classList.add("covered");
			else
				RVS.TL.slts[0].classList.remove("covered");
		}

	};

	RVS.F.layerListScrollable = function(type) {
		if (type===undefined || type==="init") {
			RVS.TL._scrollLeft = 0;
			RVS.C.layerListWrap.RSScroll({
				wheelPropagation:true,
				suppressScrollX:false,
				minScrollbarLength:30
			}).on('ps-scroll-y',function(){
				updateListElementPositionsArray({container:'#tl_layerList_'+RVS.S.slideId,cIdName:'tllayerlist_element'});
			}).on('ps-scroll-x',function(){
				RVS.TL._scrollLeft = this.scrollLeft;
				tpGS.gsap.set([jQuery('#tlLayerListWrap .context_left'),'#the_st_cl', '#hovertime', '.timeline_left_container'], {x:this.scrollLeft});
				tpGS.gsap.set(['#timeline_top_toolbar'], {x:(0-this.scrollLeft)});
				RVS.F.updateHoverTime({pos:false, cont:true});
				RVS.F.updateCoveredTimelines();
			});

		} else {
			if (type==="scrollToSelected") {
				var st = jQuery('.tllayerlist_element.checked').first().length>0 ? jQuery('.tllayerlist_element.checked').first().offset().top : 0;
				lastTLColHeight = lastTLColHeight===undefined ? RVS.TL.TL.height() : lastTLColHeight;
				if (st>(RVS.S.winh-lastTLColHeight) && st+50<RVS.S.winh) {
					// NOTHING
				} else
					RVS.C.layerListWrap.scrollTop(RVS.C.layerListWrap.scrollTop() - (((RVS.S.winh-lastTLColHeight) + 40) - st)).RSScroll("update");
			}
			if (type==="update") {
				RVS.C.layerListWrap.RSScroll("update");
			}
		}
	};

	RVS.F.saveCollapsedGroups = function() {
		RVS.SLIDER[RVS.S.slideId].slide.runtime = RVS.SLIDER[RVS.S.slideId].slide.runtime===undefined ? {} : RVS.SLIDER[RVS.S.slideId].slide.runtime;
		RVS.SLIDER[RVS.S.slideId].slide.runtime.collapsedGroups = [];
		var temp = document.getElementsByClassName('withchildren collapsed');
		for (var i in temp) {
			if(!temp.hasOwnProperty(i)) continue;
			if (temp[i].id!==undefined) RVS.SLIDER[RVS.S.slideId].slide.runtime.collapsedGroups.push(temp[i].id);
		}
	};

	RVS.F.updateCollapsedGroups = function() {
		for (var i=0;i<=RVS.SLIDER[RVS.S.slideId].slide.runtime.collapsedGroups.length-1;i++) {
			if (RVS.SLIDER[RVS.S.slideId].slide.runtime.collapsedGroups[i]!==undefined) {
				var el = document.getElementById(RVS.SLIDER[RVS.S.slideId].slide.runtime.collapsedGroups[i]);
				if (el!=null) el.className +=" collapsed";
			}
		}

	};
/***************************************************
	- DRAW THE LAYER LIST CONTAINER ON 3 LEVEL -
/***************************************************/

	RVS.F.reDrawListElements = function() {
		if (RVS.SLIDER[RVS.S.slideId].slide.runtime!==undefined && RVS.SLIDER[RVS.S.slideId].slide.runtime.collapsedGroups!==undefined)
			RVS.F.updateCollapsedGroups();

		var layerOrder = updateLayerOrder();
		if (RVS.S.llcache[RVS.S.slideId].tlLayerList===undefined || RVS.C.layerListWrap.find('.layerListContainer').length==0)
			RVS.S.llcache[RVS.S.slideId].tlLayerList = RVS.F.cE({t:'ul',id: 'tl_layerList_'+RVS.S.slideId, cN:"layerListContainer"});
		else {
			if (RVS.S.llcache[RVS.S.slideId].tlLayerList===undefined) RVS.S.llcache[RVS.S.slideId].tlLayerList = document.getElementById('tl_layerList_'+RVS.S.slideId);
			emptyLayerListContainer(RVS.S.llcache[RVS.S.slideId].tlLayerList);
			RVS.S.llcache[RVS.S.slideId].tlLayerList.parentElement.removeChild(RVS.S.llcache[RVS.S.slideId].tlLayerList);
		}

		drawListElements({list:layerOrder, container:RVS.S.llcache[RVS.S.slideId].tlLayerList,showEmpty:true, cIdName:'tllayerlist_element'});

		RVS.C.layerListWrap[0].appendChild(RVS.S.llcache[RVS.S.slideId].tlLayerList)

		showHideNoLayer(layerOrder);
		listElementSortable({container:'#tl_layerList_'+RVS.S.slideId,cacheSub:"tlLayerList",cIdName:'tllayerlist_element'});


		setLayerListMaxHeight(true);

		RVS.F.updateCollapsedGroups();
		RVS.F.checkLockedLayers();
		RVS.F.checkShowHideLayers();

	}


	function emptyLayerListContainer() {
		disableSortableLists({container:RVS.S.llcache[RVS.S.slideId].tlLayerList.id,cIdName:'tllayerlist_element'});
		RVS.S.llcache[RVS.S.slideId].tlLayerList.innerHTML = "";
	}
	/*
	REKURSIVE FUNCTION TO DRAW LIST ELEMENTS INHERITED
	*/
	function drawListElements(obj) {
		var li;
		for (var l in obj.list) {

			if(!obj.list.hasOwnProperty(l)) continue;

			var sortelement = obj.list[l],
				withchildren = sortelement.type==="zone" || sortelement.type==="group" || sortelement.type==="row" || sortelement.type==="column";

			let _ = RVS.L[sortelement.id],
				notzone = _.type==="zone" ? "" : " not_zone_layer";

			li = RVS.F.cE({	t:'li',
							cN:(withchildren ? 'withchildren ' : '') + (_.actions!==undefined && _.actions.action.length>0 ? 'actionmarked ' : ' ')+obj.cIdName+' layerlist_element '+obj.cIdName+'_'+_.type+' '+_.type+'_'+_.uid+notzone,
							id: obj.cIdName+'_'+RVS.S.slideId+'_'+_.uid,
							ds:{puid:_.group.puid,type:_.type,id:_.uid}});

			if (withchildren && sortelement.list.length===0) li.dataset.status="empty";

			li.appendChild(createLayerListElement(RVS.L[sortelement.id],withchildren));  // Returned jQuery Object

			if (withchildren) drawListElements({list:sortelement.list,container:li.querySelector('ul'),showEmpty:false, cIdName:obj.cIdName});

			if(obj.cIdName==="tllayerlist_element" && sortelement.id!=="top" && sortelement.id!=="middle" && sortelement.id!=="bottom") RVS.F.addLayerFrames(RVS.L[sortelement.id],li);
			obj.container.appendChild(li);

		}
		if (obj.showEmpty && obj.list.length===0) {
			li = RVS.F.cE({t:'li',cN:obj.cIdName+' nolayeravailable'});
			li.innerHTML = '<div class="context_left"><div class="layerlist_element_type"><i class="material-icons">not_interested</i></div><div class="layerlist_element_alias">No Layer Available</div></div>';
			obj.container.appendChild(li);
		}


	}



	/*
	CREATE LAYER ORDER HELPER BASED ON ZINDEX
	*/
	function updateLayerOrder() {
		return innerListOrder([],-1);
	}



	function innerListOrder(list,puid) {
		list = getLayersSortedInGroup(puid);

		for (var l in list) {
			if(!list.hasOwnProperty(l)) continue;
			if (list[l].type==="zone" || list[l].type==="row" || list[l].type==="column" || list[l].type==="group")
				list[l].list = innerListOrder(list,list[l].id);
		}
		return list;
	}

	function getLayersSortedInGroup(puid) {
		var list = [],withZero,moreZeros=false;
		for (var l in RVS.L) if(RVS.L.hasOwnProperty(l) && (""+RVS.L[l].group.puid==""+puid)) list.push({id:RVS.L[l].uid,  sort:RVS.L[l].group.groupOrder , zIndex:RVS.L[l].position.zIndex, type:RVS.L[l].type, alias:RVS.L[l].alias});
		for (var i in list) if (list[i].sort==0 || list[i].sort=="0") if (withZero===undefined) withZero = i; else {list[i].sort = list[i].zIndex;moreZeros = true;}
		if (moreZeros) list[withZero].sort = list[withZero].zIndex;


		if (puid==="top" || puid==="bottom" || puid==="middle")
			list.sort(function(a,b) { return a.sort - b.sort;});
		else
		if (puid===-1 || RVS.L[puid].type==="group")
			list.sort(function(a,b) { return b.sort - a.sort;});
		else
			list.sort(function(a,b) { return a.sort - b.sort;});
		return list;
	}

	function showHideNoLayer(layerOrder) {
		if (layerOrder.length>0)
			jQuery('.layerlist_element.nolayeravailable').hide();
		else
			jQuery('.layerlist_element.nolayeravailable').show();
	}





	function createLayerListElement(_,withchildren) {
		let frag = RVS.F.cF();
		let ticon = RVS.F.getLayerIcon(_.type,_.subtype);

		let markup = RVS.F.cE({cN:'layerlist_element_innerwrap',ds:{layerid:_.uid, id:_.uid}});
		let cleft = RVS.F.cE({cN:'context_left'});
		let alias = RVS.F.cE({cN:'layerlist_element_alias', id:'layerlist_element_alias_'+RVS.S.slideId+'_'+_.uid});
		let iwrap = RVS.F.cE({cN:'layerlist_toolbar_icon_wrap',id:'llist_too_iw_'+_.uid});
		let send = RVS.F.cI({cN:'layerlist_toolbar_icon fclayer_withslideend',c:'keyboard_tab', id:'slideendmarker_'+RVS.S.slideId+'_'+_.uid, ds:{layerid:_.uid}});
		let stime = RVS.F.cE({cN:'stimeline'});
		let frameswrap = RVS.F.cE({cN:'frameswrap'});
		let fus = RVS.F.cE({cN:'frame_unvisible_start', id:'frame_unvisible_start_'+RVS.S.slideId+'_'+_.uid});
		let fbg = RVS.F.cE({t:'framebg',ds:{layerid:_.uid, bg:'true', layertype:_.type}});

		alias.textContent = RVS.F.sanitize_input_ws(_.alias);

		markup.appendChild(cleft);
		cleft.appendChild(RVS.F.cE({cN:'layer_has_action'}));
		cleft.appendChild(RVS.F.cE({cN:'layerlist_element_level',icon:{c:'arrow_drop_down'}}));
		cleft.appendChild(RVS.F.cE({cN:'layerlist_element_type',icon:{c:ticon}}));
		cleft.appendChild(alias);
		if (_.type!=="zone") {
			let inp = RVS.F.cE({t:'input', type:'text', id:'layerlist_element_alias_input_'+RVS.S.slideId+'_'+_.uid, cN:'layerlist_element_alias_input indeplayerinput losefocusonenter',ds:{evt:'updateLayerAlias',sanitize:'true',cursortoclick:'true',evtparam:_.uid,r:_.uid+'.alias'}});
			inp.value=RVS.F.sanitize_alias(_.alias);
			cleft.appendChild(inp);
		}

		cleft.appendChild(RVS.F.cE({cN:'layerlist_toolbar'}));
		cleft.appendChild(RVS.F.cI({cN:'layer_current_visibility',c:'visibility_off',ds:{uid:_.uid}}));
		cleft.appendChild(RVS.F.cI({cN:'layer_current_locked material-icons',c:'lock',ds:{uid:_.uid}}));

		for (var i in RVS.JHOOKS.createLayerListElement) {
			if(!RVS.JHOOKS.createLayerListElement.hasOwnProperty(i)) continue;
			let el = RVS.JHOOKS.createLayerListElement[i]({layer:_});
			if (el!==false && el!==null && el!==undefined) cleft.appendChild(el);
		}

		cleft.appendChild(iwrap);
		iwrap.appendChild(send);

		markup.appendChild(stime);
		stime.appendChild(RVS.F.cE({cN:'slidelooptimemarker'}));
		stime.appendChild(RVS.F.cE({cN:'fixedscrolltimemarker'}));
		stime.appendChild(frameswrap);
		frameswrap.appendChild(fus)
		frameswrap.appendChild(fbg);
		frag.appendChild(markup);
		if (withchildren) frag.appendChild(RVS.F.cE({t:'ul'}));

		return frag;
	}



/******************************************************
	- CUSTOM SORTING OF LAYER ELEMENTS IN LIST -
******************************************************/

	function wasCollapsed() {
		if (jQuery(this).hasClass("collapsed"))
			this.dataset.wascollapsed = 1;
		else
			jQuery(this).addClass('collapsed');
	}

	function resetWasCollapsed() {
		if (this.dataset.wascollapsed!=1)
			jQuery(this).removeClass('collapsed');
		this.dataset.wascollapsed = 0;
	}
	function disableSortableLists(obj) {
		jQuery(obj.container+' .'+obj.cIdName).each(function() {
			if (jQuery(this).data('uiDraggable'))
				jQuery(this).draggable("destroy");
		});
	}

	function updateListElementPositionsArray(obj) {
		listElementPosArray = [];
		// CALCULATE THE POSITIONS OF THE LIST ELEMENTS
		jQuery(obj.container+' .'+obj.cIdName).each(function() {
			if (this.offsetParent!==null )
				listElementPosArray.push({dif:36, y:jQuery(this).offset().top, id:this.dataset.id, type:this.dataset.type, puid:this.dataset.puid});

		});
	}

	function listElementSortable(obj) {
		var contHeight,
			contY,
			ps,
			scTimer,
			eY = 0;
		jQuery(obj.container+' .'+obj.cIdName).draggable({
			helper:'clone',
			opacity:0.5,
			axis:"y",

			start:function(event,ui) {
				if (this.dataset.type=="zone") {
					jQuery(this).draggable("disable");
					return;
				}
				if (this.dataset.type==="row") RVS.TL.TL.addClass("layer_in_drag");
				var jsub = jQuery(RVS.S.llcache[RVS.S.slideId][obj.cacheSub]);
				ui.helper.width(jsub.width());
				ps  = jQuery(obj.container).closest('.ps');
				contHeight = ps.height();
				// COLLAPSE COLUMNS BEFORE MOVE THEM
				if (this.dataset.type === 'column') jQuery('#'+obj.cIdName+'_'+RVS.S.slideId+'_'+this.dataset.puid).find('.'+obj.cIdName+'_column').each(wasCollapsed);
				// COLLAPSE ROWS BEFORE MOVE THEM
				if (this.dataset.type === 'row') jQuery('.'+obj.cIdName+'_row').each(wasCollapsed);

				if (ps.length>0 && ps.hasClass("ps--active-y")) RVS.F.layerListScrollable("update");
				setTimeout(function() {updateListElementPositionsArray(obj);},50);
				scTimer = setInterval(function() {
					if (ps.length>0 && ps.hasClass("ps--active-y")) {
						contY = jsub.offset().top;
						if (eY<(contY+ps[0].scrollTop+20) && ps[0].scrollTop>0) {
							ps[0].scrollTop--;
							updateListElementPositionsArray(obj);
						} else
						if (eY>(parseInt(contY,0)+parseInt(contHeight,0)+ps[0].scrollTop-20) && ps[0].scrollTop<contHeight) {
							ps[0].scrollTop++;
							updateListElementPositionsArray(obj);
						}

					}


				},5);
			},

			drag:function(event,ui) {
				if (this.dataset.type=="zone") return;
				var res = getItemUnder({y:event.pageY});
				eY = event.pageY;
				if (res!==false) {
					jQuery('.'+obj.cIdName).removeClass("beforeitemdrop").removeClass("afteritemdrop").removeClass("incolumntopdrop").removeClass("incolumndrop").removeClass("afterzonedrop");
					RVS.S.llcache[RVS.S.slideId][obj.cacheSub].classList.remove("sortToTheEnd");
					if (event.pageY<listElementPosArray[1].y || res.found===true) {
						var classAddition = "",
							hs = 18;

							res.cover = res.cover<-18 ? 0 : res.cover;
						switch (this.dataset.type) {
							case "row":
								if (res.puid===-1) jQuery('#'+obj.cIdName+'_'+RVS.S.slideId+'_'+res.id).addClass("incolumndrop");

								if (res.puid === "top" || res.puid==="bottom" || res.puid==="middle") classAddition = res.cover<hs ? "beforeitemdrop" : "afteritemdrop";
								if (res.id === "top" || res.id==="bottom" || res.id==="middle") classAddition = res.cover<hs && res.id!=="top" ? "beforeitemdrop" : "afteritemdrop";
							break;
							case "group":
								classAddition = res.cover<hs ? 	"beforeitemdrop" : 	"afteritemdrop";
							break;
							case "column":
								jQuery('#'+obj.cIdName+'_'+RVS.S.slideId+'_'+this.dataset.puid).addClass("incolumndrop");
								if (res.puid === this.dataset.puid) classAddition = res.cover<hs ? 	"beforeitemdrop" : 	"afteritemdrop";
							break;
							default:
								if (res.type!=="zone" && res.type!=="row") {
									if (res.puid!==-1 && (res.type!=="zone" && res.type!=="column" && res.type!=="row" && res.type!=="group")) jQuery('#'+obj.cIdName+'_'+RVS.S.slideId+'_'+res.puid).addClass("incolumndrop");

									classAddition = res.cover<hs ? res.type!=="column" ? "beforeitemdrop" : "afteritemdrop incolumntopdrop" :
												res.type!=="row" && res.type!=="column" && res.type!=="group" ? "afteritemdrop" :
												res.type==="column"  || res.type==="group" ? "incolumntopdrop" : classAddition;
								} else {
									if (res.cover<hs && res.type==="zone" && res.id==="top") classAddition = "beforeitemdrop";
									if (res.type==="zone" && res.id==="bottom") classAddition = "afterzonedrop";
								}

							break;
						}
						jQuery('#'+obj.cIdName+'_'+RVS.S.slideId+'_'+res.id).addClass(classAddition);
					} else {
						//if (this.dataset.type!=="column") RVS.S.llcache[RVS.S.slideId][obj.cacheSub].addClass("sortToTheEnd");
					}
				}
			},
			stop:function(event,ui) {
				if (this.dataset.type=="zone") return;
				RVS.TL.TL.removeClass("layer_in_drag");
				clearInterval(scTimer);
				jQuery('.'+obj.cIdName).removeClass("beforeitemdrop").removeClass("afteritemdrop").removeClass("incolumntopdrop").removeClass("incolumndrop");
				RVS.S.llcache[RVS.S.slideId][obj.cacheSub].classList.remove("sortToTheEnd");

				// UNCOLLAPSE COLUMNS AFTER DRAG
				if (this.dataset.type === 'column') jQuery('#'+obj.cIdName+'_'+RVS.S.slideId+'_'+this.dataset.puid).find('.'+obj.cIdName+'_column').each(resetWasCollapsed);

				// UNCOLLAPSE ROWS AFTER DRAG
				if (this.dataset.type === 'row') jQuery('.'+obj.cIdName+'_row').each(resetWasCollapsed);

				var res = getItemUnder({y:event.pageY}),
					target;

				if (res!==false) {
					if (event.pageY<listElementPosArray[1].y || res.found===true) {
						target ="";
						var hs = 18;
						switch (this.dataset.type) {
							case "row":
								if (res.puid === "top" || res.puid==="bottom" || res.puid==="middle") target = res.cover<hs ? "before" : "after";
								if (res.cover>=hs && (res.id==="top" || res.id==="bottom" || res.id==="middle")) target="zone";
								if (res.cover<hs && res.id==="middle") { res.id="top"; target="zonebottom";}
								if (res.cover<hs && res.id==="bottom") { res.id="middle"; target="zonebottom";}
							break;
							case "group":
								target = res.cover<hs ? "before" : "after";
							break;
							case "column":
								if (res.puid === this.dataset.puid) target = res.cover<hs ? "before" : "after";
							break;
							default:
								target = (res.cover<hs && res.type!=="column" && res.type!=="row" && (res.type!=="zone" || res.id==="top")) ? "before" :
										 (res.cover<hs && res.type==="column") ? "column" :
										 (res.cover>(hs-1) && (res.type!=="row" && res.type!=="column" && res.type!=="group" && res.type!=="zone")) ? "after" :
										 (res.cover>(hs-1) && res.type==="column") ? "column" :
										 (res.cover>(hs-1) && res.type==="group" && res.puid==-1) ? "group" :
										 ((res.puid==-1 || res.puid==="bottom") && res.type==="zone") ? "after" : target;
							break;
						}
					}  else {

						switch (this.dataset.type) {
							case "row":	break;
							case "column":break;
							default:
								target = "after";
								res.id = lastElementInList(listElementPosArray).id;
							break;
						}
					}
					if (target!=="") RVS.F.sortLayer({layer:this.dataset.id, target:target, env:res.id});
				}
			},
			revert:"true"
		});
	}
	// GET THE LIST ITEM UNDER THE HOVERING MOUSE
	function getItemUnder(obj) {
		var found = false, i = 0;
		if (listElementPosArray!==undefined && listElementPosArray.length>0) {
			while (!found && i<listElementPosArray.length) {
				if (obj.y>=listElementPosArray[i].y && obj.y<parseInt(listElementPosArray[i].y,0)+parseInt(listElementPosArray[i].dif,0))
					found = true;
				else
					i++;
			}
			if (i>=listElementPosArray.length) i = listElementPosArray.length-1;
			return {found:found, id:listElementPosArray[i].id, puid:listElementPosArray[i].puid, y:listElementPosArray[i].y, cover:obj.y - parseInt(listElementPosArray[i].y,0), type:listElementPosArray[i].type};
		} else
		return false;
	}

	function lastElementInList(list) {
		var le, pos=0;
		for (var el in list) {
			if(!list.hasOwnProperty(el)) continue;
			if (list[el].y >= pos) {
				le = list[el];
				pos =  list[el].y;
			}
		}
		return le;
	}

	//SORT LAYER INTO OTHER POSITION AFTER DROP/DRAG
	RVS.F.sortLayerStepOne = function(obj) {
		if (obj.env!==obj.layer) {
				var pre = '#tllayerlist_element_'+RVS.S.slideId+'_',
					el = jQuery(pre+obj.layer);
			switch (obj.target) {
				case "after":
					if (RVS.L[obj.layer].type=="group" && RVS.L[obj.env].group.puid!==-1) obj.env = RVS.L[obj.env].group.puid;
					if (RVS.L[obj.layer].type!=="row" && RVS.L[obj.env].type==="row")
						el.appendTo('#tl_layerList_'+RVS.S.slideId);
					else
						el.insertAfter(pre+obj.env);
				break;
				case "before":
					if (RVS.L[obj.layer].type=="group" && RVS.L[obj.env].group.puid!==-1) obj.env = RVS.L[obj.env].group.puid;
					if (RVS.L[obj.layer].type!=="row" && RVS.L[obj.env].type==="row")
						el.appendTo('#tl_layerList_'+RVS.S.slideId);
					else
						el.insertBefore(pre+obj.env);
				break;
				case "zone":
					if (RVS.L[obj.env].group.puid!==obj.env)
						el.prependTo(pre+obj.env+'>ul');
				break;
				case "zonebottom":
					if (RVS.L[obj.layer].type=="row") {
						if (RVS.L[obj.env].group.puid!==obj.env) el.appendTo(pre+obj.env+'>ul');
					} else {
						el.appendTo('#tl_layerList_'+RVS.S.slideId);
					}
				break;
				case "group":
					el.prependTo(pre+obj.env+' ul');
				break;
				case "column":
					el.prependTo(pre+obj.env+' ul');
				break;
				case "columnend":
					el.appendTo(pre+obj.env+' ul');
				break;
				case "veryend":
					if (RVS.L[obj.layer].type=="row")
						el.appendTo(pre+"bottom"+'>ul');
					else
						el.appendTo('#tl_layerList_'+RVS.S.slideId);
				break;
			}
		}
	};

	RVS.F.sortLayerStepTwo = function(obj) {
		if (obj.dropto==="column") {
				RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+obj.layer+'.position.x.#size#.v',val:0});
				RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+obj.layer+'.position.y.#size#.v',val:0});
				RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+obj.layer+'.position.horizontal.#size#.v',val:"left"});
				RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+obj.layer+'.position.vertical.#size#.v',val:"top"});
			}
		if (obj.resetPosition!==undefined && (obj.dropto==="group" || obj.dropto==="root")) {
			var X = obj.resetPosition.x - RVS.H[obj.layer].w_offsetcache.horizontal,
				Y = obj.resetPosition.y - RVS.H[obj.layer].w_offsetcache.vertical;
			RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+obj.layer+'.position.x.#size#.v',val:X});
			RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+obj.layer+'.position.y.#size#.v',val:Y});
			RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+obj.layer+'.position.horizontal.#size#.v',val:"left"});
			RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+obj.layer+'.position.vertical.#size#.v',val:"top"});
		}
	};

	// SORT MORE THEN 1 LAYER INTO OTHER POSITIONS
	RVS.F.sortAllSelectedLayers = function(_) {
		var obj = RVS.F.safeExtend({},_),
			m = RVS.selLayers.length-1,
			si;

		for (si in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(si)) continue;
			obj.layer = RVS.selLayers[m-si];
			if (RVS.L[obj.layer].type!=="row" && RVS.L[obj.layer].type!=="column" && RVS.L[obj.layer].type!=="group")
				RVS.F.sortLayerStepOne(obj);
		}

		for (si in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(si)) continue;
			obj.layer = RVS.selLayers[m-si];
			obj.positionoffset = si;
			if (RVS.L[obj.layer].type!=="row" && RVS.L[obj.layer].type!=="column" && RVS.L[obj.layer].type!=="group")
				RVS.F.sortLayerStepTwo(obj);
		}
		updatePUIDs();
		RVS.F.updateZIndexTable();

		RVS.F.reOrderHTMLLayers();

	};

	// SORT 1 SINGLE LAYER INTO A NEW POSITION
	RVS.F.sortLayer = function(obj) {
		RVS.F.sortLayerStepOne(obj);
		setTimeout(function() {
			RVS.F.openBackupGroup({id:"layersorting",txt:"Layer Sorting",icon:"sort_by_alpha"});
			RVS.F.sortLayerStepTwo(obj);
			updatePUIDs();
			RVS.F.updateZIndexTable();
			RVS.F.closeBackupGroup({id:"layersorting"});
			RVS.F.reOrderHTMLLayers();
			if (obj.redraw)
				RVS.F.updateSelectedHtmlLayers(true);
			RVS.F.updateEasyInputs({container:jQuery('.layer_settings_collector'), path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
		},50);
	};


	// UPDATE zINDEX and GROUP Index Functions
	function updatePUIDs() {
		var els = jQuery('#tl_layerList_'+RVS.S.slideId+' .layerlist_element');

		for (var el in els) {
			if(!els.hasOwnProperty(el)) continue;
			if (els[el] !== undefined && els[el].dataset!==undefined && els[el].dataset.id!==undefined)	{
				var p = jQuery(els[el]).parent().closest('.withchildren');
				if (p.length>0) {
					els[el].dataset.puid =  p.data('id');

					var oldpuid = RVS.F.getDeepVal({path:RVS.S.slideId+'.layers.'+els[el].dataset.id+'.group.puid'});
					RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+els[el].dataset.id+'.group.puid',val:els[el].dataset.puid});

					//IF ELEMET ADDED TO A NEW GROUP, COLUMN, ROW !!
					if (oldpuid != els[el].dataset.puid && jQuery.inArray(els[el].dataset.puid,["top","bottom","middle"])==-1) {
						var _children = RVS.F.getLayerChildren({layerid:els[el].dataset.puid});
						RVS.F.setChildrenTimelines({childLayers:_children, difference: 0});
						RVS.F.backupChildren({childLayers:_children});
					}
				}
				else {
					els[el].dataset.puid = -1;
					RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+els[el].dataset.id+'.group.puid',val:-1});
				}
			}
		}

	}

	RVS.F.updateZIndexTable = function() {
		var els = jQuery('#tl_layerList_'+RVS.S.slideId+' .layerlist_element'),
			max = els.length+5,
			min = 0,
			cur = 0,
			onroot = 0,
			ind = 0,
			groups = {};
		for (var el in els) {
			if(!els.hasOwnProperty(el)) continue;
			if (els[el] !== undefined && els[el].dataset!==undefined && els[el].dataset.id!==undefined)	{
				ind++;
				var c_puid = RVS.L[els[el].dataset.id].group.puid;

				if (c_puid===-1 || RVS.L[c_puid].type==="group")
					max--;
				else
					min++;
				cur = (c_puid===-1 || RVS.L[c_puid].type==="group") ? max : min;

				groups[c_puid] = groups[c_puid]===undefined ? 0 : groups[c_puid];

				RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+els[el].dataset.id+'.position.zIndex',val:cur});

				if (c_puid!==-1 && RVS.L[c_puid].type!=="group") {
					RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+els[el].dataset.id+'.group.groupOrder',val:groups[c_puid]});
					groups[c_puid]++;
 				} else {
 					onroot++;
 					RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+els[el].dataset.id+'.group.groupOrder',val:cur});
 				}
 				//ZONE zINDEX NEED TO BE SET STRAIGHT
				if (RVS.L[els[el].dataset.id].type!=="zone") tpGS.gsap.set(RVS.H[els[el].dataset.id].w,{zIndex:cur});

			}
		}
		RVS.F.updateZoneZIndexes();

	};

})();
