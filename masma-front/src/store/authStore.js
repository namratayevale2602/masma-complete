import { create } from "zustand";
import { persist } from "zustand/middleware";
import axios from "axios";

// Create main API instance for registration
const registrationApi = axios.create({
  baseURL: "http://localhost:8000/api/registration",
  withCredentials: true,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
    "X-Requested-With": "XMLHttpRequest",
  },
});

// Create CSRF-specific instance
const csrfApi = axios.create({
  baseURL: "http://localhost:8000",
  withCredentials: true,
});

// Enhanced request interceptor
registrationApi.interceptors.request.use(
  (config) => {
    console.log("🔄 Registration API Request:", {
      method: config.method?.toUpperCase(),
      url: config.url,
    });
    return config;
  },
  (error) => {
    console.error("❌ Registration Request Interceptor Error:", error);
    return Promise.reject(error);
  }
);

// Enhanced response interceptor
// Enhanced response interceptor - FIXED VERSION
registrationApi.interceptors.response.use(
  (response) => {
    console.log("✅ Registration API Response Success:", {
      status: response.status,
      url: response.config.url,
    });
    return response;
  },
  async (error) => {
    const originalRequest = error.config;

    console.error("❌ Registration API Response Error:", {
      status: error.response?.status,
      message: error.response?.data?.error || error.message,
      url: error.config?.url,
    });

    // Don't attempt token refresh for:
    // 1. Login requests (when they fail)
    // 2. Refresh endpoint itself
    // 3. Flagged as login request
    const isLoginEndpoint =
      originalRequest.url === "/login" ||
      originalRequest.url?.includes("/login");
    const isRefreshEndpoint =
      originalRequest.url === "/refresh" ||
      originalRequest.url?.includes("/refresh");
    const isLoginRequest = originalRequest._isLoginRequest;

    if (
      error.response?.status === 401 &&
      !originalRequest._retry &&
      !isLoginEndpoint &&
      !isRefreshEndpoint &&
      !isLoginRequest
    ) {
      console.log("🔄 Registration: Attempting token refresh...");
      originalRequest._retry = true;

      try {
        await registrationApi.post("/refresh");
        console.log(
          "✅ Registration: Token refreshed, retrying original request"
        );
        return registrationApi(originalRequest);
      } catch (refreshError) {
        console.error("❌ Registration: Token refresh failed:", refreshError);
        useRegistrationAuthStore.getState().logout();
        return Promise.reject(refreshError);
      }
    }

    return Promise.reject(error);
  }
);

const useRegistrationAuthStore = create(
  persist(
    (set, get) => ({
      user: null,
      isAuthenticated: false,
      isLoading: false,
      error: null,
      csrfInitialized: false,

      initializeCSRF: async () => {
        try {
          console.log("🛡️ Registration: Initializing CSRF protection...");
          const response = await csrfApi.get("/sanctum/csrf-cookie");
          console.log("✅ Registration: CSRF protection initialized");
          set({ csrfInitialized: true });
          return true;
        } catch (error) {
          console.error("❌ Registration: CSRF initialization failed:", error);
          set({ csrfInitialized: false });
          return false;
        }
      },

      login: async (credentials) => {
        set({ isLoading: true, error: null });
        try {
          console.log("🔐 Registration: Starting login process...");

          const csrfSuccess = await get().initializeCSRF();
          if (!csrfSuccess) {
            throw new Error("CSRF initialization failed");
          }

          // Create a custom config to identify this as a login request
          const config = {
            _isLoginRequest: true, // Add custom flag for interceptor
          };

          const response = await registrationApi.post(
            "/login",
            credentials,
            config
          );

          set({
            user: response.data.user,
            isAuthenticated: true,
            isLoading: false,
            error: null,
          });

          return { success: true, data: response.data };
        } catch (error) {
          let errorMessage = "Login failed. Please try again.";

          if (error.response?.status === 401) {
            errorMessage = error.response.data.error || "Invalid credentials";
          } else if (error.response?.data?.error) {
            errorMessage = error.response.data.error;
          } else if (error.message) {
            errorMessage = error.message;
          }

          set({
            error: errorMessage,
            isLoading: false,
            isAuthenticated: false,
            user: null,
          });

          return { success: false, error: errorMessage };
        }
      },

      checkAuth: async () => {
        try {
          console.log("🔍 Registration: Checking authentication status...");
          const response = await registrationApi.get("/check-auth");

          if (response.data.authenticated) {
            set({
              user: response.data.user,
              isAuthenticated: true,
            });
            return { authenticated: true, user: response.data.user };
          } else {
            set({
              user: null,
              isAuthenticated: false,
            });
            return { authenticated: false };
          }
        } catch (error) {
          console.error("❌ Registration: Auth check failed:", error);
          set({
            user: null,
            isAuthenticated: false,
          });
          return { authenticated: false };
        }
      },

      // FIXED: Proper logout with redirection
      logout: async () => {
        set({ isLoading: true });
        try {
          console.log("🚪 Registration: Logging out...");

          // First try to call the logout endpoint
          try {
            await registrationApi.post("/logout");
            console.log("✅ Registration: Logout API call successful");
          } catch (apiError) {
            console.warn(
              "⚠️ Registration: Logout API call failed, but continuing with local logout",
              apiError
            );
          }
        } catch (error) {
          console.error("❌ Registration: Logout error:", error);
        } finally {
          // Clear local state
          set({
            user: null,
            isAuthenticated: false,
            isLoading: false,
            error: null,
          });
          console.log("🧹 Registration: Local state cleared");

          // Clear persisted storage
          localStorage.removeItem("registration-auth-storage");

          // Clear all registration-related cookies
          document.cookie =
            "access_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
          document.cookie =
            "refresh_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
          document.cookie =
            "laravel_session=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

          console.log("🍪 Registration: Cookies cleared");

          // IMPORTANT: Redirect to login page
          // Use setTimeout to ensure state is cleared before redirect
          setTimeout(() => {
            const currentPath = window.location.pathname;
            const isAlreadyOnLoginPage = currentPath.includes("/login");

            if (!isAlreadyOnLoginPage) {
              console.log("➡️ Registration: Redirecting to login page...");
              // Force a hard redirect to ensure clean state
              window.location.href = "/login";
            }
          }, 100);
        }
      },

      // Enhanced logout that can be called with redirect option
      logoutWithRedirect: async (redirectPath = "/login") => {
        set({ isLoading: true });
        try {
          await registrationApi.post("/logout");
        } catch (error) {
          console.warn("Logout API call failed:", error);
        } finally {
          set({
            user: null,
            isAuthenticated: false,
            isLoading: false,
            error: null,
          });

          localStorage.removeItem("registration-auth-storage");

          // Redirect
          setTimeout(() => {
            window.location.href = redirectPath;
          }, 100);
        }
      },

      // Alternative: Force logout without API call (for immediate redirect)
      forceLogout: () => {
        set({
          user: null,
          isAuthenticated: false,
          isLoading: false,
          error: null,
        });

        localStorage.removeItem("registration-auth-storage");

        // Redirect immediately
        window.location.href = "/login";
      },

      changePassword: async (passwords) => {
        set({ isLoading: true, error: null });
        try {
          const response = await registrationApi.post(
            "/change-password",
            passwords
          );

          // Use the enhanced logout with redirect
          await get().logoutWithRedirect("/login?passwordChanged=true");

          return { success: true, data: response.data };
        } catch (error) {
          let errorMessage = "Password change failed";
          if (error.response?.data?.error) {
            errorMessage = error.response.data.error;
          }

          set({ error: errorMessage, isLoading: false });
          return { success: false, error: errorMessage };
        }
      },

      clearError: () => set({ error: null }),

      debugState: () => {
        const state = get();
        console.log("🐛 Registration Auth Store Debug:", {
          user: state.user,
          isAuthenticated: state.isAuthenticated,
          isLoading: state.isLoading,
          csrfInitialized: state.csrfInitialized,
        });
        return state;
      },
    }),
    {
      name: "registration-auth-storage",
      partialize: (state) => ({
        isAuthenticated: state.isAuthenticated,
        csrfInitialized: state.csrfInitialized,
      }),
    }
  )
);

export default useRegistrationAuthStore;
export { registrationApi };
