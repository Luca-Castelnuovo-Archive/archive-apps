document.addEventListener('DOMContentLoaded', function() {
    // Load JS for MaterializeCSS
    M.Modal.init(document.querySelectorAll('.modal'), {dismissible: true});

    // Email Login Form
    formHandler(document.querySelector('form#auth-signin-email'), '/auth/email/request', async (success) => {
        console.log(success);
        return;

        M.toast({html: 'Request Success'}); // TODO: display the success message
                
        M.Modal.getInstance(elem).close();
        await delay(750);
        reload();
    }, async (error) => {
        console.log(error);
        return;

        M.toast({html: 'Request Failed'}); // TODO: display the error message
                
        console.error(error);
        await delay(750);
        reload();
    }, true);

    // Invite Form
    formHandler(document.querySelector('form#auth-register-invite'), '/auth/actions/invite', async (success) => {
        console.log(success);
        return;

        M.toast({html: 'Request Success'}); // TODO: display the success message
                
        M.Modal.getInstance(elem).close();
        await delay(750);
        reload();
    }, async (error) => {
        console.log(error);
        return;

        M.toast({html: 'Request Failed'}); // TODO: display the error message
                
        console.error(error);
        await delay(750);
        reload();
    }, true);

    // License Form
    formHandler(document.querySelector('form#auth-register-license'), '/auth/actions/license', async (success) => {
        console.log(success);
        return;

        M.toast({html: 'Request Success'}); // TODO: display the success message
                
        M.Modal.getInstance(elem).close();
        await delay(750);
        reload();
    }, async (error) => {
        console.log(error);
        return;

        M.toast({html: 'Request Failed'}); // TODO: display the error message
                
        console.error(error);
        await delay(750);
        reload();
    }, true);
});
