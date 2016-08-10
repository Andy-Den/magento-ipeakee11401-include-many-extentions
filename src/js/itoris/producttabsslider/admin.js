
	var tabs = {
		callUrl :	'',
		scope	:	'',
		pid		: 	0,
		sid		:	0,
		editorParams : null,
		skinUrl : '',
		//tinymceBaseUrl : '',
		
		setCallUrl : function(url){
			this.callUrl = url;
		},
		
		getCallUrl : function(){
			return this.callUrl;
		},
		
		setEditorParams : function(params){
			this.editorParams = params;
		},
		
		getEditorParams : function(){
			return this.editorParams;
		},
		
		setScope : function(scope){
			this.scope = scope;
		},
		
		getScope : function(){
			return this.scope;
		},
		
		setProductId : function(pid){
			this.pid = pid;
		},
		
		getProductId : function(){
			return this.pid;
		},
		
		setStoreId : function(sid){
			this.sid = sid;
			if(typeof(sid) == 'undefined'){
				this.sid = 0;
			}
		},
		
		getStoreId : function(){
			return this.sid;
		},
		
		toogleEnabled : function(id){
			tabsRemote.call(
					function(params){this.toogleEnabledCallback(params);}.bind(this),
					{'callAction' : 'toogleEnabled', 'id' : id});
		},
		
		toogleEnabledCallback : function(params){
			var row = $('association-no-' + params.tab_info.id);
			var img = '';
			if(params.tab_info.enabled == 1){
				img = '<img onclick="tabs.toogleEnabled('+params.tab_info.id+')" src="'+tabs.skinUrl+'images/accordion_open.png"  />';
			}else{
				img = '<img onclick="tabs.toogleEnabled('+params.tab_info.id+')" src="'+tabs.skinUrl+'images/accordion_close.png"  />';
			}
			if(row){
				row.select('.enabled')[0].update(img);
			}
		},
		
		_toogleEnabledCallback : function(params){
			var row = $('association-no-' + params.id);
			var img = '';
			if(params.enabled == 1){
				img = '<img onclick="tabs.toogleEnabled('+params.id+')" src="'+tabs.skinUrl+'images/accordion_open.png"  />';
			}else{
				img = '<img onclick="tabs.toogleEnabled('+params.id+')" src="'+tabs.skinUrl+'images/accordion_close.png"  />';
			}
			if(row){
				row.select('.enabled')[0].update(img);
			}
		},
		
		/*add	: function(){
			$('edit_form').action += '?addtab=true';
			$('edit_form').submit();
			return false;
		},*/
		
		edit : function(id, type){
			if(typeof(id) == 'undefined'){
				id = null;
			}
			if(typeof(type) == 'undifined'){
				type = '';
			}
			editDialog.prepare(id, function(){this.editPrepareComplete();}.bind(this), type);
			return false;
		},
		editPrepareComplete : function(){
			editDialog.open();
		},
		
		deleteId : function(id){
			var row = $('association-no-' + id);
			if(row){
				row.remove();
			}
		},
		
		order : function(id, dir){
			tabsRemote.call(
					function(params){this.orderCallback(params);}.bind(this),
					{	'order' : 'true', 'id' : id, 'dir' : dir,
						'pid' : this.getProductId(), 'sid' : this.getStoreId()
					});
			return false;
		},
		
		orderCallback : function(params){
			this.setOrder(params.orders);
		},
		
		setOrder : function(orders){
			var container = $$('.associations tbody')[0];
			for(var i = orders.length - 1; i >= 0; i--){
				var order = orders[i];
				var moved = $('association-no-' + order.id);
				container.insert({'top' : moved});
				this.setOrderImages(moved, order, i == 0, i == orders.length - 1);
			}
		},
		
		setOrderImages : function(container, order, isFirst, isLast){
			var images = '';
			if(!isFirst){
				images += '<img src="' + tabs.skinUrl + 'images/sort-arrow-up.png" onclick="return tabs.order('+ order.id +',\'up\');" />';
			}
			
			if(!isLast){
				images += '<img src="' + tabs.skinUrl + 'images/sort-arrow-down.png" onclick="return tabs.order('+ order.id +',\'down\');" />';
			}
			container.select('.order')[0].update(images);
		},
		
		updateTab : function(tabInfo){
			var row = $('association-no-' + tabInfo.id);
			if(!row){
				this.addTab(tabInfo);
			}else{
				if(!tabs.getScope()){
					row.select('.alias')[0].update(tabInfo.alias == CUSTOM_GLOBAL_ALIAS? '' :  tabInfo.alias.escapeHTML());
				}
				row.select('.title')[0].update(tabInfo.title.escapeHTML());
			}
			//alert("Updated");
		},
		
		addTab : function(tabInfo){
			var html = '<tr id="association-no-'+tabInfo.id+'">';
			if(!this.getScope()){
				var alias = tabInfo.alias == CUSTOM_GLOBAL_ALIAS ? '' :  tabInfo.alias.escapeHTML();
				html += '<td><div class="alias">'+alias+'</div></td>';
    		}
    		html += '<td><div class="title">'+tabInfo.title.escapeHTML()+'</div></td>';
    		html += '<td><div class="order"></div></td><td><div class="enabled"></div></td>';
    		html += '<td style="width:1px;" class="nobr">';
    		html += '<button onclick="return tabs.edit('+tabInfo.id+')">Edit</button> ';
    		if(tabInfo.inh == 1){
    			html += '<button disabled="disabled" class="disabled">Delete</button>';
    		}else{
    			html += '<button onclick="return deleteDialot.confirm('+tabInfo.id+')">Delete</button>';
    		}
    		html += '</td></tr>';
    		var container = $$('.associations tbody')[0];
    		container.insert({'bottom' : html});
		},
		
		toogleGlobal : function(el){
			var params = {
					'callAction' : 'toogleGlobal',
					'dir' : el.checked,
					'pid' : this.getProductId(),
					'sid' : this.getStoreId()};
			tabsRemote.call(function(params){this.toogleGlobalCallback(params);}.bind(this),params);
		},
		
		toogleGlobalCallback : function(params){
			if(params.dir == true){
				$$('.associations tbody tr').each(function(item){
					item.remove();
				});
				$('addNewTabButton').disabled = true;
				$('addNewTabButton').addClassName('disabled');
			}else{
				params.tabs_info.each(function(item){
					this.updateTab(item);
					this._toogleEnabledCallback(item);
				},this);
				this.setOrder(params.orders);
				$('addNewTabButton').disabled = false;
				$('addNewTabButton').removeClassName('disabled');
			}
		}
	}
	
	var deleteDialot = {
		id : null,
		confirm : function(id){
			this.id = id;
			var content = '<div class="confirm-message">Are you sure you want to delete this association?</div>';
			Dialog.confirm(content,{
				title:'Deleting association...',
				closable:true,
				ok: this.onOk.bind(this),
				width: 250,
				className:"magento",
				windowClassName:"popup-window confirm",
				buttonClass:"form-button form-button-dialog",
	            cancel: this.onCancel.bind(this),
	            onClose: this.onCancel.bind(this)
			});
			return false;
		},
		
		onOk : function(dialog){
			dialog.close();
			new Ajax.Request(tabs.getCallUrl() + '?deletetab=true&id=' + this.id + "&pid=" +tabs.getProductId() + "&sid=" + tabs.getStoreId(), {
                onSuccess: function(transport) {
					var result = transport.headerJSON;
					if(result == null){
						tabsDialog.info('Error...', 'Server returned invalid answer.');
					}else if(result.error_code == 0){
						tabs.deleteId(result.deleted_id);
						tabs.setOrder(result.orders);
						tabsDialog.info('Success...', result.message);
					}else{
						tabsDialog.info('Error...', result.message);
					}
                }.bind(this),
                
                onFailure: function(transport){
                	tabsDialog.info('Error...', transport.status + '. '+ transport.statusText);
                }
            });
		},
		
		onCancel : function(dialog){
			dialog.close();
		}
	}

	var CUSTOM_GLOBAL_ALIAS = '{{custom_global_alias}}';
	
	var editDialog = {
		alias : '',
		title : '',
		text  : '',
		pid   : tabs.getProductId(),
		sid   : tabs.getStoreId(),
		dialogContent : '',
		id	  : 0,
		parent : 0,
		currentDialog : null,
		afterPrepareCallback : null,
		defaultAliases : ['product_additional_data', 'description', 'additional', 'upsell_products', 'product.tags', 'productTagList'],
		type : '', 
		
		prepare	: function(id, callback, type){
			this.dialogContent = '';
			this.alias = '';
			this.title = '';
			this.text = '';
			this.parent = 0;
			this.pid   = tabs.getProductId();
			this.sid   = tabs.getStoreId();
			this.type = type;
			if(this.type == 'custom'){
				this.alias = CUSTOM_GLOBAL_ALIAS;
			}
			
			this.afterPrepareCallback = callback;
			if(id == null){
				this.id = 0;
				this.prepareFinish();
				return;
			}
			this.id = id;
			tabsRemote.call(function(params){this.prepareCallback(params);}.bind(this),
					{'callAction':'tabInfo', 'id':id});
			return false;
		},
		
		prepareCallback : function(params){
			this.alias = params.tab_info.alias;
			this.title = params.tab_info.title;
			this.text  = params.tab_info.text;
			this.parent = params.tab_info.parent;
			this.prepareFinish();
		},
		
		prepareFinish : function(){
			this.startDialogHtml();
			if(!tabs.getScope()){
				if(this.alias != CUSTOM_GLOBAL_ALIAS){
					this.addAliasHtml();
				}
				
				this.addTitleHtml();
				
				if(this.alias == CUSTOM_GLOBAL_ALIAS){
					this.addTextHtml();
				}
				
			}else{
				this.addTitleHtml();
				if(this.alias == ''){
					this.addTextHtml();
				}
			}
			this.fitishDialogHtml();
			this.afterPrepareCallback();
		},
		
		startDialogHtml : function(){
			this.dialogContent = '<div class="tabs-edit">';
		},
		fitishDialogHtml : function(){
			this.dialogContent += '</div>';
		},

		/* ------------------alias------------------------------------- */
		addAliasHtml : function(){
			var html = '<div class="content-header"><h3>Alias</h3></div>';
			html += '<div class="alias-content">';
			html += '<input type="radio" name="brick_alias_from" id="brick_alias_select_radio" value="alias_select"  ';
			if(this.isAliasDefault()){
				html += 'checked="checked"';
			}
			html += '/> <select name="alias_select" onclick="$(\'brick_alias_select_radio\').checked = true;" >';
			this.defaultAliases.each(function(item){
				html += '<option ';
				if(item == this.alias){
					html += 'selected="selected"';
				}
				html += ' >'+item.escapeHTML()+'</option>';
			},this);
			
			html += '</select>';
			html += '<input type="radio" value="alias_input" name="brick_alias_from" id="brick_alias_input_radio" ';
			if(!this.isAliasDefault()){
				html += 'checked="checked"';
			}
			html += ' /> <input name="alias_input" type="text" onmousedown="$(\'brick_alias_input_radio\').checked = true;" ';
			if(!this.isAliasDefault()){
				html += ' value="'+this.alias.escapeHTML() + '"';
			}
			
			html += ' />';
			html += '</div>';
			this.dialogContent += html;
		},
		
		isAliasDefault: function(){
			return this.defaultAliases.indexOf(this.alias) != -1;
		},
		
		/* --------------------------- title -----------------------------*/
		
		addTitleHtml : function(){
			var html = '<div class="content-header"><h3>Title</h3></div>'; 
			html += '<div class="title-content">';
			html += '<input type="text" name="brick_title" style="width:99.5%;" value="'+this.title.escapeHTML()+'" />';
			html += '</div>';
			this.dialogContent += html;
		},
		
		addTextHtml : function(){
			var html = '<div class="content-header"><h3>Text</h3></div>';
			html += '<textarea name="content" title="" id="short_description_editor" class="textarea  required-entry" style="width:625px !important;height:460px" rows="2" cols="15" class=" required-entry" >'+this.text.escapeHTML()+'</textarea>';
			this.dialogContent += html;
		},
		
		open : function(){
			Dialog.confirm(this.dialogContent, {
	            draggable:true,
	            resizable:true,
	            closable:true,
	            className:"magento",
	            windowClassName:"popup-window",
	            title:'Edit tab',
	            width:650,
	            zIndex:100,
	            recenterAuto:false,
	            hideEffect:Element.hide,
	            showEffect:Element.show,
	            id:"catalog-wysiwyg-editor",
	            buttonClass:"form-button",
	            okLabel:"Save",
	            cancelLabel:"Close",
	            ok: this.okDialogWindow.bind(this),
	            cancel: this.closeDialogWindow.bind(this),
	            onClose: this.closeDialogWindow.bind(this)
	        });

			if((this.alias == '' && tabs.getScope()) ||  this.alias == CUSTOM_GLOBAL_ALIAS ){
				var wParams = tabs.getEditorParams();
				if(wParams == false) return;
				if ("undefined" != typeof(Translator)) {
					Translator.add({"Insert Image...":"Insert Image...","Insert Media...":"Insert Media...","Insert File...":"Insert File..."});
				}
				//tinymce.baseURL =  tabs.tinymceBaseUrl;
				wysiwygpage_content = new tinyMceWysiwygSetup("short_description_editor", wParams);
				wysiwygpage_content.setup("exact");
				varienGlobalEvents.attachEventHandler("tinymceBeforeSetContent", wysiwygpage_content.beforeSetContent.bind(wysiwygpage_content));

				varienGlobalEvents.clearEventHandlers("open_browser_callback");
				if (wysiwygpage_content.openFileBrowser) {
					varienGlobalEvents.attachEventHandler("open_browser_callback", wysiwygpage_content.openFileBrowser.bind(wysiwygpage_content));
				} else if (wysiwygpage_content.openImagesBrowser){
					varienGlobalEvents.attachEventHandler("open_browser_callback", wysiwygpage_content.openImagesBrowser.bind(wysiwygpage_content));
				}

			}
		},
		
		okDialogWindow : function(dialog){
			var params = this.retriveValues();
			params['id'] = this.id;
			params['pid'] = this.pid;
			params['sid'] = this.sid;
			params['callAction'] = 'editTab';
			if((this.alias == '' && tabs.getScope()) ||  this.alias == CUSTOM_GLOBAL_ALIAS){
				if(tabs.getEditorParams() !== false){
					var text = tinyMCE.get('short_description_editor').getContent();
					text = wysiwygpage_content.decodeWidgets(text);
					text = wysiwygpage_content.decodeDirectives(text);
					params['text'] = text;
				}else{
					params['text'] = $('short_description_editor').value;
				}
			}else{
				params['text'] = '';
			}
			
			if(this.alias == CUSTOM_GLOBAL_ALIAS){
				params['brick_alias_from'] = "alias_select";
				params['alias_select'] = this.alias;
			}
			
			this.currentDialog = dialog;
			tabsRemote.call(function(params){this.okDialogWindowCallback(params);}.bind(this),params);
		},
		
		okDialogWindowCallback : function(params){
			tabs.updateTab(params.tab_info);
			tabs.setOrder(params.orders);
			tabs.toogleEnabledCallback(params);
			this.id = params.tab_info.id;
			this.currentDialog.close();
			tabsDialog.info('Success..', params.message);
		},
		
		closeDialogWindow : function(dialog){
			dialog.close();
		},
		
		retriveValues : function(){
			var els = ['alias_select', 'alias_input', 'brick_title'];
			var values = {};
			els.each(function(item){
				var el = document.getElementsByName(item)[0];
				if(el){
					values[item] = el.value;
				}
			});
			
			var els = document.getElementsByName('brick_alias_from');
			for(var i = 0; i < els.length; i++){
				var item = els[i];
				if(item.checked == true){
					values['brick_alias_from'] = item.value;
				}
			}
			return values;
		}
	}

	var tabsDialog = {
		info : function(caption, msg){
			msg = '<div class="confirm-message">' + msg + '</div>';
			Dialog.alert(msg,{
				title:caption,
				closable:true,
				width: 350,
				className:"magento",
				windowClassName:"popup-window confirm",
				buttonClass:"form-button"
			})
		}
	}
	
	var tabsRemote = {
		callback : null,
		call : function(callback, params){
			new Ajax.Request(tabs.getCallUrl(),{
				method		: 'post',
				parameters	: params,
				evalJSON	: true,
				onSuccess	: function(trans){
					if(trans.status != 200){
						this.onFailure(trans);
					}
					try{
						var resp = trans.responseText.evalJSON();
						if(resp.error == false){
							callback(resp);
						}else{
							tabsDialog.info('Error...', resp.message);
						}
					}catch(e){
						tabsDialog.info('Error...', e.message);
					}
				},
				
				onFailure	: function(trans){
					tabsDialog.info('Error...', transport.status + '. '+ transport.statusText);
				}
			});
		}
	};
	
	openEditorPopup = function(url, name, specs, parent) {
			if ((typeof popups == "undefined") || popups[name] == undefined || popups[name].closed) {
				if (typeof popups == "undefined") {
					popups = new Array();
				}
				var opener = (parent != undefined ? parent : window);
				popups[name] = opener.open(url, name, specs);
			} else {
				popups[name].focus();
			}
			return popups[name];
		}

		closeEditorPopup = function(name) {
			if ((typeof popups != "undefined") && popups[name] != undefined && !popups[name].closed) {
				popups[name].close();
			}
		}	
