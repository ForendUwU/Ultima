import React from "react";
import Avatar from "@mui/material/Avatar";
import Button from "@mui/material/Button";
import CssBaseline from "@mui/material/CssBaseline";
import TextField from "@mui/material/TextField";
import Paper from "@mui/material/Paper";
import Grid from "@mui/material/Grid";
import LockOutlinedIcon from "@mui/icons-material/LockOutlined";
import Typography from "@mui/material/Typography";
import {Link} from "react-router-dom";
import Header from "../Components/Header";

export default function SignIn() {
    const [showError, setShowError] = React.useState(false)
    const [errorMessage, setErrorMessage] = React.useState()
    const [authorized, setAuthorized] = React.useState(false)
    const [nickname, setNickname] = React.useState()

    export const AuthContext = React.createContext(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        let { login, password } = document.forms[0];

        fetch('https://localhost/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                login: login.value,
                password: password.value
            })
        }).then(response => {
            if (response.ok) {
                setAuthorized(true);
                setShowError(false);
                return response.json();
            } else {
                setShowError(true);
                setAuthorized(false);
                return response.json();
            }
        }).then(decodedResponse => {
            decodedResponse.map((data) => {
                if(!data['message']){
                    fetch('https://localhost/api/user/' + data['userId'], {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    }).then(response => {
                        return response.json();
                    }).then(decodedResponse => {
                        setNickname(decodedResponse['nickname']);
                    });
                } else {
                    setErrorMessage(data['message']);
                }
            });
        })
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
        <Grid container alignItems="center" justifyContent="center" style={{
            height: '100vh',
            overflow: "hidden",
            backgroundColor: "black"
        }}>
            <Grid item component={Paper} style={{
                padding: '1%',
                boxShadow: "0px 0px 50px lightblue, 0px 0px 40px lightblue"
            }}>
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
                <div style={{
                    display: 'flex',
                    justifyContent: 'center',
                }}>
                    {showError &&
                        <Paper
                            square={false}
                            style={{
                                marginTop: 5 + '%',
                                width: 50 + '%',
                                textAlign: 'center',
                                backgroundColor: 'red'
                            }}>
                            <p>Error</p>
                            {errorMessage}
                        </Paper>
                    }
                    {authorized &&
                        <Paper
                            square={false}
                            style={{
                                marginTop: 10 + '%',
                                width: 50 + '%',
                                textAlign: 'center',
                                backgroundColor: 'green'
                            }}>
                            Welcome {nickname}
                        </Paper>
                    }
                </div>
                <div style={{
                    display: 'flex',
                    justifyContent: 'center',
                }}>
                    <Link to="/" underline="none"><Typography variant="h4">Go back</Typography></Link>
                </div>
            </Grid>
        </Grid>
);
}