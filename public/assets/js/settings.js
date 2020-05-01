document.addEventListener('DOMContentLoaded', function() {
    M.Modal.init(document.querySelectorAll('.modal'), {});
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
    apiUse('post', '/user/login', {type, id});
}

const loginUnlink = type => {
    if (confirm("Do you want to unlink this login option?")) {
        apiUse('delete', '/user/login', {data: {type}});
    }
}

const removeLicense = license => {
    if (confirm("Do you want to delete this license?")) {
        apiUse('delete', '/license', {data: {license}});
    }
}

const removeAccount = () => {
    if (prompt("Type 'DELETE MY ACCOUNT' to confirm", '') == 'DELETE MY ACCOUNT') {
        if (confirm("Do you want to delete your account?")) {
            apiUse('delete', '/user/account');
        }
    }
}
