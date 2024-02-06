import React, {useEffect} from "react";
import { Link } from "react-router-dom";
import Header from "../Components/Header"
import { Grid, Typography, Container, ImageList, ImageListItem, ImageListItemBar }  from "@mui/material";

export default function HomePage() {
    const [games, setGames] = React.useState();
    const [loading, setLoading] = React.useState(true);

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
        }).finally(()=>{
            setLoading(false);
        })
    },[]);

    if(loading) return "Loading...";

    return (
        <Container maxWidth="lg">
            <Header />
            <ImageList cols={6}>
                {games.map((item) => (
                <ImageListItem>
                    <img
                        src={`https://random.imagecdn.app/v1/image?width=500&height=500`}
                        alt="Game image"
                    />
                    <ImageListItemBar position="below" title={item.title} sx={{
                        fontSize: "10vh"
                    }}/>
                </ImageListItem>
                ))}
            </ImageList>
        </Container>
    );
}
