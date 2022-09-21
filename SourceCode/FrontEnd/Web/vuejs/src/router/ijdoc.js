// ========================== Doc - Tài Liệu ============================================
const DocDocList = () => import('@/views/doc/doc/Lists');
const DocDocEdit = () => import('@/views/doc/doc/Edit');
const DocDocCreate = () => import('@/views/doc/doc/Create');
const DocDocView = () => import('@/views/doc/doc/View');

let router = [
  {
    path: 'doc',
    // redirect: '/listing/',
    name: 'Doc',
    component: {
      render(c) {
        return c('router-view')
      }
    },
    children: [
      // ===================================== document - Tài liệu ===========================================
      {
        path: 'doc',
        name: 'doc-doc',
        component: DocDocList,
      },
      {
        path: 'doc/view/:id',
        name: 'doc-doc-view',
        component: DocDocView,
      },

      {
        path: 'doc/edit/:id',
        name: 'doc-doc-edit',
        component: DocDocEdit,
      },

      {
        path: 'doc/create',
        name: 'doc-doc-create',
        component: DocDocCreate,
      },
    ]
  },
];

export default router;
