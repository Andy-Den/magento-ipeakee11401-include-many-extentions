	$jq = jQuery.noConflict();
	
	var partsfinder = (function () {
		function getFilterModel() {
			$jq.ajax({
			    url: '/partsfinder/ajax/filtermodel',
			    data: {
			    	filter_brand: $jq('#filter_brand').val()
			    },
			    success: function(response) {
				    var options = [];
			        if (response) {
			        	var obj = $jq.parseJSON(response);
						for(var i =0;i < obj.length;i++)
						{
						  options.push('<option value="'+obj[i].id+'">'+obj[i].name+'</option>');
						}
						$jq('#filter_model') .html(options.join(''));
						if (2==options.length){
							$jq('#filter_model option').first().attr('selected', 'selected');
						}
			        }
			        $jq('#model_ajax_loading').hide();
			        $jq('#model_wrapper').show();
			    }
			}); 
		}
		
		
		function setListeners() {

			$jq("#filter_brand").change(function(){
				$jq('#model_ajax_loading').show();
				$jq('#model_wrapper').hide();
				$jq("#filter_model").empty();
				var srt = '';
				$jq('#filter_brand option:checked').each(function(i,e){if ($jq(e).attr('value')) {srt = $jq(e).text();}});
				$jq('#brand_span').text(srt);
				getFilterModel();
			});

			$jq("#filter_model").change(function(){
				var srt = '';
				$jq('#filter_model option:checked').each(function(i,e){if ($jq(e).attr('value')) {srt = $jq(e).text();}});
				$jq('#model_span').text(srt);
			});

			$jq("#filter_result_show").click(function(){
				var brand_id = $jq('#filter_brand').attr("value");
				var model_id = $jq('#filter_model').attr("value");
				if (0 != brand_id) {
					console.log(brand_id + model_id);
					//$jq.cookie("brand_id", brand_id, { expires: 7 });
					//$jq.cookie("model_id", model_id, { expires: 7 });
					$jq('#partsfinder_form').submit();
				}
			});
			
			$jq("#filter_vechile_clear").click(function(){
				//$jq.removeCookie('brand_id');
				//$jq.removeCookie('model_id');
				$jq("#filter_model").empty();
			});				
			
		}

		function initFilters() {		
			setListeners();
		}		
	
	    var run = {};
	    run.getFilterModel = getFilterModel;	
	    run.initFilters = initFilters;
	    run.setListeners = setListeners;    
	    return run;
	}());
	
	