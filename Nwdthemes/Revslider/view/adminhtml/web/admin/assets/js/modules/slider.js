/*!
 * REVOLUTION 6.0.0 EDITOR SLIDER JS
 * @version: 1.0 (01.07.2019)
 * @author ThemePunch
*/

RVS.S.ulDIM = {width:0,height:0};


(function() {


	if (tpGS.SFXBounceLite===undefined) tpGS.SFXBounceLite = tpGS.CustomBounce.create("SFXBounceLite", { strength:0.3 ,squash:1, squashID:"SFXBounceLite-squash"});
	if (tpGS.SFXBounceSolid===undefined) tpGS.SFXBounceSolid = tpGS.CustomBounce.create("SFXBounceSolid", { strength:0.5,squash:2,squashID:"SFXBounceSolid-squash"});
	if (tpGS.SFXBounceStrong===undefined) tpGS.SFXBounceStrong = tpGS.CustomBounce.create("SFXBounceStrong", { strength:0.7,squash:3,squashID:"SFXBounceStrong-squash"});
	if (tpGS.SFXBounceExtrem===undefined) tpGS.SFXBounceExtrem = tpGS.CustomBounce.create("SFXBounceExtrem", { strength:0.9,squash:4,squashID:"SFXBounceExtrem-squash"});
	if (tpGS.BounceLite===undefined) tpGS.BounceLite = tpGS.CustomBounce.create("BounceLite", { strength:0.3 });
	if (tpGS.BounceSolid===undefined) tpGS.BounceSolid = tpGS.CustomBounce.create("BounceSolid", { strength:0.5});
	if (tpGS.BounceStrong===undefined) tpGS.BounceStrong = tpGS.CustomBounce.create("BounceStrong", { strength:0.7});
	if (tpGS.BounceExtrem===undefined) tpGS.BounceExtrem = tpGS.CustomBounce.create("BounceExtrem", { strength:0.9});


	/* SHORTCUTS FOR JQUERY OBJECTS */
	var _lg,_ib,_rto,_rlo,_rulerHM,_rulerVM,
	_rvbin,lgHeight_cache,inf_s_height,inf_s_width,lastLayerGridHeight,minLayerGridHeight,sticky_settings,streamSrcUpdNeed,rTBScroll,

	/* DIMENSION SHORTCUTS */
	_ibDIM = {width:0,height:0},
	_rvbiDIM = {width:0,height:0},
	_builderOffset = {left:0,top:0},
	_builderScroll = {x:0,y:0};

	RVS.S.rulerOffset = {x:0,y:0};

	/* _builderScrollDIM = {width:0,height:0}; */

	/*
	SET THE SLIDER SETTINGS
	*/
	RVS.F.setSlider = function(obj) {
		obj = obj===undefined || obj.length==0 ? {} : obj;
		if (typeof _rmig_ !=="undefined") obj = _rmig_.migrateSlider(obj);
		return(RVS.F.safeExtend(true,getNewSliderObject({}),getNewSliderObject(obj)));
	};

	/*
	INIT SLIDER AND ITS LISTENERS, SETTINGS
	*/
	RVS.F.initSliderBuilder = function() {

		RVS.C.rb = jQuery('#rev_builder');
		RVS.C.tRC = document.getElementById('the_right_toolbar');

		RVS.C.UL = _ul = jQuery('#rev_slider_ul');
	    _lg = jQuery('#layer_grid');
	    _ib = jQuery('#rev_slider_inbuild');
	    _rto = jQuery('#ruler_top_offset');
	    _rlo = jQuery('#ruler_left_offset');
	    _rulerHM = jQuery('#ruler_hor_marker');
	    _rulerVM = jQuery('#ruler_ver_marker');
	    _rvbin = jQuery('#rev_builder_inner');


	    RVS.F.updateEasyInputs({container:jQuery('#rs-layout-type'),  trigger:"init"});


	    initGlobalSkins();
		initLocalInputBoxes();
		initLocalListeners();
		RVS.F.buildRuler();

		RVS.F.sliderUpdateAllFields();
		RVS.DOC.trigger('updateShortCode');

		//initResizeables();
		revsliderScrollable();

		inf_s_height = document.getElementById('show_c_height');
		inf_s_width = document.getElementById('show_c_width');
		if (inf_s_width!==undefined) inf_s_width.innerHTML = Math.round(RVS.F.GW(RVS.screen))+"px";


		// PRELOAD STREAM DEPENDENCIES
		loadStreamDependencies();
		RVS.F.checkSliderSource();

		//Upate Global Sizes
		for (var i in RVS.V.sizesold) if (RVS.V.sizesold.hasOwnProperty(i)) document.getElementById('global_size_'+RVS.V.sizesold[i]).innerHTML = i==0 ? "> "+RVS.ENV.glb_slizes[RVS.V.sizes[1]] :
				i==3 ? "< "+RVS.ENV.glb_slizes[RVS.V.sizes[i]] : parseInt(RVS.ENV.glb_slizes[RVS.V.sizes[i]],0)-1+"px - " + RVS.ENV.glb_slizes[RVS.V.sizes[parseInt(i,0)+1]];

	};



	RVS.F.clearSnapVisual = function() {
		RVS.C.gcanvas = RVS.C.gcanvas===undefined ? document.getElementById('gridcanvas') : RVS.C.gcanvas;
		RVS.C.gCTX = RVS.C.gCTX===undefined ? RVS.C.gcanvas.getContext("2d") : RVS.C.gCTX;
		RVS.C.gcanvas.width=RVS.S.ulDIM.width;
		RVS.C.gcanvas.height=RVS.S.ulDIM.height;
		RVS.C.gCTX.clearRect(0,0,RVS.S.ulDIM.width,RVS.S.ulDIM.height);

	};

	RVS.F.getSnapPoint = function(x,y) {
		return {x: RVS.SLIDER.settings.snap.gap * Math.round(x / RVS.SLIDER.settings.snap.gap),
				y: RVS.SLIDER.settings.snap.gap * Math.round(y / RVS.SLIDER.settings.snap.gap)}
	}



	RVS.F.snapVisual = function(_) {
		_ = _ ===undefined ? {} : _;

		_.ah= _.ah===undefined ? RVS.selLayers[0]!==undefined ? RVS.L[RVS.selLayers[0]].position.horizontal[RVS.screen].v : "left" : _.ah;
		_.av= _.av===undefined ? RVS.selLayers[0]!==undefined ? RVS.L[RVS.selLayers[0]].position.vertical[RVS.screen].v : "top" : _.av;


		var RX = RVS.S.rulerOffset.x,
			RY = RVS.S.rulerOffset.y/RVS.zoom;

		RVS.F.clearSnapVisual();

		RVS.SLIDER.settings.snap.gap = parseInt(RVS.SLIDER.settings.snap.gap,0);
		RVS.SLIDER.settings.snap.gap= RVS.SLIDER.settings.snap.gap===0 ? 1 : RVS.SLIDER.settings.snap.gap;
		// GRID SNAPPING
		if (RVS.SLIDER.settings.snap.adjust==="grid" && RVS.SLIDER.settings.snap.gap>4) {
			RVS.GSNAP = {X : _.ah==="left" ? RX  : _.ah==="right" ? (RX + RVS.S.lgw) : (RX + RVS.S.lgw/2),
						 Y : _.av==="top" ? RY  : _.av==="bottom" ? (RY + RVS.S.lgh) : (RY + RVS.S.lgh/2)}

			RVS.GSNAP.XO = RVS.SLIDER.settings.snap.gap*Math.ceil(RVS.GSNAP.X/RVS.SLIDER.settings.snap.gap);
			RVS.GSNAP.YO = RVS.SLIDER.settings.snap.gap*Math.ceil(RVS.GSNAP.Y/RVS.SLIDER.settings.snap.gap);

			//DRAW THE GRID ALSO
			RVS.C.gCTX.beginPath();

			for (var x=(RVS.GSNAP.X-RVS.GSNAP.XO);x<RVS.S.ulDIM.width;x+=RVS.SLIDER.settings.snap.gap)	{
				RVS.C.gCTX.moveTo(x,0);
				RVS.C.gCTX.lineTo(x,RVS.S.ulDIM.height);
			}

			for (var y=(RVS.GSNAP.Y-RVS.GSNAP.YO);y<RVS.S.ulDIM.height;y+=RVS.SLIDER.settings.snap.gap) {
				RVS.C.gCTX.moveTo(0,y);
				RVS.C.gCTX.lineTo(RVS.S.ulDIM.width,y);
			}
			RVS.C.gCTX.strokeStyle="rgba(250, 63, 142, 0.25)";
			RVS.C.gCTX.stroke();


			if (_.sp!==undefined) {

				RVS.C.gCTX.beginPath();

				_.sp.x = RVS.GSNAP.X + (_.ah==="left" ? _.sp.x : _.ah==="right" ? 0-_.sp.x - RVS.SLIDER.settings.snap.gap : _.sp.x - RVS.SLIDER.settings.snap.gap/2);
				_.sp.y = RVS.GSNAP.Y + (_.av==="top" ? _.sp.y : _.av==="bottom" ? 0-_.sp.y - RVS.SLIDER.settings.snap.gap : _.sp.y - RVS.SLIDER.settings.snap.gap/2);

				let spyt = _.sp.y + (_.av!=="top" ? RVS.SLIDER.settings.snap.gap / (_.av==="middle" ? 2 : 1) : 0),
					spxt = _.sp.x + (_.ah!=="left" ? RVS.SLIDER.settings.snap.gap / (_.ah==="center" ? 2 : 1) : 0);

				RVS.C.gCTX.moveTo(_.sp.x, spyt);
				RVS.C.gCTX.lineTo(_.sp.x + RVS.SLIDER.settings.snap.gap,spyt);

				RVS.C.gCTX.moveTo(spxt,_.sp.y);
				RVS.C.gCTX.lineTo(spxt,_.sp.y + RVS.SLIDER.settings.snap.gap);

				RVS.C.gCTX.lineWidth = 2;
				RVS.C.gCTX.strokeStyle="rgba(250, 63, 142, 1)";
				RVS.C.gCTX.stroke();
			}
		} else

		if (RVS.SLIDER.settings.snap.adjust==="layers") {
			RVS.C.gCTX.beginPath();
			var dx = {};
			for (var i in RVS.S.DaD.snapH) {
				if (!RVS.S.DaD.snapH.hasOwnProperty(i)) continue;
				let c = RVS.S.DaD.snapH[i];
				if (dx[c.x]===undefined) {
					RVS.C.gCTX.moveTo(c.x + RX, 0);
					RVS.C.gCTX.lineTo(c.x + RX, RVS.S.ulDIM.height);
					dx[c.x] = true;
				}

				if (dx[(c.xc)]===undefined) {
					RVS.C.gCTX.moveTo(c.xc + RX, 0);
					RVS.C.gCTX.lineTo(c.xc + RX, RVS.S.ulDIM.height);
					dx[(c.xc)]=true;
				}

				if (dx[(c.xr)]===undefined) {
					RVS.C.gCTX.moveTo(c.xr + RX, 0);
					RVS.C.gCTX.lineTo(c.xr + RX, RVS.S.ulDIM.height);
					dx[(c.xr)]=true;
				}

				if (RVS.SLIDER.settings.snap.gap>5) {
					if (dx[(c.x - RVS.SLIDER.settings.snap.gap)]===undefined) {
						RVS.C.gCTX.moveTo(c.x - RVS.SLIDER.settings.snap.gap + RX, 0);
						RVS.C.gCTX.lineTo(c.x - RVS.SLIDER.settings.snap.gap + RX, RVS.S.ulDIM.height);
						dx[(c.x - RVS.SLIDER.settings.snap.gap)] = true;
					}
					if (dx[(c.xr + RVS.SLIDER.settings.snap.gap)]===undefined) {
						RVS.C.gCTX.moveTo(c.xr + RVS.SLIDER.settings.snap.gap + RX, 0);
						RVS.C.gCTX.lineTo(c.xr + RVS.SLIDER.settings.snap.gap + RX, RVS.S.ulDIM.height);
						dx[(c.xr + RVS.SLIDER.settings.snap.gap)] = true;
					}
				}

			}
			dx= {};
			for (var i in RVS.S.DaD.snapV) {
				if (!RVS.S.DaD.snapV.hasOwnProperty(i)) continue;
				let c = RVS.S.DaD.snapV[i];
				if (dx[c.y]===undefined) {
					RVS.C.gCTX.moveTo(0,c.y + RY);
					RVS.C.gCTX.lineTo(RVS.S.ulDIM.width, c.y + RY);
					dx[c.y] = true;
				}
				if (dx[c.ym]===undefined) {
					RVS.C.gCTX.moveTo(0,c.ym + RY);
					RVS.C.gCTX.lineTo(RVS.S.ulDIM.width,c.ym + RY);
					dx[c.ym] = true;
				}
				if (dx[c.yb]===undefined) {
					RVS.C.gCTX.moveTo(0,c.yb + RY);
					RVS.C.gCTX.lineTo(RVS.S.ulDIM.width, c.yb + RY);
					dx[c.yb] = true;
				}

				if (RVS.SLIDER.settings.snap.gap>5) {
					if (dx[(c.y-RVS.SLIDER.settings.snap.gap)]===undefined) {
						RVS.C.gCTX.moveTo(0,c.y + RY - RVS.SLIDER.settings.snap.gap);
						RVS.C.gCTX.lineTo(RVS.S.ulDIM.width, c.y + RY - RVS.SLIDER.settings.snap.gap);
						dx[(c.y-RVS.SLIDER.settings.snap.gap)] = true;
					}
					if (dx[(c.yb+RVS.SLIDER.settings.snap.gap)]===undefined) {
						RVS.C.gCTX.moveTo(0,c.yb + RY + RVS.SLIDER.settings.snap.gap);
						RVS.C.gCTX.lineTo(RVS.S.ulDIM.width, c.yb + RY + RVS.SLIDER.settings.snap.gap);
						dx[(c.yb+RVS.SLIDER.settings.snap.gap)] = true;
					}
				}
			}


			RVS.C.gCTX.strokeStyle="rgba(250, 63, 142, 0.25)";
			RVS.C.gCTX.stroke();
			RVS.C.gCTX.beginPath();

			//DRAW THE NEXT ITEM WHICH IS AVAILABLE
			if (RVS.S.DaD.snapHF!==undefined && RVS.S.DaD.snapHF.uid!==-1) {
				let c = RVS.S.DaD.snapH[RVS.S.DaD.snapHF.uid];
				RVS.C.gCTX.moveTo(c.x + RX + RVS.S.DaD.snapHF.offset, 0);
				RVS.C.gCTX.lineTo(c.x + RX + RVS.S.DaD.snapHF.offset, RVS.S.ulDIM.height);
				RVS.C.gCTX.lineWidth = 2;
				RVS.C.gCTX.strokeStyle="rgba(250, 63, 142, 1)";
				RVS.C.gCTX.stroke();
			}

			if (RVS.S.DaD.snapVF!==undefined && RVS.S.DaD.snapVF.uid!==-1) {
				let c = RVS.S.DaD.snapV[RVS.S.DaD.snapVF.uid];
				RVS.C.gCTX.moveTo(0,c.y + RY + RVS.S.DaD.snapVF.offset);
				RVS.C.gCTX.lineTo(RVS.S.ulDIM.width,c.y + RVS.S.rulerOffset.y + RVS.S.DaD.snapVF.offset);
				RVS.C.gCTX.lineWidth = 2;
				RVS.C.gCTX.strokeStyle="rgba(250, 63, 142, 1)";
				RVS.C.gCTX.stroke();
			}
		}

	}



	RVS.F.updateAvailableDevices = function() {
		var //amount = 0,
			chgtodesktop = false;
		for (var i=1;i<4;i++) {
			if (!RVS.SLIDER.settings.size.custom[RVS.V.sizes[i]]) {
				jQuery('#screen_selecotr_ss_'+RVS.V.sizes[i]).addClass("ssnotavailable")
				if (RVS.screen==RVS.V.sizes[i]) chgtodesktop = true;
			} else {
				jQuery('#screen_selecotr_ss_'+RVS.V.sizes[i]).removeClass("ssnotavailable");
			}
		}
		/*if (amount===3)
			jQuery('#main_screenselector').hide();
		else
			jQuery('#main_screenselector').show();*/

		if (chgtodesktop) jQuery('#screen_selecotr_ss_d').trigger("click");

		RVS.DOC.trigger("updateAllInheritedSize");

	};

	RVS.F.checkForFixedScroll = function() {
		if (RVS.eMode.top==="slider" && RVS.eMode.menu=="#form_module_scroll" && jQuery('#timeline_slider_tab').hasClass("selected"))	{
			RVS.TL.TL.addClass('fixedscrolledit');
			RVS.TL.FixedScrollEdit = true;
		} else
		if (RVS.TL.FixedScrollEdit) {
			RVS.TL.TL.removeClass('fixedscrolledit');
			RVS.TL.FixedScrollEdit = false;
		}
	};

	RVS.F.updateDeviceOnOffBtns = function(ignore) {
		for (var i in RVS.V.sizes) if (RVS.V.sizes.hasOwnProperty(i)) {
			if (RVS.V.sizes[i]!=="d") {
                var ja =  document.getElementById('sr_custom_'+RVS.V.sizes[i]),
                    jb = document.getElementById('sr_custom_'+RVS.V.sizes[i]+'_opt');
                ja.checked = RVS.SLIDER.settings.size.custom[RVS.V.sizes[i]];
                jb.checked = RVS.SLIDER.settings.size.custom[RVS.V.sizes[i]];
                if (!ignore) {
					RVS.F.turnOnOffVisUpdate({input:ja});
					RVS.F.turnOnOffVisUpdate({input:jb});
				}
			}
        }
    }

    RVS.F.updateSliderInputFields = function() {
        if (RVS.S.sliderInputFieldsInitialised!==true && RVS.S.sliderInputFieldsInitialisedWarning!==true) {
            RVS.F.showWaitAMinute({fadeIn:0,text:RVS_LANG.updatingfields});
            RVS.S.sliderInputFieldsInitialisedWarning=true;
            RVS.S.sliderInputFieldsInitialised = true;

		}
        setTimeout(function() {
            //Initialise and Update Input Fields
            RVS.F.updateEasyInputs({container:jQuery('.sliderconfig_forms'),  trigger:"init"});
            RVS.F.updateEasyInputs({container:jQuery('#screen_selector_top_list'),path:"settings."});
            RVS.F.updateEasyInputs({container:jQuery('#rbm_colorskins'),path:"settings."});
            if (RVS.S.sliderInputFieldsInitialisedWarning===true) {
                RVS.F.showWaitAMinute({fadeOut:2,text:RVS_LANG.updatingfields});
                RVS.S.sliderInputFieldsInitialisedWarning=false;
            }

        },5);
    }

    RVS.F.updateTopScreenSelectors = function() {
        RVS.F.updateEasyInputs({container:jQuery('#screen_selector_top_list'),path:"settings."});
        RVS.F.turnOnOffVisUpdate({input:document.getElementById('sr_custom_n_opt')});
        RVS.F.turnOnOffVisUpdate({input:document.getElementById('sr_custom_m_opt')});
        RVS.F.turnOnOffVisUpdate({input:document.getElementById('sr_custom_t_opt')});
	}

	RVS.F.sliderUpdateAllFields = function(recall) {
		// FIRST DRAW AND INPUT SETS SLIDER

		setSlidesDimension(true);

		RVS.F.updateAvailableDevices();

        RVS.F.updateTopScreenSelectors();

        if (RVS.S.sliderInputFieldsInitialised) RVS.F.updateSliderInputFields();

        // INIT EASY INPUT BOXES HERE
        if (recall) jQuery('.slider_general_collector .tponoffwrap').each(function() { RVS.F.turnOnOff(this,false);});

        //GLOBAL SKIN UPDATES

        RVS.F.initTpColorBoxes(jQuery('#rbm_colorskins').find('.my-color-field'));

		RVS.DOC.trigger('updateSourcePostCategories');
		RVS.DOC.trigger('updateSourceWooCategories');
		RVS.DOC.trigger('updatesliderthumb');
		RVS.DOC.trigger('moduleSpinnerChange');
		RVS.DOC.trigger('updateAutoRotate');

		//TRIGGER THE ADDON GENERAL ON SWITCH EVENTS
		for (var slug in RVS.SLIDER.settings.addOns) {
			if(!RVS.SLIDER.settings.addOns.hasOwnProperty(slug)) continue;
			if (RVS.SLIDER.settings.addOns[slug].enable) RVS.DOC.trigger(slug+"_init");
		}

		// UPDATE CONTAINER DELTA
		RVS.F.updateContentDeltas();

		// UPDATE THE NAVIGATION CONTAINERS
		RVS.F.updateAllNavigationContainer(true);
		setProgressBar();
		setSliderBG(false);
		RVS.S.ulDIM = {width:RVS.C.UL.width(), height:RVS.C.UL.height()};
		RVS.F.updateParallaxLevelTexts();
		RVS.F.updateParallaxdddBG();

		RVS.DOC.trigger('checkOnScrollSettings');


	};

	/*
	UPDATE THE NAVIGATION CONTAINERS
	*/
	RVS.F.updateAllNavigationContainer = function(init) {
		if (RVS.SLIDER.settings.nav.arrows.set) RVS.F.updateNavStyleSelection({init:init,type:"arrows"});
		if (RVS.SLIDER.settings.nav.bullets.set) RVS.F.updateNavStyleSelection({init:init,type:"bullets"});
		if (RVS.SLIDER.settings.nav.tabs.set) RVS.F.updateNavStyleSelection({init:init,type:"tabs"});
		if (RVS.SLIDER.settings.nav.thumbs.set) RVS.F.updateNavStyleSelection({init:init,type:"thumbs"});
	};

	RVS.F.redrawAllNavigationContainer = function(init) {
		if (RVS.SLIDER.settings.nav.arrows.set) RVS.F.drawNavigation({init:init,type:"arrows"});
		if (RVS.SLIDER.settings.nav.bullets.set) RVS.F.drawNavigation({init:init,type:"bullets"});
		if (RVS.SLIDER.settings.nav.tabs.set) RVS.F.drawNavigation({init:init,type:"tabs"});
		if (RVS.SLIDER.settings.nav.thumbs.set) RVS.F.drawNavigation({init:init,type:"thumbs"});
	};

	/*
	SET UP THE RULERS IN ADMIN AREA
	*/
 	RVS.F.setRulers = function() {
		RVS.S.rulerOffset.x = Math.max(0,((_ibDIM.width)-RVS.F.GW(RVS.screen))/2);
		RVS.S.rulerOffset.y = Math.max(0,RVS.S.layer_wrap_offset.y);
		setRuler({offset:{x:RVS.S.rulerOffset.x, y:RVS.S.rulerOffset.y}});
	};
	/*
	SET THE MARKERS ON THE RULER
	*/
	RVS.F.setRulerMarkers = function(mouse) {
		mouse = mouse===undefined ? {y:0,x:0} : mouse;
		var rML = "15px",
			rMD =  RVS.S.builderHover==="overruler" || RVS.S.builderHover==="overbuilder" ? "block" : "hidden",
			rMP = { left: mouse.x,
					top : Math.max(0,(mouse.y-_builderOffset.top))};
		requestAnimationFrame(function() {
			tpGS.gsap.set(_rulerHM,{left:rMP.left+"px",height:rML,display:rMD});
			tpGS.gsap.set(_rulerVM,{top:rMP.top+"px",width:rML, display:rMD});
		});
	};

	/*
	UPDATE THE OFFSET POSITION OF THE CONTAINER
	*/
	RVS.F.updateContentDeltas = function() {
		if (RVS.C.layergrid===undefined && _lg===undefined) return;
		var _ulo = RVS.C.UL.offset(),
			_lgo = RVS.S.vWmode==="slidelayout" ? RVS.C.layergrid===undefined ? _lg.offset() : RVS.C.layergrid.offset() : _lg===undefined ? RVS.C.layergrid.offset() : _lg.offset();

		RVS.S.layer_grid_offset = _lgo;
		RVS.S.layer_wrap_offset.x = _lgo.left - _ulo.left;
		RVS.S.layer_wrap_offset.y = _lgo.top - _ulo.top;
		RVS.S.layer_wrap_offset.xr = RVS.C.UL.width()-_lg.width()-RVS.S.layer_wrap_offset.x;
		RVS.S.lgw = _lg.width();
		RVS.S.lgh = _lg.height();
		RVS.SLIDER.settings.size.editorCache[RVS.screen] = RVS.S.lgh;
		if (inf_s_height!==undefined) inf_s_height.innerHTML = parseInt(RVS.S.lgh,0)+"px";
		if (inf_s_width!==undefined) inf_s_width.innerHTML = Math.round(RVS.F.GW(RVS.screen))+"px";
		window.contentDeltaFirstRun = true;
	};

	/*
	USE PAN SLIDER (ON/OFF)
	*/
	RVS.F.panSlider = function(m) {
		/*var sl = (Math.min(Math.max((m.x-_builderOffset.left),0),_ibDIM.width) / _ibDIM.width) * (_rb[0].scrollWidth - _ibDIM.width);
		if (window.nothingselected)
			_rb.scrollLeft(sl).RSScroll("update");*/
	};

	/*
	UPDATE THE PARALLAX LEVEL TEXT / DESCRIPTIPON
	*/
	RVS.F.updateParallaxLevelTexts = function() {
		var chng=false,a,i;
		jQuery('.prallaxlevelselect').each(function() {
			chng = false;
			for (i=1;i<16;i++) {
				a = i+". ("+RVS.SLIDER.settings.parallax.levels[i-1]+" %)";
				if (this.options[i].text!==a) {
					chng=1;
					this.options[i].text = a;
				}
			}
			if (chng) jQuery(this).ddTP({});
		});
	};

	// CHECK SOURCES IF THEY HAVE BEEN SET CORRECT
	RVS.F.checkSliderSource = function() {
		var allgood = true,
			s = RVS.SLIDER.settings.source[RVS.SLIDER.settings.sourcetype],
			c = s.count;
		c=c===undefined || c=="" ? 0 : c;

		switch (RVS.SLIDER.settings.sourcetype) {
			case "facebook": allgood = s.apiId!=="" && (s.typeSource!=="album" || (s.typeSource=="album" && s.album!=="")) && c!=0; break;
			case "flickr": allgood = s.apiKey!=="" && s.appSecret!=="" && (s.galleryURL!=="" || s.groupURL!=="" || s.photoSet!=="" || s.userURL!=="") && c!=0; break;
			case "instagram": allgood = s.token!==undefined && s.token!=="" ? true : false; break;
			case "vimeo": allgood = s.typeSource=="channel" && s.channelName=="" ? false : s.typeSource=="user" && s.userName=="" ? false : s.typeSource=="group" && s.groupName=="" ? false : s.typeSource=="album" && s.albumId=="" ? false : true;
						  allgood = allgood === true && c!=0;
			break;
			case "youtube": allgood = s.api!=="" && s.channelId!=="" && c!=0; break;
			case "twitter": allgood = s.accessSecret!=="" && s.accessToken!=="" && s.consumerKey!=="" && s.consumerSecret!="" && s.userId!=="" && c!=0; break;
		}

		if (!allgood) RVS.F.showInfo({content:RVS_LANG.somesourceisnotcorrect, type:"goodtoknow", showdelay:2, hidedelay:5, hideon:"click", event:"" });

	}


	RVS.F.updateParallaxdddBG = function() {
		clearTimeout(window.updateParallaxDDDBGTimer);
		window.updateParallaxDDDBGTimer = setTimeout(function() {
			RVS.F.updateEasyInputs({container:jQuery('.slider_ddd_subsettings'), init:true});
		},50);
	};

	/*
	RESoRT THE SLIDE BASED ON THE RVS.SLIDER.slideIDs ARRAY
	*/
	RVS.F.reSortSlides = function() {
		for (var ids in RVS.SLIDER.slideIDs) {
			if(!RVS.SLIDER.slideIDs.hasOwnProperty(ids)) continue;
			if ((""+RVS.SLIDER.slideIDs[ids]).indexOf("static_")===-1)
				jQuery('#slidelist').append(jQuery('#slide_list_element_'+RVS.SLIDER.slideIDs[ids]));
		}
	};

	/*
	OPEN NEW GUIDER WINDOW AT 1ST TIME
	*/
	RVS.F.openNewGuide = function() {

		if (!window.initQuickGuide) {
			RVS.DOC.on('click','#rbm_quickguide .rbm_close, .mcg_quit_page', function() {
				RVS.F.RSDialog.close();
				RVS.F.sliderUpdateAllFields(true);
			});
			RVS.DOC.on('click','.mcg_next_page', function() { window.initQuickGuide.page++; callNewGuidePage();});
			RVS.DOC.on('click','.mcg_prev_page', function() { window.initQuickGuide.page--; callNewGuidePage(-1);});
			window.initQuickGuide = {
				page : 0,
				active : 0
			};


			RVS.DOC.on('click','.guide_combi_resize',function() {
				jQuery('.guide_combi_resize').removeClass("selected");
				this.className +=" selected";
				switch (this.id) {
					case "guide_classic":
						RVS.SLIDER.settings.def.intelligentInherit = false;
						RVS.SLIDER.settings.def.autoResponsive = false;
						RVS.SLIDER.settings.def.responsiveChilds = false;
						RVS.SLIDER.settings.def.responsiveOffset = false;
						RVS.SLIDER.settings.size.custom.n = false;
						RVS.SLIDER.settings.size.custom.t = false;
						RVS.SLIDER.settings.size.custom.m = false;
					break;
					case "guide_intelligent":
						RVS.SLIDER.settings.def.intelligentInherit = true;
						RVS.SLIDER.settings.def.autoResponsive = true;
						RVS.SLIDER.settings.def.responsiveChilds = true;
						RVS.SLIDER.settings.def.responsiveOffset = true;
					break;
					case "guide_manual":
						RVS.SLIDER.settings.def.intelligentInherit = false;
						RVS.SLIDER.settings.def.autoResponsive = false;
						RVS.SLIDER.settings.def.responsiveChilds = false;
						RVS.SLIDER.settings.def.responsiveOffset = false;
						RVS.SLIDER.settings.size.custom.n = true;
						RVS.SLIDER.settings.size.custom.t = true;
						RVS.SLIDER.settings.size.custom.m = true;
					break;
				}
				RVS.F.sliderUpdateAllFields(true);
				setSlidesDimension(true);
				RVS.F.updateAvailableDevices();
				RVS.F.updateDeviceOnOffBtns();
				RVS.F.updateEasyInputs({container:jQuery('.mcg_option_third_wraps'),trigger:"init",path:"settings."});
			});
		} else
			window.initQuickGuide.page = 0;


		callNewGuidePage();
		jQuery('#guide_classic').removeClass("selected");
		jQuery('#guide_intelligent').removeClass("selected");
		jQuery('#guide_manual').removeClass("selected");

		if (RVS.SLIDER.settings.def.intelligentInherit)
			jQuery('#guide_intelligent').addClass("selected");
		else
		if (!RVS.SLIDER.settings.size.custom.n && !RVS.SLIDER.settings.size.custom.t && !RVS.SLIDER.settings.size.custom.m)
			jQuery('#guide_classic').addClass("selected");
		else
			jQuery('#guide_manual').addClass("selected");

		RVS.F.updateEasyInputs({container:jQuery('#rbm_quickguide'),path:"settings."});
		RVS.F.RSDialog.create({modalid:'rbm_quickguide', bgopacity:0.85});
	};



	/**********************************
		-	INTERNAL FUNCTIONS -
	***********************************/

	RVS.F.duplicateSkinColors = function(_) {
		if (RVS.SLIDER.settings.skins===undefined || RVS.SLIDER.settings.skins.colors===undefined) return;
		for (var i in RVS.SLIDER.settings.skins.colors) {
			if (!RVS.SLIDER.settings.skins.colors.hasOwnProperty(i) || RVS.SLIDER.settings.skins.colors[i].ref===undefined) continue;
			var ref = RVS.SLIDER.settings.skins.colors[i].ref,
				nra = [];
			for (var j in ref) {
				if (!ref.hasOwnProperty(j)) continue;

				if (_.type==="slide" && _.slideFrom!==undefined && _.slideTo!==undefined && ""+ref[j].slide === ""+_.slideFrom) {
					var nr = RVS.F.safeExtend(true,{},ref[j]);
					nr.slide = ""+_.slideTo;
					nr.r = nr.r.replace(_.slideFrom,_.slideTo);
					nra.push(nr);
				} else
				if (_.type==="layer" && _.layerFrom!==undefined && _.layerTo!==undefined && ""+ref[j].slide === ""+_.slideFrom && ""+ref[j].layer === ""+_.layerFrom) {
					var nr = RVS.F.safeExtend(true,{},ref[j]);
					nr.layer = ""+_.layerTo;
					nr.slide = ""+_.slideTo;
					nr.r = nr.r.replace(_.slideFrom,_.slideTo);
					nr.r = nr.r.replace('.layers.'+_.layerFrom,'.layers.'+_.layerTo);
					nra.push(nr);
				}
			}
			for (j=0;j<nra.length;j++) ref.push(nra[j]);
		}
	}

	function checkonlySimpleMode(c) {
		var single = false;
		for (var i in c.ref) if (c.ref.hasOwnProperty(i)) single = single===true ? true : RVS.F.noGradient(c.ref[i].r);
		return single;
	}

	function addSkinColorFields(c,i) {
		var t = '<div id="globalskin_colorrule_'+i+'">';
		if (c===null) return;
		t +='<input type="text" value="'+c.alias+'" class="globalskin_alias easyinit sliderinput" data-r="skins.colors.'+i+'.alias">';
		t +='<input class="my-color-field skininput easyinit" data-evt="updateAllSkinColors" data-evtparam="'+i+'" data-visible="true" data-editing="Global Skin Color" name="skin-color-'+i+'" data-mode="'+(checkonlySimpleMode(c) ? 'single' : 'full')+'" id="sr_skin_color_'+i+'" data-r="skins.colors.'+i+'.v" type="text" value="'+c.v+'">';
		t +='<div style="margin-left:5px" data-evt="deleteSkinColor" data-evtparam="'+i+'" class="'+(i<1 ? "disabled" : "") +' callEventButton basic_action_button onlyicon "><i class="material-icons">delete</i></div>';
		t +='</div>';
		t = jQuery(t);
		RVS.ENV.skinColors.append(t);
		RVS.F.initTpColorBoxes(t.find('.my-color-field'));
	}

	function initGlobalSkins() {
		RVS.ENV.skinColors = RVS.ENV.skinColors===undefined ? jQuery('#module_color_skins') : RVS.ENV.skinColors;
		RVS.ENV.skinFonts = RVS.ENV.skinFonts===undefined ? jQuery('#module_font_skins') : RVS.ENV.skinFonts;
		if (RVS.SLIDER.settings.skins!==undefined) {
			if (RVS.SLIDER.settings.skins.colors===undefined) {
				RVS.SLIDER.settings.skins.cid = 2;
				RVS.SLIDER.settings.skins.colors = { 0:{alias:'Highlight',v:'#ff0000'}, 1:{alias:'Headline Text',v:'#ffffff'},2:{alias:'Content Text',v:'#00ffff'}}
			}
			for (var i in RVS.SLIDER.settings.skins.colors) {
				if (!RVS.SLIDER.settings.skins.colors.hasOwnProperty(i)) continue;
				var c = RVS.SLIDER.settings.skins.colors[i];
				addSkinColorFields(c,i);
			}
		}
	}




	function callNewGuidePage(dir) {
		if (window.initQuickGuide===undefined || window.initQuickGuide.page == window.initQuickGuide.active) return;
		RVS.F.updateEasyInputs({container:jQuery('.mcg_option_third_wraps'),trigger:"init",path:"settings."});
		jQuery('#mcg_page_'+window.initQuickGuide.page).addClass("mcg_selected");
		tpGS.gsap.fromTo('#mcg_page_'+window.initQuickGuide.page,0.5,{x:dir===-1 ? "-100%" : "100%"},{x:"0%",ease:"power3.inOut"});
		tpGS.gsap.fromTo('#mcg_page_'+window.initQuickGuide.active,0.5,{x:"0%"},{x:dir===-1 ? "100%" : "-100%",ease:"power3.inOut",onComplete:function() {
			jQuery('#mcg_page_'+window.initQuickGuide.active).removeClass("mcg_selected");
			window.initQuickGuide.active = window.initQuickGuide.page;
		}});

	}
	function revsliderScrollable(type) {

		if (RVS.S.TRTIS_initied!=true && (type===undefined || type==="init")) {
			RVS.S.rb_ScrollX = 0;
			RVS.S.rb_ScrollY = 0;
			sticky_settings = jQuery('#settings_sticky_info');
			RVS.C.rb.RSScroll({
				wheelPropagation:true,
				//suppressScrollY:true,
				minScrollbarLength:100
			});

			jQuery('#the_right_toolbar_inner').RSScroll({
				wheelPropagation:true,
				suppressScrollX:true,
				minScrollbarLength:100
			}).on('ps-scroll-y',function() {
				rTBScroll = this.scrollTop;
				if (rTBScroll>50)
					sticky_settings.show();
				else
					sticky_settings.hide();
				if (RVS.S.respInfoBar && RVS.S.respInfoBar.toolbar && RVS.S.respInfoBar.toolbar[0]!==null)
					RVS.S.respInfoBar.toolbar[0].style.display = "none";
			});

			RVS.C.rb.on('ps-scroll-x',function(){
				RVS.S.rb_ScrollX = _builderScroll.x = this.scrollLeft;
				RVS.F.setRulers();
			});
			RVS.C.rb.on('ps-scroll-y',function(){
				_builderScroll.x = this.scrollLeft;
				_builderScroll.y = this.scrollTop;
				RVS.S.rb_ScrollY = _builderScroll.y = this.scrollTop;
				RVS.F.setRulers();
			});

			RVS.S.TRTIS_initied = true;
			/*jQuery('#form_slidergeneral_module .form_inner').RSScroll({
				wheelPropagation:true,
				suppressScrollX:true
			});*/
		} else {
			if (type==="update" && RVS.S.TRTIS_initied==true) {
				clearTimeout(RVS.S.scrollUpdateTimer);
				RVS.S.scrollUpdateTimer = setTimeout(function() {
					RVS.C.rb.RSScroll("update");
					jQuery('#the_right_toolbar_inner').RSScroll("update");
				},50);
			}
		}
	}

	RVS.F.buildRuler = function(f) {

		if (RVS.S.bodyHidden===undefined) {
				document.body.style.overflowY="hidden";
				RVS.S.bodyHidden=true;
			}

		var ctxTOP = _rto[0].getContext('2d'),
			ctxLEFT = _rlo[0].getContext('2d'),
			a=0;

		RVS.S.isRetina = RVS.S.isRetina===undefined ? (window.devicePixelRatio > 1) : RVS.S.isRetina;
		RVS.S.isIOS = RVS.S.isIOS===undefined ? ((ctxTOP.webkitBackingStorePixelRatio < 2) || (ctxTOP.webkitBackingStorePixelRatio == undefined)) : RVS.S.isIOS;
		RVS.S.retinaFactor =  RVS.S.retinaFactor===undefined ? (RVS.S.isRetina && RVS.S.isIOS) ? 2 : 1 : RVS.S.retinaFactor;

		ctxTOP.canvas.width=3600*RVS.S.retinaFactor;
		ctxTOP.canvas.height=15*RVS.S.retinaFactor;

		ctxLEFT.canvas.width=15*RVS.S.retinaFactor;
		ctxLEFT.canvas.height=3600*RVS.S.retinaFactor;

		ctxTOP.scale(RVS.S.retinaFactor, RVS.S.retinaFactor);
		ctxLEFT.scale(RVS.S.retinaFactor, RVS.S.retinaFactor);


		ctxTOP.strokeStyle=ctxLEFT.strokeStyle="#414243";
		ctxTOP.font = "10px Arial";
		ctxLEFT.font = "10px Arial";
		ctxTOP.fillStyle = "rgba(183,187,192,0.5)";
		ctxLEFT.fillStyle = "rgba(183,187,192,0.5)";
		ctxTOP.beginPath();
		ctxLEFT.beginPath();

		for(var i=0;i<600;i++) {
			if (a%2!==0 && a!==0) {
				ctxTOP.moveTo(((i*10))*RVS.zoom,15);
				ctxTOP.lineTo(((i*10))*RVS.zoom,(15 - (8/RVS.S.retinaFactor)));
				ctxLEFT.moveTo(15,(i*10)*RVS.zoom);
				ctxLEFT.lineTo((15 - (8/RVS.S.retinaFactor)),(i*10)*RVS.zoom);
			}
			else
			if (a===0) {
				ctxTOP.moveTo(((i*10))*RVS.zoom,15);
				ctxTOP.lineTo(((i*10))*RVS.zoom,0);
				ctxLEFT.moveTo(15,(i*10)*RVS.zoom);
				ctxLEFT.lineTo(0,(i*10)*RVS.zoom);
			}
			else {
				ctxTOP.moveTo(((i*10))*RVS.zoom,15);
				ctxTOP.lineTo(((i*10))*RVS.zoom,(15 - (8/RVS.S.retinaFactor)));
				ctxLEFT.moveTo(15,(i*10)*RVS.zoom);
				ctxLEFT.lineTo((15 - (8/RVS.S.retinaFactor)),(i*10)*RVS.zoom);
			}
			a++;
			a = a==10 ? 0 : a;
		}

		ctxTOP.stroke();
		ctxLEFT.stroke();

		for(var i=0;i<60;i++) {
			var temp = (i-12)*100,
				digits = (""+temp).split("");
			ctxTOP.fillText(temp,((i*100) + 5)*RVS.zoom,10);
			for (var m in digits) ctxLEFT.fillText(digits[m],3,((i*100)+14)* RVS.zoom+(m*9));
		}

		//Update Liniear also to right position
		tpGS.gsap.set(_rto,{left:Math.round(-1200*RVS.zoom)+"px"});
		tpGS.gsap.set(_rlo,{top:Math.round(-1200*RVS.zoom)+"px"});

		if (f===undefined) {
			// INIT DIMENSIONS
			_ibDIM.width = _ib.width();
			RVS.S.ulDIM = {width:RVS.C.UL.width(), height:RVS.C.UL.height()};

			_builderOffset = RVS.C.rb.offset();
		}
	}

	function setRuler(obj) {

		requestAnimationFrame(function() {
	 		if (obj===undefined || obj.offset.x===undefined || obj.offset.y===undefined) return;
	 		var newPos = { x:(parseInt(obj.offset.x,0) -_builderScroll.x/RVS.zoom + RVS.S.dim_offsets.navleft),
	 					   y:(obj.offset.y) - (_builderScroll.y + RVS.S.dim_offsets.navtop)};

	 		newPos.x = newPos.x * RVS.zoom;

			tpGS.gsap.set(_rto,{x:newPos.x+"px"});
			tpGS.gsap.set(_rlo,{y:newPos.y+"px"});
		});
 	}

 	/* CHECK IF ANY STREAM DEPENDENCIES MUST BE LOADED */
 	function loadStreamDependencies(event,param) {
 		if (param==="force" || streamSrcUpdNeed || streamSrcUpdNeed===undefined) {
	 		if (RVS.SLIDER.settings.sourcetype==="flickr") flickrSourceChange();
			if (RVS.SLIDER.settings.sourcetype==="facebook") facebookSourceChange();
			if (RVS.SLIDER.settings.sourcetype==="youtube") youtubeSourceChange();
		}
		streamSrcUpdNeed = false;
		RVS.DOC.trigger('updatesliderthumb');
 	}

 	/*
	CSS AND JQUERY EDITOR .js_css_editor_tabs
	*/
	RVS.F.openSliderApi = function() {
		if (window.rs_jscss_editor==="FAIL") return;
		if (typeof RevMirror==="undefined" || RevMirror===undefined) {
			RVS.F.showWaitAMinute({fadeIn:500,text:RVS_LANG.loadingRevMirror});
			RVS.F.loadCSS(RVS.ENV.plugin_url+'/admin/assets/css/RevMirror.css');
			jQuery.getScript(RVS.ENV.plugin_url+'/admin/assets/js/plugins/RevMirror.js',function() {
				setTimeout(function() {RVS.F.showWaitAMinute({fadeOut:500});},100);
				RVS.F.openSliderApi();
			}).fail(function(a,b,c) {
				setTimeout(function() {RVS.F.showWaitAMinute({fadeOut:500});},100);
				window.rs_jscss_editor = "FAIL";
			});
		} else
		if (window.rs_jscss_editor===undefined) {
			window.rs_jscss_editor = RevMirror(document.getElementById('rs_css_js_area'), {
				value:RVS.SLIDER.settings.codes.css,
				mode:"css",
				theme:"hopscotch",
				lineWrapping:true,
				lineNumbers:true,
			});
			window.rs_jscss_editor.on('focus',function() {	window.rs_jscss_editor.refresh();})
			setTimeout(RVS.F.openSliderApi,200);
		} else {
			RVS.F.RSDialog.create({modalid:'rbm_slider_api', bgopacity:0.5});
			jQuery('.emc_toggle_inner').RSScroll({ suppressScrollX:true});
			setTimeout(function() {
				window.rs_jscss_editor.refresh();
			},600);
		}
	};


	/*
	CSS AND JQUERY EDITOR .js_css_editor_tabs
	*/
	RVS.F.openColorSkinApi = function() {
		RVS.F.RSDialog.create({modalid:'rbm_colorskins', bgopacity:0});
		RVS.F.updateEasyInputs({container:jQuery('#rbm_colorskins'),path:"settings."});
		RVS.F.initTpColorBoxes(jQuery('#rbm_colorskins').find('.my-color-field'));
	};




	/*
	INIT CUSTOM EVENT LISTENERS FOR TRIGGERING FUNCTIONS
	*/
	function initLocalListeners() {

		RVS.DOC.on('click','#add_skin_color',function() {
			if (RVS.SLIDER.settings.skins===undefined) return;
			if (RVS.SLIDER.settings.skins.colors.length>10) return;
			RVS.SLIDER.settings.skins.cid++;
			RVS.SLIDER.settings.skins.colors[RVS.SLIDER.settings.skins.cid] = { alias:"Color Alias", v:"#ffffff"};
			addSkinColorFields(RVS.SLIDER.settings.skins.colors[RVS.SLIDER.settings.skins.cid],RVS.SLIDER.settings.skins.cid);
			RVS.F.updateEasyInputs({container:RVS.ENV.skinColors, trigger:"init", visualUpdate:true});
		});

		var save_goto_clicked = false;
		RVS.DOC.on('click','.save_and_goto_button',function() {
			save_goto_clicked = this.dataset.goto.replace('[return_url]', Base64.encode(document.location.href));
			RVS.DOC.trigger("saveslider");
		});
		RVS.DOC.on('slidersaved',function(){
			if (save_goto_clicked !== false) {
				window.location.href = save_goto_clicked;
			}
		});

		RVS.DOC.on('deleteSkinColor',function(a,b) {
			if (b===undefined) return;
			RVS.S.skinColorToDelete = b;
			if (RVS.SLIDER.settings.skins.colors[b].ref!==undefined && RVS.SLIDER.settings.skins.colors[b].ref.length>0) {
				RVS.F.RSDialog.create({
						bgopacity:0.85,
						modalid:'rbm_decisionModal',
						icon:'delete',
						title:RVS_LANG.deleteskin,
						maintext:RVS_LANG.areyousuredeleteskin,
						subtext:RVS_LANG.colrskinhas+' '+RVS.SLIDER.settings.skins.colors[b].ref.length+' '+RVS_LANG.references+'. '+RVS_LANG.colorwillkept,
						do:{
							icon:"check_circle",
							text:RVS_LANG.deleteskin,
							event: "forcedeleteskincolor"
						},
						cancel:{
							icon:"cancel",
							text:RVS_LANG.cancel
						},
						swapbuttons:true
					});
			} else 	RVS.DOC.trigger('forcedeleteskincolor');

		});

		RVS.DOC.on('forcedeleteskincolor',function() {
			delete RVS.SLIDER.settings.skins.colors[RVS.S.skinColorToDelete];
			jQuery('#globalskin_colorrule_'+RVS.S.skinColorToDelete).remove();
		});

		RVS.DOC.on('updateSnapVisual',function() {
			RVS.F.snapVisual();
		});

		RVS.DOC.on('showhidescrollonssm',function(a,b) {
			jQuery('.sr_sbased_tab').hide();
			jQuery('#sr_sbased_'+b).show();
			RVS.F.checkForFixedScroll();
		});

		// Updat AutoRotateOptions
		RVS.DOC.on('updateAutoRotate',function(a,ds) {
			if (ds===undefined || ds.val===undefined) {
				if (!RVS.SLIDER.settings.general.slideshow.slideShow) jQuery('#generalslideshow').hide();
			} else {
				RVS.F.openBackupGroup({id:"autorotate",txt:"Auto Slideshow",icon:"play_circle_outline"});
				var  pre = "settings.general.slideshow.";
				if (!ds.val) {
					RVS.F.updateSliderObj({path:pre+'stopSlider',val:true});
					RVS.F.updateSliderObj({path:pre+'stopAfterLoops',val:0});
					RVS.F.updateSliderObj({path:pre+'stopAtSlide',val:1});
				} else
					RVS.F.updateSliderObj({path:pre+'stopSlider',val:false});
				RVS.F.closeBackupGroup({id:"autorotate"});
				RVS.F.updateEasyInputs({container:jQuery('#form_slidergeneral_slideshow'), trigger:"init", visualUpdate:true});
			}
		});

		RVS.DOC.on('screenSelectorChanged',function() {
			RVS.F.updateEasyInputs({container:jQuery('#form_slidergeneral_general_viewport'), init:"true"});

		});

		RVS.DOC.on('checkOnScrollSettings',function() {
			if (RVS.TL===undefined || RVS.TL.TL===undefined) return;
			if (RVS.SLIDER.settings.scrolltimeline.set && RVS.SLIDER.settings.scrolltimeline.fixed && RVS.SLIDER.settings.layouttype!=="auto")
				RVS.TL.TL.addClass('fixedscrollon');
			else
				RVS.TL.TL.removeClass('fixedscrollon');
			RVS.DOC.trigger('checkLayerLoopswithOnScroll');
		});

		RVS.DOC.on('checkLayerLoopswithOnScroll',function() {
			clearTimeout(RVS.S.checkLayerLoopswithOnScroll);
			RVS.S.checkLayerLoopswithOnScroll = setTimeout(function() {
				// CHECK IF WE NEED TO DISABLE LOOP ANIMATION ON ANY LAYERS
				if (RVS.SLIDER.settings.scrolltimeline.set===true) {
					var changedsomething = false;
					for (var i in RVS.L) {
						if(!RVS.L.hasOwnProperty(i) || RVS.L[i].timeline===undefined || RVS.L[i].timeline.scrollBased===undefined) continue;
						if ((RVS.L[i].timeline.scrollBased=='true' ||  (RVS.L[i].timeline.scrollBased=='default' && RVS.SLIDER.settings.scrolltimeline.layers===true))) {
							RVS.L[i].timeline.loop.use = false;
							changedsomething = true;
						}
					}
					if (changedsomething) {
						RVS.F.updateEasyInputs({container:jQuery('#layer_looping_wrap'), trigger:"init", visualUpdate:true});
						RVS.F.showInfo({content:RVS_LANG.layerloopdisabledduetimeline, type:"goodtoknow", showdelay:0, hidedelay:2, hideon:"", event:"" });
					}
				}
			},200);
		});

		//Insert into Editor Listener
		RVS.DOC.on('click','.insertineditor',function() {
			RVS.F.insertTextAtCursor(window.rs_jscss_editor,"\n"+jQuery(this.dataset.insertfrom).val().replace("revapi.","revapi"+RVS.ENV.sliderID+".")+"\n");
			return false;
		});

		RVS.DOC.on('click','.js_css_editor_tabs',function() {
			jQuery('.js_css_editor_tabs').removeClass("selected");
			jQuery(this).addClass("selected");
			RVS.SLIDER.settings.codes[window.rs_jscss_editor.getMode().name] = window.rs_jscss_editor.getValue();
			window.rs_jscss_editor.setValue(RVS.SLIDER.settings.codes[this.dataset.mode]);
			window.rs_jscss_editor.setOption("mode", this.dataset.mode);
		});

		RVS.DOC.on('click','#emc_toggle, #form_slidergeneral_advanced_api',function() {
			jQuery('.emc_toggle_wrap').toggleClass("open");
		});

		// OPEN COLOR SKIN EDITOR
		RVS.DOC.on('openColorSkinApi',RVS.F.openColorSkinApi);
		RVS.DOC.on('click','#rbm_colorskins .rbm_close',function() {
			RVS.F.RSDialog.close();
		});

		// OPEN CSS AND JS EDITOR
		RVS.DOC.on('openSliderApi',RVS.F.openSliderApi);

		//CLOSE CSS AND JS EDITOR
		RVS.DOC.on('click','#rbm_slider_api .rbm_close',function() {
			RVS.SLIDER.settings.codes[window.rs_jscss_editor.getMode().name] = window.rs_jscss_editor.getValue();
			RVS.F.RSDialog.close();
		});

		RVS.DOC.on('device_area_dimension_update',function() {
			setSlidesDimension(true, true);
			RVS.DOC.trigger("updateAllInheritedSize");
			RVS.F.redrawSlideBG();
			RVS.F.expandCollapseTimeLine(true,"open");
		});

		RVS.DOC.on('updatePerspective',function() {
			jQuery('#global_layers_perspectives').val(RVS.SLIDER.settings.general.perspective);
			RVS.F.allLayersReDraw();
		});

		RVS.DOC.on('updatesliderlayout_main',function(e,p) {
			RVS.DOC.trigger('checkOnScrollSettings');
			RVS.DOC.trigger('updatesliderlayout',[e,p]);
		});

		RVS.DOC.on('updatesliderlayout',function(e,p) {
			RVS.F.updatesliderlayout(p);
		});

		RVS.F.updatesliderlayout = function(p) {
			if (RVS.S.calledUpdateSliderLayout!==undefined && (RVS.S.drawingHTMLLayers || !RVS.S.drawHTMLLayersCalled)) return;
			clearTimeout(window.updateSliderLayoutTimer);
			lgHeight_cache =  RVS.S.lgh;
			RVS.S.calledUpdateSliderLayout = RVS.S.calledUpdateSliderLayout || 0;
			RVS.S.calledUpdateSliderLayout++;

			window.updateSliderLayoutTimer = setTimeout(function() {
				setSlidesDimension(false);
				RVS.F.redrawSlideBG();
				if (p==="slidertype") setProgressBar();
				if (lgHeight_cache!==RVS.S.lgh || RVS.ENV.globVerOffset!== RVS.S.cacheglobVerOffset) {
					RVS.F.updateAllHTMLLayerPositions();
					RVS.S.cacheglobVerOffset = RVS.ENV.globVerOffset;
				}
				// FIRST OPEN THE TIMELINE WHEN WE DONE WITH ALL DRAWS IN INIT MODE
				if (RVS.S.ReadyToShowAll=="wait"){
					RVS.C.UL[0].style.opacity = 1;
					RVS.S.ReadyToShowAll = "done";
					RVS.F.expandCollapseTimeLine(true,"open",undefined,true)
				}
			},100);
		}

		RVS.DOC.on('device_area_availibity',function() {
			setSlidesDimension(true);
			RVS.F.updateAvailableDevices();
			RVS.F.updateDeviceOnOffBtns();
		});

		RVS.DOC.on('check_custom_size',function(e,ep) {
			checkCustomSliderSize(ep.eventparam);
		});

		RVS.F.staticsDoubleUpdate = function(callback) {
			if(RVS.S.resetLastShownAndStatic!==true && RVS.SLIDER[RVS.S.slideId].slide.static.isstatic && RVS.SLIDER[RVS.S.slideId].slide.static.lastEdited && RVS.S.lastShownSlideId!==undefined) {
				RVS.S.resetLastShownAndStatic = true;
				RVS.S.slideIdCache = RVS.S.slideId;
				RVS.S.slideId = RVS.S.lastShownSlideId;
			}
			callback.call();

			if (RVS.S.resetLastShownAndStatic) {
				clearTimeout(window.resetLastShownTimer);
				window.resetLastShownTimer = setTimeout(function() {
					RVS.S.resetLastShownAndStatic = false;
					RVS.S.lastShownSlideId = RVS.S.slideId;
					RVS.S.slideId = RVS.S.slideIdCache;
					callback.call();
				},200);
			}
		}

		RVS.DOC.on('windowresized',function() {
			RVS.F.staticsDoubleUpdate(RVS.F.sliderUpdateAfterResize);
	    });

	    RVS.F.sliderUpdateAfterResize = function() {
			_rvbiDIM.width = _rvbin.width();
			_ibDIM.width = _ib.width();
			setSlidesDimension(false);
			RVS.F.setRulers();
			RVS.F.updateContentDeltas();
			revsliderScrollable("update");
		}

		RVS.DOC.on('updateShortCode',function() {
			RVS.SLIDER.settings.alias = RVS.F.sanitize_input(RVS.SLIDER.settings.alias);
			RVS.SLIDER.settings.shortcode = '{{block class="Nwdthemes\\Revslider\\Block\\Revslider" alias="'+RVS.SLIDER.settings.alias+'"}}';
			RVS.SLIDER.settings.modalshortcode = '{{block class="Nwdthemes\\Revslider\\Block\\Revslider" usage="modal" alias="'+RVS.SLIDER.settings.alias+'"}}';
			RVS.F.updateEasyInputs({container:jQuery('#form_module_title'), init:"true"});
			RVS.F.updateEasyInputs({container:jQuery('#form_slider_as_modal'), init:"true"});

		});

		RVS.DOC.on('sliderBGUpdate',setSliderBG);
		RVS.DOC.on('drawBGOverlay',drawBGOverlay);
		RVS.DOC.on('sliderProgressUpdate',setProgressBar);
		RVS.DOC.on('coloredit colorcancel',colorEditSlider);
		//RVS.DOC.on('updateSliderToAspectRatio',updateSliderToAspectRatio);
		RVS.DOC.on('updateParallaxLevelTexts',RVS.F.updateParallaxLevelTexts);
		RVS.DOC.on('updateParallaxdddBG',RVS.F.updateParallaxdddBG);

		// LISTENER ON PRESET CHANGES
		RVS.DOC.on('updateSourcePostCategories',function() {RVS.F.updatePostCategories({postTypes:RVS.SLIDER.settings.source.post.types, categories:jQuery('#post_category')});});
		RVS.DOC.on('flickrsourcechange',flickrSourceChange);
		RVS.DOC.on('facebooksourcechange',facebookSourceChange);
		RVS.DOC.on('youtubesourcechange',youtubeSourceChange);
		RVS.DOC.on('loadStreamDependencies',loadStreamDependencies);

		// REVERT LISTENER ON SPECIAL FORMS
		RVS.DOC.on('revertEasyInputs.source',function(e,ep) {
			RVS.F.updateEasyInputs({container:ep,trigger:"init",path:"settings."});
			/* DO WE NEED RELOAD STREAM DEPENDENCIES NOW, OR FIRST WHEN WINDOW IS SELECTED !?? */
			var flickchange = RVS.SLIDER.settings.source.flickr.apiKey !== RVS.F.revert.settings.source.flickr.apiKey ||
							  RVS.SLIDER.settings.source.flickr.userURL !== RVS.F.revert.settings.source.flickr.userURL ||
							  RVS.SLIDER.settings.source.flickr.apiKey !== RVS.F.revert.settings.source.flickr.apiKey,

				fbchange = 	RVS.SLIDER.settings.source.facebook.appId !== RVS.F.revert.settings.source.facebook.appId ||
							RVS.SLIDER.settings.source.facebook.typeSource !== RVS.F.revert.settings.source.facebook.typeSource ||
							RVS.SLIDER.settings.source.facebook.album !== RVS.F.revert.settings.source.facebook.album,

				ytchange = 	RVS.SLIDER.settings.source.youtube.api !== RVS.F.revert.settings.source.youtube.api ||
							RVS.SLIDER.settings.source.youtube.channelId !== RVS.F.revert.settings.source.youtube.channelId;

			if (flickchange || fbchange || ytchange) streamSrcUpdNeed = true;
			/*if (flickchange) flickrSourceChange();
			if (fbchange) facebookSourceChange();
			if (ytchange) youtubeSourceChange();*/

		});

		RVS.DOC.on('moduleSpinnerChange',function() {

			var tpe = RVS.SLIDER.settings.layout.spinner.type;
			jQuery('rs-loader').attr('class',"spinner"+tpe).html(getSpinnerMarkup());
			if(isNaN(tpe) || parseInt(tpe, 10) < 6) setSpinnerColors();

		});

		RVS.DOC.on('scrollUpdates',function() {
			revsliderScrollable("update");
		});

	}

	function getSpinnerMarkup(color) {

		jQuery('rs-loader').css('background', '').find('div').css('background', '');
		var tpe = parseInt(RVS.SLIDER.settings.layout.spinner.type, 10),
			html;

		// legacy spinners
		if(tpe === NaN || tpe < 6) {
			html = '<div class="dot1"></div><div class="dot2"></div><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div>';
		}
		// new spinners
		else {

			var spans = [10, 0, 4, 2, 5, 9, 0, 4, 4, 2],
				num = spans[tpe - 6];

			html = '<div class="rs-spinner-inner"';
			if(!color) color = RVS.SLIDER.settings.layout.spinner.color;

			if(tpe === 7) {
				var clr;
				if(color.search('#') !== -1) {
					clr = RSColor.processRgba(color);
				}
				else if(color.search('rgb') !== -1) {
					clr = RSColor.rgbValues(color);
					if(clr.length > 2) clr = RSColor.rgbString(clr[0].trim(), clr[1].trim(), clr[2].trim());
				}
				if(clr && typeof color === 'string') {
					clr = clr.replace(')', ', ');
					if(clr.search('rgba') === -1) clr = clr.replace('rgb', 'rgba');
					html += ' style="border-top-color: ' + clr + '0.65); border-bottom-color: ' + clr + '0.15); border-left-color: ' + clr + '0.65); border-right-color: ' + clr + '0.15)"';
				}
			}
			else if(tpe === 12) {
				html += ' style="background:' + color + '"';
			}

			html += '>';
			for(var i = 0; i < num; i++) {
				if(i > 0) html += ' ';
				html += '<span style="background:' + color + '"></span>';
			}
			html += '</div>';

		}

		return html;

	}

	/*
	SET THE COLOR OF THE SPINNER
	 */
	function setSpinnerColors(col) {

		col = col===undefined ? RVS.SLIDER.settings.layout.spinner.color : col;
		var sel = RVS.SLIDER.settings.layout.spinner.type;
		if (sel==0 || sel==5) col ="#ffffff";

		var spin = jQuery('rs-loader');
		if (sel==0 || sel==1 || sel==2 || sel==5)
			spin.css({'backgroundColor':col});
		else if(sel ==3 || sel==4) {
			spin.css({'backgroundColor':'transparent'});
		 	spin.find('div').css({'backgroundColor':col});
		}
		// new spinners
		else {
			spin.html(getSpinnerMarkup(col));
		}
	}

	/**
	 * Show/Hide flickr Photosets
	 */
	function flickrSourceChange() {
		var _ = RVS.SLIDER.settings.source.flickr;
		if(_.type=='photosets'){
			if(_.userURL!="" && _.apiKey!=""){
				var data = {
								url 	:  _.userURL,
								key 	:  _.apiKey,
								count 	:  _.count,
								set 	:  _.photoSet
							};
				RVS.F.ajaxRequest("get_flickr_photosets", data, function(response){
					jQuery("#sr_src_flickr_photoset").html(response.data.html);
					RVS.F.setS2Option({select:jQuery("#sr_src_flickr_photoset"), selectValue:_.photoSet});
				});
			}
			else{
				jQuery("#sr_src_flickr_photoset").html("");
				RVS.F.setS2Option({select:jQuery("#sr_src_flickr_photoset"),selectValue:""});
			}
		}

	}

	/**
	 * Show/Hide facebook Albums
	 */
	function facebookSourceChange() {
		var _ = RVS.SLIDER.settings.source.facebook;
		if(_.typeSource=='album'){
			if(_.appId!="" && _.page_id!=""){
				var data = {
								app_id 		:  _.appId,
								page_id 	:  _.page_id,
							};
				RVS.F.ajaxRequest("get_facebook_photosets", data, function(response){
					jQuery("#sr_src_facebok_album").html(response.html);
					RVS.F.setS2Option({select:jQuery("#sr_src_facebok_album"), selectValue:_.album});
				});
			}
			else{
				jQuery("#sr_src_facebok_album").html("");
				RVS.F.setS2Option({select:jQuery("#sr_src_facebok_album"),selectValue:""});
			}
		}
	}

	/**
	 * Show/Hide YouTube Playlist
	 */
	function youtubeSourceChange() {
		var _ = RVS.SLIDER.settings.source.youtube;
		if(_.typeSource=='playlist'){
			if(_.api!="" && _.channelId!=""){
				var data = {
								api 	:  _.api,
								id 		:  _.channelId,
								playlist :  _.playList
							};
				RVS.F.ajaxRequest("get_youtube_playlists", data, function(response){
					jQuery("#sr_src_youtube_playlist").html(response.data.html);
					if (_.playList==="") {
						var fo = jQuery("#sr_src_youtube_playlist option").first();
						_.playList = fo[0].value;
					}
					RVS.F.setS2Option({select:jQuery("#sr_src_youtube_playlist"), selectValue:_.playList});
				});
			}
			else{
				jQuery("#sr_src_youtube_playlist").html("");
				RVS.F.setS2Option({select:jQuery("#sr_src_youtube_playlist"),selectValue:""});
			}
		}

	}
	/*
	HIDE/SHOW ADVANCED SETTINGS IN CASE JUSTIFY AND CAROUSEL IS SET
	 */
	function checkJustifyCarousel() {
		if (RVS.SLIDER.settings.carousel.justify && RVS.SLIDER.settings.type==="carousel") RVS.C.tRC.classList.add('_just_carousel_'); else RVS.C.tRC.classList.remove('_just_carousel_');
	}

	/*
	INIT LOCAL INPUT BOX FUNCTIONS
	*/
	function initLocalInputBoxes() {
		// Handle ScreenSize Options
		jQuery('#screenselector').on('change',function(e) {
			RVS.screen = this.value;
			RVS.S.nextscreen = RVS.screen==="d" ? "none" : RVS.screen==="n" ? "d" : RVS.screen==="t" ? "n" : RVS.screen=="m" ? "t" : "none";
			RVS.S.prevscreen = RVS.screen==="d" ? "n" : RVS.screen==="n" ? "t" : RVS.screen==="t" ? "m" : "none";
			jQuery('.screen_selector.selected').removeClass("selected");
			jQuery('.screen_selector.ss_'+RVS.screen).addClass("selected");
			setSlidesDimension(false);
			RVS.DOC.trigger('sliderSizeChanged');
			RVS.F.setRulers();
		});


		//ADD NEW SLIDE
		RVS.DOC.on('click','#newslide, #add_blank_slide',function() {
			RVS.F.addRemoveSlideWithBackupAfterSlideId({
					id : "addnewslide",
					step : "Add New Slide",
					icon : "fiber_new",
					slideObj : {slide:RVS.F.addSlideObj(), layers:{}},
					slideObjOld : {},
					beforeSelected:RVS.S.slideId,
					after:function() {RVS.DOC.trigger('changeToSlideMode');}
			});
			return false;
		});

		//ADD BULK SLIDE
		RVS.DOC.on('addBulkSlides',function(e,param) {
			RVS.F.addRemoveSlideWithBackupAfterSlideId({
					id : "addnewslide",
					step : "Add New Slide",
					icon : "fiber_new",
					slideObj : {slide:RVS.F.addSlideObj(), layers:{}},
					slideObjOld : {},
					beforeSelected:RVS.S.slideId,
					urls:param.urlImage,
					endOfMain:function() {RVS.DOC.trigger('changeToSlideMode'); setTimeout(function() {RVS.DOC.trigger('saveslider',{force:true});},500); }
			});
			return false;
		});

		// ADD TEMPLATE SLIDE
		RVS.DOC.on('click','#add_template_slide',function() {
			RVS.F.openObjectLibrary({types:["moduletemplates","modules"],filter:"all", selected:["moduletemplates"], context:"editor", success:{slide:"addSlideFromTemplate"}});
		});

		// ADD TEMPLATE SLIDE
		RVS.DOC.on('click','#add_module_slide',function() {
			RVS.F.openObjectLibrary({types:["modules","moduletemplates"],filter:"all", selected:["modules"], context:"editor", success:{slide:"addSlideFromTemplate"}});
		});

		RVS.DOC.on('addSlideFromTemplate',function(e,param) {
			RVS.F.ajaxRequest('install_template_slide', {slider_id:RVS.ENV.sliderID, slide_id:param}, function(response){
				if (response.success) {
					for (var sindex in response.slides) {
						if(!response.slides.hasOwnProperty(sindex)) continue;
						// Create New Slide Object
						var newSlide = {slide:RVS.F.addSlideObj( RVS.F.expandSlide(response.slides[sindex].params)), layers:{}, id:response.slides[sindex].id};



						// Add Layers to Slide Object
						for (var layerIndex in response.slides[sindex].layers) {
							if(!response.slides[sindex].layers.hasOwnProperty(layerIndex)) continue;
							var layerObj = response.slides[sindex].layers[layerIndex],
								newLayer =  RVS.F.addLayerObj(RVS.F.safeExtend(true,RVS.F.addLayerObj(layerObj.type,undefined,true), layerObj));


							if (newLayer) newSlide.layers[newLayer.uid] = newLayer;
						}
						// Push Slide Object to Slider Array
						RVS.SLIDER[response.slides[sindex].id] = newSlide;
						RVS.SLIDER.slideIDs.push(response.slides[sindex].id);
						RVS.F.addToSlideList({id:response.slides[sindex].id});
		            }
		            RVS.F.mainMode({mode:"slidelayout",set:true, slide:response.slides[0].id});
				}
			});


		});

		RVS.DOC.on('updatepublishicons',function(a,b) {
			if (b!==undefined && b.val!==undefined) document.getElementById('publish_toggle_icon_'+RVS.S.slideId).className = b.val+"slide";
		});

		//PUBLISH SLIDE
		RVS.DOC.on('click','.publishedslide, .unpublishedslide',function() {
			var slideid = jQuery(this).closest('li').data('ref');
			RVS.SLIDER[slideid].slide.publish.state = RVS.SLIDER[slideid].slide.publish.state==="published" ? "unpublished" : "published";
			this.className= RVS.SLIDER[slideid].slide.publish.state+"slide";
			RVS.F.updateEasyInputs({container:jQuery('#form_slidegeneral_progstate'), path:slideid+".slide.", trigger:"init"});
			RVS.F.slideinWork(slideid);
			return false;
		});

		RVS.DOC.on('deletesingleslide',function() {
			RVS.F.addRemoveSlideWithBackup({	id : "deleteslide",
				step : "Remove Slide",
				icon : "remove",
				slideObjOld : RVS.F.safeExtend(true,{},RVS.SLIDER[window.delete_slide_id]),
				slideId : window.delete_slide_id,
				slideObj : {},
				beforeSelected:RVS.S.slideId
			});
		})

		//DELETE SLIDE
		RVS.DOC.on('click','.deleteslide, #do_delete_slide',function() {
			window.delete_slide_id = this.id==="do_delete_slide" ? RVS.S.slideId : jQuery(this).closest('li').data('ref');
			RVS.F.RSDialog.create({
					bgopacity:0.85,
					modalid:'rbm_decisionModal',
					icon:'delete',
					title:RVS_LANG.deleteslide,
					maintext:RVS_LANG.deletingslide,
					subtext:RVS_LANG.deleteselectedslide+" <strong>"+RVS.SLIDER[window.delete_slide_id].slide.title+"</strong> ?",
					do:{
						icon:"delete",
						text:RVS_LANG.yesdeleteslide,
						event: "deletesingleslide"
					},
					cancel:{
						icon:"cancel",
						text:RVS_LANG.cancel
				}});


			return false;
		});

		// DUPLICATE SLIDE
		RVS.DOC.on('click','.duplicateslide, #do_duplicate_slide',function() {
			var slideid = this.id==="do_duplicate_slide" ? RVS.S.slideId : jQuery(this).closest('li').data('ref');
			RVS.F.addRemoveSlideWithBackupAfterSlideId({
				id : "duplicateslide",
				step : "Duplicate Existing Slide",
				icon : "content_copy",
				slideObj : RVS.F.safeExtend(true,{},RVS.SLIDER[slideid]),
				fromSlideId:slideid,
				//slideId : RVS.F.getNewSlideId(),
				slideObjOld : {},

				beforeSelected:RVS.S.slideId
			});

			return false;
		});

		// DUPLICATE SLIDE TO CHILDSLIDE
		RVS.DOC.on('click','.addchildslide, #do_addchild_slide',function() {
			var slideid = this.id==="do_addchild_slide" ? RVS.S.slideId : jQuery(this).closest('li').data('ref');
			RVS.F.addRemoveSlideWithBackupAfterSlideId({
				id : "duplicateslide",
				parentId:slideid,
				step : "Duplicate Existing Slide",
				icon : "content_copy",
				slideObj : RVS.F.safeExtend(true,{},RVS.SLIDER[slideid]),
				fromSlideId:slideid,
				//slideId : RVS.F.getNewSlideId(),
				slideObjOld : {},
				beforeSelected:RVS.S.slideId
			});

			return false;
		});

	}


	/*
	CHECK IF CURRENT SELECTED SIZE HAS CUSTOM DIMENSIONS, OR LINEAR INHERITED VALUES
	*/
	function checkCustomSliderSize(screen) {
		RVS.SLIDER.settings.size.custom[screen] = true;
		jQuery('#sr_custom_'+screen).prop('checked',RVS.SLIDER.settings.size.custom[screen]);
		RVS.F.turnOnOffVisUpdate({input:document.getElementById('sr_custom_'+screen)});
	}

	/*
	UPDATE ALL SLIDES DIMENSION TO CURRENT OBJECT VALUE
	*/
	function getLastBiggerSliderDimension(_s) {
		var found = false,
			r = {w:RVS.F.GW("d"), h:RVS.SLIDER.settings.size.height.d};
		for (var s in RVS.V.sizes) {
			if(!RVS.V.sizes.hasOwnProperty(s)) continue;
			if (!found && RVS.SLIDER.settings.size.custom[RVS.V.sizes[s]]) {
				r.w = RVS.F.GW(RVS.V.sizes[s]);
				r.h = parseInt(RVS.SLIDER.settings.size.height[RVS.V.sizes[s]],0);
			}
			if (RVS.V.sizes[s] === _s) found = true;
		}
		return r;
	}

	RVS.F.JWALL = function() {
		return RVS.SLIDER.settings.type==="carousel" && RVS.SLIDER.settings.carousel.justify===true;
	}

	RVS.F.GW = function(s) {
		var r = parseInt(RVS.SLIDER.settings.size.width[s],0);
		if (RVS.F.JWALL()) {
			var searchid = RVS.SLIDER[RVS.S.slideId].slide.static.isstatic && RVS.S.lastShownSlideId!==undefined ? RVS.S.lastShownSlideId : RVS.S.slideId;
			// JUSTIFY WALL IS ON AND WE HAVE AN IMAGE IN SLIDE
			if (RVS.SLIDER[searchid].slide.bg.type==="image" || RVS.SLIDER[searchid].slide.bg.type==="external")
				if (RVS.SLIDER[searchid].slide.bg.imageRatio!==undefined) r = parseInt(RVS.SLIDER.settings.size.height[s],0)  * RVS.SLIDER[searchid].slide.bg.imageRatio;

		}
		return r;
	}

	/*
	MODIFICATE THE HEIGHT IF OUTER NAV SET OR CAROUSEL PADDINGS SET
	*/
	RVS.F.sliderDimensionOffsets = function() {
		var _ = {};
		_.carouseltop = RVS.SLIDER.settings.type==="carousel" ? parseInt(RVS.SLIDER.settings.carousel.paddingTop,0) : 0;
		_.carouselbottom = RVS.SLIDER.settings.type==="carousel" ? parseInt(RVS.SLIDER.settings.carousel.paddingBottom,0) : 0;
		_.carouseloffset = RVS.SLIDER.settings.type==="carousel" ? _.carouseltop + _.carouselbottom : 0;

		_.navtop = RVS.SLIDER.settings.nav.thumbs.innerOuter==="outer-top" && RVS.SLIDER.settings.nav.thumbs.set ? RVS.S.navOffset.thumbs.top : 0;
		_.navtop = RVS.SLIDER.settings.nav.tabs.innerOuter==="outer-top" && RVS.SLIDER.settings.nav.tabs.set ? RVS.S.navOffset.tabs.top : _.navtop;

		_.navbottom = RVS.SLIDER.settings.nav.thumbs.innerOuter==="outer-bottom" && RVS.SLIDER.settings.nav.thumbs.set ? RVS.S.navOffset.thumbs.bottom : 0;
		_.navbottom = RVS.SLIDER.settings.nav.tabs.innerOuter==="outer-bottom" && RVS.SLIDER.settings.nav.tabs.set ? RVS.S.navOffset.tabs.bottom : _.navbottom;

		_.navleft = RVS.SLIDER.settings.nav.thumbs.innerOuter==="outer-left" && RVS.SLIDER.settings.nav.thumbs.set ? RVS.S.navOffset.thumbs.left : 0;
		_.navleft = RVS.SLIDER.settings.nav.tabs.innerOuter==="outer-left" && RVS.SLIDER.settings.nav.tabs.set ? RVS.S.navOffset.tabs.left : _.navleft;

		_.navright = RVS.SLIDER.settings.nav.thumbs.innerOuter==="outer-right" && RVS.SLIDER.settings.nav.thumbs.set ? RVS.S.navOffset.thumbs.right : 0;
		_.navright = RVS.SLIDER.settings.nav.tabs.innerOuter==="outer-right" && RVS.SLIDER.settings.nav.tabs.set ? RVS.S.navOffset.tabs.right : _.navright;

		_.louter = (RVS.SLIDER.settings.nav.thumbs.innerOuter==="outer-left" && RVS.SLIDER.settings.nav.thumbs.set) || (RVS.SLIDER.settings.nav.tabs.innerOuter==="outer-left" && RVS.SLIDER.settings.nav.tabs.set);
		_.router = (RVS.SLIDER.settings.nav.thumbs.innerOuter==="outer-right" && RVS.SLIDER.settings.nav.thumbs.set) || (RVS.SLIDER.settings.nav.tabs.innerOuter==="outer-right" && RVS.SLIDER.settings.nav.tabs.set);

		return _;
	};

	/*
	Content Height changed ?
	*/
	RVS.F.updateMinSliderHeights = function() {
		lastLayerGridHeight = minLayerGridHeight === undefined ? 0 : minLayerGridHeight;
		minLayerGridHeight = RVS.C.layergrid!==undefined ?  RVS.C.rZone.top.height() +  RVS.C.rZone.middle.height() +  RVS.C.rZone.bottom.height() : 0;
		return lastLayerGridHeight!==minLayerGridHeight;
	};

	/*
	DRAWS THE NEW DIMENSION OF THE SLIDER
	*/
	function setSlidesDimension(updateFields, updateShrinks) {
		// MAKE SURE ONLY RUN THIS IF DIMENSIONS HAS BEEN CHANGED


		var custom = RVS.SLIDER.settings.size.custom[RVS.screen],
			ld = getLastBiggerSliderDimension(RVS.screen),
			// CALCULATE WIDTH AND HEIGHT OF LAYER GRID
			w = custom ? RVS.F.GW(RVS.screen) : Math.min(ld.w,RVS.ENV.grid_sizes[RVS.screen]),
			h = custom ? parseInt(RVS.SLIDER.settings.size.height[RVS.screen],0) : (w / ld.w) * ld.h,
			// CALCULATE MIN HEIGHT OF PARRENT CONTAINER
			minh = RVS.SLIDER.settings.layouttype==="fullscreen" ? RVS.SLIDER.settings.size.minHeightFullScreen : RVS.SLIDER.settings.size.minHeight,
			nw = "100%",
			ar = h/w;

		checkJustifyCarousel();

		minh = minh==="none" || !RVS.F.isNumeric() ? 0 : minh;
		minh = RVS.SLIDER.settings.layouttype==="fullscreen" ? (Math.max(Math.max(minh,RVS.S.winh-RVS.ENV.globVerOffset-65),h)) : (Math.max(minh,h));

		//Respect Aspect Ratio
		minh = RVS.SLIDER.settings.size.respectAspectRatio ? nw==="100%" ? Math.max(RVS.C.rb.width(),w)*ar : parseInt(nw,0)*ar : minh;


		RVS.F.updateMinSliderHeights();
		minh = Math.max(minh,minLayerGridHeight);
		h = Math.max(h,minLayerGridHeight);

		ar = h/w;
		//CALCULATE MIN HEIGHT OF TOP CONTAINER
		var nminw = w,
			maxW = "none",
			pd = parseInt(RVS.SLIDER.settings.layout.bg.padding,0) || 0;

		RVS.S.dim_offsets = RVS.F.sliderDimensionOffsets();

		if (RVS.F.isNumeric(RVS.SLIDER.settings.size.maxWidth) && RVS.SLIDER.settings.size.maxWidth>0)
			nminw = Math.min(parseInt(RVS.SLIDER.settings.size.maxWidth,0),w)+"px";

		//Draw Layout Containers

		tpGS.gsap.set([_lg,'.layer_grid'],{width:w+"px", maxWidth:maxW, height:h+"px"});
		tpGS.gsap.set(RVS.C.UL,{	minWidth:(parseInt(nminw,0)+parseInt(pd,0))+"px",
									maxWidth:maxW, width:nw,
									minHeight:(parseInt(minh,0)+parseInt(pd,0))});


		//tpGS.gsap.set(_ib,{minHeight:(minh + RVS.ENV.globVerOffset)});


		RVS.S.ulDIM = {width:RVS.C.UL.width(), height:RVS.C.UL.height()};
		RVS.RMD = RVS.SLIDER.settings.type==="carousel" ? {width:w, height:h} : {width:RVS.S.ulDIM.width, height:RVS.S.ulDIM.height};


		var __L = Math.max(0,((RVS.S.ulDIM.width)/2 - w/2)),
			__T = Math.max(0,((RVS.S.ulDIM.height) - h) / 2);

		// OFFSET PLACE FOR CAROUSEL PADDINGS AND NAVIGATION OUTER CONTAINERS
		tpGS.gsap.set(RVS.C.UL,{minHeight:(RVS.S.ulDIM.height + RVS.S.dim_offsets.carouseloffset + RVS.S.dim_offsets.navtop + RVS.S.dim_offsets.navbottom), minWidth:(/*RVS.S.ulDIM.width*/ nminw)});



		__T = __T + RVS.S.dim_offsets.carouseltop + RVS.S.dim_offsets.navtop;
		__L = Math.max(0,__L);
		__T = Math.max(0,__T);



		//RVS.S.layer_grid_offset = RVS.S.layer_grid_offset===undefined ? {left:0, top:__T}  : RVS.S.layer_grid_offset;
		//RVS.S.layer_grid_offset.top = __T;



		var __slide = document.getElementById('slide_'+RVS.S.slideId);

		if (RVS.SLIDER.settings.type==="carousel") {
			if (__slide) tpGS.gsap.set(__slide,{width:w,height:h, top:__T, left:__L, overflow:"hidden",borderRadius:RVS.SLIDER.settings.carousel.borderRadius});
			tpGS.gsap.set(['.layer_grid'],{x:0,y:0,left:"0px", top:"0px"});
			tpGS.gsap.set(_lg,{x:0,y:0,left:__L+"px", top:__T});
			tpGS.gsap.set('.slots_wrapper',{top:0, left:0, maxWidth:"none", maxHeight:"none"});
		} else {

			if (__slide) tpGS.gsap.set(__slide,{width:"100%",height:"100%", top:0, left:0, overflow:"visible",borderRadius:0});
			tpGS.gsap.set([_lg,'.layer_grid'],{x:0,y:0,left:__L+"px", top:__T});
			tpGS.gsap.set('.slots_wrapper',{top:0, left:0, maxWidth:Math.max(RVS.S.ulDIM.width,(_rvbin.width()))+"px", maxHeight:(RVS.S.ulDIM.height)+"px"});
		}

		// UPDATE FIELDS, THAN SET VALUES AS NEEDED
		if (updateFields) updateSlideDimensionFields();
		if (updateShrinks) RVS.F.updateScreenShrinks();

		RVS.F.updateContentDeltas();

		//DRAW CAROUSEL FAKES IN CASE WE ARE IN CAROUSEL MODE
		if (RVS.SLIDER.settings.type==="carousel")
			drawFakeCarousels({width:w, height:h, top:__T, left:__L});
		else
			jQuery('.fakecarouselslide').remove();

		// REPOSITION ALL THE CONTENT INSIDE
		RVS.F.sliderNavPositionUpdate({type:"arrows"});
		RVS.F.sliderNavPositionUpdate({type:"bullets"});
		RVS.F.sliderNavPositionUpdate({type:"tabs"});
		RVS.F.sliderNavPositionUpdate({type:"thumbs"});
		revsliderScrollable("update");
		RVS.F.setRulers();
		if (RVS.S.firstPreparation===undefined || RVS.S.firstPreparation<2) {
			RVS.S.firstPreparation = RVS.S.firstPreparation===undefined ? 0 : RVS.S.firstPreparation;
			RVS.S.firstPreparation++;
			if (RVS.S.firstPreparation==2)  RVS.F.expandCollapseTimeLine(true,"open",true);
		}
	}

	// UPDATE THE SCREEN SHRINKS ON DIFFERENT DEVICES
	RVS.F.updateScreenShrinks = function()  {
		var ld = RVS.F.GW("d");
		for (var i in RVS.V.sizes) {
			if(!RVS.V.sizes.hasOwnProperty(i)) continue;
			var _s = RVS.V.sizes[i],
				custom = RVS.SLIDER.settings.size.custom[_s],
				w = custom ? RVS.F.GW(_s) : Math.min(ld,RVS.ENV.grid_sizes[_s]);

			RVS.S.shrink[_s] = w / ld;
			ld = w;
		}
	};

	function updateSlideDimensionFields() {

		for (var i in RVS.V.sizes) {
			if(!RVS.V.sizes.hasOwnProperty(i)) continue;
			var _s = RVS.V.sizes[i],
				custom = RVS.SLIDER.settings.size.custom[_s],
				ld = getLastBiggerSliderDimension(_s),
				// CALCULATE WIDTH AND HEIGHT OF LAYER GRID
				w = custom ? RVS.F.GW(_s) : Math.min(ld.w,RVS.ENV.grid_sizes[_s]),
				h = custom ? parseInt(RVS.SLIDER.settings.size.height[_s],0) : (w / ld.w) * ld.h;

			w = Math.round(w);
			h = Math.round(h);
			jQuery('#sr_size_width_'+_s).val(w+"px");
			jQuery('#sr_size_height_'+_s).val(h+"px");
		}

		var nmw = RVS.SLIDER.settings.size.maxWidth==="none" || RVS.SLIDER.settings.size.maxWidth===0 || RVS.SLIDER.settings.size.maxWidth==="" ? "none" : RVS.SLIDER.settings.size.maxWidth;
		jQuery('#sr_size_maxwidth').val(nmw);
		jQuery('#sr_size_minheight').val(RVS.SLIDER.settings.size.minHeight);
		jQuery('#sr_size_minheight_fs').val(RVS.SLIDER.settings.size.minHeightFullScreen);
		if (inf_s_width!==undefined) inf_s_width.innerHTML = Math.round(RVS.F.GW(RVS.screen))+"px";
	}

	/*
	DRAW FAKE CAROUSELS
	*/
	function drawFakeCarousels(obj) {

		var _ = RVS.SLIDER.settings,
			wrap = jQuery('#fake_carousel_elements'),
			leftoffset = 0,
			side = 1,
			ha = _.carousel.horizontal==="center" ? 2 : 1,
			d = 0,
			scaleoffset = 0;
		jQuery('.fakecarouselslide').hide();
		for (var ci = 1;ci<_.carousel.maxItems;ci++) {
			var fc = jQuery('#fakecarouselslide_'+ci),
				tr;
			if (fc.length===0) {
				fc = jQuery('<div class="fakecarouselslide" id="fakecarouselslide_'+ci+'"></div>');
				wrap.append(fc);
			}

			d = ci % 2 === 1 ? d+1 : d;


			tr = {
						width:obj.width,
						height:obj.height,
						top:obj.top,
						borderRadius:_.carousel.borderRadius,
						display:"block"
						};

			// SET SCALE DOWNS
			var sdown = parseInt(_.carousel.scaleDown,0)/100,
				mrot = parseInt(_.carousel.maxRotation,0),
				mfad = parseInt(_.carousel.maxOpacity,0)/100;

			// SET FADEOUT OF ELEMENT
			if (_.carousel.fadeOut)
				if (_.carousel.varyFade) tr.autoAlpha = 1-Math.abs(((mfad/Math.ceil(_.carousel.maxItems/ha))*d));
				else tr.autoAlpha = d>=1 || d<=-1 ?  mfad : mfad + ((1-mfad)*(1-Math.abs(d)));
			else
				tr.autoAlpha = Math.abs(d)<Math.ceil((_.carousel.maxItems/ha)) ? 1 : 0;


			if (_.carousel.scale && _.carousel.scaleDown!==undefined && sdown >0) {
				if (_.carousel.varyScale)
					tr.scale = 1- Math.abs((((1-sdown)/Math.ceil(_.carousel.maxItems/ha))*d));
				else
					tr.scale = d*side>=1 || d*side<=-1 ? sdown : (100-( sdown*Math.abs(d)));
				 scaleoffset = d * (tr.width - tr.width*tr.scale)/2;
			} else tr.scale = 1;

			leftoffset = ci % 2 === 1 ? parseFloat(leftoffset) + parseFloat(obj.width) + (parseInt(_.carousel.space,0) * (_.carousel.offsetScale ? tr.scale : 1)) : leftoffset;
			tr.left = parseFloat(obj.left) + (side * leftoffset);

			// ROTATION FUNCTIONS
			if (_.carousel.rotation && _.carousel.maxRotation!==undefined && Math.abs(mrot)!=0)	{
				if (_.carousel.varyRotate) {
					tr.rotationY = Math.abs(mrot) - Math.abs((1-Math.abs(((1/Math.ceil(_.carousel.maxItems/ha))*d))) * mrot);
					tr.autoAlpha = Math.abs(tr.rotationY)>90 ? 0 : tr.autoAlpha;
				} else {
					tr.rotationY = d*side>=1 || d*side<=-1 ?  mrot : Math.abs(d)*mrot;
				}
				tr.rotationY = tr.rotationY*side*-1;
			} else {
				tr.rotationY = 0;
			}

			// ADD EXTRA SPACE ADJUSTEMENT IF COVER MODE IS SELECTED
			if (tr.scale!==undefined && tr.scale!==1) tr.left = side<0 ? tr.left + scaleoffset : tr.left - scaleoffset;

			// ZINDEX ADJUSTEMENT
			tr.zIndex = Math.round(100-Math.abs(d*5));

			tr.force3D = true;
			// TRANSFORM STYLE
			tr.transformStyle =  "flat";
			tr.transformPerspective = 1200;
			tr.transformOrigin = "50% "+_.carousel.vertical;

			tpGS.gsap.set(fc,tr);

			side = side * -1;

		}
	}


	/*
	UPDATE SLIDER BACKGROUND
	*/
	function setSliderBG(force) {
		requestAnimationFrame(function() {
			tpGS.gsap.set(RVS.S.ulInner,{backgroundImage:""});
			var _ = RVS.SLIDER.settings,
				sbg = window.RSColor.get(_.layout.bg.color),
				is = _.layout.bg.useImage && _.layout.bg.image!==undefined ? _.layout.bg.image : "";

			//if (RVS.S.defaultMainPNG===undefined) RVS.S.defaultMainPNG = jQuery('.slotwrapper_prev .defaultimg').css('backgroundImage');

			//BG IMAGE OF THE SLIDER
			if (is!=="") tpGS.gsap.set([RVS.S.ulInner,'#slider_bg_image'],{backgroundPosition:_.layout.bg.position, 'background-size':_.layout.bg.fit, backgroundRepeat:_.layout.bg.repeat,backgroundImage:"url("+is+")"});
			else
			//BG COLOR OF SLIDER
			if (sbg.indexOf("gradient")>=0)
				tpGS.gsap.set([RVS.S.ulInner,'#slider_bg_image'],{background:sbg});
			else
			if (sbg!=='transparent')
				tpGS.gsap.set([RVS.S.ulInner,'#slider_bg_image'],{backgroundColor:sbg, backgroundImage:"none"});
			else
				tpGS.gsap.set([RVS.S.ulInner,'#slider_bg_image'],{backgroundColor:"transparent", backgroundImage:RVS.S.defaultMainPNG, backgroundRepeat:"repeat", 'background-size':"16px 16px"});

			drawBGOverlay();
			setTimeout(function() {
					RVS.F.updateEasyInputs({container:jQuery('#slider_used_library'), trigger:"init"});
					RVS.F.updateEasyInputs({container:jQuery('#slider_used_library_lists'), trigger:"init"});
					RVS.F.updateEasyInputs({container:jQuery('#slider_bg_inputfields'), trigger:"init"});
			},100);
		});
	}

	function drawBGOverlay() {
		RVS.C.sliderOverlay = RVS.C.sliderOverlay===undefined ? document.getElementById('slider_overlay') : RVS.C.sliderOverlay;
		RVS.C.sliderOverlay.style.backgroundImage = RVS._R.createOverlay("slider",RVS.SLIDER.settings.layout.bg.dottedOverlay,RVS.SLIDER.settings.layout.bg.dottedOverlaySize,{0:RVS.SLIDER.settings.layout.bg.dottedColorA,1:RVS.SLIDER.settings.layout.bg.dottedColorB});
	}

	/*
	UPDATE SLIDER PROGRESS BAR
	*/
	function setProgressBar() {
		requestAnimationFrame(progressBarDraw);
	}

	function progressBarDraw() {
		var p = RVS.SLIDER.settings.general.progressbar;
		RVS.C.pbar = RVS.C.pbar===undefined ? jQuery('#rev_progress_bar_wrap') : RVS.C.pbar;
		RVS.C.pbar_lc = RVS.C.pbar_lc===undefined ? document.getElementById('progressbar_selector_left-center') : RVS.C.pbar_lc;
		RVS.C.pbar_rc = RVS.C.pbar_rc===undefined ? document.getElementById('progressbar_selector_right-center') : RVS.C.pbar_rc;
		RVS.C.pbar_ct = RVS.C.pbar_ct===undefined ? document.getElementById('progressbar_selector_center-top') : RVS.C.pbar_ct;
		RVS.C.pbar_cb = RVS.C.pbar_cb===undefined ? document.getElementById('progressbar_selector_center-bottom') : RVS.C.pbar_cb;
		RVS.C.pbar_cc = RVS.C.pbar_cc===undefined ? document.getElementById('progressbar_selector_center-center') : RVS.C.pbar_cc;
		RVS.C.pbar_lc = RVS.C.pbar_lc===undefined ? document.getElementById('progressbar_selector_left-center') : RVS.C.pbar_lc;

		//RVS.C.pbar.detach();

		if (p.alignby==="grid" && RVS.C.layergrid!==undefined) RVS.C.layergrid.append(RVS.C.pbar); else RVS.S.ulInner.append(RVS.C.pbar);

		// SHOW / HIDE ALIGMENTS WHICH ARE NOT AVAILABLE
		if (p.style==="horizontal") {
			tpGS.gsap.set([RVS.C.pbar_lc,RVS.C.pbar_rc],{display:"block"});
			tpGS.gsap.set([RVS.C.pbar_ct,RVS.C.pbar_cb,RVS.C.pbar_cc],{display:"none"});
		} else
		if (p.style==="vertical") {
			tpGS.gsap.set([RVS.C.pbar_ct,RVS.C.pbar_cb],{display:"block"});
			tpGS.gsap.set([RVS.C.pbar_lc,RVS.C.pbar_rc,RVS.C.pbar_cc],{display:"none"});
		} else {
			tpGS.gsap.set([RVS.C.pbar_ct,RVS.C.pbar_cb,RVS.C.pbar_lc,RVS.C.pbar_rc,RVS.C.pbar_cc],{display:"block"});
		}
		var pFW = p.alignby==="grid" ? RVS.S.lgw : RVS.S.ulDIM.width,
			pFH = p.alignby==="grid" ? RVS.S.lgh : RVS.S.ulDIM.height;
		if (p.set && RVS.SLIDER.settings.style!=="hero") {
			if (p.style==="horizontal" || p.style==="vertical") {
				// ALL SLIDES
				var GAP = 3,	WNG,WWG;
				RVS.C.pbar[0].innerHTML = p.basedon==="module" ? '<div class="rev_progress_bar"></div><div class="rev_progress_bar"></div><div class="rev_progress_bgs"><div class="rev_progress_bg"></div><div class="rev_progress_bg"></div><div class="rev_progress_bg"></div><div class="rev_progress_bg"></div></div><div class="rev_progress_gap"></div><div class="rev_progress_gap"></div><div class="rev_progress_gap"></div>' : '<div class="rev_progress_bar"></div>';
				RVS.C.pbars = RVS.C.pbar[0].getElementsByClassName('rev_progress_bar');
				RVS.C.pbbgs = RVS.C.pbar[0].getElementsByClassName('rev_progress_bg');
				RVS.C.pbgaps = RVS.C.pbar[0].getElementsByClassName('rev_progress_gap');

				if (p.style==="horizontal") {
					WNG = Math.ceil(pFW / 4),
					WWG = Math.ceil((pFW - (GAP*parseInt(p.gapsize,0))) / 4);
					tpGS.gsap.set(RVS.C.pbar,{top:p.vertical==="top" ? p.y : p.vertical==="center" ? "50%" : "auto",
											  bottom:p.vertical==="top" || p.vertical==="center" ? "auto" : p.y,
											  y:p.vertical==="center" ? p.y : 0,
											  x:0,
											  left:0,
											  right:"auto",
											  width:"100%",
											  height:p.size,
											  marginTop:p.alignby==="grid" ? 0 : p.vertical==="bottom" ? 0 : p.vertical==="top" ? 0 : 0,
											  backgroundColor:p.basedon==="module" ? "transparent" : p.bgcolor
					});
					tpGS.gsap.set(RVS.C.pbars,{
						backgroundColor:p.color,
						y:0,
					 	x:p.basedon==="module" ? p.gap ? function(index) { return (p.horizontal==="right" ? GAP - index : index)*(WWG+parseInt(p.gapsize,0))} : function(index) { return (p.horizontal==="right" ? GAP - index : index)*WNG} : p.horizontal==="right" ? WNG : 0,
						width:p.basedon==="module" ? p.gap ? WWG+"px" : 100 / 4 +"%": "75%",
						height:"100%"

					});
					if (p.basedon==="module")
						tpGS.gsap.set(RVS.C.pbbgs,{
							backgroundColor:p.bgcolor,
							y:0,
							x:p.basedon==="module" ? p.gap ? function(index) { return index*(WWG+parseInt(p.gapsize,0))} : function(index) { return index*WNG} : 0,
							width:p.basedon==="module" ? p.gap ? WWG+"px" : 100 / 4 +"%": "75%",
							height:"100%"
						});


					tpGS.gsap.set(RVS.C.pbgaps,{
						backgroundColor:p.gapcolor,
						height:"100%",
						width:p.basedon==="module" ? p.gap ? p.gapsize+"px" : 0 : 0,
						y:0,
						x:p.basedon==="module" ? p.gap ? function(index) { return (index+1)*(WWG) +(parseInt(p.gapsize,0)*index)} : 0 : 0,
					});

				} else
				if (p.style==="vertical") {
					WNG = pFH / 4,
					WWG = (pFH - (GAP*parseInt(p.gapsize,0))) / 4;
					tpGS.gsap.set(RVS.C.pbar,{left:p.horizontal==="left" ? p.x : p.horizontal==="center" ? "50%" : "auto",
											  right:p.horizontal==="left" || p.horizontal==="center" ? "auto" : p.x,
											  x:p.horizontal==="center" ? p.x : 0,
											  y:p.alignby==="grid" ? 0 : 0,
											  top:0,
											  bottom:"auto",
											  height:"100%",
											  width:p.size,
											  marginLeft:p.alignby==="grid" ? 0 : p.horizontal==="left" ? 0 : p.horizontal==="right" ? 0 : 0,
											  backgroundColor:p.basedon==="module" ? "transparent" : p.bgcolor
					});
					tpGS.gsap.set(RVS.C.pbars,{
						backgroundColor:p.color,
					 	y:p.basedon==="module" ? p.gap ? function(index) { return (p.vertical==="bottom" ? GAP - index : index)*(WWG+parseInt(p.gapsize,0))} : function(index) { return (p.vertical==="bottom" ? GAP - index : index)*WNG} : p.vertical==="bottom" ? WNG : 0,
						height:p.basedon==="module" ? p.gap ? WWG+"px" : 100 / 4 +"%": "75%",
						width:"100%"
					});
					if (p.basedon==="module")
						tpGS.gsap.set(RVS.C.pbbgs,{
							backgroundColor:p.bgcolor,
							y:p.basedon==="module" ? p.gap? function(index) { return index*(WWG+parseInt(p.gapsize,0))} : function(index) { return index*WNG} : 0,
							height:p.basedon==="module" ? p.gap? WWG+"px" : 100 / 4 +"%": "75%",
							width:"100%"
						});
					tpGS.gsap.set(RVS.C.pbgaps,{
						backgroundColor:p.gapcolor,
						width:"100%",
						height:p.basedon==="module" ? p.gap ? p.gapsize+"px" : 0 : 0,
						x:0,
						y:p.basedon==="module" ? p.gap ? function(index) { return (index+1)*(WWG) +(parseInt(p.gapsize,0)*index)} : 0 : 0,
					});
				}

			} else {
				RVS.C.pbar[0].innerHTML = '<canvas width="'+p.radius*2+'" height="'+p.radius*2+'" style="position:absolute" class="rev_progress_bar"></canvas>';
				RVS.C.pbars = RVS.C.pbar[0].getElementsByClassName('rev_progress_bar')[0];
				tpGS.gsap.set(RVS.C.pbar,{top:p.vertical==="top" ? p.y : p.vertical==="center" ? "50%" : "auto",
										  bottom:p.vertical==="top" || p.vertical==="center" ? "auto" : p.y,
										  left:p.horizontal==="left" ? p.x : p.horizontal==="center" ? "50%" : "auto",
										  right:p.horizontal==="left" || p.horizontal==="center" ? "auto" : p.x,
										  y:p.vertical==="center" ? p.y : 0,
										  x:p.horizontal==="center" ? p.x : 0,
										  width:p.radius*2,
										  height:p.radius*2,
										  marginTop:p.vertical==="bottom" ? 0 : p.vertical==="top" ? 0 : 0 - p.radius,
										  marginLeft:p.horizontal==="left" ? 0 : p.horizontal==="right" ? 0 : 0 - p.radius,
										  backgroundColor:"transparent"
				});
				drawCWCCW();
			}
			RVS.C.pbar.removeClass("deactivated");
		} else RVS.C.pbar.addClass("deactivated");
		//RVS.S.ulInner.append(RVS.C.pbar);
	}

	function drawCWCCW(custom) {

		var p = RVS.SLIDER.settings.general.progressbar,
			c = RVS.C.pbars.getContext('2d'),
			posX = parseInt(p.radius),
			posY = parseInt(p.radius),
			deegres = p.style!=="cw" ? 294 : 64;

		c.lineCap = 'round';
		c.clearRect( 0, 0, p.radius*2, p.radius*2);

		c.beginPath();
		c.arc( posX, posY, p.radius-parseInt(p.size,0), (Math.PI/180) * 270, (Math.PI/180) * (270 + 360) );
		c.strokeStyle = custom!==undefined && custom.bgcolor!==undefined ? custom.bgcolor : p.bgcolor;
		c.lineWidth = parseInt(p.size,0)-1;
		c.stroke();

		c.beginPath();
		c.strokeStyle = custom!==undefined && custom.color!==undefined ? custom.color : p.color;
		c.lineWidth = parseInt(p.size,0);
		c.arc( posX, posY, p.radius-parseInt(p.size,0), (Math.PI/180) * 270, (Math.PI/180) * (270 + deegres), p.style!=="cw");
		c.stroke();
	}

	function colorEditSliderSub(n,val,canceled) {
		switch (n) {
			case "progressgapcolor": if (RVS.SLIDER.settings.general.progressbar.style==="horizontal" || RVS.SLIDER.settings.general.progressbar.style==="vertical") tpGS.gsap.set(RVS.C.pbgaps,{bakgroundColor:val});break;
			case "sliderprogresscolor":if (RVS.SLIDER.settings.general.progressbar.style==="horizontal" || RVS.SLIDER.settings.general.progressbar.style==="vertical") tpGS.gsap.set(RVS.C.pbars,{background:val}); else drawCWCCW({color:val});break;
			case "sliderprogresscolorbg":if (RVS.SLIDER.settings.general.progressbar.style==="horizontal" || RVS.SLIDER.settings.general.progressbar.style==="vertical") tpGS.gsap.set(RVS.C.pbar,{background:val}); else drawCWCCW({bgcolor:val});break;
			case "sliderbgcolor": if (canceled) setSliderBG(); else tpGS.gsap.set(RVS.C.UL,{background:val});break;
			case "sliderTabBgColor": RVS.F.bgUpdate("tabs", val);break;
			case "sliderThumbBgColor": RVS.F.bgUpdate("thumbs", val);break;
			case "module_spinner_color":setSpinnerColors(val);break;
		}
	}

	/*
	EDIT / CANCEL A COLOR VALUE (SHOW LIVE THE CHANGES)
	*/
	function colorEditSlider(e,inp, val,gradient,onSave,GC) {
		var canceled = false;
		if (inp!==undefined) window.lastColorEditjObj = jQuery(inp);
		else {
			if (window.lastColorEditjObj!==undefined) val = window.RSColor.get(window.lastColorEditjObj.val());
			canceled = true;
		}
		if (val===undefined) return;


		// STYLE CHANGES -> REWRITE STYLE TAG IN CONTAINER !!!
		if (window.lastColorEditjObj[0].dataset.navcolor==1) RVS.F.drawNavigation({type:window.lastColorEditjObj[0].dataset.evtparam,color:val,attribute:window.lastColorEditjObj[0].name});
		else colorEditSliderSub(window.lastColorEditjObj[0].name,val,canceled);

		if (GC && canceled!==true)
			for (var i in GC.ref) {
				if (GC.ref[i].type==="slider") colorEditSliderSub(GC.ref[i].inpname,val,canceled);
				if (GC.ref[i].type==="navstyle") RVS.F.drawNavigation({type:GC.ref[i].evtparam,color:val,attribute:GC.ref[i].inpname});
				if ((GC.ref[i].type==="slider" || GC.ref[i].type==="navstyle") && onSave) {
					RVS.F.updateSliderObj({path:GC.ref[i].r,val:val});
					var upinp = jQuery('input[name='+GC.ref[i].inpname+']');
					if (upinp.length>0) {
						upinp[0].value = val;
						upinp.rsColorPicker("refresh");
					}
				}
			}
	}


	function getNewSliderObject(obj) {
		var newSlider = {};

		/* SLIDE ADDONS */
		newSlider.addOns = RVS.F.safeExtend(true,{},obj.addOns) || {};

		/* VERSION CHECK */
        newSlider.version = RVS.ENV.revision;

		/* SLIDER BASICS */
		newSlider.alias = _d(obj.alias,"");

		newSlider.shortcode = _d(obj.shortcode,"");
		newSlider.type = _d(obj.type,"standard");
		newSlider.layouttype = _d(obj.layouttype,"fullwidth");
		newSlider.sourcetype= _d(obj.sourcetype,"gallery");
		newSlider.title = _d(obj.title,"New Slider");
		newSlider.googleFont = _d(obj.googleFont,[]);
		newSlider.id = _d(obj.id,"");
		newSlider.class = _d(obj.class,"");
		newSlider.wrapperclass = _d(obj.wrapperclass,"");

		newSlider.snap = _d(obj.snap,{
			adjust:"none",
			snap:false,
			helpLines:false,
			gap:20
		});

		/* SLIDER SOURCE */
		newSlider.source = _d(obj.source,{
			gallery:{},
			post:{
				excerptLimit:55,
				maxPosts:30,
				fetchType:"cat_tag"	,
				category:"",
				sortBy:"ID",
				types:"post",
				list:"",
				sortDirection:"DESC",
				subType:"post"
			},
			woo:{
				excerptLimit:55,
				maxProducts:30,
				featuredOnly:false,
				inStockOnly:false,
				category:"",
				sortBy:"ID",
				types:"product",
				sortDirection:"DESC",
				regPriceFrom:"",
				regPriceTo:"",
				salePriceFrom:"",
				salePriceTo:""
			},
			instagram:{
				count:8,
				hashTag:"",
				transient:1200,
				type:"user",
				userId:"",
				token_source:"account",
				connect_with:""
			},
			facebook:{
				album:"",
				appId:"",
				appSecret:"",
				count:8,
				transient:1200,
				typeSource:"timeline",
				token_source:"account",
				connect_with:"",
				page_id:""
			},
			flickr:{
				apiKey:"",
				count:8,
				galleryURL:"",
				groupURL:"",
				photoSet:"",
				transient:1200,
				type:"publicphotos",
				userURL:""
			},
			twitter:{
				accessSecret:"",
				accessToken:"",
				consumerKey:"",
				consumerSecret:"",
				count:8,
				excludeReplies:false,
				imageOnly:false,
				includeRetweets:false,
				transient:1200,
				userId:""
			},
			vimeo:{
				albumId:"",
				channelName:"",
				count:8,
				transient:1200,
				groupName:"",
				typeSource:"user",
				userName:""
			},
			youtube:{
				api:"",
				channelId:"",
				count:8,
				playList:"",
				transient:1200,
				typeSource:"channel"
			}
		});

		if (newSlider.source.facebook !==undefined) delete newSlider.source.facebook.pageURL;

		if (newSlider.source!==undefined && newSlider.source.post!==undefined && (""+newSlider.source.post.excerptLimit).indexOf('chars')==-1 && (""+newSlider.source.post.excerptLimit).indexOf('char')>=0) newSlider.source.post.excerptLimit = newSlider.source.post.excerptLimit.replace("char","words");
		if (newSlider.source!==undefined && newSlider.source.woo!==undefined && (""+newSlider.source.woo.excerptLimit).indexOf('chars')==-1 && (""+newSlider.source.woo.excerptLimit).indexOf('char')>=0) newSlider.source.woo.excerptLimit = newSlider.source.woo.excerptLimit.replace("char","words");

		// Default DoubleChecks
		if (newSlider.sourcetype==="youtube") {
			if (newSlider.source.youtube.count==="") newSlider.source.youtube.count=8;
			if (newSlider.source.youtube.channelId==="") newSlider.source.youtube.channelId="UCpVm7bg6pXKo1Pr6k5kxG9A";
		}

		if (newSlider.sourcetype==="vimeo") {
			if (newSlider.source.vimeo.count==="") newSlider.source.vimeo.count=8;
		}

		if (newSlider.sourcetype==="twitter") {
			if (newSlider.source.twitter.count==="") newSlider.source.twitter.count=8;
		}

		if (newSlider.sourcetype==="flickr") {
			if (newSlider.source.flickr.count==="") newSlider.source.flickr.count=8;
		}

		if (newSlider.sourcetype==="facebook") {
			if (newSlider.source.facebook.count==="") newSlider.source.facebook.count=8;
		}


		/* SLIDER DEFAULTS */
		newSlider.def = _d(obj.def,{
			intelligentInherit:true,
			autoResponsive:true,
			responsiveChilds:true,
			responsiveOffset:true,
			transition:"fade",
			transitionDuration:300,
			delay:9000,
			background:{
				fit:"cover",
				fitX:100,
				fitY:100,
				position:"center center",
				positionX:0,
				positionY:0,
				repeat:"no-repeat"
			},
			panZoom:{
				set:false,
				blurStart:0,
				blurEnd:0,
				duration:10000,
				ease:"none",
				fitEnd:100,
				fitStart:100,
				xEnd:0,
				yEnd:0,
				xStart:0,
				yStart:0,
				rotateStart:0,
				rotateEnd:0
			}
		});

		newSlider.def.intelligentInherit = newSlider.def.intelligentInherit===undefined ? true : newSlider.def.intelligentInherit;
		newSlider.def.autoResponsive = newSlider.def.autoResponsive===undefined ? true : newSlider.def.autoResponsive;
		newSlider.def.responsiveChilds = newSlider.def.responsiveChilds===undefined ? true : newSlider.def.responsiveChilds;
		newSlider.def.responsiveOffset = newSlider.def.responsiveOffset===undefined ? true : newSlider.def.responsiveOffset;

		/* SLIDER SIZE */
		newSlider.size = _d(obj.size,{
			enableUpscaling:false,
			respectAspectRatio:false,
			disableForceFullWidth:false,
			custom:{d:true,n:false,t:false,m:false},
			minHeightFullScreen:"",
			minHeight:"",
			maxWidth:0,
			maxHeight:0,
			fullScreenOffsetContainer:"",
			fullScreenOffset:"",
			width:{d:1240,n:1024,t:778,m:480},
			height:{d:900,n:768,t:960,m:720},
			editorCache:{d:0,n:0,t:0,m:0},
			overflow:false,
			useFullScreenHeight:true,
			overflowHidden:false,
			gridEQModule:false,
			forceOverflow:false,
			keepBPHeight:false,
			ignoreHeightChanges:true
		});
		newSlider.size.editorCache = newSlider.size.editorCache===undefined ? {d:0, n:0, t:0, m:0} : newSlider.size.editorCache;
		newSlider.size.editorCache.d = newSlider.size.editorCache.d === 0 ? newSlider.size.height.d : newSlider.size.editorCache.d;
		newSlider.size.editorCache.n = newSlider.size.editorCache.n === 0 ? newSlider.size.height.n : newSlider.size.editorCache.n;
		newSlider.size.editorCache.t = newSlider.size.editorCache.t === 0 ? newSlider.size.height.t : newSlider.size.editorCache.t;
		newSlider.size.editorCache.m = newSlider.size.editorCache.m === 0 ? newSlider.size.height.m : newSlider.size.editorCache.m;

		/* SLIDER CODES */
		newSlider.codes = _d(obj.codes,{
			css:"",
			javascript:""
		});

		/* CAROUSEL SETTINGS */
		newSlider.carousel = _d(obj.carousel,{
			justify:false,
			justifyMaxWidth:false,
			snap:true,
			borderRadius:0,
			borderRadiusUnit:"px",
			ease:"power3.inOut",
			fadeOut:true,
			scale:false,
			offsetScale:false,
			horizontal:"center",
			vertical:"center",
			infinity:false,
			maxItems:3,
			maxRotation:0,
			maxOpacity:100,
			paddingTop:0,
			paddingBottom:0,
			rotation:false,
			scaleDown:50,
			space:0,
			speed:800,
			stretch:false,
			varyFade:false,
			varyRotate:false,
			varyScale:false	,
			showAllLayers:'false'
		});

		newSlider.carousel.showAllLayers=newSlider.carousel.showAllLayers==="true" || newSlider.carousel.showAllLayers===true ? "all" : newSlider.carousel.showAllLayers;

		/* HERO SETTINGS */
		newSlider.hero = _d(obj.hero,{
			activeSlide:-1
		});

		/* SLIDER LAYOUT  - BG, LOADER, POSITION */
		newSlider.layout = _d(obj.layout,{
			bg:{
				color:"transparent",
				padding:0,
				dottedOverlay:"none",
				dottedOverlaySize:1,
				dottedColorA:"transparent",
				dottedColorB:"#000000",
				shadow:0,
				useImage:false,
				image:"",
				imageSourceType:"full",
				fit:"cover",
				position:"center center",
				repeat:"no-repeat"
			},
			spinner:{
				color:"#ffffff",
				type:"off"

			},
			position:{
				marginTop:0,
				marginBottom:0,
				marginLeft:0,
				marginRight:0,
				align:"center",
				fixedOnTop:false,
				addClear:false
			}
		});
		if (newSlider!==undefined && newSlider.layout!==undefined && newSlider.layout.bg!==undefined) {
			if (newSlider.layout.bg.dottedOverlay.indexOf("white")>0) newSlider.layout.bg.dottedColorB = "rgba(255,255,255,255)";
			if (newSlider.layout.bg.dottedOverlay.indexOf("twoxtwo")>=0) newSlider.layout.bg.dottedOverlay = "1";
			else if (newSlider.layout.bg.dottedOverlay.indexOf("threexthree")>=0) newSlider.layout.bg.dottedOverlay = "2";
		}

		/* SLIDER VISIBILITY */
		newSlider.visibility = _d(obj.visibility,{
			hideSelectedLayersUnderLimit:0,
			hideAllLayersUnderLimit:0,
			hideSliderUnderLimit:0
		});


		/* GENERAL SETTINGS */
		newSlider.general = _d(obj.general,{
			slideshow:{
				slideShow:true,
				stopOnHover:false,
				stopSlider:false,
				stopAfterLoops:0,
				stopAtSlide:1,
				shuffle:false,
				loopSingle:false,
				viewPort:false,
				viewPortStart:"wait",
				viewPortArea:RVS.F.cToResp({default:"200px"}),
				presetSliderHeight:false,
				initDelay:0,
				waitForInit:false
			},
			progressbar:{
				set:false,
				alignby:"slider",
				style:"horizontal",
				size:"5px",
				radius:10,
				vertical:"bottom",
				horizontal:"left",
				x:0,
				y:0,
				color:'rgba(255,255,255,0.5)',
				bgcolor:'transparent',
				basedon:"slide",
				gapsize:0,
				gap:false,
				gapcolor:'rgba(255,255,255,0.5)',
				reset:"reset",
				visibility:{
					d:true,
					m:true,
					n:true,
					t:true,
				}
			},
			firstSlide:{
				set:false,
				duration:300,
				slotAmount:7,
				type:"fade",
				alternativeFirstSlideSet:false,
				alternativeFirstSlide:1
			},
			icache:"default",
			DPR:'dpr',
			observeWrap:false,
			layerSelection:false,
			lazyLoad:"none",
			nextSlideOnFocus:false,
			disableFocusListener:false,
			enableurlhash:false,
			disableOnMobile:false,
			autoPlayVideoOnMobile:true,
			disablePanZoomMobile:false,
			useWPML:false,
			perspective:600,
			perspectiveType:"global"
		});


		if (newSlider.general.progressbar!==undefined) {

			if(newSlider.general.progressbar.height!==undefined) {
				newSlider.general.progressbar.size = newSlider.general.progressbar.height;
				delete newSlider.general.progressbar.height;
			}
			if (newSlider.general.progressbar.position!==undefined) {
				newSlider.general.progressbar.vertical = newSlider.general.progressbar.position;
				delete newSlider.general.progressbar.position;
			}
		}
		if (newSlider.general.perspectiveType===undefined) newSlider.general.perspectiveType='local';
		if (newSlider.general.perspective===undefined) newSlider.general.perspective=600;
		if (typeof newSlider.general.slideshow.viewPortArea!=="object") newSlider.general.slideshow.viewPortArea = RVS.F.cToResp({default:newSlider.general.slideshow.viewPortArea});
		//newSlider.general.slideshow.slideShow =  (newSlider.general.slideshow.stopSlider===true && (newSlider.general.stopAfterLoops===0 || newSlider.general.stopAfterLoops===undefined) && (newSlider.general.stopAtSlide===1 || newSlider.general.stopAtSlide===undefined)) ? false : true;
		/* SLIDER NAVIGATION */
		if (obj!==undefined && obj.nav!==undefined && obj.nav.swipe!==undefined) {
			obj.nav.swipe.setMobileCarousel = obj.nav.swipe.setMobileCarousel===undefined ? true : obj.nav.swipe.setMobileCarousel;
			obj.nav.swipe.setDesktopCarousel = obj.nav.swipe.setDesktopCarousel===undefined ? true : obj.nav.swipe.setDesktopCarousel;
		}
		newSlider.nav = _d(obj.nav,{
			preview:{
				width:50,
				height:100
			},
			swipe:{
				set:false,
				setOnDesktop:false,
				setMobileCarousel:true,
				setDesktopCarousel:true,
				blockDragVertical:false,
				direction:"horizontal",
				minTouch:1,
				velocity:75
			},
			keyboard:{
				direction:"horizontal",
				set:false
			},
			mouse:{
				set:'off',
				reverse:"default",
				viewport:50,
				calldelay:1000
				/*msWayUp:"top",
				msWayDown:"top",
				msWayUpOffset:0,
				msWayDownOffset:0*/
			},
			arrows:{
				set:false,
				rtl:false,
				animSpeed:"1000ms",
				animDelay:"1000ms",
				style:"1000",
				preset:"default",
				presets:{},
				alwaysOn:true,
				hideDelay:200,
				hideDelayMobile:1200,
				hideOver:false,
				hideOverLimit:0,
				hideUnder:false,
				hideUnderLimit:778,
				left:{
					anim:"fade",
					horizontal:"left",
					vertical:"center",
					offsetX:30,
					offsetY:0,
					align:"slider"
				},
				right:{
					anim:"fade",
					horizontal:"right",
					vertical:"center",
					offsetX:30,
					offsetY:0,
					align:"slider"
				}

			},
			thumbs:{
				anim:"fade",
				animSpeed:"1000ms",
				animDelay:"1000ms",
				set:false,
				rtl:false,
				style:"2000",
				preset:"default",
				presets:{},
				alwaysOn:true,
				hideDelay:200,
				hideDelayMobile:1200,
				hideOver:false,
				hideOverLimit:0,
				hideUnder:false,
				hideUnderLimit:778,
				spanWrapper:false,
				horizontal:"center",
				vertical:"bottom",
				amount:5,
				direction:"horizontal",
				height:50,
				width:100,
				widthMin:100,
				innerOuter:"inner",
				offsetX:0,
				offsetY:20,
				space:5,
				align:"slider",
				padding:5,
				wrapperColor:"transparent",
				mhoffset:0,
				mvoffset:0
			},
			tabs:{
				anim:"fade",
				animSpeed:"1000ms",
				animDelay:"1000ms",
				set:false,
				rtl:false,
				style:"4000",
				preset:"default",
				presets:{},
				alwaysOn:true,
				hideDelay:200,
				hideDelayMobile:1200,
				hideOver:false,
				hideOverLimit:0,
				hideUnder:false,
				hideUnderLimit:778,
				spanWrapper:false,
				horizontal:"center",
				vertical:"bottom",
				amount:5,
				direction:"horizontal",
				height:50,
				width:100,
				widthMin:100,
				innerOuter:"inner",
				offsetX:0,
				offsetY:20,
				space:5,
				align:"slider",
				padding:5,
				wrapperColor:"transparent",
				mhoffset:0,
				mvoffset:0
			},
			bullets:{
				anim:"fade",
				animSpeed:"1000ms",
				animDelay:"1000ms",
				set:false,
				rtl:false,
				style:"3000",
				preset:"default",
				presets:{},
				alwaysOn:true,
				horizontal:"center",
				vertical:"bottom",
				direction:"horizontal",
				offsetX:0,
				offsetY:20,
				align:"slider",
				space:5,
				/* alwaysOn:false, */
				hideDelay:200,
				hideDelayMobile:1200,
				hideOver:false,
				hideOverLimit:0,
				hideUnder:false,
				hideUnderLimit:778,
			}
		});

		if (newSlider.nav!==undefined) {
			if (newSlider.nav.arrows!==undefined && (newSlider.nav.arrows.style=="" || newSlider.nav.arrows.style==undefined)) newSlider.nav.arrows.style="1000";
			if (newSlider.nav.thumbs!==undefined && (newSlider.nav.thumbs.style=="" || newSlider.nav.thumbs.style==undefined)) newSlider.nav.thumbs.style="2000";
			if (newSlider.nav.bullets!==undefined && (newSlider.nav.bullets.style=="" || newSlider.nav.bullets.style==undefined)) newSlider.nav.bullets.style="3000";
			if (newSlider.nav.tabs!==undefined && (newSlider.nav.tabs.style=="" || newSlider.nav.tabs.style==undefined)) newSlider.nav.tabs.style="4000";
		}

		/* TROUBLESHOOTING & FALLBACKS */
		newSlider.troubleshooting = _d(obj.troubleshooting,{
			alternateImageType:"off",
			alternateURL:"",
			jsNoConflict:false,
			jsInBody:false,
			outPutFilter:"none",
			simplify_ie8_ios4:false
		});

		/* PARALLAX SETTINGS */
		newSlider.parallax = _d(obj.parallax,{
			set:false,
			setDDD:false,
			disableOnMobile:false,
			levels:[5,10,15,20,25,30,35,40,45,46,47,48,49,50,51,30],
			ddd:{
				BGFreeze:false,
				layerOverflow:false,
				overflow:false,
				shadow:false,
				zCorrection:65
			},
			mouse:{
				speed:0,
				bgSpeed:0,
				layersSpeed:0,
				origo:"slidercenter",
				type:"scroll"
			}
		});



		/* SLIDER AS MODAL */
		newSlider.modal = _d(obj.modal,{
			bodyclass:"",
			horizontal:"center",
			vertical:"middle",
			cover:true,
			coverColor:"rgba(0,0,0,0.5)",
			coverSpeed:1000
		});

		if (newSlider.modal!==undefined) newSlider.modal.coverSpeed = newSlider.modal.coverSpeed<10 ? newSlider.modal.coverSpeed*1000 : newSlider.modal.coverSpeed;


		/* SCROLLEFFECTS */
		newSlider.scrolleffects = _d(obj.scrolleffects,{
			set:false,
			setBlur:false,
			setFade:false,
			setGrayScale:false,
			bg:false,
			direction:"both",
			layers:false,
			maxBlur:10,
			multiplicator:"1.3",
			multiplicatorLayers:"1.3",
			disableOnMobile:false,
			parallaxLayers:false,
			staticLayers:false,
			staticParallaxLayers:false,
			tilt:30
		});

		/* SCROLL TIMELINE */
		newSlider.scrolltimeline = _d(obj.scrolltimeline,{
			set:false,
			fixed:false,
			fixedStart:2000,
			fixedEnd:4000,
			layers:false,
			ease:"none",
			speed:500
		});

		newSlider.skins = _d(obj.skins,{colorsAtStart:false});

		/* Access Permissions */

		newSlider.use_access_permissions = _d(obj.use_access_permissions, false);
		newSlider.allow_groups = {};
		if (obj.allow_groups) {
			for (var i in obj.allow_groups) if (obj.allow_groups.hasOwnProperty(i)) {
				if (typeof obj.allow_groups[i] == 'number') {
					newSlider.allow_groups['group' + obj.allow_groups[i]] = true;
				} else {
					newSlider.allow_groups[i] = obj.allow_groups[i];
				}
			}
		}

		// MIGRATION ISSUES FIX
		newSlider.source.post.fetchType=newSlider.source.post.fetchType===undefined ? "cat_tag" : newSlider.source.post.fetchType;
		newSlider.source.instagram.hashTag=newSlider.source.instagram.hashTag===undefined ? "" : newSlider.source.instagram.hashTag;
		newSlider.source.instagram.transient=newSlider.source.instagram.transient===undefined ? 1200 : newSlider.source.instagram.transient;
		newSlider.source.instagram.type=newSlider.source.instagram.type===undefined ? "" : newSlider.source.instagram.type;
		newSlider.source.flickr.transient=newSlider.source.flickr.transient===undefined ? 1200 : newSlider.source.flickr.transient;
		newSlider.source.vimeo.transient=newSlider.source.vimeo.transient===undefined ? 1200 : newSlider.source.vimeo.transient;
		newSlider.source.youtube.transient=newSlider.source.youtube.transient===undefined ? 1200 : newSlider.source.youtube.transient;
		newSlider.def.transition=newSlider.def.transition===undefined ? "fade" : newSlider.def.transition;
		newSlider.def.background.imageSourceType=newSlider.def.background.imageSourceType===undefined ? "full" : newSlider.def.background.imageSourceType;
		newSlider.def.panZoom.blurStart=newSlider.def.panZoom.blurStart===undefined ? 0 : newSlider.def.panZoom.blurStart;
		newSlider.def.panZoom.blurEnd=newSlider.def.panZoom.blurEnd===undefined ? 0 : newSlider.def.panZoom.blurEnd;
		newSlider.size.maxWidth=newSlider.size.maxWidth===undefined ? "" : newSlider.size.maxWidth;
		newSlider.carousel.ease=newSlider.carousel.ease===undefined ? "power3.inOut" : newSlider.carousel.ease;
		newSlider.carousel.speed=newSlider.carousel.speed===undefined ? "800" : newSlider.carousel.speed;
        if (newSlider.general.firstSlide!==undefined) newSlider.general.firstSlide.alternativeFirstSlideSet=newSlider.general.firstSlide.alternativeFirstSlideSet===undefined ? "" : newSlider.general.firstSlide.alternativeFirstSlideSet;
        if (newSlider.nav.preview) newSlider.nav.preview.width=newSlider.nav.preview.width===undefined ? 50 : newSlider.nav.preview.width;
		if (newSlider.nav.preview) newSlider.nav.preview.height=newSlider.nav.preview.height===undefined ? 100 : newSlider.nav.preview.height;
		if (newSlider.nav.mouse) newSlider.nav.mouse.reverse=newSlider.nav.mouse.reverse===undefined ? "default" : newSlider.nav.mouse.reverse;
		if (newSlider.nav.arrows.left) newSlider.nav.arrows.left.align=newSlider.nav.arrows.left.align===undefined ? "slider" : newSlider.nav.arrows.left.align;
		if (newSlider.nav.arrows.right) newSlider.nav.arrows.right.align=newSlider.nav.arrows.right.align===undefined ? "slider" : newSlider.nav.arrows.right.align;
		if (newSlider.nav.bullets) newSlider.nav.bullets.align=newSlider.nav.bullets.align===undefined ? "slider" : newSlider.nav.bullets.align;
		newSlider.parallax.ddd.zCorrection=newSlider.parallax.ddd.zCorrection===undefined ? 65 : newSlider.parallax.ddd.zCorrection;
		newSlider.parallax.mouse.bgSpeed=newSlider.parallax.mouse.bgSpeed===undefined ? 0 : newSlider.parallax.mouse.bgSpeed;
		newSlider.parallax.mouse.layersSpeed=newSlider.parallax.mouse.layersSpeed===undefined ? 1000 : newSlider.parallax.mouse.layersSpeed;
		newSlider.scrolleffects.bg=newSlider.scrolleffects.bg===undefined ? false : newSlider.scrolleffects.bg;
		newSlider.scrolleffects.direction=newSlider.scrolleffects.direction===undefined ? "both" : newSlider.scrolleffects.direction;
		newSlider.scrolleffects.maxBlur=newSlider.scrolleffects.maxBlur===undefined ? 10 : newSlider.scrolleffects.maxBlur;
		newSlider.scrolleffects.multiplicator=newSlider.scrolleffects.multiplicator===undefined ? "1.3" : newSlider.scrolleffects.multiplicator;
		newSlider.scrolleffects.multiplicatorLayers=newSlider.scrolleffects.multiplicatorLayers===undefined ? "1.3" : newSlider.scrolleffects.multiplicatorLayers;

		newSlider.scrolleffects.tilt=newSlider.scrolleffects.tilt===undefined ? "" : newSlider.scrolleffects.tilt;

		//GET RID OF UNDEFINED OBJECTS !
		/*console.log("------------  FOUND UNDEFINED VALUES IN SLIDER SETTINGS -------------------")
		console.log(newSlider);
		RVS.F.findUndefineds(newSlider);
		console.log("---------------------------------------------------------------------------");*/



		//if (newSlider.nav.arrows.preset==="custom") addSessionStartNavigation({obj:newSlider.nav.arrows,type:"arrows"});


		/*if (newSlider.nav.bullets.preset==="custom") newSlider.nav.bullets.presetsBackup = RVS.F.safeExtend({},newSlider.nav.bullets.presets,true);
		if (newSlider.nav.thumbs.preset==="custom") newSlider.nav.thumbs.presetsBackup = RVS.F.safeExtend({},newSlider.nav.thumbs.presets,true);
		if (newSlider.nav.tabs.preset==="custom") newSlider.nav.tabs.presetsBackup = RVS.F.safeExtend({},newSlider.nav.tabs.presets,true);
		*/


		return newSlider;
	}



	/***********************************
			INTERNAL FUNCTIONS
	************************************/


	/*
	SET VALUE TO A OR B DEPENDING IF VALUE A EXISTS AND NOT UNDEFINED OR NULL
	*/
	function _d(a,b) {
		if (a===undefined || a===null)
			return b;
		else
			return a;
	}

	function _truefalse(v) {
		if (v==="false" || v===false || v==="off" || v===undefined || v===0 || v===-1)
			v=false;
		else
		if (v==="true" || v===true || v==="on")
			v=true;
		return v;
	}

})();
