import React, { useState, useEffect, useRef, useCallback } from "react";
import { motion, AnimatePresence } from "framer-motion";
import axios from "../../services/api";
import {
  FaCheck,
  FaTimes,
  FaStar,
  FaInfoCircle,
  FaUsers,
  FaBuilding,
  FaStore,
  FaCrown,
  FaGraduationCap,
  FaRupeeSign,
  FaCalendarAlt,
  FaRegCreditCard,
  FaEnvelope,
  FaGlobe,
  FaChevronDown,
  FaChevronUp,
  FaVideo,
  FaBullhorn,
  FaTags,
  FaUserGraduate,
  FaUserTie,
  FaChartLine,
  FaHandshake,
  FaFileContract,
  FaPercent,
  FaChair,
  FaExternalLinkAlt,
  FaChevronLeft,
  FaChevronRight,
} from "react-icons/fa";

const MembershipPlans = () => {
  const [membershipData, setMembershipData] = useState({ membershipPlans: [], featureLabels: [] });
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [selectedPlan, setSelectedPlan] = useState(null);
  const [viewMode, setViewMode] = useState("table");
  const [expandedFeature, setExpandedFeature] = useState(null);
  const [isMobile, setIsMobile] = useState(false);
  const [currentSlide, setCurrentSlide] = useState(0);
  const sliderRef = useRef(null);

  // Fetch data from API
  useEffect(() => {
    const fetchMembershipData = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/v1/membership-plans');
        
        if (response.data.success) {
          setMembershipData(response.data.data);
        } else {
          setError('Failed to load membership data');
        }
      } catch (err) {
        console.error('Error fetching membership data:', err);
        setError('Could not connect to server');
      } finally {
        setLoading(false);
      }
    };

    fetchMembershipData();
  }, []);

  // Check if mobile on mount and resize
  useEffect(() => {
    const checkIfMobile = () => {
      setIsMobile(window.innerWidth < 768);
    };

    checkIfMobile();
    window.addEventListener("resize", checkIfMobile);
    return () => window.removeEventListener("resize", checkIfMobile);
  }, []);

  const handleRedirect = (planName) => {
    const planUrls = {
      Students: "bemember",
      "EPC Classic": "bemember",
      "Dealer /Distributor": "bemember",
      "Corporate Silver": "bemember",
      "Corporate Gold": "bemember",
    };
    const url = planUrls[planName] || "bemember";
    window.open(url, "_blank");
  };

  const handleLearnMore = () => {
    window.open("bemember", "_blank");
  };

  const handleNext = useCallback(() => {
    setCurrentSlide((prev) =>
      prev === membershipData.membershipPlans.length - 1 ? 0 : prev + 1,
    );
  }, [membershipData.membershipPlans.length]);

  const handlePrev = useCallback(() => {
    setCurrentSlide((prev) =>
      prev === 0 ? membershipData.membershipPlans.length - 1 : prev - 1,
    );
  }, [membershipData.membershipPlans.length]);

  const goToSlide = useCallback((index) => {
    setCurrentSlide(index);
  }, []);

  const getPlanIcon = (planName) => {
    switch (planName) {
      case "Students":
        return <FaUserGraduate className="w-6 h-6" />;
      case "EPC Classic":
        return <FaUserTie className="w-6 h-6" />;
      case "Dealer /Distributor":
        return <FaStore className="w-6 h-6" />;
      case "Corporate Silver":
        return <FaBuilding className="w-6 h-6" />;
      case "Corporate Gold":
        return <FaChartLine className="w-6 h-6" />;
      default:
        return <FaUsers className="w-6 h-6" />;
    }
  };

  const getFeatureIcon = (featureIndex) => {
    const icons = [
      <FaUsers className="w-4 h-4" />,
      <FaVideo className="w-4 h-4" />,
      <FaUserGraduate className="w-4 h-4" />,
      <FaHandshake className="w-4 h-4" />,
      <FaGlobe className="w-4 h-4" />,
      <FaBullhorn className="w-4 h-4" />,
      <FaVideo className="w-4 h-4" />,
      <FaBullhorn className="w-4 h-4" />,
      <FaPercent className="w-4 h-4" />,
      <FaChair className="w-4 h-4" />,
    ];
    return icons[featureIndex] || <FaInfoCircle className="w-4 h-4" />;
  };

  const formatFeatureValue = (value) => {
    if (!value) return null;
    if (value === "Yes") return <FaCheck className="w-4 h-4 text-green-500" />;
    if (
      value === "Selective Events" ||
      value.includes("Month") ||
      value.includes("year") ||
      value.includes("Free") ||
      value.includes("Discount")
    ) {
      return (
        <span className="text-xs font-medium px-2 py-1 rounded-full bg-orange-100 text-[#ed6605]">
          {value}
        </span>
      );
    }
    if (value === "Add Charges") {
      return (
        <span className="text-xs font-medium px-2 py-1 rounded-full bg-blue-100 text-[#005aa8]">
          {value}
        </span>
      );
    }
    return value;
  };

  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.1,
      },
    },
  };

  const cardVariants = {
    hidden: { y: 30, opacity: 0 },
    visible: {
      y: 0,
      opacity: 1,
      transition: {
        duration: 0.5,
        ease: "easeOut",
      },
    },
    hover: {
      y: -10,
      boxShadow: "0 20px 40px rgba(0, 0, 0, 0.1)",
      transition: {
        duration: 0.3,
      },
    },
  };

  // Loading state
  if (loading) {
    return (
      <div className="min-h-screen bg-linear-to-br from-gray-50 to-gray-100 pt-40 py-8 px-4 md:px-8">
        <div className="max-w-7xl mx-auto text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#005aa8] border-r-transparent"></div>
          <p className="mt-4 text-gray-600">Loading membership plans...</p>
        </div>
      </div>
    );
  }

  // Error state
  if (error) {
    return (
      <div className="min-h-screen bg-linear-to-br from-gray-50 to-gray-100 pt-40 py-8 px-4 md:px-8">
        <div className="max-w-7xl mx-auto text-center text-red-500">
          {error}
        </div>
      </div>
    );
  }

  // Empty state
  if (membershipData.membershipPlans.length === 0) {
    return (
      <div className="min-h-screen bg-linear-to-br from-gray-50 to-gray-100 pt-40 py-8 px-4 md:px-8">
        <div className="max-w-7xl mx-auto text-center text-gray-600">
          No membership plans available
        </div>
      </div>
    );
  }

  // Mobile Slider Component
  const MobileSlider = React.memo(() => {
    const [touchStart, setTouchStart] = useState(0);
    const [touchEnd, setTouchEnd] = useState(0);
    const minSwipeDistance = 50;

    const onTouchStart = (e) => {
      setTouchEnd(0);
      setTouchStart(e.targetTouches[0].clientX);
    };

    const onTouchMove = (e) => setTouchEnd(e.targetTouches[0].clientX);

    const onTouchEnd = () => {
      if (!touchStart || !touchEnd) return;
      const distance = touchStart - touchEnd;
      const isLeftSwipe = distance > minSwipeDistance;
      const isRightSwipe = distance < -minSwipeDistance;

      if (isLeftSwipe) {
        handleNext();
      } else if (isRightSwipe) {
        handlePrev();
      }
    };

    return (
      <div className="md:hidden">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="relative"
        >
          {/* Current Plan Indicator */}
          <div
            className="text-center mb-6 cursor-pointer"
            onClick={() => {
              const currentPlan = membershipData.membershipPlans[currentSlide];
              handleRedirect(currentPlan.name);
            }}
          >
            {/* Optional indicator content */}
          </div>

          {/* Slider Container */}
          <div
            className="relative"
            onTouchStart={onTouchStart}
            onTouchMove={onTouchMove}
            onTouchEnd={onTouchEnd}
          >
            {/* Centered Chevron Buttons */}
            <div className="absolute inset-y-0 left-0 right-0 z-10 flex items-center justify-between px-2">
              <motion.button
                initial={{ opacity: 0.8, scale: 0.95 }}
                animate={{ opacity: 1, scale: 1 }}
                whileHover={{ scale: 1.1 }}
                whileTap={{ scale: 0.9 }}
                onClick={handlePrev}
                className="p-3 rounded-full bg-white shadow-xl hover:shadow-2xl transition-all duration-300 border border-gray-100"
                aria-label="Previous plan"
                style={{
                  background:
                    "linear-gradient(135deg, #ffffff 0%, #f8fafc 100%)",
                }}
              >
                <FaChevronLeft className="text-[#005aa8] w-5 h-5" />
              </motion.button>

              <motion.button
                initial={{ opacity: 0.8, scale: 0.95 }}
                animate={{ opacity: 1, scale: 1 }}
                whileHover={{ scale: 1.1 }}
                whileTap={{ scale: 0.9 }}
                onClick={handleNext}
                className="p-3 rounded-full bg-white shadow-xl hover:shadow-2xl transition-all duration-300 border border-gray-100"
                aria-label="Next plan"
                style={{
                  background:
                    "linear-gradient(135deg, #ffffff 0%, #f8fafc 100%)",
                }}
              >
                <FaChevronRight className="text-[#005aa8] w-5 h-5" />
              </motion.button>
            </div>

            {/* Animated Slider */}
            <div className="overflow-hidden rounded-2xl">
              <motion.div
                key={currentSlide}
                initial={{ x: 300, opacity: 0 }}
                animate={{ x: 0, opacity: 1 }}
                exit={{ x: -300, opacity: 0 }}
                transition={{
                  type: "spring",
                  stiffness: 200,
                  damping: 25,
                  mass: 0.5,
                }}
                className="w-full"
              >
                {(() => {
                  const plan = membershipData.membershipPlans[currentSlide];
                  return (
                    <motion.div
                      variants={cardVariants}
                      initial="hidden"
                      animate="visible"
                      className={`bg-white rounded-2xl shadow-xl overflow-hidden ${
                        plan.highlight ? "ring-2 ring-[#ed6605]" : ""
                      }`}
                      style={{
                        background: plan.highlight
                          ? "linear-gradient(135deg, #ffffff 0%, #fff9f5 100%)"
                          : "white",
                      }}
                    >
                      {/* Plan Header - Clickable */}
                      <div
                        className="relative h-40 cursor-pointer"
                        style={{
                          background:
                            "linear-gradient(135deg, #005aa8 0%, #004080 100%)",
                        }}
                        onClick={() => handleRedirect(plan.name)}
                      >
                        {plan.highlight && (
                          <div className="absolute top-4 right-4 z-10">
                            <div className="flex items-center bg-linear-to-r from-[#ed6605] to-[#ff8c42] text-white px-4 py-2 rounded-full shadow-lg">
                              <FaStar className="mr-2" />
                              <span className="font-bold text-sm">
                                MOST POPULAR
                              </span>
                            </div>
                          </div>
                        )}

                        <div className="absolute inset-0 flex flex-col items-center justify-center text-white p-6">
                          <h3 className="text-2xl font-bold text-white mb-2">
                            {plan.name}
                          </h3>
                        </div>
                      </div>

                      {/* All Benefits Table-like View */}
                      <div className="p-4">
                        {/* Benefits Header */}
                        <div
                          className="flex items-center mb-4 p-3 bg-linear-to-r from-[#005aa8]/10 to-[#004080]/10 rounded-lg cursor-pointer hover:bg-linear-to-r hover:from-[#005aa8]/20 hover:to-[#004080]/20 transition-all"
                          onClick={() => handleRedirect(plan.name)}
                        >
                          <FaInfoCircle className="text-[#005aa8] mr-2" />
                          <span className="font-bold text-lg text-[#005aa8]">
                            All Benefits
                          </span>
                          <FaExternalLinkAlt className="ml-auto text-[#005aa8] w-4 h-4 opacity-70" />
                        </div>

                        {/* Benefits List in Table Format */}
                        <div className="space-y-1">
                          {membershipData.featureLabels.map(
                            (label, featureIndex) => {
                              const featureKey = Object.keys(plan.features)[
                                featureIndex
                              ];
                              const value = plan.features[featureKey];

                              return (
                                <div
                                  key={featureIndex}
                                  className="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg border-b border-gray-100 last:border-b-0 cursor-pointer transition-all"
                                  onClick={() => handleRedirect(plan.name)}
                                >
                                  <div className="flex items-center flex-1 min-w-0">
                                    <div className="shrink-0 w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center mr-3">
                                      {getFeatureIcon(featureIndex)}
                                    </div>
                                    <span className="font-medium text-gray-700 text-sm truncate">
                                      {label}
                                    </span>
                                  </div>
                                  <div className="shrink-0 ml-3">
                                    {value ? (
                                      value === "Yes" ? (
                                        <FaCheck className="w-5 h-5 text-green-500" />
                                      ) : (
                                        <div
                                          className="text-xs font-medium px-3 py-1 rounded-full whitespace-nowrap"
                                          style={{
                                            backgroundColor:
                                              value.includes("Month") ||
                                              value.includes("year") ||
                                              value.includes("Free")
                                                ? "#ffedd5"
                                                : value === "Add Charges"
                                                  ? "#dbeafe"
                                                  : "#dcfce7",
                                            color:
                                              value.includes("Month") ||
                                              value.includes("year") ||
                                              value.includes("Free")
                                                ? "#ed6605"
                                                : value === "Add Charges"
                                                  ? "#005aa8"
                                                  : "#166534",
                                          }}
                                        >
                                          {value}
                                        </div>
                                      )
                                    ) : (
                                      <FaTimes className="w-5 h-5 text-gray-300" />
                                    )}
                                  </div>
                                </div>
                              );
                            },
                          )}

                          {/* Membership Fees - Clickable */}
                          <div
                            className="p-3 hover:bg-gray-50 rounded-lg border-b border-gray-100 cursor-pointer transition-all"
                            onClick={() => handleRedirect(plan.name)}
                          >
                            <div className="flex items-center justify-between">
                              <div className="flex items-center">
                                <FaRupeeSign className="text-[#005aa8] mr-3" />
                                <span className="font-medium text-gray-700">
                                  Membership Fees
                                </span>
                              </div>
                              <div className="flex flex-col items-end">
                                <div
                                  className="text-lg font-bold"
                                  style={{ color: "#005aa8" }}
                                >
                                  {plan.pricing.membershipFee}
                                </div>
                                <div className="text-xs text-gray-600 mt-1 flex items-center">
                                  <FaCalendarAlt className="mr-1" />
                                  {plan.pricing.duration}
                                </div>
                                {plan.pricing.registrationCharges && (
                                  <div className="text-xs font-medium px-2 py-1 rounded-full bg-orange-100 text-[#ed6605] flex items-center mt-1">
                                    <FaInfoCircle className="mr-1" />+
                                    {plan.pricing.registrationCharges}{" "}
                                    registration
                                  </div>
                                )}
                              </div>
                            </div>
                          </div>

                          {/* Duration - Clickable */}
                          <div
                            className="p-3 hover:bg-gray-50 rounded-lg cursor-pointer transition-all"
                            onClick={() => handleRedirect(plan.name)}
                          >
                            <div className="flex items-center justify-between">
                              <div className="flex items-center">
                                <FaCalendarAlt className="text-[#005aa8] mr-3" />
                                <span className="font-medium text-gray-700">
                                  Duration of Membership
                                </span>
                              </div>
                              <div
                                className="font-medium"
                                style={{ color: "#005aa8" }}
                              >
                                {plan.pricing.duration}
                              </div>
                            </div>
                          </div>
                        </div>

                        {/* Click Hint */}
                        <div className="text-center mt-4">
                          <p className="text-gray-500 text-sm">
                            Tap anywhere on this card to learn more about{" "}
                            {plan.name} plan
                          </p>
                        </div>
                      </div>
                    </motion.div>
                  );
                })()}
              </motion.div>
            </div>
          </div>

          {/* Navigation Dots */}
          <div className="flex justify-center mt-6">
            <div className="flex space-x-2">
              {membershipData.membershipPlans.map((_, index) => (
                <motion.button
                  key={index}
                  onClick={() => goToSlide(index)}
                  className={`w-3 h-3 rounded-full transition-all duration-300 ${
                    index === currentSlide
                      ? "bg-[#005aa8] scale-125"
                      : "bg-gray-300 hover:bg-gray-400"
                  }`}
                  aria-label={`Go to slide ${index + 1}`}
                  whileHover={{ scale: 1.2 }}
                  whileTap={{ scale: 0.9 }}
                />
              ))}
            </div>
          </div>
        </motion.div>
      </div>
    );
  });

  MobileSlider.displayName = "MobileSlider";

  return (
    <div className="min-h-screen bg-linear-to-br from-gray-50 to-gray-100 pt-40 py-8 px-4 md:px-8">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <motion.div
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="text-center mb-12"
        >
          <div className="inline-flex items-center justify-center mb-4">
            <h1 className="text-3xl md:text-4xl font-bold text-[#005aa8] mb-4">
              MASMA Membership
            </h1>
          </div>

          <p className="text-gray-600 mb-8 max-w-2xl mx-auto">
            Choose the membership plan that best fits your needs and take your
            business to the next level
          </p>
        </motion.div>

        {/* Mobile Slider View */}
        {isMobile ? (
          <MobileSlider />
        ) : (
          /* Desktop Table View */
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            className="bg-white rounded-2xl shadow-xl overflow-hidden"
          >
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="bg-linear-to-r from-[#005aa8] to-[#004080] text-white">
                    <th className="text-left p-6 font-bold text-lg">
                      <div className="flex items-center">
                        <FaInfoCircle className="mr-2" />
                        Benefits
                      </div>
                    </th>
                    {membershipData.membershipPlans.map((plan) => (
                      <th
                        key={plan.id}
                        className="p-6 text-center cursor-pointer hover:opacity-90 transition-opacity"
                        onClick={() => handleRedirect(plan.name)}
                      >
                        <div className="flex flex-col items-center">
                          <div className="flex items-center mb-2">
                            {getPlanIcon(plan.name)}
                            <span className="ml-2 font-bold">{plan.name}</span>
                          </div>
                          {plan.highlight && (
                            <div className="inline-flex items-center bg-[#ed6605] px-3 py-1 rounded-full text-xs">
                              <FaStar className="mr-1" />
                              Popular
                            </div>
                          )}
                        </div>
                      </th>
                    ))}
                  </tr>
                </thead>
                <tbody>
                  {membershipData.featureLabels.map(
                    (featureLabel, featureIndex) => (
                      <React.Fragment key={featureIndex}>
                        <tr
                          className={`hover:bg-gray-50 ${
                            expandedFeature === featureIndex ? "bg-gray-50" : ""
                          }`}
                        >
                          <td className="p-4 border-t border-gray-200">
                            <div
                              className="flex items-center cursor-pointer"
                              onClick={() =>
                                setExpandedFeature(
                                  expandedFeature === featureIndex
                                    ? null
                                    : featureIndex,
                                )
                              }
                            >
                              <div className="shrink-0 w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center mr-3">
                                {getFeatureIcon(featureIndex)}
                              </div>
                              <span className="font-medium text-gray-700">
                                {featureLabel}
                              </span>
                              <button className="ml-auto">
                                {expandedFeature === featureIndex ? (
                                  <FaChevronUp className="text-gray-400" />
                                ) : (
                                  <FaChevronDown className="text-gray-400" />
                                )}
                              </button>
                            </div>
                          </td>
                          {membershipData.membershipPlans.map(
                            (plan, planIndex) => {
                              const featureKey = Object.keys(plan.features)[
                                featureIndex
                              ];
                              const value = plan.features[featureKey];
                              return (
                                <td
                                  key={planIndex}
                                  className="p-4 border-t border-gray-200 text-center cursor-pointer hover:bg-blue-50 transition-colors"
                                  onClick={() => handleRedirect(plan.name)}
                                >
                                  <div className="flex justify-center">
                                    {value ? (
                                      <div className="flex flex-col items-center">
                                        <div className="mb-1">
                                          {value === "Yes" ? (
                                            <FaCheck className="w-5 h-5 text-green-500" />
                                          ) : (
                                            <div
                                              className="text-xs font-medium px-2 py-1 rounded-full"
                                              style={{
                                                backgroundColor:
                                                  value.includes("Month") ||
                                                  value.includes("year") ||
                                                  value.includes("Free")
                                                    ? "#ffedd5"
                                                    : value === "Add Charges"
                                                      ? "#dbeafe"
                                                      : "#dcfce7",
                                                color:
                                                  value.includes("Month") ||
                                                  value.includes("year") ||
                                                  value.includes("Free")
                                                    ? "#ed6605"
                                                    : value === "Add Charges"
                                                      ? "#005aa8"
                                                      : "#166534",
                                              }}
                                            >
                                              {value}
                                            </div>
                                          )}
                                        </div>
                                        {expandedFeature === featureIndex &&
                                          value !== "Yes" && (
                                            <div className="text-xs text-gray-500 mt-1 max-w-xs">
                                              {featureIndex === 0 &&
                                                "Personal meeting invitations"}
                                              {featureIndex === 1 &&
                                                "Online meeting access"}
                                              {featureIndex === 2 &&
                                                "Associate members group"}
                                              {featureIndex === 3 &&
                                                "Main MASMA group access"}
                                              {featureIndex === 4 &&
                                                "Website listing"}
                                              {featureIndex === 5 &&
                                                "Official groups advertisement"}
                                              {featureIndex === 6 &&
                                                "Webinar hosting capabilities"}
                                              {featureIndex === 7 &&
                                                "Website advertisement space"}
                                              {featureIndex === 8 &&
                                                "Event sponsorship discounts"}
                                              {featureIndex === 9 &&
                                                "Exhibition stall discounts"}
                                            </div>
                                          )}
                                      </div>
                                    ) : (
                                      <FaTimes className="w-5 h-5 text-gray-300" />
                                    )}
                                  </div>
                                </td>
                              );
                            },
                          )}
                        </tr>
                      </React.Fragment>
                    ),
                  )}

                  {/* Membership Fees Row */}
                  <tr>
                    <td className="p-6 border-t border-gray-300">
                      <div
                        className="flex items-center font-bold text-lg"
                        style={{ color: "#005aa8" }}
                      >
                        <FaRupeeSign className="mr-2" />
                        Membership Fees
                      </div>
                    </td>
                    {membershipData.membershipPlans.map((plan, index) => (
                      <td
                        key={index}
                        className="p-6 border-t border-gray-300 text-center cursor-pointer hover:bg-blue-50 transition-colors"
                        onClick={() => handleRedirect(plan.name)}
                      >
                        <div className="flex flex-col items-center">
                          <div
                            className="text-2xl font-bold mb-1"
                            style={{ color: "#005aa8" }}
                          >
                            {plan.pricing.membershipFee}
                          </div>
                          <div className="text-sm text-gray-600 mb-1 flex items-center">
                            <FaCalendarAlt className="mr-1" />
                            {plan.pricing.duration}
                          </div>
                          {plan.pricing.registrationCharges && (
                            <div className="text-xs font-medium px-2 py-1 rounded-full bg-orange-100 text-[#ed6605] flex items-center">
                              <FaInfoCircle className="mr-1" />+
                              {plan.pricing.registrationCharges} registration
                            </div>
                          )}
                        </div>
                      </td>
                    ))}
                  </tr>

                  {/* Duration Row */}
                  <tr>
                    <td className="p-6 border-t border-gray-200">
                      <div
                        className="flex items-center font-bold text-lg"
                        style={{ color: "#005aa8" }}
                      >
                        <FaCalendarAlt className="mr-2" />
                        Duration of Membership
                      </div>
                    </td>
                    {membershipData.membershipPlans.map((plan, index) => (
                      <td
                        key={index}
                        className="p-6 border-t border-gray-200 text-center cursor-pointer hover:bg-blue-50 transition-colors"
                        onClick={() => handleRedirect(plan.name)}
                      >
                        <div
                          className="font-medium"
                          style={{ color: "#005aa8" }}
                        >
                          {plan.pricing.duration}
                        </div>
                      </td>
                    ))}
                  </tr>
                </tbody>
              </table>
            </div>
          </motion.div>
        )}

        {/* Selected Plan Modal */}
        <AnimatePresence>
          {selectedPlan !== null && (
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
              onClick={() => setSelectedPlan(null)}
            >
              <motion.div
                initial={{ scale: 0.9, y: 20 }}
                animate={{ scale: 1, y: 0 }}
                exit={{ scale: 0.9, y: 20 }}
                className="bg-white rounded-2xl p-8 max-w-md w-full"
                onClick={(e) => e.stopPropagation()}
              >
                <div className="flex items-center mb-6">
                  <div className="shrink-0 w-12 h-12 rounded-full bg-linear-to-r from-[#005aa8] to-[#004080] flex items-center justify-center text-white mr-4">
                    {getPlanIcon(
                      membershipData.membershipPlans[selectedPlan].name,
                    )}
                  </div>
                  <div>
                    <h3
                      className="text-xl font-bold"
                      style={{ color: "#005aa8" }}
                    >
                      {membershipData.membershipPlans[selectedPlan].name}
                    </h3>
                    <p className="text-gray-600">Plan Selected</p>
                  </div>
                </div>

                <div className="space-y-4 mb-8">
                  <div className="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div className="flex items-center">
                      <FaRupeeSign className="mr-2 text-gray-600" />
                      <span className="font-medium">Membership Fee</span>
                    </div>
                    <span
                      className="text-2xl font-bold"
                      style={{ color: "#005aa8" }}
                    >
                      {
                        membershipData.membershipPlans[selectedPlan].pricing
                          .membershipFee
                      }
                    </span>
                  </div>

                  {membershipData.membershipPlans[selectedPlan].pricing
                    .registrationCharges && (
                    <div className="flex justify-between items-center p-4 bg-orange-50 rounded-lg">
                      <div className="flex items-center">
                        <FaFileContract
                          className="mr-2"
                          style={{ color: "#ed6605" }}
                        />
                        <span className="font-medium">
                          Registration Charges
                        </span>
                      </div>
                      <span
                        className="text-xl font-bold"
                        style={{ color: "#ed6605" }}
                      >
                        {
                          membershipData.membershipPlans[selectedPlan].pricing
                            .registrationCharges
                        }
                      </span>
                    </div>
                  )}

                  <div className="p-4 bg-blue-50 rounded-lg">
                    <div className="flex justify-between items-center mb-2">
                      <div className="flex items-center">
                        <FaCalendarAlt className="mr-2 text-blue-600" />
                        <span className="font-medium">Duration</span>
                      </div>
                      <span className="font-bold" style={{ color: "#005aa8" }}>
                        {
                          membershipData.membershipPlans[selectedPlan].pricing
                            .duration
                        }
                      </span>
                    </div>
                    <div className="text-sm text-gray-600">
                      Valid for Economic Year 2023-24
                    </div>
                  </div>
                </div>

                <div className="flex space-x-4">
                  <motion.button
                    whileHover={{ scale: 1.02 }}
                    whileTap={{ scale: 0.98 }}
                    onClick={() => setSelectedPlan(null)}
                    className="flex-1 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center"
                  >
                    <FaTimes className="mr-2" />
                    Cancel
                  </motion.button>
                  <motion.button
                    whileHover={{ scale: 1.02 }}
                    whileTap={{ scale: 0.98 }}
                    className="flex-1 py-3 font-bold rounded-lg text-white shadow-md flex items-center justify-center"
                    style={{
                      background:
                        "linear-gradient(135deg, #005aa8 0%, #ed6605 100%)",
                    }}
                  >
                    <FaRegCreditCard className="mr-2" />
                    Proceed to Payment
                  </motion.button>
                </div>
              </motion.div>
            </motion.div>
          )}
        </AnimatePresence>

        {/* Call to Action Button */}
        <div className="bg-linear-to-r from-gray-50 to-gray-100 p-3 sm:p-4 mt-8">
          <div className="flex justify-center items-center w-full">
            <motion.div
              initial={{ opacity: 0, y: -20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              className="flex justify-center items-center text-center w-full"
            >
              <motion.button
                whileHover={{ scale: 1.05 }}
                whileTap={{ scale: 0.95 }}
                onClick={handleLearnMore}
                className="
                  inline-flex items-center justify-center
                  px-3 py-4 sm:px-6 sm:py-3
                  rounded-lg font-medium text-white
                  shadow-md hover:shadow-lg transition-all
                  text-sm sm:text-base
                  w-50 sm:w-auto
                "
                style={{
                  background:
                    "linear-gradient(135deg, #005aa8 0%, #ed6605 100%)",
                }}
              >
                <FaExternalLinkAlt className="mr-2 text-xl sm:text-sm" />
                Be A Member
              </motion.button>
            </motion.div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default MembershipPlans;