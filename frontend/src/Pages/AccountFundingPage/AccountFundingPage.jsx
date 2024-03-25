import React, {useContext} from "react";
import {Container, Stack} from "@mui/material";
import {FullscreenGrid, GlowingGrid, Header, PageTitle, FundingButton} from "../../Components";
import Loading from "../StatePages/Loading";
import Cookies from 'universal-cookie';
import {HeaderContext, UserContext} from "../../App/App";
import {doRequest} from "../../Scripts/doRequest";
import Error from "../StatePages/Error"

export default function AccountFundingPage() {
    const cookies = new Cookies();
    const [fundingError, setFundingError] = React.useState();

    const headerContext = useContext(HeaderContext);
    const userContext = useContext(UserContext);

    function handleClick (amount) {
        const [data, error] = doRequest({
            url: 'https://localhost/api/user/fund',
            method: 'POST',
            token: cookies.get('token'),
            body: {amount: amount}
        });

        setFundingError(error);

        data.then(
            decodedResponse => {
                userContext.setUserInfo((previousInfo) => ({
                    ...previousInfo,
                    balance: decodedResponse['newAmount']}))
            }
        );
    }

    if(!headerContext.userLoaded) return <Loading />;
    if(fundingError) return <Error errorText={fundingError.toString()} />;

    return (
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                    <Header updatedUserContext={userContext} />
                    <PageTitle>Account funding</PageTitle>
                    <Stack spacing={2}>
                        <FundingButton amount={5} handleClick={() => handleClick(5)} />
                        <FundingButton amount={10} handleClick={() => handleClick(10)} />
                        <FundingButton amount={20} handleClick={() => handleClick(20)} />
                        <FundingButton amount={50} handleClick={() => handleClick(50)} />
                        <FundingButton amount={100} handleClick={() => handleClick(100)} />
                    </Stack>
                </GlowingGrid>
            </Container>
        </FullscreenGrid>
    );
}
