var signUpAccountExists = false;

$(function() {
	$('#signin').button().click();
	
	$('#join').button().click();

	$("#account_type").selectable({
		selected : function(event, ui) {
			if (ui.selected.value == 1) {
				$('#acct-type').val('st');
			} else if (ui.selected.value == 2) {
				$('#acct-type').val('tu');
			}
		}
	});

	$('#signup').submit(
			function(event, ui) {
				var pswd = $('#passwd').val();
				var email = $('#email').val();

				$('#password-form-error').hide();
				$('#email-form-error').hide();

				if (pswd.length < 8) {
					$('#password-form-error').show().fadeOut(1500);
					$('#password-form-error').html(
							'Password must be at least 8 characters');

					$('#passwd').focus();
					return false;
				}

				// validate letter
				if (!pswd.match(/[A-z]/)) {
					$('#password-form-error').show().fadeOut(1500);
					$('#password-form-error').html(
							'Password must contain at least 1 letter');

					$('#passwd').focus();
					return false;
				}

				// validate capital letter
				if (!pswd.match(/[A-Z]/)) {
					$('#password-form-error').show().fadeOut(1500);
					$('#password-form-error').html(
							'Password must contain at least 1 capital letter');

					$('#passwd').focus();
					return false;
				}

				// validate number
				if (!pswd.match(/\d/)) {
					$('#password-form-error').show().fadeOut(1500);
					$('#password-form-error').html(
							'Password must contain at least 1 number');

					$('#passwd').focus();
					return false;
				}

				// validate email domain
				if (!checkDomain(email, 'utoronto.ca')) {
					$('#email-form-error').show().fadeOut(1500);
					$('#email-form-error').html(
							'Enter a valid University of Toronto email');

					$('#email').focus();
					return false;
				}

				xajax_accountExists(email);

				if (signUpAccountExists) {
					$('#email-form-error').show().fadeOut(1500);
					$('#email-form-error').html('Email already registered!!');

					$('#email').focus();
					return false;
				}
				
				xajax_signUp(xajax.getFormValues('signup'));

				return false;
			});
	
	$('#login').submit(
			function(event, ui) {
				xajax_logIn(xajax.getFormValues('login'));
				
				return false;
			});

})

function checkDomain(email, domain) {
	var emaildomain = email.substring(email.indexOf('@') + 1, email.length);

	return (emaildomain === domain);
}