import React from "react";
import {Grid, Paper} from "@mui/material";

export default function GlowingGrid({ children, errorStatus=false })
{
    return(
        <Grid item component={Paper} style={{
            padding: '1%',
            boxShadow: errorStatus ? "0px 0px 50px lightblue, 0px 0px 40px red" : "0px 0px 50px lightblue, 0px 0px 40px lightblue",
            alignSelf: 'center'
        }}>
            {children}
        </Grid>
    );
}
