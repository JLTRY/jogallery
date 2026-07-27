function fillgallery($, value, params)
{
    id = params["sidg"];
    rootdir = params['uriroot'];
    media = params['media'];
    lightbox = params['lightbox'];
    url =  rootdir + "index.php?option=com_jogallery&view=jogallery&tmpl=component&directory64=" + value +"&media=" + media + "&lightbox=" + lightbox;
    url += "&fullscreen=" + (params['fullscreen'] ?? "0");
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

