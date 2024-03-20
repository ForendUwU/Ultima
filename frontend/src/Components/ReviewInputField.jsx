import React from "react";
import {Typography} from "@mui/material";
import {Textarea} from "@mui/joy";

export default function ReviewInputField({defaultValue, setter})
{
    return(
        <>
            <Typography sx={{fontSize: "150%"}}>Review text</Typography>
            <Textarea
                placeholder="Write your review here"
                variant="large"
                size="lg"
                name="content"
                id="content"
                defaultValue={defaultValue}
                onChange={e => setter(e.target.value)}
                sx={{
                    fontSize: "100%",
                    width: "100%",
                    marginBottom: "3%",
                    backgroundColor: "#e9cda2"
                }}
            />
        </>
    );
}
