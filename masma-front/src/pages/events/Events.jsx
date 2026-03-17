import { motion, AnimatePresence } from "framer-motion";
import { useInView } from "react-intersection-observer";
import { useState } from "react";
import {
  FaCalendarAlt,
  FaMapMarkerAlt,
  FaUsers,
  FaArrowRight,
  FaArrowLeft,
  FaTimes,
  FaClock,
} from "react-icons/fa";

const Events = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.2,
  });

  const [selectedEvent, setSelectedEvent] = useState(null);
  const [currentImageIndex, setCurrentImageIndex] = useState(0);

  const futureEvents = [
    {
      id: 1,
      title: "Solar Energy Summit 2024",
      date: "December 15, 2024",
      time: "9:00 AM - 5:00 PM",
      venue: "Mumbai Convention Center, BKC",
      attendees: "500+ Industry Professionals",
      category: "Conference",
      image:
        "https://images.unsplash.com/photo-1497366754035-f200968a6e72?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      description:
        "Join industry leaders for the largest solar energy conference of the year. Network, learn, and explore the future of renewable energy.",
      images: [
        "https://images.unsplash.com/photo-1497366754035-f200968a6e72?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1509391366360-2e959784a276?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1466611653911-95081537e5b7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      ],
      agenda: [
        "Keynote: Future of Solar Energy in India",
        "Panel Discussion: Policy and Regulations",
        "Technology Showcase: Latest Innovations",
        "Networking Session with Industry Leaders",
      ],
    },
    {
      id: 2,
      title: "Solar Installation Workshop",
      date: "January 20, 2025",
      time: "10:00 AM - 4:00 PM",
      venue: "Pune Technical Institute",
      attendees: "100+ Installers & Technicians",
      category: "Workshop",
      image:
        "https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      description:
        "Hands-on training workshop for solar installers and technicians. Learn best practices and new installation techniques.",
      images: [
        "https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      ],
      agenda: [
        "Safety Protocols in Solar Installation",
        "Advanced Mounting Techniques",
        "Troubleshooting Common Issues",
        "Hands-on Practical Session",
      ],
    },
    {
      id: 3,
      title: "Renewable Energy Expo",
      date: "February 28, 2025",
      time: "10:00 AM - 6:00 PM",
      venue: "Delhi International Expo Center",
      attendees: "1000+ Visitors Expected",
      category: "Exhibition",
      image:
        "https://images.unsplash.com/photo-1562155618-e1a8bc2eb04f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      description:
        "Showcase of latest renewable energy technologies and innovations from leading companies across India.",
      images: [
        "https://images.unsplash.com/photo-1562155618-e1a8bc2eb04f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1564059813745-b2c0c42dcd19?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      ],
      agenda: [
        "Product Launches",
        "Technology Demonstrations",
        "Business Networking",
        "Industry Awards Ceremony",
      ],
    },
  ];

  const pastEvents = [
    {
      id: 4,
      title: "MASMA Annual Conference 2023",
      date: "November 10, 2023",
      time: "9:00 AM - 5:00 PM",
      venue: "Hyderabad Convention Center",
      attendees: "400+ Participants",
      category: "Conference",
      image:
        "https://images.unsplash.com/photo-1549399905-02da7514e9a3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      description:
        "Successful annual conference focusing on solar industry growth and government initiatives.",
      images: [
        "https://images.unsplash.com/photo-1549399905-02da7514e9a3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1575310279751-6864c08763db?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      ],
      agenda: [
        "Industry Growth Analysis",
        "Government Policy Updates",
        "Success Stories Sharing",
        "Networking Dinner",
      ],
    },
    {
      id: 5,
      title: "Solar Technology Workshop",
      date: "August 15, 2023",
      time: "10:00 AM - 4:00 PM",
      venue: "Bangalore Innovation Center",
      attendees: "150+ Technicians",
      category: "Workshop",
      image:
        "https://images.unsplash.com/photo-1621372040061-6ce0a937763d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      description:
        "Technical workshop focusing on advanced solar technologies and maintenance practices.",
      images: [
        "https://images.unsplash.com/photo-1621372040061-6ce0a937763d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      ],
      agenda: [
        "Advanced Panel Technologies",
        "Maintenance Best Practices",
        "Performance Optimization",
        "Case Studies",
      ],
    },
  ];

  const openEventPopup = (event) => {
    setSelectedEvent(event);
    setCurrentImageIndex(0);
  };

  const closeEventPopup = () => {
    setSelectedEvent(null);
    setCurrentImageIndex(0);
  };

  const nextImage = () => {
    if (selectedEvent) {
      setCurrentImageIndex((prev) => (prev + 1) % selectedEvent.images.length);
    }
  };

  const prevImage = () => {
    if (selectedEvent) {
      setCurrentImageIndex(
        (prev) =>
          (prev - 1 + selectedEvent.images.length) % selectedEvent.images.length
      );
    }
  };

  const EventCard = ({ event, isPast = false }) => (
    <motion.div
      className="bg-white rounded-xl border border-gray-200 overflow-hidden group transition-all duration-300"
      whileHover={{ scale: 1.02, y: -5 }}
      initial={{ opacity: 0, y: 30 }}
      animate={inView ? { opacity: 1, y: 0 } : {}}
    >
      <div className="h-48 overflow-hidden relative">
        <img
          src={event.image}
          alt={event.title}
          className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
        />
        <div
          className={`absolute top-4 left-4 px-3 py-1 rounded-full text-white text-sm font-semibold ${
            isPast ? "bg-gray-600" : "bg-[#ed6605]"
          }`}
        >
          {isPast ? "Past Event" : "Upcoming"}
        </div>
      </div>

      <div className="p-6">
        <h3
          className="text-xl font-bold text-[#005aa8] mb-3 cursor-pointer hover:text-[#ed6605] transition-colors"
          onClick={() => openEventPopup(event)}
        >
          {event.title}
        </h3>

        <div className="space-y-2 mb-4">
          <div className="flex items-center space-x-2 text-gray-600">
            <FaCalendarAlt className="text-[#ed6605]" />
            <span>{event.date}</span>
          </div>
          <div className="flex items-center space-x-2 text-gray-600">
            <FaClock className="text-[#ed6605]" />
            <span>{event.time}</span>
          </div>
          <div className="flex items-center space-x-2 text-gray-600">
            <FaMapMarkerAlt className="text-[#ed6605]" />
            <span>{event.venue}</span>
          </div>
          <div className="flex items-center space-x-2 text-gray-600">
            <FaUsers className="text-[#ed6605]" />
            <span>{event.attendees}</span>
          </div>
        </div>

        <p className="text-gray-600 text-sm mb-4 line-clamp-2">
          {event.description}
        </p>

        {!isPast && (
          <motion.button
            className="w-full py-3 bg-[#005aa8] text-white rounded-lg font-semibold hover:bg-[#004a8c] transition-colors"
            whileHover={{ scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
          >
            Register Now
          </motion.button>
        )}
      </div>
    </motion.div>
  );

  return (
    <section className="py-20 pt-40 px-4" ref={ref}>
      <div className="container mx-auto max-w-7xl">
        {/* Main Header */}
        <motion.div
          className="text-center mb-16"
          initial={{ opacity: 0, y: 30 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.8 }}
        >
          <h1 className="text-4xl md:text-5xl font-bold text-[#005aa8] mb-4">
            Our Events
          </h1>
          <div className="w-24 h-1 bg-[#ed6605] rounded-full mx-auto mb-6"></div>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Join us for industry-leading events, workshops, and conferences that
            drive solar energy adoption forward
          </p>
        </motion.div>

        {/* Future Events Section */}
        <motion.section
          className="mb-20"
          initial={{ opacity: 0, y: 50 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.8, delay: 0.2 }}
        >
          <div className="flex items-center justify-between mb-8">
            <h2 className="text-3xl font-bold text-[#005aa8]">
              Upcoming Events
            </h2>
            <div className="w-16 h-1 bg-[#ed6605] rounded-full"></div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {futureEvents.map((event, index) => (
              <EventCard key={event.id} event={event} />
            ))}
          </div>
        </motion.section>

        {/* Past Events Section */}
        <motion.section
          initial={{ opacity: 0, y: 50 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.8, delay: 0.4 }}
        >
          <div className="flex items-center justify-between mb-8">
            <h2 className="text-3xl font-bold text-[#005aa8]">Past Events</h2>
            <div className="w-16 h-1 bg-[#ed6605] rounded-full"></div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {pastEvents.map((event, index) => (
              <EventCard key={event.id} event={event} isPast={true} />
            ))}
          </div>
        </motion.section>

        {/* Event Popup Modal */}
        <AnimatePresence>
          {selectedEvent && (
            <motion.div
              className="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={closeEventPopup}
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
                    <h3 className="text-2xl font-bold text-[#005aa8]">
                      {selectedEvent.title}
                    </h3>
                    <p className="text-gray-600">{selectedEvent.category}</p>
                  </div>
                  <button
                    onClick={closeEventPopup}
                    className="p-2 hover:bg-gray-100 rounded-full transition-colors"
                  >
                    <FaTimes className="text-gray-500 text-xl" />
                  </button>
                </div>

                {/* Image Slider */}
                <div className="relative h-80 bg-gray-100">
                  <img
                    src={selectedEvent.images[currentImageIndex]}
                    alt={selectedEvent.title}
                    className="w-full h-full object-cover"
                  />

                  {/* Navigation Arrows */}
                  {selectedEvent.images.length > 1 && (
                    <>
                      <button
                        onClick={prevImage}
                        className="absolute left-4 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors"
                      >
                        <FaArrowLeft className="text-gray-700" />
                      </button>
                      <button
                        onClick={nextImage}
                        className="absolute right-4 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors"
                      >
                        <FaArrowRight className="text-gray-700" />
                      </button>
                    </>
                  )}

                  {/* Image Indicators */}
                  {selectedEvent.images.length > 1 && (
                    <div className="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                      {selectedEvent.images.map((_, index) => (
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

                {/* Event Details */}
                <div className="p-6 space-y-6">
                  {/* Basic Info */}
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="flex items-center space-x-3">
                      <FaCalendarAlt className="text-[#ed6605]" />
                      <div>
                        <p className="font-semibold">Date</p>
                        <p className="text-gray-600">{selectedEvent.date}</p>
                      </div>
                    </div>
                    <div className="flex items-center space-x-3">
                      <FaClock className="text-[#ed6605]" />
                      <div>
                        <p className="font-semibold">Time</p>
                        <p className="text-gray-600">{selectedEvent.time}</p>
                      </div>
                    </div>
                    <div className="flex items-center space-x-3">
                      <FaMapMarkerAlt className="text-[#ed6605]" />
                      <div>
                        <p className="font-semibold">Venue</p>
                        <p className="text-gray-600">{selectedEvent.venue}</p>
                      </div>
                    </div>
                    <div className="flex items-center space-x-3">
                      <FaUsers className="text-[#ed6605]" />
                      <div>
                        <p className="font-semibold">Attendees</p>
                        <p className="text-gray-600">
                          {selectedEvent.attendees}
                        </p>
                      </div>
                    </div>
                  </div>

                  {/* Description */}
                  <div>
                    <h4 className="text-lg font-semibold text-[#005aa8] mb-2">
                      About This Event
                    </h4>
                    <p className="text-gray-700 leading-relaxed">
                      {selectedEvent.description}
                    </p>
                  </div>

                  {/* Agenda */}
                  <div>
                    <h4 className="text-lg font-semibold text-[#005aa8] mb-3">
                      Event Agenda
                    </h4>
                    <ul className="space-y-2">
                      {selectedEvent.agenda.map((item, index) => (
                        <li key={index} className="flex items-center space-x-3">
                          <div className="w-2 h-2 bg-[#ed6605] rounded-full"></div>
                          <span className="text-gray-700">{item}</span>
                        </li>
                      ))}
                    </ul>
                  </div>

                  {/* Action Buttons */}
                  <div className="flex space-x-4 pt-4">
                    <motion.button
                      className="flex-1 py-3 bg-[#005aa8] text-white rounded-lg font-semibold hover:bg-[#004a8c] transition-colors"
                      whileHover={{ scale: 1.02 }}
                      whileTap={{ scale: 0.98 }}
                    >
                      Register for Event
                    </motion.button>
                    <motion.button
                      className="px-6 py-3 border border-[#005aa8] text-[#005aa8] rounded-lg font-semibold hover:bg-[#005aa8] hover:text-white transition-colors"
                      whileHover={{ scale: 1.02 }}
                      whileTap={{ scale: 0.98 }}
                    >
                      Download Brochure
                    </motion.button>
                  </div>
                </div>
              </motion.div>
            </motion.div>
          )}
        </AnimatePresence>
      </div>
    </section>
  );
};

export default Events;
