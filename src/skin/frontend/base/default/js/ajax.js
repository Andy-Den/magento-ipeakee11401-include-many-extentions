/**
 * fetch view from backend
 * @param id string
 * @param reloadurl string
 */
function encode_base64( what )
{
    var base64_encodetable = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    var result = "";
    var len = what.length;
    var x, y;
    var ptr = 0;

    while( len-- > 0 )
    {
        x = what.charCodeAt( ptr++ );
        result += base64_encodetable.charAt( ( x >> 2 ) & 63 );

        if( len-- <= 0 )
        {
            result += base64_encodetable.charAt( ( x << 4 ) & 63 );
            result += "==";
            break;
        }

        y = what.charCodeAt( ptr++ );
        result += base64_encodetable.charAt( ( ( x << 4 ) | ( ( y >> 4 ) & 15 ) ) & 63 );

        if ( len-- <= 0 )
        {
            result += base64_encodetable.charAt( ( y << 2 ) & 63 );
            result += "=";
            break;
        }

        x = what.charCodeAt( ptr++ );
        result += base64_encodetable.charAt( ( ( y << 2 ) | ( ( x >> 6 ) & 3 ) ) & 63 );
        result += base64_encodetable.charAt( x & 63 );

    }

    return result;
}

if(typeof Balance=='undefined') {
    var Balance = {};
}
//create ajax class
Balance.Ajax = Class.create();
//create ajax class method
Balance.Ajax.prototype = {
	//define loading url
	initialize: function(){
		this.views = [];
	},
	setLoadUrl:function(url){
		this.url = url;
		return this;
	},
	addView: function(view){
		this.views.push(view);
		return this;
	},
	//get view by id
	getViewById: function(id){
		for (var i in this.views){
			var view = this.views[i];
			if(view.id == id){
				return view;
			}
		}
		return false;
	},
	//get view by tag
	getViewByTag: function(tag){
		var views = [];
		for (var i in this.views){
			var view = this.views[i];
			if(view.tag == tag){
				views.push(view);
			}
		}
		if(views.length){
			return views;
		}else{
			return false;
		}
	},
	load: function(){
		var views = this.views;
		this.loadViews(views);
	},
	reloadView: function(view){
		var views = [];
		views.push(view);
		this.loadViews(views);
	},
	reloadViews: function(views){
		this.loadViews(views);
	},
	loadViews: function(views){
		var blocks = [], dt = new Object, allowJsExecute= [];
		if(views.length){
			for (var i = 0; i < views.length; i++){
				var view = views[i];
				blocks[view.id] = $(view.id);
				dt[view.id] = view.data;
				allowJsExecute[view.id] = view.allowJsExecute;
			}
			var ref = encode_base64(window.location.href.toString());
			new Ajax.Request(this.url, {
				method: 'post',
				parameters: {data:Object.toJSON(dt),uenc:ref},
				onSuccess: function(transport){
					var htmls = transport.responseText.evalJSON();
					for(var property in htmls){
						block = blocks[property];
						js = allowJsExecute[property];
						block.innerHTML = htmls[property];
						if(js){
							var html = block.innerHTML, scripts = html.extractScripts();
							scripts.map(function(script){
								return eval(script);
							});
						}
					}
				}
			});
		}
	}
},

//create ajax view class
Balance.AjaxView = Class.create();
Balance.AjaxView.prototype = {
	initialize: function(id, data, allowJsExecute, tag){
		if(tag == 'undefined') tag = null;
		if(typeof allowJsExecute == 'undefined') allowJsExecute = true;
		this.id = id;
		this.data = data;
		this.allowJsExecute = allowJsExecute;
		this.tag = tag;
	}
};


