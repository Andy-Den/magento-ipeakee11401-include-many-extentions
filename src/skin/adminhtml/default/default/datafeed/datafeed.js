document.observe('click',function(e){
		
    if(e.findElement('input[type=checkbox]')){ 
        i=e.findElement('input[type=checkbox]');
		
        i.ancestors().each(function(a){
            if(a.hasClassName('fieldset')) 	selector=$(a.id);
        })
        if(selector.id=='attributes-selector'){
            if(i.checked==true)	i.ancestors()[1].select('div')[0].select('INPUT:not(INPUT[type=checkbox]),SELECT').each(function(h){
                h.disabled=false
            })
            else i.ancestors()[1].select('div')[0].select('INPUT:not(INPUT[type=checkbox]),SELECT').each(function(h){
                h.disabled=true
            })
        }
			
        i.ancestors()[1].select('li').each(function(li){
            if(i.checked==true) { 
                li.select('INPUT')[0].checked=true;
            }
            else {
                li.select('INPUT')[0].checked=false;
            }
        })

		
		
        setValues(selector);
		
		
        selector.select('.selected').each(function(s){
            s.removeClassName('selected')
        })
        selector.select('.node').each(function(li){
            if(li.select('INPUT')[0].checked==true){
                li.addClassName('selected');
				
            }
        })
    }
})
document.observe('dom:loaded', function(){
    $$('.mapping').each(function(m){
        m.observe('focus',function(e){
            if(m.value.trim()==datafeed.mappingStr){
                m.value='';
                m.setStyle({
                    color:'green'
                })
				
            }
            setValues($('category-selector'));
        })
        m.observe('blur',function(e){
            if(m.value.trim()=='' || m.value.trim()==datafeed.mappingStr){
                m.value=datafeed.mappingStr;
                m.setStyle({
                    color:'grey'
                })
				
            }
            setValues($('category-selector'));
        })
        
        m.observe('keydown',function(e){
           
            switch(e.keyCode){
              
                case 45:
                    mapper= e.findElement('.mapping');
                    if($$('.mapping').indexOf(mapper)+1<$$('.mapping').length){
                        $$('.mapping')[($$('.mapping').indexOf(mapper)+1)].focus();
                        $$('.mapping')[($$('.mapping').indexOf(mapper)+1)].value=mapper.value;
                    }
                    break;
                case 35:
                    mapper= e.findElement('.mapping');
                    mapper.up().up().select('ul').each(function(u){
                        u.addClassName('open')
                    })
                    mapper.up().up().select('input[type=text]').each(function(i){
                        i.focus();
                        i.value=mapper.value;
                    })
                    break;
            } 
        })
    })
        
    
        
        
    if($('datafeed_categories').value!="*" && $('datafeed_categories').value!=""){
        attributes=$('datafeed_categories').value.evalJSON();
	
        attributes.each(function(attribute){
            if($('category_'+attribute.line)){
                if(attribute.checked){
                    $('category_'+attribute.line).checked=true;
                    $('category_'+attribute.line).ancestors()[1].addClassName('selected');
                    if($('category_'+attribute.line).ancestors()[2].previous())
                        $('category_'+attribute.line).ancestors()[2].previous().select('.tree_view')[0].addClassName('open');
                }
                if(attribute.mapping!=""){
                    $('category_'+attribute.line).next().next().next().value=attribute.mapping;
                    $('category_'+attribute.line).next().next().next().setStyle({
                        color:'green'
                    })
                    if($('category_'+attribute.line).ancestors()[2].previous())
                        $('category_'+attribute.line).ancestors()[2].previous().select('.tree_view')[0].addClassName('open');
                }
                else if( $('category_'+attribute.line)){
				
                    $('category_'+attribute.line).next().next().next().value=datafeed.mappingStr;
				
                   
                }
            }
        });
        $$('.node').each(function(n){
            if(n.select("ul")[0] && n.select('.tree_view.open').length<1){
                n.select("ul")[0].hide();
                n.select('.tree_view')[0].addClassName('close');
            }
            else if (n.select("ul")[0]){
                n.select('.tree_view')[0].addClassName('open');
            }
        })
    }
    else{
        $$('.mapping').each(function(m){
            m.value=datafeed.mappingStr;
        })
        $$('.node').each(function(n){
            if(n.select("ul")[0]){
                n.select('.tree_view')[0].addClassName('close');
                n.select("ul")[0].hide();
            }
        })
    }
       
    $$('.node').each(function(n){
        if(n.select('.tree_view')[0]){
            n.select('.tree_view')[0].observe('click',function(){
                if(n.select('.tree_view')[0].hasClassName('open')){
                    if(n.select("ul")[0]) n.select("ul")[0].hide();
                    n.select('.tree_view')[0].removeClassName('open').addClassName('close');
                }
                else{

                    if(n.select("ul")[0]) n.select("ul")[0].show();
                    n.select('.tree_view')[0].removeClassName('close').addClassName('open');

                }
            })
        }
    })
    
     
    if($('datafeed_type_ids').value!=''){

        $('datafeed_type_ids').value.split(',').each(function(e){
            $('type_id_'+e).checked=true;
            $('type_id_'+e).ancestors()[1].addClassName('selected');
        });
    }
    if($('datafeed_visibility').value!=''){
        $('datafeed_visibility').value.split(',').each(function(e){
            $('visibility_'+e).checked=true;
            $('visibility_'+e).ancestors()[1].addClassName('selected');
        });
    }
 
  if($('datafeed_attributes').value=='')$('datafeed_attributes').value="[]";
    attributes=$('datafeed_attributes').value.evalJSON();
    
   if(attributes.length>0){
    attributes.each(function(attribute){
 
        if(attribute.checked){
            $('attribute_'+attribute.line).checked=true;
            $('node_'+attribute.line).addClassName('selected');
            $('node_'+attribute.line).select('INPUT:not(INPUT[type=checkbox]),SELECT').each(function(h){
                h.disabled=false
            })
        }
        $('name_attribute_'+attribute.line).value=attribute.code;
        $('condition_attribute_'+attribute.line).value=attribute.condition;
        $('value_attribute_'+attribute.line).value=attribute.value;
    });
   }
     
    $('attributes-selector').select('SELECT').each(function(n){
         
        if(n.hasClassName('name-attribute')){
            prefilledValues=n.next().next();
            eval("options="+n.value);
            
            html=null;
            custom=true;
            if(options.length>0){
                options.each(function(o){
                    if (prefilledValues.next().value.split(',').indexOf(o.value+'')!=-1){
                        selected='selected'
                        custom=false;
                    }
                    else{
                        selected=false;
                    }
                
                    html+="<option value='"+o.value+"' "+selected+">"+o.label+"</option>";
                })
                if(custom)selected="selected";
                else selected='';
                html+="<option value='_novalue_' style='color:#555' "+selected+">custom value...</option>";
            
          
                if(!custom){
                          
                    prefilledValues.setStyle({
                        'display':'inline'
                        
                    });
                    prefilledValues.next().setStyle({
                        'display':'none'
                        
                    }) 
                /* r=[];
                    prefilledValues.select('OPTION').each(function(e){
                        if(e.selected) r.push(e.value)
                    })
                    r=r.join(',')
                    prefilledValues.next().value=r;
                    */
                }
                else {
                    prefilledValues.setStyle({
                        'display':'inline'
                        
                    });
                    prefilledValues.next().setStyle({
                        'display': 'block',
                        'margin': '0 0 0 422px'
                        
                    }) 
                }
                prefilledValues.update(html)
                
                
                
            }
            
            
            n.observe('change',function(){
             
                prefilledValues=n.next().next();
                eval("options="+n.value);
                html="";
                options.each(function(o){
                    (o.value==prefilledValues.next().value)? selected='selected':selected=null;
                
                    html+="<option value='"+o.value+"' "+selected+">"+o.label+"</option>";
                })
                
                html+="<option value='_novalue_' style='color:#555'>custom value...</option>";
                if(options.length>0){
                   
                    prefilledValues.setStyle({
                        'display':'inline'
                        
                    });
                    prefilledValues.next().setStyle({
                        'display':'none'
                       
                    }) 
                   
                    prefilledValues.update(html)
                    
                   
                }
                else{
                    prefilledValues.setStyle({
                        'display':'none'
                        
                    });
                    prefilledValues.next().setStyle({
                        'display':'inline',
                        'margin': '0 0 0 0'
                       
                    }) 
                    prefilledValues.next().value=null;
                    
                }
                prefilledValues.next().value=null
                setValues($("attributes-selector"))
            })
        }
    })
    $$('.pre-value-attribute').each(function(prefilledValues){
        prefilledValues.observe('change',function(){
                       
            if(prefilledValues.value!='_novalue_'){
                          
                prefilledValues.setStyle({
                    'display':'inline'
                    
                });
                prefilledValues.next().setStyle({
                    'display':'none'
                    
                }) 
                r=[];
                prefilledValues.select('OPTION').each(function(e){
                    if(e.selected) r.push(e.value)
                })
                r=r.join(',')
                     
                prefilledValues.next().value=r;
                setValues($("attributes-selector"))
               
            }
            else {
                prefilledValues.setStyle({
                    'display':'inline'
                   
                });
                prefilledValues.next().setStyle({
                    'display': 'block',
                    'margin': '0 0 0 422px'
                }) 
                
            }
                       
        })
    })
		
})

function setValues(selector){
    selection=new Array;
    selector.select('INPUT[type=checkbox]').each(function(i){
        if(selector.id=='attributes-selector'){
		
            attribute={}
            attribute.line=i.readAttribute('identifier');
            attribute.checked=i.checked;
            attribute.code=i.next().value;
            attribute.condition=i.next().next().value;
            attribute.value=i.next().next().next().next().value;
            selection.push(attribute);
        }
        else if(selector.id=='category-selector'){
			
            attribute={}
            attribute.line=i.readAttribute('identifier');
            attribute.checked=i.checked;
            attribute.mapping=i.next().next().next().value;
            if(attribute.mapping.trim()=="" || attribute.mapping.trim()==datafeed.mappingStr ) attribute.mapping="";
            selection.push(attribute);
			
        }
        else if(i.checked==true)selection.push(i.readAttribute('identifier'));
		
    })
    switch(selector.id){
        case 'category-selector':
            $('datafeed_categories').value=Object.toJSON(selection);
            break;
        case 'type-ids-selector':
            $('datafeed_type_ids').value=selection.join(',');
            break;
        case 'visibility-selector':
            $('datafeed_visibility').value=selection.join(',');
            break;
        case 'attributes-selector' :
            $('datafeed_attributes').value=Object.toJSON(selection);
            break;
    }
	
}



var datafeed={
    mappingStr:"empty",
    /*
		 * Passer en mode txt / csv  
		 * 
		 */
    clearFields : function(){
        $('feed_header').value='';
        $('feed_product').value='';
        $('feed_footer').value='';
			
    },
    /*
		 * Passer en mode txt / csv  
		 * 
		 */
    textMode : function(){
			
        $$('.txt-type').each(function(f){
            f.ancestors()[1].show()
				
        })
        $$('.txt-type:not(.not-required)').each(function(f){
            f.addClassName('required-entry')
			
        })
        $$('.xml-type').each(function(f){
            f.ancestors()[1].hide()
            f.removeClassName('required-entry')
        })
			
        $('feed_header').ancestors()[1].hide();
        $('feed_product').ancestors()[1].hide();
			
        $$('.fields-mapping').each(function(t){
            t.remove()
        });
		
		
        myContent=Builder.node('span',{
            className:'fields-mapping'
        },[
        Builder.node('div',{
            className:'mapping'
        },['Mapping']),               
        Builder.node('ul',{
            className:'txt-field-box',
            id:'txt-fields-box'
        })
        ])
        $('feed_include_header').insert({
            after:myContent
        });
			
        input=Builder.node('BUTTON',{
            className:'add-field ',
            type:'button',
            onclick:'datafeed.addTextField(\'\',\'\');datafeed.update();'
        },['Add field'])
        $('txt-fields-box').insert({
            after:input
        });
	       
        if($('feed_header').value!="" && $('feed_product').value!="")datafeed.jsonToTextFields();
			
        $('datafeed_form').addClassName('text')
    },
		
    /*
		 * Ajouter une ligne de champs de textes
		 * 
		 */
    addTextField : function(head, prod){
        input=Builder.node('LI',[ 	
            Builder.node('INPUT',{
                className:'txt-field  header-txt-field input-text', 
                value:head, 
                onkeyup:'datafeed.update()'
            }),
            Builder.node('INPUT',{
                className:'txt-field  product-txt-field input-text',
                value:prod,
                onkeyup:'datafeed.update()'
            }),
            Builder.node('BUTTON',{
                className:'remove-field ',
                type:'button', 
                onclick:'datafeed.removeTextField(this);datafeed.update();'
            },['X']),
            Builder.node('BUTTON',{
                className:'move-field-up ',
                type:'button', 
                onclick:'datafeed.moveField(this,"up");datafeed.update();'
            }),
            Builder.node('BUTTON',{
                className:'move-field-down ',
                type:'button', 
                onclick:'datafeed.moveField(this,"down");datafeed.update();'
            }),
            ])
        input.select('BUTTON')[1].update('&uarr;');
        input.select('BUTTON')[2].update('&darr;');
        $('txt-fields-box').insert({
            bottom:input
        });
    },
		
    /*
		 * Supprimer une ligne de champs de textes
		 * 
		 */
    removeTextField : function(elt){
        elt.ancestors()[0].remove();
    },
		
    /*
		 * D�placer une ligne de champs de textes
		 * 
		 */
    moveField : function(elt,direction){
			
        li=elt.ancestors()[0];
			
        index=$('txt-fields-box').select('LI').indexOf(li);
        if (index>0)  prev=index-1; 
        else prev=1;
			
        if (index<$('txt-fields-box').select('LI').length-1)  next=index+1; 
        else next=$('txt-fields-box').select('LI').length-2;
			
        prevli=$('txt-fields-box').select('LI')[prev];
        nextli=$('txt-fields-box').select('LI')[next];
          
        li.remove();
			
        switch(direction){
            case 'up' :
                prevli.insert({
                    before:li
                })
                break;
            default :
                nextli.insert({
                    after:li
                })
                break;
			
        }
    },
    /*
		 * Parser le json en lignes de champs de textes
		 * 
		 */
    jsonToTextFields : function(){
	
        data=new Object;	
        header=$('feed_header').value.evalJSON().header;
        product=$('feed_product').value.evalJSON().product;
        data.header=header;
        data.product=product;
			
        i=0;
        data.product.each(function(){
				
            datafeed.addTextField(data.header[i],data.product[i]);
            i++;
        })
		
			
    },
    /*
		 * Parser les lignes de champs de textes en JSON
		 * 
		 */
    textFieldsToJson : function(){
		
        data=new Object;	
        data.header=new Array;
        c=0;
        $('txt-fields-box').select('INPUT.header-txt-field').each(function(i){
            data.header[c]=i.value;
            c++;
        })
        data.product=new Array;
        c=0;
        $('txt-fields-box').select('INPUT.product-txt-field').each(function(i){
            data.product[c]=i.value;
            c++;
        })
        $('feed_header').value='{"header":'+Object.toJSON(data.header)+"}";
        $('feed_product').value='{"product":'+Object.toJSON(data.product)+"}";
			
    },
    /*
		 * Fournit les caract�res utilis�s pour cr�er le fichier en mode texte
		 * 
		 */
    getTextParams : function(){
        o=new Object;
        o.delimiter=$('feed_separator').value;
        o.enclosure=$('feed_protector').value;
        o.escape="\\";
        return o;
    },
		
    /*
		 * Construit le texte � afficher en preview
		 * 
		 */
    previewTextFile : function(value){
        txt=datafeed.getTextParams();
        rtn='';
        data=value.evalJSON()
        if(typeof data.header!='undefined') data=data.header;
        else data=data.product;
        i=0;
			
        data.each(function(o){
            if(txt.delimiter=='\\t')txt.delimiter="     ";
            if(i>0)rtn+=txt.delimiter;
				
            o=datafeed.escapeValue(o);
            rtn+=txt.enclosure+o+txt.enclosure;
            i++;
        })
			
			
        return rtn;
    },
    /*
		 * Echappement des caract�res 
		 * 
		 */
    escapeValue : function(value){
        txt=datafeed.getTextParams();
        if(txt.enclosure!=''){
				
            if(txt.enclosure=="|"){
                prot="\\|";
                protReplace="\\|";
                finalProt="|"
            }
            else {
                prot=txt.enclosure;
                protReplace="\\"+txt.enclosure;
                finalProt=txt.enclosure;
            }
            return value.replace(/\\/g,"\\\\").replace(eval("/"+prot+"/g"),protReplace);
			
        }
        else{
            if(txt.delimiter=="|"){
                sep="\\|";
                sepReplace="\\|";
            }
            else {
                sep=txt.delimiter;
                sepReplace="\\"+txt.delimiter;
            }
            return value.replace(/\\/g,"\\\\").replace(eval("/"+sep+"/g"),sepReplace);
        }
    },
	
	
    /*
		 * Passer en mode xml
		 * 
		 */
    xmlMode : function(){
			
        $$('.fields-mapping').each(function(t){
            t.remove()
        });
			
			
        $$('.txt-type').each(function(f){
            f.ancestors()[1].hide();
            f.removeClassName('required-entry')
        })
        $$('.xml-type').each(function(f){
            f.ancestors()[1].show()
            f.addClassName('required-entry')
        })
			
        $('feed_header').ancestors()[1].show();
        $('feed_product').ancestors()[1].show();
			
        $('datafeed_form').removeClassName('text')
    },
		
    /*
		 * Mise � jour des donn�es 
		 * 
		 */
    update:function(){
        // mise � jour des textarea si mode text
        if(!datafeed.isXmlMode()) datafeed.textFieldsToJson();
        // nom du fichier
        $('dfm-view').select('.feedname')[0].update($('feed_name').value)
        $('dfm-view').select('.feedtype')[0].update($('feed_type').options[$('feed_type').selectedIndex].innerHTML)
        // preview header
        if(!datafeed.isXmlMode())value=datafeed.enlighter(datafeed.previewTextFile($('feed_header').value));
        else value=datafeed.enlighter($('feed_header').value);
			
        if(($('feed_include_header').value!=0 && !datafeed.isXmlMode()) || datafeed.isXmlMode() ) $('dfm-view').select('._header')[0].update(value);
        else $('dfm-view').select('._header')[0].update('')
			
        // preview footer
        if( $('feed_type').value==1) $('dfm-view').select('._footer')[0].update(datafeed.enlighter($('feed_footer').value));
        else  $('dfm-view').select('._footer')[0].update('')
			
        // preview product
        if(!datafeed.isXmlMode())value=datafeed.previewTextFile($('feed_product').value);
        else value=$('feed_product').value;
			
        p='<br><pre class="productpattern">'+datafeed.enlighter(value)+'</pre><br>';
			
			
        if(!datafeed.isXmlMode()) $('dfm-view').select('._product')[0].update(p+p+p+p+p)
        else  $('dfm-view').select('._product')[0].update(p+p);
			
    },
    /*
		 * Surligenr le code
		 * 
		 */
    enlighter: function(text){
		
        // tags
        text=text.replace(/<([^?^!]{1}|[\/]{1})(.[^>]*)>/g,"<span class='blue'>"+"<$1$2>".escapeHTML()+"</span>")
			
        // comments
        text=text.replace(/<!--/g,"¤");
        text=text.replace(/-->/g,"¤");
        text=text.replace(/¤([^¤]*)¤/g,"<span class='green'>"+"<!--$1-->".escapeHTML()+"</span>");
			
        // php code
        text=text.replace(/<\?/g,"¤");
        text=text.replace(/\?>/g,"¤");
        text=text.replace(/¤([^¤]*)¤/g,"<span class='orange'>"+"<?$1?>".escapeHTML()+"</span>");
        // superattribut
        text=text.replace(/\{(G:[^\s}[:]*)(\sparent|\sgrouped|\sconfigurable|\sbundle)?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?\}/g,"<span class='purple'>{$1<span class='grey'>$2</span>$4<span class='green'>$5</span>$7<span class='green'>$8</span>$10<span class='green'>$11</span>$13<span class='green'>$14</span>$16<span class='green'>$17</span>$19<span class='green'>$20</span>}</span>");
        // superattribut 
        text=text.replace(/\{(SC:[^\s}[:]*)(\sparent|\sgrouped|\sconfigurable|\sbundle)?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?\}/g,"<span class='orangered '>{$1<span class='grey'>$2</span>$4<span class='green'>$5</span>$7<span class='green'>$8</span>$10<span class='green'>$11</span>$13<span class='green'>$14</span>$16<span class='green'>$17</span>$19<span class='green'>$20</span>}</span>");
        text=text.replace(/\{(sc:[^\s}[:]*)(\sparent|\sgrouped|\sconfigurable|\sbundle)?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?\}/g,"<span class='orangered '>{$1<span class='grey'>$2</span>$4<span class='green'>$5</span>$7<span class='green'>$8</span>$10<span class='green'>$11</span>$13<span class='green'>$14</span>$16<span class='green'>$17</span>$19<span class='green'>$20</span>}</span>");
		
        // attributs + 6 options 
        text=text.replace(/\{([^\s}[:]*)(\sparent|\sgrouped|\sconfigurable|\sbundle)?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?\}/g,"<span class='pink'>{$1<span class='grey'>$2</span>$4<span class='green'>$5</span>$7<span class='green'>$8</span>$10<span class='green'>$11</span>$13<span class='green'>$14</span>$16<span class='green'>$17</span>$19<span class='green'>$20</span>}</span>");
					
        // attributs + options bool
        text=text.replace(/\{([^\s}[:]*)(\sparent|\sgrouped|\sconfigurable|\sbundle)?(\?)(\[[^\]]*\])(:)(\[[^\]]*\])\}/g,"<span class='pink'>{$1<span class='grey'>$2</span>$3<span class='green'>$4</span>$5<span class='red'>$6</span>}</span>");
			
			
        return text;
    },		
	
    currentMode:null ,
    /*
		 * Savoir si on est en mode xml ou non
		 * 
		 */
		
    isXmlMode: function (){
        if($('feed_type').value==1) return true;
        else return false
    },
    /*
		 * Renvoie l'id du mode
		 * 
		 */
		
    getIdMode: function (){
			
        return $('feed_type').value;
    },
    /*
		 * R�gle l'id du mode
		 * 
		 */
		
    setIdMode: function (id){
			
        $('feed_type').value=id;
    },
    /*
		 * Changer de mode
		 * 
		 */
    changeMode : function (){
			
        if(datafeed.currentMode==null ){
            datafeed.currentMode=datafeed.getIdMode();
            if(datafeed.isXmlMode()) datafeed.xmlMode();
            else datafeed.textMode();
			
        }
        else{
            if((datafeed.currentMode>1 && datafeed.getIdMode()==1)|(datafeed.currentMode==1 && datafeed.getIdMode()>1) ){
                if(confirm("Changing file type from/to xml will clear all your setting.\ Do you want to continue ?")){
                    datafeed.clearFields();
                    if(datafeed.isXmlMode()) datafeed.xmlMode();
                    else datafeed.textMode();
                    datafeed.setIdMode(datafeed.getIdMode());
					
                }
                else  datafeed.setIdMode(datafeed.currentMode);
                datafeed.currentMode=datafeed.getIdMode();
            }
        }
        datafeed.update();
			
    }
		
}
/*
 * OBSERVERS
 * 
 */
document.observe('dom:loaded', function(){
	
	
    page=Builder.node('div',{
        id:'dfm-view'
    },[
    Builder.node('span','Preview : '),                                  
        Builder.node('span',{
            className:'feedname'
        },'exemple'),
        Builder.node('span','.'),
        Builder.node('span',{
            className:'feedtype'
        },'xml'),
        Builder.node('div',{
            id:'page'
        },[
	         
        Builder.node('pre',{
            className:'_header',
            name:''
        }),
        Builder.node('pre',{
            className:'_product',
            name:''
        }),
        Builder.node('pre',{
            className:'_footer',
            name:''
        })
           
        ])
        ])
    
    $('datafeed_form').select('.hor-scroll')[0].insert({
        bottom:page
    });
	
    $('feed_type').observe('change',function(){
        datafeed.changeMode();
    })
	
    $$('.refresh').each(function(f){
        f.observe('keyup', function(){
            datafeed.update()
        })
    })
    $$('.refresh').each(function(f){
        f.observe('change', function(){
            datafeed.update()
        })
    })
    datafeed.changeMode();
	
	
})