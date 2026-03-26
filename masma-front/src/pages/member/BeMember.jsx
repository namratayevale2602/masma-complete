import React, { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import axiosInstance from "../../services/api";
import { qrcode } from "../../assets/index";
import { 
  ChevronRight, 
  ChevronLeft, 
  CheckCircle, 
  User, 
  Phone, 
  MapPin, 
  Briefcase, 
  Users, 
  CreditCard,
  FileText,
  Check,
  X
} from "lucide-react";

const BeMember = () => {
  const [currentStep, setCurrentStep] = useState(1);
  const [showSuccess, setShowSuccess] = useState(false);
  const [successData, setSuccessData] = useState({ memberId: null, isRenewal: false });
  const [formData, setFormData] = useState({
    applicant_name: "",
    date_of_birth: "",
    organization: "",
    mobile: "",
    phone: "",
    whatsapp_no: "",
    office_email: "",
    city: "",
    town: "",
    village: "",
    website: "",
    organization_type: "",
    business_category: "",
    date_of_incorporation: "",
    pan_number: "",
    gst_number: "",
    about_service: "",
    membership_reference_1: "",
    membership_reference_2: "",
    registration_type: "",
    registration_amount: "",
    payment_mode: "",
    transaction_reference: "",
    declaration: false,
    applicant_photo: null,
    visiting_card: null,
    payment_screenshot: null,
  });

  const [loading, setLoading] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [message, setMessage] = useState({ type: "", text: "" });
  const [fileInfo, setFileInfo] = useState({
    applicant_photo: null,
    visiting_card: null,
    payment_screenshot: null
  });

  const organizationTypes = [
    "sole_proprietorship",
    "partnership",
    "limited_liability_partnership",
    "private_limited_company",
    "public_limited_company",
    "one_person_company",
    "other",
  ];

  const businessCategories = [
    "student",
    "plumber",
    "electrician",
    "installer_solar_pv",
    "solar_water_heater",
    "supplier",
    "dealer",
    "distributor",
    "associate_member",
    "manufacturer",
  ];

  const registrationTypes = [
    { type: "epc_classic", amount: "3000", name: "EPC Classic", tip: "500₹ for registration" },
    { type: "renew_epc_classic", amount: "2500", name: "Renew EPC Classic" },
    { type: "student", amount: "1000", name: "Student" },
    { type: "renew_student", amount: "1000", name: "Renew Student" },
    { type: "dealer_distributor", amount: "5500", name: "Dealer/Distributor", tip: "500₹ for registration" },
    { type: "renew_dealer_distributor", amount: "5000", name: "Renew Dealer/Distributor" },
    { type: "silver_corporate", amount: "10500", name: "Silver Corporate", tip: "500₹ for registration" },
    { type: "renew_silver_corporate", amount: "10000", name: "Renew Silver Corporate" },
    { type: "gold_corporate", amount: "20500", name: "Gold Corporate", tip: "500₹ for registration" },
    { type: "renew_gold_corporate", amount: "20000", name: "Renew Gold Corporate" },
  ];

  const paymentModes = [
    { value: "neft", name: "NEFT" },
    { value: "upi", name: "UPI" },
    { value: "rtgs", name: "RTGS" },
    { value: "imps", name: "IMPS" },
  ];

  const steps = [
    { number: 1, name: "Personal Info", icon: User },
    { number: 2, name: "Contact", icon: Phone },
    { number: 3, name: "Address", icon: MapPin },
    { number: 4, name: "Business", icon: Briefcase },
    { number: 5, name: "References", icon: Users },
    { number: 6, name: "Payment", icon: CreditCard },
  ];

  const getDisplayName = (value, type) => {
    if (type === "organization_type") {
      const names = {
        sole_proprietorship: "Sole Proprietorship",
        partnership: "Partnership",
        limited_liability_partnership: "Limited Liability Partnership (LLP)",
        private_limited_company: "Private Limited Company",
        public_limited_company: "Public Limited Company",
        one_person_company: "One Person Company (OPC)",
        other: "Other",
      };
      return names[value] || value;
    }

    if (type === "business_category") {
      const names = {
        student: "Student",
        plumber: "Plumber",
        electrician: "Electrician",
        installer_solar_pv: "Installer Solar PV",
        solar_water_heater: "Solar Water Heater",
        supplier: "Supplier",
        dealer: "Dealer",
        distributor: "Distributor",
        associate_member: "Associate Member",
        manufacturer: "Manufacturer",
      };
      return names[value] || value;
    }

    return value;
  };

  const handleInputChange = (e) => {
    const { name, value, type, checked, files } = e.target;

    if (type === "file") {
      const file = files[0];
      if (file) {
        const maxSizeMB = 5;
        const maxSizeBytes = maxSizeMB * 1024 * 1024;
        
        if (file.size > maxSizeBytes) {
          setMessage({
            type: "error",
            text: `File size exceeds ${maxSizeMB}MB limit. Please compress or choose a smaller image. (Current size: ${(file.size / 1024 / 1024).toFixed(2)}MB)`
          });
          e.target.value = "";
          return;
        }
        
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
          setMessage({
            type: "error",
            text: `Invalid file type. Please upload JPG, PNG, GIF, or WEBP images only.`
          });
          e.target.value = "";
          return;
        }
        
        setMessage({ type: "", text: "" });
        
        setFormData((prev) => ({
          ...prev,
          [name]: file,
        }));
        
        // Update file info
        setFileInfo((prev) => ({
          ...prev,
          [name]: { name: file.name, size: formatFileSize(file.size) }
        }));
      }
    } else if (type === "checkbox") {
      setFormData((prev) => ({
        ...prev,
        [name]: checked,
      }));
    } else {
      setFormData((prev) => ({
        ...prev,
        [name]: value,
      }));
    }
  };

  const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  };

  const handleRegistrationTypeChange = (e) => {
    const selectedType = registrationTypes.find(
      (rt) => rt.type === e.target.value
    );
    setFormData((prev) => ({
      ...prev,
      registration_type: e.target.value,
      registration_amount: selectedType ? selectedType.amount : "",
    }));
  };

  const validateStep = () => {
    switch (currentStep) {
      case 1:
        return formData.applicant_name && formData.date_of_birth;
      case 2:
        return formData.mobile && formData.office_email;
      case 5:
        return formData.membership_reference_1 && formData.membership_reference_2;
      case 6:
        return formData.registration_type && formData.payment_mode && formData.declaration;
      default:
        return true;
    }
  };

  const nextStep = () => {
    if (validateStep()) {
      setCurrentStep((prev) => Math.min(prev + 1, steps.length));
      setMessage({ type: "", text: "" });
    } else {
      setMessage({
        type: "error",
        text: "Please fill in all required fields before proceeding.",
      });
    }
  };

  const prevStep = () => {
    setCurrentStep((prev) => Math.max(prev - 1, 1));
    setMessage({ type: "", text: "" });
  };

  const resetForm = () => {
    setFormData({
      applicant_name: "",
      date_of_birth: "",
      organization: "",
      mobile: "",
      phone: "",
      whatsapp_no: "",
      office_email: "",
      city: "",
      town: "",
      village: "",
      website: "",
      organization_type: "",
      business_category: "",
      date_of_incorporation: "",
      pan_number: "",
      gst_number: "",
      about_service: "",
      membership_reference_1: "",
      membership_reference_2: "",
      registration_type: "",
      registration_amount: "",
      payment_mode: "",
      transaction_reference: "",
      declaration: false,
      applicant_photo: null,
      visiting_card: null,
      payment_screenshot: null,
    });
    setFileInfo({
      applicant_photo: null,
      visiting_card: null,
      payment_screenshot: null
    });
    setCurrentStep(1);
    setIsSubmitting(false);
    setLoading(false);
  };

  const handleCloseSuccess = () => {
    setShowSuccess(false);
    resetForm();
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    if (isSubmitting) {
      setMessage({
        type: "warning",
        text: "Please wait, your submission is already being processed..."
      });
      return;
    }
    
    if (!validateStep()) {
      setMessage({
        type: "error",
        text: "Please fill in all required fields.",
      });
      return;
    }

    if (!formData.declaration) {
      setMessage({
        type: "error",
        text: "You must accept the declaration to proceed.",
      });
      return;
    }

    setIsSubmitting(true);
    setLoading(true);
    setMessage({ type: "", text: "" });

    try {
      const submitData = new FormData();

      Object.keys(formData).forEach((key) => {
        if ((key === "applicant_photo" || key === "visiting_card" || key === "payment_screenshot") && formData[key]) {
          submitData.append(key, formData[key]);
        } else if (formData[key] !== null && formData[key] !== undefined && formData[key] !== "") {
          if (key === "declaration") {
            submitData.append(key, formData[key] ? "1" : "0");
          } else {
            submitData.append(key, formData[key]);
          }
        }
      });

      const response = await axiosInstance.post("/registrations", submitData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });

      // Store member ID from response
      const memberId = response.data.member_id;
      const isRenewal = response.data.is_renewal;
      
      setSuccessData({ memberId, isRenewal });
      setShowSuccess(true);
      
      // Don't reset form immediately, wait for modal close
      // resetForm() will be called in handleCloseSuccess

    } catch (error) {
      console.error("Registration error:", error);
      let errorMessage = "Failed to submit registration. Please try again.";

      if (error.response?.data?.errors) {
        const errors = error.response.data.errors;
        errorMessage = Object.values(errors).flat().join(", ");
      } else if (error.response?.data?.message) {
        errorMessage = error.response.data.message;
      } else if (error.response?.data?.duplicate) {
        errorMessage = error.response.data.message || "Duplicate submission detected. Please check your email for confirmation.";
      } else if (error.code === 'ERR_NETWORK') {
        errorMessage = "Network error. Please check your internet connection and try again.";
      }

      setMessage({
        type: "error",
        text: errorMessage,
      });
      
      setIsSubmitting(false);
      setLoading(false);
    }
  };

  // Success Modal Component - Defined outside to prevent re-renders
  const SuccessModal = ({ memberId, isRenewal, onClose }) => (
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
      className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
      onClick={onClose}
    >
      <motion.div
        initial={{ scale: 0.8, opacity: 0 }}
        animate={{ scale: 1, opacity: 1 }}
        exit={{ scale: 0.8, opacity: 0 }}
        transition={{ type: "spring", damping: 20, stiffness: 300 }}
        className="bg-white rounded-xl p-8 max-w-md w-full mx-4 shadow-2xl relative"
        onClick={(e) => e.stopPropagation()}
      >
        <button
          onClick={onClose}
          className="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors"
        >
          <X className="h-6 w-6" />
        </button>

        <div className="text-center">
          <motion.div
            initial={{ scale: 0 }}
            animate={{ scale: 1 }}
            transition={{ delay: 0.2, type: "spring", stiffness: 200 }}
            className="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6"
          >
            <CheckCircle className="h-12 w-12 text-green-600" />
          </motion.div>
          
          <motion.h2
            initial={{ y: 20, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            transition={{ delay: 0.3 }}
            className="text-2xl font-bold text-gray-900 mb-3"
          >
            {isRenewal ? "Membership Renewed Successfully!" : "Registration Successful!"}
          </motion.h2>
          
          {memberId && (
            <motion.div
              initial={{ y: 20, opacity: 0 }}
              animate={{ y: 0, opacity: 1 }}
              transition={{ delay: 0.4 }}
              className="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6"
            >
              <p className="text-sm text-blue-600 mb-1">Your Member ID</p>
              <p className="text-2xl font-bold text-blue-800 font-mono">{memberId}</p>
              <p className="text-xs text-blue-600 mt-2">
                Please save this ID for future reference
              </p>
            </motion.div>
          )}
          
          <motion.p
            initial={{ y: 20, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            transition={{ delay: 0.5 }}
            className="text-gray-600 mb-6"
          >
            Thank you for {isRenewal ? "renewing your membership" : "registering"}. 
            Your application has been submitted successfully. 
            We will review your details and get back to you soon.
          </motion.p>
          
          <motion.button
            initial={{ y: 20, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            transition={{ delay: 0.6 }}
            onClick={onClose}
            className="w-full px-6 py-3 bg-[#005aa8] text-white font-semibold rounded-lg hover:bg-[#004080] transition duration-300 focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:ring-offset-2"
          >
            Close
          </motion.button>
        </div>
      </motion.div>
    </motion.div>
  );

  // Step Indicator Component
  const StepIndicator = () => (
    <div className="px-8 pt-8">
      <div className="flex justify-between items-center">
        {steps.map((step, index) => (
          <React.Fragment key={step.number}>
            <div className="flex flex-col items-center">
              <motion.div
                initial={{ scale: 1 }}
                animate={{ 
                  scale: currentStep === step.number ? 1.1 : 1,
                  backgroundColor: currentStep >= step.number ? "#005aa8" : "#e5e7eb",
                  color: currentStep >= step.number ? "white" : "#9ca3af"
                }}
                className={`w-10 h-10 rounded-full flex items-center justify-center transition-colors duration-300`}
              >
                {currentStep > step.number ? (
                  <Check className="h-5 w-5" />
                ) : (
                  <step.icon className="h-5 w-5" />
                )}
              </motion.div>
              <span className={`text-xs mt-2 ${
                currentStep === step.number ? "text-[#005aa8] font-semibold" : "text-gray-500"
              }`}>
                {step.name}
              </span>
            </div>
            {index < steps.length - 1 && (
              <div className={`flex-1 h-0.5 mx-2 ${
                currentStep > index + 1 ? "bg-[#005aa8]" : "bg-gray-200"
              }`} />
            )}
          </React.Fragment>
        ))}
      </div>
    </div>
  );

  // Render different steps
  const renderStep = () => {
    switch (currentStep) {
      case 1:
        return (
          <motion.div
            key="step1"
            initial={{ x: 50, opacity: 0 }}
            animate={{ x: 0, opacity: 1 }}
            exit={{ x: -50, opacity: 0 }}
            className="space-y-4"
          >
            <h2 className="text-xl font-semibold text-[#ed6605] mb-4 flex items-center">
              <User className="mr-2" /> Personal Information
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Applicant Name *
                </label>
                <input
                  type="text"
                  name="applicant_name"
                  value={formData.applicant_name}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Date of Birth *
                </label>
                <input
                  type="date"
                  name="date_of_birth"
                  value={formData.date_of_birth}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Applicant Photo
                  <span className="text-xs text-gray-500 ml-2">(Max 5MB, JPG/PNG/GIF/WEBP)</span>
                </label>
                <input
                  type="file"
                  name="applicant_photo"
                  onChange={handleInputChange}
                  accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                />
                {fileInfo.applicant_photo && (
                  <p className="text-xs text-green-600 mt-1">
                    Selected: {fileInfo.applicant_photo.name} ({fileInfo.applicant_photo.size})
                  </p>
                )}
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Visiting Card
                  <span className="text-xs text-gray-500 ml-2">(Max 5MB, JPG/PNG/GIF/WEBP)</span>
                </label>
                <input
                  type="file"
                  name="visiting_card"
                  onChange={handleInputChange}
                  accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                />
                {fileInfo.visiting_card && (
                  <p className="text-xs text-green-600 mt-1">
                    Selected: {fileInfo.visiting_card.name} ({fileInfo.visiting_card.size})
                  </p>
                )}
              </div>
            </div>
          </motion.div>
        );

      case 2:
        return (
          <motion.div
            key="step2"
            initial={{ x: 50, opacity: 0 }}
            animate={{ x: 0, opacity: 1 }}
            exit={{ x: -50, opacity: 0 }}
            className="space-y-4"
          >
            <h2 className="text-xl font-semibold text-[#ed6605] mb-4 flex items-center">
              <Phone className="mr-2" /> Contact Information
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Mobile *
                </label>
                <input
                  type="tel"
                  name="mobile"
                  value={formData.mobile}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Phone
                </label>
                <input
                  type="tel"
                  name="phone"
                  value={formData.phone}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  WhatsApp Number
                </label>
                <input
                  type="tel"
                  name="whatsapp_no"
                  value={formData.whatsapp_no}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Office Email *
                </label>
                <input
                  type="email"
                  name="office_email"
                  value={formData.office_email}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                  required
                />
              </div>
            </div>
          </motion.div>
        );

      case 3:
        return (
          <motion.div
            key="step3"
            initial={{ x: 50, opacity: 0 }}
            animate={{ x: 0, opacity: 1 }}
            exit={{ x: -50, opacity: 0 }}
            className="space-y-4"
          >
            <h2 className="text-xl font-semibold text-[#ed6605] mb-4 flex items-center">
              <MapPin className="mr-2" /> Address Information
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  City
                </label>
                <input
                  type="text"
                  name="city"
                  value={formData.city}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Town
                </label>
                <input
                  type="text"
                  name="town"
                  value={formData.town}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Village
                </label>
                <input
                  type="text"
                  name="village"
                  value={formData.village}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                />
              </div>
            </div>
          </motion.div>
        );

      case 4:
        return (
          <motion.div
            key="step4"
            initial={{ x: 50, opacity: 0 }}
            animate={{ x: 0, opacity: 1 }}
            exit={{ x: -50, opacity: 0 }}
            className="space-y-4"
          >
            <h2 className="text-xl font-semibold text-[#ed6605] mb-4 flex items-center">
              <Briefcase className="mr-2" /> Business Information
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Organization
                </label>
                <input
                  type="text"
                  name="organization"
                  value={formData.organization}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Website
                </label>
                <input
                  type="url"
                  name="website"
                  value={formData.website}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Organization Type
                </label>
                <select
                  name="organization_type"
                  value={formData.organization_type}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                >
                  <option value="">Select Organization Type</option>
                  {organizationTypes.map((type, index) => (
                    <option key={index} value={type}>
                      {getDisplayName(type, "organization_type")}
                    </option>
                  ))}
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Business Category
                </label>
                <select
                  name="business_category"
                  value={formData.business_category}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                >
                  <option value="">Select Business Category</option>
                  {businessCategories.map((category, index) => (
                    <option key={index} value={category}>
                      {getDisplayName(category, "business_category")}
                    </option>
                  ))}
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Date of Incorporation
                </label>
                <input
                  type="date"
                  name="date_of_incorporation"
                  value={formData.date_of_incorporation}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  PAN Number
                </label>
                <input
                  type="text"
                  name="pan_number"
                  value={formData.pan_number}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                />
              </div>
              <div className="md:col-span-2">
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  GST Number
                </label>
                <input
                  type="text"
                  name="gst_number"
                  value={formData.gst_number}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                />
              </div>
              <div className="md:col-span-2">
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  About Service
                </label>
                <textarea
                  name="about_service"
                  value={formData.about_service}
                  onChange={handleInputChange}
                  rows={4}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                />
              </div>
            </div>
          </motion.div>
        );

      case 5:
        return (
          <motion.div
            key="step5"
            initial={{ x: 50, opacity: 0 }}
            animate={{ x: 0, opacity: 1 }}
            exit={{ x: -50, opacity: 0 }}
            className="space-y-4"
          >
            <h2 className="text-xl font-semibold text-[#ed6605] mb-4 flex items-center">
              <Users className="mr-2" /> Membership References
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Membership Reference 1 *
                </label>
                <input
                  type="text"
                  name="membership_reference_1"
                  value={formData.membership_reference_1}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Membership Reference 2 *
                </label>
                <input
                  type="text"
                  name="membership_reference_2"
                  value={formData.membership_reference_2}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                  required
                />
              </div>
            </div>
          </motion.div>
        );

      case 6:
        return (
          <motion.div
            key="step6"
            initial={{ x: 50, opacity: 0 }}
            animate={{ x: 0, opacity: 1 }}
            exit={{ x: -50, opacity: 0 }}
            className="space-y-6"
          >
            <h2 className="text-xl font-semibold text-[#ed6605] mb-4 flex items-center">
              <CreditCard className="mr-2" /> Payment Details
            </h2>
            
            {/* Registration Type */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Registration Type/Plan *
                </label>
                <select
                  name="registration_type"
                  value={formData.registration_type}
                  onChange={handleRegistrationTypeChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                  required
                >
                  <option value="">Select Registration Type</option>
                  {registrationTypes.map((rt, index) => (
                    <option key={index} value={rt.type}>
                      {rt.name} - ₹{rt.amount}
                    </option>
                  ))}
                </select>
                
                {/* Show tip only for selected registration type */}
                {formData.registration_type && 
                  (() => {
                    const selectedType = registrationTypes.find(rt => rt.type === formData.registration_type);
                    return selectedType?.tip && (
                      <div className="mt-2 text-sm text-blue-600 bg-blue-50 p-2 rounded">
                         Tip: {selectedType.tip}
                      </div>
                    );
                  })()
                }
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Registration Amount (₹)
                </label>
                <input
                  type="text"
                  name="registration_amount"
                  value={formData.registration_amount}
                  readOnly
                  className="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 font-semibold text-[#005aa8]"
                />
              </div>
              
            </div>

            {/* Bank Details */}
            <div className="bg-blue-50 p-4 rounded-lg border border-blue-200">
              <h5 className="font-semibold text-[#005aa8] mb-2">Bank Details for Payment</h5>
              <div className="space-y-1 text-sm">
                <p><span className="font-medium">Account Number:</span> 050310110001006</p>
                <p><span className="font-medium">IFSC Code:</span> BKID0000553</p>
                <p><span className="font-medium">Account Type:</span> Savings</p>
                <p><span className="font-medium">Bank Name:</span> Bank Of India</p>
                <p><span className="font-medium">Branch Name:</span> Satara Road, Pune</p>
              </div>
            </div>

            {/* QR Code Placeholder */}
            <div className="border-2 border-dashed border-gray-300 p-4 rounded-lg text-center">
              <h5 className="font-semibold text-gray-700 mb-2">Scan QR Code to Pay</h5>
              {/* <div className="bg-gray-100 h-32 w-32 mx-auto rounded-lg flex items-center justify-center">
                <Upload className="h-8 w-8 text-gray-400" />
              </div>
              <p className="text-xs text-gray-500 mt-2">QR Code Image Placeholder</p> */}
              <img src={qrcode} alt="Payment QR" className="md:h-200 " />
            </div>

            {/* Payment Mode and Details */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Payment Mode *
                </label>
                <select
                  name="payment_mode"
                  value={formData.payment_mode}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                  required
                >
                  <option value="">Select Payment Mode</option>
                  {paymentModes.map((mode, index) => (
                    <option key={index} value={mode.value}>
                      {mode.name}
                    </option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Transaction Reference (UTR/UPI/Transaction ID) *
                </label>
                <input
                  type="text"
                  name="transaction_reference"
                  value={formData.transaction_reference}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                  required
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Upload Payment Screenshot *
                  <span className="text-xs text-gray-500 ml-2">(Max 5MB, JPG/PNG/GIF/WEBP)</span>
                </label>
                <input
                  type="file"
                  name="payment_screenshot"
                  onChange={handleInputChange}
                  accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#005aa8] focus:border-transparent"
                  required
                />
                {fileInfo.payment_screenshot && (
                  <p className="text-xs text-green-600 mt-1">
                    Selected: {fileInfo.payment_screenshot.name} ({fileInfo.payment_screenshot.size})
                  </p>
                )}
              </div>
            </div>

            {/* Declaration */}
            <div className="flex items-start space-x-3 mt-4 p-4 bg-gray-50 rounded-lg">
              <input
                type="checkbox"
                name="declaration"
                checked={formData.declaration}
                onChange={handleInputChange}
                className="mt-1 rounded focus:ring-[#005aa8] text-[#005aa8]"
                required
              />
              <label className="text-sm text-gray-700">
                I hereby declare that the information provided above is true and
                correct to the best of my knowledge. *
              </label>
            </div>
          </motion.div>
        );

      default:
        return null;
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 py-8 pt-40 px-4 relative">
      <AnimatePresence>
        {showSuccess && (
          <SuccessModal 
            memberId={successData.memberId}
            isRenewal={successData.isRenewal}
            onClose={handleCloseSuccess}
          />
        )}
      </AnimatePresence>

      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
        className="max-w-6xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden"
      >
        {/* Header */}
        <div
          className="bg-[#005aa8] py-6 px-8 text-white"
          style={{
            background: "linear-gradient(135deg, #005aa8 0%, #003366 100%)",
          }}
        >
          <h1 className="text-3xl font-bold">Registration Form</h1>
          <p className="text-blue-100 mt-2">
            Step {currentStep} of {steps.length}: {steps[currentStep - 1].name}
          </p>
        </div>

        {/* Step Indicator */}
        <StepIndicator />

        {/* Message Alert */}
        <AnimatePresence>
          {message.text && (
            <motion.div
              initial={{ opacity: 0, y: -10 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -10 }}
              className={`mx-8 mt-4 p-4 rounded-md ${
                message.type === "success"
                  ? "bg-green-50 border border-green-200 text-green-800"
                  : "bg-red-50 border border-red-200 text-red-800"
              }`}
            >
              {message.text}
            </motion.div>
          )}
        </AnimatePresence>

        {/* Form */}
        <form onSubmit={handleSubmit} className="p-8 space-y-6">
          <AnimatePresence mode="wait">
            {renderStep()}
          </AnimatePresence>

          {/* Navigation Buttons */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            className="flex justify-between pt-6 border-t border-gray-200"
          >
            <button
              type="button"
              onClick={prevStep}
              disabled={currentStep === 1}
              className={`flex items-center px-6 py-2 rounded-md transition duration-300 ${
                currentStep === 1
                  ? "bg-gray-100 text-gray-400 cursor-not-allowed"
                  : "bg-gray-200 text-gray-700 hover:bg-gray-300"
              }`}
            >
              <ChevronLeft className="h-4 w-4 mr-2" />
              Previous
            </button>

            {currentStep === steps.length ? (
              <button
                type="submit"
                disabled={loading || isSubmitting}
                className="flex items-center px-8 py-3 bg-[#ed6605] text-white font-semibold rounded-md hover:bg-[#d45a04] transition duration-300 focus:outline-none focus:ring-2 focus:ring-[#ed6605] focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {loading || isSubmitting ? (
                  <>
                    <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                    Submitting...
                  </>
                ) : (
                  <>
                    <FileText className="h-4 w-4 mr-2" />
                    Submit Registration
                  </>
                )}
              </button>
            ) : (
              <button
                type="button"
                onClick={nextStep}
                className="flex items-center px-6 py-2 bg-[#005aa8] text-white rounded-md hover:bg-[#004080] transition duration-300"
              >
                Next
                <ChevronRight className="h-4 w-4 ml-2" />
              </button>
            )}
          </motion.div>
        </form>
      </motion.div>
    </div>
  );
};

export default BeMember;