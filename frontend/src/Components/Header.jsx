import React, {useContext} from "react";
import {Grid, Typography, Button, Menu, MenuItem} from "@mui/material";
import HeaderButton from "./HeaderButton";
import {HeaderContext} from "../App/App";

export default function Header(){
    const context = useContext(HeaderContext);

    return (
        <Grid container spacing={2} alignItems="center">
            <Grid item xs="auto" style={{flexGrow: 1}}>
                <Button href="/">
                    <Typography variant="h1" color="#54BAB9" sx={{textShadow: "0.1vh 0.1vh 0.2vh #e42323"}}>
                        Ultima
                    </Typography>
                </Button>
            </Grid>
            {!context.nickname ?
                <HeaderButton link="/sign-in">
                    Sign In
                </HeaderButton>
                :
                <>
                    <HeaderButton link="/purchased-games">
                        Your games
                    </HeaderButton>
                    <HeaderButton link="/user/profile">
                        {context.nickname}
                    </HeaderButton>
                    <HeaderButton link="/account-funding">
                        {context.balance}$
                    </HeaderButton>
                    <HeaderButton handler={context.handleLogout}>
                        Logout
                    </HeaderButton>
                </>
            }
        </Grid>
    )
}
