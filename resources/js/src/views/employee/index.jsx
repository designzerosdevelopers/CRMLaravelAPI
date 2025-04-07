import React, { useEffect, useState } from 'react';

import { Row, Col } from 'react-bootstrap';
import Card from '../../components/Card/MainCard';

import { Link } from 'react-router-dom';

import PageTitle from '../../components/PageTitle';
import axios from 'axios';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

const EmployeeIndexPage = ({ creator, successMessage }) => {
  const [emps, setEmps] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    axios
      .get(`${import.meta.env.VITE_API_URL}/employee`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`
        }
      })
      .then((response) => {
        // Assuming the API returns data in response.data.data or response.data
        const fetchedEmployees = Array.isArray(response.data.data) ? response.data.data : Array.isArray(response.data) ? response.data : [];
        // console.log('Response data:', fetchedEmployees);
        setEmps(fetchedEmployees);
        setError('');
      })
      .catch((err) => {
        setError(err.response?.data?.message || 'Error fetching employees');
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  // Handle deletion of an employee.
  const handleDelete = async (emp) => {
    if (!window.confirm('Are you sure you want to delete this employee?')) return;

    try {
      const endpoint = creator ? `/org-employee-destroy/${emp.user_id}/${emp.creator_id}` : `/employee/${emp.user_id}`;
      const url = `${import.meta.env.VITE_API_URL}${endpoint}`;

      const response = await fetch(url, {
        method: 'DELETE',
        credentials: 'include', // include cookies for authentication
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
          'Content-Type': 'application/json'
        }
      });

      if (!response.ok) {
        const data = await response.json();
        setError(data.error || 'Failed to delete employee');
      } else {
        // Remove the deleted employee from state
        setEmps(emps.filter((e) => e.user_id !== emp.user_id));
      }
    } catch (err) {
      console.error('Error deleting employee:', err);
      setError('An unexpected error occurred');
    }
  };

  if (loading) {
    return <div>Loading...</div>;
  }

  return (
    <React.Fragment>
      <div className="container-xxl flex-grow-1 container-p-y">
        <div className="d-flex justify-content-end align-items-center mb-4">
          {creator ? (
            <Link to={`/org-employee-create/${creator}`}>
              <button type="button" className="btn rounded-pill btn-primary">
                Create
              </button>
            </Link>
          ) : (
            <Link to="/employee/create">
              <button type="button" className="btn rounded-pill btn-primary">
                Create
              </button>
            </Link>
          )}
        </div>

        {successMessage && <div className="alert alert-success">{successMessage}</div>}
        {error && <div className="alert alert-danger">{error}</div>}

        <Row>
          <Col>
            <Card title="Employee List" isOption>
              <div className="table-responsive text-nowrap">
                <table className="table table-hover">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Email</th>
                      <th>User id</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody className="table-border-bottom-0">
                    {emps.length > 0 ? (
                      emps.map((emp) => {
                        // Use the nested user object directly.
                        const user = emp.user;
                        return (
                          <tr key={emp.user_id}>
                            <td>
                              <i className="fab fa-angular fa-lg text-danger me-3"></i> {user ? user.name : 'N/A'}
                            </td>
                            <td>{user ? user.email : 'N/A'}</td>
                            <td>
                              <ul className="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                <span className="badge bg-label-primary me-1 text-dark">{emp.user_id}</span>
                              </ul>
                            </td>
                            <td>
                              <div className="dropdown">
                                <button type="button" className="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                  <i className="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div className="dropdown-menu">
                                  <Link className="dropdown-item" to={`/employee/show/${emp.user_id}`} state={{ emp }}>
                                    <i className="fas fa-file me-2"></i> View
                                  </Link>

                                  {creator ? (
                                    <>
                                      <Link
                                        className="dropdown-item"
                                        to={`/employee/edit/${emp.user_id}`}
                                        state={{ user: emp.user, employee: emp, orgId: creator }}
                                      >
                                        <i className="fas fa-edit me-2"></i> Edit
                                      </Link>

                                      <button type="button" className="dropdown-item" onClick={() => handleDelete(emp)}>
                                        <i className="fas fa-trash me-2"></i> Delete
                                      </button>
                                    </>
                                  ) : (
                                    <>
                                      <Link className="dropdown-item" to={`/employee/edit/${emp.user_id}`}>
                                        <i className="fas fa-edit me-2"></i> Edit
                                      </Link>
                                      <button type="button" className="dropdown-item" onClick={() => handleDelete(emp)}>
                                        <i className="fas fa-trash me-2"></i> Delete
                                      </button>
                                    </>
                                  )}
                                </div>
                              </div>
                            </td>
                          </tr>
                        );
                      })
                    ) : (
                      <tr>
                        <td colSpan="4" className="text-center">
                          No employees found.
                        </td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
            </Card>
          </Col>
        </Row>
      </div>
    </React.Fragment>
  );
};

export default EmployeeIndexPage;
