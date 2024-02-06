import {Grid, Typography} from "@mui/material";
import {Link} from "react-router-dom";
import React, {useContext, useEffect} from "react";

export default function Header(){
    const authorized = useContext(aut)
    const [nickname, setNickname] = React.useState()

    useEffect(()=>{
        fetch('https://localhost/api/user', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
    }, [])

    return(
        <Grid container spacing={2} alignItems="center">
            <Grid item xs="auto" style={{flexGrow: 1}}>
                <Link to="/" underline="none"><Typography variant="h1">Ultima</Typography></Link>
            </Grid>
            <Grid item xs="auto" align="right" justifyContent="flex-end">
                <Link to="/" underline="none"><Typography variant="h4">Home</Typography></Link>
            </Grid>
            {}
            <Grid item xs="auto" justifyContent="flex-end">
                <Link to="/signIn" underline="none"><Typography variant="h4">Sign In</Typography></Link>
            </Grid>
        </Grid>
    );
}
