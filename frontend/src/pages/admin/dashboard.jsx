import { useAuth } from "../../context/AuthContext";

export default function AdminDashboard() {
  const { user } = useAuth();

  return (
    <div style={{ padding: "2rem" }}>
      <h1>Admin Dashboard</h1>
      <p>Welcome, {user?.username || "Admin"}.</p>
      <p>This page is protected for users with the admin role.</p>
    </div>
  );
}
