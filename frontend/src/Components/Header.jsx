import React, {useContext} from "react";
import {Grid, Typography, Button} from "@mui/material";
import HeaderButton from "./HeaderButton";
import {HeaderContext, UserContext} from "../App/App";

export default function Header(){
    const headerContext = useContext(HeaderContext);
    const userContext = useContext(UserContext);

    return (
        <Grid container spacing={2} alignItems="center">
            <Grid item xs="auto" style={{flexGrow: 1}}>
                <Button href="/">
                    <Typography variant="h1" color="#54BAB9" sx={{textShadow: "0.1vh 0.1vh 0.2vh #e42323"}}>
                        Ultima
                    </Typography>
                </Button>
            </Grid>
            {!userContext.userInfo ?
                <HeaderButton link="/sign-in">
                    Sign In
                </HeaderButton>
                :
                <>
                    <HeaderButton link="/purchased-games">
                        Your games
                    </HeaderButton>
                    <HeaderButton link="/profile">
                        {userContext.userInfo.nickname}
                    </HeaderButton>
                    <HeaderButton link="/account-funding">
                        {userContext.userInfo.balance}$
                    </HeaderButton>
                    <HeaderButton handler={headerContext.handleLogout}>
                        Logout
                    </HeaderButton>
                </>
            }
        </Grid>
    )
}
