import React from 'react';
import ReactDOM from 'react-dom';
import './index.css';
import ClaimNormalization from './claim_normalization/ClaimNormalization';
import Login from './login/Login';
import Registration from './login/Registration';
import QuestionGeneration from './question_generation/QuestionGeneration';
import { BrowserRouter as Router, Route, Switch, Redirect } from 'react-router-dom';
import VerdictValidation from './verdict_validation/VerdictValidation';
import AnnotatorControl from './control_panels/AnnotatorControl';
import ChangePassword from './login/ChangePassword';
import DisagreementResolution from './disagreement_resolution/DisagreementResolution';

const routing = (
  <Router>
      <header>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" />
      </header>
      <Switch>
          <Route exact path="/">
            <Redirect to="/login" />
          </Route>
          <Route path="/login">
            <Login/>
          </Route>
          <Route path="/register">
            <Registration />
          </Route>
          <Route path="/change_password">
            <ChangePassword/>
          </Route>
          <Route path="/phase_1">
            <ClaimNormalization />
          </Route>
          <Route path="/phase_2">
            <QuestionGeneration />
          </Route>
          <Route path="/phase_3">
            <VerdictValidation />
          </Route>
          <Route path="/disagreement">
            <DisagreementResolution />
          </Route>
          <Route path="/control">
            <AnnotatorControl />
          </Route>
      </Switch>
  </Router>
);

ReactDOM.render(routing, document.getElementById("root"));
