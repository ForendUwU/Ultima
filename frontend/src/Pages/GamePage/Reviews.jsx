import {Button, Grid, Paper, Stack, Typography} from "@mui/material";
import {PageTitle, ReviewInputField} from "../../Components";
import React from "react";
import Cookies from "universal-cookie";

export default function Reviews({handleCreateOrUpdateReview, handleDelete, reviews,  currentUserReview}) {
    const [inputData, setInputData] = React.useState();

    const cookies = new Cookies();

    return (
    <>
        {cookies.get('token') &&
            <Stack sx={{marginBottom:"3%"}}>
                <ReviewInputField setter={setInputData} defaultValue={currentUserReview.reviewContent} />
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