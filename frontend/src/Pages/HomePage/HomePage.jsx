import React, {useEffect} from "react";
import Header from "../../Components/Header"
import Error from "../Error"
import { Container, ImageList, ImageListItem, ImageListItemBar, Typography }  from "@mui/material";
import FullscreenGrid from "../../Components/FullscreenGrid";
import GlowingGrid from "../../Components/GlowingGrid";
import Loading from "../Loading";

export default function HomePage() {
    const [games, setGames] = React.useState();
    const [loading, setLoading] = React.useState(true);
    const [error, setError] = React.useState(null);

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
        })
    },[]);

    if(loading) return <Loading />;
    if(error) return <Error errorText={error.toString()} />;

    return (
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                <Header />
                <ImageList cols={6}>
                    {games.map((item, index) => (
                    <ImageListItem key={index}>
                        <img
                            src={`https://source.unsplash.com/random/200x200?sig=1`}
                            alt="Game image"
                        />
                        <Typography sx={{
                            fontSize: "100%",
                            alignSelf: "center",
                            marginTop: "5%"
                        }}>{item.title}</Typography>
                    </ImageListItem>
                    ))}
                </ImageList>
                </GlowingGrid>
            </Container>
        </FullscreenGrid>
    );
}
