import { motion, AnimatePresence } from "framer-motion";
import { useInView } from "react-intersection-observer";
import { useState, useRef } from "react";
import {
  FaPlay,
  FaPause,
  FaExpand,
  FaCompress,
  FaVolumeUp,
  FaVolumeMute,
  FaTimes,
  FaYoutube,
  FaVideo,
  FaCalendarAlt,
  FaUser,
} from "react-icons/fa";

const Videos = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.2,
  });

  const [selectedVideo, setSelectedVideo] = useState(null);
  const [isFullScreen, setIsFullScreen] = useState(false);
  const [isPlaying, setIsPlaying] = useState(false);
  const [isMuted, setIsMuted] = useState(false);
  const videoRef = useRef(null);

  const videoContent = [
    {
      id: 1,
      title: "Solar Energy Revolution in Maharashtra",
      type: "youtube",
      videoId: "dQw4w9WgXcQ", // Example YouTube ID
      thumbnail:
        "https://images.unsplash.com/photo-1509391366360-2e959784a276?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      duration: "15:30",
      category: "Documentary",
    },
    {
      id: 2,
      title: "MASMA Annual Conference 2024 Highlights",
      type: "normal",
      videoUrl:
        "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4", // Sample video URL
      thumbnail:
        "https://images.unsplash.com/photo-1497366754035-f200968a6e72?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      duration: "8:45",
      category: "Conference",
    },
    {
      id: 3,
      title: "Solar Installation Training Program",
      type: "youtube",
      videoId: "dQw4w9WgXcQ",
      thumbnail:
        "https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      duration: "22:15",
      category: "Tutorial",
    },
    {
      id: 4,
      title: "Interview with Solar Industry Leaders",
      type: "normal",
      videoUrl:
        "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4", // Sample video URL
      thumbnail:
        "https://images.unsplash.com/photo-1549399905-02da7514e9a3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      duration: "28:10",
      category: "Interview",
    },
    {
      id: 5,
      title: "Solar Technology Innovations 2024",
      type: "youtube",
      videoId: "dQw4w9WgXcQ",
      thumbnail:
        "https://images.unsplash.com/photo-1562155618-e1a8bc2eb04f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      duration: "12:20",
      category: "Technology",
    },
    {
      id: 6,
      title: "Community Solar Project Success Story",
      type: "normal",
      videoUrl:
        "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4", // Sample video URL
      thumbnail:
        "https://images.unsplash.com/photo-1564059813745-b2c0c42dcd19?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      duration: "17:45",
      category: "Case Study",
    },
  ];

  const openVideo = (video) => {
    setSelectedVideo(video);
    setIsPlaying(true);
    setIsFullScreen(false);
  };

  const closeVideo = () => {
    setSelectedVideo(null);
    setIsPlaying(false);
    setIsFullScreen(false);
    if (videoRef.current) {
      videoRef.current.pause();
      videoRef.current.currentTime = 0;
    }
  };

  const togglePlay = () => {
    if (videoRef.current) {
      if (isPlaying) {
        videoRef.current.pause();
      } else {
        videoRef.current.play();
      }
      setIsPlaying(!isPlaying);
    }
  };

  const toggleFullScreen = () => {
    setIsFullScreen(!isFullScreen);
  };

  const toggleMute = () => {
    if (videoRef.current) {
      videoRef.current.muted = !isMuted;
      setIsMuted(!isMuted);
    }
  };

  const handleVideoEnd = () => {
    setIsPlaying(false);
    if (videoRef.current) {
      videoRef.current.currentTime = 0;
    }
  };

  const VideoPlayer = ({ video }) => {
    return (
      <div className="relative w-full h-full bg-black">
        {video.type === "youtube" ? (
          <iframe
            src={`https://www.youtube.com/embed/${video.videoId}?autoplay=1&rel=0&modestbranding=1`}
            className="w-full h-full"
            frameBorder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowFullScreen
            title={video.title}
          />
        ) : (
          <video
            ref={videoRef}
            className="w-full h-full"
            controls={false}
            onEnded={handleVideoEnd}
            onPlay={() => setIsPlaying(true)}
            onPause={() => setIsPlaying(false)}
          >
            <source src={video.videoUrl} type="video/mp4" />
            Your browser does not support the video tag.
          </video>
        )}

        {/* Custom Controls for Normal Videos */}
        {video.type === "normal" && (
          <div className="absolute bottom-0 left-0 right-0 bg-linear-to-t from-black/80 to-transparent p-4">
            <div className="flex items-center justify-between">
              <div className="flex items-center space-x-4">
                <button
                  onClick={togglePlay}
                  className="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition-colors"
                >
                  {isPlaying ? (
                    <FaPause className="text-white" />
                  ) : (
                    <FaPlay className="text-white ml-1" />
                  )}
                </button>

                <button
                  onClick={toggleMute}
                  className="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition-colors"
                >
                  {isMuted ? (
                    <FaVolumeMute className="text-white text-sm" />
                  ) : (
                    <FaVolumeUp className="text-white text-sm" />
                  )}
                </button>
              </div>

              <button
                onClick={toggleFullScreen}
                className="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition-colors"
              >
                {isFullScreen ? (
                  <FaCompress className="text-white text-sm" />
                ) : (
                  <FaExpand className="text-white text-sm" />
                )}
              </button>
            </div>
          </div>
        )}
      </div>
    );
  };

  const VideoCard = ({ video }) => (
    <motion.div
      className="bg-white rounded-xl border border-gray-200 overflow-hidden group transition-all duration-300"
      whileHover={{ scale: 1.02, y: -5 }}
      initial={{ opacity: 0, y: 30 }}
      animate={inView ? { opacity: 1, y: 0 } : {}}
    >
      <div
        className="h-48 overflow-hidden relative cursor-pointer"
        onClick={() => openVideo(video)}
      >
        <img
          src={video.thumbnail}
          alt={video.title}
          className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
        />

        {/* Play Button Overlay */}
        <div className="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
          <div className="w-16 h-16 bg-[#ed6605] rounded-full flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
            <FaPlay className="text-white text-xl ml-1" />
          </div>
        </div>

        {/* Video Type Badge */}
        <div className="absolute top-3 left-3 flex items-center space-x-1 px-2 py-1 bg-black/70 rounded-full text-white text-xs">
          {video.type === "youtube" ? <FaYoutube /> : <FaVideo />}
          <span className="capitalize">{video.type}</span>
        </div>

        {/* Duration */}
        <div className="absolute bottom-3 right-3 px-2 py-1 bg-black/70 rounded text-white text-sm">
          {video.duration}
        </div>

        {/* Category */}
        <div className="absolute top-3 right-3 px-2 py-1 bg-[#ed6605] rounded text-white text-xs font-semibold">
          {video.category}
        </div>
      </div>

      <div className="p-6">
        <h3
          className="text-xl font-bold text-[#005aa8] mb-3 cursor-pointer hover:text-[#ed6605] transition-colors line-clamp-2"
          onClick={() => openVideo(video)}
        >
          {video.title}
        </h3>
      </div>
    </motion.div>
  );

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
            Video Gallery
          </h1>
          <div className="w-24 h-1 bg-[#ed6605] rounded-full mx-auto mb-6"></div>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Watch our latest videos on solar energy innovations, training
            programs, and industry insights
          </p>
        </motion.div>

        {/* Video Grid */}
        <motion.div
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
          initial={{ opacity: 0, y: 50 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.8, delay: 0.2 }}
        >
          {videoContent.map((video, index) => (
            <VideoCard key={video.id} video={video} />
          ))}
        </motion.div>

        {/* Video Player Modal */}
        <AnimatePresence>
          {selectedVideo && !isFullScreen && (
            <motion.div
              className="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={closeVideo}
            >
              <motion.div
                className="bg-white rounded-2xl max-w-4xl w-full max-h-[80vh] overflow-hidden"
                initial={{ scale: 0.8, opacity: 0 }}
                animate={{ scale: 1, opacity: 1 }}
                exit={{ scale: 0.8, opacity: 0 }}
                onClick={(e) => e.stopPropagation()}
              >
                {/* Header */}
                <div className="p-6 border-b border-gray-200 flex justify-between items-start">
                  <div className="flex-1">
                    <div className="flex items-center space-x-2 mb-2">
                      {selectedVideo.type === "youtube" ? (
                        <FaYoutube className="text-red-600 text-xl" />
                      ) : (
                        <FaVideo className="text-[#005aa8] text-xl" />
                      )}
                      <span className="text-sm text-gray-500 capitalize">
                        {selectedVideo.type} Video
                      </span>
                    </div>
                    <h3 className="text-xl font-bold text-[#005aa8]">
                      {selectedVideo.title}
                    </h3>
                  </div>
                  <button
                    onClick={closeVideo}
                    className="p-2 hover:bg-gray-100 rounded-full transition-colors ml-4"
                  >
                    <FaTimes className="text-gray-500 text-xl" />
                  </button>
                </div>

                {/* Video Player */}
                <div className="aspect-video bg-black">
                  <VideoPlayer video={selectedVideo} />
                </div>
              </motion.div>
            </motion.div>
          )}
        </AnimatePresence>

        {/* Full Screen Video Player */}
        <AnimatePresence>
          {selectedVideo && isFullScreen && (
            <motion.div
              className="fixed inset-0 bg-black z-60"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
            >
              {/* Close Button */}
              <button
                onClick={closeVideo}
                className="absolute top-4 right-4 z-10 w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition-colors"
              >
                <FaTimes className="text-white text-xl" />
              </button>

              {/* Video Info Overlay */}
              <div className="absolute top-4 left-4 z-10 text-white bg-black/50 backdrop-blur-sm px-4 py-2 rounded-lg max-w-md">
                <h3 className="font-bold text-lg mb-1">
                  {selectedVideo.title}
                </h3>
              </div>

              {/* Video Player */}
              <div className="w-full h-full">
                <VideoPlayer video={selectedVideo} />
              </div>
            </motion.div>
          )}
        </AnimatePresence>
      </div>
    </section>
  );
};

export default Videos;
