
function _tabselect($, idp, values, callback, params) {
	this._idp = idp;
	this._values = values;
	this._callback = callback;
	this._params = params;
	this.callback = function()
	{
		var listselected = [];
		$(this._idp).find("[class='tabselect']").each(function() {
				if ($(this).prop('checked')){
					listselected.push($(this).attr('for'));
				}
		});
		this._callback($, listselected, this._params);
	}
	
	this.checkall = function(checked) {
		$(this._idp).find("[class='tabselect']").each(function() {
					$(this).prop('checked', checked);
			}	
		);
		this.callback();
	}
	this.check = function(value, checked) {
		$(this._idp).find('[class="tabselect"]').each(function() {
			if ($(this).attr("for") == value) {
				$(this).prop('checked', checked);
			}
		});
	};	
	this.init = function($) {
		$(this._idp).html('');
		var text = '<table>';
		var that = this;
		$.each(this._values,function(index, tvalue) {
			var value = tvalue[0];
			var image = tvalue[1]
			var checked = 0;			
			var name = tvalue[0];
			var btclass = (checked)?"btn btn-sm btn-info":"btn btn-sm btn-light";
			var checkedattr = (checked)?"checked":"";
			var id = "checked_" + value;
			text = text + "<tr>"+
								"<td><input style=\"float:left;font-size:50%;\" class=\"tabselect\" type=\"checkbox\" " +  checkedattr +" id=\"" + id  +
										"\" value=\""+index + "\" "
										+ " for=\"" + value + "\""
										+ " idp=\"" + that._idp + "\""
										+ checkedattr + " >"
								+ "</td>"
								+ "<td><label class=\"" + btclass +"\" for=\"" +  id +"\" >" 
										+ name
								+ "</label></td>"
								+ '<td><img src="' + image +'" width="40"></img></td>';
			text = text + '</tr>';			
		});
		text += "</table>";
		$(this._idp).html(text);
		$("input[class='tabselect']").data('tabselect', this);
		$("input[class='tabselect']").change(function() {
			$(this).data('tabselect').callback();			
		});
	};
	this.init($);
	return this;
}

function tabselect($, idp, values, callback, params) {
	var tabselect = new _tabselect($, idp, values, callback, params);
	$(idp).data('tabselect', tabselect);
	return tabselect;
}