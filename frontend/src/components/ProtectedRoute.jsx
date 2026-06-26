import { Navigate, useLocation } from "react-router-dom";
import { useAuth } from "../context/AuthContext";

export default function ProtectedRoute({ children, allowedRoles }) {
    const { isAuthenticated, user } = useAuth();
    const location = useLocation();

    // ── 1. GUEST CHECK ───────────────────────────────────────────────────────
    // If they aren't logged in, kick them back to the login/register screen.
    // We pass the current location in 'state' so we can redirect them back later!
    if (!isAuthenticated) {
        return <Navigate to="/register" state={{ from: location }} replace />;
    }

    // ── 2. ROLE CHECK (RBAC) ─────────────────────────────────────────────────
    // If specific roles are required, check if the user's role matches.
    if (allowedRoles && !allowedRoles.includes(user?.role)) {
        return <Navigate to="/unauthorised" replace />;
    }

    // ── 3. ACCESS GRANTED ────────────────────────────────────────────────────
    // Everything checks out. Pass through safely.
    return children;
}