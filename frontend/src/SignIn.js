import React, { useState } from "react";
import Avatar from "@mui/material/Avatar";
import Button from "@mui/material/Button";
import CssBaseline from "@mui/material/CssBaseline";
import TextField from "@mui/material/TextField";
import Paper from "@mui/material/Paper";
import Grid from "@mui/material/Grid";
import LockOutlinedIcon from "@mui/icons-material/LockOutlined";
import Typography from "@mui/material/Typography";
// import { makeStyles } from "@mui/styles";

// const useStyles = makeStyles((theme) => ({
//     root: {
//         height: "100vh",
//     },
//     image: {
//         backgroundImage: "url(https://source.unsplash.com/random)",
//         backgroundSize: "cover",
//     },
//     paper: {
//         display: "flex",
//         flexDirection: "column",
//         alignItems: "center",
//     },
//     avatar: {
//         backgroundColor: theme.palette,
//     },
//     form: {
//         width: "100%",
//     },
// }));

export default function Signin() {
//    const classes = useStyles();
    const [showError, setShowError] = React.useState(false)
    const [showSuccess, setShowSuccess] = React.useState(false)
    const [authorized, setAuthorized] = React.useState(false)
    const [nickname, setNickname] = React.useState()

    const handleSubmit = async (e) => {
        e.preventDefault();
        let { login, password } = document.forms[0];

        const response = await fetch('https://localhost/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                login: login.value,
                password: password.value
            })
        });

        const data = await response.json();

        if (!response.ok) {
            setShowError(true);
            setShowSuccess(false);
            console.log(data);
        }else{
            const response1 = await fetch('https://localhost/api/users/' + data, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            let get = await response1.json();
            setNickname(get['nickname']);

            setAuthorized(true);
            setShowError(false);
            setShowSuccess(true);
        }

        //login.value = '';
        //password.value = '';
        //emit('user-authenticated', userIri);
    }

    const getGames = async () => {
        let response = await fetch("https://localhost/api/games");

        if (response.ok) {
            await response.json();
        } else {
            alert("HTTP-Error: " + response.status);
        }
    }

    return (
        <Grid
            container
            //className={classes.root}
        >
            <CssBaseline />
            <Grid
                item
                xs={false}
                md={7}
                //className={classes.image}
            />
            <Grid item xs={12} md={5} component={Paper} elevation={6} square>
                <div
                    //className={classes.paper}
                >
                    <Avatar
                        //className={classes.avatar}
                    >
                        <LockOutlinedIcon/>
                    </Avatar>
                    <Typography component="h1" variant="h5">
                        Sign in
                    </Typography>

                    <button onClick={getGames}>getGam</button>
                    <form
                        //className={classes.form}
                        noValidate
                        onSubmit={handleSubmit}
                    >
                        <TextField
                            variant="outlined"
                            margin="normal"
                            required
                            fullWidth
                            id="login"
                            name="login"
                            label="Login"
                        />
                        <TextField
                            variant="outlined"
                            margin="normal"
                            required
                            fullWidth
                            id="password"
                            name="password"
                            label="Password"
                            type="password"
                        />
                        <Button
                            type="submit"
                            fullWidth
                            variant="contained"
                            color="primary"
                            //className={classes.submit}
                        >
                            Sign In
                        </Button>
                    </form>
                </div>
                <div style={{
                    display: 'flex',
                    justifyContent: 'center',
                }}>
                    {showError ?
                        <Paper
                            square={false}
                            style={{
                                marginTop: 5 + '%',
                                width: 50 + '%',
                                textAlign: 'center',
                                backgroundColor: 'red'
                            }}>
                            Error
                        </Paper>
                        : null}
                </div>
                <div style={{
                    display: 'flex',
                    justifyContent: 'center',
                }}>
                    {showSuccess ?
                        <Paper
                            square={false}
                            style={{
                                marginTop: 5 + '%',
                                width: 50 + '%',
                                textAlign: 'center',
                                backgroundColor: 'green'
                            }}>
                            Success
                        </Paper>
                        : null}
                </div>

                <div style={{
                    display: 'flex',
                    justifyContent: 'center',
                }}>
                    {authorized ?
                        <Paper
                            square={false}
                            style={{
                                marginTop: 10 + '%',
                                width: 50 + '%',
                                textAlign: 'center',
                                backgroundColor: 'green'
                            }}>
                            Welcome { nickname }
                        </Paper>
                        : null}
                </div>
            </Grid>
        </Grid>
    );
}