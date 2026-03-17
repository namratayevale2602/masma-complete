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
} from "react-icons/fa";
import { masmaLogo } from "../../assets";

const Footer = () => {
  const currentYear = new Date().getFullYear();

  const footerSections = [
    {
      title: "Quick Links",
      links: [
        { name: "Home", href: "/" },
        { name: "About Us", href: "/about-us" },
        { name: "FAQ", href: "/faq" },
        { name: "Gallary", href: "/gallary" },
        { name: "Contact", href: "/ContactUs" },
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
          name: "+91 93091 67947",
          href: "tel:+91 93091 67947",
          icon: <FaPhone />,
        },
        {
          name: "info@masma.in",
          href: "mailto:info@masma.in",
          icon: <FaEnvelope />,
        },
        {
          name: "THE MAHARASHTRA SOLAR MANUFACTURES ASSOCIATION D-93, 4th Floor,Office No.93, G-Wing, S.No. 19A/3B,Pune - Satara Rd, KK Market, Ahilya devi chowk Dhankawadi, Pune, Maharashtra 411043",
          href: "https://maps.app.goo.gl/dRhyDTBvTdTtRYmx8",
          icon: <FaMapMarkerAlt />,
        },
      ],
    },
  ];

  const socialLinks = [
    { icon: <FaFacebook />, href: "#", name: "Facebook" },
    { icon: <FaTwitter />, href: "#", name: "Twitter" },
    { icon: <FaInstagram />, href: "#", name: "Instagram" },
    { icon: <FaLinkedin />, href: "#", name: "LinkedIn" },
  ];

  return (
    <footer className="bg-gray-900 text-white md:rounded-tl-[12rem] rounded-tl-[10rem]">
      <div className="container mx-auto px-4 py-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 ">
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
              Leading the way in sustainable energy solutions. We provide
              top-quality solar installations and maintenance services for
              residential and commercial properties.
            </p>
            <div className="flex justify-center md:justify-start space-x-4">
              {socialLinks.map((social, index) => (
                <motion.a
                  key={social.name}
                  href={social.href}
                  target="_blank"
                  className="w-10 h-10 bg-[#005aa8] rounded-full flex items-center justify-center hover:bg-[#ed6605] transition-colors"
                  whileHover={{ scale: 1.1 }}
                  whileTap={{ scale: 0.9 }}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ delay: index * 0.1 }}
                  viewport={{ once: true }}
                >
                  {social.icon}
                </motion.a>
              ))}
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
                      className="text-gray-300 hover:text-[#ed6605] transition-colors flex items-center justify-center md:justify-start space-x-2"
                    >
                      {link.icon && (
                        <span className="text-sm">{link.icon}</span>
                      )}
                      <span>{link.name}</span>
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
              href="/term&condition"
              className="text-gray-300 hover:text-[#ed6605] transition-colors"
            >
              Terms of Service
            </a>
            <a
              href="#"
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
