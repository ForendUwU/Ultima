import React, {useContext} from "react";
import {useParams} from "react-router-dom";
import {FullscreenGrid, GlowingGrid, Header, PageTitle} from "../../Components";
import {Button, Container } from "@mui/material";
import Cookies from "universal-cookie";
import Loading from "../StatePages/Loading";
import Error from "../StatePages/Error";
import toast, { Toaster, ToastBar } from 'react-hot-toast';
import {useNavigate} from 'react-router-dom';
import {HeaderContext} from "../../App/App";
import useFetch from "../../Hooks/useFetch";
import GameDescription from "./GameDescription";
import Reviews from "./Reviews";
import {doRequest} from "../../Scripts/doRequest";

export default function GamePage() {
    const [error, setError] = React.useState(null);
    const [updateEffect, setUpdateEffect] = React.useState(0);

    const { gameId } = useParams();
    const headerContext = useContext(HeaderContext);
    const navigate = useNavigate();
    const cookies = new Cookies();

    const [gameInfo, gameInfoError, gameInfoLoading] = useFetch({
        url: 'https://localhost/api/games/',
        urlProp: gameId,
        method: 'GET'
    });
     gameInfoError && setError(error.toString() + "\n" + gameInfoError.toString());

    const [reviews, reviewsError, reviewsLoading] = useFetch({
        url: 'https://localhost/api/reviews/',
        urlProp: gameId,
        method: 'GET',
        updateEffect: updateEffect
    });
    reviewsError && setError(error.toString() + "\n" + reviewsError.toString());

    const [currentUserReviewContent, userReviewError, userReviewLoading] = useFetch({
        url: 'https://localhost/api/user/review/',
        urlProp: gameId,
        method: 'GET',
        token: cookies.get('token'),
        tokenFlag: false,
        updateEffect: updateEffect
    });
    userReviewError && setError(error.toString() + "\n" + userReviewError.toString());

    const HandlePurchase = () => {
        if (cookies.get('token')) {
            const [data] = doRequest({
                url: 'https://localhost/api/purchase-game',
                method: 'POST',
                token: cookies.get('token'),
                body: {gameId: gameId},
                tokenFlag: false
            });

            data.then(decodedResponse => {
                if (decodedResponse['message'] === 'Game already purchased') {
                    toast.error('Game already purchased');
                } else {
                    window.location.replace('/purchased-games');
                }
            });
        } else {
            toast.error('You must be authorized to buy games', {duration: 2500});
        }
    }

    const handleDelete = () => {
        doRequest({
            url: 'https://localhost/api/reviews/',
            urlProp: gameId,
            method: 'DELETE',
            token: cookies.get('token'),
            tokenFlag: false,
        });
        toast.success('Review deleted');
        setUpdateEffect(updateEffect+1);
    }

    const handleCreateOrUpdateReview = (reviewData) => {
        let validated = false;

        if (reviewData === "") {
            toast.error("Content of review mustn't be empty");
        } else {
            validated = true;
        }

        if (validated) {
            if (!currentUserReviewContent?.message) {
                doRequest({
                    url: 'https://localhost/api/reviews/',
                    urlProp: gameId,
                    method: 'POST',
                    token: cookies.get('token'),
                    body: {content: reviewData},
                    tokenFlag: false,
                });
                toast.success('Review created');
            } else {
                doRequest({
                    url: 'https://localhost/api/reviews/',
                    urlProp: gameId,
                    method: 'PATCH',
                    token: cookies.get('token'),
                    body: {content: reviewData},
                    tokenFlag: false,
                });
                toast.success('Review updated');
            }
            setUpdateEffect(updateEffect+1);
        }
    }

    if(reviewsLoading || userReviewLoading || !headerContext.userLoaded || gameInfoLoading) return <Loading />;
    if(error) return <Error errorText={error} />;

    return (
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                    <Header />
                    <PageTitle>{gameInfo.title}</PageTitle>
                    <GameDescription HandlePurchase={HandlePurchase} gameInfo={gameInfo} />
                    <PageTitle>Reviews</PageTitle>
                    <Reviews
                        handleCreateOrUpdateReview={handleCreateOrUpdateReview}
                        handleDelete={handleDelete}
                        reviews={reviews}
                        currentUserReviewContent={currentUserReviewContent}
                    />
                </GlowingGrid>
            </Container>
            <Toaster>
                {(t) => (
                    <ToastBar toast={t}>
                        {({ icon, message }) => (
                            <>
                                {icon}
                                {message}
                                {t.message === 'You must be authorized to buy games' && (
                                    <Button variant="outlined" color="error" sx={{ width: "30%", fontSize: "100%" }} onClick={() => {toast.dismiss(t.id); navigate('/sign-in')}}>Sign In</Button>
                                )}
                            </>
                        )}
                    </ToastBar>
                )}
            </Toaster>
        </FullscreenGrid>
    );
}