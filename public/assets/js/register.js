document.addEventListener('DOMContentLoaded', () => {
    M.Modal.init(document.querySelectorAll('.modal'), {});
});

const form = document.querySelector('form');
form.addEventListener('submit', e => e.preventDefault());

const submit = (type, value) => {
    document.querySelector(`input[name="${type}"]`).value = value;
    document.querySelector('input[name="type"]').value = type;
    formSubmit(form, '/auth/register');
}

const popup = type => {
    const popupWindow = window.open(`/auth/${type}/request?popup=1`, '', 'height=720,width=480');
    popupWindow.window.focus();

    window.popupCallback = id => {
        submit(type, id);
    }
}

document.querySelector('button#email').addEventListener('click', () => {
    submit('email', document.querySelector('input#email-data').value);
});
