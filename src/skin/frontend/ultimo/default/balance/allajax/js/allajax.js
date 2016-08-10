var ajaxcart = {
   // g: new Growler(),
    initialize: function() {
       // this.g = new Growler();		
        this.bindEvents();
		this.loadinStatusGeneral = false;
    },
    bindEvents: function () {
        this.addSubmitEvent();

        $$('a[href*="/catalog/product_compare/remove/"]').each(function(e) {
            $(e).writeAttribute('onclick',' ');
            $(e).observe('click', function(event) {
               // alert('sasa');
                var confirm_res =  confirm('Are you sure you would like to remove this item from the compare products?');
                if(confirm_res){
                setLocation($(e).readAttribute('href'));
                Event.stop(event);
                return false;
                }else{
                Event.stop(event);
                return false;
                }
            });
        });
        $$('a[href*="/catalog/product_compare/clear/"]').each(function(e) {
            $(e).writeAttribute('onclick',' ');
            $(e).observe('click', function(event) {
                var confirm_res =  confirm('Are you sure you would like to remove all products from your comparison?');
                if(confirm_res){
                setLocation($(e).readAttribute('href'));
                Event.stop(event);
                return false;
                }else{
                Event.stop(event);
                return false;
                }
            });
        });
        $$('a[href*="/wishlist/index/remove/"]').each(function(e) {
            $(e).observe('click', function(event) {
                var confirm_res =  confirm('Are you sure you would like to remove this item from the wishlist?');
                if(confirm_res){
                setLocation($(e).readAttribute('href'));
                Event.stop(event);
                return false;
                }else{
                Event.stop(event);
                return false;
                }
            });
        });
    },
    ajaxCartSubmit: function(obj, id, producttype, itemid) {
        
        var _this = this;
        //if(Modalbox !== 'undefined' && Modalbox.initialized)Modalbox.hide();

        try {
            if(typeof obj == 'string') {
                var url = obj;

                new Ajax.Request(url, {
                     onSuccess	: function(response) {
                        // Handle the response content...
                        try{
                            var res = response.responseText.evalJSON();
                            if(res) {
                                //check for group product's option
                                if(res.configurable_options_block) {
                                    if(res.r == 'success') {
                                      //show group product options block
                                        
                                        if(producttype=='wishlistgrouped'){
                                        _this.showPopupForWishlist(res.configurable_options_block,itemid);    
                                        }else{
                                        _this.showPopup(res.configurable_options_block);
                                        }
                                    } else {
                                        if(typeof res.message != 'undefined') {
                                            _this.showError(res.message);
                                        } else {
                                            _this.showError("Unable to Load Configurable Options.");
                                        }
                                    }
                                } else {
                                    if(res.r == 'success') {
                                        if(res.message) {
                                            _this.showSuccess(res.message,id);
                                        } else {
                                            _this.showSuccess('Unable to Add Product into cart.');
                                        }

                                        //update all blocks here
                                        _this.updateBlocks(res.update_blocks);

                                    } else {
                                        if(typeof res.message != 'undefined') {
                                            _this.showError(res.message);
                                        } else {
                                            _this.showError("Unable to Add Product into cart.");
                                        }
                                    }
                                }
                            } else {
                                document.location.reload(true);
                            }
                        } catch(e) {
                        //window.location.href = url;
                        //document.location.reload(true);
                        }
                    }
                });
            } else {
                if(typeof obj.form.down('input[type=file]') != 'undefined') {

                    //use iframe

                    obj.form.insert('<iframe id="upload_target" name="upload_target" src="" style="width:0;height:0;border:0px solid #fff;"></iframe>');

                    var iframe = $('upload_target');
                    iframe.observe('load', function(){
                        // Handle the response content...
                        try{
                            var doc = iframe.contentDocument ? iframe.contentDocument : (iframe.contentWindow.document || iframe.document);
                            console.log(doc);
                            var res = doc.body.innerText ? doc.body.innerText : doc.body.textContent;
                            res = res.evalJSON();

                            if(res) {
                                if(res.r == 'success') {
                                    if(res.message) {
                                        _this.showSuccess(res.message,id);
                                    } else {
                                        _this.showSuccess('Item was added into cart.',id);
                                    }

                                    //update all blocks here
                                    _this.updateBlocks(res.update_blocks);

                                } else {
                                    if(typeof res.message != 'undefined') {
                                        _this.showError(res.message);
                                    } else {
                                        _this.showError("Unable to Update Block");
                                    }
                                }
                            } else {
                                _this.showError("Unable to Update Block");
                            }
                        } catch(e) {
                            console.log(e);
                            _this.showError("Unable to Update Block");
                        }
                    });

                    obj.form.target = 'upload_target';

                    //show loading
                    _this.g.warn("Processing", {
                        life: 5
                    });

                    obj.form.submit();
                    return true;

                } else {
                    //use ajax

                    var url	 = 	obj.form.action,
                    data =	obj.form.serialize();

                    new Ajax.Request(url, {
                        method		: 'post',
                        postBody	: data,
                        onCreate	: function() {

                                jQuery('.allajax-loading').css('display','block');
                                jQuery('.allajax-configurations').css('display','none');
        
                        },
                        onSuccess	: function(response) {
                            // Handle the response content...
                            try{
                                var res = response.responseText.evalJSON();

                                if(res) {
                                    if(res.r == 'success') {
                                        if(res.message) {
                                            _this.showSuccess(res.message,id);
                                        } else {
                                            _this.showSuccess('Item was added into cart.',id);
                                        }

                                        //update all blocks here
                                        _this.updateBlocks(res.update_blocks);

                                    } else {
                                        if(typeof res.message != 'undefined') {
                                            _this.showError(res.message);
                                        } else {
                                            _this.showError("Unable to Update Block");
                                        }
                                    }
                                } else {
                                    _this.showError("Unable to Update Block");
                                }
                            } catch(e) {
                                console.log(e);
                                _this.showError("Unable to Update Block");
                            }
                        }
                    });
                }
            }
        } catch(e) {
            console.log(e);
            if(typeof obj == 'string') {
                window.location.href = obj;
            } else {
                document.location.reload(true);
            }
        }
    },
//    updateDomFunctions: function(){
//        
//     
//     
//       jQuery('#quick-compare .feature-icon-hover').hover(
//            function() {
//              jQuery('#quick-compare .dropdown-menu').show();
//            }, function() {
//              jQuery('#quick-compare .dropdown-menu').hide();
//            }
//          );
//
//        
//    },
    compareDropDown: function(){
         //alert('dsdsd');
          jQuery(document).ready(function() { 
           jQuery("#quick-compare .dropdown-toggle").hover(
               function() {
                 jQuery(this).next().show();
                 jQuery('#quick-compare').addClass('open');
               }, function() {
                   setTimeout(function() {
                    if (jQuery('#quick-compare .dropdown-menu').is(':hover')) {
                        //alert('2');
                           jQuery('#quick-compare .dropdown-menu').hover(
                                   function() {

                                     }, function() {
                                         jQuery('#quick-compare .dropdown-menu').hide();
                                         jQuery('#quick-compare').removeClass('open');
                                     });
                       }else{
                         //  alert('1'+jQuery('#quick-compare .dropdown-menu').attr('class'));
                           jQuery('#quick-compare .dropdown-menu').hide();
                           jQuery('#quick-compare').removeClass('open');
                       }
                   }, 300);
               }
             );
     });
    },
    getConfigurableOptions: function(url) {
       
        var _this = this;
        new Ajax.Request(url, {
        onSuccess : function(response) {
                // Handle the response content...
                try{
                    var res = response.responseText.evalJSON();
                    if(res) {
                        if(res.r == 'success') {
                            
                            //show configurable options popup
                            _this.showPopup(res.configurable_options_block);

                        } else {
                            if(typeof res.message != 'undefined') {
                                _this.showError(res.message);
                            } else {
                                _this.showError("Unable to Load Configurable Options.");
                            }
                        }
                    } else {
                        document.location.reload(true);
                    }
                } catch(e) {
                window.location.href = url;
                //document.location.reload(true);
                }
            }
        });
    },
     getConfigurableOptionsForWishlist: function(url,id,itemid) {

        var _this = this;
        new Ajax.Request(url, {
            onSuccess: function(response) {
                // Handle the response content...
                try {
                    var res = response.responseText.evalJSON();
                    if (res) {
                        if (res.r == 'success') {

                            //show configurable options popup
                            _this.showPopupForWishlist(res.configurable_options_block,itemid);

                        } else {
                            if (typeof res.messages != 'undefined') {
                                _this.showError(res.messages);
                            } else {
                                _this.showError("Unable to Load Configurable Options.");
                            }
                        }
                    } else {
                        document.location.reload(true);
                    }
                } catch (e) {
                    window.location.href = url;
                    //document.location.reload(true);
                }
            }
        });
    },
  setLoadStart: function(addID){                                   
            var iconId = addID+'-ajax-please-wait';
            if (!this.loadinStatusGeneral) {
                Element.show(iconId);
                this.loadinStatusGeneral = true;
                return true;
            }    
            return false;    
        },
    setLoadStop: function(addID){
            var iconId = addID+'-ajax-please-wait';
            this.loadinStatusGeneral = false;
            Element.hide(iconId);
        }   
       ,
    generalAjaxRequests: function(url,waitID) {

        var _this = this;
        new Ajax.Request(url, {
            onSuccess: function(response) {
                // Handle the response content...
                try {
                    var res = response.responseText.evalJSON();
                    if (res) {
                        if (res.r == 'success') {

                            ajaxcart.setLoadStop(waitID);
                            _this.updateBlocks(res.update_blocks);    
                        } else {
                            if (typeof res.messages != 'undefined') {
                                alert(res.messages);
                            } else {
                                alert("Unable to Load Configurable Options.");
                            }
                        }
                    } else {
                        document.location.reload(true);
                        ajaxcart.setLoadStop(waitID);
                    }
                } catch (e) {
                    window.location.href = url;
                    ajaxcart.setLoadStop(waitID);
                    //document.location.reload(true);
                }
            }
        });
    },
    getParams: function(url,sParam){
            var sPageURL = url;
            var sURLVariables = sPageURL.split('/');
            for (var i = 0; i < sURLVariables.length; i++) 
            {
                //var sParameterName = sURLVariables[i].split('=');
                if (sURLVariables[i] == sParam) 
                {
                    return sURLVariables[i+1];
                }
            }  
    },
checkproductlAjaxRequests: function(url,id,original_url,itemid) {

        var _this = this;
        new Ajax.Request(url, {
            onSuccess: function(response) {
                // Handle the response content...
                try {
                    var res = response.responseText.evalJSON();
                    if (res) {
                        if (res.r == 'success') {

                            if (res.message) {
                                
                                url = res.url;
								if(res.message=="grouped"){
                                        ajaxcart.ajaxCartSubmit(url, id,'wishlistgrouped',itemid);
                                        
                                }else{
                                   if (url.search('checkout/cart/add') != -1) {
                                        ajaxcart.ajaxCartSubmit(original_url, id);
                                    
                                    }else if (url.search('options=cart') != -1) {
                                        url += '&ajax=true';
                                        ajaxcart.getConfigurableOptionsForWishlist(url, id,itemid);
                                       
                                    }
                                }
                                }
                        } else {
                            if (typeof res.message != 'undefined') {
                                alert(res.message);
                            } else {
                                alert("Unable to Complete Process.");
                            }
                        }
                    } else {
                        document.location.reload(true);
                                              
                    }
                } catch (e) {
                    window.location.href = url;

                }
            }
        });
    },
    showSuccess: function(message,id) {
        jQuery('#allajax-poststeps .allajax-respond-msg').html('<ul class="messages ajaxcart-messages"><li class="success-msg"><ul><li><span>'+message+'</span></li></ul></li></ul>');
        jQuery('#allajax-prestep').css('display','none');
        jQuery('#allajax-poststeps').css('display','block');
        ajaxcart.checkConfigurableRedirect();
        
    },

    showError: function (error) {
        var _this = this;

        if(typeof error == 'string') {
           
        jQuery('#allajax-poststeps .allajax-respond-msg').html('<ul class="messages ajaxcart-messages"><li class="error-msg"><ul><li><span>'+error+'</span></li></ul></li></ul>');
                _this.showPostStep();
          			if(error=="Please Login First."){
            window.location.replace(getBaseUrl()+"customer/account/login/");
            }  
         } else {
            error.each(function(message){
            jQuery('#allajax-poststeps .allajax-respond-msg').html('<ul class="messages ajaxcart-messages"><li class="error-msg"><ul><li><span>'+message+'</span></li></ul></li></ul>');
                _this.showPostStep();
            });
        }
    },

    addSubmitEvent: function () {

        if(typeof productAddToCartForm != 'undefined') {
            var _this = this;
            productAddToCartForm.submit = function(url){
                if(this.validator && this.validator.validate()){
                    _this.ajaxCartSubmit(this);
                   // alert('0');
                }
               // alert('1');
                return false;
            }
      
            productAddToCartForm.form.onsubmit = function() {
               // productAddToCartForm.submit();
                // alert('2');
                return false;
            };
        }
    },
  refreshFunction: function(){
          //alert('dsds');
            jQuery(document).ready(function() {
      ajaxcart.initialize();
        jQuery("#various1").fancybox({
            'titlePosition': 'inside',
            'transitionIn': 'none',
            'transitionOut': 'none'
        });



    });
    //alert('dsds');
        },
    updateBlocks: function(blocks) {
        var _this = this;

           if (blocks) {
            try {
                blocks.each(function(block) {
                    if (block.name) {
                        if(block.name=='ajaxall-wishlist'){
                            var eleId = block.name;
                            if($("ajaxall-wishlist")) {
                               $("ajaxall-wishlist").replace(block.html);                                                          
                            }else{
                                $$(".col-left").each(function(element){
                                     $(element).insert(block.html);
                                 });  
                            }
                        }else if(block.name=='toplinks'){
                            $$(".quick-access >ul.links").each(function(element){
                                     $(element).replace(block.html);
                                 });                                                    
                        }else if(block.name=='ajaxwishlist_compare'){
                            var eleId = 'quick-compare';
                            if($(eleId)) {
                                $(eleId).replace(block.html);
                            }
                        }else{                       
                 //start specific  id blocks
                            //wish list top link
                            if (block.name == 'allajax-wishlist-links'){                                
                               wishlistToplinks(block);
                               return;
                            }
                            
                            var dom_selector = block.name;
                            if ($$(dom_selector)) {
                                $$(dom_selector).each(function(e) {
                                    $(e).replace(block.html);
                                });
                            }
                            if(block.name) {
                                var eleId = block.name;
                                if($(eleId)) {                                    
                                    $(eleId).update(block.html);
                                }
                                else if ($$(eleId)) 
                                {
                                    $$(eleId).each(function(e) {
                                        $(e).replace(block.html);
                                    });
                                }
                            }
                            
                           if (block.name == 'allajax-cart'){                                
                                ajaxcart.popupReload();                              
                           }
                        }
                        
                         if (block.name == 'my-wishlist'){   
                        ajaxcart.refreshFunction();
                         }
                         
                          if (block.name == 'ajaxwishlist_compare'){   
                         ajaxcart.compareDropDown();
                         }
                         
                    }
                });
                _this.bindEvents();

                // show details tooltip
                truncateOptions();
            } catch(e) {
                console.log(e);
            }
        }
        multiWishlistReload();

    },
    
    showPopup: function(block) {
        try {
            var _this = this;
        document.getElementById("allajax-configurations-append").innerHTML = block;
        jQuery('.allajax-loading').css('display','none');
        //jQuery('.allajax-configurations').html(block);
        jQuery('.allajax-configurations').css('display','block');
         _this.extractScripts(block);
         _this.bindEvents();
        var currentId = jQuery('#ajax-loading-lightbx-pro-price-box span').attr('id'); 
        jQuery('#ajax-loading-lightbx-pro-price-box span').attr('id',currentId+'_clone'); 
        } catch(e) {
            console.log(e)
        }
    },
      showPopupForWishlist: function(block,itemid) {
        try {
            var _this = this;
            newblock = block.replace("checkout/cart/add","allajax/wishlist/cart/item/"+itemid+""); 
            document.getElementById("allajax-configurations-append").innerHTML = newblock;
            jQuery('.allajax-loading').css('display', 'none');
            //jQuery('.allajax-configurations').html(block);
            jQuery('.allajax-configurations').css('display', 'block');
            _this.extractScripts(block);
            _this.bindEvents();
            //  jQuery('.ajax-loading-lightbx-pro-price-box').removeAttr('id');
            var currentId = jQuery('#ajax-loading-lightbx-pro-price-box span').attr('id');
            jQuery('#ajax-loading-lightbx-pro-price-box span').attr('id', currentId + '_clone');
     
            //set wishlist quantity
            var qtyId = 'qty['+itemid+']';
            if ( jQuery("#wishlist-view-form input[name='"+qtyId+"']")) {
                var qtyAmount = jQuery("#wishlist-view-form input[name='"+qtyId+"']").val();
                if (typeof qtyAmount != 'undefined' && qtyAmount > 0 && jQuery('#product_addtocart_form input[name="qty"]')){                    
                    jQuery('#product_addtocart_form input[name="qty"]').val(qtyAmount); 
                }
            }
                       
             
          } catch (e) {
            console.log(e)
        }
    },
    
    extractScripts: function(strings) {
        var scripts = strings.extractScripts();
        scripts.each(function(script){
            try {
                jQuery.globalEval(script.replace(/var /gi, ""));
            }
            catch(e){
                console.log(e);
            }
        });
    },
    
    
    showWaitingPopup: function(id){
        var pro_image = jQuery('.allajax-imagesfetch-'+id).attr('src');
        var pro_name = jQuery('.allajax-namefetch-'+id+' a').text();
        var pro_url = jQuery('.allajax-namefetch-'+id+' a').attr('href');
        var pro_review = jQuery('.allajax-productdatafetch-'+id+' .ratings').html();
        var pro_price = jQuery('.allajax-productdatafetch-'+id+' .price-box').html();
        
        // Append Selected Product Data to Light Box
        jQuery('#ajax-loading-lightbx-pro-image').attr('href',pro_url);
        jQuery('#ajax-loading-lightbx-pro-image').attr('title',pro_name);
        jQuery('#ajax-loading-lightbx-pro-image img').attr('src',pro_image);
        jQuery('#ajax-loading-lightbx-pro-image img').attr('alt',pro_name);
        jQuery('#ajax-loading-lightbx-pro-rating').html(pro_review);
        jQuery('#ajax-loading-lightbx-pro-price-box').html(pro_price);
        jQuery('#ajax-loading-lightbx-pro-name a').attr('href',pro_url);
        jQuery('#ajax-loading-lightbx-pro-name a').attr('title',pro_name);
        jQuery('#ajax-loading-lightbx-pro-name a').text(pro_name);     
        
        jQuery('#ajax-loading-lightbx-pro-image-respond').attr('href',pro_url);
        jQuery('#ajax-loading-lightbx-pro-image-respond').attr('title',pro_name);
        jQuery('#ajax-loading-lightbx-pro-image-respond img').attr('src',pro_image);
        jQuery('#ajax-loading-lightbx-pro-image-respond img').attr('alt',pro_name);
               
        jQuery('#various1').trigger('click');
    },
      checkConfigurableRedirect: function(){
       var pathname = window.location.pathname;
       if (pathname.search('checkout/cart/configure') != -1) {
           var res = pathname.split('checkout');
            window.location.replace(res[0]+"checkout/cart/");
       }
    },
    showWaitingPopupProductView: function(){
        var pro_image = jQuery('.product-image #wrap a img').attr('src');
        var pro_name = jQuery('.product-shop .product-name h1').text();
        //var pro_url = jQuery('.allajax-namefetch-'+id+' a').attr('href');
        var pro_review = jQuery('.product-shop .ratings').html();
        var pro_price = jQuery('.product-shop .product-type-data .price-box').html();
        
        // Append Selected Product Data to Light Box
        jQuery('#ajax-loading-lightbx-pro-image').attr('onClick','return false');
        jQuery('#ajax-loading-lightbx-pro-image').css('cursor','default');
        jQuery('#ajax-loading-lightbx-pro-image').attr('title',pro_name);
        jQuery('#ajax-loading-lightbx-pro-image img').attr('src',pro_image);
        jQuery('#ajax-loading-lightbx-pro-image img').attr('alt',pro_name);
        jQuery('#ajax-loading-lightbx-pro-rating').html(pro_review);
        jQuery('#ajax-loading-lightbx-pro-price-box').html(pro_price);
        jQuery('#ajax-loading-lightbx-pro-name a').attr('onClick','return false');
        jQuery('#allajax-prestep .rating-links #goto-reviews').attr('onClick','return false');
        jQuery('#ajax-loading-lightbx-pro-name a').css('cursor','default');
        jQuery('#allajax-prestep .rating-links #goto-reviews').css('cursor','default');
        jQuery('#ajax-loading-lightbx-pro-name a').attr('title',pro_name);
        jQuery('#ajax-loading-lightbx-pro-name a').text(pro_name);     
        
        //jQuery('#ajax-loading-lightbx-pro-image-respond').attr('href',pro_url);
        jQuery('#ajax-loading-lightbx-pro-image-respond').attr('title',pro_name);
        jQuery('#ajax-loading-lightbx-pro-image-respond img').attr('src',pro_image);
        jQuery('#ajax-loading-lightbx-pro-image-respond img').attr('alt',pro_name);
               
        jQuery('#various1').trigger('click');
    },
    
    
    showPreStep: function(){
        jQuery('#allajax-prestep').css('display','block');
        jQuery('#allajax-poststeps').css('display','none');
        jQuery('.allajax-configurations').html('');
        jQuery('.allajax-loading').css('display','block');
    },
    
    showPostStep: function(){
        jQuery('#allajax-prestep').css('display','none');
        jQuery('#allajax-poststeps').css('display','block');
        jQuery('.allajax-configurations').html('');
    },
    showConfigurableLoadingPopup: function(form){
      
       var form1 = new VarienForm('product_addtocart_form');

       if(form1.validator && form1.validator.validate()){
                   ajaxcart.ajaxCartSubmit(form1);
                   ajaxcart.showPreStep();
                   ajaxcart.showWaitingPopupProductView();
                   ajaxcart.checkConfigurableRedirect();
         }
       
    },
    
    showProductViewWishlistPopup: function(url, id) {

            url = url.replace("wishlist/index","allajax/wishlist");
            url = url.replace("catalog/product_compare/add","allajax/wishlist/compare");
            ajaxcart.ajaxCartSubmit(url, id);
            ajaxcart.showPreStep();
            ajaxcart.showWaitingPopupProductView();
  

    },
    
    showProductViewComparePopup: function(url, id) {

            url = url.replace("catalog/product_compare/add","allajax/wishlist/compare");
            ajaxcart.ajaxCartSubmit(url, id);
            ajaxcart.showPreStep();
            ajaxcart.showWaitingPopupProductView();
  

    },
    
     popupReload: function(){
        jQuery(document).ready(function() {

    jQuery("#various1").fancybox({
        'titlePosition': 'inside',
        'transitionIn': 'none',
        'transitionOut': 'none'
    });



});
    }

};

var oldSetLocation = setLocation;
var setLocation = (function() {
     return function(url, id) {
        if (url.search('checkout/cart/add') != -1) {
            //its simple/group/downloadable product
            ajaxcart.showPreStep();
            jQuery('#allajax-poststeps .actions .button-view').css('display','inline');
            ajaxcart.showWaitingPopup(id);
            ajaxcart.ajaxCartSubmit(url, id);
        } else if (url.search('checkout/cart/delete') != -1) {
            ajaxcart.showPreStep();
            ajaxcart.showWaitingPopup(id);
            ajaxcart.ajaxCartSubmit(url, id);
        } else if (url.search('options=cart') != -1) {
            //its configurable/bundle product
            ajaxcart.showPreStep();
            jQuery('#allajax-poststeps .actions .button-view').css('display','inline');
            ajaxcart.showWaitingPopup(id);
            url += '&ajax=true';
            ajaxcart.getConfigurableOptions(url, id);
        } else if(url.search('wishlist/index/add') != -1){
            
            url = url.replace("wishlist/index","allajax/wishlist");
            ajaxcart.showPreStep();
            jQuery('#allajax-poststeps .actions .button-view').css('display','none');
            ajaxcart.showWaitingPopup(id);  
            ajaxcart.ajaxCartSubmit(url, id);
            
        } else if(url.search('product_compare/add') != -1){
            
            url = url.replace("catalog/product_compare/add","allajax/wishlist/compare");
            ajaxcart.showPreStep();
            jQuery('#allajax-poststeps .actions .button-view').css('display','none');
            ajaxcart.showWaitingPopup(id);   
            ajaxcart.ajaxCartSubmit(url, id);
            //ajaxcart.updateDomFunctions();
            
        } else if(url.search('product_compare/remove') != -1){
            
            url = url.replace("catalog/product_compare/remove","allajax/wishlist/removecompare");
            //alert(url);
            ajaxcart.setLoadStart('mini-cart');
            ajaxcart.generalAjaxRequests(url,'mini-cart');
            //ajaxcart.updateDomFunctions();

        } else if(url.search('product_compare/clear') != -1){
            
            url = url.replace("catalog/product_compare/clear","allajax/wishlist/clearcompare");
                      
            ajaxcart.setLoadStart('mini-cart');
            ajaxcart.generalAjaxRequests(url,'mini-cart');
            //ajaxcart.updateDomFunctions();
            
        }else if(url.search('wishlist/index/remove') != -1){
            
            url = url.replace("wishlist/index/remove","allajax/wishlist/removewishlist");
            //alert(url);
            ajaxcart.setLoadStart('wishlist');
            ajaxcart.generalAjaxRequests(url,'wishlist');

        }else if(url.search('wishlist/index/cart/item') != -1){
            //alert('dsdsds');
            itemid = ajaxcart.getParams(url,'item');
            url_check_product = url.replace("wishlist/index/cart","allajax/wishlist/checkoptions");
            url = url.replace("wishlist/index/cart","allajax/wishlist/cart");
            ajaxcart.showPreStep();
            jQuery('#allajax-poststeps .actions .button-view').css('display','inline');
            ajaxcart.showWaitingPopup(itemid);
            ajaxcart.checkproductlAjaxRequests(url_check_product,id,url,itemid);
                
        }else {
            oldSetLocation(url);
        }
    };
})();

setPLocation = setLocation;

document.observe("dom:loaded", function() {
    ajaxcart.initialize();
});

jQuery(document).ready(function() {

    jQuery("#various1").fancybox({
        'titlePosition': 'inside',
        'transitionIn': 'none',
        'transitionOut': 'none'
    });



});


// Shopping Cart Page AJAX Handle Class

if(typeof Balance=='undefined') {
    var Balance = {};
}
//create ajax class
Balance.Allajax = Class.create();
//create ajax class method
Balance.Allajax.prototype = {
	
	initialize: function(){            
            this.onComplete = this.loadComplete.bindAsEventListener(this);
            this.onSuccess  = this.process.bindAsEventListener(this); 
            this.loadinStatus = false;
	},	
        loadComplete:function (){
            this.setLoadStop();
        },
        setLoadStart: function(eleId){                                   
            var iconId = 'ajax-please-wait';
            if (typeof eleId !=='undefined')
            {
                iconId = eleId+'-ajax-please-wait';
				  if (!$(iconId))
                {
                    iconId = eleId;
                }  
            }
            if (!this.loadinStatus) {
                Element.show(iconId);
                this.loadinStatus = true;
                return true;
            }    
            return false;    
        },
        setLoadStop: function(eleId){
            var iconId = 'ajax-please-wait';
            if (typeof eleId !=='undefined')
            {
                iconId = eleId+'-ajax-please-wait';
				if (!$(iconId))
                {
                    iconId = eleId;
                }   
            }
            this.loadinStatus = false;
            Element.hide(iconId);
        }   
       , 
       process: function(transport)
       {
            if (transport && transport.responseText){
                try{
                    response = eval('(' + transport.responseText + ')');
                }
                catch (e) {
                    response = {};
                }
            }
           
        
            if (response.blocks){                   
                for(var i = 0; i < response.blocks.length; i++)
                {                    
                   if (response.blocks[i].name == 'allajax-wishlist-links'){
                       wishlistToplinks(response.blocks[i]);
                   } 
                   else if(jQuery('#'+response.blocks[i].name)){
                        jQuery('#'+response.blocks[i].name).html(response.blocks[i].html);                        
                   }
                   
                    if (response.blocks[i].name == 'allajax-cart'||response.blocks[i].name == 'my-wishlist'){                                
                        ajaxcart.popupReload();                              
                    }
                    
                    if(response.blocks[i].name == "ajaxall-wishlist"  &&  $("ajaxall-wishlist")) {
                        $("ajaxall-wishlist").replace(response.blocks[i].html);                                                                                    
                    }
                 }
             }
             multiWishlistReload();
               jQuery('.change').click(function(){
                 jQuery(this).next().toggle();
                 return false;
             });
             
        },                
        removeItem: function(url,handle){           
            if (!this.setLoadStart(handle)){
                return false;
            }
            var _this = this;
            var request = new Ajax.Request(
               url,
                {   
                    method: 'post',
                    onComplete: function(){ _this.setLoadStop(handle);},
                    onSuccess: this.onSuccess,
                    parameters: {"handle":handle}
                }
            );
        },
        updateCart: function(element){            
            if (!this.setLoadStart()){
                return false;
            }
            var form = element.form;
            var request = new Ajax.Request(
               form.action,
                {   
                    method: 'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSuccess,
                    parameters: Form.serialize(form)+'&'+element.name+'='+element.value
                }
            );
        },
        estimatePost: function(formObj){
            if (!formObj.validator.validate()) {
                 return false;
            }
            if (!this.setLoadStart()){
                return false;
            }
            var form = formObj.form;
           
            var request = new Ajax.Request(
               form.action,
                {   
                    method: 'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSuccess,
                    parameters: Form.serialize(form)
                }
            );
            
        },
        couponPost: function(formObj){
            if (!formObj.validator.validate()) {
                 return false;
            }
            if (!this.setLoadStart()){
                return false;
            }
            var form = formObj.form;
           
            var request = new Ajax.Request(
               form.action,
                {   
                    method: 'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSuccess,
                    parameters: Form.serialize(form)
                }
            );
            
        },
wishlistPost: function(element,url){
            var form = element.form;
            var validator = new Validation(form);
            if (!validator.validate()) {
                 return false;
            }
            if (!this.setLoadStart()){
                return false;
            }              
            var request = new Ajax.Request(
               url,
                {   
                    method: 'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSuccess,
                    parameters: Form.serialize(form)+'&'+element.name+'='+element.value
                }
            );
            
        },
        giftcardPost: function(formObj,url){
            if (!formObj.validator.validate()) {
                 return false;
            }
            if (!this.setLoadStart('gc-please-wait')){
                return false;
            }
            var form = formObj.form;
            var _this = this;
            var request = new Ajax.Request(
               url,
                {   
                    method: 'post',
                    onComplete: function(){_this.setLoadStop('gc-please-wait');},
                    onSuccess: this.onSuccess,
                    parameters: Form.serialize(form)
                }
            );
            return false;
        },
        giftcardRemove: function(url){
            if (!this.setLoadStart()){
                return false;
            }
            var request = new Ajax.Request(
               url,
                {   
                    method: 'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSuccess,                    
                }
            );
            
        },
};

function wishlistToplinks(block){
    var ele = $(block.name).up();
    var className = ele.className;
    ele.replace(block.html);
    ele.addClassName(className);
    return;
}

var allajax = new Balance.Allajax();