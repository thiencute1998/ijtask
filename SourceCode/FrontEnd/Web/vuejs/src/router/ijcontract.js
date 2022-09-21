// Document
// ========================== Item - Hàng hóa dịch vụ ============================================
// const ListingItemList = () => import('@/views/ijlisting/item/Lists');
// const ListingItemEdit = () => import('@/views/ijlisting/item/Edit');
// const ListingItemCreate = () => import('@/views/ijlisting/item/Create');
// const ListingItemDetail = () => import('@/views/ijlisting/item/Detail');


let router = [
  {
    path: 'doc',
    name: 'Doc',
    component: {
      render(c) {
        return c('router-view')
      }
    },
    children: [
      // ===================================== Item - Hàng hóa dịch vụ ===========================================
      // {
      //     path: 'item',
      //     name: 'listing-item',
      //     component: ListingItemList,
      // }

    ]
  },
];

export default router;
