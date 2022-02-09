import NoteScreen from "../note_screen/NoteScreen";
import StartTaskBox from "../note_screen/StartTaskBox";
import TaskSummaryBox from "../note_screen/TaskSummaryBox";

export default function PostPhaseOneScreen(props) {
    let desc_text = ""
    desc_text += "Thank you for completing the Averitec phase one annotation task! "
    desc_text += "You can now log out, or return to the control panel."

    let header_text = "Averitec Annotation | Phase One"

    return <NoteScreen header={header_text}>
        <TaskSummaryBox>
            {desc_text}
        </TaskSummaryBox>
    </NoteScreen>;
}