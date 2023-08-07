/************************** WPSOL CODE **************************/
/**
 * WP Speed Of Light List of functions to call
 * @type {*[]}
 */
var wpsolf = [];

/**
 * Errors thrown during the process
 * @type {*[]}
 */
var wpsole = [];

/**
 * This function is called by all events on html page loading to
 * list the functions to call in the right order
 *
 * @param id
 * @param type
 */
var wpsolr = function (id, type, url) {
    wpsolf.push({id, type, url});
};

/**
 * This function is called when webpack is detected in a script and will
 * fake a script tag after all other tags to allow webpack to detect script path
 * https://github.com/webpack/webpack/blob/4837c3ddb9da8e676c73d97460e19689dd9d4691/lib/runtime/AutoPublicPathRuntimeModule.js
 * @param url
 * @param type 1 to add the script 0 to remove it
 */
var wpsolw = function(url, type) {
    // Remove previous faker
    var p=document.getElementById('wpsolfw');
    if (p) {
        // Remove
        p.remove();
    }


    if (type === 1) {
        var s = document.createElement("script");
        s.type = "wpsol/faker";
        s.id = "wpsolfw";
        s.src=url;
        document.body.appendChild(s);
    }
}

/**
 * Run a script by ID
 * @param ij
 */
function wpsol_run(ij) {
    // Check if there is another script to run
    if (wpsolf[ij] === undefined) {
        // This is the end
        return;
    }

    try {
        if (wpsolf[ij].type === "inline") {
            var e = document.getElementById("wpsolr-" + wpsolf[ij].id);
            var s = document.createElement("script");
            var f = "wpsolr" + wpsolf[ij].id;
            var ev = eval(f).toString();
            var c = /^function\s?\(.*?\){(.*)}$/ms.exec(ev);
            s.innerHTML = c[1];
            s.type = "text/javascript";
            s.id =  "wpsolr-" + wpsolf[ij].id;
            e.parentNode.replaceChild(s, e);
            // console.log("Calling wpsol_run(" + (ij + 1) + ")");
            wpsol_run(ij + 1);
        } else if (wpsolf[ij].type === "url") {
            var f = "wpsolr" + wpsolf[ij].id;
            eval(f).apply(window, [wpsolf[ij].url]);
            // console.log("Calling wpsol_run(" + (ij + 1) + ")");
            wpsol_run(ij + 1);
        } else if (wpsolf[ij].type === "excluded") {
            var s = document.querySelector('script[type^="wpsol' + wpsolf[ij].id + '-"]');
            var p = s;
            p.onload = function () {
                // console.log("Calling wpsol_run(" + (ij + 1) + ") from onload");
                wpsol_run(ij + 1);
            };
            s.remove;
            p.type = p.type.replace(/^wpsol[0-9]+-/, "");
            document.body.appendChild(p);
        } else if (wpsolf[ij].type === "injected") {
            var original_script_elem = document.getElementById("wpsolr-" + ij);
            var new_script_elem = document.createElement("script");
            new_script_elem.type = "text/javascript";
            new_script_elem.src=wpsolf[ij].url;
            new_script_elem.onload = function() {
                // console.log("Calling wpsol_run(" + (ij + 1) + ") from onload injected");
                wpsol_run(ij + 1);
            };
            original_script_elem.parentNode.insertBefore(new_script_elem, original_script_elem);
        } else {
            // Unknown script, continue anymay
            // console.log("Calling wpsol_run(" + (ij + 1) + ")");
            wpsol_run(ij + 1);
        }
    }catch(e){
       if (wpsolf[ij].type === 'inline') {
           console.error("An error occurred in the inline script " + ij);
           console.warn(e);
           // console.log("Calling wpsol_run(" + (ij + 1) + ") from error inline");
           wpsole.push(wpsolf[ij]);
           wpsol_run(ij + 1);
       } else if(wpsolf[ij].type === 'injected'){
           console.error("An error occurred in a the injected script " + ij + ", you should  try to exclude the script url " + wpsolf[ij].url + " in WP Speed of Light settings");
           console.warn(e);
           // console.log("Calling wpsol_run(" + (ij + 1) + ") from error injected");
           wpsole.push(wpsolf[ij]);
           wpsol_run(ij + 1);
       } else {
           console.error("An error occurred in the script " + ij + ", you can try to exclude the script " + wpsolf[ij].url + " in WP Speed of Light settings");
           console.warn(e);
           // Try to load again the script by url injection
           wpsolf[ij].type = "injected";
           // console.log("Calling wpsol_run(" + ij + ") from error");
           wpsole.push(wpsolf[ij]);
           wpsol_run(ij);
       }
    }
}

window.addEventListener("DOMContentLoaded", function () {
    // Trigger the event to load all the scripts once the whole DOM is loaded
    window.dispatchEvent(new Event("wpsolr"));
    //Let's run the real process here
    wpsol_run(0);
}, {capture: true, once: true, passive: true});