import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Form, Button, Row, Col, Card, Spinner } from 'react-bootstrap';
import axios from 'axios';

const SelectRole = () => {
  const [roles, setRoles] = useState([]);
  const [selectedRole, setSelectedRole] = useState('');
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();

  // Fetch roles from the backend
  useEffect(() => {
    axios
      .get(`${import.meta.env.VITE_API_URL}/role-select`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      })
      .then((response) => {
        console.log('Fetched roles:', response.data.data);
        setRoles(response.data.data);
        setLoading(false);
      })
      .catch((error) => {
        console.error('Error fetching roles:', error);
        setLoading(false);
      });
  }, []);

  const handleSelectRole = () => {
    if (selectedRole) {
      navigate(`/role-permission/edit`, { state: { role: selectedRole } });
    } else {
      alert('Please select a Role.');
    }
  };

  if (loading) {
    return (
      <div className="container mt-4 text-center">
        <Spinner animation="border" variant="primary" />
      </div>
    );
  }

  return (
    <div className="container mt-4">
      <h3>Permission Form</h3>
      <Card>
        <Card.Header>Select Role</Card.Header>
        <Card.Body>
          <Row className="mt-3">
            <Col md={6}>
              <Form.Group>
                <Form.Label>Select Role:</Form.Label>
                <Form.Select value={selectedRole} onChange={(e) => setSelectedRole(e.target.value)}>
                  <option value="">-- Select Role --</option>
                  {roles.map((role) => (
                    <option key={role.id} value={role.id}>
                      {role.name}
                    </option>
                  ))}
                </Form.Select>
              </Form.Group>
            </Col>
          </Row>
          <Button className="mt-3" onClick={handleSelectRole}>
            Check Permission
          </Button>
        </Card.Body>
      </Card>
    </div>
  );
};

export default SelectRole;
