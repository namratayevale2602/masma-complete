import React from "react";
import Hero from "./Hero";
import Stats from "./Stats";
import Participent from "./Participent";
import AboutUs from "./AboutUs";
import CompnyLogo from "./CompnyLogo";
import GetInTouch from "./GetInTouch";
import BoardOfDirectors from "./BoardOfDirectors";
import CTACards from "./CTACards";

function Home() {
  return (
    <div className="relative">
      {/* Hero Section with CTACards positioned absolutely inside */}
      <div className="relative">
        <Hero />
        <CTACards />
      </div>

      {/* Other sections */}
      <div className="pt-15 sm:pt-36 md:pt-40 lg:pt-44 xl:pt-48">
        <Stats />
        <Participent />
        <AboutUs />
        <BoardOfDirectors />
        <GetInTouch />
        <CompnyLogo />
      </div>
    </div>
  );
}

export default Home;
