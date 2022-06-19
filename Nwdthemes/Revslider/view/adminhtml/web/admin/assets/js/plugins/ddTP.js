/*!
 * ThemePunchs Own Select2RS Library
 * @version: 2.0 (25.03.2021)
 * @author ThemePunch
*/


(function(jQuery,undefined){
"use strict";

    var listeners,
        globRS,
        KEYS = {
        BACKSPACE: 8,
        TAB: 9,
        ENTER: 13,
        SHIFT: 16,
        CTRL: 17,
        ALT: 18,
        ESC: 27,
        CMD:227,
        CMDR:91,
        CMDL:93,
        SPACE: 32,
        PAGE_UP: 33,
        PAGE_DOWN: 34,
        END: 35,
        HOME: 36,
        LEFT: 37,
        UP: 38,
        RIGHT: 39,
        DOWN: 40,
        DELETE: 46
      },
    DOC = jQuery(document);   
    window.ddTPCalls = ["destroy","update","rebuild","change","main"];
    window.ddTP_R_cache = {};
    window.ddTPcache = {};    

/*
 DropDown - ThemePunch Core
 */
    jQuery.fn.extend({
        //FALL BACK TO OLD CALLS
        select2RS : function(_,params) {                  
            return jQuery(this).ddTP(_,params);
        },

        /* DROP DOWN INIT, UPDATE, DESTROY */
        ddTP : function(_,params) {       
            if (_=="destroy" || _=="udate" || _=="rebuild" || _=="change") {
                window.ddTP_R_cache[_] = window.ddTP_R_cache[_]===undefined ? {list:[],params:[]} : window.ddTP_R_cache[_];
                window.ddTP_R_cache[_].list.push(this);
                window.ddTP_R_cache[_].params.push(params);
            } else {
                 window.ddTP_R_cache.main = window.ddTP_R_cache.main===undefined ? {list:[],params:[]} : window.ddTP_R_cache.main;
                window.ddTP_R_cache.main.list.push(this);
                window.ddTP_R_cache.main.params.push(_);
            }
           if ( window.ddTP_R_RFACalled===undefined) {
                window.ddTP_R_RFACalled=true;
                requestAnimationFrame(function() {
                    var call,i,j;
                    for (j=0;j<window.ddTPCalls.length;j++) {
                        call = window.ddTPCalls[j];                        
                        if (window.ddTP_R_cache[call]!==undefined) 
                            for (i in window.ddTP_R_cache[call].list) 
                                ddTPRFA(window.ddTP_R_cache[call].list[i],call,window.ddTP_R_cache[call].params[i]);
                    }                    
                    window.ddTP_R_cache = {};    
                    delete window.ddTP_R_RFACalled;
                });
                
            }
            if (listeners===undefined) buildListeners();
            return this;
        }       
    });

     var ddTPRFA = function(list,_,params) {   
            
            var  i;
            if (_=="destroy") {
                for (i in list) {
                    if (!list.hasOwnProperty(i) || list[i]===undefined || list[i].tagName!=="SELECT" || list[i].id===undefined || list[i].id==="" || ddTPcache[list[i].id]===undefined) continue;
                    destroy(list[i]);
                }
            } else 
            if (_=="update") {  // UPDATE CURRENT FOCUSED LIST
                for (i in list) {                    
                    if (!list.hasOwnProperty(i) || list[i]===undefined || list[i].tagName!=="SELECT" || list[i].id===undefined || list[i].id==="" ||  ddTPcache[list[i].id]===undefined || globRS.INFOCUS!==list[i].id) continue;                    
                    openSelectModal(list[i].id,true);                    
                }
            } else
            if (_=="rebuild") {
                for (var i in list) {
                    if (!list.hasOwnProperty(i) || list[i]===undefined || list[i].tagName!=="SELECT" || (list[i].id===undefined && list[i].id==="")) continue;
                        destroy(list[i]);
                        buildSelect(list[i],params);                 
                }                                
            } else 
            if (_=="change") {
                for (var i in list) {                                
                    if (!list.hasOwnProperty(i) || list[i].id===undefined || list[i].id==="" || ddTPcache[list[i].id]===undefined) continue;
                    setFakeValues(ddTPcache[list[i].id].rendered,getValueTexts(list[i],ddTPcache[list[i].id].params));
                }
            } else
            
            for (var i in list) {
                if (!list.hasOwnProperty(i) || list[i]===undefined || list[i].tagName!=="SELECT" || (list[i].id!==undefined && list[i].id!=="" && ddTPcache[list[i].id]!==undefined)) continue;
                buildSelect(list[i],params);
            }
            

            
                                                     
        }

    /* 
    BUILD DROP DOWN SELECT BOX 
    */
    var buildSelect = function(sel,params) {   

            if (sel.id===undefined || sel.id==="") sel.id = 'ddTP_'+Math.round(Math.random()*10000000000);         
            sel.classList.add('ddTP_hidden');
            sel.classList.add('ddTP_based');

            /* CREATE CONTAIERS */
            
            var cw = ddTPcache[sel.id]===undefined ? document.createElement('span') : ddTPcache[sel.id].container,
                c = document.createElement('span'),
                s = document.createElement('span'),
                r = document.createElement(sel.multiple ? 'ul' : 'span');
                
            cw.className = "ddTP ddTP_C ddTP-fake"+(sel.dataset.theme!==undefined ? " ddTP_C--"+sel.dataset.theme : "");
            c.className = "selection";
            s.className = "ddTP_S ddTP_S--"+(sel.multiple ? 'multiple' : 'single');
            r.className = "ddTP_S__rendered";
                       
            cw.dataset.refid = sel.id;
            c.dir = 'ltr';            
            c.appendChild(s);
            s.appendChild(r);

            /* CREATE AND ADD DROP DOWN ICON */
            if (!sel.multiple) {
                var bw = document.createElement('span'),
                b = document.createElement('b');
                bw.className = "ddTP_S__arrow";
                s.appendChild(bw);
                bw.appendChild(b);
            }
            
            setFakeValues(r,getValueTexts(sel,params));           
            cw.appendChild(c);            
            insertAfter(cw,sel);                    
            ddTPcache[sel.id] = {                  
                sel : sel,              
                container: cw,
                innerwrapper : c,
                selection: s,
                rendered: r,
                params: (params===undefined ? {} : params),
            }            
        },

    /*
    DESTROY DROP DOWN SELECT BOX 
    */
    destroy = function(sel) {        
        if(ddTPcache==undefined || ddTPcache[sel.id]==undefined ||  ddTPcache[sel.id].container==undefined) return;                
        ddTPcache[sel.id].container.parentNode.removeChild(ddTPcache[sel.id].container);
        ddTPcache[sel.id].container.innerHTML = "";
        delete ddTPcache[sel.id];
    },

    /* 
    CHANGE TAGS, MULTIPLE SELECTED VALUES, VALUE IN THE FAKE BOX 
    */
    setFakeValues = function(r,v) {        
        requestAnimationFrame(function() {
            if (r.tagName==="UL") {
                r.innerHTML = "";
                r.appendChild(v.values);
            } 
            else if (v.html) r.innerHTML = v.values; 
            else r.innerText = v.values;
            
        });
    },

    getPreData = function(p,ds,v) {                
        return (ds===undefined || p===undefined ? undefined : 
                p.preData!==undefined ? ds[p.preData] : 
                p.preDataFunction!==undefined ? p.preDataFunction(v) :
                undefined);        
    },
    
    /* 
    COLLECT VALUE, MULTI VALUES, TAGS AND ADD SEARCH BOX IF NEEDED INTO MULTI BOX 
    */
    getValueTexts = function(sel,params) { 
        if (sel.multiple) {
            var values = document.createDocumentFragment(),
                added=false;

            for (var i in sel.options) 
                if (sel.options[i]!==undefined && sel.options[i].selected) {
                    added = true;                    
                    values.appendChild(createResultLi(sel.options[i].value,sel.options[i].text===undefined ? sel.options[i].value : sel.options[i].text, undefined,sel.id, params,getPreData(params,sel.options[i].dataset,sel.options[i].value)));                    
                }

            if (params.tags || params.search) {
                    globRS.liSearch = document.createElement('li');
                    globRS.liSearch.className = "ddTP_SCH ddTP_SCH--inline";
                    globRS.liSearch.appendChild(createSearch(params.searchValue,sel.id));                  
                    values.appendChild(globRS.liSearch);     
                    added = true;   
            }

            if (!added) values.appendChild(createResultLi(undefined,params===undefined || params.placeholder===undefined ? "" : params.placeholder,true,sel.id,params,getPreData(params,sel.options[i].dataset,sel.options[i].value)));

            return {values:values};        
        } else {
            var values = "",iTB,pre,ishtml = false;
                
            for (var i in sel.options) 
                if (i!==sel.options[i].id && sel.options[i].selected) {                    
                    pre = getPreData(params,sel.options[i].dataset,sel.options[i].value);
                    iTB = pre!==undefined ? getiTB(params,  pre) : undefined;
                    if (iTB!==undefined) ishtml =true;                    
                    values+= (iTB===undefined ? "" : iTB) + (sel.options[i].text===undefined ? sel.options[i].value : sel.options[i].text);                    
                }        
            return { html:ishtml, values:(values==="" ? params===undefined || params.placeholder===undefined ? "" : params.placeholder : values)};
        }
    },

   

    /* 
    CREATE SEARCH INPUT 
    */
    createSearch = function(val,id) {
        var s = document.createElement('input');
        
        s.className = "ddTP_SCH__field";
        s.dataset.refid = id;        
        s.tabindex="0";
        s.autocomplete="off";
        s.autocorrect="off";
        s.autocapitalize="none";
        s.spellcheck="false";
        s.role="searchbox";
        s.ariaAutocomplete="list";
        s.placeholder="";                    
        s.type = "search";
        
        if (val!==undefined && val.length>0) {
            s.value = val;
            s.style.width = ((s.value.length+1) * 0.75) + 'em';
        } else 
        s.style.width = "0.75em";
        return s;
    },


   
    /* 
    CREATE THE GLOBAL DROPDOWN CONTAINER 
    */
    createGlobalElement = function() {
        var frag = document.createDocumentFragment();
        globRS = {
            wrap : document.createElement('span'),                    
            drop : document.createElement('span'),
            searchw : document.createElement('span'),
            searchi : document.createElement('input'),            
            result : document.createElement('span'),
            ul : document.createElement('ul')
            
        };

        
        globRS.wrap.className = "ddTP_C ddTP_C--default";
        globRS.drop.className = "ddTP-dropdown ddTP-dropdown--below";
        globRS.searchw.className = "ddTP_SCH ddTP_SCH--dropdown";
        globRS.searchi.className = "ddTP_SCH__field";

        globRS.result.className = "ddTP_R";
        globRS.ul.className = "ddTP_ROs";
        globRS.drop.dir = "ltr";
        globRS.searchi.type ="search";

        globRS.searchw.appendChild(globRS.searchi);
        globRS.wrap.appendChild(globRS.drop);
        globRS.drop.appendChild(globRS.searchw);
        globRS.drop.appendChild(globRS.result);
        globRS.result.appendChild(globRS.ul);
        frag.appendChild(globRS.wrap);
        


        globRS.wrap.style.display = "none";
        document.body.appendChild(frag);
    },

    

    getiTB = function(params,data) {
        return params!==undefined &&  params.pre!==undefined ? params.pre.replace('#data#',data) : undefined;
    },
    /*     
    CREATE A SINGLE LI CONTAINER WITH CONTENT, REMOVE BUTTON 
    */
    createResultLi = function(val,txt,isplaceholder,selid,params,ds) {        
        var li = addLi({
                c:"ddTP_S__choice"+(isplaceholder ? " isplaceholder" : ""), 
                v:val, 
                id:selid, 
                iH:txt || val, 
                iTB: getiTB(params,ds)
            });                
        if (!isplaceholder) {
            var re = document.createElement('span');
            re.className = "ddTP_S__choice__remove";            
            re.innerText = "x";
            re.dataset.refid = selid;
            li.appendChild(re);
        }        
        return li;
    },

    /* 
    CLOSE THE OPENED GLOBAL CONTAINER 
    */
    closeSelectModal = function() {
        if (globRS.INFOCUS==undefined) return;
        
        // CLOSE OPENED MODAL
        var _ = window.ddTPcache[globRS.INFOCUS];
        globRS.wrap.classList.remove('ddTP_C--open');        
        _.container.classList.remove('ddTP_C--open');
        _.container.classList.remove('ddTP_C--focus');
        if (globRS.liSearch!==null && globRS.liSearch!==undefined) globRS.liSearch.style.display="none";
        tpGS.gsap.set(globRS.wrap, {display: 'none'});
        
        delete globRS.timeStamp;
        delete globRS.INFOCUS;
        delete globRS.highlighted;
        removeGlobalListener();


    },

    // UPDATE POSITION OF DROPDOWN
    updatePositionOfModal = function() {
        if (globRS.timeStamp===undefined || globRS.INFOCUS===undefined || window.ddTPcache[globRS.INFOCUS]===undefined || window.ddTPcache[globRS.INFOCUS].container===undefined) return;
        
        var rect = window.ddTPcache[globRS.INFOCUS].container.getBoundingClientRect();           
            tpGS.gsap.set(globRS.wrap,{                
                    x:(rect.left + globRS.drop.offsetWidth > window.innerWidth ? rect.left + ((window.innerWidth-25)-(rect.left + globRS.drop.offsetWidth)) : rect.left),
                    y:(window.pageYOffset + rect.bottom + globRS.drop.offsetHeight > (window.pageYOffset + window.innerHeight - 50) ? window.pageYOffset + rect.top - globRS.drop.offsetHeight : rect.bottom+window.pageYOffset)});
                 
        requestAnimationFrame(function() {
            updatePositionOfModal();
        });
        
    },

    scrollIntoHighlitedPosition = function() {   
        if (globRS.highlighted!==undefined && globRS.highlighted!==null) {        
            if ((globRS.ul.scrollTop+50)>globRS.highlighted.offsetTop) globRS.ul.scrollTop = Math.max(0,globRS.highlighted.offsetTop-100);                
            else if ((globRS.ul.scrollTop-300)<globRS.highlighted.offsetTop) globRS.ul.scrollTop = globRS.highlighted.offsetTop-200;            
        }
    },

    addLi = function(_) {        
        var li = document.createElement('li');        
        if (_.group!==undefined) {            
            li.innerHTML = '<strong class="ddTP_R__group">'+_.group+'</strong>';
            return li;
        } 
        if (_.c) li.className = _.c;
        if (_.v) li.dataset.val = _.v;
        if (_.iTB || _.iTA) li.innerHTML = (_.iTB===undefined ? "" : _.iTB) + (_.iT===undefined ? _.iH : _.iT) + (_.iTA===undefined ? "" : _.iTA);
        else if (_.iT) li.innerText = _.iT;
        else if (_.iH) li.innerHTML = _.iH;
        if (_.id) li.dataset.refid = _.id;        
        if (_.optid!==undefined) li.dataset.optid = _.optid;        
        if (_.aS) li.ariaSelected = _.aS;
        return li;
    },

    addLiHelper = function(s,i,p,id) {        
        return addLi({c:"ddTP_RO"+(globRS.highlighted===undefined && s.selected ? " ddTP_RO--highlighted" : ""), 
                        v:s.value, id:id, aS:s.selected,
                        iT:s.text===undefined || s.text==="" ? s.value : s.text,
                        iTB: getiTB(p, getPreData(p,s.dataset,s.value)),
                        optid:i,
                        group:s.tagName==="OPTGROUP" ? s.label : undefined                            
                }); 
    },

    /* 
    OPEN GLOBAL SELECT BOX CONTAINER , DRAW AVAILABLE LIST ELEMENTS
    */
    openSelectModal = function(id,updateFakeValues) {                
        // OPEN MODAL        
        var _ = window.ddTPcache[id],            
            rect = _.container.getBoundingClientRect(),
            frag = document.createDocumentFragment(),
            sresult = "",
            li;

        _.params = _.params===undefined ? {} : _.params;
        _.params.tags = _.sel.dataset.tags=="true" ? true : _.params.tags;
        _.params.search =  _.params.search===undefined ? ((' '+_.sel.className).indexOf(' searchbox')>=0) : _.params.search; 
        
        globRS.highlighted = undefined;

        if (!globRS.timeStamp) {
            globRS.searchi.value="";
            
            requestAnimationFrame(function() {
                globRS.searchi.focus();
            });
        }
        //START GLOBAL LISTENER
        addGlobalClickListner();

        // UPDATE CLASSES
        globRS.wrap.className = "ddTP_C ddTP_C--default ddTP_C--open" +(_.sel.dataset.theme!==undefined ? " ddTP_C--"+_.sel.dataset.theme : "");            
        _.container.classList.add('ddTP_C--open');
        _.container.classList.add('ddTP_C--focus');

        globRS.ul.ariaMultiselectable= _.sel.multiple;
        globRS.searchw.style.display = _.sel.multiple || !_.params.search   ? "none" : "block";        
        globRS.INFOCUS = id;
        globRS.ul.innerHTML = "";
        globRS.searchi.dataset.refid = id;
                
        // First Li should show the Search Result
        if (_.params.search && _.params.searchValue!==undefined && _.params.searchValue!=="") sresult = (_.params.searchValue).toLowerCase();
        
        if (_.sel.multiple && (_.params.search || _.params.tags) && globRS.liSearch!==null && globRS.liSearch!==undefined) globRS.liSearch.style.display="block";
        
        
        // ADD TO LIST THE SINGLE AVAILABLE VALUES
        var optid=-1;
        for (var i in _.sel.children) {
            optid++;
            if (!_.sel.children.hasOwnProperty(i) || (_.sel.children[i].tagName!=="OPTION" && _.sel.children[i].tagName!=="OPTGROUP") || _.sel.children[i].disabled || (_.sel.children[i].tagName==="OPTION" && sresult!=="" && (_.sel.children[i].value).toLowerCase().indexOf(sresult)==-1)) continue;            
            li =  addLiHelper(_.sel.children[i],optid,_.params,id);
            // CREATE OPTGROUP, AND COLLECT CHILDREN
            if (_.sel.children[i].tagName==="OPTGROUP") {
                frag.appendChild(li);
                optid--;
                for (var j in _.sel.children[i].children) {
                    optid++;
                    if (!_.sel.children[i].children.hasOwnProperty(j) || _.sel.children[i].children[j].tagName!=="OPTION" || _.sel.children[i].children[j].disabled || (sresult!=="" && (_.sel.children[i].children[j].value).toLowerCase().indexOf(sresult)==-1)) continue;
                    li =  addLiHelper(_.sel.children[i].children[j],optid,_.params,id);
                    if (globRS.highlighted===undefined && _.sel.children[i].selected) globRS.highlighted= li;                
                    frag.appendChild(li);
                }
            } else {
                if (globRS.highlighted===undefined && _.sel.children[i].selected) globRS.highlighted= li;                
                frag.appendChild(li);
            }
        }

        //IF NOTHING HIGLIGHTED, TAKE THE 1st ONE
        if (globRS.highlighted===undefined && frag.childElementCount>0) {
            globRS.highlighted = frag.firstElementChild;
            scrollIntoHighlitedPosition();
            globRS.highlighted.classList.add('ddTP_RO--highlighted');
        }
                
        globRS.ul.appendChild(frag);

        tpGS.gsap.set(globRS.wrap, {display: 'block',left: 0,top: 0,position:"absolute",width:"185px"});

        if (globRS.timeStamp===undefined) {
            globRS.timeStamp = 2;    
            scrollIntoHighlitedPosition();
            requestAnimationFrame(function() {
                updatePositionOfModal();
            });
        }
       

        //Update Fake Values in Rendered Container
        if(updateFakeValues) {
            setFakeValues(_.rendered,getValueTexts(_.sel,_.params));
        }

        //Set Search in Focus Again           
        if ((_.params.tags || _.params.search) && _.params.searchFocus) {            
            var s = _.sel.multiple ? _.rendered.getElementsByClassName('ddTP_SCH__field') : globRS.searchi;
            if (s!==undefined && s!==null && s.length>0) requestAnimationFrame(function() {
                s[0].focus();
                if (_.params.cursorPosition!==0 && _.params.cursorPosition!==undefined) s[0].selectionStart = s[0].selectionEnd = _.params.cursorPosition;                                    
                _.params.cursorPosition = 0;
            });
            
        }
    },

    /*
    REMOVE AN OPTION BY VALUE
    */
    removeOptionByValue = function(_) {
        if (_.sel==undefined || _.sel.options==undefined) return;
        var rem;
        for (var i in _.sel.options) if (!_.sel.options.hasOwnProperty(i) || rem) continue; else rem = _.sel.options[i].value == _.value ? _.sel.options[i] : rem;        
        if (rem!==undefined) _.sel.removeChild(rem);
    },

    /*
    REMOVE AN OPTION BY VALUE
    */
    unselectOptionByValue = function(_) {
        if (_.sel==undefined || _.sel.options==undefined) return;
        var rem;
        for (var i in _.sel.options) if (!_.sel.options.hasOwnProperty(i) || rem) continue; else rem = _.sel.options[i].value == _.value ? _.sel.options[i] : rem;        
        if (rem!==undefined) rem.selected=false;
    },

    updateModal = function(id) {
        requestAnimationFrame(function() {
            openSelectModal(id,true);
        });    
    },

    globalClickFunction = function(evt) {        
        if (globRS.INFOCUS===undefined) return;
        if (evt.target.className.indexOf('ddTP')===-1) closeSelectModal();    
    },

    getGlobRSHighlighted = function() {
        globRS.highlighted = document.getElementsByClassName('ddTP_RO--highlighted');        
        globRS.highlighted = globRS.highlighted===null || globRS.highlighted===undefined || globRS.highlighted.length===0 ? undefined : globRS.highlighted[0];
    },

    setPrevNextHighlight = function(direction) {  
        getGlobRSHighlighted();             
        if (globRS.highlighted!==undefined && globRS.highlighted[direction]!==null && globRS.highlighted[direction]!==undefined) {                
            globRS.highlighted.classList.remove('ddTP_RO--highlighted');
            globRS.highlighted = globRS.highlighted[direction];
            globRS.highlighted.classList.add('ddTP_RO--highlighted');
            scrollIntoHighlitedPosition();
        }        
    },

    globalUpDownMover = function(evt) {
        if (globRS.INFOCUS===undefined || globRS.highlighted===undefined) return;    
        var key = (evt.which || evt.keyCode);     
        if (key===KEYS.UP || key===KEYS.DOWN) {
            if (ddTPcache[globRS.INFOCUS].sel.multiple) {
                if (globRS.liSearch!==null && globRS.liSearch!==undefined) globRS.liSearch.blur(); 
            } else {
                globRS.searchi.blur();
            }
            setPrevNextHighlight(key===KEYS.UP ? 'previousElementSibling' : 'nextElementSibling');
            evt.preventDefault();            
            return false;
        } else if (key===KEYS.ENTER  && !ddTPcache[globRS.INFOCUS].params.tags) {
            getGlobRSHighlighted();
            if (globRS.highlighted!==undefined) selectOption(globRS.highlighted);
            evt.preventDefault();            
            return false;
        }    
    },

    removeGlobalListener = function() {        
        document.body.removeEventListener('click',globalClickFunction);
        document.body.removeEventListener('keydown',globalUpDownMover);
        globRS.GLOBALLISTENER = undefined;
    },

    addGlobalClickListner = function() {
        // GLOBAL LISTENER, CLICK ON SOMETHING 
        if (globRS.GLOBALLISTENER) return;
        document.body.addEventListener('click',globalClickFunction);
        document.body.addEventListener('keydown',globalUpDownMover);

        globRS.GLOBALLISTENER = true;        
    },

    /*
    CREATE LISTENERS ON GLOBAL LEVEL
    */
    buildListeners = function() {
        listeners = true;
        createGlobalElement();

        DOC.on('click','.ddTP_C.ddTP-fake',function() {
            if (globRS.INFOCUS===this.dataset.refid && !ddTPcache[this.dataset.refid].params.tags && !ddTPcache[this.dataset.refid].multiple) {
                closeSelectModal();
            } else {
                ddTPcache[this.dataset.refid].params.searchFocus = true;
                openSelectModal(this.dataset.refid);            
            }
        });

        DOC.on('mouseenter','.ddTP_RO',function() {            
            this.classList.add('ddTP_RO--highlighted');
        }); 

        DOC.on('mouseleave','.ddTP_RO',function() {
            this.classList.remove('ddTP_RO--highlighted');
        }); 
        
        DOC.on('click','.ddTP_S__choice__remove',function() {
            var _ = ddTPcache[this.dataset.refid];
            if (_.params.tags) {
                removeOptionByValue({sel:_.sel,value:this.parentNode.dataset.val});
                this.parentNode.parentNode.removeChild(this.parentNode);
                _.params.searchValue = "";
                _.params.searchFocus = false;
            } else {
                unselectOptionByValue({sel:_.sel,value:this.parentNode.dataset.val});
                _.params.searchValue = "";
                _.params.searchFocus = false;
            }            
            _.sel.dispatchEvent(new Event('change', { 'bubbles': true }));
            jQuery(_.sel).trigger('ddTP:unselect');
            updateModal(this.dataset.refid);
            return false;
        });

        

        DOC.on('keydown','.ddTP_SCH__field',function(evt) {

            var key = evt.which || evt.keyCode,
                REFID = this.dataset.refid,
                _ = ddTPcache[REFID]; 

           if (key===KEYS.UP || key===KEYS.DOWN) {
                setPrevNextHighlight(key===KEYS.UP ? 'previousElementSibling' : 'nextElementSibling');
                evt.preventDefault();
                return false;
            }   

            if (key===KEYS.TAB) {
                closeSelectModal();
                return;
            }

            
            // On Tags create tag on demand
            if (_.params.tags) {
                // Get Last Option in the list
                if (_.params.tags && key === KEYS.BACKSPACE && this.value==="") {
                    var prev = this.parentNode.previousElementSibling;
                    if (prev!==null && prev!==undefined && prev.tagName==="LI" && prev.dataset.val!==undefined) {
                        _.params.searchValue = prev.dataset.val;                        
                        _.params.searchFocus = true;
                        removeOptionByValue({sel:_.sel,value:_.params.searchValue});                                            
                        jQuery(_.sel).trigger('ddTP:unselect');
                    }
                } else                     
                if (this.value.length>1 && (jQuery.inArray(String.fromCharCode(key),_.params.tokenSeparators)>=0 || jQuery.inArray(evt.key,_.params.tokenSeparators)>=0) || key===KEYS.ENTER) {
                    var option = document.createElement('option');
                    option.value = this.value;
                    option.innerHTML = this.value;
                    option.dataset.refid = REFID;
                    option.selected = true;
                    _.sel.appendChild(option);
                    if (_.sel.multiple) this.parentNode.parentNode.insertBefore(createResultLi(this.value,undefined,_.sel.id,undefined),this.parentNode);
                    this.value="";
                    _.params.searchValue = "";
                    _.params.searchFocus = true;
                    _.params.cursorPosition = 0;                    
                    if (!_.sel.multpile) option.ariaSelected = true;
                    jQuery(_.sel).trigger('ddTP:select');
                    if (!_.sel.multpile) {
                        
                        setFakeValues(_.rendered,getValueTexts(_.sel,_.params));                        
                         _.sel.dispatchEvent(new Event('change', { 'bubbles': true }));
                         closeSelectModal();
                    }
                    
                }

            } else {
                if (_.sel.multiple && _.params.search) updateSW(this); 
            }
        });

        DOC.on('keyup','.ddTP_SCH__field',function(evt) {
            var key = evt.which || evt.keyCode;

            if (key===KEYS.LEFT || key===KEYS.RIGHT || key===KEYS.SHIFT || key==KEYS.ALT || key==KEYS.CTRL || key==KEYS.CMD || key==KEYS.CMDL || key==KEYS.CMDR) return;

            _ = ddTPcache[this.dataset.refid];
            _.params.searchValue = this.value;
            _.params.searchFocus = true;
            _.params.cursorPosition = this.selectionStart;
            openSelectModal(this.dataset.refid,true);
            //updateModal();            
        });


        DOC.on('click','.ddTP_RO',function() {
           selectOption(this);            
        });

        
    },

    selectOption  = function(option) {
         var _ = ddTPcache[option.dataset.refid];
        if (!_.sel.multiple) {
            _.sel.value = option.dataset.val;
            closeSelectModal();
        } else {            
            option.ariaSelected= option.ariaSelected==true || option.ariaSelected=="true" ? false : true;            
            _.sel.options[option.dataset.optid].selected = option.ariaSelected==true || option.ariaSelected=="true" ? true : false; 
            _.params.searchValue = "";
            _.params.searchFocus = false;
             jQuery(_.sel).trigger('ddTP:select');                    
        }

        setFakeValues(_.rendered,getValueTexts(_.sel,_.params));        
        _.sel.dispatchEvent(new Event('change', { 'bubbles': true }));
        if (_.sel.multiple) updateModal(option.dataset.refid);
    },

    /* USEFULL THINGS */
    insertAfter = function(newNode, referenceNode) {
        if (referenceNode!==null && newNode!==undefined && referenceNode.parentNode!==null) referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
    },

    updateSW = function(search) {           
        tpGS.gsap.set(search,{width:((search.value.length+1) * 0.75) + 'em'});
        
    }


})(jQuery);