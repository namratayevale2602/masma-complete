import React from "react";
import {
  createBrowserRouter,
  createRoutesFromElements,
  Route,
  RouterProvider,
} from "react-router-dom";

import ProtectedRoute from "./component/ProtectedRoute";
import Layout from "./layout/Layout";
import Home from "./pages/home/Home";
import AboutUs from "./pages/aboutus/AboutUs";
import CompactTeam from "./pages/aboutus/CompactTeam";
import Associates from "./pages/aboutus/Associates";
import ReligionalChap from "./pages/aboutus/ReligionalChap";
import Events from "./pages/events/Events";
import News from "./pages/events/News";
import Videos from "./pages/events/Videos";
import FAQ from "./pages/guide/FAQ";
import Circulars from "./pages/guide/Circulars";
import ContactUs from "./pages/contactus/ContactUs";
import BeMember from "./pages/member/BeMember";
import Login from "./component/login/Login";
import Admin from "./pages/admin/Admin";
import MembershipPlans from "./pages/member/MembershipPlans";
import Visitors from "./pages/member/Visitors";
import VisitorCard from "./pages/member/VisitorCard";
import Gallary from "./pages/events/gallary";
import PrivacyPolicy from "./pages/Privacy";
import TermsAndConditions from "./pages/TermCondition";

function App() {
  const router = createBrowserRouter(
    createRoutesFromElements(
      <>
        {/* Public Routes */}
        <Route path="/" element={<Layout />}>
          <Route index element={<Home />} />
          <Route path="/about-us" element={<AboutUs />} />
          <Route path="/apex-body" element={<CompactTeam />} />
          <Route path="/associates" element={<Associates />} />
          <Route path="/ReligionalChap" element={<ReligionalChap />} />
          <Route path="/events" element={<Events />} />
          <Route path="/news" element={<News />} />
          <Route path="/gallary" element={<Gallary />} />
          <Route path="/videos" element={<Videos />} />
          <Route path="/faq" element={<FAQ />} />
          <Route path="/Circulars" element={<Circulars />} />
          <Route path="/ContactUs" element={<ContactUs />} />
          <Route path="/bemember" element={<BeMember />} />
          <Route path="/membership" element={<MembershipPlans />} />
          <Route path="/visitors" element={<Visitors />} />
          <Route path="/visitor/:id/card" element={<VisitorCard />} />
          {/* Login should be public */}
          <Route path="/login" element={<Login />} />
          <Route path="/privacy-policy" element={<PrivacyPolicy />} />
          <Route path="/term&condition" element={<TermsAndConditions />} />
        </Route>

        {/* Protected Routes */}
        <Route element={<ProtectedRoute />}>
          <Route path="/dashboard" element={<Admin />} />
          {/* Add more protected routes here */}
          <Route path="/admin/*" element={<Admin />} />
        </Route>
      </>
    )
  );

  return <RouterProvider router={router} />;
}

export default App;
