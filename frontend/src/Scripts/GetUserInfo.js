export function GetUserInfo(token)
{
    return fetch('https://localhost/api/user/get-info-by-token', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    }).then(response => {
        return response.json();
    });
}