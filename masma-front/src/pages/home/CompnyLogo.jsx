import { useState, useEffect, useRef } from "react";
import axios from "../../services/api";

const CompnyLogo = () => {
  const [isPaused, setIsPaused] = useState(false);
  const [logos, setLogos] = useState([]);
  const [loading, setLoading] = useState(true);
  const scrollContainer1Ref = useRef(null);

  // Fetch logos from API
  useEffect(() => {
    const fetchLogos = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/v1/company-logos');
        
        if (response.data.success) {
          setLogos(response.data.data);
        }
      } catch (error) {
        console.error('Error fetching company logos:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchLogos();
  }, []);

  // Duplicate cards for seamless looping
  const duplicatedCards = [...logos, ...logos, ...logos];

  useEffect(() => {
    if (isPaused || logos.length === 0) return;

    const interval = setInterval(() => {
      if (scrollContainer1Ref.current) {
        // Scroll left to right
        scrollContainer1Ref.current.scrollLeft += 1;

        // Reset scroll position when reaching the end for seamless loop
        if (
          scrollContainer1Ref.current.scrollLeft >=
          scrollContainer1Ref.current.scrollWidth / 3
        ) {
          scrollContainer1Ref.current.scrollLeft = 0;
        }
      }
    }, 20);

    return () => clearInterval(interval);
  }, [isPaused, logos.length]);

  if (loading) {
    return (
      <section className="py-16">
        <div className="container mx-auto text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#005aa8] border-r-transparent"></div>
        </div>
      </section>
    );
  }

  if (logos.length === 0) {
    return (
      <section className="py-16">
        <div className="container mx-auto text-center text-gray-600">
          No company logos found
        </div>
      </section>
    );
  }

  return (
    <section className="py-16">
      <div className="container mx-auto">
        {/* First Row - Left to Right Scroll */}
        <div>
          <div
            ref={scrollContainer1Ref}
            className="flex overflow-x-hidden space-x-6 scrollbar-hide"
            onMouseEnter={() => setIsPaused(true)}
            onMouseLeave={() => setIsPaused(false)}
          >
            {duplicatedCards.map((card, index) => (
              <div key={`row1-${card.id}-${index}`} className="shrink-0 w-80">
                <div className="bg-white rounded-2xl overflow-hidden">
                  <div className="h-30 overflow-hidden">
                    <img 
                      src={card.image} 
                      alt={card.title || 'Company logo'} 
                      className="w-full h-full object-contain"
                    />
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Custom CSS for hiding scrollbar */}
      <style jsx>{`
        .scrollbar-hide {
          -ms-overflow-style: none;
          scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
          display: none;
        }
      `}</style>
    </section>
  );
};

export default CompnyLogo;