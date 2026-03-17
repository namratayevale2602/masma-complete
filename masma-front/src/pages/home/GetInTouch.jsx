import { motion } from "framer-motion";
import {
  FaCalendarAlt,
  FaMapMarkerAlt,
  FaUsers,
  FaArrowRight,
  FaUserPlus,
  FaEnvelope,
  FaPhone,
} from "react-icons/fa";
import { useNavigate } from "react-router-dom";
import { useState, useEffect } from "react";
import axios from "../../services/api";

const GetInTouch = () => {
  const navigate = useNavigate();
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchGetInTouch = async () => {
      try {
        const response = await axios.get('/v1/get-in-touch');
        if (response.data.success && response.data.data) {
          setData(response.data.data);
        }
      } catch (error) {
        console.error('Error fetching get in touch:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchGetInTouch();
  }, []);

  const handleBecomeMember = () => {
    navigate(data?.button?.link || "/bemember");
  };

  // Map icon strings to components
  const getIcon = (iconName) => {
    const icons = {
      FaUserPlus: FaUserPlus,
      FaArrowRight: FaArrowRight,
      FaCalendarAlt: FaCalendarAlt,
      FaMapMarkerAlt: FaMapMarkerAlt,
      FaUsers: FaUsers,
      FaEnvelope: FaEnvelope,
      FaPhone: FaPhone,
    };
    return icons[iconName] || FaUserPlus;
  };

  const ButtonIcon = data?.button?.icon ? getIcon(data.button.icon) : FaUserPlus;

  if (loading) {
    return (
      <section className="relative py-16 px-4 min-h-96 flex items-center bg-gray-100">
        <div className="container mx-auto max-w-4xl text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#005aa8] border-r-transparent"></div>
        </div>
      </section>
    );
  }

  return (
    <section
      className="relative py-16 px-4 min-h-96 flex items-center"
      style={{
        backgroundImage: `url(${data?.background_image})`,
        backgroundSize: "cover",
        backgroundPosition: "center",
      }}
    >
      <div className="container mx-auto max-w-4xl text-center text-white p-20">
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8 }}
          className="space-y-6"
        >
          <h1 className="text-4xl md:text-3xl font-bold">
            {data?.title}
          </h1>
          <h1 className="text-4xl md:text-6xl font-bold">
            {data?.main_title}
          </h1>

          <p className="text-xl text-white/90 max-w-2xl mx-auto">
            {data?.description}
          </p>

          <motion.button
            onClick={handleBecomeMember}
            className="inline-flex items-center space-x-3 px-8 py-4 bg-[#ed6605] text-white rounded-lg font-semibold text-lg hover:bg-[#d45a04] transition-colors shadow-lg"
            whileHover={{ scale: 1.05 }}
            whileTap={{ scale: 0.95 }}
          >
            <ButtonIcon />
            <span>{data?.button?.text || "Became A Member"}</span>
            <FaArrowRight />
          </motion.button>
        </motion.div>
      </div>
    </section>
  );
};

export default GetInTouch;