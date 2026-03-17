import React, { useEffect, useState } from "react";
import useRegistrationAuthStore from "../../store/authStore";
import { registrationApi } from "../../store/authStore";

const HomePage = () => {
  const {
    user,
    logout,
    checkAuth,
    isAuthenticated,
    isLoading,
    testAutoRefresh,
  } = useRegistrationAuthStore();

  const [userData, setUserData] = useState(null);
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [lastRefreshTime, setLastRefreshTime] = useState(null);

  // Check authentication status on mount
  useEffect(() => {
    const initializeAuth = async () => {
      try {
        await checkAuth();
      } catch (error) {
        console.error("Failed to check auth:", error);
      }
    };

    initializeAuth();
  }, []);

  // Fetch user data when authenticated
  useEffect(() => {
    const fetchUserData = async () => {
      if (isAuthenticated) {
        try {
          const response = await registrationApi.get("/user");
          setUserData(response.data.user);
        } catch (error) {
          console.error("Failed to fetch user data:", error);
        }
      }
    };

    fetchUserData();
  }, [isAuthenticated]);

  const handleLogout = async () => {
    await logout();
    // Optional: redirect to login page
    // window.location.href = "/login";
  };

  const handleTestAutoRefresh = async () => {
    setIsRefreshing(true);
    try {
      const result = await testAutoRefresh();
      if (result.success) {
        setLastRefreshTime(new Date().toLocaleTimeString());
        alert("✅ Auto-refresh test successful!");

        // Refresh user data
        const response = await registrationApi.get("/user");
        setUserData(response.data.user);
      } else {
        alert("❌ Auto-refresh test failed: " + result.error);
      }
    } catch (error) {
      alert("❌ Auto-refresh test error: " + error.message);
    } finally {
      setIsRefreshing(false);
    }
  };

  // Show loading state
  if (isLoading) {
    return (
      <div className="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div className="max-w-4xl mx-auto">
          <div className="bg-white rounded-2xl shadow-xl p-8">
            <div className="flex justify-between items-center mb-8">
              <div>
                <h1 className="text-3xl font-bold text-gray-900">Welcome!</h1>
                <p className="text-gray-600 mt-2">
                  Loading your information...
                </p>
              </div>
              <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>

            <div className="bg-yellow-50 p-6 rounded-xl">
              <p className="text-yellow-700 flex items-center">
                <span className="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                Loading user information...
              </p>
            </div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-6xl mx-auto">
        <div className="bg-white rounded-2xl shadow-xl p-8">
          {/* Header Section */}
          <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
              <h1 className="text-3xl font-bold text-gray-900">
                Welcome, {userData?.applicant_name || user?.applicant_name}!
              </h1>
              <p className="text-gray-600 mt-2">
                Registration Portal Dashboard
              </p>
            </div>

            <div className="flex flex-wrap gap-2">
              {/* <button
                onClick={handleTestAutoRefresh}
                disabled={isRefreshing}
                className="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center disabled:opacity-50"
              >
                {isRefreshing ? (
                  <>
                    <svg
                      className="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                      fill="none"
                      viewBox="0 0 24 24"
                    >
                      <circle
                        className="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        strokeWidth="4"
                      ></circle>
                      <path
                        className="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                      ></path>
                    </svg>
                    Testing...
                  </>
                ) : (
                  "🔁 Test Auto-Refresh"
                )}
              </button> */}

              <button
                onClick={handleLogout}
                className="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition duration-300"
              >
                Logout
              </button>
            </div>
          </div>

          {/* User Information Section */}
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div className="bg-indigo-50 p-6 rounded-xl">
              <h2 className="text-xl font-semibold text-indigo-900 mb-4">
                Personal Information
              </h2>
              <div className="space-y-3">
                <div>
                  <strong className="text-gray-700">ID:</strong>
                  <p className="mt-1">{userData?.id || user?.id}</p>
                </div>
                <div>
                  <strong className="text-gray-700">Full Name:</strong>
                  <p className="mt-1">
                    {userData?.applicant_name || user?.applicant_name}
                  </p>
                </div>
                <div>
                  <strong className="text-gray-700">Office Email:</strong>
                  <p className="mt-1">
                    {userData?.office_email || user?.office_email}
                  </p>
                </div>
                <div>
                  <strong className="text-gray-700">Mobile:</strong>
                  <p className="mt-1">{userData?.mobile || user?.mobile}</p>
                </div>
              </div>
            </div>

            {/* <div className="bg-green-50 p-6 rounded-xl">
              <h2 className="text-xl font-semibold text-green-900 mb-4">
                Organization Details
              </h2>
              <div className="space-y-3">
                <div>
                  <strong className="text-gray-700">Organization:</strong>
                  <p className="mt-1">
                    {userData?.organization || user?.organization}
                  </p>
                </div>
                <div>
                  <strong className="text-gray-700">Status:</strong>
                  <div className="mt-1 flex flex-wrap gap-2">
                    <span className="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                      ✅ Payment Verified
                    </span>
                    <span className="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                      ✅ Credentials Sent
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div className="bg-purple-50 p-6 rounded-xl">
              <h2 className="text-xl font-semibold text-purple-900 mb-4">
                Token Information
              </h2>
              <div className="space-y-3">
                <p className="text-purple-700 flex items-center">
                  <span className="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                  ✅ Auto-refresh enabled
                </p>
                <div className="text-sm space-y-1">
                  <p>
                    <strong>Access Token:</strong> 2 minutes lifetime
                  </p>
                  <p>
                    <strong>Refresh Token:</strong> 7 days lifetime
                  </p>
                  <p>
                    <strong>Auto-refresh:</strong> On 401 error
                  </p>
                </div>
                {lastRefreshTime && (
                  <div className="text-sm bg-purple-100 p-2 rounded">
                    <strong>Last Refresh Test:</strong> {lastRefreshTime}
                  </div>
                )}
              </div>
            </div> */}
          </div>

          {/* Security & Auto-Refresh Info */}
          {/* <div className="bg-gray-50 p-6 rounded-xl border mb-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-4">
              Authentication System Details
            </h3>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h4 className="font-medium text-gray-700 mb-2">
                  Security Features
                </h4>
                <ul className="space-y-2 text-sm">
                  <li className="flex items-center text-green-600">
                    <svg
                      className="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fillRule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clipRule="evenodd"
                      />
                    </svg>
                    HTTP-only cookies for tokens
                  </li>
                  <li className="flex items-center text-green-600">
                    <svg
                      className="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fillRule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clipRule="evenodd"
                      />
                    </svg>
                    CSRF protection enabled
                  </li>
                  <li className="flex items-center text-green-600">
                    <svg
                      className="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fillRule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clipRule="evenodd"
                      />
                    </svg>
                    Secure SameSite cookies
                  </li>
                  <li className="flex items-center text-green-600">
                    <svg
                      className="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fillRule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clipRule="evenodd"
                      />
                    </svg>
                    Automatic token refresh
                  </li>
                </ul>
              </div>

              <div>
                <h4 className="font-medium text-gray-700 mb-2">
                  Auto-Refresh System
                </h4>
                <div className="text-sm text-gray-600 space-y-2">
                  <p>
                    <strong>How it works:</strong> When access token expires (2
                    minutes), the system automatically refreshes it using the
                    refresh token.
                  </p>
                  <p>
                    <strong>Testing:</strong> Click "Test Auto-Refresh" to
                    simulate token expiration and test the auto-refresh
                    mechanism.
                  </p>
                  <p>
                    <strong>Status:</strong>{" "}
                    {isAuthenticated ? "Active" : "Inactive"}
                  </p>
                </div>
              </div>
            </div>
          </div> */}

          {/* Quick Actions */}
          {/* <div className="bg-white border rounded-xl p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-4">
              Quick Actions
            </h3>
            <div className="flex flex-wrap gap-3">
              <button
                onClick={() =>
                  registrationApi.get("/user").then((res) => {
                    setUserData(res.data.user);
                    alert("User data refreshed!");
                  })
                }
                className="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition"
              >
                Refresh User Data
              </button>

              <button
                onClick={() => {
                  const cookies = document.cookie.split(";");
                  console.log("Current cookies:", cookies);
                  alert("Cookies logged to console!");
                }}
                className="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition"
              >
                Check Cookies
              </button>

              <button
                onClick={() => {
                  checkAuth().then((result) => {
                    alert(
                      `Auth check result: ${
                        result.authenticated
                          ? "Authenticated"
                          : "Not authenticated"
                      }`
                    );
                  });
                }}
                className="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition"
              >
                Check Auth Status
              </button>
            </div>
          </div> */}
        </div>
      </div>
    </div>
  );
};

// Debug Panel Component
const DebugCookies = () => {
  const { debugState } = useRegistrationAuthStore();

  const checkCookies = () => {
    const cookies = document.cookie.split(";");
    const accessToken = cookies.find((cookie) =>
      cookie.trim().startsWith("access_token=")
    );
    const refreshToken = cookies.find((cookie) =>
      cookie.trim().startsWith("refresh_token=")
    );
    const xsrfToken = cookies.find((cookie) =>
      cookie.trim().startsWith("XSRF-TOKEN=")
    );

    return {
      accessToken: accessToken ? "✅ Present" : "❌ Missing",
      refreshToken: refreshToken ? "✅ Present" : "❌ Missing",
      xsrfToken: xsrfToken ? "✅ Present" : "❌ Missing",
      allCookies: document.cookie || "No cookies",
    };
  };

  const cookieStatus = checkCookies();
  const state = debugState();

  return (
    <div className="fixed bottom-4 right-4 bg-gray-900 text-white p-4 rounded-lg text-xs max-w-md max-h-64 overflow-auto shadow-xl border border-gray-700">
      <h3 className="font-bold mb-2 text-yellow-400 flex items-center">
        <span className="mr-2">🍪</span> Registration Debug Panel
      </h3>

      <div className="space-y-3">
        {/* Authentication Status */}
        <div className="flex items-center justify-between">
          <span className="font-medium">Auth Status:</span>
          <span
            className={`px-2 py-1 rounded text-xs font-medium ${
              state.isAuthenticated
                ? "bg-green-900 text-green-100"
                : "bg-red-900 text-red-100"
            }`}
          >
            {state.isAuthenticated ? "✅ Authenticated" : "❌ Not Auth"}
          </span>
        </div>

        {/* CSRF Status */}
        <div className="flex items-center justify-between">
          <span className="font-medium">CSRF Token:</span>
          <span
            className={`px-2 py-1 rounded text-xs font-medium ${
              state.csrfInitialized
                ? "bg-green-900 text-green-100"
                : "bg-yellow-900 text-yellow-100"
            }`}
          >
            {state.csrfInitialized ? "✅ Initialized" : "⚠️ Not Init"}
          </span>
        </div>

        {/* Loading Status */}
        <div className="flex items-center justify-between">
          <span className="font-medium">Loading:</span>
          <span
            className={`px-2 py-1 rounded text-xs font-medium ${
              state.isLoading
                ? "bg-blue-900 text-blue-100"
                : "bg-gray-800 text-gray-300"
            }`}
          >
            {state.isLoading ? "🔄 Loading..." : "✅ Idle"}
          </span>
        </div>

        {/* Cookie Status */}
        <div className="border-t border-gray-700 pt-3">
          <h4 className="font-medium mb-2">Cookies Status:</h4>
          <div className="space-y-1">
            <div className="flex justify-between">
              <span>Access Token:</span>
              <span
                className={
                  cookieStatus.accessToken.includes("✅")
                    ? "text-green-400"
                    : "text-red-400"
                }
              >
                {cookieStatus.accessToken}
              </span>
            </div>
            <div className="flex justify-between">
              <span>Refresh Token:</span>
              <span
                className={
                  cookieStatus.refreshToken.includes("✅")
                    ? "text-green-400"
                    : "text-red-400"
                }
              >
                {cookieStatus.refreshToken}
              </span>
            </div>
            <div className="flex justify-between">
              <span>XSRF Token:</span>
              <span
                className={
                  cookieStatus.xsrfToken.includes("✅")
                    ? "text-green-400"
                    : "text-yellow-400"
                }
              >
                {cookieStatus.xsrfToken}
              </span>
            </div>
          </div>
        </div>

        {/* User Info */}
        <div className="border-t border-gray-700 pt-3">
          <h4 className="font-medium mb-1">User Info:</h4>
          <div className="truncate bg-gray-800 p-2 rounded">
            {state.user ? (
              <div className="text-xs">
                <div>
                  <strong>Email:</strong>{" "}
                  {state.user.office_email || state.user.email}
                </div>
                <div>
                  <strong>Name:</strong>{" "}
                  {state.user.applicant_name || state.user.name}
                </div>
              </div>
            ) : (
              <span className="text-gray-400">No user data</span>
            )}
          </div>
        </div>
      </div>

      <div className="mt-4 flex gap-2">
        <button
          onClick={() => {
            debugState();
            checkCookies();
            console.log("Debug info refreshed!");
          }}
          className="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-xs transition"
        >
          Refresh Debug
        </button>

        <button
          onClick={() => {
            const cookies = document.cookie.split(";");
            console.table(
              cookies.map((c) => {
                const [name, value] = c.trim().split("=");
                return {
                  Name: name,
                  Length: value ? value.length : 0,
                  Preview: value
                    ? value.substring(0, 20) + (value.length > 20 ? "..." : "")
                    : "empty",
                };
              })
            );
            alert("Cookies logged to console!");
          }}
          className="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded text-xs transition"
        >
          Log Cookies
        </button>
      </div>
    </div>
  );
};

// Export both components
export { DebugCookies };
export default HomePage;
