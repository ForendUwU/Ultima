import React from "react";
import {Grid} from "@mui/material";

export default function FullscreenGrid({ children })
{
    return(
        <Grid container justifyContent="center" sx={{
            backgroundColor: "#54BAB9",
            minHeight: "100vh",
            height: "100%"
        }}>
            {children}
        </Grid>
    );
}

