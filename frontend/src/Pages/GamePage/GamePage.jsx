import React, {useContext, useEffect} from "react";
import {useParams} from "react-router-dom";
import {FullscreenGrid, GlowingGrid, Header, PageTitle, PurchasedGameButton} from "../../Components";
import {Button, Container, Grid, Typography} from "@mui/material";
import Cookies from "universal-cookie";
import Loading from "../StatePages/Loading";
import Error from "../StatePages/Error";
import toast, { Toaster, ToastBar } from 'react-hot-toast';
import {useNavigate} from 'react-router-dom';
import {HeaderContext} from "../../App/App";

export default function GamePage() {
    const [error, setError] = React.useState(null);
    const [loading, setLoading] = React.useState(true);
    const [gameInfo, setGameInfo] = React.useState();

    const { gameId } = useParams();
    const navigate = useNavigate();
    const context = useContext(HeaderContext);

    const cookies = new Cookies();

    useEffect(() => {
        fetch('https://localhost/api/games/'+gameId, {
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
            setGameInfo(decodedResponse);
        }).catch(error => {
            setError(error);
        }).finally(()=>{
            setLoading(false);
        });
    }, []);

    const handleClick = () => {
        if (cookies.get('token')) {
            fetch('https://localhost/api/purchase-game', {
                method: 'POST',
                body: JSON.stringify({
                    gameId: gameId,
                }),
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + cookies.get('token')
                }
            }).then(response => {
                if (response.ok || response.status === 422 || response.status === 403) {
                    return response.json();
                } else {
                    throw new Error();
                }
            }).then(decodedResponse => {
                if (decodedResponse['message'] === 'Game already purchased'){
                    toast.error(decodedResponse['message'], {duration: 2500});
                } else if (decodedResponse['message'] === 'Not enough money') {
                    toast.error(decodedResponse['message'], {duration: 2500});
                } else {
                    navigate('/purchased-games');
                }
            }).catch(error => {
                console.log(error);
                setError(error);
            }).finally(()=>{
                setLoading(false);
            });
        } else {
            toast.error('You must be authorized to buy games', {duration: 2500});
        }
    }

    if(loading || !context.userLoaded) return <Loading />;
    if(error) return <Error errorText={error.toString()} />;

    return (
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                    <Header />
                    <PageTitle title={gameInfo.title} />
                    <Grid container columnSpacing={{ xs: 1, sm: 2, md: 3 }}>
                        <Grid item xs={6}>
                            <Grid container direction="column" sx={{height: "100%"}} wrap="nowrap">
                                <Grid item alignSelf="center">
                                    <Typography variant="h3">About the game</Typography>
                                </Grid>
                                <Grid item sx={{marginTop: "3%", height: "100%"}}>
                                    <Typography variant="h4">{gameInfo.description}</Typography>
                                </Grid>
                                <Grid item>
                                    <PurchasedGameButton handler={handleClick} color="success">{"Buy for "+gameInfo.price + "$"}</PurchasedGameButton>
                                </Grid>
                            </Grid>
                        </Grid>
                        <Grid item xs={6}>
                            <img
                                src={'https://source.unsplash.com/random/550x300?sig=1'}
                                alt="Game image"
                            />
                        </Grid>
                    </Grid>
                </GlowingGrid>
            </Container>
            <Toaster>
                {(t) => (
                    <ToastBar toast={t}>
                        {({ icon, message }) => (
                            <>
                                {icon}
                                {message}
                                {t.message === 'You must be authorized to buy games' && (
                                    <Button variant="outlined" color="error" sx={{ width: "30%", fontSize: "100%" }} onClick={() => {toast.dismiss(t.id); navigate('/sign-in')}}>Sign In</Button>
                                )}
                            </>
                        )}
                    </ToastBar>
                )}
            </Toaster>
        </FullscreenGrid>
    );
}