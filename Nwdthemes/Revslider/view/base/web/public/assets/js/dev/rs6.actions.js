/********************************************
 * REVOLUTION EXTENSION - ACTIONS
 * @Date: (20.06.2020)
 * @requires rs6.main.js
 * @author ThemePunch
*********************************************/
(function($,undefined) {
"use strict";
var version = "6.2.14";

jQuery.fn.revolution = jQuery.fn.revolution || {};
var _R = jQuery.fn.revolution;

///////////////////////////////////////////
// 	EXTENDED FUNCTIONS AVAILABLE GLOBAL  //
///////////////////////////////////////////
jQuery.extend(true,_R, {
	checkActions : function(layer,id) {
		if (layer===undefined) moduleEnterLeaveActions(id); else checkActions_intern(layer,id);
	},

	delayer : function(id,wait,caller) {
		_R[id].timeStamps = _R[id].timeStamps===undefined ? {} : _R[id].timeStamps;
		var dt = new Date().getTime(),
			delay =  _R[id].timeStamps[caller]===undefined ? parseInt(wait)+100 : dt- _R[id].timeStamps[caller];
		var ret = parseInt(delay)>wait ? true : false;
		if (ret) _R[id].timeStamps[caller] = dt;
		return ret;
	},

	getURLDetails : function(obj) {
		obj = obj===undefined ? {} : obj;
		obj.url = obj.url===undefined ? window.location.href : obj.url;
		obj.url = obj.url.replace("www","");
		obj.protocol = obj.url.indexOf('http://')===0 ? 'http://' : obj.url.indexOf('https://')===0 ? 'https://' : obj.url.indexOf('//')===0 ? '//' : 'relative';
		var hostpath = obj.url.replace("https://","");
		hostpath = hostpath.replace("http://","");
		if (obj.protocol==="relative") hostpath = hostpath.replace("//","");
		hostpath = hostpath.split("#");
		obj.anchor = (obj.anchor===undefined || obj.anchor=="" || obj.anchor.length==0) && hostpath.length>1 ? hostpath[1] : obj.anchor===undefined ? "" : obj.anchor.replace("#","");
		obj.anchor = obj.anchor.split("?");
		// Query Strings should come first, however this may ignored so check both ways !
		obj.queries = hostpath[0].split("?");
		obj.queries = obj.queries.length>1 ? obj.queries[1] : "";
		obj.queries = obj.queries.length>1 ? obj.queries[1] : obj.anchor.length>1 ? obj.anchor[1] : obj.queries;
		obj.anchor = obj.anchor[0];
		hostpath = hostpath[0];
		var hostpath_array = hostpath.split("/");
		var temp = hostpath.split("/");
		obj.host = temp[0];
		temp.splice(0,1);
		obj.path = "/"+temp.join("/");
		if (obj.path[obj.path.length-1]=="/") obj.path = obj.path.slice(0,-1);
		obj.origin = obj.protocol!=="relative" ?  obj.protocol+obj.host : window.location.origin.replace("www","")+window.location.pathname;
		obj.hash = (obj.queries!=="" && obj.queries!==undefined ? "?"+obj.queries  : "") + (obj.anchor!=="" && obj.anchor!==undefined ? "#"+obj.anchor  : "");
		return obj;
	},

	scrollToId : function(obj) {
		/* OBJ Attributes:
		 	id : Slider Id
		 	offset : Scroll Offset
		 	action : called Action
		 	anchor : the Targeted ID where to Scroll
		 	hash : Change Hash in URL after animation
		 	speed : Animation Speed
		 	event : Animation Ease
		 */

		var off= obj.action==="scrollbelow" ? (getOffContH(_R[obj.id].fullScreenOffsetContainer) || 0) - (parseInt(obj.offset,0) || 0) || 0 : 0-(parseInt(obj.offset,0) || 0),
			c =  obj.action==="scrollbelow" ? _R[obj.id].c : jQuery('#'+obj.anchor),
			ctop = c.length>0 ? c.offset().top : 0,
			sobj = {_y: _R[obj.id].modal.useAsModal ? _R[obj.id].cpar[0].scrollTop :  (window.pageYOffset!==document.documentElement.scrollTop) ? window.pageYOffset!==0 ? window.pageYOffset :document.documentElement.scrollTop : window.pageYOffset };

		ctop += obj.action==="scrollbelow" ? _R[obj.id].sbtimeline.fixed ? _R[obj.id].cpar.parent().height() + _R[obj.id].fullScreenOffsetResult : jQuery(_R[obj.id].slides[0]).height() : 0;

		tpGS.gsap.to(sobj,obj.speed/1000,{_y:(ctop-off), ease:obj.ease,
			onUpdate:function() { if (_R[obj.id].modal.useAsModal) _R[obj.id].cpar.scrollTop(sobj._y); else _R.document.scrollTop(sobj._y); /* document.documentElement.scrollTop = sobj._y*/},
			onComplete:function() {if (obj.hash!==undefined) window.location.hash = obj.hash}
		});
	}
});

//////////////////////////////////////////
//	-	INITIALISATION OF ACTIONS 	-	//
//////////////////////////////////////////
var moduleEnterLeaveActions = function(id) {
	if (!_R[id].moduleActionsPrepared && _R[id].c[0].getElementsByClassName('rs-on-sh').length>0) {
		_R[id].c.on('tp-mouseenter',function() {
			_R[id].mouseoncontainer = true;
			var key = _R[id].pr_next_key!==undefined ? _R[id].pr_next_key : _R[id].pr_processing_key!==undefined ? _R[id].pr_processing_key : _R[id].pr_active_key!==undefined ? _R[id].pr_active_key : _R[id].pr_next_key,
				li;
			if (key==="none" || key===undefined) return;
			key = _R.gA(_R[id].slides[key],"key");
			if (key!==undefined && _R[id].layers[key]) for (li in _R[id].layers[key]) if (_R[id].layers[key][li].className.indexOf("rs-on-sh")>=0) _R.renderLayerAnimation({layer:jQuery(_R[id].layers[key][li]), frame:"frame_1", mode:"trigger", id:id});
			for (li in _R[id].layers.static) if (_R[id].layers.static[li].className.indexOf("rs-on-sh")>=0) _R.renderLayerAnimation({layer:jQuery(_R[id].layers.static[li]), frame:"frame_1", mode:"trigger", id:id});
		});

		_R[id].c.on('tp-mouseleft',function() {
			_R[id].mouseoncontainer = true;
			var key = _R[id].pr_next_key!==undefined ? _R[id].pr_next_key : _R[id].pr_processing_key!==undefined ? _R[id].pr_processing_key : _R[id].pr_active_key!==undefined ? _R[id].pr_active_key : _R[id].pr_next_key,
				li;
			if (key==="none" || key===undefined) return;
			key = _R.gA(_R[id].slides[key],"key");
			if (key!==undefined && _R[id].layers[key]) for (li in _R[id].layers[key]) if (_R[id].layers[key][li].className.indexOf("rs-on-sh")>=0) _R.renderLayerAnimation({layer:jQuery(_R[id].layers[key][li]), frame:"frame_999", mode:"trigger", id:id});
			for (li in _R[id].layers.static) if (_R[id].layers.static[li].className.indexOf("rs-on-sh")>=0) _R.renderLayerAnimation({layer:jQuery(_R[id].layers.static[li]), frame:"frame_999", mode:"trigger", id:id});
		});
	}
	_R[id].moduleActionsPrepared = true;
}

var checkActions_intern = function(layer,id) {
	var actions = _R.gA(layer[0],"actions"),
		_L = layer.data();
	actions = actions.split("||");
	layer.addClass("rs-waction");
	_L.events = _L.events===undefined ? [] : _L.events;
	_R[id].lastMouseDown = {};
	//GET THROUGH THE EVENTS AND COLLECT INFORMATIONS
	for (var ei in actions) {
		if (!actions.hasOwnProperty(ei)) continue;
		var event = getEventParams(actions[ei].split(";"));
		_L.events.push(event);
		if (event.on==="click") layer[0].classList.add('rs-wclickaction');
		// LISTEN TO ESC TO EXIT FROM FULLSCREEN
		if (!_R[id].fullscreen_esclistener && (event.action=="exitfullscreen" || event.action=="togglefullscreen")) {
			_R.document.keyup(function(e) {
				 if (e.keyCode == 27 && jQuery('#rs-go-fullscreen').length>0) layer.trigger(event.on);
			});
			_R[id].fullscreen_esclistener = true;
		}

		var targetlayer = event.layer == "backgroundvideo" ? jQuery("rs-bgvideo") : event.layer == "firstvideo" ? jQuery("rs-slide").find('.rs-layer-video') : jQuery("#"+event.layer);

		// NO NEED EXTRA TOGGLE CLASS HANDLING
		if (jQuery.inArray(event.action,["toggleslider","toggle_mute_video","toggle_global_mute_video","togglefullscreen"])!=-1) _L._togglelisteners=true;

		// COLLECT ALL TOGGLE TRIGGER TO CONNECT THEM WITH TRIGGERED LAYER
		switch (event.action) {
			case "togglevideo": jQuery.each(targetlayer,function() { updateToggleByList(jQuery(this),'videotoggledby', layer[0].id);}); break;
			case "togglelayer": jQuery.each(targetlayer,function() {

				updateToggleByList(jQuery(this),'layertoggledby', layer[0].id); jQuery(this).data('triggered_startstatus',event.togglestate);
			});break;
			case "toggle_global_mute_video":
			case "toggle_mute_video":
				jQuery.each(targetlayer,function() { updateToggleByList(jQuery(this),'videomutetoggledby', layer[0].id);});
			break;
			case "toggleslider":
				if (_R[id].slidertoggledby == undefined) _R[id].slidertoggledby = [];
				_R[id].slidertoggledby.push(layer[0].id);
			break;
			case "togglefullscreen":
				if (_R[id].fullscreentoggledby == undefined) _R[id].fullscreentoggledby = [];
				_R[id].fullscreentoggledby.push(layer[0].id);
			break;
		}
	}


	_R[id].actionsPrepared = true;
	layer.on('mousedown', function(e){
		if(e.touches) e = e.touches[0];
		_R[id].lastMouseDown.pageX = e.pageX;
		_R[id].lastMouseDown.pageY = e.pageY;
	});

	layer.on("click mouseenter mouseleave",function(e) {

		if(e.type === 'click'){
			var evt = e.touches ? e.touches[0] : e;
			if(Math.abs(evt.pageX - _R[id].lastMouseDown.pageX) > 5 || Math.abs(evt.pageY - _R[id].lastMouseDown.pageY) > 5) return;
		}


		for (var i in _L.events) {
			if (!_L.events.hasOwnProperty(i)) continue;
			if (_L.events[i].on!==e.type) continue;
			var event = _L.events[i];
			if (event.repeat!==undefined && event.repeat>0 && !_R.delayer(id,event.repeat*1000,_L.c[0].id+"_"+event.action)) continue;

			if (event.on==="click" && layer.hasClass("tp-temporarydisabled")) return false;
			var targetlayer = event.layer == "backgroundvideo" ? jQuery(_R[id].slides[_R[id].pr_active_key]).find("rs-sbg-wrap rs-bgvideo") : event.layer == "firstvideo" ? jQuery(_R[id].slides[_R[id].pr_active_key]).find(".rs-layer-video").first() : jQuery("#"+event.layer),
				tex = targetlayer.length>0;
			switch (event.action) {
				case "menulink":
					var linkto = _R.getURLDetails({url:event.url, anchor:event.anchor}),
						linkfrom = _R.getURLDetails();

					//SAME PAGE, DIFFERENT ANCHOR ?
					if (linkto.host == linkfrom.host && linkto.path == linkfrom.path && event.target==="_self") {
						//Scroll To Position
						_R.scrollToId({id:id, offset:event.offset, action:event.action, anchor: event.anchor, hash:linkto.hash, speed:event.speed, ease:event.ease});
					} else {
						//Update Location and add Anchor, plus Speed/Ease Parameter if necessary
						if (event.target==="_self")
							window.location = linkto.url + (linkto.anchor!==undefined && linkto.anchor!=="" ? (/*(linkto.params!="" && linkto.params!=undefined ? ";" : "?") + "rsaspd=" + event.speed+";rsaese:"+event.ease+";" +*/ "#"+linkto.anchor) : "");
						else
							window.open(linkto.url + (linkto.anchor!==undefined && linkto.anchor!=="" ? (/*(linkto.params!="" && linkto.params!=undefined ? ";" : "?") + "rsaspd=" + event.speed+";rsaese:"+event.ease+";" +*/ "#"+linkto.anchor) : ""));
					}

					e.preventDefault();
				break;
				case "nextframe":
				case "prevframe":
				case "gotoframe":
				case "togglelayer":
				case "toggleframes":
				case "startlayer":
				case "stoplayer":
					if (targetlayer[0]===undefined) continue;

					var _ = _R[id]._L[targetlayer[0].id],
						frame=event.frame,
						tou = "triggerdelay";


					if (e.type==="click" && _.clicked_time_stamp !==undefined && ((new Date().getTime() - _.clicked_time_stamp)<300)) return;
					if (e.type==="mouseenter" && _.mouseentered_time_stamp !==undefined && ((new Date().getTime() - _.mouseentered_time_stamp)<300)) return;
					//if (e.type==="mouseleave" && _.mouseleaveed_time_stamp !==undefined && ((new Date().getTime() - _.mouseleaveed_time_stamp)<300)) return;

					clearTimeout(_.triggerdelayIn);
					clearTimeout(_.triggerdelayOut);
					clearTimeout(_.triggerdelay);

				 	if (e.type==="click") _.clicked_time_stamp = new Date().getTime();
				 	if (e.type==="mouseenter") _.mouseentered_time_stamp = new Date().getTime();
				 	//if (e.type==="mouseleave") _.mouseleaveed_time_stamp = new Date().getTime();
				 	if (e.type==="mouseleave") _.mouseentered_time_stamp = undefined;

				 	if (event.action==="nextframe" || event.action==="prevframe") {
				 		_.forda = _.forda===undefined ? getFordWithAction(_) : _.forda;
				 		var inx = jQuery.inArray(_.currentframe,_.ford);
				 		if (event.action==="nextframe") inx++;
				 		if (event.action==="prevframe") inx--;
				 		while (_.forda[inx]!=="skip" && inx>0 && inx<_.forda.length-1) {
				 			if (event.action==="nextframe") inx++;
				 			if (event.action==="prevframe") inx--;
				 			inx = Math.min(Math.max(0,inx),_.forda.length-1);
				 		}
				 		frame = _.ford[inx];
				 	}
				 	if (jQuery.inArray(event.action,["toggleframes","togglelayer","startlayer","stoplayer"])>=0) {


					 	_.triggeredstate = event.action==="startlayer" || (event.action==="togglelayer" && _.currentframe!=="frame_1") || (event.action==="toggleframes" && _.currentframe!==event.frameN);

					 	if (event.action==="togglelayer" && _.triggeredstate===true && _.currentframe!==undefined &&_.currentframe!=="frame_999") _.triggeredstate=false; // If we are between two Frames, not on frame99 and frame1 need to toggle to frame999 !
					 	frame = _.triggeredstate ? event.action==="toggleframes" ? event.frameN : "frame_1" : event.action==="toggleframes" ? event.frameM : "frame_999";
					 	tou = _.triggeredstate ? "triggerdelayIn" : "triggerdelayOut";

					 	if (!_.triggeredstate) {
					 		if (_R.stopVideo) _R.stopVideo(targetlayer,id);
					 		_R.unToggleState(_.layertoggledby);
					 	} else {
					 		_R.toggleState(_.layertoggledby);
					 	}
					 }
					var pars = 	{layer:targetlayer, frame:frame, mode:"trigger", id:id};

					if (event.children===true) {
						pars.updateChildren = true;
						pars.fastforward = true;
					}

				 	if (_R.renderLayerAnimation) {
				 		clearTimeout(_[tou]);
				 		_[tou] = setTimeout(function(_) {
				 			_R.renderLayerAnimation(_);
				 		},(event.delay*1000),pars);
				 	}
				break;
				case "playvideo": if (tex) _R.playVideo(targetlayer,id);break;
				case "stopvideo": if (tex && _R.stopVideo) _R.stopVideo(targetlayer,id);break;
				case "togglevideo": if (tex) if (!_R.isVideoPlaying(targetlayer,id)) _R.playVideo(targetlayer,id); else if (_R.stopVideo) _R.stopVideo(targetlayer,id);break;
				case "mutevideo": if (tex) _R.Mute(targetlayer,id,true);break;
				case "unmutevideo":	if (tex && _R.Mute) _R.Mute(targetlayer,id,false);break;
				case "toggle_mute_video": if (tex) if (_R.Mute(targetlayer,id)) _R.Mute(targetlayer,id,false); else if (_R.Mute) _R.Mute(targetlayer,id,true); /*layer.toggleClass('rs-tc-active');*/break;
				case "toggle_global_mute_video":
					var pvl = _R[id].playingvideos != undefined && _R[id].playingvideos.length>0;
					if (pvl)
						if (_R[id].globalmute)
							jQuery.each(_R[id].playingvideos,function(i,layer) { if (_R.Mute) _R.Mute(layer,id,false);});
						else
							jQuery.each(_R[id].playingvideos,function(i,layer) { if (_R.Mute) _R.Mute(layer,id,true);});
					_R[id].globalmute = !_R[id].globalmute;
					//layer.toggleClass('rs-tc-active');
				break;

				// DELAYED ACTION CHECK
				default:
					tpGS.gsap.delayedCall(event.delay,function(targetlayer,id,event,layer) {
						switch(event.action) {
							case "openmodal":
								_R.openModalAPI(event.modal,event.modalslide===undefined ? 0 : event.modalslide, _R[id].ajaxUrl,true,id,event);
							break;
							case "closemodal": _R.revModal(id,{mode:"close"});break;
							case "callback": eval(event.callback);break;
							case "simplelink":	window.open(event.url,event.target);break;
							case "simulateclick": if (targetlayer.length>0) targetlayer.trigger('click');break;
							case "toggleclass": if (targetlayer.length>0) targetlayer.toggleClass(event.classname);break;
							case "scrollbelow":
							case "scrollto":
								if (event.action==="scrollbelow") layer.addClass("tp-scrollbelowslider");
								_R.scrollToId({id:id, offset:event.offset, action:event.action, anchor: event.id,  speed:event.speed, ease:event.ease});
							break;
							case "jumptoslide":

								switch (event.slide.toLowerCase()) {
									case "rs-random":
										var ts = Math.min(Math.max(0,Math.ceil(Math.random()*_R[id].realslideamount)-1));
										ts = _R[id].activeRSSlide==ts ? ts>0 ? ts-1 : ts+1 : ts;
										_R.callingNewSlide(id,_R[id].slides[ts].dataset.key,_R[id].sliderType==="carousel");
									break;
									case "+1":
									case "next":
									case "rs-next":
										_R[id].sc_indicator="arrow";
										_R[id].sc_indicator_dir = 0;
										_R.callingNewSlide(id,1,_R[id].sliderType==="carousel");
									break;
									case "rs-previous":
									case "rs-prev":
									case "previous":
									case "prev":
									case "-1":
										_R[id].sc_indicator="arrow";
										_R[id].sc_indicator_dir = 1;
										_R.callingNewSlide(id,-1,_R[id].sliderType==="carousel");
									break;
									case "first":
									case "rs-first":
										_R[id].sc_indicator="arrow";
										_R[id].sc_indicator_dir = 1;
										_R.callingNewSlide(id,0,_R[id].sliderType==="carousel");
									break;
									case "last":
									case "rs-last":
										_R[id].sc_indicator="arrow";
										_R[id].sc_indicator_dir = 0;
										_R.callingNewSlide(id,(_R[id].slideamount-1),_R[id].sliderType==="carousel");
									break;
									default:
										var ts = _R.isNumeric(event.slide) ?  parseInt(event.slide,0) : event.slide;
										_R.callingNewSlide(id,ts,_R[id].sliderType==="carousel");
									break;
								}
							break;

							case "toggleslider":
								_R[id].noloopanymore=0;
								if (_R[id].sliderstatus=="playing") {
									_R[id].c.revpause();
									_R[id].forcepaused = true;
									_R.unToggleState(_R[id].slidertoggledby);
								}
								else {
									_R[id].forcepaused = false;
									_R[id].c.revresume();
									_R.toggleState(_R[id].slidertoggledby);
								}
							break;
							case "pauseslider":
								_R[id].c.revpause();
								_R.unToggleState(_R[id].slidertoggledby);
							break;
							case "playslider":
								_R[id].noloopanymore=0;
								_R[id].c.revresume();
								_R.toggleState(_R[id].slidertoggledby);
							break;


							case "gofullscreen":
							case "exitfullscreen":
							case "togglefullscreen":
				var gf;
								if (jQuery('.rs-go-fullscreen').length>0 && (event.action=="togglefullscreen" || event.action=="exitfullscreen")) {
									jQuery('.rs-go-fullscreen').removeClass("rs-go-fullscreen");
									gf = _R[id].c.closest('rs-fullwidth-wrap').length>0 ? _R[id].c.closest('rs-fullwidth-wrap') : _R[id].c.closest('rs-module-wrap');
									_R[id].minHeight  = _R[id].oldminheight;
									_R[id].infullscreenmode = false;
									_R[id].c.revredraw();
									_R[id].c.revredraw();
									jQuery(window).trigger("resize");
									_R.unToggleState(_R[id].fullscreentoggledby);

								} else
								if (jQuery('.rs-go-fullscreen').length==0 && (event.action=="togglefullscreen" || event.action=="gofullscreen")) {
									gf = _R[id].c.closest('rs-fullwidth-wrap').length>0 ? _R[id].c.closest('rs-fullwidth-wrap') : _R[id].c.closest('rs-module-wrap');
									gf.addClass("rs-go-fullscreen");
									_R[id].oldminheight = _R[id].minHeight;
									_R[id].minHeight = _R.getWinH(id);
									_R[id].infullscreenmode = true;
									jQuery(window).trigger("resize");
									_R.toggleState(_R[id].fullscreentoggledby);
									_R[id].c.revredraw();
								}

							break;

							default: _R[id].c.trigger('layeraction',[event.action, layer, event]);break;
						}
					},[targetlayer,id,event,layer]);
				break;
			}
		} // GET THROUGH THE EXISITNG EVENTS ON THIS ELEMENTS
	});
};

/*
GET FRAME ORDERS WITH ACTIONS
*/
function getFordWithAction(_) {
	var neworder = [];
	for (var i in _.ford) {
		if (_.frames[_.ford[i]].timeline.waitoncall)
			neworder.push(_.ford[i]);
		else
			neworder.push("skip");
	}
	return neworder;
}
/*
HELPER TO CACHE TOGGLER TRIGGERERS
 */
function updateToggleByList(j,w,id) {
	var _ = j.data(w);
	if (_ === undefined) _ = [];
	_.push(id);
	j.data(w,_);
}
/*
BUILD ACTION OBJECT FOR LAYER
 */
function getEventParams(_) {
	var r = { on:"click",
			  delay:0,
			  ease:"power2.out",
			  speed:400
			 };
	for (var i in _) {

		// needed for new default (custom values from AddOn Action)
		if(!_.hasOwnProperty(i)) continue;
		var s = _[i].split(":");

		//Fix for ":" chars in the value
		if(s.length>2 && s[0]==="call") s[1] = (s.join(":")).replace(s[0]+":",'');

		switch (s[0]) {
			case "modal": r.modal = s[1];break;
			case "ms": r.modalslide = s[1];break;
			case "m": r.frameM = s[1];break;
			case "n": r.frameN = s[1];break;
			case "o": r.on = (s[1]==="click" || s[1]==="c" ? "click" : s[1]==="ml" || s[1]==="mouseleave" ? "mouseleave" : s[1]==="mouseenter" || s[1]==="me" ? "mouseenter" : s[1]); break;
			case "d": r.delay = parseInt(s[1],0)/1000; r.delay = r.delay==="NaN" || isNaN(r.delay) ? 0 : r.delay; break;
			case "rd": r.repeat = parseInt(s[1],0)/1000; r.repeat = r.repeat==="NaN" || isNaN(r.repeat) ? 0 : r.repeat; break;
			case "a": r.action = s[1];break;
			case "f": r.frame = s[1];break;
			case "slide": r.slide = s[1];break;
			case "layer": r.layer = s[1];break;
			case "sp": r.speed = parseInt(s[1],0);break;
			case "e": r.ease = s[1];break;
			case "ls": r.togglestate = s[1];break;
			case "offset": r.offset = s[1];break;
			case "call": r.callback = s[1];break;
			case "url": r.url = ""; for (var ii=1;ii<s.length;ii++) r.url += s[ii]+(ii===s.length-1 ? "" : ":");break;
			case "target": r.target = s[1];break;
			case "class": r.classname = s[1];break;
			case "ch": r.children = (s[1]=="true" || s[1]==true || s[1]=="t" ? true : false);break;
			default: if(s[0].length>0 && s[0]!=="") r[s[0]] = s[1];
		}
	}
	return r;
}


var getOffContH = function(c) {
	if (c==undefined) return 0;
	if (c.split(',').length>1) {
		var oc = c.split(","),
			a =0;
		if (oc)
			jQuery.each(oc,function(index,sc) {
				if (jQuery(sc).length>0)
					a = a + jQuery(sc).outerHeight(true);
			});
		return a;
	} else {
		return jQuery(c).height();
	}
	return 0;
};

//Support Defer and Async and Footer Loads
window.RS_MODULES = window.RS_MODULES || {};
window.RS_MODULES.actions = {loaded:true, version:version};
if (window.RS_MODULES.checkMinimal) window.RS_MODULES.checkMinimal();

})(jQuery);