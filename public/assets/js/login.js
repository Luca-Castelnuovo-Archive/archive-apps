document.addEventListener('DOMContentLoaded', function() {
    M.Modal.init(document.querySelectorAll('.modal'), {dismissible: true});

    const inviteCode = new URLSearchParams(window.location.search).get('invite');
    if (inviteCode) {
        document.querySelector('input#invite_code').value = inviteCode;
        M.updateTextFields();
        M.Modal.getInstance(document.querySelector('form#register-invite')).open();
    }
});

function inviteSubmit() {
    formSubmit(
        document.querySelector('form#register-invite'),
        '/auth/invite',
        true
    );
}

function loginSubmit() {
    formSubmit(
        document.querySelector('form#signin-email'),
        '/auth/email/request',
        true
    );
}
