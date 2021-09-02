import './App.css';
import ClaimNormalization from './claim_normalization/ClaimNormalization';
import Login from './login/Login';
import Registration from './login/Registration';
import QuestionGeneration from './question_generation/QuestionGeneration';
import { BrowserRouter, Route, Switch, Redirect } from 'react-router-dom';

function App() {
  /*{loggedIn ? <Redirect to="/dashboard" /> : <PublicHomePage />}*/
  
  return (
    <div>
      <header>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" />
      </header>
      <body>
      <Login/>
        <BrowserRouter>
        <Switch>
          <Route path="/">
            <Redirect to="/login" />
          </Route>
          <Route path="/login">
            <Login/>
          </Route>
          <Route path="/register">
            <Registration />
          </Route>
          <Route path="/phase_1">
            <ClaimNormalization />
          </Route>
          <Route path="/phase_2">
            <QuestionGeneration />
          </Route>
        </Switch>
        </BrowserRouter>
      </body>
    </div>
  );
}

export default App;
