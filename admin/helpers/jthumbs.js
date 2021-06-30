

function thumbretriever($, id, urlroot, directory, values) {
	this._values = values;
	this._id = id;
	this._directory = directory;
	this._isstarted = false;
	this._urlroot = urlroot;
	this._forced = false;
	
	this.getthumb = function(img) {	
		$.ajax({
			url: this._urlroot + "/administrator/index.php?option=com_jgallery&view=jgallery&tmpl=component&layout=thumb&directory64="
				 + this._directory +"&image64=" + btoa(img) +"&force=" + Number(this._forced),
			type: "POST",
			dataType: "json",
			async: 'false',
			context: this,
			success: function(tvalue) {
				//console.log(this);
				$("#jgallerylog"+this._id).html(tvalue[2]);
				this._tabselect.check(tvalue[0], false);
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
		this._tabselect.checkall(checked);	
	};
	
	this.setforced = function(forced) {
		this._forced = forced;	
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
	this.show = function($) {
		var html = '<button type="button" id="thumbs' + id + '" class="btn btn-primary">Thumbs</button>'
			+ '<label><input type="checkbox" name="checkall" value="checkall">checkall</label>'	
			+ '<label><input type="checkbox" name="force" value="force">force</label>'	;
		var onchange = this.onchange.bind(this);				
		this._tabselect =  tabselect($, "#jimages1" , this._values, onchange, []);
		$("#jgallery"+this._id).html(html);
		$('#thumbs'+this._id).data('thumbretriever', this);
		$( '#thumbs'+this._id).click(function() {
			var dataretriever = $(this).data('thumbretriever');
			if (dataretriever.isstarted()) {
				dataretriever.stopthumbs();
			} else {
				dataretriever.startthumbs();
			}
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
	}
	this.show($);
	return this;
}		

function jthumbs_getimages($, id, urlroot)
{
	$("#dirselect"+id).change(function() {
		var dir = this.value;
		$.ajax({
			url:  urlroot + "/administrator/index.php?option=com_jgallery&view=jgallery&tmpl=component&layout=json&directory64="
				 + this.value,
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
	});
}

