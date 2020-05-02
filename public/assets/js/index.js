document.addEventListener('DOMContentLoaded', function() {
    M.Modal.init(document.querySelectorAll('.modal'), {dismissible: true});

    const formHandler = (form, endpoint, captchaRequired = false) => {
        form.addEventListener('submit', e => {
            e.preventDefault();
            
            formSubmit(form, endpoint, captchaRequired)
        });    
    }

    const emailCode = new URLSearchParams(window.location.search).get('email');
    if (emailCode) {
        document.querySelector('input#email').value = emailCode;
        M.updateTextFields();
        M.Modal.getInstance(document.querySelector('form#auth-signin-email')).open();
    }
    formHandler(document.querySelector('form#auth-signin-email'), '/auth/email/request', true);

    const inviteCode = new URLSearchParams(window.location.search).get('invite');
    if (inviteCode) {
        document.querySelector('input#invite_code').value = inviteCode;
        M.updateTextFields();
        M.Modal.getInstance(document.querySelector('form#auth-register-invite')).open();
    }
    formHandler(document.querySelector('form#auth-register-invite'), '/auth/invite', true);

    const licenseCode = new URLSearchParams(window.location.search).get('license');
    if (licenseCode) {
        document.querySelector('input#license_code').value = licenseCode;
        M.updateTextFields();
        M.Modal.getInstance(document.querySelector('form#auth-register-license')).open();
    }
    formHandler(document.querySelector('form#auth-register-license'), '/auth/license', true);
});
