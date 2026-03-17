import React from "react";

import HorizontalNavbar from "../../component/admin/HorizontalNavbar";
import VerticalSidebar from "../../component/admin/VerticalSidebar";
import Footer from "../../component/admin/Footer";
import HomePage from "./HomePage";
import DebugCookies from "./DebugCookies";

const Admin = () => {
  return (
    <div className="min-h-screen flex flex-col">
      {/* Horizontal Navbar */}
      <HorizontalNavbar />

      <div className="flex flex-1">
        {/* Vertical Sidebar - Now properly toggleable */}
        <VerticalSidebar />

        {/* Main Content - Adjusts based on sidebar state */}
        <div className="flex-1 min-w-0">
          <HomePage />
        </div>
      </div>
      {/* <DebugCookies /> */}
      {/* Footer */}
      <Footer />
    </div>
  );
};

export default Admin;
