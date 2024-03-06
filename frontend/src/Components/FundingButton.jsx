import React from "react";
import {Button} from "@mui/material";

export default function FundingButton({handleClick, amount})
{
    return(
        <Button
            type="submit"
            variant="outlined"
            color="primary"
            size="large"
            sx={{fontSize: "120%"}}
            onClick={handleClick}
        >
            {amount}$
        </Button>
    )
}
