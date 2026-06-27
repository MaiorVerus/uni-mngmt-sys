import { BrowserRouter, Navigate, Route, Routes } from "react-router-dom"
import { AuthProvider, useAuth } from "./context/AuthContext"
import ProtectedRoute from "./components/ProtectedRoute"

import LandingPage from "./pages/landing/landing-pg"
import Register from "./pages/register/register"
import LecturerDashBoard from "./pages/lecturer/dashboard/dashboard"
import StudentDashBoard from "./pages/student/dashboard/dashboard"
import HodDashboard from "./pages/hod/dashboard"
import AdminDashboard from "./pages/admin/dashboard"

function DashboardRedirect() {
  const { user, isAuthenticated } = useAuth();

  if (!isAuthenticated) {
    return <Navigate to="/register" replace />;
  }

  const routes = {
    student: "/student/dashboard",
    lecturer: "/lecturer/dashboard",
    hod: "/hod/dashboard",
    admin: "/admin/dashboard",
  };

  return <Navigate to={routes[user?.role] ?? "/register"} replace />;
}

export default function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <Routes>

          {/* 🔓 Public routes — no protection */}
          <Route path="/" element={<LandingPage />} />
          <Route path="/register" element={<Register />} />
          <Route path="/dashboard" element={<DashboardRedirect />} />

          {/* 🔒 Protected routes — role checked by ProtectedRoute */}
          <Route path="/lecturer/dashboard" element={
            <ProtectedRoute allowedRoles={['lecturer', 'hod']}>
              <LecturerDashBoard />
            </ProtectedRoute>
          } />

          <Route path="/student/dashboard" element={
            <ProtectedRoute allowedRoles={['student']}>
              <StudentDashBoard />
            </ProtectedRoute>
          } />

          <Route path="/hod/dashboard" element={
            <ProtectedRoute allowedRoles={['hod']}>
              <HodDashboard />
            </ProtectedRoute>
          } />

          <Route path="/admin/dashboard" element={
            <ProtectedRoute allowedRoles={['admin']}>
              <AdminDashboard />
            </ProtectedRoute>
          } />

          {/* 🚫 Catch-all for wrong roles */}
          <Route path="/unauthorised" element={
            <p>403 — You do not have permission to view this page.</p>
            // ↑ replace with a proper Unauthorised component later
          } />

          {/* 🚫 404 */}
          <Route path="*" element={
            <p>404 — Page not found.</p>
          } />

        </Routes>
      </AuthProvider>
    </BrowserRouter>
  )
}