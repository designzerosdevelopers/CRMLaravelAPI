import React, { useEffect, useState } from 'react';
import { Row, Col } from 'react-bootstrap';
import Card from '../../components/Card/MainCard';
import { Link } from 'react-router-dom';
import axios from 'axios';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

const OrganizationPage = () => {
  const [org, setOrg] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(true);

  // Delete organization endpoint
  const handleDelete = async (organization) => {
    if (!window.confirm('Are you sure you want to delete this organization?')) return;

    try {
      const endpoint = `/organization/${organization.id}`;
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
        setError(data.error || 'Failed to delete organization');
      } else {
        // Remove the deleted organization from state
        setOrg((prevOrg) => prevOrg.filter((o) => o.id !== organization.id));
      }
    } catch (err) {
      console.error('Error deleting organization:', err);
      setError('An unexpected error occurred');
    }
  };

  // Fetch organizations on component mount
  useEffect(() => {
    axios
      .get(`${import.meta.env.VITE_API_URL}/organization`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`
        }
      })
      .then((response) => {
        // Assuming the API returns data in response.data.data
        const fetchedOrganizations = response.data.data || [];
        setOrg(fetchedOrganizations);
        setError('');
      })
      .catch((err) => {
        setError(err.response?.data?.message || 'Error fetching organizations');
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  if (loading) return <div>Loading...</div>;
  if (error) return <div>{error}</div>;

  return (
    <React.Fragment>
      <div className="d-flex justify-content-end align-items-center mb-4">
        <Link to="/organization/create">
          <button type="button" className="btn rounded-pill btn-primary">
            Create
          </button>
        </Link>
      </div>

      <Row>
        <Col>
          <Card title="Organization List" isOption>
            <table className="table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Employees</th>
                  <th>Jobs</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {org.map((organization) => (
                  <tr key={organization.id}>
                    <td>{organization.organization_name}</td>
                    <td>{organization.employee_count}</td>
                    <td>{organization.job_count}</td>
                    <td>
                      <Link
                        className="dropdown-item"
                        to={`/organization/edit/${organization.user_id}`}
                        state={{ org: organization, orgId: organization.id }}
                      >
                        <i className="fas fa-edit me-2"></i> Edit
                      </Link>
                      <button type="button" className="dropdown-item" onClick={() => handleDelete(organization)}>
                        <i className="fas fa-trash me-2"></i> Delete
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </Card>
        </Col>
      </Row>
    </React.Fragment>
  );
};

export default OrganizationPage;
