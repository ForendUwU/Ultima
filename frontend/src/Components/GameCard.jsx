import {Button, ImageListItem} from "@mui/material";
import React from "react";
import {GameButtonText} from "./index";

export default function GameCard({item, handleClick = null, showPrice, showPlayingTime})
{
    return (
        <Button
            key={item.id}
            sx={{ backgroundColor: "#9ED2C6", boxShadow: "0.1vh 0.1vh 0.3vh #e42323" }}
            onClick={() => handleClick(item.id)}
            disabled={!handleClick}
        >
            <ImageListItem>
                <img
                    src={'https://source.unsplash.com/random/200x200?sig='+item.id}
                    alt="Game image"
                />
                <GameButtonText>{item.title}</GameButtonText>
                {showPrice && <GameButtonText>{item.price}$</GameButtonText>}
                {showPlayingTime && <GameButtonText>{item.hoursOfPlaying.toFixed(2)}hours</GameButtonText>}
            </ImageListItem>
        </Button>
    )
}
