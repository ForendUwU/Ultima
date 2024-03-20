import { useEffect, useState } from "react";

export default function useFetch({
    url: url,
    urlProp: urlProp = '',
    method: method,
    token: token = null,
    body: body = null,
    tokenFlag: tokenFlag = true,
    updateEffect = 0
}) {
    const [data, setData] = useState();
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(true);

    let requestHeaders = token ?
        {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
        : {
            'Content-Type': 'application/json'
        };

    let requestBody = body ?
        {
            body: JSON.stringify({body})
        } : null;

    useEffect(() => {
        console.log(token || tokenFlag);
        if (token || tokenFlag) {
            fetch(url + urlProp, {
                method: method,
                requestBody,
                headers: requestHeaders
            }).then(response => {
                return response.json()
            }).then(decodedResponse => {
                setData(decodedResponse);
            }).catch(error => {
                setError(error);
            }).finally(() => {
                setLoading(false);
            });
        } else { setLoading(false) }
    }, [url, updateEffect]);

    return [ data, error, loading ];
}