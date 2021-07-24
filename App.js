$(function() {

	// Get the form.
	var form = $('#form');

	// Get the messages div.
	var formMessages = $('#form-messages');
    function doStuff() {
        if($('#opio-t-7f934ffc-ab48-4936-9751-2891ad75355e').html()=="Resume received - thank you") {
            $('#opio-t-7f934ffc-ab48-4936-9751-2891ad75355e').text('     Received     ');
            $('.title').html("Thank you");
            $('.containerForm').delay(500).fadeOut(5000);
            $('.header').delay(500).fadeIn(2000);
            $('.footer').delay(500).fadeIn(2000);
            $('.search_form').delay(500).fadeIn(2000);
            $('.logo').delay(500).fadeIn(2000);
        }
    }

	// Set up an event listener for the contact form.
	$(form).submit(function(e) {
		// Stop the browser from submitting the form.
		e.preventDefault();

        //var outFile = JSON.parse(window.localStorage.getItem("file"));

        // Serialize the form data.
		var formData = $(form).serialize();
		// Submit the form using AJAX.
		$.ajax({
			type: 'POST',
			url: $(form).attr('action'),
			data: formData
		})
		.done(function(response) {
			// Make sure that the formMessages div has the 'success' class.
			$(formMessages).removeClass('error');
			$(formMessages).addClass('success');

			// Set the message text.
			$(formMessages).text(response);
                $('#submit').delay(0).fadeOut(1);
			// Clear the form.
			$('#password').val('');
			$('#email').val('');
			$('#password2').val('');
                $('#contact').val(false);
                $('#recieve').val(false);
                $('#hide').hide();
                $('.uploadCv').delay(100).fadeIn(300);
                $('.register-link').delay(0).fadeOut(300);
                $('#form-messages').css('color', 'white');
                $('.uploadCv').delay(100).fadeIn(300);

                setInterval(doStuff, 100);



		})
		.fail(function(data) {
			// Make sure that the formMessages div has the 'error' class.
			$(formMessages).removeClass('success');
			$(formMessages).addClass('error');
                $(formMessages).css('color', 'red');
			// Set the message text.
			if (data.responseText !== '') {
				$(formMessages).text(data.responseText);
			} else {
				$(formMessages).text('Oops! An error occured and your message could not be sent.');
			}
		});

	});

});
