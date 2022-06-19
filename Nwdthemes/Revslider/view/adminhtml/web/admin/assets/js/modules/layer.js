/*!
 * REVOLUTION 6.0.0 EDITOR LAYER JS
 * @version: 1.0 (08.03.2019)
 * @author ThemePunch
*/

// GLOBAL PARAMETERS FOR VIDEO AND IMAGE URL
;RVS.S.keyFrame = "idle";
RVS.S.frameTrgt = "layer";
RVS.V.frameLevels = {levels:["mask","chars","words","lines","color","sfx"]};
RVS.LIB.FONTS = [];
RVS.CACHE = RVS.CACHE || {};

(function() {

	var fontNameCompare,
		fontWeight,
		fontWeightOptions,
		fontWaitToLoad,
		keyframe_idle_sel,
		keyframe_lists,
		loadedFonts = [],
		qflolist;

	/*
	INITIALISE THE BASIC LISTENERS, INPUT MANAGEMENTS ETC
	*/
	RVS.F.initLayerBuilder = function() {
		RVS.C.slit = document.getElementById("selected_layers_icon_toolbar");
		RVS.ENV.video_ph_url = RVS.ENV.img_ph_url = RVS.ENV.plugin_url+"admin/assets/images/transparent_placeholder.png";
		createLayerAnimationLists();
		initLocalInputBoxes();
		initLocalListeners();
	};

	// UPDATE CUSTOM CSS FOR A LAYER
	RVS.F.updateCustomCSS = function() {
		if (window.customLayerCss_editor ==="FAIL") return;
		if (typeof RevMirror==="undefined") {
			RVS.F.showWaitAMinute({fadeIn:500,text:RVS_LANG.loadingRevMirror});
			RVS.F.loadCSS(RVS.ENV.plugin_url+'/admin/assets/css/RevMirror.css');
			jQuery.getScript(RVS.ENV.plugin_url+'/admin/assets/js/plugins/RevMirror.js',function() {
				setTimeout(function() {RVS.F.showWaitAMinute({fadeOut:500});},100);
				RVS.F.updateCustomCSS();
			}).fail(function(a,b,c) {
				setTimeout(function() {RVS.F.showWaitAMinute({fadeOut:500});},100);
				window.customLayerCss_editor = "FAIL";
			});
		}  else
		if (window.customLayerCss_editor===undefined) {
			// INITIALISE THE CSS EDITOR FOR LAYER IDLE AND HOVER
			window.customLayerCss_editor = RevMirror(document.getElementById('custom_css_layer_area'), {
				value:"",
				mode:"css",
				theme:"hopscotch",
				lineWrapping:true,
				lineNumbers:false
			});
			window.customLayerCss_editor.on('focus',function() { window.customLayerCss_editor.refresh();});
			window.customLayerCss_editor.on('change',function(cmi,event) {
				RVS.L[RVS.selLayers[0]].customCSS = window.customLayerCss_editor.getValue();
			});
			setTimeout(RVS.F.updateCustomCSS,200);
		} else

		if (window.customHoverLayerCss_editor===undefined) {
			// INITIALISE THE CSS EDITOR FOR LAYER IDLE AND HOVER
			window.customHoverLayerCss_editor = RevMirror(document.getElementById('custom_css_hover_layer_area'), {
				value:"",
				mode:"css",
				theme:"hopscotch",
				lineWrapping:true,
				lineNumbers:false
			});
			window.customHoverLayerCss_editor.on('focus',function() { window.customHoverLayerCss_editor.refresh();});
			window.customHoverLayerCss_editor.on('change',function(cmi,event) {
				RVS.L[RVS.selLayers[0]].customHoverCSS = window.customHoverLayerCss_editor.getValue();
			});
			setTimeout(RVS.F.updateCustomCSS,200);
		} else {
			RVS.F.updateCusCSSContent();
		}

	};

	RVS.F.updateCusCSSContent = function() {

		if (RVS.selLayers.length>0 && window.customLayerCss_editor!=undefined) {
			window.customLayerCss_editor.setValue(RVS.L[RVS.selLayers[0]].customCSS);
			setTimeout(function() {window.customLayerCss_editor.refresh();},200);
		}
		if (RVS.selLayers.length>0 && window.customHoverLayerCss_editor!==undefined) {
			window.customHoverLayerCss_editor.setValue(RVS.L[RVS.selLayers[0]].customHoverCSS);
			setTimeout(function() {window.customHoverLayerCss_editor.refresh();},200);
		}

	}



	RVS.F.compareGoogleFontName = function(fontname,lower) {
		if (fontname===undefined || fontname==="" || fontname.length==0) return;
		fontNameCompare = fontNameCompare===undefined ? {source:[], result:[]} : fontNameCompare;
		var found = jQuery.inArray(fontname,fontNameCompare.source),
			i=0;
		if (found>=0) return fontNameCompare.result[found];
		found = false;
		fontNameCompare.source.push(fontname);
		while (i<RVS.LIB.FONTS.length && !found) {
			if (fontname===RVS.LIB.FONTS[i].labelLowerCase || fontname===RVS.LIB.FONTS[i].label || (lower && fontname.toLowerCase()===RVS.LIB.FONTS[i].labelLowerCase)) {
				fontname = RVS.LIB.FONTS[i].label;
				found = true;
			}
			i++;
		}
		fontNameCompare.result.push(fontname);
		return fontname;
	};


	/*
	INITISALISE THE LOADED GOOGLE FONT DROP DOWN MENU
	*/
	RVS.F.initFontTypes = function(jsonClasses) {

		//return; // KRIKI, REMOVE IT AT THE END
		// set init font family types array
		RVS.LIB.FONTS = JSON.parse(jsonClasses);
		var lff = jQuery('#layer_fontfamily'),
			opts = "";
		for (var fontindex in RVS.LIB.FONTS) {
			if(!RVS.LIB.FONTS.hasOwnProperty(fontindex)) continue;
			if (RVS.LIB.FONTS[fontindex].label!=="Dont Show Me" && RVS.LIB.FONTS[fontindex].label!=="") opts += "<option value='"+RVS.LIB.FONTS[fontindex].label+"'>"+(RVS.LIB.FONTS[fontindex].label.split('"').join(''))+"</option>";
			RVS.LIB.FONTS[fontindex].labelLowerCase = RVS.LIB.FONTS[fontindex].label.toLowerCase();
		}
		lff[0].innerHTML = opts;
		lff.ddTP('change');

		if (qflolist===undefined) {
			qflolist = true;


			RVS.DOC.on('mouseenter','.ddTP_C--fontfamily .ddTP_RO',function() {
				if (this.dataset.val===undefined) this.dataset.val = this.innerHTML;
				var family = this.dataset.val.replace(/\ /g,'_');
				fontWaitToLoad = this.dataset.val;
				if (loadedFonts[family]!==undefined) RVS.F.showTextLayerWithFont({family:fontWaitToLoad});
			});

			RVS.DOC.on('mouseleave','.ddTP_C--fontfamily',function() {
				RVS.F.resetFontFamiliesOnSelectedLayers();
			});
		}
	};

	/*
	CHECK THE CURRENT SELECTED LAYERS FONT WEIGHTS
	*/
	RVS.F.checkAvailableFontWeights = function(fontWeightChange) {

		if (RVS.selLayers.length===0) return;

		fontWeight = fontWeight===undefined ? jQuery('#layer_fontweight_idle') : fontWeight;
		if (fontWeightOptions === undefined) {
			fontWeightOptions = [];
			fontWeight.find('option').each(function() {
				fontWeightOptions.push({option:this, state:true});
			});
		}
		var selectedFonts=[];



		// COLLECT SELECTED FONT FAMILIES
		for (var sl in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(sl)) continue;
			var i = RVS.selLayers[sl],
				l = RVS.L[i];
			if (l.type==="text" || l.type==="button")
				if (jQuery.inArray(l.idle.fontFamily,selectedFonts)==-1) selectedFonts.push({family:l.idle.fontFamily, weights:[]});
		}
		for (var oind in fontWeightOptions) {
			if(!fontWeightOptions.hasOwnProperty(oind)) continue;
			fontWeight.find('option[value="'+fontWeightOptions[oind].option.value+'"]').prop('disabled', false);
			fontWeightOptions[oind].state = true;
		}


		// DISABLE NONE USED FONT WEIGHTS
		for (var sfi in selectedFonts) {
			if(!selectedFonts.hasOwnProperty(sfi)) continue;
			var ffam =selectedFonts[sfi].family;
			if (ffam.length>0)
				for (var fontindex in RVS.LIB.FONTS) {
					if(!RVS.LIB.FONTS.hasOwnProperty(fontindex)) continue;
					var font = RVS.LIB.FONTS[fontindex];
					if (font.label === ffam) {
						for (var oind in fontWeightOptions) {
							if(!fontWeightOptions.hasOwnProperty(oind)) continue;
							var v = fontWeightOptions[oind].option.value;
							if (jQuery.inArray(v,font.variants)>=0 || font.type==="websafe") {
								selectedFonts[sfi].weights.push(v);
							} else
							if (fontWeightOptions[oind].state) {
								fontWeight.find('option[value="'+v+'"]').prop('disabled','disabled');
								fontWeightOptions[oind].state = false;
							}
						}
					}
				}
		}
		var changed = false,
			backupNeeded = false;

		// UPDATE FONT WEIGHTS ON SELECTED LAYERS IF NEEDED
		for (var sl in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(sl)) continue;
			var i = RVS.selLayers[sl],
				l = RVS.L[i];
			if (l.type==="text" || l.type==="button") {
				var sfIndex = -1;
				for (var sfi in selectedFonts) {
					if(!selectedFonts.hasOwnProperty(sfi)) continue;
					if (selectedFonts[sfi].family == l.idle.fontFamily && selectedFonts[sfi].family!=="") sfIndex = sfi;
				}
				if (sfIndex>=0)
					for (var ri in RVS.V.sizes) {
						if(!RVS.V.sizes.hasOwnProperty(ri)) continue;
						var s = RVS.V.sizes[ri];
						if (jQuery.inArray(l.idle.fontWeight[s].v,selectedFonts[sfIndex].weights)==-1) {
							if (backupNeeded===false && RVS.S.bckpGrp===false) {
								if (RVS.S.bckpGrp===false)
									backupNeeded="started";
								else
									backupNeeded="inProgress";
								RVS.F.openBackupGroup({id:"fontWeight",txt:"Font Weight",icon:"layers",lastkey:"layer"});

							}
							changed = true;
							var newval = getPossibleElementBefore({v:l.idle.fontWeight[s].v, a:selectedFonts[sfIndex].weights});
							RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+i+'.idle.fontWeight.'+s+".v",val:newval});
						}
					}

			}
		}

		if (backupNeeded==="started") RVS.F.closeBackupGroup({id:"fontWeight"});
		if (changed) RVS.F.updateEasyInputs({container:jQuery('#form_layerstyle_font'), path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});

		if(!fontWeightChange) {
			if(fontWeight.hasClass("ddTP-hidden-accessible")) fontWeight.ddTP('destroy');
			fontWeight.ddTP({ placeholder:"Select"});
		}

	};

	// Get The Highest Value of Array smaller than Selection
	function getPossibleElementBefore(_) {
		var r = _.a.length>0 ? _.a[0] : _.v;
		for (var i in _.a) {
			if(!_.a.hasOwnProperty(i)) continue;
			r = _.a[i]<_.v && r<_.v ? _.a[i] : r;
		}
		return r;
	}

	// GET THE PARENT CONTAINER SIZE
	function getLayerParentContainerSize(_) {
		var l = RVS.L[_.uid],
			c = l.group.puid!=-1 ? RVS.H[l.group.puid].w : RVS.C.layergrid;

		return ({width:c.width(), height:c.height()});
	}


	/*
	ALL SELECTED TEXT SHOULD BE SHOWN WITH CURRENT HOVERED GOOGLE FONT
	*/
	RVS.F.showTextLayerWithFont = function(_) {
		for (var sl in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(sl)) continue;
			var i = RVS.selLayers[sl],
				l = RVS.L[i],
				lh = RVS.H[i].c;

			if (l.type==="text" || l.type==="button")
				lh.css({fontFamily:_.family});

		}
	};
	/*
	RESET THE LAST SELECTED FONT FAMILIES ON SELECTED LAYERS
	*/
	RVS.F.resetFontFamiliesOnSelectedLayers = function() {
		for (var sl in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(sl)) continue;
			var i = RVS.selLayers[sl],
				l = RVS.L[i];

			if (l.type==="text" || l.type==="button")
				RVS.F.drawHTMLLayer({uid:i});

		}
	};

	RVS.F.getDashArray = function(a) {
		if (RVS.F.isNumeric(a) || a.indexOf(",")===-1 && a.indexOf(" ")===-1 || a.split(",").length===1 || a.split(" ").length===1)
			a = a+" "+a;
		return a;
	};

	/*
	DRAW A HTML LAYER
	*/
	RVS.F.drawHTMLLayer = function(_) {
		if (!RVS.S.drawHTMLLayersCalled) return;
		if (RVS.H[_.uid]===undefined) {
			if (_.recheck!==true) {
				_.recheck = true;
				setTimeout(function() {RVS.F.drawHTMLLayer(_);},500);
			} else return;
		}

		var lh = RVS.H[_.uid],
			l = RVS.L[_.uid],
			hfilter;

		if (lh===undefined) return;
		var pc = lh.c[0].className.indexOf("placeholder_on")>=0;

		lh.c[0].className = "_lc_content_"+(pc ? " placeholder_on" : "")+(l.idle.style!==undefined ? " "+l.idle.style : "") + (l.runtime.internalClass!==undefined ? " "+l.runtime.internalClass : "");

		var	tr = {
					textAlign:l.idle.textAlign[RVS.screen].v,
					boxSizing:"border-box",
					transformStyle:l.timeline.split ? "preserve-3d" : "flat",  // Based on if the Layer has splitted element or not
					//transformPerspective:l.idle.transformPerspective,
					//transformOrigin:l.idle.originX+" "+l.idle.originY,
					fontFamily:l.idle.fontFamily || 'Roboto',
					fontSize:parseInt(l.idle.fontSize[RVS.screen].v,0)+"px",
					lineHeight:parseInt(l.idle.lineHeight[RVS.screen].v,0)+"px",
					fontWeight: (l.idle.fontWeight[RVS.screen].v===undefined ? 400 : l.idle.fontWeight[RVS.screen].v),
					color: window.RSColor.get(l.idle.color[RVS.screen].v),
					letterSpacing:parseFloat(l.idle.letterSpacing[RVS.screen].v)+"px",
					/* textAlign:l.idle.textAlign[RVS.screen].v, */
					fontStyle:(l.idle.fontStyle==="normal" || l.idle.fontStyle===false ? "normal" : "italic"),
					textDecoration:l.type==="row" || l.type==="column" || l.type==="group" ? "none" : l.idle.textDecoration,
					textTransform:l.idle.textTransform,
					borderColor:l.type==="column" ? "transparent" : window.RSColor.get(l.idle.borderColor),
					borderRadius:l.idle.borderRadius.v[0]+" "+l.idle.borderRadius.v[1]+" "+l.idle.borderRadius.v[2]+" "+l.idle.borderRadius.v[3]+" ",
					borderWidth:l.idle.borderWidth[0]+" "+l.idle.borderWidth[1]+" "+l.idle.borderWidth[2]+" "+l.idle.borderWidth[3]+" ",
					borderStyle:l.idle.borderStyle[RVS.screen].v,
					width:l.size.width[RVS.screen].v,
					height:l.size.height[RVS.screen].v,
					whiteSpace:(l.idle.whiteSpace[RVS.screen].v=="normal" || l.idle.whiteSpace[RVS.screen].v=="full") ? "normal" : "nowrap",

					paddingTop:l.idle.padding[RVS.screen].v[0],
					paddingRight:l.idle.padding[RVS.screen].v[1],
					paddingBottom:l.idle.padding[RVS.screen].v[2],
					paddingLeft:l.idle.padding[RVS.screen].v[3],


					minWidth:l.size.minWidth[RVS.screen].v==="none" ? 0 : l.size.minWidth[RVS.screen].v,
					minHeight:l.size.minHeight[RVS.screen].v==="none" ? 0 : l.size.minHeight[RVS.screen].v,
					maxWidth:l.size.maxWidth[RVS.screen].v,
					maxHeight:l.size.maxHeight[RVS.screen].v,
					boxShadow:(l.idle.boxShadow.inuse ? l.idle.boxShadow.color+" "+l.idle.boxShadow.hoffset[RVS.screen].v+" "+l.idle.boxShadow.voffset[RVS.screen].v+" "+l.idle.boxShadow.blur[RVS.screen].v+" "+l.idle.boxShadow.spread[RVS.screen].v+" " : "none"),

					/*maxWidth:"100%",
					maxHeight:"100%"*/
				},
			  htr = {
			  		rotationX:l.hover.rotationX,
					rotationY:l.hover.rotationY,
					rotationZ:l.hover.rotationZ,
					autoAlpha:l.hover.opacity,
					transformPerspective:RVS.SLIDER.settings.general.perspective, //l.hover.transformPerspective,
					transformOrigin:l.hover.originX+" "+l.hover.originY+" "+l.hover.originZ,
					skewX:l.hover.skewX,
					skewY:l.hover.skewY,
					scaleX:l.hover.scaleX,
					scaleY:l.hover.scaleY,
					borderColor:window.RSColor.get(l.hover.borderColor),
					borderTopLeftRadius : l.hover.borderRadius.v[0],
					borderTopRightRadius : l.hover.borderRadius.v[1],
					borderBottomRightRadius : l.hover.borderRadius.v[2],
					borderBottomLeftRadius : l.hover.borderRadius.v[3],
					borderTopWidth:l.hover.borderWidth[0],
					borderRightWidth:l.hover.borderWidth[1],
					borderBottomWidth:l.hover.borderWidth[2],
					borderLeftWidth:l.hover.borderWidth[3],
					borderStyle:l.hover.borderStyle,
					color: window.RSColor.get(l.hover.color),
					textDecoration:l.hover.textDecoration,

			  },
			w_tr = l.type==="row"  ?
					{
						marginTop:l.idle.margin[RVS.screen].v[0],
						paddingRight:l.idle.margin[RVS.screen].v[1],
						marginBottom:l.idle.margin[RVS.screen].v[2],
						paddingLeft:l.idle.margin[RVS.screen].v[3]
					} :  l.type==="column" ?

					{
						paddingTop:l.idle.margin[RVS.screen].v[0],
						paddingRight:l.idle.margin[RVS.screen].v[1],
						paddingBottom:l.idle.margin[RVS.screen].v[2],
						paddingLeft:l.idle.margin[RVS.screen].v[3]
					}:
					{
						marginTop:l.idle.margin[RVS.screen].v[0],
						marginRight:l.idle.margin[RVS.screen].v[1],
						marginBottom:l.idle.margin[RVS.screen].v[2],
						marginLeft:l.idle.margin[RVS.screen].v[3]
					},

			//GET THE REAL COLOR VALUES
			bgcolor = window.RSColor.get(l.idle.backgroundColor),
			hbgcolor = window.RSColor.get(l.hover.backgroundColor),

			/* SET THE BACKGROUND IMAGE FOR THE LAYER */
			temp_bgimage = l.type==="video" ? l.media.posterUrl : l.idle.backgroundImage,

			//BG TRANSFORM FOR COLUMNS AND ROWS
			bg_tr = {},
			bg_htr = {};




		if (l.visibility[RVS.screen]===false)
			w_tr.opacity = 0.25;
		else
			w_tr.opacity = 1;

		if (l.idle.textShadow.inuse)
			tr.textShadow = l.idle.textShadow.hoffset[RVS.screen].v+" "+l.idle.textShadow.voffset[RVS.screen].v+" "+l.idle.textShadow.blur[RVS.screen].v+" "+l.idle.textShadow.color;
		else
			tr["text-shadow"] = "none";

		if (l.idle.textStroke.inuse)
			tr['-webkit-text-stroke'] = parseInt(l.idle.textStroke.width[RVS.screen].v,0)+"px"+" "+l.idle.textStroke.color;
		else
			tr["-webkit-text-stroke"] = "0 transparent";


		if (l.type=="column") {
			bg_tr.borderColor = window.RSColor.get(l.idle.borderColor);
			bg_tr.borderWidth = l.idle.borderWidth[0]+" "+l.idle.borderWidth[1]+" "+l.idle.borderWidth[2]+" "+l.idle.borderWidth[3]+" ";
			bg_tr.borderStyle = l.idle.borderStyle[RVS.screen].v;
		}

		// IDLE BG COLOR CHECK
		if (temp_bgimage!==undefined && temp_bgimage.length>4 && bgcolor.indexOf("gradient")==-1) {
			if (l.type==="column" || l.type==="row") {
				bg_tr.backgroundImage = 'url("'+temp_bgimage+'")';
				bg_tr.backgroundPosition = l.idle.backgroundPosition;
				bg_tr["background-size"] = l.idle.backgroundSize==="percentage" ? l.idle.backgroundSizePerc+"%" : l.idle.backgroundSize==="pixel" ? l.idle.backgroundSizePix+"px" : l.idle.backgroundSize;
				bg_tr.backgroundRepeat = l.idle.backgroundRepeat;
			} else {
				tr.backgroundImage ='url("'+temp_bgimage+'")';
				tr.backgroundPosition = l.idle.backgroundPosition;
				tr["background-size"] = l.idle.backgroundSize==="percentage" ? l.idle.backgroundSizePerc+"%" : l.idle.backgroundSize==="pixel" ? l.idle.backgroundSizePix+"px" : l.idle.backgroundSize;
				tr.backgroundRepeat = l.idle.backgroundRepeat;
				if (bgcolor!=="transparent") tr.backgroundColor = bgcolor;
				if (hbgcolor!=="transparent") htr.backgroundColor = hbgcolor;
			}
		} else {
			if (l.type==="column" || l.type==="row") { bg_tr.backgroundImage = "";	 bg_tr.transformStyle="preserve-3d"; } else tr.background = bgcolor;
			if (hbgcolor.indexOf("gradient")==-1) htr.backgroundColor = hbgcolor; else htr.background = hbgcolor;
		}



		if (l.type==="column" || l.type==="row") {
			bg_tr.left = l.type==="column" ? 0 : l.idle.margin[RVS.screen].v[3];
			bg_tr.right = l.type==="column" ? 0 : l.idle.margin[RVS.screen].v[1];
			bg_tr.top = 0;
			bg_tr.bottom = 0;
			bg_tr.borderRadius = l.idle.borderRadius.v[0]+" "+l.idle.borderRadius.v[1]+" "+l.idle.borderRadius.v[2]+" "+l.idle.borderRadius.v[3]+" ";
			tpGS.gsap.set(lh.bg,bg_tr);
			if (l.type==="column") tpGS.gsap.set(lh.bgmask,{top:l.idle.margin[RVS.screen].v[0],bottom:l.idle.margin[RVS.screen].v[2],left:l.idle.margin[RVS.screen].v[3],right:l.idle.margin[RVS.screen].v[1] })

			var bws = [parseInt(l.idle.borderWidth[0]),parseInt(l.idle.borderWidth[1]),parseInt(l.idle.borderWidth[2]),parseInt(l.idle.borderWidth[3])];

			// DRAW MARGINS
			tpGS.gsap.set(lh.margins.top,{height:l.idle.margin[RVS.screen].v[0], top:(l.type==="row" ? (0-l.idle.margin[RVS.screen].v[0]) : "0"), paddingRight:l.idle.margin[RVS.screen].v[1],paddingLeft:l.idle.margin[RVS.screen].v[3]});
			tpGS.gsap.set(lh.margins.bottom,{height:l.idle.margin[RVS.screen].v[2], bottom:(l.type==="row" ? (0-l.idle.margin[RVS.screen].v[2]) : "0"), paddingRight:l.idle.margin[RVS.screen].v[1],paddingLeft:l.idle.margin[RVS.screen].v[3]});
			tpGS.gsap.set(lh.margins.left,{width:l.idle.margin[RVS.screen].v[3], lineHeight:l.idle.margin[RVS.screen].v[0]});
			tpGS.gsap.set(lh.margins.right,{width:l.idle.margin[RVS.screen].v[1]});

			// DRAW PADDINGS
			tpGS.gsap.set(lh.paddings.top,{height:l.idle.padding[RVS.screen].v[0]+bws[0], top:l.type==="row" ? 0 : l.idle.margin[RVS.screen].v[0], paddingRight:(l.idle.padding[RVS.screen].v[1] + l.idle.margin[RVS.screen].v[1] + bws[1]) ,paddingLeft:(l.idle.padding[RVS.screen].v[3]+l.idle.margin[RVS.screen].v[3] +bws[3])});
			tpGS.gsap.set(lh.paddings.bottom,{height:l.idle.padding[RVS.screen].v[2]+bws[2], bottom:l.type==="row" ? 0 : l.idle.margin[RVS.screen].v[2], paddingRight:(l.idle.padding[RVS.screen].v[1] + l.idle.margin[RVS.screen].v[1] + bws[1]) ,paddingLeft:(l.idle.padding[RVS.screen].v[3]+l.idle.margin[RVS.screen].v[3] +bws[3])});
			tpGS.gsap.set(lh.paddings.left,{left:l.idle.margin[RVS.screen].v[3], width:l.idle.padding[RVS.screen].v[3]+bws[3], paddingTop:l.type==="row" ? 0 : l.idle.margin[RVS.screen].v[0], paddingBottom:l.type==="row" ? 0 : l.idle.margin[RVS.screen].v[2]});
			tpGS.gsap.set(lh.paddings.right,{right:l.idle.margin[RVS.screen].v[1], width:l.idle.padding[RVS.screen].v[1]+bws[1], paddingTop:l.type==="row" ? 0 : l.idle.margin[RVS.screen].v[0], paddingBottom:l.type==="row" ? 0 : l.idle.margin[RVS.screen].v[2]});

			if (l.type==="column") {
				// DRAW BORDERS
				tpGS.gsap.set(lh.borders.top,{top:l.idle.margin[RVS.screen].v[0],left:l.idle.margin[RVS.screen].v[3], right:l.idle.margin[RVS.screen].v[1], width:"auto"});
				tpGS.gsap.set(lh.borders.right,{right:l.idle.margin[RVS.screen].v[1],top:l.idle.margin[RVS.screen].v[0],bottom:l.idle.margin[RVS.screen].v[2], height:"auto"});
				tpGS.gsap.set(lh.borders.bottom,{bottom:l.idle.margin[RVS.screen].v[2],left:l.idle.margin[RVS.screen].v[3], right:l.idle.margin[RVS.screen].v[1], width:"auto"});
				tpGS.gsap.set(lh.borders.left,{left:l.idle.margin[RVS.screen].v[3],top:l.idle.margin[RVS.screen].v[0],bottom:l.idle.margin[RVS.screen].v[2], height:"auto"});

			} else {
				tpGS.gsap.set([lh.borders.bottom,lh.borders.top],{left:l.idle.margin[RVS.screen].v[3], right:l.idle.margin[RVS.screen].v[1], width:"auto"});
				tpGS.gsap.set(lh.borders.right,{right:l.idle.margin[RVS.screen].v[1]});
				tpGS.gsap.set(lh.borders.left,{left:l.idle.margin[RVS.screen].v[3]});
			}
		} else
		if (l.type==="video") {
			lh.vtitle.textContent = l.media.mediaType;
			lh.volay.style.backgroundImage = RVS._R.createOverlay("slide",l.media.dotted,l.media.dottedSize,{0:l.media.dottedColorA,1:l.media.dottedColorB});
			//lh.volay.className="_lc_video_overlay "+l.media.dotted;
		} else
		if (l.type==="audio") {
			if (l.media.controls===false) {
				lh.c[0].classList.add("placeholder_on");
				lh.c[0].classList.remove("audio_controls_on");
			} else {
				lh.c[0].classList.add("audio_controls_on");
				lh.c[0].classList.remove("placeholder_on");
			}
		} else
		if (l.type==="shape") lh.c[0].classList[l.idle.bgFromStream ? "add" : "remove"]("placeholder_on");


		w_tr.verticalAlign = "inherit";

		//DISPLAY AND VERTICAL ALIGN FOR COLUMNS
		if (l.type==="column") {
			w_tr.verticalAlign = l.idle.verticalAlign;
			tr.verticalAlign = l.idle.verticalAlign;
		}

		// DISPLAY (BLOCK, INLINE BOCK) FOR LAYERS IN COLUMNS
		if (l.group.puid!==-1 && RVS.L[l.group.puid].type==="column") {
			w_tr.display = tr.display = l.idle.display;
			w_tr.float = l.idle.float[RVS.screen].v;
			w_tr.clear = l.idle.clear[RVS.screen].v;
		}
		else if (l.type!=="row" && l.type!=="column") {
			tr.display = "block";
			w_tr.clear = "none";
		}


		// ADD LAYER FILTER
		if (l.hover.filter.blur!==undefined) {
			hfilter = 'blur('+parseInt(l.hover.filter.blur,0)+'px)';
			htr['-webkit-filter'] = htr['-webkit-filter']===undefined ? hfilter : htr['-webkit-filter']+' '+hfilter;
			htr.filter = htr.filter===undefined ? hfilter: htr.filter+' '+hfilter;
		}

		if (l.hover.filter.grayscale!==undefined) {
			hfilter = 'grayscale('+parseInt(l.hover.filter.grayscale,0)+'%)';
			htr['-webkit-filter'] = htr['-webkit-filter']===undefined ? hfilter : htr['-webkit-filter']+' '+hfilter;
			htr.filter = htr.filter===undefined ? hfilter: htr.filter+' '+hfilter;

		}

		if (l.hover.filter.brightness!==undefined) {
			hfilter = 'brightness('+parseInt(l.hover.filter.brightness,0)+'%)';
			htr['-webkit-filter'] = htr['-webkit-filter']===undefined ? hfilter : htr['-webkit-filter']+' '+hfilter;
			htr.filter = htr.filter===undefined ? hfilter: htr.filter+' '+hfilter;
		}
		if (htr.filter==="blur(0px) grayscale(0%) brightness(100%)") {
			htr.filter="";
			htr['-webkit-filter']="";
		}
		l.timeline.hoverFilterUsed = htr.filter!==undefined && htr.filter!=="";

		//SET UP WIDTH AND HEIGHT OF LAYERS
		lh.w_width = tr.width = l.size.width[RVS.screen].v=="auto" ? "auto" : RVS.F.smartConvertDivs(l.size.width[RVS.screen].v);
		lh.w_height = tr.height = l.size.height[RVS.screen].v=="auto" ? "auto" : RVS.F.smartConvertDivs(l.size.height[RVS.screen].v);

		if (l.type==="image") tr.overflow="hidden";

		// var exp = eval(l.group.columnSize);
		var exp = RVS.F.convertFraction(l.group.columnSize);

		//COLUMN DIMENSION MUST BE HANDLED DIFFERENT
		w_tr.width = l.type=="column" ?  (100*exp)+"%" : lh.w_width;
		w_tr.maxWidth = l.type=="column" ? "0px" : w_tr.maxWidth===undefined ? "none" : w_tr.maxWidth;
		w_tr.height = l.type=="column" || l.type=="row" ? "auto" : lh.w_height;

		tr.width = l.type=="column"  || l.type=="row" ? "100%" : tr.width;
		tr.height = l.type=="column" || l.type=="row"  ? "auto" : tr.height;

		// NEW COVER MODE IF LAYER ALIGNED TO STAGE AND NOT TO CONTENT
		var slideBased_tw_wtr = l.behavior.baseAlign==="slide" ? RVS.F.updateCoveredLayer(l.uid) : undefined;

		// COLUMN SPECIALS
		if (l.type==="column") {
			tr.whiteSpace = "normal";
			var pbreakat = RVS.L[l.group.puid].group.columnbreakat;
			if ((pbreakat==="notebook" && (RVS.screen==="n" || RVS.screen==="t" || RVS.screen==="m")) ||
				(pbreakat==="tablet" && (RVS.screen==="t" || RVS.screen==="m")) ||
				(pbreakat==="mobile" && RVS.screen==="m")) {
				w_tr.display = "block";
				tr.width = "100%";
				w_tr.width = "100%";
				w_tr.maxWidth = "none";
			}
			else
				w_tr.display = "table-cell";
		} else

		// ROW SPECIALS
		if (l.type==="row") {
			w_tr.width="100%";
			var pbreakat = l.group.columnbreakat;
			if ((pbreakat==="notebook" && (RVS.screen==="n" || RVS.screen==="t" || RVS.screen==="m")) ||
				(pbreakat==="tablet" && (RVS.screen==="t" || RVS.screen==="m")) ||
				(pbreakat==="mobile" && RVS.screen==="m"))
				tr.display = "block";
			else
				tr.display = "table";
		} else

		if (l.type==="image" && l.size.covermode!=="custom") {
			var newDims = RVS.F.getProportionalSizes({	proportional:l.size.scaleProportional,
														type:l.size.covermode,
														image:{width: l.size.originalWidth, height: l.size.originalHeight},
														viewPort:{	width: slideBased_tw_wtr !== undefined && slideBased_tw_wtr!==false && slideBased_tw_wtr.width!==undefined ? RVS.S.ulDIM.width : RVS.F.GW(RVS.screen), height: slideBased_tw_wtr !== undefined && slideBased_tw_wtr!==false && slideBased_tw_wtr.height!==undefined ? RVS.S.ulDIM.height : RVS.SLIDER.settings.size.height[RVS.screen]}
													});

			w_tr.width = tr.width = newDims.width;
			w_tr.height = tr.height = newDims.height;
		} else

		if (l.type==="video" && l.size.height[RVS.screen].v==="auto") {			// VIDEOS IN COLUMN NEED TO BE RESIZED
			var prop = l.media.ratio.split(":");
			prop = prop[1] / prop[0];
			w_tr.height = tr.height = lh.w.width() * prop;
		} else

		if (l.type==="shape") { //SHAPE LINEHEIGHT EQ. TO HEIGHT !
			w_tr.lineHeight = RVS.F.isVaOrPx(tr.height) ? parseInt(tr.height,0)+"px" : "auto";
		} else

		//SVG SIZING
		if (l.type==="svg" && lh.svg) {
			var svgsettings =

			tpGS.gsap.set(lh.svg,(l.idle.svg.originalColor!==true ?
				{
					width:"100%",
					height:"100%",
					fill:window.RSColor.get(l.idle.svg.color[RVS.screen].v),
					stroke:window.RSColor.get(l.idle.svg.strokeColor),
					"stroke-width":l.idle.svg.strokeWidth,
					"stroke-dasharray":RVS.F.getDashArray(l.idle.svg.strokeDashArray),
					"stroke-dashoffset":(l.idle.svg.strokeDashOffset===undefined ? 0 : l.idle.svg.strokeDashOffset)
				} : {width:"100%", height:"100%", clearProps:"fill,stroke,stroke-width,stroke-dasharray,stroke-dashoffset"})
			);
			if (l.idle.svg.originalColor!==true)
				tpGS.gsap.set(lh.svgPath,{ fill:window.RSColor.get(l.idle.svg.color[RVS.screen].v)});
			else
				tpGS.gsap.set(lh.svgPath,{ clearProps:"fill"});
		}

		// NEW COVER MODE IF LAYER ALIGNED TO STAGE AND NOT TO CONTENT
		if (l.behavior.baseAlign==="slide") {
			lh.w_width = w_tr.width = slideBased_tw_wtr!==false && slideBased_tw_wtr.width!==undefined ? slideBased_tw_wtr.width : w_tr.width;
			lh.w_height = w_tr.height = slideBased_tw_wtr!==false && slideBased_tw_wtr.height!==undefined ? slideBased_tw_wtr.height : w_tr.height;
		}

		//IF WIDTH or HEIGHT is % WE NEED TO SET INNER AND OUTER WRAPPER DIFFERENTLY
		if (!RVS.F.isNumeric(tr.width) && tr.width.indexOf("%")>=0) tr.width = "100%";
		if (!RVS.F.isNumeric(tr.height) && tr.height.indexOf("%")>=0) tr.height = "100%";

		// % BASED GROUP HEIGHT
		if (l.type==="group") if (tr.height==="100%") tpGS.gsap.set([lh.m,lh.iw,lh.lp],{height:"100%"}); else tpGS.gsap.set([lh.m,lh.iw,lh.lp],{height:"auto"});

		w_tr.onComplete = function() {RVS.DOC.trigger('layerDrawn',{layerid:_.uid})};

		if (l.hover.usehover=='true' || l.hover.usehover==true || l.hover.usehover=="desktop") {
			var iG = tr.background!==undefined && typeof tr.background === 'string' && tr.background.search('gradient') !== -1,
				hG = htr.background!==undefined && typeof htr.background === 'string' && htr.background.search('gradient') !== -1,
				cGRs;

			if(iG && hG) {
				if (RVS.F.gradDegree(tr.background)!==180 && RVS.F.gradDegree(htr.background) == 180) htr.background = RVS.F.addGradDegree(htr.background,180);
				 cGRs = tpGS.getSSGColors(tr.background,htr.background,l.hover.gradientStyle);
				 tr.background = cGRs.from;
				 htr.background = cGRs.to;
			}
			else if (tr.background!==undefined && !iG && hG) {
				cGRs = tpGS.getSSGColors(tr.background,htr.background,l.hover.gradientStyle)
				tr.background = cGRs.from;
				htr.background = cGRs.to;
			} else if (iG && !hG && htr.backgroundColor!==undefined) {
				cGRs = tpGS.getSSGColors(tr.background,htr.backgroundColor,l.hover.gradientStyle);
				tr.background = cGRs.from;
				htr.background = cGRs.to;
			}
		}

		if (l.type==="column") {
			delete tr.backgroundColor;
			delete tr.background;
		}


		// ONLY UPDATE IF NECESSARY
		/*RVS.CACHE[RVS.S.slideId] = RVS.CACHE[RVS.S.slideId] || {};
		RVS.CACHE[RVS.S.slideId][_.uid] = RVS.CACHE[RVS.S.slideId][_.uid] || {};
		let C_wtr = JSON.stringify(w_tr),
			C_tr = JSON.stringify(tr),
			C_pos = JSON.stringify(l.position);
	*/

		//if (RVS.CACHE[RVS.S.slideId][_.uid].wtr !=C_wtr || RVS.CACHE[RVS.S.slideId][_.uid].tr !==C_tr) {
			//SET WRAPPER DIMENSION

		tr.immediateRender = true;
		w_tr.immediateRender = true;

			//SET STYLE OF LAYER
			tpGS.gsap.set(lh.c,tr);
			tpGS.gsap.set(lh.w,w_tr);
		//}



		//RVS.CACHE[RVS.S.slideId][_.uid].wtr = C_wtr;
		//RVS.CACHE[RVS.S.slideId][_.uid].tr = C_tr;


		//if (RVS.CACHE[RVS.S.slideId][_.uid].bgcolor!==bgcolor) {
			///* CREATE SHARP CORNERS */
			RVS.F.updateSharpCorners({uid:_.uid,bgcolor:bgcolor});
	//	}

 		//RVS.CACHE[RVS.S.slideId][_.uid].bgcolor=bgcolor;

		//CREATE ANIMATION
		lh.htr = htr;
		lh.bg_htr = bg_htr;

		requestAnimationFrame(function() {
			RVS.F.renderLayerAnimation({layerid:_.uid, quickRendering:_.ignoreLayerAnimation,timeline:"full",caller:"drawHTMLLayer"});
			RVS.F.checkCurrentLayerHoverMode({layerid:_.uid});
			if ((lh.drawnonce===undefined) && l.type==="button" || l.type==="text") {
				lh.drawnonce=true;
				RVS.F.updateHTMLLayerPosition({uid:_.uid});
			}
		});


		if (RVS.S.shwLayerAnim && jQuery.inArray(_.uid,RVS.selLayers)>=0) RVS.F.playLayerAnimation({layerid:_.uid});

		if (RVS.F.updateMinSliderHeights()) {
			if (!RVS.S.drawingHTMLLayers)
				RVS.DOC.trigger('updatesliderlayout',"layer.js-586");
			else
				RVS.S.updateMinSliderHeight_force = true;
		}  else {
			RVS.F.updateHTMLLayerPosition(_);
			requestAnimationFrame(function() {
				requestAnimationFrame(function() {
					RVS.F.updateHTMLLayerPosition(_);
				});
			});

		}
		//if(RVS.CACHE[RVS.S.slideId][_.uid].position !== C_pos) 	RVS.F.updateHTMLLayerPosition(_);


		//RVS.CACHE[RVS.S.slideId][_.uid].pos = C_pos;

	};

	RVS.F.addGradDegree = function(grad, deg) {
			grad = grad.split('(');
			var begin = grad[0];
			grad.shift();
			return begin + '(' + deg + 'deg, ' + grad.join('(');
	};

	RVS.F.gradDegree = function(grad) {
			if(grad.search('deg,') !== -1) {
				var deg = grad.split('deg,')[0];
				if(deg.search(/\(/) !== -1) return parseInt(deg.split('(')[1], 10);
			}
			return 180;
	};

	RVS.F.smartConvertDivs  = function(a) {
		if (typeof a==="string" && a[0]==="#")
			return (a[1]/a[3])*100+"%";
		else
			return a;
	};

	RVS.F.updateSharpCorners = function(_) {
		if (RVS.L[_.uid].type!=="text" && RVS.L[_.uid].type!=="button" && RVS.L[_.uid].type!=="shape") return;

		var lh = RVS.H[_.uid],
			l = RVS.L[_.uid];
		if (!_.resize) {
			if (lh.leftcorner) lh.leftcorner.remove();
			if (lh.rightcorner) lh.rightcorner.remove();
		}
		if (l.idle.cornerLeft!=="none") {
			if (!_.resize || lh.leftcorner===undefined) {
				lh.leftcorner = jQuery('<'+l.idle.cornerLeft+'></'+l.idle.cornerLeft+'>');
				lh.c.append(lh.leftcorner);
			}
			lh.leftcorner.css('borderWidth',lh.c.outerHeight()+"px");
			lh.leftcorner.css('borderRight','0px solid transparent');
			if (_.bgcolor) lh.leftcorner.css('border'+(l.idle.cornerLeft==="rs-fcrt" ? "Bottom" : "Top")+'Color',_.bgcolor);
		}

		if (l.idle.cornerRight!=="none") {
			if (!_.resize || lh.rightcorner===undefined) {
				lh.rightcorner = jQuery('<'+l.idle.cornerRight+'></'+l.idle.cornerRight+'>');
				lh.c.append(lh.rightcorner);
			}
			lh.rightcorner.css('borderWidth',lh.c.outerHeight()+"px");
			lh.rightcorner.css('borderLeft','0px solid transparent');
			if (_.bgcolor)  lh.rightcorner.css('border'+(l.idle.cornerRight==="rs-bcrt" ? "Top" : "Bottom")+'Color',_.bgcolor);
		}
	};




	/*
	REORDER HTML LAYERS ON THE SLIDE
	*/
	RVS.F.reOrderHTMLLayers = function(_) {
		RVS.C.layergrid.detach();
		RVS.S.redrawHTMLLayersList = [];
		// BUILD FIRST LEVEL LAYERS
		for (var li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if ((RVS.L[li].group.puid===-1 || RVS.L[li].type==="row") &&  RVS.L[li].type!=="zone") RVS.F.reOrderHTMLLayer({uid:li});
		}
		for (li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if (RVS.L[li].type=="column") RVS.F.reOrderHTMLLayer({uid:RVS.L[li].uid});
		}
		for (li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if (RVS.L[li].type!=="column" && RVS.L[li].group.puid!==-1) RVS.F.reOrderHTMLLayer({uid:RVS.L[li].uid});
		}
		RVS.C.layergrid.appendTo(RVS.C.slide);
		for (li in RVS.S.redrawHTMLLayersList) if (RVS.S.redrawHTMLLayersList.hasOwnProperty(li)) RVS.F.drawHTMLLayer({uid:RVS.S.redrawHTMLLayersList[li]});
		RVS.F.checkRowsChildren();
	};

	/*
	REDRAW HTML LAYERS
	*/
	RVS.F.drawHTMLLayers = function() {
		if (RVS.S.drawingHTMLLayers!==true) {
			RVS.S.drawingHTMLLayers=true;
			requestAnimationFrame(function() {
				if (RVS.S.ReadyToShowAll===undefined) RVS.S.ReadyToShowAll="wait";
				RVS.S.drawHTMLLayersCalled = true;
				// BUILD FIRST LEVEL LAYERS
				for (var li in RVS.L) {
					if(!RVS.L.hasOwnProperty(li)) continue;
					if ((RVS.L[li].group.puid===-1 || RVS.L[li].type==="row") &&  RVS.L[li].type!=="zone") RVS.F.drawHTMLLayer({uid:li});
				}
				for (li in RVS.L) {
					if(!RVS.L.hasOwnProperty(li)) continue;
					if (RVS.L[li].type=="column") RVS.F.drawHTMLLayer({uid:RVS.L[li].uid});
				}
				for (li in RVS.L) {
					if(!RVS.L.hasOwnProperty(li)) continue;
					if (RVS.L[li].type!=="column" && RVS.L[li].group.puid!==-1) RVS.F.drawHTMLLayer({uid:RVS.L[li].uid});
				}
				RVS.F.checkRowsChildren();
				RVS.S.drawingHTMLLayers=false;

				if (RVS.S.updateMinSliderHeight_force ||  RVS.S.ReadyToShowAll=="wait" ) RVS.F.updatesliderlayout(undefined);

				RVS.S.updateMinSliderHeight_force = false;
			});
		}
	}

	/*
	BUILD HTML LAYERS ON THE SLIDE
	*/
	RVS.F.buildHTMLLayers = function(obj) {
		obj = obj===undefined ? {} : obj;
		// BUILD FIRST LEVEL LAYERS
		for (var li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if ((RVS.L[li].group.puid===-1 || RVS.L[li].type==="row") &&  RVS.L[li].type!=="zone") RVS.F.buildHTMLLayer({uid:li,ignoreDrawLayers:obj.ignoreDrawLayers});
		}
		for (li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if (RVS.L[li].type=="column") RVS.F.buildHTMLLayer({uid:RVS.L[li].uid,ignoreDrawLayers:obj.ignoreDrawLayers});
		}
		for (li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if (RVS.L[li].type!=="column" && RVS.L[li].group.puid!==-1) RVS.F.buildHTMLLayer({uid:RVS.L[li].uid,ignoreDrawLayers:obj.ignoreDrawLayers});
		}
		RVS.F.checkRowsChildren();
		RVS.F.checkLockedLayers();
		RVS.F.checkShowHideLayers();
		if (window.firstLockTest==="change") {
			RVS.F.showInfo({content:RVS_LANG.somelayerslocked, type:"goodtoknow", showdelay:2, hidedelay:3, hideon:"", event:"" });
			window.firstLockTest = true;
		}
	};


	// UPDATE THE INPUT FIELDS OF THE SELETED LAYERS
	RVS.F.updateLayerInputFields = function(_) {
		if (RVS.selLayers.length===0)
			if (RVS.S.layerInputFieldsInitialised)
				return;
			else {
				RVS.F.showWaitAMinute({fadeIn:0,text:RVS_LANG.updatingfields});
				setTimeout(function() {
					RVS.S.layerInputFieldsInitialised = true;
					RVS.F.updateEasyInputs({container:jQuery('.layer_settings_collector'), path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
					RVS.F.showWaitAMinute({fadeOut:2,text:RVS_LANG.updatingfields});
				},5);
				return;
			}

		RVS.S.layerInputFieldsInitialised = true;
		RVS.S.keyFrame = RVS.S.keyFrame==="idle" ? RVS.L[RVS.selLayers[0]].timeline.frameToIdle : RVS.S.keyFrame;
		if (_!==undefined && _.short===true) {
			RVS.F.updateEasyInputs({container:jQuery('.layertoolbar_wrap.layer_settings_collector'), path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
			RVS.F.updateEasyInputs({container:jQuery(RVS.eMode.menu), path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
			clearTimeout(RVS.S.updateEsyInputsTimer);
			RVS.S.updateEsyInputsTimer = setTimeout(function() {
				RVS.F.updateEasyInputs({container:jQuery('.layer_settings_collector'), path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
				delete RVS.S.updateEsyInputsTimer;
			},500);
		} else {
			RVS.F.updateEasyInputs({container:jQuery('.layer_settings_collector'), path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
		}

		RVS.F.updateCusCSSContent();
		// STOP ALL ANIMATION
		RVS.F.stopAllLayerAnimation();

		//Check Dependencies some of the Fields.
		RVS.F.checkAvailableFontWeights();
		RVS.F.updateLayerBgImage(false);
		RVS.F.updateLayerImageSrcThumb();
		RVS.F.updateRowColumnField();
		RVS.F.updateFrameRealSpeed();

		//RVS.F.updateCustomCSS();

		//REBUILD ANIMATION LIST
		RVS.DOC.trigger('updateKeyFramesList');

		//START ANIMATION IF NEEDED
		if (RVS.S.shwLayerAnim)	RVS.F.playLayerAnimation({layerid:RVS.selLayers[0]});
	};

	// UPDATE THE SMALL LAYER BACKGROUND PREVIEW WINDOW
	RVS.F.updateLayerBgImage = function(force) {
		if (RVS.selLayers.length===0) return;
		var is = RVS.L[RVS.selLayers[0]].idle.backgroundImage;
		is= is===undefined  || RVS.selLayers.length>1 ? "" : is;
		if (is==="" && RVS.L[RVS.selLayers[0]].idle.bgFromStream!==true)
			jQuery('#layer_bg_adv_settings').hide();
		else
			jQuery('#layer_bg_adv_settings').show();
		tpGS.gsap.set('#layer_bg_image',{backgroundImage:'url("'+is+'")', "background-size":(RVS.L[RVS.selLayers[0]].idle.backgroundSize==="percentage" ? RVS.L[RVS.selLayers[0]].idle.backgroundSizePerc+"%" : RVS.L[RVS.selLayers[0]].idle.backgroundSize==="pixel" ? RVS.L[RVS.selLayers[0]].idle.backgroundSizePix+"px"  : RVS.L[RVS.selLayers[0]].idle.backgroundSize) , backgroundPosition:RVS.L[RVS.selLayers[0]].idle.backgroundPosition});
		RVS.F.drawHTMLLayer({uid:RVS.selLayers[0]});
		if (force!==false) setTimeout(function() {	RVS.F.updateEasyInputs({container:jQuery('#form_layerstyle_bg'), path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});},100);
	};

	RVS.F.updateLayerImageSrcThumb = function() {
		if (RVS.selLayers.length===0) return;
		if (RVS.selLayers.length>1 ) {
			tpGS.gsap.set('#layer_image_src',{backgroundImage:"none"});
		} else
		if (RVS.L[RVS.selLayers[0]].type==="image") {
			if (RVS.L[RVS.selLayers[0]].media.imageUrl.indexOf("png")>=0 && RVS.L[RVS.selLayers[0]].media.imageUrl!==RVS.ENV.img_ph_url)
				document.getElementById("minilayerprevimage_wrap").className = "miniprevimage_wrap withimage";
			else
			if (RVS.L[RVS.selLayers[0]].media.imageUrl==RVS.ENV.img_ph_url)
				document.getElementById("minilayerprevimage_wrap").className = "miniprevimage_wrap";

			document.getElementById('layer_image_src').style.backgroundImage = 'url("'+RVS.L[RVS.selLayers[0]].media.imageUrl+'")';
		}
	};

	function updateLayerImageSrc_inside(_) {


		_.l = RVS.L[_.uid];

		_.v = _.l.size.width[RVS.screen].v;
		_.pre = RVS.S.slideId+".layers."+_.uid+".";
		RVS.H[_.uid].c.find('img').first().attr('src', _.src);
		if (_.src==RVS.ENV.img_ph_url)
			RVS.H[_.uid].c.addClass("placeholder_on");
		else
			RVS.H[_.uid].c.removeClass("placeholder_on");
		_.ignoreBackup = true;

		if (_.l.type!=="video") layerSizeUpdate(_);

		if (_.ignore)
			RVS.DOC.trigger("restoreLayersSize");

		RVS.F.drawHTMLLayer({uid:_.uid});
	}

	//UPDATE THE SRC OF THE IMAGES
	RVS.F.updateLayerImageSrc = function(e,_) {
		if (_!=undefined && _.layerid!==undefined) {
			//Check if Original Size was Set before Update !!
			for (var lid in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(lid)) continue;
				RVS.L[RVS.selLayers[lid]].size.originalSize = parseInt(RVS.L[RVS.selLayers[lid]].size.width[RVS.screen].v,0) == parseInt(RVS.L[RVS.selLayers[lid]].size.originalWidth,0);
			}

			//_.ignore = true;
			_.direction = "width";
			_.dirB = "height";
			_.src = _.src==undefined ? RVS.L[_.layerid].media.imageUrl : _.src;
			_.src = _.src==undefined ? RVS.L[_.layerid].media.posterUrl : _.src;
			_.id = _.id===undefined ? RVS.L[_.layerid].media.imageId : _.id;
			_.id = _.id===undefined ? RVS.L[_.layerid].media.posterId : _.id;



			RVS.F.preloadImage({
				uid:_.layerid,
				slideId:RVS.S.slideId,
				image : _.src,
				silent:false,
				callback:function() {
					var original = false;
					for (var lid in RVS.selLayers) {
						if(!RVS.selLayers.hasOwnProperty(lid)) continue;
						_.uid = RVS.selLayers[lid];
						_.ignore = RVS.L[RVS.selLayers[lid]].size.originalSize;
						updateLayerImageSrc_inside(_);
						original = original || _.uid===_.layerid;
					}
					if (!original) {
						_.uid = _.layerid;
						updateLayerImageSrc_inside(_);
					}
					RVS.F.updateLayerImageSrcThumb();
				}
			});

			// UPDATE SOURCE TYPE AND LIBRARY TYPE
			setTimeout(function() {	RVS.F.updateEasyInputs({container:jQuery('#form_layer_content'), path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});},100);
		}
	};

	// UPDATE SVG Source
	RVS.F.updateLayerSVGSrc = function(_) {
		if (_==undefined || _.uids===undefined || _.src===undefined || _.uids.length===0) return;
		jQuery.get(_.src, function(data) {
			for (var i in _.uids) {
				if(!_.uids.hasOwnProperty(i)) continue;
				var uid = _.uids[i],
					ih = new XMLSerializer().serializeToString(data.documentElement);
			  	RVS.H[uid].c[0].innerHTML = ih;
			  	RVS.H[uid].svg = RVS.H[uid].w.find('svg');
				RVS.H[uid].svgPath = RVS.H[uid].w.find(!RVS.L[uid].idle.svg.styleAll ? 'svg path' : 'svg path, svg circle, svg ellipse, svg line, svg polygon, svg polyline, svg rect');
			  	RVS.F.updateLayerObj({path:"svg.renderedData",val:ih,ignoreBackup:true});
			  	RVS.F.drawHTMLLayer({uid:uid});
			 }
		});

	};

	// CHECK IF VIDEO NEED PLACEHOLDER IMAGE
	RVS.F.resetVideoPlaceholder = function(e,_) {
		if (_===undefined || _.val===undefined) return;
		for (var lid in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(lid)) continue;
			var uid = RVS.selLayers[lid];
			if (RVS.L[uid].type==="video") {
				if (RVS.L[uid].media.posterUrl === RVS.ENV.img_ph_url)
					RVS.H[uid].c.addClass("placeholder_on");
				else
					RVS.H[uid].c.removeClass("placeholder_on");
			}
		}

	};

	//RESYNC SIZE OF VIDEO LAYERS BASED ON THE CURRENT ASPECT RATIO
	RVS.F.resyncVideoAspectRatio = function() {
		RVS.F.openBackupGroup({id:"layerresize",txt:"Video Aspect Ratio",icon:"videocam"});
		for (var lid in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(lid)) continue;
			var _ = RVS.L[RVS.selLayers[lid]];
			if (_.type==="video") {
				var rat = _.media.ratio.split(":"),
					pre = RVS.S.slideId+".layers."+_.uid+".";
				rat = rat[0] / rat[1];
				RVS.F.updateSliderObj({path:pre+'size.aspectRatio.#size#.v',val:rat});
				layerSizeUpdate({ignore:true, direction:"width", dirB:"height", v:RVS.L[_.uid].size.width[RVS.screen].v, l:RVS.L[_.uid], pre:pre});
				RVS.F.drawHTMLLayer({uid:_.uid});
			}
		}
		RVS.F.closeBackupGroup({id:"layerresize"});
		RVS.F.updateLayerInputFields();
	};



/***********************************
	MAIN INTERNAL FUNCTIONS
************************************/

	// UPDATE AUDIO LAYER SIZE IF NEEDED */
	RVS.F.changeAudioLayerSize = function(_) {
		RVS.F.openBackupGroup({id:"layerresize",txt:"Audio Layer Controls",icon:"photo_size_select_large"});

		for (var lid in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(lid)) continue;
			_.uid = RVS.selLayers[lid];
			_.l = RVS.L[_.uid];
			_.pre = RVS.S.slideId+".layers."+_.uid+".";
			RVS.F.updateSliderObj({path:_.pre+'media.controls',val:!_.l.media.controls});
			if (_.l.type==="audio")
				if (_.l.media.controls) {
					RVS.F.updateSliderObj({path:_.pre+'size.width.#size#.v',val:350});
					RVS.F.updateSliderObj({path:_.pre+'size.height.#size#.v',val:54});
				} else {
					RVS.F.updateSliderObj({path:_.pre+'size.width.#size#.v',val:54});
					RVS.F.updateSliderObj({path:_.pre+'size.height.#size#.v',val:54});
				}

			RVS.F.drawHTMLLayer({uid:_.uid});
		}

		RVS.F.closeBackupGroup({id:"layerresize"});
		RVS.F.updateLayerInputFields();
	};

	RVS.F.changeMediaControlsInteraction = function(_) {
		RVS.F.openBackupGroup({id:"mediacontrols",txt:"Media Layer Controls",icon:"photo_size_select_large"});
		for (var lid in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(lid)) continue;
			_.uid = RVS.selLayers[lid];
			_.l = RVS.L[_.uid];
			_.pre = RVS.S.slideId+".layers."+_.uid+".";
			RVS.F.updateSliderObj({path:_.pre+'media.nointeraction',val:!_.l.media.nointeraction});
			RVS.F.updateSliderObj({path:_.pre+'media.controls',val:false});
			RVS.F.updateSliderObj({path:_.pre+'media.largeControls',val:false});
		}
		RVS.F.closeBackupGroup({id:"mediacontrols"});
		RVS.F.updateLayerInputFields();
	};

	// UPDATE LAYER SIZE BASED ON WIDTH / HEIGHT / ASPECT RATIO / TYPE
	function layerSizeUpdate(_) {
		switch (_.l.type) {
			case "image":
				if (_.ignore!==true) RVS.F.updateSliderObj({path:_.pre+'size.'+_.direction+'.#size#.v',val:_.v, ignoreBackup:_.ignoreBackup});

				var newV;

				if (_.direction==="width")
					newV = (RVS.F.isNumeric(_.v) || (!RVS.F.isNumeric(_.v) && _.v.indexOf("px")>=0)) ? Math.round(parseInt(_.v,0) / _.l.size.aspectRatio[RVS.screen].v) : "auto";
				else
					newV = (!RVS.F.isNumeric(_.v) && _.v.indexOf("%")>=0) ? Math.round(( getLayerParentContainerSize({uid:_.uid}).height * (parseInt(_.v,0)/100) ) * _.l.size.aspectRatio[RVS.screen].v) : _.v==="auto" ? _.v : Math.round(parseInt(_.v,0) * _.l.size.aspectRatio[RVS.screen].v);
				newV = newV==="auto" ? newV : newV+"px";
				RVS.F.updateSliderObj({path:_.pre+'size.'+_.dirB+'.#size#.v',val:newV, ignoreBackup:_.ignoreBackup});
			break;
			case "group":
			case "shape":
				var newV= _.v==="auto" ? "100px" : _.v;
				if (_.ignore!==true) RVS.F.updateSliderObj({path:_.pre+'size.'+_.direction+'.#size#.v',val:newV, ignoreBackup:_.ignoreBackup});
				if (RVS.F.isNumeric(parseInt(newV,0))) {
					newV = (_.direction==="width") ? Math.round(parseInt(newV,0) / _.l.size.aspectRatio[RVS.screen].v) : Math.round(parseInt(newV,0) * _.l.size.aspectRatio[RVS.screen].v);
					RVS.F.updateSliderObj({path:_.pre+'size.'+_.dirB+'.#size#.v',val:newV, ignoreBackup:_.ignoreBackup});
				}
			break;
			case "video":
				if (_.ignore!==true) RVS.F.updateSliderObj({path:_.pre+'size.'+_.direction+'.#size#.v',val:_.v, ignoreBackup:_.ignoreBackup});

				var newV,prop = _.l.media.ratio.split(":");
				prop = prop[0] / prop[1];

				if (_.direction==="width")
					newV = (RVS.F.isNumeric(_.v) || (!RVS.F.isNumeric(_.v) && _.v.indexOf("px")>=0)) ? Math.round(parseInt(_.v,0) / prop) : "auto";
				else
					newV = (!RVS.F.isNumeric(_.v) && _.v.indexOf("%")>=0) ? Math.round(( getLayerParentContainerSize({uid:_.uid}).height * (parseInt(_.v,0)/100) ) * prop) : _.v==="auto" ? _.v : Math.round(parseInt(_.v,0) * prop);
				newV = newV==="auto" ? _.dirB==="width" ? "100%" : newV : newV+"px";

				RVS.F.updateSliderObj({path:_.pre+'size.'+_.dirB+'.#size#.v',val:newV, ignoreBackup:_.ignoreBackup});
			break;
		}
	}

	/*

	 */


	// UPDATE SIZE AND TAKE CARE OF ASPECT RATIO IF NEEDED
	RVS.F.changeLayerSizes = function(_) {
		//_.v = (l.type==="shape" || l.type==="group") && _.v==="auto" ? "100px" : _.v;
		RVS.F.openBackupGroup({id:"layerresize",txt:"Layer "+_.direction.toUpperCase(),icon:"photo_size_select_large"});
		_.dirB = _.direction==="width" ? "height" : "width";

		for (var lid in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(lid)) continue;
			_.uid = RVS.selLayers[lid];
			_.l = RVS.L[_.uid];
			_.pre = RVS.S.slideId+".layers."+_.uid+".";

			if (!_.l.size.scaleProportional)
				RVS.F.updateSliderObj({path:_.pre+'size.'+_.direction+'.#size#.v',val:_.v});
			else
				layerSizeUpdate(_);

			RVS.F.drawHTMLLayer({uid:_.uid});
		}

		RVS.F.closeBackupGroup({id:"layerresize"});
		RVS.F.updateLayerInputFields();
	};

	// LOCK / UNLOCK THE ASPECT RATIO, RESET SIZES IF NEEDED FOR IMAGES
	RVS.F.lockUnlockLayerRatio = function(_) {
		RVS.F.openBackupGroup({id:"layerresize",txt:"Layer Aspect Ratio",icon:"photo_size_select_large"});
		for (var lid in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(lid)) continue;
			_.uid = RVS.selLayers[lid];
			_.l = RVS.L[_.uid];
			_.pre = RVS.S.slideId+".layers."+_.uid+".";
			_.direction = "width";
			_.dirB = "height";

			RVS.F.updateSliderObj({path:_.pre+'size.scaleProportional',val:_.val});
			if (_.val)
				if (_.l.type==="image" ) {
					_.v = _.l.size.width[RVS.screen].v;
					_.ignore = true;
					layerSizeUpdate(_);
					RVS.F.drawHTMLLayer({uid:_.uid});
				} else {
					var lh = RVS.H[_.uid].w,
						lhW=lh.width(),
						lhH=lh.height();
					RVS.F.updateSliderObj({path:_.pre+'size.originalWidth',val:lhW});
					RVS.F.updateSliderObj({path:_.pre+'size.originalHeight',val:lhH});
					RVS.F.updateSliderObj({path:_.pre+'size.aspectRatio.#size#.v',val:lhW/lhH});
				}
		}

		RVS.F.closeBackupGroup({id:"layerresize"});
		RVS.F.updateLayerInputFields();
	};


	//LOCK UNLOCK MARGIN AND PADDING INPUTS
	RVS.F.lockUnlockMarginPadding = function(_,what,where,state) {
		state = state===undefined ? "idle" : state;
		RVS.F.openBackupGroup({id:"lock"+what,txt:"Lock and reset "+what+"(s)",icon:"border_outer"});
		for (var lid in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(lid)) continue;
			_.uid = RVS.selLayers[lid];
			_.l = RVS.L[_.uid];
			_.pre = RVS.S.slideId+".layers."+_.uid+".";
			RVS.F.updateSliderObj({path:_.pre+state+'.'+what+'Lock',val:_.val});
			if (_.val) {
				var d = _.l[state][what][RVS.screen]===undefined ?
						_.l[state][what].v===undefined ?
							_.l[state][what][0] :
							_.l[state][what].v[0] :
							_.l[state][what][RVS.screen].v[0];
				RVS.F.updateSliderObj({path:_.pre+state+'.'+what+where+'.1',val:d});
				RVS.F.updateSliderObj({path:_.pre+state+'.'+what+where+'.2',val:d});
				RVS.F.updateSliderObj({path:_.pre+state+'.'+what+where+'.3',val:d});
			}
			RVS.F.drawHTMLLayer({uid:_.uid});
		}
		RVS.F.closeBackupGroup({id:"lock"+what});
		RVS.F.updateLayerInputFields();
	};
	// UPDATE 1 OR 4 VALUES IN THE CURRENT SCREEN SIZE
	RVS.F.updateMarginPaddingValues = function(ds,what,where,state) {
		state = state===undefined ? "idle" : state;
		RVS.F.openBackupGroup({id:what+"Value",txt:"Layer "+what,icon:"border_outer"});
			for (var li in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(li)) continue;
				var l = RVS.L[RVS.selLayers[li]],
					pre = RVS.S.slideId+".layers."+RVS.selLayers[li]+".";
				if (l[state][what+'Lock'])
					for (var i=0;i<4;i++) {
						RVS.F.updateSliderObj({path:pre+state+'.'+what+where+"."+i,val:ds.val});
					}
				else
					RVS.F.updateSliderObj({path:pre+state+'.'+what+where+"."+ds.eventparam,val:ds.val});

				RVS.F.drawHTMLLayer({uid:RVS.selLayers[li]});
			}
		RVS.F.closeBackupGroup({id:what+"Value"});
		RVS.F.updateLayerInputFields();
	};



	RVS.F.replaceMetas = function(_) {
		if (_.indexOf('{{')>=0)
			for (var i in RVS.LIB.META) {
				if(!RVS.LIB.META.hasOwnProperty(i)) continue;
				if (_.search(i)>=0) {
					var re = new RegExp(i,"g");
					_ = _.replace(re,RVS.LIB.META[i]);
				}
			}
		return _;
	};

	RVS.F.redrawEditedTextLayerwhenPlaceholder = function() {
		if (RVS.selLayers[0]!==undefined && RVS.L[RVS.selLayers[0]]!==undefined) {
			//if (RVS.L[uid].placeholder!==undefined && RVS.L[uid].placeholder.length>0 && RVS.L[uid].placeholder!==" ")
			RVS.F.redrawTextLayerInnerHTML(RVS.selLayers[0],document.getElementById('ta_layertext').value!==RVS.S.inFocusValu);
		}
	}

	RVS.F.redrawTextLayerInnerHTML = function(uid,forcesplit) {
		if (RVS.L[uid].type==="text" || RVS.L[uid].type==="button") {
			if(forcesplit) {
				if (RVS.H[uid].splitText!==undefined) RVS.H[uid].splitText.revert();
				RVS.H[uid].splitText = undefined;
			}
			if (RVS.L[uid].placeholder!==undefined && RVS.L[uid].placeholder.length>0 && RVS.L[uid].placeholder!==" ")
				RVS.H[uid].c[0].innerHTML = RVS.L[uid].placeholder;
			else
				RVS.H[uid].c[0].innerHTML = jQuery.inArray(RVS.L[uid].idle.whiteSpace[RVS.screen].v,["normal","nowrap"])>=0 ? RVS.F.replaceMetas(RVS.L[uid].text) : RVS.F.replaceMetas(RVS.L[uid].text.replace(/\r\n|\r|\n/g,"<br />"));

			if (forcesplit) {
				RVS.F.drawHTMLLayer({uid:uid,ignoreLayerAnimation:true});
				if (RVS.L[uid].timeline.split!==undefined) RVS.F.updateLayerFrames({layerid:uid});
			}
		}
	};





	/*
	INIT LOCAL INPUT BOX FUNCTIONS
	*/
	function initLocalInputBoxes() {
		RVS.DOC.on('coloredit colorcancel',colorEditLayer);
	}

	function setKeyframeName(i) {
		//document.getElementById('editing_keyframe').innerHTML = RVS_LANG.keyframe+" #"+i;
		jQuery('framewrap.selected').removeClass("selected").removeClass("selected_0");
		var el = document.getElementById(RVS.S.slideId+"_"+RVS.selLayers[0]+"_"+(RVS.S.keyFrame==="frame_0" ? "frame_1" : RVS.S.keyFrame));
		if (el!=null && el!==undefined) el.className +=" selected" + (RVS.S.keyFrame==="frame_0" ? " selected_0" : "");
	}


	// UPDATE A LAYER ANIMATION FROM ANIMATION LIBRARY ON CALL
	// direction:in,out,look,
	// group: name of Animation Group,
	// transition:shortname of transition
	RVS.F.changeLayerAnimation = function(_) {
		if (!_.fromLayerTransListe) {
			window.timelineTemporaryCached=true;
			RVS.L[RVS.selLayers[0]].timelinecache = RVS.F.safeExtend(true,{},RVS.L[RVS.selLayers[0]].timeline);
		}

		updateTimeLineByTemplate(RVS.LIB.LAYERANIMS[_.direction][_.group].transitions[_.transition]);
		var icon = _.direction==="loop" ? "loop" : "play_circle_filled",
			tmpl = _.direction==="loop" ? RVS_LANG.backupTemplateLoop : RVS_LANG.backupTemplateLayerAnim,
			bcktype = _.direction==="loop" ? "layerLoop" : "layerFrames",
			path = _.direction==="loop" ? "loop" : "frames";

		if (!_.ignoreBackupGroup) RVS.F.openBackupGroup({id:"changeFramesFromTemplate",txt:tmpl,icon:icon});
		RVS.F.backup({path:path,layer:RVS.selLayers[0],slide:RVS.S.slideId,cache:undefined, icon:icon,txt:tmpl,lastkey:"timeline",force:true,val:RVS.L[RVS.selLayers[0]].timeline[path], old:RVS.L[RVS.selLayers[0]].timelinecache[path], backupType:bcktype, bckpGrType:"layerTemplateAnimation"});

		// RUN OTHER UPDATES ON OTHER ELEMENTS IF NEEDED
		for (var i in RVS.JHOOKS.changeLayerAnimation) {
			if(!RVS.JHOOKS.changeLayerAnimation.hasOwnProperty(i)) continue;
			RVS.JHOOKS.changeLayerAnimation[i](_);
		}

		if (!_.ignoreBackupGroup) RVS.F.closeBackupGroup({id:"changeFramesFromTemplate"});

		window.timelineTemporaryCached = false;
		delete RVS.L[RVS.selLayers[0]].timelinecache;
		jQuery('.layer_transliste.open').removeClass("open");
		RVS.F.renderLayerAnimation({layerid:RVS.selLayers[0]});
		RVS.F.playLayerAnimation({layerid:RVS.selLayers[0]});
		RVS.F.updateLayerInputFields();
		if (!RVS.S.shwLayerAnim)
			RVS.F.stopAllLayerAnimation();
		else
			RVS.F.playLayerAnimation({layerid:RVS.selLayers[0]});

	};

	RVS.F.updateLayerTimelineLoopLists = function() {
		var opts ="";
		window.layerTimelineLoopFrom = window.layerTimelineLoopFrom == undefined ? jQuery('#la_timeline_loop_from') : window.layerTimelineLoopFrom;
		window.layerTimelineLoopTo = window.layerTimelineLoopTo == undefined ? jQuery('#la_timeline_loop_to') : window.layerTimelineLoopTo;
		for (var i in RVS.L[RVS.selLayers[0]].timeline.frameOrder) {
			if(!RVS.L[RVS.selLayers[0]].timeline.frameOrder.hasOwnProperty(i)) continue;
			if (i>0 && RVS.L[RVS.selLayers[0]].timeline.frameOrder[i].id!=="frame_999")	opts += '<option value="'+RVS.L[RVS.selLayers[0]].timeline.frameOrder[i].id+'">'+RVS.L[RVS.selLayers[0]].timeline.frames[RVS.L[RVS.selLayers[0]].timeline.frameOrder[i].id].alias+' (#'+i+')</option>';
		}

		window.layerTimelineLoopFrom[0].innerHTML = window.layerTimelineLoopTo[0].innerHTML = opts;
		window.layerTimelineLoopFrom.val(RVS.L[RVS.selLayers[0]].timeline.tloop.from).ddTP('change');
		window.layerTimelineLoopTo.val(RVS.L[RVS.selLayers[0]].timeline.tloop.to).ddTP('change');
	};

	RVS.F.updateCoveredLayer = function(i) {

		if (RVS.L[i].size.covermode==="cover-proportional" || RVS.L[i].size.covermode==="cover") return {width : RVS.S.ulDIM.width, height:RVS.S.ulDIM.height};
		else
		if (RVS.L[i].size.covermode==="fullwidth") return {width: RVS.S.ulDIM.width};
		else
		if (RVS.L[i].size.covermode==="fullheight") return {height: RVS.S.ulDIM.height};
		else
		return false;
	}

	RVS.F.updateCoveredLayers = function(redraw) {
		window.requestAnimationFrame(function() {
			for (var i in RVS.L) {
				if (!RVS.L.hasOwnProperty(i)) continue;
				// NEW COVER MODE IF LAYER ALIGNED TO STAGE AND NOT TO CONTENT
				if (RVS.H[i]!==undefined && RVS.L[i].behavior!==undefined && RVS.L[i].behavior.baseAlign==="slide") {
					var w = RVS.F.updateCoveredLayer(i);
					if (w!==undefined && redraw) {
						RVS.F.drawHTMLLayer({uid:i});
						RVS.F.updateHTMLLayerPosition({uid:i});
					} else {
						if (w!==false && w.width!==undefined || w.height!==undefined) tpGS.gsap.set(RVS.H[i].w,w);
						RVS.H[i].w_width = w.width!==undefined ? w.width : RVS.H[i].w_width;
						RVS.H[i].w_height = w.height!==undefined ?w.height : RVS.H[i].w_height;
						if (RVS.L[i].type==="image" && RVS.L[i].size.covermode!=="custom") {
							var newDims = RVS.F.getProportionalSizes({	proportional:RVS.L[i].size.scaleProportional,
											type:RVS.L[i].size.covermode,
											image:{width: RVS.L[i].size.originalWidth, height: RVS.L[i].size.originalHeight},
											viewPort:{	width:w!==false && w.width!==undefined ? RVS.S.ulDIM.width : RVS.F.GW(RVS.screen), height:w!==false && w.height!==undefined ?RVS.S.ulDIM.height : RVS.SLIDER.settings.size.height[RVS.screen]}
										});
							RVS.H[i].c[0].style.width = newDims.width+"px";
							RVS.H[i].c[0].style.height = newDims.height+"px";
						}
					}

				}
			}
		});
	}
	/*
	INIT CUSTOM EVENT LISTENERS FOR TRIGGERING FUNCTIONS
	*/
	function initLocalListeners() {

		// Drop Function for Images and Videos
		jQuery('#builderView').on('dragover dragenter', function(e) {
			var dt = e.originalEvent.dataTransfer;
  			if ((dt.types && (dt.types.indexOf ? dt.types.indexOf('Files') != -1 : dt.types.contains('Files'))) && (!RVS.S.dragginginside && jQuery('#filedrop').length===0))
				 RVS.F.browserDroppable.init({onlydrop:true, success:"uploadImagesAndCreateLayer", action:"add_to_media_library"});
			else return;

		 });

		RVS.DOC.on('windowresized',function() {
			RVS.F.updateCoveredLayers();
		});

		RVS.DOC.on('windowresized, updatesliderlayout_main',function() {
			setTimeout(function() {
				RVS.F.updateCoveredLayers(true);
			},250);
		});

		// Update HTML5 Video Poster from Media Library
		RVS.DOC.on('updatelayerpostermpeg',function(a,b) {
			if (b!==undefined && b.val!==undefined) {
				RVS.F.videoExtract.get(b.val,function(data) {
					RVS.F.setBGPosterImage(data.path,data.id,"layer");
				},document.getElementById("layer_video_start").value,"layer");
			}
		});


		RVS.DOC.on('uploadImagesAndCreateLayer',function(e,param) {
			if (param!==undefined && param.path!==undefined && param.path.length>4) {
				var type = (param.path.substr(param.path.length - 4)).toLowerCase();
				switch (type) {
					case ".png":
					case ".bmp":
					case ".jpg":
					case ".gif":
					case "jpeg":
						var newID = RVS.F.addLayer({type:"image",forceSelect:true,subtype:"predefined", extension:{size:{originalHeight:param.height, originalWidth:param.width}, media:{imageId:param.id, imageUrl:param.path}}});
						RVS.F.intelligentUpdateValuesOnLayer(newID);
						setTimeout(function() {
							RVS.DOC.trigger('restoreLayersSize');
						},500);
						RVS.F.selectLayers({id:newID,overwrite:true, action:"add"});
					break;
					case ".mp4":
					case ".mp3":
					case ".mov":
					case ".m4v":
					case "mpeg":
						var newID = RVS.F.addLayer({type:"video",forceSelect:true,subtype:"predefined", extension:{media:{id:param.id, mp4Url:param.path}}});
					break;
				}
			}

		});

		RVS.DOC.on('click','.convert_layer_into',function() {
			var into = this.dataset.into;
			if (into==="none") return;
			for (var lid in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(lid)) continue;
				RVS.L[RVS.selLayers[lid]].type = into;
				RVS.L[RVS.selLayers[lid]] = RVS.F.addLayerObj(RVS.F.safeExtend(true,RVS.F.addLayerObj(into,undefined,false,true), RVS.L[RVS.selLayers[lid]]));
				RVS.H[RVS.selLayers[lid]].w.remove();
				delete RVS.H[RVS.selLayers[lid]];
				RVS.F.buildHTMLLayer({uid:RVS.selLayers[lid]});
				RVS.F.drawHTMLLayer({uid:RVS.selLayers[lid]});
				jQuery('#tllayerlist_element_276_'+RVS.selLayers[lid]).find('.layerlist_element_type').html('<i class="material-icons">'+RVS.F.getLayerIcon(into)+'</i>');
				RVS.F.showInfo({content:RVS_LANG.convertedlayer, type:"success", showdelay:0, hidedelay:2, hideon:"", event:"" });
			}

			window.lastselectedlayers = new Array();
			for (var i in RVS.selLayers) if (RVS.selLayers.hasOwnProperty(i)) lastselectedlayers.push(RVS.selLayers[i]);
			RVS.F.showForms(RVS.eMode.menu,true);
			for (var i in lastselectedlayers) if (lastselectedlayers.hasOwnProperty(i)) RVS.F.selectLayers({id:lastselectedlayers[i],overwrite:false, action:"add"});
            RVS.F.updateLayerInputFields();
		});

		RVS.DOC.on('updateLayerLoopTimelineframes', function(e,p) {
			if (p!==undefined && p.eventparam=="updateAllLayerFrames") RVS.F.updateAllLayerFrames();
			RVS.F.updateLayerTimelineLoopLists();
		});

		RVS.DOC.on('updateCustomCSSLayerInput',  RVS.F.updateCustomCSS);

		RVS.DOC.on('showhidelayerlooping',function(a,b) {
			jQuery('.la_loopings_tab').hide();
			jQuery('#la_loopings_tab_'+b).show();
		});


		RVS.DOC.on('redrawInnerHTML',function(e,param) {
			RVS.F.redrawTextLayerInnerHTML(param.layerid);
		});

		//TEXT LAYER CONTENT CHANGE
		RVS.DOC.on('layerTextContentUpdate',function(e,a) {
			if (a!==undefined && a.val!==undefined)
				for (var lid in RVS.selLayers) {
					if(!RVS.selLayers.hasOwnProperty(lid)) continue;
					var uid = RVS.selLayers[lid];
					if (RVS.L[uid].type==="text" || RVS.L[uid].type==="button") {
						if (RVS.H[uid].splitText!==undefined) RVS.H[uid].splitText.revert();
						RVS.H[uid].splitText = undefined;
						if (a.eventparam==="placeholder" && (a.val.length===0 || a.val==="" || a.val===" "))
							RVS.H[uid].c[0].innerHTML = jQuery.inArray(RVS.L[uid].idle.whiteSpace[RVS.screen].v,["normal","nowrap"])>=0 ? RVS.F.replaceMetas(RVS.L[uid].text) : RVS.F.replaceMetas(RVS.L[uid].text.replace(/\r\n|\r|\n/g,"<br />"));
						else
						if (RVS.L[uid].placeholder===undefined || RVS.L[uid].placeholder==="" || a.eventparam==="placeholder") RVS.H[uid].c[0].innerHTML = jQuery.inArray(RVS.L[uid].idle.whiteSpace[RVS.screen].v,["normal","nowrap"])>=0 ? RVS.F.replaceMetas(a.val) : RVS.F.replaceMetas(a.val.replace(/\r\n|\r|\n/g,"<br />"));
						RVS.F.drawHTMLLayer({uid:uid,ignoreLayerAnimation:true});
						if (RVS.H[uid].splitText!==undefined) RVS.F.updateLayerFrames({layerid:uid});
					}
				}
		});


		// TEXT LAYER INPUT CHANGES
		RVS.DOC.on('input','#ta_layertext',function() {
			var a = this.value;
			for (var lid in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(lid)) continue;
				var uid = RVS.selLayers[lid];
				if (RVS.L[uid].type==="text" || RVS.L[uid].type==="button") {
					if (RVS.H[uid].splitText!==undefined) RVS.H[uid].splitText.revert();
					RVS.H[uid].splitText = undefined;
					 RVS.H[uid].c[0].innerHTML = jQuery.inArray(RVS.L[uid].idle.whiteSpace[RVS.screen].v,["normal","nowrap"])>=0 ? RVS.F.replaceMetas(a) : RVS.F.replaceMetas(a.replace(/\r\n|\r|\n/g,"<br />"));
				}
			}
		});



		// INSERT LINEBREAK AT POSITION
		RVS.DOC.on('addBRtoTextLayer',function(e,a) {
			var insertAt = jQuery('#ta_layertext')[0].selectionStart;
			RVS.F.openBackupGroup({id:"insertlinebreak",txt:"Insert Line Break",icon:"subdirectory_arrow_right"});
			for (var lid in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(lid)) continue;
				var uid = RVS.selLayers[lid],
					pre = RVS.S.slideId+".layers."+uid+".text",
					front = RVS.L[uid].text.substring(0, insertAt),
					back = RVS.L[uid].text.substring(insertAt, RVS.L[uid].text.length);
				RVS.F.updateSliderObj({path:pre,val:front + '<br />' + back});
				RVS.F.redrawTextLayerInnerHTML(uid,true);
				//RVS.F.drawHTMLLayer({uid:uid});
			}
			RVS.F.closeBackupGroup({id:"insertlinebreak"});
			RVS.F.updateLayerInputFields();
		});


		//SCREEN SIZE CHANGED, NEED TO REFRESH FIELDS
		RVS.DOC.on('screenSelectorChanged',function() {
			window.lastselectedlayers = new Array();
			for (var i in RVS.selLayers) if (RVS.selLayers.hasOwnProperty(i)) lastselectedlayers.push(RVS.selLayers[i]);
			if (RVS.SLIDER[RVS.S.slideId].slide.static.isstatic && RVS.S.lastShownSlideId !== undefined) {
				RVS.F.showWaitAMinute({fadeIn:50,text:"Please Wait"});
				tpGS.gsap.to('#rev_slider_ul',0.05,{opacity:0});
				setTimeout(function() {
					var backtoslideid = RVS.S.slideId;
					RVS.F.mainMode({mode:"slidelayout",slide:RVS.S.lastShownSlideId});
					RVS.F.updateLayerInputFields();RVS.F.expandCollapseTimeLine(true,"open");
					RVS.F.sliderUpdateAfterResize();
					setTimeout(function() {
						RVS.F.mainMode({mode:"slidelayout",slide:backtoslideid});
						RVS.F.showForms(RVS.eMode.menu,true);
						RVS.F.sliderUpdateAfterResize();
						for (var i in lastselectedlayers) if (lastselectedlayers.hasOwnProperty(i)) RVS.F.selectLayers({id:lastselectedlayers[i],overwrite:false, action:"add"});
						RVS.F.showWaitAMinute({fadeOut:200,text:"Please Wait"});
						tpGS.gsap.to('#rev_slider_ul',0.5,{opacity:1});
					},250);
				},100);
			}
			RVS.F.updateAllHTMLLayerPositions();
			RVS.F.updateLayerInputFields();RVS.F.expandCollapseTimeLine(true,"open");
		});

		RVS.DOC.on('sliderSizeChanged',function() {
			//RVS.F.buildHTMLLayers();
			RVS.F.drawHTMLLayers();
			RVS.F.updateCurTime({pos:true, cont:true, force:true, left:0,refreshMainTimeLine:true, caller:"GoToIdle"});
		});

		//UPDATE LAYER BACKGROUND IMAGE
		RVS.DOC.on('updatelayerbgimage',RVS.F.updateLayerBgImage);

		//UPDATE LAYER SRC IMAGE
		RVS.DOC.on('updatelayerimagesrc',RVS.F.updateLayerImageSrc);

		//UPDATE LAYER VIDEO MEDIA SRC
		RVS.DOC.on('resetVideoPlaceholder',RVS.F.resetVideoPlaceholder);

		//RESYNC VIDEO ASPECT RATIO
		RVS.DOC.on('syncVideoRatio',RVS.F.resyncVideoAspectRatio);

		//LOCK/UNLOCK MARGIN AND PADDING FIELDS
		RVS.DOC.on('lockMargin',function(e,ds) {RVS.F.lockUnlockMarginPadding(ds,"margin",".#size#.v");});
		RVS.DOC.on('lockPadding',function(e,ds) {RVS.F.lockUnlockMarginPadding(ds,"padding",".#size#.v");});
		RVS.DOC.on('lockBorder',function(e,ds) {RVS.F.lockUnlockMarginPadding(ds,"borderWidth","");});
		RVS.DOC.on('lockBorderRadius',function(e,ds) {RVS.F.lockUnlockMarginPadding(ds,"borderRadius",".v");});
		RVS.DOC.on('lockBorderHover',function(e,ds) {RVS.F.lockUnlockMarginPadding(ds,"borderWidth","","hover");});
		RVS.DOC.on('lockBorderRadiusHover',function(e,ds) {RVS.F.lockUnlockMarginPadding(ds,"borderRadius",".v","hover");});

		RVS.DOC.on('updateMarginInput',function(e,ds) {RVS.F.updateMarginPaddingValues(ds,"margin",".#size#.v");});
		RVS.DOC.on('updatePaddingInput',function(e,ds) {RVS.F.updateMarginPaddingValues(ds,"padding",".#size#.v");});

		RVS.DOC.on('updateBorderInput',function(e,ds) {RVS.F.updateMarginPaddingValues(ds,"borderWidth","");});
		RVS.DOC.on('updateBorderRadiusInput',function(e,ds) {RVS.F.updateMarginPaddingValues(ds,"borderRadius",".v");});
		RVS.DOC.on('updateBorderInputHover',function(e,ds) {RVS.F.updateMarginPaddingValues(ds,"borderWidth","","hover");});
		RVS.DOC.on('updateBorderRadiusInputHover',function(e,ds) {RVS.F.updateMarginPaddingValues(ds,"borderRadius",".v","hover");});

		//MANIUPLATE FONT WEIGHTS BASED ON AVAILABLE FONT WEIGHTS
		RVS.DOC.on('updateFontFamily',function(e,p) {
			p = p === 'fontweight';
			RVS.F.checkUsedFonts(p);
		});

		//UPDATE INPUT FIELDS ON EVENT
		RVS.DOC.on('updateInputFields',function(e,p) {
			RVS.F.updateLayerInputFields();
		});

		/* LAYER SIZE CHANGE, NEED TO CHANGE ALL DEPENDING ATTRIBUTES */
		RVS.DOC.on('layerSizeChange',function(e,ds) {
			RVS.F.changeLayerSizes({direction:ds.eventparam, v:ds.val});
			for (var si in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(si)) continue;
				var i = RVS.selLayers[si]
				RVS.F.updateHTMLLayerPosition({ uid:i, updateValues:false,lhCwidth:RVS.H[i].c.outerWidth(), lhCheight:RVS.H[i].c.outerHeight()});
			}
		});

		/*LAYER AUDIO CONTROL CHANGED, SIZE MUST BE CHECKED */
		RVS.DOC.on('audioControlOnOff',function(e,ds) {
			RVS.F.changeAudioLayerSize({v:ds.val});
		});

		RVS.DOC.on('disableAllMediaControls',function(e,ds) {
			RVS.F.changeMediaControlsInteraction({v:ds.val});
		});

		/* LAYER SIZE ASPECT RATIO LOCKED */
		RVS.DOC.on('lockLayerRatio',function(e,ds) {
			RVS.F.lockUnlockLayerRatio(ds);
		});

		RVS.DOC.on('layerSizePreset',function(e,ds) {
			RVS.F.openBackupGroup({id:"layerresize",txt:"Size Preset",icon:"photo_size_select_large"});

			for (var lid in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(lid)) continue;
				var l = RVS.L[RVS.selLayers[lid]],
					pre = RVS.S.slideId+".layers."+RVS.selLayers[lid]+".";
				RVS.F.updateSliderObj({path:pre+'size.covermode',val:ds.val});
				switch (jQuery('#layer_covermode').val()) {
					case "custom":
						if (RVS.L[RVS.selLayers[lid]].type==="image") {
							RVS.F.updateSliderObj({path:pre+'size.scaleProportional',val:true});
							var newDims = RVS.F.getProportionalSizes({	proportional:true,
													type:"fit",
													image:{width:l.size.originalWidth, height:l.size.originalHeight},
													viewPort:{width:l.size.width[RVS.screen].v, height:l.size.height[RVS.screen].v}
												});
							RVS.F.updateSliderObj({path:pre+'size.width.#size#.v',val:newDims.width+"px"});
							RVS.F.updateSliderObj({path:pre+'size.height.#size#.v',val:newDims.height+"px"});

						}
					break;
					case "fullwidth":
						if (RVS.L[RVS.selLayers[lid]].type==="image") RVS.F.updateSliderObj({path:pre+'size.scaleProportional',val:true});
						RVS.F.updateSliderObj({path:pre+'position.x.#size#.v',val:"0px"});
						RVS.F.updateSliderObj({path:pre+'size.width.#size#.v',val:"100%"});

					break;
					case "fullheight":
						if (RVS.L[RVS.selLayers[lid]].type==="image") RVS.F.updateSliderObj({path:pre+'size.scaleProportional',val:true});
						RVS.F.updateSliderObj({path:pre+'position.y.#size#.v',val:"0px"});
						RVS.F.updateSliderObj({path:pre+'size.height.#size#.v',val:"100%"});
					break;
					case "cover-proportional":
						RVS.F.updateSliderObj({path:pre+'size.scaleProportional',val:true});
						RVS.F.updateSliderObj({path:pre+'position.x.#size#.v',val:"0px"});
						RVS.F.updateSliderObj({path:pre+'size.width.#size#.v',val:"100%"});
						RVS.F.updateSliderObj({path:pre+'position.y.#size#.v',val:"0px"});
						RVS.F.updateSliderObj({path:pre+'size.height.#size#.v',val:"100%"});
					break;
					case "cover":
						RVS.F.updateSliderObj({path:pre+'size.scaleProportional',val:false});
						RVS.F.updateSliderObj({path:pre+'position.x.#size#.v',val:"0px"});
						RVS.F.updateSliderObj({path:pre+'size.width.#size#.v',val:"100%"});
						RVS.F.updateSliderObj({path:pre+'position.y.#size#.v',val:"0px"});
						RVS.F.updateSliderObj({path:pre+'size.height.#size#.v',val:"100%"});
					break;
				}
				RVS.F.drawHTMLLayer({uid:RVS.selLayers[lid]});
			}

			RVS.F.closeBackupGroup({id:"layerresize"});
			RVS.F.updateLayerInputFields();
		});

		// RESTORE ORIGINAL SIZE OF LAYER
		RVS.DOC.on('restoreLayersSize',function(a,b) {
			RVS.F.openBackupGroup({id:"layerresize",txt:"Restore Original Size",icon:"photo_size_select_large"});
			for (var lid in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(lid)) continue;
				var l = RVS.L[RVS.selLayers[lid]],
					pre = RVS.S.slideId+".layers."+RVS.selLayers[lid]+".";
				switch (l.type) {
					case "shape":
						RVS.F.updateSliderObj({path:pre+'size.width.#size#.v',val:l.size.originalWidth+"px"});
						RVS.F.updateSliderObj({path:pre+'size.height.#size#.v',val:l.size.originalHeight+"px"});
					break;
					case "image":
						RVS.F.updateSliderObj({path:pre+'size.width.#size#.v',val:l.size.originalWidth+"px"});
						RVS.F.updateSliderObj({path:pre+'size.height.#size#.v',val:l.size.originalHeight+"px"});
						RVS.F.updateSliderObj({path:pre+'size.scaleProportional',val:true});
					break;
					case "video":

						RVS.F.updateSliderObj({path:pre+'size.width.#size#.v',val:RVS.F.retWitSuf(l.size.originalWidth,"px")});
						RVS.F.updateSliderObj({path:pre+'size.height.#size#.v',val:RVS.F.retWitSuf(l.size.originalHeight,"px")});
						RVS.F.updateSliderObj({path:pre+'size.scaleProportional',val:true});
					break;

					default:
						RVS.F.updateSliderObj({path:pre+'size.width.#size#.v',val:"auto"});
						RVS.F.updateSliderObj({path:pre+'size.height.#size#.v',val:"auto"});
					break;
				}
				RVS.F.drawHTMLLayer({uid:RVS.selLayers[lid]});
			}
			RVS.F.closeBackupGroup({id:"layerresize"});
			RVS.F.updateLayerInputFields();
		});

		// UPDATE AVAILABLE KEYFRAMES IN LIST
		RVS.DOC.on('updateKeyFramesList',function() {

			if (RVS.selLayers.length===0) return;
			RVS.S.keyFrame = RVS.S.keyFrame==="idle" ? RVS.L[RVS.selLayers[0]].timeline.frameToIdle : RVS.S.keyFrame;
			keyframe_lists = keyframe_lists===undefined ? jQuery("#le_keyframes_list_innerwrap") : keyframe_lists;
			keyframe_idle_sel = keyframe_idle_sel===undefined ? jQuery("#set_editor_view") : keyframe_idle_sel;
			RVS.LIB.LAYERANIMS.animSettings[0].className = "selected_"+RVS.S.keyFrame;
			RVS.LIB.LAYERANIMS.translists["0"].detach();
			RVS.LIB.LAYERANIMS.translists["999"].detach();
			var list = "";

			RVS.F.getFrameOrder({layerid:RVS.selLayers[0]});
			// var m=l=c=w=false;


			for (var i in RVS.L[RVS.selLayers[0]].timeline.frameOrder) {
				if(!RVS.L[RVS.selLayers[0]].timeline.frameOrder.hasOwnProperty(i)) continue;
				 var frame = RVS.L[RVS.selLayers[0]].timeline.frameOrder[i].id,
				 	fnr = parseInt(i,0)+1,
					cls = RVS.S.keyFrame==frame ? ' selected' : '',
					/* idleicon = frame!==RVS.L[RVS.selLayers[0]].timeline.frameToIdle ? "panorama_fish_eye" : "visibility", */
					/* idleiconcls = frame!==RVS.L[RVS.selLayers[0]].timeline.frameToIdle ? "" : "selected", */
					afbe = frame==="frame_999" || frame==="frame_0" ? '' : '<div data-evtparam="'+frame+'" data-evt="addkeyframe" class="callEventButton basic_action_button add_frame_after"><i class="material-icons">add</i></div>',
					toidle = ''; //frame==="frame_999" || frame==="frame_0" ? '' : '<div data-layerid="'+RVS.selLayers[0]+'" data-frame="'+frame+'" class="frame_list_eview '+idleiconcls+'"><i class="material-icons">'+idleicon+'</i></div>';
				if (RVS.S.keyFrame==frame) setKeyframeName(fnr);
				var shownum = frame==="frame_0" ? "IN" : frame==="frame_999" ? "OUT" : "TO", //"#"+(fnr-2)
					dropdown = frame==="frame_0" || frame==="frame_999" ? '<i class="material-icons">arrow_drop_down</i>' : '<i class="emptyspace20"></i>',
					editorview = frame===RVS.L[RVS.selLayers[0]].timeline.frameToIdle ? '<i class="material-icons">visibility</i>' : '',
					editorclass = editorview!=='' ? 'with_icon' : '';

				list +='<li id="keyframe_list_el_'+frame+'" class="keyframe_liste keyframe_liste_dyn '+cls+'" data-framenr="'+fnr+'" data-frame="'+frame+'"><div class="keyframe_CP_wrap" data-frame="'+frame+'"><div class="keyframe_CP_toggle"><i class="material-icons">more_vert</i></div></div><div class="keyframe_liste_inner"><span class="frame_list_id">'+shownum+dropdown+'</span><span class="frame_list_title '+editorclass+'">'+editorview+RVS.L[RVS.selLayers[0]].timeline.frames[frame].alias+'</span>'+afbe+'</div>'+toidle+'</li>';
			}

			if (RVS.L[RVS.selLayers[0]].timeline.frameToIdle === RVS.S.keyFrame)
				keyframe_idle_sel.addClass("disabled");
			else
				keyframe_idle_sel.removeClass("disabled");

			keyframe_lists[0].innerHTML = list;
			if (RVS.L[RVS.selLayers[0]].timeline.tloop.use) {
				RVS.F.updateLayerTimelineLoopLists();
			}
			RVS.F.updateFrameOptionsVisual();
		});

		// CLICK ON KEYFRAME SHOULD UPDATE VALUES OF INPUT FIELDS  ---
		RVS.DOC.on('click','.keyframe_liste',function() {RVS.F.setKeyframeSelected(this.dataset.frame);});



		// CICK MAIN IDLE VIEW
		RVS.DOC.on('click','.frame_list_eview',function() {
			var pre = RVS.S.slideId+".layers."+RVS.selLayers[0]+".timeline.frameToIdle";
			RVS.F.updateSliderObj({path:pre,val:this.dataset.frame});
			RVS.DOC.trigger('updateKeyFramesList');
		});


		//ADD A SINGLE KEYFRAME TO THE ANIMATION
		RVS.DOC.on('addkeyframe',function(e,after) {
			if (RVS.selLayers.length===0) return;
			var id = RVS.selLayers[0],
				newframe = getLastFrameIndex({layerid:id}),
				fr = RVS.F.getPrevNextFrame({layerid:id, frame:after});
			if (fr.next.start>fr.cur.end+500) {
				RVS.F.openBackupGroup({id:"AddLayerFrame",txt:"Add KeyFrame",icon:"theaters"});
				RVS.L[id].timeline.frames[newframe] = defaultFrame({speed:400, start:fr.cur.end+100,alias:RVS_LANG.animateto},newframe);
				//RVS.L[id].timeline.frames[newframe].transform = RVS.F.safeExtend(true,{},RVS.L[id].timeline.frames[after].transform);
				//RVS.L[id].timeline.frames[newframe].mask = RVS.F.safeExtend(true,{},RVS.L[id].timeline.frames[after].mask);
				//RVS.L[id].timeline.frames[newframe].bgcolor = RVS.F.safeExtend(true,{},RVS.L[id].timeline.frames[after].bgcolor);
				//RVS.L[id].timeline.frames[newframe].chars = RVS.F.safeExtend(true,{},RVS.L[id].timeline.frames[after].chars);
				//RVS.L[id].timeline.frames[newframe].words = RVS.F.safeExtend(true,{},RVS.L[id].timeline.frames[after].words);
				//RVS.L[id].timeline.frames[newframe].lines = RVS.F.safeExtend(true,{},RVS.L[id].timeline.frames[after].lines);
				 try{
				 	RVS.L[id].timeline.frames[newframe].transform.originX = RVS.L[id].timeline.frames[after].transform.originX;
				 	RVS.L[id].timeline.frames[newframe].transform.originY = RVS.L[id].timeline.frames[after].transform.originY;
				 	RVS.L[id].timeline.frames[newframe].transform.originZ = RVS.L[id].timeline.frames[after].transform.originZ;

				 	RVS.L[id].timeline.frames[newframe].chars.originX = RVS.L[id].timeline.frames[after].chars.originX;
				 	RVS.L[id].timeline.frames[newframe].chars.originY = RVS.L[id].timeline.frames[after].chars.originY;
				 	RVS.L[id].timeline.frames[newframe].chars.originZ = RVS.L[id].timeline.frames[after].chars.originZ;

				 	RVS.L[id].timeline.frames[newframe].words.originX = RVS.L[id].timeline.frames[after].words.originX;
				 	RVS.L[id].timeline.frames[newframe].words.originY = RVS.L[id].timeline.frames[after].words.originY;
				 	RVS.L[id].timeline.frames[newframe].words.originZ = RVS.L[id].timeline.frames[after].words.originZ;

				 	RVS.L[id].timeline.frames[newframe].lines.originX = RVS.L[id].timeline.frames[after].lines.originX;
				 	RVS.L[id].timeline.frames[newframe].lines.originY = RVS.L[id].timeline.frames[after].lines.originY;
				 	RVS.L[id].timeline.frames[newframe].lines.originZ = RVS.L[id].timeline.frames[after].lines.originZ;
				 } catch(e) { console.log(e);}

				RVS.L[id].timeline.frames[newframe].color = RVS.F.safeExtend(true,{},RVS.L[id].timeline.frames[after].color);
				RVS.L[id].timeline.frames[newframe].filter = RVS.F.safeExtend(true,{},RVS.L[id].timeline.frames[after].filter);

				RVS.F.backup({
						path:id+".timeline.frames."+newframe,
						cache:undefined,
						icon:"theaters",
						txt:"Add Layer Keyframe",
						lastkey:newframe,
						layer:id,
						slide:RVS.S.slideId,
						frame:newframe,
						force:true,
						val:RVS.L[id].timeline.frames[newframe],
						old:{},
						backupType:"frame",
						bckpGrType:"AddLayerFrame"
					});

				RVS.F.closeBackupGroup({id:"AddLayerFrame"});

				RVS.F.addLayerFrameOnDemand(RVS.L[id], document.getElementById('tllayerlist_element_'+RVS.S.slideId+'_'+id), newframe);
				RVS.F.getFrameOrder({layerid:id});
				RVS.F.updateFramesZIndexes({layerid:id});
				RVS.DOC.trigger('updateKeyFramesList');
				RVS.S.keyFrame = newframe;
				setTimeout(function() {
					RVS.F.setKeyframeSelected(newframe);
				},20);
			} else {
				RVS.F.showInfo({content:RVS_LANG.notenoughspaceontimeline, type:"warning", showdelay:0, hidedelay:2, hideon:"", event:"" });
			}
		});

		//REMOVE SINGLE KEYFRAME
		RVS.DOC.on('click','#remove_keyframe',function() {
			var id = RVS.selLayers[0];
			RVS.F.openBackupGroup({id:"RemoveLayerFrame",txt:"Remove KeyFrame",icon:"theaters"});
			RVS.F.backup({
						path:id+".timeline.frames."+RVS.S.keyFrame,
						cache:undefined,
						icon:"theaters",
						txt:"Remove Layer Keyframe",
						lastkey:RVS.S.keyFrame,
						layer:id,
						slide:RVS.S.slideId,
						frame:RVS.S.keyFrame,
						force:true,
						val:{},
						old:RVS.L[id].timeline.frames[RVS.S.keyFrame],
						backupType:"frame",
						bckpGrType:"RemoveLayerFrame"
					});
			delete RVS.L[id].timeline.frames[RVS.S.keyFrame];
			jQuery('#'+RVS.S.slideId+"_"+id+"_"+RVS.S.keyFrame).remove();

			if (RVS.S.keyFrame === RVS.L[id].timeline.frameToIdle)
				RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+id+'.timeline.frameToIdle',val:"frame_1"});

			RVS.S.keyFrame = "frame_1";

			RVS.F.getFrameOrder({layerid:id});
			RVS.F.updateFramesZIndexes({layerid:id});
			RVS.DOC.trigger('updateKeyFramesList');
			RVS.F.updateLayerInputFields();
			RVS.F.closeBackupGroup({id:"RemoveLayerFrame"});
		});

		RVS.DOC.on('click','#set_editor_view',function() {
			var id = RVS.selLayers[0];
			RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+id+'.timeline.frameToIdle',val:RVS.S.keyFrame});
			RVS.F.getFrameOrder({layerid:id});
			RVS.F.updateFramesZIndexes({layerid:id});
			RVS.DOC.trigger('updateKeyFramesList');
			RVS.F.renderLayerAnimation({layerid:RVS.selLayers[0]});
		});

		// SWITCH BETWEEN ANIMATION TRANSFORM MODES
		RVS.DOC.on('click','.transtarget_selector',function() {
			var area=jQuery(this).closest('.form_inner');
			area.find('.transtarget_selector').removeClass("selected");
			this.className +=" selected";
			area.find('.group_transsettings').hide();
			jQuery(this.dataset.showtrans).show();
			RVS.S.frameTrgt = this.dataset.frametarget;
		});

		// SWITCH BETWEEN ANIMATION TRANSFORM MODES
		RVS.DOC.on('click','.looptarget_selector',function() {
			jQuery('.looptarget_selector').removeClass("selected");
			this.className +=" selected";
			jQuery('.group_loopsettings').hide();
			jQuery(this.dataset.showloop).show();
		});

		// COPY / PASTE FRAME SETTINGS --------
		RVS.DOC.on('click','.keyframe_CP_wrap',function() {
			window.frameCopyPaste = window.frameCopyPaste===undefined ? { cache:undefined, tool:undefined} : window.frameCopyPaste;
			window.frameCopyPaste.frame = this.dataset.frame==="frame_0" ? "frame_1" : this.dataset.frame;
			window.frameCopyPaste.layerid = RVS.selLayers[0];
			if (window.frameCopyPaste.tool === undefined) {
				jQuery(document.body).append('<div id="frame_copypaste_tool"><div class="copyframe">'+RVS_LANG.copy+'</div><div class="pasteframe">'+RVS_LANG.paste+'</div></div>');
				window.frameCopyPaste.tool = jQuery('#frame_copypaste_tool');
			}
			if (window.frameCopyPaste.cache!==undefined)
				window.frameCopyPaste.tool.addClass("copy_and_paste");
			else
				window.frameCopyPaste.tool.removeClass("copy_and_paste");
			window.frameCopyPaste.visible = true;
			tpGS.gsap.set(window.frameCopyPaste.tool,{display:"block", top:jQuery(this).offset().top});
			return false;
		});

		RVS.DOC.on('click','.copyframe',function() {
			if (window.frameCopyPaste.frame==="frame_1")
				window.frameCopyPaste.cache_0 = RVS.F.safeExtend(true,{},RVS.L[window.frameCopyPaste.layerid].timeline.frames.frame_0);
			else
				delete window.frameCopyPaste.cache_0;

			window.frameCopyPaste.cache = RVS.F.safeExtend(true,{},RVS.L[window.frameCopyPaste.layerid].timeline.frames[window.frameCopyPaste.frame]);
			window.frameCopyPaste.clipPath = RVS.F.safeExtend(true,{},RVS.L[window.frameCopyPaste.layerid].timeline.clipPath);

			window.frameCopyPaste.splitlen = RVS.F.getSplitDelay({layerid:window.frameCopyPaste.layerid, frame:window.frameCopyPaste.frame});
			window.frameCopyPaste.len = RVS.F.addT([window.frameCopyPaste.cache.timeline.speed,window.frameCopyPaste.splitlen]);

			tpGS.gsap.set(window.frameCopyPaste.tool,{display:"none"});
			window.frameCopyPaste.visible = false;
			return false;
		});

		RVS.DOC.on('click','.pasteframe',function() {
			var id = RVS.selLayers[0],
				frame = RVS.L[window.frameCopyPaste.layerid].timeline.frames[window.frameCopyPaste.frame],
				clipPath = RVS.L[window.frameCopyPaste.layerid].timeline.clipPath,
				frame_0 = window.frameCopyPaste.cache_0!==undefined ? RVS.L[window.frameCopyPaste.layerid].timeline.frames.frame_0 : undefined,
				fr = RVS.F.getPrevNextFrame({layerid:id, frame:window.frameCopyPaste.frame}),
				prognose = RVS.F.addT([frame.timeline.start,window.frameCopyPaste.len]);

			if (fr.next.start>prognose) {
				RVS.F.openBackupGroup({id:"updateFrame",txt:"Copy Paste KeyFrame",icon:"theaters"});
				var newframe = RVS.F.safeExtend(true,window.frameCopyPaste.cache,{alias:frame.timeline.alias, timeline:{start:frame.timeline.start}}),
					newclipPath = RVS.F.safeExtend(true,{},window.frameCopyPaste.clipPath),
					newframe_0 = window.frameCopyPaste.cache_0!==undefined ? RVS.F.safeExtend(true,window.frameCopyPaste.cache_0,{alias:frame_0.timeline.alias, timeline:{start:frame_0.timeline.start}}) : undefined;
				if (window.frameCopyPaste.frame!=="frame_999") {
					newframe.timeline.endWithSlide = false;
					newframe.timeline.auto = false;
				}
				// BACKUP AND UPDATE MAIN FRAME
				RVS.F.backup({
						lastkey:window.frameCopyPaste.frame, frame:window.frameCopyPaste.frame,
						layer:window.frameCopyPaste.layerid, path:window.frameCopyPaste.layerid+".timeline.frames."+window.frameCopyPaste.frame,
						cache:undefined, icon:"theaters", txt:"Copy Paste KeyFrame", slide:RVS.S.slideId, force:true, val:newframe, old:frame, backupType:"singleFrame", bckpGrType:"updateFrame"
				});
				 RVS.L[window.frameCopyPaste.layerid].timeline.frames[window.frameCopyPaste.frame] = RVS.F.safeExtend(true,{},newframe);

				 // BACKUP AND UPDATE CLIPPATH
				RVS.F.backup({
						lastkey:window.frameCopyPaste.clipPath,
						layer:window.frameCopyPaste.layerid, path:window.frameCopyPaste.layerid+".timeline.clipPath",
						cache:undefined, icon:"theaters", txt:"Copy Paste clipPath", slide:RVS.S.slideId, force:true, val:newclipPath, old:clipPath, backupType:"clipPath", bckpGrType:"updateFrame"
				});
				 RVS.L[window.frameCopyPaste.layerid].timeline.clipPath = RVS.F.safeExtend(true,{},newclipPath);

				// BACKUP AND UPDATE FRAME_0 IF NEEDED
				 if (frame_0!==undefined) {
				 	RVS.F.backup({
						lastkey:"frame_0", frame:"frame_0",
						layer:window.frameCopyPaste.layerid, path:window.frameCopyPaste.layerid+".timeline.frames.frame_0",
						cache:undefined, icon:"theaters", txt:"Copy Paste KeyFrame", slide:RVS.S.slideId, force:true, val:newframe_0, old:frame_0, backupType:"singleFrame", bckpGrType:"updateFrame"
					});
					 RVS.L[window.frameCopyPaste.layerid].timeline.frames.frame_0 = RVS.F.safeExtend(true,{},newframe_0);
				 }

				RVS.F.closeBackupGroup({id:"updateFrame"});
				RVS.F.updateAllLayerFrames();
				RVS.F.renderLayerAnimation({layerid:window.frameCopyPaste.layerid});
				RVS.F.updateTimeLine({force:true, state:"time",time:RVS.TL.cache.main, timeline:"main", forceFullLayerRender:true, updateCurTime:true});
				RVS.F.selectLayers({id:window.frameCopyPaste.layerid,overwrite:true, action:"add"});
				//RVS.F.updateEasyInputs({container:jQuery('#form_layer_animation'), path:RVS.S.slideId+".layers.", trigger:"init"});
			} else {
				RVS.F.showInfo({content:RVS_LANG.notenoughspaceontimeline, type:"warning", showdelay:0, hidedelay:2, hideon:"", event:"" });
			}
		});

		RVS.DOC.on('mouseleave','#frame_copypaste_tool',function() {
			tpGS.gsap.set(window.frameCopyPaste.tool,{display:"none"});
			window.frameCopyPaste.visible = false;
		});
		// ----------

		//CHECK FRAME0 LEVELS, SMALLEST SAME LEVEL MUST BE SET !
		RVS.DOC.on('checkEnterFrameLevels',function(e,par) {
			if (par!==undefined && par.layerid!==undefined && (RVS.S.keyFrame==="frame_1" || RVS.S.keyFrame==="frame_0")) {
				var otherkeyframe = RVS.S.keyFrame==="frame_0" ? "frame_1" : "frame_0";
				if (RVS.L[par.layerid].timeline.frames[RVS.S.keyFrame].chars.use) RVS.L[par.layerid].timeline.frames[otherkeyframe].chars.use = true;
				if (RVS.L[par.layerid].timeline.frames[RVS.S.keyFrame].words.use) RVS.L[par.layerid].timeline.frames[otherkeyframe].words.use = true;
				if (RVS.L[par.layerid].timeline.frames[RVS.S.keyFrame].lines.use) RVS.L[par.layerid].timeline.frames[otherkeyframe].lines.use = true;
				if (RVS.L[par.layerid].timeline.frames[RVS.S.keyFrame].mask.use) RVS.L[par.layerid].timeline.frames[otherkeyframe].mask.use = true;
			}
			RVS.F.updateFrameOptionsVisual();
			for (var lid in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(lid)) continue;
				RVS.F.updateLayerFrame({layerid:parseInt(RVS.selLayers[lid],0), frame:(RVS.S.keyFrame!=="frame_0" ? RVS.S.keyFrame : "frame_1")});
				RVS.F.updateFrameRealSpeed();
			}
		});

		// HANDLING OF KEYFRAME PREPARED ANIMATIONS
		RVS.DOC.on('click','.layer_transliste_head',function() {
			var open = this.parentNode.className.indexOf("open")>=0;
			jQuery('.layer_transliste').removeClass("open");
			if (!open) this.parentNode.className +=" open";
		});

		RVS.DOC.on('click','.frame_list_id',function() {
			jQuery('#keyframe_list_el_frame_0').append(RVS.LIB.LAYERANIMS.translists["0"]);
			jQuery('#keyframe_list_el_frame_999').append(RVS.LIB.LAYERANIMS.translists["999"]);

			var open = this.parentNode.parentNode.className.indexOf("open")>=0;
			jQuery('.keyframe_liste').removeClass("open");
			if (!open) this.parentNode.parentNode.className +=" open";

			return false;
		});



		// OPEN THE SUBTEMPLATES OF A GROUP IN FRAME ANIMATION TEMPLATES
		RVS.DOC.on('click','.latransgroup_head',function() {
			var open = this.parentNode.className.indexOf("open")>=0;
			jQuery('.latransgroup').removeClass("open");
			if (!open) this.parentNode.className +=" open";
			return false;
		});

		// CACHE THE CURRENT FRAME ANIMATION FOR LATER PROCESSES
		RVS.DOC.on('mouseover','.load_anim_value_wrap',function() {
			if (!window.timelineTemporaryCached) {
				window.timelineTemporaryCached=true;
				RVS.L[RVS.selLayers[0]].timelinecache = RVS.F.safeExtend(true,{},RVS.L[RVS.selLayers[0]].timeline);

			}
		});
		// RESET ORIGINAL FRAME ANIMATION AFTER TEMPLATE HAS BEEN SHOWN
		RVS.DOC.on('mouseleave','.load_anim_value_wrap',function() {
			window.timelineTemporaryCached = false;
			if (RVS.L[RVS.selLayers[0]].timelinecache!==undefined)
				RVS.L[RVS.selLayers[0]].timeline = RVS.F.safeExtend(true,{},RVS.L[RVS.selLayers[0]].timelinecache);
			delete RVS.L[RVS.selLayers[0]].timelinecache;
			clearTimeout(window.timelineTemporaryUpdate);
			window.timelineTemporaryUpdate = setTimeout(function() {
				RVS.F.updateLayerFrames({layerid:RVS.selLayers[0]});
			},50);

			if (!RVS.S.shwLayerAnim)
				RVS.F.stopAllLayerAnimation();
			else
				RVS.F.playLayerAnimation({layerid:RVS.selLayers[0]});

			RVS.F.buildMainTimeLine();
			if (RVS.TL.cache.main<=0)
				RVS.F.updateCurTime({pos:true, cont:true, force:true, left:0,refreshMainTimeLine:true, caller:"load_anim_value_left"});
			else
				RVS.F.updateTimeLine({force:true, state:"time",time:RVS.TL.cache.main, timeline:"main", forceFullLayerRender:true, updateCurTime:true});

		});

		// SHOW FRAME ANIMATION WITH TEMPLATE ANIMATION
		RVS.DOC.on('mouseover','.layer_trans_liste',function() {
			if (this.dataset.tindex==="custom") return false;
			if (RVS.L[RVS.selLayers[0]].timelinecache===undefined) return;
			updateTimeLineByTemplate(RVS.LIB.LAYERANIMS[this.dataset.lindex][this.dataset.gindex].transitions[this.dataset.tindex]);
			RVS.F.renderLayerAnimation({layerid:RVS.selLayers[0]});
			RVS.F.playLayerAnimation({layerid:RVS.selLayers[0], time:this.dataset.lindex==="out" ? "frame_999" : undefined});
		});

		// CHANGE FRAME ANIMATION FROM TEMPLATE AND BACKUP OLD ONE
		RVS.DOC.on('click','.layer_trans_liste',function() {
			if (RVS.L[RVS.selLayers[0]].timelinecache===undefined) return;
			if (this.dataset.tindex==="custom") {
				var clse = jQuery(this);
				this.dataset.mode="create";
				clse.addClass("cla_showentername");
				clse.find('input').trigger('focus').trigger('select');
				RVS.S.waitOnFeedback = { allowed:["cla_entername", "cla_answer_yes","cla_answer_no"], closeEvent:"hideCustomLayerNameEntering"};
				RVS.F.addBodyClickListener();
				return false;
			}
			RVS.F.changeLayerAnimation({direction:this.dataset.lindex, group:this.dataset.gindex, transition: this.dataset.tindex, fromLayerTransListe:true});

			return false;
		});

		//HIDE CUSTOM LAYER NAME ENTERING PROCESS
		RVS.DOC.on('hideCustomLayerNameEntering',function() {
			jQuery('.cla_showentername').removeClass("cla_showentername");

		});

		RVS.DOC.on('click','.edit_custom_layeranimation',function() {
			var ltl = this.dataset.evt!==undefined ? jQuery(this).closest('.presets_listelement') : jQuery(this).closest('.layer_trans_liste');
			ltl[0].dataset.mode="rename";
			ltl.addClass("cla_showentername");
			ltl.find('input').trigger('focus').trigger('select');
			RVS.S.waitOnFeedback = { allowed:["cla_entername", "cla_answer_yes","cla_answer_no"], closeEvent:"hideCustomLayerNameEntering"};
			RVS.F.addBodyClickListener();
			return false;
		});

		RVS.DOC.on('click','.delete_custom_layeranimation',function() {
			var ltl = this.dataset.evt!==undefined ? jQuery(this).closest('.presets_listelement') : jQuery(this).closest('.layer_trans_liste');
			ltl[0].dataset.mode="delete";
			ltl.addClass("cla_showmessage");
			ltl.find('.cla_message').text(RVS_LANG.deletetemplate);
			RVS.S.waitOnFeedback = { allowed:["cla_answer_yes","cla_answer_no"], closeEvent:"hideCustomLayerNameEntering"};
			RVS.F.addBodyClickListener();
			return false;
		});

		RVS.DOC.on('click','.save_custom_layeranimation',function() {
			var ltl = this.dataset.evt!==undefined ? jQuery(this).closest('.presets_listelement') : jQuery(this).closest('.layer_trans_liste');
			ltl[0].dataset.mode="overwrite";
			ltl.addClass("cla_showmessage");
			ltl.find('.cla_message').text(RVS_LANG.overwritetemplate);
			RVS.S.waitOnFeedback = { allowed:["cla_answer_yes","cla_answer_no"], closeEvent:"hideCustomLayerNameEntering"};
			RVS.F.addBodyClickListener();
			return false;
		});

		RVS.DOC.on('click','.cla_answer_no',function() {
			RVS.S.waitOnFeedback = undefined;
			jQuery(document.body).unbind('click.revbuilderbodyclick');
			jQuery('.cla_showentername').removeClass("cla_showentername");
			jQuery('.cla_showmessage').removeClass("cla_showmessage");
			return false;
		});

		//CLICK ON YES CUSTOM LAYER TYPE FUN
		RVS.DOC.on('click','.cla_answer_yes',function() {

			// CUSTOM PRESET ELEMENT (EXTENDED BY ADDONS)
			if (this.dataset.evt!==undefined) {
				var pl = jQuery(this).closest('.presets_listelement'),
					mode = pl[0].dataset.mode,
					newname = pl.find('.cla_entername').val(), element;

				// CREATE LIST ELEMENT
				if (mode==="create") {
					element = jQuery('<div data-custom="true" data-evt="'+this.dataset.evt+'" class="presets_listelement dark_btn"><span class="cla_custom_name">'+newname+'</span><div class="cla_message">'+RVS_LANG.overwritetemplate+'</div><input data-evt="'+this.dataset.evt+'" type="text" value="'+newname+'" class="cla_entername"><div class="custom_layer_animation_toolbar"><i data-evt="'+this.dataset.evt+'" class="cla_answer_yes material-icons">done</i><i data-evt="'+this.dataset.evt+'" class="cla_answer_no material-icons">close</i><i data-evt="'+this.dataset.evt+'" class="edit_custom_layeranimation material-icons">edit</i><i data-evt="'+this.dataset.evt+'" class="save_custom_layeranimation material-icons">save</i><i data-evt="'+this.dataset.evt+'" class="delete_custom_layeranimation material-icons">delete</i></div></div>');
					pl.closest('.presets_listelements').append(element);
				}
				RVS.DOC.trigger(this.dataset.evt,{mode:mode, element:element, pl:pl, key:pl[0].dataset.key, newname:newname});
			} else {
			// ANIMATION PRESET ELEMENT
				var ltl = jQuery(this).closest('.layer_trans_liste'),
					mode = ltl[0].dataset.mode,
					animgroup = RVS.LIB.LAYERANIMS[ltl[0].dataset.lindex][ltl[0].dataset.gindex];
				switch (mode) {
					case "rename":
					case "overwrite":
					case "create":
						var newname = ltl.find('.cla_entername').val(),
							trans = {name:newname},
							tindex,
							element;

						// CREATE LIST ELEMENT
						if (ltl.data('tindex')==="custom" || mode==="create") {
							element = jQuery('<div data-lindex="'+ltl.data("lindex")+'" data-gindex="'+ltl.data("gindex")+'" class="layer_trans_liste dark_btn"><span class="cla_custom_name">'+newname+'</span><div class="cla_message">'+RVS_LANG.overwritetemplate+'</div><input type="text" value="'+newname+'" class="cla_entername"><div class="custom_layer_animation_toolbar"><i class="cla_answer_yes material-icons">done</i><i class="cla_answer_no material-icons">close</i><i class="edit_custom_layeranimation material-icons">edit</i><i class="save_custom_layeranimation material-icons">save</i><i class="delete_custom_layeranimation material-icons">delete</i></div></div>');
							ltl.closest('.lainner_transitions').append(element);
						}
						// GET CHANGES
						if (mode==="overwrite" || ltl.data('tindex')==="custom" || mode==="create") {
							if (ltl.data('lindex')==="in") {
								trans.frame_0 = simplifyFrame(RVS.F.safeExtend(true,{},RVS.L[RVS.selLayers[0]].timelinecache.frames.frame_0));
								trans.frame_1 = simplifyFrame(RVS.F.safeExtend(true,{},RVS.L[RVS.selLayers[0]].timelinecache.frames.frame_1));
							} else
							if (ltl.data('lindex')==="out")
								trans.frame_999 = simplifyFrame(RVS.F.safeExtend(true,{},RVS.L[RVS.selLayers[0]].timelinecache.frames.frame_999));
							else
							if (ltl.data('lindex')==="loop") {
								trans = simplifyFrame(RVS.F.safeExtend(true,{},RVS.L[RVS.selLayers[0]].timelinecache.loop));
								trans.name = newname;
							}
						}
						// GET TINDEX
						if (mode==="overwrite" || mode=="rename")
							tindex = ltl.data("tindex");

						// RENAME, TAKE FIRST EXISTING OBJECT
						if (mode==="rename") {
							trans = animgroup.transitions[tindex];
							animgroup.transitions[tindex].name=newname;
						}

						// CALL AJAX FUNCTION
						RVS.F.ajaxRequest('save_animation', {id:tindex, obj:trans, type:ltl[0].dataset.lindex}, function(response){
							if(response.success) {
								animgroup.transitions[response.id] = trans;
								if (ltl.data('tindex')==="custom" || mode==="create") element[0].dataset.tindex = response.id;
								if (mode==="rename") ltl.find('.cla_custom_name').text(newname);

							}
						});
					break;

					case "delete":
						RVS.F.ajaxRequest('delete_animation', {id:ltl[0].dataset.tindex},function(response) {
							if (response.success) {
								delete animgroup.transitions[ltl[0].dataset.tindex];
								ltl.remove();
							}
						});
					break;
				}
			}

			RVS.S.waitOnFeedback = undefined;
			jQuery(document.body).unbind('click.revbuilderbodyclick');
			jQuery('.cla_showentername').removeClass("cla_showentername");
			jQuery('.cla_showmessage').removeClass("cla_showmessage");
			return false;

		});

		RVS.DOC.on('SvgSelectAllChanged', function(e, obj){
			if(obj === undefined || obj.layerid === undefined) return;
			RVS.H[obj.layerid].c[0].innerHTML = RVS.L[obj.layerid].svg.renderedData;
			RVS.H[obj.layerid].svg = RVS.H[obj.layerid].w.find('svg');
			RVS.H[obj.layerid].svgPath = RVS.H[obj.layerid].w.find( !RVS.L[obj.layerid].idle.svg.styleAll ? 'svg path' : 'svg path, svg circle, svg ellipse, svg line, svg polygon, svg polyline, svg rect');
		});
	}

	// Update to Frame
	RVS.F.setKeyframeSelected = function(frame) {
		RVS.S.keyFrame = frame;
		setKeyframeName(); //this.dataset.framenr);
		RVS.F.updateLayerInputFields();
		RVS.TL.cache.main = RVS.S.keyFrame==="frame_0" ? (RVS.F.getTimeAtSelectedFrameStart("frame_1") / 100) : RVS.S.keyFrame==="frame_999" ? (RVS.F.getTimeAtSelectedFrameMiddle("frame_999") / 100) : (RVS.F.getTimeAtSelectedFrameEnd()-2) / 100;
		RVS.TL.cache.main = RVS.TL.cache.main<0.01 ? 0.01 : RVS.TL.cache.main;
		RVS.F.updateTimeLine({force:true, state:"time",time:RVS.TL.cache.main, timeline:"main", forceFullLayerRender:true, updateCurTime:true});
	};

	// Update to Frame
	RVS.F.updateKeyframeSelected = function(frame) {
		RVS.TL.cache.main = RVS.S.keyFrame==="frame_0" ? (RVS.F.getTimeAtSelectedFrameStart("frame_1") / 100) : RVS.S.keyFrame==="frame_999" ? (RVS.F.getTimeAtSelectedFrameMiddle("frame_999") / 100) : (RVS.F.getTimeAtSelectedFrameEnd()-2) / 100;
		RVS.TL.cache.main = RVS.TL.cache.main<0.01 ? 0.01 : RVS.TL.cache.main;
		RVS.F.updateCurTime({pos:true, cont:true, force:true, left:(RVS.TL.cache.main*100),refreshMainTimeLine:false, caller:"updateKeyframeSelected"});
	};



	// MIGRATION OF OLD CUSTOM IN AND OUT ANIMATION INTO LAYERANIMLIBRARY
	RVS.F.migrateCustomAnimation = function(_) {
		/* var id = 0; */
		for (var i in _) {
			if(!_.hasOwnProperty(i)) continue;
			if (_[i].settings==="in" || _[i].settings==="out") {
				RVS.LIB.LAYERANIMS[_[i].settings].custom.transitions[_[i].id] = _[i].params;
			}
			else if (_[i].params!==undefined && (_[i].params.type==="customin" || _[i].params.type==="customout")) {
				var gid = _[i].params.type=="customin" ? "in" : "out",
					ag = RVS.LIB.LAYERANIMS[gid].custom.transitions,
					fr = gid==="in" ? "frame_0" : "frame_999",
				 	tr = { name: _[i].handle};
				 tr[fr] = {transform:{}, timeline:{}};

				 if (gid==="in")
				 	tr.frame_1 = {timeline : {speed:_[i].params.speed, ease:_[i].params.easing}};
				 else
				 	tr.frame_999.timeline =  {speed:_[i].params.speed, ease:_[i].params.easing};


				// CHECK IF ANIMATION HAS MASK
				if (_[i].params.mask=="true" || _[i].params.mask==true) {
					tr[fr].mask = { use:true, x: _[i].params.mask_x, y: _[i].params.mask_y};
					if (gid==="in") tr.frame_1.mask = { use:true, x: 0, y: 0};
				}

				var inside = tr[fr].transform,
					splithelp = {use:true, delay:_[i].params.splitdelay};


				// SET TARGET ANIMATION TO SPLIT OR LAYER
				switch (_[i].params.split) {
					case "lines": case "line":tr[fr].lines = splithelp;inside = tr[fr].lines; if (gid==="in") tr.frame_1.lines = splithelp;  break;
					case "words": case "word":tr[fr].words = splithelp;inside = tr[fr].words;if (gid==="in") tr.frame_1.words = splithelp; break;
					case "chars":case "char":tr[fr].chars = splithelp;inside = tr[fr].chars;if (gid==="in") tr.frame_1.chars = splithelp; break;
				}


				if (_[i].params.captionopacity !== undefined)  inside.opacity = _[i].params.captionopacity;

				// GO THROUGH THE PARAMS AND CREATE THEM IF NEEDED
				for (var key in _[i].params) {
					if(!_[i].params.hasOwnProperty(key)) continue;
					var val = _[i].params[key];
					if (val!=="inherit" && val!=="0" && val!==0 && val!=="0px")
						switch (key) {
							case "movex": inside.x = val; break;
							case "movey": inside.y = val; break;
							case "movez": inside.z = val; break;
							case "rotationx": inside.rotationX = val; break;
							case "rotationy": inside.rotationY = val; break;
							case "rotationz": inside.rotationZ = val; break;
							case "skewx": inside.skewX = val; break;
							case "skewy": inside.skewY = val; break;
						}
				}
				ag[_[i].id] = tr;
			}
		}
	};

	// REDUCE CONTENT OF A CUSTOM FRAME BEFORE SAVE IT
	function simplifyFrame(_) {
		for (var i in _) {
			if(!_.hasOwnProperty(i)) continue;
			if (jQuery.inArray(i,["chars","filter","color","bgcolor","words","lines","mask"])>=0 && _[i].use===false) delete _[i];
			if (typeof _[i]==="object")
				for (var j in _[i]) {
					if (_[i][j]==="inherit")
						delete _[i][j];
			} else {
				if (_[i]==="inherit") delete _[i];
			}

		}
		return _;

	}
	/*
	UPDATE TIMELINE FROM ANIMATION LIBRARY
	*/
	function updateTimeLineByTemplate(_) {
		if (_===undefined) return;
		if (_.frame_0!==undefined) {
			RVS.L[RVS.selLayers[0]].timeline.frames.frame_0 = fourLevelAnims(RVS.F.safeExtend(true,defaultFrame({alias:RVS_LANG.enterstage,opacity:0},"frame_0"),_.frame_0));
			RVS.L[RVS.selLayers[0]].timeline.frames.frame_0.timeline.start = RVS.L[RVS.selLayers[0]].timelinecache.frames.frame_0.timeline.start;
			RVS.L[RVS.selLayers[0]].timeline.frames.frame_0.timeline.alias = RVS_LANG.enterstage;
		}
		if (_.frame_1!==undefined) {
			RVS.L[RVS.selLayers[0]].timeline.frames.frame_1 = fourLevelAnims(RVS.F.safeExtend(true,defaultFrame({opacity:1,effect:"none",chars:{x: 0,y: 0,z: 0,opacity: 1,rotationZ: 0,rotationX: 0,rotationY: 0,scaleX: 1,scaleY: 1,skewX: 0,skewY: 0},words:{	x: 0,y: 0,z: 0,opacity: 1,rotationZ: 0,rotationX: 0,rotationY: 0,scaleX: 1,scaleY: 1,skewX: 0,skewY: 0},lines:{	x: 0,y: 0,z: 0,opacity: 1,rotationZ: 0,rotationX: 0,rotationY: 0,scaleX: 1,scaleY: 1,skewX: 0,skewY: 0}},"frame_1"),_.frame_1));
			RVS.L[RVS.selLayers[0]].timeline.frames.frame_1.timeline.start = RVS.L[RVS.selLayers[0]].timelinecache.frames.frame_1.timeline.start;
			RVS.L[RVS.selLayers[0]].timeline.frames.frame_1.timeline.alias = RVS_LANG.onstage;
		}
		if (_.frame_999!==undefined) {
			RVS.L[RVS.selLayers[0]].timeline.frames.frame_999 = fourLevelAnims(RVS.F.safeExtend(true,defaultFrame({endWithSlide:true,alias:RVS_LANG.leavestage,opacity:0},"frame_999"),_.frame_999));
			RVS.L[RVS.selLayers[0]].timeline.frames.frame_999.timeline.start = RVS.L[RVS.selLayers[0]].timelinecache.frames.frame_999.timeline.start;
			RVS.L[RVS.selLayers[0]].timeline.frames.frame_1.timeline.alias = RVS_LANG.leavestage;
		}
		if (_.loop!==undefined) {
			RVS.L[RVS.selLayers[0]].timeline.loop = RVS.F.safeExtend(true,defaultLoopFrame(),_.loop);
			RVS.L[RVS.selLayers[0]].timeline.loop.start = RVS.L[RVS.selLayers[0]].timelinecache.loop.start;
		}

		clearTimeout(window.timelineTemporaryUpdate);
		window.timelineTemporaryUpdate = setTimeout(function() {
			RVS.F.updateLayerFrames({layerid:RVS.selLayers[0]});
		},50);
	}

	/*
	SHOW, HIDE THE "+" BUTTON IN FRAME LEVELS AND SET OTHER LEVEL ACTIVE IF IT IS NOT AVILABLE IN THE SELECTED FRAME
	*/
	RVS.F.updateFrameOptionsVisual = function() {

		for (var i in RVS.V.frameLevels.levels) {
			if(!RVS.V.frameLevels.levels.hasOwnProperty(i)) continue;
			var level = RVS.V.frameLevels.levels[i];
			RVS.V.frameLevels[level] = RVS.V.frameLevels[level]===undefined ? jQuery('#'+level+'_ts_wrapbrtn') : RVS.V.frameLevels[level];
			if ((level==="color" && (RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame]!==undefined && RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame].bgcolor !== undefined && RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame].color !== undefined)  && (RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame].bgcolor.use!==false || RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame].color.use!==false)) ||
				(level==="sfx" && (RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame]!==undefined && RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame].sfx.effect!=="" && RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame].sfx.effect!=="none")) ||
				(level==="mask" && RVS.L[RVS.selLayers[0]].timeline.clipPath.use) ||
				(level!=="color" && level!=="sfx" && (RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame]!==undefined && RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame][level]!==undefined && RVS.L[RVS.selLayers[0]].timeline.frames[RVS.S.keyFrame][level].use))
				)
				RVS.V.frameLevels[level][0].className="ts_wrapbrtn";
			else
				RVS.V.frameLevels[level][0].className="ts_wrapbrtn notinuse";
		}
		for (var i in RVS.JHOOKS.updateFrameOptionsVisual) {
			if(!RVS.JHOOKS.updateFrameOptionsVisual.hasOwnProperty(i)) continue;
			RVS.JHOOKS.updateFrameOptionsVisual[i]();
		}
	};


	function getLastFrameIndex(_) {
		var lindex = 0;
		for (var frame in RVS.L[_.layerid].timeline.frames) {
			if(!RVS.L[_.layerid].timeline.frames.hasOwnProperty(frame)) continue;
			var index = parseInt(frame.split("frame_")[1],0);
			lindex = lindex<=index && index<998 ? index+1 : lindex;
		}
		return "frame_"+lindex;
	}



	// CREATE AVAILABLE PREDEFINED LAYER ANIMATION LISTS
	function createLayerAnimationLists() {
		RVS.LIB.LAYERANIMS.animSettings = jQuery('#form_layer_animation_innerwrap');
		var ih = {in:"", out:"", loop:""};


		for (var f in RVS.LIB.LAYERANIMS) {
			if(!RVS.LIB.LAYERANIMS.hasOwnProperty(f)) continue;
			if (f==="in" || f==="out" || f==="loop") {
				for (var g in RVS.LIB.LAYERANIMS[f]) {
					if(!RVS.LIB.LAYERANIMS[f].hasOwnProperty(g)) continue;
					var _g = '<div id="lal_'+f+"_"+g+'" class="latransgroup '+(RVS.LIB.LAYERANIMS[f][g].custom ? 'custom_lainner_trans_'+g : '')+'"><div class="latransgroup_head"><span class="latransgroup_name">'+RVS.LIB.LAYERANIMS[f][g].group+'</span><div class="animation_drop_arrow"><i class="material-icons">arrow_drop_down</i></div></div><div class="lainner_transitions">';
					if (RVS.LIB.LAYERANIMS[f][g].custom) {
						_g += '<div data-lindex="'+f+'" data-gindex="'+g+'" data-tindex="custom" class="layer_trans_liste dark_btn"><span class="cla_custom_name">'+RVS_LANG.savecurrenttemplate+'</span><input type="text" value="custom" class="cla_entername"><div class="custom_layer_animation_toolbar"><i class="cla_answer_yes material-icons">done</i><i class="cla_answer_no material-icons">close</i><i class="add_custom_layeranimation material-icons">add</i></div></div>';
						RVS.LIB.LAYERANIMS[f][g].amount = 1;
						for (var p in RVS.LIB.LAYERANIMS[f][g].transitions) {
							if(!RVS.LIB.LAYERANIMS[f][g].transitions.hasOwnProperty(p)) continue;
							RVS.LIB.LAYERANIMS[f][g].transitions[p].customindex = RVS.LIB.LAYERANIMS[f][g].transitions[p].customindex=== undefined ? RVS.LIB.LAYERANIMS[f][g].amount : RVS.LIB.LAYERANIMS[f][g].transitions[p].customindex;
							_g += '<div data-lindex="'+f+'" data-gindex="'+g+'" data-tindex="'+p+'" class="layer_trans_liste dark_btn"><span class="cla_custom_name">'+RVS.LIB.LAYERANIMS[f][g].transitions[p].name+'</span><div class="cla_message">'+RVS_LANG.overwritetemplate+'</div><input type="text" value="'+RVS.LIB.LAYERANIMS[f][g].transitions[p].name+'" class="cla_entername"><div class="custom_layer_animation_toolbar"><i class="cla_answer_yes material-icons">done</i><i class="cla_answer_no material-icons">close</i><i class="edit_custom_layeranimation material-icons">edit</i><i class="save_custom_layeranimation material-icons">save</i><i class="delete_custom_layeranimation material-icons">delete</i></div></div>';
							RVS.LIB.LAYERANIMS[f][g].amount = parseInt(RVS.LIB.LAYERANIMS[f][g].transitions[p].customindex,0)+1;
						}
					} else {
						for (var p in RVS.LIB.LAYERANIMS[f][g].transitions) {
							if(!RVS.LIB.LAYERANIMS[f][g].transitions.hasOwnProperty(p)) continue;
							_g += '<div data-lindex="'+f+'" data-gindex="'+g+'" data-tindex="'+p+'" class="layer_trans_liste dark_btn">'+RVS.LIB.LAYERANIMS[f][g].transitions[p].name+'</div>';
						}
					}
					_g += '</div></div>';
					ih[f] += _g;
				}
			}
		}
		RVS.LIB.LAYERANIMS.translists = {
			"0": jQuery('<div id="layer_transliste_0" class="load_anim_value_wrap">'+ih.in+'</div>'),
			"999":jQuery('<div id="layer_transliste_999" class="load_anim_value_wrap">'+ih.out+'</div>')
		};

		jQuery('#layer_transliste_loop').append(ih.loop);

	}
	// EXTEND LIBRARY WITH FURTHER ANIMATIONS direct:"in/out", handle:"group handle", presets: Object
	RVS.F.extendLayerAnimationLists = function(_) {
		var f = _.direction,
			g = _.handle;
		RVS.LIB.LAYERANIMS[f][g] = RVS.F.safeExtend(true,{},_.preset);
		if (RVS.LIB.LAYERANIMS.translists!==undefined) {
			var _g = '<div class="latransgroup"><div class="latransgroup_head"><span class="latransgroup_name">'+RVS.LIB.LAYERANIMS[f][g].group+'</span><div class="animation_drop_arrow"><i class="material-icons">arrow_drop_down</i></div></div><div class="lainner_transitions">',
				_ing = '';
			for (var p in RVS.LIB.LAYERANIMS[f][g].transitions) {
				if(!RVS.LIB.LAYERANIMS[f][g].transitions.hasOwnProperty(p)) continue;
				_ing += '<div data-lindex="'+f+'" data-gindex="'+g+'" data-tindex="'+p+'" class="layer_trans_liste dark_btn">'+RVS.LIB.LAYERANIMS[f][g].transitions[p].name+'</div>';
			}
			if (jQuery('#lal_'+f+'_'+g).length>0) {
				jQuery('#lal_'+f+'_'+g).find('.lainner_transitions')[0].innerHTML = jQuery('#lal_'+f+'_'+g).find('.lainner_transitions')[0].innerHTML + _ing;
			} else {
				_g += _ing;
				_g += '</div></div>';
				if (f==="in") RVS.LIB.LAYERANIMS.translists["0"].append(_g);
				else
				if (f==="out") RVS.LIB.LAYERANIMS.translists["999"].append(_g);
			}
		}
	};

	function colorEditSub(i,n,val) {
		switch (n) {
			case "layerTextColorHover": case "layerTextColor": case "layerTextColorInFrame": case "frameColorAnimation":
					if (jQuery.inArray(RVS.L[i].type,["text","button"])>=0)
						if (RVS.H[i].splitText!==undefined)
							tpGS.gsap.to([RVS.H[i].c, RVS.H[i].splitText.chars,RVS.H[i].splitText.words,RVS.H[i].splitText.lines],0.001,{color:val});
						else
							tpGS.gsap.to([RVS.H[i].c],0.001,{color:val});
			break;
			case "frameBGColorAnimation": case "frameBGColorAnimationDouble": case "layerBGColor":
					//BG COLOR OF SLIDER
					if (val.indexOf("gradient")>=0)
						tpGS.gsap.to((RVS.L[i].type==="column" ?  RVS.H[i].bg : RVS.H[i].c),0.001,{background:val});
					else {
						if (RVS.L[i].idle.backgroundImage!==undefined)
							tpGS.gsap.to((RVS.L[i].type==="column" ?  RVS.H[i].bg : RVS.H[i].c),0.001,{backgroundImage:"url("+RVS.L[i].idle.backgroundImage+")", backgroundColor:val});
						else
							tpGS.gsap.to((RVS.L[i].type==="column" ?  RVS.H[i].bg : RVS.H[i].c),0.001,{background:val});
					}
			break;
			case "layerBorderColor":
				tpGS.gsap.to([RVS.H[i].c],0.001,{'borderColor':val});
			break;
			case "layerSVGColor":
					if (RVS.L[i].type==="svg") tpGS.gsap.to([RVS.H[i].svgPath,RVS.H[i].svg],0.001,{fill:val});
			break;

			case "layerStrokeColor":
					if (RVS.L[i].type==="svg") tpGS.gsap.to([RVS.H[i].svgPath,RVS.H[i].svg],0.001,{stroke:val});
			break;
		}
	}

	/*
	EDIT / CANCEL A COLOR VALUE (SHOW LIVE THE CHANGES)
	*/
	function colorEditLayer(e,inp, val, gradient, onSave,GC) {

		if (inp!==undefined) window.lastColorEditjObj = inp;
		else if (window.lastColorEditjObj!==undefined) val = window.RSColor.get(window.lastColorEditjObj.val());
		if (val===undefined) return;

		// only write the value if the color picker was saved
		if (inp!==undefined && onSave) inp.val(gradient || val).change();


		for (var sl in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(sl)) continue;
			var i = RVS.selLayers[sl];
			colorEditSub(i,window.lastColorEditjObj[0].name,val);
		}
		// UPDATE ALL RELEVANT GLOBAL COLORS

		if (GC)
			for (var i in GC.ref) {
				if (GC.ref[i].type==="layer" && GC.ref[i].slide==RVS.S.slideId) colorEditSub(GC.ref[i].layer,GC.ref[i].inpname,val);
				if (GC.ref[i].type==="layer" && onSave) {
					RVS.F.updateSliderObj({path:GC.ref[i].r,val:val,slide:GC.ref[i].slide});
					if (GC.ref[i].slide==RVS.S.slideId && RVS.selLayers[0]==GC.ref[i].layer) {
						var upinp = jQuery('input[name='+GC.ref[i].inpname+']');
						if (upinp.length>0) {
							upinp[0].value = val;
							upinp.rsColorPicker("refresh");
						}
					} else if (GC.ref[i].slide!=RVS.S.slideId) RVS.F.slideinWork(GC.ref[i].slide);
				}
				//RVS.F.backup({path:GC.ref[i].r,cache:undefined, val:val,old:RVS.F.getDeepVal({path:GC.ref[i].r})});
			}


		// REDRAW LAYER AND RECALC ANIMATIONS
		if (onSave || e.type==="colorcancel")
			for (var i in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(i)) continue;
				RVS.F.drawHTMLLayer({uid:RVS.selLayers[i]});
			}
	}

/*************************************
    - 	GOOGLE FONT HANDLING -
***************************************/
	function getGFontObj(font) {
		var ret = false;
		for(var key in RVS.LIB.FONTS) if (RVS.LIB.FONTS.hasOwnProperty(key)) {
			if(RVS.LIB.FONTS[key].label == font){
				if(RVS.LIB.FONTS[key].type == 'googlefont') ret =  RVS.LIB.FONTS[key];
				break;
			}
		}
		return ret;
	}

	// CHECK IF LAYER CURRENT FONT SETTING LOADED ALREADY
	RVS.F.checkUsedFonts = function(fontWeightChange) {

		// CHECK IF FONTWEIGHT AND ITALIC AND FONT TYPE ALREADY LOADED
		RVS.F.checkAvailableFontWeights(fontWeightChange);

		var requiredGoogleFonts = {},
			familiesToLoad = [];

		for (var i in RVS.L) {
			if(!RVS.L.hasOwnProperty(i)) continue;
			var layer = RVS.L[i];
			if ((layer.type==="text" || layer.type==="button") && layer.idle!==undefined && layer.idle.fontFamily!==undefined) {
				var family = layer.idle.fontFamily.replace(/\ /g,'_'),
					weights = [];
				for (var s in RVS.V.sizes) {
					if(!RVS.V.sizes.hasOwnProperty(s)) continue;
					var w = layer.idle.fontWeight[RVS.V.sizes[s]].v;
					w = layer.idle.fontStyle ? w+"italic" : w;
					if (jQuery.inArray(w,weights)===-1 && (loadedFonts[family]===undefined || jQuery.inArray(w,loadedFonts[family].weights)===-1) &&  (requiredGoogleFonts[family]===undefined || jQuery.inArray(w,requiredGoogleFonts[family].weights)===-1))
							weights.push(w);
				}

				if (weights.length>0) {
					if (requiredGoogleFonts[family]===undefined)
						requiredGoogleFonts[family] = {family:family, font:layer.idle.fontFamily, weights:weights, italic:layer.idle.fontStyle};
					else
						requiredGoogleFonts[family] = {family:family, font:layer.idle.fontFamily, weights: RVS.F.mergeArrays(weights, requiredGoogleFonts[family].weights) , italic:(requiredGoogleFonts[family]===true ? true : layer.idle.fontStyle)};
				}

			}
		}

		for (var i in requiredGoogleFonts) {
			if(!requiredGoogleFonts.hasOwnProperty(i)) continue;
			var familie = RVS.F.loadSingleFont(requiredGoogleFonts[i]);
			if (familie!==undefined) familiesToLoad.push(familie);
		}

		RVS.F.do_google_font_load(familiesToLoad,{silent:true});

	};

	// PRELOAD GOOGLE FONT WITH ONLY NEEDED WEIGHT AND SUBTYPE OPTIONAL ITALIC IN ONE GO
	RVS.F.preloadUsedFonts = function() {
		var requiredGoogleFonts = {},
			familiesToLoad = [];
		for (var i in RVS.L) {
			if(!RVS.L.hasOwnProperty(i)) continue;
			if ((RVS.L[i].type==="text" || RVS.L[i].type==="button") && RVS.L[i].idle!==undefined && RVS.L[i].idle.fontFamily!==undefined) {
				var family = RVS.L[i].idle.fontFamily.replace(/\ /g,'_');
				requiredGoogleFonts[family] = requiredGoogleFonts[family]===undefined ? {family:family, weights:[], italic:false} : requiredGoogleFonts[family];
				requiredGoogleFonts[family].font = RVS.L[i].idle.fontFamily;
				requiredGoogleFonts[family].italic = requiredGoogleFonts[family].italic===true ? true : RVS.L[i].idle.fontStyle;
				for (var s in RVS.V.sizes) {
					if(!RVS.V.sizes.hasOwnProperty(s)) continue;
					if (requiredGoogleFonts[family].weights.toString().indexOf(RVS.L[i].idle.fontWeight[RVS.V.sizes[s]].v)===-1) requiredGoogleFonts[family].weights.push(RVS.L[i].idle.fontWeight[RVS.V.sizes[s]].v);
				}
			}
		}

		for (var i in requiredGoogleFonts) {
			if(!requiredGoogleFonts.hasOwnProperty(i)) continue;
			var familie = RVS.F.loadSingleFont(requiredGoogleFonts[i]);
			if (familie!==undefined) familiesToLoad.push(familie);
		}

		RVS.F.do_google_font_load(familiesToLoad);
	};


	function checkWeightsInVariants(a,b) {
		var ret = [];
		for (var i in a) {
			if(!a.hasOwnProperty(i)) continue;
			if (jQuery.inArray(a[i],b)>=0) ret.push(a[i]);
		}
		return ret;
	}

	// LOAD SINGLE FONT WITH ALL ITS WEIGHTS, SUBTYPES  RVS.F.preloadUsedFonts();
	RVS.F.loadSingleFont = function(_){
		var gFontType = getGFontObj(_.font);


		if (gFontType) {
			//CACHE AND MANIPULATE VALUES

			_.weights = _.weights === undefined ? [] : _.weights;
			_.subsets = _.subsets === undefined ? [] : _.subsets;
			_.font = _.font.replace(/\ /g,'+');
			// IF LOADED, SIMPLE TRIGGER CALLBACK
			if (loadedFonts[_.family]===undefined || !RVS.F.matchArray(_.weights, loadedFonts[_.family].weights)) {


				loadedFonts[_.family] = loadedFonts[_.family]===undefined ? {weights:_.weights, subsets:_.subsets} : loadedFonts[_.family];
				loadedFonts[_.family].weights = RVS.F.mergeArrays(_.weights, loadedFonts[_.family].weights);

				// NOT LOADED YET, LETS LOAD IT
				// callBackDone = true;
				var subset = '',
					weight = '';

				//COLLECT FONT WEIGHTS
				loadedFonts[_.family].weights = checkWeightsInVariants(loadedFonts[_.family].weights,gFontType.variants);
				if (loadedFonts[_.family].weights.length===0)
					for(var mkey in gFontType.variants){
						if(!gFontType.variants.hasOwnProperty(mkey)) continue;
						if(mkey > 0) weight += ','; else weight=":";
						weight += gFontType.variants[mkey];
						loadedFonts[_.family].weights.push(gFontType.variants[mkey]);
					}
				else
					for (var w in loadedFonts[_.family].weights) {
						if(!loadedFonts[_.family].weights.hasOwnProperty(w)) continue;
						if (w>0) weight += ','; else weight=":";
						weight += loadedFonts[_.family].weights[w];
						if (_.italic && weight.indexOf("italic")==-1 && jQuery.inArray(loadedFonts[_.family].weights[w]+"italic",loadedFonts[_.family].weights)==-1) weight += ','+loadedFonts[_.family].weights[w]+"italic";
					}

				//COLLECT SUBTYPES
				if(typeof(gFontType.subsets) !== 'undefined'){
					for(var mkey in gFontType.subsets){
						if(!gFontType.subsets.hasOwnProperty(mkey)) continue;
						if(mkey > 0) subset += ','; else subset=":";
						subset += gFontType.subsets[mkey];
						_.subsets.push(gFontType.subsets[mkey]);
					}
				}
				return(_.font+weight+subset);
			}
		}
	};

	function fontLoadEnded(familyName,fvd,a) {
		/* var family = familyName.replace(/\ /g,'_'),*/

		var italic = fvd.indexOf("i")>=0,
			weight = parseInt(fvd.replace(/[^0-9]/, ''),0)*100;

		for (var l in RVS.L) if(RVS.L.hasOwnProperty(l) && ((RVS.L[l].type==="text" || RVS.L[l].type==="button") && RVS.L[l].idle.fontFamily==familyName) && ((!italic && !RVS.L[l].idle.fontStyle) || (italic && RVS.L[l].idle.fontStyle)) && RVS.L[l].idle.fontWeight[RVS.screen].v==weight) RVS.F.drawHTMLLayer({uid:RVS.L[l].uid});

		setTimeout(function() {
			for (var l in RVS.L) if(RVS.L.hasOwnProperty(l) && ((RVS.L[l].type==="text" || RVS.L[l].type==="button") && RVS.L[l].idle.fontFamily==familyName) && ((!italic && !RVS.L[l].idle.fontStyle) || (italic && RVS.L[l].idle.fontStyle)) && RVS.L[l].idle.fontWeight[RVS.screen].v==weight) RVS.F.updateHTMLLayerPosition({uid:RVS.L[l].uid});
		},150);


	}

	RVS.F.do_google_font_load = function(families,options,evt) {
		options = options === undefined ? {silent:false} : options;
		if (families!==null && families.length>0) {
			var fonts = "",
				fam = 0;
			for (var i in families) {
				if(!families.hasOwnProperty(i)) continue;
				if (i>0) fonts+=" ";
				fonts += families[i];
				fam++;
			}
			fam = fam===1 ? fam + " Font" : fam + " Fonts";
			tpWebFont.load({
				google:{ families:families},
				//fontinactive:fontLoadEnded,
				fontactive:fontLoadEnded,
				//fontloading:fontLoadEnded,
				loading:function() {
					if (options.silent!==true) RVS.F.showWaitAMinute({fadeIn:500,text:"Please Wait<br><span style='display:block;font-size:30px;line-height:35px'>Loading "+fam+"</span>"});
				},
				active:function() {
					if (options.silent!==true) setTimeout(function() {
						RVS.F.showWaitAMinute({fadeOut:500});
						if (evt!==undefined) setTimeout(function() {RVS.DOC.trigger(evt);},500);
					},50);
				},
				inactive:function(e) {
					if (options.silent!==true) setTimeout(function() {
						RVS.F.showWaitAMinute({fadeOut:500});
						if (evt!==undefined) setTimeout(function() {RVS.DOC.trigger(evt);},500);
					},50);
				}
			});
		}
	};


	RVS.F.importSelectedLayers = function(_IL) {

		_IL = _IL === undefined ? RVS.LIB.OBJ.items.moduleslides[RVS.LIB.OBJ.selectedSlideId].layers : _IL;
		var cache=false;
		//CACHE THE COPY PASTE STRUCTURE
		if (window.copyPasteLayers!==undefined && window.copyPasteLayers.layers!==undefined) {
			cache = true;
			window.backupCopyPaste = RVS.F.safeExtend(true,{},window.copyPasteLayers.layers);
		}
		window.copyPasteLayers = {amount:0, layers:{}};

		// GO THROUGH THE ROWS, COLUMNS ETC. AND EXPORT THINGS WHERE THEY NEED TO BE EXPORTED
		for (let i in RVS.LIB.OBJ.import.toImport) {
			if(!RVS.LIB.OBJ.import.toImport.hasOwnProperty(i)) continue;
			window.copyPasteLayers.layers[_IL[RVS.LIB.OBJ.import.toImport[i]].uid] = RVS.F.safeExtend(true,RVS.F.addLayerObj(_IL[RVS.LIB.OBJ.import.toImport[i]].type,undefined,true),_IL[RVS.LIB.OBJ.import.toImport[i]]);
		}
		if (RVS.LIB.OBJ.depth==="grouplayers") {
			delete window.copyPasteLayers.layers.bottom;
			delete window.copyPasteLayers.layers.middle;
			delete window.copyPasteLayers.layers.top;
		}

		//Prepare Action Links
		for (var i in window.copyPasteLayers.layers) {
			if(!window.copyPasteLayers.layers.hasOwnProperty(i)) continue;
			for (var j in window.copyPasteLayers.layers[i].actions.action) {
				if(!window.copyPasteLayers.layers[i].actions.action.hasOwnProperty(j)) continue;
				if (window.copyPasteLayers.layers[i].actions.action[j]!==undefined && window.copyPasteLayers.layers[i].actions.action[j].layer_target)
					window.copyPasteLayers.layers[i].actions.action[j].beforemigration_layer_target = window.copyPasteLayers.layers[i].actions.action[j].layer_target;
			}
		}
		var duplicateLayers = [],
			duplicateLayersID = [],
			newLayerIDs = [],
			translate = {},
			rows = [],
			rowid;

		for (var sli in window.copyPasteLayers.layers) {
			if(!window.copyPasteLayers.layers.hasOwnProperty(sli)) continue;
			var uid = window.copyPasteLayers.layers[sli].uid;
			duplicateLayers.push({type:window.copyPasteLayers.layers[uid].type,  copyPaste:"paste", duplicateId:uid, ignoreBackupGroup:true, ignoreLayerList:true , ignoreOrderHTMLLayers:true, prefix:RVS_LANG.imported});
			duplicateLayersID.push(uid);
			if (window.copyPasteLayers.layers[uid].type==="column") {
				rowid = window.copyPasteLayers.layers[uid].type==="row" ? uid : window.copyPasteLayers.layers[uid].group.puid;
				if (jQuery.inArray(rowid,rows)===-1) rows.push(rowid);
			}
		}

		RVS.F.openBackupGroup({id:"addLayer",txt:"Duplicate Layer(s)",icon:"layers",lastkey:"layer"});

		// CHECK MULTPILE DUPLICATES, LIKE COLUMN IN ROW WHICH ALREADY IN DUPLICATE MODE. (Parrents Check)
		for (var i in duplicateLayers) {
			if(!duplicateLayers.hasOwnProperty(i)) continue;
			var puid = window.copyPasteLayers.layers[duplicateLayersID[i]].group.puid;
			if (puid===-1 || (jQuery.inArray(parseInt(puid,0),duplicateLayersID)==-1)) {
				let id = RVS.F.addLayer(duplicateLayers[i]);
				translate[duplicateLayersID[i]] = id;
				newLayerIDs.push(id);
			}
		}



		//UPDATE ROWS (Extend, Remove Sizes)
		for (var i in rows)  {
			if(!rows.hasOwnProperty(i)) continue;
			if (RVS.L[rows[i]]!==undefined) RVS.F.fixColumnsInRows({layerid:rows[i]});
		}

		//FIX ACTIONS WITH NEW TRANSLATED ID'S
		for (var i in RVS.L) {
			if(!RVS.L.hasOwnProperty(i)) continue;
			if (RVS.L[i].actions)
				for (var j in RVS.L[i].actions.action) {
					if(!RVS.L[i].actions.action.hasOwnProperty(j)) continue;
					if (RVS.L[i].actions.action[j]!==undefined && RVS.L[i].actions.action[j].beforemigration_layer_target) {
						RVS.L[i].actions.action[j].layer_target = translate[RVS.L[i].actions.action[j].beforemigration_layer_target];
						delete RVS.L[i].actions.action[j].beforemigration_layer_target;
					}
				}
		}



		RVS.F.closeBackupGroup({id:"addLayer"});
		RVS.F.buildLayerLists({force:true, ignoreRebuildHTML:true});
		RVS.F.updateZIndexTable();
		RVS.F.reOrderHTMLLayers();
		for (var i in newLayerIDs) {
			if(!newLayerIDs.hasOwnProperty(i)) continue;
			RVS.F.selectLayers({id:newLayerIDs[i],overwrite:false, action:"add", ignoreUpdate:true});
		}
		RVS.F.selectedLayersVisualUpdate();
		RVS.F.updateSelectedHtmlLayers();
		RVS.F.checkShowHideLayers();
		RVS.F.checkLockedLayers();

		// RESTORE CACHE
		if (cache) window.copyPasteLayers.layers = RVS.F.safeExtend({},window.backupCopyPaste);

		setTimeout(function() {	RVS.F.showWaitAMinute({fadeOut:500});},100);

		// CLOSE IMPORT WINDOW
		if (RVS.LIB.OBJ.depth==="layers") {
			jQuery('#rb_modal_underlay').appendTo('#slider_settings');
			RVS.F.RSDialog.close();
		}
		RVS.F.closeObjectLibrary();
		//Load the Needed Fonts
		RVS.F.checkUsedFonts();
	}

/*************************************
    - 	INTERNAL FUNCTIONS -
***************************************/



	/*
	GET NEXT UNIQUE ID
	*/
	RVS.F.getUniqueid = function() {
		while (jQuery.inArray(RVS.S.uniqueId, RVS.S.uniqueIds)>=0) {
			RVS.S.uniqueId++;
		}
		return RVS.S.uniqueId;
	};

	/*
	GET NEXT ZINDEX IN SLIDE
	*/
	function getHighestZindex() {
		var z = 5;
		for (var li in RVS.L) {
			if(!RVS.L.hasOwnProperty(li)) continue;
			if (parseInt(RVS.L[li].position.zIndex,0)>=z)
				z = parseInt(RVS.L[li].position.zIndex,0)+1;
		}
		return z;
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
		if (v==="true" || v===true || v==="on")
			v=true;
		return v;
	}



	/*
	CREATE A DEFAULT FRAME OBJECT
	*/
	function defaultLoopFrame(o) {
		o =  o===undefined ? {} : o;
		o.frame_0 = o.frame_0===undefined ? {} : o.frame_0;
		o.frame_999 = o.frame_999===undefined ? {} : o.frame_999;
		var loop = {
			use:_d(o.use,false),
			ease:_d(o.ease,"none"),
			speed:_d(o.speed, 1000),
			originX:_d(o.originX,"50%"),
			originY:_d(o.originX,"50%"),
			radiusAngle:_d(o.radiusAngle,0),
			curviness:_d(o.curviness,2),
			curved:_d(o.curved,false),
			yoyo_move:_d(o.yoyo_move,false),
			yoyo_rotate:_d(o.yoyo_rotate,false),
			yoyo_scale:_d(o.yoyo_scale,false),
			yoyo_filter:_d(o.yoyo_filter,false),
			repeat:_d(o.repeat,"-1"),
			start:_d(o.start,300),
			autoRotate:_d(o.autoRotate,false),
			frame_0:{
				yr:_d(o.frame_0.yr,0),
				zr:_d(o.frame_0.zr,0),
				x:_d(o.frame_0.x,0),
				y:_d(o.frame_0.y,0),
				z:_d(o.frame_0.z,0),
				scaleX:_d(o.frame_0.scaleX,1),
				scaleY:_d(o.frame_0.scaleY,1),
				opacity:_d(o.frame_0.opacity,1),
				rotationX:_d(o.frame_0.rotationX,0),
				rotationY:_d(o.frame_0.rotationY,0),
				rotationZ:_d(o.frame_0.rotationZ,0),
				skewX:_d(o.frame_0.skewX,0),
				skewY:_d(o.frame_0.skewY,0),
				blur:0,
				brightness:100,
				grayscale:0
			},
			frame_999:{
				xr:_d(o.frame_999.xr,0),
				yr:_d(o.frame_999.yr,0),
				zr:_d(o.frame_999.zr,0),
				x:_d(o.frame_999.x,0),
				y:_d(o.frame_999.y,0),
				z:_d(o.frame_999.z,0),
				scaleX:_d(o.frame_999.scaleX,1),
				scaleY:_d(o.frame_999.scaleY,1),
				opacity:_d(o.frame_999.opacity,1),
				rotationX:_d(o.frame_999.rotationX,0),
				rotationY:_d(o.frame_999.rotationY,0),
				rotationZ:_d(o.frame_999.rotationZ,0),
				skewX:_d(o.frame_999.skewX,0),
				skewY:_d(o.frame_999.skewY,0),
				blur:0,
				brightness:100,
				grayscale:0
			}
		};
		return loop;
	}


	/*
	CREATE A DEFAULT FRAME OBJECT
	*/
	function defaultFrame(o,NR) {
		NRI = NR==="frame_0" ? 0 : NR==="frame_1" ? 1 : 2;
		o =  o===undefined ? {} : o;
		o.chars = o.chars===undefined ? {} : o.chars;
		o.words = o.words===undefined ? {} : o.words;
		o.lines = o.lines===undefined ? {} : o.lines;
		o.mask = o.mask===undefined ? {} : o.mask;
		o.color = o.color===undefined ? {} : o.color;
		o.bgcolor = o.bgcolor===undefined ? {} : o.bgcolor;

		var gs = _d(o.grayscale,[0,0,0][NRI]),
			bs = _d(o.brightness,[100,100,100][NRI]),
			blr = _d(o.blur,[0,0,0][NRI]),
			fuse = parseInt(gs,0)!==0 || parseInt(bs,0)!==100 || parseInt(blr,0)!==0 ? true : false,
			bgs = _d(o.bgrayscale,[0,0,0][NRI]),
			bbs = _d(o.bbrightness,[100,100,100][NRI]),
			bblr = _d(o.bblur,[0,0,0][NRI]),
			bgi = _d(o.binvert,[0,0,0][NRI]),
			bse = _d(o.bsepia,[0,0,0][NRI]),
			bfuse = parseInt(bgs,0)!==0 || parseInt(bbs,0)!==100 || parseInt(bblr,0)!==0  || parseInt(bgi,0)!==0  || parseInt(bse,0)!==0 ? true : false;

		var frame = {
			alias:_d(o.alias,RVS_LANG.onstage),
			filter:{use:fuse, grayscale:gs, brightness:bs, blur:blr},
			bfilter:{use:bfuse, grayscale:bgs, brightness:bbs, blur:bblr, sepia:bse, invert:bgi},
			transform:{
				x:RVS.F.cToResp({default:_d(o.x,[0,0,"inherit"][NRI])}),
				y:RVS.F.cToResp({default:_d(o.y,[0,0,"inherit"][NRI])}),
				z:_d(o.z,[0,0,"inherit"][NRI]),
				scaleX:_d(o.scaleX,[1,1,"inherit"][NRI]),
				scaleY:_d(o.scaleY,[1,1,"inherit"][NRI]),
				opacity:_d(o.opacity,[0,1,"inherit"][NRI]),
				rotationX:_d(o.rotationX,[0,0,"inherit"][NRI]),
				rotationY:_d(o.rotationY,[0,0,"inherit"][NRI]),
				rotationZ:_d(o.rotationZ,[0,0,"inherit"][NRI]),
				skewX:_d(o.skewX,[0,0,"inherit"][NRI]),
				skewY:_d(o.skewY,[0,0,"inherit"][NRI]),
				originX:_d(o.originX,["50%","50%","50%"][NRI]),
				originY:_d(o.originY,["50%","50%","50%"][NRI]),
				originZ:_d(o.originZ,["0","0","0"][NRI]),
				transformPerspective:_d(RVS.SLIDER.settings.general.perspective,["600px","600px","600px"][NRI]),
				clip:_d(o.clip,[100,100,"inherit"][NRI]),
				clipB:_d(o.clipB,[100,100,"inherit"][NRI])
			},
			reverseDirection:{
				x:_d(o.rx,false),
				y:_d(o.ry,false),
				rotationX:_d(o.rrotationX,false),
				rotationY:_d(o.rrotationY,false),
				rotationZ:_d(o.rrotationZ,false),
				skewX:_d(o.rskewX,false),
				skewY:_d(o.rskewY,false),
				maskX:_d(o.rmaskX,false),
				maskY:_d(o.rmaskY,false),

				charsX:_d(o.crx,false),
				charsY:_d(o.cry,false),
				charsDirection:_d(o.crsd,false),

				wordsX:_d(o.wrx,false),
				wordsY:_d(o.wry,false),
				wordsDirection:_d(o.wrsd,false),

				linesX:_d(o.lrx,false),
				linesY:_d(o.lry,false),
				linesDirection:_d(o.lrsd,false)
			},
			mask:{
				use:_d(o.mask.use,false),
				x:RVS.F.cToResp({default:_d(o.mask.x,[0,0,"inherit"][NRI])}),
				y:RVS.F.cToResp({default:_d(o.mask.y,[0,0,"inherit"][NRI])})
			},
			color:{
				color:_d(o.color.color,"#ffffff"),
				use:_d(o.color.use,false)
			},
			bgcolor:{
				backgroundColor:_d(o.bgcolor.backgroundColor,"transparent"),
				use:_d(o.bgcolor.use,false),
			},
			timeline:{
				//delay:_d(o.delay,1000),
				actionTriggered:_d(o.actionTriggered,false),
				ease:_d(o.ease,"power3.inOut"),
				speed:_d(o.speed,300),
				start:_d(o.start,0),
				startRelative:_d(o.startRelative,0),
				endWithSlide:_d(o.endWithSlide,false)
			},

			chars:{
				ease:_d(o.chars.ease,"inherit"),
				use:_d(o.chars.use,false),
				direction: _d(o.chars.direction,"forward"),
				delay: _d(o.chars.delay,5),
				x: RVS.F.cToResp({default:_d(o.chars.x,[0,0,"inherit"][NRI])}),
				y: RVS.F.cToResp({default:_d(o.chars.y,[0,0,"inherit"][NRI])}),
				z: _d(o.chars.z,[0,0,"inherit"][NRI]),
				scaleX: _d(o.chars.scaleX,[1,1,"inherit"][NRI]),
				scaleY: _d(o.chars.scaleY,[1,1,"inherit"][NRI]),
				opacity: _d(o.chars.opacity,"inherit"),
				rotationX: _d(o.chars.rotationX,[0,0,"inherit"][NRI]),
				rotationY: _d(o.chars.rotationY,[0,0,"inherit"][NRI]),
				rotationZ: _d(o.chars.rotationZ,[0,0,"inherit"][NRI]),
				skewX: _d(o.chars.skewX,[0,0,"inherit"][NRI]),
				skewY: _d(o.chars.skewY,[0,0,"inherit"][NRI]),
				originX:_d(o.chars.originX,["50%","50%","inherit"][NRI]),
				originY:_d(o.chars.originY,["50%","50%","inherit"][NRI]),
				originZ:_d(o.chars.originZ,["0","0","inherit"][NRI]),
				fuse:_d(o.chars.fuse,false),
				blur:_d(o.chars.blur,[0,0,0][NRI]),
				grayscale:_d(o.chars.grayscale,[0,0,0][NRI]),
				brightness:_d(o.chars.brightness,[100,100,100][NRI]),
			},
			words:{
				ease:_d(o.words.ease,"inherit"),
				use:_d(o.words.use,false),
				direction: _d(o.words.direction,"forward"),
				delay: _d(o.words.delay,5),
				x: RVS.F.cToResp({default:_d(o.words.x,[0,0,"inherit"][NRI])}),
				y: RVS.F.cToResp({default:_d(o.words.y,[0,0,"inherit"][NRI])}),
				z: _d(o.words.z,[0,0,"inherit"][NRI]),
				scaleX: _d(o.words.scaleX,[1,1,"inherit"][NRI]),
				scaleY: _d(o.words.scaleY,[1,1,"inherit"][NRI]),
				opacity: _d(o.words.opacity,"inherit"),
				rotationX: _d(o.words.rotationX,[0,0,"inherit"][NRI]),
				rotationY: _d(o.words.rotationY,[0,0,"inherit"][NRI]),
				rotationZ: _d(o.words.rotationZ,[0,0,"inherit"][NRI]),
				skewX: _d(o.words.skewX,[0,0,"inherit"][NRI]),
				skewY: _d(o.words.skewY,[0,0,"inherit"][NRI]),
				originX:_d(o.words.originX,["50%","50%","inherit"][NRI]),
				originY:_d(o.words.originY,["50%","50%","inherit"][NRI]),
				originZ:_d(o.words.originY,["0","0","inherit"][NRI]),
				fuse:_d(o.words.fuse,false),
				blur:_d(o.words.blur,[0,0,0][NRI]),
				grayscale:_d(o.words.grayscale,[0,0,0][NRI]),
				brightness:_d(o.words.brightness,[100,100,100][NRI]),
			},
			lines:{
				ease:_d(o.lines.ease,"inherit"),
				use:_d(o.lines.use,false),
				direction: _d(o.lines.direction,"forward"),
				delay: _d(o.lines.delay,5),
				x: RVS.F.cToResp({default:_d(o.lines.x,[0,0,"inherit"][NRI])}),
				y: RVS.F.cToResp({default:_d(o.lines.y,[0,0,"inherit"][NRI])}),
				z: _d(o.lines.z,[0,0,"inherit"][NRI]),
				scaleX: _d(o.lines.scaleX,[1,1,"inherit"][NRI]),
				scaleY: _d(o.lines.scaleY,[1,1,"inherit"][NRI]),
				opacity: _d(o.lines.opacity,"inherit"),
				rotationX: _d(o.lines.rotationX,[0,0,"inherit"][NRI]),
				rotationY: _d(o.lines.rotationY,[0,0,"inherit"][NRI]),
				rotationZ: _d(o.lines.rotationZ,[0,0,"inherit"][NRI]),
				skewX: _d(o.lines.skewX,[0,0,"inherit"][NRI]),
				skewY: _d(o.lines.skewY,[0,0,"inherit"][NRI]),
				originX:_d(o.lines.originX,["50%","50%","inherit"][NRI]),
				originY:_d(o.lines.originY,["50%","50%","inherit"][NRI]),
				originZ:_d(o.lines.originY,["0","0","inherit"][NRI]),
				fuse:_d(o.lines.fuse,false),
				blur:_d(o.lines.blur,[0,0,0][NRI]),
				grayscale:_d(o.lines.grayscale,[0,0,0][NRI]),
				brightness:_d(o.lines.brightness,[100,100,100][NRI]),
			},

			sfx:{
				effect:_d(o.effect,""),
				color:_d(o.sfxcolor,"#ffffff")
			}

		};

		frame.sfx.effect = frame.sfx.effect==="blockfrombottom" ? "blocktotop" : frame.sfx.effect==="blockfromtop" ? "blocktobottom" : frame.sfx.effect==="blockfromleft" ? "blocktoright" : frame.sfx.effect==="blockfromright" ? "blocktoleft" : "none";

		for (var i in RVS.JHOOKS.defaultFrame) {
			if(!RVS.JHOOKS.defaultFrame.hasOwnProperty(i)) continue;
			frame = RVS.JHOOKS.defaultFrame[i](frame);
		}

		return frame;
	}

	function fourLevelAnims(_) {
		if (typeof _.transform.x!=="object") _.transform.x = RVS.F.cToResp({default:_d(_.transform.x,0)});
		if (typeof _.transform.y!=="object")_.transform.y = RVS.F.cToResp({default:_d(_.transform.y,0)});

		if (typeof _.mask.x!=="object")_.mask.x = RVS.F.cToResp({default:_d(_.mask.x,0)});
		if (typeof _.mask.y!=="object")_.mask.y = RVS.F.cToResp({default:_d(_.mask.y,0)});

		if (typeof _.chars.x!=="object")_.chars.x = RVS.F.cToResp({default:_d(_.chars.x,"inherit")});
		if (typeof _.chars.y!=="object")_.chars.y = RVS.F.cToResp({default:_d(_.chars.y,"inherit")});


		if (typeof _.words.x!=="object")_.words.x = RVS.F.cToResp({default:_d(_.words.x,"inherit")});
		if (typeof _.words.y!=="object")_.words.y = RVS.F.cToResp({default:_d(_.words.y,"inherit")});

		if (typeof _.lines.x!=="object")_.lines.x = RVS.F.cToResp({default:_d(_.lines.x,"inherit")});
		if (typeof _.lines.y!=="object") _.lines.y = RVS.F.cToResp({default:_d(_.lines.y,"inherit")});
		return _;
	}

	/*
	BUILD AND EXTEND DEFAULT LAYERS
	*/
	RVS.F.addLayerObj = function(type,obj,compare,updateDefaults,ignoreUID) {

		if (RVS.ENV.img_ph_url===undefined) RVS.ENV.video_ph_url = RVS.ENV.img_ph_url = RVS.ENV.plugin_url+"admin/assets/images/transparent_placeholder.png";

		if (typeof type==="object") {
			obj = type;
			type = obj.type;
		}


		//TOP MIDDLE, BOTTOM ZONES SAVED AS THEY ARE
		if (obj!==undefined && obj.uid!==undefined && jQuery.inArray(obj.uid,["top","bottom","middle","zone"])>=0) return obj;
		if (jQuery.inArray(type,["top","bottom","middle","zone"])>=0) return obj;


		obj = obj === undefined ? {} : obj;

		if (obj.type==="boxshadow" || obj.type==="textshadow") return;

		var newLayer = {};
		newLayer.addOns = obj.addOns || {};
		newLayer.type = _d(obj.type,type);  	//text, image, video, audio, svg, shape
		newLayer.subtype = _d(obj.subtype,"");
		newLayer.linebreak = _d(obj.linebreak,false);
		newLayer.text = type==="text" || type==="button" ? _d(obj.text,"New Layer") : "";
		newLayer.placeholder = type==="text" || type==="button" ? _d(obj.placeholder,"") : "";
		newLayer.alias = RVS.F.firstCharUppercase(_d(obj.alias,"New Layer"));
		if (ignoreUID!==true && !compare) newLayer.uid = _d(obj.uid,RVS.F.getUniqueid());
		newLayer.version = RVS.ENV.revision;
		/*newLayer.version = _d(obj.version,"6.0.0");
		newLayer.version = newLayer.version<"6.0.0" ? "6.0.0" : newLayer.version;*/

		newLayer.htmltag = _d(obj.htmltag,"div");
		newLayer.customCSS = _d(obj.customCSS,"");
		newLayer.customHoverCSS = _d(obj.customHoverCSS,"");

		switch (newLayer.type) {
			case "text":
			case "button":
			case "image":
				newLayer.media = _d(obj.media,{
					imageUrl:RVS.ENV.img_ph_url,
					imageId:"",
					imageFromStream:false
				});
				newLayer.media.loaded = false;
			break;
			case "audio":
			case "video":
				newLayer.media = _d(obj.media,{
					mediaType:newLayer.type==="audio" ? "audio" : !compare ? "html5" : "",
					audioUrl:"",
					audioTitle:"",
					posterUrl:RVS.ENV.video_ph_url,
					posterId:"",
					fitCover: true,
					posterFromStream:false,
					thumbs:{
						veryBig:{width:640,height:480,url:RVS.ENV.video_ph_url},
						big:{width:640,height:480,url:RVS.ENV.video_ph_url},
						large:{width:640,height:360,url:RVS.ENV.video_ph_url},
						medium:{width:320,height:240,url:RVS.ENV.video_ph_url},
						small:{width:200,height:150,url:RVS.ENV.video_ph_url},
					},
					nointeraction:false,
					descSmall:"",
					description:"",
					link:"",
					mp4Url:"",
					ogvUrl:"",
					webmUrl:"",
					allowFullscreen:true,
					args:"",
					author:"",
					autoPlay:"true",
					//autoPlayFirstTime:false,
					controls:false,
					cover:false,
					disableOnMobile:false,
					dotted:"none",
					dottedSize:1,
					dottedColorA:"transparent",
					dottedColorB:"#000000",
					startAt:"00:00",
					endAt:"00:00",
					forceRewind:true,
					fullWidth:false,
					id:"",
					videoFromStream:false,
					largeControls:true,
					leaveOnPause:true,
					mute:true,
					nextSlideAtEnd:true,
					preload:"auto",
					preloadAudio:"metadata",
					preloadWait:"0",
					ratio:"16:9",
					posterOnPause:false,
					posterOnMobile:false,
					stopAllVideo:true,
					playInline:true,
					hideAudio:true,
					speed:1,
					loop:true,
					pausetimer:false,
					volume:"100"
				});

				if (newLayer!==undefined && newLayer.media!==undefined) {
					if (newLayer.media.dotted.indexOf("white")>0) newLayer.media.dottedColorB = "rgba(255,255,255,255)";
					if (newLayer.media.dotted.indexOf("twoxtwo")>=0) newLayer.media.dotted = "1";
					else if (newLayer.media.dotted.indexOf("threexthree")>=0) newLayer.media.dotted = "2";
				}

				if (newLayer.media!==undefined && newLayer.media.type==="video" && newLayer.media.allowFullscreen===undefined && compare!==true) newLayer.media.allowFullscreen=true;

				// OLD FALLBACK
				if (newLayer.media!==undefined && newLayer.media.mediaType==="streamyoutube") {
					newLayer.media.mediaType = "youtube";
					newLayer.media.videoFromStream = true;
				}

				if (newLayer.media!==undefined && newLayer.media.mediaType==="streamvimeo") {
					newLayer.media.mediaType = "vimeo";
					newLayer.media.videoFromStream = true;
				}

				newLayer.media.mediaType = !compare && (newLayer.media.mediaType==='' || newLayer.media.mediaType===undefined) ? "html5" : compare ? "alwayswrite" : newLayer.media.mediaType;

				// CHANGES, TO SET LOOP AND PAUSE TIMER INDEPENDENT, HAVING 4 CASES
				newLayer.media.loop = newLayer.media.loop===true || (obj!==undefined && obj.media!==undefined && (obj.media.loop==="loopandnoslidestop" || obj.media.loop==="loop" || obj.media.loop===true || obj.media.loop==="true")) ? true : false;
				newLayer.media.pausetimer = obj!==undefined && obj.media!==undefined && (obj.media.pausetimer===true || (obj.media.pausetimer!==false && obj.media.loop !== "loopandnoslidestop"))  ? true : false;
				if (newLayer.media.loop===true && newLayer.media.nextSlideAtEnd===true) newLayer.media.loop = false;

				if (compare && newLayer.media!==undefined) {
					newLayer.media.loop=true;
					newLayer.media.pausetimer = 0;
				}

				if (compare) newLayer.media.loop=undefined;

			break;
			case "svg":
			case "object":
				newLayer.svg = _d(obj.svg, {
					source:"",
					renderedData:""
				});
			break;
		}


		if (newLayer.media!==undefined && newLayer.media.lastLibrary!==undefined) {
			newLayer.media.imageLib = newLayer.media.lastLibrary;
			delete newLayer.medialastLibrary;
		}


		newLayer.toggle = _d(obj.toggle,{
			set:false,
			text:"",
			inverse:false,
			useHover:false

		});

		var defwidth = compare===true || updateDefaults===true ? "auto" : newLayer.type==="audio" ? "54" : newLayer.type!=="image" && newLayer.type!=="shape" && newLayer.type!=="video" && newLayer.type!=="group" ? "auto" : newLayer.type!=="video" ? "300px" : "320px",
			defheight = compare===true || updateDefaults===true ? "auto" : newLayer.type==="audio" ? "54" : newLayer.type!=="image" && newLayer.type!=="shape" && newLayer.type!=="video" && newLayer.type!=="group" ? "auto" : "180px",
			defproportion = compare===true ? "auto" : newLayer.type==="svg" || newLayer.type==="image" || newLayer.type==="video" ?  true : false,
			defaspectrat = compare===true || updateDefaults===true  ? "auto" : newLayer.type!=="image" && newLayer.type!=="shape" && newLayer.type!=="video" && newLayer.type!=="group" ? "none" : newLayer.type!=="video" ? 300/180 : 320/180;


		newLayer.size = _d(obj.size,{
			width: RVS.F.cToResp({default:defwidth}),
			height:RVS.F.cToResp({default:defheight}),
			maxWidth:RVS.F.cToResp({default:"none"}),
			maxHeight:RVS.F.cToResp({default:"none"}),
			minWidth:RVS.F.cToResp({default:"none"}),
			minHeight:RVS.F.cToResp({default:"none"}),
			originalWidth:0,
			originalHeight:0,
			aspectRatio:RVS.F.cToResp({default:defaspectrat}),
			covermode:"custom",
			scaleProportional:defproportion
		});

		if (newLayer.size.height.d!==undefined && newLayer.size.height.d.v===null) newLayer.size.height.d.v = "auto";

		if (newLayer.type==="svg" ) newLayer.size.scaleProportional = true;

		newLayer.size.originalWidth = newLayer.size.originalWidth===0 || newLayer.size.originalWidth===undefined ? newLayer.size.width.d.v : newLayer.size.originalWidth;
		newLayer.size.originalHeight = newLayer.size.originalHeight===0 || newLayer.size.originalHeight===undefined ? newLayer.size.height.d.v : newLayer.size.originalHeight;




		newLayer.position = _d(obj.position,{
			x: !compare && !updateDefaults ? RVS.F.cToResp({default:(50+RVS.S.rb_ScrollX),unit:"px"}) : RVS.F.cToResp({default:0,unit:"px"}),
			y: !compare && !updateDefaults ? RVS.F.cToResp({default:(50+RVS.S.rb_ScrollY),unit:"px"}) : RVS.F.cToResp({default:(0),unit:"px"}),
			horizontal:RVS.F.cToResp({default:"left"}),
			vertical:RVS.F.cToResp({default:"top"}),
			position:"absolute",
			staticZ:"default"
		});

		// New Static Layers z Index
		if (newLayer.position!==undefined && !compare) newLayer.position.staticZ=newLayer.position.staticZ===undefined ? "default" : newLayer.position.staticZ;

		// zIndex Settings (Also Order in the Layer List !!)


		if (obj.position!==undefined && obj.position.zIndex!==undefined)
			newLayer.position.zIndex = obj.position.zIndex;
		else
			newLayer.position.zIndex = getHighestZindex();

		if (compare) newLayer.position.zIndex="nA";

		newLayer.attributes = _d(obj.attributes,{
			alt:"",
			altOption:"media_library",
			id:"",
			classes:"",
			rel:"",
			tabIndex:0,
			title:"",
			wrapperClasses:"",
			wrapperId:""
		});
		newLayer.behavior = _d(obj.behavior,{
			autoResponsive:(newLayer.type==="row" || newLayer.type==="column" ? false : !compare && !updateDefaults ? RVS.SLIDER.settings.def.autoResponsive : true),
			intelligentInherit:(newLayer.type==="row" || newLayer.type==="column" ? false : !compare && !updateDefaults ? RVS.SLIDER.settings.def.intelligentInherit : true),
			responsiveChilds:!compare && !updateDefaults ? RVS.SLIDER.settings.def.responsiveChilds : true,
			baseAlign:"grid",
			responsiveOffset:!compare && !updateDefaults ? RVS.SLIDER.settings.def.responsiveOffset : true,
			lazyLoad:"auto",
			imageSourceType:"full",
			streamSourceType:"full"
		});

		newLayer.group = _d(obj.group,{
			puid:-1,
			groupOrder: !compare ? newLayer.position.zIndex : 99999,
			columnbreakat:"tablet",
			columnSize:"1/3"

		});




		if (newLayer.type==="row" && newLayer.group.puid===-1)
			newLayer.group.puid = "top";
		_opacity = ((newLayer.type==="group" || newLayer.type==="column" || newLayer.type==="row") && (!compare && !updateDefaults)) ?  1 : 0;


		newLayer.timeline = _d(obj.timeline,{
			scrollBased:"default",
			forcePrepare:false,
			scrollBasedOffset:0,
			filtersOnMask:false,
			frameToIdle:"frame_1",
			frames:{

				frame_0:defaultFrame({
						alias:RVS_LANG.enterstage,
						opacity:_opacity
					},"frame_0"),
				frame_1:defaultFrame({
						alias:RVS_LANG.onstage,
						opacity:1,
						chars:{	x: 0,y: 0,z: 0,opacity: 1,rotationZ: 0,rotationX: 0,rotationY: 0,scaleX: 1,scaleY: 1,skewX: 0,skewY: 0, blur:0, grayscale:0, brightness:100, fuse:false},
						words:{	x: 0,y: 0,z: 0,opacity: 1,rotationZ: 0,rotationX: 0,rotationY: 0,scaleX: 1,scaleY: 1,skewX: 0,skewY: 0, blur:0, grayscale:0, brightness:100, fuse:false},
						lines:{	x: 0,y: 0,z: 0,opacity: 1,rotationZ: 0,rotationX: 0,rotationY: 0,scaleX: 1,scaleY: 1,skewX: 0,skewY: 0, blur:0, grayscale:0, brightness:100, fuse:false},
					},"frame_1"),
				frame_999:defaultFrame({endWithSlide:true,alias:RVS_LANG.leavestage,opacity:(!compare && !updateDefaults ? 0 : "inherit")},"frame_999")
			},
			clipPath:{
				use:false,
				type:"rectangle",
				origin:"l"
			},
			static:{
				start:1,
				end:"last"
			},
			loop:defaultLoopFrame(),
			tloop:{
				use:false,
				from:"",
				to:"",
				repeat:-1,
				keep:true,
				children:true
			}
		});


		newLayer.timeline.tloop = newLayer.timeline.tloop===undefined ? { use:false,from:"",to:"",repeat:-1} : newLayer.timeline.tloop;

		newLayer.timeline.scrollBased = newLayer.timeline.scrollBased===undefined ? "default" : newLayer.timeline.scrollBased;
		newLayer.timeline.scrollBasedOffset = newLayer.timeline.scrollBasedOffset===undefined ? 0 : newLayer.timeline.scrollBasedOffset;
		newLayer.timeline.frames.frame_0.alias = newLayer.timeline.frames.frame_0.alias===undefined ? RVS_LANG.enterstage : newLayer.timeline.frames.frame_0.alias;
		newLayer.timeline.frames.frame_1.alias = newLayer.timeline.frames.frame_1.alias===undefined ? RVS_LANG.onstage : newLayer.timeline.frames.frame_1.alias;
		newLayer.timeline.frames.frame_999.alias = newLayer.timeline.frames.frame_999.alias===undefined ? RVS_LANG.leavestage : newLayer.timeline.frames.frame_999.alias;

		// Update of Values if something went wrong in Multiple Levels of Animations
		for (var i in newLayer.timeline.frames) {
			if(!newLayer.timeline.frames.hasOwnProperty(i)) continue;
			newLayer.timeline.frames[i] = fourLevelAnims(newLayer.timeline.frames[i]);
			if (newLayer.timeline.frames[i].timeline.start<0) newLayer.timeline.frames[i].timeline.start = 0;
			if (newLayer.timeline.frames[i].timeline.startRelative<0) newLayer.timeline.frames[i].timeline.startRelative = 0;
		}



		newLayer.effects = _d(obj.effects,{
			parallax:"-",
			pxmask:false,
			attachToBg:false,
			effect:"default"
		});


	/**/

		newLayer.idle = _d(obj.idle,{
			style:"",
			color:RVS.F.cToResp({default:"#ffffff"}),
			margin:RVS.F.cToResp({default:[0,0,0,0]}),
			marginLock:false,
			paddingLock:false,
			borderWidthLock:false,
			borderRadiusLock:false,
			padding:RVS.F.cToResp({default:(newLayer.type==="column"? [10,10,10,10] : newLayer.type==="row" ? [10,10,10,10] : [0,0,0,0])}),
			autolinebreak:true,
			float:	RVS.F.cToResp({default:"none"}),
			clear:	RVS.F.cToResp({default:"none"}),
			display:"block",
			fontFamily:"Roboto",
			fontStyle:false,
			fontSize:RVS.F.cToResp({default:"20"}),
			fontWeight:RVS.F.cToResp({default:"400"}),
			letterSpacing:RVS.F.cToResp({default:"0"}),
			lineHeight:RVS.F.cToResp({default:"25"}),
			overflow:"visible",
			textAlign:RVS.F.cToResp({default:"left"}),
			verticalAlign:"top",
			cursor:"auto",
			backgroundColor: (newLayer.type==="shape" && !compare && !updateDefaults ? "rgba(0,0,0,0.5)" : "transparent"),
			backgroundPosition:"center center",
			backgroundRepeat:"no-repeat",
			backgroundSize:"cover",
			backgroundSizePerc:"100",
			backgroundSizePix:"100",
			backgroundImage:"",
			backgroundImageId:"",
			bgFromStream:false,
			borderColor:"transparent",
			borderRadius:{v:[0,0,0,0],u:"%"},
			borderStyle:RVS.F.cToResp({default:"none"}),
			borderWidth:[0,0,0,0],
			rotationX:0,
			rotationY:0,
			rotationZ:0,
			opacity:1,
			textDecoration:"none",
			textTransform:"none",

			boxShadow:{
						inuse:false,
						container:"content",
						hoffset:RVS.F.cToResp({default:0,val:0}),
						voffset:RVS.F.cToResp({default:0,val:0}),
						blur:RVS.F.cToResp({default:0,val:0}),
						spread:RVS.F.cToResp({default:0,val:0}),
						color:'rgba(0,0,0,0)'
					},
			textShadow:{
					inuse:false,
					hoffset:RVS.F.cToResp({default:0,val:0}),
					voffset:RVS.F.cToResp({default:0,val:0}),
					blur:RVS.F.cToResp({default:0,val:0}),
					color:'rgba(0,0,0,0.25)'
				},
			textStroke:{
					inuse:false,
					width:RVS.F.cToResp({default:0,val:1}),
					color:'rgba(0,0,0,0.25)'
				},
			filter:{blendMode:"normal",showInEditor:true},
			spikeUse:false,
			spikeLeft:"none",
			spikeLeftWidth:10,
			spikeRight:"none",
			spikeRightWidth:10,
			cornerLeft:"none",
			cornerRight:"none",
			selectable:"default",
			whiteSpace:RVS.F.cToResp({default:(newLayer.type==="row" || newLayer.type==="column" ? "normal" : compare!==true && updateDefaults!=true ? "full" : "nowrap")}),
			svg:{
				originalColor:false,
				color:RVS.F.cToResp({default:"#ffffff"}),
				strokeColor:"transparent",
				strokeDashArray:0,
				strokeDashOffset:0,
				strokeWidth:0
			}
		});

		if (typeof newLayer.idle.borderStyle!=="object") newLayer.idle.borderStyle = RVS.F.cToResp({default:newLayer.idle.borderStyle});

		// Update TextStroke to Responsive Variable
		if (newLayer.idle.textStroke!==undefined && (typeof newLayer.idle.textStroke.width==="string" || typeof newLayer.idle.textStroke.width==="number"))
			 newLayer.idle.textStroke.width =  RVS.F.cToResp({default:0,val:newLayer.idle.textStroke.width});


		//GOOGLE FONT
		newLayer.idle.fontFamily = newLayer.idle.fontFamily===undefined || newLayer.idle.fontFamily===""  || newLayer.idle.fontFamily.toLowerCase() !== newLayer.idle.fontFamily ? RVS.F.compareGoogleFontName(newLayer.idle.fontFamily,true) : RVS.F.compareGoogleFontName(newLayer.idle.fontFamily);
		newLayer.idle.fontFamily = newLayer.idle.fontFamily===undefined ? "Roboto" : newLayer.idle.fontFamily;
		newLayer.idle.fontStyle = newLayer.idle.fontStyle==="normal" || newLayer.idle.fontStyle===false ? false : true;


		//FIX for Old Datas ! In case Update Rutine fails
		//
		for (var i in RVS.V.sizes) if (RVS.V.sizes.hasOwnProperty(i)) for (var j=0;j<4;j++) {
			newLayer.idle.margin[RVS.V.sizes[i]].v[j] = parseInt(newLayer.idle.margin[RVS.V.sizes[i]].v[j],0) || 0;
			newLayer.idle.padding[RVS.V.sizes[i]].v[j] = parseInt(newLayer.idle.padding[RVS.V.sizes[i]].v[j],0) || 0;
		}


		newLayer.hover = _d(obj.hover,{
			usehover:false,
			usehovermask:false,
			color:"#ffffff",
			opacity:1,
			backgroundColor:"transparent",
			gradientStyle:"fading",
			borderColor:"transparent",
			borderRadius:{v:[0,0,0,0],u:"%"},
			borderStyle:"none",
			borderWidth:[0,0,0,0],
			transformPerspective:RVS.SLIDER.settings.general.perspective,
			originX:"50%",
			originY:"50%",
			originZ:"50%",
			rotationZ:0,
			rotationX:0,
			rotationY:0,
			scaleX:1,
			scaleY:1,
			skewX:0,
			skewY:0,
			textDecoration:"none",
			x:0,
			y:0,
			z:0,
			speed:300,
			ease:"power3.inOut",
			zIndex:"auto",
			pointerEvents:"auto",
			filter:{grayscale:0, brightness:100, blur:0},
			svg:{
				color:"#ffffff",
				strokeColor:"transparent",
				strokeDashArray:0,
				strokeDashOffset:0,
				strokeWidth:0
			}
		});
		newLayer.actions = _d(obj.actions,{
			triggerMemory:"reset",
			action:[]
		});
		newLayer.visibility = _d(obj.visibility,{
			visible:true,
			locked:false,
			d:true,
			m:true,
			n:true,
			t:true,
			hideunder:false,
			onlyOnSlideHover:false,
			publish:true,
			alwaysOnCarousel:false
		});
		newLayer.runtime = _d(obj.runtime,{
			internalClass:"",
			isDemo:false,
			unavailable:false
		});

		if (ignoreUID!==true) RVS.S.uniqueIds.push(newLayer.uid);

		for (var i in RVS.LIB.ADDONS) {
			if(!RVS.LIB.ADDONS.hasOwnProperty(i)) continue;
			newLayer.addOns[i] = newLayer.addOns[i]===undefined ? {enable:false} : newLayer.addOns[i];
		}

		// UPDATE THE CUSTOM CSS FROM OLDER VERSION
		if (newLayer.customCSS!==undefined && newLayer.customCSS.length>0) {
			newLayer.customCSS = newLayer.customCSS.replace(/\s\s+/g, ' ');
			var rules = newLayer.customCSS.split(";"),
				newrules = "";
			for (var i in rules) {
				if(!rules.hasOwnProperty(i)) continue;
				var rule = rules[i].split(":"),
					key = rule[0].replace(/\s/g, "");

                if(RVS.F.trim(key) === '') continue;
				switch (key) {
					case "letter-spacing":newLayer.idle.letterSpacing = RVS.F.cToResp({default:rule[1]});break;
					case "text-align":newLayer.idle.textAlign = RVS.F.cToResp({default:rule[1]});break;
					case "text-shadow":if (rule[1].indexOf("none")===-1) newrules += rules[i];break;
					default:newrules += rules[i] + ";";break;
				}
			}
			newLayer.customCSS = newrules;
		}

		return newLayer;
	};

	// SIMPLIFY SINGLE LAYER OBJECT STRUCTURE
	RVS.F.simplifyLayer = function(_) {
		if (_.type==="zone")
			return RVS.F.safeExtend(true,{},_);
		else
			return RVS.F.safeExtend(true,{}, RVS.F.simplifyObject(RVS.F.addLayerObj(_.type,undefined,true),RVS.F.safeExtend(true,{},_)));
	};
	// SIMPLIFY ALL LAYER STRUCTURE
	RVS.F.simplifyAllLayer = function(layers) {

		var ret = {};
		for (var i in layers) {
			if(!layers.hasOwnProperty(i)) continue;
			ret[i] = RVS.F.simplifyLayer(layers[i]);
			ret[i].type = layers[i].type;
		}
		return ret;
	};

	// BUILD THE FULL LAYER STRUCTURE OF SIMPLIFIED STRUCTURES
	RVS.F.expandAllLayer = function(layers) {
		var ret= {};
		for (var i in layers) {
			if(!layers.hasOwnProperty(i)) continue;
			ret[i] = RVS.F.safeExtend(true,RVS.F.addLayerObj(layers[i].type,undefined,true), layers[i]);
		}
		return ret;
	};


	RVS.F.initAddonMigration = function(_) {
	};

})();
