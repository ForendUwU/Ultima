import React from "react";
import Cookies from 'universal-cookie';
import {Typography} from "@mui/material";
import {Link} from "react-router-dom";
import {FullscreenGrid, GlowingGrid, SubmitButton} from "../../Components";
import SignedInput from "../../Components/SignedInput";
import toast, { Toaster, ToastBar } from 'react-hot-toast';
import {doRequest} from "../../Scripts/doRequest";

export default function SignIn() {
    const [loading, setLoading] = React.useState();

    const cookies = new Cookies();

    const handleSubmit = (e) => {
        e.preventDefault();
        let { login, password } = document.forms[0];
        cookies.set('token', null);
        setLoading(true);

        const [data] = doRequest({
            url: 'https://localhost/api/login',
            method: 'POST',
            body: {
                login: login.value,
                password: password.value
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
                    setLoading(false);
                }
            }
        )
    }

    return (
        <FullscreenGrid>
            <GlowingGrid maxWidth="55vh">
                <form onSubmit={handleSubmit}>
                    <SignedInput inputName="login" sign="Login" />
                    <SignedInput inputName="password" sign="Password" type="password" />

                    <SubmitButton buttonText="Sign In" isLoading={loading} />
                </form>
                <br/>
                <Link to="/"><Typography variant="h5">Back to home page</Typography></Link>
                <Link to="/registration"><Typography variant="h5">Registration</Typography></Link>
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