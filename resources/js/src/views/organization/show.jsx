import React from 'react';
import PageTitle from '../components/PageTitle'; // Adjust the import path as needed

const OrganizationView = ({ org }) => {
  return (
    <div className="container-xxl flex-grow-1 container-p-y">
      <PageTitle menu="Organization" page="View" />
      <div className="row">
        <div className="col-xxl">
          <div className="card mb-4">
            <div className="card-header d-flex align-items-center justify-content-between">
              <h5 className="mb-0">Detail Form</h5>
              <small className="text-muted float-end">View all the fields</small>
            </div>
            <div className="card-body">
              {/* Email */}
              <div className="row mb-3">
                <label className="col-sm-2 col-form-label" htmlFor="email">
                  Email
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="email"
                    value={org.email}
                    readOnly
                  />
                </div>
              </div>
              {/* Organization Name */}
              <div className="row mb-3">
                <label className="col-sm-2 col-form-label" htmlFor="orgName">
                  Organization Name
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="orgName"
                    value={org.organization_name}
                    readOnly
                  />
                </div>
              </div>
              {/* Website */}
              <div className="row mb-3">
                <label className="col-sm-2 col-form-label" htmlFor="website">
                  Website
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="website"
                    value={org.website}
                    readOnly
                  />
                </div>
              </div>
              {/* Address */}
              <div className="row mb-3">
                <label className="col-sm-2 col-form-label" htmlFor="address">
                  Address
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    name="address"
                    className="form-control"
                    id="address"
                    value={org.address}
                    readOnly
                  />
                </div>
              </div>
              {/* Description */}
              <div className="row mb-3">
                <label
                  className="col-sm-2 col-form-label"
                  htmlFor="description"
                >
                  Description
                </label>
                <div className="col-sm-10">
                  <textarea
                    id="description"
                    className="form-control"
                    name="description"
                    placeholder="Tell us about the project!"
                    aria-label="Tell us about the project!"
                    aria-describedby="basic-icon-default-message2"
                    readOnly
                    value={org.description}
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default OrganizationView;
