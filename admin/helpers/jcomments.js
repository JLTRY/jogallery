

function commentsetter($, id, urlroot, directory, values) {
	this._values = values;
	this._id = id;
	this._directory = directory;
	this._isstarted = false;
	this._urlroot = urlroot;
	this._forced = false;
	
	this.onchange = function(select) {
	};
	
	this.save = function(img) {	
		var url = this._urlroot + "/index.php?option=com_jgallery&task=jgallery.savecomments&XDEBUG_SESSION_START=test&tmpl=component&directory64="
				 + this._directory;
		console.log(url);
		var values = {};
		$("#jimages"+this._id +" input[type=textbox]").each(function() {
			values[$(this).attr('name')] = $(this).val();
		});		
		$.ajax({
			url: url,
			type: "POST",
			dataType: "json",
			data: values,
			async: 'false',
			context: this,
			success: function(value) {
				//console.log(this);
				$("#jgallerylog"+this._id).html(value);
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
		var html = '<button type="button" id="comments' + id + '" class="btn btn-primary">Sauver</button>';
		var onchange = this.onchange.bind(this);				
		this._tabselectimages =  tabselectimages($, "#jimages"+ this._id , this._values, {'comments':true, 'checked':true}, onchange, []);
		$("#jgallery"+this._id).html(html);
		$('#comments'+this._id).data('commentsetter', this);
		$( '#comments'+this._id).click(function() {
			$(this).data('commentsetter').save();
		});
		$('#jgalleryimage'+this._id).data('commentsetter', this);
	}
	this.show($);
	return this;
}		

function jcomments_ajax(id, urlroot, value)
{
	var dir = value;
	var url = urlroot + "/index.php?option=com_jgallery&view=jgallery&tmpl=component&XDEBUG_SESSION_START=test&layout=json&directory64="
			 + value;
	console.log(url);
	$.ajax({
		url:  url,
		type: "POST",
		dataType: "json",
		success: function(rdata) {
			var cmtset = new commentsetter($, id, urlroot, dir, rdata);
			cmtset.show($);				
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

function jcomments_getimages($, sid, id, urlroot, value)
{
	if (value) {
		jcomments_ajax(id, urlroot, value);
	}
	else {
		$("#"+sid).change(function() {
			jcomments_ajax(id, urlroot, this.value);			
		});
	}
}

