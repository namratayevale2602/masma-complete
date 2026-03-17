import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { useInView } from "react-intersection-observer";
import {
  FaTimes,
  FaExpand,
  FaArrowLeft,
  FaArrowRight as FaRight,
} from "react-icons/fa";
import axios from "../../services/api";

const Gallery = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.2,
  });

  const [galleryItems, setGalleryItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [selectedNews, setSelectedNews] = useState(null);
  const [fullScreenImage, setFullScreenImage] = useState(null);
  const [currentImageIndex, setCurrentImageIndex] = useState(0);

  // Fetch gallery data from API
  useEffect(() => {
    const fetchGallery = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/v1/gallery');
        
        if (response.data.success) {
          setGalleryItems(response.data.data);
        } else {
          setError('Failed to load gallery data');
        }
      } catch (err) {
        console.error('Error fetching gallery:', err);
        setError('Could not connect to server');
      } finally {
        setLoading(false);
      }
    };

    fetchGallery();
  }, []);

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
      className="bg-white rounded-xl border border-gray-200 overflow-hidden group transition-all duration-300 cursor-pointer"
      whileHover={{ scale: 1.02, y: -5 }}
      initial={{ opacity: 0, y: 30 }}
      animate={inView ? { opacity: 1, y: 0 } : {}}
      onClick={() => openNewsDetail(news)}
    >
      <div className="h-70 overflow-hidden relative">
        <img
          src={news.image}
          alt={news.title}
          className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
        />
      </div>

      <div className="p-3">
        <h3 className="text-xl font-bold text-[#005aa8] mb-3 hover:text-[#ed6605] transition-colors line-clamp-2">
          {news.title}
        </h3>
      </div>
    </motion.div>
  );

  // Loading state
  if (loading) {
    return (
      <section className="py-20 pt-40 px-4 bg-gray-50">
        <div className="container mx-auto max-w-7xl text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#005aa8] border-r-transparent"></div>
          <p className="mt-4 text-gray-600">Loading gallery...</p>
        </div>
      </section>
    );
  }

  // Error state
  if (error) {
    return (
      <section className="py-20 pt-40 px-4 bg-gray-50">
        <div className="container mx-auto max-w-7xl text-center text-red-500">
          {error}
        </div>
      </section>
    );
  }

  // Empty state
  if (galleryItems.length === 0) {
    return (
      <section className="py-20 pt-40 px-4 bg-gray-50">
        <div className="container mx-auto max-w-7xl text-center text-gray-600">
          No gallery items available
        </div>
      </section>
    );
  }

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
            Gallery
          </h1>
          <div className="w-24 h-1 bg-[#ed6605] rounded-full mx-auto mb-6"></div>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Stay updated with the latest developments in solar energy.
          </p>
        </motion.div>

        {/* News Grid */}
        <motion.div
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
          initial={{ opacity: 0, y: 50 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.8, delay: 0.2 }}
        >
          {galleryItems.map((item) => (
            <NewsCard key={item.id} news={item} />
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
                <div className="p-6 border-b border-gray-200 flex justify-between items-start sticky top-0 bg-white z-10">
                  <div>
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
                <div className="relative p-6">
                  <div className="h-96 bg-gray-100 rounded-lg relative">
                    <img
                      src={selectedNews.images[currentImageIndex]}
                      alt={selectedNews.title}
                      className="w-full h-full object-contain"
                    />

                    {/* Expand Button */}
                    <button
                      onClick={() => openFullScreenImage(currentImageIndex)}
                      className="absolute top-4 right-4 w-10 h-10 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors"
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
                                ? "bg-[#ed6605] w-6"
                                : "bg-white/70 hover:bg-white"
                            }`}
                          />
                        ))}
                      </div>
                    )}
                  </div>

                  {/* Thumbnail Strip */}
                  {selectedNews.images.length > 1 && (
                    <div className="flex gap-2 mt-4 overflow-x-auto pb-2">
                      {selectedNews.images.map((img, idx) => (
                        <button
                          key={idx}
                          onClick={() => setCurrentImageIndex(idx)}
                          className={`shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-all ${
                            idx === currentImageIndex
                              ? 'border-[#ed6605] opacity-100'
                              : 'border-transparent opacity-60 hover:opacity-100'
                          }`}
                        >
                          <img
                            src={img}
                            alt={`Thumbnail ${idx + 1}`}
                            className="w-full h-full object-cover"
                          />
                        </button>
                      ))}
                    </div>
                  )}
                </div>
              </motion.div>
            </motion.div>
          )}
        </AnimatePresence>

        {/* Full Screen Image Viewer */}
        <AnimatePresence>
          {fullScreenImage && (
            <motion.div
              className="fixed inset-0 bg-black z-[60] flex items-center justify-center"
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

export default Gallery;