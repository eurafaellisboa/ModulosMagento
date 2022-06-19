/********************************************
 * REVOLUTION EXTENSION - PANZOOM
 * @date: 23.04.2021
 * @requires rs6.main.js
 * @author ThemePunch
*********************************************/

(function($) {
    "use strict";


    // CHECK IF WE SCRIPT RUNNING IN EDITOR OR IN FRONTEND
    if (window._R_is_Editor) RVS._R = RVS._R===undefined ? {} : RVS._R; else window._R_is_Editor=false;

    jQuery.fn.revolution = jQuery.fn.revolution || {};

    var  _R = _R_is_Editor ? RVS._R : jQuery.fn.revolution, version = "6.2.0";

    ///////////////////////////////////////////
    // 	EXTENDED FUNCTIONS AVAILABLE GLOBAL  //
    ///////////////////////////////////////////
    jQuery.extend(true,_R, {

        bgW : function(id,slide) { return _R_is_Editor ? RVS.RMD.width : _R[id].sliderType==="carousel" ? _R[id].justifyCarousel ? _R[id].carousel.slide_widths[slide!==undefined ? slide : _R[id].carousel.focused] : _R[id].carousel.slide_width : _R[id].module.width;},
        bgH : function(id,slide) { return _R_is_Editor ? RVS.RMD.height :_R[id].sliderType==="carousel" ? _R[id].carousel.slide_height : _R[id].module.height;},
        getPZSides : function(w,h,f,cw,ch,ho,vo) {
            var tw = w * f,
                th = h * f,
                hd = Math.abs(cw-tw),
                vd = Math.abs(ch-th),
                s = new Object();
            s.l = (0-ho)*hd;
            s.r = s.l + tw;
            s.t = (0-vo)*vd;
            s.b = s.t + th;
            s.h = ho;
            s.v = vo;
            return s;
        },
        getPZCorners : function(d,cw,ch,o) {
            var p = d.bgposition.split(" ") || "center center",
                ho = p[0] == "center"  ? "50%" : p[0] == "left" || p [1] == "left" ? "0%" : p[0]=="right" || p[1] =="right" ? "100%" : p[0],
                vo = p[1] == "center" ? "50%" : p[0] == "top" || p [1] == "top" ? "0%" : p[0]=="bottom" || p[1] =="bottom" ? "100%" : p[1];

            ho = parseInt(ho,0)/100 || 0;
            vo = parseInt(vo,0)/100 || 0;

            var sides = new Object();

            sides.start = _R.getPZSides(o.start.width,o.start.height,o.start.scale,cw,ch,ho,vo);
            sides.end = _R.getPZSides(o.start.width,o.start.height,o.end.scale,cw,ch,ho,vo);


            return sides;
        },
        getPZValues : function(d) {
            var attrs = d.panzoom.split(";"),
                _ = {duration:10, ease:'none', scalestart:1, scaleend:1, rotatestart:0.01, rotateend:0, blurstart:0, blurend:0, offsetstart:"0/0", offsetend:"0/0"};


            for (var k in attrs) {
                if (!attrs.hasOwnProperty(k)) continue;
                var _bas = attrs[k].split(":"),
                    key = _bas[0],
                    val = _bas[1];
                switch (key) {
                    case "d": _.duration = parseInt(val,0) / 1000; break;
                    case "e": _.ease = val;break;
                    case "ss": _.scalestart=parseInt(val,0)/100;break;
                    case "se": _.scaleend=parseInt(val,0)/100;break;
                    case "rs": _.rotatestart=parseInt(val,0);break;
                    case "re": _.rotateend=parseInt(val,0);break;
                    case "bs": _.blurstart=parseInt(val,0);break;
                    case "be": _.blurend=parseInt(val,0);break;
                    case "os": _.offsetstart=val;break;
                    case "oe": _.offsetend=val;break;
                }
            }
            _.offsetstart = _.offsetstart.split("/") || [0,0];
            _.offsetend = _.offsetend.split("/") || [0,0];
            _.rotatestart = _.rotatestart===0 ? 0.01 : _.rotatestart;
            d.panvalues = _;

            d.bgposition = d.bgposition == "center center" ? "50% 50%" : d.bgposition;
            return _;
        },
        pzCalcL : function(cw,ch,d) {

            var c,iws,ihs,iwe,ihe,newf,
                _ = d.panvalues === undefined ? jQuery.extend(true,{},_R.getPZValues(d)) : jQuery.extend(true,{},d.panvalues),
                ofs = _.offsetstart,
                ofe = _.offsetend,
                o = {start:{
                                width:cw,
                                height:_R_is_Editor ?  cw / d.loadobj.width * d.loadobj.height : cw / d.owidth*d.oheight,
                                rotation:_.rotatestart,
                                scale:_.scalestart,
                                transformOrigin:"0% 0%" },
                     end:{	rotation:_.rotateend,
                             scale:_.scaleend },
                     },
                sw = cw*_.scalestart,
                sh = sw/d.owidth * d.oheight,
                ew = cw*_.scaleend,
                eh = ew/d.owidth * d.oheight;



            if (o.start.height<ch) {
                newf = ch / o.start.height;
                o.start.height = ch;
                o.start.width = o.start.width*newf;
            }

            if (_.rotatestart===0.01 && _.rotateend===0) {
                delete o.start.rotation;
                delete o.end.rotation;
            }



            // MAKE SURE THAT OFFSETS ARE NOT TOO HIGH
            c = _R.getPZCorners(d,cw,ch,o);


            ofs[0] = parseFloat(ofs[0]) + c.start.l;
            ofe[0] = parseFloat(ofe[0]) + c.end.l;

            ofs[1] = parseFloat(ofs[1]) + c.start.t;
            ofe[1] = parseFloat(ofe[1]) + c.end.t;

            iws = c.start.r - c.start.l;
            ihs	= c.start.b - c.start.t;
            iwe = c.end.r - c.end.l;
            ihe	= c.end.b - c.end.t;

            ofs[0] = ofs[0]>0 ? 0 : iws + ofs[0] < cw ? cw-iws : ofs[0];
            ofe[0] = ofe[0]>0 ? 0 : iwe + ofe[0] < cw ? cw-iwe : ofe[0];

            ofs[1] = ofs[1]>0 ? 0 : ihs + ofs[1] < ch ? ch-ihs : ofs[1];
            ofe[1] = ofe[1]>0 ? 0 : ihe + ofe[1] < ch ? ch-ihe : ofe[1];

            o.start.x = ofs[0];
            o.start.y = ofs[1];
            o.end.x = ofe[0];
            o.end.y = ofe[1];
            o.end.ease =  _.ease;
            return o;
        },

        pzDrawShadow : function(id,nBG,anim) {

            var _width = anim.start.width*anim.start.scale,
                _height = anim.start.height*anim.start.scale;

            nBG.shadowCTX.clearRect(0,0,nBG.shadowCanvas.width, nBG.shadowCanvas.height);
            nBG.shadowCTX.save();
            nBG.shadowCTX.translate(anim.start.x,anim.start.y);
            if (anim.start.rotation!==undefined)  nBG.shadowCTX.rotate(anim.start.rotation*Math.PI/180);
            nBG.shadowCTX.drawImage(nBG.loadobj.img, 0, 0, _width, _height);
            nBG.shadowCTX.restore();
            if (nBG.currentState!=="animating") {
                _R.updateSlideBGs(id,anim.slidekey,nBG,true);
                if (anim.start.blur!==undefined) nBG.canvas.style.filter = anim.start.blur===0 ? "none" : "blur("+anim.start.blur+"px)";
            } else
            if (anim.start.blur!==undefined && nBG.canvasFilter) nBG.canvasFilterBlur = anim.start.blur; else nBG.canvas.style.filter = anim.start.blur===0 ? "none" : "blur("+anim.start.blur+"px)";
        },

        startPanZoom :  function(l,id,prgs,cid,prepare,key) {
            var d = _R_is_Editor ? l : l.data();
            if (d.panzoom===undefined || d.panzoom===null) return;
            var	nBG = _R_is_Editor ? d : _R[id].sbgs[key];

            if (!_R_is_Editor && _R[id].sliderType==="carousel") {
                 if (_R[id].carousel.justify && _R[id].carousel.slide_widths===undefined) _R.setCarouselDefaults(id,true);
                 if (!_R[id].carousel.justify) {
                     if (_R[id].carousel.slide_width===undefined) _R[id].carousel.slide_width = _R[id].carousel.stretch!==true ? _R[id].gridwidth[_R[id].level]*(_R[id].CM.w===0 ? 1 : _R[id].CM.w) : _R[id].canv.width;
                     if (_R[id].carousel.slide_height===undefined) _R[id].carousel.slide_height = _R[id].carousel.stretch!==true ? _R[id].gridheight[_R[id].level]*(_R[id].CM.w===0 ? 1 : _R[id].CM.w) : _R[id].canv.height;
                 }
             }

            var hDIM = _R.getmDim(id,cid, nBG);
            var	anim = _R.pzCalcL(hDIM.width,hDIM.height,d),
                PZ;

            if (!_R_is_Editor) {
                _R[id].panzoomTLs = _R[id].panzoomTLs===undefined ? {} : _R[id].panzoomTLs;
                _R[id].panzoomBGs = _R[id].panzoomBGs===undefined ? {} : _R[id].panzoomBGs;
                if (_R[id].panzoomBGs[cid]===undefined) _R[id].panzoomBGs[cid] = l;
                PZ = _R[id].panzoomTLs[cid];
            }


            prgs = prgs || 0;

            if (PZ!==undefined) { PZ.pause(); PZ.kill(); PZ = undefined;}
            PZ =  tpGS.gsap.timeline({paused:true});

            d.panvalues.duration = d.panvalues.duration===NaN || d.panvalues.duration===undefined ? 10 : d.panvalues.duration;

            if (!_R_is_Editor && d!==undefined && nBG!==undefined) nBG.panvalues = d.panvalues;


            if (nBG!==undefined) {

                if (nBG.shadowCanvas===undefined) {
                    nBG.shadowCanvas = document.createElement('canvas');
                    nBG.shadowCTX = nBG.shadowCanvas.getContext('2d');
                    nBG.shadowCanvas.style.background = "transparent";
                    nBG.shadowCanvas.style.opacity = 1;
                }

                nBG.shadowCanvas.width = hDIM.width;
                nBG.shadowCanvas.height = hDIM.height;


                anim.slideindex = cid;
                anim.slidekey = _R_is_Editor ? undefined : key;

                _R.pzDrawShadow(id,nBG,anim);
                anim.end.onUpdate = function() {_R.pzDrawShadow(id,nBG,anim);}

                nBG.panStart = jQuery.extend(true,{},anim.start);


                if (d.panvalues.blurstart!==undefined && d.panvalues.blurend!==undefined &&  (d.panvalues.blurstart!==0 || d.panvalues.blurend!==0)) {
                    anim.start.blur = d.panvalues.blurstart;
                    anim.end.blur = d.panvalues.blurend;
                }

                PZ.add(tpGS.gsap.to(anim.start,d.panvalues.duration,anim.end),0);
                PZ.progress(prgs);

                if (prepare==='play' || prepare==="first") PZ.play();

                if (!_R_is_Editor) _R[id].panzoomTLs[cid] = PZ; else RVS.TL[RVS.S.slideId].panzoom=PZ;

            }
        }
    });
    window.RS_MODULES = window.RS_MODULES || {};
    window.RS_MODULES.panzoom = {loaded:true, version:version};
    if (window.RS_MODULES.checkMinimal) window.RS_MODULES.checkMinimal();


    })(jQuery);