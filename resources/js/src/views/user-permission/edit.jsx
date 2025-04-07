import React, { useEffect, useState } from 'react';
import { useSearchParams, useNavigate, useLocation } from 'react-router-dom';
import { Row, Col } from 'react-bootstrap';
import Card from '../../components/Card/MainCard';
import axios from 'axios';

const UpdatePermissions = ({ csrfToken }) => {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const location = useLocation();

  const queryUser = searchParams.get('user');
  const queryEmail = searchParams.get('email');

  const userFromState = location.state?.user;
  const emailFromState = location.state?.email;

  const user = userFromState || queryUser;
  const email = emailFromState || queryEmail;

  const userId = typeof user === 'object' ? user.user_id : Number(user);
  const isValidUserId = !isNaN(userId);

  const [allPermissions, setAllPermissions] = useState([]);
  const [loadingPermissions, setLoadingPermissions] = useState(true);
  const [errorPermissions, setErrorPermissions] = useState(null);
  const [selectedPermissions, setSelectedPermissions] = useState([]);

  // Fetch CSRF cookie on mount
  useEffect(() => {
    const getCsrfCookie = async () => {
      await fetch(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
        credentials: 'include'
      });
    };
    getCsrfCookie();
  }, []);

  useEffect(() => {
    const fetchPermissions = async () => {
      try {
        if (!isValidUserId && !email) {
          throw new Error('Valid user ID or email is required.');
        }

        const response = await axios.get(`${import.meta.env.VITE_API_URL}/user-permission`, {
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`
          },
          params: {
            user_id: isValidUserId ? userId : undefined,
            user_email: email
          }
        });

        const data = response.data.data;
        setAllPermissions(data.allpermission);
        setSelectedPermissions(data.permissions?.map((p) => p.id) || []);
      } catch (error) {
        setErrorPermissions(error.response?.data?.message || error.message);
        console.error('Error fetching permissions:', error);
      } finally {
        setLoadingPermissions(false);
      }
    };

    if (isValidUserId || email) fetchPermissions();
  }, [userId, email]); // Optimized dependencies

  if (!user) {
    return <div>No user data received. Please select a user first.</div>;
  }

  const handlePermissionChange = (permissionId) => {
    setSelectedPermissions((prev) => (prev.includes(permissionId) ? prev.filter((id) => id !== permissionId) : [...prev, permissionId]));
  };

  const handleUpdate = async () => {
    try {
      const formData = new FormData();

      // Fix: Append CSRF token properly
      formData.append('_token', csrfToken); // ✅ Include CSRF token

      // Fix: Append user ID and email (if applicable)
      formData.append('user_id', userId);
      if (email) {
        formData.append('user_email', email);
      }

      // Fix: Send permissions as an array, not a JSON string
      selectedPermissions.forEach((perm) => {
        formData.append('user_permissions[]', perm.toString()); // ✅ Convert to string & use array format
      });

      // Fix: Ensure _method is properly included
      formData.append('_method', 'PUT');

      await axios.post(`${import.meta.env.VITE_API_URL}/user-permission-set/${userId}`, formData, {
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`
        },
        withCredentials: true
      });

      alert('Permissions updated successfully!');
      navigate('/user-permission/select-user');
    } catch (error) {
      alert(`Error updating permissions: ${error.response?.data?.message || 'An unexpected error occurred'}`);
    }
  };

  return (
    <React.Fragment>
      <Row>
        <Col>
          <Card title="User and Permissions" isOption>
            <table className="table">
              <thead>
                <tr>
                  <th>NAME</th>
                  <th>EMAIL</th>
                  <th>PERMISSIONS</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <strong>{typeof user === 'object' ? user.name : 'N/A'}</strong>
                  </td>
                  <td>{email || 'N/A'}</td>
                  <td>
                    {loadingPermissions ? (
                      <div>Loading permissions...</div>
                    ) : errorPermissions ? (
                      <div className="text-danger">{errorPermissions}</div>
                    ) : (
                      allPermissions.map((permission) => (
                        <div key={permission.id}>
                          <label>
                            <input
                              type="checkbox"
                              checked={selectedPermissions.includes(permission.id)}
                              onChange={() => handlePermissionChange(permission.id)}
                            />{' '}
                            {permission.name}
                          </label>
                          <br />
                        </div>
                      ))
                    )}
                  </td>
                </tr>
              </tbody>
            </table>

            <button onClick={handleUpdate} className="btn btn-primary mt-3">
              Update Permissions
            </button>
          </Card>
        </Col>
      </Row>
    </React.Fragment>
  );
};

export default UpdatePermissions;
