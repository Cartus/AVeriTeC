import NoteScreen from "../note_screen/NoteScreen";
import StartTaskBox from "../note_screen/StartTaskBox";
import TaskSummaryBox from "../note_screen/TaskSummaryBox";

export default function PostTrainingControl(props) {
    let desc_text = ""
    let header_text = ""
    if (props.phase === 1) {
        desc_text += "Thank you for completing training for the phase one annotation task! "
        desc_text += "You can now close this window. "
        desc_text += "Your annotations will be reviewed, and TODO."

        header_text = "Averitec Training | Phase One"
    }

    return <NoteScreen header={header_text}>
        <TaskSummaryBox>
            {desc_text}
        </TaskSummaryBox>
    </NoteScreen>;
}