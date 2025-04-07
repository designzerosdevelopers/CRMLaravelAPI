const getMenuItems = (role) => {
  let items = [];

  if (role === 'super-admin') {
    items = [
      {
        id: 'pages',
        title: 'Pages',
        type: 'group',
        icon: 'icon-pages',
        children: [
          {
            id: 'dashboard',
            title: 'Dashboard',
            type: 'item',
            icon: 'feather icon-home',
            url: '/app/dashboard/default'
          },
          {
            id: 'auth-employee',
            title: 'Employee',
            type: 'item',
            url: '/employee/index',
            classes: 'nav-item',
            icon: 'feather icon-users'
          },
          {
            id: 'auth-organization',
            title: 'Organization',
            type: 'item',
            url: '/organization/index',
            classes: 'nav-item',
            icon: 'feather icon-users'
          },
          {
            id: 'auth-categories',
            title: 'Categories',
            type: 'item',
            url: '/categories/index',
            classes: 'nav-item',
            icon: 'feather icon-users'
          },
          {
            id: 'sample-page',
            title: 'Invite',
            type: 'item',
            url: '/invite',
            classes: 'nav-item',
            icon: 'feather icon-sidebar'
          },
          {
            id: 'menu-level',
            title: 'Permissions',
            type: 'collapse',
            icon: 'feather icon-menu',
            children: [
              {
                id: 'user-permission',
                title: 'User Permission',
                type: 'item',
                url: '/user-permission/select-user'
              },
              {
                id: 'role-permission',
                title: 'Role Permission',
                type: 'item',
                url: '/role-permission/select-role'
              }
            ]
          }
        ]
      }
    ];
  } else if (role === 'organization') {
    items = [
      {
        id: 'pages',
        title: 'Pages',
        type: 'group',
        icon: 'icon-pages',
        children: [
          {
            id: 'dashboard',
            title: 'Dashboard',
            type: 'item',
            icon: 'feather icon-home',
            url: '/app/dashboard/organization'
          },
          {
            id: 'job',
            title: 'Job',
            type: 'item',
            icon: 'feather icon-briefcase',
            url: '/job/index'
          },
          {
            id: 'employee',
            title: 'Employee',
            type: 'item',
            icon: 'feather icon-users',
            url: '/employee/index'
          },
          {
            id: 'invite',
            title: 'Invite',
            type: 'item',
            icon: 'feather icon-sidebar',
            url: '/invite'
          },
          {
            id: 'permission',
            title: 'Permissions',
            type: 'collapse',
            icon: 'feather icon-menu',
            children: [
              {
                id: 'user-permission',
                title: 'User Permission',
                type: 'item',
                url: '/user-permission/select-user'
              },
              {
                id: 'role-permission',
                title: 'Role Permission',
                type: 'item',
                url: '/role-permission/select-role'
              }
            ]
          }
        ]
      }
    ];
  } else if (role === 'employee') {
    items = [
      {
        id: 'pages',
        title: 'Pages',
        type: 'group',
        icon: 'icon-pages',
        children: [
          {
            id: 'dashboard',
            title: 'Dashboard',
            type: 'item',
            icon: 'feather icon-home',
            url: '/app/dashboard/organization'
          },
          {
            id: 'job',
            title: 'Job',
            type: 'item',
            icon: 'feather icon-briefcase',
            url: '/job/index'
          }
        ]
      }
    ];
  } else {
    items = [];
  }

  return { items };
};


export default getMenuItems;

// const menuItems = {
//   items: [
//     // {
//     //   id: 'navigation',
//     //   title: 'Navigation',
//     //   type: 'group',
//     //   icon: 'icon-navigation',
//     //   children: [
//     //     {
//     //       id: 'dashboard',
//     //       title: 'Dashboard',
//     //       type: 'item',
//     //       icon: 'feather icon-home',
//     //       url: '/app/dashboard/default'
//     //     }
//     //   ]
//     // },
//     // {
//     //   id: 'ui-element',
//     //   title: 'UI ELEMENT',
//     //   type: 'group',
//     //   icon: 'icon-ui',
//     //   children: [
//     //     {
//     //       id: 'component',
//     //       title: 'Component',
//     //       type: 'collapse',
//     //       icon: 'feather icon-box',
//     //       children: [
//     //         {
//     //           id: 'button',
//     //           title: 'Button',
//     //           type: 'item',
//     //           url: '/basic/button'
//     //         },
//     //         {
//     //           id: 'badges',
//     //           title: 'Badges',
//     //           type: 'item',
//     //           url: '/basic/badges'
//     //         },
//     //         {
//     //           id: 'breadcrumb',
//     //           title: 'Breadcrumb & Pagination',
//     //           type: 'item',
//     //           url: '/basic/breadcrumb-paging'
//     //         },
//     //         {
//     //           id: 'collapse',
//     //           title: 'Collapse',
//     //           type: 'item',
//     //           url: '/basic/collapse'
//     //         },
//     //         {
//     //           id: 'tabs-pills',
//     //           title: 'Tabs & Pills',
//     //           type: 'item',
//     //           url: '/basic/tabs-pills'
//     //         },
//     //         {
//     //           id: 'typography',
//     //           title: 'Typography',
//     //           type: 'item',
//     //           url: '/basic/typography'
//     //         }
//     //       ]
//     //     }
//     //   ]
//     // },
// {
//   id: 'ui-forms',
//   title: 'FORMS & TABLES',
//   type: 'group',
//   icon: 'icon-group',
//   children: [
//     {
//       id: 'forms',
//       title: 'Form Elements',
//       type: 'item',
//       icon: 'feather icon-file-text',
//       url: '/forms/form-basic'
//     },
//     {
//       id: 'table',
//       title: 'Table',
//       type: 'item',
//       icon: 'feather icon-server',
//       url: '/tables/bootstrap'
//     }
//   ]
// },
//     // {
//     //   id: 'chart-maps',
//     //   title: 'Chart & Maps',
//     //   type: 'group',
//     //   icon: 'icon-charts',
//     //   children: [
//     //     {
//     //       id: 'charts',
//     //       title: 'Charts',
//     //       type: 'item',
//     //       icon: 'feather icon-pie-chart',
//     //       url: '/charts/nvd3'
//     //     },
//     //     {
//     //       id: 'maps',
//     //       title: 'Maps',
//     //       type: 'item',
//     //       icon: 'feather icon-map',
//     //       url: '/maps/google-map'
//     //     }
//     //   ]
//     // },
//     {
//       id: 'pages',
//       title: 'Pages',
//       type: 'group',
//       icon: 'icon-pages',
//       children: [
//         {
//           id: 'dashboard',
//           title: 'Dashboard',
//           type: 'item',
//           icon: 'feather icon-home',
//           url: '/app/dashboard/default'
//         },

//         {
//           id: 'auth',
//           title: 'Employee',
//           type: 'item',
//           url: '/employee/index',
//           classes: 'nav-item',
//           icon: 'feather icon-users'
//         },

//         {
//           id: 'auth',
//           title: 'organization',
//           type: 'item',
//           url: '/organization/index',
//           classes: 'nav-item',
//           icon: 'feather icon-users'
//         },
//         {
//           id: 'auth',
//           title: 'categories',
//           type: 'item',
//           url: '/categories/index',
//           classes: 'nav-item',
//           icon: 'feather icon-users'
//         },
//         {
//           id: 'sample-page',
//           title: 'Invite',
//           type: 'item',
//           url: '/invite',
//           classes: 'nav-item',
//           icon: 'feather icon-sidebar'
//         },
//         // {
//         //   id: 'documentation',
//         //   title: 'Documentation',
//         //   type: 'item',
//         //   icon: 'feather icon-book',
//         //   classes: 'nav-item',
//         //   url: 'https://codedthemes.gitbook.io/datta/',
//         //   target: true,
//         //   external: true
//         // },
//         {
//           id: 'menu-level',
//           title: 'permissions',
//           type: 'collapse',
//           icon: 'feather icon-menu',
//           children: [
//             {
//               id: 'User permission',
//               title: 'User permission',
//               type: 'item',
//               url: '/user-permission/select-user'
//             },
//             {
//               id: 'Role permission',
//               title: 'Role permission',
//               type: 'item',
//               url: '/role-permission/select-role'
//             }
//           ]
//         }
//         // {
//         //   id: 'disabled-menu',
//         //   title: 'Disabled Menu',
//         //   type: 'item',
//         //   url: '#',
//         //   classes: 'nav-item disabled',
//         //   icon: 'feather icon-power'
//         // }
//       ]
//     }
//   ]
// };

// export default menuItems;
