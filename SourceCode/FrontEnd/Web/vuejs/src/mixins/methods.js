import ApiService from '@/services/api.service';
const methods = {
  // notification
  $_storeTaskNotice(TaskID, TypeAction){
    let requestData = {
      method: 'post',
      url: 'extensions/api/notice/store-task-notice',
      data: {
        TaskID: TaskID,
        TypeAction: TypeAction
      },
    };

    ApiService.customRequest(requestData).then((response) => {
      let responseData = response.data;
      if (responseData.status === 1) {
        if (responseData.data.UserReceives.length) {
          socket.emit('notify', {
            Notification: responseData.data.data,
            UserReceives: responseData.data.UserReceives
          });
        }
      }
    }, (error) => {
      console.log(error);
    });
  },

  $_storeTaskDataflowNotice(TaskID, TypeAction){
    let requestData = {
      method: 'post',
      url: 'extensions/api/notice/store-task-dataflow-notice',
      data: {
        TaskID: TaskID,
        TypeAction: TypeAction
      },
    };

    ApiService.customRequest(requestData).then((response) => {
      let responseData = response.data;
      if (responseData.status === 1) {
        if (responseData.data.UserReceives.length) {
          socket.emit('notify', {
            Notification: responseData.data.data,
            UserReceives: responseData.data.UserReceives
          });
        }
      }
    }, (error) => {
      console.log(error);
    });
  },

  $_onToggleDropdownSubMenu(event){
    __.onToggleDropdownSubMenu(event);
  },
};

export default methods;
