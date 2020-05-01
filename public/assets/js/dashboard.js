window.addEventListener('message', e => {
    try {
        const data = JSON.parse(e.data);

        if (data.post_message_name === "sale") {
            apiUse('post', '/license', {
                license: data.license_key,
                gumroad_id: data.permalink
            });
        }
    } catch (e) {/* not an valid purchase*/}
}, false);
