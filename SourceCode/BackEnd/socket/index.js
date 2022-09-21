let express = require('express');
let app = express();
let path = require('path');
let server = require('http').createServer(app);
let io = require('socket.io')(server);
let port = process.env.PORT || 3003;
let _ = require('lodash');

server.listen(port, () => {
  console.log('Server listening at port %d', port);
});

// Routing
app.use(express.static(path.join(__dirname, 'public')));

// Chatroom

let numUsers = 0;
let usersConnected = [];

io.on('connection', (socket) => {
  let addedUser = false;

  // when the client emits 'new message', this listens and executes
  socket.on('new message', (data) => {
    // we tell the client to execute 'new message'
    if (!data.Social) {
      data.Content.GroupType = data.GroupType;
      if (data.GroupType === 1) {
        let clients = _.filter(usersConnected, ['UserID', data.UserID]);
        if (clients.length) {
          _.forEach(clients, function (client, key) {
            io.to(client.SocketID).emit('new message', data.Content);
          });
        }

        let currentUser = _.find(usersConnected, ['SocketID', socket.id]);
        if (currentUser) {
          let currentClients = _.filter(usersConnected, ['UserID', currentUser.UserID]);
          if (currentClients.length) {
            _.forEach(currentClients, function (currentClient, key) {
              if (currentClient.SocketID !== socket.id) {
                io.to(currentClient.SocketID).emit('new message', data.Content);
              }
            });
          }
        }

      } else {
        if (data.HideGroupChat) {
          data.Members = _.uniqBy(data.Members, 'UserID');
          data.Content.HideGroupChat = 1;
          _.forEach(data.Members, function (member, key) {
            let clients = _.filter(usersConnected, ['UserID', member.UserID]);
            _.forEach(clients, function (client, key) {
              if (client && (client.SocketID !== socket.id)) {
                io.to(client.SocketID).emit('new message', data.Content);
              }
            });
          });
        } else {
          socket.to('group ' + data.GroupID).broadcast.emit('new message', data.Content);
        }
        // if (data.isDataflow) {
        //   data.members = _.uniqBy(data.members, 'UserID');
        //   _.forEach(data.members, function (member, key) {
        //     let clients = _.filter(usersConnected, ['UserID', member.UserID]);
        //     _.forEach(clients, function (client, key) {
        //       if (client && (client.SocketID !== socket.id)) {
        //         io.to(client.SocketID).emit('new message', data.Content);
        //       }
        //     });
        //   });
        // }else {
        //   socket.to('group ' + data.GroupID).broadcast.emit('new message', data.Content);
        // }
      }
    } else {
      // social
      _.forEach(usersConnected, function (client, key) {
        if (client.UserID === data.Content.UserID) {
          return;
        }
        io.to(client.SocketID).emit('new message', data.Content);
      });
    }
  });

  socket.on('edit message', (data) => {
    if (data.GroupType === 1) {
      let clients = _.filter(usersConnected, ['UserID', data.UserID]);
      if (clients.length) {
        _.forEach(clients, function (client, key) {
          io.to(client.SocketID).emit('edit message', data.Content);
        });
      }
    } else {
      socket.to('group ' + data.GroupID).broadcast.emit('edit message', data.Content);
    }
  });

  socket.on('delete message', (data) => {
    data.Content.GroupType = data.GroupType;
    if (data.GroupType === 1) {
      let clients = _.filter(usersConnected, ['UserID', data.UserID]);
      if (clients.length) {
        _.forEach(clients, function (client, key) {
          io.to(client.SocketID).emit('delete message', data.Content);
        });
      }
    } else {
      socket.to('group ' + data.GroupID).broadcast.emit('delete message', data.Content);
    }
  });

  // when the client emits 'seen message', this listens and executes
  socket.on('seen message', (data) => {
    if (data.GroupType === 1) {
      let clients = _.filter(usersConnected, ['UserID', data.UserIDSocket]);
      if (clients.length) {
        _.forEach(clients, function (client, key) {
          io.to(client.SocketID).emit('seen message', data);
        });
      }
    } else {
      socket.to('group ' + data.GroupID).broadcast.emit('seen message', data);
    }
  });

  socket.on('read message', (data) => {
    if (data.UserID) {
      let clients = _.filter(usersConnected, ['UserID', data.UserID]);
      _.forEach(clients, function (client, key) {
        if (client && client.SocketID !== socket.id) {
          io.to(client.SocketID).emit('read message', data);
        }
      });
      socket.emit('read message', data);
    }
  });

  // when the client emits 'no new message', this listens and executes
  socket.on('no new message', (data) => {
    if (data.UserID) {
      let clinets = _.filter(usersConnected, ['UserID', data.UserID]);
      _.forEach(clinets, function (client, key) {
        if (client && client.SocketID !== socket.id) {
          io.to(client.SocketID).emit('no new message', data);
        }
      });
    }
  });

  // when the client emits 'add user', this listens and executes
  socket.on('add user', (user) => {
    if (addedUser || !user) return;

    // we store the username in the socket session for this client
    socket.EmployeeID = user.EmployeeID;
    socket.UserID = user.UserID;
    socket.EmployeeName = user.EmployeeName;
    ++numUsers;
    addedUser = true;

    usersConnected.push({
      UserID: user.UserID,
      EmployeeID: user.EmployeeID,
      EmployeeName: user.EmployeeName,
      SocketID: socket.id
    });

    // sending to the client
    socket.emit('user joined', {
      numUsers: numUsers,
      usersConnected: usersConnected,
      UserID: user.UserID,
      EmployeeID: user.EmployeeID,
      EmployeeName: user.EmployeeName,
    });

    // sending to all clients except sender
    socket.broadcast.emit('user joined', {
      numUsers: numUsers,
      usersConnected: usersConnected,
      UserID: user.UserID,
      EmployeeID: user.EmployeeID,
      EmployeeName: user.EmployeeName,
    });

  });

  // when the client emits 'join room', this listens and executes
  socket.on('join room', (groups) => {
    _.forEach(groups, function (group, key) {
      socket.join('group ' + group.GroupID);
    });
  });

  // when the client emits 'new group', this listens and executes
  socket.on('new group', (data) => {
    socket.join('group ' + data.group.GroupID);
    data.members = _.uniqBy(data.members, 'UserID');
    _.forEach(data.members, function (member, key) {
      let clients = _.filter(usersConnected, ['UserID', member.UserID]);
      _.forEach(clients, function (client, key) {
        // if (client && client.SocketID !== socket.id) {
        if (client) {
          io.to(client.SocketID).emit('new group', data);
        }
      });
    });
  });

  // when the client emits 'add members', this listens and executes
  socket.on('add members', (data) => {
    _.forEach(data.members, function (member, key) {
      let socketsConnected = _.filter(usersConnected, ['UserID', member.UserID]);
      _.forEach(socketsConnected, function (socketConnected, key) {
        io.sockets.connected[socketConnected.SocketID].join('group ' + data.GroupID);
        io.to(socketConnected.SocketID).emit('join group', data);
      });
    });
    socket.to('group ' + data.GroupID).broadcast.emit('add members', data);
  });

  socket.on('remove member', (data) => {
    if (data.member) {
      let socketsConnected = _.filter(usersConnected, ['UserID', data.member.UserID]);
      _.forEach(socketsConnected, function (socketConnected, key) {
        io.sockets.connected[socketConnected.SocketID].leave('group ' + data.GroupID);
        io.to(socketConnected.SocketID).emit('left group', data);
      });
    }
    socket.to('group ' + data.GroupID).broadcast.emit('remove member', data);
  });

  // when the client emits 'delete group', this listens and executes
  socket.on('delete group', (data) => {
    socket.to('group ' + data.GroupID).broadcast.emit('delete group', data);
  });

  // when the client emits 'typing', we broadcast it to others
  socket.on('typing', () => {
    socket.broadcast.emit('typing', {
      username: socket.username
    });
  });

  // when the client emits 'stop typing', we broadcast it to others
  socket.on('stop typing', () => {
    socket.broadcast.emit('stop typing', {
      username: socket.username
    });
  });

  // when the client emits 'user online', we broadcast it to others
  socket.on('user online', () => {
    socket.emit('user online', usersConnected);
  });

  // when the user logout app this
  socket.on('user left', () => {
    if (addedUser) {
      socket.broadcast.emit('user left', {
        UserID: socket.UserID,
        EmployeeID: socket.EmployeeID,
        EmployeeName: socket.EmployeeName,
        SocketID: socket.id,
        numUsers: numUsers
      });
      // _.remove(usersConnected, ['SocketID', socket.id]);
      _.remove(usersConnected, ['UserID', socket.UserID]);
      addedUser = false;
    }
  });

  // when the client emits 'notify', we broadcast it to others
  socket.on('notify', (data) => {
    _.forEach(data.UserReceives, function (UserID, key) {
      let clients = _.filter(usersConnected, ['UserID', UserID]);
      _.forEach(clients, function (client, key) {
        io.to(client.SocketID).emit('notify', data.Notification);
      });
    });
  });

  // when the user disconnects.. perform this
  socket.on('disconnect', () => {
    if (addedUser) {
      --numUsers;
      // echo globally that this client has left
      socket.broadcast.emit('user left', {
        UserID: socket.UserID,
        EmployeeID: socket.EmployeeID,
        EmployeeName: socket.EmployeeName,
        SocketID: socket.id,
        numUsers: numUsers
      });
      _.remove(usersConnected, ['SocketID', socket.id]);
      addedUser = false;
    }

  });

});

