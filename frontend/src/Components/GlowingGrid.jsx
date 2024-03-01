import React from "react";
import {Grid, Paper} from "@mui/material";

export default function GlowingGrid({ children, errorStatus=false, maxWidth })
{
    return(
        <Grid item component={Paper} sx={{
            padding: '1%',
            boxShadow: errorStatus ? "0px 0px 50px lightblue, 0px 0px 40px red" : "0px 0px 20px #E9DAC1",
            alignSelf: 'center',
            backgroundColor: "#E9DAC1",
            height: "100%",
            maxWidth: {maxWidth}
        }}>
            {children}
        </Grid>
    );
}
