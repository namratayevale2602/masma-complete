import { useState, useEffect, useRef } from "react";
import axios from "../../services/api";

const Participent = () => {
  const [isPaused, setIsPaused] = useState(false);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [participants, setParticipants] = useState({ row1: [], row2: [] });
  
  const scrollContainer1Ref = useRef(null);
  const scrollContainer2Ref = useRef(null);

  // Fetch participants from API
  useEffect(() => {
    const fetchParticipants = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/v1/participants');
        
        if (response.data.success) {
          setParticipants(response.data.data);
        } else {
          setError('Failed to load participants');
        }
      } catch (err) {
        console.error('Error fetching participants:', err);
        setError('Could not connect to server');
      } finally {
        setLoading(false);
      }
    };

    fetchParticipants();
  }, []);

  // Duplicate cards for seamless looping
  const duplicatedRow1Cards = participants.row1.length > 0 
    ? [...participants.row1, ...participants.row1, ...participants.row1]
    : [];
    
  const duplicatedRow2Cards = participants.row2.length > 0
    ? [...participants.row2, ...participants.row2, ...participants.row2]
    : [];

  useEffect(() => {
    if (isPaused || participants.row1.length === 0 || participants.row2.length === 0) return;

    const interval = setInterval(() => {
      if (scrollContainer1Ref.current && scrollContainer2Ref.current) {
        // First row - scroll left to right
        scrollContainer1Ref.current.scrollLeft += 1;

        // Reset scroll position when reaching the end for seamless loop
        if (
          scrollContainer1Ref.current.scrollLeft >=
          scrollContainer1Ref.current.scrollWidth / 3
        ) {
          scrollContainer1Ref.current.scrollLeft = 0;
        }

        // Second row - scroll right to left
        scrollContainer2Ref.current.scrollLeft -= 1;

        // Reset scroll position when reaching the start for seamless loop
        if (scrollContainer2Ref.current.scrollLeft <= 0) {
          scrollContainer2Ref.current.scrollLeft =
            scrollContainer2Ref.current.scrollWidth / 3;
        }
      }
    }, 20);

    return () => clearInterval(interval);
  }, [isPaused, participants]);

  // Loading state
  if (loading) {
    return (
      <section className="py-16 bg-linear-to-b from-gray-50 to-white">
        <div className="container mx-auto px-4 text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#005aa8] border-r-transparent"></div>
          <p className="mt-4 text-gray-600">Loading participants...</p>
        </div>
      </section>
    );
  }

  // Error state
  if (error) {
    return (
      <section className="py-16 bg-linear-to-b from-gray-50 to-white">
        <div className="container mx-auto px-4 text-center text-gray-600">
          {error}
        </div>
      </section>
    );
  }

  return (
    <section className="py-16 bg-linear-to-b from-gray-50 to-white">
      <div className="container mx-auto px-4">
        {/* Section Header */}
        <div className="text-center mb-12">
          <h2 className="text-3xl md:text-5xl font-bold text-[#005aa8] mb-4">
            MASMA EXPO PARTICIPANTS
          </h2>
          <div className="w-24 h-1 bg-[#ed6605] rounded-full mx-auto mb-6"></div>
          <p className="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto">
            Showcasing our annual solar energy exhibition, networking events,
            and industry collaborations
          </p>
        </div>

        {/* First Row - Left to Right Scroll */}
        {participants.row1.length > 0 && (
          <div className="mb-8">
            <div
              ref={scrollContainer1Ref}
              className="flex overflow-x-hidden space-x-6 py-6 scrollbar-hide"
              onMouseEnter={() => setIsPaused(true)}
              onMouseLeave={() => setIsPaused(false)}
              onTouchStart={() => setIsPaused(true)}
              onTouchEnd={() => setIsPaused(false)}
            >
              {duplicatedRow1Cards.map((card, index) => (
                <div
                  key={`row1-${card.id}-${index}`}
                  className="shrink-0 w-40 md:w-80 lg:w-60"
                >
                  <div className="overflow-hidden">
                    <div className="h-64 md:h-72 lg:h-100 overflow-hidden">
                      <img
                        src={card.image}
                        alt={card.alt_text || card.title || `Participant ${index + 1}`}
                        className="w-full h-full object-contain"
                      />
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* Second Row - Right to Left Scroll */}
        {participants.row2.length > 0 && (
          <div>
            <div
              ref={scrollContainer2Ref}
              className="flex overflow-x-hidden space-x-6 py-6 scrollbar-hide"
              onMouseEnter={() => setIsPaused(true)}
              onMouseLeave={() => setIsPaused(false)}
              onTouchStart={() => setIsPaused(true)}
              onTouchEnd={() => setIsPaused(false)}
            >
              {duplicatedRow2Cards.map((card, index) => (
                <div
                  key={`row2-${card.id}-${index}`}
                  className="shrink-0 w-72 md:w-80 lg:w-70"
                >
                  <div className="overflow-hidden">
                    <div className="h-56 md:h-64 overflow-hidden">
                      <img
                        src={card.image}
                        alt={card.alt_text || card.title || `Participant ${index + 1}`}
                        className="w-full h-full object-contain"
                      />
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}
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

export default Participent;