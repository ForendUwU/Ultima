import React, {createContext, useEffect} from "react";
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
import AccountFundingPage from "../Pages/AccountFundingPage/AccountFundingPage";
import GamePage from "../Pages/GamePage/GamePage";
import ProfilePage from "../Pages/ProfilePage/ProfilePage";

import {HandleLogout} from "../Scripts/handleLogout";
import {GetUserInfo} from "../Scripts/getUserInfo";
import Cookies from "universal-cookie";
import Error from "../Pages/StatePages/Error";

export const HeaderContext = createContext();

function App() {
  const [nickname, setNickname] = React.useState(null);
  const [balance, setBalance] = React.useState(null);
  const [userLoaded, setUserLoaded] = React.useState(false);
  const [error, setError] = React.useState(null);


  const cookies = new Cookies();

  useEffect(() => {
    GetUserInfo(cookies.get('token'))
        .then(decodedResponse => {
          setNickname(decodedResponse['nickname']);
          setBalance(decodedResponse['balance']);
        })
        .catch(error => {
          setError(error);
        }).finally(()=>{
          setUserLoaded(true);
        })
  }, []);

  if(error) return <Error errorText={error.toString()} />;

  return (
      <HeaderContext.Provider value={{
        handleLogout: HandleLogout,
        nickname: nickname,
        balance: balance,
        userLoaded: userLoaded
      }}>
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
                path="/account-funding"
                element={<AccountFundingPage />}
            />
            <Route
                path="/game/:gameId"
                element={<GamePage />}
            />
            <Route
                path="/user/profile"
                element={<ProfilePage />}
            />
            <Route
                path="*"
                element={<Navigate to="/" />}
            />
          </Routes>
        </Router>
      </HeaderContext.Provider>
  );
}

export default App;
