

function radiobox($, idp, values, callback, params)
{
    this._idp = idp;
    this._values = values;
    this._callback = callback;
    this._params = params;
	this._curid = null;
    this.callback = function (value) {
        this._callback($, value, this._params);
    }
    this.setchecked = function(id) {
		if (this._curid) {
			$('label[for="' + this._curid + '"]').attr('class', 'btn btn-sm btn-warning');
		}
		$('label[for="' + id + '"]').attr('class', 'btn btn-sm btn-info');
		this._curid = id;
	};

    this.init = function () {
        $(this._idp).html('');
        var btclass = (false) ? "btn btn-sm btn-info" : "btn btn-sm btn-warning";
        var text = "<fieldset id=\"findir\"" +  this._idp + "\">" +
                    '<table><tr>';
        var nbcar = 0;
        var maxcar = 80;
        $.each(this._values,function (index, item) {
            text += '<td>' +
                        '<input name="' + this._idp +
								' class=\"radiobox\" ' +
                                '" type="radio" ' +
                                " id=\"" + item.name + '"' +
                                'style="display:none;" '+
								 ' value="' + item.value + '" />' +
                        "<label class=\"" + btclass + "\"" +
                                    " for=\"" +  item.name  + "\" >" +
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
		var lastid = null;
        $(this._idp).find("input").each(function () {
			this.radiobox = that;
            $(this).change(function () {
                var radiobox = $(this).data('radiobox');
                radiobox.callback($(this).val());
				radiobox.setchecked($(this).attr('id'), true);
            });
            lastval = $(this).val();
			lastid = $(this).attr('id');
        });
		$(this._idp).find("input").each(function (){ 
			$(this).data('radiobox', that);
		});
		if (lastid) {
			this.setchecked(lastid);
			this.callback(lastval);
		}
    };
    this.init();
    return this;
}

function initradiobox($, idp, values,  callback, params)
{
    $(idp).data('radiobox', new radiobox($, idp,  values, callback, params));
}