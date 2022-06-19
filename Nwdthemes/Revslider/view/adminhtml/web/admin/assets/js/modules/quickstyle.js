/*!
 * REVOLUTION 6.0.0 EDITOR QUICK STYLE JS
 * @version: 1.0 (01.07.2019)
 * @author ThemePunch
*/

RVS.LIB.QS = {
	headlines : [
		{fs:32, lh:36, ff:"Indie Flower",fw:"400",ls:1},
		{fs:35, lh:45, ff:"Raleway",fw:"300",ls:2},
		{fs:46, lh:60, ff:"Shadows Into Light",fw:"400",ls:-1},
		{fs:36, lh:40, ff:"Roboto",fw:"500",ls:1.5},
		{fs:40, fst:"italic", lh:44, ff:"Raleway",fw:"800",ls:2},
		{fs:40, lh:50, ff:"Arial",fw:"400",ls:0},
		{fs:40, lh:50, ff:"Roboto",fw:"900",ls:0, tt:"uppercase"},
		{fs:60, lh:70, ff:"Poppins",fw:"500",ls:"-1",tt:"uppercase"},
		{fs:80, lh:90, ff:"Poppins",fw:"800",ls:"-4"},
		{fs:80, lh:90, ff:"Montserrat",fw:"200",ls:"-0.2"},
		{fs:100, lh:110, ff:"Montserrat",fw:"100",ls:"-6"}
		],
	headlines_color : '#fff',
	content_color : '#fff',
	content: [
		{fs:14, lh:24, ff:"Roboto",fw:"400",ls:1},
		{fs:16, lh:24, ff:"Poppins",fw:"400",ls:2},
		{fs:12, lh:20, ff:"Arial",fw:"400", content:"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam vestibulum orci at leo consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore..."},
		{fs:15, lh:25, ff:"Montserrat",fw:"400", ls:2},
		{fs:13, lh:20, ff:"Open Sans",fw:"400", ls:2, tt:"uppercase"},
		{fs:16, lh:24, ff:"Roboto",fw:"500",ls:0},
		{fs:24, lh:30, ff:"Open Sans",fw:"300",ls:1, content:"Lorem ipsum dolor sit amet, consectetur adipiscing elit"}
		],
	buttons:[
		// NEW BUTTONS
		{showsize:"half",fs:15, fw:"500", ff:"Roboto", lh:32, pd:["0px","20px","0px","20px"],  bg:"rgba(255,255,255,1)", color:"#000000", content:"Click Here", cls:"rev-btn", hc:"rgba(255,255,255,1)", hbg:"#000000", hs:300, ease:"power3.inOut"},
		{showsize:"half",fs:15, fw:"500", ff:"Roboto", lh:32, pd:["0px","20px","0px","20px"],  br:["16px", "16px", "16px", "16px"], bg:"rgba(255,255,255,1)", color:"#000000", content:"Click Here", cls:"rev-btn", hc:"rgba(255,255,255,1)", hbg:"#000000", hs:300,ease:"power3.inOut"},

		{showsize:"half",fs:20, fw:"500", ff:"Roboto", lh:50, pd:["0px","20px","0px","20px"],  bg:"rgba(0,0,0,1)", color:"#ffffff", content:"Click Here", cls:"rev-btn", hc:"rgba(0,0,0,1)", hbg:"#ffffff", hs:200,ease:"power1.inOut"},
		{showsize:"half",fs:20, fw:"500", ff:"Roboto", lh:50, pd:["0px","20px","0px","20px"],  br:["25px", "25px", "25px", "25px"], bg:"rgba(0,0,0,1)", color:"#ffffff", content:"Click Here", cls:"rev-btn", hc:"rgba(0,0,0,1)", hbg:"#ffffff", hs:200,ease:"power1.inOut"},

		{showsize:"half",fs:17, fw:"500", ff:"Roboto", lh:40, pd:["0px","25px","0px","25px"],  bg:"rgba(0,0,0,0)", bw:["1px","1px","1px","1px"], bs:"solid", bc:"rgba(255,255,255,0.35)", color:"#ffffff", content:"Click Here", cls:"rev-btn", hbc:"rgba(255,255,255,0.75)", hc:"rgba(255,255,255,1)", hs:300,ease:"power1.inOut"},
		{showsize:"half",fs:17, fw:"500", ff:"Roboto", lh:40, pd:["0px","25px","0px","25px"],  br:["25px", "25px", "25px", "25px"], bw:["1px","1px","1px","1px"], bs:"solid", bc:"rgba(255,255,255,0.35)",  bg:"rgba(0,0,0,0)", color:"#ffffff", content:"Click Here", cls:"rev-btn", hbc:"rgba(255,255,255,0.75)",hc:"rgba(255,255,255,1)",  hs:300,ease:"power1.inOut"},


		{showsize:"half",fs:17, fw:"500", ff:"Roboto", lh:40, pd:["0px","25px","0px","25px"],  bg:"#007aff",  color:"#ffffff", 									    content:"Click Here", cls:"rev-btn", hs:300, hc:"#ffffff", hbg:"#007aff", hfb:"120%", hfbl:0, hfg:0,ease:"power3.inOut"},
		{showsize:"half",fs:17, fw:"500", ff:"Roboto", lh:40, pd:["0px","25px","0px","25px"],  bg:"#007aff",  color:"#ffffff", br:["25px", "25px", "25px", "25px"], content:"Click Here", cls:"rev-btn", hs:300, hc:"#ffffff", hbg:"#007aff", hfb:"120%", hfbl:0, hfg:0,ease:"power3.inOut"},

		{fs:18, fw:"500", ff:"Roboto", lh:50, pd:["0px","40px","0px","40px"],  bg:"#007aff", br:["3px", "3px", "3px", "3px"], color:"#ffffff", content:"Click Here", cls:"rev-btn", hs:100, hc:"#ffffff", hbg:"#007aff", hfb:"120%", hfbl:0, hfg:0,ease:"power1.inOut"},
		{fs:20, fw:"500", ff:"Roboto", lh:55, pd:["0px","50px","0px","50px"],  bg:"#007aff", br:["3px", "3px", "3px", "3px"], color:"#ffffff", content:"Click Here", cls:"rev-btn", hs:100, hc:"#ffffff", hbg:"#007aff", hfb:"120%", hfbl:0, hfg:0,ease:"power1.inOut"},

		{fs:18, fw:"500", ff:"Roboto", lh:50, pd:["0px","40px","0px","40px"],  bxb:"20px", bxc:"#461d7d", bxv:"5px", bxh:"0px", bxs:"0px", br:["5px", "5px", "5px", "5px"], color:"#ffffff", content:"Click Here", cls:"rev-btn", hs:300, hc:"#ffffff", hbg:"{&type&:&linear&,&angle&:&181&,&colors&:[{&r&:110,&g&:74,&b&:185,&a&:1,&position&:0,&align&:&top&},{&r&:110,&g&:74,&b&:185,&a&:1,&position&:0,&align&:&bottom&},{&r&:94,&g&:53,&b&:177,&a&:1,&position&:100,&align&:&bottom&},{&r&:94,&g&:53,&b&:177,&a&:1,&position&:100,&align&:&top&}],&easing&:&sine.easeinout&,&strength&:100}", hfb:"120%", hfbl:0, hfg:0,ease:"power1.inOut", bg:"{&type&:&linear&,&angle&:&181&,&colors&:[{&r&:110,&g&:74,&b&:185,&a&:1,&position&:0,&align&:&top&},{&r&:110,&g&:74,&b&:185,&a&:1,&position&:0,&align&:&bottom&},{&r&:94,&g&:53,&b&:177,&a&:1,&position&:100,&align&:&bottom&},{&r&:94,&g&:53,&b&:177,&a&:1,&position&:100,&align&:&top&}],&easing&:&sine.easeinout&,&strength&:100}"},
		{fs:20, fw:"500", ff:"Roboto", lh:60, pd:["0px","60px","0px","60px"],  bxb:"20px", bxc:"#461d7d", bxv:"5px", bxh:"0px", bxs:"0px", br:["5px", "5px", "5px", "5px"], color:"#ffffff", content:"Click Here", cls:"rev-btn", hs:300, hc:"#ffffff", hbg:"{&type&:&linear&,&angle&:&181&,&colors&:[{&r&:110,&g&:74,&b&:185,&a&:1,&position&:0,&align&:&top&},{&r&:110,&g&:74,&b&:185,&a&:1,&position&:0,&align&:&bottom&},{&r&:94,&g&:53,&b&:177,&a&:1,&position&:100,&align&:&bottom&},{&r&:94,&g&:53,&b&:177,&a&:1,&position&:100,&align&:&top&}],&easing&:&sine.easeinout&,&strength&:100}", hfb:"120%", hfbl:0, hfg:0,ease:"power1.inOut", bg:"{&type&:&linear&,&angle&:&181&,&colors&:[{&r&:110,&g&:74,&b&:185,&a&:1,&position&:0,&align&:&top&},{&r&:110,&g&:74,&b&:185,&a&:1,&position&:0,&align&:&bottom&},{&r&:94,&g&:53,&b&:177,&a&:1,&position&:100,&align&:&bottom&},{&r&:94,&g&:53,&b&:177,&a&:1,&position&:100,&align&:&top&}],&easing&:&sine.easeinout&,&strength&:100}"},

		{fs:18, fw:"500", ff:"Roboto", lh:50, pd:["0px","40px","0px","40px"],  bxb:"20px", bxc:"rgba(0,0,0,0.25)", bxv:"10px", bxh:"0px", bxs:"0px", br:["5px", "5px", "5px", "5px"], color:"#ffffff", content:"Click Here", cls:"rev-btn", hs:300, hc:"#ffffff", hbg:"{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:149,&b&:0,&a&:1,&position&:0,&align&:&top&},{&r&:255,&g&:149,&b&:0,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:100,&align&:&bottom&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}", hfb:"120%", hfbl:0, hfg:0,ease:"power1.inOut", bg:"{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:149,&b&:0,&a&:1,&position&:0,&align&:&top&},{&r&:255,&g&:149,&b&:0,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:100,&align&:&bottom&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}"},
		{fs:20, fw:"500", ff:"Roboto", lh:60, pd:["0px","60px","0px","60px"],  bxb:"20px", bxc:"rgba(0,0,0,0.25)", bxv:"10px", bxh:"0px", bxs:"0px", br:["5px", "5px", "5px", "5px"], color:"#ffffff", content:"Click Here", cls:"rev-btn", hs:300, hc:"#ffffff", hbg:"{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:149,&b&:0,&a&:1,&position&:0,&align&:&top&},{&r&:255,&g&:149,&b&:0,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:100,&align&:&bottom&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}", hfb:"120%", hfbl:0, hfg:0,ease:"power1.inOut", bg:"{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:149,&b&:0,&a&:1,&position&:0,&align&:&top&},{&r&:255,&g&:149,&b&:0,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:100,&align&:&bottom&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}"},

		// OLD BUTTONS
		/*{showsize:"half",fs:17, ff:"Roboto", lh:20, pd:["12px","35px","12px","35px"],  bg:"rgba(0,0,0,0.75)", color:"#ffffff", content:"Click Here", cls:"rev-btn", hc:"rgba(0,0,0,1)", hbg:"#ffffff", hs:200},
		{showsize:"half",fs:17, ff:"Roboto", lh:20, pd:["12px","35px","12px","35px"], br:["30px", "30px", "30px", "30px"],  bg:"rgba(0,0,0,0.75)", color:"#ffffff", content:"Click Here", cls:"rev-btn",hc:"rgba(0,0,0,1)", hbg:"#ffffff", hs:200},
		{showsize:"half",fs:14, ff:"Roboto", lh:18, pd:["10px","30px","10px","30px"],  bg:"rgba(0,0,0,0.75)", color:"#ffffff", content:"Click Here",cls:"rev-btn",hc:"rgba(0,0,0,1)", hbg:"#ffffff", hs:200},
		{showsize:"half",fs:14, ff:"Roboto", lh:18, pd:["10px","30px","10px","30px"], br:["30px", "30px", "30px", "30px"],  bg:"rgba(0,0,0,0.75)", color:"#ffffff", content:"Click Here",cls:"rev-btn",hc:"rgba(0,0,0,1)", hbg:"#ffffff", hs:200},
		{showsize:"half",fs:12, ff:"Roboto", lh:14, pd:["8px","20px","8px","20px"],  bg:"rgba(0,0,0,0.75)", color:"#ffffff", content:"Click Here", cls:"rev-btn",hc:"rgba(0,0,0,1)", hbg:"#ffffff", hs:200},
		{showsize:"half",fs:12, ff:"Roboto", lh:14, pd:["8px","20px","8px","20px"], br:["30px", "30px", "30px", "30px"],  bg:"rgba(0,0,0,0.75)", color:"#ffffff", content:"Click Here", cls:"rev-btn",hc:"rgba(0,0,0,1)", hbg:"#ffffff", hs:200},*/

		{fs:17, ff:"Roboto", lh:20, ls:1, tt:"uppercase", pd:["12px","35px","12px","35px"], br:["30px", "30px", "30px", "30px"],  bg:"rgba(0,0,0,0.75)", color:"#ffffff", content:'Click Here <i class="fa-chevron-right"></i>', cls:"rev-btn",hc:"rgba(0,0,0,1)", hbg:"#ffffff", hs:200},
		{fs:14, ff:"Roboto", lh:18, ls:1, tt:"uppercase", pd:["10px","30px","10px","30px"], br:["30px", "30px", "30px", "30px"],  bg:"rgba(0,0,0,0.75)", color:"#ffffff", content:'Click Here <i class="fa-chevron-right"></i>',cls:"rev-btn",hc:"rgba(0,0,0,1)", hbg:"#ffffff", hs:200},
		{fs:12, ff:"Roboto", lh:14, ls:1, tt:"uppercase", pd:["8px","20px","8px","20px"], br:["30px", "30px", "30px", "30px"],  bg:"rgba(0,0,0,0.75)", color:"#ffffff", content:'Click Here <i class="fa-chevron-right"></i>', cls:"rev-btn",hc:"rgba(0,0,0,1)", hbg:"#ffffff", hs:200},

		{fs:17, ff:"Roboto", lh:20, ls:1, tt:"uppercase", pd:["12px","35px","12px","35px"], br:["30px", "30px", "30px", "30px"],  bg:"rgba(0,0,0,0.75)", color:"#ffffff", content:'Click Here <i class="fa-chevron-right"></i>', cls:"rev-btn rev-hiddenicon",hc:"rgba(0,0,0,1)", hbg:"#ffffff", hs:200},
		{fs:14, ff:"Roboto", lh:18, ls:1, tt:"uppercase", pd:["10px","30px","10px","30px"], br:["30px", "30px", "30px", "30px"],  bg:"rgba(0,0,0,0.75)", color:"#ffffff", content:'Click Here <i class="fa-chevron-right"></i>',cls:"rev-btn rev-hiddenicon",hc:"rgba(0,0,0,1)", hbg:"#ffffff", hs:200},
		{fs:12, ff:"Roboto", lh:14, ls:1, tt:"uppercase", pd:["8px","20px","8px","20px"], br:["30px", "30px", "30px", "30px"],  bg:"rgba(0,0,0,0.75)", color:"#ffffff", content:'Click Here <i class="fa-chevron-right"></i>', cls:"rev-btn rev-hiddenicon",hc:"rgba(0,0,0,1)", hbg:"#ffffff", hs:200},

		{showsize:"third", fs:17, ff:"Roboto", lh:20, ls:1, tt:"uppercase", pd:["22px","14px","22px","14px"], br:["50%", "50%", "50%", "50%"], color:"#ffffff", content:'<span></span><span></span><span></span>', cls:"rev-burger rev-b-span-light", action:{action:"toggle_class", toggle_class:"open", tooltip_event:"click"}},
		{showsize:"third",fs:17, ff:"Roboto", lh:20, ls:1, tt:"uppercase", pd:["22px","14px","22px","14px"], br:["50%", "50%", "50%", "50%"], bc:"rgba(255,255,255,0.75)", bs:"solid", bw:["2px","2px","2px","2px"],color:"#ffffff", content:'<span></span><span></span><span></span>', cls:"rev-burger rev-b-span-light", action:{action:"toggle_class", toggle_class:"open", tooltip_event:"click"}},
		{showsize:"third",fs:17, ff:"Roboto", lh:20, ls:1, tt:"uppercase", pd:["22px","14px","22px","14px"], br:["50%", "50%", "50%", "50%"],  bg:"#ffffff", color:"#ffffff", content:'<span></span><span></span><span></span>', cls:"rev-burger rev-b-span-dark", action:{action:"toggle_class", toggle_class:"open", tooltip_event:"click"}},

		{showsize:"third",fs:17, ff:"Roboto", lh:20, ls:1, tt:"uppercase", pd:["22px","14px","22px","14px"], br:["50%", "50%", "50%", "50%"], color:"#ffffff", content:'<span></span><span></span><span></span>', cls:"rev-burger rev-b-span-dark", action:{action:"toggle_class", toggle_class:"open", tooltip_event:"click"}},
		{showsize:"third",fs:17, ff:"Roboto", lh:20, ls:1, tt:"uppercase", pd:["22px","14px","22px","14px"], br:["50%", "50%", "50%", "50%"], bc:"rgba(51,51,51,0.75)", bs:"solid", bw:["2px","2px","2px","2px"],color:"#ffffff", content:'<span></span><span></span><span></span>', cls:"rev-burger rev-b-span-dark", action:{action:"toggle_class", toggle_class:"open", tooltip_event:"click"}},
		{showsize:"third",fs:17, ff:"Roboto", lh:20, ls:1, tt:"uppercase", pd:["22px","14px","22px","14px"], br:["50%", "50%", "50%", "50%"],   bg:"#333333", color:"#ffffff", content:'<span></span><span></span><span></span>', cls:"rev-burger rev-b-span-light", action:{action:"toggle_class", toggle_class:"open", tooltip_event:"click"}},

		{showsize:"fourth",w:"35px", h:"55px", fs:17, ff:"Roboto", lh:20, ls:1, tt:"uppercase", pd:["22px","14px","22px","14px"], br:["23px", "23px", "23px", "23px"], bc:"rgba(255,255,255,0.75)", bs:"solid", bw:["3px","3px","3px","3px"],color:"#ffffff", content:'<span></span><span></span><span></span>', cls:"rev-scroll-btn rev-b-span-light"},
		{showsize:"fourth",w:"35px", h:"55px", fs:17, ff:"Roboto", lh:20, ls:1, tt:"uppercase", pd:["22px","14px","22px","14px"], br:["23px", "23px", "23px", "23px"], bc:"rgba(255,255,255,0.75)", bs:"solid", bw:["3px","3px","3px","3px"], bg:"#fff", color:"#ffffff", content:'<span></span><span></span><span></span>', cls:"rev-scroll-btn rev-b-span-dark"},
		{showsize:"fourth",w:"35px", h:"55px", fs:17, ff:"Roboto", lh:20, ls:1, tt:"uppercase", pd:["22px","14px","22px","14px"], br:["23px", "23px", "23px", "23px"], bc:"rgba(51,51,51,0.75)", bs:"solid", bw:["3px","3px","3px","3px"],color:"#ffffff", content:'<span></span><span></span><span></span>', cls:"rev-scroll-btn rev-b-span-dark"},
		{showsize:"fourth",w:"35px", h:"55px", fs:17, ff:"Roboto", lh:20, ls:1, tt:"uppercase", pd:["22px","14px","22px","14px"], br:["23px", "23px", "23px", "23px"], bc:"rgba(51,51,51,0.75)", bs:"solid", bw:["3px","3px","3px","3px"], bg:"#333", color:"#ffffff", content:'<span></span><span></span><span></span>', cls:"rev-scroll-btn rev-b-span-light"},

		{showsize:"fourth",w:"37px", h:"37px", fs:20, ff:"Roboto", lh:37, ta:"center", tt:"uppercase", pd:["0","0","0","0"], br:["50%", "50%", "50%", "50%"],   bg:"#3B5998", color:"#ffffff", content:'<i class="fa-facebook-f"></i>', cls:""},
		{showsize:"fourth",w:"37px", h:"37px", fs:20, ff:"Roboto", lh:37, ta:"center", tt:"uppercase", pd:["0","0","0","0"], br:["50%", "50%", "50%", "50%"],   bg:"#FD1D1D", color:"#ffffff", content:'<i class="fa-instagram"></i>', cls:""},
		{showsize:"fourth",w:"37px", h:"37px", fs:20, ff:"Roboto", lh:37, ta:"center", tt:"uppercase", pd:["0","0","0","0"], br:["50%", "50%", "50%", "50%"],   bg:"#00A0D1", color:"#ffffff", content:'<i class="fa-twitter"></i>', cls:""},
		{showsize:"fourth",w:"37px", h:"37px", fs:20, ff:"Roboto", lh:37, ta:"center", tt:"uppercase", pd:["0","0","0","0"], br:["50%", "50%", "50%", "50%"],   bg:"rgba(0,0,0,0.5)", color:"#ffffff", content:'<i class="fa-envelope"></i>', cls:""},


		{showsize:"fourth",w:"60px", h:"60px", fs:20, ff:"Roboto", lh:60, ta:"center", tt:"uppercase", pd:["0","0","0","0"], br:["50%", "50%", "50%", "50%"],   bg:"rgba(0,0,0,0.5)", color:"#ffffff", content:'<i class="fa-play"></i>', cls:""},
		{showsize:"fourth",w:"60px", h:"60px", fs:20, ff:"Roboto", lh:60, ta:"center", tt:"uppercase", pd:["0","0","0","0"], br:["50%", "50%", "50%", "50%"],   bg:"#ffffff", color:"#333333", content:'<i class="fa-play"></i>', cls:""},
		{showsize:"fourth",w:"60px", h:"60px", fs:20, ff:"Roboto", lh:60, ta:"center", tt:"uppercase", pd:["0","0","0","0"], br:["5px", "5px", "5px", "5px"],   bg:"rgba(0,0,0,0.5)", color:"#ffffff", content:'<i class="fa-play"></i>', cls:""},
		{showsize:"fourth",w:"60px", h:"60px", fs:20, ff:"Roboto", lh:60, ta:"center", tt:"uppercase", pd:["0","0","0","0"], br:["5px", "5px", "5px", "5px"],   bg:"#ffffff", color:"#333333", content:'<i class="fa-play"></i>', cls:""},

		{showsize:"fourth",w:"60px", h:"60px", fs:20, ff:"Roboto", lh:60, ta:"center", tt:"uppercase", pd:["0","0","0","0"], br:["50%", "50%", "50%", "50%"],   bg:"rgba(0,0,0,0.5)", color:"#ffffff", content:'<i class="fa-pause"></i>', cls:""},
		{showsize:"fourth",w:"60px", h:"60px", fs:20, ff:"Roboto", lh:60, ta:"center", tt:"uppercase", pd:["0","0","0","0"], br:["50%", "50%", "50%", "50%"],   bg:"#ffffff", color:"#333333", content:'<i class="fa-pause"></i>', cls:""},
		{showsize:"fourth",w:"60px", h:"60px", fs:20, ff:"Roboto", lh:60, ta:"center", tt:"uppercase", pd:["0","0","0","0"], br:["5px", "5px", "5px", "5px"],   bg:"rgba(0,0,0,0.5)", color:"#ffffff", content:'<i class="fa-pause"></i>', cls:""},
		{showsize:"fourth",w:"60px", h:"60px", fs:20, ff:"Roboto", lh:60, ta:"center", tt:"uppercase", pd:["0","0","0","0"], br:["5px", "5px", "5px", "5px"],   bg:"#ffffff", color:"#333333", content:'<i class="fa-pause"></i>', cls:""}
	],
	shadows:[
		{showsize:"half", box_hoff:"0px", box_voff:"0px", box_blur:"0px", box_spread:"0px", box_color:"rgba(0,0,0,0.5)", box_inset:false },
		{ showsize:"half",box_hoff:"5px", box_voff:"0px", box_blur:"10px", box_spread:"0px", box_color:"rgba(0,0,0,0.25)", box_inset:false },
		{showsize:"half", box_hoff:"0px", box_voff:"10px", box_blur:"10px", box_spread:"0px", box_color:"rgba(0,0,0,0.25)", box_inset:false },
		{showsize:"half", box_hoff:"5px", box_voff:"5px", box_blur:"10px", box_spread:"0px", box_color:"rgba(0,0,0,0.15)", box_inset:false },

		{showsize:"half", text_hoff:"7px", text_voff:"7px", text_blur:"10px", text_color:"rgba(0,0,0,0.75)" },
		{showsize:"half", text_hoff:"5px", text_voff:"0px", text_blur:"10px", text_color:"rgba(0,0,0,0.75)" },
		{showsize:"half", text_hoff:"0px", text_voff:"10px", text_blur:"10px", text_color:"rgba(0,0,0,0.75)" },
		{showsize:"half", text_hoff:"15px", text_voff:"15px", text_blur:"20px", text_color:"rgba(0,0,0,0.45)" }
	]
};
RVS.LIB.QS_CONT = {
	headlines:"Headline",
	content:"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam vestibulum orci at leo..."
};

/**********************************
	-	QUICK STYLE -
********************************/
(function() {

	// INIT OVERVIEW
	RVS.F.initQuickStyle = function() {
		initLocalListeners();
	};

	RVS.F.openQuickStyle = function(_) {
		jQuery('#quick_style_trigger').addClass("selected");
		RVS.S.qstyleBackupGroup = _==undefined || _.bacupGroupOpen===undefined ? undefined : _.bacupGroupOpen;
		RVS.S.qstyle_library_open = true;
		RVS.S.qstyleParams = _;
		if (!window.quickStyleExists) {
			buildQuickStyle();
			window.quickStyleExists = true;
		} else {
			RVS.F.showQuickStyle();
		}
	};

	RVS.F.showQuickStyle = function() {
		RVS.F.updateAvailableLayerTypes();
		tpGS.gsap.to('#quick_style',0.4,{width:320,display:"block", ease:"power3.inOut"});
		tpGS.gsap.to('#quick_style_inner',0.4,{left:0, ease:"power3.inOut"});
		setTimeout(setQSMenu,400);
	};

	RVS.F.updateAvailableLayerTypes = function() {
		if (!RVS.S.qstyle_library_open) return;
		window.quickstylefilter = [];
		var btns = false, headlines = false, content=false, first = false;

		if (RVS.S.qstyleParams.list!==undefined && Array.isArray((RVS.S.qstyleParams.list)))
			for (var lid in RVS.S.qstyleParams.list) {
				if(!RVS.S.qstyleParams.list.hasOwnProperty(lid)) continue;
				switch (RVS.S.qstyleParams.list[lid]) {
					case "button":
						if (!btns) {
							window.quickstylefilter.push("buttons");
							first = first===false ? "buttons" : first;
							btns = true;
						}
					break;
					case "content":
						if (!content) {
							window.quickstylefilter.push("content");
							first = first===false ? "content" : first;
							content = true;
						}
					break;
					case "headlines":
					case "text":
						if (!headlines) {
							window.quickstylefilter.push("headlines");
							first = first===false ? "headlines" : first;
							headlines = true;
						}
					break;
				}
			}
		else
		for (var lid in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(lid)) continue;
			switch (RVS.L[RVS.selLayers[lid]].type) {
				case "button":
					if (!btns) {
						window.quickstylefilter.push("buttons");
						btns = true;
					}
				break;

				case "text":
					if (!headlines) {
						window.quickstylefilter.push("headlines");
						window.quickstylefilter.push("content");
						headlines = true;
						content = true;
						first = "headlines";
					}
				break;
				default:

				break;
			}
		}

		if (window.quickstylefilter.length===0) {
			window.quickstylefilter.push("headlines");
			window.quickstylefilter.push("content");
			window.quickstylefilter.push("buttons");
		}
		if (jQuery.inArray("shadows",window.quickstylefilter)===-1) window.quickstylefilter.push("shadows");

		jQuery('.quick_style_dep_all').hide();
		jQuery('.quick_style_menu_point').hide();
		for (var i in window.qstyle_scroll_targets) {
			if(!window.qstyle_scroll_targets.hasOwnProperty(i)) continue;
			if (jQuery.inArray(window.qstyle_scroll_targets[i].library,window.quickstylefilter)>=0)
				window.qstyle_scroll_targets[i].enable = true;
			else
				window.qstyle_scroll_targets[i].enable = false;
		}
		for (var i in window.quickstylefilter) {
			if(!window.quickstylefilter.hasOwnProperty(i)) continue;
			jQuery('#quick_style_dep_'+window.quickstylefilter[i]).show();
			jQuery('#quick_style_menu_'+window.quickstylefilter[i]).show();
		}
		if (first!==false) setTimeout(function() {	jQuery('#quick_style_menu_'+first).trigger('click');},400);
	};

	RVS.F.closeQuickStyle = function() {
		if (RVS.S.qstyleBackupGroup!==undefined) RVS.F.closeBackupGroup({id:RVS.S.qstyleBackupGroup});
		RVS.S.qstyleBackupGroup = undefined;
		RVS.S.qstyle_library_open = false;
		tpGS.gsap.to('#quick_style',0.4,{width:0,display:"none", ease:"power3.inOut"});
		tpGS.gsap.to('#quick_style_inner',0.4,{left:-270, ease:"power3.inOut"});
		jQuery('#quick_style_trigger').removeClass("selected");
	};


	/****************************
		INTERNAL FUNCTIONS
	****************************/
	function buildQuickStyle() {
		RVS.F.showWaitAMinute({fadeIn:100,text:"Building Quick Style Library"});

		var requiredGoogleFonts = {},
			familiesToLoad = [];

		var _c = '<div id="quick_style"><div id="quick_style_inner">';
		_c += '<div id="quick_style_menu">';
		for (var i in RVS.LIB.QS) {
			if(!RVS.LIB.QS.hasOwnProperty(i)) continue;
			_c += '<div data-ref="#quick_style_dep_'+i+'" data-library="'+i+'" id="quick_style_menu_'+i+'" class="quick_style_menu_point">'+RVS_LANG["qs_"+i]+'</div>';
		}
		_c += '<div id="quick_style_close"><i class="material-icons">close</i></div>';
		_c += '</div>';
		_c += '<div id="quick_style_scrollable_wrap"><div id="quick_style_scrollable">';
		for (var i in RVS.LIB.QS) {
			if(!RVS.LIB.QS.hasOwnProperty(i)) continue;
			_c += '<div id="quick_style_dep_'+i+'" class="quick_style_dep_all"><div class="quick_style_dep">'+RVS_LANG["qs_"+i]+(i==='headlines' || i==='content' ? '<div data-cont="'+i+'" class="quick_colorchange_dark"></div><div data-cont="'+i+'"  class="quick_colorchange_light selected"></div>' : '')+'</div>';
			for (var j in RVS.LIB.QS[i]) {
				if(!RVS.LIB.QS[i].hasOwnProperty(j)) continue;
				var _ = RVS.F.safeExtend(true,{},RVS.LIB.QS[i][j]);
				_ = setButtonDefault(_);
				switch (i) {
					case "headlines":
					case "content":
						var bord = _.bc!==undefined && _.bw!==undefined && _.bs!==undefined ? 'border-style:'+_.bs+';border-width:'+_.bw[0]+' '+_.bw[1]+' '+_.bw[2]+' '+_.bw[3]+';border-color:'+_.bc+';' : '';
						_c += '<div class="quick_style_example_wrap"><div class="quick_style_example" data-layertype="text" data-qstyletype="'+i+'" data-qstyleindex="'+j+'"><div class="qsec_info">'+_.ff+','+_.fs+'px,'+_.fw+'</div><div class="quick_style_example_content" style="'+(i==="headlines" ? "white-space:nowrap;":"")+bord+(_.tt!==undefined? 'text-transform:'+_.tt+';': '')+'font-style:'+_.fst+'; font-family:'+_.ff+';font-size:'+_.fs+'px; font-weight:'+_.fw+';line-height:'+_.lh+'px;letter-spacing:'+_.ls+'px">'+(_.content!==undefined ? _.content : RVS.LIB.QS_CONT[i])+'</div></div></div>';
						var family = _.ff.replace(/\ /g,'_');
						requiredGoogleFonts[family] = requiredGoogleFonts[family]===undefined ? {family:family, weights:[], italic:false} : requiredGoogleFonts[family];
						requiredGoogleFonts[family].font = _.ff;
						requiredGoogleFonts[family].italic = requiredGoogleFonts[family].italic===true ? true : _.fst;
						for (var s in RVS.V.sizes) {
							if(!RVS.V.sizes.hasOwnProperty(s)) continue;
							if (requiredGoogleFonts[family].weights.toString().indexOf(_.fw)===-1) requiredGoogleFonts[family].weights.push(_.fw);
						}
					break;
					case "buttons":
						var bord = _.bc!==undefined && _.bw!==undefined && _.bs!==undefined ? 'border-style:'+_.bs+';border-width:'+_.bw[0]+' '+_.bw[1]+' '+_.bw[2]+' '+_.bw[3]+';border-color:'+_.bc+';' : '',
							hovon = _.hs!==undefined ? 'data-ease="'+_.ease+'" data-filterbrightness="'+_.hfb+'" data-filtergrayscale="'+_.hfg+'" data-filterblur="'+_.hfbl+'" data-hoverbordercolor="'+_.hbc+'" data-hovercolor="'+_.hc+'" data-hoverspeed="'+_.hs+'" data-hoverbgcolor="'+_.hbg+'" ' : '',
							wh = _.w!==undefined ? 'width:'+_.w+';' : '';
						wh = _.h!==undefined ? wh+'height:'+_.h+';' : wh;
						_c += '<div class="quick_style_example_wrap '+(_.showsize!==undefined ? 'qsew_'+_.showsize:'')+'"><div class="quick_style_example" data-layertype="button" data-qstyletype="'+i+'" data-qstyleindex="'+j+'"><div class="quick_style_example_content '+_.cls+'" '+hovon+' style="'+bord+wh+(_.tt!==undefined? 'text-transform:'+_.tt+';': '')+(_.ta!==undefined? 'text-align:'+_.ta+';': '')+(_.bxc!==undefined ? 'box-shadow:'+_.bxh+' '+_.bxv+' '+_.bxb+' '+_.bxs+' '+_.bxc+';' : '')+'border-radius:'+_.br[0]+' '+_.br[1]+' '+_.br[2]+' '+_.br[3]+';padding:'+_.pd[0]+' '+_.pd[1]+' '+_.pd[2]+' '+_.pd[3]+';background:'+window.RSColor.get(_.bg)+';font-style:'+_.fst+'; font-family:'+_.ff+';font-size:'+_.fs+'px; color:'+_.color+';font-weight:'+_.fw+';line-height:'+_.lh+'px;letter-spacing:'+_.ls+'px">'+_.content+'</div></div></div>';
						var family = _.ff.replace(/\ /g,'_');
						requiredGoogleFonts[family] = requiredGoogleFonts[family]===undefined ? {family:family, weights:[], italic:false} : requiredGoogleFonts[family];
						requiredGoogleFonts[family].font = _.ff;
						requiredGoogleFonts[family].italic = requiredGoogleFonts[family].italic===true ? true : _.fst;
						for (var s in RVS.V.sizes) {
							if(!RVS.V.sizes.hasOwnProperty(s)) continue;
							if (requiredGoogleFonts[family].weights.toString().indexOf(_.fw)===-1) requiredGoogleFonts[family].weights.push(_.fw);
						}
					break;
					case "shadows":
						if (_.box_hoff!==undefined)
							_c += '<div class="quick_style_example_wrap '+(_.showsize!==undefined ? 'qsew_'+_.showsize:'')+'"><div class="quick_style_example qse_boxshadow" data-layertype="boxshadow" data-qstyletype="'+i+'" data-qstyleindex="'+j+'" style="box-shadow:'+_.box_hoff+' '+_.box_voff+' '+_.box_blur+' '+_.box_spread+' '+_.box_color+'"></div></div>';
						else
							_c += '<div class="quick_style_example_wrap '+(_.showsize!==undefined ? 'qsew_'+_.showsize:'')+'"><div class="quick_style_example qse_textshadow" data-layertype="textshadow" data-qstyletype="'+i+'" data-qstyleindex="'+j+'" style="text-shadow:'+_.text_hoff+' '+_.text_voff+' '+_.text_blur+' '+_.text_color+'">Shadow</div></div>';
					break;
					default:
					break;
				}
			}
			_c +='</div>';
		}
		_c += '</div></div>';
		_c += '</div></div>';
		window.qstyle_container = jQuery(_c);
		window.qstyle_scroll_targets = [];
		for (var i in requiredGoogleFonts) {
			if(!requiredGoogleFonts.hasOwnProperty(i)) continue;
			var familie = RVS.F.loadSingleFont(requiredGoogleFonts[i]);
			if (familie!==undefined) familiesToLoad.push(familie);
		}

		RVS.F.showWaitAMinute({fadeOut:500,text:"Building Quick Style Library"});

		// LOAD NEEDED FONTS
		RVS.F.do_google_font_load(familiesToLoad,undefined,"showQuickStyle");
		RVS.C.theEditor.append(window.qstyle_container);
		var id = 0;
		jQuery('.quick_style_menu_point').each(function() {
			if (this.dataset.ref!==undefined) {
				window.qstyle_scroll_targets.push({
					enable : true,
					library : this.dataset.library,
					obj : jQuery(this.dataset.ref),
					top : jQuery(this.dataset.ref).offset().top,
					height : jQuery(this.dataset.ref).height(),
					menu : jQuery(this),
					menu_js : this
				});
				this.dataset.ostref = id;
				id++;
			}
		});

		jQuery('#quick_style_scrollable').RSScroll({
			wheelPropagation:false
		});

		jQuery('#quick_style_scrollable').on('scroll',setQSMenu);

	}


	function setButtonDefault(_) {
		_.ls = _.ls===undefined ? 0 : _.ls;
		_.fw = _.fw===undefined ? 400 : _.fw;
		_.fst = _.fst===undefined ? 'normal' : _.fst;
		_.pd = _.pd===undefined ? ["0px","0px","0px","0px"] : _.pd;
		_.br = _.br===undefined ? ["0px","0px","0px","0px"] : _.br;
		_.bg = _.bg===undefined ? "transparent" : _.bg;
		_.bs = _.bs===undefined ? "none" : _.bs;
		_.tt = _.tt===undefined ? "none" : _.tt;
		return _;
	}

	function setContentColor(th,c) {
		var cont = th.closest('.quick_style_dep_all');
		RVS.LIB.QS[th[0].dataset.cont+'_color'] = c==='dark' ? '#000' : '#fff';
		cont.find('.quick_colorchange_'+(c==='dark' ? 'light' : 'dark')).removeClass("selected");
		tpGS.gsap.to(cont.find('.quick_style_example_content'),0.2,{color:RVS.LIB.QS[th[0].dataset.cont+'_color']});
		tpGS.gsap.to(cont.find('.quick_style_example_wrap'),0.2,{backgroundColor:c==='dark' ? '#fff' : '#202224'});
		cont.removeClass("light").removeClass("dark").addClass(c)
		th.addClass("selected");

	}

	function initLocalListeners() {
		RVS.DOC.on('click','.quick_colorchange_dark',function() {setContentColor(jQuery(this),'dark');});
		RVS.DOC.on('click','.quick_colorchange_light',function() {setContentColor(jQuery(this),'light');});

		RVS.DOC.on('quickstyletrigger',function() {
			RVS.F.openQuickStyle(false);
		});

		RVS.DOC.on('click','#quick_style_close', RVS.F.closeQuickStyle);
		RVS.DOC.on('showQuickStyle',RVS.F.showQuickStyle);
		RVS.DOC.on('click','.quick_style_example',function() {
			var qstype = this.dataset.qstyletype,
				_ = RVS.F.safeExtend(true,{},RVS.LIB.QS[qstype][this.dataset.qstyleindex]),
				ltype = this.dataset.layertype,
				newlayer = false;
			RVS.F.updateScreenShrinks();
			_ = setButtonDefault(_);
			if (RVS.selLayers.length===0) {
				if (ltype==="textshadow" || ltype==="boxshadow") return;
				var newID = RVS.F.addLayer({type:ltype,forceSelect:true});
				//if (ltype!=="button") RVS.F.intelligentUpdateValuesOnLayer(newID);

				RVS.F.selectLayers({id:newID,overwrite:true, action:"add"});
				//newlayer = true;

			}
			if (RVS.S.qstyleBackupGroup===undefined) RVS.F.openBackupGroup({id:"quickstyle",txt:"Quick Style Change",icon:"invert_colors"});
			for (var lid in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(lid)) continue;

				/* var l = RVS.L[RVS.selLayers[lid]], */
				var pre = RVS.S.slideId+".layers."+RVS.selLayers[lid]+".",
					updateIntelligentInherit = false;

				switch (ltype) {
					case "text":
					case "button":
						if ((RVS.L[RVS.selLayers[lid]].type==="text" && ltype==="text") || (RVS.L[RVS.selLayers[lid]].type==="button" && ltype==="button")) {
							updateIntelligentInherit = true;
							if (ltype==="button") {
								/*RVS.F.updateSliderObj({path:pre+'behavior.autoResponsive',val:false});
								RVS.F.updateSliderObj({path:pre+'behavior.intelligentInherit',val:false});
								RVS.F.updateSliderObj({path:pre+'behavior.responsiveChilds',val:false});
								RVS.F.updateSliderObj({path:pre+'behavior.responsiveOffset',val:false});*/
							}

							if (ltype==="text" && (qstype==="headlines" || qstype==="content")) RVS.F.updateSliderObj({path:pre+'idle.color.d.v',val:RVS.LIB.QS[qstype+'_color']});
							if (_.fs!==undefined) RVS.F.updateSliderObj({path:pre+'idle.fontSize.d.v',val:_.fs});
							if (_.w!==undefined) RVS.F.updateSliderObj({path:pre+'size.width.d.v',val:_.w}); else RVS.F.updateSliderObj({path:pre+'size.width.d.v',val:"auto"});
							if (_.h!==undefined) RVS.F.updateSliderObj({path:pre+'size.height.d.v',val:_.h}); else RVS.F.updateSliderObj({path:pre+'size.height.d.v',val:"auto"});
							if (_.h!==undefined && _.h!=="auto")
								RVS.F.updateSliderObj({path:pre+'size.minHeight.d.v',val:_.h});
							else
								RVS.F.updateSliderObj({path:pre+'size.minHeight.d.v',val:"0px"});

							if (_.w!==undefined)
								RVS.F.updateSliderObj({path:pre+'size.minWidth.d.v',val:_.w});
							else
								RVS.F.updateSliderObj({path:pre+'size.minWidth.d.v',val:"none"});
							if (_.lh!==undefined) RVS.F.updateSliderObj({path:pre+'idle.lineHeight.d.v',val:_.lh});
							if (_.ls!==undefined) RVS.F.updateSliderObj({path:pre+'idle.letterSpacing.d.v',val:_.ls});

							if (_.fw!==undefined) RVS.F.updateSliderObj({path:pre+'idle.fontWeight.d.v',val:_.fw});
							if (_.fst!==undefined) RVS.F.updateSliderObj({path:pre+'idle.fontStyle',val:(_.fst==="italic")});
							if (_.ff!==undefined) RVS.F.updateSliderObj({path:pre+'idle.fontFamily',val:_.ff});

							if (_.ta!==undefined) RVS.F.updateSliderObj({path:pre+'idle.textAlign.d.v',val:_.ta});
							if (_.tt!==undefined) RVS.F.updateSliderObj({path:pre+'idle.textTransform',val:_.tt});
							if (_.pd!==undefined) RVS.F.updateSliderObj({path:pre+'idle.padding.d.v',val:_.pd});
							if (_.bg!==undefined) RVS.F.updateSliderObj({path:pre+'idle.backgroundColor',val:_.bg});

							if (_.br!==undefined) RVS.F.updateSliderObj({path:pre+'idle.borderRadius.v',val:_.br});
							if (_.bc!==undefined) RVS.F.updateSliderObj({path:pre+'idle.borderColor',val:_.bc});
							if (_.bw!==undefined) RVS.F.updateSliderObj({path:pre+'idle.borderWidth',val:_.bw});
							if (_.bs!==undefined) RVS.F.updateSliderObj({path:pre+'idle.borderStyle.d.v',val:_.bs});

							if (_.bxc!==undefined) {
								RVS.F.updateSliderObj({path:pre+'idle.boxShadow.inuse',val:true});
								RVS.F.updateSliderObj({path:pre+'idle.boxShadow.hoffset.d.v',val:_.bxh});
								RVS.F.updateSliderObj({path:pre+'idle.boxShadow.voffset.d.v',val:_.bxv});
								RVS.F.updateSliderObj({path:pre+'idle.boxShadow.blur.d.v',val:_.bxb});
								RVS.F.updateSliderObj({path:pre+'idle.boxShadow.spread.d.v',val:_.bxs});
								RVS.F.updateSliderObj({path:pre+'idle.boxShadow.color',val:_.bxc});
							} else {
								RVS.F.updateSliderObj({path:pre+'idle.boxShadow.inuse',val:false});
							}

							if (_.color!==undefined) RVS.F.updateSliderObj({path:pre+'idle.color.d.v',val:_.color});

							if (_.cls!==undefined)
								RVS.F.updateSliderObj({path:pre+'runtime.internalClass',val:_.cls});
							else
								RVS.F.updateSliderObj({path:pre+'runtime.internalClass',val:""});

							if (_.action!==undefined) {
								var action;
								action = RVS.L[RVS.selLayers[lid]]!==undefined && RVS.L[RVS.selLayers[lid]].actions!==undefined && RVS.L[RVS.selLayers[lid]].actions.action!==undefined ? RVS.L[RVS.selLayers[lid]].actions.action : undefined;
								if (action!==undefined) {
									var toggleexists = false;
									for (var i in action) if (action.hasOwnProperty(i)) {
										if (toggleexists===true) continue;
										toggleexists = action[i].action==="toggle_class" && (""+action[i].layer_target)==(""+RVS.selLayers[lid]) && action[i].toggle_class==="open";
									}
									_.action.layer_target = ""+RVS.selLayers[lid];
									if (toggleexists===false) action.push(_.action)
								}
							}

							//if ( RVS.L[RVS.selLayers[lid]].type==="button") {
								if (_.content!=undefined && ltype==="button") {
									RVS.F.updateSliderObj({path:pre+'text',val:_.content});
									RVS.H[RVS.selLayers[lid]].c.html(_.content);
								}
							//}

							if (_.hs!==undefined) {
								RVS.F.updateSliderObj({path:pre+'hover.usehover',val:true});
								RVS.F.updateSliderObj({path:pre+'hover.speed',val:_.hs});
								if (_.hc!==undefined) RVS.F.updateSliderObj({path:pre+'hover.color',val:_.hc});
								if (_.hbg!==undefined) RVS.F.updateSliderObj({path:pre+'hover.backgroundColor',val:_.hbg}); else if (_.bg!==undefined) RVS.F.updateSliderObj({path:pre+'hover.backgroundColor',val:_.bg});
								if (_.br!==undefined) RVS.F.updateSliderObj({path:pre+'hover.borderRadius.v',val:_.br});
								if (_.hbc!==undefined) RVS.F.updateSliderObj({path:pre+'hover.borderColor',val:_.hbc}); else if (_.bc!==undefined) RVS.F.updateSliderObj({path:pre+'hover.borderColor',val:_.bc});
								if (_.ease!==undefined) RVS.F.updateSliderObj({path:pre+'hover.ease',val:_.ease});
								if (_.hfb!==undefined) {
									RVS.F.updateSliderObj({path:pre+'hover.filter.grayscale',val:_.hfg});
									RVS.F.updateSliderObj({path:pre+'hover.filter.brightness',val:_.hfb});
									RVS.F.updateSliderObj({path:pre+'hover.filter.blir',val:_.hfbl});
								}
								if (_.bw!==undefined) RVS.F.updateSliderObj({path:pre+'hover.borderWidth',val:_.bw});
								if (_.bs!==undefined) RVS.F.updateSliderObj({path:pre+'hover.borderStyle',val:_.bs});

							} else {
								RVS.F.updateSliderObj({path:pre+'hover.usehover',val:false});
							}


						}
					break;
					case "boxshadow":

						RVS.F.updateSliderObj({path:pre+'idle.boxShadow.hoffset.d.v',val:_.box_hoff});
						RVS.F.updateSliderObj({path:pre+'idle.boxShadow.voffset.d.v',val:_.box_voff});
						RVS.F.updateSliderObj({path:pre+'idle.boxShadow.blur.d.v',val:_.box_blur});
						RVS.F.updateSliderObj({path:pre+'idle.boxShadow.spread.d.v',val:_.box_spread});
						RVS.F.updateSliderObj({path:pre+'idle.boxShadow.color',val:_.box_color});
						RVS.F.updateSliderObj({path:pre+'idle.boxShadow.inuse',val:true});
					break;
					case "textshadow":

						RVS.F.updateSliderObj({path:pre+'idle.textShadow.hoffset.d.v',val:_.text_hoff});
						RVS.F.updateSliderObj({path:pre+'idle.textShadow.voffset.d.v',val:_.text_voff});
						RVS.F.updateSliderObj({path:pre+'idle.textShadow.blur.d.v',val:_.text_blur});
						//RVS.F.updateSliderObj({path:pre+'idle.textShadow.spread.d.v',val:_.text_spread});
						RVS.F.updateSliderObj({path:pre+'idle.textShadow.color',val:_.text_color});
						RVS.F.updateSliderObj({path:pre+'idle.textShadow.inuse',val:true});
					break;
				}
				//if (ltype!=="button")

				RVS.F.intelligentUpdateValuesOnLayer(RVS.selLayers[lid]);
				//SET INTELLIGENT INHERITING ON THE LAYER AFTER UPDATING IT
				if (updateIntelligentInherit) RVS.F.setToIntelligentUpdate(true);
				//if (ltype!=="button" && newlayer===true)
				RVS.F.updateSliderObj({path:RVS.S.slideId+'.layers.'+RVS.selLayers[lid]+'.behavior.intelligentInherit',val:true});
				RVS.F.drawHTMLLayer({uid:RVS.selLayers[lid]});
			}
			if (RVS.S.qstyleBackupGroup===undefined) RVS.F.closeBackupGroup({id:"quickstyle"});
			RVS.F.updateLayerInputFields();
		});

		RVS.DOC.on('click','.quick_style_menu_point',function() {
			setQSMenu();
			var o = { val:jQuery('#quick_style_scrollable').scrollTop()},
				res = o.val + window.qstyle_scroll_targets[this.dataset.ostref].top;

            tpGS.gsap.to(o,0.6,{val:res, onUpdate:function() {
				jQuery('#quick_style_scrollable').scrollTop(o.val);
			}, ease:"power3.out"});
			setQSMenu();
		});


		RVS.DOC.on('mouseenter','.quick_style_example_content', function() {
			if (this.dataset.hoverspeed!==undefined) {
				var j = jQuery(this);
				if (j.data('hoveranim')===undefined) {
					var ntl = tpGS.gsap.timeline(),
						animto = {color:this.dataset.hovercolor, backgroundColor:this.dataset.hoverbgcolor};

					if (this.dataset.hoverbordercolor!==undefined && this.dataset.hoverbordercolor!=='undefined') animto.borderColor = this.dataset.hoverbordercolor;
					if (this.dataset.filterbrightness!==undefined && this.dataset.filterbrightness!=='undefined') {
						animto.filter =  'blur('+this.dataset.filterblur+'px) grayscale('+this.dataset.filtergrayscale+'%) brightness('+this.dataset.filterbrightness+')';
						animto["-webkit-filter"] =  'blur('+this.dataset.filterblur+'px) grayscale('+this.dataset.filtergrayscale+'%) brightness('+this.dataset.filterbrightness+')';
						tpGS.gsap.set(this,{filter:'blur(0px) grayscale(0%) brightness(100%)', '-webkit-filter':'blur(0px) grayscale(0%) brightness(100%)'});
					}
					if (this.dataset.ease!==undefined) animto.ease = this.dataset.ease;
					ntl.add(tpGS.gsap.to(this,this.dataset.hoverspeed/1000,animto));
					j.data('hoveranim',ntl);
				}
				j.data('hoveranim').play();
			}
		});
		RVS.DOC.on('mouseleave','.quick_style_example_content', function() {
			if (this.dataset.hoverspeed!==undefined)
				jQuery(this).data('hoveranim').reverse();
		});

	}


	function setQSMenu() {
		/* var _so = jQuery('#quick_style_scrollable').scrollTop(), */
		var lastitem = -1;
		/* lasttop = 0; */

		for (var i in window.qstyle_scroll_targets) if (window.qstyle_scroll_targets.hasOwnProperty(i)) {

			if (window.qstyle_scroll_targets[i].obj.length>0 && window.qstyle_scroll_targets[i].enable) {
				window.qstyle_scroll_targets[i].top = window.qstyle_scroll_targets[i].obj.offset().top-100;
				window.qstyle_scroll_targets[i].height = window.qstyle_scroll_targets[i].obj.height();

				if (30>=window.qstyle_scroll_targets[i].top && 0<=window.qstyle_scroll_targets[i].top + window.qstyle_scroll_targets[i].height) lastitem = i;
			}
		}
		lastitem = lastitem===-1 ? window.qstyle_scroll_targets.length-1 : lastitem;
		jQuery('.quick_style_menu_point').removeClass("active");
		window.qstyle_scroll_targets[lastitem].menu.addClass("active");
	}

})();
