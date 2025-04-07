import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Form, Button, Row, Col, Card, Spinner } from 'react-bootstrap';
import axios from 'axios';

const SelectUser = () => {
  const [users, setUsers] = useState([]);
  const [selectedUser, setSelectedUser] = useState('');
  const [email, setEmail] = useState('');
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();

  // Fetch users on component mount
  useEffect(() => {
    axios
      .get(`${import.meta.env.VITE_API_URL}/user-select`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`
        }
      })
      .then((response) => {
        console.log('API Response:', response.data.data);
        // response.data.data is the array of objects with user_id, email, and (optionally) name
        setUsers(response.data.data);
        setLoading(false);
      })
      .catch((error) => {
        console.error('Error fetching users data:', error);
        setLoading(false);
      });
  }, []);

  // When user selects from the dropdown, update `selectedUser`
  // and optionally auto-fill the email field
  const handleSelectChange = (e) => {
    const userId = Number(e.target.value); // Convert to number
    setSelectedUser(userId);

    // Find the selected user using numeric comparison
    const foundUser = users.find((user) => user.user_id === userId);

    if (foundUser) {
      setEmail(foundUser.email);
    }
  };

  // On button click, navigate to new route if fields are filled
  const handleSelectUser = () => {
    if (selectedUser && email) {
      // Find the complete user object to pass along using a different variable name
      const selectedUserObj = users.find((user) => user.user_id === selectedUser);
      // Pass the full user object via React Router state
      navigate(`/user-permission/edit`, { state: { user: selectedUserObj } });
    } else {
      alert('Please select a user and enter an email.');
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
        <Card.Header>Select User</Card.Header>
        <Card.Body>
          <Row className="mt-3">
            <Col md={6}>
              <Form.Group controlId="userSelect">
                <Form.Label>Select User:</Form.Label>
                <Form.Select value={selectedUser} onChange={handleSelectChange}>
                  <option value="">-- Select User --</option>
                  {users.map((user) => (
                    <option key={user.user_id} value={user.user_id}>
                      {user.name ? user.name : user.email}
                    </option>
                  ))}
                </Form.Select>
              </Form.Group>
            </Col>
            <Col md={6}>
              <Form.Group controlId="userEmail">
                <Form.Label>Input User Email:</Form.Label>
                <Form.Control
                  type="email"
                  placeholder="Find Email to Set Permission"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                />
              </Form.Group>
            </Col>
          </Row>
          <Button className="mt-3" onClick={handleSelectUser}>
            Select User
          </Button>
        </Card.Body>
      </Card>
    </div>
  );
};

export default SelectUser;
