import { motion } from "framer-motion";
import { useInView } from "react-intersection-observer";
import { useState, useEffect } from "react";
import axios from "../../services/api";

const Associates = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.1,
  });

  const [associateCompanies, setAssociateCompanies] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchAssociates = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/v1/associates');
        
        if (response.data.success) {
          setAssociateCompanies(response.data.data);
        } else {
          setError('Failed to load associates data');
        }
      } catch (err) {
        console.error('Error fetching associates:', err);
        setError('Could not connect to server');
      } finally {
        setLoading(false);
      }
    };

    fetchAssociates();
  }, []);

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

  if (associateCompanies.length === 0) {
    return (
      <section className="py-20 px-4 pt-40">
        <div className="container mx-auto max-w-7xl text-center text-gray-600">
          No associate companies available
        </div>
      </section>
    );
  }

  return (
    <section className="py-20 px-4 pt-40">
      <div className="container mx-auto max-w-7xl">
        {/* Main Header */}
        <motion.div
          className="text-center mb-16"
          initial={{ opacity: 0, y: 30 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.8 }}
        >
          <h1 className="text-4xl md:text-5xl font-bold text-[#005aa8] mb-4">
            Our Associate Companies
          </h1>
          <div className="w-24 h-1 bg-[#ed6605] rounded-full mx-auto mb-6"></div>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Partnering with leading companies in the solar industry to drive
            sustainable energy adoption across Maharashtra
          </p>
        </motion.div>

        <div ref={ref} className="space-y-8">
          {/* Associate Companies Grid */}
          <motion.div
            className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8"
            initial={{ opacity: 0, y: 50 }}
            animate={inView ? { opacity: 1, y: 0 } : {}}
            transition={{ duration: 0.8, delay: 0.2 }}
          >
            {associateCompanies.map((company, index) => (
              <motion.div
                key={company.id}
                className="bg-white rounded-xl border border-gray-200 overflow-hidden group transition-all duration-300"
                whileHover={{ scale: 1.05, y: -5 }}
                initial={{ opacity: 0, y: 30 }}
                animate={inView ? { opacity: 1, y: 0 } : {}}
                transition={{ delay: 0.3 + index * 0.1 }}
              >
                {/* Company Logo/Image */}
                <div className="h-48 overflow-hidden p-4 flex items-center justify-center">
                  <img
                    src={company.logo}
                    alt={company.name}
                    className="max-w-full max-h-full object-contain"
                  />
                </div>

                {/* Company Info */}
                <div className="p-6 text-center">
                  <h3 className="text-lg font-bold text-[#005aa8] mb-2">
                    {company.name}
                  </h3>
                  <p className="text-[#ed6605] font-medium text-sm mb-3">
                    {company.industry}
                  </p>
                  <p className="text-gray-600 text-sm leading-relaxed">
                    {company.description}
                  </p>
                </div>
              </motion.div>
            ))}
          </motion.div>
        </div>
      </div>
    </section>
  );
};

export default Associates;