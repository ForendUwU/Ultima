import {Button} from "@mui/material";
import React from "react";

export default function SubmitButton({buttonText})
{
    return(
        <Button
            type="submit"
            fullWidth
            variant="outlined"
            color="primary"
            size="large"
            sx={{fontSize: "100%"}}
        >
            {buttonText}
        </Button>
    )
}

