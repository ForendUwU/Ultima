import {Button, Grid} from "@mui/material";
import React from "react";

export default function ChangeTypeOfChangingForm({setIsChangePassword, isChangePassword}) {
    return (
        <Grid container alignItems="center" justifyContent="center" sx={{ marginTop: "1%", marginBottom: "1%" }}>
            <Button
                variant="outlined"
                color="warning"
                sx={{fontSize: "100%", marginBottom: "1%"}}
                onClick={() => setIsChangePassword(!isChangePassword)}
            >
                {isChangePassword ? 'Change you data' : 'Change password'}
            </Button>
        </Grid>
    );
}