import React from "react";
import {OutlinedInput} from "@mui/material";

export default function TextInput({inputName, type, setter, defaultValue = null, required = true})
{
    return(
        <OutlinedInput
            required={required}
            fullWidth
            defaultValue={defaultValue}
            id={inputName}
            name={inputName}
            type={type || inputName}
            onChange={e => setter && setter(e.target.value)}
            sx={{
                height: "6vh",
                fontSize: "100%",
                margin: "2% 2% 5% 0%",
                borderRadius: "10px",
            }}
        />
    );
}
