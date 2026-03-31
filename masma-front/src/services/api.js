import axios from "axios";

export const API_BASE_URL = "https://server.masma.in/api";
export const IMAGE_PATH = "https://server.masma.in/uploads";

const axiosInstance = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    "Content-Type": "application/json",
  },
});

export default axiosInstance;
