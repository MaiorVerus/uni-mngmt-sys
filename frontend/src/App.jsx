import { BrowserRouter, Route, Routes } from "react-router-dom"
import { AuthProvider } from "./context/AuthContext"
import ProtectedRoute from "./components/ProtectedRoute"

import LandingPage from "./pages/landing/landing-pg"
import Register from "./pages/register/register"
import LecturerDashBoard from "./pages/lecturer/dashboard/dashboard"
import StudentDashBoard from "./pages/student/dashboard/dashboard"
// ↑ import every dashboard you have

export default function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <Routes>

          {/* 🔓 Public routes — no protection */}
          <Route path="/" element={<LandingPage />} />
          <Route path="/register" element={<Register />} />

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