<template>
  <div class="animated fadeIn">
    <div class="email-app mb-4">
      <nav>
        <router-link to="./compose" class="btn btn-danger btn-block">Soạn thư</router-link>
        <ul class="nav">
          <li class="nav-item">
            <router-link class="nav-link" to="./inbox"><i class="fa fa-inbox"></i>Hộp thư đến<b-badge variant="danger">4</b-badge></router-link>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#"><i class="fa fa-star"></i> Có gắn dấu sao</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#"><i class="fa fa-rocket"></i> Gửi</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#"><i class="fa fa-trash-o"></i> Thùng rác</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#"><i class="fa fa-bookmark"></i> Quan trọng<b-badge variant="info">5</b-badge></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#"><i class="fa fa-inbox"></i> Spam <b-badge variant="warning">42</b-badge></a>
          </li>
        </ul>
      </nav>
      <main class="inbox">
        <div class="toolbar">
          <div class="btn-group mr-1">
            <b-button variant="light"><span class="fa fa-envelope"></span></b-button>
            <b-button variant="light"><span class="fa fa-star"></span></b-button>
            <b-button variant="light"><span class="fa fa-star-o"></span></b-button>
            <b-button variant="light"><span class="fa fa-bookmark-o"></span></b-button>
          </div>
          <div class="btn-group mr-1">
            <b-button variant="light"><span class="fa fa-mail-reply"></span></b-button>
            <b-button variant="light"><span class="fa fa-mail-reply-all"></span></b-button>
            <b-button variant="light"><span class="fa fa-mail-forward"></span></b-button>
          </div>
          <b-button variant="light"><span class="fa fa-trash-o"></span></b-button>
          <b-dropdown class="ml-1" variant="light">
            <template slot="button-content">
              <span class="fa fa-tags"></span>
            </template>
            <b-dropdown-item href="#">add label <b-badge variant="danger"> Home</b-badge></b-dropdown-item>
            <b-dropdown-item href="#">add label <b-badge variant="info"> Job</b-badge></b-dropdown-item>
            <b-dropdown-item href="#">add label <b-badge variant="success"> Clients</b-badge></b-dropdown-item>
            <b-dropdown-item href="#">add label <b-badge variant="warning"> News</b-badge></b-dropdown-item>
          </b-dropdown>
          <div class="btn-group float-right">
            <b-button variant="light"><span class="fa fa-chevron-left"></span></b-button>
            <b-button variant="light"><span class="fa fa-chevron-right"></span></b-button>
          </div>
        </div>

        <ul class="messages">
          <li class="message unread">
            <router-link to="./message">
              <div class="actions">
                <span class="action"><i class="fa fa-square-o"></i></span>
                <span class="action"><i class="fa fa-star-o"></i></span>
              </div>
              <div class="header">
                <span class="from">Lukasz Holeczek</span>
                <span class="date"><span class="fa fa-paper-clip"></span> Hôm nay, 3:47 PM</span>
              </div>
              <div class="title">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit.
              </div>
              <div class="description">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
              </div>
            </router-link>
          </li>
          <li class="message">
            <router-link to="./message">
              <div class="actions">
                <span class="action"><i class="fa fa-square-o"></i></span>
                <span class="action"><i class="fa fa-star-o"></i></span>
              </div>
              <div class="header">
                <span class="from">Lukasz Holeczek</span>
                <span class="date"><span class="fa fa-paper-clip"></span> Hôm nay, 15:47</span>
              </div>
              <div class="title">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
              </div>
              <div class="description">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
              </div>
            </router-link>
          </li>
          <li class="message">
            <router-link to="./message">
              <div class="actions">
                <span class="action"><i class="fa fa-square-o"></i></span>
                <span class="action"><i class="fa fa-star-o"></i></span>
              </div>
              <div class="header">
                <span class="from">Lukasz Holeczek</span>
                <span class="date">Hôm nay, 15:47</span>
              </div>
              <div class="title">
                Lorem ipsum dolor sit amet.
              </div>
              <div class="description">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
              </div>
            </router-link>
          </li>
          <li class="message unread">
            <router-link to="./message">
              <div class="actions">
                <span class="action"><i class="fa fa-square-o"></i></span>
                <span class="action"><i class="fa fa-star-o"></i></span>
              </div>
              <div class="header">
                <span class="from">Lukasz Holeczek</span>
                <span class="date">Hôm nay, 15:47</span>
              </div>
              <div class="title">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit.
              </div>
              <div class="description">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
              </div>
            </router-link>
          </li>
          <li class="message">
            <router-link to="./message">
              <div class="actions">
                <span class="action"><i class="fa fa-square-o"></i></span>
                <span class="action"><i class="fa fa-star-o"></i></span>
              </div>
              <div class="header">
                <span class="from">Lukasz Holeczek</span>
                <span class="date">Hôm nay, 15:47</span>
              </div>
              <div class="title">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
              </div>
              <div class="description">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
              </div>

            </router-link>
          </li>
          <li class="message">
            <router-link to="./message">
              <div class="actions">
                <span class="action"><i class="fa fa-square-o"></i></span>
                <span class="action"><i class="fa fa-star-o"></i></span>
              </div>
              <div class="header">
                <span class="from">Hoàng Anh</span>
                <span class="date">Hôm nay, 15:47</span>
              </div>
              <div class="title">
                Lorem ipsum dolor sit amet.
              </div>
              <div class="description">
                Lãnh đạo tỉnh Điện Biên họp khẩn trong đêm khi phát hiện 4 người từ Hải Dương và 2 người từ Hà Nội trở về tỉnh này dương tính lần một với nCoV.
              </div>
            </router-link>
          </li>
          <li class="message unread">
            <router-link to="./message">
              <div class="actions">
                <span class="action"><i class="fa fa-square-o"></i></span>
                <span class="action"><i class="fa fa-star-o"></i></span>
              </div>
              <div class="header">
                <span class="from">Lukasz Holeczek</span>
                <span class="date">Hôm nay, 15:47</span>
              </div>
              <div class="title">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit.
              </div>
              <div class="description">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
              </div>
            </router-link>
          </li>
          <li class="message">
            <router-link to="./message">
              <div class="actions">
                <span class="action"><i class="fa fa-square-o"></i></span>
                <span class="action"><i class="fa fa-star-o"></i></span>
              </div>
              <div class="header">
                <span class="from">Lukasz Holeczek</span>
                <span class="date">Hôm nay, 15:47</span>
              </div>
              <div class="title">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
              </div>
              <div class="description">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
              </div>
            </router-link>
          </li>
          <li class="message">
            <router-link to="./message">
              <div class="actions">
                <span class="action"><i class="fa fa-square-o"></i></span>
                <span class="action"><i class="fa fa-star-o"></i></span>
              </div>
              <div class="header">
                <span class="from">Lukasz Holeczek</span>
                <span class="date">Hôm nay, 15:47</span>
              </div>
              <div class="title">
                Lorem ipsum dolor sit amet.
              </div>
              <div class="description">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
              </div>
            </router-link>
          </li>
        </ul>
      </main>
    </div>
  </div>
</template>
<script>

export default {
  name: 'inbox'
}
</script>
