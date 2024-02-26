import React from "react";
import {Typography, Alert, Container} from "@mui/material";
import FullscreenGrid from "../../Components/FullscreenGrid";
import GlowingGrid from "../../Components/GlowingGrid";

export default function Error(props) {
    return(
        <FullscreenGrid>
            <GlowingGrid errorStatus>
                <Container maxWidth="lg">
                    <Alert severity="error" variant="outlined" sx={{
                        alignSelf: "center"
                    }}>
                        <Typography variant="h2">Fire a programmer because of {props.errorText}</Typography>
                    </Alert>
                </Container>
            </GlowingGrid>
        </FullscreenGrid>
    );
}