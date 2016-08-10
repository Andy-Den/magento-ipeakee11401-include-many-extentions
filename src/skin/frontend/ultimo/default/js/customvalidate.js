Validation.addAllThese([
['validate-select', 'Please select an option.', function(v, elm) {
				var ret = ((v != "none") && (v != null) && (v.length != 0));
				if(!ret)
				{
					jQuery(elm).not(".no-uniform").parent(".selector").addClass("selector-validation-failed");
				}
                return ret;
            }],
]);