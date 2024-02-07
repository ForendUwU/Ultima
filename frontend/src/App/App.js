import React from "react";
import {
  BrowserRouter as Router,
  Routes,
  Route,
  Navigate,
} from "react-router-dom";

import "./App.css";

import SignIn from "../AuthorizationPages/SignIn";
import Registration from "../AuthorizationPages/Registration";
import HomePage from "../HomePage/HomePage"

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
                path="/signIn"
                element={<SignIn />}
            />
            <Route
                path="/registration"
                element={<Registration />}
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
