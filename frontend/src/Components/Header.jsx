import {Grid, Typography, Button} from "@mui/material";
import {Link} from "react-router-dom";
import React, {useEffect} from "react";
import Cookies from 'universal-cookie';

function handleLogout()
{
    const cookies = new Cookies();
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);

    cookies.set('token', '', {expires: yesterday});
    cookies.set('userId', '', {expires: yesterday});
    window.location.reload();
}

export default function Header(){
    const cookies = new Cookies();

    const userId = cookies.get('userId');
    const [nickname, setNickname] = React.useState(null)
    console.log(userId);

    useEffect(() => {
        fetch('https://localhost/api/user/' + userId, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(response => {
            return response.json();
        }).then(decodedResponse => {
            setNickname(decodedResponse['nickname']);
        });
    }, []);

    return(
        <Grid container spacing={2} alignItems="center">
            <Grid item xs="auto" style={{flexGrow: 1}}>
                <Link to="/" underline="none"><Typography variant="h1">Ultima</Typography></Link>
            </Grid>
            <Grid item xs="auto" align="right" justifyContent="flex-end">
                <Button to="/" underline="none"><Typography variant="h4">Home</Typography></Button>
            </Grid>
            <Grid item xs="auto" justifyContent="flex-end">
                <Button href="/signIn"><Typography variant="h4">{nickname || "Sign In"}</Typography></Button>
            </Grid>
            {nickname &&
            <Grid item xs="auto" justifyContent="flex-end">
                <Button onClick={handleLogout}><Typography variant="h4">Logout</Typography></Button>
            </Grid>
            }
        </Grid>
    );
}
