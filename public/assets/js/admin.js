document.addEventListener('DOMContentLoaded', function() {
    M.Modal.init(document.querySelectorAll('.modal'), {});
});

const createApp = () => {
    M.Modal.getInstance(document.querySelector('.modal#app')).open()
}

const toggleApp = identifier => {
    const app = apps.filter(app => app.id == identifier)[0];
    
    apiUse('put', `/app/${identifier}`, {
        gumroad_id: null,
        name: null,
        url: null,
        active: !(app.active == true)
    });
}

const editApp = identifier => {
    const app = apps.filter(app => app.id == identifier)[0];

    const id = document.querySelector('input#app-edit-id');
    const gumroad_id = document.querySelector('input#app-edit-gumroad_id');
    const name = document.querySelector('input#app-edit-name');
    const url = document.querySelector('input#app-edit-url');
    const active = document.querySelector('input#app-edit-active');
    
    id.value = app.id;
    gumroad_id.value = app.gumroad_id;
    name.value = app.name;
    url.value = app.url;
    active.value = app.active;

    M.updateTextFields();
    M.Modal.getInstance(document.querySelector('.modal#app')).open()
}

const appForm = document.querySelector('form#app');
appForm.addEventListener('submit', e => {
    e.preventDefault();
    
    const identifier = document.querySelector('input#app-edit-id').value;
    if (identifier) {
        return formSubmit(appForm, `/app/${identifier}`, false, 'put');
    }
    
    return formSubmit(appForm, '/app');
});

const deleteApp = () => {
    const identifier = document.querySelector('input#app-edit-id').value;

    if (confirm("Do you want to delete this app?")) {
        apiUse('delete', `/app/${identifier}`);
    }
}

const toggleUser = identifier => {
    apiUse('put', `/admin/user/${identifier}`);
}

const inviteUser = () => {
    // const email = ''; // from input
    // console.log(email);
    // apiUse('post', '/admin/invite', {email});
}
