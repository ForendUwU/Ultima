import React, {useContext, useEffect} from "react";
import {useNavigate} from "react-router-dom";
import {Container, Stack, Button, Dialog, DialogTitle} from "@mui/material";
import Cookies from 'universal-cookie';
import {FullscreenGrid, GlowingGrid, Header, PageTitle, Stopwatch, PurchasedGameCard} from "../../Components";
import Error from "../StatePages/Error"
import Loading from "../StatePages/Loading";
import {HeaderContext} from "../../App/App";

export default function PurchasedGames() {
    const [games, setGames] = React.useState([]);
    const [loading, setLoading] = React.useState(true);
    const [error, setError] = React.useState(null);
    const [showDialog, setShowDialog] = React.useState(false);
    const [currentlyPlayingGameTitle, setCurrentlyPlayingGameTitle] = React.useState();
    const [currentlyPlayingGameId, setCurrentlyPlayingGameId] = React.useState();
    const [time, setTime] = React.useState(0);

    const cookies = new Cookies();
    const navigate = useNavigate();

    const headerContext = useContext(HeaderContext);

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

    //Stopwatch
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

    if(loading || !headerContext.userLoaded) return <Loading />;
    if(error) return <Error errorText={error.toString()} />;

    return (
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                    <Header />
                    <PageTitle title="Purchased games" />
                    {games && games.length !== 0 ?
                        <Stack spacing={2}>
                            {
                                games.map((item, index) => (
                                    <PurchasedGameCard
                                        item={item}
                                        index={index}
                                        launchHandler={handleClickLaunchButton}
                                        deleteHandler={handleClickDeleteButton}
                                    />
                                ))
                            }
                        </Stack>
                        :
                        <PageTitle title="You don't have any games :(" />
                    }
                    {showDialog &&
                        <Dialog open={showDialog}>
                            <DialogTitle variant="h3">You're playing in {currentlyPlayingGameTitle}</DialogTitle>
                            <Stopwatch time={time} />
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
