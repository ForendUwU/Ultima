import React, {useContext, useEffect} from "react";
import {Header, FullscreenGrid, GlowingGrid, PageTitle, GameButtonText} from "../../Components"
import Error from "../StatePages/Error"
import {Container, ImageList, ImageListItem, Button, Autocomplete, TextField} from "@mui/material";
import Loading from "../StatePages/Loading";
import {useNavigate} from 'react-router-dom';
import toast, { Toaster, ToastBar } from 'react-hot-toast';
import {HeaderContext} from "../../App/App";

export default function HomePage() {
    const [games, setGames] = React.useState([]);
    const [loading, setLoading] = React.useState(true);
    const [error, setError] = React.useState(null);

    const headerContext = useContext(HeaderContext);

    const navigate = useNavigate();

    const handleClick = (e, gameId) => {
            navigate('/game/'+gameId);
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

    if (loading || !headerContext.userLoaded) return <Loading />
    if (error) return <Error errorText={error.toString()} />;

    return (
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                    <Header />
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
