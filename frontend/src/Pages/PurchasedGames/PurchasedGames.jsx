import React, {useContext, useEffect} from "react";
import {Container, Stack, Button, Dialog, DialogTitle} from "@mui/material";
import Cookies from 'universal-cookie';
import {FullscreenGrid, GlowingGrid, Header, PageTitle, Stopwatch, PurchasedGameCard} from "../../Components";
import Error from "../StatePages/Error"
import Loading from "../StatePages/Loading";
import {HeaderContext} from "../../App/App";
import useFetch from "../../Hooks/useFetch";
import {doRequest} from "../../Scripts/doRequest";

export default function PurchasedGames() {
    const [showDialog, setShowDialog] = React.useState(false);
    const [currentlyPlayingGameTitle, setCurrentlyPlayingGameTitle] = React.useState();
    const [currentlyPlayingGameId, setCurrentlyPlayingGameId] = React.useState();
    const [time, setTime] = React.useState(0);

    const [update, setUpdate] = React.useState(0);

    const cookies = new Cookies();

    const headerContext = useContext(HeaderContext);

    //Get games purchased by user
    const [games, error, loading] = useFetch({
        url: 'https://localhost/api/purchase-game',
        method: 'GET',
        token: cookies.get('token'),
        updateEffect: update
    });

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

    const handleClickLaunchButton = (gameTitle, gameId, hoursOfPlaying) => {
        setTime(hoursOfPlaying * 3600000);
        setShowDialog(true);
        setCurrentlyPlayingGameTitle(gameTitle);
        setCurrentlyPlayingGameId(gameId);
    }

    const handleClickDeleteButton = (gameId) =>
    {
        doRequest({
            url: 'https://localhost/api/purchase-game',
            method: 'DELETE',
            token: cookies.get('token'),
            body: {
                gameId: gameId
            }
        });
        setUpdate(update+1);
    }

    function handleCloseGame()
    {
        setShowDialog(false);

        doRequest({
            url: 'https://localhost/api/save-playing-time',
            method: 'POST',
            token: cookies.get('token'),
            body: {
                gameId: currentlyPlayingGameId,
                time: time
            }
        });

        setUpdate(update+1);
    }

    if(loading || !headerContext.userLoaded) return <Loading />;
    if(error) return <Error errorText={error.toString()} />;

    return (
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                    <Header />
                    <PageTitle>Purchased games</PageTitle>
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
                        <PageTitle>You don't have any games :(</PageTitle>
                    }
                    {showDialog &&
                        <Dialog open={showDialog}>
                            <DialogTitle variant="h3">You're playing in {currentlyPlayingGameTitle}</DialogTitle>
                            <Stopwatch time={time} />
                            <Button color="success" sx={{fontSize: "100%"}} onClick={()=>{setTime(time+60000)}}>+1 minute</Button>
                            <Button color="success" sx={{fontSize: "100%"}} onClick={()=>{setTime(time+3600000)}}>+1 hour</Button>
                            <Button color="error" sx={{fontSize: "100%"}} onClick={handleCloseGame}>Close game</Button>
                        </Dialog>
                    }
                </GlowingGrid>
            </Container>
        </FullscreenGrid>
    );
}
