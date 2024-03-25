import React from "react";
import Cookies from 'universal-cookie';
import {FullscreenGrid, GlowingGrid, SubmitButton} from "../../Components";
import {Typography} from "@mui/material";
import {Link} from "react-router-dom";
import validateNickname from "../../Scripts/nicknameValidator";
import validatePassword from "../../Scripts/passwordValidator";
import validateLogin from "../../Scripts/loginValidator";
import toast, { Toaster, ToastBar } from 'react-hot-toast';
import {doRequest} from "../../Scripts/doRequest";
import SignedInput from "../../Components/SignedInput";

export default function Registration() {
    const [isRegistering, setIsRegistering] = React.useState(false);

    const cookies = new Cookies();

    const handleSubmit = (e) => {
        e.preventDefault();

        setIsRegistering(true);

        let { login, password, confirmationPassword, email, nickname } = document.forms[0];
        let isValidated = false;

        cookies.set('token', null);

        try {
            if (validateNickname(nickname.value)
                && validatePassword(password.value, confirmationPassword.value)
                && validateLogin(login.value)
            ) {
                isValidated = true;
            }
        } catch (e) {
            toast.error(e.message);
            setIsRegistering(false);
        }

        if (isValidated) {
            const [data] = doRequest({
                url: 'https://localhost/api/register',
                method: 'POST',
                body: {
                    login: login.value,
                    password: password.value,
                    email: email.value,
                    nickname: nickname.value
                }
            });

            data.then(
                decodedResponse => {
                    if (decodedResponse.token) {
                        const tomorrow = new Date();
                        tomorrow.setDate(tomorrow.getDate() + 1);

                        cookies.set('token', decodedResponse['token'], {expires: tomorrow});
                        window.location.replace('/');
                    } else {
                        toast.error(decodedResponse['message']);
                        setIsRegistering(false);
                    }
                }
            )
        }
    }

    return (
        <FullscreenGrid>
            <GlowingGrid maxWidth="50vh">
                <form onSubmit={handleSubmit}>
                    <SignedInput inputName="login" sign="Login" />
                    <SignedInput inputName="password" sign="Password" type="password" />
                    <SignedInput inputName="confirmationPassword" sign="Confirmation Password" type="password" />
                    <SignedInput inputName="email" sign="Email" />
                    <SignedInput inputName="nickname" sign="Nickname" />

                    <SubmitButton isLoading={isRegistering} buttonText="Register" />
                </form>
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