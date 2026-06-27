import { Navigate, useLocation } from "react-router-dom";
import { useAuth } from "../context/AuthContext";

export default function ProtectedRoute({ children, allowedRoles }) {
    const { isAuthenticated, user } = useAuth();
    const location = useLocation();

    if (!isAuthenticated) {
        return <Navigate to="/register" state={{ from: location }} replace />;
    }

    if (Array.isArray(allowedRoles) && !allowedRoles.includes(user?.role)) {
        return <Navigate to="/unauthorised" replace />;
    }

    return children;
}