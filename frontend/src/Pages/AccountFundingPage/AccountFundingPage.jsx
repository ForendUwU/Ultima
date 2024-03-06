import React, {useContext} from "react";
import Error from "../StatePages/Error"
import {Container, Stack} from "@mui/material";
import {FullscreenGrid, GlowingGrid, Header, PageTitle, FundingButton} from "../../Components";
import Loading from "../StatePages/Loading";
import Cookies from 'universal-cookie';
import {useNavigate} from "react-router-dom";
import {HeaderContext} from "../../App/App";

export default function AccountFundingPage() {
    const [error, setError] = React.useState(null);

    const cookies = new Cookies();
    const navigate = useNavigate();
    const context = useContext(HeaderContext);

    function handleClick (amount) {
            fetch('https://localhost/api/fund', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + cookies.get('token')
                },
                body: JSON.stringify({
                    amount: amount
                })
            }).then(response => {
                return response.json();
            }).then(decodedResponse => {
                console.log(decodedResponse);
            }).finally(()=>{navigate(0);});
    }

    if(!context.userLoaded) return <Loading />;
    if(error) return <Error errorText={error.toString()} />;

    return (
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                    <Header />
                    <PageTitle title="Account funding" />
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
