import React, { useState, useEffect } from "react";
import { motion } from "framer-motion";
import { useInView } from "react-intersection-observer";
import {
  FaMapMarkerAlt,
  FaPhone,
  FaEnvelope,
  FaClock,
  FaFacebook,
  FaTwitter,
  FaLinkedin,
  FaInstagram,
  FaPaperPlane,
} from "react-icons/fa";
import axios from "../../services/api";

// Icon mapping
const iconMap = {
  FaMapMarkerAlt: FaMapMarkerAlt,
  FaPhone: FaPhone,
  FaEnvelope: FaEnvelope,
  FaClock: FaClock,
  FaFacebook: FaFacebook,
  FaTwitter: FaTwitter,
  FaLinkedin: FaLinkedin,
  FaInstagram: FaInstagram,
};

const ContactUs = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.1,
  });

  const [contactData, setContactData] = useState(null);
  const [socialMedia, setSocialMedia] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const [formData, setFormData] = useState({
    name: "",
    email: "",
    phone: "",
    subject: "",
    message: "",
  });

  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitStatus, setSubmitStatus] = useState(null);
  const [isChecked, setIsChecked] = useState(false);

  // Fetch contact data and social media separately
  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        
        // Fetch both endpoints in parallel for better performance
        const [contactResponse, socialResponse] = await Promise.all([
          axios.get('/v1/contact'),
          axios.get('/v1/social-media')
        ]);
        
        // Handle contact data
        if (contactResponse.data.success) {
          setContactData(contactResponse.data.data);
        } else {
          setError('Failed to load contact information');
        }
        
        // Handle social media data - this will override any social media from contact endpoint
        if (socialResponse.data.success) {
          setSocialMedia(socialResponse.data.data);
        }
        
      } catch (err) {
        console.error('Error fetching data:', err);
        setError('Could not connect to server');
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  const handleCheckboxChange = (event) => {
    setIsChecked(event.target.checked);
  };

  const handleInputChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setSubmitStatus(null);

    try {
      const response = await axios.post('/v1/contact/messages', {
        ...formData,
        consent: isChecked,
      });

      if (response.data.success) {
        setSubmitStatus({
          type: "success",
          message: response.data.message || "Thank you for your message! We'll get back to you soon.",
        });

        // Reset form
        setFormData({
          name: "",
          email: "",
          phone: "",
          subject: "",
          message: "",
        });
        setIsChecked(false);
      }
    } catch (err) {
      console.error('Error submitting form:', err);
      setSubmitStatus({
        type: "error",
        message: err.response?.data?.message || "Something went wrong. Please try again.",
      });
    } finally {
      setIsSubmitting(false);
    }
  };

  const getIcon = (iconName) => {
    return iconMap[iconName] || FaMapMarkerAlt;
  };

  const ContactCard = ({ contact, index }) => {
    const IconComponent = getIcon(contact.icon);
    
    return (
      <motion.div
        className="bg-white rounded-xl p-6 border border-gray-200 transition-all duration-300"
        whileHover={{ scale: 1.05, y: -5 }}
        initial={{ opacity: 0, y: 30 }}
        animate={inView ? { opacity: 1, y: 0 } : {}}
        transition={{ duration: 0.6, delay: index * 0.1 }}
      >
        <div className="inline-flex items-center justify-center w-14 h-14 bg-[#005aa8] rounded-2xl text-white mb-4">
          <IconComponent className="text-2xl" />
        </div>
        <h3 className="text-xl font-bold text-[#ed6605] mb-3">{contact.title}</h3>
        <div className="space-y-2">
          {contact.details.map((detail, idx) => (
            <p key={idx} className="text-gray-600">
              {detail}
            </p>
          ))}
        </div>
      </motion.div>
    );
  };

  // Loading state
  if (loading) {
    return (
      <section className="min-h-screen py-20 pt-40 px-4">
        <div className="container mx-auto max-w-7xl text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#005aa8] border-r-transparent"></div>
          <p className="mt-4 text-gray-600">Loading contact information...</p>
        </div>
      </section>
    );
  }

  // Error state
  if (error) {
    return (
      <section className="min-h-screen py-20 pt-40 px-4">
        <div className="container mx-auto max-w-7xl text-center text-red-500">
          {error}
        </div>
      </section>
    );
  }

  // Empty state
  if (!contactData) {
    return (
      <section className="min-h-screen py-20 pt-40 px-4">
        <div className="container mx-auto max-w-7xl text-center text-gray-600">
          Contact information not available
        </div>
      </section>
    );
  }

  return (
    <section className="min-h-screen py-20 pt-40 px-4">
      <div className="container mx-auto max-w-7xl">
        {/* Main Header */}
        <motion.div
          className="text-center mb-16"
          initial={{ opacity: 0, y: 30 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.8 }}
        >
          <h1 className="text-4xl md:text-5xl font-bold text-[#005aa8] mb-4">
            {contactData.page_title}
          </h1>
          <div className="w-24 h-1 bg-[#ed6605] rounded-full mx-auto mb-6"></div>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            {contactData.page_description}
          </p>
        </motion.div>

        <div ref={ref} className="space-y-16">
          {/* Contact Information Grid */}
          <motion.div
            className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
            initial={{ opacity: 0, y: 50 }}
            animate={inView ? { opacity: 1, y: 0 } : {}}
            transition={{ duration: 0.8, delay: 0.2 }}
          >
            {contactData.contact_info.map((contact, index) => (
              <ContactCard key={index} contact={contact} index={index} />
            ))}
          </motion.div>

          {/* Main Content Grid */}
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-12">
            {/* Contact Form */}
            <motion.div
              className="lg:col-span-2 bg-white rounded-2xl p-8"
              initial={{ opacity: 0, x: -50 }}
              animate={inView ? { opacity: 1, x: 0 } : {}}
              transition={{ duration: 0.8, delay: 0.4 }}
            >
              <div className="mb-8">
                <h2 className="text-3xl font-bold text-[#005aa8] mb-2">
                  {contactData.form.title}
                </h2>
                <p className="text-gray-600">
                  {contactData.form.description}
                </p>
                <div className="w-16 h-1 bg-[#ed6605] rounded-full mt-2"></div>
              </div>

              {/* Status Messages */}
              {submitStatus && (
                <motion.div
                  initial={{ opacity: 0, y: -10 }}
                  animate={{ opacity: 1, y: 0 }}
                  className={`p-4 rounded-lg mb-6 ${
                    submitStatus.type === "success"
                      ? "bg-green-100 text-green-700 border border-green-300"
                      : "bg-red-100 text-red-700 border border-red-300"
                  }`}
                >
                  {submitStatus.message}
                </motion.div>
              )}

              <form onSubmit={handleSubmit} className="space-y-6">
                {/* Name Field */}
                <div>
                  <label className="block text-sm font-semibold text-gray-700 mb-2">
                    Full Name <span className="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    name="name"
                    value={formData.name}
                    onChange={handleInputChange}
                    required
                    disabled={isSubmitting}
                    placeholder="Enter your full name"
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005aa8] focus:border-transparent transition-all"
                  />
                </div>

                {/* Email and Phone Row */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-sm font-semibold text-gray-700 mb-2">
                      Email Address <span className="text-red-500">*</span>
                    </label>
                    <input
                      type="email"
                      name="email"
                      value={formData.email}
                      onChange={handleInputChange}
                      required
                      disabled={isSubmitting}
                      placeholder="Enter your email"
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005aa8] focus:border-transparent transition-all"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-semibold text-gray-700 mb-2">
                      Phone Number
                    </label>
                    <input
                      type="tel"
                      name="phone"
                      value={formData.phone}
                      onChange={handleInputChange}
                      disabled={isSubmitting}
                      placeholder="Enter your phone number"
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005aa8] focus:border-transparent transition-all"
                    />
                  </div>
                </div>

                {/* Subject Field */}
                <div>
                  <label className="block text-sm font-semibold text-gray-700 mb-2">
                    Subject <span className="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    name="subject"
                    value={formData.subject}
                    onChange={handleInputChange}
                    required
                    disabled={isSubmitting}
                    placeholder="What is this regarding?"
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005aa8] focus:border-transparent transition-all"
                  />
                </div>

                {/* Message Field */}
                <div>
                  <label className="block text-sm font-semibold text-gray-700 mb-2">
                    Message <span className="text-red-500">*</span>
                  </label>
                  <textarea
                    name="message"
                    value={formData.message}
                    onChange={handleInputChange}
                    required
                    disabled={isSubmitting}
                    rows="6"
                    placeholder="Type your message here..."
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#005aa8] focus:border-transparent transition-all"
                  />
                </div>

                <div>
                  <label className="flex items-start">
                    <input
                      type="checkbox"
                      checked={isChecked}
                      onChange={handleCheckboxChange}
                      className="mr-2 mt-1"
                      required
                    />
                    <span className="text-sm text-gray-600">
                      By clicking this, you agree to disclose your personal
                      information to Masma for contacting you via SMS, Email, RCS
                      Messages, Calls and WhatsApp.
                    </span>
                  </label>
                </div>

                <motion.button
                  type="submit"
                  disabled={isSubmitting}
                  className="w-full flex items-center justify-center space-x-3 py-4 bg-[#ed6605] text-white rounded-lg font-semibold text-lg hover:bg-[#d45a04] transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                  whileHover={{
                    scale: isSubmitting ? 1 : 1.02,
                  }}
                  whileTap={{ scale: 0.98 }}
                >
                  {isSubmitting ? (
                    <>
                      <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                      <span>Sending...</span>
                    </>
                  ) : (
                    <>
                      <FaPaperPlane />
                      <span>Send Message</span>
                    </>
                  )}
                </motion.button>
              </form>
            </motion.div>

            {/* Sidebar */}
            <motion.div
              className="space-y-8"
              initial={{ opacity: 0, x: 50 }}
              animate={inView ? { opacity: 1, x: 0 } : {}}
              transition={{ duration: 0.8, delay: 0.6 }}
            >
              {/* Map Section */}
              {contactData.map_embed_url && (
                <div className="bg-white rounded-2xl p-6">
                  <h3 className="text-2xl font-bold text-[#005aa8] mb-4">
                    Our Location
                  </h3>
                  <div className="space-y-4">
                    <iframe
                      src={contactData.map_embed_url}
                      width="100%"
                      height="300"
                      style={{ border: 0, borderRadius: "8px" }}
                      loading="lazy"
                      referrerPolicy="no-referrer-when-downgrade"
                      title="MASMA Location"
                    ></iframe>
                    <p className="text-gray-600 text-sm">
                      Visit our office for consultations and meetings.
                    </p>
                  </div>
                </div>
              )}

              {/* Social Media - Now using data from the dedicated endpoint */}
              {socialMedia.length > 0 && (
                <div className="bg-[#005aa8] rounded-2xl p-6 text-white">
                  <h3 className="text-2xl font-bold mb-4">Connect With Us</h3>
                  <p className="text-blue-100 mb-4">
                    Follow us on social media for the latest updates on solar
                    energy initiatives.
                  </p>
                  <div className="flex space-x-4">
                    {socialMedia.map((social) => {
                      const IconComponent = getIcon(social.icon);
                      // Don't render if URL is '#'
                      if (social.url === '#') {
                        return (
                          <div
                            key={social.id}
                            className={`w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center cursor-not-allowed opacity-50 ${social.color || ''}`}
                            aria-label={social.platform}
                            title="Link not available"
                          >
                            <IconComponent />
                          </div>
                        );
                      }
                      return (
                        <motion.a
                          key={social.id}
                          href={social.url}
                          target="_blank"
                          rel="noopener noreferrer"
                          className={`w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center hover:bg-[#ed6605] transition-colors ${social.color || ''}`}
                          whileHover={{ scale: 1.1 }}
                          whileTap={{ scale: 0.9 }}
                          aria-label={social.platform}
                        >
                          <IconComponent />
                        </motion.a>
                      );
                    })}
                  </div>
                </div>
              )}
            </motion.div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default ContactUs;