import React, {useEffect} from "react";
import {useNavigate} from "react-router-dom";
import {Container, Typography, Stack, Paper, Button, Dialog, DialogTitle} from "@mui/material";
import Cookies from 'universal-cookie';

import Header from "../../Components/Header"
import FullscreenGrid from "../../Components/FullscreenGrid";
import GlowingGrid from "../../Components/GlowingGrid";
import PageTitle from "../../Components/PageTitle";
import Timer from "../../Components/Timer";
import PurchasedGameButton from "../../Components/PurchasedGameButton";

import Error from "../StatePages/Error"
import Loading from "../StatePages/Loading";

import {GetUserInfo} from "../../Scripts/GetUserInfo";

export default function PurchasedGames() {
    const [games, setGames] = React.useState([]);
    const [loading, setLoading] = React.useState(true);
    const [error, setError] = React.useState(null);
    const [nickname, setNickname] = React.useState(null);
    const [balance, setBalance] = React.useState(null);
    const [userLoaded, setUserLoaded] = React.useState(false);
    const [showDialog, setShowDialog] = React.useState(false);
    const [currentlyPlayingGameTitle, setCurrentlyPlayingGameTitle] = React.useState();
    const [currentlyPlayingGameId, setCurrentlyPlayingGameId] = React.useState();
    const [time, setTime] = React.useState(0);

    const cookies = new Cookies();
    const navigate = useNavigate();

    //Get games purchased by user
    useEffect(() => {
        fetch('https://localhost/api/purchase-game', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + cookies.get('token')
            }
        }).then(response => {
            if (response.ok || response.status === 401) {
                return response.json();
            } else {
                throw new Error();
            }
        }).then(decodedResponse => {
            if (decodedResponse.message === "Unauthorized") {
                navigate('/');
            } else {
                setGames(decodedResponse);
            }
        }).catch(error => {
            setError(error);
        }).finally(()=>{
            setLoading(false);
        })
    },[]);

    //Get info about user for header
    useEffect(() => {
        GetUserInfo(cookies.get('token'))
            .then(decodedResponse => {
                setNickname(decodedResponse['nickname']);
                setBalance(decodedResponse['balance']);
            }).catch(error => {
                setError(error);
            }).finally(()=>{
                setUserLoaded(true);
            })
    },[]);

    //Timer
    useEffect(() => {
        let interval = null;

        if (showDialog) {
            interval = setInterval(() => {
                setTime((time) => time + 10);
            }, 10);
        } else {
            clearInterval(interval);
        }

        return () => {
            clearInterval(interval);
        }
    }, [showDialog]);

    function handleClickLaunchButton (gameTitle, gameId, hoursOfPlaying)
    {
        setTime(hoursOfPlaying * 3600000);
        setShowDialog(true);
        setCurrentlyPlayingGameTitle(gameTitle);
        setCurrentlyPlayingGameId(gameId);
    }

    function handleClickDeleteButton (gameId)
    {
        fetch('https://localhost/api/purchase-game', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + cookies.get('token')
            },
            body: JSON.stringify({
                gameId: gameId,
            }),
        }).then(response => {
            return response.json();
        }).then(decodedResponse => {
            console.log(decodedResponse);
        }).finally(() => navigate(0));
    }

    function handleLogout()
    {
        const cookies = new Cookies();
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);

        fetch('https://localhost/api/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + cookies.get('token')
            }
        }).then(response => {
            return response.json();
        }).then(decodedResponse => {
            console.log(decodedResponse);
        }).finally(() => {
            cookies.set('token', '', {expires: yesterday});
            cookies.set('userId', '', {expires: yesterday});
            navigate('/');
        });
    }

    function handleCloseGame()
    {
        setShowDialog(false);

        fetch('https://localhost/api/save-playing-time', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + cookies.get('token')
            },
            body: JSON.stringify({
                gameId: currentlyPlayingGameId,
                time: time
            }),
        }).then(response => {
            return response.json();
        }).then(decodedResponse => {
            console.log(decodedResponse);
         }).finally(()=>navigate(0));
    }

    if(loading || !userLoaded) return <Loading />;
    if(error) return <Error errorText={error.toString()} />;

    return (
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                    <Header nickname={nickname} balance={balance} handleLogout={handleLogout} />
                    <PageTitle title="Purchased games" />
                    {games && games.length !== 0 ?
                        <Stack spacing={2}>
                            {
                                games.map((item, index) => (
                                    <Paper key={index} elevation={3} sx={{padding: "1%", display: "flex", justifyContent: "space-between", alignItems: "center", backgroundColor: "#9ED2C6", boxShadow: "0.1vh 0.1vh 0.3vh #e42323"}}>
                                        <img src={`https://source.unsplash.com/random/200x200?sig=1`} alt="Game image"/>
                                        <Typography sx={{fontSize: "150%"}}>{item.title}</Typography>
                                        <Typography sx={{fontSize: "150%"}}>{item.hoursOfPlaying.toFixed(2)} hours</Typography>
                                        <Stack>
                                            <PurchasedGameButton color="success" handler={() => handleClickLaunchButton(item.title, item.gameId, item.hoursOfPlaying)}>Launch game</PurchasedGameButton>
                                            <PurchasedGameButton color="error" handler={() => handleClickDeleteButton(item.gameId)}>Delete game from account</PurchasedGameButton>
                                        </Stack>
                                    </Paper>
                                ))
                            }
                        </Stack>
                        :
                        <PageTitle title="You don't have any games :(" />
                    }
                    {showDialog &&
                        <Dialog open={showDialog}>
                            <DialogTitle variant="h3">You're playing in {currentlyPlayingGameTitle}</DialogTitle>
                            <Timer time={time} />
                            <Button color="success" sx={{fontSize: "100%"}} onClick={()=>{setTime(time+60000)}}>+1 minute</Button>
                            <Button color="success" sx={{fontSize: "100%"}} onClick={()=>{setTime(time+3600000)}}>+1 hour</Button>
                            <Button color="error" sx={{fontSize: "100%"}} onClick={() => handleCloseGame()}>Close game</Button>
                        </Dialog>
                    }
                </GlowingGrid>
            </Container>
        </FullscreenGrid>
    );
}
