

function radiobox($, idp, values, callback, params) {
	this._idp = idp;
	this._values = values;
	this._callback = callback;
	this._params = params;
	this.callback = function(value)
	{
		this._callback($, value, this._params);
	}

	this.init = function() {
		$(this._idp).html('');
		var btclass = (false)?"btn btn-sm btn-info":"btn btn-sm btn-warning";
		var text = "<fieldset id=\"findir\"" +  this._idp + "\">" +
					'<table><tr>';
		var nbcar = 0;
		var maxcar = 80;
		$.each(this._values,function(index, item) {
			text += '<td>' + 
						'<input name="' + this._idp +
						        '" type="radio" '+
								" id=\""+ item.name + '"' +
								'style="dsplay:none;" value="'+ item.value + '" />' +
						"<label class=\"" + btclass + "\"" +
									" for=\""+  item.name  + "\" >" +
						item.relative +
						"</label>" +
					'</td>';
			nbcar += item.relative.length + 1;
			if (nbcar > maxcar) {
				text = text + '</tr><tr>';
				nbcar = 0;
			}
		});
		text += "</tr></table></fieldset>";
		$(this._idp).html(text);
		$("#findir" + this._idp).data('radiobox', this);
		var that = this;
		var lastval = null;
		$(this._idp).find("input").each(function(){
			$(this).change(function() {
                var radiobox = that;                
                var label = $('label[for="'+$(this).attr('value')+'"]');
                if (label.length <= 0) {
                    var parentElem = $(this).parent(),
                        parentTagName = parentElem.get(0).tagName.toLowerCase();
                    if (parentTagName == "label") {
                        label = parentElem;
                    }
                }
                if ($(this).checked) {
                    label.attr('class', 'btn btn-sm btn-info');
                    //$(this).prop("checked", false );
                }
                else {
                    label.attr('class', 'btn btn-sm btn-warning');
                    //$(this).prop("checked", true);
                }								
				radiobox.callback($(this).val());
			});
			lastval = $(this).val();
		});
		return lastval;
	};
	lastval = this.init();
	if (lastval != null) {
		this.callback(lastval);
	}
	return this;
}

function initradiobox($, idp, values,  callback, params) {
	$(idp).data('radiobox', new radiobox($, idp,  values, callback, params));
}