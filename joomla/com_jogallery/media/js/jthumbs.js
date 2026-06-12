function encode_utf8(s) {
  return unescape(encodeURIComponent(s));
}

function decode_utf8(s) {
  return decodeURIComponent(escape(s));
}

function thumbretriever($, id, urlroot, directory, values, callback) {
	this._listimages = values;
	this._id = id;
	this._directory = directory;
	this._isstarted = false;
	this._urlroot = urlroot;
	this._forced = false;
	this._keep = false;
	this._callback = callback;

	this.getthumb = function(index, params) {
		if (index < this._listimages.length) {
			var imgname = this._listimages[index];
			this.index = index;
			url = this._urlroot + "/administrator/index.php?option=com_jogallery&view=jogallery&tmpl=component&XDEBUG_SESSION_START=test&layout=thumb&directory64="
					 + this._directory +"&image64=" + btoa(encode_utf8(imgname)) +"&force=" + Number(this._forced);
			$.map(params, function(value, key) {
					url += "&" + key + "=" + value;
				}
			);
			console.log("thumbretriever" + url);
			$.ajax({
				url: url,
				type: "POST",
				dataType: "json",
				async: 'false',
				context: this,
				success: function(tvalue) {
					var txt = this. index.toString().padStart(4, ' ') + "/" + this._listimages.length + " " + decode_utf8(atob(this._directory)) + " " + tvalue[0] + "=>" + tvalue[1] + ":" + tvalue[2][0] + "<br/>";
					$("#jogallerylog"+this._id).html(txt);
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
			setTimeout($.proxy(this.getthumb, this), 500, index+1, params);
		}
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
		this._callback($, listvalues);
	}
	this.isstarted = function() {
		return this._isstarted;
	}
	this.startthumbs = function(params) {
		this._isstarted = true;
		console.log("started");
		console.log(params);
		this.getthumb(0, params);
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
		url = this._urlroot + "/administrator/index.php?option=com_jogallery&task=jogallery.delete&XDEBUG_SESSION_START=test&layout=thumb&directory64="
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
					$("#jogallerylog"+this._id).html(txt);
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
		var onchange = this.onchange.bind(this);
		this._tabselectimages =  tabselectimages($, "#jimages" + this._id , this._listimages, {'checked':true, 'name': true, 'moddate': true}, onchange, []);
		$('#thumbs'+this._id).data('thumbretriever', this);
		$('#delete'+this._id).data('thumbretriever', this);
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
			var dataretriever = $(this).data('thumbretriever');
			dataretriever.deleteselection();
		});
		$('#jogalleryimage'+this._id).data('thumbretriever', this);
		$("#jogalleryimage"+id).change(function() {
			var lvalues = $('#jogalleryimage'+id+' option:selected')
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

function jthumbs_ajax($, id, urlroot, value, callback) {
	var dir = value;
	var url = urlroot + "/administrator/index.php?option=com_jogallery&view=jogallery&tmpl=component&layout=json&directory64="
			 + value + "&XDEBUG_SESSION_START=test";
	console.log(url);
	$.ajax({
		url:  url,
		type: "POST",
		context: {"callback" : callback },
		dataType: "json",
		success: function(rdata) {
			console.log("ok thumbs");
			var thmb = new thumbretriever($, id, urlroot, dir, rdata, this.callback);
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


function jthumbs_getimages($, sid, id, urlroot, base64 = false, callback = null)
{
	$("#"+sid).change(function() {
		jthumbs_ajax($, id, urlroot, (base64)?btoa(encode_utf8(this.value)): this.value, callback);
	});
}

