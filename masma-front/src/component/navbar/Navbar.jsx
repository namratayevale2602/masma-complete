import { useState, useEffect, useRef } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { HiMenuAlt3, HiX, HiChevronDown } from "react-icons/hi";
import {
  FaSolarPanel,
  FaSun,
  FaHome,
  FaInfoCircle,
  FaEnvelope,
  FaCog,
  FaTools,
  FaShieldAlt,
  FaAddressBook,
  FaImage,
  FaBook,
} from "react-icons/fa";
import masmaLogo from "../../assets/logo/masma.png";

const Navbar = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const [activeDropdown, setActiveDropdown] = useState(null);
  const dropdownRef = useRef(null);

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 50);
    };
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  // Close dropdown when clicking outside
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
        setActiveDropdown(null);
      }
    };

    document.addEventListener("mousedown", handleClickOutside);
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, []);

  const navItems = [
    { name: "Home", icon: <FaHome />, href: "/" },
    {
      name: "About Us",
      icon: <FaAddressBook />,
      href: "#aboutus",
      dropdown: [
        { name: "About Masma", icon: <FaCog />, href: "/about-us" },
        { name: "Apex Body", icon: <FaTools />, href: "/apex-body" },
        {
          name: "Regional Chapter",
          icon: <FaInfoCircle />,
          href: "/ReligionalChap",
        },
        { name: "Associates", icon: <FaShieldAlt />, href: "/associates" },
      ],
    },
    {
      name: "Members",
      icon: <FaSolarPanel />,
      href: "#aboutus",
      dropdown: [
        { name: "Be a Member", icon: <FaCog />, href: "/bemember" },
        { name: "Membership Plan", icon: <FaTools />, href: "/membership" },
        // { name: "Visitors", icon: <FaCog />, href: "/visitors" },
      ],
    },
    {
      name: "Media",
      icon: <FaImage />,
      href: "#aboutus",
      dropdown: [
        // { name: "Events", icon: <FaImage />, href: "/events" },
        { name: "Gallery", icon: <FaImage />, href: "/gallary" },
        // { name: "News", icon: <FaImage />, href: "/news" },
        // { name: "Videos", icon: <FaImage />, href: "/videos" },
      ],
    },
    {
      name: "Solar Guide",
      icon: <FaBook />,
      href: "#aboutus",
      dropdown: [
        { name: "FAQs", icon: <FaCog />, href: "/faq" },
        { name: "Important Circular", icon: <FaTools />, href: "/Circulars" },
      ],
    },
    // { name: "Exibition", icon: <FaInfoCircle />, href: "#about" },
    { name: "Contact Us", icon: <FaEnvelope />, href: "/ContactUs" },
    { name: "Login", icon: <FaEnvelope />, href: "/login" },
  ];

  const toggleSidebar = () => setIsOpen(!isOpen);

  const toggleDropdown = (itemName) => {
    setActiveDropdown(activeDropdown === itemName ? null : itemName);
  };

  const dropdownVariants = {
    hidden: {
      opacity: 0,
      y: -10,
      scale: 0.95,
    },
    visible: {
      opacity: 1,
      y: 0,
      scale: 1,
      transition: {
        duration: 0.2,
        ease: "easeOut",
      },
    },
    exit: {
      opacity: 0,
      y: -10,
      scale: 0.95,
      transition: {
        duration: 0.15,
        ease: "easeIn",
      },
    },
  };

  const itemVariants = {
    hidden: { x: -20, opacity: 0 },
    visible: (i) => ({
      x: 0,
      opacity: 1,
      transition: {
        delay: i * 0.1,
        duration: 0.3,
      },
    }),
  };

  return (
    <>
      <motion.nav
        className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 md:rounded-br-full rounded-br-[6rem] ${
          scrolled
            ? "bg-white/90 backdrop-blur-md shadow-lg"
            : "bg-white/60 backdrop-blur-[5.5px] shadow-[0_4px_30px_rgba(0,0,0,0.1)]"
        }`}
        initial={{ y: -100 }}
        animate={{ y: 0 }}
        transition={{ duration: 0.6 }}
      >
        <div className="container mx-auto px-4">
          <div className="flex justify-between items-center md:py-4 py-6">
            {/* Logo */}
            <motion.div
              className="flex items-center space-x-2"
              whileHover={{ scale: 1.05 }}
            >
              <img
                src={masmaLogo}
                alt="masma logo"
                className="h-16 w-16 object-cover"
              />
            </motion.div>

            {/* Desktop Menu */}
            <div className="hidden md:flex space-x-8" ref={dropdownRef}>
              {navItems.map((item, index) => (
                <div key={item.name} className="relative">
                  {item.dropdown ? (
                    <motion.div
                      className="relative"
                      initial="hidden"
                      animate="visible"
                      custom={index}
                      variants={itemVariants}
                    >
                      <button
                        onClick={() => toggleDropdown(item.name)}
                        className={`flex items-center space-x-1 font-medium transition-colors ${
                          scrolled
                            ? "text-[#005aa8] hover:text-[#ed6605]"
                            : "text-[#005aa8] hover:text-[#ed6605]"
                        } ${
                          activeDropdown === item.name ? "text-[#ed6605]" : ""
                        }`}
                      >
                        <span>{item.icon}</span>
                        <span>{item.name}</span>
                        <motion.span
                          animate={{
                            rotate: activeDropdown === item.name ? 180 : 0,
                          }}
                          transition={{ duration: 0.2 }}
                        >
                          <HiChevronDown className="ml-1" />
                        </motion.span>
                      </button>

                      <AnimatePresence>
                        {activeDropdown === item.name && (
                          <motion.div
                            className="absolute top-full left-0 mt-2 w-64 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-white/20 overflow-hidden"
                            variants={dropdownVariants}
                            initial="hidden"
                            animate="visible"
                            exit="exit"
                          >
                            <div className="p-2">
                              {item.dropdown.map((dropdownItem, dropIndex) => (
                                <motion.a
                                  key={dropdownItem.name}
                                  href={dropdownItem.href}
                                  className="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:text-[#ed6605] hover:bg-gray-50 rounded-lg transition-all duration-200 group"
                                  whileHover={{
                                    x: 5,
                                    backgroundColor: "rgba(237, 102, 5, 0.05)",
                                  }}
                                  onClick={() => setActiveDropdown(null)}
                                  initial={{ opacity: 0, x: -10 }}
                                  animate={{ opacity: 1, x: 0 }}
                                  transition={{ delay: dropIndex * 0.05 }}
                                >
                                  <span className="text-[#005aa8] group-hover:text-[#ed6605] transition-colors">
                                    {dropdownItem.icon}
                                  </span>
                                  <span className="font-medium">
                                    {dropdownItem.name}
                                  </span>
                                </motion.a>
                              ))}
                            </div>
                          </motion.div>
                        )}
                      </AnimatePresence>
                    </motion.div>
                  ) : (
                    <motion.a
                      href={item.href}
                      className={`flex items-center space-x-1 font-medium transition-colors ${
                        scrolled
                          ? "text-[#005aa8] hover:text-[#ed6605]"
                          : "text-[#005aa8] hover:text-[#ed6605]"
                      }`}
                      whileHover={{ scale: 1.1 }}
                      whileTap={{ scale: 0.95 }}
                      initial="hidden"
                      animate="visible"
                      custom={index}
                      variants={itemVariants}
                    >
                      <span>{item.icon}</span>
                      <span>{item.name}</span>
                    </motion.a>
                  )}
                </div>
              ))}
            </div>

            {/* Mobile Menu Button */}
            <motion.button
              className={`md:hidden text-2xl ${
                scrolled ? "text-[#005aa8]" : "text-[#005aa8]"
              }`}
              onClick={toggleSidebar}
              whileHover={{ scale: 1.1 }}
              whileTap={{ scale: 0.9 }}
            >
              {isOpen ? <HiX /> : <HiMenuAlt3 />}
            </motion.button>
          </div>
        </div>
      </motion.nav>

      {/* Mobile Sidebar */}
      <AnimatePresence>
        {isOpen && (
          <>
            <motion.div
              className="fixed inset-0 bg-black/50 z-40 md:hidden"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={toggleSidebar}
            />
            <motion.div
              className="fixed top-0 right-0 h-full w-64 bg-white z-50 md:hidden shadow-2xl"
              initial={{ x: "100%" }}
              animate={{ x: 0 }}
              exit={{ x: "100%" }}
              transition={{ type: "spring", damping: 30 }}
            >
              <div className="p-6">
                <div className="flex items-center justify-between mb-8">
                  <div className="flex items-center space-x-2">
                    <img
                      src={masmaLogo}
                      alt="masma logo"
                      className="h-12 w-12 object-cover"
                    />
                    <span className="text-lg font-bold text-[#005aa8]">
                      SolarEnergy
                    </span>
                  </div>
                  <button
                    onClick={toggleSidebar}
                    className="text-gray-600 text-xl"
                  >
                    <HiX />
                  </button>
                </div>

                <div className="space-y-2">
                  {navItems.map((item, index) => (
                    <div key={item.name}>
                      {item.dropdown ? (
                        <div className="space-y-2">
                          <button
                            onClick={() =>
                              toggleDropdown(item.name + "-mobile")
                            }
                            className="flex items-center justify-between w-full p-3 text-gray-700 hover:text-[#ed6605] hover:bg-gray-100 rounded-lg transition-colors"
                          >
                            <div className="flex items-center space-x-3">
                              <span className="text-[#005aa8]">
                                {item.icon}
                              </span>
                              <span className="font-medium">{item.name}</span>
                            </div>
                            <motion.span
                              animate={{
                                rotate:
                                  activeDropdown === item.name + "-mobile"
                                    ? 180
                                    : 0,
                              }}
                              transition={{ duration: 0.2 }}
                            >
                              <HiChevronDown />
                            </motion.span>
                          </button>

                          <AnimatePresence>
                            {activeDropdown === item.name + "-mobile" && (
                              <motion.div
                                className="ml-6 space-y-1 border-l-2 border-[#005aa8] pl-4"
                                initial={{ opacity: 0, height: 0 }}
                                animate={{ opacity: 1, height: "auto" }}
                                exit={{ opacity: 0, height: 0 }}
                                transition={{ duration: 0.3 }}
                              >
                                {item.dropdown.map((dropdownItem) => (
                                  <motion.a
                                    key={dropdownItem.name}
                                    href={dropdownItem.href}
                                    className="flex items-center space-x-3 p-2 text-gray-600 hover:text-[#ed6605] hover:bg-gray-50 rounded-lg transition-colors"
                                    onClick={toggleSidebar}
                                    initial={{ opacity: 0, x: -20 }}
                                    animate={{ opacity: 1, x: 0 }}
                                    transition={{ duration: 0.2 }}
                                  >
                                    <span className="text-[#005aa8]">
                                      {dropdownItem.icon}
                                    </span>
                                    <span>{dropdownItem.name}</span>
                                  </motion.a>
                                ))}
                              </motion.div>
                            )}
                          </AnimatePresence>
                        </div>
                      ) : (
                        <motion.a
                          href={item.href}
                          className="flex items-center space-x-3 p-3 text-gray-700 hover:text-[#ed6605] hover:bg-gray-100 rounded-lg transition-colors"
                          onClick={toggleSidebar}
                          initial={{ x: 50, opacity: 0 }}
                          animate={{ x: 0, opacity: 1 }}
                          transition={{ delay: index * 0.1 }}
                        >
                          <span className="text-[#005aa8]">{item.icon}</span>
                          <span className="font-medium">{item.name}</span>
                        </motion.a>
                      )}
                    </div>
                  ))}
                </div>
              </div>
            </motion.div>
          </>
        )}
      </AnimatePresence>
    </>
  );
};

export default Navbar;
