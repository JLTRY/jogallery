
function imagesviewer($, id, values) {
	this._values = values;
	this._id = id;
	this._modulo = 50;
	this._start = 0;
	this._vidextensions = ["mp4", "m2ts", "mov", "MOV"];

	this.show = function($, start, modulo) {
		var divid = $('#' + this._id);

		divid.html("");
		for (let i=start; i < start + modulo; i++)
		{
			if (i < this._values.length)
			{
				var content = "";
				var tvalue = this._values[i];
				var sid = tvalue['filename'];
				var urlfilename = tvalue['urlfilename'];
				var urlshortfilename = tvalue['urlshortfilename'];
				var basename = tvalue['basename'];
				var moddate = tvalue['moddate'];
				var extension = basename.split(".").at(-1);
				if (moddate != -1) {
					date = new Date(moddate* 1000);
					sdate =  "<b>" + date.getDate()+ "/"+(date.getMonth()+1)+ "/"+date.getFullYear() + " </b>";
				} else {
					sdate = "";
				}
				if (this._vidextensions.includes(extension)) 
				{
					/*content = '<video class="lazy" controls="controls" width="auto" height="240" ><source src="'+ urlfilename + basename + '" type="video/mp4"> Sorry, your browser doesnt support 	embedded videos</video>';*/
					/*content ="<a  href=\"" + urlfilename +"\">" + basename +"</a><br>";*/
					content += "<a data-fancybox=\"gallery\"  href=\"" + urlfilename +"\"  data-caption=\"" +  sdate + tvalue['comment'] + "\">" +
								"<span class=\"parent\"><img class=\"image2\" width=\"128\" id=\""+ sid + "\" src=\"" + urlshortfilename + " \"/><i class=\"fa-solid fa-video image1\"></i></span></a>";
				}
				else {
					content += "<a data-fancybox=\"gallery\"  href=\"" + urlfilename +"\"  data-caption=\"" +  sdate + tvalue['comment'] + "\"><img id=\""+ sid + "\" src=\"" + urlshortfilename +"\" /></a>";
				}
				divid.append(content);
			}
		}
	};
	return this;
}

function jimages_getimages($, id, listfiles)
{
	var thmb = new imagesviewer($, id, listfiles);
	thmb.show($, 0, 1500);
}

function initfancybox($, page=null) {
	Fancybox.bind("[data-fancybox]", {
	  // Your options go here
		default: { buttons : [
							'slideShow',
							'fullScreen',
							'thumbs',
							'share',
							'download',
							'zoom',
							'close',
							'zoomIn',
							'zoomOut'
							]
		}			,
		on: {  
		'*': (event, fancybox, slide) => {
			console.log("event:" + event.id);
		},
		load: (fancybox, slide) => {
			console.log(`#${slide.index} slide is loaded!`);
			//console.log(`${slide.caption}`);
			//** Step 1: Get a reference to a Fancybox instance
			//** Step 2: Get a reference to a Carousel Autoplay plugin
			//const autoplay = fancybox.plugins.Slideshow.ref;
			// or
			//const autoplay = fancybox.Carousel.plugins.Autoplay;
			//** Step 3: Use any Carousel Autoplay API method, for example:
			// Start autoplay
			//autoplay.start();
		},
		/*"Carousel.change" : (fancybox) => {
            var slide = fancybox.getSlide();
			console.log(slide.triggerEl.dataset);
			slide.triggerEl.dataset.src = slide.triggerEl.dataset.lazySrc;
		},
		ready: (fancybox, slide) => {
			// Remplacer la source par celle stockée dans data-lazy-src
				console.log("ici");
				console.log(slide.triggerEl.dataset);
				if (slide.triggerEl.dataset['lazySrc']) {
					slide.triggerEl.dataset['src'] = slide.triggerEl.dataset['lazySrc'];
				}
			}*/
		},
		template : {  // Close button icon  
			"closeButton":      'closeme <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" tabindex="-1"><path d="M20 20L4 4m16 0L4 20"/></svg>',
			// Loading indicator icon  
			"spinner":      '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="25 25 50 50" tabindex="-1"><circle cx="50" cy="50" r="20"/></svg>',
			// Main container element 
			"main": '<h1>here</h1> <div  class="fancybox__container"  role="dialog"  aria-modal="true"  aria-hidden="true"  aria-label="{{MODAL}}"  tabindex="-1">  <div class="fancybox__backdrop"></div><input type="text" id="comment" name="lname">  <div class="fancybox__carousel"></div></div>',
			video: '<video class="fancybox-video lazy" controls controlsList="nodownload">' +
					'<source src="{{src}}" type="{{format}}" />' +
					"Your browser doesn't support HTML5 x video" +
					"</video>", // custom video format
		},
		
		Slideshow: {
			playOnStart: false,
		},
	});
		
	if (page != -1) {
		$("a[data-fancybox='gallery']").each(function(index, value) 
		{ 
			if (index==page)
			{
				$(value)[0].click();
			}
		});
	}
}

