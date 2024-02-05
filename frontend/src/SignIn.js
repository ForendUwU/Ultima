import React from "react";
import Avatar from "@mui/material/Avatar";
import Button from "@mui/material/Button";
import CssBaseline from "@mui/material/CssBaseline";
import TextField from "@mui/material/TextField";
import Paper from "@mui/material/Paper";
import Grid from "@mui/material/Grid";
import LockOutlinedIcon from "@mui/icons-material/LockOutlined";
import Typography from "@mui/material/Typography";

export default function SignIn() {
//    const classes = useStyles();
    const [showError, setShowError] = React.useState(false)
    const [authorized, setAuthorized] = React.useState(false)
    const [nickname, setNickname] = React.useState()

    const handleSubmit = async (e) => {
        e.preventDefault();
        let { login, password } = document.forms[0];

        const response = await fetch('https://localhost/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                login: login.value,
                password: password.value
            })
        }); //then

        const data = await response.json();

        if (!response.ok) {
            setShowError(true);
            setShowSuccess(false);
        } else {
            const response1 = await fetch('https://localhost/api/user/' + data['userId'], {
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
    }

    const getGames = async () => {
        let response = await fetch("https://localhost/api/games", {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            await response.json();
        } else {
            alert("HTTP-Error: " + response.status);
        }
    }

    const logout = async () => {
        let response = await fetch("https://localhost/api/games", {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            await response.json();
        } else {
            alert("HTTP-Error: " + response.status);
        }
    }

    return (
        <Grid container style={{
            height: '100vh',
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            overflow: "hidden"
        }}>
            <img src="https://source.unsplash.com/random" style={{
                position: "absolute",
                height: "100%",
                width: "100%",
                objectFit: "cover",
                zIndex: -1
            }} alt="Random Image"/>
            <Grid item component={Paper} elevation={6} style={{
                padding: '1%'
            }}>
                {/*<Typography variant={'h1'}>*/}
                {/*    Sign in*/}
                {/*</Typography>*/}

                <form
                    noValidate
                    onSubmit={handleSubmit}
                >
                    <div style={{marginLeft: "1%"}}>Login</div>
                    <input
                        style={{
                            marginRight: "2%",
                            marginTop: "2%",
                            marginBottom: "2%",
                            borderRadius: "5px",
                            borderColor: "rgba(25,118,210,0.5)",
                            borderStyle: "solid",
                            placeholder: "text",
                            width: "100%",
                            height: "5vh"
                        }}
                        required
                        id="login"
                        name="login"
                        type="login"
                    />

                    <div style={{marginLeft: "1%"}}>Password</div>
                    <input
                        style={{
                            marginRight: "2%",
                            marginTop: "2%",
                            marginBottom: "2%",
                            borderRadius: "5px",
                            borderColor: "rgba(25,118,210,0.5)",
                            borderStyle: "solid",
                            placeholder: "text",
                            width: "100%",
                            height: "5vh"
                        }}
                        required
                        id="password"
                        name="password"
                        type="password"
                    />
                    <Button
                        type="submit"
                        fullWidth
                        variant="outlined"
                        color="primary"
                        size="large"
                        style={{fontSize: "100%"}}
                    >
                        Sign In
                    </Button>
                </form>
                {showError &&
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
                }

                <div style={{
                    display: 'flex',
                    justifyContent: 'center',
                }}>
                    {authorized &&
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
                    }
                </div>
            </Grid>
        </Grid>
    );
}