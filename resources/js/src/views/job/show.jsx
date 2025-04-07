import React from 'react';
import PageTitle from '../components/PageTitle'; // Adjust the import path as needed

const JobViewForm = ({ job }) => {
  return (
    <div className="container-xxl flex-grow-1 container-p-y">
      <PageTitle menu="Job" page="View" />
      <div className="row">
        <div className="col-xxl">
          <div className="card mb-4">
            <div className="card-header d-flex align-items-center justify-content-between">
              <h5 className="mb-0">View Form</h5>
              <small className="text-muted float-end">View all the fields</small>
            </div>
            <div className="card-body">
              {/* Job Title */}
              <div className="row mb-3">
                <label
                  className="col-sm-2 col-form-label"
                  htmlFor="jobTitle"
                >
                  Job Title
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="jobTitle"
                    value={job.job_title}
                    readOnly
                  />
                </div>
              </div>
              {/* Address */}
              <div className="row mb-3">
                <label
                  className="col-sm-2 col-form-label"
                  htmlFor="address"
                >
                  Address
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="address"
                    value={job.address}
                    readOnly
                  />
                </div>
              </div>
              {/* Zip Code & Job Type */}
              <div className="row mb-3">
                <label
                  className="col-sm-2 col-form-label"
                  htmlFor="zipcode"
                >
                  Zip Code
                </label>
                <div className="col-sm-4">
                  <input
                    type="number"
                    className="form-control"
                    id="zipcode"
                    value={job.zipcode}
                    readOnly
                  />
                </div>
                <div className="col-sm-2 offset-sm-1">
                  <label
                    className="col-form-label"
                    htmlFor="jobType"
                  >
                    Job Type
                  </label>
                </div>
                <div className="col-sm-3">
                  <input
                    type="text"
                    className="form-control"
                    id="jobType"
                    value={job.is_remote}
                    readOnly
                  />
                </div>
              </div>
              {/* Required Skill */}
              <div className="row mb-3">
                <label
                  className="col-sm-2 col-form-label"
                  htmlFor="skill"
                >
                  Required Skill
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="skill"
                    value={job.skill}
                    readOnly
                  />
                </div>
              </div>
              {/* Required Experience */}
              <div className="row mb-3">
                <label
                  className="col-sm-2 col-form-label"
                  htmlFor="experience"
                >
                  Required Experience
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="experience"
                    value={job.experience}
                    readOnly
                  />
                </div>
              </div>
              {/* Required Education */}
              <div className="row mb-3">
                <label
                  className="col-sm-2 col-form-label"
                  htmlFor="education"
                >
                  Required Education
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="education"
                    value={job.education}
                    readOnly
                  />
                </div>
              </div>
              {/* Budget */}
              <div className="row mb-3">
                <label
                  className="col-sm-2 col-form-label"
                  htmlFor="budget"
                >
                  Budget
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="budget"
                    value={job.budget}
                    readOnly
                  />
                </div>
              </div>
              {/* Bid Closing & Deadline */}
              <div className="row mb-3">
                <label
                  className="col-sm-2 col-form-label"
                  htmlFor="bidClose"
                >
                  Bid Closing
                </label>
                <div className="col-sm-4">
                  <input
                    type="date"
                    className="form-control"
                    id="bidClose"
                    value={job.bid_close}
                    readOnly
                  />
                </div>
                <label
                  className="col-sm-2 col-form-label"
                  htmlFor="deadline"
                >
                  Deadline
                </label>
                <div className="col-sm-4">
                  <input
                    type="date"
                    className="form-control"
                    id="deadline"
                    value={job.deadline}
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
                    className="form-control"
                    id="description"
                    value={job.description}
                    readOnly
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

export default JobViewForm;
