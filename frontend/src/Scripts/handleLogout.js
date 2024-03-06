import Cookies from "universal-cookie";
//import {useNavigate} from "react-router-dom";

export function HandleLogout()
{
    const cookies = new Cookies();
    const yesterday = new Date();
    //const navigate = useNavigate();
    yesterday.setDate(yesterday.getDate() - 1);

    fetch('https://localhost/api/logout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + cookies.get('token')
        }
    }).then(response => {
        return response.json();
    }).then(decodedResponse => {
        console.log(decodedResponse);
    });

    cookies.set('token', '', {expires: yesterday});
    cookies.set('userId', '', {expires: yesterday});
    //navigate('/');
    window.location.replace('/');
}