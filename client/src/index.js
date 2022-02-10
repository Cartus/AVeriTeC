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
import TrainingOverlay from './training_components/TrainingOverlay';
import NoteScreen from './note_screen/NoteScreen';
import PreTrainingControl from './training_components/PreTrainingControl';
import MidTrainingControl from './training_components/MidTrainingControl';
import PostTrainingControl from './training_components/PostTrainingControl';
import PostPhaseOneScreen from './claim_normalization/PostPhaseOneScreen';
import PostReviewTrainingControl from './training_components/PostReviewTrainingControl';
import PrePhaseOneScreen from './claim_normalization/PrePhaseOneScreen';
import PostPhaseTwoScreen from './question_generation/PostPhaseTwoScreen';
import PrePhaseTwoScreen from './question_generation/PrePhaseTwoScreen';
import PrePhaseThreeScreen from './verdict_validation/PrePhaseThreeScreen';

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
          <Route path="/phase_1/begin">
            <PrePhaseOneScreen />
          </Route>
          <Route path="/phase_1/completed">
            <PostPhaseOneScreen />
          </Route>
          <Route path="/phase_1">
            <ClaimNormalization finish_path="/phase_1/completed/"/>
          </Route>
          <Route path="/phase_2/begin">
            <PrePhaseTwoScreen />
          </Route>
          <Route path="/phase_2/completed">
            <PostPhaseTwoScreen />
          </Route>
          <Route path="/phase_2">
            <QuestionGeneration finish_path="/phase_2/completed/"/>
          </Route>
          <Route path="/phase_3/begin">
            <PrePhaseThreeScreen />
          </Route>
          <Route path="/phase_3/completed">
            <PostPhaseTwoScreen />
          </Route>
          <Route path="/phase_3">
            <VerdictValidation finish_path="/phase_2/completed/"/>
          </Route>
          <Route path="/disagreement">
            <DisagreementResolution />
          </Route>
          <Route path="/control">
            <AnnotatorControl />
          </Route>

          <Route path="/training/phase_1/task_1_start">
            <PreTrainingControl phase={1} taskLink="/training/phase_1/task_1"/>
          </Route>
          <Route path="/training/phase_1/task_1">
            <ClaimNormalization finish_path="/training/phase_1/mid"/>
          </Route>
          <Route path="/training/phase_1/mid">
            <MidTrainingControl phase={1} taskLink="/training/phase_1/mid_review"/>
          </Route>
          <Route path="/training/phase_1/mid_review">
            <TrainingOverlay phase={1} finish_path="/training/phase_1/task_2_start"/>
          </Route>
          <Route path="/training/phase_1/task_2_start">
            <PostReviewTrainingControl phase={1} taskLink="/training/phase_1/task_2"/>
          </Route>
          <Route path="/training/phase_1/task_2">
            <ClaimNormalization finish_path="/training/phase_1/complete"/>
          </Route>
          <Route path="/training/phase_1/complete">
            <PostTrainingControl phase={1}/>
          </Route>

          <Route path="/training/phase_2/task_1_start">
            <PreTrainingControl phase={2} taskLink="/training/phase_2/task_1"/>
          </Route>
          <Route path="/training/phase_2/task_1">
            <QuestionGeneration finish_path="/training/phase_2/mid"/>
          </Route>
          <Route path="/training/phase_2/mid">
            <MidTrainingControl phase={2} taskLink="/training/phase_2/mid_review"/>
          </Route>
          <Route path="/training/phase_2/mid_review">
            <TrainingOverlay phase={2} finish_path="/training/phase_2/task_2_start"/>
          </Route>
          <Route path="/training/phase_2/task_2_start">
            <PostReviewTrainingControl phase={2} taskLink="/training/phase_2/task_2"/>
          </Route>
          <Route path="/training/phase_2/task_2">
            <QuestionGeneration finish_path="/training/phase_2/complete"/>
          </Route>
          <Route path="/training/phase_2/complete">
            <PostTrainingControl phase={2}/>
          </Route>

          <Route path="/training/phase_3/task_1_start">
            <PreTrainingControl phase={3} taskLink="/training/phase_3/task_1"/>
          </Route>
          <Route path="/training/phase_3/task_1">
            <VerdictValidation finish_path="/training/phase_3/mid"/>
          </Route>
          <Route path="/training/phase_3/mid">
            <MidTrainingControl phase={3} taskLink="/training/phase_3/mid_review"/>
          </Route>
          <Route path="/training/phase_3/mid_review">
            <TrainingOverlay phase={3} finish_path="/training/phase_3/task_2_start"/>
          </Route>
          <Route path="/training/phase_3/task_2_start">
            <PostReviewTrainingControl phase={3} taskLink="/training/phase_3/task_2"/>
          </Route>
          <Route path="/training/phase_3/task_2">
            <VerdictValidation finish_path="/training/phase_3/complete"/>
          </Route>
          <Route path="/training/phase_3/complete">
            <PostTrainingControl phase={3}/>
          </Route>
      </Switch>
  </Router>
);

ReactDOM.render(routing, document.getElementById("root"));
