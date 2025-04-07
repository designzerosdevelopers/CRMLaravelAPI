import React, { Suspense, Fragment, lazy } from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import Loader from './components/Loader/Loader';
import AdminLayout from './layouts/AdminLayout';
import { BASE_URL } from './config/constant';

// AuthGuard component to protect routes
const AuthGuard = ({ children }) => {
  const authToken = localStorage.getItem('auth_token');
  const role = localStorage.getItem('role');
  // If no auth token or role, redirect to the sign in page ("/")
  if (!authToken || !role) {
    return <Navigate to="/" />;
  }
  return children;
};

// Recursive render function for nested routes
export const renderRoutes = (routes = []) => (
  <Suspense fallback={<Loader />}>
    <Routes>
      {routes.map((route, i) => {
        const Guard = route.guard || Fragment;
        const Layout = route.layout || Fragment;
        const Element = route.element;
        return (
          <Route
            key={i}
            path={route.path}
            element={
              <Guard>
                <Layout>{route.routes ? renderRoutes(route.routes) : <Element props={true} />}</Layout>
              </Guard>
            }
          />
        );
      })}
    </Routes>
  </Suspense>
);

// Define your routes
const routes = [
  // Public Routes (auth pages)
  {
    exact: 'true',
    path: '/',
    element: lazy(() => import('./views/auth/signin/SignIn1'))
  },
  {
    exact: 'true',
    path: '/login',
    element: lazy(() => import('./views/auth/signin/SignIn1'))
  },
  {
    exact: 'true',
    path: '/auth/signin-1',
    element: lazy(() => import('./views/auth/signin/SignIn1'))
  },
  {
    exact: 'true',
    path: '/auth/signup-1',
    element: lazy(() => import('./views/auth/signup/SignUp1'))
  },
  {
    exact: 'true',
    path: '/accept-invitation/:token',
    element: lazy(() => import('./views/invite/reg-invitation'))
  },
  // Protected Routes
  {
    path: '*',
    guard: AuthGuard, // All nested routes will be protected by AuthGuard
    layout: AdminLayout,
    routes: [
      {
        exact: 'true',
        path: '/app/dashboard/default',
        element: lazy(() => import('./views/dashboard'))
      },
      {
        exact: 'true',
        path: '/basic/button',
        element: lazy(() => import('./views/ui-elements/basic/BasicButton'))
      },
      {
        exact: 'true',
        path: '/basic/badges',
        element: lazy(() => import('./views/ui-elements/basic/BasicBadges'))
      },
      {
        exact: 'true',
        path: '/basic/breadcrumb-paging',
        element: lazy(() => import('./views/ui-elements/basic/BasicBreadcrumb'))
      },
      {
        exact: 'true',
        path: '/basic/collapse',
        element: lazy(() => import('./views/ui-elements/basic/BasicCollapse'))
      },
      {
        exact: 'true',
        path: '/basic/tabs-pills',
        element: lazy(() => import('./views/ui-elements/basic/BasicTabsPills'))
      },
      {
        exact: 'true',
        path: '/basic/typography',
        element: lazy(() => import('./views/ui-elements/basic/BasicTypography'))
      },
      {
        exact: 'true',
        path: '/forms/form-basic',
        element: lazy(() => import('./views/forms/FormsElements'))
      },
      {
        exact: 'true',
        path: '/tables/bootstrap',
        element: lazy(() => import('./views/tables/BootstrapTable'))
      },
      {
        exact: 'true',
        path: '/charts/nvd3',
        element: lazy(() => import('./views/charts/nvd3-chart'))
      },
      {
        exact: 'true',
        path: '/maps/google-map',
        element: lazy(() => import('./views/maps/GoogleMaps'))
      },
      {
        exact: 'true',
        path: '/sample-page',
        element: lazy(() => import('./views/extra/SamplePage'))
      },
      {
        exact: 'true',
        path: '/employee/index',
        element: lazy(() => import('./views/employee/index'))
      },
      {
        exact: 'true',
        path: '/employee/create',
        element: lazy(() => import('./views/employee/create'))
      },
      {
        exact: 'true',
        path: '/employee/show/:id',
        element: lazy(() => import('./views/employee/show'))
      },
      {
        exact: 'true',
        path: '/employee/edit/:id',
        element: lazy(() => import('./views/employee/edit'))
      },
      {
        exact: 'true',
        path: '/organization/index',
        element: lazy(() => import('./views/organization/index'))
      },
      {
        exact: 'true',
        path: '/organization/create',
        element: lazy(() => import('./views/organization/create'))
      },
      {
        exact: 'true',
        path: '/organization/show/:id',
        element: lazy(() => import('./views/organization/show'))
      },
      {
        exact: 'true',
        path: '/organization/edit/:id',
        element: lazy(() => import('./views/organization/edit'))
      },
     
      {
        exact: 'true',
        path: '/organization/show/:id',
        element: lazy(() => import('./views/job/show'))
      },
      {
        exact: 'true',
        path: '/organization/edit/:id',
        element: lazy(() => import('./views/job/edit'))
      },
      {
        exact: 'true',
        path: '/categories/index',
        element: lazy(() => import('./views/categories/index'))
      },

      {
        exact: 'true',
        path: '/categories/create',
        element: lazy(() => import('./views/categories/create'))
      },
      {
        exact: 'true',
        path: '/categories/edit/:id',
        element: lazy(() => import('./views/categories/edit'))
      },
      {
        exact: 'true',
        path: '/job/index',
        element: lazy(() => import('./views/job/index'))
      },

      {
        exact: 'true',
        path: '/job/create',
        element: lazy(() => import('./views/job/create'))
      },
      {
        exact: 'true',
        path: '/job/edit/:id',
        element: lazy(() => import('./views/job/edit'))
      },
      {
        exact: 'true',
        path: '/invite',
        element: lazy(() => import('./views/invite/index'))
      },

      {
        exact: 'true',
        path: '/user-permission/select-user',
        element: lazy(() => import('./views/user-permission/select-user'))
      },
      {
        exact: 'true',
        path: '/user-permission/edit',
        element: lazy(() => import('./views/user-permission/edit'))
      },
      {
        exact: 'true',
        path: '/role-permission/select-role',
        element: lazy(() => import('./views/role-permission/select-role'))
      },
      {
        exact: 'true',
        path: '/role-permission/edit',
        element: lazy(() => import('./views/role-permission/edit'))
      },
      // Fallback route for unmatched paths within the protected area
      {
        path: '*',
        exact: 'true',
        element: () => <Navigate to={BASE_URL} />
      }
    ]
  }
];

export default routes;
