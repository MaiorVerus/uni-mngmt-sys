// AuthContext.jsx
import { createContext, useContext, useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
    const [token, setToken] = useState(() => localStorage.getItem("token") || null);
    const [user, setUser] = useState(() => {
        const saved = localStorage.getItem("user");

        if (!saved) return null;

        try {
            return JSON.parse(saved);
        } catch {
            localStorage.removeItem("user");
            return null;
        }
    });

    const navigate = useNavigate();
    // ↑ AuthProvider must be INSIDE <BrowserRouter> in App.jsx for this to work

    useEffect(() => {
        // ── Request interceptor ───────────────────────────────────────────
        const requestInterceptor = axios.interceptors.request.use((config) => {
            if (token) {
                config.headers.Authorization = `Bearer ${token}`;
            }
            return config;
        });

        // ── Response interceptor — global 401 handler ─────────────────────
        const responseInterceptor = axios.interceptors.response.use(
            (response) => response,
            (error) => {
                if (error.response?.status === 401) {
                    logout();
                    navigate('/register');
                }
                return Promise.reject(error);
            }
        );

        // ── Cleanup on unmount or token change ────────────────────────────
        return () => {
            axios.interceptors.request.eject(requestInterceptor);
            axios.interceptors.response.eject(responseInterceptor);
        };
    }, [token]);
    // ↑ re-runs whenever token changes — fresh interceptor with current token

    const login = (jwtToken, userData) => {
        setToken(jwtToken);
        setUser(userData);
        localStorage.setItem("token", jwtToken);
        localStorage.setItem("user", JSON.stringify(userData));
    };

    const logout = () => {
        setToken(null);
        setUser(null);
        localStorage.removeItem("token");
        localStorage.removeItem("user");
    };

    return (
        <AuthContext.Provider value={{ token, user, login, logout, isAuthenticated: !!token }}>
            {children}
        </AuthContext.Provider>
    );
}

export function useAuth() {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error("useAuth must be used within an AuthProvider");
    }
    return context;
}