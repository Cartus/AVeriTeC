import NoteScreen from "../note_screen/NoteScreen";
import StartTaskBox from "../note_screen/StartTaskBox";

export default function PreTrainingControl(props) {
    let desc_text = ""
    let header_text = ""
    if (props.phase === 1) {
        desc_text += "Welcome to phase one of the annotation task. "
        desc_text += "In the following screens, you will be presented with several fact-checking articles. "
        desc_text += "The assigned task is to collect metadata from these articles. "
        desc_text += "The first time you load the assignment interface, you will be guided through the various elements on the page. "
        desc_text += "Please refer to the annotation guideline document for precise instructions on how to complete the task. "
        desc_text += "During the training phase, you will first be presented with five warm-up claims. "
        desc_text += "Then, you will be able to compare your answers to a set of reference annotation we have compiled. "
        desc_text += "After reviewing the reference annotations, you will then be asked to annotate another five examples which will be used to evaluate your performance. "

        header_text = "Averitec Training | Phase One"
    }

    return <NoteScreen header={header_text}>
        <StartTaskBox taskLink={props.taskLink}>
            {desc_text}
        </StartTaskBox>
    </NoteScreen>;
}