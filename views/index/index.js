document.addEventListener('DOMContentLoaded', function() {
    // Load JS for MaterializeCSS
    M.Modal.init(document.querySelectorAll('.modal'), {dismissible: true});

    // Email Login Form
    formHandler(document.querySelector('form#auth-signin-email'), '/auth/email/request', true);

    // Invite Form
    const inviteCode = new URLSearchParams(window.location.search).get('invite');
    const authRegisterInvite = document.querySelector('form#auth-register-invite');
    if (inviteCode) {
        document.querySelector('input#invite_code').value = inviteCode;
        M.Modal.getInstance(authRegisterInvite).open();
    }
    formHandler(authRegisterInvite, '/auth/invite', true);

    // License Form
    // formHandler(document.querySelector('form#auth-register-license'), '/auth/license', true);
});
