import { useEffect, useState } from "react";
import { Navigate, Outlet } from "react-router-dom";
import useRegistrationAuthStore from "../store/authStore";

const ProtectedRoute = () => {
  const { isAuthenticated, checkAuth } = useRegistrationAuthStore();
  const [checkingAuth, setCheckingAuth] = useState(true);

  useEffect(() => {
    const verifyAuth = async () => {
      await checkAuth();
      setCheckingAuth(false);
    };

    verifyAuth();
  }, [checkAuth]);

  if (checkingAuth) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      </div>
    );
  }

  return isAuthenticated ? <Outlet /> : <Navigate to="/login" replace />;
};

export default ProtectedRoute;
