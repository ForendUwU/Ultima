import {Grid} from "@mui/material";
import React from "react";
import SignedInput from "../../Components/SignedInput";

export default function ChangeDataForm({setNickname, setFirstName, setLastName, setEmail, userContext}) {
    return (
        <>
            <Grid item>
                <SignedInput
                    inputName="nickname"
                    sign="Nickname"
                    setter={setNickname}
                    required={true}
                    defaultValue={userContext.userInfo.nickname}
                    key={1}
                />
            </Grid>
            <Grid item>
                <SignedInput
                    inputName="firstName"
                    sign="First name"
                    setter={setFirstName}
                    required={false}
                    defaultValue={userContext.userInfo.firstName}
                    key={2}
                />
            </Grid>
            <Grid item>
                <SignedInput
                    inputName="lastName"
                    sign="Last name"
                    setter={setLastName}
                    required={false}
                    defaultValue={userContext.userInfo.lastName}
                    key={3}
                />
            </Grid>
            <Grid item>
                <SignedInput
                    inputName="email"
                    sign="Email"
                    setter={setEmail}
                    required={true}
                    defaultValue={userContext.userInfo.email}
                    key={4}
                />
            </Grid>
        </>
    );
}