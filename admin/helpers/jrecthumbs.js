
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}




function recthumbretriever($, sid, id, urlroot, json) {
    this._sid = sid;
	this._id = id;
    this._json = json;
	this._isstarted = false;
	this._urlroot = urlroot;
	this._forced = false;
	this._keep = false;
	
	this.getthumb = function(directory, index, length, imgname, params) {	
		url = this._urlroot + "/administrator/index.php?option=com_jgallery&view=jgallery&tmpl=component&XDEBUG_SESSION_START=test&layout=thumb&directory64="
				 + directory +"&image64=" + btoa(imgname) +"&force=" + Number(this._forced);
        $.map(params, function(value, key) {
                url += "&" + key + "=" + value;
            }        
        );
        var context = {id : this._id, index: index, length: length, directory : decode_utf8(atob(directory))};
		$.ajax({
			url: url,
			type: "POST",
			dataType: "json",
			async: 'false',
			context: context,
			success: function(tvalue) {
				txt = context.directory + " " + context.index + "/" + context.length + " " + tvalue[0] + "=>" + tvalue[1] + "<br/>";
				$("#jgallerylog"+ context.id).html(txt);
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
		});
	};
    
    this.getthumbs = function($, dir, params) {
        var url = this._urlroot + "/administrator/index.php?option=com_jgallery&view=jgallery&tmpl=component&layout=json&directory64=" + dir;
        $.ajax({
            url:  url,
            type: "POST",
            dataType: "json",
            context: this,
            success: function(rdata) {
                $.each(rdata, $.proxy(function(index, value) {
                                        this.getthumb(dir, index, rdata.length, value.basename, params);
                                        }, this));
                 this._tabselectdirectories.check(dir, false);
                 //$("#jgallerylog"+this._id).html("All thumbs retrieved for "+ dir);
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
    };
	this.checkall = function(checked) {
		this._tabselectdirectories.checkall(checked);	
	};
	
	this.setforced = function(forced) {
		this._forced = forced;	
	};

	this.onchange = function($, listvalues) {
		this._listdirectories = listvalues;
        console.log(listvalues);
	}
	this.isstarted = function() {
		return this._isstarted;
	}
	this.startthumbs = function(params) {
		this._isstarted = true;
		console.log("started");
        console.log(params);
		$.each(this._listdirectories, 
				$.proxy(function(index, value) {
					if (this._isstarted) {
						this.getthumbs($, value, params);
                        this._tabselectdirectories.check(index, false);
					}
				}, this)
		);
		this._isstarted = false;
	};
	this.stopthumbs = function() {
		this._isstarted= false;
		console.log("stopped");
	};

	this.show = function($) {
		var html = '<table><tr><td><button type="button" id="thumbs' + id + '" class="btn btn-primary">Thumbs</button></td>'
        	+ '<td><label><input type="checkbox" name="checkall" value="checkall">checkall</label></td>'	
			+ '<td><label><input type="checkbox" name="force" value="force">force</label></td>'
			+ '</tr>'
            + '<tr><td><label>small_width<input type="text" name="small_width" id="small_width' + id +'" value="256" size="5"></label></td>'	
            + '<td><label>large_width<input type="text" name="small_width" id="large_width' + id +'" value="1024" size="5"></label></td>'
            +'</tr></table>';
		var onchange = this.onchange.bind(this);				
		this._tabselectdirectories = initmulticheckboxjson($, '#jimages' + this._id , this._json,  onchange, [ this._sidg]);
		$("#jgallery"+this._id).html(html);
		$('#thumbs'+this._id).data('recthumbretriever', this);
		$('#thumbs'+this._id).click($.proxy(function() {
			var dataretriever = $('#thumbs'+this._id).data('recthumbretriever');
			if (dataretriever.isstarted()) {
				dataretriever.stopthumbs();
                dataretriever._isstarted = false;
			} else {
				dataretriever.startthumbs({'small_width': $('#small_width' + this._id).val(),
                                           'large_width': $('#large_width' + this._id).val()}
                );
			}
		}, this));
		$('#jgalleryimage'+this._id).data('recthumbretriever', this);
		$("#jgalleryimage"+id).change(function() {
			var lvalues = $('#jgalleryimage'+id+' option:selected')
                .toArray().map(item => item.value);
			$(this).data('recthumbretriever').change(lvalues);
		});
		$('input[type="checkbox"][name="checkall"]').data('recthumbretriever', this);
		$('input[type="checkbox"][name="checkall"]').change(function() {
			 $(this).data('recthumbretriever').checkall(this.checked);
		 });
		$('input[type="checkbox"][name="force"]').data('recthumbretriever', this);
		$('input[type="checkbox"][name="force"]').change(function() {
			 $(this).data('recthumbretriever').setforced(this.checked);
		 });
	}
	this.show($);
	return this;
}



function jrecthumbs_getdirectories($, sid, id, urlroot, json)
{
	var thmb = new recthumbretriever($, sid, id, urlroot, json);
	thmb.show($);	
}

