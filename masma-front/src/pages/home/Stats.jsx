import { motion } from "framer-motion";
import { useInView } from "react-intersection-observer";
import { useState, useEffect } from "react";
import { 
  FaUserTie, 
  FaUsers, 
  FaUserCheck,
  FaBuilding,
  FaGlobe,
  FaAward,
  FaChartLine 
} from "react-icons/fa";
import axios from "../../services/api";

// Icon mapping
const iconMap = {
  FaUserTie: FaUserTie,
  FaUsers: FaUsers,
  FaUserCheck: FaUserCheck,
  FaBuilding: FaBuilding,
  FaGlobe: FaGlobe,
  FaAward: FaAward,
  FaChartLine: FaChartLine,
};

const Stats = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.3,
  });

  const [stats, setStats] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/v1/stats');
        
        if (response.data.success) {
          setStats(response.data.data);
        }
      } catch (error) {
        console.error('Error fetching stats:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchStats();
  }, []);

  if (loading) {
    return (
      <section className="bg-gray-100 py-5 px-4">
        <div className="container mx-auto text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#005aa8] border-r-transparent"></div>
        </div>
      </section>
    );
  }

  if (stats.length === 0) {
    return (
      <section className="bg-gray-100 py-5 px-4">
        <div className="container mx-auto text-center text-gray-600">
          No statistics found
        </div>
      </section>
    );
  }

  return (
    <section className="bg-gray-100 py-5 px-4">
      <div className="container mx-auto">
        <div
          ref={ref}
          className="flex flex-col md:flex-row items-center justify-between gap-3 md:gap-50 max-w-6xl mx-auto"
        >
          {stats.map((stat, index) => (
            <StatItemWithIcon
              key={stat.id}
              stat={stat}
              index={index}
              inView={inView}
            />
          ))}
        </div>
      </div>
    </section>
  );
};

const StatItemWithIcon = ({ stat, index, inView }) => {
  const [count, setCount] = useState(0);

  // Get the icon component from the map, default to FaUserTie if not found
  const IconComponent = iconMap[stat.icon] || FaUserTie;

  useEffect(() => {
    if (inView) {
      const duration = 2000;
      const steps = 60;
      const increment = stat.value / steps;
      let current = 0;

      const timer = setInterval(() => {
        current += increment;
        if (current > stat.value) {
          setCount(stat.value);
          clearInterval(timer);
        } else {
          setCount(Math.floor(current));
        }
      }, duration / steps);

      return () => clearInterval(timer);
    }
  }, [inView, stat.value]);

  const formattedCount = count.toLocaleString();

  return (
    <motion.div
      className="flex-1 text-center group"
      initial={{ opacity: 0, y: 50 }}
      animate={inView ? { opacity: 1, y: 0 } : {}}
      transition={{ duration: 0.8, delay: index * 0.3 }}
    >
      {/* Icon Container */}
      <motion.div className="inline-flex items-center justify-center w-15 h-15 rounded-2xl bg-white">
        <div className="text-2xl text-[#005aa8] transition-colors duration-300">
          <IconComponent />
        </div>
      </motion.div>

      {/* Main Number */}
      <motion.div
        className="text-2xl md:text-5xl lg:text-5xl font-black tracking-tight"
        initial={{ scale: 0.5, opacity: 0 }}
        animate={inView ? { scale: 1, opacity: 1 } : {}}
        transition={{
          duration: 1,
          delay: index * 0.3 + 0.4,
          type: "spring",
          stiffness: 100,
        }}
      >
        <span className="bg-[#ed6605] bg-clip-text text-transparent">
          {formattedCount}
        </span>
      </motion.div>

      {/* Label */}
      <motion.h3
        className="md:text-1xl font-semibold text-gray-800 tracking-wide uppercase mb-2"
        initial={{ opacity: 0, y: 20 }}
        animate={inView ? { opacity: 1, y: 0 } : {}}
        transition={{ duration: 0.6, delay: index * 0.3 + 0.6 }}
      >
        {stat.label}
      </motion.h3>
    </motion.div>
  );
};

export default Stats;