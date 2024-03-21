import React, {useContext} from "react";
import {Container, Grid, ImageList} from "@mui/material";
import {FullscreenGrid, GameCard, GlowingGrid, Header, PageTitle, SubmitButton} from "../../Components";
import Loading from "../StatePages/Loading";
import Error from "../StatePages/Error";
import {HeaderContext, UserContext} from "../../App/App";
import Cookies from 'universal-cookie';
import validateNickname from "../../Scripts/nicknameValidator";
import validatePassword from "../../Scripts/passwordValidator";
import toast, { Toaster, ToastBar } from 'react-hot-toast';
import useFetch from "../../Hooks/useFetch";
import {doRequest} from "../../Scripts/doRequest";
import validateName from "../../Scripts/nameValidator";
import validateEmail from "../../Scripts/emailValidator";
import UserData from "./UserData"
import ChangeTypeOfChangingForm from "./ChangeTypeOfChangingForm"
import ChangeDataForm from "./ChangeDataForm"
import ChangePasswordForm from "./ChangePasswordForm"

export default function ProfilePage() {
    const [isProfileSettingsOpened, setIsProfileSettingsOpened] = React.useState(false);
    const [isChangePassword, setIsChangePassword] = React.useState(false);

    const headerContext = useContext(HeaderContext);
    const userContext = useContext(UserContext);

    const [nickname, setNickname] = React.useState();
    const [firstName, setFirstName] = React.useState();
    const [lastName , setLastName] = React.useState();
    const [email, setEmail] = React.useState();
    const [oldPassword, setOldPassword] = React.useState();
    const [newPassword, setNewPassword] = React.useState();
    const [repeatPassword, setRepeatPassword] = React.useState();

    const cookies = new Cookies();

    const userId = userContext.userInfo ? userContext.userInfo.id : null;

    const [games, error, loading] = useFetch({
        url: 'https://localhost/api/user/'+userId+'/most-played-games',
        method: 'GET',
        token: cookies.get('token'),
    });

    const handleChangeData = () => {
        let localNickname = nickname !== undefined ? nickname : userContext.userInfo.nickname;
        let localFirstName = firstName !== undefined ? firstName : userContext.userInfo.firstName;
        let localLastName = lastName !== undefined ? lastName : userContext.userInfo.lastName;
        let localEmail = email !== undefined ? email : userContext.userInfo.email;

        let validated = false;
        try {
            if (validateNickname(localNickname)
                && validateName(localFirstName, true)
                && validateName(localLastName, true)
                && validateEmail(localEmail)
            ) {
                validated = true;
            }
        } catch (e) {
            toast.error(e.message);
        }

        if (validated) {
                doRequest({
                    url: 'https://localhost/api/user/'+userContext.userInfo.id+'/change-data',
                    method: 'PATCH',
                    token: cookies.get('token'),
                    body: {
                        nickname: localNickname,
                        firstName: localFirstName,
                        lastName: localLastName,
                        email: localEmail
                    }
                });
                userContext.setUserInfo((previousInfo) => ({
                    ...previousInfo,
                    nickname: localNickname,
                    firstName: localFirstName,
                    lastName: localLastName,
                    email: localEmail
                }))
                toast.success('Successfully updated');
        }
    }

    const handleChangePassword = () => {
        let validated = false;
        try {
            if (oldPassword) {
                if (validatePassword(newPassword, repeatPassword)
                ) {
                    validated = true;
                }
            } else {
                toast.error('Old password mustn\'t be empty');
            }
        } catch (e) {
            toast.error(e.message);
        }

        if (validated) {
            const [data] = doRequest({
                url: 'https://localhost/api/user/'+userContext.userInfo.id+'/change-pass',
                method: 'PATCH',
                token: cookies.get('token'),
                body: {
                    oldPassword: oldPassword,
                    newPassword: newPassword,
                }
            });
            data.then(decodedResponse => {
                if (decodedResponse['message'] !== 'successfully updated') {
                    toast.error(decodedResponse['message']);
                } else {
                    toast.success('Successfully updated');
                }
            })
        }
    }

    if(loading || !headerContext.userLoaded || !games) return <Loading />
    //if(error) return <Error errorText={error.toString()} />;

    return(
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                    <Header />
                    <PageTitle>Profile</PageTitle>
                    <UserData
                        setIsProfileSettingsOpened={setIsProfileSettingsOpened}
                        isProfileSettingsOpened={isProfileSettingsOpened}
                        userContext={userContext}
                    />
                    {isProfileSettingsOpened &&
                        <>
                            <ChangeTypeOfChangingForm
                                setIsChangePassword={setIsChangePassword}
                                isChangePassword={isChangePassword}
                            />
                            <Grid container alignItems="center" justifyContent="space-between">
                                {!isChangePassword ?
                                    <ChangeDataForm
                                        setNickname={setNickname}
                                        setFirstName={setFirstName}
                                        setLastName={setLastName}
                                        setEmail={setEmail}
                                        userContext={userContext}
                                    />
                                    :
                                    <ChangePasswordForm
                                        setOldPassword={setOldPassword}
                                        setNewPassword={setNewPassword}
                                        setRepeatPassword={setRepeatPassword}
                                    />
                                }
                                <SubmitButton
                                    clickHandler={!isChangePassword ? handleChangeData : handleChangePassword}
                                    buttonText="Change data"
                                />
                            </Grid>
                        </>
                    }
                    <Grid container direction="column" alignItems="center">
                        <PageTitle>Your the most played games</PageTitle>
                        <ImageList cols={5} sx={{padding: "1%"}}>
                            {games.map((item) => (
                                <GameCard item={item} showPrice={false} showPlayingTime={true} />
                            ))}
                        </ImageList>
                    </Grid>
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
            </Container>
        </FullscreenGrid>
    );
}