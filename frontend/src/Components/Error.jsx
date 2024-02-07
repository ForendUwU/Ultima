import React from "react";
import {Typography, Grid, Paper} from "@mui/material";

export default function Error(props) {
    return(
        <Grid container flexDirection="column" alignItems="center" justifyContent="center" height="100vh">
            <Grid item component={Paper} style={{
                boxShadow: "0px 0px 10px black"
            }}>
                <Typography variant="h2">Something went wrong!</Typography>
                <Typography variant="h2">{props.errorText}</Typography>
            </Grid>
        </Grid>
    );
}