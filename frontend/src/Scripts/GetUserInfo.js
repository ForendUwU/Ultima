export function GetUserInfo(token)
{
    return fetch('https://localhost/api/user/me', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    }).then(response => {
        return response.json();
    });
}