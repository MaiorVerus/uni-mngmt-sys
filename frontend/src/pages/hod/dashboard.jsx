import { useAuth } from "../../context/AuthContext";

export default function HodDashboard() {
  const { user } = useAuth();

  return (
    <div style={{ padding: "2rem" }}>
      <h1>HOD Dashboard</h1>
      <p>Welcome, {user?.username || "HOD"}.</p>
      <p>This page is protected for users with the hod role.</p>
    </div>
  );
}
