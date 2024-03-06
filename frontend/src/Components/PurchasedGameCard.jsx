import React from "react";
import {Button, Paper, Stack, Typography} from "@mui/material";
import {PurchasedGameButton} from "../Components";

export default function PurchasedGameCard({ item, index, launchHandler, deleteHandler })
{
    return(
        <Paper key={index} elevation={3} sx={{padding: "1%", display: "flex", justifyContent: "space-between", alignItems: "center", backgroundColor: "#9ED2C6", boxShadow: "0.1vh 0.1vh 0.3vh #e42323"}}>
            <img src={`https://source.unsplash.com/random/200x200?sig=1`} alt="Game image"/>
            <Typography sx={{fontSize: "150%"}}>{item.title}</Typography>
            <Typography sx={{fontSize: "150%"}}>{item.hoursOfPlaying.toFixed(2)} hours</Typography>
            <Stack>
                <PurchasedGameButton color="success" handler={() => launchHandler(item.title, item.gameId, item.hoursOfPlaying)}>Launch game</PurchasedGameButton>
                <PurchasedGameButton color="error" handler={() => deleteHandler(item.gameId)}>Delete game from account</PurchasedGameButton>
            </Stack>
        </Paper>
    );
}
