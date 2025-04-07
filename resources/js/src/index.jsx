import React from 'react';
import { createRoot } from 'react-dom/client';

import { ConfigProvider } from './contexts/ConfigContext';
import { UserProvider } from './contexts/UserContext'; // Import the provider

import './index.scss';
import App from './App';
import reportWebVitals from './reportWebVitals';

const container = document.getElementById('root');
const root = createRoot(container);
root.render(
  <ConfigProvider>
    <UserProvider>
      <App />
    </UserProvider>
  </ConfigProvider>
);

reportWebVitals();
