import {Button, Grid, Typography} from "@mui/material";
import {PurchasedGameButton} from "../../Components";
import ThumbUpIcon from "@mui/icons-material/ThumbUp";
import ThumbDownIcon from "@mui/icons-material/ThumbDown";
import React from "react";

export default function GameDescription({gameInfo, HandlePurchase}) {
    return (
        <Grid container columnSpacing={{ xs: 1, sm: 2, md: 3 }} justifyContent="center">
            <Grid item xs={6}>
                <Grid container direction="column" sx={{height: "100%"}} wrap="nowrap">
                    <Grid item alignSelf="center">
                        <Typography variant="h3">About the game</Typography>
                    </Grid>
                    <Grid item sx={{marginTop: "3%", height: "100%"}}>
                        <Typography variant="h4">{gameInfo.description}</Typography>
                    </Grid>
                    <Grid item>
                        <PurchasedGameButton handler={HandlePurchase} color="success">{"Buy for "+gameInfo.price + "$"}</PurchasedGameButton>
                    </Grid>
                </Grid>
            </Grid>
            <Grid item xs={6}>
                <img
                    src={'https://source.unsplash.com/random/550x300?sig=1'}
                    alt="Game image"
                />
            </Grid>
            <Grid item sx={{ marginTop: "3%" }}>
                <Button color="success" sx={{ width: "50%", height: "150%" }}>
                    <ThumbUpIcon />
                    <Typography sx={{ marginLeft: "10%", fontSize: "150%" }}>{gameInfo.likes}</Typography>
                </Button>
                <Button color="error" sx={{ width: "50%", height: "150%" }}>
                    <ThumbDownIcon />
                    <Typography sx={{ marginLeft: "10%", fontSize: "150%" }}>{gameInfo.dislikes}</Typography>
                </Button>
            </Grid>
        </Grid>
    );
}