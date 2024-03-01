import React, {useEffect} from "react";
import Header from "../../Components/Header"
import Error from "../StatePages/Error"
import {Container, ImageList, ImageListItem, Button} from "@mui/material";
import FullscreenGrid from "../../Components/FullscreenGrid";
import GlowingGrid from "../../Components/GlowingGrid";
import Loading from "../StatePages/Loading";
import PageTitle from "../../Components/PageTitle";
import Cookies from 'universal-cookie';
import {useNavigate} from 'react-router-dom';
import {GetUserInfo} from "../../Scripts/GetUserInfo";
import GameButtonText from "../../Components/GameButtonText";
import toast, { Toaster, ToastBar } from 'react-hot-toast';

export default function HomePage() {
    const [games, setGames] = React.useState([]);
    const [loading, setLoading] = React.useState(true);
    const [error, setError] = React.useState(null);
    const [nickname, setNickname] = React.useState(null);
    const [balance, setBalance] = React.useState(null);
    const [userLoaded, setUserLoaded] = React.useState(false);

    const navigate = useNavigate();
    const cookies = new Cookies();

    const handleClick = (e, gameId) => {
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
        });

        cookies.set('token', '', {expires: yesterday});
        cookies.set('userId', '', {expires: yesterday});
        navigate(0);
    }

    useEffect(() => {
        fetch('https://localhost/api/games', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok || response.status === 401) {
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
    }, []);

    if(loading || !userLoaded) return <Loading />;
    if(error) return <Error errorText={error.toString()} />;

    return (
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                    <Header nickname={nickname} balance={balance} handleLogout={handleLogout} />
                    <PageTitle title="Shop" />
                    <ImageList cols={5} sx={{padding: "1%"}}>
                        {games.map((item, index) => (
                            <Button key={index} sx={{ backgroundColor: "#9ED2C6", boxShadow: "0.1vh 0.1vh 0.3vh #e42323" }} onClick={e => handleClick(e, item.id)}>
                                <ImageListItem>
                                <img
                                    src={'https://source.unsplash.com/random/200x200?sig='+index}
                                    alt="Game image"
                                />
                                    <GameButtonText>{item.title}</GameButtonText>
                                    <GameButtonText>{item.price}$</GameButtonText>
                                </ImageListItem>
                            </Button>
                        ))}
                    </ImageList>
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
