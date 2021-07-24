    $(document).ready(function() {
        if (location.href.indexOf('#cv') > -1) {
        $('.containerForm').show();
        var isVisible = $(".containerForm").is(":visible");
        if (isVisible) {
        $('.header').delay(0).fadeOut(1000);
        $('.footer').delay(0).fadeOut(1000);
        $('.search_form').delay(0).fadeOut(1000);
        $('.logo').delay(0).fadeOut(1000);
        $('.logo').delay(0).fadeIn(1000);
        $('#hide').delay(0).fadeIn(1000);
        $('.uploadCv').hide();
        $('#submit').delay(0).fadeIn(1000);
        $('#form-messages').removeClass('success');
        $('#form-messages').text("");
        }
        }
        });

    $(document).ready(function() {
        $( "#cv" ).focus(function() {
            $('.containerForm').show();
            var isVisible = $( ".containerForm" ).is( ":visible" );
            if(isVisible){
                $('.header').delay(0).fadeOut(1000);
                $('.footer').delay(0).fadeOut(1000);
                $('.search_form').delay(0).fadeOut(1000);
                $('.logo').delay(0).fadeOut(1000);
                $('.logo').delay(0).fadeIn(1000);
                $('#hide').delay(0).fadeIn(1000);
                $('.uploadCv').hide();
                $('#submit').delay(0).fadeIn(1000);
                $('#form-messages').removeClass('success');
                $('#form-messages').text("");
            }
        });
        });

    $(document).ready(function() {
        $(window).on('hashchange', function(e){
            if (location.href.indexOf('#cv') > -1) {
                $('.containerForm').show();
                var isVisible = $(".containerForm").is(":visible");
                if (isVisible) {
                    $('.header').delay(0).fadeOut(1000);
                    $('.footer').delay(0).fadeOut(1000);
                    $('.search_form').delay(0).fadeOut(1000);
                    $('.logo').delay(0).fadeOut(1000);
                    $('.logo').delay(0).fadeIn(1000);
                    $('#hide').delay(0).fadeIn(1000);
                    $('.uploadCv').hide();
                    $('#submit').delay(0).fadeIn(1000);
                    $('#form-messages').removeClass('success');
                    $('#form-messages').text("");
                }
            }
        });
        });
    $(document).ready(function() {
        $('.register-link').click(function() {
            $('.containerForm').show();
            var isVisible = $( ".containerForm" ).is( ":visible" );
            if(isVisible){
                $('.header').delay(0).fadeOut(1000);
                $('.footer').delay(0).fadeOut(1000);
                $('.search_form').delay(0).fadeOut(1000);
                $('.logo').delay(0).fadeOut(1000);
                $('.logo').delay(0).fadeIn(1000);
                $('#hide').delay(0).fadeIn(1000);
                $('.uploadCv').hide();
                $('#submit').delay(0).fadeIn(1000);
                $('#form-messages').removeClass('success');
                $('#form-messages').text("");
            }
        });
        });
    $('#x').click(function() {
        $(document).ready(function() {
            $('.header').delay(0).fadeIn(1000);
            $('.footer').delay(0).fadeIn(1000);
            $('.search_form').delay(0).fadeIn(1000);
            $('.containerForm').delay(0).fadeOut(1000);
            $('.logo').delay(0).fadeIn(1000);
        });
        });
