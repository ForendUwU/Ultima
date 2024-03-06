import React from "react";
import Cookies from 'universal-cookie';
import {Typography, Alert} from "@mui/material";
import {Link, useNavigate} from "react-router-dom";
import {FullscreenGrid, GlowingGrid, TextInput, SubmitButton} from "../../Components";

export default function SignIn() {
    const [showError, setShowError] = React.useState(false)
    const [errorMessage, setErrorMessage] = React.useState('')
    const [authorized, setAuthorized] = React.useState(false)

    const cookies = new Cookies();

    const handleSubmit = (e) => {
        e.preventDefault();
        let { login, password } = document.forms[0];
        cookies.set('token', null);

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
            if(!decodedResponse['message']){
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);

                cookies.set('token', decodedResponse['token'], {expires: tomorrow});
window.location.replace('/');
                //navigate('/');
            } else {
                setErrorMessage(decodedResponse['message']);
            }
        })
    }

    return (
        <FullscreenGrid>
            <GlowingGrid maxWidth="55vh">
                <form onSubmit={handleSubmit}>
                    <Typography variant="h5" style={{marginLeft: "1%"}}>Login</Typography>
                    <TextInput inputName="login" />

                    <Typography variant="h5" style={{marginLeft: "1%"}}>Password</Typography>
                    <TextInput inputName="password" />

                    <SubmitButton buttonText="Sign In" />
                </form>
                    {showError &&
                        <Alert severity="error" variant="outlined" sx={{fontSize:"100%", marginTop: "5%"}}>{errorMessage}</Alert>
                    }
                    {authorized &&
                        <Alert severity="success" variant="outlined" sx={{fontSize:"100%", marginTop: "5%"}}>You are logged in</Alert>
                    }
                <br/>
                <Link to="/"><Typography variant="h5">Back to home page</Typography></Link>
                <Link to="/registration"><Typography variant="h5">Registration</Typography></Link>
            </GlowingGrid>
        </FullscreenGrid>
    );
}