function fillgallery($, value, params)
{
    id = params[0];
    rootdir = params[1];
    url =  rootdir + "index.php?option=com_jogallery&view=jogallery&tmpl=component&directory64=" + value;
    $.ajax({
        url: url,
        type: "POST",
        success: function (rdata) {
                $(id).html(rdata);
        },
        error: function (xhr, status, text) {
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

