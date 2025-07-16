import { psw_images_getimages, init_psw } from  "./psw_images.js";
function jselectdirs_ajax($, id, urlroot, value, media, lightbox) {
	var dir = value;
	var lastchar = urlroot.substr(urlroot.length - 1); ;
	var url = urlroot + ((lastchar == '/')? '': '/' )+ "index.php?option=com_jgallery&view=jgallery&tmpl=component&layout=json&directory64="
			 + value + "&media=" + media;
	console.log(url);
	$.ajax({
		url:  url,
		type: "POST",
		dataType: "json",
		success: function(rdata) {
			if (lightbox == "fancybox") {
				var thmb = new imagesviewer($, id, rdata);
				thmb.show($, 0, 1500);
			} else {
				psw_images_getimages($, id, rdata);
				init_psw($, id);
			}
		},
		error: function(xhr, status, text) {
			var response = $.parseJSON(xhr.responseText);
			console.log('Failure!');
			if (response) {
				console.log(response['data']['error']);
			} else {
				// This would mean an invalid response from the server - maybe the site went down or whatever...
			}
		}
	});
}


function jselectdirs_getimages($, sid, id, urlroot, media, lightbox)
{
	$("#"+sid).change(function() {
		jselectdirs_ajax($, id, urlroot, this.value, media, lightbox);
	});
}

export { jselectdirs_getimages };