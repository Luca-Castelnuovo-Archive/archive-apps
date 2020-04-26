// Utility Functions
const delay = ms => new Promise(res => setTimeout(res, ms));
const disableAll = () => document.querySelectorAll('button, input, textarea').forEach(el => {el.disabled = true;});
const reload = () => location.reload();
const redirect = to => location.replace(to);
const api = axios.create({
    baseURL: '/',
    headers: {
        'Content-Type': 'application/json'
    }
});
const formDataToJSON = data => {
    const object = {};
    [...data].map((item) => object[item[0]] = item[1]);
    return object
}
const formHandler = (form, endpoint, successCallback, errorCallback, captchaRequired = false) => {
    form.addEventListener('submit', e => {
        e.preventDefault();
        const data = formDataToJSON(new FormData(form));
    
        if (captchaRequired && !data['h-captcha-response']) {
            M.toast({html: 'Please complete captcha'});
            return;
        }
    
        disableAll();
    
        api.post(endpoint, data).then(
            async success => successCallback(success),
            async error => errorCallback(error)
        );
    });
    
}
