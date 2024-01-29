

function imagesretriever($, id, urlroot, dir, values) {
	this._values = values;
	this._id = id;
	this._urlroot = urlroot;
	this._directory = dir;
	this._modulo = 50;
	this._start = 0;

	this.getthumbs = function(index, nb) {
		var url = this._urlroot + "/index.php?option=com_jgallery&view=jgallery&tmpl=component&layout=ping&directory64="
				 + this._directory;
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
			var content = ""
			if (i < this._values.length)
			{
				var value = this._values[i];
				sid = value['filename'];
				urlfilename = value['urlfilename'];
				urlshortfilename = value['urlshortfilename'];
				moddate = value['moddate'];
				if (moddate != -1) {
					date = new Date(moddate* 1000);
					sdate =  "<b>" + date.getDate()+ "/"+(date.getMonth()+1)+ "/"+date.getFullYear() + " </b>";
				} else {
					sdate = "";
				}
				comment = sdate + value['comment'];
				content = "<a data-fancybox=\"gallery\"  href=\"" + urlfilename +"\"  data-caption=\"" + comment + "\"><img id=\""+ sid + "\" src=\"" + urlshortfilename +"\" /></a>";
				divid.append(content);
			}
		}
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
							},								},
					template : {  // Close button icon  
					closeButton:      '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" tabindex="-1"><path d="M20 20L4 4m16 0L4 20"/></svg>', 
					// Loading indicator icon  
					spinner:      '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="25 25 50 50" tabindex="-1"><circle cx="50" cy="50" r="20"/></svg>',  
					// Main container element 
					main: '<div  class="fancybox__container"  role="dialog"  aria-modal="true"  aria-hidden="true"  aria-label="{{MODAL}}"  tabindex="-1">  <div class="fancybox__backdrop"></div><input type="text" id="comment" name="lname">  <div class="fancybox__carousel"></div></div>'
					}
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