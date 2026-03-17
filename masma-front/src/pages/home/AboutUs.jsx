import { motion } from "framer-motion";
import { useInView } from "react-intersection-observer";
import { FaArrowRight } from "react-icons/fa";
import { useNavigate } from "react-router-dom";
import { useState, useEffect } from "react";
import axios from "../../services/api";
import defaultImage from "../../assets/masma/about.png";

const AboutUsCompact = () => {
  const [aboutData, setAboutData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.3,
  });

  const navigate = useNavigate();

  useEffect(() => {
    const fetchAboutUs = async () => {
      try {
        const response = await axios.get('/v1/about-us');
        if (response.data.success && response.data.data) {
          setAboutData(response.data.data);
        }
      } catch (error) {
        console.error('Error fetching about us:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchAboutUs();
  }, []);

  const handleAbout = () => {
    navigate(aboutData?.button?.link || "/about-us");
  };

  if (loading) {
    return (
      <section className="py-8 md:py-12 lg:py-16 bg-gray-100 overflow-x-hidden">
        <div className="container px-4 mx-auto max-w-7xl text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#005aa8] border-r-transparent"></div>
        </div>
      </section>
    );
  }

  return (
    <section className="py-8 md:py-12 lg:py-16 bg-gray-100 overflow-x-hidden">
      <div className="container px-4 mx-auto max-w-7xl">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-10 lg:gap-12 items-center">
          {/* Image Section */}
          <motion.div
            className="relative order-2 lg:order-1"
            initial={{ opacity: 0, x: -50 }}
            animate={inView ? { opacity: 1, x: 0 } : {}}
            transition={{ duration: 0.8 }}
          >
            <div className="relative">
              <img
                src={aboutData?.image || defaultImage}
                alt="MASMA Association"
                className="w-full h-[300px] sm:h-[350px] md:h-[400px] lg:h-[450px] xl:h-[500px] object-cover rounded-2xl"
              />
            </div>

            {/* Years Badge - Responsive positioning */}
            {aboutData?.badge && (
              <div className="absolute -bottom-4 left-1/2 transform -translate-x-1/2 lg:left-auto lg:transform-none lg:-bottom-6 lg:right-0 bg-white rounded-xl lg:rounded-2xl p-4 lg:p-6 w-[140px] sm:w-40 lg:w-auto">
                <div className="text-center">
                  <div className="text-2xl sm:text-3xl md:text-4xl font-bold text-[#005aa8]">
                    {aboutData.badge.number}
                  </div>
                  <div className="text-sm sm:text-base lg:text-lg font-semibold text-[#ed6605]">
                    {aboutData.badge.label}
                  </div>
                  <div className="text-xs sm:text-sm text-gray-500">
                    {aboutData.badge.subtext}
                  </div>
                </div>
              </div>
            )}
          </motion.div>

          {/* Content Section */}
          <motion.div
            ref={ref}
            className="space-y-4 md:space-y-6 order-1 lg:order-2 w-full"
            initial={{ opacity: 0, x: 50 }}
            animate={inView ? { opacity: 1, x: 0 } : {}}
            transition={{ duration: 0.8, delay: 0.2 }}
          >
            {/* Header */}
            <div className="text-center lg:text-left w-full">
              <h2 className="text-2xl sm:text-3xl md:text-4xl font-bold text-[#005aa8] leading-tight wrap-break-word">
                {aboutData?.title || "Welcome To Our Association"}
              </h2>
              <div className="w-16 h-1 bg-[#ed6605] rounded-full mx-auto lg:mx-0 mt-3 mb-4"></div>
            </div>

            {/* Description Section */}
            {aboutData?.description && (
              <div className="space-y-3 md:space-y-4 w-full">
                <div className="space-y-3 md:space-y-4 w-full">
                  {aboutData.description.split('\n').map((paragraph, index) => (
                    <p key={index} className="text-gray-700 leading-relaxed text-justify text-sm sm:text-base wrap-break-word">
                      {paragraph}
                    </p>
                  ))}
                </div>
              </div>
            )}

            {/* Call to Action Button */}
            <motion.div
              className="flex justify-center lg:justify-start pt-2 w-full"
              initial={{ opacity: 0, y: 20 }}
              animate={inView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.6, delay: 0.4 }}
            >
              <motion.button
                onClick={handleAbout}
                className="flex items-center space-x-2 px-6 py-3 bg-[#ed6605] text-white rounded-lg font-semibold hover:bg-[#d45a04] transition-colors text-sm sm:text-base w-full sm:w-auto"
                whileHover={{ scale: 1.05 }}
                whileTap={{ scale: 0.95 }}
              >
                <span>{aboutData?.button?.text || "Read More"}</span>
                <FaArrowRight className="text-xs sm:text-sm" />
              </motion.button>
            </motion.div>
          </motion.div>
        </div>
      </div>
    </section>
  );
};

export default AboutUsCompact;