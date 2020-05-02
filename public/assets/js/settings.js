document.addEventListener('DOMContentLoaded', function() {
    M.Modal.init(document.querySelectorAll('.modal'), {});
    M.FormSelect.init(document.querySelectorAll('select'), {});
});

const loginPopup = type => {
    const popupWindow = window.open(`/auth/${type}/request?popup=1`, '', 'height=720,width=480');
    popupWindow.window.focus();

    window.popupCallback = id => {
        loginLink(type, id);
    }
}

const loginEmail = () => {
    loginLink('email', document.querySelector('input[name="email"]').value);
}

const loginLink = (type, id) => {
    apiUse('post', '/settings/login', {type, id});
}

const loginUnlink = type => {
    if (confirm("Do you want to unlink this login option?")) {
        apiUse('delete', '/settings/login', {data: {type}});
    }
}

const addLicense = () => {
    const license = document.querySelector('input[name="license"]').value;
    const gumroad_id = document.querySelector('select[name="gumroad_id"]').value;

    apiUse('post', '/license', {license, gumroad_id});
}

const removeLicense = license => {
    if (confirm("Do you want to delete this license?")) {
        apiUse('delete', '/license', {data: {license}});
    }
}

const removeAccount = () => {
    if (prompt("Type 'DELETE MY ACCOUNT' to confirm", '') == 'DELETE MY ACCOUNT') {
        if (confirm("Do you want to delete your account?")) {
            apiUse('delete', '/settings/account');
        }
    }
}
