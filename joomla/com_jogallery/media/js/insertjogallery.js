
function insertJoGallery($, editorName) {
	let editor = parent.Joomla.editors.instances[editorName];
	if (editor) {
		var adminform = document.getElementById("adminForm");
		var Data = new FormData(adminform);
		var imgList = (Data.getAll("jform[select]")!="1")?"": " img=\""+  Data.getAll("jform[insert]") + "\"";
		var dateList = (Data.getAll("jform[select]")!="2")?"":  
							" start=\""+  Data.getAll("jform[startdate]") + " " + Data.getAll("jform[startdate_time]") +"\"" +
							" end=\""+  Data.getAll("jform[enddate]") + " " + Data.getAll("jform[enddate_time]")+"\"" ;
		editor.replaceSelection("{jgallery directory=\"" + Data.getAll("jform[folders]") + "\"" + imgList + dateList + "}");
		$(".btn-close", parent.document).click();
	}
}




//sid is the folder selector
function insertjogallery($, sid, simgid, urlroot, directory = ".") {
	this._sid = sid;
	this._simgid = simgid;
	this._directory = directory;
	this._curdir = null;
	this.selectavailable = function($) {
		var value = $('input[name="jform[select]"]:checked', '#adminForm').val();
		return ( value == "1" || value == "2");
	};
	this.updatemoddate = function() {
		listmoddate = this._tabselectimages.getmoddate();
		var startdate  = moment(listmoddate[0] * 1000).format("YYYY-MM-DD");
		var starttime  = moment(listmoddate[0] * 1000).format("HH:mm:SS");
		$('#jform_startdate').val(startdate);
		$('#jform_startdate').attr('data-alt-value', startdate);
		$('#jform_startdate_time').val(starttime);
		var enddate  = moment(listmoddate[1] * 1000).format("YYYY-MM-DD");
		var endtime  = moment(listmoddate[0] * 1000).format("HH:mm:SS");
		$('#jform_enddate').val(enddate);
		$('#jform_enddate').attr('data-alt-value', enddate);
		$('#jform_enddate_time').val(endtime);
	};
	this.updateimages = function($, value = this._curdir) {
		if (this.selectavailable($) && value && (this._curtabdir != value)) {
			var url = urlroot + "/administrator/index.php?option=com_jogallery&view=jogallery&tmpl=component&layout=json&directory64="
					 + btoa(encode_utf8(value)) + "&XDEBUG_SESSION_START=test";
			console.log(url);
			$.ajax({
				url:  url,
				type: "POST",
				context: this,
				dataType: "json",
				success: function(rdata) {
					console.log("ok thumbs");
					this._tabselectimages =  tabselectimages($, "#" + this._simgid , rdata, {'checked':true, 'name': true, 'moddate': true}, this.onchange, []);
					this.updatemoddate();
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
			this._curtabdir = value;
		}
		this._curdir = value;
		if (!this.selectavailable($)) {
			$('#' + this._simgid).hide();
		} else {
			$('#' + this._simgid).show();
		}
	};
	this.onchange = function($, listvalues) {
		this._listimages = listvalues;
		$("#jform_insert").val(listvalues.join(","));
	};
	
	this.init = function($) {
		$('#'+ this._sid).data('insertjogallery', this);
		$('input[name="jform[select]"]').data('insertjogallery', this);
		$('#'+ this._sid).change(function() {
			var that = $(this).data('insertjogallery');
			that.updateimages($, this.value);
		});
		$('input[name="jform[select]"]').change(function() {
			$(this).data('insertjogallery').updateimages($);
		});
	};
	this.init($);
	return this;
}

