import React from "react";
import {Button} from "@mui/material";

export default function FundingButton({handleClick, price})
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
            {price}$
        </Button>
    )
}
