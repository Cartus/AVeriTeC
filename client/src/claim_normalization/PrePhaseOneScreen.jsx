import NoteScreen from "../note_screen/NoteScreen";
import StartTaskBox from "../note_screen/StartTaskBox";

export default function PrePhaseOneScreen(props) {
    let desc_text = ""
    
    desc_text += "Welcome to phase one of the annotation task. "
    desc_text += "In the following screens, you will be presented with several fact-checking articles. "
    desc_text += "The assigned task is to collect metadata from these articles. "
    desc_text += "The first time you load the assignment interface, you will be guided through the various elements on the page. "
    desc_text += "Please refer to the annotation guideline document for precise instructions on how to complete the task. "

    let header_text = "Averitec Annotation | Phase One"

    let current_idx = Number(localStorage.finished_norm_annotations) + 1 - Number(localStorage.pc)

    return <NoteScreen header={header_text}>
        <StartTaskBox resume={current_idx > 1} taskLink={"/phase_1"}>
            {desc_text}
        </StartTaskBox>
    </NoteScreen>;
}