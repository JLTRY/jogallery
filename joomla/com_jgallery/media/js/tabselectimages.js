
function _tabselectimages($, idp, values, options, callback, params) {
	this._idp = idp;
	this._values = values;
	this._callback = callback;
	this._params = params;
	this._options = options;
	this.callback = function()
	{
		var listselected = [];
		$(this._idp).find("[class='tabselectimages']").each(function() {
				if ($(this).prop('checked')){
					listselected.push($(this).attr('for'));
				}
		});
		this._callback($, listselected, this._params);
	}
	
	this.checkall = function(checked) {
		$(this._idp).find("[class='tabselectimages']").each(function() {
					$(this).prop('checked', checked);
			}	
		);
		this.callback();
	}
	this.check = function(value, checked) {
		$(this._idp).find('[class="tabselectimages"]').each(function() {
			if ($(this).attr("for") == value) {
				$(this).prop('checked', checked);
			}
		});
	};	
	this.init = function($) {
		$(this._idp).html('');
		var text = '<table>';
		var that = this;
		var vid = 0;
		window.lazyLoadOptions = {
			elements_selector: ".lazy"
		};
		$.each(this._values,function(index, tvalue) {
			var simage = tvalue['urlshortfilename'];
			var limage = tvalue['urlfilename'];
			var name = tvalue['filename'];
			var basename = tvalue['basename'];
			var comment = tvalue['comment'];
			var moddate = tvalue['moddate'];
			var checked = 0;
			var btclass = (checked)?"btn btn-sm btn-info":"btn btn-sm btn-light";
			var checkedattr = (checked)?"checked":"";
			var id = name;
			text += "<tr>";
			if (that._options['checked']) {
				text += "<td ><input style=\"float:left;font-size:50%;\" class=\"tabselectimages\" type=\"checkbox\" " +  checkedattr +" id=\"" + name  +
										"\" value=\""+index + "\" "
										+ " for=\"" + basename + "\""
										+ " idp=\"" + that._idp + "\""
										+ checkedattr + " >"
						+ "</td>";
			}
			if (that._options['name']) {
				text += "<td>" 
					+ name + 
					"</td>";
			}
			if (that._options['moddate']) {
				text += "<td>" 
					+ new Date(moddate * 1000).toISOString() + 
					"</td>";
			}
			text += '<td>';
			if (basename.includes('mp4'))
			{
				//text += '<video class="lazy" controls="controls" width="auto" height="240"><source src="'+ limage + basename + '" type="video/mp4"> </video>';
				text += '<a href="'+ limage + '">'+ basename +'</a>';
				vid = 1;
			}
			else
			{
				text += '<a class="fancybox-thumbs" data-fancybox="'+ idp +'" href="' + limage +'">'
					+'    <img src="' + simage +'" id="' + basename + '" ' + 'alt="">'
					+'</a>';
			}
			text += '</td>';
			if (that._options['comments']) {
				text += '<td><input type="textbox" size="80" name="comments['+ name +']" value="'+comment +'"></td>';
			}
			text = text + '</tr>';
		});
		text += "</table>";
		$(this._idp).html(text);
		$("input[class='tabselectimages']").data('tabselectimages', this);
		$("input[class='tabselectimages']").change(function() {
			$(this).data('tabselectimages').callback();
		});
		
	};
	this.init($);
	return this;
}

function tabselectimages($, idp, values, options, callback, params) {
	var tabselectimages = new _tabselectimages($, idp, values, options, callback, params);
	$(idp).data('tabselectimages', tabselectimages);
	return tabselectimages;
}