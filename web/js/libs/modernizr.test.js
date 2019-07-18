/* Modernizr 2.8.1 (Custom Build) | MIT & BSD
 * Build: http://modernizr.com/download/#-fontface-backgroundsize-borderimage-borderradius-boxshadow-flexbox-hsla-multiplebgs-opacity-rgba-textshadow-cssanimations-csscolumns-generatedcontent-cssgradients-cssreflections-csstransforms-csstransforms3d-csstransitions-applicationcache-canvas-canvastext-draganddrop-hashchange-history-audio-video-indexeddb-input-inputtypes-localstorage-postmessage-sessionstorage-websockets-websqldatabase-webworkers-geolocation-inlinesvg-smil-svg-svgclippaths-touch-webgl-shiv-cssclasses-addtest-prefixed-teststyles-testprop-testallprops-hasevent-prefixes-domprefixes-load
 */
;


window.ModernizrTest = (function( window, document, Modernizr ) {
    var my={};
    my.version = '2.8.1';

    //my.testproCSS="fontface-backgroundsize-borderimage-borderradius-boxshadow-flexbox-hsla-multiplebgs-opacity-rgba-textshadow-cssanimations-csscolumns-generatedcontent-cssgradients-cssreflections-csstransforms-csstransforms3d-csstransitions";
    my.testproCSS={"@font-face":"fontface",
        "background-size":"backgroundsize",
        "border-image":"borderimage",
        "border-radius":"borderradius",
        "box-shadow":"boxshadow",
        "Flexible Box Model":"flexbox",
        "hsla()":"hsla",
        "Multiple backgrounds":"multiplebgs",
        "opacity":"opacity",
        "rgba()":"rgba",
        "text-shadow":"textshadow",
        "CSS Animations":"cssanimations",
        "CSS Columns":"csscolumns",
        "Generated Content":"generatedcontent",  //:before/:after
        "CSS Gradients":"cssgradients",
        "CSS Reflections":"cssreflections",
        "CSS 2D Transforms":"csstransforms",
        "CSS 3D Transforms":"csstransforms3d",
        "CSS Transitions":"csstransitions"};

    my.testproHTML5={
        "applicationCache":"applicationcache",
        "Canvas":"canvas",
        "Canvas Text":"canvastext",
        "Drag and Drop":"draganddrop",
        "hashchange Event":"hashchange",
        "History Management":"history",
        "HTML5 Audio":"audio",
        "HTML5 Audio":"audio.ogg",
        "HTML5 Audio":"audio.mp3",
        "HTML5 Audio":"audio.wav",
        "HTML5 Audio":"audio.m4a",
        "HTML5 Video":"video",
        "HTML5 Video":"video.ogg",
        "HTML5 Video":"video.webm",
        "HTML5 Video":"video.h264",
        "IndexedDB":"indexeddb",
        "Input Attributes":"input",
        "Input Types":"inputtypes",
        "localStorage":"localstorage",
        "Cross-window Messaging":"postmessage",
        "sessionStorage":"sessionstorage",
        "Web Sockets":"websockets",
        "Web SQL Database":"websqldatabase",
        "Web Workers":"webworkers"};
    //Audio:    ogg, mp3, wav, m4a
    //Video:    ogg, webm, h264
    //Input Types:          search, tel, url, email, datetime, date, month, week, time, datetime-local, number, range, color
    //Input Attributes:     autocomplete, autofocus, list, placeholder, max, min, multiple, pattern, required, step

    my.testproMisc={
        "Geolocation API":"geolocation",
        "Inline SVG":"inlinesvg",
        "SMIL":"smil",
        "SVG":"svg",
        "SVG Clip paths":"svgclippaths",
        "Touch Events":"touch",
        "WebGL":"webgl"};


    my.printTest=function(){

        function printTab(testStr){
            var outs=""
            //var proArr = testStr.split('-');
            for(var i in testStr){
                if(testStr[i].indexOf(".")>0){
                    var subproArr=testStr[i].split('.');
                    outs = outs + "<dt> " + i + "</dt><dd> " +testStr[i] +" : <span class='red'>" + Modernizr[subproArr[0]][subproArr[1]] +"</span></dd>";
                }else{
                    outs = outs + "<dt> " + i + "</dt><dd> " +testStr[i] +" : <span class='red'>" + Modernizr[testStr[i]] +"</span></dd>";
                }
                //console.log(Modernizr[proArr[i]])
                if(typeof Modernizr[testStr[i]]=="object"){
                    outs = outs + "<dt></dt><dd> ";
                    for(var v in Modernizr[testStr[i]]){
                        outs = outs +" ("+ v+": "+ Modernizr[testStr[i]][v]+") ";
                    }
                    outs = outs + "</dd> ";
                }
            }
            return outs;
        }

        var outhtml="<div><p><b>CSS FEATURES</b><hr></p><dl>"+printTab(my.testproCSS)+"</dl></div><br>";

        outhtml=outhtml+"<div><p><b>HTML5 FEATURES</b><hr></p><dl>"+printTab(my.testproHTML5)+"</dl></div><br>";

        outhtml=outhtml+"<div><p><b>MISCELLANEOUS</b><hr></p><dl>"+printTab(my.testproMisc)+"</dl></div><br>";

        return outhtml;
    }
    //return Modernizr;
    return my;

})(this, this.document, Modernizr);