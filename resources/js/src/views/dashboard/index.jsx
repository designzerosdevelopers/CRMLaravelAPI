import React from 'react';
import { Row, Col, Card, Button } from 'react-bootstrap';
import { Line, Bar, Doughnut } from 'react-chartjs-2';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  ArcElement
} from 'chart.js';

// Register Chart.js components
ChartJS.register(CategoryScale, LinearScale, BarElement, PointElement, LineElement, Title, Tooltip, Legend, ArcElement);

// Avatar Component
const Avatar = ({ style }) => (
  <div
    style={{
      width: 50,
      height: 50,
      backgroundColor: '#f8f9fa',
      borderRadius: '50%',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      boxShadow: '0 2px 8px rgba(0,0,0,0.15)',
      ...style
    }}
  >
    <span role="img" aria-label="avatar" style={{ fontSize: '24px' }}>
      👤
    </span>
  </div>
);

const DashDefault = () => {
  const role = localStorage.getItem('role');
  const roleMessages = {
    'super-admin': 'Super Admin Dashboard 🚀',
    organization: 'Organization Dashboard 📊',
    user: 'User Dashboard 🎯'
  };
  const welcomeMessage = roleMessages[role] || 'Welcome to the Dashboard! 🚀';

  // Chart Components
  const ChartWrapper = ({ children }) => <div style={{ height: 200, padding: '10px' }}>{children}</div>;

  const lineChartData = [50, 60, 45, 80, 60, 55];
  const barChartData = [12, 19, 10, 15, 9, 13];
  const doughnutData = [76, 24];

  return (
    <div className="container-fluid py-4">
      {/* Header Section */}
      <Row className="mb-4">
        {/* Left Column - Dashboard Header & Stats Cards */}
        <Col md={8}>
          {/* Main Dashboard Card */}
          <Card className="shadow-sm border-0">
            <Card.Body>
              <div className="d-flex justify-content-between align-items-center">
                <div>
                  <h1 className="fw-bold">{welcomeMessage}</h1>
                  <p className="text-muted">
                    You have achieved <span className="text-primary fw-semibold">72%</span> of your monthly goals. Keep going! 💪
                  </p>
                </div>
                <Avatar />
              </div>
            </Card.Body>
          </Card>

          {/* Small Stats Cards Below Dashboard Header */}
          <Row className="mt-3 g-3">
            {[
              { title: 'Profit', value: '$12,628', color: 'text-success' },
              { title: 'Sales', value: '$4,679', color: 'text-primary' },
              { title: 'Payments', value: '$2,456', color: 'text-info' },
              { title: 'Transactions', value: '$14,857', color: 'text-warning' }
            ].map((stat, index) => (
              <Col md={3} key={index}>
                <Card className="shadow-sm border-0 text-center">
                  <Card.Body>
                    <h6 className="fw-semibold">{stat.title}</h6>
                    <h2 className={`${stat.color} fw-bold`}>{stat.value}</h2>
                  </Card.Body>
                </Card>
              </Col>
            ))}
          </Row>
        </Col>

        {/* Right Column - Growth Chart */}
        <Col md={4}>
          <Card className="shadow-sm border-0">
            <Card.Body className="text-center">
              <h5 className="fw-semibold">Growth</h5>
              <Doughnut
                data={{
                  labels: ['Growth', 'Remaining'],
                  datasets: [{ data: doughnutData, backgroundColor: ['#198754', '#e2e3e5'] }]
                }}
                options={{ cutout: '70%' }}
              />
              <p className="text-success mt-3 fs-4 fw-bold">76%</p>
              <p className="text-muted">Company Growth</p>
            </Card.Body>
          </Card>
        </Col>
      </Row>

      {/* Revenue & Sales Charts */}
      <Row className="mb-4">
        <Col md={6}>
          <Card className="shadow-sm border-0">
            <Card.Body>
              <h5 className="fw-semibold">Total Revenue</h5>
              <ChartWrapper>
                <Bar
                  data={{
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{ data: barChartData, backgroundColor: 'rgba(25,135,84,0.7)' }]
                  }}
                  options={{ responsive: true, maintainAspectRatio: false }}
                />
              </ChartWrapper>
            </Card.Body>
          </Card>
        </Col>

        <Col md={6}>
          <Card className="shadow-sm border-0">
            <Card.Body>
              <h5 className="fw-semibold">Income vs Expenses</h5>
              <ChartWrapper>
                <Line
                  data={{
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{ data: lineChartData, borderColor: '#0d6efd', fill: false, tension: 0.3 }]
                  }}
                  options={{ responsive: true, maintainAspectRatio: false }}
                />
              </ChartWrapper>
            </Card.Body>
          </Card>
        </Col>
      </Row>

      {/* Quick Stats */}

      {/* Transactions List */}
      <Row>
        <Col md={12}>
          <Card className="shadow-sm border-0">
            <Card.Body>
              <h5 className="fw-semibold">Recent Transactions</h5>
              <ul className="list-unstyled mt-3">
                {[
                  { label: 'Send Money', amount: '-$492.6', color: 'text-danger' },
                  { label: 'Wallet Transfer', amount: '-$678.9', color: 'text-danger' },
                  { label: 'Starbucks', amount: '-$426.3', color: 'text-danger' },
                  { label: 'Amazon Refund', amount: '+$320.0', color: 'text-success' },
                  { label: 'Received Payment', amount: '+$512.4', color: 'text-success' }
                ].map((transaction, index) => (
                  <li key={index} className="d-flex justify-content-between border-bottom py-2">
                    <span>{transaction.label}</span>
                    <span className={transaction.color}>{transaction.amount}</span>
                  </li>
                ))}
              </ul>
            </Card.Body>
          </Card>
        </Col>
      </Row>
    </div>
  );
};

export default DashDefault;
