// src/pages/TermsAndConditions.jsx
import React from "react";

const TermsAndConditions = () => {
  return (
    <div className="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 pt-40">
      <div className="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <div className="mb-8 text-center">
          <h1 className="text-3xl font-bold text-gray-900 mb-2">
            Terms & Conditions
          </h1>
          <p className="text-gray-600">
            THE MAHARASHTRA SOLAR MANUFACTURES ASSOCIATION (MASMA)
          </p>
          <p className="text-gray-500 text-sm mt-2">
            Effective Date: {new Date().toLocaleDateString()}
          </p>
        </div>

        <div className="space-y-8">
          <section className="bg-blue-50 p-6 rounded-lg mb-8">
            <h2 className="text-2xl font-bold text-blue-800 mb-4">
              Welcome to MASMA
            </h2>
            <p className="text-blue-700">
              MASMA is focused on enriching your experience as members,
              providing opportunities, training, awareness programs, and
              collaborating with organizations to promote solar energy in
              Maharashtra.
            </p>
          </section>

          <section>
            <h2 className="text-2xl font-semibold text-gray-800 mb-4">
              1. Acceptance of Terms
            </h2>
            <p className="text-gray-600">
              By accessing our website, becoming a member, or using any of our
              services, you agree to be bound by these Terms and Conditions. If
              you disagree with any part, you may not access our services.
            </p>
          </section>

          <section>
            <h2 className="text-2xl font-semibold text-gray-800 mb-4">
              2. Membership
            </h2>
            <div className="space-y-4">
              <div>
                <h3 className="text-xl font-medium text-gray-700 mb-2">
                  2.1 Eligibility
                </h3>
                <ul className="list-disc pl-5 text-gray-600 space-y-1">
                  <li>
                    Solar manufacturers, EPC companies, and businesses in the
                    solar sector
                  </li>
                  <li>
                    Organizations working in climate change and renewable energy
                  </li>
                  <li>
                    Individuals and entities supporting solar energy promotion
                  </li>
                </ul>
              </div>
              <div>
                <h3 className="text-xl font-medium text-gray-700 mb-2">
                  2.2 Membership Types
                </h3>
                <ul className="list-disc pl-5 text-gray-600 space-y-1">
                  <li>
                    <strong>Regular Members:</strong> Solar manufacturers and
                    related businesses
                  </li>
                  <li>
                    <strong>Associate Members:</strong> Service providers and
                    consultants
                  </li>
                  <li>
                    <strong>Institutional Members:</strong> Educational and
                    research institutions
                  </li>
                  <li>
                    <strong>Collaborative Members:</strong> Partner
                    organizations
                  </li>
                </ul>
              </div>
              <div>
                <h3 className="text-xl font-medium text-gray-700 mb-2">
                  2.3 Membership Responsibilities
                </h3>
                <ul className="list-disc pl-5 text-gray-600 space-y-1">
                  <li>Provide accurate information during registration</li>
                  <li>Pay membership fees promptly</li>
                  <li>Adhere to MASMA's code of conduct</li>
                  <li>Promote ethical business practices</li>
                  <li>Participate in association activities</li>
                </ul>
              </div>
            </div>
          </section>

          <section>
            <h2 className="text-2xl font-semibold text-gray-800 mb-4">
              3. Services and Benefits
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="bg-green-50 p-4 rounded-lg">
                <h3 className="font-semibold text-green-800 mb-2">
                  Member Benefits
                </h3>
                <ul className="list-disc pl-5 text-green-700 text-sm space-y-1">
                  <li>Networking opportunities</li>
                  <li>Training and workshops</li>
                  <li>Industry updates</li>
                  <li>Business referrals</li>
                  <li>Event participation</li>
                </ul>
              </div>
              <div className="bg-yellow-50 p-4 rounded-lg">
                <h3 className="font-semibold text-yellow-800 mb-2">
                  Association Services
                </h3>
                <ul className="list-disc pl-5 text-yellow-700 text-sm space-y-1">
                  <li>Policy advocacy</li>
                  <li>Market research</li>
                  <li>Collaborative events</li>
                  <li>Technical support</li>
                  <li>Awareness campaigns</li>
                </ul>
              </div>
            </div>
          </section>

          <section>
            <h2 className="text-2xl font-semibold text-gray-800 mb-4">
              4. Fees and Payments
            </h2>
            <ul className="list-disc pl-5 text-gray-600 space-y-2">
              <li>Membership fees are annual and non-refundable</li>
              <li>Fees are subject to change with prior notice</li>
              <li>Late payments may result in membership suspension</li>
              <li>
                Additional fees may apply for special events and training
                programs
              </li>
            </ul>
          </section>

          <section>
            <h2 className="text-2xl font-semibold text-gray-800 mb-4">
              5. Code of Conduct
            </h2>
            <p className="text-gray-600 mb-4">All members must adhere to:</p>
            <ul className="list-disc pl-5 text-gray-600 space-y-2">
              <li>Professional and ethical business practices</li>
              <li>Respect for fellow members and competitors</li>
              <li>Compliance with all applicable laws and regulations</li>
              <li>Truthful representation of products and services</li>
              <li>Environmental responsibility in operations</li>
              <li>Confidentiality of shared business information</li>
            </ul>
          </section>

          <section>
            <h2 className="text-2xl font-semibold text-gray-800 mb-4">
              6. Intellectual Property
            </h2>
            <p className="text-gray-600 mb-2">
              All content on our website and materials provided to members:
            </p>
            <ul className="list-disc pl-5 text-gray-600 space-y-1">
              <li>Is owned by MASMA or its licensors</li>
              <li>May not be reproduced without permission</li>
              <li>Is for personal and business use only</li>
              <li>Includes trademarks, logos, and proprietary information</li>
            </ul>
          </section>

          <section>
            <h2 className="text-2xl font-semibold text-gray-800 mb-4">
              7. Events and Collaboration
            </h2>
            <div className="bg-gray-50 p-4 rounded-lg">
              <p className="text-gray-600 mb-2">
                MASMA organizes various events in collaboration with:
              </p>
              <ul className="list-disc pl-5 text-gray-600 space-y-1">
                <li>Other solar industry associations</li>
                <li>Climate change organizations</li>
                <li>Government agencies</li>
                <li>Educational institutions</li>
                <li>Regional chapters</li>
              </ul>
              <p className="text-gray-600 mt-3">
                Event participation may require separate registration and fees.
                Cancellation policies vary by event.
              </p>
            </div>
          </section>

          <section>
            <h2 className="text-2xl font-semibold text-gray-800 mb-4">
              8. Termination
            </h2>
            <p className="text-gray-600 mb-2">
              MASMA reserves the right to terminate membership for:
            </p>
            <ul className="list-disc pl-5 text-gray-600 space-y-1">
              <li>Violation of these terms</li>
              <li>Non-payment of fees</li>
              <li>Unethical business practices</li>
              <li>Actions harmful to the association's reputation</li>
              <li>Failure to comply with code of conduct</li>
            </ul>
          </section>

          <section>
            <h2 className="text-2xl font-semibold text-gray-800 mb-4">
              9. Limitation of Liability
            </h2>
            <p className="text-gray-600">
              MASMA is not liable for any direct, indirect, incidental, or
              consequential damages arising from membership or use of our
              services. We provide information and networking opportunities but
              do not guarantee specific business outcomes.
            </p>
          </section>

          <section>
            <h2 className="text-2xl font-semibold text-gray-800 mb-4">
              10. Dispute Resolution
            </h2>
            <p className="text-gray-600">
              Any disputes shall be resolved through mutual discussion. If
              unresolved, matters shall be subject to the jurisdiction of courts
              in Pune, Maharashtra.
            </p>
          </section>

          <section>
            <h2 className="text-2xl font-semibold text-gray-800 mb-4">
              11. Amendments
            </h2>
            <p className="text-gray-600">
              MASMA may modify these Terms and Conditions at any time. Members
              will be notified of significant changes. Continued use of services
              constitutes acceptance of revised terms.
            </p>
          </section>

          <section>
            <h2 className="text-2xl font-semibold text-gray-800 mb-4">
              12. Contact Information
            </h2>
            <div className="bg-gray-50 p-4 rounded-lg">
              <p className="text-gray-600 mb-1">
                <strong>THE MAHARASHTRA SOLAR MANUFACTURES ASSOCIATION</strong>
              </p>
              <p className="text-gray-600 mb-1">
                D-93, 4th Floor, Office No.93, G-Wing, S.No. 19A/3B
              </p>
              <p className="text-gray-600 mb-1">
                Pune - Satara Rd, KK Market, Ahilya devi chowk Dhankawadi
              </p>
              <p className="text-gray-600 mb-1">Pune, Maharashtra 411043</p>
              <p className="text-gray-600 mb-1">
                <strong>Phone:</strong> +91 93091 67947
              </p>
              <p className="text-gray-600 mb-1">
                <strong>Email:</strong> info@masma.com | support@masma.com
              </p>
              <p className="text-gray-600">
                <strong>Working Hours:</strong> Mon-Fri: 9:00 AM - 6:00 PM |
                Sat: 9:00 AM - 2:00 PM
              </p>
            </div>
          </section>

          <section>
            <h2 className="text-2xl font-semibold text-gray-800 mb-4">
              13. Member Engagement
            </h2>
            <div className="bg-blue-50 p-6 rounded-lg">
              <p className="text-blue-700 mb-4">
                <strong>We encourage all members to:</strong>
              </p>
              <ul className="list-disc pl-5 text-blue-700 space-y-2">
                <li>Share ideas and suggestions for improvement</li>
                <li>Participate actively in association activities</li>
                <li>Collaborate with fellow members</li>
                <li>Promote solar energy awareness</li>
                <li>Contribute to sustainable sector growth</li>
              </ul>
              <p className="text-blue-700 mt-4">
                For suggestions or to get involved, reach out to any board
                member or leader.
              </p>
            </div>
          </section>

          <div className="mt-12 pt-8 border-t border-gray-200">
            <p className="text-gray-500 text-sm">
              These Terms & Conditions constitute the entire agreement between
              you and MASMA regarding your membership and use of our services.
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default TermsAndConditions;
