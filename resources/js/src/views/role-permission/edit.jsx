import React, { useEffect, useState } from 'react';
import { useSearchParams, useNavigate, useLocation } from 'react-router-dom';
import { Row, Col } from 'react-bootstrap';
import Card from '../../components/Card/MainCard';
import axios from 'axios';

const UpdatePermissions = ({ csrfToken }) => {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const location = useLocation();

  // Get role and email from state or query params
  const queryRole = searchParams.get('role');
  const queryEmail = searchParams.get('email');

  const roleFromState = location.state?.role;
  const emailFromState = location.state?.email;

  const role = roleFromState || queryRole;
  const email = emailFromState || queryEmail;

  // Ensure roleId is a valid number
  const roleId = role && typeof role === 'object' ? role.role_id : Number(role);
  const isValidRoleId = roleId && !isNaN(roleId);

  // State variables
  const [allPermissions, setAllPermissions] = useState([]);
  const [roleData, setRoleData] = useState(null); // Changed from [] to null
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

  // Fetch permissions based on role or user
  useEffect(() => {
    console.log('Fetching role data for:', roleId || email);

    const fetchPermissions = async () => {
      try {
        if (!isValidRoleId && !email) {
          throw new Error('Valid role ID or email is required.');
        }

        let url, data;
        if (isValidRoleId) {
          url = `${import.meta.env.VITE_API_URL}/role-permission`;
          data = { role_id: roleId };
        } else {
          url = `${import.meta.env.VITE_API_URL}/user-permission`;
          data = { user_email: email };
        }

        const response = await axios.post(url, data, {
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`
          },
          withCredentials: true
        });

        const responseData = response.data.data;
        console.log('Full API Response:', response.data);
        console.log('Extracted Role Data:', responseData.role);

        // ✅ Fixed: Assign only the role object, not the whole response
        setRoleData(responseData.role);
        setAllPermissions(responseData.allpermission);
        setSelectedPermissions(responseData.permissions?.map((p) => p.id) || []);
      } catch (error) {
        setErrorPermissions(error.response?.data?.message || error.message);
        console.error('Error fetching permissions:', error);
      } finally {
        setLoadingPermissions(false);
      }
    };

    if (isValidRoleId || email) fetchPermissions();
  }, [roleId, email, csrfToken, isValidRoleId]);

  // Handle permission selection
  const handlePermissionChange = (permissionId) => {
    setSelectedPermissions((prev) =>
      prev.includes(permissionId) ? prev.filter((id) => id !== permissionId) : [...prev, permissionId]
    );
  };

  // Handle permissions update
  const handleUpdate = async () => {
    try {
      const formData = new FormData();
      formData.append('_token', csrfToken);
      formData.append('_method', 'PUT');

      let url, permissionKey;
      if (isValidRoleId) {
        url = `${import.meta.env.VITE_API_URL}/role-permission-set/${roleId}`;
        permissionKey = 'role_permissions[]';
      } else {
        url = `${import.meta.env.VITE_API_URL}/user-permission-set/${email}`;
        permissionKey = 'user_permissions[]';
      }

      selectedPermissions.forEach((perm) => {
        formData.append(permissionKey, perm.toString());
      });

      await axios.post(url, formData, {
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`
        },
        withCredentials: true
      });

      alert('Permissions updated successfully!');
      navigate('/role-permission/select-role');
    } catch (error) {
      alert(`Error updating permissions: ${error.response?.data?.message || 'An unexpected error occurred'}`);
    }
  };

  // Show error if no role or email is provided
  if (!role && !email) {
    return <div>No role or user data received. Please select a role or user first.</div>;
  }

  return (
    <React.Fragment>
      <Row>
        <Col>
          <Card title="Permissions Management" isOption>
            {loadingPermissions ? (
              <div>Loading permissions...</div>
            ) : errorPermissions ? (
              <div className="text-danger">{errorPermissions}</div>
            ) : (
              <table className="table">
                <thead>
                  <tr>
                    <th>{isValidRoleId ? 'ROLE' : 'USER'}</th>
                    <th>PERMISSIONS</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <strong>{roleData?.name || "No Role Found"}</strong>
                    </td>
                    <td>
                      {allPermissions.map((permission) => (
                        <div key={permission.id}>
                          <label>
                            <input
                              type="checkbox"
                              checked={selectedPermissions.includes(permission.id)}
                              onChange={() => handlePermissionChange(permission.id)}
                            />
                            {' ' + permission.name}
                          </label>
                        </div>
                      ))}
                    </td>
                  </tr>
                </tbody>
              </table>
            )}

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
