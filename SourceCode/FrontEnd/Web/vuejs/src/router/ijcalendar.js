// Calendar
const Calendar = () => import('@/views/calendar/Calendar');

let router = [
    {
      path: 'calendar',
      name: 'Calendar',
      redirect: 'calendar/calendar',
      component: {
        render (c) { return c('router-view') }
      },
      // component: {
      //   Calendar
      // },
      children: [
       // ===================================== Chat - messenger ===========================================
        {
          path: 'calendar',
          name: 'calendar-calendar',
          component: Calendar,
        }
      ]
    },
];

export default router;
