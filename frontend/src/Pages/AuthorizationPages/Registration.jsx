import React from "react";
import Cookies from 'universal-cookie';
import {FullscreenGrid, GlowingGrid, TextInput, SubmitButton} from "../../Components";
import {Alert, Typography} from "@mui/material";
import {Link} from "react-router-dom";
import validateNickname from "../../Scripts/nicknameValidator";
import validatePassword from "../../Scripts/passwordValidator";
import validateLogin from "../../Scripts/loginValidator";
import toast, { Toaster, ToastBar } from 'react-hot-toast';

export default function Registration() {
    const [authorized, setAuthorized] = React.useState(false);
    const [isRegistrating, setIsRegistrating] = React.useState(false);

    const cookies = new Cookies();

    const handleSubmit = (e) => {
        e.preventDefault();

        let { login, password, confirmationPassword, email, nickname } = document.forms[0];
        let isValidated = false;

        setIsRegistrating(true);

        cookies.set('token', null);

        try {
            if (validateNickname(nickname.value)
                && validatePassword(password.value, confirmationPassword.value)
                && validateLogin(login.value)
            ) {
                isValidated = true;
            }
        } catch (e) {
            toast(e.message);
        }

        if (isValidated) {
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
                    return response.json();
                } else {
                    setAuthorized(false);
                    return response.json();
                }
            }).then(decodedResponse => {
                if (!decodedResponse['message']) {
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);

                    cookies.set('token', decodedResponse['token'], {expires: tomorrow});

                    window.location.replace('/');
                } else {
                    toast(decodedResponse['message']);
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

                    <SubmitButton isLoading={isRegistrating} buttonText="Register" />
                </form>
                {authorized &&
                    <Alert severity="success" variant="outlined" sx={{fontSize:"100%", marginTop: "5%"}}>You are logged in</Alert>
                }
                <br/>
                <Link to="/"><Typography variant="h5">Back to home page</Typography></Link>
                <Link to="/sign-in"><Typography variant="h5">Back to sign in</Typography></Link>
                <Toaster>
                    {(t) => (
                        <ToastBar toast={t}>
                            {({ icon, message }) => (
                                <>
                                    {icon}
                                    {message}
                                </>
                            )}
                        </ToastBar>
                    )}
                </Toaster>
            </GlowingGrid>
        </FullscreenGrid>
    );
}