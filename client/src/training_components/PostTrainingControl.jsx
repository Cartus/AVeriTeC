import NoteScreen from "../note_screen/NoteScreen";
import StartTaskBox from "../note_screen/StartTaskBox";
import TaskSummaryBox from "../note_screen/TaskSummaryBox";

export default function PostTrainingControl(props) {
    let desc_text = ""
    let header_text = ""
    if (props.phase === 1) {
        desc_text += "Thank you for completing training for the phase one annotation task! "
        header_text = "Averitec Training | Phase One"
    } else if (props.phase === 2) {
        desc_text += "Thank you for completing training for the phase two annotation task! "
        header_text = "Averitec Training | Phase Two"
    } else if (props.phase === 3) {
        desc_text += "Thank you for completing training for the phase three annotation task! "
        header_text = "Averitec Training | Phase Three"
    }


    desc_text += "You can now close this window. "
    desc_text += "Your annotations will be reviewed, and TODO."

    return <NoteScreen header={header_text}>
        <TaskSummaryBox>
            {desc_text}
        </TaskSummaryBox>
    </NoteScreen>;
}