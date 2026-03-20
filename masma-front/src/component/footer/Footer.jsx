import React, { useState, useEffect } from "react";
import { motion } from "framer-motion";
import {
  FaSolarPanel,
  FaSun,
  FaFacebook,
  FaTwitter,
  FaInstagram,
  FaLinkedin,
  FaPhone,
  FaEnvelope,
  FaMapMarkerAlt,
  FaClock,
} from "react-icons/fa";
import { masmaLogo } from "../../assets";
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

const Footer = () => {
  const currentYear = new Date().getFullYear();
  const [contactData, setContactData] = useState(null);
  const [socialMedia, setSocialMedia] = useState([]);
  const [loading, setLoading] = useState(true);

  // Fetch data from APIs
  useEffect(() => {
    const fetchFooterData = async () => {
      try {
        // Fetch both endpoints in parallel
        const [contactResponse, socialResponse] = await Promise.all([
          axios.get('/v1/contact'),
          axios.get('/v1/social-media')
        ]);
        
        if (contactResponse.data.success) {
          setContactData(contactResponse.data.data);
        }
        
        if (socialResponse.data.success) {
          setSocialMedia(socialResponse.data.data);
        }
      } catch (error) {
        console.error('Error fetching footer data:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchFooterData();
  }, []);

  const getIcon = (iconName) => {
    return iconMap[iconName] || FaMapMarkerAlt;
  };

  // Extract contact information from API data
  const getPhoneNumber = () => {
    if (!contactData) return "+91 93091 67947";
    const phoneInfo = contactData.contact_info?.find(info => info.icon === "FaPhone");
    return phoneInfo?.details?.[0] || "+91 93091 67947";
  };

  const getEmail = () => {
    if (!contactData) return "info@masma.in";
    const emailInfo = contactData.contact_info?.find(info => info.icon === "FaEnvelope");
    return emailInfo?.details?.[0] || "info@masma.in";
  };

  const getAddress = () => {
    if (!contactData) return "";
    const addressInfo = contactData.contact_info?.find(info => info.icon === "FaMapMarkerAlt");
    return addressInfo?.details?.[0] || "";
  };

  const getWorkingHours = () => {
    if (!contactData) return [];
    const clockInfo = contactData.contact_info?.find(info => info.icon === "FaClock");
    return clockInfo?.details || [];
  };

  // Static sections that don't need API data
  const footerSections = [
    {
      title: "Quick Links",
      links: [
        { name: "Home", href: "/" },
        { name: "About Us", href: "/about-us" },
        { name: "FAQ", href: "/faq" },
        { name: "Gallery", href: "/gallery" },
        { name: "Contact", href: "/contact" },
      ],
    },
    {
      title: "Services",
      links: [
        { name: "Solar Installation", href: "#" },
        { name: "Maintenance", href: "#" },
        { name: "Consultation", href: "#" },
        { name: "Energy Audit", href: "#" },
      ],
    },
    {
      title: "Contact Info",
      links: [
        {
          name: getPhoneNumber(),
          href: `tel:${getPhoneNumber().replace(/\s+/g, '')}`,
          icon: <FaPhone />,
        },
        {
          name: getEmail(),
          href: `mailto:${getEmail()}`,
          icon: <FaEnvelope />,
        },
        {
          name: getAddress() || "THE MAHARASHTRA SOLAR MANUFACTURES ASSOCIATION D-93, 4th Floor,Office No.93, G-Wing, S.No. 19A/3B,Pune - Satara Rd, KK Market, Ahilya devi chowk Dhankawadi, Pune, Maharashtra 411043",
          href: "https://maps.app.goo.gl/dRhyDTBvTdTtRYmx8",
          icon: <FaMapMarkerAlt />,
        },
      ],
    },
  ];

  // Add working hours if available
  const workingHours = getWorkingHours();
  if (workingHours.length > 0) {
    footerSections.push({
      title: "Working Hours",
      links: workingHours.map((hours, index) => ({
        name: hours,
        href: "#",
        icon: <FaClock />,
      })),
    });
  }

  // Loading state
  if (loading) {
    return (
      <footer className="bg-gray-900 text-white md:rounded-tl-[12rem] rounded-tl-[10rem]">
        <div className="container mx-auto px-4 py-12 text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#005aa8] border-r-transparent"></div>
        </div>
      </footer>
    );
  }

  return (
    <footer className="bg-gray-900 text-white md:rounded-tl-[6rem] rounded-tl-[10rem]">
      <div className="container mx-auto px-4 py-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
          {/* Company Info */}
          <motion.div
            className="text-center md:text-left"
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
          >
            <div className="flex flex-col md:flex-row items-center space-x-0 md:space-x-2 mb-4">
              <img
                src={masmaLogo}
                alt="masma logo"
                className="h-25 w-25 object-cover"
              />
            </div>
            <p className="text-gray-300 mb-4 text-center md:text-left">
              {contactData?.page_description || "Leading the way in sustainable energy solutions. We provide top-quality solar installations and maintenance services for residential and commercial properties."}
            </p>
            <div className="flex justify-center md:justify-start space-x-4">
              {socialMedia.map((social, index) => {
                const IconComponent = getIcon(social.icon);
                // Don't render if URL is '#'
                if (social.url === '#') {
                  return (
                    <div
                      key={social.id}
                      className={`w-10 h-10 bg-[#005aa8] rounded-full flex items-center justify-center cursor-not-allowed opacity-50 ${social.color || ''}`}
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
                    className={`w-10 h-10 bg-[#005aa8] rounded-full flex items-center justify-center hover:bg-[#ed6605] transition-colors ${social.color || ''}`}
                    whileHover={{ scale: 1.1 }}
                    whileTap={{ scale: 0.9 }}
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    transition={{ delay: index * 0.1 }}
                    viewport={{ once: true }}
                    aria-label={social.platform}
                  >
                    <IconComponent />
                  </motion.a>
                );
              })}
            </div>
          </motion.div>

          {/* Footer Links */}
          {footerSections.map((section, sectionIndex) => (
            <motion.div
              key={section.title}
              className="text-center md:text-left"
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: sectionIndex * 0.1 }}
              viewport={{ once: true }}
            >
              <h3 className="text-lg font-semibold mb-4 text-[#ed6605]">
                {section.title}
              </h3>
              <ul className="space-y-2">
                {section.links.map((link, linkIndex) => (
                  <motion.li
                    key={link.name}
                    initial={{ opacity: 0, x: -20 }}
                    whileInView={{ opacity: 1, x: 0 }}
                    transition={{
                      delay: sectionIndex * 0.1 + linkIndex * 0.05,
                    }}
                    viewport={{ once: true }}
                  >
                    <a
                      href={link.href}
                      target={link.href.startsWith('http') ? "_blank" : undefined}
                      rel={link.href.startsWith('http') ? "noopener noreferrer" : undefined}
                      className="text-gray-300 hover:text-[#ed6605] transition-colors flex items-center justify-center md:justify-start space-x-2"
                    >
                      {link.icon && (
                        <span className="text-sm">{link.icon}</span>
                      )}
                      <span className={link.name.length > 50 ? "text-xs" : "text-sm"}>{link.name}</span>
                    </a>
                  </motion.li>
                ))}
              </ul>
            </motion.div>
          ))}
        </div>

        {/* Bottom Bar */}
        <motion.div
          className="border-t border-gray-700 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center text-center md:text-left"
          initial={{ opacity: 0 }}
          whileInView={{ opacity: 1 }}
          transition={{ duration: 0.6, delay: 0.3 }}
          viewport={{ once: true }}
        >
          <p className="text-gray-300 text-sm mb-4 md:mb-0">
            © {currentYear} Masma. All rights reserved.
          </p>
          <div className="flex flex-wrap justify-center gap-4 md:gap-6 text-sm">
            <a
              href="/privacy-policy"
              className="text-gray-300 hover:text-[#ed6605] transition-colors"
            >
              Privacy Policy
            </a>
            <a
              href="/terms-conditions"
              className="text-gray-300 hover:text-[#ed6605] transition-colors"
            >
              Terms of Service
            </a>
            <a
              href="/cookie-policy"
              className="text-gray-300 hover:text-[#ed6605] transition-colors"
            >
              Cookie Policy
            </a>
          </div>
        </motion.div>
      </div>
    </footer>
  );
};

export default Footer;