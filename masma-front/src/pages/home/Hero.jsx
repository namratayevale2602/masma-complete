import React, { useState, useEffect } from "react";
import axios from "../../services/api"; // Make sure this path is correct

const HeroSection = () => {
  const [currentSlide, setCurrentSlide] = useState(0);
  const [isAutoPlaying, setIsAutoPlaying] = useState(true);
  const [heroImages, setHeroImages] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Fetch hero images from backend
  useEffect(() => {
    const fetchHeroImages = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/v1/hero-images');
        
        if (response.data.success) {
          setHeroImages(response.data.data);
        } else {
          setError('Failed to load hero images');
        }
      } catch (err) {
        console.error('Error fetching hero images:', err);
        setError('Could not connect to server');
      } finally {
        setLoading(false);
      }
    };

    fetchHeroImages();
  }, []);

  // Auto-slide functionality
  useEffect(() => {
    if (!isAutoPlaying || heroImages.length === 0) return;

    const interval = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % heroImages.length);
    }, 5000);

    return () => clearInterval(interval);
  }, [isAutoPlaying, heroImages.length]);

  const nextSlide = () => {
    if (heroImages.length === 0) return;
    setCurrentSlide((prev) => (prev + 1) % heroImages.length);
  };

  const prevSlide = () => {
    if (heroImages.length === 0) return;
    setCurrentSlide(
      (prev) => (prev - 1 + heroImages.length) % heroImages.length,
    );
  };

  const goToSlide = (index) => {
    setCurrentSlide(index);
  };

  // Loading state
  if (loading) {
    return (
      <section className="relative h-[60vh] sm:h-[46vh] md:[46vh] lg:[46vh] xl:min-h-screen flex items-center justify-center overflow-hidden md:mt-20 mt-28 bg-gray-100">
        <div className="text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-blue-600 border-r-transparent"></div>
          <p className="mt-4 text-gray-600">Loading hero images...</p>
        </div>
      </section>
    );
  }

  // Error state or no images
  if (error || heroImages.length === 0) {
    return (
      <section className="relative h-[60vh] sm:h-[46vh] md:[46vh] lg:[46vh] xl:min-h-screen flex items-center justify-center overflow-hidden md:mt-20 mt-28 bg-gray-100">
        <div className="text-center text-gray-600">
          {error || 'No hero images available'}
        </div>
      </section>
    );
  }

  return (
    <section className="relative h-[60vh] sm:h-[46vh] md:[46vh] lg:[46vh] xl:min-h-screen flex items-center justify-center overflow-hidden md:mt-20 mt-28">
      {/* Image Carousel */}
      <div className="absolute inset-0 z-0">
        {heroImages.map((image, index) => (
          <div
            key={image.id}
            className={`absolute inset-0 transition-opacity duration-1000 ${
              index === currentSlide ? "opacity-100" : "opacity-0"
            }`}
          >
            {/* Desktop Image */}
            <img
              src={image.desktop}
              alt={image.alt_text || `Hero ${index + 1}`}
              className="hidden sm:block w-full h-full md:object-contain lg:object-cover xl:object-cover"
            />
            {/* Mobile Image */}
            <img
              src={image.mobile}
              alt={image.alt_text || `Hero ${index + 1}`}
              className="block md:hidden w-full h-full object-cover"
            />
            
            {/* Optional overlay with content if your API returns these fields */}
            {(image.title || image.description || image.button_text) && (
              <div className="absolute inset-0 bg-black/30 flex items-center justify-center">
                <div className="text-center text-white p-6 max-w-2xl">
                  {image.title && (
                    <h2 className="text-4xl md:text-5xl font-bold mb-4">{image.title}</h2>
                  )}
                  {image.description && (
                    <p className="text-lg md:text-xl mb-6">{image.description}</p>
                  )}
                  {image.button_text && image.button_link && (
                    <a
                      href={image.button_link}
                      className="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors"
                    >
                      {image.button_text}
                    </a>
                  )}
                </div>
              </div>
            )}
          </div>
        ))}
      </div>

      {/* Navigation Arrows */}
      <button
        onClick={prevSlide}
        className="absolute left-4 z-10 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-colors"
        aria-label="Previous slide"
      >
        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      
      <button
        onClick={nextSlide}
        className="absolute right-4 z-10 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-colors"
        aria-label="Next slide"
      >
        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
        </svg>
      </button>

      {/* Slide Indicators (Dots) */}
      <div className="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-10 flex space-x-2 lg:hidden">
        {heroImages.map((_, index) => (
          <button
            key={index}
            onClick={() => goToSlide(index)}
            className={`w-2 h-2 rounded-full transition-all ${
              index === currentSlide 
                ? "bg-white w-4" 
                : "bg-white/50 hover:bg-white/80"
            }`}
            aria-label={`Go to slide ${index + 1}`}
          />
        ))}
      </div>

      {/* Thumbnail Preview (Desktop only) */}
      <div className="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20 hidden lg:block">
        <div className="flex gap-3">
          {heroImages.map((image, index) => (
            <button
              key={image.id}
              onClick={() => goToSlide(index)}
              className={`relative w-20 h-14 rounded-lg overflow-hidden transition-all ${
                index === currentSlide
                  ? "ring-2 ring-white scale-110"
                  : "opacity-70 hover:opacity-100 hover:scale-105"
              }`}
            >
              <img
                src={image.desktop}
                alt={image.alt_text || `Thumbnail ${index + 1}`}
                className="w-full h-full object-cover"
              />
              {index === currentSlide && (
                <div className="absolute inset-0 bg-blue-500/30"></div>
              )}
            </button>
          ))}
        </div>
      </div>
    </section>
  );
};

export default HeroSection;