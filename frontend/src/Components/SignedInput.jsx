import {Typography} from "@mui/material";
import React from "react";
import {TextInput} from "./index";

export default function SignedInput({
    inputName,
    sign,
    setter = null,
    type = null,
    defaultValue = null,
    required = true,
}) {
    return(
        <>
            <Typography variant="h5" style={{marginLeft: "1%"}}>{sign}</Typography>
            <TextInput
                inputName={inputName}
                type={type}
                setter={setter}
                defaultValue={defaultValue}
                required={required}
            />
        </>
    );
}
