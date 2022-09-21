// Customer
// ========================== Customer - Khách hàng ============================================
const CustomerList = () => import('@/views/ijcustomer/customer/Lists');
const CustomerEdit = () => import('@/views/ijcustomer/customer/Edit');
const CustomerCreate = () => import('@/views/ijcustomer/customer/Create');
const CustomerView = () => import('@/views/ijcustomer/customer/View');
// ========================== Opportunity - Cơ hội ============================================
const CustomerOpportunityList = () => import('@/views/ijcustomer/opportunity/Lists');
const CustomerOpportunityEdit = () => import('@/views/ijcustomer/opportunity/Edit');
const CustomerOpportunityCreate = () => import('@/views/ijcustomer/opportunity/Create');
const CustomerOpportunityView = () => import('@/views/ijcustomer/opportunity/View');
// ========================== Customer_sales_trans - Giao dịch bán hàng  ============================
const CustomerSalesTransList = () => import('@/views/ijcustomer/salestrans/Lists');
const CustomerSalesTransEdit = () => import('@/views/ijcustomer/salestrans/Edit');
const CustomerSalesTransCreate = () => import('@/views/ijcustomer/salestrans/Create');
const CustomerSalesTransView = () => import('@/views/ijcustomer/salestrans/View');

let router = [
    {
        path: 'customer',
        // redirect: '/customer/',
        name: 'Customer',
        component: {
            render(c) {
                return c('router-view')
            }
        },
        children: [

            // ===================================== Customer - Khách hàng ===========================================
            {
                path: 'customer',
                name: 'customer-customer',
                component: CustomerList,
            },

            {
                path: 'customer/view/:id',
                name: 'customer-customer-view',
                component: CustomerView,
            },

            {
                path: 'customer/edit/:id',
                name: 'customer-customer-edit',
                component: CustomerEdit,
            },

            {
                path: 'customer/create',
                name: 'customer-customer-create',
                component: CustomerCreate,
            },
          // ===================================== opportunity - cơ hội ===========================================
            {
                path: 'opportunity',
                name: 'customer-opportunity',
                component: CustomerOpportunityList,
            },

            {
                path: 'opportunity/view/:id',
                name: 'customer-opportunity-view',
                component: CustomerOpportunityView,
            },

            {
                path: 'opportunity/edit/:id',
                name: 'customer-opportunity-edit',
                component: CustomerOpportunityEdit,
            },

            {
                path: 'opportunity/create',
                name: 'customer-opportunity-create',
                component: CustomerOpportunityCreate,
            },
            // ===================================== Customer_sales_trans - Giao dịch bán hàng  =======================
            {
              path: 'salestrans',
              name: 'customer-sales-trans',
              component: CustomerSalesTransList,
            },
            {
              path: 'salestrans/view/:id',
              name: 'customer-sales-trans-view',
              component: CustomerSalesTransView,
            },
            {
              path: 'salestrans/edit/:id',
              name: 'customer-sales-trans-edit',
              component: CustomerSalesTransEdit,
            },
            {
              path: 'salestrans/create',
              name: 'customer-sales-trans-create',
              component: CustomerSalesTransCreate,
            },
            //

        ]
    },
];

export default router;
