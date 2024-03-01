import React from "react";
import {Typography} from "@mui/material";

export default function GameButtonText({ children })
{
    return(
        <Typography sx={{
            fontSize: "200%",
            alignSelf: "center",
            marginTop: "5%",
            color: "black"
        }}>
            {children}
        </Typography>
    );
}
