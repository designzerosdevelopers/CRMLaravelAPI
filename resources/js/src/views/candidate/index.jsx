import React from 'react';
import { Link } from 'react-router-dom';
import PageTitle from '../components/PageTitle'; // Adjust the import path as needed

const CandidateList = ({ applications }) => {
  // Placeholder delete function – you can hook this up to your API
  const handleDelete = (id) => {
    console.log('Deleting candidate with id:', id);
    // e.g., call your API to delete candidate here
  };

  return (
    <div className="container-xxl flex-grow-1 container-p-y">
      <PageTitle menu="Candidate" page="index" />

      {/* Hoverable Table rows */}
      <div className="card">
        <h5 className="card-header">Candidate list</h5>
        <div className="table-responsive text-nowrap">
          <table className="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Match Score</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody className="table-border-bottom-0">
              {applications.map((application) => (
                <tr key={application.id}>
                  <td>
                    <strong>{application.name}</strong>
                  </td>
                  <td>{application.email}</td>
                  <td
                    title={
                      application.match_score
                        ? `Match Score: ${application.match_score}`
                        : 'Data not Available Application submitted as unregistered user'
                    }
                  >
                    {application.match_score ?? 'N/A'}
                  </td>
                  <td>
                    <ul className="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                      <span
                        className={`badge ${
                          application.status === 'Selected'
                            ? 'bg-label-success'
                            : application.status === 'Rejected'
                              ? 'bg-label-danger'
                              : 'bg-label-info'
                        }`}
                      >
                        {application.status}
                      </span>
                    </ul>
                  </td>
                  <td>
                    <div className="dropdown">
                      <button type="button" className="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i className="bx bx-dots-vertical-rounded"></i>
                      </button>
                      <div className="dropdown-menu">
                        <Link className="dropdown-item" to={`/view_candidates/${application.id}`}>
                          <i className="fas fa-file me-2"></i> View
                        </Link>
                        <button type="button" className="dropdown-item" onClick={() => handleDelete(application.id)}>
                          <i className="fas fa-trash me-2"></i> Delete
                        </button>
                      </div>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
      {/* End Hoverable Table rows */}
    </div>
  );
};

export default CandidateList;
