/*!
 * REVOLUTION 6.0.0 EDITOR SLIDE JS
 * @version: 1.0 (01.07.2019)
 * @author ThemePunch
*/

(function() {
	var layerlistElementMarkup = '<div class="the_layers_in_slide" id="the_layers_in_slide_###">',
		atiw,
		pzdrag,
		thbRptr;

	layerlistElementMarkup += '		<div class="resizeMainLayerListWrap" id="resizeMainLayerListWrap_###">';
	layerlistElementMarkup += '			<div class="mainLayerListWrap" id="mainLayerListWrap_###">';
	layerlistElementMarkup += '			</div>';
	layerlistElementMarkup += '		</div>';
	layerlistElementMarkup += '	</div>';

	RVS.LIB=RVS.LIB===undefined ? RVS.LIB = {} : RVS.LIB;


	/****************************************
		-	PUBLIC FUNCTIONS -
	****************************************/

	/*
	INITIALISE ALL LISTENERS AND INTERNAL FUNCTIONS FOR SLIDE EDITING/BUILDING
	*/
	RVS.F.initSlideBuilder = function() {
		thbRptr = jQuery('#slide_thumb_repeater');

		RVS.DOC.on('mouseenter','.slide_list_element',function() {
			thbRptr[0].innerHTML = "";
			if (RVS.SLIDER.settings.general.useWPML)
				jQuery('#slide_thumb_repeater').append(jQuery(this).find('.sle_thumb').clone());
			else
				jQuery('#slide_thumb_repeater').append(jQuery(this).find('.sle_thumb').first().clone());
			thbRptr.show();
		});
		RVS.DOC.on('mouseleave','.slide_list_element',function() {
			thbRptr[0].innerHTML = "";
		});

		createSlideAnimationList();
		initLocalInputBoxes();
		initLocalListeners();
		initKenBurnDrag();
	};



	RVS.F.changeFlags = function(_) {
		_ = RVS.SLIDER[RVS.S.slideId].slide.child;
		document.getElementById(RVS.S.slideId+'_flag_source').innerHTML = RVS.SLIDER.settings.general.useWPML && typeof RS_WPML_LANGS!=="undefined" && RS_WPML_LANGS!==undefined && _!==undefined && _.language!==undefined && _.language!=="" && _.language!==false && RS_WPML_LANGS[_.language]!==undefined ? '<span class="wpml_flag_wrap"><img src="'+RS_WPML_LANGS[_.language].image+'" class="wpml-img-flag" /></span>': '';
	}

	/*
	BUILD A SLIDE WHERE OBJ ATTRIBUTES EXISTING ALREADY
	*/
	RVS.F.addToSlideList = function(obj) {
		if (RVS.SLIDER.settings.general.useWPML) RVS.F.updateEasyInputs({container:jQuery('#form_slidergeneral_general_usewpml'),trigger:"init"});
		var _llem = layerlistElementMarkup.split('###').join(RVS.S.slideId),
			_ = RVS.SLIDER[obj.id].slide,
			flag = RVS.SLIDER.settings.general.useWPML && typeof RS_WPML_LANGS!=="undefined" && RS_WPML_LANGS!==undefined && _.child!==undefined && _.child.language!==undefined && _.child.language!=="" && _.child.language!==false && RS_WPML_LANGS[_.child.language]!==undefined ? '<span id="'+obj.id+'_flag_source" class="flag_container aaa"><span class="wpml_flag_wrap"><img src="'+RS_WPML_LANGS[_.child.language].image+'" class="wpml-img-flag" /></span></span>' : '<span id="'+obj.id+'_flag_source" class="flag_container bbb"></span>',
			addchildslide =  typeof RS_WPML_LANGS!=="undefined" && RS_WPML_LANGS!==undefined ? '<div class="addchildslide" data-id="'+obj.id+'"><i class="material-icons">playlist_add</i></div>' : '',
			slideelement = _.static.isstatic ?
			    jQuery('<div id="slide_list_element_'+obj.id+'" class="do_not_sort_slide_list_element slide_list_element static-slide-btn" data-ref="'+obj.id+'"><div class="sle_description"><i class="material-icons">layers</i>'+_.title+'</div></div>') :
			    _.child===undefined || _.child.parentId===undefined || _.child.parentId==="" || _.child.parentId.length===0 || _.child.parentId===false ?
				jQuery('<li id="slide_list_element_'+obj.id+'" class="slide_list_element sortable_slide_list_element" data-ref="'+obj.id+'"><div class="slide_elemenet_content"><div class="sle_thumb"></div><div class="sle_description"><i class="material-icons">swap_vert</i>'+flag+'<span id="slide_list_element_title_index_'+obj.id+'"></span><span id="slide_list_element_title_'+obj.id+'">'+_.title+'</span></div><div class="slidetab_toolbox"><div id="publish_toggle_icon_'+obj.id+'" class="'+_.publish.state+'slide"><i class="publishedicon material-icons">visibility</i><i class="unpublishedicon material-icons">visibility_off</i></div><div class="deleteslide"><i class="material-icons">delete</i></div><div class="duplicateslide"><i class="material-icons">content_copy</i></div><div class="editslide" data-id="'+obj.id+'" ><i class="material-icons">settings</i></div>'+addchildslide+'</div>'+_llem+'</div><div id="slide_list_element_childwrap_'+obj.id+'" class="slide_list_child_element_wrap"></div></li>'):
				jQuery('<li id="slide_list_element_'+obj.id+'" class="slide_list_child_element" data-ref="'+obj.id+'"><div class="sle_thumb"></div><div class="slide_elemenet_content"><div class="sle_description">'+flag+'<span id="slide_list_element_title_'+obj.id+'">'+_.title+'</span></div><div class="slidetab_toolbox"><div id="publish_toggle_icon_'+obj.id+'" class="'+_.publish.state+'slide"><i class="publishedicon material-icons">visibility</i><i class="unpublishedicon material-icons">visibility_off</i></div><div class="deleteslide"><i class="material-icons">delete</i></div><div class="editslide" data-id="'+obj.id+'" ><i class="material-icons">settings</i></div></div>'+_llem+'</div></li>');

		var thmb = slideelement.find('.sle_thumb');
		updateSlideThumbs({id:obj.id, target:thmb});
		if (_.static.isstatic)
			slideelement.insertBefore(RVS.C.slideList);
		else {
			if (_.child.parentId!=="" && _.child.parentId!==undefined)
				jQuery('#slide_list_element_childwrap_'+_.child.parentId).append(slideelement);
			else
				RVS.C.slideList.append(slideelement);
		}
		//slideelement.insertBefore('#slide_picker_wrap #newslide');
		if (obj.ignoreSort!==true) {
			RVS.F.makeSlideListSortable();
			RVS.F.makeSlideListScroll();
		}


	};

	RVS.F.makeSlideListScroll = function() {
		if (RVS.S.slidesListSB===undefined)
			RVS.S.slidesListSB = RVS.C.slideList.RSScroll({
				wheelPropagation:false,
				suppressScrollX:true,
				minScrollbarLength:100
			});
		else
			RVS.C.slideList.RSScroll("update");
	}


	/* SINGLE
	GET NEXT AVAILABLE SLIDE ID AND RUN ADDREMOVESLIDEWITHBACKUP
	*/
	RVS.F.addRemoveSlideWithBackupAfterSlideId = function(params) {
		var amount = params.urls!==undefined ? params.urls.length : 1,
			temp = RVS.F.safeExtend(true,{},params.slideObj);
			temp.slide.child.parentId = params.parentId!==undefined ? params.parentId : "";

		RVS.F.ajaxRequest('create_slide', {slider_id:RVS.ENV.sliderID, amount:amount}, function(response){
			if(response.success) {
				for (var i in response.slide_id) {
					if(!response.slide_id.hasOwnProperty(i)) continue;
					params.slideId = response.slide_id[i];
					params.slideObj = RVS.F.safeExtend(true,{},temp);
					params.slideObj.id = params.slideObj.slide.uid = response.slide_id[i];

					if (params.urls!==undefined && params.urls.length>0) {
						params.slideObj.slide.bg.type="image";
						params.slideObj.slide.bg.image=params.urls[i].url.split(" ").join("%20");
						params.slideObj.slide.bg.imageSourceType="full";
						RVS.F.slideinWork(response.slide_id[i]);
					}
					RVS.F.addRemoveSlideWithBackup(params);
					if (params.parentID!==undefined || params.fromSlideId!==undefined) {
						RVS.F.convertIDStoTxt();
						RVS.F.saveSlides({index:0,slides:RVS.SLIDER.slideIDs, trigger:RVS.F.saveSliderSettings,works:RVS.SLIDER.inWork,force:true});
					}
					if (params.fromSlideId!==undefined) RVS.F.duplicateSkinColors({type:"slide", slideFrom:params.fromSlideId, slideTo:params.slideId});
					RVS.DOC.trigger('newSlideCreated', [response.slide_id[i]]);
				}
				if (params.endOfMain!==undefined) params.endOfMain();
			}
		});
	};



	/*
	CREATE NEW SLIDE ON DEMAND - DUPLICATE OR NEW SLIDE WITH BACKUP
	*/
	RVS.F.addRemoveSlideWithBackup = function(obj,index)	{
		RVS.F.openBackupGroup({id:obj.id,txt:obj.step,icon:obj.icon,lastkey:"#"+obj.slideId});
		 // Push Slide Object to Slider Array
		RVS.SLIDER[obj.slideId] = obj.slideObj;

		var _n = RVS.SLIDER.slideIDs.slice(),
			cache,
			mMO,
			focusindex,
			ssf = false;

		if (obj.id==="deleteslide") {
			var _deleteIndex,sli;
			// Kill WPML CHILD ELEMENTS IF PARENT DELETED
			for (var i in RVS.SLIDER.slideIDs) {
				sli = ""+RVS.SLIDER.slideIDs[i];
				if (RVS.SLIDER[sli]!==undefined && RVS.SLIDER[sli].slide!==undefined && RVS.SLIDER[sli].slide.child!==undefined && (""+RVS.SLIDER[sli].slide.child.parentId===""+obj.slideId)) {
					// Delete This Slide also !
					_deleteIndex = RVS.F._inArray(sli,_n);
					focusindex = _deleteIndex-1 >=0 ? _deleteIndex-1 : _deleteIndex;
					if (""+sli==""+RVS.S.slideId) ssf = true;
					_n.splice(_deleteIndex,1);
					RVS.SLIDER[sli] = {};
				}
			}

			_deleteIndex = RVS.F._inArray(obj.slideId,_n);
			focusindex = _deleteIndex-1 >=0 ? _deleteIndex-1 : _deleteIndex;
			if (RVS.S.slideId==obj.slideId) ssf = true;
			_n.splice(_deleteIndex,1);

			RVS.F.updateSliderObj({path:'slideIDs',val:_n});
			cache = jQuery('#slide_list_element_'+obj.slideId).removeClass("selected").detach();

		} else {
			_n.push(obj.slideId);
			RVS.F.updateSliderObj({path:'slideIDs',val:_n});
			RVS.F.addToSlideList({id:obj.slideId});
			mMO = {mode:"slidelayout",set:true, slide:obj.slideId};
		}
		RVS.F.backup({path:obj.slideId, cache:cache, beforeSelected:obj.beforeSelected, icon:obj.icon,txt:obj.step,lastkey:"#"+obj.slideId,force:true,val:RVS.F.safeExtend(true,{},RVS.SLIDER[obj.slideId]), old:obj.slideObjOld, backupType:"slide", bckpGrType:obj.id});

		if (jQuery('.slide_list_element.sortable_slide_list_element').length==0) mMO = {mode:"sliderlayout",set:true};

		if (mMO!==undefined) RVS.F.mainMode(mMO);
		else if (ssf) RVS.F.setSlideFocus({slideid:(focusindex>=RVS.SLIDER.slideIDs.length ? RVS.SLIDER.slideIDs[0] : RVS.SLIDER.slideIDs[focusindex])});

		RVS.F.closeBackupGroup({id:obj.id});
		if (obj.after!==undefined) obj.after();
	};




	/*
	SLIDE FOCUSED
	*/
	RVS.F.setSlideFocus = function(obj) {
		RVS.F.setEditorUrl(obj.slideid);
		RVS.F.slideinWork(obj.slideid);

		delete RVS.S.bgobj;

		RVS.DOC.trigger('beforeSlideChange');
		jQuery('.slide_list_element.selected, .slide_list_child_element.selected').removeClass("selected");
		jQuery('#slide_list_element_'+obj.slideid).addClass("selected");
		jQuery('.slide_li').hide(); // MOve to Else if want to show underlaying Slide also.

		if (RVS.SLIDER[obj.slideid].slide.static.isstatic) {
			window.lastSlideSettingForm = "static";
			RVS.F.updateStaticStartEndList();
			RVS.C.vW.classList.add("staticlayersview");
			RVS.F.openSettings({forms:["*slidelayout**mode__slidestyle*#form_slidestatic"], uncollapse:true});
		} else {
			RVS.C.vW.classList.remove("staticlayersview");
			if (window.lastSlideSettingForm === "static") {
				window.lastSlideSettingForm = jQuery('.slide_submodule_trigger.selected').data('forms');
				RVS.F.openSettings({forms:window.lastSlideSettingForm, uncollapse:true});
			}
		}
		RVS.DOC.trigger("slideAmountUpdated");

		RVS.S.slideId = obj.slideid;
		RVS.DOC.trigger('showLastEditedSlideStatic');
		RVS.DOC.trigger("slideFocusChanged");

		//CHECK SLIDE EXISTENS
		if (jQuery('#slide_'+obj.slideid).length===0) {

			// CREATE THE SLIDE HERE, AND LOAD ALL ELEMENTS ETC.
			var newSlide = jQuery('#slide_li_template').clone();
			newSlide.attr('id',"slide_"+obj.slideid);
			if (RVS.SLIDER[obj.slideid].slide.static.isstatic) newSlide.addClass("static_slide_li");
			newSlide.find('.crumb_title').html('<i class="material-icons">wallpaper</i>'+RVS.SLIDER[obj.slideid].slide.title);
			RVS.S.ulInner.append(newSlide);
			RVS.TL[RVS.S.slideId] = RVS.TL[RVS.S.slideId]===undefined ? {} : RVS.TL[RVS.S.slideId];
		}

		// Trigger Slide Change Event
		if (RVS.S.lastVisibleSlideId!==obj.slideid) RVS.DOC.trigger("slideChanged");
		RVS.S.lastVisibleSlideId = obj.slideid;

		RVS.C.slide = jQuery('#slide_'+obj.slideid);

		// CREATE SLIDE BG CONTAINERS
		if (RVS.SBGS===undefined || RVS.SBGS[RVS.S.slideId]===undefined) {
			RVS.SBGS = RVS.SBGS===undefined ? {} : RVS.SBGS;
			RVS.SBGS[RVS.S.slideId] = {
				wrap : RVS.F.cE({t:'rs-sbg-px'}),
				n : {
					sbg : RVS.F.cE({t:'rs-sbg-wrap',cN:'in'}),
					canvas : RVS.F.cE({t:'canvas'}),
					bgvid : false,
					loadobj:{}
				},
				c : {
					sbg : RVS.F.cE({t:'rs-sbg-wrap'}),
					canvas : RVS.F.cE({t:'canvas'}),
					bgvid : false,
					previous : true,
					loadobj:{}
				}
			}

			RVS.SBGS[RVS.S.slideId].n.sbg.appendChild(RVS.SBGS[RVS.S.slideId].n.canvas);
			RVS.SBGS[RVS.S.slideId].c.sbg.appendChild(RVS.SBGS[RVS.S.slideId].c.canvas);
			RVS.SBGS[RVS.S.slideId].wrap.appendChild(RVS.SBGS[RVS.S.slideId].n.sbg);
			RVS.SBGS[RVS.S.slideId].wrap.appendChild(RVS.SBGS[RVS.S.slideId].c.sbg);
			RVS.C.slide.prepend(RVS.SBGS[RVS.S.slideId].wrap);
			RVS.SBGS[RVS.S.slideId].c.ctx = RVS.SBGS[RVS.S.slideId].c.canvas.getContext('2d');
			RVS.SBGS[RVS.S.slideId].n.ctx = RVS.SBGS[RVS.S.slideId].n.canvas.getContext('2d');

		}


		RVS.C.layergrid = RVS.C.slide.find('.layer_grid');
		RVS.DOC.trigger('sliderProgressUpdate');
		if (!window.contentDeltaFirstRun) RVS.F.updateContentDeltas();
		 RVS.C.rZone.top = RVS.C.layergrid.find('.row_wrapper_top');
		 RVS.C.rZone.middle = RVS.C.layergrid.find('.row_wrapper_middle');
		 RVS.C.rZone.bottom = RVS.C.layergrid.find('.row_wrapper_bottom');

		RVS.C.layergrid.attr("id","layer_grid_"+obj.slideid);
		RVS.H = {};

		RVS.C.slide.show();
		RVS.DOC.trigger('updatesliderlayout','setSlideFocus-139');
		RVS.F.setRulers();

		RVS.F.updateFields(obj.ignoreUpdateFields);


		//CALL BASIC CHANGES

		if (RVS.S.slideBGCallFirsttime!==undefined) RVS.F.redrawSlideBG();
		if (RVS.S.slideInputFieldsInitialised) RVS.F.udpateSelectedSlideAnim(true);

		RVS.F.updateParallaxLevelTexts();
		RVS.F.setRulers();
		var beforeSelected = RVS.selLayers.length;

		//Build Layers here
		RVS.F.buildLayerLists({ignoreSelectLayers:obj.ignoreUpdateFields, ignoreDrawLayers:true});
		RVS.F.updateAllLayerFrames();

		RVS.F.updateSelectedHtmlLayers(true,true);

		if (RVS.S.slideBGCallFirsttime!==undefined && RVS.selLayers.length!=beforeSelected) RVS.F.selectedLayersVisualUpdate();

		RVS.S.slideBGCallFirsttime=true;

		RVS.DOC.trigger('updateScrollBars');

		RVS.DOC.trigger("updateAllInheritedSize");
		RVS.DOC.trigger("slideFocusFunctionEnd");

		if (RVS.S.firstPreparation!==undefined && RVS.S.firstPreparation!==1) RVS.F.expandCollapseTimeLine(true,"open");

		RVS.DOC.trigger('updateSlideLoopRange');
		RVS.DOC.trigger('updateFixedScrollRange');

		//Create a First Save Option to make sure that we have some Basic to compare
		RVS.S.lastSaved = RVS.S.lastSaved===undefined ? {} : RVS.S.lastSaved;
		setTimeout(function() {
			if (RVS.S.lastSaved[RVS.S.slideId] === undefined && RVS.S.zIndexAtStartChanged!==true) {
					RVS.S.lastSaved[RVS.S.slideId] =
						{	params : JSON.stringify(RVS.F.simplifySlide(RVS.SLIDER[RVS.S.slideId].slide)),
							layers : JSON.stringify(RVS.F.simplifyAllLayer(RVS.SLIDER[RVS.S.slideId].layers))
						};
				delete RVS.S.zIndexAtStartChanged;
			}
		},500);
	};

	RVS.F.getAllSlidesBgDimension = function() {
		for (var i in RVS.SLIDER.slideIDs) {
			if (!RVS.SLIDER.slideIDs.hasOwnProperty(i)) continue;
			RVS.F.loadBGImages(RVS.SLIDER.slideIDs[i]);
			RVS.F.slideinWork(RVS.SLIDER.slideIDs[i]);
		}
	}

	/*
	 IF CAROUSEL AND JUSTIFY WALL ENABLED, SLIDER BG DIMENSION NEED TO BE CALCULATED
	 */
    RVS.F.loadBGImages = function(slideId,type) {


        // Build Cache for Loaded Images
        RVS.allimages= RVS.allimages===undefined ? {src:[], dim:[], img:[]} : RVS.allimages;
        //if (!RVS.F.JWALL()) return;
        slideId = slideId === undefined ? RVS.S.slideId : slideId;


        if (jQuery.inArray(RVS.SLIDER[slideId].slide.bg.type,["image","youtube","html5","vimeo","external"])>=0 || (type==="c" && slideId!==undefined && RVS.SBGS!==undefined && RVS.SBGS[RVS.S.slideId]!==undefined && RVS.SBGS[RVS.S.slideId][type].src!==undefined)) {
            // type  - n Next (coming Image), c - Current (previous Image leaving)
            var src = type==="c" ? RVS.SBGS[RVS.S.slideId][type].src : RVS.SBGS[RVS.S.slideId].n.src; // RVS.SLIDER[slideId].slide.bg.type==="external" ? RVS.SLIDER[slideId].slide.bg.externalSrc : RVS.SLIDER[slideId].slide.bg.image;


            if (RVS.SLIDER[slideId].slide.bg.type==="external" && src==="") src = RVS.ENV.plugin_url+"admin/assets/images/transparent.png";
            if (src===undefined) return;
            src = src.split(" ").join('%20');

            if (type!==undefined) RVS.SBGS[RVS.S.slideId][type].loadobj = RVS.SBGS[RVS.S.slideId][type].loadobj === undefined ? {} : RVS.SBGS[RVS.S.slideId][type].loadobj;
            var index = jQuery.inArray(src,RVS.allimages.src);
            if (index>=0) {
                if (RVS.allimages.dim[index]!==undefined) {
                    if (type!=="c") {
                        RVS.SLIDER[slideId].slide.bg.imageWidth = RVS.allimages.dim[index].w;
                        RVS.SLIDER[slideId].slide.bg.imageHeight = RVS.allimages.dim[index].h;
                        RVS.SLIDER[slideId].slide.bg.imageRatio = RVS.allimages.dim[index].r;
                    }

                    if (type!==undefined) {
                        RVS.SBGS[RVS.S.slideId][type].loadobj.img = RVS.allimages.img[index];
                        RVS.SBGS[RVS.S.slideId][type].loadobj.width = RVS.allimages.dim[index].w;
                        RVS.SBGS[RVS.S.slideId][type].loadobj.height = RVS.allimages.dim[index].h;
                        RVS.SBGS[RVS.S.slideId][type].loadobj.src = RVS.allimages.src[index];
                        RVS.SBGS[RVS.S.slideId][type].loadobj.progress = "loaded";
                    }
                    RVS.DOC.trigger('device_area_dimension_update');
                }
            } else {

                if (src!==RVS.S.lastLoadedBGImage) {
                    RVS.S.lastLoadedBGImage = src;
                    index = RVS.allimages.src.length;
                    RVS.allimages.img[index] = new Image();
                    RVS.F.getImgWithCORS(RVS.allimages.img[index], src);
                    if (type!==undefined) RVS.SBGS[RVS.S.slideId][type].loadobj.progress = "loading";
                    RVS.allimages.src[index] = src;
                    RVS.allimages.img[index].tpRequest = 0;

                    RVS.allimages.img[index].onload=function() {
                        RVS.allimages.dim[index] = {w:this.width, h:this.height, r: this.width/this.height};

                        if (type!==undefined) {
                            RVS.SBGS[RVS.S.slideId][type].loadobj.img = RVS.allimages.img[index];
                            RVS.SBGS[RVS.S.slideId][type].loadobj.src = RVS.allimages.src[index];
                            RVS.SBGS[RVS.S.slideId][type].loadobj.width = this.width;
                            RVS.SBGS[RVS.S.slideId][type].loadobj.height = this.height;
                            RVS.SBGS[RVS.S.slideId][type].loadobj.progress = "loaded";
						}

                        if (type!=="c") {
							RVS.SLIDER[slideId].slide.bg.imageWidth = this.width;
							RVS.SLIDER[slideId].slide.bg.imageHeight = this.height;
							RVS.SLIDER[slideId].slide.bg.imageRatio = this.width/this.height;
                        }
                        RVS.DOC.trigger('device_area_dimension_update');
                        if (this.tpRequest===1) {
                            console.info('%c'+RVS_LANG.CORSWARNING,'color:#f1c40f;')
                            console.info(this.src);
                        }
                    };
                    RVS.allimages.img[index].onerror = function(e, m, o) {
                        if(this.tpRequest === 0 && this.crossOrigin) {
                            delete this["crossOrigin"];
                            this.removeAttribute("crossorigin")
                            this.tpRequest = 1;
                            this.src = this.src;
                        }
                    }

                    RVS.allimages.img[index].src = RVS.allimages.src[index];
				}
            }
        }
	}

	/*
	DRAW THE BG OF THE CURRENT SLIDE
	*/
    RVS.F.redrawSlideBG = function(obj) {

        if (RVS.C.slide===undefined) return;
        clearTimeout(RVS.S.redrawSlideBGTimeOut);
        RVS.S.redrawSlideBGTimeOut = setTimeout(function() {
                var _ = RVS.SLIDER[RVS.S.slideId].slide,
                    slideBGFrom;

                if (obj!==undefined && obj.liveColorChange && obj.backgroundColor!==undefined) slideBGFrom = { bg:{color:obj.backgroundColor, type:"solid"}};
                else
                    for (var i in RVS.JHOOKS.redrawSlideBG) {
                        if(!RVS.JHOOKS.redrawSlideBG.hasOwnProperty(i)) continue;
                        slideBGFrom = RVS.JHOOKS.redrawSlideBG[i](slideBGFrom);
                    }

                //BUILD BG OBJECT
                RVS.SBGS[RVS.S.slideId].n = RVS.F.safeExtend(true,RVS.SBGS[RVS.S.slideId].n,RVS.F.getSlideBGObj({slideBGFrom:slideBGFrom}));
                RVS.SBGS[RVS.S.slideId].c = RVS.F.safeExtend(true,RVS.SBGS[RVS.S.slideId].c,RVS.F.getSlideBGObj({type:"c", slideBGFrom : { bg:{color:"transparent", repeat:"repeat",position:"center center",fit:"50%", src:RVS.ENV.plugin_url+"admin/assets/images/light_pattern_2x.png"}}}));

                //LOAD IMAGES IF NOT YET LOADED
                if (RVS.SBGS[RVS.S.slideId].n.loadobj.img===undefined || RVS.SBGS[RVS.S.slideId].n.loadobj.src!==RVS.SBGS[RVS.S.slideId].n.src || RVS.S.lastSlideBGSrc!==RVS.SLIDER[RVS.S.slideId].slide.bg.type) RVS.F.loadBGImages(undefined,"n");
                if (RVS.SBGS[RVS.S.slideId].c.loadobj.img===undefined) RVS.F.loadBGImages(undefined,"c");


                RVS.S.lastSlideBGSrc = RVS.SLIDER[RVS.S.slideId].slide.bg.type;

                //UPDATE PAN ZOOM SETTINGS AS WELL
                updateKenBurnSettings();

                // ADD FILTER TO CONTAINERS
                RVS.SBGS[RVS.S.slideId].wrap.className = _.bg.mediaFilter;
                if (RVS.SBGS[RVS.S.slideId].n.usebgColor && RVS.SBGS[RVS.S.slideId].n.bgcolor=="transparent") {
                    RVS.F.resetSlideTL();
                    RVS.SBGS[RVS.S.slideId].n.ctx.clearRect(0,0,RVS.SBGS[RVS.S.slideId].n.ctx.canvas.width, RVS.SBGS[RVS.S.slideId].n.ctx.canvas.height);
                }
                else
                    RVS.F.buildSlideAnimation();

                //PUT ANIMATION IN POSITION
                RVS.F.slideAnimation({progress:1,type:"slide"});

                var sliderOverlay = document.getElementById('slider_overlay');
                if(sliderOverlay) RVS.C.slide[0].querySelector('rs-sbg-wrap').appendChild(sliderOverlay);

                //BUILD PAN ZOOM EFFECT ????
                RVS.DOC.trigger('redrawSlideBGDone');
            },100);

        };

        RVS.F.updateSlideInputFields = function() {
            if (RVS.S.slideInputFieldsInitialised!==true && RVS.F.slideInputFieldsInitialisedInfo!==true) {
                RVS.F.udpateSelectedSlideAnim(true);
                RVS.F.showWaitAMinute({fadeIn:0,text:RVS_LANG.updatingfields});
                RVS.S.slideInputFieldsInitialised = true;
                RVS.F.slideInputFieldsInitialisedInfo=true;
            }

            setTimeout(function() {

                RVS.F.updateEasyInputs({container:jQuery('.slide_settings_collector, #do_title_slide'), path:RVS.S.slideId+".slide.", trigger:"init"});
                jQuery('#s_bg_color').val(RVS.SLIDER[RVS.S.slideId].slide.bg.color).rsColorPicker("refresh");
                jQuery('#slide_bg_type').trigger('change');

                updateSlideThumbs();

                if (RVS.F.slideInputFieldsInitialisedInfo===true) {
                    RVS.F.showWaitAMinute({fadeOut:2,text:RVS_LANG.updatingfields});
                    RVS.F.slideInputFieldsInitialisedInfo=false;
                }
                RVS.DOC.trigger('slideInputFieldsUpdated');
            },5);

        }

	/*
	UPDATE THE INPUT FIELDS TO SHOW THE CURRENT SELECTED VALUES
	*/
    RVS.F.updateFields = function(ignoreUpdateFields) {

		// Update Specials
		buildSlideToLinkDrop();

		//Reset Transition List
        RVS.F.updateSlideAnimationView();
        //Update Fields If Slide Mode selected and not yet Updated.
        if (ignoreUpdateFields!==true) {

            RVS.F.updateSlideInputFields();
        }

		RVS.F.updateSlideBasedNavigationStyle();

        //Update Ken Burn Settings
        updateKenBurnBasics(ignoreUpdateFields);

        //Update Alternating Animations
        RVS.F.alternatingSlideAnims();

		//Update TimeLine
		RVS.F.buildSlideFrames();
		RVS.F.updateSlideFrames();
		RVS.F.updateMaxTime({pos:true, cont:true});
        if (RVS.S.firstTimeGoToIdle!==undefined) RVS.F.goToIdle();
        RVS.S.firstTimeGoToIdle = true;
    };



    /*GET BG IMAGE OBJECT */
    RVS.F.getSlideBGObj = function(obj) {

        obj.id = obj.id===undefined ? RVS.S.slideId : obj.id;

        var _ = obj.slideBGFrom===undefined ? RVS.SLIDER[obj.id].slide : obj.slideBGFrom,
            bgobj = {
                bgcolor:"transparent",
                bgrepeat:_.bg.repeat,
                bgposition:(_.bg.position==="percentage" ? parseInt(_.bg.positionX,0)+"% "+parseInt(_.bg.positionY,0)+"%" : _.bg.position),
                bgfit:(_.bg.fit==="percentage" ? parseInt(_.bg.fitX,0)+"% "+parseInt(_.bg.fitY,0)+"%" : _.bg.fit),
            },
            sip = jQuery('#slide_bg_image_path');


        if (obj.type!=="c") {  // ONLY RUN THIS IF IT IS NOT THE "CURRENT SBGS (PREVIOUS SLIDE)"
            switch (_.bg.type) {
                case "trans":
                    bgobj.usebgColor = true;
                break;
                case "solid":
                    var sbg = window.RSColor.get(_.bg.color);
                    //BG COLOR OF SLIDER
                    if (sbg.indexOf("gradient")>=0)
                        bgobj.bgcolor = sbg;
                    else
                        bgobj.bgcolor = _.bg.color;
                    bgobj.type="solid";
                    bgobj.usebgColor = true;
                break;
                case "external":
                    var iurl = _.bg.externalSrc.split(' ').join('%20');
                    bgobj.src = iurl;
                    sip.val(_.bg.externalSrc);
                    sip.height(Math.max(25, (8 + ((_.bg.externalSrc.length/20) * 16))));
                    bgobj.type="image";
                    bgobj.usebgColor = false;
                break;
                case "html5":
                case "vimeo":
                case "youtube":
                case "image":
                    var iurl = _.bg.image.split(' ').join('%20');
                    bgobj.src = iurl;
                    sip.val(_.bg.image);
                    if (_.bg.image!==undefined )
                        sip.height(Math.max(25, (8 + ((_.bg.image.length/20) * 16))));
                    else
                    if (_.bg.image!==undefined )
                        sip.height(Math.max(25, (8 + ((_.bg.image.length/20) * 16))));
                    bgobj.type="image";
                    bgobj.usebgColor = false;
                break;
            }
        } else {
            bgobj.src = _.bg.src;
        }
        return bgobj;
    }

	/*
	GET THE CSS OBJECT OF A SLIDE THUMBNAIL
	*/
	RVS.F.getSlideBGDrawObj = function(obj) {
		obj = obj===undefined ? {updateSip:false} : obj;
		obj.id = obj.id===undefined ? RVS.S.slideId : obj.id;

		var _ = obj.slideBGFrom===undefined ? RVS.SLIDER[obj.id].slide : obj.slideBGFrom,
			bgobj = {
				backgroundImage:"",
				backgroundColor:"transparent",
				backgroundRepeat:_.bg.repeat,
				backgroundPosition:(_.bg.position==="percentage" ? parseInt(_.bg.positionX,0)+"% "+parseInt(_.bg.positionY,0)+"%" : _.bg.position),
				"background-size":(_.bg.fit==="percentage" ? parseInt(_.bg.fitX,0)+"% "+parseInt(_.bg.fitY,0)+"%" : _.bg.fit),
			},
			sip = jQuery('#slide_bg_image_path');

		switch (_.bg.type) {
			case "solid":
				var sbg = window.RSColor.get(_.bg.color);
				//BG COLOR OF SLIDER

				if (sbg.indexOf("gradient")>=0)
					bgobj = {background:sbg};
				else
					bgobj.backgroundColor = _.bg.color;
			break;
			case "trans":
			break;
			case "external":
				var iurl = _.bg.externalSrc.split(' ').join('%20');

				bgobj.backgroundImage = 'url('+iurl+')';
				if (obj.updateSip) {
					sip.val(_.bg.externalSrc);
					sip.height(Math.max(25, (8 + ((_.bg.externalSrc.length/20) * 16))));
				}
			break;
			case "html5":
			case "vimeo":
			case "youtube":
			case "image":
				var iurl = _.bg.image.split(' ').join('%20');

				bgobj.backgroundImage = 'url("'+iurl+'")';
				if (obj.updateSip) {
					sip.val(_.bg.image);
					if (_.bg.image!==undefined )
						sip.height(Math.max(25, (8 + ((_.bg.image.length/20) * 16))));
					else
					if (_.bg.image!==undefined )
						sip.height(Math.max(25, (8 + ((_.bg.image.length/20) * 16))));
				}
			break;
		}

		return bgobj;
	};


	/*
	GET THE SMALLEST SLIDE LENGTH BASED ON ADDED LAYERS AND CURRENT SELECTED TIME
	*/
	RVS.F.slideMinLength = function(v) {
		var _tempv = v;
		v = (v==="default" || v==="Default" || v===0 || v==="0ms") ? parseInt(RVS.SLIDER.settings.def.delay,0) : parseInt(v,0);

		var min = RVS.F.setSmallestSlideLength({left:v/10})*10;
		return  (_tempv==="Default" || _tempv===0 || _tempv==="0ms" || _tempv==="default") ? "Default" : min;
	};

	/*
	GET SLIDE LENGTH BASED ON DEFAULT AND EDITED VALUE
	*/
	RVS.F.getSlideLength = function() {
		var d = RVS.SLIDER[RVS.S.slideId].slide.timeline.delay;
		d = d == undefined || d=="" || d==="default" || d==0 || d==="Default"  ? RVS.SLIDER.settings.def.delay : d;
		d = d == undefined || d=="" || d==="default" || d==0 || d==="Default"  ? 8000 : parseInt(d,0);
		return d/10;
	};


	/*
	SLIDE ANIMATION HANDLINGS
	*/
	RVS.F.getSlideAnimParams = function(attribute) {

		//RVS.S.slideTrans
		var currentSelected = jQuery('#active_transitions_innerwrap li.selected').index();
		currentSelected = currentSelected===-1 ? 1 : currentSelected;
		if (RVS.SLIDER[RVS.S.slideId].slide.timeline[attribute]!==undefined) {
			var	r = RVS.SLIDER[RVS.S.slideId].slide.timeline[attribute][currentSelected];
			if (currentSelected===0)
				r = r=="default" && attribute=="duration" ? RVS.F.getSliderTransitionParameters(RVS.SLIDER[RVS.S.slideId].slide.timeline.transition[currentSelected]).TR[10] : r;
			else
				r = r=="default" && attribute=="duration" ? RVS.SLIDER[RVS.S.slideId].slide.timeline[attribute][currentSelected]===undefined ?  RVS.SLIDER.settings.def.transitionDuration : RVS.F.getSliderTransitionParameters(RVS.SLIDER[RVS.S.slideId].slide.timeline.transition[currentSelected]).TR[10] : r;
		} else r = "default";

		return r;
	};


	/*
	BUILD THE SLIDE ANIMATION
	*/
	RVS.F.buildSlideAnimation = function(obj) {
		if ((RVS.SBGS[RVS.S.slideId].n.type!=="image" || RVS.SBGS[RVS.S.slideId].n.loadobj.progress==="loaded") && RVS.SBGS[RVS.S.slideId].c.loadobj.progress==="loaded")
			RVS._R.animateSlide(undefined,RVS._R.convertSlideAnimVals(obj===undefined ? RVS.SLIDER[RVS.S.slideId].slide.slideChange : obj));
	};

	/*
	SET THE SLIDE ANIMATION PROGRESS POSITION
	*/
	RVS.F.slideAnimation = function(obj) {
		if (RVS.TL[RVS.S.slideId][obj.type]===undefined) return;
		if (obj.progress!==undefined) {
			RVS.TL[RVS.S.slideId][obj.type].progress(0.9999);
			RVS.TL[RVS.S.slideId][obj.type].progress(obj.progress);
		}
	};


	/****************************************
		-	INTERNAL FUNCTIONS -
	****************************************/

	function setActiveSlide(_) {

		var slidetab = jQuery('#slide_list_element_'+_.id);
		if (_.openclose) {
			if (!slidetab.hasClass("opened_slidetab")) {
				jQuery('.slide_list_element.sortable_slide_list_element').removeClass("opened_slidetab");
				slidetab.addClass("opened_slidetab");
				if (RVS.S.slideId!==_.id) {
					RVS.F.setSlideFocus({slideid:_.id});
				}
			} else {
				slidetab.removeClass("opened_slidetab");
			}
		} else

		if (RVS.S.slideId!==_.id) RVS.F.setSlideFocus({slideid:_.id});


		if (RVS.S.vWmode!=="mode__slidelayout") RVS.F.mainMode({mode:"slidelayout",set:false});
		RVS.F.showHideLayerEditor({mode:"slidelayout"});
		setTimeout(function() {
			RVS.DOC.trigger('sliderSizeChanged');
			RVS.DOC.trigger('device_area_availibity');
		},500);

	}

	RVS.F.setSlideAnimPresetToCustom = function() {
		RVS.SLIDER[RVS.S.slideId].slide.slideChange.preset = "custom";
		RVS.F.udpateSelectedSlideAnim();
	}

	function getAltSlideCnt(i) {
		return '<div class="slide_anim_alternates"><div class="remove_altslide basic_action_button leftbutton onlyicon" data-index="'+i+'"><i class="material-icons">delete</i></div><div class="slide_alt_slide"><label_a>'+(parseInt(i)+2)+'.'+RVS_LANG.transition+'</label_a><select class="slideinput tos2 searchbox easyinit slideAnimSelect" data-theme="wideopentos2" data-r="slideChange.alt.'+i+'"></select></div></div>';
	}

	/*ALTERNATING SLIDE ANIMATONS*/
	RVS.F.alternatingSlideAnims = function() {
		RVS.C.altslcon = RVS.C.altslcon===undefined ? jQuery('#sanimation_sfalternates') : RVS.C.altslcon;
		var cnt = "",
			alt = RVS.SLIDER[RVS.S.slideId].slide.slideChange.alt;
		if (alt!==undefined && Array.isArray(alt) && alt.length>0)
			for ( var i in alt) cnt += getAltSlideCnt(i);

		RVS.C.altslcon[0].innerHTML = cnt;


		RVS.C.altslcon.find('.tos2.slideAnimSelect').each(function() {
			RVS.F.createSlideAnimOptions(this);
		});
	}

	RVS.F.updateAlternateSlideAnims = function() {
		RVS.F.alternatingSlideAnims();
		RVS.C.altslcon.find('.tos2.slideAnimSelect').ddTP({placeholder:"Enter or Select"});
		RVS.F.updateEasyInputs({container:RVS.C.altslcon, path:RVS.S.slideId+".slide.", trigger:"init"});
	}

	/*
	INIT LOCAL INPUT BOX FUNCTIONS
	*/
	function initLocalInputBoxes() {

		RVS.DOC.on('changeflags',RVS.F.changeFlags);

		RVS.DOC.on('click','.remove_altslide',function() {
			var old = RVS.SLIDER[RVS.S.slideId].slide.slideChange.alt.join(",").split(",");
			RVS.SLIDER[RVS.S.slideId].slide.slideChange.alt.splice(this.dataset.index,1);
			RVS.F.openBackupGroup({id:'slide_transition',txt:"Slide Transition Change",icon:'calendar_view_day'});
			RVS.F.backup({	path:RVS.S.slideId+".slide.slideChange.alt",
						val:RVS.SLIDER[RVS.S.slideId].slide.slideChange.alt,
						old:old,
						backupType:"array",
						mode:'slidealttransition',
						callBack:function() {

						}
					});

			RVS.F.closeBackupGroup({id:'slide_transition'});
			RVS.F.updateAlternateSlideAnims();
		});

		RVS.DOC.on('addslidetransition',function() {
			RVS.SLIDER[RVS.S.slideId].slide.slideChange.alt = RVS.SLIDER[RVS.S.slideId].slide.slideChange.alt===undefined ? [] : RVS.SLIDER[RVS.S.slideId].slide.slideChange.alt;
			RVS.SLIDER[RVS.S.slideId].slide.slideChange.alt.push("");
			var a = jQuery(getAltSlideCnt(RVS.SLIDER[RVS.S.slideId].slide.slideChange.alt.length-1));
			RVS.C.altslcon.append(a);
			a.find('.slideAnimSelect').each(function() {
				RVS.F.createSlideAnimOptions(this);
				jQuery(this).ddTP({placeholder:"Enter or Select"});
			});

		});

		RVS.DOC.on('updateSlideAnimationView',RVS.F.updateSlideAnimationView);
		RVS.DOC.on('updateSlideAnimation',function(a,b) {
			if (b!==undefined) {
				if (b.eventparam==="tocustom") RVS.F.setSlideAnimPresetToCustom();
				RVS.F.redrawSlideBG();
			}
		});

		RVS.DOC.on('updateSlideAnimationFavoits',function(a,b) {
			if (b!==undefined) {
				RVS.F.showHidePresetFavorits(RVS.LIB.SLTR_FAVORIT,"slide_trans_mains");
				if (RVS.SLIDER[RVS.S.slideId].slide.slideChange.favorit) {
					RVS.S.ATI[0].classList.add("showonlyfavoritpresets");
					RVS.S.ATIR[0].classList.add("showonlyfavoritpresets");
				} else {
					RVS.S.ATI[0].classList.remove("showonlyfavoritpresets");
					RVS.S.ATIR[0].classList.remove("showonlyfavoritpresets");
				}
			}

		});

		RVS.DOC.on('colrowslideanimchange',function(inp,b) {
			if (b!==undefined && b.eventparam!==undefined) {
				var type =  b.eventparam.indexOf("in.")>=0 ? "in" : "out",
					keep = b.eventparam.indexOf(".col")>=0 ? "col" : "row",
					change = keep==="row" ? "col" : "row";
				if (parseInt(RVS.SLIDER[RVS.S.slideId].slide.slideChange[type].col)*parseInt(RVS.SLIDER[RVS.S.slideId].slide.slideChange[type].row)>1000) {
					RVS.SLIDER[RVS.S.slideId].slide.slideChange[type][change] = Math.floor(1000/parseInt(RVS.SLIDER[RVS.S.slideId].slide.slideChange[type][keep]));
					document.getElementById('sltrans_'+type+'_'+change).value = RVS.SLIDER[RVS.S.slideId].slide.slideChange[type][change]
				}

				// UPDATE ROW / COL VIA ADDON IF NEEDED
				if (RVS.JHOOKS.extendSlideAnimationRowCol)
					for (var i in RVS.JHOOKS.extendSlideAnimationRowCol) {
						if(!RVS.JHOOKS.extendSlideAnimationRowCol.hasOwnProperty(i)) continue;
						RVS.JHOOKS.extendSlideAnimationRowCol[i](type,keep,change);
					}
			}



			RVS.F.setSlideAnimPresetToCustom();
			RVS.F.redrawSlideBG();
		});

		RVS.DOC.on('click','.editslide',function() {
			setActiveSlide({id:this.dataset.id});
			return false;
		});



		RVS.DOC.on('showLastEditedSlideStatic',function() {
			jQuery('.showunderstatic').removeClass("showunderstatic");
			if (RVS.SLIDER[RVS.S.slideId].slide.static.isstatic && RVS.S.lastShownSlideId!==undefined) {
				if (RVS.SLIDER[RVS.S.slideId].slide.static.lastEdited) {
					jQuery('#slide_'+RVS.S.lastShownSlideId).addClass("showunderstatic");
					jQuery('#slide_'+RVS.S.lastShownSlideId).find('._lc_.selected').removeClass("selected");
					setTimeout(function() {jQuery('#slide_'+RVS.S.slideId).addClass("hideslotsinslide");},200);
					// jQuery()
				} else {
					jQuery('#slide_'+RVS.S.slideId).removeClass("hideslotsinslide");
				}
			}
		});



		RVS.DOC.on('click','.open_close_slide',function() {
			setActiveSlide({id:this.dataset.id, openclose:true});
			return false;
		});

		// LISTEN TO ACTIVE SELECTED SLIDE TRANSITION
		RVS.DOC.on('click','.added_slide_transition',function() {
			jQuery('.added_slide_transition.selected').removeClass("selected");
			this.className = this.className+" selected";
			RVS.S.slideTrans = jQuery(this).index();
			RVS.F.updateEasyInputs({container:jQuery('#active_transitions_settings'), path:RVS.S.slideId+".slide.", trigger:"init"});
			RVS.F.updateSlideFrames();
		});

		RVS.DOC.on('click','.transition-replace',function() {RVS.DOC.trigger("showhidetransitions");});

		RVS.DOC.on("mouseenter mouseleave", '#active_transitions_innerwrap_results .presetelement.dark_btn, #active_transitions_innerwrap_results .presets_listelement.dark_btn',function(e) {
			clearTimeout(window.backToDefaultAnimationTimer);

			RVS.S.demoSlideAnim = e.type==="mouseenter" ? {
				key : this.dataset.custom=="true" && this.dataset!==undefined && this.dataset.key!==undefined ? (this.dataset.key).replace("custom_","") : ""+this.dataset.rndgrp!=="undefined" || ""+this.dataset.rndmain!=="undefined" ?  RVS._R.getRandomSlideTrans(this.dataset.rndmain,this.dataset.rndgrp,RVS.LIB.SLTR)  : this.dataset.key,
				preset : this.dataset.custom=="true" ? RVS.F.safeExtend(true,{},RVS.LIB.SLTR_CUSTOM[this.dataset.key.replace("custom_","")].preset) : undefined
			} : undefined;
			window.backToDefaultAnimationTimer = setTimeout(RVS.F.showExampleOrSelectedSlideAnim,e.type==="mouseenter" ? 200 : 0);
		});
	}

	RVS.F.clearSBGSAttrs = function(_,onlyhelp) {
		delete _.help_canvas;
		delete _.help_ctx;
		if (_.cube!==undefined) {
			if (document.body.contains(_.cube.c)) _.cube.c.parentNode.removeChild(_.cube.c);
			delete _.cube;
		}
		tpGS.gsap.set([_.canvas,_.sbg],{x:0, y:0, z:0, rotateX:0, rotateY:0,rotateZ:0});

		if (onlyhelp!==true) {
			delete _.shadowCTX;
			delete _.shadowCanvas;
			delete _.SLOT;
			delete _.cDIMS;
			delete _.mDIM;
			delete _.usebgColor;
			delete _.usepattern;
			RVS.F.redrawSlideBG();
		}
	}
	// SHOW DEMO ( PREVIEW ) OR SELECTED SLIDE ANIMAITION
	RVS.F.showExampleOrSelectedSlideAnim = function() {


		if (RVS.S.demoSlideAnim!==undefined) {
			RVS.TL[RVS.S.slideId].slide.pause();
			RVS.F.clearSBGSAttrs(RVS.SBGS[RVS.S.slideId].c,true);
			RVS.F.clearSBGSAttrs(RVS.SBGS[RVS.S.slideId].n,true);

			RVS.TL[RVS.S.slideId].slideRepeat = true;
			RVS.F.buildSlideAnimation(getSlideTransitionDefaults(RVS.S.demoSlideAnim.preset!==undefined ? {preset:RVS.S.demoSlideAnim.preset} : {key:RVS.S.demoSlideAnim.key}));
			RVS.TL[RVS.S.slideId].slide.progress(0).play();
		} else {
			// IF WE DELETE ALL, WE NEED TO RECREATE THEM !!!
			RVS.TL[RVS.S.slideId].slide.pause();
			RVS.F.clearSBGSAttrs(RVS.SBGS[RVS.S.slideId].c);
			RVS.F.clearSBGSAttrs(RVS.SBGS[RVS.S.slideId].n);
		}
	}



	function buildSlideToLinkDrop() {
		var linktoslide = document.getElementById('slide_seo_linktoslide');
		if (linktoslide!==null) {
			var iH = "";

			iH += '<option value="nothing">- Not Choosen -</option>';
			iH += '<option value="next">- Next Slide  -</option>';
			iH += '<option value="prev">- Previous Slide -</option>';
			iH += '<option value="scroll_under">- Scroll Below Slider -</option>';
			for (var slides in RVS.SLIDER.slideIDs) {
				if(!RVS.SLIDER.slideIDs.hasOwnProperty(slides)) continue;
				var nu = RVS.SLIDER.slideIDs[slides],
					tx = RVS.SLIDER[nu].slide.title;
				tx = tx===undefined ? "Slide" : tx;
				iH += '<option value="'+nu+'">'+tx+' (ID:'+nu+')</option>';
			}
			linktoslide.innerHTML = iH;
		}
	}

	/*
	INIT CUSTOM EVENT LISTENERS FOR TRIGGERING FUNCTIONS
	*/
	function initLocalListeners() {
		//RVS.DOC.on('',function() {);

		RVS.DOC.on('updateslidebasic',RVS.F.redrawSlideBG);
		RVS.DOC.on('updateslidebasicmpeg',function() {
			RVS.F.videoExtract.get(RVS.SLIDER[RVS.S.slideId].slide.bg.mpeg,function(data) {
				RVS.F.setBGPosterImage(data.path,data.id,"slide");
				RVS.F.redrawSlideBG();
			},RVS.SLIDER[RVS.S.slideId].slide.bg.video.startAt,"slide");
		});

		RVS.DOC.on('coloredit colorcancel',colorEditSlider);
		RVS.DOC.on('showSlideFilter',tempSlideFilter);
		RVS.DOC.on('updateKenBurnBasics',function() {
			updateKenBurnBasics();
		});

		RVS.DOC.on('updateKenBurnSettings',function() {updateKenBurnSettings(true)});
		RVS.DOC.on('previewKenBurn',function() {RVS.TL[RVS.S.slideId].panzoom.play();});
		RVS.DOC.on('previewStopKenBurn',function() { RVS.TL[RVS.S.slideId].panzoom.pause();});

		RVS.DOC.on('rewindKenBurn',function() { RVS.TL[RVS.S.slideId].panzoom.progress(0).pause();});
		RVS.DOC.on('beforeLayoutModeChange accordionaction',function() {
			RVS.F.updateTimeLine({state:"stop",timeline:"panzoom"});
			RVS.F.changeSwitchState({el:jQuery('#kenburn_simulator')[0],state:"play"});
		});
		RVS.DOC.on('updateslidethumbs',function() {
			updateSlideThumbs();
		});
		RVS.DOC.on('resetslideadminthumb',function(e,p) {
			RVS.F.updateSliderObj({path:RVS.S.slideId+"."+p,val:""});
			updateSlideThumbs();
		});

		RVS.DOC.on('changeToLayerMode',function() { RVS.F.showHideLayerEditor({mode:"slidecontent"});});
		RVS.DOC.on('changeToSlideMode',function() { RVS.F.showHideLayerEditor({mode:"slidelayout"});});

		RVS.DOC.on("windowresized",RVS.F.redrawSlideBG);


		RVS.DOC.on('sliderSizeChanged',RVS.F.redrawSlideBG);


		RVS.DOC.on('showhidetransitions',function() {
			var ts = jQuery('#transition_selector');
			if (ts.is(':visible'))
				ts.hide();
			else
				ts.show();
		});

		RVS.DOC.on('updateSlideNameInList',function() {
			jQuery('#slide_list_element_title_'+RVS.S.slideId).html(RVS.SLIDER[RVS.S.slideId].slide.title);
		});

		RVS.DOC.on('click','#do_edit_slidename',function() {
			jQuery('#slide_title_field').trigger('focus');
		});

		RVS.DOC.on('slide_ajax_calls',function(e, _) {

			var preset,
				key;



			// GET CHANGES
			if (_.mode==="overwrite" || _.mode==="create") preset = RVS.F.safeExtend(true,{},RVS.SLIDER[RVS.S.slideId].slide.slideChange);

			// GET TINDEX
			if (_.mode==="overwrite" || _.mode=="rename") key = _.pl.data("key");

			// RENAME, TAKE FIRST EXISTING OBJECT
			if (_.mode==="rename") { preset = RVS.LIB.SLTR_CUSTOM[_.key].preset; RVS.LIB.SLTR_CUSTOM[_.key].title=_.newname;}

			if (_.mode==="delete") {

				RVS.F.ajaxRequest('delete_custom_templates_slidetransitions', {id:_.key.replace("custom_","")},function(response) {
					if (response.success) {
						delete RVS.LIB.SLTR_CUSTOM[_.key];
						_.pl.remove();
					}
				});
			} else {
				// CALL CREATE / RENAME / OVERWRITE AJAX FUNCTION
				RVS.F.ajaxRequest('save_custom_templates_slidetransitions', {id:_.key, obj:{title:_.newname, preset:preset}}, function(response){
					if(response.success) {
						RVS.LIB.SLTR_CUSTOM[response.data.id] = {title:_.newname, preset:preset};
						if (_.mode==="create") _.element[0].dataset.key = response.data.id;
						if (_.mode==="rename") _.pl.find('.cla_custom_name').text(_.newname);
					}
				});
			}

		 })
	}


	/*
	SHOW HIDE LAYER / SLIDE EDITOR MODE
	*/
	RVS.F.showHideLayerEditor = function(obj) {
		var selected;
		RVS.eMode = RVS.eMode===undefined ? {top:"", menu:""} : RVS.eMode;
		if (obj.mode === "slidecontent") {
			RVS.C.vW.classList.add('mode__slidecontent');
			RVS.C.vW.classList.remove('mode__slidestyle');
			RVS.eMode.top = "layer";
			selected = jQuery('.layer_submodule_trigger.selected');
			if (selected!==undefined && obj.openSettings!==false) RVS.F.openSettings({forms:selected.data("forms"), uncollapse:selected[0].dataset.collapse});
		} else {
			RVS.C.vW.classList.remove('mode__slidecontent');
			RVS.C.vW.classList.add('mode__slidestyle');
			RVS.eMode.top = "slide";
			selected  = jQuery('.slide_submodule_trigger.selected');
		}
		if (selected!==undefined && selected.length>=1 && selected.data("forms")!==undefined) RVS.eMode.menu = selected.data("forms")[0];
	};



	/*
	INIT KEN BURN DRAG FUNCTION
	*/
	function initKenBurnDrag() {
		pzdrag = {	container:jQuery('#kenburn_timeline')};
		pzdrag.pin = pzdrag.container.find('.pz_pin');
		pzdrag.done = pzdrag.container.find('.pz_timedone');
		pzdrag.pinWidth = 	9;
		pzdrag.hovered = false;

		pzdrag.pin.draggable({
			axis:"x",
			containment: "parent",
			start:function(event,ui) {
				pzdrag.container.addClass("indrag");
				pzdrag.containerWidth = pzdrag.container.width();
			},
			stop:function(event,ui) {
				pzdrag.container.removeClass("indrag");
			},
			drag:function(event,ui) {
				updatePzTimeDone({left:ui.position.left, force:true});
				RVS.F.updateTimeLine({state:"progress",timeline:"panzoom",prgs:ui.position.left / (pzdrag.containerWidth -pzdrag.pinWidth)});
			}
		});
		pzdrag.container.on('mouseenter',function() {
			pzdrag.hovered = true;
			pzdrag.laststate = RVS.F.updateTimeLine({state:"getstate",timeline:"panzoom"});
			RVS.F.updateTimeLine({state:"pause",timeline:"panzoom"});
		}).on('mouseleave',function() {
			pzdrag.hovered = false;
			if (pzdrag.laststate)
				RVS.F.updateTimeLine({state:"play",timeline:"panzoom"});
		});
	}

	function updatePzTimeDone(obj) {
		if (pzdrag.hovered===false || obj.force===true) {
			tpGS.gsap.set(pzdrag.done,{width:obj.left});
			if (obj.auto)
				tpGS.gsap.set(pzdrag.pin,{left:obj.left});
		}
	}

	/*
	UPDATE THE THUMBS IN THE SLIDE (ADMIN AND NAVIGATION)
	*/
	function updateSlideThumbs(obj) {
		obj = obj===undefined ? {id:RVS.S.slideId, target:['#admin_purpose_thumbnail, #slide_list_element_'+RVS.S.slideId+' .sle_thumb'], default:true} : obj;

		var adminsrc = RVS.SLIDER[obj.id].slide.thumb.customAdminThumbSrc,
			navsrc = RVS.SLIDER[obj.id].slide.thumb.customThumbSrc;



		if (adminsrc===null || adminsrc==null || adminsrc===undefined || adminsrc.length<3) {
			var thmbbg = RVS.F.getSlideBGDrawObj(obj);
			if (thmbbg.backgroundImage==="" && thmbbg.backgroundColor==="transparent") {
				thmbbg.backgroundImage =  'url('+RVS.ENV.plugin_url+"admin/assets/images/trans_tile.png"+')';
				thmbbg.backgroundSize = "16px";
				thmbbg['background-size'] = "16px";
				thmbbg.backgroundRepeat = "repeat";
			}
			if (obj.target!==undefined && obj.target[0]!==undefined) tpGS.gsap.set(obj.target,thmbbg);
		}
		else
			tpGS.gsap.set(obj.target,{"background-size":"cover", backgroundPosition:"center center", backgroundRepeat:"no-repeat",backgroundImage:'url('+adminsrc+')'});



		if (obj.default) {
			if (navsrc===undefined || navsrc.length<3 || navsrc[navsrc.length-1]==="/")
				tpGS.gsap.set(["#navigation_purpose_thumbnail","#thumbs_"+obj.id,"#tabs_"+obj.id,"#bullets_"+obj.id,"#arrow_"+obj.id],RVS.F.getSlideBGDrawObj());
			else
				tpGS.gsap.set(["#navigation_purpose_thumbnail","#thumbs_"+obj.id,"#tabs_"+obj.id,"#bullets_"+obj.id,"#arrow_"+obj.id],{"background-size":"cover", backgroundPosition:"center center", backgroundRepeat:"no-repeat",backgroundImage:'url('+navsrc+')'});
		}
	}

	/**
	UPDATE SORTABLE FEATURE OF SLIDE LIST
	**/
	RVS.F.makeSlideListSortable = function() {
		var sl = RVS.C.slideList;
		if (RVS.C.slideList.hasClass("ui-sortable")) RVS.C.slideList.sortable('destroy');

		indexSlides();
		RVS.C.slideList.sortable({
			item:".sortable_slide_list_element",
			cancel:"#theslidermodule, #newslide, .do_not_sort_slide_list_element",
			start:function(event,ui) {
				// var nodes = Array.prototype.slice.call(document.getElementById("slidelist").getElementsByClassName("sortable_slide_list_element"));
				RVS.C.vW.classList.add("slides_in_sort");
				RVS.C.slideList.sortable("refreshPositions");
			},
			stop:function(event,ui) {
				RVS.C.vW.classList.remove("slides_in_sort");
				var nodes = Array.prototype.slice.call(document.getElementById("slidelist").getElementsByClassName("sortable_slide_list_element")),
					_nn = [],
					stat = "";
				for (var sti in RVS.SLIDER.slideIDs) {
					if(!RVS.SLIDER.slideIDs.hasOwnProperty(sti)) continue;
					if ((""+RVS.SLIDER.slideIDs[sti]).indexOf("static_")>=0) stat = RVS.SLIDER.slideIDs[sti];
				}
				for (var i in nodes) {
					if(!nodes.hasOwnProperty(i)) continue;
					_nn.push(nodes[i].dataset.ref);
				}
				_nn.push(stat);
				RVS.F.updateSliderObj({path:'slideIDs',val:_nn});
				indexSlides();
			}
		});
	}

	/*
	INDEXING THE SLIDES
	*/
	function indexSlides() {
		var indexes = {},
			_index=1;
		for (var ind in RVS.SLIDER.slideIDs) {
			if(!RVS.SLIDER.slideIDs.hasOwnProperty(ind)) continue;
			indexes[RVS.SLIDER.slideIDs[ind]] = _index;
			if ((RVS.F.isNumeric(RVS.SLIDER.slideIDs[ind]) || RVS.SLIDER.slideIDs[ind].indexOf("static")==-1) && (RVS.SLIDER[RVS.SLIDER.slideIDs[ind]]!==undefined && RVS.SLIDER[RVS.SLIDER.slideIDs[ind]].slide!==undefined && RVS.SLIDER[RVS.SLIDER.slideIDs[ind]].slide.child!==undefined && (RVS.SLIDER[RVS.SLIDER.slideIDs[ind]].slide.child.parentId==undefined || RVS.SLIDER[RVS.SLIDER.slideIDs[ind]].slide.child.parentId==""))) _index++;
		}

		for (var ind in indexes) {
			if(!indexes.hasOwnProperty(ind)) continue;
			var el = document.getElementById('slide_list_element_title_index_'+ind);
			if (el!==null && el!==undefined) el.innerHTML = "#"+indexes[ind]+" ";
		}

	}

	function FOtoA(a) {
		//If Multiple Values written like this:  {1:"fade",2:"parallax"....} convert them to array: ["fade","parallax"....]
        if (typeof a==="object" && !Array.isArray(a) && ((a[0]!==undefined && (typeof a[0]==="string" || typeof a[0]==="number")) || (a[1]!==undefined && (typeof a[1]==="string" || typeof a[1]==="number")))) a = Object.values(a);
		return a;
	}

	function FCV(b) {
        b = b===undefined ? ['default'] : b;
        return Array.isArray(b) && typeof b[0]!=='object' ?  b : typeof b[0]==="object" ? Object.values(b[0]) : [b];
    }

	/**
	SLIDE ANIMATION SETTINGS
	**/
    RVS.F.updateSlideAnimationView = function() {
		RVS.F.updateEasyInputs({container:jQuery('#active_transitions_settings'), path:RVS.S.slideId+".slide.", trigger:"init"});
        if (RVS.C.sltran===undefined) {
            RVS.C.sltran = {};
            RVS.C.sltmenu = {};
            RVS.C.sltaddon = RVS.C.sltaddon===undefined ? {} : RVS.C.sltaddon;
            RVS.C.sltran.all_globals = jQuery('#sltrans_all_globals');
            RVS.C.sltran.pause = jQuery('#sltrans_pause');
            RVS.C.sltran.flow = jQuery('#sltrans_flow');
            RVS.C.sltran.in_full_wrap = jQuery('#sltrans_in_full_wrap');
            RVS.C.sltran.in_rowcol_wrap = jQuery('#sltrans_in_rowcol_wrap');
            RVS.C.sltran.in_ease_wrap = jQuery('#sltrans_in_ease_wrap');
            RVS.C.sltran.in_mamo_wrap = jQuery('#sltrans_in_mamo_wrap');
            RVS.C.sltran.in_xy_wrap = jQuery('#sltrans_in_xy_wrap');
            RVS.C.sltran.in_rzo_wrap = jQuery('#sltrans_in_rzo_wrap');
            RVS.C.sltran.in_sxsy_wrap = jQuery('#sltrans_in_sxsy_wrap');
            RVS.C.sltran.in_auto_input_wrap = jQuery('#sltrans_in_auto_input_wrap');
            RVS.C.sltran.in_filter_input_wrap = jQuery('#sltrans_in_filter_input_wrap');
            RVS.C.sltran.filters_wrap = jQuery('#sltrans_filters_wrap');
            RVS.C.sltran.d3_wrap = jQuery('#sltrans_3d_wrap');

            RVS.C.sltran.out_rowcol_wrap = jQuery('#sltrans_out_rowcol_wrap');
            RVS.C.sltran.out_ease_wrap = jQuery('#sltrans_out_ease_wrap');
            RVS.C.sltran.out_mask_wrap = jQuery('#sltrans_out_mask_wrap');
            RVS.C.sltran.out_xy_wrap = jQuery('#sltrans_out_xy_wrap');
            RVS.C.sltran.out_rzo_wrap = jQuery('#sltrans_out_rzo_wrap');
            RVS.C.sltran.out_sxsy_wrap = jQuery('#sltrans_out_sxsy_wrap');
            RVS.C.sltran.out_full_wrap = jQuery('#sltrans_out_full_wrap');

            RVS.C.sltmenu.in = jQuery('#slidein_ts_wrapbrtn');
            RVS.C.sltmenu.out = jQuery('#slideout_ts_wrapbrtn');
            RVS.C.sltmenu.filter = jQuery('#slidefilter_ts_wrapbrtn');
            RVS.C.sltmenu.ddd = jQuery('#slide3d_ts_wrapbrtn');
        }

        var i,j,q,u;

        for (i in RVS.C.sltaddon) if (RVS.C.sltaddon.hasOwnProperty(i) && RVS.C.sltaddon[i].slt_areas!==undefined) for (q in RVS.C.sltaddon[i].slt_areas) if (RVS.C.sltaddon[i].slt_areas.hasOwnProperty(q)) RVS.C.sltaddon[i].slt_areas[q].hide();
        for (i in RVS.C.sltaddon) if (RVS.C.sltaddon.hasOwnProperty(i) && RVS.C.sltaddon[i].menu!==undefined) for (q in RVS.C.sltaddon[i].menu) if (RVS.C.sltaddon[i].menu.hasOwnProperty(q)) RVS.C.sltaddon[i].menu[q][0].classList.add("disabled");

        switch (RVS.SLIDER[RVS.S.slideId].slide.slideChange.e) {
            case 'none':
                for (i in RVS.C.sltran) if (RVS.C.sltran.hasOwnProperty(i)) RVS.C.sltran[i].hide();
                for (i in RVS.C.sltmenu) if (RVS.C.sltmenu.hasOwnProperty(i)) RVS.C.sltmenu[i][0].classList.add("disabled");
            break;
            case 'basic':
                for (i in RVS.C.sltran) if (RVS.C.sltran.hasOwnProperty(i)) RVS.C.sltran[i].show();
                for (i in RVS.C.sltmenu) if (RVS.C.sltmenu.hasOwnProperty(i)) RVS.C.sltmenu[i][0].classList.remove("disabled");
            break;
            case "slidingoverlay":
                for (i in RVS.C.sltran) if (RVS.C.sltran.hasOwnProperty(i)) if (jQuery.inArray(i,["in_full_wrap", "out_full_wrap", "filters_wrap","pause","flow","d3_wrap"])>=0) RVS.C.sltran[i].hide(); else RVS.C.sltran[i].show();
                for (i in RVS.C.sltmenu) if (RVS.C.sltmenu.hasOwnProperty(i)) RVS.C.sltmenu[i][0].classList.add("disabled");
                //for (i in RVS.C.sltmenu) if (RVS.C.sltmenu.hasOwnProperty(i)) if (jQuery.inArray(i,["in","out","filter"])) RVS.C.sltmenu[i][0].classList.add("disabled"); else RVS.C.sltmenu[i][0].classList.remove("disabled");
            break;
            default:
                RVS.DOC.trigger('updateSlideAnimationViewDefault');
            break;
		}
	}

    // Update Selected Anim Lisrt
    RVS.F.udpateSelectedSlideAnim = function(open) {
        if (RVS.S.calledSlideAnimListUpdate) return;
        RVS.S.calledSlideAnimListUpdate = true;
        requestAnimationFrame(function() {
            RVS.S.calledSlideAnimListUpdate=false;
            RVS.S.ATI.find('.s_s_preset').removeClass('s_s_preset');
            RVS.S.ATIR.find('.s_s_preset').removeClass('s_s_preset');
            var el = RVS.S.ATIR.find('.presets_listelement[data-key="'+RVS.SLIDER[RVS.S.slideId].slide.slideChange.preset+'"]')[0];
            if (el!==undefined && el!==null) {
                el.classList.add("s_s_preset");
                var main = document.getElementById(el.dataset.grpid),
                    grp = document.getElementById(el.dataset.grpid+"_"+el.dataset.grp),
                    head;

                if (main!==undefined && main!==null) {
                    main.classList.add('s_s_preset');
                    if (open && main.className.indexOf("open")==-1) {
                        head = main.getElementsByClassName('presets_liste_head');
                        if (head!==undefined && head!==null && head.length>0) head[0].click();
                    }
                }

                if (grp!==undefined && grp!==null) {
                    grp.classList.add('s_s_preset');
                    if (open && grp.className.indexOf("open")==-1) {
                        head = grp.getElementsByClassName('presetssgroup_head');
                        if (head!==undefined && head!==null && head.length>0) head[0].click();
                    }
                }
			}
        });
    }

    function appendPresets(grp,title,id,ref) {

        var cont = RVS.F.createPresets({
            modern:true,
            icon:grp.icon,
            groupid:id,
            groupclass:"slide_trans_templates",
            maingrpclass:"slide_trans_mains",
            title:title,
            customevt:"slide_ajax_calls",
            groups: grp,
            ref:ref,
            favoriteAjax:"slide_transitions",
            favoriteList:RVS.LIB.SLTR_FAVORIT,
            onclick: function(key,custom,main,sub,rndgrp,rndmain) {
                        if (custom=='true' || custom==true)
                            addTransitionToActive({preset:RVS.LIB.SLTR_CUSTOM[key.replace("custom_","")].preset,customkey:key});
                        else
                            addTransitionToActive({key:key,main:main,sub:sub});
                        RVS.DOC.trigger("showhidetransitions");
                        RVS.F.updateSlideFrames();
                        RVS.F.udpateSelectedSlideAnim();
                        RVS.F.updateSlideAnimationView();
            }});
        RVS.S.ATI.append(cont.main);
        RVS.S.ATIR.append(cont.inner);
	}

    function createSlideAnimationList() {

        RVS.LIB.SLTR_CUSTOM = RVS.LIB.SLTR_CUSTOM===undefined ? {} : RVS.LIB.SLTR_CUSTOM;
        var list = {};
        RVS.S.ATI = jQuery('#active_transitions_innerwrap'),
        RVS.S.ATIR = jQuery('#active_transitions_innerwrap_results');

        // EXTEND SLIDE LIST IF NEEDED
        for (var i in RVS.JHOOKS.extendSlideAnimationList) {
            if(!RVS.JHOOKS.extendSlideAnimationList.hasOwnProperty(i)) continue;
            RVS.JHOOKS.extendSlideAnimationList[i]();
        }

        for (var main in RVS.LIB.SLTR) {
            if (!RVS.LIB.SLTR.hasOwnProperty(main)) continue;
            list[main] = list[main]===undefined ? {} : list[main];
            for (var group in RVS.LIB.SLTR[main]) {
                if (!RVS.LIB.SLTR[main].hasOwnProperty(group)) continue;
                if (group==="noSubLevel") list[main].noSubLevel = RVS.LIB.SLTR[main].noSubLevel;
                else
                if (group=="icon") list[main].icon = RVS.LIB.SLTR[main].icon;
                else
                if (RVS.LIB.SLTR[main].noSubLevel) {
                    list[main][main] = list[main][main]===undefined ? { title:RVS_LANG["sltr_"+main]===undefined ? main : RVS_LANG["sltr_"+main], elements:{}} : list[main][main];
                    list[main][main].elements[group] = {title:RVS.LIB.SLTR[main][group].title, grp: group, main:main};
                    if (RVS.LIB.SLTR[main][group].rndgrp!==undefined) list[main][main].elements[group].rndgrp = RVS.LIB.SLTR[main][group].rndgrp;
                    if (RVS.LIB.SLTR[main][group].rndmain!==undefined) list[main][main].elements[group].rndmain = RVS.LIB.SLTR[main][group].rndmain;
                } else {
                    list[main][group] = list[main][group]===undefined ? { title:RVS_LANG["sltr_"+group]===undefined ? group : RVS_LANG["sltr_"+group], elements:{}} : list[main][group];
                    for (var element in RVS.LIB.SLTR[main][group]) if (element!=="icon" && RVS.LIB.SLTR[main][group].hasOwnProperty(element)) list[main][group].elements[element] = {title:RVS.LIB.SLTR[main][group][element].title, grp: group, main:main};
                }
            }
		}

        var j = 0;
        for (main in list) if (list.hasOwnProperty(main)) appendPresets(list[main],(RVS_LANG["sltr_"+main]===undefined ? main : RVS_LANG["sltr_"+main]), 'slide_trans_templates'+(j++),main);


        //Add Custom
        appendPresets({icon:"tune",custom:{title:RVS_LANG.customtransitionpresets,elements:RVS.LIB.SLTR_CUSTOM,custom:true}}, RVS_LANG.customtemplates, 'slide_trans_templates_custom',"custom");

        updateTransitionListe();

    }

	function convertToArray(a) {
		if (typeof a === "object" && !Array.isArray(a))
			return Object.keys(a).map(function(key) { return [a[key]];});
		else
		return a;
	}

	/**
	SLIDE ANIMATION CREATE / ADD ACTIVE ANIMATION TO LIST
	**/
    function addTransitionToActive(obj) {
        var tl = getSlideTransitionDefaults(obj);
        RVS.F.openBackupGroup({id:'slide_transition',txt:"Slide Transition Change",icon:'calendar_view_day'});
        RVS.F.backup({    path:RVS.S.slideId+".slide.slideChange",
                        val:tl,
                        old:RVS.F.safeExtend(true,{},RVS.SLIDER[RVS.S.slideId].slide.slideChange),
                        backupType:"object",
                        mode:'slidetransition',
                        callBack:function() {
                            RVS.F.updateEasyInputs({container:jQuery('#form_slide_transition'), path:RVS.S.slideId+".slide.", trigger:"init"});
                        }
                    });

        RVS.SLIDER[RVS.S.slideId].slide.slideChange = RVS.F.safeExtend(true,{},RVS.SLIDER[RVS.S.slideId].slide.slideChange,tl);

        RVS.F.closeBackupGroup({id:'slide_transition'});

        RVS.F.updateEasyInputs({container:jQuery('#form_slide_transition'), path:RVS.S.slideId+".slide.", trigger:"init"});

        RVS.F.redrawSlideBG();
	}



	/**
	UPDATE TRANSITION LIST NOW
	**/
	function updateTransitionListe() {
		var group = jQuery('.transgroup.selected').data('group');
		jQuery('.inner_transitions').hide();
		jQuery('.inner_transitions.'+group).show();

		RVS.F.udpateSelectedSlideAnim();
	}


	/*
	TEMPORARY FILTER FOR SLIDE IMAGE
	*/
	function tempSlideFilter(e,param) {
		RVS.C.slide.find('.slots_wrapper').attr('class','slots_wrapper '+param);
	}

	function colorEditSliderSub(n,val,c) {
		switch (n) {
			case "slide_bg_color":	RVS.F.redrawSlideBG({liveColorChange:true, backgroundColor:val}); break;
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
		colorEditSliderSub(window.lastColorEditjObj[0].name, val,canceled)
		if (GC && canceled!==true)
			for (var i in GC.ref) {
				if (GC.ref[i].type==="slide" && GC.ref[i].slide===RVS.S.slideId) colorEditSliderSub(GC.ref[i].inpname,val);
				if (GC.ref[i].type==="slide" && onSave) {
					RVS.F.updateSliderObj({path:GC.ref[i].r,val:val});
					if (GC.ref[i].slide===RVS.S.slideId) {
						var upinp = jQuery('input[name='+GC.ref[i].inpname+']');
						if (upinp.length>0) {
							upinp[0].value = val;
							upinp.rsColorPicker("refresh");
						}
					} else RVS.F.slideinWork(GC.ref[i].slide);
				}
			}
	}



	/***
	PAN ZOOM MAGIC
	***/

	RVS.F.buildKenBurn = function() {
		updateKenBurnBasics();
	}

	/*
	MOVE KEN BURN SETTINGS FROM ONE CONTAINER TO AN OTHER
	*/
	function updateKenBurnBasics(ignoreredraw) {
		if (RVS.SLIDER[RVS.S.slideId].slide.panzoom.set) {
			jQuery('#slide_bg_settings_wrapper').appendTo(jQuery('#ken_burn_bg_setting_on'));
			jQuery('#internal_kenburn_settings').hide();
			jQuery('#kenburnissue').hide();
		} else {
			jQuery('#slide_bg_settings_wrapper').appendTo(jQuery('#ken_burn_bg_setting_off'));
			RVS.SBGS[RVS.S.slideId].n.canvas.style.filter="none";
			if (RVS.TL[RVS.S.slideId]!==undefined && RVS.TL[RVS.S.slideId].panzoom!=undefined) {
				RVS.TL[RVS.S.slideId].panzoom.pause().kill();
				RVS.TL[RVS.S.slideId].panzoom=undefined;
				RVS.SBGS[RVS.S.slideId].n.panzoom = undefined;
			}
		}
		//Update Basics should Redraw the BG again
		if (ignoreredraw!==true) RVS.F.redrawSlideBG();
	}


	/*
	UPDATE KENBURN TIMELINE WITH OUR WITHOUT PROGRESS
	*/

	function getPanValues(_) {
		return {
			duration:parseInt(_.duration)/1000,
			ease:_.ease,
			scalestart:parseInt(_.fitStart)/100,
			scaleend:parseInt(_.fitEnd)/100,
			rotatestart:parseFloat(_.rotateStart),
			rotateend:parseFloat(_.rotateEnd),
			blurstart:_.blurStart,
			blurend:_.blurEnd,
			offsetstart:[_.xStart,_.yStart],
			offsetend:[_.xEnd,_.yEnd]
		}
	}


	function updateKenBurnSettings(cont) {
		if (RVS.SLIDER[RVS.S.slideId].slide.bg.type!=="image" && RVS.SLIDER[RVS.S.slideId].slide.bg.type!=="external") RVS.SLIDER[RVS.S.slideId].slide.panzoom.set = false;
		if (!RVS.SLIDER[RVS.S.slideId].slide.panzoom.set) {
			delete RVS.SBGS[RVS.S.slideId].n.panzoom;
			return;
		}
		if ((RVS.SBGS[RVS.S.slideId].n.type!=="image" || RVS.SBGS[RVS.S.slideId].n.loadobj.progress==="loaded") && RVS.SBGS[RVS.S.slideId].c.loadobj.progress==="loaded") {
			var prgs = 0,
				tl = 0,
				active = false;

			if (RVS.TL[RVS.S.slideId].panzoom!==undefined) {
				tl = RVS.TL[RVS.S.slideId].panzoom.time();
				prgs = RVS.TL[RVS.S.slideId].panzoom.progress();
				active = RVS.TL[RVS.S.slideId].panzoom.isActive();
				RVS.TL[RVS.S.slideId].panzoom.kill();
			}
			RVS.SBGS[RVS.S.slideId].n.panzoom = true;
			RVS.SBGS[RVS.S.slideId].n.panvalues = getPanValues(RVS.SLIDER[RVS.S.slideId].slide.panzoom);


			RVS._R.startPanZoom(RVS.SBGS[RVS.S.slideId].n,undefined,0.01,RVS.SBGS[RVS.S.slideId].n.skeyindex,'prepare');
			RVS.TL[RVS.S.slideId].panzoom.render(tl,true,true);

			pzdrag.containerWidth= pzdrag.containerWidth===undefined ? pzdrag.container.width() : pzdrag.containerWidth;
			updatePzTimeDone({left: (prgs *(pzdrag.containerWidth -pzdrag.pinWidth)),auto:true });


			RVS.TL[RVS.S.slideId].panzoom.eventCallback("onUpdate", function() {
				pzdrag.containerWidth= pzdrag.containerWidth===undefined ? pzdrag.container.width() : pzdrag.containerWidth;
				updatePzTimeDone({left: (RVS.TL[RVS.S.slideId].panzoom.progress() *(pzdrag.containerWidth -pzdrag.pinWidth)),auto:true });
			});

			RVS.TL[RVS.S.slideId].panzoom.eventCallback("onComplete", function() {
				RVS.F.changeSwitchState({el:jQuery('#kenburn_simulator')[0],state:"play"});
				RVS.TL[RVS.S.slideId].panzoom.pause();
			});

			if (active) RVS.TL[RVS.S.slideId].panzoom.play();
		}
	}

	/*
	BUILD AND EXTEND DEFAULT SLIDE
	*/
	RVS.F.addSlideObj = function(obj,compare) {

		var empty = obj===undefined || jQuery.isEmptyObject(obj);
		obj = obj===undefined ? {} : obj;

		var newSlide = {};
		newSlide.addOns = obj.addOns || {};
		newSlide.version = RVS.ENV.revision;
		/*newSlide.version = _d(obj.version,"6.0.0");
		newSlide.version = newSlide.version<"6.0.0" ? "6.0.0" : newSlide.version;*/
		newSlide.static = _d(obj.static,{
				isstatic:false,
				overflow:"hidden",
				position:"front",
				lastEdited:true
		});
		newSlide.runtime=_d(obj.runtime,{
			collapsedGroups:[]
		});
		newSlide.title = _d(obj.title,"New Slide");
		newSlide.child = _d(obj.child,{
			parentId:"",
			language:""
		});
		newSlide.bg = _d(obj.bg,{
			type : "trans",
			color:"#ffffff",
			externalSrc:"",
			fit:"cover",
			fitX:"100",
			fitY:"100",
			position:"center center",
			positionX:"0",
			positionY:"0",
			repeat:"no-repeat",
			image:"",
			imageId:"",
			imageFromStream:false,
			imageSourceType:"full",
			imageLib:"nothing",
			galleryType:"gallery",
			mpeg:"",
			ogv:"",
			webm:"",
			vimeo:"",
			youtube:"",
			mediaFilter:"none",
			video:{
				args:"",
				argsVimeo:"",
				dottedOverlay:"none",
				dottedOverlaySize:1,
				dottedColorA:"transparent",
				dottedColorB:"#000000",
				startAt:"",
				endAt:"",
				fitCover:true,
				forceRewind:true,
				loop:true,
				pausetimer:false,
				mute:true,
				nextSlideAtEnd:false,
				ratio:"16:9",
				speed:1,
                volume:0,
                startAfterTransition:false
            },
			videoId:"",
			videoFromStream:false
		});
        if (newSlide!==undefined && newSlide.bg!==undefined && newSlide.bg.video!==undefined) {
            if (newSlide.bg.video.dottedOverlay.indexOf("white")>0) newSlide.bg.video.dottedColorB = "rgba(255,255,255,255)";
            if (newSlide.bg.video.dottedOverlay.indexOf("twoxtwo")>=0) newSlide.bg.video.dottedOverlay = "1";
            else if (newSlide.bg.video.dottedOverlay.indexOf("threexthree")>=0) newSlide.bg.video.dottedOverlay = "2";
        }

		// CHANGES, TO SET LOOP AND PAUSE TIMER INDEPENDENT, HAVING 4 CASES
		if (newSlide.bg.video!==undefined && compare!==undefined) {
			newSlide.bg.video.loop = newSlide.bg.video.loop===true || (obj!==undefined && obj.bg!==undefined && obj.bg.video!==undefined && (obj.bg.video.loop==="loopandnoslidestop" || obj.bg.video.loop==="loop" || obj.bg.video.loop===true || obj.bg.video.loop==="true")) ? true : false;
			newSlide.bg.video.pausetimer =obj.pausetimer!==undefined && (obj.pausetimer===true || obj.pausetimer===false) ? obj.pausetimer : obj!==undefined && obj.bg!==undefined && obj.bg.video!==undefined && obj.bg.video.loop === "loop"  ? true : false;
			if (newSlide.bg.video.loop===true && newSlide.bg.video.nextSlideAtEnd===true) newSlide.bg.video.loop = false;
		}

		newSlide.thumb = _d(obj.thumb,{
			customThumbSrc:"",
			customThumbSrcId:"",
			customAdminThumbSrc:"",
			customAdminThumbSrcId:"",
			dimension:"orig"
			/*fromStream:true*/
		});
		newSlide.info = _d(obj.info,{
			params:[{v:"",l:10},{v:"",l:10},{v:"",l:10},{v:"",l:10},{v:"",l:10},{v:"",l:10},{v:"",l:10},{v:"",l:10},{v:"",l:10},{v:"",l:10}],
			description:""
		});
		newSlide.attributes = _d(obj.attributes,{
			title:"",
			titleOption:"media_library",
			class:"",
			data:"",
			id:"",
			attr:"",
			alt:"",
            altOption:"media_library",
            deeplink:""
        });
		newSlide.publish = _d(obj.publish,{
			from:"",
			to:"",
			state:"published"
		});

		newSlide.timeline = _d(obj.timeline,{
			stopOnPurpose:false,
			delay:"Default",
			transition:["fade"],
			slots:[0],
			duration:[1000],
			easeIn:["default"],
			easeOut:["default"],
			rotation:[0],
			loop:{
				set:false,
				repeat:"unlimited",
				start:2500,
				end:4500
			}
		});

		newSlide.timeline.loop = newSlide.timeline.loop === undefined ? {set:false, repeat:"unlimited", start:2500, end:4500} : newSlide.timeline.loop;

        //Need to Translate old values to New Values
        newSlide.slideChange = newSlide.timeline.transition!==undefined || obj.slideChange===undefined ? migrateSlideAnimations(newSlide.timeline,compare) : _d(obj.slideChange, RVS._R.getSlideAnim_EmptyObject());


        if (newSlide.timeline.transition) {
            delete newSlide.timeline.duration;
            delete newSlide.timeline.slots;
            delete newSlide.timeline.easeIn;
            delete newSlide.timeline.easeOut;
            delete newSlide.timeline.transition;
            delete newSlide.timeline.rotation;
        }


		newSlide.visibility = _d(obj.visibility,{
			hideAfterLoop:0,
			hideOnMobile:false,
			hideFromNavigation:false
		});

		newSlide.effects = _d(obj.effects,{
			parallax:"-",
			fade:"default",
			blur:"default",
			grayscale:"default"
		});
		newSlide.panzoom = _d(obj.panzoom,{
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
		});
		newSlide.seo = _d(obj.seo,{
			set:false,
			link:"",
			linkHelp:"auto",
			slideLink:"nothing",
			target:"_self",
			z:"front",
			type:"regular"

		});
		newSlide.nav = _d(obj.nav,{
						arrows:{presets:{}},
						thumbs:{presets:{}},
						tabs:{presets:{}},
						bullets:{presets:{}}
					});

		/* Store view visibility */

		newSlide.store_permissions = _d(obj.store_permissions, false);
		newSlide.allow_stores = {};
		if (obj.allow_stores) {
			for (var i in obj.allow_stores) if (obj.allow_stores.hasOwnProperty(i)) {
				newSlide.allow_stores[i] = obj.allow_stores[i];
			}
		}

		// backwards compatibility
		if (obj.store_id) {
			var storeIds = obj.store_id.split(',');
			newSlide.store_permissions = storeIds.indexOf('0') == -1;
			for (var i in storeIds) if (storeIds.hasOwnProperty(i) && storeIds[i] !== '0') {
				newSlide.allow_stores['store' + storeIds[i]] = true;
			}
		}

		return newSlide;
	};

	// SIMPLIFY SINGLE Slide OBJECT STRUCTURE
	RVS.F.simplifySlide = function(_) {
		if (_.type==="zone")
			return RVS.F.safeExtend(true,{},_);
		else
			return RVS.F.safeExtend(true,{}, RVS.F.simplifyObject(RVS.F.addSlideObj(undefined,true),RVS.F.safeExtend(true,{},_)));
	};
	// SIMPLIFY ALL Slide STRUCTURE
	RVS.F.simplifyAllSlide = function(_) {
		window.__Slides = {};
		for (var i in RVS.SLIDER.slideIDs) {
			if(!RVS.SLIDER.slideIDs.hasOwnProperty(i)) continue;
            if (!RVS.F.isNumeric(RVS.SLIDER.slideIDs[i]) && RVS.SLIDER.slideIDs[i].indexOf("static")>=0) {
            } else {
				window.__Slides[RVS.SLIDER.slideIDs[i]] = RVS.F.simplifySlide(RVS.SLIDER[RVS.SLIDER.slideIDs[i]].slide);
			}
		}
	};

	// BUILD THE FULL LAYER STRUCTURE OF SIMPLIFIED STRUCTURES
	RVS.F.expandSlide = function(slide) {
		return RVS.F.safeExtend(true,RVS.F.addSlideObj(), slide);
	};

    // Backend only solution for cross origin images (IE 11 does not support URL)
    RVS.F.getImgWithCORS = function(img, url) {
        if(URL && typeof URL === 'function'){
            if ((new URL(url, window.location.href)).origin !== window.location.origin) {
                img.crossOrigin = "anonymous";
            }
        } else {
            console.warn("URL object is not available");
        }
    }

	/***********************************
			INTERNAL FUNCTIONS
	************************************/

    function getSlideTransitionDefaults(_) {
        var b = _.preset!==undefined ? RVS.F.safeExtend(true,RVS._R.getSlideAnim_EmptyObject(),_.preset) :
                _.key!==undefined ? RVS._R.getAnimObjectByKey(_.key,RVS.LIB.SLTR)!==undefined ? RVS.F.safeExtend(true,{},RVS._R.getSlideAnim_EmptyObject(),RVS._R.getAnimObjectByKey(_.key,RVS.LIB.SLTR)) : RVS._R.getSlideAnim_EmptyObject():
                RVS._R.getSlideAnim_EmptyObject();

        if (_.slot!==undefined) b.in.col = b.in.row = _.slot;
        b.speed = Math.round(b.speed);

        b.preset = _.key!==undefined ? _.key : _.customkey!==undefined ? _.customkey : "custom";


        return b;
    }

    /* MIGATION OF OLD TRANSITIONS TO THE NEW TRANSITION TABLE */
    function migrateSlideAnimations(tl,compare) {
        var alltrans = tl.transition;


        if (compare) return RVS._R.getSlideAnim_EmptyObject();
        var a = ["duration","rotation","easeIn","easeOut","slots","transition"];

        if (tl.slots!==undefined) tl.slots = FCV(FOtoA(tl.slots));
        if (tl.slots!==undefined) tl.slots = FCV(FOtoA(tl.slots));

        if (tl.transition==="undefined" || tl.transition===undefined) tl.transition = ["fade"];
        //FALL BACK ON RANDOM ANIMATIONS
        if (tl.transition[0]==="random" || tl.transition[0]==="random-static" || tl.transition[0]==="random-premium" || tl.transition==="random" || tl.transition==="random-static" || tl.transition==="random-premium") {
            tl.transition = ["rndany"];
            tl.slots = [1];
            try{tl.duration = tl.duration===undefined || tl.duration[0]===undefined || tl.duration<500 || tl.duration[0]<500 ? 750 : tl.duration} catch(e) {}
        }

        for (var i in a) if (a.hasOwnProperty(i)) tl[a[i]] = FCV(FOtoA(tl[a[i]]));

        var nslot = false;
        for (i in RVS.LIB.SLTR.basic) if (RVS.LIB.SLTR.basic.hasOwnProperty(i)) for (var j in RVS.LIB.SLTR.basic[i]) if (nslot) continue; else nslot = j===tl.transition[0];


        var settings = getSlideTransitionDefaults({
            key:tl.transition[0]=="fade" || tl.transition[0]=="default" ? "fade" : tl.transition[0],
            slot:nslot ? 1 : tl.slots!==undefined && tl.slots[0]!==undefined && tl.slots[0]!=="default" ? tl.slots[0] : undefined
        });


        if (tl.transition[0]==="3dcurtain-vertical" || tl.transition[0]==="3dcurtain-horizontal" || tl.transition==="3dcurtain-vertical" || tl.transition==="3dcurtain-horizontal") {
            settings.speed = tl.duration[0]===undefined || tl.duration[0]==="default" || tl.duration[0]=="d" ? 500 : parseInt(parseInt(tl.duration[0])/3);
        } else
        if (tl.duration[0]!==undefined && tl.duration[0]!=="default") settings.speed = tl.duration[0];

        //FIX RANDOM SPEED
        if (settings.speed==="random") settings.speed = 1000;

        if (tl.easeIn[0]!==undefined && tl.easeIn[0]!=="default") settings.in.e = tl.easeIn[0];
        if (tl.easeOut[0]!==undefined && tl.easeOut[0]!=="default") settings.out.e = tl.easeOut[0];
        if (tl.rotation[0]!==undefined && tl.rotation[0]!=="default" && tl.rotation[0]!==0) settings.in.r = tl.rotation[0];
        if (tl.rotation[0]!==undefined && tl.rotation[0]!=="default" && tl.rotation[0]!==0) settings.out.r = tl.rotation[0];

        if (alltrans!==undefined && Array.isArray(alltrans) && alltrans.length>1) {
            settings.alt = [];
            for (var i=1;i<alltrans.length;i++)  settings.alt.push(alltrans[i])

        }


        return settings;

    }

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
			v=true;
		return v;
	}

})();
