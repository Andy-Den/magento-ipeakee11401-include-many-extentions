(function($) {

    $(document).ready( function() {

        function IsEmail(email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }

        var form = ".discount-form ";

        $(form+".radio label").each( function() {
            $(this).click( function() {
                $(form+".radio input[type='radio']").removeAttr("checked");
                $(this).closest("span").find("input[type='radio']").attr("checked", "checked");
            });
        });

        $(form+"input[type='submit']").click( function (e) {
            $(".field_error").empty();
            $(form+"input[type='text']").each( function() {
                var name = $(this).attr("name");
                $(this).removeClass("validation-failed");
                if($(this).val() === '') {
                    e.preventDefault();
                    $(this).addClass("validation-failed").closest(".field_block").find(".field_error").html("<p class='validation-advice'>Please enter a value</p>");
                } else if (name === "80589") {
                    if(IsEmail($(form+" input[name='80589']").val()) === false) {
                        e.preventDefault();
                        $(form+" input[name='80589']").addClass("validation-failed").closest(".field_block").find(".field_error").html("<p class='validation-advice'>Please enter a valid email address</p>");
                    }
                } else if (name === "80634") {
                    if(IsEmail($(form+" input[name='80634']").val()) === false) {
                        e.preventDefault();
                        $(form+" input[name='80634']").addClass("validation-failed").closest(".field_block").find(".field_error").html("<p class='validation-advice'>Please enter a valid email address</p>");
                    }
                }
            });
        });

    });

})(jQuery);
