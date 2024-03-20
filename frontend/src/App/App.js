import React, {createContext, useEffect} from "react";
import {
  BrowserRouter as Router,
  Routes,
  Route,
  Navigate,
} from "react-router-dom";

import "./App.css";

import {SignIn, Registration, HomePage, PurchasedGames, AccountFundingPage, GamePage, ProfilePage} from "../Pages";

import {HandleLogout} from "../Scripts/handleLogout";
import {GetUserInfo} from "../Scripts/getUserInfo";
import Cookies from "universal-cookie";
import Error from "../Pages/StatePages/Error";

export const HeaderContext = createContext();
export let UserContext = createContext();

function App() {
    const [userLoaded, setUserLoaded] = React.useState(false);
    const [error, setError] = React.useState(false);
    const [userInfo, setUserInfo] = React.useState(null);

    const cookies = new Cookies();

    useEffect(() => {
        if (cookies.get('token')) {
            GetUserInfo(cookies.get('token'))
                .then(decodedResponse => {
                    setUserInfo({
                        login: decodedResponse['login'],
                        nickname: decodedResponse['nickname'],
                        balance: decodedResponse['balance'],
                        firstName: decodedResponse['firstName'],
                        lastName: decodedResponse['lastName'],
                        email: decodedResponse['email']
                    })
                })
                .catch(error => {
                    setError(error);
                }).finally(() => {
                setUserLoaded(true);
            })
        } else { setUserLoaded(true); }
    }, []);

    if(error) return <Error errorText={error.toString()} />;

    return (
        <HeaderContext.Provider value={{
            handleLogout: HandleLogout,
            userLoaded: userLoaded
        }}>
            <UserContext.Provider value={{userInfo, setUserInfo}}>
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
                            path="/profile"
                            element={<ProfilePage />}
                        />
                        <Route
                            path="*"
                            element={<Navigate to="/" />}
                        />
                    </Routes>
                </Router>
            </UserContext.Provider>
        </HeaderContext.Provider>
    );
}

export default App;
