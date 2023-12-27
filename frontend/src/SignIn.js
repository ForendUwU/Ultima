import React, { useState } from "react";
import Avatar from "@mui/material/Avatar";
import Button from "@mui/material/Button";
import CssBaseline from "@mui/material/CssBaseline";
import TextField from "@mui/material/TextField";
import Paper from "@mui/material/Paper";
import Grid from "@mui/material/Grid";
import LockOutlinedIcon from "@mui/icons-material/LockOutlined";
import Typography from "@mui/material/Typography";
import { makeStyles } from "@mui/styles";

const useStyles = makeStyles((theme) => ({
    root: {
        height: "100vh",
    },
    image: {
        backgroundImage: "url(https://source.unsplash.com/random)",
        backgroundSize: "cover",
    },
    paper: {
        display: "flex",
        flexDirection: "column",
        alignItems: "center",
    },
    avatar: {
        backgroundColor: theme.palette,
    },
    form: {
        width: "100%",
    },
}));

async function loginUser(credentials) {
    return fetch("https://localhost:80/api/games", {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
        },
        //body: JSON.stringify(credentials),
    }).then((data) => data.json());
}

export default function Signin() {
    const classes = useStyles();
    const [username, setUserName] = useState();
    const [password, setPassword] = useState();

    const handleSubmit = async (e) => {
        e.preventDefault();
        let response;
        response = await fetch(`https://localhost:80/api/games`)
        const backendHtmlString = await response.text()

        console.log(backendHtmlString)
        // if ("accessToken" in response) {
        //     swal("Success", response.message, "success", {
        //         buttons: false,
        //         timer: 2000,
        //     }).then((value) => {
        //         localStorage.setItem("accessToken", response["accessToken"]);
        //         localStorage.setItem("user", JSON.stringify(response["user"]));
        //         window.location.href = "/profile";
        //     });
        // } else {
        //     swal("Failed", response.message, "error");
        // }
    };

    async function getGames() {
        let response = await fetch("http://localhost:80/api/games");

        if (response.ok) {
            await response.json();
        } else {
            alert("HTTP-Error: " + response.status);
        }
    }


    return (
        <Grid container className={classes.root}>
            <CssBaseline />
            <Grid item xs={false} md={7} className={classes.image} />
            <Grid item xs={12} md={5} component={Paper} elevation={6} square>
                <div className={classes.paper}>
                    <Avatar className={classes.avatar}>
                        <LockOutlinedIcon/>
                    </Avatar>
                    <Typography component="h1" variant="h5">
                        Sign in
                    </Typography>
                    <button onClick={getGames}>GET GAMES</button>

                    <form className={classes.form} noValidate onSubmit={getGames}>
                        <TextField
                            variant="outlined"
                            margin="normal"
                            required
                            fullWidth
                            id="email"
                            name="email"
                            label="Email Address"
                            onChange={(e) => setUserName(e.target.value)}
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
                            onChange={(e) => setPassword(e.target.value)}
                        />
                        <Button
                            type="submit"
                            fullWidth
                            variant="contained"
                            color="primary"
                            className={classes.submit}
                        >
                            Sign In
                        </Button>
                    </form>
                </div>
            </Grid>
        </Grid>
    );
}