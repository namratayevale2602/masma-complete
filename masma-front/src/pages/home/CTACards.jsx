import React, { useState, useEffect } from "react";
import { motion } from "framer-motion";
import { 
  FaIndustry, 
  FaUsers, 
  FaUserTie, 
  FaArrowRight,
  FaBuilding,
  FaAward,
  FaHandshake,
  FaStar 
} from "react-icons/fa";
import axios from "../../services/api";

// Icon mapping
const iconMap = {
  FaIndustry: FaIndustry,
  FaUsers: FaUsers,
  FaUserTie: FaUserTie,
  FaBuilding: FaBuilding,
  FaAward: FaAward,
  FaHandshake: FaHandshake,
  FaStar: FaStar,
};

const CTACards = () => {
  const [cards, setCards] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchCards = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/v1/cta-cards');
        
        if (response.data.success) {
          setCards(response.data.data);
        }
      } catch (error) {
        console.error('Error fetching CTA cards:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchCards();
  }, []);

  const handleRegister = (link) => {
    if (link.startsWith('http')) {
      window.open(link, "_blank", "noopener,noreferrer");
    } else {
      // For internal links, you might want to use navigate
      window.location.href = link;
    }
  };

  if (loading) {
    return (
      <section className="absolute -bottom-15 md:-bottom-24 lg:-bottom-28 xl:-bottom-5 left-0 right-0 z-30 px-4">
        <div className="container mx-auto max-w-7xl">
          <div className="grid grid-cols-3 sm:grid-cols-3 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
            {[1, 2, 3].map((i) => (
              <div key={i} className="bg-white rounded-xl md:rounded-2xl shadow-2xl p-8">
                <div className="animate-pulse">
                  <div className="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                  <div className="h-3 bg-gray-200 rounded w-full mb-2"></div>
                  <div className="h-3 bg-gray-200 rounded w-5/6 mb-6"></div>
                  <div className="h-10 bg-gray-200 rounded w-full"></div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>
    );
  }

  if (cards.length === 0) {
    return null; // Don't show anything if no cards
  }

  return (
    <section className="absolute -bottom-15 md:-bottom-24 lg:-bottom-28 xl:-bottom-5 left-0 right-0 z-30 px-4">
      <div className="container mx-auto max-w-7xl">
        <div className="grid grid-cols-3 sm:grid-cols-3 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
          {cards.map((card, index) => {
            // Get the icon component from the map, default to FaIndustry if not found
            const IconComponent = iconMap[card.icon] || FaIndustry;
            
            return (
              <motion.div
                key={card.id}
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: index * 0.1 }}
                whileHover={{
                  y: -8,
                  transition: { duration: 0.3 },
                }}
                className="bg-white rounded-xl md:rounded-2xl shadow-2xl hover:shadow-2xl transition-all duration-300 overflow-hidden"
                style={{ backgroundColor: card.color }}
              >
                {/* Card Header */}
                <div
                  className="p-2 md:p-4 sm:p-5 lg:p-8 text-white"
                  style={{ backgroundColor: card.color }}
                >
                  <div className="flex items-center gap-2 mb-2">
                    <IconComponent className="text-xl md:text-2xl" />
                    <h3 className="text-sm sm:text-xl md:text-2xl font-bold">
                      {card.title}
                    </h3>
                  </div>
                  
                  <p className="text-xs sm:text-sm md:text-base opacity-90 mb-3 md:mb-4">
                    {card.description}
                  </p>

                  {card.stats && (
                    <div className="mb-3 text-xs sm:text-sm font-semibold opacity-80">
                      {card.stats}
                    </div>
                  )}

                  <button
                    onClick={() => handleRegister(card.link)}
                    className="w-full py-1 md:py-3 lg:py-4 rounded-lg md:rounded-xl font-bold transition-all bg-white duration-300 hover:shadow-lg flex items-center justify-center gap-2 group text-sm md:text-base"
                    style={{ color: card.color }}
                  >
                    {card.button_text || 'Register'}
                    <FaArrowRight className="group-hover:translate-x-1 transition-transform" />
                  </button>
                </div>
              </motion.div>
            );
          })}
        </div>
      </div>
    </section>
  );
};

export default CTACards;