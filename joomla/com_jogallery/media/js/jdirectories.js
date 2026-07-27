

function directoriesdisplay($, sid, icon, directories, options=[])
{
    this._directories = directories;
    this._sid = sid;
    this._icon = icon;
	this._options = options;

    this.show = function ($) {
        var content = '<table><tr>';
        var i = 0;
        var maxitem = 7;
        var window_width = $(window).width();
        if ( window_width <= 1280 ) {
            maxitem = 3;
        }
        if ( window_width <= 480 ) {
            maxitem = 2;
        }
        $.each(this._directories, $.proxy(function (index, value) {
                var urlfilename =  value.url;
                var dirname = value.name;
                var nbcar = dirname.len;
				console.log("show")
				console.log(this._options);
				if (this._options)
				{
					console.log("show:len" + this._options);
					urlfilename += "&media=" + (this._options['media'] ?? "ALL") + "&lightbox=" + (this._options['lightbox'] ?? "fancybox");
					urlfilename += "&fullscreen=" + (this._options['fullscreen'] ?? "0");
				}
                content += '<td><a href="' + urlfilename + '" data-placement="left" data-bs-toggle="tooltip" title="' + dirname + '">';
                content +=  '<img src="' + this._icon + '">' +
                                '<input style="border: 0; text-overflow:ellipsis;" size="15" type="text" name="' + dirname + '" value=" ' + dirname + '" readonly >';
                content +=  '</a></td>';
                i = i + 1;
            if (i >= maxitem) {
                content += '</tr><tr>';
                i = 0;
            }
        }, this));
        content += "</tr></table>";
        $('#' + this._sid).append(content);
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
         $(".tooltip").tooltip({
                position: {
                    my: "left center",
                    at: "right center"
                }
            });
    };
    return this;
}

function jdirectories_show($, id, icon, listdirectories, options=[])
{
    var tdir = new directoriesdisplay($, id, icon, listdirectories, options);
	console.log("jdirectories_show");
	console.log(listdirectories);
	console.log(options);
    tdir.show($);
}
