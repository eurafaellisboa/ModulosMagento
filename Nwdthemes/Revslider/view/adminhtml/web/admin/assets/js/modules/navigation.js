/*!
 * REVOLUTION 6.3.2 EDITOR NAVIGATION JS
 * @version: 2.0 (01.12.2020)
 * @author ThemePunch
 */

(function() {
	var navtypes = ["arrows","thumbs","bullets","tabs"],
		_leftarrow,_rightarrow,_bullets,_thumbstabs,
		iconlist = '<div class="font_icon_subcontainer"><i class="nav_preseticon_pick rs_ne_icon_e817 revicon-left-dir" data-content="\\e817"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e818 revicon-right-dir" data-content="\\e818"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e819 revicon-left-open" data-content="\\e819"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e81a revicon-right-open" data-content="\\e81a"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e820 revicon-angle-left" data-content="\\e820"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e81d revicon-angle-right" data-content="\\e81d"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e81f revicon-left-big" data-content="\\e81f"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e81e revicon-right-big" data-content="\\e81e"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e82a revicon-left-open-1" data-content="\\e82a"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e82b revicon-right-open-1" data-content="\\e82b"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e822 revicon-left-open-mini" data-content="\\e822"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e823 revicon-right-open-mini" data-content="\\e823"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e824 revicon-left-open-big" data-content="\\e824"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e825 revicon-right-open-big" data-content="\\e825"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e836 revicon-left" data-content="\\e836"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e826 revicon-right" data-content="\\e826"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e82e revicon-left-open-outline" data-content="\\e82e"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e82f revicon-right-open-outline" data-content="\\e82f"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e82c revicon-left-open-2" data-content="\\e82c"></i>'+
					  '<i class="nav_preseticon_pick rs_ne_icon_e82d revicon-right-open-2" data-content="\\e82d"></i>'+
					  '</div>';


	/*
	INIT NAVIGATION LIST AND FUNCTIONS
	*/
	RVS.F.initNavigation = function() {
		_leftarrow = jQuery('#tp-leftarrow');
		_rightarrow = jQuery('#tp-rightarrow');
		_bullets = jQuery('#tp-bullets');
		_thumbstabs = {
					tabs:{self:jQuery('#tp-tabs'), mask:jQuery('#tp-tabs-mask'), inner:jQuery('#tp-tabs-inner-wrapper'), single:"tp-tab"},
					thumbs:{self:jQuery('#tp-thumbs'), mask:jQuery('#tp-thumbs-mask'), inner:jQuery('#tp-thumbs-inner-wrapper'), single:"tp-thumb"}
				};
		RVS.F.buildNavigationLists();
		initLocalListeners();
	};

	/*
	BUILD THE DROP DOWN LISTS FOR NAVIGATION STYLES
	*/
	RVS.F.buildNavigationLists = function(force) {
		for (var j in navtypes) {
			if(!navtypes.hasOwnProperty(j)) continue;
			var _h="";
			for (var i in RVS.nav[navtypes[j]]) {
				if(!RVS.nav[navtypes[j]].hasOwnProperty(i)) continue;
				_h += '<option value="'+i+'">'+RVS.nav[navtypes[j]][i].name+'</option>';
			}
			jQuery('#sr_'+navtypes[j]+'_style')[0].innerHTML = _h; //.ddTP({ placeholder:"Enter or Select"});
			if (force) jQuery('#sr_'+navtypes[j]+'_style').ddTP({placeholder:"Enter or Select"});
		}
		if (force) RVS.F.updateEasyInputs({container:jQuery('#nav_form_collector'),path:"settings."});
	};

	/*
	BUILD LIST FOR FACTORY AND CUSTOM SKINS
	*/
	function buildEditorList(type,force) {
		var f ="", c = "";
		RVS.nav.currentSkinType = type;
		RVS.nav.currentEditorMode = "markup";

		for (var i in RVS.nav[type]) {
			if(!RVS.nav[type].hasOwnProperty(i)) continue;
			let skin = RVS.nav[type][i];
			if (skin.factory)
				f += '<div data-type="'+type+'" data-handle="'+i+'" class="rs_ne_nav_skin rs_ne_listelement"><span class="rs_ne_nskin_title">'+skin.name+'</span><div class="rs_ne_nskin_tbar_basic"><i class="rs_ne_nskin_copy material-icons">content_copy</i></div></div>';
			else {
				c += '<div id="rs_ne_nav_skin_'+i+'" data-type="'+type+'" data-handle="'+i+'" class="rs_ne_nav_skin rs_ne_listelement"><span class="rs_ne_nskin_title">'+skin.name+'</span><span class="rs_ne_nskin_message"></span><input class="rs_ne_nskin_title_input" value="'+skin.name+'" type="text"/><div class="rs_ne_nskin_tbar_basic"><i class="rs_ne_nskin_edit material-icons">edit</i><i class="rs_ne_nskin_copy material-icons">content_copy</i><i class="rs_ne_nskin_delete material-icons">delete</i></div><div class="rs_ne_nskin_tbar_yesno"><i class="rs_ne_nskin_yes material-icons">done</i><i class="rs_ne_nskin_no material-icons">close</i></div></div>';
				//RVS.nav.currentMaxid = Math.max(RVS.nav.currentMaxid,parseInt(skin.id,0));
			}
		}
		document.getElementById('rs_ne_factory_list').innerHTML = f;
		document.getElementById('rs_ne_custom_list').innerHTML = c;
		jQuery('.rs_ne_list_wrapper').scrollTop(0).RSScroll({suppressScrollX:true});
	}

	function setToSave() {
		if (RVS.nav.currentSkin!==undefined) RVS.nav.currentSkin.changed = true;
		jQuery('#save_naveditor').show();
		RVS.nav.toSave = true;
	}

	/*
	LOAD THE EDITOR MODUELE
	*/
	RVS.F.openNavigationEditor = function() {

		RVS.nav= RVS.nav===undefined ? {} : RVS.nav;
		RVS.nav.toSave = false;
		RVS.nav.toDelete = [];


		if (RVS.nav.editor==="FAIL") return;
		if (typeof RevMirror==="undefined" || RevMirror===undefined) {
			RVS.F.showWaitAMinute({fadeIn:500,text:RVS_LANG.loadingRevMirror});
			RVS.F.loadCSS(RVS.ENV.plugin_url+'/admin/assets/css/RevMirror.css');
			jQuery.getScript(RVS.ENV.plugin_url+'/admin/assets/js/plugins/RevMirror.js',function() {
				setTimeout(function() {RVS.F.showWaitAMinute({fadeOut:500});},100);
				RVS.F.openNavigationEditor();
			}).fail(function(a,b,c) {
				setTimeout(function() {RVS.F.showWaitAMinute({fadeOut:500});},100);
				window.nav.editor = "FAIL";
			});
		} else {

			if (RVS.nav.editor===undefined) 	{
				var nff = jQuery('#nav_fontfamily'),
					opts = "";
				for (var fontindex in RVS.LIB.FONTS) if(RVS.LIB.FONTS.hasOwnProperty(fontindex) && RVS.LIB.FONTS[fontindex].label!=="Dont Show Me") opts +='<option value="'+RVS.LIB.FONTS[fontindex].label+'">'+RVS.LIB.FONTS[fontindex].label+'</option>';

				nff.append(opts);
				nff.ddTP({placeholder:"Enter or Select"});

				nff.ddTP('change');
				initLocalListeners();

				RVS.nav.editor = RevMirror(document.getElementById('rs_nav_css_js_area'), {
					value:"",
					mode:"css",
					theme:"hopscotch",
					lineWrapping:true,
					lineNumbers:false,
				});
				navEditorListeners();
				RVS.nav.editor.on('change',function() {
					if (RVS.nav.currentSkin!== undefined && RVS.nav.currentEditorMode!==undefined && RVS.nav.currentSkin[RVS.nav.currentEditorMode]!==undefined && RVS.nav.currentSkin[RVS.nav.currentEditorMode]!==RVS.nav.editor.getValue()) {
						RVS.nav.currentSkin[RVS.nav.currentEditorMode] = RVS.nav.editor.getValue();
						RVS.F.drawEditorNavigation();
						setToSave();
					}
				});
			}

			RVS.nav.cache = {};
			for (var i in navtypes) {
				if(!navtypes.hasOwnProperty(i)) continue;
				RVS.nav.cache[navtypes[i]] = RVS.F.safeExtend(true,{},RVS.nav[navtypes[i]]);
			}
			RVS.nav.currentMaxid = 0;
			buildEditorList("arrows");

			RVS.F.RSDialog.create({modalid:'rbm_navigation_editor', bgopacity:0.5});
			jQuery('.emc_toggle_inner').RSScroll({ suppressScrollX:true});
			RVS.nav.editor.refresh();

			_leftarrow.detach();
			_rightarrow.detach();
			_bullets.detach();
			_thumbstabs.tabs.self.detach();
			_thumbstabs.thumbs.self.detach();

			// PREPEARE SELECTED NAV TYPE AND SELECTED STYLE
			var selectedNavType = "arrows";
			for (var i in navtypes) {
				if(!navtypes.hasOwnProperty(i)) continue;
				var navSettings = document.querySelector('[data-pcontainer="#nav_settings"] .selected');
				if (navSettings && navSettings.dataset && navSettings.dataset.forms && navSettings.dataset.forms.indexOf(navtypes[i])>=0) {
					selectedNavType = navtypes[i];
					break;
				}
			}
			var selectedNavStyle = RVS.SLIDER.settings.nav[selectedNavType]!==undefined ? RVS.SLIDER.settings.nav[selectedNavType].style : "1000";
			RVS.F.pickNavType(document.getElementById('rs_ne_selector_'+selectedNavType));
			var navStyle = jQuery('#rs_ne_navlist .rs_ne_nav_skin[data-handle="'+selectedNavStyle+'"]');
			if (navStyle.length>0) pickNavStyle(navStyle[0]);
			RVS.F.pickMarkupCssMode(document.getElementById(('rs_ne_mcss_thecsseditor')));

		}
	};

	/*
	BUILD THE LIST OF THE SELECTED NAVIGATION TYPE
	*/
	function buildNavsMetaList() {
		document.getElementById('rs_ne_nav_width').value = RVS.nav.currentSkin.dim.width;
		document.getElementById('rs_ne_nav_height').value = RVS.nav.currentSkin.dim.height;
		document.getElementById('rs_ne_nav_classname').value = RVS.nav.currentSkin.handle;
		var _h = '';
		for (var i in RVS.nav.currentSkin.placeholders) {
			if(!RVS.nav.currentSkin.placeholders.hasOwnProperty(i)) continue;
			let p = RVS.nav.currentSkin.placeholders[i];
			_h+='<div data-placeholder="'+i+'" class="rs_ne_meta_value_btn rs_ne_listelement" ><span data-insert="##'+i+'##" class="rs_ne_nskin_title">'+p.title+'</span><span class="rs_ne_nskin_message"></span>';
			_h+='<div class="rs_ne_nskin_tbar_basic"><i data-insert="##'+i+'##" class="rs_ne_nskin_meta_add material-icons">add</i><i data-placeholder="'+i+'" class="rs_ne_nskin_meta_config material-icons">settings</i><i class="rs_ne_nskin_delete material-icons">delete</i></div><div class="rs_ne_nskin_tbar_yesno"><i class="rs_ne_nskin_yes material-icons">done</i><i class="rs_ne_nskin_no material-icons">close</i></div>';
			_h+='</div>';
		}
		document.getElementById('rs_ne_meta_values_inner').innerHTML = _h;
	}

	/*
	CHECK IF ANY OF IMPORTANT META INPUTES ARE MISSING OR DOUBLED
	*/
	function checkImportandMetaFields() {
		var placeholder = jQuery('#rs_ne_def_meta_handle')[0].value;

		if ((placeholder.length===0 || jQuery('#rs_ne_def_meta_title')[0].value.length===0) ||
			(RVS.nav.currentPlaceholder!==placeholder && RVS.nav.currentSkin.placeholders[placeholder]!==undefined))
			jQuery('#update_nav_meta_value').addClass("disabled");
		else
			jQuery('#update_nav_meta_value').removeClass("disabled");

		if (RVS.nav.currentPlaceholder!==placeholder && RVS.nav.currentSkin.placeholders[placeholder]!==undefined)
			jQuery('#rs_ne_def_meta_handle').addClass("badvalue");
		else
			jQuery('#rs_ne_def_meta_handle').removeClass("badvalue");
	}

	/*
	DRAW THE EDITOR NAVIGATIONS
	*/
	RVS.F.drawEditorNavigation = function() {
		if (RVS.nav.c===undefined) {
			RVS.nav.c = {
				arrows : jQuery('#rs_ne_arrows'),
				left : jQuery('#rs_ne_tp-leftarrow'),
				right: jQuery('#rs_ne_tp-rightarrow'),
				bullets : jQuery('#rs_ne_bullets'),
				tabs : {
						self:jQuery('#rs_ne_tabs'),
						inner:jQuery('#rs_ne_tabs-inner-wrapper'),
						mask:jQuery('#rs_ne_tabs-mask'),
						single:"tp-tab"
				},
				thumbs : {
						self:jQuery('#rs_ne_thumbs'),
						inner:jQuery('#rs_ne_thumbs-inner-wrapper'),
						mask:jQuery('#rs_ne_thumbs-mask'),
						single:"tp-thumb"
				}
			};
		}

		RVS.nav.currentTestSpace = RVS.nav.currentTestSpace==undefined ? 5 : RVS.nav.currentTestSpace;
		RVS.nav.currentTestPadding = 10;
		RVS.nav.currentAlignMode = RVS.nav.currentAlignMode==undefined ? "horizontal" : RVS.nav.currentAlignMode;
		RVS.nav.currentPosVer = RVS.nav.currentPosVer==undefined ? "bottom" : RVS.nav.currentPosVer;
		RVS.nav.currentPosHor = RVS.nav.currentPosHor==undefined ? "center" : RVS.nav.currentPosHor;

		var cname = RVS.F.sanitize_input(RVS.nav.currentSkin.handle.toLowerCase())+" "+RVS.F.sanitize_input(RVS.nav.currentSkin.name.toLowerCase()),
			thecss = manipulateNavCSS({type:RVS.nav.currentSkinType, skin:RVS.nav.currentSkin ,default:true});

		switch (RVS.nav.currentSkinType) {
			case "arrows":
				var markup = RVS.nav.currentSkin.markup.replace('##title##',"Title");
				RVS.nav.c.left[0].className ="tparrows tp-leftarrow "+cname;
				RVS.nav.c.right[0].className ="tparrows tp-rightarrow "+cname;
				RVS.nav.c.left[0].innerHTML = thecss+markup;
				RVS.nav.c.right[0].innerHTML = markup;
				RVS.nav.c.arrows.show();
				RVS.nav.c.bullets.hide();
				RVS.nav.c.tabs.self.hide();
				RVS.nav.c.thumbs.self.hide();
			break;
			case "bullets":
				RVS.nav.c.arrows.hide();
				RVS.nav.c.tabs.self.hide();
				RVS.nav.c.thumbs.self.hide();
				RVS.nav.c.bullets.show();
				RVS.nav.c.bullets[0].className = 'tp-bullets '+cname+' nav-dir-'+RVS.nav.currentAlignMode+' nav-pos-ver-'+RVS.nav.currentPosVer+' nav-pos-hor-'+RVS.nav.currentPosHor;

				var markup = RVS.nav.currentSkin.markup.replace('##title##','Slide Title'),
					_h = "";
				for (var i=0;i<=4;i++) {
					_h += '<div class="tp-bullet '+(i==0 ? "selected" : "")+'">'+markup+'</div>';
				}
				RVS.nav.c.bullets[0].innerHTML = thecss+_h;

				RVS.nav.c.bullets.find('.tp-bullet').each(function(i) {
					var b = jQuery(this),
						am = 5,
						w = b.outerWidth()+parseInt((RVS.nav.currentTestSpace===undefined? 0:RVS.nav.currentTestSpace),0),
						h = b.outerHeight()+parseInt((RVS.nav.currentTestSpace===undefined? 0:RVS.nav.currentTestSpace),0);

					if (RVS.nav.currentAlignMode==="vertical") {
						b.css({top:((i)*h)+"px", left:"0px"});
						tpGS.gsap.set(RVS.nav.c.bullets,{height:(((am-1)*h) + b.outerHeight()),width:b.outerWidth()});
					}
					else {
						b.css({left:((i)*w)+"px", top:"0px"});
						tpGS.gsap.set(RVS.nav.c.bullets,{width:(((am-1)*w) + b.outerWidth()),height:b.outerHeight()});
					}
				});
			break;
			case "tabs":
			case "thumbs":
				var all = RVS.nav.currentSkinType,
					single = all.replace("s","");

				RVS.nav.c.arrows.hide();
				RVS.nav.c.bullets.hide();
				RVS.nav.c.tabs.self.hide();
				RVS.nav.c.thumbs.self.hide();
				RVS.nav.c[all].self.show();
				RVS.nav.c[all].self[0].className = 'tp-'+all+' '+cname+' nav-dir-'+RVS.nav.currentAlignMode+' nav-pos-ver-'+RVS.nav.currentPosVer+' nav-pos-hor-'+RVS.nav.currentPosHor;

				var markup = RVS.nav.currentSkin.markup.replace('##title##','Slide Title'),
					_h = "";
				for (var i=0;i<10;i++) markup = markup.replace('##param'+i+'##',"Parameter "+i);
				for (var i=0;i<=2;i++) {_h += '<div class="'+cname+' tp-'+single+' '+(i==0 ? "selected" : "")+'">'+markup+'</div>';}
				RVS.nav.c[all].inner[0].innerHTML = thecss+_h;

				//UPDATE CLASSES
				var am = 3,
					w = parseInt(RVS.nav.currentSkin.dim.width,0)+parseInt(RVS.nav.currentTestSpace,0),
					h = parseInt(RVS.nav.currentSkin.dim.height,0)+parseInt(RVS.nav.currentTestSpace,0);

				tpGS.gsap.set(RVS.nav.c[all].self,{padding:RVS.nav.currentTestPadding});
				RVS.nav.currentTestPadding = parseInt(RVS.nav.currentTestPadding,0);

				// SET BULLET SPACES AND POSITION
				RVS.nav.c[all].inner.find('.'+RVS.nav.c[all].single).each(function(i) {
					if (RVS.nav.currentAlignMode==="vertical")
						tpGS.gsap.set(this,{top:((i)*h)+"px",left:"0px",width:RVS.nav.currentSkin.dim.width+"px",height:RVS.nav.currentSkin.dim.height+"px"});
					else
						tpGS.gsap.set(this,{left:((i)*w)+"px", top:"0px",width:RVS.nav.currentSkin.dim.width+"px",height:RVS.nav.currentSkin.dim.height+"px"});
				});

				var maxw = RVS.nav.currentAlignMode==="horizontal" ? (RVS.nav.currentSkin.dim.width * am) + (RVS.nav.currentTestSpace*(am-1)) : RVS.nav.currentSkin.dim.width,
					maxh = RVS.nav.currentAlignMode==="horizontal" ? RVS.nav.currentSkin.dim.height : (RVS.nav.currentSkin.dim.height * am) + (RVS.nav.currentTestSpace*(am-1)),
					wrapcssobj = {width:maxw+"px", height:maxh+"px",overwrite:"auto"},
					maskcssobj = {top:"auto", left:"auto", bottom:"auto", marginTop:"0px", marginBottom:"0px",right:"auto", y:"0%", x:"0px",width:maxw+"px",height:maxh+"px",overflow:"hidden",position:"relative",overwrite:"auto",marginLeft:"auto",marginRight:"auto"};
					/* innecssobj = {y:"0px",x:"0px",position:"relative"}; */

				tpGS.gsap.set(RVS.nav.c[all].self,wrapcssobj);
				tpGS.gsap.set(RVS.nav.c[all].mask,maskcssobj);
			break;
		}
	};

	/*
	SAVE NAVIGATION
	*/
	RVS.F.savechangesonnavigation = function() {
		RVS.nav.changes = {};
		//CACHE LAST SELECTED ITEM
		RVS.nav.currentHandle = RVS.nav.currentSkin!==undefined ? RVS.nav.currentSkin.handle : undefined;


		for (var nt in navtypes) {
			if(!navtypes.hasOwnProperty(nt)) continue;
			for (var i in RVS.nav[navtypes[nt]]) {
				if(!RVS.nav[navtypes[nt]].hasOwnProperty(i)) continue;
				if (RVS.nav[navtypes[nt]][i].changed) {
					RVS.nav.changes[i] = RVS.F.safeExtend(true,{},RVS.nav[navtypes[nt]][i]);
					delete RVS.nav.changes[i].changed;
                    try {
                        var sspres = RVS.SLIDER[RVS.S.slideId].slide.nav[navtypes[nt]].presets;
                        for (var j in RVS.nav.changes[i].placeholders) {
                            if (!RVS.nav.changes[i].placeholders.hasOwnProperty(j) || sspres[j]===undefined || sspres[j+"-def"]===true) continue;
                            sspres[j] = RVS.nav.changes[i].placeholders[j].data;
                        }

                    } catch(e) {}
				}
			}
		}

		RVS.F.ajaxRequest("save_navigation", {navs:RVS.nav.changes, delete:RVS.nav.toDelete}, function(response){
			for (var i in navtypes) {
				if(!navtypes.hasOwnProperty(i)) continue;
				RVS.nav[navtypes[i]] = RVS.F.safeExtend(true,{},response.navs[navtypes[i]]);
			}
			if (RVS.nav.currentHandle!==undefined) {
				// REFRESH CONTENT
				buildEditorList(RVS.nav.currentSkinType);

				// ReSelect last Selected Element
				var lastselected,id,newselected;
				for (var i in RVS.nav[RVS.nav.currentSkinType]) {
					if(!RVS.nav[RVS.nav.currentSkinType].hasOwnProperty(i)) continue;
					lastselected = lastselected===undefined && RVS.nav[RVS.nav.currentSkinType][i].handle == RVS.nav.currentHandle ? i : lastselected;
				}
				if (lastselected!==undefined) jQuery('#rs_ne_nav_skin_'+lastselected).trigger('click');

			}
		});

		RVS.nav.toDelete = [];
		RVS.nav.toSave = false;
		jQuery('#save_naveditor').hide();

		RVS.F.updatePresetInputs({type: RVS.nav.currentSkinType,env:"slide"});
		RVS.F.updatePresetInputs({type: RVS.nav.currentSkinType,env:"global"});
	};


	//CHECK HANDLE TO USE AS CLASS NAME
	RVS.F.checkSkinHandle = function(handle) {
		var exists = handle.length<4 || jQuery.inArray(handle,["cursor","pointer","margin","padding","display","position","width","height","transition","relative","absolute","color","arrows","thumbs","arrow","thumb","tab","tabs","bullet","bullets","hover","z-index"])>=0;
		for (var i in RVS.nav[RVS.nav.currentSkinType]) {
			if(!RVS.nav[RVS.nav.currentSkinType].hasOwnProperty(i)) continue;
			exists = exists ===true || RVS.nav[RVS.nav.currentSkinType][i].handle===handle ? true :false;
		}
		return exists;
	};

	function reAddOriginalNav() {
		_leftarrow.appendTo(jQuery('#rev_slider_ul_inner'));
		_rightarrow.appendTo(jQuery('#rev_slider_ul_inner'));
		_bullets.appendTo(jQuery('#rev_slider_ul_inner'));
		_thumbstabs.tabs.self.appendTo(jQuery('#rev_slider_ul_inner'));
		_thumbstabs.thumbs.self.appendTo(jQuery('#rev_slider_ul_inner'));
	}

	/*
	LISTENERS FOR NAVIGATION EDITOR
	*/
	function navEditorListeners() {



		//PICK A NAVIGATION ELEMENT TO EDIT / VIEW
		RVS.DOC.on('click','.rs_ne_nav_skin',function() {
			pickNavStyle(this);
		});

		//CLOSE CSS AND JS EDITOR
		RVS.DOC.on('click', '#rbm_navigation_editor .rbm_close', function() {

			if (!RVS.nav.toSave) {

				reAddOriginalNav();
				RVS.F.RSDialog.close();
				RVS.F.buildNavigationLists(true);
			} else {

				RVS.F.RSDialog.create({
					bgopacity:0.85,
					modalid:'rbm_decisionModal',
					icon:'warning',
					title:RVS_LANG.warning,
					maintext:RVS_LANG.changesdone_exit,
					subtext:RVS_LANG.exitwihoutchangesornot,
					do:{
						icon:"exit_to_app",
						text:RVS_LANG.leavewithoutsave,
						event: "leavaeditorwithoutsave"
					},
					cancel:{
						icon:"cancel",
						text:RVS_LANG.stayineditor
				}});

				// ASK IF YOU REALLY WANT TO LEAVE
			}
		});

		// LEAVE WITHOUT SAVING
		RVS.DOC.on('leavaeditorwithoutsave',function() {
			// OVERWRITE OLD CACHE VALUES ON LIFE VERSION, SINCE EDITOR CLOSED WITHOUT SAVE !
			for (var i in navtypes) {
				if(!navtypes.hasOwnProperty(i)) continue;
				RVS.nav[navtypes[i]] = RVS.F.safeExtend(true,{},RVS.nav.cache[navtypes[i]]);
			}
			RVS.nav.toDelete = [];
			RVS.nav.toSave = false;
			jQuery('#save_naveditor').hide();
			reAddOriginalNav();
		});


		// CLICK ON SAVE NAVIGATION
		RVS.DOC.on('click','#save_naveditor',RVS.F.savechangesonnavigation);


		//NAVIGATION TYPE SELECTOR
		RVS.DOC.on('click','.rs_ne_selector',function() {
			RVS.F.pickNavType(this);
		});


		// CONFIGURATE PLACEHOLDER
		RVS.DOC.on('click','.rs_ne_nskin_meta_config',function() {
			jQuery('#rs_ne_cssmeta_config').show();
			jQuery('#rs_ne_cssmeta_values').hide();
			RVS.nav.currentPlaceholder = this.dataset.placeholder;
			jQuery('#rs_ne_meta_type').val(RVS.nav.currentSkin.placeholders[RVS.nav.currentPlaceholder].type).change();
			document.getElementById('rs_ne_def_meta_title').value = RVS.nav.currentSkin.placeholders[RVS.nav.currentPlaceholder].title;
			document.getElementById('rs_ne_def_meta_handle').value = RVS.nav.currentPlaceholder;
			switch (RVS.nav.currentSkin.placeholders[RVS.nav.currentPlaceholder].type) {
				case "color":
					jQuery('#rs_ne_def_meta_color_val').val(RVS.nav.currentSkin.placeholders[RVS.nav.currentPlaceholder].data).rsColorPicker("refresh");
				break;
				case "icon":
					jQuery('#rs_ne_def_meta_icon_val').val(RVS.nav.currentSkin.placeholders[RVS.nav.currentPlaceholder].data);
					var code = RVS.nav.currentSkin.placeholders[RVS.nav.currentPlaceholder].data;
					code = code.length>0 ? code.replace('\\',"") : code;
					jQuery('.rs_ne_pick.selected').removeClass("selected");
					jQuery('.rs_ne_icon_'+code).addClass("selected");
				break;
				case "custom":
					jQuery('#rs_ne_def_meta_custom_val').val(RVS.nav.currentSkin.placeholders[RVS.nav.currentPlaceholder].data);
				break;
				case "font-family":
					// jQuery('#nav_fontfamily').val(RVS.nav.currentSkin.placeholders[placeholder].data);
					var nff= jQuery('#nav_fontfamily')
					nff.val(RVS.nav.currentSkin.placeholders[RVS.nav.currentPlaceholder].data);
					nff.ddTP('change');
				break;
			}
		});

		// ADD NEW PLACEHOLDER
		RVS.DOC.on('click','#add_new_placeholder',function() {
			jQuery('#rs_ne_cssmeta_config').show();
			jQuery('#rs_ne_cssmeta_values').hide();
			RVS.nav.currentPlaceholder = "in_create_process";
			document.getElementById('rs_ne_def_meta_title').value = "";
			document.getElementById('rs_ne_def_meta_handle').value = "";
			jQuery('#rs_ne_def_meta_color_val').val('#ffffff');
			jQuery('#rs_ne_def_meta_icon_val').val();
			jQuery('#rs_ne_def_meta_custom_val').val();
			jQuery('#nav_fontfamily').val("Arial");
			checkImportandMetaFields();
		});

		// CHANGING THE HANDLE NAME HAS REAL INFLUENCE ON CONTENT
		RVS.DOC.on('change','#rs_ne_nav_classname',function() {
			var _old = RVS.nav.currentSkin.handle,
				_new = RVS.F.sanitize_input_lc(this.value.toLowerCase());
			this.value = _new;
			if (_old!==_new) {
				if (RVS.F.checkSkinHandle(this.value)) {
					jQuery('#rs_ne_nav_classname').addClass("badvalue");
				} else {
					jQuery('#rs_ne_nav_classname').removeClass("badvalue");
					RVS.nav.currentSkin.handle = _new;
					var re = new RegExp("\\."+_old,"g");
					RVS.nav.currentSkin.css = RVS.nav.currentSkin.css.replace(re,"."+_new);
					RVS.nav.editor.setValue(RVS.nav.currentSkin[RVS.nav.currentEditorMode]);
				}
				setToSave();
			}
		});



		//VERTICAL / HORIZONTAL CHANGER
		RVS.DOC.on('setrsnavtovertical',function() {
			jQuery('#rs_ne_horizontaltest').removeClass("selected");
			jQuery('#rs_ne_verticaltest').addClass("selected");
			RVS.nav.currentAlignMode = "vertical";
			RVS.F.drawEditorNavigation();

		});

		RVS.DOC.on('setrsnavtohorizontal',function() {
			jQuery('#rs_ne_horizontaltest').addClass("selected");
			jQuery('#rs_ne_verticaltest').removeClass("selected");
			RVS.nav.currentAlignMode = "horizontal";
			RVS.F.drawEditorNavigation();
		});

		// BASIC TEST MODE VALUES CHANGED
		RVS.DOC.on('rsdimgapchange',function(a,b) {
			if (b!==undefined && b.eventparam!==undefined) {
				switch (b.eventparam) {
					case "width":
					case "height":
						RVS.nav.currentSkin.dim[b.eventparam] = b.val;
						setToSave();
					break;
					case "space":
						RVS.nav.currentTestSpace = b.val;
					break;
				}
				RVS.F.drawEditorNavigation();
			}
		});


		// POSITION FOR PREVIEW CHANGED
		RVS.DOC.on('setrsnavposition',function() {
			switch (jQuery('#rs_nav_test_position').val()) {
				case "left top": RVS.nav.currentPosVer = "top"; RVS.nav.currentPosHor = "left";break;
				case "center top":RVS.nav.currentPosVer = "top";RVS.nav.currentPosHor = "center";break;
				case "right top":RVS.nav.currentPosVer = "top";RVS.nav.currentPosHor = "right";break;
				case "left center": RVS.nav.currentPosVer = "center"; RVS.nav.currentPosHor = "left";break;
				case "center center":RVS.nav.currentPosVer = "center";RVS.nav.currentPosHor = "center";break;
				case "right center":RVS.nav.currentPosVer = "center";RVS.nav.currentPosHor = "right";break;
				case "left bottom": RVS.nav.currentPosVer = "bottom"; RVS.nav.currentPosHor = "left";break;
				case "center bottom":RVS.nav.currentPosVer = "bottom";RVS.nav.currentPosHor = "center";break;
				case "right bottom":RVS.nav.currentPosVer = "bottom";RVS.nav.currentPosHor = "right";break;
			}
			RVS.F.drawEditorNavigation();
		});


		// CHANGING TITLE OR HANDLE OF META
		RVS.DOC.on('change','#rs_ne_def_meta_title',checkImportandMetaFields);
		RVS.DOC.on('change','#rs_ne_def_meta_handle',function() {
			this.value = RVS.F.sanitize_input_lc(this.value);
			checkImportandMetaFields();
		});

		// CHANGING ICON
		RVS.DOC.on('click','.rs_ne_pick',function() {
			jQuery('.rs_ne_pick.selected').removeClass("selected");
			this.className +=" selected";
			jQuery('#rs_ne_def_meta_icon_val').val(this.dataset.content);
		});


		// INSERT META INTO MARKUP
		//Insert into Editor Listener
		RVS.DOC.on('click','.rs_ne_nskin_meta_add, .rs_ne_markup_meta_btn',function() {
			if (!RVS.nav.infactorymode)
				RVS.F.insertTextAtCursor(RVS.nav.editor,this.dataset.insert);

			return false;
		});

		// CLOSE WITHOUT CHANGES THE META VALUE EDITOR
		RVS.DOC.on('closenavmetavalue',function() {
			jQuery('#rs_ne_cssmeta_config').hide();
			jQuery('#rs_ne_cssmeta_values').show();
		});

		// CLOSE WITH CHANGES THE META VALUE EDITOR
		RVS.DOC.on('updatenavmetavalue',function() {
			var placeholder = document.getElementById('rs_ne_def_meta_handle').value;
			RVS.nav.currentSkin.placeholders = RVS.nav.currentSkin.placeholders==="" || RVS.nav.currentSkin.placeholders===undefined || typeof RVS.nav.currentSkin.placeholders== "string" ? {} : RVS.nav.currentSkin.placeholders;
			RVS.nav.currentSkin.placeholders[placeholder] = RVS.nav.currentSkin.placeholders[placeholder]===undefined || RVS.nav.currentSkin.placeholders[placeholder]=="" ? {} : RVS.nav.currentSkin.placeholders[placeholder];

			RVS.nav.currentSkin.placeholders[placeholder].type = document.getElementById('rs_ne_meta_type').value;
			RVS.nav.currentSkin.placeholders[placeholder].title = document.getElementById('rs_ne_def_meta_title').value;
			switch (RVS.nav.currentSkin.placeholders[placeholder].type) {
				case "color":
					RVS.nav.currentSkin.placeholders[placeholder].data = document.getElementById('rs_ne_def_meta_color_val').value;
				break;
				case "icon":
					RVS.nav.currentSkin.placeholders[placeholder].data = document.getElementById('rs_ne_def_meta_icon_val').value;
				break;
				case "custom":
					RVS.nav.currentSkin.placeholders[placeholder].data = document.getElementById('rs_ne_def_meta_custom_val').value;
				break;
				case "font-family":
					RVS.nav.currentSkin.placeholders[placeholder].data = document.getElementById('nav_fontfamily').value;
				break;
			}
			if (placeholder !== RVS.nav.currentPlaceholder) {
				//RENAME IN MARKUP ALL THE EXISTING HANDLES !!
				delete RVS.nav.currentSkin.placeholders[RVS.nav.currentPlaceholder];
			}

			buildNavsMetaList();
			jQuery('#rs_ne_cssmeta_config').hide();
			jQuery('#rs_ne_cssmeta_values').show();
			RVS.F.drawEditorNavigation();
			setToSave();
		});

		// CHANGE CUSTOM NAVIGATION NAME
		RVS.DOC.on('click','.rs_ne_nskin_edit',function() {
			var skin = jQuery(this).closest('.rs_ne_nav_skin');
			skin[0].dataset.mode="rename";
			skin.addClass("rs_changename");
			skin.find('input').trigger('focus').trigger('select');
			RVS.S.waitOnFeedback = { allowed:["rs_ne_nskin_title_input", "rs_ne_nskin_yes","rs_ne_nskin_no", "rbm_close"], closeEvent:"hideCustomNavNameEntering"};
			RVS.F.addBodyClickListener();
			return false;
		});

		// DELETE CUSTOM NAVIGATION
		RVS.DOC.on('click','.rs_ne_nskin_delete',function() {
			var skin = jQuery(this).closest('.rs_ne_listelement');
			skin[0].dataset.mode="delete";
			skin.addClass("rs_showmessage");
			skin.find('.rs_ne_nskin_message').text(RVS_LANG.deletetemplate);
			RVS.S.waitOnFeedback = { allowed:["rs_ne_nskin_yes","rs_ne_nskin_no", "rbm_close"], closeEvent:"hideCustomNavNameEntering"};
			RVS.F.addBodyClickListener();
			return false;
		});

		// DUPLICATE NAVIGATION
		RVS.DOC.on('click','.rs_ne_nskin_copy',function() {
			var skin = jQuery(this).closest('.rs_ne_nav_skin');
			RVS.nav.currentMaxid++;
			RVS.nav[skin[0].dataset.type]["new_"+RVS.nav.currentMaxid] = RVS.F.safeExtend(true,{},RVS.nav[skin[0].dataset.type][skin[0].dataset.handle]);

			var _old = RVS.nav[skin[0].dataset.type]["new_"+RVS.nav.currentMaxid].handle,
				_new = RVS.nav[skin[0].dataset.type]["new_"+RVS.nav.currentMaxid].handle +"_copy"+Math.round(Math.random()*100+10);
			if (RVS.F.checkSkinHandle(_new)===true) _new = _new+Math.round(Math.random()*100+10);
			if (RVS.F.checkSkinHandle(_new)===true) _new = _new+Math.round(Math.random()*100+10);

			RVS.nav[skin[0].dataset.type]["new_"+RVS.nav.currentMaxid].factory = false;
			RVS.nav[skin[0].dataset.type]["new_"+RVS.nav.currentMaxid].name += " Copy";
			RVS.nav[skin[0].dataset.type]["new_"+RVS.nav.currentMaxid].handle = _new;
			RVS.nav[skin[0].dataset.type]["new_"+RVS.nav.currentMaxid].changed = true;
			RVS.nav[skin[0].dataset.type]["new_"+RVS.nav.currentMaxid].id = "new_"+RVS.nav.currentMaxid;
			RVS.nav[skin[0].dataset.type]["new_"+RVS.nav.currentMaxid].presets={};

			// CHANGE THE CLASS NAMES AS WELL
			var re = new RegExp("\\."+_old,"g");
			RVS.nav[skin[0].dataset.type]["new_"+RVS.nav.currentMaxid].css = RVS.nav[skin[0].dataset.type]["new_"+RVS.nav.currentMaxid].css.replace(re,"."+_new);


			RVS.S.waitOnFeedback = undefined;
			jQuery(document.body).unbind('click.revbuilderbodyclick');
			buildEditorList(skin[0].dataset.type);
			setToSave();

			return false;
		});

		// CREATE NEW NAVIGATION
		RVS.DOC.on('click','#rs_ne_new_custom_nav',function() {
			RVS.nav.currentMaxid++;
			RVS.nav[RVS.nav.currentSkinType]["new_"+RVS.nav.currentMaxid] = RVS.F.createNewNavigation(RVS.nav.currentSkinType);
			buildEditorList(RVS.nav.currentSkinType);
			jQuery("#rs_ne_nav_skin_new_"+RVS.nav.currentMaxid).trigger('click');
			jQuery("#rs_ne_nav_skin_new_"+RVS.nav.currentMaxid+' .rs_ne_nskin_edit').trigger('click');
			setToSave();
		});

		//HIDE CUSTOM LAYER NAME ENTERING PROCESS
		RVS.DOC.on('hideCustomNavNameEntering',function() {
			jQuery('.rs_changename').removeClass("rs_changename");
		});

		// CLICK ON YES - CHANGE NAME/ DELETE TEMPLATE
		RVS.DOC.on('click','.rs_ne_nskin_yes',function() {
			var skin = jQuery(this).closest('.rs_ne_listelement'),
				inp = skin.find('input');
			if (skin[0].dataset.mode==="rename") {
				RVS.nav[skin[0].dataset.type][skin[0].dataset.handle].name = inp.val();
				RVS.S.waitOnFeedback = undefined;
				jQuery(document.body).unbind('click.revbuilderbodyclick');
				buildEditorList(skin[0].dataset.type);
				setToSave();
				return false;
			} else
			if (skin[0].dataset.mode==="delete") {
				if (skin.hasClass("rs_ne_meta_value_btn")) {
					delete RVS.nav.currentSkin.placeholders[skin[0].dataset.placeholder];
					RVS.S.waitOnFeedback = undefined;
					jQuery(document.body).unbind('click.revbuilderbodyclick');
					buildNavsMetaList();
					RVS.F.drawEditorNavigation();
				} else {
					// IF IT WAS ALREADY EXISTING IN DB, MARK TO DELETE
					if (RVS.nav.cache[skin[0].dataset.type][skin[0].dataset.handle]!==undefined) RVS.nav.toDelete.push(skin[0].dataset.handle);
					delete RVS.nav[skin[0].dataset.type][skin[0].dataset.handle];
					RVS.S.waitOnFeedback = undefined;
					jQuery(document.body).unbind('click.revbuilderbodyclick');
					buildEditorList(skin[0].dataset.type);
				}
				setToSave();
				return false;
			}
		});

		// CLICK ON ON (CHANGE NAME / DELETE TEMPLATE)
		RVS.DOC.on('click','.rs_ne_nskin_no',function() {
			var skin = jQuery(this).closest('.rs_ne_listelement');
			RVS.S.waitOnFeedback = undefined;
			jQuery(document.body).unbind('click.revbuilderbodyclick');
			if (skin.hasClass("rs_ne_meta_value_btn"))
				buildNavsMetaList();
			else
				buildEditorList(skin[0].dataset.type);
			return false;
		});

		// MARKUP/CSS SELECTOR
		RVS.DOC.on('click','.rs_ne_markup_css_button',function() {
			RVS.F.pickMarkupCssMode(this);
		});
	}



	/*
	TRIGGERED COLOR CHANGE ON THUMBS AND TABS
	*/
	RVS.F.bgUpdate = function(_, val) {
		tpGS.gsap.set(_thumbstabs[_].self,{background:val});
	};

	/*
	PICK CSS / MARKUP MODE SELECTOR
	 */
	RVS.F.pickMarkupCssMode = function(a) {
		jQuery('.rs_ne_markup_css_button.selected').removeClass("selected");
		jQuery(a.dataset.hide).hide();
		jQuery(a.dataset.show).show();
		RVS.nav.currentEditorMode = a.dataset.mode;
		a.className += " selected";
		if (RVS.nav.currentSkin!==undefined)
			RVS.nav.editor.setValue(RVS.nav.currentSkin[RVS.nav.currentEditorMode]);
	}

	/*
	PICK A NAVIGATION TYPE
	*/
	RVS.F.pickNavType = function(a) {
		jQuery('.rs_ne_selector.selected').removeClass("selected");
		a.className +=" selected";
		RVS.nav.currentSkinType = a.dataset.type;
		jQuery('#rs_ne_helper_wrap, #rs_ne_settings, #rs_ne_markup_css_button_wrap').hide();
		jQuery('#rs_ne_cssmeta_config').hide();
		jQuery('#rs_ne_cssmeta_values').show();
		RVS.F.pickMarkupCssMode(document.getElementById(('rs_ne_mcss_thecsseditor')));
		buildEditorList(a.dataset.type);
		jQuery('#rs_ne_factory_list .rs_ne_nav_skin').first().trigger('click');
	}

	/*
	PICK A NAVIGTION STYLE
	*/
	function pickNavStyle(a) {
		jQuery('.rs_ne_nav_skin.selected').removeClass("selected");
		a.className += " selected";
		RVS.nav.currentSkinType = a.dataset.type;
		RVS.nav.currentSkinHandle = a.dataset.handle;
		RVS.nav.currentSkin = RVS.nav[a.dataset.type][a.dataset.handle];
		buildNavsMetaList();
		jQuery('#rs_ne_helper_wrap, #rs_ne_settings, #rs_ne_markup_css_button_wrap').show();
		jQuery('#rs_ne_cssmeta_config').hide();
		jQuery('#rs_ne_cssmeta_values').show();
		jQuery('#rs_ne_nav_classname').removeClass("badvalue");

		//SET DEFAULT VALUE
		RVS.nav.editor.setValue(RVS.nav.currentSkin[RVS.nav.currentEditorMode]);
		RVS.nav.infactorymode = RVS.nav.currentSkin.factory;
		if (RVS.nav.infactorymode)
			jQuery('#rs_ne_settings').addClass("infactorymode");
		else
			jQuery('#rs_ne_settings').removeClass("infactorymode");

		RVS.nav.editor.setOption("readOnly",RVS.nav.currentSkin.factory);
		RVS.F.drawEditorNavigation();
		RVS.F.pickMarkupCssMode(document.getElementById(('rs_ne_mcss_thecsseditor')));
	}

	/*
	ADD A NEW PRESET ELEMENT TO BE ABLE TO RESET TO THE LOADED SESSION SETTINGS
	*/
	/*
	function addSessionStartNavigation(_) {
		for (var nav in RVS.nav) {
			if(!RVS.nav.hasOwnProperty(nav)) continue;
			if (nav===_.obj.style) {
				RVS.nav[nav].presets = RVS.nav[nav].presets===undefined ? {} : RVS.nav[nav].presets;
				RVS.nav[nav].presets.sessionstart = { name:"Session Start",values:RVS.F.safeExtend(true,{},_.obj.presets)};
			}
		}
	}
	*/

	/*
	PRESET SELECTION CHANGED
	*/
	RVS.F.setNavPresetValues = function(obj) {

		var _ = RVS.SLIDER.settings.nav[obj.type];

		if (_.preset==="default") {
			for (var p in RVS.nav[obj.type][_.style].placeholders) {
				if(!RVS.nav[obj.type][_.style].placeholders.hasOwnProperty(p)) continue;
				RVS.F.updateSliderObj({path:'settings.nav.'+obj.type+'.presets',val:{}});
			}
		} else {

			var preset =  RVS.nav[obj.type][_.style].presets[_.preset].values;
			for (var valueindex in preset) {
				if(!preset.hasOwnProperty(valueindex)) continue;
				if (valueindex.indexOf('-def')===-1) {
					RVS.F.updateSliderObj({path:'settings.nav.'+obj.type+'.presets.'+valueindex,val:preset[valueindex]});
				} else {
					if (preset[valueindex]==="on" || preset[valueindex]===true || preset[valueindex]==='true')
						RVS.F.updateSliderObj({path:'settings.nav.'+obj.type+'.presets.'+valueindex,val:true});
				}
			}
		}
		RVS.F.updateNavStyleSelection({init:false,type:obj.type});
	};

	/*
	UPDATE PRESET INPUT FIELDS
	*/
	RVS.F.updatePresetInputs = function(obj) {
		var _ =  obj.env==="slide" ? RVS.SLIDER[RVS.S.slideId].slide.nav[obj.type] : RVS.SLIDER.settings.nav[obj.type],
			container = obj.env==="slide" ? jQuery('#sl_'+obj.type+'_styles_fieldset') : jQuery('#sr_'+obj.type+'_styles_fieldset'),
			cgroup = obj.env==="slide" ?  document.getElementById('form_slide_nav_'+obj.type) : document.getElementById('form_nav_'+obj.type+'_style');



		// IF NAVIGATION NOT EXISTS ANY MORE !!
		RVS.SLIDER.settings.nav[obj.type].style = RVS.nav[obj.type][RVS.SLIDER.settings.nav[obj.type].style]===undefined ? obj.type==="arrows" ? 1000 : obj.type==="bullets" ? 3000 :obj.type==="thumbs" ? 2000 : 4000 : RVS.SLIDER.settings.nav[obj.type].style;

		var placeholders = RVS.nav[obj.type][RVS.SLIDER.settings.nav[obj.type].style].placeholders,
			preset = obj.env==="slide" ? undefined : RVS.SLIDER.settings.nav[obj.type].preset,
			presetvalues = preset!==undefined && preset!=="default" && RVS.nav[obj.type][RVS.SLIDER.settings.nav[obj.type].style].presets[preset]!==undefined ? RVS.nav[obj.type][RVS.SLIDER.settings.nav[obj.type].style].presets[preset].values : {},
			prefix = obj.env==="slide" ? "sl_"+obj.type+"_" : "sr_"+obj.type+"_",
			rpre = obj.env==="slide" ? RVS.S.slideId+".slide." : "settings.",
			sinp = obj.env==="slide" ? "slideinput" : "sliderinput",
			frag = RVS.F.cF();

		_.presets = _.presets===undefined ? {} : _.presets;

		var field,v,a,b,ipw,tmpi;

		for (var fields in placeholders) {
			if(!placeholders.hasOwnProperty(fields)) continue;
			field = placeholders[fields];
			v =  presetvalues[field]!==undefined ? presetvalues[field] : field.data;
			a = false;

			if (_.preset!=="default" && _.presets!==undefined && _.presets[fields]!==undefined) {
				v = _.presets[fields];
				a = _.presets[fields+"-def"];
			} else {
				_.presets[fields] = v;
				_.presets[fields+"-def"] = a;
			}

			b = _truefalse(a);
			a = b ? " checked='checked' "	: "";

			var row = RVS.F.cE({t:'row',cN:"directrow navpresetrow"}),
				onelong = RVS.F.cE({t:'onelong'}),
				oneshort = RVS.F.cE({t:'oneshort'}),
				labela = RVS.F.cE({t:'label_a'}),
				labeli = RVS.F.cE({t:'label_icon'}),
				input = RVS.F.cE({t:'input',type:'checkbox', id:prefix+fields+'-def', cN:'presetToCustom nav_'+obj.type+'_custom_defaults '+sinp,ds:{evt:"redrawNavigation", evtparam:obj.type, r : 'nav.'+obj.type+'.presets.'+fields+'-def'}});

			labela.innerText = field.title;
			input.checked = b;


			onelong.appendChild(labela);
			oneshort.appendChild(labeli);
			oneshort.appendChild(input);

			row.appendChild(onelong);
			row.appendChild(oneshort);

			switch (field.type) {
				case "font-family":
					var	select = RVS.F.cE({t:'select',cN:"navstyleinput searchbox tos2 presetToCustom "+classext, id:prefix+fields, ds:{evt:"redrawNavigation",evtparam:obj.type,theme:"minl120",r:rpre+'nav.'+obj.type+'.presets.'+fields}});
					select.value = v;
					onelong.appendChild(select);
					for (var fontindex in RVS.LIB.FONTS) if(RVS.LIB.FONTS.hasOwnProperty(fontindex) && RVS.LIB.FONTS[fontindex].label!=="Dont Show Me") select.appendChild(RVS.F.CO( RVS.LIB.FONTS[fontindex].label, RVS.LIB.FONTS[fontindex].label));
				break;
				case "icon":
				case "custom":
					var classext = "";
					try {if (RVS.F.isNumeric(parseInt(v,0))) classext=" valueduekeyboard";} catch(e) {}
					tmpi =  RVS.F.cE({t:'input',type:'text', id:prefix+fields, cN:'presetToCustom'+classext+' navstyleinput', ds:{evt:"redrawNavigation",evtparam:obj.type,r:rpre+'nav.'+obj.type+'.presets.'+fields}});
					tmpi.value = v;
					if (field.type==="icon") {
						ipw = RVS.F.cE({cN:"input_presets_wrap"});
						ipw.appendChild(RVS.F.cI({cN:'input_presets_dropdown',c:'more_vertical'}));
						let iinto = RVS.F.cE({cN:"input_presets",ds:{insertinto:prefix+fields}});
						iinto.innerHTML = iconlist;
						ipw.appendChild(iinto);
						ipw.prepend(tmpi);
						onelong.appendChild(ipw);
					} else onelong.appendChild(tmpi);
				break;
				case "color":
				case "color-rgba":
					if (v.indexOf(",")>=0 && v.indexOf("(")==-1)
						v = v.split(",").length>3 ? "rgba("+v+")" : "rgb("+v+")";
					row.className ="directrow";
					tmpi =  RVS.F.cE({t:'input',type:'text',id:prefix+fields, cN:'navstyleinput presetToCustom my-color-field',ds:{evt:"redrawNavigation",evtparam:obj.type,r:rpre+'nav.'+obj.type+'.presets.'+fields,visible:'true',editing:RVS.F.capitalise(obj.type)+' '+field.title,navcolor:"1",mode:"single"}});
					tmpi.name=fields;
					tmpi.value = v;
					onelong.appendChild(tmpi);
				break;
			}
			frag.appendChild(row);
		}
		requestAnimationFrame(function() {
			// RESET INPUTS
			container[0].innerHTML="";
			container[0].appendChild(frag);
			container.find('.navstyleinput.searchbox.tos2.presetToCustom ').ddTP("destroy").ddTP({placeholder:"Enter or Select"});

			RVS.F.initTpColorBoxes(container.find('.navstyleinput.presetToCustom.my-color-field'));
			RVS.F.initOnOff(container);
			if (container[0].innerHTML==="")
				cgroup.classList.add("hide_while_empty");
			else
				cgroup.classList.remove("hide_while_empty");
		});

		//REMOVE NOT NEEDED OBJECT ATTRIBUTES
		for (var i in _.presets) {
			if(!_.presets.hasOwnProperty(i)) continue;
			var key = i.replace("-def","");
			if (placeholders[key]===undefined)  delete _.presets[i];
		}
	};

	/*
	UPDATE THE PRESET LIST FOR THE SELECTED STYLE IN NAVIGATION
	*/
	function updateNavPresetList(obj) {
		var _apc = document.getElementById('sr_'+obj.type+'_style_preset');
		if (_apc===null || _apc==undefined) return;

		_apc.options.length = 0;

		var f = document.createDocumentFragment();
		f.appendChild(RVS.F.CO("custom","Custom"));
		f.appendChild(RVS.F.CO("default","Default"));


		if (obj.navobj==undefined || obj.navobj.settings===null) {
			_apc.appendChild(f);
			return;
		} else
		if (obj.navobj!==undefined) {
			var o;
			for (var preset in obj.navobj.presets) {
				if(!obj.navobj.presets.hasOwnProperty(preset)) continue;
				o = RVS.F.CO(preset,obj.navobj.presets[preset].name);
				//o.dataset.values = obj.navobj.presets[preset].values;
				f.appendChild(o);
			}
		}
		_apc.appendChild(f);

		if (obj.presetChange===true) RVS.SLIDER.settings.nav[obj.type].preset = "default";
		_apc.value = RVS.SLIDER.settings.nav[obj.type].preset;
		jQuery(_apc).ddTP({tags:true}).ddTP('change');

		RVS.F.updatePresetInputs(obj);
		RVS.F.updateSlideBasedNavigationStyle();
	}


	/*
	NAVIGATION STYLE CHANGED / INITIALISED
	*/
	RVS.F.updateNavStyleSelection = function(obj) {

		// UPDATE PRESETS FOR SELECTED NAVIGATION STYLES
		updateNavPresetList({init:obj.init, navobj:RVS.nav[obj.type][RVS.SLIDER.settings.nav[obj.type].style], type:obj.type, presetChange:obj.presetChange});

		// REDRAW NAVIGATION ELEMENT
		RVS.F.drawNavigation({	type:obj.type, init:obj.init, presetChange:obj.presetChange});
	};


	/*
	GET DEFAULT, CUSTOM OR PRESET CSS SETTINGS
	*/
	function manipulateNavCSS(obj) {
		var _ =  RVS.SLIDER.settings.nav[obj.type].presets,
			__ = RVS.SLIDER[RVS.S.slideId].slide.nav[obj.type].presets,
			skin = obj.skin===undefined ? RVS.nav[obj.type][obj.handle] : obj.skin,
			thecss = skin.css,
			parts = thecss.split('##'),
			sfor = [],
			counter = 0,
			ph = "";

		if (thecss==undefined) return "";
		for (var i=0;i<parts.length;i++) {
			if (counter==1) {
				ph=parts[i];
				counter=0;
				sfor.push(ph);
			} else {
				counter++;
			}
		}
		if (RVS.SLIDER[RVS.S.slideId].slide==undefined) return "";

		jQuery.each(sfor,function(i,sf) {
			var v="",
				w="";
			w = (skin.placeholders[sf]!==undefined && skin.placeholders[sf].data!==undefined) ? skin.placeholders[sf].data : "";
			v = sf===obj.attribute ? obj.color : obj.default===true ? skin.placeholders[sf]!==undefined ? skin.placeholders[sf].data : "" : __!==undefined && __[sf+"-def"] ? __[sf] : _[sf+"-def"] ? _[sf] : w;

			thecss = thecss.replace('##'+sf+'##',v);
		});
		_leftarrow.css({width:"",height:""});
		_rightarrow.css({width:"",height:""});
		return "<style id='"+obj.type+"_stylemanipualtion"+"'>"+thecss+"</style>";
	}

	/*
	GET NAV THUMB CSS
	*/
	function getNavCSS(obj) {
		if (RVS.SLIDER[obj.id]===undefined) return "";
		var navsrc = RVS.SLIDER[obj.id].slide.thumb.customThumbSrc,
			_css = {};
		if (navsrc===undefined || navsrc.length<3 || navsrc[navsrc.length-1]==="/")
				_css = RVS.F.getSlideBGDrawObj({id:obj.id});
			else
				_css = {"background-size":"cover", backgroundPosition:"center center", backgroundRepeat:"no-repeat",backgroundImage:'url('+navsrc+')'};
		return _css;
	}

	/*
	DRAW NAVIGATION
	*/
	RVS.F.drawNavigation = function(obj) {
		if (obj===undefined || RVS.SLIDER.slideIDs.length==0) return;
		var _ = RVS.SLIDER.settings.nav[obj.type],
			_STYLE = obj.style!==undefined ? obj.style : _.style,
			navobj = RVS.nav[obj.type][_STYLE],
			nextslideid = RVS.SLIDER.slideIDs.length>0 ? RVS.SLIDER.slideIDs[1] : RVS.SLIDER.slideIDs[0],
			prevslideid = RVS.SLIDER.slideIDs[RVS.SLIDER.slideIDs.length-1];

		if (navobj!==undefined && navobj.markup!==undefined) {
			var cname = RVS.F.sanitize_input(navobj.handle.toLowerCase()),
				thecss = navobj.css!==undefined ? manipulateNavCSS({color:obj.color, attribute:obj.attribute, type:obj.type, handle:_STYLE ,default:obj.default}) : "";

			// BUILD BASIC HTML OF ARROWS
			if (obj.type==="arrows") {

				_leftarrow.attr('class','aable markable tparrows tp-leftarrow '+cname);
				_rightarrow.attr('class','aable markable tparrows tp-rightarrow '+cname);

				var markupright,
					markupleft;

				markupright = markupleft = navobj.markup;
				var thumbleft = getNavCSS({id:prevslideid}),
					thumbright = getNavCSS({id:nextslideid});

				markupright = markupright.replace('##title##',(RVS.SLIDER[nextslideid]===undefined ? "Title" : RVS.SLIDER[nextslideid].slide.title));
				markupleft = markupleft.replace('##title##',(RVS.SLIDER[prevslideid]===undefined ? "Title" : RVS.SLIDER[prevslideid].slide.title));

				_leftarrow.html(thecss+markupleft);
				_rightarrow.html(markupright);
				_leftarrow.find('.tp-arr-imgholder').css(thumbleft);
				_leftarrow.find('.tp-arr-imgholder').attr('id','arrows_'+prevslideid);
				_rightarrow.find('.tp-arr-imgholder').css(thumbright);
				_rightarrow.find('.tp-arr-imgholder').attr('id','arrows_'+nextslideid);

				RVS.F.dragMe({element:_leftarrow,
						input:{x:jQuery("#nav_arrows_left_offsetx"),y:jQuery("#nav_arrows_left_offsety")},
						updateInput:true,
						attributeRoot:"settings.",
						callEvent:"sliderNavPositionUpdate",
						callEventParam:"arrows",
						forms:["*navlayout*#form_nav_arrows:#sr_na_arr_12"]
					});

				RVS.F.dragMe({element:_rightarrow,
						input:{x:jQuery("#nav_arrows_right_offsetx"),y:jQuery("#nav_arrows_right_offsety")},
						updateInput:true,
						attributeRoot:"settings.",
						callEvent:"sliderNavPositionUpdate",
						callEventParam:"arrows",
						forms:["*navlayout*#form_nav_arrows:#sr_na_arr_13"]
					});

			} else
			if (obj.type==="bullets") {
				_bullets.data('cname',cname);
				_bullets.attr('class','aable markable tp-bullets '+cname+' nav-dir-'+_.direction+' nav-pos-ver-'+_.vertical+' nav-pos-hor-'+_.horizontal);
				_bullets.html(thecss);

				var markup = navobj.markup;
				for (var i=0;i<=RVS.SLIDER.slideIDs.length-1;i++) {
					var slideid = RVS.SLIDER.slideIDs[i],
						newmarkup = markup.replace('##title##',RVS.SLIDER[slideid].slide.title),
						selectedelem = i==0 ? "selected" : "",
						bullet = jQuery('<div class="tp-bullet '+selectedelem+'">'+newmarkup+'</div>');
					bullet.find('.tp-bullet-image').css(getNavCSS({id:slideid})).attr('id','bullets'+slideid);

					_bullets.append(bullet);
				}
				RVS.F.dragMe({element:_bullets,
					input:{x:jQuery("#nav_bullets_offsetx"),y:jQuery("#nav_bullets_offsety")},
					updateInput:true,
					attributeRoot:"settings.",
					callEvent:"sliderNavPositionUpdate",
					callEventParam:"bullets",
					forms:["*navlayout*form_nav_bullets:#sr_na_bul_11"]
				});
			}
			else
			if (obj.type==="tabs" || obj.type==="thumbs") {
				if (obj.presetChange) {
					_.width = navobj.dim.width!==undefined ? navobj.dim.width : _.width;
					_.height = navobj.dim.height!==undefined ? navobj.dim.height : _.height;
					jQuery('#nav_'+obj.type+'_width').val(_.width);
					jQuery('#nav_'+obj.type+'_height').val(_.height);
				} else {
					tpGS.gsap.set(_thumbstabs[obj.type].self,{background:window.RSColor.get(_.wrapperColor)});
				}

				_thumbstabs[obj.type].self.data('cname',cname);
				_thumbstabs[obj.type].inner.html(thecss);
				var markup = navobj.markup;
				for (var i=0;i<=Math.min(_.amount,RVS.SLIDER.slideIDs.length-1);i++) {

					var slideid = RVS.SLIDER.slideIDs[i],
						newmarkup = markup.replace('##title##',RVS.SLIDER[slideid].slide.title),
						selectedelem = i==0 ? "selected" : "";
					for (var pars=0;pars<10;pars++) {
						var parval = (RVS.SLIDER[slideid].slide.info.params[pars]!==undefined) ? RVS.SLIDER[slideid].slide.info.params[pars].val : '';
						if (parval!==undefined && parval.length>0)
							newmarkup = newmarkup.replace('##param'+pars+'##',parval);
					}
					var tab = jQuery('<div class="'+_thumbstabs[obj.type].single+' '+selectedelem+'">'+newmarkup+'</div>');
					tab.find('.'+_thumbstabs[obj.type].single+'-image').css(getNavCSS({id:slideid})).attr('id',obj.type+'_'+slideid);

					_thumbstabs[obj.type].inner.append(tab);
				}
				var callform = obj.type=="tabs" ? "*navlayout*#form_nav_tabs:#sr_na_tab_11" : "*navlayout*#form_nav_thumbs:#sr_na_thumb_11";
				RVS.F.dragMe({element:_thumbstabs[obj.type].self,
					input:{x:jQuery("#nav_"+obj.type+"_offsetx"),y:jQuery("#nav_"+obj.type+"_offsety")},
					updateInput:true,
					attributeRoot:"settings.",
					callEvent:"sliderNavPositionUpdate",
					callEventParam:obj.type,
					forms:[callform]
				});
			}
		}


		RVS.F.sliderNavPositionUpdate({type:obj.type});
	};



	/*
	UPDATE THE POSITION AND VISIBILITY OF THE NAVIGATION ELEMENTS
	*/
	function putNavObjInPos(el,_) {
		if (_===undefined) return;
		var cOffset = {x:0, y:0},
			temp = _.align==="slider" ?
				{	x:parseInt(_.offsetX,0) + (_.horizontal==="left" ? cOffset.x : 0),
					y:parseInt(_.offsetY,0) + (_.vertical==="top" ? cOffset.y : 0)} :
				{	x:parseInt(_.offsetX,0) + (_.horizontal==="right" ? RVS.S.layer_wrap_offset.xr : RVS.S.layer_wrap_offset.x),
					y:parseInt(_.offsetY,0) + (_.vertical==="top" ? RVS.S.layer_wrap_offset.y : RVS.S.layer_wrap_offset.y - cOffset.y)
				},
			animobj = {marginLeft:"0px",x:"0%",left:temp.x+"px",right:"auto", marginTop:"0px",y:"0%",top:temp.y+"px",bottom:"auto"};


		RVS.S.dim_offsets = RVS.F.sliderDimensionOffsets();

		switch (_.horizontal) {
			case "right":
				animobj.left = "auto";
				animobj.right = (parseInt(temp.x,0))+"px";
			break;
			case "center":
				animobj.marginLeft = ((cOffset.x/2) + parseInt(_.offsetX,0))+"px";
				animobj.x = "-50%";
				animobj.left = "50%";
			break;
		}
		switch (_.vertical) {
			case "bottom":
				animobj.top = "auto";
				animobj.bottom = temp.y+"px";
			break;
			case "center":
				var extraoffset = (RVS.S.dim_offsets.carouseltop/2 + RVS.S.dim_offsets.navtop/2) - (RVS.S.dim_offsets.carouselbottom/2 + RVS.S.dim_offsets.navbottom/2);
				animobj.marginTop = (cOffset.y/2) + (parseInt(_.offsetY,0)+parseInt(extraoffset,0))+"px";
				animobj.y = "-50%";
				animobj.top = "50%";
			break;
		}
		if (_.spanWrapper===true) {
			switch(_.direction) {
				case "horizontal":
					animobj.marginLeft="0px";
					animobj.x="0%";
					animobj.left="0%";
				break;
				case "vertical":
					animobj.marginTop="0px";
					animobj.y="0%";
					animobj.top="0px";
				break;
			}
		}
		tpGS.gsap.set(el,animobj);
	}


	RVS.F.sliderNavPositionUpdate = function(obj) {
		requestAnimationFrame(function() {
			RVS.F.sliderNavPositionUpdateRAF(obj);
		});
	}

	RVS.F.sliderNavPositionUpdateRAF = function(obj) {

		var _ = RVS.SLIDER.settings.nav[obj.type];
		switch (obj.type) {
			case "arrows":
				if (!_.set || RVS.SLIDER.settings.type==="hero") {
					_leftarrow.hide();
					_rightarrow.hide();
				}
				else {
					_leftarrow.show();
					_rightarrow.show();
				}
			break;
			case "bullets":
				if (!_.set || RVS.SLIDER.settings.type==="hero")
					_bullets.hide();
				else
					_bullets.show();
			break;
			case "tabs":
			case "thumbs":
				if (!_.set || RVS.SLIDER.settings.type==="hero")
					_thumbstabs[obj.type].self.hide();
				else
					_thumbstabs[obj.type].self.show();
			break;
		}
		if (_.set===false || RVS.SLIDER.settings.type==="hero") {

			return false;

		}


		if (obj.type==="arrows") {
			putNavObjInPos(_leftarrow,_.left);
			putNavObjInPos(_rightarrow,_.right);
		} else
		if (obj.type==="bullets") {
			//UPDATE CLASSES
			_bullets.attr('class','aable markable tp-bullets '+_bullets.data('cname')+' nav-dir-'+_.direction+' nav-pos-ver-'+_.vertical+' nav-pos-hor-'+_.horizontal);
			// SET BULLET SPACES AND POSITION
			_bullets.find('.tp-bullet').each(function(i) {
				var b = jQuery(this),
					am = RVS.SLIDER.slideIDs.length,
					w = b.outerWidth()+parseInt((_.space===undefined? 0:_.space),0),
					h = b.outerHeight()+parseInt((_.space===undefined? 0:_.space),0);

				if (_.direction==="vertical") {

					b.css({top:((i)*h)+"px", left:"0px"});
					_bullets.css({height:(((am-1)*h) + b.outerHeight()),width:b.outerWidth()});
				}
				else {

					b.css({left:((i)*w)+"px", top:"0px"});
					_bullets.css({width:(((am-1)*w) + b.outerWidth()),height:b.outerHeight()});
				}
			});
			putNavObjInPos(_bullets,_);
		} else
		if (obj.type==="tabs" || obj.type==="thumbs") {


			//UPDATE CLASSES
			var am = Math.min(_.amount,RVS.SLIDER.slideIDs.length),
				w = parseInt(_.width,0)+parseInt(_.space,0),
				h = parseInt(_.height,0)+parseInt(_.space,0);
			_.width = parseInt(_.width);
			_.height = parseInt(_.height);
			_thumbstabs[obj.type].self.attr('class','aable markable '+_thumbstabs[obj.type].single+'s '+_thumbstabs[obj.type].self.data('cname')+' nav-dir-'+_.direction+' nav-pos-ver-'+_.vertical+' nav-pos-hor-'+_.horizontal);
			tpGS.gsap.set(_thumbstabs[obj.type].self,{padding:_.padding});
			_.padding = parseInt(_.padding,0);


			// SET BULLET SPACES AND POSITION
			_thumbstabs[obj.type].inner.find('.'+_thumbstabs[obj.type].single).each(function(i) {
				if (_.direction==="vertical")
					tpGS.gsap.set(this,{top:((i)*h)+"px",left:"0px",width:_.width+"px",height:_.height+"px"});
				else
					tpGS.gsap.set(this,{left:((i)*w)+"px", top:"0px",width:_.width+"px",height:_.height+"px"});
			});

			var maxw = _.direction==="horizontal" ? (_.width * am) + (_.space*(am-1)) + parseInt(2*_.mhoffset) : _.width + parseInt(2*_.mhoffset),
				maxh = _.direction==="horizontal" ? parseInt(_.height) + parseInt(2*_.mvoffset): (_.height * am) + (_.space*(am-1))+parseInt(2*_.mvoffset),
				wrapcssobj = {width:maxw+"px", height:maxh+"px",overwrite:"auto"},
				maskcssobj = {padding:_.mvoffset+"px "+_.mhoffset+"px", top:"auto", left:"auto", bottom:"auto", marginTop:"0px", marginBottom:"0px",right:"auto", y:"0%", x:"0px",width:maxw+"px",height:maxh+"px",overflow:"hidden",position:"relative",overwrite:"auto",marginLeft:"auto",marginRight:"auto"};
				/* innecssobj = {y:"0px",x:"0px",position:"relative"}; */

			if (_.spanWrapper===true) {
				switch (_.direction) {
					case "horizontal":
						wrapcssobj.width = RVS.S.ulDIM.width-(parseInt(_.padding,0)*2)+"px";
						maskcssobj.x = _.offsetX;
						maskcssobj.marginLeft = _.horizontal==="center" ? "auto" : "0px";
						maskcssobj.marginRight = _.horizontal==="center" ? "auto" : "0px";
						if (_.horizontal==="right") {
							maskcssobj.right=_.padding+"px";
							maskcssobj.position="absolute";
						}
						if (_.innerOuter==="outer-bottom")
							RVS.S.navOffset[obj.type].bottom = parseInt(_.height,0) + (2*_.padding);
						else
						if (_.innerOuter==="outer-top")
							RVS.S.navOffset[obj.type].top = parseInt(_.height,0) + (2*_.padding);
					break;
					case "vertical":
						wrapcssobj.height =(RVS.S.ulDIM.height-(parseInt(_.padding,0)*2))+"px";
						maskcssobj.marginTop = _.vertical!=="bottom" ? _.offsetY+"px" : "0px";
						maskcssobj.marginBottom = _.vertical==="bottom" ? -1*_.offsetY+"px" : "0px";
						if (_.vertical==="center") {
							maskcssobj.top = "50%";
							maskcssobj.y = "-50%";
							maskcssobj.position = "absolute";
						} else
						if (_.vertical==="bottom") {
							maskcssobj.top = "auto";
							maskcssobj.bottom = _.padding+"px";
							maskcssobj.position = "absolute";
						}
						if (_.innerOuter==="outer-left")
							RVS.S.navOffset[obj.type].left = parseInt(_.width,0) + (2*_.padding);
						else
						if (_.innerOuter==="outer-right")
							RVS.S.navOffset[obj.type].right = parseInt(_.width,0) + (2*_.padding);
					break;
				}
			}
			tpGS.gsap.set(_thumbstabs[obj.type].inner,{position:'relative'});
			tpGS.gsap.set(_thumbstabs[obj.type].self,wrapcssobj);
			tpGS.gsap.set(_thumbstabs[obj.type].mask,maskcssobj);
			putNavObjInPos(_thumbstabs[obj.type].self,_);
		}

	};

	function setNavInnerOuter(obj) {
		var _=RVS.SLIDER.settings.nav[obj.type],
			hor = jQuery('#sr_'+obj.type+'hor'),
			ver = jQuery('#sr_'+obj.type+'ver'),
			ori = 'sr_'+obj.type+'direction';

		jQuery('#sr_'+obj.type+'_halignwrap').show();
		jQuery('#sr_'+obj.type+'_valignwrap').show();

		switch (_.innerOuter) {
			case "inner":
				jQuery('#'+obj.type+'pos_selector_center-center').show();
				jQuery('#'+obj.type+'pos_selector_left-center').show();
				jQuery('#'+obj.type+'pos_selector_right-center').show();
				jQuery('#'+obj.type+'pos_selector_center-top').show();
				jQuery('#'+obj.type+'pos_selector_center-bottom').show();
			break;
			case "outer-vertical":
				RVS.F.setS2Option({select:hor, selectValue:"left"});
				RVS.F.setS2Option({select:ver, selectValue:"center"});
				RVS.F.setRadio({radio:ori, radioValue:"vertical", change:true});
				jQuery('#'+obj.type+'pos_selector_center-top').hide();
				jQuery('#'+obj.type+'pos_selector_center-center').hide();
				jQuery('#'+obj.type+'pos_selector_center-bottom').hide();
				jQuery('#'+obj.type+'pos_selector_left-center').show();
				jQuery('#'+obj.type+'pos_selector_right-center').show();
				jQuery('#sr_'+obj.type+'_halignwrap').hide();
			break;

			case "outer-horizontal":
				RVS.F.setS2Option({select:hor, selectValue:"center"});
				RVS.F.setS2Option({select:ver, selectValue:"bottom"});
				RVS.F.setRadio({radio:ori, radioValue:"horizontal", change:true});


				jQuery('#'+obj.type+'pos_selector_left-center').hide();
				jQuery('#'+obj.type+'pos_selector_center-center').hide();
				jQuery('#'+obj.type+'pos_selector_right-center').hide();
				jQuery('#'+obj.type+'pos_selector_center-top').show();
				jQuery('#'+obj.type+'pos_selector_center-bottom').show();
				jQuery('#sr_'+obj.type+'_valignwrap').hide();
			break;
		}
		hor.trigger('change');
		ver.trigger('change');
	}


	/*
	FILE AND AJAX HANDLINGS
	*/

	/*
	UPDATE NAV OBJECT STRUCTURE
	*/
	/*
	function updateNavObject(obj,style,mode) {
		var found = false,todelete,foundnav;

		for (var nav in RVS.navigations) {
			if(!RVS.navigations.hasOwnProperty(nav)) continue;
			if (RVS.navigations[nav].handle===style) {
				RVS.navigations[nav].settings.presets = RVS.navigations[nav].settings.presets===undefined ? {} : RVS.navigations[nav].settings.presets;
				for (var preset in RVS.navigations[nav].settings.presets) {
					if(!RVS.navigations[nav].settings.presets.hasOwnProperty(preset)) continue;
					if (RVS.navigations[nav].settings.presets[preset].handle === obj.handle) {
						found = true;
						foundnav = nav;
						if (mode==="save")
							RVS.navigations[nav].settings.presets[preset] = RVS.F.safeExtend({},obj,true);
						else
							todelete = preset;
					}

				}
				if (found===false && mode==="save") {
					found=true;
					RVS.navigations[nav].settings.presets.push(obj);
				}
			}
		}
		if (mode!=="save") RVS.navigations[foundnav].settings.presets.splice(todelete,1);
	}
	*/

	/*
	SAVE NAVIGATION PRESETS
	*/
	RVS.F.saveDeleteNavPreset = function(_) {

		var onp = jQuery('#sr_'+_.type+'_style_preset'),
			name = onp.val(),
			presethandle = RVS.F.sanitize_input(name),
			tosave = {},
			settings = RVS.SLIDER.settings.nav[_.type],
			navobj =  RVS.nav[_.type][settings.style],
			request = _.mode==="save" ? "create_navigation_preset" : "delete_navigation_preset";

		if (name==="" || name==="null" || name===null) {
			RVS.F.showErrorMessage(RVS_LANG.no_preset_name);
			return;
		}
		if (_.mode==="save") {
			for (var attr in settings.presets) {
				if(!settings.presets.hasOwnProperty(attr)) continue;
				var v = settings.presets[attr];
				if (attr.indexOf("-def")>0 && (v==="on" || v===true || v==='true')) {
					var real = attr.replace("-def","");
					tosave[real] = settings.presets[real];
					tosave[attr] = true;
				}
			}
			if(jQuery.isEmptyObject(tosave)){
				RVS.F.showErrorMessage(RVS_LANG.no_nav_changes_done);
				return;
			}
		} else
		if (_.mode==="delete") delete navobj.presets[presethandle];

		var data = _.mode==="save" ? { navigation: settings.style, name: name,handle: presethandle,type: _.type, do_overwrite: true,values: tosave} : {style_handle: settings.style,handle: presethandle,type: _.type};
		navobj.presets = navobj.presets===undefined  || navobj.presets==="" ? {} : navobj.presets;
		if (_.mode==="save") navobj.presets[name] = { name:name, values:tosave};
		RVS.F.ajaxRequest(request, data, function(response){});

		// UPDATE PRESETS FOR SELECTED NAVIGATION STYLES LIVE
		updateNavPresetList({init:false,navobj:navobj,type:_.type,presetChange:true});
		settings.preset = _.mode==="save" ? presethandle : "default";



		RVS.F.setNavPresetValues({type:_.type});
	};


	/*
	LOCAL LISTENERS
	*/
	function initLocalListeners() {
		// LISTENER ON NAVIGATION STYLE AND PRESET CHANGES

		RVS.DOC.on('openNavigationEditor',RVS.F.openNavigationEditor);
		RVS.DOC.on('sliderNavUpdate',function(e,ep) {RVS.F.updateNavStyleSelection({init:false,type:ep,presetChange:true});});
		RVS.DOC.on('redrawNavigation',function(e,ep) {RVS.F.drawNavigation({type:ep,init:true}); });
		RVS.DOC.on('sliderNavPreset',function(e,ep) {RVS.F.setNavPresetValues({type:ep});});
		RVS.DOC.on('sliderNavPositionUpdate',function(e,ep) {RVS.F.sliderNavPositionUpdate({type:ep}); /*setSlidesDimension(false);*/});
		RVS.DOC.on('navinnerouter',function(e,ep) {setNavInnerOuter({type:ep});});

		// SAVE NAVIGATON PRESETS LISTENER
		RVS.DOC.on('saveNavPreset',function(e,ep) {RVS.F.saveDeleteNavPreset({mode:"save", type:ep}); });

		// DELETE NAVIGATON PRESETS LISTENER
		RVS.DOC.on('deleteNavPreset',function(e,ep) {RVS.F.saveDeleteNavPreset({mode:"delete", type:ep}); });


		// ARROWS QUICK SHOW THE SELECTED ARROW STYLE
		RVS.DOC.on('mouseenter','#ddTP-sr_arrows_style-results li',function() {
			var fourdigit = this.id.split('-result-')[1].split("-")[0],
				key = this.id.split(fourdigit+"-")[1];
			RVS.F.drawNavigation({	type:"arrows", style:key, init:false, presetChange:true, default:true});
		});

		// BULLETS QUICK SHOW THE SELECTED ARROW STYLE
		RVS.DOC.on('mouseenter','#ddTP-sr_bullets_style-results li',function() {
			var fourdigit = this.id.split('-result-')[1].split("-")[0],
				key = this.id.split(fourdigit+"-")[1];
			RVS.F.drawNavigation({	type:"bullets", style:key, init:false, presetChange:true, default:true});
		});

		// THUMBS QUICK SHOW THE SELECTED ARROW STYLE
		RVS.DOC.on('mouseenter','#ddTP-sr_thumbs_style-results li',function() {
			var fourdigit = this.id.split('-result-')[1].split("-")[0],
				key = this.id.split(fourdigit+"-")[1];
			RVS.F.drawNavigation({	type:"thumbs", style:key, init:false, presetChange:true, default:true});
		});



		// ARROWS QUICK SHOW THE SELECTED ARROW STYLE
		RVS.DOC.on('mouseenter','#ddTP-sr_tabs_style-results li',function() {
			var fourdigit = this.id.split('-result-')[1].split("-")[0],
				key = this.id.split(fourdigit+"-")[1];
			RVS.F.drawNavigation({	type:"tabs", style:key, init:false, presetChange:true, default:true});
		});

        RVS.DOC.on('ddTP:open', '.sr_nav_style_tos',function() {
			window.shortcachenav = RVS.F.safeExtend(true,{},RVS.SLIDER.settings.nav[this.dataset.evtparam]);
			window.shortnavindex = this.selectedIndex;
		});

        RVS.DOC.on('ddTP:close','.sr_nav_style_tos',function(e) {
			 if (window.shortnavindex===this.selectedIndex) {
			 	RVS.SLIDER.settings.nav[this.dataset.evtparam] = RVS.F.safeExtend(true,{},window.shortcachenav);
			 	RVS.F.drawNavigation({	type:this.dataset.evtparam, init:false});
			 } else {
				RVS.F.drawNavigation({	type:this.dataset.evtparam, init:false});
			}
		});

		RVS.DOC.on('click','.nav_preseticon_pick', function() {
			var p = this.parentNode.parentNode;
			jQuery("#"+p.dataset.insertinto).val(this.dataset.content).trigger("change");
		});

	}

	RVS.F.updateSlideBasedNavigationStyle = function() {
		// SHOW NAVIGATION ONLY IF NEEDED
		if (RVS.F.type!=="hero") {
			// ReDraw the Navigation with Slide related Settings
			if (RVS.SLIDER.settings.nav.arrows.set) RVS.F.updatePresetInputs({type:"arrows",env:"slide"});
			if (RVS.SLIDER.settings.nav.bullets.set) RVS.F.updatePresetInputs({type:"bullets",env:"slide"});
			if (RVS.SLIDER.settings.nav.thumbs.set) RVS.F.updatePresetInputs({type:"thumbs",env:"slide"});
			if (RVS.SLIDER.settings.nav.tabs.set) RVS.F.updatePresetInputs({type:"tabs",env:"slide"});
		}
	};



	/****************************************
			MIGRATION RUTINES
	******************************************/
	RVS.F.createNewNavigation = function(t) {
		var n =  {
			id : "new_"+RVS.nav.currentMaxid,
			name : "New "+t+" Navigation",
			css : "",
			markup : "",
			dim :   {width:160, height:160},
			placeholders:{},
			presets:{},
			type:t,
			factory:false,
			handle:"newnavclass_"+RVS.nav.currentMaxid,
		};

		return n;
	};



	RVS.F.migrateNavigation = function(obj) {
		RVS.nav = obj;
	};

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
