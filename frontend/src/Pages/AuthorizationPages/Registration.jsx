import React from "react";
import Cookies from 'universal-cookie';
import FullscreenGrid from "../../Components/FullscreenGrid";
import GlowingGrid from "../../Components/GlowingGrid";
import {Alert, Typography} from "@mui/material";
import TextInput from "../../Components/TextInput";
import SubmitButton from "../../Components/SubmitButton";
import {Link, useNavigate} from "react-router-dom";
import Loading from "../StatePages/Loading";

export default function Registration() {
    const [showError, setShowError] = React.useState(false)
    const [errorMessage, setErrorMessage] = React.useState('')
    const [authorized, setAuthorized] = React.useState(false)
    const [isLoading, setIsLoading] = React.useState(false)

    const cookies = new Cookies();
    const navigate = useNavigate();

    const handleSubmit = (e) => {
        e.preventDefault();
        let { login, password, confirmationPassword, email, nickname } = document.forms[0];
        cookies.set('token', null);

        let validation = false;
        if (login.value.length < 6) {
            setShowError(true);
            setErrorMessage('Login must contain 6 or more characters');
        } else if (login.value.length > 20) {
            setShowError(true);
            setErrorMessage('Login must contain less than 20 characters');
        } else if (!login.value.match(/^[a-zA-Z0-9!~_&*%@$]+$/)) {
            setShowError(true);
            setErrorMessage('Login must contain only letters, numbers and "!", "~", "_", "&", "*", "%", "@", "$" characters');
        } else if (password.value.length < 6) {
            setShowError(true);
            setErrorMessage('Password must contain 6 or more characters');
        } else if (password.value.length > 50) {
            setShowError(true);
            setErrorMessage('Password must contain less than 50 characters');
        } else if (!password.value.match(/^[a-zA-Z0-9!~_&*%@$]+$/)) {
            setShowError(true);
            setErrorMessage('Password must contain only letters, numbers and "!", "~", "_", "&", "*", "%", "@", "$" characters');
        } else if (password.value !== confirmationPassword.value) {
            setShowError(true);
            setErrorMessage('Password and confirmation password must match');
        } else if (nickname.value.length < 2) {
            setShowError(true);
            setErrorMessage('Nickname must contain 2 or more characters');
        } else if (nickname.value.length > 20) {
            setShowError(true);
            setErrorMessage('Nickname must contain less than 20 characters');
        } else if (!nickname.value.match(/^[a-zA-Z0-9!~_&*%@$]+$/)) {
            setShowError(true);
            setErrorMessage('Nickname must contain only letters, numbers and "!", "~", "_", "&", "*", "%", "@", "$" characters');
        } else {
            validation = true;
        }

        if (validation) {
            fetch('https://localhost/api/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    login: login.value,
                    password: password.value,
                    email: email.value,
                    nickname: nickname.value
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
                if (!decodedResponse['message']) {
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);

                    cookies.set('token', decodedResponse['token'], {expires: tomorrow});

                    navigate('/');
                } else {
                    setErrorMessage(decodedResponse['message']);
                }
            })
        }
    }

    return (
        <FullscreenGrid>
            <GlowingGrid maxWidth="50vh">
                <form onSubmit={handleSubmit}>
                    <Typography variant="h5" style={{marginLeft: "1%"}}>Login</Typography>
                    <TextInput inputName="login" />

                    <Typography variant="h5" style={{marginLeft: "1%"}}>Password</Typography>
                    <TextInput type="password" inputName="password" />

                    <Typography variant="h5" style={{marginLeft: "1%"}}>Confirmation password</Typography>
                    <TextInput type="password" inputName="confirmationPassword" />

                    <Typography variant="h5" style={{marginLeft: "1%"}}>Email</Typography>
                    <TextInput inputName="email" />

                    <Typography variant="h5" style={{marginLeft: "1%"}}>Nickname</Typography>
                    <TextInput inputName="nickname" />

                    <SubmitButton buttonText="Register" />
                </form>
                {isLoading &&
                    <Loading />
                }
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