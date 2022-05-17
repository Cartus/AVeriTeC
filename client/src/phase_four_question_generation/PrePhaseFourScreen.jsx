import NoteScreen from "../note_screen/NoteScreen";
import StartTaskBox from "../note_screen/StartTaskBox";

export default function PrePhaseFourScreen(props) {
    let desc_text = ""
    
    desc_text += "Welcome to re-annotation for the Averitec task. "
    desc_text += "In the following screens, you will be presented with several fact-checking articles. "
    desc_text += "Your fellow annotators have already extracted claims and collected metadata about these articles. "
    desc_text += "Furthermore, question-answer pairs and a verdict have been generated. "
    desc_text += "Unfortunately, for the claims you are about to be shown, there was a mismatch between phase two and phase three labels. "
    desc_text += "Your task is to resolve the disagreement between the two annotators. "
    desc_text += "To do so, you can modify the existing question-answer pairs, create additional question-answer pairs. "
    desc_text += "The fact-checking article can be used as inspiration for which questions to ask. "
    desc_text += "If appropriate, you can also submit a new verdict without modifying the question-answer pairs. "
    desc_text += "Please refer to the annotation guideline document for precise instructions on how to complete the task. "

    let header_text = "Averitec Annotation | Question Generation: Second Round"

    let current_idx = Number(localStorage.finished_norm_annotations) + 1 - Number(localStorage.pc)

    return <NoteScreen header={header_text}>
        <StartTaskBox resume={current_idx > 1} taskLink={"/phase_4"}>
            {desc_text}
        </StartTaskBox>
    </NoteScreen>;
}