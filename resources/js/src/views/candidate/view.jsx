import React, { useState } from 'react';

const CandidateDetailView = ({ user, initialErrors = {} }) => {
  const [errors, setErrors] = useState(initialErrors);

  const handleSubmit = async (status, event) => {
    event.preventDefault();
    try {
      // Replace with your actual API endpoint and adjust headers as needed.
      const response = await fetch(`/api/candidates/${user.id}/select`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json'
          // Include auth headers or CSRF token if required.
        },
        body: JSON.stringify({
          job_id: user.job_id,
          status: status
        })
      });
      if (!response.ok) {
        const data = await response.json();
        setErrors(data.errors || {});
      } else {
        // Handle success (redirect, notification, etc.)
        console.log('Candidate status updated successfully');
      }
    } catch (error) {
      console.error('Error updating candidate status:', error);
      setErrors({ general: 'An error occurred. Please try again.' });
    }
  };

  return (
    <div className="container-xxl flex-grow-1 container-p-y">
      {/* Replace with your custom PageTitle component if needed */}
      <div className="page-title">
        <h1>Candidate View</h1>
      </div>

      {/* Error Alert */}
      {Object.keys(errors).length > 0 && (
        <div className="alert alert-danger">
          <ul>
            {Object.values(errors).map((error, index) => (
              <li key={index}>{error}</li>
            ))}
          </ul>
        </div>
      )}

      {/* Detail Form */}
      <div className="row">
        <div className="col-xxl">
          <div className="card mb-4">
            <div className="card-header d-flex align-items-center justify-content-between">
              <h5 className="mb-0">Detail Form</h5>
              <small className="text-muted float-end">View all the fields</small>
            </div>
            <div className="card-body">
              {/* Name */}
              <div className="row mb-3">
                <label htmlFor="basic-default-name" className="col-sm-2 col-form-label">
                  Name
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="basic-default-name"
                    value={user.name}
                    readOnly
                  />
                </div>
              </div>
              {/* Email */}
              <div className="row mb-3">
                <label htmlFor="basic-default-email" className="col-sm-2 col-form-label">
                  Email
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="basic-default-email"
                    value={user.email}
                    readOnly
                  />
                </div>
              </div>
              {/* Phone Number */}
              <div className="row mb-3">
                <label htmlFor="basic-default-phone" className="col-sm-2 col-form-label">
                  Phone Number
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="basic-default-phone"
                    value={user.phone_number}
                    readOnly
                  />
                </div>
              </div>
              {/* Gender */}
              <div className="row mb-3">
                <label htmlFor="basic-default-gender" className="col-sm-2 col-form-label">
                  Gender
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="basic-default-gender"
                    value={user.gender}
                    readOnly
                  />
                </div>
              </div>
              {/* Birth Date */}
              <div className="row mb-3">
                <label htmlFor="basic-default-birth" className="col-sm-2 col-form-label">
                  Birth Date
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="basic-default-birth"
                    value={user.birth_date}
                    readOnly
                  />
                </div>
              </div>
              {/* Address */}
              <div className="row mb-3">
                <label htmlFor="basic-default-address" className="col-sm-2 col-form-label">
                  Address
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="basic-default-address"
                    value={user.address}
                    readOnly
                  />
                </div>
              </div>
              {/* Zip Code */}
              <div className="row mb-3">
                <label htmlFor="basic-default-zipcode" className="col-sm-2 col-form-label">
                  Zip Code
                </label>
                <div className="col-sm-10">
                  <input
                    type="number"
                    className="form-control"
                    id="basic-default-zipcode"
                    value={user.zipcode}
                    readOnly
                  />
                </div>
              </div>
              {/* Skill */}
              <div className="row mb-3">
                <label htmlFor="basic-default-skill" className="col-sm-2 col-form-label">
                  Skill
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="basic-default-skill"
                    value={user.skill}
                    readOnly
                  />
                </div>
              </div>
              {/* Latest Degree */}
              <div className="row mb-3">
                <label htmlFor="basic-default-degree" className="col-sm-2 col-form-label">
                  Latest Degree
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="basic-default-degree"
                    value={user.degree}
                    readOnly
                  />
                </div>
              </div>
              {/* Latest University */}
              <div className="row mb-3">
                <label htmlFor="basic-default-university" className="col-sm-2 col-form-label">
                  Latest University
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="basic-default-university"
                    value={user.latest_university}
                    readOnly
                  />
                </div>
              </div>
              {/* Current Organization */}
              <div className="row mb-3">
                <label htmlFor="basic-default-current-org" className="col-sm-2 col-form-label">
                  Current Organization
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="basic-default-current-org"
                    value={user.current_organization}
                    readOnly
                  />
                </div>
              </div>
              {/* Current Department */}
              <div className="row mb-3">
                <label htmlFor="basic-default-department" className="col-sm-2 col-form-label">
                  Current Department
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="basic-default-department"
                    value={user.current_department}
                    readOnly
                  />
                </div>
              </div>
              {/* Current Position */}
              <div className="row mb-3">
                <label htmlFor="basic-default-position" className="col-sm-2 col-form-label">
                  Current Position
                </label>
                <div className="col-sm-10">
                  <input
                    type="text"
                    className="form-control"
                    id="basic-default-position"
                    value={user.current_position}
                    readOnly
                  />
                </div>
              </div>
              {/* Description */}
              <div className="row mb-3">
                <label htmlFor="basic-default-description" className="col-sm-2 col-form-label">
                  Description
                </label>
                <div className="col-sm-10">
                  <textarea
                    id="basic-default-description"
                    className="form-control"
                    value={user.description}
                    readOnly
                  ></textarea>
                </div>
              </div>
              {/* Action Buttons */}
              <div className="row justify-content-end">
                <div className="col-sm-10">
                  <form onSubmit={(e) => e.preventDefault()}>
                    <input type="hidden" name="job_id" value={user.job_id} />
                    <button
                      type="button"
                      onClick={(e) => handleSubmit('Selected', e)}
                      className="btn btn-primary me-2"
                    >
                      Select
                    </button>
                    <button
                      type="button"
                      onClick={(e) => handleSubmit('Rejected', e)}
                      className="btn btn-danger"
                    >
                      Reject
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CandidateDetailView;
