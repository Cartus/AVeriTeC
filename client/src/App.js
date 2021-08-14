import './App.css';
import ClaimNormalization from './claim_normalization/ClaimNormalization';
import QuestionGeneration from './question_generation/QuestionGeneration';

function App() {
  
  
  return (
    <div className="feverous">
      <header className="feverous-header">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" />
      </header>
      <body>
        <QuestionGeneration/>
      </body>
    </div>
  );
}

export default App;
