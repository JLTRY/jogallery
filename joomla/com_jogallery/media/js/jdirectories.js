

function directoriesdisplay($, sid, icon, directories) {
	this._directories = directories;
	this._sid = sid;
	this._icon = icon;
	
	this.show = function($) {
		var content = '<table><tr>';
		var i = 0;
		var maxitem = 7;
		var window_width = $(window).width();
		if( window_width <= 1280 ) {
			maxitem = 3;
		}
		if( window_width <= 480 ) {
			maxitem = 2;
		}	
		$.each(this._directories, $.proxy(function(index, value)  {
				var urlfilename =  value.url;
				var dirname = value.name;
				var nbcar = dirname.len;
				content += '<td><a href="' + urlfilename + '" data-placement="left" data-bs-toggle="tooltip" title="' + dirname +'">';
				content +=	'<img src="' + this._icon +'">'+
								'<input style="border: 0; text-overflow:ellipsis;" size="15" type="text" name="' + dirname +'" value=" ' + dirname +'" readonly >';
				content +=	'</a></td>';
				i = i + 1;
				if (i >= maxitem){
					content += '</tr><tr>';
					i = 0;
				}
			}, this)
		);
		content += "</tr></table>";
		$('#' + this._sid).append(content);
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl);
		}); 
		 $( ".tooltip" ).tooltip({
			position: {
				my: "left center",
				at: "right center"
			}
		});
	};
	return this;
}		

function jdirectories_show($, id, icon, listdirectories)
{
	var tdir = new directoriesdisplay($, id, icon, listdirectories);
	tdir.show($);
}
