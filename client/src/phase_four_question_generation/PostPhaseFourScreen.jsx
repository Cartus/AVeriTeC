import NoteScreen from "../note_screen/NoteScreen";
import StartTaskBox from "../note_screen/StartTaskBox";
import TaskSummaryBox from "../note_screen/TaskSummaryBox";

export default function PostPhaseFourScreen(props) {
    let desc_text = ""
    desc_text += "Thank you for completing the additional question generation phase! "
    desc_text += "You can now log out, or return to the control panel."

    let header_text = "Averitec Annotation | Question Generation: Second Round"

    return <NoteScreen header={header_text}>
        <TaskSummaryBox>
            {desc_text}
        </TaskSummaryBox>
    </NoteScreen>;
}