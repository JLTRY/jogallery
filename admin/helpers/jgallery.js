function fillgallery($, id)
{
	$("#dirselect"+id).change(function() {
		$.ajax({
			url: "/index.php?option=com_jgallery&view=jgallery&tmpl=component&directory64="
				 + this.value,
			type: "POST",
			success: function(rdata) {
					$("#jgallery"+id).html(rdata);					
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
	});
}

