/*!
 * REVOLUTION 6.3.3 UTILS OPTMIZE CONTENT
 * @version: 2.0 (08.12.2020)
 * @author ThemePunch
*/

/**********************************
	-	GLOBAL VARIABLES	-
**********************************/
;window.RVS = window.RVS === undefined ? {} : window.RVS;
RVS.F = RVS.F === undefined ? {} : RVS.F;
RVS.ENV = RVS.ENV === undefined ? {} : RVS.ENV;
RVS.LIB = RVS.LIB === undefined ? {} : RVS.LIB;
RVS.V = RVS.V === undefined ? {} : RVS.V;
RVS.S = RVS.S === undefined ? {} : RVS.S;
RVS.C = RVS.C === undefined ? {} : RVS.C;
RVS.WIN = RVS.WIN === undefined ? jQuery(window) : RVS.WIN;

RVS.DOC = RVS.DOC === undefined ? jQuery(document) : RVS.DOC;
RVS.OZ = RVS.OZ === undefined ? {} : RVS.OZ;

/**********************************
	-	OPTIMIZER FUNCTIONS	-
********************************/
(function() {
	/*
	OPEN OPTIMIZER
	*/
	RVS.F.openOptimizer = function(_) {
		optimizerListener();
		if (_!==undefined && ((_.sliderid!==undefined && _.sliderid!=="") || (_.alias!==undefined && _.alias!==""))) {

			RVS.F.showWaitAMinute({fadeIn:0,text:RVS_LANG.loadingcontent});
			var params = {};
			if (_.alias!==undefined && _.alias!=="") params.alias = _.alias;
			if (_.sliderid!==undefined && _.sliderid!=="") params.id = 'slider-'+_.sliderid;
			setTimeout(function() {
				RVS.F.ajaxRequest('get_full_slider_object', params , function(response){

					if (response.id===undefined) {
						RVS.F.showWaitAMinute({fadeOut:500,text:RVS_LANG.preparingdatas});
					} else {
						RVS.ENV.sliderid = response.id;
						RVS.ENV.sliderID = response.id;
						RVS.F.showWaitAMinute({fadeIn:500,text:RVS_LANG.preparingdatas});
						response.slider_params.alias = response.alias;
						response.slider_params.title = response.title;
			            //Init Slider SETTINGS
			            RVS.OZ = {slideIDs:[]};
			            RVS.OZ.settings = RVS.F.safeExtend(true,{},response.slider_params);
			            if (response.static_slide!==undefined && response.static_slide.params!=undefined && response.static_slide.params.static!==undefined && response.static_slide.params.static.isstatic===true) {
			            	RVS.OZ[response.static_slide.id] = {
			            		slide:RVS.F.safeExtend(true,{},response.static_slide.params),
								layers:RVS.F.safeExtend(true,{},response.static_slide.layers)
			            	}
			            	RVS.OZ.slideIDs.push(response.static_slide.id);
			            }
			            for (var sindex in response.slides) {
							if(response.slides.hasOwnProperty(sindex)) {
								let slide = response.slides[sindex];
								RVS.OZ[slide.id] = {
									slide:RVS.F.safeExtend(true,{},slide.params),
									layers:RVS.F.safeExtend(true,{},slide.layers)
								}
								RVS.OZ.slideIDs.push(slide.id);
							}
			            }
			            buildOptimizer();
			            RVS.F.showWaitAMinute({fadeOut:500,text:RVS_LANG.preparingdatas});

			        }
		        },undefined,true);
		        RVS.F.showWaitAMinute({fadeOut:0,text:RVS_LANG.loadingcontent});
			},100);

	    } else
	    if (RVS.SLIDER!==undefined) {
	    	RVS.S.optimID = RVS.SLIDER.id;
	    	RVS.OZ = RVS.F.safeExtend(true,{},RVS.SLIDER);
	    	buildOptimizer();
	    }
	};



	// BUILD BASIC CONTAINERS
	function buildOptimizer(selid) {
		if (RVS.ENV.optimizer===undefined) {
			var _= '<div class="rb-modal-wrapper _TPRB_" data-modal="rbm_optimizer">';
				_+='	<div class="rb-modal-inner">';
				_+='		<div class="rb-modal-content">';
				_+='			<div id="rbm_optimizer" class="rb_modal form_inner">';
				_+='				<div class="rbm_header"><i class="rbm_symbol material-icons">flash_on</i><span class="rbm_title">'+RVS_LANG.optimizertitel+'</span><i class="rbm_close material-icons">close</i></div>';
				_+='<div class="rbm_header_content">';
				_+='<div class="optim_row noborder" style="height:50px">';
				_+='<div class="optim_cell op_c_a optim_title">'+RVS_LANG.element+'</div>';
				_+='<div class="optim_cell op_c_b optim_title">'+RVS_LANG.filesize+'</div>';
				_+='<div class="optim_cell op_c_c optim_title">'+RVS_LANG.dimensions+'<i id="more_about_optimizer" class="material-icons">help</i><div class="optim_subtitles"><div class="optim_subtitle" style="width:90px">'+RVS_LANG.toosmall+'</div><div class="optim_subtitle" style="width:139px">'+RVS_LANG.standard1x+'</div><div class="optim_subtitle" style="width:91px">'+RVS_LANG.retina2x+'</div><div class="optim_subtitle" style="width:69px">'+RVS_LANG.oversized+'</div></div></div>';
				_+='<div class="optim_cell op_c_d optim_title">'+RVS_LANG.suggestion+'</div>'; //<div class="optim_subtitles"><div class="optim_subtitle" style="width:33px">25%</div><div class="optim_subtitle" style="width:33px">50%</div><div class="optim_subtitle" style="width:33px">75%</div><div class="optim_subtitle" style="width:33px">100%</div></div>
				_+='<div class="optim_cell op_c_e"></div>';		//<div class="optim_checkbox" style="margin-top:15px"><i class="material-icons">check</i></div>
				_+='</div>';
				_+='</div>';
				_+='				<div id="rbm_optimizer_list" class="rbm_content">';
				_+='				</div>';
				_+='				<div class="rbm_footer">';
				_+='					<div id="optimizer_compression">';
				_+='						<div class="opt_loaded_title">'+RVS_LANG.servercompression+'<a target="_blank" href="https://www.sliderrevolution.com/faq/what-is-server-compression-gzip-brotli/"><i class="material-icons">help</i></a></div>';
				_+='						<div class="opt_loaded_subtitle"><a target="_blank" href="https://www.sliderrevolution.com/faq/what-is-server-compression-gzip-brotli/">'+(RVS.ENV.output_compress.length===0 ? +RVS_LANG.noservercompression : RVS.ENV.output_compress.toString())+'</a></div>';
				_+='					</div>';
				_+='					<div id="opt_summary">';
				_+='						<div id="optimizer_fullsize">0 kb</div><div id="optimizer_cachedsize"></div>';
				_+='						<div class="optimier_footerinfo">';
				_+='							<div class="opt_loaded_title" id="optimized_prec">'+RVS_LANG.sizeafteroptim+'</div>';
				_+='							<div class="opt_loaded_subtitle" id="optimize_to_save">'+RVS_LANG.loadedmediainfo+'</div>';
				_+='						</div>';
				_+='					</div>';
				_+='					<div id="rbm_optpanel_savebtn" class="large_actionbutton basic_action_coloredbutton"><i class="material-icons mr10">flash_on</i><span class="rbm_cp_save_text">'+RVS_LANG.savechanges+'</span></div>';
				_+='				</div>';
				_+='			</div>';
				_+='		</div>';
				_+='	</div>';
				_+='</div>';
			RVS.ENV.optimizer = jQuery(_);
			RVS.ENV.optlist = RVS.ENV.optimizer.find('#rbm_optimizer_list');
			jQuery(document.body).append(RVS.ENV.optimizer);
		}
		RVS.ENV.optlist[0].innerHTML = "";
		RVS.F.RSDialog.create({modalid:'#rbm_optimizer', bgopacity:0.5});
		RVS.S.optUID = 0;
		RVS.OZ.slideFullSize = 0;
		RVS.OZ.list = {};
		RVS.OZ.loadAddons = [];
		RVS.OZ.addOnsFullSize = 0;
		createOptimizerList();
		jQuery('#rbm_optimizer_list').RSScroll({wheelPropagation:false, suppressScrollX:true});
		RVS.F.RSDialog.center();
		//if (RVS.S.optUID===0) RVS.F.RSDialog.close();
	}

	function buildRow(_) {
		RVS.S.optUID++;
		var t ='<div id="OPTROW_'+RVS.S.optUID+'" class="optim_row" data-uid="'+RVS.S.optUID+'">';
			t+='<div class="optim_cell op_c_a"><div class="opt_img" style="background-image:url('+_.url+')"></div>'+_.alias+'</div>';
			t+='<div class="optim_cell op_c_b">'+RVS_LANG.calculating+'</div>';
			t+='<div class="optim_cell op_c_c"><ofs_mark class="ofsmark_a"></ofs_mark><ofs_mark class="ofsmark_b"><div class="ofsinfo" data-optim="'+_.rw+' x '+_.rh+'"></div></ofs_mark><ofs_mark class="ofsmark_c"><div class="ofsinfo" data-optim="'+(2*_.rw)+' x '+(2*_.rh)+'"></div></ofs_mark><ofs_mark class="ofsmark_d"></ofs_mark><opt_fs_grd></opt_fs_grd></div>';
			t+='<div class="optim_cell op_c_d"></div>';
			t+='<div class="optim_cell op_c_e"></div>';		//<div class="optim_checkbox"><i class="material-icons">check</i></div>
			t+='</div>';
		RVS.OZ.list[RVS.S.optUID] = {row:jQuery(t), url:_.url, rw:_.rw, rh:_.rh, path:_.path, bgsize:_.bgsize, pathURL:_.pathURL, pathSize:_.pathSize, slideid:_.slideid, layerid:_.layerid, imgtype:_.imgtype};
		return RVS.OZ.list[RVS.S.optUID].row;
	}

	function isWebSave(_) {
		if (RVS==undefined || RVS.LIB===undefined || RVS.LIB.FONTS===undefined) return false;
		var ret = false;
		for (var i in RVS.LIB.FONTS) {
			if (ret===true || !RVS.LIB.FONTS.hasOwnProperty(i) || (RVS.LIB.FONTS[i].type!=="custom" && RVS.LIB.FONTS[i].type!=="websafe")) continue;
			ret = RVS.LIB.FONTS[i].label == _;
		}
		return ret;
	}

	function buildFontRow(_) {
		_.alias = _.alias.trimStart();
		_.alias = _.alias.trimEnd();
		var family = _.alias.split(' ').join('+'),
			idfam = (_.alias.split(' ').join('_')).split(",").join('_'),
			weights = _.weights.toString().split(",").join('%2C'),
			webSafe = isWebSave(_.alias);

		var t ='<div class="optim_row">';
			t+='<div class="optim_cell op_c_a"><i class="material-icons">text_fields</i>'+_.alias+' '+_.weights.toString()+'</div>';
			t+='<div class="optim_cell op_c_b" id="fontrowsize_'+idfam+'">'+(webSafe===true ? 'N.A' : RVS_LANG.calculating)+'</div>';
			t+='<div class="optim_cell op_c_c"></div>';
			t+='<div class="optim_cell op_c_d"></div>';
			t+='<div class="optim_cell op_c_e"></div>';
			t+='</div>';
		t = jQuery(t);
		if (webSafe!==true)	getFontURLS("https://fonts.googleapis.com/css?family="+family+":"+weights, _.alias,idfam);
		return t;
	}

	function buildAddonRow(_) {
		RVS.OZ.loadAddons.push(_.slug);
		_.alias = _.alias.replace("Slider Revolution","");
		var aliasid = _.alias.split(" ").join("_");
		var t ='<div class="optim_row">';
			t+='<div class="optim_cell op_c_a"><i class="material-icons">extension</i>'+_.alias+'</div>';
			t+='<div class="optim_cell op_c_b" id="addrowsize_'+_.slug+'">'+RVS_LANG.calculating+'</div>';
			t+='<div class="optim_cell op_c_c"></div>';
			t+='<div class="optim_cell op_c_d"></div>';
			t+='<div class="optim_cell op_c_e"></div>';
			t+='</div>';
		t = jQuery(t);

		return t;
	}

	function buildCoreRow(_) {
		var t ='<div class="optim_row">';
			t+='<div class="optim_cell op_c_a"><i class="material-icons">code</i>'+_.alias+'</div>';
			t+='<div class="optim_cell op_c_b">'+_.size+'</div>';
			t+='<div class="optim_cell op_c_c"></div>';
			t+='<div class="optim_cell op_c_d">'+_.warning+'</div>';
			t+='<div class="optim_cell op_c_e"></div>';
			t+='</div>';
		t = jQuery(t);

		return t;
	}

	function getAvailableSizes() {
		var r = [];
		if (RVS.V.sizes===undefined) {
			RVS.V.sizes = ["d","n","t","m"];
			RVS.V.sizesold = ["desktop","notebook","tablet","mobile"];
		}
		for (var i in RVS.V.sizes) if (RVS.V.sizes.hasOwnProperty(i)) {
			if (RVS.OZ.settings.size.custom[RVS.V.sizes[i]]===true) r.push(RVS.V.sizes[i]);
		}
		return r;
	}

	function addFontFamily(l) {
		if (l.idle!==undefined && l.idle.fontFamily===undefined) l.idle.fontFamily = "Roboto";
		RVS.OZ.fontFamilies=RVS.OZ.fontFamilies===undefined ? {} : RVS.OZ.fontFamilies;
		RVS.OZ.fontFamilies[l.idle.fontFamily]=RVS.OZ.fontFamilies[l.idle.fontFamily]===undefined ? { weights:[], italic:false} : RVS.OZ.fontFamilies[l.idle.fontFamily];
		for (var i in RVS.V.avSizes) if (RVS.V.avSizes.hasOwnProperty(i)) {
			if (l.idle!==undefined && l.idle.fontWeight!==undefined && l.idle.fontWeight[RVS.V.avSizes[i]]!==undefined && jQuery.inArray(l.idle.fontWeight[RVS.V.avSizes[i]].v,RVS.OZ.fontFamilies[l.idle.fontFamily].weights)===-1)
				RVS.OZ.fontFamilies[l.idle.fontFamily].weights.push(l.idle.fontWeight[RVS.V.avSizes[i]].v);
		}
		RVS.OZ.fontFamilies[l.idle.fontFamily].italic = RVS.OZ.fontFamilies[l.idle.fontFamily].italic===true || l.idle.fontStyle===true ? true : false;

	}

	function get_fontSizes(url,fontid,idfam) {
		var xhr = new XMLHttpRequest();
		xhr.open("HEAD",url,true);
		xhr.onreadystatechange = function() {
			if (this.readyState === this.DONE) {
				var size = parseInt(xhr.getResponseHeader("Content-Length"));
				RVS.OZ.fontFamilies[fontid].size = RVS.OZ.fontFamilies[fontid].size===undefined ? size : RVS.OZ.fontFamilies[fontid].size + size;
				jQuery('#fontrowsize_'+idfam)[0].innerHTML = Math.round(parseInt(RVS.OZ.fontFamilies[fontid].size) / 1000)	+ " kb";
				updateSummary();
			}
		}
		xhr.onerror = function(e) {
	   		jQuery('#fontrowsize_'+idfam)[0].innerHTML = "N.A.";
	   }
		xhr.send();
	}

	function getFontURLS(url, fontid,idfam) {
	   var xhr = new XMLHttpRequest();
	   xhr.open("GET", url, true);
	   xhr.onreadystatechange = function() {
	       if (this.readyState == this.DONE) {
	       		var urls=[],
	       			urls_temp = xhr.response.split("url(");
	       		for (var i in urls_temp) if (urls_temp.hasOwnProperty(i)) {
	       			if (urls_temp[i][0]!=='h' && urls_temp[i][0]!=='H') continue;
	       			var url = urls_temp[i].split(') format');
	       			if (jQuery.inArray(url[0],urls)==-1) urls.push(url[0]);
	       		}
	       		for (var j in urls) if (urls.hasOwnProperty(j)) get_fontSizes(urls[j],fontid,idfam);
	       }
	   };
	   xhr.onerror = function(e) {
	   		jQuery('#fontrowsize_'+idfam)[0].innerHTML = "N.A.";
	   }
	   xhr.send();
	}


	function min(_,parentSize,column,breakat) {
		var	m = 0,
			sizes={};
		for (var i in RVS.V.avSizes) if (RVS.V.avSizes.hasOwnProperty(i)) {
			var ns = m;
			if (column!==undefined) {
				if (breakat==="notebook" && jQuery.inArray(RVS.V.avSizes[i],["t","m","n"])>=0) column = 1;
				if (breakat==="mobile" && jQuery.inArray(RVS.V.avSizes[i],["m"])>=0) column = 1;
				if (breakat==="tablet" && jQuery.inArray(RVS.V.avSizes[i],["t","m"])>=0) column = 1;
				ns = parentSize[RVS.V.avSizes[i]] * column;
			} else {
				if (_!==undefined && _[RVS.V.avSizes[i]]!==undefined)
				 	ns =  _[RVS.V.avSizes[i]].v!==undefined ? RVS.F.isNumeric((parseInt(_[RVS.V.avSizes[i]].v))) ? _[RVS.V.avSizes[i]].v : m :
				 		 RVS.F.isNumeric((parseInt(_[RVS.V.avSizes[i]]))) ? _[RVS.V.avSizes[i]] : m;

				if ((ns+"").indexOf('%')>=0) ns = parentSize[RVS.V.avSizes[i]]*(parseInt(ns,0)/100); else ns = parseInt(ns,0);
			}
			m = Math.max(ns,m);
			sizes[RVS.V.avSizes[i]] = ns;
		}
		return {max:parseInt(m,0), sizes:sizes};
	}

	function getRequiredDim(w,h, mw, mh) {
		var r = {w:w, h:h};
		if (w>h) {
			if (w>mw) r.w = mw;
		} else {
			if (h>mh) r.h = mh;
		}
		return r;
	}

	function createOptimizerList() {

		RVS.V.avSizes = getAvailableSizes();
		RVS.OZ.toLoad = {};
		// IF SLIDER DIMENSIONS ARE KNOWN, OTHER WAY WE CAN NOT CALCULATE IMAGE SIZES, ETC.
		if (RVS.OZ!==undefined && RVS.OZ.settings!==undefined && RVS.OZ.settings.size!==undefined) {

			var stagedims = { w: min(RVS.OZ.settings.size.width), h:min(RVS.OZ.settings.size.height)},
				rdim = getRequiredDim(stagedims.w.max, stagedims.h.max, 1920, 1920),
				fontrow, addonsrow;

			// Check Slider BG
			if (RVS.OZ.settings!==undefined && RVS.OZ.settings.layout!==undefined && RVS.OZ.settings.layout.bg!==undefined && RVS.OZ.settings.layout.bg.useImage && RVS.OZ.settings.layout.bg.image!==undefined && RVS.OZ.settings.layout.bg.image!=="" && RVS.OZ.settings.layout.bg.image.length<5) {
				RVS.ENV.optlist.append(buildRow({alias:RVS_LANG.modulbackground, url:RVS.OZ.settings.layout.bg.image, rw:rdim.w, rh:rdim.h, pathURL:"settings.layout.bg.image", pathSize:"settings.layout.bg.imageSourceType", slideid:"settings"}));
				RVS.OZ.toLoad[RVS.S.optUID] = RVS.OZ.list[RVS.S.optUID].url;
			}

			// Check Slide Backgrounds
			for (var i in RVS.OZ.slideIDs) if (RVS.OZ.slideIDs.hasOwnProperty(i)) {
				var li = RVS.OZ.slideIDs[i];
				if (RVS.OZ[li]!==undefined && RVS.OZ[li].slide!==undefined && RVS.OZ[li].slide.bg!==undefined) {
					if (RVS.OZ[li].slide.bg.type==="image" || RVS.OZ[li].slide.bg.type==="external" || RVS.OZ[li].slide.bg.type==="html5" || RVS.OZ[li].slide.bg.type==="youtube" || RVS.OZ[li].slide.bg.type==="vimeo") {
						if (RVS.OZ[li].slide.bg.image===undefined || RVS.OZ[li].slide.bg.image==="" || RVS.OZ[li].slide.bg.image.length<5) {
							// Nothing to Do
						} else {
							RVS.OZ[li].slide.title = RVS.OZ[li].slide.title===undefined ? "Slide" : RVS.OZ[li].slide.title;
							RVS.ENV.optlist.append(buildRow({alias:'#'+(parseInt(i)+1)+' '+RVS.OZ[li].slide.title, url:RVS.OZ[li].slide.bg.image, rw:rdim.w, rh:rdim.h,  pathURL:li+".slide.bg.image", pathSize:li+".slide.bg.imageSourceType", slideid:li}));
							RVS.OZ.toLoad[RVS.S.optUID] = RVS.OZ.list[RVS.S.optUID].url;
						}
					}
				}
			}


			// COLLECT LAYERS
			for (var i in RVS.OZ.slideIDs) if (RVS.OZ.slideIDs.hasOwnProperty(i)) {
				var li = RVS.OZ.slideIDs[i];
				if (RVS.OZ[li]!==undefined && RVS.OZ[li].layers!==undefined) {
					for (var j in RVS.OZ[li].layers) if (RVS.OZ[li].layers.hasOwnProperty(j)) {
						var l = RVS.OZ[li].layers[j],row=0, fontrow;
						// BACKGROUND IMAGES

						if (l.idle!==undefined && l.idle.backgroundImage!==undefined && l.idle.backgroundImage.length>4) row = {layerid:j, imgtype: "bg",bgsize:l.idle.backgroundSize, alias:l.alias, url:l.idle.backgroundImage, path:li+".layers."+j+".idle.", pathURL:li+".layers."+j+".idle.backgroundImage", pathSize:li+".layers."+j+".behavior.imageSourceType", slideid:li};
						// IMAGE SOURCE
						if (l.type==="image" && l.media!==undefined && l.media.imageUrl!==undefined && l.media.imageUrl.length>4) row = {layerid:j, imgtype: "image", alias:l.alias, url:l.media.imageUrl,  pathURL:li+".layers."+j+".media.imageUrl", pathSize:li+".layers."+j+".behavior.imageSourceType", slideid:li};
						// VIDEO POSTER SOURCE
						if ((l.type==="video") && l.media!==undefined && l.media.posterUrl!==undefined && l.media.posterUrl.length>4) row = {layerid:j, imgtype: "poster", alias:l.alias, url:l.media.posterUrl, pathURL:li+".layers."+j+".media.posterUrl" , pathSize:li+".layers."+j+".behavior.imageSourceType", slideid:li};

						// TEXT LAYERS
						if ((l.type==="text") || l.type==="button") addFontFamily(l);

						if (row!==undefined && row!==0) {
							var pdim = {w:stagedims.w.sizes, h:stagedims.w.sizes};
							if (l.group.puid!==-1 && l.group.puid!==undefined) {
								var player = RVS.OZ[li].layers[l.group.puid];
								if (player.type==="group") {
									pdim.w = min(player.size.width,pdim.w).sizes;
									pdim.h = min(player.size.height,pdim.h).sizes;
								}
							}

							if (l.type==="column") {
								row.rw = min(l.size.width,pdim.w, RVS.F.convertFraction(l.group.columnSize),l.group.columnbreakat).max;
								row.rh = min(l.size.height,pdim.h).max;
							} else {
								row.rw =  min(l.size.width,pdim.w).max;
								row.rh =  min(l.size.height,pdim.h).max;
							}

							RVS.ENV.optlist.append(buildRow(row));
							RVS.OZ.toLoad[RVS.S.optUID] = RVS.OZ.list[RVS.S.optUID].url;
						}
					}
				}
			}
			// COLLECT FONT LIBRARIES

			for (i in RVS.OZ.fontFamilies) {
				if (!RVS.OZ.fontFamilies.hasOwnProperty(i)) continue;
				fontrow = buildFontRow({alias:i, weights:RVS.OZ.fontFamilies[i].weights});
				RVS.ENV.optlist.append(fontrow);
			}

			// COLLECT ADDONS
			for (i in RVS.OZ.settings.addOns) if (RVS.OZ.settings.addOns.hasOwnProperty(i)) {
				if (!RVS.OZ.settings.addOns.hasOwnProperty(i) || RVS.OZ.settings.addOns[i].enable!==true ) continue;

                addonsrow = buildAddonRow({alias:(RVS.LIB.ADDONS===undefined || RVS.LIB.ADDONS[i]===undefined || RVS.LIB.ADDONS[i].full_title===undefined ? i : RVS.LIB.ADDONS[i].full_title), slug:i});
				RVS.ENV.optlist.append(addonsrow);
			}
			var comp = RVS.ENV.output_compress!==undefined && RVS.ENV.output_compress.length>0;
			RVS.ENV.optlist.append(buildCoreRow({alias:RVS_LANG.coretools, size:comp ? "43 kb" : "116 kb", warning:comp ? "" : RVS_LANG.enablecompression}));
			RVS.ENV.optlist.append(buildCoreRow({alias:RVS_LANG.corejs, size:comp ? "72 kb" : "289 kb", warning:comp ? "" : RVS_LANG.enablecompression}));
			RVS.ENV.optlist.append(buildCoreRow({alias:RVS_LANG.corecss, size:comp ? "11 kb" : "55 kb", warning:comp ? "" : RVS_LANG.enablecompression}));
			RVS.OZ.coreFullSize = comp ? 134 : 481;

			// LOAD ADDON SIZES
			getAddonInfos();

			// LOAD IMAGE SIZES
			getImageInfos();
		}

	}

	function compareSize(_) {
		var OH = _.h, OW = _.w, temp, opted = false, rsized = true;

		// IF AUTO RECOMMENDED WIDTH/HEIGHT IS COMING IN
		if (_.rh===0 && _.rw===0) { _.rh = _.h; _.rw = _.w};
		if (_.rh===0) _.rh = (_.rw/_.w) * _.h;
		if (_.rw===0) _.rw = (_.rh/_.h) * _.w;

		return {width:_.rw, height:_.rh};
	}

	function updateListView() {
		RVS.OZ.minFullSize = 0;
		for (var i in RVS.OZ.list) {
			if (!RVS.OZ.list.hasOwnProperty(i)) continue;
			var l = RVS.OZ.list[i],
				cont = jQuery(l.row.find('.op_c_c')),
				maxbad = {index:-1, val:0},
				mingood = {index:-1, val:999999},
				def = { index:-1, val:99999},
				calW="width",optimal,
				amnt = 0,
				gamnt = 0,
				defcs = 0,
				defsize = 0,
				ok,retina,
				left,
				positions = [];

			if (l.selected!==undefined) {
				if (RVS.F.isNumeric(l.selected.size)) {
					l.currentSize = l.selected.size;
					l.currentUrl = l.selected.url;
					var size = Math.round(l.selected.size/1000) + " kb";
					l.row.find('.op_c_b')[0].innerHTML = size;
				}

				optimal = compareSize({rw:l.rw, rh:l.rh, w:l.selected.width, h:l.selected.height});
				if (optimal.width<optimal.height && l.selected.width>l.selected.height) calW = "height";
				if (l.bgsize==="contain") if (calW==="width") calW="height"; else calW="width";

			}

			if (optimal!==undefined) {
				for (var iis in l.images) {
					if (!l.images.hasOwnProperty(iis)) continue;
					var cs = ((l.images[iis][calW] / optimal[calW])*100)/3;
					if (cs>=33 && cs<=67) gamnt++;
					if (l.images[iis].default==true) {
						ok = cs>=33 && cs<=67;
						retina = cs>67;
						defcs = cs;
						defsize = l.images[iis].size;
						amnt = addMarkpoint({cont:cont, ozindex:i, imgindex:iis, left:(cs>100 ? 100 : cs), amnt:amnt, class:"selected original"})
					} else if (l.images[iis][calW]===optimal[calW] || l.images[iis][calW]*2===optimal[calW]) amnt = addMarkpoint({cont:cont, ozindex:i, imgindex:iis, left:(cs>100 ? 100 : cs), amnt:amnt})
					else if (maxbad.val<cs && cs<33) maxbad={index:iis, val:cs, size:l.images[iis].size};
					else if (mingood.val>cs && cs>67) mingood={index:iis, val:cs, size:l.images[iis].size};
					else if (cs>33 && cs<67) {
						left = (cs<38 ? 38 : cs>62 ? 62 : cs);
						for (var p in positions) if (positions.hasOwnProperty(p)) if (Math.abs(positions[p]-left)<3) left += left<positions[p] ? -3 : 3;
						positions.push(left);
						amnt = addMarkpoint({cont:cont,ozindex:i, imgindex:iis, left:left, amnt:amnt});
					}
					l.images[iis].cs = cs;
				}
				if (maxbad.index!==-1) {
					if (Math.abs(maxbad.val-defcs)<3) maxbad.val += defcs<maxbad.val ? 3 : -3;
					left = (maxbad.val<=3 ?  3 :  maxbad.val>30 ? 30 : maxbad.val);
					amnt = addMarkpoint({cont:cont,ozindex:i, imgindex:maxbad.index, left:left, amnt:amnt});
				}
				if (mingood.index!==-1 && (retina!==true || defcs>mingood.val)) {
					if (Math.abs(mingood.val-defcs)<3) mingood.val += defcs<mingood.val ? 3 : -3;
					left = (mingood.val>=97 ? 97 : mingood.val<70 ? 70 : mingood.val);
					amnt = addMarkpoint({cont:cont, ozindex:i, imgindex:mingood.index, left:left, amnt:amnt});
				}

				// COLLECT SMALLEST VALUES
				var addition = defsize;
				if (maxbad.index!==-1 && RVS.F.isNumeric(parseInt(maxbad.size,0))) addition = defsize>maxbad.size ? parseInt(maxbad.size,0) : defsize; else
				if (mingood.index!==-1 && RVS.F.isNumeric(parseInt(mingood.size,0))) addition = defsize>mingood.size ? parseInt(mingood.size,0) : defsize;
				RVS.OZ.minFullSize += addition;
			}
			var suggest = "";
			if (gamnt===0) suggest = RVS_LANG.chgimgsizesrc;
			else if (gamnt>0 && ok!==true) suggest = RVS_LANG.pickandim;
			if (suggest!=="") l.row.find('.op_c_d')[0].innerHTML = suggest;
			l.row[0].dataset.currentinfo = suggest;
		}
		RVS.OZ.minFullSize = RVS.OZ.minFullSize/1000;
		updateSummary(true);
	}

	function addMarkpoint(_) {
		_.class=_.class===undefined ? "" : _.class;
		_.cont.append('<div data-ozindex="'+_.ozindex+'" data-imgindex="'+_.imgindex+'" class="ofs_markpoint '+_.class+'" style="left:'+_.left+'%"></div>');
		return _.amnt+1;
	}

	function getImageInfos() {
		RVS.F.ajaxRequest('get_same_aspect_ratio', {images:RVS.OZ.toLoad} , function(response){
			if (response.success) {
				for (var i in response.images) {
					if (!response.images.hasOwnProperty(i)) continue;
					RVS.OZ.list[i].images = RVS.F.safeExtend(true,{},response.images[i]);
					for (var j in RVS.OZ.list[i].images) {
						if (!RVS.OZ.list[i].images.hasOwnProperty(j)) continue;
						if (RVS.OZ.list[i].images[j].default===true) RVS.OZ.list[i].selected = RVS.F.safeExtend(true,{},RVS.OZ.list[i].images[j])
					}
				}
				updateListView();

			}
		},undefined,true);
	}

	function getAddonInfos() {
		if (RVS.OZ.loadAddons===undefined || RVS.OZ.loadAddons.length==0) return;
		RVS.F.ajaxRequest('get_addons_sizes', {addons:RVS.OZ.loadAddons} , function(response){
			if (response.success) {
				for (var i in response.addons) if (response.addons.hasOwnProperty(i)) {
                    if (RVS.F.isNumeric(parseInt(response.addons[i]))) {
						var v = Math.round(parseInt(response.addons[i],0)/1000);
						jQuery('#addrowsize_'+i).html(v+" kb");
						RVS.OZ.addOnsFullSize+=v;
					}
				}
				updateListView();
			}
		},undefined,true);
	}

	function updateSummary(cache) {
		var existingurls = [];
		RVS.OZ.slideFullSize = 0;
		for (var i in RVS.OZ.list) {
			if (!RVS.OZ.list.hasOwnProperty(i)) continue;
			var l = RVS.OZ.list[i];
			if (RVS.F.isNumeric(l.currentSize)) {
				if (jQuery.inArray(l.currentUrl,existingurls)==-1) {
					existingurls.push(l.currentUrl);
					RVS.OZ.slideFullSize += Math.round(l.currentSize/1000);
					var size = Math.round(l.currentSize/1000) + " kb";
					l.row.find('.op_c_b')[0].innerHTML = size;
				} else {
					// DOUBLE LOADED, MAYBE MARK SOMEHOW ?
				}
			}
		}
		RVS.OZ.fontsFullSize = 0;
		for (var j in RVS.OZ.fontFamilies) {
			if (!RVS.OZ.fontFamilies.hasOwnProperty(j)) continue;
			if (RVS.OZ.fontFamilies[j].size!==undefined && RVS.F.isNumeric(parseInt(RVS.OZ.fontFamilies[j].size)))
				RVS.OZ.fontsFullSize += Math.round(parseInt(RVS.OZ.fontFamilies[j].size) / 1000);
		}
		if (cache===true) RVS.OZ.slideFullSizeCache = RVS.OZ.slideFullSize;

		var std = parseInt(RVS.OZ.fontsFullSize)+parseInt(RVS.OZ.addOnsFullSize)+parseInt(RVS.OZ.coreFullSize),
			bef = parseInt(RVS.OZ.slideFullSizeCache)+std,
			mini = parseInt(RVS.OZ.minFullSize)+std,
			aft = parseInt(RVS.OZ.slideFullSize)+std,
			proc = Math.round((mini/bef)*100),
			diffproc = Math.round(((bef-aft)/bef)*100),
			diftxt = (bef-aft)===0 ? "" : " ( "+(bef<aft ? "+" : "-")+Math.abs((diffproc))+"% ~ "+(bef-aft)+ " kb)";


		jQuery('#optimizer_fullsize').html(aft+" kb");
		jQuery('#optimizer_cachedsize').html((RVS.OZ.slideFullSizeCache+std)+" kb");
		jQuery('#optimized_prec').html(RVS_LANG.sizeafteroptim+diftxt);
		jQuery('#optimize_to_save').html(RVS_LANG.loadedmediainfo+" "+(100-proc)+"% ~ "+(bef-mini)+" kb");
	}

	function optimizerListener() {
		if (RVS.S.optimizerListener!==undefined) return;
		// CLOSE EDITOR
		RVS.DOC.on('click','#rbm_optimizer .rbm_close' , function() {
			RVS.F.RSDialog.close();
		});

		RVS.DOC.on('click','.optim_checkbox',function() {
			jQuery(this).toggleClass("checked");
		});

		RVS.DOC.on('mouseenter','.ofsinfo',function(e) {
			var el = jQuery(this),
				row = el.closest('.optim_row'),
				_d = row.find('.op_c_d');
			_d[0].innerHTML = 'Optimal: '+this.dataset.optim;
		});
		RVS.DOC.on('mouseleave','.ofsinfo',function(e) {
			var el = jQuery(this),
				row = el.closest('.optim_row'),
				_d = row.find('.op_c_d');
			row[0].dataset.currentinfo = row[0].dataset.currentinfo===undefined ? "" : row[0].dataset.currentinfo;
			_d[0].innerHTML = row[0].dataset.currentinfo;
		});

		RVS.DOC.on('click mouseenter','.ofs_markpoint',function(e) {

			var el = jQuery(this),
				row = el.closest('.optim_row'),
				_b = row.find('.op_c_b'),
				_d = row.find('.op_c_d'),
				_e = row.find('.op_c_e'),
				uid = row[0].dataset.uid,
				img = RVS.OZ.list[uid].images[el[0].dataset.imgindex];


			if (e.type==="mouseenter") {
				RVS.OZ.list[uid].lastSize = RVS.OZ.list[uid].currentSize;
				RVS.OZ.list[uid].lastUrl = RVS.OZ.list[uid].currentUrl;
			} else {
				if (el.hasClass("selected")) return;
				RVS.OZ.list[uid].lastSize=img.size;
				RVS.OZ.list[uid].lastUrl=img.url;
			}

			RVS.OZ.list[row[0].dataset.uid].currentSize = img.size;
			RVS.OZ.list[row[0].dataset.uid].currentUrl = img.url;
			_d[0].innerHTML = 'Dimension: '+img.width+' x '+img.height;
			if (RVS.F.isNumeric(img.size)) _b[0].innerHTML = Math.round(img.size/1000) + " kb";

			if (e.type!=="mouseenter") {
				RVS.OZ.list[uid].pickedImage = el[0].dataset.imgindex;
				row.find('.ofs_markpoint.selected').removeClass("selected");
				el.addClass("selected");
				row[0].dataset.currentinfo = img.cs<31 || img.cs>69 ? RVS_LANG.pickandim : "";
				if (img.default!==true) _e[0].innerHTML = '<i class="material-icons">flash_on</i>'; else _e[0].innerHTML='';

			}
			updateSummary();
		});

		RVS.DOC.on('mouseleave','.ofs_markpoint',function() {
			var el = jQuery(this),
				row = el.closest('.optim_row'),
				b = row.find('.op_c_b'),
				d = row.find('.op_c_d'),
				uid = row[0].dataset.uid;

			if (RVS.OZ.list[uid].lastSize!==RVS.OZ.list[uid].currentSize) {
				RVS.OZ.list[uid].currentSize = RVS.OZ.list[uid].lastSize;
				RVS.OZ.list[uid].currentUrl = RVS.OZ.list[uid].lastUrl;
				if (RVS.F.isNumeric(RVS.OZ.list[uid].lastSize)) b[0].innerHTML = Math.round(RVS.OZ.list[uid].lastSize/1000) + " kb";
				updateSummary();
			}
			row[0].dataset.currentinfo = row[0].dataset.currentinfo===undefined ? "" : row[0].dataset.currentinfo;
			d[0].innerHTML = row[0].dataset.currentinfo;
		});

		RVS.DOC.on('click','#rbm_optpanel_savebtn',function() {
			var changes = [],
				layers = [];
			for (var i in RVS.OZ.list) {
				if (!RVS.OZ.list.hasOwnProperty(i) || RVS.OZ.list[i].pickedImage===undefined) continue;
				var l = RVS.OZ.list[i];
				var img = l.images[l.pickedImage];
				if (img.default!==true) {
					if (jQuery.inArray(l.slideid,changes)==-1) changes.push(l.slideid);
					if (l.layerid!==undefined) layers.push({slideid:l.slideid, layerid:l.layerid, type:l.imgtype, url:img.url});
					writeDeepPath(RVS.OZ, l.pathURL, img.url);
					writeDeepPath(RVS.OZ, l.pathSize, l.pickedImage);
				}
			}

			if (changes.length>0) {
				if (RVS.SLIDER !== undefined && typeof RVS.SLIDER.inWork != 'undefined') {
					for (i in changes) if (changes.hasOwnProperty(i)) {
						if (changes[i]!=="settings" && jQuery.inArray(changes[i],RVS.SLIDER.inWork)==-1) RVS.SLIDER.inWork.push(changes[i]);
						RVS.SLIDER[changes[i]] = RVS.F.safeExtend(true,RVS.SLIDER[changes[i]],RVS.OZ[changes[i]]);
					}
					for (i in layers) {
						if(!layers.hasOwnProperty((i))) continue;
						var li = layers[i],
							layer = jQuery('#_lc_'+li.slideid+"_"+li.layerid+"_");
						if (layer.length>0) {
							if (li.type==="image") layer.find('._lc_image_inside_').attr('src',li.url);
							RVS.F.drawHTMLLayer({uid:li.layerid});
						}

					}
					RVS.F.convertIDStoTxt();
					RVS.F.convertArrayToObjects();
					RVS.F.saveSlides({index:0,slides:RVS.SLIDER.slideIDs, trigger:RVS.F.saveSliderSettings,works:RVS.SLIDER.inWork});
				} else {

					for (i in changes) if (changes.hasOwnProperty(i)) {
						RVS.OZ.inWork = RVS.OZ.inWork===undefined ? [] : RVS.OZ.inWork;
						if (changes[i]!=="settings" && jQuery.inArray(changes[i],RVS.OZ.inWork)==-1) RVS.OZ.inWork.push(changes[i]);
						RVS.OZ[changes[i]] = RVS.F.safeExtend(true,RVS.OZ[changes[i]],RVS.OZ[changes[i]]);
					}
					for (i in layers) {
						if(!layers.hasOwnProperty((i))) continue;
						var li = layers[i],
							layer = jQuery('#_lc_'+li.slideid+"_"+li.layerid+"_");
						if (layer.length>0) {
							if (li.type==="image") layer.find('._lc_image_inside_').attr('src',li.url);
							RVS.F.drawHTMLLayer({uid:li.layerid});
						}
					}
					convertIDStoTxt();
					convertArrayToObjects();
					saveSlides({index:0,slides:RVS.OZ.slideIDs, trigger:saveSliderSettings,works:RVS.OZ.inWork});
				}

				//SAVE CHANGES
			}
			RVS.F.RSDialog.close();
		});

		RVS.DOC.on('click','#more_about_optimizer',function() {
			RVS.F.RSDialog.create({modalid:'#rbm_optimizer_infos', bgopacity:0.5});
			RVS.F.RSDialog.center();
		});

		RVS.DOC.on('click','#rbm_optimizer_infos .rbm_close',function() {
			RVS.F.RSDialog.close();
		});

		jQuery('#rbm_optimizer_infos').closest('.rb-modal-wrapper').appendTo(jQuery(document.body));

		RVS.S.optimizerListener = true;
	}

	convertIDStoTxt = function() {
		for (var i in RVS.OZ.slideIDs) if(RVS.OZ.slideIDs.hasOwnProperty(i)) RVS.OZ.slideIDs[i] = ""+RVS.OZ.slideIDs[i];
		for (var i in RVS.OZ.inWork) if(RVS.OZ.inWork.hasOwnProperty(i)) RVS.OZ.inWork[i] = ""+RVS.OZ.inWork[i];
	}

	function saveSlides(_) {
		if (_.index < _.slides.length) {
			_.order = _.order===undefined ? 0 : _.order;
			_.order++;
			var slideindex = _.slides[_.index],
				workindex = jQuery.inArray(slideindex+"",RVS.OZ.inWork);

			if (workindex>=0) {
				var params = JSON.stringify(RVS.OZ[_.slides[_.index]].slide),
					layers = JSON.stringify(RVS.OZ[_.slides[_.index]].layers),
					options = {slider_id:RVS.ENV.sliderID, slide_id:_.slides[_.index], params:params, layers:layers, slide_order:_.order};
				RVS.DOC.trigger('rs_save_slide_params', [options]);
				RVS.F.ajaxRequest('save_slide', options, function(response){
					if(response.success) {
						_.index++;
						saveSlides(_);
					}
				},undefined,undefined,RVS_LANG.saveslide+'<br><span style="font-size:17px; line-height:25px;">"'+RVS.OZ[_.slides[_.index]].slide.title+'"</span>');

			} else {
				_.index++;
				saveSlides(_);
			}
		} else {
			RVS.OZ.inWork = RVS.OZ.inWork===undefined ? [] : RVS.OZ.inWork;
			if (_.trigger!==undefined) _.trigger();
		}
	};

	function convertArrayToObjects() {
		RVS.OZ.settings.nav.arrows.presets = Object.assign({},RVS.OZ.settings.nav.arrows.presets);
		RVS.OZ.settings.nav.bullets.presets = Object.assign({},RVS.OZ.settings.nav.bullets.presets);
		RVS.OZ.settings.nav.thumbs.presets = Object.assign({},RVS.OZ.settings.nav.thumbs.presets);
		RVS.OZ.settings.nav.tabs.presets = Object.assign({},RVS.OZ.settings.nav.tabs.presets);
		if (RVS.OZ.settings.skins!==undefined && RVS.OZ.settings.skins.colors!==undefined)  RVS.OZ.settings.skins.colors = Object.assign({},RVS.OZ.settings.skins.colors);

	};



	function saveSliderSettings() {
		var params = JSON.stringify(RVS.OZ.settings),
			slideids = RVS.OZ.slideIDs.slice(),
			staticindex = -1;
			for (var si in slideids) {
				if(!slideids.hasOwnProperty(si)) continue;
				if ((""+slideids[si]).indexOf("static")>=0) staticindex = si;
			}
			if (staticindex > -1) {
				slideids.splice(staticindex,1);
			}
		RVS.F.ajaxRequest('save_slider', {slider_id:RVS.ENV.sliderID, params:params, slide_ids:/*RVS.OZ.slideIDs*/slideids}, function(response){
			if (response.success && response.missing!==undefined && response.missing.length>0) saveSlides({index:0,slides:RVS.OZ.slideIDs,works:response.missing});
		},undefined,undefined,RVS_LANG.saveslide+'<br><span style="font-size:17px; line-height:25px;">'+RVS_LANG.slidersettings+'</span>');
	};


    // write deep value from path
	function writeDeepPath(obj, path, val,ds) {
		if(typeof path !== 'string') return;

		var paths = path.split('.'),
			len = paths.length,
			total = len - 1,
			data = obj;
		if(!len) return;
		for(var i = 0; i < len; i++) {
			if (i< total && data[paths[i]]===undefined) data[paths[i]] = {};
			if(i < total) data = data[paths[i]];
			else data[paths[i]] = val;
		}
	}

	function getDeepValue(obj, path,ds) {
		if(typeof path === 'string') path = path.split('.');
		if(path.length > 1) {
			var prop = path.shift();
			if (prop==='#slider#') prop = ds.slider;
			if (prop==='#slide#') prop = ds.slide;
			if (prop==='#layer#') prop = ds.layer;
			return obj.hasOwnProperty(prop) ? getDeepValue(obj[prop], path,ds) : undefined;
		}
		return obj.hasOwnProperty(path[0]) ? obj[path[0]] : undefined;
	}



	//SAVE ORIGINAL DATA STRUCTURES BEFORE START TO EDITING THEM
	function replaceId(a,w,b) { while (a.indexOf(w)>=0) a = a.replace(w,b); return a;}
	function shorten(a,n){ return (a.length > n) ? a.substr(0, n-1) + '&hellip;' : a;};

})();