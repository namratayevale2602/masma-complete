import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { useInView } from "react-intersection-observer";
import {
  FaChevronDown,
  FaChevronUp,
} from "react-icons/fa";
import axios from "../../services/api";

const FAQ = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.2,
  });

  const [faqItems, setFaqItems] = useState([]);
  const [categories, setCategories] = useState(['All']);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [openItems, setOpenItems] = useState([]);
  const [activeCategory, setActiveCategory] = useState("All");

  // Fetch FAQ data from API
  useEffect(() => {
    const fetchFaqs = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/v1/faqs', {
          params: { category: activeCategory }
        });
        
        if (response.data.success) {
          setFaqItems(response.data.data.faqs);
          setCategories(['All', ...response.data.data.categories]);
        } else {
          setError('Failed to load FAQ data');
        }
      } catch (err) {
        console.error('Error fetching FAQs:', err);
        setError('Could not connect to server');
      } finally {
        setLoading(false);
      }
    };

    fetchFaqs();
  }, [activeCategory]);

  const toggleItem = (index) => {
    setOpenItems((prev) =>
      prev.includes(index)
        ? prev.filter((item) => item !== index)
        : [...prev, index]
    );
  };

  const FAQItem = ({ item, index, isOpen }) => (
    <motion.div
      className="bg-white rounded-xl border border-gray-200 overflow-hidden mb-4"
      initial={{ opacity: 0, y: 20 }}
      animate={inView ? { opacity: 1, y: 0 } : {}}
      transition={{ duration: 0.5, delay: index * 0.05 }}
    >
      <button
        className="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-100 transition-colors"
        onClick={() => toggleItem(index)}
      >
        <div className="flex items-start space-x-4">
          <div className="w-8 h-8 bg-[#005aa8] rounded-full flex items-center justify-center text-white text-sm font-bold mt-1 shrink-0">
            {index + 1}
          </div>
          <div className="text-left">
            <h3 className="text-lg font-semibold text-gray-800 pr-4">
              {item.question}
            </h3>
            <span className="inline-block px-2 py-1 bg-[#ed6605] text-white text-xs rounded-full mt-2">
              {item.category}
            </span>
          </div>
        </div>
        <motion.div
          animate={{ rotate: isOpen ? 180 : 0 }}
          transition={{ duration: 0.3 }}
          className="shrink-0"
        >
          {isOpen ? (
            <FaChevronUp className="text-[#ed6605] text-lg" />
          ) : (
            <FaChevronDown className="text-[#005aa8] text-lg" />
          )}
        </motion.div>
      </button>

      <AnimatePresence>
        {isOpen && (
          <motion.div
            initial={{ height: 0, opacity: 0 }}
            animate={{ height: "auto", opacity: 1 }}
            exit={{ height: 0, opacity: 0 }}
            transition={{ duration: 0.3 }}
            className="overflow-hidden"
          >
            <div className="px-6 pb-4">
              <div className="border-t border-gray-200 pt-4">
                <div className="prose max-w-none text-gray-700 leading-relaxed whitespace-pre-line">
                  {item.answer}
                </div>
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </motion.div>
  );

  // Loading state
  if (loading) {
    return (
      <section className="py-20 pt-40 px-4" ref={ref}>
        <div className="container mx-auto max-w-4xl text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#005aa8] border-r-transparent"></div>
          <p className="mt-4 text-gray-600">Loading FAQs...</p>
        </div>
      </section>
    );
  }

  // Error state
  if (error) {
    return (
      <section className="py-20 pt-40 px-4" ref={ref}>
        <div className="container mx-auto max-w-4xl text-center text-red-500">
          {error}
        </div>
      </section>
    );
  }

  // Empty state
  if (faqItems.length === 0) {
    return (
      <section className="py-20 pt-40 px-4" ref={ref}>
        <div className="container mx-auto max-w-4xl text-center text-gray-600">
          No FAQs available
        </div>
      </section>
    );
  }

  return (
    <section className="py-20 pt-40 px-4" ref={ref}>
      <div className="container mx-auto max-w-4xl">
        <motion.div
          className="text-center mb-12"
          initial={{ opacity: 0, y: 30 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.8 }}
        >
          <h1 className="text-4xl md:text-5xl font-bold text-[#005aa8] mb-4">
            Solar FAQ
          </h1>
          <div className="w-24 h-1 bg-[#ed6605] rounded-full mx-auto mb-6"></div>
          <p className="text-gray-600">
            Find answers to common solar energy questions
          </p>
        </motion.div>

        {/* Category Tabs */}
        {categories.length > 1 && (
          <div className="flex flex-wrap gap-2 mb-8 justify-center">
            {categories.map((category) => (
              <button
                key={category}
                onClick={() => setActiveCategory(category)}
                className={`px-4 py-2 rounded-full font-semibold transition-colors ${
                  activeCategory === category
                    ? "bg-[#005aa8] text-white"
                    : "bg-white text-gray-600 hover:bg-gray-100"
                }`}
              >
                {category}
              </button>
            ))}
          </div>
        )}

        {/* FAQ Items */}
        <div className="space-y-4">
          {faqItems.map((item, index) => (
            <FAQItem
              key={item.id}
              item={item}
              index={index}
              isOpen={openItems.includes(index)}
            />
          ))}
        </div>

        {/* No results message */}
        {faqItems.length === 0 && activeCategory !== 'All' && (
          <div className="text-center py-8 text-gray-500">
            No FAQs found for category "{activeCategory}"
          </div>
        )}
      </div>
    </section>
  );
};

export default FAQ;