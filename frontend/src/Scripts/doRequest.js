export function doRequest({
    url: url,
    urlProp: urlProp = '',
    method: method,
    token: token = null,
    body: body = null,
    tokenFlag: tokenFlag = true,
    path: path
}) {
    let data, error, loading;

    let requestHeaders = token ?
        {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
        : {
            'Content-Type': 'application/json'
        };

        if (token || tokenFlag) {
            data = fetch(url + urlProp, {
                method: method,
                body: JSON.stringify(body),
                headers: requestHeaders
            }).then(response => {
                if (path) {
                    window.location.replace(path);
                } else {
                    return response.json();
                }
            }).catch(err => {
                error = err;
            }).finally(() => {
                loading = false;
            });
        } else { loading = false }

    return [ data, error, loading ];
}