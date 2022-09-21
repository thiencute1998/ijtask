// Apps
// ========================== Chat - Trò chuyện ============================================
const AppsChatMessage = () => import('@/views/apps/chat/ChatMessage');
const AppsChatSocial = () => import('@/views/apps/chat/ChatSocial');

// ========================== Notification - Thông báo =====================================
const AppsNotification = () => import('@/views/apps/notification/Lists');

// ========================== Email =====================================
const Compose = () => import('@/views/apps/email/Compose')
const Inbox = () => import('@/views/apps/email/Inbox')
const Message = () => import('@/views/apps/email/Message');

let router = [
    {
        path: 'apps',
        name: 'Apps',
        component: {
            render(c) {
                return c('router-view')
            }
        },
        children: [
         // ===================================== Chat - messenger ===========================================
          {
            path: 'chat/message',
            name: 'apps-chat-message',
            component: AppsChatMessage,
          },

          // ===================================== Chat - social ===========================================
          {
            path: 'chat/social',
            name: 'apps-chat-social',
            component: AppsChatSocial,
          },

          // ===================================== Notification - lists ===========================================
          {
            path: 'notification',
            name: 'apps-notification',
            component: AppsNotification,
          },

          // ===================================== Email ===========================================
          {
            path: 'email',
            redirect: '/apps/email/inbox',
            name: 'Email',
            component: {
              render (c) { return c('router-view') }
            },
            children: [
              {
                path: 'compose',
                name: 'Compose',
                component: Compose
              },
              {
                path: 'inbox',
                name: 'Inbox',
                component: Inbox
              },
              {
                path: 'message',
                name: 'Message',
                component: Message
              }
            ]
          }
        ]
    },
];

export default router;
