import React from "react";
import {OutlinedInput} from "@mui/material";

export default function TextInput({inputName})
{
    return(
        <OutlinedInput
            required
            fullWidth
            id={inputName}
            name={inputName}
            type={inputName}
            sx={{
                height: "6vh",
                fontSize: "100%",
                margin: "2% 2% 5% 0%",
                borderRadius: "10px",
            }}
        />
    );
}