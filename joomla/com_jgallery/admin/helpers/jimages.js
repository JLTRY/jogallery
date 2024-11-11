function loadscript(url, callback)
{
    // adding the script element to the head as suggested before
   var head = document.getElementsByTagName('head')[0];
   var script = document.createElement('script');
   script.type = 'text/javascript';
   script.src = url;

   // then bind the event to the callback function 
   // there are several events for cross browser compatibility
   //script.onreadystatechange = callback;
   //script.onload = callback;

   // fire the loading
   head.appendChild(script);
}

function imagesretriever($, id, urlroot, dir, values) {
	this._values = values;
	this._id = id;
	this._urlroot = urlroot;
	this._directory = dir;
	this._modulo = 50;
	this._start = 0;
	this._extensions = ["mp4", "m2ts", "mov", "MOV"];

	this.getthumbs = function(index, nb) {
		var url = this._urlroot + "/index.php?option=com_jgallery&view=jgallery&tmpl=component&layout=ping&directory64="
				 + this._directory + "&XDEBUG_SESSION_START=test";
		$.ajax({url: url ,
			type: "POST",
			dataType: "json",
			async: 'false',
			context: this,
			success: function(tvalue) {
				for (let i= index ; i < index + this._modulo; i++) 
				{
					if (i < this._values.length)
					{
						var value = this._values[i];
						sid = value['filename'];
						urlfilename = value['urlfilename'];
						urlshortfilename = value['urlshortfilename'];
						img = $('#'+sid);
						$('#'+sid).attr('src' ,urlshortfilename);
						$('#'+sid).removeAttr('width');
					}
				}
			},
			error: function(xhr, status, text) {
				var response = xhr.responseText;
				console.log('Failure!');
				if (response) {
					console.log(response);
				} else {
					// This would mean an invalid response from the server - maybe the site went down or whatever...
				}
			}
		}
		)
	};

	this.show = function($, start, modulo) {
		var divid = $('#' + this._id);

		divid.html("");
		for (let i=start; i < start + modulo; i++)
		{
			var content = "<style>.parent {position: relative; top: 0; left: 0; display:inline;}"+
							 ".image1 { position: relative; top: -5px; left: -50px;}" +
							 ".image2 { position: relative; top: 2px; left: 2px; border: 1px solid #000000;}</style>";
			if (i < this._values.length)
			{
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
				if (this._extensions.includes(extension)) 
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
		window.lazyLoadOptions = {
				elements_selector: ".lazy"
		};
		loadscript("https://cdn.jsdelivr.net/npm/vanilla-lazyload@16.1.0/dist/lazyload.js");
	};
	return this;
}

function jimages_getimages($, id, urlroot, directory, listfiles)
{
	var thmb = new imagesretriever($, id, urlroot, directory, listfiles);
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
					'*': (event, fancybox, slide) => {        console.log(`event: ${event}`);      },
					load: (fancybox, slide) => {
						//console.log(`#${slide.index} slide is loaded!`);    
						//console.log(`${slide.caption}`);
						//** Step 1: Get a reference to a Fancybox instance
						//** Step 2: Get a reference to a Carousel Autoplay plugin
						const autoplay = fancybox.plugins.Slideshow.ref;
						// or
						//const autoplay = fancybox.Carousel.plugins.Autoplay;
						//** Step 3: Use any Carousel Autoplay API method, for example:
						// Start autoplay
						autoplay.start();
						},
					},
					template : {  // Close button icon  
					closeButton:      '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" tabindex="-1"><path d="M20 20L4 4m16 0L4 20"/></svg>',
					// Loading indicator icon  
					spinner:      '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="25 25 50 50" tabindex="-1"><circle cx="50" cy="50" r="20"/></svg>',
					// Main container element 
					main: '<div  class="fancybox__container"  role="dialog"  aria-modal="true"  aria-hidden="true"  aria-label="{{MODAL}}"  tabindex="-1">  <div class="fancybox__backdrop"></div><input type="text" id="comment" name="lname">  <div class="fancybox__carousel"></div></div>'
					},
					Slideshow: {
						playOnStart: true,
					},
	});
	if (page != -1) {
		$("a[data-fancybox='gallery']").each(function(index, value) 
			{ 
				if (index== page)
				{
					$(value)[0].click();
				}
			});

	}
}