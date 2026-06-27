import { useState } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import { useAuth } from "../../context/AuthContext";

export default function Register() {
    const { login } = useAuth();
    const navigate = useNavigate();
    const [action, setAction] = useState("login");
    const [err, setErr] = useState(null);
    const [formData, setFormData] = useState({
        username: '',
        email: '',
        password: '',
        role: 'student'
    });

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    async function handleSubmit(e) {
        e.preventDefault();
        setErr(null);

        const url = action === "login"
            ? "http://localhost/uni-mngmt-sys/api/public/auth/login"
            : "http://localhost/uni-mngmt-sys/api/public/auth/signup";

        try {
            const payload = action === "login"
                ? { email: formData.email, password: formData.password }
                : formData;

            const response = await axios.post(url, payload);

            if (action === "login") {
                const { token, user } = response.data;
                login(token, user);

                const routes = {
                    student: '/student/dashboard',
                    lecturer: '/lecturer/dashboard',
                    hod: '/hod/dashboard',
                    admin: '/admin/dashboard',
                };

                navigate(routes[user?.role] ?? '/dashboard');
            } else {
                setAction("login");
                setErr(null);
                alert("Signup successful! Please log in.");
            }
        } catch (error) {
            const message = error.response?.data?.error
                ?? error.message
                ?? "Something went wrong. Please try again.";
            setErr(message);
        }
    }

    return (
        <div style={{ maxWidth: "420px", margin: "3rem auto", fontFamily: "sans-serif" }}>
            <h2>{action === "login" ? "Login" : "Create account"}</h2>

            {err && <p style={{ color: "crimson" }}>{err}</p>}

            <form onSubmit={handleSubmit}>
                {action !== "login" && (
                    <div style={{ marginBottom: "0.8rem" }}>
                        <label>Username</label>
                        <input
                            name="username"
                            value={formData.username}
                            onChange={handleChange}
                            required
                            style={{ display: "block", width: "100%", marginTop: "0.3rem" }}
                        />
                    </div>
                )}

                <div style={{ marginBottom: "0.8rem" }}>
                    <label>Email</label>
                    <input
                        name="email"
                        type="email"
                        value={formData.email}
                        onChange={handleChange}
                        required
                        style={{ display: "block", width: "100%", marginTop: "0.3rem" }}
                    />
                </div>

                <div style={{ marginBottom: "0.8rem" }}>
                    <label>Password</label>
                    <input
                        name="password"
                        type="password"
                        value={formData.password}
                        onChange={handleChange}
                        required
                        style={{ display: "block", width: "100%", marginTop: "0.3rem" }}
                    />
                </div>

                {action !== "login" && (
                    <div style={{ marginBottom: "0.8rem" }}>
                        <label>Role</label>
                        <select
                            name="role"
                            value={formData.role}
                            onChange={handleChange}
                            style={{ display: "block", width: "100%", marginTop: "0.3rem" }}
                        >
                            <option value="student">Student</option>
                            <option value="lecturer">Lecturer</option>
                            <option value="hod">HOD</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                )}

                <button type="submit" style={{ width: "100%", padding: "0.7rem" }}>
                    {action === "login" ? "Login" : "Register"}
                </button>
            </form>

            <p style={{ marginTop: "1rem" }}>
                {action === "login" ? "Don’t have an account?" : "Already have an account?"}{" "}
                <button type="button" onClick={() => setAction(action === "login" ? "register" : "login")}>
                    {action === "login" ? "Register" : "Login"}
                </button>
            </p>
        </div>
    );
}