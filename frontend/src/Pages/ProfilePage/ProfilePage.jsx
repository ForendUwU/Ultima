import React, {useContext, useEffect} from "react";
import {Avatar, Container, Grid, Typography, Button, ImageListItem, ImageList} from "@mui/material";
import {
    FullscreenGrid,
    GameButtonText,
    GlowingGrid,
    Header,
    PageTitle,
    SubmitButton,
    TextInput
} from "../../Components";
import Loading from "../StatePages/Loading";
import Error from "../StatePages/Error";
import {HeaderContext, UserContext} from "../../App/App";
import Cookies from 'universal-cookie';
import validateNickname from "../../Scripts/nicknameValidator";
import validatePassword from "../../Scripts/passwordValidator";
import toast, { Toaster, ToastBar } from 'react-hot-toast';

export default function ProfilePage() {
    const [loading, setLoading] = React.useState(true);
    const [error, setError] = React.useState(null);
    const [games, setGames] = React.useState([]);
    const [isProfileSettingsOpened, setIsProfileSettingsOpened] = React.useState(false);
    const [isOldPasswordCorrect, setIsOldPasswordCorrect] = React.useState();

    const headerContext = useContext(HeaderContext);
    const userContext = useContext(UserContext);

    const cookies = new Cookies();

    useEffect(() => {
        fetch('https://localhost/api/user/get-most-played-games', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + cookies.get('token')
            }
        }).then(response => {
            if (response.ok) {
                return response.json();
            } else {
                throw new Error();
            }
        }).then(decodedResponse => {
            setGames(decodedResponse);
        }).catch(error => {
            setError(error);
        }).finally(()=>{
            setLoading(false);
        });
    }, []);

    const handleSubmit = (e) => {
        e.preventDefault();
        let { nickname, firstName, lastName, oldPassword, newPassword, repeatPassword, email  } = document.forms[0];

        nickname.value = nickname.value ? nickname.value : null;
        firstName.value = firstName.value ? firstName.value : null;
        lastName.value = lastName.value ? lastName.value : null;
        email.value = email.value ? email.value : null;

        if (oldPassword.value && newPassword.value && repeatPassword.value) {
            try {
                fetch('https://localhost/api/user/check-pass', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + cookies.get('token')
                    },
                    body: JSON.stringify({
                        password: newPassword.value,
                    })
                }).then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error(response.status);
                    }
                }).then(decodedResponse => {
                    if (decodedResponse['result'] === "invalid") {
                        setIsOldPasswordCorrect(false);
                    }
                }).catch(e=>console.log(e));

                if (!isOldPasswordCorrect) {
                    toast('Old password is not correct');
                } else {
                    validatePassword(newPassword.value, repeatPassword.value, oldPassword.value);
                }
            } catch (e) {
                toast(e.message);
                newPassword.value = null;
            }
        }

        if (nickname.value) {
            try {
                validateNickname(nickname.value);
            } catch (e) {
                toast(e.message);
                nickname.value = null;
            }
        }

        fetch('https://localhost/api/user/change-data/'+userContext.login, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + cookies.get('token')
            },
            body: JSON.stringify({
                nickname: nickname.value,
                password: newPassword.value,
                firstName: firstName.value,
                lastName: lastName.value,
                email: email.value
            })
        }).then(response => {
            if (response.ok) {
                return response.json();
            }
        }).then(decodedResponse => {
            window.location.reload();
        })
    }

    if(loading || !headerContext.userLoaded) return <Loading />;
    if(error) return <Error errorText={error.toString()} />;

    return(
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                    <Header />
                    <PageTitle title="Profile" />
                    <Grid container alignItems="center" direction="column">
                        <Grid item>
                            <Avatar alt={userContext.nickname} src="/static/images/avatar/1.jpg" sx={{fontSize: "190%", width: 180, height: 180}} />
                        </Grid>
                        <Grid item>
                            <Typography variant="h3" sx={{paddingTop: "20%"}}>{userContext.nickname}</Typography>
                        </Grid>
                        <Grid item>
                            <Button variant="outlined" color="success" sx={{marginTop: "10%", fontSize: "100%"}} onClick={() => setIsProfileSettingsOpened(!isProfileSettingsOpened)}>Profile settings</Button>
                        </Grid>
                    </Grid>
                    {isProfileSettingsOpened &&
                        <form onSubmit={handleSubmit}>
                        <Grid container alignItems="center" justifyContent="space-between">
                                <Grid item>
                                    <Typography variant="h5" style={{marginLeft: "1%"}}>Nickname</Typography>
                                    <TextInput required={false} inputName="nickname" defaultValue={userContext.nickname}/>
                                </Grid>
                                <Grid item>
                                    <Typography variant="h5" style={{marginLeft: "1%"}}>First name</Typography>
                                    <TextInput required={false} inputName="firstName" defaultValue={userContext.firstName}/>
                                </Grid>
                                <Grid item>
                                    <Typography variant="h5" style={{marginLeft: "1%"}}>Last name</Typography>
                                    <TextInput required={false} inputName="lastName" defaultValue={userContext.lastName}/>
                                </Grid>
                                <Grid item>
                                    <Typography variant="h5" style={{marginLeft: "1%"}}>Old password</Typography>
                                    <TextInput required={false} inputName="oldPassword"/>
                                    <Typography variant="h5" style={{marginLeft: "1%"}}>New password</Typography>
                                    <TextInput required={false} inputName="newPassword"/>
                                    <Typography variant="h5" style={{marginLeft: "1%"}}>Repeat new password</Typography>
                                    <TextInput required={false} inputName="repeatPassword"/>
                                </Grid>
                                <Grid item>
                                    <Typography variant="h5" style={{marginLeft: "1%"}}>Email</Typography>
                                    <TextInput required={false} inputName="email" defaultValue={userContext.email}/>
                                </Grid>
                                <SubmitButton buttonText="Change data"/>
                        </Grid>
                        </form>
                    }
                    <Grid container direction="column" alignItems="center">
                        <Grid item>
                            <Typography sx={{marginTop: "10%", marginBottom: "10%"}} variant="h3">Your the most played games</Typography>
                        </Grid>
                        <Grid item>
                            <ImageList cols={5} sx={{padding: "1%"}}>
                                {games.map((item, index) => (
                                    <Button disabled key={index} sx={{ backgroundColor: "#9ED2C6", boxShadow: "0.1vh 0.1vh 0.3vh #e42323" }}>
                                        <ImageListItem>
                                            <img
                                                src={'https://source.unsplash.com/random/200x200?sig='+index}
                                                alt="Game image"
                                            />
                                            <GameButtonText>{item.title}</GameButtonText>
                                            <GameButtonText>{item.hoursOfPlaying.toFixed(2)} hours</GameButtonText>
                                        </ImageListItem>
                                    </Button>
                                ))}
                            </ImageList>
                        </Grid>
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