import React, {useEffect} from "react";
import Header from "../../Components/Header"
import Error from "../StatePages/Error"
import {Container, ImageList, ImageListItem, Typography, Button, Alert, AlertTitle} from "@mui/material";
import FullscreenGrid from "../../Components/FullscreenGrid";
import GlowingGrid from "../../Components/GlowingGrid";
import Loading from "../StatePages/Loading";
import PageTitle from "../../Components/PageTitle";
import Cookies from 'universal-cookie';
import {useNavigate} from 'react-router-dom';
import {GetUserInfo} from "../../Scripts/GetUserInfo";

export default function HomePage() {
    const [games, setGames] = React.useState();
    const [loading, setLoading] = React.useState(true);
    const [error, setError] = React.useState(null);
    const [nickname, setNickname] = React.useState(null);
    const [userLoaded, setUserLoaded] = React.useState(false);
    const [isForbidden, setIsForbidden] = React.useState(false);

    const navigate = useNavigate();
    const cookies = new Cookies();

    const handleClick = (e, gameId) => {
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
            if (response.ok || response.status === 422) {
                return response.json();
            } else {
                throw new Error();
            }
        }).then(decodedResponse => {
            if (decodedResponse['message'] === 'Game already purchased'){
                setIsForbidden(true);
            } else {
                navigate('/purchased-games');
            }
        }).catch(error => {
            console.log(error);
            setError(error);
        }).finally(()=>{
            setLoading(false);
        });
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

    useEffect(() => {
        GetUserInfo(cookies.get('token'))
            .then(decodedResponse => {
                setNickname(decodedResponse['nickname']);
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
                    <Header nickname={nickname} handleLogout={handleLogout} />
                    <PageTitle title="Shop" />
                    <ImageList cols={5} sx={{padding: "1%"}}>
                        {games.map((item, index) => (
                            <Button key={index} sx={{ backgroundColor: "#9ED2C6", boxShadow: "0.1vh 0.1vh 0.3vh #e42323" }} onClick={e => handleClick(e, item.id)}>
                                <ImageListItem>
                                <img
                                    src={`https://source.unsplash.com/random/200x200?sig=1`}
                                    alt="Game image"
                                />
                                    <Typography sx={{
                                        fontSize: "200%",
                                        alignSelf: "center",
                                        marginTop: "5%",
                                        color: "#F7ECDE"
                                    }}>{item.title}</Typography>
                                </ImageListItem>
                            </Button>
                        ))}
                    </ImageList>
                </GlowingGrid>
            </Container>
            {isForbidden &&
                <Alert severity="success" variant="standard" className="alert" sx={{position: "absolute"}}>
                    <AlertTitle>Failure!</AlertTitle>
                    You already has this game!
                    <Button onClick={() => {setIsForbidden(false)}}>Ok</Button>
                </Alert>
            }
        </FullscreenGrid>
    );
}
