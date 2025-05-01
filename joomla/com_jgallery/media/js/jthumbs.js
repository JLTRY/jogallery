function encode_utf8(s) {
  return unescape(encodeURIComponent(s));
}

function decode_utf8(s) {
  return decodeURIComponent(escape(s));
}

function thumbretriever($, id, urlroot, directory, values, menu) {
	this._values = values;
	this._id = id;
	this._directory = directory;
	this._isstarted = false;
	this._urlroot = urlroot;
	this._forced = false;
	this._keep = false;

	this.getthumb = function(imgname, params) {
		url = this._urlroot + "/administrator/index.php?option=com_jgallery&view=jgallery&tmpl=component&XDEBUG_SESSION_START=test&layout=thumb&directory64="
				 + this._directory +"&image64=" + btoa(encode_utf8(imgname)) +"&force=" + Number(this._forced);
		$.map(params, function(value, key) {
				url += "&" + key + "=" + value;
			}
		);
		console.log("thumbretriver" + url);
		$.ajax({
			url: url,
			type: "POST",
			dataType: "json",
			async: 'false',
			context: this,
			success: function(tvalue) {
				txt = decode_utf8(atob(this._directory)) + " " + tvalue[0] + "=>" + tvalue[1] + ":" + tvalue[2][0] + "<br/>";
				$("#jgallerylog"+this._id).html(txt);
				console.log('OK');
				var imge = $("img[id='"+ imgname + "']");
				imge.attr('src', imge.attr('src')+"?timestamp=" + new Date().getTime());
				this._tabselectimages.check(tvalue[0], false);
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
	this.checkall = function(checked) {
		this._tabselectimages.checkall(checked);
	};

	this.setforced = function(forced) {
		this._forced = forced;
	};
	this.setkeep = function(keep) {
		this._keep = keep;
	};
	this.onchange = function($, listvalues) {
		this._listimages = listvalues;
	}
	this.isstarted = function() {
		return this._isstarted;
	}
	this.startthumbs = function(params) {
		this._isstarted = true;
		console.log("started");
		console.log(params);
		$.each(this._listimages, 
				$.proxy(function(index, value) {
					if (this._isstarted) {
						this.getthumb(value, params);
					}
				}, this)
		);	   
		this._isstarted = false;
	};
	this.stopthumbs = function() {
		this._isstarted= false;
		console.log("stopped");
	};
	this.deleteselection = function() {
		var values = [];
		$.each(this._listimages, 
				$.proxy(function(index, value) {
					values.push(value);
				}, this)
		);
		url = this._urlroot + "/administrator/index.php?option=com_jgallery&task=jgallery.delete&XDEBUG_SESSION_START=test&layout=thumb&directory64="
				 + this._directory +"&keep=" + Number(this._keep);
		console.log(url);
		$.ajax({
			url: url,
			type: "POST",
			dataType: "json",
			data : { 'images' : values },
			async: 'false',
			context: this,
			success: function(tvalue) {
				console.log("success");
				txt = "";
				$.each(tvalue, function(index, item) {
					txt = item +"<br/>";
					$("#jgallerylog"+this._id).html(txt);
				});
				$.each(this._listimages, 
					$.proxy(function(index, value) {
						var imge = $("img[id='"+ value + "']");
						imge.closest('tr').remove();
					}, this)
				);
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
	this.show = function($) {
		var html = '<table><tr><td><button type="button" id="thumbs' + id + '" class="btn btn-primary">Thumbs</button></td>'
			+ '<td><label><input type="checkbox" name="checkall" value="checkall">checkall</label></td>'
			+ '<td><label><input type="checkbox" name="force" value="force">force</label></td>'
			+ '<td><button type="button" id="delete' + id + '" class="btn btn-primary">Delete</button></td>'
			+ '<td><label><input type="checkbox" name="keep" value="keep">keep</label></td>'
			+ '</tr>'
			+ '<tr><td><label>small_width<input type="text" name="small_width" id="small_width' + id +'" value="256" size="5"></label></td>'
			+ '<td><label>large_width<input type="text" name="small_width" id="large_width' + id +'" value="1024" size="5"></label></td>'
			+'</tr></table>';
		var onchange = this.onchange.bind(this);
		this._tabselectimages =  tabselectimages($, "#jimages" + this._id , this._values, {'checked':true, 'name': true, 'moddate': true}, onchange, []);
		if (menu) {
			$("#jmenuthumbs"+this._id).html(html);
		}
		$('#thumbs'+this._id).data('thumbretriever', this);
		$('#delete'+this._id).data('delete', this);
		$('#thumbs'+this._id).click($.proxy(function() {
			var dataretriever = $('#thumbs'+this._id).data('thumbretriever');
			if (dataretriever.isstarted()) {
				dataretriever.stopthumbs();
			} else {
				dataretriever.startthumbs({'small_width': $('#small_width' + this._id).val(),
										   'large_width': $('#large_width' + this._id).val()}
				);
			}
		}, this));
		$( '#delete'+this._id).click(function() {
			var dataretriever = $(this).data('delete');
			dataretriever.deleteselection();
		});
		$('#jgalleryimage'+this._id).data('thumbretriever', this);
		$("#jgalleryimage"+id).change(function() {
			var lvalues = $('#jgalleryimage'+id+' option:selected')
				.toArray().map(item => item.value);
			$(this).data('thumbretriever').change(lvalues);
		});
		$('input[type="checkbox"][name="checkall"]').data('thumbretriever', this);
		$('input[type="checkbox"][name="checkall"]').change(function() {
			 $(this).data('thumbretriever').checkall(this.checked);
		 });
		$('input[type="checkbox"][name="force"]').data('thumbretriever', this);
		$('input[type="checkbox"][name="force"]').change(function() {
			 $(this).data('thumbretriever').setforced(this.checked);
		 });
		$('input[type="checkbox"][name="keep"]').data('thumbretriever', this);
		$('input[type="checkbox"][name="keep"]').change(function() {
			 $(this).data('thumbretriever').setkeep(this.checked);
		 });
	}
	this.show($);
	return this;
}

function jthumbs_ajax($, id, urlroot, value, menu) {
	var dir = value;
	var url = urlroot + "/administrator/index.php?option=com_jgallery&view=jgallery&tmpl=component&layout=json&directory64="
			 + value + "&XDEBUG_SESSION_START=test";
	console.log(url);
	$.ajax({
		url:  url,
		type: "POST",
		dataType: "json",
		success: function(rdata) {
			console.log("ok thumbs");
			var thmb = new thumbretriever($, id, urlroot, dir, rdata, menu);
			thmb.show($);
		},
		error: function(xhr, status, text) {
			var response = $.parseJSON(xhr.responseText);
			console.log('Failure thumbs!');
			if (response) {
				console.log(response['data']['error']);
			} else {
				// This would mean an invalid response from the server - maybe the site went down or whatever...
			}
		}
	});
}


function jthumbs_getimages($, sid, id, urlroot, value, menu)
{
	$("#"+sid).change(function() {
		jthumbs_ajax($, id, urlroot, this.value, menu);
	});
}

