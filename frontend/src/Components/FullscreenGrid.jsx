import React from "react";
import {Grid} from "@mui/material";

export default function FullscreenGrid({ children })
{
    return(
        <Grid container justifyContent="center" style={{
            height: '100vh',
            backgroundColor: "black"
        }}>
            {children}
        </Grid>
    );
}

