export function GetUserInfo(token)
{
    return fetch('https://localhost/api/user-info-by-token', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': token
        }
    }).then(response => {
        return response.json();
    })
}