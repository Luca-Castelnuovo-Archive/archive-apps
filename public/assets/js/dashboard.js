const licensePopup = gumroad_id => {
    const popupWindow = window.open(`/license/${gumroad_id}`, '', 'height=720,width=480');
    popupWindow.window.focus();

    window.popupCallback = data => {
        apiUse('post', '/license', {
            license: data.license_key,
            gumroad_id: data.gumroad_id
        });
    }
}
