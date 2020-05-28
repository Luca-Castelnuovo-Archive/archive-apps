document.addEventListener('DOMContentLoaded', function() {
    M.Modal.init(document.querySelectorAll('.modal'), {dismissible: true});

    const inviteSubmit = token => {
        document.querySelector('input#register-invite-captcha').value = token;

        formSubmit(
            document.querySelector('form#register-invite'),
            '/auth/invite',
            true
        );
    }

    const loginSubmit = token => {
        document.querySelector('input#signin-email-captcha').value = token;

        formSubmit(
            document.querySelector('form#signin-email'),
            '/auth/email/request',
            true
        );
    }
});
