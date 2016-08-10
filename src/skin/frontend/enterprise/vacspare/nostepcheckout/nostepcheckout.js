jQuery(document).ready(function(){    
    AjaxNoStepCheckout = function(url) {
        if(!url) return false;  
        

            jQuery.fancybox.open({
                href : '#nostepcheckout-dialog',
                padding: 22,
                helpers: {
                    overlay : {
                        closeClick : false
                    }
                },
                afterLoad:function(){
                    fanbox = this;
                    window._nostepcheckout_Loading();  
                    jQuery.get( url ,
                        function(data){
                            window._nostepcheckout_LoadJsonData(data);     
                            jQuery.fancybox.update();
                        }, "json");       
                },
                afterClose: function() {
                     window._nostepcheckout_CloseDialog();
                }
            //title: "title"
            });



        

        return true;
    }
    
    
    if (typeof productNoStepCheckoutForm == 'object'){
        productNoStepCheckoutForm.submit = function(){
            if (this.validator.validate()) {
                if(jQuery("#product_nostepcheckout_form .share-option-selector").length > 0) {
                    if(!productAddToCartForm.validator.validate()) {
                        return false;
                    }
                }
                _form = jQuery("#product_nostepcheckout_form");
                if(_form) {
                    _form_vars =  _form.serialize();
                    url = _form.attr("action");
                    if(!url) this.form.submit();
                    if(!AjaxNoStepCheckout(url + "?" + _form_vars)) this.form.submit();
                }
            }
        }
    }
    if (typeof cartNoStepCheckoutForm == 'object'){
        cartNoStepCheckoutForm.submit = function(){            
            if (this.validator.validate()) {
                _form = jQuery("#cart_nostepcheckout_form");
                if(_form) {
                    _form_vars =  _form.serialize();
                    url = _form.attr("action");
                    if(!url) this.form.submit();
                    if(!AjaxNoStepCheckout(url + "?" + _form_vars)) this.form.submit();
                }
            }
        }
    }
});

function _nostepcheckout_Loading() {
    jQuery("#nostepcheckout-dialog .nostepcheckout-loader").show();
    jQuery('#nostepcheckout-dialog .nostepcheckout-content').hide();
    jQuery('#nostepcheckout-dialog .nostepcheckout-error').hide();
    return;
}

function _nostepcheckout_AfterLoading(){
    jQuery("#nostepcheckout-dialog .nostepcheckout-loader").hide();    
    //    jQuery( "#nostepcheckout-dialog" ).dialog({
    //        position:"center"
    //    });
    return;
}

function _nostepcheckout_LoadJsonData(data){
    if(data.redirect_url) {
        location.href = data.redirect_url;
        return true;
    } else if(data.error) {
        window._nostepcheckout_AfterLoading(); 
        jQuery('#nostepcheckout-dialog .nostepcheckout-error').show().html(data.error);
        return true;
    }
    if(data.content) {
        window._nostepcheckout_AfterLoading(); 
        jQuery('#nostepcheckout-dialog .nostepcheckout-content').show().html(data.content);
        
    }
    return;
}
function _nostepcheckout_CloseDialog(){
    jQuery('#nostepcheckout-dialog .nostepcheckout-content').html("");    
    jQuery(".ui-dialog .ui-dialog-buttonpane").hide();
    return;
}