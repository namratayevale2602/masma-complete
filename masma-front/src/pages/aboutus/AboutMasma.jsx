import { motion } from "framer-motion";
import { useInView } from "react-intersection-observer";
import { useState, useEffect } from "react";
import axios from "../../services/api";
import defaultImage from "../../assets/directors/amit-kulkarni.jpeg";

const AboutMasma = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.3,
  });

  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchAboutMasma = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/v1/about-masma');
        
        if (response.data.success && response.data.data) {
          setData(response.data.data);
        }
      } catch (error) {
        console.error('Error fetching about masma:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchAboutMasma();
  }, []);

  if (loading) {
    return (
      <section className="py-8 md:py-12 lg:py-16 bg-gray-100 pt-30 md:pt-32 lg:pt-40">
        <div className="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#005aa8] border-r-transparent"></div>
        </div>
      </section>
    );
  }

  if (!data) {
    return null;
  }

  return (
    <section className="py-8 md:py-12 lg:py-16 bg-gray-100 pt-30 md:pt-32 lg:pt-40">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-10 lg:gap-12 items-center">
          {/* Image Section */}
          <motion.div
            className="relative order-2 lg:order-1"
            initial={{ opacity: 0, x: -50 }}
            animate={inView ? { opacity: 1, x: 0 } : {}}
            transition={{ duration: 0.8 }}
          >
            <div className="relative h-[500px] w-full">
              <img
                src={data.president?.image || defaultImage}
                alt={data.president?.name || "MASMA President"}
                className="w-full h-full object-contain"
              />
              {/* President Badge */}
              <div className="absolute bottom-4 left-1/2 transform -translate-x-1/2 lg:bottom-6 lg:left-6 lg:transform-none bg-white/90 backdrop-blur-sm rounded-xl p-4 max-w-[280px] w-full shadow-lg">
                <div className="text-center lg:text-left">
                  <div className="text-lg sm:text-xl lg:text-2xl font-bold text-[#005aa8] leading-tight">
                    {data.president?.name || "Mr. Amit Kulkarni"}
                  </div>
                  <div className="text-sm sm:text-base lg:text-lg font-semibold text-[#ed6605] mt-1">
                    {data.president?.title || "President"}
                  </div>
                </div>
              </div>
            </div>
          </motion.div>

          {/* Content Section */}
          <motion.div
            ref={ref}
            className="space-y-4 md:space-y-6 order-1 lg:order-2"
            initial={{ opacity: 0, x: 50 }}
            animate={inView ? { opacity: 1, x: 0 } : {}}
            transition={{ duration: 0.8, delay: 0.2 }}
          >
            {/* Header */}
            <div className="text-center lg:text-left">
              <h2 className="text-2xl sm:text-3xl md:text-4xl font-bold text-[#005aa8] leading-tight">
                {data.title || "Welcome To Our Association"}
              </h2>
              <div className="w-16 h-1 bg-[#ed6605] rounded-full mx-auto lg:mx-0 mt-3 mb-4"></div>
            </div>

            {/* President's Message Section */}
            <div className="backdrop-blur-sm rounded-xl p-4 md:p-6">
              <h3 className="text-xl sm:text-2xl font-semibold text-[#ed6605] mb-3">
                President's Message
              </h3>
              <div className="space-y-3 md:space-y-4">
                {data.president?.message && (
                  <p className="text-gray-700 leading-relaxed text-justify text-sm sm:text-base">
                    {data.president.message}
                  </p>
                )}
                {data.president?.message_2 && (
                  <p className="text-gray-700 leading-relaxed text-justify text-sm sm:text-base">
                    {data.president.message_2}
                  </p>
                )}
                {data.president?.message_3 && (
                  <p className="text-gray-700 leading-relaxed text-justify text-sm sm:text-base">
                    {data.president.message_3}
                  </p>
                )}
              </div>
            </div>
          </motion.div>
        </div>

        {/* Stats Section */}
        {data.stats && data.stats.length > 0 && (
          <>
            {/* Desktop Stats */}
            <motion.div
              className="hidden lg:grid grid-cols-1 md:grid-cols-3 gap-6 mt-12"
              initial={{ opacity: 0, y: 30 }}
              animate={inView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.8, delay: 0.4 }}
            >
              {data.stats.map((stat, index) => (
                <div key={index} className="bg-white rounded-xl p-6 text-center border border-gray-200 shadow-sm">
                  <div className="text-2xl font-bold text-[#005aa8] mb-2">{stat.value}</div>
                  <div className="text-gray-600">{stat.label}</div>
                </div>
              ))}
            </motion.div>

            {/* Mobile Stats */}
            <motion.div
              className="lg:hidden grid grid-cols-3 gap-4 mt-8"
              initial={{ opacity: 0, y: 20 }}
              animate={inView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.6, delay: 0.4 }}
            >
              {data.stats.map((stat, index) => (
                <div key={index} className="bg-white rounded-lg p-4 text-center border border-gray-200 shadow-sm">
                  <div className="text-lg font-bold text-[#005aa8]">{stat.value}</div>
                  <div className="text-xs text-gray-600 mt-1">{stat.label.split(' ')[0]}</div>
                </div>
              ))}
            </motion.div>
          </>
        )}
      </div>
    </section>
  );
};

export default AboutMasma;