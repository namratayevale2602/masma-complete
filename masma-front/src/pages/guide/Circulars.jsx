import React, { useState, useEffect } from "react";
import { motion } from "framer-motion";
import { useInView } from "react-intersection-observer";
import {
  FaFilePdf,
  FaExternalLinkAlt,
  FaDownload,
  FaFileAlt,
  FaClipboardList,
} from "react-icons/fa";
import axios from "../../services/api";

// Icon mapping
const iconMap = {
  FaClipboardList: FaClipboardList,
  FaFileAlt: FaFileAlt,
};

const Circulars = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.2,
  });

  const [circularSections, setCircularSections] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Fetch circulars data from API
  useEffect(() => {
    const fetchCirculars = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/v1/circulars');
        
        if (response.data.success) {
          setCircularSections(response.data.data);
        } else {
          setError('Failed to load circulars data');
        }
      } catch (err) {
        console.error('Error fetching circulars:', err);
        setError('Could not connect to server');
      } finally {
        setLoading(false);
      }
    };

    fetchCirculars();
  }, []);

  const handleDocumentClick = (document) => {
    if (document.link) {
      window.open(document.link, '_blank');
    } else {
      alert('Document URL not available');
    }
  };

  const handleDownload = async (document, event) => {
    event.stopPropagation();
    
    if (document.link) {
      try {
        const response = await fetch(document.link);
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = document.title.replace(/[^a-zA-Z0-9]/g, '_') + '.pdf';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
      } catch (error) {
        console.error('Download failed:', error);
        alert('Download failed. Please try again.');
      }
    }
  };

  const getIcon = (iconName) => {
    return iconMap[iconName] || FaFileAlt;
  };

  const DocumentCard = ({ document, index }) => {
    const SectionIcon = getIcon(document.icon);
    
    return (
      <motion.div
        className="bg-white rounded-xl border border-gray-200 overflow-hidden group transition-all duration-300 cursor-pointer"
        whileHover={{ scale: 1.02, y: -5 }}
        initial={{ opacity: 0, y: 30 }}
        animate={inView ? { opacity: 1, y: 0 } : {}}
        transition={{ duration: 0.5, delay: index * 0.1 }}
        onClick={() => handleDocumentClick(document)}
      >
        <div className="p-6">
          <div className="flex items-start justify-between mb-4">
            <div className="flex items-start space-x-4 flex-1">
              <div className="shrink-0 w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center text-red-600">
                <FaFilePdf className="text-xl" />
              </div>
              <div className="flex-1 min-w-0">
                <h3 className="text-lg font-semibold text-gray-800 mb-2 group-hover:text-[#005aa8] transition-colors line-clamp-2">
                  {document.title}
                </h3>
                {document.description && (
                  <p className="text-gray-600 text-sm mb-3 line-clamp-2">
                    {document.description}
                  </p>
                )}
              </div>
            </div>

            <div className="flex items-center space-x-2 shrink-0">
              <button
                onClick={(e) => handleDownload(document, e)}
                className="p-2 text-gray-400 hover:text-[#ed6605] hover:bg-orange-50 rounded-lg transition-colors"
                title="Download Document"
              >
                <FaDownload />
              </button>
              <div className="text-gray-400 group-hover:text-[#005aa8] transition-colors">
                <FaExternalLinkAlt />
              </div>
            </div>
          </div>

          <div className="flex items-center justify-between pt-4 border-t border-gray-200">
            <span className="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
              PDF Document
            </span>
            <span className="text-sm text-gray-500">Click to open</span>
          </div>
        </div>
      </motion.div>
    );
  };

  // Loading state
  if (loading) {
    return (
      <section className="py-20 pt-40 px-4" ref={ref}>
        <div className="container mx-auto max-w-6xl text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#005aa8] border-r-transparent"></div>
          <p className="mt-4 text-gray-600">Loading circulars...</p>
        </div>
      </section>
    );
  }

  // Error state
  if (error) {
    return (
      <section className="py-20 pt-40 px-4" ref={ref}>
        <div className="container mx-auto max-w-6xl text-center text-red-500">
          {error}
        </div>
      </section>
    );
  }

  // Empty state
  if (circularSections.length === 0) {
    return (
      <section className="py-20 pt-40 px-4" ref={ref}>
        <div className="container mx-auto max-w-6xl text-center text-gray-600">
          No circulars available
        </div>
      </section>
    );
  }

  return (
    <section className="py-20 pt-40 px-4" ref={ref}>
      <div className="container mx-auto max-w-6xl">
        {/* Main Header */}
        <motion.div
          className="text-center mb-16"
          initial={{ opacity: 0, y: 30 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.8 }}
        >
          <h1 className="text-4xl md:text-5xl font-bold text-[#005aa8] mb-4">
            Important Circulars & Documents
          </h1>
          <div className="w-24 h-1 bg-[#ed6605] rounded-full mx-auto mb-6"></div>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Access important government circulars, application forms, and
            procedural documents for solar energy systems
          </p>
        </motion.div>

        {/* Circular Sections */}
        <div className="space-y-12">
          {circularSections.map((section, sectionIndex) => {
            const SectionIcon = getIcon(section.icon);
            
            return (
              <motion.section
                key={section.id}
                className="rounded-2xl p-8"
                initial={{ opacity: 0, y: 50 }}
                animate={inView ? { opacity: 1, y: 0 } : {}}
                transition={{ duration: 0.8, delay: sectionIndex * 0.2 }}
              >
                {/* Section Header */}
                <div className="flex items-center space-x-4 mb-8">
                  <div className="shrink-0 w-16 h-16 bg-linear-to-br from-[#005aa8] to-blue-600 rounded-2xl flex items-center justify-center text-white">
                    <SectionIcon className="text-2xl" />
                  </div>
                  <div>
                    <h2 className="text-3xl font-bold text-[#005aa8]">
                      {section.title}
                    </h2>
                    <p className="text-lg text-gray-600 mt-1">
                      {section.subtitle}
                    </p>
                    <div className="w-16 h-1 bg-[#ed6605] rounded-full mt-2"></div>
                  </div>
                </div>

                {/* Documents Grid */}
                <div className="grid grid-cols-1 gap-6">
                  {section.items.map((document, docIndex) => (
                    <DocumentCard
                      key={document.id}
                      document={document}
                      index={docIndex}
                    />
                  ))}
                </div>
              </motion.section>
            );
          })}
        </div>
      </div>
    </section>
  );
};

export default Circulars;