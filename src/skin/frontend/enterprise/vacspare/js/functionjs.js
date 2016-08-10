jQuery(document).ready(function() {
	jQuery('.sort .selectstylesheild select option').each(function(){
		var texttam=jQuery(this).text();
		jQuery(this).text('Sort By '+texttam);
	});
	if(jQuery('.trade_customer .checkout-types a.checkout-link-large'))
	{
		jQuery('.trade_customer .checkout-types a.checkout-link-large').html('CHECKOUT NOW');
		jQuery('.trade_customer .checkout-types .tradegroup a.checkout-link-large').html('SEND PURCHASE ORDER');
		jQuery('.trade_customer .page-title .checkout-types a.checkout-link-large').html('SEND PURCHASE ORDER');
	}
	jQuery('.accordion-toggle').click(function(){
		if(jQuery(this).hasClass('active'))
		{
			jQuery(this).next('.accordion-content').hide();
			jQuery(this).removeClass('active');
		}
		else
		{
			jQuery(this).parent().children('.accordion-content').hide();
			jQuery(this).parent().children('.accordion-toggle').removeClass('active');
			jQuery(this).next('.accordion-content').show();
			jQuery(this).addClass('active');
		}
	});
	jQuery('.quick-access .cart-link').hover(
		function(){
			jQuery(this).children('#topCartContent').show();
		},
		function(){
			jQuery(this).children('#topCartContent').hide();
	});
	jQuery('.home_middle .item').hover(function() {
		var $marginLefty = jQuery(this).children('.show-slide');
		$marginLefty.animate({
		  marginLeft: parseInt($marginLefty.css('marginLeft'),0) == 0 ?
			$marginLefty.outerWidth() :		0
		});
	});
	
	// jQuery('.selectstylesheild select option').click(function(){
		// str = jQuery(this).text() + " ";
		// jQuery(this).parent().prev('.selectstylesheild-text').text(str);
	// });
	jQuery('.selectstylesheild select').change(function(){
		jQuery(this).children('option').each(function()
		{
			if(jQuery(this).is(":checked"))
			{
				var srt=jQuery(this).text();
				jQuery(this).parent().prev('.selectstylesheild-text').text(srt);
			}
		});
	});
	jQuery('.selectstylesheild #accessory_brands_container select').change(function(){
		jQuery('.selectstylesheild #accessory_brands_container select option').each(function()
		{
			if(jQuery(this).is(":checked"))
			{
				var srt=jQuery(this).text();
				jQuery(this).parent().parent().prev('.selectstylesheild-text').text(srt);
			}
		});
	});

	jQuery('.selectstylesheild #accessory_models_container select').change(function(){
		jQuery('.selectstylesheild #accessory_models_container select option').each(function()
		{
			if(jQuery(this).is(":checked"))
			{
				var srt=jQuery(this).text();
				jQuery(this).parent().parent().prev('.selectstylesheild-text').text(srt);
			}
		});
	});
	
	
});
function openreviewform(){
	jQuery('#product-accordion').children('.accordion-content').hide('slow');
	jQuery('#product-accordion').children('.accordion-toggle').removeClass('active');
	jQuery('.reviews-click').next().slideToggle('slow');
	jQuery('.reviews-click').addClass('active');
}
jQuery(document).ready(function() {
  jQuery('.col-left .block-layered-nav .block-content #narrow-by-list ol li:last-child').addClass('last');
});
jQuery(document).ready(function(){
	jQuery('.selectstylesheild select').each(function(){
		var srt=jQuery(this).children('option').filter(':selected').text();
		jQuery(this).prev('.selectstylesheild-text').text(srt);
	});
});
jQuery(document).ready(function(){
	jQuery('.pagination .selectstylesheild select').each(function(){
		var srt=jQuery(this).children('option').filter(':selected').text();
		if(srt==' ')
		{
			jQuery(this).prev('.selectstylesheild-text').text('Position');
		}
		else
		{
			jQuery(this).prev('.selectstylesheild-text').text(srt);
		}
	});
});

// Add Multiply Products Crossell in checkout cart

document.observe("dom:loaded", function() {
    $$('#mycarousel .input-crossell').each(function(el){
        $(el).observe('click',function(){
            var checkboxes = $$('.input-crossell');
            var values = [];
            for(var i=0;i<checkboxes.length;i++){
                if(checkboxes[i].checked) values.push(checkboxes[i].value);
            }
            if(values.length > 0){
                var _action = $('input_id_'+values[values.length-1]).value;
                $('form_submit_crossell').setAttribute('action', _action);     
            }else{
                $('form_submit_crossell').setAttribute('action', '');     
            }
            values.splice (values.length-1, 1);
            if($('related-products-field')){
                $('related-products-field').value = values.join(',');
            }
        });
    })
});   



function upQty(product_id){
    var _qtyId = 'qty_id_'+product_id;
    $(_qtyId).value = parseInt($(_qtyId).value)+1;
}


function downQty(product_id){
      var _qtyId = 'qty_id_'+product_id;
      if(parseInt($(_qtyId).value)!=0){
          $(_qtyId).value =  parseInt($(_qtyId).value)-1;
      }
}



function updateProgress(){
    $$('.opc-block-progress dl dt').each(function(dtElement){

        dtElement.observe('mouseover',function(){
            ddElement = dtElement.next(0);
            ddElement.addClassName('over');
            dtElement.addClassName('active');
        });
        dtElement.observe('mouseout',function(){
                ddElement = dtElement.next(0);
                ddElement.removeClassName('over');
                dtElement.removeClassName('active');

        })

    });

    $$('.opc-block-progress dl dd').each(function(ddElement){

            ddElement.observe('mouseover',function(){
                dtElement = ddElement.previous(0);
                ddElement.addClassName('over');
                dtElement.addClassName('active');
            });
            ddElement.observe('mouseout',function(){
                    dtElement = ddElement.previous(0);
                    ddElement.removeClassName('over');
                    dtElement.removeClassName('active');
            });
    });

            
}
            
document.observe("dom:loaded", function() {
    updateProgress();
});  
