import React, { useState, useRef } from "react";
import axiosInstance from "../../services/api";
import QRCode from "qrcode";

const Visitors = () => {
  const [formData, setFormData] = useState({
    visitor_name: "",
    bussiness_name: "",
    mobile: "",
    phone: "",
    whatsapp_no: "",
    email: "",
    city: "",
    town: "",
    village: "",
    remark: "",
  });

  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState(null);
  const [qrCodeDataUrl, setQrCodeDataUrl] = useState(null);
  const [emailStatus, setEmailStatus] = useState(null);
  const [visitorId, setVisitorId] = useState(null);

  const qrCodeDataRef = useRef(null);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));

    if (error && error[name]) {
      setError((prev) => {
        const newErrors = { ...prev };
        delete newErrors[name];
        return newErrors;
      });
    }
  };

  // Function to generate QR code on frontend
  const generateQRCode = async (data) => {
    try {
      // Create the URL for the visitor card
      const cardUrl = `${window.location.origin}/visitor/${data.id}/card`;

      // Generate QR code as Data URL
      const qrCodeUrl = await QRCode.toDataURL(cardUrl, {
        width: 300,
        margin: 2,
        color: {
          dark: "#000000",
          light: "#FFFFFF",
        },
      });

      return qrCodeUrl;
    } catch (err) {
      console.error("QR generation error:", err);
      throw err;
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError(null);
    setSuccess(false);
    setQrCodeDataUrl(null);
    setEmailStatus(null);
    setVisitorId(null);

    try {
      // Step 1: First create the visitor in backend
      const response = await axiosInstance.post("/visitors", formData);

      console.log("Step 1 - Visitor Created:", response.data);

      if (response.data.success) {
        const visitor = response.data.visitor;
        const visitorId = visitor.id;
        setVisitorId(visitorId);

        // Step 2: Generate QR code on frontend
        console.log("Step 2 - Generating QR code...");
        const qrCodeUrl = await generateQRCode({
          id: visitorId,
          ...formData,
        });

        setQrCodeDataUrl(qrCodeUrl);
        qrCodeDataRef.current = qrCodeUrl;

        // Step 3: Send QR code to backend for storage and email
        console.log("Step 3 - Sending QR code to backend...");

        // Extract base64 data from data URL
        const base64Data = qrCodeUrl.split(",")[1];

        const qrResponse = await axiosInstance.post("/visitors/qrcode", {
          visitor_id: visitorId,
          qr_code_data: base64Data,
          qr_code_metadata: {
            name: formData.visitor_name,
            email: formData.email,
            mobile: formData.mobile,
            business: formData.bussiness_name || "N/A",
            generated_at: new Date().toISOString(),
            frontend_generated: true,
          },
        });

        console.log("Step 3 - QR Code Response:", qrResponse.data);

        if (qrResponse.data.success) {
          setSuccess(true);
          setEmailStatus(qrResponse.data.email_status || "sent");

          // Reset form
          setFormData({
            visitor_name: "",
            bussiness_name: "",
            mobile: "",
            phone: "",
            whatsapp_no: "",
            email: "",
            city: "",
            town: "",
            village: "",
            remark: "",
          });

          // Auto-hide after 15 seconds
          setTimeout(() => {
            setSuccess(false);
            setQrCodeDataUrl(null);
            setEmailStatus(null);
          }, 15000);
        } else {
          setError({
            general: [qrResponse.data.message || "QR code processing failed"],
          });
        }
      } else {
        setError({ general: [response.data.message || "Registration failed"] });
      }
    } catch (err) {
      console.error("API Error:", err.response || err);

      if (err.response && err.response.data) {
        const data = err.response.data;
        if (data.errors) {
          setError(data.errors);
        } else if (data.message) {
          setError({ general: [data.message] });
        } else {
          setError({ general: ["An error occurred. Please try again."] });
        }
      } else {
        setError({
          general: [err.message || "An error occurred. Please try again."],
        });
      }
    } finally {
      setLoading(false);
    }
  };

  // Function to download QR code
  const downloadQRCode = () => {
    if (!qrCodeDataUrl) return;

    const link = document.createElement("a");
    link.href = qrCodeDataUrl;
    link.download = `visitor-${visitorId}-qrcode.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  // Function to copy QR code URL
  const copyQRCodeUrl = () => {
    if (!visitorId) return;

    const cardUrl = `${window.location.origin}/visitor/${visitorId}/card`;
    navigator.clipboard
      .writeText(cardUrl)
      .then(() => {
        alert("Card URL copied to clipboard!");
      })
      .catch((err) => {
        console.error("Failed to copy: ", err);
      });
  };

  return (
    <div className="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md pt-40">
      <h2 className="text-2xl font-bold mb-6 text-gray-800">
        Visitor Registration Form
      </h2>

      {/* Success Message */}
      {success && qrCodeDataUrl && visitorId && (
        <div className="mt-4 p-4 bg-green-100 rounded">
          <p className="text-green-800 mb-2">
            Your ID card is ready. You can download it in different formats:
          </p>
          <div className="flex flex-col md:flex-row items-center md:space-x-4 space-y-4 md:space-y-0">
            <div className="text-center">
              <div className="w-48 h-48 mx-auto border border-gray-300 rounded flex items-center justify-center bg-white p-2">
                <img
                  src={qrCodeDataUrl}
                  alt="Visitor QR Code"
                  className="max-w-full max-h-full"
                />
              </div>
              <p className="text-sm text-gray-600 mt-2">
                Scan to view visitor card
              </p>
            </div>
            <div className="flex flex-col space-y-2">
              <button
                onClick={downloadQRCode}
                className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-center"
              >
                <i className="fas fa-qrcode mr-2"></i>
                Download QR Code Only
              </button>

              <a
                href={`${axiosInstance.defaults.baseURL}/visitors/${visitorId}/idcard/download`}
                target="_blank"
                rel="noopener noreferrer"
                className="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition text-center"
              >
                <i className="fas fa-id-card mr-2"></i>
                Download ID Card (PDF)
              </a>

              <a
                href={`${axiosInstance.defaults.baseURL}/visitors/${visitorId}/idcard/view`}
                target="_blank"
                rel="noopener noreferrer"
                className="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-center"
              >
                <i className="fas fa-eye mr-2"></i>
                View ID Card (PDF)
              </a>

              <a
                href={`/visitor/${visitorId}/card`}
                target="_blank"
                rel="noopener noreferrer"
                className="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition text-center"
              >
                <i className="fas fa-external-link-alt mr-2"></i>
                View Digital Card
              </a>

              <button
                onClick={() => window.print()}
                className="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition"
              >
                <i className="fas fa-print mr-2"></i>
                Print ID Card
              </button>
            </div>
          </div>

          {/* Preview of ID Card Design */}
          <div className="mt-6 p-4 bg-linear-to-r from-blue-500 to-purple-600 rounded-lg text-white max-w-md mx-auto">
            <div className="text-center mb-4">
              <h3 className="text-xl font-bold">ID Card Preview</h3>
              <p className="text-sm opacity-90">Standard Business Card Size</p>
            </div>
            <div className="flex items-center space-x-4">
              <div className="w-24 h-24 bg-white rounded p-1">
                <img src={qrCodeDataUrl} alt="QR" className="w-full h-full" />
              </div>
              <div className="flex-1">
                <h4 className="font-bold text-lg">{formData.visitor_name}</h4>
                <p className="text-sm mt-1">📱 {formData.mobile}</p>
                <p className="text-sm">✉️ {formData.email}</p>
                {formData.bussiness_name && (
                  <p className="text-sm">🏢 {formData.bussiness_name}</p>
                )}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Error Message */}
      {error && error.general && (
        <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <div className="flex items-center">
            <svg
              className="w-5 h-5 text-red-500 mr-2"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fillRule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clipRule="evenodd"
              />
            </svg>
            <span className="text-red-700">{error.general[0]}</span>
          </div>
        </div>
      )}

      {/* Form */}
      <form onSubmit={handleSubmit} className="space-y-6">
        {/* Personal Information */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Full Name *
            </label>
            <input
              type="text"
              name="visitor_name"
              value={formData.visitor_name}
              onChange={handleChange}
              required
              className={`w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                error?.visitor_name ? "border-red-500" : "border-gray-300"
              }`}
              placeholder="Enter your full name"
            />
            {error?.visitor_name && (
              <p className="text-red-500 text-sm mt-1">
                {error.visitor_name[0]}
              </p>
            )}
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Business Name
            </label>
            <input
              type="text"
              name="bussiness_name"
              value={formData.bussiness_name}
              onChange={handleChange}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Optional"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Mobile Number *
            </label>
            <input
              type="tel"
              name="mobile"
              value={formData.mobile}
              onChange={handleChange}
              required
              className={`w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                error?.mobile ? "border-red-500" : "border-gray-300"
              }`}
              placeholder="e.g., +1234567890"
            />
            {error?.mobile && (
              <p className="text-red-500 text-sm mt-1">{error.mobile[0]}</p>
            )}
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Email Address *
            </label>
            <input
              type="email"
              name="email"
              value={formData.email}
              onChange={handleChange}
              required
              className={`w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                error?.email ? "border-red-500" : "border-gray-300"
              }`}
              placeholder="your@email.com"
            />
            {error?.email && (
              <p className="text-red-500 text-sm mt-1">{error.email[0]}</p>
            )}
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Phone Number
            </label>
            <input
              type="tel"
              name="phone"
              value={formData.phone}
              onChange={handleChange}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Optional"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              WhatsApp Number
            </label>
            <input
              type="tel"
              name="whatsapp_no"
              value={formData.whatsapp_no}
              onChange={handleChange}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Optional"
            />
          </div>
        </div>

        {/* Address Information */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              City
            </label>
            <input
              type="text"
              name="city"
              value={formData.city}
              onChange={handleChange}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="City"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Town
            </label>
            <input
              type="text"
              name="town"
              value={formData.town}
              onChange={handleChange}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Town"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Village
            </label>
            <input
              type="text"
              name="village"
              value={formData.village}
              onChange={handleChange}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Village"
            />
          </div>
        </div>

        {/* Remarks */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">
            Remarks / Notes
          </label>
          <textarea
            name="remark"
            value={formData.remark}
            onChange={handleChange}
            rows="4"
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Any additional information or notes..."
          />
        </div>

        {/* Submit Button */}
        <div className="pt-4">
          <button
            type="submit"
            disabled={loading}
            className={`w-full py-3 px-4 rounded-md font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 ${
              loading
                ? "bg-blue-400 cursor-not-allowed"
                : "bg-blue-600 hover:bg-blue-700"
            } text-white transition`}
          >
            {loading ? (
              <span className="flex items-center justify-center">
                <svg
                  className="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                  xmlns="http://www.w3.org/2000/svg"
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
                Processing...
              </span>
            ) : (
              "Register & Generate QR Code"
            )}
          </button>
        </div>
      </form>
    </div>
  );
};

export default Visitors;
