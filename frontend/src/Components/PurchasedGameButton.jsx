import React from "react";
import {Button} from "@mui/material";

export default function PurchasedGameButton({ children, color, handler })
{
    return(
        <Button onClick={handler} variant="outlined" color={color} size="large" sx={{
            width: "100%",
            fontSize: "100%",
            marginBottom: "1%",
        }}>
            {children}
        </Button>
    );
}
