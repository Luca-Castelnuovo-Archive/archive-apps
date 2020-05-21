const licensePopup = id => {
    const popupWindow = window.open(`/license/${id}/${window.offer_code}`, '', 'height=720,width=480');
    popupWindow.window.focus();

    window.popupCallback = data => { 
        apiUse('post', '/license', {
            license: data.license,
            id: data.id
        });
    }
}
