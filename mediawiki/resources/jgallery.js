function fillgallery1($, value, params)
{
	initfancybox($);
	id = params[0];
	rootdir = params[1];
	url =  rootdir + "index.php?option=com_jgallery&view=jgallery&layout=json&tmpl=component&directory64=" + value;
	$.ajax({
		url: url,
		type: "POST",
		success: function(rdata) {
			jimages_getimages($, id, rdata);
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


function fillgallery(jQuery, directory){
    fillgallery1(jQuery, btoa(directory), ["jgallery1", "http://www.jltryoen.fr/"]);
}
