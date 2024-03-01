import React, {useEffect} from "react";
import Header from "../../Components/Header"
import Error from "../StatePages/Error"
import {Container, Stack} from "@mui/material";
import FullscreenGrid from "../../Components/FullscreenGrid";
import GlowingGrid from "../../Components/GlowingGrid";
import Loading from "../StatePages/Loading";
import Cookies from 'universal-cookie';
import PageTitle from "../../Components/PageTitle";
import {GetUserInfo} from "../../Scripts/GetUserInfo";
import FundingButton from "../../Components/FundingButton";
import {useNavigate} from "react-router-dom";

export default function AccountFundingPage() {
    const [loading, setLoading] = React.useState(true);
    const [error, setError] = React.useState(null);
    const [nickname, setNickname] = React.useState(null);
    const [balance, setBalance] = React.useState(null);
    const [userLoaded, setUserLoaded] = React.useState(false);

    const cookies = new Cookies();
    const navigate = useNavigate();

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

    useEffect(() => {
        GetUserInfo(cookies.get('token'))
            .then(decodedResponse => {
                setNickname(decodedResponse['nickname']);
                setBalance(decodedResponse['balance']);
            }).catch(error => {
            setError(error);
        }).finally(()=>{
            setUserLoaded(true);
            setLoading(false);
        })
    },[]);

    function handleLogout()
    {
        const cookies = new Cookies();
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);

        fetch('https://localhost/api/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + cookies.get('token')
            }
        }).then(response => {
            return response.json();
        }).then(decodedResponse => {
            console.log(decodedResponse);
        });

        cookies.set('token', '', {expires: yesterday});
        cookies.set('userId', '', {expires: yesterday});
        navigate('/');
    }

    if(loading || !userLoaded) return <Loading />;
    if(error) return <Error errorText={error.toString()} />;

    return (
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                    <Header nickname={nickname} balance={balance} handleLogout={handleLogout} />
                    <PageTitle title="Account funding" />
                    <Stack spacing={2}>
                        <FundingButton price={5} handleClick={() => handleClick(5)} />
                        <FundingButton price={10} handleClick={() => handleClick(10)} />
                        <FundingButton price={20} handleClick={() => handleClick(20)} />
                        <FundingButton price={50} handleClick={() => handleClick(50)} />
                        <FundingButton price={100} handleClick={() => handleClick(100)} />
                    </Stack>
                </GlowingGrid>
            </Container>
        </FullscreenGrid>
    );
}
