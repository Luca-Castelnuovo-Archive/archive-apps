const licensePopup = gumroad_id => {
    const popupWindow = window.open(`/license/${gumroad_id}/${window.offer_code}`, '', 'height=720,width=480');
    popupWindow.window.focus();

    window.popupCallback = data => { 
        apiUse('post', '/license', {
            license: data.license,
            gumroad_id: data.gumroad_id
        });
    }
}
