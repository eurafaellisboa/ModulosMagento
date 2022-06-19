/********************************************
 * REVOLUTION EXTENSION - SLIDE ANIMATIONS
 * @date:23.04.2021
 * @requires rs6.main.js
 * @author ThemePunch
*********************************************/

(function($) {
    "use strict";


    // CHECK IF WE SCRIPT RUNNING IN EDITOR OR IN FRONTEND
    if (window._R_is_Editor) RVS._R = RVS._R===undefined ? {} : RVS._R; else window._R_is_Editor=false;


    var version = "6.4.9",
        p1i = "power1.in",  p1o = "power1.out", p1io = "power1.inOut", p2i = "power2.in", p2o = "power2.out", p2io = "power2.inOut",p3i = "power3.in", p3o = "power3.out", p3io = "power3.inOut";

    jQuery.fn.revolution = jQuery.fn.revolution || {};
    var _R = _R_is_Editor ? RVS._R : jQuery.fn.revolution;


            // Function References
            if (_R_is_Editor) RVS._R.isNumeric = RVS.F.isNumeric;


        ///////////////////////////////////////////
        // 	EXTENDED FUNCTIONS AVAILABLE GLOBAL  //
        ///////////////////////////////////////////
        jQuery.extend(true,_R, {

            // Read DATA Values from Slide DOM Element and Return the Animation Attributes
            getSlideAnimationObj : function(id,_,key) {
                var ret = {},attrs,basic;

                if (_.anim===undefined && _.in==undefined) _.in = "o:0";
                for (var i in _) {
                    if (!_.hasOwnProperty(i) || _[i]===undefined) continue;
                    var attrs = _[i].split(";");
                    for (var k in attrs) {
                        if (!attrs.hasOwnProperty(k)) continue;
                            basic = attrs[k].split(":");
                        if (basic[0]!==undefined && basic[1]!==undefined) {
                            ret[i] = ret[i]===undefined ? {} : ret[i];
                            ret[i][basic[0]] = i==="d3" && basic[0]==="c" ? basic[1] : basic[1].split(",")[0];
                        }
                    }
                }
                ret.in = ret.in===undefined ? {} : ret.in;
                ret.anim = ret.anim===undefined ? {e:"basic"} : ret.anim;
                // If Presets Exists, load the Preset Library and build the Random List
                if (!_R_is_Editor && ret.in!==undefined && ret.in.prst!==undefined) _R.loadSlideAnimLibrary(id,{key:key,prst:ret.in.prst});
                _R[id].sbgs[key].slideanimationRebuild = false;
                return ret;
            },

            loadSlideAnimLibrary : function(id,rnd) {
                if (_R.SLTR===undefined && _R.SLTR_loading!==true) {
                    _R.SLTR_loading=true;
                    jQuery.ajax({type:'post',url:_R[id].ajaxUrl,dataType:'json',data:{action:'revslider_ajax_call_front', client_action:'get_transitions' /*, token:_R[id].ajaxNonce*/},
                        success:function(ret, textStatus, XMLHttpRequest) { if(ret.success == true) {_R.SLTR = ret.transitions; if (rnd!==undefined) _R.setRandomDefaults(id,rnd.key,rnd.prst); }},
                        error:function(e) { console.log("Transition Table can not be loaded"); console.log(e);}
                    });
                } else if (rnd!==undefined && _R.SLTR!==undefined) _R.setRandomDefaults(id,rnd.key,rnd.prst);
            },

            convertSlideAnimVals : function (_) {
                return  {	anim : { eng:_.eng, ms:parseInt(_.speed,0), o:_.o, e:_.e, f:_.f, p:_.p ,d:parseInt(_.d,0), adpr: _.adpr},
                            d3 : {f:_.d3.f, d:_.d3.d, z:_.d3.z, t:_.d3.t, c:_.d3.c, e:_.d3.e, fdi:_.d3.fdi, fdo:_.d3.fdo, fz:_.d3.fz,  su:_.d3.su, smi:_.d3.smi, sma:_.d3.sma, sc:_.d3.sc, sl:_.d3.sl},
                            in : { eng:_.in.eng,  o:_R_is_Editor && _.preset!==undefined && _.preset.indexOf("rnd")===0 ? 0 : _R.valBeau(_.in.o), x:_R.valBeau(_.in.x), y:_R.valBeau(_.in.y), r:_R.valBeau(_.in.r), sx:_R.valBeau(_.in.sx), sy:_R.valBeau(_.in.sy), m:_.in.m, e:_.in.e, row:_.in.row, col:_.in.col,  mo:_.in.mou!=="false" && _.in.mou!==false ? _R.valBeau(_.in.mo) : 0, moo:_.in.mou!=="false" && _.in.mou!==false ? _R.valBeau(_.in.moo) : 'none', mou:_.in.mou},
                            out:_.out.a===undefined || _.out.a=='true' || _.out.a===true ? undefined : { a: tf(_.out.a), o:_R.valBeau(_.out.o), x:_R.valBeau(_.out.x), y:_R.valBeau(_.out.y), r:_R.valBeau(_.out.r), sx:_R.valBeau(_.out.sx), sy:_R.valBeau(_.out.sy), m:_.out.m, e:_.out.e, row:_R.valBeau(_.out.row), col:_R.valBeau(_.out.col)},
                            filter: {u:_.filter.u, e:_.filter.e, b:_.filter.b, g:_.filter.g, h:_.filter.h,  s:_.filter.s,  c:_.filter.c, i:_.filter.i},
                            addOns: _.addOns
                        };
            },

            setRandomDefaults : function(id,key,prst) {
                _R[id].sbgs[key].random = _R.getAnimObjectByKey(prst,_R.SLTR);
            },

            getSlideAnim_AddonDefaults : function() {
                var r = {};
                for (var i in _R.enabledSlideAnimAddons) r  = jQuery.extend(true,r,_R[_R.enabledSlideAnimAddons[i]].defaults());
                return r;
            },


            getSlideAnim_EmptyObject:function() {
                return {
                        speed:1000, o:'inout', e:'basic', f:'start', p:'none', d:15, eng:'animateCore', adpr: false,
                        d3 : {f:'none', d:'horizontal', z:300, t:0, c:'#ccc', e:'power2.inOut',fdi:1.5,fdo:2, fz:0, su:false, smi:0, sma:0.5, sc:'#000' , sl:1},
                        filter: {u:false, e:"default", b:0, g:0, h:100,  s:0,  c:100, i:0},
                        in: { o:1, x:0, y:0, r:0, sx:1, sy:1, m:false, e:'power2.inOut', row:1, col:1,  mo:80, mou:false},
                        out:{ a:'true', o:1, x:0, y:0, r:0, sx:1, sy:1, m:false, e:'power2.inOut', row:1, col:1},
                        addOns: _R.getSlideAnim_AddonDefaults()
                        }
            },

            //Get the Right Animation
            getAnimObjectByKey : function(key,lib) {
                //Cache Last Found
                if (_R.getAnimObjectCacheKey===key) return _R.getAnimObjectCache;
                _R.getAnimObjectCacheKey = key;

                var r;
                for (var main in lib)  if (lib.hasOwnProperty(main) && r===undefined)
                    for (var group in lib[main]) if (lib[main].hasOwnProperty(group) && r===undefined)
                        if (key===group && key.indexOf("rnd")===0) {
                            r = lib[main][group];
                            r.main = main;
                            r.group = group;
                        } else
                        for (var element in lib[main][group]) if (lib[main][group].hasOwnProperty(element) && r===undefined && element===key) {
                            r = lib[main][group][element];
                            r.main = main;
                            r.group = group;
                        }

                //Cache the R into Global Variable
                _R.getAnimObjectCache = jQuery.extend(true,{},r);
                return r;
            },

            getRandomSlideTrans : function(main,grp,lib) {
                if (_R.randomSlideAnimCache!==undefined && _R.randomSlideAnimCache[main]!==undefined && _R.randomSlideAnimCache[main][grp]!==undefined)
                    return _R.randomSlideAnimCache[main][grp][Math.floor(Math.random() *  _R.randomSlideAnimCache[main][grp].length)];

                _R.randomSlideAnimCache = _R.randomSlideAnimCache===undefined ? {} : _R.randomSlideAnimCache;
                _R.randomSlideAnimCache[main] = _R.randomSlideAnimCache[main]===undefined ? {} : _R.randomSlideAnimCache[main];
                _R.randomSlideAnimCache[main][grp] = _R.randomSlideAnimCache[main][grp]===undefined ? [] : _R.randomSlideAnimCache[main][grp];

                for (var m in lib) if (lib.hasOwnProperty(m) && m!=="random" && m!=="custom" && (main=="all" || m==main))
                    for (var g in lib[m]) if (lib[m].hasOwnProperty(g) && g!=="icon" && (""+grp=="undefined" || grp.indexOf(g)>=0))
                        for (var e in lib[m][g])  if (lib[m][g].hasOwnProperty(e) && jQuery.inArray(lib[m][g][e].title,["*north*","*south*","*east*","*west*"])==-1) _R.randomSlideAnimCache[main][grp].push(e);
                return _R.randomSlideAnimCache[main][grp][Math.floor(Math.random() *  _R.randomSlideAnimCache[main][grp].length)];
            },

            cbgW : function(id,slide) { return _R_is_Editor ? RVS.RMD.width : _R[id].sliderType==="carousel" ? _R[id].justifyCarousel ? _R[id].carousel.slide_widths[slide!==undefined ? slide : _R[id].carousel.focused] : _R[id].carousel.slide_width : _R[id].canv.width;},
            cbgH : function(id,slide) { return _R_is_Editor ? RVS.RMD.height :_R[id].sliderType==="carousel" ?  _R[id].carousel.slide_height : _R[id].maxHeight!==undefined && _R[id].maxHeight>0 && !_R[id].fixedOnTop ? Math.min(_R[id].canv.height,_R[id].maxHeight) : _R[id].canv.height;},

            valBeau : function(a) {
                a = (""+a).split(",").join('|');
                a = (""+a).replace("{",'ran(');
                a = (""+a).replace("}",')');
                a = (""+a).replace("[",'cyc(');
                a = (""+a).replace("]",')');

                return a;
            },

            animateSlide : function(id,obj) {
                if (_R_is_Editor) RVS.F.resetSlideTL();
                if (tpGS.eases.late===undefined) {
                    tpGS.CustomEase.create("late", "M0,0,C0,0,0.474,0.078,0.724,0.26,0.969,0.438,1,1,1,1");
                    tpGS.CustomEase.create("late2", "M0,0 C0,0 0.738,-0.06 0.868,0.22 1,0.506 1,1 1,1 ");
                    tpGS.CustomEase.create("late3", "M0,0,C0,0,0.682,0.157,0.812,0.438,0.944,0.724,1,1,1,1");
                }
                return prepareSlideAnimation(id,obj);
            },

            //Get Basic Animation Object
            getBasic : function(o) {
                //attr -> Which attribute should be compared to incoming options from Slide Options
                return jQuery.extend(true,{ attr:o==undefined || o.attr===undefined ? ['o', 'r', 'sx', 'sy', 'x','y', 'm', 'e', 'row', 'col','mo', 'moo'] : o.attr,
                                        in :{ f:"start", m:false, o:1, r:0, sx:1, sy:1, x:0,y:0,row:1,col:1, e:p2io, ms:1000, mo:0, moo: 'none'},
                                        out:{ f:"start", m:false, o:1, r:0, sx:1, sy:1, x:0,y:0,row:1,col:1, e:p2io, ms:1000}
                                },o);
            },

            playBGVideo : function(id,key,nBG) {

                if (_R_is_Editor) nBG = nBG===undefined ? RVS.SBGS[RVS.S.slideId].n : nBG; else {
                    if (nBG===undefined && (_R[id].pr_next_bg===undefined || _R[id].pr_next_bg.length===0)) return;
                    nBG = nBG===undefined ? _R[id].sbgs[key===undefined ? _R[id].pr_next_bg[0].dataset.key : key] : nBG;
                }
                if (nBG.bgvid!==undefined && nBG.bgvid.length>0) {
                    setBGVideoHandling(id,{},nBG,"in");
                    _R.resetVideo(nBG.bgvid,id);
                    _R.playVideo(nBG.bgvid,id,true);
                    tpGS.gsap.to(nBG.bgvid[0],0.2,{zIndex:30, display:"block", autoAlpha:1,delay:0.075, overwrite:"all"});


                }

            },

            stopBGVideo : function(id,key,nBG) {
                if (_R_is_Editor) nBG = nBG===undefined ? RVS.SBGS[RVS.S.slideId].n : nBG; else {
                    if (nBG===undefined && (_R[id].pr_next_bg===undefined || _R[id].pr_next_bg.length===0)) return;
                    nBG = nBG===undefined ? _R[id].sbgs[key===undefined ? _R[id].pr_next_bg[0].dataset.key : key] : nBG;
                }

                if (nBG.bgvid!==undefined && nBG.bgvid.length>0) {
                    nBG.drawVideoCanvasImagesRecall = false;
                    _R.stopVideo(nBG.bgvid,id);
                    tpGS.gsap.to(nBG.bgvid[0],0.2,{autoAlpha:0,zIndex:0, display:"none"})


                }
            },

            SATools : {
                // CALCULATE OFFSET BASED ON SIZE AND % or PX  //
                getOffset : function(v,size,sdir,ind) {
                    var p = (""+v).indexOf("%")>=0;
                    v = _R.SATools.getSpecialValue(v,ind,sdir);
                    return v==0 || v===undefined ? 0 : p ? size * (parseInt(v) / 100) : parseInt(v);
                },

                // Get Special Values like RANDOM||45  or WRAP||10,20,10,25,...//
                getSpecialValue : function(v,ind,sdir,p) {
                    if (_R.isNumeric(parseFloat(v,0))) return parseFloat(v,0);

                    var type = (""+v).split("ran(").length>1 ? "random" : (""+v).split("cyc(").length>1 ? "wrap" : (""+v).split("(").length>1 ?  "dir" : "unknown",
                        s = type==="random" ? v.slice(4,-1) : type==="wrap" ? v.slice(4,-1) : v.slice(1,-1),
                        vals = s.split("|");

                    if (type==="random") return tpGS.gsap.utils.random(parseFloat(vals[0]),parseFloat((vals.length>1 ? vals[1] : 0-vals[0])));
                    else
                    if (type==="wrap") {
                        var r = tpGS.gsap.utils.wrap(vals,ind);
                        return  (""+r).split("(").length>1 ? (parseFloat((r.slice(1,-1))) * sdir) + (p ? "%" : "") : r;
                    } else
                    if (type==="dir") return (parseFloat(vals[0]) * sdir) + (p ? "%" : "");
                }
            },

            getmDim : function(id,key, nBG) {
                var w = _R.cbgW(id,key),
                    h = _R.cbgH(id,key);
                nBG.DPR = _R_is_Editor ? Math.min(window.devicePixelRatio, 2) : _R[id].DPR;
                return _R.maxDimCheck(nBG,w,h);
            },

            maxDimCheck : function(nBG,w,h) {
                //CHECK IF CONTAINER WIDTH / HEIGHT BIGGER THEN THE ORIGINAL SOURCE
                var isVidImg = nBG.video!==undefined && (nBG.video.tagName==="img" || nBG.video.videoWidth==undefined ||  nBG.video.videoWidth==0),
                    newW,
                    newH;

                if ((nBG.currentState!=="animating" && nBG.panzoom==undefined) || (nBG.currentState==="animating" && (nBG.slideanimation==undefined || nBG.slideanimation.anim==undefined || nBG.slideanimation.anim.adpr !== 'true'))) {

                    if (nBG.DPR>1 && _R.ISM && h>1024) {
                        nBG.DPR = 1;
                        newW = w;
                        newH = h;
                    } else {
                        var od = {
                            w : nBG.video==undefined || nBG.isVidImg ? nBG.loadobj.width : nBG.video.videoWidth==0 ? nBG.loadobj.width : nBG.video.videoWidth,
                            h : nBG.video==undefined || nBG.isVidImg ? nBG.loadobj.height : nBG.video.videoHeight==0 ? nBG.loadobj.height : nBG.video.videoHeight
                        }


                        var vDPR = h/od.w,
                            hDPR = w/od.h,
                            naturalDPR = Math.max(vDPR,hDPR);
                        if (naturalDPR>nBG.DPR || (vDPR>=1 && hDPR>=1)) nBG.DPR = 1;
                        else if (nBG.DPR>naturalDPR) nBG.DPR = Math.min(nBG.DPR,nBG.DPR/naturalDPR);

                        newW = w * nBG.DPR;
                        newH = h * nBG.DPR;


                        if (nBG.DPR>1) {
                            var ratio = w/h;
                            if(od.w > od.h && od.w < newW){
                                newW = Math.max(w,od.w);
                                newH = newW / ratio;
                                nBG.DPR = 1;
                            } else if( od.h > od.w && od.h < newH){
                                newH = Math.max(h,od.h);
                                newW = newH * ratio;
                                nBG.DPR = 1;
                            }
                        }
                    }

                } else {
                    nBG.DPR = 1;
                    newW = w;
                    newH = h;
                }

                return {width:Math.round(newW), height: Math.round(newH), w: w, h: h};
            },

            // Upodate The Slide Background Rescaling and Redrwaing the Canvas
            updateSlideBGs : function(id,key,nBG,ignore) {


                if (_R_is_Editor) nBG = nBG===undefined ? RVS.SBGS[RVS.S.slideId].n : nBG; else {
                    if (nBG===undefined && (_R[id].pr_next_bg===undefined || _R[id].pr_next_bg.length===0)) return;
                    nBG = nBG===undefined ? _R[id].sbgs[key===undefined ? _R[id].pr_next_bg[0].dataset.key : key] : nBG;
                }

                ignore = nBG.mDIM===undefined ? false : ignore;

                if (!ignore) nBG.mDIM = _R.getmDim(id,nBG.skeyindex, nBG);

                if (nBG.video!==undefined) { // Reset and Force Recalculating of Canvas Size of Video
                    if (nBG.video.tagName!=="IMG") nBG.isVidImg="";
                    //if (_R[id].sliderType==="carousel") {
                        nBG.cDIMS = _R.getBGCanvasDetails(id,nBG);
                        nBG.canvas.width = nBG.mDIM.width;
                        nBG.canvas.height = nBG.mDIM.height;

                        nBG.ctx.clearRect(0,0,nBG.mDIM.width, nBG.mDIM.height);
                        nBG.ctx.drawImage( nBG.shadowCanvas , 0,0);
                    //}
                } else { // Recalculate Dimensions of Canvas
                    nBG.cDIMS = _R.getBGCanvasDetails(id,nBG,ignore);
                    nBG.canvas.width = nBG.mDIM.width;
                    nBG.canvas.height = nBG.mDIM.height;
                    if (nBG.currentState!=="panzoom" && nBG.currentState!=="animating" && (nBG.currentState!==undefined || _R_is_Editor || _R[id].sliderType =="carousel")) {
                        nBG.ctx.clearRect(0,0,nBG.mDIM.width, nBG.mDIM.height);
                        nBG.ctx.drawImage( nBG.shadowCanvas  , 0,0);
                    }
                }


                if (nBG.currentState==="animating" && _R[id].sliderType!=="carousel") _R.animatedCanvasUpdate(id,nBG);

            },

            addCanvas : function() {
                var c = document.createElement('canvas');
                x = c.getContext('2d');
                c.style.background = "transparent";
                c.style.opacity =1;
                return x;
            },

            // Playing HTML5 Video Frames or Cover Image
            updateVideoFrames : function(id,nBG,force) {

                    nBG.now = Date.now();
                    nBG.then = nBG.then===undefined ? nBG.now-500 : nBG.then;
                    nBG.elapsed = (nBG.now - nBG.then);
                    nBG.fps = nBG.currentState==="animating" && window._rs_firefox ? 50 : 33;
                    if (nBG.elapsed>nBG.fps) {
                        nBG.then = nBG.now - (nBG.elapsed % nBG.fps);
                    //Draw Frames on the Shadow Canvas
                    var isVidImg = nBG.video.tagName==="img" || nBG.video.videoWidth==undefined ||  nBG.video.videoWidth==0;
                    if (nBG.video!==undefined && !nBG.video.BGrendered && nBG.loadobj!==undefined && nBG.loadobj.img!==undefined || (_R.ISM && _R.isFirefox(id))) {

                        nBG.mDIM = _R.getmDim(id,nBG.skeyindex, nBG);
                        nBG.pDIMS = getContentDimensions(nBG.mDIM,nBG,{width:nBG.mDIM.width, height:nBG.mDIM.height, x:0, y:0, contw: nBG.loadobj.width, conth: nBG.loadobj.height});
                        nBG.shadowCanvas.width = nBG.mDIM.width;
                        nBG.shadowCanvas.height = nBG.mDIM.height;
                        nBG.shadowCTX.drawImage(nBG.loadobj.img,nBG.pDIMS.x, nBG.pDIMS.y, nBG.pDIMS.width, nBG.pDIMS.height);


                    } else {
                        if (force || nBG.sDIMS===undefined || isVidImg!==nBG.isVidImg || nBG.sDIMS.width===0 || nBG.sDIMS.height===0 ) {
                            nBG.isVidImg = isVidImg;
                            nBG.mDIM = _R.getmDim(id,nBG.skeyindex, nBG);
                            nBG.sDIMS = getContentDimensions(nBG.mDIM,nBG,{width:nBG.mDIM.width, height:nBG.mDIM.height, x:0, y:0, contw: ( nBG.isVidImg ? nBG.loadobj.width : nBG.video.videoWidth), conth: (nBG.isVidImg ? nBG.loadobj.height : nBG.video.videoHeight)});
                        }

                        if (nBG.sDIMS!==undefined && nBG.sDIMS.width!==0 && nBG.sDIMS.height!==0) {
                            if (nBG.currentState==="animating")	{   // It is still animating, so create Shadow Canvas First
                                nBG.shadowCanvas.width = nBG.sDIMS.width;
                                nBG.shadowCanvas.height = nBG.sDIMS.height;
                                nBG.shadowCTX.drawImage(nBG.video,nBG.sDIMS.x, nBG.sDIMS.y, nBG.sDIMS.width, nBG.sDIMS.height);
                            } else
                            if(nBG.animateDirection===undefined) {  // If not in Animating and/or Out Animating, than simple draw straight on the Real Canvas, no need Shadow Canvas any more
                                nBG.canvas.width = nBG.mDIM.width;
                                nBG.canvas.height = nBG.mDIM.height;
                                nBG.ctx.drawImage(nBG.video, nBG.sDIMS.x, nBG.sDIMS.y, nBG.sDIMS.width, nBG.sDIMS.height);
                            }
                            nBG.shadowCanvas_Drawn = true;
                        }
                    }
                }

                if (force || (nBG.drawVideoCanvasImagesRecall && nBG.currentState==="animating") || (nBG.currentState==="animating" && nBG.shadowCanvas_Drawn===undefined))
                    window.requestAnimationFrame(function() {_R.updateVideoFrames(id,nBG);})

            },

            createOverlay : function(id,type,len,colors) {
                if(type === "none") return "none";
                type = type===undefined ? 1 : type;
                len = len===undefined ? 1 : len;
                var colors = colors === undefined ? {
                        0: 'rgba(0, 0, 0, 0)',
                        1: 'rgba(0, 0, 0, 1)',
                    } : colors,
                    patterns = {none:[[0]],
                                1:[[1, 0],[0, 0]],
                                    2:[[1, 0,0],[0,0,0],[0,0,0]],
                                    3:[[1,0,0,0],[0,0,0,0],[0,0,0,0]],
                                    4:[[1],[0]],
                                    5:[[1],[0],[0]],
                                    6:[[1],[0],[0],[0]],
                                    7:[[1,0]],
                                    8:[[1,0,0]],
                                    9:[[1,0,0,0]],
                                10:[[1,0,0,0,0],[0,1,0,1,0],[0,0,0,0,0],[0,1,0,1,0],[0,0,0,0,1]],
                                11:[[0,0,1,0,0],[0,1,0,1,0],[1,0,0,0,1],[0,1,0,1,0],[0,0,1,0,0]],
                                12:[[1,0,0],[0,1,0],[0,0,1]],
                                13:[[0,0,1],[0,1,0],[1,0,0]],
                                14:[[1,0,0,0,0],[0,1,0,0,0],[0,0,1,0,0],[0,0,0,1,0],[0,0,0,0,0]],
                                15:[[0,0,0,0,1],[0,0,0,1,0],[0,0,1,0,0],[0,1,0,0,0],[1,0,0,0,0]],
                                16:[[1,0,0,0,1],[0,1,0,1,0],[0,0,1,0,0],[0,1,0,1,0],[1,0,0,0,1]]
                                },
                    pattern = patterns[type]===undefined ? patterns['2'] : patterns[type];
                if (_R_is_Editor) _R[id] = _R[id]===undefined ? {} : _R[id];
                _R[id].patternCanvas = document.createElement('canvas');
                _R[id].patternCtx = _R[id].patternCanvas.getContext("2d");
                    _R[id].patternCanvas.width = pattern[0].length * len;
                    _R[id].patternCanvas.height = pattern.length * len;
                    for(var i = 0; i < pattern.length; i++)
                    for(var j = 0; j < pattern[i].length; j++ ){
                        if (colors[pattern[i][j]]=="transparent") continue;
                        _R[id].patternCtx.fillStyle = colors[pattern[i][j]];
                        _R[id].patternCtx.fillRect(j * len, i * len, len, len);
                    }

                var dataURL = _R[id].patternCanvas.toDataURL();
                return "url(" + dataURL + ")";

            },
            //Get, Build BG Infos like Size, Content, Shadow Canvas
            getBGCanvasDetails : function(id,nBG,ignore) {
                var r ;
                if (!ignore) nBG.mDIM = _R.getmDim(id,nBG.skeyindex, nBG);
                nBG.usepattern = (nBG.bgfit==="auto" || nBG.bgfit.indexOf('%')>=0) && (nBG.loadobj===undefined || nBG.loadobj.useBGColor!==true);
                if (_R_is_Editor && nBG.panzoom===undefined) delete nBG.shadowCanvas;
                //Create a Shadow Canvas where we get the Content from later
                if (nBG.shadowCanvas===undefined) {
                    nBG.shadowCanvas = document.createElement('canvas');
                    nBG.shadowCTX = nBG.shadowCanvas.getContext('2d');
                    nBG.shadowCanvas.style.background = "transparent";
                    nBG.shadowCanvas.style.opacity =1;
                }

                if (nBG.replaceShadowCanvas!==true && nBG.loadobj.bgColor!==true && nBG.usebgColor!==true && nBG.panzoom===undefined && (nBG.isHTML5==undefined || nBG.poster==true) && !nBG.usepattern) {  // IT IS AN IMAGE AND NOT A BACKGROUND COLOR
                    r = getContentDimensions(nBG.mDIM,nBG,{width:nBG.mDIM.width, height:nBG.mDIM.height, x:0, y:0, contw: nBG.loadobj.width, conth: nBG.loadobj.height});
                    nBG.shadowCanvas.width = nBG.mDIM.width;
                    nBG.shadowCanvas.height = nBG.mDIM.height;
                    if (nBG.loadobj!==undefined && nBG.loadobj.img!==undefined){
                        nBG.shadowCTX.drawImage(nBG.loadobj.img,r.x, r.y, r.width, r.height);
                    }
                    r = {width:nBG.mDIM.width, height:nBG.mDIM.height, x:0, y:0}

                } else {

                    r = {width:nBG.mDIM.width, height:nBG.mDIM.height, x:0, y:0}
                    if (nBG.usepattern && nBG.loadobj!==undefined && nBG.loadobj.img!==undefined)
                        _R.getCanvasPattern(id,nBG, { ratio : nBG.loadobj.height / nBG.loadobj.width});
                    else
                    if (nBG.loadobj.bgColor || nBG.usebgColor) {
                        nBG.shadowCanvas.width = nBG.mDIM.width;
                        nBG.shadowCanvas.height = nBG.mDIM.height;
                        _R.getCanvasGradients(id,nBG);
                    }
                }

                return r;
            },
            getCanvasPattern : function(id,nBG,r) {
                // CREATE PATTERN
                if (nBG.patternImageCanvas===undefined) {
                    nBG.patternImageCanvas = document.createElement('canvas');
                    nBG.patternImageCTX = nBG.patternImageCanvas.getContext('2d');
                }
                var fit = nBG.bgfit.split(" ");
                if (fit.length===1) fit[1] = fit[0];

                r.width = fit[0]==="auto" ? nBG.loadobj.width : nBG.loadobj.width * (parseInt(fit[0],0)/100);
                r.height = fit[1]==="auto" ? nBG.loadobj.height : r.width * r.ratio;

                nBG.DPR = _R_is_Editor ? Math.min(window.devicePixelRatio, 2) : _R[id].DPR;
                var ratio = r.width/r.height;
                r.width = r.width * nBG.DPR;
                r.height = r.height * nBG.DPR;

                if(_R.isIOS && r.width * r.height > 60*1024*1024/4){
                    if(nBG.mDIM.width > nBG.mDIM.height){
                        r.width = nBG.mDIM.width;
                        r.height = Math.round(nBG.mDIM.width / ratio);
                    } else {
                        r.height = nBG.mDIM.height;
                        r.width = Math.round(nBG.mDIM.height * ratio);
                    }
                }

                nBG.patternImageCanvas.width = r.width;
                nBG.patternImageCanvas.height = r.height;
                nBG.patternImageCTX.drawImage(nBG.loadobj.img,0,0,r.width,r.height);

                nBG.shadowCanvas.width = nBG.mDIM.width;
                nBG.shadowCanvas.height = nBG.mDIM.height;
                nBG.pattern = nBG.shadowCTX.createPattern(nBG.patternImageCanvas, nBG.bgrepeat);
                nBG.shadowCTX.fillStyle = nBG.pattern;

                nBG.shadowShifts = {  h: nBG.bgposition.split(" ")[0], v:nBG.bgposition.split(" ")[1]};

                nBG.shadowShifts.hperc = _R.isNumeric(parseInt(nBG.shadowShifts.h)) ? parseInt(nBG.shadowShifts.h)/100 * nBG.mDIM.width : 0;
                nBG.shadowShifts.vperc = _R.isNumeric(parseInt(nBG.shadowShifts.v)) ? parseInt(nBG.shadowShifts.v)/100 * nBG.mDIM.height : 0;

                nBG.shadowShifts.x = nBG.shadowShifts.h==="left" ? 0 : nBG.shadowShifts.h==="center" || nBG.shadowShifts.h=="50%" ? nBG.bgrepeat=="repeat" || nBG.bgrepeat=="repeat-x" ? (nBG.mDIM.width/2 - r.width/2) - Math.ceil((nBG.mDIM.width/2)/r.width)*r.width : (nBG.mDIM.width/2 - r.width/2) : nBG.shadowShifts.h==="right" ? nBG.bgrepeat=="repeat" || nBG.bgrepeat=="repeat-x" ? -(r.width-(nBG.mDIM.width%r.width)) :nBG.mDIM.width-r.width  : nBG.bgrepeat=="repeat" || nBG.bgrepeat=="repeat-x" ?  -(r.width -(nBG.shadowShifts.hperc%r.width))  : nBG.shadowShifts.hperc ;
                nBG.shadowShifts.y = nBG.shadowShifts.v==="top" ? 0 : nBG.shadowShifts.v==="center" || nBG.shadowShifts.v=="50%" ? nBG.bgrepeat=="repeat" || nBG.bgrepeat=="repeat-y" ? (nBG.mDIM.height/2 - r.height/2) - Math.ceil((nBG.mDIM.height/2)/r.height)*r.height : (nBG.mDIM.height/2 - r.height/2)  : nBG.shadowShifts.v==="bottom" ? nBG.bgrepeat=="repeat" || nBG.bgrepeat=="repeat-y" ? -(r.height-(nBG.mDIM.height%r.height)) : nBG.mDIM.height-r.height : nBG.bgrepeat=="repeat" || nBG.bgrepeat=="repeat-y" ? -(r.height -(nBG.shadowShifts.vperc%r.height))  : nBG.shadowShifts.vperc ;

                nBG.shadowCTX.translate(nBG.shadowShifts.x,nBG.shadowShifts.y);
                nBG.shadowCTX.fillRect(0,0,nBG.mDIM.width-nBG.shadowShifts.x,nBG.mDIM.height-nBG.shadowShifts.y);
            },
            getCanvasGradients : function(id,nBG) {	//Create Canvas with color
                if (nBG.bgcolor.indexOf('gradient')>=0) {
                    nBG.gradient = nBG.gradient==undefined || _R_is_Editor ? _R.getGradients(nBG.bgcolor) : nBG.gradient;
                    nBG.shadowGrd = nBG.gradient.type==="radialGradient" ?
                                        nBG.shadowCTX.createRadialGradient(nBG.mDIM.width/2,nBG.mDIM.height/2,0,nBG.mDIM.width/2,nBG.mDIM.height/2,Math.max(nBG.mDIM.width/2,nBG.mDIM.height/2)) :
                                        _R.calcLinearGradient(nBG.shadowCTX,nBG.shadowCanvas.width,nBG.shadowCanvas.height,nBG.gradient.deg);

                    //ADD COLOR STOPS AND FILL AREA
                    for(var i=0;i<nBG.gradient.stops.length;i=i+2) nBG.shadowGrd.addColorStop(nBG.gradient.stops[i+1],nBG.gradient.stops[i]);
                    nBG.shadowCTX.clearRect(0,0,nBG.mDIM.width,nBG.mDIM.height);
                    nBG.shadowCTX.fillStyle=nBG.shadowGrd;
                    nBG.shadowCTX.fillRect(0,0,nBG.mDIM.width,nBG.mDIM.height);
                } else {
                    // FILL SIMPLE COLOR

                    nBG.shadowCTX.fillStyle=nBG.bgcolor;
                    nBG.shadowCTX.fillRect(0,0,nBG.mDIM.width,nBG.mDIM.height);
                }
            },

            cNS : function(_) { // Create NS Document with Style and Attributes
              var p;
              _.n = document.createElementNS("http://www.w3.org/2000/svg", _.n);
              for (p in _.v) _.n.setAttributeNS(null, p.replace(/[A-Z]/g, function(m, p, o, s) { return "-" + m.toLowerCase(); }), _.v[p]); // SET SVG ATTRIBUTES
              if (_.c!==undefined) _.n.setAttribute('class',_.c);  // SET CLASS
              if (_.id!==undefined) _.n.id = _.id; // SET CLASS
              if (_.t!==undefined) _.n.textContent = _.t; // SET CONTENT
              for (p in _.s) if (_.s.hasOwnProperty(p)) _.n.style[p] = _.s[p];  //SET STYLE
              return _.n
            },


            rgbToHex : function(rgb) {
              return "#" + componentToHex(rgb[0]) + componentToHex(rgb[1]) + componentToHex(rgb[2]);
            },

            getSVGGradient : function(color) {
                if (color===undefined) return color;
                if (_R_is_Editor) color = RSColor.convert(color);
                if (color.indexOf('gradient')==-1) return color;
                var gradient = _R.getGradients(color);

                if (_R.gradSVG===undefined) {
                    // BUILD SVG
                    _R.gradSVG = _R.cNS({n:'svg',id:'tp_svg_gradients', s:{width:"100%", height:"100%", opacity:0, pointerEvents:"none"}});
                    _R.gradSVG.setAttribute('viewBox', '0 0 1 1');
                    _R.gradSVG.setAttribute('preserveAspectRatio', 'none');
                    document.body.appendChild(_R.gradSVG);
                    _R.svgGradients = [];
                }
                var found = false, cString = JSON.stringify(color);

                //Check if we created SVG GRADIENT ALREADY
                for (var i=0;i<_R.svgGradients.length;i++) {
                    if (found) continue;
                    if (_R.svgGradients[i].src == cString) {
                        found = true;
                        color = _R.svgGradients[i].url;
                    }
                }

                if (!found) {
                    var cS,c,nG,
                        anglePI = gradient.type==="radialGradient" ? 0 : (gradient.deg) * (Math.PI / 180),
                            aC = gradient.type==="radialGradient" ? 0 : {
                                'x1': Math.round(50 + Math.sin(anglePI) * 50) + '%',
                                'y1': Math.round(50 + Math.cos(anglePI) * 50) + '%',
                                'x2': Math.round(50 + Math.sin(anglePI + Math.PI) * 50) + '%',
                                'y2': Math.round(50 + Math.cos(anglePI + Math.PI) * 50) + '%',
                            };
                        nG = _R.cNS({n:gradient.type,id:'tp_svg_gradient_'+_R.svgGradients.length, v:gradient.type==="radialGradient" ? undefined : {gradientUnits:"userSpaceOnUse", x1:aC.x1, y1:aC.y1, x2:aC.x2, y2:aC.y2}})

                    for (var s=0;s<=gradient.stops.length/2;s=s+2) {
                        c = tpGS.gsap.utils.splitColor(gradient.stops[s]);
                        cS = _R.cNS({n:'stop', v:{offset:(gradient.stops[s+1]*100)+"%", stopColor:_R.rgbToHex(c), stopOpacity:c.length>3 ? c[3] : 1}})
                        nG.appendChild(cS);
                    }
                    _R.gradSVG.appendChild(nG);
                    color = 'url(#tp_svg_gradient_'+(_R.svgGradients.length)+')';
                    _R.svgGradients.push({url:color,src:cString,g:nG});
                }

                return color;

            },
            getGradients : function(color) {
                // RADIAL GRADIENT
                if (color.indexOf('radial-gradient')>=0)
                    return {stops : _R.getGradientColorStopPoints((color.split('radial-gradient(ellipse at center, ')[1])), type:'radialGradient',deg:0};
                else if (color.indexOf('gradient')!==-1)
                    return _R.getLinearGradientStops(color);
                else return color;

            },
            getLinearGradientStops : function(color) {
                var c = color.split('linear-gradient(')[1];
                if (_R_is_Editor) {
                    c = c.split(', ').join(',');
                    c = c.split(',rgba').join(', rgba');
                }
                var cDeg = c.split('deg, ');
                c = (cDeg.length>1 ? cDeg[1] : cDeg[0]).split(" ");
                cDeg = cDeg.length>1 ? cDeg[0] : 180;
                for(var i in c) if (c.hasOwnProperty(i) && c[i].indexOf('%')>=0) c[i] = ""+Math.round(parseFloat((c[i].split("%,")[0]).split("%)")[0])*100)/10000;
                return {stops:c, deg:cDeg, type:'linearGradient'};
            },

            getGradientColorStopPoints : function(color){
                var pattern = /rgb([\s\S]*?)%/g;
                var match;
                var matches = [];
                var points = [];

                do {
                    match = pattern.exec(color);
                    if (match) {
                        matches.push(match[0]);
                    }
                } while (match);

                for (var i = 0; i < matches.length; i++) {
                    var match = matches[i];
                    var color = /rgb([\s\S]*?)\)/.exec(match);
                    var stop = /\)([\s\S]*?)%/.exec(match);
                    if (color[0]) color = color[0];
                    if (stop[1]) stop = stop[1];

                    points.push(color);
                    points.push(parseFloat(stop)/100);
                }

                return points;
            },

            calcLinearGradient : function (ctx,w, h,deg) {

                deg = deg * Math.PI / 180 + Math.PI / 2;

                var tx,ty,bx,by,
                    _w = w/2,
                    _h = h/2,
                    len = Math.sqrt(_w * _w + _h * _h),
                    l1 = {
                        x1: Math.cos(deg) * len + _w,
                        y1: Math.sin(deg) * len + _h,
                        x2: _w,
                        y2: _h
                    },
                    lines = [
                        getPerpLine({x: 0, y: 0}, deg),
                        getPerpLine({x: w, y: 0}, deg),
                        getPerpLine({x: w, y: h}, deg),
                        getPerpLine({x: 0, y: h}, deg),
                    ],
                    intersects = [];


                    for (var i = 0; i < lines.length; i++) intersects.push(lineIntersect(lines[i], l1));

                    if(dist(_w, _h, intersects[0].x, intersects[0].y) > dist(_w, _h, intersects[1].x, intersects[1].y)){
                        tx = intersects[0].x;
                        ty = intersects[0].y;
                    } else {
                        tx = intersects[1].x;
                        ty = intersects[1].y;
                    }

                    if(dist(_w, _h, intersects[2].x, intersects[2].y) > dist(_w, _h, intersects[3].x, intersects[3].y)){
                        bx = intersects[2].x;
                        by = intersects[2].y;
                    } else {
                        bx = intersects[3].x;
                        by = intersects[3].y;
                    }

                    var eAngle = Math.round( Math.atan2((_h - ty), (_w - tx)) * 100) / 100;
                    var aAngle = Math.round(deg % (Math.PI * 2) * 100) / 100;

                    if(eAngle === aAngle){
                        var x = tx, y = ty;
                        tx = bx, ty = by;
                        bx = x, by = y;
                    }

                return ctx.createLinearGradient(Math.round(tx), Math.round(ty), Math.round(bx), Math.round(by));
            },

            // Here you can extend the Transition Library to manipulate animations during the transformations and by preparing them.
            transitions : {
                filter : {
                    update : function(f,ctx,blurExt) {
                        if (f!==undefined && f.tl!==undefined) {
                            var blur = blurExt!==undefined || f.tl.blur!==undefined ? " blur("+(blurExt!==undefined ? blurExt : 0 + f.tl.blur!==undefined ? f.tl.blur : 0 )+"px)" : "";
                            ctx.canvas.style.filter = f.tl.filter === undefined ? "" + blur : f.tl.filter + blur;
                        }
                    },

                    extendTimeLine : function(TL,_,BG) {
                        if (_==undefined) return;
                        //var f = _.b!==undefined && _.b!=="0px" && _.b!==0 ? "blur(_b_px)" : "";
                        var f = _.g!==undefined && _.g!=="0%" && _.g!==0 ? (f==="" ? "" : " ") + "grayscale(_g_%)" : "";
                            f += _.h!==undefined && _.h!=="100%" && _.h!==100 ? (f==="" ? "" : " ") + "brightness(_h_%)" : "";
                            f += _.s!==undefined && _.s!=="0px" && _.s!==0 ? (f==="" ? "" : " ") + "sepia(_s_%)" : "";
                            f += _.c!==undefined && _.c!==100 ? (f==="" ? "" : " ") + "contrast(_c_%)" : "";
                            f += _.i!==undefined && _.i!==0 ? (f==="" ? "" : " ") + "invert(_i_%)" : "";

                        if (f!=="") _.tl = {filter:f.replace('_g_',parseFloat(_.g)).replace('_h_',parseFloat(_.h)).replace('_s_',parseFloat(_.s)).replace('_c_',parseFloat(_.c)).replace('_i_',parseFloat(_.i))};
                        if (_.b!==undefined && _.b!=="0px" && _.b!==0) if (_.tl===undefined) _.tl = {blur:parseFloat(_.b)}; else _.tl.blur = parseFloat(_.b);
                        if (_.tl!==undefined) {
                            TL.add(tpGS.gsap.to(_.tl, _.ms/_.sec, _.tl.filter===undefined ? {blur:0} : _.tl.blur===undefined ? {filter:f.replace('_g_','0').replace('_h_','100').replace('_s_','0').replace('_c_',100).replace('_i_',0), ease:_.e} : {blur:0, filter:f.replace('_g_','0').replace('_h_','100').replace('_s_','0').replace('_c_',100).replace('_i_',0), ease:_.e}),0)
                            BG.canvasFilter = true;
                        }
                    },
                },

                slidingoverlay : {
                    getBasic : function() {
                        return _R.getBasic({ attr:['x','y'], in:{m:true,o:-1, _xy:20, _gxys:10,_gxye:-10 ,zIndex:20,e:p1io},out:{m:true, reversed:false, _xy:-100,o:0, zIndex:10, e:p1io}});
                        },
                    updateAnim : function(id,_,sdir) {
                        var v = _.in.x!==undefined && _.in.x!==0 && _.in.x!=="0%" ? "x" : "y";
                        _.in["g"+v+"s"] =_R.SATools.getOffset(_.in[v],_.in._gxys,sdir,1)+"%";
                        _.in["g"+v+"e"] = _R.SATools.getOffset(_.in[v],_.in._gxye,sdir,1)+"%";
                        _.out[v] = _R.SATools.getOffset(_.in[v],_.out._xy,sdir,1)+"%"
                        _.in[v] = _R.SATools.getOffset(_.in[v],_.in._xy,sdir,1)+"%";
                        var pos = parseInt(_.in[v])>=0;
                        _.in.d = v==="x" ?  pos ? "left" : "right" : pos ? "up" : "down";
                        return _;
                    },

                    beforeDraw : function(id,ctx,box,BG) {
                        if (box.d!==undefined) {
                                box._dxs = box.d==="right" ?  0 + box.mw  : box.d==="left" ? 0 - box.mw : 0,
                                box._dys = box.d==="down" ?  0 + box.mh  : box.d==="up" ? 0 - box.mh : 0,
                                box._xs = box.d==="left" ? 0 - box.mw : 0,
                                box._ys = box.d==="up" ? 0 - box.mh : 0,
                                box._xe = box.d==="right" ? BG.SLOT.OW + box.mw : box.d==="left" ?  BG.SLOT.OW - box.mw  : BG.SLOT.OW,
                                box._ye = box.d==="down" ? BG.SLOT.OH + box.mh : box.d==="up" ?  BG.SLOT.OH - box.mh  : BG.SLOT.OH;
                            ctx.beginPath();
                            ctx.rect(box.d==="left" ? Math.max(0,box._xs) : box.d==="right" ? Math.min(0,box._xs) : 0,
                                        box.d==="up" ? Math.max(0,box._ys) : box.d==="down" ? Math.min(0,box._ys) : 0,
                                        box.d==="left" ? Math.max(BG.SLOT.OW,box._xe) : box.d==="right" ? Math.min(BG.SLOT.OW,box._xe) : box._xe,
                                        box.d==="up" ? Math.max(BG.SLOT.OH,box._ye) : box.d==="down" ? Math.min(BG.SLOT.OH,box._ye) : box._ye);
                            ctx.clip();
                        }

                    },
                    afterDraw : function(id,ctx,box, BG,tr) {
                        // Ghost Image (For Slide Overlays)
                        if (box.d!==undefined) {
                            ctx.save();
                            ctx.beginPath();
                            ctx.rect(Math.max(0,box._dxs),Math.max(0,box._dys),box._xe,box._ye);
                            ctx.clip();
                            ctx.save();
                            ctx.transform(tr.csx, tr.ssx, tr.ssy, tr.csy, BG.SLOT.OW*(0.5) + box.x + box.sgx, BG.SLOT.OH*(0.5)+ box.y + box.sgy);
                            ctx.drawImage(BG.shadowCanvas!==undefined ? BG.shadowCanvas : BG.loadobj.img, 0 ,  0  , BG.SLOT.OW, BG.SLOT.OH, box.sgx-BG.SLOT.OW/2, box.sgy-BG.SLOT.OH/2  , BG.SLOT.OW, BG.SLOT.OH);
                            ctx.restore();
                            ctx.fillStyle="rgba(0,0,0,0.6)";
                            ctx.fillRect(box.gx,  box.gy,BG.SLOT.OW, BG.SLOT.OH);
                            ctx.restore();
                        }
                    },

                    extendTimeLine : function(id,TL,boxes,_,params,dim) {
                        if (params.direction==="in" && (_.gxe!==undefined || _.gye!==undefined)) {
                            jQuery.extend(true,boxes[0], {	d:_.d,
                                                            gx:_.gxs===undefined ? 0 : _R.SATools.getOffset(_.gxs,dim.width,params.sdir,0)*2,
                                                            gy:_.gys===undefined ? 0 : _R.SATools.getOffset(_.gys,dim.height,params.sdir,0)*2,
                                                            sgx:_.gxs===undefined ? 0 : _R.SATools.getOffset(_.gxs,dim.width,params.sdir,0),
                                                            sgy:_.gys===undefined ? 0 : _R.SATools.getOffset(_.gys,dim.height,params.sdir,0),
                                                            mw:0-dim.width,
                                                            mh:0-dim.height});


                            TL.add(tpGS.gsap.to(boxes, (_.ms/_.sec), {	gx:_.gxe===undefined ? 0 : _R.SATools.getOffset(_.gxe,dim.width,params.sdir,0)*2,
                                                                        gy:_.gye===undefined ? 0 : _R.SATools.getOffset(_.gye,dim.height,params.sdir,0)*2,
                                                                        sgx:_.gxe===undefined ? 0 : _R.SATools.getOffset(_.gxe,dim.width,params.sdir,0)*2,
                                                                        sgy:_.gye===undefined ? 0 : _R.SATools.getOffset(_.gye,dim.height,params.sdir,0)*2,
                                                                        mw:dim.width,
                                                                        mh:dim.height,
                                                                        ease:_.e
                                                                    }),0);
                        }

                    }
                },
                motionFilter : {
                    init: function(BG,mo) {
                        if(mo!==undefined && parseFloat(mo) > 0){
                            mo = parseFloat(mo);
                            BG.fmExists = true;
                            BG.fmShadow = BG.fmShadow === undefined ? document.createElement('canvas') : BG.fmShadow;
                            BG.fmCtx = BG.fmShadow.getContext('2d');
                            BG.fmShadow.width = BG.ctx.canvas.width;
                            BG.fmShadow.height = BG.ctx.canvas.height;
                            BG.fmCtx.globalAlpha = tpGS.gsap.utils.mapRange(100, 0, 40, 0,mo) / 100;
                            BG.fmCtx.clearRect(0, 0, BG.ctx.canvas.width, BG.ctx.canvas.height);
                        } else BG.fmExists = false;
                        return mo;
                    },
                    render : function(BG, o) {
                        if(o === 'partial')BG.fmCtx.globalCompositeOperation = 'source-over';
                        BG.fmCtx.drawImage(BG.canvas, 0, 0, BG.canvas.width, BG.canvas.height);
                        BG.ctx.clearRect(0,0,BG.canvas.width, BG.canvas.height);
                        BG.ctx.drawImage(BG.fmCtx.canvas, 0, 0, BG.canvas.width, BG.canvas.height);
                        if(o === 'partial')BG.fmCtx.globalCompositeOperation = 'source-atop';
                        if(o === 'partial' || o === 'full'){
                            BG.fmCtx.fillStyle = 'rgba(255, 255, 255, 0.1)';
                            BG.fmCtx.fillRect(0,0,BG.canvas.width, BG.canvas.height);
                        }
                    },
                    clearFull : function(BG,TL) {
                        if (BG.fmExists) {
                            if (BG.fmCtx!==undefined) {
                                BG.ctx.clearRect(0,0,BG.canvas.width, BG.canvas.height);
                                BG.fmCtx.clearRect(0,0,BG.canvas.width, BG.canvas.height);
                                if (TL!==undefined) TL.render(TL.time(), true, true);
                            }
                        }
                    },
                    complete : function(BG) {
                        if(BG.fmShadow) BG.fmShadow.remove();
                    }
                },
                d3 : {
                    ticker : function(_,BG,dir) {
                        if (_.helper===undefined) return;
                        var min = _.smi * ( dir==="in" ? _.helper.oo : _.helper.o),
                            max = _.sma * ( dir==="in" ? _.helper.oo : _.helper.o);

                        //Gradient on Canvases
                        _.gradient = _.d==="vertical" ? dir==="in" ? BG.ctx.createLinearGradient(0,0,0,BG.canvas.height) : BG.ctx.createLinearGradient(0,BG.canvas.height,0,0)
                                                        : dir==="in" ? BG.ctx.createLinearGradient(0,0,BG.canvas.width,0) : BG.ctx.createLinearGradient(BG.canvas.width,0,0,0);

                        _.gradient.addColorStop(0,'rgba('+_.sc+','+min+')');
                        _.gradient.addColorStop(_.sl,'rgba('+_.sc+','+max+')');


                        BG.ctx.fillStyle = _.gradient;
                        BG.ctx.fillRect(0,0,BG.canvas.width,BG.canvas.height);


                        //Gradient on Wall
                        if(BG.cube!==undefined && BG.cube.ctx) {
                            var rh = _.roomhelper!==undefined && _.roomhelper!==false ? (90-_.roomhelper.r)/90 : false;
                            min =  rh!==false ? rh : _.smi * _.helper.o;
                            max =  rh!==false ? rh : _.sma * _.helper.o;
                            BG.cube.ctx.clearRect(0,0,BG.cube.ctx.canvas.width,BG.cube.ctx.canvas.height);
                            _.gradientW = rh!==false ? _.d==="vertical" ? (_.t<0 && _.sdir===1) || (_.t>0 && _.sdir===-1) ?
                                                        BG.ctx.createRadialGradient(0,BG.cube.ctx.canvas.width/2,0,0,0,BG.cube.ctx.canvas.width*2)
                                                        : BG.ctx.createRadialGradient(BG.cube.ctx.canvas.width,0,0,0,0,BG.cube.ctx.canvas.width*2) :
                                                        (_.t>0 && _.sdir===1) || (_.t<0 && _.sdir===-1) ?
                                                        BG.ctx.createRadialGradient(BG.cube.ctx.canvas.width/2,BG.cube.ctx.canvas.height,0,BG.cube.ctx.canvas.width/2,BG.cube.ctx.canvas.height,BG.cube.ctx.canvas.width)
                                                        : BG.ctx.createRadialGradient(BG.cube.ctx.canvas.width/2,BG.cube.ctx.canvas.height*0.2,0,BG.cube.ctx.canvas.width/2,BG.cube.ctx.canvas.height*0.2,BG.cube.ctx.canvas.width)
                                            : _.d==="vertical" ? BG.ctx.createLinearGradient(0,0,0,BG.cube.ctx.canvas.height) : BG.ctx.createLinearGradient(0,0,BG.cube.ctx.canvas.width,0);

                            _.gradientW.addColorStop(0,'rgba('+_.sc+','+(rh!==false ? _.DIR==="a" ? max : 0 : _.DIR==="a" ? 0 : max )+')');
                            _.gradientW.addColorStop(1,'rgba('+_.sc+','+(rh!==false ? _.DIR==="a" ? 0 : max : _.DIR==="a" ? max : 0)+')');
                            BG.cube.ctx.fillStyle = _.gradientW;
                            BG.cube.ctx.fillRect(0,0,BG.cube.ctx.canvas.width,BG.cube.ctx.canvas.height);
                        }

                    },
                    setWall : function(_,t,p,d,c,first) {
                        _.TL = tpGS.gsap.timeline();
                        _.TL.add(tpGS.gsap.to(_.c,0.2,{display:"block"}),0);
                        if (p==="rotationX") {
                            _.ctx.canvas.width = d.w;
                            _.ctx.canvas.height = d.w;
                            _.TL.add(tpGS.gsap.set(_.w,{backgroundColor:c,width : d.w, height : d.w, transformOrigin : "50% 50% -"+d.w/2+"px",x:0,y: t>0 ? -(d.w-d.h) : 0,rotationX: t>0 ? -90 : 90,rotationY: 0}),0);
                        }
                        else {
                            _.ctx.canvas.width = first ? d.w : d.h;
                            _.ctx.canvas.height = d.h;
                            _.TL.add(tpGS.gsap.set(_.w,{backgroundColor:c,width: first ? d.w : d.h,height : d.h,transformOrigin : "50% 50% -"+(first ? d.w : d.h)/2+"px",x : t<0 ? (d.w-d.h) : 0,y : 0,rotationX : 0,rotationY : t>0 ? -90 : 90}),0);
                        }
                        return _.TL;
                    },
                    buildCube : function(BG) {
                        BG.cube = { c:document.createElement('div'),
                                    w:document.createElement('canvas')};
                        BG.cube.ctx =  BG.cube.w.getContext('2d');
                        BG.cube.c.className ="rs_fake_cube";
                        BG.cube.w.className ="rs_fake_cube_wall";
                        tpGS.gsap.set(BG.cube.c,{width:BG.mDIM.w, height:BG.mDIM.h});
                        tpGS.gsap.set(BG.cube.w,{width:BG.mDIM.w, height:BG.mDIM.h, backgroundColor:"#ccc"});
                        BG.cube.c.appendChild(BG.cube.w);
                        BG.sbg.appendChild(BG.cube.c);

                    },
                    cubeTL : function(id,_,BG,dir) {
                        if (_.f==="none" || _.f===undefined) return;

                        BG.sbg.style.transformStyle = "preserve-3d";
                        var TL = tpGS.gsap.timeline(),
                            zDirection = _.f==="incube" ? 1 : -1,
                            isCube = _.f==="incube" || _.f==="cube",
                            rD = _.f==="fly" ? -30 : 90,
                            tilt = _.f!=="turn" && _.t!==false && (_R_is_Editor || _R[id].firstSlideAnimDone===true),
                            roomZ =  _.z*-1,
                            frame_Canvas_from = {},
                            frame_Z_from = {z:tilt ? 0 : roomZ ,ease:"power1.inOut"},
                            frame_Canvas_to = {ease:_.e},
                            frame_Z_to = {z:0,ease:"power1.inOut"},
                            targets = [BG.canvas],
                            canvasOrigin = isCube ? "50% 50% " : "20% 20% ",
                            canvasRotation = "rotationX",
                            roomRotation = "rotationY",
                            canvasMove = "y",
                            canvasDistance = "height",
                            canvasDuration = _.fd,
                            DELAY = 0;

                        if (_.d!=="vertical"){
                            canvasRotation = "rotationY";
                            roomRotation = "rotationX";
                            canvasMove = "x";
                            canvasDistance = "width";
                            _.DIR = _.sdir === 1 ? "b" : "a";
                        } else {
                            _.DIR = _.sdir === 1 ? "a" : "b";
                        }
                        canvasDistance = canvasDistance==="width" ? "w" : canvasDistance==="height" ? "h" : canvasDistance;

                        if (_.f==="turn") {
                            rD = _.d==="vertical" ? -120 : 120;
                            canvasOrigin = _.d==="vertical" ? _.sdir===1 ? dir==="in" ? "0% 0% 0%" : "0% 100% 0%" : dir==="in" ? "0% 100% 0%" : "0% 0% 0%" :
                                                                _.sdir===1 ? dir==="in" ? "0% 0% 0%" : "100% 0% 0%" : dir==="in" ? "100% 0% 0%" : "0% 0% 0%";

                            frame_Z_from.z = 0;
                            frame_Canvas_to.ease = dir==="out" ? "power3.out" : frame_Canvas_to.ease;
                            canvasDuration = dir==="out" ? canvasDuration/2 : canvasDuration;
                        } else {
                            canvasOrigin +=(zDirection*BG.mDIM[canvasDistance]/2 )+"px";
                        }

                        frame_Canvas_to[canvasRotation] = 0;
                        frame_Canvas_to[canvasMove] = 0;
                        if (dir==="in") frame_Canvas_from[canvasRotation] = rD * _.sdir; else frame_Canvas_to[canvasRotation] = -rD * _.sdir;


                        if (_.f==="fly") {
                            var fR = _.fz===undefined ? Math.random()*20-10 : parseInt(_.fz);
                            if (dir==="in") {
                                frame_Canvas_from[canvasMove] = BG.mDIM[canvasDistance]*(_.fdi===undefined ? 1.5 : parseFloat(_.fdi)) * _.sdir;
                                frame_Canvas_from.rotateZ = _.sdir * fR;
                                frame_Canvas_to.rotateZ = 0;
                            } else {
                                frame_Canvas_to[canvasMove] = -1 * (BG.mDIM[canvasDistance]*(_.fdo===undefined ? 2 : parseFloat(_.fdo)) * _.sdir);
                                frame_Canvas_to.rotateZ = _.sdir * fR * -1;
                            }
                        }

                        BG.sbg.style.perspective = tilt ? "2500px" : "1500px";

                        // If Tilt set, need to change the perspective of parrents also
                        if (tilt) {
                            var frame_Room_Start = {z:roomZ*(_.f==="fly" ? 1.5 : 3),ease:"power1.inOut"},
                                frame_Room_End = {z:0,ease:"power1.inOut"};
                            frame_Room_Start[roomRotation] = -1*_.t;
                            frame_Room_End[roomRotation] = 0;
                            _.roomhelper = {r:0};
                            TL.add(tpGS.gsap.set(_R_is_Editor ? RVS.SBGS[RVS.S.slideId].wrap : BG.wrap[0],{perspective:1200, transformStyle:"preserve-3d", transformOrigin:canvasOrigin}),0);
                            TL.add(tpGS.gsap.to(BG.sbg,_.md*3,frame_Room_Start),0);
                            TL.add(tpGS.gsap.to(BG.sbg,_.md*3,frame_Room_End),(canvasDuration-_.md));

                            //Gradients Opacity on Wall
                            TL.add(tpGS.gsap.to(_.roomhelper,_.md*3,{r:Math.abs(_.t)}),0);
                            TL.add(tpGS.gsap.to(_.roomhelper,_.md*3,{r:0}),(canvasDuration-_.md));

                            // Building the Cube
                            if (dir==="in" && zDirection!==1 && isCube) {
                                if (BG.cube===undefined)  _R.transitions.d3.buildCube(BG);
                                TL.add(_R.transitions.d3.setWall(BG.cube,frame_Room_Start[roomRotation],roomRotation,BG.mDIM,_.c),0);
                                targets.push(BG.cube.c);
                            }
                        } else {
                            _.roomhelper=false;
                            TL.add(tpGS.gsap.set(_R_is_Editor ? RVS.SBGS[RVS.S.slideId].wrap : BG.wrap[0],{perspective:"none", transformStyle:"none", transformOrigin:"50% 50%"}),0);
                            if (!_R_is_Editor && _R[id].firstSlideAnimDone!==true && isCube) {
                                if (BG.cube===undefined)  _R.transitions.d3.buildCube(BG);
                                TL.add(_R.transitions.d3.setWall(BG.cube,frame_Canvas_from[canvasRotation],canvasRotation,BG.mDIM,_.c,true),0);
                                TL.add(tpGS.gsap.fromTo(BG.cube.w,_.md*4,{opacity:0},{opacity:1}),0);
                                targets.push(BG.cube.c);
                            }
                        }
                        //Gradients Opacity
                        _.helper = {o:0, oo:1};
                        TL.add(tpGS.gsap.to(_.helper,canvasDuration,{o:1, oo:0, ease:_.e}),_.md+DELAY);

                        TL.add(tpGS.gsap.set(targets,jQuery.extend(true,{},frame_Canvas_from,{force3D:true,transformOrigin:canvasOrigin})),0);
                        if (_.f!=="turn") TL.add(tpGS.gsap.to(targets,_.md*3,frame_Z_from),0);
                        TL.add(tpGS.gsap.to(targets,canvasDuration,frame_Canvas_to),_.md+DELAY);
                        if (_.f!=="turn") TL.add(tpGS.gsap.to(targets,_.md*3,frame_Z_to),(canvasDuration-_.md));
                        if (dir==="out" && zDirection!==1) TL.add(tpGS.gsap.to(targets,_.md*2,{opacity:0}),(_.dur - _.md*2));

                        return TL;
                    }
                }
            },


            ////////////////////////////////////////////
            // 		Animate The Slide In/Out Effect  //
            ////////////////////////////////////////////
            animatedCanvasUpdate : function(id,BG) {
                // ANIMATION PREPARATION
                BG.cDIMS = _R.getBGCanvasDetails(id,BG);

                BG.canvas.style.backgroundColor = "transparent";
                BG.canvas.style.opacity = 1;

                if (BG.canvas.width!==BG.mDIM.width) BG.canvas.width = BG.mDIM.width;
                if (BG.canvas.height!==BG.mDIM.height) BG.canvas.height = BG.mDIM.height;

                if(!_R_is_Editor && _R[id].clearModalBG === true) {
                    BG.ctx.clearRect(0, 0, BG.canvas.width, BG.canvas.height);
                    _R[id].clearModalBG = false;
                    BG.sbg.parentNode.style.opacity = 1
                }

                BG.col = BG.col || 1;
                BG.row = BG.row || 1;
                BG.SLOT = jQuery.extend(true,{s:{},c:{}},slotSize(id,BG.col,BG.row, BG.mDIM,"OW","OH"));
                BG.SLOT.DX = 0-BG.SLOT.OW/2;
                BG.SLOT.DY = 0-BG.SLOT.OH/2;
                BG.row=Math.ceil(BG.mDIM.height/BG.SLOT.OH) || 1;

                //FURTHER ADJUSTEMENTS
                if (BG.callFromAnimatedCanvasUpdate!==undefined) BG.callFromAnimatedCanvasUpdate();


            },

            slideAnimFinished : function(id,BG,params,ignoreClear) {
                if (BG===undefined) return;
                //Clear BG of Outgoing Canvas to make sure not disturbing next time
                if (BG.bgvid!==undefined && BG.bgvid.length>0 && params.direction==="out") {
                    BG.drawVideoCanvasImagesRecall = false;
                    _R.stopVideo(BG.bgvid,id);
                    BG.bgvid[0].style.display="none";
                    BG.bgvid[0].style.zIndex=0;
                }

                if (params.direction==="in") {
                    _R.transitions.motionFilter.complete(BG);
                    BG.ctx.canvas.style.filter = "none";
                    tpGS.gsap.set(params.slide,{zIndex:20});
                    delete BG.animateDirection;
                    if (BG.bgvid.length>0) {
                        if (!BG.isHTML5) {
                            _R.resetVideo(BG.bgvid,id);
                            _R.playVideo(BG.bgvid,id,true);
                        }
                        tpGS.gsap.set(BG.bgvid[0],{zIndex:30, display:"block",opacity:1});
                    }
                }
                if (params.direction==="out") {
                    tpGS.gsap.set(params.slide,{zIndex:10});
                    tpGS.gsap.set(BG.canvas,{rotationX:0, rotationY:0, rotationZ:0,x:0,y:0,z:0,opacity:1});
                    BG.currentState=undefined;
                } else  BG.currentState="idle";

                if (BG.cube!==undefined) BG.cube.c.style.display="none";
                if (params.direction==="in") {
                    //Recalculate it for better DPR
                    _R.updateSlideBGs(id,BG.skeyindex, BG);
                    //Update for good DPR the PanZoom Also
                    if (BG.panzoom!==undefined && !_R_is_Editor) _R.startPanZoom(_R[id].pr_next_bg,id,(_R[id].panzoomTLs[BG.skeyindex]!==undefined ? _R[id].panzoomTLs[BG.skeyindex].progress() : 0),BG.skeyindex,'play', BG.key);
                    // Clear the Rectangle of Outgoing Canvas
                    if (params.BG!==undefined && ignoreClear!==true) params.BG.ctx.clearRect(0,0,BG.canvas.width*2, BG.canvas.height*2); // Clear the Out BG
                }

                //if (_R_is_Editor && RVS.TL[RVS.S.slideId].slideRepeat) RVS.TL[RVS.S.slideId].slide.progress(0).play();
            },

            animateCore : function(id,BG,_,params) {
                var canvas = BG.canvas,
                    ctx = BG.ctx,
                    i,j,c=0, amnt,boxes;
                BG.col = _.col;
                BG.row = _.row;

                // REMOVE WEBGL CONTAINERS IN CASE WE ARE IN EDIROR MODE
                if (_R_is_Editor && BG.three) {
                    BG.canvas.style.display="block";
                    while(BG.three.scene.children.length > 0){
                        BG.three.scene.remove(BG.three.scene.children[0]);
                    }
                    BG.three.canvas.parentNode.removeChild(BG.three.canvas);
                    BG.three = 	undefined;
                }

                _R.animatedCanvasUpdate(id,BG);
                _.row = BG.row;

                BG.animateDirection = params.direction;
                params.delay = params.delay===undefined ? 0 : params.delay;
                amnt = _.col * _.row,
                boxes = Array(amnt);

                // Build a Sprite for Extra BG
                if (BG.help_canvas===undefined && params.direction==="out" && params.bgColor!==undefined) {
                    BG.help_canvas = document.createElement('canvas');
                    BG.help_ctx = BG.help_canvas.getContext('2d');
                }

                // Color the Sprite Background (For Fade Through Effects)
                if (params.direction==="out" && params.bgColor!==undefined) {
                    BG.help_canvas.width = BG.mDIM.width;
                    BG.help_canvas.height = BG.mDIM.height;
                    BG.help_ctx.fillStyle = params.bgColor;
                    BG.help_ctx.fillRect(0,0,BG.mDIM.width,BG.mDIM.height);
                }

                //Init Motion Filter
                _.mo = _R.transitions.motionFilter.init(BG,_.mo);

                //Duration
                _.dur = (_.ms/_.sec);

                //3D Preparation
                if (params.d3!==undefined) {
                    params.d3.dur = _.dur;
                    params.d3.fd = _.dur*0.7;
                    params.d3.md = _.dur*0.15;
                    params.d3.sdir = params.sdir;
                }

                BG.SLOT.c = { ws :0, hs : 0, wd : 0, hd : 0};
                if (_.mo>0 && _R_is_Editor) ctx.clearRect(0,0,canvas.width, canvas.height);

                // Main Animation
                var TL = tpGS.gsap.timeline({onUpdate:function() {

                    c = 0;
                    //Check Motion Filtler
                    if(_.mo > 0)
                        _R.transitions.motionFilter.render(BG, _.moo);
                    else
                        ctx.clearRect(0,0,canvas.width, canvas.height);

                    //Helper Background
                    if (BG.help_canvas && params.direction==="out") ctx.drawImage(BG.help_canvas,  0,0);

                    //Draw Filter
                    if((params.filter && params.filter.u) || !_R_is_Editor)
                        _R.transitions.filter.update(params.filter,ctx,BG.canvasFilterBlur);

                    if (_R_is_Editor && (_.zIndex!==0 && _.zIndex!==undefined)) tpGS.gsap.set(params.slide,{zIndex:_.zIndex});


                    if (BG.shadowCanvas!==undefined)
                        for(i=0;i<_.col;i++) {
                            BG.SLOT.SX = BG.SLOT.OW*i;
                            BG.SLOT.tw = BG.SLOT.OW*(i+0.5);

                            BG.SLOT.c.wd = BG.mDIM.width - (BG.SLOT.tw+BG.SLOT.DX+BG.SLOT.OW);
                            BG.SLOT.c.wd = (BG.SLOT.c.wd<0 ? BG.SLOT.c.wd : 0);
                            BG.SLOT.DW = BG.SLOT.SW = BG.SLOT.OW+BG.SLOT.c.wd;

                            for (j=0;j<_.row;j++) {
                                ctx.save();
                                var r =   -(Math.PI / 180) * boxes[c].r,
                                    csx = _.r!==0 ? Math.cos(r) * boxes[c].sx : boxes[c].sx,
                                    csy = _.r!==0 ? Math.cos(r) * boxes[c].sy : boxes[c].sy,
                                    ssx = _.r!==0 ? Math.sin(r) * boxes[c].sx : 0,
                                    ssy = _.r!==0 ? Math.sin(r) * -boxes[c].sy : 0;
                                BG.SLOT.SY = (BG.SLOT.OH*j);
                                BG.SLOT.th = BG.SLOT.OH*(j+0.5);

                                //Before Draw Specials
                                if (_R.transitions[params.effect] && _R.transitions[params.effect].beforeDraw) _R.transitions[params.effect].beforeDraw(id,ctx,boxes[c],BG);

                                if (_.m) { // MASK !?
                                    ctx.beginPath();
                                    ctx.rect(BG.SLOT.OW*i,  BG.SLOT.OH*j, BG.SLOT.OW, BG.SLOT.OH);
                                    ctx.clip();
                                }

                                ctx.transform(csx, ssx, ssy, csy, (BG.SLOT.tw+ boxes[c].x), (BG.SLOT.th+ boxes[c].y));
                                ctx.globalAlpha = Math.max(0,boxes[c].o);


                                BG.SLOT.c.hd = BG.mDIM.height - (BG.SLOT.th+BG.SLOT.DY+BG.SLOT.OH);
                                BG.SLOT.c.hd = (BG.SLOT.c.hd<0 ? BG.SLOT.c.hd : 0);
                                BG.SLOT.DH = BG.SLOT.SH = BG.SLOT.OH+BG.SLOT.c.hd;

                                if (BG.SLOT.SW>1 && BG.SLOT.SH>1)
                                    ctx.drawImage(BG.shadowCanvas,  BG.SLOT.SX, BG.SLOT.SY,
                                                                    BG.SLOT.SW , BG.SLOT.SH,
                                                                    BG.SLOT.DX, BG.SLOT.DY,
                                                                    BG.SLOT.DW, BG.SLOT.DH);
                                ctx.restore();
                                //After Draw Specials
                                if (_R.transitions[params.effect] && _R.transitions[params.effect].afterDraw)
                                    _R.transitions[params.effect].afterDraw(id,ctx,boxes[c],BG,{csx:csx,csy:csy,ssx:ssx,ssy:ssy});

                                c++;
                            }
                        }
                    if (params.d3!==undefined && params.d3.su) _R.transitions.d3.ticker(params.d3,BG,params.direction);
                    BG.currentState="animating";
                },


                onComplete:function() {
                    _R.slideAnimFinished(id,BG,params);
                }});

                // Protection against Gsap Bug
                if (_.col*_.row<2) _.f = "start";

                // Set Right zIndex on Elements
                if (_.zIndex!==0 && _.zIndex!==undefined) TL.add(tpGS.gsap.set(params.slide,{zIndex:parseInt(_.zIndex,0)}),0);

                _.m = _.m=="false" || _.m===false ? false : true;

                //SINGLE BOX ANIMATIONS
                if (params.direction==="in") {
                    for (i=0;i<amnt;i++) boxes[i] = { 	x:_R.SATools.getOffset(_.x,_.m ? BG.SLOT.OW :BG.mDIM.width,params.sdir,i),
                                                        y:_R.SATools.getOffset(_.y,_.m ? BG.SLOT.OH : BG.mDIM.height,params.sdir,i),
                                                        o:_R.SATools.getSpecialValue(_.o,i,params.sdir),
                                                        sx:_R.SATools.getSpecialValue(_.sx,i,params.sdir),
                                                        sy:_R.SATools.getSpecialValue(_.sy,i,params.sdir),
                                                        r:_.r!==0 ? _R.SATools.getSpecialValue(_.r,i,params.sdir) : 0
                                                    }
                    TL.add(tpGS.gsap.to(boxes, _.dur, { o:1, sx:1, sy:1, r:0,  x:0, y:0, ease:_.e, stagger:{amount:_.f==="nodelay" ? 0 : _.ms/_.stasec,grid:[_.col,_.row],from:_.f==="nodelay" ? "start" : _.f}}),params.delay);
                    //3D Cube, InCube Etc.

                    if (params.d3!==undefined) TL.add(_R.transitions.d3.cubeTL(id,params.d3,BG,"in"),0);

                    //Extend TimeLine with Filters
                    _R.transitions.filter.extendTimeLine(TL,params.filter,BG);
                } else {
                    for (i=0;i<amnt;i++) boxes[i] = {x:0, y:0, o:1, sx:1, sy:1, r:0 }
                    TL.add(tpGS.gsap.to(boxes, _.dur, {	o:function(i) { return _R.SATools.getSpecialValue(_.o,i,params.sdir)},
                                                                sx:function(i) { return _R.SATools.getSpecialValue(_.sx,i,params.sdir)},
                                                                sy:function(i) { return _R.SATools.getSpecialValue(_.sy,i,params.sdir)},
                                                                r:_.r!==0  && _.r!==undefined ? function(i) { return _R.SATools.getSpecialValue(_.r,i,params.sdir)} : 0,
                                                                x:function(i) { return _R.SATools.getOffset(_.x,_.m ? BG.SLOT.OW :BG.mDIM.width,params.sdir,i) * (_.reversed ? -1 : 1)},
                                                                y:function(i) { return _R.SATools.getOffset(_.y,_.m ? BG.SLOT.OH : BG.mDIM.height,params.sdir,i) * (_.reversed ? -1 : 1)},
                                                                ease:_.e,
                                                                stagger:{amount:_.f==="nodelay" ? 0 :_.ms/_.stasec,grid:[_.col,_.row],from:_.f==="nodelay" ? "start" : _.f}
                                                            }),params.delay + (_.outdelay!==undefined ? _.outdelay : 0));
                    if (params.d3!==undefined) TL.add(_R.transitions.d3.cubeTL(id,params.d3,BG,"out"),0);
                }

                // TL Extensions Due other Animations
                if (_R.transitions[params.effect] && _R.transitions[params.effect].extendTimeLine)
                    _R.transitions[params.effect].extendTimeLine(id,TL,boxes, _,params,BG.mDIM);

                if (_R_is_Editor) RVS.TL[RVS.S.slideId].slide.add(TL,0); else _R[id].mtl.add(TL,params.delay);
            }
        });



    var toDefNum = function(d,a) {
            return a!==undefined && _R.isNumeric(a) ? parseFloat(a,0) : a==undefined || a==="default" || a==="d" ? d : a;
        },

        //Calculate Slot Size
        slotSize = function(id, col, row,dim,w,h) {
            var r = {};
            r[w] = Math.ceil(dim.width/col);
            r[h] = _R_is_Editor ? Math.ceil(dim.height/row) : /*(_R[id].sliderLayout=="fullscreen" && _R[id].keepBGMaxHeightsOnFullscreen) ? Math.ceil(Math.max(!_R_is_Editor ? _R[id].module.height : 0,(_R.getWinH(id)-_R[id].fullScreenOffsetResult))/row) :*/ Math.ceil(dim.height/row);
            return  r;
        },

        MinOne = function(a) {
            return a===undefined || a===null || a===0 || a===NaN ? 1 : a;
        },

        //Get the Searched Animation
        getAnim = function(id,obj,sdir) {

            var	anim =  _R.transitions[obj.anim.e] !== undefined  && _R.transitions[obj.anim.e].getBasic!==undefined ? _R.transitions[obj.anim.e].getBasic() : _R.getBasic(),
                a = "";

            //MOVE OPTIONS
            anim.out = anim.out==undefined ? {} : anim.out;
            anim.out.reversed = obj.out!==undefined ? false : anim.out.reversed===undefined ? true : anim.out.reversed;

            // Translate Directsion from up,down,left,right to GBO(x) or to Definition due Animation Defaults or due Set x,y %s
            var in_dist = obj.iw!==undefined ? parseInt(obj.iw,0) : 100,
                out_dist = obj.ow!==undefined ? parseInt(obj.ow,0) : 100;

            // Collect Needed Attributes
            for (var i in anim.attr) {
                a = anim.attr[i];
                anim.in[a] = toDefNum(anim.in[a], obj.in[a]);  // Get Values from Object
                anim.out[a] = anim.out.reversed ? anim.in[a] : obj.out===undefined ? anim.out[a] : toDefNum(anim.out[a], obj.out[a]); // Get Values from Object
            }

            //Get Filters
            anim.filter = obj.filter!==undefined ? jQuery.extend(true,obj.filter,obj.filter) : anim.filter;



            //Extend, update Options
            if (_R.transitions[obj.anim.e] && _R.transitions[obj.anim.e].updateAnim) anim = _R.transitions[obj.anim.e].updateAnim(id,anim,sdir);
            anim.e = obj.anim.e;

            //ROW, COL min 1 !
            if (anim.in!==undefined) { anim.in.col = anim.in.col==="random" ? tpGS.gsap.utils.random(1,10,1) : MinOne(anim.in.col);anim.in.row = anim.in.row==="random" ? tpGS.gsap.utils.random(1,10,1) : MinOne(anim.in.row);}
            if (anim.out!==undefined) { anim.out.col = anim.out.col==="random" ? tpGS.gsap.utils.random(1,10,1) : MinOne(anim.out.col);anim.out.row = anim.out.row==="random" ? tpGS.gsap.utils.random(1,10,1) : MinOne(anim.out.row);}

            return anim;
        },

        // Prepate The Slide Animation
        prepareSlideAnimation = function(id,obj) {

            if (!_R_is_Editor) _R[id].duringslidechange = true;

            // GET THE TRANSITION
            var SDIR = _R_is_Editor ? -1 : _R[id].sc_indicator=="arrow" ? _R[id].sc_indicator_dir===undefined ? _R[id].sdir : _R[id].sc_indicator_dir : _R[id].sdir,
                NBGE = _R_is_Editor ? true : _R[id].pr_next_bg!==undefined && _R[id].pr_next_bg.length>0 && _R[id].pr_next_bg[0]!==undefined,
                CBGE = _R_is_Editor ? true : _R[id].pr_active_bg!==undefined && _R[id].pr_active_bg.length>0 && _R[id].pr_active_bg[0]!==undefined,

                nBG = _R_is_Editor ? RVS.SBGS[RVS.S.slideId].n : NBGE ? _R[id].sbgs[_R[id].pr_next_bg[0].dataset.key] : undefined,
                cBG = _R_is_Editor ? RVS.SBGS[RVS.S.slideId].c : CBGE ? _R[id].sbgs[_R[id].pr_active_bg[0].dataset.key] : undefined,
                _;

            SDIR = SDIR === 1 ? -1 : 1; //-1 -> Previous, 1 -> Next
            _ = jQuery.extend(true,{},getAnim(id,obj,SDIR));


            if (nBG.random!==undefined  && _R.SLTR!==undefined && cBG!==undefined) {
                delete cBG.help_canvas;
                delete cBG.help_ctx;
            }


            //GET NOT DEFAULTS
            _.ms = toDefNum(undefined,obj.anim.ms===undefined ? 1000 : obj.anim.ms);


            _.f = toDefNum(undefined,obj.anim.f);
            _.p = toDefNum(undefined,obj.anim.p);
            _.d = toDefNum(undefined,obj.anim.d);  // Delays in Staggers
            _.o = obj.anim.o;


            if (obj.d3!==undefined)  {
                obj.d3.t = obj.d3.t===undefined || obj.d3.t===0 ? false : obj.d3.t;
                obj.d3.su = obj.d3.su=="true" || obj.d3.su==true ? true : false;
                if (obj.d3.su) {
                    obj.d3.smi = obj.d3.smi===undefined ? 0 : parseFloat(obj.d3.smi);
                    obj.d3.sl = obj.d3.sl===undefined ? 1 : parseFloat(obj.d3.sl);
                    obj.d3.sma = obj.d3.sma===undefined ? 0.5 : parseFloat(obj.d3.sma);
                    obj.d3.sc = obj.d3.sc===undefined ? '0,0,0' : tpGS.gsap.utils.splitColor(obj.d3.sc).join(',');
                }
                _.p="none";
                if (_.in.row!==undefined && _.in.col!==undefined && _.in.row*_.in.col>200) _.filter = undefined;
            }


            // ADJUST SETTINGS
            _.in.sec = _.in.sec===undefined ? 1000 : _.in.sec;
            _.in.stasec = _.in.stasec===undefined ? _.d===undefined ? 1500 : _.d*100 : _.in.stasec;

            _.in.ms = _.ms==="default" || _.ms==="d" ? _.in.ms : _.ms==="random" ? Math.round(Math.random()*1000+300) : _.ms!=undefined ? parseInt(_.ms,0) : _.in.ms;
            _.out.ms = _.in.ms;
            if (_.filter!==undefined) {
                _.filter.ms = _.in.ms;
                _.filter.sec = _.in.sec;
                _.filter.e = _.filter.e===undefined || _.filter.e==="default"? _.in.e : _.filter.e;
            }

            // Also Write From into in and out
            _.in.f = _.f===undefined || _.f==="default" || _.f==="d" ? _.in.f : _.f;
            _.in.f = _.in.f ==="slidebased" ? SDIR==1 ? 'start' : 'end' :  _.in.f ==="oppslidebased" ? SDIR===1 ? 'end' : 'start' : _.in.f;
            _.out.f = _.in.f;
            _.out = jQuery.extend(true,{},_.in,_.out);

            // GET ENGINE
            _.in.eng = _.out.eng = obj.anim.eng;

            //PAUSE OPTIONS
            if (_.p!==undefined && _.p!=="none") {
                _.in.bg = _.p==="dark" ? '#000' : _.p==="light" ? '#fff' : 'transparent';
                _.out.delay = _.p!=="none" ? function(a,b) { return a/2.5} : 0;

                // If Outgoing Animation does not move at all, we can fade out to show BG of Incoming Animation
                if (_.out.o===1 && _.out.x===0 && _.out.y===0) _.out.o = 0;
            }



            // CHECK IF zINDEX NEED TO BE CHANGED !
            if (_.o==="forceinout") {
                _.in.zIndex = 20;
                _.out.zIndex = 10;
            } else
            if (_.o==="outin" || (_.in.o===1 && _.in.x===0 && _.in.y===0 && (obj.out!==undefined && (_.out.o!==1 || _.out.x!==0 || _.out.y!==0)))) {
                _.in.zIndex = 10;
                _.out.zIndex = 20;
            }


            // VIDEO NEED TO BE STOPPED DURING SLIDE ANIMATION AT SOME SLIDE ANIMATIONS
            if (nBG.bgvid.length>0) _.in = setBGVideoHandling(id,_.in,nBG,"in");
            if (CBGE && cBG.bgvid!==undefined && cBG.bgvid.length>0) _.out = setBGVideoHandling(id,_.out,cBG,"out");

            // FALL BACK TO SIMPLE ANIMATION IF YOUTUBE/VIMEO VIDEO EXISTS
            if (_.out!==undefined && (_.out.simplify || _.in.simplify)) _.out = simplify(_.out);
            if (_.in.simplify) _.in = simplify(_.in);

            // FIX PARALLAX CALCULATION IF NEEDED, BUT IGNORE LAYERS FOR THE MOMENT
            if (!_R_is_Editor) requestAnimationFrame(function() {_R.generalObserver(_R.ISM,true);});


            _.in.eng = _.in.eng===undefined ? "animateCore" : _.in.eng;
            _.out.eng = _.out.eng===undefined ? "animateCore" : _.out.eng;


            // Animate In and Out the Slide Background Content
            if (CBGE && _.out.skip!==true) _R[_.out.eng](id,cBG, _.out,{	effect:_.e,
                                                                                slide:_R_is_Editor ? RVS.SBGS[RVS.S.slideId].c.sbg : _R[id].pr_active_slide,
                                                                                direction:"out",
                                                                                delay:0,
                                                                                bgColor:_.in.bg,
                                                                                sdir:SDIR,
                                                                                filter:undefined,
                                                                                d3:obj.d3,
                                                                                addOns:_R_is_Editor ? obj.addOns : undefined
                                                                            });

            if (_.in.skip!==true) _R[_.in.eng](id,nBG,_.in, {	effect:_.e,
                                                                    slide:_R_is_Editor ? RVS.SBGS[RVS.S.slideId].n.sbg : _R[id].pr_next_slide,
                                                                    direction:"in",
                                                                    delay:CBGE ? typeof _.out.delay==="function" ? _.out.delay(_.in.ms/1000, _.out.row*_.out.col) : _.out.delay : _.in.delay,
                                                                    BG:cBG,
                                                                    outslide:_R_is_Editor ? RVS.SBGS[RVS.S.slideId].c.sbg : _R[id].pr_active_slide,
                                                                    sdir:SDIR,
                                                                    filter:_.filter,
                                                                    d3:obj.d3,
                                                                    addOns:_R_is_Editor ? obj.addOns : undefined
                                                                });

            return;
        },





        ///////////////////////////////////////////////
        ///	Get cover, contain and auto dimenstions //
        //////////////////////////////////////////////
        getContentDimensions = function(c,nBG,r) {


            function getPos(c,i,off) {
                var x,y;
                off = off.split(" ");
                if (off.length===1) off[1] = off[0];

                x = off[0]==="center" || off[0]==="50%" ? (c.width - i.width) / 2 :
                        off[0]==="left" ? 0 :
                        off[0]==="right" ? (c.width - i.width) :
                        !_R.isNumeric(off[0]) ? off[0].indexOf('%')>=0 ? (parseInt(off[0],0)/100 * c.width) - (parseInt(off[0],0)/100 * i.width): parseInt(off[0],0) : 0;

                y = off[1]==="center" || off[1]==="50%" ? (c.height - i.height) / 2 :
                        off[1]==="top" ? 0 :
                        off[1]==="bottom" ? (c.height - i.height) :
                        !_R.isNumeric(off[1]) ? off[1].indexOf('%')>=0 ? (parseInt(off[1],0)/100 * c.height) - (parseInt(off[1],0)/100 * i.height) : parseInt(off[1],0) : 0;
                return {x:x, y:y};
            }
            var mratio = c.height / c.width;

            r.ratio = r.conth / r.contw;
            // DRAW IMAGE (Center, Center)
            if ((r.ratio < mratio && nBG.bgfit === 'contain') || (r.ratio > mratio && nBG.bgfit === 'cover'))
                r.height = c.width * r.ratio;
            else
            if ((r.ratio > mratio && nBG.bgfit === 'contain') || (r.ratio < mratio && nBG.bgfit === 'cover'))
                r.width = c.width * mratio / r.ratio;
            else
            if (r.ratio===mratio && (nBG.bgfit === 'contain' || nBG.bgfit === 'cover')) r.width = c.width;
            else {
                var fit = nBG.bgfit.split(" ");
                if (fit.length===1) fit[1] = fit[0];
                r.width = fit[0]==="auto" ? r.contw : c.width * (parseInt(fit[0],0)/100);
                r.height = fit[1]==="auto" ? r.conth : r.width * r.ratio;
                nBG.usepattern=true;
            }
            var p = getPos(c, r, nBG.bgposition);
            r.x = p.x;
            r.y = p.y;

            return r;
        },


        ///////////////////////////////////////////////////////////////////////////////////////
        // Make complex Animations to normal Fade animations (for YouTuve and Vimeo Content) //
        ///////////////////////////////////////////////////////////////////////////////////////
        simplify = function(_) {
            _.o = 0; _.r = 0;_.row = 1;_.col = 1;_.x = 0;_.y = 0;_.sx = 1; _.sy = 1;
            return _;
        },

        tf = function(v) {
            if (v==="false" || v===false || v==="off" || v===undefined || v===0 || v===-1)
                v=false;
            else
                v=true;
            return v;
        },


        componentToHex = function(c) {
          var hex = c.toString(16);
          return hex.length == 1 ? "0" + hex : hex;
        },

        radToDeg = function(radians){
          var pi = Math.PI;
          return radians * (180/pi);
        },

        degToRad = function(degrees){
          var pi = Math.PI;
          return degrees * (pi/180);
        },

        //////////////////////////////////////////
        // Prepare Video Content for Animation	//
        //////////////////////////////////////////
        setBGVideoHandling = function(id,_,BG,dir) {
            _.skip = false;
            if (dir==="in") { // INCOMING VIDEO HANDLING
                if (BG.isHTML5) { //HTML5 Video
                    BG.bgvid[0].style.display="none";
                    _R.resetVideo(BG.bgvid,id);
                    BG.animateDirection = "in";
                    BG.currentState="animating";
                    BG.drawVideoCanvasImagesRecall=true;
                    _R.updateVideoFrames(id,BG,true);
                    _R.playVideo(BG.bgvid,id);
                } else {
                    _R[id].videos[BG.bgvid[0].id].pauseCalled = false;
                    _.waitToSlideTrans = _R[id].videos[BG.bgvid[0].id].waitToSlideTrans; //Wait Video Start until animation done ?

                    if (BG.poster!==true) { // HAS NO COVER ->
                        _R.resetVideo(BG.bgvid,id);
                        _R[id].videos[BG.bgvid[0].id].prePlayForaWhile = false; //Vimeo Video does Preload some frame to be able to jump on right Frame
                        if (_.waitToSlideTrans!==true) _R.playVideo(BG.bgvid,id,true);
                        tpGS.gsap.fromTo(BG.bgvid,_.ms/_.sec,{zIndex:30,display:"block",opacity:0},{opacity:1,zIndex:30,display:"block"});
                        BG.loadobj.bgColor = true;
                        BG.bgcolor = "#000";
                        _.simplify = true;
                    } else { // HAS COVER
                        _R[id].videos[BG.bgvid[0].id].prePlayForaWhile = false;
                        _R.resetVideo(BG.bgvid,id);
                        _R.playVideo(BG.bgvid,id);
                        BG.bgvid[0].style.display="none";
                        BG.bgvid[0].style.zIndex=0;
                        BG.bgvid[0].style.opacity=0;
                    }
                }
            } else
            if (dir==="out") {
                if (BG.isHTML5) { // HTML5 VIDEO
                    BG.currentState="animating";
                    BG.drawVideoCanvasImagesRecall=true;
                    _R.updateVideoFrames(id,BG,true);
                    window.requestAnimationFrame(function() {
                        tpGS.gsap.to(BG.bgvid,0.1,{zIndex:0,display:"none"});
                    });
                } else { // YOUTUBE, VIMEO VIDEO

                    _R.stopVideo(BG.bgvid,id,true);
                    if (BG.poster!==true) {
                        BG.loadobj.bgColor = true;
                        BG.bgcolor = "#000";
                    }
                }
            }
            return _;
        },

        dist = function(x1, y1, x2, y2){ return Math.sqrt( Math.pow((x1-x2),2) + Math.pow((y1-y2),2));},
        getPerpLine = function(p, ang){ var a = ang + Math.PI/2; return { x1: p.x, y1: p.y, x2: p.x + Math.cos(a) * 100, y2: p.y + Math.sin(a) * 100}},
        lineIntersect = function(l1, l2) {
            var A1 = l1.y2 - l1.y1,
                B1 = l1.x1 - l1.x2,
                C1 = A1 * l1.x1 + B1 * l1.y1,
                A2 = l2.y2 - l2.y1,
                B2 = l2.x1 - l2.x2,
                C2 = A2 * l2.x1 + B2 * l2.y1,
                denominator = (A1 * B2 - A2 * B1);

            return denominator === 0 ? false : {
                x: Math.round((B2 * C1 - B1 * C2) / denominator*100) / 100,
                y: Math.round((A1 * C2 - A2 * C1) / denominator*100) / 100
            }
        }
        //Support Defer and Async and Footer Loads
    window.RS_MODULES = window.RS_MODULES || {};
    window.RS_MODULES.slideanims = {loaded:true, version:version};
    if (window.RS_MODULES.checkMinimal) window.RS_MODULES.checkMinimal();

    })(jQuery);