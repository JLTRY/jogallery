function encode_utf8(s) {
  return unescape(encodeURIComponent(s));
}

function decode_utf8(s) {
  return decodeURIComponent(escape(s));
}

function thumbretriever($, id, urlroot) {
	this._sid = "jogalleryselect" + id;
	this._simgid = "jimages" + id;
	this._id = id;
	this._isstarted = false;
	this._urlroot = urlroot;
	this._forced = false;
	this._keep = false;
	this._listimages = [];

	this.updateimages = function($, value) {
		var url = this._urlroot + "/administrator/index.php?option=com_jogallery&view=jogallery&tmpl=component&layout=json&directory64="
				 + value;
		console.log(url);
		$.ajax({
			url:  url,
			type: "POST",
			context: this,
			dataType: "json",
			success: function(rdata) {
				console.log("ok thumbs");
				var onchange = this.onchange.bind(this);
				this._tabselectimages =  tabselectimages($, "#" + this._simgid , rdata, {'checked':true, 'name': true, 'moddate': true}, onchange, []);
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
		this._directory = value;
	};
	
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
					var txt = this.index.toString().padStart(4, ' ') + "/" + this._listimages.length + " " + decode_utf8(atob(this._directory)) + " " + tvalue[0] + "=>" + tvalue[1] + ":" + tvalue[2][0] + "<br/>";
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
		} else {
			$('#thumbs'+this._id).blur(); 
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

	this.init = function($) {
		$("#"+ this._sid).data('thumbretriever', this);
		$("#"+ this._sid).change(function() {
			$(this).data('thumbretriever').updateimages($, this.value);
		});
		var onchange = this.onchange.bind(this);
		$('#thumbs'+this._id).data('thumbretriever', this);
		$('#thumbs'+this._id).click(function() {
			var dataretriever = $(this).data('thumbretriever');
			if (dataretriever.isstarted()) {
				dataretriever.stopthumbs();
			} else {
				dataretriever.startthumbs({'small_width': $('#small_width' + dataretriever._id).val(),
										   'large_width': $('#large_width' + dataretriever._id).val()}
				);
			}
		});
		$('#delete'+this._id).data('thumbretriever', this);
		$( '#delete'+this._id).click(function() {
			var dataretriever = $(this).data('thumbretriever');
			dataretriever.deleteselection();
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
	this.init($);
	return this;
}





