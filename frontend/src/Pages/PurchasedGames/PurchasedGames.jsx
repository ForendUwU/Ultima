import React, {useEffect} from "react";
import Header from "../../Components/Header"
import Error from "../StatePages/Error"
import {Container, ImageList, ImageListItem, Typography} from "@mui/material";
import FullscreenGrid from "../../Components/FullscreenGrid";
import GlowingGrid from "../../Components/GlowingGrid";
import Loading from "../StatePages/Loading";
import Cookies from 'universal-cookie';
import PageTitle from "../../Components/PageTitle";
import {GetUserInfo} from "../../Scripts/GetUserInfo";
import {useNavigate} from "react-router-dom";

export default function PurchasedGames() {
    const [games, setGames] = React.useState([]);
    const [loading, setLoading] = React.useState(true);
    const [error, setError] = React.useState(null);
    const [nickname, setNickname] = React.useState(null);
    const [userLoaded, setUserLoaded] = React.useState(false);

    const cookies = new Cookies();
    const navigate = useNavigate();

    useEffect(() => {
        fetch('https://localhost/api/purchase-game', {
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
        })
    },[]);

    useEffect(() => {
        GetUserInfo(cookies.get('token'))
            .then(decodedResponse => {
                setNickname(decodedResponse['nickname']);
            }).catch(error => {
                setError(error);
            }).finally(()=>{
                setUserLoaded(true);
            })
    },[]);

    function handleLogout()
    {
        const cookies = new Cookies();
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);

        fetch('https://localhost/api/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' . cookies.get('token')
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

    if(loading || !userLoaded) return <Loading />;
    if(error) return <Error errorText={error.toString()} />;

    return (
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                    <Header nickname={nickname} handleLogout={handleLogout} />
                    <PageTitle title="Purchased games" />
                        {games && games.length !== 0 ?
                            <ImageList cols={6}>
                                {
                                    games.map((item, index) => (
                                        <ImageListItem key={index}>
                                            <img
                                                src={`https://source.unsplash.com/random/200x200?sig=1`}
                                                alt="Game image"
                                            />
                                        <Typography sx={{
                                            fontSize: "100%",
                                            alignSelf: "center",
                                            marginTop: "5%"
                                        }}>
                                            {item.title}
                                        </Typography>
                                        </ImageListItem>
                                    ))
                                }
                            </ImageList>
                        :
                            <PageTitle title="You don't have any games :(" />
                        }

                </GlowingGrid>
            </Container>
        </FullscreenGrid>
    );
}
