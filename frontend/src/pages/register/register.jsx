import { useState } from "react"





import css from "./register.module.css"


export default function Register(params) {
    const [action, setAction] = useState("login");
    const [formData, setFormData] = useState({
        username: '',
        email: '',
        password: ''
    });
    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData((prevData) => ({
            ...prevData,
            [name]: value // Dynamic key assignment
        }));
    };

    async function handleSubmit(params) {

    }

    return (<>
        <p>signup or register</p>
        <div className={css['form-container']}>
            <form onSubmit={handleSubmit}>
                {action === "login" ? null : (
                    <div className={css['input-label txt']}>
                        <label htmlFor="">Username</label> <br />
                        <input
                            name="username"
                            placeholder="e.g Jean Paul"
                            type="text"
                            onChange={handleChange}
                            value={formData.username}
                            required
                        /> <br />
                    </div>
                )}

                <div className={css['input-label txt']}>
                    <label htmlFor="">Email:
                        <span className={css['sp-n']}>*Use school email*</span>
                    </label> <br />
                    <input
                        name="email"
                        placeholder="e.g tmajor@xool.com"
                        type="email"
                        onChange={handleChange}
                        value={formData.email}
                        required
                    /> <br />
                </div>
                <div className={css['input-label txt']}>
                    <label htmlFor="">Password</label> <br />
                    <input
                        name="password"
                        placeholder="e.g tM@ior=yte"
                        type="password"
                        onChange={handleChange}
                        value={formData.password}
                        required
                    /> <br />
                </div>
                <input type="submit" value="Submit" />

            </form>
            <div className={css["action-btns"]}>
                <button
                    type="button"
                    className={css[action === "login" ? "active" : null]}
                    onClick={() => setAction("login")}
                >Login</button>
                <button
                    type="button"
                    className={css[action === "signup" ? "active" : null]}
                    onClick={() => setAction("signup")}
                >Sign Up</button>
            </div>
        </div>

    </>)
}