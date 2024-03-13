import React from "react";
import {Button as JoyButton} from '@mui/joy';

export default function SubmitButton({buttonText, isLoading})
{
    return(
        <>
            <JoyButton
                loading={isLoading}
                type="submit"
                fullWidth
                variant="solid"
                color="primary"
                size="lg"
                sx={{fontSize: "100%"}}
            >
                {buttonText}
            </JoyButton>
        </>
    )
}

