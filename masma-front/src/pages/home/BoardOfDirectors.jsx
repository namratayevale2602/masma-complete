import { motion } from "framer-motion";
import { useInView } from "react-intersection-observer";
import { useState, useEffect, useRef } from "react";
import { FaChevronLeft, FaChevronRight } from "react-icons/fa";
import axios from "../../services/api";

const BoardOfDirectors = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.1,
  });

  const [currentSlide, setCurrentSlide] = useState(0);
  const [isMobile, setIsMobile] = useState(false);
  const [directors, setDirectors] = useState([]);
  const [loading, setLoading] = useState(true);
  const [year, setYear] = useState("2025-26");
  const sliderRef = useRef(null);

  // Fetch directors from API
  useEffect(() => {
    const fetchDirectors = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/v1/board-directors', {
          params: { year }
        });
        
        if (response.data.success) {
          setDirectors(response.data.data.directors);
        }
      } catch (error) {
        console.error('Error fetching directors:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchDirectors();
  }, [year]);

  // Check for mobile view
  useEffect(() => {
    const checkMobile = () => {
      setIsMobile(window.innerWidth < 768);
    };

    checkMobile();
    window.addEventListener("resize", checkMobile);

    return () => window.removeEventListener("resize", checkMobile);
  }, []);

  // Auto slide functionality
  useEffect(() => {
    if (!isMobile || directors.length === 0) return;

    const interval = setInterval(() => {
      setCurrentSlide((prev) => (prev === directors.length - 1 ? 0 : prev + 1));
    }, 5000);

    return () => clearInterval(interval);
  }, [isMobile, directors.length]);

  // Manual slide functions
  const nextSlide = () => {
    setCurrentSlide((prev) => (prev === directors.length - 1 ? 0 : prev + 1));
  };

  const prevSlide = () => {
    setCurrentSlide((prev) => (prev === 0 ? directors.length - 1 : prev - 1));
  };

  // Go to specific slide
  const goToSlide = (index) => {
    setCurrentSlide(index);
  };

  // Stagger animation for cards (desktop)
  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.1,
        delayChildren: 0.2,
      },
    },
  };

  const cardVariants = {
    hidden: { opacity: 0, y: 30 },
    visible: {
      opacity: 1,
      y: 0,
      transition: {
        duration: 0.6,
        ease: "easeOut",
      },
    },
  };

  // Card animation for slider
  const sliderCardVariants = {
    hidden: { opacity: 0, x: 100 },
    visible: {
      opacity: 1,
      x: 0,
      transition: {
        duration: 0.5,
        ease: "easeOut",
      },
    },
    exit: {
      opacity: 0,
      x: -100,
      transition: {
        duration: 0.5,
        ease: "easeIn",
      },
    },
  };

  if (loading) {
    return (
      <section className="py-12 md:py-16 lg:py-20 bg-gray-50 overflow-hidden">
        <div className="container px-4 mx-auto max-w-7xl text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#005aa8] border-r-transparent"></div>
        </div>
      </section>
    );
  }

  if (directors.length === 0) {
    return (
      <section className="py-12 md:py-16 lg:py-20 bg-gray-50 overflow-hidden">
        <div className="container px-4 mx-auto max-w-7xl text-center text-gray-600">
          No board directors found for {year}
        </div>
      </section>
    );
  }

  return (
    <section className="py-12 md:py-16 lg:py-20 bg-gray-50 overflow-hidden">
      <div className="container px-4 mx-auto max-w-7xl">
        {/* Header Section */}
        <motion.div
          className="text-center mb-10 md:mb-14 lg:mb-16"
          initial={{ opacity: 0, y: -20 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.6 }}
        >
          <h1 className="text-3xl md:text-4xl lg:text-5xl font-bold text-[#005aa8] mb-4">
            Board of Directors
          </h1>
          <div className="w-20 h-1 bg-[#ed6605] rounded-full mx-auto mb-4"></div>
          <p className="text-lg md:text-xl text-gray-600 font-semibold">
            ({year})
          </p>
          <p className="text-gray-600 mt-2 max-w-2xl mx-auto">
            Meet our dedicated team of professionals leading the solar energy
            revolution in Maharashtra
          </p>
        </motion.div>

        {/* Desktop Grid View (md and above) */}
        <div className="hidden md:block">
          <motion.div
            ref={ref}
            variants={containerVariants}
            initial="hidden"
            animate={inView ? "visible" : "hidden"}
            className="flex justify-center"
          >
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 gap-6 md:gap-8 justify-items-center">
              {directors.map((director, index) => (
                <motion.div
                  key={director.id}
                  variants={cardVariants}
                  whileHover={{
                    y: -10,
                    transition: { duration: 0.3 },
                  }}
                  className="bg-white rounded-xl overflow-hidden w-full max-w-sm"
                >
                  {/* Director Image */}
                  <div className="relative h-56 md:h-64 overflow-hidden">
                    <img
                      src={director.image}
                      alt={director.name}
                      className="w-full h-full object-contain"
                    />
                    {/* Designation Badge */}
                    <div className="absolute top-4 right-4 bg-[#ed6605] text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">
                      {director.designation}
                    </div>
                  </div>

                  {/* Director Info */}
                  <div className="p-5 md:p-6">
                    {/* Name and Place */}
                    <div className="mb-4">
                      <h3 className="text-xl font-bold text-gray-800 mb-1">
                        {director.name}
                      </h3>
                      <div className="flex items-center text-gray-600">
                        <svg
                          className="w-4 h-4 mr-1"
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path
                            fillRule="evenodd"
                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                            clipRule="evenodd"
                          />
                        </svg>
                        <span className="text-sm font-medium">
                          {director.place}
                        </span>
                      </div>
                    </div>

                    {/* Education */}
                    <div className="mb-4">
                      <div className="flex items-start mb-2">
                        <svg
                          className="w-5 h-5 text-[#005aa8] mr-2 mt-0.5 shrink-0"
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                        </svg>
                        <div>
                          <h4 className="text-sm font-semibold text-gray-700 mb-1">
                            Education
                          </h4>
                          <p className="text-sm text-gray-600 leading-relaxed">
                            {director.education}
                          </p>
                        </div>
                      </div>
                    </div>

                    {/* Experience */}
                    <div className="pt-4 border-t border-gray-100">
                      <div className="flex items-center">
                        <svg
                          className="w-5 h-5 text-[#ed6605] mr-2 shrink-0"
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path
                            fillRule="evenodd"
                            d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                            clipRule="evenodd"
                          />
                          <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                        </svg>
                        <div>
                          <h4 className="text-sm font-semibold text-gray-700 mb-1">
                            Experience
                          </h4>
                          <p className="text-sm text-gray-600">
                            {director.experience}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </motion.div>
              ))}
            </div>
          </motion.div>
        </div>

        {/* Mobile Slider View (below md) */}
        <div className="md:hidden relative">
          <div className="relative overflow-hidden rounded-xl">
            <motion.div
              key={currentSlide}
              variants={sliderCardVariants}
              initial="hidden"
              animate="visible"
              exit="exit"
              className="bg-white w-full rounded-xl overflow-hidden shadow-lg border border-gray-200"
            >
              {/* Director Image */}
              <div className="relative h-64 overflow-hidden">
                <img
                  src={directors[currentSlide]?.image }
                  alt={directors[currentSlide]?.name}
                  className="w-full h-full object-contain"
                />
                {/* Designation Badge */}
                <div className="absolute top-4 right-4 bg-[#ed6605] text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">
                  {directors[currentSlide]?.designation}
                </div>
              </div>

              {/* Director Info */}
              <div className="p-5">
                {/* Name and Place */}
                <div className="mb-4">
                  <h3 className="text-xl font-bold text-gray-800 mb-1">
                    {directors[currentSlide]?.name}
                  </h3>
                  <div className="flex items-center text-gray-600">
                    <svg
                      className="w-4 h-4 mr-1"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fillRule="evenodd"
                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                        clipRule="evenodd"
                      />
                    </svg>
                    <span className="text-sm font-medium">
                      {directors[currentSlide]?.place}
                    </span>
                  </div>
                </div>

                {/* Education */}
                <div className="mb-4">
                  <div className="flex items-start mb-2">
                    <svg
                      className="w-5 h-5 text-[#005aa8] mr-2 mt-0.5 shrink-0"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                    </svg>
                    <div>
                      <h4 className="text-sm font-semibold text-gray-700 mb-1">
                        Education
                      </h4>
                      <p className="text-sm text-gray-600 leading-relaxed">
                        {directors[currentSlide]?.education}
                      </p>
                    </div>
                  </div>
                </div>

                {/* Experience */}
                <div className="pt-4 border-t border-gray-100">
                  <div className="flex items-center">
                    <svg
                      className="w-5 h-5 text-[#ed6605] mr-2 shrink-0"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fillRule="evenodd"
                        d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                        clipRule="evenodd"
                      />
                      <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                    </svg>
                    <div>
                      <h4 className="text-sm font-semibold text-gray-700 mb-1">
                        Experience
                      </h4>
                      <p className="text-sm text-gray-600">
                        {directors[currentSlide]?.experience}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </motion.div>

            {/* Navigation Buttons */}
            <button
              onClick={prevSlide}
              className="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full p-2 shadow-lg hover:shadow-xl transition-all duration-300 z-10"
              aria-label="Previous slide"
            >
              <FaChevronLeft className="w-5 h-5" />
            </button>
            <button
              onClick={nextSlide}
              className="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full p-2 shadow-lg hover:shadow-xl transition-all duration-300 z-10"
              aria-label="Next slide"
            >
              <FaChevronRight className="w-5 h-5" />
            </button>
          </div>

          {/* Dots Indicator */}
          <div className="flex justify-center mt-6 space-x-2">
            {directors.map((_, index) => (
              <button
                key={index}
                onClick={() => goToSlide(index)}
                className={`w-2 h-2 rounded-full transition-all duration-300 ${
                  index === currentSlide
                    ? "bg-[#ed6605] w-6"
                    : "bg-gray-300 hover:bg-gray-400"
                }`}
                aria-label={`Go to slide ${index + 1}`}
              />
            ))}
          </div>

          {/* Slide Counter */}
          <div className="text-center mt-4 text-sm text-gray-600">
            <span className="font-semibold text-[#005aa8]">
              {currentSlide + 1}
            </span>
            <span className="mx-1">/</span>
            <span>{directors.length}</span>
          </div>
        </div>
      </div>
    </section>
  );
};

export default BoardOfDirectors;