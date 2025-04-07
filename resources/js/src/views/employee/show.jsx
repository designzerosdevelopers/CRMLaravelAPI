import React, { useState, useEffect } from 'react';
import { MDBCol, MDBContainer, MDBRow, MDBCard, MDBCardText, MDBCardBody, MDBCardImage, MDBTypography, MDBIcon } from 'mdb-react-ui-kit';
import { useLocation, useParams } from 'react-router-dom';
import axios from 'axios';

const EmployeeDetail = () => {
  const { id } = useParams();
  const location = useLocation();
  const initialEmp = location.state?.emp;

  const initialUsr = location.state?.emp;
  const [emp, setEmp] = useState(initialEmp || null);
  const [usr, setUsr] = useState(initialUsr || null);
  const [loading, setLoading] = useState(initialEmp ? false : true);
  const [error, setError] = useState('');

  useEffect(() => {
    // Only fetch if we don't already have emp data
    axios
      .get(`${import.meta.env.VITE_API_URL}/employee/${id}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`
        }
      })
      .then((response) => {
        console.log('API response:', response.data);
        // Adjust according to your API response structure.
        const usrData = response.data.data?.user || response.data.data;
        const employeeData = response.data?.data || response.data;

        setUsr(usrData || null);
        setEmp(employeeData || null);

        setLoading(false);
      })
      .catch((err) => {
        console.error('API error:', err);
        setError(err.response?.data?.message || 'Error fetching employee data');
        setLoading(false);
      });
  }, [id]);

  if (loading) return <div>Loading...</div>;
  if (error) return <div>{error}</div>;
  if (!emp || Object.keys(emp).length === 0) return <div>No employee data available for ID: {id}</div>;

  return (
    <section className="vh-100" style={{ backgroundColor: '#f4f5f7' }}>
      <MDBContainer className="py-5">
        <MDBRow className="justify-content-center">
          {/* Increase the column size for a wider card */}
          <MDBCol lg="10" className="mb-4">
            <MDBCard className="mb-3" style={{ borderRadius: '.5rem' }}>
              <MDBRow className="g-0">
                <MDBCol
                  md="4"
                  className="gradient-custom text-center text-white"
                  style={{
                    borderTopLeftRadius: '.5rem',
                    borderBottomLeftRadius: '.5rem'
                  }}
                >
                  <MDBCardImage
                    src={emp.image || `https://ui-avatars.com/api/?name=${encodeURIComponent(emp.name)}`}
                    alt="Employee Photo"
                    className="my-5"
                    style={{ width: '100px' }}
                    fluid
                  />
                  <MDBTypography tag="h5">{usr.name || 'N/A'}</MDBTypography>

                  <MDBCardText>{emp.current_position || 'N/A'}</MDBCardText>

                  <MDBIcon far icon="edit mb-5" />
                </MDBCol>
                <MDBCol md="8">
                  <MDBCardBody className="p-4">
                    <MDBTypography tag="h6">Employee Information</MDBTypography>
                    <hr className="mt-0 mb-4" />
                    <MDBRow className="pt-1">
                      <MDBCol size="6" className="mb-3">
                        <MDBTypography tag="h6">Email</MDBTypography>
                        <MDBCardText className="text-muted">{usr.email || 'N/A'}</MDBCardText>
                      </MDBCol>
                      <MDBCol size="6" className="mb-3">
                        <MDBTypography tag="h6">Gender</MDBTypography>
                        <MDBCardText className="text-muted">{emp.gender || 'N/A'}</MDBCardText>
                      </MDBCol>
                    </MDBRow>
                    <MDBRow className="pt-1">
                      <MDBCol size="6" className="mb-3">
                        <MDBTypography tag="h6">Address</MDBTypography>
                        <MDBCardText className="text-muted">{emp.address || 'N/A'}</MDBCardText>
                      </MDBCol>
                      <MDBCol size="6" className="mb-3">
                        <MDBTypography tag="h6">Phone</MDBTypography>
                        <MDBCardText className="text-muted">{emp.phone_number || 'N/A'}</MDBCardText>
                      </MDBCol>
                    </MDBRow>
                    <div className="d-flex justify-content-start">
                      <a href="#!">
                        <MDBIcon fab icon="facebook me-3" size="lg" />
                      </a>
                      <a href="#!">
                        <MDBIcon fab icon="twitter me-3" size="lg" />
                      </a>
                      <a href="#!">
                        <MDBIcon fab icon="instagram me-3" size="lg" />
                      </a>
                    </div>
                  </MDBCardBody>
                </MDBCol>
              </MDBRow>
            </MDBCard>
          </MDBCol>
        </MDBRow>
      </MDBContainer>
    </section>
  );
};

export default EmployeeDetail;
