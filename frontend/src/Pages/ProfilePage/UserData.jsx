import {Avatar, Button, Grid, Typography} from "@mui/material";
import React from "react";

export default function UserData({setIsProfileSettingsOpened, isProfileSettingsOpened, userContext}) {
    return (
        <Grid container alignItems="center" direction="column">
            <Grid item>
                <Avatar alt={userContext.userInfo.nickname} src="/static/images/avatar/1.jpg" sx={{fontSize: "190%", width: 180, height: 180}} />
            </Grid>
            <Grid item>
                <Typography variant="h3" sx={{paddingTop: "20%"}}>{userContext.userInfo.nickname}</Typography>
            </Grid>
            <Grid item>
                <Button
                    variant="outlined"
                    color="success"
                    sx={{marginTop: "10%", fontSize: "100%"}}
                    onClick={() => setIsProfileSettingsOpened(!isProfileSettingsOpened)}
                >
                    Profile settings
                </Button>
            </Grid>
        </Grid>
    );
}