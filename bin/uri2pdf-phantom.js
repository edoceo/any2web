/**
    https://groups.google.com/forum/?fromgroups=#!topic/phantomjs/rooLTvR7gEU
    @see http://www.iphoneresolution.com/

    @see https://github.com/ariya/phantomjs/blob/master/examples/rasterize.js
*/

var system = require('system');

var UA_DESKTOP = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36';

var req_page = system.args[1]; // http://....
var out_path = system.args[2]; // filename

var web_kind = system.args[3]; // 'desktop'; // ;
if (undefined === typeof web_kind) web_kind = 'desktop';
if (!web_kind) web_kind = 'desktop';

var page;

function make_page()
{
	var p = require('webpage').create();

	p.onAlert = function(msg) {
		console.log('Alert: ' + msg);
	};

	p.onPrompt = function(msg, val) {
		console.log('Prompt: ' + msg);
		val = 'any2web.io';
		return val;
	};

	return p;
}

function save_page_exit(page,file,w,h)
{
	// Most Popular Size
	// @see http://techcrunch.com/2012/04/11/move-over-1024x768-the-most-popular-screen-resolution-on-the-web-is-now-1366x768/
	if ('undefined' == typeof w) w = 1024;
	if ('undefined' == typeof h) h = 768;

	// Set to white if transparent
	page.evaluate(function() {
		if (getComputedStyle(document.body, null).backgroundColor === 'rgba(0, 0, 0, 0)') {
			document.body.bgColor = 'white';
		}
	});

	// Update Pages Screen Object?
	//page.evaluate(function () {
	//	 (function () {
	//		window.screen = {
	//			width: 320,
	//			height: 360
	//		};
	//	})();
	//});

    // page.viewportSize = {
	// 	width:w,
	// 	height:h
	// };
    // page.clipRect = {
	// 	top: 0,
	// 	left: 0,
	// 	width:w,
	// 	height:h
	// };
	// page.driver.resize(1280, 1280)

    setTimeout(function() {
        page.render(file);
        phantom.exit(0);
    }, 3210);
};

// Big Size
switch (web_kind) {
case 'android':
	page = make_page();
    page.customHeaders = {
        'User-Agent': 'Mozilla/5.0 (Linux; Android 4.0.4; Galaxy Nexus Build/IMM76B) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.133 Mobile Safari/535.19',
    };
    // page.zoomFactor = 0.80;
    page.open(req_page, function () {
		save_page_exit(page, out_path, 384, 519);
    });
	break;
case 'iphone':
case 'mobile':

	console.log("Making and iPhone Style Screenshot");
	page = make_page();

    // Mobile Size (iPhone 4)
    page.customHeaders = {
        'User-Agent': 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3',
    };
    // page.zoomFactor = 0.80;
    page.open(req_page, function () {
		save_page_exit(page, out_path, 320, 360);
    });
    break;

case 'big':
    page = make_page();
    page.customHeaders = {
        'User-Agent': UA_DESKTOP,
    };
    page.open(req_page, function () {
		save_page_exit(page, out_path, 1280, 1280);
    });
    break;

case 'tablet':
    // iPad - 
    page = make_page();
    page.customHeaders = {
        'User-Agent': 'Mozilla/5.0 (iPad; CPU OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3',
    };
    page.open(req_page, function () {
		save_page_exit(page, out_path, 750, 920);
    });
    break;

// Desktop / Default
case 'desktop':
default:

    page = make_page();
    page.customHeaders = {
        'User-Agent': UA_DESKTOP,
    };
	page.viewportSize = {
		width: 1280,
		height: 1280 * 0.75
	};
    page.open(req_page, function (status) {
		save_page_exit(page, out_path);
    });

}
