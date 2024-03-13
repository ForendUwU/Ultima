import React from "react";
import {Grid, Typography} from "@mui/material";

export default function PageTitle({children}){
    return(
        <Grid container justifyContent="center">
            <Grid item sx={{
                marginTop: "3%",
                marginBottom: "3%",
            }}>
                <Typography variant="h2" sx={{
                    color: "#54BAB9FF",
                    textShadow: "0.1vh 0.1vh 0.2vh #e42323"
                }}>
                    {children}
                </Typography>
            </Grid>
        </Grid>
    );
}