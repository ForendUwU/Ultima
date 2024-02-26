import React from "react";
import {Grid, Typography, Button} from "@mui/material";

export default function HeaderButton({ children, variant, link, handler }) {
    return (
        <Grid item xs="auto" align="right" justifyContent="flex-end">
            <Button href={link} onClick={handler}>
                <Typography variant={variant || "h3"} color="#54BAB9" sx={{
                    textShadow: "0.1vh 0.1vh 0.2vh #e42323"
                }}>
                    {children}
                </Typography>
            </Button>
        </Grid>
    );
}