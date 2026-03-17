import { motion } from "framer-motion";
import { useInView } from "react-intersection-observer";
import { useState, useEffect } from "react";
import axios from "../../services/api";
import {
  FaUsers,
  FaHandshake,
  FaGraduationCap,
  FaBullhorn,
  FaCog,
  FaSun,
  FaChartLine,
  FaShieldAlt,
  FaBalanceScale,
  FaAward,
  FaRocket,
  FaBuilding,
  FaMoneyCheck,
  FaUserTie,
  FaClipboardCheck,
  FaEye,
  FaBullseye,
  FaFlag,
  FaGlobe,
  FaLightbulb,
} from "react-icons/fa";

// Icon mapping
const iconMap = {
  FaUsers: FaUsers,
  FaHandshake: FaHandshake,
  FaGraduationCap: FaGraduationCap,
  FaBullhorn: FaBullhorn,
  FaCog: FaCog,
  FaSun: FaSun,
  FaChartLine: FaChartLine,
  FaShieldAlt: FaShieldAlt,
  FaBalanceScale: FaBalanceScale,
  FaAward: FaAward,
  FaRocket: FaRocket,
  FaBuilding: FaBuilding,
  FaMoneyCheck: FaMoneyCheck,
  FaUserTie: FaUserTie,
  FaClipboardCheck: FaClipboardCheck,
  FaEye: FaEye,
  FaBullseye: FaBullseye,
  FaFlag: FaFlag,
  FaGlobe: FaGlobe,
  FaLightbulb: FaLightbulb,
};

const OurObjective = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.1,
  });

  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/v1/our-objective');
        
        if (response.data.success) {
          setData(response.data.data);
        }
      } catch (error) {
        console.error('Error fetching data:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  const getIcon = (iconName) => {
    return iconMap[iconName] || FaRocket;
  };

  if (loading) {
    return (
      <section className="py-20 px-4">
        <div className="container mx-auto max-w-8xl text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#005aa8] border-r-transparent"></div>
        </div>
      </section>
    );
  }

  if (!data) {
    return null;
  }

  return (
    <section className="py-20 px-4">
      <div className="container mx-auto max-w-8xl">
        {/* Main Header */}
        <motion.div
          className="text-center mb-16"
          initial={{ opacity: 0, y: 30 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.8 }}
        >
          <h1 className="text-4xl md:text-5xl font-bold text-[#005aa8] mb-4">
            {data.page_title}
          </h1>
          <div className="w-24 h-1 bg-[#ed6605] rounded-full mx-auto mb-6"></div>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            {data.page_subtitle}
          </p>
        </motion.div>

        <div ref={ref} className="space-y-16">
          {/* Vision, Mission & Goals Section */}
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Vision Card */}
            {data.vision && (
              <motion.div
                className="bg-gray-100 rounded-2xl p-8 border-t-4 border-[#005aa8]"
                initial={{ opacity: 0, y: 50 }}
                animate={inView ? { opacity: 1, y: 0 } : {}}
                transition={{ duration: 0.6, delay: 0.2 }}
              >
                <div className="text-center mb-6">
                  <div className="inline-flex items-center justify-center w-16 h-16 bg-[#ed6605] rounded-2xl text-white mb-4">
                    {getIcon(data.vision.icon)()}
                  </div>
                  <h2 className="text-2xl font-bold text-[#005aa8]">
                    {data.vision.title}
                  </h2>
                </div>
                <p className="text-gray-700 leading-relaxed mb-6">
                  {data.vision.description}
                </p>
                <div className="space-y-3">
                  {data.vision.highlights?.map((highlight, index) => (
                    <div key={index} className="flex items-start space-x-3">
                      <div className="shrink-0 w-2 h-2 bg-[#005aa8] rounded-full mt-2"></div>
                      <span className="text-gray-600 text-sm">{highlight}</span>
                    </div>
                  ))}
                </div>
              </motion.div>
            )}

            {/* Mission Card */}
            {data.mission && (
              <motion.div
                className="bg-gray-100 rounded-2xl p-8 border-t-4 border-[#005aa8]"
                initial={{ opacity: 0, y: 50 }}
                animate={inView ? { opacity: 1, y: 0 } : {}}
                transition={{ duration: 0.6, delay: 0.4 }}
              >
                <div className="text-center mb-6">
                  <div className="inline-flex items-center justify-center w-16 h-16 bg-[#ed6605] rounded-2xl text-white mb-4">
                    {getIcon(data.mission.icon)()}
                  </div>
                  <h2 className="text-2xl font-bold text-[#005aa8]">
                    {data.mission.title}
                  </h2>
                </div>
                <p className="text-gray-700 leading-relaxed mb-6">
                  {data.mission.description}
                </p>
                <div className="space-y-3">
                  {data.mission.points?.map((point, index) => (
                    <div key={index} className="flex items-center space-x-3">
                      <div className="shrink-0 w-2 h-2 bg-[#005aa8] rounded-full"></div>
                      <span className="text-gray-600 text-sm">{point.text}</span>
                    </div>
                  ))}
                </div>
              </motion.div>
            )}

            {/* Goals Card */}
            {data.goals && data.goals.length > 0 && (
              <motion.div
                className="bg-gray-100 rounded-2xl p-8 border-t-4 border-[#005aa8]"
                initial={{ opacity: 0, y: 50 }}
                animate={inView ? { opacity: 1, y: 0 } : {}}
                transition={{ duration: 0.6, delay: 0.6 }}
              >
                <div className="text-center mb-6">
                  <div className="inline-flex items-center justify-center w-16 h-16 bg-[#ed6605] rounded-2xl text-white mb-4">
                    {getIcon(data.goals[0]?.icon)()}
                  </div>
                  <h2 className="text-2xl font-bold text-[#005aa8]">
                    {data.goals[0]?.title}
                  </h2>
                </div>
                <p className="text-gray-700 leading-relaxed mb-6">
                  {data.goals[0]?.description}
                </p>
                <div className="space-y-4">
                  {data.goals.map((goal, index) => (
                    <div key={index}>
                      <div className="flex items-center space-x-2 mb-2">
                        <div className="shrink-0 w-2 h-2 bg-[#005aa8] rounded-full"></div>
                        <h4 className="text-sm text-gray-800">
                          {goal.categories?.[0]?.title}
                        </h4>
                      </div>
                    </div>
                  ))}
                </div>
              </motion.div>
            )}
          </div>

          {/* Section 1: MASMA Objectives */}
          {data.objectives && data.objectives.length > 0 && (
            <motion.section
              className="bg-white rounded-2xl p-8"
              initial={{ opacity: 0, y: 50 }}
              animate={inView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.8, delay: 0.2 }}
            >
              <div className="text-center mb-12">
                <div className="inline-flex items-center justify-center w-16 h-16 bg-[#ed6605] rounded-2xl text-white mb-4">
                  <FaRocket className="text-2xl" />
                </div>
                <h1 className="text-3xl md:text-5xl font-bold text-[#005aa8] mb-4">
                  Objectives
                </h1>
                <div className="w-24 h-1 bg-[#ed6605] rounded-full mx-auto mb-6"></div>
                <p className="text-gray-600 text-lg">
                  Uniting the solar industry and driving sustainable energy
                  adoption across Maharashtra
                </p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {data.objectives.map((objective, index) => {
                  const IconComponent = getIcon(objective.icon);
                  return (
                    <motion.div
                      key={objective.id}
                      className="bg-gray-100 rounded-xl p-6 border-l-4 border-[#ed6605] transition-all duration-300 group"
                    >
                      <div className="flex items-center space-x-4 mb-4">
                        <div className="shrink-0 w-12 h-12 bg-[#005aa8] rounded-lg flex items-center justify-center text-white">
                          <IconComponent />
                        </div>
                        <h3 className="text-lg font-semibold text-gray-800">
                          {objective.title}
                        </h3>
                      </div>
                      <p className="text-gray-600 leading-relaxed">
                        {objective.description}
                      </p>
                    </motion.div>
                  );
                })}
              </div>
            </motion.section>
          )}

          {/* Section 2: Director Responsibilities */}
          {data.director_responsibilities && data.director_responsibilities.length > 0 && (
            <motion.section className="bg-white rounded-2xl p-8">
              <div className="text-center mb-12">
                <div className="inline-flex items-center justify-center w-16 h-16 bg-[#ed6605] rounded-2xl text-white mb-4">
                  <FaUserTie className="text-2xl" />
                </div>
                <h1 className="text-3xl md:text-5xl font-bold text-[#005aa8] mb-4">
                  Duties and Responsibilities of Directors
                </h1>
                <div className="w-24 h-1 bg-[#ed6605] rounded-full mx-auto mb-6"></div>
                <p className="text-gray-600 text-lg">
                  Leadership commitments for driving MASMA's growth and member
                  services
                </p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {data.director_responsibilities.map((responsibility, index) => {
                  const IconComponent = getIcon(responsibility.icon);
                  return (
                    <motion.div
                      key={responsibility.id}
                      className="flex items-start space-x-4 p-4 bg-gray-100 rounded-lg border-l-4 border-[#ed6605]"
                      initial={{ opacity: 0, x: -20 }}
                      animate={inView ? { opacity: 1, x: 0 } : {}}
                      transition={{ delay: 0.5 + index * 0.05 }}
                    >
                      <div className="shrink-0 w-10 h-10 bg-[#005aa8] rounded-lg flex items-center justify-center text-white mt-1">
                        <IconComponent />
                      </div>
                      <p className="text-gray-700 font-medium">
                        {responsibility.task}
                      </p>
                    </motion.div>
                  );
                })}
              </div>
            </motion.section>
          )}

          {/* Section 3: Ethical Standards */}
          {data.ethical_standards && data.ethical_standards.length > 0 && (
            <motion.section
              className="bg-white rounded-2xl p-8"
              initial={{ opacity: 0, y: 50 }}
              animate={inView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.8, delay: 0.6 }}
            >
              <div className="text-center mb-12">
                <div className="inline-flex items-center justify-center w-16 h-16 bg-[#ed6605] rounded-2xl text-white mb-4">
                  <FaShieldAlt className="text-2xl" />
                </div>
                <h1 className="text-3xl md:text-5xl font-bold text-[#005aa8] mb-4">
                  Ethical Standards
                </h1>
                <div className="w-24 h-1 bg-[#ed6605] rounded-full mx-auto mb-6"></div>
                <p className="text-gray-600 text-lg">
                  Commitment to integrity, transparency, and quality in all our
                  endeavors
                </p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                {data.ethical_standards.map((standard, index) => {
                  const IconComponent = getIcon(standard.icon);
                  return (
                    <motion.div
                      key={standard.id}
                      className="text-center p-6 bg-gray-100 rounded-xl border-l-4 border-[#ed6605]"
                    >
                      <div className="inline-flex items-center justify-center w-10 h-10 bg-[#005aa8] rounded-2xl text-white mb-4">
                        <IconComponent />
                      </div>
                      <h3 className="text-xl font-semibold text-gray-800 mb-3">
                        {standard.title}
                      </h3>
                      <p className="text-gray-600 leading-relaxed">
                        {standard.description}
                      </p>
                    </motion.div>
                  );
                })}
              </div>
            </motion.section>
          )}
        </div>
      </div>
    </section>
  );
};

export default OurObjective;