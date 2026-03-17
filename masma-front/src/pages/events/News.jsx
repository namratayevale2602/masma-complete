import { motion, AnimatePresence } from "framer-motion";
import { useInView } from "react-intersection-observer";
import { useState } from "react";
import {
  FaCalendarAlt,
  FaUser,
  FaArrowRight,
  FaTimes,
  FaExpand,
  FaArrowLeft,
  FaArrowRight as FaRight,
  FaShare,
  FaBookmark,
} from "react-icons/fa";

const News = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.2,
  });

  const [selectedNews, setSelectedNews] = useState(null);
  const [fullScreenImage, setFullScreenImage] = useState(null);
  const [currentImageIndex, setCurrentImageIndex] = useState(0);

  const newsArticles = [
    {
      id: 1,
      title: "MASMA Partners with Government for Solar Subsidy Program",
      excerpt:
        "New initiative to make solar energy more accessible to residential consumers across Maharashtra with increased subsidy benefits.",
      content:
        "MASMA has signed a memorandum of understanding with the Maharashtra government to launch an enhanced solar subsidy program. This initiative aims to make solar energy more affordable for residential consumers, with subsidies of up to 40% for rooftop solar installations. The program will benefit over 50,000 households in the first phase.",
      author: "Solar Energy Desk",
      date: "December 1, 2024",
      category: "Policy Update",
      image:
        "https://images.unsplash.com/photo-1509391366360-2e959784a276?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1509391366360-2e959784a276?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
        "https://images.unsplash.com/photo-1466611653911-95081537e5b7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
        "https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
      ],
      tags: ["Solar Policy", "Subsidy", "Government", "Residential"],
    },
    {
      id: 2,
      title: "New Solar Technology Breakthrough Announced at MASMA Conference",
      excerpt:
        "Revolutionary solar panel technology increases efficiency by 25% while reducing manufacturing costs significantly.",
      content:
        "At the annual MASMA technology conference, researchers unveiled a groundbreaking solar panel technology that promises to revolutionize the industry. The new panels use perovskite-silicon tandem cells to achieve unprecedented efficiency levels while maintaining cost-effectiveness. Early tests show a 25% increase in energy generation compared to conventional panels.",
      author: "Tech Innovation Team",
      date: "November 25, 2024",
      category: "Technology",
      image:
        "https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
        "https://images.unsplash.com/photo-1562155618-e1a8bc2eb04f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
      ],
      tags: ["Innovation", "Technology", "Research", "Efficiency"],
    },
    {
      id: 3,
      title:
        "MASMA Training Program Creates 1000+ Solar Jobs in Rural Maharashtra",
      excerpt:
        "Skill development initiative successfully trains and places over 1000 individuals in solar installation and maintenance roles.",
      content:
        "MASMA's rural employment initiative has achieved remarkable success, creating over 1000 jobs in the solar energy sector across rural Maharashtra. The comprehensive training program equipped participants with skills in solar panel installation, maintenance, and system design. Many graduates have started their own solar installation businesses, contributing to local economic development.",
      author: "Employment Bureau",
      date: "November 18, 2024",
      category: "Employment",
      image:
        "https://images.unsplash.com/photo-1549399905-02da7514e9a3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1549399905-02da7514e9a3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
        "https://images.unsplash.com/photo-1575310279751-6864c08763db?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
      ],
      tags: ["Employment", "Training", "Rural Development", "Skills"],
    },
    {
      id: 4,
      title: "International Solar Expo Attracts Record Participation",
      excerpt:
        "MASMA-organized international solar exhibition sees participation from 50+ countries and 200+ exhibitors.",
      content:
        "The International Solar Expo organized by MASMA concluded with record-breaking participation. The event featured 200+ exhibitors from 50 countries, showcasing the latest innovations in solar technology. Key announcements included new partnerships between Indian and international solar companies, and the launch of several innovative solar products designed for the Indian market.",
      author: "Event Coverage Team",
      date: "November 12, 2024",
      category: "Events",
      image:
        "https://images.unsplash.com/photo-1564059813745-b2c0c42dcd19?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1564059813745-b2c0c42dcd19?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
        "https://images.unsplash.com/photo-1497366754035-f200968a6e72?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
      ],
      tags: ["Expo", "International", "Innovation", "Networking"],
    },
    {
      id: 5,
      title: "Solar Energy Capacity in Maharashtra Grows by 35%",
      excerpt:
        "Latest reports show significant growth in solar energy adoption across industrial and residential sectors in Maharashtra.",
      content:
        "Maharashtra has witnessed a 35% growth in solar energy capacity in the past year, according to the latest energy report. This growth is attributed to favorable government policies, decreasing solar panel costs, and increased awareness about renewable energy benefits. The industrial sector led the adoption, followed by residential and commercial installations.",
      author: "Energy Analytics Division",
      date: "November 5, 2024",
      category: "Industry Report",
      image:
        "https://images.unsplash.com/photo-1621372040061-6ce0a937763d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1621372040061-6ce0a937763d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
      ],
      tags: ["Growth", "Statistics", "Capacity", "Maharashtra"],
    },
    {
      id: 6,
      title: "MASMA Launches Solar Quality Certification Program",
      excerpt:
        "New certification program ensures quality standards for solar products and installation services across the state.",
      content:
        "MASMA has launched a comprehensive quality certification program for solar products and installation services. The program sets stringent quality standards and provides certification to manufacturers and installers who meet these standards. This initiative aims to build consumer confidence and ensure the long-term reliability of solar installations across Maharashtra.",
      author: "Quality Standards Committee",
      date: "October 28, 2024",
      category: "Quality Assurance",
      image:
        "https://images.unsplash.com/photo-1559027615-cd4628902d4a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1559027615-cd4628902d4a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
      ],
      tags: ["Quality", "Certification", "Standards", "Reliability"],
    },
  ];

  const openNewsDetail = (news) => {
    setSelectedNews(news);
    setCurrentImageIndex(0);
  };

  const closeNewsDetail = () => {
    setSelectedNews(null);
    setCurrentImageIndex(0);
  };

  const openFullScreenImage = (imageIndex = 0) => {
    setFullScreenImage({
      images: selectedNews.images,
      currentIndex: imageIndex,
    });
    setCurrentImageIndex(imageIndex);
  };

  const closeFullScreenImage = () => {
    setFullScreenImage(null);
  };

  const nextImage = () => {
    if (fullScreenImage) {
      const nextIndex = (currentImageIndex + 1) % fullScreenImage.images.length;
      setCurrentImageIndex(nextIndex);
    }
  };

  const prevImage = () => {
    if (fullScreenImage) {
      const prevIndex =
        (currentImageIndex - 1 + fullScreenImage.images.length) %
        fullScreenImage.images.length;
      setCurrentImageIndex(prevIndex);
    }
  };

  const NewsCard = ({ news }) => (
    <motion.div
      className="bg-white rounded-xl border border-gray-200 overflow-hidden group transition-all duration-300"
      whileHover={{ scale: 1.02, y: -5 }}
      initial={{ opacity: 0, y: 30 }}
      animate={inView ? { opacity: 1, y: 0 } : {}}
    >
      <div className="h-48 overflow-hidden relative">
        <img
          src={news.image}
          alt={news.title}
          className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
        />
        <div className="absolute top-4 left-4 px-3 py-1 bg-[#ed6605] rounded-full text-white text-sm font-semibold">
          {news.category}
        </div>
      </div>

      <div className="p-6">
        <div className="flex items-center space-x-4 text-sm text-gray-500 mb-3">
          <div className="flex items-center space-x-1">
            <FaCalendarAlt />
            <span>{news.date}</span>
          </div>
          <div className="flex items-center space-x-1">
            <FaUser />
            <span>{news.author}</span>
          </div>
        </div>

        <h3
          className="text-xl font-bold text-[#005aa8] mb-3 cursor-pointer hover:text-[#ed6605] transition-colors line-clamp-2"
          onClick={() => openNewsDetail(news)}
        >
          {news.title}
        </h3>

        <p className="text-gray-600 mb-4 line-clamp-3">{news.excerpt}</p>

        <div className="flex flex-wrap gap-2 mb-4">
          {news.tags.slice(0, 3).map((tag, index) => (
            <span
              key={index}
              className="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full"
            >
              #{tag}
            </span>
          ))}
        </div>

        <motion.button
          className="flex items-center space-x-2 text-[#005aa8] font-semibold hover:text-[#ed6605] transition-colors"
          whileHover={{ x: 5 }}
          onClick={() => openNewsDetail(news)}
        >
          <span>Read More</span>
          <FaArrowRight />
        </motion.button>
      </div>
    </motion.div>
  );

  return (
    <section className="py-20 pt-40 px-4 bg-gray-50" ref={ref}>
      <div className="container mx-auto max-w-7xl">
        {/* Main Header */}
        <motion.div
          className="text-center mb-16"
          initial={{ opacity: 0, y: 30 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.8 }}
        >
          <h1 className="text-4xl md:text-5xl font-bold text-[#005aa8] mb-4">
            Latest News & Updates
          </h1>
          <div className="w-24 h-1 bg-[#ed6605] rounded-full mx-auto mb-6"></div>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Stay updated with the latest developments in solar energy, policy
            changes, and MASMA initiatives
          </p>
        </motion.div>

        {/* News Grid */}
        <motion.div
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
          initial={{ opacity: 0, y: 50 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.8, delay: 0.2 }}
        >
          {newsArticles.map((news, index) => (
            <NewsCard key={news.id} news={news} />
          ))}
        </motion.div>

        {/* News Detail Popup */}
        <AnimatePresence>
          {selectedNews && (
            <motion.div
              className="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={closeNewsDetail}
            >
              <motion.div
                className="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
                initial={{ scale: 0.8, opacity: 0 }}
                animate={{ scale: 1, opacity: 1 }}
                exit={{ scale: 0.8, opacity: 0 }}
                onClick={(e) => e.stopPropagation()}
              >
                {/* Header */}
                <div className="p-6 border-b border-gray-200 flex justify-between items-start">
                  <div>
                    <span className="inline-block px-3 py-1 bg-[#ed6605] rounded-full text-white text-sm font-semibold mb-2">
                      {selectedNews.category}
                    </span>
                    <h3 className="text-2xl font-bold text-[#005aa8]">
                      {selectedNews.title}
                    </h3>
                  </div>
                  <button
                    onClick={closeNewsDetail}
                    className="p-2 hover:bg-gray-100 rounded-full transition-colors"
                  >
                    <FaTimes className="text-gray-500 text-xl" />
                  </button>
                </div>

                {/* Image Gallery */}
                <div className="relative">
                  <div className="h-80 bg-gray-100 relative">
                    <img
                      src={selectedNews.images[currentImageIndex]}
                      alt={selectedNews.title}
                      className="w-full h-full object-cover"
                    />

                    {/* Expand Button */}
                    <button
                      onClick={() => openFullScreenImage(currentImageIndex)}
                      className="absolute top-4 right-4 w-10 h-10 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors "
                    >
                      <FaExpand className="text-gray-700" />
                    </button>

                    {/* Navigation Arrows */}
                    {selectedNews.images.length > 1 && (
                      <>
                        <button
                          onClick={() =>
                            setCurrentImageIndex(
                              (prev) =>
                                (prev - 1 + selectedNews.images.length) %
                                selectedNews.images.length
                            )
                          }
                          className="absolute left-4 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors"
                        >
                          <FaArrowLeft className="text-gray-700" />
                        </button>
                        <button
                          onClick={() =>
                            setCurrentImageIndex(
                              (prev) => (prev + 1) % selectedNews.images.length
                            )
                          }
                          className="absolute right-4 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors"
                        >
                          <FaRight className="text-gray-700" />
                        </button>
                      </>
                    )}

                    {/* Image Indicators */}
                    {selectedNews.images.length > 1 && (
                      <div className="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                        {selectedNews.images.map((_, index) => (
                          <button
                            key={index}
                            onClick={() => setCurrentImageIndex(index)}
                            className={`w-3 h-3 rounded-full transition-all ${
                              index === currentImageIndex
                                ? "bg-[#ed6605]"
                                : "bg-white/70"
                            }`}
                          />
                        ))}
                      </div>
                    )}
                  </div>
                </div>

                {/* News Content */}
                <div className="p-6 space-y-6">
                  {/* Meta Information */}
                  <div className="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                    <div className="flex items-center space-x-2">
                      <FaCalendarAlt />
                      <span>{selectedNews.date}</span>
                    </div>
                    <div className="flex items-center space-x-2">
                      <FaUser />
                      <span>By {selectedNews.author}</span>
                    </div>
                  </div>

                  {/* Full Content */}
                  <div className="prose max-w-none">
                    <p className="text-gray-700 leading-relaxed text-lg">
                      {selectedNews.content}
                    </p>
                  </div>

                  {/* Tags */}
                  <div className="flex flex-wrap gap-2">
                    {selectedNews.tags.map((tag, index) => (
                      <span
                        key={index}
                        className="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full"
                      >
                        #{tag}
                      </span>
                    ))}
                  </div>

                  {/* Action Buttons */}
                  <div className="flex space-x-4 pt-4 border-t border-gray-200">
                    <motion.button
                      className="flex items-center space-x-2 px-6 py-3 bg-[#005aa8] text-white rounded-lg font-semibold hover:bg-[#004a8c] transition-colors"
                      whileHover={{ scale: 1.02 }}
                      whileTap={{ scale: 0.98 }}
                    >
                      <FaShare />
                      <span>Share</span>
                    </motion.button>
                    <motion.button
                      className="flex items-center space-x-2 px-6 py-3 border border-[#005aa8] text-[#005aa8] rounded-lg font-semibold hover:bg-[#005aa8] hover:text-white transition-colors"
                      whileHover={{ scale: 1.02 }}
                      whileTap={{ scale: 0.98 }}
                    >
                      <FaBookmark />
                      <span>Save</span>
                    </motion.button>
                  </div>
                </div>
              </motion.div>
            </motion.div>
          )}
        </AnimatePresence>

        {/* Full Screen Image Viewer */}
        <AnimatePresence>
          {fullScreenImage && (
            <motion.div
              className="fixed inset-0 bg-black z-60 flex items-center justify-center"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
            >
              {/* Close Button */}
              <button
                onClick={closeFullScreenImage}
                className="absolute top-4 right-4 z-10 w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition-colors"
              >
                <FaTimes className="text-white text-xl" />
              </button>

              {/* Navigation Arrows */}
              {fullScreenImage.images.length > 1 && (
                <>
                  <button
                    onClick={prevImage}
                    className="absolute left-4 top-1/2 transform -translate-y-1/2 z-10 w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition-colors"
                  >
                    <FaArrowLeft className="text-white text-xl" />
                  </button>
                  <button
                    onClick={nextImage}
                    className="absolute right-4 top-1/2 transform -translate-y-1/2 z-10 w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition-colors"
                  >
                    <FaRight className="text-white text-xl" />
                  </button>
                </>
              )}

              {/* Image */}
              <motion.img
                key={currentImageIndex}
                src={fullScreenImage.images[currentImageIndex]}
                alt="News image"
                className="max-w-full max-h-full object-contain"
                initial={{ scale: 0.8, opacity: 0 }}
                animate={{ scale: 1, opacity: 1 }}
                exit={{ scale: 0.8, opacity: 0 }}
                transition={{ duration: 0.3 }}
              />

              {/* Image Counter */}
              {fullScreenImage.images.length > 1 && (
                <div className="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-10 text-white bg-black/50 backdrop-blur-sm px-4 py-2 rounded-full">
                  {currentImageIndex + 1} / {fullScreenImage.images.length}
                </div>
              )}
            </motion.div>
          )}
        </AnimatePresence>
      </div>
    </section>
  );
};

export default News;
