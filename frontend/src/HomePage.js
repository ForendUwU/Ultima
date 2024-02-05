import React from "react";
import { Link } from "react-router-dom";

const HomePage = () => {
    return (
        <div style={{
            display: "flex",
            alignItems: "center"

        }}>
            <div><h1>Ultima</h1></div>
            <div>
                <Link to="/">Home</Link>
                <Link to="/signIn">Sign In</Link>
            </div>
        </div>
    );
};

export default HomePage;