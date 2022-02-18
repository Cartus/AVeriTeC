import NoteScreen from "../note_screen/NoteScreen";
import StartTaskBox from "../note_screen/StartTaskBox";

export default function PrePhaseTwoScreen(props) {
    let desc_text = ""
    
    desc_text += "Welcome to the Averitec question generation annotation task. "
    desc_text += "In the following screens, you will be presented with several fact-checking articles. "
    desc_text += "Your fellow annotators have already extracted claims and collected metadata about these articles. "
    desc_text += "Your task is to create question-answer pairs that allow you to conclude whether the fact is support by available evidence. "
    desc_text += "The fact-checking article can be used as inspiration for which questions to ask. "
    desc_text += "Please refer to the annotation guideline document for precise instructions on how to complete the task. "

    let header_text = "Averitec Annotation | Question Generation"

    let current_idx = Number(localStorage.finished_norm_annotations) + 1 - Number(localStorage.pc)

    return <NoteScreen header={header_text}>
        <StartTaskBox resume={current_idx > 1} taskLink={"/phase_2"}>
            {desc_text}
        </StartTaskBox>
    </NoteScreen>;
}