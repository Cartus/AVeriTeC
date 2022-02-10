import NoteScreen from "../note_screen/NoteScreen";
import StartTaskBox from "../note_screen/StartTaskBox";
import TaskSummaryBox from "../note_screen/TaskSummaryBox";

export default function MidTrainingControl(props) {
    let desc_text = ""
    let header_text = ""

    desc_text += "Thank you for completing the initial training step! "
    desc_text += "You will now be able to review your answers, and compare them to a set of reference annotations we have prepared. "
    desc_text += "Please carefully check your annotations before proceeding to the second training step."

    if (props.phase === 1) {
        header_text = "Averitec Training | Phase One"
    } else if (props.phase === 2) {
        header_text = "Averitec Training | Phase Two"
    } else if (props.phase === 3) {
        header_text = "Averitec Training | Phase Three"
    }

    return <NoteScreen header={header_text}>
        <TaskSummaryBox taskLink={props.taskLink} continue={true}>
            {desc_text}
        </TaskSummaryBox>
    </NoteScreen>;
}