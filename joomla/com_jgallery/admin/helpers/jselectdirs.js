function jselectdirs_ajax($, id, urlroot, value) {
	var dir = value;
	var lastchar = urlroot.substr(urlroot.length - 1); ;
	var url = urlroot + ((lastchar == '/')? '': '/' )+ "index.php?option=com_jgallery&view=jgallery&tmpl=component&layout=json&directory64="
			 + value;
	console.log(url);
	$.ajax({
		url:  url,
		type: "POST",
		dataType: "json",
		success: function(rdata) {
			var thmb = new imagesretriever($, id, urlroot, dir, rdata);
			thmb.show($, 0, 1500);
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


function jselectdirs_getimages($, sid, id, urlroot, value)
{
	$("#"+sid).change(function() {
		jselectdirs_ajax($, id, urlroot, this.value);
	});
}

