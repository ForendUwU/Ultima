import React, {useContext} from "react";
import {Header, FullscreenGrid, GlowingGrid, PageTitle, GameCard} from "../../Components"
import Error from "../StatePages/Error"
import {Container, ImageList} from "@mui/material";
import Loading from "../StatePages/Loading";
import {useNavigate} from 'react-router-dom';
import {HeaderContext} from "../../App/App";
import useFetch from "../../Hooks/useFetch";

export default function HomePage() {
    const headerContext = useContext(HeaderContext);

    const navigate = useNavigate();

    const handleClick = (gameId) => {
        navigate('/game/'+gameId);
    }

    const [games, error, loading] = useFetch({
        url: 'https://localhost/api/games',
        method: 'GET'
    })

    if (loading || !headerContext.userLoaded) return <Loading />
    if (error) return <Error errorText={error.toString()} />;

    return (
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                    <Header />
                    <PageTitle>Shop</PageTitle>
                    <ImageList cols={5} sx={{padding: "1%"}}>
                        {games.map((item) => (
                            <GameCard item={item} handleClick={handleClick} showPrice={true} showPlayingTime={false} />
                        ))}
                    </ImageList>
                </GlowingGrid>
            </Container>
        </FullscreenGrid>
    );
}
