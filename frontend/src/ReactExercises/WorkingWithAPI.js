import React, {useState, useEffect} from "react";
import { Row, Col, Container, Button } from 'react-bootstrap';

function Games()
{
    const [games, setGames] = useState([]);

    useEffect(() => {
        fetch("https://random-data-api.com/api/users/random_user?size=10")
            .then(res => res.json())
            .then(
                (data) => {
                setGames(data)
            });
    }, []);
    var counter = 0;
    return (
        <Container>
            <Row>
                {
                    games.map(
                        item =>
                            <Col style={{
                                padding: "3%",
                                borderStyle: "solid",
                                borderRadius: "10px",
                                width: "20%"
                            }}>
                                <img style={{width: "100%"}}
                                     src={item.avatar}
                                     alt={"avatar"}
                                />
                                <div>{item.first_name}</div>
                                <br></br>
                                <div>{item.last_name}</div>
                            </Col>

                    )
                }
            </Row>
        </Container>
    );
}

export default Games;
