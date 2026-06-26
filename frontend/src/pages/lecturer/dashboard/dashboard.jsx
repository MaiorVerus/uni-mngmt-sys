
import { Link } from "react-router-dom"





export default function LecturerDashBoard() {




    return (<>
        <p>welcome to lecturer</p>
        <p>this is your dashborad. explore</p>

<ul>
    <li><Link to="/lessons/ict">introduction to ict</Link></li>
    <li><Link to="/lessons/eng">introduction to english for academia</Link></li>

</ul>
    </>)
}