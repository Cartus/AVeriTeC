import NoteScreen from "../note_screen/NoteScreen";
import StartTaskBox from "../note_screen/StartTaskBox";

export default function PrePhaseThreeScreen(props) {
    let desc_text = ""
    
    desc_text += "Welcome to phase three of the annotation task. "
    desc_text += "In the following screens, you will be presented with several claims along with question-answer pairs generated by your fellow annotators. "
    desc_text += "Your task is to determine whether the question-answer pairs support or refute the claims. "
    desc_text += "In this phase, you will NOT see the fact-checking article, and you should NOT search for any additional evidence beyond what the question-answer pairs present. "
    desc_text += "If something is wrong with a question-answer pair, you will also be able to flag it. "
    desc_text += "Please refer to the annotation guideline document for precise instructions on how to complete the task. "

    let header_text = "Averitec Annotation | Phase Three"

    let current_idx = Number(localStorage.finished_norm_annotations) + 1 - Number(localStorage.pc)

    return <NoteScreen header={header_text}>
        <StartTaskBox resume={current_idx > 1} taskLink={"/phase_3"}>
            {desc_text}
        </StartTaskBox>
    </NoteScreen>;
}