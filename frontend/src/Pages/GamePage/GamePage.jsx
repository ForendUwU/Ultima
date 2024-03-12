import React, {useContext, useEffect} from "react";
import {useParams} from "react-router-dom";
import {FullscreenGrid, GlowingGrid, Header, PageTitle, PurchasedGameButton, ReviewInputField} from "../../Components";
import {Button, Container, Grid, Paper, Stack, Typography} from "@mui/material";
import Cookies from "universal-cookie";
import Loading from "../StatePages/Loading";
import Error from "../StatePages/Error";
import toast, { Toaster, ToastBar } from 'react-hot-toast';
import {useNavigate} from 'react-router-dom';
import {HeaderContext} from "../../App/App";
import ThumbUpIcon from '@mui/icons-material/ThumbUp';
import ThumbDownIcon from '@mui/icons-material/ThumbDown';

export default function GamePage() {
    const [error, setError] = React.useState(null);
    const [gameInfoLoading, setGameInfoLoading] = React.useState(true);
    const [userReviewsLoading, setUserReviewsLoading] = React.useState(true);
    const [reviewsLoading, setReviewsLoading] = React.useState(true);
    const [gameInfo, setGameInfo] = React.useState();
    const [reviews, setReviews] = React.useState();
    const [currentUserReviewContent, setCurrentUserReviewContent] = React.useState();

    const { gameId } = useParams();
    const navigate = useNavigate();

    const headerContext = useContext(HeaderContext);

    const cookies = new Cookies();

    useEffect(() => {
        if (cookies.get('token')) {
            fetch('https://localhost/api/user/review/' + gameId, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + cookies.get('token')
                }
            }).then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error();
                }
            }).then(decodedResponse => {
                setCurrentUserReviewContent(decodedResponse['result']);
            }).finally(() => {
                setUserReviewsLoading(false);
            });
        }
    }, []);

    useEffect(() => {
        fetch('https://localhost/api/games/'+gameId, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok || response.status === 401) {
                return response.json();
            } else {
                throw new Error();
            }
        }).then(decodedResponse => {
            setGameInfo(decodedResponse);
        }).catch(error => {
            setError(error);
        }).finally(()=>{
            setGameInfoLoading(false);
        });
    }, []);

    useEffect(() => {
        fetch('https://localhost/api/reviews/'+gameId, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                return response.json();
            } else {
                throw new Error();
            }
        }).then(decodedResponse => {
            setReviews(decodedResponse);
        }).catch(error => {
            setError(error);
        }).finally(()=>{
            setReviewsLoading(false);
        });
    }, []);

    const handlePurchase = () => {
        if (cookies.get('token')) {
            fetch('https://localhost/api/purchase-game', {
                method: 'POST',
                body: JSON.stringify({
                    gameId: gameId,
                }),
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + cookies.get('token')
                }
            }).then(response => {
                if (response.ok || response.status === 422 || response.status === 403) {
                    return response.json();
                } else {
                    throw new Error();
                }
            }).then(decodedResponse => {
                if (decodedResponse['message'] === 'Game already purchased'){
                    toast.error(decodedResponse['message'], {duration: 2500});
                } else if (decodedResponse['message'] === 'Not enough money') {
                    toast.error(decodedResponse['message'], {duration: 2500});
                } else {
                    window.location.replace('/purchased-games');
                }
            }).catch(error => {
                setError(error);
            });
        } else {
            toast.error('You must be authorized to buy games', {duration: 2500});
        }
    }

    const handleDelete = () => {
        fetch('https://localhost/api/reviews/'+gameId, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + cookies.get('token')
            }
        }).then(response => {
            if (response.ok) {
                return response.json();
            } else {
                throw new Error();
            }
        }).then(decodedResponse => {
            //window.location.reload();
        }).catch(error => {
            setError(error);
        });
    }

    const handleCreateOrUpdateReview = (e) => {
        e.preventDefault();
        let { content } = document.forms[0];
        let validated = false;

        if (content.value === "") {
            toast.error("Content of review mustn't be empty");
        } else {
            validated = true;
        }

        if (validated) {
            if (!currentUserReviewContent) {
                fetch('https://localhost/api/reviews/'+gameId, {
                    method: 'POST',
                    body: JSON.stringify({
                        content: content.value,
                    }),
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + cookies.get('token')
                    }
                }).then(response => {
                    if (response.ok) {
                        return response.json();
                    } if (response.status === 422) {
                        toast.error("You already have review on this game");
                    } else {
                        throw new Error();
                    }
                }).then(decodedResponse => {
                    window.location.reload();
                }).catch(error => {
                    setError(error);
                });
            } else {
                fetch('https://localhost/api/reviews/'+gameId, {
                    method: 'PATCH',
                    body: JSON.stringify({
                        content: content.value,
                    }),
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + cookies.get('token')
                    }
                }).then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error();
                    }
                }).then(decodedResponse => {
                    window.location.reload();
                }).catch(error => {
                    setError(error);
                });
            }
        }
    }

    if(gameInfoLoading || reviewsLoading || userReviewsLoading || !headerContext.userLoaded) return <Loading />;
    if(error) return <Error errorText={error.toString()} />;

    return (
        <FullscreenGrid>
            <Container maxWidth="lg">
                <GlowingGrid>
                    <Header />
                    <PageTitle>{gameInfo.title}</PageTitle>
                    <Grid container columnSpacing={{ xs: 1, sm: 2, md: 3 }}>
                        <Grid item xs={6}>
                            <Grid container direction="column" sx={{height: "100%"}} wrap="nowrap">
                                <Grid item alignSelf="center">
                                    <Typography variant="h3">About the game</Typography>
                                </Grid>
                                <Grid item sx={{marginTop: "3%", height: "100%"}}>
                                    <Typography variant="h4">{gameInfo.description}</Typography>
                                </Grid>
                                <Grid item>
                                    <PurchasedGameButton handler={handlePurchase} color="success">{"Buy for "+gameInfo.price + "$"}</PurchasedGameButton>
                                </Grid>
                            </Grid>
                        </Grid>
                        <Grid item xs={6}>
                            <img
                                src={'https://source.unsplash.com/random/550x300?sig=1'}
                                alt="Game image"
                            />
                        </Grid>
                    </Grid>
                    <PageTitle>Reviews</PageTitle>
                    <Stack sx={{marginBottom:"3%"}}>
                        <form onSubmit={handleCreateOrUpdateReview}>
                        <ReviewInputField defaultValue={currentUserReviewContent} />
                        <Button
                            variant="outlined"
                            color="success"
                            type="submit"
                            sx={{ fontSize: "120%", width: currentUserReviewContent ?  "49%" : "100%" }}
                        >
                            {currentUserReviewContent ?  "Change review" : "Create review"}
                        </Button>
                            {currentUserReviewContent &&
                                <Button
                                    variant="outlined"
                                    color="error"
                                    onClick={handleDelete}
                                    sx={{ fontSize: "120%", width: "49%", marginLeft: "2%" }}
                                >
                                    Delete review
                                </Button>
                            }
                        </form>
                    </Stack>
                    <Stack spacing={2}>
                    {reviews.length !== 0 ?
                        reviews.map((item, index) => (
                            <Paper sx={{ backgroundColor: "#e9cda2", padding: "1%" }}>
                                <Grid container justifyContent="space-between">
                                    <Grid item>
                                        <Typography sx={{ fontSize: "150%" }}>{item.user}</Typography>
                                        <Typography sx={{ fontSize: "100%" }}>{item.content}</Typography>
                                    </Grid>
                                    <Grid item>
                                        <Button color="success" sx={{height: "100%"}}>
                                            <ThumbUpIcon />
                                            <Typography sx={{ fontSize: "150%", marginLeft: "10%" }}>{item.likes}</Typography>
                                        </Button>
                                        <Button color="error" sx={{height: "100%"}}>
                                            <ThumbDownIcon />
                                            <Typography sx={{ fontSize: "150%", marginLeft: "10%" }}>{item.dislikes}</Typography>
                                        </Button>
                                    </Grid>
                                </Grid>
                            </Paper>
                        )) :
                        <PageTitle>This game doesn't have any reviews</PageTitle>
                    }
                    </Stack>
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