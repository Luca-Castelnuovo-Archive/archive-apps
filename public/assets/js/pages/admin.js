document.addEventListener('DOMContentLoaded', function() {
    M.Modal.init(document.querySelectorAll('.modal'), {});
});

const appFrom = document.querySelector('form#app');
appFrom.addEventListener('submit', e => {
    e.preventDefault();
    
    updateApp(document.querySelector('input#app-id').value);
});

const updateApp = id => {    
    apiUse('post', `/app/${id}`);
}

const toggleApp = id => { 
    apiUse('put', `/app/${id}`);
}

const deleteApp = id => {
    if (confirm("Do you want to delete this app?")) {
        apiUse('delete', `/app/${id}`);
    }
}

const toggleUser = id => {
    apiUse('put', `/admin/user/${id}`);
}

const inviteForm = document.querySelector('form#invite');
inviteForm.addEventListener('submit', e => {
    e.preventDefault();
    
    formSubmit(inviteForm, '/admin/invite');
});

const clearHistory = document.querySelector('button#clearHistory');
clearHistory.addEventListener("click", () => {
    if (confirm("Do you want to clear the history?")) {
        apiUse('delete', '/admin/history');
    }
});
