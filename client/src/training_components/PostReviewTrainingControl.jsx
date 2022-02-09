import NoteScreen from "../note_screen/NoteScreen";
import StartTaskBox from "../note_screen/StartTaskBox";

export default function PostReviewTrainingControl(props) {
    let desc_text = ""
    let header_text = ""
    if (props.phase === 1) {
        desc_text += "Welcome to phase one of the annotation task, round two. "
        desc_text += "In the following screens, you will be presented with several fact-checking articles. "
        desc_text += "The assigned task is to collect metadata from these articles. "
        desc_text += "Please refer to the annotation guideline document for precise instructions on how to complete the task. "
        desc_text += "As in the warm-up round, you will be asked to annotate several examples. "
        desc_text += "In this round, the five examples will be used to evaluate your performance. "

        header_text = "Averitec Training | Phase One"
    }

    return <NoteScreen header={header_text}>
        <StartTaskBox taskLink={props.taskLink}>
            {desc_text}
        </StartTaskBox>
    </NoteScreen>;
}