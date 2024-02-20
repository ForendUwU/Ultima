import React from "react";
import Cookies from 'universal-cookie';
import FullscreenGrid from "../../Components/FullscreenGrid";
import GlowingGrid from "../../Components/GlowingGrid";
import {Alert, Typography} from "@mui/material";
import TextInput from "../../Components/TextInput";
import SubmitButton from "../../Components/SubmitButton";
import {Link} from "react-router-dom";

export default function Registration() {
    const [showError, setShowError] = React.useState(false)
    const [errorMessage, setErrorMessage] = React.useState('')
    const [authorized, setAuthorized] = React.useState(false)

    const cookies = new Cookies();

    const handleSubmit = (e) => {
        e.preventDefault();
        let { login, password, email, inputNickname } = document.forms[0];
        cookies.set('token', null);

        fetch('https://localhost/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                login: login.value,
                password: password.value,
                email: email.value,
                nickname: inputNickname.value
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

                window.location.replace("/");
            } else {
                setErrorMessage(decodedResponse['message']);
            }
        })
    }

    return (
        <FullscreenGrid>
            <GlowingGrid>
                <form onSubmit={handleSubmit}>
                    <Typography variant="h5" style={{marginLeft: "1%"}}>Login</Typography>
                    <TextInput inputName="login" />

                    <Typography variant="h5" style={{marginLeft: "1%"}}>Password</Typography>
                    <TextInput inputName="password" />

                    <Typography variant="h5" style={{marginLeft: "1%"}}>Email</Typography>
                    <TextInput inputName="email" />

                    <Typography variant="h5" style={{marginLeft: "1%"}}>Nickname</Typography>
                    <TextInput inputName="inputNickname" />

                    <SubmitButton buttonText="Registrate" />
                </form>
                {showError &&
                    <Alert severity="error" variant="outlined" sx={{fontSize:"100%", marginTop: "5%"}}>{errorMessage}</Alert>
                }
                {authorized &&
                    <Alert severity="success" variant="outlined" sx={{fontSize:"100%", marginTop: "5%"}}>You are logged in</Alert>
                }
                <br/>
                <Link to="/"><Typography variant="h5">Back to home page</Typography></Link>
                <Link to="/sign-in"><Typography variant="h5">Back to sign in</Typography></Link>
            </GlowingGrid>
        </FullscreenGrid>
    );
}