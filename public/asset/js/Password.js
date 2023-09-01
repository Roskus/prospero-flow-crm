$(".toggle-password").click(function() {
    $(this).toggleClass("la-eye la-eye-slash");
    let input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});

$( "#password" ).on( "change", function() {
    $(this).removeClass('is-invalid');
} );
