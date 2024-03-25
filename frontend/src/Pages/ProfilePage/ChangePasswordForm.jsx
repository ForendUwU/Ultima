import {Grid} from "@mui/material";
import React from "react";
import SignedInput from "../../Components/SignedInput";

export default function ChangeDataForm({setOldPassword, setNewPassword, setRepeatPassword}) {
    return (
        <>
            <Grid item>
                <SignedInput
                    inputName="oldPassword"
                    sign="Old password"
                    setter={setOldPassword}
                    required={false}
                    key={5}
                    type="password"
                />
            </Grid>
            <Grid item>
                <SignedInput
                    inputName="newPassword"
                    sign="New password"
                    setter={setNewPassword}
                    required={false}
                    key={6}
                    type="password"
                />
            </Grid>
            <Grid item>
                <SignedInput
                    inputName="repeatPassword"
                    sign="Repeat password"
                    setter={setRepeatPassword}
                    required={false}
                    key={7}
                    type="password"
                />
            </Grid>
        </>
    );
}