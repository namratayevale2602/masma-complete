import { motion } from "framer-motion";
import { useInView } from "react-intersection-observer";
import { useState, useEffect } from "react";
import axios from "../../services/api";
import {
  FaUserTie,
  FaCrown,
} from "react-icons/fa";

// Icon mapping
const iconMap = {
  FaUserTie: FaUserTie,
  FaCrown: FaCrown,
};

const RegionalChapter = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.2,
  });

  const [directorCategories, setDirectorCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchDirectors = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/v1/regional-directors');
        
        if (response.data.success) {
          setDirectorCategories(response.data.data);
        } else {
          setError('Failed to load directors data');
        }
      } catch (err) {
        console.error('Error fetching directors:', err);
        setError('Could not connect to server');
      } finally {
        setLoading(false);
      }
    };

    fetchDirectors();
  }, []);

  const getIcon = (iconName) => {
    return iconMap[iconName] || FaUserTie;
  };

  if (loading) {
    return (
      <section className="py-20 px-4 pt-40">
        <div className="container mx-auto max-w-7xl text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#005aa8] border-r-transparent"></div>
        </div>
      </section>
    );
  }

  if (error) {
    return (
      <section className="py-20 px-4 pt-40">
        <div className="container mx-auto max-w-7xl text-center text-red-500">
          {error}
        </div>
      </section>
    );
  }

  if (directorCategories.length === 0) {
    return (
      <section className="py-20 px-4 pt-40">
        <div className="container mx-auto max-w-7xl text-center text-gray-600">
          No directors data available
        </div>
      </section>
    );
  }

  return (
    <section className="py-20 px-4 pt-40">
      <div className="container mx-auto max-w-7xl">
        {/* Main Header */}
        <motion.div className="text-center mb-2">
          <h1 className="text-4xl md:text-5xl font-bold text-[#005aa8] mb-4">
            Directors
          </h1>
          <div className="w-24 h-1 bg-[#ed6605] rounded-full mx-auto mb-6"></div>
        </motion.div>

        <div ref={ref} className="space-y-16">
          {directorCategories.map((category, categoryIndex) => {
            const IconComponent = getIcon(category.icon);
            
            return (
              <motion.section key={category.id} className="p-8">
                {/* Category Header */}
                <div className="flex items-center justify-center mb-12">
                  <div className="flex items-center space-x-4">
                    <IconComponent className="text-4xl text-[#ed6605]" />
                    <div>
                      <h2 className="text-3xl font-bold text-[#ed6605]">
                        {category.title}
                      </h2>
                    </div>
                  </div>
                </div>

                {/* Directors Row */}
                <div className="flex flex-wrap justify-center gap-8">
                  {category.members.map((member, memberIndex) => (
                    <motion.div
                      key={member.id}
                      className="w-64 bg-white rounded-xl border border-gray-200"
                      whileHover={{ y: -10 }}
                      transition={{ duration: 0.3 }}
                    >
                      {/* Member Image */}
                      <div className="h-80 overflow-hidden rounded-t-xl">
                        <img
                          src={member.image}
                          alt={member.name}
                          className="w-full h-full object-cover hover:scale-110 transition-transform duration-300"
                        />
                      </div>

                      {/* Member Info */}
                      <div className="p-6 text-center">
                        <h3 className="text-lg font-bold text-gray-800 mb-2">
                          {member.name}
                        </h3>
                        <p className="text-[#005aa8] font-medium text-sm">
                          {member.city}
                        </p>
                        <p className="text-[#ed6605] font-medium text-sm">
                          {member.position}
                        </p>
                      </div>
                    </motion.div>
                  ))}
                </div>
              </motion.section>
            );
          })}
        </div>
      </div>
    </section>
  );
};

export default RegionalChapter;