import React from "react";
import {
  BrowserRouter as Router,
  Routes,
  Route,
  Navigate,
} from "react-router-dom";

import "./App.css";

import SignIn from "./SignIn";
import Games from "./ReactExercises/WorkingWithAPI";
import HomePage from "./HomePage"

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
                path="*"
                element={<Navigate to="/" />}
            />
          </Routes>
        </Router>
      </>
  );
}

export default App;
