import {Grid, Paper, Stack, Typography} from "@mui/material";
import {PageTitle, ReviewInputField} from "../../Components";
import React from "react";
import Cookies from "universal-cookie";
import ThumbUpIcon from "@mui/icons-material/ThumbUp";
import ThumbDownIcon from "@mui/icons-material/ThumbDown";
import {Button as JoyButton} from "@mui/joy";
import {Button} from "@mui/material";

export default function Reviews({handleCreateOrUpdateReview, handleDelete, reviews,  currentUserReview, handleRate}) {
    const [inputData, setInputData] = React.useState();

    const cookies = new Cookies();

    return (
    <>
        {cookies.get('token') &&
            <Stack>
                <ReviewInputField setter={setInputData} defaultValue={currentUserReview.reviewContent} />
                <Grid item sx={{ marginBottom: "3%", height: "100%" }}>
                    <Button variant={currentUserReview.rating === 1 ? 'contained' : 'outlined'} onClick={() => {handleRate(1)}} color="success" sx={{ height: "100%" }}>
                        <ThumbUpIcon />
                    </Button>
                    <Button variant={currentUserReview.rating === 2 ? 'contained' : 'outlined'} onClick={() => {handleRate(2)}} color="error" sx={{ height: "100%" }}>
                        <ThumbDownIcon />
                    </Button>
                </Grid>
                <Grid>
                    <Button
                        variant="outlined"
                        color="success"
                        onClick={() => handleCreateOrUpdateReview(inputData)}
                        sx={{ fontSize: "120%", width: currentUserReview.reviewId ?  "49%" : "100%" }}
                    >
                        {currentUserReview.reviewId ?  "Change review" : "Create review"}
                    </Button>
                    {currentUserReview.reviewId &&
                        <Button
                            variant="outlined"
                            color="error"
                            onClick={handleDelete}
                            sx={{ fontSize: "120%", width: "49%", marginLeft: "2%" }}
                        >
                            Delete review
                        </Button>
                    }
                </Grid>
            </Stack>
        }

        <Stack spacing={2}>
            {reviews.length !== 0 ?
                reviews.map((item) => (
                    item.isFull &&
                    <Paper key={item.id} sx={{ backgroundColor: "#e9cda2", padding: "1%" }}>
                        <Grid container justifyContent="space-between">
                            <Grid item>
                                <Typography sx={{ fontSize: "150%" }}>{item.userNickname}</Typography>
                                <Typography sx={{ fontSize: "100%" }}>{item.content}</Typography>
                            </Grid>
                        </Grid>
                    </Paper>
                )) :
                <PageTitle>This game doesn't have any reviews</PageTitle>
            }
        </Stack>
    </>
    )
}