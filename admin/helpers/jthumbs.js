

function thumbretriever($, id, urlroot, directory, values) {
	this._values = values;
	this._id = id;
	this._directory = directory;
	this._isstarted = false;
	this._urlroot = urlroot;
	this._forced = false;
	this._keep = false;
	
	this.getthumb = function(imgname) {	
		url = this._urlroot + "/administrator/index.php?option=com_jgallery&view=jgallery&tmpl=component&XDEBUG_SESSION_START=test&layout=thumb&directory64="
				 + this._directory +"&image64=" + btoa(imgname) +"&force=" + Number(this._forced);
		console.log(url);
		$.ajax({
			url: url,
			type: "POST",
			dataType: "json",
			async: 'false',
			context: this,
			success: function(tvalue) {
				//console.log(this);
				txt = "";
				$.each(tvalue[2], function(index, item) {
					txt += item +"<br/>";
				});
				$("#jgallerylog"+this._id).html(txt);
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
	this.startthumbs = function() {
		this._isstarted = true;
		console.log("started");
		$.each(this._listimages, 
				$.proxy(function(index, value) {
					if (this._isstarted) {
						this.getthumb(value);
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
					txt += item +"<br/>";
				});
				$("#jgallerylog"+this._id).html(txt);
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
			+ '</tr></table>'	;
		var onchange = this.onchange.bind(this);				
		this._tabselectimages =  tabselectimages($, "#jimages" + this._id , this._values, {'checked':true, 'name': true}, onchange, []);
		$("#jgallery"+this._id).html(html);
        //$("#toolbar").html(html);
		$('#thumbs'+this._id).data('thumbretriever', this);
		$('#delete'+this._id).data('delete', this);
		$( '#thumbs'+this._id).click(function() {
			var dataretriever = $(this).data('thumbretriever');
			if (dataretriever.isstarted()) {
				dataretriever.stopthumbs();
			} else {
				dataretriever.startthumbs();
			}
		});
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

function jthumbs_ajax($, id, urlroot, value) {
	var dir = value;
	var url = urlroot + "/administrator/index.php?option=com_jgallery&view=jgallery&tmpl=component&layout=json&directory64="
			 + value;
	console.log(url);
	$.ajax({
		url:  url,
		type: "POST",
		dataType: "json",
		success: function(rdata) {
			var thmb = new thumbretriever($, id, urlroot, dir, rdata);
			thmb.show($);				
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


function jthumbs_getimages($, sid, id, urlroot, value)
{
	if (value) {
		jthumbs_ajax($, id, urlroot, value);
	}
	else {
		$("#"+sid).change(function() {
			jthumbs_ajax($, id, urlroot, this.value);
		});
	}
}

