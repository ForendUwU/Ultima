import React from "react";
import {
  BrowserRouter as Router,
  Routes,
  Route,
  Navigate,
} from "react-router-dom";

import "./App.css";

import SignIn from "../Pages/AuthorizationPages/SignIn";
import Registration from "../Pages/AuthorizationPages/Registration";
import HomePage from "../Pages/HomePage/HomePage"
import PurchasedGames from "../Pages/PurchasedGames/PurchasedGames";

function App() {
  return (
      <>
        <Router>
          <Routes>
            <Route
                exact
                path="/"
                element={<HomePage />}
            />
            <Route
                path="/sign-in"
                element={<SignIn />}
            />
            <Route
                path="/registration"
                element={<Registration />}
            />
            <Route
                path="/purchased-games"
                element={<PurchasedGames />}
            />
            <Route
                path="*"
                element={<Navigate to="/" />}
            />
          </Routes>
        </Router>
      </>
  );
}

export default App;
