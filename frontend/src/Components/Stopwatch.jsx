import React from "react";
import {Grid, Typography} from "@mui/material";

export default function Stopwatch(props) {
    return (
        <Grid container justifyContent="center">
            <Typography sx={{fontSize: "150%"}}>
                {("0" + Math.floor((props.time / 3600000) % 60)).slice(-2)}:
            </Typography>
            <Typography sx={{fontSize: "150%"}}>
                {("0" + Math.floor((props.time / 60000) % 60)).slice(-2)}:
            </Typography>
            <Typography sx={{fontSize: "150%"}}>
                {("0" + Math.floor((props.time / 1000) % 60)).slice(-2)}
            </Typography>
        </Grid>
    );
}
