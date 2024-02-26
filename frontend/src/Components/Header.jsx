import React from "react";
import {Grid, Typography, Button} from "@mui/material";
import HeaderButton from "./HeaderButton";

export default function Header({nickname, handleLogout}){
    return (
        <Grid container spacing={2} alignItems="center">
            <Grid item xs="auto" style={{flexGrow: 1}}>
                <Button href="/">
                    <Typography variant="h1" color="#54BAB9" sx={{textShadow: "0.1vh 0.1vh 0.2vh #e42323"}}>
                        Ultima
                    </Typography>
                </Button>
            </Grid>
            <HeaderButton link="/">
                Home
            </HeaderButton>
            {!nickname ?
                <HeaderButton link="/sign-in">
                    Sign In
                </HeaderButton>
                :
                <>
                    <HeaderButton link="/purchased-games">
                        {nickname}
                    </HeaderButton>
                    <HeaderButton handler={handleLogout}>
                        Logout
                    </HeaderButton>
                </>
            }
        </Grid>
    )
}
