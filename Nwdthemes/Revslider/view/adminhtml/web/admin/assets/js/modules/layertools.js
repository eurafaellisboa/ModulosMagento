/*!
 * REVOLUTION 6.0.0 EDITOR LAYERTOOLS JS
 * @version: 1.0 (01.07.2019)
 * @author ThemePunch
*/

(function() {

	RVS.RSCBA = { 	a:["width","height","maxWidth","maxHeight","minWidth","minHeight"],
					ai:["layer_width","layer_height","layer_max_width","layer_max_height","layer_min_width","layer_min_height"],
					t:["fontSize","lineHeight","letterSpacing"],
					ti:["layer_font_size_idle", "layer_line_height_idle", "layer_letter_spacing_idle"],
					f:["transform","mask","chars","words","lines"],
					sh:["hoffset","voffset","blur","spread"]
				};
	var respAttrs = ["horizontal","vertical","borderStyle","width","height","x","y","lineHeight","fontSize","color","textAlign","fontWeight","letterSpacing","blur","hoffset","voffset","spread", "frameX", "frameY", "charsX", "charsY", "wordsX", "wordsY", "linesX", "linesY","whiteSpace"],
		lockedLayers,
		visibleLayers;


	/*
	INITIALISE THE BASIC LISTENERS, INPUT MANAGEMENTS ETC
	*/
	RVS.F.initLayerTools = function() {
		initLocalInputBoxes();
		initMultipleLayerSelector();
		initDragAndDrop();
		initLocalListeners();
		prepareRescaler();

	};


	/*
	ADD THE LAYER IN THE RIGHT ORDER TO THE PARENT ELEMENT (ROWS IN ZONES, COLUMNS IN ROWS, ELEMENTS IN COLUMNS)
	*/
	function addLayerInOrder(_) {

		var insertBehindUID = "start";

		if (_.container!==undefined)
			_.container.find(_.type).each(function() {
				if (_.uid!==this.dataset.uid)
					insertBehindUID = RVS.L[this.dataset.uid].group.groupOrder<=RVS.L[_.uid].group.groupOrder && RVS.H[this.dataset.uid]!==undefined ? this.dataset.uid : insertBehindUID;

			});

		if (insertBehindUID==="start")
			_.container[0].prepend(_.layer);
		else
			RVS.F.insertAfter(_.layer,RVS.H[insertBehindUID].w[0]);


	}

	RVS.F.allSelectedHasHover = function() {
		var ja = true;
		for (var l in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(l)) continue;
			ja =  (RVS.L[RVS.selLayers[l]].hover.usehover===true || RVS.L[RVS.selLayers[l]].hover.usehover==='true' || RVS.L[RVS.selLayers[l]].hover.usehover==='desktop') ? ja : false;
		}
		return ja;
	};
	/*
	BUILD HTML LAYER
	*/
	RVS.F.buildHTMLLayer = function(_) {
		// CREATE LAYER IF IT IS NOT CREATED YET, OR IF LAYER NEED TO BE RECREACTED BY FORCE
		var l = RVS.L[_.uid],
			oldelement = RVS.H!==undefined && RVS.H[_.uid]!==undefined && RVS.H[_.uid].w!==undefined ? RVS.H[_.uid].w[0] : document.getElementById('_lc_'+RVS.S.slideId+'_'+_.uid+'_');
		RVS.H = RVS.H == undefined ? {} : RVS.H;

		if (_.force==true || RVS.H==undefined || RVS.H[_.uid]===undefined || oldelement===null) {
			if (_.force==true || RVS.H==undefined || RVS.H[_.uid]===undefined ) {
				if (oldelement!==null) oldelement.parentNode.removeChild(oldelement);

				var lc = RVS.F.cE({id:'_lc_'+RVS.S.slideId+'_'+_.uid+'_', cN:'_lc_ _lc_type_'+l.type,ds:{type:l.type,uid:_.uid,pid:l.group.puid,multiplemark:true}}),
					lockedbg = RVS.F.cE({cN:"_lc_locked_bg_"}),
					locked  = RVS.F.cE({cN: "_lc_locked_"}),
					locked_i = RVS.F.cI({c:"lock_outline"}),
					_tb_ = RVS.F.cE({t:"span",cN:"_tb_ _borders_"}),
					_bb_ = RVS.F.cE({t:"span",cN:"_bb_ _borders_"}),
					_rb_ = RVS.F.cE({t:"span",cN:"_rb_ _borders_"}),
					_lb_ = RVS.F.cE({t:"span",cN:"_lb_ _borders_"}),
					_loop = RVS.F.cE({cN:"_lc_loop_"}),
					_mask = RVS.F.cE({cN:"_lc_mask_"}),
					_iw = RVS.F.cE({cN:"_lc_iw_"}),
					_content = RVS.F.cE({cN:"_lc_content_"});

				if (l.type==="column" || l.type==="row") {
					var _topm_ = RVS.F.cE({t:"span",cN:"_c_margins _topm_"}),
						_botm_ = RVS.F.cE({t:"span",cN:"_c_margins _botm_"}),
						_lefm_ = RVS.F.cE({t:"span",cN:"_c_margins _lefm_"}),
						_rigm_ = RVS.F.cE({t:"span",cN:"_c_margins _rigm_"}),
						_topp_ = RVS.F.cE({t:"span",cN:"_c_paddings _topp_"}),
						_botp_ = RVS.F.cE({t:"span",cN:"_c_paddings _botp_"}),
						_lefp_ = RVS.F.cE({t:"span",cN:"_c_paddings _lefp_"}),
						_rigp_ = RVS.F.cE({t:"span",cN:"_c_paddings _rigp_"});
				}

				locked.appendChild(locked_i);
				_loop.appendChild(_mask);
				_mask.appendChild(_iw);
				_iw.appendChild(_content);

				lc.appendChild(lockedbg);
				lc.appendChild(locked);
				lc.appendChild(_tb_);
				lc.appendChild(_bb_);
				lc.appendChild(_rb_);
				lc.appendChild(_lb_);
				lc.appendChild(_loop);

				if (l.type==="column" || l.type==="row") {
					lc.appendChild(_topm_);
					lc.appendChild(_botm_);
					lc.appendChild(_lefm_);
					lc.appendChild(_rigm_);
					lc.appendChild(_lefp_);
					lc.appendChild(_rigp_);
					lc.appendChild(_botp_);
					lc.appendChild(_topp_);
				}

				if (l.group.puid===-1 || l.type==="row") {
					if (l.type=="row")
						addLayerInOrder({container: RVS.C.rZone[l.group.puid], layer:lc, uid:_.uid, type:"._lc_type_row"});
					else
						RVS.C.layergrid[0].appendChild(lc);
				} else
				if (l.type==="column")
					addLayerInOrder({container:RVS.H[l.group.puid].c, layer:lc, uid:_.uid, type:"._lc_type_column"});
				else
					addLayerInOrder({container:RVS.H[l.group.puid].c, layer:lc, uid:_.uid, type:"._lc_"});


				if (l.idle.style!==undefined && l.idle.style.length>0) lc.className += " "+l.idle.style;

				RVS.H[_.uid] = {w:jQuery(lc), last_puid : l.group.puid,  last_groupOrder:l.group.groupOrder};
				RVS.H[_.uid].m =_mask;
				RVS.H[_.uid].lp = _loop;
				RVS.H[_.uid].iw = _iw;
				RVS.H[_.uid].c = jQuery(_content);

				if (l.type==="column" || l.type==="row") {
					RVS.H[_.uid].margins =  {top: _topm_, bottom: _botm_,left: _lefm_,right: _rigm_};
					RVS.H[_.uid].paddings = {top: _topp_, bottom: _botp_,left: _lefp_,right: _rigp_};
				}
				RVS.H[_.uid].borders = {top: _tb_,bottom: _bb_,left: _lb_,right: _rb_};
			}

			var _img,_vidtitle;

			switch (l.type) {

				case "svg":
					RVS.H[_.uid].c[0].innerHTML = l.svg.renderedData;
					RVS.H[_.uid].svg = RVS.H[_.uid].w.find('svg');
					RVS.H[_.uid].svgPath = RVS.H[_.uid].w.find( !l.idle.svg.styleAll ? 'svg path' : 'svg path, svg circle, svg ellipse, svg line, svg polygon, svg polyline, svg rect');
				break;

				case "image":
					if (RVS.H[_.uid].w[0].querySelector('._lc_img_placeholder')===null) RVS.H[_.uid].c[0].appendChild(RVS.F.cE({cN:"_lc_img_placeholder",icon:{c:"filter_hdr"}}));
					if (RVS.H[_.uid].w[0].querySelector('._lc_image_inside_')===null) {
						_img = RVS.F.cE({t:'img',cN:"_lc_image_inside_",ds:{ow:l.size.originalWidth, oh:l.size.originalHeight}})
						_img.src = l.media.imageUrl;
						RVS.H[_.uid].c[0].appendChild(_img);
					}

					if (l.media.imageUrl === RVS.ENV.img_ph_url) RVS.H[_.uid].c[0].classList.add("placeholder_on");

				break;
				case "video":
					if (RVS.H[_.uid].w[0].querySelector('._lc_img_placeholder')===null) RVS.H[_.uid].c[0].appendChild(RVS.F.cE({cN:"_lc_img_placeholder",icon:{c:"videocam"}}));
					if (RVS.H[_.uid].w[0].querySelector('._lc_video_title_')===null) {
						_vidtitle = RVS.F.cE({cN:"_lc_video_title_"});
						_vidtitle.textContent = l.media.mediaType;
						RVS.H[_.uid].c[0].appendChild(_vidtitle);
						RVS.H[_.uid].vtitle =_vidtitle;

					} else
					if (RVS.H[_.uid].vtitle===undefined) RVS.H[_.uid].vtitle = RVS.H[_.uid].w[0].querySelector('._lc_video_title_');

					if (RVS.H[_.uid].w[0].querySelector('._lc_video_placeholder_')===null) RVS.H[_.uid].c[0].appendChild(RVS.F.cE({cN:"_lc_video_placeholder_"}));

					if (RVS.H[_.uid].w[0].querySelector('._lc_video_overlay')===null) {
						RVS.H[_.uid].volay = RVS.F.cE({cN:"_lc_video_overlay"});
						RVS.H[_.uid].c[0].appendChild(RVS.H[_.uid].volay);
					} else
					if (RVS.H[_.uid].volay===undefined) RVS.H[_.uid].volay = RVS.H[_.uid].w[0].querySelector('._lc_video_overlay')

					if (l.media.posterUrl === RVS.ENV.img_ph_url)  RVS.H[_.uid].c[0].classList.add("placeholder_on");
				break;
				case "audio":
					// PLACEHOLDER
					if (RVS.H[_.uid].w[0].querySelector('._lc_img_placeholder')===null) RVS.H[_.uid].c[0].append(RVS.F.cE({cN:"_lc_img_placeholder",icon:{c:"audiotrack"}}));
					if (RVS.H[_.uid].w[0].querySelector('audio')===null) RVS.H[_.uid].c.append('<audio controls></audio>');
					if (l.media.controls===false) RVS.H[_.uid].c[0].classList.add("placeholder_on"); else RVS.H[_.uid].c[0].classList.remove("audio_controls_on");

				break;
				case "group":
					// EXTRA BACKGROUND CONTANER
					if (RVS.H[_.uid].bg===undefined)
						if (RVS.H[_.uid].w[0].querySelector(':scope > ._lc_extra_bg')===null) {
							RVS.H[_.uid].bg = RVS.F.cE({cN:"_lc_extra_bg"});
							RVS.H[_.uid].w[0].appendChild(RVS.H[_.uid].bg);
						} else
						RVS.H[_.uid].bg = RVS.H[_.uid].w[0].querySelector(':scope > ._lc_extra_bg');

					// PLACEHOLDER
					if (RVS.H[_.uid].w[0].querySelector('._lc_group_placeholder')===null) RVS.H[_.uid].c[0].append(RVS.F.cE({cN:"_lc_group_placeholder",icon:{c:"format_shapes"}}))

					// LOCKER CONTAINER
					if (RVS.H[_.uid].w[0].querySelector('._group_lock_')===null) RVS.H[_.uid].c[0].append(RVS.F.cE({cN:"_group_lock_",icon:{c:"layers"}}));

					// HEAD CONTAINER
					if (RVS.H[_.uid].w[0].querySelector('._group_head_')===null)
						RVS.H[_.uid].w.append('<div class="_group_head_"><span id="_group_head_title_'+RVS.S.slideId+'_'+_.uid+'" class="_group_head_title_">'+l.alias+'</span><div data-uid="'+_.uid+'" class="_group_lock_toggle_"><i class="group_lock_icon material-icons">layers_clear</i><i class="group_lockopen_icon material-icons">layers</i></div></div>');
				break;

				case "row":
					if (RVS.H[_.uid].bg===undefined)
						if (RVS.H[_.uid].w[0].querySelector(':scope > ._lc_extra_bg')===null) {
							RVS.H[_.uid].bg = RVS.F.cE({cN:"_lc_extra_bg"});
							RVS.H[_.uid].w[0].appendChild(RVS.H[_.uid].bg);
						} else
						RVS.H[_.uid].bg = RVS.H[_.uid].w[0].querySelector(':scope > ._lc_extra_bg');
				break;
				case "column":
					if (RVS.H[_.uid].bg===undefined)
						if (RVS.H[_.uid].w[0].querySelector(':scope > ._lc_extra_bg_wrap')===null) {
							RVS.H[_.uid].bg = RVS.F.cE({cN:"_lc_extra_bg"});
							RVS.H[_.uid].bgmask = RVS.F.cE({cN:"_lc_extra_bg_wrap"});
							RVS.H[_.uid].bgmask.appendChild(RVS.H[_.uid].bg);
							RVS.H[_.uid].w[0].appendChild(RVS.H[_.uid].bgmask);
						} else {
							RVS.H[_.uid].bgmask = RVS.H[_.uid].w[0].querySelector(':scope > ._lc_extra_bg_wrap');
							RVS.H[_.uid].bg = RVS.H[_.uid].bgmask.querySelector(':scope > ._lc_extra_bg');
						}
				break;
				case "shape":
					if (RVS.H[_.uid].w[0].querySelector('._lc_stream_placeholder')===null) RVS.H[_.uid].c[0].appendChild(RVS.F.cE({cN:"_lc_stream_placeholder",icon:{c:"cast"}}));
					if (l.idle.bgFromStream) RVS.H[_.uid].c[0].classList.add("placeholder_on");
				break;
				default:
				break;
			}
			if (l.linebreak) RVS.H[_.uid].w[0].classList.add("rs-linebreak");
		}

		//First make it Draggable when the Mouse enters. No need to create extra costs on Load
		if (RVS.S.initDragAndResizeonHTMLLayers === undefined) {
			RVS.S.initDragAndResizeonHTMLLayers = true;
			RVS.DOC.on('mouseenter','._lc_',function() {
				if (this.dataset.dragandresize===undefined) {
					this.dataset.dragandresize = true;
					if (this.dataset.type!=="column" && this.dataset.type!=="row") RVS.F.doDraggable({uid:this.dataset.uid, drag:true, resize:true});
					if (jQuery.inArray(this.dataset.type,["text","button","svg","shape","group"])>=0)
						if (RVS.H[this.dataset.uid].sclr===undefined || RVS.H[this.dataset.uid].w[0].querySelector(':scope >._lc_reScaler')===null) {
							var sclr = RVS.F.cE({cN:"_lc_reScaler"}),
								sclrpin = RVS.F.cE({cN:"_lc_reScaler_pin"}),
								sclricon = RVS.F.cE({cN:"_lc_reScaler_icon"});
							sclr.appendChild(sclrpin);
							sclr.appendChild(sclricon);
							RVS.H[this.dataset.uid].w[0].appendChild(sclr);
							RVS.H[this.dataset.uid].sclr = jQuery(sclrpin);
							prepareRescaler(RVS.H[this.dataset.uid].sclr);
						}
				}
			})
		}

		//_.ignoreDrawLayers = false;

		/*
		LOAD GOOGLE FONT BEFORE 1ST TIME DRAW THE LAYER
		*/
		if (l.type==="text" || l.type=="button") {
			//RVS.F.checkUsedFonts(l);
			RVS.F.redrawTextLayerInnerHTML(_.uid);
			if (_.ignoreDrawLayers!==true) RVS.F.drawHTMLLayer({uid:_.uid});
		} else
		if (l.type==="svg") {
			if (l.svg.renderedData===undefined || (l.svg.source!==undefined && l.svg.source.length>4)) {
				jQuery.get(l.svg.source, function(data) {
					 	RVS.H[_.uid].c[0].innerHTML = l.svg.renderedData = new XMLSerializer().serializeToString(data.documentElement);
						RVS.H[_.uid].svg = RVS.H[_.uid].w.find('svg');
						RVS.H[_.uid].svgPath = RVS.H[_.uid].w.find( !l.idle.svg.styleAll ? 'svg path' : 'svg path, svg circle, svg ellipse, svg line, svg polygon, svg polyline, svg rect');
					  	RVS.F.drawHTMLLayer({uid:_.uid});
				});
			} else {
				if (_.ignoreDrawLayers!==true) RVS.F.drawHTMLLayer({uid:_.uid});
			}
		} else
		if (l.type==="image") {
			if (l.media.imageUrl!==undefined && l.media.imageUrl!==null && l.media.imageUrl.length>0 && l.media!==undefined && l.media.loaded!==true) {
				tpGS.gsap.set(RVS.H[_.uid].w,{visibility:"hidden"});
				RVS.F.preloadImage({
					uid:_.uid,
					slideId:RVS.S.slideId,
					image : l.media.imageUrl,
					silent:false,
					callback:function() {
						tpGS.gsap.set(RVS.H[_.uid].w,{visibility:"visible"});
						if (_.ignoreDrawLayers!==true) RVS.F.drawHTMLLayer({uid:_.uid});
					}
				});
			} else
				if (_.ignoreDrawLayers!==true) RVS.F.drawHTMLLayer({uid:_.uid})

		} else
				if (_.ignoreDrawLayers!==true) RVS.F.drawHTMLLayer({uid:_.uid})
	};


	/*
	REORDER HTML LAYER
	*/
	RVS.F.reOrderHTMLLayer = function(_) {

		// CREATE LAYER IF IT IS NOT CREATED YET, OR IF LAYER NEED TO BE RECREACTED BY FORCE
		if (RVS.H==undefined || RVS.H[_.uid]===undefined || RVS.H[_.uid].w===undefined) return;

		var l = RVS.L[_.uid],
			htmllayer = RVS.H[_.uid].w;

		var same = RVS.H[_.uid].last_puid===l.group.puid && RVS.H[_.uid].last_groupOrder===l.group.groupOrder;
		 RVS.H[_.uid].last_puid=l.group.puid;
		 RVS.H[_.uid].last_groupOrder=l.group.groupOrder;


		if (l.group.puid===-1 || l.type==="row") {
			if (l.type=="row")
				addLayerInOrder({container: RVS.C.rZone[l.group.puid], layer:htmllayer[0], uid:_.uid, type:"._lc_type_row"});
			else
				RVS.C.layergrid[0].appendChild(htmllayer[0]);
		} else
		if (l.type==="column")
			addLayerInOrder({container:RVS.H[l.group.puid].c, layer:htmllayer[0], uid:_.uid, type:"._lc_type_column"});
		else
			addLayerInOrder({container:RVS.H[l.group.puid].c, layer:htmllayer[0], uid:_.uid, type:"._lc_"});

		if (!same) RVS.S.redrawHTMLLayersList.push(_.uid);

	};

	RVS.F.checkRowsChildren = function() {
		for (var li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if (RVS.L[li].type=="row" || RVS.L[li].type=="group") {
				var children = RVS.F.getLayerChildren({layerid:li}),
					am=0;

				for (var chi in children) if (children.hasOwnProperty(chi)) {
					if (children[chi].type!=="column")
						am++;
				}
				if (am>0)
					RVS.H[li].w.removeClass("nocontent");
				else
					RVS.H[li].w.addClass("nocontent");

			}
		}
	};

	function getHighestGroupOrderInRow(_) {
		_.type = _.type===undefined ? "column" : _.type;
		var cols = RVS.F.getColumnsInRow({layerid:_.uid, type:_.type}),
			hgo = 0;
		for (var c in cols) {
			if(!cols.hasOwnProperty(c)) continue;
			hgo = parseInt(hgo,0)<=parseInt(RVS.L[cols[c]].group.groupOrder,0) ? parseInt(RVS.L[cols[c]].group.groupOrder,0)+1 : hgo;
		}
		return hgo;
	}

	/*
	ADD SINGLE LAYERS (CALL ONLY FROM ADDLAYER AND DUPLICATE LAYER FUNCTIONS)
	*/
	RVS.F.addLayerToLayers = function(obj) {
		var newLayer;
		if (obj.layerobject!==undefined) {
			newLayer = RVS.F.safeExtend(true,{},obj.layerobject);
			newLayer.uid = obj.copyPaste==="copy" ? window.copyPasteLayers.amount : RVS.F.getUniqueid();
			newLayer = RVS.F.addLayerObj(newLayer);
			newLayer.alias = obj.prefix!==undefined ? obj.prefix+" "+newLayer.alias.replace(/Copy/g,'') : "Copy "+newLayer.alias.replace(/Copy/g,'');
		} else {
			newLayer = RVS.F.addLayerObj(obj.type);
		}

		if (obj.extension) newLayer = RVS.F.safeExtend(true,newLayer,obj.extension);

		newLayer.group.puid = obj.puid!==undefined ? obj.puid : newLayer.group.puid;
		if (obj.newGroupOrder && obj.copyPaste!=="copy")
			newLayer.group.groupOrder = getHighestGroupOrderInRow({uid:newLayer.group.puid, type:newLayer.type});


		newLayer.alias = obj.alias!==undefined ? obj.alias+"-"+newLayer.uid : newLayer.alias;
		if (obj.copyPaste==="copy") {
			window.copyPasteLayersSlideId = RVS.S.slideId;
			window.copyPasteLayers.amount++;
			newLayer.copiedFromUid = obj.layerobject.uid;
			window.copyPasteLayers.layers[newLayer.uid] = newLayer;
		} else {
			if (obj.layerobject!==undefined) RVS.F.duplicateSkinColors({type:"layer", layerFrom:(obj.layerobject.copiedFromUid!==undefined ? obj.layerobject.copiedFromUid : obj.layerobject.uid), layerTo:newLayer.uid, slideFrom:window.copyPasteLayersSlideId,slideTo:RVS.S.slideId});
			delete newLayer.copiedFromUid;
			RVS.SLIDER[RVS.S.slideId].layers[newLayer.uid] = newLayer;


			RVS.F.backup({path:newLayer.uid,icon:"layers",txt:"Create New Layer",lastkey:"newlayer",force:true,slideid:RVS.S.slideId,val:RVS.F.safeExtend(true,{},RVS.SLIDER[RVS.S.slideId]).layers[newLayer.uid],old:{}, backupType:"layer", bckpGrType:"addlayer"});
		}
		if (obj.buildHTMLLayer && obj.copyPaste!=="copy") RVS.F.buildHTMLLayer({uid:newLayer.uid});
		return newLayer.uid;
	};

	// EXTEND ADD LAYER TYPE LISTS
	RVS.F.extendLayerTypes = function(obj) {
		RVS.S.extendedLayerTypes= RVS.S.extendedLayerTypes===undefined ? {} : RVS.S.extendedLayerTypes;
		if (RVS.S.extendedLayerTypes[obj.subtype]===undefined) {
			obj.extension.subtype = obj.subtype;
			RVS.S.extendedLayerTypes[obj.subtype] = {
				type:obj.type,
				subtype:obj.subtype,
				extension:obj.extension
			};

			var h;
			if (obj.subdrop) {
				h = '<div id="toolbar_add_layer_'+obj.alias+'" class="toolbar_dd_subdrop_wrap">';
				if(obj.objLibInMainMenu) {
					h += '<div class="add_layer" data-type="'+obj.type+'" data-subtype="object_library" data-libevent="'+obj.libevent+'" data-extensiongroup="'+obj.subtype+'" data-libfilters="'+obj.libfilters+'"><i class="material-icons">'+obj.icon+'</i>'+obj.alias+'</div>';
				} else {
					h +='<div class="add_layer" data-type="'+obj.type+'" data-subtype="'+obj.subtype+'"><i class="material-icons">'+obj.icon+'</i>'+obj.alias+'</div>';
				}
				h +='<div class="toolbar_dd_subdrop">';
				if (obj.objectlibrary) h += '<div class="add_layer" data-type="'+obj.type+'" data-subtype="object_library" data-libevent="'+obj.libevent+'" data-extensiongroup="'+obj.subtype+'" data-libfilters="'+obj.libfilters+'"><i class="material-icons">style</i>Object Library</div>';
				if (obj.emptyplaceholder) h+= '<div class="add_layer" data-type="'+obj.type+'" data-subtype="'+obj.subtype+'"><i class="material-icons">flip_to_back</i>Empty Placeholder</div>';
				if (obj.subList) for (var i in obj.subList)
						if (obj.subList.hasOwnProperty(i)) h+= '<div class="add_layer" data-evt="'+obj.subList[i].evt+'" data-type="'+obj.type+'" data-subtype="'+obj.subtype+'" data-subsubtype="'+i+'"><i class="material-icons">'+obj.subList[i].icon+'</i>'+obj.subList[i].name+'</div>';


				h +='</div>';
				h +='</div>';
			} else {
				h = '<div class="add_layer" data-type="'+obj.type+'" data-subtype="'+obj.subtype+'"><i class="material-icons">'+obj.icon+'</i>'+obj.alias+'</div>';
			}
			jQuery(h).insertAfter('#toolbar_add_layer_group')
			//jQuery('#add_layer_main_wrap').append(jQuery(h));
		}
	};




	/*
	ADD LAYER (OBJECT, LIST ELEMENT, FRAMES) ON DEMAND
	*/
	RVS.F.addLayer = function(obj) {
		RVS.DOC.trigger('changeToLayerMode');
		if (!obj.ignoreBackupGroup) RVS.F.openBackupGroup({id:"addLayer",txt:"Create New "+obj.type+" Layer",icon:"layers",lastkey:"layer"});
		var rowid,
			newLayerID,
			itemsInGroup,
			_SRC =  obj.copyPaste==="paste" ? window.copyPasteLayers.layers : RVS.L;
		switch (obj.type) {
			case "row":
				if (obj.duplicateId===undefined) {
					rowid = newLayerID = RVS.F.addLayerToLayers({type:"row",alias:"row",buildHTMLLayer:true});
					for (var i=0;i<3;i++) {
						RVS.F.addLayerToLayers({type:"column", puid:rowid, alias:"column",buildHTMLLayer:true});
					}
				} else {
					rowid = newLayerID = RVS.F.addLayerToLayers({layerobject:_SRC[obj.duplicateId],buildHTMLLayer:true,copyPaste:obj.copyPaste, prefix:obj.prefix, newGroupOrder:true});
					var columnsInRow = RVS.F.getColumnsInRow({layerid:obj.duplicateId,copyPaste:obj.copyPaste});
					for (var coi in columnsInRow) {
						if(!columnsInRow.hasOwnProperty(coi)) continue;
						var colid = RVS.F.addLayerToLayers({puid:rowid, layerobject:_SRC[columnsInRow[coi]],buildHTMLLayer:true,copyPaste:obj.copyPaste, prefix:obj.prefix}),
							itemsInColumn = RVS.F.getLayersFirstDepth({layerid:columnsInRow[coi],copyPaste:obj.copyPaste});

						for (var ii in itemsInColumn) {
							if(!itemsInColumn.hasOwnProperty(ii)) continue;
							RVS.F.addLayerToLayers({puid:colid, layerobject:_SRC[itemsInColumn[ii]],buildHTMLLayer:true,copyPaste:obj.copyPaste, prefix:obj.prefix});
						}
					}
				}
			break;
			case "column":
				if (obj.duplicateId===undefined)
					newLayerID = RVS.F.addLayerToLayers({type:"column", puid:obj.puid, alias:"column",buildHTMLLayer:true});
				else {
					var columnid;
					columnid = newLayerID = RVS.F.addLayerToLayers({layerobject:_SRC[obj.duplicateId],newGroupOrder:true,buildHTMLLayer:true,copyPaste:obj.copyPaste, prefix:obj.prefix});
					itemsInGroup = RVS.F.getLayersFirstDepth({layerid:obj.duplicateId,copyPaste:obj.copyPaste});
					for (var ii in itemsInGroup) {
						if(!itemsInGroup.hasOwnProperty(ii)) continue;
						RVS.F.addLayerToLayers({puid:columnid, layerobject:_SRC[itemsInGroup[ii]],buildHTMLLayer:true,copyPaste:obj.copyPaste, prefix:obj.prefix});
					}
				}
			break;
			case "group":

				if (obj.duplicateId===undefined)
					newLayerID = RVS.F.addLayerToLayers({type:"group",alias:"group",buildHTMLLayer:true});
				else {
					var groupid;
					groupid = newLayerID = RVS.F.addLayerToLayers({layerobject:_SRC[obj.duplicateId],buildHTMLLayer:true,copyPaste:obj.copyPaste, prefix:obj.prefix,newGroupOrder:true});
					itemsInGroup = RVS.F.getLayersFirstDepth({layerid:obj.duplicateId,copyPaste:obj.copyPaste});
					for (var ii in itemsInGroup) {
						if(!itemsInGroup.hasOwnProperty(ii)) continue;
						RVS.F.addLayerToLayers({puid:groupid, layerobject:_SRC[itemsInGroup[ii]],buildHTMLLayer:true,copyPaste:obj.copyPaste, prefix:obj.prefix});
					}
				}
			break;
			case "linebreak":
				newLayerID = RVS.F.addLayerToLayers({
					type:"shape",alias:"linebreak",buildHTMLLayer:true,extension:{
						size:{
								width:{d:{v:"100%"},n:{v:"100%"}, t:{v:"100%"}, m:{v:"100%"}},
								height:{d:{v:"10px"},n:{v:"10px"}, t:{v:"10px"}, m:{v:"10px"}}
							},
						idle:{	backgroundColor:'transparent',
								clear:{d:{v:"both"},n:{v:"both"}, t:{v:"both"}, m:{v:"both"}}
							},
						linebreak:true
					}
				});
			break;
			default:
				if (obj.subtype==="predefined") {
					newLayerID = RVS.F.addLayerToLayers({type:obj.type, alias:obj.type,buildHTMLLayer:true,extension:obj.extension});
				} else
				if (obj.duplicateId===undefined) {

					if ((RVS.S.extendedLayerTypes!==undefined && RVS.S.extendedLayerTypes[obj.subtype]!==undefined) ||
						(RVS.S.extendedLayerTypes!==undefined && obj.extensiongroup!==undefined && RVS.S.extendedLayerTypes[obj.extensiongroup]!==undefined)) {
							if (obj.extensiongroup!==undefined) {
								newLayerID = RVS.F.addLayerToLayers({type:obj.type, alias:obj.type, extension: RVS.S.extendedLayerTypes[obj.extensiongroup].extension,  buildHTMLLayer:true});
							}
							else
								newLayerID = RVS.F.addLayerToLayers({type:obj.type, alias:obj.type, extension: RVS.S.extendedLayerTypes[obj.subtype].extension,  buildHTMLLayer:true});
					} else
						newLayerID = RVS.F.addLayerToLayers({type:obj.type, alias:obj.type,buildHTMLLayer:true});
				}
				else
					newLayerID = RVS.F.addLayerToLayers({layerobject:_SRC[obj.duplicateId],buildHTMLLayer:true,copyPaste:obj.copyPaste, prefix:obj.prefix, newGroupOrder:_SRC[obj.duplicateId].group.puid!==-1});


				switch (obj.subtype) {
					case "wordpress_library":
						if (obj.type==="image")
							jQuery('#image_layer_media_library_button').trigger('click');
						else
						if (obj.type==="video") {

							jQuery('#video_layer_media_library_button').trigger('click');
						}

					break;
					case "object_library":
						if (obj.type==="object")
							RVS.F.openObjectLibrary({types:["fonticons","svgs","svgcustom"],filter:"all", selected:["fonticons"], success:{icon:"insertContentFromOL"}});
						else
						if (obj.type==="image")
							jQuery('#image_layer_object_library_button').trigger('click');
						else
						if (obj.type==="video")
							jQuery('#video_layer_object_library_button').trigger('click');
						else
						if (obj.libfilters!==undefined) {
							var filters = obj.libfilters.split(",");
							RVS.F.openObjectLibrary({types:filters,filter:"all", selected:[filters[0]], success:{custom:obj.libevent}, extension:obj.extensiongroup});
						}
					break;
					case "headline":
						obj.ignoreBackupGroup = true;
						RVS.F.openQuickStyle({bacupGroupOpen:"addLayer",list:["headlines","content"]});
					break;
					case "simple_content":
					obj.ignoreBackupGroup = true;
						RVS.F.openQuickStyle({bacupGroupOpen:"addLayer",list:["content","headlines"]});
					break;
					case "button":
						obj.ignoreBackupGroup = true;
						RVS.F.openQuickStyle({bacupGroupOpen:"addLayer", list:["button"]});
					break;
					case "predefined":

					break;
					default:
					break;
				}
			break;
		}


		if (!obj.ignoreBackupGroup) RVS.F.closeBackupGroup({id:"addLayer"});
		if (!obj.ignoreLayerList) RVS.F.buildLayerLists({force:true, ignoreRebuildHTML:true});
		if (!obj.ignoreOrderHTMLLayers) RVS.F.reOrderHTMLLayers();
		if (RVS.eMode!==undefined && RVS.eMode.top==="layer" && RVS.eMode.mode==="animation") RVS.F.showForms("*slidelayout**mode__slidecontent*#form_layer_style",true);
		RVS.DOC.trigger('layerAdded',{layerid:newLayerID, params:obj});
		return 	newLayerID;
	};



	/*
	DELETE LAYER STRUCTURES
	*/
	RVS.F.deleteLayerfromLayers = function(obj) {
		RVS.DOC.trigger('changeToLayerMode');
		if (RVS.L[obj.layerid]===undefined) return;
		var localBackupGroup = false;
		if (obj.groupisopen===undefined && !RVS.S.bckpGrp) {
			if (!RVS.S.bckpGrp) localBackupGroup = true;
			RVS.F.openBackupGroup({id:"removeLayer",txt:"Remove "+RVS.L[obj.layerid].type+" Layer",icon:"delete",lastkey:"layer"});
		}

		switch (RVS.L[obj.layerid].type) {
			case "row":
				var columnsInRow = RVS.F.getColumnsInRow({layerid:obj.layerid});
				for (var coi in columnsInRow) {
					if(!columnsInRow.hasOwnProperty(coi)) continue;
					var itemsInGroup = RVS.F.getLayersFirstDepth({layerid:columnsInRow[coi]});
					for (var ii in itemsInGroup) {
						if(!itemsInGroup.hasOwnProperty(ii)) continue;
						if (obj.newpuid!==undefined) {
							/*var old = RVS.F.getDeepVal({path:RVS.S.slideId+'.layers.'+itemsInGroup[ii]+'.group.puid'});*/
							RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+itemsInGroup[ii]+'.group.puid',val:obj.newpuid});
						} else {
							RVS.F.backup({path:itemsInGroup[ii],icon:"layers",txt:"Remove Layer",lastkey:"removelayer",slideid:RVS.S.slideId,force:true,val:{},old:RVS.F.safeExtend(true,{},RVS.SLIDER[RVS.S.slideId]).layers[itemsInGroup[ii]],backupType:"layer",bckpGrType:"removelayer"});
							delete RVS.SLIDER[RVS.S.slideId].layers[itemsInGroup[ii]];
							// DELETE RVS.H ROW
							jQuery('#_lc_'+RVS.S.slideId+'_'+itemsInGroup[ii]+'_').remove();
							delete RVS.H[itemsInGroup[ii]];

						}
					}
					RVS.F.backup({path:columnsInRow[coi],icon:"layers",txt:"Remove Layer",lastkey:"removelayer",slideid:RVS.S.slideId,force:true,val:{},old:RVS.F.safeExtend(true,{},RVS.SLIDER[RVS.S.slideId]).layers[columnsInRow[coi]],backupType:"layer",bckpGrType:"removelayer"});
					delete RVS.SLIDER[RVS.S.slideId].layers[columnsInRow[coi]];
					//DELETE RVS.H Columns in ROW
					jQuery('#_lc_'+RVS.S.slideId+'_'+columnsInRow[coi]+'_').remove();
					delete RVS.H[columnsInRow[coi]];

				}
			break;
			case "column":
			case "group":
				var itemsInGroup = RVS.F.getLayersFirstDepth({layerid:obj.layerid});
				for (var ii in itemsInGroup) {
					if(!itemsInGroup.hasOwnProperty(ii)) continue;
					if (obj.newpuid!==undefined) {
						RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+itemsInGroup[ii]+'.group.puid',val:obj.newpuid});
					} else {
						RVS.F.backup({path:itemsInGroup[ii],icon:"layers",txt:"Remove Layer",lastkey:"removelayer",force:true,slideid:RVS.S.slideId,val:{},old:RVS.F.safeExtend(true,{},RVS.SLIDER[RVS.S.slideId]).layers[itemsInGroup[ii]],backupType:"layer",bckpGrType:"removelayer"});
						delete RVS.SLIDER[RVS.S.slideId].layers[itemsInGroup[ii]];
						//DELETE RVS.H GROUP AND COLUMN
						jQuery('#_lc_'+RVS.S.slideId+'_'+itemsInGroup[ii]+'_').remove();
						delete RVS.H[itemsInGroup[ii]];
					}
				}
			break;
		}
		RVS.F.backup({path:obj.layerid,icon:"layers",txt:"Remove Layer",lastkey:"removelayer",slideid:RVS.S.slideId,force:true,val:{},old:RVS.F.safeExtend(true,{},RVS.SLIDER[RVS.S.slideId]).layers[obj.layerid],backupType:"layer",bckpGrType:"removelayer"});
		delete RVS.SLIDER[RVS.S.slideId].layers[obj.layerid];
		delete RVS.H[obj.layerid];
		jQuery('#_lc_'+RVS.S.slideId+'_'+obj.layerid+'_').remove();
		if (localBackupGroup) {
			RVS.F.closeBackupGroup({id:"removeLayer"});
			RVS.F.buildLayerLists({force:true});
		}

	};

	/*
	UPDATE SELECTED HTML LAYERS
	*/
	RVS.F.updateSelectedHtmlLayers = function(ignoreSelected,ignoreClasses) {
		requestAnimationFrame(function() {
			for (var i in RVS.L) {
				if(!RVS.L.hasOwnProperty(i)) continue;
				if (i!=="top" && i!=="bottom" && i!=="middle") {
					if (!ignoreSelected && RVS.F.inSelectedLayers({id:i}) && RVS.H[i]!==undefined) {
						RVS.H[i].w[0].classList.add("selected");
						//RVS.H[i].w[0].classList.add("marked");
						RVS.H[i].selected = true;
						RVS.F.setZindex({id:i,o:475});
					} else {
						if (RVS.H[i]!==undefined) {
							if (ignoreClasses!==true) {
								RVS.H[i].w[0].classList.remove("selected");
								RVS.H[i].selected = false;
							}
							RVS.F.setZindex({id:i});
						}

					}
				}
			}
		});
	};

	/*
	UPDATE STATIC LAYER START / END LISTS
	*/
	RVS.F.updateStaticStartEndList = function() {
		window.staticLayerStartIndex = window.staticLayerStartIndex===undefined ? jQuery('#staticlayer_Startindex') : window.staticLayerStartIndex;
		window.staticLayerEndIndex = window.staticLayerEndIndex===undefined ? jQuery('#staticlayer_Endindex') : window.staticLayerEndIndex;
		RVS.F.removeAllOptionsS2({select:window.staticLayerStartIndex});
		RVS.F.removeAllOptionsS2({select:window.staticLayerEndIndex});

		for (var i=1;i<RVS.SLIDER.slideIDs.length-1;i++) {
			RVS.F.addOptionS2({select:window.staticLayerStartIndex, val:i.toString(), txt:i});
			RVS.F.addOptionS2({select:window.staticLayerEndIndex, val:(i+1).toString(), txt:(i+1)});
		}
		RVS.F.addOptionS2({select:window.staticLayerEndIndex, val:"last", txt:RVS_LANG.lastslide});
	};

	/*
	SWAP ZINDEX BASED ON IF IT IS IN EDITED MODE OR NOT
	*/
	RVS.F.setZindex = function(_) {
		_.o = _.o == undefined ? 0 : _.o;
		tpGS.gsap.set(RVS.H[_.id].w,{zIndex:parseInt(RVS.L[_.id].position.zIndex,0) + parseInt(_.o,0)});
	};

	/*
	UPDATE ALL LAYERS QUICK
	*/
	RVS.F.allLayersReDraw = function() {

		for (var li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			RVS.F.drawHTMLLayer({uid:li});
		}




	}
	/*
	SELECTED LAYERS HANDLING
	*/
	RVS.F.selectedLayersVisualUpdate = function() {
		jQuery('.layerselector').removeClass("checked");
		jQuery('.tllayerlist_element').removeClass("checked");

		for (var t in RVS.S.selLayerTypes) {
			if(!RVS.S.selLayerTypes.hasOwnProperty(t)) continue;
			RVS.S.selLayerTypes[t] = false;
		}
		var inColumn = false,
			ncl = "";
		for (var li in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(li)) continue;
			jQuery('#tllayerlist_element_'+RVS.S.slideId+'_'+RVS.selLayers[li]).addClass("checked");
			jQuery('#tllayerlist_element_selector_'+RVS.S.slideId+'_'+RVS.selLayers[li]).addClass("checked");
			if (!inColumn && RVS.L[RVS.selLayers[li]].group.puid!=-1 && RVS.L[RVS.L[RVS.selLayers[li]].group.puid].type==="column") inColumn = true;
			RVS.S.selLayerTypes[RVS.L[RVS.selLayers[li]].type] = true;
		}


		for (var t in RVS.S.selLayerTypes) {
			if(!RVS.S.selLayerTypes.hasOwnProperty(t)) continue;
			if (RVS.S.selLayerTypes[t]) ncl=ncl+" _"+t+"_sel_";
		}
		ncl = inColumn===true ? ncl+" _layer_in_column_sel_" : ncl;
		ncl = ncl==="" ? "no_layers_selected" : ncl;
		ncl = RVS.selLayers.length>1 ? ncl + " multiple_layers_selected" : ncl;

		RVS.C.the_cont[0].className = ncl;
	};

	/*
	RESIZE LAYER OR GROUP WITH CHILDREN LINEAR
	 */
	function prepareRescaler(pin) {
		if (pin===undefined) return;
		pin.resizable({
			handles:"se",
			start:function(e,ui) {
				switch (RVS.L[RVS.selLayers[0]].type) {
					case "text":
					case "button":
						RVS.F.showForms("*slidelayout**mode__slidecontent*#form_layer_style",true);
					break;
					default:
						RVS.F.showForms("*slidelayout**mode__slidecontent*#form_layer_position",true);
					break;
				}

				RVS.C.layergrid.addClass("layersInDragorResize");
				RVS.F.doResizeLayers({mode:"init"});
				window.lastReScalerWidth = RVS.H[RVS.selLayers[0]].w.width();

			},
			resize:function(e,ui) {
				var s = Math.round((ui.size.width/ui.originalSize.width)*100) / 100;
				if (s!==window.lastReScalerSize) RVS.F.doResizeLayers({mode:"update",size:s});
				window.lastReScalerSize = s;
			},
			aspectRatio: true,
			stop:function(e,ui) {
				var s = Math.round((ui.size.width/ui.originalSize.width)*100) / 100;
				RVS.F.doResizeLayers({mode:"set",size:s});
				ui.helper[0].style.width="100%";
				ui.helper[0].style.height="100%";
				RVS.C.layergrid.removeClass("layersInDragorResize");
				RVS.S.justresized = true;
				setTimeout(function() {
					RVS.S.justresized = false;
				},100)

			}
		})
	}
	/*
	MULTIPLICATE VALUES IF POSSIBLE
	 */
	function reLaMu(how,a,b,c,d) {
		var r;
		if (a===undefined || a=="auto" || a=="none" || !RVS.F.isNumeric(parseInt(a,0)) || (!RVS.F.isNumeric(a) && a.indexOf("%")>0))
			r=a;
		else {
			r=Math[how]((parseInt(a,0) * b));
			r = c===undefined ? r : r+c;
		}
		return r;
	}
	/*
	CACHE THE ATTRIBUTES BEFORE LAYER RESIZER STARTS
	 */
	function cacheBeforeScale(l) {
		var r = {
			x: l.position.x[RVS.screen].v,
			y: l.position.y[RVS.screen].v,
			padding : RVS.F.safeExtend(true,{},l.idle.padding[RVS.screen].v),
			borderRadius : RVS.F.safeExtend(true,{},l.idle.borderRadius.v),
			width : l.size.width[RVS.screen].v,
			height : l.size.height[RVS.screen].v,
			minHeight : l.size.minHeight[RVS.screen].v,
			maxHeight : l.size.maxHeight[RVS.screen].v,
			minWidth : l.size.minWidth[RVS.screen].v,
			maxWidth : l.size.maxWidth[RVS.screen].v,
			wrap : l.idle.whiteSpace[RVS.screen].v,
			boxShadow : {
				hoffset : l.idle.boxShadow.hoffset[RVS.screen].v,
				voffset : l.idle.boxShadow.voffset[RVS.screen].v,
				blur : l.idle.boxShadow.blur[RVS.screen].v,
				spread : l.idle.boxShadow.spread[RVS.screen].v
			}
		};

		if (jQuery.inArray(l.type,["text","button"])>=0) {
			r.fontSize = l.idle.fontSize[RVS.screen].v;
			r.letterSpacing = l.idle.letterSpacing[RVS.screen].v;
			r.lineHeight = l.idle.lineHeight[RVS.screen].v;
		}
		r.frames = {};
		for (var i in l.timeline.frames) if (l.timeline.frames.hasOwnProperty(i))
			r.frames[i] = {
				transform : { x : l.timeline.frames[i].transform.x[RVS.screen].v, y : l.timeline.frames[i].transform.y[RVS.screen].v},
				mask : { x : l.timeline.frames[i].mask.x[RVS.screen].v,  y : l.timeline.frames[i].mask.y[RVS.screen].v},
				chars :{ x : l.timeline.frames[i].chars.x[RVS.screen].v, y : l.timeline.frames[i].chars.y[RVS.screen].v},
				words :{ x : l.timeline.frames[i].words.x[RVS.screen].v, y : l.timeline.frames[i].words.y[RVS.screen].v},
				lines :{ x : l.timeline.frames[i].lines.x[RVS.screen].v, y : l.timeline.frames[i].lines.y[RVS.screen].v}
			}
		return r;
	}

	/*
	MULTIPLICATE ATTRIBUTES ON LAYERS
	 */
	function resizeLayer(obj) {
		var l = RVS.L[obj.layerid],
			_ = RSCBS[obj.layerid],
			p = RVS.S.slideId+'.layers.'+obj.layerid;

		if (jQuery.inArray(l.type,["text","button"])>=0) for (var a in RVS.RSCBA.t) if (RVS.RSCBA.t.hasOwnProperty(a)) RVS.F.updateSliderObj({path:p+'.idle.'+RVS.RSCBA.t[a]+'.'+RVS.screen+".v",val:reLaMu("round",_[RVS.RSCBA.t[a]],obj.size),ignoreResponsive:obj.ignoreResponsive, ignoreBackup:obj.ignoreBackup});
		for (var a in RVS.RSCBA.a) if (RVS.RSCBA.a.hasOwnProperty(a)) RVS.F.updateSliderObj({path:p+'.size.'+RVS.RSCBA.a[a]+'.'+RVS.screen+".v",val:reLaMu("round",_[RVS.RSCBA.a[a]],obj.size),ignoreResponsive:obj.ignoreResponsive, ignoreBackup:obj.ignoreBackup});
		for (var i=0;i<4;i++) RVS.F.updateSliderObj({path:p+'.idle.padding.'+RVS.screen+".v."+i,val:reLaMu("round",_.padding[i],obj.size),ignoreResponsive:obj.ignoreResponsive, ignoreBackup:obj.ignoreBackup});
		if (l.idle.boxShadow.inuse===true) for (var a in RVS.RSCBA.sh) if (RVS.RSCBA.sh.hasOwnProperty(a)) RVS.F.updateSliderObj({path:p+'.idle.boxShadow.'+RVS.RSCBA.sh[a]+'.'+RVS.screen+".v",val:reLaMu("round",_.boxShadow[RVS.RSCBA.sh[a]],obj.size,"px"),ignoreResponsive:obj.ignoreResponsive, ignoreBackup:obj.ignoreBackup});

		//for (var i=0;i<4;i++) RVS.F.updateSliderObj({path:p+'.idle.borderRadius.v.'+i,val:reLaMu("round",_.borderRadius[i],obj.size,"px"),ignoreResponsive:obj.ignoreResponsive, ignoreBackup:obj.ignoreBackup});

		for (i in RVS.L[obj.layerid].timeline.frames) if (RVS.L[obj.layerid].timeline.frames.hasOwnProperty(i))
			for (var a in RVS.RSCBA.f) if (RVS.RSCBA.f.hasOwnProperty(a)) {
				RVS.F.updateSliderObj({path:p+'.timeline.frames.'+i+'.'+RVS.RSCBA.f[a]+'.x.'+RVS.screen+".v",val: reLaMu("round",_.frames[i][RVS.RSCBA.f[a]].x,obj.size,"px"),ignoreResponsive:obj.ignoreResponsive, ignoreBackup:obj.ignoreBackup});
				RVS.F.updateSliderObj({path:p+'.timeline.frames.'+i+'.'+RVS.RSCBA.f[a]+'.y.'+RVS.screen+".v",val: reLaMu("round",_.frames[i][RVS.RSCBA.f[a]].y,obj.size,"px"),ignoreResponsive:obj.ignoreResponsive, ignoreBackup:obj.ignoreBackup});
			}
		if (obj.ingroup) {
			RVS.F.updateSliderObj({path:p+'.position.x.'+RVS.screen+".v",val: reLaMu("round",_.x,obj.size,"px"),ignoreResponsive:obj.ignoreResponsive, ignoreBackup:obj.ignoreBackup});
			RVS.F.updateSliderObj({path:p+'.position.y.'+RVS.screen+".v",val: reLaMu("round",_.y,obj.size,"px"),ignoreResponsive:obj.ignoreResponsive, ignoreBackup:obj.ignoreBackup});
		}
		RVS.F.drawHTMLLayer({uid:obj.layerid});
		RVS.F.updateEasyInputs({container:jQuery('#form_layer_style'), path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
		RVS.F.updateEasyInputs({container:jQuery('#form_layer_advstyle'), path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
		RVS.F.updateEasyInputs({container:jQuery('#form_layer_position'), path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
	}


	/*
	MULTIPLICATE ATTRIBUTES ON LAYERS
	 */
	function resizeLayerSimple(obj) {
		var l = RVS.L[obj.layerid],
			_ = RSCBS[obj.layerid];

		RVS.cC = RVS.cC===undefined ? {} : RVS.cC;

		if (jQuery.inArray(l.type,["text","button"])>=0) {
			for (var a in RVS.RSCBA.t) if (RVS.RSCBA.t.hasOwnProperty(a)) {
				l.idle[RVS.RSCBA.t[a]][RVS.screen].v = reLaMu("round",_[RVS.RSCBA.t[a]],obj.size);
				RVS.cC["js_"+RVS.RSCBA.t[a]] = RVS.cC["js_"+RVS.RSCBA.t[a]]===undefined ? document.getElementById(RVS.RSCBA.ti[a]) : RVS.cC["js_"+RVS.RSCBA.t[a]];
				RVS.cC["js_"+RVS.RSCBA.t[a]].value = l.idle[RVS.RSCBA.t[a]][RVS.screen].v+"px";
			}
		}

		for (var a in RVS.RSCBA.a) if (RVS.RSCBA.a.hasOwnProperty(a)) {
			l.size[RVS.RSCBA.a[a]][RVS.screen].v = reLaMu((RVS.RSCBA.a[a]==="width" ? "ceil" : "round"),_[RVS.RSCBA.a[a]],obj.size);
			RVS.cC["js_"+RVS.RSCBA.a[a]] = RVS.cC["js_"+RVS.RSCBA.a[a]]===undefined ? document.getElementById(RVS.RSCBA.ai[a]) : RVS.cC["js_"+RVS.RSCBA.a[a]];
			RVS.cC["js_"+RVS.RSCBA.a[a]].value = l.size[RVS.RSCBA.a[a]][RVS.screen].v + (RVS.F.isNumeric(l.size[RVS.RSCBA.a[a]][RVS.screen].v) ? "px" : "");
		}

		if (l.idle.boxShadow.inuse===true) for (var a in RVS.RSCBA.sh) if (RVS.RSCBA.sh.hasOwnProperty(a)) l.idle.boxShadow[RVS.RSCBA.sh[a]][RVS.screen].v = reLaMu("round",_.boxShadow[RVS.RSCBA.sh[a]],obj.size,"px");

		for (var i=0;i<4;i++) l.idle.padding[RVS.screen].v[i] = reLaMu("round",_.padding[i],obj.size);
		//for (var i=0;i<4;i++) l.idle.borderRadius.v[i] = reLaMu("round",_.borderRadius[i],obj.size,"px");
		if (obj.ingroup) {
			l.position.x[RVS.screen].v = reLaMu("round",_.x,obj.size,"px");
			l.position.y[RVS.screen].v = reLaMu("round",_.y,obj.size,"px");
		}

		RVS.F.drawHTMLLayer({uid:obj.layerid,ignoreLayerAnimation:true});
		//RVS.F.updateEasyInputs({container:jQuery('#form_layer_style'), path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
	}

	/*
	DO RESIZE LAYERS BASED ON SCALER
	 */
	RVS.F.doResizeLayers = function(_) {
		if (RVS.selLayers.length>1 || RVS.selLayers.length===0) return;
		switch (_.mode) {
			case "init":
				window.RSCBS = {};
				window.RSCBS[RVS.selLayers[0]] = cacheBeforeScale(RVS.L[RVS.selLayers[0]]);
				if (RVS.L[RVS.selLayers[0]].type!=="group" && window.RSCBS[RVS.selLayers[0]].width==="auto" && RVS.L[RVS.selLayers[0]].group.puid===-1 && (RVS.L[RVS.selLayers[0]].type==="text" || RVS.L[RVS.selLayers[0]].type==="button")) {
					if (window.RSCBS[RVS.selLayers[0]].wrap==="full" || window.RSCBS[RVS.selLayers[0]].wrap==="normal") RVS.L[RVS.selLayers[0]].idle.whiteSpace[RVS.screen].v = "content";
					window.RSCBS[RVS.selLayers[0]].CacheWidth = window.RSCBS[RVS.selLayers[0]].width;
					window.RSCBS[RVS.selLayers[0]].width = window.RSCBS[RVS.selLayers[0]].width==="auto" ? RVS.H[RVS.selLayers[0]].w.width() : window.RSCBS[RVS.selLayers[0]].width;
				}
				if (RVS.L[RVS.selLayers[0]].type==="group") {
					window.RSCBS.layers = RVS.F.getLayersFirstDepth({layerid:RVS.selLayers[0]});
					for (var i in window.RSCBS.layers) if (window.RSCBS.layers.hasOwnProperty(i)) window.RSCBS[window.RSCBS.layers[i]] = cacheBeforeScale(RVS.L[window.RSCBS.layers[i]]);
				}
			break;
			case "update":
				resizeLayerSimple({layerid:RVS.selLayers[0],size:_.size});
				if (RVS.L[RVS.selLayers[0]].type==="group") for (var i in window.RSCBS.layers) if (window.RSCBS.layers.hasOwnProperty(i)) resizeLayerSimple({layerid:window.RSCBS.layers[i],size:_.size,ingroup:true})
			break;
			case "set":
				var backtoorig = false;
				if (RVS.L[RVS.selLayers[0]].type!=="group"  && RVS.L[RVS.selLayers[0]].group.puid===-1 &&  (RVS.L[RVS.selLayers[0]].type==="text" || RVS.L[RVS.selLayers[0]].type==="button") &&
					((RVS.S.layer_grid_offset.left + RVS.S.lgw) > (RVS.H[RVS.selLayers[0]].w.offset().left + RVS.H[RVS.selLayers[0]].w.width()))) {
					backtoorig= true;
					window.RSCBS[RVS.selLayers[0]].width = window.RSCBS[RVS.selLayers[0]].CacheWidth==="auto" ? "auto" : window.RSCBS[RVS.selLayers[0]].width;
				}
				resizeLayerSimple({layerid:RVS.selLayers[0],size:1});
				if (RVS.L[RVS.selLayers[0]].type==="group") for (var i in window.RSCBS.layers) if (window.RSCBS.layers.hasOwnProperty(i)) resizeLayerSimple({layerid:window.RSCBS.layers[i],size:1})
				RVS.F.openBackupGroup({id:"layerScaling",txt:"Layer Scaling",icon:"layers",lastkey:"layer"});
				resizeLayer({layerid:RVS.selLayers[0],size:_.size,ignore:false,redraw:true, updatefields:true, ignoreBackup:false});
				if (backtoorig===true) RVS.L[RVS.selLayers[0]].idle.whiteSpace[RVS.screen].v = window.RSCBS[RVS.selLayers[0]].wrap;
				else if (RVS.L[RVS.selLayers[0]].type!=="group"  && RVS.L[RVS.selLayers[0]].group.puid===-1 &&  (RVS.L[RVS.selLayers[0]].type==="text" || RVS.L[RVS.selLayers[0]].type==="button") && window.RSCBS[RVS.selLayers[0]].width!==window.RSCBS[RVS.selLayers[0]].CacheWidth && window.RSCBS[RVS.selLayers[0]].CacheWidth=="auto")
					RVS.F.showInfo({content:RVS_LANG.layerbleedsout, type:"info", showdelay:0, hidedelay:8, hideon:"", event:"" });

				if (RVS.L[RVS.selLayers[0]].type==="group") for (var i in window.RSCBS.layers) if (window.RSCBS.layers.hasOwnProperty(i)) resizeLayer({layerid:window.RSCBS.layers[i],size:_.size,ingroup:true,ignore:false,redraw:true, updatefields:true, ignoreBackup:false})
				RVS.F.closeBackupGroup({id:"layerScaling"});
			break;
		}
	}

	/*
	ADD SELECTED LAYERS TO THE SELECTED ARRAY
	*/
	RVS.F.selectLayers = function(_) {
		if (RVS.S.justresized) return;

		_ = _ ===undefined ? {} : _;

		if (RVS.eMode.mode!=="animation")
			RVS.S.keyFrame = "idle";
		else
		if (_.keepSelectedFrame!==true || RVS.S.keyFrame===undefined) {
			_.selectedKeyFrame =_.selectedKeyFrame===undefined ? "idle" : _.selectedKeyFrame;
			RVS.S.keyFrame = _.selectedKeyFrame;
		}

		if (_!==undefined && _.action==="add" && _.id!==undefined && RVS.L[_.id].visibility && (RVS.L[_.id].visibility.locked || !RVS.L[_.id].visibility.visible))
			_.action = "remove";

		if (!_.ignoreModeChange && (RVS.eMode.top!=="layer" || RVS.eMode.menu!=="#form_layer_animation")) {
			RVS.F.mainMode({set:false, ignoreReDraw:true, mode:"slidelayout"});
			RVS.F.showHideLayerEditor({mode:"slidecontent",openSettings:false});
		}

		if (_.overwrite)
			RVS.selLayers = [];
		else

		if (RVS.selLayers.length>0 && !_.quickmode && RVS.eMode.top==="layer" && RVS.eMode.menu==="#form_layer_animation")
			RVS.F.showInfo({content:RVS_LANG.noMultipleSelectionOfLayers, type:"info", showdelay:0.2, hidedelay:2, hideon:"", event:"" });


		_.id = _.id==="top" || _.id==="bottom" || _.id==="middle" ? "ignore" : _.id===undefined ? _.id : parseInt(_.id,0);

		if (_.id!==undefined && _.id!=="ignore")
			if (_.action === 'add' && jQuery.inArray(_.id,RVS.selLayers)==-1) RVS.selLayers.push(_.id);
			else
			if (_.action === 'remove' && jQuery.inArray(_.id,RVS.selLayers)!==-1) RVS.selLayers.splice(jQuery.inArray(_.id,RVS.selLayers),1);



		if (_.ignoreUpdate!==true && !_.quickmode) {
			if (_.ignoreUpdate!=="onlyhtml") RVS.F.selectedLayersVisualUpdate();
			RVS.F.updateSelectedHtmlLayers();
			if (RVS.eMode.mode!=="animation") RVS.F.updateAllLayerToIDLE();
			RVS.F.updateSelectedLayersIdleHover();
		}

		if (_.ignoreFieldUpdates!==true && !_.quickmode) {
			RVS.F.updateLayerInputFields({short:true});
			RVS.C.slit.textContent = RVS.selLayers.length===1 ? RVS.F.getLayerIcon(RVS.L[RVS.selLayers[0]].type,RVS.L[RVS.selLayers[0]].subtype) : "layers";
			RVS.DOC.trigger('selectLayersDone');
			RVS.F.checkForAudioLayer();
		} else
		if (_.quickmode) RVS.F.updateSelectedHtmlLayers();
		else
 		if (window.qstyle_library_open) RVS.F.updateAvailableLayerTypes();


		//Go to SelectedKeyFrame
		if (_.selectedKeyFrame!==undefined) {
			RVS.TL.cache.main = (RVS.F.getTimeAtSelectedFrameEnd()-2) / 100;

			if (RVS.TL.cache.main<=0)
				RVS.F.updateCurTime({pos:true, cont:true, force:true, left:0,refreshMainTimeLine:true, caller:"selectLayers"});
			else
				RVS.F.updateTimeLine({force:true, state:"time",time:RVS.TL.cache.main, timeline:"main", forceFullLayerRender:true, updateCurTime:true});

		} else if (!_.quickmode && RVS.selLayers.length===0) jQuery('framewrap.selected').removeClass("selected");
		if (!_.quickmode && RVS.S.shwLayerAnim) RVS.DOC.trigger("previewLayerAnimation");

	};

	RVS.F.convertTimeToSec = function(a,def) {
		a = a==="" ? parseFloat(def) : a;
		var cansplit = !RVS.F.isNumeric(a) && a.indexOf(':')>=0,
			b = 0;
		a = cansplit ? a.split(":") : a;
		if (cansplit && a.length>1)
			b = ((60*parseFloat(a[0])) +parseFloat(a[1]));
		else
		if (cansplit)
			b =  parseFloat(a[0]);
		else
			b = a;
		return  b===null || b===undefined  ? 0 : b;
	};

	RVS.F.updateAudioTimes = function(duration) {

		if (RVS.selLayers.length!=1) return;
		if (RVS.L[RVS.selLayers[0]].type!=="audio") return;
		RVS.L[RVS.selLayers[0]].media.startAt = RVS.L[RVS.selLayers[0]].media.startAt==="" ? 0 : RVS.L[RVS.selLayers[0]].media.startAt;
		RVS.L[RVS.selLayers[0]].media.endAt = RVS.L[RVS.selLayers[0]].media.endAt==="" ? duration : RVS.L[RVS.selLayers[0]].media.endAt;
		RVS.L[RVS.selLayers[0]].media.endAt = RVS.L[RVS.selLayers[0]].media.endAt>duration ? duration : RVS.L[RVS.selLayers[0]].media.endAt;

		document.getElementById('layer_video_start').value = RVS.L[RVS.selLayers[0]].media.startAt;
		document.getElementById('layer_video_end').value = RVS.L[RVS.selLayers[0]].media.endAt;

		var st = RVS.F.convertTimeToSec(RVS.L[RVS.selLayers[0]].media.startAt,0),
			en = RVS.F.convertTimeToSec(RVS.L[RVS.selLayers[0]].media.endAt,window.audiomaster.getDuration());
		en = en===0 || en<=st ?  window.audiomaster.getDuration() : en;

		window.audiomaster.regions.destroy();

		window.audioregion = window.audiomaster.regions.add({start:st,end:en,color:'rgba(0,109,210,0.2)'});
		window.audioregion.on('update',function() {
			RVS.L[RVS.selLayers[0]].media.startAt = window.audioregion.start;
			RVS.L[RVS.selLayers[0]].media.endAt = window.audioregion.end;
			document.getElementById('layer_video_start').value = RVS.L[RVS.selLayers[0]].media.startAt;
			document.getElementById('layer_video_end').value = RVS.L[RVS.selLayers[0]].media.endAt;
		});

		window.audioregion.on('dblclick',function() {
			window.audioregion.play();
		});

	};

	// CHECK IF AUDIO LAYER ADDED, AND LOAD ITS WAVE IF NEEDED
	RVS.F.checkForAudioLayer = function() {

		// RETURN IF NOTHING TO LOAD
		if (RVS.selLayers.length!=1) return;
		if (RVS.L[RVS.selLayers[0]].type!=="audio") return;
		if (RVS.L[RVS.selLayers[0]].media.audioUrl==="") return;
		if (window.audiomaster==="FAIL") return;

		//BUILD AUDIO MASTER IF NEEDED AND LOAD JS FILES FOR IT
		if (window.audiomaster===undefined) {
			RVS.F.showWaitAMinute({fadeIn:500,text:RVS_LANG.audiolibraryloading});
			jQuery.getScript(RVS.ENV.plugin_url+'/admin/assets/js/plugins/wavesurfer.js',function() {
				window.audiomaster = WaveSurfer.create({
					container : '#media_audio_master',
					cursorColor:'#5e35b1',
					progressColor:'transparent',
					height:30,
					plugins: [WaveSurfer.regions.create({})]
				});
				RVS.DOC.on('listenAudioMaster',function() {
					window.audiomaster.play();
				});

				RVS.DOC.on('muteAudioMaster',function() {
					window.audiomaster.pause();
				});

				RVS.DOC.on('updateaudiorange', function() {
					RVS.F.updateAudioTimes(window.audiomaster.getDuration());
				});
				//Recall the Function
				RVS.F.checkForAudioLayer();
				setTimeout(function() {RVS.F.showWaitAMinute({fadeOut:500});},100);
			}).fail(function(a,b,c) {
				setTimeout(function() {RVS.F.showWaitAMinute({fadeOut:500});},100);
				window.audiomaster = "FAIL";
			});



		} else {
			window.audiomaster.on('ready',function() {
				RVS.F.updateAudioTimes(window.audiomaster.getDuration());
				jQuery('#audio_simulator').removeClass("disabled");
			});
			window.audiomaster.on('loading',function() { jQuery('#audio_simulator').addClass("disabled");});
			window.audiomaster.on('finish',function() { RVS.F.changeSwitchState({el:jQuery('#audio_simulator')[0],state:"play"});});
			window.audiomaster.load(RVS.L[RVS.selLayers[0]].media.audioUrl);
		}

	};

	RVS.F.updateSelectedLayersIdleHover = function() {
		for (var l in RVS.L) {
			if(!RVS.L.hasOwnProperty(l)) continue;
			if (RVS.H[l]!==undefined) {
				if (RVS.eMode.mode==="idle" && RVS.H[l].hover!==undefined) {
					RVS.H[l].hover.seek(0).pause();
				}
				else if (RVS.eMode.mode==="hover" && (RVS.L[l].hover.usehover===true || RVS.L[l].hover.usehover==='true' || RVS.L[l].hover.usehover==="desktop")&& RVS.H[l].hover!==undefined) {
					if (jQuery.inArray(parseInt(l,0),RVS.selLayers)>=0) {
						RVS.H[l].hover.seek(9999).pause();
					}
					else {
						RVS.F.drawHTMLLayer({uid:l});
						RVS.H[l].hover.seek(0).pause();
					}
				}
			}
		}

	};

	RVS.F.checkCurrentLayerHoverMode = function(_) {
		if (RVS.eMode.mode==="hover" && (RVS.L[_.layerid].hover.usehover=='true' || RVS.L[_.layerid].hover.usehover==true || RVS.L[_.layerid].hover.usehover=='desktop') && RVS.H[_.layerid].hover!==undefined) RVS.H[_.layerid].hover.seek(9999).pause();
	};



	// GET THE CURRENT ROW STRUCTURE
	RVS.F.getRowStructures = function(obj) {
		if (RVS.L[obj.layerid].type==="row") {
			var cols = RVS.F.getColumnsInRow({layerid:obj.layerid}),
				structure = "",
				j = 0;
			for (var c in cols) {
				if(!cols.hasOwnProperty(c)) continue;
				structure = j===0 ? "" : structure+"+";
				structure += RVS.L[cols[c]].group.columnSize;
				j++;
			}
			return structure;
		}
	};

	/*UPDATE THE COLUMN AND ROW FIELDS BASED ON SELECTED LAYERS*/
	RVS.F.updateRowColumnField = function() {
		var newval = "",
			different = false;
		for (var li in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(li)) continue;
			var uid = RVS.selLayers[li],
				temp = "";
			if (RVS.L[uid].type==="row") temp = RVS.F.getRowStructures({layerid:uid});
			else
			if (RVS.L[uid].type==="column") temp = RVS.F.getRowStructures({layerid:RVS.L[RVS.L[uid].group.puid].uid});

			if (RVS.L[uid].type==="row" || RVS.L[uid].type==="column") {
				if (!different) {

					if (newval==="" || newval===temp) {
						newval=temp;
					} else {
							different = true;
							newval="";
						}
				}
			}
		}
		jQuery('#row_column_structure').val(RVS.F.sanitize_columns(newval)).change();
	};

	/*
	GET NEXT ELEMENT IN COLUMN (IN ORDER)
	*/
	RVS.F.getPrevNextLayerInOrder = function(id,dir) {
		if (RVS.L[id]===undefined || RVS.L[id].group===undefined || RVS.L[id].group.puid===undefined) return;
		var ret = { order:dir==="next" ? 99999 : -2, id:id};
		for (var i in RVS.L) {
			if(!RVS.L.hasOwnProperty(i)) continue;
			if (RVS.L[i].group && RVS.L[i].group.puid == RVS.L[id].group.puid &&
				(
					(dir==="next" && RVS.L[i].group.groupOrder>RVS.L[id].group.groupOrder && RVS.L[i].group.groupOrder<ret.order) ||
					(dir==="prev" && RVS.L[i].group.groupOrder<RVS.L[id].group.groupOrder && RVS.L[i].group.groupOrder>ret.order)
				)) {

				ret.order = RVS.L[i].group.groupOrder;
				ret.id = RVS.L[i].uid;
			}
		}
		return ret.id;
	};


	/*
	GET COLUMNS IDS IN A ROW
	*/
	RVS.F.getColumnsInRow = function(obj) {
		var temp = [],
			list = [];
		obj.type = obj.type===undefined ? "column" : obj.type;
		if (obj.copyPaste==="paste")
			for (var li in window.copyPasteLayers.layers) {
				if(!window.copyPasteLayers.layers.hasOwnProperty(li)) continue;
				if (window.copyPasteLayers.layers[li].type===obj.type && window.copyPasteLayers.layers[li].group.puid==obj.layerid)
					temp.push({order:window.copyPasteLayers.layers[li].group.groupOrder, uid:window.copyPasteLayers.layers[li].uid});
			}
		else
			for (var li in RVS.L) {
				if(!RVS.L.hasOwnProperty(li)) continue;
				if (RVS.L[li].type===obj.type && RVS.L[li].group.puid==obj.layerid)
					temp.push({order:RVS.L[li].group.groupOrder, uid:RVS.L[li].uid});
			}

		temp.sort(function(a,b) { return a.order - b.order;});

		for (var li in temp) {
			if(!temp.hasOwnProperty(li)) continue;
			list.push(temp[li].uid);
		}

		return list;
	};


	/*
	GET INHERITED ITEMS ON NEXT LEVEL
	*/
	RVS.F.getLayersFirstDepth = function(obj) {
		var list = [];
		if (obj.copyPaste==="paste")
			for (var li in window.copyPasteLayers.layers) {
				if(!window.copyPasteLayers.layers.hasOwnProperty(li)) continue;
				if (window.copyPasteLayers.layers[li].group.puid==obj.layerid)
					list.push(window.copyPasteLayers.layers[li].uid);
			}
		else
			for (var li in RVS.L) {
				if(!RVS.L.hasOwnProperty(li)) continue;
				if (RVS.L[li].group.puid==obj.layerid)
					list.push(RVS.L[li].uid);
			}
		return list;
	};

	RVS.F.getLayersAllDepth = function(obj) {
		var list = [];
		for (var li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if (RVS.L[li].group.puid==obj.layerid) {
				if (RVS.L[li].type==="column") {
					var inlist = RVS.F.getLayersFirstDepth({layerid:RVS.L[li].uid});
					for (var il in inlist) {
						if(!inlist.hasOwnProperty(il)) continue;
						list.push(inlist[il].uid);
					}
				} else {
					list.push(RVS.L[li].uid);
				}
			}
		}
		return list;
	};

	/*
	CHECK IF ID IS ALREADY SELECTED
	*/
	RVS.F.inSelectedLayers = function(_) {
		var isin = false;
		for (var i in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(i)) continue;
			if (RVS.selLayers[i] == _.id) isin = true;
		}
		return isin;
	};

	RVS.F.notOnRoot = function(layer) {
		return (layer!==undefined && layer.group!==undefined && layer.group.puid!==undefined && layer.group.puid>=0 && layer.group.puid<=5000);
	};

	RVS.F.groupOrColumn = function(layer) {
		return (layer!=undefined && (layer.type==="column" || layer.type==="group"));
	};

	//Get First Selected Predefine Type or Parent if its Type fits
	RVS.F.getFirstSelectedType = function(type) {
		var r = false;
		for (var i in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(i)) continue;
			if (r===false && RVS.L[RVS.selLayers[i]].type===type) r = RVS.selLayers[i];
			if (r===false && RVS.F.notOnRoot(RVS.L[RVS.selLayers[i]]) && RVS.L[RVS.L[RVS.selLayers[i]].group.puid].type===type) r = RVS.L[RVS.selLayers[i]].group.puid;
		}
		return r;
	};

	RVS.F.updateAllHTMLLayerPositions = function(onlyslidealigned) {
		for (var i in RVS.L) {
			if(!RVS.L.hasOwnProperty(i)) continue;
			if (RVS.L[i].position.x!==undefined && (onlyslidealigned==undefined || RVS.L[i].behavior.baseAlign==="slide"))
				RVS.F.updateHTMLLayerPosition({uid:i});
		}
	};

	/*
	PUT LAYER IN RIGHT POSITION
	*/
	RVS.F.updateHTMLLayerPosition = function(_) {
		var lh = RVS.H[_.uid],
			l = RVS.L[_.uid];

		if (l===undefined || lh===undefined) return;
		var tr = {left:0,top:0},
			o = _.o ==undefined ? {x:0,y:0} : _.o,
			lpv = l.position.vertical[RVS.screen].v,
			lph = l.position.horizontal[RVS.screen].v,
			temp,
			mem;

		temp = mem = {x:parseInt(l.position.x[RVS.screen].v,0), y:+parseInt(l.position.y[RVS.screen].v,0)};


		tr.force3D=true;


		if (l.type!=="row" && l.type!=="column" && (l.group.puid===-1 || (RVS.L[l.group.puid].type!=="column"))) {
			var lhcw =  _.lhCwidth===undefined ? lh.c.outerWidth() : _.lhCwidth,
				lhch =  _.lhCheight===undefined ? lh.c.outerHeight() : _.lhCheight,
				parcontsize = l.group.puid===-1 ? {width:RVS.C.layergrid.width(), height:RVS.C.layergrid.height()} : {width:RVS.H[l.group.puid].w.width(), height:RVS.H[l.group.puid].w.height()},
				cache = { x: lph==="center" ? parcontsize.width/2 - lhcw/2 : temp.x, y: lpv==="middle" ? parcontsize.height/2 - lhch/2 : temp.y};

			if (_.snapToGrid && RVS.S.DaD.sameLevelNotColumn) {
				var s = RVS.F.getSnapPoint(cache.x,cache.y),
					so = RVS.F.getSnapPoint(o.x,o.y);
				temp = RVS.F.getSnapPoint(temp.x,temp.y);
				if (lph!=="center") cache.x = s.x;
				if (lpv!=="middle") cache.y = s.y;
				RVS.S.updatePosToXDif = o.x-so.x - (temp.x -mem.x);
				RVS.S.updatePosToYDif = o.y-so.y - (temp.y -mem.y);
				o  = so;
			}

			if (lph!=="right") {tr.right = "auto"; tr.left = cache.x + o.x; } else { tr.left="auto"; tr.right = cache.x - o.x}
			if (lpv!=="bottom") {tr.bottom = "auto"; tr.top = cache.y + o.y; } else { tr.top="auto"; tr.bottom = cache.y - o.y}

			tr.x = lph==="center" ? temp.x : 0;
			tr.y = lpv==="middle" ? temp.y : 0;



			if (l.behavior.baseAlign==="slide" && l.group.puid===-1) {
				tr.x = lph==="right" ? tr.x + (RVS.C.layergrid[0].offsetLeft) : lph==="left" ? tr.x - (RVS.C.layergrid[0].offsetLeft) : tr.x;
				tr.y = lpv==="bottom" ? tr.y + (RVS.C.layergrid[0].offsetTop) : lpv==="top" ? tr.y - (RVS.C.layergrid[0].offsetTop) : tr.y;
			}

			if (_.updateValues) {
				var updatePosTo = {x:(lph === "right" ? mem.x-o.x : mem.x+o.x),
								   y: (lpv === "bottom" ? mem.y-o.y : mem.y+o.y)};



				if (RVS.S.DaD.toContainerType==="column") {updatePosTo.x = 0;updatePosTo.y = 0; }
				else if (_.snapToGrid && RVS.S.DaD.sameLevelNotColumn) updatePosTo  = RVS.F.getSnapPoint(updatePosTo.x,updatePosTo.y);
				else if (_.snapToLayers && RVS.S.DaD.sameLevelNotColumn) {
				 	if (RVS.S.DaD.snapHF.uid!==-1) {
				 		if (_.uid == RVS.S.DaD.currentLayerId) {
							var newx = parseInt(RVS.L[RVS.S.DaD.snapHF.uid].position.x[RVS.screen].v,0) + (lph==="left" ? RVS.S.DaD.snapHF.offset : lph==="center" ? RVS.S.DaD.snapHF.offset - RVS.S.DaD.snapHF.w/2 : RVS.S.DaD.snapHF.w - RVS.S.DaD.snapHF.offset),
								correction = RVS.S.DaD.snapHF.prio>6 ? lph==="right" ? 0 : lph==="left" ? 0-lhcw : 0-lhcw/2 : RVS.S.DaD.snapHF.prio>3 ? lph==="right" ? 0-lhcw/2 : lph==="left" ? 0-lhcw/2 : 0 : RVS.S.DaD.snapHF.prio<4 ? lph==="right" ? 0-lhcw : lph==="left" ? 0 : lhcw/2 : 0;
							newx += correction;
							RVS.S.DaD.difh = updatePosTo.x - newx;
							updatePosTo.x = Math.round(newx);
						} else {
							updatePosTo.x = updatePosTo.x - RVS.S.DaD.difh;
						}
						if (lph==="right") tr.right = tr.right - RVS.S.DaD.difh; else tr.left = tr.left - RVS.S.DaD.difh;

					}
					if (RVS.S.DaD.snapVF.uid!==-1) {
						if (_.uid == RVS.S.DaD.currentLayerId) {
							var newy = parseInt(RVS.L[RVS.S.DaD.snapVF.uid].position.y[RVS.screen].v,0) + (lpv==="top" ? RVS.S.DaD.snapVF.offset : lpv==="middle" ? RVS.S.DaD.snapVF.offset - RVS.S.DaD.snapVF.h/2 : RVS.S.DaD.snapVF.h - RVS.S.DaD.snapVF.offset),
								correction = RVS.S.DaD.snapVF.prio>6 ? lpv==="bottom" ? 0 : lpv==="top" ? 0-lhch : 0-lhch/2 : RVS.S.DaD.snapVF.prio>3 ? lpv==="bottom" ? 0-lhch/2 : lpv==="top" ? 0-lhch/2 : 0 : RVS.S.DaD.snapVF.prio<4 ? lpv==="bottom" ? 0-lhch : lpv==="top" ? 0 : lhch/2 : 0;
							newy += correction;
							RVS.S.DaD.difv = updatePosTo.y - newy;
							updatePosTo.y = Math.round(newy);
						} else {
							updatePosTo.y = updatePosTo.y - RVS.S.DaD.difv;
						}
						if (lpv==="bottom") tr.bottom = tr.bottom - RVS.S.DaD.difv; else tr.top = tr.top - RVS.S.DaD.difv;
					}
				}
				RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+_.uid+'.position.x.#size#.v',val:updatePosTo.x+"px", uid:_.uid});
				RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+_.uid+'.position.y.#size#.v',val:updatePosTo.y+"px", uid:_.uid});
				RVS.S.updatePosToX = updatePosTo.x;
				RVS.S.updatePosToY = updatePosTo.y;
			} else
			if (_.mouseInfo) {
				RVS.S.updatePosToX = RVS.S.DaD.toContainerType==="column" ? 0 : (lph === "right" ? mem.x-o.x : mem.x+o.x);
				RVS.S.updatePosToY = RVS.S.DaD.toContainerType==="column" ? 0 : (lpv === "bottom" ? mem.y-o.y : mem.y+o.y);
				if (_.snapToGrid && RVS.S.DaD.sameLevelNotColumn) {
					var sp = RVS.F.getSnapPoint(RVS.S.updatePosToX,RVS.S.updatePosToY);
					RVS.S.updatePosToX = sp.x;
					RVS.S.updatePosToY = sp.y;
				}
			}


			tr.position="absolute";

			requestAnimationFrame(function() {
				tpGS.gsap.set(lh.w,tr);
			});
		} else {
			requestAnimationFrame(function() {
				if (lh.w[0].style.position!=="relative") tpGS.gsap.set(lh.w,{x:0,y:0,position:"relative",left:"auto",right:"auto",top:"auto",bottom:"auto"});
			});
		}

	};

	function clearDragClasses(i) {
		RVS.H[i].w[0].className = RVS.H[i].w[0].className
									.split("dont_blur").join("")
									.split("drop_over_layer").join("")
									.split("drop_after_layer").join("")
									.split("drop_before_layer").join("")
									.split("drop_before_firstlayer").join("")
									.split("drop_after_lastlayer").join("");

		//RVS.H[i].w.removeClass("dont_blur").removeClass("drop_over_layer").removeClass("drop_after_layer").removeClass("drop_before_layer").removeClass("drop_before_firstlayer").removeClass("drop_after_lastlayer");
	}

	RVS.F.resetDragStates = function() {
		RVS.S.DaD.showInMini = false;
		for (var i in RVS.H) if(RVS.H.hasOwnProperty(i)) clearDragClasses(i);

	};


	/*
	MOVE LAYERS BY KEYBOARD
	*/
	RVS.F.moveLayerByKeys = function(_) {
		if (!window.moveByKeyboard) {
			RVS.F.openBackupGroup({id:"LayerPosition",txt:"Layer Position",icon:"open_with"});
			window.moveByKeyboard = true;
		}

		RVS.S.DaD.difh = 0;
		RVS.S.DaD.difv = 0;

		if (RVS.selLayers.length===1 && RVS.L[RVS.selLayers[0]].group.puid!==-1 && RVS.L[RVS.L[RVS.selLayers[0]].group.puid].type==="column") {

			if (_.x==-1 || _.y==-1) {
				var tid = RVS.F.getPrevNextLayerInOrder(RVS.selLayers[0],"prev");
				if (tid!==RVS.selLayers[0]) RVS.F.sortLayer({layer:RVS.selLayers[0], target:"before",env:tid, redraw:true});
			} else
			if (_.x===1 || _.y===1) {
				var tid = RVS.F.getPrevNextLayerInOrder(RVS.selLayers[0],"next");
				if (tid!==RVS.selLayers[0]) RVS.F.sortLayer({layer:RVS.selLayers[0], target:"after",env:tid, redraw:true});
			}
		} else
		for (var si in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(si)) continue;
			var i = RVS.selLayers[si];
			RVS.F.updateHTMLLayerPosition({ uid:i,o:_,updateValues:true,lhCwidth:RVS.H[i].c.outerWidth(), lhCheight:RVS.H[i].c.outerHeight()});

			document.getElementById('layer_pos_x').value = RVS.L[i].position.x[RVS.screen].v;
			document.getElementById('layer_pos_y').value = RVS.L[i].position.y[RVS.screen].v;

		}
	};

	// LAYER SNAPPING HELPERS
	RVS.F.collectAllSameAlignedLayers = function(uid) {
		RVS.S.DaD.snapH = {};
		RVS.S.DaD.snapV = {};
		for (var i in RVS.L) {
		 	if (!RVS.L[i].hasOwnProperty || RVS.L[i].group.puid!== RVS.S.DaD.fromContainerID || RVS.L[i].hover===undefined || uid==RVS.L[i].uid) continue;
		 	if (RVS.L[i].position.horizontal[RVS.screen].v === RVS.L[uid].position.horizontal[RVS.screen].v) {
		 			RVS.S.DaD.snapH[RVS.L[i].uid] = { ox:parseInt(RVS.L[i].position.x[RVS.screen].v,0), x: RVS.H[i].w[0].offsetLeft,  w:RVS.H[i].w[0].offsetWidth};
		 			RVS.S.DaD.snapH[RVS.L[i].uid].x = RVS.L[uid].position.horizontal[RVS.screen].v === "center" ? RVS.S.DaD.snapH[RVS.L[i].uid].ox + RVS.S.lgw/2  - RVS.S.DaD.snapH[RVS.L[i].uid].w/2: RVS.S.DaD.snapH[RVS.L[i].uid].x;
		 			RVS.S.DaD.snapH[RVS.L[i].uid].xc = RVS.S.DaD.snapH[RVS.L[i].uid].x + RVS.S.DaD.snapH[RVS.L[i].uid].w/2;
		 			RVS.S.DaD.snapH[RVS.L[i].uid].xr = RVS.S.DaD.snapH[RVS.L[i].uid].x + RVS.S.DaD.snapH[RVS.L[i].uid].w;
		 	}

		 	if (RVS.L[i].position.vertical[RVS.screen].v === RVS.L[uid].position.vertical[RVS.screen].v) {
		 		RVS.S.DaD.snapV[RVS.L[i].uid] = {  oy:parseInt(RVS.L[i].position.y[RVS.screen].v,0),  y: RVS.H[i].w[0].offsetTop, h:RVS.H[i].w[0].offsetHeight};
		 		RVS.S.DaD.snapV[RVS.L[i].uid].y = RVS.L[uid].position.vertical[RVS.screen].v === "middle" ? RVS.S.DaD.snapV[RVS.L[i].uid].oy + RVS.S.lgh/2  - RVS.S.DaD.snapV[RVS.L[i].uid].h/2 : RVS.S.DaD.snapV[RVS.L[i].uid].y;
		 		RVS.S.DaD.snapV[RVS.L[i].uid].ym = RVS.S.DaD.snapV[RVS.L[i].uid].y + RVS.S.DaD.snapV[RVS.L[i].uid].h/2;
		 		RVS.S.DaD.snapV[RVS.L[i].uid].yb = RVS.S.DaD.snapV[RVS.L[i].uid].y + RVS.S.DaD.snapV[RVS.L[i].uid].h;
		 	}
		}
	}

	RVS.F.closeToOtherLayers = function(uid,o) {
		RVS.S.DaD.snapHF = {offset:10000, d:20000,uid:-1,prio:50};
		RVS.S.DaD.snapVF = {offset:10000, d:20000,uid:-1,prio:50};
		o.m = o.x+o.w/2;
		o.e = o.x+o.w;
		var g = RVS.SLIDER.settings.snap.gap,
			gh = 10;


		for (var i in RVS.S.DaD.snapH) {
			if (!RVS.S.DaD.snapH[i].hasOwnProperty) continue;
			let c = RVS.S.DaD.snapH[i],
				ll = Math.abs(o.x-c.x + RVS.S.DaD.scrolldiff.x), lm = Math.abs(o.x-c.xc + RVS.S.DaD.scrolldiff.x), le = Math.abs(o.x-c.xr + RVS.S.DaD.scrolldiff.x),
				gll = Math.abs(o.x-(c.x-g) + RVS.S.DaD.scrolldiff.x), gle = Math.abs(o.x-(c.xr+g) + RVS.S.DaD.scrolldiff.x),
				ml = Math.abs(o.m-c.x + RVS.S.DaD.scrolldiff.x), mm = Math.abs(o.m-c.xc + RVS.S.DaD.scrolldiff.x), me = Math.abs(o.m-c.xr + RVS.S.DaD.scrolldiff.x),
				//gml = Math.abs(o.m-(c.x-g) + RVS.S.DaD.scrolldiff.x),  gme = Math.abs(o.m-(c.xr+g) + RVS.S.DaD.scrolldiff.x),
				rl = Math.abs(o.e-c.x + RVS.S.DaD.scrolldiff.x), rm = Math.abs(o.e-c.xc + RVS.S.DaD.scrolldiff.x), re = Math.abs(o.e-c.xr + RVS.S.DaD.scrolldiff.x);
				grl = Math.abs(o.e-(c.x-g) + RVS.S.DaD.scrolldiff.x),  gre = Math.abs(o.e-(c.xr+g) + RVS.S.DaD.scrolldiff.x);

			if (ll<gh && ll<RVS.S.DaD.snapHF.d) RVS.S.DaD.snapHF = {offset:0, uid:i ,d:ll, prio:1, w:c.w};
			else if (g>5 && gll<gh && gll<RVS.S.DaD.snapHF.d) RVS.S.DaD.snapHF = {offset:0-g, uid:i ,d:ll, prio:1, w:c.w};

			else if (RVS.S.DaD.snapHF.prio>=2 && lm<=gh && RVS.S.DaD.snapHF.offset>c.w/2) RVS.S.DaD.snapHF = {offset:c.w/2, uid:i, prio:2 ,d:lm, w:c.w};

			else if (RVS.S.DaD.snapHF.prio>=3 && le<=gh && RVS.S.DaD.snapHF.offset>c.w) RVS.S.DaD.snapHF = {offset:c.w, uid:i, prio:3, d:le, w:c.w};
			else if (g>5 && RVS.S.DaD.snapHF.prio>=3 && gle<gh && RVS.S.DaD.snapHF.offset>c.w) RVS.S.DaD.snapHF = {offset:c.w+g, uid:i, prio:3, d:le, w:c.w, gap:1};

			else if (RVS.S.DaD.snapHF.prio>=4 && ml<=gh && ml<RVS.S.DaD.snapHF.d) RVS.S.DaD.snapHF = {offset:0, d:ml, uid:i, prio:4 ,w:c.w};
			//else if (RVS.S.DaD.snapHF.prio>=4 && gml<g && ml<RVS.S.DaD.snapHF.d) RVS.S.DaD.snapHF = {offset:0-g, d:ml, uid:i, prio:4 ,w:c.w, gap:1};

			else if (RVS.S.DaD.snapHF.prio>=5 && mm<=gh && mm<RVS.S.DaD.snapHF.d) RVS.S.DaD.snapHF = {offset:c.w/2, d:mm, uid:i, prio:5 ,w:c.w};

			else if (RVS.S.DaD.snapHF.prio>=6 && me<=gh && me<RVS.S.DaD.snapHF.d) RVS.S.DaD.snapHF = {offset:c.w, d:me, uid:i, prio:6 ,w:c.w};
			//else if (RVS.S.DaD.snapHF.prio>=6 && gme<g && me<RVS.S.DaD.snapHF.d) RVS.S.DaD.snapHF = {offset:c.w+g, d:me, uid:i, prio:6 ,w:c.w, gap:1};

			else if (RVS.S.DaD.snapHF.prio>=7 && rl<=gh && rl<RVS.S.DaD.snapHF.d) RVS.S.DaD.snapHF = {offset:0, d:rl, uid:i, prio:7,w:c.w};
			else if (g>5 && RVS.S.DaD.snapHF.prio>=7 && grl<g && rl<RVS.S.DaD.snapHF.d) RVS.S.DaD.snapHF = {offset:0-g, d:rl, uid:i, prio:7,w:c.w, gap:1};

			else if (RVS.S.DaD.snapHF.prio>=8 && rm<=gh && rm<RVS.S.DaD.snapHF.d) RVS.S.DaD.snapHF = {offset:c.w/2, d:rm, uid:i, prio:8,w:c.w};

			else if (RVS.S.DaD.snapHF.prio>=9 && re<=gh && re<RVS.S.DaD.snapHF.d) RVS.S.DaD.snapHF = {offset:c.w, d:re, uid:i, prio:9,w:c.w};
			else if (g>5 && RVS.S.DaD.snapHF.prio>=9 && gre<g && re<RVS.S.DaD.snapHF.d) RVS.S.DaD.snapHF = {offset:c.w+g, d:re, uid:i, prio:9,w:c.w, gap:1};
		}

		o.m = o.y+o.h/2;
		o.e = o.y+o.h;

		for (var i in RVS.S.DaD.snapV) {
			if (!RVS.S.DaD.snapV[i].hasOwnProperty) continue;
			let c = RVS.S.DaD.snapV[i],
				tt = Math.abs(o.y-c.y + RVS.S.DaD.scrolldiff.y), tm = Math.abs(o.y-c.ym + RVS.S.DaD.scrolldiff.y), tb = Math.abs(o.y-c.yb + RVS.S.DaD.scrolldiff.y),
				gtt = Math.abs(o.y-(c.y-g) + RVS.S.DaD.scrolldiff.y),  gtb = Math.abs(o.y-(c.yb+g)+ RVS.S.DaD.scrolldiff.y),

				mt = Math.abs(o.m-c.y + RVS.S.DaD.scrolldiff.y), mm = Math.abs(o.m-c.ym + RVS.S.DaD.scrolldiff.y), mb = Math.abs(o.m-c.yb + RVS.S.DaD.scrolldiff.y),
				//gmt = Math.abs(o.m-(c.y-g) + RVS.S.DaD.scrolldiff.y), gmb = Math.abs(o.m-(c.yb+g)+ RVS.S.DaD.scrolldiff.y),

				bt = Math.abs(o.e-c.y + RVS.S.DaD.scrolldiff.y), bm = Math.abs(o.e-c.ym + RVS.S.DaD.scrolldiff.y), bb = Math.abs(o.e-c.yb + RVS.S.DaD.scrolldiff.y),
				gbt = Math.abs(o.e-(c.y-g) + RVS.S.DaD.scrolldiff.y), gbb = Math.abs(o.e-(c.yb+g)+ RVS.S.DaD.scrolldiff.y);


			if (tt<gh && tt<RVS.S.DaD.snapVF.d) RVS.S.DaD.snapVF = {offset:0, uid:i ,d:tt, prio:1, h:c.h};
			else if (g>5 && gtt<gh && tt<RVS.S.DaD.snapVF.d) RVS.S.DaD.snapVF = {offset:0-g, uid:i ,d:tt, prio:1, h:c.h, gap:1};

			else if (RVS.S.DaD.snapVF.prio>=2 && tm<gh && RVS.S.DaD.snapVF.offset>c.h/2) RVS.S.DaD.snapVF = {offset:c.h/2, uid:i, prio:2 ,d:tm, h:c.h};

			else if (RVS.S.DaD.snapVF.prio>=3 && tb<gh && RVS.S.DaD.snapVF.offset>c.h) RVS.S.DaD.snapVF = {offset:c.h, uid:i, prio:3, d:tb, h:c.h};
			else if (g>5 && RVS.S.DaD.snapVF.prio>=3 && gtb<gh && RVS.S.DaD.snapVF.offset>c.h) RVS.S.DaD.snapVF = {offset:c.h+g, uid:i, prio:3, d:tb, h:c.h, gap:1};

			else if (RVS.S.DaD.snapVF.prio>=4 && mt<gh && mt<RVS.S.DaD.snapVF.d) RVS.S.DaD.snapVF = {offset:0, d:mt, uid:i, prio:4, h:c.h};
			//else if (RVS.S.DaD.snapVF.prio>=4 && gmt<gh && mt<RVS.S.DaD.snapVF.d) RVS.S.DaD.snapVF = {offset:0-g, d:mt, uid:i, prio:4, h:c.h, gap:1};

			else if (RVS.S.DaD.snapVF.prio>=5 && mm<gh && mm<RVS.S.DaD.snapVF.d) RVS.S.DaD.snapVF = {offset:c.h/2, d:mm, uid:i, prio:5, h:c.h};

			else if (RVS.S.DaD.snapVF.prio>=6 && mb<gh && mb<RVS.S.DaD.snapVF.d) RVS.S.DaD.snapVF = {offset:c.h, d:mb, uid:i, prio:6, h:c.h};
			//else if (RVS.S.DaD.snapVF.prio>=6 && gmb<gh && mb<RVS.S.DaD.snapVF.d) RVS.S.DaD.snapVF = {offset:c.h+g, d:mb, uid:i, prio:6, h:c.h, gap:1};

			else if (RVS.S.DaD.snapVF.prio>=7 && bt<gh && bt<RVS.S.DaD.snapVF.d) RVS.S.DaD.snapVF = {offset:0, d:bt, uid:i, prio:7, h:c.h};
			else if (g>5 && RVS.S.DaD.snapVF.prio>=7 && gbt<gh && bt<RVS.S.DaD.snapVF.d) RVS.S.DaD.snapVF = {offset:0-g, d:bt, uid:i, prio:7, h:c.h, gap:1};

			else if (RVS.S.DaD.snapVF.prio>=8 && bm<gh && bm<RVS.S.DaD.snapVF.d) RVS.S.DaD.snapVF = {offset:c.h/2, d:bm, uid:i, prio:8, h:c.h};

			else if (RVS.S.DaD.snapVF.prio>=9 && bb<gh && bb<RVS.S.DaD.snapVF.d) RVS.S.DaD.snapVF = {offset:c.h, d:bb, uid:i, prio:9, h:c.h};
			else if (g>5 && RVS.S.DaD.snapVF.prio>=9 && gbb<gh && bb<RVS.S.DaD.snapVF.d) RVS.S.DaD.snapVF = {offset:c.h+g, d:bb, uid:i, prio:9, h:c.h, gap:1};
		}
	}

	/*
	MAKE THE LAYERS DRAGGABLE
	*/
	RVS.F.doDraggable = function(_) {
		var lh = RVS.H[_.uid];
		if (lh.w.data('draggable')) lh.w.draggable("destroy");
		if (lh.w.data('resizable')) lh.w.resizable("destroy");

		if (_.drag) {
			lh.w.draggable({
				helper:"clone",
				appendTo:"#layer_grid_"+RVS.S.slideId,
				start:function(event,ui) {

					RVS.animationMode = (RVS.eMode.menu==="#form_layer_animation" && RVS.eMode.mode==="animation");
					RVS.F.setRulers();
					RVS.F.stopAndPauseAllLayerAnimation();
					RVS.F.resetDragStates();
					RVS.S.DaD.touchPosition = {x : event.clientX - ui.originalPosition.left,  y: event.clientY - ui.originalPosition.top};

					RVS.S.ulInner[0].classList.add("dropSensorActive");

					RVS.F.updateContentDeltas();
					RVS.S.DaD.dropSensor = RVS.animationMode!==true ? 1 : 0;
					RVS.S.DaD.currentLayerId = _.uid;
					RVS.S.DaD.draggedPosType = lh.w[0].style.position;
					RVS.S.click.y = event.clientY;
					RVS.S.click.x = event.clientX;
					RVS.S.DaD.startPos = "0";
					RVS.S.DaD.fromContainerID =  RVS.L[_.uid].group.puid;
					RVS.S.DaD.lastRegisteredRow = RVS.S.DaD.fromContainerID===-1 ? -1 : RVS.L[RVS.S.DaD.fromContainerID].type==="group" ? "group" : RVS.L[RVS.S.DaD.fromContainerID].group.puid;
					RVS.S.DaD.lastRegisteredRowBefore = RVS.S.DaD.lastRegisteredRow;
					window.scrollMem =  {y:RVS.S.rb_ScrollY, x:RVS.S.rb_ScrollX};
					RVS.S.DaD.fromContainerRowColumn = RVS.S.DaD.fromContainerID!==-1;// && RVS.L[RVS.L[_.uid].group.puid].type!=="group";
					RVS.S.DaD.uiHelper = ui.helper;
					RVS.S.DaD.clone = RVS.S.DaD.fromContainerRowColumn ? ui.helper : lh.w;
					RVS.S.DaD.dragItemOffset = lh.w.offset();
					RVS.S.DaD.fromContainerType = RVS.S.DaD.fromContainerID>=0 && RVS.S.DaD.fromContainerID<=5000 ? RVS.L[RVS.L[_.uid].group.puid].type : "root";
					RVS.S.DaD.scrolldiff = { x: (RVS.S.rb_ScrollX - window.scrollMem.x), y:(RVS.S.rb_ScrollY - window.scrollMem.y)};

					RVS.ENV.tlGridWrap[0].classList.remove("selected");


					if (RVS.animationMode!==true) {

						RVS.S.WWL = RVS.L[_.uid];
						RVS.S.lpv = RVS.S.WWL.position.vertical[RVS.screen].v;
						RVS.S.lph = RVS.S.WWL.position.horizontal[RVS.screen].v;
						RVS.C.layer_pos_x = RVS.C.layer_pos_x===undefined ? document.getElementById('layer_pos_x') : RVS.C.layer_pos_x;
						RVS.C.layer_pos_y = RVS.C.layer_pos_y===undefined ? document.getElementById('layer_pos_y') : RVS.C.layer_pos_y;

						//COLLECT ALL AYERS SAME ALIGNED
						if (RVS.SLIDER.settings.snap.adjust==="layers") RVS.F.collectAllSameAlignedLayers(_.uid);
						RVS.S.DaD.snapToGrid = RVS.SLIDER.settings.snap.adjust==="grid";
						RVS.S.DaD.snapToLayers = RVS.SLIDER.settings.snap.adjust==="layers";

						//Update Offsets of Containers For Drop Options
						for (var i in RVS.H) {
							if(!RVS.H.hasOwnProperty(i)) continue;
							if (RVS.L[i].type==="group")
								RVS.H[i].w_offset = RVS.H[i].w.offset();

						}

						//if (RVS.F.inSelectedLayers({id:_.uid})==false)
						RVS.F.selectLayers({id:_.uid,overwrite:true, action:"add", quickmode:true});
						RVS.S.DaD.CoveredDims = RVS.L[_.uid].behavior.baseAlign==="slide" ? RVS.F.updateCoveredLayer(_.uid) : undefined;

						//CACHE TEMPORARY SIZES	AND DISTANCES
						for (var si in RVS.selLayers) {
							if(!RVS.selLayers.hasOwnProperty(si)) continue;
							var i = RVS.selLayers[si];
								//_nwtr = RVS.L[i].behavior.baseAlign==="slide" ? RVS.F.updateCoveredLayer(i) : undefined;
							RVS.H[i].c_width = /*RVS.H[i].w_width =  _nwtr!==undefined && _nwtr.width!==undefined ? _nwtr.width : */RVS.H[i].c.outerWidth();
							RVS.H[i].c_height = /*RVS.H[i].w_height = _nwtr!==undefined && _nwtr.height!==undefined ? _nwtr.height : */RVS.H[i].c.outerHeight();
							RVS.H[i].w_offsetcache = {horizontal:RVS.S.DaD.dragItemOffset.left - RVS.H[i].w.offset().left, vertical:RVS.S.DaD.dragItemOffset.top - RVS.H[i].w.offset().top};
						}

						RVS.S.DaD.originalWidth = lh.w.width()+1;
						ui.helper.css({zIndex:100000, width:RVS.S.DaD.originalWidth});
						tpGS.gsap.set(lh.w,{opacity:0});
						RVS.C.layergrid.addClass("layersInDragorResize");


					} else {
						if (RVS.L[RVS.S.DaD.currentLayerId].timeline.frames[RVS.S.keyFrame]===undefined || ""+RVS.S.DaD.currentLayerId!==""+RVS.selLayers[0]) {
							RVS.F.selectLayers({id:RVS.S.DaD.currentLayerId,overwrite:true, action:"add", ignoreUpdate:"onlyhtml", ignoreFieldUpdates:false, ignoreModeChange:true});
							if (RVS.S.keyFrame===undefined) {
								RVS.F.setKeyframeSelected("frame_1");
								RVS.S.keyFrame="frame_1";
							}
						}
						RVS.S.DaD.layerFrameX = document.getElementById('le_frame_x');
						RVS.S.DaD.layerFrameY = document.getElementById('le_frame_y');



						RVS.S.DaD.frameOXH = RVS.L[RVS.S.DaD.currentLayerId].timeline.frames[RVS.S.keyFrame].transform.x[RVS.screen].v;
						RVS.S.DaD.frameOYH = RVS.L[RVS.S.DaD.currentLayerId].timeline.frames[RVS.S.keyFrame].transform.y[RVS.screen].v;

						//IF Inherit, get Values before that
						if (RVS.S.DaD.frameOXH==="inherit" || RVS.S.DaD.frameOYH==="inherit") {
							var fr = RVS.F.getPrevNextFrame({layerid:RVS.selLayers[0], frame:RVS.S.keyFrame});
							RVS.S.DaD.frameOXH = RVS.S.DaD.frameOXH==="inherit" ? RVS.L[RVS.S.DaD.currentLayerId].timeline.frames[fr.prev.frameid].transform.x[RVS.screen].v : RVS.S.DaD.frameOXH;
							RVS.S.DaD.frameOYH = RVS.S.DaD.frameOYH==="inherit" ? RVS.L[RVS.S.DaD.currentLayerId].timeline.frames[fr.prev.frameid].transform.y[RVS.screen].v : RVS.S.DaD.frameOYH;
							RVS.S.DaD.frameOXH = RVS.S.DaD.frameOXH==="inherit" ? 0 : RVS.S.DaD.frameOXH;
							RVS.S.DaD.frameOYH = RVS.S.DaD.frameOYH==="inherit" ? 0 : RVS.S.DaD.frameOYH;
						}

						RVS.S.DaD.frameOX = parseInt(RVS.S.DaD.frameOXH,0);
						RVS.S.DaD.frameOY = parseInt(RVS.S.DaD.frameOYH,0);

						RVS.S.DaD.frameOX_N = RVS.F.isNumeric(RVS.S.DaD.frameOX) && ((""+RVS.S.DaD.frameOXH).indexOf("px")>=0 || RVS.S.DaD.frameOXH=="0");
						RVS.S.DaD.frameOY_N = RVS.F.isNumeric(RVS.S.DaD.frameOY) && ((""+RVS.S.DaD.frameOYH).indexOf("px")>=0 || RVS.S.DaD.frameOYH=="0");

						tpGS.gsap.set(lh.w,{opacity:1});
						ui.helper.css({opacity:0});
					}

					tpGS.gsap.set(ui.helper.find('._lc_content_')[0],{whiteSpace:"nowrap"});

				},

				drag:function(event,ui) {

					// CALCULATE  THE SCROLL OFFSET TO THE POSITION
					RVS.S.DaD.scrolldiff = { x: (RVS.S.rb_ScrollX - window.scrollMem.x), y:(RVS.S.rb_ScrollY - window.scrollMem.y)};
					RVS.S.DaD.dragdelta.x = Math.round(((event.clientX - RVS.S.click.x) + RVS.S.DaD.scrolldiff.x) / RVS.zoom);
					RVS.S.DaD.dragdelta.y = Math.round(((event.clientY - RVS.S.click.y) + RVS.S.DaD.scrolldiff.y) / RVS.zoom);


					if (RVS.animationMode===true) {
						// ANIMATION MODE, NEED TO CHANGE THE TRANSFORMS ONLY
						if (RVS.S.DaD.frameOX_N) RVS.S.DaD.layerFrameX.value = RVS.L[RVS.S.DaD.currentLayerId].timeline.frames[RVS.S.keyFrame].transform.x[RVS.screen].v=RVS.S.DaD.frameOX+RVS.S.DaD.dragdelta.x+"px";
						if (RVS.S.DaD.frameOY_N) RVS.S.DaD.layerFrameY.value = RVS.L[RVS.S.DaD.currentLayerId].timeline.frames[RVS.S.keyFrame].transform.y[RVS.screen].v=RVS.S.DaD.frameOY+RVS.S.DaD.dragdelta.y+"px";
						RVS.F.renderLayerAnimation({layerid:RVS.S.DaD.currentLayerId, quickRendering:false,timeline:"full",caller:"drawHTMLLayer"});

						requestAnimationFrame(function() {
							RVS.F.showMouseInfo({classes:"purplemouseinfo", html:"<div class='mouse_frame_label'>"+RVS.L[RVS.S.DaD.currentLayerId].timeline.frames[RVS.S.keyFrame].alias+"</div><div class='mouse_info_coor '><div><span class='mouselabel'>X</span><span class='mouseval'>"+RVS.S.DaD.layerFrameX.value+"</span></div><div><span class='mouselabel'>Y</span><span class='mouseval'>"+RVS.S.DaD.layerFrameY.value+"</span></div></div></div>"});
						});

					} else {

						RVS.S.DaD.sameLevelNotColumn = RVS.S.DaD.toContainerID===-1 && RVS.S.DaD.fromContainerType==="root";


						// CLEAR THE INPUT FIELD UPDATE TIMER !!
						if (RVS.S.updateEsyInputsTimer!==undefined) {
							clearTimeout(RVS.S.updateEsyInputsTimer);
							RVS.S.updateEsyInputsTimerMemory = true;
							delete RVS.S.updateEsyInputsTimer;
						}

						//REMOVING ELEMENTS FROM COLUMNS SHOULD BE DEPENDENT ON MOUSE POINTER
						if (RVS.S.DaD.fromContainerType==="column" && !RVS.S.DaD.showInMini) {
							RVS.S.DaD.dragdelta.x = RVS.S.DaD.dragdelta.x + RVS.S.DaD.touchPosition.x-RVS.S.layer_grid_offset.left;
							RVS.S.DaD.dragdelta.y = RVS.S.DaD.dragdelta.y + RVS.S.DaD.touchPosition.y-RVS.S.layer_grid_offset.top;
						}
						ui.position = {};

						//requestAnimationFrame(function() {
							for (var si in RVS.selLayers) {
								if(!RVS.selLayers.hasOwnProperty(si)) continue;
								var i = RVS.selLayers[si];
								RVS.F.updateHTMLLayerPosition({ uid:i,  o:RVS.S.DaD.dragdelta, updateDistanceLines:_.uid, lhCwidth:RVS.H[i].c_width, lhCheight:RVS.H[i].c_height,updateFields:i==_.uid , mouseInfo:i==RVS.S.DaD.currentLayerId, snapToGrid:RVS.S.DaD.snapToGrid, snapToLayers:RVS.S.DaD.snapToLayers});
							}
						//});

						var posobj = {
								left:((event.clientX-RVS.S.layer_grid_offset.left)/RVS.zoom),
								top:((event.clientY-RVS.S.layer_grid_offset.top)/RVS.zoom),
								transformOrigin:"0 0",opacity:0.75,scale:0.6,
								width:RVS.S.DaD.originalWidth, display:RVS.L[RVS.S.DaD.currentLayerId].idle.display,
								x:0+RVS.S.DaD.scrolldiff.x, y:0+RVS.S.DaD.scrolldiff.y
						};

						if (!RVS.S.DaD.showInMini) {
							if (RVS.S.DaD.fromContainerType!=="column") {
								posobj.left = ((event.clientX-RVS.S.DaD.touchPosition.x)/RVS.zoom);
								posobj.top = ((event.clientY-RVS.S.DaD.touchPosition.y)/RVS.zoom);
							}

							posobj.width = RVS.S.DaD.CoveredDims!==undefined && RVS.S.DaD.CoveredDims.width!==undefined ? RVS.S.DaD.CoveredDims :  RVS.L[RVS.S.DaD.currentLayerId].size.width[RVS.screen].v;
							posobj.display = "block";
							posobj.scale = 1;
						}

						if (RVS.S.DaD.snapToGrid && RVS.S.DaD.sameLevelNotColumn) {
							posobj.left = posobj.left - RVS.S.updatePosToXDif;
							posobj.top = posobj.top - RVS.S.updatePosToYDif;
						} else
						if (RVS.S.DaD.snapToLayers) RVS.F.closeToOtherLayers(RVS.S.DaD.currentLayerId, {x:posobj.left, y:posobj.top, w:RVS.H[RVS.S.DaD.currentLayerId].c_width, h:RVS.H[RVS.S.DaD.currentLayerId].c_height});


						tpGS.gsap.set(ui.helper,posobj);
						RVS.S.DaD.startPos =  {x:posobj.left, y:posobj.top};


						RVS.C.layer_pos_x.value = RVS.S.updatePosToX;
						RVS.C.layer_pos_y.value = RVS.S.updatePosToY;
						requestAnimationFrame(function() {
							if (RVS.S.updatePosToX!==undefined && RVS.S.updatePosToY!==undefined && (RVS.S.updatePosToX!=0 || RVS.S.updatePosToY!=0))
								RVS.F.showMouseInfo({html:"<div class='mouse_info_coor'><div><span class='mouselabel'>X</span><span class='mouseval'>"+RVS.S.updatePosToX+"</span></div><div><span class='mouselabel'>Y</span><span class='mouseval'>"+RVS.S.updatePosToY+"</span></div></div><div class='mouse_info_align "+RVS.S.lph+" "+RVS.S.lpv+"'><span class='mia_tl'></span><span class='mia_tc'></span><span class='mia_tr'></span><span class='mia_ml'></span><span class='mia_mc'></span><span class='mia_mr'></span><span class='mia_bl'></span><span class='mia_bc'></span><span class='mia_br'></span></div>"});
							else RVS.F.hideMouseInfo();
							if ((RVS.SLIDER.settings.snap.adjust!=="none")) {
								if (RVS.S.DaD.sameLevelNotColumn)
									RVS.F.snapVisual({av:RVS.S.lpv,ah:RVS.S.lph,sp:(RVS.SLIDER.settings.snap.adjust==="grid" ? RVS.F.getSnapPoint(RVS.S.updatePosToX,RVS.S.updatePosToY) : undefined)});
								else
								RVS.F.clearSnapVisual();
							}
						});

					}

				},
				stop:function(event,ui) {

					if ((RVS.SLIDER.settings.snap.adjust!=="none")) RVS.F.clearSnapVisual();


					tpGS.gsap.set(ui.helper,{scale:1});
					RVS.S.ulInner[0].classList.remove("dropSensorActive");
					RVS.S.DaD.dropSensor = false;
					clearHCoors();
					requestAnimationFrame(function() {
						RVS.F.hideMouseInfo();
					});
					var bgroupid = RVS.animationMode===true ? "frame" : RVS.S.DaD.fromContainerID==-1 && RVS.S.DaD.target!==undefined && RVS.S.DaD.target.into=="free" ? "layermovement" : "layersorting_layermovement";


					RVS.F.openBackupGroup({id:bgroupid,txt:RVS.animationMode!==true ? "Layer Position" : "Frame Position" ,icon:"open_with"});

					RVS.S.DaD.difh = 0;
					RVS.S.DaD.difv = 0;


					if (RVS.animationMode!==true) {
						RVS.F.updateHTMLLayerPosition({ uid:RVS.S.DaD.currentLayerId,  o:RVS.S.DaD.dragdelta,updateValues:true,lhCwidth:RVS.H[RVS.S.DaD.currentLayerId].c_width, lhCheight:RVS.H[RVS.S.DaD.currentLayerId].c_height, snapToGrid:RVS.S.DaD.snapToGrid, snapToLayers:RVS.S.DaD.snapToLayers});
						for (var si in RVS.selLayers) {
							if(!RVS.selLayers.hasOwnProperty(si)) continue;
							var i = RVS.selLayers[si];
							if (i==RVS.S.DaD.currentLayerId) continue;
							RVS.F.updateHTMLLayerPosition({ uid:i,  o:RVS.S.DaD.dragdelta,updateValues:true,lhCwidth:RVS.H[i].c_width, lhCheight:RVS.H[i].c_height, snapToGrid:RVS.S.DaD.snapToGrid, snapToLayers:RVS.S.DaD.snapToLayers});
						}
					} else {

						if (RVS.S.DaD.frameOX_N) RVS.F.backup({	path:RVS.S.slideId+".layers."+RVS.S.DaD.currentLayerId+".timeline.frames."+RVS.S.keyFrame+".transform.x.#size#.v",
																lastkey:"v",
																val:RVS.L[RVS.S.DaD.currentLayerId].timeline.frames[RVS.S.keyFrame].transform.x[RVS.screen].v,
																old:RVS.S.DaD.frameOX});

						if (RVS.S.DaD.frameOY_N) RVS.F.backup({	path:RVS.S.slideId+".layers."+RVS.S.DaD.currentLayerId+".timeline.frames."+RVS.S.keyFrame+".transform.y.#size#.v",
																lastkey:"v",
																val:RVS.L[RVS.S.DaD.currentLayerId].timeline.frames[RVS.S.keyFrame].transform.y[RVS.screen].v,
																old:RVS.S.DaD.frameOY});
					}
					RVS.S.DaD.snapToGrid = false;
					RVS.S.DaD.snapToLayers = false;

					RVS.C.layergrid[0].classList.remove("layersInDragorResize");
					if (RVS.animationMode!==true) dropLayerAfterMove();
					tpGS.gsap.set(lh.w,{opacity:1});
					RVS.F.closeBackupGroup({id:bgroupid});

					RVS.F.selectedLayersVisualUpdate();

					if (RVS.S.updateEsyInputsTimerMemory) {
						RVS.F.updateEasyInputs({container:jQuery('.layer_settings_collector'), path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
						RVS.S.updateEsyInputsTimerMemory = false;
					} else {

						RVS.F.selectLayers({id:_.uid,overwrite:true, action:"add", keepSelectedFrame:RVS.animationMode===true});
					}

				}
			});
		}


		if (_.resize) {
			lh.w.resizable({
				handles:"n,s,w,e",
				start:function(event,ui) {
					// STOP LAYER ANIMATION
					RVS.F.stopAndPauseAllLayerAnimation();
					RVS.F.showForms("*slidelayout**mode__slidecontent*#form_layer_position",true);
					RVS.S.WWL = RVS.L[_.uid];
					RVS.S.lpv = RVS.S.WWL.position.vertical[RVS.screen].v;
					RVS.S.lph = RVS.S.WWL.position.horizontal[RVS.screen].v;
					RVS.S.WWHL = RVS.H[_.uid];
					RVS.C.layer_width = RVS.C.layer_width===undefined ? jQuery('#layer_width') : RVS.C.layer_width;
					RVS.C.layer_height = RVS.C.layer_height===undefined ? jQuery('#layer_height') : RVS.C.layer_height;
					RVS.C.layer_covermode = RVS.C.layer_covermode===undefined ? jQuery('#layer_covermode') : RVS.C.layer_covermode;

					window.layertemp_width = window.layerneww = ui.size.width;
					window.layertemp_height = window.layernewh = ui.size.height;
					window.resizeDirection = "none";

					RVS.F.setZindex({id:_.uid,o:475});
					RVS.C.layergrid.addClass("layersInDragorResize");
					//PREPARE SIMILAR CONTAINER FOR CALCULATING AUTO AND NORMAL HEIGHTS
					if (RVS.S.WWL.type==="text" || RVS.S.WWL.type==="button") {
						window.layerclone = RVS.S.WWHL.w.clone(false);
						window.layercloneinside = window.layerclone.find('._lc_content_').first();
						RVS.C.slide.append(window.layerclone);
						tpGS.gsap.set(window.layerclone,{autoAlpha:0});
					}

				},
				resize:function(event,ui) {
					var dir = window.layerneww!=ui.size.width ? "horizontal" : "vertical";

					window.layerneww = ui.size.width;
					window.layernewh = ui.size.height;


					switch (RVS.S.WWL.type) {
						case "text":
						case "button":
							tpGS.gsap.set([window.layercloneinside],{width:ui.size.width, height:"auto"});
							if (RVS.S.WWL.size.height[RVS.screen].v==="auto" || window.layercloneinside.height() > ui.size.height) {
								if (dir==="horizontal")
									window.layernewh = ui.size.height = window.layercloneinside.outerHeight();
								else
									window.layernewh = ui.size.height = Math.max(parseInt(ui.size.height,0) || 0, parseInt(window.layercloneinside.outerHeight(),0) || 0);
							}
						break;
					}



					if (RVS.S.WWL.size.minWidth[RVS.screen].v!=="none") window.layerneww = ui.size.width = Math.max(window.layerneww, parseInt(RVS.S.WWL.size.minWidth[RVS.screen].v,0) || 0);
					if (RVS.S.WWL.size.maxWidth[RVS.screen].v!=="none") window.layerneww = ui.size.width = Math.min(window.layerneww, parseInt(RVS.S.WWL.size.maxWidth[RVS.screen].v,0) || 0);
					if (RVS.S.WWL.size.minHeight[RVS.screen].v!=="none") window.layernewh = ui.size.height = Math.max(window.layernewh, parseInt(RVS.S.WWL.size.minHeight[RVS.screen].v,0) || 0);
					if (RVS.S.WWL.size.maxHeight[RVS.screen].v!=="none") window.layernewh = ui.size.height = Math.min(window.layernewh, parseInt(RVS.S.WWL.size.maxHeight[RVS.screen].v,0) || 0);



					requestAnimationFrame(function() {
						RVS.F.showMouseInfo({html:"<div class='mouse_info_coor'><div><span class='mouselabel'>W</span><span class='mouseval'>"+window.layerneww+"</span></div><div><span class='mouselabel'>H</span><span class='mouseval'>"+window.layernewh+"</span></div></div><div class='mouse_info_align "+RVS.S.lph+" "+RVS.S.lpv+"'><span class='mia_tl'></span><span class='mia_tc'></span><span class='mia_tr'></span><span class='mia_ml'></span><span class='mia_mc'></span><span class='mia_mr'></span><span class='mia_bl'></span><span class='mia_bc'></span><span class='mia_br'></span></div>"});
					});




					//CHANGE WIDTH AND COVER MODE IF WIDTH CHANGED
					if (window.layertemp_width != window.layerneww) {
						if (window.resizeDirection==="none") window.resizeDirection="horizontal";
						RVS.C.layer_width[0].value = window.layerneww+"px";
						if (RVS.L[_.uid].size.covermode==="fullwidth" || RVS.L[_.uid].size.covermode==="cover" || RVS.L[_.uid].size.covermode==="cover-proportional") {
							if (RVS.C.layer_covermode[0].value!=="custom") RVS.C.layer_covermode.val("custom").ddTP('change');
							if (RVS.L[_.uid].size.covermode==="cover" || RVS.L[_.uid].size.covermode==="cover-proportional")
								jQuery('.layersize_wrap').removeClass("disablecontainer");
							else
								jQuery('.layersize_wrap_width').removeClass("disablecontainer");
						}
					}

					//CHANGE HEIGHT AND COVER MODE IF HEIGHT CHANGED
					if (window.layertemp_height != window.layernewh) {
						if (window.resizeDirection==="none") window.resizeDirection="vertical";
						RVS.C.layer_height[0].value = (RVS.S.WWL.type==="video" && RVS.S.WWL.size.height[RVS.screen].v==="auto" ? "auto" : window.layernewh+"px");
						if (RVS.L[_.uid].size.covermode==="fullheight" || RVS.L[_.uid].size.covermode==="cover" || RVS.L[_.uid].size.covermode==="cover-proportional") {
							if (RVS.C.layer_covermode[0].value!=="custom") RVS.C.layer_covermode.val("custom").ddTP('change');
							if (RVS.L[_.uid].size.covermode==="cover" || RVS.L[_.uid].size.covermode==="cover-proportional")
								jQuery('.layersize_wrap').removeClass("disablecontainer");
							else
								jQuery('.layersize_wrap_height').removeClass("disablecontainer");
						}
					}



					//RESPECT ASPECT RATIO !!!
					if (RVS.S.WWL.size.scaleProportional &&  (RVS.S.WWL.size.aspectRatio[RVS.screen].v!=="auto" && RVS.S.WWL.size.aspectRatio[RVS.screen].v!=="none")) {
						if (window.resizeDirection==="horizontal")
							window.layernewh = ui.size.height = Math.round(window.layerneww / RVS.S.WWL.size.aspectRatio[RVS.screen].v);
						else
							window.layerneww = ui.size.width = Math.round(window.layernewh * RVS.S.WWL.size.aspectRatio[RVS.screen].v);
					}

					// VIDEO WITHIN COLUMN ON RESIZE MUST BE HANDLD SPECIAL
					if (RVS.S.WWL.type==="video" && RVS.S.WWL.size.height[RVS.screen].v==="auto") {
						var prop = RVS.S.WWL.media.ratio.split(":");
						prop = prop[1] / prop[0];
						ui.size.height = window.layerneww * prop;
					}


						tpGS.gsap.set(RVS.H[_.uid].c,{width:window.layerneww, height:window.layernewh});
						RVS.F.updateHTMLLayerPosition({uid:_.uid});

						//ELEMENTS IN GROUP NEED TO BE REPOSITIONED ALSO !
					requestAnimationFrame(function() {
						var lInColumns = RVS.F.getLayerChildren({layerid:_.uid});
						for (var i in lInColumns) {
							if(!lInColumns.hasOwnProperty(i)) continue;
							RVS.F.updateHTMLLayerPosition({uid:i});
						}

						if (RVS.F.updateMinSliderHeights()) RVS.DOC.trigger('updatesliderlayout','layertools.js - 1893');

						RVS.F.updateSharpCorners({uid:_.uid,resize:true});
					});
				},
				stop:function(event,ui) {
					RVS.C.layergrid.removeClass("layersInDragorResize");
						RVS.F.hideMouseInfo();
					RVS.S.justresized = true;

					RVS.F.openBackupGroup({id:"layerresize",txt:"Resize Layer",icon:"photo_size_select_large"});

						// IF LAYER WAS TEXT, CLONED HELPCONTAINER MUST BE REMOVED
						if (RVS.S.WWL.type==="text" || RVS.S.WWL.type==="button") {
							window.layernewh = window.layercloneinside.height() === window.layernewh && RVS.L[_.uid].type=="text" ? "auto" : window.layernewh;
							window.layerclone.remove();
						}
						// IF WIDTH HAS BEEN CHANGED, CHECK INFLUENCES AND SAVE OBJECT CHANGES
						if (window.layertemp_width != window.layerneww) {
							RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+_.uid+'.size.width.#size#.v',val:window.layerneww+"px", uid:_.uid});
							if (RVS.L[_.uid].size.covermode==="fullwidth" || RVS.L[_.uid].size.covermode==="cover" || RVS.L[_.uid].size.covermode==="cover-proportional")
								RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+_.uid+'.size.covermode',val:"custom", uid:_.uid});
						}
						// IF HEIGHT HAS BEEN CHANGED, CHECK INFLUENCES AND SAVE OBJECT CHANGES
						if (window.layertemp_height != window.layernewh) {
							RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+_.uid+'.size.height.#size#.v',val:(RVS.S.WWL.type==="video" && RVS.S.WWL.size.height[RVS.screen].v==="auto" ? "auto" : isNaN(window.layernewh) ? window.layernewh : window.layernewh+"px"), uid:_.uid});
							if (RVS.L[_.uid].size.covermode==="fullheight" || RVS.L[_.uid].size.covermode==="cover" || RVS.L[_.uid].size.covermode==="cover-proportional")
								RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+_.uid+'.size.covermode',val:"custom", uid:_.uid});
						}
					RVS.F.closeBackupGroup({id:"layerresize"});


					if (RVS.F.updateMinSliderHeights())
						RVS.DOC.trigger('updatesliderlayout','layertools.js - 1056');

					setTimeout(function() {
						RVS.S.justresized = false;
					},100);

				}
			});
		}
	};

/***********************************
	MAIN INTERNAL FUNCTIONS
************************************/


	/*
	APPEND/PREPEND THE DRAGGED LAYER INTO THE RIGHT CONTAINE BY TRIGGERING THE
	*/
	function dropLayerAfterMove() {
		clearTimeout(RVS.S.DaD.timer);

		var target = RVS.S.DaD.target !== undefined ? RVS.S.DaD.target.into : "free";
		if (target==="column") {
			if (RVS.S.DaD.target!==undefined) {

				if (RVS.S.DaD.target.columnID!==undefined && (RVS.S.DaD.target.columnType==="group" || RVS.S.DaD.target.elementID===undefined)) {
					if (RVS.S.DaD.target.columnTop) {
						if (RVS.S.DaD.target.columnType!=="group" || RVS.S.DaD.fromContainerID != RVS.S.DaD.target.columnID) {
							var rp = { //only if dropped into a Group
										x:(RVS.S.DaD.clone.offset().left-RVS.S.DaD.dropParentPos.x)/RVS.zoom + RVS.S.DaD.scrolldiff.x,
										y:(RVS.S.DaD.clone.offset().top-RVS.S.DaD.dropParentPos.y)/RVS.zoom + RVS.S.DaD.scrolldiff.y};

							// MODIFICATE SIZE OF TEXT LAYERS DROPPED INTO COLUMN
							if (RVS.S.DaD.target.columnType==="column") {
								for (var i in RVS.selLayers) {
									if(!RVS.selLayers.hasOwnProperty(i)) continue;
									if (RVS.L[RVS.selLayers[i]].type==="text") {
										for (var _screens in RVS.V.sizes) {
											if(!RVS.V.sizes.hasOwnProperty(_screens)) continue;
											RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+RVS.selLayers[i]+'.idle.whiteSpace.'+RVS.V.sizes[_screens]+'.v',val:"full", uid:RVS.selLayers[i]});
											if (RVS.L[RVS.selLayers[i]].size.width[RVS.V.sizes[_screens]].v.indexOf("%")==-1)
												RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+RVS.selLayers[i]+'.size.width.'+RVS.V.sizes[_screens]+'.v',val:"auto", uid:RVS.selLayers[i]});
										}
									}
								}
							}

							RVS.F.sortAllSelectedLayers({	layer:RVS.S.DaD.currentLayerId,
												target:RVS.S.DaD.target.columnType,
												env:RVS.S.DaD.target.columnID,
												dropto:RVS.S.DaD.target.columnType,
												resetPosition:rp
											});
						}
					} else {
						RVS.F.sortAllSelectedLayers({layer:RVS.S.DaD.currentLayerId, target:"columnend", env:RVS.S.DaD.target.columnID});
					}
				} else
				if (RVS.S.DaD.target.elementID!==undefined)
					if (RVS.S.DaD.target.elementBefore) {
						RVS.F.sortAllSelectedLayers({layer:RVS.S.DaD.currentLayerId, target:"before", env:RVS.S.DaD.target.elementID});
					} else {
						RVS.F.sortAllSelectedLayers({layer:RVS.S.DaD.currentLayerId, target:"after", env:RVS.S.DaD.target.elementID});
					}
			}
		} else {
			if (RVS.L[RVS.S.DaD.currentLayerId].linebreak) {
				RVS.F.showInfo({content:RVS_LANG.cantpulllinebreakoutside, type:"goodtoknow", showdelay:0, hidedelay:3, hideon:"", event:"" });
				return;
			}
			if ( RVS.L[RVS.S.DaD.currentLayerId].group.puid!==-1) {
				RVS.F.sortAllSelectedLayers({	layer:RVS.S.DaD.currentLayerId,
									target:"before",
									env:"top",
									dropto:"root",
									resetPosition:{
											x:RVS.S.DaD.startPos.x,
											y:RVS.S.DaD.startPos.y}
								});
			}

		}
	}

	// SHOW THE BORDER AND THE TOP/BOTTOM LINE OF
	function drawDroppingTarget() {

		var target = RVS.S.DaD.target !== undefined ? RVS.S.DaD.target.into : "free";

		if (target==="column") {
			if (RVS.S.DaD.target!==undefined) {
				RVS.C.layergrid[0].classList.remove('drop_in_root');
				if (RVS.S.DaD.target.rowID!==undefined && RVS.S.DaD.target.rowID!=="group") RVS.H[RVS.S.DaD.target.rowID].w[0].className+=" dont_blur drop_over_layer";
				if (RVS.S.DaD.target.columnID!==undefined) {
					RVS.H[RVS.S.DaD.target.columnID].w[0].className+=" dont_blur drop_over_layer";
					if ((RVS.SLIDER.settings.snap.adjust!=="none")) RVS.F.clearSnapVisual();
					if (RVS.S.DaD.target.elementID===undefined)
						if (RVS.S.DaD.target.columnTop)
							RVS.H[RVS.S.DaD.target.columnID].w[0].classList.add("drop_before_firstlayer");
						else
							RVS.H[RVS.S.DaD.target.columnID].w[0].classList.add("drop_after_lastlayer");
				}
				if (RVS.S.DaD.target.elementID!==undefined) {
					if (RVS.S.DaD.target.elementBefore)
						RVS.H[RVS.S.DaD.target.elementID].w[0].classList.add("drop_before_layer");
					else
						RVS.H[RVS.S.DaD.target.elementID].w[0].classList.add("drop_after_layer");
				}
			}

			if (RVS.S.DaD.target.columnType=="column" && RVS.S.DaD.showInMini==false) {
				RVS.S.DaD.showInMini = true;
				tpGS.gsap.to(RVS.S.DaD.uiHelper,0.1,{
															left:(RVS.S.mP.left-RVS.S.layer_grid_offset.left) / RVS.zoom,
															top:(RVS.S.mP.top-RVS.S.layer_grid_offset.top) / RVS.zoom,
															transformOrigin:"0 0",
															width:RVS.S.DaD.originalWidth,
															display:RVS.L[RVS.S.DaD.currentLayerId].idle.display,
															opacity:0.75,
															scale:0.6,
															x:0+RVS.S.DaD.scrolldiff.x,
															y:0+RVS.S.DaD.scrolldiff.y
														});
			}
		} else
		if (target==="free") {
			if (RVS.S.DaD.showInMini && RVS.S.DaD.showInMini==true)
				tpGS.gsap.to(RVS.S.DaD.uiHelper,0.1,{top:RVS.S.mP.top+RVS.S.rb_ScrollY, width:RVS.L[RVS.S.DaD.currentLayerId].size.width[RVS.screen].v, display:"block", left:RVS.S.mP.left+RVS.S.rb_ScrollX, scale:1, x:0-RVS.S.DaD.touchPosition.x+"px", y:0-RVS.S.DaD.touchPosition.y+"px"});
			RVS.S.DaD.showInMini = false;

			if (RVS.S.DaD.target!==undefined) {

				RVS.C.layergrid.addClass("drop_in_root");

				if (RVS.S.DaD.target.rowID!==undefined && RVS.S.DaD.target.rowID!==-1 && RVS.S.DaD.target.rowID!=="group")
						RVS.H[RVS.S.DaD.target.rowID].w[0].className= RVS.H[RVS.S.DaD.target.rowID].w[0].className.split("dont_blur").join("").split("drop_over_layer").join("");

				if (RVS.S.DaD.target.columnID!==undefined)
					RVS.H[RVS.S.DaD.target.columnID].w[0].className = RVS.H[RVS.S.DaD.target.columnID].w[0].className
																		.split("dont_blur").join("")
																		.split("drop_over_layer").join("")
																		.split("drop_before_firstlayer").join("")
																		.split("drop_after_lastlayer");
				if (RVS.S.DaD.target.elementID!==undefined)
					RVS.H[RVS.S.DaD.target.elementID].w[0].className = RVS.H[RVS.S.DaD.target.elementID].w[0].className
																		.split("drop_after_layer").join("")
																		.split("drop_before_layer").join("");

				if (RVS.S.DaD.lastRegisteredRow!==-1 && RVS.S.DaD.lastRegisteredRow!=="group" && RVS.S.DaD.lastRegisteredRow!==undefined)
					if (RVS.S.DaD.lastRegisteredRow!==undefined) RVS.H[RVS.S.DaD.lastRegisteredRow].w.className +=" dont_blur drop_over_layer";

			}
		}

	}

	function clearHCoors() {
		for (var i in RVS.H) if (RVS.H.hasOwnProperty(i)) delete RVS.H[i].coor;
	}
	/*
	INIT DRAG AND DROP FUNCTION OF LAYERS
	*/
	function initDragAndDrop() {
		// DRAG AND DROP LAYERS. FIND DROP ZONE BASED ON WHERE THE LAYER COMES FROM, AND WHERE THE LAYER GOES TO

		RVS.DOC.on('mousemove','#dropSensor',function(event) {
			if (RVS.S.DaD.dropSensor!==1) return;
			requestAnimationFrame(function() {
				if (RVS.S.rb_ScrollX!==window.scrollMem.x || RVS.S.rb_ScrollY!==window.scrollMem.y) clearHCoors();
				var wrap = jQuery('#dropSensor'),
					b = { x: (event.clientX-15), y: (event.clientY-65)},
					wo = wrap.offset(),currentZindex;
				RVS.S.DaD.dropTimer = 0;
				RVS.S.DaD.targetBefore = RVS.S.DaD.target;  // FOLLOW THE LAST ELEMENT HAS BEEN HOVERED TO DROP
				RVS.S.DaD.target = {};
				RVS.S.DaD.dropParentPos = {x:0, y:0};

				clearTimeout(RVS.S.DaD.timer);
				if (RVS.L[RVS.S.DaD.currentLayerId].type!=="group") {

					for (var i in RVS.H) {

						if(!RVS.H.hasOwnProperty(i)) continue;
						clearDragClasses(i)

						RVS.H[i].coor = RVS.H[i].coor===undefined ? { top: (RVS.H[i].w.offset().top-wo.top - RVS.S.rb_ScrollY),
								  left:(RVS.H[i].w.offset().left-wo.left - RVS.S.rb_ScrollX),
								  width:RVS.H[i].w.outerWidth(),
								  height: RVS.H[i].w.outerHeight()
								} : RVS.H[i].coor;
						RVS.H[i].coor.right = RVS.H[i].coor.left + (RVS.H[i].coor.width*RVS.zoom);
						RVS.H[i].coor.center = RVS.H[i].coor.left + (RVS.H[i].coor.width*RVS.zoom)/2;
						RVS.H[i].coor.bottom = RVS.H[i].coor.top + (RVS.H[i].coor.height*RVS.zoom);
						RVS.H[i].coor.middle = RVS.H[i].coor.top + (RVS.H[i].coor.height*RVS.zoom)/2;

						if (RVS.L[i]==undefined || RVS.L[i].visibility===undefined ||
							!RVS.L[i].visibility.visible || RVS.L[i].visibility.locked ||
							(RVS.L[i].group.puid!==undefined && RVS.L[i].group.puid!==-1 && RVS.L[RVS.L[i].group.puid]!==undefined && RVS.L[RVS.L[i].group.puid].visibility!==undefined &&
								(!RVS.L[RVS.L[i].group.puid].visibility.visible || RVS.L[RVS.L[i].group.puid].visibility.locked))) continue;



						if (b.x>=RVS.H[i].coor.left && b.x<=RVS.H[i].coor.right && b.y>=RVS.H[i].coor.top && b.y <=RVS.H[i].coor.bottom) {

							// CHECK IF LAYER IS IN THE COLUMN / ROW AND UNDERLAYING FULL ITEMS NOT WINNING

							currentZindex = RVS.S.DaD.lastGroupParent!==undefined && RVS.S.DaD.lastGroupParent === i ? 474 :
											RVS.S.DaD.target.groupID!==undefined && RVS.S.DaD.target.groupID!==-1 ? RVS.L[RVS.S.DaD.target.groupID].position.zIndex :
											RVS.S.DaD.target.puid!==-1 && RVS.S.DaD.target.puid!==undefined ? RVS.L[RVS.S.DaD.target.puid].position.zIndex
											: RVS.L[i].position.zIndex;

							if (RVS.S.DaD.target.zIndex!==undefined && RVS.S.DaD.target.zIndex>=currentZindex && (RVS.L[i].group.puid==-1 || RVS.L[i].group.puid==undefined)) continue;

							if (RVS.L[i].type==="column" && RVS.L[i].visibility.visible) {
								RVS.S.DaD.target.type = RVS.L[i].type;
								RVS.S.DaD.target.groupID = RVS.L[i].group.puid;
								RVS.S.DaD.target.rowID = RVS.L[i].group.puid;
							 	RVS.S.DaD.target.columnID = RVS.S.DaD.toContainerID = i;
							 	RVS.S.DaD.target.columnType = RVS.S.DaD.toContainerType = "column";
							 	RVS.S.DaD.target.columnTop = b.y<=RVS.H[i].coor.middle;
							 	RVS.S.DaD.target.zIndex = 	currentZindex;
							} else
							if (RVS.L[i].type==="group" && RVS.L[i].visibility.visible) {
								RVS.S.DaD.target.type = RVS.L[i].type;
								RVS.S.DaD.target.rowID = "group";
								RVS.S.DaD.target.groupID = RVS.L[i].group.puid;
								RVS.S.DaD.target.columnID = RVS.S.DaD.toContainerID = i;
								RVS.S.DaD.target.columnType = RVS.S.DaD.toContainerType = "group";
								RVS.S.DaD.target.columnTop = true;
								RVS.S.DaD.target.zIndex = 	currentZindex;
							} else
							if (RVS.L[i].type!=="row" && RVS.S.DaD.target.columnType!=="group" && i!=RVS.S.DaD.currentLayerId) {
								RVS.S.DaD.target.type = RVS.L[i].type;
								RVS.S.DaD.target.elementID = i;
								RVS.S.DaD.target.puid = RVS.L[i].group.puid;
								RVS.S.DaD.target.elementMiddle = RVS.H[i].coor.middle;
								RVS.S.DaD.target.elementBefore = b.y<=RVS.H[i].coor.middle;
								RVS.S.DaD.target.zIndex = 	currentZindex;
							}

						}
					}


					var gpcache = RVS.S.DaD.lastGroupParent;
					delete RVS.S.DaD.lastGroupParent;
					if (RVS.S.DaD.target.rowID==="group") {
						RVS.S.DaD.lastGroupParent = RVS.S.DaD.target.columnID;
						RVS.F.setZindex({id:RVS.S.DaD.lastGroupParent,o:474});
					}
					if (gpcache!==undefined && RVS.S.DaD.lastGroupParent!==gpcache) RVS.F.setZindex({id:gpcache});



					if (RVS.S.DaD.target!==undefined && RVS.S.DaD.target.rowID === "group") {
						RVS.S.DaD.dropParentPos.x = RVS.H[RVS.S.DaD.target.columnID].w_offset.left;
						RVS.S.DaD.dropParentPos.y = RVS.H[RVS.S.DaD.target.columnID].w_offset.top;
					}


					//Check the Closest Layer in Column if Hovering on Column but not on Layer
					if (RVS.S.DaD.targetBefore!==undefined && RVS.S.DaD.target.elementID===undefined && RVS.S.DaD.target.columnID!==undefined && RVS.S.DaD.target.columnID===RVS.S.DaD.targetBefore.columnID) {

						var lInColumns = RVS.F.getLayerChildren({layerid:RVS.S.DaD.target.columnID}),
							in_same_vrange = [],
							big_bottom = 0,
							smallest_dist = 10000;

						for (var i in lInColumns) {
							if(!lInColumns.hasOwnProperty(i)) continue;
							if (i!=RVS.S.DaD.currentLayerId) {
								if (RVS.H[i].coor.bottom<b.y || RVS.H[i].coor.middle<=b.y && RVS.H[i].coor.bottom>=b.y) {
									in_same_vrange.push(i);
									big_bottom = big_bottom<RVS.H[i].coor.bottom ? RVS.H[i].coor.bottom : big_bottom;
								}
							}
						}
						for (var k in in_same_vrange) {
							if(!in_same_vrange.hasOwnProperty(k)) continue;
							var i = in_same_vrange[k];
							if (big_bottom<=RVS.H[i].coor.bottom) {
								if (smallest_dist>Math.abs(RVS.H[i].coor.center-b.x)) {
									smallest_dist = Math.abs(RVS.H[i].coor.center-b.x);
									RVS.S.DaD.target.elementID = i;
									RVS.S.DaD.target.elementBefore = false;
								}
							}
						}
					} else
					//CHECK IF THE HOVERED LAYER IS REALLY IN A COLUMN IF COLUMN ALSO HOVERED
					if (RVS.S.DaD.target.elementID!==undefined && RVS.S.DaD.target.columnID!==undefined)
						if (RVS.S.DaD.target.puid==-1) RVS.S.DaD.target.elementID = undefined;

				} else for (var i in RVS.H) if(RVS.H.hasOwnProperty(i)) clearDragClasses(i);

				if (RVS.S.DaD.target!==undefined && RVS.S.DaD.lastRegisteredRow===RVS.S.DaD.target.rowID) {
					RVS.S.DaD.target.into = "column";
					RVS.S.DaD.toContainerID = RVS.S.DaD.target.columnID;
					RVS.S.DaD.toContainerType = RVS.S.DaD.target.columnType;
					clearTimeout(RVS.S.DaD.timerLeaveRow);
					RVS.S.DaD.timerLeaveRowStarted = false;

					drawDroppingTarget();
				} else {
					RVS.S.DaD.toContainerType = "root";
					RVS.S.DaD.target.into = "free";
					RVS.S.DaD.toContainerID = -1;
					drawDroppingTarget();
				}


				// REGISTER ON NEW ROW IF WE STAY 0.6 SEC OVER A COLUMN
				if (RVS.S.DaD.target!==undefined && RVS.S.DaD.lastRegisteredRow!==RVS.S.DaD.target.rowID && RVS.S.DaD.target.rowID!==undefined)
					RVS.S.DaD.timer = setTimeout(function() {

						RVS.S.DaD.lastRegisteredRow = RVS.S.DaD.target.rowID;
						if (RVS.S.DaD.target!==undefined && RVS.S.DaD.lastRegisteredRow===RVS.S.DaD.target.rowID) {
							if (RVS.S.DaD.lastRegisteredRowBefore && RVS.S.DaD.lastRegisteredRowBefore!==-1 && RVS.S.DaD.lastRegisteredRowBefore!=="group")
								RVS.H[RVS.S.DaD.lastRegisteredRowBefore].w[0].className = RVS.H[RVS.S.DaD.lastRegisteredRowBefore].w[0].className.split("dont_blur").join("").split("drop_over_layer").join("");

							clearTimeout(RVS.S.DaD.timerLeaveRow);
							RVS.S.DaD.timerLeaveRowStarted = false;
							RVS.S.DaD.target.into = "column";
							RVS.S.DaD.toContainerID = RVS.S.DaD.target.columnID;
							RVS.S.DaD.toContainerType = RVS.S.DaD.target.columnType;
							drawDroppingTarget();
						} else {
							RVS.S.DaD.target.into = "free";
							RVS.S.DaD.toContainerType = "root";
							RVS.S.DaD.toContainerID = -1;
							drawDroppingTarget();
						}
						RVS.S.DaD.lastRegisteredRowBefore = RVS.S.DaD.lastRegisteredRow;
					},250);

				if (RVS.S.DaD.timerLeaveRowStarted!==true && RVS.S.DaD.lastRegisteredRow!==-1 && RVS.S.DaD.target!==undefined && RVS.S.DaD.lastRegisteredRow!==RVS.S.DaD.target.rowID && RVS.S.DaD.target.rowID===undefined) {
					RVS.S.DaD.timerLeaveRowStarted = true;
					RVS.S.DaD.timerLeaveRow = setTimeout(function() {
						if (RVS.S.DaD.lastRegisteredRow!==-1 && RVS.S.DaD.lastRegisteredRow!=="group")
							RVS.H[RVS.S.DaD.lastRegisteredRow].w[0].className = RVS.H[RVS.S.DaD.lastRegisteredRow].w[0].className.split("dont_blur").join("").split("drop_over_layer").join("");
						RVS.S.DaD.lastRegisteredRow = -1;
						RVS.S.DaD.target.into = "free";
						RVS.S.DaD.toContainerType = "root";
						RVS.S.DaD.toContainerID = -1;
						RVS.S.DaD.timerLeaveRowStarted = false;
						drawDroppingTarget();
					},500);
				}
			});
		});

	}

	/*
	INIT THE MULTPILE LAYER SELECTOR BY DRAWING A RECTANGLE
	*/
	function initMultipleLayerSelector() {
		/* SELECT MORE THAN ONE LAYER BY DRAW */
		RVS.DOC.on('mousedown','#selectbydraw',function(event) {
			RVS.WIN.scrollTop(0);
			var wrap = jQuery('#selectbydraw');
			wrap.append('<div id="selectbydraw_box"></div>');
			RVS.F.updateContentDeltas();
			RVS.S.click.y = event.clientY+RVS.S.rb_ScrollY;
			RVS.S.click.x = event.clientX+RVS.S.rb_ScrollX;
			window.scrollCacheY = RVS.S.rb_ScrollY;
			window.scrollCacheX = RVS.S.rb_ScrollX;
			window.selectbydraw=1;
			RVS.F.selectLayers({overwrite:true});
		});


		RVS.DOC.on('mouseup','#selectbydraw',function(event) {
			jQuery('#selectbydraw').remove();
			jQuery('#select_by_cursor').trigger('click');
			RVS.F.selectLayers({});
			window.selectbydraw=0;
		});


		// MULTIPLE SELECT LAYERS BY DRAWING A BOX
		RVS.DOC.on('mousemove','#selectbydraw',function(event) {
			if (window.selectbydraw===1) {
				var wrap = jQuery('#selectbydraw');
				RVS.S.DaD.dragdelta.x =(event.clientX+(RVS.S.rb_ScrollX) - RVS.S.click.x);
				RVS.S.DaD.dragdelta.y =(event.clientY+(RVS.S.rb_ScrollY) - RVS.S.click.y);


				var newPos = {
								x:RVS.S.click.x, //-RVS.S.layer_wrap_offset.x,
								y:RVS.S.click.y //-RVS.S.layer_wrap_offset.y
							};

				newPos.x = RVS.S.DaD.dragdelta.x<0 ? newPos.x + (RVS.S.DaD.dragdelta.x) : newPos.x;
				newPos.y = RVS.S.DaD.dragdelta.y<0 ? newPos.y + (RVS.S.DaD.dragdelta.y) : newPos.y;

				//newPos.x = newPos.x -RVS.S.layer_grid_offset.left;
				//	newPos.y = newPos.y -RVS.S.layer_grid_offset.top;
				var b = { top:newPos.y, left:newPos.x, right:(newPos.x+Math.abs(RVS.S.DaD.dragdelta.x)), bottom:(newPos.y+Math.abs(RVS.S.DaD.dragdelta.y))},
					wo = wrap.offset();


				tpGS.gsap.set('#selectbydraw_box',{top:newPos.y, left:newPos.x, width:Math.abs(RVS.S.DaD.dragdelta.x), height:Math.abs(RVS.S.DaD.dragdelta.y)});


				for (var i in RVS.H) {
					if(!RVS.H.hasOwnProperty(i)) continue;
					var c = { top: (RVS.H[i].w.offset().top-wo.top),
									left:(RVS.H[i].w.offset().left-wo.left)};
					c.right = c.left + RVS.H[i].w.width();
					c.bottom = c.top + RVS.H[i].w.height();

					if (RVS.L[i].visibility.locked || !RVS.L[i].visibility.visible)
						RVS.F.selectLayers({id:i,overwrite:false, action:"remove", ignoreUpdate:"onlyhtml" , ignoreFieldUpdates:true, ignoreModeChange:true});
					else

					if (((c.left>b.left && c.left<b.right && c.top>b.top && c.top <b.bottom) ||
							(c.right>b.left && c.right<b.right && c.top>b.top && c.top <b.bottom) ||
							(c.left>b.left && c.left<b.right && c.bottom>b.top && c.bottom <b.bottom) ||
							(c.right>b.left && c.right<b.right && c.bottom>b.top && c.bottom <b.bottom) ||
							(b.left>c.left && b.left<c.right && b.top>c.top && b.top <c.bottom) ||
							(b.right>c.left && b.right<c.right && b.top>c.top && b.top <c.bottom) ||
							(b.left>c.left && b.left<c.right && b.bottom>c.top && b.bottom <c.bottom) ||
							(b.right>c.left && b.right<c.right && b.bottom>c.top && b.bottom <c.bottom) ||
							(b.top<c.top && b.bottom>c.bottom && b.left>c.left && b.right<c.right) ||
							(b.top>c.top && b.bottom<c.bottom && b.left<c.left && b.right>c.right)
						 ) && (
							!(b.left>c.left && b.right<c.right && b.top>c.top && b.bottom<c.bottom)
						 )) {
						RVS.F.selectLayers({id:i,overwrite:false, action:"add", ignoreUpdate:"onlyhtml", ignoreFieldUpdates:true, ignoreModeChange:true});
					} else {
						RVS.F.selectLayers({id:i,overwrite:false, action:"remove", ignoreUpdate:"onlyhtml" , ignoreFieldUpdates:true, ignoreModeChange:true});
					}
				}
			}
		});

	}

	/*
	FIGURE THE RIGHT COLUMN INPUT
	Using 5+4+6 should create a 5/15+4/15+6/15
	Using 3 should crearte 1/3+1/3+1/3
	Using 1/5+3/5+1/5 shouuld stay as it is
	and all should be calculated and checked again 100%
	*/
	RVS.F.figureColumnSizes = function(obj) {
		if (obj.plain!==undefined) {
			obj.plain = RVS.F.sanitize_columns(obj.plain);
			obj.plain = obj.plain.length===0 || obj.plain===undefined || obj.plain==="" ? "1/2+1/2" : obj.plain;
			obj.cols = obj.temp = obj.plain.split("+");
		}


		obj.summ = 0;

		// FIGURE THE INPUT
		//VALUES ARE DECIMALS
		if (obj.plain.indexOf("/")===-1) {
			obj.cols = [];
			obj.plain = "";
			//Single DEC defined, we need to create a full
			if (obj.temp.length==1) {
				var interval = parseInt(obj.temp[0],0);
				interval = interval>9 ? "9" : interval;
				for (var i=0; i<interval; i++) {
					if (obj.plain.length>0) obj.plain += "+";
					obj.plain += "1/"+interval;
					obj.cols.push("1/"+interval);
				}
			} else {
				//Multiple DEC defined
				var full = 0;
				obj.plain = "";
				for (var i=0;i<obj.temp.length;i++) {
					if (obj.temp[i]!=="" && obj.temp[i].length>0)
						full = full + parseInt(obj.temp[i],0);
				}
				for (var i=0;i<obj.temp.length;i++) {
					if (obj.temp[i]!=="" && obj.temp[i].length>0) {
						if (obj.plain.length>0) obj.plain += "+";
						obj.plain += obj.temp[i]+"/"+full;
						obj.cols.push(obj.temp[i]+"/"+full);
					}
				}
			}
		}

		var evals;
		for (var c in obj.cols) {
			if(!obj.cols.hasOwnProperty(c)) continue;
			evals = RVS.F.convertFraction(obj.cols[c]);
			obj.summ += (100*evals);
		}
		if (Math.round(obj.summ)!==100)
			jQuery('#row_column_structure').addClass("badvalue");
		else
			jQuery('#row_column_structure').removeClass("badvalue");
		return obj;
	};

	// FIX THE LAST COLUMN SIZE TO FILL GAP IN CASE COLUMN HAS BEEN DELETED
	RVS.F.fixColumnsInRows = function(_) {
		if (RVS.L[_.layerid]===undefined || RVS.L[_.layerid].type!=="row") return;
		var cols = RVS.F.getColumnsInRow(_),
			sum = 0,
			colsizes = [],
			evals;

		for (var c in cols) {
			if(!cols.hasOwnProperty(c)) continue;
			evals = RVS.F.convertFraction(RVS.L[cols[c]].group.columnSize);
			sum = sum += (100*evals);
			colsizes.push(RVS.L[cols[c]].group.columnSize.split("/"));
		}

		var a = cols.length;
		sum = sum===99.99999999999999 ? 100 : sum;


		//ROW IS NOT FILLED WITH COLUMNS 100%

		if (sum<100) {
			var dif = 100 - sum,
				uid = cols[cols.length-1],
				cs = RVS.L[uid].group.columnSize,
				cn = cs.split("/");

				evals = RVS.F.convertFraction(cs);

				var v = (100*evals) / parseInt(cn[0],0),
					addon = dif / v,
					newcs = parseInt(cn[0],0) + parseInt(addon,0) + "/" + cn[1],
					pre = RVS.S.slideId+".layers."+uid+".group.columnSize";

				if (a===1)
					RVS.F.updateSliderObj({path:pre,val:"1/1"});
				else
					RVS.F.updateSliderObj({path:pre,val:newcs});
				RVS.F.drawHTMLLayer({uid:uid});
		}

		// COLUMNS SIZES SUM BIGGER THAN 100%
		if (sum>100) {


			//Update Multiplicators
			for (var c in cols) {
				if(!cols.hasOwnProperty(c)) continue;
				colsizes[c][0] = 1;
				colsizes[c][1] = a;
			}

			for (var c in cols) {
				if(!cols.hasOwnProperty(c)) continue;
				var uid = cols[c],
					pre = RVS.S.slideId+".layers."+uid+".group.columnSize";
				RVS.F.updateSliderObj({path:pre, val:colsizes[c][0]+"/"+colsizes[c][1]});
				RVS.F.drawHTMLLayer({uid:uid});
			}
		}
	};





	/********************************
		LOCK / UNLOCK LAYERS
	*********************************/
	// UPDATE UNLOCK LIST HERE
	RVS.F.checkLockedLayers = function() {
		lockedLayers = lockedLayers===undefined ? {wrap:jQuery('#locked_layers_list'), switch:jQuery('#layer_lock_iconswitch')} : lockedLayers;
		lockedLayers.default = lockedLayers.default===undefined ? lockedLayers.wrap[0].innerHTML : lockedLayers.default;
		lockedLayers.layers = [];

		for (var lid in RVS.L) {
			if(!RVS.L.hasOwnProperty(lid)) continue;
			if (RVS.L[lid].visibility!==undefined && RVS.L[lid].visibility.locked && jQuery.inArray(lid,lockedLayers.layers)===-1) lockedLayers.layers.push(lid);
		}

		var ihtml = '';
		for (var i in lockedLayers.layers) {
			if(!lockedLayers.layers.hasOwnProperty(i)) continue;
			var uid = lockedLayers.layers[i];
			if (RVS.H[uid]===undefined || RVS.H[uid].w==undefined) continue;
			RVS.H[uid].w.addClass("_locked_");
			var el = document.getElementById('tllayerlist_element_'+RVS.S.slideId+'_'+uid);
			if (el.className.indexOf('_locked_')===-1) {
				el.className+=' _locked_';
				window.firstLockTest = window.firstLockTest===undefined ? "change" : window.firstLockTest;
			}
			ihtml += '<div data-uid="'+uid+'" class="unlock_single_layer lockstep"><i class="material-icons">lock_open</i>'+RVS.L[uid].alias+'</div>';
		}
		ihtml = lockedLayers.default + ihtml;
		lockedLayers.wrap[0].innerHTML = ihtml;
	};



	//TRIGGER LOCK/UNLOCK OF 1 SINGLE LAYER
	RVS.F.lockUnlockLayer = function(_) {
		_.val = _.val===undefined ? !RVS.L[_.uid].visibility.locked : _.val;
		RVS.F.updateSliderObj({path:RVS.S.slideId+".layers."+_.uid+"."+'visibility.locked',val:_.val});
		if (_.val===false && RVS.H[_.uid].w[0].className.indexOf("_locked_")>=0) {
			RVS.H[_.uid].w.removeClass("_locked_");
			var el = document.getElementById('tllayerlist_element_'+RVS.S.slideId+'_'+_.uid);
			el.className = el.className.replace(' _locked_','');
		}
	};

	//TRIGGER LOCK/UNLOCK ON SELECTED LAYERS
	RVS.F.lockUnlockLayers = function(_) {

		for (var lid in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(lid)) continue;
			var lock = _!==undefined && _.val!==undefined ? _.val : !RVS.L[RVS.selLayers[lid]].visibility.locked;
			RVS.F.lockUnlockLayer({uid:RVS.selLayers[lid], val:lock});
		}
		RVS.F.checkLockedLayers();
	};

	/********************************
		SHOW / HIDE LAYERS
	*********************************/
	// UPDATE UNLOCK LIST HERE
	RVS.F.checkShowHideLayers = function() {
		visibleLayers = visibleLayers===undefined ? {wrap:jQuery('#unvisible_layers_list'), switch:jQuery('#layer_visibility_iconswitch')} : visibleLayers;
		visibleLayers.default = visibleLayers.default===undefined ? visibleLayers.wrap[0].innerHTML : visibleLayers.default;
		visibleLayers.layers = [];

		for (var lid in RVS.L) {
			if(!RVS.L.hasOwnProperty(lid)) continue;
			if (RVS.L[lid].visibility!==undefined && RVS.L[lid].visibility.visible===false && jQuery.inArray(lid,visibleLayers.layers)===-1) visibleLayers.layers.push(lid);
		}

		var ihtml = "";
		for (var i in visibleLayers.layers) {
			if(!visibleLayers.layers.hasOwnProperty(i)) continue;
			var uid = visibleLayers.layers[i];
			if (RVS.H[uid]===undefined || RVS.H[uid].w==undefined) continue;
			RVS.H[uid].w.addClass("_unvisible_");
			var el = document.getElementById('tllayerlist_element_'+RVS.S.slideId+'_'+uid);
			if (el.className.indexOf('_unvisible_')===-1) {
				el.className+=' _unvisible_';
				window.firstLockTest = window.firstLockTest===undefined ? "change" : window.firstLockTest;
			}
			ihtml += '<div data-uid="'+uid+'" class="visible_single_layer visiblestep"><i class="material-icons">visibility</i>'+RVS.L[uid].alias+'</div>';
		}

		ihtml = visibleLayers.default + ihtml;
		visibleLayers.wrap[0].innerHTML = ihtml;
	};

	//TRIGGER SHOW/HIDE OF 1 SINGLE LAYER
	RVS.F.showHideLayer = function(_) {
		if (_.val===undefined) _.val = !RVS.L[_.uid].visibility.visible;
		RVS.F.updateSliderObj({ignoreBackup:_.ignoreBackup, path:RVS.S.slideId+".layers."+_.uid+"."+'visibility.visible',val:_.val});
		if (_.val===true && RVS.H[_.uid].w[0].className.indexOf("_unvisible_")>=0) {
			RVS.H[_.uid].w.removeClass("_unvisible_");
			var el = document.getElementById('tllayerlist_element_'+RVS.S.slideId+'_'+_.uid);
			el.className = el.className.replace(' _unvisible_','');

		}
	};

	//TRIGGER SHOW/HIDE ON SELECTED LAYERS
	RVS.F.showHideLayers = function(_) {
		for (var lid in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(lid)) continue;
			var vis = _!==undefined && _.val!==undefined ? _.val : !RVS.L[RVS.selLayers[lid]].visibility.visible;
			RVS.F.showHideLayer({uid:RVS.selLayers[lid], val:vis});
		}
		RVS.F.checkShowHideLayers();
	};

	// UPDATE COLUMN BREAKPOINTS AND SYNC WITH PARRENT ELEMENT
	RVS.F.updateColumnBreaksChildren = function() {
		for (var i in RVS.L) {
			if(!RVS.L.hasOwnProperty(i)) continue;
			if (RVS.L[i].type==="column")
				RVS.L[i].group.columnbreakat = RVS.L[RVS.L[i].group.puid].group.columnbreakat;
		}
	};


	/*******************************+/
	// INTELLIGENT SIZE PROCESSES //
	/*******************************/
	RVS.F.isIntelligentInherited = function(uid,slide) {
		return (slide!==undefined ? RVS.SLIDER[slide].layers[uid]!==undefined ? RVS.SLIDER[slide].layers[uid].behavior.intelligentInherit : false : RVS.L[uid]!==undefined ? RVS.L[uid].behavior.intelligentInherit : false);
	};

	//ENABLE THE OPTION INTELLIGENT INHERIT && SET VALUES AUTO CALCULATED
	RVS.F.setToIntelligentUpdate = function(reset) {

		RVS.F.updateScreenShrinks();
		var txt = reset ? "Reset All values to Intelligent Values" : "Enable Intelligent Inherit";
		RVS.F.openBackupGroup({id:"IntelligentInherit",txt:txt,icon:"important_devices",lastkey:"layer"});
		for (var li in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(li)) continue;
			for (var r in respAttrs) {
				if(!respAttrs.hasOwnProperty(r)) continue;
				RVS.F.intelligentUpdate({calcShrink:false,key:respAttrs[r], index:"v", uid:RVS.selLayers[li], backup:true, notEdited:reset, reset:reset});
			}
			for (var i=0;i<4;i++) {
				RVS.F.intelligentUpdate({calcShrink:false,key:"padding", index:i, uid:RVS.selLayers[li], backup:true, notEdited:reset, reset:reset});
				RVS.F.intelligentUpdate({calcShrink:false,key:"margin", index:i, uid:RVS.selLayers[li], backup:true, notEdited:reset, reset:reset});
			}
			RVS.F.updateSliderObj({ignoreResponsive:true, path:RVS.S.slideId+'.layers.'+RVS.selLayers[li]+'.behavior.intelligentInherit',val:true});
		}
		RVS.F.closeBackupGroup({id:"IntelligentInherit"});
		RVS.DOC.trigger('sliderSizeChanged');
	};


	RVS.F.intelligentUpdateValuesOnLayer = function(uid) {
		var reset = true;
		if (!RVS.L[uid].behavior.intelligentInherit) return;
		for (var r in respAttrs) {
			if(!respAttrs.hasOwnProperty(r)) continue;
			RVS.F.intelligentUpdate({calcShrink:false,key:respAttrs[r], index:"v", uid:uid, backup:false, notEdited:reset, reset:reset});
		}
		for (var i=0;i<4;i++) {
			RVS.F.intelligentUpdate({calcShrink:false,key:"padding", index:i, uid:uid, backup:false, notEdited:reset, reset:reset});
			RVS.F.intelligentUpdate({calcShrink:false,key:"margin", index:i, uid:uid, backup:false, notEdited:reset, reset:reset});
		}
	};

	// UPDATE VALUES BASED ON THE LATEST SCREEN SIZES / RULES
	RVS.F.updateAllInheritedSize = function() {
		RVS.S.updatingAllInheritedSizeCounter = RVS.S.updatingAllInheritedSizeCounter===undefined ? 0 : RVS.S.updatingAllInheritedSizeCounter;
		if (RVS.S.updatingAllInheritedSize!==true && RVS.S.updatingAllInheritedSizeCounter>0) {
			RVS.S.updatingAllInheritedSize=true;
			requestAnimationFrame(function() {
				RVS.F.updateScreenShrinks();
				for (var li in RVS.L) {
					if(!RVS.L.hasOwnProperty(li)) continue;
					if (RVS.L[li].type!=="zone" && RVS.L[li].behavior.intelligentInherit) {
						for (var r in respAttrs) {
							if(!respAttrs.hasOwnProperty(r)) continue;
							RVS.F.intelligentUpdate({calcShrink:false,key:respAttrs[r], index:"v", uid:RVS.L[li].uid});
						}
						for (var i=0;i<4;i++) {
							RVS.F.intelligentUpdate({calcShrink:false,key:"padding", index:i, uid:RVS.L[li].uid});
							RVS.F.intelligentUpdate({calcShrink:false,key:"margin", index:i, uid:RVS.L[li].uid});
						}

						//RVS.F.drawHTMLLayer({uid:RVS.L[li].uid});
					}
				}
				RVS.F.drawHTMLLayers();
				RVS.F.closeBackupGroup({id:"IntelligentInherit"});
				RVS.S.updatingAllInheritedSize = false;
			});
		}
		RVS.S.updatingAllInheritedSizeCounter++;
	};

	//DISABLE THE OPTION INTELLIGENT INHERIT
	RVS.F.disableIntelligentUpdate = function() {
		RVS.F.openBackupGroup({id:"DIntelligentInherit",txt:"Disable Intelligent Inherit",icon:"important_devices",lastkey:"layer"});
		for (var li in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(li)) continue;
			RVS.F.updateSliderObj({ignoreResponsive:true, path:RVS.S.slideId+'.layers.'+RVS.selLayers[li]+'.behavior.intelligentInherit',val:false});
		}
		RVS.F.closeBackupGroup({id:"DIntelligentInherit"});
	};

	// RESET ALL SIZE VALUES TO DESKTOP
	RVS.F.resetLayersDeviceSizesToDesktop = function() {
		RVS.F.openBackupGroup({id:"desktopValueReset",txt:"Reset Values to Dekstop",icon:"important_devices",lastkey:"layer"});
		for (var li in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(li)) continue;
			for (var r in respAttrs) {
				if(!respAttrs.hasOwnProperty(r)) continue;
				RVS.F.intelligentUpdate({calcShrink:false,key:respAttrs[r],  index:"v", uid:RVS.selLayers[li], backup:true, allToOne:true, notEdited:true});
			}
			for (var i=0;i<4;i++) {
				RVS.F.intelligentUpdate({calcShrink:false,key:"padding",  index:i, uid:RVS.selLayers[li], backup:true, allToOne:true, notEdited:true});
				RVS.F.intelligentUpdate({calcShrink:false,key:"margin",  index:i, uid:RVS.selLayers[li], backup:true, allToOne:true, notEdited:true});
			}
		}
		RVS.F.closeBackupGroup({id:"desktopValueReset"});
		for (var lid in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(lid)) continue;
			RVS.F.drawHTMLLayer({uid:RVS.selLayers[lid]});
		}
	};

	// RESET ALL TO NONE EDITED, AND RESHRINK VALUES
	RVS.F.resetIntelligentInherits = function() {
		RVS.F.setToIntelligentUpdate(true);
	};

	// INTERNAL FUNCTION TO CALCULATE NEW VALUES
	RVS.F.iUHelp = function(_) {
		var v = _.l[RVS.V.sizes[0]].v,
			mu = 1;

		for (var i=0;i<=3;i++) {
			var s = !RVS.F.isNumeric(v) ? v.indexOf("%")>=0 ? "%" :  "px" : "",
				tmp = v==="inherit" || (!RVS.F.isNumeric(v) && (v.indexOf("{")>=0 || v.indexOf("[")>=0 || jQuery.inArray(v,["top","left","bottom","right","center","middle"])>=0 || (v[0]=='#' && v[2]=="/" && v[4]=='#')));
			mu = _.iii ? RVS.S.shrink[RVS.V.sizes[i]] : mu;

			if (!_.allToOne)
				v =  _.reset || !_.l[RVS.V.sizes[i]].e ?
					_.shrink ?  tmp || v==="auto" || (!RVS.F.isNumeric(v) && v.indexOf("%")>=0) ? v : parseInt(v,0) * mu  : v 	// IF SHRINK, CHECK IF AUTO & VALUE EXISTS. IF YES, DONT SHRINK
					: _.number && !tmp && v!== "auto" ? parseInt(_.l[RVS.V.sizes[i]].v,0) : _.l[RVS.V.sizes[i]].v;		// IF NOT INDEXED, CHECK ONLY IF NMBER OR NOT

			var wv = _.allToOne ? v : !_.reset && _.l[RVS.V.sizes[i]].e ?  _.l[RVS.V.sizes[i]].v :  _.number ? v==="auto" || tmp  ? v :_.minValue!==undefined ? Math.max(_.minValue,Math.round(parseInt(v,0))) : Math.round(parseInt(v,0)) : v;
			s = wv==="auto" || !RVS.F.isNumeric(wv) ? "" : s;

			if (_.backup) {
				RVS.F.updateSliderObj({ignoreResponsive:true, path:RVS.S.slideId+'.layers.'+_.p+RVS.V.sizes[i]+'.v',val:wv+s});
				if (_.notEdited) RVS.F.updateSliderObj({ignoreResponsive:true, path:RVS.S.slideId+'.layers.'+_.p+RVS.V.sizes[i]+'.e',val:false});
			} else {
				_.l[RVS.V.sizes[i]].v = wv+s;
				if (_.notEdited) _.l[RVS.V.sizes[i]].e = false;
			}
			v=wv+s;


		}
	}

	// INTELLIGENT UPDATE OF VALUES ON DIFFERENT DEVICES && RESET VALUES ON DEMAND && RESET EDITED  ON DEMAND
	RVS.F.intelligentUpdate = function(_) {
		if (_.calcShrink) RVS.F.updateScreenShrinks();
		if (RVS.S.respInfoBar.visible) RVS.F.showFieldResponsiveValues();
		_.iii = _.iii === undefined ? true : _.iii;
		var L = _.slide!==undefined ? RVS.SLIDER[_.slide].layers : RVS.L;
		switch (_.key) {
			case "viewPortArea":
				_.l = RVS.SLIDER.settings.general.slideshow.viewPortArea;
				_.minValue = -1500;
				_.useSuffix = true;
				_.number = true;
				_.shrink = true;
				RVS.F.iUHelp(_);
			break;
			case "horizontal":
			case "vertical":
			case "y":
			case "x":
				_.l = L[_.uid].position[_.key];
				_.p = _.uid+'.position.'+_.key+'.';
				if (_.key==="x" || _.key==="y") {
					_.number = true;
					_.shrink = true;
					RVS.F.iUHelp(_);
					// Frames
					for (var fi in L[_.uid].timeline.frames) {

						if(!L[_.uid].timeline.frames.hasOwnProperty(fi)) continue;

						_.l = L[_.uid].timeline.frames[fi].transform[_.key];
						_.p = _.uid+'.timeline.frames.'+fi+'.transform.'+_.key+'.';
						RVS.F.iUHelp(_);
						_.l = L[_.uid].timeline.frames[fi].mask[_.key];
						_.p = _.uid+'.timeline.frames.'+fi+'.mask.'+_.key+'.';
						RVS.F.iUHelp(_);

						_.l = L[_.uid].timeline.frames[fi].chars[_.key];
						_.p = _.uid+'.timeline.frames.'+fi+'.chars.'+_.key+'.';
						RVS.F.iUHelp(_);

						_.l = L[_.uid].timeline.frames[fi].words[_.key];
						_.p = _.uid+'.timeline.frames.'+fi+'.words.'+_.key+'.';
						RVS.F.iUHelp(_);

						_.l = L[_.uid].timeline.frames[fi].lines[_.key];
						_.p = _.uid+'.timeline.frames.'+fi+'.lines.'+_.key+'.';
						RVS.F.iUHelp(_);
					}
				} else {
					RVS.F.iUHelp(_);
				}


			break;
			case "width":
			case "height":
				_.l = L[_.uid].size[_.key];
				_.p = _.uid+'.size.'+_.key+'.';
				_.minValue = 1;
				_.useSuffix = true;
				_.number = true;
				_.shrink = true;
				RVS.F.iUHelp(_);
			break;
			case "blur":
			case "spread":
			case "hoffset":
			case "voffset":
				_.l = L[_.uid].idle.boxShadow[_.key];
				_.p = _.uid+'.idle.boxShadow.'+_.key+'.';
				_.useSuffix = true;
				_.number = true;
				_.shrink = true;
				RVS.F.iUHelp(_);
				if (_.key !=="spread") {
					_.l = L[_.uid].idle.textShadow[_.key];
					_.p = _.uid+'.idle.textShadow.'+_.key+'.';
					_.useSuffix = true;
					_.number = true;
					_.shrink = true;
					RVS.F.iUHelp(_);
				}
			break;


			case "margin":
			case "padding":
				if (_.index==="v") {
					for (_.index=0;_.index<4;_.index++) {
						_.val = L[_.uid].idle[_.key][RVS.V.sizes[0]].v[_.index];
						var v = parseInt(_.val,0),
							mu = 1;
						for (var i=0;i<=3;i++) {
							mu = _.iii ? RVS.S.shrink[RVS.V.sizes[i]] : mu;
							v = _.allToOne ? v :  _.reset || !L[_.uid].idle[_.key][RVS.V.sizes[i]].e ? Math.round(v * mu) : parseInt(L[_.uid].idle[_.key][RVS.V.sizes[i]].v[_.index],0);
							if (_.backup) {
								RVS.F.updateSliderObj({ignoreResponsive:true, path:RVS.S.slideId+'.layers.'+_.uid+'.idle.'+_.key+'.'+RVS.V.sizes[i]+'.v.'+_.index,val:v});
								if (_.notEdited) RVS.F.updateSliderObj({ignoreResponsive:true, path:RVS.S.slideId+'.layers.'+_.uid+'.idle.'+_.key+'.'+RVS.V.sizes[i]+'.e',val:false});
							} else {
								L[_.uid].idle[_.key][RVS.V.sizes[i]].v[_.index] = v;
								if (_.notEdited) L[_.uid].idle[_.key][RVS.V.sizes[i]].e = false;
							}
						}
					}
				} else {
					_.val = L[_.uid].idle[_.key][RVS.V.sizes[0]].v[_.index];
					var v = parseInt(_.val,0),
						mu = 1;
					for (var i=0;i<=3;i++) {
						mu = _.iii ? RVS.S.shrink[RVS.V.sizes[i]] : mu;
						v = _.allToOne ? v :  _.reset || !L[_.uid].idle[_.key][RVS.V.sizes[i]].e ? Math.round(v * mu) : parseInt(L[_.uid].idle[_.key][RVS.V.sizes[i]].v[_.index],0);
						if (_.backup) {
							RVS.F.updateSliderObj({ignoreResponsive:true, path:RVS.S.slideId+'.layers.'+_.uid+'.idle.'+_.key+'.'+RVS.V.sizes[i]+'.v.'+_.index,val:v});
							if (_.notEdited) RVS.F.updateSliderObj({ignoreResponsive:true, path:RVS.S.slideId+'.layers.'+_.uid+'.idle.'+_.key+'.'+RVS.V.sizes[i]+'.e',val:false});
						} else {
							L[_.uid].idle[_.key][RVS.V.sizes[i]].v[_.index] = v;
							if (_.notEdited) L[_.uid].idle[_.key][RVS.V.sizes[i]].e = false;
						}
					}
				}
			break;
			case "textAlign":
			case "fontWeight":
			case "borderStyle":
			case "color":

				_.l = _.key==="color" && L[_.uid].type==="svg" ? L[_.uid].idle.svg.color : L[_.uid].idle[_.key];
				_.p = _.key==="color" && L[_.uid].type==="svg" ? _.uid+'.idle.svg.color.' : _.uid+'.idle.'+_.key+'.';


				RVS.F.iUHelp(_);
			break;

			case "lineHeight":
			case "letterSpacing":
			case "fontSize":
				_.l = L[_.uid].idle[_.key];

				_.p = _.uid+'.idle.'+_.key+'.';
				_.number = true;
				_.shrink = true;

				if (_.key==="fontSize") _.minValue =4;
				if (_.key==="lineHeight") _.minValue =6;
				RVS.F.iUHelp(_);
			break;
			case "whiteSpace":
				_.p = _.uid+'.idle.whiteSpace.';
				_.l = L[_.uid].idle[_.key];
				RVS.F.iUHelp(_);
			break;
		}
	};

	/*
	UPDATE GROUP LOCKS
	*/
	RVS.F.updateGroupLocks = function() {
		for (var i in RVS.L) {
			if(!RVS.L.hasOwnProperty(i)) continue;
			if (RVS.L[i].group && RVS.L[i].type==="group" && RVS.H[i]!==undefined) {
			  if (RVS.L[i].group.locked)
				RVS.H[i].w.addClass('_group_locked_');
			  else
			  	RVS.H[i].w.removeClass('_group_locked_');
			}
		}
	};

	/********************************
	INIT LOCAL INPUT BOX FUNCTIONS
	*********************************/
	function initLocalInputBoxes() {

		// ReOrder the Layers and set the Correct zIndex Table
		RVS.DOC.on('addImportedLayers',function(e,a) {
		});

		// COPY HOVER SETTINGS ON ELEMENT IF NEEDED
		RVS.DOC.on('copyhoversettings',function(e,a) {
			if (RVS.selLayers.length===0) return;
			if (a!==undefined && a==="checkiffirst" && RVS.L[RVS.selLayers[0]].hover.copied===true) return;
			for (var li in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(li)) continue;
				var l = RVS.L[RVS.selLayers[li]];
				l.hover.copied = true;
				l.hover.backgroundColor = l.idle.backgroundColor;
				l.hover.borderColor = l.idle.borderColor;
				l.hover.color = l.idle.color[RVS.screen].v;
				l.hover.borderRadius = RVS.F.safeExtend(true,{},l.idle.borderRadius);
				l.hover.borderStyle = l.idle.borderStyle[RVS.screen].v;
				l.hover.borderWidth = l.idle.borderWidth.map(x=>x);
				RVS.F.drawHTMLLayer({uid:RVS.selLayers[li]});
			}
			RVS.F.updateEasyInputs({container:jQuery('#form_layer_hover'), path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});

		});

		// CREATE LINEBREAK IN COLUMNS
		RVS.DOC.on('click','.add_linebreak',function() {
			var list=new Array();
			RVS.F.openBackupGroup({id:"addLineBreak",txt:"Add LineBreak",icon:"add",lastkey:"layer"});
			for (var i in RVS.selLayers) if(RVS.selLayers.hasOwnProperty(i)) list.push(RVS.selLayers[i])

			for (i in list) {
				if(!list.hasOwnProperty(i)) continue;
				if (RVS.L[list[i]].group.puid!==-1 && RVS.L[RVS.L[list[i]].group.puid].type==="column") {
					var newID = RVS.F.addLayer({type:"linebreak",forceSelect:false, subtype:this.dataset.subtype, libevent:this.dataset.libevent, libfilters:this.dataset.libfilters});
					//firstadded = firstadded===undefined ? newID : firstadded;
					RVS.F.intelligentUpdateValuesOnLayer(newID);
					RVS.F.sortLayer({layer:""+newID, target:this.dataset.pos, env:""+list[i]});
				}
			}
			//RVS.F.selectLayers({id:firstadded,overwrite:true, action:"add"});
			RVS.F.closeBackupGroup({id:"addLineBreak",txt:"Add LineBreak",icon:"add",lastkey:"layer"});
		});

		//SORT ROW LAYERS
		RVS.DOC.on('updateRowPosition',function(a,param) {
			if (param==undefined || param.val===undefined) return;
			for (var i in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(i)) continue;
				if (RVS.L[RVS.selLayers[i]].type==="row") {
					if (RVS.L[RVS.selLayers[i]].group.puid!==param.val) RVS.F.sortLayer({layer:RVS.selLayers[i], target:"zone",env:param.val});
				} else
				if (RVS.L[RVS.selLayers[i]].type==="column") {
					var rpuid = RVS.L[RVS.selLayers[i]].group.puid;
					if (RVS.L[rpuid].group.puid!==param.val) RVS.F.sortLayer({layer:rpuid, target:"zone",env:param.val});
				}
			}
		});

		// IMPORT LAYERS
		RVS.DOC.on('click','#import_layers',function() {
			RVS.F.openObjectLibrary({types:["modules"],filter:"all", selected:["modules"], context:"editor", depth:"layers", updatelist:false, staticalso:true, success:{layers:"addImportedLayers"}});
			return false;
		});

		RVS.DOC.on('click','#add_from_layerlibrary', function() {
			RVS.F.openObjectLibrary({types:["layers"],filter:"all", selected:["layers"], context:"editor", depth:"grouplayers", success:{layers:"addLayerLibrary"}});
			return false;
		});

		// CHANGE COL STRUCTURE -> UPDATE THE FIELD
		RVS.DOC.on('click','.colselector',function() {
			jQuery('#row_column_structure').val(this.dataset.col).change();
		});

		// TOGGLE GROUP LOCKS
		RVS.DOC.on('click','._group_lock_toggle_',function() {
			RVS.L[this.dataset.uid].group.locked = RVS.L[this.dataset.uid].group.locked===undefined ? true : !RVS.L[this.dataset.uid].group.locked;
			RVS.F.updateGroupLocks();
		});

		// UPDATED COL STRUCTURE FIELD -> SELECT CORRECR COL SELECTOR (IF EXISTS)
		RVS.DOC.on('update blur change','#row_column_structure',function() {
			if (this.value===undefined || this.value=="") return;
			var c = this.value = RVS.F.figureColumnSizes({plain:this.value}).plain;
			jQuery('#colselector_wrap .colselector').each(function() {
				if (c == RVS.F.sanitize_columns(this.dataset.col))
					this.className="colselector selected";
				else
					this.className="colselector";
			});
		});

		// ADD META TO THE CURRENT LAYER
		RVS.DOC.on('addMetaToLayer',function(e,param) {
			window.metatarget = param.eventparam;
			RVS.F.RSDialog.create({modalid:'rbm_layer_metas', bgopacity:0.5});
			jQuery('#rbm_layer_metas .rbm_content').RSScroll({
				wheelPropagation:false,
				suppressScrollX:true
			});
		});

		// CLICK ON ACCORDION, SHOUD OPEN/CLOSE THE META DATAS ACCORDIONS
		RVS.DOC.on('click','.mdl_group_header',function() {
			var group = jQuery(this).closest('.mdl_group');
			group.toggleClass("closed");
		});

		RVS.DOC.on('click', '#rbm_layer_metas .rbm_close',function() {
			RVS.F.RSDialog.close();
		});

		RVS.DOC.on('click','.mdl_group_member',function() {
			if (metatarget==="layer") {
				var insertAt = jQuery('#ta_layertext')[0].selectionStart;
				RVS.F.openBackupGroup({id:"insertMeta",txt:"Insert Meta Data",icon:"note_add"});
				for (var lid in RVS.selLayers) {
					if(!RVS.selLayers.hasOwnProperty(lid)) continue;
					var uid = RVS.selLayers[lid],
						pre = RVS.S.slideId+".layers."+uid+".text",
						front = RVS.L[uid].text.substring(0, insertAt),
						back = RVS.L[uid].text.substring(insertAt, RVS.L[uid].text.length);
					RVS.F.updateSliderObj({path:pre,val:front + this.dataset.val + back});
					RVS.F.redrawTextLayerInnerHTML(uid);
					RVS.F.drawHTMLLayer({uid:uid});
				}
				RVS.F.closeBackupGroup({id:"insertMeta"});
				RVS.F.updateLayerInputFields();
			} else {
				var inp = jQuery(window.metatarget),
					insertAt = inp[0].selectionStart,
					nval = inp.val().substring(0,insertAt) + this.dataset.val + inp.val().substring(insertAt,inp.val().length);
				inp.val(nval).change();
				RVS.F.RSDialog.close();

			}

		});


		RVS.DOC.on('checkforaudiolayer',RVS.F.checkForAudioLayer);


		RVS.DOC.on('click','.add_layer',function(e,ep) {
			if (this.id==="import_layers" || this.id==="add_from_layerlibrary") return;
			var newID = RVS.F.addLayer({type:this.dataset.type,forceSelect:true, subtype:this.dataset.subtype, libevent:this.dataset.libevent, libfilters:this.dataset.libfilters, extensiongroup:this.dataset.extensiongroup, subsubtype:this.dataset.subsubtype});

			RVS.F.intelligentUpdateValuesOnLayer(newID);
			RVS.F.selectLayers({id:newID,overwrite:true, action:"add"});
		});

		// DELETE LAYER ON CLICK
		RVS.DOC.on('click','#do_delete_layer',function(p) {
			RVS.DOC.trigger("do_delete_layer");
		});


		// SELECT ALL LAYERS DUE LAYER LIST
		RVS.DOC.on('click','.all_layer_selector', function() {
			RVS.DOC.trigger("do_select_all_layer");
			return false;
		});

		// DUPLICATE LAYER ON CLICK
		RVS.DOC.on('click','#do_duplicate_layer',function() {
			RVS.DOC.trigger("do_duplicate_layer");
		});

		// COPY LAYERS ON CLICK
		RVS.DOC.on('click','#do_copy_layer',function() {
			RVS.DOC.trigger("do_copy_layer");
		});

		// PASTE LAYERS ON CLICK
		RVS.DOC.on('click','#do_paste_layer',function() {
			RVS.DOC.trigger("do_paste_layer");
		});


		//MOVE ZIndex in the Background of Layer
		RVS.DOC.on('mouseenter','#do_background_layer, #do_foreground_layer',function() {
			var uid = RVS.selLayers[0];
			if (RVS.H[uid]!==undefined) tpGS.gsap.set(RVS.H[uid].w,{zIndex:RVS.L[uid].position.zIndex});
		});
		RVS.DOC.on('mouseleave','#do_background_layer, #do_foreground_layer',function() {
			RVS.F.updateSelectedHtmlLayers();
		});

		RVS.DOC.on('click','#do_background_layer', function() {
			var uid = RVS.selLayers[0],
				tid = RVS.F.getLayerAfterZIndex(uid);
			switch(RVS.L[uid].type) {
				case "row":
					if (tid!==undefined)
						RVS.F.sortLayer({layer:uid, target:"after", env:tid,redraw:true});
					else
					if (RVS.L[uid].group.puid==="top")
						RVS.F.sortLayer({layer:uid, target:"zone", env:"middle",redraw:true});
					else
					if (RVS.L[uid].group.puid==="middle")
						RVS.F.sortLayer({layer:uid, target:"zone", env:"bottom",redraw:true});

				break;
				case "column":
					if (tid!==undefined)
						RVS.F.sortLayer({layer:uid, target:"after", env:tid,redraw:true});
				break;
				default:
					if (jQuery.inArray(tid,["top","bottom","middle"])===-1 && RVS.L[uid].group!==undefined && RVS.L[uid].group.puid!==-1 && RVS.L[RVS.L[uid].group.puid].type==="column") {
						tid = RVS.F.getPrevNextLayerInOrder(uid,"next");
						if (tid!==uid)
							RVS.F.sortLayer({layer:uid, target:"after",env:tid, redraw:true});
					} else
					if (tid!==undefined) {
						tid = tid==="top" || tid==="middle" ? "bottom" : tid;
						RVS.F.sortLayer({layer:uid, target:"after", env:tid,redraw:true});
					}
				break;
			}
		});

		//MOVE ZIndex in the Foreground of Layer
		RVS.DOC.on('click','#do_foreground_layer', function() {
			var uid = RVS.selLayers[0],
				tid = RVS.F.getLayerBeforeZIndex(uid);
			switch(RVS.L[uid].type) {
				case "row":
					if (tid!==undefined)
						RVS.F.sortLayer({layer:uid, target:"before", env:tid,redraw:true});
					else
					if (RVS.L[uid].group.puid==="bottom")
						RVS.F.sortLayer({layer:uid, target:"zonebottom", env:"middle",redraw:true});
					else
					if (RVS.L[uid].group.puid==="middle")
						RVS.F.sortLayer({layer:uid, target:"zonebottom", env:"top",redraw:true});
				break;
				case "column":
					if (tid!==undefined)
						RVS.F.sortLayer({layer:uid, target:"before", env:tid,redraw:true});
				break;
				default:
					if (jQuery.inArray(tid,["top","bottom","middle"])===-1 && RVS.L[uid].group!==undefined && RVS.L[uid].group.puid!==-1 && RVS.L[RVS.L[uid].group.puid].type==="column") {
						tid = RVS.F.getPrevNextLayerInOrder(uid,"prev");
						if (tid!==uid)
							RVS.F.sortLayer({layer:uid, target:"before",env:tid, redraw:true});

					} else
					if (tid!==undefined) {
						tid = tid==="bottom" || tid==="middle" ? "top" : tid;
						RVS.F.sortLayer({layer:uid, target:"before", env:tid,redraw:true});
					}

				break;
			}
		});

		// KEYBOARD LISTENERS FOR LAYERS
		RVS.DOC.on('keydown',function(e) {
			window.shiftdown = e.shiftKey;
			window.altdown = e.altKey;
			var noreturn = false,
				multip = window.shiftdown ? 10 : 1;
			if ((RVS.S.OSName==="MacOS" && e.metaKey && !e.ctrlKey) || e.ctrlKey) window.cmdctrldown=true;
			if (RVS.S.builderHover && (RVS.S.inFocus==="none" || RVS.S.inFocus===undefined)) {

				if (window.shiftdown && !(RVS.eMode.top==="layer" && RVS.eMode.menu==="#form_layer_animation")) RVS.DOC.trigger('squareselection');
				if (window.cmdctrldown && !(RVS.eMode.top==="layer" && RVS.eMode.menu==="#form_layer_animation")) RVS.DOC.trigger('cursorselectionadd');
				if ((RVS.S.OSName==="MacOS" && e.metaKey && !e.ctrlKey) || e.ctrlKey)
					switch (e.keyCode) {
						case 65: //a
							RVS.DOC.trigger("do_select_all_layer");
						return false;

						case 67: // c
							RVS.DOC.trigger("do_copy_layer");
						break;

						case 86: // v
							RVS.DOC.trigger("do_paste_layer");
						break;

						case 74: // j
							if (window.altdown!==true) RVS.DOC.trigger("do_duplicate_layer");
						break;

						case 79: //o
							var radiobtn = document.getElementById("magnet_fr_sticky_inh");
							radiobtn.checked = true;
							noreturn = true;
						break;

						case 73: //i
							var radiobtn = document.getElementById("magnet_fr_sticky");
							radiobtn.checked = true;
							noreturn = true;
						break;

						case 85: //u
							var radiobtn = document.getElementById("magnet_fr_none");
							radiobtn.checked = true;
							noreturn = true;
						break;

					}
				switch (e.keyCode) {
					case 8: RVS.DOC.trigger("do_delete_layer");return false;
					case 46: RVS.DOC.trigger("do_delete_layer");return false;
					case 37: RVS.F.moveLayerByKeys({x:-1*multip,y:0}); return false;
					case 39: RVS.F.moveLayerByKeys({x:1*multip,y:0}); return false;
					case 38: RVS.F.moveLayerByKeys({x:0,y:-1*multip});return false;
					case 40: RVS.F.moveLayerByKeys({x:0,y:1*multip}); return false;
				}
			}
			if (noreturn) {
				 e.preventDefault();
				 return false;
			}
		});

		RVS.DOC.on('keyup',function(e) {
			window.shiftdown = e.shiftKey;
			window.altdown = e.altKey;
			window.cmdctrldown = ((RVS.S.OSName==="MacOS" && e.metaKey && !e.ctrlKey) || e.ctrlKey);

			if (!window.shiftdown && !window.cmdctrldown) RVS.DOC.trigger('cursorselection');

			if (window.moveByKeyboard) {
				RVS.F.closeBackupGroup({id:"LayerPosition"});
				window.moveByKeyboard = false;
			}
		});




		/*HIDE, SHOW LAYER GROUPS */
		RVS.DOC.on('click','.ui_free_layers, .ui_top_row_layers',function() {
			var f = jQuery(this);
			f.toggleClass("selected");
			if (f.hasClass("selected")) {
				jQuery('#rev_builder_inner').removeClass(this.dataset.realref);
				jQuery('#timeline').removeClass(this.dataset.ref);
			} else {
				jQuery('#rev_builder_inner').addClass(this.dataset.realref);
				jQuery('#timeline').addClass(this.dataset.ref);
			}
		});

		/*SELECT LAYER ON CLICK ON LAYER*/
		RVS.DOC.on('click dblclick','._lc_',function(e) {
			if (RVS.S.inFocus!==undefined && RVS.S.inFocus!=="none" && RVS.S.inFocus.history &&  RVS.S.inFocus.value && RVS.S.inFocus.history!=RVS.S.inFocus.value) jQuery(RVS.S.inFocus).trigger("change");
			if (e.type==="click" && RVS.S.clickedLayer === this.dataset.uid && (RVS.S.clickOnLayerTimer!==undefined && e.timeStamp-RVS.S.clickOnLayerTimer <800)) return false;
			RVS.S.clickOnLayerTimer = e.timeStamp;
			RVS.S.clickedLayer = this.dataset.uid;
			RVS.F.selectLayers({id:this.dataset.uid,overwrite:!window.RS_sel_and_add && !window.cmdctrldown, action:"add", ignoreUpdate:false});
			//Open Closest Group
			if (RVS.L[this.dataset.uid].group.puid !== -1 && RVS.L[this.dataset.uid].type!=="row") {
				var puid = RVS.L[this.dataset.uid].group.puid;
				jQuery('#tllayerlist_element_'+RVS.S.slideId+'_'+puid).removeClass("collapsed");
				if (RVS.L[puid].group.puid !== -1 && RVS.L[puid].type!=="row")
					jQuery('#tllayerlist_element_'+RVS.S.slideId+'_'+RVS.L[puid].group.puid).removeClass("collapsed");
				RVS.F.saveCollapsedGroups();
			}

			if (e.type==="dblclick") {
				RVS.F.openSettings({forms:["*slidelayout**mode__slidecontent*#form_layer_content"],uncollapse:true});
				switch (RVS.L[this.dataset.uid].type) {
					case "text":
					case "button":
						jQuery('#ta_layertext').trigger('focus');
					break;
					case "image":
						if (RVS.L[this.dataset.uid].media.imageLib==="objectlibrary")
							jQuery('#image_layer_object_library_button').trigger('click');
						else
							jQuery('#image_layer_media_library_button').trigger('click');
					break;
					case "svg":
						RVS.F.openObjectLibrary({types:["fonticons","svgs","svgcustom"],filter:"all", selected:["svgs"], success:{icon:"insertContentFromOL"}});
					break;
				}
			}

			// Scroll in Position
			if (RVS.selLayers.length===1) RVS.F.layerListScrollable('scrollToSelected');
			RVS.DOC.trigger('layerselectioncomplete', [this]);



			return false;
		});

		/*SELECT LAYER ON CLICK ON LAYER*/
		RVS.DOC.on('dblclick','._lc_',function() {
			RVS.F.selectLayers({id:this.dataset.uid,overwrite:!window.RS_sel_and_add, action:"add", ignoreUpdate:false});
			return false;
		});



		RVS.DOC.on('mouseenter','.layerlist_element',function() {
			if (jQuery.inArray(this.dataset.id,["top","bottom","middle"])==-1) RVS.H[this.dataset.id].w.addClass("hoveredinlist");
		});
		RVS.DOC.on('mouseleave','.layerlist_element',function() {
			if (jQuery.inArray(this.dataset.id,["top","bottom","middle"])==-1) RVS.H[this.dataset.id].w.removeClass("hoveredinlist");
		});

		RVS.DOC.on('click','#unlock_all_layer',function() {
			for (var i in RVS.L) if (RVS.L.hasOwnProperty(i)) {
				if (RVS.L[i].visibility!==undefined && RVS.L[i].visibility.locked) {
					RVS.F.updateSliderObj({path:RVS.S.slideId+".layers."+i+"."+'visibility.locked',val:false});
					RVS.H[i].w.removeClass("_locked_");
					var el = document.getElementById('tllayerlist_element_'+RVS.S.slideId+'_'+i);
					el.className = el.className.replace(' _locked_','');
				}
			}
			RVS.F.checkLockedLayers();
		});

		RVS.DOC.on('click','.unlock_single_layerm',function() {
			RVS.F.lockUnlockLayer({uid:this.dataset.uid, val:false});
			RVS.F.checkLockedLayers();
		});

		RVS.DOC.on('click','.layer_current_locked',function() {
			RVS.F.lockUnlockLayer({uid:this.dataset.uid});
			RVS.F.checkLockedLayers();
		});


		RVS.DOC.on('click','#visible_all_layer',function() {
			for (var i in RVS.L) {
				if(!RVS.L.hasOwnProperty(i)) continue;
				if (RVS.L[i].visibility!==undefined && RVS.L[i].visibility.visible===false) {
					RVS.F.updateSliderObj({path:RVS.S.slideId+".layers."+i+"."+'visibility.visible',val:true});
					RVS.H[i].w.removeClass("_unvisible_");
					var el = document.getElementById('tllayerlist_element_'+RVS.S.slideId+'_'+i);
					el.className = el.className.replace(' _unvisible_','');

				}
			}
			RVS.F.checkShowHideLayers();
		});


		/* TOGGLE SELECTED LAYER LOCKS */
		RVS.DOC.on('click','#toggle_lock_layer',function() {
			RVS.F.lockUnlockLayers();
		});

		/* TOGGLE SELECTED LAYER VISIBILITY */
		RVS.DOC.on('click','#toggle_visible_layer',function() {
			RVS.F.showHideLayers({uid:this.dataset.uid});
		});

		/* SHOW / HIDE MARKING BOXES */
		RVS.DOC.on('click','#hide_highlight_boxes',function() {
			jQuery(this).toggleClass("selected");
			RVS.C.theEditor.toggleClass("nohiglightboxes");
		});


		RVS.DOC.on('click','.visible_single_layer',function() {
			RVS.F.showHideLayer({uid:this.dataset.uid, val:true});
			RVS.F.checkShowHideLayers();
			return false;
		});

		RVS.DOC.on('click',' .layer_current_visibility',function() {
			RVS.F.showHideLayer({uid:this.dataset.uid});
			RVS.F.checkShowHideLayers();
			return false;
		});

		RVS.DOC.on('mouseenter','.visible_single_layer',function(e) {RVS.H[this.dataset.uid].w.addClass("hoveredinlist");});
		RVS.DOC.on('mouseleave','.visible_single_layer',function(e) {RVS.H[this.dataset.uid].w.removeClass("hoveredinlist");});

		RVS.DOC.on('mouseenter','.unlock_single_layer',function(e) {RVS.H[this.dataset.uid].w.addClass("hoveredinlist");});
		RVS.DOC.on('mouseleave','.unlock_single_layer',function(e) {RVS.H[this.dataset.uid].w.removeClass("hoveredinlist");});

	}

	/*
	SELECT, DESELECT TOOLKIT
	*/
	function selectToolkit(_) {
		var a = jQuery(_.id);
		a.addClass("selected");
		jQuery(_.remove).removeClass("selected");
		jQuery('#toolkit_selector_ph_icon').html(a[0].dataset.toolkiticon);
		jQuery('#toolkit_selector_ph_icon_sub').html(a[0].dataset.toolkiticonsub);
		if (_.multi && jQuery('#selectbydraw').length===0)
			jQuery('#rev_slider_ul').append('<div id="selectbydraw"></div>');
		else
		if (!_.multi)
			jQuery('#selectbydraw').remove();
		window.RS_sel_and_add = _.add;
	}



	/*
	INIT CUSTOM EVENT LISTENERS FOR TRIGGERING FUNCTIONS
	*/
	function initLocalListeners() {


		//Update Layer Position on Event
		RVS.DOC.on('updateLayerPosition',function() {
			for (var si in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(si)) continue;
				var i = RVS.selLayers[si];
				RVS.F.updateHTMLLayerPosition({ uid:i, updateValues:false,lhCwidth:RVS.H[i].c.outerWidth(), lhCheight:RVS.H[i].c.outerHeight()});
			}
		});

		// Intelligent Sizing Function
		RVS.DOC.on('intelligentInheritUpdate',function(e,par) {
			if (par!==undefined)
				if(par.val===true)
					RVS.F.setToIntelligentUpdate();
				else
					RVS.F.disableIntelligentUpdate();
			for (var l in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(l)) continue;
				RVS.F.drawHTMLLayer({uid:RVS.selLayers[l]});
			}


		});

		RVS.DOC.on('inheritValuesFromDesktop',function(e,par) {
			RVS.F.resetLayersDeviceSizesToDesktop();
		});

		RVS.DOC.on('resetIntelligentInherits',function(e,par) {
			RVS.F.resetIntelligentInherits();
		});

		RVS.DOC.on('updateAllInheritedSize',function(e,par) {
			RVS.F.updateAllInheritedSize();
		});



		// REAL UPDATE OF COLUMN AND ROW STRUCTURES !!
		RVS.DOC.on('updateColumnStructure', function() {
			RVS.F.openBackupGroup({id:"RowStructure",txt:"Change Row(s) Structure",icon:"view_column"});
			var newstructue = RVS.F.figureColumnSizes({plain:jQuery('#row_column_structure').val()});

			for (var sl in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(sl)) continue;
				var uid = RVS.L[RVS.selLayers[sl]].type==="column" ? RVS.L[RVS.selLayers[sl]].group.puid : RVS.selLayers[sl];
				if (RVS.L[uid].type==="row") {

					var cols = RVS.F.getColumnsInRow({layerid:uid});
					// CHECK THE AMOUNT OF COLS IN NEW STRUCTURE


					if (cols.length>newstructue.cols.length) {
						var lastknown = cols[newstructue.cols.length-1];

						for (var i=cols.length-1;i>=newstructue.cols.length;i--) {
							RVS.F.deleteLayerfromLayers({layerid:cols[i], newpuid:lastknown});
						}
						//DELETE COLS
					} else
					if (cols.length<newstructue.cols.length) {
						for (var i=cols.length;i<newstructue.cols.length;i++) {
							RVS.F.addLayerToLayers({type:"column", puid:uid, alias:"column"});
						}
					}

					cols = RVS.F.getColumnsInRow({layerid:uid});

					for (var i=0;i<cols.length;i++) {
						var pre = RVS.S.slideId+".layers."+cols[i]+".group.columnSize";

						RVS.F.updateSliderObj({path:pre,val:newstructue.cols[i]});
					}

				}
			}
			RVS.F.closeBackupGroup({id:"RowStructure"});
			RVS.F.buildLayerLists({force:true});
			RVS.F.reOrderHTMLLayers();

		});

		// UPDATE THE LAYER ALIAS DUE THE SINGLE INPUT FIELD FOMR TOOLBAR
		RVS.DOC.on('updateLayerAliasFromSingleInput',function(e,param) {
			if (param.layerid==undefined) return;
			document.getElementById('layerlist_element_alias_'+RVS.S.slideId+'_'+param.layerid).innerHTML = RVS.L[param.layerid].alias;
			document.getElementById('layerlist_element_alias_input_'+RVS.S.slideId+'_'+param.layerid).value = RVS.L[param.layerid].alias;
			if (RVS.L[param.layerid].type==="group") document.getElementById('_group_head_title_'+RVS.S.slideId+'_'+param.layerid).innerHTML = RVS.L[param.layerid].alias;
		});

		/* SELECT ALL LAYERS */
		RVS.DOC.on('do_select_all_layer', function() {
			for (var i in RVS.L) {
				if(!RVS.L.hasOwnProperty(i)) continue;
				RVS.F.selectLayers({id:i,overwrite:false, action:"add", ignoreUpdate:"onlyhtml", ignoreFieldUpdates:true, ignoreModeChange:true});
			}
			RVS.F.selectLayers({});

		});

		// DELETE SELECTED LAYERS
		RVS.DOC.on('do_delete_layer', function() {
			RVS.DOC.trigger('previewStopLayerAnimation');
			RVS.F.openBackupGroup({id:"removeLayer",txt:"Remove Multiple Layers",icon:"delete",lastkey:"layer"});
			var deletelayers = [],
				rows = [],
				rowid;

			//Collect Layers to Delete (First Rows, Columns and Layers without Parrent) & Record Depending Rows
			for (var sli in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(sli)) continue;
				var uid = RVS.selLayers[sli];

				if (RVS.L[uid].group.puid==-1) deletelayers.push(uid);
				else
				if (RVS.L[uid].type==="row" || RVS.L[uid].type==="column") {
					rowid = RVS.L[uid].type==="row" ? uid : RVS.L[uid].group.puid;
					if (jQuery.inArray(rowid,rows)===-1) rows.push(rowid);
					deletelayers.push(uid);
				}
			}

			for (var sli in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(sli)) continue;
				var uid = RVS.selLayers[sli];
				//Ignore Elements within other Elements
				if (RVS.L[uid].group.puid!==-1 && RVS.L[uid].type!=="column" && RVS.L[uid].type!=="row")   {
					var puid = RVS.L[RVS.L[uid].group.puid].type==="column" ? RVS.L[RVS.L[uid].group.puid].group.puid : RVS.L[uid].group.puid;
					if (jQuery.inArray(puid,deletelayers)===-1) deletelayers.push(uid);
				}
			}

			//NOW WE CAN REMOVE THE LAYERS
			for (var i in deletelayers) {
				if(!deletelayers.hasOwnProperty(i)) continue;
				RVS.F.deleteLayerfromLayers({layerid:deletelayers[i]});
			}



			//UPDATE ROWS (Extend, Remove Sizes)
			if (rows.length>0) {
				for (var i in rows) {
					if(!rows.hasOwnProperty(i)) continue;
					if (RVS.L[rows[i]]!==undefined) {
						//Check if Row has still any Elements !?
						if (RVS.F.getColumnsInRow({layerid:rows[i]}).length===0)
							RVS.F.deleteLayerfromLayers({layerid:rows[i]});
						else
							RVS.F.fixColumnsInRows({layerid:rows[i]});
					}
				}
			}

			RVS.F.closeBackupGroup({id:"removeLayer"});
			RVS.F.buildLayerLists({force:true});

		});

		/*Column Break has Been Changed ! */
		RVS.DOC.on('updateColumnBreak',function(e,p) {
			RVS.F.openBackupGroup({id:"ColumnBreak",txt:"Row Breakpoint",icon:"layers",lastkey:"layer"});
			var rows = [];
			for (var lid in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(lid)) continue;
				if  (RVS.L[RVS.selLayers[lid]].type==="row" || RVS.L[RVS.selLayers[lid]].type==="column") {
					/* var l = RVS.L[RVS.selLayers[lid]], */
					var rowid = RVS.L[RVS.selLayers[lid]].type==="column" ? RVS.L[RVS.selLayers[lid]].group.puid : RVS.selLayers[lid],
						pre = RVS.S.slideId+".layers."+rowid+".";
						RVS.F.updateSliderObj({path:pre+'group.columnbreakat',val:p.val});
						rows.push(rowid);
				}
			}
			RVS.F.closeBackupGroup({id:"ColumnBreak"});
			for (var r in rows) {
				if(!rows.hasOwnProperty(r)) continue;
				var cols = RVS.F.getColumnsInRow({layerid:rows[r]});
				for (var c in cols) {
					if(!cols.hasOwnProperty(c)) continue;
					RVS.L[cols[c]].group.columnbreakat = p.val;
					RVS.F.drawHTMLLayer({uid:cols[c]});
				}
				RVS.F.drawHTMLLayer({uid:rows[r]});
			}
		});


		/* INIT THE SELECT LAYERS PER DRAW*/
		RVS.DOC.on('squareselection',function() {
			selectToolkit({id:'#select_by_draw', remove:"#select_by_cursor, #select_by_cursor_add", add:false, multi:true});
		});

		/* INIT THE CURSOR SELECTIO*/
		RVS.DOC.on('cursorselection',function() {
			selectToolkit({id:'#select_by_cursor', remove:"#select_by_draw, #select_by_cursor_add", add:false, multi:false});
		});
		/* INIT THE CURSOR SELECTIO*/
		RVS.DOC.on('cursorselectionadd',function() {
			selectToolkit({id:'#select_by_cursor_add', remove:"#select_by_draw, #select_by_cursor", add:true,multi:false});
		});


		/* LOCK LAYER FROM EDIT */
		RVS.DOC.on('lockLayer',function(e,ds) {
			RVS.F.lockUnlockLayers(ds);
		});



		/* SHOW/HIDE LAYER FROM EDIT */
		RVS.DOC.on('showHideLayer',function(e,ds) {
			RVS.F.showHideLayers(ds);
		});


		/*UPDATE LAYER FRAME TO CUSTOM IF ANY VALUE CHANGED*/
		RVS.DOC.on('frameAnimToCustom',function(e,ds) {
		});

		/***** VIDEO FUNCTIONS */
		// Get VIMEO Thumb

		RVS.DOC.on('click','.getLayerImageFromVimeo',function() {
			jQuery.ajax({
		        type:'GET',
		        url: "//vimeo.com/api/v2/video/" + jQuery('#layer_youtubevimeo_id').val() + ".json",
		        success: function(data){
              jQuery('#layer_video_poster').val(data[0].thumbnail_large).trigger("change");
		        }
		    });
		});

		RVS.DOC.on('click','.getLayerImageFromYouTube',function() {
			var youtubeid = jQuery('#layer_youtubevimeo_id').val();
			jQuery('#layer_video_poster').val(location.protocol+"//img.youtube.com/vi/"+youtubeid+"/maxresdefault.jpg").trigger("change");
		});

		RVS.DOC.on('click','.removeLayerPoster',function() { // KRIKI, IMAGE, POSTER ID CHANGE BACK
			RVS.F.openBackupGroup({id:"VideoPosterChange",txt:"Change Video Poster",icon:"layers",lastkey:"layer"});
			jQuery('#layer_video_poster').val(RVS.ENV.img_ph_url).trigger("change");
			RVS.F.closeBackupGroup({id:"VideoPosterChange"});
		});

		RVS.DOC.on('click','.resetVideoArguments',function() {
			var type = jQuery('input[name="layer_video_type"]:checked').val();
			var newarg = type==="vimeo" ? RVS.ENV.vimeoargs: type==="youtube" ? RVS.ENV.youtubeargs : "";
			jQuery('#layer_video_arg').val(newarg).trigger("change");
		});

		// UPDATE VIDEO TYPE // VIDEO ID
		RVS.DOC.on('checkVideoID',function(e,par) {
			if (par!==undefined && par.val!==undefined) {
				RVS.F.openBackupGroup({id:"VideoTypeChange",txt:"Change Video Type",icon:"layers",lastkey:"layer"});
				for (var lid in RVS.selLayers) {
					if(!RVS.selLayers.hasOwnProperty(lid)) continue;
					_.uid = RVS.selLayers[lid];
					_.l = RVS.L[_.uid];
					_.pre = RVS.S.slideId+".layers."+_.uid+".";

					if (par.val==="vimeo") {
						RVS.F.updateSliderObj({path:_.pre+'media.args',val:RVS.ENV.vimeoargs});
						jQuery('#layer_video_arg').val(RVS.ENV.vimeoargs);
					} else
					if (par.val==="youtube") {
						RVS.F.updateSliderObj({path:_.pre+'media.args',val:RVS.ENV.youtubeargs});
						jQuery('#layer_video_arg').val(RVS.ENV.youtubeargs);
					}
					RVS.F.updateSliderObj({path:_.pre+'media.mediaType',val:par.val});
				}
				RVS.F.closeBackupGroup({id:"VideoTypeChange"});
			}


			var type = jQuery('input[name="layer_video_type"]:checked').val(),
				videoid = jQuery('#layer_youtubevimeo_id').val();

			if (type==="vimeo") RVS.F.checkVimeoID({id:videoid});
			if (type==="youtube") RVS.F.checkYouTubeID({id:videoid});
		});


		/* LAYER DUPLICATE LISTENER */
		RVS.DOC.on('do_duplicate_layer',function() {

			var duplicateLayers = [],
				duplicateLayersID = [],
				newLayerIDs = [],
				rows = [],
				rowid;

			for (var sli in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(sli)) continue;
				var uid = RVS.selLayers[sli];
				duplicateLayers.push({type:RVS.L[uid].type, duplicateId:uid, ignoreBackupGroup:true, ignoreLayerList:true , ignoreOrderHTMLLayers:true});
				duplicateLayersID.push(uid);
				if (RVS.L[uid].type==="column") {
					rowid = RVS.L[uid].type==="row" ? uid : RVS.L[uid].group.puid;
					if (jQuery.inArray(rowid,rows)===-1) rows.push(rowid);
				}
			}


			RVS.F.openBackupGroup({id:"addLayer",txt:"Duplicate Layer(s)",icon:"layers",lastkey:"layer"});

			// CHECK MULTPILE DUPLICATES, LIKE COLUMN IN ROW WHICH ALREADY IN DUPLICATE MODE. (Parrents Check)
			for (var i in duplicateLayers) {
				if(!duplicateLayers.hasOwnProperty(i)) continue;
				var puid = RVS.L[duplicateLayersID[i]].group.puid;
				if (puid===-1 || (jQuery.inArray(puid,duplicateLayersID)==-1)) {
					newLayerIDs.push(RVS.F.addLayer(duplicateLayers[i]));
				}
			}

			//UPDATE ROWS (Extend, Remove Sizes)
			if (rows.length>0) {
				for (var i in rows) {
					if(!rows.hasOwnProperty(i)) continue;
					if (RVS.L[rows[i]]!==undefined)
						RVS.F.fixColumnsInRows({layerid:rows[i]});
				}
			}

			RVS.F.buildLayerLists({force:true, ignoreRebuildHTML:true});
			RVS.F.reOrderHTMLLayers();
			for (var i in newLayerIDs) {
				if(!newLayerIDs.hasOwnProperty(i)) continue;
				RVS.F.selectLayers({id:newLayerIDs[i],overwrite:false, action:"add", ignoreUpdate:true, ignoreFieldUpdates:true});
			}

			RVS.F.selectedLayersVisualUpdate();
			RVS.F.updateSelectedHtmlLayers();
			RVS.F.updateZIndexTable();
			RVS.F.closeBackupGroup({id:"addLayer"});

		});

		/* LAYER COPY LISTENER */
		RVS.DOC.on('do_copy_layer',function() {
			window.copyPasteLayers = {amount:0, layers:{}};
			var copyLayers = [],
				copyLayersID = [];


			for (var sli in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(sli)) continue;
				var uid = RVS.selLayers[sli];
				copyLayers.push({type:RVS.L[uid].type, duplicateId:uid, ignoreBackupGroup:true, ignoreLayerList:true , ignoreOrderHTMLLayers:true, copyPaste:"copy"});
				copyLayersID.push(uid);
			}

			// CHECK MULTPILE DUPLICATES, LIKE COLUMN IN ROW WHICH ALREADY IN DUPLICATE MODE. (Parrents Check)
			for (var i in copyLayers) {
				if(!copyLayers.hasOwnProperty(i)) continue;
				var puid = RVS.L[copyLayersID[i]].group.puid;
				if (puid===-1 || (jQuery.inArray(puid,copyLayersID)==-1)) {
					RVS.F.addLayer(copyLayers[i]);
				}
			}
			jQuery('#do_paste_layer').removeClass("disabled");
		});

		/* LAYER PASTE LISTENER */
		RVS.DOC.on('do_paste_layer', function() {
			var pasteLayers = [],
				pasteLayersID = [],
				newLayerIDs = [],
				selectedRow = RVS.F.getFirstSelectedType("row"),
				selectedGroupColumn = RVS.F.getFirstSelectedType("column"),
				rows = [],
				rowid;

			//CACHE THE COPY PASTE STRUCTURE
			window.backupCopyPaste = RVS.F.safeExtend(true,{},window.copyPasteLayers.layers);

			//CHECK IF CURRENTLY ROWS, COLUMNS EXISTS TO INCLUDE THE ITEMS INTO
			selectedRow = selectedRow===false && selectedGroupColumn!==false ? RVS.L[selectedGroupColumn].group.puid : selectedRow;
			selectedGroupColumn = selectedGroupColumn===false ? RVS.F.getFirstSelectedType("group") : selectedGroupColumn;

			//CHECK COPIED COLUMNS WITHOUT PARRENT CONTAINERS
			for (var sli in window.copyPasteLayers.layers) {
				if(!window.copyPasteLayers.layers.hasOwnProperty(sli)) continue;
				var type = window.copyPasteLayers.layers[sli].type,
					puid = window.copyPasteLayers.layers[sli].group.puid;
				switch (type) {
					case "column":
						if (window.copyPasteLayers.layers[puid]===undefined || window.copyPasteLayers.layers[puid].type!=="row") {
							puid = selectedRow!==false ? selectedRow : RVS.F.addLayerToLayers({type:"row",alias:"row",buildHTMLLayer:false,copyPaste:"copy"});
							if(puid!==-1 && jQuery.inArray(puid,rows)===-1) rows.push(puid);
						}
					break;
					case "group":
					case "row":
					break;

					default:
						//Should have Parrent, But it is not Existing, Not Column and not Group
						if (puid>=0 && puid<=5000 && (window.copyPasteLayers.layers[puid]===undefined || (window.copyPasteLayers.layers[puid].type!=="group" && window.copyPasteLayers.layers[puid].type!=="column")))
							puid = selectedGroupColumn!==false ? selectedGroupColumn : -1;
						else
							puid = puid===-1 && selectedGroupColumn!==false ? selectedGroupColumn : puid;

						if (puid === selectedGroupColumn) {
							for (var device in RVS.V.sizes) {
								if(!RVS.V.sizes.hasOwnProperty(device)) continue;
								var s = RVS.V.sizes[device];
								window.copyPasteLayers.layers[sli].position.horizontal[s].v = "center";
								window.copyPasteLayers.layers[sli].position.vertical[s].v = "middle";
								window.copyPasteLayers.layers[sli].position.x[s].v = 0;
								window.copyPasteLayers.layers[sli].position.y[s].v = 0;
							}

						}
					break;
				}
				window.copyPasteLayers.layers[sli].group.puid = puid;
			}

			// Paste Elements
			for (var sli in window.copyPasteLayers.layers) {
				if(!window.copyPasteLayers.layers.hasOwnProperty(sli)) continue;
				var uid = window.copyPasteLayers.layers[sli].uid;
				pasteLayers.push({type:window.copyPasteLayers.layers[sli].type, duplicateId:uid, ignoreBackupGroup:true, ignoreLayerList:true , ignoreOrderHTMLLayers:true, copyPaste:"paste"});
				pasteLayersID.push(uid);
			}

			RVS.F.openBackupGroup({id:"addLayer",txt:"Paste Layer(s)",icon:"layers",lastkey:"layer"});
			// CHECK MULTPILE DUPLICATES, LIKE COLUMN IN ROW WHICH ALREADY IN DUPLICATE MODE. (Parrents Check)
			for (var i in pasteLayers) {
				if(!pasteLayers.hasOwnProperty(i)) continue;
				var puid = window.copyPasteLayers.layers[pasteLayersID[i]].group.puid;
				rowid = -1;
				if (puid===-1 || (jQuery.inArray(puid,pasteLayersID)==-1) || (pasteLayers[i].type==="column" && RVS.L[puid]!==undefined && RVS.L[puid].type==="row")) {
					var newid = RVS.F.addLayer(pasteLayers[i]);
					newLayerIDs.push(newid);
					rowid = RVS.L[newid].type==="row" ? newid : puid;
				}
				rowid = RVS.L[puid]!==undefined && RVS.L[puid].type==="row" ? puid : rowid;

				if(rowid!==-1 && jQuery.inArray(rowid,rows)===-1) rows.push(rowid);

			}

			//UPDATE ROWS (Extend, Remove Sizes)
			if (rows.length>0) {
				for (var i in rows) {
					if(!rows.hasOwnProperty(i)) continue;
					if (RVS.L[rows[i]]!==undefined) RVS.F.fixColumnsInRows({layerid:rows[i]});
				}
			}

			RVS.F.closeBackupGroup({id:"addLayer"});
			RVS.F.buildLayerLists({force:true, ignoreRebuildHTML:true});
			RVS.F.reOrderHTMLLayers();
			for (var i in newLayerIDs) {
				if(!newLayerIDs.hasOwnProperty(i)) continue;
				RVS.F.selectLayers({id:newLayerIDs[i],overwrite:false, action:"add", ignoreUpdate:true});
			}
			RVS.F.selectedLayersVisualUpdate();
			RVS.F.updateSelectedHtmlLayers();

			// RESTORE CACHE
			window.copyPasteLayers.layers = RVS.F.safeExtend({},window.backupCopyPaste);
		});


		/* ADD CONTENT BY OIBJECT LIBRARY EVENT */
		RVS.DOC.on('insertContentFromOL',function(e,par) {
			if (par.libraryType=="fonticons") {
				var cl = par.tags[0]==="MaterialIcons" ? "material-icons" : par.handle.replace(".",""),
					ih = par.tags[0]==="MaterialIcons" ? par.handle.replace(".","") : "";
				RVS.F.openBackupGroup({id:"PreStyleLAyer",txt:"Prestyle Object Layer",icon:"layers",lastkey:"layer"});
				for (var lid in RVS.selLayers) {
					if(!RVS.selLayers.hasOwnProperty(lid)) continue;
					RVS.F.updateLayerObj({path:"text",val:'<i class="'+cl+'">'+ih+'</i>',ignoreRedraw:true});
					RVS.F.updateLayerObj({path:"type",val:'text',ignoreRedraw:true});
					RVS.F.updateLayerObj({path:"idle.fontSize."+RVS.screen+".v",val:'50px',ignoreRedraw:true});
					RVS.F.updateLayerObj({path:"idle.lineHeight."+RVS.screen+".v",val:'50px',ignoreRedraw:true});
					RVS.H[RVS.selLayers[lid]].c[0].innerHTML = '<i class="'+cl+'">'+ih+'</i>';
					RVS.F.drawHTMLLayer({uid:RVS.selLayers[lid]});
				}
				RVS.F.closeBackupGroup({id:"PreStyleLAyer"});
			} else
			if (par.libraryType==="svgs" || par.libraryType==="svgcustom") {
				RVS.F.openBackupGroup({id:"PreStyleLAyer",txt:"Prestyle Object Layer",icon:"layers",lastkey:"layer"});
				var uids = [];
				for (var lid in RVS.selLayers) {
					if(!RVS.selLayers.hasOwnProperty(lid)) continue;
					uids.push(RVS.selLayers[lid]);
					if (par.libraryType==="svgcustom")
						RVS.F.updateLayerObj({path:"idle.svg.originalColor",val:true,ignoreRedraw:true});
					else
						RVS.F.updateLayerObj({path:"idle.svg.originalColor",val:false,ignoreRedraw:true});

					RVS.F.updateLayerObj({path:"svg.source",val:par.img,ignoreRedraw:true});
					RVS.F.updateLayerObj({path:"type",val:'svg',ignoreRedraw:true});
					RVS.F.updateLayerObj({path:"size.width."+RVS.screen+".v",val:'100px',ignoreRedraw:true});
					RVS.F.updateLayerObj({path:"size.height."+RVS.screen+".v",val:'100px',ignoreRedraw:true});
				}
				RVS.F.updateLayerSVGSrc({uids:uids, src:par.img});
				RVS.F.closeBackupGroup({id:"PreStyleLAyer"});
			}
			RVS.F.selectedLayersVisualUpdate();
			RVS.F.updateSelectedHtmlLayers();
			RVS.F.updateLayerInputFields();
 		});
	}

	/* UPDATE LAYER FRAME */
	RVS.DOC.on('updateLayerFrame',function(e,par) {
		if (par!==undefined) {
			for (var lid in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(lid)) continue;
				RVS.F.updateLayerFrame({layerid:parseInt(RVS.selLayers[lid],0), frame:RVS.S.keyFrame});
				RVS.F.updateFrameRealSpeed();
			}
		}
	});

	/*Column Break has Been Changed ! */
	RVS.DOC.on('updateLayerFrameStart',function(e,par) {
		if (par!==undefined || par.val!==undefined) {
			RVS.F.openBackupGroup({id:"LayerFrameStart",txt:"Layer Frame Start",icon:"layers",lastkey:"layer"});
			//Check if start is hitting frame before !?
			var lid = parseInt(RVS.selLayers[0],0),
				PN =  RVS.F.getPrevNextFrame({layerid:lid, frame:RVS.S.keyFrame});
				cur = parseInt(par.val,0),
			cur = cur<PN.prev.end ? PN.prev.end + 10 : cur+PN.cur.framelength>PN.next.start ? PN.next.start - (PN.cur.framelength + 10) : cur;
			cur = cur>RVS.F.getSlideLength()*10 ? RVS.F.getSlideLength()*10 : cur;
			RVS.F.updateLayerObj({path:"timeline.frames."+RVS.S.keyFrame+'.timeline.start', val:cur});

			RVS.F.closeBackupGroup({id:"LayerFrameStart"});
			document.getElementById('layerframestart').value = cur;
			for (var lid in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(lid)) continue;
				RVS.F.updateLayerFrame({layerid:parseInt(RVS.selLayers[lid],0), frame:RVS.S.keyFrame});
				RVS.F.updateFrameRealSpeed();
			}

		}

	});


})();






/*************************************
    - 	INTERNAL FUNCTIONS -
***************************************/


/*
 * Copyright 2015 Small Batch, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
/* Web Font Loader v1.5.18 - (c) Adobe Systems, Google. License: Apache 2.0 */
;(function(window,document,undefined){function aa(a,b,c){return a.call.apply(a.bind,arguments)}function ba(a,b,c){if(!a)throw Error();if(2<arguments.length){var d=Array.prototype.slice.call(arguments,2);return function(){var c=Array.prototype.slice.call(arguments);Array.prototype.unshift.apply(c,d);return a.apply(b,c)}}return function(){return a.apply(b,arguments)}}function k(a,b,c){k=Function.prototype.bind&&-1!=Function.prototype.bind.toString().indexOf("native code")?aa:ba;return k.apply(null,arguments)}var n=Date.now||function(){return+new Date};function q(a,b){this.K=a;this.w=b||a;this.G=this.w.document}q.prototype.createElement=function(a,b,c){a=this.G.createElement(a);if(b)for(var d in b)b.hasOwnProperty(d)&&("style"==d?a.style.cssText=b[d]:a.setAttribute(d,b[d]));c&&a.appendChild(this.G.createTextNode(c));return a};function r(a,b,c){a=a.G.getElementsByTagName(b)[0];a||(a=document.documentElement);a&&a.lastChild&&a.insertBefore(c,a.lastChild)}function ca(a,b){function c(){a.G.body?b():setTimeout(c,0)}c()}
function s(a,b,c){b=b||[];c=c||[];for(var d=a.className.split(/\s+/),e=0;e<b.length;e+=1){for(var f=!1,g=0;g<d.length;g+=1)if(b[e]===d[g]){f=!0;break}f||d.push(b[e])}b=[];for(e=0;e<d.length;e+=1){f=!1;for(g=0;g<c.length;g+=1)if(d[e]===c[g]){f=!0;break}f||b.push(d[e])}a.className=b.join(" ").replace(/\s+/g," ").replace(/^\s+|\s+$/,"")}function t(a,b){for(var c=a.className.split(/\s+/),d=0,e=c.length;d<e;d++)if(c[d]==b)return!0;return!1}
function u(a){if("string"===typeof a.na)return a.na;var b=a.w.location.protocol;"about:"==b&&(b=a.K.location.protocol);return"https:"==b?"https:":"http:"}function v(a,b){var c=a.createElement("link",{rel:"stylesheet",href:b,media:"all"}),d=!1;c.onload=function(){d||(d=!0)};c.onerror=function(){d||(d=!0)};r(a,"head",c)}
function w(a,b,c,d){var e=a.G.getElementsByTagName("head")[0];if(e){var f=a.createElement("script",{src:b}),g=!1;f.onload=f.onreadystatechange=function(){g||this.readyState&&"loaded"!=this.readyState&&"complete"!=this.readyState||(g=!0,c&&c(null),f.onload=f.onreadystatechange=null,"HEAD"==f.parentNode.tagName&&e.removeChild(f))};e.appendChild(f);window.setTimeout(function(){g||(g=!0,c&&c(Error("Script load timeout")))},d||5E3);return f}return null};function x(a,b){this.Y=a;this.ga=b};function y(a,b,c,d){this.c=null!=a?a:null;this.g=null!=b?b:null;this.D=null!=c?c:null;this.e=null!=d?d:null}var da=/^([0-9]+)(?:[\._-]([0-9]+))?(?:[\._-]([0-9]+))?(?:[\._+-]?(.*))?$/;y.prototype.compare=function(a){return this.c>a.c||this.c===a.c&&this.g>a.g||this.c===a.c&&this.g===a.g&&this.D>a.D?1:this.c<a.c||this.c===a.c&&this.g<a.g||this.c===a.c&&this.g===a.g&&this.D<a.D?-1:0};y.prototype.toString=function(){return[this.c,this.g||"",this.D||"",this.e||""].join("")};
function z(a){a=da.exec(a);var b=null,c=null,d=null,e=null;a&&(null!==a[1]&&a[1]&&(b=parseInt(a[1],10)),null!==a[2]&&a[2]&&(c=parseInt(a[2],10)),null!==a[3]&&a[3]&&(d=parseInt(a[3],10)),null!==a[4]&&a[4]&&(e=/^[0-9]+$/.test(a[4])?parseInt(a[4],10):a[4]));return new y(b,c,d,e)};function A(a,b,c,d,e,f,g,h){this.N=a;this.k=h}A.prototype.getName=function(){return this.N};function B(a){this.a=a}var ea=new A("Unknown",0,0,0,0,0,0,new x(!1,!1));
B.prototype.parse=function(){var a;if(-1!=this.a.indexOf("MSIE")||-1!=this.a.indexOf("Trident/")){a=C(this);var b=z(D(this)),c=null,d=E(this.a,/Trident\/([\d\w\.]+)/,1),c=-1!=this.a.indexOf("MSIE")?z(E(this.a,/MSIE ([\d\w\.]+)/,1)):z(E(this.a,/rv:([\d\w\.]+)/,1));""!=d&&z(d);a=new A("MSIE",0,0,0,0,0,0,new x("Windows"==a&&6<=c.c||"Windows Phone"==a&&8<=b.c,!1))}else if(-1!=this.a.indexOf("Opera"))a:if(a=z(E(this.a,/Presto\/([\d\w\.]+)/,1)),z(D(this)),null!==a.c||z(E(this.a,/rv:([^\)]+)/,1)),-1!=this.a.indexOf("Opera Mini/"))a=
z(E(this.a,/Opera Mini\/([\d\.]+)/,1)),a=new A("OperaMini",0,0,0,C(this),0,0,new x(!1,!1));else{if(-1!=this.a.indexOf("Version/")&&(a=z(E(this.a,/Version\/([\d\.]+)/,1)),null!==a.c)){a=new A("Opera",0,0,0,C(this),0,0,new x(10<=a.c,!1));break a}a=z(E(this.a,/Opera[\/ ]([\d\.]+)/,1));a=null!==a.c?new A("Opera",0,0,0,C(this),0,0,new x(10<=a.c,!1)):new A("Opera",0,0,0,C(this),0,0,new x(!1,!1))}else/OPR\/[\d.]+/.test(this.a)?a=F(this):/AppleWeb(K|k)it/.test(this.a)?a=F(this):-1!=this.a.indexOf("Gecko")?
(a="Unknown",b=new y,z(D(this)),b=!1,-1!=this.a.indexOf("Firefox")?(a="Firefox",b=z(E(this.a,/Firefox\/([\d\w\.]+)/,1)),b=3<=b.c&&5<=b.g):-1!=this.a.indexOf("Mozilla")&&(a="Mozilla"),c=z(E(this.a,/rv:([^\)]+)/,1)),b||(b=1<c.c||1==c.c&&9<c.g||1==c.c&&9==c.g&&2<=c.D),a=new A(a,0,0,0,C(this),0,0,new x(b,!1))):a=ea;return a};
function C(a){var b=E(a.a,/(iPod|iPad|iPhone|Android|Windows Phone|BB\d{2}|BlackBerry)/,1);if(""!=b)return/BB\d{2}/.test(b)&&(b="BlackBerry"),b;a=E(a.a,/(Linux|Mac_PowerPC|Macintosh|Windows|CrOS|PlayStation|CrKey)/,1);return""!=a?("Mac_PowerPC"==a?a="Macintosh":"PlayStation"==a&&(a="Linux"),a):"Unknown"}
function D(a){var b=E(a.a,/(OS X|Windows NT|Android) ([^;)]+)/,2);if(b||(b=E(a.a,/Windows Phone( OS)? ([^;)]+)/,2))||(b=E(a.a,/(iPhone )?OS ([\d_]+)/,2)))return b;if(b=E(a.a,/(?:Linux|CrOS|CrKey) ([^;)]+)/,1))for(var b=b.split(/\s/),c=0;c<b.length;c+=1)if(/^[\d\._]+$/.test(b[c]))return b[c];return(a=E(a.a,/(BB\d{2}|BlackBerry).*?Version\/([^\s]*)/,2))?a:"Unknown"}
function F(a){var b=C(a),c=z(D(a)),d=z(E(a.a,/AppleWeb(?:K|k)it\/([\d\.\+]+)/,1)),e="Unknown",f=new y,f="Unknown",g=!1;/OPR\/[\d.]+/.test(a.a)?e="Opera":-1!=a.a.indexOf("Chrome")||-1!=a.a.indexOf("CrMo")||-1!=a.a.indexOf("CriOS")?e="Chrome":/Silk\/\d/.test(a.a)?e="Silk":"BlackBerry"==b||"Android"==b?e="BuiltinBrowser":-1!=a.a.indexOf("PhantomJS")?e="PhantomJS":-1!=a.a.indexOf("Safari")?e="Safari":-1!=a.a.indexOf("AdobeAIR")?e="AdobeAIR":-1!=a.a.indexOf("PlayStation")&&(e="BuiltinBrowser");"BuiltinBrowser"==
e?f="Unknown":"Silk"==e?f=E(a.a,/Silk\/([\d\._]+)/,1):"Chrome"==e?f=E(a.a,/(Chrome|CrMo|CriOS)\/([\d\.]+)/,2):-1!=a.a.indexOf("Version/")?f=E(a.a,/Version\/([\d\.\w]+)/,1):"AdobeAIR"==e?f=E(a.a,/AdobeAIR\/([\d\.]+)/,1):"Opera"==e?f=E(a.a,/OPR\/([\d.]+)/,1):"PhantomJS"==e&&(f=E(a.a,/PhantomJS\/([\d.]+)/,1));f=z(f);g="AdobeAIR"==e?2<f.c||2==f.c&&5<=f.g:"BlackBerry"==b?10<=c.c:"Android"==b?2<c.c||2==c.c&&1<c.g:526<=d.c||525<=d.c&&13<=d.g;return new A(e,0,0,0,0,0,0,new x(g,536>d.c||536==d.c&&11>d.g))}
function E(a,b,c){return(a=a.match(b))&&a[c]?a[c]:""};function G(a){this.ma=a||"-"}G.prototype.e=function(a){for(var b=[],c=0;c<arguments.length;c++)b.push(arguments[c].replace(/[\W_]+/g,"").toLowerCase());return b.join(this.ma)};function H(a,b){this.N=a;this.Z=4;this.O="n";var c=(b||"n4").match(/^([nio])([1-9])$/i);c&&(this.O=c[1],this.Z=parseInt(c[2],10))}H.prototype.getName=function(){return this.N};function I(a){return a.O+a.Z}function ga(a){var b=4,c="n",d=null;a&&((d=a.match(/(normal|oblique|italic)/i))&&d[1]&&(c=d[1].substr(0,1).toLowerCase()),(d=a.match(/([1-9]00|normal|bold)/i))&&d[1]&&(/bold/i.test(d[1])?b=7:/[1-9]00/.test(d[1])&&(b=parseInt(d[1].substr(0,1),10))));return c+b};function ha(a,b){this.d=a;this.q=a.w.document.documentElement;this.Q=b;this.j="wf";this.h=new G("-");this.ha=!1!==b.events;this.F=!1!==b.classes}function J(a){if(a.F){var b=t(a.q,a.h.e(a.j,"active")),c=[],d=[a.h.e(a.j,"loading")];b||c.push(a.h.e(a.j,"inactive"));s(a.q,c,d)}K(a,"inactive")}function K(a,b,c){if(a.ha&&a.Q[b])if(c)a.Q[b](c.getName(),I(c));else a.Q[b]()};function ia(){this.C={}};function L(a,b){this.d=a;this.I=b;this.o=this.d.createElement("span",{"aria-hidden":"true"},this.I)}
function M(a,b){var c=a.o,d;d=[];for(var e=b.N.split(/,\s*/),f=0;f<e.length;f++){var g=e[f].replace(/['"]/g,"");-1==g.indexOf(" ")?d.push(g):d.push("'"+g+"'")}d=d.join(",");e="normal";"o"===b.O?e="oblique":"i"===b.O&&(e="italic");c.style.cssText="display:block;position:absolute;top:-9999px;left:-9999px;font-size:300px;width:auto;height:auto;line-height:normal;margin:0;padding:0;font-variant:normal;white-space:nowrap;font-family:"+d+";"+("font-style:"+e+";font-weight:"+(b.Z+"00")+";")}
function N(a){r(a.d,"body",a.o)}L.prototype.remove=function(){var a=this.o;a.parentNode&&a.parentNode.removeChild(a)};function O(a,b,c,d,e,f,g,h){this.$=a;this.ka=b;this.d=c;this.m=d;this.k=e;this.I=h||"BESbswy";this.v={};this.X=f||3E3;this.ca=g||null;this.H=this.u=this.t=null;this.t=new L(this.d,this.I);this.u=new L(this.d,this.I);this.H=new L(this.d,this.I);M(this.t,new H("serif",I(this.m)));M(this.u,new H("sans-serif",I(this.m)));M(this.H,new H("monospace",I(this.m)));N(this.t);N(this.u);N(this.H);this.v.serif=this.t.o.offsetWidth;this.v["sans-serif"]=this.u.o.offsetWidth;this.v.monospace=this.H.o.offsetWidth}
var P={sa:"serif",ra:"sans-serif",qa:"monospace"};O.prototype.start=function(){this.oa=n();M(this.t,new H(this.m.getName()+",serif",I(this.m)));M(this.u,new H(this.m.getName()+",sans-serif",I(this.m)));Q(this)};function R(a,b,c){for(var d in P)if(P.hasOwnProperty(d)&&b===a.v[P[d]]&&c===a.v[P[d]])return!0;return!1}
function Q(a){var b=a.t.o.offsetWidth,c=a.u.o.offsetWidth;b===a.v.serif&&c===a.v["sans-serif"]||a.k.ga&&R(a,b,c)?n()-a.oa>=a.X?a.k.ga&&R(a,b,c)&&(null===a.ca||a.ca.hasOwnProperty(a.m.getName()))?S(a,a.$):S(a,a.ka):ja(a):S(a,a.$)}function ja(a){setTimeout(k(function(){Q(this)},a),50)}function S(a,b){a.t.remove();a.u.remove();a.H.remove();b(a.m)};function T(a,b,c,d){this.d=b;this.A=c;this.S=0;this.ea=this.ba=!1;this.X=d;this.k=a.k}function ka(a,b,c,d,e){c=c||{};if(0===b.length&&e)J(a.A);else for(a.S+=b.length,e&&(a.ba=e),e=0;e<b.length;e++){var f=b[e],g=c[f.getName()],h=a.A,m=f;h.F&&s(h.q,[h.h.e(h.j,m.getName(),I(m).toString(),"loading")]);K(h,"fontloading",m);h=null;h=new O(k(a.ia,a),k(a.ja,a),a.d,f,a.k,a.X,d,g);h.start()}}
T.prototype.ia=function(a){var b=this.A;b.F&&s(b.q,[b.h.e(b.j,a.getName(),I(a).toString(),"active")],[b.h.e(b.j,a.getName(),I(a).toString(),"loading"),b.h.e(b.j,a.getName(),I(a).toString(),"inactive")]);K(b,"fontactive",a);this.ea=!0;la(this)};
T.prototype.ja=function(a){var b=this.A;if(b.F){var c=t(b.q,b.h.e(b.j,a.getName(),I(a).toString(),"active")),d=[],e=[b.h.e(b.j,a.getName(),I(a).toString(),"loading")];c||d.push(b.h.e(b.j,a.getName(),I(a).toString(),"inactive"));s(b.q,d,e)}K(b,"fontinactive",a);la(this)};function la(a){0==--a.S&&a.ba&&(a.ea?(a=a.A,a.F&&s(a.q,[a.h.e(a.j,"active")],[a.h.e(a.j,"loading"),a.h.e(a.j,"inactive")]),K(a,"active")):J(a.A))};function U(a){this.K=a;this.B=new ia;this.pa=new B(a.navigator.userAgent);this.a=this.pa.parse();this.U=this.V=0;this.R=this.T=!0}
U.prototype.load=function(a){this.d=new q(this.K,a.context||this.K);this.T=!1!==a.events;this.R=!1!==a.classes;var b=new ha(this.d,a),c=[],d=a.timeout;b.F&&s(b.q,[b.h.e(b.j,"loading")]);K(b,"loading");var c=this.B,e=this.d,f=[],g;for(g in a)if(a.hasOwnProperty(g)){var h=c.C[g];h&&f.push(h(a[g],e))}c=f;this.U=this.V=c.length;a=new T(this.a,this.d,b,d);d=0;for(g=c.length;d<g;d++)e=c[d],e.L(this.a,k(this.la,this,e,b,a))};
U.prototype.la=function(a,b,c,d){var e=this;d?a.load(function(a,b,d){ma(e,c,a,b,d)}):(a=0==--this.V,this.U--,a&&0==this.U?J(b):(this.R||this.T)&&ka(c,[],{},null,a))};function ma(a,b,c,d,e){var f=0==--a.V;(a.R||a.T)&&setTimeout(function(){ka(b,c,d||null,e||null,f)},0)};function na(a,b,c){this.P=a?a:b+oa;this.s=[];this.W=[];this.fa=c||""}var oa="//fonts.googleapis.com/css";na.prototype.e=function(){if(0==this.s.length)throw Error("No fonts to load!");if(-1!=this.P.indexOf("kit="))return this.P;for(var a=this.s.length,b=[],c=0;c<a;c++)b.push(this.s[c].replace(/ /g,"+"));a=this.P+"?family="+b.join("%7C");0<this.W.length&&(a+="&subset="+this.W.join(","));0<this.fa.length&&(a+="&text="+encodeURIComponent(this.fa));return a};function pa(a){this.s=a;this.da=[];this.M={}}
var qa={latin:"BESbswy",cyrillic:"&#1081;&#1103;&#1046;",greek:"&#945;&#946;&#931;",khmer:"&#x1780;&#x1781;&#x1782;",Hanuman:"&#x1780;&#x1781;&#x1782;"},ra={thin:"1",extralight:"2","extra-light":"2",ultralight:"2","ultra-light":"2",light:"3",regular:"4",book:"4",medium:"5","semi-bold":"6",semibold:"6","demi-bold":"6",demibold:"6",bold:"7","extra-bold":"8",extrabold:"8","ultra-bold":"8",ultrabold:"8",black:"9",heavy:"9",l:"3",r:"4",b:"7"},sa={i:"i",italic:"i",n:"n",normal:"n"},ta=/^(thin|(?:(?:extra|ultra)-?)?light|regular|book|medium|(?:(?:semi|demi|extra|ultra)-?)?bold|black|heavy|l|r|b|[1-9]00)?(n|i|normal|italic)?$/;
pa.prototype.parse=function(){for(var a=this.s.length,b=0;b<a;b++){var c=this.s[b].split(":"),d=c[0].replace(/\+/g," "),e=["n4"];if(2<=c.length){var f;var g=c[1];f=[];if(g)for(var g=g.split(","),h=g.length,m=0;m<h;m++){var l;l=g[m];if(l.match(/^[\w-]+$/)){l=ta.exec(l.toLowerCase());var p=void 0;if(null==l)p="";else{p=void 0;p=l[1];if(null==p||""==p)p="4";else var fa=ra[p],p=fa?fa:isNaN(p)?"4":p.substr(0,1);l=l[2];p=[null==l||""==l?"n":sa[l],p].join("")}l=p}else l="";l&&f.push(l)}0<f.length&&(e=f);
3==c.length&&(c=c[2],f=[],c=c?c.split(","):f,0<c.length&&(c=qa[c[0]])&&(this.M[d]=c))}this.M[d]||(c=qa[d])&&(this.M[d]=c);for(c=0;c<e.length;c+=1)this.da.push(new H(d,e[c]))}};function V(a,b){this.a=(new B(navigator.userAgent)).parse();this.d=a;this.f=b}var ua={Arimo:!0,Cousine:!0,Tinos:!0};V.prototype.L=function(a,b){b(a.k.Y)};V.prototype.load=function(a){var b=this.d;"MSIE"==this.a.getName()&&1!=this.f.blocking?ca(b,k(this.aa,this,a)):this.aa(a)};
V.prototype.aa=function(a){for(var b=this.d,c=new na(this.f.api,u(b),this.f.text),d=this.f.families,e=d.length,f=0;f<e;f++){var g=d[f].split(":");3==g.length&&c.W.push(g.pop());var h="";2==g.length&&""!=g[1]&&(h=":");c.s.push(g.join(h))}d=new pa(d);d.parse();v(b,c.e());a(d.da,d.M,ua)};function W(a,b){this.d=a;this.f=b;this.p=[]}W.prototype.J=function(a){var b=this.d;return u(this.d)+(this.f.api||"//f.fontdeck.com/s/css/js/")+(b.w.location.hostname||b.K.location.hostname)+"/"+a+".js"};
W.prototype.L=function(a,b){var c=this.f.id,d=this.d.w,e=this;c?(d.__tpwebfontfontdeckmodule__||(d.__tpwebfontfontdeckmodule__={}),d.__tpwebfontfontdeckmodule__[c]=function(a,c){for(var d=0,m=c.fonts.length;d<m;++d){var l=c.fonts[d];e.p.push(new H(l.name,ga("font-weight:"+l.weight+";font-style:"+l.style)))}b(a)},w(this.d,this.J(c),function(a){a&&b(!1)})):b(!1)};W.prototype.load=function(a){a(this.p)};function X(a,b){this.d=a;this.f=b;this.p=[]}X.prototype.J=function(a){var b=u(this.d);return(this.f.api||b+"//use.typekit.net")+"/"+a+".js"};X.prototype.L=function(a,b){var c=this.f.id,d=this.d.w,e=this;c?w(this.d,this.J(c),function(a){if(a)b(!1);else{if(d.Typekit&&d.Typekit.config&&d.Typekit.config.fn){a=d.Typekit.config.fn;for(var c=0;c<a.length;c+=2)for(var h=a[c],m=a[c+1],l=0;l<m.length;l++)e.p.push(new H(h,m[l]));try{d.Typekit.load({events:!1,classes:!1})}catch(p){}}b(!0)}},2E3):b(!1)};
X.prototype.load=function(a){a(this.p)};function Y(a,b){this.d=a;this.f=b;this.p=[]}Y.prototype.L=function(a,b){var c=this,d=c.f.projectId,e=c.f.version;if(d){var f=c.d.w;w(this.d,c.J(d,e),function(e){if(e)b(!1);else{if(f["__mti_fntLst"+d]&&(e=f["__mti_fntLst"+d]()))for(var h=0;h<e.length;h++)c.p.push(new H(e[h].fontfamily));b(a.k.Y)}}).id="__MonotypeAPIScript__"+d}else b(!1)};Y.prototype.J=function(a,b){var c=u(this.d),d=(this.f.api||"fast.fonts.net/jsapi").replace(/^.*http(s?):(\/\/)?/,"");return c+"//"+d+"/"+a+".js"+(b?"?v="+b:"")};
Y.prototype.load=function(a){a(this.p)};function Z(a,b){this.d=a;this.f=b}Z.prototype.load=function(a){var b,c,d=this.f.urls||[],e=this.f.families||[],f=this.f.testStrings||{};b=0;for(c=d.length;b<c;b++)v(this.d,d[b]);d=[];b=0;for(c=e.length;b<c;b++){var g=e[b].split(":");if(g[1])for(var h=g[1].split(","),m=0;m<h.length;m+=1)d.push(new H(g[0],h[m]));else d.push(new H(g[0]))}a(d,f)};Z.prototype.L=function(a,b){return b(a.k.Y)};var $=new U(this);$.B.C.custom=function(a,b){return new Z(b,a)};$.B.C.fontdeck=function(a,b){return new W(b,a)};$.B.C.monotype=function(a,b){return new Y(b,a)};$.B.C.typekit=function(a,b){return new X(b,a)};$.B.C.google=function(a,b){return new V(b,a)};this.tpWebFont||(this.tpWebFont={},this.tpWebFont.load=k($.load,$),this.tpWebFontConfig&&$.load(this.tpWebFontConfig));})(this,document);
