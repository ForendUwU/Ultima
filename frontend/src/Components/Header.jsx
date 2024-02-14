import {Grid, Typography, Button} from "@mui/material";
import React, {useEffect} from "react";
import Cookies from 'universal-cookie';
import {GetUserInfo} from "../Scripts/GetUserInfo";

function handleLogout()
{
    const cookies = new Cookies();
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);

    fetch('https://localhost/api/logout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': cookies.get('token')
        }
    }).then(response => {
        return response.json();
    }).then(decodedResponse => {
        console.log(decodedResponse);
    });

    cookies.set('token', '', {expires: yesterday});
    cookies.set('userId', '', {expires: yesterday});
    window.location.reload();
}

export default function Header(){
    const cookies = new Cookies();
    const [nickname, setNickname] = React.useState(null)

    useEffect(() => {
        console.log(cookies.get('token'));
        GetUserInfo(cookies.get('token')).then(decodedResponse => {
            setNickname(decodedResponse['nickname']);
        });
    }, []);

    return(
        <Grid container spacing={2} alignItems="center">
            <Grid item xs="auto" style={{flexGrow: 1}}>
                <Button href="/"><Typography variant="h1">Ultima</Typography></Button>
            </Grid>
            <Grid item xs="auto" align="right" justifyContent="flex-end">
                <Button href="/"><Typography variant="h4">Home</Typography></Button>
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
