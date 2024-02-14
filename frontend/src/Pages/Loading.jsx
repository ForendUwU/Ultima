import React, {useState} from "react";
import { Typography } from "@mui/material";
import FullscreenGrid from "../Components/FullscreenGrid";
import { animated, useTrail } from '@react-spring/web'

export default function Loading() {
    const[up, setUp] = useState(false)

    const trailSprings = useTrail(3, {
        from: { transform: up ? "translateY(46vh)" : "translateY(48vh)", color: "white", fontSize: "4vh" },
        to: {transform: up ? "translateY(48vh)" : "translateY(46vh)", color: "white" },
        onRest: () => { setUp(!up); }
    });

    return (
        <FullscreenGrid>
            <Typography variant="h2" alignSelf="center" style={{ color: "white" }}>
                Loading
            </Typography>
            {
                trailSprings.map((props, index) => (
                    <animated.div style={{...props}} key={index}>
                        .
                    </animated.div>
                ))
            }
        </FullscreenGrid>
    );
}