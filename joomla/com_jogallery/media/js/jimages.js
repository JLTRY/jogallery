function loadscript(url, callback)
{
    // adding the script element to the head as suggested before
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;

   // then bind the event to the callback function
   // there are several events for cross browser compatibility
    script.onreadystatechange = callback;
    script.onload = callback;

   // fire the loading
    head.appendChild(script);
}

function imagesviewer($, id, values)
{
    this._values = values;
    this._id = id;
    this._modulo = 50;
    this._start = 0;
    this._vidextensions = ["mp4", "m2ts", "mov", "MOV"];

    this.show = function ($, start, modulo, options=[]) {
        var divid = $('#' + this._id);

        divid.html("");
        var page = options['page']?? -1;
        for (let i = start; i < start + modulo; i++) {
            if (i < this._values.length) {
                var content = "";
                var tvalue = this._values[i];
                var sid = tvalue['filename'];
                var urlfilename = tvalue['urlfilename'];
                var urlshortfilename = tvalue['urlshortfilename'];
                var basename = tvalue['basename'];
                var moddate = tvalue['moddate'];
                var extension = basename.split(".").at(-1);
                var datapage = "";
				if ((page != -1) && (i == page -1)) {
					datapage = " autoclick ";
				}
                if (moddate != -1) {
                    date = new Date(moddate * 1000);
                    sdate =  "<b>" + date.getDate() + "/" + (date.getMonth() + 1) + "/" + date.getFullYear() + " </b>";
                } else {
                    sdate = "";
                }
                if (this._vidextensions.includes(extension)) {
                    content += "<a data-fancybox=\"gallery\" " + datapage + "loading=\"lazy\" href=\"" + urlfilename + "\"  data-caption=\"" +  sdate + tvalue['comment'] + "\">" +
                                "<span class=\"parent\"><img class=\"image2\" width=\"128\"  loading=\"lazy\" id=\"" + sid + "\" src=\"" + urlshortfilename + " \"/><i class=\"fa-solid fa-video image1\"></i></span></a>";
                } else {
                    content += "<a data-fancybox=\"gallery\"  " + datapage +  "href=\"" + urlfilename + "\"  data-caption=\"" +  sdate + tvalue['comment'] + "\"><img  loading=\"lazy\" id=\"" + sid + "\" src=\"" + urlshortfilename + "\" /></a>";
                }
                divid.append(content);
            }
        }
    };
    return this;
}

function forcefullscreen($) {
  $(document).ready(function() {
		document.querySelectorAll('a[data-fancybox]').forEach(function(link) {
		link.addEventListener('click', function() {
		  if (/mobi|android|webos|iphone|ipad|ipod|blackberry|opera mini/i.test(navigator.userAgent)) {
			setTimeout(function() {
				$('button[data-fullscreen-action]').trigger('click');
			}, 100);
		  }
		});
	  });
	// foreceFullscreen will fail
	$("a[autoclick]").each(function(index, value) {
		$(value)[0].click();
	});
  });
}


function jimages_getimages($, id, listfiles, options)
{
  var thmb = new imagesviewer($, id, listfiles);
  thmb.show($, 0, 1500, options);
  if (options['fullscreen']?? false) {
    forcefullscreen($, options);
  }
  
}



function initfancybox($, options = [])
{
  Fancybox.bind("[data-fancybox]", {
    Zoom: {
      enabled: true, // Enable zoom functionality
    },
    theme: "auto",
    contentClick: "iterateZoom",
    Carousel: {
      Autoplay: {
        autoStart: false,
        timeout: 3000,
      },
    Toolbar: {
        display: {
          left: [],
          middle: [],
          right: ['autoplay', 'fullscreen', 'zoomIn', 'zoomOut', 'thumbs', 'close'],
        },
    },
   }
  });
}


